# Sistema de Prácticas Profesionales UNICAH

Proyecto universitario para gestionar procesos de práctica profesional. El sistema continúa en desarrollo: algunas funcionalidades de prototipo, catálogos y decisiones institucionales siguen pendientes.

## Segundo Parcial

La entrega incluye mejoras visuales, persistencia MySQL, autenticación por DNI, sesiones y roles, backend Laravel/Eloquent, CRUD de Empresas, API REST JSON, validaciones y manejo de errores. Las vistas y formularios integran frontend, backend y base de datos. Esto no representa la finalización de todos los módulos del sistema.

## Tecnologías y requisitos

- PHP 8.2 o compatible con las dependencias de Composer, Laravel 12 y Composer.
- MySQL y una base local `sistema_practicas_unicah`.
- Extensiones habituales de Laravel, PDO MySQL y PDO SQLite para las pruebas en memoria.
- Frontend Blade, HTML, CSS y JavaScript; Node.js y Vite cuando se compilen los recursos de `resources/`.

## Configuración y ejecución local

Desde la carpeta del repositorio:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Copia `.env` y genera la clave únicamente al preparar una instalación nueva. No sobrescribas un `.env` ni una clave existentes y no publiques credenciales. Configura una base MySQL ya creada y las credenciales de tu entorno:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_practicas_unicah
DB_USERNAME=usuario_local
DB_PASSWORD=clave_local
```

La conexión se define en `config/database.php` mediante `.env`. MySQL debe estar disponible antes de ejecutar migraciones o seeders. Para preparar o actualizar el esquema y cargar datos de desarrollo:

```powershell
php artisan migrate
php artisan db:seed
php artisan serve
```

Revisa los seeders antes de ejecutarlos sobre datos editados. Los seeders de demostración contienen datos de desarrollo; `AcademicCatalogSeeder` importa la fuente académica versionada y conserva sus conflictos. No reconstruyas destructivamente una instalación para actualizarla ni inventes códigos o créditos faltantes.

Abre `http://127.0.0.1:8000`. El ingreso utiliza DNI y contraseña; las cuentas de desarrollo y sus roles están definidos en `database/seeders/`. Para compilar recursos Vite, cuando corresponda: `npm install` y `npm run build` (o `npm run dev` durante desarrollo).

## Estructura frontend/backend

- `routes/web.php`: navegación y formularios web protegidos por sesión y rol.
- `routes/api.php`: endpoints REST de Empresas, habilitados en `bootstrap/app.php`.
- `app/Http/Controllers/`: controladores web y API separados.
- `app/Models/`: relaciones y persistencia Eloquent.
- `app/Validation/CompanyRules.php`: validaciones compartidas entre Empresas web y API.
- `database/migrations/` y `database/seeders/`: esquema y datos de desarrollo.
- `resources/views/`: presentación Blade; `resources/css/` y `resources/js/`: entradas y fuentes compiladas por Vite; `public/js/`: scripts específicos conservados por pantalla.
- `tests/`: pruebas automatizadas. Algunas herramientas personales del estudiante conservan localStorage; no se presentan como persistencia MySQL.

Flujo principal: Route → Controller → Eloquent → MySQL → Blade o JSON.

## CRUD de Empresas

Coordinación y ROOT pueden listar, consultar, crear y actualizar empresas desde `/coordinacion/empresas`, y gestionar responsables. Se mantienen separados `official_name` y `commercial_name`. El CRUD web conserva diseño, rutas, redirecciones y reglas. La API reutiliza el mismo modelo y validaciones, sin columnas ni fuentes paralelas.

## API REST de Empresas

Base local: `http://127.0.0.1:8000/api`. Las respuestas y errores son JSON; la eliminación exitosa devuelve cuerpo vacío. Los registros se devuelven bajo `data`: arreglo en listado y objeto en consulta/escritura.

### Autenticación y seguridad

Se reutiliza la sesión Laravel existente: inicia sesión por DNI desde el formulario web y conserva la cookie. Solo usuarios activos COORDINACION o ROOT tienen acceso. No se instalaron paquetes ni se implementaron tokens Bearer. Las escrituras mantienen protección CSRF: envía el token de la sesión mediante `X-CSRF-TOKEN` o `_token`, junto con la cookie. En un cliente externo conserva las cookies desde el login y renueva el token después de autenticarte. No desactives CSRF.

Ejemplo en la consola del navegador, desde una página autenticada del sistema que contenga un formulario con `_token`:

```javascript
const response = await fetch('/api/companies', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
    },
    body: JSON.stringify({
        official_name: 'Empresa de ejemplo',
        commercial_name: 'Nombre comercial',
        sector: 'Servicios',
        status: 'ACTIVE',
    }),
});
const result = await response.json();
```

### Endpoints

| Método | Endpoint | Operación | Éxito |
| --- | --- | --- | --- |
| GET | `/api/companies` | Listar empresas ordenadas por razón social | 200 |
| GET | `/api/companies/{company}` | Consultar por ID real | 200 |
| POST | `/api/companies` | Crear | 201 y encabezado Location |
| PUT | `/api/companies/{company}` | Actualizar con campos obligatorios | 200 |
| PATCH | `/api/companies/{company}` | Actualizar solo los campos enviados | 200 |
| DELETE | `/api/companies/{company}` | Eliminar solo sin responsables ni prácticas | 204 |

Campos reales: `official_name`, `commercial_name`, `rtn`, `sector`, `address`, `city`, `department`, `phone`, `email`, `status`. Creación y PUT requieren `official_name`, `sector` y `status`; PATCH los valida si se envían. Los demás son opcionales y admiten null. Textos: máximo 255 caracteres; email válido. Razón social y RTN no se repiten (se excluye el propio registro al actualizar). Estado: `ACTIVE` o `INACTIVE`. Campos ajenos a las reglas no se persisten.

Errores: `401` sin sesión, `403` sin permiso o cuenta inactiva, `404` registro inexistente, `419` token CSRF ausente/expirado en escritura, `422` validación con objeto `errors` por campo, `409` eliminación bloqueada por relaciones. No se borran responsables en cascada ni se desvinculan prácticas para permitir un DELETE.

## Pruebas y validación

Antes de probar las vistas, instala y compila el frontend como se explica a continuación. La suite comprueba también el manifest y las utilities realmente generadas.

```powershell
php artisan route:list
php artisan test --do-not-cache-result
php artisan view:cache
git diff --check
```

`phpunit.xml` configura SQLite en memoria para aislar la suite de MySQL local. Las pruebas Feature de API cubren consultas, creación, validación, PUT/PATCH, IDs inexistentes, eliminación segura, roles y autenticación. No requieren borrar datos de MySQL.

## Frontend, Vite y Tailwind

Con Node.js compatible con Vite 7 (20.19+, 22.12+ o posterior compatible) y npm, desde el repositorio:

```powershell
npm ci
npm run build
```

`package-lock.json` fija las versiones reproducibles. `.npmrc` mantiene la caché en `.npm-cache/`, ignorada por Git. Para desarrollo ejecuta `npm run dev` junto con `php artisan serve`; Vite utiliza su configuración de puerto habitual. Detén Vite al terminar para no dejar un `public/hot` apuntando a un servidor detenido. En una entrega sin servidor Vite ejecutándose, usa `npm run build`.

Entradas registradas en `vite.config.js`:

- `resources/css/app.css`: importa el CSS de los portales (`app-shell.css` y `presentation.css`).
- `resources/css/login.css`: importa exclusivamente el diseño de autenticación (`login-page.css`), aislado de los estilos de los portales.
- `resources/js/app.js`: importa Axios y la navegación global una sola vez. Los cuatro layouts y el Login utilizan `@vite` para cargar sus entradas.

Ambas entradas CSS importan `resources/css/tailwind.css`. Tailwind 4 se procesa con `@tailwindcss/vite`, analiza `resources/views` y `resources/js`, usa el prefijo `tw:` y no importa Preflight. Así evita colisiones y resets sobre Bootstrap. El logo del Login utiliza `tw:flex tw:justify-center` para conservar su alineación; estas utilities aparecen en el CSS compilado y se verifican en `FrontendAssetsTest`.

Bootstrap 5.3.8 y Bootstrap Icons 1.11.3 mantienen sus CDN originales y requieren acceso a ellos. No se ha rediseñado el sistema. Los scripts específicos `academic-filters.js`, `coordination-record-modal.js` y `academic-load-catalog.js` permanecen en `public/js/`: solo los cargan las pantallas que los necesitan, conservando sus inicializadores y eventos actuales. La navegación global ya no se carga directamente desde `public/js/`.

Vite genera `public/build/manifest.json`, CSS y JavaScript con hash y la imagen importada del Login. `public/build/` es salida compilada ignorada por Git; no debe editarse manualmente ni confundirse con código fuente. No se conservan copias paralelas del CSS propio en `public/css/`.

Comprobación visual reproducible sin MySQL: `php tests/RenderFrontendReview.php` genera temporalmente las vistas reales de los cinco portales con SQLite en memoria en `public/frontend-review/` (ignorado por Git). Con Laravel servido, abre `/frontend-review/login.html` o `/frontend-review/coordinacion.html`. Sus formularios están bloqueados para evitar escrituras y sus enlaces principales apuntan a otras vistas renderizadas. Son fixtures de presentación, no un acceso autenticado ni una prueba de persistencia MySQL. Retira esos archivos temporales al finalizar la revisión.

## Bloque 6A: catálogo académico y solicitudes de acceso

El catálogo reutiliza `academic_careers` y `academic_subjects`. Contiene las ocho facultades/carreras indicadas para el campus y la unidad **Generales**, que no es una carrera estudiantil. Una asignatura global puede tener varios planes mediante `academic_career_subject`; se conservan período, procedencia y metadatos del Excel.

Fuente: `docs/reference/Catalogo_Academico_UNICAH_SAP_CORREGIDO.xlsx`. Para regenerar su exportación determinista (Python estándar, sin paquetes adicionales):

```powershell
python tools/export_academic_catalog.py
```

Laravel consume `database/data/academic_catalog.json` al importar, y consulta Eloquent/MySQL durante la ejecución normal; no lee Excel en cada petición. Tras iniciar la base MySQL local autorizada, aplica las migraciones aditivas y carga solo el catálogo:

```powershell
php artisan migrate
php artisan db:seed --class=AcademicCatalogSeeder
```

No uses `migrate:fresh`. El catálogo conserva códigos ausentes como null, referencias con conflictos y valores editados. Los planes duplicados comparten asignatura; la coincidencia de una fila sin código se resuelve únicamente si nombre, tipo y créditos coinciden inequívocamente con otro registro de la misma fuente. No se asimila Administración de Empresas a Gestión Estratégica de Empresas ni una facultad histórica genérica a una unidad específica.

La regla central de `AcademicClassTime` es: 1 crédito → 1 hora; 2 créditos de laboratorio explícitamente identificado → 2 horas; 2 créditos normales → 3 horas; desde 3 créditos normales → créditos + 1 horas. Cada hora clase equivale a 50 minutos. No se infiere un laboratorio por una coincidencia parcial en el nombre. Las inconsistencias académicas quedan señaladas y no producen una equivalencia confirmada para la carga académica.

### Registro y revisión

- `/registro/estudiante`: solicitud pública con siete tipos documentales, declaración de asignaturas por facultad y contraseña confirmada. El estudiante escribe su DNI una sola vez; el servidor utiliza ese mismo valor como número de cuenta interno, sin modificar estudiantes históricos. Permite más de dos asignaturas y no impone una excepción automática a Enfermería.
- `/registro/docente`: solicitud pública con facultad/área (incluye Generales), sin documentos ni requisito de inducción.
- El envío crea una **solicitud pendiente**, no un User activo. La contraseña se guarda mediante hash.
- Se entrega un **enlace privado de seguimiento** con secreto aleatorio; solo se conserva su hash en base de datos. Permite consultar y corregir cuando CA lo solicita. No hay envío de correos ni recuperación de ese enlace en este bloque; consérvalo y no lo compartas.
- `/coordinacion/solicitudes-acceso`: módulo exclusivo para CA/ROOT, separado de las solicitudes de práctica; incluye filtros, contadores y advertencia para más de dos asignaturas.
- CA inicia la revisión, evalúa documentos, solicita correcciones o rechaza con motivo. Los resultados documentales no tienen aprobación predeterminada.
- La inducción sigue usando una lista **física**. CA registra su verificación manual; no hay módulo ni lista digital de asistentes. Enviar una solicitud no exige haber asistido.
- Para aprobar un estudiante se exige la verificación de inducción y los siete documentos aprobados. En una transacción se crean User/Student, las asociaciones académicas y documentales, y el historial. No se crea una práctica ni se cambia una etapa automáticamente.
- Al aprobar al docente se crea User/Teacher, su unidad principal y su fecha de aprobación. Aparece automáticamente en el filtro por facultad de Coordinación → Docentes. CA puede corregir la unidad principal desde el detalle. La relación admite varias unidades futuras sin crear tablas separadas por facultad.
- El estudiante aprobado ve **Asignaturas cursadas durante la práctica** y sus documentos de acceso en Inicio/Mi práctica. Sus herramientas personales privadas no cambian.

### Archivos y seguridad

Se reutilizan `AcademicDocument` y `DocumentVersion`. Los archivos se almacenan en un disco privado `access_documents`, dentro de `storage/app/private/access-documents`, sin enlace público. Las descargas validan el secreto privado, o bien sesión y pertenencia/autorización en servidor. Los documentos de acceso sin práctica no se mezclan con la revisión documental del proceso ya existente.

Tipos: Datos Generales del lugar de práctica, Carta de Aceptación, identidad del estudiante (ambos lados), identidades de los dos testigos, título de colegio (ambos lados) y solvencia de pago. No se solicita el instructivo de monografía. Se admiten PDF/JPG/JPEG/PNG con validación MIME y extensión, hasta 8 MB por archivo (también aplican los límites de PHP del entorno). Para ambos lados, un PDF que los contenga o dos imágenes. CA verifica el contenido. Cada corrección crea una versión nueva sin borrar las anteriores.

Los estados son Pendiente, En revisión, Requiere corrección, Aprobada y Rechazada. Las acciones verifican estado y revisión vigente, usan CSRF y autorización por rol, y registran eventos. DNI/correo/cuenta no pueden duplicarse. El rol, la inducción y la aprobación no se aceptan del formulario público. Una falla al crear el perfil revierte la creación de User y la aprobación.

Pruebas específicas: `php artisan test --do-not-cache-result --filter="AccessAdmissionsTest|CatalogSchemaTest"`. Revisión visual aislada: `php tests/RenderAccessReview.php`; utiliza SQLite en memoria y genera fixtures temporales bajo `public/frontend-review/`, sin persistir cuentas en MySQL. Su revisión no sustituye las pruebas manuales contra la base local cuando esté disponible.
## Bloque 6B: gestión avanzada de ternas

Se amplía el mismo módulo de `academic_committees` y `committee_members`. Coordinación Académica/ROOT puede registrar una terna pendiente desde el inicio de cualquier práctica y asignar tres docentes distintos: **Coordinador de terna**, **Revisión técnica** y **Revisión de estilo**. El coordinador pasa a ser el docente de seguimiento de esa práctica. Cambiar un seguimiento ya asignado exige motivo.

Cada función permite seleccionar independientemente facultad/área y docente, incluidas las ocho facultades y Generales del catálogo del Bloque 6A. Solo aparecen docentes activos, aprobados y asociados a una unidad activa; no se aprueban ni reclasifican docentes históricos automáticamente. Se admiten ternas interdisciplinarias. Los filtros incluyen facultad del estudiante, período/año de origen, estado, nombre y DNI/cuenta.

`/coordinacion/ternas/carga-docente` muestra cargas activas por período de origen y total, sin imponer cupos. El selector muestra la misma información. El período se conserva en la terna aunque cambien el período de práctica o la fecha de defensa. Sustituir integrantes conserva sus asignaciones anteriores y eventos con actor, fecha, motivo y antes/después; los contadores se consultan desde las relaciones, sin tablas duplicadas de carga.

Una defensa realizada no cierra la terna automáticamente. Coordinación Académica registra el resultado, y solo puede cerrar formalmente con defensa aprobada y verificación manual de evaluación/documentación final. Una defensa reprobada permite abrir una nueva ronda de propuestas y disponibilidad con **la misma terna**; la ronda anterior queda en el historial y sus opciones no pueden reutilizarse como acuerdos vigentes. No se implementa una rúbrica ni se cambia la etapa/estado de la práctica automáticamente. Una práctica nueva tiene otra terna, sin inventar un flujo automático de reprobación total.

La función vigente de cada integrante determina quién registra cada revisión de monografía. Coordinación Académica conserva supervisión; no suplanta a los revisores. Se preservan los plazos 10/3/1 y la entrega a −7 días. Las revisiones ya resueltas mantienen su responsable histórico; sustituir un revisor pendiente no reinicia su plazo.

Aplicar la migración aditiva con `php artisan migrate`. La reversión se prueba en SQLite aislado; se impide revertir si ello perdería historial avanzado. No se han agregado docentes o asignaciones de demostración a MySQL.

Pruebas específicas: `php artisan test --do-not-cache-result --filter="AdvancedCommitteeTest|CommitteeSchemaTest|AcademicClosingTest"`. Revisión visual: `php tests/RenderAdvancedCommitteeReview.php`, exclusivamente con SQLite en memoria y formularios bloqueados. Genera vistas temporales en `public/frontend-review/`; retirarlas después. El informe de entrega está en `docs/bloque-6b/INFORME.md`.
