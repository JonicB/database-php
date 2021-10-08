<?php

require_once '../safeplace/settings.inc.php';
require_once  MODULES_DIR.'autoloader.php';
require_once '../vendor/autoload.php';

use MyApp\Logger\FileLoggerBuf;
use MyApp\EventHandler;

try{
    $loader = new \Twig\Loader\FilesystemLoader('./templates');
    $twig = new \Twig\Environment($loader, $options);

    $logger = new FileLoggerBuf('tmp.log');
    $handler = new EventHandler($dbSettings,$logger);

    $page = $handler->run();
    echo $twig->render($page['template'],$page['info']);
}
catch(Exception $e){
    if (DEBUG){
        echo "Exception";
        print_r($e);
    }
}