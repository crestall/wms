<?php
/**
 * PDF controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
//use Mpdf\Mpdf;
class pdfController extends Controller
{

    public function beforeAction()
    {
        parent::beforeAction();
        $action = $this->request->param('action');
        $post_actions = array(
            'printRunsheet'
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

    public function printVicLocalLabels()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $pdf = new Mympdf([
            'mode'          => 'utf-8',
            'format'        => [148,105],
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 5,
            'margin_bottom' => 5
        ]);
        $order_ids  = $this->request->data['orders'];
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/viclocallabels.php', [
            'orders_ids'    =>  $order_ids
        ]);
        //$stylesheet = file_get_contents(STYLES."local_sticker.css");
        //$pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printCometLocalLabels()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        $pdf = new Mympdf([
            'mode'          => 'utf-8',
            'format'        => [148,105],
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_top'    => 5,
            'margin_bottom' => 5
        ]);
        $order_ids  = $this->request->data['orders'];
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/cometlocallabels.php', [
            'orders_ids'    =>  $order_ids
        ]);
        //$stylesheet = file_get_contents(STYLES."local_sticker.css");
        //$pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($html, 2);
        $pdf->Output();
    }

    public function printHuntersLabels()
    {
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => [148,105]]);
        $order_ids  = $this->request->data['orders'];
        foreach($order_ids as $order_id)
        {
            $od = $this->order->getOrderDetail($order_id);
            $base64pdf = $od['hunters_label'];
            file_put_contents(BASE_DIR."/_tmp/label_{$order_id}.pdf", base64_decode($base64pdf));
            $pdfs[] = array(
                'file'          =>  BASE_DIR."/_tmp/label_{$order_id}.pdf",
                'orientation'   =>  "P"
            );
            $this->order->updateStatus($this->order->packed_id, $order_id);
        }
        $today = date("Ymd", time());
        $pdf->mergePDFFiles($pdfs, "hunters_labels_".$today.".pdf");
        foreach($pdfs as $pdf)
        {
            unlink($pdf['file']);
        }
    }

    public function printRunsheet()
    {
        //echo "<pre>",print_r($this->request),"</pre>";die();
        // set up the data for the pdf
        if(empty($this->request->data))
            return $this->error(400);
        $post_data = $this->request->data;
        $driver = ($post_data['driver_id'] > 0)? $this->driver->getDriverName($post_data['driver_id']) : "";
        $table_body = "";
        if(count($post_data['tasks']['jobs']))
        {
            foreach($post_data['tasks']['jobs'] as $job_id => $on)
            {
                $job = $this->productionjob->getJobById($job_id);
                echo "<pre>",print_r($job),"</pre>"; continue;
                $address_string = "";
                if(!empty($job['job_address2']))
                    $address_string .= "<br>".$job['job_address2'];
                $address_string .= "<br>".$job['job_suburb'];
                $address_string .= "<br>".$job['job_postcode'];
                $table_body .= "
                  <tr>
                    <td>{$job['job_id']}</td>
                    <td>{$job['customer_name']}</td>
                    <td>{$job['description']}</td>
                    <td>$address_string</td>
                    <td>{$post_data['units']}</td>
                    <td>".ucwords($job['salesrep_name'])."</td>
                    <td></td>
                    <td></td>
                  </tr>
                ";
            }
        }
        die();
        $pdf = new Mympdf(['mode' => 'utf-8', 'format' => 'A4', 'orientation' => 'L']);
        $pdf->SetDisplayMode('fullpage');
        $html = $this->view->render(Config::get('VIEWS_PATH') . 'pdf/runsheet.php', [
            'driver'        => $driver,
            'table_body'    => $table_body
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