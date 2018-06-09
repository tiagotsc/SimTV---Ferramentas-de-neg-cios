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

.cabecalhoDashboard{
    text-align: center;
}
</style>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Dashboard / Diretoria</li>
                </ol>
                <div id="divMain">
                    <h3>Diretoria</h3>
                    <div id="dashboards">
                    <?php if(in_array(7, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(7), $(this).verificaClick(); $(this).graficoBarraPendenciaInstalacaoDezDias();">Pend&ecirc;ncias de instala&ccedil;&atilde;o</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#pendInstalacao').print()">Imprimir</a>
                            </div>
                            <div class="row dashCamPreechimento">
                                <div class="col-md-6">
                                <?php
                                $options = array('' => 'TODOS');		
                        		foreach($permissor as $per){
                        			$options[$per->permissor] = $per->nome;
                        		}
                        		
                        		echo form_label('Selecione o permissor:', 'permPendInst');
                        		echo form_dropdown('permPendInst', $options, '', 'id="permPendInst" class="form-control"');
                                ?>
                                </div>
                                <div class="ocultar col-md-6">
                                <?php
                                $options = array('' => 'TODOS', 'CM' => 'Banda Larga','COMBO' => 'COMBO', 'PTV' => 'TV');		
                        		/*foreach($servico as $ser){
                        			$options[$ser->SIGLA] = $ser->NOME;
                        		}*/
                        		
                        		echo form_label('Selecione o servi&ccedil;o:', 'servPendInst');
                        		echo form_dropdown('servPendInst', $options, '', 'id="servPendInst" class="form-control"');
                                ?>
                                </div>
                            </div>
                            <div id="pendInstalacao">
                                <h5 class="ocultar tituloGrafico">Comparativo dos &uacute;ltimos 10 dias</h5>
                                <!--<p class="ocultar">Descri&ccedil;&atilde;o: Quantidade de t&iacute;tulos que ser&atilde;o baixados e rejeitadas pelo siga.</p>-->
                                <div id="chart1"></div>
                                <h5 class="ocultar tituloGrafico">Comparativo m&ecirc;s a m&ecirc;s</h5>
                                <div id="chart2"></div>
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
            collapsible: true, // Habilita a opção de expandir e ocultar ao clicar
            heightStyle: "content",
            active: false
        });
    });
    
    //$(".data").mask("00/00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    }); 
    
    $("#permPendInst").change(function() {
        var serv = '';
        if($("#servPendInst").val() != ''){
            serv = $("#servPendInst").val();
        }
        //$(this).carregando(700); 
        if($(this).val() != ''){ 
            $(this).graficoBarraPendenciaInstalacaoDezDias($(this).val(), serv);
        }else{
            $(this).graficoBarraPendenciaInstalacaoDezDias('0', serv);
        }
    });   
    
    $("#servPendInst").change(function() {
        var per = ''; 
        if($("#permPendInst").val() != ''){
            per = $("#permPendInst").val();
        }
        //$(this).carregando(700); 
        if($(this).val() != ''){
            $(this).graficoBarraPendenciaInstalacaoDezDias(per, $(this).val());
        }else{
            $(this).graficoBarraPendenciaInstalacaoDezDias(per, '0');
        }
    }); 
    
    /*################### PENDÊNCIAS DE INSTALAÇÃO - INÍCIO #######################*/
    // COMPARATIVO DOS ÚLTIMOS 10 DIAS
    $.fn.graficoBarraPendenciaInstalacaoDezDias = function(permissor, servico) {
        
        var resPermissor = '0';
        var resServico = '0';
        
        if(permissor != '' && typeof permissor !== 'undefined'){
            resPermissor = permissor;
        }
        //if(typeof permissor !== 'undefined'){
        //alert(permissor);
        //}
        if(servico != '' && typeof servico !== 'undefined'){
            resServico = servico;
        }
        
        c3.generate({
            bindto: '#chart1',
            data: {
                //url: '<?php echo base_url('assets/c3_test.json')?>',
                url: '<?php echo base_url(); ?>ajax/pendenciaInstalacaoDezDias/'+resPermissor+'/'+resServico,
                mimeType: 'json',
                type: 'bar',
                x : 'diaMes',
                columns: ['diaMes','Qtd. Vendas', '<?php echo utf8_encode('Qtd. Instalação');?>', 'Qtd. Cancelamento', '<?php echo utf8_encode('Qtd. Pendência');?>']
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
            }/*,
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = id === 'Valor baixado' ? d3.format(',') : d3.format(',');
                        return format(value).replace(',', '.').replace(',', '.');
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            }*/
        });    
        $(this).graficoBarraPendenciaInstalacaoMesAmes(resPermissor, resServico);
    };
    
    // COMPARATIVO MÊS A MÊS
    $.fn.graficoBarraPendenciaInstalacaoMesAmes = function(permissor, servico) {
         
        c3.generate({
            bindto: '#chart2',
            data: {
                //url: '<?php echo base_url('assets/c3_test.json')?>',
                url: '<?php echo base_url(); ?>ajax/pendenciaInstalacaoMesAmes/'+permissor+'/'+servico,
                mimeType: 'json',
                type: 'bar',
                x : 'mesAno',
                columns: ['mesAno','Qtd. Vendas', '<?php echo utf8_encode('Qtd. Instalação');?>', 'Qtd. Cancelamento', '<?php echo utf8_encode('Qtd. Pendência');?>']
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
            }/*,
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = id === 'Valor baixado' ? d3.format(',') : d3.format(',');
                        return format(value).replace(',', '.').replace(',', '.');
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            }*/
        });    
        
    };


    /*################### PENDÊNCIAS DE INSTALAÇÃO - FIM #######################*/  
    
});


</script>