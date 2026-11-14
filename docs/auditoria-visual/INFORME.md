# Auditoría visual integral

Fecha: 12 de noviembre de 2026. Rama: `codex/auditoria-visual`.

## 1. Vistas revisadas

Se recorrieron los cuatro portales en navegador local: Coordinación, Docente, Estudiante y Jefe inmediato. Se revisaron inicios, listados, detalles disponibles, formularios, estados vacíos, carga académica, monografías, ternas, defensas y las herramientas personales del estudiante sin guardar registros.

Se efectuaron 184 comprobaciones de layout sobre 50 rutas/casos distintos, incluidos 49 destinos de la aplicación y un caso visual desechable con textos extensos. Los tamaños de referencia fueron 1440, 1100, 820 y 390 píxeles de ancho. También se revisó el acceso al sistema. El inventario de rutas, tamaños, títulos y medidas se conserva en `mediciones.json` junto a este informe.

Los listados principales y los cinco paneles del proceso se comprobaron en los cuatro tamaños. Los detalles disponibles y las herramientas personales también se inspeccionaron en escritorio y móvil. No se inventaron registros para rellenar detalles inexistentes de Defensa.

## 2. Vistas modificadas

- Inicio del Estudiante y Mi práctica.
- Expediente y detalle de práctica de Coordinación.
- Seguimiento de practicante del Docente.
- Dashboard de Coordinación: concordancia singular/plural de las frases de atención.
- Acceso: etiqueta del campo DNI y asociación correcta entre etiqueta e input.
- Los cuatro layouts: hoja visual común y navegación móvil compartida. El layout Docente incorpora el botón de menú, cierre y overlay que faltaban, y utiliza las mismas clases de topbar que los otros portales.

Se creó un componente Blade compartido para el proceso. No se modificaron controladores, modelos, policies, relaciones, migraciones, seeders, autenticación ni servicios académicos.

## 3. Estilos modificados

Se añadió `public/css/presentation.css`, cargado después de la hoja existente en los cuatro portales. `app.css` y los colores institucionales se conservaron.

La hoja centraliza separación de nombre/estado, tamaño legible de texto, alineación de iconos, espaciado de botones, badges multilínea, formularios, estadísticas, contenido extenso, tablas con scroll controlado y límites del nombre de usuario en la topbar.

## 4. Componentes centralizados

- `resources/views/components/practice-process.blade.php`: icono, nombre, estado, descripción y enlaces ya existentes, con texto escapado.
- `public/css/presentation.css`: presentación compartida de proceso y ajustes generales.
- `public/js/navigation.js`: apertura/cierre del sidebar en los cuatro layouts, estado accesible del botón, cierre con Escape y devolución del foco. Sustituye tres copias inline y permite usar el menú móvil del Docente.

El componente recibe los estados y clases previamente calculados. No resuelve autorizaciones, revisiones ni transiciones de etapa.

## 5. Separadores del proceso

Aplicados en:

1. Estudiante → Inicio.
2. Estudiante → Mi práctica.
3. Coordinación → Expediente del estudiante.
4. Coordinación → Detalle de práctica.
5. Docente → Seguimiento de practicante.

Todos conservan exactamente:

`Solicitud → Documentos → Empresa | Práctica → Visita → Monografía | Defensa → Finalización`.

En escritorio hay tres grupos separados por líneas verticales discretas. En móvil se distribuyen en tres filas de 3, 3 y 2 etapas, con divisores horizontales. En tablet o ventanas intermedias, el componente admite desplazamiento horizontal contenido. Las conexiones entre etapas no atraviesan los divisores.

No se añadieron títulos de bloque ni colores para distinguir grupos. Los colores de los estados mantienen la identidad existente. No se añadió un flujo de ocho etapas al portal Jefe inmediato, que no lo tenía.

## 6. Problemas responsive corregidos

- Compresión de ocho etapas en poco espacio y falta de separación entre etiquetas y estados.
- Texto largo en nombres, empresas, estados, períodos y campos informativos.
- Tablas que necesitan conservar columnas legibles: el desplazamiento se limita a su contenedor.
- Cabeceras, filtros y botones que requieren saltos de línea y separación coherentes.
- Formulario de carga en móvil, con campos legibles y scroll vertical del modal.
- Topbar del Docente y ausencia de control para recuperar su sidebar en móvil.

Las 184 mediciones no detectaron desbordamiento horizontal del documento. El caso extenso verificó nombres largos, una denominación comercial larga sin espacios y estados amplios: las etiquetas se ajustaron a su ancho; la tabla se desplazó internamente. El HTML temporal de ese caso fue retirado de `public` al terminar. Su generador queda en pruebas y no consulta ni modifica datos académicos.

## 7. Inconsistencias visuales corregidas

- Cinco presentaciones distintas del proceso sustituidas por una estructura común.
- Iconos centrados, nombre y estado en elementos separados y alturas de línea consistentes.
- Tipografía de estadísticas y etiquetas de información unificada.
- Badges, botones, tablas y formularios con reglas comunes de ajuste.
- Frases como «1 visitas habilitadas» y «1 monografías se encuentran» ahora concuerdan con el contador existente.
- «Usuario o correo institucional» se sustituyó por «Número de cuenta / DNI», conforme al campo que ya utiliza el formulario.
- La leyenda «Fecha de entrega no disponible» ya no aparece junto a una fecha calculada disponible; el cálculo original no cambió.

## 8. Problemas funcionales observados

Se corrigió únicamente el problema de navegación visual del Docente: no tenía botón/overlay/control de cierre para el sidebar oculto en móvil. Se comprobaron apertura, cierre y Escape; no se cambiaron destinos ni permisos.

Se conservan y documentan dos comportamientos preexistentes fuera del alcance visual:

- El panel Docente todavía infiere los estados de Solicitud, Documentos y Empresa a partir de la etapa alcanzada. Pueden diferir de los registros mostrados por los módulos persistentes. Esta auditoría conserva esos estados y no inventa una regla ni altera su consulta.
- Los indicadores personales del inicio del Estudiante pueden seguir siendo de prototipo, aunque sus herramientas privadas tengan otros valores. No se migró ni modificó su almacenamiento ni sus cálculos.

Durante la integración del componente se detectó un error de compilación al mezclar una inicialización PHP inline con bloques Blade; se corrigió usando bloques PHP explícitos y se volvieron a validar los GET y la suite. No queda ese error en el resultado final.

## 9. Validaciones

- `php artisan test --do-not-cache-result`: **45 pruebas aprobadas, 1034 aserciones**.
- `php artisan view:cache`: correcto.
- `git diff --check`: sin errores.
- `node --check public/js/navigation.js`: correcto.
- Comprobación GET sobre MySQL autorizado: **52 respuestas esperadas**.
- Compilación de **111 bloques de scripts renderizados** mediante Node: correcta.
- Consola del navegador en el recorrido final: sin errores JavaScript observados.
- Controles de menú comprobados interactivamente en los cuatro portales.
- Pruebas nuevas para ocho etapas, grupos 3/3/2 sin títulos, estados conservados, escape de texto extenso, enlaces existentes y controles de navegación de cada rol.

No se ejecutaron migraciones ni seeders sobre MySQL en esta tarea. Las pruebas automatizadas usan SQLite en memoria; el recorrido local solo consultó información y abrió/cerró controles de interfaz.

El servidor local iniciado para la revisión fue detenido; la sesión de navegador se cerró y el tamaño de viewport se restauró.

## 10. Commit y evidencias

Commit de esta tarea: «Uniforma presentacion y separadores del proceso de practica». El identificador exacto se incluye en la respuesta final.

Evidencias guardadas junto a este informe:

- `seguimiento-escritorio.png`.
- `seguimiento-movil.png`.
- `mediciones.json`.

El archivo de instrucciones `CODEX_AUDITORIA_VISUAL.md`, que ya estaba sin seguimiento al comenzar, se conserva intacto sin incorporarlo al commit. No se hizo push ni merge a main. Todo el trabajo quedó dentro del repositorio autorizado.
