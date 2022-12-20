<?php
/**
 * Sample index file for running the frontend. This is a simple file which creates
 * a new UNL_UCBCN_Frontend object and handles sending the output to the user.
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN_Frontend
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
// namespace UNL\UCBCN\Frontend;

if (file_exists(dirname(__FILE__).'/../config.inc.php')) {
    require_once dirname(__FILE__).'/../config.inc.php';
} else {
    require dirname(__FILE__).'/../config.sample.php';
}

$routes = include __DIR__ . '/../data/routes.php';
$router = new RegExpRouter\Router(array('baseURL' => UNL\UCBCN\Frontend\Controller::$url));
$router->setRoutes($routes);
if (isset($_GET['model'])) {
    // Prevent injecting a specific model through the web interface
    unset($_GET['model']);
}

try {
    // Initialize Controller, and construct everything the user requested
    $controller = new UNL\UCBCN\Frontend\Controller($router->route($_SERVER['REQUEST_URI'], $_GET));

    // Now render what the user has requested
    $outputcontroller = new UNL\UCBCN\Frontend\OutputController($controller);
    $outputcontroller->addGlobal('controller', $controller);
    $outputcontroller->setTemplatePath(dirname(__FILE__).'/templates/html');

    if (isset($siteNotice)) {
        $outputcontroller->addGlobal('siteNotice', $siteNotice);
    }

    echo $outputcontroller->render($frontend);
}  catch (UNL\UCBCN\Frontend\Exception $e) {

    header('HTTP/1.1 ' . $e->getCode() .' ' . $e->getMessage());
    header('Status: ' . $e->getCode() .' ' . $e->getMessage());
    die();
}
