<style>
    .con{
        width:490px; 
        height: 330px;
        margin-left: 50px;
        /*background-color: black;*/
    }
    #ng{
        margin-top: 10px;
    }
</style>

<?php
echo "<script type='text/javascript' src='" . base_url('assets/js/jquery.blockui/jquery.block.ui.js') . "'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.shiftcheckbox.js") ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/tooltip.css") ?>" />



<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!--<script type="text/javascript" src="chart.js"></script>-->

<div class="con">

    <div id="chart" ></div>
    <!--<button id="btn">click</button>-->
        
</div>




<script type="text/javascript">
    
    
//     var dados = [
//        ["regionais","dados","meta"],
//        ["SIM",	1.62,	6.5],
//        ["SGO",	3.01,	12,],
//        ["NIT",	2.67,	12,],
//        ["SSA",	1.94,	6.7],
//        ["CBA",	1.69,     6],
//        ["JF",	1.54,	8.3],
//        ["RJOP",1.48,     7],
//        ["FSA",	1.47,     8],
//        ["VR",	1.35,     7],
//        ["GTI",	1.02,	4.8],
//        ["AJU",	0.77,	5.8]
//    ];
    
    
//   $('#btn').click(function(){
//        drawChart(dados);
//    });
    
//   $(document).ready(function(){
//       alert(dados);
//        drawChart(dados);
//   });
    
    
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
        
    function drawChart(inf) {
        
        var dados = [
            ["regionais","dados","meta"],
            ["SIM",	1.62,	6.5],
            ["SGO",	3.01,    12],
            ["NIT",	2.67,    12],
            ["SSA",	1.94,	6.7],
            ["CBA",	1.69,     6],
            ["JF",	1.54,	8.3],
            ["RJOP",    1.48,     7],
            ["FSA",	1.47,     8],
            ["VR",	1.35,     7],
            ["GTI",	1.02,	4.8],
            ["AJU",	0.77,	5.8]
        ];
        
        
        // Create the data table.
        var data = google.visualization.arrayToDataTable(dados);
//        data.addColumn('string', 'Topping');
//        data.addColumn('number', 'Slices');
//        data.addRows();

        // Set chart options
        var options = {
            title: 'How Much Slice of Pizza I Ate',
            curveType: 'function',
//            vAxis:{viewWindowMode:'explicit'},
            hAxis:{
                slantedTextAngle:50,
                slantedText:true
            },
            backgroundColor:{
                stroke:'#666',
                strokeWidth:1
            },
            legend:'none',
            seriesType: 'bars',
            width: 423,
            height: 300,
            series:{1: {type:'line'}}
        };
        


        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.ComboChart(document.getElementById('chart'));
        chart.draw(data, options);
    }

    
</script>

<script type="text/javascript">
    
//    /*$('#chart').hide();
//    
    
    
//     var dados = [
//        ["regionais","dados","meta"],
//        ["SIM",	1.62,	6.5],
//        ["SGO",	3.01,	12,],
//        ["NIT",	2.67,	12,],
//        ["SSA",	1.94,	6.7],
//        ["CBA",	1.69,     6],
//        ["JF",	1.54,	8.3],
//        ["RJOP",1.48,     7],
//        ["FSA",	1.47,     8],
//        ["VR",	1.35,     7],
//        ["GTI",	1.02,	4.8],
//        ["AJU",	0.77,	5.8]
//    ];
    
    
//   $('#btn').click(function(){
//        drawChart(dados);
//    });
    
//   $(document).ready(function(){
//       alert(1);
//        drawChart(dados);
//   });
   
        
    
</script>