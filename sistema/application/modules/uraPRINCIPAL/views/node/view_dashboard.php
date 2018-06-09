<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Sim TV - Painel de Registro</title>
  
<?php
header("Refresh:5");
# Bootstrap core CSS
echo link_tag(array('href' => 'assets/css/bootstrap.css','rel' => 'stylesheet','type' => 'text/css'));

# JavaScript
echo "<script type='text/javascript' src='".base_url('assets/js/jquery-1.10.2.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/bootstrap.js')."'></script>";

echo link_tag(array('href' => 'assets/js/jquery-ui/jquery-ui.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url("assets/js/jquery-ui/jquery-ui.js")."'></script>";

# Css para personalização
echo link_tag(array('href' => 'assets/css/personalizado.css','rel' => 'stylesheet','type' => 'text/css')); 

?>
<?php
echo "<script type='text/javascript' src='".base_url('assets/js/fullscreen/jquery.fullscreen-min.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/jQuery.print.js')."'></script>";
?>
 
</head><!--/head-->
<body>
    <div class="col-md-12">
        <div class="row">
            <div id="alerta" class="col-md-12 alert alert-success">   
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"> 
                <a href="<?php echo base_url('home/inicio');?>">Voltar</a>
            </div>
            <div id="titulo" class="col-md-4 text-center">
                <strong> Monitoramento node </strong>                                            
            </div>
            <div id="div_fullscreen" class="col-md-4">
                <!--<button class="btn btn-success pull-right" id="fullscreen" onclick="$(document).toggleFullScreen();"><strong>Tela Cheia (Tecla F11)</strong></button>-->
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12"> 
            <?php 
            $colunas = array();
            $contCell = 0; 
            foreach($campos as $nome => $display){
                
                $colunas[] = $display;
                
            }
            
            $this->table->set_heading($colunas);
            
            $cor = '#00008B'; # Azul
            foreach($dados as $da){
                
                if($da->dataStatus == 'Expirou'){
                    $cor = '#FF8C00'; # Alaranjado
                }/*elseif($da->dataStatus == 'Futuramente'){
                    $cor = '#000000'; # Preto
                }elseif($da->status == 'Inativo'){
                    $cor = '#FF0000'; # Vermelha
                }*/
                
                $cell[$contCell++] = array('data' => $da->percod.' - '.utf8_decode($this->dadosBanco->unidade($da->percod)[0]->nome), 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->nodeNro, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->nodeDsc, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $this->util->formataData(substr($da->dataInicio, 0, 10),'BR').' '.substr($da->dataInicio, 11, 10), 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $this->util->formataData(substr($da->dataFim, 0, 10),'BR').' '.substr($da->dataFim, 11, 10), 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->origem, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->status, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                if(trim($da->observacao) != ''){
                    #$obs = '<a href="#" class="glyphicon glyphicon-info-sign" title="'.trim($da->observacao).'"></a>';  
                    $obs = '<span class="glyphicon glyphicon-info-sign" aria-hidden="true" title="'.trim(htmlentities($da->observacao)).'"></span>';
                                
                }else{
                    $obs = '';
                }                
                $cell[$contCell++] = array('data' => $obs, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                    
                $this->table->add_row($cell);
                $contCell = 0; 
                
            }
            
        	$template = array('table_open' => '<table class="table zebra">');
        	$this->table->set_template($template);
        	echo $this->table->generate();
            ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"> 
            <span style="color: #00008B;" class="glyphicon glyphicon-asterisk" aria-hidden="true"></span> Ativo<br />
            </div>
            <div class="col-md-2"> 
            <span style="color: #FF8C00;" class="glyphicon glyphicon-asterisk" aria-hidden="true"></span> Expirado<br />
            </div>
        </div>
    </div>
		
<script>   
    
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$(document).ready(function() {
   
   $('#ocultaBotao').css('display', 'none');
   
   $("#fullscreen").css('visibility', 'hidden');
   
   $( "#div_fullscreen" ).mouseover(function() { // Executa quando o mouse esta encima

      $("#fullscreen").css('visibility', 'visible');
   });
   
   $( "#div_fullscreen" ).mouseout(function() { // Executa quando o mouse sai decima
   
      $("#fullscreen").css('visibility', 'hidden');
      
   });
   
    $('#alerta').css('display','none');
    
    $('#nome').keyup(function(){
        
        if($(this).val() != "" && $("#sobrenome").val() != ""){
            $('#ocultaBotao').css('display', 'block');
        }else{
            $('#ocultaBotao').css('display', 'none');
        }
        
    });
    
    $('#sobrenome').keyup(function(){
        
        if($(this).val() != "" && $("#nome").val() != ""){
            $('#ocultaBotao').css('display', 'block');
        }else{
            $('#ocultaBotao').css('display', 'none');
        }
        
    });
    
    $('#registrar').click(function(){
        
        $.ajax({
          type: "POST",
          url: '<?php echo base_url(); ?>ajax/geraSenha',
          data: {
            local: $("#cd_local").val(),
            timezone: $("#timezone").val(),
            nome: $("#nome").val(),
            sobrenome: $("#sobrenome").val()
          },
          dataType: "json",
          error: function(res) {
            $("#alerta").html('<span>Erro de execução</span>');
          },
          success: function(res) {
            
            if(res['senha'] == false){
                $('#alerta').text('Erro ao registrar!');
            }else{
                $('#alerta').text('Registrado! Aguarde chamar pelo nome');
                $("#nome").val('');
                $("#sobrenome").val('');
                $('#ocultaBotao').css('display', 'none');
            }
            
            $('#alerta').css('display','block');
          }
          
        });
        
        setTimeout(function() {
       	    $('#alerta').css('display','none');
        }, 4000);
        
    });
   
});

</script>    
</body>
</html>