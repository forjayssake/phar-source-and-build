<?php

$input = $argv[1] ?? __DIR__ . '/app.phar';
$output = __DIR__ . '/source';

if (!file_exists($input)) {
    die("Input PHAR not found: $input\n");
}

// The working directories are not tracked in git, so create them on first run.
foreach (array($output, __DIR__ . '/build') as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

try {
    $phar = new Phar($input);

    // Save the original executable stub.
    file_put_contents(
        __DIR__ . '/stub.php',
        $phar->getStub()
    );

    // Extract the archive contents.
    $phar->extractTo($output, null, true);

    echo "Extraction complete.\n";
    echo "Original stub saved to stub.php\n";
    echo "Source files extracted to source/\n";

} catch (Throwable $e) {
    die("Extraction failed: " . $e->getMessage() . "\n");
}
