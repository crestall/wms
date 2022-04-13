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
    * Encryption Keys
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
define('MAINTENANCE', false);
/*************************************************************************
* Some useful constants
**************************************************************************/
    //Direct Freight Fuel Surcharge
    define('DF_FUEL_SURCHARGE', 1.17);
/*************************************************************************
* Client Charge Defaults
**************************************************************************/
    //Delivery
    define('STANDARD_TRUCK', '35.00');
    define('URGENT_TRUCK', '45.00');
    define('STANDARD_UTE', '20.00');
    define('URGENT_UTE', '25.00');
    //Storage
    define('STANDARD_BAY', '4.00');
    define('OVERSIZE_BAY', '6.00');
    define('PICK_FACE', '2.50');
    //Goods In/Out
    define('PALLET_IN','4.00');
    define('PALLET_OUT', '4.00');
    define('CARTON_IN','0.50');
    define('CARTON_OUT', '0.50');
    //Container Unloading
    define('LOOSE_40GP', '400.00');
    define('LOOSE_20GP', '210.00');
    define('PALLETISED_40GP', '220.00');
    define('PALLETISED_20GP', '160.00');
    define('MAX_LOOSE_40GP', 1250);
    define('MAX_LOOSE_20GP', 800);
    define('ADDITIONAL_LOOSE', '0.50');
    //Miscellaneous
    define('REPALLETISING', '5.00');
    define('SHRINKWRAP', '5.00');
    define('MANUAL_ORDER_ENTRY', '4.95');
    define('MONTHLY_FEE', '100.00');
/*************************************************************************
* Database Configuration
**************************************************************************/
define('DB_HOST', "localhost");
define('DB_NAME', "fsg_wms_dev");
define('DB_USER', "website");
define('DB_PASS', "66ihu#9J");
define('DB_CHARSET', "utf8");
?>
