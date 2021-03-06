$(document).ready(function(){
    $(".data").mask("00/00/0000");
    $("#previewArquivo").hide();
    $("#mesCompra").hide();
    $('#exibicaoFeriados').hide();
});


var tes = $('#teste').DataTable();

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
    beforeShow :  function (){
        setTimeout ( function (){
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 );
        },  0 );
    }
});


$("#cd_unidade").on("change",function(){
    $('#mesCompra').show('slow');
});

/*
$("#baixaArquivo").click(function (){
    event.preventDefault();
    $('#geraArquivo').attr('action',geraArquivo);
    $('#geraArquivo').submit();
});

$("#salvaFalta").click(function(){
//    alert(salvaFalta);
    event.preventDefault();
    $('#geraArquivo').attr('action',salvaFalta);
    $('#geraArquivo').submit();
});*/

$("#mesCompraBeneficio").on("keyup",function(){
    if($(this).val().length > 1){

        $.ajax({
          type: "POST",
          url: urlAjaxPassagem,
          data: {
            cd_unidade: $("#cd_unidade").val(),
            nomeDaTabela: $("#tableName").val(),
            mesCompraBeneficio: $("#mesCompraBeneficio").val(),
          },
          dataType: "json",

          success: function(res) {
                var tr;
                var feriados = '';
                var tot = res['compraValeTransporte'].length;
                var totValor = 0;

                $('#regionalValue').val(res['unidade']);

                    $.each(res['compraValeTransporte'],function(index){
                        var total = (Number(this.diasUteis) + (Number(this.qdt_acressimo) - Number(this.qdt_descontos) ) ) * (this.valor*2);
                        tr+= '<tr>';
                            tr+= '<input id="matricula_'+index+'" name="matricula['+index+']" class="form-control " type="hidden" value="'+this.matricula_usuario+'" readonly><td class="matricula">'+this.matricula_usuario+'</td>';
                            tr+= '<input id="nome_'+index+'" name="nome['+index+']" class="form-control" type="hidden" value="'+this.nome_usuario+'" readonly><td>'+this.nome_usuario+'</td>';
                            tr+= '<input id="dias_'+index+'" name="dias['+index+']" class="form-control" type="hidden" value="'+this.diasUteis+'" readonly><td class="dia">'+this.diasUteis+'</td>';
                            tr+= '<td><input key="'+index+'" id="acrescimos_'+index+'" name="acrescimos['+index+']" class="form-control dia dataExtraSoma" type="number" value="'+this.qdt_acressimo+'"></td>';
                            tr+= '<td><input key="'+index+'" id="descontos_'+index+'" name="descontos['+index+']" class="form-control dia dataExtraSubtracao" type="number" value="'+this.qdt_descontos+'"></td>';
                            tr+= '<td><input id="valorPassagem_'+index+'" name="valorPassagem['+index+']" class="form-control passagem" type="text" value="'+this.valor.toFixed(2)+'" readonly></td>';
                            tr+= '<td><input id="total_'+index+'" name="total['+index+']" class="form-control total" type="text" value="'+total.toFixed(2)+'" readonly></td>';
                            tr+= '<input id="id_'+index+'" type="hidden" name="cpf['+index+']" value="'+this.cpf+'">';
                        tr+= '</tr>';
                        totValor+= Number(total);
                    });

                    if(res['feriado'] == null){

                        feriados+= '<div class="col-md-4">';
                            feriados+= '<strong>nenhum feriado neste mes<strong>';
                        feriados+= '</div>';

                    }else{

                        $.each(res['feriado'],function(){

                                feriados+= '<div class="col-md-4">';

                                    feriados+= '<ul type="none">';
                                        feriados+= '<li><strong>Data: '+this.data+'</strong></li>';
                                        feriados+= '<li><strong>Descricao: '+this.descricao+'</strong></li>';
                                    feriados+= '</ul>';
                                feriados+= '</div>';

                        });

                    }
                    
                    $('#mesFalta').val($('#mesCompraBeneficio').val());
                    $('#corpoTabela').html(tr);
                    
                    var data = $('#dados').DataTable({
                        "order":[[1,'asc']],
                        "pageLength": 10,
                        "lengthChange": false,
                        "destroy": true
                    });
                    
                    $("#salvaFalta").on('click',function(){
                        
                        event.preventDefault();
                        $('#geraArquivo').attr('action',salvaFalta);
                        
                        $('#dadosColaboradores').hide();
                        data.destroy();
                        
                        $('#geraArquivo').submit();
                    });
                    
                    
                    $("#baixaArquivo").click(function (){
                        event.preventDefault();
                        $('#geraArquivo').attr('action',geraArquivo);
                        
                        $('#dadosColaboradores').hide();
                        data.destroy();
                        
                        $('#geraArquivo').submit();
                    });
                    
                    function atualizaTotalArquivo(){
                        
                         totValor = 0;
                        $('.total').each(function(index){
                           if(index < tot){
                                totValor += parseFloat($('#total_'+index).val());
                           }
                        });
                        
                        $('#valorTotalArquivo').html(totValor.toFixed(2));
                        
                    }
                    
                    
                    //depreciete
//                    $(".dataExtraSoma").on('keyup',function(){
//
//                        var valorCompraIndividual;
//                        var valPassagem = $('#valorPassagem_'+ $(this).attr('key')).val() * 2;
//                        var acrescimo = $('#acrescimos_' + $(this).attr('key')).val();
//                        var valorAcrescimo = acrescimo * valPassagem;
//
//                        if(parseInt(acrescimo)>0){
//                            valorCompraIndividual = parseFloat($('#total_' + $(this).attr('key')).val()) + parseFloat(valorAcrescimo);
//                        }else{
//                            valorCompraIndividual = $('#dias_'+ $(this).attr('key')).val() * valPassagem;
//                        }
//                        
//                        $('#total_' + $(this).attr('key')).val(valorCompraIndividual.toFixed(2));
//                        atualizaTotalArquivo();
//                    });
//                    
//                    $(".dataExtraSubtracao").on('keyup',function(){
//                        
//                        var valorCompraIndividual;
//                        var valPassagem = $('#valorPassagem_'+ $(this).attr('key')).val() * 2;
//                        var desconto = $('#descontos_' + $(this).attr('key')).val();
//                        var valorDesconto = desconto * valPassagem;
//
//                        if(parseInt(desconto)>0){
//                            valorCompraIndividual = parseFloat($('#total_' + $(this).attr('key')).val()) - parseFloat(valorDesconto);
//                        }else{
//                            valorCompraIndividual = $('#dias_'+ $(this).attr('key')).val() * valPassagem;
//                        }
//                        
//                        $('#total_' + $(this).attr('key')).val(valorCompraIndividual.toFixed(2));
//                        atualizaTotalArquivo();
//                        
//                    });
                    
                    
                    $(".dia").on('keyup change',function(){

                        var valPassagem = parseFloat($('#valorPassagem_'+ $(this).attr('key')).val() * 2);
                        var diasUteis = parseFloat($('#dias_'+ $(this).attr('key')).val());
                        var acressimo = parseFloat($('#acrescimos_' + $(this).attr('key')).val());
                        var desconto = parseFloat($('#descontos_' + $(this).attr('key')).val());
                        
                        acressimo = (isNaN(acressimo))?0:acressimo;
                        desconto = (isNaN(desconto))?0:desconto;
                        
                        var valorCompraIndividual = (diasUteis + (acressimo - desconto)) * valPassagem;
                        $('#total_' + $(this).attr('key')).val(valorCompraIndividual.toFixed(2));
                        atualizaTotalArquivo();
                    });
                    
//                        depreciado
//                        $(".dataExtraSubtracao").keyup(function(){
//                             
//                            var valPassagem = $('#valorPassagem_'+ $(this).attr('key')).val() * 2;
//                            var valDias = $('#dias_'+ $(this).attr('key')).val();
//                            var descontos = $('#descontos_' + $(this).attr('key')).val();
//                            
//                            descontos = (descontos.length > 0)? parseInt(descontos): 0;
//                                
////                            totValor-= valPassagem * parseInt(descontos);
//                            
//                            var sumDias = parseInt(valDias) - parseInt(descontos);
//                            var tot = sumDias * valPassagem;
//                            $('#total_' + $(this).attr('key')).val(tot.toFixed(2));
//                            
//                            $(this).myFunction();
//                            $('#valorTotalArquivo').html(totValor.toFixed(2));
//                            
//                         });



//                        $("#formPesq").hide('slow');
                    $('#exibicaoFeriados').html(feriados);

                    $('#totalBeneficiosCompra').html(tot);
                    $('#valorTotalArquivo').html(totValor.toFixed(2));



                    $('#previewArquivo').hide();
                    $('#exibicaoFeriados').hide();

                    $('#previewArquivo').fadeIn('slow');
                    $('#exibicaoFeriados').fadeIn('slow');
                    

          }//FIM - success
        });//FIM - Ajax

    }//FIM - IF
});//FIM - on keyup

//$(".dataExtraSoma").keyup(function(){
//    alert(1);
//    var valPassagem = $('#valorPassagem_'+ $(this).attr('key')).val() * 2;
//    var valDias = $('#dias_'+ $(this).attr('key')).val();
//    var acrescimo = $('#acrescimos_' + $(this).attr('key')).val();
//
//    acrescimo = (acrescimo.length > 0)? parseInt(acrescimo): 0;
//
////                            totValor+= valPassagem * parseInt(acrescimo);
//
//    var sumDias = parseInt(valDias) + parseInt(acrescimo);
//    var tot = sumDias * valPassagem;
//    $('#total_' + $(this).attr('key')).val(tot.toFixed(2));
//
//    $(this).myFunction();
//    $('#valorTotalArquivo').html(totValor.toFixed(2));
////                            
//});
//
//$(".dataExtraSubtracao").keyup(function(){
//
//    var valPassagem = $('#valorPassagem_'+ $(this).attr('key')).val() * 2;
//    var valDias = $('#dias_'+ $(this).attr('key')).val();
//    var descontos = $('#descontos_' + $(this).attr('key')).val();
//
//    descontos = (descontos.length > 0)? parseInt(descontos): 0;
//
////                            totValor-= valPassagem * parseInt(descontos);
//
//    var sumDias = parseInt(valDias) - parseInt(descontos);
//    var tot = sumDias * valPassagem;
//    $('#total_' + $(this).attr('key')).val(tot.toFixed(2));
//
//    $(this).myFunction();
//    $('#valorTotalArquivo').html(totValor.toFixed(2));
//
// });

