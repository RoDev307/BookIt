# BookIt integrado para administración del negocio

## Procedencia
- Base descargada de RoDev307/BookIt, rama develop, commit d3e93ec40bfb52c9dbd9c9b24467a5306a0cf33c.
- Vue se conserva exactamente como está en ese commit.
- Ionic procede del archivo Ionic_corregido.zip recibido del equipo, con sus ajustes de HTTP, sesión y navegación.
- Laravel incorpora el soporte de fichas de clientes, historial, citas asistidas, personal y notificaciones preparado anteriormente.

## Cambios solicitados
Se retiraron de Ionic el registro público, la ruta y las pantallas del portal del cliente final y la sección Cuenta de acceso con generación de códigos de vinculación. Las cuentas de rol client no pueden abrir el área administrativa de Ionic; si se intenta iniciar sesión con ese rol se informa el motivo y se solicita revocar el token recién emitido.

Se mantienen Clientes (crear, buscar, editar, historial), Agendar cita, Gestionar citas, Calendario operativo, Servicios, Personal, Horarios, Reportes, Notificaciones y Cuenta del administrador. La sesión conserva la validación compartida y el tiempo de caché del compañero.

No se publican los endpoints adicionales client-links ni tenant/clients/{id}/link-code. Se conserva la API pública original de Laravel, incluida auth/register y sus rutas de citas, por compatibilidad con la aplicación original. Retirar las pantallas de Ionic no equivale a eliminar esas funciones históricas del backend. El rol client y los campos opcionales existentes no se eliminaron del esquema.

## Configuración y ejecución
Este ZIP contiene código fuente. No contiene credenciales, .env, respaldos, vendor ni node_modules.

1. Descomprimir en una carpeta nueva.
2. En backend ejecutar composer install. Configurar backend/.env con el archivo que ya utiliza el equipo. Para una instalación local nueva se puede copiar .env.example a .env, completar sus valores y generar su APP_KEY. El ejemplo no contiene la conexión real.
3. Antes de aplicar migraciones revisar php artisan migrate:status en el entorno elegido. Esta integración no ejecutó migraciones en la base compartida ni eliminó columnas. Las migraciones nuevas deben revisarse contra las ya aplicadas por el equipo; no restablecer la base existente.
4. Ejecutar php artisan serve --host=127.0.0.1 --port=8000 desde backend.
5. En frontend/Ionic instalar dependencias con npm ci y ejecutar npm start -- --port=8100. La URL de la API se define en src/environments/environment.ts y environment.prod.ts y conserva http://localhost:8000/api/v1 del compañero. Ajustar ambas para otro servidor o un teléfono físico.
6. En frontend/Vue ejecutar pnpm install --frozen-lockfile y pnpm dev. Vue conserva el estado funcional de develop; su README documenta la integración de API pendiente de esa interfaz. Esta entrega no rehízo ni conectó las vistas Vue.

## Comprobaciones de esta integración
- Vue: tipos y compilación de producción correctos con las dependencias del archivo de bloqueo.
- Ionic: compilación de producción y tipos de pruebas E2E correctos.
- Ionic: 34 pruebas unitarias correctas.
- Backend: 20 pruebas y 89 aserciones correctas con SQLite exclusivamente en memoria.
- Navegador móvil: 4 escenarios correctos con respuestas de API controladas: ausencia del portal, bloqueo de sesión client, rechazo y revocación de token client y ficha administrativa con historial sin vinculación.
- Las pruebas no utilizaron la base de datos remota. No acreditan por sí solas todos los flujos contra el servidor desplegado.

Para repetir el backend, preparar el .env local y ejecutar:
php vendor/bin/pest --configuration phpunit.bookit.xml

Para repetir Ionic:
npm run build
npm run test:unit

Para repetir los 4 escenarios de navegador, iniciar Ionic en 8114, instalar Google Chrome y ejecutar:
npx playwright test --config playwright.admin.config.ts

Se incluyeron las migraciones históricas de clientes y códigos para mantener la secuencia del esquema revisado. Los códigos quedan sin controlador ni rutas activas; no es necesario borrar columnas para retirar la interfaz.

## Publicación
El proyecto se preparó en una copia independiente. No se ha desplegado, hecho push ni creado un pull request. El ZIP se puede revisar y después incorporar a la rama de integración que acuerde el equipo.
