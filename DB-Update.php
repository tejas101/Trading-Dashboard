<?php
/*
   Project: Tading Managment Dashboard
   Developer : Tejas Raval - tr7550@rit.edu
   GitHub:https://github.com/tejas101
   */
include ('config.php');
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE_TRADER);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//update the data based on the primary key
$row =isset($_POST['row'])?$_POST['row']:'';
$newValue =isset($_POST['newValue'])?$_POST['newValue']:'';
$col =isset($_POST['col'])?$_POST['col']:'';
/**
If is_buy column is updated, replace the new value with the
appropriate value for table update. 
*/
if($col==5){
	if($newValue=="No"){
		$newValue="0";
	}
	else{
		$newValue="1";
	}
}
$sql='UPDATE trade_info SET '.TABLE_STRUCTURE[$col].'="'.$newValue.'" WHERE trade_id="'.$row.'"';
$result = mysqli_query($conn, $sql);
if(mysqli_error($conn)){
	echo "error";
	exit(); 
}
else{
	echo "sucess";
}
   ?>