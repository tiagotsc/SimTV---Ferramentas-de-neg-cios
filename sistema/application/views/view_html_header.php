<?php 
# Configurações do sistema
include_once('configSistema.php');
#header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <!--<meta charset="ISO-8859-1">-->   
    <!--<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" /> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Ferramentas de neg&oacute;cio</title>
    
<?php

# Bootstrap core CSS
echo link_tag(array('href' => 'assets/css/bootstrap.css','rel' => 'stylesheet','type' => 'text/css'));  
echo link_tag(array('href' => 'assets/css/modern-business.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/font-awesome/css/font-awesome.min.css','rel' => 'stylesheet','type' => 'text/css'));

# Css para personalização
echo link_tag(array('href' => 'assets/css/personalizado.css','rel' => 'stylesheet','type' => 'text/css')); 

# JavaScript
echo "<script type='text/javascript' src='".base_url('assets/js/jquery-1.10.2.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/bootstrap.js')."'></script>";  
echo "<script type='text/javascript' src='".base_url('assets/js/modern-business.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/util.js')."'></script>";

echo link_tag(array('href' => 'assets/js/jquery-ui/jquery-ui.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url("assets/js/jquery-ui/jquery-ui.js")."'></script>";

?>    

</head>

<body>