# Productos API

API REST en Laravel para administrar productos, monedas y precios de productos en otras monedas.

## Requisitos

- PHP 8.4.1 o superior para las dependencias instaladas en este proyecto.
- Composer y una base de datos configurada en Laravel (por ejemplo, MySQL o SQLite).

## Entorno utilizado

| Tecnología / herramienta | Versión o configuración |
|---|---|
| Laravel | 13.39.0 |
| PHP CLI | 8.4.27 |
| Composer | 2.10.3 |
| Sistema operativo | Windows |
| Entorno local | XAMPP | 8.2.12
| Base de datos | MySQL | 9.1
| Cliente HTTP para pruebas | Postman | 11.54.6
| Control de versiones | Git |
| Repositorio remoto | GitHub |
| Terminal | Git Bash |
>
> Las pruebas usan SQLite en memoria. Si `php -m` no muestra `pdo_sqlite` y `sqlite3`, habilitar `extension=pdo_sqlite` y `extension=sqlite3` en php.ini.

## Instalación

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Configura en `.env` la conexión de base de datos. Crea la base de datos si corresponde y luego ejecuta:

```powershell
php artisan migrate
php artisan serve
```

La API queda disponible normalmente en `http://127.0.0.1:8000/api`.

No hay registro público. Antes de probar los endpoints, crea un usuario de confianza desde Tinker:

```powershell
php artisan tinker
```

En el prompt de Tinker pega este código PHP, sin escribir `<?php`:

```php
\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'api@example.com',
    'password' => 'reemplazar-por-una-clave-segura',
]);
```

La contraseña se almacena con hash. Usa una clave segura y escribe `exit` para salir de Tinker.

## Endpoints

| Método | Ruta | Acción |
|---|---|---|
| POST | `/auth/token` | Iniciar sesión y emitir token (6 intentos por minuto) |
| DELETE | `/auth/token` | Revocar el token actual |
| GET | `/products` | Listar productos (15 por página) |
| POST | `/products` | Crear producto |
| GET | `/products/{id}` | Consultar producto y precios |
| PUT | `/products/{id}` | Actualizar producto; admite cambios parciales |
| DELETE | `/products/{id}` | Eliminar producto |
| GET | `/products/{productId}/prices` | Listar precios alternativos |
| POST | `/products/{productId}/prices` | Registrar precio alternativo |
| GET | `/currencies` | Listar monedas |
| POST | `/currencies` | Crear moneda |

Todas las rutas llevan el prefijo `/api`. Las solicitudes con cuerpo deben usar `Content-Type: application/json`; para recibir errores en JSON, enviar `Accept: application/json`.

Excepto `POST /auth/token`, todos los endpoints requieren `Authorization: Bearer <token>`.

Ejemplo de moneda:

```json
{
  "name": "Dólar estadounidense",
  "symbol": "USD",
  "exchange_rate": 1000
}
```

Ejemplo de producto (usa el `id` de una moneda existente):

```json
{
  "name": "Notebook",
  "description": "Producto de prueba",
  "price": 1200,
  "currency_id": 1,
  "tax_cost": 210,
  "manufacturing_cost": 500
}
```

Credenciales inválidas, tokens ausentes, vencidos o revocados devuelven `401`; validaciones fallidas, `422`; recursos inexistentes, `404`; y creaciones exitosas, `201`. Los precios no pueden ser negativos, las monedas deben existir y no se permiten dos precios para la misma combinación de producto y moneda.

## Flujo rápido en Postman

1. Crea un usuario con `php artisan tinker` siguiendo el paso anterior. No hay registro público.
2. Envía un `POST` a `http://127.0.0.1:8000/api/auth/token`. En **Body > raw > JSON**, usa el email y la contraseña de ese usuario:

   ```json
   {
     "email": "api@example.com",
     "password": "TU_CLAVE",
     "device_name": "postman"
   }
   ```

   Agrega `Accept: application/json` y `Content-Type: application/json`. La respuesta `200` incluye el campo `token`.
3. En cada solicitud a productos, monedas o precios, abre **Authorization**, selecciona **Bearer Token** y pega el valor de `token`. También puedes configurar esto en la colección para que lo hereden sus solicitudes.
4. Comprueba con `GET http://127.0.0.1:8000/api/products`. Con token debe responder `200`; sin token debe responder `401`. Para cerrar sesión, envía `DELETE http://127.0.0.1:8000/api/auth/token` con el mismo token.

## Pruebas

Ejecuta la suite automatizada desde PowerShell (en las pruebas realizadas php estaba en C:\php84, la ruta deberá ajustarse si fuera diferente):

```powershell
$env:Path = "C:\php84;$env:Path"
php -v
composer test
```

`composer test` ejecuta PHPUnit, incluidas pruebas de autenticación, revocación y límite de intentos de inicio de sesión.

Para probar la API manualmente:

1. Iniciar el servidor con `php artisan serve`.
2. En Postman o `curl.exe`, solicitar `POST /api/auth/token` con las credenciales del usuario creado. Usar `Accept: application/json` y `Content-Type: application/json`:

```powershell
curl.exe -i -X POST http://127.0.0.1:8000/api/auth/token `
  -H "Accept: application/json" -H "Content-Type: application/json" `
  -d '{"email":"api@example.com","password":"TU_CLAVE","device_name":"prueba-local"}'
```

3. Copiar el campo `token` de la respuesta. En Postman, configurarlo como Bearer Token; con `curl.exe`, agregar `-H "Authorization: Bearer TU_TOKEN"` a cada llamada protegida.
4. Crear una moneda con `POST /api/currencies` y el JSON de ejemplo; debe responder `201`. Usar el `id` devuelto como `currency_id` para crear un producto con `POST /api/products`.
5. Comprobar `GET /api/products` y `GET /api/products/{id}`; actualizar con `PUT /api/products/{id}` y `{"name":"Notebook actualizada"}`.
6. Enviar datos inválidos (por ejemplo, un precio negativo) y verificar `422`. Eliminar el producto con `DELETE /api/products/{id}`; volver a consultarlo debe responder `404`.
7. Cerrar la sesión con `DELETE /api/auth/token` y el mismo Bearer Token. Las solicitudes posteriores con ese token deben responder `401`.

## Seguridad y despliegue

Las rutas de productos, precios y monedas requieren tokens Bearer de Sanctum. Los tokens se almacenan como hashes, vencen a los 60 minutos y se pueden revocar con `DELETE /api/auth/token`. El inicio de sesión está limitado a seis intentos por minuto; los usuarios se crean manualmente y no existe registro público. Todos los usuarios autenticados tienen los mismos permisos sobre los recursos.

En producción:

- Usar HTTPS, `APP_ENV=production` y `APP_DEBUG=false`.
- Mantener secretos, contraseñas y tokens fuera del repositorio; usar credenciales únicas y una cuenta de base de datos con privilegios mínimos.
- Dar acceso solo a usuarios de confianza: actualmente no hay roles ni permisos diferenciados.
- RECOMENDACIÓN: Hacer copias de seguridad y revisar los registros. No compartir ni registrar tokens Bearer.
