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
class imposto_model extends CI_Model{
	
    const tabela = 'tcom_imposto';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
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
                        	tcom_imposto.id,
                        	tcom_imposto.nome,
                            tcom_servico.nome AS servico,
                            CONCAT(estado1.sigla_estado,' / ',estado2.sigla_estado) AS uf,
                            efetiva,
                            base_calculo,
                            reducao,
                        	CASE WHEN tcom_imposto.status = 'A' THEN 'Ativo' Else 'Inativo' END AS status
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('status','idServico'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
                
            }
        }  
        
        $this->db->join(BANCO_TELECOM.'.tcom_servico', 'tcom_servico.id = idServico'); 
        $this->db->join('adminti.estado AS estado1', 'estado1.cd_estado = tcom_imposto.cd_estado_origem'); 
        $this->db->join('adminti.estado AS estado2', 'estado2.cd_estado = tcom_imposto.cd_estado'); 
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['camposLabel'] = array('efetiva' => 'Efet.%', 'uf' => 'UF orig./dest.', 'base_calculo' => 'Base cal.%', 'reducao' => 'Red%');
        $dados['campos'] = array('id', 'nome', 'servico','uf', 'efetiva', 'base_calculo','reducao', 'status');

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
                    if(in_array($res[0], array('status','idServico'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        $this->db->join(BANCO_TELECOM.'.tcom_servico', 'tcom_servico.id = idServico');
        $this->db->join('adminti.estado AS estado1', 'estado1.cd_estado = tcom_imposto.cd_estado_origem'); 
        $this->db->join('adminti.estado AS estado2', 'estado2.cd_estado = tcom_imposto.cd_estado'); 
        
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

            foreach($_POST as $c => $v){

                if($c <> 'id'){

                    $valorFormatado = $this->util->removeAcentos($this->input->post($c));
                    $valorFormatado = $this->util->formaValorBanco(ucfirst($valorFormatado));

                    $campo[] = $c;
                    $valor[] = $valorFormatado;

                }

            }

            $campos = implode(', ', $campo);
            $valores = str_replace('%','',implode(', ', $valor));

    
    
    
    $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_imposto (".$campos.")\n VALUES(".$valores.");";
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
    
	
    /**
    * Viabilidade_model::atualiza()
    * 
    * Função que realiza a atualização dos dados da operadora na base de dados
    * @return O número de linhas afetadas pela operação
    */
    public function atualiza(){
        
        $this->db->trans_begin();
        
            foreach($_POST as $c => $v){

                    if($c != 'id'){
                            $valorFormatado = $this->util->removeAcentos($this->input->post($c));
                            $valorFormatado = $this->util->formaValorBanco(ucfirst($valorFormatado));

                            $campoValor[] = $c.' = '.$valorFormatado;

                    }
            }

            $camposValores = str_replace('%','',implode(', ', $campoValor));

    
            
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_imposto SET ".$camposValores." WHERE id = ".$this->input->post('id').";";
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
    
    public function servicos($status = false){
        
        /*$this->db->select("tcom_imposto.*");
        $this->db->where("data_inicio >= '".date('Y-m-d')."'");
        $this->db->where("data_fim >= '".date('Y-m-d')."'");
        $this->db->join(BANCO_TELECOM.'.tcom_imposto', 'idServico = tcom_servico.id');*/
        if($status){
            $this->db->where("status",$status);
        }
        $this->db->order_by('nome', 'asc');
        return $this->db->get(BANCO_TELECOM.'.tcom_servico')->result(); 
        
    }
    
    public function servicosPesquisa($idServico = false){
        
        $servicoCondicao = ($idServico)? 'OR tcom_servico.id='.$idServico: '';
        
        $sql = "SELECT 
            	DISTINCT tcom_servico.*
            FROM telecom.tcom_servico
            JOIN telecom.tcom_imposto ON tcom_imposto.idServico = tcom_servico.id ".$servicoCondicao."
            ORDER BY nome asc";   
        return $this->db->query($sql)->result();     
        
    }
    
    public function estados(){
        
        $this->db->select('estado.cd_estado, nome_estado, cd_unidade');
        $this->db->join('adminti.estado_cidade', 'estado.cd_estado = estado_cidade.cd_estado');
        $this->db->order_by('nome_estado', 'asc');
        return $this->db->get('adminti.estado')->result(); 
        
    }

    public function delete(){
        
        $this->db->trans_begin();

            $sql = "DELETE FROM ".BANCO_TELECOM.".".self::tabela." WHERE id = ".$this->input->post('apg_id');
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