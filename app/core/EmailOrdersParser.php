<?php

/**
 * EmailOrdersParser class.
 *
 * Trawls thru inboxes to import orders

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class EmailOrdersParser{

    public $controller;

    private $output;
    private $charset;
    private $htmlmsg;
    private $plainmsg;
    private $attachments;
    private $figure8items;
    private $nuchevsampleitems;
    private $return_array = array(
        'import_count'          => 0,
        'import_error'          => false,
        'error'                 => false,
        'error_count'           => 0,
        'error_string'          => '',
        'import_error_string'   => ''
    );
    private $states = array(
        'VIC'   => 'VICTORIA',
        'TAS'   => 'TASMANIA',
        'NSW'   => 'NEW SOUTH WALES',
        'QLD'   => 'QUEENSLAND',
        'ACT'   => 'AUSTRALIAN CAPITAL TERRITORY',
        'NT'   => 'NORTHERN TERRITORY',
        'SA'   => 'SOUTH AUSTRALIA',
        'WA'   => 'WESTERN AUSTRALIA'
    );
    private $acceptable_states;
    private $inbox;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->acceptable_states = array_keys($this->states);
    }

    public function getNuchevSamples()
    {
        $db = Database::openConnection();
        $hostname = '{host.sollysweb.com/novalidate-cert}INBOX';
        $username = 'nuchev@wms.3plplus.com.au';
        $password = 'WMSnuchev2018';

        $this->inbox = imap_open($hostname,$username,$password) or die('Cannot connect to server: ' . imap_last_error());
        $num = imap_num_msg($this->inbox);
        $mail_message = $address_message = "";

        $this->output = "=========================================================================================================".PHP_EOL;
        $this->output .= "IMPORTING NUCHEV SAMPLES FOR ".date("jS M Y (D), g:i a (T)").PHP_EOL;
        if($num == 0)
        {
            $this->output .= "No messages on server".PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;
        }
        else
        {
            $collected_orders = array();
            for($x=1; $x <= $num; $x++)
            {
                set_time_limit(60);
                $this->getmsg($this->inbox, $x);
                $header = imap_header($this->inbox,$x);

                if(preg_match('/.?Oli 6 - Sachet Request Order.?/i', $header->subject, $matches))
                {
                    $text = (imap_fetchbody($this->inbox,$x,1.1));
                    if($text == '')
                    {
                        $text = (imap_fetchbody($this->inbox,$x,1));
                    }
                    $text = preg_replace('#<br\s*/?>#i', "\n", $text);
                    $text = strip_tags($text);
                    $text = str_replace('&nbsp;', '', $text);
                    $text = preg_replace( '/(?:(?:\r\n|\r|\n)\s*){2}/', "\n", $text );
                    $lines = explode("\n", $text);

                    $collected_orders[$x] = $lines;
                }
                else
                {
                    /*  */
                    if(!imap_mail_move ( $this->inbox , $x , 'INBOX.store'  ))
                    {
                        $this->output .=  "Error moving message $x to Store folder".PHP_EOL;
                    }
                    else
                    {
                        $this->output .=  "Moved message $x to Store folder".PHP_EOL;
                    }
                }

                //$this->output .= "-----------------------------------------------".PHP_EOL;
            }
            $this->output .= "Collected Orders".PHP_EOL;
            $this->output .= print_r($collected_orders,true).PHP_EOL;

            //echo nl2br($this->output);//die();
            $orders = $this->procNuchevSamples($collected_orders);
            //echo "<pre>",print_r($this->nuchevsampleitems),"</pre>";
            //echo "<pre>",print_r($orders),"</pre>"; die();
            if($orders)
            {
                $this->addNuchevSamples($orders);
            }
            else
            {
                $this->return_array['error'] = true;
            }
            Logger::logOrderImports('order_imports/nuchevSamples', $this->output);
            imap_expunge($this->inbox);
            /* close the connection */
            imap_close($this->inbox);
            return $this->return_array;
        }
    }

    private function addNuchevSamples($orders)
    {
        foreach($orders as $x => $o)
        {
            //check for errors first
            $item_error = false;
            $error_string = "";
            foreach($this->nuchevsampleitems[$o['client_order_id']] as $item)
            {
                //echo "<pre>",print_r($item),"</pre>";
                if($item['item_error'])
                {
                    $item_error = true;
                    $error_string .= $item['item_error_string'];
                }
            }
            //die();
            if($item_error)
            {
                $message = "<p>There was a problem with some items</p>";
                $message .= $error_string;
                $message .= "<p>Orders with these items will not be processed at the moment</p>";
                $message .= "<p>Customer: {$o['ship_to']}</p>";
                $message .= "<p>Customer: {$o['company_name']}</p>";
                $message .= "<p>Address: {$o['address']}</p>";
                $message .= "<p>{$o['address_2']}</p>";
                $message .= "<p>{$o['suburb']}</p>";
                $message .= "<p>{$o['state']}</p>";
                $message .= "<p>{$o['postcode']}</p>";
                $message .= "<p>{$o['country']}</p>";
                //if (php_sapi_name() !='cli')
                if ($_SERVER['HTTP_USER_AGENT'] != '3PLPLUSAGENT')
                {
                    ++$this->return_array['error_count'];
                    $this->return_array['error_string'] .= $message;
                }
                else
                {
                    //Email::nuchevSampleImportError($message);

                }
                continue;
            }
            if($o['import_error'])
            {
                $this->return_array['import_error'] = true;
                $this->return_array['import_error_string'] = $o['import_error_string'];
                continue;
            }
            //echo "<pre>",print_r($this->return_array),"</pre>";die();
            //insert the order
            $vals = array(
                'client_order_id'       => $o['client_order_id'],
                'client_id'             => 5,
                'deliver_to'            => $o['ship_to'],
                'date_ordered'          => $o['date_ordered'],
                'tracking_email'        => $o['tracking_email'],
                'weight'                => $o['weight'],
                'errors'                => $o['errors'],
                'error_string'          => $o['error_string'],
                'address'               => $o['address'],
                'address2'              => $o['address_2'],
                'state'                 => $o['state'],
                'suburb'                => $o['suburb'],
                'postcode'              => $o['postcode'],
                'country'               => $o['country'],
                'contact_phone'         => $o['contact_phone'],
                'entered_by'            => 0
            );
            if($o['signature_req'] == 1) $vals['signature_req'] = 1;
            if($o['eparcel_express'] == 1) $vals['eparcel_express'] = 1;

            $items_toparse = array($this->nuchevsampleitems[$o['client_order_id']]);
            //echo "<pre>",print_r($vals),"</pre>";
            //echo "<pre>",print_r($items_toparse),"</pre>";
            //die();

            $order_number = $this->controller->order->addOrder($vals, $items_toparse);
            $this->output .= "Inserted Order: $order_number".PHP_EOL;
            $this->output .= print_r($vals,true).PHP_EOL;
            $this->output .= print_r($this->nuchevsampleitems[$o['client_order_id']], true).PHP_EOL;
            ++$this->return_array['import_count'];
            /**/
            if(!imap_mail_move ( $this->inbox , $x , 'INBOX.processed_ok'  ))
            {
                $this->output .=  "Error moving message $x to Processed folder".PHP_EOL;
            }
            else
            {
                $this->output .=  "Moved message $x to Processed folder".PHP_EOL;
            }

        }
    }

    private function procNuchevSamples($collected_orders)
    {
        $db = Database::openConnection();
        $orders = array();
        if(count($collected_orders))
        {
            $allocations = array();
            $orders_items = array();
            foreach($collected_orders as $x => $lines)
            {
                $mail_message = $address_message = "";
                $items_errors = false;
                $weight = 0;
                $mm = "";
                $items = array();
                $error = false;
                //$o = trimArray($o);
                $order = array(
                    'error_string'          => '',
                    'items'                 => array(),
                    'ref2'                  => '',
                    'client_order_id'       => Utility::generateRandString(5),
                    'errors'                => 0,
                    'tracking_email'        => "",
                    'ship_to'               => "",
                    'company_name'          => "",
                    'date_ordered'          => time(),
                    'status_id'             => $this->controller->order->ordered_id,
                    'eparcel_express'       => 0,
                    'signature_req'         => 0,
                    'contact_phone'         => "",
                    'import_error'          => false,
                    'import_error_string'   => '',
                    'weight'                => $weight
                );
                $product = NULL;
                $suburb = $phone = $state = $postcode = $country = $address2 = $found_state = "";
                for($i = 0; $i < count($lines); ++$i)
                {
                    $line = trim($lines[$i]);
                    $next_line = isset($lines[$i + 1])? trim($lines[$i + 1]) : "";

                    if( empty($line) )
                        continue;
                    if(preg_match('/.?First Name.?/i', $line, $matches))
                    {
                        $firstname = $next_line;
                    }
                    if(preg_match('/.?Last Name.?/i', $line, $matches))
                    {
                        $lastname = $next_line;
                    }
                    if(preg_match('/.?Email.?/i', $line, $matches))
                    {
                        $email = $next_line;
                    }
                    if(preg_match('/.?Phone.?/i', $line, $matches))
                    {
                        $phone = $next_line;
                    }
                    if(preg_match('/.?Address.?/i', $line, $matches))
                    {
                        if($lines[$i + 5] == "Map It")
                        {
                            $address = $next_line;
                            $address2 = trim($lines[$i + 2]);
                            if( preg_match('/([\w \.]+), ?([\w \.]+) (\d+)/i', $lines[$i + 3], $matches) )
                            {
                                $suburb = $matches[1];
                                $state = $found_state = strtoupper($matches[2]);
                                $postcode = $matches[3];
                            }
                            $country = trim($lines[$i + 4]);
                        }
                        else
                        {
                            $address = $next_line;
                            if( preg_match('/([\w \.]+), ?([\w \.]+) (\d+)/i', $lines[$i + 2], $matches) )
                            {
                                $suburb = $matches[1];
                                $state = $found_state = strtoupper($matches[2]);
                                $postcode = $matches[3];
                            }
                            $country = trim($lines[$i + 3]);
                        }
                        //echo "<p>$state</p>";
                        $found_state = $state;
                        $state = str_replace('.', '', $state);
                        if(!in_array($state, $this->acceptable_states))
                        {
                            $state = array_search($state, $this->states);
                        }
                        $cc = $db->queryValue('countries', array('name' => $country), 'iso_code_2');
                        /**/
                        if(!$cc)
                        {
                            $cc = "AU";
                        }

                        if(strlen($address) > 40 || strlen($address2) > 40)
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>Addresses cannot have more than 40 characters</p>";
                        }
                        $aResponse = $this->controller->Eparcel->ValidateSuburb($suburb, $state, str_pad($postcode,4,'0',STR_PAD_LEFT));
                        if(isset($aResponse['errors']))
                        {
                            $order['errors'] = 1;
                            foreach($aResponse['errors'] as $e)
                            {
                                $order['error_string'] .= "<p>{$e['message']}</p>";
                            }
                        }
                        elseif($aResponse['found'] === false)
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>Postcode does not match suburb or state</p>";
                        }
                        if(!preg_match("/(?:[A-Za-z].*?\d|\d.*?[A-Za-z])/i", $address) && (!preg_match("/(?:care of)|(c\/o)|( co )/i", $address)))
                        {
                            $order['errors'] = 1;
                            $order['error_string'] .= "<p>The address is missing either a number or a word</p>";
                        }
                    }
                    if(preg_match('/.?Number of Sachets.?/i', $line, $matches))
                    {
                        $qty = $next_line;
                    }
                    if(preg_match('/.?Product.?/i', $line, $matches))
                    {
                        $sku = $next_line;
                        $product = $db->queryRow("SELECT * FROM items WHERE SKU = :sku", array('sku' => $sku));
                    }
                }//end foreach line
                if(empty($product))
                {
                    $this->output .= "No product found for $sku".PHP_EOL;
                    $order['import_error'] = true;
                    $order['import_error_string'] .= "<p>No product found for $sku</p>";
                    /**/
                    if(!imap_mail_move ( $this->inbox , $x , 'INBOX.processed_fail'  ))
                    {
                        $this->output .=  "Error moving message $x to Failed folder".PHP_EOL;
                    }
                    else
                    {
                        $this->output .=  "Moved message $x to failed folder".PHP_EOL;
                    }

                    continue;
                }
                if(empty($state))
                {
                    $this->output .= "No state found for $found_state".PHP_EOL;
                    $order['import_error'] = true;
                    $order['import_error_string'] .= "<p>No state found for $found_state</p>";
                    /* */
                    if(!imap_mail_move ( $this->inbox , $x , 'INBOX.processed_fail'  ))
                    {
                        $this->output .=  "Error moving message $x to Failed folder".PHP_EOL;
                    }
                    else
                    {
                        $this->output .=  "Moved message $x to failed folder".PHP_EOL;
                    }

                    continue;
                }
                if( $db->queryValue('nuchev_samples', array('email' => $email)))
                {
                    $this->output .= "Email address - $email - already associated with a request".PHP_EOL;
                    $order['import_error'] = true;
                    $order['import_error_string'] .= "<p>Email address - $email - already associated with a request</p>";
                    /*   */
                    if(!imap_mail_move ( $this->inbox , $x , 'INBOX.processed_fail'  ))
                    {
                        $this->output .=  "Error moving message $x to Failed folder".PHP_EOL;
                        //fwrite($handle, "Error moving message $x to Processed folder".PHP_EOL);
                    }
                    else
                    {
                        $this->output .=  "Moved message $x to failed folder".PHP_EOL;
                    }

                }
                else
                {
                    $address_details = array(
                    	'address'	=>	$address,
                        'address_2' =>  "",
                        'suburb'	=>	$suburb,
                        'state'		=>	$state,
                        'postcode'	=>	$postcode,
                        'country'	=>	$cc
                    );
                    if(!empty($address2))
                    {
                        $address_details['address_2'] = $address2;
                    }
                    $items[] = array(
                        'qty'           =>  3,
                        'id'            => $product['id'],
                        'whole_pallet'  => false
                    );
                    $orders_items[$order['client_order_id']] = $items;
                    $order['ship_to'] = ucwords($firstname." ".$lastname);
                    $order['items'] = $items;
                    $order['tracking_email'] = $email;
                    $order['contact_phone'] = $phone;
                    $order = array_merge($order, $address_details);
                    $orders[$x] = $order;
                    /**/
                    $rec_details = array(
                        'email'     =>  $email,
                        'postcode'  =>  $postcode
                    );
                    $db->insertQuery('nuchev_samples', $rec_details);
                    if(!imap_mail_move ( $this->inbox , $x , 'INBOX.processed_ok'  ))
                    {
                        $this->output .=  "Error moving message $x to Processed folder".PHP_EOL;
                    }
                    else
                    {
                        $this->output .=  "Moved message $x to Processed folder".PHP_EOL;
                    }

                }
            }

            $this->nuchevsampleitems = $this->controller->allocations->createOrderItemsArray($orders_items);

            return $orders;
        }//end if count orders
        else
        {
            $this->output .= "=========================================================================================================".PHP_EOL;
            $this->output .= "No New Samples".PHP_EOL;
            $this->output .= "=========================================================================================================".PHP_EOL;
        }
        return false;
    }



    private function getmsg($mbox,$mid)
    {
        $this->htmlmsg      = "";
        $this->plainmsg     = "";
        $this->charset      = "";
        $this->attachments  = array();

        // HEADER
        $h = imap_header($mbox,$mid);
        // add code here to get date, from, to, cc, subject...

        // BODY
        $s = imap_fetchstructure($mbox,$mid);
        //var_dump($s);die();
        if (!isset($s->parts))  // simple
            $this->getpart($mbox, $mid, $s, 0);  // pass 0 as part-number
        else
        {
            // multipart: cycle through each part
            foreach ($s->parts as $partno0 => $p)
                $this->getpart($mbox, $mid, $p, $partno0+1);
        }

    }

    private function getpart($mbox, $mid, $p, $partno) {
        // $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple

        // DECODE DATA
        $data = ($partno)?
            imap_fetchbody($mbox, $mid, $partno):  // multipart
            imap_body($mbox, $mid);  // simple
        // Any part may be encoded, even plain text messages, so check everything.
        if ($p->encoding == 4)
            $data = quoted_printable_decode($data);
        elseif ($p->encoding == 3)
            $data = base64_decode($data);

        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        $params = array();
        if (isset($p->parameters))
            foreach ($p->parameters as $x)
                $params[strtolower($x->attribute)] = $x->value;
        if (isset($p->dparameters))
            foreach ($p->dparameters as $x)
                $params[strtolower($x->attribute)] = $x->value;

        // ATTACHMENT
        // Any part with a filename is an attachment,
        // so an attached text file (type 0) is not mistaken as the message.
        if (isset($params['filename']) || isset($params['name']))
        {
            // filename may be given as 'Filename' or 'Name' or both
            $filename = (isset($params['filename']))? $params['filename'] : $params['name'];
            // filename may be encoded, so see imap_mime_header_decode()
            $this->attachments[$filename] = $data;  // this is a problem if two files have same name
        }

        // TEXT
        if ($p->type == 0 && $data)
        {
            // Messages may be split in different parts because of inline attachments,
            // so append parts together with blank row.
            if (strtolower($p->subtype) == 'plain')
                $this->plainmsg .= trim($data) ."\n\n";
            else
                $this->htmlmsg .= $data ."<br><br>";
            $this->charset = $params['charset'];  // assume all parts are same charset
        }

        // EMBEDDED MESSAGE
        // Many bounce notifications embed the original message as type 2,
        // but AOL uses type 1 (multipart), which is not handled here.
        // There are no PHP functions to parse embedded messages,
        // so this just appends the raw source to the main message.
        elseif ($p->type == 2 && $data)
        {
            $this->plainmsg .= $data."\n\n";
        }

        // SUBPART RECURSION
        if (isset($p->parts)) {
            foreach ($p->parts as $partno0 => $p2)
                $this->getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
        }
    }
}