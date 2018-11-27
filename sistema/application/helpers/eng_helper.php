<?php
/**
* Checks a variable if it is true or false, humanlike.
* We account for values as 'on', '1', '' and so on.
* Finally, for some reare occurencies we account with
* crazy logic to fit some arrays and objects.
*
* @author Kim Steinhaug, <kim@steinhaug.com>
* @param mixed $var, the variable to check
*
* Example:
* $test = 'true';
* if(def_bool($test)){ echo 'true'; } else { echo 'false'; }
*/
function def_bool($var){
  if(is_bool($var)){
	return $var;
  } else if($var === NULL || $var === 'NULL' || $var === 'null'){
	return false;
  } else if(is_string($var)){
	$var = trim($var);
	if($var=='false'){ return false;
	} else if($var=='true'){ return true;
	} else if($var=='no'){ return false;
	} else if($var=='yes'){ return true;
	} else if($var=='off'){ return false;
	} else if($var=='on'){ return true;
	} else if($var==''){ return false;
	} else if(ctype_digit($var)){
	  if((int) $var)
		return true;
		else
		return false;
	} else { return true; }
  } else if(ctype_digit((string) $var)){
	  if((int) $var)
		return true;
		else
		return false;
  } else if(is_array($var)){
	if(count($var))
	  return true;
	  else
	  return false;
  } else if(is_object($var)){
	return true;// No reason to (bool) an object, we assume OK for crazy logic
  } else {
	return true;// Whatever came though must be something,  OK for crazy logic
  }
}
function multipleExplode($delimiters = array(), $string = ''){

	$mainDelim=$delimiters[count($delimiters)-1]; // dernier

	array_pop($delimiters);

	foreach($delimiters as $delimiter){

		$string= str_replace($delimiter, $mainDelim, $string);

	}

	$result= explode($mainDelim, $string);
	return $result;

}
function remNum($addr){
	$s = multipleExplode(array(' ',','), $addr);
	if(is_numeric($s[count($s)-1]) && count($s)>2)
		return implode(' ',array_slice($s,0,count($s)-1));
	else
		return $addr;
}
function currMonth(){
		return "Sep-2012";
}
function dmy2ymd($d){
	$date = date_create_from_format('d-m-Y', $d);
	return $date->format('Y-m-d');
}
function window_time($w){
	$s = explode('~', $w);
	return $s[1].':00:00';
}
function fCep($cep){
	if(strlen($cep) === 8)
		return substr($cep,0,5).'-'.substr($cep,5);
	else
		return $cep;
}
function fTel($tel,$ddd=false,$null=false){
	if(strlen($tel)>7)
		return (($ddd)?'('.$ddd.') ':'').substr($tel,0,4).'-'.substr($tel,4);
	else
		return (($null)?null:'---');
}
function fDate($d,$zero=false,$t=false){
	$aux = strtotime($d);
	if($aux !== false && $d){
		return prettydate($aux).
				(($t)?' - '.date('H:i:s',$aux):'');
	}else{
		if($zero)
			return 99999;
		else
			return "---";
	}
}
function breakemail($u){
	$e = explode('@',$u);
	return $e[0];
}
function fDate2($d,$zero=false,$t=false){
	$aux = strtotime($d);
	if($aux !== false && $d){
		return date('Y-m-d'.(($t)?' H:i:s':''),$aux);
	}else{
		if($zero)
			return 99999;
		else
			return "---";
	}
}

function datefy($x,$pleasenull=false,$dbf=false){
	foreach($x as $k => $y){


		if(strtoupper(trim($k)) === 'DT_GERACAO'){
			$x[$k] = date_create_from_format('d-M-Y - H:i',$y);
			$x[$k] = $x[$k]->format( (($dbf)?'Y-m-d H:i:s':'d/m/Y H:i:s') );
		}else{
			$pre1 = strtoupper(substr(trim($k),0,3));
			$pre2 = strtoupper(substr(trim($k),0,5));
			if($pre1 === 'DT_' || $pre2 === 'DATA_'){
				if($dbf)
					$aux = fDate2( $y, false, ( strlen($y) > strlen('01-JAN-01') ) );
				else
					$aux = fDate( $y, false, ( strlen($y) > strlen('01-JAN-01') ) );
				$x[$k] = (($aux === '---' && $pleasenull)?null:$aux);
			}
		}

	}
	return $x;
}

function makeBarCode($sn,$chipid,$img){
	require_once 'application/libraries/bc.php';

	$font = 'lib/arialbd.ttf';

	// create CHIPID BARCODE
	$im	 = imagecreatetruecolor(1000, 1000);
	$black  = ImageColorAllocate($im,0x00,0x00,0x00);
	$white  = ImageColorAllocate($im,0xff,0xff,0xff);
	imagefilledrectangle($im, 0, 0, 1000, 1000, $white);
	$data['chipid'] = Barcode::gd($im, $black, 200, 30, 0, "code128", $chipid, 1, 20);
	$data['sn'] = Barcode::gd($im, $black, 200, $data['chipid']['p4']['y'] + 15 + 5 + 25, 0, "code128", $sn, 1, 20);

	// create SERIAL NUMBER BARCODE
	$im2 = imagecreatetruecolor(max($data['chipid']['width'],$data['sn']['width'])+10, ($data['chipid']['height']*2)+5+15+15);
	$black  = ImageColorAllocate($im2,0x00,0x00,0x00);
	$white  = ImageColorAllocate($im2,0xff,0xff,0xff);

	// COPY TO PROPERLY SIZED IMAGE
	imagefilledrectangle($im2,
			0, 0,
			max($data['chipid']['width'],$data['sn']['width'])+10, ($data['chipid']['height']*2)+5+20+20+5,
			$white);
	imagecopyresized(
			$im2,$im,
			5,5,
			$data['chipid']['p1']['x'],$data['chipid']['p1']['y'],
			$data['chipid']['width'],$data['chipid']['height'],
			$data['chipid']['width'],$data['chipid']['height']);
	imagecopyresized(
			$im2,$im,
			5,$data['chipid']['height']+15+5,
			$data['sn']['p1']['x'],$data['sn']['p1']['y'],
			$data['sn']['width'],$data['sn']['height'],
			$data['sn']['width'],$data['sn']['height']);

	// DRAW BOTTOM TEXT
	imagettftext($im2,7, 0, 5,$data['chipid']['height'] + 5+10, $black, $font, "NUID: ".$chipid);
	imagettftext($im2, 7, 0, 5,($data['sn']['height']*2) + 5+15+10, $black, $font, "S/N: ".$sn);
	imagepng($im2, getcwd()."/media/$img");
}
function orderBYC($a){
	for($i=0;$i<count($a)-1;$i++){
		$max = $i;
		for($j=$i+1;$j<count($a);$j++){
			if(intval($a[$j]['c']) > intval($a[$max]['c'])){
				$max = $j;
			}
		}
		$temp = $a[$i];
		$a[$i] = $a[$max];
		$a[$j] = $temp;
	}
	return $a;
}
function abbrArea($area,$revert=false){
	/* $area = strtoupper($area); */
	if($revert){
		switch ($area) {
			case "AJU":
				return 'Aracaju';
				break;

			case 'CBA':
				return "Cuiabá";
				break;

			case 'FSA':
				return "Feira de Santana";
				break;

			case 'GTI':
				return "Gravataí";
				break;

			case 'JF':
				return "Juiz de Fora";
				break;

			case 'NIT':
				return "Niterói";
				break;

			case "RJOP":
				return 'RJOP';
				break;
			case 'SGO':
				return "São Gonçalo";
				break;
			case 'SSA':
				return "Salvador";
				break;

			case 'VR':
				return "Volta Redonda";
				break;
			case "SIM":
				return 'SIM';
				break;
			default:
				return false;
				break;
		}
	}else{
		switch ($area) {
			case "Aracaju":
				return 'AJU';
				break;

			case "Cuiabá":
				return 'CBA';
				break;

			case "Feira de Santana":
				return 'FSA';
				break;

			case "Gravataí":
				return 'GTI';
				break;

			case "Juiz de Fora":
				return 'JF';
				break;

			case "Niterói":
				return 'NIT';
				break;

			case "RJOP":
				return 'RJOP';
				break;
			case "São Gonçalo":
				return 'SGO';
				break;
			case "Salvador":
				return 'SSA';
				break;

			case "Volta Redonda":
				return 'VR';
				break;
			case "SIM":
				return 'SIM';
				break;
			default:
				return false;
				break;
		}
	}
}

function ptmes($dt){
	$d = explode('-',$dt);

	switch (intval($d[1])) {
		case 1:
			return 'Jan-'.substr($d[0],2);
			break;
		case 2:
			return 'Fev-'.substr($d[0],2);
			break;
		case 3:
			return 'Mar-'.substr($d[0],2);
			break;
		case 4:
			return 'Abr-'.substr($d[0],2);
			break;
		case 5:
			return 'Mai-'.substr($d[0],2);
			break;
		case 6:
			return 'Jun-'.substr($d[0],2);
			break;
		case 7:
			return 'Jul-'.substr($d[0],2);
			break;
		case 8:
			return 'Ago-'.substr($d[0],2);
			break;
		case 9:
			return 'Set-'.substr($d[0],2);
			break;
		case 10:
			return 'Out-'.substr($d[0],2);
			break;
		case 11:
			return 'Nov-'.substr($d[0],2);
			break;
		case 12:
			return 'Dez-'.substr($d[0],2);
			break;

	}
}
function strip_em_all($row){
	if(is_array($row))
		foreach(array_keys($row) as $k){
			if(is_string($row[$k])){
				$row[$k] = trim(strip_tags($row[$k]));
			}
		}
	return $row;
}
function nozeropercent($a,$b){
	$a = intval($a);
	$b = intval($b);
	if($b === 0){
		return 0;
	}else{
		return round(($a/$b)*100,2);
	}
}
function trim_em_all($row, $nocaps = false, $notnull=false){
	$nrow = array();
	foreach(array_keys($row) as $k){
			$nrow[(($nocaps)?strtolower($k):$k)] =
				((is_string($row[$k]))
					?str_replace("'",' ',trim($row[$k]))
					:(( $notnull && ($row[$k] === null || $row[$k] === false) )
						?'---'
						:$row[$k]
					)
				);
	}
	return $nrow;
}
function smart_implode($x){
	$ks = array_keys($x);
	$r = '';
	foreach ($ks as $k) {
		if(is_string($x[$k]))
			$r .= "'{$x[$k]}'";
		else
			$r .= "{$x[$k]}";
	}
}
function month_weeks($m){
	$mT = strtotime($m);
	$mm = date('Y-m',$mT);
	$ini = "{$mm}-01";
	$end = date('Y-m-t',$mT);

	$weeks = array();
	$lWeek = null;
	$i = $ini;
	$c = 0;

	do{
		$iT = strtotime($i);
		$w = date('W',$iT);

		if( !$lWeek || $lWeek !== $w ){
			$Y = date('o',$iT);
			$weeks[] = "{$Y}:{$w}";

			$lWeek = $w;
		}

		$i = new DateTime();
		$i->setTimestamp($iT);
		$i->add(new DateInterval('P1D'));
		$i = $i->format('Y-m-d');
		
		
	}while( $i < $end );

	return $weeks;
}
function days_in_week($semana){
	$s = explode(':',$semana);
	$ss = "{$s[0]}-W".str_pad($s[1],2,'0',STR_PAD_LEFT).'-1';

	$ds = array();
	$d = new DateTime();
	$d->setTimestamp(strtotime($ss));

	for($i=0;$i<7;$i++){
		$year = intval($d->format("Y"));
		$week = intval($d->format("W"));

		$d->setISODate($year, $week, $i+1);
		$ds[] = $d->format('Y-m-d');
	}
	return $ds;
}
function week_of_month($date) {
	$date_parts = explode('-', $date);
	$date_parts[2] = '01';
	$first_of_month = implode('-', $date_parts);
	$day_of_first = date('N', strtotime($first_of_month));
	$day_of_month = date('j', strtotime($date));
	return floor(($day_of_first + $day_of_month - 1) / 7) + 1;
}

function statusColor($st){
	$st = strtoupper($st);
			if($st == 'FINALIZADA'){
					return '#058DC7';
			}else if($st == 'FINALIZADO'){
					return '#058DC7';

			}else if($st == 'CANCELADA'){
					return '#50b432';
			}else if($st == 'CANCELADO'){
				return '#50b432';


			}else if($st == 'PENDENTE'){
					return '#ff6f34';


			}else if($st == 'EMITIDO'){
					return '#DDDF00';
			}else if($st == 'EMITIDA'){
					return '#DDDF00';


			}else if($st == 'AGENDADO'){
					return '#24CBE5';
			}else if($st == 'AGENDADA'){
					return '#24CBE5';

			}else if($st == 'SUSPENSO'){
					return '#2B2B2B';
			}else if($st == 'SUSPENSA'){
					return '#2B2B2B';

			}else if($st == 'REAGENDAR'){
					return '#64E572';

			}else if($st == 'REAGENDADA'){
					return '#FF9655';
			}else if($st == 'REAGENDADO'){
					return '#FF9655';
			}
	}
function noCountryForOldMen($fs){
	foreach($fs as $f){
		$fdate = new DateTime();
		$fdate->setTimestamp(filemtime($f));
		$now = new DateTime();
		date_add($fdate, date_interval_create_from_date_string('1 day'));
		if($fdate < $now)
			unlink($f);
	}
}
function getAllFiles($directory, $recursive = false) {
	 $result = array();
	 $handle =  opendir($directory);
	 while ($datei = readdir($handle))
	 {
		  if (($datei != '.') && ($datei != '..'))
		  {
			   $file = $directory.$datei;
			   if (is_dir($file)) {
					if ($recursive) {
						 $result = array_merge($result, getAllFiles($file.'/'));
					}
			   } else {
					$result[] = $file;
			   }
		  }
	 }
	 closedir($handle);
	 return $result;
}
function xTimeAgo($start, $end=null, $upToX = 2) {
    if(!($start instanceof DateTime)) { 
        $start = new DateTime($start); 
    } 
    
    if($end === null) { 
        $end = new DateTime(); 
    } 
    
    if(!($end instanceof DateTime)) { 
        $end = new DateTime($end); 
    } 
    
    $interval = $end->diff($start); 
    $doPlural = function($nb,$str){
    	return (($nb>1)
    				?(($str === 'mês')?'meses':$str.'s')
    				:$str
				);
	}; // adds plurals 
    
    $format = array(); 
    if($interval->y !== 0) { 
        $format[] = "%y ".$doPlural($interval->y, "ano"); 
    } 
    if($interval->m !== 0) { 
        $format[] = "%m ".$doPlural($interval->m, "mês"); 
    } 
    if($interval->d !== 0) { 
        $format[] = "%d ".$doPlural($interval->d, "dia"); 
    } 
    if($interval->h !== 0) { 
        $format[] = "%h ".$doPlural($interval->h, "hora"); 
    } 
    if($interval->i !== 0) { 
        $format[] = "%i ".$doPlural($interval->i, "minuto"); 
    } 
    if($interval->s > 0 || !$format) { 
        $format[] = "%s ".$doPlural($interval->s, "segundo");
    }
    
    // We use the two biggest parts 
    if(count($format) > 1) {
		
		$c = min(array(count($format),$upToX));
		$format = implode(' e ', array_slice( $format, 0, $c ) );
    } else { 
        $format = array_pop($format); 
    } 
    
    // Prepend 'since ' or whatever you like 
    return $interval->format($format); 
} 
function timediffbr($t){
	if(is_string($t))
		$t = strtotime($t);

	$now = new DateTime();
	
	$d = new DateTime();
	$d->setTimestamp($t);
	$interval = $now->diff($d);

	return $interval->format("");
}
function min_to_h($m){
	$d = array();
	$h = floor( $m / 60 );
	if($h)
		$d[] = $h.' hora'.(($h>1)?'s':'');
	$m = $m%60;
	if($m)
		$d[] = $m.' minuto'.(($m>1)?'s':'');
	return implode(', ',$d);
}
function fLower($txt){
	return mb_strtolower(trim($txt),'UTF-8');
}
function isOS($os){
	return is_array($os) && array_key_exists('os',$os) && array_key_exists('per',$os) && array_key_exists('svc',$os) && $os['os'] && $os['per'] && $os['svc'];
}
function fCap($txt){
	if($txt && is_string($txt))
		return ucwords(mb_strtolower(trim($txt),'UTF-8'));
	else
		return '';
}
function otherDiffDate($end='2020-06-09 10:30:00', $out_in_array=true){
		$intervalo = date_diff(date_create(), date_create($end));
		$out = $intervalo->format("Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s");
		if(!$out_in_array)
			return $out;
		$a_out = array();
		array_walk(explode(',',$out),
		function($val,$key) use(&$a_out){
			$v=explode(':',$val);
			$a_out[$v[0]] = $v[1];
		});
		return $a_out;
}
function fDiff($d){
	$r = '';
	if($d['Years'] > 0)
		$r .= $d['Years'].' Anos';
	if($d['Months']>0)
		$r .= (($d['Years']>0)?' e ':'').$d['Months'].' Meses';
	return $r;
}
function find_cfm_index($n,$x){
	foreach($x as $k=>$a){
		if($a['name'] === $n)
			return $k;
	}
	return -1;
}
function tcmes1($x,$y,$sts){
	if($sts === 'ok')
		$n_sts = 'not_ok';
	else
		$n_sts = 'ok';
	if($y['CAUSA']){
		$pos = find_cfm_index($y['CAUSA'], $x['diag']['causas']['a']);
		if($pos === -1){
			$x['diag']['causas']['a'][] = array('name'=>$y['CAUSA'],'total'=>array($sts=>0,$n_sts=>0));
			$pos = count($x['diag']['causas']['a'])-1;
		}
		$x['diag']['causas']['a'][$pos]['total'][$sts]++;
	}

	if($y['FALHA']){
		$pos = find_cfm_index($y['FALHA'], $x['diag']['falhas']['a']);
		if($pos === -1){
			$x['diag']['falhas']['a'][] = array('name'=>$y['FALHA'],'total'=>array($sts=>0,$n_sts=>0));
			$pos = count($x['diag']['falhas']['a'])-1;
		}
		$x['diag']['falhas']['a'][$pos]['total'][$sts]++;
	}

	if($y['MOTIVO']){
		$pos = find_cfm_index($y['MOTIVO'], $x['diag']['motivos']['a']);
		if($pos === -1){
			$x['diag']['motivos']['a'][] = array('name'=>$y['MOTIVO'],'total'=>array($sts=>0,$n_sts=>0));
			$pos = count($x['diag']['motivos']['a'])-1;
		}
		$x['diag']['motivos']['a'][$pos]['total'][$sts]++;
	}
	return $x;
}
function tcmes2($x,$y){
	if($y['CAUSA']){
		$pos = find_cfm_index($y['CAUSA'], $x['diag']['causas']['b']);
		if($pos === -1){
			$x['diag']['causas']['b'][] = array('name'=>$y['CAUSA'],'total' => 0);
			$pos = count($x['diag']['causas']['b'])-1;
		}
		$x['diag']['causas']['b'][$pos]['total']++;
	}

	if($y['FALHA']){
		$pos = find_cfm_index($y['FALHA'], $x['diag']['falhas']['b']);
		if($pos === -1){
			$x['diag']['falhas']['b'][] = array('name'=>$y['FALHA'],'total' => 0);
			$pos = count($x['diag']['falhas']['b'])-1;
		}
		$x['diag']['falhas']['b'][$pos]['total']++;
	}

	if($y['MOTIVO']){
		$pos = find_cfm_index($y['MOTIVO'], $x['diag']['motivos']['b']);
		if($pos === -1){
			$x['diag']['motivos']['b'][] = array('name'=>$y['MOTIVO'],'total' => 0);
			$pos = count($x['diag']['motivos']['b'])-1;
		}
		$x['diag']['motivos']['b'][$pos]['total']++;
	}
	return $x;
}
function cfmSort($a,$s=false){
	for($i=0;$i<(count($a)-1);$i++){
		$mx = $i;
		for($j=$i;$j<count($a);$j++){
			if($s){
				if($a[$j]['total']['not_ok'] > $a[$mx]['total']['not_ok'])
					$mx = $j;
			}else{
				if($a[$j]['total'] > $a[$mx]['total'])
					$mx = $j;
			}
		}
		if($mx !== $i){
			$aux = $a[$i];
			$a[$i] = $a[$mx];
			$a[$mx] = $aux;
		}
	}
	return $a;
}

function page_avaible($perms){
	if(strpos($perms, 'a') !== false)
		return 'dashboard';
	else if(strpos($perms, 'e') !== false)
		return 'events';
	else if(strpos($perms, 'x') !== false)
		return 'adm';
	else if(strpos($perms, 'm') !== false)
		return 'mon';
	else if(strpos($perms, 'l') !== false)
		return 'loc';
	else return 'display';
}
function stName($st){
	switch ($st) {
		case 'A':
			return 'Agendada';
			break;
		case 'P':
			return 'Pendente';
			break;
		case 'F':
			return 'Finalizada';
			break;
		case 'E':
			return 'Emitida';
			break;
		case 'C':
			return 'Cancelada';
			break;
		case 'R':
			return 'Reagendada';
			break;
		case 'S':
			return 'Suspensa';
			break;
		default:
			return 'Pendente';
			break;
	}
}
function pacTipo($p){
	if($p && is_string($p))
		$p = trim(strtoupper($p));
	switch($p){
		case 'A':
			return 'adicional';
		case 'B':
			return 'principal';
		case 'P':
			return 'extra';
		default:
			return null;
	}
}
function subTipo($tp,$svc = 'tv'){
	if($tp)
		$tp = strtoupper($tp);
	else
		$tp = null;
	$svc = strtolower($svc);
	if($svc === 'tv'){
		switch ($tp) {
			//DECODE(R.RECFLGGEN, 
			//'B', 'BAIXA TOTAL', 
			//'M', 'MOROSIDADE', 
			//'N', 'MUDANCA DE NIVEL', 
			//'Z', 'MUDANCA DE ENDERECO', 
			//'R', 'REINSTALAR', 
			//'Y', 'CONTRATO NOVO', 
			//'G', 'MELHORA', 
			//'H', 'INST CANC ZONA N??O HAB', R.RECFLGGEN) DESCRICAOOS
			case 'B':
				return 'Baixa total';
				break;
			case 'M':
				return 'Morosidade';
				break;
			case 'N':
				return 'Mudança de nível';
				break;
			case 'Z':
				return 'Mudança de endereço';
				break;
			case 'R':
				return 'Reinstalar';
				break;
			case 'Y':
				return 'Contrato novo';
				break;
			case 'G':
				return 'Melhora';
				break;
			case 'H':
				return 'Instalação cancelada zona não habilitada';
				break;
			default:
				return NULL;
				break;
		}
	}elseif($svc === 'cm'){
		//DECODE(R.IORDORG, 
		//'S', 'SERVICO', 
		//'C', 'CONTRATO', 
		//'M', 'MOROSIDADE', 
		//'Z', 'MUDANCA', 
		//'R', 'RECONEXAO', 
		//'D', 'DOWNGRADE', 
		//'T', 'TROCA', 
		//'U', 'UPGRADE', R.IORDORG)
		switch ($tp) {
			case 'S':
				return 'Serviço';
				break;
			case 'C':
				return 'Contrato novo';
				break;
			case 'M':
				return 'Morosidade';
				break;
			case 'Z':
				return 'Mudança';
				break;
			case 'R':
				return 'Reconexão';
				break;
			case 'D':
				return 'Downgrade';
				break;
			case 'T':
				return 'Troca';
				break;
			case 'U':
				return 'Upgrade';
				break;
			default:
				return NULL;
				break;
		}
	}
}


function tpName($tp,$svc = 'tv'){
	$tp = strtoupper($tp);
	$svc = strtolower($svc);
	if($svc === 'cm'){
		switch ($tp) {
			case 'D':
				return 'desconexão';
				break;
			case 'P':
				return 'instalação';// PC';
				break;
			case 'R':
				return 'instalação';// Rede';
				break;
			case 'A':
				return 'acessórios';
				break;
			case 'S':
				return 'reclamação';
				break;
			case 'C':
				return 'w';
				break;
			default:
				return 'indefinido';
				break;
		}
	}elseif($svc === 'tv'){
		switch ($tp) {
			case 'R':
				return 'reclamação';
				break;
			case 'I':
				return 'instalação';
				break;
			case 'D':
				return 'desconexão';
				break;
			case 'M':
				return 'mudança';
				break;
			default:
				return 'indefinido';
				break;
		}
	}
}
function perms_needed($page){
	switch ($page) {
		case 'dashboard':
			return 'a';
			break;
		case 'adm':
			return 'x';
			break;
		case 'ass':
			return 'a';
			break;
		case 'acomp':
			return 'a';
			break;
		case 'events':
			return 'e';
			break;
		case 'mon':
			return 'm';
			break;
		case 'loc':
			return 'l';
			break;
	}
}
function h_parse($h,$r = false){
	if(!$r){
		$h = str_pad($h, 4, '0', STR_PAD_LEFT);
		$min = substr($h,-2);
		$hor =  substr($h,-4,2);
		return $hor.':'.$min;
	}else{
		$hs = explode(':',$h);
		return intval($hs[0]).''.intval($hs[1]);
	}
}

function ping($host,$port=80,$timeout=6)
{
		$fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
		if ( ! $fsock )
		{
				return FALSE;
		}
		else
		{
				return TRUE;
		}
}
function sem_format($s){
	$x = explode('-', $s);
	return "{$x[0]}º Sem. ".monthbr($x[1]).", {$x[2]}";
}
function prettydate($t){
	$m = monthbr(date('M',$t));
	$d = date('d',$t);$y = date('Y',$t);
	return "$m $d, $y";
}
function monthbr($m){
	switch (strtolower($m)) {
		case 'jan':
			return 'Jan';
			break;
		case 'feb':
			return 'Fev';
			break;
		case 'mar':
			return 'Mar';
			break;
		case 'apr':
			return 'Abr';
			break;
		case 'may':
			return 'Mai';
			break;
		case 'jun':
			return 'Jun';
			break;
		case 'jul':
			return 'Jul';
			break;
		case 'aug':
			return 'Ago';
			break;
		case 'sep':
			return 'Set';
			break;
		case 'oct':
			return 'Out';
			break;
		case 'nov':
			return 'Nov';
			break;
		case 'dec':
			return 'Dez';
			break;
		default:
			return $m;
			break;
	}
}
function diaBR($t = false){
	if($t){
		$t = getdate($t);
	}else{
		$t = getdate();
	}
	$diasdasemana = array (1 => "Segunda-Feira",2 => "Terça-Feira",3 => "Quarta-Feira",4 => "Quinta-Feira",5 => "Sexta-Feira",6 => "Sábado",0 => "Domingo");
	return $diasdasemana[$t['wday']];
}
function mesAno($t = false){
	return mesBR($t)." de ".date('Y');
}
function mesBR($t = false){
	if($t){
		$t = getdate($t);
	}else{
		$t = getdate();
	}
	$meses = array (1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");
	return $meses[$t['mon']];
}


function daybr($m){
	switch (strtolower($m)) {
		case 'sunday':
			return 'Domingo';
			break;
		case 'monday':
			return 'Segunda';
			break;
		case 'tuesday':
			return 'Terça';
			break;
		case 'wednesday':
			return 'Quarta';
			break;
		case 'thursday':
			return 'Quinta';
			break;
		case 'friday':
			return 'Sexta';
			break;
		case 'saturday':
			return 'Sábado';
			break;
	}
}
function per_decode($p){
	if(is_array($p))
		return $p;
	else if($p === null)
		return null;
	else{
		$x = json_decode($p,true);
		if(!is_array($x))
			$x = array($x);
		return $x;
	}
}

function tt_user_check($x,$g,$p = false,$notmod = false){
	if($p !== false && $p !== null)
		$p = intval($p);
	//write_file('/tmp/tt_user_check', "\n-------------".date('Y-m-d H:i:s')."---------------\n".json_encode($x)."\n".json_encode($g)."\n".  json_encode($p),'a');
	if(!is_array($g))
		$g = array($g);
	if(!in_array('mod',$g) && !$notmod)
		$g[] = 'mod';
	foreach($x as $k){
		$this_p = per_decode($k['per']);
		//write_file('/tmp/tt_user_check',"\n".json_encode($this_p)."\n".  json_encode($k),'a');
		if($p === false){
			if(in_array($k['group'],$g)){
				return true;
			}
		}else{
			if( in_array($k['group'],$g) && ( $this_p === null || in_array($p,$this_p) ) ){
				return true;
			}
		}

	}
	return false;
}
function status_group($st){
	$st = strtolower($st);
	switch ($st) {
		case 'noc':
			return 'noc';
			break;
		case 'pendente':
			return 'noc';
			break;
		case 'cancelado':
			return -1;
			break;
		case 'fechado':
			return 'mod';
			break;
		case 're':
			return 're';
			break;
		case 'ri':
			return 'ri';
			break;
		case 'he':
			return 'he';
			break;
		case 'dc':
			return 'dc';
			break;
		default:
			return $st;
			break;
	}
}
function uri_hash($controller, $args){
	return $controller.'#!'.  rawurlencode(json_encode($args));
}
function kickuser($is_ajax){
	if($is_ajax){
		exit(json_encode(array('status'=>'permission_error')));
	}else{
		$redirect_pack = array(
			'msg' => array('type' => 'error', 
					'content' => 
						'Você está sendo redirecionado pois não '.
						'tem permissão para acessar a página desejada, '.
						'faça login ou entre em contato com os Administradores do Sistema.'
					)
				);
		
		redirect(uri_hash('login', $redirect_pack));
	}
}
function tmp_path($ext = 'sql',$dir='/tmp/'){
	if (substr($dir, -1) !== '/') {
		$dir .= '/';
	}
    $fname = random_string('unique');
    return "{$dir}{$fname}.{$ext}";
}

function write_filen($path,$str){
	if($path)
		write_file($path,"{$str}\n",'a');
}
function import_to_met($path){
	$pswd = json_decode(file_get_contents('.xconfig/passwords.json'), true);
    exec("mysql -u met --password='{$pswd['met']}' met < $path");
}
function calc_q($a,$b){
    return $a.' |   '.round(($a/$b)*100,2);
}
function fFloat($x){
	return round(floatval($x),2);
}
function fNum($x,$d = 0){
	return number_format($x, $d, ',', '.');
}
function imgCheck($x,$w='100%',$h = '400px'){
	if($x && file_exists(getcwd()."/media/".$x) && !is_dir(getcwd()."/media/".$x)){
		if($w>0){
			return '
					<div class="tdimg">
						<a href="'.base_url().'media/'.$x.'" target="_blank">
							<img src="'.base_url().'media/'.$x.'" width="'.$w.'" height="'.$h.'">
						</a>
					</div>
					';
		}else{
			return '
					<div class="tdimg">
						<a href="/media/'.$x.'" target="_blank">
							<img src="/media/'.$x.'">
						</a>
					</div>
					';
		}
	}else
		return '
				<div class="tdimg">
					<img alt="SEM IMAGEM" class="tdimg" src="/lib/img/blackbox.png" width="'.$w.'" height="200px">
				</div>';
}
function trashbarcode($f){
	if($f && file_exists(getcwd()."/media/".$f)){
		unlink(getcwd()."/media/".$f);
	}
}
function trashthem($imgs){
	if($imgs['front'] && file_exists(getcwd()."/media/".$imgs['front'])){
		unlink(getcwd()."/media/".$imgs['front']);
	}
	if($imgs['front'] && file_exists(getcwd()."/media/".$imgs['back'])){
		unlink(getcwd()."/media/".$imgs['back']);
	}
	return true;
}
function warrantyCalc($d,$perms){
	if($d && $perms){
		$date = date_create($d);
		date_add($date, date_interval_create_from_date_string('365 days'));
		if(date_format($date,'Y-m-d') > date('Y-m-d'))
			return "
						<td style='background-color:green;color:white;font-weight:bold;'>
							Garantia:
								<span class='tdval'>
									".date_format($date, 'd-m-Y') . "
								</span>
						</td>";
		else
			return "
						<td style='background-color:red;color:white;font-weight:bold;'>
							Garantia:
								<span class='tdval'>
									".date_format($date, 'd-m-Y') . "
								</span>
						</td>";
	}else{
		return "<td style='background-color:#959595;color:white;font-weight:bold;'></td>";
	}
}
function check_desloc($desloc,$min_desloc_time,$max_desloc_time){
	if($desloc > $max_desloc_time)
		return $max_desloc_time * 60;
	elseif($desloc < $min_desloc_time)
		return $min_desloc_time * 60;
	else
		return $desloc * 60;
}
function geo_dist($a,$b){
	/*
	if(!array_key_exists('lat',$a))
		exit('A: '.json_encode($a));
	if(!array_key_exists('lat',$b))
		exit('A: '.json_encode($b));
	 */
	return geo_distance(floatval($a['lat']),floatval($a['lng']),floatval($b['lat']),floatval($b['lng']));
}
function geo_distance($lat1, $lon1, $lat2, $lon2, $unit = 'K')
{
   $theta = $lon1 - $lon2;
   $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
   $dist = acos($dist);
   $dist = rad2deg($dist);
   $miles = $dist * 60 * 1.1515;
   $unit = strtoupper($unit);

   if ($unit == "K")
   {
      return ($miles * 1.609344);
   }
   else
   {
      return $miles;
   }
}
function stime($x,$t = 'ini'){
	if($x["real_{$t}"])
		return $x["real_{$t}"];
	else
		return $x["scheduled_{$t}"];
}
function oscount($x){
	$c = 0;
	foreach($x as $i)
		if($i['activity'] === 'os')
			$c++;
	return $c;

}
function check_user_agent ( $type = NULL ) {
        $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
        if ( $type == 'bot' ) {
                // matches popular bots
                if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
                        return true;
                        // watchmouse|pingdom\.com are "uptime services"
                }
        } else if ( $type == 'browser' ) {
                // matches core browser types
                if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
                        return true;
                }
        } else if ( $type == 'mobile' ) {
                // matches popular mobile devices that have small screens and/or touch inputs
                // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
                // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
                if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
                        // these are the most common
                        return true;
                } else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
                        // these are less common, and might not be worth checking
                        return true;
                }
        }
        return false;
}
function latest_script_version($script){
	$fs = glob(JS_PATH."packs/*-{$script}.js");
	if($fs){
		rsort($fs);
		$fs = $fs[0];
	}
	return ($fs)?basename($fs):null;
}

function returnDates($fromdate, $todate) {
	$ds = array();
    $fromdate = new DateTime($fromdate);
    $todate = new DateTime($todate);
    $datePeriod = new DatePeriod($fromdate, new DateInterval('P1D'), $todate->modify('+1 day'));
	foreach($datePeriod as $date) {
		$ds[] = $date->format('Y-m-d');
	}
	return $ds;
}
function relat_footer($cidade){
	return 
		"<div class='page-footer'>{$cidade}, ".
			date('H:i').' - '.diaBR().' '.date('d').' de '.mesBR().' de '.date('Y').
		"</div>";
}

function fmtPlaca($p) {
	if (!is_string($p) || strlen($p) < 7) return '';
    $p = strtoupper($p);
    return substr($p, 0, 3).'-'.substr($p, 3);
}
function is_assoc($array) {
  return (bool)count(array_filter(array_keys($array), 'is_string'));
}

function imprimeVetor($vetor){
    echo '<pre>';
    print_r($vetor);
    echo '</pre>';
    exit();
}