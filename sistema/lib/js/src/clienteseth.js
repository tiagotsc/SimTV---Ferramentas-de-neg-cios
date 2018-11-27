cas.controller = function() {
    var $form = $('#clienteseth_form');
    var form_block = false;
    
    $form.on('submit', function (e) {
        e.preventDefault();
        
        if (form_block) {
            return;
        }
        
        form_block = true;

        
        var dados_arr = $form.serializeArray();
        var dados = {};
        
        dados_arr.forEach(function (a) {
           dados[a.name] = a.value;
        });
        
        cas.hidethis('body');
        
        cas.ajaxer({
            sendto: $form.attr('action'),
            sendme: dados,
            andthen: function () {
                $form.find('input').val('');
            },
            complete: function () {
                form_block = false;
                cas.showthis("body");
            }
        });
     
    });
};