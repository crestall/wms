<?php
class Dfbooking extends Model{
    public $table = "df_bookings";

    public function addBooking($data)
    {
        $db = Database::openConnection();
        $vals = [
            "receiver_name"         => $data['receiver_details']['ReceiverName'],
            "receiver_contact_name" => $data['receiver_details']['ReceiverContactName'],
            "date_shipped"          => time(),
            "signature_req"         => ($data['receiver_details']['IsAuthorityToLeave'])? 0:1,
            "consignment_id"        => $data['consignment']['Connote'],
            "other_charges"         => ($data['charge']['OtherCharge'] + $data['charge']['surcharge']),
            "postage_charge"        => $data['charge']['FreightCharge'],
            "fuel_levee"            => $data['charge']['FuelLevyCharge'],
            "instructions"          => $data['receiver_details']['DeliveryInstructions'],
            "label_url"             => $data['consignment']['label_url'],
            "address"               => $data['receiver_details']['AddressLine1'],
            "suburb"                => $data['receiver_details']['Suburb'],
            "state"                 => $data['receiver_details']['State'],
            "postcode"              => $data['receiver_details']['Postcode'],
            "entered_by"            => Session::getUserId()
        ];
        if(!empty($data['receiver_details']['ReceiverContactEmail']))
            $vals['tracking_email'] = $data['receiver_details']['ReceiverContactEmail'];
        if(!empty($data['receiver_details']['AddressLine2']))
            $vals['address_2'] = $data['receiver_details']['AddressLine2'];
        if(!empty($data['receiver_details']['ReceiverContactMobile']))
            $vals['contact_phone'] = $data['receiver_details']['ReceiverContactMobile'];
        if(!empty($data['receiver_details']['DeliveryInstructions']))
            $vals['instructions'] = $data['receiver_details']['DeliveryInstructions'];
        $booking_id = $db->insertQuery($this->table, $vals);
        //echo "<pre>",print_r($vals),"<pre>";die();
        return $booking_id;
    }

    public function getBookingById($id)
    {
        $db = Database::openConnection();
        $booking = $db->queryById($this->table, $id);
        return (empty($booking))? false : $booking;
    }
}
?>