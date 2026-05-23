
  # Clonación del repositorio.
  ~~~bash  
   git clone https://github.com/Rrosso27/prueba-tecnica-smartSoluctions-app.git
  ~~~
  # Crear Base de datos    
  Antes de empezar crea una base de de datos, con el nombre de:
  ~~~bash  
    prueba_tecnica_smart_soluctions_app
  ~~~
  Este nombre es opcional, pero si decides cambiarlo, recuerda actualizarlo también en el archivo .nev.

  ## Crear el .env 
  Para crear el archivo .env, debes hacer una copia de [.env.example ](https://github.com/Rrosso27/prueba-tecnica-smartSoluctions-app/blob/main/.env.example) y renombrarla como .env

  ## Instalar las dependencias del composer    
  Para instalar las dependencias de Composer, debes tener  [Composer ](https://getcomposer.org/) previamente instalado en tu equipo y ejecuta este comando para instalar la dependencias  
  ~~~bash  
    composer install
  ~~~

  ## Ejecutar las migraciones   
  Las migraciones permiten gestionar nuestra base de datos de manera más eficiente, facilitando el control y el versionamiento de sus cambios. Para ejecutarlas, utiliza el siguiente comando:  
  ~~~bash  
      php artisan migrate
  ~~~

  ## Ejecutar los seeders 
  Los seeders permiten insertar datos por defecto en la base de datos. En este caso, se utilizan para generar una encuesta inicial que facilita la interacción con el sistema. Para ejecutar los seeders, utiliza el siguiente comando:
  ~~~bash  
      php artisan db:seed
  ~~~
  ## Ejecutar el servicio   🚀  
  Para ejecutar el servicio, asegúrate de que todos los requisitos estén cumplidos y utiliza el comando adecuado.
  ~~~bash  
      php artisan serve
  ~~~

---

# 🐳 Docker

El proyecto incluye configuración Docker lista para desarrollo local y despliegue en **Render**.

## Archivos Docker

| Archivo | Descripción |
|---|---|
| `Dockerfile` | Imagen de producción basada en PHP 8.2 + Apache |
| `.dockerignore` | Excluye archivos innecesarios del contexto de build |
| `start.sh` | Script de inicio: configura puerto, corre migraciones y arranca Apache |
| `render.yaml` | Blueprint de Render para despliegue automático |
| `docker-compose.yml` | Entorno local con MySQL para desarrollo |

## Ejecutar con Docker (local)

**1. Crear el archivo `.env`** a partir de `.env.example` y configurar un `APP_KEY`:
~~~bash
cp .env.example .env
php artisan key:generate --show
# copia el valor generado y agrégalo como APP_KEY en el .env
~~~

**2. Levantar los contenedores:**
~~~bash
docker compose up --build
~~~

La API quedará disponible en `http://localhost:8080`.

Para detener:
~~~bash
docker compose down
~~~

## Despliegue en Render 🚀

**1. Generar el `APP_KEY`:**
~~~bash
php artisan key:generate --show
~~~

**2. Subir el repositorio** a GitHub o GitLab.

**3. En Render:**
- Ve a **New → Blueprint** y conecta el repositorio.
- Render leerá el `render.yaml` y creará automáticamente:
  - Un **Web Service** con Docker.
  - Una **base de datos PostgreSQL** gratuita.

**4. Variables de entorno** — Establece manualmente en el dashboard de Render:

| Variable | Valor |
|---|---|
| `APP_KEY` | Valor generado en el paso 1 |
| `APP_URL` | URL asignada por Render (ej. `https://tu-app.onrender.com`) |

> Las variables de base de datos (`DB_HOST`, `DB_PORT`, etc.) se inyectan automáticamente desde el `render.yaml`.

**5. Hacer deploy.** El script `start.sh` correrá las migraciones automáticamente al iniciar el contenedor.

## Variables de entorno requeridas en producción

~~~env
APP_NAME=PifPoliAPI
APP_ENV=production
APP_DEBUG=false
APP_KEY=             # Generado con php artisan key:generate
APP_URL=             # URL de Render

DB_CONNECTION=pgsql
DB_HOST=             # Inyectado por Render
DB_PORT=             # Inyectado por Render
DB_DATABASE=         # Inyectado por Render
DB_USERNAME=         # Inyectado por Render
DB_PASSWORD=         # Inyectado por Render

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
~~~
