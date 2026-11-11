BLOQUE 4 AUTÓNOMO — MONOGRAFÍA + CARGA ACADÉMICA + DISPONIBILIDAD

Tienes autorización previa para completar TODO este bloque sin solicitar confirmaciones intermedias.

Cuando termines este bloque y todas las pruebas pasen, continúa automáticamente con el BLOQUE 5 que recibirás a continuación.

ALCANCE ABSOLUTO

Trabaja únicamente dentro de:

C:\Users\50497\Desktop\ProyectoWebII\sistema-practicas-unicah

La única base de datos autorizada es:

sistema_practicas_unicah

No accedas ni modifiques archivos fuera del repositorio.

AUTORIZACIÓN DE COMANDOS

Dentro del repositorio tienes autorización para ejecutar sin preguntar:

- lectura y edición de archivos;
- git status;
- git diff;
- git log;
- git branch;
- git checkout;
- creación de ramas;
- git add;
- git commit;
- php artisan migrate;
- php artisan migrate:status;
- php artisan db:seed;
- php artisan db:seed --class=...;
- php artisan test;
- php artisan route:list;
- php artisan optimize:clear;
- comandos de validación PHP/JavaScript necesarios;
- consultas de lectura/escritura exclusivamente sobre `sistema_practicas_unicah`.

NO ESTÁ AUTORIZADO:

- git push;
- git reset --hard;
- git clean -fd;
- migrate:fresh;
- db:wipe;
- DROP DATABASE;
- DROP TABLE;
- TRUNCATE;
- modificar configuración global de Git/PHP/XAMPP/Windows/VS Code/Codex;
- trabajar fuera del repositorio.

Si estás en `feature/tercer-bloque-cierre-academico` y el árbol está limpio, crea:

feature/autonomia-bloques-4-5

y continúa allí.

────────────────────
PARTE A — CORRECCIÓN FINAL DE MONOGRAFÍA
────────────────────

Corrige primero los puntos pendientes del Bloque 3.

1. Una revisión técnica en estado “En revisión” NO debe tener “Aprobada” seleccionada por defecto.

El usuario debe elegir explícitamente:

- Aprobada;
- Requiere corrección;

o los estados equivalentes ya implementados.

Usa una opción inicial equivalente a:

“Seleccione un resultado”.

2. La revisión técnica de María López tiene como responsable a Msc. Carlos Martínez.

El docente responsable debe poder:

- consultar la monografía;
- ver versión;
- ver estado;
- ver inicio;
- ver fecha límite;
- registrar su resultado;
- registrar observación.

La autorización debe comprobarse en servidor.

Otro docente no puede registrar esa revisión.

Coordinación conserva consulta y supervisión, pero no debe registrar una revisión técnica en nombre del docente responsable salvo que exista una regla institucional explícita.

3. Integra la revisión técnica en el portal Docente, preferiblemente dentro del seguimiento del practicante si encaja con el diseño actual.

No es obligatorio crear otra opción del menú.

4. Debe existir una sola monografía por práctica con múltiples versiones.

Si ya existe una monografía:

- no crear otra monografía independiente;
- una nueva entrega genera una nueva versión cuando corresponda;
- conservar versiones anteriores;
- conservar historial de revisiones.

La interfaz debe distinguir:

“Registrar monografía”

de:

“Registrar nueva versión”.

5. No inventes una dependencia obligatoria Visita → Monografía.

Si Visita está pendiente y Monografía está en revisión, conserva ambos estados reales.

No automatices cambios de etapa basándote en una regla no confirmada.

6. Mantén pendientes:

- responsable institucional definitivo de revisión de estilo;
- responsable definitivo de revisión del coordinador;
- requisitos completos para Defensa;
- posible conflicto entre finalización de práctica y plazo de revisión.

No inventes respuestas.

────────────────────
PARTE B — MIGRAR CARGA ACADÉMICA
────────────────────

El portal Docente ya tiene una interfaz de Carga Académica que aún depende de información local/prototipo.

Migra únicamente la carga académica necesaria para programación de Defensa hacia:

Laravel + Eloquent + MySQL.

Debe poder representar como mínimo:

- docente;
- período académico;
- año;
- asignatura;
- días;
- hora de inicio;
- hora de finalización;
- créditos cuando estén disponibles;
- horas de clase semanales cuando estén disponibles;
- estado.

No inventes equivalencias de créditos.

Si existen valores de créditos y horas, almacena ambos independientemente.

No calcules automáticamente equivalencias no confirmadas.

Los períodos académicos deben poder conservar historial.

────────────────────
PARTE C — DISPONIBILIDAD DE DEFENSA
────────────────────

Conecta Defensa con la carga académica persistente.

Regla confirmada:

SOLO las horas reales de clase del docente bloquean disponibilidad.

No bloquees automáticamente:

- almuerzo;
- transporte;
- reuniones;
- descansos;
- tiempo personal.

El último bloque académico conocido termina a:

9:50 PM

No inventes bloques posteriores.

Cuando exista una opción de Defensa:

- comprobar a todos los integrantes de la terna;
- detectar cruces con clases registradas;
- mantener duraciones permitidas de 60, 90 o 120 minutos;
- no confirmar si existe conflicto académico;
- no confirmar sin coincidencia válida de participantes.

No inventes horarios académicos si no existen registros.

────────────────────
PARTE D — COORDINACIÓN
────────────────────

Coordinación debe poder consultar la carga académica necesaria para corregir errores cuando corresponda.

No construyas un catálogo institucional completo de asignaturas si todavía no existe información suficiente.

No inventes planes de estudio.

No avances a PERSONAL_CAMPUS.

────────────────────
PARTE E — PRUEBAS
────────────────────

Agrega pruebas para:

- resultado técnico sin aprobación predeterminada;
- solo docente responsable registra revisión técnica;
- otro docente recibe denegación;
- estudiante no crea dos monografías para una misma práctica;
- nueva versión conserva versión anterior;
- carga académica persiste;
- historial por período se conserva;
- conflicto de clase bloquea Defensa;
- ausencia de clase no inventa un bloqueo;
- 60/90/120 siguen siendo duraciones válidas.

Ejecuta:

php artisan migrate

php artisan test --do-not-cache-result

php artisan route:list

git diff --check

Aplica migraciones aditivas a `sistema_practicas_unicah` cuando existan.

Puedes ejecutar los seeders necesarios.

No dejes módulos nuevos respondiendo 503 si las migraciones pueden aplicarse correctamente.

────────────────────
PARTE F — GIT
────────────────────

Realiza commits separados y descriptivos.

Ejemplos:

Corrige flujo de revision tecnica de monografia

Migra carga academica docente al backend

Integra carga academica con disponibilidad de defensa

No hagas push.

Al completar el Bloque 4:

- confirma árbol limpio;
- conserva todos los commits;
- continúa AUTOMÁTICAMENTE con el Bloque 5;
- no esperes autorización del usuario.