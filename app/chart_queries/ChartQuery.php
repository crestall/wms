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

}