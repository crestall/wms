<?php
/**
 * PDF controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
//use Mpdf\Mpdf;
class pdfController extends Controller
{

    public function beforeAction()
    {
        parent::beforeAction();
        $action = $this->request->param('action');
        $post_actions = array(
            "createDeliveryDocket",
            "createDeliveryLabels"
        );
        $this->Security->requirePost($post_actions);
        if(in_array($action, $post_actions))
        {
            $this->Security->config("form", [ 'fields' => ['csrf_token']]);
        }
        else
        {
           $this->Security->config("validateForm", false);
        }
    }

    public function createDeliveryLabels()
    {
        //echo "REQUEST DATA<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "POSTDATA<pre>",print_r($post_data),"</pre>"; die();
        FormValidator::validateAddress($address, $suburb, $state, $postcode, 'AU', isset($ignore_address_error));
        if(!FormValidator::dataSubbed($ship_to))
        {
            Form::setError('ship_to', "A Deliver To Name is required");
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            return $this->redirector->to(PUBLIC_ROOT."jobs/create-delivery-docket/job=$job_id");
        }
        else
        {
            //gonna make the pdf
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $sender_details = $this->deliverydocketsender->getSenderById($post_data['sender_id']);
            $pdf = new Mympdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'orientation'   => 'P',
                'margin_left'   => 0,
                'margin_right'  => 0,
                'margin_top'    => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);
            $pdf->SetDisplayMode('fullpage');
            $pdf->adjustFontDescLineheight = 1.5;
            $template = $sender_details['template_file'];
            $css_file = $sender_details['template_css'];
            $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/'.$template, [
                'sender_details'    => $sender_details,
                'dl_details'        => $post_data
            ]);
            $stylesheet = file_get_contents(STYLES.$css_file);
            $pdf->WriteHTML($stylesheet,1);
            $pdf->WriteHTML($html, 2);
            $pdf->Output();
        }
    }

    public function printDeliveryDockets()
    {
        //echo "REQUEST DATA<pre>",print_r($this->request),"</pre>"; die();
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'P']);
        $pdf->SetDisplayMode('fullpage');
        $delivery_ids  = $this->request->data['delivery_ids'];
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/printdeliverydocket.php', [
            'delivery_ids'  => $delivery_ids
        ]);
        //die($html);
        $stylesheet = file_get_contents(STYLES."deliverydoket.css");
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printPickupDocket()
    {
        //echo "REQUEST DATA<pre>",print_r($this->request),"</pre>"; //die();
        if(!isset($this->request->params['args']['pickup']))
        {
            //no pickup id supplied
            (new SiteErrorsController())->siteError("noPickupId")->send();
            return;
        }
        $pickup_id = $this->request->params['args']['pickup'];
        $vehicle = false;
        if(isset($this->request->params['args']['vehicle']))
        {
            $vehicle = $this->request->params['args']['vehicle'];
            //echo "<p>Will update vehicle to $vehicle for pickup id $pickup_id</p>";  die();
            $this->pickup->updateFieldValue('vehicle_type', $vehicle, $pickup_id);
        }
        $this->pickup->markPickupVehicleAssigned($pickup_id);
        if($vehicle == "client_supplied")
            die("<h2>No Pickup Docket Required</h2><p>You can close this window</p>");
        $pickup = $this->pickup->getPickupDetails($pickup_id);
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetDisplayMode('fullpage');
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/pickupdocket.php', [
            'pickup'    =>  $pickup
        ]);
        $stylesheet = file_get_contents(STYLES."pickupdocket.css");
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printDeliveryDocket()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        if(!isset($this->request->params['args']['delivery']))
        {
            //no pickup id supplied
            (new SiteErrorsController())->siteError("noDeliveryId")->send();
            return;
        }
        $delivery_id = $this->request->params['args']['delivery'];
        $vehicle = false;
        if(isset($this->request->params['args']['vehicle']))
        {
            $vehicle = $this->request->params['args']['vehicle'];
            //echo "<p>Will update vehicle to $vehicle for pickup id $pickup_id</p>";
            $this->delivery->updateFieldValue('vehicle_type', $vehicle, $delivery_id);
        }
        $this->delivery->markDeliveryVehicleAssigned($delivery_id);
        if($vehicle == "client_supplied")
            die("<h2>No Delivery Docket Required</h2><p>You can close this window</p>");
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetDisplayMode('fullpage');
        $delivery_ids[]  = $this->request->params['args']['delivery'];
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/printdeliverydocket.php', [
            'delivery_ids'    =>  $delivery_ids
        ]);
        //echo $html;die();
        //$this->delivery->markDeliveryOnboard($this->request->params['args']['delivery']);
        $stylesheet = file_get_contents(STYLES."deliverydoket.css");
        //$pdf->SetWatermarkText('REPLACEMENT');
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function createDeliveryDocket()
    {
        //echo "REQUEST DATA<pre>",print_r($this->request->data),"</pre>"; //die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
            else
            {
                foreach($value as $key => $avalue)
                {
                    $post_data[$field][$key] = $avalue;
                    ${$field}[$key] = $avalue;
                }
            }
        }
        //echo "POSTDATA<pre>",print_r($post_data),"</pre>"; die();
        FormValidator::validateAddress($address, $suburb, $state, $postcode, 'AU', isset($ignore_address_error));
        if(!FormValidator::dataSubbed($ship_to))
        {
            Form::setError('ship_to', "A Deliver To Name is required");
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            //return $this->redirector->to(PUBLIC_ROOT."jobs/create-delivery-docket/job=$job_id");
            return $this->redirector->to(PUBLIC_ROOT."orders/create-delivery-docket/order=$order_id"); 
        }
        else
        {
            //gonna make the pdf
            //echo "ALL GOOD<pre>",print_r($post_data),"</pre>"; die();
            $sender_details = $this->deliverydocketsender->getSenderById($post_data['sender_id']);

            $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'P']);
            $pdf->SetDisplayMode('fullpage');
            $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/deliverydocket.php', [
                'sender_details'    => $sender_details,
                'dd_details'        => $post_data
            ]);
            $stylesheet = file_get_contents(STYLES."deliverydoket.css");
            $pdf->WriteHTML($stylesheet,1);
            $pdf->WriteHTML($html, 2);
            $pdf->Output();

        }
    }

    public function printRunsheet()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        // set up the data for the pdf
        $data = array();
        if(empty($this->request->data))
            return $this->error(400);
        $rss = $this->runsheet->getRunsheetForPrinting($this->request->data['runsheet_id'], $this->request->data['driver_id']);
        $runsheet = Utility::createPrintRunsheetArray($rss);
        //echo "<pre>",print_r($runsheet),"</pre>";die();
        $driver = $runsheet['driver_name'];
        $runsheet_day = $runsheet['runsheet_day'];
        $runsheet_id = $runsheet['runsheet_id'];
        $table_body = "";
        if(isset($runsheet['tasks']))
        {
            foreach($runsheet['tasks'] as $task)
            {
                if($task['order_id'] == 0 &&  $task['job_id'] == 0)
                {
                    $delivery_id    = "";
                    $customer       = (empty($task['attention']))? $task['deliver_to'] : $task['attention'];
                    $description    = "";
                }
                elseif($task['order_id'] > 0)
                {
                    $delivery_id    = $task['order_number']." / ".$task['client_order_id'];
                    $customer       = $task['order_client_name'];
                    $description    = $task['order_description'];
                }
                else
                {
                    $delivery_id    = $task['job_number'];
                    $customer       = $task['customer_name'];
                    $description    = $task['job_description'];
                }

                $address_string = $task['deliver_to'];
                if(!empty($task['attention']))
                    $address_string .= "<br>".$task['attention'];
                $address_string .= "<br>".$task['address'];
                if(!empty($task['address_2']))
                    $address_string .= "<br>".$task['address_2'];
                $address_string .= "<br>".$task['suburb'];
                $address_string .= "<br>".$task['postcode'];
                if(!empty($task['delivery_instructions']))
                    $address_string .= "<br><br>".$task['delivery_instructions'];

                $table_body .= "
                  <tr>
                    <td>$delivery_id</td>
                    <td>$customer</td>
                    <td>$description</td>
                    <td>$address_string</td>
                    <td>{$task['units']}</td>
                    <td>{$task['fsg_contact']}</td>
                    <td></td>
                    <td></td>
                  </tr>
                ";
                $this->runsheet->runsheetPrinted(array(
                    'runsheet_id'   => $runsheet_id,
                    'task_id'       => $task['task_id']
                ));
            }
        }
        //die();

        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'L']);
        $pdf->SetDisplayMode('fullpage');
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/runsheet.php', [
            'driver'        => $driver,
            'table_body'    => $table_body,
            'runsheet_day'  => date("jS M, Y", $runsheet_day)
        ]);
        $stylesheet = file_get_contents(STYLES."runsheets.css");
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printPickslips()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetDisplayMode('fullpage');
        $order_ids  = $this->request->data['items'];
        ;
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/pickslip.php', [
            'orders_ids'    =>  $order_ids
        ]);
        $stylesheet = file_get_contents(STYLES."pickslip.css");
        $pdf->SetWatermarkText('REPLACEMENT');
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printDeliveryPickslips()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetDisplayMode('fullpage');
        $delivery_ids  = $this->request->data['delivery_ids'];
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/deliverypickslip.php', [
            'delivery_ids'    =>  $delivery_ids
        ]);
        //echo $html;die();
        $stylesheet = file_get_contents(STYLES."pickslip.css");
        //$pdf->SetWatermarkText('REPLACEMENT');
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printDeliveryPickslip()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetDisplayMode('fullpage');
        $delivery_ids[]  = $this->request->params['args']['delivery'];
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/deliverypickslip.php', [
            'delivery_ids'    =>  $delivery_ids
        ]);
        //echo $html;die();
        $stylesheet = file_get_contents(STYLES."pickslip.css");
        //$pdf->SetWatermarkText('REPLACEMENT');
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printJobsTable()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();

        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetDisplayMode('fullpage');
        $job_ids_string  = implode(",",$this->request->data['orderids']);
        $jobs = $this->productionjob->getJobsForPDF($job_ids_string);
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/printjobstable.php', [
            'jobs'    => $jobs
        ]);
        $stylesheet = file_get_contents(STYLES."jobstable.css");
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printInvoices()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $order_ids  = $this->request->data['orders'];
        $pdfs = array();
        foreach($order_ids as $id)
        {
            if($od = $this->order->getOrderDetail($id))
            {
                if(!empty($od['uploaded_file']))
                {
                    $pdfs[] = array(
                        'file'          => DOC_ROOT."/client_uploads/{$od['client_id']}/{$od['uploaded_file']}",
                        'orientation'	=>	'P'
                    );
                }
            }

        }
        //echo "<pre>",print_r($pdfs),"</pre>";die();
        $pdf = new Mympdf();
        $pdf->mergePDFFiles($pdfs, 'invoices.pdf');


    }

    public function printPackslips()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $order_ids  = $this->request->data['items'];
        $pdfs = array();
        foreach($order_ids as $order_id)
        {
            $order = $this->order->getOrderDetail($order_id);
            $client = $this->client->getClientInfo($order['client_id']);
            $order_items = $this->order->getItemsForOrder($order_id);

            $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
            $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/packingslip.php', [
                'od'        => $order,
                'items'     => $order_items,
                'client'    => $client
            ]);
            $stylesheet = file_get_contents(STYLES."packslip.css");
            $pdf->WriteHTML($stylesheet,1);
            $pdf->WriteHTML($html, 2);
            $pdf->Output(BASE_DIR."/_tmp/packslip_{$order_id}.pdf", 'F');

            $pdfs[] = array(
                'file'          =>  BASE_DIR."/_tmp/packslip_{$order_id}.pdf",
                'orientation'   =>  "P"
            );
        }
        $pdf2 = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf2->mergePDFFiles($pdfs, "packingslips.pdf");
        //die();
        foreach($pdfs as $pdf)
        {
            unlink($pdf['file']);
        }
    }

    public function packingSlip()
    {
        if(!isset($this->request->params['args']['order']))
        {
            return $this->error(404);
        }

        $order_id = $this->request->params['args']['order'];
        $order = $this->order->getOrderDetail($order_id);
        $client = $this->client->getClientInfo($order['client_id']);
        $order_items = $this->order->getItemsForOrder($order_id);

        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4']);
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/packingslip.php', [
            'od'        => $order,
            'items'     => $order_items,
            'client'    => $client
        ]);
        $stylesheet = file_get_contents(STYLES."packslip.css");
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function orderSummary()
    {
        if(!isset($this->request->params['args']['summary']))
        {
            return $this->error(404);
        }
        $summary = $this->eparcelorder->getSummary($this->request->params['args']['summary']);
        if(empty($summary))
        {
           return $this->error(404);
        }
        $this->eparcelorder->setAsPrinted($this->request->params['args']['summary']);
        $headers = [
            'Content-Disposition' => 'inline; filename=order_summary.pdf'
        ];
        $this->response->pdf($summary['order_summary'], $headers);
    }

    private function noPickupFound()
    {
        //render the error page
        Config::setJsConfig('curPage', "errors");
        Config::set('curPage', "errors");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('VIEWS_PATH') . 'errors/noPickupFound.php', []);
    }


    public function isAuthorized(){
        return true;
    }
}
?>