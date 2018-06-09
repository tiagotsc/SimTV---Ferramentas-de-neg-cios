<?php

/**
 * Operadora_model
 * 
 * Classe que realiza o tratamento do módulo de operadoras
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Operadora_model extends CI_Model{
	
    const tabela = 'tcom_oper';
    const assunto = 'operadora';
    
	/**
	 * Node_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function telefonesCobranca($idCobranca = false){
        if(!$idCobranca){
            return false;
        }
        $this->db->where('idOperCobr', $idCobranca);
        return $this->db->get(BANCO_TELECOM.'.tcom_oper_cobr_telefone')->result();
    }
    
    public function pais($id = false){
        if(!$id){
            $this->db->where('id !=', $id);
        }
        $this->db->where('pai', '0');
        $this->db->where('status', 'A');
        $this->db->order_by('titulo', 'asc');
        return $this->db->get(BANCO_TELECOM.'.tcom_oper')->result();
    }
    
    public function operadoras($tipo = false){
        if(strtoupper($tipo) == 'PAI'){
            $this->db->where('pai', '0');
        }
        if(strtoupper($tipo) == 'FILHO'){
            $this->db->where('pai !=', '0');
        }
        
        $this->db->where('tcom_oper.status', 'A');
        
        $this->db->select('tcom_oper.id,
                            tcom_oper.titulo,
                            tcom_oper.cd_unidade,
                            tcom_oper.email,
                            tcom_oper.razaoSocial,
                            tcom_oper.inscEstadual,
                            tcom_oper.inscMunicipal,
                            tcom_oper.pai,
                            tcom_oper.cobInst,
                            tcom_oper.observacao,
                            tcom_oper.data_cadastro,
                            tcom_oper_inst.idOper,
                            tcom_oper_inst.cnpj,
                            tcom_oper_inst.cep,
                            tcom_oper_inst.endereco,
                            tcom_oper_inst.numero,
                            tcom_oper_inst.bairro,
                            tcom_oper_inst.cd_estado,
                            tcom_oper_inst.complemento');
        $this->db->order_by('titulo', 'asc');
        $this->db->join(BANCO_TELECOM.'.tcom_oper_inst', 'tcom_oper_inst.idOper = tcom_oper.id');
        return $this->db->get(BANCO_TELECOM.'.tcom_oper')->result();
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
                            CASE WHEN pai = '0' THEN 'Pai' ELSE 'Filho' END AS tipo,
                        	titulo,
                            nome,
                            CASE WHEN tcom_oper.status = 'A' THEN 'Ativo' ELSE 'Inativo' END AS status,
                            email,
                            (SELECT COUNT(*) FROM ".BANCO_TELECOM.".tcom_contrato WHERE idOper = tcom_oper.id AND status = 'A') AS contador
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
                    }elseif(in_array($res[0], array('nome'))){
                        $this->db->like(self::tabela.'.titulo', $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        } 
        
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_oper.cd_unidade', 'left');
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['camposLabel'] = array('tipo'=>'Tipo','titulo'=>'Título','nome'=>'Unidade','email'=>'E-mail','contador'=>'Contratos ativos');
        $dados['campos'] = array('id', 'tipo', 'titulo', 'nome', 'email', 'status', 'contador');

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
                    }elseif(in_array($res[0], array('nome'))){
                        $this->db->like(self::tabela.'.titulo', $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    public function insere(){
        
        $existe = $this->verificaOperadora('titulo');
        
        if($existe){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning"><strong>A operadora informada j&aacute; foi cadastrada no sistema!</strong></div>');
            redirect(base_url('tcomOperadora/operadora/ficha'));
            exit();
        }
        
        $this->db->trans_begin();
        
        $id = $this->insereDados();
        $this->insereDadosInstalacao($id);
        $idCob = $this->insereDadosCobranca($id);
        $this->salvaTelefoneCobranca($id);
        
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
        
        $posts = array('titulo', 'cd_unidade', 'email', 'razaoSocial', 'inscEstadual', 'inscMunicipal', 'cobInst', 'status', 'observacao', 'pai');
        
        $campo = array();
		$valor = array();

		foreach($posts as $p){

			if($p == 'observacao'){
                
    			$valorFormatado = $this->util->formaValorBanco(trim($this->input->post($p)));
                
            }elseif($p == 'titulo'){
                
                $valorFormatado = trim($this->util->removeAcentos( str_replace(" ", "", str_replace(" - ", "-", $this->input->post($p))) ));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
                
            }else{
            
    			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
            }
			
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_oper (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        return $this->db->insert_id();
        
    }
    
    public function insereDadosInstalacao($id){
        
        $posts = array('cnpj', 'cep', 'cd_estado', 'endereco', 'numero', 'bairro', 'cidade', 'complemento');
        
        $campo[] = 'idOper';
		$valor[] = $id;

		foreach($posts as $p){
            
			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_oper_inst (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);                
        
    }
    
    public function insereDadosCobranca($id){
        
        $posts = array('cnpj_cob', 'cep_cob', 'cd_estado_cob', 'endereco_cob', 'numero_cob', 'bairro_cob', 'cidade_cob', 'complemento_cob', 'hub_cob', 'headend_cob');
        
        $campo[] = 'idOper';
		$valor[] = $id;

		foreach($posts as $p){
            
            $valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
            $valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
            
			$campo[] = str_replace("_cob", "", $p);
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_oper_cobr (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql); 
        return $this->db->insert_id();
        
    }
    
    public function atualiza(){
        
        $existe = $this->verificaOperadora('titulo');
        
        if($existe){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning"><strong>A operadora informada j&aacute; foi cadastrada no sistema!</strong></div>');
            redirect(base_url('tcomOperadora/operadora/ficha'));
            exit();
        }
        
        $this->db->trans_begin();
        
        $this->atualizaDados();
        $this->atualizaDadosInstalacao();
        $this->atualizaDadosCobranca();
        $this->salvaTelefoneCobranca($this->input->post('id'));
        
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
        
        $posts = array('titulo', 'cd_unidade', 'email', 'razaoSocial', 'inscEstadual', 'inscMunicipal', 'cobInst', 'status', 'observacao', 'pai');
        
        foreach($posts as $p){
            
			if($p == 'observacao'){
                
    			$valorFormatado = $this->util->formaValorBanco(trim($this->input->post($p)));
                
            }elseif($p == 'titulo'){
                
                $valorFormatado = trim($this->util->removeAcentos( str_replace(" ", "", str_replace(" - ", "-", $this->input->post($p))) ));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
                
            }else{
            
    			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
            
            }
		
			$campoValor[] = $p.' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_oper SET ".$camposValores." WHERE id = ".$this->input->post('id').";";
		$this->db->query($sql);
        
    }
    
    public function atualizaDadosInstalacao(){
        
        $posts = array('cnpj', 'cep', 'cd_estado', 'endereco', 'numero', 'bairro', 'cidade', 'complemento');
        
        foreach($posts as $p){

			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
		
			$campoValor[] = $p.' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_oper_inst SET ".$camposValores." WHERE idOper = ".$this->input->post('id').";";
		$this->db->query($sql);
        
    }
    
    public function atualizaDadosCobranca(){
        
        $posts = array('cnpj_cob', 'cep_cob', 'cd_estado_cob', 'endereco_cob', 'numero_cob', 'bairro_cob', 'cidade_cob', 'complemento_cob', 'hub_cob', 'headend_cob');
        
        foreach($posts as $p){
            
 			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
 			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
		
			$campoValor[] = str_replace("_cob", "", $p).' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_oper_cobr SET ".$camposValores." WHERE idOper = ".$this->input->post('id').";";
        $this->db->query($sql);
        
    }
    
    public function salvaTelefoneCobranca($id){
        
        $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_oper_cobr_telefone WHERE idOper = ".$id;
        $this->db->query($sql);
        
        if($this->input->post('telefone')){
            
            foreach($this->input->post('telefone') as $telefone){
                
                if(trim($telefone) != ''){
                
                    $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_oper_cobr_telefone (idOper, idOperCobr, telefone, tipo) VALUES(".$id.", ".$idOperCobr.", '".$telefone."', NULL)";
                    $this->db->query($sql);
                
                }
            
            }
            
        }
        
    }
    
    public function verificaOperadora($titulo){
        
        $titulo = $this->util->removeAcentos($titulo);
        
        $this->db->where('titulo', $titulo);
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows();
        
        return false;
    }

}