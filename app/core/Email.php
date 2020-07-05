<?php

 /**
  * Email Class
  *
  * Sending emails via SMTP.
  * It uses PHPMailer library to send emails.
  *

  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */
  use PHPMailer\PHPMailer\PHPMailer;

 class Email{
     /**
      * This is the constructor for Email object.
      *
      * @access private
      */
    private function __construct(){
    }


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
        $mail->IsSMTP();
        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."passwordreset.html");
            $replace_array = array("{LINK}", "{NAME}");
    		$replace_with_array = array("<a href='".Config::get('EMAIL_PASSWORD_RESET_URL') . "?id=" . urlencode(Encryption::encryptId($user_id)) . "&token=" . urlencode($password_token)."'>Reset Link</a>", $name);
    		$body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		$mail->AddAddress($email, $name);

            $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');

    		$mail->Subject = "Reset your password for FSG WMS system";

            $mail->AddEmbeddedImage(IMAGES."backgrounds/FSG_logo.png", "emailfoot", "email_logo.png");

    		$mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent to ". $name);
                return false;
            }
        } catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
        //die('email');
        return true;
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
		$mail->Subject = "FSG WMS: Low Stock Warning";
		$mail->MsgHTML($body);

        if(SITE_LIVE)
        {
            $mail->AddAddress($email, $name);
            //$mail->AddBCC('mark.solly@3plplus.com.au', 'Mark Solly');
        }
        else
        {
            $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
        }

		if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
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

    public static function sendOnePlateTrackingEmail($order_id)
	{
        $db = Database::openConnection();

		$mail = new PHPMailer();
        $mail->IsSMTP();
        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."oneplatetracking.html");

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
            $mail->AddEmbeddedImage(IMAGES."op_email_foot.png", "emailfoot", "op_email_foot.png");
    		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
    		$mail->Subject = "Your Order With One Plate Has Been Dispatched";
    		$mail->MsgHTML($body);

    		$mail->AddAddress($od['tracking_email'], $od['ship_to']);
            //$mail->AddAddress("mark.solly@3plplus.com.au", "Mark Solly");
            $mail->AddBCC("mark.solly@fsg.com.au", "Mark Solly");

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
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }

	}

    public static function sendFreedomMYOBSummary($message)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."freedom_myob_import_summary.html");
            $replace_array = array("{MESSAGE}", "{TIME}");
		    $replace_with_array = array($message, date("D the jS of M at g:i a"));
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->AddEmbeddedImage(IMAGES."backgrounds/FSG_logo.png", "emailfoot", "email_logo.png");
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Freedom MYOB Import Summary";
    		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent to ". $name);
                return false;
            }
        } catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
        //die('email');
        return true;
    }

    public static function sendFreedomMYOBError($message)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."freedom_myob_import_error.html");
            $replace_array = array("{MESSAGE}");
		    $replace_with_array = array($message);
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->AddEmbeddedImage(IMAGES."backgrounds/FSG_logo.png", "emailfoot", "email_logo.png");
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Freedom MYOB Import Error";
    		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent to ". $name);
                return false;
            }
        } catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
        //die('email');
        return true;
    }

    public static function sendNewUserEmail($name, $email)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."new_user.html");
            $replace_array = array("{NAME}");
		    $replace_with_array = array($name);
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->AddEmbeddedImage(IMAGES."backgrounds/FSG_logo.png", "emailfoot", "email_logo.png");
            $mail->addAttachment(Config::get('EMAIL_ATTACHMENTS_PATH')."WMS Instructions.docx", 'wms_instructions.docx');
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Access Instructions For FSG WMS";
    		$mail->AddAddress($email, $name);
            $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent to ". $name);
                return false;
            }
        } catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
        //die('email');
        return true;
    }

 }
	
