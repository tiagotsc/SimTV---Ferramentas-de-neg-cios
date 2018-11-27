<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php'); 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class relatorio extends Base {
     
    /**
     * relatorio::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        $this->load->model('Relatorio_model','relatorio'); 
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        
	} 
     
	/**
     * relatorio::index()
     * 
     * Lista os relatórios existentes
     * 
     */
	public function index()
	{ 

       #if(in_array($this->session->userdata('cd'),array(3891))){
        #echo 'teste'; exit();
       #}
       if(!$this->session->userdata('permissoes')){
            echo 'Falha na permissão'; exit();
       }
       
       $departamentos = $this->relatorio->departamentosRelatorios();
       
       foreach($departamentos as $depar){
            $relatorios[$depar->cd_departamento] = $this->relatorio->relatoriosCategorias($depar->cd_departamento, $this->session->userdata('permissoes'));
       }
       
       $dados['departamentos'] = $departamentos;
       $dados['relatorios'] = $relatorios;
       
       #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
       
	    #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_relatorio',$dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
	}
    
    public function gerenciar(){
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $dados['departamento'] = $this->relatorio->departamentosRelatorios();
        
	    #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_psq_relatorio', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function ficha($cd = false){
        
        if($cd){
            
            $pegaPerfisRelatorio = $this->relatorio->perfilRelatorio($cd);
            
            foreach($pegaPerfisRelatorio as $pPr){
                
                $perfilRelatorio[] = $pPr->cd_perfil;
                
            }
            
            $parametros = $this->relatorio->relatorioParametro($cd);
            
            $nomes_parametros = $this->relatorio->parametrosDoRelatorio($cd);
            
            foreach($parametros as $param){
                
                $rel_param[] = $param->cd_parametro;
                
            }
            
            $rel_param[] = 'A';
            $rel_param[] = 'B';
            
            #echo '<pre>'; print_r($parametros); exit();
            $dados = $this->relatorio->dadosRelatorio($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $dados['rel_param'] = $rel_param;
            
            $dados['nome_parametros'] = $nomes_parametros;
            
        }else{
            
            $perfilRelatorio[] = array();
            
            $campos = $this->relatorio->camposRelatorio();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
        
            $dados['rel_param'] = array('A','B');
            
            $dados['nome_parametros'] = false;
        
        }
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
       
        $dados['departamento'] = $this->dadosBanco->departamento();
        $dados['parametros'] = $this->dadosBanco->parametro();
        
        $dados['perfis'] = $this->permissaoPerfil->perfil();
        
        $dados['perfilRelatorio'] = $perfilRelatorio;
        
	    #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(34, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'view_frm_relatorio', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');   
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function salvar(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_relatorio')){
            
            try{
            
                $status = $this->relatorio->atualiza();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->relatorio->insere();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_relatorio'] = $status;
        }
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Relat&oacute;rio salvo com sucesso!</strong></div>');
            
            redirect(base_url('relatorio/ficha/'.$this->input->post('cd_relatorio'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar relat&oacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('relatorio/ficha'));
            
        }
        
    }
    
    /**
     * Relatorio::pesquisar()
     * 
     * Pesquisa o relatório
     * 
     * @param mixed $nome Nome do relatório para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesquisar($nome = null, $departamento = null, $status = null, $pagina = null){
        
        $nome = ($nome == null)? '0': $nome;
        $status = ($status == null)? '0': $status;
        $departamento = ($departamento == null)? '0': $departamento;
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postNome'] = ($this->input->post('nome_relatorio') != '')? $this->input->post('nome_relatorio') : $nome;
        $dados['postStatus'] = ($this->input->post('status_relatorio') != '')? $this->input->post('status_relatorio') : $status;
        $dados['postDepartamento'] = ($this->input->post('cd_departamento') != '')? $this->input->post('cd_departamento') : $departamento;
        
        $mostra_por_pagina = 30;
        $dados['relatorios'] = $this->relatorio->psqRelatorios($dados['postNome'], $dados['postDepartamento'], $dados['postStatus'], $pagina, $mostra_por_pagina);   
        $dados['qtdRelatorios'] = $this->relatorio->psqQtdRelatorios($dados['postNome'], $dados['postDepartamento'], $dados['postStatus']);  
        
        $dados['departamento'] = $this->relatorio->departamentosRelatorios();                   
        
        $config['base_url'] = base_url('relatorio/pesquisar/'.$dados['postNome'].'/'.$dados['postDepartamento'].'/'.$dados['postStatus']); 
		$config['total_rows'] = $dados['qtdRelatorios'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 6;
        $config['first_link'] = '&lsaquo; Primeiro';
        $config['last_link'] = '&Uacute;ltimo &rsaquo;';
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
        
        $dados['postNome'] = ($dados['postNome'] == '0')? '': $dados['postNome'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];
        $dados['postDepartamento'] = ($dados['postDepartamento'] == '0')? '': $dados['postDepartamento'];
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_psq_relatorio', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function copiar(){
        
        $cd = $this->relatorio->copiarRelatorio();  
        
        if($cd){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Relat&oacute;rio copiado com sucesso!</strong></div>');
            
            redirect(base_url('relatorio/ficha/'.$cd)); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao copiar relat&oacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('relatorio/ficha'));
            
        }
        
    }
    
    /**
     * Relatório::apaga()
     * 
     * Apaga o relatório
     * 
     * @return
     */
    public function apaga(){
        
        try{
        
            $status = $this->relatorio->deleteRelatorio();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Relat&oacute;rio apagado com sucesso!</strong></div>');
            redirect(base_url('relatorio/gerenciar'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar Relat&oacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('relatorio/gerenciar'));
        
        }
    }
    
    /**
     * Relatorio::baixarRelatorio()
     * 
     * Gera o excel do relatório solicitado
     * 
     */
    public function baixarRelatorio(){ 
        
        set_time_limit(0);
        
        $this->relatorio->registraAcessoRelatorio();
        
        $queryBancoRelatorio = $this->relatorio->dadosBancoRelatorio();
        
        $query = $queryBancoRelatorio[0]->query_relatorio;
        
        foreach($_POST as $campo => $valor){
            
            $valor = $this->util->formaValorBanco($valor);
            
            $query = str_ireplace('**'.$campo.'**', $valor, $query);
            
        }
        
        $query = str_ireplace('**SESSION_PERFIL**', $this->session->userdata('perfil'), $query);
        $query = str_ireplace('**SESSION_DP**', $this->session->userdata('departamento'), $query);
        $query = str_ireplace('**SESSION_CARGO**', $this->session->userdata('cargo'), $query);
        $query = str_ireplace('**SESSION_UNIDADE**', $this->session->userdata('unidade'), $query);

        try{

            $executa = $this->relatorio->rodaQuery($query, $queryBancoRelatorio[0]->banco_relatorio);
            
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $dados['valores'] = ($executa)? $executa: '';
        $dados['campos'] = ($executa)? array_keys($dados['valores'][0]): '';

        $this->load->view('view_baixa_relatorio', $dados);
        
    }
    
    public function testaTxt(){
        
        $query = "SELECT 
                	tcentroCusto.codigo AS centro_custo,
                	CASE WHEN tusuario.cd_usuario IN 
                		(
                			SELECT DISTINCT cd_usuario FROM adminti.telefonia_emprestimo
                		)
                		THEN 'SIM'
                	ELSE 'NAO' END AS emprestimo,
                	tddd.ddd,
                	identificacao AS chip,
                	numero AS linha,
                	(
                		SELECT
                			GROUP_CONCAT(nome)
                		FROM adminti.telefonia_linha_servico AS tLs
                		INNER JOIN adminti.telefonia_servico AS ts ON ts.cd_telefonia_servico = tLs.cd_telefonia_servico
                		WHERE 
                		CURDATE() BETWEEN tLs.data_inicio AND tLs.data_fim
                		AND tLs.cd_telefonia_linha = tlinha.cd_telefonia_linha
                	) AS servicos_linha,
                	toperadora.nome AS operadora,
                	tplano.nome AS plano,
                	tlinha.tipo AS tipo_linha,
                	CASE 
                		WHEN tlinha.status = 'A'
                			THEN 'Ativo'
                		WHEN tlinha.status = 'E'
                			THEN 'Estoque'
                	ELSE 'Inativo' END AS status_linha,
                	DATE_FORMAT(temprestimo.data_inicio, '%d/%m/%Y') AS inicio_emprestimo,
                	DATE_FORMAT(temprestimo.data_fim, '%d/%m/%Y') AS fim_emprestimo,
                	DATE_FORMAT(temprestimo.data_termo, '%d/%m/%Y') AS data_termo,
                	DATE_FORMAT(temprestimo.data_criacao_termo, '%d/%m/%Y') AS data_criacao_termo,
                	temprestimo.aceite_termo,
                	DATE_FORMAT(temprestimo.data_aceite_termo, '%d/%m/%Y') AS data_aceite_termo,
                	tmarca.nome AS marca_aparelho,
                	modelo,
                	taparelho.tipo AS tipo_aparelho,
                	taparelho.nota_fiscal,
                	DATE_FORMAT(taparelho.data_inicio, '%d/%m/%Y') AS inicio_comodado,
                	DATE_FORMAT(taparelho.data_fim, '%d/%m/%Y') AS fim_comodado,
                	taparelho.status AS status_aparelho,
                	matricula_usuario,
                	nome_usuario,
                	email_usuario,
                	nome_departamento,
                	tcargo.nome AS cargo,
                	tunidade.nome AS unidade,
                	testado.nome_estado AS estado,
                	tfebraban.tipo,
                	tfebraban.dt_vencimento,
                	tfebraban.dt_emissao,
                	tfebraban.uf_localidade,
                	tfebraban.localidade,
                	tfebraban.dt_ligacao,
                	tfebraban.desc_operadora,
                	tfebraban.ddd_destino,
                	tfebraban.telefone_destino,
                	tfebraban.uf_destino,
                	tfebraban.horario_ligacao,
                	tfebraban.duracao_ligacao,
                	tfebraban.categoria,
                	tfebraban.desc_categoria,
                	tfebraban.tipo_chamada,
                	tfebraban.desc_horario_tarifario,
                	tfebraban.degrau_ligacao,
                	tfebraban.sinal_valor_ligacao,
                	tfebraban.aliquota_valor,
                	tfebraban.valor_ligacao,
                	tfebraban.ini_assinatura,
                	tfebraban.fim_assinatura,
                	tfebraban.ini_servico,
                	tfebraban.fim_servico,
                	tfebraban.valor_assinatura,
                	tfebraban.valor_icms,
                	tfebraban.nota_fiscal AS nota_fiscal_fatura,
                	tfebraban.valor_conta,
                	tfebraban.valor_total_impostos,
                	tfebraban.cd_log_arquivo
                	#tfebraban.*
                FROM adminti.telefonia_linha AS tlinha
                INNER JOIN adminti.telefonia_operadora AS toperadora ON toperadora.cd_telefonia_operadora = tlinha.cd_telefonia_operadora
                INNER JOIN adminti.telefonia_plano AS tplano ON tplano.cd_telefonia_plano = tlinha.cd_telefonia_plano
                INNER JOIN adminti.telefonia_ddd AS tddd ON tddd.cd_telefonia_ddd = tlinha.cd_telefonia_ddd
                LEFT JOIN adminti.telefonia_emprestimo_linha AS tEmpLinha ON tEmpLinha.cd_telefonia_linha = tlinha.cd_telefonia_linha
                LEFT JOIN adminti.telefonia_emprestimo AS temprestimo ON temprestimo.cd_telefonia_emprestimo = tEmpLinha.cd_telefonia_emprestimo
                LEFT JOIN adminti.telefonia_aparelho AS taparelho ON taparelho.cd_telefonia_aparelho = temprestimo.cd_telefonia_aparelho
                LEFT JOIN adminti.telefonia_marca AS tmarca ON tmarca.cd_telefonia_marca = taparelho.cd_telefonia_marca
                LEFT JOIN adminti.usuario AS tusuario ON tusuario.cd_usuario = temprestimo.cd_usuario
                LEFT JOIN adminti.departamento AS tdepartamento ON tdepartamento.cd_departamento = tusuario.cd_departamento
                LEFT JOIN adminti.cargo AS tcargo ON tcargo.cd_cargo = tusuario.cd_cargo
                LEFT JOIN adminti.unidade AS tunidade ON tunidade.cd_unidade = tusuario.cd_unidade
                LEFT JOIN adminti.estado AS testado ON testado.cd_estado  = tusuario.cd_estado
                LEFT JOIN adminti.centro_custo AS tcentroCusto ON tcentroCusto.cd_departamento = tdepartamento.cd_departamento AND tcentroCusto.cd_unidade = tunidade.cd_unidade
                LEFT JOIN adminti.telefonia_febraban AS tfebraban ON CONCAT(tfebraban.ddd,tfebraban.telefone) = CONCAT(tddd.ddd,tlinha.numero)
                WHERE 
                #CONCAT(tddd.ddd,tlinha.numero) = '6581452530' AND
                tfebraban.dt_emissao LIKE '2015-12%'
                ORDER BY tusuario.nome_usuario, tfebraban.tipo";
        
        $executa = $this->relatorio->rodaQuery($query, 'mysql');
        $dados['valores'] = ($executa)? $executa: '';
        $dados['campos'] = ($executa)? array_keys($dados['valores'][0]): '';
        
        $name = './temp/arquivo.txt';
        $file = fopen($name, 'a');
        $text = implode(';', $dados['campos']);
        fwrite($file, $text."\r\n");
        foreach($dados['valores'] as $valores){
            fwrite($file, implode(';', $valores)."\r\n");
        }
        fclose($file);
        
        header('Cache-control: private');
        header('Content-Type: application/octet-stream');
        header('Content-Length: '.filesize($name));
        header('Content-Disposition: filename='.basename($name));
        header("Content-Disposition: attachment; filename=".basename($name));
        
        // Envia o arquivo Download
        readfile($name);
        
        if(file_exists($name)){
            @unlink($name); // aqui apaga
        }
        
        #echo '<pre>';
        #print_r($executa);
        exit();
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */