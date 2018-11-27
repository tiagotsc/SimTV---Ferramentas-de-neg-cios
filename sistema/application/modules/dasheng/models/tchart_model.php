<?php

class tchart_model extends CI_Model {

    public $sim_tv_color = '#A4A4A4',
            $con;
    
    
    function __construct() {
        $this->con = $this->load->database('defaultSgo',TRUE);
        $this->load->model('tbase_model','tbase');
    }

    function cepRankByNodeChart($ceps) {
        $result = array(
                    'name' => 'Logradouros Críticos',
                    'series' => array('name' => 'Reclamações', 'data' => array())
        );
        $total = 0;
        for ($i = 0; $i < count($ceps); $i++) {
            $total += intval($ceps[$i]['c']);
            if ($ceps[$i]['cep']) {


                $result['series']['data'][] = array(
                    'name' => $ceps[$i]['logradouro'],
                    'cep' => $ceps[$i]['cep'],
                    'bairro' => ucwords(strtolower($ceps[$i]['bairro'])),
                    'cidade' => ucwords(strtolower($ceps[$i]['cidade'])),
                    'y' => intval($ceps[$i]['c']));
            }
        }
        $result['sum'] = $total;
        $result['series'] = array($result['series']);
        return $result;
    }

    function historicoImbGroupsChart($r) {
        $sim_cm_color = '#A4A4A4';
        $sim_tv_color = 'black';
        $tv_color = '#002F8E';
        $cm_color = '#3260BC';
        // def
        $l = $r[0]['area'];
        $c_tv = array();
        $c_cm = array();
        $result = array();
        $c = 0;
        foreach ($r as $x) {
            if ($x['area'] !== $l) {
                $result['series'][] = array('name' => $l . " - TV", 'data' => $c_tv, 'color' =>
                    (($l !== 'SIM') ?
                            $this->tbase->areas['nome'][$this->tbase->area_abbr($l)]['color'] :
                            'black')
                );
                $result['series'][] = array('name' => $l . " - CM", 'data' => $c_cm, 'color' =>
                    (($l !== 'SIM') ?
                            $this->tbase->areas['nome'][$this->tbase->area_abbr($l)]['color'] :
                            'black')
                );
                $c_tv = array();
                $c_cm = array();
                $l = $x['area'];
                $c++;
            }
            $c_tv[] = array(
                        'name' => $x['area'] . ' - TV',
                        'y' => (($x['base_tv']) ? round(($x['x_tv'] / $x['base_tv'] * 100), 2) : 0)
            );
            $c_cm[] = array(
                        'name' => $x['area'] . ' - CM',
                        'y' => (($x['base_cm']) ? round(($x['x_cm'] / $x['base_cm'] * 100), 2) : 0)
            );
            if ($c === 0) {
                $result['categories'][] = date('M-Y', strtotime($x['m']));
            }
        }
        $result['series'][] = array('name' => "SIM - TV", 'data' => $c_tv, 'color' => $sim_tv_color, 'lineWidth' => 2);
        $result['series'][] = array('name' => "SIM - CM", 'data' => $c_cm, 'color' => $sim_cm_color, 'lineWidth' => 2);
        return $result;
    }

    function causaRankChart($causas) {
        $result = array(
                    'name' => 'Ranking de Causas',
                    'series' => array('name' => 'Reclamações', 'data' => array())
        );
        $total = 0;
        for ($i = 0; $i < count($causas); $i++) {
            $total += intval($causas[$i]['c']);
            if ($causas[$i]['CAUSA'])
                $result['series']['data'][] = array(
                    'name' => fCap($causas[$i]['CAUSA']),
                    'y' => intval($causas[$i]['c']),
                    'x' => $i + 1);
            else
                $result['series']['data'][] = array(
                    'name' => 'Não Preenchido',
                    'color' => '#6E6E6E',
                    'y' => intval($causas[$i]['c']),
                    'x' => $i + 1);
        }
        $result['sum'] = $total;
        return $result;
    }

    function motivoRankChart($motivos) {
        $result = array(
                    'name' => 'Ranking de Motivos',
                    'series' => array('name' => 'Instalaçoes', 'data' => array())
        );
        $total = 0;
        for ($i = 0; $i < count($motivos); $i++) {
            $total += intval($motivos[$i]['c']);
            $result['series']['data'][] = array(
                'name' => fCap($motivos[$i]['motivo']),
                'y' => intval($motivos[$i]['c']),
                'x' => $i + 1);
        }
        $result['sum'] = $total;
        return $result;
    }

    function imbProjChart($cid, $sim, $area) {
        $sim_cm_color = '#E4E4E4';
        $sim_tv_color = '#A4A4A4';
        $alert_color = '#BE0000';
        $std_color = '#00205F';
        $tv_color = '#002F8E';
        $cm_color = '#3260BC';
        $sum_area = ($cid !== 'SIM');

        $result['sim_proj'] = array('id' => 'sim', 'name' => 'Projeção Sim', 'data' => array(), 'lineWidth' => 2, 'color' => '#505050', 'dashStyle' => 'dash');
        if ($sum_area)
            $result['area_proj'] = array('id' => 'area', 'name' => 'Projeção IMB', 'data' => array(), 'lineWidth' => 2, 'color' => '#8593FF', 'dashStyle' => 'dash');
        $result['sim_part'] = array('id' => 'sim', 'name' => 'IMB Sim Parcial', 'data' => array(), 'lineWidth' => 2, 'color' => $sim_tv_color);
        if ($sum_area)
            $result['area_part'] = array('id' => 'area', 'name' => 'IMB Parcial', 'data' => array(), 'lineWidth' => 2, 'color' => '#8593FF');
        $result['meta'] = array('id' => 'meta', 'name' => 'Meta', 'data' => array(), 'lineWidth' => 2, 'color' => $alert_color);

        $window = array('sim' => array(), 'area' => array());
        $sim_base = 0;
        $sim_partial = 0;
        $area_partial = 0;
        $area_base = 0;
        $lmonth = -5;
        foreach ($sim as $k => $ss) {

            $d = strtotime($sim[$k]['dia']);
            $d_js = ($d + ( 60 * 60 * 12 )) * 1000;

            if (date('Y-m', $d) !== $lmonth) {
                $sim_partial = 0;
                $area_partial = 0;
                $lmonth = date('Y-m', $d);
            }

            if (count($window['sim']) === 30)
                array_shift($window['sim']);

            $sim_partial += intval($sim[$k]['manutencoes']);
            $window['sim'][] = intval($sim[$k]['manutencoes']);
            //$result['track'][] = $window;
            $sim_base = intval($sim[$k]['base']);

            if ($sim_base > 0) {
                $tmpy = round((array_sum($window['sim']) / $sim_base) * 100, 2);
                $tmpy_part = round(($sim_partial / $sim_base) * 100, 2);
            } else {
                $tmpy = 0;
                $tmpy_part = 0;
            }

            $result['sim_proj']['data'][] = array(
                'x' => $d_js,
                'y' => $tmpy
            );
            $result['sim_part']['data'][] = array(
                'x' => $d_js,
                'y' => $tmpy_part
            );

            $result['meta']['data'][] = array(
                'x' => $d_js,
                'y' => (($sum_area) ? floatval($area[$k]['meta']) : floatval($sim[$k]['meta']))
            );

            if ($sum_area) {
                if (count($window['area']) == 30)
                    array_shift($window['area']);
                $area_partial += intval($area[$k]['manutencoes']);
                $window['area'][] = intval($area[$k]['manutencoes']);
                $area_base = intval($area[$k]['base']);

                if ($area_base > 0) {
                    $tmpy = round((array_sum($window['area']) / $area_base) * 100, 2);
                    $tmpy_part = round(($area_partial / $area_base) * 100, 2);
                } else {
                    $tmpy = 0;
                    $tmpy_part = 0;
                }

                $result['area_proj']['data'][] = array(
                    'x' => $d_js,
                    'y' => $tmpy
                );
                $result['area_part']['data'][] = array(
                    'x' => $d_js,
                    'y' => $tmpy_part
                );
            }
        }
        //[data.area_proj, 
        //data.sim_proj, 
        //data.area_part, 
        //data.sim_part, 
        //data.meta]

        $x = array(
            'series' => array(
            )
        );
        foreach ($result as $tmp)
            $x['series'][] = $tmp;
        return $x;
    }

    function backlogDPChart($b) {
        $tv_color = '#002F8E';
        $cm_color = '#3260BC';
        $ars = $con->order_by('id')->get('area')->result_array();
        $r = array(
            'categories' => array(),
            'series' => array(
                array(
                    'name' => 'Analógico',
                    'data' => array(),
                    'color' => '#6F9DF8'
                ),
                array(
                    'name' => 'Cable Modem',
                    'data' => array(),
                    'color' => '#3260BC'
                ),
                array(
                    'name' => 'Digital',
                    'data' => array(),
                    'color' => '#002F8E'
                )
            )
        );

        foreach ($ars as $area) {
            $area['id'] = intval($area['id']);
            $areas[$area['id']] = array('total' => 0, 'p' => array(), 'name' => $area['name']);
        }
        foreach ($b as $x) {
            $aid = intval($x['aid']);
            $x['c'] = intval($x['c']);
            $areas[$aid]['total'] += $x['c'];
            if ($x['produto'][0] === 'a')
                $i = 0;
            if ($x['produto'][0] === 'c')
                $i = 1;
            if ($x['produto'][0] === 'd')
                $i = 2;
            $areas[$aid]['p'][$i] = array('y' => $x['c'], 'name' => $x['area']);
        }
        usort($areas, function($a, $b) {
            if ($a['total'] > $b['total'])
                return -1;
            elseif ($a['total'] < $b['total'])
                return 1;
            else
                return 0;
        });
        foreach ($areas as $area) {
            $r['categories'][] = $this->tbase->area_abbr($area['name']);
            for ($i = 0; $i < 3; $i++)
                if (array_key_exists($i, $area['p']))
                    $r['series'][$i]['data'][] = $area['p'][$i];
                else
                    $r['series'][$i]['data'][] = array('y' => 0, 'name' => $area['name']);
        }
        return $r;
    }

    function backlogChart($b) {
        $lstatus = -1;
        $i = -1;
        $aa = array();
        $result = array('categories' => array(), 'series' => array());
        $series = array();
        $hascorp = ((!$b) ? false : (array_key_exists('corp', $b[0])));
        foreach ($b as $x) {
            $a = $this->tbase->area_abbr($x['area']);
            $c = intval($x['c']);
            if ($hascorp)
                $corp = intval($x['corp']);

            if (!array_key_exists($a, $aa)) {
                $aa[$a] = 0;
            }

            $aa[$a] += $c;

            if ($x['status'] !== $lstatus) {
                $lstatus = $x['status'];
                $i++;
                $series[$i] = array('name' => $x['status'], 'data' => array(), 'color' => $x['color']);
            }

            $series[$i]['data'][$a] = array('y' => $c, 'name' => $x['area'], 'abbr' => $a);
            if ($hascorp)
                $series[$i]['data'][$a]['corp'] = $corp;
        }
        arsort($aa);
        $aux = 0;
        $result['categories'] = array();
        foreach ($series as $i => $s) {
            $result['series'][$aux] = array('name' => $s['name'], 'data' => array(), 'color' => $s['color']);
            foreach ($aa as $k => $x) {
                if ($aux === 0) {
                    $result['categories'][] = $series[$i]['data'][$k]['abbr'];
                }
                $result['series'][$aux]['data'][] = $s['data'][$k];
            }
            $aux++;
        }

        return $result;
    }

    function statusShareChart($sts) {
        $result = array(
                    'name' => 'Status das Ordens de Serviço',
                    'series' => array(
                        'type' => 'pie',
                        'name' => 'Status das Ordens de Serviço',
                        'data' => array())
        );
        for ($i = 0; $i < count($sts); $i++) {
            $result['series']['data'][] = array('color' => statusColor($sts[$i]['status']), 'name' => $sts[$i]['status'], 'y' => intval($sts[$i]['c']));
        }
        $result['series'] = array($result['series']);
        return $result;
    }

    function nodeRankChart($nodes) {
        $sim_cm_color = '#E4E4E4';
        $sim_tv_color = '#A4A4A4';
        $alert_color = '#BE0000';
        $std_color = '#00205F';
        $tv_color = '#002F8E';
        $cm_color = '#3260BC';

        $result = array('categories' => array(), 'series' =>
            array(
                'tv' => array('visible' => false, 'type' => 'column', 'name' => '#TV', 'color' => $tv_color, 'data' => array())
                , 'cm' => array('visible' => false, 'type' => 'column', 'name' => '#CM', 'color' => $cm_color, 'data' => array())
                , 'sum' => array('type' => 'column', 'name' => '#Total', 'color' => $std_color, 'data' => array())
            )
        );
        foreach ($nodes as $n) {
            if ($n['NODE'] !== '') {
                $result['series']['tv']['data'][] = array('y' => intval($n['manutencoes_tv']));
                $result['series']['cm']['data'][] = array('y' => intval($n['manutencoes_cm']));
                $result['series']['sum']['data'][] = array('y' => intval($n['manutencoes_tv']) + intval($n['manutencoes_cm']));
                $result['categories'][] = $n['NODE'];
            } else {
                $result['series']['tv']['data'][] = array('color' => "black", 'y' => intval($n['manutencoes_tv']));
                $result['series']['cm']['data'][] = array('color' => "black", 'y' => intval($n['manutencoes_cm']));
                $result['series']['sum']['data'][] = array('color' => "black", 'y' => intval($n['manutencoes_tv']) + intval($n['manutencoes_cm']));
                $result['categories'][] = 'SEM';
            }
        }
        $result['series'] = array(
            $result['series']['sum'],
            $result['series']['tv'],
            $result['series']['cm']
        );
        return $result;
    }

    function repairTimeLineChart($dias) {
        $sim_cm_color = '#E4E4E4';
        $sim_tv_color = '#A4A4A4';
        $alert_color = '#BE0000';
        $std_color = '#00205F';
        $tv_color = '#002F8E';
        $cm_color = '#3260BC';
        $i = 0;
        $d = date_create_from_format('Y-m-d', $dias[$i]['dia']);

        $min = 0;
        $max = 0;
        $sum = 0;

        while (date_format($d, 'Y-m-d') <= $dias[count($dias) - 1]['dia']) {
            $thidate = date_create_from_format('Y-m-d', $dias[$i]['dia']);
            if ($d != $thidate) {
                $result['data'][] = array(
                    'x' => mktime(0, 0, 0, $d->format('m'), $d->format('d'), $d->format('Y')) * 1000,
                    'y' => 0
                );
            } else {
                $result['data'][] = array(
                    'x' => mktime(0, 0, 0, $d->format('m'), $d->format('d'), $d->format('Y')) * 1000,
                    'y' => intval($dias[$i]['manutencoes'])
                );
                $i++;
            }
            $currindex = count($result['data']) - 1;
            if ($result['data'][$currindex]['y'] > $result['data'][$max]['y'])
                $max = $currindex;

            if ($result['data'][$currindex]['y'] < $result['data'][$min]['y'])
                $min = $currindex;

            $sum += $result['data'][$currindex]['y'];

            date_add($d, date_interval_create_from_date_string('1 day'));
        }

        $result['max'] = $result['data'][$max]['y'];
        $result['data'][$max]['dataLabels'] = array('enabled' => true, 'align' => 'right', 'rotation' => 30, 'x' => 8, 'y' => -22);
        $result['data'][$max]['marker'] = array('enabled' => true, 'fillColor' => 'red', 'lineColor' => 'black', 'lineWidth' => 1);
        $result['min'] = $result['data'][$min]['y'];
        $result['avg'] = round($sum / count($result['data']), 2);
        return $result;
    }

    function repairTimeLineGroupsChart($dias) {
        $sim_cm_color = '#E4E4E4';
        $sim_tv_color = '#A4A4A4';
        $alert_color = '#BE0000';
        $std_color = '#00205F';
        $tv_color = '#002F8E';
        $cm_color = '#3260BC';
        $i = 0;

        $result = array(
            'tv' => array('name' => 'Reclamações TV', 'color' => $tv_color, 'data' => array(), 'id' => 'tv', 'tooltip' => array('valueDecimals' => 0)),
            'cm' => array('name' => 'Reclamações CM', 'color' => $cm_color, 'data' => array(), 'id' => 'cm', 'tooltip' => array('valueDecimals' => 0))
        );

        $min['tv'] = 0;
        $min['cm'] = 0;
        $max['tv'] = 0;
        $max['cm'] = 0;
        for ($i = 0; $i < count($dias); $i++) {
            $d = date_create_from_format('Y-m-d', $dias[$i]['dia']);
            $result[$dias[$i]['xservice']]['data'][] = array(
                'x' => mktime(0, 0, 0, $d->format('m'), $d->format('d'), $d->format('Y')) * 1000,
                'y' => intval($dias[$i]['c'])
            );
            $index = count($result[$dias[$i]['xservice']]['data']) - 1;

            if (intval($dias[$i]['c']) < $result[$dias[$i]['xservice']]['data'][$min[$dias[$i]['xservice']]]['y'])
                $min[$dias[$i]['xservice']] = $index;
            if (intval($dias[$i]['c']) > $result[$dias[$i]['xservice']]['data'][$max[$dias[$i]['xservice']]]['y'])
                $max[$dias[$i]['xservice']] = $index;
        }

        $result['tv']['data'][$max['tv']]['dataLabels'] = array('enabled' => true, 'align' => 'right', 'rotation' => 30, 'x' => 8, 'y' => -22);
        $result['cm']['data'][$max['cm']]['dataLabels'] = array('enabled' => true, 'align' => 'right', 'rotation' => 30, 'x' => 8, 'y' => -22);

        $result['tv']['data'][$max['tv']]['marker'] = array('enabled' => true, 'fillColor' => 'red', 'lineColor' => 'black', 'lineWidth' => 1);
        $result['cm']['data'][$max['cm']]['marker'] = array('enabled' => true, 'fillColor' => 'red', 'lineColor' => 'black', 'lineWidth' => 1);
        $result['series'] = array(
                    $result['tv'],
                    $result['cm']
        );
        $result['name'] = 'Reclamações ingressadas por dia';
        unset($result['tv']);
        unset($result['cm']);
        return $result;
    }

    function nodeEvChart($nodes) {
        $result = array(
                    'name' => 'Eventos Massivos',
                    'series' =>
                    array(
                        array(
                            'name' => 'Eventos Massivos',
                            'type' => 'column',
                            'data' => array()
                        )
                    ),
                    'categories' => array()
        );
        if ($nodes)
            foreach ($nodes as $n) {
                $result['series'][0]['data'][] = array('y' => intval($n['a']));
                $result['categories'][] = $n['node'];
            }
        $result['status'] = 'success';
        return $result;
    }

    function nodeMonChart($nodes) {
        $result = array(
                    'name' => 'Alarmes por Node',
                    'series' =>
                    array(
                        array(
                            'name' => 'Alarmes na Monitoração',
                            'type' => 'column',
                            'data' => array()
                        )
                    ),
                    'categories' => array()
        );
        if ($nodes)
            foreach ($nodes as $n) {
                $result['series'][0]['data'][] = array('y' => intval($n['c']));
                $result['categories'][] = $n['node'];
            }
        $result['status'] = 'success';
        return $result;
    }

    function perEvChart($pers) {
        $result = array(
                    'name' => 'Eventos Massivos',
                    'series' =>
                    array(
                        array(
                            'name' => 'Eventos Massivos',
                            'type' => 'column',
                            'data' => array()
                        )
                    ),
                    'categories' => array()
        );
        if ($pers)
            foreach ($pers as $i => $n) {
                if ($n['name'] === 'SIM') {
                    $result['series'][0]['data'][] = array('y' => intval($n['c']), 'color' => '#A4A4A4', 'name' => $n['name']);
                } else {
                    $result['series'][0]['data'][] = array('y' => intval($n['c']), 'name' => $n['name']);
                }

                $result['categories'][] = $n['abbr'];
            }
        $result['status'] = 'success';
        return $result;
    }

    function perMonChart($pers) {
        $result = array(
                    'name' => 'Volume de Alarmes',
                    'series' =>
                    array(
                        array(
                            'name' => 'Alarmes na Monitoração',
                            'type' => 'column',
                            'data' => array()
                        )
                    ),
                    'categories' => array()
        );
        if ($pers)
            foreach ($pers as $i => $n) {
                if ($n['name'] === 'SIM') {
                    $result['series'][0]['data'][] = array('y' => intval($n['c']), 'color' => '#A4A4A4', 'name' => $n['name']);
                } else {
                    $result['series'][0]['data'][] = array('y' => intval($n['c']), 'name' => $n['name']);
                }

                $result['categories'][] = $n['abbr'];
            }
        $result['status'] = 'success';
        return $result;
    }

    function evTimeline($ds) {
        $result = array(
            'name' => 'Eventos por dia',
            'series' =>
            array(
                array('name' => 'Eventos', 'data' => array())
            )
        );
        foreach ($ds as $x => $y) {
            $result['series'][0]['data'][] = array('x' => (strtotime($y['d']) + ( 60 * 60 * 12 )) * 1000, 'y' => intval($y['c']));
        }
        return $result;
    }

    function monTimeLine($ds) {
        $result = array(
            'name' => 'Contagem de Nodes',
            'series' =>
            array(
                array('name' => 'Total de Nodes', 'data' => array(), 'color' => '#4C509B'),
                array('name' => 'Nodes Críticos', 'data' => array(), 'color' => '#BE2525', 'visible' => false),
                array('name' => 'Corporativos', 'data' => array(), 'color' => '#71A250', 'visible' => false),
                array('name' => 'Retorno RI', 'data' => array(), 'color' => '#696969', 'visible' => false),
                array('name' => 'Fora do SLA', 'data' => array(), 'color' => '#FF7700', 'visible' => false)
            )
        );
        foreach ($ds as $y) {
            $result['series'][0]['data'][] = array('x' => $y['tot']['x'] * 1000, 'y' => $y['tot']['y']);
            $result['series'][1]['data'][] = array('x' => $y['crit']['x'] * 1000, 'y' => $y['crit']['y']);
            $result['series'][2]['data'][] = array('x' => $y['corp']['x'] * 1000, 'y' => $y['corp']['y']);
            $result['series'][3]['data'][] = array('x' => $y['ri']['x'] * 1000, 'y' => $y['ri']['y']);
            $result['series'][4]['data'][] = array('x' => $y['sla']['x'] * 1000, 'y' => $y['sla']['y']);
        }
        return $result;
    }

    function imbChart($month = false, $l = false) {
        
//        $result = array('categories' => array(), 'series' => array());
        $result = array('categories' => array());
        $areas = $this->con->select('id,name')->order_by('id')->get('area')->result_array();
        $imb = array(array('reclamacoes' => 0, 'base' => 0, 'meta' => $this->tbase->sim_imb_meta, 'name' => 'SIM', 'color' => $this->sim_tv_color));

        foreach ($areas as $area) {
            $aid = intval($area['id']);
            $x = $this->tbase->imb($aid, $month);
            $x['name'] = $area['name'];

            $imb[0]['reclamacoes'] += $x['reclamacoes'];
            $imb[0]['base'] += $x['base'];

            if ($l) {
                $x['real'] = $x['imb'];
                $x['y'] = nozeropercent($this->timesInfinity($x['reclamacoes']), $x['base']);
            } else {
                $x['y'] = $x['imb'];
            }

            $imb[] = $x;
        }

        if ($l) {
            $imb[0]['real'] = nozeropercent($imb[0]['reclamacoes'], $imb[0]['base']);
            $imb[0]['y'] = $this->timesInfinity($imb[0]['real']);
        } else {
            $imb[0]['y'] = nozeropercent($imb[0]['reclamacoes'], $imb[0]['base']);
        }

        $result['categories'][] = "SIM";

        usort($imb, function($a, $b) {
            if ($a['name'] === 'SIM') {
                return -1;
            } elseif ($b['name'] === 'SIM') {
                return 1;
            } else {
                return (($a['y'] === $b['y']) ? 0 : ($a['y'] < $b['y']));
            }
        });
        $result['categories'] = array();
        foreach ($imb as $a) {
            $result['categories'][] = $this->tbase->area_abbr($a['name']);
        }

        $meta_imb = array_map(function($a) {return $a['meta'];}, $imb);

//        $result['series'][] = array('name' => 'IMB', 'type' => 'column', 'data' => $imb);
//        $result['series'][] = array('name' => 'Meta', 'type' => 'spline','marker' => array('enabled' => false),'data' => $meta_imb);
        
        $result['dados'] = $imb;
        $result['complemento'] = $meta_imb;
        
        return $result;
    }

    function imbMetaHist($cid, $m) {

        $result = array();
        $cm = $this->tbase->imb($cid, $m, 'cm');
        $tv = $this->tbase->imb($cid, $m, 'tv');
        $total = $this->tbase->imb($cid, $m);
        $result['total'] = $total;
        $result['tv'] = $tv;
        $result['cm'] = $cm;
        if (!$this->tbase->mes_fechado($m)) {
            $result['cm']['y'] = nozeropercent($this->timesInfinity($result['cm']['reclamacoes']), $result['cm']['base']);
            $result['tv']['y'] = nozeropercent($this->timesInfinity($result['tv']['reclamacoes']), $result['tv']['base']);
            $result['total']['y'] = nozeropercent($this->timesInfinity($result['total']['reclamacoes']), $result['total']['base']);
        } else {
            $result['total']['y'] = $total['imb'];
            $result['tv']['y'] = $tv['imb'];
            $result['cm']['y'] = $cm['imb'];
        }

        if ($m < '2012-09-01') {
            $result['meta_total'] = array('y' => null);
            $result['meta_tv'] = array('y' => null);
            $result['meta_cm'] = array('y' => null);
        } else {
            $result['meta_total'] = $total['meta'];
            $result['meta_tv'] = $tv['meta'];
            $result['meta_cm'] = $cm['meta'];
        }

        return $result;
    }

    function imbGroupsChart($month, $lmonth) {

        $sim = array();

        $alert_color = '#BE0000';
        $std_color = '#00205F';

        $sim_color['cm'] = '#E4E4E4';
        $sim_color['tv'] = '#A4A4A4';
        $color['tv'] = '#002F8E';
        $color['cm'] = '#3260BC';
        $areas = $con->select('id,name')->order_by('id')->get('area')->result_array();
        $areas = array_merge(array(array('id' => 0, 'name' => 'SIM')), $areas);
        $imb = array();
        foreach ($areas as $a) {
            $a['id'] = intval($a['id']);
            foreach (array('tv', 'cm') as $p) {
                $x = $this->tbase->imb($a['name'], $month, $p);
                if ($lmonth)
                    $x['imb'] = nozeropercent($this->timesInfinity($x['reclamacoes']), $x['base']);
                if ($p === 'tv')
                    $imb[$a['id']] = array('y' => 0, 'name' => $a['name']);
                $is_sim = (($a['id'] === 0) ? true : false);
                $imb[$a['id']]['y'] += $x['imb'];
                if ($x['imb'] > $x['meta']) {
                    //-----------INDICE ACIMA DA META
                    $aux = array(
                        'borderWidth' => 0,
                        'color' => (($is_sim) ? 'black' : $alert_color),
                        'name' => 'IMB',
                        'p' => 1,
                        'y' => abs($x['imb'] - $x['meta']),
                        'reclamacoes' => $x['reclamacoes'],
                        'base' => $x['base'],
                    );
                    if ($lmonth)
                        $aux['real'] = nozeropercent($x['reclamacoes'], $x['base']);


                    $imb[$a['id']][$p]['diff'] = $aux;

                    $imb[$a['id']][$p]['value'] = array(
                                'borderWidth' => 0,
                                'color' => (($is_sim) ? $sim_color[$p] : $color[$p]),
                                'name' => 'Meta',
                                'p' => 0,
                                'y' => $x['meta']
                    );
                }else {
                    //-----------INDICE ABAIXO DA META
                    $imb[$a['id']][$p]['diff'] = array(
                                'borderColor' => '#C8C8C8',
                                'dashStyle' => 'Dash',
                                'color' => 'rgba(255, 255, 255, 0.1)',
                                'name' => 'Meta',
                                'p' => 1,
                                'y' => abs($x['imb'] - $x['meta'])
                    );
                    $aux = array(
                        'borderWidth' => 0,
                        'color' => (($is_sim) ? $sim_color[$p] : $color[$p]),
                        'name' => 'IMB',
                        'p' => 0,
                        'y' => $x['imb'],
                        'reclamacoes' => $x['reclamacoes'],
                        'base' => $x['base']
                    );
                    if ($lmonth)
                        $aux['real'] = nozeropercent($x['reclamacoes'], $x['base']);
                    $imb[$a['id']][$p]['value'] = $aux;
                }
            }
        }
        usort($imb, function($a, $b) {
            if ($a['name'] === 'SIM') {
                return -1;
            } elseif ($b['name'] === 'SIM') {
                return 1;
            } else {
                return (($a['y'] === $b['y']) ? 0 : ($a['y'] < $b['y']));
            }
        }
        );

        $result['categories'] = array();
        foreach ($imb as $a) {
            $result['categories'][] = $this->tbase->area_abbr($a['name']);
        }

        foreach ($imb as $k => $x) {
            foreach (array('tv', 'cm') as $p) {
                $imb[$p]['diff'][] = $imb[$k][$p]['diff'];
                $imb[$p]['value'][] = $imb[$k][$p]['value'];
            }
            unset($imb[$k]);
        }
        //exit(json_encode($imb));
        $result['series'][] = array('name' => 'TV', 'stack' => 'TV', 'data' => $imb['tv']['diff']);
        $result['series'][] = array('name' => 'CM', 'stack' => 'CM', 'data' => $imb['cm']['diff']);

        $result['series'][] = array('name' => 'TV', 'stack' => 'TV', 'data' => $imb['tv']['value']);
        $result['series'][] = array('name' => 'CM', 'stack' => 'CM', 'data' => $imb['cm']['value']);
        return $result;
    }

    function imbHistChart($lines, $sim) {
        foreach ($sim as $line) {
            $lines[] = $line;
        }
        $areas = array();
        $c = 0;
        $m = array();
        foreach ($lines as $line) {
            $a = $this->tbase->area_abbr($line['aname']);
            $ptmes = ptmes($line['rmes']);
            if (!array_key_exists($a, $areas)) {
                $areas[$this->tbase->area_abbr($line['aname'])] = $c;
                if ($line['aname'] == 'SIM')
                    $series[] = array('name' => $a, 'data' => array(), 'lineWidth' => 5, 'color' => 'black');
                else
                    $series[] = array('name' => $a, 'data' => array(), 'color' => $this->tbase->areas['nome'][$this->tbase->area_abbr($line['aname'])]['color']);
                $c++;
            }
            if (!in_array($ptmes, $m))
                $m[] = $ptmes;
            if (intval($line['base']) === 0) {
                $series[$areas[$a]]['data'][] = null;
            } elseif (!$this->tbase->mes_fechado($line['rmes'])) {
                $series[$areas[$a]]['data'][] = array('y' => round(($this->timesInfinity($line['manutencoes']) / intval($line['base'])) * 100, 2),
                    'real' => round((intval($line['manutencoes']) / intval($line['base'])) * 100, 2));
            } else {
                $series[$areas[$a]]['data'][] = round((intval($line['manutencoes']) / intval($line['base'])) * 100, 2);
            }
        }
        $result['series'] = $series;
        $result['categories'] = $m;
        return $result;
    }

    function timesInfinity($manut) {
        $manut = floatval($manut);
        $lday = intval(date('j', strtotime($this->tbase->lday)));
        $ldaym = intval(date('t', strtotime($this->tbase->currmonth)));
        if ($lday)
            return fFloat($manut * ($ldaym / $lday));
        else
            return fFloat($manut);
    }

    function revisitaPorMes($mes = false) {
        $result = array('categories' => array(), 'series' => array());
        $rev = array('name' => 'Índice de Revisita', 'data' => array());
        $rev['data'][] = array(
                    'y' => 0,
                    'total' => 0,
                    'nope' => 0,
                    'name' => 'SIM',
                    'color' => '#A4A4A4',
                    'meta' => $this->tbase->sim_irm_meta
        );
        $mt = array('data' => array());
        $mt['name'] = 'Meta IRM';
        $mt['type'] = 'spline';
        $mt['color'] = '#BE0000';
        $mt['lineWidth'] = 2;
        $mt['marker']['enabled'] = false;

        $areas = $con->order_by('name')->get("area")->result_array();
        foreach ($areas as $x) {
            $a = $this->tbase->revisita($mes, $x['id']);
            $a_m = $this->tbase->mmeta('meta_irm', $a['name'], $mes);

            $rev['data'][0]['total'] += intval($a['total']);
            $rev['data'][0]['nope'] += intval($a['nope']);
            $rev['data'][] = array(
                'id' => intval($a['id']),
                'y' => fFloat($a['irm']),
                'total' => intval($a['total']),
                'nope' => intval($a['nope']),
                'name' => $a['name'],
                'meta' => $a_m
            );
        }
        $rev['data'][0]['y'] = nozeropercent($rev['data'][0]['nope'], $rev['data'][0]['total']);

        usort($rev['data'], function($a, $b) {
            if ($a['name'] === 'SIM') {
                return -1;
            } elseif ($b['name'] === 'SIM') {
                return 1;
            } else {
                return (($a['y'] === $b['y']) ? 0 : ($a['y'] < $b['y']));
            }
        });
        $result['categories'] = array();
        foreach ($rev['data'] as $a) {
            $result['categories'][] = $this->tbase->area_abbr($a['name']);
        }

        $mt['data'] = array_map(
                function($a) {
            return array('y' => $a['meta'], 'name' => $a['name']);
        }, $rev['data']
        );

        $result['series'][] = $rev;
        $result['series'][] = $mt;

        return $result;
    }

    function tecProdArea($tec, $mes) {
        if (strtolower($mes) === 'total')
            $mes = false;
        $qds = array(
                    array('name' => 'QI:  0   |   0%', 'marker' => array('symbol' => 'circle'), 'data' => array()),
                    array('name' => 'QII: 0   |   0%', 'marker' => array('symbol' => 'circle'), 'data' => array()),
                    array('name' => 'QIII:	0   |   0%', 'marker' => array('symbol' => 'circle'), 'data' => array()),
                    array('name' => 'QIV: 0   |   0%', 'marker' => array('symbol' => 'circle'), 'data' => array())
        );
        $meta = array('x' => 50, 'y' => 10);
        $range = array(
                    'x' => array('min' => 0, 'max' => 100),
                    'y' => array('min' => 0, 'max' => 20)
        );
        $c = 0;
        if ($tec)
            foreach ($tec as $i => $t) {
                unset($tec[$i]['ainfo']);
                if (!(in_array($t['name'], $this->tbase->nontecs) || !$t['name'])) {
                    if ($c === 0) {
                        if ($mes)
                            $time = date('Y-m', strtotime($mes)) . '-' . date('t', strtotime($mes));
                        else
                            $time = date('Y-m-d');
                        $a = $this->tbase->busca_area_metas($t['item']['area'], $time);
                        $m_quali = floatval($a['meta_qualidade']);
                        $m_prod = floatval($a['meta_producao']);
                        $meta = array('x' => $m_quali, 'y' => $m_prod);
                        $range = array(
                                    'x' => array('min' => intval($m_quali * 0.8), 'max' => 100),
                                    'y' => array('min' => 0, 'max' => $m_prod * 2)
                        );
                        $c++;
                    }

                    $range['x']['max'] = max(array($t['x'], $range['x']['max']));
                    $range['y']['max'] = max(array($t['y'], $range['y']['max']));

                    $range['x']['min'] = min(array($t['x'], $range['x']['min']));
                    $range['y']['min'] = min(array($t['y'], $range['y']['min']));

                    if ($tec[$i]['x'] >= $meta['x'] && $tec[$i]['y'] >= $meta['y']) {
                        $qds[0]['data'][] = $tec[$i];
                        $qds[0]['name'] = "QI:  " . calc_q(count($qds[0]['data']), count($tec)) . "%";
                    } else if ($tec[$i]['x'] >= $meta['x'] && $tec[$i]['y'] < $meta['y']) {
                        $qds[1]['data'][] = $tec[$i];
                        $qds[1]['name'] = "QII: " . calc_q(count($qds[1]['data']), count($tec)) . "%";
                    } else if ($tec[$i]['x'] < $meta['x'] && $tec[$i]['y'] >= $meta['y']) {
                        $qds[2]['data'][] = $tec[$i];
                        $qds[2]['name'] = "QIII:	" . calc_q(count($qds[2]['data']), count($tec)) . "%";
                    } else if ($tec[$i]['x'] < $meta['x'] && $tec[$i]['y'] < $meta['y']) {
                        $qds[3]['data'][] = $tec[$i];
                        $qds[3]['name'] = "QIV: " . calc_q(count($qds[3]['data']), count($tec)) . "%";
                    }
                }
            }
        return array(
            'name' => 'Produção x Qualidade',
            'series' => $qds,
            'range' => $range,
            'meta' => $meta
        );
    }

    function tecProdSim($tec) {
        //exit(json_encode($tec));
        $areas = array();
        $meta = array('x' => $this->tbase->sim_meta_qualidade, 'y' => $this->tbase->sim_meta_producao);
        $range = array(
                    'x' => array('min' => intval($meta['x'] * 0.8), 'max' => 100),
                    'y' => array('min' => 0, 'max' => $meta['y'] * 2)
        );
        if ($tec) {
            $areas[] = array(
                        'name' => 'SIM',
                        'keyword' => 'tec_prod',
                        'marker' => array('symbol' => 'circle'),
                        'color' => 'black',
                        'data' =>
                        array(
                            array(
                                'name' => 'SIM',
                                'id' => 0,
                                'vts' => 0,
                                'xvts' => 0,
                                'y' => 0,
                                'x' => 0,
                                'tecs' => 0,
                                'ds' => 0
                            )
                        )
            );
            foreach ($tec as $i => $t) {
                $aid = intval($t['item']['area']);
                if (!array_key_exists($aid, $areas)) {
                    $areas[$t['item']['area']] = array(
                                'name' => $t['ainfo']['name'],
                                'keyword' => 'tec_prod',
                                'marker' =>
                                array('symbol' => 'circle'),
                                'color' => $t['ainfo']['color'],
                                'data' =>
                                array(
                                    array(
                                        'name' => $t['ainfo']['name'],
                                        'id' => $aid,
                                        'vts' => 0,
                                        'xvts' => 0,
                                        'y' => 0,
                                        'x' => 0,
                                        'tecs' => 0,
                                        'ds' => 0
                                    )
                                )
                    );
                }

                $areas[0]['data'][0]['vts'] += $t['vts'];
                $areas[0]['data'][0]['xvts'] += $t['xvts'];


                $areas[$aid]['data'][0]['vts'] += $t['vts'];
                $areas[$aid]['data'][0]['xvts'] += $t['xvts'];


                if (!(in_array($t['name'], $this->tbase->nontecs) || !$t['name'])) {
                    $areas[0]['data'][0]['tecs'] ++;
                    $areas[$aid]['data'][0]['tecs'] ++;
                    $areas[$aid]['data'][0]['ds'] += $t['ds'];
                    $areas[0]['data'][0]['ds'] += $t['ds'];
                }
            }
        }
        for ($i = 0; $i < count($areas); $i++) {
            $a = array(
                        'x' =>
                        (($areas[$i]['data'][0]['vts'] > 0) ?
                                round((1 - ( $areas[$i]['data'][0]['xvts'] / $areas[$i]['data'][0]['vts'] ) ) * 100, 2) : 0
                        ),
                        'y' =>
                        (($areas[$i]['data'][0]['ds'] > 0) ? round($areas[$i]['data'][0]['vts'] / $areas[$i]['data'][0]['ds'], 2) : 0
                        ),
                        'ds' =>
                        (($areas[$i]['data'][0]['tecs'] > 0) ? round($areas[$i]['data'][0]['ds'] / $areas[$i]['data'][0]['tecs'], 2) : 0
                        )
            );
            $areas[$i]['data'][0]['x'] = $a['x'];
            $areas[$i]['data'][0]['y'] = $a['y'];
            $areas[$i]['data'][0]['ds'] = $a['ds'];
            $range['x']['max'] = max(array($a['x'], $range['x']['max']));
            $range['y']['max'] = max(array($a['y'], $range['y']['max']));

            $range['x']['min'] = min(array($a['x'], $range['x']['min']));
            $range['y']['min'] = min(array($a['y'], $range['y']['min']));
        }
        return array('name' => 'Produção x Qualidade', 'series' => $areas, 'range' => $range, 'meta' => $meta);
    }

    function tecProdTec($m) {
        if ($m) {
            if (intval($m['total']))
                $series = array(
                            'name' => date('M-Y', strtotime($m['mes'] . '-01')),
                            'id' => date('Y-m', strtotime($m['mes'] . '-01')),
                            'marker' =>
                            array('symbol' => 'circle'),
                            'data' =>
                            array(
                                array(
                                    'vts' => intval($m['total']),
                                    'xvts' => intval($m['nope']),
                                    'x' => round((1 - floatval($m['perc'])) * 100, 2),
                                    'y' => round(intval($m['total']) / intval($m['ds']), 2),
                                    'ds' => intval($m['ds']),
                                )
                            )
                );
            else
                $series = false;
        }else {
            $series = false;
        }
        return array(/* 'name'=>'Produção x Qualidade','range'=>$range,'meta'=>$meta, */'series' => $series);
    }

    function vendInstTimeline($x) {
        $result = array(
                    'series' =>
                    array(
                        array('name' => 'Vendas', 'color' => '#7675B8', 'data' => array()),
                        array('name' => 'Instalações', 'color' => '#558B55', 'data' => array())
                    ),
                    'name' => 'Histórico Venda/Instalação'
        );
        if (!$x)
            $x = array();
        foreach ($x as $y) {
            $result['series'][0]['data'][] = array('x' => (strtotime($y['d']) + ( 60 * 60 * 12 )) * 1000, 'y' => intval($y['v']));
            $result['series'][1]['data'][] = array('x' => (strtotime($y['d']) + ( 60 * 60 * 12 )) * 1000, 'y' => intval($y['i']));
        }
        return $result;
    }

    function vendInst($x) {
        $result = array(
                    'series' =>
                    array(
                        array(
                            'name' => 'Vendas',
                            'color' => '#7675B8',
                            'data' =>
                            array(
                                array(
                                    'name' => 'SIM',
                                    'color' => 'black',
                                    'y' => 0
                                )
                            )
                        ),
                        array(
                            'name' => 'Instalações',
                            'color' => '#558B55',
                            'data' =>
                            array(
                                array(
                                    'name' => 'SIM',
                                    'color' => '#A4A4A4',
                                    'y' => 0
                                )
                            )
                        )
                    ),
                    'name' => 'Venda/Instalação',
                    'categories' => array('SIM')
        );
        $areas = array();
        foreach ($x as $y) {
            $aid = intval($y['aid']);
            if (!array_key_exists($aid, $areas)) {
                $result['series'][0]['data'][] = array('name' => $y['area'], 'y' => 0);
                $result['series'][1]['data'][] = array('name' => $y['area'], 'y' => 0);
                $areas[$aid] = count($result['series'][1]['data']) - 1;
                $result['categories'][] = $this->tbase->area_abbr($y['area']);
            }
            $result['series'][0]['data'][$areas[$aid]]['y'] += intval($y['v']);
            $result['series'][1]['data'][$areas[$aid]]['y'] += intval($y['i']);

            $result['series'][0]['data'][0]['y'] += intval($y['v']);
            $result['series'][1]['data'][0]['y'] += intval($y['i']);
        }
        return $result;
    }

    function vendInstPie($x) {
        $result = array(
                    'series' =>
                    array(
                        array(
                            'name' => 'Vendas',
                            'color' => '#7675B8',
                            'data' =>
                            array(
                                array(
                                    'name' => 'SIM',
                                    'color' => 'black',
                                    'y' => 0
                                )
                            )
                        ),
                        array(
                            'name' => 'Instalações',
                            'color' => '#558B55',
                            'data' =>
                            array(
                                array(
                                    'name' => 'SIM',
                                    'color' => '#A4A4A4',
                                    'y' => 0
                                )
                            )
                        )
                    ),
                    'name' => 'Venda/Instalação',
                    'categories' => array('SIM')
        );
        $areas = array();
        foreach ($x as $y) {
            $aid = intval($y['aid']);
            if (!array_key_exists($aid, $areas)) {
                $result['series'][0]['data'][] = array('name' => $y['area'], 'y' => 0);
                $result['series'][1]['data'][] = array('name' => $y['area'], 'y' => 0);
                $areas[$aid] = count($result['series'][1]['data']) - 1;
                $result['categories'][] = $this->tbase->area_abbr($y['area']);
            }
            $result['series'][0]['data'][$areas[$aid]]['y'] += intval($y['v']);
            $result['series'][1]['data'][$areas[$aid]]['y'] += intval($y['i']);

            $result['series'][0]['data'][0]['y'] += intval($y['v']);
            $result['series'][1]['data'][0]['y'] += intval($y['i']);
        }
        return $result;
    }

    function vendInstFI($x) {
        $result = array(
                    'name' => 'Forma de Ingresso',
                    'series' => array('name' => 'Vendas', 'data' => array())
        );
        $total = 0;
        for ($i = 0; $i < count($x); $i++) {
            $total += intval($x[$i]['c']);
            $result['series']['data'][] = array(
                'name' => fCap($x[$i]['f']),
                'y' => intval($x[$i]['c']),
                'x' => $i + 1);
        }
        $result['sum'] = $total;
        return $result;
    }

    function vendInstTA($x) {
        $result = array(
                    'name' => 'Tipo de Assinante',
                    'series' =>
                    array(
                        array('name' => 'Vendas', 'data' => array())
                    )
        );
        for ($i = 0; $i < count($x); $i++) {
            $result['series'][0]['data'][] = array(
                'name' => fCap($x[$i]['t'])/* .": ".intval($x[$i]['c']) */,
                'y' => intval($x[$i]['c']),
                'x' => $i + 1);
        }
        return $result;
    }

    function vendInstBase($x) {
        $tv_color = '#002F8E';
        $cm_color = '#3260BC';
        $combo_color = '#9796DA';
        $result = array(
                    'name' => 'Evolução da Base',
                    'series' =>
                    array(
                        /* array('name' => 'Base Total','data'=>array()), */
                        array('name' => 'Base Combo', 'data' => array(), 'color' => $combo_color),
                        array('name' => 'Base CM', 'data' => array(), 'color' => $cm_color),
                        array('name' => 'Base TV', 'data' => array(), 'color' => $tv_color)
                    ),
                    'categories' => array()
        );
        for ($i = 0; $i < count($x); $i++) {
            //$result['series'][0]['data'][] =  array('y' => intval($x[$i]['base']));
            $result['series'][0]['data'][] = array('y' => intval($x[$i]['combo']));
            $result['series'][1]['data'][] = array('y' => intval($x[$i]['cm']));
            $result['series'][2]['data'][] = array('y' => intval($x[$i]['tv']));
            $m = date('M-Y', strtotime($x[$i]['month']));
            $result['categories'][] = $m;
        }
        return $result;
    }

    function st_realtime($st) {

        $area = array();
        $x = array(
                /* array('corp' => 0,'name' => 'SIM','y' => 0) */
        );
        if ($st === 'P') {
            $l['corp'] = $this->tbase->backlog_pendente(true);
            //$x[0]['corp'] = count($l['corp']);
            $l['ind'] = $this->tbase->backlog_pendente(false);
            //$x[0]['y'] = count($l['corp']) + count($l['ind']);
        } else {
            $l['corp'] = $this->tbase->backlog_status($st, true);
            //$x[0]['corp'] = count($l['corp']);
            $l['ind'] = $this->tbase->backlog_status($st, false);
            //$x[0]['y'] = count($l['corp']) + count($l['ind']);
        }
        $p = $con->
                        select('per.area,per.id as per,area.name')->
                        join('area', 'per.area = area.id')->
                        order_by('area')->
                        get('per')->result_array();

        foreach ($p as $aux) {
            $per = intval($aux['per']);
            $area[$per] = array(
                        'id' => intval($aux['area']) - 1,
                        'name' => $aux['name']
            );
            $x[$area[$per]['id']] = array('y' => 0, 'corp' => 0, 'name' => $area[$per]['name']);
        }
        foreach ($l as $k => $i)
            foreach ($i as $j) {
                $per = intval($j['per']);
                $x[$area[$per]['id']]['y'] ++;
                if ($k === 'corp')
                    $x[$area[$per]['id']]['corp'] ++;
            }
        return $x;
    }

    function iar_parse($ages, $ordens) {

        $findInAges = function($ages, $diff) {
            foreach ($ages as $i => $age)
                if ($diff > $age['from'] && (!array_key_exists('to', $age) || $diff <= $age['to'] ))
                    return $i;
            return null;
        };
        $total = 0;
        $series = array();
        for ($i = 0; $i < count($ages); $i++)
            $series[] = array('y' => 0, 'os' => array(), 'stack' => 0);
        foreach ($ordens as $ordem) {

            $ingresso = strtotime($ordem['ingresso']);
            $cumpr = strtotime($ordem['real_ini']);

            $diff = $cumpr - $ingresso;
            $diffH = intval($diff / (60 * 60));

            $ordemAge = $findInAges($ages, $diffH);
            if ($ordemAge !== null) {
                $series[$ordemAge]['y'] ++;

                $series[$ordemAge]['os'][] = array(
                            'os' => $ordem['os'],
                            'per' => $ordem['per'],
                            'svc' => $ordem['svc'],
                            'ingr' => $ingresso,
                            'agenda' =>
                            (($ordem['min_ini']) ? date('d/m/Y', strtotime(substr($ordem['min_ini'], 0, 10)))
                                    . ' - ' .
                                    substr($ordem['min_ini'], 11, 2) . 'h às ' .
                                    substr($ordem['max_ini'], 11, 2) . 'h' : '---'
                            ),
                            'cumpr' => date('d/m/y H:i:s', $cumpr)
                );

                for ($l = 0; $l <= $ordemAge; $l++)
                    $series[$l]['stack'] ++;
                $total++;
            }
        }
        foreach ($series as $i => $serie) {
            $series[$i]['stackPercent'] = nozeropercent($serie['stack'], $total);
        }
        return $series;
    }

    function cst_meta_val($meta, $area = null, $time = null) {

        if (!$area)
            $area = null;

        $con->where('meta', $meta);
        $con->where('area', $area);

        if ($time)
            $con->where('ini <=', ( ( strlen($time) >= 10 ) ? $time : $time . '-01'));

        $x = $con->order_by('ini DESC, id DESC')->limit(1)->get('cst_meta')->row_array();
        return (($x) ? floatval($x['value']) : null);
    }

    function cst_cluster_meta_val($meta, $area = null, $time = null) {

        if (!$area) {
            $area = null;
        }
        $con->where('meta', $meta);
        $con->where('cst_cluster', $area);

        if ($time)
            $con->where('ini <=', ( ( strlen($time) >= 10 ) ? $time : $time . '-01'));

        $x = $con->order_by('ini DESC, id DESC')->limit(1)->get('cst_cluster_meta')->row_array();
        return (($x) ? floatval($x['value']) : null);
    }

}

?>
