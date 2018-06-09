<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js"); ?>"></script>
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
                    	echo form_open($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha ".$assunto."<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '', '0' => '-');	
                                    foreach($pais as $p){
                                        $options[$p->id] = $p->titulo;
                                    }	
                            		echo form_label('Grupo<span class="obrigatorio">*</span>', 'pai');
                            		echo form_dropdown('pai', $options, $pai, 'id="pai" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                                    foreach($unidade as $uni){
                                        if($uni->cd_unidade != 15){
                                        $options[$uni->cd_unidade] = htmlentities($uni->nome);
                                        }
                                    }		
                            		echo form_label('Permissor<span class="obrigatorio obriOpcional">*</span>', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>'; 
                                    
                                    echo '<div class="col-md-5">';
                                    echo form_label('Título<span class="obrigatorio">*</span>', 'titulo');
                        			$data = array('name'=>'titulo', 'value'=>$titulo,'id'=>'titulo', 'placeholder'=>'Informe o título', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('E-mail', 'email');
                        			$data = array('name'=>'email', 'value'=>$email,'id'=>'email', 'placeholder'=>'Informe o e-mail', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Razão social', 'razaoSocial');
                        			$data = array('name'=>'razaoSocial', 'value'=>$razaoSocial,'id'=>'razaoSocial', 'placeholder'=>'Informe a razão social', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Inscrição estadual', 'inscEstadual');
                        			$data = array('name'=>'inscEstadual', 'value'=>$inscEstadual,'id'=>'inscEstadual', 'placeholder'=>'Informe a inscrição estadual', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Inscrição municipal', 'inscMunicipal');
                        			$data = array('name'=>'inscMunicipal', 'value'=>$inscMunicipal,'id'=>'inscMunicipal', 'placeholder'=>'Informe a inscrição municipal', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Status', 'status');
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';  
                                    
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong>DADOS INSTALAÇÃO</strong></div>';
                                echo '</div>';
                                
                                echo '<div class="feedbackCep alert alert-warning" role="alert">';
                                    echo '<strong>Pesquise o endere&ccedil;o correto (Rua, avenida, etc) no site dos Correios <a target="_blank" href="http://www.correios.com.br">Correios</a></strong>';
                                echo '</div>';
                                
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-3">';
                                    echo form_label('CNPJ<span class="obrigatorio obriOpcional">*</span>', 'cnpj');
                        			$data = array('name'=>'cnpj', 'value'=>$cnpj,'id'=>'cnpj', 'placeholder'=>'Informe o CNPJ', 'class'=>'form-control cnpj');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('CEP<span class="obrigatorio obriOpcional">*</span>', 'cep');
                        			$data = array('name'=>'cep', 'value'=>$cep,'id'=>'cep', 'placeholder'=>'Informe o CEP', 'class'=>'cep form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo '<div id="maskProtecaoUf"></div>';
                                    $options = array('' => '');		
                                    foreach($estado as $est){
                                        $options[$est->cd_estado] = $est->sigla_estado;
                                    }		
                            		echo form_label('UF<span class="obrigatorio obriOpcional">*</span>', 'cd_estado');
                            		echo form_dropdown('cd_estado', $options, $cd_estado, 'readonly="readonly" id="cd_estado" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-5">';
                                    echo form_label('Endereço<span class="obrigatorio obriOpcional">*</span>', 'endereco');
                        			$data = array('name'=>'endereco', 'readonly'=>'readonly', 'value'=>$endereco,'id'=>'endereco', 'placeholder'=>'Informe o endereço', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Número<span class="obrigatorio obriOpcional">*</span>', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Informe o número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Bairro<span class="obrigatorio obriOpcional">*</span>', 'bairro');
                        			$data = array('name'=>'bairro', 'readonly'=>'readonly', 'value'=>$bairro,'id'=>'bairro', 'placeholder'=>'Informe o bairro', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Cidade', 'cidade');
                        			$data = array('name'=>'cidade', 'value'=>$cidade,'id'=>'cidade', 'placeholder'=>'Informe a cidade', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Complemento', 'complemento');
                        			$data = array('name'=>'complemento', 'value'=>$complemento,'id'=>'complemento', 'placeholder'=>'Informe o complemento', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-5">';
                                    echo form_label('O endereço de cobrança é o mesma a instalação?', 'cobInst');
                                    $options = array('NAO' => 'Não', 'SIM' => 'Sim');		
                            		echo form_dropdown('cobInst', $options, $cobInst, 'id="cobInst" class="form-control" style="width:150px"');
                                    echo '</div>'; 
                                    
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong>DADOS COBRANÇA</strong></div>';
                                echo '</div>';
                                
                                echo '<div class="feedbackCep alert alert-warning" role="alert">';
                                    echo '<strong>Pesquise o endere&ccedil;o correto (Rua, avenida, etc) no site dos Correios <a target="_blank" href="http://www.correios.com.br">Correios</a></strong>';
                                echo '</div>';
                                
                                
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-3">';
                                    echo form_label('CNPJ<span class="obrigatorio obriOpcional">*</span>', 'cnpj_cob');
                        			$data = array('name'=>'cnpj_cob', 'value'=>$cnpj_cob,'id'=>'cnpj_cob', 'placeholder'=>'Informe o CNPJ', 'class'=>'form-control cnpj');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('CEP<span class="obrigatorio obriOpcional">*</span>', 'cep_cob');
                        			$data = array('name'=>'cep_cob', 'value'=>$cep_cob,'id'=>'cep_cob', 'placeholder'=>'Informe o CEP', 'class'=>'cep form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo '<div id="maskProtecaoUf"></div>';
                                    $options = array('' => '');		
                                    foreach($estado as $est){
                                        $options[$est->cd_estado] = $est->sigla_estado;
                                    }		
                            		echo form_label('UF<span class="obrigatorio obriOpcional">*</span>', 'cd_estado_cob');
                            		echo form_dropdown('cd_estado_cob', $options, $cd_estado_cob, 'readonly="readonly" id="cd_estado_cob" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-5">';
                                    echo form_label('Endereço<span class="obrigatorio obriOpcional">*</span>', 'endereco_cob');
                        			$data = array('name'=>'endereco_cob', 'readonly'=>'readonly', 'value'=>$endereco_cob,'id'=>'endereco_cob', 'placeholder'=>'Informe o endereço', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Número<span class="obrigatorio obriOpcional">*</span>', 'numero_cob');
                        			$data = array('name'=>'numero_cob', 'value'=>$numero_cob,'id'=>'numero_cob', 'placeholder'=>'Informe o número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Bairro<span class="obrigatorio obriOpcional">*</span>', 'bairro_cob');
                        			$data = array('name'=>'bairro_cob', 'readonly'=>'readonly', 'value'=>$bairro_cob,'id'=>'bairro_cob', 'placeholder'=>'Informe o bairro', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Cidade', 'cidade_cob');
                        			$data = array('name'=>'cidade_cob', 'value'=>$cidade_cob,'id'=>'cidade_cob', 'placeholder'=>'Informe a cidade', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Complemento', 'complemento_cob');
                        			$data = array('name'=>'complemento_cob', 'value'=>$complemento_cob,'id'=>'complemento_cob', 'placeholder'=>'Informe o complemento', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    if($telefones){
                                        $contTel = 1;
                                        foreach($telefones as $telefone){
                                            
                                            if($contTel == 1){
                                                $link = '&nbsp<a id="addTel" class="glyphicon glyphicon-plus" href="#"></a>';
                                            }else{
                                                $link = '&nbsp;<a class="glyphicon glyphicon-minus delTel" href="#"></a>';
                                            }
                                            
                                            echo '<div class="col-md-2">';
                                            echo form_label('Telefone'.$link, 'telefone[]');
                                			$data = array('name'=>'telefone[]', 'value'=>$telefone->telefone,'id'=>'telefone[]', 'placeholder'=>'Informe o telefone', 'class'=>'telefone form-control');
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
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('HUB Operadora', 'hub_cob');
                        			$data = array('name'=>'hub_cob', 'value'=>$hub_cob,'id'=>'hub_cob', 'placeholder'=>'Informe o HUB', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('HEADEND Operadora', 'headend_cob');
                        			$data = array('name'=>'headend_cob', 'value'=>$headend_cob,'id'=>'headend_cob', 'placeholder'=>'Informe o HEADEND', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-12">';
                                    echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                                      echo '<textarea id="observacao" name="observacao" rows="5" cols="10" class="form-control">'.utf8_decode($observacao).'</textarea>';
                                    echo '</div>';  
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                echo form_hidden('idCob', $idCob);
                                
                                echo form_submit("btn","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$('#pai').select2({
  placeholder: 'Selecione uma opção'
});

$("#pai").change(function(){

    // Não é pai - Informe os dados de instalação e cobrança
    if($(this).val() != '0'){
        
        $(".obriOpcional").show();
        
        $( "#cnpj" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o CNPJ"
          }
        });
        
        $( "#cep" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o CEP"
          }
        });
        
        $( "#cd_estado" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o estado da instalação"
          }
        });
        
        $( "#endereco" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o da instalação"
          }
        });
        
        $( "#numero" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o número"
          }
        });
        
        $( "#bairro" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o bairro"
          }
        });
        
        $( "#cd_unidade" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o permissor"
          }
        });
        
        $( "#cnpj_cob" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o CNPJ de cobrança"
          }
        });
        
        $( "#cep_cob" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o CEP de cobrança"
          }
        });
        
        $( "#cd_estado_cob" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o estado de cobrança"
          }
        });
        
        $( "#endereco_cob" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o endereço de cobrança"
          }
        });
        
        $( "#numero_cob" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o número de cobrança"
          }
        });
        
        $( "#bairro_cob" ).rules( "add", {
          required: true,
          messages: {
            required: "Informe o bairro de cobrança"
          }
        });
        
    }else{ // Se for pai informa só o cabeçalho
        
        $(".obriOpcional").hide();
    
        $( "#cnpj" ).rules( "remove");
        $( "#cep" ).rules( "remove");
        $( "#cd_estado" ).rules( "remove");
        $( "#endereco" ).rules( "remove");
        $( "#numero" ).rules( "remove");
        $( "#bairro" ).rules( "remove");
        $( "#cd_unidade" ).rules( "remove");
        $( "#cnpj_cob" ).rules( "remove");
        $( "#cep_cob" ).rules( "remove");
        $( "#cd_estado_cob" ).rules( "remove");
        $( "#endereco_cob" ).rules( "remove");
        $( "#numero_cob" ).rules( "remove");
        $( "#bairro_cob" ).rules( "remove");
    }
})

// Busca o endereço de acordo com o CEP
$(".cep").keyup(function(){
    var auxNome = '';
    if($(this).attr("name") == 'cep_cob'){
        auxNome = '_cob';
    }
    
    var cep = $(this);
    
    //$(".feedbackCep").hide();
    //$(this).parent().parent().siblings('.feedbackCep').show();
            
    if($(this).val().length == 9){ // CEP Completo
        
        $(this).carregando('Buscando endereço');
    
        $.ajax({
              type: "POST",
              //url: '<?php echo base_url(); ?>ajax/pegaEndereco',
              url: wsBuscaCEP+$(this).val(),              
              data: {
                cep: $(this).val()
              },
              dataType: "json",
              /*error: function(res) {
                $("#resMarcar").html('<span>Erro de execução</span>');
              },*/
              success: function(res) {
                $("#endereco"+auxNome).val(res.endereco);
                $("#bairro"+auxNome).val(res.bairro);
                $("#cidade"+auxNome).val(res.cidade);
                
                $('#cd_estado'+auxNome+' option').each(function(){
                   var item = $(this).text();
                   if(item == res.uf){
                      $(this).prop('selected',true);
                   }
                });
                
                if(res == 0){ // Não achou endereço
                    cep.parent().parent().prev().show();
                }else{ // Achou endereço
                    cep.parent().parent().prev().hide();
                }
                
              }
            }); 
        
        $(document).ajaxStop($.unblockUI);    
        
    }else{ // CEP Incompleto
        $("#endereco"+auxNome).val('');
        $("#bairro"+auxNome).val('');
        $("#cidade"+auxNome).val('');
        $("#cd_estado"+auxNome+" option").prop("selected", false);
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

$("#cobInst").change(function(){
    if($(this).val() == 'SIM'){
        $("#cnpj_cob").val($("#cnpj").val());
        $("#cep_cob").val($("#cep").val());
        $("#cd_estado_cob").val($("#cd_estado").val());
        $("#endereco_cob").val($("#endereco").val());
        $("#numero_cob").val($("#numero").val());
        $("#bairro_cob").val($("#bairro").val());
        $("#cidade_cob").val($("#cidade").val());
        $("#complemento_cob").val($("#complemento").val());
        
    }else{
        $("#cnpj_cob").val('');
        $("#cep_cob").val('');
        $("#cd_estado_cob").val('');
        $("#endereco_cob").val('');
        $("#numero_cob").val('');
        $("#bairro_cob").val('');
        $("#cidade_cob").val('');
        $("#complemento_cob").val('');
    }
})

$(document).ready(function(){ 
    
    $(".feedbackCep").hide();
    
    $(this).deletaTelefone();
    
    $(".obriOpcional").hide();
    
    $(".telefone").mask("(00)0000-0000#");
    $(".cnpj").mask("99.999.999/9999-99");
    $('.cep').mask('00000-000');
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            titulo: {
                required: true
            },
            email: { 
                //required: true, 
                email: true 
            },
			pai: {
                required: true
            }
		},
		messages: {
            titulo: {
                required: "Informe o título."
            },
            email: { 
                //required: 'Informe o seu email', 
                email: 'Ops, informe um email válido' 
            },
			pai: {
                required: "Selecione o grupo da empresa."
            }
	   }
   });
    
});

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