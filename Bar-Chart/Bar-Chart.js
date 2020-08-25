function makeBarChart(data){
$('#barChart').empty();
var exampleOptions = {
  'height': 350,
  'title': '',
  'width': $(window).width()/2,
  'fixPadding': 18,
  'barFont': [0, 12, "bold"],
  'labelFont': [0, 13, 0]
};
graphite(data, exampleOptions, barChart);
	
}