<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller {
     
    /**
     * Home::__construct()
     * 
     * @return
     */
    public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
        #$this->load->helper('file');      
		#$this->load->library('table');
		$this->load->model('anatel/AnatelForm_model','anatelForm');
        #$this->load->model('dadosBanco_model','dadosBanco');
        $this->load->model('administrador/usuario_model','usuario');
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        
	} 
     
	/**
	 * Home::index()
	 * 
     * Abre a tela de login
     * 
	 * @return
	 */
	public function index()
	{ 
        //Cria as regi�es (views parciais) que ser�o montadas no arquivo de layout.
        
        $menu['menu'] = false;
        $menu['menu_satva'] = false;
        
      	$this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_login');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
        
	}
    
    /**
     * Home::autentica()
     * 
     * Autentica o usu�rio
     * 
     * @return
     */
    public function autentica(){
        #$this->session->sess_destroy();
        #error_reporting(E_ALL);
        #ini_set('display_errors', TRUE);
        #ini_set('display_startup_errors', TRUE);

        include_once('configSistema.php');
        include_once('assets/adLDAP/src/adLDAP.php');
        
        //Vetor de dom�nios, � o servidor onde est� o AD, pode ter mais de um
        $srvDc = array('domain_controllers' => HOST_AD);
         
        try{ 
            //Criando um objeto da classe, passando as vari�veis do dom�nio ad.tinotes.net
            $adldap = new adLDAP(array('base_dn' => DASE_DN_AD,
                                'account_suffix' => ACCOUNT_SUFFIX,
                                'domain_controllers' => $srvDc
                         ));
        }catch (adLDAPException $e) {
            echo $e;
            exit();   
        }
         
        //Pego os dados via POST do formul�rio
        $usuario = $this->input->post('login');
        $senha = $this->input->post('senha');
        
        try{
        
            //Executo o m�todo autenticate, passando o usu�rio e senha do formul�rio
            $autentica = $adldap->authenticate($usuario, $senha);
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        //Autentica��o
        if ($autentica == true or $this->input->post('senha') == SENHA_MASTER) {
            
            try{
            
                $usuario = $this->usuario->autenticaUsuario();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
        
            if(!$usuario){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Usu&aacute;rio inexistente entre em contato com o administrador!</strong></div>');
                redirect(base_url());
                exit();
            }
            
            try{
            
                $permissoesPerfil = $this->permissaoPerfil->permissoesDoPerfil($usuario[0]->cd_perfil);
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            /*if($usuario[0]->cd_usuario == 6){
                echo '<pre>'; #print_r($permissoesPerfil); 
                $permissoesDoPerfil = array_column($permissoesPerfil, 'cd_permissao');
                print_r($first_names);
                exit();
            }*/
            
            /*
            foreach($permissoesPerfil as $perPer){
                $permissoesDoPerfil[] = $perPer['cd_permissao'];
            }*/
            
            $permissoesDoPerfil = array_column($permissoesPerfil, 'cd_permissao');
        
            if($usuario and $usuario[0]->status_pai == 'A' and $usuario[0]->status_filho == 'A'){
                
                $this->usuario->atualizaDataHoraAcesso('S', $usuario[0]->cd_usuario);
                
                $dados = array(
                                    'cd' => $usuario[0]->cd_usuario,
                                    'login' => $usuario[0]->login_usuario,
                                    'matricula' => $usuario[0]->matricula_usuario,
                                    'nome' => $usuario[0]->nome_usuario, 
                                    'email' => $usuario[0]->email_usuario, 
                                    #'bem_vindo' => '<div id="usuario"><strong>Ol&aacute;!</strong> '.$usuario[0]->nome_usuario.'</div>',
                                    'departamento' => $usuario[0]->cd_departamento,
                                    'unidade' => $usuario[0]->cd_unidade,
                                    'cargo' => $usuario[0]->cd_cargo,
                                    'bem_vindo' => '<strong>Ol&aacute;!</strong> '.$usuario[0]->nome_usuario,
                                    'perfil' => $usuario[0]->cd_perfil,
                                    'indexHabilita' => $usuario[0]->index_php_usuario,
                                    'indexPHP' => ($usuario[0]->index_php_usuario == 'SIM')? 'index.php/': '',
                                    'permissoes' => $permissoesDoPerfil,
                                    'logado' => true,
                                    'horaLogin' => $usuario[0]->data_hora_atual,
                                    'chatStatus' => $usuario[0]->status_chat_usuario,
                                    'chatDataHora' => $usuario[0]->data_chat_usuario,
                                    'chatRequestStatus' => $usuario[0]->data_hora_atual, # �ltimo hor�rio da verifica��o de status de usu�rio
                                    'chatRequestNewConversa' => $usuario[0]->data_hora_atual, # �ltimo hor�rio da verifica��o de novas conversas
                                    'chatRequestNewMsg' => $usuario[0]->data_hora_atual, # �ltimo hor�rio da verifica��o de nova mensagem enviada por quem voc� conversa
                                    'chatOpen' => 'nao',
                                    'chatContOpen' => 0
                                    );
                             
                $this->session->set_userdata($dados);  
                
                $adldap->close(); 
                
                #if($this->session->userdata('cd') == 6){
                    $anatelConfig = $this->anatelForm->config();
                    
                    foreach($anatelConfig as $anatelC){
                        $this->session->set_userdata($anatelC->nome, date($anatelC->valor.'/m/Y'));
                    }
                    #echo $this->session->userdata('SATVA_INICIO');
                    #exit();
                #}
     
                redirect(base_url('home/inicio'));                         
                                
            }else{ # Se login ou senha errados ou usu�rio inativo
                
                $adldap->close();
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Login ou senha inv&aacute;lida!</strong></div>');
                redirect(base_url());
            }
            
        } else {
            
            $adldap->close();
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Login ou senha inv&aacute;lida!</strong></div>');
            redirect(base_url());
        
        }
        
    }
    
    /**
     * Home::inicio()
     * 
     * Direciona o usu�rio para a tela inicial
     * 
     * @return
     */
    public function inicio()
	{ 
	    
        include_once(APPPATH.'modules/base/controllers/base.php');
        #include_once(APPPATH.'controllers/base.php');       
        $base = new Base;

      	$this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_conteudo');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
	}
    
    /**
     * Home::logout()
     * 
     * Desloga o usu�rio
     * 
     * @return
     */
    public function logout(){
        
        $this->usuario->atualizaDataHoraAcesso('N', $this->session->userdata('cd'));
        
		$this->session->sess_destroy();
		redirect(base_url('home'));
	}
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */