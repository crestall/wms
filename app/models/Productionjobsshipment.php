<?php
/**
    * Prodution Job Shipment Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        PUBLIC FUNCTIONS
        addPackage($data)
        deletePackage($id)
        enterJobShipmentAddress($data)
        getDispatchesCount($job_id)
        getJobShipments($id = 0, $dispatched = -1)
        getJobShipmentsTotal($dispatched = -1)
        getJobsWithShipments($dispatched = -1)
        getPackagesForJob($job_id, $shipment_id = 0)
        getPartShipmentDetailsForJob($job_id)
        getShipmentForJob($job_id, $shipment_id)
        getUnDispatchesCount($job_id)
        updateJobShipmentAddress($data)

        PRIVATE FUNCTIONS
        getDispatchCount($job_id, $dispatched)

    */
class Productionjobsshipment extends Model{
    public $table = "production_jobs_shipments";
    public $packages_table = "production_jobs_shipments_packages";

    public function enterJobShipmentAddress($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'ship_to'   => $data['ship_to'],
            'address'   => $data['address'],
            'suburb'    => $data['suburb'],
            'state'     => $data['state'],
            'postcode'  => $data['postcode'],
            'country'   => $data['country'],
            'job_id'    => $data['job_id']
        );
        if(!empty($data['delivery_instructions'])) $vals['delivery_instructions'] = $data['delivery_instructions'];
        if(!empty($data['attention'])) $vals['attention'] = $data['attention'];
        if(!empty($data['tracking_email'])) $vals['tracking_email'] = $data['tracking_email'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(isset($data['signature_req'])) $vals['signature_required'] = 1;
        $db->insertQuery($this->table, $vals);
    }

    public function updateJobShipmentAddress($data)
    {
        //echo "<pre>",print_r($data),"</pre>";//die();
        $db = Database::openConnection();
        $vals = array(
            'ship_to'               => $data['ship_to'],
            'address'               => $data['address'],
            'suburb'                => $data['suburb'],
            'state'                 => $data['state'],
            'postcode'              => $data['postcode'],
            'country'               => $data['country'],
            'job_id'                => $data['job_id'],
            'delivery_instructions' => NULL,
            'attention'             => NULL,
            'address_2'             => NULL,
            'tracking_email'        => NULL,
            'signature_required'    => 0
        );
        if(!empty($data['delivery_instructions'])) $vals['delivery_instructions'] = $data['delivery_instructions'];
        if(!empty($data['attention'])) $vals['attention'] = $data['attention'];
        if(!empty($data['tracking_email'])) $vals['tracking_email'] = $data['tracking_email'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(isset($data['signature_req'])) $vals['signature_required'] = 1;
        //echo "<pre>",print_r($vals),"</pre>";die();
        $db->updateDatabaseFields('production_jobs_shipments', $vals, $data['shipment_id']);
    }

    public function addPackage($data)
    {
        $db = Database::openConnection();
        $values = array(
            'job_id'        =>  $data['job_id'],
            'shipment_id'   =>  $data['shipment_id'],
            'width'         =>  $data['width'],
            'height'        =>  $data['height'],
            'depth'         =>  $data['depth'],
            'weight'        =>  $data['weight'],
            'count'         =>  $data['count'],
            'pallet'        =>  0
        );
        if(isset($data['pallet']))
            $values['pallet'] = 1;
        return $db->insertQuery($this->packages_table, $values);
    }

    public function deletePackage($id)
    {
        $db = Database::openConnection();
        $db->deleteQuery($this->packages_table, $id);
    }

    public function getUnDispatchesCount($job_id)
    {
        return $this->getDispatchCount($job_id, 0);
    }

    public function getDispatchesCount($job_id)
    {
        return $this->getDispatchCount($job_id, 1);
    }

    public function getJobsWithShipments($dispatched = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT pj.*
            FROM production_jobs pj JOIN ".$this->table." pjs ON pj.id = pjs.job_id
        ";
        if($dispatched > -1)
            $q .= " WHERE pjs.dispatched = $dispatched";
        return $db->queryData($q);
    }

    public function getPackagesForJob($job_id, $shipment_id = 0)
    {
        $db = Database::openConnection();
        return $db->queryData("
            SELECT * FROM ".$this->packages_table." WHERE job_id = $job_id AND shipment_id = $shipment_id
        ");
    }

    public function getShipmentForJob($job_id, $shipment_id)
    {
        $db = Database::openConnection();
        return($db->queryRow("
            SELECT pjs.*, pj.job_id AS job_number FROM `".$this->table."` pjs JOIN production_jobs pj ON pjs.job_id = pj.id WHERE pjs.job_id = $job_id AND pjs.id = $shipment_id LIMIT 1
        "));
    }

    public function getPartShipmentDetailsForJob($job_id)
    {
        $db = Database::openConnection();
        return($db->queryRow("
            SELECT * FROM `".$this->table."` WHERE job_id = $job_id AND courier_id = 0 AND dispatched = 0 LIMIT 1
        "));
        /*
        if(!$shipment_id = $db->queryValue('production_jobs_shipments', array('job_id' => $job_id, 'courier_id' => 0, 'dispatched' => 0)))
            $shipment_id = 0;

        return $shipment_id;
        */
    }

    public function getJobShipments($id = 0, $dispatched = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                pj.id AS job_id, pj.job_id AS job_number,
                pjs.*,
                GROUP_CONCAT(
                    IFNULL(pjsp.id,''),'|',
                    IFNULL(pjsp.width,''),'|',
                    IFNULL(pjsp.height,''),'|',
                    IFNULL(pjsp.depth,''),'|',
                    IFNULL(pjsp.weight,''),'|',
                    IFNULL(pjsp.count,''),'|',
                    IFNULL(pjsp.pallet,''),'|'
                    SEPARATOR '~'
                ) AS packages
            FROM
                `production_jobs` pj LEFT JOIN
                `production_jobs_shipments` pjs ON pj.id = pjs.job_id LEFT JOIN
                `production_jobs_shipments_packages` pjsp ON pjsp.shipment_id = pjs.id
            WHERE
                pj.id = $id
        ";
        if($dispatched > -1)
            $q .= " AND pjs.dispatched = $dispatched";
        $q .= "
            GROUP BY
                pjs.job_id
        ";
        return $db->queryData($q);
    }

    public function getJobShipmentsTotal($dispatched = -1)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                pj.id AS job_id, pj.job_id AS job_number,
                GROUP_CONCAT(
                    pjs.the_courier_name,'|',
                    pjs.handling_charge,'|',
                    pjs.postage_charge, '|',
                    pjs.gst,'|',
                    pjs.total_charge,'|',
                    pjs.consignment_id,'|'
                    SEPARATOR '~'
                ) AS shipments
            FROM
                `production_jobs` pj JOIN
                (SELECT production_jobs_shipments.*,IFNULL(production_jobs_shipments.courier_name,couriers.name) AS the_courier_name FROM production_jobs_shipments JOIN couriers ON production_jobs_shipments.courier_id = couriers.id) pjs ON pjs.job_id = pj.id
            ";
        if($dispatched > -1)
            $q .= " WHERE pjs.dispatched = $dispatched ";
        $q .= "
            GROUP BY
                pj.id
        ";
        return $db->queryData($q);
    }

    //Private functions
    private function getDispatchCount($job_id, $dispatched)
    {
        return $db->countData($this->table, array('job_id' => $job_id, 'dispatched' => $dispatched));
    }

}//end class

?>