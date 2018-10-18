<?php
/* Only deliver over https */
if(!((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443))
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
/* Load dependencies */
require  '../vendor/autoload.php';

/* load the contants */
require '../app/config/constants.php';
/*
--------------------------------------------------------------------------
 Register Error & Exception handlers
--------------------------------------------------------------------------
*/

Handler::register();

/*
--------------------------------------------------------------------------
 Start Session and Form Objects
--------------------------------------------------------------------------
*/
Session::init();
Form::init();
/*
--------------------------------------------------------------------------
 Create The Application
--------------------------------------------------------------------------
*/

$app = new App();

// base URL link
define('PUBLIC_ROOT', $app->request->root());

/*
--------------------------------------------------------------------------
 Run The Application
--------------------------------------------------------------------------
*/

$app->run();