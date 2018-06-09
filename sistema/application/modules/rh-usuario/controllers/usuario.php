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

    private $logDados;
    const modulo = 'rh-usuario';
    const controller = 'usuario';
    const pastaView = 'usuario';
    const tabela = 'adminti.usuario';
    const assunto = 'Usuario';
    const modelAssunto = 'usuario';
    const perModulo = 359;
    const perPesq = 16;
    const perCadEdit = 16;
    const perLimCadEdit = 362;
    const perDeletar = 1;
    const perFerias = 302;
    

    /**
     * Usuario::__construct()
     *
     * @return
     */
    public function __construct(){

        parent::__construct();

        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];

        $this->load->library('Crud', '', 'crud');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('usuario_model', self::modelAssunto);
        $this->load->model('rh-usuario/rhferias_model','ferias');
        $this->load->model('rh-beneficio/beneficio_model','beneficio');
        $this->load->model('rh-usuario/faltas_model', 'faltas');

    }

    function index(){

        $this->layout->region('html_header', 'view_html_header');
        #$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
    }
    
//    public function teste(){}

    /**
     * Usuario::usuarios()
     *
     * @return
     */
    public function usuarios(){
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        $this->layout->show('layout');

    }


    //Metodos referentes a cadastro de ferias - INICIO

        public function salvarFerias(){

            $inicio = $this->util->formataData($this->input->post('inicio'), 'USA');
            $fim = $this->util->formataData($this->input->post('fim'), 'USA');
            $cd_usuario = $_POST['cd_usuario'];
            
//            echo base_url('admin-usuario/usuario/ficha/'.$cd_usuario);
//            exit();

            if(strtotime($inicio) > strtotime($fim)){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Data in&iacute;cio &eacute; maior que data fim! Verifique, por favor.</strong></div>');
                redirect(base_url('rh-usuario/usuario/usuarios'));
            }

            $status = $this->ferias->salvaFerias();

            if($status){

                $this->logDados['descricao'] = utf8_encode('Usu�rios - Definido f�rias');
                $this->logDados['acao'] = 'UPDATE';
                $this->logGeral->grava($this->logDados);

                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>F&eacute;rias do(a) '.$this->input->post('ferias-nome').' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar f&eacute;rias, caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url('rh-usuario/usuario/ficha/'.$cd_usuario));
        }

        public function informativoFerias(){

            $dados['feriasEntra'] = $this->ferias->feriasEntra();
            $dados['feriasVolta'] = $this->ferias->feriasVolta();
            $msg = $this->load->view('usuario/view_entra_sai_ferias', $dados, true);
            $usuario = $this->ferias->usuarioEnviaEmail(3);

            $nomeDe = 'Sim TV - Ferramenta de negocios | Informe f�rias';
            $emailDe = 'naoresponda@simtv.com.br';
            $titulo = 'Informe de ferias';
            $para = 'suporte@simtv.com.br';
            foreach($usuario as $usu){

                $this->util->enviaEmail($nomeDe, $emailDe, $para, $titulo, $msg, false);

            }
            $this->ferias->desativaFerias();
            $this->logDados['descricao'] = utf8_encode('Usu�rios - Informativo de f�rias');
            $this->logDados['acao'] = 'PROCESSANDO';
            $this->logGeral->grava($this->logDados);

        }

        public function apagaFerias($id){
            
            try{

                $status = $this->ferias->deleteFerias($_POST['id_ferias']);

            }catch( Exception $e ){

                log_message('error', $e->getMessage());

            }

            if($status){

                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>F&eacute;rias apagada com sucesso!</strong></div>');
                redirect(base_url('rh-usuario/usuario/ficha').'/'.$_POST['cd_usuario']);

            }else{

                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar f&eacute;rias, caso o erro persiste comunique o administrador!</div>');
                redirect(base_url('rh-usuario/usuario/ficha').'/'.$_POST['cd_usuario']);

            }

        }

    //Metodos referentes a cadastro de ferias - FIM


    //CRUD - INICIO

    /**
     * Usuario::ficha()
     *
     * Exibe a ficha para cadastro e atualiza��o do usu�rio
     *
     * @param bool $cd Cd do usu�rio que quando informado carrega os dados do usu�rio
     * @return
     */
    public function ficha($cd = false){
        
        
        $modelAssunto = self::modelAssunto;

        if($cd){

        //--------------- Edicao de usuario ---------------

            $dados = $this->usuario->dadosUsuario($cd);

            $campos = array_keys($dados);

            $dados['perfil'] = $this->controlaAcesso(FALSE);
            
            $dados ['vale_transporte'] = $this->beneficio->retornaValeTransporte($cd);


            foreach($campos as $campo){

                $info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
            }

        //--------------- Editicao de usuario ---------------

        }else{
        //--------------- Cadastro de usuario ---------------

            $campos = $this->usuario->camposUsuario();

            $dados['perfil'] = $this->controlaAcesso(TRUE);


            foreach($campos as $campo){
                $info[$campo] = '';
           }
        //--------------- Cadastro de usuario ---------------
        }

        $dados['departamento'] = $this->dadosBanco->departamento();
        $dados['celulasSelecionadas'] = $this->$modelAssunto->retornaSolicitacoesSistema($cd);
        $dados['perLimCadEdit'] = self::perLimCadEdit;
        $dados['estado'] = $this->dadosBanco->estado();
        $dados['unidade'] = $this->dadosBanco->unidade();
        $dados['feriasAtivas'] = $this->ferias->retornaFerias($cd,'A');
        $dados['feriasInativas'] = $this->ferias->retornaFerias($cd);
        $dados['cargos'] = $this->dadosBanco->cargos();
        $dados['sistemas'] = $this->$modelAssunto->retornaSistemas();
        $dados['passagens'] = $this->beneficio->retornaPassagem();
        
        
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');

        if(in_array(self::perCadEdit, $this->session->userdata('permissoes'))){

            $this->layout->region('corpo', self::pastaView.'/view_frm', $dados);

        }else{

            $this->layout->region('corpo', 'view_permissao');
        }

        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');

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
            redirect(base_url('rh-usuario/usuario/pesq'));

        }else{

            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('rh-usuario/usuario/pesq'));

        }
    }

    public function pesq(){

        $mostra_por_pagina = 30;
        $modelAssunto = self::modelAssunto;

        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;


        $resultado = $this->$modelAssunto->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);

        $resultado['tabela'] = self::assunto;

        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 

        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['departamento'] = $this->dadosBanco->departamento();
        $crud['perEditarCadastrar'] = self::perCadEdit;
        $crud['perCadastFerias'] = self::perFerias;
        $crud['perExcluir'] = self::perDeletar;
        $crud['assunto'] = self::assunto;


        $this->layout->region('html_header', 'view_html_header');

        if(in_array(self::perPesq, $this->session->userdata('permissoes'))){

            $this->layout->region('corpo', self::pastaView.'/view_psq', $crud);

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
        $model = self::modelAssunto;

        if($this->input->post('cd_usuario')){

            try{

                $status = $this->usuario->atualiza();
                $_POST['tipo_email'] = 'Atualizacao';
                
//                $this->logDados['descricao'] = utf8_encode('Atualizacao de usuario');
//                $this->logDados['acao'] = 'UPDATE';
//                $this->logDados['idAcao'] = $this->input->post('cd_usuario');
//                $this->logGeral->grava($this->logDados);

            }catch( Exception $e ){

                log_message('error', $e->getMessage());

            }

        }else{

            try{
                
                $_POST['matricula_usuario'] = $this->$model->geraMatricula();
                $status = $this->usuario->insere();
                $_POST['tipo_email'] = 'Cadastro';
                
//                $this->logDados['descricao'] = utf8_encode('Cadastro de usuario');
//                $this->logDados['acao'] = 'INSERT';
//                $this->logDados['idAcao'] = $this->input->post('cd_usuario');
//                $this->logGeral->grava($this->logDados);

            }catch( Exception $e ){

                log_message('error', $e->getMessage());

            }

            $_POST['cd_usuario'] = $status;
        }

        if($status){

            if(in_array(self::perLimCadEdit, $this->session->userdata('permissoes'))){
                
                $this->envioEmail();
            }
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio salvo com sucesso!</strong></div>');
            redirect(base_url(self::modulo.'/'.self::modelAssunto.'/ficha').'/'.$this->input->post('cd_usuario'));

        }else{

            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url(self::modulo.'/'.self::modelAssunto.'/ficha/'));
        }
    }

    //CRUD - FIM



    //Metodos de envio de e-mail - Inicio
    
    public function envioEmail(){
        
        $modelAssunto = self::modelAssunto;
        $_POST['dados_usuario'] = $this->usuario->dadosEmail($_POST['cd_usuario']); 
        $_POST['dados_sistemas'] = $this->usuario->retornaSistemas($_POST['cd_usuario']);
        
        $nomeDe = 'Sim TV - Ferramenta de negocios | Cadastro - '.self::assunto;
        $emailDe = 'naoresponda@simtv.com.br';
        $titulo = utf8_decode('Cadastro de '.self::assunto);
        $para = 'suporte@simtv.com.br';
        $msg = $this->geraMensagem();
        $this->util->enviaEmail($nomeDe, $emailDe, $para, $titulo, $msg, $ordemPdf);

    }

    private function geraMensagem(){   
        
        if($_POST['sistemas'] <> NULL){
            $te = '<br>
                    <div>
                    <table border="1">
                        <tr>
                            <th>Acessos necessarios</th>
                        </tr>';
            
            foreach($_POST['dados_sistemas'] as $dados){
                $te = $te. '<tr><td>'.$dados['nome_sistema'].'</td></tr>';
            }
            $te = $te .'</table></div>';
        }
        
        
        
        
        $msg = '<strong>'.$_POST['tipo_email'].' de usuario</strong>
                <br><br><br>
                <table border="1">
                    <tr>
                        <th colspan="2">Informacoes do colaborador</th>
                    </tr>
                    <tr>
                        <td>Nome</td>
                        <td>'.$_POST['dados_usuario']->nome_usuario.'</td>
                    </tr>
                    <tr>
                        <td>Departamento</td>
                        <td>'.$_POST['dados_usuario']->nome_departamento.'</td>
                    </tr>
                    <tr>
                        <td>Cargo</td>
                        <td>'.$_POST['dados_usuario']->nome.'</td>
                    </tr>
                    <tr>
                        <td>Unidade</td>
                        <td>'.$_POST['dados_usuario']->nome_estado.'</td>
                    </tr>
                </table>';
                    
        $msg = $msg . $te;
                       
        return $msg;
    }
    
    //Metodos de envio de e-mail - Fim
    
    //Metodos auxiliares

    public function controlaAcesso($testaOpcao){

        if($testaOpcao){
        //Operacao de cadastro de usuario

            //Testa se o usuario é administrador

            if(!in_array(self::perLimCadEdit, $this->session->userdata('permissoes'))){
                //administrador

                return $this->usuario->retornaPerfil();
            }
            else{
                //Perfil limitado
                
                return $this->usuario->retornaPerfil(24);

            }

        }else{
        //Operacao de edicao de usuario

            //Testa se o usuario é administrador

            if(!in_array(self::perLimCadEdit, $this->session->userdata('permissoes'))){

                //administrador

                return $this->usuario->retornaPerfil();
            }
            else{
                //Perfil limitado
                
                return $this->usuario->retornaPerfil($dados['cd_perfil']);
            }
        }
    }
    
    public function imprimeVetor($vetor){
        
        echo '<pre>';
        print_r($vetor);
        echo '</pre>';
        exit();
    }
    
}