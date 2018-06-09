cas.controller = function () {
    
    $("#group-login").click(function () {
        $('#logintb').toggle();
    });
    
    $('#ad-login').click(function (e) {
        $('#ADLoginForm>table').toggle();
    });

    $('#loginForm').submit(function (event) {
        event.preventDefault();
        cas.ajaxer({
            sendme: {
                email: $('#user-login').val(),
                password: $('#user-password').val()
            },
            sendto: $(this).attr('action'),
            andthen: function(x){
                window.location.reload(true);
            }
        });
    });

    $('#ADLoginForm').submit(function (event) {
        event.preventDefault();
        $.post('/login/l_ad', $(this).serialize())

            .done(function (e) {
                window.location.reload(true);
            })

            .fail(function (e) {
                console.log(e);
                cas.makeNotif('warning', e.responseText);
            });

    });

    $('#google-login').click(function(){
        cas.hidethis('body');
    });
    
  (function() {
    var po = document.createElement('script');
    po.type = 'text/javascript'; po.async = true;
    po.src = 'https://plus.google.com/js/client:plusone.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
  })();
  
    window.onSignInCallback = function (authResult) {
        var args = arguments;
        if (authResult['access_token']) {
            
            $.ajax({
                type: 'POST',
                url: '/login/gplus',
                dataType: 'text',
                data: {code: authResult.code}
            }).done(function (response) {
                cas.makeNotif('success', response);
                
                setTimeout(function() {
                    window.location.reload(true);
                }, 1000);
                
            }).fail(function (e) {
                cas.makeNotif('error', e.responseText)
            });
        }
        
    };
};