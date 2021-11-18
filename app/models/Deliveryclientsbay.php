<?php
 /**
  * Deliveryclientsbay Class
  *

  * @author     Mark Solly <mark.solly@fsg.com.au>


  FUNCTIONS

  getBayUsage($from, $to)
  stockAdded($client_id, $location_id)
  stockRemoved($client_id, $location_id, $product_id)

  */
class Deliveryclientsbay extends Model{
    public $table = "delivery_clients_bays";

    public function getCurrentBayUsage($client_id)
    {
        $db = Database::openConnection();
        $bays = $db->queryData("
            SELECT * FROM {$this->table} WHERE client_id = $client_id AND date_removed = 0;
        ");

        return $bays;
    }

    public function getSpaceUsage($from, $to, $client_id = 0)
    {
        $db = Database::openConnection();
        $excluded_location_ids = [
            2914,   //backorders
            2922    //collection items
        ];
        $q = "
        SELECT
            cb.id AS client_bay_id, cb.date_added, cb.date_removed, cb.size,
            dh.dh AS days_held,
            FROM_UNIXTIME(cb.date_added) AS DATE_ADDED,
            FROM_UNIXTIME(cb.date_removed) AS DATE_REMOVED,
            l.location, l.tray,
            c.client_name,
            CONCAT(i.name,'( ',i.sku,' )') AS item_name,
            FROM_UNIXTIME($from) AS DATE_FROM,
            FROM_UNIXTIME($to) AS DATE_TO,
            CAST(ROUND(
            CASE
            	cb.size
            WHEN
            	standard
            THEN
            	csc.standard * dh.dh / 7
            ELSE
            	csc.oversize * dh.dh / 7
            END,2) AS DECIMAL(10,2)) AS storage_charge
        FROM
            delivery_clients_bays cb JOIN
            (
                SELECT
                    CASE
                        delivery_clients_bays.date_removed
                    WHEN
                        0
                    THEN
                        DATEDIFF(
                            FROM_UNIXTIME($to),
                            FROM_UNIXTIME(delivery_clients_bays.date_added)
                        )
                    ELSE
                        DATEDIFF(
                            FROM_UNIXTIME(delivery_clients_bays.date_removed),
                            FROM_UNIXTIME(delivery_clients_bays.date_added)
                        )
                    END AS dh,
                    delivery_clients_bays.id
                FROM
                    delivery_clients_bays
                HAVING
                	dh > 0
            ) dh ON cb.id = dh.id JOIN
            locations l ON l.id = cb.location_id JOIN
            items i ON cb.item_id = i.id JOIN
            clients c ON cb.client_id = c.id JOIN
            client_storage_charges csc ON cb.client_id = csc.client_id
        WHERE
            c.delivery_client = 1 AND cb.location_id NOT IN(".implode(",",$excluded_location_ids).")";
        if($client_id > 0)
            $q .= " AND cb.client_id = $client_id ";
        $q .= "
        HAVING
            DATE(FROM_UNIXTIME(cb.date_added)) BETWEEN DATE_FROM AND DATE_TO
            AND (cb.date_removed = 0 OR DATE(FROM_UNIXTIME(cb.date_removed)) < DATE_TO)
        ORDER BY
            c.client_name
        ";
        //die($q);
        return $db->queryData($q);
    }

    public function getClientSpaceUsage($from, $to, $client_id = 0)
    {
        $db = Database::openConnection();
        $excluded_location_ids = [
            2914,   //backorders
            2922    //collection items
        ];
        $q = "
        SELECT
            cb.id AS client_bay_id, cb.date_added, cb.date_removed, cb.size,
            dh.dh AS days_held,
            FROM_UNIXTIME(cb.date_added) AS DATE_ADDED,
            FROM_UNIXTIME(cb.date_removed) AS DATE_REMOVED,
            l.location, l.tray,
            c.client_name,
            CONCAT(i.name,'( ',i.sku,' )') AS item_name,
            FROM_UNIXTIME($from) AS DATE_FROM,
            FROM_UNIXTIME($to) AS DATE_TO,
            CAST(ROUND(
            CASE
            	cb.size
            WHEN
            	standard
            THEN
            	csc.standard * dh.dh / 7
            ELSE
            	csc.oversize * dh.dh / 7
            END,2) AS DECIMAL(10,2)) AS storage_charge
        FROM
            delivery_clients_bays cb JOIN
            (
                SELECT
                    CASE
                        delivery_clients_bays.date_removed
                    WHEN
                        0
                    THEN
                        DATEDIFF(
                            FROM_UNIXTIME($to),
                            FROM_UNIXTIME(delivery_clients_bays.date_added)
                        )
                    ELSE
                        DATEDIFF(
                            FROM_UNIXTIME(delivery_clients_bays.date_removed),
                            FROM_UNIXTIME(delivery_clients_bays.date_added)
                        )
                    END AS dh,
                    delivery_clients_bays.id
                FROM
                    delivery_clients_bays
                HAVING
                	dh > 0
            ) dh ON cb.id = dh.id JOIN
            locations l ON l.id = cb.location_id JOIN
            items i ON cb.item_id = i.id JOIN
            clients c ON cb.client_id = c.id JOIN
            client_storage_charges csc ON cb.client_id = csc.client_id
        WHERE
            c.delivery_client = 1 AND cb.location_id NOT IN(".implode(",",$excluded_location_ids).")";
        if($client_id > 0)
            $q .= " AND cb.client_id = $client_id ";
        $q .= "
        HAVING
            DATE(FROM_UNIXTIME(cb.date_added)) < DATE_TO
            AND (cb.date_removed = 0 OR DATE(FROM_UNIXTIME(cb.date_removed)) <= DATE_TO)
        ORDER BY
            c.client_name
        ";
        //die($q);
        return $db->queryData($q);
    }

    public function stockAdded($data)
    {
        $db = Database::openConnection();
        $db->insertQuery($this->table, array(
            'client_id'     => $data['client_id'],
            'location_id'   => $data['location_id'],
            'item_id'       => $data['item_id'],
            'size'          => $data['size'],
            'date_added'    => time()
        ));
        return true;
    }

    public function stockRemoved($client_id, $location_id, $item_id)
    {
        $db = Database::openConnection();
        $this_row = $db->queryRow("
            SELECT *
            FROM {$this->table}
            WHERE date_removed = 0 AND client_id = $client_id AND location_id = $location_id AND item_id = $item_id
        ");
        $db->updateDatabaseField($this->table, 'date_removed', time(), $this_row['id']);
        return true;
    }

}
?>