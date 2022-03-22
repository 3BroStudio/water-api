<?php

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

$validAccount = false;

if (!isset($_POST['userName']) or !isset($_POST['token'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    print(json_encode("not enough parameter"));
    die();
}

$userName = strtolower(trimAndEscape($conn, $_POST['userName']));
$validAccount = checkUserByUserName($conn, $userName);

if( !$validAccount ){
    http_response_code(400);
    header('Content-Type: application/json');
    print(json_encode("username is not valid"));
    die();  
}

if( isset($_POST['token']) ){
    $token = trimAndEscape($conn, $_POST['token']);
}else{
    http_response_code(400);
    header('Content-Type: application/json');
    print(json_encode("no token provided"));
    die();  
}

if ( strlen($_POST['token']) != 64) {
    http_response_code(400);
    header('Content-Type: application/json');
    print(json_encode("token not in correct format"));
    die();
}

$sqlVerify = "SELECT tokenID FROM userToken WHERE userName = '$userName' AND token = '$token' LIMIT 1 ";
$resultVerify = mysqli_query($conn, $sqlVerify);

if ( $resultVerify->num_rows > 0 ) {
    //token and username exists
    $row = $resultVerify->fetch_row();
    $tokenID = $row[0];

    $sqlLogout = "DELETE FROM userToken WHERE tokenID = '$tokenID' LIMIT 1";
    $resultLogout = mysqli_query($conn, $sqlLogout);

    if( $conn->affected_rows > 0 ){
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode('Logout successfully.');
    }else{
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode('Server/DB error');
    }

}else{
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode('Incorect username/token combination');
}

