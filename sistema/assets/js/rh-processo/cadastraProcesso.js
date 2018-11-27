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

$('.money').mask('000.000.000.000.000,00', {reverse: true});

//alert(retornaInformacao);

$('#btnCadastra').click(function(){
    $('#cadastraProcesso').submit();
});

$('#numMatricula').focusout(function(){
    if( $('#numMatricula').val() != null ){
        $.ajax({
            type: "POST",
            url: retornaInformacao,
            data:{
               matricula_colaborador: $(this).val()
            },
            dataType:"json",
            success:function(data){
                $('#nome').val(data['info']['nome_usuario']);
                $('#unidade').val(data['info']['cd_unidade']);
                $('#setor').val(data['info']['cd_departamento']);
                $('#cargo').val(data['info']['cd_cargo']);
                
            }
        });//fim ajax
    }//fim if
});