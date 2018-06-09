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
                    <li class="active">Dashboard / Telecom</li>
                </ol>
                <div id="divMain">
                    <h3>Telecom</h3>
                    <div id="dashboards">
                    <?php if(in_array($acessoGrafico, $dashboardPermitidos)){ ?>
                          <h3>Contratos pendentes</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#Telecom1').print()">Imprimir</a>
                            </div>
                            <div id="Telecom1">
                                <h5 class="ocultar tituloGrafico">Contratos pendentes</h5>
                                <div class="row dashCamPreechimento">
                                    <div class="col-md-6">
                                    <?php
                            		$options = array('todas' => 'Todos os permissores');
                                    foreach($unidadeContratos as $uniCont){
                                        $options[$uniCont->cd_unidade] = htmlentities($uniCont->nome);
                                                                            
                                    }                                    
                            		echo form_label('Selecione o permissor:', 'uniCont');
                            		echo form_dropdown('uniCont', $options, '', 'id="uniCont" class="form-control"');
                                    ?>
                                    </div>
                                </div>
                                <b>Total de contrato pendentes: </b><span id="totalContratos"></span>
                                <a name="telAcomp"></a>
                                <div class="row" id="chart4"></div>
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
   
       if($(this).text() == 'Contratos pendentes'){
            $('#cd_grafico').val(<?php echo $acessoGrafico;?>); 
            $(this).verificaClick(); 
            $(this).contratosPendentes('todas'); 

       }
   
   }
    
});


function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$("#uniCont").change(function(){
    $(this).contratosPendentes($(this).val());
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
    
    /*################### GRÁFICO 1 - INÍCIO #######################*/
    $.fn.contratosPendentes = function(unidade) {
        
        $.post( '<?php echo base_url(); ?>dashboard/dashTelecomAjax/qtdContHistPend/'+unidade, function( data ) {
          $( "#totalContratos" ).html( data.qtd );
        }, "json");
        
        var chart1 = c3.generate({
            bindto: '#chart4',
            data: {
                url: '<?php echo base_url(); ?>dashboard/dashTelecomAjax/historicoPendencias/'+unidade,
                mimeType: 'json',
                type: 'bar',
                //x : 'status',
                //columns: ['status']
            },
            legend: {
                show: true,
                position: 'right',
                hide:['status']
            }
        });
                          
        
    };
    /*################### GRÁFICO 1 - FIM #######################*/ 
    
});


</script>