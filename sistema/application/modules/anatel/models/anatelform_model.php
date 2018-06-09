<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do usuário
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class AnatelForm_model extends CI_Model{
	
	/**
	 * Anatel_form_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function config(){
        
        return $this->db->get('anatel_config')->result();
        
    }
    
    /**
     * Anatel_form_model::tipos_frm()
     * 
     * Pega as tipos de formulários da Anatel
     * 
     * @return As tipos existentes
     */
    public function tipos_frm(){

        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc');
        return $this->db->get('anatel_tipo_frm')->result();
        
    }
    
    /**
     * Anatel_form_model::insereForm()
     * 
     * Cadastra o formulário
     * 
     * @return Retorna o cd do formulário cadastrado
     */
    public function insereForm(){
        
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'perguntas' and $c <> 'cd_anatel_frm' and $c <> 'tipo_resposta' and $c <> 'resp-obrig' and $c <> 'sigla'){
            
    			$valorFormatado = ucfirst($this->util->formaValorBanco($this->input->post($c)));
    			
    			$campo[] = $c;
    			$valor[] = $valorFormatado;
            
            }
            
		}
        
        $campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
        $this->db->trans_begin();
        
		$sql = "INSERT INTO anatel_frm (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        if($cd){
            
            if($this->input->post('perguntas')){
        
                foreach($this->input->post('perguntas') as $pos => $perg){
                    
                    $sigla = $this->input->post('sigla')[$pos];
                    $tipoResp = $this->input->post('tipo_resposta')[$pos];
                    $obrigatorio = $this->input->post('resp-obrig')[$pos];
                    
                    if(trim($perg) <> ''){
                    
                        $sql = "INSERT INTO anatel_quest (sigla, questao, tipo_resp, obrigatorio, cd_anatel_frm) VALUES ('".$sigla."' ,'".ucfirst($perg)."', '".$tipoResp."', '".$obrigatorio."',".$cd.");";
                        $this->db->query($sql);
                    
                    }
                    
                }
            
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
            
            return $cd;
        }
        
    }
    
    /**
     * Anatel_form_model::insereForm()
     * 
     * Atualiza o formulário
     * 
     * @return Retorna bool
     */
    public function atualizaForm(){
        
        foreach($_POST as $c => $v){
			
			if($c <> 'perguntas' and $c <> 'perguntas-up' and $c <> 'cd_frm_anatel' and $c <> 'tipo_resposta' and $c <> 'tipo-resposta-up' and $c <> 'resp-obrig' and $c <> 'resp-obrig-up' and $c <> 'sigla' and $c <> 'sigla-up'){
				$valorFormatado = $this->util->formaValorBanco($this->input->post($c));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE anatel_frm SET ".$camposValores." WHERE cd_anatel_frm = ".$this->input->post('cd_anatel_frm').";";
		$this->db->query($sql);
        
        if($this->input->post('perguntas-up')){
            
            foreach($this->input->post('perguntas-up') as $cd_perg => $valor){
                $sigla = $this->input->post('sigla-up')[$cd_perg];
                $tipoResp = $this->input->post('tipo-resposta-up')[$cd_perg];
                $obrigatorio = $this->input->post('resp-obrig-up')[$cd_perg];
                $sql = "UPDATE anatel_quest SET sigla = '".$sigla."', questao = '".ucfirst($valor)."', tipo_resp = '".$tipoResp."', obrigatorio = '".$obrigatorio."' WHERE cd_anatel_quest = ".$cd_perg;
                $this->db->query($sql);
                
            }
            
        }
        
        if($this->input->post('perguntas')){
        
            foreach($this->input->post('perguntas') as $pos => $perg){
                $sigla = $this->input->post('sigla')[$pos];
                $tipoResp = $this->input->post('tipo_resposta')[$pos];
                $obrigatorio = $this->input->post('resp-obrig')[$pos];
                if(trim($perg) <> ''){
                
                    $sql = "INSERT INTO anatel_quest (sigla, questao, tipo_resp, obrigatorio, cd_anatel_frm) VALUES ('".$sigla."', '".ucfirst($perg)."', '".$tipoResp."', '".$obrigatorio."',".$this->input->post('cd_anatel_frm').");";
                    $this->db->query($sql);
                
                }
                
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
            
            return true;
        }
        
    }
    
    /**
    * Anatel_form_model::dadosFormulario()
    * 
    * Função que monta um array com todos os dados formulário
    * @param $cd Cd do formulário para recuperação de dados
    * @return Retorna todos os dados do formulário
    */
	public function dadosFormulario($cd){
	   
		$this->db->where('cd_anatel_frm', $cd);
		$usuario = $this->db->get('anatel_frm')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $usuario[0];
	}
	
    /**
    * Anatel_form_model::camposFormulario()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela formulário
    * @return Os campos da tabela formulário
    */
	public function camposFormulario($cd){
		
        $this->db->where('cd_anatel_frm', $cd);
		$campos = $this->db->get('anatel_frm')->list_fields();
		
		return $campos;
		
	}
    
    /**
    * Anatel_form_model::perguntas()
    * 
    * Função que pega as perguntas do formulário
    * @return As perguntas
    */
    public function perguntas($cd){
        
        $this->db->where('cd_anatel_frm', $cd);
        return $this->db->get('anatel_quest')->result();
        
    }   
    
    /**
    * Anatel_form_model::insereRegraMeta()
    * 
    * Função cadastra a regra da meta
    * @return Bool
    */
    public function insereRegraMeta(){
        
        $this->db->trans_begin();
        
        if($this->input->post('tipo_regra') == 'P'){ // Regra meta porcetagem
            
            $sql = "INSERT INTO anatel_meta (cd_anatel_frm, regra, operador, comparador, numero)"; 
            $sql .= "VALUES(".$this->input->post('cd_anatel_frm').",'".$this->input->post('tipo_regra')."', '/', '".$this->input->post('comparador')."', ".$this->input->post('porc_meta').");";
            
            $this->db->query($sql);
            $cd = $this->db->insert_id();
            
            # Pergunta 1
            $sql = "INSERT INTO anatel_meta_campo (cd_anatel_meta, cd_anatel_quest, ordem_questao)"; 
            $sql .= "VALUES(".$cd.", ".$this->input->post('cd_pergunta1_porc').", 1);";
            $this->db->query($sql);
            
            # Pergunta 2
            $sql = "INSERT INTO anatel_meta_campo (cd_anatel_meta, cd_anatel_quest, ordem_questao)"; 
            $sql .= "VALUES(".$cd.", ".$this->input->post('cd_pergunta2_porc').", 2);";
            $this->db->query($sql);
            
        }
        
        if($this->input->post('tipo_regra') == 'N'){ // Regra meta numérica
            
            $sql = "INSERT INTO anatel_meta (cd_anatel_frm, regra, operador, comparador, numero)"; 
            $sql .= "VALUES(".$this->input->post('cd_anatel_frm').",'".$this->input->post('tipo_regra')."', '==', '=', ".$this->input->post('num_meta').");";
            
            $this->db->query($sql);
            $cd = $this->db->insert_id();
            
            # Pergunta 1
            $sql = "INSERT INTO anatel_meta_campo (cd_anatel_meta, cd_anatel_quest, ordem_questao)"; 
            $sql .= "VALUES(".$cd.", ".$this->input->post('cd_pergunta_num').", 1);";
            $this->db->query($sql);
            
        }
        
        if($this->input->post('tipo_regra') == 'F'){ // Regra meta fórmula (Equação)
            
            $sql = "INSERT INTO anatel_meta (cd_anatel_frm, regra, operador, comparador, numero)"; 
            $sql .= "VALUES(".$this->input->post('cd_anatel_frm').",'".$this->input->post('tipo_regra')."', '-', '".$this->input->post('comparador')."', ".$this->input->post('porc_meta').");";
            
            $this->db->query($sql);
            $cd = $this->db->insert_id();
            
            # Pergunta 1
            $sql = "INSERT INTO anatel_meta_campo (cd_anatel_meta, cd_anatel_quest, ordem_questao)"; 
            $sql .= "VALUES(".$cd.", ".$this->input->post('cd_pergunta1_form').", 1);";
            $this->db->query($sql);
            
            # Pergunta 2
            $sql = "INSERT INTO anatel_meta_campo (cd_anatel_meta, cd_anatel_quest, ordem_questao)"; 
            $sql .= "VALUES(".$cd.", ".$this->input->post('cd_pergunta2_form').", 2);";
            $this->db->query($sql);
            
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
    
    /**
     * Anatel_form_model::regrasMeta()
     * 
     * Pega as regras de determinado formulário
     * 
     * @param $cd Cd do formulário para recuperação das regras
     * 
     * @return Retorna as regras
     */
    public function regrasMeta($cd){
        
        $sql = "SELECT 
                    anatel_meta.cd_anatel_meta,
                	cd_anatel_meta_campo,
                	regra,
                	pri.cd_anatel_quest AS pergunta1,
                	(SELECT sec.cd_anatel_quest FROM anatel_meta_campo AS sec WHERE sec.cd_anatel_meta = pri.cd_anatel_meta AND sec.ordem_questao = 2) AS pergunta2,
                	operador,
                    comparador,
                	numero
                FROM anatel_meta
                INNER JOIN anatel_meta_campo AS pri ON anatel_meta.cd_anatel_meta = pri.cd_anatel_meta
                WHERE pri.ordem_questao = 1 and anatel_meta.cd_anatel_frm = ".$cd;
                
        return $this->db->query($sql)->result(); 
        
    }
    
    /**
     * Anatel_form_model::apagaQuestao()
     * 
     * Apaga a questão informada
     * 
     * @param $cd Cd da questão que será apagada
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function apagaQuestao(){
        
        $sql = "DELETE FROM anatel_quest WHERE cd_anatel_quest = ".$this->input->post('apg_questao');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    /**
     * Anatel_form_model::apagaRegra()
     * 
     * Apaga a regra informada
     * 
     * @param $cd Cd da regra que será apagada
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function apagaRegra(){
        
        $sql = "DELETE FROM anatel_meta WHERE cd_anatel_meta = ".$this->input->post('apg_regra_meta');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    /**
     * Anatel_form_model::psqFrms()
     * 
     * Lista os formulários existentes de acordo com os parâmetros informados
     * @param $tipoFrm de formulário que se deseja filtrar
     * @param $departamento que se deseja filtrar
     * @param $status que se deseja filtrar
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $mostra_por_pagina Página corrente da paginação
     * @param $pagina Página da paginação
     * 
     * @return A lista dos usuários
     */
    public function psqFrms($tipoFrm = null, $departamento = null, $status = null, $mostra_por_pagina = null, $sort_by = null, $sort_order = null, $pagina = null){
        
        // Verifica qual ordenação foi informada
        $sort_order = ($sort_order == 'desc') ? 'desc' : 'asc';
        // Campos da tabela que podem receber ordenação
		$sort_columns = array('anatel_frm.cd_anatel_tipo_frm', 'anatel_frm.cd_departamento', 'anatel_frm.status');
        // Verifica qual campo foi informado para ordenação
        $sort_by = (in_array($sort_by, $sort_columns)) ? $sort_by : 'anatel_frm.cd_anatel_tipo_frm';
                        
        $this->db->select("
                            anatel_frm.cd_anatel_frm,
                            anatel_indicador.sigla,
                            anatel_tipo_frm.nome,
                            nome_departamento,
                            CASE WHEN anatel_frm.status = 'A'
                            	THEN 'Ativo'
                            ELSE 'Inativo' END AS status
                            ");       
        
        
        if($tipoFrm != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "anatel_frm.cd_anatel_tipo_frm = ".$tipoFrm;
            $this->db->where($condicao);
        }
        
        if($departamento != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "anatel_frm.cd_departamento = ".$departamento;
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "anatel_frm.status = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->join('anatel_tipo_frm', 'anatel_tipo_frm.cd_anatel_tipo_frm = anatel_frm.cd_anatel_tipo_frm');  
        $this->db->join('anatel_indicador', 'anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador');     
        $this->db->join('departamento', 'departamento.cd_departamento = anatel_frm.cd_departamento');    
        $this->db->order_by($sort_by, $sort_order);  
        
        return $this->db->get('anatel_frm', $mostra_por_pagina, $pagina)->result();
        
    }
    
    /**
     * Anatel_form_model::psqQtdFrms()
     * 
     * Consulta a quantidade de usuários da pesquisa
     * 
     * @param $nome Nome do usuário para filtrar a consulta
     * 
     * @param $status Status do usuário para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function psqQtdFrms($tipoFrm = null, $departamento = null, $status = null){
        
        if($tipoFrm != '0'){
            $condicao = "cd_anatel_tipo_frm = ".$tipoFrm;
            $this->db->where($condicao);
        }
        
        if($departamento != '0'){
            $condicao = "cd_departamento = ".$departamento;
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        return $this->db->get('anatel_frm')->result();
    }
    
    /**
     * Anatel_form_model::deleteFormulario()
     * 
     * Apaga o formulário
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function deleteFormulario(){
        
        $sql = "DELETE FROM anatel_frm WHERE cd_anatel_frm = ".$this->input->post('apg_cd_frm_anatel');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    /**
     * Anatel_form_model::usuariosDisponiveis()
     * 
     * Pega os usuários disponíveis para serem adicionados no formulário
     * 
     * @return 
     */
    public function usuariosDisponiveis(){
                
        $sql = 'SELECT 
                	DISTINCT
                	usuario.cd_usuario,
                	nome_usuario
                FROM adminti.usuario 
                WHERE cd_departamento = '.$this->input->post('cd_departamento').'
                AND cd_unidade IS NOT NULL
                AND usuario.cd_usuario NOT IN(
                    SELECT DISTINCT cd_usuario FROM anatel_resp_indicador WHERE cd_anatel_frm = '.$this->input->post('cd_anatel_frm').'
                ) ORDER BY nome_usuario';
        
        return $this->db->query($sql)->result();  
        
    }
    
    /**
     * Anatel_form_model::verificaResponsavel()
     * 
     * Verifica se o usuário é responsável por responder formulário da Anatel
     * 
     * @return A count(True) se é responsável ou 0 (false) caso não seja responsável
     */
    public function verificaResponsavel(){
        
        $this->db->distinct();
        $this->db->where('cd_usuario', $this->session->userdata('cd'));
        $this->db->from('anatel_resp_indicador');
        return $this->db->count_all_results();
        
    }
    
    /**
     * Anatel_form_model::formsResponsavel()
     * 
     * Pega os formulários que o usuário é responsável
     * 
     * @param $unidade Unidade do responsável
     * 
     * @return Os formulários
     */
    public function formsResponsavel($unidade = null){
                
        $sql = "SELECT 
                	anatel_frm.cd_anatel_frm,
                		sigla,
                		anatel_indicador.nome AS nome_indicador,
                	anatel_tipo_frm.nome,
                    adminti.unidade.cd_unidade,
                	unidade.nome AS nome_unidade,
                		(
                		SELECT 
                			COUNT(*) 
                		FROM anatel_res 
                		WHERE anatel_res.cd_unidade = unidade.cd_unidade
                				AND anatel_res.cd_anatel_frm = anatel_frm.cd_anatel_frm
                		/*AND SUBSTR(anatel_res.data_cadastro, 1, 10) BETWEEN '".date('Y-m')."-05' AND '".date('Y-m')."-12'*/
                		/*AND DATE_FORMAT(anatel_res.data_cadastro,'%d/%m/%Y') BETWEEN '01/03/2015' AND '31/03/2015'*/
                        AND DATE_FORMAT(anatel_res.data_cadastro,'%d/%m/%Y') BETWEEN '".ANATEL_FRM_INICIO."' AND '".ANATEL_FRM_FIM."'
                	) AS verificador 
                FROM anatel_frm
                INNER JOIN anatel_tipo_frm ON anatel_tipo_frm.cd_anatel_tipo_frm = anatel_frm.cd_anatel_tipo_frm
                INNER JOIN anatel_resp_indicador ON anatel_resp_indicador.cd_anatel_frm = anatel_frm.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                INNER JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                WHERE 
                		anatel_resp_indicador.cd_usuario = ".$this->session->userdata('cd')." 
                		AND anatel_frm.status = 'A'";

        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::pegaDadosFrm()
     * 
     * Pega os dados do formulário (Tipo de formulário e perguntas)
     * 
     * @param $cd_frm Cd do formulário
     * @param $unidade Unidade do usuário
     * 
     * @return Os formulários
     */
    public function pegaDadosFrm($cd_frm, $unidade = null){
        
        $sql = "SELECT 
                	pri.cd_anatel_frm,
                    cd_anatel_xml,
                	anatel_tipo_frm.nome AS tipo_frm,
                		anatel_tipo_frm.descricao AS tipo_frm_descricao,
                	anatel_indicador.sigla AS sigla_indicador,
                	anatel_indicador.nome AS nome_indicador,
                	nome_departamento,
               		CASE WHEN adminti.unidade.nome IS NULL
                		THEN (SELECT nome FROM adminti.unidade WHERE cd_unidade = ".$unidade.")
                	ELSE adminti.unidade.nome END AS nome_unidade,
                	anatel_quest.cd_anatel_quest,
                	questao,
            		#(SELECT resposta FROM anatel_res WHERE anatel_res.cd_anatel_quest = anatel_quest.cd_anatel_quest) AS resposta
            		resposta,
                    tipo_resp,
                    obrigatorio,
                    grupo
                FROM anatel_frm AS pri
                INNER JOIN departamento ON departamento.cd_departamento = pri.cd_departamento
                INNER JOIN anatel_tipo_frm ON anatel_tipo_frm.cd_anatel_tipo_frm = pri.cd_anatel_tipo_frm
                INNER JOIN anatel_quest ON anatel_quest.cd_anatel_frm = pri.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = pri.cd_anatel_indicador
                LEFT JOIN anatel_res ON anatel_res.cd_anatel_quest = anatel_quest.cd_anatel_quest AND anatel_res.cd_unidade = ".$unidade." AND anatel_res.data_cadastro LIKE '".date('Y-m')."%'
                LEFT JOIN adminti.unidade ON anatel_res.cd_unidade = adminti.unidade.cd_unidade
                WHERE 
                pri.cd_anatel_frm = ".$cd_frm."
                AND pri.status = 'A'
                AND pri.cd_anatel_frm IN (
                	SELECT DISTINCT sec.cd_anatel_frm FROM anatel_resp_indicador AS sec WHERE sec.cd_usuario = ".$this->session->userdata('cd')." AND sec.cd_anatel_frm = pri.cd_anatel_frm
                )
                ORDER BY grupo, anatel_quest.cd_anatel_quest";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::pegaRegrasFrm()
     * 
     * Pega do fomulário
     * 
     * @param $cd_frm Cd do formulário
     * 
     * @return As regras
     */
    public function pegaRegrasFrm($cd_frm, $cd_unidade){
        
        $sql = "SELECT 
                    anatel_meta.cd_anatel_meta,
                	regra,
                	pri.cd_anatel_quest AS pergunta1,
                	CASE WHEN regra = 'P' or regra = 'F'
                		THEN (SELECT sec.cd_anatel_quest FROM anatel_meta_campo AS sec WHERE sec.cd_anatel_meta = anatel_meta.cd_anatel_meta AND sec.ordem_questao = 2)
                	ELSE '' END AS pergunta2,
                	operador,
                	comparador,
                	numero,
                	pri.ordem_questao,
                    cd_anatel_motivo_just,
                	diagnostico,
                	acao_corretiva,
                    ilustracao
                FROM anatel_meta
                INNER JOIN anatel_meta_campo AS pri ON pri.cd_anatel_meta = anatel_meta.cd_anatel_meta
                LEFT JOIN anatel_meta_res ON anatel_meta_res.cd_anatel_meta = anatel_meta.cd_anatel_meta AND anatel_meta_res.cd_unidade = ".$cd_unidade." AND anatel_meta_res.data_cadastro LIKE '".date('Y-m')."%'
                WHERE anatel_meta.cd_anatel_frm = ".$cd_frm."
                AND pri.ordem_questao = 1";
             
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::motivos_just()
     * 
     * Pega os motivos de justificativas
     * 
     * @return Os motivos
     */
    public function motivos_just(){
        
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc');
        return $this->db->get('anatel_motivo_just')->result();
        
    }
    
    /**
     * Anatel_form_model::gravaResposta()
     * 
     * Grava a resposta do formulário
     * 
     * @return bool
     */
    public function gravaResposta(){
        
        #echo '<pre>'; print_r($_POST); exit();
        if($this->input->post('cd_anatel_xml') == 1){ # Planos oferecidos
            $respExist = implode(',', array_keys($this->input->post('resp')[1]));
        }else{
            $respExist = implode(',', array_keys($this->input->post('resp')));
        }
        
        $this->db->trans_begin();
        
        $sql = "DELETE FROM anatel_res WHERE cd_unidade = ".$this->input->post('cd_unidade')." AND cd_anatel_quest IN (".$respExist.") AND data_cadastro LIKE '".date('Y-m')."%'";
        $this->db->query($sql);
        
        if($this->input->post('cd_anatel_xml') == 1){ # Planos oferecidos
            
            foreach($this->input->post('resp') as $grupo => $res){ 
                
                foreach($res as $cd_pergunta => $resposta){
                    
                    $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, cd_usuario, cd_unidade, grupo)";
                    $sql .= "VALUES(".$this->input->post('cd_anatel_frm').", ".$cd_pergunta.", '".$resposta."', ".$this->session->userdata('cd').", ".$this->input->post('cd_unidade').", ".$grupo.")";
                    $this->db->query($sql);
                    
                }
                
            }
            
        }else{
        
            foreach($this->input->post('resp') as $cd => $res){
                
                $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, cd_usuario, cd_unidade, grupo)";
                $sql .= "VALUES(".$this->input->post('cd_anatel_frm').", ".$cd.", '".$res."', ".$this->session->userdata('cd').", ".$this->input->post('cd_unidade').", 1)";
                $this->db->query($sql);
                
            }
        
        }
        
        $this->gravaJustificativa();
        
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
    
    /**
     * Anatel_form_model::gravaJustificativa()
     * 
     * Grava a justificativa do formulário
     * 
     * @return bool
     */
    public function gravaJustificativa(){
        
        $sql = "DELETE FROM anatel_meta_res WHERE cd_unidade = ".$this->input->post('cd_unidade')." AND cd_anatel_frm = ".$this->input->post('cd_anatel_frm')." AND data_cadastro LIKE '".date('Y-m')."%'";
        $this->db->query($sql);
        
        foreach($this->input->post('motivo') as $cd => $valor){
            #$sql = "UPDATE anatel_meta SET cd_anatel_motivo_just = ".$valor.", diagnostico = '".trim($this->input->post('diagnostico')[$cd])."', acao_corretiva = '".trim($this->input->post('acao_corretiva')[$cd])."' WHERE cd_anatel_meta = ".$cd;
            
            if($valor != ''){
            
                $sql = "INSERT INTO anatel_meta_res(cd_anatel_meta, cd_anatel_frm, cd_usuario, cd_unidade, cd_anatel_motivo_just, diagnostico, acao_corretiva, ilustracao, resultado) \n"; 
                $sql .= "VALUES(".$cd.", ".$this->input->post('cd_anatel_frm').", ".$this->session->userdata('cd').", ".$this->input->post('cd_unidade').", ".$valor.", '".addslashes(trim($this->input->post('diagnostico')[$cd]))."', '".addslashes(trim($this->input->post('acao_corretiva')[$cd]))."', '".trim($this->input->post('ilustracao')[$cd])."', '".str_replace(',','.',(float)$this->input->post('ilustracao')[$cd])."')";
                $this->db->query($sql);
            
            }
            
        }
        
    }
    
    /**
     * Anatel_form_model::anatelIndDepartamento()
     * 
     * Pega os indicadores da Anatel de acordo com o departamento informado
     * 
     * @return Os indicadores
     */
    public function anatelIndDepartamento($cd_departamento = null){
        
        $this->db->where('status', 'A');
        $this->db->where('cd_departamento', $cd_departamento);
        $this->db->order_by('nome', 'asc');
        return $this->db->get('anatel_indicador')->result();
        
    }
    
    /**
     * Anatel_form_model::anatelGrupoIndicador()
     * 
     * Pega os indicadores da Anatel de acordo com o departamento informado
     * 
     * @return Os indicadores
     */
    public function anatelGrupoIndicador($cd_sistema = null){
        
        $this->db->where('status', 'A');
        if($cd_sistema != null){
            $this->db->where('cd_anatel_tipo_frm', $cd_sistema);
        }
        $this->db->order_by('nome', 'asc');
        return $this->db->get('anatel_xml')->result();
        
    }
    
    /**
     * Anatel_form_model::tiposXml()
     * 
     * Pega os tipos XML existentes
     * 
     * @return Os tipos
     */
    public function tiposXml($rejeitar = false){
        
        if($rejeitar){# Oculta o registro "outros"
            $this->db->where_not_in('cd_anatel_xml', $rejeitar);
        }
        
        $this->db->where('status', 'A');
        $this->db->where('cd_anatel_tipo_frm', $this->input->post('tipo_frm'));
        $this->db->order_by('nome', 'asc');
        return $this->db->get('anatel_xml')->result();
        
    }
    
    /**
     * Anatel_form_model::formNaoRespondidos()
     * 
     * Lista os forms não respondidos
     * 
     * @return A lista
     */
    /*public function formNaoRespondidos(){
                
        $sql = "SELECT 
                	DISTINCT
                	anatel_tipo_frm.nome AS tipo_sistema,
                	sigla AS indicador,
                	nome_departamento,
                	adminti.unidade.nome AS unidade,
                	nome_usuario,
                	email_usuario
                FROM
                adminti.usuario
                INNER JOIN anatel_resp_indicador ON anatel_resp_indicador.cd_usuario = adminti.usuario.cd_usuario
                INNER JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                INNER JOIN anatel_tipo_frm ON anatel_tipo_frm.cd_anatel_tipo_frm = anatel_frm.cd_anatel_tipo_frm
                INNER JOIN departamento ON departamento.cd_departamento = anatel_frm.cd_departamento
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                WHERE CONCAT(anatel_frm.cd_anatel_frm,'|',adminti.unidade.cd_unidade) NOT IN(
                	SELECT DISTINCT CONCAT(cd_anatel_frm,'|',cd_unidade) FROM anatel_res WHERE DATE_FORMAT(anatel_res.data_cadastro,'%m/%Y') = '".$this->input->post('mes_ano')."'
                )";
                
        return $this->db->query($sql)->result();
        
    }*/
    
    /**
     * Anatel_form_model::departamento()
     * 
     * Lista os departamentos que estão associados a algum indicador
     * 
     * @return A lista
     */
    public function departamento(){
        
        $sql = "SELECT 
        *
        FROM adminti.departamento
        WHERE cd_departamento IN (
        SELECT DISTINCT cd_departamento FROM anatel_indicador
        )";
        
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::associaResponsavel()
     * 
     * Associa o usuário ao indicador para que ele possa responde-lo
     * 
     * @return bool
     */
    public function associaResponsavel(){
        
        $this->db->trans_begin();
        
        #$sql = "DELETE FROM anatel_resp_indicador WHERE cd_anatel_frm = ".$this->input->post('cd_anatel_frm');
        #$this->db->query($sql);
        foreach($this->input->post('user') as $user){

            foreach($this->input->post('cd_unidade') as $unidade){
                $sql = "INSERT INTO anatel_resp_indicador(cd_usuario, cd_unidade, cd_anatel_frm)";
                $sql .= " VALUES(".$user.", ".$unidade.", ".$this->input->post('cd_anatel_frm').");";
                $this->db->query($sql);
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
            
            return true;
        }
        
    }
    
    /**
     * Anatel_form_model::usuariosDepartamento()
     * 
     * Lista os usuários disponíveis, que ainda não foram associados, para associar ao indicador
     * 
     * @return Os usuários disponíveis
     */
    public function usuariosDepartamento(){
        
        /*if($this->input->post('cd_departamento') != ''){
            $this->db->where('cd_departamento', $this->input->post('cd_departamento'));
        }
        
        $this->db->select('usuario.cd_usuario, nome_usuario');
        $this->db->where('status_usuario', 'A');
        $this->db->where('status_config_usuario', 'A');
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario');
        $this->db->order_by('nome_usuario', 'asc'); 
        return $this->db->get('adminti.usuario')->result();*/
        
        $sql = "SELECT 
                    adminti.usuario.cd_usuario,
                    nome_usuario 
                FROM adminti.usuario 
                INNER JOIN sistema.config_usuario ON sistema.config_usuario.cd_usuario = adminti.usuario.cd_usuario
                WHERE 
                status_usuario = 'A'
                AND status_config_usuario = 'A'
                AND cd_departamento = ".$this->input->post('cd_departamento')."
                AND adminti.usuario.cd_usuario NOT IN (
                	SELECT cd_usuario FROM anatel_resp_indicador WHERE cd_anatel_frm = ".$this->input->post('cd_anatel_frm')."
                )";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::responsavelIndicador()
     * 
     * Lista os responsáveis pelo indicador
     * 
     * @return bool
     */
    public function responsavelIndicador($cd_anatel_frm){
    
        $sql = 'SELECT 
                	DISTINCT
                	adminti.usuario.cd_usuario,
                	nome_usuario
                FROM anatel_resp_indicador 
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = anatel_resp_indicador.cd_usuario
                WHERE cd_anatel_frm = '.$cd_anatel_frm.'
                ORDER BY nome_usuario';
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::responsavelUnidades()
     * 
     * Lista os responsáveis por indicador/unidade
     * 
     * @return bool
     */
    public function responsavelUnidades($cd_anatel_frm, $cd_usuario){
        
        $sql = 'SELECT 
                	DISTINCT
                	cd_usuario,
                	adminti.unidade.cd_unidade,
                	nome
                FROM anatel_resp_indicador 
                INNER JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                WHERE cd_anatel_frm = '.$cd_anatel_frm.' AND cd_usuario = '.$cd_usuario.'
                ORDER BY nome';
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::apgUniUser()
     * 
     * Apaga a unidade associada ao responsável
     * 
     * @return bool
     */
    public function apgUniUser($indicador, $unidade, $usuario){
        
        $sql = 'DELETE FROM anatel_resp_indicador WHERE cd_anatel_frm = '.$indicador.' AND cd_usuario = '.$usuario.' AND cd_unidade = '.$unidade;
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    public function salvaConfig(){
        
        $this->db->trans_begin();
                
        if($this->input->post('SATVA_INICIO')){
            
            $sql = "UPDATE anatel_config SET valor = '".$this->input->post('SATVA_INICIO')."' WHERE nome = 'SATVA_INICIO';";
            $this->db->query($sql);
            
        }
        
        if($this->input->post('SATVA_FIM')){
            
            $sql = "UPDATE anatel_config SET valor = '".$this->input->post('SATVA_FIM')."' WHERE nome = 'SATVA_FIM';";
            $this->db->query($sql);
            
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
    
    /**
     * Anatel_form_model::apagaResponsavel()
     * 
     * Apaga responsável
     * 
     * @return bool
     */
    public function apagaResponsavel(){
        
        $sql = 'DELETE FROM anatel_resp_indicador WHERE cd_anatel_frm = '.$this->input->post('cd_anatel_frm').' AND cd_usuario = '.$this->input->post('apg_assoc');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    /**
     * Anatel_form_model::menuIndicadores()
     * 
     * Lista os indicadores para montar menu
     * 
     * @return Os indicadores
     */
    public function menuIndicadores(){
        
        $sql = "SELECT 
                DISTINCT 
                anatel_indicador.cd_anatel_indicador AS pai_menu
                FROM anatel_resp_indicador 
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                WHERE anatel_frm.status = 'A' AND anatel_indicador.status = 'A' AND anatel_resp_indicador.cd_usuario = ".$this->session->userdata('cd');
        return $this->db->query($sql)->result_array();
        
    }
    
    /**
     * Anatel_form_model::menuIndicadoresUnidades()
     * 
     * Lista as unidades dos indicadores para montar o menu
     * 
     * @return As unidades
     */
    public function menuIndicadoresUnidades(){
        
        /*$sql = "SELECT
                cd_menu,
                CASE WHEN verificador > 0
                	THEN CONCAT(nome_menu,' <span class=\"glyphicon glyphicon-ok statusOk\" aria-hidden=\"true\"></span>')
                ELSE nome_menu END AS nome_menu,
                link_menu,
                pai_menu,
                verificador
                FROM (
                SELECT 
                DISTINCT 
                	anatel_indicador.cd_anatel_indicador AS cd_menu,
                	anatel_indicador.sigla AS nome_menu,
                	'' AS link_menu,
                	'0' AS pai_menu,
                	0 AS verificador
                FROM anatel_resp_indicador 
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                WHERE anatel_resp_indicador.cd_usuario = ".$this->session->userdata('cd')."
                UNION ALL
                SELECT 
                	cd_anatel_resp_indicador AS cd_menu,
                	unidade.nome AS nome_menu,
                	CONCAT('anatel/openForm/',anatel_resp_indicador.cd_anatel_frm,'/',anatel_resp_indicador.cd_unidade) AS link_menu,
                	anatel_indicador.cd_anatel_indicador AS pai_menu,
                	(
                		SELECT 
                			COUNT(*) 
                		FROM anatel_res 
                		WHERE anatel_res.cd_unidade = unidade.cd_unidade
                				AND anatel_res.cd_anatel_frm = anatel_frm.cd_anatel_frm
                				AND DATE_FORMAT(anatel_res.data_cadastro,'%d/%m/%Y') BETWEEN '".ANATEL_FRM_INICIO."' AND '".ANATEL_FRM_FIM."'
                	) AS verificador 
                FROM anatel_resp_indicador 
                INNER JOIN adminti.unidade ON unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                WHERE anatel_resp_indicador.cd_usuario = ".$this->session->userdata('cd')."
                ) AS RES";*/
                
        $sql = "SELECT
                cd_menu,
            	CASE WHEN (cont_pergunta = cont_resposta) AND cont_pergunta > 0
            		#THEN CONCAT(nome_menu,' <span class=\"glyphicon glyphicon-ok statusOk\" aria-hidden=\"true\"></span>')
            		THEN 
            			CASE 
            				WHEN resposta_meta > 0 # Caso justificativa esteja pendente
            					/*THEN CONCAT(nome_menu,' <span title=\"Respondido\" class=\"glyphicon glyphicon-ok statusOk\" aria-hidden=\"true\"></span><span title=\"Falta justificar\" class=\"glyphicon glyphicon-exclamation-sign statusAlerta\" aria-hidden=\"true\"></span>')*/
            			         THEN CONCAT(nome_menu,' </span><span title=\"Falta justificar\" class=\"glyphicon glyphicon-exclamation-sign statusAlerta\" aria-hidden=\"true\"></span>')
                        ELSE CONCAT(nome_menu,' <span class=\"glyphicon glyphicon-ok statusOk\" aria-hidden=\"true\"></span>') END
            	ELSE nome_menu END AS nome_menu,
                link_menu,
                pai_menu/*,
            	CASE 
            		WHEN resposta_meta > 0 # Caso justificativa esteja pendente
            			THEN ' <span class=\"glyphicon glyphicon-exclamation-sign statusPendente\" aria-hidden=\"true\"></span>'
            	ELSE '' END AS resposta_meta*/                                
                FROM (
                SELECT 
                DISTINCT 
                	anatel_indicador.cd_anatel_indicador AS cd_menu,
                	anatel_indicador.sigla AS nome_menu,
                	'' AS link_menu,
                	'0' AS pai_menu,
                	0 AS cont_pergunta,
                    0 AS cont_resposta,
                    '' AS resposta_meta 
                FROM anatel_resp_indicador 
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                WHERE anatel_frm.status = 'A' AND anatel_indicador.status = 'A' AND anatel_resp_indicador.cd_usuario = ".$this->session->userdata('cd')."
                UNION ALL
                SELECT 
                	cd_anatel_resp_indicador AS cd_menu,
                	unidade.nome AS nome_menu,
                	CONCAT('anatel/openForm/',anatel_resp_indicador.cd_anatel_frm,'/',anatel_resp_indicador.cd_unidade) AS link_menu,
                	anatel_indicador.cd_anatel_indicador AS pai_menu,
               		(
            			SELECT 
            					COUNT(*)
            				FROM anatel_res 
            				WHERE anatel_res.cd_unidade = unidade.cd_unidade
            						AND anatel_res.cd_anatel_frm = anatel_frm.cd_anatel_frm
            						AND SUBSTR(anatel_res.data_cadastro, 1, 7) = SUBSTR(CURDATE(), 1, 7)
            		) AS cont_pergunta,
                	(
                		SELECT 
                			COUNT(*) 
                		FROM anatel_res 
                		WHERE anatel_res.cd_unidade = unidade.cd_unidade
                				AND anatel_res.cd_anatel_frm = anatel_frm.cd_anatel_frm
                				AND resposta IS NOT NULL AND resposta <> ''
                				AND SUBSTR(anatel_res.data_cadastro, 1, 7) = SUBSTR(CURDATE(), 1, 7)
                	) AS cont_resposta,
            		(
            			SELECT 
            				COUNT(*) 
            			FROM anatel_meta_res AS mr
            			WHERE /*mr.ilustracao <> ''
            			AND mr.resultado <> ''
            			AND mr.cd_anatel_motivo_just = ''
            			AND mr.diagnostico = ''
            			AND mr.acao_corretiva = ''*/
                        cd_usuario IS NULL
            			AND SUBSTR(mr.data_cadastro, 1, 7) = SUBSTR(CURDATE(), 1, 7)
            			AND mr.cd_anatel_frm = anatel_frm.cd_anatel_frm
            			AND mr.cd_unidade = unidade.cd_unidade
            		) AS resposta_meta                    
                FROM anatel_resp_indicador 
                INNER JOIN adminti.unidade ON unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                WHERE anatel_frm.status = 'A' AND anatel_indicador.status = 'A' AND anatel_resp_indicador.cd_usuario = ".$this->session->userdata('cd')."
                ) AS RES";
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::dadosIndicadores()
     * 
     * Pega os dados dos indicadores para montar o xml
     * 
     * @return Os dados
     */
    public function dadosIndicadores(){
        
        $data = implode('-', array_reverse(explode('/', $this->input->post('mes_ano'))));
        
        # Pega os ceps e operadoras
        $sql = "SELECT
                	DISTINCT
                	id_cep_aps,
                    id_operadora
                FROM anatel_res
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_res.cd_anatel_frm
                LEFT JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_res.cd_unidade
                WHERE 
                	#anatel_res.cd_anatel_frm = 1 
                	#AND anatel_res.cd_unidade = 9
                    /*AND*/ id_operadora = ".$this->input->post('operadora')."
                    AND cd_anatel_xml = ".$this->input->post('tipo_xml')."
                	AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,7) = '".$data."'
                ORDER BY id_operadora, unidade.nome";
                
        $dados['ids'] = $this->db->query($sql)->result();
        
        switch($this->input->post('tipo_xml')){
            case 1: # Planos oferecidos
                #$campoResp = 'GROUP_CONCAT(resposta ORDER BY cd_anatel_res ASC) AS resposta';
                $campoResp = "GROUP_CONCAT(CASE WHEN resposta IS NULL THEN 'S/N' ELSE resposta END ORDER BY cd_anatel_res ASC) AS resposta";
                #$groupBy = 'GROUP BY id_servico, id_operadora, id_cep_aps, sigla, grupo';
                $groupBy = 'GROUP BY id_servico, id_operadora, id_cep_aps, sigla, grupo , nome_servico, nome_pacote, tecnologia, inicio_servico';
            break;
            case 3: # Banda larga
                #$campoResp = 'GROUP_CONCAT(resposta ORDER BY cd_anatel_res ASC) AS resposta';
                $campoResp = "GROUP_CONCAT(CASE WHEN resposta IS NULL THEN 'S/N' ELSE resposta END ORDER BY cd_anatel_res ASC) AS resposta";
                #$groupBy = 'GROUP BY id_servico, id_operadora, id_cep_aps, sigla';
                $groupBy = 'GROUP BY id_servico, id_operadora, id_cep_aps, sigla, grupo , nome_servico, nome_pacote, tecnologia, inicio_servico';
            break;
            default:
                $campoResp = 'resposta';
                $groupBy = '';
        }
        /*
        # Se for: Indicador de qualidade
        if(in_array($this->input->post('tipo_xml'), array(5))){
            $campoResp = 'resposta';
            $groupBy = '';
        }elseif(in_array($this->input->post('tipo_xml'), array(1))){
            $campoResp = 'GROUP_CONCAT(resposta ORDER BY cd_anatel_res ASC) AS resposta';
            $groupBy = 'GROUP BY id_servico, id_operadora, id_cep_aps, sigla, grupo';
        }else{
            $campoResp = 'GROUP_CONCAT(resposta ORDER BY cd_anatel_res ASC) AS resposta';
            $groupBy = 'GROUP BY id_servico, id_operadora, id_cep_aps, sigla';
        }
        */
        #$campoResp = (in_array($this->input->post('tipo_xml'), array(5)))? 'resposta': 'GROUP_CONCAT(resposta ORDER BY cd_anatel_res ASC) AS resposta';
        
        # Pegas as respostas dos indicadores
        /*$sql = "SELECT
                	id_servico,
                	id_operadora,
                	id_cep_aps,
                	sigla,
                    nome_servico,
					nome_pacote,
					tecnologia,
                    DATE_FORMAT(inicio_servico,'%d/%m/%Y') AS inicio_servico,
                	".$campoResp.",
                	SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),6,2) AS mes,
                	SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,4) AS ano
                FROM anatel_quest
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_quest.cd_anatel_frm
                LEFT JOIN anatel_res ON anatel_quest.cd_anatel_quest = anatel_res.cd_anatel_quest 
                LEFT JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_res.cd_unidade
                WHERE 
                	anatel_quest.sigla IS NOT NULL 
                	#AND anatel_quest.cd_anatel_frm = 1 
                	#AND anatel_res.cd_unidade = 9
                    AND id_operadora = ".$this->input->post('operadora')."
                    AND cd_anatel_xml = ".$this->input->post('tipo_xml')."
                	AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,7) = '".$data."'
                    ".$groupBy."
                #ORDER BY id_operadora, unidade.nome
                ORDER BY grupo, id_operadora, unidade.nome, anatel_quest.cd_anatel_quest";*/
        $sql = "SELECT
                	id_servico,
                	unidade.id_operadora,
                	REPLACE(operadora.nome, ' ', '') AS nome_operadora,
                    REPLACE(anatel_xml.nome, ' ', '') AS tipo_xml,
                	id_cep_aps,
                	anatel_quest.sigla,
                	nome_servico,
                	nome_pacote,
                	tecnologia,
                	DATE_FORMAT(inicio_servico,'%d/%m/%Y') AS inicio_servico,
                	".$campoResp.",
                	SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),6,2) AS mes,
                	SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,4) AS ano
                FROM anatel_quest
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_quest.cd_anatel_frm
                INNER JOIN anatel_xml ON anatel_xml.cd_anatel_xml = anatel_frm.cd_anatel_xml
                LEFT JOIN anatel_res ON anatel_quest.cd_anatel_quest = anatel_res.cd_anatel_quest 
                LEFT JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_res.cd_unidade
                INNER JOIN adminti.operadora ON adminti.operadora.id_operadora = adminti.unidade.id_operadora
                WHERE 
                	anatel_quest.sigla IS NOT NULL 
                	#AND anatel_quest.cd_anatel_frm = 1 
                	#AND anatel_res.cd_unidade = 9
                	AND unidade.id_operadora = ".$this->input->post('operadora')."
                	AND anatel_frm.cd_anatel_xml = ".$this->input->post('tipo_xml')."
                	AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,7) = '".$data."'
      		    ".$groupBy."
                #ORDER BY id_operadora, unidade.nome
                ORDER BY grupo, unidade.id_operadora, unidade.nome, anatel_quest.cd_anatel_quest";
                
        $dados['itens'] = $this->db->query($sql)->result();
        
        return $dados;
        
    }
    
    /**
     * Anatel_form_model::contGrupos()
     * 
     * Pega a quantidade de grupos de respostas
     * 
     * @return A quantidade
     */
    public function contGrupos($cd_frm, $cd_unidade){
        
        $sql = "SELECT
                	COUNT(*)+1 AS cont_grupo
                FROM (
                	SELECT
                	DISTINCT grupo
                	FROM anatel_res
                	WHERE cd_anatel_frm = ".$cd_frm."
                	AND cd_unidade = ".$cd_unidade."
                ) AS res";
                
        return $this->db->query($sql)->row()->cont_grupo;
        
    }
    
    /**
     * Anatel_form_model::importaPlanosOferecidos()
     * 
     * Importa os dados dos planos oferecidos
     * 
     * @return Os dados
     */
    public function importaPlanosOferecidos(){
        
        $conexao = $this->load->database('oracle', TRUE);
        #$sql = 'SELECT * FROM V_PACOTES_PRECOS_VIGENTE ORDER BY COD_PERMISSOR';
        $sql = 'SELECT 
                	DISTINCT
                	COD_PERMISSOR,
                	PERMISSOR,
                	PACOTE,
                	VALOR_INSTALACAO
                FROM V_PACOTES_PRECOS_VIGENTE ORDER BY COD_PERMISSOR';
        return $conexao->query($sql)->result();
    }
    
    /**
     * Anatel_form_model::importaIREDEC()
     * 
     * Importa os dados do IREDEC
     * 
     * @return Os dados
     */
    public function importaIREDEC(){
        
        $conexao = $this->load->database('siga_bcv', TRUE);
        $sql = "SELECT
                	COD_OPERADORA,
                	OPERADORA,
                	SUM(DECODE (TIPO, 20, QTDE, NULL)) numero_atend_erros,
                	SUM(DECODE (TIPO, 21, QTDE, NULL)) numero_doc_emitidos,
                    TO_CHAR(SYSDATE, 'YYYY-MM-DD HH24:MI:SS') data_cadastro
                FROM (
                	SELECT 
                		*
                	FROM supsiga.v_supgxv_anatel_iredc
                	WHERE COD_OPERADORA != 0
                	AND TIPO != 30
                	AND TRUNC(MES) BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -1), 'MM') AND LAST_DAY(ADD_MONTHS(SYSDATE, -1))
                	ORDER BY MES, OPERADORA, TIPO
                )
                GROUP BY COD_OPERADORA, OPERADORA";
        return $conexao->query($sql)->result();
        
    }
    
    public function importaICCO(){
        
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        $sql = "SELECT
                	COD_OPERADORA,
                	OPERADORA,
                	SUM(DECODE (TIPO, 6, QTDE, NULL)) dentro_prazo,
                	SUM(DECODE (TIPO, 7, QTDE, NULL)) total,
                	SUM(DECODE (TIPO, 8, QTDE, NULL)) fora_prazo,
                	TO_CHAR(SYSDATE, 'YYYY-MM-DD HH24:MI:SS') data_cadastro
                FROM (
                	SELECT 
                		* 
                	FROM supsiga.v_supgxv_anatel_icco
                	WHERE 
                	TRUNC(MES) BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -1), 'MM') AND LAST_DAY(ADD_MONTHS(SYSDATE, -1))
                	AND TIPO IN (6,7,8)
                	AND COD_OPERADORA != 0
                	ORDER BY OPERADORA, TIPO
                )
                GROUP BY COD_OPERADORA, OPERADORA";
                
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::importaBase()
     * 
     * Importa os dados da Base
     * 
     * @return Os dados
     */
    public function importaBase($permissor = false){
        
        $condPermissor = ($permissor)? 'AND COD_OPERADORA = '.$permissor: '';
        
        $conexao = $this->load->database('siga_bcv', TRUE);
        $sql = "SELECT 
                	ANO,
                	MES,
                	COD_OPERADORA,
                	OPERADORA,
                	CABLE_MODEM,
                	COMBO,
                	TV,
                	TOTAL_GERAL,
                	BASE_TV,
                	BANDA_LARGA,
                	TO_CHAR(SYSDATE, 'YYYY-MM-DD HH24:MI:SS') data_cadastro
                FROM supsiga.v_supgxv_anatel_base_ass
                WHERE CONCAT(ANO,MES) = TO_CHAR(ADD_MONTHS(SYSDATE, -1), 'YYYYfmMM')
                AND COD_OPERADORA != 0
                ".$condPermissor."
                ORDER BY MES";
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::indicadoresRespondidos()
     * 
     * Importa os dados dos planos oferecidos
     * 
     * @return Os dados
     */
    public function indicadoresRespondidos($respondido = 'NAO', $grupoIndicador = 'TODOS'){
        
        if($respondido == 'NAO'){ # Não foram respondidos
            $where = ' WHERE cont_resposta < cont_pergunta OR cont_pergunta = 0';
        }
        
        if($respondido == 'SIM'){ # Foram respondidos
            $where = ' WHERE cont_resposta = cont_pergunta AND cont_pergunta > 0';
        }
        
        if($grupoIndicador == 'TODOS'){
            $filtro = "WHERE anatel_frm.status = 'A'";
        }
        
        if($grupoIndicador == 1){ # Planos oferecidos
            $filtro = "WHERE anatel_frm.status = 'A' AND cd_anatel_xml = ".$grupoIndicador;
        }
        
        if($grupoIndicador == 3){ # Banda larga
            $filtro = "WHERE anatel_frm.status = 'A' AND cd_anatel_xml = ".$grupoIndicador;
        }
        
        if($grupoIndicador == 5){ # Indicadores de qualidade
            $filtro = "WHERE anatel_frm.status = 'A' AND cd_anatel_xml = ".$grupoIndicador." AND anatel_frm.cd_anatel_indicador NOT IN (13,14)";
        }
        
        $sql = "SELECT
                	tipo_sistema,
                	indicador,
                	nome_departamento,
                	unidade,
                	CASE WHEN (cont_pergunta = cont_resposta) AND cont_pergunta > 0
                		THEN 'SIM'
                	ELSE 'NAO' END AS respondido,
                	cont_pergunta,
                	cont_resposta,
                	nome_usuario,
                	email_usuario
                FROM (
                	SELECT 
                		anatel_tipo_frm.nome AS tipo_sistema,
                		anatel_indicador.sigla AS indicador,
                		nome_departamento,
                		unidade.nome AS unidade,
                		nome_usuario,
                		email_usuario,
                		(
                			SELECT 
                					COUNT(*)
                			FROM anatel_res 
                			WHERE anatel_res.cd_unidade = unidade.cd_unidade
                					AND anatel_res.cd_anatel_frm = anatel_frm.cd_anatel_frm
                					#AND SUBSTR(anatel_res.data_cadastro, 1, 7) = SUBSTR(CURDATE(), 1, 7)
								    #AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH), 1, 7) = '2015-03'
                                    AND DATE_FORMAT(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),'%m/%Y') = '".$this->input->post('mes_ano')."'
                		) AS cont_pergunta,
                		(
                			SELECT 
                				COUNT(*) 
                			FROM anatel_res 
                			WHERE anatel_res.cd_unidade = unidade.cd_unidade
                					AND anatel_res.cd_anatel_frm = anatel_frm.cd_anatel_frm
                					AND resposta IS NOT NULL AND resposta <> ''
                					#AND SUBSTR(anatel_res.data_cadastro, 1, 7) = SUBSTR(CURDATE(), 1, 7)
								    #AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH), 1, 7) = '2015-03'
                                    AND DATE_FORMAT(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),'%m/%Y') = '".$this->input->post('mes_ano')."'
                		) AS cont_resposta
                	FROM anatel_resp_indicador 
                	INNER JOIN adminti.usuario ON usuario.cd_usuario = anatel_resp_indicador.cd_usuario
                	INNER JOIN adminti.unidade ON unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                	INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                	INNER JOIN anatel_tipo_frm ON anatel_tipo_frm.cd_anatel_tipo_frm = anatel_frm.cd_anatel_tipo_frm
                	INNER JOIN departamento ON departamento.cd_departamento = anatel_frm.cd_departamento
                	INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                	".$filtro."
                ) AS RES".$where;
        
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::importaBandaLarga()
     * 
     * Importa os dados da banda larga
     * 
     * @return Os dados
     */
    public function importaBandaLarga(){
        
        $conexao = $this->load->database('oracle', TRUE);
        $sql = 'SELECT * FROM Supsiga.V_PACOTES_PRECOS_VIGENTE_CM ORDER BY PERCOD, IPAQDSC';
        return $conexao->query($sql)->result();
    }
    
    
    /**
     * Anatel_form_model::importaSIGA()
     * 
     * Importa os dados de ocorrências do siga
     * 
     * @return Os dados
     */
    public function importaSIGA(){
        
        $sql = "select  
                    o.PERCOD permissor, 
                    count(*) qtd
                from ocoabo o,    
                abonad y   
                where   
                (   (o.ogrpcod = '301'  and o.otpocod = '17' and o.OARESCOD in ('9685','9687') )   
                OR (o.ogrpcod = '302'	and o.otpocod = '16' and o.OARESCOD in ('9685') )   
                OR  (o.ogrpcod = '302'	and o.otpocod = '17' and o.OARESCOD in ('9703','9704','9705') )   
                OR  (o.ogrpcod = '302'	and o.otpocod = '18' and o.OARESCOD in ('9703','9704','9705','9706') )   
                OR  (o.ogrpcod = '302'	and o.otpocod = '19' and o.OARESCOD in ('9685','9707') )   
                OR  (o.ogrpcod = '302'	and o.otpocod = '20' and o.OARESCOD in ('9708','9709')	)   
                OR  (o.ogrpcod = '302'	and o.otpocod = '21' and o.OARESCOD in ('9685','9702','9710') )   
                OR  (o.ogrpcod = '302'	and o.otpocod = '22' and o.OARESCOD in ('9685','9711','9712','9713','9714','9715','9716','9717','9718','9719','9720','9721','9722','9723','9724') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '14' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '18' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '19' and o.OARESCOD in ('9685','9736') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '20' and o.OARESCOD in ('9685','9738','9737') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '21' and o.OARESCOD in ('9685','9738','9739') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '22' and o.OARESCOD in ('9685') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '23' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '24' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '26' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '303'	and o.otpocod = '29' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '307'	and o.otpocod = '1' and o.OARESCOD in ('9134','9846') )   
                OR  (o.ogrpcod = '308'	and o.otpocod = '3' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '308'	and o.otpocod = '4' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '308'	and o.otpocod = '5' and o.OARESCOD in ('9734') )   
                OR  (o.ogrpcod = '308'	and o.otpocod = '6' and o.OARESCOD in ('9734') )    
                OR  (o.ogrpcod = '308'	and o.otpocod = '7' and o.OARESCOD in ('9734') )   
                )   
                and o.percod   = y.percod   
                and o.abocod   = y.abocod   
                and trunc(o.OCOFCH) BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -1), 'MM') AND LAST_DAY(ADD_MONTHS(SYSDATE, -1))
                and trunc(y.ABOFCHDESC) BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -2)) AND TRUNC(SYSDATE)
                and y.abosts IN ('X', 'C')
                group by o.PERCOD";
        
        $conexao = $this->load->database('oracle', TRUE);
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::sigaUnidadesPendentes()
     * 
     * Pega as unidades que não foram importadas das ocorrências do Siga (Estão sem resposta definida)
     * 
     * @return Os dados
     */
    public function sigaUnidadesPendentes(){
        
        $sql = "SELECT 
                	cd_unidade 
                FROM anatel_resp_indicador WHERE cd_anatel_frm = 16 AND cd_unidade NOT IN(
                	SELECT 
                		DISTINCT cd_unidade 
                	FROM anatel_res WHERE cd_anatel_frm = 16 AND DATE_FORMAT(data_cadastro, '%m/%Y') = DATE_FORMAT(CURDATE(), '%m/%Y')
                )";
        
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::importaIAP()
     * 
     * Importa os dados da banda larga
     * 
     * @return Os dados
     */
    public function importaIAP(){
        
        $sql = "SELECT
                    codPer AS cod_unidade,
                	permissor,
                	tot_atend_prazo,
                	tot_atend_mes,
                	CASE WHEN tot_atend_superior >= 0 THEN tot_atend_superior ELSE 0 END AS tot_atend_superior,
                	FORMAT((tot_atend_prazo/tot_atend_mes) * 100, 2) AS res_porc,
                    CURRENT_TIMESTAMP() AS data_cadastro
                FROM (
                	SELECT 
                		codPer, 
                		CASE
                			WHEN codPer = 1 # Niterói
                				THEN 61
                			WHEN codPer = 2 # São Gonçalo
                				THEN 62
                			WHEN codPer = 5 # Gravatai
                				THEN 82
                			WHEN codPer = 6 # Juiz de Fora
                				THEN 64
                			WHEN codPer = 7 # Volta Redonda
                				THEN 63
                			WHEN codPer = 8 # Cuiabá
                				THEN 91
                			WHEN codPer = 9 # Varzea Grande
                				THEN 91
                			WHEN codPer = 10 # Salvador
                				THEN 51
                			WHEN codPer = 11 # Feira de Santana
                				THEN 53
                			WHEN codPer = 12 # Aracaju
                				THEN 52
                			WHEN codPer = 13 # Recife
                				THEN 71
                			WHEN codPer = 14 # Olinda
                				THEN 72
                			WHEN codPer = 15 # Jaboatão
                				THEN 73
                			WHEN codPer = 16 # Paulista
                				THEN 74
                		ELSE codPer END AS permissor,
                		(
                			SELECT 
                			COUNT(codAt) AS tot_atend_prazo
                			FROM atendimentos 
                			WHERE
                				date_format(horaChe, '%Y-%m-%d') >= CONCAT(SUBSTR(ADDDATE(CURDATE(), INTERVAL -1 MONTH),1,7),'-01')
                				AND date_format(horaChe, '%Y-%m-%d') <= LAST_DAY(ADDDATE(CURDATE(), INTERVAL -1 MONTH))
                				AND subtime(date_format(horaIniAt, '%H:%i:%s'), date_format(horaChe, '%H:%i:%s')) < '00:20:00'
                				AND codPer = pri.codPer
                			GROUP BY codPer
                		) AS tot_atend_prazo,
                		COUNT(codAt) AS tot_atend_mes,
                		(
                			SELECT 
                			COUNT(codAt) AS tot_atend_superior
                			FROM atendimentos 
                			WHERE
                				date_format(horaChe, '%Y-%m-%d') >= CONCAT(SUBSTR(ADDDATE(CURDATE(), INTERVAL -1 MONTH),1,7),'-01')
                				AND date_format(horaChe, '%Y-%m-%d') <= LAST_DAY(ADDDATE(CURDATE(), INTERVAL -1 MONTH))
                				AND subtime(date_format(horaIniAt, '%H:%i:%s'), date_format(horaChe, '%H:%i:%s')) > '00:30:00'
                				AND codPer = pri.codPer
                			GROUP BY codPer
                		) AS tot_atend_superior
                	FROM atendimentos AS pri
                	WHERE
                		codPer <> 9
                		AND date_format(horaIniAt, '%Y-%m-%d') >= CONCAT(SUBSTR(ADDDATE(CURDATE(), INTERVAL -1 MONTH),1,7),'-01')
                		AND date_format(horaIniAt, '%Y-%m-%d') <= LAST_DAY(ADDDATE(CURDATE(), INTERVAL -1 MONTH))
                		AND codPer IN (
                			SELECT codPer FROM permissores WHERE status = '0'
                		)
                	GROUP BY codPer
                ) AS RES";
                
        $conexao = $this->load->database('mysqlAntigo', TRUE);
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Anatel_form_model::verificaImportIRS()
     * 
     * Verifica se é possível realizar o calculo do IRS
     * Se retorna algo é sinal que ainda existem questões pendentes antes de realizar o calculo
     * 
     * @return Os dados
     */
    public function verificaImportIRS(){
        
        /*$sql = "SELECT
                	cd_unidade, 
                	nome,
                	COUNT(resposta) AS qtd_resposta,
                	CASE 
                		WHEN COUNT(resposta) BETWEEN 1 AND 5
                			THEN 'INCOMPLETO'
                		WHEN COUNT(resposta) = 6
                			THEN 'COMPLETO'
                	ELSE 'VAZIO' END status
                FROM (
                	SELECT 
                	DISTINCT anatel_quest.cd_anatel_quest, anatel_resp_indicador.cd_unidade, unidade.nome, resposta
                	FROM anatel_resp_indicador
                	INNER JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                	INNER JOIN anatel_quest ON anatel_quest.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                	LEFT JOIN anatel_res ON anatel_res.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm 
                        AND anatel_res.cd_anatel_quest = anatel_quest.cd_anatel_quest 
                        AND anatel_res.cd_unidade = anatel_resp_indicador.cd_unidade 
                        AND DATE_FORMAT(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH), '%m/%Y') = '".$this->input->post('mes_ano')."'
                        #AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,7) = '2015-05'
                	WHERE anatel_quest.cd_anatel_quest IN (33,19,39,41,42,61) 
                ) AS res
                #WHERE resposta IS NOT NULL
                GROUP BY cd_unidade, nome
                #HAVING COUNT(resposta) < 6";*/
                
        $sql = "SELECT 
                    anatel_indicador.sigla AS sigla,
                	anatel_indicador.nome AS indicador,
                	adminti.unidade.nome AS unidade,
                	anatel_quest.questao AS questao,
                	adminti.usuario.nome_usuario AS usuario,
                	adminti.usuario.email_usuario AS email
                FROM anatel_resp_indicador
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = anatel_resp_indicador.cd_usuario
                INNER JOIN anatel_frm ON anatel_frm.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                INNER JOIN anatel_indicador ON anatel_indicador.cd_anatel_indicador = anatel_frm.cd_anatel_indicador
                INNER JOIN adminti.unidade ON adminti.unidade.cd_unidade = anatel_resp_indicador.cd_unidade
                INNER JOIN anatel_quest ON anatel_quest.cd_anatel_frm = anatel_resp_indicador.cd_anatel_frm
                WHERE 
                anatel_quest.cd_anatel_quest IN (33,19,39,41,42,61)
                AND anatel_quest.cd_anatel_quest NOT IN (
                	SELECT cd_anatel_quest 
                	FROM anatel_res 
                	WHERE 
                        cd_unidade = adminti.unidade.cd_unidade 
                        AND DATE_FORMAT(ADDDATE(data_cadastro, INTERVAL -1 MONTH), '%m/%Y') = '".$this->input->post('mes_ano')."'
                )
                ORDER BY sigla, adminti.usuario.nome_usuario";
        
        return $this->db->query($sql)->result_array();
        
    }
    
    public function calculaESalvaIRS(){
        
        $this->db->trans_begin();
        $dadosCalculo = $this->dadosSomaIRS();
        
        foreach($dadosCalculo as $dC){
            $sql = "INSERT INTO anatel_res(cd_anatel_frm, cd_anatel_quest, resposta, grupo, cd_unidade, data_cadastro) ";
            $sql .= "VALUES(".$dC->cd_anatel_frm.", ".$dC->cd_anatel_quest.", '".$dC->resposta."', 1, ".$dC->cd_unidade.", '".$dC->data_cadastro."')";  
            $this->db->query($sql);
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
    
    public function dadosSomaIRS(){
        
        $dataInformada = implode('-',array_reverse(explode('/', $this->input->post('mes_ano'))));
        
        $dataCadastro = date('Y-m', strtotime("+1 month", strtotime($dataInformada.'-10')));
        
        $apagaExistente = $this->apagaIRSExistente($dataCadastro);
        
        $sql  = "SELECT 
                7 AS cd_anatel_frm, 21 AS cd_anatel_quest, SUM(resposta) AS resposta, 1 AS grupo, cd_unidade, '".$dataCadastro."-10' AS data_cadastro
                FROM anatel_quest 
                LEFT JOIN anatel_res ON anatel_quest.cd_anatel_quest = anatel_res.cd_anatel_quest AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,7) = '".$dataInformada."'
                WHERE anatel_quest.cd_anatel_quest IN (33,19,39,41,42)
                GROUP BY cd_unidade
                UNION
                SELECT 
                7 AS cd_anatel_frm, 22 AS cd_anatel_quest, resposta, 1 AS grupo, cd_unidade, '".$dataCadastro."-10' AS data_cadastro
                FROM anatel_quest 
                LEFT JOIN anatel_res ON anatel_quest.cd_anatel_quest = anatel_res.cd_anatel_quest AND SUBSTR(ADDDATE(anatel_res.data_cadastro, INTERVAL -1 MONTH),1,7) = '".$dataInformada."'
                WHERE anatel_quest.cd_anatel_quest IN (61)";
                
        return $this->db->query($sql)->result();
        
    }
    
    public function apagaIRSExistente($anoMes){
        
        $sql = "DELETE FROM anatel_res WHERE cd_anatel_frm = 7 AND data_cadastro LIKE '".$anoMes."%'";
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    # TEMPORARIO APAGAR DEPOIS
    public function emailsSenhas(){
        
        $sql = "SELECT * FROM envio";
        return $this->db->query($sql)->result();
        
    }

}