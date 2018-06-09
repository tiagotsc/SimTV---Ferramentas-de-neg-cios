<?php 
include_once('base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Arquivo extends Base {
     
    /**
     * Arquivo::__construct()
     * 
     * @return
     */
    public function __construct(){
		parent::__construct();
        
        $this->load->helper('file');
        $this->load->model('ArquivoCobranca_model','arquivoCobranca');
        $this->load->model('usuario_model','usuario');
        $this->load->model('permissaoPerfil_model','permissaoPerfil');
        /*
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        # Configurações do sistema
        include_once('configSistema.php');
        
		$this->load->helper('url');
		$this->load->helper('form');
        #$this->load->helper('file');
        #$this->load->library('Util', '', 'util');        
		#$this->load->library('table');
		#$this->load->model('ArquivoCobranca_model','arquivoCobranca');
        $this->load->model('dadosBanco_model','dadosBanco');
        $this->load->model('usuario_model','usuario');
        $this->load->model('permissaoPerfil_model','permissaoPerfil');
        
        if($this->anatelForm->verificaResponsavel()){ // Se é responsável por responder relatório da Anatel
            
            // Se a data corrente estiver dentro do período pega os formulários
            if(date('d/m/Y') >= $this->session->userdata('SATVA_INICIO') and date('d/m/Y') <= $this->session->userdata('SATVA_FIM')){
                $menu['frmsAnatel'] = $this->anatelForm->formsResponsavel();
            }else{
                $menu['frmsAnatel'] = false;
            }
            
        }else{
            $menu['frmsAnatel'] = false;
        }
        
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        $this->layout->region('menu', 'view_menu', $menu);
        */
	} 
    
    /**
     * Arquivo::index()
     * 
     * Direciona o usuário para a tela inicial
     * 
     * @return
     */
    public function index()
	{ 
        
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
      	$this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'arquivo/view_arquivo');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
	}
    
    /**
     * Home::logout()
     * 
     * Desloga o usuário
     * 
     * @return
     */
    public function logout(){
		$this->session->sess_destroy();
		redirect(base_url('home'));
	}
    
 /*
    public function salvaArquivo(){
        
        #print_r($_POST);
        
        # Grava o nome do banco retorna o ID
        $idArquivo = $this->arquivoCobranca->gravaNomeArquivoRetorno();
        
        # Se foi gravado com sucesso
        if($idArquivo){
            
            $arquivo = $this->upload_arquivo();
            
            // Se não existe erro (Correto - Tudo bem)
            if(!isset($arquivo['error'])){
                
                # Pega o banco
                $dadosBanco = $this->arquivoCobranca->bancoArquivo($this->input->post('cd_banco_arquivo'));
                
                #echo $arquivo['file_name'];
                
                #echo substr($arquivo['file_name'], 0,1);
                
                # Daycoval arquivo iniciando com C                
                if($dadosBanco[0]->cd_banco_arquivo == 1 and substr($arquivo['file_name'], 0,1) == 'C'){
                    
                    $inicio = 62;
                    $fim = 21;                    
                
                # Daycoval arquivo iniciando com V                
                }elseif($dadosBanco[0]->cd_banco_arquivo == 1 and substr($arquivo['file_name'], 0,1) == 'V'){
                    
                    $inicio = 37;
                    $qtd = 25;                    
                        
                                                    
                }                                                                
                                                
                #echo '<pre>';
                #print_r($dadosBanco);
                
                $handle = '';
                $handle = file($arquivo['full_path']);
    		    $num_linhas = count($handle);
                
                $cont = 0;    

                foreach($handle as $han){
                    
                    if($cont > 0 and $cont < $num_linhas-1){
                        #echo $han; echo '<br>';
                        #echo intval(substr($han,$inicio,$qtd)); echo '<br>';
                                                       #linha, número título, id do arquivo
                        $boleto = intval(substr($han,$inicio,$qtd));    
                                                  
                        $gravaLinhas = $this->arquivoCobranca->gravaLinhas($han,$boleto,$idArquivo);
                        
                        if(!$gravaLinhas){
                            
                            $apaga = $this->arquivoCobranca->apagaArquivo($idArquivo);
                            
                            if($apaga){
                                $this->session->set_flashdata('statusOperacao', utf8_encode('<div class="alert alert-danger">O arquivo da operação foi excluido, pois houve um erro na operação.</div>'));
                                redirect(base_url('home'));
                            }else{
                                $this->session->set_flashdata('statusOperacao', utf8_encode('<div class="alert alert-danger">Erro ao excluir o arquivo da operação.</div>'));
                                redirect(base_url('home'));
                                exit();
                            }
                            
                        }
                    
                    }
                    
                    $cont++;
                }
                
                @unlink($arquivo['full_path']);
                
            }else{ // Existe erro (Errado - Deu erro)
                echo $arquivo['error'];
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao upar arquivo.</div>');
                redirect(base_url('home'));
                exit();
            }
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success">Arquivo gravado com sucesso!</div>');
            redirect(base_url('home'));
        
       } # Fecha if (Nome do arquivo gravado)
        
        
    } // Fecha salvarArquivo()
    
    function upload_arquivo(){
        
		$config['upload_path'] = './temp';
		$config['allowed_types'] = '*';
		#$config['max_size'] = '0';
		#$config['max_width'] = '0';
		#$config['max_height'] = '0';
		#$config['encrypt_name'] = true;
		$this->load->library('upload',$config);
		if(!$this->upload->do_upload()){
			$error = array('error' => $this->upload->display_errors());
			#print_r($error);
			#exit();
		}else{
			#$data = array('upload_data' => $this->upload->data());
			#return $data['upload_data']['file_name'];
            return $this->upload->data();
		}
        
	} // Fecha upload_arquivo()
    
    public function pesquisarBoleto($cdArquivo)
	{
        $dados['dadosArquivo'] = $this->arquivoCobranca->dadosArquivo($cdArquivo);
		$this->load->view('pequisaBoleto', $dados);
	}
*/
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */