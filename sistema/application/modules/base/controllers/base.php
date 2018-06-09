<?php
#error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe base dos controllers
*/
class Base extends MX_Controller 
{
    
	/**
	 * Base::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        # Configurações do sistema
        include_once('configSistema.php');
        
        $this->load->helper('url');
        $this->load->library('pagination');
		$this->load->helper('form');
        $this->load->helper('text');
        $this->load->library('table');
        $this->load->library('Util', '', 'util');     
        $this->load->model('base/dadosBanco_model','dadosBanco');
        $this->load->model('anatel/AnatelForm_model','anatelForm');
        #$this->load->model('chat_model','chat');
        #$this->load->library('ChatMonta', '', 'ChatMonta'); 
        $this->load->model('base/log_model','logGeral');        
        
        if($this->anatelForm->verificaResponsavel()){ // Se é responsável por responder relatório da Anatel
            
            // Se a data corrente estiver dentro do período pega os formulários
            if(date('d/m/Y') >= $this->session->userdata('SATVA_INICIO') and date('d/m/Y') <= $this->session->userdata('SATVA_FIM')){
                $this->util->setMenuCompleto('');
                $this->util->setPositionMenu('left');
                $menu['menu_satva'] = $this->util->montaMenu($this->anatelForm->menuIndicadoresUnidades(), $this->anatelForm->menuIndicadores());
            }else{
                $menu['menu_satva'] = false;
            }
            
        }else{
            $menu['menu_satva'] = false;
        }
        #if(in_array($this->session->userdata('cd'), array(6/*,2771,3588,3648,3951,4256,4298,4345,4583*/))){ 
            #$chatView = $this->ChatMonta->getChatView();
            #$menu['chatHtml'] = $chatView;
        #}
        
        $this->util->setMenuCompleto('');
        $this->util->setPositionMenu('right');
        
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        $this->layout->region('menu', 'view_menu', $menu);

    }
    
 }