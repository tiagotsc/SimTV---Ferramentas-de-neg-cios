$('#regional').hide();
$('#unidades').hide();
$("#mesCompra").hide();
$('#previewArquivo').hide();

//var teste = [];
//document.addEventListener('keydown',function(event){
//    
//    var konami = [38, 38, 40, 40, 37, 39, 37, 39, 65, 66];
//    teste.push(event.keyCode);
//    console.log(teste);
//    
//    if(teste.length >= 10){
//        if(arraysEqual(konami,teste)){
//            alert('konami');
//            teste = [];
//        }
//    }
//
//});
//
//function arraysEqual(arr1, arr2) {
//    if(arr1.length !== arr2.length)
//        return false;
//    for(var i = arr1.length; i--;) {
//        if(arr1[i] !== arr2[i])
//            return false;
//    }
//    return true;
//}

document.addEventListener('keydown',function(event){
    
    var t = $("p[key='3']").html();
    if(event.keyCode == 32){
        alert(t);
    }
});

$('#tipoBeneficio').on('change',function(){
    if($(this).val() != ''){
        $('#regional').fadeIn();
    }else{
        $('#regional').fadeOut();
        $('#mesCompra').fadeOut();
    }
});



$('#unidadeOpc').on('change',function(){
    if($(this).val() != ''){
        $('#mesCompra').fadeIn();
    }else{
        $('#mesCompra').fadeOut();
    }
});  

$('#razao_social').on('change',function(){
    $('#mesCompra').fadeIn();
});

$("#mesCompraBeneficio").keyup(function(){
    if($(this).val().length > 1){
        $.ajax({
            type:"POST",
            url: urlAjaxAlelo,
            data:{
                nomeDaTabela: $("#tableName").val(),
                razao_social: $("#razao_social").val(),
                opcBeneficio: $('#tipoBeneficio').val(),
                mesCompraBeneficio: $("#mesCompraBeneficio").val(),
            },
            dataType: "json",
            success:function(res){
                var tr;
                var totValor = 0;
                var totColab = res['infoBeneficio'].length;
                var opcBeneficio = $('#tipoBeneficio').val();
                
                $('#regionalValue').val(res['unidade']);
                $('#mesFalta').val(res['mesCompraBeneficio']);
                $('#opcBeneficio').val($('#tipoBeneficio').val());
                $('#razaoSocial').val($('#razao_social').val());
                
                $.each(res['infoBeneficio'],function(index){
                    console.log(this);
                    tr+= '<tr>';
                        tr+= '<input id="cd_unidade" type="hidden" name="colaboradores['+index+'][cd_unidade]" value="'+this.unidade+'">';
                        tr+= '<input id="elegivelBeneficio'+index+'" type="hidden" value="'+this.elegivelBeneficio+'">';
                        tr+= '<input id="matricula_'+index+'" name="colaboradores['+index+'][matricula]" class="form-control" type="hidden" value="'+this.matricula_usuario+'"><td class="matricula">'+this.matricula_usuario+'</td>';
                        tr+= '<td>'+this.nome_usuario+'</td>';
                        tr+= '<td class="dia" id="dias_'+index+'">'+this.diasUteis+'</td>';
                        tr+= '<td><input key="'+index+'" id="acrescimos_'+index+'" name="colaboradores['+index+'][acrescimos]" class="form-control dia dataExtraSoma" type="number" value="'+this.qdt_acressimo+'"></td>';
                        tr+= '<td><input key="'+index+'" id="descontos_'+index+'" name="colaboradores['+index+'][descontos]" class="form-control dia dataExtraSubtracao" type="number" value="'+this.qdt_descontos+'" ></td>';
                        
                        tr+= '<td id="conf_'+index+'">'+this.opcBeneficio+'</td>';
                        tr+= '<td id="total_'+index+'">'+this.valor.toFixed(2)+'</td>';
                    tr += '</tr>';
                    totValor+= Number(this.valor);
                    
                });
                
                $('#valorTotalArquivo').html(totValor);
                $('#totalBeneficiosCompra').html(totColab);
                $('#corpoTabela').html(tr);
                
                var data = $('#paginaca').DataTable({
                    "pageLength": 10,
                    "order":[[1,"asc"]],
                    "lengthChange": false,                  
                    "destroy": true
                });
                
                data.on('draw',function(){
                    $(this).atualizaValorCompra();
                });
                
                $("#montaArquivo").click(function(){
                    event.preventDefault();
                    $('#geraArquivo').attr('action',geraArquivo);                   
                    data.destroy();
                    $('#previewArquivo').hide();
                    $('#geraArquivo').submit(); 
                });

                $("#salvaFalta").click(function(){
                    event.preventDefault();
                    $('#geraArquivo').attr('action',salvaFalta);
                    data.dsetroy();
                    $('#previewArquivo').hide();
                    $('#geraArquivo').submit();
                });
                
                $.fn.atualizaValorCompra = function(){
                    
                    $('.dia').on('change',function(){

                        var valorCompraIndividual = 0;

                        var beneficioCompra = $('#tipoBeneficio').val();
                        var diasUteis = parseFloat($('#dias_'+ $(this).attr('key')).html());
                        var opcBeneficio = parseInt($('#conf_'+ $(this).attr('key')).html());

                        var desconto = parseFloat($('#descontos_' + $(this).attr('key')).val());
                        var acressimo = parseFloat($('#acrescimos_' + $(this).attr('key')).val());
                        var elegivelBeneficio = $('#elegivelBeneficio' + $(this).attr('key')).val();

                        acressimo = (isNaN(acressimo))?0:acressimo;
                        desconto = (isNaN(desconto))?0:desconto;

                        var valorDia = parseInt($('#valorVR').val());
                        var valorMes = parseInt($('#valorVA').val());


                        if(opcBeneficio === 3){
                            valorCompraIndividual = (beneficioCompra == "2")? parseFloat(valorMes):parseFloat(((diasUteis + (acressimo - desconto)) * valorDia));
                        }else{
                            valorCompraIndividual = parseFloat(((diasUteis + (acressimo - desconto)) * valorDia) + ((elegivelBeneficio == 'S')?valorMes:0) );
                        }

                        $('#total_' + $(this).attr('key')).html(valorCompraIndividual.toFixed(2));
                    });
                };

                $('#previewArquivo').fadeIn();

            }// Fim success
        });// Fim Ajax
    }// Fim If
});// Fim KeyUP

$('#opcBeneficio').tooltip({
    content:"<strong>Tipos de beneficio\n\
    <ol><li>Refeicao</li><li>Alimentacao</li><li>Ambos</li></ol></strong>",
    track: true,
    position:{ 
        my: "left+15 bottom-15"
    }
});