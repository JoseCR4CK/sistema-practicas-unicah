# Corrección del Segundo Parcial: Vite, CSS y Tailwind

## Diagnóstico y solución

1. **Diagnóstico inicial.** Rama inicial `main`, sin modificaciones salvo `CODEX_CORRECCION_VITE_TAILWIND.md`, proporcionado por el usuario y todavía sin seguimiento. Se informó antes de continuar y se creó `feature/correccion-vite-tailwind`. Se conserva la especificación íntegra como referencia del alcance. No había `package-lock.json` ni `node_modules/`.

2. **Vite.** El plugin Laravel ya declaraba `resources/css/app.css` y `resources/js/app.js`, pero los cinco layouts reales no usaban `@vite`. Solo la plantilla de bienvenida, no utilizada por el login ni los portales, tenía esa directiva. Se conectaron los layouts reales al manifest de Vite.

3. **CSS.** Los cuatro portales cargaban directamente `public/css/app.css` y `public/css/presentation.css`; Login cargaba `public/css/login.css`. La entrada CSS de resources era una configuración inicial de Tailwind sin los estilos del sistema. Se trasladaron los estilos propios a resources sin refactorizar el diseño. El CSS de los portales y presentación se conservó íntegro; se comprobó contra Git normalizando finales de línea.

4. **Estado inicial de Tailwind.** `package.json` declaraba `tailwindcss` y `@tailwindcss/vite` con rango `^4.0.0`. El plugin estaba registrado y la entrada CSS importaba Tailwind con Preflight, pero no llegaba a los layouts principales. No existía un uso demostrable de utilities compiladas en esos layouts; la plantilla de bienvenida conserva el ejemplo inicial, ajeno a las rutas reales revisadas.

5. **Versiones detectadas.** Tras instalar las dependencias declaradas: Tailwind **4.3.5**, Vite **7.3.8**, plugin Laravel **2.1.0**. Herramientas locales: Node **24.22.1**, npm **11.19.1**. No se modificaron los rangos de `package.json`. El nuevo lockfile fija la resolución y `npm ci` se ejecutó correctamente después de generarlo.

6. **Configuración Vite.** Se añadió `resources/css/login.css` como tercera entrada, junto con `resources/css/app.css` y `resources/js/app.js`. Se conservaron plugins, refresh, exclusión de vistas compiladas y configuración de puertos. La entrada separada de Login impide aplicar los estilos globales de los portales a autenticación.

7. **PostCSS/Tailwind.** No había configuración PostCSS independiente y no se añadió una innecesaria. Se usa el plugin oficial para Vite de Tailwind 4. `resources/css/tailwind.css` importa theme y utilities con `prefix(tw)`, declara fuentes explícitas `resources/views` y `resources/js` y no importa Preflight. No se copiaron opciones de Tailwind 3.

8. **resources/css.** `app.css` importa `tailwind.css`, `app-shell.css` y `presentation.css`. `login.css` importa `tailwind.css` y `login-page.css`. Este último conserva el diseño anterior, salvo trasladar dos declaraciones de alineación a utilities equivalentes y resolver la imagen del campus como import de Vite. No existen copias fuente paralelas en public/css.

9. **resources/js.** `app.js` importa la configuración existente de Axios y `navigation.js`, trasladado íntegramente desde public. La navegación global se ejecuta una sola vez. Los tres scripts específicos `academic-filters.js`, `coordination-record-modal.js` y `academic-load-catalog.js` permanecen en public/js porque sus vistas ya los cargan de forma selectiva y no era necesario alterar su ejecución para integrar la entrada global.

10. **Layouts actualizados.** `layouts/app.blade.php` (Coordinación/ROOT), `student.blade.php`, `teacher.blade.php`, `company.blade.php` y `auth/login.blade.php`. Cada uno carga su CSS y el módulo global mediante `@vite`. No se cambió la estructura funcional de los layouts.

11. **Referencias antiguas.** Se retiraron las cargas directas a los tres CSS propios y al script navigation.js. Se actualizó además el generador visual existente para obtener assets mediante Laravel Vite. Se verificaron los consumidores antes de retirar las ubicaciones anteriores. Las comprobaciones negativas en tests no son consumidores.

12. **Bootstrap.** Conserva exactamente los CDN anteriores: Bootstrap **5.3.8** y Bootstrap Icons **1.11.3**. No estaba instalado por npm. No se duplicó su carga ni se añadieron paquetes para migrarlo. Su disponibilidad requiere acceso a los CDN. Se comprobaron grid, cards, badges, tablas, formulario y modal de Empresas.

13. **Coexistencia Tailwind/Bootstrap.** Las utilities utilizan `tw:` y no existe Preflight. Se evita así aplicar clases sin prefijo o un reset nuevo a componentes Bootstrap. No se convirtió la interfaz a otro framework.

14. **Demostración verificable de Tailwind.** Vista `resources/views/auth/login.blade.php`, elemento visible `.login-footer`, clases `tw:flex tw:justify-center`. Fuente de configuración: `resources/css/tailwind.css`; entrada: `resources/css/login.css`. Salida observada: `public/build/assets/login-BrlpBAsB.css`, con `.tw\:flex{display:flex}` y `.tw\:justify-center{justify-content:center}`. En el navegador: `display: flex`, `justify-content: center`. Se retiraron las dos declaraciones CSS anteriores para que este resultado dependa realmente de las utilities. `FrontendAssetsTest` comprueba su existencia en el CSS generado, no solo en package.json.

## Validaciones

15. **npm run build.** Exitoso, código 0, 63 módulos transformados. Compilación final sin warnings de assets ni imports rotos. La primera compilación avisó que la URL absoluta de la imagen del campus se resolvería en ejecución; se corrigió a un import relativo del archivo real y se recompiló.

16. **Salida generada.** Manifest `public/build/manifest.json`; `assets/app-D45AGwRR.css` (157.16 kB), `assets/login-BrlpBAsB.css` (5.10 kB), `assets/app-DpD9RTEh.js` (59.81 kB), `assets/campus-A6eOGxcI.jpeg` (263.13 kB). Los nombres con hash corresponden al build revisado y pueden cambiar al modificar fuentes. La salida sigue ignorada por Git, como antes; no se editó ni versionó manualmente. npm informó que el postinstall de esbuild no estaba autorizado por su política de scripts; no se cambiaron permisos ni ajustes globales: la instalación reproducible, Vite y sus compilaciones funcionaron con el binario disponible. El aviso de actualización mayor de npm tampoco motivó una actualización.

17. **npm run dev.** Inició correctamente en su puerto habitual **5173**, sin modificarlo. Se verificaron en navegador `/@vite/client`, `resources/js/app.js`, `resources/css/login.css` y la aplicación real de las utilities; sin errores de consola en esa comprobación. Se detuvo el proceso. Se retiró el marcador temporal public/hot que quedó después de Ctrl+C y se regeneraron las fixtures con assets de producción. No quedaron servidores iniciados por esta tarea.

18. **Blade.** `php artisan view:clear` y `php artisan view:cache` finalizaron correctamente. También pasaron las verificaciones de sintaxis PHP de los nuevos archivos y `node --check` de las entradas JS y configuración Vite.

19. **Suite Laravel.** `php artisan test --do-not-cache-result` pasó al inicio y después de la integración. La suite final incluye API REST, CRUD web, autenticación, roles y flujos académicos existentes; no se omitieron pruebas.

20. **Totales.** Inicial: **59 pruebas / 1243 aserciones**. Final: **61 pruebas / 1277 aserciones**, todas aprobadas. Se añadieron dos pruebas de assets y se actualizó la comprobación del módulo global en VisualPresentationTest. Las pruebas usan SQLite en memoria; no borran ni modifican MySQL.

21. **Rutas.** `php artisan route:list` finalizó correctamente y mostró **111 rutas**, igual que antes del cambio. No se editaron archivos de rutas ni controladores.

22. **Formato.** `git diff --check` pasó. No hay errores de espacios o conflictos pendientes.

23. **Regresiones y límites encontrados.** No se detectaron regresiones visuales atribuibles al pipeline. MySQL estaba apagado: la solicitud normal al Login fallaba al consultar la sesión en MySQL antes de renderizar. No se cambió .env, no se inició XAMPP y no se alteró el backend para ocultarlo. La comprobación visual se realizó con vistas reales renderizadas por Laravel y sus controladores usando SQLite en memoria; no equivale a una validación de persistencia o login real contra MySQL.

24. **Correcciones y comprobación visual.** Se revisaron **31 vistas** a **1440, 1024, 768 y 390 px**: 124 comprobaciones sin desbordamiento horizontal del documento. Diez comparaciones de los cinco layouts antes/después (escritorio y móvil) conservaron medidas y estilos principales. No se trata de una comparación de cada píxel. Se verificaron apertura/cierre del menú móvil en los cuatro portales, enlaces principales, modal de Empresas en escritorio y móvil, y mostrar contraseña en Login. Las transiciones CSS requieren esperar su finalización al comprobar visibilidad; no se cambió su comportamiento. Los estados académicos y separadores del proceso se conservaron. Datos y métricas están en `validacion-visual.json`; las capturas adjuntas muestran Login, Coordinación, Docente, Estudiante y el modal móvil. Las fixtures tenían formularios bloqueados, no escribían datos y fueron retiradas tras la revisión.

## Archivos y entrega

25. **Archivos creados.** `.npmrc`, `package-lock.json`, `resources/css/tailwind.css`, `resources/css/login.css`, `tests/Feature/FrontendAssetsTest.php`, `tests/RenderFrontendReview.php`, este informe, `validacion-visual.json` y cinco capturas JPEG en esta carpeta. Se incorpora sin modificaciones `CODEX_CORRECCION_VITE_TAILWIND.md`, entregado por el usuario, para dejar trazabilidad y el árbol limpio.

26. **Archivos modificados.** `.gitignore` (caché local npm y fixtures temporales), `README.md` (flujo frontend sin eliminar Segundo Parcial/API), `vite.config.js`, `resources/css/app.css`, `resources/js/app.js`, los cuatro layouts, Login, `tests/Feature/VisualPresentationTest.php` y `tests/RenderVisualFixture.php`. Archivos reubicados: CSS principal a `resources/css/app-shell.css`, presentación a `resources/css/presentation.css`, Login a `resources/css/login-page.css`, navegación a `resources/js/navigation.js`. Solo login-page.css recibió los ajustes puntuales ya descritos. No se modificaron modelos, middleware, políticas, migraciones, seeders, lógica de negocio, API ni CRUD.

27. **Ubicaciones eliminadas.** `public/css/app.css`, `public/css/presentation.css`, `public/css/login.css` y `public/js/navigation.js`, cuyos contenidos ahora están en resources. También se retiraron las fixtures y el marcador hot temporales; no se eliminó información académica. node_modules, caché npm, .env, vistas compiladas y public/build no se incluyen en el commit.

28. **Commit.** Se entrega un único commit descriptivo: `Integra Vite y Tailwind al frontend del proyecto`. Su identificador se informa en la respuesta final y puede consultarse con `git log -1 --oneline`. Este informe forma parte de ese commit.

29. **Rama final.** `feature/correccion-vite-tailwind`, creada desde el estado estable existente. No se hizo merge a main ni push.

30. **Git status final.** Árbol limpio después de incluir únicamente los archivos de esta corrección y la especificación recibida. Se verifica nuevamente después del commit. Todo el trabajo de archivos quedó dentro de `C:\Users\50497\Desktop\ProyectoWebII\sistema-practicas-unicah`; no se modificaron configuraciones globales ni otra base de datos.
