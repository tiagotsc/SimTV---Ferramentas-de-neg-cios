<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
setlocale(LC_ALL, 'pt_BR.UTF-8');
class Fatura{
	
    private $feedbackGravaArquivo = array();
    private $idTodosArquivosOk = array();
    
     /**
      * Fatura::__construct()
      * 
      * @return
      */
     public function __construct()
    {
        #parent::Model();
        #$this->load->model('Financeiro_model','financeiro');
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        #$this->CI->load->library('telefonia/febrabanLayout', '', 'febrabanLayout');  
        $this->CI->load->library('telefonia/febrabanLayout', '', 'febrabanLayout');      
        #$this->MO =& get_instance();
        #$this->CI->load->model('telefonia/fatura_model','fatura');   
        $this->CI->load->model('telefonia/fatura_model','faturaModel');
        $this->CI->load->model('base/logarquivo_model','logArquivo'); 
        #$this->load->model('telefonia/fatura_model','fatura');   
        $this->CI->load->library('Util', '', 'util');  
        $this->CI->load->helper('form');
        $this->CI->load->helper('url');

    }
    
    /**
      * Fatura::processarArquivoMovel()
      * 
      * Inicia o processamento dos arquivo de fatura móvel
      * 
      * @return o status
      */
    public function processarArquivoMovel(){
        
        #$dir = PASTA_SISTEMA.'TESTE_TI_NAO_APAGAR'; 
        
        $this->feedback = '<div class="alert alert-info" role="alert"><strong>Iniciando processo</strong></div>';        
                
        #$dir = PASTA_REDE_SISTEMA.'movel';
        $dir = './temp/movel';
        
        $this->CI->session->set_userdata('feedback', $this->feedback);
        $this->CI->session->set_userdata('feedbackStatus', 'processando');
                                
        try{
            $this->CI->util->limpaArquivos();
            $arquivos = $this->CI->util->buscaArquivosDiretorios($dir);
            
            if($arquivos){
            
                $this->feedback .= '<div class="alert alert-info" role="alert"><strong>'.implode('<br>', $arquivos).'</strong></div>';
                $this->CI->session->set_userdata('feedback', $this->feedback);      
            
            }                              
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
        }
        
        if(count($arquivos) == 0){
            
            $this->feedback .= '<div class="alert alert-info" role="alert"><strong>Nenhum arquivo encontrado</strong></div>';
            $this->CI->session->set_userdata('feedback', $this->feedback); 
            $this->feedback .= '<div class="alert alert-info" role="alert"><strong>Finalizado</strong></div>';
            $this->CI->session->set_userdata('feedback', $this->feedback);   
            $this->CI->session->set_userdata('feedbackStatus', 'finalizado');        
                        
            return array('<div class="alert alert-danger"><strong>'.htmlentities('Coloque os arquivos na pasta "'.$dir.'" para ser processado.').'</strong></div>');
            
        }else{
            
            foreach($arquivos as $arq){                
                                
                $status[] = $this->processaConteudoArquivoMovel($arq);
                $this->CI->util->apagaArquivo($arq);
                
                $this->feedback .= '<div class="alert alert-info" role="alert"><strong>Apagando arquivo '.$arq.' da pasta de origem</strong></div>';
                $this->CI->session->set_userdata('feedback', $this->feedback);                                
            
            }
            
            $this->feedback .= '<div class="alert alert-info" role="alert"><strong>Finalizado</strong></div>';
            $this->CI->session->set_userdata('feedback', $this->feedback);  
            $this->CI->session->set_userdata('feedbackStatus', 'finalizado');          
                        
            return $status;
            
        }
        
    }
    
    /**
      * Fatura::processaConteudoArquivoMovel()
      * 
      * Faz processamento de fato dos arquivos de fatura móvel
      * 
      * @return o status
      */
    private function processaConteudoArquivoMovel($arquivo = false){
        
        if($arquivo){
            
            if($this->CI->logArquivo->existenciaArquivo(md5_file($arquivo))){
                
                $msg = '<div class="alert alert-warning"><strong>O arquivo "'.basename($arquivo).'" j&aacute; foi processado!</strong></div>';
                
                $this->feedback = $this->feedback.$msg;
                $this->CI->session->set_userdata('feedback', $this->feedback);
                
                return $msg;
            }

            $handle = file($arquivo);
            $num_linhas = count($handle);
            
            $verifica = $this->CI->febrabanLayout->header();
            
            if(!in_array(substr($handle[0], $verifica[1]['posicao'], $verifica[1]['qtd']), $this->CI->febrabanLayout->getEmpresasAutorizadas()) and !$logArquivo){
                $msg = '<div class="alert alert-danger"><strong>Layout do arquivo "'.basename($arquivo).'" n&atilde;o identificado ou definido. Informe o adminstrador do sistema!</strong></div>';
                $this->feedback = $this->feedback.$msg;
                $this->CI->session->set_userdata('feedback', $this->feedback);
                return $msg;
            }
            
            $this->CI->faturaModel->iniciaTransacao();
            
            $log['nome'] = basename($arquivo);
            $log['localizacao'] = $arquivo;
            $log['md5file'] = md5_file($arquivo);
            $log['fonte'] = 'MOVEL';
            
            $logArquivo = $this->CI->logArquivo->grava($log);
            
            $cont = 1; 
            foreach($handle as $han){
                
                $tipoRegistro = substr($han, 0,1);
                
                switch($tipoRegistro){
                    
                    case 0: # Header
                        $posicoes = $this->CI->febrabanLayout->header();
                    break;
                    case 1: # Resumo
                        $posicoes = $this->CI->febrabanLayout->resumo();
                    break;
                    case 3: #Bilhetação
                        $posicoes = $this->CI->febrabanLayout->bilhetacao();
                    break;
                    case 4: # Serviços
                        $posicoes = $this->CI->febrabanLayout->servicos();
                    break;
                    case 5: # Desconto
                        $posicoes = $this->CI->febrabanLayout->descontos();
                    break;
                    case 9: # Trailler (Footer)
                        $posicoes = $this->CI->febrabanLayout->trailler();
                    break;
                    default:
                    
                        $msg = '<div class="alert alert-danger"><strong>Bloco do arquivo indefinido! Entre em contato com o administrador!</strong></div>'; 
                        $this->feedback = $this->feedback.$msg;
                        $this->CI->session->set_userdata('feedback', $this->feedback);
                        return $msg;
                }

                foreach($posicoes as $po){
                    $campos[] = $po['campo'];
                    $valores[] = $this->CI->util->formataPorParametro(trim(substr($han, $po['posicao'], $po['qtd'])), $po['formatacao']);           
                }
                
                $campos[] = 'cd_log_arquivo';
                $valores[] = $logArquivo;

                $this->CI->faturaModel->gravaQuery($campos, $valores);
                $campos = array();
                $valores = array();

                $cont++;
            }

            $status = $this->CI->faturaModel->finalizaTransacao();
            
        }else{
            
            $status = false;
            
        }
        
        if($status){
            $msg = '<div class="alert alert-success"><strong>Arquivo "'.basename($arquivo).'" processado com sucesso!</div>';
            $this->feedback = $this->feedback.$msg;
            $this->CI->session->set_userdata('feedback', $feedback);
            return $msg;
        }else{
            $msg = '<div class="alert alert-danger"><strong>Arquivo n&atilde;o processado!</strong></div>';
            $this->feedback = $this->feedback.$msg;
            $this->CI->session->set_userdata('feedback', $this->feedback);
            return $msg;            
        }
        
    }
    
    /**
      * Fatura::processaArquivoCallCenter()
      * 
      * Faz processamento dos arquivos do CallCenter
      * 
      * @return o status
      */
    public function processaArquivoCallCenter($arquivo){
        
        $handle = file($arquivo['arquivo']['full_path']);
        
        $logArquivo['nome'] = $arquivo['arquivo']['file_name'];
        $logArquivo['localizacao'] = $arquivo['arquivo']['file_path'];
        $logArquivo['md5file'] = md5_file($arquivo['arquivo']['full_path']);
        
        $logArquivo['fonte'] = $_POST['tipo'];

        $this->CI->faturaModel->inserirArquivoCallCenter($handle, $logArquivo);
        
        if($handle){
            return $handle;
        }else{
            return false;
        }
        
    }

}