<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
#error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');
class ChatMonta{
    
    private $chatView = false;
    private $montouChat = false;
    
    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->model('chat_model','chat');
        $this->CI->load->library('Util', '', 'util');
        
    }
    
    public function getChatView(){
        
        if(!$this->montouChat){
            $this->setChatView();
        }
        
        return $this->chatView;
        
    }
    
    public function setChatView(){
        
        $dados['emotions'] = $this->getEmotions();
        $dados['favoritos'] = $this->getFavoritos();
        $dados['qtdConvNaoLidas'] = $this->getQtdConvNaoLidas();
        $dados['recentes'] = $this->getRecentes();
        $dados['dps'] = $this->getDp();
        
        $this->montouChat = true;
        
        return $this->chatView = $this->CI->load->view('chat', $dados, true);
        
    }
    
    public function getEmotions(){
        
        $this->CI->util->limpaArquivos();
        $emotions = $this->CI->util->buscaArquivosDiretorios('./files/chat/emotions/smilies');
        
        foreach($emotions as $emo){
            #$dados[] = $this->CI->util->getDataURI( base_url(substr($emo, 2)) );
            $dados[] = base_url(substr($emo, 2));
        }
        
        return $dados;
        
    }
    
    public function getFavoritos(){
        
        return $this->CI->chat->favoritos();
        
    }
    
    public function getQtdConvNaoLidas(){
        
        return $this->CI->chat->qtdConversasNaoLidas();
        
    }
    
    public function getRecentes(){
        
        return $this->CI->chat->conversasRecentes();
        
    }
    
    public function getDp(){
        
        return $this->CI->chat->dpUnidades();
        
    }

}