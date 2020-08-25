<?php
   /*
   Project: Tading Managment Dashboard
   Developer : Tejas Raval - tr7550@rit.edu
   GitHub:https://github.com/tejas101
   */
   include ('config.php');//config file for DB credentials 
   ?><!DOCTYPE html>
<html lang="en">
   <head>
      <title>Dashboard</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <!--===============================================================================================-->
      <link rel="stylesheet" type="text/css" href="css/util.css">
      <link rel="stylesheet" type="text/css" href="css/main.css">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
      <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
	  <!--===============================================================================================-->
   </head>
   <body>
      <div class="clear-fix"></div>
      <div style=" text-align: center; " >
	      <span class="title-span">Trading Managment Dashboard</span>
	      <span style=" float: right; "><a href="http://localhost/Trading/Statistics.php">Statistic</a></span>
	  </div>
	  <!--Table construction using DataTables -->
      <table id="table_id" class="display">
         <thead>
            <tr>
               <th>Trade Id</th>
               <th>Timestamp</th>
               <th>Symbol</th>
               <th>Quantity</th>
               <th>Price</th>
               <th>Is Buyer</th>
               <th>Trader</th>
            </tr>
         </thead>
         <tbody>
            <?php
               // Create connection
               $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE_TRADER);
               // Check connection
               if ($conn->connect_error) {
                   die("Connection failed: " . $conn->connect_error);
               }
               $sql = "SELECT * FROM trade_info";
               $result = mysqli_query($conn, $sql);
               while ($row = mysqli_fetch_array($result)) {//fetch data from SQL and populate the table.2
               ?>
            <tr>
               <td><?php echo $row['trade_id'] ?></td>
               <td><?php echo $row['timestamp'] ?></td>
               <td><?php echo $row['symbol'] ?></td>
               <td><?php echo $row['quantity'] ?></td>
               <td><?php echo $row['price'] ?></td>
               <td><?php echo ($row['is_buy']==0 ?  "No" :  "Yes") ?></td>
               <td><?php echo $row['trader'] ?></td>
            </tr>
            <?php
               }
               ?>
         </tbody>
      </table>
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="jquery/dataTables.cellEdit.js"></script><!--https://github.com/ejbeaty/CellEdit -->
      
<script>$(document).ready(function() {
    //Table format used : DataTable- https://datatables.net/
	//Search box on each coloumn
    $('#table_id thead th').each(function() {
        var title = $(this).text();
        $(this).append('<input class="customSearch" type="text" placeholder="Search ' + title + '" />');
    });
    var table = $('#table_id').DataTable();
    table.columns().every(function() {
        var that = this;
        $('input', this.header()).on('keyup change clear', function() {
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });
    });
	//Making Cell Editable
    table.MakeCellsEditable({
        "onUpdate": callbackFunction,
        "columns": [2, 3, 4, 5, 6], // allow to edit all columns expect Trade ID and Timestamp
        "allowNulls": {
            "columns": [7],
            "errorClass": 'error'
        },
        "confirmationButton": {
            "confirmCss": 'my-confirm-class',
            "cancelCss": 'my-cancel-class'
        },
        "inputTypes": [{
                "column": 2,
                "type": "char"
            },
            {
                "column": 3,
                "type": "number"
            },
            {
                "column": 4,
                "type": "number"
            },
            {
                "column": 5,
                "type": "list",
                "options": [{
                        "value": "Yes",
                        "display": "Yes"
                    },
                    {
                        "value": "No",
                        "display": "No"
                    }
                ]
            }

        ]
    });
});

function callbackFunction(updatedCell, updatedRow, oldValue) {
    var row = updatedRow.data()[0]; //extract trade_id(primary key) from the updatedRow.data
    var newValue = updatedCell.data();
    var col = updatedCell[0][0].column; //Get the column number as it will be neede for Update query.
    $.ajax({
        url: "DB-Update.php",//helper file to do DB operations
        type: "POST",

        data: {
            row,
            newValue,
            col
        },
        success: function(result) {
            if (result == "sucess") {
                console.log("sucess");

            } else {
				alert("Sorry. Some error occured. Please try again later.")
                console.log("error");
            }
        } //success
    });
}
      </script>
   </body>
</html>