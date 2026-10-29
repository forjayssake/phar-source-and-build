<?php

$output = $argv[1] ?? __DIR__ . '/build/app.phar';
$source = __DIR__ . '/source';
$stub = __DIR__ . '/stub.php';

if (ini_get('phar.readonly')) {
    die("phar.readonly is on; run with: php -d phar.readonly=0 build.php\n");
}

// The working directories are not tracked in git, so create them on first run.
foreach (array($source, __DIR__ . '/build') as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

if (!(new FilesystemIterator($source))->valid() || !file_exists($stub)) {
    die("source/ is empty or stub.php is missing; run extract.php first.\n");
}

try {
    if (!is_dir(dirname($output))) {
        mkdir(dirname($output), 0777, true);
    }
    if (file_exists($output)) {
        unlink($output);
    }

    // Match the original archive: alias is the file name, uncompressed, SHA-256 signed.
    $alias = basename($output);
    $phar = new Phar($output, 0, $alias);
    $phar->startBuffering();
    $phar->buildFromDirectory($source);
    $phar->setStub(file_get_contents($stub));
    $phar->setSignatureAlgorithm(Phar::SHA256);
    $phar->stopBuffering();

    echo "Build complete.\n";
    echo "Archive written to $output (" . count($phar) . " files)\n";

} catch (Throwable $e) {
    die("Build failed: " . $e->getMessage() . "\n");
}
