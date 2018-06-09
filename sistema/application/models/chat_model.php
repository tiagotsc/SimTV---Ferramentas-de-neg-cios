<?php

/**
 * Chat_model
 * 
 * Classe que manipula o banco do chat
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2015
 * @access public
 */
class Chat_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
        $this->load->library('Util', '', 'util');
	}
	
    /**
     * Usuario_model::atualizaStatusUsuario()
     * 
     * Atualiza o status do usuário
     * 
     * @return 
     */
    public function atualizaStatusUsuario(){
        
        $sql = "UPDATE adminti.usuario SET status_chat_usuario = '".$this->input->post('chatStatus')."', data_chat_usuario = CURRENT_TIMESTAMP WHERE cd_usuario = ".$this->session->userdata('cd').";";
		return $this->db->query($sql);
        
    }
    
    /**
     * Usuario_model::dpUnidades()
     * 
     * Pega os departamentos / unidades que possuem usuários ativos
     * 
     * @return 
     */
    public function dpUnidades(){
        
        $sql = "SELECT 
                DISTINCT
                	departamento.cd_departamento,
                	departamento.nome_departamento,
                	unidade.cd_unidade,
                	unidade.nome AS nome_unidade
                FROM adminti.usuario
                INNER JOIN adminti.departamento ON departamento.cd_departamento = usuario.cd_departamento
                INNER JOIN adminti.unidade ON unidade.cd_unidade = usuario.cd_unidade
                WHERE usuario.cd_departamento IS NOT NULL AND usuario.cd_unidade IS NOT NULL AND status_usuario = 'A'
                ORDER BY departamento.nome_departamento, unidade.nome";
            
        return $this->db->query($sql)->result();
    }
    
    /**
     * Usuario_model::dpUnidades()
     * 
     * Pega os usuários ativos
     * 
     * @return 
     */
    public function usuarios($cd_departamento = false, $cd_unidade = false, $usuario = false){
        
        $limit = '';
        
        if($cd_departamento){
            $condicaoDp = "AND cd_departamento = ".$cd_departamento;
        }else{
            $condicaoDp = '';
        }
        
        if($cd_unidade){
            $condicaoUni = "AND cd_unidade = ".$cd_unidade;
        }else{
            $condicaoUni = '';
        }
        
        if($usuario){
            $condicaoUser = "AND (nome_usuario LIKE '%".$usuario."%' OR email_usuario LIKE '%".$usuario."%' OR matricula_usuario LIKE '%".$usuario."%')";
            $limit = 'LIMIT 10';
        }else{
            $condicaoUser = '';
        }
        
        $sql = "SELECT 
                	cd_usuario,
                	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,
                	email_usuario,
                    cd_departamento,
                    cd_unidade,
                	logado_usuario,
                	data_logado_usuario,
            		CASE 
                		WHEN logado_usuario = 'S' AND status_chat_usuario = 'ONLINE'
                			THEN 'ONLINE'
                		WHEN logado_usuario = 'N'
                			THEN 'OFFLINE'
                	ELSE status_chat_usuario END AS status_chat_usuario,
                	data_chat_usuario,
                    (
                    	SELECT 
                    		COUNT(*) 
                    	FROM adminti.chat_favoritos 
                    	WHERE cd_adicionado = adminti.usuario.cd_usuario AND cd_usuario = ".$this->session->userdata('cd')."
                    ) AS favorito
                FROM adminti.usuario
                WHERE status_usuario = 'A' AND tipo_usuario = 'USER' 
                AND cd_usuario != ".$this->session->userdata('cd')."
                ".$condicaoDp."
                ".$condicaoUni."
                AND cd_departamento IS NOT NULL
                AND cd_unidade IS NOT NULL
                AND cd_usuario IN (
                	SELECT cd_usuario FROM sistema.config_usuario WHERE status_config_usuario = 'A'
                )
                AND cd_usuario NOT IN (
                    SELECT cd_adicionado FROM adminti.chat_favoritos WHERE cd_usuario = ".$this->session->userdata('cd')."
                )
                ".$condicaoUser."
                ORDER BY nome_usuario ".$limit;
        
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Usuario_model::favoritos()
     * 
     * Pega os usuários favoritados
     * 
     * @return 
     */
    public function favoritos(){
        
        $sql = "SELECT 
                	chat_favoritos.cd_adicionado,
                	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,
                	email_usuario,
                	logado_usuario,
                    cd_departamento,
                    cd_unidade,
                	data_logado_usuario,
                	CASE 
            			WHEN logado_usuario = 'S' AND status_chat_usuario = 'ONLINE'
            				THEN 'ONLINE'
            			WHEN logado_usuario = 'N'
            				THEN 'OFFLINE'
            		ELSE status_chat_usuario END AS status_chat_usuario,
            		data_chat_usuario 
                FROM adminti.chat_favoritos
                INNER JOIN adminti.usuario ON usuario.cd_usuario = chat_favoritos.cd_adicionado
                WHERE chat_favoritos.cd_usuario = ".$this->session->userdata('cd');
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Usuario_model::addFavoritos()
     * 
     * Adiciona usuários ao meus favoritos
     * 
     * @return 
     */
    public function addFavoritos(){
        
        $this->removeFavoritos();
        $sql = 'INSERT INTO adminti.chat_favoritos(cd_adicionado, cd_usuario) ';
        $sql .= 'VALUES('.$this->input->post('user').', '.$this->session->userdata('cd').')';
        return $this->db->query($sql);
        
    }
    
    /**
     * Usuario_model::removeFavoritos()
     * 
     * Remove usuário ao do meus favoritos
     * 
     * @return 
     */
    public function removeFavoritos(){
        
        $sql = 'DELETE FROM adminti.chat_favoritos WHERE cd_adicionado = '.$this->input->post('user').' AND cd_usuario = '.$this->session->userdata('cd');
        return $this->db->query($sql);
    }
    
    /**
     * Usuario_model::statusUsuarios()
     * 
     * Pega o status dos usuários
     * 
     * @return 
     */
    public function statusUsuarios(){
        
        $sql = "SELECT
                	cd_usuario,
                	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,
                	CASE 
                		WHEN logado_usuario = 'S' AND status_chat_usuario = 'ONLINE'
                			THEN 'ONLINE'
                		WHEN logado_usuario = 'N'
                			THEN 'OFFLINE'
                	ELSE status_chat_usuario END AS status_chat_usuario,
                	data_chat_usuario
                FROM adminti.usuario
                WHERE cd_usuario != ".$this->session->userdata('cd')." 
                AND logado_usuario = 'S' 
                AND data_chat_usuario > '".$this->input->post('dataUltimoStatus')."'
                ORDER BY data_chat_usuario";
        
        return $this->db->query($sql)->result();
        
    }
  
    /**
     * Usuario_model::qtdNewMsgPorConversa()
     * 
     * Pega a quantidade de novas mensagens por conversa
     * 
     * @return 
     */
    public function qtdNewMsgPorConversa(){
        
        /*$sql = "SELECT 
                	cd_origem, 
                    tipo_origem, 
                    COUNT(*) AS qtd_msg,
                    data_envio
                FROM adminti.chat_msg
                WHERE tipo_destino = 'user'
                AND cd_destino = ".$this->session->userdata('cd')."
                AND lida = 'N'
                #AND data_envio > '".$this->input->post('dataUltimoNovaMsg')."'
                GROUP BY cd_origem, tipo_origem
                ORDER BY data_envio";*/
        
        # SOMENTE USUÁRIO        
        /*$sql = "SELECT 
                	cd_usuario,
                	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,
                	email_usuario,
                	cd_departamento,
                	cd_unidade,
                	logado_usuario,
                	data_logado_usuario,
                    (
                    	SELECT 
                    		COUNT(*) 
                    	FROM adminti.chat_favoritos 
                    	WHERE cd_adicionado = adminti.usuario.cd_usuario AND cd_usuario = ".$this->session->userdata('cd')."
                    ) AS favorito,
                	CASE 
                		WHEN logado_usuario = 'S' AND status_chat_usuario = 'ONLINE'
                			THEN 'ONLINE'
                		WHEN logado_usuario = 'N'
                			THEN 'OFFLINE'
                	ELSE status_chat_usuario END AS status_chat_usuario,
                	cd_origem, 
                	tipo_origem, 
                	COUNT(*) AS qtd_msg,
                	data_envio
                FROM adminti.chat_msg
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = adminti.chat_msg.cd_origem
                WHERE tipo_destino = 'user'
                AND cd_destino = ".$this->session->userdata('cd')."
                AND lida = 'N'
                #AND data_envio > '".$this->input->post('dataUltimoNovaConversa')."'
                GROUP BY cd_origem, tipo_origem
                ORDER BY adminti.chat_msg.data_envio DESC";*/
                
        $sql = "SELECT 
                	cd_usuario,
                	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,
                	email_usuario,
                	cd_departamento,
                	cd_unidade,
                	logado_usuario,
                	data_logado_usuario,
                		(
                			SELECT 
                				COUNT(*) 
                			FROM adminti.chat_favoritos 
                			WHERE cd_adicionado = adminti.usuario.cd_usuario AND cd_usuario = ".$this->session->userdata('cd')."
                		) AS favorito,
                	CASE 
                		WHEN logado_usuario = 'S' AND status_chat_usuario = 'ONLINE'
                			THEN 'ONLINE'
                		WHEN logado_usuario = 'N'
                			THEN 'OFFLINE'
                	ELSE status_chat_usuario END AS status_chat_usuario,
                	cd_origem, 
                	tipo_origem, 
                	COUNT(*) AS qtd_msg,
                    'user' AS tipo,
                	MAX(data_envio) AS data_envio
                FROM adminti.chat_msg
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = adminti.chat_msg.cd_origem
                WHERE tipo_destino = 'user'
                AND cd_destino = ".$this->session->userdata('cd')."
                AND lida = 'N'
                #AND data_envio > '".$this->input->post('dataUltimoNovaConversa')."'
                GROUP BY cd_origem, tipo_origem
                #ORDER BY adminti.chat_msg.data_envio DESC
                UNION
                SELECT
                	CONCAT(cd_departamento,'-',cd_unidade) AS cd_dp,
                	CONCAT(nome_departamento,' - ',unidade.nome) AS nome_dp,
                	'' AS email,
                	cd_departamento,
                	cd_unidade,
                	'' AS logado,
                	'' AS data_logado,
                	'' AS favorito,
                	'' AS status_chat,
                	CONCAT(departamento.cd_departamento,'-',unidade.cd_unidade) AS cd_origem,
                	'' AS tipo_origem,
                	COUNT(*) AS qtd_msg,
                    'dp' AS tipo,
                	MAX(data_envio) AS data_envio
                FROM adminti.chat_msg
                INNER JOIN adminti.chat_lidas ON chat_lidas.cd_chat_msg = chat_msg.cd_chat_msg
                INNER JOIN adminti.departamento ON cd_departamento = chat_msg.cd_destino
                INNER JOIN adminti.unidade ON cd_unidade = chat_msg.local
                WHERE tipo_destino = 'dp'
                AND status_lida = 'N'
                AND cd_usuario = ".$this->session->userdata('cd')."
                GROUP BY cd_dp, nome_dp
                ORDER BY data_envio DESC
                ";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Usuario_model::qtdConversasNaoLidas()
     * 
     * Pega a quantidade de conversas não lidas
     * 
     * @return 
     */
    public function qtdConversasNaoLidas(){
        
        $sql = "SELECT 
                	COUNT(*) AS qtd_conv_nao_lidas
                FROM (
                	SELECT 
                		cd_origem,
                		COUNT(*)
                	FROM adminti.chat_msg
                	WHERE tipo_destino = 'user'
                	AND cd_destino = ".$this->session->userdata('cd')."
                	AND lida = 'N'
                	GROUP BY cd_origem
                    UNION
                	SELECT
                		CONCAT(chat_msg.cd_destino,'-',chat_msg.local) AS cd_origem,
                		COUNT(*)
                	FROM adminti.chat_msg
                	INNER JOIN adminti.chat_lidas ON chat_lidas.cd_chat_msg = chat_msg.cd_chat_msg AND cd_usuario = ".$this->session->userdata('cd')."
                	WHERE tipo_destino = 'dp'
                	AND status_lida = 'N'
                    GROUP BY cd_origem
                ) AS res";
                
        return $this->db->query($sql)->row();
        
    }
    
    /**
     * Usuario_model::conversasRecentes()
     * 
     * Pega as conversas recentes
     * 
     * @return 
     */
    public function conversasRecentes(){
        /*
        $sql = "SELECT 
                	cd_usuario,
                	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,
                	email_usuario,
                	cd_departamento,
                	cd_unidade,
                	logado_usuario,
                	data_logado_usuario,
                    (
                    	SELECT 
                    		COUNT(*) 
                    	FROM adminti.chat_favoritos 
                    	WHERE cd_adicionado = adminti.usuario.cd_usuario AND cd_usuario = ".$this->session->userdata('cd')."
                    ) AS favorito,
                	CASE 
                		WHEN logado_usuario = 'S' AND status_chat_usuario = 'ONLINE'
                			THEN 'ONLINE'
                		WHEN logado_usuario = 'N'
                			THEN 'OFFLINE'
                	ELSE status_chat_usuario END AS status_chat_usuario,
                    cd_origem,
                	(
                		SELECT
                			CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1))
                		FROM adminti.usuario 
                		WHERE cd_usuario = pri.cd_origem
                	) AS origem,
                	(
                		SELECT 
                			COUNT(*)
                		FROM adminti.chat_msg
                		WHERE tipo_destino = 'user'
                		AND cd_destino = ".$this->session->userdata('cd')." 
                		AND lida = 'N'
                		AND cd_origem = pri.cd_destino
                		AND tipo_origem = pri.tipo_origem
                	) AS qtd_msg_nao_lidas
                FROM adminti.chat_msg AS pri
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = CASE WHEN pri.cd_destino = ".$this->session->userdata('cd')." THEN pri.cd_origem ELSE pri.cd_destino END
                WHERE pri.tipo_origem = 'user'
                AND pri.cd_origem = adminti.usuario.cd_usuario
                AND pri.tipo_destino = 'user'
                AND DATE_FORMAT(pri.data_envio, '%Y-%m-%d') > ADDDATE(CURDATE(), INTERVAL -30 DAY)
                GROUP BY cd_usuario, nome_usuario, email_usuario, cd_departamento, cd_unidade, logado_usuario, data_logado_usuario, qtd_msg_nao_lidas
                ORDER BY pri.data_envio DESC";
        */        
        $sql = "SELECT
                    DISTINCT
                    cd_usuario,
                    nome_usuario,
                    email_usuario,
                    cd_departamento,
                    cd_unidade,
                    logado_usuario,
                    data_logado_usuario,
                    favorito,
                    status_chat_usuario,
                    cd_origem,
                    #origem,
                    qtd_msg_nao_lidas,
                    tipo
                FROM (
                    SELECT 
                    	cd_usuario,
                    	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario,
                    	email_usuario,
                    	cd_departamento,
                    	cd_unidade,
                    	logado_usuario,
                    	data_logado_usuario,
                    		(
                    			SELECT 
                    				COUNT(*) 
                    			FROM adminti.chat_favoritos 
                    			WHERE cd_adicionado = adminti.usuario.cd_usuario AND cd_usuario = ".$this->session->userdata('cd')."
                    		) AS favorito,
                    	CASE 
                    		WHEN logado_usuario = 'S' AND status_chat_usuario = 'ONLINE'
                    			THEN 'ONLINE'
                    		WHEN logado_usuario = 'N'
                    			THEN 'OFFLINE'
                    	ELSE status_chat_usuario END AS status_chat_usuario,
                   		CASE WHEN pri.cd_origem = ".$this->session->userdata('cd')." THEN pri.cd_destino ELSE pri.cd_origem END AS cd_origem,

                    	(
                    		SELECT
                    			CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1))
                    		FROM adminti.usuario 
                    		WHERE cd_usuario = pri.cd_origem
                    	) AS origem,
                    	(
                    		SELECT 
                    			COUNT(*)
                    		FROM adminti.chat_msg
                    		WHERE tipo_destino = 'user'
                    		AND cd_destino = ".$this->session->userdata('cd')." 
                    		AND lida = 'N'
                    		#AND cd_origem = pri.cd_origem
                            AND cd_origem IN (pri.cd_origem, pri.cd_destino)
                    		AND tipo_origem = pri.tipo_origem
                    	) AS qtd_msg_nao_lidas,
                    	MAX(pri.data_envio) AS data_envio,
                    	'user' AS tipo
                    FROM adminti.chat_msg AS pri
                    INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = CASE WHEN pri.cd_destino = ".$this->session->userdata('cd')." THEN pri.cd_origem ELSE pri.cd_destino END
                    WHERE pri.tipo_origem = 'user'
                    #AND pri.cd_origem = adminti.usuario.cd_usuario
                    AND (pri.cd_origem = ".$this->session->userdata('cd')." OR pri.cd_destino = ".$this->session->userdata('cd').")
                    AND pri.tipo_destino = 'user'
                    AND DATE_FORMAT(pri.data_envio, '%Y-%m-%d') > ADDDATE(CURDATE(), INTERVAL -90 DAY)
                    GROUP BY cd_usuario, nome_usuario, email_usuario, cd_departamento, cd_unidade, logado_usuario, data_logado_usuario, qtd_msg_nao_lidas
                    UNION
                    SELECT
                    	CONCAT(departamento.cd_departamento,'-',unidade.cd_unidade) AS cd_dep_uni, 
                    	CONCAT(departamento.nome_departamento,' - ',unidade.nome) AS nome_dep_uni,
                    	'' AS email_usuario,
                    	departamento.cd_departamento,
                    	unidade.cd_unidade,
                    	'' AS logado_usuario,
                    	'' AS data_logado_usuario,
                    	'' AS favorito,
                    	'' AS status_chat_usuario,
                    	'' AS cd_origem,
                    	'' AS origem,
                    	(
                    		SELECT
                    			COUNT(*)
                    		FROM adminti.chat_lidas
                    		WHERE 
                    			status_lida = 'N'
                    			AND tipo = 'dp'
                    			AND cd_usuario = ".$this->session->userdata('cd')."
                    			AND CONCAT(cd_destino,'-',local) = CONCAT(departamento.cd_departamento,'-',unidade.cd_unidade)
                    	) AS qtd_msg_nao_lidas,
                    	MAX(data_envio) AS data_envio,
                    	'dp' AS tipo
                    FROM adminti.chat_msg
                    INNER JOIN adminti.departamento ON departamento.cd_departamento = cd_destino
                    INNER JOIN adminti.unidade ON unidade.cd_unidade = local
                    WHERE tipo_destino = 'dp'
                    	AND (tipo_origem = 'user' AND cd_origem = ".$this->session->userdata('cd').") 
                        AND DATE_FORMAT(data_envio, '%Y-%m-%d') > ADDDATE(CURDATE(), INTERVAL -90 DAY)
                    	OR CONCAT(cd_destino,'-',local) = 
                    		(SELECT CONCAT(cd_departamento,'-',cd_unidade) FROM adminti.usuario WHERE cd_usuario = ".$this->session->userdata('cd').")
                    GROUP BY cd_dep_uni, nome_dep_uni, cd_departamento, cd_unidade
                ) AS res
                ORDER BY data_envio DESC";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Usuario_model::usuarioConversa()
     * 
     * Pega o usuário com que vai conversar
     * 
     * @return 
     */
    public function usuarioConversa(){
        
        $sql = "SELECT 
                cd_usuario,
                nome_usuario,
                cd_departamento,
                cd_unidade,
                (
                	SELECT 
                		COUNT(*) 
                	FROM adminti.chat_favoritos 
                	WHERE cd_adicionado = adminti.usuario.cd_usuario AND cd_usuario = ".$this->session->userdata('cd')."
                ) AS favorito
                FROM adminti.usuario WHERE cd_usuario = ".$this->input->post('user');
                
        return $this->db->query($sql)->row();
        
    }
    
    /**
     * Usuario_model::dpConversa()
     * 
     * Pega os dados do departamento com quem vai conversar
     * 
     * @return 
     */
    public function dpConversa($dp){
        
        $sql = "SELECT
                	cd_departamento,
                	nome_departamento
                FROM adminti.departamento
                WHERE cd_departamento = ".$dp;
        return $this->db->query($sql)->row();
        
    }
    
    /**
     * Usuario_model::uniConversa()
     * 
     * Pega os dados da unidade do departamento com que vai conversar
     * 
     * @return 
     */
    public function uniConversa($uni){
        
        $sql = "SELECT 
                	cd_unidade,
                	nome
                FROM adminti.unidade
                WHERE cd_unidade = ".$uni;
        return $this->db->query($sql)->row();
        
    }
    
    /**
     * Usuario_model::historicoUserConversa()
     * 
     * Pega o histórico do usuário com que esta conversando
     * 
     * @return 
     */
    public function historicoUserConversa($tipo = 'open'){
        
        if($tipo == 'open'){
            
            $dataNaoLida = $this->primeiraNaoLida();
            
            if($dataNaoLida){
                $condiData = "AND DATE_FORMAT(data_envio, '%Y-%m-%d') >= '".$dataNaoLida."'";
            }else{
                $condiData = "AND DATE_FORMAT(data_envio, '%Y-%m-%d') > ADDDATE(CURDATE(), INTERVAL -20 DAY)"; 
            }

        }else{
            $dataInicio = date('Y-m-d', strtotime("-20 days",strtotime($this->input->post('dataCorrente'))));
            $dataFim = $this->input->post('dataCorrente');
            $condiData = "AND DATE_FORMAT(data_envio, '%Y-%m-%d') >= '".$dataInicio."' AND DATE_FORMAT(data_envio, '%Y-%m-%d') <= '".$dataFim."'";
        }
        
        $sql = "SELECT 
                   	cd_chat_msg,
                	cd_origem,
                    SUBSTR(nome_usuario,1,20) AS nome_usuario,
                	tipo_origem,
                	cd_destino,
                	tipo_destino,
                	mensagem,
                	mensagem_tipo,
                    diretorio,
                    extensao,
                	lida,
                	DATE_FORMAT(data_lida, '%d/%m/%Y') AS data_lida,
                	SUBSTR(data_lida,12,8) AS hora_lida,
                    DATE_FORMAT(data_envio, '%Y-%m-%d') AS data_envio_original,
                	CASE 
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = CURRENT_DATE()
                			THEN 'Hoje'
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = ADDDATE(CURDATE(), INTERVAL -1 DAY)
                			THEN 'Ontem'
                	ELSE DATE_FORMAT(data_envio, '%d/%m/%Y') END AS data_envio,
                	SUBSTR(data_envio,12,8) AS hora_envio 
                FROM adminti.chat_msg
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = adminti.chat_msg.cd_origem
                WHERE tipo_origem = 'user'
                ".$condiData."
                AND cd_origem IN (".$this->input->post('user').",".$this->session->userdata('cd').")
                AND tipo_destino = '".$this->input->post('tipo')."'
                AND cd_destino IN (".$this->input->post('user').",".$this->session->userdata('cd').")
                ORDER BY adminti.chat_msg.data_envio ASC";
        return $this->db->query($sql)->result();
        
    }
    
    public function verificaMsgAnterior($data){
        
        if($this->input->post('tipo') == 'user'){
            $condicao = "tipo_origem = 'user'
                        AND cd_origem IN (".$this->input->post('user').",".$this->session->userdata('cd').")
                        AND tipo_destino = '".$this->input->post('tipo')."'
                        AND cd_destino IN (".$this->input->post('user').",".$this->session->userdata('cd').")";
        }else{ 
            $dpUni = explode('-', $this->input->post('user'));
            $condicao = "tipo_destino = 'dp'
                        AND chat_msg.cd_destino = ".$dpUni[0]."
                        AND chat_msg.local = ".$dpUni[1];
        }
        
        $sql = "SELECT
                    DATE_FORMAT(data_envio, '%Y-%m-%d') AS data_envio
                FROM adminti.chat_msg
                WHERE 
                ".$condicao."
                AND DATE_FORMAT(data_envio, '%Y-%m-%d') < '".$data."'
                ORDER BY data_envio DESC
                LIMIT 1";
                
        return $this->db->query($sql)->row()->data_envio;
        
    }
    
    /**
     * Usuario_model::historicoDpConversa()
     * 
     * Pega o histório de conversas do departamento - unidade
     * 
     * @return 
     */
    public function historicoDpConversa($dp, $uni, $tipo = 'open'){
        /*
        $sql = "SELECT 
            		cd_chat_msg,
            	cd_origem,
            		nome_usuario,
            	tipo_origem,
            	cd_destino,
            	tipo_destino,
            	mensagem,
            	mensagem_tipo,
            		diretorio,
            		extensao,
            	lida,
            	DATE_FORMAT(data_lida, '%d/%m/%Y') AS data_lida,
            	SUBSTR(data_lida,12,8) AS hora_lida,
            	CASE 
            		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = CURRENT_DATE()
            			THEN 'Hoje'
            		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = ADDDATE(CURDATE(), INTERVAL -1 DAY)
            			THEN 'Ontem'
            	ELSE DATE_FORMAT(data_envio, '%d/%m/%Y') END AS data_envio,
            	SUBSTR(data_envio,12,8) AS hora_envio 
            FROM adminti.chat_msg
            INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = adminti.chat_msg.cd_origem
            WHERE tipo_destino = 'dp'
            AND cd_destino = ".$dp."
            AND local = ".$uni."
            ORDER BY adminti.chat_msg.data_envio ASC";
        */  
        
        if($tipo == 'open'){
            
            $dataNaoLida = $this->primeiraNaoLida();
            
            if($dataNaoLida){
                $condiData = "AND DATE_FORMAT(data_envio, '%Y-%m-%d') >= '".$dataNaoLida."'";
            }else{
                $condiData = "AND DATE_FORMAT(data_envio, '%Y-%m-%d') > ADDDATE(CURDATE(), INTERVAL -20 DAY)";   
            }
            
        }else{
            $dataInicio = date('Y-m-d', strtotime("-20 days",strtotime($this->input->post('dataCorrente'))));
            $dataFim = $this->input->post('dataCorrente');
            $condiData = "AND DATE_FORMAT(data_envio, '%Y-%m-%d') >= '".$dataInicio."' AND DATE_FORMAT(data_envio, '%Y-%m-%d') <= '".$dataFim."'";
        }
          
        $sql = "SELECT 
                	chat_msg.cd_chat_msg,
                	cd_origem,
                	SUBSTR(nome_usuario,1,20) AS nome_usuario,
                	tipo_origem,
                	chat_msg.cd_destino,
                	tipo_destino,
                	mensagem,
                	mensagem_tipo,
                	diretorio,
                	extensao,
                	status_lida AS lida,
                	DATE_FORMAT(chat_lidas.data_lida, '%d/%m/%Y') AS data_lida,
                	SUBSTR(chat_lidas.data_lida,12,8) AS hora_lida,
                    DATE_FORMAT(data_envio, '%Y-%m-%d') AS data_envio_original,
                	CASE 
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = CURRENT_DATE()
                			THEN 'Hoje'
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = ADDDATE(CURDATE(), INTERVAL -1 DAY)
                			THEN 'Ontem'
                	ELSE DATE_FORMAT(data_envio, '%d/%m/%Y') END AS data_envio,
                	SUBSTR(data_envio,12,8) AS hora_envio 
                FROM adminti.chat_msg
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = adminti.chat_msg.cd_origem
                LEFT JOIN adminti.chat_lidas ON chat_lidas.cd_chat_msg = chat_msg.cd_chat_msg AND chat_lidas.cd_usuario = ".$this->session->userdata('cd')."
                WHERE tipo_destino = 'dp'
                ".$condiData."
                AND chat_msg.cd_destino = ".$dp."
                AND chat_msg.local = ".$uni."
                ORDER BY adminti.chat_msg.data_envio ASC";
        
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Usuario_model::primeiraNaoLida()
     * 
     * Pega a data da primeira mensagem não lida caso exista e seja menor que a data de paginação
     * 
     * @return 
     */
    public function primeiraNaoLida(){
        
        if($this->input->post('tipo') == 'user'){
        
            $sql = "SELECT 
                    	CASE
                    		WHEN DATE_FORMAT(MIN(data_lida), '%Y-%m-%d') < ADDDATE(CURDATE(), INTERVAL -20 DAY)
                    			THEN DATE_FORMAT(MIN(data_lida), '%Y-%m-%d')
                    		ELSE ADDDATE(CURDATE(), INTERVAL -20 DAY) END AS data_inicio
                    FROM adminti.chat_msg 
                    WHERE 
                    cd_origem = ".$this->input->post('user')." 
                    AND tipo_origem = 'user'
                    AND cd_destino = ".$this->session->userdata('cd')." 
                    AND tipo_destino = 'user'
                    AND lida = 'N'";
        
        }else{
            
            $dpUni = explode('-', $this->input->post('user'));
            
            $sql = "SELECT 
                    	CASE
                    		WHEN DATE_FORMAT(MIN(chat_lidas.data_lida), '%Y-%m-%d') < ADDDATE(CURDATE(), INTERVAL -20 DAY)
                    			THEN DATE_FORMAT(MIN(chat_lidas.data_lida), '%Y-%m-%d')
                    		ELSE ADDDATE(CURDATE(), INTERVAL -20 DAY) END AS data_inicio
                    FROM adminti.chat_msg 
                    LEFT JOIN adminti.chat_lidas ON chat_msg.cd_chat_msg = chat_lidas.cd_chat_lidas AND chat_lidas.cd_usuario = 6
                    WHERE 
                    chat_msg.cd_destino = ".$dpUni[0]."
                    AND chat_msg.tipo_destino = 'dp'
                    AND chat_msg.local = ".$dpUni[1]."
                    AND status_lida = 'N'";
            
        }
        
        return $this->db->query($sql)->row()->data_inicio;
        
    }
    
    /**
     * Usuario_model::statusMsgLida()
     * 
     * Atualiza as mensagens lidas para os status de LIDA
     * 
     * @return 
     */
    public function statusMsgLida(){
        
        if($this->input->post('origem_tipo') == 'user'){
        
            $sql = "UPDATE adminti.chat_msg SET lida = 'S', data_lida = CURRENT_TIMESTAMP ";
            $sql .= "WHERE cd_chat_msg IN (".$this->input->post('cd_msgs').") ";
            $sql .= "AND cd_destino = ".$this->session->userdata('cd');
        
        }else{
            
            $sql = "UPDATE adminti.chat_lidas SET status_lida = 'S', data_lida = CURRENT_TIMESTAMP ";
            $sql .= "WHERE cd_chat_msg IN (".$this->input->post('cd_msgs').") ";
            $sql .= "AND cd_usuario = ".$this->session->userdata('cd');
            
        }
        
        return $this->db->query($sql);
        
    }
    
    /**
     * Usuario_model::insereConversa()
     * 
     * Insere a nova mensagem
     * 
     * @return 
     */
    public function insereConversa(){
        
        if($this->input->post('destino_tipo') == 'user'){
            
            $destino = $this->input->post('destino');
            $campoUni = '';
            $valorUni = '';
            
        }else{
            
            $dadosDestino = explode('-', $this->input->post('destino'));
            $destino = $dadosDestino[0];
            $campoUni = ', local';
            $valorUni = ', '.$dadosDestino[1];
            
        }
        
        $sql = "INSERT INTO adminti.log_chat_msg(cd_origem, tipo_origem, cd_destino, tipo_destino".$campoUni.", mensagem, mensagem_tipo) ";
        $sql .= "VALUES(".$this->session->userdata('cd').", 'user', ".$destino.", '".$this->input->post('destino_tipo')."'".$valorUni.", '".$this->input->post('mensagem')."', '".$this->input->post('mensagem_tipo')."')";
        $this->db->query($sql);
        
        $sql = "INSERT INTO adminti.chat_msg(cd_origem, tipo_origem, cd_destino, tipo_destino".$campoUni.", mensagem, mensagem_tipo) ";
        $sql .= "VALUES(".$this->session->userdata('cd').", 'user', ".$destino.", '".$this->input->post('destino_tipo')."'".$valorUni.", '".$this->input->post('mensagem')."', '".$this->input->post('mensagem_tipo')."')";
        $this->db->query($sql);
        
        $cd_msg = $this->db->insert_id();
        
        if($this->input->post('destino_tipo') == 'dp'){
            $this->gravaNaoLidas($dadosDestino[0], $dadosDestino[1], $cd_msg);
        }
        
        return $cd_msg;
        
    }
    
    public function gravaNaoLidas($departamento, $unidade, $cd_msg){
        
        $usuarios = $this->usuariosDepartamentoUnidade($departamento, $unidade);
        
        foreach($usuarios as $usu){
            $sql = "INSERT INTO adminti.chat_lidas(cd_usuario, cd_chat_msg, cd_destino, local, tipo) ";
            $sql .= "VALUES(".$usu->cd_usuario.", ".$cd_msg.", ".$departamento.", ".$unidade.", '".$this->input->post('destino_tipo')."');";
            $this->db->query($sql);
        }
        
    }
    
    public function usuariosDepartamentoUnidade($departamento, $unidade){
        
        $sql = "SELECT 
                	cd_usuario
                FROM adminti.usuario
                WHERE cd_departamento = ".$departamento." 
                AND cd_unidade = ".$unidade."
                AND cd_usuario != ".$this->session->userdata('cd');
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * Usuario_model::insereArquivoConversa()
     * 
     * Insere o arquivo da conversa
     * 
     * @return 
     */
    public function insereArquivoConversa(){
        
        $path_parts = pathinfo($_FILES["file"]["name"]);
        $extension = $path_parts['extension'];
        
        if($this->input->post('userTipoEnviaFile') == 'user'){
            
            $destino = $this->input->post('userEnviaFile');
            $destino_tipo = $this->input->post('userTipoEnviaFile');
            $campoLocal = '';
            $valorLocal = '';
            
        }else{
            
            $dadosDestino = explode('-', $this->input->post('userEnviaFile'));
            $destino = $dadosDestino[0];
            $campoLocal = ', local';
            $valorLocal = ', '.$dadosDestino[1];
            $destino_tipo = $this->input->post('userTipoEnviaFile');
            
        }
        
        $sql = "INSERT INTO adminti.log_chat_msg(cd_origem, tipo_origem, cd_destino, tipo_destino".$campoLocal.", mensagem, mensagem_tipo, diretorio, extensao) ";
        $sql .= "VALUES(".$this->session->userdata('cd').", 'user', ".$destino.", '".$destino_tipo."'".$valorLocal.", '".$_FILES['file']['name']."', 'arquivo', '".'./files/chat/arquivos/'.date('Y').'/'.date('m')."','".$extension."')";
        $this->db->query($sql);
        
        $sql = "INSERT INTO adminti.chat_msg(cd_origem, tipo_origem, cd_destino, tipo_destino".$campoLocal.", mensagem, mensagem_tipo, diretorio, extensao) ";
        $sql .= "VALUES(".$this->session->userdata('cd').", 'user', ".$destino.", '".$destino_tipo."'".$valorLocal.", '".$_FILES['file']['name']."', 'arquivo', '".'./files/chat/arquivos/'.date('Y').'/'.date('m')."','".$extension."')";
        $this->db->query($sql);
        
        $cd_msg = $this->db->insert_id();
        
        if($this->input->post('userTipoEnviaFile') == 'dp'){
            $_POST['destino_tipo'] = 'dp';
            $this->gravaNaoLidas($dadosDestino[0], $dadosDestino[1], $cd_msg);
        }
        
        return $cd_msg;
        
    }
    
    /**
     * Usuario_model::dadosMsg()
     * 
     * Pega os dados da mensagem inserida
     * 
     * @return 
     */
    public function dadosMsg($cd_msg){
        
        $sql = "SELECT 
                    chat_msg.cd_chat_msg,
                	cd_origem,
                	tipo_origem,
                	chat_msg.cd_destino,
                	tipo_destino,
                	mensagem,
                	mensagem_tipo,
                    diretorio,
                    extensao,
                	CASE 
                        WHEN tipo_destino = 'user'
                            THEN lida
                    ELSE status_lida END AS lida,
                    CASE 
                        WHEN tipo_destino = 'user'
                            THEN DATE_FORMAT(chat_msg.data_lida, '%d/%m/%Y')
                    ELSE DATE_FORMAT(chat_lidas.data_lida, '%d/%m/%Y') END AS data_lida,
                    CASE 
                        WHEN tipo_destino = 'user'
                            THEN SUBSTR(chat_msg.data_lida,12,8)
                    ELSE SUBSTR(chat_lidas.data_lida,12,8) END AS hora_lida,
                	CASE 
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = CURRENT_DATE()
                			THEN 'Hoje'
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = ADDDATE(CURDATE(), INTERVAL -1 DAY)
                			THEN 'Ontem'
                	ELSE DATE_FORMAT(data_envio, '%d/%m/%Y') END AS data_envio,
                	SUBSTR(data_envio,12,8) AS hora_envio 
                FROM adminti.chat_msg 
                LEFT JOIN adminti.chat_lidas ON chat_lidas.cd_chat_msg = chat_msg.cd_chat_msg AND chat_lidas.cd_usuario = ".$this->session->userdata('cd')."
                WHERE chat_msg.cd_chat_msg = ".$cd_msg;
        return $this->db->query($sql)->row();
        
    }
    
    /**
     * Usuario_model::dadosFile()
     * 
     * Pega os dados do arquivo
     * 
     * @return 
     */
    public function dadosFile($cd_arquivo){
        
        $sql = "SELECT 
                	cd_chat_msg,
                	mensagem,
                	mensagem_tipo,
                	diretorio,
                    extensao
                FROM adminti.chat_msg 
                WHERE cd_chat_msg = ".$cd_arquivo."
                AND mensagem_tipo = 'arquivo'
                AND tipo_origem = 'user'
                #AND tipo_destino = 'user'
                AND (
                	(cd_origem = ".$this->session->userdata('cd')." OR cd_destino = ".$this->session->userdata('cd').")
                	OR 
                	CASE WHEN tipo_destino = 'dp'
                		THEN cd_destino = (SELECT cd_departamento FROM adminti.usuario WHERE cd_usuario = ".$this->session->userdata('cd').")
                	END 
                )";
        return $this->db->query($sql)->row();
        
    }
    
    /**
     * Usuario_model::pegaNovasMsgs()
     * 
     * Pega as novas mensagem
     * 
     * @return 
     */
    public function pegaNovasMsgs(){
        
        if($this->input->post('origem_tipo') == 'user'){
            
            $lida = 'lida';
            $dataLida = "DATE_FORMAT(data_lida, '%d/%m/%Y') AS data_lida";
            $horaLida = 'SUBSTR(data_lida,12,8) AS hora_lida';
            $join = '';
            
            $condicao = "tipo_origem = '".$this->input->post('origem_tipo')."' ";
            $condicao .= "AND cd_origem = ".$this->input->post('origem')." ";
            $condicao .= "AND tipo_destino = 'user' ";
            $condicao .= "AND cd_destino = ".$this->session->userdata('cd')." ";
            
        }else{
            
            $lida = 'status_lida AS lida';
            $dataLida = "DATE_FORMAT(chat_lidas.data_lida, '%d/%m/%Y') AS data_lida";
            $horaLida = 'SUBSTR(chat_lidas.data_lida,12,8) AS hora_lida';
            $join = 'LEFT JOIN adminti.chat_lidas ON chat_lidas.cd_chat_msg = chat_msg.cd_chat_msg AND chat_lidas.cd_usuario = '.$this->session->userdata('cd');
            
            $destino = explode("-", $this->input->post('origem'));
            
            $condicao = "tipo_destino = '".$this->input->post('origem_tipo')."' ";
            $condicao .= "AND chat_msg.cd_destino = ".$destino[0]." ";
            $condicao .= "AND chat_msg.local = ".$destino[1]." ";
            $condicao .= "AND tipo_origem = 'user' ";
            $condicao .= "AND cd_origem != ".$this->session->userdata('cd');
            
        }
        
        $sql = "SELECT 
      		        chat_msg.cd_chat_msg,
                	cd_origem,
               		SUBSTR(nome_usuario,1,20) AS nome_usuario,
                	tipo_origem,
                	chat_msg.cd_destino,
                	tipo_destino,
                	mensagem,
                	mensagem_tipo,
                    diretorio,
                    extensao,
                	".$lida.",
                	".$dataLida.",
                	".$horaLida.",
                	CASE 
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = CURRENT_DATE()
                			THEN 'Hoje'
                		WHEN DATE_FORMAT(data_envio, '%Y-%m-%d') = ADDDATE(CURDATE(), INTERVAL -1 DAY)
                			THEN 'Ontem'
                	ELSE DATE_FORMAT(data_envio, '%d/%m/%Y') END AS data_envio,
                	SUBSTR(data_envio,12,8) AS hora_envio,
                    data_envio AS data
                FROM adminti.chat_msg
                INNER JOIN adminti.usuario ON adminti.usuario.cd_usuario = adminti.chat_msg.cd_origem
                ".$join."
                WHERE 
                ".$condicao."
                AND lida = 'N'
                AND adminti.chat_msg.data_envio > '".$this->input->post('dataUltimoNovaMsg')."'
                ORDER BY adminti.chat_msg.data_envio ASC";
                
                return $this->db->query($sql)->result();
        
    }
    
    /**
     * Usuario_model::verificaExistenciaDinamica()
     * 
     * Verifica se existe alguma dinâmica
     * 
     * @return 
     */
    public function verificaExistenciaDinamica(){
        
        if($this->input->post('tipo') == 'dp'){
            $dpLocal = explode('-', $this->input->post('user'));
            $destino = $dpLocal[0];
            $local = "AND local = ".$dpLocal[1];
        }else{
            $destino = $this->input->post('user');
            $local = "";
        }
        
        $sql = "SELECT 
                COUNT(*) AS qtd
                FROM adminti.chat_dinamica
                WHERE 
                	cd_origem = ".$this->session->userdata('cd')." 
                	AND tipo_origem = 'user'
                	AND cd_destino = ".$destino."
                	AND tipo_destino = '".$this->input->post('tipo')."'
                    ".$local;
                    
        return $this->db->query($sql)->row();
        
    }
    
    /**
     * Usuario_model::configDinamica()
     * 
     * Configura inicio dinâmica
     * 
     * @return 
     */
    public function configDinamica(){
        
        $existe = $this->verificaExistenciaDinamica();
        
        if($existe->qtd == 0){
            
            if($this->input->post('tipo') == 'dp'){
                $dpLocal = explode('-', $this->input->post('user'));
                $destino = $dpLocal[0];
                $localInCampo = ', local';
                $localInValor = ', '.$dpLocal[1];
            }else{
                $destino = $this->input->post('user');
                $localInCampo = '';
                $localInValor = '';
            }
            
            $sql = "INSERT INTO adminti.chat_dinamica(cd_origem,tipo_origem, cd_destino, tipo_destino".$localInCampo.", data_abertura) ";
            $sql .= "VALUES(".$this->session->userdata('cd').", 'user', ".$destino.", '".$this->input->post('tipo')."'".$localInValor.", CURRENT_TIMESTAMP());";
        }else{
            
            if($this->input->post('tipo') == 'dp'){
                $dpLocal = explode('-', $this->input->post('user'));
                $destino = $dpLocal[0];
                $localUp = ' AND local = '.$dpLocal[1];
            }else{
                $destino = $this->input->post('user');
                $localUp = '';
            }
            
            $sql = "UPDATE adminti.chat_dinamica SET status_escrita = null ";
            $sql .= "WHERE cd_origem = ".$this->session->userdata('cd')." AND tipo_origem = 'user' AND cd_destino = ".$destino." AND tipo_destino = '".$this->input->post('tipo')."'".$localUp;
        }
        
        $this->db->query($sql);
        
    }
    
    /**
     * Usuario_model::defineDinamica()
     * 
     * Define o status da dinâmica
     * 
     * @return 
     */
    public function defineDinamica(){
        
        if($this->input->post('tipo') == 'user'){
        
            $sql = "UPDATE adminti.chat_dinamica SET status_escrita = '".$this->input->post('statusEscrita')."' ";
            $sql .= "WHERE cd_origem = ".$this->session->userdata('cd')." AND tipo_origem = 'user' AND cd_destino = ".$this->input->post('user')." AND tipo_destino = '".$this->input->post('tipo')."'";
        
        }else{
            
            $destino = explode('-',$this->input->post('user'));
            
            $sql = "UPDATE adminti.chat_dinamica SET status_escrita = '".$this->input->post('statusEscrita')."' ";
            $sql .= "WHERE cd_origem = ".$this->session->userdata('cd')." AND tipo_origem = 'user' AND cd_destino = ".$destino[0]." AND local = ".$destino[1]." AND tipo_destino = '".$this->input->post('tipo')."'";
            
        }
        $this->db->query($sql);
        
    }
    
    /**
     * Usuario_model::consultaDinamica()
     * 
     * Consulta se alguém esta escrevendo algo
     * 
     * @return 
     */
    public function consultaDinamica(){
        
        if($this->input->post('tipo') == 'user'){
        
            $sql = "SELECT 
                    	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario, 
                    	status_escrita 
                    FROM adminti.chat_dinamica
                    INNER JOIN adminti.usuario ON usuario.cd_usuario = cd_origem 
                            AND tipo_origem = '".$this->input->post('tipo')."' AND cd_origem = ".$this->input->post('user')." AND cd_origem != ".$this->session->userdata('cd')."
                    WHERE (cd_destino = ".$this->session->userdata('cd')." AND tipo_destino = 'user')
                    LIMIT 1";
                
        }else{
            
            $destino = explode('-',$this->input->post('user'));
            
            $sql = "SELECT 
                    	CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1)) AS nome_usuario, 
                    	CASE 
                    		WHEN status_escrita != ''
                    			THEN CONCAT(SUBSTRING_INDEX(nome_usuario,' ',1),' ', SUBSTRING_INDEX(nome_usuario,' ',-1), ' - ', status_escrita) 
                    	ELSE '' END AS status_escrita 
                    FROM adminti.chat_dinamica
                    INNER JOIN adminti.usuario ON usuario.cd_usuario = cd_origem AND cd_origem != ".$this->session->userdata('cd')."
                    WHERE cd_destino = ".$destino[0]." AND local = ".$destino[1]." AND tipo_destino = 'dp'
                    ORDER BY data_abertura DESC
                    LIMIT 1";
            
        }
                
        return $this->db->query($sql)->row();
        
    }

}