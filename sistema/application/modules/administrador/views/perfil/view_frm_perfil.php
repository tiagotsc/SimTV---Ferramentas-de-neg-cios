<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.shiftcheckbox.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Criar/Editar perfil</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvar_perfil');
                    	echo form_open('perfil/salvarPerfil',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Cadastrar perfil<a href='".base_url('perfil/perfis')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisa</a>", $attributes);
                    		
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-8">';
                                    echo form_label('Nome do perfil', 'nome_perfil');
                        			$data = array('name'=>'nome_perfil', 'value'=>$nome_perfil,'id'=>'nome_perfil', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status<span class="obrigatorio">*</span>', 'status_perfil');
                            		echo form_dropdown('status_perfil', $options, $status_perfil, 'id="status_perfil" class="form-control"');
                                    echo '</div>';
                                
                                echo '</div>';
                                                                
                                echo '<div style="position:fixed; top: 80%; right: 7.7%" class="actions">';
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                     
                                echo form_hidden('cd_perfil', $cd_perfil);
                                
                                echo '<br><a id="mostraOcultaTodos" href="#mostraOcultaTodos">Mostrar todos</a>';     
                                echo '<label>';
                                echo '<input onclick="marcaTodos()" type="checkbox" id="todos" />';
                                echo '&nbspMARCAR / DESMARCAR TODOS</label>';
                                echo $permissoes;     
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                        
                        
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

$("#mostraOcultaTodos").click(function(){
    event.preventDefault();
   $(".todasDivs").toggle(0,function(){
		if($(this).css('display')=='none'){
			$("#mostraOcultaTodos").html('Mostrar todos');//change the button label to be 'Show'
		}else{
			$("#mostraOcultaTodos").html('Ocultar todos');//change the button label to be 'Hide'
		}
	});
});

function mostrarOcultar(link, div){
    event.preventDefault();
    $(div).toggle(0,function(){
		if($(this).css('display')=='none'){
			$(link).html('Mostrar');//change the button label to be 'Show'
		}else{
			$(link).html('Ocultar');//change the button label to be 'Hide'
		}
	});
}

function marcaGrupo(classe, campo){
    if(campo.checked == true){
        $(classe).prop('checked', true);
    }else{
        $(classe).prop('checked', false);
    }

}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    $(".todasDivs").css('display', 'none');
    
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
	$("#salvar_perfil").validate({
		debug: false,
		rules: {
			nome_perfil: {
                required: true
            }
		},
		messages: {
			nome_perfil: {
                required: "Digite um nome para o perfil."
            }
	   }
   });
   
   $('#idPermissoes :checkbox').shiftcheckbox();    
   
});

</script>