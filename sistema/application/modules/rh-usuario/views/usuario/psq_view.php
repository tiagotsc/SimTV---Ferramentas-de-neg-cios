<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    
    <!-- IN�CIO Modal Apaga registro de telecom -->
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
                            echo form_open('rh-usuario/usuario/apaga',$data);

                                echo form_label('Nome', 'apg_nome_usuario');
                                    $data = array('id'=>'apg_nome_usuario', 'name'=>'apg_nome_usuario', 'readonly'=>'readonly','class'=>'form-control');
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
    
    <!-- IN�CIO Modal registro de F�rias -->
    <div class="modal fade" id="ferias" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Defina ferias do colaborador</h4>
                </div>
                <div class="modal-body row">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'frm_ferias');
                        echo form_open('rh-usuario/usuario/salvarFerias',$data);
                            
                            echo '<div class="col-md-12">';
                            echo form_label('Nome', 'ferias-nome');
                    		$data = array('id'=>'ferias-nome', 'name'=>'ferias-nome', 'readonly'=>'readonly', 'class'=>'form-control');
                    		echo form_input($data,'');
                            echo '</div>';
                            
                            echo '<div class="col-md-6">';
                            echo form_label('Inicio', 'inicio');
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
                    <button type="button" id="ferias-apg-quest" class="btn btn-default pull-left">Apagar ferias?</button>
                    <div id="ferias-apg">
                        <button type="button" id="ferias-apg-sim" class="btn btn-default pull-left">Sim</button>
                        <button type="button" id="ferias-res-nao" class="btn btn-default pull-left">Nao</button>
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
    <!-- FIM Modal registro de F�rias -->
    
            <div id="corpo" class="col-md-offset-1 col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                     
                        $nome = (isset($nome))? $nome: false;
                        $status = (isset($status))? $status: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open('rh-usuario/usuario/pesq',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? "<a href='".base_url($pasta.'/'.$controller.'/ficha')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset($titulo.$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-4">';
                                        echo form_label('Matr&iacute;cula', 'matricula_usuario');
                                        $data = array('name'=>'matricula_usuario', 'value'=>$this->input->post('matricula_usuario'),'id'=>'matricula_usuario', 'placeholder'=>'Digite a matr&iacute;cula', 'class'=>'form-control matricula');
                                        echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
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

                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn",'Pesquisar', 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>    
                </div>
                
                <div class="row">&nbsp</div>
                <div class="well">
                    <p>
                        <strong>Mostrando <?php echo ($qtdDadosCorrente)? $qtdDadosCorrente: 0; ?> de <?php echo ($qtdRegistos)? $qtdRegistos: 0; ?> registros localizados.</strong>
                    </p>
                    <?php 

                        $colunas = array();
                        $contCell = 0; 

                        foreach($campos as $nome){     

                            if($nome == $id){
                                $pk = $nome;
                            }

                            if($nome != $id){

                                if($sort_by == $nome){
                                    $class = "sort_$sort_order";

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

                                $label = (isset($camposLabel[$nome]))? $camposLabel[$nome]: $nome;

                                $colunas[] = anchor($pasta.'/'.$controller.'/'.$metodo.'/'.$post.'/'.$nome.'/'.(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$label.$icoAscDesc, array('class' => $class));

                            }

                        }

                        $colunas[] = 'A&ccedil;&atilde;o';

                        $this->table->set_heading($colunas);

                        foreach($dados as $da){

                            $campo1 = strtolower($campos[1]);
                            $campo2 = strtolower($campos[2]);

                            $nome = $da->$campo1.' - '.$da->$campo2;

                            foreach($campos as $campo => $valor){
                                $valor = strtolower($valor);
                                if($campo != $id){
                                    $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)) );
                                }

                            }

//                            $botaoFerias = (in_array($perCadastFerias, $this->session->userdata('permissoes')))? '<a title="Definir F�rias" href="#" class="glyphicon glyphicon-user btn-ferias" user-id="'.$da->$pk.'" data-toggle="modal"  data-target="#ferias"></a>': '';
                            $botaoEditar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<div style="white-space: nowrap"><a title="Editar" href="'.base_url($pasta.'/'.$controller.'/ficha/'.$da->$pk).'" class="glyphicon glyphicon-pencil"></a>': '';
                            $botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$da->cd_usuario.',\''.$da->nome_usuario.'\')" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a></div>': '';
                            $cell[$contCell++] = array('data' => $botaoFerias.$botaoEditar.$botaoExcluir);

                            
                            $this->table->add_row($cell);
                            $contCell = 0;

                        }

                        $template = array('table_open' => '<table class="table zebra">');
                        $this->table->set_template($template);
                        echo $this->table->generate();
                        echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>";
                    ?>
                </div>
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
    $(location).attr('href', '<?php echo base_url(); ?>rh-usuario/usuario/apagaFerias/'+$("#fer_cd_usuario").val());
})

function resetaSenhaAD(cd_usuario){
    // Usu�rio teste AD: Login: teste.ti Senha: simtv123
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
CONFIGURA O CALEND�RIO DATEPICKER NO INPUT INFORMADO
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
    
    // Traz o calend�rio input datepicker para frente da modal
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
    
    // Valida o formul�rio
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