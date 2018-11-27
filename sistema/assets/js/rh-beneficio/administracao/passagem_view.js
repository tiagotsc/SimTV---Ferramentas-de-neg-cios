$(document).ready(function(){
    $('#passagensInfo').hide();
});

$( "#modalCadastraPassagem").on("click", function(event){
    var unidade = $("#cd_unidade").val();

    $.post('#',{unidade: unidade},function(retorno){
        $('#cadastraPassagem').modal('toggle');
        $('#cdUnidadeModal').val(unidade);
    });
});

function modal(idPassagemAntiga){
    
    $.ajax({
        type: 'POST',
        url: urlRetornaPassagemInativa,
        data:{
            idPassagem: idPassagemAntiga
        },
        dataType: 'json',
        success:function(data){
            var tb;
            $.each(data['passagens'],function(){
                tb += '<tr>';
                    tb += '<td>'+((typeof this.passagens === 'string')?this.passagens:'Bilhete Unico')+'</td>';
                    tb += '<td>'+this.valor+'</td>';
                    tb += '<td><input type="radio" name="idPassagemNova" value="'+this.id+'" ></td>';
                tb += '</tr>';    
            });//fim each*/
//            alert('Para inativar este valor \xE9 necessario selecionar um valor para substituilo!');
//            alert('Somente passagens cadastradas previamente apareceram na lista.');
            $('#idPassagemAntiga').val(idPassagemAntiga);
            $('#corpoTableEditaPassagemModal').html(tb);
        }//fim success
    });//fim ajax
    
    $('#editaPassagem').modal('show');
    
}

$("#cd_unidade").on('change',function(){

    $.ajax({
        type:"POST",
        url: urlAjaxPassagem,
        data:{
            cd_unidade: $("#cd_unidade").val()
        },
        dataType: "json",
        success:function(data){
            var td = '';

            $('#passagensInfo').hide();

            $.each(data['passagens'],function(index){

                td += '<tr>';
                    td += '<td><p id="numeroPassagem_'+index+'" class="valoresPassagem">'+((this.passagens === null)?'Bilhete Unico':this.passagens)+'</p></td>';
                    td += '<td><p id="valor_'+index+'" class="valoresPassagem">R$ '+this.valor+'</p></td>';
                    td += '<th><a href="#" onclick=modal('+this.id+')><span class="glyphicon glyphicon-pencil"></span></a></th>';
                td += '</tr>';
            });

//                    hid += '<input type="hidden" name="cd_unidade" value="">';


            $('#corpoTabelaPassagemView').html(td);
//                    $('#passagensInfo').html(hid);
            $('#passagensInfo').fadeIn('slow');
        }
    });
});



$('#salvaAlteracao').click(function(){
//    alert(1);
    $('#alteraPassagem').submit();
});