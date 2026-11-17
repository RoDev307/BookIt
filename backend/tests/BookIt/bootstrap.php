<?php
// Este conjunto no admite ninguna base persistente ni configuración cacheada.
$root = dirname(__DIR__, 2);
if (getenv('APP_ENV') !== 'testing' || getenv('DB_CONNECTION') !== 'sqlite'
    || getenv('DB_DATABASE') !== ':memory:' || getenv('DB_URL') !== '') {
    throw new RuntimeException('Ejecuta con phpunit.bookit.xml: se requiere SQLite en memoria.');
}
$cache = getenv('APP_CONFIG_CACHE') ?: $root.'/bootstrap/cache/config.php';
if (is_file($cache)) {
    throw new RuntimeException('Existe configuración cacheada. Usa una copia sin ese archivo para estas pruebas.');
}
require $root.'/vendor/autoload.php';
