<?php
include_once("../common/config/config.php");

$sql = "SELECT * 
        FROM (
            SELECT *, 
            DATEDIFF(CURDATE(), remembered_date) created_x_days_ago,
            IF(DATEDIFF(CURDATE(), remembered_date) > 365, 'y', 'n') has_cookie_expired,
            IF(last_used_date is null, 'y', 'n') never_used,
            DATEDIFF(CURDATE(), last_used_date) last_used_x_days_ago,
            IF(DATEDIFF(CURDATE(), last_used_date) < 60, 'y', 'n') used_in_the_last_60_days,
            DATEDIFF(last_used_date, remembered_date) days_created_vs_last_used
            FROM freestuff.user_remember_me
        ) tmp
        WHERE never_used = 'n'
        AND has_cookie_expired = 'n'
        AND used_in_the_last_60_days = 'y'
        AND days_created_vs_last_used > 60";