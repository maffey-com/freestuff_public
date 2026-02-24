<?php
require_once("resources/initial.php");
$listing_id = paramFromGet("listing_id");

echo "<h1>Full listing details for: $listing_id </h1>";

$sql = "select * from listing where listing_id = " . quoteSQL($listing_id);
$listing_data = runQueryGetFirstRow($sql);
dumpDetail($listing_data);


$user_data = runQueryGetFirstRow("select * from user where user_id = " . quoteSQL($listing_data["user_id"]));
echo "<h2>User details</h2>";
dumpDetail($user_data);

//cross-reference ip_addresses and user_ids
$_user_id_sql = quoteSQL($listing_data['user_id']);
$_ip_address_sql = quoteSQL($listing_data['ip_address']);
$sql =
<<<CROSS_REF_SQL
    WITH RECURSIVE cte AS (
        -- Anchor member: Select initial rows that match either user_id or ip_address condition
        SELECT
            listing_id,
            user_id,
            ip_address
        FROM listing
        WHERE user_id = $_user_id_sql OR ip_address = $_ip_address_sql
    
        UNION ALL
    
        -- Recursive member: Join the CTE with itself to recursively find more matches
        SELECT
            l.listing_id,
            l.user_id,
            l.ip_address
        FROM listing l
        INNER JOIN cte c ON l.ip_address = c.ip_address
        LIMIT 1000 -- Limit the depth of recursion to avoid excessive load
    )
    SELECT * FROM cte
    ORDER BY cte.listing_id DESC
    ;
CROSS_REF_SQL;


$result = runQueryGetAll($sql);

$ip_addresses = array_unique(
    array_column($result, 'ip_address')
);
$user_ids = array_unique(
    array_column($result, 'user_id')
);
$listing_ids = array_unique(
    array_column($result, 'listing_id')
);


//$ip_addresses = array($listing_data["ip_address"]);
//$user_ids = array($listing_data["user_id"]);
//$listing_ids = array($listing_data["listing_id"]);
//$last_ip_size = 0;
//$last_user_size = 0;
//while (sizeof($ip_addresses) != $last_ip_size || sizeof($user_ids) != $last_user_size) {
//    $last_ip_size = sizeof($ip_addresses);
//    $last_user_size = sizeof($user_ids);
//
//    $sql = "select listing_id,user_id,ip_address from listing";
//    $sql .= " where ip_address in (" . arrayToSQLIn($ip_addresses) . ")";
//    $sql .= " or user_id in  (" . arrayToSQLIn($user_ids) . ")";
//    $result = runQueryGetAll($sql);
//    foreach ($result as $row) {
//        if (!in_array($row["ip_address"],$ip_addresses)) {
//            $ip_addresses[] = $row["ip_address"];
//        }
//        if (!in_array($row["user_id"],$user_ids)) {
//            $user_ids[] = $row["user_id"];
//        }
//        if (!in_array($row["listing_id"],$listing_ids)) {
//            $listing_ids[] = $row["listing_id"];
//        }
//    }
//
//}

echo "<h2>IP addresses found</h2>";
echo implode("<br/>",$ip_addresses);



$sql = "select listing_date,title,description,listing_status,listing_id,ip_address,u.user_id,u.email,u.firstname";
$sql .= " from listing";
$sql .= " join user u on u.user_id = listing.user_id";
$sql .= " where listing.listing_id in (" . arrayToSQLIn($listing_ids) .")";
$sql .= " order by listing.listing_date desc";

$other_listings = runQueryGetAll($sql);
echo "<h2>User other listings</h2>";
dumpTable($other_listings);

//reports
$sql = "select report_id,listing_id,user.user_id,user.firstname,user.email,user.mobile,user.email,report_comment";
$sql .= " from report ";
$sql .= " join user on user.user_id = report.user_id";
$sql .= " where listing_id in (" . arrayToSQLIn($listing_ids) .")";
$sql .= " order by listing_id desc";
//exit($sql);
$reports = runQueryGetAll($sql);
echo "<h2>People reporting listings</h2>";
dumpTable($reports);





echo "<h2>Apache Logs</h2>";
echo "<span style='font-family: Courier New,Courier;font-size:8pt'>";
$apache = array();
if (file_exists("/var/log/virtualmin") ) {
    foreach ($ip_addresses as $ip_address) {
        exec("cat /var/log/virtualmin/freestuff.co.nz_access_log |grep $ip_address",$apache);

        exec("zcat /var/log/virtualmin/freestuff.co.nz_access_log.1.gz |grep $ip_address",$apache);

        exec("zcat /var/log/virtualmin/freestuff.co.nz_access_log.2.gz |grep $ip_address",$apache);

        exec("zcat /var/log/virtualmin/freestuff.co.nz_access_log.3.gz |grep $ip_address",$apache);

        exec("zcat /var/log/virtualmin/freestuff.co.nz_access_log.4.gz |grep $ip_address",$apache);

        exec("zcat /var/log/virtualmin/freestuff.co.nz_access_log.5.gz |grep $ip_address",$apache);

    }

    echo implode("<br/>",$apache);
  //  echo str_ireplace("\r","<br/><br/>",$apache);

} else {
    echo "No Logs";
}
echo "</span>";

function dumpDetail($data) {
    echo "<table>";
    foreach ($data as $key => $value) {
        if ($key == "password" || $key == "passkey" || $key == "remember_me_passkey") {
            continue;
        }
        echo "<tr>";
        echo "<th>" . $key . "</th>";
        echo "<td>" . $value . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}


function dumpTable($data) {
    echo "<table>";
    echo "<tr>";
    foreach ($data[0] ?? [] as $key => $null) {
        if ($key == "password" || $key == "passkey" || $key == "remember_me_passkey") {
            continue;
        }
        echo "<th>" . $key . "</th>";
    }
    echo "</tr>";
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            if ($key == "password" || $key == "passkey" || $key == "remember_me_passkey") {
                continue;
            }
            echo "<td>" . $value . "</td>";
        }


        echo "</tr>";
    }

    echo "</table>";
}

?>


<style>
    th {
        text-align: left;
    }
</style>


