<?php

/**
 * ChartQuery class.
 *
 * Generates database queries and returns array to aid google charts to graphically represent date

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class ChartQuery{
    private function __construct(){}

    public static function init()
    {
        $db = Database::openConnection();
        $p = $db->query("SHOW PROCEDURE STATUS WHERE NAME=:name", ['name' => 'filldate']);
        var_dump($p);
        die();
    }



    private static function createFillYearWeekProcedure()
    {

        return "
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
        ";
    }

    private static function createFillDatesProcedure()
    {
        return "
            DROP PROCEDURE IF EXISTS filldates;
            DELIMITER //
            CREATE PROCEDURE filldates(dateStart DATE, dateEnd DATE)
            BEGIN
                WHILE dateStart <= dateEnd DO
                    INSERT INTO yw (id) VALUES (YEARWEEK(dateStart));
                    SET dateStart = date_add(dateStart, INTERVAL 7 DAY);
                END WHILE;
            END;
            //
            DELIMITER ;
        ";
    }
}