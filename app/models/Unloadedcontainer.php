<?php
 /**
  * Unloadedcontainer Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>

  FUNCTIONS

  recordData($data)

  */
class Unloadedcontainer extends Model{
    public $table = "unloaded_containers";

    public function recordData($data)
    {
        //echo "<pre>",print_r($data),"</pre>"; //die();
        $client = new Client();
        $client_id = $data['client_id'];
        $db = Database::openConnection();
        $client_info = $client->getClientInfo($client_id);
        $size = str_replace(" Foot", "GP", $data['container_size'])."_".strtolower($data['load_type']);
        $charge = $client_info[$size];
        //echo "<p>Size: $size</p>";
        if(isset($data['item_count']) && $data['item_count'] > $client_info['max_loose_'.str_replace(" Foot", "GP", $data['container_size'])])
        {
            $charge += ( $data['item_count'] - $client_info['max_loose_'.str_replace(" Foot", "GP", $data['container_size'])] );
        }
        //echo "<p>Charge: $charge</p>";die();
        $vals = array(
            'client_id'         =>  $client_id,
            'container_size'    =>  $data['container_size'],
            'load_type'         =>  $data['load_type'],
            'charge'            =>  $charge,
            'entered_by'	    =>  Session::getUserId(),
            'date'              =>  $data['date_value']
        );
        if(isset($data['item_count']))
            $vals['item_count'] = $data['item_count'];
        if(isset($data['disposal']))
            $vals['disposal'] = 1;
        if(isset($data['repalletising']))
            $vals['repalletising'] = 1;
        $db->insertQuery($this->table, $vals);
        return true;
    }

    public function getUnloadedContainers($from, $to)
    {
        $db = Database::openConnection();
        $query = "
            SELECT
                c.client_name, uc.*, u.name AS entered_by_name
            FROM
                unloaded_containers uc join clients c on uc.client_id = c.id JOIN users u on u.id = uc.entered_by
            WHERE
                uc.date >= $from AND uc.date <= $to
        ";

        return $db->queryData($query);
    }

    public function getUnloadedContainersArray($from, $to)
    {
        $ucs = $this->getUnloadedContainers($from, $to);
        $return = array();
        foreach($ucs as $uc)
        {
            $row = array(
                'date'              => date("d/m/Y", $uc['date']),
                'client_name'       => $uc['client_name'],
                'container_size'    => $uc['container_size'],
                'load_type'         => $uc['load_type'],
                'entered_by'        => $uc['entered_by_name'],
                'item_count'        => $uc['item_count'],
                'repalletising'     => $uc['repalletising'],
                'disposal'          => $uc['disposal']
            );
            $return[] = $row;
        }
        return $return;
    }
}
?>