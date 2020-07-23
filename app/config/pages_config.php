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
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-dolly"></i>'
            ),
			'view-products'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-box-open"></i>'
            ),
			'add-product'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-hand-holding-box"></i>'
            ),
			'edit-product'    => array(
                'display'   => false,
                'icon'      => ''
            ),
            'pack-items-edit'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-edit"></i>'
            ),
            'collections-edit'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-edit"></i>'
            ),
            'product-search'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-telescope"></i>'
            ),
		),
        'inventory'   =>  array(
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-warehouse-alt"></i>'
            ),
            'view-inventory'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-inventory"></i>'
            ),
            'pack-items-manage'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-box-check"></i>'
            ),
            'scan-to-inventory'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-scanner"></i>'
            ),
            'product-movement'    => array(
                'display'   => false,
                'icon'      => ''
            ),
            'goods-out'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-forklift"></i>'
            ),
            'goods-in'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-forklift fa-flip-horizontal"></i>'
            ),
            'add-subtract-stock'    => array(
                'display'   => false,
                'icon'      => ''
            ),
            'quality-control'    => array(
                'display'   => false,
                'icon'      => ''
            ),
            'transfer-location'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-dolly-flatbed"></i>'
            ),
            'move-bulk-items'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-dolly-flatbed-alt"></i>'
            ),
            'move-all-client-stock'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-conveyor-belt"></i>'
            )
        ),
		'reports'			=> array(
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-chart-bar"></i>'
            ),
			'stock-movement-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-person-dolly"></i>'
            ),
			'dispatch-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
			'inventory-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
            'location-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
            'client-space-usage-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
            'goods-out-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
            'goods-out-summary'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
            'goods-in-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
            'goods-in-summary'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet"></i>'
            ),
            'stock-at-date'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-calendar-alt"></i>'
            ),
            'unloaded-containers-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-container-storage"></i>'
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
                'icon'      => '<i class="fad fa-warehouse-alt"></i>'
            ),
            'pickups-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-truck-pickup"></i>'
            )
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