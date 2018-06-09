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

/*################### RENTABILIZAÇÃO - INÍCIO #######################*/
/* Texto */
.c3-ygrid-line.grid4 text{
    stroke: #696969;
}

/* Linha */
.c3-ygrid-line.grid4 line{
    stroke: green;
}
/*################### RENTABILIZAÇÃO - FIM #######################*/
</style>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Dashboard / Cobran&ccedil;a e faturamento</li>
                </ol>
                <div id="divMain">
                    <h3>Cobran&ccedil;a e faturamento</h3>
                    <div id="dashboards">
                    <?php if(in_array(2, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(2), $(this).verificaClick();">Arquivo retorno</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#arquivoRetorno').print()">Imprimir</a>
                            </div>
                            <div class="row dashCamPreechimento">
                                <div class="col-md-6">
                                <?php
                                $options = array('' => '');		
                        		foreach($anoDashboard as $ano){
                        			$options[$ano] = $ano;
                        		}
                        		
                        		echo form_label('Selecione o ano:', 'ano_dashboard');
                        		echo form_dropdown('ano_dashboard', $options, '', 'id="ano_dashboard" class="form-control"');
                                ?>
                                </div>
                                <div class="ocultar col-md-6">
                                <?php
                                $options = array('' => '');	
                                
                                if(isset($banco)){	
                            		foreach($banco as $ba){
                            			$options[$ba->cd_banco] = html_entity_decode($ba->nome_banco);
                            		}
                                }
                        		
                        		echo form_label('Filtre pelo banco:', 'cd_banco');
                        		echo form_dropdown('cd_banco', $options, '', 'id="cd_banco" class="form-control"');
                                ?>
                                </div>
                            </div>
                            <div id="arquivoRetorno">
                                <div class="row">
                                    <h5 class="ocultar tituloGrafico">Quantidade de t&iacute;tulos baixados e rejeitados
                                        <a class="descritivo_manual" target="_blank" href="<?php echo base_url('manual/manual_grafico_qtd_baixados_rejeitados.pdf');?>">Descritivo desse gr&aacute;fico</a>
                                    </h5>
                                    <p class="ocultar">Descri&ccedil;&atilde;o: Quantidade de t&iacute;tulos que ser&atilde;o baixados e rejeitadas pelo siga.</p>
                                    <div class="divDashboard" id="chart1"></div>
                                </div>
                                <div class="row">
                                    <h5 class="ocultar tituloGrafico" class="ocultar">Valor total dos t&iacute;tulos baixados e rejeitados
                                        <a class="descritivo_manual" target="_blank" href="<?php echo base_url('manual/manual_grafico_valor_baixados_rejeitados.pdf');?>">Descritivo desse gr&aacute;fico</a>
                                    </h5>
                                    <p class="ocultar">Descri&ccedil;&atilde;o: Valor total dos t&iacute;tulos que ser&atilde;o baixados e rejeitados pelo siga, sendo que o valor dos rejeitados s&atilde;o t&iacute;tulo de t&iacute;tulos que foram pagos e que foram rejeitados pelo banco.</p>
                                    <div class="ocultar" id="chart2"></div>
                                </div>
                            </div>
                          </div>
                    <?php } ?>
                    <?php if(in_array(4, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(4), $(this).verificaClick(); $(this).graficoBarraTituloPago();">T&iacute;tulos pagos</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#div_titulo_pago').print()">Imprimir</a>
                            </div>
                            <div id="div_titulo_pago">
                                <div id="chart3"></div>
                            </div>
                          </div>
                    <?php } ?>
                    <?php if(in_array(6, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(6), $(this).verificaClick(); $(this).comboStatusCobranca('O');">Status do Boleto</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#div_status_titulo').print();">Imprimir</a>
                            </div>                          
                            <div class="row dashCamPreechimento">
                                <div id="div_status_boleto" class="col-md-6"></div>
                            </div>
                            <div class="div_status_cobranca">
                                <h5 class="ocultar tituloGrafico">Informa os valores para todos os ciclos</h5>
                                <div class="ocultar" id="tipoCobBoleto"></div>
                                <div class="row dashCamPreechimento ocultar">
                                    <div id="tipoCicloCobBoleto" class="col-md-6">
                                    </div>
                                </div>
                                <h5 class="ocultar tituloGrafico">Informa a quantidade por banco de determinado ciclo</h5>
                                <div>
                                    <div class="ocultar" id="grafCicloCobBoleto"></div>
                                </div>
                            </div>                           
                          </div>
                    <?php } ?>
                    <?php if(in_array(9, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(9), $(this).verificaClick(); $(this).comboStatusCobranca('B');">Status do DCC</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#div_status_titulo').print();">Imprimir</a>
                            </div>                          
                            <div class="row dashCamPreechimento">
                                <div id="div_status_dcc" class="col-md-6"></div>
                            </div>
                            <div class="div_status_cobranca">
                                <h5 class="ocultar tituloGrafico">Informa os valores para todos os ciclos</h5>
                                <div class="ocultar" id="tipoCobDcc"></div>
                                <div class="row dashCamPreechimento ocultar">
                                    <div id="tipoCicloCobDcc" class="col-md-6">
                                    </div>
                                </div>
                                <h5 class="ocultar tituloGrafico">Informa a quantidade por banco de determinado ciclo</h5>
                                <div>
                                    <div class="ocultar" id="grafCicloCobDcc"></div>
                                </div>
                            </div>                           
                          </div>
                    <?php } ?>
                    <?php if(in_array(10, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(10), $(this).verificaClick(); $(this).comboStatusCobranca('T');">Status do Cart&atilde;o</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#div_status_titulo').print();">Imprimir</a>
                            </div>                          
                            <div class="row dashCamPreechimento">
                                <div id="div_status_cartao" class="col-md-6"></div>
                            </div>
                            <div class="div_status_cobranca">
                                <h5 class="ocultar tituloGrafico">Informa os valores para todos os ciclos</h5>
                                <div class="ocultar" id="tipoCobCartao"></div>
                                <div class="row dashCamPreechimento ocultar">
                                    <div id="tipoCicloCobCartao" class="col-md-6">
                                    </div>
                                </div>
                                <h5 class="ocultar tituloGrafico">Informa a quantidade por banco de determinado ciclo</h5>
                                <div>
                                    <div class="ocultar" id="grafCicloCobCartao"></div>
                                </div>
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

/* Se aba do dashboard for aberta grava log de acesso */
$.fn.verificaClick = function() {
    if(!$(this).hasClass('ui-state-active')) {
        $.post( "<?php echo base_url(); ?>dashboard/registraAcesso/", { cd_grafico: $("#cd_grafico").val()} );
    }
};

$(document).ready(function(){
    
    $(function() {
        $( "#dashboards" ).accordion({
            //activate: function( event, ui ) {},
            //beforeActivate: function( event, ui ) {alert(2)},
            collapsible: true, // Habilita a opção de expandir e ocultar ao clicar
            heightStyle: "content",
            active: false
        });
    });
    
    $(".ocultar").css('display', 'none');
    
    $("#ano_dashboard").change(function(){         
        
        $("#cd_banco").val('');
        
        if($(this).val() != ""){
            
            $(".ocultar").css('display', 'block');
            
            $(this).graficoLinha();
            $(this).graficoBarraColada();
            $(this).carregando(5000);  
                                              
        }else{
            $(".ocultar").css('display', 'none');
        }
        
    });
    
    $("#cd_banco").change(function(){
    
        if($(this).val() != ""){
            
            $(this).graficoLinha();
            $(this).graficoBarraColada();
            $(this).carregando(6000); 
            
        }else{
                    
            $(this).graficoLinha();
            $(this).graficoBarraColada();
            $(this).carregando(6000);
            
        }
    
    });
    
});

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

/*################### ARQUIVO RETORNO - INÍCIO #######################*/

$.fn.graficoLinha = function() {
    
    // GRÁFICO DE LINHA                        
    c3.generate({
        bindto: '#chart1',
        data: {
            //url: '<?php echo base_url('assets/c3_test.json')?>',
            url: '<?php echo base_url(); ?>ajax/graficoLinhaRetornoAno/'+$("#ano_dashboard").val()+'/'+$("#cd_banco").val(),
            mimeType: 'json',
            //onclick: function (d, element) { alert(d.value); },
            keys: {
                x: 'meses', // it's possible to specify 'x' when category axis
                value: ['Quantidade baixado', 'Quantidade rejeitado']
            }
        },
    	axis: {
    		x: {
    			type: 'category'
    		},
             y : {
            tick: {
                    format: d3.format(',')
                }
            }            
    	},
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = id === 'Quantidade baixado' ? d3.format(',') : d3.format(',');
                    return format(value).replace(',', '.').replace(',', '.');
                }
    //            value: d3.format(',') // apply this format to both y and y2
            }
        }
    });
    
};

$.fn.graficoBarraColada = function() {
    
    // GRÁFICO BARRA COLADA            
    chart2 = c3.generate({
        bindto: '#chart2',
        data: {
            //url: '<?php echo base_url('assets/c3_test.json')?>',
            url: '<?php echo base_url(); ?>ajax/graficoBarraColadaRetornoAno/'+$("#ano_dashboard").val()+'/'+$("#cd_banco").val(),
            mimeType: 'json',
            type: 'bar',
            x : 'meses',
            columns: ['meses','Valor baixado', 'Valor rejeitado'],
            groups: [
                ['Valor baixado', 'Valor rejeitado']
            ]
        },
        axis: {
            x: {
                type: 'category' // this needed to load string x value
            },
             y : {
            tick: {
                    format: d3.format(",")
                }
            }
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = id === 'Valor baixado' ? d3.format(',') : d3.format(',');
                    return format(value).replace(',', '.').replace(',', '.');
                }
    //            value: d3.format(',') // apply this format to both y and y2
            }
        }
    });    
    
};

/*################### ARQUIVO RETORNO - FIM #######################*/

/*################### TÍTULO PAGO - INÍCIO #######################*/

$.fn.graficoBarraTituloPago = function() {
    
    // GRÁFICO BARRA COLADA            
    chart2 = c3.generate({
        bindto: '#chart3',
        data: {
            //url: '<?php echo base_url('assets/c3_test.json')?>',
            url: '<?php echo base_url(); ?>ajax/titulosPagos/',
            mimeType: 'json',
            type: 'bar',
            x : 'meses',
            columns: ['meses','Valor total', 'Valor pago', 'Valor pago outros']
        },
        axis: {
            x: {
                type: 'category' // this needed to load string x value
            },
             y : {
            tick: {
                    format: d3.format(",")
                }
            }
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = id === 'Valor baixado' ? d3.format(',') : d3.format(',');
                    return format(value).replace(',', '.').replace(',', '.');
                }
    //            value: d3.format(',') // apply this format to both y and y2
            }
        }
    });    
    
};

/*################### TÍTULO PAGO - FIM #######################*/

/*################### STATUS DA COBRANÇA - INÍCIO #######################*/

$.fn.changeStatusCobranca = function(campo ,tipo) {
  
        if(tipo == 'B'){
            var nomeCampo = 'dcc';
            var divTipoCicloCobranca = '#tipoCicloCobDcc';
            var grafCicloCob = '#grafCicloCobDcc';
        }
        if(tipo == 'O'){
            var nomeCampo = 'boleto';
            var divTipoCicloCobranca = '#tipoCicloCobBoleto';
            var grafCicloCob = '#grafCicloCobBoleto';
        }
        if(tipo == 'T'){
            var nomeCampo = 'cartao';
            var divTipoCicloCobranca = '#tipoCicloCobCartao';
            var grafCicloCob = '#grafCicloCobCartao';
        }
        
        if($(campo).val() != ""){
            
            $(".ocultar").css('display', 'block');
            
            $(this).graficoStatusCobranca($(campo).val(), tipo);
            //$('#chart6').css('height', '400px');
            $(grafCicloCob).html('');
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/comboCicloStatusCobranca/'+$(campo).val()+'/'+tipo,
              dataType: "json",
              error: function(res) {
 	            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
                alert('erro');
              },
              success: function(res) {
                
               var select = '<label for="ciclo_status_cob_'+nomeCampo+'">Selecione o ciclo:</label>';
                   select += '<select name="ciclo_status_cob_'+nomeCampo+'" id="ciclo_status_cob_'+nomeCampo+'" class="form-control">';
                   select += '<option value="" selected="selected"></option>';
                if(res.length > 0){
                
                    $.each(res, function() {
                      
                      select += '<option value="'+this.CICLO+'">'+this.CICLO+'</option>';
                      
                    });
                    
                    select += '</select>';
                    
                    $(divTipoCicloCobranca).html(select);
                
                }else{
                    
                    $(divTipoCicloCobranca).html('');
                    
                }
                
                $("#ciclo_status_cob_"+nomeCampo).change(function(){ 
        
                    if($("#ciclo_status_cob_"+nomeCampo).val() != ""){
                        $(this).graficoStatusCicloCobranca($(campo).val(), $("#ciclo_status_cob_"+nomeCampo).val(), tipo, grafCicloCob);
                        //$('#chart7').css('height', '420px');
                    }else{
                        $(grafCicloCob).html('');
                    }
                    
                });
                
              }
              
            });
                                        
        }else{
            
            $(".ocultar").css('display', 'none');
        }
        
    }

$.fn.comboStatusCobranca = function(tipo) {
    
    if(tipo == 'B'){
        var campo = 'dcc';
    }
    if(tipo == 'O'){
        var campo = 'boleto';
    }
    if(tipo == 'T'){
        var campo = 'cartao';
    }
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>ajax/comboStatusCobranca/'+tipo,
      dataType: "json",
      error: function(res) {
        //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
        alert('erro');
      },                                
      success: function(res) {            
        
       var select = '<label for="status_'+campo+'">Selecione o m&ecirc;s/ano:</label>';
           select += '<select name="status_'+campo+'" id="status_'+campo+'" onchange="$(this).changeStatusCobranca(this,\''+tipo+'\')" class="form-control">';
           select += '<option value="" selected="selected"></option>';
        if(res.length > 0){
        
            $.each(res, function() {
              
              select += '<option value="'+this.VALOR+'">'+this.EXIBICAO+'</option>';
              
            });
                     
            select += '</select>';
            
            $("#div_status_"+campo).html(select);
              
        }else{
            
            $("#div_status_"+campo).html('');
            
        }
      }
      
    });
    
}

$.fn.graficoStatusCobranca = function(valor, tipo) { 
    
    if(tipo == 'B'){
        var divTipoCobranca = '#tipoCobDcc';
    }
    if(tipo == 'O'){
        var divTipoCobranca = '#tipoCobBoleto';
    }
    if(tipo == 'T'){
        var divTipoCobranca = '#tipoCobCartao';
    }
    
    c3.generate({
        bindto: divTipoCobranca,
        data: {
            //url: '<?php echo base_url('assets/c3_test.json')?>',
            url: '<?php echo base_url(); ?>ajax/statusCobranca/'+valor+'/'+tipo,
            mimeType: 'json',
            type: 'bar',
            x : 'data',
            columns: ['data'/*,'Pago', 'Pago Antes', 'Enviado', 'Rejeitado', 'Não retornado', 'Não gerado'*/]//,
            //groups: [
                //['Pago', 'Pago Antes', 'Enviado', 'Rejeitado', 'Não retornado'/*, 'Não gerado'*/]
            //]
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
        zoom: { // Zoom Scroll
            enabled: false
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = id === 'Pago' ? d3.format(',') : d3.format(',');
                    //return 'R$ '+ format(value).replace(',', '.').replace(',', '.');
                    return 'R$ '+number_format(value.toFixed(2),2,',','.');
                }
    //            value: d3.format(',') // apply this format to both y and y2
            }
        }
    });    
    
};

$.fn.graficoStatusCicloCobranca = function(mesAno, ciclo, tipo, div) {

    c3.generate({
        bindto: div,
        data: {
            //url: '<?php echo base_url('assets/c3_test.json')?>',
            url: '<?php echo base_url(); ?>ajax/statusCicloCobranca/'+mesAno+'/'+ciclo+'/'+tipo,
            mimeType: 'json',
            type: 'bar',
            x : 'data',
            columns: ['data'/*,'Pago', 'Pago Antes', 'Registrado', 'Rejeitado', 'Não retornado', 'Não gerado'*/]/*,
            groups: [
                ['Pago', 'Pago Antes', 'Registrado', 'Rejeitado', 'Não retornado', 'Não gerado']
            ]*/
        },
        zoom: { // Zoom Scroll
            enabled: false
        },
        axis: {
            x: {
                type: 'category' // this needed to load string x value
            },
             y : {
            tick: {
                    //format: d3.format(",")
                    format: function (d) { return 'R$ ' + number_format(d.toFixed(2),2,',','.'); }
                }
            }
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    var format = id === 'Pago' ? d3.format(',') : d3.format(',');
                    //return 'R$ '+ format(value).replace(',', '.').replace(',', '.');
                    return 'R$ '+number_format(value.toFixed(2),2,',','.');
                }
    //            value: d3.format(',') // apply this format to both y and y2
            }
        }
    });    
};

/*################### STATUS DA COBRANÇA - FIM #######################*/

</script>