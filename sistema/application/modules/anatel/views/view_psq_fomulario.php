<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro de telecom -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar o formul&aacute;rio da anatel?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('anatel/apagaFrmAnatel',$data);
                        
                            echo form_label('Formul&aacute;rio', 'apg_nome_anatel');
                    		$data = array('id'=>'apg_nome_anatel', 'name'=>'apg_nome_anatel', 'class'=>'form-control', 'readonly' => 'readonly');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_cd_frm_anatel" name="apg_cd_frm_anatel" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga registro de telecom -->

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Anatel - Pesquisar formul&aacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                        $postTipoFrm = (isset($postTipoFrm))? $postTipoFrm: false;
                        $postDepartamento = (isset($postDepartamento))? $postDepartamento: false;
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisa_anatel');
                    	echo form_open('anatel/pesquisarForm',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(130, $this->session->userdata('permissoes')))? "<a href='".base_url('anatel/fichaForm')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar formul&aacute;rio".$botaoCadastrar, $attributes);
                    		
                                echo '<div class="row">';
                                    echo '<div class="col-md-6">';
                                    $options = array('' => '');	
                                    foreach($tipos_frm_anatel as $tFa){
                                        $options[$tFa->cd_anatel_tipo_frm] = $tFa->nome;
                                    }	
                            		echo form_label('Tipo de formul&aacute;rio<span class="obrigatorio">*</span>', 'cd_tipo_frm_anatel');
                            		echo form_dropdown('cd_tipo_frm_anatel', $options, $postTipoFrm, 'id="cd_tipo_frm_anatel" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '');		
                            		foreach($departamento as $dep){
                            			$options[$dep->cd_departamento] = htmlentities($dep->nome_departamento);
                            		}	
                            		echo form_label('Departamento<span class="obrigatorio">*</span>', 'cd_departamento');
                            		echo form_dropdown('cd_departamento', $options, $postDepartamento, 'id="cd_departamento" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status_frm_anatel');
                            		echo form_dropdown('status_frm_anatel', $options, $postStatus, 'id="status_frm_anatel" class="form-control"');
                                    echo '</div>';
                                echo '</div>';    
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_cadastro","Pesquisar anatel", 'class="btn btn-primary pull-right"');
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
                $colunas[] = 'Cd';
                $colunas[] = 'Indicador';
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
                    
                    $colunas[] = anchor("anatel/pesquisarForm/".(($postTipoFrm == '')? '0': $postTipoFrm)."/".(($postDepartamento == '')? '0': $postDepartamento)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';
                #$this->table->set_heading('Cd', 'Nome', 'Status', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                            
                foreach($frmsTodos as $frmsT){
                    
                    $cell1 = array('data' => $frmsT->cd_anatel_frm);
                    $cell2 = array('data' => $frmsT->sigla);
                    $cell3 = array('data' => htmlentities($frmsT->nome));
                    $cell4 = array('data' => htmlentities($frmsT->nome_departamento));
                    $cell5 = array('data' => $frmsT->status);
                    
                    $botaoEditar = (in_array(130, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('anatel/fichaForm/'.$frmsT->cd_anatel_frm).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(131, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$frmsT->cd_anatel_frm.',\''.$frmsT->nome.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
                    $cell6 = array('data' => $botaoEditar.$botaoExcluir);
                        
                    $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5, $cell6);
                    
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
    $("#apg_cd_frm_anatel").val(cd);
    $("#apg_nome_anatel").val('CD '+cd+' - '+nome);
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