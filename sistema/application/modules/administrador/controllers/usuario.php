<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe respons�vel pela usu�rio
*/
class Usuario extends Base
{
    
    private $dominioEmail = "@SIMTV.COM.BR";
    private $logDados;
    
	/**
	 * Usuario::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = 'Usuarios';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('administrador/usuario_model','usuario');  
        $this->load->model('administrador/ferias_model','ferias');
        $this->load->model('rh-usuario/rhferias_model','rh');

    }
    
    function index()
    {
        
      	$this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
    }
    /**
     * Usuario::usuarios()
     * 
     * @return
     */
    public function usuarios(){

        $dados['departamento'] = $this->dadosBanco->departamento();
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'usuario/view_psq_usuario', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
      	$this->layout->show('layout');
        
    }
    
    public function teste(){
        $this->rh->desativaFerias();
    }
    
    /**
     * Usuario::ficha()
     * 
     * Exibe a ficha para cadastro e atualiza��o do usu�rio
     * 
     * @param bool $cd Cd do usu�rio que quando informado carrega os dados do usu�rio
     * @return
     */
    public function ficha($cd = false){
        
        if($cd){
            
            $dados = $this->usuario->dadosUsuario($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->usuario->camposUsuario();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
        
        }
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $dados['departamento'] = $this->dadosBanco->departamento();
        
        $dados['estado'] = $this->dadosBanco->estado();
        $dados['unidade'] = $this->dadosBanco->unidade();
        
        $dados['perfil'] = $this->permissaoPerfil->perfil();
        $dados['cargos'] = $this->dadosBanco->cargos();
       
   	 #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(16, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'usuario/view_frm_usuario', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Usuario::salvar()
     * 
     * Cadastra ou atualiza o usu�rio
     * 
     * @return
     */
    public function salvar(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_usuario')){
            
            try{
            
                $status = $this->usuario->atualiza();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->usuario->insere();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_usuario'] = $status;
        }
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio salvo com sucesso!</strong></div>');
            
            redirect(base_url('usuario/ficha/'.$this->input->post('cd_usuario'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('usuario/ficha'));
            
        }
        
    }
    
    /**
     * Usuario::pesquisar()
     * 
     * Pesquisa o usu�rio
     * 
     * @param mixed $nome Nome do usu�rio para pesquisa
     * @param mixed $pagina P�gina corrente
     * @return
     */
    public function pesquisar($matricula = null, $nome = null, $departamento = null, $status = 'A', $sort_by = 'nome', $sort_order = 'asc', $pagina = null){
        #$this->output->enable_profiler(TRUE);
        $matricula = ($matricula == null)? '0': $matricula;
        $nome = ($nome == null)? '0': $nome;
        $departamento = ($departamento == null)? '0': $departamento;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                'matricula' => 'Matr&iacute;cula',
                                'login' => 'Login', 
                                'nome' => 'Nome', 
                                'cidade' => 'Cidade', 
                                'departamento' => 'Departamento', 
                                'perfil' => 'Perfil');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'matricula':
                $campoSortBy = 'CAST(matricula_usuario AS UNSIGNED INTEGER)';
                break;
            case 'login':
                $campoSortBy = 'login_usuario';
                break;
            case 'cidade':
                $campoSortBy = 'adminti.estado.cd_estado';
                break;
            case 'departamento':
                $campoSortBy = 'adminti.departamento.cd_departamento';
                break;
            case 'perfil':
                $campoSortBy = 'sistema.perfil.cd_perfil';
                break;
            default:
                $campoSortBy = 'nome_usuario';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postMatricula'] = ($this->input->post('matricula_usuario') != '')? $this->input->post('matricula_usuario') : $nome;
        $dados['postNome'] = ($this->input->post('nome_usuario') != '')? $this->input->post('nome_usuario') : $nome;
        $dados['postDepartamento'] = ($this->input->post('cd_departamento') != '')? $this->input->post('cd_departamento') : $departamento;
        $dados['postStatus'] = ($this->input->post('status_usuario') != '')? $this->input->post('status_usuario') : $status;
        
        $mostra_por_pagina = 30;
        $dados['usuarios'] = $this->usuario->psqUsuarios($dados['postMatricula'], $dados['postNome'], $dados['postDepartamento'], $dados['postStatus'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdUsuarios'] = $this->usuario->psqQtdUsuarios($dados['postMatricula'], $dados['postNome'], $dados['postDepartamento'], $dados['postStatus']); 

        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;   
        $dados['departamento'] = $this->dadosBanco->departamento();                 
        
        $config['base_url'] = base_url('usuario/pesquisar/'.$dados['postMatricula'].'/'.$dados['postNome'].'/'.$dados['postDepartamento'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdUsuarios'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 9;
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
        
        $dados['postMatricula'] = ($dados['postMatricula'] == '0')? '': $dados['postMatricula'];
        $dados['postNome'] = ($dados['postNome'] == '0')? '': $dados['postNome'];
        $dados['postDepartamento'] = ($dados['postDepartamento'] == '0')? '': $dados['postDepartamento'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'usuario/view_psq_usuario', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function resetaSenhaAD($cd_usuario){
        
        include_once('assets/adLDAP/src/adLDAP.php');
        
        //Vetor de dom�nios, � o servidor onde est� o AD, pode ter mais de um
        $srvDc = array('domain_controllers' => HOST_AD);
         
        //Criando um objeto da classe, passando as vari�veis do dom�nio ad.tinotes.net
        $adldap = new adLDAP(array('base_dn' => DASE_DN_AD,
                            'account_suffix' => ACCOUNT_SUFFIX,
                            'domain_controllers' => $srvDc
                     ));
         
        //Pego os dados via POST do formul�rio
        $usuario = 'teste.ti';
        $senha = 'simtv123';
        
        try{
        
            //Executo o m�todo autenticate, passando o usu�rio e senha do formul�rio
            $autentica = $adldap->authenticate($usuario, $senha);
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($autentica){
            echo 'existe';
        }else{
            echo 'nao existe';
        }
        
    }
    
    public function salvarFerias(){
        
        $inicio = $this->util->formataData($this->input->post('inicio'), 'USA');
        $fim = $this->util->formataData($this->input->post('fim'), 'USA');
        
        if(strtotime($inicio) > strtotime($fim)){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Data in&iacute;cio &eacute; maior que data fim! Verifique, por favor.</strong></div>');
            #$_POST['nome_usuario'] = $this->input->post('ferias-nome');
            #$this->pesquisar();
            #redirect(base_url('usuario/pesquisar/0/'.$this->input->post('ferias-nome').'/A/nome/asc/0'));
            redirect(base_url('usuario/usuarios'));
        }

        $status = $this->ferias->salvar();
        
        if($status){
            
            $this->logDados['descricao'] = utf8_encode('Usu�rios - Definido f�rias');
            #$this->logDados['idAcao'] = $this->input->post('tipo_email');
            $this->logDados['acao'] = 'UPDATE';
            $this->logGeral->grava($this->logDados);
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>F&eacute;rias do(a) '.$this->input->post('ferias-nome').' salva com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar f&eacute;rias, caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url('usuario/usuarios'));
    }
    
    public function informativoFerias(){

        $dados['feriasEntra'] = $this->ferias->feriasEntra();
        $dados['feriasVolta'] = $this->ferias->feriasVolta();
        $msg = $this->load->view('usuario/view_entra_sai_ferias', $dados, true);
        $usuario = $this->ferias->usuarioEnviaEmail(3);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Informe f�rias';
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = 'Informe de ferias';
        $para = 'equipe.sistemas@simtv.com.br';
        #$msg = '<strong>Vistoria - Reenvio</strong> - Segue em anexo<br><br>'.$titulo;
        #$this->util->enviaEmail($nomeDe, $emailDe, 'tiago.costa@simtv.com.br', $titulo, $msg, false);
        foreach($usuario as $usu){
            
            $this->util->enviaEmail($nomeDe, $emailDe, $usu->email_usuario, $titulo, $msg, false);
//            $this->util->enviaEmail($nomeDe, $emailDe, 'romulo.lobosco@simtv.com.br', $titulo, $msg, false);
        
        }
        
        $this->logDados['descricao'] = utf8_encode('Usu�rios - Informativo de f�rias');
        #$this->logDados['idAcao'] = $this->input->post('tipo_email');
        $this->logDados['acao'] = 'PROCESSANDO';
        $this->logGeral->grava($this->logDados);
        
    }
    
    public function apagaFerias($id){
        
        try{
        
            $status = $this->ferias->deleteFerias($id);  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>F&eacute;rias apagada com sucesso!</strong></div>');
            redirect(base_url('usuario/usuarios'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar f&eacute;rias, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('usuario/usuarios'));
        
        }
        
    }
    
    /**
     * Usuario::apaga()
     * 
     * Apaga o usu�rio
     * 
     * @return
     */
    public function apaga(){
        
        try{
        
            $status = $this->usuario->deleteUsuario();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio apagado com sucesso!</strong></div>');
            redirect(base_url('usuario/usuarios'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('usuario/usuarios'));
        
        }
    }
                
}
