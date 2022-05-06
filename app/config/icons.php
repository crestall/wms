<?php
    /**
     * Fontastic Icons To Use for page tiles throughout the app
     *
     * try to keep it alphabetical to avoid double ups
     *
     * @author     Mark Solly <mark.solly@fsg.com.au>
    */

$fontastic_icons = array(
    'customers' => array(
        'default'           => '<i class="fad fa-user-tie"></i>',
        'add-customer'      => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-user-tie"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span></div>',
        'view-customers'    => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-user-tie"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span></div>',
    ),
    'financials'    => array(
        'default'                   => '<i class="fad fa-usd-circle fa-2x"></i>',
        'delivery-client-charges'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fal fa-truck"></i><i class="fad fa-usd-circle" data-fa-transform="shrink-4 up-1 left-3"></i></span></div>',
        'pickpack-client-charges'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-thin fa-box-open-full"></i><i class="fad fa-usd-circle" data-fa-transform="shrink-4 up-4 right-1"></i></span></div>' 
    ),
    'finishers' => array(
        'default'           => '<i class="fad fa-people-arrows"></i>',
        'add-finisher'      => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-people-arrows"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span></div>',
        'view-finishers'    => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-people-arrows"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span></div>'
    ),
    'fsg-contacts'  => array(
        'default'       => '<i class="fad fa-user-chart"></i>',
        'add-contact'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-user-chart"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span></div>',
        'view-contacts' => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-user-chart"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span></div>',

    ),
    'jobs'  => array(
        'default'       => '<i class="fad fa-tasks"></i>',
        'add-job'       => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-tasks"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-3 right-4"></i></span></div>',
        'job-search'    => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-tasks"></i><i class="far fa-search" data-fa-transform="shrink-4 up-1 right-4"></i></span></div>',
        'view-jobs'     => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-tasks"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span></div>'
    ),
    'production-settings'   => array(
        'default'               => '<i class="fad fa-cog"></i>',
        'drivers'               => '<span class="fa-3x align-middle"><i class="fad fa-steering-wheel" data-fa-transform="shrink-6 down-6" data-fa-mask="fad fa-user"></i></span>',
        'edit-job-status'       => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fal fa-list-alt" data-fa-transform="grow-2"></i><i class="fad fa-pencil-alt" data-fa-transform="shrink-4 up-5 right-8"></i></span></div>',
        'finisher-categories'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fad fa-people-arrows"></i><i class="fal fa-list-ol" data-fa-transform="shrink-5 up-6"></i></span></div>'
    ),
    'orders'    => array(
        //'default'       => '<i class="fad fa-truck fa-2x"></i>',
        'default'       => '<i class="fal fa-shopping-cart fa-2x"></i>',
        'add-order'     => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fal fa-shopping-cart"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-8 right-4"></i></span></div>',
        'back-orders'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fal fa-exchange" data-fa-transform="shrink-4 up-1 left-8"></i><i class="fal fa-truck"></i></span></div>',
        'order-search'  => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fal fa-shopping-cart"></i><i class="fad fa-search" data-fa-transform="shrink-4 up-6 right-2"></i></span></div>',
        'view-orders'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fal fa-shopping-cart"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-6 right-4 rotate-30"></i></span></div>'
    ),
    'runsheets' => array(
        'default'               => '<i class="fad fa-list-ol"></i>',
        'finalise-runsheets'    => '<div class="fa-2x"><span class="fa-layers fa-fw"><i class="fad fa-list-ol"></i><i class="far fa-check" data-fa-transform="right-3 shrink-2 up-1" style="color:#66ff33"></i></span></div>',
        'prepare-runsheets'     => '<div class="fa-2x"><span class="fa-layers fa-fw"><i class="fad fa-list-ol"></i><i class="fal fa-pencil-alt" data-fa-transform="right-5 shrink-6 up-2"></i></span></div>',
        'print-runsheets'       => '<div class="fa-2x"><span class="fa-layers fa-fw"><i class="fad fa-list-ol"></i><i class="fal fa-print" data-fa-transform="right-3 shrink-5 up-3"></i></span></div>',
        'view-runsheets'        => '<div class="fa-2x"><span class="fa-layers fa-fw"><i class="fad fa-list-ol"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span></div>',

    ),
    'generic'   => array(
        'spreadsheet'   => '<div class="fa-3x"><i class="fad fa-file-spreadsheet"></i></div>'
    ),
    'deliveries'   => array(
        'add-delivery'      => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-thin fa-truck-fast"></i><i class="fa-solid fa-plus" data-fa-transform="shrink-5 up-3 left-3"></i></span></div>',
        'add-pickup'        => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-thin fa-flip-horizontal fa-truck-fast"></i><i class="fa-solid fa-plus" data-fa-transform="shrink-5 up-3 right-3"></i></span></div>',
        'pickup-search'     => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-thin fa-flip-horizontal fa-truck-fast"></i><i class="fa-solid fa-magnifying-glass" data-fa-transform="shrink-6 up-2 right-4"></i></span></div>',
        'delivery-search'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-thin fa-truck-fast"></i><i class="fa-solid fa-magnifying-glass" data-fa-transform="shrink-6 up-2 left-4 rotate-90"></i></span></div>',
        'view-pickups'      => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-thin fa-flip-horizontal fa-truck-fast"></i><i class="fa-solid fa-binoculars" data-fa-transform="shrink-6 up-6 right-6 rotate-30"></i></span></div>',
        'view-deliveries'   => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-thin fa-truck-fast"></i><i class="fa-solid fa-binoculars" data-fa-transform="shrink-6 up-6 left-6 rotate-330"></i></span></div>'
    ),
    'items-collections' => array(
        'record-collection' => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-duotone fa-boxes-packing"></i><i class="far fa-plus" data-fa-transform="shrink-4 up-5 right-5"></i></span></div>',
        'view-collections'  => '<div class="fa-3x"><span class="fa-layers fa-fw"><i class="fa-duotone fa-boxes-packing"></i><i class="fal fa-binoculars" data-fa-transform="shrink-6 up-2 right-6 rotate-30"></i></span></div>'
    )
);

?>