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
   $sql = "SELECT DISTINCT trader FROM trade_info";
   $result = mysqli_query($conn, $sql);
   
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>Dashboard</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!--===============================================================================================-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.1.1/flatly/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="css/util.css">
      <link rel="stylesheet" type="text/css" href="css/main.css">
   </head>
   <body>
      <div class="clear-fix"></div>
      <div style=" text-align: center; " >
	      <span class="title-span">Trading Managment Dashboard</span>
	      <span style=" float: right; "><a href="http://localhost/Trading/">Trading Table</a></span>
	  </div>
      <div  class="infoDiv">
         <select id="selectTrader"><!--Trader DropDown-->
            <option value="default">Select a Trader</option>
            <?php while ($row = mysqli_fetch_array($result)) {
               ?>
            <option value="<?php echo $row['trader']; ?>"><?php echo $row['trader']; ?></option>
            <?php } ?>
         </select>
      </div>
      <div class="infoDiv">
         <div><span>Total # of trades :</span><span id="numTraders"></span></div>
         <div><span>Share of each stock :</span><span id="shareTraders"></span></div>
         <div><span>Average buying price :</span><span id="averageBuying"></span></div>
         <div><span>Average selling price :</span><span id="averageSelling"></span></div>
      </div>
      <div  class="infoDiv"><span>Number Of Trades For Each User:</span></div>
      <div style=" display: flex; width: 100%; ">
         <div id="pieChartHolder">
            <div class="chart-info">Pie Chart with Number of Trades</div>
            <div id="pieChart"></div>
         </div>
         <div id="barChartHolder">
            <div  class="chart-info">
               <span>Bar Chart with Number of Trades<span>
               <span>
                  <h6 style=" color: red;font-size: 13px; ">  X axix:Symbols, Y axix:Trades</h6>
               </span>
            </div>
            <div class="graph" id="barChart"></div>
         </div>
      </div>
</body>
<script src="jquery/jquery-3.2.1.min.js"></script>
<script src="https://d3js.org/d3.v4.js"></script>
<script src="Pie-Chart/Pie-Chart.js"></script>
<script src="Bar-Chart/Bar-Chart.js"></script>
<script src="Bar-Chart/graphite.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>
<script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>
<script>$(document).ready(function() {
    $("#selectTrader").change(function() {
		clearUI();// clear UI before each on change event to remove old stats.
        $('#selectTrader option[value="default"]').attr("disabled", true);
        var trader = $(this).children("option:selected").val(); //select a trader first
        //console.log(a);
        if (trader.length != 0) {
            $.ajax({
                url: "Statistics-helper.php",//helper code to return data for a trader.
                type: "POST",

                data: {
                    trader
                },
                success: function(result) {
                    const numberShares = JSON.parse(result).reduce((acc, it) => {
                        acc[it.symbol] = acc[it.symbol] + 1 || 1;
                        return acc;
                    }, {});
                    $("#numTraders").text(JSON.parse(result).length);//length of result object obtained from a trader will be number of trades
					//call calculateShares() to get Shares for each trader
                    $.each(calculateShares(JSON.parse(result)), function(index, value) {
                        if (value['val'] != "NaN")
                            $("#shareTraders").append(" " + value['symbol'] + " = " + value['val'] + ",");
                    });
					//Call calculateAvg() to get avarage selling price of the share 
                    $.each(calculateAvg('0', JSON.parse(result)), function(index, value) {

                        //console.log("hesssre ",index,value);
                        if (value['val'] != "NaN")
                            $("#averageSelling").append(" " + value['symbol'] + " = " + value['val'] + "$ ,");
                    });
					//Call calculateAvg() to get avarage buying price of the share.
                    $.each(calculateAvg('1', JSON.parse(result)), function(index, value) {
                        if (value['val'] != "NaN")
                            $("#averageBuying").append(" " + value['symbol'] + " = " + value['val'] + "$ ,");
                    });
                    $(".chart-info").show();
                    makePieChart(numberShares);//make Pie CHart based on the number of Symbols a trader have
                    makeBarChart(numberShares);//make Bar Chart based on the number of Symbols a trader have
                } //success
            });
        } else {
            clearUI();
        }
    });

});
/**
calculateShares()
Parameter: All the rows returned by SQL for a particular trader
Return: Object containg the all the symbols and their number of shares.
 
This function number of shares(quantity) each trader have till date.
Summation of all the bought shares is substracted from the summation
of all the sold shares till date.
Assumption - If is_buyer is false or 0 then I consider that Trader as seller for that
particular transcation. And, vice a versa 
**/
function calculateShares(data) {
    var symbols = _.uniq(data, item => item.symbol);
    symbols = _.pluck(symbols, 'symbol');//compute how many symbols a trader has
    var finalShare = [];
    $.each(symbols, function(index, value) {
        var shareSymb = [];
        $.each([1, 0], function(id, vl) {// for each is_buyer=1 and is_buyer=0, do the summation of all its shares.
            var filterData = _.filter(data, function(d) {
                return d.symbol == value && d.is_buy == vl
            });
            filterData = _.pluck(filterData, "quantity");
            filterData = _.reduce(filterData, function(a, b) {
                return parseInt(a) + parseInt(b);//summation done here
            }, 0);

            shareSymb.push(filterData);
			//shareSymb[0] holdes summation of buying while shareSymb[1] holder summation of selling
        });

        finalShare.push({
            "symbol": value,
            "val": Math.abs(shareSymb[0] - shareSymb[1])//substraction done to get the  remaing shares with the trader.
        })
    });
    return _.sortBy(finalShare, "val");// return the object to be displayed on the UI.
}

/**
calculateAvg()
Parameter: isBuy
           When isBuy=0, Avgrage selling price of each share will be computed.
           When isBuy=1, Avgrage buying price of each share will be computed.
		   data =All the rows returned by SQL for a particular trader
		   
Return: Object containg the all the symbols and average  prices.
**/
function calculateAvg(isBuy, data) {
    var symbols = _.uniq(data, item => item.symbol);
    symbols = _.pluck(symbols, 'symbol');//compute how many symbols a trader has
    var finalAvg = [];
    $.each(symbols, function(index, value) {
        var filterData = _.filter(data, function(d) {//filter based on quantity and price for a trader in each element of the object
            return d.symbol == value && d.is_buy == isBuy
        });
        var filterQuantity = _.pluck(filterData, "quantity");
        var filterPrice = _.pluck(filterData, "price");
        var temp = [filterQuantity, filterPrice];
        var ansIS = _.map(_.zip.apply(_, temp), function(pieces) {
            return parseInt(pieces[0]) * parseInt(pieces[1]);//multiply price of share by its quantity
        });
        var totalQuantity = _.reduce(filterQuantity, function(a, b) {
            return parseInt(a) + parseInt(b) // add all the answers obtained from the previous operation
        })
        var calculate = ((_.reduce(ansIS, function(a, b) {
            return parseInt(a) + parseInt(b)
        })) / totalQuantity).toFixed(2);//find the avetage by divding the summ of all (share*quantity) by the total quantitys
        finalAvg.push({
            "symbol": value,
            "val": calculate
        });

    });
    return _.sortBy(finalAvg, "val");// return the object to be displayed on the UI.
}

function clearUI() {// clear the UI  
    $("#numTraders").text("");
    $("#shareTraders").text("");
    $("#averageBuying").text("");
    $("#averageSelling").text("");
    $('#pieChart').empty();
    $(".chart-info").hide();
}
      
   </script>
</html>