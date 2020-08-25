<?php
/*
Project: Trading Managment Dashboard
Developer : Tejas Raval - tr7550@rit.edu
GitHub:https://github.com/tejas101



*/
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_DATABASE_TRADER', 'trader');
   define('DB_DATABASE_TABLE', 'trade_info');
   define('TABLE_STRUCTURE', array("trade_id", "timestamp", "symbol","quantity", "price", "is_buy","trader"));
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE_TRADER);
?>