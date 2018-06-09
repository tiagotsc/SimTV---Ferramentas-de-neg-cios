<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Sim TV - Intera&ccedil;&atilde;o de viabilidade</title>
  
<?php
# Bootstrap core CSS
echo link_tag(array('href' => 'assets/css/bootstrap.css','rel' => 'stylesheet','type' => 'text/css'));

# JavaScript
echo "<script type='text/javascript' src='".base_url('assets/js/jquery-1.10.2.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/bootstrap.js')."'></script>";

echo link_tag(array('href' => 'assets/js/jquery-ui/jquery-ui.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url("assets/js/jquery-ui/jquery-ui.js")."'></script>";

# Css para personalização
echo link_tag(array('href' => 'assets/css/personalizado.css','rel' => 'stylesheet','type' => 'text/css')); 

echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.blockui/jquery.block.ui.js') ?>"></script>
</head><!--/head-->
<body>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12"> 
                <a id="hist-viab-fecha" href="<?php echo base_url($modulo.'/'.$controller.'/listarHistorico/'.$idViabResp); ?>"><strong>VOLTAR</strong></a>
            </div>
        </div>
        
        <div id="hist-via_cabecalho" class="row">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <th>Controle</th>
                        <th>N&uacute;mero da solicita&ccedil;&atilde;o</th>
                        <th>Permissor</th>
                        <th>Tipo de solicita&ccedil;&atilde;o</th>
                    </tr>
                    <tr>
                        <td><?php echo $dadosViab->controle; ?></td>
                        <td><?php echo $dadosViab->n_solicitacao; ?></td>
                        <td><?php echo $dadosViab->permissor.' - '.htmlentities($dadosViab->unidade); ?></td>
                        <td><?php echo htmlentities($dadosViab->tipo); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php   
            
            $anexoArquivo = '';
            echo $this->session->flashdata('statusOperacao');
            $data = array('class'=>'pure-form','id'=>'frm-salvar');
        	echo form_open_multipart($modulo.'/'.$controller.'/salvar',$data);
                $attributes = array('id' => 'address_info', 'class' => 'address_info');
 
        		echo form_fieldset("Ficha intera&ccedil;&atilde;o", $attributes);
        		
                    echo '<div class="row">';

                        echo '<div class="col-md-6">';
                        $options = array('' => '');	
                        foreach($status as $st){
                            $options[$st->id] = htmlentities($st->nome);
                        }	
                		echo form_label('Status<span id="infoHab" title="Status final - Esse status finaliza o processo, ou seja, ap&oacute;s a defini&ccedil;&atilde;o desse status n&atilde;o ser&aacute; mais poss&iacute;vel realizar nenhuma intera&ccedil;&atilde;o encima desse hist&oacute;rico." class="glyphicon glyphicon-question-sign"></span>', 'idStatusHist');
                		echo form_dropdown('idStatusHist', $options, $idStatusHist, 'id="idStatusHist" class="form-control"');
                        echo '</div>';      
                        
                        echo '<div class="col-md-6">';
                            echo '<div style="margin-bottom: 10px"><strong>Arquivo</strong></div>';
                            if($anexo){
                                $anexoArquivo = $anexo;
                            }
                            echo '<a href="'.base_url($dirDownload).'/'.$anexoArquivo.'" target="_blank">'.$anexo_label.'</a>';
                        echo '</div>';
                        
                        echo '<div id="file" class="col-md-12">';
                            echo form_label('Arquivo', 'anexo');
                			$data = array('name'=>'anexo','id'=>'anexo', 'placeholder'=>'Selecione o arquivo', 'class'=>'form-control');
                			echo form_upload($data);
                        echo '</div>';
                        
                        echo '<div class="col-md-12">';
                        echo form_label('Observa&ccedil;&atilde;o', 'observacao');
                        echo '<textarea id="observacao" name="observacao" rows="12" cols="10" class="form-control">'.htmlentities($observacao).'</textarea>';
                        echo '</div>'; 
                        
                    echo '</div>';
                                                  
                    echo '<div id="hist-viab" class="actions">';
                    
                    echo form_hidden('id', $id);
                    echo form_hidden('idViabResp', $idViabResp);
                    echo form_hidden('anexoOrigem', $anexoArquivo);
                    
                    echo form_submit("btn","Salvar", 'id="btn" class="btn btn-primary pull-right"');
                    echo '</div>';   
                                                
        		echo form_fieldset_close();
        	echo form_close(); 
            
        ?> 
    </div>
		
<script>    
   
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$.fn.carregando = function() {
    $(document).ajaxStart(
        $.blockUI({ 
        message:  '<h1>Salvando e enviando e-mail...</h1>',
        css: { 
        	border: 'none', 
        	padding: '15px', 
        	backgroundColor: '#000', 
        	'-webkit-border-radius': '10px', 
        	'-moz-border-radius': '10px', 
        	opacity: .5, 
        	color: '#fff' 
        	} 
        })
    ); 
    
    //setTimeout($.unblockUI, tempo);   
    //$(document).ajaxStart($.blockUI);
    
}

  $(function() {
    $( document ).tooltip({
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
  });

$("#idStatusHist").change(function(){
   
   if($(this).val() != ''){
    $(this).verificaTipoStatus();
   }else{
    $("#infoHab").hide();
   }
    
});

$.fn.verificaTipoStatus = function() {
    $.ajax({
      type: "POST",
      url: '<?php echo base_url().$modulo; ?>/ajaxViabilidadeRespHist/tipoStatus',          
      data: {
        status: $("#idStatusHist").val()
      },
      dataType: "json",
      /*error: function(res) {
        $("#resMarcar").html('<span>Erro de execução</span>');
      },*/
      success: function(res) { 
        
        if(res == 'S'){
            $("#infoHab").show();
        }else{
            $("#infoHab").hide();
        }
        
      }
    }); 
}

$(document).ready(function() {
    
    $(this).verificaTipoStatus();
    $("#infoHab").hide();
    
    // Valida o formulário
	$("#frm-salvar").validate({
		debug: false,
		rules: {
            idStatusHist: {
                required: true
            },
			observacao: {
                required: true
            }
		},
		messages: {
            idStatusHist: {
                required: "Selecione o status."
            },
			observacao: {
                required: "Informe a observa&ccedil;&atilde;o."
            }
	   }
   });
   
   $("#btn").click(function(){
        if($( "#frm-salvar" ).valid()){
            $(this).carregando();
        }
   });

});

</script>    
</body>
</html>