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
    define('HOME_PAGE_LINK', "https://dev.fsg.com.au/");
    
    /*********************************************************************
    * Encrption Keys
    **********************************************************************/
    define('ENCRYPTION_KEY', "f@!$251Êìcef08%&3¥‹a0e");
    define('HMAC_SALT', "0%8Qfd9K4m6d$8a8C7n7^Ed6Dab");
    define('HASH_KEY', "9Mp7Lf2cHz5F");

/*************************************************************************
* Is Site Live?
**************************************************************************/
define('SITE_LIVE', false);
/*************************************************************************
* Under Maintenance?
**************************************************************************/
define('MAINTENANCE', true);
/*************************************************************************
* Some useful constants
**************************************************************************/
    //Direct Freight Fuel Surcharge
    define('DF_FUEL_SURCHARGE', 1.133);
/*************************************************************************
* Database Configuration
**************************************************************************/
define('DB_HOST', "localhost");
define('DB_NAME', "fsg_wms_dev");
define('DB_USER', "dev_website");
define('DB_PASS', "Cra233%d");
define('DB_CHARSET', "utf8");
?>
