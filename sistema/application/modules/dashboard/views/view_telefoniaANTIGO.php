<?php

echo link_tag(array('href' => 'assets/js/c3js/c3.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/css/css_dashboard.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url('assets/js/c3js/d3.v3.min.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/c3js/c3.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/jQuery.print.js')."'></script>";
?>
<style type="text/css">
/*.c3-xgrid-line line {
    stroke: blue;
}
.c3-xgrid-line.grid4 line {
    stroke: pink;
}
.c3-xgrid-line.grid4 text {
    fill: pink;
}*/

/*.c3-ygrid-line.grid800 line {
    stroke: green;
}
.c3-ygrid-line.grid800 text {
    fill: green;
}*/

/*################### RENTABILIZA��O - IN�CIO #######################*/
/* Texto */
.c3-ygrid-line.grid4 text{
    stroke: #696969;
}

/* Linha */
.c3-ygrid-line.grid4 line{
    stroke: green;
}
/*################### RENTABILIZA��O - FIM #######################*/

.cabecalhoDashboard{
    text-align: center;
}

#chart {
  stroke-width: 5px;
}
</style>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Dashboard / Telefonia</li>
                </ol>
                <div id="divMain">
                    <h3>Telefonia</h3>
                    <div id="dashboards">
                    <?php if(in_array(8, $dashboardPermitidos)){ ?>
                          <!--<h3 onclick="$('#cd_grafico').val(8), $(this).verificaClick();$(this).grafico1(); $(this).grafico2();  $(this).graficoAtivos();">Telefonia</h3>-->
                          <h3 onclick="$('#cd_grafico').val(8), $(this).verificaClick(); $(this).graficoAtivos('N','N'); $(this).graficoReceptivo('N','0800'); $(this).graficoReceptivo('N','4004')">Telefonia</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#pendInstalacao').print()">Imprimir</a>
                            </div>
                            <div id="pendInstalacao">
                                <!--
                                <h5 class="ocultar tituloGrafico">Gr&aacute;fico 1</h5>
                                <div class="row" id="chart1"></div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="ocultar tituloGrafico">Gr&aacute;fico 2 - Quantidade</h5>
                                        <div id="chart2"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="ocultar tituloGrafico">Gr&aacute;fico 3 - Tempo</h5>
                                        <div id="chart3"></div>
                                    </div>
                                </div>
                                -->
                                <h5 class="ocultar tituloGrafico">Gr&aacute;fico 1 - Algar - Acompanhamento ativo</h5>
                                <div class="row dashCamPreechimento">
                                    <div class="col-md-6">
                                    <?php
                                    $options = array('N' => date('m/Y'));		
                            		foreach($mesTelAtivo as $mTa){
                            			$options[$mTa->data_banco] = $mTa->data;
                            		}
                            		
                            		echo form_label('Selecione o m&ecirc;s:', 'periodoTel');
                            		echo form_dropdown('periodoTel', $options, '', 'id="periodoTel" class="form-control"');
                                    ?>
                                    </div>
                                </div>
                                <b>Custo Total Fatura: </b><span id="custoTotal"></span>
                                <a name="telAcomp"></a>
                                <div class="row" id="chart4"></div>
                                
                                <?php
                                #if($this->session->userdata('cd') == 6){
                                ?>
                                <h5 class="ocultar tituloGrafico">Gr&aacute;fico 2 - Algar - Acompanhamento Receptivo - 0800</h5>
                                <div class="row dashCamPreechimento">
                                    <div class="col-md-6">
                                    <?php
                                    $options = array('N' => date('m/Y'));		
                            		foreach($mesTelReceptivo0800 as $mTr0800){
                            			$options[$mTr0800->data_banco] = $mTr0800->data;
                            		}
                            		
                            		echo form_label('Selecione o m&ecirc;s:', 'periodoTelRecep0800');
                            		echo form_dropdown('periodoTelRecep0800', $options, '', 'id="periodoTelRecep0800" class="form-control"');
                                    ?>
                                    </div>
                                </div>
                                <b>Custo Total Fatura: </b><span id="custoTotalReceptivo0800"></span>
                                <div class="row" id="chart6"></div>
                                
                                <h5 class="ocultar tituloGrafico">Gr&aacute;fico 3 - Algar - Acompanhamento Receptivo - 4004</h5>
                                <div class="row dashCamPreechimento">
                                    <div class="col-md-6">
                                    <?php
                                    $options = array('N' => date('m/Y'));		
                            		foreach($mesTelReceptivo4004 as $mTr4004){
                            			$options[$mTr4004->data_banco] = $mTr4004->data;
                            		}
                            		
                            		echo form_label('Selecione o m&ecirc;s:', 'periodoTelRecep4004');
                            		echo form_dropdown('periodoTelRecep4004', $options, '', 'id="periodoTelRecep4004" class="form-control"');
                                    ?>
                                    </div>
                                </div>
                                <b>Custo Total Fatura: </b><span id="custoTotalReceptivo4004"></span>
                                <div class="row" id="chart7"></div>
                                <?php    
                                #}
                                ?>
                                
                                <!--
                                <h5 class="ocultar tituloGrafico">Gr&aacute;fico 5 - Call Center - Detalhado</h5>
                                <div class="row" id="chart5"></div>
                                -->
                            </div>
                          </div>
                    <?php } ?>                                       
                    </div>
                           <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>        
                </div>
            </div>

    <input type="hidden" id="cd_grafico" value="" />
    
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$("#periodoTel").change(function(){
   if($(this).val() != ''){
    $(this).graficoAtivos($(this).val(), '');
   }
    //window.location.href = "#telAcomp";
});

$("#periodoTelRecep0800").change(function(){
   if($(this).val() != ''){
    //$(this).graficoReceptivo($(this).val(), '');
    $(this).carregando(1000);
    $(this).graficoReceptivo($(this).val(), '0800');
   }
    //window.location.href = "#telAcomp";
});

$("#periodoTelRecep4004").change(function(){
   if($(this).val() != ''){
    //$(this).graficoReceptivo($(this).val(), '');
    $(this).carregando(1000);
    $(this).graficoReceptivo($(this).val(), '4004');
   }
    //window.location.href = "#telAcomp";
});

/* Se aba do dashboard for aberta grava log de acesso */
$.fn.verificaClick = function() {
    if(!$(this).hasClass('ui-state-active')) {
        $.post( "<?php echo base_url(); ?>dashboard/registraAcesso/", { cd_grafico: $("#cd_grafico").val()} );
    }
};

$.fn.carregando = function(tempo) {
    
    $.blockUI({ 
    message:  '<h1>Gerando dashboard...</h1>',
    css: { 
    	border: 'none', 
    	padding: '15px', 
    	backgroundColor: '#000', 
    	'-webkit-border-radius': '10px', 
    	'-moz-border-radius': '10px', 
    	opacity: .5, 
    	color: '#fff' 
    	} 
    }); 
    
    setTimeout($.unblockUI, tempo);    
    
}

$(document).ready(function(){
    
    $(function() {
        $( "#dashboards" ).accordion({
            //activate: function( event, ui ) {},
            //beforeActivate: function( event, ui ) {alert(2)},
            collapsible: true, // Habilita a op��o de expandir e ocultar ao clicar
            heightStyle: "content",
            active: false
        });
    });
    
    //$(".data").mask("00/00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    }); 
    
    /*################### GR�FICO 1 - IN�CIO #######################*/
    // GR�FICO 1
    $.fn.grafico1 = function() {
        var chart = c3.generate({
            bindto: '#chart1',
            data: {
                url: '<?php echo base_url(); ?>ajax/telefoniaGrafico1/',
                mimeType: 'json',
                type: 'bar',
                x : 'data',
                columns: ['data','Qtd. Fixo', 'Qtd. Celular', 'Minutos Fixo', 'Minutos Celular'],
                onclick: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                types: {
                    //data3: 'spline',
                    //data6: 'area',
                    'Qtd. Fixo': 'bar',
                    'Qtd. Celular' : 'bar',
                    'Minutos Fixo': 'line',
                    'Minutos Celular': 'line',
                }
            },
            /*subchart: { // Zoom Arrastar
                show: true
            },*/
            zoom: { // Zoom Scroll
                enabled: true
            },
            axis: {
                x: {
                    type: 'category' // this needed to load string x value
                }/*,
                 y : {
                tick: {
                        format: d3.format(",")
                    }
                }*/
            }
        });
    };
    
    // COMPATATIVO SEMANAL
    $.fn.grafico2 = function() {
        var dados1 = [];
        var dados2 = [];
        $.ajax({
          type: "POST",
          url: '<?php echo base_url(); ?>ajax/telefoniaGrafico2',
          /*data: {
            dataPainel: $(this).val(),
            banco: $("#banco_arquivo").val()
          },*/
          dataType: "json",
          /*error: function(res) {
            $("#resQtdArquivosDia").html('<span>Erro de execu��o</span>');
          },*/
          success: function(res) {
            
            
            if(res.length > 0){
                
                var count = 0;
                $.each(res, function() {
                  dados1[count] = [this.campo, this.valor];
                  dados2[count] = [this.campo, this.tempo];
                  count += 1;
                  
                });
                
                var chart = c3.generate({
                    bindto: '#chart2',
                    data: {
                        columns: dados1,
                        type : 'pie',
                        onclick: function (d, i) { console.log("onclick", d, i); },
                        onmouseover: function (d, i) { console.log("onmouseover", d, i); },
                        onmouseout: function (d, i) { console.log("onmouseout", d, i); }
                    },
                    legend: {
                        show: false
                    }
                });
                
                var chart = c3.generate({
                    bindto: '#chart3',
                    data: {
                        columns: dados2,
                        type : 'pie',
                        onclick: function (d, i) { console.log("onclick", d, i); },
                        onmouseover: function (d, i) { console.log("onmouseover", d, i); },
                        onmouseout: function (d, i) { console.log("onmouseout", d, i); }
                    },
                    legend: {
                        show: false
                    }
                });
            }else{
                dados1 = false;
                dados2 = false;
            }
            
          }
        });        
        
    };
    /*################### GR�FICO 1 - FIM #######################*/  
    
    /*################### GR�FICO 4 - IN�CIO #######################*/
    // CALL CENTER - ATIVO - ACOMPANHAMENO
    $.fn.graficoAtivos = function(periodo, zoom) {

        if(periodo){
            var per = periodo;
        }else{
            var per = 'N';
        }
        
        if(zoom){
           var zoo = zoom; 
        }else{
            var zoo = 'N';
        }
        
        var chart1 = c3.generate({
            bindto: '#chart4',
            data: {
                /*url: '<?php echo base_url(); ?>ajax/telefoniagraficoAtivos/'+per+'/'+zoo,*/
                url: '<?php echo base_url(); ?>ajax/telefoniaAtivo/'+per,
                mimeType: 'json',
                type: 'bar',
                x : 'data',
                columns: ['data', /*'Qtd.', 'Minutos',*/ 'Holding - Custo', 'Holding - PosDia', 'CallCenter - PosDia'],
                groups: [
                    ['Holding - PosDia', 'CallCenter - PosDia']
                ],
                /*labels: {
        //            format: function (v, id, i, j) { return "Default Format"; },
                    //format: function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); }
                    format: {
                        'CallCenter - PosDia': function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); },
        //                data1: function (v, id, i, j) { return "Format for data1"; },
                    }
                },*/
                onclick: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                onscroll: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                types: {
                    'Holding - Custo': 'line',
                    'Holding - Minutos': 'line',
                    'Holding - Qtd.': 'line',
                    'CallCenter - Custo': 'line',
                    'CallCenter - Minutos': 'line',
                    'CallCenter - Qtd.': 'line'
                }
            },
            zoom: { // Zoom Scroll
                enabled: true/*,
                onzoom: function (domain) { 
                    if(domain[0] > 0 && domain[0] < 0.65){
                        // Botando zoom
                        $(this).graficoAtivos($("#periodoTel").val(), 'in');   
                    }
                    
                    if(domain[0] < 0 && domain[1] == 11){
                        // Tirando zoom
                        $(this).graficoAtivos($("#periodoTel").val(), 'out'); 
                    }
                }*/
            },
            axis: {
                x: {
                    type: 'category' // this needed to load string x value
                },
                 y : {
                    tick: {
                            //format: d3.format("$,")
                            format: function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); }
                        }
                }
            },
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = id === 'Custo' ? d3.format(',') : d3.format(',');
                        //return 'R$ '+ format(value).replace(',', '.').replace(',', '.');
                        //return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        //return 'R$ '+value;
                        if(id === 'Holding - Custo' || id === 'CallCenter - Custo' || id === 'Holding - PosDia' || id === 'CallCenter - PosDia'){
                            return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        }else{
                            return value;
                        }
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });
        
        // CALL CENTER - DETALHADO
        var chart3 = c3.generate({
            bindto: '#chart5',
            data: {
                url: '<?php echo base_url(); ?>ajax/telefoniaGrafico4/detalhado',
                mimeType: 'json',
                type: 'bar',
                x : 'data',
                columns: ['data', 'Qtd.', 'Minutos', 'Custo'],
                onclick: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                types: {
                    'Minutos': 'line',
                    //'Segundos Celular': 'line',
                }
            },
            zoom: { // Zoom Scroll
                enabled: true
            },
            axis: {
                x: {
                    type: 'category' // this needed to load string x value
                }
            },
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = id === 'Custo' ? d3.format(',') : '';
                        //return 'R$ '+ format(value).replace(',', '.').replace(',', '.');
                        if(id === 'Custo'){
                            return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        }else{
                            return value;
                        }
                        //return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        //return 'R$ '+value;
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });
        
        /* Custo Total */                
        $.ajax({
          type: "POST",
          url: '<?php echo base_url(); ?>ajax/totalFatura/'+per+'/'+zoo+'/ATIVO',
          dataType: "json",
          error: function(res) {
            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
            alert('erro');
          },
          success: function(res) {
            
            $("#custoTotal").html('R$ '+number_format(res['total'],2,',','.')+res['data']);
            
          }
          
        });                        
 
    };
    /*################### GR�FICO 4 - FIM #######################*/ 
    
    /*################### CALL CENTER - RECEPTIVO - ACOMPANHAMENO #######################*/
    $.fn.graficoReceptivoANTIGA = function(periodo, zoom) {
    
        if(periodo){
            var per = periodo;
        }else{
            var per = 'N';
        }
        
        if(zoom){
           var zoo = zoom; 
        }else{
            var zoo = 'N';
        }    
        
        var chart2 = c3.generate({
            bindto: '#chart6',
            data: {
                url: '<?php echo base_url(); ?>ajax/telefoniaGrafico5/'+per+'/'+zoo,
                mimeType: 'json',
                type: 'bar',
                x : 'data',
                columns: ['data', /*'Qtd.', 'Minutos',*/ 'CallCenter - PosDia'],
                groups: [
                    ['CallCenter - PosDia']
                ],
                /*labels: {
        //            format: function (v, id, i, j) { return "Default Format"; },
                    //format: function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); }
                    format: {
                        'CallCenter - PosDia': function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); },
        //                data1: function (v, id, i, j) { return "Format for data1"; },
                    }
                },*/
                onclick: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                onscroll: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                types: {
                    'CallCenter - Custo': 'line',
                    'CallCenter - Minutos': 'line',
                    'CallCenter - Qtd.': 'line'
                }
            },
            zoom: { // Zoom Scroll
                enabled: true/*,
                onzoom: function (domain) { 
                    if(domain[0] > 0 && domain[0] < 0.65){
                        // Botando zoom
                        $(this).graficoAtivos($("#periodoTel").val(), 'in');   
                    }
                    
                    if(domain[0] < 0 && domain[1] == 11){
                        // Tirando zoom
                        $(this).graficoAtivos($("#periodoTel").val(), 'out'); 
                    }
                }*/
            },
            axis: {
                x: {
                    type: 'category' // this needed to load string x value
                },
                 y : {
                    tick: {
                            //format: d3.format("$,")
                            format: function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); }
                        }
                }
            },
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = id === 'Custo' ? d3.format(',') : d3.format(',');
                        //return 'R$ '+ format(value).replace(',', '.').replace(',', '.');
                        //return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        //return 'R$ '+value;
                        if(id === 'CallCenter - Custo' || id === 'CallCenter - PosDia'){
                            return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        }else{
                            return value;
                        }
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });
        
        /* Custo Total */                
        $.ajax({
          type: "POST",
          url: '<?php echo base_url(); ?>ajax/totalFatura/'+per+'/'+zoo+'/RECEPTIVO',
          dataType: "json",
          error: function(res) {
            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
            alert('erro');
          },
          success: function(res) {
            
            $("#custoTotalReceptivo0800").html('R$ '+number_format(res['total'],2,',','.')+res['data']);
            
          }
          
        });
        
    };       

    $.fn.graficoReceptivo = function(periodo, fonte) {
    
        if(periodo){
            var per = periodo;
        }else{
            var per = 'N';
        }
        
        if(fonte == '0800'){
            var divReceptivo = '#chart6';
        }
        
        if(fonte == '4004'){
            var divReceptivo = '#chart7';
        }
        
        /*
        if(zoom){
           var zoo = zoom; 
        }else{
            var zoo = 'N';
        }    
        */          
        var chart2 = c3.generate({
            bindto: divReceptivo,
            data: {
                url: '<?php echo base_url(); ?>ajax/telefoniaReceptivo/'+per+'/'+fonte,
                mimeType: 'json',
                type: 'bar',
                x : 'data',
                columns: ['data', /*'Qtd.', 'Minutos',*/ 'Celular - PosDia', 'Fixo Local - PosDia', 'Fixo LDN - PosDia'],
                groups: [
                    ['Celular - PosDia', 'Fixo Local - PosDia', 'Fixo LDN - PosDia']
                ],
                /*labels: {
        //            format: function (v, id, i, j) { return "Default Format"; },
                    //format: function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); }
                    format: {
                        'CallCenter - PosDia': function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); },
        //                data1: function (v, id, i, j) { return "Format for data1"; },
                    }
                },*/
                onclick: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                onscroll: function (d, i) { 
                    //dump(d); 
                    alert(d.name);
                },
                types: {
                    'Celular - Custo': 'line',
                    'Celular - Minutos': 'line',
                    'Celular - Qtd.': 'line',
                    'Fixo Local - Custo': 'line',
                    'Fixo Local - Minutos': 'line',
                    'Fixo Local - Qtd.': 'line',
                    'Fixo LDN - Custo': 'line',
                    'Fixo LDN - Minutos': 'line',
                    'Fixo LDN - Qtd.': 'line'
                }
            },
            /*legend: {
                show: false
            },*/
            zoom: { // Zoom Scroll
                enabled: true/*,
                onzoom: function (domain) { 
                    if(domain[0] > 0 && domain[0] < 0.65){
                        // Botando zoom
                        $(this).graficoAtivos($("#periodoTel").val(), 'in');   
                    }
                    
                    if(domain[0] < 0 && domain[1] == 11){
                        // Tirando zoom
                        $(this).graficoAtivos($("#periodoTel").val(), 'out'); 
                    }
                }*/
            },
            axis: {
                x: {
                    type: 'category' // this needed to load string x value
                },
                 y : {
                    tick: {
                            //format: d3.format("$,")
                            format: function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); }
                        }
                }
            },
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        //var format = id === 'Custo' ? d3.format(',') : d3.format(',');
                        //return 'R$ '+ format(value).replace(',', '.').replace(',', '.');
                        //return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        //return 'R$ '+value;
                        if(id === 'Celular - Custo' || id === 'Celular - PosDia' || id === 'Fixo Local - Custo' || id === 'Fixo Local - PosDia' || id === 'Fixo LDN - Custo' || id === 'Fixo LDN - PosDia'){
                            return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        }else{
                            return value;
                        }
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });
        
        /* Custo Total */                
        $.ajax({
          type: "POST",
          url: '<?php echo base_url(); ?>ajax/totalFatura/'+per+'/N/'+fonte,
          dataType: "json",
          error: function(res) {
            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
            alert('erro');
          },
          success: function(res) {
            
            $("#custoTotalReceptivo"+fonte).html('R$ '+number_format(res['total'],2,',','.')+res['data']);
            
          }
          
        });
        
    };
    
});


</script>