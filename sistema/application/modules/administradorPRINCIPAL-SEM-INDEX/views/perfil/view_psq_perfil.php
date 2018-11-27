<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro de perfil -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar o perfil?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('perfil/apagaPerfil',$data);
                        
                            echo form_label('Nome', 'apg_nome_perfil');
                    		$data = array('id'=>'apg_nome_perfil', 'name'=>'apg_nome_perfil', 'class'=>'form-control data');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_cd_perfil" name="apg_cd_perfil" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga registro de perfil -->
    
    <!-- INÍCIO Modal Copiar perfil -->
    <div class="modal fade" id="copiar" tabindex="-1" role="dialog" aria-labelledby="copiar" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja copiar essa perfil?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'copiarPerfil');
                        echo form_open('perfil/copiarPerfil',$data);
                        
                            echo form_label('Nome', 'copy_nome_perfil');
                    		$data = array('id'=>'copy_nome_perfil', 'name'=>'copy_nome_perfil', 'class'=>'form-control data');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="copy_cd_perfil" name="copy_cd_perfil" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Copiar perfil -->

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Pesquisar perfil</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postNome = (isset($postNome))? $postNome: false;
                        $postStatus = (isset($postStatus))? $postStatus: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisa_perfil');
                    	echo form_open('perfil/pesquisarPerfil',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(18, $this->session->userdata('permissoes')))? "<a href='".base_url('perfil/fichaPerfil')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar perfil".$botaoCadastrar, $attributes);
                    		
                                echo '<div class="row">';
                                    echo '<div class="col-md-8">';
                                    echo form_label('Nome do perfil', 'nome_perfil');
                        			$data = array('name'=>'nome_perfil', 'value'=>$postNome,'id'=>'nome_perfil', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status_perfil');
                            		echo form_dropdown('status_perfil', $options, $postStatus, 'id="status_perfil" class="form-control"');
                                    echo '</div>';
                                echo '</div>';    
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_cadastro","Pesquisar perfil", 'class="btn btn-primary pull-right"');
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
                
                <?php 
                $colunas = array();
                foreach($campos as $nome => $display){
                    
                    if($sort_by == $nome){
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
                    
                    $colunas[] = anchor("perfil/pesquisarPerfil/".(($postNome == '')? '0': $postNome)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';
                #$this->table->set_heading('Cd', 'Nome', 'Status', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                            
                foreach($perfis as $per){
                    
                    $cell1 = array('data' => $per->cd_perfil);
                    $cell2 = array('data' => html_entity_decode($per->nome_perfil));
                    $cell3 = array('data' => $per->status_perfil);
                    
                    $botaoCopy = (in_array(162, $this->session->userdata('permissoes')))? '<a title="Copiar" href="#" onclick="copyRegistro('.$per->cd_perfil.',\''.$per->nome_perfil.'\')" data-toggle="modal"  data-target="#copiar" class="glyphicon glyphicon glyphicon-copyright-mark"></a>': '';
                    $botaoEditar = (in_array(18, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('perfil/fichaPerfil/'.$per->cd_perfil).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(19, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$per->cd_perfil.',\''.$per->nome_perfil.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
                    $cell4 = array('data' => $botaoCopy.$botaoEditar.$botaoExcluir);
                        
                    $this->table->add_row($cell1, $cell2, $cell3, $cell4);
                    
                }
                
            	$template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>"; 
                ?>
                </div>
                <?php
                }
                ?>
                
            </div>
    
<script type="text/javascript">

function apagarRegistro(cd, nome){
    $("#apg_cd_perfil").val(cd);
    $("#apg_nome_perfil").val(nome);
}

function copyRegistro(cd, nome){
    $("#copy_cd_perfil").val(cd);
    $("#copy_nome_perfil").val(nome);
}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data,#data2").datepicker({
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