<?php
// die();
require_once 'conn.php';
require_once 'helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {    
   return 0;    
}else{
    if ( $_SERVER['REQUEST_METHOD'] != 'GET') {
        http_response_code(400);
        header('Content-Type: application/json');
        print(json_encode("GET method required"));
        die();
    }else{
        $_POST = json_decode(file_get_contents('php://input'), true);
    }
}

$sqlMain = "
    SELECT
        u.nickName AS nickname,
        COALESCE(SUM(r.quantity), 0) AS cups,
        COALESCE('no') AS isPro,
        u.userID AS userID
    FROM
        user u 
        LEFT JOIN record r ON u.userID = r.userID 
    GROUP BY
        u.nickName
    ORDER BY
        cups desc
    LIMIT
        10";

$resultMain = mysqli_query($conn, $sqlMain);

if ( $resultMain ) {
    $finalArray = array();
    while ($rowMain = $resultMain->fetch_assoc()) {
        $isPro = getPro($conn, $rowMain["userID"]);
        if($isPro){
            $rowMain["isPro"] = true;
        }
        unset($rowMain["userID"]);
        array_push($finalArray, $rowMain);
    }
    
    // $rowMain = $resultMain->fetch_all(MYSQLI_ASSOC);
    
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($finalArray);
}else{
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(array("message"=>'Database Error'));
    die();
}

die();

