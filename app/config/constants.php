<?php
/*--------------------------------------------------------------------------
 Define Application Configuration Constants
--------------------------------------------------------------------------*/
    define('BASE_DIR', str_replace("\\", "/", dirname(dirname(__DIR__))));
    define('APP',  BASE_DIR . "/app/");
    define('DOC_ROOT', BASE_DIR . "/public_html/");
    define('IMAGES',   DOC_ROOT . "/images/");
    define('STYLES',   DOC_ROOT . "/styles/");
    define('UPLOADS',  DOC_ROOT. "/client_uploads/");
    /*********************************************************************
    * Cookie Key
    **********************************************************************/
    define('COOKIE_SECRET_KEY', "^SVa_j|yPN+1ZbUj|zlv=M1iJT5lto");
    /*********************************************************************
    * Encrption Keys
    **********************************************************************/
    define('ENCRYPTION_KEY', "f@!$251��cef08%&3��a0e");
    define('HMAC_SALT', "0%8Qfd9K4m6d$8a8C7n7^Ed6Dab");
    define('HASH_KEY', "9Mp7Lf2cHz5F");

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
