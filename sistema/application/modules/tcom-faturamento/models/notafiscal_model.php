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
class notafiscal_model extends faturamento_model{
	
    const tabela = 'tcom_imposto';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function dadosNFTabelaDinamica(){ 
        
        $data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        
        $sql = "SELECT 
                    tcom_oper.id AS idPai,
                	(
                	SELECT COUNT(*) FROM ".BANCO_TELECOM.".tcom_titulo_fat AS sec WHERE sec.nota_fiscal IS NOT NULL AND CASE WHEN pri.idOperCobrPai = '' THEN sec.idOperCobrPai IS NULL ELSE sec.idOperCobrPai = pri.idOperCobrPai END AND sec.competencia = '".$data."-01'
                	) AS existe,
                	'Sim' AS faturado,
                	(
                		SELECT DATE_FORMAT(MAX(tcom_delin.competencia),'%m/%Y') FROM ".BANCO_TELECOM.".tcom_delin WHERE idOperCobrPai = tcom_oper.id
                	) AS proxima,
                	CASE WHEN tcom_oper.titulo IS NULL THEN 'NAO DEFINIDO' ELSE tcom_oper.titulo END AS grupo, 
                	COUNT(*) AS qtdOper,
                	ROUND(SUM(valor_cobrado),2) AS valor_cobrado
                FROM ".BANCO_TELECOM.".tcom_titulo_fat AS pri
                LEFT JOIN ".BANCO_TELECOM.".tcom_oper ON idOperCobrPai = tcom_oper.id
                WHERE 
                1=1
                AND nota_fiscal IS NULL
                AND competencia = '".$data."-01'
                GROUP BY tcom_oper.id, tcom_oper.titulo";
                #echo '<pre>'.$sql; exit();
            
        return $this->db->query($sql)->result(); 
        
    }
    
    public function gravaNF(){
        
        $data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        $idSelecionados = $this->input->post('idSelecionados');
        $semDefinicao = (substr($idSelecionados,0,2) == '0,')? 'OR paiOper.id IS NULL': '';
        
        $sql = "SELECT 
                	id,
                	CONCAT(SUBSTR(numero, 1,4),lpad(@contador := @contador + 1, 6, '0')) AS nota_fiscal
                FROM ".BANCO_TELECOM.".tcom_titulo_fat
                INNER JOIN (SELECT @contador := (SELECT CASE WHEN SUBSTR(MAX(nota_fiscal),5) IS NULL THEN 0 ELSE SUBSTR(MAX(nota_fiscal),5) END AS qtd FROM ".BANCO_TELECOM.".tcom_titulo_fat WHERE competencia = '".$data."-01')) AS cont
                WHERE 
                1=1
                AND nota_fiscal IS NULL
                AND competencia = '".$data."-01'
                AND idOperCobrPai IN (
                    ".$idSelecionados."
                ) ".$semDefinicao;
                
        $resultado = $this->db->query($sql)->result();
        
        $this->db->trans_begin();
        
        foreach($resultado as $res){
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_titulo_fat SET nota_fiscal = '".$res->nota_fiscal."' WHERE id = ".$res->id;
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

}