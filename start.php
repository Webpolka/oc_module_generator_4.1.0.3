<?php

if ($argc < 2) {
    die("Usage: php ./start.php [name]\n");
}

// --------------------------
// Обработка имени
// --------------------------
$nameRaw = $argv[1];
$name = str_replace('-', '_', $nameRaw);
$moduleName = str_replace(['_', '-'], ' ', $name);
$moduleName = str_replace(' ', '', ucwords($moduleName));

echo "name: $name\n";
echo "ModuleName: $moduleName\n";

// --------------------------
// Папка шаблона и целевая папка
// --------------------------
$templateDir = __DIR__ . '/source';
$rootDir = __DIR__ . '/' . $name;       // folder/
$innerDir = $rootDir . '/' . $name;     // folder/folder/

if (!is_dir($innerDir)) mkdir($innerDir, 0777, true);

// --------------------------
// Копируем файлы и заменяем плейсхолдеры
// --------------------------
recurseCopy($templateDir, $innerDir);
replacePlaceholders($innerDir, $name, $moduleName);

echo "Module starter created successfully at: $innerDir\n";

// --------------------------
// Создаём ZIP рядом с rootDir
// --------------------------
$zipFile = $rootDir . '/' . $name . '.ocmod.zip';
zipFolder($innerDir, $zipFile);

echo "ZIP archive created successfully at: $zipFile\n";

// --------------------------
// Функции
// --------------------------

function recurseCopy($src, $dst) {
    $dir = opendir($src);
    if (!is_dir($dst)) mkdir($dst, 0777, true);

    while (false !== ($file = readdir($dir))) {
        if ($file == '.' || $file == '..') continue;

        $srcPath = $src . '/' . $file;
        $dstPath = $dst . '/' . str_replace('%name%', $GLOBALS['name'], $file);

        if (is_dir($srcPath)) {
            recurseCopy($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
    closedir($dir);
}

function replacePlaceholders($dir, $name, $moduleName) {
    $fields = ['%name%', '%ModuleName%'];
    $values = [$name, $moduleName];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;

        $basename = strtolower($file->getBasename());

        // README.md и LICENSE копируем без замены
        if (in_array($basename, ['readme.md', 'license'])) continue;

        $original = $file->getPathname();
        $dest = str_replace($fields, $values, $original);

        $content = file_get_contents($original);
        $content = str_replace($fields, $values, $content);
        file_put_contents($dest, $content);

        if ($original !== $dest) unlink($original);
    }
}

function zipFolder($srcDir, $zipFile) {
    $srcDir = realpath($srcDir);
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        die("Cannot create ZIP file: $zipFile\n");
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isFile()) continue;

        $filePath = $file->getRealPath();
        //  Кладём файлы в ZIP **без внутренней папки**, сразу в корень
        $relativePath = substr($filePath, strlen($srcDir) + 1);
        $zip->addFile($filePath, $relativePath);
    }

    $zip->close();
}
