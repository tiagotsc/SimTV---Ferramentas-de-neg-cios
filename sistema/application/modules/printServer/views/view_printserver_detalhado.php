<style>

    .label-form{
        margin-right: -25px;        
    }

</style>

<?php
echo link_tag(array('href' => 'assets/css/css_dashboard_call.css', 'rel' => 'stylesheet', 'type' => 'text/css'));
echo link_tag(array('href' => 'assets/componentes/datepicker/css/datepicker.css',
    'rel' => 'stylesheet', 'type' => 'text/css'));
echo "<script type='text/javascript' src='" .
 base_url('assets/componentes/datepicker/js/bootstrap-datepicker.js') . "'></script>";

header('Content-Type: text/html; charset=UTF-8');

$this->session->set_userdata('current_menu_ps', 1);
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<div class="col col-md-12 md-offset-3 col-sm-3 main">

    <div class="row-fluid col col-md-11">

        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li class="active">Dashboard / Print Server</li>
        </ol>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center">
                        <!--<h3 id="tituloImpressao" class="panel-title">Acompanhamento de Impress&atildeo</h3>-->
                        <h3 id="tituloImpressao" class="panel-title">Acompanhamento Detalhado</h3>
                    </div>

                    <div class="panel-footer">

                        <form class="form-horizontal col col-md-10" role="form">
                            <div class="form-group">

                                <label id="und-label" class="col-sm-1 control-label">Unidade: </label>
                                <div class="col-md-offset-1 col-md-3">
                                    <?php
                                    $option3 = array('' => '');
                                    foreach ($undArray as $lo) {
                                        $option3[$lo->PERMISSOR] = htmlentities($lo->LOCALIDADE);
                                    }
                                    echo form_dropdown('und', $option3, $und, 'id="und" class="form-control"');
                                    ?>
                                </div>

                                <label id="ano-label" class="label-form col-sm-1 control-label">Ano: </label>
                                <div class="col-md-2">
                                    <?php
                                    $option2 = array('' => '');
                                    foreach ($undArray as $a) {
                                        $option2[$a->ANO] = htmlentities($a->ANO);
                                    }
                                    echo form_dropdown('ano', $option2, $ano, 'id="ano" class="form-control"');
                                    ?>
                                </div>

                                <label id="mes-label" class="label-form col-sm-1 control-label">M&ecircs: </label>
                                <div class="col-md-2">
                                    <select id="mes" class="form-control">

                                    </select>
                                </div>                                
                            </div>
                        </form>
                        <div class="col-md-2">
                            <button id="gerarResultado" type="button" class="btn btn-primary">Gerar</button>
                        </div>

                        <div class="row">
                            <div id=""></div>
                        </div>
                    </div>


                    <div class="panel-body">
                        <div class="row center-block">
                            <div id="graphPrintServer"><div class="preloader">&nbsp;</div></div>
                        </div>
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
    $(document).ready(function () {

//        $("#mes").hide();
//        $("#ano").hide();
//        $("#mes-label").hide();
//        $("#ano-label").hide();
//        $('#gerarResultado').hide();

    });

    $("#und").on('change', function () {

        var localidade = $('#und').val();

        $.ajax({
//                type: "POST",
            url: '/sistema/printserver/ajaxPrintServer/dados',
            data: {
                und: localidade
            },
            dataType: "json",
            success: function (data) {

                var opt_mes = "<option value = ''> </option>";

                console.log(data);

                $.each(data['mes'], function (index) {
                    opt_mes += "<option value = '" + this.MES + "'>" + this.MES + "</option>";
                });

                $('#mes').html(opt_mes);

            }
        });

    });


    var chart_data;
    google.charts.load('current', {'packages': ['corechart', 'bar']});
    google.charts.setOnLoadCallback(load_page_data);

    //Escolha do Gráfico em Google Chart:
    function drawChart(chart_data) {
        var chart1_data = new google.visualization.DataTable(chart_data);
        var chart1_options = {
            seriesType: 'bars',
            bars: 'horizontal',
            title: 'Total impressos:',
            focusTarget: 'category',
            legend: {position: 'none'}
        };

        var chart1_chart = new
                google.visualization.ComboChart(document.getElementById('graphPrintServer'));
        chart1_chart.draw(chart1_data, chart1_options);
    }

    //Função do botão "Gerar Gráfico":
    $("#gerarResultado").click(function () {
        $(".preloader").show();
        load_page_data();


    });
    //Carrega como padrão os parâmetros de "DataInicio" e "DataFinal" para chamada do Ajax:
    function load_page_data(dataInicio = '', dataFinal = ''){
        $("#graphPrintServer").empty();

        var localidade = $('#und').val();
        var mes = $('#mes').val();
        var ano = $('#ano').val();

//        var und;
//        if (dataFinal) {
//
//        } else {
//            var d = new Date();
//            var dia = (("0" + d.getDate()).slice(-2)) - 1;
//            var mes = ("0" + (d.getMonth() + 1)).slice(-2);
//            var ano = d.getFullYear();
//            dataInicio = ano + "-" + mes + "-" + dia;
//            dataFinal = ano + "-" + mes + "-" + dia;
//            //alert( dataInicio );
//        }

        //Ajax captura as informações do JSON + parâmetros do DatePicker:
        var jsonData = $.ajax({
            url: "/sistema/dashboard/printserver/dadosPrintServerDetalhado/" + localidade + '/' + ano + '/' + mes,
            dataType: "json",
            async: false
        }).responseText;

        drawChart(jsonData);

    }

</script>