
# Sistema de Gestión de Papelería

Description

El Sistema de Gestión de Papelería es una aplicación desarrollada en PHP, utilizando MySQL como gestor de base de datos.
Permite administrar los módulos principales de un negocio de papelería:

Gestión de productos

Gestión de proveedores

Gestión de ventas

Facturación

Gestión de usuarios

Además, aplica principios de SOLID, técnicas de tolerancia a fallos, manejo de sesiones, modularidad y conexión estructurada a base de datos.



## tablas de contenido
Descripción

Estructura

Requisitos

Base de datos

Usuario Admin

Conexión

Ejecución

Tecnologías

Integrantes

Notas para docente
## Requerimientos
XAMPP (Apache + MySQL)

PHP 8.x o superior

MySQL Workbench (opcional)

Navegador web moderno
## Installation

Instalar XAMPP

Activar Apache y MySQL

Abrir MySQL Workbench

Crear conexión a localhost

Ejecutar el archivo database/papeleria.sql
## Database

Database

El archivo SQL contiene:

✔ Creación de la base de datos
✔ Tablas del sistema
✔ Insert del administrador
## Admin User

Usuario inicial:

Usuario: admin

Contraseña: admin123

Rol: administrador

La contraseña se almacena usando password_hash().
## Configuración

Archivo de conexión: config/database.php

Ejemplo:

$host = "localhost";
$user = "root";
$password = "";
$dbname = "papeleria";

$conn = new mysqli($host, $user, $password, $dbname);
## Usage
Colocar el proyecto en:

C:\xampp\htdocs\papeleria\


Abrir en el navegador:

http://localhost/papeleria/public/login.php


Iniciar sesión para acceder al panel principal.
## Tech Stack

PHP (lógica)

MySQL (DB)

HTML/CSS/JS (frontend)

SOLID

Manejo de sesiones

Tolerancia a fallos (ventas/facturas)

try/catch

validaciones

transacciones
## Features
Login seguro

CRUD de productos

CRUD de proveedores

Ventas con validación y consistencia

Facturación

Gestión de usuarios

Sesiones

Modularidad
## Contributors
Integrante	Responsabilidad

1 YAGUAL MONTECE ARIEL ALEJANDRO	Carpetas, BD, conexión, login, README, integración

2 SANCHEZ GONZALEZ ARIEL MATTHEW	Módulo productos

3 VERA CARDOZO JOSE MARIO	Módulo proveedores

4 ROBELLY PINCAY TOMAS JEFFERSON	Ventas + tolerancia a fallos

5 SALCEDO VILLON MARIA DOLORES	Facturación + pruebas + documentación final
## Notas

Ejecutable con XAMPP

La BD está en /database/papeleria.sql

El login usa credenciales del README

Todos los módulos se acceden desde el panel principal
## Estatus

Versión: 1.0

Estado: Funcional – Apta para demostración académica