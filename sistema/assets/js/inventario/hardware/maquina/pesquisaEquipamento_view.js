$('#equipInfo').DataTable({
    pageLenght:10,
    lengthChange:false,
    order:[0,'asc'],
    destroy:true
});

$('document').ready(function(){
//    console.log(retornaEquipamento);

});

function p(id){
//    alert("ola");
//    event.preventDefault();
    
    $.ajax({
        type: "POST",
        url: retornaEquipamento,
        data:{
            idEquipamento: id,
        },
        dataType:"json",
        success:function(data){
            
            $('#regional').val( data['equipamento']['FK_cd_localidade'] );
            $('#departamento').val( data['equipamento']['FK_cd_setor'] );
            $('#numSerie').val( data['equipamento']['numero_serie'] );
            $('#tipoEquip').val( data['equipamento']['FK_tipo_equipamento'] );
            $('#fabricante').val( data['equipamento']['FK_fabricante'] );
            $('#modeloEquipamento').val( data['equipamento']['FK_modelo'] );
            $('#equipStatus').val( data['equipamento']['status'] );
            
        }
    });
    
    
    $('#editaModal').modal();
    
    
    
//    $('#equipamentoId').val(id);
//    console.log(id);
}