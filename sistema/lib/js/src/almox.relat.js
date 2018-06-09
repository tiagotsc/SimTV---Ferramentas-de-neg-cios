
(function(){
    function submitRelatForm(){
        var form = $(this), 
            rSelector = form.find('.relat-select'),
            relat = rSelector.val(),
            relatObj = rSelector.find('option:selected').data(),
            data = {
                tecs: [], 
                users: [], 
                from: form.find('.relat-form-from').val(),
                to: form.find('.relat-form-to').val()
            };
        
        if(relatObj.has_tecs){
            form.find('.relat-tecs>li.selected').each(function(){
                data.tecs.push(parseInt($(this).data('id')));
            });
        }
        
        if(relatObj.has_users){
            form.find('.relat-users>li.selected').each(function(){
                data.users.push(parseInt($(this).data('id')));
            });
        }
        
        data.tecs = data.tecs.join('.');
        data.users = data.users.join('.');
        data.per = $('#per').val();
        window.open('almox_relat/'+relat+'?'+$.param(data));
        
    }
    
    function userList(elem,users,per){
        
        for(var i in users){
            var user = users[i];
            user.elem = $('<li>').addClass('relat-user-li').data('id',user.id);
            if(user.email === cas.user.login || user.per && user.per === per){
                user.elem.addClass('selected');
                user.selected = true;
            }
            var aux = $("<span class='relat-user-pic'>").attr('title',user.name).appendTo(user.elem);
            if(user.avatar){
                aux.css('background-image', 'url(/media/user/32/'+user.avatar+')');
            }
            var sub = [((user.cidade)?user.cidade:''),((user.email)?user.email:'')];
            user.elem.append("<span class='relat-user-descr'>"+
                "<div class='relat-user-name'>"+user.name+"</div>"+
                "<sup class='relat-user-sub'>"+sub.join(' - ')+"</sup>"+
            "</span>");
            user.elem.click(function(){
                $(this).toggleClass('selected');
            });
        }
        users.sort(function(a,b){
            if(!a.selected || !b.selected){
                if(a.selected)
                    return -1;
                if(b.selected)
                    return 1;
            }
            if(a.name === b.name)
                return 0;
            return (a.name < b.name)?-1:1;
        });
        $(users).each(function(){
            this.elem.appendTo(elem);
        });
    }
    cas.almoxRelatForm = function(relats,users,tecnicos,per){
        var form = $('<div>').addClass('relat-form'), line = $('<h3>').addClass('title-right').appendTo(form), selector;
        line = $('<select>').addClass('relat-select').change(function(){
            var optSelected = $(this).find('option:selected').data();
            var x = {disable: [], enable: []};
            
            x[(optSelected.has_tecs)?'enable':'disable'].push('tecs');
            x[(optSelected.has_users)?'enable':'disable'].push('users');
            
            for(var i in x.disable){
                $(this).closest('.relat-form').find('.relat-'+x.disable[i]).addClass('disabled');
            }
            for(var i in x.enable){
                $(this).closest('.relat-form').find('.relat-'+x.enable[i]).removeClass('disabled');
            }
        }).appendTo(line);

        for(var i in relats){
            $('<option>').append(relats[i].name)
                .attr('value',relats[i].id).data('has_tecs',relats[i].has_tecs)
                .data('has_users',relats[i].has_users).appendTo(line);
        }
        selector = line;
        
        $('<h3>').append('Técnicos // Equipe do almoxarifado').appendTo(form);
        line = $("<div class='relat-form-hor-div'>").appendTo(form);

        userList($('<ul>').addClass('relat-tecs').appendTo(line),tecnicos, per);
        userList($('<ul>').addClass('relat-users').appendTo(line),users, per);

        line = $("<div class='relat-form-dt'>").appendTo(form);
        line.append("<span>Data Inicial:</span>");
        $("<input class='relat-form-from' type='date' />").val(window.svrtime.toYMD()).appendTo(line);
        $("<button> => </button>").click(function(){
            var p = $(this).parent();
            p.find('.relat-form-to').val(p.find('.relat-form-from').val());
        }).appendTo(line);
        line.append("<span>Data Final:</span>");
        $("<input class='relat-form-to' type='date' />")
                .val(window.svrtime.toYMD())
                /*.attr('max',window.svrtime.toYMD())
                .change(function(){
                    if($(this).val() > window.svrtime.toYMD())
                        $(this).val(window.svrtime.toYMD());
                })*/
                .appendTo(line);
        if(!checkInput('date')){
            datePolyfill(line);
        }
        form.dialog({
            modal: true,
            closeOnEscape: true,
            width: $(window).width() - 50,
            height: $(window).height() - 50,
            dialogClass: 'noTitleDialog',
            resizable: false,
            close: function(){
                $(this).dialog('destroy');
            },
            buttons: [ 
                {
                    text: "Gerar Relatório", 
                    click: submitRelatForm
                }, { 
                    text: "Fechar", 
                    click: function(){
                        $(this).dialog( "close" ); 
                    } 
                }
            ]
        });
        var h = 0;
        form.children().each(function(){
            if(!$(this).is('.relat-form-hor-div'))
                h += $(this).outerHeight();
        });
        $('.relat-form-hor-div').height(form.height() - h - 20);
        selector.trigger('change');
    };
})();