ite<?php
/*
--------------------------------------------------------------------------
 Define Application Configuration Constants
--------------------------------------------------------------------------
*/

define('BASE_DIR', str_replace("\\", "/", dirname(dirname(__DIR__))));
define('APP',  BASE_DIR . "/app/");
define('DOC_ROOT', BASE_DIR . "/public_html/");
define('IMAGES',   DOC_ROOT . "/images/");
define('STYLES',   DOC_ROOT . "/styles/");
define('UPLOADS',  DOC_ROOT. "/client_uploads/");

/*************************************************************************
* Is Site Live?
**************************************************************************/
define('SITE_LIVE', true);
define('HUNTERS_TEST', false);
/*************************************************************************
* Database Configuration
**************************************************************************/
define('DB_HOST', "localhost");
define('DB_NAME', "fsg_wms");
define('DB_USER', "website");
define('DB_PASS', "66ihu#9J");
define('DB_CHARSET', "utf8");
?>
