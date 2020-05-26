<?php

 /**
  * Email Class
  *
  * Sending emails via SMTP.
  * It uses PHPMailer library to send emails.
  *
  
  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

 class Email{

     /**
      * This is the constructor for Email object.
      *
      * @access private
      */
    private function __construct(){}


    public static function sendDailyReport($filenames, $client_id)
    {
        $db = Database::openConnection();
        $mail = new PHPMailer();
        $today = date('d/m/Y', time());
        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."dispatchreport.html");

        $cd = $db->queryByID('clients', $client_id);
        $replace_array = array("{NAME}");
		$replace_with_array = array($cd['contact_name']);
        $body = str_replace($replace_array, $replace_with_array, $body);
        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Warehouse Reports For $today";
		$mail->MsgHTML($body);
        foreach($filenames as $f)
        {
            $mail->AddAttachment($f);
        }

        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

        if(SITE_LIVE)
        //if(Config::get("SITE_LIVE"))
        {
            $mail->AddAddress($cd['billing_email'], $cd['contact_name']);
            if($client_id == 6)
            {
                $mail->AddAddress($cd['inventory_email'], $cd['inventory_contact']);
                $mail->AddAddress('kimberly@thebigbottleco.com', 'Kimberly Lacsa');
            }
            $mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');
        }
        else
        {
            $mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');
        }

        $mail->Send();
    }


    public static function sendClientSubmittedPickupNotifcations($post_data, $client_name, $name, $email)
    {
        $mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."cs_pickup_notification.html");

        $address_string = "<p>{$post_data['address']}<br/>";
        if(isset($post_data['address_2']))
            $address_string .= $post_data['address_2']."<br />";
        $address_string .= $post_data['suburb']."<br />";
        $address_string .= "VIC<br />";
        $address_string .= $post_data['postcode']."</p>";

        $puaddress_string = "<p>{$post_data['puaddress']}<br/>";
        if(isset($post_data['puaddress_2']))
            $puaddress_string .= $post_data['puaddress_2']."<br />";
        $puaddress_string .= $post_data['pusuburb']."<br />";
        $puaddress_string .= "VIC<br />";
        $puaddress_string .= $post_data['pupostcode']."</p>";

        $cartons = empty($post_data['cartons'])? 0 : $post_data['cartons'];
        $pallets = empty($post_data['pallets'])? 0 : $post_data['pallets'];

        $replace_array = array("{CLIENT_NAME}", "{PUADDRESS}", "{ADDRESS}", "{CARTONS}", "{PALLETS}");
		$replace_with_array = array($client_name, $puaddress_string, $address_string, $cartons, $pallets);
		$body = str_replace($replace_array, $replace_with_array, $body);

        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

        if(SITE_LIVE)
        //if(Config::get("SITE_LIVE"))
        {
		    $mail->AddAddress($email, $name);
            //$mail->AddBCC('daniel.mackenzie@3plplus.com.au', 'Daniel Mackenzie');
            //$mail->AddBCC('fred.scherzer@3plplus.com.au', 'Fred Scherzer');
            $mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');
        }
        else
        {
            $mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');
        }



		$mail->Subject = "Pickup Request From ".$client_name;

        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

		$mail->MsgHTML($body);

        return $mail->Send();
    }

     public static function sendPasswordReset($user_id, $name, $email, $password_token)
     {
        $mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."passwordreset.html");
        $replace_array = array("{LINK}", "{NAME}");
		$replace_with_array = array(Config::get('EMAIL_PASSWORD_RESET_URL') . "?id=" . urlencode(Encryption::encryptId($user_id)) . "&token=" . urlencode($password_token), $name);
		$body = str_replace($replace_array, $replace_with_array, $body);

        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

		$mail->AddAddress($email, $name);

		$mail->Subject = "Reset your password for 3PLPLUS WMS system";

        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

		$mail->MsgHTML($body);

        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent to ". $name);
        }
     }
    public static function sendBBInternationOrder($message, $header = "International Order", $subject = "Big Bottle International Order")
    {
        $mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."bbinternationalorder.html");
        $replace_array = array("{HEADER}", "{CONTENT}");
		$replace_with_array = array($message, $header);
		$body = str_replace($replace_array, $replace_with_array, $body);

        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

		$mail->AddAddress('teambbc@thebigbottleco.com', 'Team BBC');
        //$mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');

		$mail->AddBCC('customersupport@3plplus.com.au');

        $mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');

		$mail->Subject = $subject;

        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

		$mail->MsgHTML($body);

        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendOnePlateImportError($message)
    {
        $mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."oneplateimporterror.html");
        $replace_array = array("{CONTENT}");
		$replace_with_array = array($message);
		$body = str_replace($replace_array, $replace_with_array, $body);

        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');

        //$mail->AddAdress('Joshua Lanzarini','joshua@oneplate.co');

		$mail->Subject = "Order with item error for One Plate";

        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

		$mail->MsgHTML($body);

        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendTTImportError($message)
    {
        $mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."ttimporterror.html");
        $replace_array = array("{CONTENT}");
		$replace_with_array = array($message);
		$body = str_replace($replace_array, $replace_with_array, $body);

        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

		//$mail->AddAddress('chris.wilson@3plplus.com.au', 'Chris Wilson');
        $mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');

		//$mail->AddBCC('customersupport@3plplus.com.au');

        //$mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');

		$mail->Subject = "Order with item error for TT Australia";

        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

		$mail->MsgHTML($body);

        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendNuchevImportError($message)
    {
        $mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."nuchevimporterror.html");
        $replace_array = array("{CONTENT}");
		$replace_with_array = array($message);
		$body = str_replace($replace_array, $replace_with_array, $body);

        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

		//$mail->AddAddress('rachel.wang@nuchev.com.au', 'Rachel Wang');
        $mail->AddAddress('brand.manager@nuchev.com.au', 'Olivia Xiao');

        $mail->AddBCC('customersupport@3plplus.com.au');

        //$mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');

		$mail->Subject = "Order with item error for Nuchev";

        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

		$mail->MsgHTML($body);

        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendCronError($e, $client)
    {
        $mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."cronerror.html");
        $replace_array = array("{CONTENT}", "{CLIENT}");
		$replace_with_array = array(print_r($e, true), $client);
		$body = str_replace($replace_array, $replace_with_array, $body);

        $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');

        //$mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');

		$mail->Subject = "Cron Import Error";

       $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

		$mail->MsgHTML($body);

        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendLowStockWarning($item_name, $email, $name)
    {

		$mail = new PHPMailer(); // defaults to using php "mail()"
		$body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."lowstockmail.html");

		$replace_array = array("{NAME}", "{ITEM_NAME}");
		$replace_with_array = array($name, $item_name);
		$body = str_replace($replace_array, $replace_with_array, $body);

		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");
		$mail->Subject = "3PLPlus WMS: Low Stock Warning";
		$mail->MsgHTML($body);

        if(SITE_LIVE)
        {
            $mail->AddAddress($email, $name);
            //$mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');
        }
        else
        {
            $mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');
        }

		if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendNoaConfirmEmail($order_id)
    {
        $db = Database::openConnection();
		$mail = new PHPMailer(); // defaults to using php "mail()"
		$body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."noa_shipped_email.html");
        $od = $db->queryRow("SELECT * FROM orders WHERE id = $order_id");
        $items = $db->queryData("SELECT i.*, oi.qty, oi.location_id
            FROM orders_items oi JOIN items i ON oi.item_id = i.id
            WHERE oi.order_id = $order_id");

        $client_name = "Noa Sleep";
        //$cd = $db->queryRow("SELECT * FROM customers WHERE id = {$od['customer_id']}");

        $email_link = "<a href='mailto:{$od['tracking_email']}'>{$od['tracking_email']}</a>";
        $sad =  $bad = array(
            'address'   =>  $od['address'],
            'address_2' =>  $od['address_2'],
            'suburb'    =>  $od['suburb'],
            'state'     =>  $od['state'],
            'postcode'  =>  $od['postcode'],
            'country'   =>  $od['country']
        );

        $billing_string = "<p>{$od['ship_to']}</br/>";
        $billing_string .= $bad['address']."<br/>";
        if(!empty($bad['address_2']))
        {
            $billing_string .= $bad['address_2']."<br/>";
        }
        $billing_string .= $bad['suburb'].", ".$bad['state']." ".$bad['postcode']."<br/>";
        $billing_string .= $db->queryValue('countries', array('iso_code_2' => $bad['country']), "name")."</p>";
        /*
        $shipping_string = "<p>{$cd['name']}</br/>";
        $shipping_string .= $sad['address']."<br/>";
        if(!empty($sad['address_2']))
        {
            $shipping_string .= $sad['address_2']."<br/>";
        }
        $shipping_string .= $sad['suburb'].", ".$sad['state']." ".$sad['postcode']."<br/>";
        $shipping_string .= $db->queryValue('countries', array('iso_code_2' => $sad['country']), 'name')."</p>";
        */
        $shipping_string = $billing_string;
        $items_string = "";
        foreach($items as $i)
        {
            $item_price = "$".number_format($i['price'], 2);
            $items_string .= "<tr><td><a href='#'>{$i['name']}</a></td><td>{$i['qty']}</td><td>$item_price <span class='extax'>(ex.tax)</span></td></tr>";
        }
        $total_price = "$".number_format($od['order_total'], 2);
        $items_string .= "<tr><td colspan='2' class='bold'>Subtotal:</td><td>$total_price <span class='extax'>(ex.tax)</span></td></tr>";
        $items_string .= "<tr><td colspan='2' class='bold'>Shipping:</td><td>Free shipping</td></tr>";

        $tracking_info = 'Please visit <a href="https://startrack.com.au/">startrack.com.au</a> and click the "Track & Trace" link at the top of the page. Enter '.$od['consignment_id'].' as the Tracking / reference number(s) for the latest on your delivery. In the event of your absence at the time of delivery, the driver may occasionally, based on careful judgment and inspection of the premises, leave your order in a safe place on your property without your signature ("Authority To Leave"). The driver is not responsible for in home or room placement of your order.<br/>';
        if($od['courier_id'] == $db->queryValue("couriers", array('name' => 'Direct Freight')))
        {
            $tracking_info = 'Please visit <a href="https://directfreight.com.au/">Direct Freight</a> and enter '.$od['consignment_id'].' in the Track & Trace form and click "Track" for the latest on your delivery. In the event of your absence at the time of delivery, the driver may occasionally, based on careful judgment and inspection of the premises, leave your order in a safe place on your property without your signature ("Authority To Leave"). The driver is not responsible for in home or room placement of your order.<br/>';
        }

        $replace_array = array("{NAME}", "{ORDER_NUMBER}", "{EMAIL_LINK}", "{PHONE_NUMBER}", "{BILLING_STRING}", "{SHIPPING_STRING}", "{TRACKING_INFO}", "{ITEM_BODY}");
		$replace_with_array = array($od['ship_to'], $od['client_order_id'], $email_link, $od['contact_phone'], $billing_string, $shipping_string, $tracking_info, $items_string);
		$body = str_replace($replace_array, $replace_with_array, $body);

		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Your Order With Noa Home Has Been Dispatched";
		$mail->MsgHTML($body);
		//$mail->AddEmbeddedImage("$root/images/3pl_logo.png", "emailfoot", "3pl_logo.png");
        //$mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');
		$mail->AddAddress($od['tracking_email'], $od['ship_to']);
        /*   */
        $mail->AddBCC('jc@noahome.com');
        $mail->AddBCC('jeremykopek@noahome.com');
        $mail->AddBCC('customersupport@3plplus.com.au');
        $mail->AddBCC('brendasaccomando@noahome.com', 'Brenda');
        $mail->AddBCC('janikakopek@noahome.com', 'Janika');
        //$mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');

        if(!$mail->Send())
		{
			die($mail->ErrorInfo);
		}
		else
		{
		    $db->updateDatabaseField('orders', 'customer_emailed', 1, $order_id);
			return true;
		}

    }

    public static function sendNoaSydneyConfirmEmail($order_id)
    {
        $db = Database::openConnection();
		$mail = new PHPMailer(); // defaults to using php "mail()"
		$body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."noa_shipped_syd_comet_email.html");
        $od = $db->queryRow("SELECT * FROM orders WHERE id = $order_id");
        $items = $db->queryData("SELECT i.*, oi.qty, oi.location_id
            FROM orders_items oi JOIN items i ON oi.item_id = i.id
            WHERE oi.order_id = $order_id");

        $client_name = "Noa Home";
        //$cd = $db->queryRow("SELECT * FROM customers WHERE id = {$od['customer_id']}");

        $email_link = "<a href='mailto:{$od['tracking_email']}'>{$od['tracking_email']}</a>";
        $sad =  $bad = array(
            'address'   =>  $od['address'],
            'address_2' =>  $od['address_2'],
            'suburb'    =>  $od['suburb'],
            'state'     =>  $od['state'],
            'postcode'  =>  $od['postcode'],
            'country'   =>  $od['country']
        );

        $billing_string = "<p>{$od['ship_to']}</br/>";
        $billing_string .= $bad['address']."<br/>";
        if(!empty($bad['address_2']))
        {
            $billing_string .= $bad['address_2']."<br/>";
        }
        $billing_string .= $bad['suburb'].", ".$bad['state']." ".$bad['postcode']."<br/>";
        $billing_string .= $db->queryValue('countries', array('iso_code_2' => $bad['country']), "name")."</p>";
        /*
        $shipping_string = "<p>{$cd['name']}</br/>";
        $shipping_string .= $sad['address']."<br/>";
        if(!empty($sad['address_2']))
        {
            $shipping_string .= $sad['address_2']."<br/>";
        }
        $shipping_string .= $sad['suburb'].", ".$sad['state']." ".$sad['postcode']."<br/>";
        $shipping_string .= $db->queryValue('countries', array('iso_code_2' => $sad['country']), 'name')."</p>";
        */
        $shipping_string = $billing_string;
        $items_string = "";
        foreach($items as $i)
        {
            $items_string .= "<tr><td><a href='#'>{$i['name']}</a></td><td>{$i['qty']}</td></tr>";
        }

        $items_string .= "<tr><td class='bold'>Shipping:</td><td>Free shipping</td></tr>";

        $tracking_info = 'Please visit <a href="https://startrack.com.au/">startrack.com.au</a> and click the "Track & Trace" link at the top of the page. Enter '.$od['consignment_id'].' as the Tracking / reference number(s) for the latest on your delivery. In the event of your absence at the time of delivery, the driver may occasionally, based on careful judgment and inspection of the premises, leave your order in a safe place on your property without your signature ("Authority To Leave"). The driver is not responsible for in home or room placement of your order.<br/>';
        if($od['courier_id'] == $db->queryValue("couriers", array('name' => 'Direct Freight')))
        {
            $tracking_info = 'Please visit <a href="https://directfreight.com.au/">Direct Freight</a> and enter '.$od['consignment_id'].' in the Track & Trace form and click "Track" for the latest on your delivery. In the event of your absence at the time of delivery, the driver may occasionally, based on careful judgment and inspection of the premises, leave your order in a safe place on your property without your signature ("Authority To Leave"). The driver is not responsible for in home or room placement of your order.<br/>';
        }

        $replace_array = array("{NAME}", "{ORDER_NUMBER}", "{EMAIL_LINK}", "{PHONE_NUMBER}", "{BILLING_STRING}", "{SHIPPING_STRING}", "{TRACKING_INFO}", "{ITEM_BODY}");
		$replace_with_array = array($od['ship_to'], $od['client_order_id'], $email_link, $od['contact_phone'], $billing_string, $shipping_string, $tracking_info, $items_string);
		$body = str_replace($replace_array, $replace_with_array, $body);

		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Your Order With Noa Home Has Been Dispatched";
		$mail->MsgHTML($body);
		//$mail->AddEmbeddedImage("$root/images/3pl_logo.png", "emailfoot", "3pl_logo.png");
        //$mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');
		$mail->AddAddress($od['tracking_email'], $od['ship_to']);
        /*  */
        $mail->AddBCC('jc@noahome.com');
        $mail->AddBCC('jeremykopek@noahome.com');
        $mail->AddBCC('customersupport@3plplus.com.au');
        $mail->AddBCC('brendasaccomando@noahome.com', 'Brenda');
        $mail->AddBCC('janikakopek@noahome.com', 'Janika');
        $mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');

        if(!$mail->Send())
		{
			die($mail->ErrorInfo);
		}
		else
		{
		    $db->updateDatabaseField('orders', 'customer_emailed', 1, $order_id);
			return true;
		}

    }

    public static function sendNoaLocalConfirmEmail($order_ids)
    {
        $db = Database::openConnection();
		$mail = new PHPMailer(); // defaults to using php "mail()"
		$body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."noalocalnotification.html");
        $o_nums = implode(",<br/>", $order_ids);
        $replace_array = array("{ORDER_NUMBERS}");
		$replace_with_array = array($o_nums);
		$body = str_replace($replace_array, $replace_with_array, $body);

		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Noa Orders Dispatched";
		$mail->MsgHTML($body);
		$mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

        if(SITE_LIVE)
        //if(Config::get("SITE_LIVE"))
        {
            $mail->AddAddress('jc@noahome.com');
            $mail->AddAddress('jeremykopek@noahome.com');
            $mail->AddBCC('customersupport@3plplus.com.au');
            $mail->AddAddress('brendasaccomando@noahome.com', 'Brenda');
            $mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');
            $mail->AddAddress('janikakopek@noahome.com', 'Janika');
        }
        else
        {
            $mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');
        }

        if(!$mail->Send())
		{
			die($mail->ErrorInfo);
		}
		else
		{
		    //$db->updateDatabaseField('orders', 'customer_emailed', 1, $order_id);
			return true;
		}

    }

    public static function sendNoaCometConfirmEmail($order_ids)
    {
        $db = Database::openConnection();
		$mail = new PHPMailer(); // defaults to using php "mail()"
		$body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."noacometnotification.html");
        $o_nums = implode(",<br/>", $order_ids);
        $replace_array = array("{ORDER_NUMBERS}");
		$replace_with_array = array($o_nums);
		$body = str_replace($replace_array, $replace_with_array, $body);

		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Noa Orders Dispatched";
		$mail->MsgHTML($body);
		$mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");

        if(SITE_LIVE)
        //if(Config::get("SITE_LIVE"))
        {
            $mail->AddAddress('jc@noahome.com');
            $mail->AddAddress('jeremykopek@noahome.com');
            $mail->AddBCC('customersupport@3plplus.com.au');
            $mail->AddAddress('brendasaccomando@noahome.com', 'Brenda');
            $mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');
            $mail->AddAddress('janikakopek@noahome.com', 'Janika');
        }
        else
        {
            $mail->AddAddress('mark.solly@3plplus.com.au', 'Mark Solly');
        }

        if(!$mail->Send())
		{
			die($mail->ErrorInfo);
		}
		else
		{
		    //$db->updateDatabaseField('orders', 'customer_emailed', 1, $order_id);
			return true;
		}

    }

    public static function sendTrackingEmail($order_id)
	{
        $db = Database::openConnection();

		$mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."tracking.html");

        $od = $db->queryRow("SELECT * FROM orders WHERE id = $order_id");
        $client_details = $db->queryByID('clients', $od['client_id']);
        $client_name = $client_details['client_name'];

        $courier_name = $db->queryValue('couriers', array('id' => $od['courier_id']), 'name');
        $content = "";
        if( !empty($od['customer_order_id']) )
        {
            $content .= "<p>Your order number: {$od['customer_order_id']}</p><p></p>";
        }
        if($courier_name == "Direct Freight")
        {
            $content .= "
                    <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                <p>Please visit <a href='https://www.directfreight.com.au'>www.directfreight.com.au</a> and enter {$od['consignment_id']} as the consignment number in the 'Track and Trace' form at the top left of the webpage.</p>
            ";
        }
        elseif($courier_name == "eParcel" || $courier_name == "eParcel Express" || $courier_name == "Bayswater Eparcel")
        {
            $content .= "
                    <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                <p>Click the following link <a href='https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}'>https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}</a> to track your order.</p>
            ";
        }
        elseif($courier_name == "DHL")
        {
            $content .= "
                    <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                    <p>Click the following link <a href='https://dhlecommerce.asia/track/Track?ref={$od['consignment_id']}'>https://dhlecommerce.asia/track/Track?ref={$od['consignment_id']}</a> to track your order.</p>
            ";
        }
        else
        {
            $courier_name = $od['courier_name'];
            if(preg_match("/hunter(s)?/i", $courier_name, $matches))
            {
                    $content .= "
                            <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                            <p>Please visit <a href='https://www.hunterexpress.com.au'>www.hunterexpress.com.au</a> and enter {$od['consignment_id']} as the consignment number in the 'Quick Track' form at the top right of the webpage.</p>
                    ";
            }
            elseif(preg_match("/eparcel( express)?/i", $courier_name, $matches))
            {
                    $content .= "
                            <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                            <p>Click the following link <a href='https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}'>https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}</a> to track your order.</p>
                    ";
            }
            else
            {
                    $content .= "
                            <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                            <p>Your order has been shipped with $courier_name</p><p>To check the status of your order, please contact them and quote {$od['consignment_id']}.</p>
                    ";
            }
        }
		$replace_array = array("{NAME}", "{CLIENT}", "{CONTENT}");
		$replace_with_array = array($od['ship_to'], $client_name, $content);
		$body = str_replace($replace_array, $replace_with_array, $body);
        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");
		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Your Order With $client_name Has Been Dispatched";
		$mail->MsgHTML($body);

		$mail->AddAddress($od['tracking_email'], $od['ship_to']);
        //$mail->AddAddress("mark.solly@3plplus.com.au", "Mark Solly");
        //$mail->AddBCC("mark.solly@3plplus.com.au", "Mark Solly");

        if($client_details['id'] == 55)
        {
                $mail->AddBCC($client_details['deliveries_email']);
        }
		if(!$mail->Send())
		{
			die($mail->ErrorInfo);
		}
		else
		{
		    $db->updateDatabaseField('orders', 'customer_emailed', 1, $order_id);
			return true;
		}
	}

    public static function sendNDCTracking($order_id)
	{
        $db = Database::openConnection();

		$mail = new PHPMailer();

        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."ndc_tracking.html");

        $od = $db->queryRow("SELECT * FROM orders WHERE id = $order_id");
        $client_details = $db->queryByID('clients', $od['client_id']);
        $client_name = $client_details['client_name'];

        $courier_name = $db->queryValue('couriers', array('id' => $od['courier_id']), 'name');
        $content = "";
        if( !empty($od['customer_order_id']) )
        {
            $content .= "<p>Your order number: {$od['customer_order_id']}</p><p></p>";
        }
        if($courier_name == "Hunters Small" || $courier_name == "Hunters Bulk" || $courier_name == "Hunters Pallet")
        {
            $content .= "
                    <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                <p>Please visit <a href='https://www.hunterexpress.com.au'>www.hunterexpress.com.au</a> and enter {$od['consignment_id']} as the consignment number in the 'Quick Track' form at the top right of the webpage.</p>
            ";
        }
        elseif($courier_name == "eParcel" || $courier_name == "eParcel Express")
        {
            $content .= "
                    <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                <p>Click the following link <a href='https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}'>https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}</a> to track your order.</p>
            ";
        }
        elseif($courier_name == "DHL")
        {
            $content .= "
                    <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                    <p>Click the following link <a href='https://dhlecommerce.asia/track/Track?ref={$od['consignment_id']}'>https://dhlecommerce.asia/track/Track?ref={$od['consignment_id']}</a> to track your order.</p>
            ";
        }
        else
        {
            $courier_name = $od['courier_name'];
            if(preg_match("/hunter(s)?/i", $courier_name, $matches))
            {
                    $content .= "
                            <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                            <p>Please visit <a href='https://www.hunterexpress.com.au'>www.hunterexpress.com.au</a> and enter {$od['consignment_id']} as the consignment number in the 'Quick Track' form at the top right of the webpage.</p>
                    ";
            }
            elseif(preg_match("/eparcel( express)?/i", $courier_name, $matches))
            {
                    $content .= "
                            <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                            <p>Click the following link <a href='https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}'>https://auspost.com.au/parcels-mail/track.html#/track?id={$od['consignment_id']}</a> to track your order.</p>
                    ";
            }
            else
            {
                    $content .= "
                            <p>Your tracking number is <strong>{$od['consignment_id']}</strong>.</p><p></p>
                            <p>Your order has been shipped with $courier_name</p><p>To check the status of your order, please contact them and quote {$od['consignment_id']}.</p>
                    ";
            }
        }
		$replace_array = array("{NAME}", "{CLIENT}", "{CONTENT}");
		$replace_with_array = array($od['ship_to'], $client_name, $content);
		$body = str_replace($replace_array, $replace_with_array, $body);
        $mail->AddEmbeddedImage(IMAGES."client_logos/tn_ndcbrandcharcoal01.jpg", "emailfoot", "email_logo.png");
		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Your Order With $client_name Has Been Dispatched";
		$mail->MsgHTML($body);

		$mail->AddAddress($od['tracking_email'], $od['ship_to']);
        //$mail->AddBCC("mark.solly@3plplus.com.au", "Mark Solly");

        if(!$mail->Send())
		{
			die($mail->ErrorInfo);
		}
		else
		{
		    $db->updateDatabaseField('orders', 'customer_emailed', 1, $order_id);
			return true;
		}
	}

    public static function sendNewUserEmail($name, $email)
    {
        $mail = new PHPMailer();
        $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."new_user.html");
        $replace_array = array("{NAME}");
		$replace_with_array = array($name);
        $body = str_replace($replace_array, $replace_with_array, $body);
        $mail->AddEmbeddedImage(IMAGES."email_logo.png", "emailfoot", "email_logo.png");
		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
		$mail->Subject = "Access Instructions For 3PLPlus WMS";
		$mail->MsgHTML($body);
        $mail->addAttachment(Config::get('EMAIL_ATTACHMENTS_PATH')."WMS Instructions.docx", 'wms_instructions.docx');
        $mail->AddAddress($email, $name);
        $mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');
        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            //throw new Exception("Email couldn't be sent ");
        }
    }

 }
	
