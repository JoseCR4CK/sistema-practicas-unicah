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

Revisa los seeders antes de ejecutarlos sobre datos editados. Sus registros son de desarrollo, no información institucional real. No reconstruyas destructivamente una instalación para actualizarla. El catálogo académico todavía requiere asignaturas verificadas; no se inventan códigos ni créditos.

Abre `http://127.0.0.1:8000`. El ingreso utiliza DNI y contraseña; las cuentas de desarrollo y sus roles están definidos en `database/seeders/`. Para compilar recursos Vite, cuando corresponda: `npm install` y `npm run build` (o `npm run dev` durante desarrollo).

## Estructura frontend/backend

- `routes/web.php`: navegación y formularios web protegidos por sesión y rol.
- `routes/api.php`: endpoints REST de Empresas, habilitados en `bootstrap/app.php`.
- `app/Http/Controllers/`: controladores web y API separados.
- `app/Models/`: relaciones y persistencia Eloquent.
- `app/Validation/CompanyRules.php`: validaciones compartidas entre Empresas web y API.
- `database/migrations/` y `database/seeders/`: esquema y datos de desarrollo.
- `resources/views/`: presentación Blade; `public/css/` y `public/js/`: recursos de interfaz.
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

```powershell
php artisan route:list
php artisan test --do-not-cache-result
php artisan view:cache
git diff --check
```

`phpunit.xml` configura SQLite en memoria para aislar la suite de MySQL local. Las pruebas Feature de API cubren consultas, creación, validación, PUT/PATCH, IDs inexistentes, eliminación segura, roles y autenticación. No requieren borrar datos de MySQL.
