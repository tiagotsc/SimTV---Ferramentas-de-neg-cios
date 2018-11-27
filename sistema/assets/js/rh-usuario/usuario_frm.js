function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

function marcaTodos(){
    
    if($('#todos').prop('checked') === true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
}

function marcaGrupo(classe, campo){
    
    if(campo.checked === true){
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


$('#deleta-ferias').click(function(){
    $('#feriasAlter').attr('action', '/sistema/rh-usuario/usuario/apagaFerias');
});

$('#edita-ferias').click(function(){
    $('#edita-ferias').attr('action', '/sistema/rh-usuario/usuario/salvarFerias');
});

//------------ Beneficios ---------

$('#vale_transporte').hide();
$('#vale_alimentacao').hide();
$('#vale_refeicao').hide();

$('.vale').mask('0000 0000 0000 0000');


$('.beneficios_opc').click(function(){
    if($(this).prop('checked') === true){
        $('#vale_' + $(this).attr('key')).fadeIn();
    }else{
        $('#num_' + $(this).attr('key')).val('');
        $('#id_' + $(this).attr('key')).val('');
        $('#vale_' + $(this).attr('key')).fadeOut();
    }
});

$('.beneficios_opc').prop('checked',function(){
    if($(this).prop('checked') === true){
        $('#vale_' + $(this).attr('key')).fadeIn();
    }else{
        $('#vale_' + $(this).attr('key')).fadeOut();
    }
});

$('.edicao').click(function(e){
    e.preventDefault();
//    var b = $(this).attr('key');
//    alert(b);
    $.ajax({
        type: "POST",
        url: '<?php echo base_url(); ?>rh-usuario/ajaxUsuario/editaBeneficio',
        data:{
            cd_usuario: $('#cd_usuario').val(),
            beneficio: $(this).attr('key')
        },
        dataType: "json",
        
        sucess: function(data){
            
        }
        
    });

    $.post('#',{cd_usuario:cd_usuario,opcao:opc},function(){
        $('#editaBeneficios').modal('toggle');
    });
});

$('#modalFaltas').click(function(){
    $('#faltasModal').modal('toggle');
});

$('#submitBtn').click(function(){
    
    $('#num_alimentacao').unmask();
    $('#num_refeicao').unmask();
    $('#num_transporte').unmask();
    $('#salvar_usuario').submit();
});

$('.numeroPassagem').change(function(){
    $.ajax({
        type:"POST",
        url: '<?php echo base_url(); ?>rh-beneficio/ajaxBeneficio/passagem',
        data: {
            id_passagem: $(this).val(),
        },
        dataType: "json",

        success: function (res){

            var input = res['passagem'][0].valor;
            $('#valorPassagem').val(input);
        }
    });
});