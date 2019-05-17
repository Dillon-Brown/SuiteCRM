<?php

use Robo\Runner;
use Symfony\Component\Console\Output\ConsoleOutput;

const APP_NAME = 'SuiteCRM';
const APP_VERSION = '0.0.1';

$pharPath = \Phar::running(true);
if ($pharPath) {
    $autoloaderPath = "$pharPath/vendor/autoload.php";
} elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $autoloaderPath = __DIR__ . '/vendor/autoload.php';
} else {
    die("Could not find autoloader. Run 'composer install'.");
}
$classLoader = require_once $autoloaderPath;

$commandClasses = [
    \SuiteCRM\Robo\RoboFile::class,
    \SuiteCRM\Robo\Plugin\Commands\BuildCommands::class,
    \SuiteCRM\Robo\Plugin\Commands\CodeCoverageCommands::class,
    \SuiteCRM\Robo\Plugin\Commands\TestEnvironmentCommands::class,
    \SuiteCRM\Robo\Traits\RoboTrait::class
];

$statusCode = (new Runner($commandClasses))
    ->setClassLoader($classLoader)
    ->execute(
        $argv,
        APP_NAME,
        APP_VERSION,
        new ConsoleOutput()
    );
exit($statusCode);
