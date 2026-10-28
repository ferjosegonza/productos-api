# Productos API

API REST desarrollada con Laravel para administrar productos, sus precios en distintas monedas y las monedas disponibles.

Repositorio: https://github.com/ferjosegonza/productos-api

## 1. Tecnologías y herramientas

### Entorno utilizado

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

Las versiones indicadas corresponden al entorno de desarrollo registrado durante la implementación.

### Herramientas alternativas

Laravel es la tecnología base de esta aplicación. Las demás herramientas pueden reemplazarse según las necesidades del entorno:

- **Bases de datos:** MySQL, PostgreSQL, SQLite o SQL Server, siempre que la versión de Laravel utilizada y el esquema sean compatibles.
- **Pruebas HTTP:** Postman, Insomnia, Bruno, Hoppscotch o `curl`.
- **Entorno local:** XAMPP, otro servidor local compatible con PHP o el servidor de desarrollo de Laravel.
- **Control de versiones:** Git con GitHub u otro servidor Git compatible.

Se debe tener en cuenta que cambiar una herramienta no garantiza que todo funcione sin ajustes. Por ejemplo, al cambiar de base de datos deben revisarse las migraciones, los tipos de datos, las restricciones y el controlador de PHP requerido.

## 2. Funcionalidades

La API permite:

- Consultar productos con paginación.
- Crear, consultar, actualizar y eliminar productos.
- Consultar los precios asociados a un producto.
- Registrar precios alternativos para un producto en distintas monedas.
- Consultar y registrar monedas.
- Validar los datos recibidos y devolver respuestas JSON con códigos HTTP apropiados.

## 3. Requisitos previos

Para ejecutar el proyecto se necesita:

1. PHP en una versión compatible con las dependencias del proyecto. El entorno utilizado requiere PHP 8.4.1 o superior.
2. Composer.
3. Una base de datos compatible con Laravel.
4. Git, si se desea clonar el repositorio.
5. Un cliente HTTP para probar los endpoints, aunque también se pueden utilizar comandos de terminal.

Los controladores de base de datos de PHP deben estar habilitados según el motor elegido. Por ejemplo, `pdo_mysql` para MySQL, `pdo_pgsql` para PostgreSQL y `pdo_sqlsrv` para SQL Server.

## 4. Instalación y configuración

### 4.1. Obtener el proyecto

Clonar el repositorio:

```bash
git clone https://github.com/ferjosegonza/productos-api.git
cd productos-api
```

### 4.2. Instalar dependencias

```bash
composer install
```

Este comando instala las dependencias definidas en `composer.lock`. No debe reemplazarse por `composer update` durante una instalación normal, porque ese comando puede actualizar versiones de las dependencias.

Verificar el entorno:

```bash
php -v
composer --version
php artisan --version
```

Si Artisan informa que la versión de PHP no es compatible, verificar qué ejecutable utiliza la terminal antes de modificar Composer.

### 4.3. Configurar las variables de entorno

Crear el archivo `.env` a partir de `.env.example`, si todavía no existe:

```bash
cp .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

Configurar en `.env` el nombre de la base de datos, las credenciales y el controlador correspondiente.

### 4.4. Configurar la base de datos

#### Opción A: MySQL

Ejemplo de configuración:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=productos_api
DB_USERNAME=root
DB_PASSWORD=
```

Crear previamente la base de datos `productos_api` desde la herramienta de administración disponible, por ejemplo phpMyAdmin o la consola de MySQL.

Los valores de usuario y contraseña deben ajustarse a la instalación local. No se deben subir credenciales reales al repositorio.

#### Opción B: PostgreSQL

Ejemplo de configuración:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=productos_api
DB_USERNAME=postgres
DB_PASSWORD=
```

Es necesario crear la base de datos, instalar y habilitar el controlador `pdo_pgsql` y comprobar que las migraciones sean compatibles con PostgreSQL.

#### Opción C: SQLite

Crear el archivo de base de datos:

```bash
touch database/database.sqlite
```

Configurar:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Verificar que la ruta sea reconocida correctamente por Laravel y que PHP tenga habilitado `pdo_sqlite`.

#### Opción D: SQL Server

Ejemplo de configuración:

```
DB_CONNECTION=sqlsrv
DB_HOST=127.0.0.1
DB_PORT=1433
DB_DATABASE=productos_api
DB_USERNAME=sa
DB_PASSWORD=
```

Se requiere instalar y configurar el controlador de PHP para SQL Server. Los valores del ejemplo deben sustituirse por los de la instalación real.

**Importante:** los ejemplos son orientativos. Si se cambia el motor de base de datos, debe verificarse la compatibilidad del esquema, las migraciones, los tipos decimales, las claves foráneas y las restricciones únicas.

### 4.5. Ejecutar las migraciones

Con la base de datos creada y `.env` configurado:

```bash
php artisan migrate
```

Este comando crea las tablas pendientes según las migraciones del proyecto.

No utilizar `php artisan migrate:fresh` en una base de datos que contenga información que se deba conservar: elimina las tablas y vuelve a crearlas.

### 4.6. Iniciar el servidor

```bash
php artisan serve
```

Por defecto, Laravel suele exponer el servidor de desarrollo en:

```text
http://127.0.0.1:8000
```

La URL base de la API será:

```text
http://127.0.0.1:8000/api
```

La URL puede cambiar si se utiliza otro puerto, servidor o entorno de despliegue.

## 5. Endpoints disponibles

Todos los endpoints de esta implementación utilizan JSON para las solicitudes y respuestas correspondientes.

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/products` | Listar productos con paginación |
| POST | `/api/products` | Crear un producto |
| GET | `/api/products/{id}` | Consultar un producto |
| PUT | `/api/products/{id}` | Actualizar un producto |
| DELETE | `/api/products/{id}` | Eliminar un producto |
| GET | `/api/products/{productId}/prices` | Consultar los precios de un producto |
| POST | `/api/products/{productId}/prices` | Registrar un precio para un producto |
| GET | `/api/currencies` | Listar monedas |
| POST | `/api/currencies` | Crear una moneda |

La API expone nueve rutas. No se implementó un endpoint para eliminar monedas.

### 5.1. Crear un producto

`POST /api/products`

Ejemplo de cuerpo JSON:

```json
{
  "name": "Notebook",
  "description": "Notebook de prueba",
  "price": 1200,
  "currency_id": 1,
  "tax_cost": 210,
  "manufacturing_cost": 500
}
```

La moneda referenciada debe existir previamente.

### 5.2. Crear una moneda

`POST /api/currencies`

```json
{
  "name": "Dólar estadounidense",
  "symbol": "USD",
  "exchange_rate": 1000
}
```

El tipo de cambio del ejemplo es ilustrativo sólo a efectos de probar que funciona, no una cotización real.

### 5.3. Registrar un precio alternativo

`POST /api/products/1/prices`

```json
{
  "currency_id": 2,
  "price": 1.25
}
```

La moneda debe existir y no debe haber ya un precio registrado para esa combinación de producto y moneda.

### 5.4. Actualizar parcialmente un producto

`PUT /api/products/1`

```json
{
  "name": "Notebook actualizada",
  "price": 1500
}
```

La implementación admite actualizaciones parciales mediante validaciones con `sometimes`. Los campos omitidos no se reemplazan automáticamente por valores nulos.

## 6. Validaciones y códigos HTTP

| Código | Significado en la API |
|---|---|
| 200 | Operación exitosa |
| 201 | Recurso creado |
| 404 | Recurso inexistente o ruta no implementada |
| 422 | Error de validación |

Entre las validaciones verificadas se encuentran:

- Los precios no pueden ser negativos.
- Un precio igual a cero está permitido.
- La moneda referenciada debe existir.
- No se puede registrar dos veces la misma combinación de producto y moneda.
- El nombre de una moneda debe ser único.
- El tipo de cambio debe ser mayor que cero.
- Las consultas a recursos inexistentes devuelven 404.

Los valores decimales pueden aparecer en el JSON como cadenas, por ejemplo `"1200.00"` o `"1.00000000"`.

## 7. Cómo probar la API

### 7.1. Postman

1. Iniciar el servidor de Laravel.
2. Crear una solicitud HTTP.
3. Usar la URL base `http://127.0.0.1:8000/api`.
4. Seleccionar el método HTTP y agregar el endpoint.
5. Para solicitudes JSON, seleccionar `Body > raw > JSON`.
6. Agregar estos encabezados:

```text
Accept: application/json
Content-Type: application/json
```

7. Enviar la solicitud y revisar el código HTTP y el cuerpo de la respuesta.

Para evitar posibles errores, tener en cuenta que el encabezado `Accept: application/json` ayuda a que Laravel devuelva errores de validación en JSON. `Content-Type: application/json` indica que el cuerpo enviado está codificado en JSON.

### 7.2. Alternativas a Postman

- **Insomnia:** aplicación de escritorio para probar APIs HTTP.
- **Bruno:** cliente de APIs que permite mantener colecciones en archivos dentro del repositorio.
- **Hoppscotch:** cliente de APIs accesible desde el navegador.
- **curl:** herramienta de terminal disponible en numerosos sistemas.

Ejemplo equivalente con `curl`:

```bash
curl -i -X GET \
  http://127.0.0.1:8000/api/products \
  -H "Accept: application/json"
```

Ejemplo de creación de un producto:

```bash
curl -i -X POST \
  http://127.0.0.1:8000/api/products \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Notebook","description":"Producto de prueba","price":1200,"currency_id":1,"tax_cost":210,"manufacturing_cost":500}'
```

En Windows se puede utilizar `curl.exe` desde PowerShell o Git Bash. La sintaxis de comillas puede variar según la terminal.

## 8. Pruebas manuales realizadas

Las pruebas de esta implementación se realizaron manualmente con Postman. No se debe interpretar esta lista como evidencia de una suite automatizada de pruebas.

Se verificaron, entre otros, los siguientes casos:

- Creación, listado, consulta, actualización y eliminación de productos.
- Respuesta 404 para productos inexistentes.
- Listado y creación de monedas.
- Registro y consulta de precios alternativos.
- Rechazo de precios negativos y aceptación del precio cero.
- Rechazo de monedas inexistentes.
- Rechazo de nombres de monedas duplicados.
- Rechazo de tipos de cambio iguales a cero.
- Rechazo de precios duplicados para la misma combinación de producto y moneda.
- Actualización parcial de productos.
- Respuestas JSON de validación con código 422.
- Paginación con resultados y páginas sin registros.

La eliminación de monedas no se probó como operación de negocio: el endpoint DELETE no forma parte de las rutas expuestas.

## 9. Errores frecuentes y cómo evitarlos

### Error: Composer indica que PHP es demasiado antiguo

**Causa:** la terminal puede estar utilizando un ejecutable de PHP diferente del que se configuró para el proyecto.

En el entorno utilizado, PHP 8.4 se instaló en `C:\php84`. Para Git Bash:

```bash
export PATH="/c/php84:$PATH"
```
NOTA: esto configura temporalmente en la terminal sobre la cual se está trabajando.

Verificar:

```bash
which php
php -v
```

Para conservar la configuración en nuevas terminales de Git Bash:

```bash
echo 'export PATH="/c/php84:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

Este ajuste solo configura Git Bash; no cambia automáticamente la versión de PHP usada por Apache u otros programas de XAMPP.

### Error: Laravel devuelve HTML o una redirección al probar la API

**Causa posible:** la solicitud no indica que espera JSON.

Agregar:

```text
Accept: application/json
Content-Type: application/json
```

En Postman, confirmar que el cuerpo esté configurado como JSON. Si se observa una respuesta HTML inesperada, desactivar temporalmente el seguimiento automático de redirecciones permite comprobar si el servidor devuelve un 302.

### Error 422 al registrar un precio

Comprobar que:

- El producto existe.
- La moneda existe.
- El precio no es negativo.
- No se haya registrado previamente esa moneda para el mismo producto.

Si la solicitud incumple varias reglas, Laravel puede devolver más de un error de validación.

### Error 404 al consultar o eliminar un recurso

Verificar el identificador, la URL y el método HTTP. Un 404 al intentar `DELETE /api/currencies/{id}` es esperado en esta versión porque esa ruta no está implementada.

### Una página de productos devuelve una lista vacía

La paginación puede responder con HTTP 200 y un arreglo `data` vacío cuando se solicita una página que no contiene registros. Revisar `current_page`, `last_page` y `total`.

### Problemas después de cambiar la base de datos

Verificar:

- El valor de `DB_CONNECTION`.
- La dirección, puerto, nombre de base de datos y credenciales.
- El controlador de PHP correspondiente.
- La existencia de la base de datos.
- La compatibilidad de las migraciones con el nuevo motor.

Si Laravel sigue utilizando la configuración anterior, revisar el archivo `.env` y, si corresponde, ejecutar:

```bash
php artisan config:clear
```

## 10. Git y publicación de cambios

Consultar el estado del repositorio:

```bash
git status
```

Preparar y confirmar cambios:

```bash
git add .
git commit -m "docs: update project documentation"
```

Publicar la rama:

```bash
git push origin main
```

GitHub no acepta la contraseña normal de la cuenta para operaciones Git por HTTPS. La autenticación debe realizarse mediante un mecanismo compatible, como Git Credential Manager o un token de acceso personal.

Antes de utilizar `git add .`, revisar que no se incluyan archivos sensibles, credenciales, archivos `.env` o artefactos generados.

## 11. Consideraciones de alcance

Esta documentación describe el comportamiento implementado y probado hasta el momento. Antes de utilizar la API en producción se deben evaluar, entre otros aspectos, autenticación, autorización, límites de solicitudes, configuración segura, manejo de errores y pruebas automatizadas.
