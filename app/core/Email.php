<?php

 /**
  * Email Class
  *
  * Sending emails via SMTP.
  * It uses PHPMailer library to send emails.
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>
  */
  use PHPMailer\PHPMailer\PHPMailer;

 class Email{
     /**
      * This is the constructor for Email object.
      *
      * @access private
      */
    private function __construct(){}

    public static function notifyCustomerForPickup($data)
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
            $mail->ClearReplyTos();
            $mail->addReplyTo($data['client_email'], $data['client_name']);

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."notifyofpickup.html");
            $replace_array = array("{NAME}", "{CLIENT_ORDER_ID}", "{CLIENT}");
    		$replace_with_array = array($data['name'], $data['client_order_id'], $data['client_name']);
    		$body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		if(SITE_LIVE)
            //if(Config::get("SITE_LIVE"))
            {
                $mail->AddAddress($data['email'],$data['name']);
                $mail->AddBCC($data['client_email'], $data['client_name']);
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }

    		$mail->Subject = "Your ".$data['client_name']." Order is Ready for Collection";

            $mail->AddEmbeddedImage($data['client_logo'], "emailfoot", "FSG_logo@130px.png");
            $mail->AddEmbeddedImage(IMAGES."fsg_map.png", "mapimage", "fsg_map.png");

            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent to ". $data['email']);
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

    public static function sendErrorPageReport($data)
    {
        $sender_id = Session::getUserId();
        $user = new User;
        $sender_details = $user->getProfileInfo($sender_id);
        $user =  $sender_details['name']." (".$sender_details['email'].")";
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
            $mail->ClearReplyTos();
            $mail->addReplyTo($sender_details['email'], $sender_details['name']);

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."errorpagereport.html");
            $replace_array = array("{URL}", "{ERROR_TYPE}", "{DATE_TIME}", "{USER}");
    		$replace_with_array = array($data['url'], $data['error_type'], date("c", $data['loaded']), $user);
    		$body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');

    		$mail->Subject = "Error Page reported: ".$data['error_type'];

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

    		$mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent to ". $sender_details['name']);
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

    public static function sendContactUsEmail($subject, $message)
    {
        $sender_id = Session::getUserId();
        $user = new User;
        $sender_details = $user->getProfileInfo($sender_id);
        //echo "<pre>",print_r($sender_details),"</pre>";die();
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."contactus.html");
            $replace_array = array("{SUBJECT}", "{FROM}", "{FROM_EMAIL}", "{MESSAGE}");
    		$replace_with_array = array($subject, $sender_details['name'], $sender_details['email'], $message);
    		$body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');

    		$mail->Subject = "Message From Contacte Form: $subject";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

    		$mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent to ". $sender_details['name']);
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

    public static function sendProductionJobReminder($job)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."productionreminder2D.html");
            $replace_array = array("{NAME}", "{JOB_DETAILS}");
            $job_details = "
              <table>
                <tr>
                    <td class='field'>Job Number</td>
                    <td class='value'>{$job['job_id']}<td>
                </tr>
                <tr>
                    <td class='field'>Job Customer</td>
                    <td class='value'>{$job['customer_name']}<td>
                </tr>
                <tr>
                    <td class='field'>Job Description</td>
                    <td class='value'>{$job['description']}<td>
                </tr>
                <tr>
                    <td class='field'>Due Date</td>
                    <td class='value'>".date("d/m/Y", $job['due_date'])."<td>
                </tr>
              </table>
            ";
    		$replace_with_array = array('Andrea', $job_details);
            $body = str_replace($replace_array, $replace_with_array, $body);
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
    		$mail->Subject = "There Is An Urgent Job Due Soon";
            $mail->AddEmbeddedImage(IMAGES."backgrounds/FSG_logo.png", "emailfoot", "FSG_logo.png");
            //$mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");
    		$mail->MsgHTML($body);
            $mail->AddAddress('production@fsg.com.au', 'Andrea DiPrima');
            //$mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
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

        $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

        if(SITE_LIVE)
        //if(Config::get("SITE_LIVE"))
        {
            $mail->AddAddress($cd['billing_email'], $cd['contact_name']);
            if($client_id == 6)
            {
                $mail->AddAddress($cd['inventory_email'], $cd['inventory_contact']);
                $mail->AddAddress('kimberly@thebigbottleco.com', 'Kimberly Lacsa');
            }
            $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
        }
        else
        {
            $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
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
            $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
        }
        else
        {
            $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
        }



		$mail->Subject = "Pickup Request From ".$client_name;

        $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

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
    		$replace_with_array = array(Config::get('EMAIL_PASSWORD_RESET_URL') . "?id=" . urlencode(Encryption::encryptId($user_id)) . "&token=" . urlencode($password_token), $name);
    		$body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		$mail->AddAddress($email, $name);

            $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');

    		$mail->Subject = "Reset your password for FSG WMS system";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

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

        $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

		$mail->MsgHTML($body);

        if(!$mail->Send())
        {
            Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendBuzzBeeShopifyImportSummary($ret_array)
    {
        //echo "<pre>",print_r($ret_array),"</pre>"; die();
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."bbshopifyimportfeedback.html");

            $import_errors = "";
            if($ret_array['error_count'] > 0)
            {
                $error_numbers = implode(", ", $ret_array['error_orders']);
                $import_errors = "
                    <h3 class='error'>The following orders numbers have <strong>NOT</strong> been imported due to inventory or SKU errors</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #f8dbdb;border: 1px solid #ff3333;color: #ff3333;'>
                        <tr>
                            <td style='padding: 20px;'>$error_numbers</td>
                        </tr>
                    </table>
                ";
            }
            $imports = "";
            if($ret_array['import_count'] > 0)
            {
                $import_numbers = implode(", ", $ret_array['imported_orders']);
                $imports = "
                    <h3 class='success'>The following order numbers have been successfuly import and will be picked and packed shortly</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #ccebd6;border: 3px solid #009933;color: #009933;'>
                        <tr>
                            <td style='padding: 20px;'>$import_numbers</td>
                        </tr>
                    </table>
                ";
            }

            $replace_array = array("{IMPORT_ERROR_COUNT}","{IMPORT_COUNT}","{IMPORT_ERRORS}","{IMPORTS}");
            $replace_with_array = array($ret_array['error_count'], $ret_array['import_count'], $import_errors, $imports);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                //$mail->AddBCC('tim@fsg.com.au', 'Tim Swanton');
                $mail->AddAddress('admin@buzzbee.com.au','Graham Abrey');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->Subject = "Shopify Order Import Summary for BuzzBee Australia";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

    public static function sendNuchevMarketplacerImportSummary($ret_array)
    {
        //echo "<pre>",print_r($ret_array),"</pre>"; die();
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."nuchevmarketplacerimportfeedback.html");

            $import_errors = "";
            if($ret_array['error_count'] > 0)
            {
                $error_numbers = implode(", ", $ret_array['error_orders']);
                $import_errors = "
                    <h3 class='error'>The following orders numbers have <strong>NOT</strong> been imported due to inventory or SKU errors</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #f8dbdb;border: 1px solid #ff3333;color: #ff3333;'>
                        <tr>
                            <td style='padding: 20px;'>$error_numbers</td>
                        </tr>
                    </table>
                ";
            }
            $imports = "";
            if($ret_array['import_count'] > 0)
            {
                $import_numbers = implode(", ", $ret_array['imported_orders']);
                $imports = "
                    <h3 class='success'>The following order numbers have been successfuly import and will be picked and packed shortly</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #ccebd6;border: 3px solid #009933;color: #009933;'>
                        <tr>
                            <td style='padding: 20px;'>$import_numbers</td>
                        </tr>
                    </table>
                ";
            }

            $replace_array = array("{IMPORT_ERROR_COUNT}","{IMPORT_COUNT}","{IMPORT_ERRORS}","{IMPORTS}");
            $replace_with_array = array($ret_array['error_count'], $ret_array['import_count'], $import_errors, $imports);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                $mail->AddAddress('accounts.receivable@nuchev.com.au','Pauline Cripps');
                $mail->AddAddress('john.ware@nuchev.com.au','John Ware');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->Subject = "Markeplacer Order Import Summary for Nuchev";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

    public static function sendPBAShopifyImportSummary($ret_array, $site = "Perfect Practice Golf")
    {
        //echo "<pre>",print_r($ret_array),"</pre>"; die();
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."pbashopifyimportfeedback.html");

            $import_errors = "";
            if($ret_array['error_count'] > 0)
            {
                $error_numbers = implode(", ", $ret_array['error_orders']);
                $import_errors = "
                    <h3 class='error'>The following orders numbers have <strong>NOT</strong> been imported due to inventory or SKU errors</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #f8dbdb;border: 1px solid #ff3333;color: #ff3333;'>
                        <tr>
                            <td style='padding: 20px;'>$error_numbers</td>
                        </tr>
                    </table>
                ";
            }
            $imports = "";
            if($ret_array['import_count'] > 0)
            {
                $import_numbers = implode(", ", $ret_array['imported_orders']);
                $imports = "
                    <h3 class='success'>The following order numbers have been successfuly import and will be picked and packed shortly</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #ccebd6;border: 3px solid #009933;color: #009933;'>
                        <tr>
                            <td style='padding: 20px;'>$import_numbers</td>
                        </tr>
                    </table>
                ";
            }

            $replace_array = array("{IMPORT_ERROR_COUNT}","{IMPORT_COUNT}","{IMPORT_ERRORS}","{IMPORTS}","{SITE}");
            $replace_with_array = array($ret_array['error_count'], $ret_array['import_count'], $import_errors, $imports,$site);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                //$mail->AddBCC('tim@fsg.com.au', 'Tim Swanton');
                $mail->AddAddress('clint@performancebrandsaustralia.com','Clint Rice');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->Subject = "Shopify Order Import Summary for Performance Brands Australia";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

 public static function sendPBAEbayImportSummary($ret_array)
    {
        //echo "<pre>",print_r($ret_array),"</pre>"; die();
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."pbaebayimportfeedback.html");

            $import_errors = "";
            if($ret_array['error_count'] > 0)
            {
                $error_numbers = implode(", ", $ret_array['error_orders']);
                $import_errors = "
                    <h3 class='error'>The following orders numbers have <strong>NOT</strong> been imported due to inventory or SKU errors</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #f8dbdb;border: 1px solid #ff3333;color: #ff3333;'>
                        <tr>
                            <td style='padding: 20px;'>$error_numbers</td>
                        </tr>
                    </table>
                ";
            }
            $imports = "";
            if($ret_array['import_count'] > 0)
            {
                $import_numbers = implode(", ", $ret_array['imported_orders']);
                $imports = "
                    <h3 class='success'>The following order numbers have been successfuly import and will be picked and packed shortly</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #ccebd6;border: 3px solid #009933;color: #009933;'>
                        <tr>
                            <td style='padding: 20px;'>$import_numbers</td>
                        </tr>
                    </table>
                ";
            }

            $replace_array = array("{IMPORT_ERROR_COUNT}","{IMPORT_COUNT}","{IMPORT_ERRORS}","{IMPORTS}");
            $replace_with_array = array($ret_array['error_count'], $ret_array['import_count'], $import_errors, $imports);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                //$mail->AddBCC('tim@fsg.com.au', 'Tim Swanton');
                $mail->AddAddress('clint@performancebrandsaustralia.com','Clint Rice');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->Subject = "Ebay Order Import Summary for Performance Brands Australia";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

    public static function sendPBAWooImportSummary($ret_array)
    {
        //echo "<pre>",print_r($ret_array),"</pre>"; die();
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."pbawooimportfeedback.html");

            $import_errors = "";
            if($ret_array['error_count'] > 0)
            {
                $error_numbers = implode(", ", $ret_array['error_orders']);
                $import_errors = "
                    <h3 class='error'>The following orders numbers have <strong>NOT</strong> been imported due to inventory or SKU errors</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #f8dbdb;border: 1px solid #ff3333;color: #ff3333;'>
                        <tr>
                            <td style='padding: 20px;'>$error_numbers</td>
                        </tr>
                    </table>
                ";
            }
            $imports = "";
            if($ret_array['import_count'] > 0)
            {
                $import_numbers = implode(", ", $ret_array['imported_orders']);
                $imports = "
                    <h3 class='success'>The following order numbers have been successfuly import and will be picked and packed shortly</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #ccebd6;border: 3px solid #009933;color: #009933;'>
                        <tr>
                            <td style='padding: 20px;'>$import_numbers</td>
                        </tr>
                    </table>
                ";
            }

            $replace_array = array("{IMPORT_ERROR_COUNT}","{IMPORT_COUNT}","{IMPORT_ERRORS}","{IMPORTS}");
            $replace_with_array = array($ret_array['error_count'], $ret_array['import_count'], $import_errors, $imports);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                //$mail->AddBCC('tim@fsg.com.au', 'Tim Swanton');
                $mail->AddAddress('clint@performancebrandsaustralia.com','Clint Rice');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->Subject = "WooCommerce Order Import Summarry for Performance Brands Australia";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

    public static function sendBBImportError($message)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."bbimporterror.html");
            $replace_array = array("{CONTENT}");
            $replace_with_array = array($message);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                //$mail->AddBCC('tim@fsg.com.au', 'Tim Swanton');
                $mail->AddAddress('admin@buzzbee.com.au','Graham Abrey');

                //$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }

            $mail->Subject = "Order with item error for BuzzBee Australia";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

    public static function sendNuchevMarketplacerImportError($message)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."marketplacerimporterror.html");
            $replace_array = array("{CONTENT}");
            $replace_with_array = array($message);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                $mail->AddAddress('accounts.receivable@nuchev.com.au','Pauline Cripps');
                $mail->AddAddress('john.ware@nuchev.com.au','John Ware');
                //$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }

            $mail->Subject = "Order with item error for BuzzBee Australia";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

    public static function sendPBAImportError($message)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."pbaimporterror.html");
            $replace_array = array("{CONTENT}");
            $replace_with_array = array($message);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

            if(SITE_LIVE)
            {
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
                //$mail->AddBCC('tim@fsg.com.au', 'Tim Swanton');
                $mail->AddAddress('clint@performancebrandsaustralia.com','Clint Rice');
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }

            $mail->Subject = "Order with item error for Performance Brands Australia";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

            $mail->MsgHTML($body);

            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
    }

    public static function sendBDSImportFeedback($feedback)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        extract($feedback);
        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');

            $import_errors = "";
            if($import_error)
            {
                $import_errors .= "
                    <h3 class='error'>The Following Feedback Has Been Supplied Regarding Import Errors</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #f8dbdb;border: 1px solid #ff3333;color: #ff3333;'>
                        <tr>
                            <td style='padding: 20px;'>$import_error_string</td>
                        </tr>
                    </table>
                ";
            }
            $backorders = "";
            if($backorder)
            {
                $backorders .= "
                    <h3 class='backorder'>The Following Feedback Has Been Supplied Regarding Print On Demand Orders</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #e6f2ff;border: 3px solid #007bff;color: #007bff;'>
                        <tr>
                            <td style='padding: 20px;'>$backorder_string</td>
                        </tr>
                    </table>
                ";
            }
            $inventory_errors = "";
            if($inventory_error)
            {
                $inventory_errors .= "
                    <h3 class='backorder'>The Following Feedback Has Been Supplied Regarding Inventory Errors</h3>
                    <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #f8dbdb;border: 1px solid #ff3333;color: #ff3333;'>
                        <tr>
                            <td style='padding: 20px;'>$inventory_error_string</td>
                        </tr>
                    </table>
                ";
            }
            $imports = "
                <h3 class='success'>The Following Feedback Has Been Supplied Regarding Successful Imports</h3>
                <table cellspacing='0' cellpadding='0' border='0' style='width:720px;background: none repeat scroll 0 0 #ccebd6;border: 3px solid #009933;color: #009933;'>
                    <tr>
                        <td style='padding: 20px;'>$import_string</td>
                    </tr>
                </table>
            ";
            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."bdsimportfeedback.html");
            $replace_array = array("{INVENTORY_ERROR_COUNT}","{INVENTORY_ERRORS}","{TOTAL_IMPORT}","{IMPORT_ERROR_COUNT}","{BACKORDER_COUNT}","{IMPORT_COUNT}","{IMPORT_ERRORS}","{BACKORDERS}","{IMPORTS}");
		    $replace_with_array = array($inventory_error_count, $inventory_errors, $total_import, $import_error_count, $backorder_count, $import_count,$import_errors,$backorders,$imports);
		    $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            if(SITE_LIVE)
            {
                $mail->AddAddress('bds@bahai.org.au', 'Michael Cohen');
            }


    		$mail->Subject = "BDS Order Import Summary";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

    		$mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent");
                return false;
            }
        } catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
        return true;
    }

    public static function sendBDSFinaliseFeedback($count = 0)
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

            if($count == 1)
            {
                $replace = "One order has been";
            }
            else
            {
                $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                $replace = ucfirst($f->format($count))." orders have been";
            }

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."bdsfinalisefeedback.html");
            $replace_array = array("{ORDER_COUNT}");
            $replace_with_array = array($replace);
            $body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            if(SITE_LIVE)
            {
                $mail->AddAddress('bds@bahai.org.au', 'Michael Cohen');
            }

    		$mail->Subject = "BDS Order Finalise Summary";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

    		$mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent");
                return false;
            }
        } catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
        return true;
    }

    public static function sendBDSNoOrdersFeedback()
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."bdsnoordersfeedback.html");
            //$replace_array = array("{ORDER_COUNT}");
            //$replace_with_array = array($count);
            //$body = str_replace($replace_array, $replace_with_array, $body);

            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));

    		$mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');

    		$mail->Subject = "BDS No Orders";

            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

    		$mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent");
                return false;
            }
        } catch (phpmailerException $e) {
            print_r($e->errorMessage());die();
        } catch (Exception $e) {
            print_r($e->getMessage());die();
        }
        return true;
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

        //$mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');

		$mail->Subject = "Order with item error for Nuchev";

        $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");

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
        $mail->IsSMTP();
        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');
            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."cronerror.html");
            $replace_array = array("{CONTENT}", "{CLIENT}");
    		$replace_with_array = array(print_r($e, true), $client);
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            $mail->Subject = "Cron Import Error";
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "email_logo.png");
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent ");
            }
        }
        catch (phpmailerException $e) {
            Logger::log("Mail Error: ", print_r($e->errorMessage(), true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        } catch (Exception $e) {
            Logger::log("Mail Error: ", print_r($e->getMessage(), true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }

    }

    public static function sendLowStockWarning($item_name, $email, $name)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."lowstockmail.html");
    		$replace_array = array("{NAME}", "{ITEM_NAME}");
    		$replace_with_array = array($name, $item_name);
    		$body = str_replace($replace_array, $replace_with_array, $body);
            //$mail->AddEmbeddedImage(IMAGES."op_email_foot.png", "emailfoot", "email_logo.png");
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "email_logo.png");
    		$mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "FSG WMS: Low Stock Warning";
            $mail->MsgHTML($body);
            if(SITE_LIVE)
            {
                $mail->AddAddress($email, $name);
                $mail->AddBCC('mark.solly@fsg.com.au', 'Mark Solly');
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
            return true;
        }
        catch (phpmailerException $e) {
            Logger::log("Mail Error: ", print_r($e->errorMessage(), true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        } catch (Exception $e) {
            Logger::log("Mail Error: ", print_r($e->getMessage(), true), __FILE__, __LINE__);
            throw new Exception("Email couldn't be sent ");
        }
    }

    public static function sendTrackingEmail($order_id)
	{
        $db = Database::openConnection();

        $mail = new PHPMailer();
        $mail->IsSMTP();

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

        try{
            $mail->Host = "smtp.office365.com";
            $mail->Port = Config::get('EMAIL_PORT');
            $mail->SMTPDebug  = 0;
            $mail->SMTPSecure = "tls";
            $mail->SMTPAuth = true;
            $mail->Username = Config::get('EMAIL_UNAME');
            $mail->Password = Config::get('EMAIL_PWD');
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "FSG_logo@130px.png");
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Your Order With $client_name Has Been Dispatched";
            $mail->MsgHTML($body);

            $mail->AddAddress($od['tracking_email'], $od['ship_to']);
            //$mail->AddAddress("mark.solly@fsg.com.au", "Mark Solly");
            //$mail->AddBCC("mark.solly@fsg.com.au", "Mark Solly");
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
            //$mail->AddAddress("mark.solly@fsg.com.au", "Mark Solly");
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
		    $replace_with_array = array($message, date('D \t\h\e jS \o\f M \a\t g:i a'));
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

    public static function notifyStatusChange($job_no, $new_status, $changer_name)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."status_change.html");

            $replace_array = array("{JOB_NO}", "{NEW_STATUS}", "{STATUS_CHANGER}", "{CHANGE_TIME}");
            $replace_with_array = array($job_no, $new_status, $changer_name, date("F j, Y, g:i a"));
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "email_logo.png");
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Production Job Status Change";
            if(SITE_LIVE)
            {
    		    $mail->AddAddress('production@fsg.com.au', 'Andrea DiPrima');
                $mail->AddAddress('megan@fsg.com.au', 'Megan Low');
            }

            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent");
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

    public static function notifyProdOfDeliveryNoteChange($job_no, $new_note, $changer_name)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."delivery_note_change.html");

            $replace_array = array("{JOB_NO}", "{NEW_NOTE}", "{NOTE_CHANGER}", "{CHANGE_TIME}");
            $replace_with_array = array($job_no, $new_note, $changer_name, date("F j, Y, g:i a"));
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "email_logo.png");
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Production Job Delivery Notes Updated";
            if(SITE_LIVE)
            {
    		    $mail->AddAddress('mike@fsg.com.au', 'Mike Bond');
            }

            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent");
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

    public static function notifyProdAdminOfNoteChange($job_no, $new_note, $changer_name)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."note_change.html");

            $replace_array = array("{JOB_NO}", "{NEW_NOTE}", "{NOTE_CHANGER}", "{CHANGE_TIME}");
            $replace_with_array = array($job_no, $new_note, $changer_name, date("F j, Y, g:i a"));
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "email_logo.png");
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Production Job Notes Updated";
            if(SITE_LIVE)
            {
    		    $mail->AddAddress('production@fsg.com.au', 'Andrea DiPrima');
                $mail->AddAddress('megan@fsg.com.au', 'Megan Low');
            }

            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent");
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

    public static function notifyProdContactOfStatusChange($job_no, $job_client, $new_status, $pc_email, $pc_name)
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

            $body = file_get_contents(Config::get('EMAIL_TEMPLATES_PATH')."pc_status_change.html");

            $replace_array = array("{JOB_NO}", "{JOB_CLIENT}", "{NEW_STATUS}", "{CHANGE_TIME}");
            $replace_with_array = array($job_no, $job_client, $new_status, date("F j, Y, g:i a"));
    		$body = str_replace($replace_array, $replace_with_array, $body);
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "email_logo.png");
            $mail->SetFrom(Config::get('EMAIL_FROM'), Config::get('EMAIL_FROM_NAME'));
            $mail->Subject = "Production Job Status Change";
            if(SITE_LIVE)
            {
    		    $mail->AddAddress($pc_email, $pc_name);
            }
            else
            {
                $mail->AddAddress('mark.solly@fsg.com.au', 'Mark Solly');
            }
            $mail->MsgHTML($body);
            if(!$mail->Send())
            {
                Logger::log("Mail Error", print_r($mail->ErrorInfo, true), __FILE__, __LINE__);
                throw new Exception("Email couldn't be sent");
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
            $mail->AddEmbeddedImage(IMAGES."FSG_logo@130px.png", "emailfoot", "email_logo.png");
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
	
