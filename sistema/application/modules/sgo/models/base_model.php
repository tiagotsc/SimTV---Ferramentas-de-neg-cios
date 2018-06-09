<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class base_model extends CI_Model {
	public
		$sys_args,
		$month_closed,
		$acomp_fid_sla,
		$acomp_solved_expiration,
		$currmonth,
		$currmonth2,
		$sim_imb_meta_cm,
		$sim_imb_meta_tv,
		$sim_imb_meta,
		$sim_irm_meta,
		$sim_meta_producao,
		$sim_meta_qualidade,
		$sim_meta_cad_inst_diff,
		$acomp_ri_sla,
		$mon_days,
		$intervalo_corrente,
		$myareas = false,
		$goodguy,
		$lday,
		$areas = array(),
		$supsiga = null,
		$intervalo_revisita,
		$begin = '2011-01-01',
		$dia_d = '2011-12-01',
		$user = false,
		$nontecs = array(
			'CST',
			'ATP RECIFE',
			'ATP',
			'CIT / ARACAJU',
			'CONTAX',
			'CST',
			'FEIRA DE SANTANA',
			'GRAVATAI',
			'HEADEND',
			'LABORATÓRIO',
			'MIGRA',
			'RETENÇÃO',
			'RI RECIFE',
			'SALVADOR - CABO',
			'TECNICO TVC',
			'TÉCNICOS TVC JUIZ DE FORA'
		),$miss = 0, $ntitle;
	function __construct() {
		parent::__construct();
		$is_cli = $this->input->is_cli_request();

		if (!$is_cli) $this->load->library('session');

		$this->mountUserObj();

		if ($this->input->is_ajax_request()) {
			$this->output->set_content_type('application/json');
		}
		
		if (!$is_cli) $this->basicAuth();

		$this->_dash_utils();
		
	}

	function mountUserObj(){

		if(!$this->input->is_cli_request() && $this->session->userdata('login')){
			$login = $this->session->userdata('login');
			$this->user = $this->db->select('num_id,permissions,home')->get_where('user',array('login' => $login))->row_array();
			if( !$this->user ){
				$this->session->sess_destroy();
				redirect('login');
			}
			$this->user['login'] = $login;
			$this->user['prefs'] = $this->user_prefs();
		}else{
			$this->user = array('permissions' => null,'home' => null);
			$this->user['login'] = null;
		}
	}
	function basicAuth(){
		$page = $this->db->where('name', $this->router->class)->get('page')->row_array();
		if($page){
			$this->ntitle = $page['descr'];
			switch ($page['auth_mode']){
				case 'auto':
					$page_perms = $this->db->where('page',$this->router->class)->get('page_perms')->result_array();
					$e = 0;
					foreach($page_perms as $p){
						if( !$this->check_perms($p['perms']) ){
							$e++;
						}
					}
					if( $e > 0 && $e === count($page_perms) ){
						kickuser($this->input->is_ajax_request());
					}
					break;
				case 'custom':
					//autenticação no controlador
					break;
				case 'none':
					//aberto a todos
					break;
				case 'login':
					$this->check_login();
					break;
			}
		}/*else{
			$this->check_login();
		}*/
	}
	function _dash_utils(){
		$dd = date_sub(new DateTime(),  date_interval_create_from_date_string('12 months'));
		$this->dia_d = $dd->format('Y-m') .'-01';
		$sys = $this->db->select('name,value')->get('system_args')->result_array();
		$args = array();
		
		if($sys){
			foreach($sys as $x){
				$args[$x['name']] = $x['value'];
			}
		}
		
		$this->sys_args = $args;
		
		if(!array_key_exists('chat_ok',$this->sys_args)){
			$this->sys_args['chat_ok'] = false;
		}else{
			$this->sys_args['chat_ok'] = def_bool(intval($this->sys_args['chat_ok']));
		}
		
		if($this->router->class === 'dashboard' 
			|| $this->router->class === 'adm' 
				|| $this->input->is_cli_request()
		){
			
			$lm = $this->db->order_by('m','desc')->limit(1)->get('mes')->row_array();
			$this->currmonth = $lm['mm'];
			$this->currmonth2 = $lm['m'];
			$this->sim_imb_meta_cm = fFloat($args['sim_imb_meta_cm']);
			$this->sim_imb_meta_tv = fFloat($args['sim_imb_meta_tv']);
			$this->sim_imb_meta = fFloat($args['sim_imb_meta']);
			$this->sim_irm_meta = fFloat($args['sim_irm_meta']);
			$this->sim_meta_producao = fFloat($args['sim_meta_producao']);
			$this->sim_meta_qualidade = fFloat($args['sim_meta_qualidade']);
			$this->sim_meta_cad_inst_diff = fFloat($args['sim_meta_cad_inst_diff']);
			$this->acomp_ri_sla = intval($args['acomp_ri_sla']);
			$this->acomp_solved_expiration = intval($args['acomp_solved_expiration']);
			$this->acomp_fid_sla = intval($args['acomp_fid_sla']);
			$this->mon_days = intval($args['mon_days']);
			$this->intervalo_corrente = intval($args['intervalo_corrente']);
			$this->intervalo_revisita = intval($args['intervalo_revisita']);
			$this->myareas = $this->areas_coordenadas_por_usuario($this->user['login']);
			$this->goodguy = $this->usuario_fidelizacao($this->user['login']);
			$this->month_closed = $this->mes_fechado();
			$this->lday = $this->dia_da_ultima_reclamacao();
			$a = $this->db->order_by('id','asc')->get('area')->result_array();
			
			foreach($a as $b){
				$this->areas['id'][$b['id']] = $b;
				$this->areas['nome'][$this->area_abbr($b['name'])] = $b;
			}
			
		}
	}
	function user_prefs(){
		$prefz = array();
		$prefs = $this->db->where('user',$this->user['login'])->get('user_pref')->result_array();
		foreach($prefs as $p)
			$prefz[$p['pref']] = json_decode($p['value'],true);
		if(!$prefz)
			$prefz = null;
		return $prefz;
	}
	function load_amax_vt(){
		foreach ($this->areas['id'] as $a){
			if(!array_key_exists('max_vts',$a)){
				$mx_a = $this->maximo_de_vts_por_assinante($a['id']);
				$this->areas['id'][$a['id']] = array_merge($a,array('max_vts'=>$mx_a));
				$this->areas['nome'][$this->area_abbr($a['name'])] = array_merge($a,array('max_vts'=>$mx_a));
			}
		}
	}
	function maximo_de_vts_por_assinante($area){
		$date = new DateTime();
		date_sub($date,date_interval_create_from_date_string("{$this->intervalo_corrente} days"));
		$d1 = $date->format('Y-m-d');

		$magic =
			"SELECT
				COUNT(*) as c
			FROM (`pita`)
			JOIN `per` ON `per`.`id` = `pita`.`PER`
			LEFT JOIN repair_ack 
				ON (repair_ack.os = pita.NRO_OS 
					AND repair_ack.per = pita.PER 
						AND RIGHT(pita.SERVICO,2) = repair_ack.svc)
			LEFT JOIN acomp ON (pita.NUM_ASS = acomp.ass AND pita.PER = acomp.per AND acomp.status < 2)
			WHERE
				repair_ack.os IS NULL
				AND acomp.id IS null
				AND  `pita`.`DT_INGR`  >= '{$d1}'
				AND per.area = {$area}
			GROUP BY `pita`.`NUM_ASS`, `pita`.`PER`
			ORDER BY `c` DESC
			LIMIT 1";
		$m = $this->db->query($magic)->row_array();
		if($m && intval($m['c'])>1)
			return intval($m['c']);
		else
			return 2;
	}
	function notificar_fidelizacao($update){
		$a = $this->db->get_where('acomp',array('id'=>$update['parent']));
		if($a){
			$a = $a->row_array();
			$ass = $this->db->
					select('assinante.nome,
						area.id as areaID,
						area.name as area,
						assinante.cod as ass,
						assinante.per')->
					join('per','per.id = assinante.per')->
					join('area','area.id = per.area')->
					where('assinante.cod',$a['ass'])->
					where('assinante.per',$a['per'])->
					get('assinante')->row_array();
			$ppl = $this->user_model->list_level_fid_users(1);
			if($ass && $ppl){
				$this->load->model('email_model');
				$this->email_model->fid_notification($ass,$ppl);
			}
		}
	}
	function notificacoes_de_acompanhamento(){
		$acomps =
		$this->db->query(
		"SELECT
			acomp.ass as ass,
			acomp.per as per,
			acomp.stage as stage,
			acomp.status as status,
			area.name as area,
			assinante.nome,
			assinante.node as node,
			acomp_update.id as lupdate,
			acomp_update.schedule as schedule,
			acomp_update.date as date
		FROM acomp
		JOIN (select * from acomp_update order by id desc) acomp_update ON acomp_update.parent = acomp.id
		JOIN per ON per.id = acomp.per
		JOIN area ON area.id = per.area
		JOIN assinante ON (assinante.cod = acomp.ass AND assinante.per = acomp.per)
		WHERE
			(acomp.status >= 0 AND acomp.status < 2)
		GROUP BY acomp_update.parent")
		->result_array();
		foreach($acomps as $a){
			if(intval($a['stage']) === 1){
				if($a['schedule'])
					$since = new DateTime($a['schedule']);
				else
					$since = new DateTime($a['date']);
				$now = new DateTime();
				$diff = $now->diff($since,TRUE);
				$ndays = $diff->days;
				if(intval($a['status']) === 0)
					$times_expired = 0;
				else
					$times_expired = intval($ndays/$this->acomp_ri_sla);
				$to_list = $this->user_model->find_per_coords($a['per'],$times_expired+1);
				$m = $this->user_model->max_level_per_area($a['per']);
				if($times_expired > intval($m['level'])){
					$tt = $this->user_model->list_global_coord($times_expired - $m['level']);
					foreach($tt as $i => $t){
						$tt[$i]['spec'] = true;
					}
					$to_list = array_merge($to_list,$tt);
				}
			}else if(intval($a['stage']) === 2){
				$since = new DateTime($a['date']);
				$now = new DateTime();
				$diff = $now->diff($since,TRUE);
				$ndays = $diff->days;
				$times_expired = intval($ndays/$this->acomp_fid_sla);
				if($times_expired > 0)
					$to_list = $this->user_model->list_fid(2);
				else
					$to_list = $this->user_model->list_fid(1);
				if($times_expired > 1){
					$tt = $this->user_model->list_global_coord($times_expired-1);
					foreach($tt as $i => $t){
						$tt[$i]['spec'] = true;
					}
					$to_list = array_merge($to_list,$tt);
				}
			}
			if($a && $to_list){
				$this->email_model->acomp_notification($a,$to_list);
			}
		}
	}
	function notificacoes_de_assinante(){
		$asses = $this->assinantes_criticos_por_area();
		foreach($asses as $a){
			$notif = $this->db->order_by('date','asc')->limit(1)->get_where('ass_notif',array('ass'=>$a['ass'],'per'=>$a['per']))->row_array();

			$times_expired = 0;
			$ndays = 0;

			if($notif){
				$now = new DateTime();
				$since = new DateTime($notif['date']);
				$diff = $now->diff($since,TRUE);
				$ndays = $diff->days;
				$times_expired = intval($ndays/$this->acomp_ri_sla);
			}

			$to_list = $this->user_model->find_per_coords($a['per'],$times_expired+1);
			$m = $this->user_model->max_level_per_area($a['per']);
			if($times_expired > intval($m['level'])){
				$tt = $this->user_model->list_global_coord($times_expired - $m['level']);
				foreach($tt as $i => $t){
					$tt[$i]['spec'] = true;
				}
				$to_list = array_merge($to_list,$tt);
			}
			if($a && $to_list){
				$this->email_model->ass_notification($a,$to_list);
			}
		}
	}
	function checa_quarentena(){
		$acomps = $this->db->where('status',2)->get('acomp')->result_array();
		foreach($acomps as $a){
			$reborn = $this->db->query(
				"SELECT
					COUNT(*) as c
				FROM pita
				LEFT JOIN repair_ack 
					ON (repair_ack.os = pita.NRO_OS 
						AND repair_ack.per = pita.PER 
							AND RIGHT(pita.SERVICO,2) = repair_ack.svc)
				WHERE
					repair_ack.os IS NULL
					AND pita.PER = {$a['per']}
					AND pita.NUM_ASS = {$a['ass']}
					AND DT_INGR > '".date('Y-m-d',strtotime($a['solved_in']))."'
				")->row_array();
			if(intval($reborn['c']) > 0){
				$this->db->where('id',$a['id'])->update('acomp',array('status'=>0,'stage'=>1,'solved_in'=>null));
			}
		}
		return true;
	}
	function expirar_acompanhamentos(){
		$date = new DateTime();
		date_sub($date,date_interval_create_from_date_string($this->acomp_solved_expiration.' days'));
		$d1 = $date->format('Y-m-d H:i:s');
		$magic =
		"UPDATE acomp
		SET status = 3
		WHERE
			date < '{$d1}'
			AND status = 2";
		$this->db->query($magic);
		return true;
	}
	function salvar_parametros_do_sistema($args){
		foreach($args as $key => $val){
			$this->db->where('name',$key)->update('system_args',array('value'=>$val));
		}
	}
	function usuario_fidelizacao($user){
		return ($this->db->where('user',$user)->get('fid')->num_rows() > 0);
	}
	function areas_coordenadas_por_usuario($user){
		$gs = $this->db->select('COUNT(*) as c')->where('user',$user)->get('global_coord')->row_array();
		if( $gs && intval($gs['c']) > 0 ){
			return true;
		}else{
			$a = $this->db->select('area')->where('user',$user)->get('area_coord')->result_array();
			if($a){
				$areas = array();
				foreach($a as $b){
					$areas[] = intval($b['area']);
				}
				return $areas;
			}else{
				return false;
			}
		}
	}
	function lista_metas_imb(){
		$magic = "select meta_imb from area";
		$r = $this->db->query($magic);
		$meta_imbs[] = array('meta_imb' => ".$this->sim_imb_meta.");
		$meta_imbs[] = $r->result_array();
		return ;
	}
	function historico_imb_por_produto(){
		$magic =
			"SELECT mm as m,a.id as areaID,a.name as area,
				(
					SELECT COUNT(*)
					FROM repair
					WHERE repair.xservice = 'cm' AND repair.area = a.id AND mes.mm = repair.month
				) as x_cm,
				(
					SELECT COUNT(*)
					FROM repair
					WHERE repair.xservice = 'tv' AND repair.area = a.id AND mes.mm = repair.month
				) x_tv,
				(
					SELECT SUM(cbase.base)
					FROM cbase
					WHERE cbase.area = a.id AND cbase.month = mes.mm AND (cbase.cservice = '1' OR cbase.cservice = '2')
				) as base_cm,
				(
					SELECT SUM(cbase.base)
					FROM cbase
					WHERE cbase.area = a.id AND cbase.month = mes.mm AND (cbase.cservice = '2' OR cbase.cservice = '3')
				) as base_tv
			 FROM mes
			 JOIN area a
			 ORDER by areaID,m";
		$r = $this->db->query($magic)->result_array();
		$magic =
			"SELECT mm as m,10 as areaID,'SIM' as area,
				(
					SELECT COUNT(*)
					FROM repair
					WHERE repair.xservice = 'cm' AND mes.mm = repair.month
				) as x_cm,
				(
					SELECT COUNT(*)
					FROM repair
					WHERE repair.xservice = 'tv' AND mes.mm = repair.month
				) x_tv,
				(
					SELECT SUM(cbase.base)
					FROM cbase
					WHERE cbase.month = mes.mm AND (cbase.cservice = '1' OR cbase.cservice = '2')
				) as base_cm,
				(
					SELECT SUM(cbase.base)
					FROM cbase
					WHERE cbase.month = mes.mm AND (cbase.cservice = '2' OR cbase.cservice = '3')
				) as base_tv
			 FROM mes
			 ORDER by m";
		$r = array_merge($r,$this->db->query($magic)->result_array());
		return $r;
	}
	function imb_por_produto_total($month='',$area=''){
		if($month != 'Total'){
			$magic = "
				SELECT
					cm.indice as indice_cm,
					tv.indice as indice_tv,
					".$this->sim_imb_meta_tv." as meta_imb_tv,
					".$this->sim_imb_meta_cm." as meta_imb_cm
				FROM
				(
					SELECT ((COUNT(*)/cnumero) * 100) as indice, xservice
					FROM repair
					JOIN
					(
							SELECT SUM(cbase.base) as 'cnumero'
							FROM cbase
							WHERE  month = '$month-01' AND (cbase.cservice = '1' OR cbase.cservice = '2')
					) cbase
					WHERE
						repair.month = '$month-01'
						AND repair.xservice = 'cm'
				) cm,
				(
					SELECT ((COUNT(*)/cnumero) * 100) as indice, xservice
					FROM repair
					JOIN
					(
						SELECT SUM(cbase.base) as 'cnumero'
						FROM cbase
						WHERE  month = '$month-01' AND (cbase.cservice = '2' OR cbase.cservice = '3')
					) cbase
					WHERE
						repair.month = '$month-01'
						AND repair.xservice = 'tv'
				) tv
				";
			}else{
				$magic =
					"SELECT
						cm.indice as indice_cm,
						tv.indice as indice_tv,
						".$this->sim_imb_meta_tv." as meta_imb_tv,
						".$this->sim_imb_meta_cm." as meta_imb_cm
					FROM
					(
						SELECT ((COUNT(*)/cnumero) * 100) as indice, xservice
						FROM repair
						JOIN
						(
							SELECT SUM(cbase.base) as 'cnumero'
							FROM cbase
							WHERE cbase.cservice = '1' OR cbase.cservice = '2'
						) cbase
						WHERE repair.xservice = 'cm'
					) cm,
					(
						SELECT ((COUNT(*)/cnumero) * 100) as indice, xservice
						FROM repair
						JOIN
						(
							SELECT SUM(cbase.base) as 'cnumero'
							FROM cbase
							WHERE cbase.cservice = '2' OR cbase.cservice = '3'
						) cbase
						WHERE repair.xservice = 'tv'
					) tv
					";
			}
		$r = $this->db->query($magic);
		return $r->row_array();
	}
	function imb_por_produto($month=''){
		if($month != 'Total'){
			$magic =
			"SELECT
				x.*,
				(
					SELECT value
					FROM area_meta
					WHERE
						name = 'meta_imb_tv'
						AND area_meta.area = x.areaID
						AND date_format(ini,'%Y-%m') <= '$month'
					ORDER BY ini DESC, id DESC
					LIMIT 1
				) as meta_imb_tv,
				(
					SELECT value
					FROM area_meta
					WHERE
						name = 'meta_imb_cm'
						AND area_meta.area = x.areaID
						AND date_format(ini,'%Y-%m') <= '$month'
					ORDER BY ini DESC, id DESC
					LIMIT 1
				) as meta_imb_cm
			FROM (
				SELECT
					tv.areaName as area,
					cm.indice as indice_cm,
					tv.indice as indice_tv,
					cm.indice + tv.indice as total,
					tv.areaID
				FROM
				(
					SELECT
						((COUNT(*)/cnumero) * 100) as indice,
						cbase.name as areaName,
						xservice,
						areaID
					FROM repair
					JOIN
					(
						SELECT
							SUM(cbase.base) as 'cnumero',
							area.name,
							cbase.area as areaID
						FROM cbase,area
						WHERE
							month = '$month-01'
							AND cbase.area = area.id
							AND (cbase.cservice = '1' OR cbase.cservice = '2')
						GROUP BY area
					) cbase ON repair.area = cbase.areaID
					WHERE
						repair.month = '$month-01'
						AND repair.xservice = 'cm'
					GROUP BY repair.area
				) cm
				JOIN
				(
					SELECT
						((COUNT(*)/cnumero) * 100) as indice,
						cbase.name as areaName,
						xservice,
						areaID
					FROM repair
					JOIN
					(
						SELECT
							SUM(cbase.base) as 'cnumero',
							area.name,
							cbase.area as areaID
						FROM cbase,area
						WHERE
							month = '$month-01'
							AND cbase.area = area.id
							AND (cbase.cservice = '2' OR cbase.cservice = '3')
						GROUP BY area
					) cbase ON repair.area = cbase.areaID
					WHERE
						repair.month = '$month-01'
						AND repair.xservice = 'tv'
					GROUP BY repair.area
				) tv ON tv.areaID = cm.areaID
				ORDER by total DESC
			) x";
		}else{
			$magic =
			"SELECT
				x.*,
				(
					SELECT value
					FROM area_meta
					WHERE
						name = 'meta_imb_tv'
						AND area_meta.area = x.areaID
					ORDER BY ini DESC, id DESC
					LIMIT 1
				) as meta_imb_tv,
				(
					SELECT value
					FROM area_meta
					WHERE
						name = 'meta_imb_cm'
						AND area_meta.area = x.areaID
					ORDER BY ini DESC, id DESC
					LIMIT 1
				) as meta_imb_cm
			FROM (
				SELECT
					tv.areaName as area,
					cm.indice as indice_cm,
					tv.indice as indice_tv,
					cm.indice + tv.indice as total,
					tv.areaID
				FROM
				(
					SELECT
						((COUNT(*)/cnumero) * 100) as indice,
						cbase.name as areaName,
						xservice,
						areaID
					FROM repair
					JOIN
					(
						SELECT
							SUM(cbase.base) as 'cnumero',
							area.name,
							cbase.area as areaID
						FROM cbase,area
						WHERE
							cbase.area = area.id
							AND (cbase.cservice = '1' OR cbase.cservice = '2')
						GROUP BY area
					) cbase ON repair.area = cbase.areaID
					WHERE repair.xservice = 'cm'
					GROUP BY repair.area
				) cm
				JOIN
				(
					SELECT
						((COUNT(*)/cnumero) * 100) as indice,
						cbase.name as areaName,
						xservice,
						areaID
					FROM repair
					JOIN
					(
						SELECT
							SUM(cbase.base) as 'cnumero',
							area.name,
							cbase.area as areaID
						FROM cbase,area
						WHERE
							cbase.area = area.id
							AND (cbase.cservice = '2' OR cbase.cservice = '3')
						GROUP BY area
					) cbase ON repair.area = cbase.areaID
					WHERE repair.xservice = 'tv'
					GROUP BY repair.area
				) tv ON tv.areaID = cm.areaID
				ORDER by total DESC
			) x";
		}
		$r = $this->db->query($magic);

		return $r->result_array();
	}
	function sim_bases(){
		$magic =
		"SELECT
			IFNULL(cbase.base,0) as base,
			cservice.id as cservice
		FROM cservice
		LEFT JOIN
			(
				SELECT
					SUM(cbase.base) as base,
					cbase.cservice
				FROM cbase
				WHERE
					cbase.month = '".$this->currmonth."'
				GROUP BY cbase.cservice
			) cbase ON cbase.cservice = cservice.id
		ORDER BY cservice";
		return $this->db->query($magic)->result_array();
	}
	function area_bases($area){
		$magic =
		"SELECT
			IFNULL(cbase.base,0) as base,
			cservice.id as cservice,
			area.id as areaID
		FROM area
		JOIN cservice
		LEFT JOIN
			(
				SELECT
					SUM(cbase.base) as base,
					cbase.area as areaID,
					cbase.cservice
				FROM cbase
				WHERE
					cbase.area = ".$this->area_id($area)."
					AND cbase.month = '".$this->currmonth."'
				GROUP BY cbase.area,cbase.cservice
			) cbase ON (cbase.areaID = area.id AND cbase.cservice = cservice.id)
		WHERE
			area.id = '".$this->area_id($area)."'
		ORDER BY cservice";
		$q = $this->db->query($magic);
		return $q->result_array();
	}
	function cst_cluster_id($area){
		if(!$area || strtolower($area) === 'sim' || strtolower($area) === 'sim tv'){
			return null;
		}
		
		if(strlen($area) === 2){
			return $area;
		}
		
		$myarea = 
			$this->db->select('id')->
				where('name',$area)->
					get('cst_cluster')->row_array();
		
		return ($myarea)?$myarea['id']:null;
	}
	function cst_area_id($area){
		if(!$area || strtolower($area) === 'sim' || strtolower($area) === 'sim tv')
			return 0;
		if(is_numeric($area))
			return intval($area);
		
		$myarea = 
			$this->db->select('id')->
				where('name',$area)->
				or_where('abbr',$area)->
				get('cst_area')->row_array();
		return (($myarea)?intval($myarea['id']):0);
	}
	function area_id($area){
		if(!$area || strtolower($area) === 'sim' || strtolower($area) === 'sim tv')
			return 0;
		if(is_numeric($area))
			return intval($area);
		
		$myarea = 
			$this->db->select('id')->
				where('name',$area)->
				or_where('name_abbr',$area)->
				get('area')->row_array();
		return (($myarea)?intval($myarea['id']):0);
	}
	function area_name($area){
		if(strtolower($area) === 'sim' || strtolower($area) === 'sim tv' )
			return 'SIM';
		
		
		$this->db->select('name a');
		
		if(is_numeric($area)){
			$this->db->where('id',$area);
		}else{
			$this->db->where('name',$area)->or_where('name_abbr',$area);
		}
		
		$myarea = $this->db->get('area')->row_array();
		return (($myarea)?$myarea['a']:null);
	}
	function area_abbr($area){
		if(strtolower($area) === 'sim' || strtolower($area) === 'sim tv' )
			return 'SIM';
		
		
		$this->db->select('name_abbr a');
		
		if(is_numeric($area)){
			$this->db->where('id',$area);
		}else{
			$this->db->where('name',$area)->or_where('name_abbr',$area);
		}
		
		$myarea = $this->db->get('area')->row_array();
		return (($myarea)?$myarea['a']:null);
	}
	function area_stats($area=false,$m=false,$irm=true){
		if(strtolower($area) === 'sim')
			$area = false;
		$aid = (($area)?$this->area_id($area):false);

		if(!$m)
			$m = $this->currmonth;

		$tv = $this->imb($aid,$m,'tv');
		$cm = $this->imb($aid,$m,'cm');
		$total = $this->imb($aid,$m);

		$r = array();

		$r['imb']['cm'] = $cm;
		$r['imb']['tv'] = $tv;
		$r['imb']['total'] = $total;
		$r['imb']['combo'] = ($tv['base'] + $cm['base']) - $total['base'];

		$r['rev'] = $this->revisita($m,$area);

		return $r;
	}
	function rcount($a=false,$m=false,$p=false){
		$key = sha1(json_encode(array('function' => 'rcount', 'a' => $a, 'm' => $m,'p'=>$p)));

		$this->load->driver('cache');
		$x = $this->cache->memcached->get($key);
		if ( $x === false ){
			 $x = $this->_rcount($a, $m, $p);
			 $this->_memcacher($key, $x);
		}
		return $x;
	}
	function _rcount($a,$m,$p){
		$this->db->select('COUNT(*) as c');
		if($a)
			$this->db->where('area', $a);

		if($m)
			$this->db->where('month',$m.'-01');

		if($p)
			$this->db->where('xservice',$p);

		$r = $this->db->get('repair')->row_array();
		return intval($r['c']);
	}
	function basecount($a,$m,$p){
		$key = sha1(json_encode(array('function' => 'basecount', 'a' => $a, 'm' => $m,'p'=>$p)));

		$this->load->driver('cache');
		$x = $this->cache->memcached->get($key);
		if ( $x === false )
		{
			 $x = $this->_basecount($a, $m, $p);
			 $this->_memcacher($key, $x);
		}
		return $x;
	}
	function _basecount($a,$m,$p){
		$this->db->select('SUM(base) as base');

		if($a)
			$this->db->where('area', $a);

		if($m){
			$this->db->where('month',$m.'-01');
		}else{
			$this->db->where('month',$this->currmonth);
		}

		if($p){
			if($p === 'cm')
				$this->db->where('(cservice = 1 OR cservice = 2)',null,false);
			if($p === 'tv')
				$this->db->where('(cservice = 2 OR cservice = 3)',null,false);
			
			if($p === 'cb')
				$this->db->where('cservice',2);
			if($p === 'tv0')
				$this->db->where('cservice',3);
			if($p === 'cm0')
				$this->db->where('cservice',1);
			
		}

		$b = $this->db->get('cbase')->row_array();
		return intval($b['base']);
	}
	function imb($a=false,$m=false,$p=false){

		$a = ((strtoupper($a) === 'SIM' || !$a)?false:$a);
		$aid = (($a)?$this->area_id($a):0);
		$m = ((!$m || strtolower($m) === 'total')?false:date('Y-m',  strtotime($m)));
		$p = (($p)?strtolower($p):false);

		$r = $this->rcount($aid,$m,$p);
		$b = $this->basecount($aid, $m, $p);

		$meta = false;
		if($a)
			$meta = $this->mmeta("meta_imb".(($p)?"_{$p}":''),$aid,$m);
		else{
			if($p){
				if($p === 'cm')
					$meta = $this->sim_imb_meta_cm;
				if($p === 'tv')
					$meta = $this->sim_imb_meta_tv;
			}else{
				$meta = $this->sim_imb_meta;
			}
		}

		return array('imb' => nozeropercent($r, $b), 'meta'=>$meta, 'reclamacoes'=>$r, 'base'=>$b);
	}
	function mmeta($meta,$a,$d=false){

		$a = $this->area_id($a);

		$d = (($d)?date('Y-m',  strtotime($d)):false);

		$this->db->select('value');
		$this->db->where('name',$meta);
		$this->db->where('area',$a);

		if($d)
			$this->db->where("date_format(ini,'%Y-%m') <=",$d);

		$m = $this->db->order_by('ini DESC, id DESC')->limit(1)->get('area_meta')->row_array();
		
		if($m){
			return fFloat($m['value']);
		}else{
			return 0.0;
		}
	}
	function timeline_imb_sim(){
		$magic = "
			select
				base,IFNULL(c,0) as manutencoes,d as dia,value as meta
			from (
				select *
				from (
					select *
					from dates
					order by d desc
					limit 720
				) x
				order by d asc
			) dates
			JOIN system_args ON name = 'sim_imb_meta'
			JOIN
				(
					SELECT
							SUM(base) as base,
							month
					FROM cbase
					group by cbase.month
				) cbase ON CONCAT_WS('-',date_format(dates.d,'%Y-%m'),'01') = cbase.month
			LEFT JOIN
				(
					SELECT
							COUNT(*) as c,date
					FROM repair
					GROUP BY date
				) repair ON repair.date = dates.d";
		$r = $this->db->query($magic);
		return $r->result_array();

	}
	function historico_imb_sim(){
		$magic = "
			select 'SIM' as aname,count(*) as manutencoes,b.base,repair.month as rmes
			from repair
			JOIN (
				select SUM(cbase.base) as base,cbase.month as bmes
				from cbase
				group by cbase.month) b
			ON repair.month = b.bmes
			group by repair.month
			order by repair.month";
		$r = $this->db->query($magic);
		return $r->result_array();

	}
	function historico_imb(){
		$magic =
		"select
			area.name as aname,
			count(*) as manutencoes,
			b.base,
			repair.month as rmes
		from repair
		JOIN `area` ON `area`.`id` = repair.area
		JOIN (
			select
				cbase.area,
				SUM(base) as base,
				cbase.month as bmes
			from cbase
			group by cbase.area,cbase.month
			order by cbase.area) b ON (repair.month = b.bmes AND repair.area = b.area)
		group by repair.area,repair.month
		order by repair.area,repair.month";
		$r = $this->db->query($magic);
		return $r->result_array();
	}
	function historico_imb_por_area($area){
		$magic =
		"SELECT
			base,IFNULL(c,0) as manutencoes,d as dia,value as meta
		FROM (
			select *
			from (
				select *
				from dates
				order by d desc
				limit 720
			) x
			order by d asc
		) dates
		JOIN
			(
				SELECT
						SUM(base) as base,
						month
				FROM cbase
				WHERE
					cbase.area = ".$this->area_id($area)."
				group by cbase.month
			) cbase ON CONCAT_WS('-',date_format(dates.d,'%Y-%m'),'01') = cbase.month
		LEFT JOIN
			(
				SELECT
						COUNT(*) as c,date
				FROM repair
				WHERE
					repair.area = ".$this->area_id($area)."
				GROUP BY date
			) repair ON repair.date = dates.d
		JOIN
			(
				SELECT value,ini,area
				FROM area_meta
				WHERE
					name = 'meta_imb'
				ORDER BY ini DESC, id DESC
			) area_meta ON (area = ".$this->area_id($area)." AND ini <= d)
		GROUP BY d";
		$r = $this->db->query($magic);

		return $r->result_array();
	}
	function mes_fechado($mes = false){
		
		if($mes)
			$mes = date('Y-m',strtotime($mes));
		else 
			$mes = $this->currmonth2;
		
		return ( $mes < date('Y-m')
				|| (date('d',  strtotime($this->dia_da_ultima_reclamacao())) === date('j',strtotime($this->currmonth))) );
	}
	function existencia_area($area){
		$a = new DateTime($this->dia_da_primeira_reclamacao($area));
		$b = new DateTime($this->dia_da_ultima_reclamacao($area));
		$diff = date_diff($a, $b, true);
		return array(
				'a'=>$a->format('Y-m-d'),
				'b'=>$b->format('Y-m-d'),
				'c'=>$diff->days);
	}
	function dia_da_primeira_reclamacao($area = false){
		$this->db->select_min('repair.date','fdoh');
		if($area && $area !== 'SIM')
			$this->db->where('repair.area',$this->area_id($area));
		$r = $this->db->get('repair');
		if($r){
			$a = $r->row_array();
			return $a['fdoh'];
		}else
			return false;
	}
	function dia_da_ultima_reclamacao($area=false){
		$this->db->select_max('repair.date','ldoh');
		if($area && $area !== 'SIM')
			$this->db->where('repair.area',$this->area_id($area));
		$r = $this->db->get('repair');
		if($r){
			$a = $r->row_array();
			return $a['ldoh'];
		}else
			return false;
	}
	function lista_meses($ord = 'DESC'){
		return $this->db->select('mm as month')->order_by('mm',$ord)->get('mes')->result_array();
	}
	function busca_area_metas($id,$time = false){
		$metas = array(
			'meta_imb' => 0,
			'meta_imb_tv' => 0,
			'meta_imb_cm' => 0,
			'meta_irm' => 0,
			'meta_producao' => 0,
			'meta_qualidade' => 0,
			'meta_cad_inst_diff' => 0
		);
		foreach($metas as $x => $m){
			$this->db->
				select('value')->
				where('name',$x)->
				where('area',$id);
			if($time){
				$this->db->where('ini <=',$time);
			}
			$a = $this->db->
				order_by('ini','desc')->
				limit(1)->get('area_meta')->row_array();
			$metas[$x] = fFloat($a['value']);
		}
		$a = $this->db->select('color')->get_where('area',array('id'=>$id))->row_array();
		$metas['color'] = $a['color'];
		return $metas;
	}
	function lista_acomp_status(){
		return $this->db->select('id,name')->order_by('id','asc')->get('acomp_status')->result_array();
	}
	function lista_acomp_stages(){
		return $this->db->select('id,name')->order_by('id','asc')->get('acomp_stage')->result_array();
	}
	function lista_areas(){
		return $this->db->select('area.id as id,area.name as name,area.name_abbr')->order_by('name','asc')->get('area')->result_array();
	}
	function timeline_reclamacoes_por_produto($area = false){
		$magic =
		"SELECT
			dates.d as dia, xservice.name as xservice,repair.c
		FROM (
			select *
			from (
				select *
				from dates
				order by d desc
				limit 720
			) x
			order by d asc
		) dates
		JOIN xservice
		LEFT JOIN
		(
			SELECT
				COUNT(*) as c,date,xservice
			FROM repair
				".(($area && $area !== 'SIM')?"JOIN area ON repair.area = area.id":'')."
			".(($area && $area !== 'SIM')?
			"WHERE
				area.name = '{$area}'":'')."
			GROUP BY repair.date,repair.xservice
		) repair ON (repair.date = dates.d AND repair.xservice = xservice.name)";

		return $this->db->query($magic)->result_array();
	}
	function timeline_reclamacoes($m,$area = false){
		$result = array('x' => array());
		$dia = new DateTime($m);
		$areaID = $this->area_id($area);
		$pers = null;
		if($areaID){
			$pers = $this->db->select('abbr')->where('area',$areaID)->get('per')->result_array();
			$pers = array_map(function($per){return $per['abbr'];},$pers);
		}
		while($dia->format('Y-m-d') <= $this->lday && $dia->format('Y-m') === substr($m,0,7)){
			$d = $dia->format('Y-m-d');
			$diaD = null;
			if($area && $area !== 'SIM'){
				$r = $this->db->query(
					"SELECT
						COUNT(*) as manutencoes
					FROM repair
					WHERE
						repair.area = {$areaID}
						AND date = '{$d}'")->row_array();
				$diaD =
					array(
						'dia' => $d,
						'pers' => $pers,
						'evCount' => $this->evCount($d,$areaID),
						'y' => intval($r['manutencoes']),
						'x' => ((strtotime($d) + ( 60 * 60 * 12 )) * 1000)
					);
			}else{
				$r = $this->db->query(
					"SELECT
						COUNT(*) as manutencoes
					FROM repair
					WHERE
						date = '{$d}'")->row_array();
				
				$diaD =
					array(
						'dia' => $d,
						'evCount' => $this->evCount($d),
						'y' => intval($r['manutencoes']),
						'x' => ((strtotime($d) + ( 60 * 60 * 12 )) * 1000)
					);
			}
			if($diaD){
				$result['x'][] = $diaD;
			}
			$dia->add(new DateInterval('P1D'));
		}
		
		return $result;
	}
	function cadInstFilter($mes = null,$prod = false){
		$instal = 'instal'.(($prod)?"_{$prod}":'');
		if($mes){
			$this->db->where("date_format(assinante.cad, '%Y-%m') = ".$this->db->escape($mes),NULL,FALSE);
		}
		$this->db->where("assinante.cad <= assinante.{$instal}",NULL,FALSE);
		$this->db->where("assinante.cad IS NOT NULL",NULL,FALSE);
		$this->db->where("assinante.{$instal} IS NOT NULL",NULL,FALSE);
	}
	function cadInstDiffFaixas($mes){
		$faixas = array(
			array('from' => 0,'to' => 3, 'color' => '#80A4D2'),
			array('from' => 3,'to' => 7, 'color' => '#5ABC4F'),
			array('from' => 7,'to' => 14, 'color' => '#FFFF00'),
			array('from' => 14, 'to' => 30, 'color' => '#D74B1D'),
			array('from' => 30, 'color' => '#B40404'),
		);
		
		$result = array(
			'name' => 'Tempo de Espera Instalação', 
			'series' => array(), 
			'categories' => array()
		);


		$sim = array('id' => 0, 'name' => 'Sim Tv', 'abbr' => 'SIM');
		$areas = $this->db->select('area.id,area.name,area.name_abbr abbr,area.color')->order_by('name')->get('area')->result_array();
		
		$series = array();
		$areas = array_merge(array($sim),$areas);
		foreach($faixas as $faixaIndex => $faixa){
			
			$from = array_key_exists('from',$faixa)?$faixa['from']:0;
			$to = array_key_exists('to',$faixa)?$faixa['to']:0;

			$diffStr = "datediff(assinante.instal, assinante.cad)";
			$series = array(
				'name' => 
					(($from && $to)
						?"De {$from} à {$to} dias"
							:((!$to)
								?"A partir de {$from} dias"
									:"Menos de {$to} dias")),
				'data' => array(),
				'color' => $faixa['color']
			);

			foreach($areas as $j => $area){

				$area['id'] =  intval($area['id']);
				
				$this->cadInstFilter($mes);
				if($area['id']){
					$this->db->where('area.id',$area['id']);
				}
				if($from){
					$this->db->where("{$diffStr} >=", $from);
				}
				if($to){
					$this->db->where("{$diffStr} <", $to);
				}
				$this->db->join('per','per.id = assinante.per');
				$this->db->join('area','per.area = area.id');
				$count = $this->db->count_all_results('assinante');
				
				if(!array_key_exists('total',$area)){
					$area['total'] = 0;
				}
				$area['total'] += $count;
				if($faixaIndex === 0){
					$area['y'] = $count;
				}

				$series['data'][$area['id']] = array(
					'id' => $area['id'],
					'y' => $count,
					'name' => $area['name'],
					'area' => $area['id'],
					'faixa' => $faixa
				);
				$areas[$j] = $area;
			}
			
			$series['data'] = array_values($series['data']);
			$result['series'][] = $series;
		}

		usort($areas, function($a,$b){
			if(!$a['id']){
				return -1;
			}
			if(!$b['id']){
				return 1;
			}
			
			$b['val'] = nozeropercent($b['y'],$b['total']);
			$a['val'] = nozeropercent($a['y'],$a['total']);

			return $b['val'] - $a['val'];
		});
		
		foreach($result['series'] as $i => $serie){
			foreach($areas as $j => $area){
				if($i === 0){
					$result['categories'][] = $area['abbr'];
				}
				$serie['data'][$area['id']]['newIndex'] = $j;
				$serie['data'][$area['id']]['percent'] = nozeropercent($serie['data'][$area['id']]['y'], $area['total']);
			}
			usort($serie['data'], function($a,$b){
				return $a['newIndex'] - $b['newIndex'];
			});
			$result['series'][$i] = $serie;
		}
		
		$result['series'] = array_reverse($result['series']);
		return $result;
	}
	function evCount($d,$areaID = null){
		$key = "evCount.{$d}.".(($areaID)?$areaID:'sim');
		
		$evCount = $this->cache->memcached->get($key);
		if($evCount !== false){
			return $evCount;
		}
		$this->miss++;
		if($areaID){
			$magic = "SELECT COUNT(DISTINCT id) c
			FROM (
				select tt.id
				from tt_location
				JOIN tt ON tt_location.tt = tt.id
				JOIN node ON node.node = tt_location.location
				JOIN per ON per.id = node.per
				JOIN area ON area.id = per.area
				where
					tt.type != 'backlog'
					AND area.id = {$areaID}
				 	AND tt_location.location_type = 'node'
					AND tt.status != 'cancelado'
					AND DATE(tt.open) = '{$d}'
				
				UNION

				select tt.id
				from tt_location
				join tt ON tt_location.tt = tt.id
				JOIN per ON (per.name = tt_location.location OR per.abbr = tt_location.location)
				JOIN area ON area.id = per.area
				where
					tt.type != 'backlog'
					AND area.id = {$areaID}
				 	AND tt_location.location_type = 'cidade'
					AND tt.status != 'cancelado' 
					AND DATE(tt.open) = '{$d}'
			) x";
			$evCount = $this->db->query($magic)->row_array();
		}else{
			$evCount = $this->db->query(
			"SELECT count(*) c
			from tt
			where
				tt.type != 'backlog'
				AND tt.status != 'cancelado'
				AND DATE(tt.open) = '{$d}'")->row_array();
		}
		$evCount = ($evCount)?intval($evCount['c']):0;
		$this->cache->memcached->save($key, $evCount, ($d < date('Y-m-d'))?60 * 60 * 24:60 * 30);
		return $evCount;
	}
	function ranking_de_nodes_por_area($area='SIM',$month = false){
		if($area === 'SIM' || !$area){
			if($month){
					$pleasework = "AND repair.date LIKE '$month-%'";
			}else{
					$pleasework = "";
			}
			$r = $this->db->query(
			"select
					tv.NODE,
					tv.manutencoes as manutencoes_tv,
					cm.manutencoes as manutencoes_cm
			from
			(
				select pita.NODE,COUNT(*) as manutencoes
				from repair
				LEFT JOIN pita ON repair.id = pita.id
				WHERE
					NODE != ''
					AND repair.xservice = 'tv' $pleasework
				GROUP BY pita.NODE
				ORDER BY NODE
			) tv
			LEFT JOIN
			(
				select pita.NODE,COUNT(*) as manutencoes
				from repair
				LEFT JOIN pita ON repair.id = pita.id
				WHERE
					NODE != ''
					AND repair.xservice = 'cm' $pleasework
				GROUP BY pita.NODE
				ORDER BY NODE
			) cm
			ON tv.NODE = cm.NODE
			ORDER BY IF(manutencoes_tv IS NULL,0,manutencoes_tv) + IF(manutencoes_cm IS NULL,0,manutencoes_cm) DESC");
			return $r->result_array();
		}else{
			$area = $this->area_id($area);
			if($month){
					$pleasework = "AND repair.date LIKE '$month-%'";
			}else{
					$pleasework = "";
			}
			$r = $this->db->query(
			"select
					tv.NODE,
					tv.manutencoes as manutencoes_tv,
					cm.manutencoes as manutencoes_cm
			from
			(
					select pita.NODE,COUNT(*) as manutencoes
					from repair
					LEFT JOIN pita ON repair.id = pita.id
					WHERE
						NODE != ''
						AND repair.area = $area AND repair.xservice = 'tv' $pleasework
					GROUP BY pita.NODE
			) tv
			LEFT JOIN
			(
					select pita.NODE,COUNT(*) as manutencoes
					from repair
					LEFT JOIN pita ON repair.id = pita.id
					WHERE
						NODE != ''
						AND repair.area = $area AND repair.xservice = 'cm' $pleasework
					GROUP BY pita.NODE
			) cm
			ON tv.NODE = cm.NODE
			ORDER BY IF(manutencoes_tv IS NULL,0,manutencoes_tv) + IF(manutencoes_cm IS NULL,0,manutencoes_cm) DESC");
			return $r->result_array();
		}
	}
	function ranking_de_status_por_area($area='SIM',$month=false){
			if($area === 'SIM'){
					if($month){
							$addquery = "WHERE pita.DT_INGR LIKE '$month-%'";
					}else{
							$addquery = "";
					}
					$r = $this->db->query(
					"select STATUS_OS as status,COUNT(*) as c
					from pita
					$addquery
					GROUP BY STATUS_OS
					ORDER BY c DESC");
					return $r->result_array();
			}else{
					if($month){
							$addquery = "AND pita.DT_INGR LIKE '$month-%'";
					}else{
							$addquery = "";
					}
					$r = $this->db->query(
					"select STATUS_OS as status,COUNT(*) as c
					from pita
					LEFT JOIN per ON per.id = pita.PER
					LEFT JOIN area ON area.id = per.area
					WHERE area.name = '$area' $addquery
					GROUP BY STATUS_OS
					ORDER BY c DESC");
					return $r->result_array();
			}
	}
	function ranking_de_causas_por_area($area='SIM',$month='Total'){
		$this->db->select('CAUSA, count(*) as c')->from('pita')->where('STATUS_OS !=','CANCELADO')->where('CAUSA !=','');
		if($area != 'SIM')
			$this->db->join('per','per.id = pita.PER')->join('area','per.area = area.id')->where('area.name',$area);
		if($month != 'Total')
			$this->db->like('pita.DT_INGR',$month);

		return $this->db->group_by('CAUSA')->order_by('c','DESC')->get()->result_array();
	}
	function ranking_de_motivos_por_area($date,$area = 'SIM'){
		$magic =
		"SELECT
			motivo, SUM(C) AS c
		FROM (
			SELECT *
			FROM (
				SELECT
					MOTIVO_CM AS motivo,
					COUNT(*) C
				FROM backlog
				".
				(($area && $area !== 'SIM')
				?
				"JOIN per ON per.id = backlog.COD_OPERADORA
				JOIN area ON area.id = per.area"
				:
				"")."
				WHERE
					MOTIVO_CM IS NOT NULL
					AND MOTIVO_CM > '0'
					AND DT_GERACAO LIKE '{$date}%'
					".(($area !== 'SIM')?"AND area.name = '{$area}'":"")."
				GROUP BY MOTIVO_CM
				ORDER BY C DESC
			) cm

			UNION

			SELECT *
			FROM (
				SELECT
					MOTIVO_PTV AS motivo,
					COUNT(*) C
				FROM backlog
				".
				(($area && $area !== 'SIM')
				?
				"JOIN per ON per.id = backlog.COD_OPERADORA
				JOIN area ON area.id = per.area"
				:
				"")."
				WHERE
					MOTIVO_PTV IS NOT NULL
					AND MOTIVO_PTV > '0'
					AND DT_GERACAO LIKE '{$date}%'
					".(($area && $area !== 'SIM')?"AND area.name = '{$area}'":"")."
				GROUP BY MOTIVO_PTV
				ORDER BY C DESC
			) tv
		) x
		GROUP BY motivo
		ORDER BY c DESC";

		return $this->db->query($magic)->result_array();
	}
	function ranking_de_causas_por_node($node=false,$month='Total'){
		$this->db->select('CAUSA, count(*) as c')->from('pita')->where('STATUS_OS !=','CANCELADO')->where('CAUSA !=','');
		if($node)
			$this->db->where('pita.NODE',$node);
		if($month != 'Total')
			$this->db->like('pita.DT_INGR',$month);

		return $this->db->group_by('CAUSA')->order_by('c','DESC')->get()->result_array();
	}
	function cep_logradouro($cep,$cidade=false){
		$this->db->select('logradouro,tp_logradouro');
		$this->db->where('cep',$cep);
		if($cidade)
			$this->db->where('cidade',$cidade);
		$cp = $this->db->get('cep')->row_array();
		if($cp)
			return trim($cp['tp_logradouro'].' '.$cp['logradouro']);
		else
			return false;
	}
	function ranking_de_cep_por_node($node,$month='Total'){

		$magic =
			"SELECT end as logradouro, bairro, cep, per.name as cidade, count(*) as c
			FROM pita
			JOIN per ON per.id = pita.PER
			LEFT JOIN assinante ON (assinante.cod = pita.NUM_ASS AND assinante.per =  pita.PER)
			WHERE
				pita.NODE =  ".$this->db->escape($node).
				(($month != 'Total')
					?" AND  pita.DT_INGR  LIKE '".$this->db->escape_like_str($month)."%'"
					:""
				).
			"GROUP BY assinante.cep
			ORDER BY c desc";
		$ceps = $this->db->query($magic)->result_array();
		for($i=0;$i<count($ceps);$i++)
		{
			$ceps[$i]['logradouro'] = remNum($ceps[$i]['logradouro']);
			$ceps[$i]['cep'] = fCep($ceps[$i]['cep']);
		}
		return $ceps;
	}
	function node_info($node){
		$magic =
		"select area.name as area
		from node
		JOIN per ON node.per = per.id
		JOIN area ON per.area = area.id
		WHERE
			node.node = '$node'";
		return $this->db->query($magic)->row_array();
	}
	function arquiva_notificacao_de_acompanhamento($acomp){
		$notif = $this->db->select('acomp_notif.acomp as acomp,acomp_notif.user as user,acomp_notif.date as date')->join('acomp_update','acomp_notif.acomp = acomp_update.id')->where('acomp_update.parent',$acomp)->get('acomp_notif');
		if($notif)
			$notif = $notif->row_array();
		foreach($notif as $n)
			$this->db->insert('notif_log',$n);
		if($notif)
			return $this->db->query("DELETE acomp_notif.* FROM acomp_notif
									INNER JOIN acomp_update ON acomp_notif.acomp = acomp_update.id
									WHERE
										acomp_update.parent = {$acomp}");
		else
			return true;
	}
	function arquiva_notificacao_de_assinante($ass,$per){
		$notif = $this->db->get_where('ass_notif',array('ass'=>$ass,'per'=>$per))->result_array();
		foreach($notif as  $n)
			$this->db->insert('notif_log',$n);
		if($notif)
			return $this->db->where('ass',$ass)->where('per',$per)->delete('ass_notif');
		else
			return true;

	}
	function arquiva_notificacao_de_assinantes_que_sairam_do_critico(){
		$date = new DateTime();
		$this->load_amax_vt();
		date_sub($date,date_interval_create_from_date_string("{$this->intervalo_corrente} days"));
		$d1 = $date->format('Y-m-d');
		$ns = $this->db->select('ass,per,per.area as area')->join('per','per.id = ass_notif.per')->group_by(array('ass','per'))->get('ass_notif')->result_array();
		foreach($ns as  $n)
		{
			$magic =
			"SELECT
				 COUNT(*) as c
			 FROM (`pita`)
			 LEFT JOIN repair_ack 
				ON (repair_ack.os = pita.NRO_OS 
					AND repair_ack.per = pita.PER 
						AND RIGHT(pita.SERVICO,2) = repair_ack.svc)
			 LEFT JOIN acomp ON (pita.NUM_ASS = acomp.ass AND pita.PER = acomp.per AND acomp.status < 2)
			 WHERE
				 repair_ack.os IS NULL
				 AND acomp.id IS null
				 AND pita.PER = '{$n['per']}'
				 AND pita.NUM_ASS = '{$n['ass']}'
				 AND  `pita`.`DT_INGR`  >= '{$d1}'";
			$r = $this->db->query($magic)->row_array();
			if(intval($r['c']) < ($this->areas['id'][$n['area']]['max_vts']-1))
				$this->arquiva_notificacao_de_assinante($n['ass'], $n['per']);
		}
	}
	function novo_acompanhamento($acomp){
		if($this->db->get_where('acomp',array('ass'=>$acomp['ass'],
			'per'=>$acomp['per'],'status <'=>3))->num_rows() === 0){
			$a =
				array(
					'ass'=>$acomp['ass'],
					'per'=>$acomp['per'],
					'author'=>$this->user['login'],
					'solved_in'=>NULL,
					'status'=> $acomp['status'],
					'date' => date('Y-m-d H:i:s'),
					'stage' => $acomp['stage']
				);
			if($this->db->insert('acomp',$a)){
				$update = $acomp['update'];
				$update['parent'] = $this->db->insert_id();
				$update['date'] = date('Y-m-d H:i:s');
				$update['author'] = $this->user['login'];
				$insert_check = $this->db->insert('acomp_update',$update);
				$update['id'] = $this->db->insert_id();
				if($update['stage'] === 2){
					$this->notificar_fidelizacao($update);
				}
				return ($insert_check && $this->arquiva_notificacao_de_assinante($acomp['ass'],$acomp['per']));
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function causa_hist_rank($me,$por = 'area'){
		if($por === 'area')
		{
			$me = $this->area_name($me);
			if($me === 'SIM')
				$x = $this->db->query(
					"select x.m,x.causa,IF(p.c IS NULL,0,p.c) as c
					FROM
					(
							select mes.m as m,top_causa.causa as causa
							FROM mes,top_causa
					) x
					LEFT JOIN
					(
							SELECT
									COUNT(*) as c,
									date_format(DT_INGR,'%Y-%m') as m,
									CAUSA
							from pita
							GROUP BY date_format(DT_INGR,'%Y-%m'),pita.CAUSA
					) p ON p.CAUSA = x.causa AND p.m = x.m
					ORDER BY x.causa,x.m")->result_array();
			else
				$x = $this->db->query(
					"select x.m,x.causa,IF(p.c IS NULL,0,p.c) as c
					FROM
					(
							select mes.m as m,top_causa.causa as causa
							FROM mes,top_causa
					) x
					LEFT JOIN
					(
							SELECT
									COUNT(*) as c,
									date_format(DT_INGR,'%Y-%m') as m,
									CAUSA
							from pita
							JOIN per ON per.id = pita.PER
							JOIN area ON per.area = area.id
							WHERE
								area.name = '{$me}'
							GROUP BY date_format(DT_INGR,'%Y-%m'),pita.CAUSA
					) p ON p.CAUSA = x.causa AND p.m = x.m
					ORDER BY x.causa,x.m")->result_array();
		}
		else if($por === 'node')
		{
			$x = $this->db->query(
					"select x.m,x.causa,IF(p.c IS NULL,0,p.c) as c
					FROM
					(
							select mes.m as m,top_causa.causa as causa
							FROM mes,top_causa
					) x
					LEFT JOIN
					(
							SELECT
									COUNT(*) as c,
									date_format(DT_INGR,'%Y-%m') as m,
									CAUSA
							from pita
							WHERE
								NODE = '{$me}'
							GROUP BY date_format(DT_INGR,'%Y-%m'),pita.CAUSA
					) p ON p.CAUSA = x.causa AND p.m = x.m
					ORDER BY x.causa,x.m")->result_array();
		}

		$result = array('series'=>array(),'categories'=>array());
		if($x[0]['causa'])
			$l = $x[0]['causa'];
		else
			$l = 'Não Preenchido';
		$curr = array('name'=>$l,'data'=>array());
		foreach($x as $y){
			$mm = date('M-Y',strtotime($y['m'].'-01'));
			if(!array_key_exists($mm,$result['categories']))
				$result['categories'][] = $mm;
			if($y['causa'] && $y['causa'] !== $l){
				$result['series'][] = $curr;
				if($y['causa'])
					$l = $y['causa'];
				else
					$l = 'Não Preenchido';
				$curr = array('name'=>$l,'data'=>array());

			}
			$curr['data'][] = intval($y['c']);
		}
		return $result;
	}
	function update_acomp_ri($acomp){
		$update = $acomp['update'];
		$update['parent'] = $acomp['parent'];
		$update['date'] = date('Y-m-d H:i:s');
		$update['author'] = $this->user['login'];
		$u = $this->db->insert('acomp_update',$update);
		$update['id'] = $this->db->insert_id();
		if($u){
			$this->arquiva_notificacao_de_acompanhamento($acomp['parent']);
			$a = $this->db->where('id',$acomp['parent'])->update('acomp',
				array(
					'solved_in' => null,
					'status'=> $acomp['status'],
					'stage' => $acomp['stage']
				));
			if($update['stage'] === 2){
				$this->notificar_fidelizacao($update);
			}
			return ($a);
		}else{
			return false;
		}
	}
	function update_acomp_fidelizacao($acomp){
		$update = $acomp['update'];
		$update['parent'] = $acomp['parent'];
		$update['date'] = date('Y-m-d H:i:s');
		$update['author'] = $this->user['login'];
		
		$u = $this->db->insert('acomp_update',$update);
		if($u){
			$this->arquiva_notificacao_de_acompanhamento($acomp['parent']);
			$a = $this->db->where('id',$acomp['parent'])->update('acomp',
				array(
					'solved_in'=>(($acomp['status'] === 2)?date('Y-m-d H:i:s'):null),
					'status'=> $acomp['status'],
					'stage' => $acomp['stage']
				));
			if($acomp['status'] === 2){
				$this->cria_acks($acomp['parent']);
			}
			return ($a);
		}else{
			return false;
		}
	}
	function cria_acks($acomp){
		$ass = $this->db->select('ass,per')->where('acomp.id',$acomp)->get('acomp')->row_array();
		if($ass){
			$rs = $this->db->select('
				NRO_OS as os,
				PER as per,
				RIGHT(pita.SERVICO,2) as svc',FALSE)->where('NUM_ASS',$ass['ass'])->where('PER',$ass['per'])->get('pita')->result_array();
			if($rs){
				foreach($rs as $x){
					$this->db->insert('repair_ack',
							array(
								'os'=>$x['os'],
								'per'=>$x['per'],
								'svc'=>$x['svc'],
								'acomp'=>$acomp
							)
						);
				}
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function contagem_de_vts_por_assinante($ass,$per){
		$this->db->select('COUNT(*) as c');
		$this->db->where('ass',$ass);
		$this->db->where('per',$per);
		$this->db->group_by(array('ass','per'));
		$a = $this->db->get('repair')->row_array();
		return $a['c'];
	}
	function assinantes_criticos_por_area($area='SIM'){
		$date = new DateTime();
		$this->load_amax_vt();
		date_sub($date,date_interval_create_from_date_string("{$this->intervalo_corrente} days"));
		$d1 = $date->format('Y-m-d');
		if($area != 'SIM'){
			$magic =
				"SELECT *
				FROM (
					SELECT
						COUNT(*) as c,
						`pita`.`NUM_ASS` as ass,
						`pita`.`PER` as per,
						`pita`.`NOME_ASSINANTE` as nome,
						`pita`.`NODE` as node,
						'".$this->area_name($area)."' as area,
						".$this->area_id($area)." as areaID
					FROM (`pita`)
					JOIN per ON pita.PER = per.id
					LEFT JOIN repair_ack 
						ON (repair_ack.os = pita.NRO_OS 
							AND repair_ack.per = pita.PER 
								AND RIGHT(pita.SERVICO,2) = repair_ack.svc)
					LEFT JOIN acomp ON (pita.NUM_ASS = acomp.ass AND pita.PER = acomp.per AND acomp.status < 2)
					WHERE
						repair_ack.os IS NULL
						AND acomp.id IS null
						AND  `pita`.`DT_INGR`  >= '{$d1}'
						AND per.area = ".$this->area_id($area)."
					GROUP BY `pita`.`NUM_ASS`, `pita`.`PER`
					ORDER BY `c` DESC, `nome` ASC
				) a
				WHERE
					c > ".($this->areas['nome'][$area]['max_vts']-1);
			
			return $this->db->query($magic)->result_array();
		}else{
			$x = array();
			foreach($this->areas['id'] as $a)
			{
				$magic =
				"SELECT *
				FROM (
					SELECT
						COUNT(*) as c,
						`pita`.`NUM_ASS` as ass,
						`pita`.`PER` as per,
						`pita`.`NOME_ASSINANTE` as nome,
						`pita`.`NODE` as node,
						'{$a['name']}' as area,
						{$a['id']} as areaID
					FROM (`pita`)
					JOIN per ON pita.PER = per.id
					LEFT JOIN (SELECT * FROM repair_ack ORDER BY date DESC) 
						repair_ack ON (repair_ack.os = pita.NRO_OS 
							AND repair_ack.per = pita.PER 
								AND RIGHT(pita.SERVICO,2) = repair_ack.svc)
					LEFT JOIN acomp ON (pita.NUM_ASS = acomp.ass AND pita.PER = acomp.per AND acomp.status < 2)
					WHERE
							repair_ack.os IS NULL
							AND acomp.id IS null
							AND  `pita`.`DT_INGR`  >= '{$d1}'
							AND per.area = {$a['id']}
					GROUP BY `pita`.`NUM_ASS`, `pita`.`PER`
					ORDER BY `c` DESC, `nome` ASC
				) a
				WHERE
					c > ".($a['max_vts']-1)."
				LIMIT 3";
				$y = $this->db->query($magic)->result_array();
				if($y)
					$x = array_merge($x,$y);
			}
			//dumb sort
			for($i=0;$i<count($x)-1;$i++){
				$max = $i;
				for($j=$i+1;$j<count($x);$j++){
					if(intval($x[$j]['c']) > intval($x[$max]['c'])){
						$max = $j;
					}
				}
				$temp = $x[$i];
				$x[$i] = $x[$max];
				$x[$max] = $temp;
			}
			return $x;
		}
	}
	function assinantes_criticos_por_area_e_mes($area='SIM',$month='Total'){
		if($area != 'SIM'){
			$magic =
			"SELECT *
			FROM (
				SELECT
					COUNT(*) as c,
					`pita`.`NUM_ASS` as ass,
					`pita`.`PER` as per,
					`pita`.`NOME_ASSINANTE` as nome,
					`pita`.`NODE` as node,
					'".$this->area_abbr($area)."' as area,
					".$this->area_id($area)." as areaID
				FROM (`pita`)
				JOIN per ON pita.PER = per.id
				LEFT JOIN repair_ack ON (repair_ack.os = pita.NRO_OS 
					AND repair_ack.per = pita.PER 
						AND RIGHT(pita.SERVICO,2) = repair_ack.svc)
				LEFT JOIN acomp ON (pita.NUM_ASS = acomp.ass AND pita.PER = acomp.per AND acomp.status < 2)
				WHERE
					repair_ack.os IS NULL
					AND acomp.id IS null
					".(($month != 'Total')?" AND  `pita`.`DT_INGR`  LIKE '%{$this->db->escape_like_str($month)}%'":'')."
					AND per.area = ".$this->area_id($area)."
				GROUP BY `pita`.`NUM_ASS`, `pita`.`PER`
				ORDER BY `c` DESC, `nome` ASC
				LIMIT 27
			) y where c > 1";
			return $this->db->query($magic)->result_array();
		}else{
			$x = array();
			foreach($this->areas['id'] as $a)
			{
				$magic =
				"SELECT *
				FROM (
					SELECT
						COUNT(*) as c,
						`pita`.`NUM_ASS` as ass,
						`pita`.`PER` as per,
						`pita`.`NOME_ASSINANTE` as nome,
						`pita`.`NODE` as node,
						'{$a['name']}' as area,
						{$a['id']} as areaID
					FROM (`pita`)
					JOIN per ON pita.PER = per.id
					LEFT JOIN repair_ack ON (repair_ack.os = pita.NRO_OS 
						AND repair_ack.per = pita.PER 
							AND RIGHT(pita.SERVICO,2) = repair_ack.svc)
					LEFT JOIN acomp ON (pita.NUM_ASS = acomp.ass AND pita.PER = acomp.per AND acomp.status < 2)
					WHERE
							repair_ack.os IS NULL
							AND acomp.id IS null
							".(($month != 'Total')?" AND  `pita`.`DT_INGR`  LIKE '%{$this->db->escape_like_str($month)}%'":'')."
							AND per.area = {$a['id']}
					GROUP BY `pita`.`NUM_ASS`, `pita`.`PER`
					ORDER BY `c` DESC, `nome` ASC
					LIMIT 3
				) y where c > 1";
				$y = $this->db->query($magic)->result_array();
				if($y)
					$x = array_merge($x,$y);
			}
			//dumb sort
			for($i=0;$i<count($x)-1;$i++){
				$max = $i;
				for($j=$i+1;$j<count($x);$j++){
					if(intval($x[$j]['c']) > intval($x[$max]['c'])){
						$max = $j;
					}
				}
				$temp = $x[$i];
				$x[$i] = $x[$max];
				$x[$max] = $temp;
			}
			return $x;
		}
	}
	function lista_acomp_aberta($area='SIM',$type='aberto'){
		if($type === 'aberto'){
		$magic =
			"SELECT
				acomp.id as acomp,
				acomp.ass cod,
				acomp.per,
				date,
				assinante.node,
				assinante.nome,
				acomp.stage as stageID,
				acomp.status as statusID,
				IF(acomp.status = 0,1,0) as xxx,
				area.id as areaID,
				acomp_status.name status,
				acomp_stage.name stage
			from acomp
			JOIN per ON per.id = acomp.per
			JOIN assinante ON (assinante.cod = acomp.ass AND assinante.per = acomp.per)
			
			JOIN acomp_status ON acomp_status.id = acomp.status
			JOIN acomp_stage ON acomp_stage.id = acomp.stage
			
			JOIN area ON area.id = per.area
			WHERE
				(acomp.status >= 0 AND acomp.status < 2)"
				.((strtoupper($area)!=='SIM')?"
				AND area.name = {$this->db->escape($area)}":'')."
			GROUP BY acomp.ass,acomp.per
			ORDER BY xxx DESC,acomp ASC";

			return $this->db->query($magic)->result_array();
		}else if($type === 'nao-resolvido'){
			$magic =
			"SELECT
				acomp.id as acomp,
				acomp.ass cod,
				acomp.per,
				date,
				assinante.node,
				assinante.nome,
				acomp.stage as stageID,
				acomp.status as statusID,
				IF(acomp.status = 0,1,0) as xxx,
				area.id as areaID,
				acomp_status.name status,
				acomp_stage.name stage
			from acomp
			JOIN per ON per.id = acomp.per
			JOIN assinante ON (assinante.cod = acomp.ass AND assinante.per = acomp.per)
			
			JOIN acomp_status ON acomp_status.id = acomp.status
			JOIN acomp_stage ON acomp_stage.id = acomp.stage
			
			JOIN area ON area.id = per.area
			WHERE
				acomp.status = -1"
				.((strtoupper($area)!=='SIM')?"
				AND area.name = {$this->db->escape($area)}":'')."
			GROUP BY acomp.ass,acomp.per
			ORDER BY acomp ASC";
			return $this->db->query($magic)->result_array();
		}else{
			return false;
		}

	}
	function assinante_nome_e_node($ass,$per){
		return $this->db->select('NOME_ASSINANTE as nome,NODE node')->get_where('pita',array('NUM_ASS'=>$ass,'PER'=>$per))->row_array();
	}
	function lista_acomp_updates($acomp){
		return 
			$this->db->select(
					'acomp_update.id,
					acomp_update.date,
					acomp_update.schedule,
					acomp_update.window,
					acomp_update.os,
					acomp_update.descr,
					acomp_stage.name as stage,
					acomp_update.author')->
				join('acomp_stage','acomp_stage.id = acomp_update.stage')->
				order_by('id','asc')->
				get_where('acomp_update',array('parent'=>$acomp))->result_array();
	}
	function lista_acomp_aberta_para_assinante($ass,$per){
		return $this->db->select('acomp.id,acomp.author,acomp.date,acomp_status.name as status')->join('acomp_status','acomp_status.id = acomp.status')->where('acomp.ass',$ass)->where('acomp.per',$per)->get('acomp')->result_array();
	}
	function ass_hist($ass,$per){
		$this->db->select(
				"NRO_OS, 
				STATUS_OS, 
				DT_INGR, 
				DT_AGEND, 
				DT_CUMPR, 
				NOME_TECNICO, 
				FALHA, 
				CAUSA, 
				MOTIVO, 
				OBS_ORIGEM, 
				OBS_TECNICO");
		$this->db->from('pita');
		$this->db->where('NUM_ASS',$ass);
		$this->db->where('PER',$per);
		$this->db->order_by('DT_INGR','DESC');
		$r = $this->db->get();
		return $r->result_array();
	}
	function pacote_info($ass,$per){
		return $this->db->
			select("cservice.name as produto,
				PROD_PTV as pac_tv,
				PROD_INT as pac_cm,
				DT_INSTAL_PTV as inst_tv,
				DT_INSTAL_CM as inst_cm")->
			join("cservice","cservice.alt_name = base_atual.PRODUTO")->
			where('COD_OPERADORA',$per)->
			where('COD_ASS',$ass)->
			get('base_atual')->row_array();
	}
	function fetch_ass_info($ass,$per){
		$a = $this->db->
				select("assinante.cod,
					assinante.cod ass,
					assinante.per,
					assinante.tipo,
					assinante.node,
					assinante.nome,
					assinante.cep,
					assinante.tel,
					assinante.cel,
					assinante.com,
					assinante.end,
					assinante.comp,
					assinante.bairro,
					assinante.tv,
					assinante.cm,
					assinante.instal,
					assinante.sub_tipo,
					assinante.class classe,
					assinante.produto,
					area.name as area,
					per.name as cidade")->
				join('per','per.id = assinante.per')->
				join('area','area.id = per.area')->
				where('cod',$ass)->
				where('assinante.per',$per)->
				get('assinante')->row_array();
		return (($a)?$a:null);
			
	}
	function lista_os_por_area($area='SIM',$month='Total',$status=''){
			if($month !== 'Total'){
					$addquery = "AND pita.DT_INGR LIKE '$month-%'";
			}else{
					$addquery = "";
			}
			if(is_array($status)){
					foreach($status as $s){
							$tmparray[] = "STATUS_OS = '$s'";
					}
					$addquery .= ' AND (' . implode(' OR ',$tmparray).')';
			}else{
					$addquery .= "";
			}
			if($area !== 'SIM'){
				$addquery .= " and area.name = '$area'";
			}else{
					$addquery .= "";
			}
			$magic = "
			select
					NRO_OS,
					pita.NODE,
					per.name as CIDADE,
					STATUS_OS,
					NUM_ASS,
					pita.NOME_ASSINANTE,
					pita.TIPO_ASS,
					assinante.end as LOGRADOURO,
					assinante.bairro as BAIRRO,
					pita.DT_INGR,
					pita.CAUSA,
					pita.FALHA,
					pita.OBS_ORIGEM,
					pita.OBS_TECNICO
			from pita
			LEFT JOIN assinante ON (assinante.cod = pita.NUM_ASS AND assinante.per = pita.PER)
			JOIN per ON per.id = pita.PER
			JOIN area ON area.id = per.area
			where
				1 = 1
				$addquery
			ORDER BY DT_INGR
			LIMIT 5000";
			$r = $this->db->query($magic);
			return $r->result_array();
	}
	function lista_reclamacoes_por_cep($cep,$node,$month='Total'){
			$magic =
			"select
					NRO_OS,
					pita.NODE,
					per.name as CIDADE,
					STATUS_OS,
					NUM_ASS,
					NOME_ASSINANTE,
					TIPO_ASS,
					end as LOGRADOURO,
					bairro as BAIRRO,
					DT_INGR,
					CAUSA,
					FALHA,
					OBS_ORIGEM,
					OBS_TECNICO
			from pita
			LEFT JOIN assinante ON (assinante.cod = pita.NUM_ASS AND assinante.per =  pita.PER)
			JOIN per ON per.id = pita.PER
			JOIN area ON area.id = per.area
			where
					assinante.cep = ".$this->db->escape($cep)."
					and pita.NODE = ".$this->db->escape($node);
			if($month != 'Total')
				$magic .= " AND  pita.DT_INGR LIKE '".$this->db->escape_like_str($month)."%'";
			$magic .= "
				ORDER BY DT_INGR
				LIMIT 5000";
			$r = $this->db->query($magic);
			return $r->result_array();
	}
	function ass_search($term){
		if(is_numeric($term))
		{
			return $this->db->select('
				area.name as area,
				assinante.nome,
				assinante.per,
				assinante.cod as ass')->order_by('area','asc')->order_by('nome','asc')->
				where('assinante.cod',$term)->
				join('per','per.id = assinante.per')->
				join('area','area.id = per.area')->
				limit(200)->get('assinante')->result_array();
		}else
		{
			return $this->db->select('
				area.name as area,
				assinante.nome,
				assinante.per,
				assinante.cod as ass')->order_by('area','asc')->order_by('nome','asc')->
				like('assinante.nome',$term)->
				join('per','per.id = assinante.per')->
				join('area','area.id = per.area')->
				limit(200)->get('assinante')->result_array();
		}
	}
	function acomps($area=false,$status=false,$stage=false,$month=false){
		$magic =
		"SELECT
			`acomp`.*,
			`assinante`.`nome` as assinante,
			`area`.`name` as area, 
			`area`.`name_abbr`,
			`acomp_stage`.`name` as stage_n,
			`acomp_status`.`name` as status_n,
			CONCAT_WS('-',CONCAT_WS('-',DAY(acomp.date),MONTH(acomp.date)),YEAR(acomp.date)) as dmy
		FROM (`acomp`)
		JOIN assinante ON (acomp.ass = assinante.cod AND acomp.per = assinante.per)
		JOIN `per` ON `per`.`id` = `acomp`.`per`
		JOIN `area` ON `area`.`id` = `per`.`area`
		JOIN `acomp_status` ON `acomp_status`.`id` = `acomp`.`status`
		JOIN `acomp_stage` ON `acomp_stage`.`id` = `acomp`.`stage`
		WHERE
			1 = 1
			".(($area)?"AND area.id = ".$this->db->escape($area):'')."
			".(($status !== false && $status !== '')?"AND `acomp_status`.`id` = ".$this->db->escape($status):'')."
			".(($stage)?"AND `acomp_stage`.`id` = ".$this->db->escape($stage):'')."
			".(($month)?"AND date_format(acomp.date,'%Y-%m') = ".$this->db->escape($month):'')."
		ORDER BY acomp.id DESC";
		return $this->db->query($magic)->result_array();
	}
	function revisita_batch($log_file){
		$path = tmp_path();
		write_filen($log_file,"{$path}");
		$this->db->empty_table('revisita');
		$d = (60*60*24);
		$a = $this->db->select('NUM_ASS,PER')->group_by(array('NUM_ASS','PER'))->get('pita')->result_array();

		foreach($a as $ass){

			$oss = $this->db->
				select('id,DT_INGR,ORDINAL,STATUS_OS')->
				where('NUM_ASS',$ass['NUM_ASS'])->
				where('PER',$ass['PER'])->
				order_by('NRO_OS','ASC')->
				get('pita')->result_array();
			foreach($oss as $i => $x){

				$rev = array('pita'=>$x['id'],'pitaDT'=>$x['DT_INGR']);
				if(intval($x['ORDINAL']) === 0 && in_array($x['STATUS_OS'],array('FINALIZADA','CANCELADA'))){
					for($k = ($i-1);$k >= 0;$k--){
						$prev = $oss[$k];
						if($prev['DT_INGR'] < $x['DT_INGR'] && intval($prev['ORDINAL']) === 0 && in_array($prev['STATUS_OS'],array('FINALIZADA','CANCELADA')) ){
							$rev['prevID'] = $prev['id'];
							$rev['prevDT'] = $prev['DT_INGR'];
							$rev['prevDIFF'] = intval((strtotime($x['DT_INGR']) - strtotime($prev['DT_INGR']))/$d);
							break;
						}
					}

					for($k = ($i+1);$k<count($oss);$k++){
						$next = $oss[$k];
						if($next['DT_INGR'] > $x['DT_INGR'] && intval($next['ORDINAL']) === 0 && in_array($next['STATUS_OS'],array('FINALIZADA','CANCELADA')) ){
							$rev['nextID'] = $next['id'];
							$rev['nextDT'] = $next['DT_INGR'];
							$rev['nextDIFF'] = intval((strtotime($next['DT_INGR']) - strtotime($x['DT_INGR']))/$d);
							break;
						}
					}
				}
				write_file($path, $this->db->insert_string('revisita',$rev).";\n",'a');
			}

		}
		import_to_met($path);
		unlink($path);
	}
	function revisita($mes=false,$area=false){
		$mes = ((!$mes || strtolower($mes) === 'total')?false:date('Y-m',  strtotime($mes)));
		if(strtolower($area) === 'sim')
			$area = false;
		$key = sha1(json_encode(array('function' => 'revisita', 'a' => $this->area_id($area), 'm' => $mes)));

		$this->load->driver('cache');
		$x = $this->cache->memcached->get($key);
		if ( $x === false )
		{
			 $x = $this->_revisita($mes, $area);
			 $this->_memcacher($key, $x);
		}
		return $x;
	}
	function _memcacher($key,$val){
		$this->miss++;
		$t5 = mktime(5, 0, 0, date('n'), date('j') + 1);
		$this->cache->memcached->save($key, $val, $t5 - time());
	}
	function _revisita($mes,$area){
		if(strtolower($area) === 'sim' || strtolower($area) === 'sim tv')
			$area = false;

		if(!$area)
			$aid = 0;
		else
			$aid = $this->area_id($area);

		$magic =
			"SELECT
				COUNT(*) total,
				SUM(rev) nope,
				(SUM(rev)/COUNT(*))*100 as irm,
				area.name,
				area.color,
				area.id as id
			FROM (
					SELECT
						IF(ORDINAL>0 OR NOT (STATUS_OS = 'CANCELADA' OR STATUS_OS = 'FINALIZADA'),0,
							IF(prevDIFF IS NULL,0,
								IF(prevDIFF <= ".$this->intervalo_revisita.",1,0)
							)
						) rev,
						per.area
					FROM revisita
					JOIN pita ON pita.id = revisita.pita
					JOIN per ON pita.PER = per.id
					WHERE
						1 = 1
						".(($mes)?"AND date_format(pitaDT,'%Y-%m') = '{$mes}'":'')."
						".(($area)?"AND per.area = {$aid}":'')."
			) x
			JOIN area ON x.area = area.id
				".(($area)?"WHERE
					area.id = {$aid}":'')."
			ORDER BY irm DESC";
		$x = $this->db->query($magic)->row_array();
		if(!$area){
			$x['name'] = 'SIM';
			$x['id'] = 0;
			$x['color'] = 'black';
		}
		return $x;
	}
	function revisita_hist(){
		$magic =
		"SELECT
			COUNT(*) total,
			SUM(rev) nope,
			SUM(rev)/COUNT(*) as perc,
			area.id aid,
			area.name,
			m
		FROM (
			SELECT
				IF(ORDINAL>0 OR NOT (STATUS_OS = 'CANCELADA' OR STATUS_OS = 'FINALIZADA'),0,
					IF(prevDIFF IS NULL,0,
						IF(prevDIFF <= ".$this->intervalo_revisita.",1,0)
					)
				) rev,
				per.area,
				date_format(pitaDT,'%Y-%m') m
			FROM revisita
			JOIN pita ON pita.id = revisita.pita
			JOIN per ON pita.PER = per.id
		) x
		JOIN area ON x.area = area.id
		GROUP BY area.id,m
		ORDER BY m,area";
		return $this->db->query($magic)->result_array();
	}
	function prod_x_quali_tec($t,$mes=false,$rev = false){
		$mes = ((!$mes || strtolower($mes) === 'total')?false:date('Y-m',  strtotime($mes)));
		$key =
		sha1(
			json_encode(
				array(
					'function' => 'prod_x_quali_tec',
					't' => $t,
					'm' => $mes,
					'rev' => $rev
				)
			)
		);

		$this->load->driver('cache');
		$x = $this->cache->memcached->get($key);
		if ( $x === false )
		{
			 $x = $this->_prod_x_quali_tec($t,$mes,$rev);
			 $this->_memcacher($key, $x);
		}
		return $x;
	}
	function _prod_x_quali_tec($t,$mes,$rev){
		$magic =
			"SELECT
				COUNT(*) total,
				SUM(rev) nope,
				SUM(rev)/COUNT(*) as perc,
				TEC,NOME_TECNICO,
				(
					SELECT COUNT(DISTINCT DT_INGR)
					FROM pita
					JOIN per ON pita.PER = per.id
					WHERE
						TEC ".(($t['TEC'] !== NULL)?"= '{$t['TEC']}'":'IS NULL')."
						".(($mes)?'AND date_format(DT_INGR,"%Y-%m") = '.$this->db->escape($mes):'')."
						AND per.area = {$t['area']}
				) as ds,
				area,
				color,
				name
			FROM (
			SELECT
				".(($rev)
					?"IF(ORDINAL>0 OR NOT (STATUS_OS = 'CANCELADA' OR STATUS_OS = 'FINALIZADA'),0,
						IF(prevDIFF IS NULL,0,
							IF(prevDIFF <= ".$this->intervalo_revisita.",1,0)
						)
					) rev"
					:"IF(ORDINAL>0 OR NOT (STATUS_OS = 'CANCELADA' OR STATUS_OS = 'FINALIZADA'),0,
					IF(nextDIFF IS NULL,0,
						IF(nextDIFF <= ".$this->intervalo_revisita.",1,0)
					)
					) rev"
				).",
				TEC,NOME_TECNICO,per.area
			FROM revisita
			JOIN pita ON pita.id = revisita.pita
			JOIN per ON pita.PER = per.id
			WHERE
				TEC ".(($t['TEC'] !== NULL)?"= '{$t['TEC']}'":'IS NULL')."
				AND per.area = {$t['area']}
				".(($mes)?"AND date_format(pitaDT,'%Y-%m') = ".$this->db->escape($mes):"")."
			) x
			JOIN area ON area.id = x.area";
		return $this->db->query($magic)->row_array();
	}
	function prod_x_quali($mes=false,$area=false,$rev = false){
		if(strtolower($area) === 'sim')
			$area = false;
		$area = $this->area_id(($area));
		$mes = ((!$mes || strtolower($mes) === 'total')?false:date('Y-m',  strtotime($mes)));
		$key = sha1(json_encode(array('function' => 'prod_x_quali', 'a' => $area, 'm' => $mes,'rev'=>$rev)));

		$this->load->driver('cache');
		$x = $this->cache->memcached->get($key);
		if ( $x === false )
		{
			 $x = $this->_prod_x_quali($mes,$area,$rev);
			 $this->_memcacher($key, $x);
		}
		return $x;
	}
	function _prod_x_quali($mes,$area,$rev){
		$this->db->
			select('pita.NOME_TECNICO,pita.TEC,per.area')->
			group_by(array('pita.TEC','per.area'))->
			order_by('area,NOME_TECNICO')->
			join('per','per.id = pita.PER');
		
		if($mes)
			$this->db->where("date_format(DT_INGR,'%Y-%m') = ".$this->db->escape($mes),NULL,FALSE);
		if($area)
			$this->db->where('per.area',$area);
		
		$tecs = $this->db->get('pita')->result_array();

		$ts = array();
		if($tecs)
			foreach($tecs as $t){
				$x = $this->prod_x_quali_tec($t, $mes, $rev);
				if($x){

					$ts[] =
						array(
							'vts' => intval($x['total']),
							'xvts' => intval($x['nope']),
							'x' => round((1 - floatval($x['perc']))*100,2),
							'y' => round(intval($x['total'])/intval($x['ds']),2),
							'item' => 
								array(
									'tec' => intval($x['TEC']),
									'area'=>intval($x['area'])
								),
							'name' => $x['NOME_TECNICO'],
							'ds' => intval($x['ds']),
							'ainfo' => array('color' => $x['color'],'name'=>$x['name'])
						);
				}
			}
		return $ts;
	}
	function max_os_siga($per,$svc,$start_at){
		$max = false;
		if($svc === 'cm'){
			$max = $this->supsiga->
						select('MAX(IORDNRO) max_os')->
						where("GXVSIM.IORDENES.PERCOD", $per)->
						where("GXVSIM.IORDENES.IORDFCHING >",$start_at)->
						get('GXVSIM.IORDENES')->row_array();
		}elseif($svc === 'tv'){
			$max = $this->supsiga->
						select('MAX(RECNRO) max_os')->
						where("GXVSIM.REPARA.GRPPERCOD", $per)->
						where("GXVSIM.REPARA.RECFCHING >",$start_at)->
						get('GXVSIM.REPARA')->row_array();
		}
		if($max)
			$max = intval($max['MAX_OS']);
		else
			$max = 0;
		return $max;
	}
	function min_os_siga($per,$svc,$start_at=false){
		$min = $this->_min_os($per,$svc,$start_at);
		return intval($min);
	}
	function _min_os($per,$svc,$start_at=false){
		$min = false;
		if($svc === 'cm'){
			$this->supsiga->
				select('MIN(IORDNRO) min_os')->
				where("GXVSIM.IORDENES.PERCOD", $per);

			if($start_at)
				$this->supsiga->where("GXVSIM.IORDENES.IORDFCHING >",$start_at);

			$min = $this->supsiga->get('GXVSIM.IORDENES')->row_array();
		}elseif($svc === 'tv'){
			$this->supsiga->
				select('MIN(RECNRO) min_os')->
				where("GXVSIM.REPARA.GRPPERCOD", $per);

			if($start_at)
				$this->supsiga->where("GXVSIM.REPARA.RECFCHING >",$start_at);

			$min = $this->supsiga->get('GXVSIM.REPARA')->row_array();
		}
		if($min)
			$min = intval($min['MIN_OS']);
		else
			$min = 0;
		return $min;
	}
	function grpper($per){
		$x = $this->db->select('grp')->where('id',$per)->get('per')->row_array();
		if($x){
			return intval($x['grp']);
		}else{
			return $per;
		}
	}
	function os_cache_merge($log_file){
		$this->load_supsiga();
		$a = array();
		$currentHour = intval(date('H'));
		
		$idleTime = ( $currentHour > 21 || $currentHour < 7 );
		$nTime = time() - (60 * 60 * 24 * ( $idleTime ? 7 : 2 )  );
		$nDays = date('d-M-y', $nTime);
		
		$c = 0;
		$zero = time();
		if($this->db->count_all_results('os_cache') === 0){
			
			$d1 = new DateTime();
			$z = $this->sys_arg_int('os_cache_span');
			$d1->sub(date_interval_create_from_date_string("{$z} days"));
			$d = $d1->format('d-M-y');
			$diff = 1;
			$i = 0;
			$j = 1000;
			
			while($diff > 0){
				$b = $this->supsiga->query(
				"SELECT
					os,per,svc
				FROM (
					SELECT
						y.*,rownum rn
					FROM (
						SELECT x.*
						FROM (
							SELECT
								'cm' svc,
								GXVSIM.IORDENES.IORDNRO os,
								GXVSIM.IORDENES.PERCOD per
							FROM GXVSIM.IORDENES
							WHERE
								GXVSIM.IORDENES.IORDFCHING >= '{$d}'
								OR GXVSIM.IORDENES.IORDAGEFCH >= '{$nDays}'
							UNION
							SELECT
								'tv' as svc,
								GXVSIM.REPARA.RECNRO os,
								GXVSIM.REPARA.PERCOD per
							FROM GXVSIM.REPARA
							WHERE
								GXVSIM.REPARA.RECFCHING >= '{$d}'
								OR GXVSIM.REPARA.RECAGEFCH >= '{$nDays}'
						) x
						ORDER BY os,per,svc
					) y
					WHERE
						rownum <= {$j}
				) z
				WHERE
					rn > {$i}")->result_array();
				
				$diff = 0;
				if($b)
					$diff = count($b);
				$c = 0;
				write_filen($log_file,"{$diff} novas ordens...");
				foreach($b as $os){

					$os = array(
						'os' => intval($os['OS']),
						'per' => intval($os['PER']),
						'svc' => trim(strtolower($os['SVC']))
					);

					$key = "{$os['os']}_{$os['per']}_{$os['svc']}";

					$c++;
					write_filen($log_file,"{$key}");
					$y = $this->os_cache_get(
						array(
							'os' => $os['os'],
							'per' => $os['per'], 
							'svc' => $os['svc']
						)
					);
					if($y){
						if($this->db->insert('os_cache',$y))
							write_filen($log_file, 'success');
						else
							write_filen($log_file, 'NOPE: '.json_encode($y));
					}else{
						write_filen($log_file,"ERRRRRRRRRRRR");
					}
					write_filen($log_file,($c + $i)." gone");
				}
				$i += 1000;
				$j += 1000;
			}
		}else{
			
			$s = $zero - (60 * 60 * 48);
			$d0 = date('Y-m-d',$s);
			$d = date('d-M-y',$s);
			$a = $this->db->
					select('svc,os,per')->
					where('DATE(ingr) >=',$d0)->
					or_where('ag >=',$d0)->
					get('os_cache')->result_array();
			
			$b = $this->
					supsiga->query(
						"SELECT *
						FROM (
							SELECT
								'cm' svc,
								GXVSIM.IORDENES.IORDNRO os,
								GXVSIM.IORDENES.PERCOD per
							FROM GXVSIM.IORDENES
							WHERE
								GXVSIM.IORDENES.IORDFCHING >= '{$d}'
								OR GXVSIM.IORDENES.IORDAGEFCH >= '{$d}'
							UNION
							SELECT
								'tv' as svc,
								GXVSIM.REPARA.RECNRO os,
								GXVSIM.REPARA.PERCOD per
							FROM GXVSIM.REPARA
							WHERE
								GXVSIM.REPARA.RECFCHING >= '{$d}'
								OR GXVSIM.REPARA.RECAGEFCH >= '{$d}'
						) x
						ORDER BY dbms_random.value")->result_array();

			$a = array_map(function($x){
				return array(
					'os' => intval($x['os']),
					'per' => intval($x['per']),
					'svc' => trim(strtolower($x['svc']))
				);
			}, $a);
			
			$b = array_map(function($x){
				return array(
					'os' => intval($x['OS']),
					'per' => intval($x['PER']),
					'svc' => trim(strtolower($x['SVC']))
				);
			}, $b);
			
			write_filen($log_file,"LOCAL: ".count($a).", REMOTE: ".count($b));
			$newToMe = array_udiff($b, $a, function($a,$b){
				return strcmp(json_encode($a), json_encode($b));
			});
			
			$diff = count($newToMe);
			$c = 0;
			
			write_filen($log_file,"{$diff} ordens...");
			foreach($newToMe as $os){
				
				$key = "{$os['os']}_{$os['per']}_{$os['svc']}";
				write_filen($log_file,"{$key}");
				
				$y = $this->os_cache_get(
					array(
						'os' => $os['os'],
						'per' => $os['per'], 
						'svc' => $os['svc']
					)
				);
				
				$in_cache = 
					$this->db->
						where('os',$os['os'])->
						where('per',$os['per'])->
						where('svc',$os['svc'])->count_all_results('os_cache');
				if($y){
					if(
						( $in_cache && 
							$this->db->where('os',$os['os'])->
								where('per',$os['per'])->
								where('svc',$os['svc'])->update('os_cache',$y)
						)

						||

						$this->db->insert('os_cache',$y)
					){
						write_filen($log_file, 'successfully '.(($in_cache)?'updated':'inserted'));
					}else{
						write_filen($log_file, 'NOPE: '.var_export($y,true));
					}
				}else{
					write_filen($log_file,"ERRRRRRRRRRRR");
				}
				$c++;
				write_filen($log_file,"{$c} gone, ".($diff - $c)." to go...");
			}
		}
	}
	function newer_than($os,$per,$svc,$nope,$log_file){
		$grpper = $this->grpper($per);
		$svc = strtolower($svc);
		$d = new DateTime();
		$d->sub(date_interval_create_from_date_string($this->sys_arg_int('os_cache_span').' days'));
		$d = $d->format('d-M-y');
		$gper = (($svc === 'tv')?$grpper:$per);
		$max = $this->max_os_siga($gper, $svc,$d);
		if($os === 0){
			$os = $this->min_os_siga($gper, $svc, $d);
			if($os === 0)
				$os = $max;
		}else{
			$os++;
		}

		write_filen($log_file,"per: {$per} | svc: {$svc}");
		write_filen($log_file,"from: {$os}");
		write_filen($log_file,"to: {$max}");

		$t = time();

		for($n = $os;$n <= $max;$n++){

			$key = "{$n}_{$gper}_{$svc}";
			$y = false;

			write_filen($log_file,"{$key}");
			if(!in_array($key,$nope)){
				$y = $this->os_cache_get(array('os' => $n,'per' => $gper, 'svc' => $svc));
				$nope[] = $key;
			}
			if($y){
				write_filen($log_file,"success");
				$this->db->insert('os_cache',$y);
			}else{
				write_filen($log_file,"not found");
			}
		}
		return $nope;
	}
	function os_cache_get($o){
		$o['svc'] = strtolower($o['svc']);
		if($o['svc'] === 'cm')
			$magic =
				"SELECT
					x.IORDSTS os_status,
					x.IORDNRO os,
					x.IORDHORING h,
					x.PERCOD per,
					x.ABOCOD asscod,
					x.ABONOMAPE ass,
					x.IORDOBSORG obs_origem,
					x.IORDOBS obs_tec,
					x.IORDFCHING dt,
					x.IORDAGEFCH ag,
					x.ABOTELCOD telcod,
					x.ABOTEL tel,
					x.ABOCELCOD celcod,
					x.ABOCEL cel,
					x.IORDTPO os_tipo,
					x.IORDORG sub_tipo,
					x.ABOTPO asstipo,
					x.GRPAFIDSC grupo,
					x.ICONCOD contrato,
					x.IORDAGETUR tur,
					
					GXVSIM.ITECNICO.ITECDSC tec,
					GXVSIM.IFALLAS.IFALLDSC falha,
					GXVSIM.ICAUFAL.ICAUSFALLDSC causa,
					TRIM(GXVSIM.ICONTRA.CMMACADD) modem,
					TRIM(GXVSIM.IPAQUETE.IPAQDSC) pacote,
					NULL dec, NULL equipamento, NULL dec_tipo
				FROM (
					SELECT
						GXVSIM.IORDENES.IORDSTS,
						GXVSIM.IORDENES.IORDNRO,
						GXVSIM.IORDENES.IORDHORING,
						GXVSIM.IORDENES.PERCOD,
						GXVSIM.ABONAD.ABOCOD,
						GXVSIM.ABONAD.ABONOMAPE,
						GXVSIM.IORDENES.IORDOBSORG,
						GXVSIM.IORDENES.IORDOBS,
						GXVSIM.IORDENES.IORDFCHING,
						GXVSIM.IORDENES.IORDAGEFCH,
						GXVSIM.ABONAD.ABOTELCOD,
						GXVSIM.ABONAD.ABOTEL,
						GXVSIM.ABONAD.ABOCELCOD,
						GXVSIM.ABONAD.ABOCEL,
						GXVSIM.IORDENES.IORDTPO,
						GXVSIM.IORDENES.IORDORG,
						GXVSIM.ABONAD.ABOTPO,
						GXVSIM.ABOGRP.GRPAFIDSC,
						GXVSIM.IORDENES.ICONCOD,
						GXVSIM.IORDENES.IORDAGETUR,
						GXVSIM.IORDENES.IFALLNRO,
						GXVSIM.IORDENES.ICAUSFALLNRO,
						GXVSIM.IORDENES.ITECCOD

					FROM 
						GXVSIM.IORDENES,
						GXVSIM.ABONAD,
						GXVSIM.ABOGRP
					WHERE
						GXVSIM.IORDENES.IORDNRO = {$o['os']}
						AND GXVSIM.IORDENES.PERCOD = {$o['per']}
						
						AND GXVSIM.IORDENES.IABOCODREP = GXVSIM.ABONAD.ABOCOD
						AND GXVSIM.IORDENES.PERCOD = GXVSIM.ABONAD.PERCOD

						AND GXVSIM.ABONAD.TPOABOCODABO = GXVSIM.ABOGRP.TPOABOCOD
						AND GXVSIM.ABONAD.GRPAFICODABO = GXVSIM.ABOGRP.GRPAFICOD

					".((array_key_exists('ini',$o))
						?"AND GXVSIM.IORDENES.IORDFCHING > '{$o['ini']}'"
						:''
					)."
				) x
				LEFT JOIN GXVSIM.IFALLAS ON x.IFALLNRO = GXVSIM.IFALLAS.IFALLNRO
				LEFT JOIN GXVSIM.ICAUFAL ON x.ICAUSFALLNRO = GXVSIM.ICAUFAL.ICAUSFALLNRO
				LEFT JOIN GXVSIM.ICONTRA ON
					( x.ICONCOD = GXVSIM.ICONTRA.ICONCOD
						AND x.PERCOD = GXVSIM.ICONTRA.PERCOD )
				LEFT JOIN GXVSIM.IPAQUETE ON GXVSIM.IPAQUETE.IPAQCOD = GXVSIM.ICONTRA.IPAQCOD
				LEFT JOIN GXVSIM.ITECNICO ON 
					( x.PERCOD = GXVSIM.ITECNICO.PERCOD
						AND x.ITECCOD = GXVSIM.ITECNICO.ITECCOD )";
		elseif($o['svc'] === 'tv')
			$magic =
				"SELECT 
					x.RECSTS os_status,
					x.RECNRO os,
					x.RECHORING h,
					x.PERCOD per,
					x.ABOCOD asscod,
					x.ABONOMAPE ass,
					x.RECOBSORG obs_origem,
					x.RECOBS obs_tec,
					x.RECFCHING dt,
					x.RECAGEFCH ag,
					x.ABOTELCOD telcod,
					x.ABOTEL tel,
					x.ABOCELCOD celcod,
					x.ABOCEL cel,
					x.RECTPO os_tipo,
					x.RECFLGGEN sub_tipo,
					x.ABOTPO asstipo,
					x.GRPAFIDSC grupo,
					x.CONCOD contrato,
					x.RECAGETUR tur,
					
					GXVSIM.TECNICOS.TECDSC tec,
					GXVSIM.FALLAS.FALLDSC falha,
					GXVSIM.CAUSFALLA.CAUSFALLDSC causa,
					GXVSIM.CONABO.DECNROSER dec,
					GXVSIM.TIPEQ.EQDSC equipamento,
					GXVSIM.TIPEQ.EQSIS dec_tipo,
					TRIM(GXVSIM.PAQUETE.PAQDSC) pacote,
					NULL modem
				FROM (
					SELECT
						GXVSIM.REPARA.RECSTS,
						GXVSIM.REPARA.RECNRO,
						GXVSIM.REPARA.RECHORING,
						GXVSIM.REPARA.PERCOD,
						GXVSIM.ABONAD.ABOCOD,
						GXVSIM.ABONAD.ABONOMAPE,
						GXVSIM.REPARA.RECOBSORG,
						GXVSIM.REPARA.RECOBS,
						GXVSIM.REPARA.RECFCHING,
						GXVSIM.REPARA.RECAGEFCH,
						GXVSIM.ABONAD.ABOTELCOD,
						GXVSIM.ABONAD.ABOTEL,
						GXVSIM.ABONAD.ABOCELCOD,
						GXVSIM.ABONAD.ABOCEL,
						GXVSIM.REPARA.RECTPO,
						GXVSIM.REPARA.RECFLGGEN,
						GXVSIM.ABONAD.ABOTPO,
						GXVSIM.ABOGRP.GRPAFIDSC,
						GXVSIM.REPARA.CONCOD,
						GXVSIM.REPARA.RECAGETUR,
						GXVSIM.REPARA.FALLNRO,
						GXVSIM.REPARA.CAUSFALLNRO,
						GXVSIM.REPARA.TECCOD,
						GXVSIM.REPARA.GRPPERCOD
						
					FROM 
						GXVSIM.REPARA,
						GXVSIM.ABONAD,
						GXVSIM.ABOGRP

					WHERE
						GXVSIM.REPARA.ABOCODREP = GXVSIM.ABONAD.ABOCOD 
						AND GXVSIM.REPARA.PERCOD = GXVSIM.ABONAD.PERCOD
						AND GXVSIM.ABONAD.TPOABOCODABO = GXVSIM.ABOGRP.TPOABOCOD
						AND GXVSIM.ABONAD.GRPAFICODABO = GXVSIM.ABOGRP.GRPAFICOD

						AND GXVSIM.REPARA.RECNRO = {$o['os']}
						AND GXVSIM.REPARA.GRPPERCOD = ".$this->grpper($o['per']).
						((array_key_exists('ini',$o))
							?"
							AND GXVSIM.REPARA.RECFCHING > '{$o['ini']}'"
							:''
						)."
				) x
				LEFT JOIN GXVSIM.FALLAS ON x.FALLNRO = GXVSIM.FALLAS.FALLNRO
				LEFT JOIN GXVSIM.CAUSFALLA ON x.CAUSFALLNRO = GXVSIM.CAUSFALLA.CAUSFALLNRO
				LEFT JOIN GXVSIM.CONABO ON 
					(x.CONCOD = GXVSIM.CONABO.CONCOD
						AND x.PERCOD = GXVSIM.CONABO.PERCOD)
				LEFT JOIN GXVSIM.PAQUETE ON GXVSIM.CONABO.PAQCOD = GXVSIM.PAQUETE.PAQCOD
				LEFT JOIN GXVSIM.DECODERS ON
					(GXVSIM.CONABO.DECNROSER = GXVSIM.DECODERS.DECNROSER
						AND GXVSIM.CONABO.PERCOD = GXVSIM.DECODERS.PERCOD)
				LEFT JOIN GXVSIM.TIPEQ ON GXVSIM.DECODERS.EQTIPO = GXVSIM.TIPEQ.EQTIPO
				LEFT JOIN GXVSIM.TECNICOS ON 
					( x.GRPPERCOD = GXVSIM.TECNICOS.GRPPERCOD
						AND x.TECCOD = GXVSIM.TECNICOS.TECCOD )";
		$y = $this->os_get_addr($this->supsiga->query($magic)->row_array());

		if($y){
			$y = trim_em_all($y);
			$y = $this->os_cache_format($y, $o);
		}else{
			return null;
		}
		return $y;
	}
	function os_cache_format($y,$o){

		$t = time();
		if(!$o){
			$o = array(
				'svc' => $y['svc']
			);
		}
		$result = array(
				'os' => intval($y['OS']),
				'per' => intval($y['PER']),
				'svc' => $o['svc'],
				'tecnico' => 
						( ( array_key_exists('TEC',$y) && $y['TEC'] ) 
							?fCap(trim($y['TEC']))
							:null
						),
				'tipo' => tpName($y['OS_TIPO'],$o['svc']),
				'sub_tipo' => subTipo($y['SUB_TIPO'],$o['svc']),
				'status' => strtolower(stName($y['OS_STATUS'])),
				'ingr' => date('Y-m-d', strtotime(trim($y['DT']))).' '.h_parse($y['H']),
				'ag' =>
						(
							(!$y['AG'] || !trim($y['AG']) || trim($y['AG']) === '01-JAN-01')
							?null
							:date('Y-m-d', strtotime(trim($y['AG'])))
						),
				'asscod' => $y['ASSCOD'],
				'assname' => fCap($y['ASS']),
				'asstipo' => (($y['ASSTIPO']==='P')?'Corporativo':'Individual'),
				'assgrupo' => mb_strtolower($y['GRUPO'],'UTF-8'),
				'contrato' => intval($y['CONTRATO']),
				'cep' => fCep($y['CEP']),
				'end' => fCap($y['ADDR']),
				'bairro' => fCap($y['BAIRRO']),
				'node' => $y['NODE'],
				'obs_origem' => $y['OBS_ORIGEM'],
				'obs_tec' => $y['OBS_TEC'],
				'falha' => fCap($y['FALHA']),
				'causa' => ((array_key_exists('CAUSA',$y) 
								&& $y['CAUSA'])?fCap($y['CAUSA']):NULL),
				'tel' => fTel($y['TEL'],$y['TELCOD']),
				'cel' => fTel($y['CEL'],$y['CELCOD']),
				'serial' => $y['DEC'],
				'decoder' => $y['EQUIPAMENTO'],
				'decoder_tipo' =>
						(($y['DEC_TIPO'])?(($y['DEC_TIPO'] === 'A')?'analógico':'digital'):null),
				'pacote' => (($y['PACOTE'])?fCap($y['PACOTE']):null),
				'modem' => (($y['MODEM'])?strtoupper($y['MODEM']):null)
			);
		
		if( array_key_exists('TUR',$y) && intval($y['TUR']) ){
			$result['turno'] = intval($y['TUR']);
		}
		
		return $result;
	}
	function _sigaConn(){
		$t = time();
		$day = date('Y-m-d',$t);
		$min = date('Y-m-d H:i',$t).":00";
		$this->db->insert('siga_conn',
			array(
				'minute' => intval(($t - strtotime($day))/60),
				'day' => $day,
				'user' => 
					(($this->user && $this->user['login'])
						?$this->user['login']
						:null
					),
				'uri' => $this->uri->uri_string()
			)
		);
		$h = intval(date('G'));
		if( $h < 7 &&  $h > 0 ){
			$this->db->where('DATEDIFF(current_date,day) >',7)->delete('siga_conn');
		}
	}
	function load_supsiga(){
		if(!$this->supsiga){
			$this->supsiga = $this->load->database('supsiga', TRUE);
			$this->_sigaConn();
		}
	}
	function close_supsiga(){
		if($this->supsiga){
			$this->supsiga->close();
			$this->supsiga = null;
		}
	}
	
	function load_hits(){
		return $this->supsiga->query(
			"SELECT
				*
			FROM (
				select
					a.percod per,
					count(*) c,
					'cm' t
				from GXVSIM.cmbja A
				where
					a.percod not in (54,55,56,58,59)
					and a.cmevsts = 'I'
					and to_date(a.CMEVFCH,'DD-MM-RR') between trunc(sysdate - 30)
					and trunc(sysdate - 0)
				group by a.percod

				UNION

				select
					a.percod per,
					count(*) c,
					'tv' t
				from
					GXVSIM.casbja a
				where
					a.percod not in (54,55,56,58,59)
					and a.CASEVSTS = 'I'
					and to_date(CASEVFCHEXE,'DD-MM-RR') between trunc(sysdate - 30)
					and trunc(sysdate - 0)
				group by a.percod
			) x")->result_array();
	}
	function tecnico($tec,$area){
		$the_tec = 
			$this->db->
				select('NOME_TECNICO as NOME')->
				join('per','per.id = pita.PER')->
				where('per.area',$area)->
				where("TEC = '{$tec}'",NULL,FALSE)->
				get('pita')->row_array();
		$f_os = $this->db->
				select(
				"MIN(DT_INGR) as since,
				MAX(DT_INGR) as until,
				COUNT(*) as c,
				(
					SELECT COUNT(*)
					FROM
					(
						SELECT DISTINCT(DT_INGR)
						FROM pita
						JOIN per ON pita.PER = per.id
						WHERE
							per.area = {$area} AND TEC = '{$tec}'
					) a
				) as dias",FALSE)->
				join('per','pita.PER = per.id')->
				where('per.area',$area)->
				where("TEC = '{$tec}'",NULL,FALSE)->
				get('pita')->row_array();
		return array_merge($the_tec,$f_os);
	}
	function tecnico_mes($tec,$area,$m){
		$mes = date('Y-m',strtotime($m));
		$magic =
		"SELECT
			p1.id,
			p1.NRO_OS,
			p1.SERVICO,
			p1.PER,
			p1.NUM_ASS,
			p1.NOME_ASSINANTE,
			p1.DT_INGR,
			p1.CAUSA,
			p1.FALHA,
			p1.MOTIVO,
			p1.OBS_TECNICO,
			p1.TEC,
			p1.NOME_TECNICO,
			p2.NRO_OS as NRO_OS2,
			p2.SERVICO as SERVICO2,
			p2.PER as PER2,
			p2.DT_INGR as DT_INGR2,
			p2.CAUSA as CAUSA2,
			p2.FALHA as FALHA2,
			p2.MOTIVO as MOTIVO2,
			p2.TEC as TEC2,
			p2.OBS_TECNICO as OBS_TECNICO2,
			p2.NOME_TECNICO as NOME_TECNICO2
		FROM pita p1
		JOIN revisita ON p1.id = revisita.pita
		LEFT JOIN pita p2 ON 
			(p2.id = revisita.nextID 
				AND revisita.nextDIFF <= ".$this->intervalo_revisita.")
		JOIN per ON p1.PER = per.id
		WHERE
			p1.TEC = '{$tec}'
			AND per.area = {$area}
			AND date_format(p1.DT_INGR,'%Y-%m') = '{$mes}'
		GROUP BY p1.id
		ORDER BY id DESC";

		$r =  $this->db->query($magic)->result_array();
		$x = array(
			'hist' => array(),
			'diag' =>
				array(
					'causas'=>array('a'=>array(),'b'=>array()),
					'falhas'=>array('a'=>array(),'b'=>array()),
					'motivos'=>array('a'=>array(),'b'=>array()),
					'total'=>array('ok'=>0,'not_ok'=>0),
					'cm'=>array('a'=>array('ok'=>0,'not_ok'=>0),
								'b'=>0),
					'ptv'=>array('a'=>array('ok'=>0,'not_ok'=>0),
								'b'=>0)
				)
			);

		foreach ($r as $y) {
			$y['TEC'] = $y['TEC'];
			$s = strtolower($y['SERVICO']);
			if(!$y['DT_INGR2']){
				unset($y['DT_INGR2']);
				unset($y['COD_CAUSA2']);
				unset($y['COD_FALHA2']);
				unset($y['COD_MOTIVO2']);
				unset($y['CAUSA2']);
				unset($y['FALHA2']);
				unset($y['MOTIVO2']);
				unset($y['TEC2']);
				unset($y['OBS_TECNICO2']);
				unset($y['NOME_TECNICO2']);
				unset($y['NRO_OS2']);
				unset($y['PER2']);
				unset($y['SERVICO2']);
				$sts = 'ok';
			}else{
				$sts = 'not_ok';
				$x['diag'][strtolower($y['SERVICO2'])]['b']++;
				$y['DT_INGR2'] = date('d-m-Y',strtotime($y['DT_INGR2']));
				$y['TEC2'] = $y['TEC2'];
			}
			$y['DT_INGR'] = date('d-m-Y',strtotime($y['DT_INGR']));

			$x['diag'][$s]['a'][$sts]++;
			$x['diag']['total'][$sts]++;

			$r = $this->db->query(
					"SELECT COUNT(*) as c
					FROM
					(
						SELECT DISTINCT(DT_INGR)
						FROM pita
						JOIN per ON per.id = pita.PER
						WHERE
							TEC = '{$tec}'
							AND per.area = {$area}
							AND date_format(DT_INGR,'%Y-%m') = '{$mes}'
					) a")->row_array();
			$x['ndays'] = intval($r['c']);

			$x = tcmes1($x,$y,$sts);
			if($sts === 'not_ok')
				$x = tcmes2($x,$y);
			$x['hist'][] = $y;
		}
		$x['diag']['causas']['a'] = cfmSort($x['diag']['causas']['a'],true);
		$x['diag']['causas']['b'] = cfmSort($x['diag']['causas']['b']);
		$x['diag']['falhas']['a'] = cfmSort($x['diag']['falhas']['a'],true);
		$x['diag']['falhas']['b'] = cfmSort($x['diag']['falhas']['b']);
		$x['diag']['motivos']['a'] = cfmSort($x['diag']['motivos']['a'],true);
		$x['diag']['motivos']['b'] = cfmSort($x['diag']['motivos']['b']);
		return $x;
	}
	
	function calc_new_base($log_file){
		$mm = date('Y-m').'-01';
		$this->db->query("DELETE FROM cbase WHERE month = '{$mm}'");
		$magic =
			"INSERT INTO cbase
			(id,ctype,cservice,month,area,base)
			select
				0 as id,
				ctype.id as ctype,
				cservice.id as cservice,
				'{$mm}' as month,
				per.area,
				COUNT(*) as base
			from assinante
			JOIN per ON assinante.per = per.id
			JOIN ctype ON ctype.def_name = assinante.tipo
			JOIN cservice ON cservice.def_name = assinante.produto
			WHERE
				ativo = 1
				AND produto IS NOT NULL
			group by tipo,produto,per.area
			ORDER by area,ctype,cservice";
		return $this->db->query($magic);
	}
	function per_group($per){
		$grp = $this->db->select('grp')->where('id',$per)->get('per')->row_array();
		return intval($grp['grp']);
	}
	function get_ass_addr($cod, $per, $canUseLocalData = false){
		$x = null;

        if ($canUseLocalData) {
            $x = $this->db->
                    select('cod, per, cep, end, comp, bairro, node')->
                    where('cod', $cod)->
                    where('per', $per)->
                    get('assinante')->row_array();
        }

		if ($x) {

			$x['ref'] = null;
			$x['cep'] = fCep($x['cep']);

		} else {

			$this->load_supsiga();
			$x = $this->supsiga->query(
				"SELECT
					GXVSIM.ABONAD.ABOCALZIP cep,
					
					GXVSIM.TPOCAL.TPOCALDSC tp_logr,
					GXVSIM.CALLES.CALNOM logr,
					GXVSIM.ABONAD.ABOCALNRO nro,
					
					GXVSIM.ABONAD.AC1 c1,
					GXVSIM.ABONAD.ACV1 n1,
					
					GXVSIM.ABONAD.AC2 c2,
					GXVSIM.ABONAD.ACV2 n2,
					
					GXVSIM.ABONAD.AC3 c3,
					GXVSIM.ABONAD.ACV3 n3,
					
					GXVSIM.ABONAD.AC4 c4,
					GXVSIM.ABONAD.ACV4 n4,
					
					GXVSIM.ABONAD.AC5 c5,
					GXVSIM.ABONAD.ACV5 n5,
					
					GXVSIM.ABONAD.AC6 c6,
					GXVSIM.ABONAD.ACV6 n6,
					
					GXVSIM.ABONAD.ABOCALAMPNRO ref,
					
					GXVSIM.URBANI.URBDSC bairro,
					GXVSIM.MANZAN.MANDSC node
					
				FROM 
					GXVSIM.ABONAD,
					GXVSIM.MANZAN,
					GXVSIM.CALLES,
					GXVSIM.URBANI,
					GXVSIM.TPOCAL

				WHERE
					GXVSIM.ABONAD.ABOCOD = {$cod}
					AND GXVSIM.ABONAD.PERCOD = {$per}

					AND GXVSIM.MANZAN.ESTACOD = GXVSIM.ABONAD.ESTACOD
					AND GXVSIM.MANZAN.DISTCOD = GXVSIM.ABONAD.DISTCOD
					AND GXVSIM.MANZAN.SECNRO = GXVSIM.ABONAD.SECNRO
					AND GXVSIM.MANZAN.MANNRO = GXVSIM.ABONAD.MANNRO

					AND GXVSIM.CALLES.ESTACOD = GXVSIM.ABONAD.ESTACOD
					AND GXVSIM.CALLES.DISTCOD = GXVSIM.ABONAD.DISTCOD
					AND GXVSIM.CALLES.CALCOD = GXVSIM.ABONAD.ABOCAL

					AND GXVSIM.URBANI.ESTACOD = GXVSIM.ABONAD.ESTACOD
					AND GXVSIM.URBANI.DISTCOD = GXVSIM.ABONAD.DISTCOD
					AND GXVSIM.URBANI.URBCOD = GXVSIM.ABONAD.URBCOD

					AND GXVSIM.TPOCAL.TPOCALCOD = GXVSIM.CALLES.TPOCALCOD
				")->row_array();

			$x = trim_em_all($x, TRUE);
			
			$comp = array();
			
			for($i = 1;$i < 7;$i++){
				$k1 = "c{$i}";
				$k2 = "n{$i}";
				
				
				if(strlen($x[$k1]) && strlen($x[$k2])){
					$c = trim($x[$k1].' '.$x[$k2]);
					$comp[] = $c;
					
				}
				unset($x[$k1]);
				unset($x[$k2]);
			}

			$x['comp'] = join(', ',$comp);
			$x['end'] = trim($x['tp_logr']).' '.trim($x['logr']).' '.trim($x['nro']);

			foreach($x as $j => $k)
				$x[$j] = trim($k);
			
			unset($x['tp_logr']);
			unset($x['logr']);
			unset($x['nro']);
		}
		return $x;
	}
	function filter_scheduled_oss($oss){
		$o = array();
		foreach($oss as $x){
			if(!$this->os_has_schedule($x)){
				$o[] = $x;
			}
		}
		return $o;
	}
	function os_has_schedule($os){
		return ($this->db->where('os',$os['os'])->where('per',$os['per'])->where('svc',$os['svc'])->count_all_results('tec_schedule_os') > 0);
	}
	function reset_dates($log_file){
		$this->db->query('TRUNCATE TABLE dates');
		$date = new DateTime($this->begin);
		$d = $date->format('Y-m-d');
		while($d <= date('Y-m-d')){
			$this->db->insert('dates',array('d'=>$d));
			date_add($date,date_interval_create_from_date_string("1 days"));
			$d = $date->format('Y-m-d');
		}
		// ~~~ ~~ ~~
		$this->db->query('TRUNCATE TABLE mes');
		$date = new DateTime($this->begin);
		while($date->format('Y-m') <= date('Y-m')){
			$this->db->insert('mes',
				array(
					'm'=>$date->format('Y-m'),
					'mm'=>$date->format('Y-m').'-01'
				)
			);
			date_add($date,date_interval_create_from_date_string("1 month"));
		}
	}
	function calc_bases(){
		$mm = date('Y-m').'-01';
		$this->db->query("DELETE FROM cbase WHERE month = '{$mm}'");
		$magic =
			"INSERT INTO cbase
			(id,ctype,cservice,month,area,base)
			select
			0 as id,ctype.id as ctype,cservice.id as cservice,'{$mm}' as month,per.area,COUNT(*) as base
			from base_atual
			JOIN per ON base_atual.COD_OPERADORA = per.id
			JOIN ctype ON ctype.alt_name = base_atual.TIPO
			JOIN cservice ON cservice.alt_name = base_atual.PRODUTO
			WHERE
				PRODUTO IS NOT NULL
			group by TIPO,PRODUTO,per.area
			ORDER by area,ctype,cservice";
		return $this->db->query($magic);
	}
	function lday_backlog(){
		$lday = $this->db->select('MAX(DATE(DT_GERACAO)) as d',FALSE)->get('backlog')->row_array();
		if($lday && $lday['d'])
			return $lday['d'];
		else
			return date('Y-m-d');
	}
	function lday_backlog_r(){
		$d = $this->db->select_max('d')->get('backlog_log')->row_array();
		if($d)
			return $d['d'];
		else
			return date('Y-m-d');
	}
	function backlog_r_h(){
		$set = $this->db->query('SELECT MIN(d) as d1, MAX(d) as d2 FROm backlog_log')->row_array();

		$ds = array();
		$oneday = date_interval_create_from_date_string("1 day");
		$i = $set['d1'];
		$ii = new DateTime($i);
		$w = week_of_month($i);
		$w = str_pad($w, 2, '0', STR_PAD_LEFT).'-'.date('M-y',strtotime($i));
		$lweek = $w;
		$c = 0;
		$cc = 0;
		do{
			if($cc){
				$w = week_of_month($i);
				$w = str_pad($w, 2, '0', STR_PAD_LEFT).'-'.date('M-y',strtotime($i));
			}

			if($w !== $lweek){
				$lweek = $w;
				$c++;
			}

			$ds[$c]['d'][] = $i;
			$ds[$c]['s'] = $w;

			date_add($ii,$oneday);
			$i = $ii->format('Y-m-d');

			$cc++;


		}while($i <= $set['d2']);


		$result = array('categories'=>array(),'series'=>array());
		$aux = 0;
		foreach ($ds as $d){
			$j = ( count($d['d']) - 1 ) ;
			$zero = true;
			$sem = sem_format($d['s']);
			while ( $zero && $j >= 0 ){
				$tmp = array();
				$s = $this->db->query(
					"SELECT
						os_status.status as status,
						os_status.color as color,
						os_status.order as st_order,
						IFNULL(corp,0) as corp,
						IFNULL(b2.c,0) as c
					FROM os_status
					LEFT JOIN
							(
								SELECT xxx.status,COUNT(*) as c,SUM(corp) as corp
								FROM
								(
									SELECT
											UCASE(status) as status,
											corp
									FROM backlog_log b2
									WHERE
											b2.d = '{$d['d'][$j]}'
								) xxx
								GROUP BY UCASE(status)
							) b2 ON os_status.status2 = b2.status
					ORDER BY st_order DESC")->result_array();
				foreach($s as $i => $x){
					if($aux === 0)
						$result['series'][$i] = array( 'name' => $x['status'],'data'=>array(),'color'=>$x['color'] );
					$y = intval($x['c']);
					if($y > 0) $zero = false;
					$tmp[$i] = array( 'y' => $y,'corp' => intval($x['corp']),'name' => $sem );
				}

			 if(!$zero)
				foreach($s as $i => $x)
					$result['series'][$i]['data'][] = $tmp[$i];
			 $j--;
			 $aux++;
			}
			if(!$zero)
				$result['categories'][] = $sem;
		}
		$result['series'][$i]['data'] = $result['series'][$i]['data'];
		$result['categories'] = $result['categories'];
		return $result;
	}
	function backlog_h(){
		$set = $this->db->query(
			'SELECT 
				MIN(DATE(DT_GERACAO)) as d1, 
				MAX(DATE(DT_GERACAO)) as d2 
			FROM backlog')->row_array();

		$ds = array();
		$oneday = date_interval_create_from_date_string("1 day");
		$i = $set['d1'];
		$ii = new DateTime($i);
		$w = week_of_month($i);
		$w = str_pad($w, 2, '0', STR_PAD_LEFT).'-'.date('M-y',strtotime($i));
		$lweek = $w;
		$c = 0;
		$cc = 0;
		do{
			if($cc){
				$w = week_of_month($i);
				$w = str_pad($w, 2, '0', STR_PAD_LEFT).'-'.date('M-y',strtotime($i));
			}

			if($w !== $lweek){
				$lweek = $w;
				$c++;
			}

			$ds[$c]['d'][] = $i;
			$ds[$c]['s'] = $w;

			date_add($ii,$oneday);
			$i = $ii->format('Y-m-d');

			$cc++;


		}while($i <= $set['d2']);


		$result = array('categories'=>array(),'series'=>array());
		$aux = 0;
		foreach ($ds as $d){
			$j = ( count($d['d']) - 1 ) ;
			$zero = true;
			$sem = sem_format($d['s']);
			while ( $zero && $j >= 0 ){
				$tmp = array();
				$s = $this->db->query(
					"SELECT
						os_status.status as status,
						os_status.color as color,
						os_status.order as st_order,
						IFNULL(corp,0) as corp,
						IFNULL(b2.c,0) as c
					FROM os_status
					LEFT JOIN
							(
								SELECT xxx.status,COUNT(*) as c,SUM(corp) as corp
								FROM
								(
									SELECT
											STATUS as  status,
											IF(PESSOA = 'JURIDICA',1,0) as corp
									FROM backlog b2
									WHERE
											b2.DT_GERACAO LIKE '{$d['d'][$j]}%'
								) xxx
								GROUP BY status
							) b2 ON os_status.status = b2.status
					ORDER BY st_order DESC")->result_array();
				foreach($s as $i => $x){
					if($aux === 0)
						$result['series'][$i] = array( 'name' => $x['status'],'data'=>array(),'color'=>$x['color'] );
					$y = intval($x['c']);
					if($y > 0) $zero = false;
					$tmp[$i] = array( 'y' => $y,'corp' => intval($x['corp']),'name' => $sem );
				}

			 if(!$zero)
				foreach($s as $i => $x)
					$result['series'][$i]['data'][] = $tmp[$i];
			 $j--;
			 $aux++;
			}
			if(!$zero)
				$result['categories'][] = $sem;
		}
		$result['series'][$i]['data'] = $result['series'][$i]['data'];
		$result['categories'] = $result['categories'];
		return $result;
	}
	function backlog_extract($a,$d,$s){
		$this->db->select(
			"COD_OPERADORA as per,
			COD_ASS as ass,
			PESSOA as pessoa,
			TIPO as tipo,
			DT_CAD_ASS as dt_cad,
			STATUS as status,
			DT_INGR_OS_PTV as ing_tv,
			DT_INGR_OS_CM as ing_cm,
			SERVICO as svc,
			NOME,
			TEL,
			CEL,
			END,
			BAIRRO,
			CEP,
			NODE,
			per.name as permissora,
			IFNULL(MOTIVO_CM,'') as MOTIVO_CM,
			IFNULL(MOTIVO_PTV,'') as MOTIVO_PTV,
			IFNULL(OBSERVACAO_PTV,'') as OBSERVACAO_PTV,
			IFNULL(OBSERVACAO_CM,'') as OBSERVACAO_CM",FALSE)->join('per','per.id = backlog.COD_OPERADORA');
		if($a)
			$this->db->where('per.area',$a);
		if($d)
			$this->db->like('DT_GERACAO',$d);
		if($s)
			$this->db->where('STATUS',$s);
		$r = $this->db->get('backlog')->result_array();
		if($r){
			$this->load_supsiga();
			foreach($r as $i => $x){
				$z = $this->supsiga->select('ABODOCNRO')->where('ABOCOD',$x['ass'])->where('PERCOD',$x['per'])->get('GXVSIM.ABONAD')->row_array();
				if($z)
					$r[$i]['doc'] = $z['ABODOCNRO'];
			}
			return $r;
		}else
			return array();
		
	}
	function backlog_aging_r($d,$a){
		$total = ($a === 'SIM');
		$magic =
		"SELECT
			os_status.status as status,
			os_status.color as color,
			os_status.order as st_order,
			IFNULL(corp,0) as corp,
			IFNULL(b2.c,0) as c,
			backlog_aging.series as aging
		FROM os_status
		JOIN backlog_aging
		LEFT JOIN
		(
			SELECT
				xxx.status,
				aging,
				COUNT(*) as c,
				SUM(corp) as corp
			FROM
			(
				SELECT
					IF(dias < 04,'00 - 03',
						IF(dias < 11,'04 - 10',
							IF(dias < 21,'11 - 20',
								IF(dias < 31,'21 - 30',
									IF(dias < 61,'31 - 60',
										'> 60'))))) as aging,
					status,
					corp
				FROM (
					SELECT
						UCASE(status) as status,
						corp,
						DATEDIFF(d,IFNULL(ag,ingr)) as dias
					FROM backlog_log b2
					".((!$total)? "JOIN per ON per.id = b2.per":'')."
					WHERE
						b2.d = '{$d}'
						".((!$total)? "AND per.area = {$this->areas['nome'][$a]['id']}":'')."
				) yyy
			) xxx
			GROUP BY status,aging
		) b2 ON (os_status.status2 = b2.status AND b2.aging = backlog_aging.series)
		ORDER BY st_order DESC,backlog_aging.series";
		$b = $this->db->query($magic)->result_array();

		$lstatus = -1;
		$i = -1;
		$result = array('series' => array(),'categories' => array());
		foreach($b as $x){
			if($lstatus !== $x['status']){
				$i++;
				$result['series'][$i] = array('name' => $x['status'],'data'=>array(),'color'=>$x['color']);
				$lstatus = $x['status'];
			}
			$result['series'][$i]['data'][] = array( 'y' => intval($x['c']), 'corp' => intval($x['corp']), 'name' => $x['aging'] );

			if(!in_array($x['aging'], $result['categories']))
				$result['categories'][] = $x['aging'];
		}
		return $result;
	}
	function backlog_aging_d($a){
		$total = ($a === 'SIM');
		$magic =
		"SELECT
			os_status.status as status,
			os_status.color as color,
			os_status.order as st_order,
			IFNULL(b2.c,0) as c,
			backlog_aging.series as aging
		FROM os_status
		JOIN backlog_aging
		LEFT JOIN
		(
			SELECT
				xxx.status,
				aging,
				COUNT(*) as c
			FROM
			(
				SELECT
					IF(dias < 04,'00 - 03',
						IF(dias < 11,'04 - 10',
							IF(dias < 21,'11 - 20',
								IF(dias < 31,'21 - 30',
									IF(dias < 61,'31 - 60',
										'> 60'))))) as aging,
					status
				FROM (
					SELECT
						UCASE(os_status) as status,
						DATEDIFF(CURRENT_TIMESTAMP,ingr) as dias
					FROM backlog_d b2
					".(
						(!$total)?
						"JOIN per ON per.id = b2.per
						WHERE
							per.area = {$this->areas['nome'][$a]['id']}
							AND falha != 'Mudança De Nivel'"
						:''
					)."
				) yyy
			) xxx
			GROUP BY status,aging
		) b2 ON (os_status.status2 = b2.status AND b2.aging = backlog_aging.series)
		ORDER BY st_order DESC,backlog_aging.series";

		$b = $this->db->query($magic)->result_array();

		$lstatus = -1;
		$i = -1;
		$result = array('series' => array(),'categories' => array());
		foreach($b as $x){
			if($lstatus !== $x['status']){
				$i++;
				$result['series'][$i] = array('name' => $x['status'],'data'=>array(),'color'=>$x['color']);
				$lstatus = $x['status'];
			}
			$result['series'][$i]['data'][] = array( 'y' => intval($x['c']),'name' => $x['aging'] );

			if(!in_array($x['aging'], $result['categories']))
				$result['categories'][] = $x['aging'];
		}
		return $result;
	}
	function backlog_aging($d,$a){
		$total = ($a === 'SIM');
		$magic =
		"SELECT
			os_status.status as status,
			os_status.color as color,
			os_status.order as st_order,
			IFNULL(corp,0) as corp,
			IFNULL(b2.c,0) as c,
			backlog_aging.series as aging
		FROM os_status
		JOIN backlog_aging
		LEFT JOIN
		(
			SELECT
				xxx.status,
				aging,
				COUNT(*) as c,
				SUM(corp) as corp
			FROM
			(
				SELECT
					IF(dias < 04,'00 - 03',
						IF(dias < 11,'04 - 10',
							IF(dias < 21,'11 - 20',
								IF(dias < 31,'21 - 30',
									IF(dias < 61,'31 - 60',
										'> 60'))))) as aging,
					status,
					corp
				FROM (
					SELECT
						STATUS as status,
						IF(PESSOA = 'JURIDICA',1,0) as corp,
						IF(IFNULL(QTDE_DIAS_ABERTO_OS_PTV,0) > IFNULL(QTDE_DIAS_ABERTO_OS_CM,0),IFNULL(QTDE_DIAS_ABERTO_OS_PTV,0),IFNULL(QTDE_DIAS_ABERTO_OS_CM,0)) as dias
					FROM backlog b2
					".((!$total)? "JOIN per ON per.id = b2.COD_OPERADORA":'')."
					WHERE
						b2.DT_GERACAO LIKE '{$d}%'
						".((!$total)? "AND per.area = {$this->areas['nome'][$a]['id']}":'')."
				) yyy
			) xxx
			GROUP BY status,aging
		) b2 ON (os_status.status = b2.status AND b2.aging = backlog_aging.series)
		ORDER BY st_order DESC,backlog_aging.series";
		$b = $this->db->query($magic)->result_array();
		$lstatus = -1;
		$i = -1;
		$result = array('series' => array(),'categories' => array());
		foreach($b as $x){
			if($lstatus !== $x['status']){
				$i++;
				$result['series'][$i] = array('name' => $x['status'],'data'=>array(),'color'=>$x['color']);
				$lstatus = $x['status'];
			}
			$result['series'][$i]['data'][] = array( 'y' => intval($x['c']), 'corp' => intval($x['corp']), 'name' => $x['aging'] );

			if(!in_array($x['aging'], $result['categories']))
				$result['categories'][] = $x['aging'];
		}
		return $result;
	}
	function backlog_timeline_d($area){
		$total = ($area === 'SIM');
		$magic =
		"SELECT
			dia dt,SUM(c) as c
		FROM backlog_d_saldo
		".((!$total)?
		"JOIN per ON per.id = backlog_d_saldo.per
		WHERE
			per.area = {$this->areas['nome'][$area]['id']}":''
		)."
		group by dia
		ORDER BY dt";
		$r = $this->db->query($magic)->result_array();
		$result = array(
					'name' => 'Evolução do Backlog',
					'series' =>
							array(
								array(
									'name' => 'Pendentes',
									'data'=>array(),
									'type' => 'area'
								)
							)
				);
		foreach($r as $x => $y){
			$result['series'][0]['data'][] = array( (strtotime($y['dt']) + ( 60 * 60 * 12 )) * 1000,intval($y['c']));
		}
		return $result;
	}
	function backlog_timeline_r($area){
		$total = ($area === 'SIM');
		$magic =
		"SELECT
			d dt,COUNT(*) as c
		FROM backlog_log
		".((!$total)? "JOIN per ON per.id = backlog_log.per":'')."
		".((!$total)?
		"WHERE
			per.area = {$this->areas['nome'][$area]['id']}":'')."
		group by d
		ORDER BY dt";
		$r = $this->db->query($magic)->result_array();
		$result = array(
					'name' => 'Evolução do Backlog',
					'series' =>
							array(
								array('name' => 'Pendentes','data'=>array(),'type' => 'area')
							)
				);
		foreach($r as $x => $y){
			$result['series'][0]['data'][] = array( (strtotime($y['dt']) + ( 60 * 60 * 12 )) * 1000,intval($y['c']));
		}
		return $result;
	}
	function backlog_timeline($area){
		$total = ($area === 'SIM');
		$magic =
		"SELECT
			DATE(DT_GERACAO) dt,COUNT(*) as c
		FROM backlog
		".((!$total)? "JOIN per ON per.id = COD_OPERADORA":'')."
		".((!$total)?
		"WHERE
			per.area = {$this->areas['nome'][$area]['id']}":'')."
		group by DATE(DT_GERACAO)
		ORDER BY dt";
		$r = $this->db->query($magic)->result_array();
		$result = array(
					'name' => 'Evolução do Backlog',
					'series' =>
							array(
								array('name' => 'Pendentes','data'=>array(),'type' => 'area')
							)
				);
		foreach($r as $x => $y){
			$result['series'][0]['data'][] = array((strtotime($y['dt']) + ( 60 * 60 * 12 )) * 1000,intval($y['c']));
		}
		return $result;
	}
	function backlog_d($d){
		$b = $this->db->
				query(
					"SELECT
							a1.name as area,
							os_status.status as status,
							os_status.color as color,
							os_status.order as st_order,
							IFNULL(corp,0) as corp,
							IFNULL(b2.c,0) as c
					FROM os_status
					JOIN area a1
					LEFT JOIN
						(
							SELECT 
								xxx.status,
								xxx.area,
								xxx.aid,
								COUNT(*) as c,
								SUM(corp) as corp
							FROM
							(
								SELECT
									STATUS as  status,
									area.name as area,
									area.id as aid,
									IF(PESSOA = 'JURIDICA',1,0) as corp
								FROM backlog b2
								JOIN per ON per.id = b2.COD_OPERADORA
								JOIN area ON area.id = per.area
								WHERE
									b2.DT_GERACAO LIKE '{$d}%'
							) xxx
							GROUP BY area,status
						) b2 ON (os_status.status = b2.status AND a1.id = b2.aid)
					ORDER BY st_order DESC,area")->result_array();
		return $b;
	}
	function backlog_d_r($d){
		$magic = 
				"SELECT
					a1.name as area,
					os_status.status as status,
					os_status.color as color,
					os_status.order as st_order,
					IFNULL(corp,0) as corp,
					IFNULL(b2.c,0) as c
				FROM os_status
				JOIN area a1
				LEFT JOIN
					(
						SELECT xxx.status,xxx.area,xxx.aid,COUNT(*) as c,SUM(corp) as corp
						FROM
						(
										SELECT
												UCASE(b2.status) as status,
												area.name as area,
												area.id as aid,
												corp
										FROM backlog_log b2
										JOIN per ON per.id = b2.per
										JOIN area ON area.id = per.area
										WHERE
												b2.d = '{$d}'
						) xxx
						GROUP BY area,status
					) b2 ON (os_status.status2 = b2.status AND a1.id = b2.aid)
				ORDER BY st_order DESC,area";
		return $this->db->query($magic)->result_array();
	}
	function lday_backlog_descon(){
		$d = $this->db->query(
			"SELECT
				MAX(dia) as d
			FROM backlog_d_saldo"
		)->row_array();
		return (($d)?$d['d']:null);
	}
	function backlog_d_produto($d){
		return $this->db->query(
			"SELECT
				area.id as aid,
				area.name as area,
				produto,
				SUM(c) as c
			FROM backlog_d_saldo
			JOIN per ON per.id = backlog_d_saldo.per
			JOIN area ON area.id = per.area
			WHERE
				backlog_d_saldo.dia = '{$d}'
			GROUP BY area.id,produto")->result_array();
	}
	function backlog_descon($d){
		$magic = 
			"SELECT
				a1.name as area,
				os_status.status as status,
				os_status.color as color,
				os_status.order as st_order,
				IFNULL(b2.c,0) as c
			FROM os_status
			JOIN area a1
			LEFT JOIN (
				SELECT xxx.status,xxx.area,xxx.aid,SUM(c) as c
				FROM
				(
					SELECT
						UCASE(b2.status) as status,
						area.name as area,
						area.id as aid,
						c
					FROM backlog_d_saldo b2
					JOIN per ON per.id = b2.per
					JOIN area ON area.id = per.area
					WHERE
						b2.dia = '{$d}'
				) xxx
				GROUP BY area,status
			) b2 ON (os_status.status2 = b2.status AND a1.id = b2.aid)
			ORDER BY st_order DESC,area";
		return $this->db->query($magic)->result_array();
	}
	function os_real_status ($o) {
		$this->load->driver('cache');
		
		$key = "osSIGAStatus:{$o['per']}:{$o['os']}:".strtolower($o['svc']);
		$info = $this->cache->memcached->get($key);
		
		if ($info !== false) return $info;

		$this->load_supsiga();
		if(strtolower($o['svc']) === 'cm'){
			$x = $this->supsiga->
						select('GXVSIM.IORDENES.IORDSTS st')->
						where("GXVSIM.IORDENES.PERCOD",$o['per'])->
						where("GXVSIM.IORDENES.IORDNRO",$o['os'])->
						get('GXVSIM.IORDENES')->row_array();
		} else {
			$x = $this->supsiga->
					select('GXVSIM.REPARA.RECSTS st')->
					where("GXVSIM.REPARA.GRPPERCOD",$this->grpper($o['per']))->
					where("GXVSIM.REPARA.RECNRO",$o['os'])->
					get('GXVSIM.REPARA')->row_array();
		}

		if ($x && $x['ST']) {
			$st = strtolower(stName($x['ST']));
			$this->db->
					where('os',$o['os'])->
					where('per',$o['per'])->
					where('svc',$o['svc'])->
					where('status !=',$st)->
					update('os_cache',array('status' => $st));
			$info = $st;
		} else {
			$info = null;
		}

		$this->cache->memcached->save($key, $info, 60 * 2);
		return $info;
	}
	function os_open($o, $st = 'P', $dtf = 'd/m/y', $show_future = false){
		$this->load_supsiga();
		$pers = array();

		if(strtolower($o['svc']) === 'cm'){
			$r = $this->supsiga->
					select('GXVSIM.IORDENES.IORDSTS st, '
						. 'GXVSIM.IORDENES.IORDAGEFCH ag, '
						. 'GXVSIM.IORDENES.IORDAGETUR tur')->
					where("GXVSIM.IORDENES.PERCOD", $o['per'])->
					where("GXVSIM.IORDENES.IORDNRO", $o['os'])->
					get('GXVSIM.IORDENES')->row_array();

			if(!$r){
				return null;
			}

			$newst = strtoupper(trim($r['ST']));
			$newag = strtotime($r['AG']);
			$newag2 = date('Y-m-d', $newag);
			
			$oscache = array(
					'status' => strtolower(stName($newst)), 
					'ag' => $newag2 
				);
			
			if (intval($r['TUR'])) {
				$oscache['turno'] = intval($r['TUR']);
			}
			
			$this->db->where('os', $o['os']);
			$this->db->where('per', $o['per']);
			$this->db->where('svc', 'cm');
			$this->db->update('os_cache', $oscache);

			$this->db->where('NRO_OS',$o['os']);
			$this->db->where('PER',$o['per']);
			$this->db->where('SERVICO','CM');
			$this->db->update('pita',
				array(
					'STATUS_OS' =>  strtoupper(stName($newst)),
					'DT_AGEND' => $newag2 
				) 
			);
		
			$magic =
				"SELECT
					GXVSIM.IORDENES.IORDNRO os,
					GXVSIM.IORDENES.IORDHORING h,
					'CM' b,
					GXVSIM.IORDENES.IORDSTS os_status,
					GXVSIM.IORDENES.PERCOD per,
					GXVSIM.ABONAD.ABOCOD asscod,
					GXVSIM.ABONAD.ABONOMAPE ass,
					GXVSIM.IORDENES.IORDOBSORG obs_origem,
					GXVSIM.IORDENES.IORDOBS obs_tec,
					GXVSIM.IORDENES.IORDFCHING dt,
					GXVSIM.IORDENES.IORDAGEFCH ag,
					GXVSIM.IFALLAS.IFALLDSC falha,
					GXVSIM.ABONAD.ABOTELCOD telcod,
					GXVSIM.ABONAD.ABOTEL tel,
					GXVSIM.ABONAD.ABOCELCOD celcod,
					GXVSIM.ABONAD.ABOCEL cel,
					GXVSIM.IORDENES.IORDORG sub_tipo,
					GXVSIM.ABONAD.ABOTPO asstipo
				FROM 
					GXVSIM.IORDENES,
					GXVSIM.IFALLAS,
					GXVSIM.ABONAD

				WHERE
					GXVSIM.IORDENES.IFALLNRO = GXVSIM.IFALLAS.IFALLNRO (+)
					AND GXVSIM.IORDENES.IABOCODREP = GXVSIM.ABONAD.ABOCOD 
					AND GXVSIM.ABONAD.PERCOD = GXVSIM.IORDENES.PERCOD

					AND GXVSIM.IORDENES.IORDTPO = 'S'
					AND GXVSIM.IORDENES.IORDSTS = ".$this->supsiga->escape($st)."
					AND GXVSIM.IORDENES.IORDNRO = {$o['os']}
					AND GXVSIM.IORDENES.PERCOD = {$o['per']}";
			$r = $this->os_get_addr($this->supsiga->query($magic)->row_array());
		}else{
			$r = $this->supsiga->
					select('GXVSIM.REPARA.RECSTS st, '
							. 'GXVSIM.REPARA.RECAGEFCH ag, '
							. 'GXVSIM.REPARA.RECAGETUR tur')->
					where("GXVSIM.REPARA.GRPPERCOD",$this->grpper($o['per']))->
					where("GXVSIM.REPARA.RECNRO",$o['os'])->
					get('GXVSIM.REPARA')->row_array();
			
			if(!$r){
				return null;
			}

			
			$newst = strtoupper(trim($r['ST']));
			$newag = strtotime($r['AG']);
			$newag2 = date('Y-m-d', $newag);
			
			$oscache = array(
					'status' => strtolower(stName($newst)), 
					'ag' => $newag2 
				);
			
			if(intval($r['TUR'])){
				$oscache['turno'] = intval($r['TUR']);
			}
			
			$this->db->where('os',$o['os']);
			$this->db->where('per',$o['per']);
			$this->db->where('svc','tv');
			$this->db->update('os_cache', $oscache);

			$this->db->where('NRO_OS',$o['os']);
			$this->db->where('PER',$o['per']);
			$this->db->where('SERVICO','PTV');
			$this->db->update('pita',
				array(
					'STATUS_OS' =>  strtoupper(stName($newst)),
					'DT_AGEND' => $newag2 
				)
			);
		
			$magic =
				"SELECT
					GXVSIM.REPARA.RECNRO os,
					GXVSIM.REPARA.RECHORING h,
					'TV' b,
					GXVSIM.REPARA.RECSTS os_status,
					GXVSIM.REPARA.PERCOD per,
					GXVSIM.ABONAD.ABOCOD asscod,
					GXVSIM.ABONAD.ABONOMAPE ass,
					GXVSIM.REPARA.RECOBSORG obs_origem,
					GXVSIM.REPARA.RECOBS obs_tec,
					GXVSIM.REPARA.RECFCHING dt,
					GXVSIM.REPARA.RECAGEFCH ag,
					GXVSIM.FALLAS.FALLDSC falha,
					GXVSIM.ABONAD.ABOTELCOD telcod,
					GXVSIM.ABONAD.ABOTEL tel,
					GXVSIM.ABONAD.ABOCELCOD celcod,
					GXVSIM.ABONAD.ABOCEL cel,
					GXVSIM.REPARA.RECFLGGEN sub_tipo,
					GXVSIM.ABONAD.ABOTPO asstipo
				FROM 
					GXVSIM.REPARA,
					GXVSIM.FALLAS,
					GXVSIM.ABONAD

				WHERE
					GXVSIM.REPARA.FALLNRO = GXVSIM.FALLAS.FALLNRO (+)
					AND GXVSIM.REPARA.ABOCODREP = GXVSIM.ABONAD.ABOCOD 
					AND GXVSIM.REPARA.PERCOD = GXVSIM.ABONAD.PERCOD

					AND GXVSIM.REPARA.RECTPO = 'R'
					AND GXVSIM.REPARA.RECSTS = ".$this->supsiga->escape($st)."
					AND GXVSIM.REPARA.RECNRO = {$o['os']}
					AND GXVSIM.REPARA.GRPPERCOD = ".$this->grpper($o['per'])."";
			$r = $this->os_get_addr($this->supsiga->query($magic)->row_array());
		}
		
		if(!$r){
			return null;
		}
		
		$newag = strtotime($r['AG']);
		$newag2 = date('Y-m-d',$newag);
		$okag = true;
		if (!$show_future) {
			if ($st === 'A') {
				$okag = ($newag <= strtotime(date('Y-m-d')));
			}
			if ($st === 'E') {
				$okag = ($newag < strtotime(date('Y-m-d')));
			}
		}

		
		if(!$okag){
			return null;
		}

		$r = trim_em_all($r);
		$r = array(
				'os' => intval($o['os']),
				'per' => intval($o['per']),
				'svc' => strtoupper($o['svc']),
				'asscod' => $r['ASSCOD'],
				'assname' => fCap($r['ASS']),
				'tipo' => (($r['ASSTIPO']==='P')?'Corporativo':'Individual'),
				'cep' => fCep($r['CEP']),
				'end' => fCap($r['ADDR']),
				'bairro' => fCap($r['BAIRRO']),
				'node' => $r['NODE'],
				'obs_origem' => $r['OBS_ORIGEM'],
				'obs_tec' => $r['OBS_TEC'],
				'ingr' => date($dtf, strtotime(trim($r['DT']))).' '.h_parse($r['H']),
				'ag' => ((trim($r['AG']) !== '01-JAN-01')?date($dtf, strtotime(trim($r['AG']))):null),
				'falha' => fCap($r['FALHA']),
				'os_status' => stName($r['OS_STATUS']),
				'tel' => fTel($r['TEL'],$r['TELCOD']),
				'cel' => fTel($r['CEL'],$r['CELCOD'])
			);
		if(!array_key_exists($r['per'],$pers)){
			$pers[$r['per']] = $this->db->where('id',$r['per'])->get('per')->row_array();
		}

		$r['cid'] = $pers[$r['per']]['name'];
		$r['uf'] = $pers[$r['per']]['uf'];
		
		if(array_key_exists('since',$o)){
			$r['since'] = date("{$dtf} H:i",strtotime($o['since']));
		}

		if(array_key_exists('tt',$o)){
			$r['tt'] = $o['tt'];
		}
		return $r;
	}
	function geo_search($cep,$end){
		return $this->db->where('cep',str_replace('-','',$cep))->where('end',strtolower($end))->get('geo_cache')->row_array();
	}
	function os_get_turno($o){
		return $this->os_get($o, false, false, true);
	}
	function os_get_raw($o){
		return $this->os_get($o,false,false,false,true,true);
	}
	function is_cached($o){
		return $this->db->
					where('os',$o['os'])->
					where('per',$o['per'])->
					where('svc',$o['svc'])->
					get('os_cache')->row_array();
	}
	function should_be_cached($os){
		$span = $this->sys_arg_int('os_cache_span');
		$d = new DateTime();
        $d->sub(date_interval_create_from_date_string("{$span} days"));
        
        $ingrD = $d->getTimestamp();
        $agD = date('Y-m-d',time() - 60 * 60 * 24 * 7);
		
		$ag = 0;
		if($os['AG'] && strtoupper($os['AG']) !== '01-JAN-01'){
			$ag = strtotime($os['AG']);
		}
		
        $ingr = strtotime($os['DT']);

		return $ingr >= $ingrD || $ag >= $agD;
	}
	function os_get(
		$o,
		$fulladdress = false,
		$geo = false,
		$turno = false,
		$ignorecache = false,
		$inhuman = false
	){
		$this->load_supsiga();
		$grpper = $this->grpper($o['per']);

		if(strtolower($o['svc']) === 'cm'){
			$magic =
				"SELECT
					x.IORDTPO os_tipo,
					x.IORDORG sub_tipo,
					x.IORDSTS os_status,
					x.IORDNRO os,
					x.IORDHORING h,
					x.PERCOD per,
					x.ABOCOD asscod,
					x.ABONOMAPE ass,
					x.IORDOBSORG obs_origem,
					x.IORDOBS obs_tec,
					x.IORDFCHING dt,
					x.IORDAGEFCH ag,
					x.ITECCOD tec_id,
					x.ABOTELCOD telcod,
					x.ABOTEL tel,
					x.ABOCELCOD celcod,
					x.ABOCEL cel,
					x.ABOTPO asstipo,
					x.GRPAFICODABO grupocod,
					x.GRPAFIDSC grupo,
					x.IORDAGETUR tur,
					x.ICONCOD contrato,
					x.TPODOCCOD doc_tipo,
					x.ABODOCNRO doc,

					x.IORDFCHFIN cumprimento,
					x.IORDHORCOM hora_ini,
					x.IORDHORFIN hora_fim,

					GXVSIM.ITECNICO.ITECDSC tec,
					GXVSIM.IFALLAS.IFALLDSC falha,
					GXVSIM.ICAUFAL.ICAUSFALLDSC causa,
					NULL dec, NULL equipamento, NULL dec_tipo,
					TRIM(GXVSIM.IPAQUETE.IPAQDSC) pacote,
					TRIM(GXVSIM.ICONTRA.CMMACADD) modem,
					NULL pacote_tipo
				FROM (
					SELECT
						GXVSIM.IORDENES.IORDTPO,
						GXVSIM.IORDENES.IORDORG,
						GXVSIM.IORDENES.IORDSTS,
						GXVSIM.IORDENES.IORDNRO,
						GXVSIM.IORDENES.IORDHORING,
						GXVSIM.IORDENES.PERCOD,
						GXVSIM.ABONAD.ABOCOD,
						GXVSIM.ABONAD.ABONOMAPE,
						GXVSIM.IORDENES.IORDOBSORG,
						GXVSIM.IORDENES.IORDOBS,
						GXVSIM.IORDENES.IORDFCHING,
						GXVSIM.IORDENES.IORDAGEFCH,
						GXVSIM.IORDENES.ITECCOD,
						GXVSIM.ABONAD.ABOTELCOD,
						GXVSIM.ABONAD.ABOTEL,
						GXVSIM.ABONAD.ABOCELCOD,
						GXVSIM.ABONAD.ABOCEL,
						GXVSIM.ABONAD.ABOTPO,
						GXVSIM.ABONAD.GRPAFICODABO,
						GXVSIM.ABOGRP.GRPAFIDSC,
						GXVSIM.IORDENES.IORDAGETUR,
						GXVSIM.IORDENES.ICONCOD,
						GXVSIM.ABONAD.TPODOCCOD,
						GXVSIM.ABONAD.ABODOCNRO,
						GXVSIM.IORDENES.ICAUSFALLNRO,
						GXVSIM.IORDENES.IFALLNRO,

						GXVSIM.IORDENES.IORDFCHFIN,
						GXVSIM.IORDENES.IORDHORCOM,
						GXVSIM.IORDENES.IORDHORFIN
					FROM 
						GXVSIM.IORDENES,
						GXVSIM.ABONAD,
						GXVSIM.ABOGRP
					WHERE
						GXVSIM.IORDENES.IORDNRO = {$o['os']}
						AND GXVSIM.IORDENES.PERCOD = {$o['per']}

						AND GXVSIM.IORDENES.IABOCODREP = GXVSIM.ABONAD.ABOCOD 
						AND GXVSIM.ABONAD.PERCOD = GXVSIM.IORDENES.PERCOD
						
						AND GXVSIM.ABONAD.TPOABOCODABO = GXVSIM.ABOGRP.TPOABOCOD
						AND GXVSIM.ABONAD.GRPAFICODABO = GXVSIM.ABOGRP.GRPAFICOD
				) x
				LEFT JOIN GXVSIM.IFALLAS ON x.IFALLNRO = GXVSIM.IFALLAS.IFALLNRO
				LEFT JOIN GXVSIM.ICAUFAL ON x.ICAUSFALLNRO = GXVSIM.ICAUFAL.ICAUSFALLNRO
				LEFT JOIN GXVSIM.ITECNICO ON 
					( x.PERCOD = GXVSIM.ITECNICO.PERCOD
						AND x.ITECCOD = GXVSIM.ITECNICO.ITECCOD )
				LEFT JOIN GXVSIM.ICONTRA ON 
					( x.ICONCOD = GXVSIM.ICONTRA.ICONCOD
						AND x.PERCOD = GXVSIM.ICONTRA.PERCOD )
				LEFT JOIN GXVSIM.IPAQUETE ON GXVSIM.ICONTRA.IPAQCOD = GXVSIM.IPAQUETE.IPAQCOD";
		}else{
			$magic =
				"SELECT 
					x.RECTPO os_tipo,
					x.RECFLGGEN sub_tipo,
					x.RECSTS os_status,
					x.RECNRO os,
					x.RECHORING h,
					x.PERCOD per,
					x.ABOCOD asscod,
					x.ABONOMAPE ass,
					x.RECOBSORG obs_origem,
					x.RECOBS obs_tec,
					x.RECFCHING dt,
					x.RECAGEFCH ag,
					x.TECCOD tec_id,
					x.ABOTELCOD telcod,
					x.ABOTEL tel,
					x.ABOCELCOD celcod,
					x.ABOCEL cel,
					x.ABOTPO asstipo,
					x.GRPAFICODABO grupocod,
					x.GRPAFIDSC grupo,
					x.RECAGETUR tur,
					x.CONCOD contrato,
					x.TPODOCCOD doc_tipo,
					x.ABODOCNRO doc,

					x.RECFCHARR cumprimento, 
					x.RECHORCOM hora_ini,
					x.RECHORFIN hora_fim,

					GXVSIM.TECNICOS.TECDSC tec,
					GXVSIM.FALLAS.FALLDSC falha,
					GXVSIM.CAUSFALLA.CAUSFALLDSC causa,
					GXVSIM.CONABO.DECNROSER dec,
					GXVSIM.TIPEQ.EQDSC equipamento,
					GXVSIM.TIPEQ.EQSIS dec_tipo,
					TRIM(GXVSIM.PAQUETE.PAQDSC) pacote,
					NULL modem,
					GXVSIM.CONABO.PAQFLGTPO pacote_tipo
				FROM (
				SELECT
					GXVSIM.REPARA.RECTPO,
					GXVSIM.REPARA.RECFLGGEN,
					GXVSIM.REPARA.RECSTS,
					GXVSIM.REPARA.RECNRO,
					GXVSIM.REPARA.RECHORING,
					GXVSIM.REPARA.PERCOD,
					GXVSIM.ABONAD.ABOCOD,
					GXVSIM.ABONAD.ABONOMAPE,
					GXVSIM.REPARA.RECOBSORG,
					GXVSIM.REPARA.RECOBS,
					GXVSIM.REPARA.RECFCHING,
					GXVSIM.REPARA.RECAGEFCH,
					GXVSIM.REPARA.TECCOD,
					GXVSIM.ABONAD.ABOTELCOD,
					GXVSIM.ABONAD.ABOTEL,
					GXVSIM.ABONAD.ABOCELCOD,
					GXVSIM.ABONAD.ABOCEL,
					GXVSIM.ABONAD.ABOTPO,
					GXVSIM.ABONAD.GRPAFICODABO,
					GXVSIM.ABOGRP.GRPAFIDSC,
					GXVSIM.REPARA.RECAGETUR,
					GXVSIM.REPARA.CONCOD,
					GXVSIM.ABONAD.TPODOCCOD,
					GXVSIM.ABONAD.ABODOCNRO,
					GXVSIM.REPARA.FALLNRO,
					GXVSIM.REPARA.CAUSFALLNRO,
					GXVSIM.REPARA.GRPPERCOD,

					GXVSIM.REPARA.RECFCHARR,
					GXVSIM.REPARA.RECHORCOM,
					GXVSIM.REPARA.RECHORFIN
				FROM 
					GXVSIM.REPARA,
					GXVSIM.ABONAD,
					GXVSIM.ABOGRP

				WHERE
					GXVSIM.REPARA.RECNRO = {$o['os']}
					AND GXVSIM.REPARA.GRPPERCOD = {$grpper}
					
					AND GXVSIM.REPARA.ABOCODREP = GXVSIM.ABONAD.ABOCOD 
					AND GXVSIM.REPARA.PERCOD = GXVSIM.ABONAD.PERCOD
					
					AND GXVSIM.ABONAD.TPOABOCODABO = GXVSIM.ABOGRP.TPOABOCOD
					AND GXVSIM.ABONAD.GRPAFICODABO = GXVSIM.ABOGRP.GRPAFICOD

			) x
			LEFT JOIN GXVSIM.FALLAS ON x.FALLNRO = GXVSIM.FALLAS.FALLNRO
			LEFT JOIN GXVSIM.CAUSFALLA ON x.CAUSFALLNRO = GXVSIM.CAUSFALLA.CAUSFALLNRO
			LEFT JOIN GXVSIM.TECNICOS ON 
				( x.GRPPERCOD = GXVSIM.TECNICOS.GRPPERCOD
					AND x.TECCOD = GXVSIM.TECNICOS.TECCOD )
			LEFT JOIN GXVSIM.CONABO ON 
				( x.CONCOD = GXVSIM.CONABO.CONCOD
					AND x.PERCOD = GXVSIM.CONABO.PERCOD )
			LEFT JOIN GXVSIM.PAQUETE ON GXVSIM.CONABO.PAQCOD = GXVSIM.PAQUETE.PAQCOD
			LEFT JOIN GXVSIM.DECODERS ON 
				( GXVSIM.CONABO.DECNROSER = GXVSIM.DECODERS.DECNROSER
					AND GXVSIM.CONABO.PERCOD = GXVSIM.DECODERS.PERCOD )
			LEFT JOIN GXVSIM.TIPEQ ON GXVSIM.DECODERS.EQTIPO = GXVSIM.TIPEQ.EQTIPO";
		}
		$r = $this->os_get_addr($this->supsiga->query($magic)->row_array());
		
		if($r){
			$r = trim_em_all($r);
			$r['svc'] = $o['svc'];
			$tur = $r['TUR'];
			$p = $this->db->
					select('name')->
					get_where('per',array('id'=>$o['per']))->
					row_array();
			if(!$ignorecache){
				
				$cached = $this->is_cached($o);
				$should_be = $this->should_be_cached($r);

				if( $cached || $should_be ){

					$wat = $this->os_cache_format($r,$cached);
					if($cached){
						$this->db->
							where('os',$o['os'])->
							where('per',$o['per'])->
							where('svc',$o['svc'])->
							update('os_cache',$wat);
					}else{
						$this->db->insert('os_cache',$wat);
					}
				}
			}
			$r = array(
					'os' => $o['os'],
					'per' => $o['per'],
					'grpper' => $grpper,
					'cidade' => $p['name'],
					'svc' => strtolower($o['svc']),
					'os_tipo' => fCap(tpName($r['OS_TIPO'],strtolower($o['svc']))),
					'sub_tipo' => subTipo($r['SUB_TIPO'],$o['svc']),
					'os_status' => strtolower(stName($r['OS_STATUS'])),
					'asscod' => intval($r['ASSCOD']),
					'contrato' => intval($r['CONTRATO']),
					'assname' => fCap($r['ASS']),
					'tipo' => (($r['ASSTIPO']==='P')?'Corporativo':'Individual'),
					'grupo' => mb_strtolower($r['GRUPO'],'UTF-8'),
					'doc_tipo' => $r['DOC_TIPO'],
					'documento' => $r['DOC'],
					'cep' => fCep($r['CEP']),
					'end' => fCap($r['ADDR']),
					'bairro' => fCap($r['BAIRRO']),
					'node' => $r['NODE'],
					'ingr' => date((($inhuman)?'Y-m-d':'d/m/Y'), strtotime(trim($r['DT']))).' '.h_parse($r['H']),
					'ag' => (($r['AG'] && trim($r['AG']) > '01-JAN-01')
								?date((($inhuman)?'Y-m-d':'d/m/Y'), strtotime(trim($r['AG'])))
								:null
							),
					'cumprimento' => 
							(($r['CUMPRIMENTO'] && trim($r['CUMPRIMENTO']) > '01-JAN-01')
								?date(
									(($inhuman)?'Y-m-d':'d/m/Y'), 
									strtotime(trim($r['CUMPRIMENTO']))
								).' '.h_parse($r['HORA_INI']).' ~ '.h_parse($r['HORA_FIM'])
								:null
							),
					'falha' => fCap($r['FALHA']),
					'causa' => ((array_key_exists('CAUSA',$r) 
								&& $r['CAUSA'])?fCap($r['CAUSA']):NULL),
					'tel' => fTel($r['TEL'],$r['TELCOD']),
					'cel' => fTel($r['CEL'],$r['CELCOD']),
					'tec_id' => (($r['TEC_ID'])?intval($r['TEC_ID']):NULL),
					'tec' => (($r['TEC'])?fCap($r['TEC']):NULL),
					'obs_origem' => $r['OBS_ORIGEM'],
					'obs_tec' => $r['OBS_TEC'],
					'decoder' => $r['DEC'],
					'equipamento' => $r['EQUIPAMENTO'],
					'dec_tipo' =>
							(($r['DEC_TIPO'])?(($r['DEC_TIPO'] === 'A')?'analógico':'digital'):null),
					'pacote' => (($r['PACOTE'])?fCap($r['PACOTE']):null),
					'pacote_tipo' => 
						(array_key_exists('PACOTE_TIPO',$r)
							?pacTipo($r['PACOTE_TIPO'])
							:null),
					'modem' => (($r['MODEM'])?strtoupper($r['MODEM']):null)
				);
			if($turno){
				$r['turno'] = (($tur)?$this->turno($tur):null);
			}
			if($fulladdress){
				$per = $this->db->where('id',$r['per'])->get('per')->row_array();
				$r['cid'] = $per['name'];
				$r['uf'] = $per['uf'];
			}
			if($geo){
				$x = $this->geo_search($r['cep'],$r['end']);
				if($x){
					$r['lat'] = floatval($x['lat']);
					$r['lng'] = floatval($x['lng']);
				}
			}
		}
		return $r;
	}
	function os_get_addr($r){
		if($r){
			$a = $this->get_ass_addr(intval($r['ASSCOD']),intval($r['PER']));
			$r['CEP'] = $a['cep'];
			$r['ADDR'] = $a['end'];
			$r['BAIRRO'] = $a['bairro'];
			$r['NODE'] = $a['node'];
		}
		return $r;
	}
    function assinante_pontos($cod, $per){
        $this->load_supsiga();
        $cod = $this->supsiga->escape($cod);
        $per = $this->supsiga->escape($per);

        $tv = $this->supsiga->query(
            "SELECT
				GXVSIM.DECODERS.DECNROSER serial,
				GXVSIM.TIPEQ.EQDSC equipamento,
				GXVSIM.TIPEQ.EQSIS tipo,
				TRIM(GXVSIM.PAQUETE.PAQDSC) pacote,
                GXVSIM.CONABO.PAQFLGTPO pacote_tipo
			FROM GXVSIM.CONABO
            JOIN GXVSIM.PAQUETE ON GXVSIM.CONABO.PAQCOD = GXVSIM.PAQUETE.PAQCOD
			LEFT JOIN GXVSIM.DECODERS ON
				( GXVSIM.CONABO.DECNROSER = GXVSIM.DECODERS.DECNROSER
					AND GXVSIM.CONABO.PERCOD = GXVSIM.DECODERS.PERCOD )
			LEFT JOIN GXVSIM.TIPEQ ON GXVSIM.DECODERS.EQTIPO = GXVSIM.TIPEQ.EQTIPO
			WHERE
              GXVSIM.CONABO.ABOCOD = {$cod}
              AND GXVSIM.CONABO.PERCOD = {$per}
              AND GXVSIM.CONABO.CONSTSHAB = 'C'
			")->result_array();
        $cm = $this->supsiga->query(
            "SELECT
                TRIM(GXVSIM.IPAQUETE.IPAQDSC) pacote,
                TRIM(GXVSIM.ICONTRA.CMMACADD) serial
            FROM GXVSIM.ICONTRA
            JOIN GXVSIM.IPAQUETE ON GXVSIM.ICONTRA.IPAQCOD = GXVSIM.IPAQUETE.IPAQCOD
            WHERE
              GXVSIM.ICONTRA.ABOCOD = {$cod}
              AND GXVSIM.ICONTRA.PERCOD = {$per}
              AND GXVSIM.ICONTRA.ICONSTS = 'C'
            ")->result_array();
        return array_merge($tv, $cm);
    }
	function assinante_decoders($cod,$per,$expand = false){
        $this->load_supsiga();
        $cod = $this->supsiga->escape($cod);
		$per = $this->supsiga->escape($per);

		$d = $this->supsiga->query(
			"SELECT
				GXVSIM.DECODERS.DECNROSER serial,
				GXVSIM.TIPEQ.EQDSC equipamento,
				GXVSIM.TIPEQ.EQSIS PAQFLGTPO
			FROM 
				GXVSIM.ABODEC,
				GXVSIM.DECODERS,
				GXVSIM.TIPEQ
			WHERE
				GXVSIM.ABODEC.ABOCOD = {$cod}
				AND GXVSIM.ABODEC.PERCOD = {$per}
				AND GXVSIM.ABODEC.DECNROSER = GXVSIM.DECODERS.DECNROSER
				AND GXVSIM.ABODEC.PERCOD = GXVSIM.DECODERS.PERCOD
				AND GXVSIM.DECODERS.EQTIPO = GXVSIM.TIPEQ.EQTIPO
			")->result_array();
		$decoders = array();
		if($d){
			if($expand) {
                foreach ($d as $i => $x) {
                    $c = $i + 1;
                    $decoders["serial_{$c}"] = trim($x['SERIAL']);
                    $decoders["nome_{$c}"] = strtoupper(trim($x['EQUIPAMENTO']));
                    $decoders["tipo_{$c}"] = (($x['DEC_TIPO'] === 'A') ? 'analógico' : 'digital');
                }
            }else {
                foreach ($d as $x) {
                    $decoders[] =
                        array(
                            'serial' => trim($x['SERIAL']),
                            'nome' => strtoupper(trim($x['EQUIPAMENTO'])),
                            'tipo' => (($x['DEC_TIPO'] === 'A') ? 'analógico' : 'digital')
                        );
                }
            }
		}
		return $decoders;

	}
	function os_tt_attached($o){
		$tts = array();
		$tt1 = $this->db->
				select('
					per.abbr as per,
					tt.id,
					tt.descr,
					tt.ini,
					tt.loc,
					tt_type.abbr as type')->
				join('tt','tt_os_ack.tt = tt.id')->
				join('tt_type','tt_type.id = tt.type')->
				join('per','tt.per = per.id','left')->
				where('tt_os_ack.os',$o['os'])->
				where('tt_os_ack.per',$o['per'])->
				where('tt_os_ack.svc',$o['svc'])->
				get('tt_os_ack')->row_array();
		if($tt1){
			$tt1['loc'] = $this->db->where('ok',0)->where('tt',$tt1['id'])->get('tt_location')->result_array();
			$tt1['loc'] = implode('; ', array_map(function($a){
				return $a['location'];
			},$tt1['loc']));

			$u = $this->db->
					select('tt_update.id,tt_status.name as status')->
					join('tt_status','tt_status.id = tt_update.status')->
					order_by('id','desc')->
					limit(1)->
					get_where('tt_update',array('tt'=>$tt1['id']))->row_array();
			$tt1['status'] = $u['status'];
			$tt1['ini_br'] = date('d/m/Y H:i',strtotime($tt1['ini']));
			$tts[] = $tt1;
		}
		$tt2 = $this->db->
				select('
					per.abbr as per,
					tt.id,
					tt.descr,
					tt.ini,
					tt.loc,
					tt_type.abbr as type,
					tt_os_ack_trash.obs,
					tt_os_ack_trash.user,
					tt_os_ack_trash.timestamp')->
				from('tt_os_ack_trash')->
				join('tt','tt_os_ack_trash.tt = tt.id')->
				join('tt_type','tt_type.id = tt.type')->
				join('per','tt.per = per.id','left')->
				where('tt_os_ack_trash.os',$o['os'])->
				where('tt_os_ack_trash.per',$o['per'])->
				where('tt_os_ack_trash.svc',$o['svc'])->
				get()->result_array();
		if($tt2){
			foreach($tt2 as $tt){
				$tt['loc'] = $this->db->where('ok',0)->where('tt',$tt['id'])->get('tt_location')->result_array();
				$tt['loc'] = implode('; ', array_map(function($a){
					return $a['location'];
				},$tt['loc']));
				$u = $this->db->
						select('tt_update.id,tt_status.name as status')->
						order_by('id','desc')->
						join('tt_status','tt_status.id = tt_update.status')->
						limit(1)->get_where('tt_update',array('tt'=>$tt['id']))->row_array();
				$tt['status'] = $u['status'];
				$tt['ini_br'] = date('d/m/Y H:i:s',strtotime($tt['ini']));
				$tt['timestamp'] = date('d/m/Y H:i:s',strtotime($tt['timestamp']));
				$tts[] = $tt;
			}
		}
		usort($tts,function($a,$b){
			if($a['id'] > $b['id'])
				return -1;
			elseif($a['id'] < $b['id'])
				return 1;
			else
				return 0;
		});
		return $tts;
	}
	function node_pita($node,$filter=true){
		$d = new DateTime();
		$numberOfDays = $this->sys_arg_int('mon_days');
		$d->sub(date_interval_create_from_date_string("{$numberOfDays} days"));
		$list = $this->db->query(
			"SELECT
				os_cache.updated_in,
				os_cache.svc,
				os_cache.ingr,
				os_cache.node,
				os_cache.bairro,
				os_cache.cep,
				os_cache.end,
				os_cache.os,
				os_cache.per,
				os_cache.status,
				os_cache.asscod,
				os_cache.assname,
				os_cache.asstipo,
				os_cache.falha,
				os_cache.obs_origem,
				per.abbr,
				per.name as pername,
				IFNULL(tt_os_ack_trash.id,0) ri
			FROM os_cache
			LEFT JOIN tt_os_ack ON (tt_os_ack.os = os_cache.os AND tt_os_ack.per = os_cache.per AND tt_os_ack.svc = os_cache.svc)
			LEFT JOIN tt_os_ack_trash ON (tt_os_ack_trash.os = os_cache.os AND tt_os_ack_trash.per = os_cache.per AND tt_os_ack_trash.svc = os_cache.svc)
			JOIN per ON per.id = os_cache.per
			WHERE
				(date(os_cache.ingr) > '".$d->format('Y-m-d')."' 
					   OR (tt_os_ack_trash.id IS NOT NULL AND tt_os_ack.id IS NULL)
						   OR asstipo != 'individual') 
				AND os_cache.tipo = 'reclamação'"
				.(($filter)?"
					AND (status = 'pendente' OR status = 'agendada')
					AND tt_os_ack.id IS NULL"
				:"AND os_cache.status != 'cancelada'")."
				AND os_cache.node = '{$node}'
			GROUP BY os_cache.svc,os_cache.per,os_cache.os
			ORDER BY ingr DESC")->result_array();
		$result = $this->_gptb_struct();
		foreach($list as $x){
			$ri = $this->from_ri($x);
			$result['rows'][] =
				array(
					date('d/m/y H:i:s', strtotime($x['ingr'])),
					$this->_linkOS($x),
					$x['abbr'],
					$x['assname']." [{$x['asscod']}]",
					$x['asstipo'],
					$x['falha'],
					$x['obs_origem'],
					fCap($x['status']),
					$x['end'],
					$x['bairro'],
					$x['cep'],
					$x['node'],
					(($ri)?true:false)
				);
		}
		return $result;
	}
	function _linkOS($x){
		$os = intval($x['os']);
		$per = intval($x['per']);
		return "<a target='_blank' title='Abrir Ordem' class='open-os' href='os#os={$os}&per={$per}&svc=".strtolower($x['svc'])."'>".strtoupper($x['svc'])." - {$os}</a>";
	}
	function cep_pita($cep){
		$d = new DateTime();
		$numberOfDays = $this->sys_arg_int('mon_days');
		$d->sub(date_interval_create_from_date_string("{$numberOfDays} days"));
		$list = $this->db->query(
			"SELECT
				os_cache.updated_in,
				os_cache.svc,
				os_cache.ingr,
				os_cache.node,
				os_cache.bairro,
				os_cache.cep,
				os_cache.end,
				os_cache.os,
				os_cache.per,
				os_cache.status,
				os_cache.asscod,
				os_cache.assname,
				os_cache.asstipo,
				os_cache.falha,
				os_cache.obs_origem,
				per.abbr,
				per.name as pername
			FROM os_cache
			JOIN per ON per.id = os_cache.per
			LEFT JOIN tt_os_ack ON (tt_os_ack.os = os_cache.os AND tt_os_ack.per = os_cache.per AND tt_os_ack.svc = os_cache.svc)
			LEFT JOIN tt_os_ack_trash ON (tt_os_ack_trash.os = os_cache.os AND tt_os_ack_trash.per = os_cache.per AND tt_os_ack_trash.svc = os_cache.svc)
			WHERE
				(date(ingr) > '".$d->format('Y-m-d')."' 
					OR (tt_os_ack_trash.id IS NOT NULL AND tt_os_ack.id IS NULL)
						OR asstipo != 'individual') 
				AND os_cache.tipo = 'reclamação'
				AND os_cache.status != 'cancelada'
				AND os_cache.cep = '{$cep}'
			ORDER BY ingr DESC")->result_array();
		$result = $this->_gptb_struct();
		foreach($list as $x){
			$ri = $this->from_ri($x);
			$result['rows'][] =
				array(
					date('d/m/y H:i:s', strtotime($x['ingr'])),
					$this->_linkOS($x),
					$x['abbr'],
					$x['assname']." [{$x['asscod']}]",
					$x['asstipo'],
					$x['falha'],
					$x['obs_origem'],
					fCap($x['status']),
					$x['end'],
					$x['bairro'],
					$x['cep'],
					$x['node'],
					(($ri)?true:false)
				);
		}
		return $result;
	}
	function from_ri($os){
		return $this->db->where('os',$os['os'])->where('per',$os['per'])->where('svc',$os['svc'])->from('tt_os_ack_trash')->count_all_results();
	}
	function pita_range($id,$level='node',$filter=true,$range=false){
		switch($level){
			case 'node':
				$lquery = "AND os_cache.node = '{$id}'";
				break;
			case 'area':
				$lquery = "AND per.abbr = '{$id}'";
				break;
			case 'sim':
				$lquery = "";
				break;
		}
		if($range && $range['from'] === $range['to']){
			$d = date('Y-m-d',strtotime($range['from']));
			$range = false;
		}else{
			$d = date('Y-m-d');
		}
		$list = $this->db->query(
			"SELECT
				os_cache.updated_in,
				os_cache.svc,
				os_cache.ingr,
				os_cache.node,
				os_cache.bairro,
				os_cache.cep,
				os_cache.end,
				os_cache.os,
				os_cache.per,
				os_cache.status,
				os_cache.asscod,
				os_cache.assname,
				os_cache.asstipo,
				os_cache.falha,
				os_cache.obs_origem,
				per.abbr,
				per.name as pername
			FROM os_cache
			LEFT JOIN tt_os_ack ON (tt_os_ack.os = os_cache.os AND tt_os_ack.per = os_cache.per AND tt_os_ack.svc = os_cache.svc)
			JOIN per ON per.id = os_cache.per
			WHERE
				".(($range)?
				"DATE(ingr) BETWEEN '".date('Y-m-d',strtotime($range['from']))."' AND '".date('Y-m-d',strtotime($range['to']))."'"
				:"ingr >= '{$d}'")."
				".(($filter)?"AND (status = 'pendente' OR status = 'agendada') ":"")."
				AND tt_os_ack.id IS NULL
				{$lquery}
				AND os_cache.tipo = 'reclamação'
			ORDER BY ingr DESC")->result_array();
		$result = array('oss' => array());
		foreach($list as $x){
			$ri = $this->from_ri($x);
			$x['svc'] = strtoupper($x['svc']);
			$x['ingr'] = date('d/m/y H:i:s',strtotime($x['ingr']));
			$x['status'] = fCap($x['status']);
			$x['ri'] = (($ri)?"S":"N");
			$result['oss'][] = $x;
		}
		return $result;
	}
	function _gptb_struct(){
		return 
			array(
				'rows' => array(),
				'cols' => array(
					array(
						"title" => "Ingresso", 
						"type" => "string"
					),
					array(
						"title" => "OS", 
						"type" => "string"
					),
					array(
						"title" => "PER", 
						"type" => "string"
					),
					array(
						"title" => "Assinante", 
						"type" => "string"
					),
					array(
						"title" => "Tipo", 
						"type" => "string"
					),
					array(
						"title" => "Falha", 
						"type" => "string"
					),
					array(
						"title" => "Obs. Origem",
						"type" => "string"
					),
					array(
						"title" => "Status",
						"type" => "string"
					),
					array(
						"title" => "Endereço",
						"type" => "string"
					),
					array(
						"title" => "Bairro",
						"type" => "string"
					),
					array(
						"title" => "CEP",
						"type" => "string"
					),
					array(
						"title" => "NODE",
						"type" => "string"
					),
					array(
						"title" => "Retorno",
						"type" => "boolean"
					)
				)
			);
	}
	function imb_node($node){
		$key = "node-avg-{$node}";
		$this->load->driver('cache');
		$imb = $this->cache->memcached->get($key);
		if ( $imb === false ){
			$imb = $this->_imb_node($node);
			$this->cache->memcached->save($key, $imb, 60 * 60 * 6);
		}
		return $imb;
	}
	function _imb_node($node){
		$n = $this->db->
				select('per.id per, area.id area, area.name area_name')->
				join('per','per.id = node.per')->
				join('area','area.id = per.area')->
				where('node.node',$node)->get('node')->row_array();
		if(!$n){
			return 0;
		}
		return $this->mmeta('meta_imb', $n['area_name']);
	}
	function node_avg($node,$weekDays){
		$key = "node-avg-{$node}-".implode('-',$weekDays);
		$this->load->driver('cache');
		$avg = $this->cache->memcached->get($key);
		if ( $avg === false ){
			$avg = $this->_node_avg($node,$weekDays);
			$this->cache->memcached->save($key, $avg, 60 * 60 * 2);
		}
		return $avg;
	}
	function _node_avg($node,$weekDays){
		$std = 5;
		$minDt = date('Y-m-d', time() - 60 * 60 * 24 * 120);
		$val = 0;
		foreach($weekDays as $d){
			$query = 
				"SELECT avg(c) m
				FROM (
					SELECT COUNT(*) c
					FROM pita
					WHERE
						node = ".$this->db->escape($node)."
						AND dt_ingr > '{$minDt}'
						AND DAYOFWEEK(dt_ingr) = {$d}
						AND status_os != 'CANCELADA'
					GROUP BY dt_ingr
				) x";
			$avg = $this->db->query($query)->row_array();
			if($avg){
				$val += floatval($avg['m']);
			}
		}
		return ($val)?round($val):$std;
	}
	function per_avg($per,$weekDays){
		$key = "per-avg-{$per}-".implode('-',$weekDays);
		$this->load->driver('cache');
		$avg = $this->cache->memcached->get($key);
		if ( $avg === false ){
			$avg = $this->_per_avg($per,$weekDays);
			$this->cache->memcached->save($key, $avg, 60 * 60 * 2);
		}
		return $avg;
	}
	function _per_avg($per,$weekDays){
		$std = 40;
		$minDt = date('Y-m-d', time() - 60 * 60 * 24 * 120);
		$val = 0;
		foreach($weekDays as $d){
			$query = 
				"SELECT avg(c) m
				FROM (
					SELECT COUNT(*) c
					FROM pita
					WHERE
						per = ".$this->db->escape($per)."
						AND dt_ingr > '{$minDt}'
						AND DAYOFWEEK(dt_ingr) = {$d}
						AND status_os != 'CANCELADA'
					GROUP BY dt_ingr
				) x";
			$avg = $this->db->query($query)->row_array();
			if($avg){
				$val += floatval($avg['m']);
			}
		}
		return ($val)?round($val):$std;
	}
	function mon_list(){
		$d = new DateTime();
		$numberOfDays = $this->sys_arg_int('mon_days');
		$d->sub(date_interval_create_from_date_string("{$numberOfDays} days"));
		
		$dMin = $d->format('Y-m-d');
		$d->add(date_interval_create_from_date_string("1 day"));
		
		$weekDays = array();
		$dias = array();
		
		while($d->getTimestamp() <= time()){
			
			$dia = intval($d->format('w')) + 1;
			if(!in_array($dia, $weekDays)){
				$weekDays[] = $dia;
				$dias[] = "<b>".daybr($d->format('l'))." (".$d->format('d').")"."</b>";
			}
			$d->add(date_interval_create_from_date_string("1 day"));
		}
		
		if(count($dias) > 1){
			$ultimo = array_pop($dias);
			$dias[count($dias)-1] .= " e {$ultimo}";
		}
		$result = array('areas'=>array(),'total'=>0,'x' => array('tv'=>0,'cm'=>0));
		$magic =
		"SELECT
			os_cache.svc,
			os_cache.ingr,
			os_cache.node,
			os_cache.bairro,
			os_cache.cep,
			os_cache.end,
			os_cache.os,
			os_cache.per,
			os_cache.status,
			node_warn,
			node_crit,
			per.abbr,
			per.name as pername,
			area.name_abbr
		FROM os_cache
		JOIN per ON per.id = os_cache.per
		JOIN area ON area.id = per.area
		LEFT JOIN tt_os_ack ON (tt_os_ack.os = os_cache.os AND tt_os_ack.per = os_cache.per AND tt_os_ack.svc = os_cache.svc)
		LEFT JOIN tt_os_ack_trash ON (tt_os_ack_trash.os = os_cache.os AND tt_os_ack_trash.per = os_cache.per AND tt_os_ack_trash.svc = os_cache.svc)
		WHERE
			os_cache.tipo = 'reclamação'
			AND os_cache.status != 'cancelada'
			AND (date(os_cache.ingr) > '{$dMin}' 
				OR (tt_os_ack_trash.id IS NOT NULL AND tt_os_ack.id IS NULL)
					OR os_cache.asstipo != 'individual')
		GROUP BY os_cache.svc,os_cache.per,os_cache.os
		ORDER BY ingr ASC";
		$list = $this->db->query($magic)->result_array();
		foreach($list as $x){
			
			$per = trim($x['per']);
			$pername = trim($x['pername']);
			$abbr = trim($x['abbr']);
			$cep = trim($x['cep']);
			$addr = fCap($x['end']);
			$bairro = fCap($x['bairro']);
			$node = trim($x['node']);
			
			if(!array_key_exists($per, $result['areas'])){
				$result['areas'][$per] =
					array(
						'id' => $per,
						'abbr' => $abbr,
						'name' => $pername,
						'avg' => $this->per_avg($per,$weekDays),
						'dashboard' => uri_hash('dashboard',array(
							'dashboard' => array(
								'dashboard' => 'ri',
								'ind' => 'imb',
								'view' => 'cidade',
								'item' => $x['name_abbr']
							)
						)),
						'count'=> 0,
						'x' =>
							array(
								'tv' => 0,
								'cm' => 0
							),
						'nodes' => array()
					);
			}

			if(!array_key_exists($node, $result['areas'][$per]['nodes'])){
				$result['areas'][$per]['nodes'][$node] =
					array(
						'id' => $node,
						'name' => $node,
						'count' => 0,
						'dashboard' => uri_hash('dashboard',
							array(
								'dashboard' => array(
									'dashboard' => 'ri',
									'ind' => 'imb',
									'view' => 'node',
									'item' => $node,

									)
								)
							),
						'avg' => $this->node_avg($node,$weekDays),
						'base' => $this->node_base($node),
						'x' =>
							array(
								'tv'=>0,
								'cm'=>0
							),
						'ceps'=>array()
					);
			}
			if(!array_key_exists($cep, $result['areas'][$per]['nodes'][$node]['ceps'])){
				$ccep = $this->cep_logradouro(str_replace('-','',$cep));
				if($ccep){
					$logr = $ccep;
				}else{
					$logr = remNum($addr);
				}
				$result['areas'][$per]['nodes'][$node]['ceps'][$cep] =
					array(
						'id'=>$cep,
						'name'=> $logr,
						'descr'=>$bairro,
						'count'=>0,
						'x' =>
							array(
								'tv'=>0,
								'cm'=>0
							)
					);
			}
			$result['total']++;
			$result['x'][$x['svc']]++;
			$result['areas'][$per]['nodes'][$node]['ceps'][$cep]['count']++;
			$result['areas'][$per]['nodes'][$node]['ceps'][$cep]['x'][$x['svc']]++;

			$result['areas'][$per]['nodes'][$node]['count']++;
			$result['areas'][$per]['nodes'][$node]['x'][$x['svc']]++;

			$result['areas'][$per]['count']++;
			$result['areas'][$per]['x'][$x['svc']]++;
		}
		$result['areas'] = $this->fsort($result['areas']);
		$result['label'] = "Mostrando reclamações de ".implode(', ',$dias)." <b>+ prioridades</b>.";
		return $result;
	}
	function node_base($node){
		
		$key = "node-base-{$node}";
		$this->load->driver('cache');
		$base = $this->cache->memcached->get($key);
		
		if ( $base === false ){
			 $base = 
				$this->db->query(
					"select
						node,
						count(*) c,
						sum(cm) as cm,
						sum(tv) as tv
					from (
						select
							if(produto = 'cm' or produto = 'cb',1,0) as cm,
							if(produto = 'tv' or produto = 'cb',1,0) as tv,
							node
						from assinante
						where
							ativo = 1
							and node = ".$this->db->escape($node)."
					) x
					")->row_array();
			 $this->cache->memcached->save($key, $base, 60 * 60 * 3);
		}
		return $base;
	}
	function per_base($per){
		
		$key = "per-base-{per}";
		$this->load->driver('cache');
		$base = $this->cache->memcached->get($key);
		
		if ( $base === false ){
			 $base = 
				$this->db->query(
					"select
						per,
						count(*) c,
						sum(cm) as cm,
						sum(tv) as tv
					from (
						select
							if(produto = 'cm' or produto = 'cb',1,0) as cm,
							if(produto = 'tv' or produto = 'cb',1,0) as tv,
							per
						from assinante
						where
							ativo = 1
							and per = ".$this->db->escape($per)."
					) x
					")->row_array();
			 $this->cache->memcached->save($key, $base, 60 * 60 * 3);
		}
		return $base;
	}
	function nodes_crit_unseen($d){
		return $this->db->query(
                "SELECT
                    os_cache.os,
                    os_cache.per,
                    per.name as pername,
                    os_cache.svc,
                    os_cache.ingr,
                    os_cache.node,
                    os_cache.status,
                    node_warn,
                    node_crit,
                    abbr
                FROM os_cache
                LEFT JOIN tt_os_ack ON (tt_os_ack.os = os_cache.os AND tt_os_ack.per = os_cache.per AND tt_os_ack.svc = os_cache.svc)
                LEFT JOIN tt_os_ack_trash ON (tt_os_ack_trash.os = os_cache.os AND tt_os_ack_trash.per = os_cache.per AND tt_os_ack_trash.svc = os_cache.svc)
                JOIN per ON per.id = os_cache.per
                WHERE
                    tt_os_ack.id IS NULL
                    AND tt_os_ack_trash.id IS NULL
                    AND status = 'pendente'
					AND os_cache.tipo = 'reclamacão'
                    AND ingr <= '".$d->format('Y-m-d H:i:s')."'
		GROUP BY os_cache.svc,os_cache.per,os_cache.os
                ORDER BY ingr ASC"
            )->result_array();
	}
	function nodes_crit_alt($d){
		return $this->db->query(
				"SELECT
					os_cache.svc,
					os_cache.ingr,
					os_cache.node,
					os_cache.os,
					os_cache.per,
					os_cache.status,
					os_cache.asstipo,
					node_warn,
					node_crit,
					abbr,
					IFNULL(tt_os_ack_trash.id,0) ri
				FROM os_cache
				LEFT JOIN tt_os_ack ON (tt_os_ack.os = os_cache.os AND tt_os_ack.per = os_cache.per AND tt_os_ack.svc = os_cache.svc)
				LEFT JOIN tt_os_ack_trash ON (tt_os_ack_trash.os = os_cache.os AND tt_os_ack_trash.per = os_cache.per AND tt_os_ack_trash.svc = os_cache.svc)
				JOIN per ON per.id = os_cache.per
				WHERE
					tt_os_ack.id IS NULL
					AND status = 'pendente'
					AND os_cache.tipo = 'reclamacão'
					AND (date(os_cache.ingr) > '".$d->format('Y-m-d')."' 
							OR tt_os_ack_trash.id IS NOT NULL
								OR asstipo != 'individual')
				GROUP BY os_cache.svc,os_cache.per,os_cache.os
				ORDER BY ingr ASC"
			)->result_array();
	}
	function nodes_crit($area = false){
		$d = new DateTime();
		$numberOfDays = $this->sys_arg_int('mon_days');
		$d->sub(date_interval_create_from_date_string("{$numberOfDays} days"));
		$list = $this->db->query(
				"SELECT
					os_cache.svc,
					os_cache.ingr,
					os_cache.node,
					os_cache.os,
					os_cache.per,
					os_cache.status,
					os_cache.asstipo,
					node_warn,
					node_crit,
					ag_min,
					abbr,
					IFNULL(tt_os_ack_trash.id,0) ri
				FROM os_cache
				LEFT JOIN tt_os_ack ON (tt_os_ack.os = os_cache.os AND tt_os_ack.per = os_cache.per AND tt_os_ack.svc = os_cache.svc)
				LEFT JOIN tt_os_ack_trash ON (tt_os_ack_trash.os = os_cache.os AND tt_os_ack_trash.per = os_cache.per AND tt_os_ack_trash.svc = os_cache.svc)
				JOIN per ON per.id = os_cache.per
				WHERE
					tt_os_ack.id IS NULL
					AND os_cache.tipo = 'reclamação'
					AND (status = 'pendente' OR status = 'agendada')
					AND (date(os_cache.ingr) > '".$d->format('Y-m-d')."' 
						OR tt_os_ack_trash.id IS NOT NULL
							OR asstipo != 'individual')
					".(($area)?"AND (os_cache.per = ".  implode(' OR os_cache.per = ', $area).")":"")."
				GROUP BY os_cache.svc,os_cache.per,os_cache.os
				ORDER BY ingr ASC"
			)->result_array();
		$aux = array();
		foreach($list as $l){
			if(!array_key_exists($l['node'],$aux))
				$aux[$l['node']] = array(
						'pe' => 0,
						'ag' => 0,
						'node' => $l['node'],
						'total' => 0,
						'per' => intval($l['per']),
						'abbr' => $l['abbr'],
						'node_crit' => intval($l['node_crit']),
						'node_warn' => intval($l['node_warn']),
						'ag_min' => intval($l['ag_min']),
						'corp' => false,
						'ri'=>false,
						'l'=>array());
			$aux[$l['node']]['total']++;
			if(trim($l['status']) === 'pendente'){

				if(intval($l['ri']) > 0 && !$aux[$l['node']]['ri']){
					$aux[$l['node']]['ri'] = true;
					$aux[$l['node']]['times'] = array(strtotime($l['ingr']) * 1000);
				}elseif(strtolower($l['asstipo']) !== 'individual' && !$aux[$l['node']]['ri'] && !$aux[$l['node']]['corp']){
					$aux[$l['node']]['corp'] = true;
					$aux[$l['node']]['times'] = array(strtotime($l['ingr']) * 1000);
				}
				if(!$aux[$l['node']]['corp'] && !$aux[$l['node']]['ri'])
					$aux[$l['node']]['l'][] = 
						array(
							't' => strtotime($l['ingr']) * 1000,
							'x' => 'pe'
						);
				$aux[$l['node']]['pe']++;
			}elseif(trim($l['status']) === 'agendada'){
				$aux[$l['node']]['ag']++;
				if(!$aux[$l['node']]['corp'] && !$aux[$l['node']]['ri'])
					$aux[$l['node']]['l'][] = 
						array(
							't' => strtotime($l['ingr']) * 1000,
							'x' => 'ag'
						);
			}
		}
		$new = array();
		foreach($aux as $i => $x){
			$x['pez'] = $x['pe'] >= $x['node_warn'];
			$x['critz'] = $x['pe'] >= $x['node_crit'];
			
			$x['agz'] = 
				($x['ag_min']
					&& $x['ag'] >= $x['ag_min']
						&& !$x['pez']);
			
			
			if(!$x['corp'] && !$x['ri']){
				$x['times'] = array();
				foreach($x['l'] as $y){
					if(!$x['pez'] || $y['x'] === 'pe')
						$x['times'][] = intval($y['t']);
				}
			}
			if($x['ri'] 
				|| $x['corp'] 
					|| $x['pez']
						|| $x['agz'])
				$new[] = $x;
			
		}

		return array('nodes'=>$new, 'timestamp'=> time() * 1000);
	}
	function backlog_pendente($corp,$per = false){
		$fff = 
			function($os){
				return array(
					'per' => intval($os['per']),
					'os' => intval($os['os']),
					'ag' => (($os['ag'] && $os['ag'] >= '2012-01-01')
								?strtotime($os['ag'])
								:0
							),
					'ingr' => strtotime($os['ingr']),
					'svc' => strtolower(substr($os['svc'],-2)),
					'status' => 'P',
					'loaded' => false
				);
			};
		$magic =
			"SELECT
					o.os,
					o.per,
					o.svc,
					o.ag,
					o.ingr
				FROM os_cache o
				JOIN tt_os_ack tto ON (tto.os = o.os AND tto.per = o.per AND tto.svc = o.svc)
				JOIN tt ON tt.id = tto.tt
				WHERE
					( tt.status = 'fechado'  OR tt.status = 'ativo' OR (tt.type = 'backlog' AND tt.status = 'ri' ) )
					".(($per)?"AND o.per = ".$this->db->escape($per):"")."
					AND o.tipo = 'reclamação'
					AND IF(IFNULL(o.asstipo,'individual') = 'individual',0,1) ".(($corp)?'!':'')."= 0
					AND o.status = 'pendente'";

		$xx = $this->db->query($magic)->result_array();
		if(!$xx)
			$xx = array();
		else
			$xx = array_map($fff,$xx);

		//from pita
		$magic =
		"SELECT
				o.PER as per,
				o.NRO_OS as os,
				o.SERVICO as svc,
				o.DT_AGEND as ag,
				o.DT_INGR as ingr
			FROM pita o
			LEFT JOIN tt_os_ack tto ON (tto.os = o.NRO_OS AND tto.per = o.PER AND tto.svc = IF(o.SERVICO = 'PTV','tv','cm') )
			LEFT JOIN os_cache osc ON (osc.os = o.NRO_OS AND osc.per = o.PER AND osc.svc = IF(o.SERVICO = 'PTV','tv','cm') )
			LEFT JOIN tt ON tt.id = tto.tt
			WHERE
				(
					( tt.status = 'fechado'  OR tt.status = 'ativo' OR (tt.type = 'backlog' AND tt.status = 'ri' ) )
					OR
					osc.os IS NULL
				)
				".(($per)?"AND o.PER = ".$this->db->escape($per):"")."
				AND IF(IFNULL(o.TIPO_ASS,'individual') != 'mestre',0,1) ".(($corp)?'!':'')."= 0
				AND o.STATUS_OS = 'PENDENTE'";
		$yy = $this->db->query($magic)->result_array();

		if($yy){
			$yy = array_map($fff, $yy);
			$xx =
				array_merge($xx,
					array_filter($yy,
						function($os) use ($xx){
							$unique = true;
							foreach($xx as $k){
								if(
									$k['os'] === $os['os']
									&& $k['per'] === $os['per']
									&& $k['svc'] === $os['svc']
								){
									$unique = false;
									break;
								}
							}
							return $unique;
						}
					)
				);
		}
		
		return $xx;
	}
	function backlog_status($st, $corp, $per = false, $show_future = false){
		$fff = function($os) use($st){
						return array(
							'per' => intval($os['per']),
							'os' => intval($os['os']),
							'ag' => (($os['ag'] && $os['ag'] >= '2012-01-01')
										?strtotime($os['ag'])
										:0
									),
							'ingr' => strtotime($os['ingr']),
							'svc' => strtolower(substr($os['svc'],-2)),
							'status' => $st,
							'loaded' => false
						);
					};
		$d = new DateTime();
		$d->sub(date_interval_create_from_date_string(
			$this->sys_arg_int('os_cache_span')." days"));


		//from cache
		$this->db->select('per,os,svc,ag,ingr');
		if($per)
			$this->db->where('per',$per);

		if(!$show_future){
			if($st === 'A'){
				$this->db->where("ag <=", date('Y-m-d'));
			}
			if($st === 'E'){
				$this->db->where("(ag < '".date('Y-m-d')."' OR ag IS NULL)", NULL, FALSE);
			}
		}
		

		$this->db->where("asstipo".(($corp)?" !=":''),'individual');//tipo de assinante
		$this->db->where('status', strtolower(stName($st)));//status os
		$this->db->where('tipo','reclamação');
		$this->db->order_by('per,os');
		$xx = $this->db->get('os_cache')->result_array();
		
		if(!$xx){
			$xx = array();
		}else{
			$xx = array_map($fff,$xx);
		}

		//from pita
		$this->db->select("PER as per,NRO_OS as os,SERVICO as svc, DT_AGEND as ag, DT_INGR as ingr");
		$this->db->where('DT_INGR <=',$d->format('Y-m-d'));
		if($per){
			$this->db->where('PER',$per);
		}
		if (!$show_future) {
			if($st === 'A'){
				$this->db->where("DT_AGEND <=", date('Y-m-d'));
			}
			if($st === 'E'){
				$this->db->where("( DT_AGEND < '".date('Y-m-d')."' OR DT_AGEND IS NULL )", NULL, FALSE);
			}
		}
		
		$this->db->where("TIPO_ASS".((!$corp)?" !=":''),'mestre');//tipo de assinante
		$this->db->where('STATUS_OS', strtolower(stName($st)));//status os
		$this->db->order_by('per,os');
		$yy = $this->db->get('pita')->result_array();

		if($yy){
			$yy = array_map($fff, $yy);
			$xx =
				array_merge($xx,
					array_filter($yy,
						function($os) use ($xx){
							$unique = true;
							foreach($xx as $k){
								if(
									$k['os'] === $os['os']
									&& $k['per'] === $os['per']
									&& $k['svc'] === $os['svc']
								){
									$unique = false;
									break;
								}
							}
							return $unique;
						}
					)
				);
		}
		
		return $xx;
	}
	function loc_tree(){
		$nodes = $this->db->select('node.node,per.abbr as per,per.name as per_name')->join('per','per.id = node.per')->order_by('per_name,node')->get('node')->result_array();
		$lper = -1;
		$i = -1;
		$t = array();
		foreach($nodes as $n){
			if($n['per'] !== $lper){
				$lper = $n['per'];
				$t[] =
					array(
						'id' => $lper,
						'name' => $n['per_name'],
						'nodes'=> array()
					);
				$i++;
			}
			$t[$i]['nodes'][] = $n['node'];
		}
		return $t;
	}
	function fsort($a){
		$children = false;
		if($a){
			//check level
			$ks = array_keys($a);
			if(array_key_exists('nodes', $a[$ks[0]]))
				$children = 'nodes';
			else
				if(array_key_exists('ceps', $a[$ks[0]]))
					$children = 'ceps';

			//sort
			//$ks = array_keys($a);
			$max = $ks[0];
			for($i = 1;$i<count($ks);$i++){
				$k = $ks[$i];
				if($a[$k]['count'] > $a[$max]['count']){
					$max = $k;
				}
			}

			$m = $a[$max];
			unset($a[$max]);

			//recursion
			if($children)
				$m[$children] = $this->fsort($m[$children]);
			$new = array($m);
			return array_merge($new,$this->fsort($a));
		}else{
			return array();
		}
	}
	function perms_needed($page){
		$p = $this->db->select('perms')->get_where('page_perms',array('page'=>$page))->row_array();
		if($p)
			return $p['perms'];
		else
			return null;
	}
	function pick_controller($perms,$home){
		$pN = $this->perms_needed($home);
		if( $perms && $home && ($pN === null ||  strpos($perms,$pN) !== false) )
			return $home;
		else
			return page_avaible($perms);
	}
	function check_perms($perm){
		if(!$perm && !is_numeric($perm) && !is_string($perm)){
			return true;
		}elseif($this->user['permissions'] && strpos($this->user['permissions'], $perm)  !== false){
			return true;
		}else{
			return false;
		}
	}
	function lastttu($tt){
		return $this->db->order_by('id','desc')->limit(1)->get_where('tt_update',array('tt'=>$tt))->row_array();
	}
	function tt_group_users($g,$per = null){
		$this->db->
			select('user.login as email,user.cel1 as cel')->
			join('user','tt_user.user = user.login')->
			where('group',$g);
		if($per){
			$this->db->where(" ( tt_user.per IS NULL OR tt_user.per LIKE '%{$per}%' ) ",NULL,FALSE);
		}
		return $this->db->get('tt_user')->result_array();
	}
	function node_eventos_ranking($mes=false,$per=false){
		if($mes)
			$mes = date('Y-m',strtotime($mes));
		$magic = 
			"select tt_location.location node,count(*) a
			from tt_location
			join tt ON tt_location.tt = tt.id
			JOIN node ON node.node = tt_location.location
			JOIN per ON per.id = node.per
			where
				tt.type != 'backlog'
				AND tt.status != 'cancelado' ".
				(($mes)
					?"AND date_format(tt.open,'%Y-%m') = ".$this->db->escape($mes)
					:""
				).
				(($per)
					?" AND per.abbr = ".$this->db->escape($per)
					:''
				).
				" AND tt_location.location_type = 'node'
			group by tt_location.location
			order by a desc,node asc";
		$nodes = $this->db->query($magic)->result_array();
		return $nodes;
	}
	function node_mon_ranking($mes=false,$per=false){
		if($mes)
			$mes = date('Y-m',strtotime($mes));
		$this->db->
				select('node,COUNT(*) as c')->
				join('per','crit_node.per = per.id');
		if($mes)
			$this->db->where('date_format(ini,"%Y-%m") = '.$this->db->escape($mes),NULL,FALSE);
		if($per)
			$this->db->where('per.abbr',$per);
		return $this->db->group_by('node')->order_by('c','desc')->order_by('node','asc')->get('crit_node')->result_array();
	}
	function cidade_evento_ranking($mes=false){
		if($mes)
			$mes = date('Y-m',strtotime($mes));
		$sql = 
			"select 
				x.abbr,x.name,COUNT(DISTINCT id) c
			from(
				select 
					tt.id,per.abbr,per.name
				from tt_location
				join tt ON tt_location.tt = tt.id
				JOIN node ON node.node = tt_location.location
				JOIN per ON per.id = node.per
				where
					tt.type != 'backlog'
				 	AND tt_location.location_type = 'node'
					AND tt.status != 'cancelado' ".
					(($mes)
						?"AND date_format(tt.open,'%Y-%m') = ".$this->db->escape($mes)
						:""
					)."

				UNION
				
				select 
					tt.id,per.abbr,per.name
				from tt_location
				join tt ON tt_location.tt = tt.id
				JOIN per ON (per.name = tt_location.location OR per.abbr = tt_location.location)
				where
					tt.type != 'backlog'
				 	AND tt_location.location_type = 'cidade'
					AND tt.status != 'cancelado' ".
					(($mes)
						?"AND date_format(tt.open,'%Y-%m') = ".$this->db->escape($mes)
						:""
					)."
			) x
			group by abbr
			order by c desc";
		
		$pers = $this->db->query($sql)->result_array();
		return $pers;
	}
	function cidade_mon_ranking($mes=false){
		if($mes)
			$mes = date('Y-m',strtotime($mes));
		$pers = $this->db->query(
			"SELECT
				per.abbr,per.name,tcount.c
			FROM per
			LEFT JOIN (
				select count(*) as c,per
				from crit_node
					".(($mes)
						?"where
							date_format(ini,'%Y-%m') = ".$this->db->escape($mes)
						:""
					)."
				group by per
			) tcount ON tcount.per = per.id
			ORDER BY c DESC")->result_array();
		return $pers;
	}
	function evento_x_dia($per){
		if($per){
			$magic =
				"SELECT d, COUNT(DISTINCT id) c
				FROM (
					SELECT
						tt.id,
						DATE(tt.open) d
					FROM tt
					JOIN tt_location ON (tt_location.location_type = 'cidade' AND tt_location.tt = tt.id)
					JOIN per ON per.name = tt_location.location
					WHERE
						tt.type != 'backlog'
						AND tt.status != 'cancelado'
						AND per.abbr = ".$this->db->escape($per)."

					UNION

					SELECT
						tt.id,
						DATE(tt.open) d
					FROM tt
					JOIN tt_location ON (tt_location.location_type = 'node' AND tt_location.tt = tt.id)
					JOIN node ON node.node = tt_location.location
					JOIN per ON per.id = node.per
					WHERE
						tt.type != 'backlog'
						AND tt.status != 'cancelado'
						AND per.abbr = ".$this->db->escape($per)."
				) x
				GROUP BY d
				ORDER BY d ASC";
		}else{
			$magic =
				"SELECT
					DATE(tt.open) d,
					COUNT(*) c
				FROM tt
				WHERE
					tt.type != 'backlog' 
					AND tt.status != 'cancelado'
				GROUP BY DATE(tt.open)
				ORDER BY d ASC";
		}
		$ds = $this->db->query($magic)->result_array();
		return $ds;
	}
	function mon_timeline($per,$d){
		$mn = strtotime($d);
		$mx = min(array($mn + 24 * (60 * 60),time()));

		$t = $mn;
		$t1 = $t;
		$x = array();

		while($t < $mx){
			$t1 += (((NECKBEARD)?10:5) * 60);
			$magic = "SELECT
					SUM(a) as tot,
					SUM(crit) as crit,
					SUM(ri) as ri,
					SUM(corp) as corp,
					SUM(sla) as sla
				FROM (
					SELECT
						1 as a,
						crit_node_update.crit,
						crit_node_update.corp,
						crit_node_update.ri,
						IF(crit_node_update.age > crit_node_update.sla,1,0) as sla
					FROM crit_node_update
					JOIN crit_node ON crit_node_update.crit_id = crit_node.id
					JOIN per ON per.id = crit_node.per
					WHERE
						crit_node_update.timestamp >= '".date('Y-m-d H:i:s',$t)."'
						AND crit_node_update.timestamp < '".date('Y-m-d H:i:s',$t1)."'
						".(($per)
							?"AND per.abbr = ".$this->db->escape($per)
							:""
						)."
					GROUP BY crit_node_update.crit_id
				) a";

			$y = $this->db->query($magic)->row_array();
			if($y){
				$x[] =
					array(
						'tot' => array('y' => intval($y['tot']),'x' => $t),
						'crit' => array('y' => intval($y['crit']),'x' => $t),
						'corp' => array('y' => intval($y['corp']),'x' => $t),
						'ri' => array('y' => intval($y['ri']),'x' => $t),
						'sla' => array('y' => intval($y['sla']),'x' => $t)
					);
			}else{
				$x[] =
					array(
						'tot' => array('y' => 0,'x' => $t),
						'crit' => array('y' => 0,'x' => $t),
						'corp' => array('y' => 0,'x' => $t),
						'ri' => array('y' => 0,'x' => $t),
						'sla' => array('y' => 0,'x' => $t)
					);
			}
			$t = $t1;
		}

		return $x;
	}
	function vend_inst_timeline($area = false){
		if($area === 'SIM')
			$area = false;
		$magic =
		"SELECT
			d,
			IFNULL(i.c,0) as i,
			IFNULL(v.c,0) as v
		FROM (
			SELECT DISTINCT(dt) as d
			FROM
			(
				SELECT DT_CAD_ASS dt
				from vend_log
				UNION
				SELECT DT_INSTAL_ASS dt
				from inst_log
			) y
			ORDER BY dt
		) x
		LEFT JOIN
		(
			select
				count(*) c,
				DT_INSTAL_ASS as dt
			from inst_log
			".(($area)?"join per ON per.id = COD_OPERADORA":"")."
			where
				(INSTAL_CANCELADA IS NULL OR INSTAL_CANCELADA = 'NAO')
				".(($area)?"AND per.area = ".$this->area_id($area):"")."
			GROUP BY DT_INSTAL_ASS
		) i ON i.dt = x.d
		LEFT JOIN
		(
			select
				count(*) c,
				DT_CAD_ASS as dt
			from vend_log
			".(($area)?"join per ON per.id = COD_OPERADORA":"")."
			where
				VENDA IS NULL
				".(($area)?"AND per.area = ".$this->area_id($area):"")."
			GROUP BY DT_CAD_ASS
		) v ON v.dt = x.d";

		return $this->db->query($magic)->result_array();
	}
	function vend_inst($d = false){
		if($d === 'Total')
			$d = false;
		else
			$d = date('Y-m',strtotime($d));
		$magic =
			"SELECT
				x.*,
				x.i + x.v as t
			FROM
			(
				SELECT
					a.name as area,
					a.id as aid,
					(
						SELECT
							COUNT(*) c
						FROM vend_log
						JOIN per ON per.id = COD_OPERADORA
						WHERE
							VENDA IS NULL
							AND per.area = a.id
							".(($d)?"AND date_format(DT_CAD_ASS,'%Y-%m') = '{$d}'":"")."
					) as v,
					(
						SELECT
							COUNT(*) c
						FROM inst_log
						JOIN per ON per.id = COD_OPERADORA
						WHERE
							(INSTAL_CANCELADA IS NULL OR INSTAL_CANCELADA = 'NAO')
							AND per.area = a.id
							".(($d)?"AND date_format(DT_INSTAL_ASS,'%Y-%m') = '{$d}'":"")."
					) as i
				FROM area a
			) x
			ORDER BY t DESC";

		return $this->db->query($magic)->result_array();
	}
	function vend_inst_fi($area=false,$d=false){
		if($area === 'SIM')
			$area = false;
		if($d === 'Total')
			$d = false;
		else
			$d = date('Y-m',strtotime($d));
		$magic =
			"select FORMA_INGRESSO f, COUNT(*) c
			from vend_log
			".(($area)?"join per ON per.id = COD_OPERADORA":"")."
			WHERE
				VENDA IS NULL
				".(($area)?"AND per.area = ".$this->area_id($area):"")."
				".(($d)?"AND date_format(DT_CAD_ASS,'%Y-%m') = '{$d}'":"")."
			group by FORMA_INGRESSO
			order by c desc";

		return $this->db->query($magic)->result_array();
	}
	function vend_inst_ta($area=false,$d=false){
		if($area === 'SIM')
			$area = false;
		if($d === 'Total')
			$d = false;
		else
			$d = date('Y-m',strtotime($d));
		$magic =
			"select TIPO_ASS t, COUNT(*) c
			from vend_log
			".(($area)?"join per ON per.id = COD_OPERADORA":"")."
			WHERE
				VENDA IS NULL
				".(($area)?"AND per.area = ".$this->area_id($area):"")."
				".(($d)?"AND date_format(DT_CAD_ASS,'%Y-%m') = '{$d}'":"")."
			group by TIPO_ASS
			order by c desc";

		return $this->db->query($magic)->result_array();
	}
	function vend_inst_base($area=false){
		if($area === 'SIM')
			$area = false;
		$magic =
			"select
				sum(base) as base,
				sum(cm) as cm,
				sum(tv) as tv,
				sum(combo) as combo,
				month
			from
			(
				SELECT
					base,
					month,
					IF(cservice.name = 'CM',base,0) as cm,
					IF(cservice.name = 'TV',base,0) as tv,
					IF(cservice.name = 'COMBO',base,0) as combo
				FROM cbase
				JOIN cservice ON cbase.cservice = cservice.id
				".(($area)?
				"WHERE
					cbase.area = ".$this->area_id($area):"")."
			) x
			group by month
			order by month";

		return $this->db->query($magic)->result_array();
	}
	function _per_tecs($per){
		$this->load_supsiga();
		$grp = $this->per_group($per);
		$tecs = $this->supsiga->
				select('TRIM(TECDSC) name')->
				where('GRPPERCOD',$grp)->
				where('TECSTS','A')->
				get('TECNICOS')->result_array();
		if(!$tecs)
			$tecs = array();
		$tt = array();
		$tecs =
			array_map(function($t) use (&$tt,$per){
				$tn = fLower($t['NAME']);
				$tt[] = $tn;
				return array(
						'name' => $tn,
						'per' => $per
					);
			},$tecs);

		$itecs =$this->supsiga->
				select('TRIM(ITECDSC) name')->
				where('PERCOD',$per)->
				where('ITECSTS','A')->
				get('ITECNICO')->result_array();
		if(!$itecs)
			$itecs = array();

		$itecs =
			array_map(function($t) use($per){
				$tn = fLower($t['NAME']);
				return array(
						'name' => $tn,
						'per' => $per
				);
			},$itecs);

		$tecs = array_merge($tecs,
				array_filter($itecs,function($t) use($tt){
					return !in_array($t['name'],$tt);
				})
			);
		usort($tecs,function($a,$b){
			$a = $a['name'];
			$b = $b['name'];
			return
				(($a === $b)
					?
						0
					:
						(($a < $b)
							?-1
							:1
						)
				);
		});
		return $tecs;
	}
	function get_tec($name,$per,$create=true){
		$t = $this->db->where('name',$name)->where('per',$per)->get('tec_aloc')->row_array();
		if($t){
			return $t;
		}elseif($create){
			$this->db->insert('tec_aloc',array('name' => $name,'per' => $per));
			return $this->db->where('id',$this->db->insert_id())->get('tec_aloc')->row_array();
		}else{
			return null;
		}
	}
	function tec_working_now($tec,$day,$time=false){
		if(!$tec['workgrid']){
			return false;
		}else{
			
			$w = date('N',strtotime($day));
			if($time !== false){
				$ttime = date('Y-m-d H:i:s',strtotime($day." ".$time));
				$magic =
					"SELECT
						COUNT(*) c
					FROM tec_working
					LEFT JOIN tec_timeout ON
						(
							tec_timeout.tec = tec_working.tec
							AND '{$ttime}' BETWEEN tec_timeout.ini AND tec_timeout.end
						)
					WHERE
						tec_working.working = 1
						AND tec_working.tec = {$tec['id']}
						AND tec_working.weekday = {$w}
						AND tec_timeout.id IS NULL
						AND '{$time}' BETWEEN tec_working.ini AND tec_working.end";
			}else{
				if($this->db->
						where('day',$day)->
						where('tec',$tec['id'])->
						count_all_results('tec_schedule') > 0)
					return true;
				$ini = date('Y-m-d H:i:s',strtotime($day." 06:00:00"));
				$end = date('Y-m-d H:i:s',strtotime($day." 23:00:00"));
				$magic =
					"SELECT
						COUNT(*) c
					FROM tec_working
					LEFT JOIN tec_timeout ON
						(
							tec_timeout.tec = tec_working.tec
							AND tec_timeout.ini <= '{$ini}' AND tec_timeout.end >= '{$end}'
						)
					WHERE
						tec_working.working = 1
						AND tec_working.tec = {$tec['id']}
						AND tec_working.weekday = {$w}
						AND tec_timeout.id IS NULL";
			}
			$c = $this->db->query($magic)->row_array();
			$c = intval($c['c']);
			return ($c > 0);
		}
	}
	function geo_tecs($tecs,$d){
		foreach($tecs as $i => $t){
			$x = $this->db->select('AVG(lat) lat,AVG(lng) lng')->where('day',$d)->where('lat IS NOT NULL',null,false)->where('lng IS NOT NULL',null,false)->where('tec',$t['id'])->get('tec_schedule')->row_array();
			if($x && $x['lat'] && $x['lng']){
				$tecs[$i]['lat'] = floatval($x['lat']);
				$tecs[$i]['lng'] = floatval($x['lng']);
			}else{
				$z = false;

				if($t['zona']){
					$z = $this->db->select('lat,lng')->where('id',$t['zona'])->get('zona')->row_array();
					if($z){
						$tecs[$i]['lat'] = floatval($z['lat']);
						$tecs[$i]['lng'] = floatval($z['lng']);
					}
				}

				if(!$z || !$t['zona']){
					$tecs[$i]['lat'] = 0;
					$tecs[$i]['lng'] = 0;
				}
			}
		}
		return $tecs;
	}
	function os_tipo_tecs($tecs){
		foreach($tecs as $i => $t){
			$x = $this->db->select('os_tipo as tipo')->where('tec',$t['id'])->get('os_tipo_tec')->result_array();
			$tecs[$i]['tipos'] = array();
			if($x)
				$tecs[$i]['tipos'] = array_map(function($a){
					return $a['tipo'];
				},$x);
		}
		return $tecs;
	}
	function filter_working_tecs($tecs,$d,$time=false){
		foreach($tecs as $i => $t)
			if(!$this->tec_working_now($t,$d,$time))
				unset($tecs[$i]);
		return $tecs;
	}
	function per_tecs($per,$create = true){
		$key = "per_tecs_$per";
		$this->load->driver('cache');
		$tecs = $this->cache->memcached->get($key);
		if ( $tecs === false ){
			 $tecs = $this->_per_tecs($per);
			 $this->cache->memcached->save($key, $tecs, 60 * 30);
		}
		$t2 = array();
		foreach($tecs as $t){
			$tec = $this->get_tec($t['name'],$t['per'],$create);
			if($tec){
				$t2[$tec['id']] = array_merge($t,$tec);
			}
		}
		return $t2;
	}
	function join_tecs_items($per,$tecs){
		$t = $this->db->
				select('tec_aloc.*')->
				where('tec_aloc.per',$per)->
				join('tec_aloc','tec_aloc.id = tec_item.tec')->
				group_by('tec')->
				get('tec_item')->result_array();
		
		foreach($t as $x){
			$id = intval($x['id']);
			if(!array_key_exists($id, $tecs))
				$tecs[$id] = $x;
		}
		return $tecs;
	}
	function join_tecs_with_schedule($per,$d,$tecs){
		$t = $this->db->
				select('tec_aloc.*')->
				where('tec_aloc.per',$per)->
				where('tec_schedule.day',$d)->
				join('tec_aloc','tec_aloc.id = tec_schedule.tec')->
				group_by('tec')->
				get('tec_schedule')->result_array();
		
		foreach($t as $x){
			$id = intval($x['id']);
			if(!array_key_exists($id, $tecs))
				$tecs[$id] = $x;
		}
		return $tecs;
	}
	function tecs_battery_level($tecs,$d){
		$safeDay = $this->db->escape($d);
		foreach($tecs as $i => $t){
			$this->db->
					where('tec',$t['id'])->
					where("time >=", $d)->
					where("time < {$safeDay} + INTERVAL 1 DAY", NULL, FALSE);
			
			if(date('Y-m-d') === $d){
				$this->db->where('time >',date('Y-m-d H:i:s',time() - 60 * 60));
			}
			
			$b = $this->db->
					order_by('time DESC')->
					limit(1)->get('tec_battery')->row_array();
			$x = null;
			if($b){
				$x = max(array(0.0,
							round(floatval($b['battery']),2)
						)
					);
				$tecs[$i]['battery_last_update'] = date('H:i:s',strtotime($b['time']));
			}
			$tecs[$i]['battery'] = $x;
		}
		return $tecs;
	}
	function tecs_signal_level($tecs,$d){
		$safeDay = $this->db->escape($d);
		foreach($tecs as $i => $t){
			$this->db->
					where('tec',$t['id'])->
					where("time >=", $d)->
					where("time < {$safeDay} + INTERVAL 1 DAY", NULL, FALSE);
			if(date('Y-m-d') === $d){
				$this->db->where('time >',date('Y-m-d H:i:s',time() - 60 * 60));
			}
			$b = $this->db->
					order_by('time DESC')->
					limit(1)->get('tec_signal')->row_array();
			$x = null;
			if($b){
				$x = max(array(0.0,
						round(floatval($b['signal']),2)
					));
				$tecs[$i]['signal_last_update'] = date('H:i:s',strtotime($b['time']));
			}
			$tecs[$i]['signal'] = $x;
		}
		return $tecs;
	}
	function tecs_gps_status($tecs,$d){
		$safeDay = $this->db->escape($d);
		foreach($tecs as $i => $t){
			$b = $this->db->
					where('tec',$t['id'])->
					where("time >=", $d)->
					where("time < {$safeDay} + INTERVAL 1 DAY", NULL, FALSE)->
					order_by('time DESC')->
					limit(1)->get('tec_gps_status')->row_array();
			$x = null;
			if($b){
				$x = intval($b['status']);
				$tecs[$i]['gps_last_update'] = date('H:i:s',strtotime($b['time']));
			}
			$tecs[$i]['gps_status'] = $x;
		}
		return $tecs;
	}
	function tecs_report_count($tecs,$d){
		foreach($tecs as $i => $t){
			$tec = $this->db->escape($t['id']);
			$safeDay = $this->db->escape($d);
			$b = 
				$this->db->query(
					"SELECT
						SUM(c) c
					FROM (
						SELECT COUNT(*) c
						FROM tec_signal
						WHERE
							tec = {$tec}
							AND time >= {$safeDay}
							AND time < {$safeDay} + INTERVAL 1 DAY

						UNION

						SELECT COUNT(*) c
						FROM tec_battery
						WHERE
							tec = {$tec}
							AND time >= {$safeDay}
							AND time < {$safeDay} + INTERVAL 1 DAY

						UNION

						SELECT COUNT(*) c
						FROM tec_gps_status
						WHERE
							tec = {$tec}
							AND time >= {$safeDay}
							AND time < {$safeDay} + INTERVAL 1 DAY
					) x")->row_array();
			$c = 0;
			if($b){
				$c = intval($b['c']);
			}
			$tecs[$i]['reports'] = $c;
		}
		return $tecs;
	}
	function tecs_workgrid_bounds($tecs,$d){
		$w = date('N',strtotime($d));
		foreach($tecs as $i => $t){
			$x = $this->db->select("MIN(ini) as turno_ini,MAX(end) as turno_end")->
				where('weekday',$w)->
				where('tec',$t['id'])->
				where('working',1)->
				order_by('ini')->get('tec_working')->row_array();
			if($x){
				$tecs[$i]['shift'] = array('ini' => strtotime($d.' '.$x['turno_ini']),'end' => strtotime($d.' '.$x['turno_end']));
			}else
				$tecs[$i]['shift'] = null;
		}
		return $tecs;
	}
	function tecs_workgrid($tecs,$d){
		$w = date('N',strtotime($d));
		foreach($tecs as $i => $t){
			$x = $this->db->select("ini,end,weekday,tec,working")->
				where('weekday',$w)->
				where('tec',$t['id'])->
				order_by('ini')->get('tec_working')->result_array();
			if($x){
				foreach($x as $z => $y){
					$x[$z]['ini'] = strtotime($d.' '.$y['ini']);
					$x[$z]['end'] = strtotime($d.' '.$y['end']);
					$x[$z]['working'] = (intval($x[$z]['working']) > 0);
					$x[$z]['working'] = $x[$z]['working'] && $this->tec_working_now($t, $d, $y['ini']);
				}
				$tecs[$i]['workgrid'] = $x;
			}else
				$tecs[$i]['workgrid'] = array();
		}
		return $tecs;
	}
	function per_timezone($per){
		$x = $this->db->select('timezone')->where('id',$per)->get('per')->row_array();
		return $x['timezone'];
	}
	function per_time($localDate,$per){
		if(!$localDate){
			return null;
		}
		
		$tZ = new DateTimeZone($this->per_timezone($per));
		$dateObj = new DateTime($localDate);
		
		return $dateObj->setTimezone($tZ);
	}
	function tecs_schedules($tecs,$d){
		foreach($tecs as $i => $t){
			$x = $this->db->select(
				"tec_schedule.*",FALSE)->
				where('day',$d)->
				where('tec',$t['id'])->
				order_by('scheduled_ini')->get('tec_schedule')->result_array();
			if($x){
				foreach($x as $j => $y){
					$x[$j]['id'] = intval($x[$j]['id']);
					$x[$j]['desloc'] = (($x[$j]['desloc']!== null)?intval($x[$j]['desloc']):null);
					$x[$j]['destination'] = (($x[$j]['destination']!== null)?intval($x[$j]['destination']):null);
					
					$x[$j]['tini'] = strtotime($y['scheduled_ini']);
					$x[$j]['tend'] = strtotime($y['scheduled_end']);
					$x[$j]['trini'] = (($y['real_ini'])?strtotime($y['real_ini']):null);
					$x[$j]['trend'] = (($y['real_end'])?strtotime($y['real_end']):null);
					
					/*
					$x[$j]['trend'] = null;
					$x[$j]['trini'] = null;
					
					$realIni = $this->per_time($y['real_ini'], $x[$j]['per']);
					if($realIni){
						$x[$j]['real_ini'] = $realIni->format('Y-m-d H:i:s');
						$x[$j]['trini'] = $realIni->getTimestamp();
					}
					
					$realEnd = $this->per_time($y['real_end'], $x[$j]['per']);
					if($realEnd){
						$x[$j]['real_end'] = $realEnd->format('Y-m-d H:i:s');
						$x[$j]['trend'] = $realEnd->getTimestamp();
					}
					*/
					
					if($y['activity'] === 'os'){
						$m = $this->db->select('id,os,per,svc,os_tipo,turno')->where('tec_schedule',$y['id'])->get('tec_schedule_os')->result_array();
						if($m)
							$x[$j]['pack'] = $m;
						else
							$x[$j]['pack'] = array();
					}
				}
				$tecs[$i]['schedule'] = $x;

			}else
				$tecs[$i]['schedule'] = array();
		}
		return $tecs;
	}
	function tecs_lunch($tecs,$d){
		$workgrids = array();
		if(!$tecs)
			return null;
		foreach($tecs as $i => $tec){
			$tec['workgrid'] = intval($tec['workgrid']);
			if($this->db->where('tec',$tec['id'])->where('day',$d)->count_all_results('tec_schedule') === 0){
				if(!array_key_exists($tec['workgrid'],$workgrids))
					$workgrids[$tec['workgrid']] = $this->db->where('id',$tec['workgrid'])->get('workgrid')->row_array();
				$this->db->insert('tec_schedule',array(
					'tec' => $tec['id'],
					'activity' => 'lunch',
					'per' => $tec['per'],
					'day' => $d,
					'descr' => 'Refeição',
					'scheduled_ini' => $d.' '.$workgrids[$tec['workgrid']]['lunch_ini'],
					'scheduled_end' => date('Y-m-d H:i:s',strtotime($d.' '.$workgrids[$tec['workgrid']]['lunch_ini']) + (60 * 60)),
					'scheduled_duration' => 60 * 60,
					'create_time' => date('Y-m-d H:i:s')
				));
			}
		}
	}
	function activities_times($per){
		$x = $this->db->where('per',$per)->get('os_tipo_time')->result_array();
		$times = array();
		foreach($x as $y){
			$times[$y['name']] = intval($y['tec_time']);
		}
		return $times;
	}
	function tecs_zonas($tecs){
		foreach($tecs as $i => $tec){

			$t = $this->db->query(
					"SELECT
						zona.id zona,
						zona.color,
						tec_aloc.workgrid,
						tec_aloc.user
					FROM tec_aloc
					JOIN zona ON (tec_aloc.zona = zona.id)
					WHERE
						tec_aloc.id = {$tec['id']}"
				)->row_array();
			if(!$t)
				$t = array('zona' => null,'color'=>null);

			$tecs[$i] = array_merge($tec,$t);
		}
		return $tecs;
	}
	function os_tipoS($abbr = false){
		$tps = $this->db->select('name, LEFT(name,1) as abbr',FALSE)->order_by('name')->get('os_tipo')->result_array();
		$r = array();
		foreach($tps as $t)
			$r[$t['abbr']] = $t['name'];
		return $r;
	}
	function stperminute(){
		$read = false;
		$x = array();
		$y = $this->db->where('name','os_cache')->get('system_args')->row_array();
		if($y)
			$read = $y['value'];
		if($read){
			$x = json_decode($read,true);
		}else{
			$tps = $this->os_tipoS();
			$ks = array('agendada','finalizada','cancelada','emitida','pendente','reagendada','suspensa');
				foreach($ks as $i){
					foreach($tps as $t => $x)
						$x[$i][$t] = array('x' => $x,'y' => 0);
				}
		}
		return $x;
	}
	function sys_arg_int($name,$default = 5){
		$x = $this->db->select('value')->where('name',$name)->get('system_args')->row_array();
		if($x)
			return intval($x['value']);
		else
			return $default;
	}
	function turno($id){
		$x = $this->db->where('id',$id)->get('turno')->row_array();
		return (($x)?$x:null);
	}
	function sigaLink($os){
		if(strtolower($os['svc']) === 'cm')
			return "http://192.168.140.97:8080/gxvision/servlet/haccioniordenes2?{$os['per']},{$os['os']}";
		else
			return "http://192.168.140.97:8080/gxvision/servlet/haccionrepara?".$this->grpper($os['per']).",{$os['os']}";
	}
	function local_agenda($per,$d){
		$d = $this->db->escape($d);
		$magic =
			"SELECT
				IF(os_cache.ag = '2001-01-01',NULL,os_cache.ag) ag,
				os_cache.asscod,
				os_cache.assgrupo,
				os_cache.assname,
				os_cache.asstipo,
				os_cache.bairro,
				os_cache.cel,
				os_cache.cep,
				os_cache.contrato,
				os_cache.serial,
				os_cache.decoder,
				os_cache.decoder_tipo,
				os_cache.end,
				os_cache.falha,
				os_cache.ingr,
				os_cache.node,
				per.name as cid,
				per.grp as grpper,
				per.uf,
				os_cache.obs_origem,
				os_cache.obs_tec,
				os_cache.os,
				os_cache.per,
				os_cache.status,
				os_cache.svc,
				os_cache.tel,
				os_cache.tipo as os_tipo,
				os_cache.sub_tipo,
				os_cache.turno,
				geo_cache.lat,
				geo_cache.lng,
				turno.id as turnoID,
				turno.name as turnoNAME,
				turno.ini as turnoINI,
				turno.end as turnoEND
			FROM os_cache
			JOIN per ON per.id = os_cache.per
			LEFT JOIN turno ON turno.id = os_cache.turno
			LEFT JOIN geo_cache ON (geo_cache.cep = REPLACE(os_cache.cep, '-', '') AND lower(geo_cache.end) = lower(os_cache.end))
			LEFT JOIN 
				(
					SELECT t.*
					FROM tec_schedule_os t
					JOIN tec_schedule ON (tec_schedule.id = t.tec_schedule AND tec_schedule.day = {$d})
				) tec_schedule_os ON 
					( os_cache.os = tec_schedule_os.os 
						AND os_cache.svc = tec_schedule_os.svc 
						AND os_cache.per = tec_schedule_os.per
					)
			WHERE
				ag = {$d}
				AND tec_schedule_os.id IS NULL
				AND os_cache.per = {$per}
				AND os_cache.status IN ('emitida','agendada')
			GROUP BY os_cache.os, os_cache.per, os_cache.svc
			ORDER BY per,os";
		
		$x = $this->db->query($magic)->result_array();
		if($x)
			foreach($x as $i => $os){
				$x[$i] = $this->_local_os_format($os);
			}
			
		return $x;
	}
	function _local_os_format($os){
		$os['os'] = intval($os['os']);
		$os['per'] = intval($os['per']);
		$os['ingr'] = date('d/m/Y H:i',strtotime($os['ingr']));
		if($os['ag'])
			$os['ag'] = date('d/m/Y',strtotime($os['ag']));
		else
			$os['ag'] = '';
		$os['svc'] = strtolower($os['svc']);
		$os['os_tipo'] = strtolower($os['os_tipo']);
		
		$os['turno'] = array(
			'id' => intval($os['turnoID']),
			'name' => $os['turnoNAME'],
			'ini' => $os['turnoINI'],
			'end' => $os['turnoEND']
		);
		
		unset($os['turnoID']);
		unset($os['turnoNAME']);
		unset($os['turnoINI']);
		unset($os['turnoEND']);
		return $os;
	}
	function _local_top_query(){
		return "SELECT
				IF(os_cache.ag = '2001-01-01',NULL,os_cache.ag) ag,
				os_cache.asscod,
				os_cache.assgrupo,
				os_cache.assname,
				os_cache.asstipo,
				os_cache.bairro,
				os_cache.cel,
				os_cache.cep,
				os_cache.contrato,
				os_cache.serial,
				os_cache.decoder,
				os_cache.decoder_tipo,
				os_cache.end,
				os_cache.falha,
				os_cache.ingr,
				os_cache.node,
				os_cache.pacote,
				per.name as cid,
				per.grp as grpper,
				per.uf,
				os_cache.obs_origem,
				
				tec_schedule.id as schedule_id,
				tec_schedule.real_end,
				tec_schedule.real_ini,
				
				tec_schedule_os.obs as obs_tec,
				tec_schedule_os.id as osackid,
				tec_schedule_os.motivo,
				tec_schedule_os.causa,
				tec_schedule_os.equipamento_in,
				tec_schedule_os.equipamento_out,
				tec_schedule_os.tx,
				tec_schedule_os.rx,
				tec_schedule_os.ch_baixo,
				tec_schedule_os.ch_alto,
				tec_schedule_os.baixa,
				tec_schedule_os.user as baixa_user,
				
				
				tec_aloc.name as tecname,
				os_cache.os,
				os_cache.per,
				os_cache.status,
				os_cache.svc,
				os_cache.tel,
				os_cache.tipo as os_tipo,
				os_cache.sub_tipo,
				os_cache.turno,
				geo_cache.lat,
				geo_cache.lng,
				turno.id as turnoID,
				turno.name as turnoNAME,
				turno.ini as turnoINI,
				turno.end as turnoEND
			FROM os_cache
			LEFT JOIN turno ON turno.id = os_cache.turno
			JOIN per ON per.id = os_cache.per
			JOIN tec_schedule_os ON (os_cache.os = tec_schedule_os.os AND os_cache.svc = tec_schedule_os.svc AND os_cache.per = tec_schedule_os.per)
			JOIN tec_schedule ON tec_schedule.id = tec_schedule_os.tec_schedule
			LEFT JOIN tec_aloc ON tec_aloc.id = tec_schedule.tec
			LEFT JOIN geo_cache ON (geo_cache.cep = REPLACE(os_cache.cep, '-', '') AND geo_cache.end = os_cache.end)";
	}
	function ordens_baixadas($per){
		$mytecs = $this->user_model->user_tecs();
		$magic =
			$this->_local_top_query().
			"WHERE
				tec_schedule.real_end IS NOT NULL
				AND tec_schedule_os.baixa IS NULL
				AND os_cache.per = {$per}
				".(($mytecs)?"AND tec_schedule.tec IN (".
						join(',',$mytecs).
							")":'')."
			ORDER BY tec_schedule.real_end";
		
		$x = $this->db->query($magic)->result_array();
		if($x)
			foreach($x as $i => $os){
				$x[$i] = $this->_local_os_format($os);
				
				$x[$i]['tx'] = (($x[$i]['tx'] !== null)?floatval($x[$i]['tx']):'---');
				$x[$i]['rx'] = (($x[$i]['rx'] !== null)?floatval($x[$i]['rx']):'---');
				
				$x[$i]['ch_baixo'] = (($x[$i]['ch_baixo'] !== null)?floatval($x[$i]['ch_baixo']):'---');
				$x[$i]['ch_alto'] = (($x[$i]['ch_alto'] !== null)?floatval($x[$i]['ch_alto']):'---');
			}
		return $x;
	}
	function local_os_get($o,$scheduleID = false){
		$magic = 
			$this->_local_top_query().
			"WHERE
				os_cache.os = ".$this->db->escape($o['os'])."
				".(($scheduleID)
					?' AND tec_schedule_os.tec_schedule = '.$this->db->escape($scheduleID)
					:''
				)."
				AND os_cache.per = ".$this->db->escape($o['per'])."
				AND os_cache.svc = ".$this->db->escape($o['svc'])."";
		
		$os = $this->db->query($magic)->row_array();
		if($os)
			$os = $this->_local_os_format($os);
		else
			$os = null;
		
		return $os;
	}
	function os_to_agenda($os,$per,$svc){
		$l = $this->db->query(
			"SELECT
				IF(os_cache.ag = '2001-01-01',NULL,os_cache.ag) ag,
				os_cache.asscod,
				os_cache.assname,
				os_cache.ingr,
				os_cache.os,
				os_cache.per,
				os_cache.svc,
				os_cache.turno,
				os_cache.tipo as os_tipo,
				os_tipo_time.tec_time,
				geo_cache.lat,
				geo_cache.lng
			FROM os_cache
			JOIN per ON per.id = os_cache.per
			LEFT JOIN geo_cache ON (geo_cache.cep = REPLACE(os_cache.cep, '-', '') AND geo_cache.end = os_cache.end)
			JOIN os_tipo_time ON ( os_tipo_time.name = os_cache.tipo AND os_tipo_time.per = os_cache.per )
			WHERE
				os_cache.per = {$per}
				AND os_cache.svc = '{$svc}'
				AND os_cache.os = {$os}")->result_array();
		return $l;
	}
	function ass_agenda($ass,$per,$d){
		$l = $this->db->query(
			"SELECT
				IF(os_cache.ag = '2001-01-01',NULL,os_cache.ag) ag,
				os_cache.asscod,
				os_cache.assname,
				os_cache.ingr,
				os_cache.os,
				os_cache.per,
				os_cache.svc,
				os_cache.turno,
				os_cache.tipo as os_tipo,
				os_tipo_time.tec_time,
				geo_cache.lat,
				geo_cache.lng
			FROM os_cache
			JOIN per ON per.id = os_cache.per
			LEFT JOIN geo_cache ON (geo_cache.cep = REPLACE(os_cache.cep, '-', '') AND geo_cache.end = os_cache.end)
			LEFT JOIN 
				(
					SELECT t.*
					FROM tec_schedule_os t
					JOIN tec_schedule ON (tec_schedule.id = t.tec_schedule AND tec_schedule.day = {$d})
				) tec_schedule_os ON 
					( os_cache.os = tec_schedule_os.os 
						AND os_cache.svc = tec_schedule_os.svc 
						AND os_cache.per = tec_schedule_os.per
					)
			JOIN os_tipo_time ON ( os_tipo_time.name = os_cache.tipo AND os_tipo_time.per = os_cache.per )
			WHERE
				ag = '{$d}'
				AND tec_schedule_os.id IS NULL
				AND os_cache.per = {$per}
				AND os_cache.asscod = {$ass}
				AND (status = 'emitida' OR status = 'agendada')")->result_array();
		return (($l)?$l:array());
	}
	function agenda($per,$t){
		$d = strtoupper(date('d-M-y',$t));
		$this->load_supsiga();
		$tv = $this->supsiga->
				query(
				"SELECT
					GXVSIM.REPARA.RECAGEFCH ag,
					GXVSIM.REPARA.RECAGETUR tur,
					GXVSIM.REPARA.RECFCHING dt,
					GXVSIM.REPARA.RECNRO os,
					GXVSIM.REPARA.RECHORING h,
					GXVSIM.REPARA.RECTPO tp,
					GXVSIM.REPARA.ABOCODREP asscod,
					'tv' svc
				FROM GXVSIM.REPARA
				WHERE
					GXVSIM.REPARA.PERCOD =  $per
					AND GXVSIM.REPARA.RECSTS = 'E'
					AND GXVSIM.REPARA.RECAGEFCH = '{$d}'
				")->result_array();
		if(!$tv)
			$tv = array();
		$cm =
			$this->supsiga->query(
				"SELECT
					GXVSIM.IORDENES.IORDNRO os,
					GXVSIM.IORDENES.IORDAGETUR tur,
					GXVSIM.IORDENES.IORDAGEFCH ag,
					GXVSIM.IORDENES.IORDHORING h,
					GXVSIM.IORDENES.IORDFCHING dt,
					GXVSIM.IORDENES.IORDTPO tp,
					GXVSIM.IORDENES.IABOCODREP asscod,
					'cm' svc
				FROM GXVSIM.IORDENES
				WHERE
					GXVSIM.IORDENES.PERCOD = $per
					AND GXVSIM.IORDENES.IORDSTS = 'E'
					AND GXVSIM.IORDENES.IORDAGEFCH = '{$d}'")->result_array();
		if(!$cm)
			$cm = array();
		$x = array_merge($tv,$cm);
		$oss = array();
		foreach($x as $os){
			$new =
				array(
					'per' => $per,
					'os' => intval($os['OS']),
					'ag' => (($os['AG'])
								?strtotime($os['AG'].' 00:00:00')
								:0
							),
					'ingr' => strtotime($os['DT'].' '.h_parse($os['H'])),
					'svc' => strtolower($os['SVC']),
					'asscod' => intval($os['ASSCOD']),
					'loaded' => false
				);
			$new['os_tipo'] = tpName($os['TP'],$new['svc']);
			$new['turno'] = $this->turno($os['TUR']);
			$oss[] = $new;
		}


		usort($oss,function($a,$b){
			if($a['turno']['ini'] < $b['turno']['ini'])
				return -1;
			elseif($a['turno']['ini'] > $b['turno']['ini'])
				return 1;
			else{
				if($a['asscod'] < $b['asscod']){
					return -1;
				}elseif($a['asscod'] > $b['asscod']){
					return 1;
				}else{
					if($a['ingr'] < $b['ingr']){
						return -1;
					}elseif($a['ingr'] > $b['ingr']){
						return 1;
					}else{
						return 0;
					}
				}

			}
		});
		return $oss;
	}
	function siga_motivos(){
		$this->load_supsiga();
		$m = array();
		$m['tv'] =
			$this->supsiga->select('GXVSIM.REPMOT.REPMOTDSC as "name", GXVSIM.REPMOT.REPMOTCOD as "cod"')->order_by('name')->get('GXVSIM.REPMOT')->result_array();
		$m['cm'] =
			$this->supsiga->select('GXVSIM.IORDMOT.IORDMOTDSC as "name", GXVSIM.IORDMOT.IORDMOTCOD as "cod"')->order_by('name')->get('GXVSIM.IORDMOT')->result_array();
		return $m;
	}
	function siga_causas(){
		$this->load_supsiga();
		$c = array();
		$c['tv'] =
			$this->supsiga->select('GXVSIM.CAUSFALLA.CAUSFALLDSC as "name", GXVSIM.CAUSFALLA.CAUSFALLNRO as "cod"')->order_by('name')->get('GXVSIM.CAUSFALLA')->result_array();
		$c['cm'] =
			$this->supsiga->select('GXVSIM.ICAUFAL.ICAUSFALLDSC as "name", GXVSIM.ICAUFAL.ICAUSFALLNRO as "cod"')->order_by('name')->get('GXVSIM.ICAUFAL')->result_array();
		return $c;
	}
	function check_login(){
		if(!$this->user['login']){
			kickuser($this->input->is_ajax_request());
		}
	}
	function eventos_abertos($type = false,$perlist=null,$fullPerName = false,$dia = null){
		$magic =
		"SELECT
			tt.id,
			tt.ini,
			tt.descr,
			tt.loc,
			tt.type,
			tt.per,
			tt.status,
			tt_status.name as stname,
			tt.last_update as last_updated,
			tt.deadline as deadline,
			UCASE(tt_type.abbr) as t_abbr,
			UCASE(tt_type.name) as t_name,
			tt_type.order as t_order
		FROM tt
		JOIN tt_status ON tt_status.id = tt.status
		JOIN tt_type ON tt_type.id = tt.type
		WHERE
			tt_status.name != 'cancelado' 
			AND tt.type != 'backlog'
			".(($dia)
				?" AND DATE(tt.ini) = ".$this->db->escape($dia)." "

				:" AND tt_status.name != 'fechado' 
				AND tt_status.name != 'ativo' "
			).
			(($type)
				?
					" AND tt.type = ".$this->db->escape($type)
				:
					''
			)."
		ORDER BY t_order,id";
		
		$d = $this->db->query($magic)->result_array();
		if(!$d)
			$d = array();
		foreach($d as $y => $t){
			$t['ini_d'] = date('d/m/y H:i',strtotime($t['ini']));
			$t['t_deadline'] = strtotime($t['deadline']) * 1000;
			$t['s_deadline'] = $t['deadline'];
			$t['deadline'] = date('d/m/y H:i',strtotime($t['deadline']));
			$t = $this->ttLocation($t,true);

			$pers = array();
			foreach($t['pers'] as $per){
				$p = $this->db->where('id',$per)->get('per')->row_array();
				if($fullPerName)
					$pers[] = $p['name'];
				else
					$pers[] = $p['abbr'];
			}

			$t['pper'] = implode(', ', $pers);

			$d[$y] = $t;
			if( !( !$perlist || !$t['pers'] || array_intersect($t['pers'],$perlist) )  ){
				unset($d[$y]);
			}

		}

		return array_values($d);
	}
	function ttLocation($tt,$filterOk = false){
		$cidades = 
			$this->db->
				select('tt_location.id,
					tt_location.location,
					tt_location.location_type,
					tt_location.ok,
					per.id per')->
				where('tt_location.tt',$tt['id'])->
				where('tt_location.location_type','cidade')->
				join('per','per.name = tt_location.location','left')->
				order_by('ok','asc')->
				get('tt_location')->result_array();

		$nodes = 
			$this->db->
				select('tt_location.id,
					tt_location.location,
					tt_location.location_type,
					tt_location.ok,
					node.per')->
				where('tt_location.tt',$tt['id'])->
				where('tt_location.location_type','node')->
				join('node','node.node = tt_location.location','left')->
				order_by('ok','asc')->
				get('tt_location')->result_array();

		$others =
			$this->db->
				select('tt_location.id,
					tt_location.location,
					tt_location.location_type,
					tt_location.ok')->
				where('tt_location.tt',$tt['id'])->
				where('tt_location.location_type !=','node')->
				where('tt_location.location_type !=','cidade')->
				order_by('ok','asc')->
				get('tt_location')->result_array();

		$tt['locations'] = array_merge($nodes,$others,$cidades);

		usort($tt['locations'],function($a,$b){
			if(def_bool($a['ok']) === def_bool($b['ok']))
				return 0;
			return (def_bool($a['ok']) && !def_bool($b['ok'])) ? -1 : 1;
		});

		$tt['location'] = array();

		$tt['pers'] = array();
		usort($tt['locations'], function($a,$b){
			if($a['location_type'] !== 'cidade' && $b['location_type'] === 'cidade'){
				return -1;
			}elseif($a['location_type'] === 'cidade' && $b['location_type'] !== 'cidade'){
				return 1;
			}else{
				return 0;
			}
		});
		foreach($tt['locations'] as $i => $a){
			$a['ok'] = def_bool($a['ok']);
			if($a['ok'])
				$tt['location'][] = "<del>".mb_strtoupper($a['location'])."</del>";
			else
				$tt['location'][] = "<b>".mb_strtoupper($a['location'])."</b>";

			if(array_key_exists('per',$a) && $a['per']){
				$a['per'] = intval($a['per']);
				if(!in_array($a['per'], $tt['pers'])){
					$tt['pers'][] = $a['per'];
				}
			}
			$tt['locations'][$i] = $a;
			if($a['ok'] && $filterOk)
				unset($tt['locations'][$i]);
		}
		$tt['locations'] = array_values($tt['locations']);
		$tt['location'] = implode('; ',$tt['location']);
		return $tt;
	}
	function reclamacoes_iar_mes($mes,$areaID){
		$mesINI = null; $mesEND = null;
		if($mes){
			$t = strtotime($mes);
			$mesINI = date('Y-m',$t).'-01 00:00:00';
			$mesEND = date('Y-m-t',$t).' 23:59:59';
		}
		return $this->_reclamacoes_iar($mesINI,$mesEND,$areaID);
	}
	function reclamacoes_iar_semana($semana,$areaID){
		$ds = days_in_week($semana);
		if(strlen($ds[0]) === 10)
			$ds[0] .= ' 00:00:00';
		if(strlen($ds[6]) === 10)
			$ds[6] .= ' 23:59:59';
		return $this->_reclamacoes_iar($ds[0],$ds[6],$areaID,'real_ini');
	}
	function reclamacoes_iar_dia($dia,$areaID){
		return $this->_reclamacoes_iar($dia,$dia,$areaID,'real_ini');
	}
	function _reclamacoes_iar($mesINI,$mesEND,$areaID,$campo = 'ingresso'){

		if($areaID)
			$this->db->join('per','per.id = os_fin.per')->where('per.cst_area',$areaID);
		if($mesINI !== $mesEND){
			$this->db->where("os_fin.{$campo} BETWEEN '{$mesINI}' AND '{$mesEND}'",NULL,FALSE);
		}else{
			$this->db->where("date(os_fin.{$campo}) = ".$this->db->escape($mesINI),NULL,FALSE);
		}
		return 
			$this->db->
				select(
					'os_fin.os,
					os_fin.per,
					os_fin.svc,
					os_fin.ingresso,
					os_fin.real_ini,
					os_fin.min_ini,
					os_fin.max_ini')->
				where("os_fin.real_ini IS NOT NULL",NULL,FALSE)->
				where('os_fin.tipo','reclamação')->
				order_by('ingresso','asc')->
				get('os_fin')->result_array();
	}
	function _cja_os_list(
		$areaID = null,
		$id,
		$tipo = null,
		$dIni = false,
		$dEnd = false,
		$cumpr = false
	){
		$campo = ($cumpr)?'real_ini':'ingresso';
		
		if(is_array($tipo)){
			$tipos = array();
			foreach($tipo as $t)
				$tipos[] = $this->db->escape($t);
			$tipo = $tipos;
		}

		$selector = '';
		switch ($id) {
			case 'conforme':
				$selector = 
					"( ( os_fin.offset IS NULL AND os_fin.inside IS NOT NULL )
							OR ( os_fin.offset > -300 AND os_fin.offset < 300 ) )";
				break;
			case 'antes':
				$selector = 
					"os_fin.offset < -300";
				break;
			case 'depois':
				$selector = 
					"DATE(os_fin.real_ini) = DATE(os_fin.min_ini)
						 AND os_fin.offset > 300";
				break;
				
			case 'outro_dia':
				$selector = 
					"DATE(os_fin.real_ini) > DATE(os_fin.min_ini)";
				break;
			case 'indefinido':
				$selector = 
					" ( os_fin.offset IS NULL AND os_fin.inside IS NULL ) ";
				break;
		}
		$magic = 
			"SELECT
				os_fin.os,
				os_fin.per,
				os_fin.svc,
				os_fin.real_ini,
				os_fin.min_ini,
				os_fin.max_ini,
				os_fin.offset
			FROM os_fin
			JOIN per ON ( per.id = os_fin.per
			".(($areaID)
				?" AND per.cst_area = ".$this->db->escape($areaID)
				:''
			)." )
			WHERE 
				$selector "
				.(($dIni && $dEnd)
					?(($dIni === $dEnd)
						?" AND DATE(os_fin.{$campo}) = '{$dIni}' "
						:" AND DATE(os_fin.{$campo}) BETWEEN '{$dIni}' AND '{$dEnd}' ")
					:''
				).(($tipo)
					?	( ( is_array($tipo) )
							?" AND os_fin.tipo IN (".implode(', ',$tipo).") "
							:" AND os_fin.tipo = ".$this->db->escape($tipo)
						)
					:''
				);
		return $this->db->query($magic)->result_array();
	}
	function _cja_ind(
		$areaID = null,
		$tipo = null,
		$clean = false,
		$dIni = false,
		$dEnd = false,
		$cumpr = false
	){
		$campo = ($cumpr)?'real_ini':'ingresso';
		
		if($dIni && strlen($dIni) === 10)
			$dIni = "{$dIni} 00:00:00";

		if($dEnd && strlen($dEnd) === 10)
			$dEnd = "{$dEnd} 23:59:59";

		if( is_array($tipo) ){
			$tipos = array();
			foreach($tipo as $t)
				$tipos[] = $this->db->escape($t);
			$tipo = $tipos;
		}

		$mesmoDIA = "DATE(os_fin.real_ini) = DATE(os_fin.min_ini)";
		$magic = 
			"SELECT 
					SUM(conforme) conforme,
					SUM(total) total,
					SUM(antes) antes,
					SUM(depois) depois,
					SUM(outro_dia) outro_dia,
					SUM(indefinido) indefinido
			FROM (
				SELECT
					IF(
						( os_fin.offset IS NULL AND os_fin.inside IS NOT NULL )
							OR ( os_fin.offset > -300 AND os_fin.offset < 300 )
					,1,0) conforme,
					1 total,
					IF(os_fin.offset < -300,1,0) antes,
					IF({$mesmoDIA} AND os_fin.offset > 300,1,0) depois,
					IF(DATE(os_fin.real_ini) > DATE(os_fin.min_ini),1,0) outro_dia,
					IF(os_fin.offset IS NULL AND os_fin.inside IS NULL, 1, 0 ) indefinido
				FROM os_fin
				JOIN per ON ( per.id = os_fin.per
				".(($areaID)
					?" AND per.cst_area = ".$this->db->escape($areaID)
					:''
				)." )
				WHERE
					1 = 1 "
				.(($dIni && $dEnd)
					?(($dIni === $dEnd)
						?" AND DATE(os_fin.{$campo}) = '{$dIni}' "
						:" AND os_fin.{$campo} BETWEEN '{$dIni}' AND '{$dEnd}' ")
					:''
				).(($tipo)
					?	( ( is_array($tipo) )
							?" AND os_fin.tipo IN (".implode(', ',$tipo).") "
							:" AND os_fin.tipo = ".$this->db->escape($tipo)
						)
					:''
				).(($clean)
					?" AND NOT ( os_fin.offset IS NULL AND os_fin.inside IS NULL ) "
					:''
				)." ) x";

		
		$c = $this->db->query($magic)->row_array();
		$n_conforme = intval($c['total']) - intval($c['conforme']);
		return array(
			'conforme' => nozeropercent($c['conforme'],$c['total']),
			'indefinido' => nozeropercent($c['indefinido'],$c['total']),
			'antes' => nozeropercent($c['antes'],$c['total']),
			'depois' => nozeropercent($c['depois'],$c['total']),
			'outro_dia' => nozeropercent($c['outro_dia'],$c['total']),
			'n_conforme' => nozeropercent($n_conforme,$c['total']),
			'totals' => array(
				'total' => intval($c['total']),
				'conforme' => intval($c['conforme']),
				'indefinido' => intval($c['indefinido']),
				'antes' => intval($c['antes']),
				'depois' => intval($c['depois']),
				'outro_dia' => intval($c['outro_dia']),
				'n_conforme' => $n_conforme
			)
		);
	}
	function cja_ind_mes($areaID,$mes,$tipo = null,$clean = false){
		$mesINI = null; $mesEND = null;
		if($mes){
			$t = strtotime($mes);
			$mesINI = date('Y-m',$t).'-01';
			$mesEND = date('Y-m-t',$t);
		}
		return $this->_cja_ind($areaID,$tipo,$clean,$mesINI,$mesEND);
	}
	function cja_os_list_mes($areaID,$mes,$id,$tipo = false){
		$mesINI = null; $mesEND = null;

		if($mes){
			$t = strtotime($mes);
			$mesINI = date('Y-m',$t).'-01';
			$mesEND = date('Y-m-t',$t);
		}
		return $this->_cja_os_list($areaID,$id,$tipo,$mesINI,$mesEND);
	}
	function cja_os_list_semana($areaID,$semana,$id,$tipo = false){
		$ds = days_in_week($semana);
		return $this->_cja_os_list($areaID,$id,$tipo,$ds[0],$ds[6],true);
	}
	function cja_os_list_dia($areaID,$dia,$id,$tipo = false){
		return $this->_cja_os_list($areaID,$id,$tipo,$dia,$dia,true);
	}
	function cja_ind_semana($areaID,$semana,$tipo = null,$clean = false){
		$ds = days_in_week($semana);
		return $this->_cja_ind($areaID,$tipo,$clean,$ds[0],$ds[6],true);
	}
	function cja_ind_dia($areaID,$dia,$tipo = null,$clean = false){
		return $this->_cja_ind($areaID,$tipo,$clean,$dia,$dia,true);
	}
	function cja_gen($os){
		$this->load_supsiga();
		$os['svc'] = strtolower($os['svc']);
		$ag = null; $turno = null;

		if($os['svc'] === 'cm'){
			$ag =
				trim_em_all(
					$this->supsiga->
						select('IORDAGEFCH agenda,IORDAGETUR turno')->
						where('IORDAGETUR >',0)->
						where('IORDAGEFCH >','01-JAN-12')->
						where('IORDNRO',$os['os'])->
						where('PERCOD',$os['per'])->
						get('IORDENES')->row_array(),
					true);
		}else{
			$ag =
				trim_em_all(
					$this->supsiga->
						select('RECAGEFCH agenda,RECAGETUR turno')->
						where('RECAGETUR >',0)->
						where('RECAGEFCH >','01-JAN-12')->
						where('RECNRO',$os['os'])->
						where('PERCOD',$os['per'])->
						get('REPARA')->row_array(),
					true);
		}

		if($ag)
			$turno = $this->db->where('id',$ag['turno'])->get('turno')->row_array();

		if($turno){
			$agenda = date('Y-m-d',strtotime($ag['agenda']));
			$minT = strtotime($agenda.' '.$turno['ini']);
			$maxT = strtotime($agenda.' '.$turno['end']);
			$realT = strtotime($os['real_ini']);
			$ag = 
				array(
					'tec_schedule_os' => $os['id'],
					'real_ini' => $os['real_ini'],
					'min_ini' => date('Y-m-d H:i:s',$minT),
					'max_ini' => date('Y-m-d H:i:s',$maxT)
				);

			if($realT >= $minT && $realT <= $maxT)
				$ag['inside'] = $realT - $minT;
			else
				$ag['offset'] = $realT - ( ($realT > $maxT) ? $maxT : $minT );
		}
		return $ag;		
	}

	function workgrid_auto_name ($grid, $short = false) {
		
		$id = is_array($grid) ? $grid['id'] : $grid;
		$id = $this->db->escape($id);
		$days = 
			$this->db->query(
				"select
				    workgrid_id,
				    work_shift,
				    workgrid.lunch_ini,
				    group_concat(weekday_id order by weekday_id separator ',') w_id,
				    group_concat(weekday order by weekday_id separator ', ') weekdays
				FROM (
				    select 
				        workgrid workgrid_id,
				        concat(
				            time_format(MIN(ini),'%H:%i'),
				            ' às ', 
				            DATE_FORMAT(
				                FROM_UNIXTIME(
				                    unix_timestamp(
				                        concat(
				                            CURDATE(),' ',MAX(end)
				                        )
				                    ) + 1
				                ),'%H:%i'
				            ) 
				        ) work_shift,
				        ".($short ? "left(weekday.name, 3)" : "weekday.name")." weekday,
				        weekday.id weekday_id
				    from workgrid_entry
				    join timegrid ON timegrid.id = workgrid_entry.timeID
				    join weekday on timegrid.weekday = weekday.id
				    where workgrid = {$id}
				    group by timegrid.weekday, workgrid
				    order by weekday.id
				) x
				join workgrid on workgrid.id = workgrid_id
				group by workgrid_id, work_shift
				order by w_id")->result_array();
		$lunch = null;
		$wdays = array();

		foreach ($days as $day) {
			$lunch = $lunch ? $lunch : $day['lunch_ini'];
			$ds = explode(', ', $day['weekdays']);
			$wdays[] = "{$day['work_shift']}: ".array_shift(explode('-', $ds[0])).(count($ds) > 1 ? ' <> '.array_shift(explode('-', array_pop($ds))) : '');
		}
		if (!$wdays) {
			return 'Horário indefinido';
		}

		return implode('; ', $wdays)."; Almoço: ".substr($lunch, 0, 5);
	}
}