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
$trader =isset($_POST['trader'])?$_POST['trader']:'';
$sql = 'SELECT * FROM trade_info WHERE trader="'.$trader.'"';
$result = mysqli_query($conn, $sql);
$rowNew=array();
   while ($row = mysqli_fetch_array($result)) {
  
   array_push($rowNew,$row);
   
   }   
   echo json_encode(($rowNew));// return the data for a trader
   
   ?>