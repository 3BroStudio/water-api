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

if (!isset($_POST['username']) OR !isset($_POST['nickname'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    print(json_encode(array("message"=>"not enough parameter")));
    die();
}else{
    $userName = trimAndEscape($conn, $_POST['username']);
    $nickName = trimAndEscape($conn, $_POST['nickname']);
    $presetCups = false;
    $presetPro = false;
}

if ( isset($_POST['cups']) && is_numeric($_POST['cups']) ) {
    $presetCups = $_POST['cups'];
}

if ( isset($_POST['isPro']) && $_POST['isPro'] === true ) {
    $presetPro = true;
}


$sqlMain = "
    INSERT INTO
        user
    SET
        userName = '$userName',
        nickName = '$nickName'
    ";

$resultMain = mysqli_query($conn, $sqlMain);

if ( $resultMain ) {
    http_response_code(200);
    header('Content-Type: application/json');
    $msg = array("message"=>"Registered successfully");
    if( $presetPro ){
        upgrade($conn, $conn->insert_id);
        $msg["message"] .= " as Pro user";
    }
    if( $presetCups ){
        drink($conn, $conn->insert_id, $presetCups);
        $msg["message"] .= " with preset cups of water";
    }
    echo json_encode($msg);
}else{
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(array("message"=>'Database Error'));
    die();
}

die();

