<?php
$dir = __DIR__ . '/config';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$file = $dir . '/database.php';
$content = "<?php
return [
    'db_host' => '127.0.0.1',
    'db_name' => 'u427445037_coresuite',
    'db_user' => 'u427445037_coresuite',
    'db_pass' => 'Giogiu2123@'
];
";
file_put_contents($file, $content);
echo 'File config/database.php creato!';
?>