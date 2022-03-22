<?php
// die();
require_once 'conn.php';
require_once 'helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {    
   return 0;    
}else{
    if ( $_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(400);
        header('Content-Type: application/json');
        print(json_encode("POST method required"));
        die();
    }else{
        $_POST = json_decode(file_get_contents('php://input'), true);
    }
}

if (!isset($_POST['username'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    print(json_encode(array("message"=>"not enough parameter")));
    die();
}else{
    $userName = trimAndEscape($conn, $_POST['username']);
    $userID = getUserID($conn, $userName);
    
    if( !$userID ){
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array("message"=>'Username not found'));
        die();
    }
}

$resultPro = getPro($conn, $userID);

if ( $resultPro ) {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array("result"=>'true'));
    die();
}else{
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(array("result"=>'false'));
    die();
}

http_response_code(500);
header('Content-Type: application/json');
echo json_encode(array("message"=>'Database error'));
die();

