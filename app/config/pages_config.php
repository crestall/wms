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
     * @author     Mark Solly <mark.solly@3plplus.com.au>
    */
$icons = array(
    'orders'            =>  'fas fa-truck',
    'ordering'          =>  'fas fa-cash-register',
    'clients'	        =>	'fas fa-user-tie',
    'products'	        =>	'fas fa-dolly',
    'inventory'	        =>	'fas fa-tasks',
    'reports'	        =>	'far fa-chart-bar',
    'site-settings'	    =>	'fas fa-cog',
    'staff'		        =>	'fas fa-users',
    'stock-movement'    =>  'fas fa-dolly',
    'data-entry'        =>  'fas fa-indent',
    'sales-reps'		=>	'fas fa-users',
    'solar-teams'		=>	'fas fa-users',
    'stores'            =>  'fas fa-store-alt',
    'downloads'         =>  'fas fa-download',
    'financials'        =>  'fas fa-file-invoice-dollar',
    'admin-only'        =>  'fas fa-lock',
    'scheduling'        =>  'far fa-calendar-alt',
    'solar-jobs'        =>  'fas fa-tools'
);
return array(
    "ADMIN_PAGES"   =>  array(
        'orders' => array(
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-truck"></i>'
            ),
            'add-order' =>  array(
                'display'   => true,
                'icon'      => '<i class="fad fa-shipping-fast"></i>'
            ),
            'add-bulk-orders' =>  array(
                'display'   => true,
                'icon'      => '<i class="fal fa-boxes"></i>'
            ),
            'order-update' =>  array(
                'display'   => false,
                'icon'      => ''
            ),
			'order-summaries' =>  array(
                'display'   => true,
                'icon'      => '<i class="fad fa-list-ul"></i>'
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
                'icon'      => '<i class="fad fa-file-search"></i>'
            ),
            'order-search-results' =>  array(
                'display'   => false,
                'icon'      => ''
            ),
            'order-details' =>  array(
                'display'   => false,
                'icon'      => ''
            ),
            'view-orders' =>  array(
                'display'   => true,
                'icon'      => '<i class="fad fa-th-list"></i>'
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
                'icon'      => '<i class="fad fa-file-import"></i>'
            )
        ),
		'clients'	=> array(
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-user-tie"></i>'
            ),
			'view-clients'  => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-users"></i>'
            ),
			'add-client'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-user-plus"></i>'
            ),
			'edit-client'   => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-user-edit"></i>'
            ),
		),
		'products'	=> array(
			'view-products'			=> true,
			'add-product'			=> true,
			'edit-product'			=> false,
			//'bulk-product-upload'	=>	true,
            'pack-items-edit'       =>  true,
            'collections-edit'      =>  true,
            'product-search'        =>  true,
		),
        'inventory'   =>  array(
            'view-inventory'		=>	true,
            'pack-items-manage'     =>  true,
            'scan-to-inventory'     =>  true,
            'product-movement'      =>  false,
            'goods-out'             =>  true,
            'goods-in'               =>  true,
            'add-subtract-stock'    =>  false,
            'quality-control'       =>  false,
            'transfer-location' => true,
            'solar-returns' => true,
            'move-bulk-items'   => true,
            'move-all-client-stock' => true
        ),
		'reports'			=> array(
			'stock-movement-report'	=>	true,
			'dispatch-report'			=>	true,
			'inventory-report'			=>	true,
            'location-report'           =>  true,
            'client-space-usage-report'   =>  true,
            'goods-out-report'          =>  true,
            'goods-out-summary'         =>  true,
            'goods-in-report'          =>  true,
            'goods-in-summary'         =>  true,
            'stock-at-date'             =>  true,
            'unloaded-containers-report'       =>  true,
            'truck-run-sheet'       =>  true,
            '3pl-dispatch-report'   =>  false,
            '3pl-stock-movement-report' =>  false,
            'empty-bay-report'      => true,
            'pickups-report'    => true,
            'solar-returns-report'    => true,
            'solar-consumables-reorder' => true,
            'swatches-report'       => true,
            'order-serial-numbers-report'   => true,
            '3pl-order-serials-report'  => false
		),
        'data-entry'    =>  array(
            'container-unloading'      => true,
            'error-data'                =>  true,
            'incoming-shipments'        =>  true
        ),
		'site-settings'		=> array(
			'order-status'				=> 	true,
			'stock-movement-reasons'	=> 	true,
            'locations'                 =>  true,
            'staff'                     =>  true,
            'manage-users'	            =>	true,
            'packing-types'             =>  true,
            'user-roles'                =>  true,
            'couriers'                  => true,
            'edit-user-profile'         => false,
            'add-user'                  => false
		),
        'financials'    =>  array(
            'directfreight-check'   => true
        ),
        'downloads' => array(
            'super_admin_only'  => true,
            'print-location-barcodes'   => true,
            'useful-barcodes'   => true
        ),
        'admin-only'    => array(
            'super_admin_only'  => true,
            'eparcel-shipment-deleter'  => true,
            'dispatched-orders-updater' => false,
            'client-bay-fixer'  => true,
            'encrypt-some-shit' => false,
            'update-configuration'  => true,
            'reece-data-tidy'   => true
        ),
    ),
    'WAREHOUSE_PAGES' => array(
        'orders'      => array(
            'order-picking'     =>  true,
            'order-packing'     =>  true,
            'order-dispatching'         =>  true,
            'view-orders'               =>  true,
            'order-search'              =>  true,
            'order-search-results'      =>  false,
        ),
        'products'    =>  array(
            'view-products'			=> true,
            'add-product'           =>  true,
            'edit-product'			=> false,
             'product-search'        =>  true,

        ),
        'inventory'     =>  array(
            'view-inventory'		=>	true,
            'product-to-location'   =>  true,
            'scan-to-inventory'     =>  true,
            //'client-locations'      =>  true,
            'product-movement'      =>  false,
            'goods-out'             =>  true,
            'goods-in'               =>  true,
            'add-subtract-stock'    =>  false,
            'quality-control'       =>  false,
            'pack-items-manage'     =>  true,
        )
    ),
    'CLIENT_PAGES' => array(
        'orders'			=>	array(
			'client-orders'		=>	true,
			'order-detail'	    =>	false,
			'order-tracking'	=>	false,
            'add-order'         =>  true,
            'bulk-upload-orders'     =>  true,
            //'book-pickup'       => true,
            //'add-origin-order'  => true
		),
		'inventory'			=>	array(
			'client-inventory'	=>	true,
            'expected-shipments'    =>  true,
            'register-new-stock'    => true
		),
        'reports'           =>  array(
            'dispatch-report'   =>  true,
            'stock-at-date'             =>  true ,
            'returns-report'             =>  true,
            'stock-movement-report'     =>  true,
            'stock-movement-summary'    =>  true,
            'client-dispatch-report'    =>  false,
            'client-stock-movement-report'  =>  false
        )
    )
)
?>