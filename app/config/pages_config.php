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
            'orders-index'  => true,
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-truck fa-2x"></i>'
            ),
            'add-order' =>  array(
                'display'   => true,
                'icon'      => '<i class="fad fa-shipping-fast fa-3x"></i>'
            ),
            'add-bulk-orders' =>  array(
                'display'   => true,
                'icon'      => '<i class="fal fa-boxes fa-3x"></i>'
            ),
            'order-update' =>  array(
                'display'   => false,
                'icon'      => ''
            ),
			'order-summaries' =>  array(
                'display'   => true,
                'icon'      => '<i class="fad fa-list-ul fa-3x"></i>'
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
                'icon'      => '<i class="fad fa-file-search fa-3x"></i>'
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
                'icon'      => '<i class="fad fa-th-list fa-3x"></i>'
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
                'icon'      => '<i class="fad fa-file-import fa-3x"></i>'
            )
        ),
		'clients'	=> array(
            'clients-index' => true,
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-user-tie fa-2x"></i>'
            ),
			'view-clients'  => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-users fa-3x"></i>'
            ),
			'add-client'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-user-plus fa-3x"></i>'
            ),
			'edit-client'   => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-user-edit fa-3x"></i>'
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
                'icon'      => '<i class="fad fa-box-open fa-3x"></i>'
            ),
			'add-product'      => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-hand-holding-box fa-3x"></i>'
            ),
			'edit-product'    => array(
                'display'   => false,
                'icon'      => ''
            ),
            'pack-items-edit'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-edit fa-3x"></i>'
            ),
            'collections-edit'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-edit fa-3x"></i>'
            ),
            'product-search'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-telescope fa-3x"></i>'
            ),
		),
        'inventory'   =>  array(
            'inventory-index'   => true,
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-warehouse-alt fa-2x"></i>'
            ),
            'view-inventory'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-inventory fa-3x"></i>'
            ),
            'pack-items-manage'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-box-check fa-3x"></i>'
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
            ),
            'transfer-location'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-dolly-flatbed fa-3x"></i>'
            ),
            'move-bulk-items'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-dolly-flatbed-alt fa-3x"></i>'
            ),
            'move-all-client-stock'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-conveyor-belt fa-3x"></i>'
            )
        ),
		'reports'   => array(
            'reports-index' => true,
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-chart-bar fa-2x"></i>'
            ),
			'stock-movement-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-person-dolly fa-3x"></i>'
            ),
			'dispatch-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
			'inventory-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
            'location-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
            'client-space-usage-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
            'goods-out-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
            'goods-out-summary'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
            'goods-in-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
            'goods-in-summary'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
            ),
            'stock-at-date'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-calendar-alt fa-3x"></i>'
            ),
            'unloaded-containers-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-container-storage fa-3x"></i>'
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
                'icon'      => '<i class="fad fa-warehouse-alt fa-3x"></i>'
            ),
            'pickups-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-truck-pickup fa-3x"></i>'
            )
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
            ),
            'incoming-shipments'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-shipping-timed fa-3x"></i>'
            )
        ),
		'site-settings'		=> array(
            'site-settings-index'   => true,
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-cog fa-2x"></i>'
            ),
			'order-status'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-tasks fa-3x"></i>'
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
            ),
            'packing-types'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-mail-bulk fa-3x"></i>'
            ),
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
                'icon'      => ''
            ),
            'add-user'    => array(
                'display'   => false,
                'icon'      => ''
            )
		),
        'downloads' => array(
            'downloads-index'   => true,
            'super_admin_only'  => true,
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-download fa-2x"></i>'
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
                'icon'      => '<i class="fad fa-lock-alt fa-2x"></i>'
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
            )
        ),
    ),
    'WAREHOUSE_PAGES' => array(
        'orders'      => array(
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-truck fa-2x"></i>'
            ),
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
            ),
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
            ),
            'product-search'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-telescope fa-3x"></i>'
            ),

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
            ),
            'pack-items-manage'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-box-check fa-3x"></i>'
            ),
        )
    ),
    'CLIENT_PAGES' => array(
        'orders'			=>	array(
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
            'register-new-stock'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-clipboard-check fa-3x"></i>'
            )
		),
        'reports'           =>  array(
            'reports-index'   => true,
            'default-icon'  => array(
                'display'   => false,
                'icon'      => '<i class="fad fa-chart-bar fa-2x"></i>'
            ),
            'dispatch-report'    => array(
                'display'   => true,
                'icon'      => '<i class="fad fa-file-spreadsheet fa-3x"></i>'
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
            )
        )
    )
)
?>