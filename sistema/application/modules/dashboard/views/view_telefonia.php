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
                          <h3>Ativo</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#telefonia1').print()">Imprimir</a>
                            </div>
                            <div id="telefonia1">
                                <h5 class="ocultar tituloGrafico">Algar - Acompanhamento ativo</h5>
                                <div class="row dashCamPreechimento">
                                    <div class="col-md-6">
                                    <?php
                            		$options = array('' => '');
                            		echo form_label('Selecione o m&ecirc;s:', 'periodoTel');
                            		echo form_dropdown('periodoTel', $options, '', 'id="periodoTel" class="form-control"');
                                    ?>
                                    </div>
                                </div>
                                <b>Custo Total Fatura: </b><span id="custoTotal"></span>
                                <a name="telAcomp"></a>
                                <div class="row" id="chart4"></div>
                            </div>
                          </div>
                    <?php } ?>  
                    <?php if(in_array(11, $dashboardPermitidos)){ ?>
                          <h3>Receptivo 0800</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#telefonia2').print()">Imprimir</a>
                            </div>
                            <div id="telefonia2">
                                <h5 class="ocultar tituloGrafico">Algar - Acompanhamento Receptivo - 0800</h5>
                                <div class="row dashCamPreechimento">
                                    <div class="col-md-6">
                                    <?php
                                    $options = array('' => '');		
                            		echo form_label('Selecione o m&ecirc;s:', 'periodoTelRecep0800');
                            		echo form_dropdown('periodoTelRecep0800', $options, '', 'id="periodoTelRecep0800" class="form-control"');
                                    ?>
                                    </div>
                                </div>
                                <b>Custo Total Fatura: </b><span id="custoTotalReceptivo0800"></span>
                                <div class="row" id="chart6"></div>
                            </div>
                          </div>
                    <?php } ?> 
                    <?php if(in_array(12, $dashboardPermitidos)){ ?>
                          <h3>Receptivo 4004</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#telefonia3').print()">Imprimir</a>
                            </div>
                            <div id="telefonia3">
                                <h5 class="ocultar tituloGrafico">Algar - Acompanhamento Receptivo - 4004</h5>
                                <div class="row dashCamPreechimento">
                                    <div class="col-md-6">
                                    <?php
                                    $options = array('' => '');		
                            		echo form_label('Selecione o m&ecirc;s:', 'periodoTelRecep4004');
                            		echo form_dropdown('periodoTelRecep4004', $options, '', 'id="periodoTelRecep4004" class="form-control"');
                                    ?>
                                    </div>
                                </div>
                                <b>Custo Total Fatura: </b><span id="custoTotalReceptivo4004"></span>
                                <div class="row" id="chart7"></div>
                            </div>
                          </div>
                    <?php } ?>                                    
                    </div>
                           <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>        
                </div>
            </div>

    <input type="hidden" id="cd_grafico" value="" />
    
<script type="text/javascript">

$("#dashboards h3").click(function(){
   
   if(!$(this).hasClass('ui-state-active')) {
   
       if($(this).text() == 'Ativo'){
            
            $(this).carregaCombo('ATIVO', 'periodoTel');
            $('#cd_grafico').val(8); 
            $(this).verificaClick(); 
            $(this).graficoAtivos('N','N'); 

       }
       
       if($(this).text() == 'Receptivo 0800'){
            
            $(this).carregaCombo('0800', 'periodoTelRecep0800');
            $('#cd_grafico').val(8); 
            $(this).verificaClick(); 
            $(this).graficoReceptivo('N','0800'); 
            
       }
       
       if($(this).text() == 'Receptivo 4004'){
            
            $(this).carregaCombo('4004', 'periodoTelRecep4004');
            $('#cd_grafico').val(8); 
            $(this).verificaClick(); 
            $(this).graficoReceptivo('N','4004'); 
            
       }
   
   }
    
});

$.fn.carregaCombo = function(grupo, input) {
    
    if($('#'+input+' option').size() == 1){
    
        $.post( "<?php echo base_url(); ?>ajax/mesesLigacao/", { tipo: grupo }, function( res ) {
            var content = '<option value="N"><?php echo date('m/Y'); ?></option>';
            
            $.each(res, function() {
              //alert(this.data_banco);
              content += '<option value="'+ this.data_banco +'">'+ this.data +'</option>';
              
            });
            
            $("#"+input).html('');
            $("#"+input).append(content);
        }, "json");
    
    }
    
}

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
    $(this).carregando();
    $(this).graficoReceptivo($(this).val(), '0800');
   }
    //window.location.href = "#telAcomp";
});

$("#periodoTelRecep4004").change(function(){
   if($(this).val() != ''){
    $(this).carregando();
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

$.fn.carregando = function() {
    $(document).ajaxStart(
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
        })
    ); 
    
    //setTimeout($.unblockUI, tempo);   
    //$(document).ajaxStart($.blockUI);
    
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
    
    /*################### GR�FICO 4 - IN�CIO #######################*/
    // CALL CENTER - ATIVO - ACOMPANHAMENO
    $.fn.graficoAtivos = function(periodo, zoom) {
        
        $(this).carregando();
        
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
            onrendered: function () { /*alert('Finalizando')*/ $(document).ajaxStop($.unblockUI); },
            data: {
                /*url: '<?php echo base_url(); ?>ajax/telefoniagraficoAtivos/'+per+'/'+zoo,*/
                url: '<?php echo base_url(); ?>ajax/dashboardTelefoniaAtivo/'+per,
                mimeType: 'json',
                type: 'bar',
                x : 'data',
                columns: ['data', /*'Qtd.', 'Minutos',*/ 'Holding - Custo', 'Holding - PosDia', 'CallCenter - PosDia'],
                groups: [
                    ['Holding - PosDia', 'CallCenter - PosDia']
                ],
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
                enabled: true
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
                        if(id === 'Holding - Custo' || id === 'CallCenter - Custo' || id === 'Holding - PosDia' || id === 'CallCenter - PosDia'){
                            return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        }else{
                            return value;
                        }
                    }
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
        
        //$(document).ajaxStop($.unblockUI);
        
    };
    /*################### GR�FICO 4 - FIM #######################*/ 

    $.fn.graficoReceptivo = function(periodo, fonte) {
    
        $(this).carregando();
    
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
            
        var chart2 = c3.generate({
            bindto: divReceptivo,
            //oninit: function () { alert('Iniciando') },
            onrendered: function () { /*alert('Finalizando')*/ $(document).ajaxStop($.unblockUI); },
            data: {
                url: '<?php echo base_url(); ?>ajax/dashboardTelefoniaReceptivo/'+per+'/'+fonte,
                mimeType: 'json',
                type: 'bar',
                x : 'data',
                columns: ['data', /*'Qtd.', 'Minutos',*/ 'Celular - PosDia', 'Fixo Local - PosDia', 'Fixo LDN - PosDia'],
                groups: [
                    ['Celular - PosDia', 'Fixo Local - PosDia', 'Fixo LDN - PosDia']
                ],
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
                enabled: true
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
                        if(id === 'Celular - Custo' || id === 'Celular - PosDia' || id === 'Fixo Local - Custo' || id === 'Fixo Local - PosDia' || id === 'Fixo LDN - Custo' || id === 'Fixo LDN - PosDia'){
                            return 'R$ '+number_format(value.toFixed(2),2,',','.');
                        }else{
                            return value;
                        }
                    }
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