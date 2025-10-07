# 🔗 URL Shortener API

Una API REST moderna y eficiente para acortar URLs, construida con Laravel 8 y documentada con Swagger/OpenAPI.

[![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos Previos](#-requisitos-previos)
- [Instalación Local](#-instalación-local)
- [Despliegue en la Nube](#-despliegue-en-la-nube)
- [Documentación de la API](#-documentación-de-la-api)
- [Pruebas](#-pruebas)
- [Solución de Problemas](#-solución-de-problemas)
- [Licencia](#-licencia)

## ✨ Características

- 🚀 **API RESTful** - Endpoints claros y bien estructurados
- 📝 **Documentación Swagger** - Documentación interactiva de la API
- 🔐 **Validación de URLs** - Validación completa de URLs originales
- 🎲 **Códigos Únicos** - Generación automática de códigos cortos únicos
- 📊 **Estadísticas** - Contador de clics para cada URL acortada
- 🗄️ **Base de Datos MySQL** - Almacenamiento persistente y confiable
- ✅ **Testing** - Suite completa de tests unitarios y de integración

## 🔧 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **PHP** >= 7.3 (recomendado: PHP 8.2)
- **Composer** >= 2.0
- **MySQL** >= 5.7 o **MariaDB** >= 10.2
- **Node.js** >= 12.x y **NPM** >= 6.x (opcional, para assets)
- **Git** (para clonar el repositorio)

### Verificar instalaciones:

```bash
php --version
composer --version
mysql --version
node --version
npm --version
```

## 🚀 Instalación Local

### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/pnvdev/url-shortener-api.git
cd url-shortener-api
```

### Paso 2: Instalar Dependencias

```bash
# Instalar dependencias de PHP
composer install

# (Opcional) Instalar dependencias de Node.js
npm install
```

### Paso 3: Configurar Variables de Entorno

```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### Paso 4: Configurar Base de Datos

Edita el archivo `.env` con tu configuración de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

Crea la base de datos:

```bash
# En MySQL
mysql -u root -p
CREATE DATABASE url_shortener CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Paso 5: Ejecutar Migraciones

```bash
php artisan migrate
```

### Paso 6: Generar Documentación de Swagger

```bash
php artisan l5-swagger:generate
```

### Paso 7: Iniciar el Servidor de Desarrollo

```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

La documentación Swagger estará en: `http://localhost:8000/api/documentation`

### 🧪 Ejecutar Tests (Opcional)

```bash
# Ejecutar todos los tests
php artisan test

# O con PHPUnit
./vendor/bin/phpunit
```

## ☁️ Despliegue en la Nube

### Opción 1: Despliegue en AWS EC2

#### Paso 1: Crear una Instancia EC2

1. Accede a [AWS Console](https://console.aws.amazon.com)
2. Ve a **EC2** > **Launch Instance**
3. Configura tu instancia:
   - **AMI**: Ubuntu Server 22.04 LTS
   - **Instance Type**: t2.micro (Free tier) o t2.small
   - **Key pair**: Crea una nueva o selecciona una existente
   - **Security Group**: Permite SSH (22), HTTP (80), HTTPS (443)

#### Paso 2: Conectarse al Servidor

```bash
chmod 400 tu-key.pem
ssh -i "tu-key.pem" ubuntu@tu-ip-ec2
```

#### Paso 3: Actualizar el Sistema

```bash
sudo apt update && sudo apt upgrade -y
```

#### Paso 4: Instalar LEMP Stack (Linux, Nginx, MySQL, PHP)

```bash
# Instalar Nginx
sudo apt install nginx -y

# Instalar MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Instalar PHP y extensiones necesarias
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-json php8.2-tokenizer -y

# Verificar instalación de PHP
php -v
```

#### Paso 5: Instalar Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer --version
```

#### Paso 6: Configurar MySQL

```bash
# Acceder a MySQL
sudo mysql

# Crear base de datos y usuario
CREATE DATABASE url_shortener CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'url_shortener_user'@'localhost' IDENTIFIED BY 'contraseña_segura';
GRANT ALL PRIVILEGES ON url_shortener.* TO 'url_shortener_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Paso 7: Clonar el Repositorio

```bash
# Instalar Git
sudo apt install git -y

# Crear directorio para aplicaciones web
sudo mkdir -p /var/www
cd /var/www

# Clonar el repositorio
sudo git clone https://github.com/pnvdev/url-shortener-api.git
cd url-shortener-api

# Establecer permisos
sudo chown -R www-data:www-data /var/www/url-shortener-api
sudo chmod -R 755 /var/www/url-shortener-api
sudo chmod -R 775 /var/www/url-shortener-api/storage
sudo chmod -R 775 /var/www/url-shortener-api/bootstrap/cache
```

#### Paso 8: Instalar Dependencias

```bash
cd /var/www/url-shortener-api
sudo composer install --optimize-autoloader --no-dev
```

#### Paso 9: Configurar Variables de Entorno

```bash
# Copiar archivo de configuración
sudo cp .env.example .env

# Editar el archivo .env
sudo nano .env
```

Configurar las siguientes variables:

```env
APP_NAME="URL Shortener API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://tu-ip-ec2

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=url_shortener_user
DB_PASSWORD=contraseña_segura

L5_SWAGGER_CONST_HOST=http://tu-ip-ec2
```

```bash
# Generar clave de aplicación
sudo php artisan key:generate

# Ejecutar migraciones
sudo php artisan migrate --force

# Generar documentación Swagger
sudo php artisan l5-swagger:generate

# Optimizar aplicación
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

#### Paso 10: Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/url-shortener
```

Agregar la siguiente configuración:

```nginx
server {
    listen 80;
    server_name tu-ip-ec2;
    root /var/www/url-shortener-api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Habilitar el sitio
sudo ln -s /etc/nginx/sites-available/url-shortener /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

# Verificar configuración de Nginx
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

## 📚 Documentación de la API

### Endpoints Disponibles

#### 1. Crear URL Corta

```http
POST /api/short-urls
Content-Type: application/json

{
  "original_url": "https://www.ejemplo.com/una/url/muy/larga"
}
```

**Respuesta exitosa (201):**

```json
{
  "id": 1,
  "original_url": "https://www.ejemplo.com/una/url/muy/larga",
  "short_code": "abc123",
  "short_url": "http://tu-ip-ec2/s/abc123",
  "clicks": 0,
  "created_at": "2025-10-06T10:30:00.000000Z"
}
```

#### 2. Redirigir a URL Original

```http
GET /s/{shortCode}
```

Redirige automáticamente a la URL original e incrementa el contador de clics.

### Documentación Interactiva

Accede a la documentación completa de Swagger en:

```
http://tu-ip-ec2/api/documentation
```

## 🧪 Pruebas

### Ejecutar Tests Localmente

```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=UrlTest

# Con cobertura de código
php artisan test --coverage
```

### Ejecutar Tests en Producción

```bash
# En tu servidor
cd /var/www/url-shortener-api
php artisan test --env=testing
```

## 🔍 Solución de Problemas

### Error: "Class 'Composer\InstalledVersions' not found"

```bash
composer dump-autoload
```

### Error: Permisos de Storage

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Error: "No application encryption key has been specified"

```bash
php artisan key:generate
```

### Error: Swagger no se genera

```bash
php artisan config:clear
php artisan cache:clear
php artisan l5-swagger:generate
```

### Error de Conexión a Base de Datos

1. Verifica las credenciales en `.env`
2. Asegúrate de que MySQL está ejecutándose
3. Verifica que el firewall permita conexiones

```bash
# Verificar MySQL
sudo systemctl status mysql

# Reiniciar MySQL
sudo systemctl restart mysql
```

### Error 502 Bad Gateway (Nginx)

```bash
# Verificar PHP-FPM
sudo systemctl status php8.2-fpm

# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm
```

## 🔐 Seguridad

### Recomendaciones de Producción

1. **Nunca** dejes `APP_DEBUG=true` en producción
2. Usa contraseñas seguras para la base de datos
3. Configura HTTPS con certificados SSL
4. Mantén PHP y Laravel actualizados
5. Configura rate limiting para prevenir abuso
6. Realiza backups regulares de la base de datos

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 📧 Contacto

- **GitHub**: [@pnvdev](https://github.com/pnvdev)
- **Proyecto**: [url-shortener-api](https://github.com/pnvdev/url-shortener-api)

---

⭐ Si este proyecto te fue útil, considera darle una estrella en GitHub!
