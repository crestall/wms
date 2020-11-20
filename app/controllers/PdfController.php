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
                if($task['order_id'] > 0)
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
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/pickslip.php', [
            'orders_ids'    =>  $order_ids
        ]);
        $stylesheet = file_get_contents(STYLES."pickslip.css");
        $pdf->SetWatermarkText('REPLACEMENT');
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



    public function isAuthorized(){
        return true;
    }
}
?>