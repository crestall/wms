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
        updateJobStatus($job_id, $status_id)

    */

class Productionjob extends Model{
    public $table = "production_jobs";

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

    public function getJobsForDisplay($completed = false, $cancelled = false)
    {
        $db = Database::openConnection();
        $q = "
            SELECT
                pj.*,
                pc.id AS customer_id, pc.name AS customer_name, pc.contact AS customer_contact, pc.email AS customer_email, pc.phone AS customer_phone,
                sr.id as salesrep_id, sr.name AS salesrep_name,
                ps.id as supplier_id, ps.name AS supplier_name, ps.contact AS supplier_contact, ps.email AS supplier_email, ps.phone AS supplier_phone,
                js.name AS `status`, js.colour AS status_colour, js.text_colour AS status_text_colour
            FROM
                `production_jobs` pj LEFT JOIN
                `production_customers` pc ON pj.customer_id = pc.id LEFT JOIN
                `sales_reps` sr ON pj.salesrep_id = sr.id LEFT JOIN
                `production_suppliers` ps ON pj.supplier_id = ps.id LEFT JOIN
                job_status js ON pj.status_id = js.id
        ";
        if($completed)
        {
            $q .= " WHERE pj.status_id = 9";
        }
        else
        {
            $q .= " WHERE pj.status_id != 9";
        }
        if($cancelled)
        {
            $q .= " AND pj.status_id = 11";
        }
        else
        {
            $q .= " AND pj.status_id != 11";
        }
        $q .= "
            ORDER BY
                pj.due_date DESC
        ";
        return $db->queryData($q);
    }

    public function getJobById($id = 0)
    {
        $db = Database::openConnection();
        return $db->queryById($this->table, $id);
    }

    public function addJob($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'job_id'        => $data['job_id'],
            'customer_id'   => $data['customer_id'],
            'description'   => $data['description'],
            'created_date'  => $data['date_entered_value'],
            'status_id'     => $data['status_id'],
            'date'          => time()
        );
        if(!empty($data['previous_job_id'])) $vals['previous_job_id'] = $data['previous_job_id'];
        if(!empty($data['date_ed_value'])) $vals['ed_date'] = $data['date_ed_value'];
        if(!empty($data['date_due_value'])) $vals['due_date'] = $data['date_due_value'];
        if(!empty($data['supplier_id'])) $vals['supplier_id'] = $data['supplier_id'];
        if(!empty($data['salesrep_id'])) $vals['salesrep_id'] = $data['salesrep_id'];
        if(!empty($data['designer'])) $vals['designer'] = $data['designer'];
        if(!empty($data['notes'])) $vals['notes'] = $data['notes'];
        $id = $db->insertQuery($this->table, $vals);
        return $id;
    }

    public function updateJobDetails($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'job_id'        => $data['job_id'],
            'description'   => $data['description'],
            'created_date'  => $data['date_entered_value'],
            'due_date'      => null,
            'status_id'     => $data['status_id']
        );
        if(!empty($data['previous_job_id'])) $vals['previous_job_id'] = $data['previous_job_id'];
        if(!empty($data['date_due_value'])) $vals['due_date'] = $data['date_due_value'];
        if(!empty($data['salesrep_id'])) $vals['salesrep_id'] = $data['salesrep_id'];
        if(!empty($data['designer'])) $vals['designer'] = $data['designer'];
        if(!empty($data['notes'])) $vals['notes'] = $data['notes'];
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
        $db->updateDatabaseField($this->table, 'status_id', $status_id, $job_id);
        return true;
    }

    public function updateJobSupplierId($job_id, $supplier_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'supplier_id', $supplier_id, $job_id);
        return true;
    }

    public function updateJobCustomerId($job_id, $customer_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'customer_id', $customer_id, $job_id);
        return true;
    }

    public function updateExpectedDeliveryDate($job_id, $edd)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'ed_date', $edd, $job_id);
        return true;
    }

    public function updateDueDate($job_id, $due_date)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'due_date', $due_date, $job_id);
        return true;
    }

    public function removeSupplier($job_id)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField($this->table, 'supplier_id', 0, $job_id);
        return true;
    }

    public function getSearchResults($args)
    {
        extract($args);
        $db = Database::openConnection();
        //echo "<pre>CUSTOMER IDS",print_r($customer_ids),"</pre>";die();
        $query = "
            SELECT
                pj.*,
                pc.id AS customer_id, pc.name AS customer_name, pc.contact AS customer_contact, pc.email AS customer_email, pc.phone AS customer_phone,
                sr.id as salesrep_id, sr.name AS salesrep_name,
                ps.id as supplier_id, ps.name AS supplier_name, ps.contact AS supplier_contact, ps.email AS supplier_email, ps.phone AS supplier_phone,
                js.name AS `status`, js.colour AS status_colour, js.text_colour AS status_text_colour
            FROM
                `production_jobs` pj LEFT JOIN
                `production_customers` pc ON pj.customer_id = pc.id LEFT JOIN
                `sales_reps` sr ON pj.salesrep_id = sr.id LEFT JOIN
                `production_suppliers` ps ON pj.supplier_id = ps.id LEFT JOIN
                job_status js ON pj.status_id = js.id
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
                        ps.name LIKE :term16 OR
                        ps.contact LIKE :term17 OR
                        ps.email LIKE :term18 OR
                        ps.phone LIKE :term19 OR
                        ps.address LIKE :term20 OR
                        ps.address_2 LIKE :term21 OR
                        ps.suburb LIKE :term22 OR
                        ps.state LIKE :term23 OR
                        ps.postcode LIKE :term24 OR
                        ps.country LIKE :term25
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
            $query .= " AND (pj.customer_id IN( $c_ids)";
            //$array['customer_id'] = $customer_id;
        }
        if($supplier_id > 0)
        {
            $query .= " AND (pj.supplier_id = :supplier_id)";
            $array['supplier_id'] = $supplier_id;
        }
        if($salesrep_id > 0)
        {
            $query .= " AND (pj.salesrep_id = :salesrep_id)";
            $array['salesrep_id'] = $salesrep_id;
        }
        if($status_id > 0)
        {
            $query .= " AND (pj.status_id = :status_id)";
            $array['status_id'] = $status_id;
        }
        //print_r($array);
        //die($query);
        return $jobs = $db->queryData($query, $array);
    }
}
?>