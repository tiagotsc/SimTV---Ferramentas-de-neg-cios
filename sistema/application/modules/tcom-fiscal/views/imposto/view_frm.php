<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.maskMoney.min.js") ?>"></script>
<?php
echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Ficha <?php echo $assunto; ?></li>
                </ol>
                <div id="divMain">
                    <?php   

                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha ".$assunto."<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                
                                    /*echo '<div class="col-md-3">';
                                    echo form_label('Nome<span class="obrigatorio">*</span>', 'nome');
                        			$data = array('name'=>'nome', 'value'=>$nome,'id'=>'nome', 'placeholder'=>'Informe o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';*/
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '',
                                                    'ICMS' => 'ICMS',
                                                    'COFINS' => 'COFINS',
                                                    'PIS' => 'PIS',
                                                    'FUST' => 'FUST',
                                                    'FUNTTEL' => 'FUNTTEL',
                                                    'ISS' => 'ISS',
                                                    'COFINS N CUM.' => 'COFINS N CUM.',
                                                    'PIS N CUM.' => 'PIS N CUM.',
                                                    'IR' => 'IR',
                                                    'CSLL' => 'CSLL');	
                            		echo form_label('Nome<span class="obrigatorio">*</span>', 'nome');
                            		echo form_dropdown('nome', $options, $nome, 'id="nome" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($estados as $est){
                                        $options[$est->cd_estado] = htmlentities($est->nome_estado);
                                    }	
                            		echo form_label('UF Origem<span class="obrigatorio">*</span> <span class="glyphicon glyphicon-info-sign" title="UF de origem do contrato"></span>', 'cd_estado_origem');
                            		echo form_dropdown('cd_estado_origem', $options, $cd_estado_origem, 'id="cd_estado_origem" class="form-control"');
                                    echo '</div>';   
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($estados as $est){
                                        $options[$est->cd_estado] = htmlentities($est->nome_estado);
                                    }	
                            		echo form_label('UF Destino<span class="obrigatorio">*</span> <span class="glyphicon glyphicon-info-sign" title="UF de faturado por"></span>', 'cd_estado');
                            		echo form_dropdown('cd_estado', $options, $cd_estado, 'id="cd_estado" class="form-control"');
                                    echo '</div>';   
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($servicos as $ser){
                                        $options[$ser->id] = htmlentities($ser->nome);
                                    }	
                            		echo form_label('Servi&ccedil;o<span class="obrigatorio">*</span>', 'idServico');
                            		echo form_dropdown('idServico', $options, $idServico, 'id="idServico" class="form-control"');
                                    echo '</div>';  
                                    
                              echo '</div>';
                              echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Efetiva(%)<span class="obrigatorio">*</span>', 'efetiva');
                        			$data = array('name'=>'efetiva', 'value'=>$efetiva,'id'=>'efetiva', 'placeholder'=>'Informe a porcetagem', 'maxlength' => '7', 'class'=>'porcentagem form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Redu&ccedil;&atilde;o(%)<span class="obrigatorio">*</span>', 'reducao');
                        			$data = array('name'=>'reducao', 'value'=>$reducao,'id'=>'reducao', 'placeholder'=>'Informe a porcetagem', 'maxlength' => '7', 'class'=>'porcentagem form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Base de calculo(%)', 'base_calculo');
                        			$data = array('name'=>'base_calculo', 'value'=>$base_calculo,'id'=>'base_calculo', 'placeholder'=>'Informe a base de calculo', 'maxlength' => '7', 'class'=>'porcentagem form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Data in&iacute;cio<span class="obrigatorio">*</span>', 'data_inicio');
                        			$data = array('name'=>'data_inicio', 'value'=>$this->util->formataData($data_inicio,'br'),'id'=>'data_inicio', 'placeholder'=>'Informe a data in&iacute;cio', 'class'=>'data form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Data fim<span class="obrigatorio">*</span>', 'data_fim');
                        			$data = array('name'=>'data_fim', 'value'=>$this->util->formataData($data_fim),'id'=>'data_fim', 'placeholder'=>'Informe a data fim', 'class'=>'data form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';   
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                
                                echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$(function() {
    $( document ).tooltip({
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
});

$(".porcentagem").maskMoney({suffix:'%', allowNegative: true, thousands:'.', decimal:'.', affixesStay: true});

$(document).ready(function(){
    
    $("#infoHab").hide();
    
    // Valida o formul�rio
	$("#frm-salvar").validate({
            debug: false,
            rules: {
                nome: {
                    required: true
                },
                cd_estado_origem: {
                    required: true
                },
                cd_estado: {
                    required: true
                },
                efetiva: {
                    required: true
                },
                /*base_calculo: {
                    required: true
                },*/
                data_inicio: {
                    required: true
                },
                data_fim: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: "Informe o nome."
                },
                cd_estado_origem: {
                    required: "Selecione a UF origem."
                },
                cd_estado: {
                    required: "Selecione a UF destino."
                },
                efetiva: {
                    required: "Informe a porcetagem."
                },
                /*base_calculo: {
                    required: "Informe a base de c&aacute;lculo."
                },*/
                data_inicio: {
                    required: "Informe a data in&iacute;cio."
                },
                data_fim: {
                    required: "Informe a data fim."
                }
            }
        });
    
});

$("#final").change(function(){
   
   if($(this).val() != ''){
    if($(this).val() == 'S'){
        $("#infoHab").show();
    }else{
        $("#infoHab").hide();
    }
   }else{
    $("#infoHab").hide();
   }
    
});

$(function() {
    $( document ).tooltip({
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
});

$(".data").datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Pr&oacute;ximo',
	prevText: 'Anterior',
    
    // Traz o calendário input datepicker para frente da modal
    beforeShow :  function ()  { 
        setTimeout ( function (){ 
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
        },  0 ); 
    } 
});

</script>