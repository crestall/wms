<?php
/*
--------------------------------------------------------------------------
 Define Application Configuration Constants
--------------------------------------------------------------------------

PUBLIC_ROOT: 	the root URL for the application (see below).
BASE_DIR: 	path to the directory that has all of your "app", "public_html", "vendor", ... directories.
IMAGES:		path to upload images, don't use it for displaying images, use PUBLIC_ROOT . "/images/" instead.
APP:			path to app directory.
*/

define('BASE_DIR', str_replace("\\", "/", dirname(dirname(__DIR__))));
define('APP',  BASE_DIR . "/app/");
define('DOC_ROOT', BASE_DIR . "/public_html/");
define('IMAGES',   DOC_ROOT . "/images/");
define('STYLES',   DOC_ROOT . "/styles/");
define('UPLOADS',  DOC_ROOT. "/client_uploads/");
?>