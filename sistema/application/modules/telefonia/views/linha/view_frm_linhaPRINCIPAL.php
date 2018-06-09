<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
    <!-- INÍCIO Modal adiciona serviço -->
    <div class="modal fade" id="new-servico" tabindex="-1" role="dialog" aria-labelledby="new-servico" aria-hidden="true">
        <div style="width: 800px;" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Adicionar novo servi&ccedil;o</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php              
                        $data = array('class'=>'pure-form','id'=>'frm-novo-servico');
                        echo form_open('telefonia/novoServico',$data);
                        
                        echo '<div class="col-md-4">';
                            echo form_label('Nome<span class="obrigatorio">*</span>', 'nome_servico[0]');
                			$data = array('name'=>'nome_servico[0]', 'id'=>'nome_servico[0]', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                			echo form_input($data);
                        echo '</div>';  
                        echo '<div class="col-md-2">';
                            echo form_label('Quantidade<span class="obrigatorio">*</span>', 'qtd_servico[0]');
                			$data = array('name'=>'qtd_servico[0]', 'id'=>'qtd_servico[0]', 'placeholder'=>'Digite a quantidade', 'class'=>'form-control qtd');
                			echo form_input($data);
                        echo '</div>'; 
                        echo '<div class="col-md-2">';
                            echo form_label('Valor', 'valor_servico[0]');
                			$data = array('name'=>'valor_servico[0]', 'id'=>'valor_servico[0]', 'placeholder'=>'Digite o valor', 'class'=>'form-control valor');
                			echo form_input($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-2">';
                            echo form_label('In&iacute;cio', 'inicio_servico[0]');
                			$data = array('name'=>'inicio_servico[0]', 'id'=>'inicio_servico[0]', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                			echo form_input($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-2">';
                            echo form_label('Fim', 'fim_servico[0]');
                			$data = array('name'=>'fim_servico[0]', 'id'=>'fim_servico[0]', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                			echo form_input($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-4">';
                            echo form_label('Nome', 'nome_servico[1]');
                			$data = array('name'=>'nome_servico[1]', 'id'=>'nome_servico[1]', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                			echo form_input($data);
                        echo '</div>';  
                        echo '<div class="col-md-2">';
                            echo form_label('Quantidade', 'qtd_servico[1]');
                			$data = array('name'=>'qtd_servico[1]', 'id'=>'qtd_servico[1]', 'placeholder'=>'Digite a quantidade', 'class'=>'form-control qtd');
                			echo form_input($data);
                        echo '</div>'; 
                        echo '<div class="col-md-2">';
                            echo form_label('Valor', 'valor_servico[1]');
                			$data = array('name'=>'valor_servico[1]', 'id'=>'valor_servico[1]', 'placeholder'=>'Digite o valor', 'class'=>'form-control valor');
                			echo form_input($data);
                        echo '</div>'; 
                        echo '<div class="col-md-2">';
                            echo form_label('In&iacute;cio', 'inicio_servico[1]');
                			$data = array('name'=>'inicio_servico[1]', 'id'=>'inicio_servico[1]', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                			echo form_input($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-2">';
                            echo form_label('Fim', 'fim_servico[1]');
                			$data = array('name'=>'fim_servico[1]', 'id'=>'fim_servico[1]', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                			echo form_input($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-4">';
                            echo form_label('Nome', 'nome_servico[2]');
                			$data = array('name'=>'nome_servico[2]', 'id'=>'nome_servico[2]', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                			echo form_input($data);
                        echo '</div>';  
                        echo '<div class="col-md-2">';
                            echo form_label('Quantidade', 'qtd_servico[2]');
                			$data = array('name'=>'qtd_servico[2]', 'id'=>'qtd_servico[2]', 'placeholder'=>'Digite a quantidade', 'class'=>'form-control qtd');
                			echo form_input($data);
                        echo '</div>';
                        echo '<div class="col-md-2">';
                            echo form_label('Valor', 'valor_servico[2]');
                			$data = array('name'=>'valor_servico[2]', 'id'=>'valor_servico[2]', 'placeholder'=>'Digite o valor', 'class'=>'form-control valor');
                			echo form_input($data);
                        echo '</div>';
                        echo '<div class="col-md-2">';
                            echo form_label('In&iacute;cio', 'inicio_servico[2]');
                			$data = array('name'=>'inicio_servico[2]', 'id'=>'inicio_servico[2]', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                			echo form_input($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-2">';
                            echo form_label('Fim', 'fim_servico[2]');
                			$data = array('name'=>'fim_servico[2]', 'id'=>'fim_servico[2]', 'placeholder'=>'Digite a data', 'class'=>'form-control data');
                			echo form_input($data);
                        echo '</div>';
                            
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="cd_linha" name="cd_linha" value="<?php echo $cd_telefonia_linha; ?>" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal adiciona serviço -->

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
                                
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                                    foreach($ddds as $ddd){
                                        $options[$ddd->cd_telefonia_ddd] = $ddd->descricao;
                                    }
                            		echo form_label('DDD<span class="obrigatorio">*</span>', 'cd_telefonia_ddd');
                            		echo form_dropdown('cd_telefonia_ddd', $options, $cd_telefonia_ddd, 'id="cd_telefonia_ddd" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('N&uacute;mero<span class="obrigatorio">*</span>', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Digite o nome', 'class'=>'form-control celular');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '');	
                                    foreach($operadoras as $ope){
                                        $options[$ope->cd_telefonia_operadora] = $ope->nome;
                                    }	
                            		echo form_label('Operadora<span class="obrigatorio">*</span>', 'cd_telefonia_operadora');
                            		echo form_dropdown('cd_telefonia_operadora', $options, $cd_telefonia_operadora, 'id="cd_telefonia_operadora" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($planos as $pla){
                                        $options[$pla->cd_telefonia_plano] = $pla->nome;
                                    }	
                            		echo form_label('Plano<span class="obrigatorio">*</span>', 'cd_telefonia_plano');
                            		echo form_dropdown('cd_telefonia_plano', $options, $cd_telefonia_plano, 'id="cd_telefonia_plano" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';
                                    
                                echo '</div>';
                                /*                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('cd_telefonia_linha', $cd_telefonia_linha);
                                
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        */
                        #if($cd_telefonia_linha){
                    ?>       
                        <div class="row text-center">
                            <div><br /><strong>Servi&ccedil;os associados a linha</strong></div>
                            <div id="res_associacao"></div>
                        </div>
                        
                        <div class="row" tabindex="1">
                            <div class="row">
                                <div class="col-md-12">
                                <?php
                                $options = array();
                                foreach($todosServicos as $tS){
                                    $valor = ($tS->valor)? ' | R$ '.$tS->valor: '';
                                    $qtd = ($tS->qtd)? ' | QTD.: '.$tS->qtd: '';
                                    $inicio = ($tS->data_inicio)? ' | IN&Iacute;CIO: '.$this->util->formataData($tS->data_inicio,'BR'): '';
                        			$fim = ($tS->data_fim)? ' | FIM :'.$this->util->formataData($tS->data_fim,'BR'): '';
                                    $options[$tS->cd_telefonia_servico] = ucfirst(strtolower($tS->nome)).$qtd.$valor.$inicio.$fim;
                        		}
                                #$options[100] = 'Outros';
                                echo form_label('Selecione o(s) servi&ccedil;o(s)&nbsp<a href="" id="novo-servico" data-toggle="modal"  data-target="#new-servico">Adicionar novo</a>
                                - &nbsp<a href="#" onclick="$(\'#cd_servico\').val(\'\').prop(\'selected\', true);$(\'#servicosSelecionados\').html(\'\');">Limpar servi&ccedil;os</a>', 'cd_servico');
                                echo form_dropdown('cd_servico[]', $options, $servicos, 'id="cd_servico" title="Par&acirc;metros que precisar&atilde;o ser preenchido para gerar o relat&oacute;rio" class="form-control" style="height:250px"');
                                ?>
                                </div>
                            </div>
                        </div>
                    <?php
                        #}
                        
                            echo '<div class="actions">';
                                
                            echo form_hidden('cd_telefonia_linha', $cd_telefonia_linha);
                                
                            echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                            echo '</div>';  
                        
                            echo form_fieldset_close();
                    	echo form_close(); 
                    ?>
                </div>
            </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
    
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$(document).ready(function(){

    //$("input[name='telefone_cliente[]']").mask("(00)00000-0000");
    
    maskReChamada();
    
    function maskReChamada(){
        //$("input[name='telefone_cliente[]']").mask("(00)00000-0000");
        $(".qtd").mask("###0");
        $(".valor").mask("0.00##"/*, {reverse: true}*/);
        $("input[name^='nome_servico'],input[name^='qtd_servico'],input[name^='qtd'], input[name^='valor']").css('font-weight','normal');
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
                required: "<?php echo utf8_encode('Digite o número.'); ?>",
                minlength: "<?php echo utf8_encode('Digite o número completo.'); ?>"
            },
            cd_telefonia_operadora: {
                required: 'Selecione a operadora'
            },
            cd_telefonia_plano: {
                required: 'Selecione o plano'
            }
	   }
   });   
   
   $("#frm-novo-servico").validate({
        debug: false,
		rules: {
            'nome_servico[0]':{
                required: true  
            },
            'qtd_servico[0]':{
                required: true  
            }
        },
        messages: {
            'nome_servico[0]':{
                required: "Digite o nome"  
            },
            'qtd_servico[0]':{
                required: "Digite a quantidade"  
            }
        },
   });
   
});
</script>