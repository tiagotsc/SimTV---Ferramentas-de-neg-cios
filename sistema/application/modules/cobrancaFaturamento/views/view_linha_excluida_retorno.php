<!DOCTYPE HTML>
<html lang="pt-br">
   <head>
   	<meta charset="UTF-8"/>
   	<title>Linhas exclu&iacute;das</title>
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

?>    
   </head>
   <body>
    <pre>
    <?php
    
    $this->table->set_heading('Nome do banco', 'Banco', 'Data / hora lote', 'Data do arquivo');
    
    $cell1 = array('data' => $dadosArquivo[0]->nome_arquivo_retorno);
    $cell2 = array('data' => $dadosArquivo[0]->nome_banco);
    $cell3 = array('data' => $this->util->formataData(substr($dadosArquivo[0]->data_insercao_arquivo_retorno, 0, 10),'BR').''.substr($dadosArquivo[0]->data_insercao_arquivo_retorno, 10));
    $cell4 = array('data' => $this->util->formataData($dadosArquivo[0]->data_arquivo_retorno,'BR'));
        
    $this->table->add_row($cell1, $cell2, $cell3, $cell4);
    
    foreach($linhasExcluidos as $lE){
    $cell = array('data' => $lE->linha_excluido_arquivo_retorno, 'colspan' => 4);
    $this->table->add_row($cell);
    }
    
    $template = array('table_open' => '<table class="table table-bordered">');
    $this->table->set_template($template);
    echo $this->table->generate();
    
    ?>    
  	</pre>
  </body>
</html>