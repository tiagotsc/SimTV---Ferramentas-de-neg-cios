<?php

Class Helpdesk_model extends CI_Model {

    public function __construct() {

        parent::__construct();
    }

    public function chamadosEmAberto() {

        $sql = "SELECT
                COUNT(SIS.sistema) as CHAMADOS,
		SIS.sistema as AREA,
		SUM(IF ((TIMESTAMPDIFF(minute, OCOR.data_abertura, sysdate())) < (SLA.slas_tempo-(SLA.slas_tempo/5)), 1, '0')) as POSITIVO,
                SUM(IF ((TIMESTAMPDIFF(minute, OCOR.data_abertura, sysdate())) between (SLA.slas_tempo-(SLA.slas_tempo/5)) and SLA.slas_tempo, '1', '0')) as ALERTA,
                SUM(IF ((TIMESTAMPDIFF(minute, OCOR.data_abertura, sysdate())) >= (SLA.slas_tempo), '1', '0')) as NEGATIVO
        
                FROM ocomon_ti.ocorrencias OCOR
                inner join ocomon_ti.problemas PRO
                on PRO.prob_id = OCOR.problema
                inner join ocomon_ti.instituicao UND
                on UND.inst_cod = OCOR.instituicao
                inner join ocomon_ti.sla_solucao SLA
                on SLA.slas_cod = PRO.prob_sla
                inner join ocomon_ti.sistemas SIS
                on SIS.sis_id = OCOR.sistema

                WHERE STATUS not in (4,12)
                AND SIS.sis_id not in (3)

                GROUP BY area
                ORDER BY SIS.sistema;";

        $conexao = $this->load->database('mysqlAntigo', TRUE);

        $resultQuery = $conexao->query($sql)->result();

        $colunas = array(
            array(
                "id" => "",
                "label" => "Área",
                "pattern" => "",
                "type" => "string",
            ),
            array(
                "id" => "",
                "label" => "Dentro do SLA",
                "pattern" => "",
                "type" => "number",
            ),
            array(
                "id" => "",
                "label" => "Fora do SLA",
                "pattern" => "",
                "type" => "number",
            ),
            array(
                "id" => "",
                "label" => "SLA à Expirar",
                "pattern" => "",
                "type" => "number",
        ));


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => $r->AREA,
                        "f" => null,
                    ),
                    array(
                        "v" => $r->POSITIVO,
                        "f" => null
                    ),
                    array(
                        "v" => $r->NEGATIVO,
                        "f" => null
                    ),
                    array(
                        "v" => $r->ALERTA,
                        "f" => null
                    ))
            );

            $i++;
        }


        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function chamadosConcluidosArea($data1, $data2) {

        $sql = "SELECT
                COUNT(SIS.sistema) as CHAMADOS,
		SIS.sistema as AREA,
		SUM(IF (OCOR.status = 4, '1', '0')) as CONCLUIDO,
                SUM(IF (OCOR.status = 12, '1', '0')) as CANCELADO
                
                FROM ocomon_ti.ocorrencias OCOR
                inner join ocomon_ti.problemas PRO
                on PRO.prob_id = OCOR.problema
                inner join ocomon_ti.instituicao UND
                on UND.inst_cod = OCOR.instituicao
                inner join ocomon_ti.sla_solucao SLA
                on SLA.slas_cod = PRO.prob_sla
                inner join ocomon_ti.sistemas SIS
                on SIS.sis_id = OCOR.sistema

                WHERE OCOR.STATUS in (4,12)
                AND SIS.sis_id not in (3)";

        if ($data1 != "" and $data2 != "") {

            $sql .= " AND date_format(OCOR.data_fechamento, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= " GROUP BY area
                ORDER BY SIS.sistema;";


        $conexao = $this->load->database('mysqlAntigo', TRUE);

        $resultQuery = $conexao->query($sql)->result();


        $colunas = array(
            array(
//                "id" => "",
                "label" => "Área",
//                "pattern" => "",
                "type" => "string",
            ),
            array(
//                "id" => "",
                "label" => "Concluídos",
//                "pattern" => "",
                "type" => "number",
        ));


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => (string) $r->AREA,
//                        "f" => null,
                    ),
                    array(
                        "v" => (float) $r->CHAMADOS,
//                        "f" => null
                    ))
            );

            $i++;
        }

        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function chamadosConcluidosUnidade($data1, $data2) {

        $sql = "SELECT
                COUNT(SIS.sistema) as CHAMADOS,
		UND.inst_nome as UNIDADE
                
                FROM ocomon_ti.ocorrencias OCOR
                inner join ocomon_ti.problemas PRO
                on PRO.prob_id = OCOR.problema
                inner join ocomon_ti.instituicao UND
                on UND.inst_cod = OCOR.instituicao
                inner join ocomon_ti.sla_solucao SLA
                on SLA.slas_cod = PRO.prob_sla
                inner join ocomon_ti.sistemas SIS
                on SIS.sis_id = OCOR.sistema

                WHERE OCOR.STATUS in (4,12)
                AND SIS.sis_id not in (3)";

        if ($data1 != "" and $data2 != "") {

            $sql .= " AND date_format(OCOR.data_fechamento, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= " GROUP BY UNIDADE
                ORDER BY SIS.sistema;";


        $conexao = $this->load->database('mysqlAntigo', TRUE);

        $resultQuery = $conexao->query($sql)->result();


        $colunas = array(
            array(
//                "id" => "",
                "label" => "Unidade",
//                "pattern" => "",
                "type" => "string",
            ),
            array(
//                "id" => "",
                "label" => "Concluídos",
//                "pattern" => "",
                "type" => "number",
        ));


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => (string) $r->UNIDADE,
//                        "f" => null,
                    ),
                    array(
                        "v" => (float) $r->CHAMADOS,
//                        "f" => null
                    ))
            );

            $i++;
        }

        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function chamadosConcluidosTecnico($data1, $data2) {

        $sql = "SELECT
                COUNT(SIS.sistema) as CHAMADOS,
		USU.nome as TECNICO
                
                FROM ocomon_ti.ocorrencias OCOR
                inner join ocomon_ti.problemas PRO
                on PRO.prob_id = OCOR.problema
                inner join ocomon_ti.instituicao UND
                on UND.inst_cod = OCOR.instituicao
                inner join ocomon_ti.sla_solucao SLA
                on SLA.slas_cod = PRO.prob_sla
                inner join ocomon_ti.sistemas SIS
                on SIS.sis_id = OCOR.sistema
                inner join ocomon_ti.usuarios USU
                on USU.user_id = OCOR.operador

                WHERE OCOR.STATUS in (4,12)
                AND SIS.sis_id not in (3)";

        if ($data1 != "" and $data2 != "") {

            $sql .= " AND date_format(OCOR.data_fechamento, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= " GROUP BY TECNICO
                ORDER BY CHAMADOS;";


        $conexao = $this->load->database('mysqlAntigo', TRUE);

        $resultQuery = $conexao->query($sql)->result();


        $colunas = array(
            array(
//                "id" => "",
                "label" => "Técnico",
//                "pattern" => "",
                "type" => "string",
            ),
            array(
//                "id" => "",
                "label" => "Concluídos",
//                "pattern" => "",
                "type" => "number",
        ));


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => (string) $r->TECNICO,
//                        "f" => null,
                    ),
                    array(
                        "v" => (float) $r->CHAMADOS,
//                        "f" => null
                    ))
            );

            $i++;
        }

        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function chamadosComparativo($data1, $data2, $cond = false) {

        $sql = "SELECT
                date_format(OCOR.data_abertura, '%Y') as ANO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 1, 1, 0)) as JANEIRO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 2, 1, 0)) as FEVEREIRO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 3, 1, 0)) as MARÇO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 4, 1, 0)) as ABRIL,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 5, 1, 0)) as MAIO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 6, 1, 0)) as JUNHO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 7, 1, 0)) as JULHO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 8, 1, 0)) as AGOSTO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 9, 1, 0)) as SETEMBRO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 10, 1, 0)) as OUTUBRO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 11, 1, 0)) as NOVEMBRO,
                SUM(IF(date_format(OCOR.data_abertura, '%m') = 12, 1, 0)) as DEZEMBRO
                
                FROM ocomon_ti.ocorrencias OCOR
                inner join ocomon_ti.problemas PRO
                on PRO.prob_id = OCOR.problema
                inner join ocomon_ti.instituicao UND
                on UND.inst_cod = OCOR.instituicao
                inner join ocomon_ti.sla_solucao SLA
                on SLA.slas_cod = PRO.prob_sla
                inner join ocomon_ti.sistemas SIS
                on SIS.sis_id = OCOR.sistema
                inner join ocomon_ti.usuarios USU
                on USU.user_id = OCOR.operador

                WHERE OCOR.STATUS in (4,12)
                AND SIS.sis_id not in (3)";
        
        if ($data1 != "" and $data2 != "") {
            
            $sql .= "AND date_format(OCOR.data_abertura, '%Y') between '" . $data1 . "' and '" . $data2 . "'";
        }
        
        $sql .= " GROUP BY ANO
                ORDER BY ANO;";

        $conexao = $this->load->database('mysqlAntigo', TRUE);

        if ($cond == true) {

            return $conexao->query($sql)->result();
        }
        
        
        $resultQuery = $conexao->query($sql)->result();


        $colunas = array(
            array(
                "label" => "ANO",
                "type" => "string",
            ),
            array(
                "label" => "Janeiro",
                "type" => "number",
            ),
            array(
                "label" => "Fevereiro",
                "type" => "number",
            ),
            array(
                "label" => "Março",
                "type" => "number",
            ),
            array(
                "label" => "Abril",
                "type" => "number",
            ),
            array(
                "label" => "Maio",
                "type" => "number",
            ),
            array(
                "label" => "Junho",
                "type" => "number",
            ),
            array(
                "label" => "Julho",
                "type" => "number",
            ),
            array(
                "label" => "Agosto",
                "type" => "number",
            ),
            array(
                "label" => "Setembro",
                "type" => "number",
            ),
            array(
                "label" => "Outubro",
                "type" => "number",
            ),
            array(
                "label" => "Novembro",
                "type" => "number",
            ),
            array(
                "label" => "Dezembro",
                "type" => "number",
        ));


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => (integer) $r->ANO,
//                        "f" => null,
                    ),
                    array(
                        "v" => (integer) $r->JANEIRO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->FEVEREIRO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->MARÇO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->ABRIL,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->MAIO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->JUNHO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->JULHO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->AGOSTO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->SETEMBRO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->OUTUBRO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->NOVEMBRO,
//                        "f" => null
                    ),
                    array(
                        "v" => (integer) $r->DEZEMBRO,
//                        "f" => null
                    ))
            );

            $i++;
        }

        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

}
