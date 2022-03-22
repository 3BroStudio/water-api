<?php

debug_backtrace() || die ("Direct access not permitted");

date_default_timezone_set("Asia/Hong_Kong");

function trimAndEscape($conn, $inputVar){
    $inputVar = trim($inputVar);
    $inputVar = mysqli_escape_string($conn, $inputVar);
    return $inputVar;
}

function getUserID($conn, $userName){
    $sql = "SELECT userID FROM user WHERE userName = '$userName' LIMIT 1";
    $sqlResult = mysqli_query($conn, $sql);
    if ( $sqlResult->num_rows > 0 ) {
        $row = $sqlResult->fetch_row();
        return $row[0];
    }else{
        return false;
    }
    return error;
}

function getPro($conn, $userID){
    $sql = "SELECT userID FROM payment WHERE userID = '$userID' LIMIT 1";
    $sqlResult = mysqli_query($conn, $sql);
    if ( $sqlResult->num_rows > 0 ) {
        return true;
    }else{
        return false;
    }
    return error;
}

function drink($conn, $userID, $cups = 1){
    if( $cups == 1 || !isset($cups) )
        $sql = "INSERT INTO record SET userID = '$userID'";
    else
        $sql = "INSERT INTO record SET userID = '$userID', quantity = '$cups'";
    $result = mysqli_query($conn, $sql);
    
    if ( $result ) {
        $sqlFinal = "SELECT SUM(r.quantity) AS cups
            FROM record r
            WHERE r.userID = '$userID'
            LIMIT 1
            ";
        $resultFinal = mysqli_query($conn, $sqlFinal);
        if ( $resultFinal->num_rows > 0 ) {
            $rowFinal = $resultFinal->fetch_assoc();
            return $rowFinal;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function upgrade($conn, $userID){
    $sql = "INSERT INTO payment SET userID = '$userID'";
    $result = mysqli_query($conn, $sql);
    
    if ( $result ) {
        return true;
    }else{
        return false;
    }
}