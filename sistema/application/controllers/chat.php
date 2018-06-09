<?php 
include_once('base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class chat extends Base {
     
    /**
     * chat::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        $this->load->model('Relatorio_model','relatorio'); 
        $this->load->model('permissaoPerfil_model','permissaoPerfil');
        $this->load->model('chat_model','chat');
         
	} 
     
	/**
     * chat::index()
     * 
     * Lista os relatórios existentes
     * 
     */
	public function index()
	{ 
        
	}
    
    /**
     * chat::uploadFile()
     * 
     * Realiza o upload dos arquivos do chat
     * 
     */
    public function uploadFile(){
        #echo '<pre>'; print_r($_FILES); exit();
        $dir = './files/chat/arquivos/'.date('Y').'/'.date('m');
        
        if(!file_exists ( $dir )){
            mkdir($dir, 0777, true);
        }
        
        $cd_arquivo = $this->chat->insereArquivoConversa();
        
        $config['upload_path'] = $dir;
        #$config['allowed_types'] = 'txt';
		$config['allowed_types'] = '*';
		$config['max_size'] = '50000';
		#$config['max_width'] = '0';
		#$config['max_height'] = '0';
		#$config['encrypt_name'] = true;
        
        #$file_name = md5($_FILES['file_var_name']['name']);
        #$file_name = md5($_FILES['file']['name']);
        $file_name = $cd_arquivo;
        $config['file_name'] = $file_name;
                        
        $status = $this->util->uploadArquivo('file', $config);
        
        if($status['status']){
            echo json_encode(array('status'=>base64_encode($cd_arquivo)));
            
            #return array('status' => 'OK');
            #echo 'ok';
        }else{
            echo json_encode(array('status'=>'erro'));
            #return array('status' => 'ERRO');
            #echo 'erro';
        }
        
    }
    
    public function downloadFile($cd_arquivo){
        
        $cd_arquivo = base64_decode($cd_arquivo);
        
        $dadosArquivo = $this->chat->dadosFile($cd_arquivo);
        
        $arquivo = $dadosArquivo->cd_chat_msg.'.'.$dadosArquivo->extensao; // Nome do Arquivo
        $local = $dadosArquivo->diretorio.'/'; // Pasta que contém os arquivos para download
        $local_arquivo = $local.$arquivo; // Concatena o diretório com o nome do arquivo
        
        #echo '<pre>'; print_r($local_arquivo); exit();
        if(stripos($arquivo, './') !== false || stripos($arquivo, '../') !== false || !file_exists($local_arquivo))
        {
        #echo 'O comando não pode ser executado.';
        exit();
        }
        else
        {
        header('Cache-control: private');
        header('Content-Type: application/octet-stream');
        header('Content-Length: '.filesize($local_arquivo));
        header('Content-Disposition: filename='.$arquivo);
        header("Content-Disposition: attachment; filename=".basename($local_arquivo));
        
        // Envia o arquivo Download
        readfile($local_arquivo);
        }
        
    }
    
    public function apaga(){
        
        $this->util->limpaArquivos();
        $arquivos = $this->util->buscaArquivosDiretorios('./files/chat/arquivos/'.date('Y').'/'.date('m'));
        foreach($arquivos as $ar){
            @unlink($ar);
        }
        @rmdir('./files/chat/arquivos/'.date('Y').'/'.date('m'));
        @rmdir('./files/chat/arquivos/'.date('Y'));
        exit();
    }
    
    /**
     * chat::atualizaStatusUsuario()
     * 
     * Atualiza status do usuário
     * 
     */
    public function atualizaStatusUsuario(){
        
        $status = $this->chat->atualizaStatusUsuario();
        
        $this->session->set_userdata('chatStatus', $this->input->post('chatStatus'));
        $this->session->set_userdata('chatDataHora', date('Y-m-d H:i:s'));
        
        if($status){
            echo json_encode(array('status'=>'ok'));
        }else{
            echo json_encode(array('status'=>'erro'));
        }
        
    }
    
    /**
     * chat::statusAbertura()
     * 
     * Atualiza a sessão da abertura de chat
     * 
     */
    public function statusAbertura(){
        
        $this->session->set_userdata('chatOpen', $this->input->post('status'));
        
        $contAbertura = $this->session->userdata('chatContOpen')+1;
        $this->session->set_userdata('chatContOpen', $contAbertura);
        
    }
    
    /**
     * chat::statusAbertura()
     * 
     * Adiciona o usuário na lista de favoritos
     * 
     */
    public function addFavoritos(){
        
        $status = $this->chat->addFavoritos();
        if($status){
            echo json_encode(array('status'=>'ok'));
        }else{
            echo json_encode(array('status'=>'erro'));
        }
        
    }
    
    /**
     * chat::removeFavoritos()
     * 
     * Remove o usuário na lista de favoritos
     * 
     */
    public function removeFavoritos(){
        
        $status = $this->chat->removeFavoritos();
        if($status){
            echo json_encode(array('status'=>'ok'));
        }else{
            echo json_encode(array('status'=>'erro'));
        }
        
    }
    
    /**
     * chat::pesquisaUsuario()
     * 
     * Retorna os usuários pesquisados
     * 
     */
    public function pesquisaUsuario(){
        
        $res = $this->chat->usuarios(false, false, $this->input->post('usuario'));
        
        if(count($res) > 0){
            $resDados['dados'] = $res;
        }else{
            $resDados['dados']['status'] = false;
        }

		$this->load->view('view_json',$resDados);
        
    }
    
    /**
     * chat::statusUsuarios()
     * 
     * Verifica os status dos usuários
     * 
     */
    public function statusUsuarios(){
        
        $res = $this->chat->statusUsuarios();
        
        if(count($res) > 0){
            $this->session->unset_userdata('chatRequestStatus');
            
            $ultimaData = $res[count($res)-1]->data_chat_usuario; 
            $this->session->set_userdata('chatRequestStatus', $ultimaData);
            $resDados['dados'] = $res;
        }else{
            $resDados['dados']['status'] = false;
        }
        
		$this->load->view('view_json',$resDados);
        
    }
    
    /**
     * chat::qtdNewMsgPorConversa()
     * 
     * Pega a quantidade de novas conversas
     * 
     */
    public function qtdNewMsgPorConversa(){
        
        $res = $this->chat->qtdNewMsgPorConversa();
        /*
        if(count($res) > 0){
            $this->session->unset_userdata('chatRequestNewConversa');
            
            $ultimaData = $res[count($res)-1]->data_envio; 
            $this->session->set_userdata('chatRequestNewConversa', $ultimaData);
        }
        */
        #$resDados['dados']['data'] = $this->input->post('dataUltimoNovaMsg');
        
        if(count($res) > 0){
            $resDados['dados'] = $res;
        }else{
            $resDados['dados']['status'] = false;
        }

		$this->load->view('view_json',$resDados);
        
    }
    
    /**
     * chat::abreConversa()
     * 
     * Pega os dados para abertura de nova conversa
     * 
     */
    public function abreConversa(){
        #$_POST['user'] = '35-15'; $_POST['tipo'] = 'dp'; $_POST['dataCorrente'] = '2015-12-29';
        $this->chat->configDinamica();
        
        if($this->input->post('tipo') == 'user'){
        
            $user = $this->chat->usuarioConversa();
            $msgs = $this->chat->historicoUserConversa();
            $resDados['dados']['usuario'] = $user;
        
        }else{
            
            $dpUni = explode('-', $this->input->post('user'));
            
            $dp = $this->chat->dpConversa($dpUni[0]);
            $uni = $this->chat->uniConversa($dpUni[1]);
            $msgs = $this->chat->historicoDpConversa($dpUni[0],$dpUni[1]);
            
            $resDados['dados']['departamento'] = $dp;
            $resDados['dados']['unidade'] = $uni;
            
        }
        
        $resDados['dados']['anterior'] = $this->chat->verificaMsgAnterior($msgs[0]->data_envio_original); 
        $resDados['dados']['msgs'] = $msgs;
        #echo '<pre>'; print_r($resDados['dados']); exit();
		$this->load->view('view_json',$resDados);
        
    }
    
    public function adicionaConversaAnterior(){
        
        if($this->input->post('tipo') == 'user'){
            
            $msgs = $this->chat->historicoUserConversa('paginacao');
            
        }else{
            
            $dpUni = explode('-', $this->input->post('user'));
            $msgs = $this->chat->historicoDpConversa($dpUni[0],$dpUni[1], 'paginacao');
            
        }
        
        #echo $this->chat->verificaMsgAnterior($msgs[0]->data_envio_original); exit();
        $resDados['dados']['anterior'] = $this->chat->verificaMsgAnterior($msgs[0]->data_envio_original);
        $resDados['dados']['msgs'] = $msgs;
		$this->load->view('view_json',$resDados);
        
    }
    
    /**
     * chat::defineDinamica()
     * 
     * Define a dinâmica
     * 
     */
    public function defineDinamica(){
        
        $this->chat->defineDinamica();
        
    }
    
    public function consultaDinamica(){
        
        $resDados['dados'] = $this->chat->consultaDinamica();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
     * chat::statusMsgLida()
     * 
     * Atualiza as mensagens lidas para os status de LIDA
     * 
     */
    public function statusMsgLida(){
        
        $status = $this->chat->statusMsgLida();
        $resDados['dados']['status'] = $status;
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
     * chat::insereConversa()
     * 
     * Insere a nova mensagem 
     * 
     */
    public function insereConversa(){

        $cd_novo_msg = $this->chat->insereConversa();
        
        if($cd_novo_msg){
            $resDados['dados'] = $this->chat->dadosMsg($cd_novo_msg);
        }else{
            $resDados['dados']['status'] = false;
        }
        $this->load->view('view_json',$resDados); 
        
    }
    
    /**
     * chat::pegaNovasMsgs()
     * 
     * Pega as novas mensagens
     * 
     */
    public function pegaNovasMsgs(){
        
        $res = $this->chat->pegaNovasMsgs();  
        
        if(count($res) > 0){
            $this->session->unset_userdata('chatRequestNewMsg');
            
            $ultimaData = $res[count($res)-1]->data; 
            $this->session->set_userdata('chatRequestNewMsg', $ultimaData);
            $resDados['dados'] = $res;
        }else{
            $resDados['dados']['status'] = false;
        }
        
		$this->load->view('view_json',$resDados);  
        
    }
    
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */