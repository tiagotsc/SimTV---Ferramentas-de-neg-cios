	<style>
/*		* {
			font-family: Arial;
			font-size: 12px;
		}
		table {
			border-collapse: collapse;
		}
		td, th {
			border: 1px solid #666666;
			padding:  4px;
		}
		div {
			margin: 4px;
		}
		.sort_asc:after {
			content: "?";
		}
		.sort_desc:after {
			content: "?";
		}
*/
	</style>

<?php
echo link_tag(array('href' => 'assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.min.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.css','rel' => 'stylesheet','type' => 'text/css'));
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.ui.timepicker.addon/jquery-ui-sliderAccess.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.ui.timepicker.addon/jquery-ui-timepicker-addon.js") ?>"></script>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Altera node -->
    <div  class="modal fade" id="alt-status" tabindex="-1" role="dialog" aria-labelledby="alt-status" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Mudar status</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('ura/mudarStatusNode',$data);
                        
                        echo '<div class="row">';
                        
                            echo '<div class="col-md-3">';
                            echo form_label('Node', 'alt_nome_node');
                      		$data = array('id'=>'alt_nome_node', 'name'=>'alt_nome_node', 'readonly' => 'readonly', 'class'=>'form-control');
                      		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            echo form_label('Data fim', 'alt_data_fim');
                      		$data = array('id'=>'alt_data_fim', 'name'=>'alt_data_fim', 'class'=>'form-control data');
                      		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            echo form_label('Hora fim', 'alt_hora_fim');
                      		$data = array('id'=>'alt_hora_fim', 'name'=>'alt_hora_fim', 'class'=>'form-control hora');
                      		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            $options = array('Ativo' => 'Ativo', 'Inativo' => 'Inativo');		
                    		echo form_label('Status', 'status');
                    		echo form_dropdown('status', $options, '', 'id="status" class="form-control"');
                            echo '</div>';
                            
                        echo '</div>';
                        
                        echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                        echo '<textarea id="observacao" name="observacao" class="form-control">'.utf8_decode($observacao).'</textarea>';
                        
                        echo '<div style="max-height: 300px;overflow: auto" class="row"><div id="todasObs" class="col-md-12"></div></div>';
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="alt_cd_node" name="alt_cd_node" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Altera node -->
    
    <!-- INÍCIO Modal Altera todos node -->
    <div  class="modal fade" id="alt-todos-status" tabindex="-1" role="dialog" aria-labelledby="alt-todos-status" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Mudar status dos selecionados</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('ura/mudarStatusNodeTodos',$data);
                        
                        echo '<div class="row">';
                        
                            echo '<div class="col-md-3">';
                            echo form_label('Node', 'alt_nome_node_todos');
                      		$data = array('id'=>'alt_nome_node_todos', 'name'=>'alt_nome_node_todos', 'value' => 'Selecionados', 'readonly' => 'readonly', 'class'=>'form-control');
                      		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            echo form_label('Data fim', 'alt_data_fim_todos');
                      		$data = array('id'=>'alt_data_fim_todos', 'name'=>'alt_data_fim_todos', 'class'=>'form-control data');
                      		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            echo form_label('Hora fim', 'alt_hora_fim_todos');
                      		$data = array('id'=>'alt_hora_fim_todos', 'name'=>'alt_hora_fim_todos', 'class'=>'form-control hora');
                      		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-3">';
                            $options = array('Ativo' => 'Ativo', 'Inativo' => 'Inativo');		
                    		echo form_label('Status', 'status_todos');
                    		echo form_dropdown('status_todos', $options, '', 'id="status_todos" class="form-control"');
                            echo '</div>';
                            
                        echo '</div>';
                        
                        echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                        echo '<textarea id="observacao" name="observacao" class="form-control">'.utf8_decode($observacao).'</textarea>';
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="idSelecionados" name="idSelecionados" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Altera todos node -->
    
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Pesquisar node</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postPermissor = (isset($postPermissor))? $postPermissor: false;
                        $postNumero = (isset($postNumero))? $postNumero: false;
                        $postStatus = (isset($postStatus))? $postStatus: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesq_node');
                    	echo form_open('ura/pesqNode',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(261, $this->session->userdata('permissoes')))? "<a href='".base_url('ura/fichaNode')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar node".$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($permissor as $per){
                      		            
                                        if($per->permissor != ''){                                        
                          			       $options[$per->permissor] = $per->permissor.' - '.htmlentities($per->nome);
                                        }                                      
                            		}	
                            		echo form_label('Permissor', 'cd_permissor');
                            		echo form_dropdown('permissor', $options, $postPermissor, 'id="permissor" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('N&uacute;mero', 'numero');
                        			$data = array('name'=>'numero', 'value'=>$postNumero,'id'=>'numero', 'placeholder'=>'Digite n&uacute;mero', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($tipos as $tip){                       
                    			       $options[$tip->nome] = htmlentities($tip->nome);                                  
                            		}	
                            		echo form_label('Tipo', 'tipo');
                            		echo form_dropdown('tipo', $options, $postTipo, 'id="tipo" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'', 'Ativo' => 'Ativo', 'Inativo' => 'Inativo');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $postStatus, 'id="status" class="form-control"');
                                    echo '</div>';
                                      
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_cadastro",utf8_encode("Pesquisar"), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>        
                </div>
                
                <div class="row">&nbsp</div>
                <?php
                if($pesquisa == 'sim'){
                ?>
                <div class="well">
                <p>
                    <strong>Mostrando <?php echo ($qtdDadosCorrente)? $qtdDadosCorrente: 0; ?> de <?php echo ($qtdNode[0]->total)? $qtdNode[0]->total: 0; ?> registros localizados.</strong>
                </p>
                
                <?php 
                $colunas[] = '<input type="checkbox" id="todos" name="todos" value="1">';
                foreach($campos as $nome => $display){
                    
                    if($sort_by == $nome){
                        $class = "sort_$sort_order";
                        $class = '';
                        
                        if($sort_order == 'asc'){
                            // Crescente
                            $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>';
                        }else{
                            // Descrecente
                            $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>';
                        }
                        
                    }else{
                        $class = "";
                        $icoAscDesc = '';
                    }
                    
                    $colunas[] = anchor("ura/pesqNode/".(($postPermissor == '')? '0': $postPermissor)."/".(($postNumero == '')? '0': $postNumero)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';

                #$this->table->set_heading('Login', 'Nome', /*'E-mail',*/ 'Cidade', 'Departamento', 'Perfil', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                            
                foreach($dadosNode as $dNode){
                    
                    $cell0 = array('data' => '<input type="checkbox" class="pegaCheckbox" name="node[]" value="'.$dNode->id.'">');
                    $cell1 = array('data' => htmlentities($this->dadosBanco->unidade($dNode->percod)[0]->nome));
                    $cell2 = array('data' => $dNode->nodeDsc);
                    $cell3 = array('data' => $dNode->tipo);
                    $cell4 = array('data' => $this->util->formataData(substr($dNode->dataInicio, 0, 10),'BR').' '.substr($dNode->dataInicio, 11, 5));
                    #$cell3 = array('data' => $usu->email_usuario);
                    $cell5 = array('data' => $this->util->formataData(substr($dNode->dataFim, 0, 10),'BR').' '.substr($dNode->dataFim, 11, 5));
                    $cell6 = array('data' => htmlentities($dNode->origem));
                    #$cell6 = array('data' => '<a href="#" class="altStatus" id="'.$dNode->id.'">'.$dNode->status.'</a>');
                    $cell7 = array('data' => $dNode->status);
                    
                    #$botaoEditar = (in_array(16, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('usuario/ficha/'.$usu->cd_usuario).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoEditar = (in_array(262, $this->session->userdata('permissoes')))? '<a title="Mudar status" href="#" onclick="statusRegistro('.$dNode->id.', \''.$dNode->nodeNro.' - '.$dNode->nodeDsc.'\',\''.$this->util->formataData(substr($dNode->dataFim, 0, 10),'BR').'\',\''.substr($dNode->dataFim, 11, 5).'\', \''.$dNode->status.'\')" data-toggle="modal"  data-target="#alt-status" class="glyphicon glyphicon-pencil"></a>': '';
                    
                    $cell8 = array('data' => $botaoEditar);
                        
                    $this->table->add_row($cell0, $cell1, $cell2, $cell3, $cell4, $cell5, $cell6, $cell7, $cell8);
                    
                }
                
            	$template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                
                echo '<div id="divButton" class="text-right"><button type="button" data-toggle="modal"  data-target="#alt-todos-status" class="btn btn-primary">Alterar Selecionados</button></div>';
                
                echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>"; 
                ?>
                </div>
                <?php
                }
                ?>
                
            </div>
    
<script type="text/javascript">

$("#divButton").css("display", "none");

$(".pegaCheckbox").click(function(){
    var arr = [];
    $("input:checkbox[name^='node']:checked").each(function(){
        arr.push($(this).val());
    });
    var selecionados = implode_js(',',arr);
    $("#idSelecionados").val(selecionados); 
    
    if($("#idSelecionados").val() != ''){
       $("#divButton").css("display", "block"); 
    }else{
       $("#divButton").css("display", "none"); 
    }
})

$("#todos").click(function(){
    
    if($(this).prop('checked') == true){
        $('input:checkbox').prop('checked', true);
        
        var arr = [];
        $.each($(".pegaCheckbox"), function() {
            arr.push($(this).val());
        });
        var selecionados = implode_js(',',arr);
        $("#idSelecionados").val(selecionados);
        
        if($("#idSelecionados").val() != ''){
           $("#divButton").css("display", "block"); 
        }else{
           $("#divButton").css("display", "none"); 
        }
        
    }else{
        $('input:checkbox').prop('checked', false);
        $("#idSelecionados").val('');
    }
    
})


function statusRegistro(cd, nome, datafim, horafim, status){ 
    $("#alt_cd_node").val(cd);
    $("#alt_nome_node").val(nome);
    $("#alt_data_fim").val(datafim);
    $("#alt_hora_fim").val(horafim);
    $("#status").val(status);
    
    $("#todasObs").html('');
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url();?>ura/pegaTodasObs',          
      data: {
        idNode: cd
      },
      dataType: "json",
      /*error: function(res) {
        $("#resMarcar").html('<span>Erro de execução</span>');
      },*/
      success: function(res) { 
        if(res){
            
            var conteudo = '<table class="table">';
            
            $.each(res, function() {
                conteudo += '<tr>';
                conteudo += '<td><strong>Autor: </strong>'+this.autor+'</td>';
                conteudo += '<td><strong>Data / Hora: </strong>'+this.data_cadastro+'</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '<td colspan="2"><p class="text-left">'+this.observacao+'</p></td>';
                conteudo += '</tr>';
            });
            
            conteudo += '</table>';
            
            $("#todasObs").html(conteudo);
            
        }else{
            $("#todasObs").html('');
        }
        
      }
    });    
    
}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    $(".hora").mask("00:00");
    $(".matricula").mask("#####000000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});

$('#alt_hora_fim').timepicker({
    timeText: 'Hor&aacute;rio',
    hourText: 'Hora',
    minuteText: 'Minuto',
    currentText: 'Atual',
    closeText: 'Fechar',
    timeFormat: 'HH:mm',
    // year, month, day and seconds are not important
    //minTime: new Date(0, 0, 0, 8, 0, 0),
    //maxTime: new Date(0, 0, 0, 15, 0, 0),
    // time entries start being generated at 6AM but the plugin 
    // shows only those within the [minTime, maxTime] interval
    startHour: 6,
    // the value of the first item in the dropdown, when the input
    // field is empty. This overrides the startHour and startMinute 
    // options
    //startTime: new Date(0, 0, 0, 8, 20, 0),
    // items in the dropdown are separated by at interval minutes
    interval: 30
});

$(".altStatus").click(function(){
    
    event.preventDefault();
    /*
    $.post( "test.php", { func: "getNameAndTime" }, function( data ) {
      console.log( data.name ); // John
      console.log( data.time ); // 2pm
    }, "json");
    */
    //alert($(this).attr("id"));
    if($(this).text() == 'Ativo'){
        $(this).text('Inativo');
    }else{
        $(this).text('Ativo');
    }
})

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
	$("#relatorios").validate({
		debug: false,
		rules: {
			data: {
                required: true
            }
		},
		messages: {
			data: {
                required: "Informe uma data."
            }
	   }
   });   
   
});

</script>