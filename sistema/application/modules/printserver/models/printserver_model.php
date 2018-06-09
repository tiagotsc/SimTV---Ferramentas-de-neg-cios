<?php

class printserver_model extends CI_Model {

    public function __construct() {

        parent::__construct();
    }

    public function acompanhamentoGeral($data1, $data2) {

        $sql = "SELECT sum(JAS.pages) as TOTAL_PAGINAS, UND.nome as LOCALIDADE

                FROM jobs_log JAS

                INNER JOIN adminti.unidade UND

                on UND.permissor = JAS.id_permissor";

        if ($data1 != "" and $data2 != "") {

            $sql .= " WHERE date_format(JAS.date, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= " GROUP BY JAS.id_permissor

                UNION

                SELECT sum(JAS.pages) as TOTAL_PAGINAS, IF (UND.nome = '', 'Total', 'Total') as LOCALIDADE

                FROM jobs_log JAS

                INNER JOIN adminti.unidade UND
                on UND.permissor = JAS.id_permissor
                
                WHERE date_format(JAS.date, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
            
        
        $conexao = $this->load->database('impTest', TRUE);

        $resultQuery = $conexao->query($sql)->result();

        $colunas = array(
            array(
                "id" => "",
                "label" => "Localidade",
                "pattern" => "",
                "type" => "string",
            ),
            array(
                "id" => "",
                "label" => "Total Impressos",
                "pattern" => "",
                "type" => "number",
        ));


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => $r->LOCALIDADE,
                        "f" => null,
                    ),
                    array(
                        "v" => $r->TOTAL_PAGINAS,
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

    public function acompanhamentoDetalhado($und, $ano, $mes) {

        $sql = "SELECT
                JAS.id_permissor as PERMISSOR,
                sum(JAS.pages) as TOTAL_PAGINAS,
                UND.nome as LOCALIDADE,
                COALESCE(DP.nome_departamento, 'Outros') as DEPARTAMENTO


                FROM jasmine.jobs_log JAS

                LEFT JOIN adminti.usuario USU on USU.login_usuario = JAS.user
                INNER JOIN adminti.unidade UND on UND.permissor = JAS.id_permissor
                LEFT JOIN adminti.departamento DP on DP.cd_departamento = USU.cd_departamento";

        IF ($und === NULL) {
            $sql .=" WHERE UND.nome is null";
        } else {
            $sql .=" WHERE UND.permissor = " . $und;
        }

        if ($mes != "" and $ano != "") {

            $sql .= " AND date_format(JAS.date, '%Y-%m') = '" . $ano . "-" . $mes . "'";
        }

        $sql .= " GROUP BY LOCALIDADE, DEPARTAMENTO
                ORDER BY TOTAL_PAGINAS DESC;";

        $conexao = $this->load->database('impTest', TRUE);

        $resultadoQuery = $conexao->query($sql)->result();

        $colunas = array(
            array(
                "id" => "",
                "label" => "Departamento",
                "pattern" => "",
                "type" => "string",
            ),
            array(
                "id" => "",
                "label" => "Total Impressos",
                "pattern" => "",
                "type" => "number",
        ));


        $i = 0;
        foreach ($resultadoQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => $r->DEPARTAMENTO,
                        "f" => null,
                    ),
                    array(
                        "v" => $r->TOTAL_PAGINAS,
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

    public function unidadeArray($und = null) {


        $sql = "SELECT DISTINCT
            JAS.id_permissor as PERMISSOR,
            UND.nome as LOCALIDADE,
            date_format(JAS.date, '%m') as MES,
            date_format(JAS.date, '%Y') as ANO
            
            FROM jasmine.jobs_log JAS

            INNER JOIN adminti.unidade UND on UND.permissor = JAS.id_permissor";

        IF ($und != null) {

            $sql .= " WHERE UND.permissor = " . $und;
        }


        $sql .= " GROUP BY LOCALIDADE, MES, ANO;";

        $conexao = $this->load->database('impTest', TRUE);

        return $conexao->query($sql)->result();
    }

}
