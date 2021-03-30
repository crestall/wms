<?php

/**
    * Prodution Job Class
    *

    * @author     Mark Solly <mark.solly@fsg.com.au>

        FUNCTIONS

        addJob($data)
        editJob($data)
        getAllJobs($status_id = 0)
        getJobById($id = 0)
        jobNumberExists($job_number)
        updateJobStatus($job_id, $status_id)

    */

class Productionjob extends Model{
    public $table = "production_jobs";
    public $finishers_table = "production_jobs_finishers";

    public function updateJobAddress($data)
    {
        //echo "<pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $vals = array(
            'delivery_instructions' => NULL,
            'ship_to'               => NULL,
            'attention'             => NULL,
            'address'               => NULL,
            'address_2'             => NULL,
            'suburb'                => NULL,
            'state'                 => NULL,
            'postcode'              => NULL,
            'country'               => 'AU'
        );
        if(!empty($data['delivery_instructions'])) $vals['delivery_instructions'] = $data['delivery_instructions'];
        if(!empty($data['ship_to'])) $vals['ship_to'] = $data['ship_to'];
        if(!empty($data['attention'])) $vals['attention'] = $data['attention'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['job_id']);
    }

    public function getStrictDueDateJobs()
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                pj.*,
                pc.name AS customer_name
            FROM
                production_jobs pj LEFT JOIN
                `production_customers` pc ON pj.customer_id = pc.id
            WHERE
                pj.strict_dd = 1 AND
                pj.status_id != 9
            ORDER BY
                pj.due_date ASC
        ";
        return $db->queryData($q);
    }

    public function jobNumberExists($job_number)
    {
        $db = Database::openConnection();
        return $db->fieldValueTaken($this->table, $job_number, 'job_id');
    }

    public function checkJobIds($jobid, $current_jobid)
    {
        $db = Database::openConnection();
        //$sku = strtoupper($sku);
        //$current_sku = strtoupper($current_sku);
        $q = "SELECT job_id FROM {$this->table}";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($jobid == $row['job_id'] && $jobid != $current_jobid)
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function getAllJobs($status_id = 0)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM {$this->table} ";
        if($status_id > 0)
        {
            $q .= "WHERE status_id = $status_id ";
        }
        $q .= "ORDER BY created_date";
        return $db->queryData($q);
    }

    public function getJobsForPDF($ids)
    {
        $db = Database::openConnection();
        $q = $this->getJobQuery();
        $q .= "
            WHERE pj.id IN($ids)
            GROUP BY pj.id
            ORDER BY pj.created_date DESC
        ";
        return $db->queryData($q);
    }

    public function getJobsForDisplay($args)
    {
        $db = Database::openConnection();
        $q = $this->getJobQuery();
        $defaults = array(
            'completed'	    => false,
            'cancelled'	    => false,
            'customer_ids'	=> array(),
            'finisher_ids'  => array(),
            'salesrep_ids'  => array(),
            'status_ids'    => array()
        );
        $args = array_merge($defaults, $args);
        extract($args);
        if($completed)
        {
            $q .= " WHERE pj.status_id = 9";
            $status_ids = array();
        }
        elseif($cancelled)
        {
            $q .= " WHERE pj.status_id = 11";
            $status_ids = array();
        }
        elseif(count($status_ids))
        {
            $st_ids = implode(',',$status_ids);
            $q .= " WHERE (pj.status_id IN( $st_ids))";
        }
        else
        {
            $q .= " WHERE (pj.status_id != 9 AND pj.status_id != 11)";
        }
        if(count($customer_ids))
        {
            $c_ids = implode(',',$customer_ids);
            $q .= " AND (pj.customer_id IN( $c_ids))";
        }
        if(count($finisher_ids))
        {
            $f_ids = implode(',',$finisher_ids);
            $q .= " AND (pj.finisher_id IN( $f_ids))";
        }
        if(count($salesrep_ids))
        {
            $sr_ids = implode(',',$salesrep_ids);
            $q .= " AND (pj.salesrep_id IN( $sr_ids))";
        }

        $q .= "
            GROUP BY
                pj.id
            ORDER BY
                js.ranking ASC, pj.created_date DESC, pj.job_id DESC
        ";
        //die($q);
        return $db->queryData($q);
    }

    public function getJobById($id = 0)
    {
        $db = Database::openConnection();
        //return $db->queryById($this->table, $id);
        $q = $this->getJobQuery();
        $q .= "
            WHERE
                pj.id = $id
            GROUP BY
                pjf.job_id
        ";
        return $db->queryRow($q);
    }

    public function addJob($data)
    {
        //echo "<pre>",print_r($data),"</pre>"; die();
        $db = Database::openConnection();
        $vals = array(
            'job_id'        => $data['job_id'],
            'customer_id'   => $data['customer_id'],
            'description'   => $data['description'],
            'created_date'  => $data['date_entered_value'],
            'status_id'     => $data['status_id'],
            'date'          => time()
        );
        $vals['strict_dd'] = (isset($data['strict_dd']))? 1 : 0;
        if(!empty($data['ship_to'])) $vals['ship_to'] = $data['ship_to'];
        if(!empty($data['date_due_value'])) $vals['due_date'] = $data['date_due_value'];
        if(!empty($data['attention'])) $vals['attention'] = $data['attention'];
        if(!empty($data['delivery_instructions'])) $vals['delivery_instructions'] = $data['delivery_instructions'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        if(!empty($data['previous_job_id'])) $vals['previous_job_id'] = $data['previous_job_id'];
        if(!empty($data['related_job_id'])) $vals['related_job_id'] = $data['related_job_id'];
        if(isset($data['customer_contact_id']) && $data['customer_contact_id'] > 0) $vals['customer_contact_id'] = $data['customer_contact_id'];
        if(!empty($data['salesrep_id'])) $vals['salesrep_id'] = $data['salesrep_id'];
        if(!empty($data['designer'])) $vals['designer'] = $data['designer'];
        if(!empty($data['notes'])) $vals['notes'] = $data['notes'];
        if(!empty($data['delivery_notes'])) $vals['delivery_notes'] = $data['delivery_notes'];
        if(!empty($data['priority'])) $vals['priority'] = $data['priority'];
        if(!empty($data['customer_po_number'])) $vals['customer_po_number'] = $data['customer_po_number'];
        $id = $db->insertQuery($this->table, $vals);
        if(isset($data['finishers']) && is_array($data['finishers']))
        {
            foreach($data['finishers'] as $finisher)
            {
                $ed_date = (empty($finisher['ed_date_value']))? 0 : $finisher['ed_date_value'];
                $po = (empty($finisher['purchase_order']))? NULL : $finisher['purchase_order'];
                $this->addFinisherToJob($id, array(
                    'finisher_id'       => $finisher['finisher_id'],
                    'contact_id'        => $finisher['contact_id'],
                    'purchase_order'    => $po,
                    'ed_date'           => $ed_date
                ));
            }
        }
        return $id;
    }

    public function addFinisherToJob($job_id, $data)
    {
        $db = Database::openConnection();
        $ed_date = ( isset($data['ed_date_value']) && !empty($data['ed_date_value']) )? $data['ed_date_value'] : 0;
        $contact_id = ( isset($data['contact_id']) && !empty($data['contact_id']) )? $data['contact_id'] : 0;
        $po = ( isset($data['purchase_order']) && !empty($data['purchase_order']))? $data['purchase_order'] : NULL ;
        $db->insertQuery($this->finishers_table, array(
            'job_id'            => $job_id,
            'finisher_id'       => $data['finisher_id'],
            'contact_id'        => $contact_id,
            'purchase_order'    => $po,
            'ed_date'           => $ed_date
        ));
    }

    public function removeFinishersFromJob($job_id)
    {
        $db = Database::openConnection();
        $db->deleteQuery($this->finishers_table, $job_id, 'job_id');
        return true;
    }

    public function updateJobDetails($data)
    {
        //echo "<pre>",print_r($data),"</pre>"; die();
        $current_statusid = $this->getJobStatusId($data['id']);
        if($current_statusid != $data['status_id'])
        {
            $this->updateJobStatus($data['id'], $data['status_id']);
        }
        $db = Database::openConnection();
        $vals = array(
            'job_id'                => $data['job_id'],
            'description'           => $data['description'],
            'created_date'          => $data['date_entered_value'],
            'due_date'              => 0,
            'priority'              => 0,
            'notes'                 => null,
            'delivery_notes'        => null,
            'customer_po_number'    => null
        );
        $vals['strict_dd'] = (isset($data['strict_dd']))? 1 : 0;
        if(!empty($data['previous_job_id'])) $vals['previous_job_id'] = $data['previous_job_id'];
        if(!empty($data['date_due_value'])) $vals['due_date'] = $data['date_due_value'];
        if(!empty($data['salesrep_id'])) $vals['salesrep_id'] = $data['salesrep_id'];
        if(!empty($data['designer'])) $vals['designer'] = $data['designer'];
        if(!empty($data['notes'])) $vals['notes'] = $data['notes'];
        if(!empty($data['delivery_notes'])) $vals['delivery_notes'] = $data['delivery_notes'];
        if(!empty($data['priority'])) $vals['priority'] = $data['priority'];
        if(!empty($data['customer_po_number'])) $vals['customer_po_number'] = $data['customer_po_number'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['id']);
        return $id;
    }

    public function editJob($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'          =>  strtolower($data['name']),
            'email'         =>  null,
            'contact'       =>  null,
            'phone'         =>  null,
            'address'       =>  null,
            'address_2'     =>  null,
            'suburb'        =>  null,
            'state'         =>  null,
            'postcode'      =>  null,
            'country'       =>  null
        );
        $vals['active'] = isset($data['active'])? 1 : 0;
        $vals['strict_dd'] = (isset($data['strict_dd']))? 1 : 0;
        if(!empty($data['email'])) $vals['email'] = $data['email'];
        if(!empty($data['contact'])) $vals['contact'] = $data['contact'];
        if(!empty($data['phone'])) $vals['phone'] = $data['phone'];
        if(!empty($data['address'])) $vals['address'] = $data['address'];
        if(!empty($data['address2'])) $vals['address_2'] = $data['address2'];
        if(!empty($data['suburb'])) $vals['suburb'] = $data['suburb'];
        if(!empty($data['state'])) $vals['state'] = $data['state'];
        if(!empty($data['postcode'])) $vals['postcode'] = $data['postcode'];
        if(!empty($data['country'])) $vals['country'] = $data['country'];
        $id = $db->updateDatabaseFields($this->table, $vals, $data['supplier_id']);
        return $id;
    }

    public function updateJobStatus($job_id, $status_id)
    {
        $db = Database::openConnection();
        $new_vals = array(
            'status_id'             => $status_id,
            'status_change_time'    => time(),
            'status_change_by'      => Session::getUserId()
        );
        $db->updateDatabaseFields($this->table, $new_vals, $job_id);
        if( !(Session::getUserId() == 176 || Session::getUserId() == 178) )
        {
            //If Andrea or Megan didn't do the change, notify them of it
            $sd = $db->queryByID('job_status', $status_id);
            $jd = $db->queryByID('production_jobs', $job_id);
            Email::notifyStatusChange($jd['job_id'], ucwords($sd['name']), Session::getUsersName());
        }
        if($status_id == 8 || $status_id == 26 || $status_id == 22 || $status_id == 24)
        {
            //notify FSG contact of status change
            $sd = $db->queryByID('job_status', $status_id);
            $jd = $db->queryByID('production_jobs', $job_id);
            if( $jd['salesrep_id'] > 0 )
            {
                $pcd = $db->queryById('sales_reps', $jd['salesrep_id']);
                $cd = $db->queryById('production_customers', $jd['customer_id']);
                Email::notifyProdContactOfStatusChange($jd['job_id'],ucwords($cd['name']), ucwords($sd['name']), $pcd['email'], ucwords($pcd['name']));
            }
        }
        return true;
    }

    public function updateJobPriority($job_id, $priority)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table,"priority",$priority,$job_id);
        return true;
    }

    public function updateJobFinisherId($job_id, $finisher_id, $finisher_number = "")
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'finisher'.$finisher_number.'_id', $finisher_id, $job_id);
        return true;
    }

    public function updateJobFinisherPo($job_id, $po, $finisher_number = "")
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'finisher'.$finisher_number.'_po', $po, $job_id);
        return true;
    }

    public function updateJobCustomerId($job_id, $customer_id, $customer_contact_id = 0)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'customer_id', $customer_id, $job_id);
        if( $customer_contact_id > 0 )
            $db->updateDatabaseField($this->table, 'customer_contact__id', $customer_contact_id, $job_id);
        return true;
    }

    public function updateExpectedDeliveryDate($job_id, $edd, $fn = "")
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'ed'.$fn.'_date', $edd, $job_id);
        return true;
    }

    public function updateDueDate($job_id, $due_date)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'due_date', $due_date, $job_id);
        return true;
    }

    public function removeFinisher($job_id, $fn = "")
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'finisher'.$fn.'_id', 0, $job_id);
        return true;
    }

    public function removeFinisherPo($job_id, $fn = "")
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'finisher'.$fn.'_po', NULL, $job_id);
        return true;
    }

    public function getSearchResults($args)
    {
        extract($args);
        $db = Database::openConnection();
        //echo "<pre>CUSTOMER IDS",print_r($customer_ids),"</pre>";die();
        $query = $this->getJobQuery();
        $query .= "
            WHERE
        ";
        $array = array();
        if(!empty($term))
        {
            $query .= "(pj.designer LIKE :term1 OR
                        pj.notes LIKE :term2 OR
                        pj.job_id LIKE :term3 OR
                        pj.previous_job_id LIKE :term4 OR
                        pj.description LIKE :term5 OR
                        pc.name LIKE :term6 OR
                        pc.contact LIKE :term7 OR
                        pc.email LIKE :term8 OR
                        pc.phone LIKE :term9 OR
                        pc.address LIKE :term10 OR
                        pc.address_2 LIKE :term11 OR
                        pc.suburb LIKE :term12 OR
                        pc.state LIKE :term13 OR
                        pc.postcode LIKE :term14 OR
                        pc.country LIKE :term15 OR
                        pf.name LIKE :term16 OR
                        pf.contact LIKE :term17 OR
                        pf.email LIKE :term18 OR
                        pf.phone LIKE :term19 OR
                        pf.address LIKE :term20 OR
                        pf.address_2 LIKE :term21 OR
                        pf.suburb LIKE :term22 OR
                        pf.state LIKE :term23 OR
                        pf.postcode LIKE :term24 OR
                        pf.country LIKE :term25
                        ) AND";
            for($i = 1; $i <= 25; ++$i)
            {
                $array['term'.$i] = "%".$term."%";
            }
        }

        $date_to_value = ($date_to_value == 0)? $date_to_value = time(): $date_to_value;
        $query .= "(pj.created_date < :to)";
        $array['to'] = $date_to_value;
        if($date_from_value > 0)
        {
            $query .= " AND (pj.created_date > :from)";
            $array['from'] = $date_from_value;
        }
        if(count($customer_ids))
        {
            $c_ids = implode(',',$customer_ids);
            $query .= " AND (pj.customer_id IN( $c_ids))";
        }
        if(count($supplier_ids))
        {
            $s_ids = implode(',',$supplier_ids);
            $query .= " AND (pj.supplier_id IN( $s_ids))";
        }
        if(count($salesrep_ids))
        {
            $sr_ids = implode(',',$salesrep_ids);
            $query .= " AND (pj.salesrep_id IN( $sr_ids))";
        }
        if(count($status_ids))
        {
            $st_ids = implode(',',$status_ids);
            $query .= " AND (pj.status_id IN( $st_ids))";
        }
        $query .= "
            GROUP BY
                pj.id
        ";
        //print_r($array);
        //die($query);
        return $jobs = $db->queryData($query, $array);
    }

    public function getJobStatusId($job_id)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('id' => $job_id), 'status_id');
    }

    private function getJobQuery()
    {
        return "
            SELECT
                pj.*,
                pc.id AS customer_id, pc.name AS customer_name, pc.email AS customer_email, pc.phone AS customer_phone,
                pcc.name AS contact_name, pcc.email AS contact_email, pcc.phone AS contact_phone, pcc.role AS contact_role,
                sr.id as salesrep_id, sr.name AS salesrep_name,
                GROUP_CONCAT(
                    IFNULL(pf.id,''),',',
                    IFNULL(pf.name,''),',',
                    IFNULL(pf.email,''),',',
                    IFNULL(pf.phone,''),',',
                    IFNULL(pf.address,''),',',
                    IFNULL(pf.address_2,''),',',
                    IFNULL(pf.suburb,''),',',
                    IFNULL(pf.state,''),',',
                    IFNULL(pf.postcode,''),',',
                    IFNULL(pf.country,''),',',
                    IFNULL(pfc.id,''),',',
                    IFNULL(pfc.name,''),',',
                    IFNULL(pfc.email,''),',',
                    IFNULL(pfc.phone,''),',',
                    IFNULL(pfc.role,''),',',
                    IFNULL(pjf.purchase_order,''),',',
                    IFNULL(pjf.ed_date,'')
                    SEPARATOR '|'
                ) AS finishers,
                js.name AS `status`, js.colour AS status_colour, js.text_colour AS status_text_colour, js.ranking,
                IFNULL(rs.id, 0) AS runsheet_id, IFNULL(rs.printed, 0) AS printed, rs.runsheet_day, IFNULL(rs.runsheet_completed, 0) AS runsheet_completed, rs.driver_id
            FROM
                (SELECT `production_jobs`.*, `users`.`name` AS `status_change_name` FROM `production_jobs` LEFT JOIN `users` ON `production_jobs`.`status_change_by` = `users`.`id`) pj LEFT JOIN
                `production_customers` pc ON pj.customer_id = pc.id LEFT JOIN
                `production_contacts` pcc ON pj.customer_contact_id = pcc.id LEFT JOIN
                `production_jobs_finishers` pjf ON pj.id = pjf.job_id LEFT JOIN
                `production_finishers` pf ON pjf.finisher_id = pf.id LEFT JOIN
                `production_contacts` pfc ON pjf.contact_id = pfc.id LEFT JOIN
                `sales_reps` sr ON pj.salesrep_id = sr.id LEFT JOIN
                job_status js ON pj.status_id = js.id LEFT JOIN
                (SELECT runsheets.id, runsheet_tasks.printed, runsheet_tasks.job_id, runsheets.runsheet_day, runsheet_tasks.driver_id, runsheet_tasks.completed AS runsheet_completed FROM runsheets JOIN runsheet_tasks ON runsheets.id = runsheet_tasks.runsheet_id JOIN production_jobs ON runsheet_tasks.job_id = production_jobs.id) rs ON rs.job_id = pj.id
        ";
    }
}
?>