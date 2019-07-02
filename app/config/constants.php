<?php
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
define('SITE_LIVE', false);
define('HUNTERS_TEST', true);
/*************************************************************************
* Database Configuration
**************************************************************************/
define('DB_HOST', "localhost");
define('DB_NAME', "cobaltma_newclient_portal_dev");
define('DB_USER', "cobaltma_cpsite");
define('DB_PASS', "{,e3^bfcfcMp");
define('DB_CHARSET', "utf8");
?>
