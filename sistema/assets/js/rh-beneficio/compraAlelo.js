$(document).ready(function(){
    if($('#opcAlelo').val() != ''){
        var t = $('#opcAlelo').val();
//        $('#vale_refeicao').fadeIn();
//        alert(t);
        switch(t){
            case "1":
                $('#vale_refeicao').fadeIn();
                break;
            case "2":
                $('#vale_alimentacao').fadeIn();
                break;
            case "3":
                $('#vale_refeicao').fadeIn();
                $('#vale_alimentacao').fadeIn();
                break;
        }
    }
});

$('#dirCartao').hide();

$('#opcAlelo').on('change',function(){
    switch($(this).val()){
        case '1':
            $('#num_alimentacao').val('');
            $('#vale_alimentacao').hide();
            $('#vale_refeicao').fadeIn();
            break;
        case '2':
            $('#num_refeicao').val('');
            $('#vale_refeicao').hide();
            $('#vale_alimentacao').fadeIn();
            break;
        case '3':
            $('#vale_alimentacao').fadeIn();
            $('#vale_refeicao').fadeIn();
            break;
        default:
            $('#num_alimentacao').val('');
            $('#num_refeicao').val('');
            $('#vale_alimentacao').fadeOut();
            $('#vale_refeicao').fadeOut();
            break;
    }
});