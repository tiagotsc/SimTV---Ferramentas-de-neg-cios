<?php
echo link_tag(array('href' => 'assets/css/hr.css','rel' => 'stylesheet','type' => 'text/css'));
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.blockui/jquery.block.ui.js') ?>"></script>
            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Ficha <?php echo $assunto; ?></li>
                </ol>
                <div id="divMain">
                    <div>
                        <a style="font-size: 21px;" href="<?php echo base_url($modulo.'/'.$controller.'/pesq'); ?>" class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>Voltar Pesquisar</a>
                    </div>
                    <div>
                    <?php
                    echo $this->session->flashdata('statusOperacao');
                    ?>
                    </div>
                    <table class="table">
                        <tr>
                            <th>Contrato</th>
                            <th>Permissor</th>
                            <th>Data início</th>
                            <th>Data fim</th>
                        </tr>
                        <tr>
                            <td><?php echo $numero; ?></td>
                            <td><?php echo utf8_decode($unidade_permissor.' - '.$unidade_nome); ?></td>
                            <td><?php echo $this->util->formataData($data_inicio,'BR'); ?></td>
                            <td><?php echo $this->util->formataData($data_fim,'BR'); ?></td>
                        </tr>
                    </table>
                    <hr class="style1" />
                    <?php  
                    if(in_array($perNumero, $this->session->userdata('permissoes'))){
                        $data = array('class'=>'pure-form','id'=>'frm-numero');
                    	echo form_open($modulo.'/'.$controller.'/alteraNumero',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Número", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Número<span class="obrigatorio">*</span>', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';

                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                echo form_hidden('id', $id);
                                echo form_submit("btn","Alterar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        echo '<hr class="style1" />';
                     }
                     
                    if(in_array($perVigencia, $this->session->userdata('permissoes'))){
                        $data = array('class'=>'pure-form','id'=>'frm-vigencia');
                    	echo form_open($modulo.'/'.$controller.'/alteraVigencia',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Vigência", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Data ínicio<span class="obrigatorio">*</span>', 'data_inicio');
                        			$data = array('name'=>'data_inicio', 'value'=>$this->util->formataData($data_inicio,'BR'),'id'=>'data_inicio', 'readonly' => 'readonly', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Data fim<span class="obrigatorio">*</span>', 'data_fim');
                        			$data = array('name'=>'data_fim', 'value'=>$this->util->formataData($data_fim,'BR'),'id'=>'data_fim', 'readonly' => 'readonly', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '', '12' => '12', '24' => '24', '36' => '36');		
                            		echo form_label('Dur. meses<span class="obrigatorio">*</span>', 'duracao_mes');
                            		echo form_dropdown('duracao_mes', $options, $duracao_mes, 'id="duracao_mes" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Calcular na:', 'calcular_hoje');
                                    $data = array(
                                        'name'        => 'calcular_hoje',
                                        'id'          => 'calcular_hoje',
                                        'value'       => 'S'
                                        );
                                    
                                    echo form_checkbox($data);
                                    echo 'Data de hoje';
                                    echo '</div>';  

                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('backup_mes', $duracao_mes);
                                echo form_hidden('id', $id);
                                
                                echo form_submit("btn","Alterar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        echo '<hr class="style1" />';
                     }
                        
                     if(in_array($perStatus, $this->session->userdata('permissoes'))){   
                        $data = array('class'=>'pure-form','id'=>'frm-status');
                    	echo form_open($modulo.'/'.$controller.'/alteraStatus',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Status", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo', 'C' => 'Cancelado'/*, 'P' => 'Pendente'*/);	
                                    echo '<div class="col-md-4">';	
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';     

                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                echo form_hidden('backup_status', $status);
                                
                                echo form_submit("btn","Alterar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        echo '<hr class="style1" />';
                        $data = array('class'=>'pure-form','id'=>'frm-anexo');
                    	echo form_open_multipart($modulo.'/'.$controller.'/salvarAnexo',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Anexo", $attributes);
                    		
                                echo '<div class="row">';
                                    	
                                    echo '<div class="col-md-4">';	
                            		echo form_label('Novo Arquivo', 'anexo');
                        			$data = array('name'=>'anexo','id'=>'anexo', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
                        			echo form_upload($data, '');
                                    echo '</div>'; 

                                echo '</div>';
                                
                                if($anexos){
                                    #echo '<pre>'; print_r($anexos); echo '</pre>';
                                    echo '<table class="table">';
                                    echo '<tr>';
                                    echo '<th>Arquivos</th>';
                                    echo '<th>Data</th>';
                                    echo '<th>A&ccedil;&atilde;o</th>';
                                    echo '</tr>';
                                    foreach($anexos as $an){
                                    echo '<tr>';
                                    echo '<td><a target="_blank" href="'.base_url('files/telecom/contrato/'.$an->anexo).'">'.$an->anexo_label.'</a></td>';
                                    echo '<td>'.$an->data_cadastro.'</td>';
                                    echo '<td><a href="#" anexo="'.$an->anexo.'" class="delAnexo glyphicon glyphicon-remove"></a></td>';
                                    echo '</tr>';
                                    }
                                    echo '</table>';
                                }
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('idContrato', $id);
                                
                                echo form_submit("btn","Anexar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        echo '<hr class="style1" />';
                     }
                        
                     if(in_array($perDesignacao, $this->session->userdata('permissoes'))){
                        $data = array('class'=>'pure-form','id'=>'frm-circuito');
                    	echo form_open('tcom-circuito/circuito/alterarCircuito',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Designação", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Designação', 'designacao');
                        			$data = array('name'=>'designacao', 'value'=>$circuito_designacao,'id'=>'data_fim', 'readonly'=>'readonly', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    $options = array('' => '');
                                    foreach($interface as $int){
                                        $options[$int->id] = $int->nome;
                                    }	
                                    echo '<div class="col-md-4">';	
                            		echo form_label('Interface', 'idInterface');
                            		echo form_dropdown('idInterface', $options, $circuito_idInterface, 'id="idInterface" class="form-control"');
                                    echo '</div>'; 
                                    
                                    $options = array('' => '');
                                    foreach($velocidade as $veloc){
                                        $options[$veloc->id] = $veloc->velocidade.' '.$veloc->tipo;
                                    }	
                                    echo '<div class="col-md-4">';	
                            		echo form_label('Velocidade', 'idTaxaDigital');
                            		echo form_dropdown('idTaxaDigital', $options, $circuito_idTaxaDigital, 'id="idTaxaDigital" class="form-control"');
                                    echo '</div>';      

                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                echo form_hidden('idCircuito', $idCircuito);
                                echo form_hidden('idContrato', $id);
                                echo form_submit("btn","Alterar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close();
                        echo '<hr class="style1" />';
                     }
                     
                     if(in_array($perPontaB, $this->session->userdata('permissoes'))){
                        $data = array('class'=>'pure-form','id'=>'frm-ponta-b');
                    	echo form_open($modulo.'/'.$controller.'/alteraPontabEndereco',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ponta B - Endereço", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Título', 'titulo');
                        			$data = array('name'=>'titulo', 'value'=>$dadosContCir->tituloPontaB,'id'=>'data_fim', 'readonly'=>'readonly', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('CEP<span class="obrigatorio">*</span>', 'cep');
                        			$data = array('name'=>'cep', 'value'=>$dadosContCir->cep,'id'=>'cep', 'placeholder'=>'Informe o CEP', 'class'=>'cep form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';	
                                    unset($options);
                                    foreach($estado as $est){
                                        $options[$est->cd_estado] = $est->sigla_estado;
                                    }		
                            		echo form_label('UF<span class="obrigatorio">*</span>', 'cd_estado');
                                    echo form_dropdown('cd_estado', $options, $dadosContCir->cd_estado, 'id="cd_estado" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Cidade', 'cidade');
                        			$data = array('name'=>'cidade', /*'readonly'=>'readonly',*/ 'value'=>$dadosContCir->cidade,'id'=>'cidade', 'placeholder'=>'Informe o bairro', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Endereço<span class="obrigatorio">*</span>', 'endereco');
                        			$data = array('name'=>'endereco', /*'readonly'=>'readonly',*/ 'value'=>$dadosContCir->endereco,'id'=>'endereco', 'placeholder'=>'Informe o endereço', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Número<span class="obrigatorio">*</span>', 'numero_end_cliente');
                        			$data = array('name'=>'numero_end_cliente', 'value'=>$dadosContCir->numero,'id'=>'numero_end_cliente', 'placeholder'=>'Informe o número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Bairro<span class="obrigatorio">*</span>', 'bairro');
                        			$data = array('name'=>'bairro', /*'readonly'=>'readonly',*/ 'value'=>$dadosContCir->bairro,'id'=>'bairro', 'placeholder'=>'Informe o bairro', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Complemento', 'complemento');
                        			$data = array('name'=>'complemento', 'value'=>$dadosContCir->complemento,'id'=>'complemento', 'placeholder'=>'Informe o complemento', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    if($dadosContCir->telefones){
                                        
                                        $contTel = 1;
                                        $telefones = explode(' / ',$dadosContCir->telefones);
                                        
                                        foreach($telefones as $tel){
                                            if($contTel == 1){
                                                $link = '&nbsp<a id="addTel" class="glyphicon glyphicon-plus" href="#"></a>';
                                            }else{
                                                $link = '&nbsp;<a class="glyphicon glyphicon-minus delTel" href="#"></a>';
                                            }
                                            
                                            echo '<div class="col-md-2">';
                                            echo form_label('Telefone'.$link, 'telefone[]');
                                			$data = array('name'=>'telefone[]', 'value'=>$tel,'id'=>'telefone[]', 'placeholder'=>'Informe o telefone', 'class'=>'telefone form-control');
                                			echo form_input($data);
                                            echo '</div>';
                                            
                                            $contTel++;
                                        }
                                    }else{
                                        echo '<div class="col-md-2">';
                                        echo form_label('Telefone&nbsp<a id="addTel" class="glyphicon glyphicon-plus" href="#"></a>', 'telefone[]');
                            			$data = array('name'=>'telefone[]', 'value'=>'','id'=>'telefone[]', 'placeholder'=>'Informe o telefone', 'class'=>'telefone form-control');
                            			echo form_input($data);
                                        echo '</div>';
                                    }   
                                    
                                    echo '<div id="telefones"></div>'; 

                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                echo form_hidden('idCircuito', $idCircuito);
                                echo form_hidden('idContrato', $id);
                                echo form_submit("btn","Alterar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close();
                     }
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$(".delAnexo").click(function(event){
    
    event.preventDefault();
    
    var r = confirm("Deseja apagar esse arquivo?");
    if (r == true) {
        url = "<?php echo base_url('tcom-contrato/contrato/anexoApaga/'.$id); ?>/"+$(this).attr('anexo');
        $(location).attr("href", url);        
    }
    
});

$(document).ready(function(){ 
    
    $(this).deletaTelefone();
    
    $(".telefone").mask("(00)0000-0000#");
    $('.cep').mask('00000-000');
    
    // Valida o formulário
	$("#frm-numero").validate({
		debug: false,
		rules: {
            numero: {
                required: true
            }
		},
		messages: {
            numero: {
                required: "Preencha o número, por favor."
            }
	   }
   });
   
   // Valida o formulário
	$("#frm-ponta-b").validate({
		debug: false,
		rules: {
            cep: {
                required: true
            },
            cd_estado: {
                required: true
            },
            endereco: {
                required: true
            },
            numero_end_cliente: {
                required: true
            },
            bairro: {
                required: true
            }
		},
		messages: {
            cep: {
                required: "Preencha o cep, por favor."
            },
            cd_estado: {
                required: "Selecione o estado, por favor."
            },
            endereco: {
                required: "Preencha o endereço, por favor."
            },
            numero_end_cliente: {
                required: "Preencha o número, por favor."
            },
            bairro: {
                required: "Preencha o bairro, por favor."
            }
	   }
   });
    
});

// Busca o endereço de acordo com o CEP
$(".cep").keyup(function(){

    if($(this).val().length == 9){ // CEP Completo
    
        $(this).carregando('Buscando endereço');

        $.ajax({
              type: "POST",
              url: wsBuscaCEP+$(this).val(),              
              data: {
                cep: $(this).val()
              },
              dataType: "json",
              /*error: function(res) {
                $("#resMarcar").html('<span>Erro de execução</span>');
              },*/
              success: function(res) { 
                $("#endereco").val(res.endereco);
                $("#bairro").val(res.bairro);
                $("#cidade").val(res.cidade);
                
                $('#cd_estado'+' option').each(function(){
                   var item = $(this).text();
                   if(item == res.uf){
                      $(this).prop('selected',true);
                   }
                });
                
                if(res == 0){ // Não achou endereço
                    //$("#maskProtecaoUf").css('display', 'none'); // Desabilita bloqueio de UF
                    $("#feedback").css('display', 'block');
                }else{ // Achou endereço
                   //$("#maskProtecaoUf").css('display', 'block'); // Habilita bloqueio de UF
                   $("#feedback").css('display', 'none');
                }
                
              }
            });
            
        $(document).ajaxStop($.unblockUI);
         
    }else{ // CEP Incompleto
        $("#endereco").val('');
        $("#bairro").val('');
        $("#cidade").val('');
        $("#cd_estado"+" option").prop("selected", false);
    }
    
}); 

$("#addTel").click(function(){
    event.preventDefault();
    var tel ='<div class="col-md-2">';
    tel += '<label for="telefone[]">';
    tel += 'Telefone&nbsp;<a class="glyphicon glyphicon-minus delTel" href="#"></a>';
    tel += '</label>';
    tel += '<input type="text" name="telefone[]" id="telefone[]" placeholder="Informe o telefone" class="telefone form-control">';
    tel += '</div>';
    
    $("#telefones").append(tel);
    
    $(".telefone").mask("(00)0000-0000#");
    
    $(this).deletaTelefone();
    
});

$.fn.deletaTelefone = function(){
    
    $(".delTel").click(function(){
        event.preventDefault();
        $(this).parent().parent().remove();
    }); 
    
};

$.fn.carregando = function(msg) {
    $(document).ajaxStart(
        $.blockUI({ 
        message:  '<h1>'+msg+'...</h1>',
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

</script>