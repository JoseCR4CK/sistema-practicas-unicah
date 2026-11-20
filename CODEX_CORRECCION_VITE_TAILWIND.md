# CORRECCIÓN DEL SEGUNDO PARCIAL — VITE, CSS Y TAILWIND

Trabaja únicamente dentro de:

`C:\Users\50497\Desktop\ProyectoWebII\sistema-practicas-unicah`

## CONTEXTO

El docente realizó la siguiente retroalimentación sobre el proyecto:

- integrar correctamente Vite en las plantillas principales de Laravel;
- organizar los estilos CSS para que sean procesados mediante el sistema de compilación;
- demostrar que Tailwind funciona realmente en las interfaces donde se utilice;
- ejecutar `npm run build`;
- comprobar que la compilación finaliza sin errores;
- verificar que el diseño, navegación y funcionalidades existentes continúan funcionando;
- publicar posteriormente las correcciones en GitHub.

El backend, MySQL, API REST, CRUD, autenticación, roles y lógica académica NO son el objetivo de esta tarea.

El propósito es corregir exclusivamente el pipeline del frontend y dejar evidencia clara de que Vite y Tailwind están funcionando correctamente.

---

# RESTRICCIÓN DE TRABAJO

No accedas, leas ni modifiques nada fuera de:

`C:\Users\50497\Desktop\ProyectoWebII\sistema-practicas-unicah`

No modifiques configuraciones globales de:

- Git;
- Node.js;
- npm;
- PHP;
- XAMPP;
- VS Code;
- Windows.

No uses comandos destructivos.

No hagas `git push`.

No hagas merge a `main`.

Trabaja en una rama nueva creada desde el estado actual estable.

Nombre sugerido:

`feature/correccion-vite-tailwind`

Si el árbol no está limpio, informa antes de continuar.

---

# 1. DIAGNÓSTICO INICIAL OBLIGATORIO

Antes de modificar archivos, inspecciona:

- `package.json`
- `package-lock.json`
- `vite.config.js` o equivalente
- `resources/css/`
- `resources/js/`
- `public/css/`
- `public/js/`
- configuración Tailwind existente
- configuración PostCSS existente
- `bootstrap/app.php`
- layouts Blade principales
- vistas de autenticación
- cualquier uso de `@vite`
- cualquier uso directo de `<link>` hacia CSS propio
- cualquier `<script>` directo a JavaScript propio
- uso de CDN para Bootstrap, Bootstrap Icons u otras librerías
- clases Tailwind realmente utilizadas actualmente
- imports CSS/JS actuales

Revisa especialmente los layouts:

- Coordinación
- Estudiante
- Docente
- Jefe inmediato
- Login/autenticación

Identifica:

1. qué archivos CSS propios se cargan actualmente desde `public/`;
2. qué archivos JS propios se cargan actualmente desde `public/`;
3. qué archivos ya pasan por Vite;
4. si Tailwind está instalado y configurado;
5. qué versión de Tailwind existe;
6. si Tailwind realmente genera estilos;
7. si Bootstrap se carga mediante Vite, npm o CDN;
8. riesgos de conflicto Bootstrap/Tailwind;
9. duplicaciones de CSS o JavaScript.

No asumas la versión de Tailwind.

Utiliza exactamente la configuración adecuada a la versión instalada.

---

# 2. OBJETIVO DE VITE

Las plantillas principales de Laravel deben utilizar correctamente Vite.

La solución final debe tener una entrada clara y mantenible, preferiblemente basada en:

`resources/css/app.css`

y:

`resources/js/app.js`

Los layouts principales deben cargar los assets mediante Laravel Vite, por ejemplo mediante `@vite(...)`, utilizando la sintaxis correcta para la configuración real del proyecto.

No mantengas dos pipelines paralelos para el mismo CSS.

No debe suceder que:

- una parte use `public/css/app.css`;
- otra parte use `resources/css/app.css`;
- ambas contengan versiones distintas de los mismos estilos.

Consolida los estilos propios del proyecto de forma segura.

---

# 3. ORGANIZACIÓN DEL CSS

Los estilos propios del sistema deben quedar bajo el pipeline de Vite.

Si actualmente `public/css/app.css` contiene el CSS principal:

- migra o reorganiza ese CSS hacia `resources/css/`;
- conserva exactamente el diseño visual actual;
- actualiza los layouts para usar el recurso compilado;
- elimina referencias directas antiguas solo después de comprobar que ya no tienen consumidores.

NO elimines archivos sin comprobar primero dónde se utilizan.

La carpeta `public/build/` debe considerarse salida compilada, no código fuente.

No dupliques manualmente el CSS compilado.

---

# 4. JAVASCRIPT

Revisa también el JavaScript global.

El JavaScript global que forme parte de la aplicación debe poder cargarse correctamente mediante Vite desde `resources/js/app.js`.

No es obligatorio migrar cada script específico de una pantalla si hacerlo aumenta innecesariamente el riesgo.

Sin embargo:

- la entrada principal JS debe estar correctamente integrada;
- imports globales deben estar organizados;
- no deben existir duplicaciones accidentales;
- no deben ejecutarse scripts dos veces.

Los scripts específicos que permanezcan en `public/js/` deben tener una razón técnica válida y documentarse.

---

# 5. BOOTSTRAP

El sistema actual utiliza Bootstrap y NO debe ser rediseñado.

No conviertas el proyecto completo de Bootstrap a Tailwind.

Bootstrap debe continuar funcionando correctamente.

Determina cómo está siendo cargado actualmente.

Si Bootstrap ya está instalado mediante npm y puede integrarse con Vite sin riesgo:

- utiliza la integración correcta mediante Vite.

Si actualmente depende de CDN y migrarlo implica un riesgo innecesario:

- conserva Bootstrap;
- pero asegúrate de que el CSS propio y Tailwind sí pasen correctamente por Vite.

No rompas:

- Grid;
- modales;
- dropdowns;
- botones;
- sidebar;
- navbar;
- formularios;
- tablas;
- badges.

---

# 6. TAILWIND

El docente exige demostrar que Tailwind funciona realmente donde se utilice.

Primero determina:

- si Tailwind ya está instalado;
- su versión;
- configuración existente;
- archivos que analiza;
- clases Tailwind ya presentes en las vistas.

## Si ya existen clases Tailwind

Haz que se compilen correctamente mediante Vite y verifica visualmente que produzcan estilos reales.

## Si Tailwind está instalado pero actualmente no existe un uso real demostrable

Introduce una utilización mínima, segura y claramente identificable de Tailwind dentro de una interfaz existente.

NO rediseñes la interfaz.

El uso debe ser suficientemente claro para demostrar al docente que Tailwind está siendo procesado por Vite.

Ejemplos aceptables:

- layout/flex;
- espaciado;
- alineación;
- responsive;
- tipografía;
- un pequeño badge;
- un contenedor auxiliar.

Evita reemplazar componentes grandes de Bootstrap.

---

# 7. EVITAR CONFLICTOS BOOTSTRAP / TAILWIND

Bootstrap es actualmente la base visual del proyecto.

Tailwind debe coexistir sin alterar globalmente la apariencia existente.

Detecta la versión de Tailwind y aplica la estrategia de aislamiento adecuada.

Si la versión instalada permite configurar prefijo de utilities, considera utilizar un prefijo para evitar colisiones.

Si el reset/preflight de Tailwind modifica visualmente componentes Bootstrap, utiliza la configuración apropiada para impedir esa regresión.

NO copies configuraciones de otra versión de Tailwind.

La solución debe corresponder exactamente a la versión instalada.

---

# 8. PLANTILLAS QUE DEBEN USAR VITE

Revisa todos los layouts principales utilizados realmente por la aplicación.

Como mínimo:

- Coordinación;
- Estudiante;
- Docente;
- Jefe inmediato;
- Login.

Todos deben cargar correctamente los assets compilados necesarios.

Evita repetir bloques de carga innecesariamente si existe una solución reutilizable.

No cambies la estructura funcional de los layouts.

---

# 9. VISTAS Y DISEÑO

Después de integrar Vite/Tailwind verifica que visualmente permanezcan correctas las principales interfaces.

Revisa especialmente:

## Coordinación
- Dashboard
- Estudiantes
- Empresas
- Prácticas
- Solicitudes
- Documentos
- Visitas
- Monografía
- Terna
- Defensa
- Carga académica

## Docente
- Inicio
- Mis practicantes
- Seguimiento de practicante
- Visitas
- Seguimientos
- Carga académica
- Monografía

## Estudiante
- Inicio
- Mi práctica
- Solicitudes
- Documentos
- Visita
- Monografía
- Terna
- Defensa

## Jefe inmediato
- Inicio
- Practicantes
- Empresa
- Seguimiento

## Autenticación
- Login

No alteres el diseño institucional salvo para corregir una regresión causada por Vite/Tailwind.

---

# 10. RESPONSIVE

Comprueba que la integración no rompa:

- escritorio;
- pantalla reducida;
- tablet;
- móvil razonable.

Especialmente:

- sidebar;
- topbar;
- cards;
- tablas;
- formularios;
- modales;
- flujo visual de práctica;
- scroll horizontal controlado.

---

# 11. NPM Y DEPENDENCIAS

Trabaja únicamente con las dependencias del proyecto.

Si existe `package-lock.json`, utiliza preferentemente:

`npm ci`

cuando sea apropiado para reproducir las dependencias.

Si el proyecto requiere actualizar dependencias porque la configuración actual es inválida, realiza únicamente los cambios mínimos necesarios y documenta el motivo.

No actualices versiones arbitrariamente.

No ejecutes actualizaciones masivas.

---

# 12. COMPILACIÓN OBLIGATORIA

Debes ejecutar:

`npm run build`

La tarea NO se considera terminada hasta que este comando finalice correctamente.

Comprueba:

- código de salida exitoso;
- generación de `public/build/`;
- existencia del manifest de Vite;
- CSS compilado;
- JavaScript compilado;
- ausencia de errores;
- ausencia de imports rotos.

Si aparecen warnings:

- determina si son relevantes;
- corrige los que indiquen un problema real;
- documenta los que sean seguros.

---

# 13. MODO DESARROLLO

Comprueba también que el proyecto pueda trabajar en desarrollo con:

`npm run dev`

No es necesario dejar el servidor ejecutándose al finalizar.

Solo confirma que la configuración es válida.

No cambies el puerto del proyecto ni configuraciones globales.

---

# 14. LARAVEL

Después de modificar los layouts y assets ejecuta:

`php artisan view:clear`

`php artisan view:cache`

Verifica que las vistas Blade compilen sin errores.

---

# 15. REGRESIÓN FUNCIONAL

NO basta con que `npm run build` pase.

Después del cambio ejecuta la suite completa:

`php artisan test --do-not-cache-result`

La cantidad de pruebas debe ser igual o superior al estado actual del repositorio.

No aceptes regresiones.

También ejecuta:

`php artisan route:list`

`git diff --check`

---

# 16. API REST Y BACKEND

No modifiques innecesariamente:

- API REST de Empresas;
- CRUD de Empresas;
- autenticación;
- roles;
- middleware;
- Policies;
- Models;
- migraciones;
- seeders;
- Monografía;
- Terna;
- Defensa;
- Carga académica;
- reglas institucionales.

Esta tarea es de frontend/build.

Si una modificación backend fuese absolutamente necesaria para corregir la carga de assets, documenta claramente por qué.

---

# 17. README

Actualiza el README únicamente si es necesario para explicar el nuevo flujo frontend.

Añade o corrige una sección clara como:

`## Frontend, Vite y Tailwind`

Debe explicar:

- instalación de dependencias;
- `npm ci` o `npm install`;
- `npm run dev`;
- `npm run build`;
- entradas principales de Vite;
- cómo se integra Tailwind;
- cómo convive con Bootstrap;
- dónde se generan los assets;
- que `public/build/` es salida compilada.

No elimines la documentación existente del Segundo Parcial ni de la API.

---

# 18. EVIDENCIA PARA EL DOCENTE

El resultado debe dejar evidencia técnica clara para una nueva revisión.

Debe poder demostrarse:

1. un layout Blade utilizando `@vite`;
2. `resources/css/app.css` siendo procesado;
3. `resources/js/app.js` siendo procesado;
4. configuración Tailwind real;
5. al menos un elemento visible que utilice Tailwind realmente;
6. `npm run build` terminando correctamente;
7. archivos generados por Vite;
8. diseño existente sin regresiones;
9. navegación funcional;
10. suite Laravel aprobada.

---

# 19. PRUEBA DE TAILWIND

Incluye una comprobación técnica que permita demostrar que una utility Tailwind utilizada en una vista aparece en el CSS compilado.

No basta con afirmar que Tailwind está instalado.

Debe existir evidencia de que:

vista Blade
→ clase Tailwind
→ procesamiento de Tailwind
→ Vite
→ CSS compilado.

Documenta en el informe:

- vista;
- clase utilizada;
- archivo fuente;
- archivo compilado donde se genera el resultado.

No edites manualmente el CSS compilado.

---

# 20. NO VERSIONAR ARTEFACTOS INCORRECTOS

Respeta la estrategia actual de `.gitignore`.

No agregues:

- `node_modules/`;
- credenciales;
- `.env`;
- archivos temporales;
- cachés innecesarios.

Respecto a `public/build/`:

- respeta lo que establezca el repositorio;
- no cambies arbitrariamente su política;
- documenta si está o no versionado.

---

# 21. COMMITS

Realiza cambios pequeños y comprensibles.

Al terminar crea un commit descriptivo, por ejemplo:

`Integra Vite y Tailwind al frontend del proyecto`

No hagas push.

---

# 22. INFORME FINAL

Entrega un informe detallado con:

1. diagnóstico inicial;
2. problema encontrado con Vite;
3. problema encontrado con CSS;
4. estado inicial de Tailwind;
5. versión de Tailwind detectada;
6. cambios en `vite.config`;
7. cambios en PostCSS/Tailwind;
8. cambios en `resources/css`;
9. cambios en `resources/js`;
10. layouts actualizados;
11. referencias antiguas eliminadas;
12. forma en que Bootstrap quedó integrado;
13. forma en que Tailwind quedó integrado;
14. elemento usado para demostrar Tailwind;
15. resultado de `npm run build`;
16. archivos producidos por Vite;
17. resultado de `npm run dev`;
18. resultado de compilación Blade;
19. resultado de pruebas Laravel;
20. total de pruebas y aserciones;
21. resultado de `route:list`;
22. resultado de `git diff --check`;
23. regresiones encontradas;
24. regresiones corregidas;
25. archivos creados;
26. archivos modificados;
27. archivos eliminados;
28. commit creado;
29. rama final;
30. `git status` final.

---

# CRITERIO DE FINALIZACIÓN

La tarea solamente queda terminada si se cumplen simultáneamente:

- Vite integrado en los layouts principales;
- CSS propio procesado por Vite;
- Tailwind funcionando realmente;
- Bootstrap continúa funcionando;
- `npm run build` exitoso;
- vistas Blade compilan;
- pruebas Laravel pasan;
- navegación existente permanece funcional;
- diseño no presenta regresiones evidentes;
- árbol Git limpio después del commit.

No continúes con otros módulos al finalizar.