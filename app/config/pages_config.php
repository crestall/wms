<?php
    /**
     * The pages for the app
     * Pages must be listed here or a 404 error will be thrown
     * @format
     * user_access  => array(
     *      controller   => array(
     *                  page-name   => array(
     *                      display-in-menu => boolean  (true/false)
     *                      icon-to-display => string   (fontawesone class)
     *              (
     *       )
     * )
     * @author     Mark Solly <mark.solly@fsg.com.au>
    */
include(APP."/config/icons.php");
$padmin = array(
    'jobs'  => array(), //merges with production later
    'warehouse-orders'  => array(
        'warehouse-orders-index'    => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-truck fa-2x"></i>'
        ),
        'view-orders' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-th-list fa-3x"></i>'
        ),
        'order-update' =>  array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'customers' => array(
        'add-customer'  => array(
            'display'       => true,
            'icon'          => $fontastic_icons['customers']['add-customer']
        ),
        'edit-customer'  => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'finishers' => array(
        'finishers-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['finishers']['default']
        ),
        'view-finishers'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['finishers']['view-finishers']
        ),
        'add-finisher'  => array(
            'display'       => true,
            'icon'          => $fontastic_icons['finishers']['add-finisher']
        ),
        'edit-finisher'  => array(
            'display'   => false,
            'icon'      => ''
        )
    ),/*
    'runsheets' => array(
        'runsheets-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['runsheets']['default']
        ),
        'view-runsheets'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['runsheets']['view-runsheets']
        ),
        'print-runsheets'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['runsheets']['print-runsheets']
        ),
        'finalise-runsheets'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['runsheets']['finalise-runsheets']
        ),
        'finalise-runsheet'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'prepare-runsheets'     => array(
            'display'   => true,
            'icon'      => $fontastic_icons['runsheets']['prepare-runsheets']
        ),
        'prepare-runsheet'  => array(
            'display'   => false,
            'icon'      => ''
        ),
        'add-misc-task'  => array(
            'display'   => false,
            'icon'      => ''
        )
    ),*/
    'production-reports'    => array(
        'production-reports-index'  => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-chart-bar fa-2x"></i>'
        ),
        'warehouse-orders'  => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-warehouse-alt fa-3x"></i>'
        ),
        'order-tracking'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'order-detail'    => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'production-settings'   => array(
        'production-settings-index' => true,
        'default-icon'              => array(
            'display'   => false,
            'icon'      => $fontastic_icons['production-settings']['default']
        ),
        'drivers'   => array(
            'display'   => true,
            'icon'      => $fontastic_icons['production-settings']['drivers']
        ),/*
        'customers-csv-import'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-2x"><span class="fa-layers fa-fw"><i class="fad fa-file-import"></i><i class="fal fa-file-csv" data-fa-transform="shrink-5 left-6 up-2"></i></span></div>'
        ),
        'suppliers-csv-import'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-2x"><span class="fa-layers fa-fw"><i class="fad fa-file-import"></i><i class="fal fa-file-csv" data-fa-transform="shrink-5 left-6 up-2"></i></span></div>'
        ),*/
        'edit-job-status'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['production-settings']['edit-job-status']
        ),
        'finisher-categories'   => array(
            'display'   => true,
            'icon'      => $fontastic_icons['production-settings']['finisher-categories']
        ),/*
        'job-csv-import'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-2x"><span class="fa-layers fa-fw"><i class="fad fa-file-import"></i><i class="fal fa-file-csv" data-fa-transform="shrink-5 left-6 up-2"></i></span></div>'
        ) */
    ),
    'fsg-contacts'    => array(
        'fsg-contacts-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['fsg-contacts']['default']
        ),
        'view-contacts' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['fsg-contacts']['view-contacts']
        ),
        'add-contact'   => array(
            'display'   => true,
            'icon'      => $fontastic_icons['fsg-contacts']['add-contact']
        ),
        'edit-contact'  => array(
            'display'   => false,
            'icon'      => ''
        )
    )
);
$prod = array(
    'jobs'  => array(
        'jobs-index'    => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => $fontastic_icons['jobs']['default']
        ),
        'add-job'   => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['add-job']
        ),
        'update-job'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'create-delivery-docket'    => array
        (
            'display'   => false,
            'icon'      => ''
        ),
        'view-jobs' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['view-jobs']
        ),/**/
        'job-search' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['job-search']
        ),
        'job-search-results'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'get-shipping-quotes'   => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-file-invoice-dollar"></i></div>'
        ),
        'create-shipment'    => array(
            'display'   => false,
            'icon'      => ''
        ),/*
        'manage-shipments'  => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-3x"></i>'
        ),
        'manage-shipment'    => array(
            'display'   => false,
            'icon'      => ''
        ),*/
        'shipment-address-update' => array(
            'display'   => false,
            'icon'      => ''
        ),
        'errors'    => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'customers' => array(
        'customers-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['customers']['default']
        ),
        'view-customers'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['customers']['view-customers']
        ),
        'view-customer'    => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'finishers' => array(
        'finishers-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['finishers']['default']
        ),
        'view-finishers'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['finishers']['view-finishers']
        ),
        'view-finisher'    => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'fsg-contacts'    => array(
        'fsg-contacts-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['fsg-contacts']['default']
        ),
        'view-contacts' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['fsg-contacts']['view-contacts']
        )
    )
);
$prod_sales = array(
    'jobs'  => array(
        'jobs-index'    => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => $fontastic_icons['jobs']['default']
        ),
        'create-delivery-docket'    => array
        (
            'display'   => false,
            'icon'      => ''
        ),
        'view-jobs' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['view-jobs']
        ),
        'job-search' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['job-search']
        ),
        'job-search-results'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'get-shipping-quotes'   => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-file-invoice-dollar fa-3x"></i></div>'
        )
    ),
    'customers' => array(
        'customers-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['customers']['default']
        ),
        'view-customers'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['customers']['view-customers']
        ),
        'add-customer'  => array(
            'display'       => true,
            'icon'          => $fontastic_icons['customers']['add-customer']
        ),
        'edit-customer'  => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'finishers' => array(
        'finishers-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['finishers']['default']
        ),
        'view-finishers'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['finishers']['view-finishers']
        )
    ),
    'fsg-contacts'    => array(
        'fsg-contacts-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['fsg-contacts']['default']
        ),
        'view-contacts' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['fsg-contacts']['view-contacts']
        )
    )
);
$prod_sales_admin = array(
    'jobs'  => array(
        'jobs-index'    => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => $fontastic_icons['jobs']['default']
        ),
        'add-job'   => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['add-job']
        ),
        'view-jobs' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['view-jobs']
        ),/**/
        'job-search' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['job-search']
        ),
        'create-delivery-docket'    => array
        (
            'display'   => false,
            'icon'      => ''
        ),
        'job-search-results'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'get-shipping-quotes'   => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-file-invoice-dollar"></i></div>'
        )
    ),
    'customers' => array(
        'customers-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['customers']['default']
        ),
        'view-customers'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['customers']['view-customers']
        ),
        'add-customer'  => array(
            'display'       => true,
            'icon'          => $fontastic_icons['customers']['add-customer']
        ),
        'edit-customer'  => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'finishers' => array(
        'finishers-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['finishers']['default']
        ),
        'view-finishers'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['finishers']['view-finishers']
        ),
        'add-finisher'  => array(
            'display'       => true,
            'icon'          => $fontastic_icons['finishers']['add-finisher']
        ),
        'edit-finisher'  => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'fsg-contacts'    => array(
        'fsg-contacts-index'   => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => $fontastic_icons['fsg-contacts']['default']
        ),
        'view-contacts' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['fsg-contacts']['view-contacts']
        ),
        'add-contact'   => array(
            'display'   => true,
            'icon'      => $fontastic_icons['fsg-contacts']['add-contact']
        ),
        'edit-contact'  => array(
            'display'   => false,
            'icon'      => ''
        )
    )
);
$admin = array(
   'orders' => array(
        'orders-index'  => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => $fontastic_icons['orders']['default']
        ),
        'add-order' =>  array(
            'display'   => true,
            'icon'      => $fontastic_icons['orders']['add-order']
        ),
        'get-quotes'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-file-invoice-dollar"></i></div>'
        ),
        'add-bulk-orders' =>  array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-boxes"></i></div>'
        ),
        'order-update' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'order-summaries' =>  array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-list-ul"></i></div>'
        ),
        'edit-address' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'order-edit' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'edit-customer' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'order-search' =>  array(
            'display'   => true,
            'icon'      => $fontastic_icons['orders']['order-search']
        ),
        'order-search-results' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'order-detail' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'view-orders' =>  array(
            'display'   => true,
            'icon'      => $fontastic_icons['orders']['view-orders']
        ),
        'address-update' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'items-update' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'order-importing' =>  array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-file-import"></i></div>'
        ),
        'book-direct-freight-collection'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-truck"></i></div>'
        ),
        'view-backorders'   => array(
            'display'   => true,
            'icon'      => $fontastic_icons['orders']['back-orders']
        )
    ),
    'jobs'      => array(
        'jobs-index'    => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => $fontastic_icons['jobs']['default']
        ),
        'create-delivery-docket'    => array
        (
            'display'   => false,
            'icon'      => ''
        ),
        'view-jobs' => array(
            'display'   => true,
            'icon'      => $fontastic_icons['jobs']['view-jobs']
        )
    ),
    'deliveries'    => array(
        'deliveries-index'  => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-truck fa-2x"></i>'
        ),
        'manage-deliveries' => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-shipping-fast"></i></div>'
        ),
        'manage-delivery' => array(
            'display'   => false,
            'icon'      => ''
        ),
        'manage-pickups' => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-shipping-fast fa-flip-horizontal"></i></div>'
        ),
        'manage-pickup' => array(
            'display'   => false,
            'icon'      => ''
        ),
        'delivery-search' =>  array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-file-search"></i></div>'
        ),
        'delivery-search-results' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'pickup-search' =>  array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-file-search"></i></div>'
        ),
        'pickup-search-results' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'pickup-detail'    => array(
            'display'   => false,
            'icon'      => ''
        ),
    ),
    //'runsheets' => array(),
    'clients'	=> array(
        'clients-index' => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-user-tie fa-2x"></i>'
        ),
        'view-clients'  => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-users"></i></div>'
        ),
        'add-client'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-user-plus"></i></div>'
        ),
        'edit-client'   => array(
            'display'   => false,
            'icon'      => '<div fa-3x"><i class="fad fa-user-edit"></i></div>'
        ),
    ),
    'products'	=> array(
        'products-index'    => true,
        'default-icon'      => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-dolly fa-2x"></i>'
        ),
        'view-products'    => array(
                'display'   => true,
                'icon'      => '<div class="fa-3x"><i class="fad fa-box-open"></i></div>'
            ),
        'add-product'      => array(
                'display'   => true,
                'icon'      => '<div class="fa-3x"><i class="fad fa-hand-holding-box"></i></div>'
            ),
        'edit-product'    => array(
                'display'   => false,
                'icon'      => ''
            ),
        'collections-edit'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-edit"></i></div>'
        ),/*
        'product-search'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-telescope fa-3x"></i>'
        ),*/
    ),
    'inventory'   =>  array(
        'inventory-index'   => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-warehouse-alt fa-2x"></i>'
        ),
        'view-inventory'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-inventory"></i></div>'
        ),
        'scan-to-inventory'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-scanner"></div></i>'
        ),
        'receive-pod-stock'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-scanner fa-flip-horizontal"></i></div>'
        ),
        'product-movement'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'goods-out'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-forklift"></i></div>'
        ),
        'goods-in'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-forklift fa-flip-horizontal"></i></div>'
        ),
        'add-subtract-stock'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'move-stock'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'quality-control'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'transfer-location'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-dolly-flatbed"></i></div>'
        ),/*
        'move-bulk-items'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-dolly-flatbed-alt fa-3x"></i>'
        ),
        'move-all-client-stock'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-conveyor-belt fa-3x"></i>'
        )*/
    ),
    'reports'   => array(
        'reports-index' => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-chart-bar fa-2x"></i>'
        ),
        'stock-movement-report'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-person-dolly"></i></div>'
        ),
        'client-space-usage-report' => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-warehouse"></i></div>'
        ),
        'delivery-client-space-usage-report' => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-warehouse"></i></div>'
        ),
        'dispatch-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'inventory-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'location-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'client-bay-usage-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'goods-out-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'goods-out-summary'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'goods-in-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'goods-in-summary'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'stock-at-date'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-calendar-alt"></i></div>'
        ),
        'unloaded-containers-report'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-container-storage"></i></div>'
        ),
        '3pl-dispatch-report'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        '3pl-stock-movement-report'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'empty-bay-report'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fad fa-warehouse-alt"></i></div>'
        ),
        'client-bays-usage-report'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><i class="fal fa-inventory"></i></div>'
        ),
        'deliveries-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
        'fsg-deliveries-report'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'pickups-report'    => array(
            'display'   => true,
            'icon'      => $fontastic_icons['generic']['spreadsheet']
        ),
    ),
    'data-entry'    =>  array(
        'data-entry-index'  => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-indent fa-2x"></i>'
        ),
        'container-unloading'    => array(
            'display'   => true,
            'icon'      => '<i class="fal fa-container-storage fa-3x"></i>'
        ),/*
        'incoming-shipments'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-timed fa-3x"></i>'
        )*/
    ),
    'site-settings'		=> array(
        'site-settings-index'   => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-cog fa-2x"></i>'
        ),/**/
        'delivery-urgencies'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-exclamation-triangle fa-3x"></i>'
        ),
        'stock-movement-reasons'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-dolly-flatbed fa-3x"></i>'
        ),
        'locations'    => array(
            'display'   => true,
            'icon'      => '<i class="fal fa-warehouse-alt fa-3x"></i>'
        ),
        'manage-users'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-users fa-3x"></i>'
        ),/*
        'packing-types'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-mail-bulk fa-3x"></i>'
        ),*/
        'user-roles'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-users-cog fa-3x"></i>'
        ),
        'couriers'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-truck-loading fa-3x"></i>'
        ),
        'edit-user-profile'    => array(
            'display'   => false,
            'icon'      => '<i class="fal fa-warehouse"></i>'
        ),
        'add-user'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'drivers'   => array(
            'display'   => true,
            //'icon'      => '<span class="fa-layers fa-fw fa-3x align-middle"><i class="fal fa-user"></i><i class="fad fa-steering-wheel" data-fa-transform="shrink-6 down-6"></i></span>'
            'icon'      => '<span class="fa-3x align-middle"><i class="fad fa-steering-wheel" data-fa-transform="shrink-6 down-6" data-fa-mask="fad fa-user"></i></span>'
        )
    ),
    'downloads' => array(
        'downloads-index'   => true,
        'super_admin_only'  => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-download fa-3x"></i>'
        ),
        'print-location-barcodes'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-print fa-3x"></i>'
        ),
        'useful-barcodes'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-barcode-alt fa-3x"></i>'
        )
    ),
    'admin-only'    => array(
        'admin-only-index'  => true,
        'super_admin_only'  => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-lock-alt fa-3x"></i>'
        ),
        'eparcel-shipment-deleter'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-trash-alt fa-3x"></i>'
        ),
        'encrypt-some-shit'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'update-configuration'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-check-double fa-3x"></i>'
        ),
        'reece-data-tidy'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-calendar-check fa-3x"></i>'
        ),
        'runsheet-completion-tidy'    => array(
            'display'   => true,
            'icon'      => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-list-ol"></i><i class="far fa-check" data-fa-transform="right-3 shrink-2 up-1" style="color:#66ff33"></i></span></div>'
        ),
        'shopify-api-testing'   => array(
            'display'   => false,
            'icon'      => "<i class='fal fa-question-square fa-3x'></i>"
        ),
        'update-production-database-tables'   => array(
            'display'   => false,
            'icon'      => "<i class='fas fa-database fa-3x'></i>"
        ),
        'ebay-api-testing'   => array(
            'display'   => false,
            'icon'      => "<i class='fad fa-server fa-3x'></i>"
        ),
        'inventory-comparing'   => array(
            'display'   => true,
            'icon'      => "<i class='fal fa-warehouse fa-3x'></i>"
        ),
        'marketplacer-testing'  => array(
            'display'   => true,
            'icon'      => "<i class='fal fa-question-square fa-3x'></i>"
        )
    )
);
$warehouse = array(
    'orders'      => array(
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-truck fa-3x"></i>'
        ),/*
        'order-picking' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-hand-holding-box fa-3x"></i>'
        ),
        'order-packing' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-box-open fa-3x"></i>'
        ),
        'order-dispatching' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-mail-bulk fa-3x"></i>'
        ),*/
        'view-orders' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-th-list fa-3x"></i>'
        ),
        'order-search' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-file-search fa-3x"></i>'
        ),
        'order-search-results' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
    ),
    'products'    =>  array(
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-dolly fa-2x"></i>'
        ),
        'view-products'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-box-open fa-3x"></i>'
        ),
        'add-product'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-hand-holding-box fa-3x"></i>'
        ),
        'edit-product'    => array(
            'display'   => false,
            'icon'      => ''
        ),/*
        'product-search'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-telescope fa-3x"></i>'
        ),*/

    ),
    'inventory'     =>  array(
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-warehouse-alt fa-2x"></i>'
        ),
        'view-inventory'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-inventory fa-3x"></i>'
        ),
        'product-to-location'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-dolly-flatbed fa-3x"></i>'
        ),
        'scan-to-inventory'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-scanner fa-3x"></i>'
        ),
        'product-movement'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'goods-out'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-forklift fa-3x"></i>'
        ),
        'goods-in'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-forklift fa-flip-horizontal fa-3x"></i>'
        ),
        'add-subtract-stock'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'quality-control'    => array(
            'display'   => false,
            'icon'      => ''
        )
    )
);
$client = array(
    'deliveries'    => array(
        'delivery-clients'  => true,
        'deliveries-index'  => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-truck fa-2x"></i>'
        ),
        'book-delivery' => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-3x"></i>'
        ),
        'book-pickup' => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-flip-horizontal fa-3x"></i>'
        ),
        'view-deliveries'   => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-th-list fa-3x"></i>'
        ),
        'view-pickups'   => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-th-list fa-3x fa-flip-horizontal"></i>'
        ),
        'delivery-search' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-file-search fa-3x"></i>'
        ),
        'delivery-search-results' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'pickup-search' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-file-search fa-3x"></i>'
        ),
        'pickup-search-results' =>  array(
            'display'   => false,
            'icon'      => ''
        ),
        'pickup-detail'    => array(
            'display'   => false,
            'icon'      => ''
        )
    ),
    'orders'			=>	array(
        'delivery-clients'  => false,
        'orders-index'   => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-truck fa-2x"></i>'
        ),
        'client-orders' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-th-list fa-3x"></i>'
        ),
        'order-detail'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'order-tracking'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'add-order' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-3x"></i>'
        ),
        'bulk-upload-orders' =>  array(
            'display'   => true,
            'icon'      => '<i class="fad fa-upload fa-3x"></i>'
        )
    ),
    'inventory'			=>	array(
        'inventory-index'   => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-warehouse-alt fa-2x"></i>'
        ),
        'client-inventory'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-inventory fa-3x"></i>'
        ),
        'expected-shipments'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-timed fa-3x"></i>'
        ),
        'record-new-product'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-clipboard-check fa-3x"></i>'
        ),
        'view-collections'  => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-edit fa-3x"></i>'
        )
    ),
    'reports'           =>  array(
        'reports-index'   => true,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => '<i class="fad fa-chart-bar fa-2x"></i>'
        ),
        'space-usage-report'    => array(
            'display'   => true,
            'icon'      => '<i class="fal fa-warehouse fa-3x"></i>',
        ),
        'dispatch-report'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-3x"></i>'
        ),
        'stock-at-date'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-calendar-alt fa-3x"></i>'
        ),
        'returns-report'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-flip-horizontal fa-3x"></i>'
        ),
        'stock-movement-report'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-person-dolly fa-3x"></i>'
        ),
        'stock-movement-summary'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-person-dolly fa-3x"></i>'
        ),
        'client-dispatch-report'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'client-stock-movement-report'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'deliveries-report'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-3x"></i>'
        ),
        'client-deliveries-report'    => array(
            'display'   => false,
            'icon'      => ''
        ),
        'pickups-report'    => array(
            'display'   => true,
            'icon'      => '<i class="fad fa-shipping-fast fa-flip-horizontal fa-3x"></i>'
        ),
        'client-pickups-report'    => array(
            'display'   => false,
            'icon'      => ''
        ),
    ),
    'products' => array(
        'products-index'   => false,
        'default-icon'  => array(
            'display'   => false,
            'icon'      => ''
        ),
        'client-product-edit' => array(
            'display'   => false,
            'icon'      => ''
        )
    )
);

//merge and tidy page arrays
$padmin['jobs'] = array_merge($padmin['jobs'], $prod['jobs']);
$padmin['customers'] = array_merge($padmin['customers'], $prod['customers']);
$padmin['finishers'] = array_merge($padmin['finishers'], $prod['finishers']);

//share the reports
$prod_sales_admin['production-reports'] = $prod_sales['production-reports'] = $padmin['production-reports'];

//$admin['runsheets'] = array_merge($admin['runsheets'], $padmin['runsheets']);
$admin['jobs'] = array_merge($admin['jobs'], $prod['jobs']);

$prod_admin = array_merge($padmin,$prod);

//add the errors pages

//return the pages
return array(
    "PRODUCTION_SALES_ADMIN_PAGES"    => $prod_sales_admin,
    "PRODUCTION_SALES_PAGES"          => $prod_sales,
    "PRODUCTION_ADMIN_PAGES"          => $prod_admin,
    "PRODUCTION_PAGES"                => $prod,
    "ADMIN_PAGES"                     => $admin ,
    'WAREHOUSE_PAGES'                 => $warehouse,
    'CLIENT_PAGES'                    => $client
)
?>