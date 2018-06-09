<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro de telecom -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar o usu&aacute;rio?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('usuario/apaga',$data);
                        
                            echo form_label('Nome', 'apg_nome_usuario');
                    		$data = array('id'=>'apg_nome_usuario', 'name'=>'apg_nome_usuario', 'class'=>'form-control data');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_cd_usuario" name="apg_cd_usuario" />
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
    <!-- INÍCIO Modal registro de Férias -->
    <div class="modal fade" id="ferias" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Defina férias do colaborador</h4>
                </div>
                <div class="modal-body row">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'frm_ferias');
                        echo form_open('usuario/salvarFerias',$data);
                            
                            echo '<div class="col-md-12">';
                            echo form_label('Nome', 'ferias-nome');
                    		$data = array('id'=>'ferias-nome', 'name'=>'ferias-nome', 'readonly'=>'readonly', 'class'=>'form-control');
                    		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-6">';
                            echo form_label('Início', 'inicio');
                    		$data = array('id'=>'inicio', 'name'=>'inicio', 'class'=>'form-control data');
                    		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-6">';
                            echo form_label('Fim', 'fim');
                    		$data = array('id'=>'fim', 'name'=>'fim', 'class'=>'form-control data');
                    		echo form_input($data,'');
                            echo '</div>';
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="fer_cd_usuario" name="fer_cd_usuario" />
                    <button type="button" id="ferias-apg-quest" class="btn btn-default pull-left">Apagar férias?</button>
                    <div id="ferias-apg">
                        <button type="button" id="ferias-apg-sim" class="btn btn-default pull-left">Sim</button>
                        <button type="button" id="ferias-res-nao" class="btn btn-default pull-left">Não</button>
                    </div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal registro de Férias -->
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Pesquisar usu&aacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $pesquisa = (isset($pesquisa))? $pesquisa: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisa_usuario');
                    	echo form_open('usuario/pesquisar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array(16, $this->session->userdata('permissoes')))? "<a href='".base_url('usuario/ficha')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset("Pesquisar usu&aacute;rio".$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-4">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Matr&iacute;cula', 'matricula_usuario');
                        			$data = array('name'=>'matricula_usuario', 'value'=>$this->input->post('matricula_usuario'),'id'=>'matricula_usuario', 'placeholder'=>'Digite a matr&iacute;cula', 'class'=>'form-control matricula');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Nome ou e-mail do usu&aacute;rio', 'nome_usuario');
                        			$data = array('name'=>'nome_usuario', 'value'=>$this->input->post('nome_usuario'),'id'=>'nome_usuario', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'');
                                    foreach($departamento as $dep){
                                        $options[$dep->cd_departamento] = htmlentities($dep->nome_departamento);
                                    }		
                            		echo form_label('Departamento', 'cd_departamento');
                            		echo form_dropdown('cd_departamento', $options, $postDepartamento, 'id="cd_departamento" class="form-control"');
                                    echo '</div>';
                                    /*
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status_usuario');
                            		echo form_dropdown('status_usuario', $options, $postStatus, 'id="status_usuario" class="form-control"');
                                    echo '</div>';
                                     */  
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
                $colunas = array();
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
                    
                    $colunas[] = anchor("usuario/pesquisar/".(($postMatricula == '')? '0': $postMatricula)."/".(($postNome == '')? '0': $postNome)."/".(($postStatus == '')? '0': $postStatus)."/".$nome."/".(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$display.$icoAscDesc, array('class' => $class));
                    
                }
                $colunas[] = 'A&ccedil;&atilde;o';

                #$this->table->set_heading('Login', 'Nome', /*'E-mail',*/ 'Cidade', 'Departamento', 'Perfil', 'A&ccedil;&atilde;o');
                $this->table->set_heading($colunas);
                            
                foreach($usuarios as $usu){
                    
                    $cell1 = array('data' => $usu->matricula_usuario);
                    $cell2 = array('data' => $usu->login_usuario);
                    $cell3 = array('data' => htmlentities($usu->nome_usuario));
                    #$cell3 = array('data' => $usu->email_usuario);
                    $cell4 = array('data' => htmlentities($usu->nome_estado));
                    $cell5 = array('data' => htmlentities($usu->nome_departamento));
                    $cell6 = array('data' => $usu->nome_perfil);
                    
                    if($this->session->userdata('cd') == 6){
                        $botaoResetarSenha = (in_array(16, $this->session->userdata('permissoes')))? '<a title="Resetar Senha" onclick="resetaSenhaAD('.$usu->cd_usuario.')" href="#" class="glyphicon glyphicon-retweet"></a>': '';
                    }else{
                        $botaoResetarSenha = '';
                    }
                    $botaoFerias = (in_array(302, $this->session->userdata('permissoes')))? '<a title="Definir Férias" href="#" class="glyphicon glyphicon-user btn-ferias" user-id="'.$usu->cd_usuario.'" data-toggle="modal"  data-target="#ferias"></a>': '';
                    $botaoEditar = (in_array(16, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url('usuario/ficha/'.$usu->cd_usuario).'" class="glyphicon glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array(17, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$usu->cd_usuario.',\''.$usu->nome_usuario.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                    
                    $cell7 = array('data' => $botaoFerias.$botaoEditar.$botaoExcluir.$botaoResetarSenha);
                        
                    $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5, $cell6, $cell7);
                    
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

$(".btn-ferias").click(function(){
    
   $("#fer_cd_usuario").val($(this).attr('user-id'));
   $("#ferias-nome").val($(this).parent().prev().prev().prev().prev().html());
   
   $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>administrador/ajaxFerias/dados',
      data: {
        cd_usuario: $("#fer_cd_usuario").val(),
      },
      dataType: "json",

      success: function(res) {
        $("#inicio").val(res['inicio']);
        $("#fim").val(res['fim']);
        
      }
    });
   
});

$("#ferias-apg-sim").click(function(){
    //alert($("#fer_cd_usuario").val());
    $(location).attr('href', '<?php echo base_url(); ?>usuario/apagaFerias/'+$("#fer_cd_usuario").val());
})

function resetaSenhaAD(cd_usuario){
    // Usuário teste AD: Login: teste.ti Senha: simtv123
    //alert(cd_usuario);
    var msg = "Deseja resetar a senha no AD? \n Senha default: 'simtv123'\nTrocar no primeiro logon.";
    var r = confirm(msg);
    if (r == true) {
        $(location).attr('href', '<?php echo base_url(); ?>usuario/resetaSenhaAD/'+cd_usuario);
    }
}

function apagarRegistro(cd, nome){
    $("#apg_cd_usuario").val(cd);
    $("#apg_nome_usuario").val(nome);
}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    $(".matricula").mask("#####000000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


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
    
    $("#ferias-apg").hide();
    
    $("#ferias-apg-quest").click(function(){
        $("#ferias-apg").show();
    });
    
    $("#ferias-res-nao").click(function(){
        $("#ferias-apg").hide();
    });
    
    // Valida o formulário
	$("#frm_ferias").validate({
		debug: false,
		rules: {
			inicio: {
                required: true
            },
            fim: {
                required: true
            }
		},
		messages: {
			inicio: {
                required: "Informe uma data."
            },
            fim: {
                required: "Informe uma data."
            }
	   }
   });   
   
});

</script>