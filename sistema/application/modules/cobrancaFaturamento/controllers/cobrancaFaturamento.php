<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cobrancaFaturamento extends Base {
    
    # ARMAZENA TODOS OS ARQUIVOS ENCONTRADOS
    private $dirArquivosRetorno = array();
    
    # ARMAZENA TODOS AS PASTAS ENCONTRADAS
    private $dirPastasRetorno = array();
    
    private $qtdArquivosRemovidos = 0;
    
    private $logDados;
     
    /**
     * cobrancaFaturamento::__construct()
     * 
     * Carrega as classes e modelos necessários
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = utf8_encode('Faturamento/Cobrança');
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->helper('file');
        $this->load->model('Financeiro_model','financeiro');
        $this->load->model('relatorio/Relatorio_model','relatorio');    
        $this->load->model('RegistroTelecom_model','RegistroTelecom'); 
        $this->load->model('ArquivoCobranca_model','arquivoCobranca'); 
        /*
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        # Configurações do sistema
        include_once('configSistema.php');
        
		$this->load->helper('url');
		$this->load->helper('form');
        $this->load->helper('file');
        $this->load->library('Util', '', 'util');        
		$this->load->library('table');
        $this->load->library('pagination');
		$this->load->model('Financeiro_model','financeiro');
        $this->load->model('Relatorio_model','relatorio');    
        $this->load->model('RegistroTelecom_model','RegistroTelecom');   
        $this->load->model('dadosBanco_model','dadosBanco');
        $this->load->model('ArquivoCobranca_model','arquivoCobranca'); 
        $this->load->model('AnatelForm_model','anatelForm');
        
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
        $this->util->setMenuCompleto('');
        $this->util->setPositionMenu('right');
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        $this->layout->region('menu', 'view_menu', $menu);
        */   
	} 
     
	/**
	 * cobrancaFaturamento::index()
	 * 
	 * @return
	 */
	public function index()
	{ 
	    #$dados['bancoArquivo'] = $this->arquivoCobranca->bancoArquivo();
        #$dados['arquivosRegistrados'] = $this->arquivoCobranca->todosArquivos();
		#$this->load->view('home', $dados);
        //Cria as regiões (views parciais) que serão montadas no arquivo de layout.
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
      	$this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
	}
    
    /**
     * cobrancaFaturamento::arquivo()
     * 
     * Controla qual o tipo de arquivo é solicitado
     * 
     * @param mixed $tipoArquivo Tipo de arquivo para ser filtrado
     * 
     */
    public function arquivo($tipoArquivo){
        
        switch($tipoArquivo){
            
            case 'validaRetorno':
                $dados['painelDiaArquivo'] = $this->arquivoCobranca->qtdArquivosDiarios();
                $conteudo = 'view_valida_retorno';
                #$conteudo = 'teste_view';
                break;
            case '':
                $conteudo = 'view_valida_remessa';
                break;
            default:
                echo utf8_decode('View não definida');
                exit();
            
        }

        $dados['vazio'] = ''; # Criado para não dá erro no $dados
        
        $dados['pesquisa'] = 'nao';
        $dados['dataInsercao'] = $this->arquivoCobranca->dataInsercaoArquivoRetorno();
        $dados['banco'] = $this->financeiro->banco();
        
        $dados['postBanco'] = '';
        $dados['postDataLote'] = '';

        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'cobrancaFaturamento/'.$conteudo, $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * cobrancaFaturamento::execValidaArquivoRetorno()
     * 
     * Realiza a validação dos arquivos de retorno
     * 
     */
    public function execValidaArquivoRetorno(){      
        
        $this->logDados['descricao'] = 'Arquivo retorno - Inicia processo';
        $this->logDados['acao'] = 'INICIA';
        $this->logGeral->grava($this->logDados);
        
        # Carrega a classe que realiza o processo de validação
        $this->load->library('arquivoRetorno', '', 'arquivoRetorno');
        
        # Diretório padrão para busca
        #$dir = '../faturamento/Retornos ICNET - IBBRADESCO/TI/RETORNO_ORIGINAL'; 
        #$dir = PASTA_SISTEMA.'RETORNO_ORIGINAL'; 
        $dir = PASTA_REDE_SISTEMA.'faturamento/retorno_original'; 
#echo disk_total_space($dir); exit();
        # Busca os arquivos no diretório
        
        try{
        
            $this->buscaArquivosRetornoDiretorios($dir);
            $this->logDados['descricao'] = utf8_encode('Arquivo retorno - Buscando arquivos no diretório');
            $this->logDados['acao'] = 'PROCESSANDO';
            $this->logGeral->grava($this->logDados);
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        # SE O DIRETÓRIO ESTIVER VAZIO
        if(count($this->dirArquivosRetorno) == 0){
            
            #$this->buscaArquivosRetornoDiretorios($dir);
            $this->logDados['descricao'] = utf8_encode('Arquivo retorno - Diretório vazio');
            $this->logDados['acao'] = 'FINALIZA';
            $this->logGeral->grava($this->logDados);
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">'.htmlentities('Coloque as pastas dos arquivos de retorno no diretório da rede.').'</div>');
            
            redirect(base_url('cobrancaFaturamento/arquivo/validaRetorno'));
            
        }else{
            
            try{
            # REALIZA A VALIDAÇÃO DOS ARQUIVOS ENCONTRADOS
            $resultadoValidacao = $this->arquivoRetorno->validaArquivo($this->dirArquivosRetorno);
            
            #$this->buscaArquivosRetornoDiretorios($dir);
            $this->logDados['descricao'] = 'Arquivo retorno - Processa arquivos localizados';
            $this->logDados['acao'] = 'PROCESSANDO';
            $this->logGeral->grava($this->logDados);
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            #echo $_SESSION['totalRetornos']; echo $_SESSION['retornoProcessado']; exit();
            #$this->session->userdata('totalRetornos'); exit();
            # APAGA ARQUIVOS ORIGINAIS DA PASTA "ARQUIVO_ORIGINAL"
            /*foreach($this->dirArquivosRetorno as $arquivo){
                
                #$this->buscaArquivosRetornoDiretorios($dir);
                $this->logDados['descricao'] = utf8_encode('Arquivo retorno - Apaga os arquivos do diretório ').$arquivo;
                $this->logDados['acao'] = 'PROCESSANDO';
                $this->logGeral->grava($this->logDados);
                
                @unlink($arquivo);
            }
            
            # APAGA AS PASTA DENTRO DE "ARQUIVO_ORIGINAL"
            foreach($this->dirPastasRetorno as $pasta){
                
                #$this->buscaArquivosRetornoDiretorios($dir);
                $this->logDados['descricao'] = utf8_encode('Arquivo retorno - Remove as pastas do diretório ').$pasta;
                $this->logDados['acao'] = 'PROCESSANDO';
                $this->logGeral->grava($this->logDados);
                
                @rmdir($pasta);
            }*/
            
            # Limpar o atributos que armazena os arquivos
            $this->dirArquivosRetorno = null;
            $this->dirPastasRetorno = null;
            #echo '<pre>';
            #print_r($resultadoValidacao);
            #exit();
            
            try{
            # Cria os arquivos validados e armazena no diretório "RETORNO VALIDADO"
            $novosArquivos = $this->arquivoRetorno->criaArquivosValidados($resultadoValidacao['IdArquivos']);
            
            #$this->buscaArquivosRetornoDiretorios($dir);
            $this->logDados['descricao'] = utf8_encode('Arquivo retorno - Cria os novos arquivos no diretório');
            $this->logDados['acao'] = 'PROCESSANDO';
            $this->logGeral->grava($this->logDados);
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            
            # CRIA UMA SESSÃO COM STATUS DO PROCESSAMENTO
            /*$newdata = array(
                   'resultadoValidacao'  => $resultadoValidacao['feedback'],
                   'novosArquivos'     => $novosArquivos
               );

            $this->session->set_userdata($newdata);*/
            
            #$this->buscaArquivosRetornoDiretorios($dir);
            $this->logDados['descricao'] = 'Arquivo retorno - Processo finalizado com sucesso';
            $this->logDados['acao'] = 'FINALIZA';
            $this->logGeral->grava($this->logDados);
        
        }
        
        $dados['painelDiaArquivo'] = $this->arquivoCobranca->qtdArquivosDiarios();
        $dados['vazio'] = ''; # Criado para não dá erro no $dados
        
        $dados['pesquisa'] = 'nao';
        $dados['dataInsercao'] = $this->arquivoCobranca->dataInsercaoArquivoRetorno();
        $dados['banco'] = $this->financeiro->banco();
        
        $dados['postBanco'] = '';
        $dados['postDataLote'] = '';
        
        $dados['resultadoValidacao'] = $resultadoValidacao['feedback'];
        $dados['novosArquivos'] = $novosArquivos;
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'cobrancaFaturamento/view_valida_retorno', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
        #redirect(base_url('cobrancaFaturamento/arquivo/validaRetorno'));

    }
    
    /**
     * cobrancaFaturamento::gerarArquivoRetorno()
     * 
     * Gera o arquivo validado
     * 
     */
    /**
     * cobrancaFaturamento::gerarArquivoRetorno()
     * 
     * @param mixed $id
     * @return
     */
    public function gerarArquivoRetorno($id){
        
        $this->load->library('arquivoRetorno', '', 'arquivoRetorno');
        
        $idArray = array($id);
        
        try{
        
        $novosArquivos = $this->arquivoRetorno->criaArquivosValidados($idArray);
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $newdata = array(
                   'novosArquivos'     => $novosArquivos
               );

        $this->session->set_userdata($newdata);
        
        redirect(base_url('cobrancaFaturamento/arquivo/validaRetorno'));
        
    }
    
    /**
     * cobrancaFaturamento::buscaArquivosRetornoDiretorios()
     * 
     * Busca os arquivos no diretório
     * 
     * @param mixed $this->dirArquivosRetorno Diretório em que será extraido os arquivos 
     * 
     */
    public function buscaArquivosRetornoDiretorios($diretorio = null){
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        #Abre o diretório
        $ponteiro  = opendir($diretorio);
        
        #echo '<pre>'; print_r($ponteiro); exit();
        // monta os vetores com os itens encontrados na pasta (Pastas encontradas)
        #while ($nome_itens = readdir($ponteiro)) {
        while (false !== ($nome_itens = readdir($ponteiro))){
            $itens[] = $nome_itens;
        }
        
        # Lopping nas pastas encontradas
        foreach($itens as $listar){
        
            # Remove os pontos de diretório
            if ($listar!="." && $listar!=".."){

                # Se é pasta
                if (is_dir($diretorio.'/'.$listar)) { 
                    
                    # Armazena as pastas
                    $this->dirPastasRetorno[] = $diretorio.'/'.$listar;
                    
                    /*
                    Chama as função "buscaArquivosRetornoDiretorios" novamente 
                    para destrinchar o diretório até encontrar os arquivos
                    */
                    $this->buscaArquivosRetornoDiretorios($diretorio.'/'.$listar);

                }else{
                    #echo filesize($diretorio.'/'.$listar); echo '<br>';
                    # Armazena os arquivos
                    $this->dirArquivosRetorno[] = $diretorio.'/'.$listar;
                    
                }
            }
        }      
        
    }

    
    /**
     * cobrancaFaturamento::registrosTelecom()
     * 
     * Exibe o formulário de registro de telecom e lista os registros atuais
     * 
     */
    /**
     * cobrancaFaturamento::registrosTelecom()
     * 
     * @param mixed $cd
     * @return
     */
    public function registrosTelecom($cd = null){
        
        if(!empty($cd)){ # SE DESEJA ALTERAR
		
			$dadosTelecom = $this->RegistroTelecom->dadosRegistroTelecom($cd);
			
			#echo $dados['nome_paciente'];
			$campos = array_keys($dadosTelecom);
			#print_r($dados[0]->nome_paciente); exit();
			foreach($campos as $campo){
				$dados[$campo] = $dadosTelecom[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
			
			$dados['botao'] = 'Alterar';
			
		}else{ # SE DESEJA INSERIR
		
			$campos = $this->RegistroTelecom->camposRegistroTelecom();
			#print_r($campos); exit();
			foreach($campos as $camp){
				$dados[$camp] = ''; # DEIXAS OS CAMPOS SEU CONTEÚDO
			}
			
			$dados['botao'] = 'Cadastrar';
			
		}
        
        $dados['registros'] = $this->RegistroTelecom->registros();
        
        $dados['status'] = $this->dadosBanco->status();
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'cobrancaFaturamento/view_registro_telecom', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * cobrancaFaturamento::salvaRegistrosTelecom()
     * 
     * Salva o registro de telecom
     * 
     */
    /**
     * cobrancaFaturamento::salvaRegistrosTelecom()
     * 
     * @return
     */
    public function salvaRegistrosTelecom(){
        
        array_pop($_POST); // Remove o último elemento do array $_POST
		
		if($this->input->post('cd_registro_telecom')){
		  
            try{
            
                $executa = $this->RegistroTelecom->atualiza();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $cd = $this->input->post('cd_registro_telecom');
            
		}else{
            
            array_pop($_POST); // Remove o último elemento do array $_POST
            
            try{
            
                $executa = $this->RegistroTelecom->insere();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $cd = $executa;
                        
		}
		
		if($executa){
		  
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success">Os dados foram salvos sucesso!</div>');
          
            redirect(base_url('cobrancaFaturamento/registrosTelecom/'.$cd));
            
		}else{
		  
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Altere algum dado atual antes de salvar.</div>');
          
			redirect(base_url('cobrancaFaturamento/registrosTelecom/'.$cd));
		}
        
    }
    
    /**
     * cobrancaFaturamento::iniPesquisarArquivoRetorno()
     * 
     * Inicia o processo de pesquisa do retorno
     * 
     */
    /**
     * cobrancaFaturamento::iniPesquisarArquivoRetorno()
     * 
     * @return
     */
    public function iniPesquisarArquivoRetorno(){ # REDIRECINAMENTO PARA OCULTAR INDEX DA URL
        
            if($this->input->post('banco_arquivo') <> ''){
                $banco = $this->input->post('banco_arquivo');
            }else{
                $banco = '0';
            }                
             
            if($this->input->post('data_lote') <> ''){
                $dataLote = $this->input->post('data_lote');
            }else{
                $dataLote = '0';         
            }
                                                                   
            redirect(base_url('cobrancaFaturamento/pesquisarArquivoRetorno/'.$banco.'/'.str_replace("/", "", $dataLote)));
            
	}
    
    /**
     * cobrancaFaturamento::pesquisarArquivoRetorno()
     * 
     * Realiza o processo de pesquisa
     * 
     * @param $banco Banco para filtrar a pesquisa
     * @param $dataLote Data do lote para filtrar a pesquisa
     * @param $pagina Número da página para controlar a paginação
     * 
     */
    public function pesquisarArquivoRetorno($banco = null, $dataLote = null, $pagina = null){
        
        $dataOriginal = $dataLote;
        
        if(!empty($dataLote)){
            $dataLote = preg_replace('/^([0-9]{2})([0-9]{2})([0-9]{4})/','$3-$2-$1', $dataLote);
        }

        $this->load->library('pagination');
        
        $mostra_por_pagina = 30;
        $dados['arquivosPesquisa'] = $this->arquivoCobranca->psqArquivosRetorno($banco, $dataLote, $pagina, $mostra_por_pagina);   
        $dados['qtdArquivosPesquisa'] = $this->arquivoCobranca->psqQtdArquivosRetorno($banco, $dataLote);                     
        
        $dados['pesquisa'] = 'sim';
        
        $dados['painelDiaArquivo'] = $this->arquivoCobranca->qtdArquivosDiarios();
        
        $dados['dataInsercao'] = $this->arquivoCobranca->dataInsercaoArquivoRetorno();
        $dados['banco'] = $this->financeiro->banco();
        
        $dados['postBanco'] = $banco;
        $dados['postDataLote'] = ($dataOriginal == '0')? '': $dataOriginal;
        
        $config['base_url'] = base_url('cobrancaFaturamento/pesquisarArquivoRetorno/'.$banco.'/'.$dataLote); 
		$config['total_rows'] = $dados['qtdArquivosPesquisa'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 5;
        $config['first_link'] = '&lsaquo; Primeiro';
        $config['last_link'] = 'Último &rsaquo;';
        $config['full_tag_open'] = '<li>';
        $config['full_tag_close'] = '</li>';
        $config['first_tag_open']	= '';
       	$config['first_tag_close']	= '';
        $config['last_tag_open']		= '';
	    $config['last_tag_close']		= '';
	    $config['first_url']			= ''; // Alternative URL for the First Page.
	    $config['cur_tag_open']		= '<a id="paginacaoAtiva" class="active"><strong>';
	    $config['cur_tag_close']		= '</strong></a>';
	    $config['next_tag_open']		= '';
        $config['next_tag_close']		= '';
	    $config['prev_tag_open']		= '';
	    $config['prev_tag_close']		= '';
	    $config['num_tag_open']		= '';
		$this->pagination->initialize($config);
		$dados['paginacao'] = $this->pagination->create_links();
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'cobrancaFaturamento/view_valida_retorno', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
        
    
    
    /**
     * cobrancaFaturamento::apagaRegistroTelecom()
     * 
     * Apaga registros da tabela registro_telecom
     * 
     */
    /**
     * cobrancaFaturamento::apagaRegistroTelecom()
     * 
     * @return
     */
    public function apagaRegistroTelecom(){
        
        try{
        
            $executa = $this->RegistroTelecom->deletarRegistro();
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($executa){
		  
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success">O dado foi apagado com sucesso!</div>');
          
            redirect(base_url('cobrancaFaturamento/registrosTelecom/'));
            
		}else{
		  
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Altere algum dado atual antes de salvar.</div>');
          
			redirect(base_url('cobrancaFaturamento/registrosTelecom/'));
		}        
        
    }
    
    /**
     * cobrancaFaturamento::linhasExcluidasRetorno()
     * 
     * Abre as linhas excluídas
     * 
     * @param $cdArquivo Cd do arquivo para abrir as linhas excluídas
     * 
     */
    /**
     * cobrancaFaturamento::linhasExcluidasRetorno()
     * 
     * @param mixed $cdArquivo
     * @return
     */
    public function linhasExcluidasRetorno($cdArquivo){
        
        $dados['dadosArquivo'] = $this->arquivoCobranca->dadosArquivoRetorno($cdArquivo);
        $dados['linhasExcluidos'] = $this->arquivoCobranca->excluidoArquivoRetorno($cdArquivo);
        
        $this->load->view('cobrancaFaturamento/view_linha_excluida_retorno', $dados);
        
    }
    
    /**
     * cobrancaFaturamento::pesquisaConteudoRetorno()
     * 
     * @return
     */
    public function pesquisaConteudoRetorno(){
        
        if($this->input->post('numero_titulo') <> ''or $this->input->post('nosso_numero') <> '' or $this->input->post('nosso_numero_corresp') <> ''){
            $dados['conteudoArquivo'] = $this->arquivoCobranca->dadosConteudoArquivoRetorno($this->input->post('numero_titulo'), $this->input->post('nosso_numero'), $this->input->post('nosso_numero_corresp'));
        }else{
            $dados['conteudoArquivo'] = '';
            
        }

        
        if($this->input->post('numero_titulo') <> ''){
            
            $dados['numero_titulo'] = $this->input->post('numero_titulo');
        }else{
            
            $dados['numero_titulo'] = '';
        }
        
        if($this->input->post('nosso_numero') <> ''){
            $dados['nosso_numero'] = $this->input->post('nosso_numero');
        }else{
            $dados['nosso_numero'] = '';
        }
        
        if($this->input->post('nosso_numero_corresp') <> ''){
            $dados['nosso_numero_corresp'] = $this->input->post('nosso_numero_corresp');
        }else{
            $dados['nosso_numero_corresp'] = '';
        }
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'cobrancaFaturamento/view_pesquisa_conteudo_retorno', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
      	$this->layout->show('layout');
        
    }
    
    /**
     * cobrancaFaturamento::arquivosDuplicados()
     * 
     * @return
     */
    public function arquivosDuplicados(){
        
        $dados['duplicados'] = $this->arquivoCobranca->pegaDuplicados();
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'cobrancaFaturamento/view_duplicados', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
      	$this->layout->show('layout');
        
    }
    
    /**
     * cobrancaFaturamento::exeRemocaoArquivos()
     * 
     * @return
     */
    public function exeRemocaoArquivos(){
        
        $duplicados = $this->arquivoCobranca->pegaDuplicados();
        
        if($duplicados){
            
            /*echo '<pre>';
            print_r($duplicados);
            exit();*/
            
            foreach($duplicados as $dup){
                
                try{
                
                    $remocao = $this->arquivoCobranca->apagaRetornoDuplicado($dup->cd_arquivo_retorno);
                
                }catch( Exception $e ){
            
                    log_message('error', $e->getMessage());
                    
                }
                
                if($remocao){
                    $this->qtdArquivosRemovidos += 1;
                }
                
                #$this->qtdArquivosRemovidos += 1;
                #echo $dup->cd_arquivo_retorno; echo '<br>';
                
            }
            #echo '<br><br>';
            #echo 'TOTAL: '. $this->qtdArquivosRemovidos; echo '<br><br>';
            
            $this->exeRemocaoArquivos();
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success">Foram removidos '.$this->qtdArquivosRemovidos.' arquivos duplicados com sucesso!</div>');
            $dados['duplicados'] = $this->arquivoCobranca->pegaDuplicados();

            redirect(base_url('cobrancaFaturamento/arquivosDuplicados/'));
        }
        
    }
    
    /**
     * cobrancaFaturamento::salvaArquivo()
     * 
     * @return
     */
    /**
     * cobrancaFaturamento::salvaArquivo()
     * 
     * @return
     */
    public function salvaArquivo(){
        
        #print_r($_POST);
        
        try{
        
        # Grava o nome do banco retorna o ID
        $idArquivo = $this->arquivoCobranca->gravaNomeArquivoRetorno();
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
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
     * cobrancaFaturamento::upload_arquivo()
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
	 * Ajax::pegaQtdArquivosDiarios()
	 * 
     * Pega a quantidade diária de arquivos que foram processados
     * 
	 */
    public function pegaQtdArquivosDiarios(){
        
        $this->load->model('ArquivoCobranca_model','arquivoCobranca');
        $resDados['dados'] = $this->arquivoCobranca->qtdArquivosDiarios();
        $this->load->view('view_json',$resDados);
    }
    
    /**
     * cobrancaFaturamento::pesquisarBoleto()
     * 
     * @param mixed $cdArquivo
     * @return
     */
    /*public function pesquisarBoleto($cdArquivo)
	{
        $dados['dadosArquivo'] = $this->arquivoCobranca->dadosArquivo($cdArquivo);
		$this->load->view('pequisaBoleto', $dados);
	}*/
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */