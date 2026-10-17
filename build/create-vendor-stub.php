<?php

declare(strict_types=1);


// Pharynx 0.3.x (the latest release) resolves transitive Composer
// dependencies by their package name and does not understand Composer's
// `replace` mechanism. Since betterlite/pocketmine-mp replaces
// pocketmine/pocketmine-mp, Pharynx looks for a package that Composer never
// installs. Materialise a metadata-only stub at the expected location so the
// phar build can continue (the stub contains no code and is never shipped).
$stub_dir = dirname(__DIR__) . "/vendor/pocketmine/pocketmine-mp";
if(file_exists($stub_dir . "/composer.json")) {
    exit(0);
}

if(!is_dir($stub_dir) && !@mkdir($stub_dir, 0777, true) && !is_dir($stub_dir)) {
    fwrite(STDERR, "Failed to create " . $stub_dir . PHP_EOL);
    exit(1);
}

file_put_contents($stub_dir . "/composer.json", json_encode([
    "name" => "pocketmine/pocketmine-mp",
    "description" => "Stub: this package is replaced by betterlite/pocketmine-mp.",
    "version" => "5.50.0"
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
