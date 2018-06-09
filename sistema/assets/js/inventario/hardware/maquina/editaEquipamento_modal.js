$('document').ready(function(){
//    $('#equipamentoId').change(function(){
//        alert('change');
//    });
//    alert(1);
});


jQuery.validator.addMethod("numSerieValido",function(){
    $.ajax({
        type: "POST",
        url: $('#validaNumeroSerie').val(),
        data:{
            numeroSerie: $('#numSerie').val(),
        },
        dataType:"json",
        success: function(data){
            if(data['numero'] == true){
                $('#nSerie').removeClass("has-success has-error");
                $('#nSerie').addClass("has-success");
                $('#numSerie').attr("valido","true");
            }else{
                $('#nSerie').removeClass("has-success has-error");
                $('#nSerie').addClass("has-error");
                $('#numSerie').attr("valido","false");
            }
        }//Fim 'success'
    });//Fim ajax
    return ($('#numSerie').attr("valido") == "true")?true:false;
},"Numero de serie ja cadastrado");

$("#form_pesq").validate({
    rules:{
        "FK_cd_localidade":"required",
        FK_cd_setor:"required",
        numero_serie:{
            required: true,
            numSerieValido: true
        },
        tipo_equipamento:"required",
        fabricante:"required",
        modelo:"required",
        status:"required"
    },
    messages: {
        FK_cd_localidade:"Campo obrigatorio",
        FK_cd_setor:"Campo obrigatorio",
        numero_serie:{
            required:"Campo obrigatorio"
        },
        tipo_equipamento:"Campo obrigatorio",
        fabricante:"Campo obrigatorio",
        modelo:"Campo obrigatorio",
        status:"Campo obrigatorio"
    }
});