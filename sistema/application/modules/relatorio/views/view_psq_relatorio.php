<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga relatório -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar o relat&oacute;rio?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('relatorio/apaga',$data);
                        
                            echo form_label('Nome', 'apg_nome_relatorio');
                    		$data = array('id'=>'apg_nome_relatorio', 'name'=>'apg_nome_relatorio', 'class'=>'form-control data');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_cd_relatorio" name="apg_cd_relatorio" />
                    <input type="hidden" id="apg_cd_permissao" name="apg_cd_permissao" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga relatório -->
    
    <!-- INÍCIO Modal Copiar relatório -->
    <div class="modal fade" id="copiar" tabindex="-1" role="dialog" aria-labelledby="copiar" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja copiar o relat&oacute;rio?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'copiaRegistro');
                        echo form_open('relatorio/copiar',$data);
                        
                            echo form_label('Nome', 'copy_nome_relatorio');
                    		$data = array('id'=>'copy_nome_relatorio', 'name'=>'copy_nome_relatorio', 'class'=>'form-control data');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="copy_cd_relatorio" name="copy_cd_relatorio" />
                    <input type="hidden" id="copy_cd_permissao" name="copy_cd_permissao" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Copiar relatório -->
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Pesquisar relat&oacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $postNome = (isset($postNome))? $postNome: false;
                        $postDepartamento = (isset($postDepartamento))? $postDepartamento: false;
                        $postStatus = (isset($postStatus))? $postStatus: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisa_relatorio');
                    	echo form_open('relatorio/pesquisar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(16, $this->session->userdata('permissoes')))? "<a href='".base_url('relatorio/ficha')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar relat&oacute;rio".$botaoCadastrar, $attributes);
                    		
                                echo '<div class="row">';
                            
                                    echo '<div class="col-md-5">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Nome', 'nome_relatorio');
                        			$data = array('name'=>'nome_relatorio', 'value'=>$postNome,'id'=>'nome_relatorio', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($departamento as $dep){
                            			$options[$dep->cd_departamento] = html_entity_decode($dep->nome_departamento);
                            		}	
                            		echo form_label('Departamento', 'cd_departamento');
                            		echo form_dropdown('cd_departamento', $options, $postDepartamento, 'id="cd_departamento" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status_relatorio');
                            		echo form_dropdown('status_relatorio', $options, $postStatus, 'id="status_relatorio" class="form-control"');
                                    echo '</div>';
                                
                                echo '</div>';
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_cadastro","Pesquisar", 'class="btn btn-primary pull-right"');
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
                $this->table->set_heading('Cd', 'Nome', 'Departamento', 'Status', 'A&ccedil;&atilde;o');
                            
                foreach($relatorios as $rel){
                    
                    $cell1 = array('data' => $rel->cd_relatorio);
                    $cell2 = array('data' => htmlentities($rel->nome_relatorio));
                    $cell3 = array('data' => htmlentities($rel->nome_departamento));
                    $cell4 = array('data' => $rel->status_relatorio);
                    
                    $botaoCopy = (in_array(183, $this->session->userdata('permissoes')))? '<a title="Copiar" href="#" onclick="copyRegistro('.$rel->cd_relatorio.', '.$rel->cd_permissao.',\''.$rel->nome_relatorio.'\')" data-toggle="modal"  data-target="#copiar" class="glyphicon glyphicon glyphicon-copyright-mark"></a>': '';
                    $botaoEditar = (in_array(34, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('relatorio/ficha/'.$rel->cd_relatorio).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(35, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$rel->cd_relatorio.','.$rel->cd_permissao.',\''.$rel->nome_relatorio.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
                    $cell5 = array('data' => $botaoCopy.$botaoEditar.$botaoExcluir);
                        
                    $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5);
                    
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

function apagarRegistro(cd, permissao, nome){
    
    $("#apg_cd_relatorio").val(cd);
    $("#apg_cd_permissao").val(permissao);
    $("#apg_nome_relatorio").val(nome);
}

function copyRegistro(cd, permissao, nome){
    
    $("#copy_cd_relatorio").val(cd);
    $("#copy_cd_permissao").val(permissao);
    $("#copy_nome_relatorio").val(nome);
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