<?php
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url($this->session->userdata('indexPHP').'home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Ficha edifica&ccedil;&atilde;o</li>
                </ol>
                <div id="divMain">
                    <div id="existeEndereco" class="alert alert-warning" role="alert">
                    </div>
                    <div id="feedback" class="alert alert-warning" role="alert">
                        <strong>Pesquise o endere&ccedil;o correto (Rua, avenida, etc) no site dos Correios <a target="_blank" href="http://www.correios.com.br">Correios</a></strong>
                    </div>
                    <?php   
                    #$data = "2016-04-09"; 
                    #echo date('Y-m-d', strtotime("+2 days",strtotime($data))); 
                    #echo date('N', strtotime(date('2016-04-05')));
                        $anexoArquivo = '';
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'frm-salvar');
                        echo form_open_multipart($this->session->userdata('indexPHP').'telecom/edificacao/salvarEdificacao',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha edifica&ccedil;&atilde;o<a href='".base_url($this->session->userdata('indexPHP').'telecom/edificacao/pesqEdificacao')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Controle', 'controle');
                        			$data = array('name'=>'controle', 'value'=>$controle,'id'=>'controle', 'readonly'=>'readonly', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '');		
                            		foreach($permissor as $per){
                      		            
                                        if($per->permissor != ''){                                        
                          			       $options[$per->cd_unidade] = $per->permissor.' - '.htmlentities($per->nome);
                                        }                                      
                            		}	
                            		echo form_label('Permissor<span class="obrigatorio">*</span>', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '', 'NOVO' => 'Novo assinante', 'ENDERECO' => 'Mudan&ccedil;a Endere&ccedil;o');		
                            		echo form_label('Origem<span class="obrigatorio">*</span>', 'origem');
                            		echo form_dropdown('origem', $options, $origem, 'id="origem" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Contrato<span class="obrigatorio">*</span>', 'contrato');
                        			$data = array('name'=>'contrato', 'value'=>$contrato,'id'=>'contrato', 'placeholder'=>'Informe o contrato', 'style' =>'width:80%; display: inline', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '<span id="pesq" style="margin-left:10px; cursor: pointer" class="glyphicon glyphicon-search" aria-hidden="true"></span>';
                                    echo '</div>';
                                    
                                    echo '<div id="divNode" class="col-md-3">';
                                    echo '<div id="maskProtecaoUf"></div>';
                                    $options = array('' => '');	
                                    foreach($nodes as $nod){
                                        $options[$nod->id] = $nod->descricao;
                                    }	
                            		echo form_label('Node<span class="obrigatorio">*</span>', 'idNode');
                            		echo form_dropdown('idNode', $options, $idNode, 'id="idNode" readonly="readonly" class="insertNode form-control"');
                                    echo '</div>';
                                
                                    echo '<div class="col-md-2">';
                                    echo form_label('In&iacute;cio<span class="obrigatorio">*</span>', 'inicio');
                        			$data = array('name'=>'inicio', 'value'=>$this->util->formataData($inicio, 'BR'),'id'=>'inicio', 'placeholder'=>'Informe o in&iacute;cio', 'class'=>'data form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Previs&atilde;o<span class="obrigatorio">*</span>', 'previsao');
                        			$data = array('name'=>'previsao', 'value'=>$this->util->formataData($previsao,'BR'),'id'=>'previsao', 'readonly'=>'readonly', 'placeholder'=>'Informe a previs&atilde;o', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';

                                    echo '<div class="col-md-6">';
                                    echo form_label('Nome<span class="obrigatorio">*</span>', 'nome');
                        			$data = array('name'=>'nome', 'value'=>$nome,'id'=>'nome', 'readonly' => 'readonly', 'placeholder'=>'Informe o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Telefone', 'telefone');
                        			$data = array('name'=>'telefone', 'value'=>$telefone,'id'=>'telefone', 'placeholder'=>'Informe o telefone', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Celular', 'celular');
                        			$data = array('name'=>'celular', 'value'=>$celular,'id'=>'celular', 'placeholder'=>'Informe o telefone', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('CEP', 'cep');
                        			$data = array('name'=>'cep', 'value'=>$cep,'id'=>'cep', 'readonly' => 'readonly', 'placeholder'=>'Informe o CEP', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Endere&ccedil;o<span class="obrigatorio">*</span>', 'endereco');
                        			$data = array('name'=>'endereco', 'value'=>$endereco,'id'=>'endereco', 'readonly' => 'readonly', 'placeholder'=>'Informe o endere&ccedil;o', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('Cidade<span class="obrigatorio">*</span>', 'cidade');
                        			$data = array('name'=>'cidade', 'value'=>$cidade,'id'=>'cidade', 'readonly' => 'readonly', 'placeholder'=>'Informe a cidade', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo '<div id="maskProtecaoUf"></div>';
                                    $options = array('' => '');		
                                    foreach($estado as $est){
                                        $options[$est->cd_estado] = $est->sigla_estado;
                                    }
                            		echo form_label('Estado<span class="obrigatorio">*</span>', 'cd_estado');
                            		echo form_dropdown('cd_estado', $options, $cd_estado, 'readonly="readonly" id="cd_estado" class="bloq form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Bairro<span class="obrigatorio">*</span>', 'bairro');
                        			$data = array('name'=>'bairro', 'value'=>$bairro,'id'=>'bairro' , 'readonly' => 'readonly', 'placeholder'=>'Informe o bairro', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    echo form_label('N&uacute;mero<span class="obrigatorio">*</span>', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$numero,'id'=>'numero', 'readonly' => 'readonly', 'placeholder'=>'Informe o n&uacute;mero', 'class'=>'bloq form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Complemento', 'complemento');
                        			$data = array('name'=>'complemento', 'value'=>$complemento,'id'=>'complemento', /*'readonly' => 'readonly',*/ 'placeholder'=>'Informe o complemento', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';   
                                echo '</div>';
                                
                                if($tem === true){
                                if(in_array($perMudarStatus, $this->session->userdata('permissoes'))){
                                echo '<div class="row">';  
                                    echo '<div class="col-md-2">';
                                        $options = array('SIM' => 'Sim', 'NAO' => 'N&atilde;o');
                                		echo form_label('conclu&iacute;do', 'concluido');
                                		echo form_dropdown('concluido', $options, $concluido, 'id="concluido" class="bloq form-control"');
                                    echo '</div>';
                                    echo '<div class="col-md-2">';
                                    echo form_label('Data Conclus&atilde;o', 'conclusao');
                        			$data = array('name'=>'conclusao', 'value'=>$this->util->formataData($conclusao, 'BR'),'id'=>'conclusao', 'placeholder'=>'Informe a data', 'class'=>'data form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    echo '<div class="col-md-3">';
                                		$options = array(''=>'');
                                        foreach($aval as $av){
                                		  $options[$av->id] = htmlentities($av->nome);
                                		}
                                        echo form_label('Aval', 'idAval');
                                		echo form_dropdown('idAval', $options, $idAval, 'id="idAval" class="bloq form-control"');
                                    echo '</div>';
                                    echo '<div class="col-md-2">';
                                        echo '<div style="margin-bottom: 10px"><strong>Arquivo</strong></div>';
                                        if($anexo){
                                            #$anexo = explode('.', $anexo);
                                            #$anexoArquivo = $anexo[0].'.'.strtolower($anexo[1]);
                                            $anexoArquivo = $anexo;
                                        }
                                        
                                        echo '<a href="'.base_url($dirDownload).'/'.$anexoArquivo.'" target="_blank">'.$anexoArquivo.'</a>';
                                    echo '</div>';
                                    echo '<div id="file" class="col-md-3">';
                                        echo form_label('Arquivo (Pdf)', 'anexo');
                            			$data = array('name'=>'anexo','id'=>'anexo', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
                            			echo form_upload($data);
                                    echo '</div>';
                                echo '</div>'; 
                                }   
                                }                                                                 
                                
                                echo '<div class="row">';    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Refer&ecirc;ncia', 'referencia');
                                    /*$data = array(
                                          'name'        => 'referencia',
                                          'id'          => 'referencia',
                                          'value'       => $referencia,
                                          'rows'        => '5',
                                          'cols'        => '10',
                                          'class'       => 'form-control'
                                          //'style'       => 'width:50%',
                                        );
                                      echo form_textarea($data);*/
                                      echo '<textarea id="referencia" name="referencia" rows="5" cols="10" class="form-control">'.utf8_decode($referencia).'</textarea>';
                                    echo '</div>';      
                                    
                                    echo '<div class="col-md-6">';
                                    echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                                    /*$data = array(
                                          'name'        => 'observacao',
                                          'id'          => 'observacao',
                                          'value'       => utf8_encode($observacao),
                                          'rows'        => '5',
                                          'cols'        => '10',
                                          'class'       => 'form-control'
                                          //'style'       => 'width:50%',
                                        );
                                      echo form_textarea($data);*/
                                      echo '<textarea id="observacao" name="observacao" rows="5" cols="10" class="form-control">'.utf8_decode($observacao).'</textarea>';
                                    echo '</div>';   
                           
                                echo '</div>';
                                                              
                                echo '<div class="actions">';
                                
                                echo form_hidden('id', $id);
                                echo form_hidden('anexoOrigem', $anexoArquivo);
                                
                                echo form_submit("btn","Salvar", 'id="btn" class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">

$.fn.existeEndereco = function() {  
    $.ajax({
      type: "POST",
      url: '<?php echo base_url().$this->session->userdata('indexPHP'); ?>telecom/ajaxTelecom/existeEndereco',
      data: {
        <?php if($id){ ?>
        id: <?php echo $id;?>,
        <?php } ?>
        endereco: $("#endereco").val(),
        cidade: $("#cidade").val(),
        cd_estado: $("#cd_estado").val(),
        bairro: $("#bairro").val(),
        numero: $("#numero").val()
      },
      dataType: "json",
      /*error: function(res) {
        $("#resMarcar").html('<span>Erro de execução</span>');
      },*/
      success: function(res) {
        if(res.length > 0){
            
            var links = '<strong>O endere&ccedil;o informado j&aacute; foi cadastrado no sistema:</strong><br><br>';
            
            $.each( res, function() {
              links += '<strong><a target="_blank" href="<?php echo base_url($this->session->userdata('indexPHP').'telecom/edificacao/fichaEdificacao'); ?>/'+this.id+'">Contrato - '+this.contrato+'</a></strong><br>';
            });
            $("#existeEndereco").html(links);
            $("#existeEndereco").show();
        }else{
            $("#existeEndereco").html('');
            $("#existeEndereco").hide();
        }
      }
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

// Busca o endereço de acordo com o CEP
$("#cep").keyup(function(){

    if($(this).val().length == 9){ // CEP Completo
        $.ajax({
              type: "POST",
              //url: '<?php echo base_url().$this->session->userdata('indexPHP'); ?>ajax/pegaEndereco',
              url: 'http://clareslab.com.br/ws/cep/json/'+$(this).val(),              
              data: {
                cep: $(this).val()
              },
              dataType: "json",
              success: function(res) { 
                $("#endereco").val(res.endereco);
                $("#bairro").val(res.bairro);
                $("#cidade").val(res.cidade);
                
                $('#cd_estado option').each(function(){
                   var item = $(this).text();
                   if(item == res.uf){
                      $(this).prop('selected',true);
                   }
                });
                
                if(res == 0){ // Não achou endereço
                    $("#endereco").val('');
                    $("#bairro").val('');
                    $("#cidade").val('');
                    $("#cd_estado option").prop("selected", false);
                }
              }
            }); 
    }
    
    if($(this).val().length == 0){ // Vazio
        $("#endereco").val('');
        $("#bairro").val('');
        $("#cidade").val('');
        $("#cd_estado option").prop("selected", false);
    }
    
}); 
/*
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}
*/



$(document).ready(function(){ 
    
    $("#existeEndereco").hide();
    
    <?php if($id){ ?>
    $("#divNode").css('display', 'block');
    <?php }else{ ?>
    $("#divNode").css('display', 'none');
    <?php } ?>
    
    if($("#origem").val() == 'ENDERECO'){
        $("#cd_estado").prop('readonly', false).css({"cursor": "default","backgroundColor": "white"}).prev().prev().hide();
        $('.bloq').prop('readonly', false); 
    }else{
        $('.bloq').prop('readonly', true);
    }
    
    
    $('#cep').mask('00000-000');
    $('.data').mask('00/00/0000');
    $("#telefone").mask('(00)0000-0000');
    $("#celular").mask('(00)0000-0000#');
    
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
    
    $("#origem").change(function(){
        if($("#cd_unidade").val() == ''){
            $(this).val('');
            alert('Selecione o permissor');
            $("#cd_unidade").focus();
        }else{
            apagarDadosPesquisa();
        }
        /*
        if(($(this).val() == 'ENDERECO' && $("#cd_unidade").val() != ''){
            $("#cd_estado").prop('readonly', false).css({"cursor": "default","backgroundColor": "white"}).prev().prev().hide();
            $('.bloq').prop('readonly', false); 
        }
        
        if($(this).val() == 'NOVO' && $("#cd_unidade").val() != ''){
            $("#cd_estado").prop('readonly', true).css({"cursor": "not-allowed","backgroundColor": "#eee"}).prev().prev().show();
            $('.bloq').prop('readonly', true); 
        }
        */
    });
    
    $("#cd_unidade").change(function(){
       if($("#cd_unidade").val() == ''){
            apagarDadosPesquisa();
       } 
    });
    
    $("#concluido").change(function(){
        if($(this).val() == 'NAO'){
            $("#conclusao").val('');
        }
    });
    
    function apagarDadosPesquisa(){
        
        $("#idNode option").prop("selected", false);
        $("#nome").val('');
        $("#telefone").val('');
        $("#celular").val('');
        $("#cep").val('');
        $("#endereco").val('');
        $("#cidade").val('');
        $("#cd_estado option").prop("selected", false);
        $("#bairro").val('');
        $("#numero").val('');
        $("#complemento").val('');
        $("#referencia").val('');
        
    }
    
    $("#pesq").click(function(){
        
        if($("#cd_unidade").val() == '' || $("#origem").val() == '' || $("#contrato").val() == ''){
            alert('Selecione, "Permissor", "Origem" e informe o "Contrato".');
            apagarDadosPesquisa();
        }else{
            
            var urlParte = '';
            
            if($("#origem").val() == 'NOVO'){
                urlParte = 'dadosNovoAssinante';
            }else{
                urlParte = 'dadosAssinanteMudancaEndereco';
            }
            
            var perm = $("#cd_unidade option:selected").text().split(" - ");
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url().$this->session->userdata('indexPHP'); ?>telecom/ajaxTelecom/'+urlParte,
              data: {
                permissor: perm[0],
                assinante: $("#contrato").val()
              },
              dataType: "json",
              /*error: function(res) {
                $("#resMarcar").html('<span>Erro de execução</span>');
              },*/
              success: function(res) {

                if(res.length != 0){
                    
                    var achouNode = false;
                    
                    $('#idNode option').each(function(){
                       var item = $(this).text();
                       if(item == res.DSC_NODE){
                            achouNode = true;
                            $(this).prop('selected',true);
                       }
                    });
                    
                    if(achouNode == false){
                        
                        $("#divNode").css('display', 'none');
                        
                        alert('<?php echo utf8_encode("Node não encontrado! Cadastre o novo node, por favor."); ?>\nPermissor: '+res.PERMISSOR+' Node: '+res.DSC_NODE);
                        apagarDadosPesquisa();
                    }else{
                        
                        $("#divNode").css('display', 'block');
                        
                        if(res.TELEFONE === null){
                            var telefone = '';
                        }else{
                            var telefone = '(00)'+res.TELEFONE.replace(/([0-9]{4})([0-9]{4})/, '$1-$2'); 
                            if(res.TELEFONE.length == 9){ 
                                telefone = '(00)'+res.TELEFONE.replace(/([0-9]{1})([0-9]{4})([0-9]{4})/, '$2-$3'); 
                            }
                        }
                        
                        if(res.CELULAR === null){
                            var celular = '';
                        }else{
                            if(res.CELULAR.length == 8){
                                var celular = '(00)'+res.CELULAR.replace(/([0-9]{4})([0-9]{4})/, '$1-$2');    
                            }else{
                                var celular = '(00)'+res.CELULAR.replace(/([0-9]{4})([0-9]{5})/, '$1-$2');
                            }
                        }
                        
                        $("#nome").val(res.NOME);
                        $("#telefone").val(telefone);
                        $("#celular").val(celular);
                        $("#cep").val(res.CEP.replace(/([0-9]{5})([0-9]{3})/, '$1-$2'));
                        $("#endereco").val(res.ENDERECO);
                        $("#cidade").val(res.CIDADE);
                        $('#cd_estado option').each(function(){
                           var item = $(this).text();
                           if(item == res.UF){
                              $(this).prop('selected',true);
                           }
                        });
                        $("#bairro").val(res.BAIRRO);
                        $("#numero").val(res.NUMERO_ENDERECO);
                        $("#complemento").val(res.COMPLEMENTO);
                        $("#referencia").val(res.REFERENCIA);
                        
                        $(this).existeEndereco();
                    }
                    
                    $("#cd_estado").prop('readonly', false).css({"cursor": "default","backgroundColor": "white"}).prev().prev().hide();
                    $('.bloq').prop('readonly', false); 
                    
                }else{
                    
                    $("#divNode").css('display', 'none');
                    
                    alert('Nada encontrado! Verifique o PERMISSOR, ORIGEM ou CONTRATO digitado.');
                    
                    apagarDadosPesquisa();
                    
                    $("#cd_estado").prop('readonly', true).css({"cursor": "not-allowed","backgroundColor": "#eee"}).prev().prev().show();
                    $('.bloq').prop('readonly', true); 
                    
                }
                
              }
            });
        }
        
    });
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            cd_unidade: {
                required: true
            },
			idNode: {
                required: true
            },
            inicio: {
                required: true
			},
            origem: {
                required: true
			},
            contrato: {
                required: true
			},
            nome: {
                required: true
			},
            /*telefone: {
                required: true
			},
            celular: {
                required: true
			},
            cep: {
                required: true
			},*/
            conclusao: {
                required: {
                    depends: function(element) {
                        return ($('#concluido').val() == 'SIM');
                    }
                }
            },
            idAval: {
                required: {
                    depends: function(element) {
                        return ($('#concluido').val() == 'SIM');
                    }
                }
            },
            endereco: {
                required: true
			},
            cidade: {
                required: true
			},
            cd_estado: {
                required: true
			},
            bairro: {
                required: true
			},
            numero: {
                required: true
			}
		},
		messages: {
            cd_unidade: {
                required: "Selecione o permissor."
            },
			idNode: {
                required: "Selecione o node."
            },
            inicio: {
                required: "Informe a data"
            },
            origem: {
                required: "Selecione a origem."
            },
            contrato: {
                required: "Informe o contrato."
            },
            nome: {
                required: "Informe o nome."
            },
            /*telefone: {
                required: "Informe o telefone."
            },
            celular: {
                required: "Informe o celular."
            },
            cep: {
                required: "Informe o CEP."
            },*/
            conclusao: {
                required: "Informe a conclus&atilde;o."
            },
            idAval: {
                required: "Selecione o aval."
            },
            endereco: {
                required: "Informe o endereco."
            },
            cidade: {
                required: "Informe a cidade."
            },
            cd_estado: {
                required: "Informe o estado."
            },
            bairro: {
                required: "Informe o bairro."
            },
            numero: {
                required: "Informe o numero."
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