<?php
 /*
 *  This file is part of the phpCfdi package.
 *  
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *  
 *  (c) 2020 phpCfdi
 *  
 */

use PhpCfdi\Efos\Runner;

(static function () : void {
    $autoloadFiles = [
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/../../../autoload.php',
    ];

    $autoloaderFound = false;
    foreach ($autoloadFiles as $autoloadFile) {
        if (! file_exists($autoloadFile)) {
            continue;
        }

        require_once $autoloadFile;
        $autoloaderFound = true;
    }

    if (! $autoloaderFound) {
        fwrite(STDERR, 'vendor/autoload.php could not be found. Did you run `composer install`?' . PHP_EOL);
        exit(1);
    }

    exit((Runner::getApplication())->run());
})();

