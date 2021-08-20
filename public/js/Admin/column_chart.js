google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(getUsersDataToBuildChart);




/**
 * FROM users JOIN orders
 * WHERE users.id = orders.user_id
 * AND orders.start_date >= "2020-01-01"
 * AND users.created_at >= "2019-01-01" 
 * AND users.created_at < "2021-01-01"
 */

 function getUsersDataToBuildChart()
 {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          let usersData = JSON.parse(this.responseText);
          let drawChartArray = [];
          drawChartArray.push(['שנה', 'משתמשים רשומים', 'משתמשים פעילים']);

          let maxYear = 0;
          let minYear = 2100;
          for (year in usersData) {
            maxYear = Math.max(maxYear,year);
            minYear = Math.min(minYear,year);
            drawChartArray.push([year, parseInt(usersData[year].total_users) , parseInt(usersData[year].active_users)]);
          }
          drawChartUsers(drawChartArray,{chart: {
              title: 'פעילות משתמשים רשומים',
              subtitle: `פעילות משתמשים בין השנים ${minYear} - ${maxYear}`
          }});
        }
      };
      xhttp.open("POST", "../../statistics/users", true);
      xhttp.send();
 }



function drawChartUsers(drawChartArray,optionsObj) {
  var data = google.visualization.arrayToDataTable(drawChartArray);
  var options = optionsObj;
  var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
  chart.draw(data, google.charts.Bar.convertOptions(options));
}

