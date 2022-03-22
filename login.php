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
        echo json_encode(array("message"=>'username not found'));
        die();
    }
}

$sqlMain = "
    SELECT
        u.nickName AS nickname,
        SUM(r.quantity) AS cups,
        COALESCE('false') AS isPro 
    FROM
        user u 
        LEFT JOIN record r ON u.userID = r.userID 
    WHERE
        u.userID = '$userID'
    LIMIT
        1";

$resultMain = mysqli_query($conn, $sqlMain);

if ( $resultMain->num_rows > 0 ) {

    $rowMain = $resultMain->fetch_assoc();
    
    $isPro = getPro($conn, $userID);
    if($isPro){
        $rowMain["isPro"] = true;
    }
    
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($rowMain);
}else{
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(array("message"=>'Database Error'));
    die();
}

die();

