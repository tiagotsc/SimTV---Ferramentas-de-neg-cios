<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Ficha plano</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open('telefonia/salvaPlano',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha Plano<a href='".base_url('telefonia/planos')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-6">';
                                    echo form_label('Nome<span class="obrigatorio">*</span>', 'nome');
                        			$data = array('name'=>'nome', 'value'=>$nome,'id'=>'nome', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($operadoras as $ope){
                                        $options[$ope->cd_telefonia_operadora] = $ope->nome;
                                    }	
                            		echo form_label('Operadora<span class="obrigatorio">*</span>', 'cd_telefonia_operadora');
                            		echo form_dropdown('cd_telefonia_operadora', $options, $cd_telefonia_operadora, 'id="cd_telefonia_operadora" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('SMS (Qtd)', 'sms');
                        			$data = array('name'=>'sms', 'value'=>$sms,'id'=>'sms', 'placeholder'=>'Digite a qtd.', 'class'=>'form-control qtd');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Voz (Minutos)', 'voz');
                        			$data = array('name'=>'voz', 'value'=>$voz,'id'=>'voz', 'placeholder'=>'Digite a qtd.', 'class'=>'form-control qtd');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Dados (Qtd)', 'dados');
                        			$data = array('name'=>'dados', 'value'=>$dados,'id'=>'dados', 'placeholder'=>'Digite a qtd', 'class'=>'form-control qtd');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('GB' => 'Giga', 'MB' => 'Mega');		
                            		echo form_label('Tipo dados<span class="obrigatorio">*</span>', 'tipo_dados');
                            		echo form_dropdown('tipo_dados', $options, $tipo_dados, 'id="tipo_dados" class="form-control"');
                                    echo '</div>';
                                    
                                echo '</div>';
                                
                                echo '<div class="row">';
                                echo '<div class="col-md-12"><a id="add-tarifas" href="#">Adicionar tarifa</a></div>';
                                echo '</div>';
                                echo '<div id="tarifas">';
                                if($tarifas){
                                    $cont = 0;
                                    foreach($tarifas as $ta){
                                        echo '<div class="row">';
                                        echo '<div class="col-md-3">';
                                        echo form_label('Nome', 'nome_tarifa['.$cont.']');
                            			$data = array('name'=>'nome_tarifa['.$cont.']', 'value'=>$ta->nome,'id'=>'nome_tarifa['.$cont.']', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                            			echo form_input($data);
                                        echo '</div>';
                                        echo '<div class="col-md-2">';
                                        echo form_label('Valor Tarifa', 'valor_tarifa['.$cont.']');
                            			$data = array('name'=>'valor_tarifa['.$cont.']', 'value'=>$ta->valor,'id'=>'valor_tarifa['.$cont.']', 'placeholder'=>'Digite o valor', 'class'=>'form-control valor');
                            			echo form_input($data);
                                        echo '</div>';
                                        echo '<div class="col-md-2">';
                                        echo form_label('In&iacute;cio Tarifa', 'inicio_tarifa['.$cont.']');
                            			$data = array('name'=>'inicio_tarifa['.$cont.']', 'value'=>$ta->data_inicio,'id'=>'inicio_tarifa['.$cont.']', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                            			echo form_input($data);
                                        echo '</div>';
                                        echo '<div class="col-md-2">';
                                        echo form_label('Fim Tarifa', 'fim_tarifa['.$cont.']');
                            			$data = array('name'=>'fim_tarifa['.$cont.']', 'value'=>$ta->data_fim,'id'=>'fim_tarifa['.$cont.']', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                            			echo form_input($data);
                                        echo '</div>';
                                        echo '<div class="col-md-2 paddingTopMedio">';
                                        echo '<a onclick="confirmacao(this)" href="#" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>';
                                        echo '</div>';
                                        echo '</div>';
                                        
                                        $cont++;
                                    }
                                }
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('cd_telefonia_plano', $cd_telefonia_plano);
                                
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

function confirmacao(elemento) {
    var r = confirm("<?php echo utf8_encode('Deseja remover essa tarifa?\nCaso sim, click no botão \'Salvar\' para confirmar a alteração.');?>");
    if (r == true) {
        $(elemento).parent().parent().remove();
    } 
}

function marcaTodos(){
    
    if($('#todos').prop('checked') == true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
}

$("#add-tarifas").click(function(){
    
    var cont = $('[name*="nome_tarifa"]').length;
    
   var inputTarifas = '<div class="row">';
   inputTarifas += '<div class="col-md-3">';
   inputTarifas += '<label>Nome Tarifa';
   inputTarifas += '<input type="text" id="nome_tarifa['+cont+']" title="Digite o nome" name="nome_tarifa['+cont+']" placeholder="Digite o nome" class="form-control dinamic required" />';
   inputTarifas += '</label>';
   inputTarifas += '</div>';
   inputTarifas += '<div class="col-md-2">';
   inputTarifas += '<label>Valor Tarifa';
   inputTarifas += '<input type="text" onkeypress="mask()" id="valor_tarifa['+cont+']" title="Digite a tarifa" name="valor_tarifa['+cont+']" placeholder="Digite o valor" class="form-control valor dinamic required" />';
   inputTarifas += '</label>';
   inputTarifas += '</div>';
   inputTarifas += '<div class="col-md-2">';
   inputTarifas += '<label>In&iacute;cio Tarifa';
   inputTarifas += '<input type="text" onkeypress="mask()" id="inicio_tarifa['+cont+']" title="Digite a data" name="inicio_tarifa['+cont+']" placeholder="Digite a data" class="form-control data dinamic required" />';
   inputTarifas += '</label>';
   inputTarifas += '</div>';
   inputTarifas += '<div class="col-md-2">';
   inputTarifas += '<label>Fim Tarifa';
   inputTarifas += '<input type="text" onFocus="mask()" id="fim_tarifa['+cont+']" title="Digite a data" name="fim_tarifa['+cont+']" placeholder="Digite a data" class="form-control data dinamic required" />';
   inputTarifas += '</label>';
   inputTarifas += '</div>';
   inputTarifas += '<div class="col-md-2">';
   inputTarifas += '<a onclick="$(this).parent().parent().remove();" href="#" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>';
   inputTarifas += '</div>';
   inputTarifas += '</div>';
        
   $("#tarifas").append(inputTarifas);
   
   $(".dinamic").css('font-weight', 'normal');
   
   $("#frm-salvar").validate();

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

   /*$( "#nome_tarifa["+cont+"]" ).rules( "add", {
      required: true,
      messages: {
        required: "O campo Logradouro &eacute; indispens&aacute;vel"
      }
    });*/
   
});

function marcaGrupo(classe, campo){
    
    if(campo.checked == true){
        $(classe).prop('checked', true);
    }else{
        $(classe).prop('checked', false);
    }

}

function mask(){
    $(".valor").mask("0.00##"/*, {reverse: true}*/);
    $(".data").mask("00/00/0000");

}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    $(".valor").mask("0.00##"/*, {reverse: true}*/);
    $(".qtd").mask("###0");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
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

$(document).ready(function(){
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
			nome: {
                required: true,
                minlength: 2
            },
            cd_telefonia_operadora: {
                required: true
            }
		},
		messages: {
			nome: {
                required: "Digite o nome.",
                minlength: "Digite o nome completo"
            },
            cd_telefonia_operadora: {
                required: 'Selecione a operadora'
            }
	   }
   });   
   
});
</script>