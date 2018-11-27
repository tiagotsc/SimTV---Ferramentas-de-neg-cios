/*  SE A SESSÃO ESTIVER DEFINIDA PARA ABRIR TELA DE CHAT */
if(chatAberto == 'sim'){
    $('.chat-conteudo').css('display', 'block');
}else{ /* SE A SESSÃO ESTIVER DEFINIDA PARA NÃO ABRIR TELA DE CHAT */
    $('.chat-conteudo').css('display', 'none');
}

$("#div-msg-anterior span").click(function(){
   //alert(dataChatPaginacao);
   //alert($("#user-conversa").attr("idUsuarioConversa"));
   //alert($("#user-conversa").attr("tipoUsuarioConversa"));
   
   $.ajax({
          type: "POST",
          url: baseUrl+'chat/adicionaConversaAnterior',
          data: {
            dataCorrente: dataChatPaginacao,
            user: $("#user-conversa").attr("idUsuarioConversa"),
            tipo: $("#user-conversa").attr("tipoUsuarioConversa")
          },
          dataType: "json",
          error: function(res) {
            //alert(res.status);
          },
          success: function(res) {
            //alert(res.status);
            if(res.msgs.length > 0){
                
                if(res.anterior != '' && res.anterior != null){
                    
                    dataChatPaginacao = res.anterior;
                    $("#div-msg-anterior").css('display', 'block');
                    
                }else{
                    $("#div-msg-anterior").css('display', 'none');
                }
                
                var msgConteudo = '';
                
                $.each(res.msgs, function() {
                    
                    // Se o originador da mensagem é você
                    if(this.cd_origem == sessionUserId){
                        
                            msgConteudo += '<li idMsg="'+this.cd_chat_msg+'" class="clearfix">';
                            msgConteudo += '<div class="message-data align-right">';
                            msgConteudo += '<span class="message-data-time" >'+this.hora_envio+', '+this.data_envio+'</span> &nbsp; &nbsp;';
                            msgConteudo += '<span class="message-data-name" >Voc&ecirc;</span> <i class="fa fa-circle me"></i>';
                            msgConteudo += '</div>';
                            msgConteudo += '<div class="message other-message float-right">';
                            
                            if(this.mensagem_tipo == 'arquivo'){
                                
                                var dadosFile = $().crypt({
                                    method: "b64enc",
                                    source: this.cd_chat_msg
                                });
                                
                                msgConteudo += '<a title="Baixar" class="linkArquivo" file="'+dadosFile+'" href="'+baseUrl+'chat/downloadFile/'+dadosFile+'">'+this.mensagem+'</a>';
                            }else{
                                msgConteudo += this.mensagem;
                            }
    
                            msgConteudo += '</div>';
                            msgConteudo += '</li>';
                            
                    }else{
                        
                        if(this.cd_destino == sessionUserId && this.tipo_destino == 'user'){
                            if(this.lida == 'N'){
                                var classLida = 'nao-lida';
                                var lida = '';
                            }else{
                                var classLida = 'lida';
                                var lida = '<span class="glyphicon glyphicon-ok chat-icone-lida" aria-hidden="true"></span>';
                            }
                        }
                        
                        if(this.tipo_destino == 'dp'){ 
                            if(this.lida == 'S'){
                                var classLida = 'lida';
                                var lida = '<span class="glyphicon glyphicon-ok chat-icone-lida" aria-hidden="true"></span>';
                            }else{
                                var classLida = 'nao-lida';
                                var lida = '';
                            }                        
                        }
                        
                            msgConteudo += '<li class="'+classLida+'" idMsg="'+this.cd_chat_msg+'" >';
                            msgConteudo += '<div >';
                            msgConteudo += '<div class="message-data">';
                            msgConteudo += '<span class="message-data-name"><i class="fa fa-circle online"></i> '+this.nome_usuario+'</span>';
                            msgConteudo += '<span class="message-data-time">'+this.hora_envio+', '+this.data_envio+'</span>';
                            msgConteudo += '</div>';
                            msgConteudo += '<div id="msgStatus'+this.cd_chat_msg+'" class="message my-message">';
                            
                            if(this.mensagem_tipo == 'arquivo'){
                                
                                var dadosFile = $().crypt({
                                    method: "b64enc",
                                    source: this.cd_chat_msg
                                });
                                
                                msgConteudo += '<a title="Baixar" href="'+baseUrl+'chat/downloadFile/'+dadosFile+'">'+this.mensagem+'</a>';
                            }else{
                                msgConteudo += this.mensagem;
                            }
                            
                            msgConteudo += lida;                                                
                            msgConteudo += '</div>';
                            msgConteudo += '</div>';
                            msgConteudo += '</li>';
                        
                    }
        
                });
                
                // Store reference to top message
                var firstMsg = $('#chat-msg-historico li:first');
                // Store current scroll/offset
                var curOffset = firstMsg.offset().top - $('.chat-history').scrollTop();
                
                //$("#chat-msg-historico").prepend(msgConteudo);
                
                firstMsg.before(msgConteudo);
                
                $('.chat-history').scrollTop(firstMsg.offset().top-curOffset);
                
                $("#qtd-user-conversa").html($("#chat-msg-historico li").length+' mensagens.');
    
            }
            
          }
        });
        
        $(this).verificaLidas();
        //$('.chat-history').stop().animate({scrollTop: $(".nao-lida").first().offset().top - 300}, 1000,'easeInOutExpo');
   
});

/*
 Desloca o ponteiro para o final da da div editável
*/
function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined"
            && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}

$(".emotion").click(function(){
    $('div[contenteditable=true]').append($(this).html());
    placeCaretAtEnd(document.getElementById("chatWrite"));
});

/* ACIONA A VERIFICAÇÃO DE MENSAGENS NÃO LIDAS AO ROLAR O SCROLL PARA BAIXO */
$("#emotionOpen").click(function(){
   $("#chatEmotion").toggle();
});

// EXECUTA A CADA SEGUNDO
self.setInterval(
    function(){
        
        // Se o andamento estiver negativo entra nas funções intervaladas, caso contrário espera o termino das funções
        if(!verifEmAndamento){
        
            verifEmAndamento = true;
        
            // Verifica status do usuário
            $.ajax({
              type: "POST",
              url: baseUrl+'chat/statusUsuarios',
              data: {
                dataUltimoStatus: dataReqStatus
              },
              dataType: "json",
              error: function(res) {
                //alert(res.status);
              },
              success: function(res) {
                
                if(res.length > 0){
                    
                    dataReqStatus = res[res.length - 1].data_chat_usuario;
                    
                    $.each(res, function() {
                        var retStatus = '<img src="'+baseUrl+'/assets/chat/ico/'+this.status_chat_usuario+'.png" />';
                            retStatus += this.status_chat_usuario.toLowerCase();
                            
                            $(".idUserConversaStatus"+this.cd_usuario+" img").attr('src', baseUrl+'/assets/chat/ico/'+this.status_chat_usuario+'.png');
                            $(".idUserConversaStatus"+this.cd_usuario+" span").html(this.status_chat_usuario.toLowerCase());
                            
                        $("#"+this.cd_usuario+" .status").html(retStatus);  
                        //alert(this.nome_usuario);
                        if(this.status_chat_usuario == 'ONLINE'){
                        
                            if($('select[name=chatStatus]').val() != 'OFFLINE'){
                                $.notify(this.nome_usuario+" online", {
                                  className:'success',
                                  //clickToHide: false,
                                  //autoHide: false,
                                  globalPosition: 'bottom right'
                                });
                            }
                        
                        }
         
                    });
                }
                            
              }
            });
            
            // Verifica a quantidade de novas mensagens e conversas recentes
            $.ajax({
              type: "POST",
              url: baseUrl+'chat/qtdNewMsgPorConversa',
              data: {
                dataUltimoNovaConversa: chatRequestNewConversa
              },
              dataType: "json",
              error: function(res) {
                //alert(dump(res));
              },
              success: function(res) {
                
                $("#abaConversa strong").remove();
                $(".name strong").html('');
                
                if(res.length > 0){
                    
                    //chatRequestNewConversa = res[res.length - 1].data_envio;
                    chatRequestNewConversa = res[0].data_envio;
                    
                   $("#abaConversa").append('<strong class="style-new-msg">'+res.length+'<span class="glyphicon glyphicon-comment style-new-msg" aria-hidden="true"></span></strong>'); 
                   $("#chat-menu-left").html('<strong class="style-new-msg">'+res.length+'<span class="glyphicon glyphicon-comment style-new-msg" aria-hidden="true"></span></strong>');
                }else{
                    $("#chat-menu-left").html('');
                }
                
                $.each(res, function() { 
                    // Se elemento não existe
                    if(!$(".new-msg"+this.cd_origem).length && this.cd_origem != sessionUserId){
                        
                        htmlRecentes = '<li class="clearfix ui-state-default">';
                       	htmlRecentes += '<!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />-->';
                        htmlRecentes += '<div class="about">';
                        
                        if(this.tipo == 'user'){
                        
                            htmlRecentes += '<div class="name">';
                            //htmlRecentes += this.nome_usuario;
                            
                            if(this.favorito == 1){
                                var favoritoClass = 'favorito-sim';
                            }else{
                                var favoritoClass = 'favorito-nao';
                            }
                            
                            htmlRecentes += '<span class="click" onclick="$(this).abreChat('+this.cd_usuario+',\''+this.tipo+'\');">'+this.nome_usuario+'</span>';
                            htmlRecentes += '<span idUserConversa="'+this.cd_usuario+'" dpFav="'+this.cd_departamento+'" unFav="'+this.cd_unidade+'" tipo="userList" class="glyphicon glyphicon-star favorito-status '+favoritoClass+'" aria-hidden="true"></span>';
                            htmlRecentes += '&nbsp<strong class="style-new-msg new-msg'+this.cd_usuario+'"></strong>';
                            htmlRecentes += '</div>';
                            htmlRecentes += '<div class="status idUserConversaStatus'+this.cd_usuario+'">';
                            htmlRecentes += '<img src="'+baseUrl+'/assets/chat/ico/'+this.status_chat_usuario+'.png" />';
                            htmlRecentes += this.status_chat_usuario.toLowerCase();
                            htmlRecentes += '</div>';
                        
                        }else{
                            
                            htmlRecentes += '<div class="name">';
                            //htmlRecentes += this.nome_usuario;
                            htmlRecentes += '<span class="click" onclick="$(this).abreChat('+this.cd_usuario+',\''+this.tipo+'\');">'+this.nome_usuario+'</span>';
                            htmlRecentes += '<span idUserConversa="'+this.cd_usuario+'" dpFav="'+this.cd_departamento+'" unFav="'+this.cd_unidade+'" tipo="userList" class="glyphicon glyphicon-star favorito-nao" aria-hidden="true"></span>';
                            htmlRecentes += '&nbsp<strong class="style-new-msg new-msg'+this.cd_usuario+'"></strong>';
                            htmlRecentes += '</div>';
                            htmlRecentes += '<div class="status idUserConversaStatus'+this.cd_usuario+'">';
                            htmlRecentes += '<br>';
                            htmlRecentes += this.status_chat_usuario.toLowerCase();
                            htmlRecentes += '</div>';
                            
                        }
                        
                        htmlRecentes += '</div>';
                        htmlRecentes += '</li>';                    
                            
                        
                        htmlRecentes += '</ul>'; 
                        
                        $("#recentes").append(htmlRecentes);
                        
                        if(this.tipo != 'user'){
                            $("span[idUserConversa='"+this.cd_usuario+"']").css('cursor', 'not-allowed');
                        }
                        
                    }
                    $(".new-msg"+this.cd_usuario).html(this.qtd_msg+'<span class="glyphicon glyphicon-comment style-new-msg" aria-hidden="true"></span>');
                });
                
                $("#status-user-favorito, .favorito-status").unbind("click");
                
                $(this).iniDragDrop();    
                $(this).dragDropArrastarDp(); 
                $(this).addRevFavorito(); 
                
              }
            });
            
            // Se a tela de conversa estiver aberta pega / verifica novas mensagens
            if($("#user-conversa").attr("idUsuarioConversa") != ''){
                $.ajax({
                  type: "POST",
                  url: baseUrl+'chat/pegaNovasMsgs',
                  data: {
                    dataUltimoNovaMsg: chatRequestNewMsg,
                    origem: $("#user-conversa").attr("idUsuarioConversa"),
                    origem_tipo: $("#user-conversa").attr("tipoUsuarioConversa")
                  },
                  dataType: "json",
                  error: function(res) {
                    //alert(res.status);
                  },
                  success: function(res) {
                    
                    if(res.length > 0){
                        
                        chatRequestNewMsg = res[res.length - 1].data;
                        
                        $.each(res, function() {
                        
                            var msgConteudo = '<li class="nao-lida" idMsg="'+this.cd_chat_msg+'" >';
                            	msgConteudo += '<div >';
                            	msgConteudo += '<div class="message-data">';
                            	msgConteudo += '<span class="message-data-name"><i class="fa fa-circle online"></i> '+this.nome_usuario+'</span>';
                            	msgConteudo += '<span class="message-data-time">'+this.hora_envio+', '+this.data_envio+'</span>';
                            	msgConteudo += '</div>';
                            	msgConteudo += '<div id="msgStatus'+this.cd_chat_msg+'" class="message my-message">';
                            	
                                if(this.mensagem_tipo == 'arquivo'){
                            
                                    var dadosFile = $().crypt({
                                        method: "b64enc",
                                        source: this.cd_chat_msg
                                    });
                                    
                                    msgConteudo += '<a class="linkArquivo" file="'+dadosFile+'" title="Baixar" href="'+baseUrl+'chat/downloadFile/'+dadosFile+'">'+this.mensagem+'</a>';
                                }else{
                                    msgConteudo += this.mensagem;
                                }
                                  
                            	msgConteudo += '</div>';
                            	msgConteudo += '</div>';
                            	msgConteudo += '</li>';
                            	
                            $("#chat-msg-historico").append(msgConteudo);
                        
                        });
                        $(this).verificaLidas();
                        
                        $("#qtd-user-conversa").html($("#chat-msg-historico li").length+' mensagens.');
                        
                    }
                  }
                });
                
                $.post( baseUrl+"chat/consultaDinamica", { 
                        user: $("#user-conversa").attr("idUsuarioConversa"),
                        tipo: $("#user-conversa").attr("tipoUsuarioConversa") 
                    }, function( data ) {
                      /*console.log( data.name ); // John
                      console.log( data.time ); // 2pm*/
                      if(data.status_escrita == ''){
                        $("#chat-status-escrita").html('');
                      }else{
                        $("#chat-status-escrita").html(data.status_escrita);
                      }
                }, "json");
                
            }
            
            verifEmAndamento = false;
        
        }
    }
,1000);

// EXECUTA A CADA 2 SEGUNDOS
self.setInterval(
    
    function(){
        
        // Se o usuário estiver conversando com alguém
        if($("#user-conversa").attr("idUsuarioConversa") != ''){
    
            // Verifica o status da escrita com quem esta conversando
            $.post( baseUrl+"chat/defineDinamica", { 
                statusEscrita: " ", 
                user: $("#user-conversa").attr("idUsuarioConversa"), 
                tipo: $("#user-conversa").attr("tipoUsuarioConversa") 
                } );
            
        }
    }    
,2000);

/* MOSTRA E OCULTA DEPARTAMENTO / UNIDADE */
//$(".chatDp").css('display', 'none');
function mostrarOcultar(link, div){
    $(div).toggle(0,function(){
		if($(this).css('display')=='none'){
			$(link).html('<strong>+</strong>');//change the button label to be 'Show'
		}else{
			$(link).html('<strong>-</strong>');//change the button label to be 'Hide'
		}
	});
}

/* PESQUISA USUÁRIO */
$("#chat-search").keyup(function(){
    
    if($(this).val() != ''){
        $.ajax({
          type: "POST",
          url: baseUrl+'chat/pesquisaUsuario',
          data: {
            usuario: $(this).val()
          },
          dataType: "json",
          error: function(res) {
            alert('erro');
          },
          success: function(res) {
          
            if(res.length > 0){
                var resPesq = '<h5><strong>Pesquisa</strong></h5>';
                    resPesq += '<ul class="connectedSortable dragDrop list chatDp">';
                $.each(res, function() {
                    
                resPesq += '<li class="click clearfix ui-state-default" onclick="$(this).abreChat('+this.cd_usuario+',\'user\');">';
               	resPesq += '<!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />-->';
                resPesq += '<div class="about">';
                resPesq += '<div class="name">';
                resPesq += this.nome_usuario;
                /*
                if(this.favorito == 1){
                    var favoritoCor = 'color: #FFA500;';
                }else{
                    var favoritoCor = 'color: #D8DADF;';
                }
                
                resPesq += '<span onclick="$(this).abreChat('+this.cd_usuario+',\'user\');">'+this.nome_usuario+'</span>';
                resPesq += '<span idUserConversa="'+this.cd_usuario+'" dpFav="'+this.cd_departamento+'" unFav="'+this.cd_unidade+'" tipo="userList" style="float: left; '+favoritoCor+'" class="glyphicon glyphicon-star favorito-status" aria-hidden="true"></span>';
                */
                resPesq += '</div>';
                resPesq += '<div class="status">';
                resPesq += '<img src="'+baseUrl+'/assets/chat/ico/'+this.status_chat_usuario+'.png" />';
                resPesq += this.status_chat_usuario.toLowerCase();
                resPesq += '</div>';
                resPesq += '</div>';
                resPesq += '</li>';                    
                    
                });
                resPesq += '</ul>'; 
                
                $("#searchRes").html(resPesq);
                
                $("#status-user-favorito, .favorito-status").unbind("click");
                
                $(this).iniDragDrop();    
                $(this).dragDropArrastarDp(); 
                $(this).addRevFavorito();          
                
            }else{
                $("#searchRes").html('');
            }
            
          }
        });
    }else{
        $("#searchRes").html('');
    }
});

$(document).ready(function(e) {
    
    $("span[tipo='dpList']").css('cursor', 'not-allowed');
    $("#status-user-favorito").css('display', 'none');
    
    /* INICIA ARRASTAR E SOLTA NAS LISTAS CONFIGURADAS */
    $(this).iniDragDrop();
    
    /* ATIVA A FUNÇÃO SOLTAR NAS LISTAS DE DEPARTAMENTO */
    $(this).dragDropArrastarDp();
    
    /* ATIVA A FUNÇÃO ADICIONAR OU REMOVER FAVORITO AO CLICAR */
    $(this).addRevFavorito();
    
    $('#chatStatus option[value="'+chatSeuStatus+'"]').prop('selected', true);
    try {
    $("#chatStatus").msDropDown();
    } catch(e) {
    //alert(e.message);
    }
});

/* ATUALIZA STATUS DO USUÁRIO */
$("#chatStatus").change(function(){
   if($(this).val() != ''){

        $.ajax({
          type: "POST",
          url: baseUrl+'chat/atualizaStatusUsuario',
          data: {
            chatStatus: $(this).val()
          },
          dataType: "json",
          error: function(res) {
            //alert(res.status);
          },
          success: function(res) {
            //alert(res.status);
          }
        });  
               
   } 
});

/* ARRASTAR E SOLTAR - INICIO */
$('#myfavorites').droppable({
    drop: function(event, ui){
        
        //alert(ui.draggable.text());
        
        /* PEGA TODOS OS ELEMENTOS DA LISTA DESTINATÁRIA
        var list_sortable = $(this).sortable('toArray').toString();
        alert(list_sortable);
        */
        
        /* PEGA O ID DO ELEMENTO ARRASTADO ATUALMENTE */
        //alert($(ui.draggable).attr("id"));
        
        $.ajax({
          type: "POST",
          url: baseUrl+'chat/addFavoritos',
          data: {
            user: $(ui.draggable).attr("id")
          },
          dataType: "json",
          error: function(res) {
            //alert(res.status);
          },
          success: function(res) {
            //alert(res.status);
            $("#status-user-favorito").removeClass("favorito-nao").addClass('favorito-sim');
            $(".favorito-status[idUserConversa='"+$(ui.draggable).attr("id")+"']").removeClass("favorito-nao").addClass("favorito-sim");
          }
        });

    }
});

/* ATIVA A FUNÇÃO SOLTAR NAS LISTAS DE DEPARTAMENTO */
$.fn.dragDropArrastarDp = function() {     
    $('.chatDp').droppable({
        drop: function(event, ui){
            //alert(ui.draggable.text());
            
            /* PEGA TODOS OS ELEMENTOS DA LISTA DESTINATÁRIA
            var list_sortable = $(this).sortable('toArray').toString();
            alert(list_sortable);
            */
            
            /* PEGA O ID DO ELEMENTO ARRASTADO ATUALMENTE */
            
            $.ajax({
              type: "POST",
              url: baseUrl+'chat/removeFavoritos',
              data: {
                user: $(ui.draggable).attr("id")
              },
              dataType: "json",
              error: function(res) {
                //alert(res.status);
              },
              success: function(res) {
                //alert(res.status);
                $("#status-user-favorito").removeClass("favorito-sim").addClass('favorito-nao');
                $(".favorito-status[idUserConversa='"+$(ui.draggable).attr("id")+"']").removeClass("favorito-sim").addClass("favorito-nao");
              }
            });
        }
    });
    
}

/* INICIA ARRASTAR E SOLTA NAS LISTAS CONFIGURADAS */    
$.fn.iniDragDrop = function() {  
  $(function() {
    $( ".dragDrop" ).sortable({
      connectWith: ".connectedSortable",
      cursor: 'move'      
    }).disableSelection();
  });
}

/* ADICIONA OU REMOVE FAVORITOS AO CLICAR NA ESTRELA */
$.fn.addRevFavorito = function() {
    $("#status-user-favorito, .favorito-status").click(function(){
        
        // É favorito portanto remove ele da lista ao clicar
        if($(this).css('color') == 'rgb(255, 165, 0)'){
            
            var dpFavorito = $(this).attr("dpFav");
            var unFavorito = $(this).attr("unFav");
            
            // Usuário com quem esta conversando
            if($(this).attr("tipo")== 'userWrite'){ 
                $(".favorito-status[idUserConversa='"+$(this).attr("idUserConversa")+"']").removeClass("favorito-sim").addClass("favorito-nao");
            }else if($(this).attr("tipo")== 'userList'){
                $(".favorito-status[idUserConversa='"+$(this).attr("idUserConversa")+"']").removeClass("favorito-sim").addClass("favorito-nao");
            }else{ // Usuário da lista
                
                // Se usuário da lista for igual ao selecionado para conversar
                if($(this).attr("idUserConversa") == $("#status-user-favorito").attr("idUserConversa")){
                    $("#status-user-favorito").removeClass("favorito-sim").addClass("favorito-nao"); 
                }
            }
            
            $(this).removeClass("favorito-sim").addClass("favorito-nao");
            $("#"+$(this).attr("idUserConversa")).appendTo("#"+dpFavorito+"-"+unFavorito);
            $.post( baseUrl+'chat/removeFavoritos', { user: $(this).attr("idUserConversa") } );
            
        }else{ // Não é favorito portanto adiciona ele na lista ao clicar
            
            // Usuário com quem esta conversando
            if($(this).attr("tipo")== 'userWrite'){
                $(".favorito-status[idUserConversa='"+$(this).attr("idUserConversa")+"']").removeClass("favorito-nao").addClass("favorito-sim");
            }else if($(this).attr("tipo")== 'userList'){
                $(".favorito-status[idUserConversa='"+$(this).attr("idUserConversa")+"']").removeClass("favorito-nao").addClass("favorito-sim");
            }else{  // Usuário da lista
            
                // Se usuário da lista for igual ao selecionado para conversar
                if($(this).attr("idUserConversa") == $("#status-user-favorito").attr("idUserConversa")){
                    $("#status-user-favorito").removeClass("favorito-nao").addClass("favorito-sim");
                }
            }
            
            $(this).removeClass("favorito-nao").addClass("favorito-sim");
            $("#"+$(this).attr("idUserConversa")).appendTo("#myfavorites");
            $.post( baseUrl+'chat/addFavoritos', { user: $(this).attr("idUserConversa") } );
            
        }
    });
}

/* ABRE CHAT AO CLICAR NO USUARIO */
$.fn.abreChat = function(id, tipo) {
    
    $('div[contenteditable=true]').html('');
    
    // Armazena o ID do usuário com quem esta conversando
    var conversaRecente = $("#user-conversa").attr("idUsuarioConversa");
    
    $("#status-user-favorito").removeClass("favorito-nao favorito-sim");
    
    $("#user-conversa").attr("tipoUsuarioConversa", tipo);
    $(".chat").css('width', '490px');
    $(".chat-box").css('display', 'block');
    $("#status-user-favorito").css('display', 'block');
    $.ajax({
      type: "POST",
      url: baseUrl+'chat/abreConversa',
      data: {
        user: id,
        tipo: tipo
      },
      dataType: "json",
      error: function(res) {
        alert('Erro ao abrir chat');
      },
      success: function(res) {
        
        if(tipo == 'user'){ // Conversa com usuário
            
            $("#status-user-favorito").css("display", "block");
            //$("#status-user-favorito").css('cursor', 'pointer');
            
            $("#user-conversa").html(res.usuario.nome_usuario);
            $("#user-conversa").attr("idUsuarioConversa", res.usuario.cd_usuario);
            $("#userEnviaFile").val(res.usuario.cd_usuario);
            $("#userTipoEnviaFile").val(tipo);
            $("#qtd-user-conversa").html(res.msgs.length+" mensagens.");        
            if(res.usuario.favorito == 1){ 
                $("#status-user-favorito").addClass("favorito-sim");
            }else{
                $("#status-user-favorito").addClass("favorito-nao");
            }
            
            // Alimenta o cabeçalho com as informações da pessoa com quem esta conversando
            $("#status-user-favorito").attr("idUserConversa", res.usuario.cd_usuario);
            $("#status-user-favorito").attr("dpFav", res.usuario.cd_departamento);
            $("#status-user-favorito").attr("unFav", res.usuario.cd_unidade);
            
            $("#status-user-favorito, .favorito-status").unbind("click");
            
            $(this).addRevFavorito();
        
        }else{ // Conversa com departamento - unidade
            
            $("#status-user-favorito").css("display", "none");
            //$("#status-user-favorito").css('cursor', 'not-allowed');
            
            $("#user-conversa").html(res.departamento.nome_departamento+' - '+res.unidade.nome);
            $("#user-conversa").attr("idUsuarioConversa", res.departamento.cd_departamento+'-'+res.unidade.cd_unidade);
            $("#userEnviaFile").val(id);
            $("#userTipoEnviaFile").val(tipo);
            $("#qtd-user-conversa").html(res.msgs.length+" mensagens.");        
            $("#status-user-favorito").addClass("favorito-nao");
            
            // Alimenta o cabeçalho com as informações da pessoa com quem esta conversando
            $("#status-user-favorito").attr("idUserConversa", '');
            $("#status-user-favorito").attr("dpFav", res.departamento.cd_departamento);
            $("#status-user-favorito").attr("unFav", res.unidade.cd_unidade);
            
            $("#status-user-favorito, .favorito-status").unbind("click");
            
        }
        
        $("#chat-msg-historico").html('');
        
        // Se existe conversa, mostra o conteúdo
        if(res.msgs.length > 0){
            
            if(res.anterior != ''  && res.anterior != null){ 
                dataChatPaginacao = res.anterior; 
                $("#div-msg-anterior").css('display', 'block');
            }else{
                $("#div-msg-anterior").css('display', 'none');
            }
            
            $.each(res.msgs, function() {
                
                // Se o originador da mensagem é você
                if(this.cd_origem == sessionUserId){
                    
                    var msgConteudo = '<li idMsg="'+this.cd_chat_msg+'" class="clearfix">';
                        msgConteudo += '<div class="message-data align-right">';
                        msgConteudo += '<span class="message-data-time" >'+this.hora_envio+', '+this.data_envio+'</span> &nbsp; &nbsp;';
                        msgConteudo += '<span class="message-data-name" >Voc&ecirc;</span> <i class="fa fa-circle me"></i>';
                        msgConteudo += '</div>';
                        msgConteudo += '<div class="message other-message float-right">';
                        
                        if(this.mensagem_tipo == 'arquivo'){
                            
                            var dadosFile = $().crypt({
                                method: "b64enc",
                                source: this.cd_chat_msg
                            });
                            
                            msgConteudo += '<a title="Baixar" class="linkArquivo" file="'+dadosFile+'" href="'+baseUrl+'chat/downloadFile/'+dadosFile+'">'+this.mensagem+'</a>';
                        }else{
                            msgConteudo += this.mensagem;
                        }

                        msgConteudo += '</div>';
                        msgConteudo += '</li>';
                        
                }else{
                    
                    if(this.cd_destino == sessionUserId && this.tipo_destino == 'user'){
                        if(this.lida == 'N'){
                            var classLida = 'nao-lida';
                            var lida = '';
                        }else{
                            var classLida = 'lida';
                            var lida = '<span class="glyphicon glyphicon-ok chat-icone-lida" aria-hidden="true"></span>';
                        }
                    }
                    
                    if(this.tipo_destino == 'dp'){ 
                        if(this.lida == 'S'){
                            var classLida = 'lida';
                            var lida = '<span class="glyphicon glyphicon-ok chat-icone-lida" aria-hidden="true"></span>';
                        }else{
                            var classLida = 'nao-lida';
                            var lida = '';
                        }                        
                    }
                    
                    var msgConteudo = '<li class="'+classLida+'" idMsg="'+this.cd_chat_msg+'" >';
                        msgConteudo += '<div >';
                        msgConteudo += '<div class="message-data">';
                        msgConteudo += '<span class="message-data-name"><i class="fa fa-circle online"></i> '+this.nome_usuario+'</span>';
                        msgConteudo += '<span class="message-data-time">'+this.hora_envio+', '+this.data_envio+'</span>';
                        msgConteudo += '</div>';
                        msgConteudo += '<div id="msgStatus'+this.cd_chat_msg+'" class="message my-message">';
                        
                        if(this.mensagem_tipo == 'arquivo'){
                            
                            var dadosFile = $().crypt({
                                method: "b64enc",
                                source: this.cd_chat_msg
                            });
                            
                            msgConteudo += '<a title="Baixar" href="'+baseUrl+'chat/downloadFile/'+dadosFile+'">'+this.mensagem+'</a>';
                        }else{
                            msgConteudo += this.mensagem;
                        }
                        
                        msgConteudo += lida;                                                
                        msgConteudo += '</div>';
                        msgConteudo += '</div>';
                        msgConteudo += '</li>';
                    
                }
    
                $("#chat-msg-historico").append(msgConteudo);
    
            });
            
            if(conversaRecente != id){
            
                // Verifica mensagm não lidas
                $(this).verificaLidas();
                //$('.chat-history').stop().animate({scrollTop: $(".nao-lida").first().offset().top - 300}, 1000,'easeInOutExpo');
                $('.chat-history').scrollTop($(".nao-lida").first().offset().top - 300);
            
            }

        }
        
      }
    });
    
}

/* VERIFICA MENSAGENS NÃO LIDAS QUE ESTÂO SENDO LIDAS, APARECEM NA TELA AO DESTINADO, E MUDA O STATUS */
$.fn.verificaLidas = function() {
    
    // Pega todas as mensagens  
    var idMsgs = new Array(); 
    $("#chat-msg-historico li").each(function(){
        
    	var visible = $(this).visible( false );
        // Pega somente as mensagens não lidas
    	if($(this).hasClass('nao-lida')){
            if(visible){
            
               idMsgs.push($(this).attr("idMsg"));  
               $("#msgStatus"+$(this).attr("idMsg")).append('<span class="glyphicon glyphicon-ok chat-icone-lida" aria-hidden="true"></span>');
               $(this).removeClass('nao-lida').addClass('lida'); 

            }
        }
        
    });
    
    if(idMsgs != ''){
    
        $.ajax({
          type: "POST",
          url: baseUrl+'chat/statusMsgLida',
          data: {
            cd_msgs: idMsgs.join(","),
            origem: $("#user-conversa").attr("idUsuarioConversa"),
            origem_tipo: $("#user-conversa").attr("tipoUsuarioConversa")
            //cd_user: $("#status-user-favorito").attr("idUserConversa")
          },
          dataType: "json",
          error: function(res) {
            //alert(res.status);
          },
          success: function(res) {
            //alert(res.status);
          }
        });
    
    }
    
}

/* ACIONA A VERIFICAÇÃO DE MENSAGENS NÃO LIDAS AO ROLAR O SCROLL PARA BAIXO */
var position = $(".chat-history").scrollTop();

$(".chat-history").scroll(function () {
        var scroll = $(".chat-history").scrollTop();
        var detectPartial = false;
        if (scroll > position) {
            
            $(this).verificaLidas();
            
        }
    position = scroll+3;
});

var controleStatusEscrita = '';
/* INSERI UMA QUEBRA DE LINHA DE FORMA OCULTA AO PRESIONAR A TECLA ENTER */
$('div[contenteditable=true]').keydown(function(e) {
    //alert(e.keyCode);
    
    // Escrevendo
    if($(this).html().length != 0 && e.keyCode != 8){
        //alert('escrevendo');   
        //alert($("#user-conversa").attr("idUsuarioConversa"));
        //$("#chat-status-escrita").html("Escrevendo...");
        $.post( baseUrl+"chat/defineDinamica", { 
            statusEscrita: "Escrevendo...", 
            user: $("#user-conversa").attr("idUsuarioConversa"), 
            tipo: $("#user-conversa").attr("tipoUsuarioConversa") 
            } );
    }
    
    // Apagando
    if($(this).html().length != 0 && e.keyCode == 8){
        //alert('apagando');
        //$("#chat-status-escrita").html("Apagando");
        $.post( baseUrl+"chat/defineDinamica", { 
            statusEscrita: "Apagando...", 
            user: $("#user-conversa").attr("idUsuarioConversa"), 
            tipo: $("#user-conversa").attr("tipoUsuarioConversa") 
            } );
    }
    
    // trap the return key being pressed
    if (e.keyCode == 13) {
      // insert 2 br tags (if only one br tag is inserted the cursor won't go to the second line)
      /*
      if($.browser.mozilla){
        document.queryCommandSupported('insertBrOnReturn');
      }else{
        document.execCommand('insertHTML', false, '<br><br>');
      }
      */
      $("#chat-btn-enviar").trigger("click");
      // prevent the default behaviour of return key pressed
      return false;
    }
});

/* ENVIA A MENSAGEM DIGITADA - INICIO */
$("#chat-btn-enviar").click(function(){
    
    /* CONFIGURAÇÃO TEXTAREA
    var msg = $("#chat-textarea").val().split('\n');
    var msgConteudo = "<span class='nao-lida'><br>";
    $.each(msg, function(index, value) {
       msgConteudo += value+"<br>";
    });
    msgConteudo += "<br><br></span>";
    $("#chat-msg-historico").append(msgConteudo);
    */
    
    if($('div[contenteditable=true]').html() != ''){
    
        $.ajax({
          type: "POST",
          url: baseUrl+'chat/insereConversa',
          data: {
            destino: $("#user-conversa").attr("idUsuarioConversa"),
            destino_tipo: $("#user-conversa").attr("tipoUsuarioConversa"),
            mensagem: $('div[contenteditable=true]').html(),
            mensagem_tipo: 'texto'
          },
          dataType: "json",
          error: function(res) {
            alert('Mensagem não enviada');
          },
          success: function(res) {
            
            var msgConteudo = '<li idMsg="'+res.cd_chat_msg+'" class="clearfix">';
            msgConteudo += '<div class="message-data align-right">';
            msgConteudo += '<span class="message-data-time" >'+res.hora_envio+', '+res.data_envio+'</span> &nbsp; &nbsp;';
            msgConteudo += '<span class="message-data-name" >Voc&ecirc;</span> <i class="fa fa-circle me"></i>';
            msgConteudo += '</div>';
            msgConteudo += '<div class="message other-message float-right">';
            msgConteudo += res.mensagem;
            msgConteudo += '</div>';
            msgConteudo += '</li>';
        
    
            $("#chat-msg-historico").append(msgConteudo);
            
            $("#chatEmotion").css('display', 'none');
            $('div[contenteditable=true]').html('');
            
            $("#qtd-user-conversa").html($("#chat-msg-historico li").length+' mensagens.');
            
            if(!$(".new-msg"+$("#user-conversa").attr("idUsuarioConversa")).length){
                
                htmlRecentes = '<li id="recentId'+$("#user-conversa").attr("idUsuarioConversa")+'" class="clearfix ui-state-default">';
                htmlRecentes += $("#"+$("#user-conversa").attr("idUsuarioConversa")).html();  
                htmlRecentes += '</li>'; 
                
                $("#recentes").prepend(htmlRecentes);
                
                $("#status-user-favorito, .favorito-status").unbind("click");
                
                $(this).iniDragDrop();    
                $(this).dragDropArrastarDp(); 
                $(this).addRevFavorito();
                
            }else{
                
                $("#recentId"+$("#user-conversa").attr("idUsuarioConversa")).prependTo( "#recentes" );
                
            }
            
          }
        });
    
    }
    
    //$("#chat-msg-historico").append("<b>Voce: </b><br>"+$("#chat-textarea").val().replace("\n", "<br>")+"<br>");
    //$("#chat-msg-historico").animate({scrollTop:$("#chat-msg-historico").height()}, 'slow');
    //$("#chat-msg-historico").animate({ scrollTop: $("#chat-msg-historico").prop("scrollHeight")}, 1000);
    
});

/* ACIONA A VERIFICAÇÃO DE MENSAGENS NÃO LIDAS AO ROLAR O SCROLL PARA BAIXO */
$("#chat-min-max").click(function(){
   $(".chat-conteudo").toggle(0, function(){
		if($(this).css('display')=='none'){
		      
            $.post( baseUrl+'chat/statusAbertura', { status: 'nao'} );
          
			$("#chat-min-max").html('<img class="click" src="'+baseUrl+'assets/chat/ico/maximize.png" />');//change the button label to be 'Show'
		}else{
		  
            $.post( baseUrl+'chat/statusAbertura', { status: 'sim'} );
          
			$("#chat-min-max").html('<img class="click" src="'+baseUrl+'assets/chat/ico/minimize.png" />');//change the button label to be 'Hide'
		}
	});
});

/* ANEXA ARQUIVOS NO CHAT ATRAVÉS DE DRAG AND DROP */
Dropzone.options.myDropzone = {
    init: function() {
        /*
        var msgConteudo = '<li class="nao-lida clearfix">';
        		msgConteudo += '<div class="message-data align-right">';
        		msgConteudo += '<span class="message-data-time">';
        		msgConteudo += '10:10 AM, Today';
        		msgConteudo += '</span>';
        		msgConteudo += '&nbsp; &nbsp;';
        		msgConteudo += '<span class="message-data-name">';
        		msgConteudo += 'Olia';
        		msgConteudo += '</span>';
        		msgConteudo += '<i class="fa fa-circle me">';
        		msgConteudo += '</i>';
        		msgConteudo += '</div>';
        		msgConteudo += '<div class="message other-message float-right">';
                msgConteudo += 'Arquivos enviados:</div>';
        		msgConteudo += '</li>';
    		
              $("#chat-msg-historico").append(msgConteudo);
    */
      /* ALTERNATIVA PARA BOTÃO REMOVER
      this.on("addedfile", function(file) {
    
        // Create the remove button
        var removeButton = Dropzone.createElement("<button>Remove file</button>");
    
    
        // Capture the Dropzone instance as closure.
        var _this = this;
    
        // Listen to the click event
        removeButton.addEventListener("click", function(e) {
          // Make sure the button click doesn't submit the form:
          e.preventDefault();
          e.stopPropagation();
    
          // Remove the file preview.
          _this.removeFile(file);
          // If you want to the delete the file on the server as well,
          // you can do the AJAX request here.
        });
    
        // Add the button to the file preview element.
        file.previewElement.appendChild(removeButton);
      });*/
        /* AO COMPLETAR TODO O EVENTO
        this.on("complete", function (data) {
            var res = JSON.parse(data.xhr.responseText);
    
            if (this.getQueuedFiles().length == 0) {
                alert("File(s) were uploaded successfully.");
    
                //$("#Grid").data("kendoGrid").dataSource.read(); //for Chrome
            }
    
        });*/
        this.on('drop', function( ){
              /*
              var msgConteudo = '<li class="nao-lida clearfix">';
        		msgConteudo += '<div class="message-data align-right">';
        		msgConteudo += '<span class="message-data-time">';
        		msgConteudo += '10:10 AM, Today';
        		msgConteudo += '</span>';
        		msgConteudo += '&nbsp; &nbsp;';
        		msgConteudo += '<span class="message-data-name">';
        		msgConteudo += 'Olia';
        		msgConteudo += '</span>';
        		msgConteudo += '<i class="fa fa-circle me">';
        		msgConteudo += '</i>';
        		msgConteudo += '</div>';
        		msgConteudo += '<div class="message other-message float-right">';
                msgConteudo += 'Arquivos enviados:</div>';
        		msgConteudo += '</li>';
    		
              $("#chat-msg-historico").append(msgConteudo);*/
        });
        this.on('sending', function( file, resp ){
              
              
        });
        this.on('success', function( file, resp ){
            
            var obj = $.parseJSON(resp);
            //alert(obj.status);
    
            var baixarButton = Dropzone.createElement('<a title="Baixar" href="'+baseUrl+'chat/downloadFile/'+obj.status+'">OK</a>');
            
            var _this = this;
    
            // Listen to the click event
            baixarButton.addEventListener("click", function(e) {
                //alert(obj.status);
            });  
            
            file.previewElement.appendChild(baixarButton);
            
        });
        
        this.on("complete", function (data) {
            //var msgConteudo = 'teste</div>';
        		//msgConteudo += '</li>';
            //$("#chat-msg-historico").append(msgConteudo);
    
        });
    
    },
    dictFileTooBig: "Tamanho excedido!",  
    maxFilesize: 50, // MB
    addRemoveLinks: true,
    dictDefaultMessage: "" /*"Arraste o arquivo pra ca."*/,
    previewsContainer: "#chat-msg-historico",
    dictCancelUploadConfirmation: "Gostaria de cancelar?",
    dictCancelUpload: "Cancelar",
    dictRemoveFile: "" /*"Remover arquivo"*/, 
    clickable: "#clickable", 
    fallback: function(file) {
        //alert(1);
    },      
    removedfile: function(file) { 
      var _ref;
      return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
    }   
};