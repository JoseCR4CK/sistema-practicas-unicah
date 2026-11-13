# Carga académica basada en catálogo

Fecha: 13 de noviembre de 2026. Rama: `codex/catalogo-carga-academica`.

## 1. Estructura creada

Se incorporaron `academic_careers` y `academic_subjects`. Una carrera tiene asignaturas; cada asignatura admite código opcional, nombre, créditos, horas clase semanales, estado y procedencia. Los códigos no se generan ni se deducen del nombre.

`academic_loads` incorpora relaciones opcionales a carrera y asignatura del catálogo. Sus campos anteriores se conservan como datos históricos. En los registros nuevos y las correcciones, nombre, carrera, créditos y horas se obtienen en servidor desde la asignatura seleccionada. Los valores arbitrarios enviados por el navegador no son la fuente de esos campos.

Los cambios siguen usando el historial existente, con antes/después, actor, motivo y control de revisión. No se alteró la lógica de Defensa ni su consulta de clases persistentes, incluidas las clases históricas pendientes de asociación.

## 2. Carreras cargadas

Se incorporaron únicamente las tres carreras ya presentes en los expedientes:

- Administración de Empresas.
- Ingeniería en Ciencias de la Computación.
- Ingeniería Industrial.

Su procedencia queda indicada en el catálogo. No se inventaron códigos de carrera.

## 3. Asignaturas cargadas

**Cero asignaturas en MySQL.** Los archivos actuales no contienen un plan institucional confiable que permita importar asignaturas con sus créditos/códigos. El catálogo temporal anterior era de prototipo y no se utilizó como fuente académica.

La arquitectura completa está implementada y probada con asignaturas sintéticas exclusivamente en SQLite en memoria. No se esperó a tener todos los planes ni se insertaron ejemplos en la base real para aparentar disponibilidad.

## 4. Información pendiente

Faltan las asignaturas y sus datos institucionales para las tres carreras. `Redes I` existe como carga manual, pero no se convirtió en catálogo por el solo hecho de estar registrada. La carrera abreviada de esa carga tampoco se asumió automáticamente como un identificador oficial de carrera.

Hasta incorporar al menos una asignatura confirmada, las pantallas conservan el historial y explican que las altas/correcciones requieren una asignatura válida del catálogo. No ofrecen escritura libre para eludir esa condición.

El catálogo puede ampliarse mediante seeders posteriores usando los modelos existentes, sin nuevas tablas ni cambios del flujo. Para una asignatura con código oficial se utiliza carrera + código como identidad; si no existe código, se utiliza carrera + nombre confirmado. Se conserva la procedencia y se evita sobrescribir ediciones con seeders de restauración.

Para créditos distintos de 2/3/4 se mantiene la equivalencia pendiente: no se extrapola una fórmula. Solo una confirmación institucional posterior puede ampliar las equivalencias admitidas por la regla central.

## 5. Equivalencias utilizadas

| Créditos | Horas clase semanales | Máximo semanal |
| --- | --- | --- |
| 2 | 3 | 150 minutos |
| 3 | 4 | 200 minutos |
| 4 | 5 | 250 minutos |

Una hora clase equivale a 50 minutos. Estas son las únicas equivalencias automáticas admitidas. Si las horas explícitas del catálogo contradicen la equivalencia confirmada, la asignatura no es seleccionable hasta corregir sus datos.

## 6. Cálculo e interfaz

`AcademicClassTime` centraliza las equivalencias y el cálculo del servidor:

`(hora final − hora inicial, en minutos) × cantidad de días seleccionados`.

Se compara con `horas clase semanales × 50`. Se permite un horario menor y se identifica como **Horario parcial**; no se exige igualdad ni se inventa un mínimo institucional adicional.

Período es un desplegable I/II/III Período Académico, con año separado. Carrera precede a Asignatura. La asignatura inicia bloqueada, muestra únicamente opciones de la carrera y se limpia al cambiarla. Créditos y horas son de solo lectura, se limpian junto a la selección y se actualizan inmediatamente desde los datos del catálogo servidos por Laravel.

JavaScript muestra el presupuesto preventivo y un mensaje nativo de validación cuando se supera. El servidor vuelve a consultar el catálogo y valida independientemente de JavaScript. No se utilizan stores ni localStorage para el catálogo.

## 7. Validaciones realizadas

- Solo I, II y III como valores nuevos de período.
- Carrera y asignatura existentes, activas y relacionadas entre sí.
- Créditos presentes y equivalencia confirmada; no se aceptan valores libres del request como autoridad.
- Días únicos, horas válidas y fechas de vigencia coherentes.
- Rechazo del exceso de minutos con el máximo y el total seleccionado en el mensaje.
- Conservación de cargas parciales, períodos anteriores y revisiones.
- Correcciones de Coordinación con motivo obligatorio y la misma validación de catálogo.
- Autorización del docente propietario y de los roles de gestión existentes, sin añadir permisos.
- Revalidación al cambiar de asignatura, con registro de valores anteriores.
- Conflictos de Defensa todavía detectados desde las clases persistentes.

Los períodos históricos con variantes de escritura se pueden filtrar y visualizar sin reescribirlos en la migración. Al editar se muestra la opción I/II/III correspondiente cuando la variante es reconocida.

## 8. Tratamiento de Redes I

Se conservó íntegramente la carga existente, identificador 3:

- Asignatura: Redes I.
- Carrera histórica: Ing. en Ciencias de la Computación.
- Período original: III Periodo Académico; año 2026.
- Sección: IF-346. No se asumió que fuese un código de asignatura.
- Créditos: 4.00; horas clase semanales: 5.00.
- Lunes a viernes, 10:00–10:50, 250 minutos semanales.
- Vigencia: 16/09/2026–11/12/2026; estado, autor, revisión y timestamps originales conservados.

Su vínculo de catálogo permanece vacío y la interfaz muestra **Pendiente de asociación al catálogo**, con los datos históricos visibles. No se cambió silenciosamente su carrera ni se sustituyeron sus números por valores vacíos en la base. Los campos calculados del formulario permanecen pendientes hasta seleccionar una asignatura confirmada.

La asociación automática exige una sola coincidencia de nombre y carrera, metadatos confirmados compatibles y un horario dentro del máximo. No se usan coincidencias difusas ni abreviaturas supuestas. Las asociaciones inequívocas conservan los datos originales y registran un evento técnico explícito en el historial; la referencia de usuario es el autor original. Los casos ambiguos o incompatibles quedan pendientes.

Se guardó un respaldo previo de las cargas en `storage/app/catalog-loads-before.json`, dentro del repositorio e ignorado por Git. La comprobación posterior compara todos los campos originales. Ese respaldo de auditoría no debe sobrescribirse ni tratarse como mecanismo para restaurar cambios legítimos posteriores.

## 9. Archivos creados

- `app/Models/AcademicCareer.php`.
- `app/Models/AcademicSubject.php`.
- `app/Services/AcademicClassTime.php`.
- `app/Services/AssociateLegacyAcademicLoads.php`.
- `database/migrations/2026_11_13_100000_create_academic_catalog_tables.php`.
- `database/seeders/AcademicCatalogSeeder.php`.
- `public/js/academic-load-catalog.js`.
- `tests/Feature/AcademicCatalogTest.php`.
- `tests/RenderAcademicCatalogFixture.php`.
- `tests/ValidateAcademicCatalog.php`.
- Este informe y las evidencias `mysql-validacion.json` y `redes-i-conservada.png`.

La fixture HTML temporal usada para comprobar JavaScript se retiró de `public` al finalizar. No quedó publicada como una página adicional del sistema.

## 10. Archivos modificados

- `app/Http/Controllers/AcademicLoadController.php`.
- `app/Models/AcademicLoad.php`.
- `database/seeders/DatabaseSeeder.php`.
- `resources/views/academic/loads/fields.blade.php`.
- `resources/views/academic/loads/index.blade.php`.
- `tests/Feature/AcademicClosingTest.php`.

No se modificaron controladores/servicios de Defensa, roles, policies, calendarios académicos ni otros módulos.

## 11. Migración aplicada

Migración aditiva `2026_11_13_100000_create_academic_catalog_tables`, aplicada correctamente en el lote 7 de `sistema_practicas_unicah`. Crea las dos tablas, índices y relaciones nullable para conservar registros antiguos. Las 20 migraciones figuran aplicadas; no se utilizaron comandos destructivos.

`AcademicCatalogSeeder` se ejecutó repetidamente y la comparación confirmó idempotencia. Solo incorporó las tres carreras; no añadió asignaturas ni modificó Redes I.

## 12. Pruebas agregadas y adaptadas

Se añadieron seis pruebas de catálogo: períodos y pertenencia carrera/asignatura; resistencia a manipulación para Docente y Coordinación; equivalencias y límites/horarios parciales; edición e historial; estructura de los desplegables y datos de solo lectura; asociación única frente a duplicados, abreviaturas no confirmadas y horarios incompatibles.

Las pruebas existentes de Carga/Defensa ahora seleccionan asignaturas de un catálogo aislado de PHPUnit. El horario sigue detectando conflictos en Defensa. Las comprobaciones de aislamiento se aplican a la colección de cargas, diferenciándola de las opciones generales del catálogo académico.

En navegador se comprobó el bloqueo inicial, la selección dependiente, limpieza al cambiar carrera, valores 2→3 y 3→4, horario parcial y mensaje de 250 minutos frente al máximo de 150. Los datos de ese escenario se generaron solo en memoria. Se revisaron también el historial real de Redes I, su formulario y la vista de Coordinación; no se guardaron cambios desde el navegador.

## 13. Resultado final

- `php artisan migrate --no-interaction`: correcto.
- `php artisan test --do-not-cache-result`: **51 pruebas aprobadas, 1151 aserciones**.
- `php artisan route:list`: **107 rutas**; se conservaron las seis rutas de Carga, sin añadir endpoints innecesarios.
- `php artisan view:cache`: correcto.
- `node --check public/js/academic-load-catalog.js`: correcto.
- `git diff --check`: sin errores.
- GET sobre MySQL: **52 respuestas esperadas** y **115 bloques de scripts renderizados** con sintaxis comprobada.
- `ValidateAcademicCatalog.php`: campos originales conservados, catálogo idempotente, tres carreras, cero asignaturas y Redes I sin asociación indebida.

Durante las pruebas se ajustaron dos comprobaciones de test: una confundía las opciones generales del catálogo con los registros filtrados del docente; otra comparaba un modelo recién creado antes de recuperar los defaults y el orden de columnas de SQL. Se corrigieron los snapshots y se repitió la suite completa, sin omitir fallos.

## 14. Commit y cierre

Commit descriptivo de esta mejora: «Implementa catalogo y validacion de carga academica». El identificador exacto se entrega en la respuesta final.

No se hizo push ni se avanzó a otro módulo. Se conservan sin seguimiento, como al inicio, los dos archivos de instrucciones del usuario. Todo el trabajo y el respaldo permanecen dentro del repositorio autorizado; únicamente se utilizó `sistema_practicas_unicah` como base MySQL y SQLite en memoria para pruebas.
