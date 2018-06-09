cas.controller = function(){
    var thread = null;
    var lsearched = '';
    function searchMe(term){
        $.ajax({
            type: "POST",

            dataType: 'json',
            data: {'term':term},
            url: 'ass/ass_auto_comp',
            beforeSend: function(){
                $('#loadimg').show();
            },
            success: function(data){
                $('#loadimg').hide();
                if(data.status === 'success'){
                    $('#container a').remove();
                    for(var i=0;i<data.usrs.length;i++){
                        $('#container').append(
                            "<a class='asslistitem' target='_blank' href='dashboard#"+
                                    cas.hashbangify(
                                        {dashboard: {
                                                dashboard: 'ri',
                                                view: 'assinante',
                                                ind: 'imb',
                                                item: data.usrs[i].per+':'+data.usrs[i].ass
                                            }
                                        }
                                    )+"'>"+
                            "<span class='asslist-area'>"+data.usrs[i].area+
                                "</span><span class='asslist-name'>"+data.usrs[i].nome+"</span></a>");
                    }
                }else if(data.status === 'permission_error'){
                    window.location.replace("login");
                }else{

                }
            }
        });
    }
    $('#hlpbt').button({
        text:false,
        icons: {
            primary: "ui-icon-help"
        }
    });
    $('#hlpdlg').dialog({
        autoOpen: false,
        width: 500,
        modal: true,
        dialogClass: 'noTitleDialog',
        resizable: false,
        buttons: {
            "Fechar": function(){
                $('#hlpdlg').dialog('close');
            }
        }
    });
    $('#ass_srch').keyup(function() {
        clearTimeout(thread);
        var $this = $(this);
        thread = setTimeout(function() {
            var txt = $.trim($this.val());
            if(txt.length == 0){
                $('#container a').remove();
                lsearched = txt;
            }else
                if((txt.length > 3|| !isNaN(parseInt(txt))) && txt != lsearched){
                    searchMe(txt);
                    lsearched = txt;
                }
        }, 500);
    });
    $('#hlpbt').click(function(){
        $('#hlpdlg').dialog('open');
    });
};