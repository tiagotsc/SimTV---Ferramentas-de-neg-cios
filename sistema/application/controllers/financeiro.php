<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Financeiro extends CI_Controller {

     
    private $controlaDiretorio = 0; 
     
    /**
     * Financeiro::__construct()
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
		$this->load->helper('url');
		$this->load->helper('form');
        $this->load->helper('file');
        $this->load->library('Util', '', 'util');        
		$this->load->library('table');
		$this->load->model('Financeiro_model','financeiro');
	} 
     
	/**
	 * Financeiro::index()
	 * 
	 * @return
	 */
	public function index()
	{ 
	    #$dados['bancoArquivo'] = $this->arquivoCobranca->bancoArquivo();
        #$dados['arquivosRegistrados'] = $this->arquivoCobranca->todosArquivos();
		#$this->load->view('home', $dados);
        //Cria as regiões (views parciais) que serão montadas no arquivo de layout.
      	$this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
	}
    
    /**
     * Financeiro::arquivo()
     * 
     * @param mixed $tipoArquivo
     * @return
     */
    public function arquivo($tipoArquivo){
        
        switch($tipoArquivo){
            
            case 'validaRetorno':
                $conteudo = 'view_valida_retorno';
                $dados['listaMeses'] = $this->util->listaMesesAteAtual('sim');
                $dados['listaBancos'] = $this->financeiro->bancosCobranca();
                break;
            case '':
                $conteudo = 'view_valida_remessa';
                break;
            default:
                echo utf8_encode('View não definida');
                exit();
            
        }
        
        $this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'financeiro/arquivos/'.$conteudo, $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
        #exit();
        
    }
    
    /**
     * Financeiro::validaArquivoRetorno()
     * 
     * @return
     */
    public function validaArquivoRetorno(){
        
        #echo '<pre>';
        #print_r($this->input->post('mes'));
        #print_r($_POST);
        
        #exit();
        
        $dataDiretorio = substr(date('Y'),0,1).' '.substr(date('Y'),1,1).' '.substr(date('Y'),2,1).' '.substr(date('Y'),3,1);
        $dir = '../faturamento/Retornos ICNET - IBBRADESCO/'.$dataDiretorio;
        
        $processamento = $this->buscaArquivosDiretorios($dir, 'nao');
        exit();
    }
    
    /**
     * Financeiro::buscaArquivosDiretorios()
     * 
     * @param mixed $diretorio
     * @param string $raiz
     * @return
     */
    public function buscaArquivosDiretorios($diretorio = null, $raiz = 'nao'){
        $this->controlaDiretorio += 1;
        $cont = $this->controlaDiretorio;
        $meses = '/.*'.implode('|', $this->input->post('mes')).'.*/';
        
        $ponteiro  = opendir($diretorio);
        // monta os vetores com os itens encontrados na pasta
        while ($nome_itens = readdir($ponteiro)) {
            $itens[] = $nome_itens;
        }
        
        foreach($itens as $listar){
        
            if ($listar!="." && $listar!=".."){
                
                $caminho = strtoupper($this->util->removeAcentos(utf8_decode($diretorio.'/'.$listar)));
                
                if(preg_match($meses, $caminho)){
                
                    if(preg_match('/[0-9]{2}.[0-9]{2}.[0-9]{2}./', $caminho)){
                        
                        if(preg_match('/'.strtoupper($this->input->post('banco_arquivo')).'/', $caminho)){                        
                                                                        
                            #echo 'aqui';echo $cont; echo $diretorio.'/'.$listar; echo '<br>';
                            #echo $listar; echo '<br>';
                            if (is_dir($diretorio.'/'.$listar)) { 
                                $this->buscaArquivosDiretorios($diretorio.'/'.$listar, 'sim');
                               #echo $cont; echo $diretorio.'/'.$listar; echo '<br>';
                                //echo $caminho; echo '<br>';
                            }else{
                                #echo 'Não é pasta<br>';
                                echo $diretorio.'/'.$listar; echo '<br>';
                                //echo $caminho; echo '<br>';
                            }                            
                        
                        }                        
                                                                        
                    }else{
                    
                    #echo $listar; echo '<br>';
                    if (is_dir($diretorio.'/'.$listar)) { 
                        $this->buscaArquivosDiretorios($diretorio.'/'.$listar, 'sim');
                       #echo $cont; echo $diretorio.'/'.$listar; echo '<br>';
                        //echo $caminho; echo '<br>';
                    }else{
                        #echo 'Não é pasta<br>';
                        echo $diretorio.'/'.$listar; echo '<br>';
                        //echo $caminho; echo '<br>';
                    }
                    
                    }                                        
                
                }
            }
        }      
        
    }
    
    /**
     * Financeiro::salvaArquivo()
     * 
     * @return
     */
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
    
    /**
     * Financeiro::upload_arquivo()
     * 
     * @return
     */
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
    
    /**
     * Financeiro::pesquisarBoleto()
     * 
     * @param mixed $cdArquivo
     * @return
     */
    public function pesquisarBoleto($cdArquivo)
	{
        $dados['dadosArquivo'] = $this->arquivoCobranca->dadosArquivo($cdArquivo);
		$this->load->view('pequisaBoleto', $dados);
	}
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */