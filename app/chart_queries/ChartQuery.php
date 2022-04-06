<?php

/**
 * ChartQuery class.
 *
 * Generates database queries and returns array to aid google charts to graphically represent date

 * The following stored procedure queries need to be executed first
    DROP PROCEDURE IF EXISTS fillyearweak;
    DELIMITER //
    CREATE PROCEDURE fillyearweak(dateStart DATE, dateEnd DATE)
    BEGIN
        WHILE dateStart <= dateEnd DO
            INSERT INTO yw (id) VALUES (YEARWEEK(dateStart));
            SET dateStart = date_add(dateStart, INTERVAL 7 DAY);
        END WHILE;
    END;
    //
    DELIMITER ;

    DROP PROCEDURE IF EXISTS filldates;
    DELIMITER |
    CREATE PROCEDURE filldates(dateStart DATE, dateEnd DATE)
    BEGIN
        WHILE dateStart <= dateEnd DO
            INSERT INTO date_list (id) VALUES (dateStart);
            SET dateStart = date_add(dateStart, INTERVAL 1 DAY);
        END WHILE;
    END;
    |
    DELIMITER ;

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class ChartQuery{
    private function __construct(){}

    //weekly pickup activity
    public static function getWeeklyPickupActivity($client_id = 0)
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE yw (id int Primary Key);
        ");
        $db->query("
            CALL fillyearweek(DATE(timestamp(current_date) - INTERVAL 3 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $pickups = $db->queryData("
            SELECT
                a.MONDAY,
                a.TOTAL_PICKUPS,
                ROUND(AVG(b.total_pickups), 1) AS pickup_average
            FROM
            (
                SELECT
                	count(p.date_entered) AS TOTAL_PICKUPS,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 2 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                	(SELECT date_entered FROM pickups WHERE client_id = $client_id AND cancelled = 0)
                    p ON YEARWEEK(FROM_UNIXTIME(p.date_entered)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
                ORDER BY
                    MONDAY ASC
            )a JOIN
            (
                SELECT
                    COUNT(p.date_entered) AS total_pickups,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                	(SELECT date_entered FROM pickups WHERE client_id = $client_id AND cancelled = 0)
                    p ON YEARWEEK(FROM_UNIXTIME(p.date_entered)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )b ON b.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(a.year_week,' Monday'), '%X%V %W') - INTERVAL 1 MONTH) AND  a.year_week
            GROUP BY
                a.year_week
            ORDER BY
                MONDAY ASC
        ");
        //echo "<pre>",print_r($deliveries),"</pre>";die();
        $return_array = array(
            array(
                'Week Beginning',
                'Total Pickups Per Week',
                'Running Monthly Average'
            )
        );
        foreach($pickups as $p)
        {
            $row_array = array();
            $row_array[0] = $p['MONDAY'];
            $row_array[1] = (int)$p['TOTAL_PICKUPS'];
            $row_array[2] = (float)$p['pickup_average'];
            $return_array[] = $row_array;
        }
        return $return_array;
    }

    //weekly delivery activity
    public static function getWeeklyDeliveryActivity($client_id = 0)
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE yw (id int Primary Key);
        ");
        $db->query("
            CALL fillyearweek(DATE(timestamp(current_date) - INTERVAL 3 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $deliveries = $db->queryData("
            SELECT
                a.MONDAY,
                a.TOTAL_DELIVERIES,
                ROUND(AVG(b.total_deliveries), 1) AS delivery_average
            FROM
            (
                SELECT
                	count(d.date_entered) AS TOTAL_DELIVERIES,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 2 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                	(SELECT date_entered FROM deliveries WHERE client_id = $client_id AND cancelled = 0)
                    d ON YEARWEEK(FROM_UNIXTIME(d.date_entered)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
                ORDER BY
                    MONDAY ASC
            )a JOIN
            (
                SELECT
                    COUNT(d.date_entered) AS total_deliveries,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                	(SELECT date_entered FROM deliveries WHERE client_id = $client_id AND cancelled = 0)
                    d ON YEARWEEK(FROM_UNIXTIME(d.date_entered)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )b ON b.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(a.year_week,' Monday'), '%X%V %W') - INTERVAL 1 MONTH) AND  a.year_week
            GROUP BY
                a.year_week
            ORDER BY
                MONDAY ASC
        ");
        //echo "<pre>",print_r($deliveries),"</pre>";die();
        $return_array = array(
            array(
                'Week Beginning',
                'Total Deliveries Per Week',
                'Running Monthly Average'
            )
        );
        foreach($deliveries as $d)
        {
            $row_array = array();
            $row_array[0] = $d['MONDAY'];
            $row_array[1] = (int)$d['TOTAL_DELIVERIES'];
            $row_array[2] = (float)$d['delivery_average'];
            $return_array[] = $row_array;
        }
        return $return_array;
    }
    //daily job activity for production
    public static function getDailyJobTrends()
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE date_list (id date Primary Key);
        ");
        $db->query("
            CALL filldates(DATE(timestamp(current_date) - INTERVAL 2 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $jobs = $db->queryData("
            SELECT
                a.TODAY,
                a.total_jobs AS total_jobs,
                ROUND(AVG(b.total_jobs), 1) AS job_average
            FROM
            (
                SELECT
                    count(pj.created_date) AS total_jobs,
                    date_list.id AS TODAY,
                    DATE(timestamp(current_date) - INTERVAL 1 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY
                FROM
                    date_list LEFT JOIN
                    production_jobs pj ON DATE(FROM_UNIXTIME(pj.created_date)) = date_list.id
                WHERE
                    WEEKDAY(date_list.id) <= 4
                GROUP BY
                    date_list.id
                HAVING
                    (TODAY >= START_DAY AND TODAY <= END_DAY)
            )a JOIN
            (
                SELECT
                    count(pj.created_date) AS total_jobs,
                    date_list.id AS TODAY,
                    DATE(timestamp(current_date) - INTERVAL 2 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY
                FROM
                    date_list LEFT JOIN
                    production_jobs pj ON DATE(FROM_UNIXTIME(pj.created_date)) = date_list.id
                WHERE
                    WEEKDAY(date_list.id) <= 4
                GROUP BY
                    date_list.id
                HAVING
                    (TODAY >= START_DAY AND TODAY <= END_DAY)
            )b ON b.TODAY BETWEEN DATE(a.TODAY - INTERVAL 1 MONTH) AND  a.TODAY
            GROUP BY
                a.today
            ORDER BY
                a.TODAY ASC
        ");
        $return_array = array(
            array(
                'Date',
                'Total Jobs Per Day',
                'Running Monthly Average'
            )
        );
        foreach($jobs as $o)
        {
            $row_array = array();
            $row_array[0] = $o['TODAY'];
            $row_array[1] = (int)$o['total_jobs'];
            $row_array[2] = (float)$o['job_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }

    //wekly job activity for production
    public static function getWeeklyJobTrends()
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE yw (id int Primary Key);
        ");
        $db->query("
            CALL fillyearweek(DATE(timestamp(current_date) - INTERVAL 6 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $jobs = $db->queryData("
             			SELECT
                a.MONDAY,
                a.total_jobs,
                ROUND(AVG(b.total_jobs), 1) AS job_average
            FROM
            (
                SELECT
                    count(pj.created_date) AS total_jobs,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 2 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    production_jobs pj ON YEARWEEK(FROM_UNIXTIME(pj.created_date)) = yw.id
                GROUP BY
                	yw.id
                HAVING
                	year_week >= START_WEEK AND year_week <= END_WEEK
            )a JOIN
            (
                SELECT
                    count(pj.created_date) AS total_jobs,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                	yw LEFT JOIN
                	production_jobs pj ON YEARWEEK(FROM_UNIXTIME(pj.created_date)) = yw.id
                GROUP BY
                	yw.id
                HAVING
                	year_week >= START_WEEK AND year_week <= END_WEEK
            )b ON b.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(a.year_week,' Monday'), '%X%V %W') - INTERVAL 1 MONTH) AND  a.year_week
            GROUP BY
                a.year_week
            ORDER BY
                a.MONDAY ASC
        ");

        $return_array = array(
            array(
                'Week Beginning',
                'Total Jobs Per Week',
                'Running Monthly Average'
            )
        );
        foreach($jobs as $o)
        {
            $row_array = array();
            $row_array[0] = $o['MONDAY'];
            $row_array[1] = (int)$o['total_jobs'];
            $row_array[2] = (float)$o['job_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }

    //weekly client activity for the warehouse
    public static function getWeeklyClientActivity()
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE yw (id int Primary Key);
        ");
        $db->query("
            CALL fillyearweek(DATE(timestamp(current_date) - INTERVAL 3 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $activity = $db->queryData("
            SELECT
                a.MONDAY,
                a.total_orders,
                (ROUND(AVG(a_av.total_orders), 1) + ROUND(AVG(b_av.total_deliveries), 1)  + ROUND(AVG(c_av.total_pickups), 1)) AS activity_average,
                b.total_deliveries,
                c.total_pickups
            FROM
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 2 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    orders o ON YEARWEEK(FROM_UNIXTIME(o.date_fulfilled)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )a JOIN
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    orders o ON YEARWEEK(FROM_UNIXTIME(o.date_fulfilled)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK

            )a_av ON a_av.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(a.year_week,' Monday'), '%X%V %W') - INTERVAL 1 MONTH) AND  a.year_week
            JOIN
            (
                SELECT
                	count(d.date_entered) AS total_deliveries,
                    YEARWEEK(timestamp(current_date) - INTERVAL 2 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    deliveries d ON YEARWEEK(FROM_UNIXTIME(d.date_entered)) = yw.id
                WHERE
                    d.cancelled IS NULL OR d.cancelled = 0
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )b ON b.year_week = a.year_week
            JOIN
            (
                SELECT
                	count(d.date_entered) AS total_deliveries,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    deliveries d ON YEARWEEK(FROM_UNIXTIME(d.date_entered)) = yw.id
                WHERE
                    d.cancelled IS NULL OR d.cancelled = 0
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            ) b_av ON b_av.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(b.year_week,' Monday'), '%X%V %W') - INTERVAL 1 MONTH) AND  b.year_week
            JOIN
            (
                SELECT
                	count(p.date_entered) AS total_pickups,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    pickups p ON YEARWEEK(FROM_UNIXTIME(p.date_entered)) = yw.id
                WHERE
                    p.cancelled IS NULL OR p.cancelled = 0
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )c ON c.year_week = a.year_week
            JOIN
            (
                SELECT
                	count(p.date_entered) AS total_pickups,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    pickups p ON YEARWEEK(FROM_UNIXTIME(p.date_entered)) = yw.id
                WHERE
                    p.cancelled IS NULL OR p.cancelled = 0
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )c_av ON c_av.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(c.year_week,' Monday'), '%X%V %W') - INTERVAL 1 MONTH) AND  c.year_week
            GROUP BY
                a.year_week
            ORDER BY
                a.MONDAY ASC
        ");
        $return_array = array(
            array(
                'Week Beginning',
                'Total Orders Per Week',
                'Total Deliveries Per Week',
                'Total Pickups Per Week',
                'Running Monthly Average'
            )
        );

        foreach($activity as $a)
        {
            $row_array = array();
            $row_array[0] = $a['MONDAY'];
            $row_array[1] = (int)$a['total_orders'];
            $row_array[2] = (int)$a['total_deliveries'];
            $row_array[3] = (int)$a['total_pickups'];
            $row_array[4] = (float)$a['activity_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }

    //Daily client activity for the warehouse
    public static function getDailyClientActivity()
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE date_list (id date Primary Key);
        ");
        $db->query("
            CALL filldates(DATE(timestamp(current_date) - INTERVAL 3 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $activity = $db->queryData("
            SELECT
                a.date AS TODAY,
                a.total_orders,
                (ROUND(AVG(a_av.total_orders), 1) + ROUND(AVG(b_av.total_deliveries), 1)  + ROUND(AVG(c_av.total_pickups), 1)) AS activity_average,
                b.total_deliveries,
                c.total_pickups
            FROM
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    DATE(timestamp(current_date) - INTERVAL 1 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    orders o ON DATE(FROM_UNIXTIME(o.date_fulfilled)) = date_list.id AND o.cancelled = 0
                WHERE
                    WEEKDAY(date_list.id) < 5 AND date_list.id >= DATE(timestamp(current_date) - INTERVAL 1 MONTH) AND date_list.id <= DATE(timestamp(current_date))
                GROUP BY
                    date_list.id
            )a
            JOIN
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    DATE(timestamp(current_date) - INTERVAL 2 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    orders o ON DATE(FROM_UNIXTIME(o.date_fulfilled)) = date_list.id AND o.cancelled = 0
                WHERE
                    WEEKDAY(date_list.id) < 5 AND date_list.id >= DATE(timestamp(current_date) - INTERVAL 1 MONTH) AND date_list.id <= DATE(timestamp(current_date))
                GROUP BY
                    date_list.id
            )a_av ON a_av.date BETWEEN (a.date - INTERVAL 2 MONTH) AND  a.date
            JOIN
            (
                SELECT
                    COALESCE(count(d.date_entered),0) AS total_deliveries,
                    DATE(timestamp(current_date) - INTERVAL 1 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    deliveries d ON DATE(FROM_UNIXTIME(d.date_entered)) = date_list.id AND d.cancelled = 0
                WHERE
                    WEEKDAY(date_list.id) < 5 AND date_list.id >= DATE(timestamp(current_date) - INTERVAL 1 MONTH) AND date_list.id <= DATE(timestamp(current_date))
                GROUP BY
                    date_list.id
            )b ON b.date = a.date
            JOIN
            (
                SELECT
                    COALESCE(count(d.date_entered),0) AS total_deliveries,
                    DATE(timestamp(current_date) - INTERVAL 2 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    deliveries d ON DATE(FROM_UNIXTIME(d.date_entered)) = date_list.id AND d.cancelled = 0
                WHERE
                    WEEKDAY(date_list.id) < 5 AND date_list.id >= DATE(timestamp(current_date) - INTERVAL 1 MONTH) AND date_list.id <= DATE(timestamp(current_date))
                GROUP BY
                    date_list.id
            )b_av ON b_av.date BETWEEN (b.date - INTERVAL 2 MONTH) AND  b.date
            JOIN
            (
                SELECT
                    COALESCE(count(p.date_entered),0) AS total_pickups,
                    DATE(timestamp(current_date) - INTERVAL 1 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    pickups p ON DATE(FROM_UNIXTIME(p.date_entered)) = date_list.id AND p.cancelled = 0
                WHERE
                    WEEKDAY(date_list.id) < 5 AND date_list.id >= DATE(timestamp(current_date) - INTERVAL 1 MONTH) AND date_list.id <= DATE(timestamp(current_date))
                GROUP BY
                    date_list.id
            )c ON c.date = a.date
            JOIN
            (
                SELECT
                    COALESCE(count(p.date_entered),0) AS total_pickups,
                    DATE(timestamp(current_date) - INTERVAL 2 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                    pickups p ON DATE(FROM_UNIXTIME(p.date_entered)) = date_list.id AND p.cancelled = 0
                WHERE
                    WEEKDAY(date_list.id) < 5 AND date_list.id >= DATE(timestamp(current_date) - INTERVAL 1 MONTH) AND date_list.id <= DATE(timestamp(current_date))
                GROUP BY
                    date_list.id
            )c_av ON c_av.date BETWEEN (c.date - INTERVAL 2 MONTH) AND  c.date
            GROUP BY
                a.date
            ORDER BY
                a.date ASC
        ");

        $return_array = array(
            array(
                'Day',
                'Total Orders Per Day',
                'Total Deliveries Per Day',
                'Total Pickups Per Day',
                'Running Monthly Average'
            )
        );

        foreach($activity as $a)
        {
            $row_array = array();
            $row_array[0] = $a['TODAY'];
            $row_array[1] = (int)$a['total_orders'];
            $row_array[2] = (int)$a['total_deliveries'];
            $row_array[3] = (int)$a['total_pickups'];
            $row_array[4] = (float)$a['activity_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }

    public static function getDailyPPClientActivity($client_id = 0)
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE date_list (id date Primary Key);
        ");
        $db->query("
            CALL filldates(DATE(timestamp(current_date) - INTERVAL 6 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $activity = $db->queryData("
            SELECT
                a.date AS TODAY,
                a.total_orders,
                ROUND(AVG(a_av.total_orders), 1) AS order_average
            FROM
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    DATE(timestamp(current_date) - INTERVAL 1 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                	(
                     	SELECT date_fulfilled FROM orders WHERE client_id = $client_id
                    )o ON DATE(FROM_UNIXTIME(o.date_fulfilled)) = date_list.id
                WHERE
                    WEEKDAY(date_list.id) < 5
                GROUP BY
                    date_list.id
                HAVING
                    date >= START_DAY AND date <= END_DAY
            )a
            JOIN
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    DATE(timestamp(current_date) - INTERVAL 2 MONTH) AS START_DAY,
                    DATE(timestamp(current_date)) AS END_DAY,
                    date_list.id AS date
                FROM
                    date_list LEFT JOIN
                	(
                     	SELECT date_fulfilled FROM orders WHERE client_id = $client_id
                    )o ON DATE(FROM_UNIXTIME(o.date_fulfilled)) = date_list.id
                WHERE
                    WEEKDAY(date_list.id) < 5
                GROUP BY
                    date_list.id
                HAVING
                    date >= START_DAY AND date <= END_DAY
            )a_av ON a_av.date BETWEEN (a.date - INTERVAL 1 MONTH) AND  a.date
            GROUP BY
                a.date
            ORDER BY
                a.date ASC
        ");
        $return_array = array(
            array(
                'Day',
                'Total Orders Per Day',
                'Running Monthly Average'
            )
        );

        foreach($activity as $a)
        {
            $row_array = array();
            $row_array[0] = $a['TODAY'];
            $row_array[1] = (int)$a['total_orders'];
            $row_array[2] = (float)$a['order_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }

    public static function getWeeklyPPClientActivity($client_id = 0)
    {
        $db = Database::openConnection();
        $db->query("
            CREATE TEMPORARY TABLE yw (id int Primary Key);
        ");
        $db->query("
            CALL fillyearweek(DATE(timestamp(current_date) - INTERVAL 6 MONTH),DATE(timestamp(current_date) + INTERVAL 1 DAY));
        ");
        $activity = $db->queryData("
                        SELECT
                a.MONDAY,
                a.total_orders,
                ROUND(AVG(a_av.total_orders), 1) AS order_average
            FROM
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 2 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                    (SELECT date_fulfilled FROM orders WHERE client_id = $client_id)
                    o ON YEARWEEK(FROM_UNIXTIME(o.date_fulfilled)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )a JOIN
            (
                SELECT
                    count(o.date_fulfilled) AS total_orders,
                    STR_TO_DATE(  CONCAT(yw.id,' Monday'), '%X%V %W') AS MONDAY,
                    YEARWEEK(timestamp(current_date) - INTERVAL 3 MONTH) AS START_WEEK,
                    YEARWEEK(timestamp(current_date) + INTERVAL 1 DAY) AS END_WEEK,
                    yw.id AS year_week
                FROM
                    yw LEFT JOIN
                	(SELECT date_fulfilled FROM orders WHERE client_id = $client_id)
                    o ON YEARWEEK(FROM_UNIXTIME(o.date_fulfilled)) = yw.id
                GROUP BY
                    yw.id
                HAVING
                    year_week >= START_WEEK AND year_week <= END_WEEK
            )a_av ON a_av.year_week BETWEEN YEARWEEK(STR_TO_DATE(  CONCAT(a.year_week,' Monday'), '%X%V %W') - INTERVAL 1 MONTH) AND  a.year_week
            GROUP BY
                a.year_week
            ORDER BY
                a.MONDAY ASC
        ");
        $return_array = array(
            array(
                'Day',
                'Total Orders Per Week',
                'Running Monthly Average'
            )
        );

        foreach($activity as $a)
        {
            $row_array = array();
            $row_array[0] = $a['MONDAY'];
            $row_array[1] = (int)$a['total_orders'];
            $row_array[2] = (float)$a['order_average'];
            $return_array[] = $row_array;
        }
        //print_r($return_array);
        return $return_array;
    }

}