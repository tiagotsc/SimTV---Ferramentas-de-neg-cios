<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<link href="<?php echo base_url("assets/js/select2/css/select2-personalizado.min.css"); ?>" rel="stylesheet" />
<script src="<?php echo base_url("assets/js/select2/js/select2.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.blockui/jquery.block.ui.js') ?>"></script>


    <!-- INÍCIO Modal Pendência registro -->
    <div class="modal fade" id="pendencia" tabindex="-1" role="dialog" aria-labelledby="pendencia" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Informe a pendência para realização de vistoria</h4>
                </div>
                <div class="modal-body">
                    <div id="feedbackGravacao"></div>
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open("",$data);
                        echo form_label('Qual a pendência?', 'pergunta');
                        echo '<textarea id="pergunta" name="pergunta" class="form-control"></textarea>';
                        
                        echo '<div style="max-height: 300px;overflow: auto" class="row"><div id="todasPends" class="col-md-12"></div></div>';
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="idViabPend" name="idViabPend" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" id="btPend" class="btn btn-primary">Abrir pendência</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga Pendência -->


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
                        echo form_open_multipart($modulo.'/'.$controller.'/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoVoltar = "<a href='".base_url($modulo.'/'.$controller.'/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>";
                    		echo form_fieldset("Ficha ".$assunto.$botaoVoltar, $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-6">';
                                    $options = array('' => '');	
                                    foreach($vistoriasPendentes as $vistPend){
                                       $options[$vistPend->id] = htmlentities($vistPend->n_solicitacao); 
                                    }	
                            		echo form_label('Viabilidade<span class="obrigatorio">*</span>', 'idViab');
                            		echo form_dropdown('idViab', $options, $idViab, 'id="idViab" class="form-control"');
                                    echo '</div>'; 
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '', 'S' => 'Sim', 'N' => 'Não');		
                            		echo form_label('Viável<span class="obrigatorio">*</span>', 'viavel');
                            		echo form_dropdown('viavel', $options, $viavel, 'id="viavel" class="form-control"');
                                    echo '</div>'; 
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '', 'S' => 'Sim', 'N' => 'Não');		
                            		echo form_label('Endereço encontrado?<span class="obrigatorio">*</span>', 'end_enco');
                            		echo form_dropdown('end_enco', $options, $end_enco, 'id="end_enco" class="form-control"');
                                    echo '</div>'; 
                                    
                                echo '</div>'; 
                                
                                echo '<div id="dadosViabilidade" class="row"></div>';
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong id="titulo_endereco">CLIENTE HFC</strong></div>';
                                echo '</div>';
                                
                                echo '<div class="row">'; 
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Cabo (Mts)', 'cabo');
                        			$data = array('name'=>'cabo', 'value'=>$cabo,'id'=>'cabo', 'placeholder'=>'Informe o cabo', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Cordoalha (Mts)', 'cordoalha');
                        			$data = array('name'=>'cordoalha', 'value'=>$cordoalha,'id'=>'cordoalha', 'placeholder'=>'Informe o cordoalha', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Canalização (Mts)', 'canalizacao');
                        			$data = array('name'=>'canalizacao', 'value'=>$canalizacao,'id'=>'canalizacao', 'placeholder'=>'Informe o canalizacao', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                        echo '<div style="margin-bottom: 10px"><strong>Arquivo</strong></div>';
                                        if($anexo){
                                            $anexoArquivo = $anexo;
                                        }
                                        echo '<a href="'.base_url($dirDownload).'/'.$anexoArquivo.'" target="_blank">'.$anexoArquivo.'</a>';
                                    echo '</div>';
                                    
                                    echo '<div id="file" class="col-md-6">';
                                        echo form_label('Arquivo', 'anexo');
                            			$data = array('name'=>'anexo','id'=>'anexo', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
                            			echo form_upload($data);
                                    echo '</div>';

                                echo '</div>';
                                
                                echo '<div class="row">';
                                    echo '<div class="col-md-12 cabecalhoDivisor text-center"><strong id="titulo_endereco">PERCURSO FIBRA</strong></div>';
                                echo '</div>';
                                
                                echo '<div class="row">';
                                
                                    echo '<div id="divNode" class="col-md-4">';
                                    $options = array('' => '');		
                            		echo form_label('Node', 'idNode');
                            		echo form_dropdown('idNode', $options, '', 'id="idNode" class="form-control"');
                                    echo '</div>'; 
                                    
                                    echo '<div id="div_node_distancia" class="col-md-4">';
                                    echo form_label('Node distância', 'node_distancia');
                        			$data = array('name'=>'node_distancia', 'value'=>$node_distancia,'id'=>'node_distancia', 'maxlength' => '50', 'placeholder'=>'Informe a distância', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-12">';
                                    echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                                      echo '<textarea id="observacao" name="observacao" rows="5" cols="10" class="form-control">'.utf8_decode($observacao).'</textarea>';
                                    echo '</div>'; 
                                
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                echo form_hidden('idContrato', $idContrato);
                                echo form_hidden('idViab_backup', $idViab);
                                echo form_hidden('anexoOrigem', $anexoArquivo);
                                echo form_hidden('cd_unidade', $cd_unidade);
                                echo form_hidden('controle', '');                                
                                
                                echo form_submit("btn","Salvar", 'id="btn" class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$("#btPend").click(function(){
    
    if($.trim($("#pergunta").val()) == ''){
        alert('Preencha a pergunta para poder cria-la, por favor.');
    }else{

        $.post( "<?php echo base_url('tcom-viabilidade-pend/ajaxViabilidadePend/salvarPergunta'); ?>", { idviab: $("#idViabPend").val(), pergunta: $("#pergunta").val() }, function( data ) {
          
          if(data == 'OK'){
            var msg = '<div class="alert alert-success" role="alert"><strong>Pendência enviada com sucesso!</strong></div>';
          }else if(data == 'ERRO'){
            var msg = '<div class="alert alert-warning" role="alert"><strong>Erro ao enviar pergunta, caso o erro persista comunique o administrador.</strong></div>';
          }else{
            var msg = '<div class="alert alert-danger" role="alert"><strong>Você não tem permissão!</strong></div>';
          }
          
          $("#feedbackGravacao").html(msg);
          
          $("#pergunta").val('');
          
          setInterval(function(){ $("#feedbackGravacao").html(''); }, 3000);
          $(this).pegaPendencias();
          
        }, "json");
        
    } 
   
});

$.fn.pegaPendencias = function() {
    
    $("#todasPends").html('');

    $.ajax({
      type: "POST",
      url: '<?php echo base_url('tcom-viabilidade-pend/ajaxViabilidadePend/pendenciasViabilidade'); ?>',          
      data: {
        idviab: $("#idViab").val()
      },
      dataType: "json",
      /*error: function(res) {
        $("#resMarcar").html('<span>Erro de execução</span>');
      },*/
      success: function(res) { 
        if(res){
            
            var classTable = '';
            var conteudo = '<table class="table">';
            
            var cont = 0;
            $.each(res, function() {
                
                if(cont % 2 == 0){ 
                    classTable = 'fundoCinzaClaro';
                }else{
                    classTable = '';
                }
                
                conteudo += '<tr class="'+classTable+'">';
                conteudo += '<td><strong>Autor Pergunta: </strong>'+this.usuario_pergunta+'</td>';
                conteudo += '<td><strong>Data / Hora: </strong>'+this.data_cadastro_pergunta+'</td>';
                conteudo += '</tr>';
                conteudo += '<tr class="'+classTable+'">';
                conteudo += '<td><strong>Status: </strong>'+this.status+'</td>';
                conteudo += '<td></td>';
                conteudo += '</tr>';
                conteudo += '<tr class="'+classTable+'">';
                conteudo += '<td colspan="2"><p class="text-left"><strong>Pergunta:</strong><br>'+this.pergunta+'</p></td>';
                conteudo += '</tr>';
                
                if(this.data_cadastro_resposta){
                    
                    conteudo += '<tr class="'+classTable+'">';
                    conteudo += '<td><strong>Autor Resposta: </strong>'+this.usuario_resposta+'</td>';
                    conteudo += '<td><strong>Data / Hora: </strong>'+this.data_cadastro_resposta+'</td>';
                    conteudo += '</tr>';
                    conteudo += '<tr class="'+classTable+'">';
                    conteudo += '<td colspan="2"><p class="text-left">'+this.resposta+'</p></td>';
                    conteudo += '</tr>';
                    
                }
                
                cont++;
                
            });
            
            conteudo += '</table>';
            
            $("#todasPends").html(conteudo);
            
        }else{
            $("#todasPends").html('');
        }
        
      }
    }); 
    
}

$.fn.carregaHistorico = function() {
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url().$modulo; ?>/ajaxViabilidadeResp/comboNode',              
      data: {
        unidade: unidade
      },
      dataType: "json",
      /*error: function(res) {
        $("#resMarcar").html('<span>Erro de execução</span>');
      },*/
      success: function(res) { 
        if(res.length > 0){
            
            $("#idNode").html('');
            $("#idNode").append('<option value=""></option>');

            $.each(res, function() {
                $("#idNode").append('<option value="'+ this.id +'">'+ this.descricao +' ('+ this.distancia +')</option>');
            });
            
            $("#divNode").show();
            
        }else{
            $("#idNode").val('');
            $("#divNode").hide();
        }
      }
    }); 
        
};

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

$("#idNode").change(function(){
   if($(this).val() != ''){
    $("#node_distancia").val($("#idNode option:selected").text().replace(")","").split("(")[1]);
    $("#div_node_distancia").show();
   }else{
    $("#node_distancia").val('');
   }   
});

$("#idViab").change(function(){
   
   if($(this).val() != ''){

    dadosViabilidade();
    $(this).pegaPendencias();
    
   }else{
        $("input[name='cd_unidade']").val('');
        $("#dadosViabilidade").html('');
        $("#idNode").val('');
        $("#divNode").hide();
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
                
                if(res['viabilidade'].idContrato !== null){
                    $("input[name='idContrato']").val(res['viabilidade'].idContrato); 
                }else{
                    $("input[name='idContrato']").val(''); 
                }
                
                $("input[name='controle']").val(res['viabilidade'].controle);                 
                                
                $("input[name='cd_unidade']").val(res['viabilidade'].cd_unidade);
                
                if(res['viabilidade'].id_tipo == 5){
                    var endPontoB = 'mdEnd';
                }else{
                    var endPontoB = 'clienteEnd';
                }
                
                $("#idViabPend").val($('#idViab').val());
                
                var linkPend = '';
                <?php if(in_array($perPendCadPerg, $this->session->userdata('permissoes'))){ ?>
                var linkPend = '<a href="#" data-toggle="modal" data-target="#pendencia">Informar ou visualizar pendência para realização de vistoria</a>';
                <?php } ?>
                
                var conteudo = '<div class="col-md-12">'+linkPend+'</div>';
                conteudo += '<div class="col-md-12"><table id="tbajax" class="table table-bordered">';
                conteudo += '<tr>';
                conteudo += '<th>PONTO A</th><th>PONTO B</th>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                
                if(res['viabilidade'].designacao !== null){
                    var base_url = '<?php echo base_url('tcom-contrato/contrato/imprimir'); ?>/';
                    var designacao = 'Designacão: <a target="_blank" href="'+base_url+res['viabilidade'].idContrato+'">'+res['viabilidade'].designacao+'</a>';
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
                conteudo += '<td>Endereço: '+ res['operadoraInst'].endereco +', '+ res['operadoraInst'].numero +'</td>';
                conteudo += '<td>Endereço: '+ res[endPontoB].endereco +', '+res[endPontoB].numero+'</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '<td>Cidade: '+ res['operadoraInst'].nome_estado +'</td>';
                conteudo += '<td>Cidade: '+ res[endPontoB].nome_estado +'</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '<td>UF: '+ res['operadoraInst'].sigla_estado +'</td>';
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

function comboNodeCarrega(unidade){
        
        $("#divNode").hide();
        
        $.ajax({
          type: "POST",
          url: '<?php echo base_url().$modulo; ?>/ajaxViabilidadeResp/comboNode',              
          data: {
            unidade: unidade
          },
          dataType: "json",
          /*error: function(res) {
            $("#resMarcar").html('<span>Erro de execução</span>');
          },*/
          success: function(res) { 
            if(res.length > 0){
                
                $("#idNode").html('');
                $("#idNode").append('<option value=""></option>');
    
                $.each(res, function() {
                    $("#idNode").append('<option value="'+ this.id +'">'+ this.descricao +' ('+ this.distancia +')</option>');
                });
                
                $("#divNode").show();
                
            }else{
                $("#idNode").val('');
                $("#divNode").hide();
            }
          }
        }); 
}

$(document).ready(function(){ 
    
    $('#cabo').mask('000####');
    $('#cordoalha').mask('000####');
    $('#canalizacao').mask('000####');
    
    if($("input[name='id']").val() != ''){
        if($("#node_distancia").val() != ''){
            $("#div_node_distancia").show();
        }else{
            $("#div_node_distancia").hide();
        }
        $("#divNode").show();
        dadosViabilidade();
    }else{
        $("#div_node_distancia").hide();
        $("#divNode").hide();
    }
    
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