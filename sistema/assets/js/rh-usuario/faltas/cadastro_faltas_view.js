$(document).ready(function(){
    //hidd's
    $('#mesCompra').hide();
    $('#previewArquivo').hide();
});

$('#salvaFalta').click(function(){   
    event.preventDefault();
    $('#faltasForm').submit();
});

$('#regional').change(function(){
    $('#mesCompra').show('slow');
});

$('#mesCompraBeneficio').on('keyup',function(){
    if($(this).val().length > 1){
        $.ajax({
            type:"POST",
            url: urlAjaxFaltas,
            data:{
                unidade: $('#cd_unidade').val(),
                nomeDaTabela: $("#tableName").val(),
                mesCompraBeneficio: $("#mesCompraBeneficio").val()
            },
            dataType: "json",
            success:function(res){
                var tr;
                
                $.each(res['usuarios'],function(index){
                    var total = this.diasUteis + (this.qdt_acressimo - this.qdt_descontos);
                    
                    tr += '<tr>';
//                        tr+= '<input type="hidden" name="cd_usuario['+index+'] value="'+this.cd_usuario+'">';
                        tr+= '<td><input id="matricula_'+index+'" name="matricula['+index+']" class="form-control matricula" type="hidden" value="'+this.matricula_usuario+'" readonly>'+this.matricula_usuario+'</td>';
                        tr+= '<td><input id="nome_'+index+'" name="nome['+index+']" class="nome form-control" type="hidden" value="'+this.nome_usuario+'" readonly>'+this.nome_usuario+'</td>';
                        tr+= '<td><input id="dias_'+index+'" name="dias['+index+']" class="form-control dia" type="hidden" value="'+this.diasUteis+'" readonly>'+this.diasUteis+'</td>';
                        tr+= '<td><input key="'+index+'" id="acrescimos_'+index+'" name="acrescimos['+index+']" class="form-control dia dataExtraSoma" type="number" value="'+this.qdt_acressimo+'"></input></td>';
                        tr+= '<td><input key="'+index+'" id="descontos_'+index+'" name="descontos['+index+']" class="form-control dia dataExtraSubtracao" type="number" value="'+this.qdt_descontos+'"></input></td>';
                        tr+= '<td><input id="total_'+index+'" name="total['+index+']" class="form-control total" type="text" value="'+total+'" readonly></input></td>';
                    tr+= '</tr>';
                });
                
                $('#mesFalta').val($('#mesCompraBeneficio').val());
                $('#regionalValue').val($('#cd_unidade').val());
                
                $('#corpoTabela').html(tr);
//                $('#tabela').DataTable();
                
                $('#previewArquivo').show('slow');
                
                //funcoes auxiliares
                
                //altera os valores do campo "total" baseado nos campos "+" e "-"
                $(".dia").on('keyup change',function(){
                    
                    var diasUteis = parseInt($('#dias_'+ $(this).attr('key')).val());
                    var desconto = parseInt($('#descontos_' + $(this).attr('key')).val());
                    var acressimo = parseInt($('#acrescimos_' + $(this).attr('key')).val());

                    acressimo = (isNaN(acressimo))?0:acressimo;
                    desconto = (isNaN(desconto))?0:desconto;

                    var totalDias = (diasUteis + (acressimo - desconto));
//                    alert(totalDias);
                    $('#total_' + $(this).attr('key')).val(totalDias);
//                    atualizaTotalArquivo();
                });

            }//fim sucess
        });//fim ajax
    }//fim if
});//fim keyup

