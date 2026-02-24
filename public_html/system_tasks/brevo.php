<?php
chdir(__DIR__);
require_once("resources/initial.php");
is_cli();

// Only run if called from the commandline and the psw is set correctly.
if (!php_sapi_name() == 'cli' && (paramFromGet('psw') != SYSTEMTASK_PASSWORD)) {
    die("Access denied.");
}

// Get the initial count of users needing update
$count = Brevo::getUsersNeedingUpdate('count');

// Loop until the count hits zero

while ($count > 0) {
    // Call the updateNeedingUpdate function
    Brevo::pushUsersNeedingUpdate(20);

    // Get the updated count of users needing update
    $count = Brevo::getUsersNeedingUpdate('count');

    // Print the remaining count to the console
    echo "Remaining users needing update: $count\n";
}

echo "Update completed.\n";

//push new users to brevo

Brevo::pushUsersNeedingInsert(35);

// Get the updated count of users needing update
$count = Brevo::getUsersNeedingInsert('count');

// Print the remaining count to the console
echo "--------- Remaining users needing update: $count\n";

