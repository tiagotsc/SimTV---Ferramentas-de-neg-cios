cas.controller = function(){
    var ajaxini;
    function simpleAjax(x,loadmsg){
        if(typeof loadmsg === 'undefined')
            loadmsg = 1;
        $.ajax({
            type: "POST",

            dataType: 'json',
            data: x.sendme,
            url: x.sendto,
            beforeSend: function(){
                ajaxini = new Date();
                if(loadmsg === 1)
                    $('#loading-pri').show();
                else{
                    cas.hidethis('body');
                }
            },
            complete:function(data){

            },
            success: function(data){
                if(data.status == 'success'){
                    if(loadmsg === 1)
                        $('#loading-pri').hide();
                    else{
                        cas.showthis('body');
                        var ajaxend = new Date();
                        $('#timeloaded').html('Dados carregados em: '+((ajaxend-ajaxini)/1000)+' segundos');
                        }
                        x.andthen({'data':data,'etc':x.etc});
                }else if(data.status == 'permission_error'){
                        window.location.replace("login");
                }else{
                    var ajaxend = new Date();
                    $('#timeloaded').html('Dados carregados em: '+((ajaxend-ajaxini)/1000)+' segundos');
                    cas.makeNotif('error',data.msg);

                }
            }
        });
    }
    function x(){
        simpleAjax({
            'sendto':"smser/x",
            'sendme': {'nums':$('#nums').val(),'msg':$('#msg').val()},
            'andthen':
                function(x){
                    cas.makeNotif('success','Sms adicionado a fila, agora é só esperar! :)');
                }
            },0);
    }
    function l()
    {
        simpleAjax({
            'sendto':"smser/l",
            'andthen':
                function(x){
                    $('#pri_list').empty();
                    if(x.data.nrun)
                        $('#countdown').html('Faltam '+x.data.nrun+' segundos para a próxima fila de SMS');
                    else
                        $('#countdown').html('Rodando!');
                    for(var i = 0;i < x.data.list.length;i++){
                        var parthtm = "<span style='width:50%;text-align:right;' class='pri' fulltxt='"+x.data.list[i].msg+"'>";
                        if(x.data.list[i].msg.length > 20)
                            parthtm += "<span class='xpndme'>"+x.data.list[i].msg.substr(0, 20)+" [...]</span>";
                        else
                            parthtm += x.data.list[i].msg;
                        parthtm += '</span>'
                        if(i == (x.data.list.length -1)){
                            parthtm = $("<div class='pri-item' style='border-style:none'><span class='pri' style='width:20%;' title='"+x.data.list[i].nums+"'>"+x.data.list[i].nums.substr(0,30)+"</span><span class='pri' style='width:20%;'>"+x.data.list[i].date+"</span>"+parthtm+"</div>");
                            parthtm.appendTo('#pri_list');
                        }else{
                            parthtm = $("<div class='pri-item'><span class='pri' style='width:20%;' title='"+x.data.list[i].nums+"'>"+x.data.list[i].nums.substr(0,30)+"</span><span class='pri' style='width:20%;'>"+x.data.list[i].date+"</span>"+parthtm+"</div>");
                            parthtm.appendTo('#pri_list');
                        }
                        parthtm.find('.xpndme').on('click',function(){
                            $('#fulldescr').html($(this).parent().attr('fulltxt'));
                            $('#descr-dlg').dialog('open');
                        });
                    }
                }
            });
            window.setTimeout(l, 3000);
    }
    $( "#env_sms" ).button({
            icons: {
                primary: "ui-icon-circle-arrow-n"
            }
    });
    $( "#env_sms" ).click(function(){
        x();
    });
    $('#descr-dlg').dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: true,
        width: 300,
        height:300,
        draggable:true,
        resizable: true,
        open: function(){
        },
        close: function(){}
    });
    $('#msg').change(function(){faltamX();});
    $('#msg').keyup(function(){faltamX();});
    $('#msg').click(function(){faltamX();});
    function faltamXY(){
        faltamX();
        window.setTimeout(faltamXY, 100);
    }
    function faltamX(){
        var numberOfLineBreaks = ($('#msg').val().match(/\n/g)||[]).length;
        var tot = ($('#msg').val().length + numberOfLineBreaks);
        if(tot < 160){
            $('#faltam_x').css('color','green');
            $('#faltam_x').html('Você ainda pode digitar '+(160 - tot)+" caracteres.");
        }else if(tot == 160){
            $('#faltam_x').css('color','blue');
            $('#faltam_x').html("Você atingiu o limite de caracteres, isso não é um problema... mas pare por aí.");
        }else{
            $('#faltam_x').css('color','red');
            $('#faltam_x').html('Os últimos '+(tot - 160)+" caracteres serão ignorados.");
        }
    }
    faltamXY();
    l();
};