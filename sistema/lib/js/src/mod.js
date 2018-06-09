cas.controller = function(){
    $('#loading').hide();
    var ongoingAjax = 0,ajaxini;
    function simpleAjax(x, hideme)
    {
        if (typeof hideme === 'undefined')
            hideme = 'body';
        $.ajax(
        {
            type: "POST",
            dataType: 'json',
            data: x.sendme,
            url: x.sendto,
            beforeSend: function ()
            {
                ongoingAjax++;
                cas.hidethis(hideme);
                if (ongoingAjax == 1){
                    ajaxini = new Date();

                }
            },
            complete: function (data)
            {
                ongoingAjax--;
                cas.showthis(hideme);
                if (ongoingAjax == 0)
                {

                    var ajaxend = new Date();
                    $('#timeloaded').html('Dados carregados em: ' + ((ajaxend - ajaxini) / 1000) + ' segundos');
                }
            },
            success: function (data)
            {
                if(typeof data.msg !== 'undefined')
                    cas.makeNotif(((data.status !== 'success')?'error':'success'), data.msg);
                if (data.status == 'success')
                {
                    if (x.andthen) x.andthen(
                    {
                        'data': data,
                        'etc': x.etc
                    });
                }
                else if (data.status == 'permission_error')
                {
                    window.location.replace("login");
                }
                else
                {
                    $('.popclose').dialog('close');
                }
            }
        });
    }
    function ajaxFileUpload(elem,mid,img){
        $.ajaxFileUpload({
            url:"mod/u",
            secureuri:false,
            fileElementId:elem,
            dataType: 'json',
            data:{id:mid,im:img},
            error: function (data, status, e){
                console.log(e);
            }
        });

    }
    function openNewModelForm(){
        simpleAjax({
            sendto: "mod/n",
            andthen: function(x){
                $("#container").html(x.data.htm);
                location.hash = "n";
            }
        });
    };
    function decodeUrl(){
        var weird = false;
        if (location.hash.substr(0,1) == "#"){
            var query = new Array();
            if(location.hash.indexOf("?") != -1){
                var l = location.hash.split("?");
                if(location.hash.indexOf("?") != -1){
                    var chunks = l[1].split("&");
                    for(x in chunks){
                        var a = chunks[x].split("=");
                        var k = a[0];
                        var v = a[1];
                        query[k] = v;
                    }
                }else{
                    weird = true;
                }
            }else{
                weird = true;
            }
        }
        switch(location.hash.substr(1,1)){
            case 'm':
                if(weird){
                    cas.makeNotif('error','URL Inválida');
                }else{
                    $('#guesswhat').val(query['id']);
                    loadModel(query);
                }
                break;
            case 'e':
                if(weird){
                    cas.makeNotif('error','URL Inválida');
                }else{
                    $('#guesswhat').val(query['id']);
                    editModel(query);
                }
                break;
            case 'n':
                openNewModelForm();
                break;
        }
    }
    function deleteModel(v){
        simpleAjax({
            sendme: {id:v['id']},
            sendto: "mod/x",
            andthen:function(x){
                $("#container").empty();
            }
        });
    }
    function loadModel(v){
        simpleAjax({
            sendme: {id:v['id']},
            sendto: "mod/m",
            andthen:function(x){
                $("#container").html(x.data.htm);
                $('td pre').css('height',$('.descr').parent().css('height'));
                location.hash = "m?id=" + v['id'];
            }
        });
    }
    function editModel(v){
        simpleAjax({
            sendme: {id:v['id']},
            sendto: "mod/e",
            andthen:function(x){
                $("#container").html(x.data.htm);
                $('.descr').css('height',$('.descr').parent().css('height'));
                location.hash = "e?id=" + v['id'];
                $('#back_img_form').iframePostForm({
                    json : true,
                    post : function sendBackImg(){
                            if ($('#back_img_form input[type=file]').val().length){
                                    //loading
                            }else{
                                    alert('Por favor, selecione uma imagem.')
                                    return false;
                            }
                    },
                    complete : function (response){
                        if (response.status !== 'success'){
                            cas.makeNotif('error',response.msg);
                        }else{
                            if(response.imageSource){
                                $('#back_td .tdimg').empty();
                                $('#back_td .tdimg').html('<a href="media/model/' + response.imageSource + '" target="_blank"></a>');
                                $('#back_td a').append("<img src='media/model/" + response.imageSource +"'/>");
                                $('#back_td img').attr('width',"100%");
                                $('#back_td img').attr('height',"400px");
                            }
                        }
                    }
                });
                $('#front_img_form').iframePostForm({
                    json : true,
                    post : function sendfrontImg(){
                            if ($('#front_img_form input[type=file]').val().length){
                                   //loading
                            }else{
                                alert('Por favor, selecione uma imagem.')
                                return false;
                            }
                    },
                    complete : function (response){
                        if (response.status !== 'success'){
                            cas.makeNotif('error',response.msg);
                        }else{
                            if(response.imageSource){
                                cas.makeNotif('information',response.msg);
                                $('#front_td .tdimg').empty();
                                $('#front_td .tdimg').html('<a href="media/model/' + response.imageSource + '" target="_blank"></a>');
                                $('#front_td a').append("<img src='media/model/" + response.imageSource +"'/>");
                                $('#front_td img').attr('width',"100%");
                                $('#front_td img').attr('height',"400px");
                            }
                        }
                    }
                });
            }
        });
    }
    function saveModel(){
        simpleAjax({
            sendme: { id:$('#mid').val(),
                    name:$('#mname').val(),
                    vendor:$('#mvendor').val(),
                    descr:$('#mdescr').val()},
            sendto: "mod/s",
            andthen:function(x){
                loadModel({id:$('#mname').val()});
            }
        });

    }
    function newModel(){
        simpleAjax({
            sendme: {name:$('#newmodelname').val(),
                    vendor:$('#newmodelvendor').val(),
                    descr:$('#newmodeldescr').val()},
            sendto: "mod/s",
            andthen:function(x){
                editModel({id:$('#newmodelname').val()});
            }
        });
    }
    $('#content').on('click','#sendimgbutton',function(){
        $(this).parent().parent().submit();
    });
    $('#newbt').click(function(){
        openNewModelForm();
    });
    $('#content').on('click','#nextbt',function(){
        newModel();
    });
    $('#searchbt').click(function(){
        loadModel({'id':$('#guesswhat').val()});
    });
    $('#guesswhat').keypress(function(e){
        if (e.which == 13) {
            loadModel({'id':$('#guesswhat').val()});
        }
    });
    $('#content').on('click','#edit_item',function(){
        editModel({'id':$('#mname').val()});
    });
    $('#content').on('click','#cancel_item',function(){
        loadModel({id:$('#mname0').val()});
    });
    $('#content').on('click','#delete_item',function(){
        var r = confirm("Você realmente deseja excluir este item?");
        if (r==true){
            deleteModel({'id':$('#mid').val()});
        }
    });
    $('#content').on('click','#save_item',function(){
        saveModel();
    });
    $("#guesswhat").autocomplete({
        source: "mod/a",
        select: function(event, ui) { loadModel({id:ui.item.value}); },
        minLength: 1
    });
    decodeUrl();
};