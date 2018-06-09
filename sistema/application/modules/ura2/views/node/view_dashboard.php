<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Sim TV - Painel de Registro</title>
  
<?php
#header("Refresh:5");
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
<style>

.corPadrao{
    color:#00008B;
    /*font-weight: bold;*/
}

.corAlternativa{
    color: #FF8C00;
    /*font-weight: bold;*/
}

</style>
 
</head><!--/head-->
<body>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4"> 
                <a href="<?php echo base_url('home/inicio');?>">Voltar</a>
            </div>
            <div id="titulo" class="col-md-4 text-center">
                <strong> Monitoramento node </strong>                                            
            </div>
            <div id="div_fullscreen" class="col-md-4">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12"> 
            	<table class="table zebra">
            		<thead>
            			<tr>
            				<th>Permissor</th>
            				<th>N&uacute;mero</th>
            				<th>Descri&ccedil;&atilde;o</th>
            				<th>Data In&iacute;cio</th>
            				<th>Data Fim</th>
            				<th>Origem</th>
            				<th>Status</th>
            				<th>Obs</th>
            			</tr>
            		</thead>
            		<tbody id="add-dados">
            			<!--<tr>
            				<td class='fontNode' style='color:#FF8C00'>91 - Cuiab?</td>
            				<td class='fontNode' style='color:#FF8C00'>845</td>
            				<td class='fontNode' style='color:#FF8C00'>CI845</td>
            				<td class='fontNode' style='color:#FF8C00'>07/06/2016 04:29:42</td>
            				<td class='fontNode' style='color:#FF8C00'>08/06/2016 19:00:00</td>
            				<td class='fontNode' style='color:#FF8C00'>TIAGO SILVA COSTA</td>
            				<td class='fontNode' style='color:#FF8C00'>Ativo</td>
            				<td class='fontNode' style='color:#FF8C00'>
            					<span class="glyphicon glyphicon-info-sign" aria-hidden="true" title="A controv&eacute;rsia a respeito da aglomera&ccedil;&atilde;o de pessoas no evento esportivo cresceu &agrave; medida que a doen&ccedil;a passou a ser mais conhecida. O v&iacute;rus transmitido pelo mosquito Aedes Aegypti causa malforma&ccedil;&atilde;o cerebral em beb&ecirc;s e foi ligado &agrave; doen&ccedil;a neurol&oacute;gica conhecida como s&iacute;ndrome de Guillain-Barr&eacute; em adultos">
            					</span>
            				</td>
            			</tr>-->
            		</tbody>
            	</table>            
            <?php 
            /*$colunas = array();
            $contCell = 0; 
            foreach($campos as $nome => $display){
                
                $colunas[] = $display;
                
            }
            
            $this->table->set_heading($colunas);
            
            $cor = '#00008B'; # Azul
            foreach($dados as $da){
                
                if($da->dataStatus == 'Expirou'){
                    $cor = '#FF8C00'; # Alaranjado
                }
                
                $cell[$contCell++] = array('data' => $da->percod.' - '.utf8_decode($this->dadosBanco->unidade($da->percod)[0]->nome), 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->nodeNro, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->nodeDsc, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $this->util->formataData(substr($da->dataInicio, 0, 10),'BR').' '.substr($da->dataInicio, 11, 10), 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $this->util->formataData(substr($da->dataFim, 0, 10),'BR').' '.substr($da->dataFim, 11, 10), 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->origem, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                $cell[$contCell++] = array('data' => $da->status, 'style' => 'color:'.$cor, 'class' => 'fontNode');
                if(trim($da->observacao) != ''){
    
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
        	echo $this->table->generate();*/
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

$( document ).ready(function() {
    $(this).carregaDados();
});

self.setInterval(function(){$(this).carregaDados()}, 5000); // Chama a função a cada 5 segundos


$.fn.carregaDados = function(){ 

    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>ura/ajaxUra/dadosDashboard',
      dataType: "json",
      error: function(res) {
        //alert('Erro');
      },
      success: function(res) {
        
        var conteudo = '';
        
        if(res.length > 0){
            
            $.each(res, function() {
                
                var classe = 'class="corPadrao"';
                if(this.dataStatus == 'Expirou'){
                    classe = 'class="corAlternativa"';
                }
                conteudo += '<tr>';
                conteudo += '<td '+classe+'>'+this.permissor+'</td>';
                conteudo += '<td '+classe+'>'+this.nodeNro+'</td>';
                conteudo += '<td '+classe+'>'+this.nodeDsc+'</td>';
                conteudo += '<td '+classe+'>'+this.dataInicio+'</td>';
                conteudo += '<td '+classe+'>'+this.dataFim+'</td>';
                conteudo += '<td '+classe+'>'+this.origem+'</td>';
                conteudo += '<td '+classe+'>'+this.status+'</td>';
                
                var obs = '';
                if(this.observacao != ''){
                    obs = '<span class="glyphicon glyphicon-info-sign" aria-hidden="true" title="'+this.observacao+' - '+this.dtObs+'"></span>';
                }
                
                conteudo += '<td '+classe+'>'+obs+'</td>';
                conteudo += '</tr>';
            
            });

        }else{
            conteudo += '';
        }
        
        $("#add-dados").html(conteudo);
        
      }
    });

}

</script>    
</body>
</html>