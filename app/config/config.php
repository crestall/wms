<?php

 /**
  * This file contains configuration for the application.
  * It will be used by app/core/Config.php
  *
  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

return array(
    /**
     * Configuration for: Paths
     * Paths from App directory
     */
    "VIEWS_PATH"            => APP . "views/",
    "ERRORS_PATH"           => APP . "views/errors/",
    "LOGIN_PATH"            => APP . "views/login/",
    "ADMIN_VIEWS_PATH"      => APP . "views/admin/",
    "EMAIL_TEMPLATES_PATH"  => APP . "email_templates/",
    "EMAIL_ATTACHMENTS_PATH"  => APP . "email_attachments/",

    /**
     * Configuration for: Cookies
     *
     * COOKIE_RUNTIME: How long should a cookie be valid by seconds.
     *      - 1209600 means 2 weeks
     *      - 604800 means 1 week
     * COOKIE_DOMAIN: The domain where the cookie is valid for.
     *      COOKIE_DOMAIN mightn't work with "localhost", ".localhost", "127.0.0.1", or ".127.0.0.1". If so, leave it as empty string, false or null.
     *      @see http://stackoverflow.com/questions/1134290/cookies-on-localhost-with-explicit-domain
     *      @see http://php.net/manual/en/function.setcookie.php#73107
     *
     * COOKIE_PATH: The path where the cookie is valid for. If set to '/', the cookie will be available within the entire COOKIE_DOMAIN.
     * COOKIE_SECURE: If the cookie will be transferred through secured connection(SSL). It's highly recommended to set it to true if you have secured connection
     * COOKIE_HTTP: If set to true, Cookies that can't be accessed by JS - Highly recommended!
     * COOKIE_SECRET_KEY: A random value to make the cookie more secure. Now Stored in database
     *
     */
    "COOKIE_EXPIRY"         => 1209600,
    "SESSION_COOKIE_EXPIRY" => 604800,
    "COOKIE_DOMAIN"         => '',
    "COOKIE_PATH"           => '/',
    "COOKIE_SECURE"         => true,
    "COOKIE_HTTP"           => true,

    /**
     * Configuration for Email
     *
     */
    "EMAIL_FROM"        => "FSGWMS@fsg.com.au",
    "EMAIL_FROM_NAME"   => "Film Shot Graphics Warehouse Management System",
    "EMAIL_REPLY_TO"    => "FSGWMS@fsg.com.au",
    "EMAIl_HOST"        => "smtp.office365.com",
    "EMAIL_PORT"        => 587,

    "EMAIL_PASSWORD_RESET_URL" => PUBLIC_ROOT . "login/resetPassword",


    /**
     * Configuration for: Hashing strength
     *
     * It defines the strength of the password hashing/salting. "10" is the default value by PHP.
     * @see http://php.net/manual/en/function.password-hash.php
     *
     */
    "HASH_COST_FACTOR" => "10",

    /**
     * Configuration for: Pagination
     *
     */
    "PAGINATION_DEFAULT_LIMIT" => 10,

    /*************************************************************************
    * Max Shipping Price Allowed Without Checking
    **************************************************************************/
    'MAX_SHIPPING_CHARGE' => 80,

    /*************************************************************************
    * 3PL Address
    **************************************************************************/
    "THREEPL_ADDRESS" => array(
      	'address'	=>	'5 Mosrael Place',
		'address_2'	=>	'',
		'suburb'	=>	'Rowville',
		'city'		=>	'Melbourne',
		'state'		=>	'VIC',
		'country'	=>	'AU',
		'postcode'	=>	'3178'
	),
    /*************************************************************************
    * FSG Address
    **************************************************************************/
    "FSG_ADDRESS" => array(
        'address'	=>	'865 Mountain Hwy',
        'address_2'	=>	'',
        'suburb'	=>	'Bayswater',
        'city'		=>	'Melbourne',
        'state'		=>	'VIC',
        'country'	=>	'AU',
        'postcode'	=>	'3153'
    ),
    /*************************************************************************
    * Big Bottle Adventure Range
    **************************************************************************/
    "BB_ADRANGE_IDS" => array(
        11594,
        11595,
        11596,
        11597
    ),
    /*************************************************************************
    * Big Bottle 1.5L Range
    **************************************************************************/
    "BB_1.5L_IDS" => array(
        11768,
        11765,
        11767,
        11764,
        11769,
        11766
    ),
    /*************************************************************************
    * Big Bottle Weighted IDS
    **************************************************************************/
    "BB_WEIGHTED_IDS" => array(
        6067,   //big brush
        6040,   //royal chill
        6041,   //blush chill
        6042,    //plum chill
        6030,
        6031,
        10889
    ),
    /*************************************************************************
    * Big Bottle Boxes
    **************************************************************************/
    "BBBOX_WEIGHTS" => array(
        0,
        0.32,
        0.58,
        0.89,
        1.1,
        1.51,
        1.72,
        1.98,
        2.18,
        2.4,
        2.6,
        2.9,
        3,
        3.33,
        3.58,
        3.93,
        4.15,
        4.51,
        4.72,
        4.98,
        5.18,
        5.4,
        5.6,
        5.9,
        6
    ),
    "BBBOX_DIMENSIONS" => array(
        array(14,14,28),
        array(14,14,28),
        array(14,28,28),
        array(28,28,28),
        array(28,28,28),
        array(28,42,28),
        array(28,42,28),
        array(28,56,28),
        array(28,56,28),
        array(56,42,28),
        array(56,42,28),
        array(56,42,28),
        array(56,42,28),
        array(56,56,28),
        array(56,56,28),
        array(70,56,56),
        array(70,56,56)
    ),
    /*************************************************************************
    * Nuchev Boxes
    **************************************************************************/
    "nuchev1softbox" => array(
        'weight'        =>  0.24,
        'dimensions'    =>  array(14,14,19)
    ),
    "nuchev2softbox" => array(
        'weight'        =>  0.45,
        'dimensions'    =>  array(14,28,19)
    ),
    "nuchev3softbox" => array(
        'weight'        =>  0.77,
        'dimensions'    =>  array(15,42,20)
    ),
    "nuchev4softbox" => array(
        'weight'        =>  0.88,
        'dimensions'    =>  array(28,28,19)
    ),
    "nuchev6softbox" => array(
        'weight'        =>  1.26,
        'dimensions'    =>  array(40,28,19)
    ),

    "nuchev1box" => array(
        'weight'        =>  1.1,
        'dimensions'    =>  array(14,14,19)
    ),
    "nuchev2box" => array(
        'weight'        =>  2.1,
        'dimensions'    =>  array(14,28,19)
    ),
    "nuchev3box" => array(
        'weight'        =>  3.3,
        'dimensions'    =>  array(15,42,20)
    ),
    "nuchev4box" => array(
        'weight'        =>  4.2,
        'dimensions'    =>  array(28,28,19)
    ),
    "nuchev6box" => array(
        'weight'        =>  6.3,
        'dimensions'    =>  array(40,28,19)
    ),
    /*************************************************************************
    * Natural Distilling Boxes
    **************************************************************************/
    "NDC1box" => array(
        'weight'        => 1.8,
        'dimensions'    => array(28,14,14)
    ),
    "NDC2box" => array(
        'weight'        => 3.6,
        'dimensions'    => array(28,14,29)
    ),
    "NDC4box" => array(
        'weight'        => 7.2,
        'dimensions'    => array(28,29,29)
    ),
    /*************************************************************************
    * Hide Estimated Shipping Charge From
    **************************************************************************/
    "HIDE_CHARGE_CLIENTS"   =>  array(
        59, //noasleep
        69, //Team Timbuktu
        73, //Natural Distilling Co
    ),
    
    /**
    * Order status
    *
    */

);
