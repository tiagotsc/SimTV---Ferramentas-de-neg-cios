<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

            
<!-- Modal Ferias - INICIO -->

    
    <?php include_once 'view_ferias.php';?>
    <?php include_once 'view_cadastro_beneficio.php';?>
<!-- Modal Ferias - FIM -->
                                            


        <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-10 col-lg-offset-1">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>
                    <li class="active">Ficha usu&aacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                    
                    echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvar_usuario');
                    	echo form_open('rh-usuario/usuario/salvar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Ficha usu&aacute;rio<a href='".base_url('rh-usuario/usuario/pesq')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisar</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-3">';
                                    
                                    echo form_label('Matr&iacute;cula', 'matricula_usuario');
                                                $data = array('name'=>'matricula_usuario', 'value'=>$matricula_usuario,'id'=>'matricula_usuario', 'readonly'=>'readonly', 'class'=>'form-control matricula', 'maxlength'=>'15');
                                                echo form_input($data);
                                    echo '</div>';
                                      
                                    //Testa se o usuario possui perfil limitado, caso tenha os campos de 'E-mail' e 'Login de rede', ficam somente como leitura
                                    if(!in_array($perLimCadEdit, $this->session->userdata('permissoes'))){
                                        $readonly = '';
                                        $perfilPadrao = '';
                                    }else{
                                        $readonly = 'readonly';
                                        $perfilPadrao = $perfil['cd_perfil'];
                                    }
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('Login da rede (AD)', 'login_usuario');
                                                $data = array('name'=>'login_usuario', 'value'=>$login_usuario,'id'=>'login_usuario', 'placeholder'=>'Digite o login do AD', $readonly=>'','class'=>'form-control');
                                                echo form_input($data);
                                    echo '</div>';

                                    echo '<div class="col-md-3">';
                                    echo form_label('E-mail', 'email_usuario');
                                                $data = array('name'=>'email_usuario', 'value'=>$email_usuario,'id'=>'email_usuario', 'placeholder'=>'Digite o e-mail', $readonly=>'','class'=>'form-control');
                                                echo form_input($data);
                                    echo '</div>';
                                
                                    echo '<div class="col-md-3">';
                                    echo form_label('Nome<span class="obrigatorio">*</span>', 'nome_usuario');
                        			$data = array('name'=>'nome_usuario', 'value'=>$nome_usuario,'id'=>'nome_usuario', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('RG', 'rg_usuario');
                        			$data = array('name'=>'rg_usuario', 'value'=>$rg_usuario,'id'=>'rg_usuario', 'placeholder'=>'Digite o RG', 'class'=>'form-control rg');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    echo form_label('CPF', 'cpf_usuario');
                        			$data = array('name'=>'cpf_usuario', 'value'=>$cpf_usuario,'id'=>'cpf_usuario', 'placeholder'=>'Digite o CPF', 'class'=>'form-control cpf');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($cargos as $ca){
                                        $options[$ca->cd_cargo] = htmlentities($ca->nome);
                                    }	
                            		echo form_label('Cargo', 'cd_cargo');
                            		echo form_dropdown('cd_cargo', $options, $cd_cargo, 'id="cd_cargo" class="form-control"');
                                    echo '</div>';
                                
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($estado as $est){
                            			$options[$est->cd_estado] = htmlentities($est->nome_estado);
                            		}	
                            		echo form_label('Cidade', 'cd_estado');
                            		echo form_dropdown('cd_estado', $options, $cd_estado, 'id="cd_estado" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');		
                            		foreach($departamento as $dep){
                            			$options[$dep->cd_departamento] = htmlentities($dep->nome_departamento);
                            		}	
                            		echo form_label('Departamento<span class="obrigatorio">*</span>', 'cd_departamento');
                            		echo form_dropdown('cd_departamento', $options, $cd_departamento, 'id="cd_departamento" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                        $options = array('' => '');
                                        foreach($perfil as $per){
                                            $options[$per->cd_perfil] = $per->nome_perfil;
                            		}	
                            		echo form_label('Perfil<span class="obrigatorio">*</span>', 'cd_perfil');
                            		echo form_dropdown('cd_perfil', $options, $cd_perfil, 'id="cd_perfil" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('' => '');	
                                    foreach($unidade as $un){
                                        $options[$un->cd_unidade] = htmlentities($un->nome);
                                    }	
                            		echo form_label('Unidade', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status_usuario');
                            		echo form_dropdown('status_usuario', $options, $status_config_usuario, 'id="status_usuario" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('USER' => 'Usu&aacute;rio', 'DP' => 'Departamento');		
                            		echo form_label('Tipo usu&aacute;rio<span class="obrigatorio">*</span>', 'tipo_usuario');
                            		echo form_dropdown('tipo_usuario', $options, $tipo_usuario, 'id="tipo_usuario" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array('CLT' => 'Celetista', 'PJ' => 'Pessoa Juridica');		
                            		echo form_label('Tipo funcionario<span class="obrigatorio">*</span>', 'tipo_funcionario_usuario');
                            		echo form_dropdown('tipo_funcionario_usuario', $options, $tipo_usuario, 'id="tipo_usuario" class="form-control"');
                                    echo '</div>';
                                    
                                    
                                echo '</div>';
                                
                                echo '<div class="row">';
                                    if($matricula_usuario<>NULL){
                                        echo '<br>';
                                            echo '<div class=col-md-3>';
                                                echo '<h4><a href="#" id="modal" data-toggle="modal" data-target="#FeriasModal">Historico de Ferias</a></h4>';
                                            echo '</div>';
                                    }
                                echo '</div>';
                                
                                echo '<br>';echo '<br>';
                                    
                                echo form_fieldset("Acessos do usu&aacute;rio");
                                    
                                    echo '<div class="row">';
                                     
                                        foreach( $sistemas as $c => $s)
                                        {
                                            $checked = (in_array($s, $celulasSelecionadas))? 'checked': '';
                                            
                                            echo '<div class="col-md-2 text-center">';
                                                echo form_label($c);
                                                echo form_checkbox('sistemas[]',$s,$checked);
                                            echo '</div>';
                                        }
                                    echo '</div>';
                                    
                                
                                echo '<br>';echo '<br>';
                                    
                                if($matricula_usuario<>NULL){
                                    echo form_fieldset("Beneficios do usu&aacute;rio ".'<a href="#" data-toggle="modal" data-target="#benefifios"><span class="glyphicon glyphicon-plus" id="cadastroBeneficios" style="color:blue" aria-hidden="true"></span></a>');
                                }
                                
                                    
                                echo '<div class="actions">';
                                
                                    echo form_hidden('cd_usuario', $cd_usuario);

                                    echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';   
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                    if($cd_usuario){   
                    ?> 
                    
                    
                    <?php
                    }
                    ?>       
                </div>
            </div>
    
<script type="text/javascript">
    
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

function marcaTodos(){
    
    if($('#todos').prop('checked') == true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
}

function marcaGrupo(classe, campo){
    
    if(campo.checked == true){
        $(classe).prop('checked', true);
    }else{
        $(classe).prop('checked', false);
    }

}

$(document).ready(function(){
    
    $(".matricula").mask("000000000000");
    $(".data").mask("00/00/0000");
    $(".rg").mask("00.000.000.0");
    $(".cpf").mask("000.000.000-00");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALEND�RIO DATEPICKER NO INPUT INFORMADO
*/

$(document).ready(function(){
    
    // Valida o formul�rio
	$("#salvar_usuario").validate({
		debug: false,
		rules: {
		nome_usuario: {
                required: true,
                minlength: 8
            },
            /*email_usuario: {
                required: true,
				email: true
			},
            login_usuario: {
                required: true
			},*/
            cd_cidade: {
                required: true
			},
            cd_departamento: {
                required: true
			},
            cd_perfil: {
                required: true
			}
		},
            messages: {
                nome_usuario: {
                required: "Digite o nome do usu&aacute;rio.",
                minlength: "Digite o nome completo"
            },
            /*email_usuario: {
                required: "Digite o e-mail.",
                email: "E-mail inv&aacute;lido."
            },
            login_usuario: {
                required: "Digite o login da rede (AD)."
            },*/
            cd_cidade: {
                required: "Selecione a cidade."
            },
            cd_departamento: {
                required: "Selecione o departamento."
            },
            cd_perfil: {
                required: "Selecione o perfil."
            }
	   }
   });   
   
});

// ------------------ Modal de Ferias ------------------

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
    
    $("#alterarFerias").hide();
    
    
    $("#btnEditaFerias").click(function(event){
        event.preventDefault();
        $("#alterarFerias").show("slow");
    });
    
    $("#btnCadastraFerias").click(function(event){
        event.preventDefault();
        $("#alterarFerias").show("slow");
    });
    
    $("#fechaModalFerias").click(function(event){
        event.preventDefault();
        $("#alterarFerias").hide("slow");
    });
    
    
    // Valida o formul�rio
    $("#feriasAlter").validate({
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

$(document).ready(function(){
    
    $("#cadastraFalta").hide();
    
    $("#btnCadastraFaltas").click(function(event){
        event.preventDefault();
        $("#cadastraFalta").show("slow");
    });
    
    $("#fechaModalFaltas").click(function(event){
        event.preventDefault();
        $("#cadastraFalta").hide("slow");
    });
});

$('#deleta-ferias').click(function(){
    $('#feriasAlter').attr('action', '/sistema/rh-usuario/usuario/apagaFerias');
});

$('#edita-ferias').click(function(){
    $('#edita-ferias').attr('action', '/sistema/rh-usuario/usuario/salvarFerias');
});





</script>