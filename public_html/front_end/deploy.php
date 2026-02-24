<?php

/**
 * GIT DEPLOYMENT SCRIPT
 *
 * Used for automatically deploying websites via github securely, more deets here:
 *
 *		https://gist.github.com/limzykenneth/baef1b190c68970d50e1
 */

include('resources/initial.php');

writeLog('Deploying...',"git.log");
// The header information which will be verified
$agent=$_SERVER['HTTP_USER_AGENT'];
$signature=$_SERVER['HTTP_X_HUB_SIGNATURE'];
$body=@file_get_contents('php://input');

// The commands
$commands = array(
    'git pull origin '.GIT_BRANCH,
    'git submodule sync',
    'git submodule update'
);

base64_encode($agent);
base64_encode($signature);

if (strpos($agent,'GitHub-Hookshot') !== false){
    if (hash_equals($signature, verify_request())){
        // Run the commands
        foreach($commands AS $command){
            // Run it
            writeLog("Executing $command","git.log");
            $tmp = shell_exec($command);
            writeLog($tmp,"git.log");
        }
    }else{
        header('HTTP/1.1 403 Forbidden');
        writeLog('403...bad signature',"git.log");
        echo "Invalid request.";
    }
}else{
    header('HTTP/1.1 403 Forbidden');
    echo "Invalid request.";
    writeLog('403...wrong agent',"git.log");
}

// Generate the hash verification with the request body and the key stored in your .htaccess file
function verify_request(){
    $message = $GLOBALS['body'];
    $key     = 'lajlfjdsui7735268394938374902';
    $hash    = hash_hmac("sha1", $message, $key);
    $hash = "sha1=".$hash;
    return $hash;
}


echo "Deploy successful.";


/*

// The header information which will be verified
$agent=$_SERVER['HTTP_USER_AGENT'];
$signature=$_SERVER['HTTP_X_HUB_SIGNATURE'];
$body_contents=@file_get_contents('php://input');

// The commands
$commands = array(
    'git reset --hard',
    'git pull',
    'git submodule sync',
    'git submodule update',
);

base64_encode($agent);
base64_encode($signature);

// Run the commands for output
$output = '';
if (strpos($agent,'GitHub-Hookshot') !== false){
    if (hash_equals($signature, verify_request($body_contents))){
        // Run the commands
        foreach($commands AS $command){
            // Run it
            $tmp = shell_exec($command);
            // Output
            $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
            $output .= htmlentities(trim($tmp)) . "\n";
        }
    }else{
        header('HTTP/1.1 403 Forbidden');
        $output = "Invalid request.";
    }
}else{
    header('HTTP/1.1 403 Forbidden');
    $output = "Invalid request.";
}
// Generate the hash verification with the request body and the key stored in your .htaccess file
function verify_request($message){
    $key     = 'lajlfjdsui7735268394938374902'; //getenv('GIT_TOKEN');
    $hash    = hash_hmac("sha1", $message, $key);
    $hash = "sha1=".$hash;
    return $hash;
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>GIT DEPLOYMENT SCRIPT</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>
 .  ____  .    ____________________________
 |/      \|   |                            |
[| <span style="color: #FF0000;">&hearts;    &hearts;</span> |]  | Git Deployment Script v0.2 |
 |___==___|  /                             |
              |____________________________|

    <?php echo $output; ?>
</pre>
</body>
</html>*/