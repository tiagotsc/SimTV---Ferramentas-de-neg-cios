$(function(){
    var isMobile = cas.detectmob();
    var showAll = false;
    var user = {},convs = {};
    var maxID = -1;
    var firstLoad = false;
    var chattoggler = null;
    alerts.chat = [];
    var chatbox = 
        $("<div class=chatbox>"+
            "<ul class='chat-list'>"+
                "<li class='chat-label'>"+
                    "<span class='lbl-txt'>Chat corporativo - SIM TV</span>"+
                "</li>"+
                "<li class='chat-group' style='display:none'>"+
                    "<h4>Novo chat em grupo</h4>"+
                    "<input class='chat-group-name' type='text' placeholder='"+
                        "Digite um nome para o Chat' />"+
                    "<ul class='chat-group-user-list'></ul>"+
                    "<textarea placeholder='Digite sua mensagem' class='chat-textarea'></textarea>"+
                "</li>"+
            "</ul>"+
            "<div class='chat-bottom'>"+
                "<div class='chat-bottom-inner'>"+
                    "<span class='chat-bt chat-icon hide-chat'>&zwnj;</span>"+
                    "<span class='chat-bt chat-icon chat-hide-all'>&zwnj;</span>"+
                    "<span class='chat-bt chat-icon chat-tgl chat-group-pic chat-group-new'>&zwnj;</span>"+
                    "<span class='chat-bt chat-icon chat-tgl chat-toggle-all' "+
                        "title='Mostrar/Ocultar usuários offline'>&zwnj;</span>"+
                "</div>"+
            "</div>"+
        "</div>")
        .appendTo('body');
    
    
    $('.chatbox,.chat-list,body').scroll(function(){
        if($(this).scrollLeft() > 0)
            $(this).scrollLeft(0);
    });
    
    var chatW = 300;
    
        
    if(isMobile){
        
        chatbox.addClass('mobilechat');
        chatW = '100%';
        
    }else{
        
        $('body').click(function(event){
            if( chatVisible && event.pageX > chatbox.offset().left + chatbox.outerWidth() ){
                chatbox.animate({
                    opacity: 0.1
                },500);
            }
        });
        
        var mTimeout;
        chatbox.mouseenter(function(){
            clearTimeout(mTimeout);
            if(!chatVisible)
                return;
            
            chatbox.animate({
                opacity: 1
            },100);
            
        }).mouseleave(function(event){
            if( event.pageX <= chatbox.offset().left + chatbox.outerWidth() )
                return;
            
            clearTimeout(mTimeout);
            if(!chatVisible)
                return;
            mTimeout = setTimeout(function(){
                chatbox.animate({
                    opacity: 0.1
                },500);
            },1000);
        });
        
    }
    var hoverTime;
    function hoverize(uPic){
        uPic.on('mouseenter',function(){
            
            var src = $(this).attr('src');
                        
            if(src === '/lib/img/def-user.png')
                return false;
            
            src = src.replace('/32/','/orig/');
            clearTimeout(hoverTime);
            hoverTime = setTimeout(function(){
                $('.chat-image-hoverize').remove();
                $('<img>').addClass('chat-image-hoverize')
                    .attr('src',src)
                    .imagesLoaded(function(){
                        $(this).css('opacity',1);
                    }).appendTo('body');
            },1000);
        }).on('mouseleave',function(){
            $('.chat-image-hoverize').remove();
            clearTimeout(hoverTime);
        });
    }
    function doScroll(){
        chatbox.niceScroll({horizrailenabled:false});
    }
    function unScroll(){
        var c = chatbox.getNiceScroll();
        if(c.length)
            c.remove();
    }
    var chatVisible = false;
    function chatHide(){
        undoTextMax();
        unScroll();
        $('.chatbox,.chat-bottom').stop(true).animate({left:'-'+chatW},function(){
            chatVisible = false;
            $('body').css('height','');
            chatbox.hide();
            setTimeout(function(){
                chatbox.height($(document).height()).show();
            },100);    
                
        });
    }
    function chatShow(){
        
        $('.chatbox,.chat-bottom').stop(true).animate({opacity:1,left:0},function(){
            chatVisible = true;
            doScroll();
            totalResize();
        });
    }
    var tRt;
    function totalResize(){
        if(!chatVisible)
            return true;
        clearTimeout(tRt);
        tRt = setTimeout(function(){
            if(isMobile){
                chatbox.css('height','');
                if($(document).height() > chatbox.height())
                    chatbox.height($(document).height());
                $('body').height(chatbox.height());

            }else{
                var c = chatbox.getNiceScroll();
                if(c.length)
                    c.resize();
            }
        },10);
    }
    
    
    
    function hideAll(){
        
        if(undoTextMax())
            return true;
        chatbox.find('.chat-open').removeClass('chat-open');
        chatbox.find('.chat-conversation').slideUp(totalResize);
        if(isMobile)
            $(document.documentElement).animate({scrollTop:0});
        else
            chatbox.animate({scrollTop:0});
        
    }
    function groupNew(){
        if(undoTextMax())
            return true;
        
        chatbox.find('.chat-group').slideToggle(totalResize)
                .find('.chat-group-user-list').empty();
        chatbox.find('.chat-group').find('.chat-group-name').val('');
    }
    
    function undoTextMax(){
        var c = $('.chat-textarea-max');
        if(c.length > 0){
            c.removeClass('chat-textarea-max').removeAttr('style');
            c.parent().hide();
            setTimeout(function(){
                c.parent().show();
                $(document.documentElement).scrollTop(c.parent().offset().top - 30);
            },1000);
            return true;
        }
        return false;
    }
    function textMax(){
        if(!isMobile)
            return true;
        
        $('.chat-textarea-max').removeClass('chat-textarea-max');
        $(this).addClass('chat-textarea-max').css({
                    position:'fixed',
                    left:0,top:0,'z-index':1
                }).height('100%').width('100%');
    }
    function goToAlert(){
        var x = getConversationChatBox({id:$(this).attr('conv')});
        focusOnConv(x);
    }
    function focusOnConv(x){
        if(isMobile){
            var m = x.find('.chat-msg-box');
            $(document.documentElement).scrollTop( ( m.offset().top + m.height() ) - 100 );
        }else{
            chatbox.scrollTop(x.position().top);
            x.find('.chat-textarea').focus();
        }
    }
    function loadUsers(){
        cas.ajaxer({
            method:'GET',
            sendto:'chat/user_list',
            andthen:loadUsers_
        });
    }
    function loadUsers_(x){
        var us = x.data.user, u;
        user = {};
        convs = {};
        
        var conv = x.data.convs;
        var ul = chatbox.find('.chat-list');
        ul.find('.chat-container').remove();
        
        for(var i in us){
            u = us[i];
            if(u.login !== cas.user.login){
                makeUser(u);
            }
            user[u.num_id] = u;
        }
        
        for(var i in conv){
            conv[i].id = parseInt(conv[i].id);
            convs[conv[i].id] = conv[i];
            makeConv(conv[i],ul);
        }
        init();
    }
    function init(){
        if(!chattoggler){
            chattoggler = 
                $("<div class='chat-show-button"+
                    ((isMobile)?' mobilechat':'')+"' "+
                    "title='Clique para abrir o Chat'>"+
                    "<span class='chat-icon show_chat'>"+
                        "&zwnj;</span>"+
                    "<span class='chat-icon-txt'>"+
                        "&zwnj;</span></div>")
                .click(chatShow);
            if(!isMobile && $('#foot').is(':visible')){
                $('#foot').css('text-indent',0);
                chattoggler.css({
                    display:'inline-block',
                    position: 'relative',
                    'margin-right': 10
                }).prependTo('#foot');
            }else{
                chattoggler.appendTo('body');
            }
            window.actuallyOpenChat = function(){
                var x = getConversationChatBox({user:$(this).attr('data-user')});
                
                if(!chatVisible)
                    chatShow();
                
                if(x && x.length){
                    chatbox.scrollTop(x.offset().top);
                    x.find('.chat-textarea').focus();
                }
                return false;
            };
        }
        refreshStatus();
        poll();
        setInterval(poll,1000 * 60);
    }
    
    function getOut(){
        var elem = $(this).closest('.chat-container');
        cas.ajaxer({
            etc:{elem:elem},
            sendme:{
                conversation:elem.attr('conversation-id')
            },sendto:'chat/nothing_to_see_here',
            andthen:getOut_
        });
    }
    function getOut_(x){
        x.etc.elem.remove();
    }
    function saveUs(){
        
        var elem = $(this).closest('.chat-container');
        var new_name = elem.find('.chat-group-name').val();
        if(new_name && new_name.length){
            elem.find('.user-name').html(new_name);
        }
        var new_users = [];
        elem.find('.chat-group-user-item.new').each(function(){
            new_users.push($(this).attr('data-user'));
        });
        
        cas.ajaxer({
            etc:{elem:elem},
            sendme:{
                conversation: elem.attr('conversation-id'),
                name: new_name,
                new_users: new_users
            },sendto:'chat/add_them'
        });
        
    }
    function gOpts(){
        $(this).toggleClass('chat-gopts');
        var t = $(this).closest('.chat-container');
        t.find('.chat-group-opts').slideToggle();
        return false;
    }
    function makeUser(u,rev){
        var j = 
            $("<li class='chat-container'>"+
                "<a class='chat-header'>"+
                    "<div class='user-name-container'>"+
                        "<div data-user='"+
                            u.num_id+
                        "' class='status-indicator'></div>"+
                        "<div class='user-name' title='"+u.login+"'>"+
                            ((u.name)?u.name:u.login)+
                        "</div>"+
                    "</div>"+
                    "<div class='user-pic-container'></div>"+
                "</a>"+
            "</li>").attr('data-user',u.login);
        
        j.find('.user-pic-container').append(userPic(u));
        
        if(!rev)
            j.appendTo(chatbox.find('.chat-list'));
        else
            j.insertAfter(chatbox.find('.chat-group'));
        
        j.find('.chat-header').click(clickChat);

        if(u.chat_id)
            j.attr('conversation-id',u.chat_id);
        
        return j;
    }
    function makeConv(c){
        var nm = ((c.name)?c.name:c.conv_users.join(', '));
        var j = 
            $("<li class='chat-container'>"+
                "<a class='chat-header'>"+
                    "<div class='user-name-container'>"+
                        "<div class='status-indicator'></div>"+
                        "<div class='user-name'>"+nm+"</div>"+
                    "</div>"+
                    "<div class='user-pic-container chat-group-pic'></div>"+
                "</a>"+
                "<div class='chat-group-opts' style='display:none;'>"+
                    "<input class='chat-group-name' type='text' value='"+
                        ((c.name)?c.name:'')+
                        "' placeholder='Digite um nome para o Chat' />"+
                    "<ul class='chat-group-user-list'></ul>"+
                    "<div class='chat-group-bts'>"+
                        "<button class='chat-group-get-out'>Sair deste chat</button>"+
                        "<button class='chat-group-sv'>Salvar</button>"+
                    "</div>"+
                "</div>"+
            "</li>");
        if(c.id)
            j.attr('conversation-id',c.id);
    
        j.find('.user-pic-container').click(gOpts);
        j.find('.chat-group-get-out').click(getOut);
        j.find('.chat-group-sv').click(saveUs);
        
        var jj = j.find('.chat-group-user-list');
        for(var i in c.conv_users){
            $("<li class='chat-group-user-item'>"+c.conv_users[i]+"</li>").appendTo(jj);
        }
        j.insertAfter(chatbox.find('.chat-group'));
        j.find('.chat-header').click(clickChat);
        return j;
    }
    
    function uNM(u){
        if(u){
            if(u.name)
                return u.name;
            else
                return u.login;
        }else{
            return '---';
        }
    }
    function userPic(u){
        var pic =  $("<img class='user-pic' title='"+uNM(u)+"' src='"+avatar(u)+"'/>");
        if(!isMobile)
            hoverize(pic);
        return pic;
    }
    function avatar(u,d){
        if(typeof d === 'undefined')
            d = 32;
        return ((u && u.avatar)
                    ?"/media/user/"+d+"/"+u.avatar+""
                    :'/lib/img/def-user.png'
                );
    }
    
    function refreshStatus(){
        cas.ajaxer({
            method:'GET',
            sendto:'chat/user_status',
            andthen:refreshStatus_
        });
        setTimeout(refreshStatus,1000 * 60);
    }
    function chatToggleAll(){
        if(showAll){
            $('.chat-offline').not('.chat-open').hide();
        }else{
            $('.chat-offline').show();
        }
        showAll = !showAll;
        totalResize();
    }
    function refreshStatus_(x){
        var i,u,z;
        for(i in x.data.user){
            u = x.data.user[i];
            u.id = parseInt(u.id);
            user[u.id].online = u.on;
            user[u.id].seen = u.seen;
            z = $('.status-indicator[data-user="'+u.id+'"]')
                    .attr('data-status',(u.on)?'on':'off');
            
            z = z.closest('.chat-container');
            if(!u.on){
                z.addClass('chat-offline');
                if(!z.is('.chat-open') && !showAll)
                    z.hide();
            }else if(u.on){
                z.removeClass('chat-offline');
                z.show();
            }
            
        }
        totalResize();
    }
    function boot(){
        loadUsers();
    }
    function clickChat(){
        
        if(!firstLoad)
            return false;
        var me = $(this).parent(),
            chat_id = me.attr('conversation-id'),
            u = me.attr('data-user'),
            c,grouper = 
                chatbox.find('.chat-group-user-list:visible').first();
        
        //GROUP SELECTION MODE
        if( grouper.length > 0 && u ){
            var i = searchUser(u);
            if(grouper.find('.chat-group-user-item[data-user="'+u+'"]').length <= 0){
                $("<li class='chat-group-user-item new'>"+
                    ((user[i].name)
                        ?user[i].name
                        :user[i].login.split('@')[0]
                    )+
                "</li>").click(function(){$(this).remove();}).attr('data-user',u).appendTo(grouper);
            }
            return false;
        }
        //---------------------
        
        me.toggleClass('chat-open');
        if(typeof chat_id !== 'undefined'){
            chat_id = parseInt(chat_id);
        }
        
        var c = me.find('.chat-conversation');
        if(c.length){
            c.toggle();
        }else{
            c = genConv($("<div class='chat-conversation'>")).appendTo(me);
            if(chat_id)
                loadRecentConversation(chat_id);
        }
        if(c.is(':visible'))
            cleanMyAlerts(c);
        
        totalResize();
    }
    function loadRecentConversation(id){
        cas.ajaxer({
            method:'GET',
            sendme:{
                conversation:id
            },
            sendto:'chat/recent_conversation',
            andthen:loadMsgs
        });
    }
    function loadMsgs(x,readStatus){
        var msg = x.data.xat;
        for(var i in msg)
            putMsg(msg[i],readStatus);
    }
    function loadOlderMsgs(){
        
        if($(this).is('.cWait'))
            return true;
        var p = $(this).closest('.chat-container');
        $(this).addClass('cWait');
        cas.ajaxer({
            method: 'GET',
            etc:{me:$(this)},
            sendme: {
                conversation: p.attr('conversation-id'),
                id: p.find('.chat-message-item').first().attr('msg-id')
            },sendto:'chat/load_older_than',
            complete:function(x){
                x.etc.me.removeClass('cWait');
            },
            andthen: loadMsgAndScroll
        });
    }
    function loadMsgAndScroll(x){
        loadMsgs(x);
        x.etc.me.closest('.chat-container').find('.chat-msg-box').scrollTop(0);
    }
    function loadConvDays(){
        
        if($(this).is('.cWait'))
            return true;
        
        var me = $(this),
            p = me.parent(), 
            z = p.find('.chat-days').toggle();
        
        if( !z.length ){
            me.addClass('cWait');
            z = $("<div class='chat-days'>").appendTo(p);
            cas.ajaxer({
                method: 'GET',
                etc:{me:$(this),elem: z},
                sendme: {
                    conversation: $(this).closest('.chat-container').attr('conversation-id')
                },sendto:'chat/conv_days',
                complete:function(x){
                    x.etc.me.removeClass('cWait');
                },
                andthen: loadConvDays_
            });
        }
        
    }
    function loadConvDays_(x){
        var ds = x.data.ds;
        for(var i in ds){
            $("<a class='chat-this-day'>")
                .attr('data-d',ds[i].t)
                .html(ds[i].d)
                .click(loadMyDay)
                .appendTo(x.etc.elem);
        }
    }
    
    function loadMyDay(){
        $(this).parent().slideToggle();
        cas.ajaxer({
            method:'GET',
            etc:{
                me:$(this)
            },
            sendme:{
                d: $(this).attr('data-d'),
                conversation: $(this).closest('.chat-container').attr('conversation-id')
            },sendto:'chat/chat_from_day',
            andthen:loadMsgAndScroll
        });
    }
    function genConv(x){
        x.empty();
        $("<div class='chat-extra-menu'>")
            .append(
                $("<a class='chat-load-days' title='Carregar histórico'>").click(loadConvDays)
            )
            .append(
                $("<a class='chat-load-older' title='Carregar anterior'>").click(loadOlderMsgs)
            ).appendTo(x),
        $("<div class='chat-msg-box"+((isMobile)?' mobilebox':'')+"'></div>").appendTo(x),
        genMsgInput(
            $("<div class='chat-msg-input'></div>").appendTo(x)
        );
        return x;
    }
    function genMsgInput(x){
        x.empty();
        var z = 
            $('<div>')
                .append(
                    $("<textarea class='chat-textarea' placeholder='Digite sua mensagem' rows=1></textarea>")
                        .keydown(isEnter).focus(checkBack)
                ).appendTo(x);
        return x;
    }
    function checkBack(){
        cleanMyAlerts($(this).closest('.chat-conversation'));
    }
    
    chatbox.find('.chat-group>.chat-textarea')
        .keydown(function isEnterG(e){
            if (parseInt(e.which) === 13 && !e.shiftKey && !e.ctrlKey){
                e.preventDefault();
                var txt = $(this).val();
                if(txt.length < 1 || $(this).is('.cWait'))
                    return true;

                var users = [];
                
                $(this).prev('.chat-group-user-list').children('.chat-group-user-item')
                    .each(function(){
                        if($(this).attr('data-user'))
                            users.push($(this).attr('data-user'));
                    });
                
                if(users.length < 2)
                    return true;
                
                
                $(this).addClass('cWait');
                var s = {
                            to: users,
                            txt:$(this).val(),
                            name:$(this).parent().find('.chat-group-name').val()
                        };

                $('.chat-group-new').removeClass('chat-tgl-on');
                cas.ajaxer({
                    sendme:s,
                    etc:{elem:$(this)},
                    sendto:'chat/text_msg',
                    complete:function(x){
                        x.etc.elem.val('').removeClass('cWait')
                            .closest('.chat-group').hide()
                            .find('.chat-group-name').val('');
                        totalResize();
                        poll();
                    }
                });
            }else if(parseInt(e.which) === 27){
                $('.chat-group-new').removeClass('chat-tgl-on');
                $(this).closest('.chat-group').hide();
                totalResize();
            }
        });
    function isEnter(e){
        
        if (parseInt(e.which) === 13 && !e.shiftKey && !e.ctrlKey){
            e.preventDefault();
            var txt = $(this).val();
            if(txt.length < 1 || $(this).is('.cWait'))
                return true;
            
            var u = $(this).closest('.chat-container');
            var user = u.attr('data-user');
            var conversation = u.attr('conversation-id');
            var s = {to: user,txt:$(this).val()};
            
            if(conversation !== 'undefined')
                s.conversation = conversation;
            
            cas.ajaxer({
                sendme:s,
                etc:{elem:u,unlock:$(this).addClass('cWait'), host: user},
                sendto:'chat/text_msg',
                andthen:msgSent
            });
            
            $(this).val("");
            
        }else if(parseInt(e.which) === 27){
            $(this).closest('.chat-conversation').hide();
            totalResize();
        }
    }
    function searchUser(u){
        for(var i in user)
            if(user[i].login === u)
                return i;
        return null;
    }
    function msgSent(x){
        x.etc.unlock.removeClass('cWait');
        if(x.data.conversation){
            x.etc.elem.attr('conversation-id',x.data.conversation);
            loadMsgs(x);
        }
    }
    function getConversationChatBox(conversation){
        var x = null;
        var id = conversation.id;
        
        if(id)
            x = $('.chat-container[conversation-id="'+id+'"]');
    
        if(!x || !x.length){
            if(conversation.user){
                x = $('.chat-container[data-user="'+conversation.user+'"]')
                        .attr('conversation-id',id);
            }else{
                makeConv(conversation,chatbox.find('.chat-list'));
            }
        }
            
        if( !x.children('.chat-conversation').length 
            || !x.children('.chat-conversation').is(':visible')
        ){
            x.find('.chat-header').trigger('click');
        }
        
        if(x.length)
            x.show();
        
        return x;
    }
    function makeUseOf(u){
        if(!u.num_id)
            return false;
        
        u.num_id = parseInt(u.num_id);
        if(!user[u.num_id]){
            user[u.num_id] = u;
            if(u.login !== cas.user.login){        
                var x = makeUser(u,true);
                genConv($("<div class='chat-conversation'>")).appendTo(x);
            }
        }
    }
    var msghovert;
    function putMsg(x,readStatus){
        
        var u = x.user, usr,
            id = parseInt(x.id),
            txt = x.txt.autoLink({target: "_blank"}), repo;
        
        if(x.this_user){
            makeUseOf(x.this_user);
            if(x.this_user.login === u){
                usr = x.this_user;
            }
        }else{
            var i = searchUser(u);
            if(i === null)
                usr = {
                    login: u
                };
            else
                usr = user[i];
        }
        
        var p = getConversationChatBox(
                    {id:x.conversation,
                        user:((x.conv_type === 'priv')?u:null),
                            name:x.conv_name,
                                conv_name:x.conv_name,
                                    conv_users:x.conv_users}
                ), 
                c = p.find('.chat-msg-box');
        
        if( c.length > 0 && c.find('.chat-message-item[msg-id="'+id+'"]').length === 0 ){
            
            if(id > maxID)
                maxID = id;
            
            var repo = null;
            var bros = c.find('.chat-message-item');
            var first = bros.first(),before = false;
            
            bros.each(function(){
                var $this = $(this),
                    this_id = parseInt($this.attr('msg-id'));
                
                if(this_id > id)
                    return false;
                
                repo = $this;
            });
            
            
            if(
                (repo
                && repo.attr('user') === u
                && repo.attr('tgroup') === x.tgroup
                )
                
                ||
                
                (!repo && first.length
                && first.attr('user') === u
                && first.attr('tgroup') === x.tgroup
                )
            ){
                if(repo)
                    repo = repo.parent();
                else{
                    repo = first.parent();
                    before = true;
                }
            }else{
                var elem = 
                    $("<div class='chat-message'>")
                        .attr('user',usr.login)
                        .attr('tgroup',x.tgroup)
                        .append(
                            $("<div class='message-pic'>").append( userPic(usr) )
                        );
                
                if(repo){
                    elem.insertAfter(repo.parent().parent());
                }else{
                    elem.prependTo(c);
                }
                
                repo = $("<div class='message-content'></div>").appendTo(elem);
                var msgh = x.fD+' - '+x.fT+', '+uNM(usr);
                $("<div class='message-header'>"+msgh+":</div>").attr('title',msgh).insertBefore(repo);
            }
            
            var t = $("<div class='chat-message-item'>")
                        .attr('user',usr.login)
                        .attr('tgroup',x.tgroup)
                        .attr('msg-id',id).html(txt);
            if(!isMobile){
                t.hover(
                    function(){
                        var me = $(this);
                        clearTimeout(msghovert);
                        msghovert = setTimeout(function(){
                            me.addClass('chat-msg-onhover').attr('title','Clique para ver detalhes');
                        },1000 * 2);
                    },function(){
                        clearTimeout(msghovert);
                        $(this).removeClass('chat-msg-onhover').removeAttr('title');
                    });
            }
            if(before)
                t.prependTo(repo);
            else
                t.appendTo(repo);
            
            if(!isMobile && !c.is(':hover') )
                c.scrollTop(c[0].scrollHeight);
            
            if(isMobile && c.is(':visible'))
                totalResize();
            
            
            if( readStatus === 2 || p.find('.chat-textarea').is(':focus') || !usr.num_id ){
                setTimeout(function(){
                    pendingCheck.push(parseInt(id));
                },1000 * 5);
            }
            
            if( readStatus === 1 && ! x.msg_checked && ! x.mine ){
                t.addClass('unread');
                convAlert(x,c);
                chatMsgDesktopNotification(x,usr);
                pushChatAlert(id);
            }
            
        }else{
            pendingCheck.push(parseInt(id));
        }
    }
    var pendingCheck = [];
    function checker(){
        var msgs = pendingCheck.splice(0,30);
        if(msgs.length)
            cas.ajaxer({
                sendme:{msg:msgs},
                sendto:'chat/msg_check'
            });
    }
    checker();
    setInterval(checker,1000);
    function chatMsgDesktopNotification(x,user){
        if(!window.Notification)
            return false;

        var txt = ultimateStripTags(x.txt);

        var opts = {
            icon: avatar(user),
            body: txt,
            tag: 'chat-msg-'+x.id,
            onclick: function(){
                window.focus();
                if(!chatVisible){
                    chatShow();
                }
                chatbox.trigger('mouseenter');
                var y = getConversationChatBox({id:x.conversation});
                focusOnConv(y);                
            }
        };

        cas.createDesktopNotification(
            ((user.name)?user.name:user.login)+': ', //title
            opts //options
        );
      }
    function convAlert(x,parent){
        var conv = $('.conv-alert[conv="'+x.conversation+'"]');
        if(!conv.length){
            var u = ((x.conv_type === 'priv')
                            ?user[
                                searchUser(
                                    $('.chat-container[conversation-id="'+x.conversation+'"]')
                                        .attr('data-user')
                                )
                            ]
                            :x.this_user
                        );
            conv = 
                $("<span class='conv-alert'>").attr('conv',x.conversation)
                    .append(
                        "<span class='fake-pic' style='"+
                            "background: url("+avatar(u)+") center no-repeat white'>&zwnj;</span>"
                    )
                    .append("<span class='conv-alert-count'></span>");
                    
            $('.chat-bottom-inner').append(conv);
        }
        conv.find('.conv-alert-count').html(parent.find('.unread').length);
    }
    function checkAlerts(){
        if(!chattoggler)
            return false;
        if(alerts.chat.length > 0){
            chattoggler.find('.chat-icon-txt')
                    .html("<span class='chat-alert-count'>"+alerts.chat.length+"</span>&zwnj;");
        }else if(alerts.chat.length === 0){
            chattoggler.find('.chat-icon-txt')
                    .html("&zwnj;");
        }
    }
    checkAlerts();
    setInterval(checkAlerts,1000);
    function pushChatAlert(id){
        id = parseInt(id);
        var pos = alerts.chat.indexOf(id);
        if(pos === -1){
            alerts.chat.push(id);
        }
    }
    function removeAlert(id){
        cas.kill(parseInt(id),alerts.chat);
        pendingCheck.push(parseInt(id));
    }
    function cleanMyAlerts(x){
        var id;
        
        if(x.is('.chat-container')){
            id = x.attr('conversation-id');
        }else{
            id = x.closest('.chat-container').attr('conversation-id');
        }
        
        if(!id)
            return false;
        
        x.find('.unread').each(function(){
            $(this).removeClass('unread');
            removeAlert($(this).attr('msg-id'));
        });
        
        $('.conv-alert[conv="'+id+'"]').fadeOut(function(){
            $(this).remove();
        });
    }
    
    var pollT,pollRunning = false;
    
    function poll(){
        if(pollRunning)
            return true;
        
        pollRunning = true;
        clearTimeout(pollT);
        
        cas.ajaxer({
            method:'GET',
            sendme:{
                last_one:maxID
            },
            silent_load: true,
            sendto:'chat/unchecked',
            complete: function(){
                pollRunning = false;
                clearTimeout(pollT);
                pollT = setTimeout(poll,500);
            },error:function(){
                console.log('nothing to see here');
            },
            andthen: function(x){
                loadMsgs(x,1);
                firstLoad = true;
                if(maxID === -1)
                    maxID = 0;
            }
        });
    }
    function seemBy(e){
        e.preventDefault();
        var mid = $(this).attr('msg-id');
        cas.ajaxer({
            method:'GET',
            etc:{
                me:$(this)
            },
            sendme:{
                id: mid
            },sendto:'chat/seem_by',
            andthen:function(x){
                var us = x.data.us;
                var t = $("<table class='chat-seemby-list'>")
                            .append('<tr><td colspan=2>Mensagem enviada às <br><b>'+
                                x.data.x.timestamp+'</b></td></tr>');
                for(var i in us){
                    t.append(
                        "<tr>"+
                            "<td>Visualizado por <b>"+us[i].user+"</b></td>"+
                            "<td><i>"+us[i].timestamp+"</i></td>"+
                        "</tr>");
                }
                cas.weirdDialogSpawn(x.etc.me.offset(),t);
            }
        });
    }
    $('.chat-bottom').on('click','.conv-alert',goToAlert);
    chatbox.on('focus','.chat-textarea',textMax);
    if(!isMobile){
        chatbox.on('click','.chat-msg-onhover',seemBy);
    }
    chatbox.find('.hide-chat').click(chatHide);
    chatbox.find('.chat-hide-all').click(hideAll);
    chatbox.find('.chat-toggle-all').click(chatToggleAll);
    chatbox.find('.chat-group-new').click(groupNew);
    chatbox.find('.chat-tgl').click(function(){
        $(this).toggleClass('chat-tgl-on');
    });
    setInterval(totalResize,1000);
    boot();
    
});
