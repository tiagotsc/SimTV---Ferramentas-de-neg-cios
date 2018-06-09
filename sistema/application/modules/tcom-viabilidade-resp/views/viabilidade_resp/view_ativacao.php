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
                    #echo APPPATH;
                        $anexoArquivo = '';
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                        echo form_open_multipart($modulo.'/ativacao/salvarAtivacao',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoVoltar = "<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>";
                    		echo form_fieldset("Ficha Ativação".$botaoVoltar, $attributes);
              		            
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-8">';
                                    $options = array('' => '');	
                                    foreach($pendenciaAtivacao as $pendAtiva){
                                       $options[$pendAtiva->id] = htmlentities($pendAtiva->numero)/*htmlentities($pendAtiva->n_solicitacao)*/; 
                                    }	
                            		echo form_label('Contrato<span class="obrigatorio">*</span>', 'idViab');
                            		echo form_dropdown('idViab', $options, '', 'id="idViab" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'S' => 'Sim', 'N'=>'Não');	
                                    
                            		echo form_label('Deseja ativar?<span class="obrigatorio">*</span>', 'pergunta_ativacao');
                            		echo form_dropdown('pergunta_ativacao', $options, '', 'id="pergunta_ativacao" class="form-control"');
                                    echo '</div>'; 
                               
                               echo '</div>';  
                               
                               echo '<div id="dadosViabilidade" class="row"></div>';
                               
                               echo '<div class="row">';
                                   echo '<div id="file" class="iniciaOculta col-md-6">';
                                        echo form_label('Arquivo', 'anexo');
                            			$data = array('name'=>'anexo','id'=>'anexo', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
                            			echo form_upload($data);
                                    echo '</div>';
                                echo '</div>';
                               
                               echo '<div id="div_equip" class="iniciaOculta row">';
                               
                                    echo '<div class="col-md-12">';
                                    $options = array(''=>'');	
                                    foreach($equipamentos as $equip){
                                        $options[$equip->id] = htmlentities($equip->marca.' - '.$equip->modelo.' - '.$equip->identificacao);
                                    }
                            		echo form_label('Selecione o equipamento a ser adicionado<span class="obrigatorio">*</span>', 'equipamento');
                            		echo form_dropdown('equipamento', $options, '', 'id="equipamento" class="form-control"');
                                    echo '</div>'; 
                                    
                                    echo '<div class="col-md-2">';
                                    echo '<br><div class="tabela-div"><a id="add-equip" href="#"><spa class="glyphicon glyphicon-plus"></spa><strong>Adicionar</strong></a></div>';
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-12">';
                                    echo '<table id="equip-adicionados" class="table zebra">';
                                    echo '<tr>';
                                    echo '<th colspan="2">';
                                    echo 'Equipamentos Adicionados';
                                    echo '</th>';
                                    echo '</tr>';
                                    echo '</table>';
                                    echo '</div>';
                                    
                               echo '</div>';
                                 
                               echo '<div class="row">'; 
                                    
                                    echo '<div id="div_textarea" class="iniciaOculta col-md-12">';
                                    echo form_label('Observa&ccedil;&atilde;o', 'obs_ativacao');
                                      echo '<textarea id="obs_ativacao" name="obs_ativacao" rows="5" cols="10" class="form-control"></textarea>';
                                    echo '</div>'; 
                                    
                                    echo '<div class="col-md-12 actions">';
                                    echo form_submit("btn","Ativar", 'id="btn" class="btn iniciaOculta btn-primary pull-right"');
                                    echo '</div>';
                               
                               echo '</div>'; 
                               
                               echo '<div class="actions">';
                                
                               echo '<input type="hidden" id="redirect" name="redirect" value="'.$redirect.'" />';
                               echo '<input type="hidden" id="numeroContrato" name="numeroContrato" />';
                               echo '<input type="hidden" id="idTipo" name="idTipo" />'; 
                               echo '<input type="hidden" id="idContratoAnterior" name="idContratoAnterior" />';  
                               echo '<input type="hidden" id="idContrato" name="idContrato" />'; 
                               echo '<input type="hidden" id="idResposta" name="idResposta" />';  
                               echo '<input type="hidden" id="idInterface" name="idInterface" />'; 
                               echo '<input type="hidden" id="idTaxaDigital" name="idTaxaDigital" />';  
                               echo '<input type="hidden" id="qtdCircuitos" name="qtdCircuitos" />';
                               echo '<input type="hidden" id="idUnidade" name="idUnidade" />';
                               echo '<input type="hidden" id="idCircuito" name="idCircuito" />';
                               echo '<input type="hidden" id="idCliente" name="idCliente" />';
                               echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                           
                               echo '<div class="ocultar row">';   
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Viável', 'viavel');
                        			$data = array('name'=>'viavel', 'value'=>'','id'=>'viavel', 'readonly'=>true, 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>'; 
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Endereço encontrado?', 'end_enco');
                        			$data = array('name'=>'end_enco', 'value'=>'','id'=>'end_enco', 'readonly'=>true, 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>'; 
                                    
                                echo '</div>'; 
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong id="titulo_endereco">CLIENTE HFC</strong></div>';
                                echo '</div>';
                                
                                echo '<div class="row">'; 
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Cabo (Mts)', 'cabo');
                        			$data = array('name'=>'cabo', 'value'=>'','id'=>'cabo', 'readonly'=>true, 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Cordoalha (Mts)', 'cordoalha');
                        			$data = array('name'=>'cordoalha', 'value'=>'','id'=>'cordoalha', 'readonly'=>true, 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Canalização (Mts)', 'canalizacao');
                        			$data = array('name'=>'canalizacao', 'value'=>'','id'=>'canalizacao', 'readonly'=>true, 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                        echo '<div style="margin-bottom: 10px"><strong>Arquivo</strong></div>';
                                        if($anexo){
                                            $anexoArquivo = $anexo;
                                        }
                                        echo '<span id="dadosAnexo"></span><a href="'.base_url($dirDownload).'/'.$anexoArquivo.'" target="_blank">'.$anexoArquivo.'</a>';
                                    echo '</div>';

                                echo '</div>';
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong id="titulo_endereco">PERCURSO FIBRA</strong></div>';
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Node distância', 'node_distancia');
                        			$data = array('name'=>'node_distancia', 'value'=>$node_distancia,'id'=>'node_distancia', 'readonly'=>true, 'maxlength' => '50', 'placeholder'=>'Informe a distância', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-12">';
                                    echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                                      echo '<textarea id="observacao" readonly="true" name="observacao" rows="5" cols="10" class="form-control">'.utf8_decode($observacao).'</textarea>';
                                    echo '</div>'; 
                                
                                echo '</div>';
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">
var lista = [];
$("#add-equip").click(function(event){ 
    event.preventDefault();
    
    if($("#equipamento").val() == ''){
        alert("Selecione um equipamento para adiciona-lo.");
        return false;
    }
    
    var inputTexto = $("#equipamento :selected").text();
    var inputValor = $("#equipamento :selected").val();
    

    var tableLinha = '<tr>';
        tableLinha += '<td>';
        tableLinha += '<span id="adicionadoTexto'+inputValor+'">'+inputTexto+'</span>';
        tableLinha += '<input type="hidden" class="adicionados" id="equip[]" name="equip[]" value="'+inputValor+'" />';
        tableLinha += '<input type="hidden" class="adicionados" id="equip-nome[]" name="equip-nome[]" value="'+inputTexto+'" />';
        tableLinha += '</td>';
        tableLinha += '<td>';
        tableLinha += '<a key="'+inputValor+'" class="removeEquipamento glyphicon glyphicon-remove"></a>';
        tableLinha += '</td>';
        tableLinha += '</tr>';
        
    $("#equip-adicionados").append(tableLinha);
   
    $('#equipamento option[value="'+inputValor+'"]').remove();
    $("#select2-equipamento-container").html('');
   
    var x = $(".adicionados").serializeArray();

    $.each(x, function(i, field){
        
        lista[field.value] = $("#adicionadoTexto"+field.value).html();

    });

    removeEquipmento();

});

$('#equipamento').select2({
  //placeholder: 'Selecione uma opção'
  width: '100%'
});

function removeEquipmento() {
    $(".removeEquipamento").click(function(event){
        event.preventDefault();
        
        if($('#equipamento option[value="'+$(this).attr('key')+'"]').text() == ''){
        $('#equipamento').append('<option value="'+$(this).attr('key')+'">'+lista[$(this).attr('key')]+'</option>');
        }
        $(this).parent().parent().remove();
    });
}

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
$('#idViab').select2({
  //placeholder: 'Selecione uma opção'
});


$("#idViab").change(function(){
   
   if($(this).val() != ''){

    dadosViabilidade();
    dadosRespPorViabilidade();
    
   }else{
        $("input[name='cd_unidade']").val('');
        $("#dadosViabilidade").html('');
        $("#idNode").val('');
        $("#divNode").hide();
   }
    
});

$("#pergunta_ativacao").change(function(){
   if($(this).val() != '' && $(this).val() == 'S'){
    $("#div_equip.iniciaOculta, #file.iniciaOculta, .actions .iniciaOculta, #div_textarea").show();
   }else{
    $("#div_equip.iniciaOculta, #file.iniciaOculta, .actions .iniciaOculta, #div_textarea").hide();
   }
});

function dadosViabilidade(){
        $.ajax({
          type: "POST",
          url: '<?php echo base_url().$modulo; ?>/ajaxViabilidadeResp/dadosViabilidade',              
          data: {
            id: $('#idViab').val()
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) {
            if(res != 0){
                
                if(res['viabilidade'].id_tipo == 5){
                    var endPontoB = 'mdEnd';
                }else{
                    var endPontoB = 'clienteEnd';
                }
                
                $("#idTipo").val(res['viabilidade'].id_tipo);
                $("#idInterface").val(res['viabilidade'].idInterface);
                $("#idTaxaDigital").val(res['viabilidade'].idTaxaDigital);
                $("#qtdCircuitos").val(res['viabilidade'].qtd_circuitos);
                $("#idUnidade").val(res['viabilidade'].cd_unidade);
                $("#idCircuito").val(res['viabilidade'].idCircuito);
                $("#idCliente").val(res['viabilidade'].cliente);
                                
                var conteudo = '<div class="col-md-12"><table id="tbajax" class="table table-bordered">';
                conteudo += '<tr>';
                conteudo += '<th>PONTO A</th><th>PONTO B</th>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                
                if(res['viabilidade'].designacao !== null){
                    var base_url = '<?php echo base_url('tcom-contrato/contrato/imprimir'); ?>/';
                    var designacao = 'Designação: <a target="_blank" href="'+base_url+res['viabilidade'].idContrato+'">'+res['viabilidade'].designacao+'</a>';
                }else{
                    var designacao = '';
                }
                
                conteudo += '<td>Tipo solicitação: '+res['viabilidade'].tipo+'</td>';
                conteudo += '<td>'+designacao+'</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '<td>Nome: '+ res['operadora'].titulo +'</td>';
                conteudo += '<td>Nome: '+ res['cliente'].titulo +'</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '<td>Endereço: '+ res['operadoraCob'].endereco +', '+ res['operadoraCob'].numero +'</td>';
                conteudo += '<td>Endereço: '+ res[endPontoB].endereco +', '+res[endPontoB].numero+'</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '<td>Cidade: '+ res['operadoraCob'].nome_estado +'</td>';
                conteudo += '<td>Cidade: '+ res[endPontoB].nome_estado +'</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '<td>UF: '+ res['operadoraCob'].sigla_estado +'</td>';
                conteudo += '<td>UF: '+ res[endPontoB].sigla_estado +'</td>';
                conteudo += '</tr>';
                conteudo += '</table></div>';
                $("#dadosViabilidade").html(conteudo);
                comboNodeCarrega(res['viabilidade'].cd_unidade);
            }else{
                $("input[name='controle']").val('');                 
                $("input[name='cd_unidade']").val('');
                $("#dadosViabilidade").html('');
                $("#idNode").val('');
            }
          }
        });   
}

function dadosRespPorViabilidade(){
        $.ajax({
          type: "POST",
          url: '<?php echo base_url().$modulo; ?>/ajaxViabilidadeResp/dadosViabRespPorViabilidade',              
          data: {
            id: $('#idViab').val()
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) {
            if(res != 0){
                
                if(res.idContratoAtual !== null){
                    var idContrato = res.idContratoAtual;
                    $("#idContratoAnterior").val(res.idContrato);
                }else{
                    var idContrato = res.idContrato;
                    $("#idContratoAnterior").val('');
                }
                
                $("#numeroContrato").val(res.numero);
                $("#cabo").val(res.cabo);
                $("#cordoalha").val(res.cordoalha);
                $("#canalizacao").val(res.canalizacao);
                $("#node_distancia").val(res.node_distancia);
                $("#observacao").val(res.observacao);
                $("#idContrato").val(idContrato);
                $("#idResposta").val(res.id);
                
                if(res.anexo !== null){
                    var link = '<a target="_blank" href="<?php echo base_url($dirDownload); ?>/'+res.anexo+'">'+res.anexo+'</a>';
                    $("dadosAnexo").html(link);
                }else{
                    $("dadosAnexo").html('');
                }
                
                if(res.viavel !== null){
                    if(res.viavel == 'S'){
                        $("#viavel").val('Sim');  
                    }else{
                        $("#viavel").val('Não');
                    }
                }else{
                    $("#viavel").val('');
                }
                
                if(res.end_enco !== null){
                    if(res.end_enco == 'S'){
                        $("#end_enco").val('Sim');  
                    }else{
                        $("#end_enco").val('Não');
                    }
                }else{
                    $("#end_enco").val('');
                }
                
            }else{
                $("#numeroContrato").val('');
                $("#cabo").val('');
                $("#cordoalha").val('');
                $("#canalizacao").val('');
                $("#node_distancia").val('');
                $("dadosAnexo").html('');
                $("#observacao").val('');
                $("#viavel").val('');
                $("#end_enco").val('');
                $("#idContrato").val('');
            }
          }
        });     
};

$(document).ready(function(){ 
    
    
    
    $(".ocultar, .btn, .iniciaOculta").hide();
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            idViab: {
                required: true
            },
			viavel: {
                required: true
            },
            end_enco: {
                required: true
            }
		},
		messages: {
            idViab: {
                required: "Selecione o pedido."
            },
			viavel: {
                required: "É viável?"
            },
            end_enco: {
                required: "Encontrou o endereço?"
            }
	   }
   });
   
   $("#btn").click(function(){
        if($( "#frm-salvar" ).valid()){
            $(this).carregando();
        }
   });
    
});


</script>