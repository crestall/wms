<?php
/**
 * View Products Implementation of the DataTablesSS Class.
 *
 *
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
 class ViewProductionJobs extends DataTablesSS
 {
    private static $return_array       = array();
    private static $table              = "production_jobs";
    private static $columns            = array();
    private static $query              = "";
    private static $user_role          = "";
    private static $cancelled          = "";
    private static $completed          = "";
    private static $customer_ids       = "";
    private static $finisher_ids       = "";
    private static $salesrep_ids       = "";
    private static $status_ids         = "";
    private static $can_change_status  = false;

    private function __construct(){}

    //public collection methods
    public static function collectData( $request )
    {
        //the database object
        $db = Database::openConnection();
        //other stuff from the request
        self::$user_role = $request['userRole'];
        self::$cancelled = $request['cancelled'];
        self::$completed = $request['completed'];
        self::$customer_ids = $request['customerIds'];
        self::$finisher_ids = $request['finisherIds'];
        self::$status_ids = $request['statusIds'];
        self::$salesrep_ids = $request['salesrepIds'];
        self::$can_change_status = Permission::canChangeStatus(self::$user_role);
        //return self::$user_role;
        //the columns setup
        self::$columns = array(
            array(
                'db' => 'id',
                'dt' => 'DT_RowId',
                'formatter' => function( $d, $row ) {
                    return 'row_'.$d;
                }
            ),
            array(
                'db' => 'status_colour',
                'dt' => 'DT_StatusColour',
                'formatter' => function( $d, $row ) {
                    return $d;
                }
            ),
            array(
                'db' => 'status_text_colour',
                'dt' => 'DT_StatusTextColour',
                'formatter' => function( $d, $row ) {
                    return $d;
                }
            ),
            array(
                'db' => 'due_date',
                'dt' => 'DT_DueDateColour',
                'formatter' => function( $d, $row ){
                    return ($row['strict_dd'] > 0)? $d : 0;
                }
            ),
            array(
                'db' => 'priority',
                'dt' => 0,
                'formatter' => function( $d, $row ){
                    $ranking = ($d > 0)? $d : "";
                    return "
                      <select class='selectpicker priority'  id='priority_{$row['id']}' data-ranking='".$ranking."' data-style='btn-outline-secondary btn-sm' data-width='fit'><option value=-0'>--</option>".Utility::getPrioritySelect($d)."</select>
                    ";
                }
            ),
            array(
                'db' => 'job_id',
                'dt' => 1,
                'formatter' => function( $d, $row ){
                    $ret = (self::$user_role == "production_admin" ||  self::$user_role == "production" || self::$user_role == "production_sales")?
                        "<a href='/jobs/update-job/job=".$row['id']."'>".$d."</a>":
                        $d;
                    if(!empty($row['previous_job_id']))
                    {
                        $ret .= "
                           <p class='border-top border-secondary border-top-dashed pt-3'>
                                Previous<br>".$row['previous_job_id']."
                            </p>
                        ";
                    }
                    $ret .= "<p>Created: ".date("d/m/Y", $row['created_date'])."</p>";
                    return $ret;
                }
            ),
            array(
                'db' => 'customer_name',
                'dt' => 2,
                'formatter' => function( $d, $row ){
                    $ret = "<span style='font-size: larger'>";
                    if(self::$user_role == "production_admin"):
                        $ret .= "<a href='/customers/edit-customer/customer={$row['customer_id']}'>".$d."</a>";
                    else:
                        $ret .= $d;
                    endif;
                    $ret .= "</span> ";
                    return $ret;
                }
            ),
            array(
                'db' => 'description',
                'dt' => 3,
                'formatter' => function( $d, $row ){
                    $ret = $d;
                    if(!empty($row['notes']))
                    {
                        $note = nl2br($row['notes']);
                        $ret .= "
                            <div class='notes notes-info mt-3'>
                                <h6>Production Notes:</h6>
                                $note
                            </div>
                            <p class='text-right mt-3'><button class='btn btn-sm btn-outline-fsg production_note' data-jobid='{$row['id']}' data-jobno='{$row['job_id']}'>Add Note For Production</button></p>
                        ";
                    }
                    return $ret;
                }
            ),
            array(
                'db' => 'finishers',
                'dt' => 4,
                'formatter' => function( $d, $row ){
                    $finisher_array = array();
                    $ret = "";
                    if(!empty($row['finishers']))
                    {
                        $fa = explode("|", $row['finishers']);
                        foreach($fa as $f)
                        {
                            list($a['id'], $a['name'],$a['email'],$a['phone'],$a['address'],$a['address_2'],$a['suburb'],$a['state'],$a['postcode'],$a['country'],$a['contact_id'],$a['contact_name'],$a['contact_email'],$a['contact_phone'], $a['contact_role'],$a['purchase_order'],$a['ed_date_value']) = explode(',', $f);
                            if(!empty($a['id']))
                                $finisher_array[] = $a;
                        }
                    }
                    if(!empty($finisher_array)):
                        foreach($finisher_array as $fin):
                            $ret .= "<p class='border-bottom border-secondary border-bottom-dashed mb-3'>";
                                if(self::$user_role == "production_admin")
                                    $ret .= "<a href='/finishers/edit-finisher/finisher={$fin['id']}'>".ucwords($fin['name'])."</a>";
                                else
                                    $ret .= ucwords($fin['name']);
                            $ret .= "</p>";
                        endforeach;
                    endif;
                    return $ret;
                }
            ),
            array(
                'db' => 'salesrep_name',
                'dt' => 5,
                'formatter' => function( $d, $row ){
                    return ucwords($d);
                }
            ),
            array(
                'db' => 'status',
                'dt' => 6,
                'formatter' => function( $d, $row ){
                    $ret = "<select class='selectpicker status' id='status_{$row['id']}' data-style='btn-outline-secondary btn-sm' data-width='fit'";
                    if(!self::$can_change_status)
                        $ret .= " disabled ";
                    $ret .= "><option value='0'>--Select One--</option>";
                    $js = new Jobstatus();
                    $ret .= $js->getSelectJobStatus($row['status_id']);
                    $ret .= "</select>";
                    return $ret;
                }
            ),
            array(
                'db' => 'due_date',
                'dt' => 7,
                'formatter' => function( $d, $row ){
                    return ($d > 0)? date("d/m/Y", $d): "";
                }
            )
        );
        // Build the SQL query string from the request
        $limit = self::limit( $request );
        $order = self::order( $request, self::$columns);
        $having = self::havingFilter( $request, self::$columns );

        $query = self::createQuery();
        // Total Data Set length
        $resTotalLength = $db->queryData($query);
        $recordsTotal = count($resTotalLength);
        // Filtering
        $query .= $having;
        // Data Set length after filtering
        $resFilterLength = $db->queryData($query, self::$db_array);
        $recordsFiltered = count($resFilterLength);
        // Order and limit for display
        $query .= $order;
        $query .= $limit;
        // Data for display
        $data = $db->queryData($query, self::$db_array);

        return array(
            "draw"            => isset ( $request['draw'] ) ?
                intval( $request['draw'] ) :
                0,
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => self::dataOutput( self::$columns, $data )
        );
    }

    //private helper methods
    private static function createQuery()
    {
        return "
            SELECT
                pj.*,
                pc.id AS customer_id, pc.name AS customer_name, pc.email AS customer_email, pc.phone AS customer_phone,
                pcc.name AS contact_name, pcc.email AS contact_email, pcc.phone AS contact_phone, pcc.role AS contact_role,
                sr.id AS salesrep_id, sr.name AS salesrep_name,
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
                js.name AS `status`, js.colour AS status_colour, js.text_colour AS status_text_colour, js.ranking
            FROM
                (SELECT `production_jobs`.*, `users`.`name` AS `status_change_name` FROM `production_jobs` LEFT JOIN `users` ON `production_jobs`.`status_change_by` = `users`.`id`) pj LEFT JOIN
                `production_customers` pc ON pj.customer_id = pc.id LEFT JOIN
                `production_contacts` pcc ON pj.customer_contact_id = pcc.id LEFT JOIN
                `production_jobs_finishers` pjf ON pj.id = pjf.job_id LEFT JOIN
                `production_finishers` pf ON pjf.finisher_id = pf.id LEFT JOIN
                `production_contacts` pfc ON pjf.contact_id = pfc.id LEFT JOIN
                `sales_reps` sr ON pj.salesrep_id = sr.id LEFT JOIN
                job_status js ON pj.status_id = js.id
            GROUP BY
                pj.id
        ";
    }

    static function havingFilter ( $request, $columns  )
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck( $columns, 'dt' );
        if ( isset($request['search']) && $request['search']['value'] != '' )
        {
            $str = $request['search']['value'];
            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ )
            {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];
                if ( $requestColumn['searchable'] == 'true' )
                {
                    if(!empty($column['db']))
                    {
                        $globalSearch[] = "`".$column['db']."` LIKE :gterm".$i;
                        self::$db_array['gterm'.$i] = "%".$str."%";
                    }
                }
            }
        }
        // Individual column filtering
        if ( isset( $request['columns'] ) )
        {
            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ )
            {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];
                $str = $requestColumn['search']['value'];
                if ( $requestColumn['searchable'] == 'true' && $str != '' )
                {
                    if(!empty($column['db'])){
                        $columnSearch[] = "`".$column['db']."` LIKE :cterm".$i;
                        self::$db_array['cterm'.$i] = "%".$str."%";
                    }
                }
            }
        }
        // Combine the filters into a single string
        $having = '';
        if ( count( $globalSearch ) ) {
            $having = '('.implode(' OR ', $globalSearch).')';
        }
        if ( count( $columnSearch ) ) {
            $having = $having === '' ?
                implode(' AND ', $columnSearch) :
                $having .' AND '. implode(' AND ', $columnSearch);
        }
        if ( $having !== '' ) {
            $having = ' HAVING '.$having.' AND ';
        }
        else
        {
            $having = ' HAVING ';
        }
        if(self::$completed)
        {
            $having .= " pj.status_id = 9";
            self::$status_ids = "";
        }
        elseif(self::$cancelled)
        {
            $having .= " pj.status_id = 11";
            self::$status_ids = "";
        }
        elseif(!empty(self::$status_ids))
        {
            $having .= " pj.status_id IN(".self::$status_ids.")";
        }
        else
        {
            $having .= " pj.status_id != 9 AND pj.status_id != 11";
        }
        if(!empty(self::$customer_ids))
        {
            $having .= " AND pj.customer_id IN(".self::$customer_ids.")";
        }
        if(!empty(self::$finisher_ids))
        {
            $having .= " AND pj.finisher_id IN(".self::$finisher_ids.")";
        }
        if(!empty(self::$salesrep_ids))
        {
            $having .= " AND pj.salesrep_id IN(".self::$salesrep_ids.")";
        }
        return $having;
    }
 }
?>