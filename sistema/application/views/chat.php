<?php
# Dropzone drag and drop de upload de imagens
#echo link_tag(array('href' => 'assets/js/dropzone/basic.css','rel' => 'stylesheet','type' => 'text/css')); 
echo link_tag(array('href' => 'assets/js/dropzone/dropzone.css','rel' => 'stylesheet','type' => 'text/css')); 
echo "<script type='text/javascript' src='".base_url('assets/js/dropzone/dropzone.js')."'></script>";
#echo "<script type='text/javascript' src='".base_url('assets/js/dropzone/dropzone-amd-module.js')."'></script>";

# Verifica elementos vistos na tela na rolagem vertical
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.visible.js')."'></script>";

# Dropdown com imagens
echo link_tag(array('href' => 'assets/js/dropdown/dd.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url('assets/js/dropdown/jquery.dd.min.js')."'></script>";

# Notificações
echo "<script type='text/javascript' src='".base_url('assets/js/notify.js')."'></script>";

# Jquery Cript
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.crypt.js')."'></script>";
?>

<?php
#Template Chat
#echo link_tag(array('href' => 'assets/chat/css/reset.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/chat/css/style.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/chat/css/chat_personalizado.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/chat/js/index.js')."'></script>";
?>
<?php

if($qtdConvNaoLidas->qtd_conv_nao_lidas > 0){
    $conversaCont = '<strong class="style-new-msg">'.$qtdConvNaoLidas->qtd_conv_nao_lidas.'<span class="glyphicon glyphicon-comment style-new-msg" aria-hidden="true"></span></strong>';
}else{
    $conversaCont = '';
}

?>                                                                                                              
<div id="chat-base" class="clearfix">
	<div id="chat-menu">
		<div id="chat-menu-left">
			<!--<img src="<?php echo base_url('assets/chat/ico/menu-alt-20.png'); ?>" />-->
            <?php echo $conversaCont; ?>
		</div>
		<div id="chat-min-max">
            <?php
            if($this->session->userdata('chatOpen') == 'sim'){
                $chatMaxMin = base_url('assets/chat/ico/minimize.png');;
            }else{
                $chatMaxMin = base_url('assets/chat/ico/maximize.png');
            }
            ?>
			<img class="click" src="<?php echo $chatMaxMin; ?>" />
		</div>
	</div>
	<div class="people-list chat-conteudo" id="people-list">
        <div id="chat-config">
            <ul class="list" style="padding-bottom: 0px; margin-bottom: 0px;">
                <li id="500" class="clearfix">
    				<!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />-->
    				<div class="about">
    					<div id="feedback" class="name">
    						<!--Tiago Silva-->Seu status
    					</div>
    					<div class="status">
    						<select name="chatStatus" id="chatStatus">
                                <option value="ONLINE" data-image="<?php echo base_url('assets/chat/ico/ONLINE.png'); ?>">Online</option>
                                <option value="OCUPADO" data-image="<?php echo base_url('assets/chat/ico/OCUPADO.png'); ?>">Ocupado</option>
                                <option value="OFFLINE" data-image="<?php echo base_url('assets/chat/ico/OFFLINE.png'); ?>">Offline</option>
                            </select>
    					</div>
    				</div>
    			</li>
            </ul>
        </div>
		<div class="search">
			<input type="text" id="chat-search" placeholder="Pesquisar" />
			<i class="fa fa-search">
			</i>
		</div>
        <div id="searchRes"></div>
        
        <ul class="nav nav-tabs">
          <li>
            <a data-toggle="tab" href="#tabContatos">
                <b>Contatos</b>
            </a>
          </li>
          <li class="active">         
            <a id="abaConversa" data-toggle="tab" href="#tabConversas">
                <b>Conversas</b>
                <?php echo $conversaCont; ?>
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="tabContatos" class="tab-pane fade">
            <h5 class="tituloDp"><strong>Favoritos</strong></h5>
    		<ul id="myfavorites" class="connectedSortable list dragDrop">
                <?php foreach($favoritos as $chF){ ?>
    			<li id="<?php echo $chF->cd_adicionado; ?>" class="clearfix ui-state-default">
    				<!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />-->
    				<div class="about">
    					<div class="name">
    						<span class="click" onclick="$(this).abreChat(<?php echo $chF->cd_adicionado; ?>,'user');"><?php echo $chF->nome_usuario; ?></span>
                            <span idUserConversa="<?php echo $chF->cd_adicionado; ?>" dpFav="<?php echo $chF->cd_departamento; ?>" unFav="<?php echo $chF->cd_unidade; ?>" tipo="userList" class="glyphicon glyphicon-star favorito-status favorito-sim" aria-hidden="true"></span>
                            <!--<strong class="style-new-msg new-msg<?php echo $chF->cd_adicionado; ?>"></strong>-->
                        </div>
    					<div class="status">
    						<img src="<?php echo base_url('assets/chat/ico/'.$chF->status_chat_usuario.'.png'); ?>" />
    						<?php echo strtolower($chF->status_chat_usuario); ?>
    					</div>
    				</div>
    			</li>
                <?php } ?>
    		</ul>  
            <?php foreach($dps as $cDps){ ?>              
            <h5 class="tituloDp click <?php echo $cDps->cd_departamento.'-'.$cDps->cd_unidade; ?>" onclick="$(this).abreChat('<?php echo $cDps->cd_departamento.'-'.$cDps->cd_unidade; ?>','dp');">
                <strong><?php echo $cDps->nome_departamento.'<br>'.$cDps->nome_unidade; ?></strong><!--&nbsp<a href="#" onclick="mostrarOcultar(this, '<?php echo '#'.$cDps->cd_departamento.'-'.$cDps->cd_unidade; ?>')">-</a>-->
            </h5>                
    		<ul id="<?php echo $cDps->cd_departamento.'-'.$cDps->cd_unidade; ?>" class="connectedSortable list dragDrop chatDp">
                <?php 
                $chatUserDp = $this->chat->usuarios($cDps->cd_departamento, $cDps->cd_unidade);
                foreach($chatUserDp as $cUDp){
                    
                    if($cUDp->favorito == 1){
                        $favoritoClass = 'favorito-sim';
                    }else{
                        $favoritoClass = 'favorito-nao';
                    }
                    
                ?>        
    			<li id="<?php echo $cUDp->cd_usuario; ?>" class="clearfix ui-state-default">
    				<!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />-->
    				<div class="about">
    					<div class="name">
                            <span class="click" onclick="$(this).abreChat(<?php echo $cUDp->cd_usuario; ?>,'user');"><?php echo $cUDp->nome_usuario; ?></span>
                            <span idUserConversa="<?php echo $cUDp->cd_usuario; ?>" dpFav="<?php echo $cUDp->cd_departamento; ?>" unFav="<?php echo $cUDp->cd_unidade; ?>" tipo="userList" class="glyphicon glyphicon-star favorito-status <?php echo $favoritoClass; ?>" aria-hidden="true"></span>
    					</div>
    					<div class="status">
    						<img src="<?php echo base_url('assets/chat/ico/'.$cUDp->status_chat_usuario.'.png'); ?>" />
    						<?php echo ucfirst(strtolower($cUDp->status_chat_usuario)); ?>
    					</div>
    				</div>
    			</li>
                <?php 
                } 
                ?>            
    		</ul>
            <?php } ?>
          </div>
          <div id="tabConversas" class="tab-pane fade in active">
            <ul id="recentes" class="connectedSortable list dragDrop">
                <?php 
                foreach($recentes as $conRe){ 
                    
                    if($conRe->favorito == 1){
                        $favoritoClass = 'favorito-sim';
                    }else{
                        $favoritoClass = 'favorito-nao';
                    }
                    
                    if($conRe->tipo == 'user'){
                        $tipoList = 'userList';
                        $classFavorito = 'favorito-status';
                    }else{
                        $tipoList = 'dpList';
                        $classFavorito = '';
                    }
                    
                    #if($conRe->cd_origem != $this->session->userdata('cd')){
                ?>
    			<li id="recentId<?php echo $conRe->cd_usuario; ?>" class="clearfix ui-state-default">
    				<!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg" alt="avatar" />-->
    				<div class="about">
    					<div class="name">
    						<span class="click" onclick="$(this).abreChat('<?php echo $conRe->cd_usuario; ?>','<?php echo $conRe->tipo; ?>');"><?php echo $conRe->nome_usuario; ?></span>
                            <span idUserConversa="<?php echo $conRe->cd_usuario; ?>" dpFav="<?php echo $conRe->cd_departamento; ?>" unFav="<?php echo $conRe->cd_unidade; ?>" tipo="<?php echo $tipoList; ?>" class="glyphicon glyphicon-star <?php echo $classFavorito; ?> <?php echo $favoritoClass; ?>" aria-hidden="true"></span>
                            <?php
                            if($conRe->qtd_msg_nao_lidas > 0){
                                $qtdNewMsg = $conRe->qtd_msg_nao_lidas.'<span class="glyphicon glyphicon-comment style-new-msg" aria-hidden="true"></span>';
                            }else{
                                $qtdNewMsg = '';
                            }
                            ?>
                            <strong class="style-new-msg new-msg<?php echo $conRe->cd_usuario; ?>"><?php echo $qtdNewMsg; ?></strong>
                        </div>
                        
    					<div class="status idUserConversaStatus<?php echo $conRe->cd_usuario; ?>">
                        <?php if($conRe->tipo == 'user'){ ?>
    						<img src="<?php echo base_url('assets/chat/ico/'.$conRe->status_chat_usuario.'.png'); ?>" />
    						<span><?php echo strtolower($conRe->status_chat_usuario); ?></span>
                        <?php }else{ echo '<br>'; } ?>
    					</div>
                                                
    				</div>
    			</li>
                <?php 
                    #}
                } 
                ?>
    		</ul>
          </div>
        </div>    
	</div>
	<div class="chat chat-conteudo">
		<div class="chat-header clearfix chat-box">
			<!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg" alt="avatar" />-->
			<div class="chat-about">
				<div id="user-conversa" idUsuarioConversa="" tipoUsuarioConversa="" class="chat-with">
				</div>
				<div id="qtd-user-conversa" class="chat-num-messages">
				</div>
                <div id="chat-status-escrita">
                
                </div>
			</div>
            <span id="status-user-favorito" tipo="userWrite" class="glyphicon glyphicon-star favorito-status" aria-hidden="true"></span>
		</div>
		<!-- end chat-header -->
		<div class="chat-history chat-box">
            <form action="<?php echo base_url('chat/uploadFile'); ?>" id="my-dropzone" class="dropzone">
                <div class="fallback">
                <input name="file" type="file" multiple />
                </div>  
                <input type="hidden" id="userEnviaFile" name="userEnviaFile" value="" />
                <input type="hidden" id="userTipoEnviaFile" name="userTipoEnviaFile" value="" />
                <div id="div-msg-anterior"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></div>                  
    			<ul id="chat-msg-historico">	
    			</ul>
            </form>
		</div>
		<!-- end chat-history -->
        <div id="chatEmotion">
        <?php
        foreach($emotions as $chatE){
            #echo '<span class="emotion"><img src="'.$this->util->getDataURI( base_url(substr($chatE, 2)) ).'" /></span>';
            echo '<span class="emotion"><img src="'.$this->util->getDataURI( $chatE ).'" /></span>';
            #echo '<span class="emotion"><img src="'.$chatE.'" /></span>';
        }
        ?>
        </div>
		<div id="chat-escrita" class="chat-message clearfix chat-box">
            <div id="chat-emotion-file">
                <img id="emotionOpen" src="<?php echo $this->util->getDataURI( base_url('files/chat/emotions/smilies/Big-Grin.png') );?>" />
                <!--<img id="emotionOpen" src="<?php echo base_url('files/chat/emotions/smilies/Big-Grin.png');?>" />-->
				<span id="clickable" class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
            </div>
			<div id="chat-escrita-campo">
				<!--<textarea style="margin-bottom: 0px" name="message-to-send" id="message-to-send" placeholder="Type your message" rows="2"></textarea>-->
				<div id="chatWrite" contenteditable="true">
				</div>
            </div>
			<div id="chat-escrita-envio">
				<!--<i id="clickable" class="fa fa-file-o">
				</i>-->
				&nbsp;&nbsp;&nbsp;
                <span id="chat-btn-enviar" class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            </div>
		</div>
		<!-- end chat-message -->
	</div>
	<!-- end chat -->
</div>

<!-- end container -->
<script>

/* Data corrente da paginação */
var dataChatPaginacao = '<?php echo date('Y-m-d'); ?>';
var baseUrl = '<?php echo base_url(); ?>';
var sessionUserId = <?php echo $this->session->userdata('cd'); ?>;
/* # Último horário da verificação de status de usuário */
var dataReqStatus = '<?php echo $this->session->userdata('chatRequestStatus'); ?>';
/* # Último horário da verificação de usuários que te enviaram novas mensagem */
var chatRequestNewConversa = '<?php echo $this->session->userdata('chatRequestNewConversa'); ?>';
/* # Último data hora de requisição de novas mensagens */
var chatRequestNewMsg = '<?php echo $this->session->userdata('chatRequestNewMsg'); ?>';
/* # Armazena o seu status */
var chatSeuStatus = '<?php echo $this->session->userdata('chatStatus'); ?>';
/* # Armazena e informa se o chat esta aberto ou não */
var chatAberto = '<?php echo $this->session->userdata('chatOpen'); ?>';

</script>
<?php 
#echo "<script type='text/javascript' src='".base_url('assets/chat/js/chat.min.js')."'></script>"; 
?>