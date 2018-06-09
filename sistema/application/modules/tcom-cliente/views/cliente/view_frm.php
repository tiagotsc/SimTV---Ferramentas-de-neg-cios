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
                    
                        $idCob = (isset($idCob))? $idCob: '';
                    
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha ".$assunto."<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Título', 'titulo');
                        			$data = array('name'=>'titulo', 'value'=>$titulo,'id'=>'titulo', 'placeholder'=>'Informe o título', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('E-mail', 'email');
                        			$data = array('name'=>'email', 'value'=>$email,'id'=>'email', 'placeholder'=>'Informe o e-mail', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('CNPJ', 'cnpj');
                        			$data = array('name'=>'cnpj', 'value'=>$cnpj,'id'=>'cnpj', 'placeholder'=>'Informe o CNPJ', 'class'=>'form-control cnpj');
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
                                    
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong>DADOS ENDEREÇO</strong></div>';
                                echo '</div>';
                                
                                echo '<div id="feedback" class="alert alert-warning" role="alert">';
                                    echo '<strong>Pesquise o endere&ccedil;o correto (Rua, avenida, etc) no site dos Correios <a target="_blank" href="http://www.correios.com.br">Correios</a></strong>';
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('CEP<span class="obrigatorio">*</span>', 'cep');
                        			$data = array('name'=>'cep', 'value'=>$cep,'id'=>'cep', 'placeholder'=>'Informe o CEP', 'class'=>'cep form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    #echo '<div id="maskProtecaoUf"></div>';
                                    $options = array('' => '');		
                                    foreach($estado as $est){
                                        $options[$est->cd_estado] = $est->sigla_estado;
                                    }		
                            		echo form_label('UF<span class="obrigatorio">*</span>', 'cd_estado');
                            		#echo form_dropdown('cd_estado', $options, $cd_estado, 'readonly="readonly" id="cd_estado" class="form-control"');
                                    echo form_dropdown('cd_estado', $options, $cd_estado, 'id="cd_estado" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Endereço<span class="obrigatorio">*</span>', 'endereco');
                        			$data = array('name'=>'endereco', /*'readonly'=>'readonly',*/ 'value'=>$endereco,'id'=>'endereco', 'placeholder'=>'Informe o endereço', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Número', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'placeholder'=>'Informe o número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Bairro<span class="obrigatorio">*</span>', 'bairro');
                        			$data = array('name'=>'bairro', /*'readonly'=>'readonly',*/ 'value'=>$bairro,'id'=>'bairro', 'placeholder'=>'Informe o bairro', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Complemento', 'complemento');
                        			$data = array('name'=>'complemento', 'value'=>$complemento,'id'=>'complemento', 'placeholder'=>'Informe o complemento', 'class'=>'form-control');
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

// Busca o endereço de acordo com o CEP
$(".cep").keyup(function(){
    
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

$(document).ready(function(){ 
    
    $(this).deletaTelefone();
    
    $(".telefone").mask("(00)0000-0000#");
    $(".cnpj").mask("99.999.999/9999-99");
    $('.cep').mask('00000-000');
    
    // Valida o formulário
	$("#frm-salvar").validate({
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
            bairro: {
                required: true
            }
		},
		messages: {
            cep: {
                required: "Informe o CEP."
            },
            cd_estado: { 
                required: 'Informe o estado' 
            },
			endereco: {
                required: "Informe o endereço."
            },
            bairro:{
                required: "Informe o bairro."
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