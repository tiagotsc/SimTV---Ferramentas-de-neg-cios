<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<link href="<?php echo base_url("assets/js/select2/css/select2-personalizado.min.css"); ?>" rel="stylesheet" />
<script src="<?php echo base_url("assets/js/select2/js/select2.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.blockui/jquery.block.ui.js') ?>"></script>

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
                echo form_open_multipart($modulo.'/'.$controller.'/salvarAprovacao',$data);
                    $attributes = array('id' => 'address_info', 'class' => 'address_info');
     
            		echo form_fieldset("Ficha aprovação de viabilidade<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisa</a>", $attributes);
                ?>
                    <div class="row">
                    
                        <div class="col-md-12">
                            <strong>CONTROLE: <?php echo $viab['viabilidade']->controle; ?></strong>
                        </div>
                        <div class="col-md-12">
                            <?php
                            if($viab['viabilidade']->id_tipo == 5){
                                $clienteEnd = 'mdEnd';
                            }else{
                                $clienteEnd = 'clienteEnd';
                            }
                            ?>
                        
                            <table class="table table-bordered">
                                <tr>
                                    <th>PONTO A</th>
                                    <th>PONTO B</th>
                                </tr>
                                <tr>
                                    <td><strong>Tipo solicitação:</strong> <?php echo htmlentities($viab['viabilidade']->tipo); ?></td>
                                    <?php
                                    if(trim($viab['viabilidade']->designacao) != ''){
                                        $designacao = '<strong>Designação:</strong> '.'<a target="_blank" href="'.base_url('tcom-contrato/contrato/imprimir/'.$viab['viabilidade']->idContrato).'">'.$viab['viabilidade']->designacao.'</a>';
                                    }else{
                                        $designacao = '';
                                    }
                                    ?>
                                    <td class="text-left"><?php echo $designacao; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nome:</strong> <?php echo htmlentities($viab['operadora']->titulo); ?></td>
                                    <td><strong>Nome:</strong> <?php echo htmlentities($viab['cliente']->titulo); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Endereço:</strong> <?php echo htmlentities($viab['operadoraCob']->endereco.', '.$viab['operadoraCob']->numero); ?></td>
                                    <td><strong>Endereço:</strong> <?php echo htmlentities($viab[$clienteEnd]->endereco.', '.$viab[$clienteEnd]->numero); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Cidade:</strong> <?php echo $viab['operadoraCob']->nome_estado; ?></td>
                                    <td><strong>Cidade:</strong> <?php echo $viab[$clienteEnd]->nome_estado; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>UF:</strong> <?php echo $viab['operadoraCob']->sigla_estado; ?></td>
                                    <td><strong>UF:</strong> <?php echo $viab[$clienteEnd]->sigla_estado; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 cabecalhoDivisor text-center">
                            <strong id="titulo_endereco">DADOS INFORMADO PELO TÉCNICO: <?php echo $viabResp->nome_usuario; ?></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Viável:</strong> <?php echo ($viabResp->viavel =='S')? 'Sim': 'Não'; ?></td>
                                    <td><strong>Endereço encontrado?</strong> <?php echo ($viabResp->end_enco =='S')? 'Sim': 'Não'; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><h5 class="text-center"><strong>CLIENTE HFC</strong></h5></td>
                                </tr>
                                <tr>
                                    <td><strong>Cabo (Mts):</strong> <?php echo $viabResp->cabo; ?></td>
                                    <td><strong>Cordoalha (Mts):</strong> <?php echo $viabResp->cordoalha; ?></td>
                                    <td><strong>Canalização (Mts):</strong> <?php echo $viabResp->canalizacao; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><h5 class="text-center"><strong>PERCURSO FIBRA</strong></h5></td>
                                </tr>
                                <tr>
                                    <td><strong>Node distância:</strong> <?php echo $viabResp->node_distancia; ?></td>
                                    
                                    <?php
                                    $link = '';
                                    if($viabResp->anexo){
                                        
                                        $anexo = explode(',', $viabResp->anexo);
                                        
                                        foreach($anexo as $ane){
                                            $link[] = '<a target="_blank" href="'.base_url($dirDownload.'/'.$ane).'">'.$ane.'</a>';
                                        }
                                    }
                                    ?>
                                    
                                    <td colspan="2"><strong>Anexo:</strong> <?php echo implode(', ', $link); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><p class="text-left"><strong>Observação:</strong><br /><?php echo nl2br(htmlentities($viabResp->observacao)); ?></p></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 cabecalhoDivisor text-center">
                            <strong>DADOS DE APROVAÇÃO:</strong>
                        </div>
                    </div>
                    <?php   
                        
                        $numero = (isset($contrato[0]->numero))? $contrato[0]->numero: $viab['viabilidade']->n_solicitacao;
                                
                        echo '<div class="row">';
                            
                            echo '<div class="col-md-3">';
                            $options = array('' => '', 'S' => 'Sim', 'N' => 'Não', 'C' => 'Cancelado');		
                    		echo form_label('Aprovado<span class="obrigatorio">*</span>', 'aprovacao');
                    		echo form_dropdown('aprovacao', $options, $viabResp->aprovacao, 'id="aprovacao" class="form-control"');
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            echo form_label('Número do contrato', 'numero');
                			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Informe o número', 'class'=>'form-control');
                			echo form_input($data);
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            /*if(trim($viab['cliente']->titulo) != ''){
                                $redonly = 'readonly';
                            }else{
                                $redonly = 'no-readonly';
                            }*/
                            $redonly = 'no-readonly';
                            echo form_label('Nome do cliente', 'nome_cliente');
                			$data = array('name'=>'nome_cliente',$redonly=>true, 'value'=>trim($viab['cliente']->titulo),'id'=>'nome_cliente', 'placeholder'=>'Informe o nome do cliente', 'class'=>'form-control');
                			echo form_input($data);
                            echo '</div>';
                            /*
                            echo '<div class="col-md-3">';
                            echo form_label('Número do contrato', 'contrato');
                			$data = array('name'=>'contrato', 'value'=>$contrato,'id'=>'contrato', 'placeholder'=>'Informe o contrato', 'class'=>'form-control');
                			echo form_input($data);
                            echo '</div>';
                            */
                            echo '<div class="col-md-3">';
                            echo form_label('Prazo de ativação', 'prazo_ativacao');
                			$data = array('name'=>'prazo_ativacao', 'value'=>$this->util->formataData($viabResp->prazo_ativacao, 'BR'),'id'=>'prazo_ativacao', 'placeholder'=>'Informe o prazo', 'class'=>'data form-control');
                			echo form_input($data);
                            echo '</div>';
                            /*
                            echo '<div class="col-md-3">';
                            if(isset($contrato[0]->data_fim)){
                                $dataFim = $this->util->formataData($contrato[0]->data_fim, 'BR');                                
                                $redonly = 'readonly';
                                $classData = '';
                            }else{
                                $dataFim = '';                                
                                $redonly = '';
                                $classData = 'data';
                            }
                            echo form_label('Duração do contrato', 'data_fim');
                			$data = array('name'=>'data_fim',$redonly=>1, 'value'=>$dataFim,'id'=>'data_fim', 'placeholder'=>'Informe a duração', 'class'=>$classData.' form-control', );
                			echo form_input($data);
                            echo '</div>';
                            */
                            if($viabResp->gerou_contrato == 'S'){
                                $contrato = ($viabResp->idContratoAtual)? $viabResp->idContratoAtual: $viabResp->idContrato;
                                echo '<div class="col-md-4">';
                                echo '<strong>Designação gerada</strong><br>';
                    			echo '<a href="'.base_url('tcom-contrato/contrato/imprimir/'.$contrato).'" target="_BLANK">'.htmlentities($circuito->designacao).'</a>';
                                echo '</div>';
                            }
                         
                        echo '</div>'; 
                                                      
                        echo '<div class="actions">';
                        
                        echo form_hidden('id', $id);   
                        echo form_hidden('controle', $viab['viabilidade']->controle); 
                        echo form_hidden('idContrato', $viab['viabilidade']->idContrato); 
                        echo form_hidden('idCircuito', $circuito->id); 
                        echo form_hidden('idViab', $viab['viabilidade']->id); 
                        echo form_hidden('idTipo', $viab['viabilidade']->id_tipo);
                        echo form_hidden('tipo',$viab['viabilidade']->tipo);   
                        echo form_hidden('idUnidade', $viab['viabilidade']->cd_unidade);  
                        echo form_hidden('idOperadora', $viab['operadora']->id);   
                        echo form_hidden('idCliente', $viab['cliente']->id);  
                        echo form_hidden('idInterface', $viab['viabilidade']->idInterface); 
                        echo form_hidden('idTaxaDigital', $viab['viabilidade']->idTaxaDigital);  
                        echo form_hidden('qtdCircuitos', $viab['viabilidade']->qtd_circuitos);  
                        echo form_hidden('temContrato',$viabResp->idContrato); 
                                                                 
                        
                        if($viabResp->aprovacao == ''){
                        echo form_submit("btn","Salvar", 'id="btn" class="btn btn-primary pull-right"');
                        }
                        echo '</div>';   
                                                    
            		echo form_fieldset_close();
            	echo form_close(); 
                        
                ?>       
                </div>
            </div>
    
<script type="text/javascript">

$.fn.carregando = function() {
    $(document).ajaxStart(
        $.blockUI({ 
        message:  '<h1>Salvando e enviando e-mail...</h1>',
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

// Cria combo autocomplete


$(document).ready(function(){ 
    
    $('.data').mask('00/00/0000');
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            aprovacao: {
                required: true
            },
            numero: {
                required: true
            },
            nome_cliente: {
                required: true  
            },
            prazo_ativacao: {
                required: true  
            }
		},
		messages: {
            aprovacao: {
                required: "Selecione a aprovação."
            },
            numero: {
                required: "Informe o número."
            },
            nome_cliente: {
                required: "Informe o nome do cliente."
            },
            prazo_ativacao: {
                required: "Informe o prazo de ativação."
            }
	   }
   });
   
   $("#btn").click(function(){
        if($( "#frm-salvar" ).valid()){
            $(this).carregando();
        }
   });
    
});

$("#aprovacao").change(function (){
   
   if($(this).val() == 'S'){
    $( "#prazo_ativacao" ).rules( "add", {
      required: true,
      messages: {
        required: "Informe o prazo"
      }
    });
    $( "#numero" ).rules( "add", {
      required: true,
      messages: {
        required: "Informe o número do contrato"
      }
    });
    /*$( "#data_fim" ).rules( "add", {
      required: true,
      messages: {
        required: "Informe a duração"
      }
    });*/    
   }else{
    $( "#prazo_ativacao" ).rules( "remove");
    //$( "#data_fim" ).rules( "remove");
   }
    
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

<?php if($viab['viabilidade']->id_tipo != 1){ ?>

$( "#data_fim" ).val('00/00/0000').parent().hide();
<?php } ?>

</script>