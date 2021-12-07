<?php

/**
 * ChartQuery class.
 *
 * Generates database queries and returns array to aid google charts to graphically represent date

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */
class ChartQuery{
    private function __construct()
    {
        self::createFillYearWeekProcedure();
    }

    private static function createFillYearWeekProcedure()
    {
        $db = Database::openConnection();
        if(!$db->queryRow("SHOW PROCEDURE STATUS WHERE NAME=:name", ['name' => 'fillyearweak']))
        {
            $db->query("CREATE PROCEDURE fillyearweak(dateStart DATE, dateEnd DATE)
            BEGIN
                WHILE dateStart <= dateEnd DO
                    INSERT INTO yw (id) VALUES (YEARWEEK(dateStart));
                    SET dateStart = date_add(dateStart, INTERVAL 7 DAY);
                END WHILE;
            END;");
        }
        $db::closeConnection();
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