<?php

require_once 'conn.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {    
   return 0;    
}else{
    $_POST = json_decode(file_get_contents('php://input'), true);
}

$sqlU = "SELECT DISTINCT u.uid,
                u.userName
            FROM users u
            GROUP BY u.uid
            ORDER BY u.uid
            ";

if(isset($_GET['exist'])){
    $sqlU = "SELECT DISTINCT u.uid,
                u.userName,
                COUNT(v.vid) AS totalVideos
            FROM users u
            JOIN videos v ON v.uid = u.uid
            GROUP BY u.uid
            ORDER BY u.uid
            ";
}

if (isset($_GET['uid'])) {
    $uid = trimAndEscape($conn, $_GET['uid']);
    $sqlU .= "WHERE u.uid = '$uid'";
}

$resultU = mysqli_query($conn,$sqlU);

if ($resultU->num_rows > 0) {
    $uData = array();

    while($row = $resultU->fetch_assoc()) {
        $uData[] = $row;
    }

    $outputData = array(
    	"users" => $uData,
    );

    date_default_timezone_set("Asia/Hong_Kong");
    header("Pragma: no-cache");
    header("Expires: 0");

    http_response_code(200);
    header('Content-Type: application/json');

    echo json_encode($outputData);
}

function trimAndEscape($conn, $inputVar){
    $inputVar = trim($inputVar);
    $inputVar = mysqli_escape_string($conn, $inputVar);
    return $inputVar;
}