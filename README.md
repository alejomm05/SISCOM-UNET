<<<<<<< HEAD
# SISCOM-UNET
Sistema de Comunicación y Reporte para Seguridad Universitaria
=======# SISCOM-UNET
### Sistema de Comunicación y Reporte para Seguridad Universitaria

SISCOM-UNET es una plataforma de software diseñada para digitalizar, centralizar y optimizar la gestión operativa y la comunicación del cuerpo de seguridad de la Universidad Nacional Experimental del Táchira (UNET). El sistema sustituye los esquemas de radiocomunicación tradicionales por una arquitectura distribuida en tiempo real que mejora la documentación de incidencias y minimiza el tiempo de respuesta ante emergencias en el campus.

---

## Módulos del Sistema

El panel administrativo web implementa las siguientes capacidades operativas:

*   **Comunicación en Tiempo Real:** Chat bidireccional e interactivo con soporte para transmisiones de texto y adjuntos multimedia (imágenes, audio y video) procesados mediante sockets.
*   **Gestión y Reporte de Incidentes:** Formulario de registro de situaciones irregulares que incluye captura de geolocalización, evidencias digitales y auditoría de estados de resolución.
*   **Control de Tareas y Checklists:** Asignación, seguimiento y verificación de hojas de ruta diarias (apertura de accesos, monitoreo de infraestructura) destinadas al personal de guardia.
*   **Protocolos de Emergencia:** Recepción prioritaria de alertas críticas emitidas desde dispositivos móviles y coordinación de enlace operativo con organismos de rescate públicos (Protección Civil).
*   **Administración de Personal:** Control de perfiles de usuario, roles, activación/desactivación de cuentas y asignación estructurada de turnos de vigilancia.

---

## Arquitectura y Stack Tecnológico

El proyecto ha sido estructurado bajo altos estándares de desacoplamiento, escalabilidad y seguridad:

*   **Backend:** PHP 8.2+ y Laravel 12.x bajo el patrón arquitectónico Modelo-Vista-Controlador (MVC).
*   **Seguridad y Control de Acceso:** Autenticación basada en API mediante Laravel Sanctum. Implementación de Políticas de Autorización (Laravel Policies) acopladas a controladores y rutas mediante middlewares específicos para restringir privilegios críticos a nivel de modelo.
*   **Base de Datos:** MySQL con gestión de colas de procesos (Queue Jobs) para la ejecución asíncrona de tareas pesadas.
*   **Tiempo Real:** Laravel Reverb como servidor de WebSockets nativo.
*   **Frontend Web:** JavaScript moderno, Bootstrap y compilación optimizada a través de Vite.

---

## Requisitos de Sistema

*   PHP >= 8.2
*   Composer >= 2.x
*   Node.js >= 18.x
*   NPM >= 9.x
*   Motor de base de datos MySQL o MariaDB

---

## Instrucciones de Instalación y Configuración

Siga las siguientes instrucciones para desplegar un entorno de desarrollo local:

### 1. Clonar el repositorio y configurar dependencias de PHP
```bash
git clone [https://github.com/alejomm05/SISCOM-UNET.git](https://github.com/alejomm05/SISCOM-UNET.git)
cd SISCOM-UNET
composer install

>>>>>>> Douglas_Aliendres 
