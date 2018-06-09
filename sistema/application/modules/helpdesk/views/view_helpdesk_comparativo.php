<?php
echo link_tag(array('href' => 'assets/css/css_dashboard_call.css', 'rel' => 'stylesheet', 'type' => 'text/css'));
echo link_tag(array('href' => 'assets/componentes/datepicker/css/datepicker.css',
    'rel' => 'stylesheet', 'type' => 'text/css'));
echo "<script type='text/javascript' src='" .
 base_url('assets/componentes/datepicker/js/bootstrap-datepicker.js') . "'></script>";

//header('Content-Type: text/html; charset=UTF-8');

$this->session->set_userdata('current_helpDesk', 5);
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="col col-md-11 md-offset-3 col-sm-3 main">
    <div class="row-fluid col col-md-11">

        <div class="panel panel-primary">
            <div class="panel-heading text-center">
                <h3 id="tituloComparativo" class="panel-title"><b>Comparativo</b></h3>
            </div>
            <div class="panel-footer">

                <form class="form-horizontal col col-md-8" role="form">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">De: </label>
                        <div class="col-sm-1 col-md-3">
                            <?php
                            $option = array('' => '');
                            foreach ($ANO as $a) {
                                $option[$a->ANO] = $a->ANO;
                            }
                            echo form_dropdown('anoInicial', $option, $anoInicial, 'id="anoI" class="form-control"');
                            ?>
                        </div>

                        <label class="col-sm-2 control-label">At&eacute: </label>
                        <div class="col-sm-1 col-md-3">
                            <?php
                            $option2 = array('' => '');
                            foreach ($ANO as $a) {
                                $option2[$a->ANO] = $a->ANO;
                            }
                            echo form_dropdown('anoFinal', $option2, $anoFinal, 'id="anoF" class="form-control"');
                            ?>
                        </div>
                    </div>
                </form>

                <div class="col-md-4">
                    <button id="gerarResultado" type="button" class="btn btn-primary">Gerar Gr&aacutefico</button>
                </div>
                <div class="row">
                    <div id=""></div>
                </div>
            </div>

            <div class="panel-body">
                <!--<div class="row center-block">-->
                    <div id="graphHelpDesk"><div class="preloader">&nbsp;</div></div>
                <!--</div>-->
            </div>
            <!--                    Footer aqui-->
        </div>
    </div>



    <script type="text/javascript">
        $('.abaIndicadores').hide();
        $('.menuIndicadores').hide();
        $('.alert-warning').hide();
        $('#conteudo').removeClass('container');
        $('#conteudo').addClass('container-fluid');</script>

    <script type="text/javascript">
        var chart_data;
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(load_page_data);
        //Escolha do Gráfico em Google Chart:
        function drawChart(chart_data) {
            var chart1_data = new google.visualization.DataTable(chart_data);
            //var chart1_data = chart_data;

            var chart1_options = {
                seriesType: 'bars',
                bars: 'horizontal',
                focusTarget: 'category',
                //            isStacked: 'percent',
                'width': 1100,
                'height': 460,
                legend: {position: 'right'}
            };
            var chart1_chart = new google.visualization.ComboChart(document.getElementById('graphHelpDesk'));
            chart1_chart.draw(chart1_data, chart1_options);
        }

        //Função do botão "Gerar Gráfico":
        $("#gerarResultado").click(function () {

            $(".preloader").show();
            var anoIni = $('#anoI').val();
            var anoFim = $('#anoF').val();

            load_page_data(anoIni, anoFim);
        });

        //Carrega gráfico p/ Ajax:
        function load_page_data(anoIni = '', anoFim = '') {
            $("#graphHelpDesk").empty();
            if (anoFim) {

            } else {
                var d = new Date();
                var dia = ("0" + d.getDate()).slice(-2);
                var mes = ("0" + (d.getMonth() + 1)).slice(-2);
                anoIni = d.getFullYear();
                anoFim = d.getFullYear();
                //alert( dataInicio );
            }


            //Ajax captura as informações do JSON + parâmetros do DatePicker:
            var jsonData = $.ajax({
                url: "/sistema/dashboard/helpdesk/dadosHelpDeskComparativo/" + anoIni + "/" + anoFim,
                dataType: "json",
                async: false
            }).responseText;
            drawChart(jsonData);
        }



    </script>