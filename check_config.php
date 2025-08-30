<?php
echo "<h2>Verificación de Configuración PHP</h2>";

// Verificar archivo php.ini cargado
$loaded_ini = php_ini_loaded_file();
if ($loaded_ini) {
    echo "✅ php.ini cargado: " . $loaded_ini . "<br>";
} else {
    echo "❌ NO se está cargando ningún php.ini<br>";
}

// Verificar extensiones SQLite
echo "PDO_SQLite: " . (extension_loaded('pdo_sqlite') ? '✅ INSTALADO' : '❌ NO INSTALADO') . "<br>";
echo "SQLite3: " . (extension_loaded('sqlite3') ? '✅ INSTALADO' : '❌ NO INSTALADO') . "<br>";

// Verificar versión de PHP
echo "Versión PHP: " . phpversion() . "<br>";

// Mostrar todas las extensiones cargadas
echo "<h3>Extensiones cargadas:</h3>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<ul>";
foreach ($extensions as $ext) {
    echo "<li>" . $ext . "</li>";
}
echo "</ul>";
?>