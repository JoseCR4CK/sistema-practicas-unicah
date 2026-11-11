# Informe de los Bloques 4 y 5

Fecha: 11 de noviembre de 2026, America/Tegucigalpa.

Repositorio autorizado: `C:\Users\50497\Desktop\ProyectoWebII\sistema-practicas-unicah`.
Base autorizada: `sistema_practicas_unicah`.

## 1. Trabajo realizado en el Bloque 4

Se verificó y conservó la corrección de Monografía del commit previo `4d0d909`: selección explícita de resultado, revisión técnica exclusiva del docente responsable, acceso desde Seguimiento y una sola monografía por práctica con historial de versiones. Coordinación consulta y supervisa la revisión técnica. Los plazos 10/3/1 y la entrega siete días antes del final no cambiaron.

Se migró la carga académica del docente y su consulta/corrección desde Coordinación a Eloquent/MySQL. Se conservan docente, período, año, asignatura, carrera y sección opcionales, días, horario, vigencia, créditos y horas semanales independientes, estado, autor y revisiones. Los cambios conservan valores anteriores/posteriores y actor; las correcciones de Coordinación requieren motivo. No se eliminan clases para perder historial: pueden desactivarse.

Defensa contrasta las clases persistentes de todos los integrantes de la terna, además de las clases puntuales ya registradas. Se comprueban conflictos al proponer, declarar disponibilidad y confirmar. Se mantienen 60/90/120 minutos, coincidencia de participantes y el límite conocido de las 21:50. No se añadieron bloqueos personales ni transiciones automáticas de etapa.

## 2. Trabajo realizado en el Bloque 5

Se auditó el origen de datos, consumidores de stores, rutas, relaciones, validación, autorización por registro, CSRF, acciones repetidas y seeders. Se conectaron los datos residuales de empresa/docente en el portal Estudiante y el resumen general del Jefe inmediato a relaciones persistentes. El jefe consulta exclusivamente las prácticas vinculadas a su responsabilidad de empresa; no recibe nuevas operaciones académicas.

Se corrigieron contadores, nombres, períodos, estados e indicadores visuales que todavía inferían resultados por posición en el proceso. Se eliminaron cálculos duplicados del día/estado de Visita en JavaScript; esas pantallas reciben los valores calculados por el backend. Se conservaron los estilos y componentes existentes.

## 3. Migraciones creadas y aplicadas

Se creó y aplicó `2026_11_11_130000_create_academic_loads_tables.php`, lote 6: tablas `academic_loads` y `academic_load_changes`, claves foráneas, índices, clave única de envío y número de revisión.

Al comenzar, las tres migraciones del Bloque 3 ya figuraban aplicadas, lote 5. Se ejecutaron `php artisan migrate --no-interaction` y `php artisan migrate:status`. Las 19 migraciones del repositorio están aplicadas; ninguna quedó pendiente. No se reconstruyó la base ni se ejecutaron comandos destructivos.

## 4. Seeders ejecutados

En MySQL: `AcademicClosingSeeder` mediante Artisan y los seeders `AcademicFlowSeeder`, `AcademicClosingSeeder` y `DevelopmentTextCleanupSeeder` mediante la comprobación de integración, ejecutados repetidamente con comparación de registros.

`DatabaseSeeder`, `RoleSeeder` y `DemoDataSeeder` también se ejecutan en las pruebas aisladas. Se revisaron los cinco seeders. Los roles existentes conservan sus etiquetas editadas; el cierre académico solo genera ejemplos para cuentas de desarrollo; la limpieza sustituye únicamente textos originales exactos y conserva observaciones modificadas y cuentas ajenas al conjunto de desarrollo.

No se añadieron personas, horarios ficticios ni ternas o defensas artificiales a MySQL. Los registros base conservaron sus valores; la segunda ejecución de seeders no produjo duplicados ni cambios.

## 5. Archivos creados

El inventario al final de este informe marca con **A** cada archivo incorporado. Incluye los tres archivos de autorización aportados por el usuario, controladores de carga y consulta de empresa, modelos, policy, migración, vistas compartidas, seeder de limpieza y validaciones. Este informe es el archivo adicional de documentación de cierre.

## 6. Archivos modificados

El inventario final marca con **M** todos los archivos modificados respecto a `4d0d909`, y con **D** los retirados. La vista antigua de carga académica se sustituyó por las vistas compartidas persistentes; su ruta mantiene el nombre anterior.

## 7. Stores eliminados

Se eliminó `practices-store.js` después de comprobar que no tenía consumidores en el repositorio y que sus datos académicos ya se consultan mediante Eloquent. Los cuatro stores de flujos retirados en el Bloque 2 permanecen ausentes.

## 8. Stores conservados y motivo

Se conserva el almacenamiento inline de Agenda, Mis notas, Mis pendientes y Bitácora, expresamente fuera del alcance. No se borraron claves del navegador ni se expusieron esas herramientas a otros roles. No quedan stores de carga académica o prácticas como fuente principal activa.

Los horarios que existan únicamente en navegadores anteriores no se importan automáticamente: el servidor no puede conocerlos. Deben registrarse con su vigencia real. No se inventaron clases a partir del catálogo temporal eliminado.

## 9. Problemas de seguridad o autorización encontrados

- Login sin límite de intentos: se añadió limitación técnica de 10 solicitudes por minuto por origen.
- Reutilizar la clave de envío de un seguimiento en otra práctica, aunque ambas estuvieran asignadas al mismo docente, devolvía el registro del proceso anterior: ahora devuelve 409.
- Pantallas estáticas mostraban empresa, docente o practicante de un ejemplo a usuarios distintos: ahora usan las relaciones del usuario autenticado. Esto era información simulada incorrecta, no una policy que autorizara lectura arbitraria de registros reales.

Las comprobaciones de IDs ajenos, opciones de Defensa bajo otro padre, responsables bajo otra empresa, revisiones/versiones incompatibles y registros inexistentes conservaron las denegaciones verificadas. No se encontraron accesos adicionales autorizados al Jefe inmediato sin una relación válida de terna.

## 10. Correcciones realizadas

Además de lo anterior: carga con autorización del propietario o Coordinación/ROOT, asignación de docente desde servidor, validación de fechas/días/horarios, escape Blade, CSRF, deduplicación de envío, revisión optimista para evitar sobrescrituras y registro de correcciones.

El botón de alta de estudiantes, que no tenía operación de guardado implementada, quedó deshabilitado con un mensaje claro; no se implementó un nuevo flujo de creación de cuentas fuera del alcance. El modal conserva su estructura y las carreras se obtienen de datos existentes. Los módulos aún sin operaciones muestran mensajes neutros.

## 11. Datos hardcodeados eliminados

Se retiraron registros duplicados del store de prácticas, catálogo temporal de asignaturas, nombre fijo del docente en Carga, nombres/datos de empresa y responsable en las páginas del estudiante, docente y período fijos, nombre fijo del jefe en el layout y datos fijos del practicante en su resumen.

Los textos originales de solicitud y metadatos de documento se reemplazaron por «Solicitud de práctica profesional.» e «Información de la versión del documento.». No se reescribieron observaciones manuales.

## 12. Contadores corregidos

- Prácticas activas del dashboard de Coordinación: consulta real en lugar de 84.
- Visitas por atender en Docentes: visitas Habilitadas o Programadas relacionadas con sus prácticas.
- Docentes con alertas: mismos motivos ya usados en el portal docente, visitas por atender, seguimientos pendientes o monografías en revisión.
- Carga: clases, días y horas registradas se calculan desde la consulta filtrada; no se convierte créditos a horas.
- Jefe inmediato: practicantes distintos, prácticas activas y visitas registradas según sus relaciones.

El informe de estudiantes sigue utilizando la misma fuente persistente que el listado.

## 13. Módulos con persistencia

Estudiantes, Empresas, Docentes y Prácticas conservan sus consultas persistentes. Solicitudes, Documentos, Visitas, Seguimientos, Monografía, Terna, Defensa y Carga académica utilizan los modelos y tablas correspondientes para las operaciones implementadas. Los portales comparten esos registros.

«Persistencia» se refiere al alcance implementado de cada módulo; no significa CRUD universal ni almacenamiento físico definitivo de archivos. Documentos y Monografía conservan metadatos, versiones, estados y trazabilidad; no se añadió carga física en estos bloques.

## 14. Dependencias que permanecen en localStorage

Agenda, Mis notas, Mis pendientes y Bitácora. Sus pantallas, almacenamiento y comportamiento privado no fueron modificados. Los indicadores personales de prototipo del inicio del estudiante se conservaron fuera de esta migración académica.

## 15. Reglas institucionales pendientes

Responsable definitivo de estilo y del revisor coordinador; posible superposición de plazos de revisión con el final de práctica; requisitos definitivos de Defensa; documentos específicos de Visita y Defensa; evaluación formal del Jefe inmediato; rúbrica/calificación; reprogramación y ausencias; PERSONAL_CAMPUS; condiciones finales de cierre; equivalencia completa créditos/horas y catálogo/calendario institucional completo.

La función de coordinador de terna que ya utiliza el Bloque 3 permanece como una referencia técnica contextual, sin convertirla en una confirmación del responsable institucional definitivo. No se implementó finalización automática ni habilitación automática de Defensa.

## 16. Pruebas ejecutadas

- `php artisan test --do-not-cache-result`.
- `php artisan migrate --no-interaction` y `php artisan migrate:status`.
- `php artisan route:list`, también en formato JSON para contar y revisar rutas.
- `git diff --check`.
- `php artisan view:cache`.
- `php -l` sobre todos los PHP de app, routes, database y tests.
- `php tests/ValidateAcademicDelivery.php` contra MySQL.
- `php tests/SmokeAcademicFlows.php --scripts`, con compilación de scripts mediante Node.

La suite cubre autenticación por DNI/rol, logout, usuario inactivo, rol sin portal, CSRF fuera del bypass de pruebas, límite de intentos, 403/404, aislamiento, relaciones, persistencia, transiciones válidas, versiones y revisiones, plazos, composición de terna, consenso y conflictos de horario. El módulo de carga cubre historial, correcciones, envíos repetidos y conservación independiente de créditos/horas.

## 17. Resultados

42 pruebas aprobadas, 816 aserciones, cero fallos finales. Las 92 comprobaciones de sintaxis PHP pasaron. Blade compiló correctamente y `git diff --check` terminó sin errores. Se validaron 52 respuestas GET y la sintaxis de 100 bloques de scripts renderizados.

Durante la ampliación apareció un fallo en una prueba de limpieza: el modelo de la prueba conservaba en memoria el valor anterior a una actualización de seeder. Se corrigió refrescando el modelo antes de la siguiente edición y se repitió la suite completa. También se reforzó la comparación exacta en PHP para que la colación de MySQL no trate como originales textos editados con mayúsculas distintas.

## 18. Validaciones MySQL

La conexión fue comprobada explícitamente como MySQL y `sistema_practicas_unicah`. La comprobación de seeders compara conteos y hashes, verifica que los datos base no cambien y que la segunda ejecución sea idéntica a la primera. Se validaron escritura/lectura de JSON y decimales de carga y detección de cruces; el registro de comprobación se revirtió en una transacción.

Estado conservado: 6 roles, 10 usuarios, 5 estudiantes, 1 docente, 4 empresas, 2 responsables, 5 prácticas, 3 solicitudes, 15 documentos y versiones, 2 visitas, 3 seguimientos, 1 monografía con 1 versión y 3 revisiones, 1 terna pendiente sin miembros, 0 defensas y 0 clases de carga. No se dejaron registros ficticios de la comprobación.

Los GET de cierre académico responden correctamente; no permanecen en 503. Con un solo docente disponible no se inventaron personas para llenar una terna. Los flujos completos de terna/Defensa se probaron con fixtures aislados en SQLite; MySQL se comprobó con los registros existentes y la operación transaccional de carga, sin afirmar una programación real de Defensa inexistente.

## 19. Rutas registradas

107 rutas registradas en total. Se incorporaron GET/POST de carga por docente en Coordinación y PUT para correcciones; POST/PUT de carga propia en Docente; se mantuvo el GET existente del docente con su nombre de ruta. Empresa/Docente del estudiante y dashboard del jefe ahora resuelven controladores con datos propios. Login conserva su ruta y añade throttling. Los accesos genéricos a módulos académicos migrados de Coordinación redirigen al módulo real.

## 20. Commits

- `644e9dd` Documenta alcance autorizado de bloques 4 y 5.
- `9b19017` Migra carga academica e integra disponibilidad de defensa.
- `1386796` Conserva datos editados y neutraliza textos de desarrollo.
- `914ddbc` Audita seguridad y unifica informacion entre portales.
- Commit de cierre documental: «Documenta validacion final de bloques 4 y 5», que contiene este informe.

## 21. Git status final

Árbol limpio después del commit de este informe, sin cambios pendientes ni archivos sin seguimiento. Los archivos de autorización iniciales quedaron conservados en Git.

## 22. Rama final

`feature/autonomia-bloques-4-5`. Se conservó la rama anterior y no se trabajó sobre main.

## 23. Push

No se hizo push. Se siguió la prohibición específica de los archivos de Bloques 4 y 5 y su requisito final, aunque la autorización general permite hacerlo.

## 24. Merge

No se hizo merge a main ni se borraron ramas existentes.

## 25. Límite de trabajo

Todos los archivos inspeccionados y modificados para el proyecto permanecen dentro del repositorio autorizado. No se accedió a otros proyectos, adjuntos externos ni configuraciones globales. Solo se utilizó `sistema_practicas_unicah` como base MySQL. Las pruebas automatizadas usan SQLite en memoria y las herramientas se ejecutan normalmente según la autorización.

## Inventario de archivos

Comparación con `4d0d909`; A = incorporado, M = modificado, D = retirado. Se añade este informe como archivo de documentación.

- **A** [CODEX_AUTORIZACION_BLOQUES_4_5.md](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/CODEX_AUTORIZACION_BLOQUES_4_5.md)
- **A** [CODEX_BLOQUE_4.md](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/CODEX_BLOQUE_4.md)
- **A** [CODEX_BLOQUE_5.md](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/CODEX_BLOQUE_5.md)
- **A** [app/Http/Controllers/AcademicLoadController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/AcademicLoadController.php)
- **A** [app/Http/Controllers/CompanyPortalController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/CompanyPortalController.php)
- **M** [app/Http/Controllers/Coordination/StudentController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/Coordination/StudentController.php)
- **M** [app/Http/Controllers/Coordination/TeacherController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/Coordination/TeacherController.php)
- **M** [app/Http/Controllers/DefenseController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/DefenseController.php)
- **M** [app/Http/Controllers/FollowupController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/FollowupController.php)
- **M** [app/Http/Controllers/StudentFlowController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/StudentFlowController.php)
- **M** [app/Http/Controllers/TeacherPortalController.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Http/Controllers/TeacherPortalController.php)
- **A** [app/Models/AcademicLoad.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Models/AcademicLoad.php)
- **A** [app/Models/AcademicLoadChange.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Models/AcademicLoadChange.php)
- **M** [app/Models/Teacher.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Models/Teacher.php)
- **A** [app/Policies/AcademicLoadPolicy.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Policies/AcademicLoadPolicy.php)
- **M** [app/Services/DefenseScheduling.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/app/Services/DefenseScheduling.php)
- **A** [database/migrations/2026_11_11_130000_create_academic_loads_tables.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/database/migrations/2026_11_11_130000_create_academic_loads_tables.php)
- **M** [database/seeders/AcademicClosingSeeder.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/database/seeders/AcademicClosingSeeder.php)
- **M** [database/seeders/AcademicFlowSeeder.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/database/seeders/AcademicFlowSeeder.php)
- **M** [database/seeders/DatabaseSeeder.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/database/seeders/DatabaseSeeder.php)
- **A** [database/seeders/DevelopmentTextCleanupSeeder.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/database/seeders/DevelopmentTextCleanupSeeder.php)
- **M** [database/seeders/RoleSeeder.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/database/seeders/RoleSeeder.php)
- **D** C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/public/js/practices-store.js
- **M** [resources/views/academic/defenses/show.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/academic/defenses/show.blade.php)
- **A** [resources/views/academic/loads/fields.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/academic/loads/fields.blade.php)
- **A** [resources/views/academic/loads/index.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/academic/loads/index.blade.php)
- **M** [resources/views/coordinacion/dashboard.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/coordinacion/dashboard.blade.php)
- **M** [resources/views/coordinacion/docente-detalle.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/coordinacion/docente-detalle.blade.php)
- **M** [resources/views/coordinacion/docentes.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/coordinacion/docentes.blade.php)
- **M** [resources/views/coordinacion/estudiantes.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/coordinacion/estudiantes.blade.php)
- **M** [resources/views/coordinacion/expediente.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/coordinacion/expediente.blade.php)
- **M** [resources/views/coordinacion/modulo.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/coordinacion/modulo.blade.php)
- **M** [resources/views/coordinacion/practica-detalle.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/coordinacion/practica-detalle.blade.php)
- **M** [resources/views/docente/alertas.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/docente/alertas.blade.php)
- **D** C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/docente/carga-academica.blade.php
- **M** [resources/views/docente/dashboard.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/docente/dashboard.blade.php)
- **M** [resources/views/docente/modulo.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/docente/modulo.blade.php)
- **M** [resources/views/docente/practicantes.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/docente/practicantes.blade.php)
- **M** [resources/views/estudiante/dashboard.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/estudiante/dashboard.blade.php)
- **M** [resources/views/estudiante/docente.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/estudiante/docente.blade.php)
- **M** [resources/views/estudiante/empresa.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/estudiante/empresa.blade.php)
- **M** [resources/views/estudiante/modulo.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/estudiante/modulo.blade.php)
- **M** [resources/views/estudiante/practica.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/estudiante/practica.blade.php)
- **M** [resources/views/jefe_inmediato/dashboard.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/jefe_inmediato/dashboard.blade.php)
- **M** [resources/views/jefe_inmediato/modulo.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/jefe_inmediato/modulo.blade.php)
- **M** [resources/views/layouts/company.blade.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/resources/views/layouts/company.blade.php)
- **M** [routes/web.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/routes/web.php)
- **M** [tests/Feature/AcademicClosingTest.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/tests/Feature/AcademicClosingTest.php)
- **A** [tests/Feature/DeliveryAuditTest.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/tests/Feature/DeliveryAuditTest.php)
- **M** [tests/SmokeAcademicFlows.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/tests/SmokeAcademicFlows.php)
- **A** [tests/ValidateAcademicDelivery.php](C:/Users/50497/Desktop/ProyectoWebII/sistema-practicas-unicah/tests/ValidateAcademicDelivery.php)
