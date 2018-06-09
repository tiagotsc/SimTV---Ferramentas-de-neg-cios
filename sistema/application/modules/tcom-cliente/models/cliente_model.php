<?php

/**
 * Cliente_model
 * 
 * Classe que realiza o tratamento do módulo de cliente
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Cliente_model extends CI_Model{
	
    const tabela = 'tcom_cliente';
    
	/**
	 * Node_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function clientes(){
        
        $this->db->select("
                        tcom_cliente.id,
                        tcom_cliente.titulo,
                        tcom_cliente.email,
                        tcom_cliente.cnpj,
                        tcom_cliente.razaoSocial,
                        tcom_cliente.inscEstadual,
                        tcom_cliente.inscMunicipal,
                        tcom_cliente.observacao,
                        tcom_cliente.data_cadastro,
                        tcom_cliente_end.idCliente,
                        tcom_cliente_end.cep,
                        tcom_cliente_end.endereco,
                        tcom_cliente_end.numero,
                        tcom_cliente_end.bairro,
                        tcom_cliente_end.cd_estado,
                        tcom_cliente_end.complemento
                        ");
        
        $this->db->order_by('titulo', 'asc');
        $this->db->join(BANCO_TELECOM.'.tcom_cliente_end', 'tcom_cliente_end.idCliente = tcom_cliente.id');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->result();
    }
    
    public function telefones($id = false){
        if(!$id){
            return false;
        }
        $this->db->where('idCliente', $id);
        return $this->db->get(BANCO_TELECOM.'.tcom_cliente_telefone')->result();
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
                        	tcom_cliente.id,
                        	titulo,
                            email
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    if(in_array($res[0], array('nome'))){
                        $this->db->like(self::tabela.'.titulo', $res[1]);
                    }elseif(in_array($res[0], array('endereco'))){
                        $this->db->like('tcom_cliente_end.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        } 
        $this->db->join(BANCO_TELECOM.'.tcom_cliente_end', 'idCliente = tcom_cliente.id');
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['camposLabel'] = array('titulo'=>'Título','email'=>'E-mail');
        $dados['campos'] = array('id', 'titulo', 'email');

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
                    if(in_array($res[0], array('nome'))){
                        $this->db->like(self::tabela.'.titulo', $res[1]);
                    }elseif(in_array($res[0], array('endereco'))){
                        $this->db->like('tcom_cliente_end.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        }
        $this->db->join(BANCO_TELECOM.'.tcom_cliente_end', 'idCliente = tcom_cliente.id');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    public function insere(){
        
        $existe = $this->verificaCliente('titulo');
        
        if($existe){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning"><strong>O cliente informada j&aacute; foi cadastrada no sistema!</strong></div>');
            redirect(base_url('tcomCliente/cliente/ficha'));
            exit();
        }
        
        $this->db->trans_begin();
        
        $id = $this->insereDados();
        $this->insereDadosEndereco($id);
        $this->salvaTelefone($id);
        
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
    
    public function insereDados(){
        
        $posts = array('titulo', 'cnpj', 'email', 'razaoSocial', 'inscEstadual', 'inscMunicipal', 'idClienteTipo', 'observacao');
        
        $campo = array();
		$valor = array();

		foreach($posts as $p){

			if($p == 'observacao'){
                
    			$valorFormatado = $this->util->formaValorBanco(trim( addslashes ( $this->input->post($p) ) ));
                
            }else{
            
    			$valorFormatado = trim($this->util->removeAcentos( addslashes ( $this->input->post($p) ) ));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
            }
			
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_cliente (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        return $this->db->insert_id();
        
    }
    
    public function insereDadosEndereco($id){
        
        $posts = array('cep', 'cd_estado', 'endereco', 'numero', 'bairro', 'cidade', 'complemento');
        
        $campo[] = 'idCliente';
		$valor[] = $id;

		foreach($posts as $p){
            
			$valorFormatado = trim($this->util->removeAcentos( addslashes ( $this->input->post($p) ) ));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_cliente_end (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);  
        
        return $this->db->affected_rows();              
        
    }
    
    public function atualiza(){
        
        $existe = $this->verificaCliente('titulo');
        
        if($existe){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning"><strong>O cliente informada j&aacute; foi cadastrada no sistema!</strong></div>');
            redirect(base_url('tcomCliente/cliente/ficha'));
            exit();
        }
        
        $this->db->trans_begin();
        
        $this->atualizaDados();
        $this->atualizaDadosEndereco();
        $this->salvaTelefone($this->input->post('id'));
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return $this->input->post('id');
        }
        
    }
    
    public function atualizaDados(){
        
        $posts = array('titulo', 'cnpj', 'email', 'razaoSocial', 'inscEstadual', 'inscMunicipal', 'idClienteTipo', 'observacao');
        
        foreach($posts as $p){
            
			if($p == 'observacao'){
                
    			$valorFormatado = $this->util->formaValorBanco(trim( addslashes ( $this->input->post($p) ) ));
                
            }else{
            
    			$valorFormatado = trim($this->util->removeAcentos( addslashes ( $this->input->post($p) ) ));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
            
            }
		
			$campoValor[] = $p.' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_cliente SET ".$camposValores." WHERE id = ".$this->input->post('id').";";
		$this->db->query($sql);
        
        return $this->db->affected_rows();
        
    }
    
    public function atualizaDadosEndereco(){
        
        $posts = array('cep', 'cd_estado', 'endereco', 'numero', 'bairro', 'cidade', 'complemento');
        
        foreach($posts as $p){
            
            if(isset($_POST[$p])){
            
    			$valorFormatado = trim($this->util->removeAcentos( addslashes ( $this->input->post($p) ) ));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    		
    			$campoValor[] = $p.' = '.$valorFormatado;
            
            }
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_cliente_end SET ".$camposValores." WHERE idCliente = ".$this->input->post('id').";";
		$this->db->query($sql);
        
        return $this->db->affected_rows();
        
    }
    
    public function salvaTelefone($id){
        
        $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_cliente_telefone WHERE idCliente = ".$id;
        $this->db->query($sql);
        
        if($this->input->post('telefone')){
            
            foreach($this->input->post('telefone') as $telefone){
                
                if(trim($telefone) != ''){
                
                    $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_cliente_telefone (idCliente, telefone, tipo) VALUES(".$id.", '".$telefone."', NULL)";
                    $this->db->query($sql);
                
                }
            
            }
            
        }
        
    }
    
    public function verificaCliente($titulo){
        
        $titulo = $this->util->removeAcentos($titulo);
        
        $this->db->where('titulo', $titulo);
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows();
        
        return false;
    }
    
    public function execAtivacaoMdEndereco(){
        
        #$this->db->trans_begin();
        
        $novoEnd = $this->dadosMdEndereco($this->input->post('idViab'));
        
        #$backupIdViabResp = $this->input->post('id');
        
        $campos = array_keys($novoEnd[0]);
            
            foreach($campos as $campo){            
                if(!in_array($campo, array('cnpj', 'id'))){
				    $_POST[$campo] = $novoEnd[0][$campo];
                } 
			}
            
        $_POST['id'] = $this->input->post('idCliente');
        
        $status = $this->atualizaDadosEndereco();
        
        $telefones = array_column($this->telMdEndereco($novoEnd[0]['id'], 'array'), 'telefone');
        #echo '<pre>'; print_r($telefones); exit();
        #echo '<pre>'; print_r((array)$this->telMdEndereco($novoEnd[0]['id'])); exit();
        #$telefones = array_column((array)$this->telMdEndereco($novoEnd[0]['id']), 'telefone');
        $_POST['telefone'] = $telefones;
       #echo '<pre>'; print_r($_POST['telefone']); exit();
        $this->salvaTelefone($this->input->post('idCliente'));
         
        #$_POST['id'] = $backupIdViabResp;
        
        /*if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return true;
        }*/
        
        
    }
    
    public function dadosEndereco($idCliente = false){
        
        if(!$idCliente){
            return false;
        }
        
        $this->db->where('idCliente', $idCliente);
        return $this->db->get(BANCO_TELECOM.'.tcom_cliente_end')->result_array();
        
    }
    
    public function telEndereco($idCliente = false, $tipoRetorno = 'objeto'){
        
        if(!$idCliente){
            return false;
        }
        
        $this->db->where('idCliente', $idCliente);
        if($tipoRetorno == 'objeto'){
            return $this->db->get(BANCO_TELECOM.'.tcom_cliente_telefone')->result();
        }else{
            return $this->db->get(BANCO_TELECOM.'.tcom_cliente_telefone')->result_array();
        }
        
    }
    
    public function dadosMdEndereco($idViab = false){
        
        if(!$idViab){
            return false;
        }
        
        $this->db->where('idViab', $idViab);
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_md_end')->result_array();
        
    }
    
    public function telMdEndereco($idViabMdEnd = false, $tipoRetorno = 'objeto'){
        
        if(!$idViabMdEnd){
            return false;
        }
        
        $this->db->where('idViabMdEnd', $idViabMdEnd);
        if($tipoRetorno == 'objeto'){
            return $this->db->get(BANCO_TELECOM.'.tcom_viab_md_end_tel')->result();
        }else{
            return $this->db->get(BANCO_TELECOM.'.tcom_viab_md_end_tel')->result_array();
        }
        
    }
    
    public function atualizaPontaB($idContrato, $tipo){
        
        if($tipo == 5){ # MUDANÇA DE ENDEREÇO
            $clienteEnd = $this->cliente->dadosMdEndereco($this->input->post('idViab'));
            $clienteEndTel = $this->cliente->telMdEndereco($clienteEnd[0]['id'], 'array');
            $this->execAtivacaoMdEndereco();
        }else{
            $clienteEnd = $this->cliente->dadosEndereco($this->input->post('idCliente'));
            $clienteEndTel = $this->cliente->telEndereco($this->input->post('idCliente'), 'array');
        }
        
        $telefones = implode(' / ', array_column($clienteEndTel, 'telefone'));
        $campoValor[] = 'idCliente = '.$this->input->post('idCliente');
        
        foreach($clienteEnd[0] as $campo => $valor){
            if(!in_array($campo, array('id','idViab','data_cadastro'))){
                $valorFormatado = strtoupper($this->util->formaValorBanco(addslashes($valor)));
                $campoValor[] = $campo.' = '.$valorFormatado;
            }
        }
        
        if($telefones){
            $campoValor[] = "telefones = '".$telefones."'";
        }
        
        $camposValores = implode(', ', $campoValor);
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato_circuito SET ".$camposValores." WHERE idContrato = ".$this->input->post('idContrato').";";;
        $this->db->query($sql);
        
    }
    
    public function clientesTipo(){
        
        $this->db->where('status', 'A');
        return $this->db->get(BANCO_TELECOM.'.tcom_cliente_tipo')->result();
        
    }

}