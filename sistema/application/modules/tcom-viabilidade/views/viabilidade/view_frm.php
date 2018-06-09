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
                        $dt_solicitacao = ($dt_solicitacao == '')? date('d/m/Y'): $dt_solicitacao;
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                    	echo form_open_multipart($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha ".$assunto."<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Controle', 'controle');
                        			$data = array('name'=>'controle', 'value'=>$controle,'id'=>'controle', 'readonly' => 'readonly', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Data da solicitação', 'dt_solicitacao');
                        			$data = array('name'=>'dt_solicitacao', 'value'=>$this->util->formataData($dt_solicitacao, 'BR'),'id'=>'dt_solicitacao', 'placeholder'=>'Informe o data', 'class'=>'data form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Número da solicitação', 'n_solicitacao');
                        			$data = array('name'=>'n_solicitacao', 'value'=>$n_solicitacao,'id'=>'n_solicitacao', 'placeholder'=>'Informe o número', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '-');		
                                    foreach($tiposViab as $tViab){
                                        $options[$tViab->id] = htmlentities($tViab->nome);
                                    }		
                            		echo form_label('Tipo<span class="obrigatorio">*</span>', 'idViabTipo');
                            		echo form_dropdown('idViabTipo', $options, $idViabTipo, 'id="idViabTipo" class="form-control"');
                                    echo '</div>'; 

                                    echo '<div class="col-md-3">';
                                    echo '<div class="pri maskProt"></div>';
                                    $options = array('' => '');
                                    foreach($unidade as $uni){
                                        if(!in_array($uni->cd_unidade, array(15))){
                                            $options[$uni->cd_unidade] = htmlentities($uni->nome);
                                        }
                                    }			
                            		echo form_label('Permissor<span class="obrigatorio">*</span>', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>'; 
                                    
                                    echo '<div style="margin-top:50px" class="contrato col-md-12">';
                                    $options = array('' => '-');	
                                    foreach($contratos as $con){
                                        $options[$con->id] = $con->numero;
                                    }		
                            		echo form_label('Contrato<span class="obrigatorio">*</span>', 'idContrato');
                            		echo form_dropdown('idContrato', $options, $idContrato, 'id="idContrato" class="form-control"');
                                    echo '</div>';                                     
                                    
                                    echo '<div class="col-md-12">';
                                    echo '<div class="geral maskProt"></div>';
                                    $options = array('' => '-');		
                                    foreach($operadoras as $ope){
                                        $options[$ope->id] = htmlentities($ope->id.' - '.$ope->titulo.' - '.$ope->cnpj.' -> '.$ope->endereco);
                                    }		
                            		echo form_label('Ponto A (Operadora)<span class="obrigatorio">*</span>', 'idOper');
                            		echo form_dropdown('idOper', $options, $idOper, 'id="idOper" class="form-control"');
                                    echo '</div>';  
                                    
                                    echo '<div class="col-md-12">';
                                    echo '<div class="geral maskProt"></div>';
                                    $options = array('' => '-', 'NOVO' => 'Novo Cliente');	
                                    	
                                    foreach($clientes as $cli){
                                        $options[$cli->id] = htmlentities($cli->id.' - '.$cli->titulo.' -> '.$cli->endereco);
                                    }		
                            		echo form_label('Ponto B (Cliente)<span class="obrigatorio">*</span>', 'idCliente');
                            		echo form_dropdown('idCliente', $options, $idCliente, 'id="idCliente" class="form-control"');
                                    echo '</div>';  
                                    
                              echo '</div>';

                                echo '<div class="novo-cliente row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong id="titulo_endereco">DADOS NOVO CLIENTE</strong></div>';
                                echo '</div>';
                                echo '<div id="feedback" class="alert alert-warning" role="alert">';
                                    echo '<strong>Pesquise o endere&ccedil;o correto (Rua, avenida, etc) no site dos Correios <a target="_blank" href="http://www.correios.com.br">Correios</a></strong>';
                                echo '</div>';
                                
                                echo '<div class="novo-cliente row">';
                                    
                                    echo '<div id="nome_cliente" class="col-md-12">';
                                    echo form_label('Nome', 'titulo');
                        			$data = array('name'=>'titulo', 'value'=>$titulo,'id'=>'titulo', 'placeholder'=>'Informe o nome.', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
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
                                			$data = array('name'=>'telefone[]', 'value'=>$telefone->telefone,'id'=>'telefone[]', 'maxlength' => 14, 'placeholder'=>'Informe o telefone', 'class'=>'telefone form-control');
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
                              
                              echo '<div class="row">';
                                
                                echo '<div class="col-md-2">';
                                echo '<div class="sec maskProt"></div>';
                                    $options = array('NAO' => 'Não', 'FIBRA' => 'Fibra Ótica', 'EQUIPAMENTO' => 'Equipamento');			
                            		echo form_label('Redundância', 'redundancia');
                            		echo form_dropdown('redundancia', $options, $redundancia, 'id="redundancia" class="form-control"');
                                echo '</div>';
                                
                                echo '<div class="col-md-2">';
                                echo form_label('Qtd. de Circuitos', 'qtd_circuitos');
                    			$data = array('name'=>'qtd_circuitos', 'value'=>$qtd_circuitos,'id'=>'qtd_circuitos', 'placeholder'=>'Informe o qtd.', 'class'=>'form-control');
                    			echo form_input($data);
                                echo '</div>';
                                
                                echo '<div class="col-md-2">';
                                echo '<div class="sec maskProt"></div>';
                                    $options = array('' => '');		
                                    foreach($interfaces as $int){
                                        $options[$int->id] = $int->nome;
                                    }		
                            		echo form_label('Interface<span class="obrigatorio">*</span>', 'idInterface');
                            		echo form_dropdown('idInterface', $options, $idInterface, 'id="idInterface" class="form-control"');
                                echo '</div>';
                                
                                echo '<div class="col-md-2">';
                                echo '<div class="sec maskProt"></div>';
                                    $options = array('' => '');		
                                    foreach($taxas as $t){
                                        $options[$t->id] = $t->velocidade.' '.$t->tipo;
                                    }		
                            		echo form_label('Velocidade<span class="obrigatorio">*</span>', 'idTaxaDigital');
                            		echo form_dropdown('idTaxaDigital', $options, $idTaxaDigital, 'id="idTaxaDigital" class="form-control"');
                                echo '</div>';

                              echo '</div>';
                            
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-12">';
                                    echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                                      echo '<textarea id="observacao" name="observacao" rows="5" cols="10" class="form-control">'.utf8_decode($observacao).'</textarea>';
                                    echo '</div>';
                                    
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                echo form_hidden('idMdEnd', $idMdEnd);
                                
                                echo form_submit("btn","Salvar", 'id="btn" class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$("#cd_unidade").change(function (){
   
   if($(this).val() != ''){
        $(this).carregaOperadora($(this).val());
   }else{
        $(this).carregaOperadora('N');
   }
    
});

$.fn.carregaOperadora = function(varUnidade) {
    if(varUnidade == ''){
        varUnidade = 'N';
    }
    $.ajax({
      type: "POST",
      url: '<?php echo base_url().$modulo; ?>/ajaxViabilidade/pegaOperadorasUnidade',          
      data: {
        unidade: varUnidade
      },
      dataType: "json",
      /*error: function(res) {
        $("#resMarcar").html('<span>Erro de execução</span>');
      },*/
      success: function(res) { 
        
        if(res.length > 0){
        
            content = '<option value=""></option>';
            
            $.each(res, function() {
              
              content += '<option value="'+ this.id +'">'+this.id+' - '+ this.titulo +' - '+this.cnpj+' -> '+this.endereco+'</option>';
              
            });
            
            $("#idOper").html('');
            $("#idOper").append(content);
        
        }else{
            $("#idOper").html('');
        }
        
        $("#idOper").val('<?php echo $idOper; ?>').trigger('change');
        
      }
    }); 
    
}

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

// Seleção de contrato
$("#idContrato").change(function(){
    if($(this).val() != ''){
        dadosContrato();
    }else{
        limpaDadosContrato();
    }
});

// Limpa dados do contrato carregado
function limpaDadosContrato(){
    $("#idOper").val('');
    $("#idCliente").val('');
    $("#idOper").val('').trigger('change');
    $("#idCliente").val('').trigger('change');
    $("#cd_unidade").val('');
    $("#redundancia").val('NAO');
    $("#qtd_circuitos").val('');
    $("#idInterface").val('');
    $("#idTaxaDigital").val('');
}

// Carrega dados do contrato
function dadosContrato(){
    
    if($("#idViabTipo").val() == ''){
        alert("Selecione o tipo de viabilidade");
        $("#idViabTipo").focus();
        limpaDadosContrato();
    }else if($("#idContrato").val() == ''){
        //alert("Selecione o contrato");
        $("#idContrato").focus();
        limpaDadosContrato();
    }else{ 
        $.ajax({
          type: "POST",
          url: '<?php echo base_url().$modulo; ?>/ajaxViabilidade/dadosContrato',          
          data: {
            id: $("#idContrato").val()
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) { 
            $("#idOper").val(res.idOper).trigger('change');
            $("#idCliente").val(res.idCliente).trigger('change');
            $("#cd_unidade").val(res.cd_unidade);
            $("#qtd_circuitos").val(res.qtd_circuitos);
            $("#idInterface").val(res.idInterface);
            $("#idTaxaDigital").val(res.idTaxaDigital);
          }
        }); 
               
    }

}

// Limpa dados do novo cliente
function limpaDadosNovoCliente(){
    
    $("#titulo").val('');
    $("#cep").val('');
    $("#cd_estado").val('');
    $("#endereco").val('');
    $("#numero").val('');
    $("#bairro").val('');
    $("#complemento").val('');
    $("input[name^='telefone']").val('');
    $(".telefoneDinamico").parent().parent().remove();
    
}

// Cria combo autocomplete
$('#idOper, #idCliente, #idContrato').select2({
  //placeholder: 'Selecione uma opção'
});

// Ao selecionar o tipo de viabilidade
$("#idViabTipo").change(function(){
   
   $(".geral.maskProt").show();
   $(".select2-selection__rendered").css("backgroundColor", "#eee");
   primeiroBloqueio();
   segundoBloqueio();
   
   // Ativação ou Darkfiber
   if($(this).val() == '1' || $(this).val() == '4'){
    
    limpaDadosContrato();
    
    $(".geral.maskProt").hide();
    $("#idContrato").val('');
    $(".contrato").hide();
    $(".select2-selection__rendered").css("backgroundColor", "white");
    primeiroDesbloqueio();
    segundoDesbloqueio();
   }
   
   // Upgrade ou Downgrade
   if($(this).val() == '2' || $(this).val() == '3'){
    segundoDesbloqueio();
    $(".contrato").show();
    $("#select2-idContrato-container").css({"backgroundColor": "white"});
   }
   
   // Endereço
   if($(this).val() == '5'){
    $(".contrato").show();
    $("#titulo_endereco").html("MUDANÇA DE ENDEREÇO");  
    obrigaEndereco();
    $("#nome_cliente").hide();
    $("#titulo").val('');
    $(".novo-cliente").show();
    $("#select2-idContrato-container").css({"backgroundColor": "white"});
   }else{
    $("#titulo_endereco").html("DADOS NOVO CLIENTE");
    desobrigarEndereco();
    $("#nome_cliente").show();
    $(".novo-cliente").hide();
   }
   
   // Se for upgrade, downgrade ou mudança de endereço
   // Obriga a seleção de contrato
   if($(this).val() == '2' || $(this).val() == '3' || $(this).val() == '5'){
    $( "#idContrato" ).rules( "add", {
      required: true,
      messages: {
        required: "Selecione o contrato"
      }
    });
   }else{
    $( "#idContrato" ).rules( "remove");
   }

});

// Obriga preenchimento do endereço
function obrigaEndereco(){
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
}

// Desobriga preenchimento do endereço
function desobrigarEndereco(){
        $( "#cep" ).rules( "remove");
        $( "#cd_estado" ).rules( "remove");
        $( "#endereco" ).rules( "remove");
        $( "#numero" ).rules( "remove");
        $( "#bairro" ).rules( "remove");
}

// Bloqueia primeiro bloco
function primeiroBloqueio(){ 
    $("#cd_unidade").prop('readonly',true).css("backgroundColor", "#eee");
    $(".pri.maskProt").show();
}

// Desbloqueia primeiro bloco
function primeiroDesbloqueio(){
    $("#cd_unidade").prop('readonly',false).css("backgroundColor", "white");
    $(".pri.maskProt").hide();
}

// Bloqueia segundo bloco
function segundoBloqueio(){
    $("#qtd_circuitos").prop('readonly',true);
    $("#idInterface").prop('readonly',true).css("backgroundColor", "#eee");
    $("#idTaxaDigital").prop('readonly',true).css("backgroundColor", "#eee");
    $(".sec.maskProt").show();
}

// Desbloqueia segundo bloco
function segundoDesbloqueio(){
    $("#qtd_circuitos").prop('readonly',false);
    $("#idInterface").prop('readonly',false).css("backgroundColor", "white");
    $("#idTaxaDigital").prop('readonly',false).css("backgroundColor", "white");
    $(".sec.maskProt").hide();
}

// Ao selecionar cliente
$("#idCliente").change(function(){
    if($(this).val() == ''){ 
        $("#titulo").val('');
        limpaDadosNovoCliente();
        desobrigarEndereco();
        $(".novo-cliente").hide();
    }else if($(this).val() == 'NOVO'){ 
        $("#titulo").val('');
        obrigaEndereco();
        $(".novo-cliente").show();
    }else{ 
        $("#titulo").val('');
        limpaDadosNovoCliente();
        desobrigarEndereco();
        
        // Se for diferente de tipo endereço
        if($("#idViabTipo").val() != 5){
            $(".novo-cliente").hide();
        }
    }
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
                    $("#feedback").css('display', 'block');
                }else{ // Achou endereço
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

// Adicionar telefone dinâmicamente
$("#addTel").click(function(){
    event.preventDefault();
    var tel ='<div class="col-md-2">';
    tel += '<label for="telefone[]">';
    tel += 'Telefone&nbsp;<a class="glyphicon glyphicon-minus delTel" href="#"></a>';
    tel += '</label>';
    tel += '<input type="text" name="telefone[]" id="telefone[]" maxlength="14" placeholder="Informe o telefone" class="telefone telefoneDinamico form-control">';
    tel += '</div>';
    
    $("#telefones").append(tel);
    
    $(".telefone").mask("(00)0000-0000#");
    
    $(this).deletaTelefone();
    
});

// Apaga telefone
$.fn.deletaTelefone = function(){
    
    $(".delTel").click(function(){
        event.preventDefault();
        $(this).parent().parent().remove();
    }); 
    
};

$(document).ready(function(){ 
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            dt_solicitacao: {
                required: true
            },
            n_solicitacao: { 
                required: true 
            },
			idViabTipo: {
                required: true
            },
            cd_unidade: {
                required: true
            },
            idInterface: {
                required: true
            },
            idTaxaDigital: {
                required: true
            },
            idOper: {
                required: true
            },
            idCliente: {
                required: true
            }
		},
		messages: {
            dt_solicitacao: {
                required: "Informe a data."
            },
            n_solicitacao: { 
                required: 'Informe o número' 
            },
			idViabTipo: {
                required: "Selecione o tipo."
            },
            cd_unidade: {
                required: "Selecione o permissor."
            },
            idInterface:{
                required: "Selecione a interface."
            },
            idTaxaDigital:{
                required: "Selecione a velocidade."
            },
            idOper:{
                required: "Selecione o ponto A (Operadora)."
            },
            idCliente:{
                required: "Selecione o ponto B (Velocidade)."
            }
	   }
   });
   
   $(this).carregaOperadora('N');
   //$(this).carregaOperadora($("#cd_unidade").val());

    if($("input[name='id']").val() == ''){
        // Oculta combo contrato
        $(".contrato").hide();
        // Oculta combo autocomplete
        $(".select2-selection__rendered").css("backgroundColor", "#eee");
        // Oculta div de dados novo cliente
        $(".novo-cliente").hide();
    }
    
    $(".contrato").hide();
    $(".novo-cliente").hide();
    $(".select2-selection__rendered").css("backgroundColor", "#eee");
    
   // Ativação ou Darkfiber
   if($("#idViabTipo").val() == '1' || $("#idViabTipo").val() == '4'){
    
    $(".geral.maskProt").hide();
    $("#idContrato").val('');
    $(".contrato").hide();
    $(".select2-selection__rendered").css("backgroundColor", "white");
    primeiroDesbloqueio();
    segundoDesbloqueio();
   }
    
    // Upgrade, downgrade
    if($("#idViabTipo").val() == '2' || $("#idViabTipo").val() == '3'){
        segundoDesbloqueio();
    }
    
    //Upgrade, downgrade, mudança de endereço
    if($("#idViabTipo").val() == '2' || $("#idViabTipo").val() == '3' || $("#idViabTipo").val() == '5'){
        $(".contrato").show();
    }
    
    // Mudança de endereço
    if($("#idViabTipo").val() == '5'){
        $("#titulo_endereco").html('MUDANÇA DE ENDEREÇO');
        $("#nome_cliente").hide();
        $(".novo-cliente").show();
        segundoBloqueio();
        obrigaEndereco();
        
    }
       
    // Habilitar deletar celular
    $(this).deletaTelefone();
    
    $(".telefone").mask("(00)0000-0000#");
    $(".cnpj").mask("99.999.999/9999-99");
    $('.cep').mask('00000-000');
   
   $("#btn").click(function(){
        if($( "#frm-salvar" ).valid()){
            $(this).carregando('Salvando e enviando e-mail');
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