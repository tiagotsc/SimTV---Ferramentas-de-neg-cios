<?php

class asteriskPBX_model extends CI_Model {

    public function __construct() {

        parent::__construct();
    }

    public function receptivoTridigito($data1, $data2) {
        $sql = "SELECT CASE
                WHEN channel LIKE '%niteroi%' THEN 'NITEROI'
                WHEN channel LIKE '%sg%' THEN 'SAO GONCALO'
                WHEN channel LIKE '%vr%' THEN 'VOLTA REDONDA'
                WHEN channel LIKE '%juizdefora%' THEN 'JUIZ DE FORA'
                WHEN channel LIKE '%fs%' THEN 'FEIRA DE SANTANA'
                WHEN channel LIKE '%gravatai%' THEN 'GRAVATAI'
                WHEN channel LIKE '%cuiaba%' THEN 'CUIABA'
                WHEN channel LIKE '%172.16.91.211%' THEN 'CUIABA'
                WHEN channel LIKE '%salvador%' THEN 'SALVADOR'
                WHEN channel LIKE '%aracaju%' THEN 'ARACAJU'
                WHEN channel LIKE '%recife%' THEN 'RECIFE'
                END as Tronco,
                
                SUM(IF(TipoChamada = 'FIXO', billsec, 0)) as FIXO,
                SUM(IF(TipoChamada = 'CELULAR', billsec, 0)) as CELULAR,
                sec_to_time(SUM(IF(TipoChamada = 'FIXO', billsec, 0))) as FIXO_label,
                sec_to_time(SUM(IF(TipoChamada = 'CELULAR', billsec, 0))) as CELULAR_label


                FROM asteriskcdrdb.`10603` ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(calldate, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= " GROUP BY Tronco
                  ORDER BY SUM(billsec);";

        $conexao = $this->load->database('asteriskpbx', TRUE);

        $resultQuery = $conexao->query($sql)->result();

        $colunas = array(
            array(
                "id" => "",
                "label" => "Tronco",
                "pattern" => "",
                "type" => "string",
            ),
            array(
                "id" => "",
                "label" => "FIXO",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "CELULAR",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],);


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => $r->Tronco,
                        "f" => null,
                    ),
                    array(
                        "v" => $r->FIXO,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FIXO_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CELULAR,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CELULAR_label,
                        "f" => null
                    ),
                )
            );

            $i++;
        }


        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function ativoChamadasGSM($data1, $data2) {
        $sql = "SELECT Unidade,
                CASE
                WHEN dstchannel LIKE '%algar%' THEN 'ALGAR'
                WHEN dstchannel LIKE '%Intelig%' THEN 'INTELIG'
                WHEN dstchannel LIKE '%niteroi%' THEN 'INTELIG'
                WHEN dstchannel LIKE '%GW%' THEN 'GSM'
                WHEN dstchannel LIKE '%fpbx%' THEN 'ANTIGO PBX'
                WHEN dstchannel LIKE 'DAHDI%' THEN 'LINK ALGAR E1'
                WHEN dstchannel LIKE '%vicidial%' THEN 'VICIDIAL' ELSE 'OUTROS'
                END as tronco,

                SUM(IF(tipo = 'fixoLocal', segundos, 0)) as FixoLocal,
                SUM(IF(tipo = 'celularLocal', segundos, 0)) as CelularLocal,
                SUM(IF(tipo = 'fixoLDN', segundos, 0)) as FixoLDN,
                SUM(IF(tipo = 'celularLDN', segundos, 0)) as CelularLDN,
                
                sec_to_time(SUM(IF(tipo = 'fixoLocal', segundos, 0))) as FixoLocal_label,
                sec_to_time(SUM(IF(tipo = 'celularLocal', segundos, 0))) as CelularLocal_label,
                sec_to_time(SUM(IF(tipo = 'fixoLDN', segundos, 0))) as FixoLDN_label,
                sec_to_time(SUM(IF(tipo = 'celularLDN', segundos, 0))) as CelularLDN_label

                FROM asterisk.chamadas ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(data, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'
                    AND dstchannel LIKE '%GW%'";
        }

        $sql .= " GROUP BY tronco, unidade
                ORDER BY SUM(segundos)";

        $conexao = $this->load->database('impTest', TRUE);

        $resultQuery = $conexao->query($sql)->result();

        $colunas = array(
            array(
                "id" => "",
                "label" => "Unidade",
                "pattern" => "",
                "type" => "string",
            ),
            array(
                "id" => "",
                "label" => "FixoLocal",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "CelularLocal",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "FixoLDN",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "CelularLDN",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],);


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => $r->Unidade,
                        "f" => null,
                    ),
                    array(
                        "v" => $r->FixoLocal,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FixoLocal_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLocal,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLocal_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FixoLDN,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FixoLDN_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLDN,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLDN_label,
                        "f" => null
                    )
                )
            );

            $i++;
        }


        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function ativoChamadasIntelig($data1, $data2) {
        $sql = "SELECT Unidade,
                CASE
                WHEN dstchannel LIKE '%algar%' THEN 'ALGAR'
                WHEN dstchannel LIKE '%Intelig%' THEN 'INTELIG'
                WHEN dstchannel LIKE '%niteroi%' THEN 'INTELIG'
                WHEN dstchannel LIKE '%GW%' THEN 'GSM'
                WHEN dstchannel LIKE '%fpbx%' THEN 'ANTIGO PBX'
                WHEN dstchannel LIKE 'DAHDI%' THEN 'LINK ALGAR E1'
                WHEN dstchannel LIKE '%vicidial%' THEN 'VICIDIAL' ELSE 'OUTROS'
                END as tronco,

                SUM(IF(tipo = 'fixoLocal', segundos, 0)) as FixoLocal,
                SUM(IF(tipo = 'celularLocal', segundos, 0)) as CelularLocal,
                SUM(IF(tipo = 'fixoLDN', segundos, 0)) as FixoLDN,
                SUM(IF(tipo = 'celularLDN', segundos, 0)) as CelularLDN,
                
                sec_to_time(SUM(IF(tipo = 'fixoLocal', segundos, 0))) as FixoLocal_label,
                sec_to_time(SUM(IF(tipo = 'celularLocal', segundos, 0))) as CelularLocal_label,
                sec_to_time(SUM(IF(tipo = 'fixoLDN', segundos, 0))) as FixoLDN_label,
                sec_to_time(SUM(IF(tipo = 'celularLDN', segundos, 0))) as CelularLDN_label

                FROM asterisk.chamadas ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(data, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'
                    AND (dstchannel LIKE '%Intelig%'
                    OR dstchannel LIKE '%niteroi%')";
        }

        $sql .= " GROUP BY tronco, unidade
                ORDER BY SUM(segundos)";

        $conexao = $this->load->database('impTest', TRUE);

        $resultQuery = $conexao->query($sql)->result();

        $colunas = array(
            array(
                "id" => "",
                "label" => "Unidade",
                "pattern" => "",
                "type" => "string",
            ),
            array(
                "id" => "",
                "label" => "FixoLocal",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "CelularLocal",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "FixoLDN",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "CelularLDN",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],);


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => $r->Unidade,
                        "f" => null,
                    ),
                    array(
                        "v" => $r->FixoLocal,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FixoLocal_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLocal,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLocal_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FixoLDN,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FixoLDN_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLDN,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CelularLDN_label,
                        "f" => null
                    )
                )
            );

            $i++;
        }


        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function receptivoQmt9988($data1, $data2) {
        $sql = "SELECT CASE
                WHEN channel LIKE '%niteroi%' THEN 'NITEROI'
                WHEN channel LIKE '%sg%' THEN 'S. GONÇALO'
                WHEN channel LIKE '%vr%' THEN 'VR'
                WHEN channel LIKE '%juizdefora%' THEN 'JF'
                WHEN channel LIKE '%fs%' THEN 'FEIRA'
                WHEN channel LIKE '%gravatai%' THEN 'GRAVATAI'
                WHEN channel LIKE '%cuiaba%' THEN 'CUIABA'
                WHEN channel LIKE '%172.16.91.211%' THEN 'CUIABA'
                WHEN channel LIKE '%salvador%' THEN 'SALVADOR'
                WHEN channel LIKE '%aracaju%' THEN 'ARACAJU'
                WHEN channel LIKE '%recife%' THEN 'RECIFE'
                END as Tronco,
                
                SUM(IF(TipoChamada = 'FIXO', billsec, 0)) as FIXO,
                SUM(IF(TipoChamada = 'CELULAR', billsec, 0)) as CELULAR,
                sec_to_time(SUM(IF(TipoChamada = 'FIXO', billsec, 0))) as FIXO_label,
                sec_to_time(SUM(IF(TipoChamada = 'CELULAR', billsec, 0))) as CELULAR_label

                FROM asteriskcdrdb.`40039988` ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(calldate, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= " GROUP BY Tronco
                  ORDER BY SUM(billsec)";

        $conexao = $this->load->database('asteriskpbx', TRUE);

        $resultQuery = $conexao->query($sql)->result();

        $colunas = array(
            array(
                "id" => "",
                "label" => "Tronco",
                "pattern" => "",
                "type" => "string",
            ),
            array(
                "id" => "",
                "label" => "FIXO",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],
            array(
                "id" => "",
                "label" => "CELULAR",
                "pattern" => "",
                "type" => "number",
            ),
            [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ],);


        $i = 0;
        foreach ($resultQuery as $r) {

            $row[$i] = array(
                "c" => array(
                    array(
                        "v" => $r->Tronco,
                        "f" => null,
                    ),
                    array(
                        "v" => $r->FIXO,
                        "f" => null
                    ),
                    array(
                        "v" => $r->FIXO_label,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CELULAR,
                        "f" => null
                    ),
                    array(
                        "v" => $r->CELULAR_label,
                        "f" => null
                    ),
                )
            );

            $i++;
        }


        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function receptivoQmt8668($data1, $data2) {
        $sql = "SELECT CASE
                WHEN channel LIKE '%niteroi%' THEN 'NITEROI'
                WHEN channel LIKE '%sg%' THEN 'SAO GONCALO'
                WHEN channel LIKE '%vr%' THEN 'VOLTA REDONDA'
                WHEN channel LIKE '%juizdefora%' THEN 'JUIZ DE FORA'
                WHEN channel LIKE '%fs%' THEN 'FEIRA DE SANTANA'
                WHEN channel LIKE '%gravatai%' THEN 'GRAVATAI'
                WHEN channel LIKE '%cuiaba%' THEN 'CUIABA'
                WHEN channel LIKE '%172.16.91.211%' THEN 'CUIABA'
                WHEN channel LIKE '%salvador%' THEN 'SALVADOR'
                WHEN channel LIKE '%aracaju%' THEN 'ARACAJU'
                WHEN channel LIKE '%recife%' THEN 'RECIFE'
                END as Tronco,
                
                SUM(IF(TipoChamada = 'FIXO', billsec, 0)) as FIXO,
                SUM(IF(TipoChamada = 'CELULAR', billsec, 0)) as CELULAR,
                sec_to_time(SUM(IF(TipoChamada = 'FIXO', billsec, 0))) as FIXO_label,
                sec_to_time(SUM(IF(TipoChamada = 'CELULAR', billsec, 0))) as CELULAR_label

                FROM asteriskcdrdb.`40038668` ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(calldate, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= " GROUP BY Tronco
                 ORDER BY SUM(billsec);";


        $conexao = $this->load->database('asteriskpbx', TRUE);

        $resultQuery = $conexao->query($sql)->result();

        //CRIA AS COLUNAS DO BANCO DE DADOS
        $colunas_db = ['FIXO', 'CELULAR'];

        $col[] = [
            "id" => "",
            "label" => 'Tronco',
            "pattern" => "",
            "type" => "string",
        ];

        foreach ($colunas_db as $db) :

            $col[] = [
                "id" => "",
                "label" => $db,
                "pattern" => "",
                "type" => "number",
            ];

            $col[] = [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ];
        endforeach;

        $colunas = $col;


        $i = 0;

        foreach ($resultQuery as $r) {
            $ro = [[
            "v" => $r->Tronco,
            "f" => null,
            ]];

            foreach ($colunas_db as $m) {
                $horas = floor($r->$m / 3600);
                $minutos = floor($r->$m / 60 % 60);
                $segundos = floor($r->$m % 60);
                $convert = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

                $ro[] = [
                    "v" => $r->$m,
                    "f" => null
                ];

                $ro[] = [
                    "v" => $convert,
                    "f" => null
                ];
            }

            $ro[] = [
                "v" => NULL,
                "f" => null
            ];

            $row[$i] = array(
                "c" => $ro
            );

            $i++;
        }

        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function comparativoPeriodo($data1, $data2) {
        $sql = "SELECT
                'Tridígito' as tipo,                
                SUM(IF(date_format(calldate, '%m') = 1, billsec, 0)) as JANEIRO,
                SUM(IF(date_format(calldate, '%m') = 2, billsec, 0)) as FEVEREIRO,
                SUM(IF(date_format(calldate, '%m') = 3, billsec, 0)) as MARÇO,
                SUM(IF(date_format(calldate, '%m') = 4, billsec, 0)) as ABRIL,
                SUM(IF(date_format(calldate, '%m') = 5, billsec, 0)) as MAIO,
                SUM(IF(date_format(calldate, '%m') = 6, billsec, 0)) as JUNHO,
                SUM(IF(date_format(calldate, '%m') = 7, billsec, 0)) as JULHO,
                SUM(IF(date_format(calldate, '%m') = 8, billsec, 0)) as AGOSTO,
                SUM(IF(date_format(calldate, '%m') = 9, billsec, 0)) as SETEMBRO,
                SUM(IF(date_format(calldate, '%m') = 10, billsec, 0)) as OUTUBRO,
                SUM(IF(date_format(calldate, '%m') = 11, billsec, 0)) as NOVEMBRO,
                SUM(IF(date_format(calldate, '%m') = 12, billsec, 0)) as DEZEMBRO,
                SUM(billsec) as Total
                FROM asteriskcdrdb.`10603` ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(calldate, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= "UNION
                SELECT
                '4003-8668' as tipo,                
                SUM(IF(date_format(calldate, '%m') = 1, billsec, 0)) as JANEIRO,
                SUM(IF(date_format(calldate, '%m') = 2, billsec, 0)) as FEVEREIRO,
                SUM(IF(date_format(calldate, '%m') = 3, billsec, 0)) as MARÇO,
                SUM(IF(date_format(calldate, '%m') = 4, billsec, 0)) as ABRIL,
                SUM(IF(date_format(calldate, '%m') = 5, billsec, 0)) as MAIO,
                SUM(IF(date_format(calldate, '%m') = 6, billsec, 0)) as JUNHO,
                SUM(IF(date_format(calldate, '%m') = 7, billsec, 0)) as JULHO,
                SUM(IF(date_format(calldate, '%m') = 8, billsec, 0)) as AGOSTO,
                SUM(IF(date_format(calldate, '%m') = 9, billsec, 0)) as SETEMBRO,
                SUM(IF(date_format(calldate, '%m') = 10, billsec, 0)) as OUTUBRO,
                SUM(IF(date_format(calldate, '%m') = 11, billsec, 0)) as NOVEMBRO,
                SUM(IF(date_format(calldate, '%m') = 12, billsec, 0)) as DEZEMBRO,
                SUM(billsec) as Total
                FROM asteriskcdrdb.`40038668` ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(calldate, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $sql .= "UNION
                SELECT
                '4003-9988' as tipo,                
                SUM(IF(date_format(calldate, '%m') = 1, billsec, 0)) as JANEIRO,
                SUM(IF(date_format(calldate, '%m') = 2, billsec, 0)) as FEVEREIRO,
                SUM(IF(date_format(calldate, '%m') = 3, billsec, 0)) as MARÇO,
                SUM(IF(date_format(calldate, '%m') = 4, billsec, 0)) as ABRIL,
                SUM(IF(date_format(calldate, '%m') = 5, billsec, 0)) as MAIO,
                SUM(IF(date_format(calldate, '%m') = 6, billsec, 0)) as JUNHO,
                SUM(IF(date_format(calldate, '%m') = 7, billsec, 0)) as JULHO,
                SUM(IF(date_format(calldate, '%m') = 8, billsec, 0)) as AGOSTO,
                SUM(IF(date_format(calldate, '%m') = 9, billsec, 0)) as SETEMBRO,
                SUM(IF(date_format(calldate, '%m') = 10, billsec, 0)) as OUTUBRO,
                SUM(IF(date_format(calldate, '%m') = 11, billsec, 0)) as NOVEMBRO,
                SUM(IF(date_format(calldate, '%m') = 12, billsec, 0)) as DEZEMBRO,
                SUM(billsec) as TOTAL
                FROM asteriskcdrdb.`40039988` ";

        if ($data1 != "" and $data2 != "") {
            $sql .= " WHERE date_format(calldate, '%Y-%m-%d') between '" . $data1 . "' and '" . $data2 . "'";
        }

        $conexao = $this->load->database('asteriskpbx', TRUE);

        $resultQuery = $conexao->query($sql)->result();


//CRIA AS COLUNAS MENSAIS c/ Array
        $colunas_db = ['JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO', 'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO'];

        $col = array([
                "id" => "",
                "label" => "tipo",
                "pattern" => "",
                "type" => "string",
            ],);
//CONCATENA na variavel $COL
        foreach ($resultQuery as $r) {


            foreach ($colunas_db as $co) :
                if ($r->$co != 0) {
                    $col[] = array(
                        "id" => "",
                        "label" => $co,
                        "pattern" => "",
                        "type" => "number",
                    );
                    $col[] = [
                        "id" => "",
                        "label" => "",
                        "role" => "tooltip",
                        "type" => "string",
                    ];
                }

            endforeach;

            $col[] = array(
                "id" => "",
                "label" => 'TOTAL',
                "pattern" => "",
                "type" => "number",
            );
            $col[] = [
                "id" => "",
                "label" => "",
                "role" => "tooltip",
                "type" => "string",
            ];
        }


//MONTA AS COLUNAS MENSAIS
        $colunas = $col;

        $i = 0;

        foreach ($resultQuery as $r) {
            $ro = [[
            "v" => $r->tipo,
            "f" => null,
            ]];
            foreach ($colunas_db as $m) {

                if ($r->$m != 0) {
                    $horas = floor($r->$m / 3600);
                    $minutos = floor($r->$m / 60 % 60);
                    $segundos = floor($r->$m % 60);
                    $convert = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);


                    $ro[] = [
                        "v" => $r->$m,
                        "f" => null
                    ];

                    $ro[] = [
                        "v" => $convert,
                        "f" => null
                    ];
                }
            }

            $ro[] = [
                "v" => NULL,
                "f" => null
            ];

            $horas = floor($r->Total / 3600);
            $minutos = floor($r->Total / 60 % 60);
            $segundos = floor($r->Total % 60);
            $converte = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);

            $ro[] = [
                "v" => 'TOTAL: ' . $converte,
                "f" => null
            ];


            $row[$i] = array(
                "c" => $ro
            );


            $i++;
        }

        return array(
            "cols" => $colunas,
            "rows" => $row,
        );
    }

    public function comparativoMensal($data1, $data2) {
        
    }

}
