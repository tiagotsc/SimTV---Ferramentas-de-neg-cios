<?php

function removeAcentosSinaisEspacos($string) {

    $string = htmlentities($string, ENT_COMPAT, 'UTF-8');
    $string = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil);/', '$1',$string);

	$string = preg_replace("/-/", "", $string);
	/*$string = preg_replace("/[ÉÈÊéèê]/", "e", $string);
	$string = preg_replace("/[ÍÌíì]/", "i", $string);
	$string = preg_replace("/[ÓÒÔÕÖóòôõö]/", "o", $string);
	$string = preg_replace("/[ÚÙÜúùü]/", "u", $string);
	$string = preg_replace("/[Çç]/", "c", $string);
	$string = preg_replace("/[][><}{;,!?*%~^`&#]/", "", $string);*/
	#$string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
	#$string = preg_replace("/ /", "_", $string);
	#$string = strtolower($string);
	
	return $string;
	
}