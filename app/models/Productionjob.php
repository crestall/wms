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
                pj.*, pc.name, sr.name AS salesrep_name, ps.name AS supplier_name, js.name AS `status`
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
            'due_date'      => $data['date_due_value'],
            'ed_date'       => $data['date_ed_value'],
            'status_id'     => $data['status_id'],
            'date'          => time()
        );
        if(!empty($data['previous_job_id'])) $vals['previous_job_id'] = $data['previous_job_id'];
        if(!empty($data['supplier_id'])) $vals['supplier_id'] = $data['supplier_id'];
        if(!empty($data['salesrep_id'])) $vals['salesrep_id'] = $data['salesrep_id'];
        if(!empty($data['designer'])) $vals['designer'] = $data['designer'];
        if(!empty($data['notes'])) $vals['notes'] = $data['notes'];
        $id = $db->insertQuery($this->table, $vals);
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

}
?>