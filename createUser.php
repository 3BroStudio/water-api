<?php

require_once 'conn.php';
require_once 'helper.php';
require_once 'emailAPI.php';
require_once 'tokenFunction.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {    
   return 0;    
}else{
    if ( $_SERVER['REQUEST_METHOD'] != 'PUT') {
    	http_response_code(400);
    	header('Content-Type: application/json');
    	print(json_encode("PUT method required"));
    	die();
    }else{
        $_PUT = json_decode(file_get_contents('php://input'), true);
    }
}

// $check_array = [
// 	'userName',
// 	'userEmailAddress',
// 	'userPassword'
// ];

// if (array_diff($check_array, array_keys($_PUT))){
// 	http_response_code(400);
//     header('Content-Type: application/json');
//     print(json_encode("not enough parameter"));
//     die();
// }

$userName = strtolower(trimAndEscape($conn, $_PUT['userName']));
$userEmailAddress = trimAndEscape($conn, $_PUT['userEmailAddress']);

$userPassword = trimAndEscape($conn, $_PUT['userPassword']);
if ( strlen($_PUT['userPassword']) != 128) {
	http_response_code(400);
    header('Content-Type: application/json');
    print(json_encode("userPassword not in correct hash format"));
    die();
}

if ( isset($_PUT['userDisplayName']) ) {
	$userDisplayName = trimAndEscape($conn, $_PUT['userDisplayName']);
}else{
	$userDisplayName = "";
}

$preSql = "SELECT userID FROM userInformation WHERE userName = '$userName' OR userEmailAddress = '$userEmailAddress' ";
$resultZ = mysqli_query($conn, $preSql);

if ( $resultZ->num_rows > 0 ) {
	http_response_code(409);
    header('Content-Type: application/json');
    echo json_encode('Duplicated userName or userEmailAddress');
    die();
}else{
    // $sqlA = "INSERT INTO userInformation (userName, userDisplayName, userEmailAddress, userPassword) VALUES( '$userName','$userDisplayName','$userEmailAddress','$userPassword' ) ";
    $sqlA = "INSERT INTO userInformation SET userName = '$userName', userDisplayName = '$userDisplayName', userEmailAddress = '$userEmailAddress', userPassword = '$userPassword' ";
    
    $resultA = mysqli_query($conn, $sqlA);
    
	if ($resultA) {
	    $subject = "Confirm your email address on Example.com";
		$htmlbody = "https://login.fighter.php/#/confirmToken?{userName}&amp;{confirmToken}";
		
		$token = confirmToken($conn, $userName);
		
		if( strlen($token) == 64 ){
		    $trans = array(
	            "{userName}"=>"userName=".$userName,
	            "{confirmToken}"=>"confirmToken=".$token
	        );
		    $htmlbody = strtr($htmlbody, $trans);
		    
		    $mailArray = array(
    		    "emailAddress"=> array(
    		        "address" => $userEmailAddress,
    		        "name"    => $userDisplayName
    	        ),
    	        "subject"     => $subject,
    	        "htmlbody"    => $htmlbody
    	    );
    		mailGateway($mailArray);
		}

		http_response_code(201);
		header('Content-Type: application/json');
		echo json_encode('Done so.');
	}else{
	    http_response_code(500);
	    header('Content-Type: application/json');
	    echo json_encode('Failed.');
	}
}

