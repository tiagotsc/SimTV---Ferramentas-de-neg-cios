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
class RhFerias_model extends CI_Model{
	
    
	/**
	 * Email_model::__construct()
	 * 
	 * @return
	 */
    function __construct(){
        
        parent::__construct();
        
    }
    
    public function salvar(){
        
        $inicio = $this->util->formaValorBanco($this->input->post('inicio'));
        $fim = $this->util->formaValorBanco($this->input->post('fim'));
        
        if($this->existeFerias($this->input->post('fer_cd_usuario'))){
            $sql = "UPDATE adminti.ferias SET inicio = ".$inicio.", fim = ".$fim." WHERE cd_usuario = ".$this->input->post('fer_cd_usuario');
        }else{
            $sql = "INSERT INTO adminti.ferias(inicio, fim, cd_usuario) VALUES(".$inicio.",".$fim.", ".$this->input->post('fer_cd_usuario').")";
        }
        
        $this->db->trans_begin();
        
        $this->db->query($sql);
        
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
    
    public function existeFerias($usuario){
        
        $this->db->where('cd_usuario', $usuario);
        $this->db->from('adminti.ferias');
        return $this->db->count_all_results();
        
    }

    public function dados($cd_usuario){
        
        $sql = "SELECT 
                    DATE_FORMAT(inicio, '%d/%m/%Y') AS inicio, 
                    DATE_FORMAT(fim, '%d/%m/%Y') AS fim, 
                    cd_usuario 
                    FROM adminti.ferias 
                    WHERE cd_usuario = ".$cd_usuario;
        
        return $this->db->query($sql)->row(); 
        
    }
    
    public function feriasEntra(){
        
        $sql = "SELECT
                	matricula_usuario, 
                	nome_usuario
                FROM adminti.usuario
                INNER JOIN adminti.ferias ON ferias.cd_usuario = usuario.cd_usuario
                WHERE inicio = CURDATE()";
        return $this->db->query($sql)->result(); 
        
    }
    
    public function feriasVolta(){
        
        $sql = "SELECT
                	matricula_usuario, 
                	nome_usuario
                FROM adminti.usuario
                INNER JOIN adminti.ferias ON ferias.cd_usuario = usuario.cd_usuario
                WHERE fim = CURDATE()";
        return $this->db->query($sql)->result(); 
        
    }
    
    public function usuarioEnviaEmail($emailEnvia){
        
        $sql = "SELECT 
                		DISTINCT
                		nome_usuario, 
                		email_usuario
                FROM adminti.usuario
                INNER JOIN sistema.email_recebe ON email_recebe.cd_usuario = usuario.cd_usuario
                WHERE 
                	status_usuario = 'A'
                	AND email_usuario IS NOT NULL
               		AND email_recebe.idEmailEnvia = ".$emailEnvia."
                	";
                    
        return $this->db->query($sql)->result();
        
    }
    
    public function deleteFerias($id){
        
        $sql = "DELETE FROM adminti.ferias WHERE id = ".$id;
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    
    // ------------ Novas funcoes de ferias ------------
    
    public function retornaFerias($cd = NULL, $status = NULL){
        
        if($status == 'A'){
            $condicao = array('cd_usuario' => $cd, 'status' => 'A');

            $this->db->select('id, inicio, fim, cd_usuario, tipo');
            $this->db->where($condicao);
            return $this->db->get('adminti.ferias')->row_array();
        }else{
            $condicao = array('cd_usuario' => $cd, 'status' => 'I');

            $this->db->select('id, inicio, fim, cd_usuario, tipo');
            $this->db->where($condicao);
            return $this->db->get('adminti.ferias')->result_array();
            
        }
    }
        
    public function salvaFerias() {
        
        $data = array(
                        'inicio' => $this->util->formataData($this->input->post('inicio'),'USA'),
                        'fim' => $this->util->formataData($this->input->post('fim'),'USA'),
                        'tipo'=>$this->input->post('tipo'),
                        'cd_usuario'=>$this->input->post('cd_usuario')
        );
        
        $this->db->trans_begin();
        
            if($this->input->post('id_ferias')){

                array_pop($data);

                $this->db->where('id',$this->input->post('id_ferias'));
                $this->db->update('adminti.ferias',$data);
            }else{

                $this->db->insert('adminti.ferias', $data);
            }

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

    public function desativaFerias(){
        
        $this->db->select('id, fim');
        $this->db->where('status','A');
        $temp = $this->db->get('adminti.ferias')->result_array();
        
        $alter = array('status' => 'I');
                     
        foreach($temp as $t){
            if(date('Y-m-d')>=$t['fim']){
                $this->db->where('id',$t['id']);
                $this->db->update('adminti.ferias', $alter);
            }
        }
    }
        
    
    

}