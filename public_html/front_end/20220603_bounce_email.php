<?php
$sql = "ALTER TABLE `freestuff`.`user` 
ADD COLUMN `email_bounced_date` DATETIME NULL AFTER `district_id`";