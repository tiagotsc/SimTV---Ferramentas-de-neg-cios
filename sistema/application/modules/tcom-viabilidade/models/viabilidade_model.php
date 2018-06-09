<?php

/**
 * Viabilidade_model
 * 
 * Classe que realiza o tratamento do módulo de viabilidade
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Viabilidade_model extends CI_Model{
	
    const tabela = 'tcom_viab';
    
	/**
	 * Node_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function proximoControle($mes){
        
        $this->db->like('controle', $mes, 'after'); 
        $qtd = $this->db->count_all_results(BANCO_TELECOM.'.tcom_viab');
        return $qtd + 1;
        
    }
    
    public function dadosContrato($id){
        
        $this->db->where('tcom_contrato.id', $id);
        
        $this->db->select("tcom_contrato.*, tcom_circuito.idInterface, tcom_circuito.idTaxaDigital");
        
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = tcom_contrato.idCircuito'); 
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato')->row();
        
    }
    
    public function tiposViabilidade(){
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc');
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_tipo')->result();
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
                        	tcom_viab.id,
                        	controle,
                            n_solicitacao,
                            dt_prazo,
                            dt_solicitacao,
                            unidade.nome AS nomeunidade,
                            tcom_viab_tipo.nome AS tiposolic,
                            (
                                SELECT 
                                    COUNT(*) 
                                FROM ".BANCO_TELECOM.".tcom_viab_pend 
                                WHERE 
                                status = 'Pendente'
                                AND idViab = tcom_viab.id
                            ) AS pendencia,
                            CASE WHEN vistoriado = 'N' AND dt_prazo < CURDATE()
                            	THEN CONCAT(DATEDIFF(CURDATE(),dt_prazo),' dia de atraso')
                            ELSE '' END AS dias_atrazo,
                            CASE WHEN vistoriado = 'N' AND dt_prazo < CURDATE()
                            	THEN CONCAT(DATEDIFF(CURDATE(),dt_prazo),' dia de atraso')
                            ELSE '' END AS dias_atrazo,
                            CASE 
                            	WHEN tcom_viab.vistoriado = 'S'
                            		THEN 'OK'
                            	WHEN (tcom_viab.vistoriado = 'N' AND dt_prazo >= CURDATE()) OR (tcom_viab.vistoriado = 'N' AND dt_prazo IS NULL)
                            		THEN 'PENDENTE'
                           	ELSE 'ATRASADO' END AS situacao_atual
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    if(in_array($res[0], array('cd_unidade','status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('endereco'))){
                        $this->db->like('tcom_cliente_end.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        } 
        
        if(!$_POST){        
                
            if(!isset($_POST['vistoriado']) or trim($_POST['vistoriado']) == ''){
                if(!$pagina){
                    $this->db->where(self::tabela.'.vistoriado', 'N');
                }
            }
        
        }                
        
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_viab.cd_unidade', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_cliente', 'tcom_viab.idCliente = tcom_cliente.id', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_cliente_end', 'tcom_cliente_end.idCliente = tcom_cliente.id', 'left'); 
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros, $pagina);
        $dados['camposLabel'] = array('dt_prazo' => 'Prazo', 'n_solicitacao' => 'Nº soliticação', 'dt_solicitacao' => 'Dt. Solic.', 'nomeunidade' => 'Permissor', 'tiposolic' => 'Tipo');
        $dados['campos'] = array('id', 'controle', 'n_solicitacao', 'dt_solicitacao', 'dt_prazo', 'nomeunidade', 'tiposolic');

        return $dados;
    }
    
    /**
     * 
     * Quantidade de linhas da consulta
     * @param $parametros Condições para filtro
     * 
     * @return A quantidade de linhas
     */
    public function qtdLinhas($parametros = null, $pagina = false){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('cd_unidade','status'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('endereco'))){
                        $this->db->like('tcom_cliente_end.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        if(!$_POST){
        
            if(!isset($_POST['vistoriado']) or trim($_POST['vistoriado']) == ''){
                if(!$pagina){
                    $this->db->where(self::tabela.'.vistoriado', 'N');
                }
            }
        
        }
        
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_viab.cd_unidade', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_cliente', 'tcom_viab.idCliente = tcom_cliente.id', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_cliente_end', 'tcom_cliente_end.idCliente = tcom_cliente.id', 'left'); 
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    public function insere(){
        
        $this->db->trans_begin();
        
        if($this->input->post('idCliente') == 'NOVO'){
            $_POST['idCliente'] = $this->insereNovoCliente();
        }
        
        $id = $this->insereDados();
        // Mudança de endereço
        if($this->input->post('idViabTipo') == 5){
            $this->insereDadosMdEndereco($id);
        }
        
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
    
    public function insereNovoCliente(){
        
        #$this->load->model('tcom_cliente/cliente_model','cliente');
        $obs_armazena = $this->input->post('observacao');
        $_POST['observacao'] = '';
        $id = $this->cliente->insereDados();
        
        $this->cliente->insereDadosEndereco($id);
        $this->cliente->salvaTelefone($id);

        $_POST['observacao'] = $obs_armazena;
        
        return $id;
        
    }
    
    public function insereDados(){
        
        $posts = array('controle', 'dt_solicitacao', 'dt_prazo', 'n_solicitacao', 'idViabTipo', 'idContrato', 
                        'cd_unidade', 'idOper', 'idCliente', 'redundancia', 'qtd_circuitos', 'idInterface','idTaxaDigital', 'observacao');
        
        $campo = array();
		$valor = array();

		foreach($posts as $p){

			if($p == 'observacao'){
                
    			$valorFormatado = $this->util->formaValorBanco(trim($this->input->post($p)));
                
            }else{
            
    			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
            }
			
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_viab (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        return $this->db->insert_id();
        
    }
    
    public function insereDadosMdEndereco($id){
        
        $posts = array('cep', 'cd_estado', 'endereco', 'numero', 'bairro', 'complemento');
        
        $campo[] = 'idViab';
		$valor[] = $id;
        
		foreach($posts as $p){
            
			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_viab_md_end (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);  
        $id = $this->db->insert_id();
        
        $this->salvaTelefoneMdEnd($id);              
        
    }
    
    public function atualiza(){
        
        $this->db->trans_begin();
        
        if($this->input->post('idCliente') == 'NOVO'){
            $_POST['idCliente'] = $this->insereNovoCliente();
        }
        
        $this->atualizaDados();
        // Mudança de endereço
        if($this->input->post('idViabTipo') == 5){ 
            if($this->input->post('idMdEnd')){ 
                $this->atualizaDadosMdEndereco($this->input->post('id'));
            }else{
                $this->insereDadosMdEndereco($this->input->post('id'));
            }
            
        }        
        
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
        
        $posts = array('controle', 'dt_solicitacao', 'dt_prazo', 'n_solicitacao', 'idViabTipo', 'idContrato', 
                        'cd_unidade', 'idOper', 'idCliente', 'redundancia', 'qtd_circuitos', 'idInterface','idTaxaDigital', 'observacao');
        
        foreach($posts as $p){
            
			if($p == 'observacao'){
                
    			$valorFormatado = $this->util->formaValorBanco(trim($this->input->post($p)));
                
            }else{
            
    			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
            
            }
		
			$campoValor[] = $p.' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_viab SET ".$camposValores." WHERE id = ".$this->input->post('id').";";
		$this->db->query($sql);
        
    }
    
    public function atualizaDadosMdEndereco(){

        $posts = array('cep', 'cd_estado', 'endereco', 'numero', 'bairro', 'complemento');
        
        foreach($posts as $p){

			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
		
			$campoValor[] = $p.' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_viab_md_end SET ".$camposValores." WHERE idViab = ".$this->input->post('id').";";
		$this->db->query($sql);
        
        $this->salvaTelefoneMdEnd($this->input->post('idMdEnd'));
        
    }
    
    public function salvaTelefoneMdEnd($id){
        
        $sql = "Delete FROM ".BANCO_TELECOM.".tcom_viab_md_end_tel WHERE idViabMdEnd = ".$id;
        $this->db->query($sql);
        
        if($this->input->post('telefone')){
            
            foreach($this->input->post('telefone') as $telefone){
                
                if(trim($telefone) != ''){
                
                    $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_viab_md_end_tel (idViabMdEnd, telefone, tipo) VALUES(".$id.", '".$telefone."', NULL)";
                    $this->db->query($sql);
                
                }
            
            }
            
        }
        
    }
    
    public function telefonesMdEndereco($idMdEnd = false){
        if(!$idMdEnd){
            return false;
        }
        $this->db->where('idViabMdEnd', $idMdEnd);
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_md_end_tel')->result();
    }
    
    public function dadosViabilidade($id){
        
        # Tabela Viabilidade
        $sql = "SELECT 
                    tviab.id,
                	tviab.controle,
                    tcirc.designacao,
                    tviab.idContrato,
                    tviab_tipo.id AS id_tipo,
                	tviab_tipo.nome AS tipo,
                    tcont.idCircuito AS idCircuito,
                    tviab.cd_unidade,
                    tunidade.permissor AS permissor_cod,
                	tunidade.nome AS permissor,
                	tviab.n_solicitacao,
                	tviab.dt_solicitacao,
                	tviab.dt_prazo,
                    tint.id AS idInterface,
                	tint.nome AS interface,
                    tt_digital.id AS idTaxaDigital,
                	CONCAT(tt_digital.velocidade,' ', tt_digital.tipo) AS velocidade,
                	tviab.qtd_circuitos,
                    tviab.idOper AS operadora,
                    tviab.idCliente AS cliente,
                	tviab.vistoriado,
                    tviab.observacao
                FROM ".BANCO_TELECOM.".tcom_viab AS tviab
                LEFT JOIN ".BANCO_TELECOM.".tcom_viab_tipo AS tviab_tipo ON tviab.idViabTipo = tviab_tipo.id
                LEFT JOIN adminti.unidade AS tunidade ON tunidade.cd_unidade = tviab.cd_unidade
                LEFT JOIN ".BANCO_TELECOM.".tcom_interface AS tint ON tint.id = tviab.idInterface
                LEFT JOIN ".BANCO_TELECOM.".tcom_taxa_digital AS tt_digital ON tt_digital.id = tviab.idTaxaDigital
                LEFT JOIN ".BANCO_TELECOM.".tcom_contrato AS tcont ON tcont.id = tviab.idContrato
                LEFT JOIN ".BANCO_TELECOM.".tcom_circuito AS tcirc ON tcirc.id = tcont.idCircuito
                WHERE tviab.id = ".$id;
        $dados['viabilidade'] = $this->db->query($sql)->row();
        
        # Tabela Operadora
        $this->db->where('id', $dados['viabilidade']->operadora);
        $dados['operadora'] = $this->db->get(BANCO_TELECOM.'.tcom_oper')->row();
        
        # Tabela Operadora Instalação
        $this->db->where('idOper', $dados['viabilidade']->operadora);
        $this->db->join('adminti.estado', 'estado.cd_estado = tcom_oper_inst.cd_estado', 'left'); 
        $dados['operadoraInst'] = $this->db->get(BANCO_TELECOM.'.tcom_oper_inst')->row();
        
        # Tabela Operadora Cobrança
        $this->db->where('idOper', $dados['viabilidade']->operadora);
        $this->db->join('adminti.estado', 'estado.cd_estado = tcom_oper_cobr.cd_estado', 'left'); 
        $dados['operadoraCob'] = $this->db->get(BANCO_TELECOM.'.tcom_oper_cobr')->row();
        
        # Tabela Operadora Cobrança Telefones
        $this->db->where('idOperCobr', $dados['operadoraCob']->id);
        $dados['operadoraCobTel'] = $this->db->get(BANCO_TELECOM.'.tcom_oper_cobr_telefone')->result();
        
        # Tabela Cliente
        $this->db->where('id', $dados['viabilidade']->cliente);
        $dados['cliente'] = $this->db->get(BANCO_TELECOM.'.tcom_cliente')->row();
        
        # Tabela Cliente endereço
        $this->db->where('idCliente', $dados['viabilidade']->cliente);
        $this->db->join('adminti.estado', 'estado.cd_estado = tcom_cliente_end.cd_estado', 'left');   
        $dados['clienteEnd'] = $this->db->get(BANCO_TELECOM.'.tcom_cliente_end')->row();
        
        # Tabela Cliente telefone
        $this->db->where('idCliente', $dados['viabilidade']->cliente);
        $dados['clienteEndTel'] = $this->db->get(BANCO_TELECOM.'.tcom_cliente_telefone')->result();
        
        // Mudança de endereço
        if($dados['viabilidade']->id_tipo == 5){
        
        # Tabela Mudança de endereço
        $this->db->where('idViab', $dados['viabilidade']->id);
        $this->db->join('adminti.estado', 'estado.cd_estado = tcom_viab_md_end.cd_estado', 'left');   
        $dados['mdEnd'] = $this->db->get(BANCO_TELECOM.'.tcom_viab_md_end')->row();
        
        # Tabela Mudança de endereço telefone
        $this->db->where('idViabMdEnd', $dados['mdEnd']->id);
        $dados['mdEndTel'] = $this->db->get(BANCO_TELECOM.'.tcom_viab_md_end_tel')->result();
        
        }
        
        return $dados;     
    }
    
    public function operadorasUnidade($cdUnidade){
        
        if($cdUnidade != 'N'){
            $this->db->where('cd_unidade', $cdUnidade);
        }
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
        $this->db->where('pai != 0');
        $this->db->where('tcom_oper.status', 'A');
        $this->db->order_by('titulo', 'asc');
        $this->db->join(BANCO_TELECOM.'.tcom_oper_inst', 'tcom_oper_inst.idOper = tcom_oper.id');
        return $this->db->get(BANCO_TELECOM.'.tcom_oper')->result();
        
    }

}