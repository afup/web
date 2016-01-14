# Import scans after an event into the AFUP database

Here is an example of SQL script:

```sql
--
-- Example of SQL script in order to import scans
-- after an event like the Forum PHP.
--
-- 1. Import the table "scan" after deleting it (below) with all data from scanner
--
DROP TABLE IF EXISTS `scan`;

-- Then in the SAME session:

-- 2. Change the event dates below
--
SET @day1date := '2015-11-23';
SET @day2date := '2015-11-24';

-- 3. Still in the SAME session, run the following SQL statements:
--
DROP TEMPORARY TABLE IF EXISTS `tmp_scans`;

CREATE TEMPORARY TABLE IF NOT EXISTS `tmp_scans` (
    `visitor_id` INT(11) UNSIGNED NOT NULL,
    `day1` TINYINT(1) NULL DEFAULT NULL,
    `day2` TINYINT(1) NULL DEFAULT NULL
);

INSERT INTO `tmp_scans` (`visitor_id`, `day1`, `day2`)
    SELECT `visitor_id`, COUNT(`day1`) AS `day1`, COUNT(`day2`) AS `day2` FROM (
        SELECT `visitor_id`,
            IF(DATE(`DATE`) = @day1date, 1, NULL) AS `day1`,
            IF(DATE(`DATE`) = @day2date, 1, NULL) AS `day2`
        FROM `scan`
        WHERE `visitor_id` IS NOT NULL
        GROUP BY DATE(`DATE`), `visitor_id`
    ) AS `tmp`
    GROUP BY `tmp`.`visitor_id`
;

UPDATE `afup_inscription_forum` AS `inscr`
LEFT JOIN `tmp_scans` AS `scans` ON `scans`.`visitor_id` = `inscr`.`id`
SET `presence_day1` = `scans`.`day1`,
      `presence_day2` = `scans`.`day2`;
      
-- Done!
```

