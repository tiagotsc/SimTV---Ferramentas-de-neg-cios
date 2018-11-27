// Load Charts and the corechart and barchart packages.
google.charts.load('current', {'packages':['corechart']});

// Draw the pie chart and bar chart when Charts is loaded.
google.charts.setOnLoadCallback(drawChart);

function drawChart() {


    var line_options = {
        legend: { position: 'bottom' }
    };

    //var linechart = new google.visualization.LineChart(document.getElementById('barchart_div'));
    //linechart.draw(data1, line_options);




    var jsonData = $.ajax({
        url: "http://telefonia-devel.simtv.com.br/json/acom-call",
        dataType: "json",
        async: false
    }).responseText;

    var data1 = new google.visualization.DataTable(jsonData);


    var options = {
        //title : 'Monthly Coffee Production by Country',
        //vAxis: {title: 'Valor'},
        //hAxis: {title: ("0" + dataAtual.getDate()).slice(-2) + '\\' + ("0" + dataAtual.getMonth()).slice(-2) + '\\' + dataAtual.getFullYear()},
        //hAxis: {title: 'Titulo'},
        seriesType: 'bars',
        //bar: { groupWidth: '30%' },
        //series: {3: {type: 'line'}},
        focusTarget: 'category',
        //tooltip: {isHtml: true},
        legend: 'none',
        isStacked: true,
        //legend: { position: 'bottom', maxLines: 3 },


    };

    var chart = new google.visualization.ComboChart(document.getElementById('teste'));
    chart.draw(data1, options);




}

