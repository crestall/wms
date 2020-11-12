<?php
class Finishercategories extends Model{
    public $table = "finisher_categories";
    public $linked_table = "finishers_finisher_categories";

    public function getSelectFinisherCategories($selected = false, $active = 1)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $cats = $db->queryData("SELECT fc.id, fc.name  FROM {$this->table} fc WHERE active=$active ORDER BY display_order ASC, name");
        foreach($cats as $c)
        {
            $label = ucwords($c['name']);
            $value = $c['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            else
            {
                $check = "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function getMultiSelectFinisherCategories($selected = array(), $active = 1)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $cats = $db->queryData("SELECT fc.id, fc.name  FROM {$this->table} fc WHERE active=$active ORDER BY display_order ASC, name");
        foreach($cats as $c)
        {
            $label = ucwords($c['name']);
            $value = $c['id'];
            $ret_string .= "<option value='$value'";
            if(in_array($value, $selected))
            {
                $check = ($value == $selected)? "selected='selected'" : "";
                $ret_string .= " selected";
            }
            $ret_string .= ">$label</option>";
        }
        return $ret_string;
    }

    public function getCategoryName($id)
    {
        $db = Database::openConnection();
        return ucwords($db->queryValue($this->table, array('id' =>  $id), 'name'));
    }

    public function getCategoryId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue($this->table, array('name' =>  $name));
    }

    public function getCategories($active = -1)
    {
        $db = Database::openConnection();
        //$q = "SELECT * FROM {$this->table}";
        $q = "SELECT * FROM {$this->table}";
        if($active >= 0)
        {
            $q .= " WHERE active = $active";
        }
        $q .= " ORDER BY display_order ASC, name";
        return $db->queryData($q);
    }

    public function getCategoriesForFinisher($finisher_id)
    {
        $db = Database::openConnection();
        $q = "
                SELECT
                    fc.*
                FROM
                    {$this->table} fc JOIN {$this->linked_table} ffc ON fc.id = ffc.category_id
                WHERE
                    ffc.finisher_id = $finisher_id
                ORDER BY
                    fc.name
        ";
        return $db->queryData($q);
    }

    public function addCategory($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  strtolower($data['name'])
        );
        return $db->insertQuery($this->table, $vals);
    }

    public function addFinisherCategories($categories, $finisher_id)
    {
        $db = Database::openConnection();
        //remove old ones first
        $this->removeFinisherCategories($finisher_id);
        //add all the new ones
        foreach($categories as $i => $category_id)
        {
            $db->insertQuery($this->linked_table, array(
                'finisher_id'   => $finisher_id,
                'category_id'   => $category_id
            ));
        }
        return true;
    }

    public function removeFinisherCategories($finisher_id)
    {
        $db = Database::openConnection();
        $db->deleteQuery($this->linked_table, $finisher_id, 'finisher_id');
    }

    public function editCategory($data)
    {
        echo "in the model<pre>",print_r($data),"</pre>";die();
        $db = Database::openConnection();
        $vals = array(
            'name'      => strtolower($data['name'])
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $db->updateDatabaseFields($this->table, $vals, $data['line_id']);
        return true;
    }

    public function checkCategoryNames($name, $current_name)
    {
        $db = Database::openConnection();
        $name = strtoupper($name);
        $current_name = strtoupper($current_name);
        $q = "SELECT name FROM {$this->table}";
        $rows = $db->queryData($q);
        $valid = 'true';
        foreach($rows as $row)
        {
        	if($name == strtoupper($row['name']) && $name != $current_name)
        	{
        		$valid = 'false';
        	}
        }
        return $valid;
    }

    public function updateHeirarchy($cats)
    {
        $db = Database::openConnection();
        foreach($cat as $rank => $sid)
        {
            ++$rank;
            $db->updateDatabaseField($this->table, 'display_order', $rank, $sid);
        }
        return true;
    }
}
?>