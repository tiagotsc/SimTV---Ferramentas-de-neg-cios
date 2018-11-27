$('#tableResultado').DataTable({
    "order":[[1,"asc"]],
    "lengthChange": false
});

$('.modalOpen').click(function(){
    $('#myModal').modal('show');
});

//function teste(){
//    alert(2);
//}

function editaProcesso(idProcesso){
    event.preventDefault();
    alert(1);
    $.ajax({
        type: "POST",
        url: 'http://sistemas-teste.simtv.com.br/sistema/rh-processos/ajaxProcesso/editaProcesso',
        data:{
            idProcesso: idProcesso
        },
        dataType:"json",
        success: function(data){
            
            $('#numMatricula').val(data['processo']['nome_colaborador']);
            $('#nome').val(data['processo']['matricula_colaborador']);
            $('#cargo').val(data['processo']['cargo_colaborador']);
            $('#setor').val(data['processo']['setor_colaborador']);
            $('#unidade').val(data['processo']['unidade_colaborador']);
            $('#periodoTrabalhadoInicio').val(data['processo']['inicio_periodo_trabalho']);
            $('#periodoTrabalhadoFim').val(data['processo']['fim_periodo_trabalho']);
            $('#numProcesso').val(data['processo']['numero_processo']);
            $('#motivoAcao').val(data['processo']['motivo_processo']);
            $('#vara').val(data['processo']['vara_processo']);
            $('#primeiraReclamadaProcesso').val(data['processo']['primeira_reclamada_processo']);
            $('#segundaReclamadaProcesso').val(data['processo']['segunda_reclamada_processo']);
            $('#andanmentoProcesso').val(data['processo']['andamento_processo']);
            $('#valorCausa').val(data['processo']['valor_causa_processo']);
            $('#principal').val(data['processo']['principal_processo']);
            $('#inss').val(data['processo']['INSS_processo']);
            $('#ir').val(data['processo']['IR_processo']);
            $('#valorProvisao').val(data['processo']['valor_provisao_processo']);
            $('#prognostico').val(data['processo']['prognostico_processo']);
            $('#valorEnvolvido').val(data['processo']['valor_envolvido_processo']);
            $('#valorContingencia').val(data['processo']['valor_contigencia_processo']);
            $('#faseProcesso').val(data['processo']['fase_processo']);
            $('#objeto').val(data['processo']['objeto_processo']);
            $('#deposito').val(data['processo']['deposito_processo']);
            $('#dataProcesso').val(data['processo']['data_processo']);
            $('#valorBloqueado').val(data['processo']['valor_bloqueado_processo']);
            $('#acordo').val(data['processo']['acordo_processo']);
            $('#outros').val(data['processo']['outros_processo']);
            
        }//fim success
    });//fim ajax
}//fim editaProcesso