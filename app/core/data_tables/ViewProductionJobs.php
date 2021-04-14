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

    private function __construct(){}

    //public collection methods
    public static function collectData( $request )
    {
        //the database object
        $db = Database::openConnection();
        //other stuff from the request
        self::$user_role = $request['userRole'];
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
                        "<a href='/jobs/update-job/job=".$row['id'].">".$d."</a>":
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
            $having = ' HAVING '.$having;
        }
        else
        {
            $having = ' HAVING (pj.status_id != 9 AND pj.status_id != 11)';
        }
        return $having;
    }

 }
?>