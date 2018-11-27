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
class Email_grupo_model extends CI_Model{
    
    const tabelaDb = 'sistema.email_grupo';
    const tabela = 'email_grupo';
    
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
    
    public function getQtdUnidades(){
        
        $this->db->where('status', 'A');
        return $this->db->count_all_results('adminti.unidade');
        
    }
    
    public function gruposEmail($tipoEmail, $permissor, $nome_grupo){
        
        $this->db->select("
                            email_grupo.id,
                            email_grupo.email,
                            CASE WHEN email_grupo_recebe.cd_unidade IS NOT NULL THEN 1 ELSE NULL END AS recebe,
                            CASE 
                             WHEN COUNT(*) = ".$this->qtdUnidades."
                             THEN CONCAT('TODOS: ', GROUP_CONCAT(unidade.nome SEPARATOR ',\n'))
                             WHEN COUNT(*) = 0
                             THEN 'NENHUM'
                             WHEN COUNT(*) > 1 AND COUNT(*) < ".$this->qtdUnidades." AND email_grupo_recebe.cd_unidade IS NOT NULL AND GROUP_CONCAT(unidade.nome) IS NOT NULL
                             THEN CONCAT('SOMENTE ALGUNS: ', GROUP_CONCAT(unidade.nome SEPARATOR ',\n'))
                             WHEN COUNT(*) = 1 AND GROUP_CONCAT(unidade.nome) IS NOT NULL
                             THEN CONCAT('HABILITADO PARA: ',GROUP_CONCAT(unidade.nome SEPARATOR ',\n'))
                            WHEN GROUP_CONCAT(unidade.nome) IS NULL
                             THEN 'NADA HABILITADO'
                             ELSE CONCAT('SOMENTE: ',GROUP_CONCAT(unidade.nome SEPARATOR ',\n')) END AS habilitados
                        ");
        
        if($tipoEmail != '' and $nome_grupo == ''){
            $this->db->where('(email_grupo_recebe.idEmailEnvia = '.$tipoEmail.')');
        }
        
        if($permissor != 'todos' and $nome_grupo == ''){
            $this->db->where('email_grupo_recebe.cd_unidade', $permissor);
        }
        
        if($nome_grupo != ''){
            $this->db->where("email_grupo.email LIKE '%".strtoupper($nome_grupo)."%'");
            $this->db->limit(30);
        }
        
        $this->db->group_by("email_grupo.id,
                            email_grupo.email"); 
        
        $this->db->join('sistema.email_grupo_recebe', 'email_grupo_recebe.idEmailGrupo = email_grupo.id AND email_grupo_recebe.idEmailEnvia = '.$tipoEmail, 'left');
        
        if($permissor == 'todos'){
            $this->db->join('adminti.unidade', "unidade.cd_unidade = email_grupo_recebe.cd_unidade", 'left');
        }else{
            $this->db->join('adminti.unidade', "unidade.cd_unidade = email_grupo_recebe.cd_unidade AND email_grupo_recebe.cd_unidade = ".$permissor, 'left');
        }
        
        if($permissor == 'todos' and $nome_grupo == ''){
            $this->db->having('COUNT(*) = '.$this->qtdUnidades);
        }else{
            $this->db->having('COUNT(*) != 0'); 
        }
        
        #$this->db->get('sistema.email_grupo')->result();
        #echo '<pre>'; print_r($this->db->last_query()); exit();
        $this->db->order_by('email', 'asc');
        return $this->db->get('sistema.email_grupo')->result();
        
    }
    
    public function gravaGrupoQueRecebe(){
        
        $unidades = false;
        
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
        
    }
    
    public function unidades(){
        
        $this->db->where('status', 'A');
		return $this->db->get('adminti.unidade')->result_array();
        
    }
    
    public function apagaTodosEncontrados($unidades = false){
        
        if($this->input->post('todosGrupos')){
            $grupos = implode(',',$this->input->post('todosGrupos'));

            if(is_array($unidades)){
                $unidades = implode(',',array_column($unidades, 'cd_unidade'));
            }
            $sql = "DELETE FROM sistema.email_grupo_recebe WHERE idEmailEnvia = ".$this->input->post('tipo_email')." AND idEmailGrupo IN(".$grupos.") AND cd_unidade IN(".$unidades.")";
            $this->db->query($sql);
        }
        
    }
    
    public function gravaMarcados($unidades = false){
        
        if($this->input->post('todosGrupos')){
            foreach($this->input->post('todosGrupos') as $da){
                if(in_array($da, $this->input->post('marcados_grupos'))){
                    if($unidades){
                        if(is_array($unidades)){
                            foreach($unidades as $uni){
                                $sql = "INSERT INTO sistema.email_grupo_recebe(idEmailEnvia, idEmailGrupo, cd_unidade) VALUES(".$this->input->post('tipo_email').",".$da.", ".$uni['cd_unidade'].")";
                                $this->db->query($sql);
                            }
                        }else{
                            $sql = "INSERT INTO sistema.email_grupo_recebe(idEmailEnvia, idEmailGrupo, cd_unidade) VALUES(".$this->input->post('tipo_email').",".$da.", ".$unidades.")";
                            $this->db->query($sql);
                        }
                    }
                }
            }
        }
        
    }
    
    /**
     * 
     * lista os dados existentes de acordo com os parâmetros informados
     * @param $parametros Condições para filtro
     * @param $mostra_por_pagina Página corrente da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $pagina Página da paginação
     * 
     * @return A lista das dados
     */
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        
        $this->db->select("
                        	id,
                        	email,
                        	CASE WHEN status = 'A' THEN 'Ativo' Else 'Inativo' END AS status
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
                
            }
        }  
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(self::tabelaDb, $mostra_por_pagina, $pagina)->result();          
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('id', 'email', 'status');

        return $dados;
    }
    
    /**
     * 
     * Quantidade de linhas da consulta
     * @param $parametros Condições para filtro
     * 
     * @return A quantidade de linhas
     */
    public function qtdLinhas($parametros = null){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        return $this->db->get(self::tabelaDb)->num_rows(); 
        
    }
    
    public function usuarioEnviaEmail($emailEnvia, $unidade){
        
        $sql = "SELECT 
                		DISTINCT
                        login_usuario,
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