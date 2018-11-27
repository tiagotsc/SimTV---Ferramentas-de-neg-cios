cas.controller = function() {

    function buscatt (html, trouble) {
        return html + (
       '<tr>' +
                    '<td>' + trouble.protocolo + '</td>' +
                    '<td>' + trouble.operadora + '</td>' +
                    '<td>' + trouble.statuss + '</td>' +
                    '<td>' + trouble.cidade + '</td>' +
                    '<td>' + trouble.tipo_problema + '</td>' +
                    '<td>' + trouble.cliente_final + '</td>' +
                    '<td>' + trouble.designacao + '</td>' +
                    '<td>' + trouble.date + '</td>' +
            '</tr>'
        );
 
    }
 
    function buscar (resposta) {
        var $tbody = $('#tb-trouble>tbody');
 
        $tbody.html(
            resposta.data.t_ticket.reduce(buscatt, 'protocolo')
        );
    }
 
    $('#content > form').on('submit', function buscarprotocolo (e) {
 
        e.preventDefault();
 
        cas.ajaxer({
            method: 'GET',
            sendto: '/trouble/buscar',
            sendme: {
 
                protocolo: $(this).find('input').val()
            },
 
            andthen: buscar
        });
 
    });
};