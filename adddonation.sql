DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `displaydonation`()
BEGIN
select sum(c.amount) as cashamount,date(d.timestamp) as donationdate from cash c inner join donations d on c.donation_id=d.donation_id group by DATE(d.timestamp);
END$$
DELIMITER ;