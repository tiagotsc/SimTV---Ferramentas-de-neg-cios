<?php
echo link_tag(array('href' => 'assets/css/css_dashboard_call.css', 'rel' => 'stylesheet', 'type' => 'text/css'));
echo link_tag(array('href' => 'assets/css/helpdesk.css',
    'rel' => 'stylesheet', 'type' => 'text/css'));
echo "<script type='text/javascript' src='" .
 base_url('assets/componentes/datepicker/js/bootstrap-datepicker.js') . "'></script>";

//header('Content-Type: text/html; charset=UTF-8');

$this->session->set_userdata('current_helpDesk', 0);
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="col col-md-12 md-offset-3 col-sm-3 main">

    <div class="row-fluid col col-md-11">

        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li class="active">Dashboard / Help-Desk</li>
        </ol>

        <div class="row">
            <div class="col-md-11">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center">
                        <h3 id="tituloImpressao" class="panel-title">Chamados em Aberto</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row center-block">
                            <div id="graphHelpDesk"></div>
                        </div>
                        <div class="preloader">&nbsp;</div>
                        <div class="row">
                            <div id=""></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
//Refresh de 5 em 5 segundos
    setInterval("load_page_data()", 5000);

    var chart_data;
    google.charts.load('current', {'packages': ['corechart', 'bar']});
    google.charts.setOnLoadCallback(load_page_data);
//Escolha do Gráfico em Google Chart:
    function drawChart(chart_data) {
        var chart1_data = new google.visualization.DataTable(chart_data);

        var chart1_options = {
            bars: 'horizontal',
            legend: {position: 'top', 'alignment': 'center'},
            vAxis: {format: 'short'}
        };

        var chart1_chart = new google.visualization.ColumnChart(document.getElementById('graphHelpDesk'));
        chart1_chart.draw(chart1_data, chart1_options);

        $(".preloader").hide();
    }

//Carrega gráfico p/ Ajax:
    function load_page_data() {
//        $("#graphHelpDesk").empty();
        $(".preloader").show();
//Ajax captura as informações do JSON + parâmetros do DatePicker:
        var jsonData = $.ajax({
            url: "/sistema/dashboard/helpdesk/dadosGraficoHelpDesk/",
            dataType: "json",
            async: false
        }).responseText;
        drawChart(jsonData);
    }

</script>