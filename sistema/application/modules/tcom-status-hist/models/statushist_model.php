<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do m�dulo de interface
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class statusHist_model extends CI_Model{
	
    const tabela = 'tcom_status_hist';
    private $perDefRecebeEmail = false;
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function setPerDefRecebeEmail($perValor){
        
        $this->perDefRecebeEmail = $perValor;
        
    }
    
    /**
     * 
     * lista os dados existentes de acordo com os par�metros informados
     * @param $parametros Condi��es para filtro
     * @param $mostra_por_pagina P�gina corrente da pagina��o
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de orde��o do campo (Crescente ou decrescente)
     * @param $pagina P�gina da pagina��o
     * 
     * @return A lista das dados
     */
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        
        $this->db->select("
                        	id,
                        	nome,
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
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['campos'] = array('id', 'nome', 'status');

        return $dados;
    }
    
    /**
     * 
     * Quantidade de linhas da consulta
     * @param $parametros Condi��es para filtro
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
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    /**
    * Viabilidade_model::insere()
    * 
    * Função que realiza a inserção dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
    public function insere(){
        
        $this->db->trans_begin();
        
        $nome = ucfirst($this->util->removeAcentos($this->input->post('nome')));
        
    
        $sql = "INSERT INTO email_envia (nome,cd_permissao)\n VALUES('Telecom - Status Histórico Viabilidade - ".$nome."',".$this->perDefRecebeEmail.");";
        $this->db->query($sql);
        $id = $this->db->insert_id();
        
        $campo = array();
        $valor = array();
        
        $campo[] = "idEmailEnvia";
        $valor[] = $id;

            foreach($_POST as $c => $v){

                if($c <> 'id'){

                    $valorFormatado = $this->util->removeAcentos($this->input->post($c));
                    $valorFormatado = $this->util->formaValorBanco(ucfirst($valorFormatado));

                    $campo[] = $c;
                    $valor[] = $valorFormatado;

                }

            }

            $campos = implode(', ', $campo);
            $valores = implode(', ', $valor);

    
    
    
    $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_status_hist (".$campos.")\n VALUES(".$valores.");";
    $this->db->query($sql);
    $id = $this->db->insert_id();
    
    

    if ($this->db->trans_status() === FALSE)
    {
        $this->db->trans_rollback();
        return false;
    }
    else
    {
        $this->db->trans_commit();

        return $id;
    }

    }
    
    public function recuperaIdEmailEnvia($id_tcom_stratus_hist){
        
        $this->db->where('id',$id_tcom_stratus_hist);
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->row()->idEmailEnvia;
        
    }
    
	
    /**
    * Viabilidade_model::atualiza()
    * 
    * Função que realiza a atualização dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
    public function atualiza(){
        
        $this->db->trans_begin();
        $idEmailEnvia = $this->recuperaIdEmailEnvia($this->input->post('id'));
        
        
        $postNome = ucfirst($this->util->removeAcentos($this->input->post('nome')));
        $nome = "Telecom - Status Histórico Viabilidade - ".$postNome;
        $sql = "UPDATE email_envia SET nome='".$nome."' where id=".$idEmailEnvia;
        $this->db->query($sql);
        
            foreach($_POST as $c => $v){

                    if($c != 'id'){
                            $valorFormatado = $this->util->removeAcentos($this->input->post($c));
                            $valorFormatado = $this->util->formaValorBanco(ucfirst($valorFormatado));

                            $campoValor[] = $c.' = '.$valorFormatado;

                    }
            }

            $camposValores = implode(', ', $campoValor);

    
            
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_status_hist SET ".$camposValores." WHERE id = ".$this->input->post('id').";";
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

    public function delete(){
        
        $this->db->trans_begin();
            
            
        
            $idEmailEnvia = $this->recuperaIdEmailEnvia($this->input->post('apg_id'));
            $sql = "DELETE FROM sistema.email_envia WHERE id = ".$idEmailEnvia;
            $this->db->query($sql);


            $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_status_hist WHERE id = ".$this->input->post('apg_id');
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

}