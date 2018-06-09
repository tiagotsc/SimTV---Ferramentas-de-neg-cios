<?php
echo link_tag(array('href' => 'assets/css/css_dashboard_call.css', 'rel' => 'stylesheet', 'type' => 'text/css'));
echo link_tag(array('href' => 'assets/componentes/datepicker/css/datepicker.css',
    'rel' => 'stylesheet', 'type' => 'text/css'));
echo "<script type='text/javascript' src='" .
 base_url('assets/componentes/datepicker/js/bootstrap-datepicker.js') . "'></script>";

$this->session->set_userdata('current_menu_pbx', 1);
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!DOCTYPE html>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<div class="col col-md-12 md-offset-3 col-sm-3 main">

    <div class="row-fluid col col-md-12">

        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li class="active">Dashboard</li>
            <li><a href="<?php echo base_url('dashboard/telefonia'); ?>">Telefonia</a></li>
            <li class="active">Ativo - Chamadas [Gateway GSM]</li>
        </ol>

        <div class="row">
            <div class="col-md-11">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center">
                        <h3 id="tituloAsterisk" class="panel-title">VAR JQUERY</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row center-block">
                            <div id="graphTelefonia"><div class="preloader">&nbsp;</div></div>
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

                            <div class="col-md-4">
                                <input class="input-append form-control" value="" id="dpd2" type="text" placeholder="At&eacute" readonly>
                            </div>

                            <div class="col-md-4">
                                <button id="gerarResultado" type="button" class="btn btn-primary">Gerar Gr&aacutefico</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $('.abaIndicadores').hide();
    $('.menuIndicadores').hide();
    $('.alert-warning').hide();
    $('#conteudo').removeClass('container');
    $('#conteudo').addClass('container-fluid');</script>

<script type="text/javascript">
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $('#dpd1').datepicker({
        format: 'dd/mm/yyyy'
    }).on('changeDate', function (ev) {
        if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date);
            newDate.setDate(newDate.getDate());
            checkout.setValue(newDate);
        }
        checkin.hide();
        $('#dpd2')[0].focus();
    }).data('datepicker');
    var checkout = $('#dpd2').datepicker({
        format: 'dd/mm/yyyy'
    }).on('changeDate', function (ev) {
        checkout.hide();
    }).data('datepicker');</script>

<script type="text/javascript">
    var chart_data;
    google.charts.load('current', {'packages': ['corechart', 'bar']});
    google.charts.setOnLoadCallback(load_page_data);

    //Escolha do Gráfico em Google Chart:
    function drawChart(chart_data) {
        var chart1_data = new google.visualization.DataTable(chart_data);
        var chart1_options = {
            seriesType: 'bars',
            bars: 'horizontal',
            focusTarget: 'category',
            isStacked: 'true',
            title: 'Total de Minutagens:',
            legend: {position: 'top', 'alignment': 'center'},
            width: 1020,
            vAxis: {
                baselineColor: '#fff',
                textPosition: 'none'
            },
            fontSize: 10,
            colors: ['CornflowerBlue', 'DimGray', 'Orange', 'OliveDrab']
        };
        var chart1_chart = new
                google.visualization.ComboChart(document.getElementById('graphTelefonia'));
        chart1_chart.draw(chart1_data, chart1_options);
    }

    //Função do botão "Gerar Gráfico":
    $("#gerarResultado").click(function () {

        $(".preloader").show();
        var dataI = $('#dpd1').val();
        var dataF = $('#dpd2').val();
        if (dataI === "" || dataF === "") {
            load_page_data(dataInicio = '', dataFinal = '');
        } else {
            var arrInicio = dataI.split('/');
            var arrFinal = dataF.split('/');
            var dataInicio = arrInicio[2] + "-" + arrInicio[1] + "-" + arrInicio[0];
            var dataFinal = arrFinal[2] + "-" + arrFinal[1] + "-" + arrFinal[0];
            //alert ( dataInicio );
            load_page_data(dataInicio, dataFinal);
        }
    });
    //Carrega como padrão os parâmetros de "DataInicio" e "DataFinal" para chamada do Ajax:
    function load_page_data(dataInicio = '', dataFinal = ''){
        $("#graphTelefonia").empty();
        if (dataFinal) {

        } else {
            var d = new Date();
            var dmu = new Date();
            dmu.setDate(d.getDate() - 1);
            var dia = (("0" + dmu.getDate()).slice(-2));            
            var mes = ("0" + (dmu.getMonth() + 1)).slice(-2);
            var ano = dmu.getFullYear();
            dataInicio = ano + "-" + mes + "-" + dia;
            dataFinal = ano + "-" + mes + "-" + dia;
            //alert( dataInicio );
        }

        //Criando título com variável "DE" e "ATÉ":

        var tituloPBX = 'Ativo - Chamadas - Gateway GSM ';
        if (dataInicio === dataFinal) {
            $('#tituloAsterisk').text(tituloPBX + '  [D-1] <' + dataInicio + '>');
        } else {
            $('#tituloAsterisk').text(tituloPBX + '   de:  (' + dataInicio + ')' + ' ate: (' + dataFinal + ')');
        }
        ;


        //Ajax captura as informações do JSON + parâmetros do DatePicker:
        var jsonData = $.ajax({
            url: "/sistema/dashboard/telefonia/dadosTelefoniaAtivoGSM/" + dataInicio + "/" + dataFinal,
            dataType: "json",
            async: false
        }).responseText;

        drawChart(jsonData);

    }

</script>