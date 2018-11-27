<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do e-mail
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Email_model extends CI_Model{
	
    private $dominioEmail = '@SIMTV.COM.BR';
    private $qtdUnidades = false;
    
	/**
	 * Email_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        
        if(!$this->qtdUnidades){
            $this->qtdUnidades = $this->getQtdUnidades();
        }
        
	}
    
    public function setDominioEmail($domino){
        $this->dominioEmail = $domino;
    }
    
    public function getQtdUnidades(){
        
        $this->db->where('status', 'A');
        return $this->db->count_all_results('adminti.unidade');
        
    }
    
    public function tiposEmailsPermitidos(){
        
        $sql = "SELECT 
                * 
                FROM email_envia WHERE cd_permissao IN (
                ".implode(',',$this->session->userdata('permissoes'))."
                )";
        return $this->db->query($sql)->result();
        
    }
    
    public function usuariosEmail($tipoEmail, $permissor, $nome_email, $departamento, $unidade, $funcao){

        $this->db->select("	usuario.cd_usuario, 
                        	usuario.matricula_usuario, 
                        	usuario.nome_usuario, 
                        	usuario.email_usuario, cargo.nome AS cargo,  
                            CASE 
                            WHEN '".$permissor."' IN ('todos','".$permissor."') AND COUNT(*) = ".$this->qtdUnidades." AND email_recebe.cd_unidade IS NOT NULL AND GROUP_CONCAT(unidade.nome) IS NOT NULL
                            THEN 1
                            WHEN '".$permissor."' != 'todos' AND COUNT(*) != ".$this->qtdUnidades." AND email_recebe.cd_unidade IS NOT NULL AND GROUP_CONCAT(unidade.nome) IS NOT NULL
                            THEN 1 
                            ELSE NULL END AS recebe,   
                        	GROUP_CONCAT(unidade.nome SEPARATOR ',\n') AS locais_habilitados,
                            CASE 
                             WHEN COUNT(*) = ".$this->qtdUnidades."
                             THEN CONCAT('TODOS: ', GROUP_CONCAT(unidade.nome SEPARATOR ',\n'))
                             WHEN COUNT(*) = 0
                             THEN 'NENHUM'
                             WHEN COUNT(*) > 1 AND COUNT(*) < ".$this->qtdUnidades." AND email_recebe.cd_unidade IS NOT NULL AND GROUP_CONCAT(unidade.nome) IS NOT NULL
                             THEN CONCAT('SOMENTE ALGUNS: ', GROUP_CONCAT(unidade.nome SEPARATOR ',\n'))
                             WHEN COUNT(*) = 1 AND GROUP_CONCAT(unidade.nome) IS NOT NULL
                             THEN CONCAT('HABILITADO PARA: ',GROUP_CONCAT(unidade.nome SEPARATOR ',\n'))
                            WHEN GROUP_CONCAT(unidade.nome) IS NULL
                             THEN 'NADA HABILITADO'
                             ELSE CONCAT('SOMENTE: ',,GROUP_CONCAT(unidade.nome SEPARATOR ',\n')) END AS habilitados");
        if($nome_email != ''){
            $this->db->where("usuario.nome_usuario LIKE '%".strtoupper($nome_email)."%' OR usuario.email_usuario LIKE '%".strtoupper($nome_email)."%'");
            $this->db->limit(30);
        }
                            
        if($departamento != 'todos' and $departamento != 0){                  
            $this->db->where('usuario.cd_departamento', $departamento);
        }
        
        if($unidade != 'todos' and $unidade != 0){                  
            $this->db->where('usuario.cd_unidade', $unidade);
        }
        
        if($funcao != 'todos' and $funcao != 0){                  
            $this->db->where('usuario.cd_cargo', $funcao);
        }
        
        if($tipoEmail != '' and $nome_email == '' and $departamento == '' and $unidade == '' and $funcao == ''){
            $this->db->where('(email_recebe.idEmailEnvia = '.$tipoEmail.')');
        }
        
        $this->db->where('usuario.status_usuario', 'A');
        #$this->db->where('config_usuario.status_config_usuario', 'A');
        $this->db->where('usuario.tipo_usuario', 'USER');
        
        if($permissor != 'todos' and $nome_email == '' and $departamento == '' and $unidade == '' and $funcao == ''){
            $this->db->where('email_recebe.cd_unidade', $permissor);
        }

        $this->db->join('sistema.email_recebe', 'email_recebe.cd_usuario = usuario.cd_usuario AND email_recebe.idEmailEnvia = '.$tipoEmail, 'left');
        
        if($permissor == 'todos'){
            $this->db->join('adminti.unidade', "unidade.cd_unidade = email_recebe.cd_unidade", 'left');
        }else{
            $this->db->join('adminti.unidade', "unidade.cd_unidade = email_recebe.cd_unidade AND email_recebe.cd_unidade = ".$permissor, 'left');
        }
        $this->db->join('adminti.cargo', 'cargo.cd_cargo = usuario.cd_cargo', 'left');
        
        $this->db->group_by("usuario.cd_usuario, usuario.matricula_usuario, usuario.nome_usuario"); 
        if($permissor == 'todos' and $nome_email == '' and $departamento == '' and $unidade == '' and $funcao == ''){
            $this->db->having('COUNT(*) = '.$this->qtdUnidades);
        }else{
            $this->db->having('COUNT(*) != 0'); 
        }
        #$this->db->order_by('nome_usuario', 'asc');
        #$this->db->get('adminti.usuario')->result();
        #echo '<pre>'; print_r($this->db->last_query()); exit();
        
		if($tipoEmail and $permissor){
            return $this->db->get('adminti.usuario')->result();
        }else{
            return false;
        }
        
    }
    
    public function gravaQuemRecebe(){
        
        $unidades = false;

        $this->db->trans_begin();
        
        #Define Permissor(es)
        if($this->input->post('permissor')){
            if($this->input->post('permissor') == 'todos'){
                $unidades = $this->unidades(); # Array
            }else{
                $unidades = $this->input->post('permissor'); # Id
            }
        }
        
        $this->apagaTodosEncontrados($unidades);
        $this->gravaMarcados($unidades);
        $this->atualizaPreenchimentoEmail();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return true;
        }
        
    }
    
    public function unidades(){
        
        $this->db->where('status', 'A');
		return $this->db->get('adminti.unidade')->result_array();
        
    }
    
    public function apagaTodosEncontrados($unidades = false){
        
        if($this->input->post('todosUsuarios')){
            $usuarios = implode(',',$this->input->post('todosUsuarios'));

            if(is_array($unidades)){
                $unidades = implode(',',array_column($unidades, 'cd_unidade'));
            }
            $sql = "DELETE FROM sistema.email_recebe WHERE idEmailEnvia = ".$this->input->post('tipo_email')." AND cd_usuario IN(".$usuarios.") AND cd_unidade IN(".$unidades.")";
            $this->db->query($sql);
        }
        
    }
    
    public function gravaMarcados($unidades = false){
        
        if($this->input->post('todosUsuarios')){
            foreach($this->input->post('todosUsuarios') as $usu){
                if(in_array($usu, $this->input->post('marcados'))){
                    if($unidades){
                        if(is_array($unidades)){
                            foreach($unidades as $uni){
                                $sql = "INSERT INTO sistema.email_recebe(idEmailEnvia, cd_usuario, cd_unidade) VALUES(".$this->input->post('tipo_email').",".$usu.", ".$uni['cd_unidade'].")";
                                $this->db->query($sql);
                            }
                        }else{
                            $sql = "INSERT INTO sistema.email_recebe(idEmailEnvia, cd_usuario, cd_unidade) VALUES(".$this->input->post('tipo_email').",".$usu.", ".$unidades.")";
                            $this->db->query($sql);
                        }
                    }
                }
            }
        }
        
    }
    
    public function existeHabilitacao($tipoEmail, $usuario){
        
        $this->db->where('idEmailEnvia', $tipoEmail);
        $this->db->where('cd_usuario', $usuario);
        $this->db->from('sistema.email_recebe');
        return $this->db->count_all_results();
        
    }
    
    public function atualizaPreenchimentoEmail(){
        
         if($this->input->post('email')){
            
            foreach($this->input->post('email') as $usuario => $email){
                
                if(trim($email)){
                    $sql = "UPDATE adminti.usuario SET email_usuario = '".strtoupper($email).$this->dominioEmail."' WHERE cd_usuario = ".$usuario;
                    $this->db->query($sql);
                }
                
            }
            
         }
                
    }
    
    public function usuarioEnviaEmail($emailEnvia, $unidade){
        
        $sql = "SELECT 
                		DISTINCT
                		nome_usuario, 
                		email_usuario
                FROM adminti.usuario
                INNER JOIN sistema.email_recebe ON email_recebe.cd_usuario = usuario.cd_usuario
                WHERE 
                	status_usuario = 'A'
                	AND email_usuario IS NOT NULL
               		AND email_recebe.cd_unidade = ".$unidade."
               		AND email_recebe.idEmailEnvia = ".$emailEnvia."
                	";
                    
        return $this->db->query($sql)->result();
        
    }

}