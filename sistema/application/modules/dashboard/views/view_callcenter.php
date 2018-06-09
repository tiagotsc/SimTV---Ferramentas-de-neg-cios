<?php
    echo link_tag(array('href' => 'assets/css/css_dashboard.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(array('href' => 'assets/componentes/datepicker/css/datepicker.css',
        'rel' => 'stylesheet','type' => 'text/css'));
    echo "<script type='text/javascript' src='".
        base_url('assets/componentes/datepicker/js/bootstrap-datepicker.js')."'></script>";
    //header('Content-Type: text/html; charset=UTF-8');
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="painelCallcenter" class="row">

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading text-center">
                <h3 id="tituloAcomCall" class="panel-title">Acompanhamento Atendidas / Não Atendidas</h3>
            </div>
            <div class="panel-body">
                <div class="row center-block">
                    <div id="graphAcomCall"></div>
                    <div class="preloader">&nbsp;</div>
                </div>
                <div class="row">
                    <div id=""></div>
                </div>
            </div>
            <div class="panel-footer">
                <div id="dataRelatorio" class="row center">
                    <div class="col-md-4">
                         <input class="input-append form-control" value="" id="dpd1" type="text" placeholder="De" readonly>
                    </div>

                    <div class="col-md-4 testeQ">
                         <input class="input-append form-control" value="" id="dpd2" type="text" placeholder="Até" readonly>
                    </div>

                    <div class="col-md-4">
                        <button id="gerarRelatorio" type="button" class="btn btn-primary">Gerar Gráfico</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php
    $dadosJson = file_get_contents('http://telefonia-devel.simtv.com.br/json/acom-callcenter');

?>

<script type="text/javascript">
    $('.abaIndicadores').hide();
    $('.menuIndicadores').hide();
    $('.alert-warning').hide();

</script>

<script type="text/javascript">
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);


    var checkin = $('#dpd1').datepicker({
        onRender: function(date) {
            //return date.valueOf() < now.valueOf() ? 'disabled' : '';
        },
        format: 'dd/mm/yyyy'
    }).on('changeDate', function(ev) {
        if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date)
            newDate.setDate(newDate.getDate());
            checkout.setValue(newDate);
        }
        checkin.hide();
        $('#dpd2')[0].focus();
    }).data('datepicker');

    var checkout = $('#dpd2').datepicker({
        format: 'dd/mm/yyyy'
    }).on('changeDate', function(ev) {
        checkout.hide();
    }).data('datepicker');



</script>

<script type="text/javascript">
    var chart_data;

    $("#gerarRelatorio").click( function () {
        var dataI = $('#dpd1').val();
        var dataF = $('#dpd2').val();

        if ( dataI == "" || dataF == "") {
            load_page_data(dataInicio = null, dataFinal = null)
        } else {
            var arrInicio = dataI.split('/');
            var arrFinal = dataF.split('/');

            var dataInicio = arrInicio[2] + "-" + arrInicio[1] + "-" + arrInicio[0];
            var dataFinal = arrFinal[2] + "-" + arrFinal[1] + "-" + arrFinal[0];

            //alert ( dataInicio );
            load_page_data( dataInicio, dataFinal);

        }
    });

    google.charts.load('current', {'packages':['corechart']});

    google.charts.setOnLoadCallback(load_page_data);

    function load_page_data(dataInicio = null, dataFinal = null){
        if (dataFinal) {

        } else {
            var d = new Date();
            var dia = ("0" + d.getDate()).slice(-2);
            var mes = ("0" + (d.getMonth() + 1)).slice(-2);

            dataInicio = d.getFullYear() + "-" + mes + "-" + dia;
            dataFinal = d.getFullYear() + "-" + mes + "-" + dia;
            //alert( dataInicio );
        }


        $("#graphAcomCall").empty();
        $(".preloader").show()
        var jsonData = $.ajax({
            url: "http://sistemas.simtv.com.br/sistema/json/dadosGraficoCallcenter/" + dataInicio + "/" + dataFinal,
            dataType: "json",
            async: false
        }).responseText;

        if(jsonData){
            var titGraficoAcom = 'Acompanhamento Atendidas / Não Atendidas';
            if ( dataInicio == dataFinal ) {
                $('#tituloAcomCall').text( titGraficoAcom + " - " + dataFinal );
            } else {
                $('#tituloAcomCall').text( titGraficoAcom + "- De: " + dataInicio + " Até: " + dataFinal );
            }

            drawChart(jsonData);

        }
    }

    function drawChart(chart_data) {
        var chart1_data = new google.visualization.DataTable(chart_data);

        var chart1_options = {
            seriesType: 'bars',
            focusTarget: 'category',
            legend: 'none',
            isStacked: 'percent'
        };

        var chart1_chart = new google.visualization.ComboChart(document.getElementById('graphAcomCall'));
        $(".preloader").fadeOut("fast");
        chart1_chart.draw(chart1_data, chart1_options);
    }




</script>




