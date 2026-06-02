<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Vercel PHP Diagnostic Test</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

echo "<h3>Checking Extension Status</h3>";
$extensions = ['pdo_mysql', 'openssl', 'mbstring', 'xml', 'curl', 'ctype', 'json'];
echo "<ul>";
foreach ($extensions as $ext) {
    echo "<li>Extension <strong>$ext</strong>: " . (extension_loaded($ext) ? "✅ Loaded" : "❌ NOT Loaded") . "</li>";
}
echo "</ul>";

echo "<h3>Checking Environment Variables</h3>";
echo "<ul>";
echo "<li>APP_KEY: " . (getenv('APP_KEY') ? "✅ Set" : "❌ NOT Set") . "</li>";
echo "<li>DB_HOST: " . (getenv('DB_HOST') ? "✅ Set (" . getenv('DB_HOST') . ")" : "❌ NOT Set") . "</li>";
echo "<li>DB_DATABASE: " . (getenv('DB_DATABASE') ? "✅ Set" : "❌ NOT Set") . "</li>";
echo "<li>VERCEL: " . (getenv('VERCEL') ? "✅ Set" : "❌ NOT Set") . "</li>";
echo "</ul>";

echo "<h3>Checking Filesystem</h3>";
echo "<ul>";
echo "<li>Autoload file exists: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? "✅ Yes" : "❌ No (Composer build failed)") . "</li>";
echo "<li>Bootstrap cache directory writable: " . (is_writable('/tmp/storage/bootstrap/cache') ? "✅ Yes" : "❌ No") . "</li>";
echo "</ul>";
