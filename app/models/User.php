<?php

 /**
  * User Class
  *
  
  * @author     Mark Solly <mark.solly@3plplus.com.au>
  */

class User extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "users";

    /**
     * returns an associative array holds the user info(image, name, id, ...etc.)
     *
     * @access public
     * @param  integer $userId
     * @return array Associative array of current user info/data.
     * @throws Exception if $userId is invalid.
     */
    public function getProfileInfo($userId){

        $db = Database::openConnection();

        $user = $db->queryById('users', $userId);

        if(empty($user))
        {
            throw new Exception("User ID " .  $userId . " doesn't exists");
        }

        $user["id"]    = (int)$user["id"];
        $user["image"] = PUBLIC_ROOT . "images/profile_pictures/" . $user['profile_picture'];

        return $user;
    }

    public function getUserByEmail($email)
    {
        $db = Database::openConnection();
        return ($db->queryRow("SELECT * FROM users WHERE email = :email LIMIT 1", array("email" => $email)));
    }

    public function updateProfileInfo($data, $userId)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'  =>  $data['name']
        );
        if(isset($data['image_name'])) $vals['profile_picture'] = $data['image_name'].".jpg";
        elseif(isset($data['delete_image'])) $vals['profile_picture'] = 'default.png';
        if(isset($data['hashed_password']))
        {
            $vals['hashed_password'] = $data['hashed_password'];
            $vals['password_change'] = time();
        }
        $db->updateDatabaseFields($this->table, $vals, $userId);
        return true;
    }

    public function addUser($data)
    {
        //echo "<pre>",print_r($data),"</pre>"; die();
        $db = Database::openConnection();
        $vals = array(
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role_id'   => $data['role_id'],
            'client_id' => $data['client_id']
        );
        if(isset($data['test_user']))
        {
            $vals['hashed_password'] = password_hash('3PLPlus', PASSWORD_DEFAULT, array('cost' => Config::get('HASH_COST_FACTOR')));
        }
        $user_id = $db->insertQuery($this->table, $vals);
        return $user_id;
    }


    public function reportBug($userId, $subject, $label, $message){

        $validation = new Validation();
        if(!$validation->validate([
            "Subject" => [$subject, "required|minLen(4)|maxLen(80)"],
            "Label" => [$label, "required|inArray(".Utility::commas(["bug", "feature", "enhancement"]).")"],
            "Message" => [$message, "required|minLen(4)|maxLen(1800)"]])){

            $this->errors = $validation->errors();
            return false;
          }

        $curUser = $this->getProfileInfo($userId);
        $data = ["subject" => $subject, "label" => $label, "message" => $message];

        // email will be sent to the admin
        Email::sendEmail(Config::get('EMAIL_REPORT_BUG'), Config::get('ADMIN_EMAIL'), ["id" => $userId, "name" => $curUser["name"]], $data);

        return true;
    }

    public function getAllUsers( $role = "" )
    {
        $db = Database::openConnection();
        $array = array();
        $query = "SELECT * FROM users";
        if(!empty($role))
        {
            $role_id = $this->getUserRoleId($role);
            if($role_id)
            {
                $query .= " WHERE role_id = :role_id";
                $array['role_id'] = $role_id;
            }
        }
        return $db->queryData($query, $array);;
    }

    public function getAllUsersByRoleID( $role_id, $active = -1 )
    {
        $db = Database::openConnection();
        $array = array();
        $query = "SELECT * FROM users WHERE role_id = $role_id";
        if($active >= 0)
        {
            $query .= " AND active = $active";
        }
        $query .= " ORDER BY name";
        return $db->queryData($query);;
    }

    public function getUserName( $user_id )
    {
        $db = Database::openConnection();
        return ($db->queryValue($this->table,array('id' => $user_id), 'name'));
    }

    public function getUserEmail( $user_id )
    {
        $db = Database::openConnection();
        return ($db->queryValue($this->table,array('id' => $user_id), 'email'));
    }

    public function getUserClientId( $user_id )
    {
        $db = Database::openConnection();
        return ($db->queryValue($this->table,array('id' => $user_id), 'client_id'));
    }

    public function getUserRoleName($id)
    {
        $db = Database::openConnection();
        return $db->queryValue('user_roles', array('id' =>  $id), 'name');
    }

    public function getUserRoleId($name)
    {
        $db = Database::openConnection();
        return $db->queryValue('user_roles', array('name' =>  $name));
    }

    public function getUserRoles($active = -1)
    {
        $db = Database::openConnection();
        $q = "SELECT * FROM user_roles";
        if($active >= 0)
        {
            $q .= " WHERE active = $active";
        }
        $q .= " ORDER BY ranking";
        return $db->queryData($q);
    }

    public function getClientRoleId()
    {
        $db = Database::openConnection();
        return $db->queryValue("user_roles", array('name' => 'client'));
    }

    public function getSelectUserRoles($selected = false)
    {
        $db = Database::openConnection();
        $check = "";
        $ret_string = "";
        $user_role_id = $db->queryValue($this->table, array('id' => Session::getUserId()), 'role_id');
        $user_rank = $db->queryValue('user_roles', array('id' => $user_role_id), 'ranking');
        $types = $db->queryData("SELECT id, name FROM user_roles WHERE active = 1 AND ranking >= $user_rank ORDER BY name");
        foreach($types as $t)
        {
            $label = $t['name'];
            $value = $t['id'];
            if($selected)
            {
                $check = ($value == $selected)? "selected='selected'" : "";
            }
            $ret_string .= "<option $check value='$value'>$label</option>";
        }
        return $ret_string;
    }

    public function addUserRole($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  strtolower($data['name'])
        );
        return $db->insertQuery('user_roles', $vals);
    }

    public function editUserRole($data)
    {
        $db = Database::openConnection();
        $vals = array(
            'name'      =>  strtolower($data['name'])
        );
        $vals['active'] = (isset($data['active']))? 1:0;
        $db->updateDatabaseFields('user_roles', $vals, $data['id']);
        return true;
    }

    public function checkRoleNames($name, $current_name)
    {
        $db = Database::openConnection();
        $name = strtoupper($name);
        $current_name = strtoupper($current_name);
        $q = "SELECT name FROM user_roles";
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

    public function canManageRole($role_id)
    {
        $db = Database::openConnection();
        $role_rank = $db->queryValue('user_roles', array('id' => $role_id), 'ranking');
        $user_role_id = $db->queryValue($this->table, array('id' => Session::getUserId()), 'role_id');
        $user_rank = $db->queryValue('user_roles', array('id' => $user_role_id), 'ranking');
        //die($user_role_id." : ".$role_rank);
        return ($user_rank < $role_rank);
    }

    public function isAdminUser($user_id = null)
    {
        $db = Database::openConnection();
        if(empty($user_id))
            $user_id = Session::getUserId();
        $q = "SELECT ur.ranking FROM user_roles ur JOIN users u ON ur.id = u.role_id WHERE u.id = $user_id";
        $res = $db->queryRow($q);
        $urank = $res['ranking'];
        $admin_rank = $db->queryValue('user_roles', array('name' => 'admin'), 'ranking');
        return $urank <= $admin_rank;
    }

    public function emailTaken($email)
    {
        $db = Database::openConnection();

        return $db->fieldValueTaken('users', $email, 'email');
    }
}
