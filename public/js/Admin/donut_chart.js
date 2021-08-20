google.charts.load("current", {packages:["corechart","bar"]});
google.charts.setOnLoadCallback(getCategoriesDataToBuildChart);

// Send ajax request to get statistics about categories
function getCategoriesDataToBuildChart()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let categoriesData = JSON.parse(this.responseText);
            let drawChartArray = [];
            drawChartArray.push(['קטגוריה','מוצרים נמכרו']);
            for (category in categoriesData) {
                drawChartArray.push([categoriesData[category].display_name, parseInt(categoriesData[category].sold_products)]);
            }
            drawChartCategories(drawChartArray,{
                title: 'הקטגוריות המובילות',
                pieHole: 0.4,
            });
        }
    };
    xhttp.open("POST", "../../statistics/categories_statistics", true);
    xhttp.send();
}


function drawChartCategories(drawChartArray,optionsObj) 
{
    var data = google.visualization.arrayToDataTable(drawChartArray);
    var options = optionsObj;
    var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
    chart.draw(data, options);
}

