<?php

/**
 * Downloads controller
 *
 
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class DownloadsController extends Controller {


    public function beforeAction(){
        /*  */
        parent::beforeAction();
        $action = $this->request->param('action');
        $this->Security->config("validateForm", false);
        $post_actions = ['clientDispatchReportCSV'];
        $this->Security->requirePost($post_actions);
        $secure_downloads = [
            'stockMovementCSV',
            'clientBayUsageCSV',
            'clientClientSpaceUsageCSV',
            'clientDispatchReportCSV',
            'clientInventoryCSV',
            'clientSpaceUsageCSV',
            'clientStockMovementCSV',
            'clientStockSummaryCSV',
            'deliveriesReportCSV',
            'deliveryClientSpaceUsageCSV',
            'dispatchReportCSV',
            'goodsInReportCSV',
            'goodsOutReportCSV',
            'locationReportCSV',
            'returnsReportCSV',
            'solarInventoryCSV',
            'stockAtDateCSV',
            'truckRunSheetCSV',
            'inventoryReportCSV',
            'orderAuspostExportCSV',
            'orderExportCSV'
        ];
        if(in_array($action, $secure_downloads))
        {
            $this->Security->config("validateCsrfToken", true);
        }
    }

    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'downloads-index');
        parent::displayIndex(get_class());
    }

    public function clientDeliveryClientSpaceUsageCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $cols = array(
            "Bay Name",
            "Date Added",
            "Date Removed",
            "Item",
            "Size",
            "Days Held"
        );
        //$bays = $this->deliveryclientsbay->getClientSpaceUsage($date, $client_id);
        $bays = $this->deliveryclientsbay->getSpaceUsage($from, $to, $client_id);
        $rows = array();
        $standard_bay_days = $oversize_bay_days = 0;
        foreach($bays as $b)
        {
            $date_removed = ($b['date_removed'] > 0)? date("d/m/Y", $b['date_removed']): "";
            $row = [
                $b['location'],
                date("d/m/Y", $b['date_added']),
                $date_removed,
                $b['item_name'],
                ucwords($b['size']),
                $b['days_held']
            ];
            $rows[] = $row;
            if(strtolower($b['size']) == "standard")
                $standard_bay_days += $b['days_held'];
            else
                $oversize_bay_days += $b['days_held'];
        }
        array_unshift($rows, ['','','','','','','','']);
        $oversize_row = [
            'Bays per week or part of',
            '','','','','',
            'Oversize',
            ceil($oversize_bay_days / 7)
        ];
        //$rows[] = $oversize_row;
        array_unshift($rows, $oversize_row);
        $standard_row = [
            'Bays per week or part of',
            '','','','','',
            'Standard',
            ceil($standard_bay_days /7)
        ];
        //$rows[] = $standard_row;
        array_unshift($rows, $standard_row);
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "space_usage".date("Ymd")]);
    }

    public function clientClientSpaceUsageCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $cols = array(
            "Bay Name",
            "Date Added",
            "Date removed",
            "Oversize",
            "Pickface",
            "Days Held"
        );
        $bays = $this->clientsbays->getClientSpaceUsage($date, $client_id);
        $rows = array();
        foreach($bays as $b)
        {
            $date_removed = ($b['date_removed'] > 0)? ($b['date_removed'] > $date)? "After ".date("d/m/Y", strtotime("-1 day", $date)) : date("d/m/Y", $b['date_removed']): "";
            $oversize = ($b['oversize']> 0)? "Yes" : "No";
            $pickface = ($b['tray']> 0)? "Yes" : "No";
            $row = [
                $b['location'],
                date("d/m/Y", $b['date_added']),
                $date_removed,
                $oversize,
                $pickface,
                $b['days_held']
            ];
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "space_usage".date("Ymd")]);
    }

    public function deliveryClientSpaceUsageCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $cols = array(
            "Client",
            "Bay Name",
            "Date Added",
            "Date Removed",
            "Item",
            "Days Held",
            "Charge Rate",
            "Charge"
        );
        $bays = $this->deliveryclientsbay->getSpaceUsage($from, $to, $client_id);
        $rows = array();
        $standard_charge = $oversize_charge = 0;
        foreach($bays as $b)
        {
            $date_removed = ($b['date_removed'] > 0)? date("d/m/Y", $b['date_removed']) : "";
            $row = [
                $b['client_name'],
                $b['location'],
                date("d/m/Y", $b['date_added']),
                $date_removed,
                $b['item_name'],
                $b['days_held'],
                ucwords($b['size']),
                "$ ".$b['storage_charge']
            ];
            $rows[] = $row;
            if(strtolower($b['size']) == "standard")
                $standard_charge += $b['storage_charge'];
            else
                $oversize_charge += $b['storage_charge'];
        }
        array_unshift($rows, ['','','','','','','','']);
        $oversize_row = [
            'Total Charges',
            '','','','','',
            'Oversize',
            "$".$oversize_charge
        ];
        //$rows[] = $oversize_row;
        array_unshift($rows, $oversize_row);
        $standard_row = [
            'Total Charges',
            '','','','','',
            'Standard',
            "$".$standard_charge
        ];
        //$rows[] = $standard_row;
        array_unshift($rows, $standard_row);
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "delivery_client_space_usage".date("Ymd")]);
    }

    public function clientSpaceUsageCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $charge_rates = [
            "Standard",
            "Hi Bay"
        ];
        $cols = array(
            "Client",
            "Bay Name",
            "Date Added",
            "Date removed",
            "Days Held",
            "Charge Rate",
            "Charge"
        );
        $bays = $this->clientsbays->getSpaceUsage($from, $to, $client_id);
        $rows = array();
        foreach($bays as $b)
        {
            $date_removed = ($b['date_removed'] > 0)? date("d/m/Y", $b['date_removed']) : "";
            $row = [
                $b['client_name'],
                $b['location'],
                date("d/m/Y", $b['date_added']),
                $date_removed,
                $b['days_held'],
                $charge_rates[$b['oversize']],
                "$ ".$b['storage_charge']
            ];
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "client_space_usage".date("Ymd")]);
    }

    public function pickupsReportCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $cols = array(
            "Pickup Number/Booked By",
            "Date Requested",
            "Date Completed",
            "Pickup Address",
            "Urgency",
            "Vehicle",
            "Charge Level"
        );
        if(Session::getUserRole() != "client")
        {
            $admin_cols = [
                "Pickup Charge",
                "Repalletize Charge",
                "Rewrap Charge",
                "GST",
                "Total Charge"
            ];
            $cols = array_merge($cols, $admin_cols);
        }
        $pickups = $this->pickup->getClosedPickups($client_id, $from, $to);
        $rows = array();
        $extra_cols = 0;
        foreach($pickups as $d)
        {
            $address_string = $d['client_name'];
            if(!empty($d['address'])) $address_string .= "\n".$d['address'];
            if(!empty($d['address_2'])) $address_string .= "\n".$d['address_2'];
            if(!empty($d['suburb'])) $address_string .= "\n".$d['suburb'];
            if(!empty($d['state'])) $address_string .= "\n".$d['state'];
            if(!empty($d['country'])) $address_string .= "\n".$d['country'];
            if(!empty($d['postcode'])) $address_string .= "\n".$d['postcode'];
            $items = explode("~",$d['items']);
            $row = [
                $d['pickup_number']."\n".$d['requested_by_name'],
                date('D d/m/Y - g:i A', $d['date_entered']),
                date('D d/m/Y - g:i A', $d['date_fulfilled']),
                $address_string,
                $d['pickup_window'],
                $d['vehicle_type'],
                $d['charge_level']
            ];
            if(Session::getUserRole() != "client")
            {
                $row[] =  "$".number_format($d['shipping_charge'], 2, '.', ',');
                $row[] =  "$".number_format($d['repalletize_charge'], 2, '.', ',');
                $row[] =  "$".number_format($d['rewrap_charge'], 2, '.', ',');
                $row[] =  "$".number_format($d['gst'], 2, '.', ',');
                $row[] =  "$".number_format($d['total_charge'], 2, '.', ',');
            }
            $extra_cols = max($extra_cols, count($items));
            $i = 1;
            foreach($items as $item)
            {
                list($item_id, $item_name, $item_sku, $pallet_count) = explode("|",$item);
                $row[] = $item_name. "(".$item_sku.")";
                $row[] = $pallet_count;
                ++$i;
            }
            $rows[] = $row;
        }
        $i = 1;
        while($i <= $extra_cols)
        {
            $cols[] = "Item $i Name";
            $cols[] = "Item $i Pallets";
            ++$i;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "pickups_report_".date("Ymd")]);
    }

    public function deliveriesReportCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $cols = array(
            "Delivery Number/Reference",
            "Date Entered",
            "Date Fulfilled",
            "Delivered To",
            "Urgency",
            "Vehicle",
            "Charge Level"
        );
        if(Session::getUserRole() != "client")
        {
            $admin_cols = [
                "Charge",
                "GST",
                "Total Charge"
            ];
            $cols = array_merge($cols, $admin_cols);
        }
        $deliveries = $this->delivery->getClosedDeliveries($client_id, $from, $to);
        $rows = array();
        $extra_cols = 0;
        foreach($deliveries as $d)
        {
            $address_string = $d['client_name'];
            if(!empty($d['attention'])) $address_string .= "\n".$d['attention'];
            if(!empty($d['address'])) $address_string .= "\n".$d['address'];
            if(!empty($d['address_2'])) $address_string .= "\n".$d['address_2'];
            if(!empty($d['suburb'])) $address_string .= "\n".$d['suburb'];
            if(!empty($d['state'])) $address_string .= "\n".$d['state'];
            if(!empty($d['country'])) $address_string .= "\n".$d['country'];
            if(!empty($d['postcode'])) $address_string .= "\n".$d['postcode'];
            $items = explode("~",$d['items']);
            $pallet_count = 0;
            $row = [
                $d['delivery_number']."\n".$d['client_reference'],
                date('D d/m/Y - g:i A', $d['date_entered']),
                date('D d/m/Y - g:i A', $d['date_fulfilled']),
                $address_string,
                $d['delivery_window'],
                $d['vehicle_type'],
                $d['charge_level']
            ];
            if(Session::getUserRole() != "client")
            {
                $row[] =  "$".number_format($d['shipping_charge'], 2, '.', ',');
                $row[] =  "$".number_format($d['gst'], 2, '.', ',');
                $row[] =  "$".number_format($d['total_charge'], 2, '.', ',');
            }
            $extra_cols = max($extra_cols, count($items));
            $i = 1;
            foreach($items as $item)
            {
                list($item_id, $item_name, $item_sku, $item_qty, $location_id) = explode("|",$item);
                $row[] = $item_name. "(".$item_sku.")";
                $row[] = $item_qty;
                ++$i;
            }
            $rows[] = $row;
        }
        $i = 1;
        while($i <= $extra_cols)
        {
            $cols[] = "Item $i Name";
            $cols[] = "Item $i Qty";
            ++$i;
        }

        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "deliveries_report_".date("Ymd")]);
    }

    public function clientInventoryCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $items = $this->item->getClientInventoryForCSV($client_id);
        $cols = array(
            "Name",
            "SKU",
            "Client Product ID",
            "Barcode",
            "On Hand",
            "Allocated",
            "Damaged/Unsuitable",
            "Available",
            "Pallet Bays Used"
        );
        $rows = array();
        foreach($items as $i)
        {
            $row = array(
                $i['item_name'],
                $i['sku'],
                $i['client_product_id'],
                $i['barcode'],
                $i['on_hand'],
                $i['allocated'],
                $i['qc_count'],
                $i['available'],
                $i['bays']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "inventory_".date("Ymd")]);
    }

    public function clientBaysUsageCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $bays = $this->location->getClientsBaysUsage($client_id);
        $client_name = strtolower(str_replace(" ", "_",$this->client->getClientName($client_id)));
        $cols = array(
            "Location Name",
            "Oversize",
            "Pick Face"
        );
        $rows = array();
        foreach($bays as $b)
        {

            $row = array(
                $b['location'],
                $b['oversize'],
                $b['tray']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => $client_name."_bay_usage"]);
    }

    public function downloadFile()
    {
        $file_name = (isset($this->request->params['args']['file']))? $this->request->params['args']['file']: "";
        $fullPath = APP . "files/" . $file_name;

        //die($fullPath);

        $extension = explode(".", $file_name);
        $ext = end($extension);

        if(!Uploader::isFileExists($fullPath))
        {
            return $this->error(404);
        }

        $this->response->download($fullPath, ["basename" => $file_name, "extension" => $ext]);
    }

    public function solarInventoryCSV()
    {
        $products = $this->item->getItemsForClient($this->client->solar_client_id);
        $cols = array(
            "Name",
            "SKU",
            "Supplier",
            "Owner",
            "On Hand",
            "Allocated",
            "Under QC",
            "Available"
        );
        $rows = array();
        foreach($products as $p)
        {
            $onhand = $this->item->getStockOnHand($p['id']);
            $allocated = $this->item->getAllocatedStock($p['id'], $this->order->fulfilled_id);
            $underqc = $this->item->getStockUnderQC($p['id']);
            $available = $onhand - $allocated - $underqc;
            $owner = ($p['solar_type_id'] > 0)?$this->solarordertype->getSolarOrderType($p['solar_type_id']): "";

            $row = array(
                $p['name'],
                $p['sku'],
                $p['supplier'],
                $owner,
                $onhand,
                $allocated,
                $underqc,
                $available
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "solar_inventory".date("Ymd")]);
    }

    public function solarConsumablesReorderCSV()
    {
        $products = $this->item->getSolarConsumablesReordering();
        $cols = array(
            "Name",
            "SKU",
            "Currently Available",
            "Minimum Reorder Amount"
        );
        $rows = array();
        foreach($products as $p)
        {
            $row = array(
                $p['name'],
                $p['sku'],
                $p['currently_available'],
                $p['minimum_reorder_amount']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "solar_consumables_reorder".date("Ymd")]);
    }

    public function dispatchReportCSV()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
        $cols = array(
            "Date Ordered",
            "Entered By",
            "Date Dispatched",
            "Order Number",
            "Client Order Number",
            "Shipped To",
            'Country',
            'Charge Code',
            'Handling Charge',
            'Postage Charge',
            'GST',
            'Total Charge',
            'Weight',
            'Shrink Wrap',
            'Bubble Wrap',
            'Pallets',
            "Courier",
            "Consignment ID",
            "Cartons",
            "Comments",
            "Dispatching",
            "Packing",
            "Total Items",
            "Store Order"
        );

        $rows = array();
        $extra_cols = 0;
        foreach($orders as $o)
        {
            $weight = 0;
            foreach($o['parcels'] as $parc)
            {
                $weight += $parc['weight'];
            }
            $row = array(
                $o['date_ordered'],
                $o['entered_by'],
                $o['date_fulfilled'],
                $o['order_number'],
                $o['client_order_number'],
                str_replace("<br/>", ", ",$o['shipped_to']),
                $o['country'],
                $o['charge_code'],
                $o['handling_charge'],
                $o['postage_charge'],
                $o['gst'],
                $o['total_charge'],
                $weight,
                $o['shrink_wrap'],
                $o['bubble_wrap'],
                $o['pallets'],
                $o['courier'],
                $o['consignment_id'],
                $o['cartons'],
                $o['comments'],
                strip_tags($o['dispatched_by']),
                strip_tags($o['packed_by']),
                $o['total_items'],
                $o['store_order']
            );
            $extra_cols = max($extra_cols, count($o['csv_items']));
            $i = 1;
            foreach($o['csv_items'] as $array)
            {
                $row[] = $array['name'];
                $row[] = $array['qty'];
                $row[] = $array['pallet'];
                ++$i;
            }
            $rows[] = $row;
        }
        $i = 1;
        while($i <= $extra_cols)
        {
            $cols[] = "Item $i Name";
            $cols[] = "Item $i Qty";
            $cols[] = "Item $i Pallet";
            ++$i;
        }

        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "dispatch_report_".$extra_cols]);
    }

    public function cometCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }

        $cols = array(
            "3PL Order Number",
            "Date",
            "Service Code",
            "DEL/PUP",
            "Customer Name",
            "Customer Address",
            "Customer Address 2",
            "Customer Suburb",
            "Customer Postcode",
            "Customer Phone"
        );

        $rows = array();
        $extra_cols = 0;
        foreach($this->request->data['order_ids'] as $order_id)
        {
            if( $od = $this->order->getOrderDetail($order_id) )
            {
                $name = empty($od['company_name'])? $od['ship_to']: $od['company_name'];
                $phone = preg_replace("/\+61/","0",$od['contact_phone']);
                $phone = preg_replace("/[^\d]/","",$phone);
                $row = array(
                    $od['order_number'],
                    date("d/m/Y"),
                    "HM2",
                    "DEL",
                    $name,
                    $od['address'],
                    $od['address_2'],
                    $od['suburb'],
                    $od['postcode'],
                    $phone
                );
                $items = $this->order->getItemsForOrderNoLocations($order_id);
                $extra_cols = max($extra_cols, count($items));
                $i = 1;
                foreach($items as $item)
                {
                    $row[] = $item['name'];
                    $row[] = $item['sku'];
                    $row[] = $item['qty'];
                    ++$i;
                }
                $rows[] = $row;
            }
        }
        $i = 1;
        while($i <= $extra_cols)
        {
            $cols[] = "Item $i Name";
            $cols[] = "Item $i SKU";
            $cols[] = "Item $i Qty";
            ++$i;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "comet_export".date("Ymd")]);
    }

    public function orderAuspostExportCSV()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        //$client_info = $this->client->getClientInfo($client_id);
        $cols = array(
            "Row type",
            "Sender account",
            "Payer account",
            "Recipient contact name",
            "Recipient business name",
            "Recipient address line 1",
            "Recipient address line 2",
            "Recipient address line 3",
            "Recipient suburb",
            "Recipient state",
            "Recipient postcode",
            "Send tracking email to recipient",
            "Recipient email address",
            "Recipient phone number",
            "Delivery/special instruction 1",
            "Special instruction 2",
            "Special instruction 3",
            "Sender reference 1 ",
            "Sender reference 2",
            "Product id",
            "Authority to leave",
            "Safe drop ",
            "Quantity",
            "Packaging type",
            "Weight",
            "Length",
            "Width",
            "Height",
            "Parcel contents",
            "Transit cover value"
        );
        $rows = array();
        foreach($this->request->data['order_ids'] as $order_id)
        {
            if( $od = $this->order->getOrderDetail($order_id) )
            {
                $ci = $this->client->getClientInfo($od['client_id']);
                $name = empty($od['company_name'])? $od['ship_to']: $od['company_name'];
                $phone = preg_replace("/\+61/","0",$od['contact_phone']);
                $phone = preg_replace("/[^\d]/","",$phone);
                $ste = (!empty($od['tracking_email']))? "YES":"NO";
                $e = (!empty($od['tracking_email']))? $od['tracking_email']:"";
                $items = $this->order->getItemsForOrder($order_id);
                $packages = $this->order->getPackagesForOrder($order_id);
                //$products = $this->getItemsCountForOrder($co['id']);
                $pcode = ($od['eparcel_express'] == 1)? "7I85":"7C85";
                $atl = ($od['signature_req'] == 1)? "NO":"YES";


                $parcels = Packaging::getPackingForOrder($od,$items,$packages);
                /*    */
                foreach($parcels as $i)
                {
                    $weight = ceil($i['pieces'] * $i['weight']);
                    $cubic = round($i['width'] * $i['depth'] * $i['height'] * $i['pieces'] / 1000000, 3);
                    $row = array(
                        "S",
                        "",
                        "",
                        $od['ship_to'],
                        "",
                        str_replace(",", " ",$od['address']),
                        str_replace(",", " ",$od['address_2']),
                        "",
                        $od['suburb'],
                        $od['state'],
                        $od['postcode'],
                        $ste,
                        $e,
                        $od['contact_phone'],
                        "",
                        "",
                        "",
                        $od['ref_1'],
                        $od['order_number'],
                        $pcode,
                        $atl,
                        "",
                        "",
                        "",
                        $i['weight'],
                        "",
                        "",
                        "",
                        "",
                        ""
                    );
                    $rows[] = $row;
                }

            }

        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "Eparcel_csv".date("Ymd")]);
    }

    public function orderExportCSV()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        //$client_info = $this->client->getClientInfo($client_id);
        $cols = array(
            "ConID",
            "Receiver Name",
            "Receiver Address1",
            "Receiver Address2",
            "Receiver City",
            "Receiver State",
            "Receiver Postcode",
            "Customer Reference",
            "Special Instruction",
            "Line Reference",
            "Package Description",
            "Item Count",
            "Weight",
            "Length",
            "Width",
            "Height",
            "Qty",
            "Cubic",
            "Receiver Contact Name",
            "Receiver Contact Email",
            "Receiver Contact Mobile",
            "ATL",
            "Dangerous Goods",
            "End of Record"
        );
        $rows = array();
        foreach($this->request->data['order_ids'] as $order_id)
        {
            if( $od = $this->order->getOrderDetail($order_id) )
            {
                $ci = $this->client->getClientInfo($od['client_id']);
                $name = empty($od['company_name'])? $od['ship_to']: $od['company_name'];
                $phone = preg_replace("/\+61/","0",$od['contact_phone']);
                $phone = preg_replace("/[^\d]/","",$phone);

                $items = $this->order->getItemsForOrder($order_id);
                $packages = $this->order->getPackagesForOrder($order_id);
                //$products = $this->getItemsCountForOrder($co['id']);

                $parcels = Packaging::getPackingForOrder($od,$items,$packages);

                foreach($parcels as $i)
                {
                    $weight = ceil($i['pieces'] * $i['weight']);
                    $cubic = round($i['width'] * $i['depth'] * $i['height'] * $i['pieces'] / 1000000, 3);
                    $row = array(
                        "",
                        $name,
                        str_replace(",", " ",$od['address']),
                        str_replace(",", " ",$od['address_2']),
                        $od['suburb'],
                        $od['state'],
                        $od['postcode'],
                        "|".$ci['ref_1']."|".$od['order_number'],
                        "",
                        $od['client_order_id'],
                        "CARTON",
                        $i['pieces'],
                        $weight,
                        ceil($i['width']),
                        ceil($i['depth']),
                        ceil($i['height']),
                        $i['pieces'],
                        $cubic,
                        $od['ship_to'],
                        $od['tracking_email'],
                        $phone,
                        "N",
                        "N",
                        "EOR"
                    );
                    $rows[] = $row;
                }
            }

        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "order_export"], false);
    }

    public function inventoryReportCSV()
    {
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $products = $this->item->getClientInventoryArray( $client_id );
        $cols = array(
            "Name",
            "SKU",
            "Total On Hand",
            "Currently Allocated",
            "Under Quality Control",
            "Total Available"
        );

        $rows = array();
        $extra_cols = 0;
        foreach($products as $p)
        {
            $available = $p['onhand'] - $p['qc_count'] - $p['allocated'];
            $row = array(
                $p['name'],
                $p['sku'],
                $p['onhand'],
                $p['allocated'],
                $p['qc_count'],
                $available
            );
            $extra_cols = max($extra_cols, count($p['locations']));
            $i = 1;
            foreach($p['locations'] as $array)
            {
                $row[] = $array['name'];
                $row[] = $array['onhand'];
                $row[] = $array['allocated'];
                $row[] = $array['qc_count'];
                ++$i;
            }
            $rows[] = $row;
        }
        $i = 1;
        while($i <= $extra_cols)
        {
            $cols[] = "Location $i Name";
            $cols[] = "Location $i On Hand";
            $cols[] = "Location $i Allocated";
            $cols[] = "Location $i Under Quality Control";
            ++$i;
        }

        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "inventory_report"]);
    }

    public function clientOrdersCSV()
    {
        $client_id = Session::getUserClientId();
        $db = Database::openConnection();
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $orders = $this->order->getOrdersForClient($client_id, $from, $to);
        $cols = array(
            "Date Ordered",
            "Entered By",
            "Date Dispatched",
            "WMS Order Number",
            "Your Order Number",
            "Shipped To",
            "Items",
            "total Items",
            "Courier",
            "Charge Code",
            "Consignment ID"
        );
        $rows = array();
        foreach($orders as $o)
        {
            $ad = array(
                'address'   =>  $o['address'],
                'address_2' =>  $o['address_2'],
                'suburb'    =>  $o['suburb'],
                'state'     =>  $o['state'],
                'postcode'  =>  $o['postcode'],
                'country'   =>  $o['country']
            );
            $date_ordered = date("d/m/Y", $o['date_ordered']);
            $eb = $db->queryValue('users', array('id' => $o['entered_by']), 'name');
            if(empty($eb))
            {
                $eb = "Automatically Imported";
            }
            $date_despatched = ($o['date_fulfilled'] > 0)? date("d/m/Y", $o['date_fulfilled']) : "Not Yet Dispatched";
            $address = Utility::formatAddressCSV($ad);
            $shipped_to = "";
            if(!empty($o['company_name'])) $shipped_to .= $o['company_name']."<br/>";
            if(!empty($o['ship_to'])) $shipped_to .= $o['ship_to']."<br/>";
            $shipped_to .= $address;
            $num_items = 0;
            $items = "";
            $products = $this->order->getItemsCountForOrder($o['id']);
            foreach($products as $p)
            {
                $items .= $p['name']." (".$p['qty']."),<br/>";
                $num_items += $p['qty'];
                $pallet = ($p['palletized'] && $p['qty'] == $p['per_pallet'])? 1 : "";
            }
            if($o['courier_id'] > 0)
            {
                $courier_name = $db->queryValue('couriers', array('id' => $o['courier_id']), 'name');
                if($courier_name == "Local")
                {
                    $courier_name = $o['courier_name'];
                }
            }
            else
            {
                $courier_name = "Not Yet Assigned";
            }


            $row = array(
                $date_ordered,
                $eb,
                $date_despatched,
                $o['order_number'],
                $o['client_order_id'],
                str_replace("<br/>", ", ",$shipped_to),
                str_replace("<br/>", "",$items),
                $num_items,
                $courier_name,
                $o['charge_code'],
                $o['consignment_id']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "orders"]);
    }

    public function clientDispatchReportCSV()
    {
        $client_id = Session::getUserClientId();
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        //$hidden = Config::get("HIDE_CHARGE_CLIENTS");
        $orders = $this->order->getDispatchedOrdersArray($from, $to, $client_id);
        $cols = array(
            "Date Ordered",
            "Entered By",
            "Date Dispatched",
            "WMS Order Number",
            "Your Order Number",
            "Shipped To",
            "Items",
            "total Items",
            "Courier",
            "Charge Code",
            "Consignment ID"
        );
        $rows = array();
        foreach($orders as $o)
        {
            $row = array(
                $o['date_ordered'],
                $o['entered_by'],
                $o['date_fulfilled'],
                $o['order_number'],
                $o['client_order_number'],
                str_replace("<br/>", ", ",$o['shipped_to']),
                str_replace("<br/>", "",$o['items']),
                $o['total_items'],
                $o['courier'],
                $o['charge_code'],
                $o['consignment_id']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "dispatch_report"]);
    }

    public function clientStockMovementCSV()
    {
        $client_id = Session::getUserClientId();
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $exc = array($this->stockmovementlabels->getLabelId('Internal Stock Movement'));
        $movements = $this->itemmovement->getItemMovementsArray($client_id, $from, $to, $exc);
        $cols = array(
            "Date",
            "Order Number/Reference",
            "Your Order Number",
            "Deliver To",
            "SKU",
            "Product",
            "Number In",
            "Number Out",
            "Reason"
        );
        $rows = array();
        foreach($movements as $m)
        {
            $row = array(
                $m['date'],
                $m['order_number'],
                $m['client_order_id'],
                $m['address'],
                $m['sku'],
                $m['name'],
                $m['qty_in'],
                $m['qty_out'],
                $m['reason']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "product_movement_report"]);
    }

    public function stockMovementCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $movements = $this->itemmovement->getItemMovementsArray($client_id, $from, $to);
        $cols = array(
            "Date",
            "SKU",
            "Product",
            "Number In",
            "Number Out",
            "Reason",
            "Reference/FSG Order Number",
            "Client Order Number",
            "Location",
            "Entered By"
        );
        $rows = array();
        foreach($movements as $m)
        {
            $row = array(
                $m['date'],
                $m['sku'],
                $m['name'],
                $m['qty_in'],
                $m['qty_out'],
                $m['reason'],
                $m['order_number'],
                $m['client_order_id'],
                $m['location'],
                $m['entered_by']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "stock_movement_report"]);
    }

    public function clientStockSummaryCSV()
    {
        $client_id = Session::getUserClientId();
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $exc = array($this->stockmovementlabels->getLabelId('Internal Stock Movement'));
        $movements = $this->itemmovement->getItemMovementsSummaryArray($client_id, $from, $to, $exc);
        $cols = array(
            "Name",
            "SKU",
            "Total In",
            "Total Out",
            "Currently On Hand"
        );
        $rows = array();
        foreach($movements as $m)
        {
            $row = array(
                $m['name'],
                $m['sku'],
                $m['total_in'],
                $m['total_out'],
                $m['on_hand']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "product_movement_summary"]);
    }

    public function returnsReportCSV()
    {
        $client_id = Session::getUserClientId();
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $returns = $this->orderreturn->getReturnedOrdersArray($from, $to, $client_id);
        $cols = array(
            "Date Returned",
            "Item",
            "Number Returned",
            "WMS Order Number",
            "Your Order Number",
            "Reason",
            "Entered By"
        );
        $rows = array();
        foreach($returns as $o)
        {
            $item_name = $o['item_name'];
            if(!empty($o['client_product_id']))
                $item_name .= " [".$o['client_product_id']."]";
            elseif(!empty($o['sku']))
                $item_name .= " [".$o['sku']."]";
            $row = array(
                $o['return_date'],
                $item_name,
                $o['qty_returned'],
                $o['order_number'],
                $o['client_order_number'],
                $o['reason'],
                $o['entered_by']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "returns_report"]);
    }

    public function goodsInReportCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $goodsin = $this->inwardsgoods->getInwardsGoodsArray($from, $to);
        $cols = array(
            "Date",
            "Client",
            "Pallets",
            "Cartons",
            "Entered By"
        );
        $rows = array();
        foreach($goodsin as $gi)
        {
            $row = array(
                $gi['date'],
                $gi['client_name'],
                $gi['pallets'],
                $gi['cartons'],
                $gi['entered_by']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "goodsin_report"]);
    }

    public function goodsinSummaryCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $summary = $this->inwardsgoods->getSummaryArray($from, $to);
        $cols = array(
            "Client",
            "Pallets",
            "Cartons"
        );
        $rows = array();
        foreach($summary as $s)
        {
            $row = array(
                $s['client_name'],
                $s['pallets'],
                $s['cartons']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "goodsin_summary"]);
    }

    public function goodsoutSummaryCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $summary = $this->outwardsgoods->getSummaryArray($from, $to);
        $cols = array(
            "Client",
            "Pallets",
            "Cartons",
            "Satchels"
        );
        $rows = array();
        foreach($summary as $s)
        {
            $row = array(
                $s['client_name'],
                $s['pallets'],
                $s['cartons'],
                $s['satchels']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "goodsout_summary"]);
    }

    public function unloadedContainersCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $unloaded_containers = $this->unloadedcontainer->getUnloadedContainersArray($from, $to);
        $cols = array(
            "Date",
            "Client",
            "Container Size",
            "Load Type",
            "Item Count",
            "Charge",
            "Entered By"
        );
        $rows = array();
        foreach($unloaded_containers as $uc)
        {
            $row = array(
                $uc['date'],
                $uc['client_name'],
                $uc['container_size'],
                $uc['load_type'],
                $uc['item_count'],
                $uc['charge'],
                $uc['entered_by']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "unloaded_containers"]);
    }

    public function truckRunSheetCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $runs = $this->truckusage->getRunSheetArray($from, $to);
        $cols = array(
            "Date",
            "Client",
            "Order Number",
            "Suburb",
            "Charge",
            "Entered By"
        );
        $rows = array();
        foreach($runs as $r)
        {
            $row = array(
                $r['date'],
                $r['client_name'],
                $r['order_number'],
                $r['suburb'],
                $r['charge'],
                $r['entered_by']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "truck_runsheet"]);
    }

    public function goodsOutReportCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $goodsout = $this->outwardsgoods->getOutwardsGoodsArray($from, $to);
        $cols = array(
            "Date",
            "Client",
            "Pallets",
            "Cartons",
            "Satchels",
            "Entered By"
        );
        $rows = array();
        foreach($goodsout as $gi)
        {
            $row = array(
                $gi['date'],
                $gi['client_name'],
                $gi['pallets'],
                $gi['cartons'],
                $gi['satchels'],
                $gi['entered_by']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "goodsout_report"]);
    }

    public function stockAtDateCSV()
    {
        $client_id = Session::getUserClientId();
        //echo "<pre>",print_r($this->request),"</pre>"; die();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $stock = $this->itemmovement->getStockAtDateArray($client_id, $date);
        $cols = array(
            "Name",
            "SKU",
            "Client Product ID",
            "Stock On Hand At ".date("d/m/Y", $date)
        );
        $rows = array();
        foreach($stock as $i)
        {
            $row = array(
                $i['name'],
                $i['sku'],
                $i['client_product_id'],
                $i['on_hand']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "stock_at_date_".date("Ymd", $date)]);
    }

    public function locationreportCSV()
    {
        $locations = $this->location->getLocationUsage();
        $cols = array(
            "Location",
            "Oversize",
            "Client",
            "Item",
            "SKU",
            "Count"
        );
        $rows = array();
        foreach($locations as $l)
        {
            $os = ($l['oversize'] > 0)? "Yes":"No";
            $row = array(
                $l['location'],
                $os,
                $l['client_name'],
                $l['name'],
                $l['sku'],
                $l['qty']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "location_report"]);
    }

    public function emptyBaysCSV()
    {
        $locations = $this->location->getEmptyLocations();
        $cols = array(
            "Location"
        );
        $rows = array();
        foreach($locations as $l)
        {
            $row = array(
                $l['location']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "empty_bays"]);
    }

    public function clientBayUsageCSV()
    {
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
            }
        }
        $usage = $this->location->getAllClientsBayUsage();
        $cols = array(
            "Client",
            "Standard Bays",
            "Oversize Bays"
        );
        $rows = array();
        foreach($usage as $cu)
        {
            $row = array(
                $cu['client_name'],
                $cu['location_count'],
                $cu['oversize_count']
            );
            $rows[] = $row;
        }
        $expire=time()+60;
        setcookie("fileDownload", "true", $expire, "/");
        $this->response->csv(["cols" => $cols, "rows" => $rows], ["filename" => "client_bay_usage"]);
    }

    public function printLocationBarcodes()
    {
        $pdf = new Mympdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 12,
            'margin_bottom' => 12
        ]);
        $locations  = $this->location->getAllLocations();
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/locations.php', [
            'locations'    =>  $locations
        ]);
        $stylesheet = file_get_contents(STYLES."barcodes.css");
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function usefulBarcodes()
    {
        $pdf = new Mympdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 5,
            'margin_bottom' => 5
        ]);
        $items  = $this->item->getItemsForClient(51);
        //echo "<pre>",print_r($items),"</pre>"; die();
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/usefulBarcodes.php', [
            'items'    =>  $items
        ]);
        $stylesheet = file_get_contents(STYLES."barcodes.css");
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function isAuthorized(){

        $role = Session::getUserRole();
        $action = $this->request->param('action');
        $resource = "downloads";
        //admin users
        Permission::allow(['super admin', 'admin'], $resource, ['*']);

        //warehouse users
        Permission::allow('warehouse', $resource, array(

        ));

        //client users
        Permission::allow('client', $resource, array(
            "clientDispatchReportCSV",
            "clientInventoryCSV",
            "clientOrdersCSV",
            "returnsReportCSV",
            "clientStockMovementCSV",
            "clientStockSummaryCSV",
            "stockAtDateCSV",
            "downloadFile"
        ));

        if(Session::isDeliveryClientUser())
        {
            Permission::allow('client', $resource,[
                "clientDeliveryClientSpaceUsageCSV",
                "deliveriesReportCSV",
                "pickupsReportCSV"
            ]);
        }
        else
        {
            Permission::allow('client', $resource,[
                "clientClientSpaceUsageCSV"
            ]);
        }

        //remove an allocation
        Permission::deny('admin', $resource, array(
            "printLocationBarcodes"
        ));

        return Permission::check($role, $resource, $action);
        return false;

    }
}
