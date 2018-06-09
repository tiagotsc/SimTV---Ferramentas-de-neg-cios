<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";

$optionServico = '';
foreach($todosServicos as $tS){
    $optionServico .= '<option value="'.$tS->cd_telefonia_servico.'">'.htmlentities($tS->nome).'</option>';
}

$optionsServicos = array('' => '');	
foreach($todosServicos as $tS){
    $optionsServicos[$tS->cd_telefonia_servico] = htmlentities($tS->nome);
}

?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Ficha linha</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open('telefonia/salvaLinha',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha Linha<a href='".base_url('telefonia/linhas')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Identifica&ccedil;&atilde;o', 'identificacao');
                        			$data = array('name'=>'identificacao', 'value'=>$identificacao,'id'=>'identificacao', 'placeholder'=>'Digite o ID', $readonly => true, 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                                    foreach($ddds as $ddd){
                                        $options[$ddd->cd_telefonia_ddd] = htmlentities($ddd->descricao);
                                    }
                            		echo form_label('DDD<span class="obrigatorio">*</span>', 'cd_telefonia_ddd');
                            		echo form_dropdown('cd_telefonia_ddd', $options, $cd_telefonia_ddd, $disabled.' id="cd_telefonia_ddd" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('N&uacute;mero<span class="obrigatorio">*</span>', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Digite o nome', $readonly => true, 'class'=>'form-control celular');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '');	
                                    foreach($operadoras as $ope){
                                        $options[$ope->cd_telefonia_operadora] = $ope->nome;
                                    }	
                            		echo form_label('Operadora<span class="obrigatorio">*</span>', 'cd_telefonia_operadora');
                            		echo form_dropdown('cd_telefonia_operadora', $options, $cd_telefonia_operadora, $disabled.' id="cd_telefonia_operadora" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($planos as $pla){
                                        $options[$pla->cd_telefonia_plano] = $pla->nome;
                                    }	
                            		echo form_label('Plano<span class="obrigatorio">*</span>', 'cd_telefonia_plano');
                            		echo form_dropdown('cd_telefonia_plano', $options, $cd_telefonia_plano, $disabled.' id="cd_telefonia_plano" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    
                                    if($cd_telefonia_linha){
                                        if($status == 'I' or $status == 'E'){
                                            $options = array('I' => 'Inativo', 'E' => 'Estoque');
                                        }else{
                                            $options = array('A' => 'Ativo');
                                        }
                                        #$options = array('I' => 'Inativo', 'A' => 'Ativo');	
                                    }else{
                                        $options = array('E' => 'Estoque');
                                    }
                                    	
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status');
                            		echo form_dropdown('status', $options, $status, $disabled.' id="status" class="form-control"');
                                    echo '</div>';
                                    
                                echo '</div>';
                    if(in_array(221, $this->session->userdata('permissoes')) and !$visualizar){
                    ?>       
                        <div class="row paddingTopMedio">
                            <div class="col-md-12"><b><a id="add-servico" href="#add-servico">Adicionar servi&ccedil;o</a></b></div>
                        </div>
                    <?php
                    }
                    ?>    
                        <div id="servicos-ass"></div>  
                        
                        <div class="row text-center">
                            <div><br /><strong>Servi&ccedil;os associados a linha</strong></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                            <?php 
                            
                            $contCombo = 0;
                            if($servicosAtivos){ 
                                $this->table->set_heading('Servi&ccedil;os ativos', 'Quantidade', 'Valor', 'In&iacute;cio', 'Fim', 'A&ccedil;&atilde;o');
                                foreach($servicosAtivos as $sA){
                                    
                            		$comboServico = form_dropdown('cd_servico['.$contCombo.']', $optionsServicos, $sA->cd_telefonia_servico, $disabled.' id="cd_servico['.$contCombo.']" class="form-control"');
                                    
                                    $data = array('name'=>'qtd_servico['.$contCombo.']', 'value'=>$sA->qtd,'id'=>'qtd_servico['.$contCombo.']', 'readonly' => true, 'class'=>'form-control required ');
                        			$qtdServico = form_input($data);
                                    
                                    $data = array('name'=>'valor_servico['.$contCombo.']', 'value'=>$sA->valor,'id'=>'valor_servico['.$contCombo.']', 'readonly' => true, 'class'=>'form-control required ');
                        			$valorServico = form_input($data);
                                    
                                    $data = array('name'=>'inicio_servico['.$contCombo.']', 'value'=>$this->util->formataData($sA->data_inicio,'BR'),'id'=>'inicio_servico['.$contCombo.']', 'title'=>'Digite a data', $readonly => true, 'class'=>'form-control required '.$classData);
                        			$inicioServico = form_input($data);
                                    
                                    $data = array('name'=>'fim_servico['.$contCombo.']', 'value'=>$this->util->formataData($sA->data_fim,'BR'),'id'=>'fim_servico['.$contCombo.']', 'title'=>'Digite a data', $readonly => true, 'class'=>'form-control required '.$classData);
                        			$fimServico = form_input($data);
                                    
                                    $cell1 = array('data' => $comboServico, 'style' => 'width:30%');
                                    $cell2 = array('data' => $qtdServico);
                                    $cell3 = array('data' => $valorServico);
                                    $cell4 = array('data' => $inicioServico, 'style' => 'width:15%');
                                    $cell5 = array('data' => $fimServico, 'style' => 'width:15%');
                                    
                                    $botaoExcluir = (in_array(221, $this->session->userdata('permissoes')) and !$visualizar)? '<a title="Apagar" href="#" onclick="confirmacao(this)" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                                    $cell6 = array('data' => $botaoExcluir);
                                    
                                    $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5, $cell6);
                                    
                                    $contCombo++;
                                    
                                }
                                $template = array('table_open' => '<table class="table zebra">');
                            	$this->table->set_template($template);
                            	echo $this->table->generate();
                            } 
                            ?>
                            </div>
                            <div class="col-md-12">
                            <?php 
                            if($servicosInativos){ 
                                $this->table->set_heading('Servi&ccedil;os inativos', 'Quantidade', 'Valor', 'In&iacute;cio', 'Fim', 'A&ccedil;&atilde;o');
                                foreach($servicosInativos as $sI){
                                    
                                    $comboServico = form_dropdown('cd_servico['.$contCombo.']', $optionsServicos, $sI->cd_telefonia_servico, $disabled.' id="cd_servico['.$contCombo.']" class="form-control"');
                                    
                                    $data = array('name'=>'qtd_servico['.$contCombo.']', 'value'=>$sI->qtd,'id'=>'qtd_servico['.$contCombo.']', 'readonly' => true, 'class'=>'form-control required ');
                        			$qtdServico = form_input($data);
                                    
                                    $data = array('name'=>'valor_servico['.$contCombo.']', 'value'=>$sI->valor,'id'=>'valor_servico['.$contCombo.']', 'readonly' => true, 'class'=>'form-control required ');
                        			$valorServico = form_input($data);
                                    
                                    $data = array('name'=>'inicio_servico['.$contCombo.']', 'value'=>$this->util->formataData($sI->data_inicio,'BR'),'id'=>'inicio_servico['.$contCombo.']', 'title'=>'Digite a data', $readonly => true, 'class'=>'form-control required '.$classData);
                        			$inicioServico = form_input($data);
                                    
                                    $data = array('name'=>'fim_servico['.$contCombo.']', 'value'=>$this->util->formataData($sI->data_fim,'BR'),'id'=>'fim_servico['.$contCombo.']', 'title'=>'Digite a data', $readonly => true, 'class'=>'form-control required '.$classData);
                        			$fimServico = form_input($data);
                                    
                                    $cell1 = array('data' => $comboServico, 'style' => 'width:30%');
                                    $cell2 = array('data' => $qtdServico);
                                    $cell3 = array('data' => $valorServico);
                                    $cell4 = array('data' => $inicioServico, 'style' => 'width:15%');
                                    $cell5 = array('data' => $fimServico, 'style' => 'width:15%');
                                    
                                    $botaoExcluir = (in_array(221, $this->session->userdata('permissoes')) and !$visualizar)? '<a title="Apagar" href="#" onclick="confirmacao(this)" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                                    $cell6 = array('data' => $botaoExcluir);
                                    
                                    $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5, $cell6);
                                    
                                    $contCombo++;
                                    
                                }
                                $template = array('table_open' => '<table class="table zebra">');
                            	$this->table->set_template($template);
                            	echo $this->table->generate();
                            } 
                            ?>
                            </div> 
                        </div> 
                        
                    <?php
                        
                    if(in_array(221, $this->session->userdata('permissoes')) and !$visualizar){    
                            echo '<div class="actions">';
                                
                            echo form_hidden('cd_telefonia_linha', $cd_telefonia_linha);
                                
                            echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                            echo '</div>';  
                        
                            echo form_fieldset_close();
                    	echo form_close();
                    } 
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
    var r = confirm("<?php echo utf8_encode('Deseja remover esse serviço?\nCaso sim, click no botão \'Salvar\' para confirmar a alteração.');?>");
    if (r == true) {
        $(elemento).parent().parent().remove();
    } 
}

function servicoConfig(){

    $('[name*="cd_servico"]').change(function(){
        
        var id = $(this).attr('id');
        var qtd_servico = $(this).attr('name').replace("cd_servico", "qtd_servico");
        var valor_servico = $(this).attr('name').replace("cd_servico", "valor_servico");
        var inicio_servico = $(this).attr('name').replace("cd_servico", "inicio_servico");
        var fim_servico = $(this).attr('name').replace("cd_servico", "fim_servico");
        var value = $(this).val();
        
        if($(this).val() != ''){
            
            //alert(value);
            
            //$('[name="'+qtd_servico+'"]').val('200');
            //$('[name="'+valor_servico+'"]').val('10.00');
            //alert($.datepicker.formatDate('dd/m/yy', new Date()));
            //alert(new Date('2015-10-12'));
            //$('[name="'+inicio_servico+'"]').val($.datepicker.formatDate('dd/m/yy', new Date('2015-10-12')));
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/servicosDados/'+value,
              /*data: {
                cd: ddd,
                linha: linha
              },*/
              dataType: "json",
              error: function(res) {
                alert('erro');
              },
              success: function(res) {
               
                $('[name="'+qtd_servico+'"]').val(res['qtd']);
                $('[name="'+valor_servico+'"]').val(res['valor']);
                $('[name="'+inicio_servico+'"]').val($.datepicker.formatDate('dd/mm/yy', new Date(res['data_inicio']))).prop( "disabled", false );
                $('[name="'+fim_servico+'"]').val($.datepicker.formatDate('dd/mm/yy', new Date(res['data_fim']))).prop( "disabled", false );
                
                calendario();
              }
            });
            
        }else{
            
            $('[name="'+qtd_servico+'"]').val('');
            $('[name="'+valor_servico+'"]').val('');
            $('[name="'+inicio_servico+'"]').val('').prop( "disabled", true );
            $('[name="'+fim_servico+'"]').val('').prop( "disabled", true );
            
        }
        
    });
}

$("#add-servico").click(function(){
    
    var cont = $('[name*="cd_servico"]').length;
    
    /*if(cont == 2){
        alert('<?php echo utf8_encode("Limite máximo alcançado");?>');
        return false;
    }*/
    
   var inputServico = '<div class="row">';
   inputServico += '<div class="col-md-3">';
   inputServico += '<label for="cd_servico['+cont+']" class="marginBottomZero">Nome<span class="obrigatorio">*</span></label>';
   inputServico += '<select name="cd_servico['+cont+']" id="cd_servico['+cont+']" title="Selecione o servi&ccedil;o" class="form-control required">';
   inputServico += '<option value="" selected="selected"></option>';
   inputServico += '<?php echo $optionServico; ?>';
   inputServico += '</select>';
   inputServico += '</div>';
   inputServico += '<div class="col-md-2">';
   inputServico += '<label>Quantidade';
   inputServico += '<input type="text" id="qtd_servico['+cont+']" name="qtd_servico['+cont+']" disabled class="form-control dinamic required" />';
   inputServico += '</label>';
   inputServico += '</div>';
   inputServico += '<div class="col-md-2">';
   inputServico += '<label>Valor';
   inputServico += '<input type="text" id="valor_servico['+cont+']" name="valor_servico['+cont+']" disabled class="form-control dinamic required" />';
   inputServico += '</label>';
   inputServico += '</div>';
   inputServico += '<div class="col-md-2">';
   inputServico += '<label>In&iacute;cio';
   inputServico += '<input type="text" id="inicio_servico['+cont+']" name="inicio_servico['+cont+']" title="Informe a data" class="form-control dinamic required data" />';
   inputServico += '</label>';
   inputServico += '</div>';
   inputServico += '<div class="col-md-2">';
   inputServico += '<label>Fim';
   inputServico += '<input type="text" id="fim_servico['+cont+']" name="fim_servico['+cont+']" title="Informe a data" class="form-control dinamic required data" />';
   inputServico += '</label>';
   inputServico += '</div>';
   inputServico += '<div class="col-md-1 paddingTopMedio">';
   inputServico += '<a onclick="$(this).parent().parent().remove();" href="#add-servico" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>';
   inputServico += '</div>';
   inputServico += '</div>';
        
   $("#servicos-ass").append(inputServico);
   
   $(".dinamic").css('font-weight', 'normal');
   
   $("#frm-salvar").validate();
   
   calendario();
   
   servicoConfig();
   
   $('[name*="inicio_servico['+cont+']"]').prop( "disabled", true );
   $('[name*="fim_servico['+cont+']"]').prop( "disabled", true );
   
});

$(document).ready(function(){

    //$("input[name='telefone_cliente[]']").mask("(00)00000-0000");
    
    maskReChamada();
    calendario();
    servicoConfig();
    
    function maskReChamada(){
        //$("input[name='telefone_cliente[]']").mask("(00)00000-0000");
        $(".qtd").mask("###0");
        $(".valor").mask("0.00##"/*, {reverse: true}*/);
        $("input[name^='cd_servico'],input[name^='qtd_servico'],input[name^='qtd'], input[name^='valor']").css('font-weight','normal');
    }
    
    $(".data").mask("00/00/0000");
    $(".valor").mask("0.00##"/*, {reverse: true}*/);
    $(".celular").mask("000000000");
    $(".qtd").mask("###0");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALEND&Aacute;RIO DATEPICKER NO INPUT INFORMADO
*/

function calendario(){
    $(".data").datepicker({
    	dateFormat: 'dd/mm/yy',
    	dayNames: ['Domingo','Segunda','Ter&amp;ccedil;a','Quarta','Quinta','Sexta','S&amp;aacute;bado','Domingo'],
    	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
    	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&amp;aacute;b','Dom'],
    	monthNames: ['Janeiro','Fevereiro','Mar&amp;ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
    	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    	nextText: 'Pr&amp;oacute;ximo',
    	prevText: 'Anterior',
        
        // Traz o calend&aacute;rio input datepicker para frente da modal
        beforeShow :  function ()  { 
            setTimeout ( function (){ 
                $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
            },  0 ); 
        } 
    });
}

$(document).ready(function(){
    
    // Valida o formul&aacute;rio
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            cd_telefonia_ddd:{
                required: true  
            },
			numero: {
                required: true,
                minlength: 8
            },
            cd_telefonia_operadora: {
                required: true
            },
            cd_telefonia_plano: {
                required: true
            }
		},
		messages: {
            cd_telefonia_ddd:{
                required: "Selecione o DDD"  
            },
			numero: {
                required: "<?php echo utf8_encode('Digite o n&uacute;mero.'); ?>",
                minlength: "<?php echo utf8_encode('Digite o n&uacute;mero completo.'); ?>"
            },
            cd_telefonia_operadora: {
                required: 'Selecione a operadora'
            },
            cd_telefonia_plano: {
                required: 'Selecione o plano'
            }
	   }
   });   
   
});
</script>