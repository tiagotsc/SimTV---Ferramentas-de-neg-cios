<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Valida&ccedil;&atilde;o e registro de arquivos de retornos</li>
                </ol>
                <div id="divMain">
            
                    <?php
                    
                    echo $this->session->flashdata('statusOperacao');
                    if(isset($resultadoValidacao)){
                        #echo '<pre>'; print_r($resultadoValidacao);
                        echo '<h5>Foram processados '.count($resultadoValidacao).' arquivos, segue abaixo:</h5>';
                        foreach($resultadoValidacao as $resVal){
                            echo $resVal;
                        }
                    }
                    
                    if(isset($novosArquivos)){
                        #echo '<pre>'; print_r($resultadoValidacao);
                        echo '<h5>Foram gerados '.count($novosArquivos).' arquivos validados, segue abaixo:</h5>';
                        foreach($novosArquivos as $novoAr){
                            echo $novoAr; 
                        }
                    }
                    echo '<a class="descritivo_manual" target="_BLANK" href="'.base_url('manual/manual_extracao_retorno.pdf').'">Descri&ccedil;&atilde;o dos campos extra&iacute;dos</a>';
                    echo '<div class="well">';                        
                    $data = array('class'=>'pure-form','id'=>'val_retorno');
                	echo form_open('cobrancaFaturamento/execValidaArquivoRetorno',$data);
                        $attributes = array('id' => 'address_info', 'class' => 'address_info');
         
                		echo form_fieldset("Valida&ccedil;&atilde;o e registro de arquivos de retornos", $attributes);

                            echo '<div class="well">
                                    <h4>Instru&ccedil;&otilde;es:</h4>
                                    <blockquote>
                                    <p id="pInstrucoes">
                                    <strong>1&ordf; -</strong> Coloque os arquivos no diret&oacute;rio da rede:<br>
                                    <span>&nbsp&nbsp&nbsp\Sistemas\faturamento\<strong>retorno_original</strong></span><br>
                                    <strong>2&ordf; -</strong> Ap&oacute;s colocar os arquivos clique no bot&atilde;o "<strong>Validar arquivos</strong>".<br>
                                    <strong>3&ordf; -</strong> Pegue os arquivos validados no diret&oacute;rio:<br></strong>
                                    <span>&nbsp&nbsp&nbsp\Sistemas\faturamento\<strong>retorno_resultado</strong></span><br><br>
                                    <strong>Observa&ccedil;&atilde;o:</strong><br>
                                    <span>Se voc&ecirc; n&atilde;o tiver acesso as pastas citadas acima, pe&ccedil;a ao suporte da TI para mapear.</span>
                                    </blockquote>
                                    </p>
                                </div>';
                            echo '<div class="actions">';
                			echo form_submit("btn_cadastro","Validar arquivos", 'class="btn btn-primary pull-right"');
                            echo '</div>';
                		echo form_fieldset_close();
                	echo form_close();
                    echo '</div>';                       
                    ?>     
                    <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>
                    <div class="well">
                    
                        <h4>Painel arquivos validados no dia:</h4>
                        <label>Informe a data:
                        <input type="text" id="dataPainel" name="dataPainel" class="form-control" value="<?php echo date('d/m/Y');?>" />
                        </label>
                        <div id="resQtdArquivosDia">
                        <?php
                        if($painelDiaArquivo){
                        
                            $this->table->set_heading('Arquivo banco', 'quatidade');
                            
                            $total = 0;
                            foreach($painelDiaArquivo as $pD){
                            
                                $cell1 = array('data' => htmlentities($pD->nome_banco));
                                $cell2 = array('data' => $pD->qtd_arquivos);
                                $this->table->add_row($cell1, $cell2); 
                                
                                $total += $pD->qtd_arquivos;  
                            
                            }  
                            
                            $cell1 = array('data' => '<strong>TOTAL:</strong>');
                            $cell2 = array('data' => '<strong>'.$total.'</strong>');
                            $this->table->add_row($cell1, $cell2);    
                                     
                            $template = array('table_open' => '<table class="table zebra">');
                            $this->table->set_template($template);
                            echo $this->table->generate();    
                        }else{
                            echo '<div class="alert alert-warning" role="alert"><strong>Nenhum arquivo foi validado nesse dia.</strong></div>';
                        }                
                        ?>
                        </div>
                        
                        <h4>Pesquisar arquivo:</h4>
                        <?php

                        echo form_open('cobrancaFaturamento/iniPesquisarArquivoRetorno');

                        $options = array('' => '');		
                		foreach($banco as $ba){
                			$options[$ba->cd_banco] = htmlentities($ba->nome_banco);
                		}
                		
                		echo form_label('Banco:', 'banco_arquivo');
                		echo form_dropdown('banco_arquivo', $options, $postBanco, 'id="banco_arquivo" class="form-control"');
                        /*
                        $options = array('' => '');		
                		foreach($dataInsercao as $dI){
                			$options[$dI->data_banco_insercao] = html_entity_decode($dI->data_formatada);
                		}
                		
                		echo form_label('Data da inserção (lote):', 'data_lote');
                		echo form_dropdown('data_lote', $options, $postDataLote, 'id="data_lote" class="form-control"');
                        */
                        echo form_label('Informe a data do lote:', 'data_lote');
                		$data = array('id'=>'data_lote', 'name'=>'data_lote', 'value'=>$postDataLote, 'class'=>'form-control data');
                		echo form_input($data,'');
                        
                        echo '<div class="actions">';
                        echo form_submit("btn_cadastro","Pesquisar", 'class="btn btn-primary pull-right"');     
                        echo '</div>'; 
                                   
                        echo form_close();

                        ?>
                        <div class="row"></div>
                     </div>  
                     <div class="well">
                        <?php

                        if($pesquisa == 'sim'){
                        
                            $this->table->set_heading('Nome do Arquivo', 'Banco', 'Data/Hora lote','Data do arquivo', 'A&ccedil;&atilde;o');
                            
                            foreach($arquivosPesquisa as $arq){
                                
                                $cell1 = array('data' => $arq->nome_arquivo_retorno);
                                $cell2 = array('data' => htmlentities($arq->nome_banco));
                                $cell3 = array('data' => $arq->data_insercao_arquivo_retorno);
                                $cell4 = array('data' => $arq->data_arquivo_retorno);
                                
                                if($arq->excluido <> 0){
                                    $linkLinhasExcluidas = '<a id="linhaExcluida" target="_blank" href="'.base_url('cobrancaFaturamento/linhasExcluidasRetorno/'.$arq->cd_arquivo_retorno).'" title="Ver linhas excluídas" class="glyphicon glyphicon-zoom-in"></a>';
                                }else{
                                    $linkLinhasExcluidas = '';
                                }
                                
                                $linkGerarArquivo = '<a title="Gerar Arquivo" href="'.base_url('cobrancaFaturamento/gerarArquivoRetorno/'.$arq->cd_arquivo_retorno).'" class="actions glyphicon glyphicon-download"></a>';
                                
                                $cell5 = array('data' => $linkLinhasExcluidas.$linkGerarArquivo);
                                    
                                $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5);
                            }
                            
                        	$template = array('table_open' => '<table class="table zebra">');
                        	$this->table->set_template($template);
                        	echo $this->table->generate();
                            echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>";
                        }
                        ?>

                     </div>          
                </div>
            </div>

    
<script type="text/javascript">
$(document).ready(function() {
    
    $("#data_lote, #dataPainel").mask("00/00/0000");
    
   $(".actions").click(function() {
      $('#aguarde').css({display:"block"});
   });
   
   $("#dataPainel").change(function(){
	  
		//Verificar se o campo o nome nao e vazio
		if($(this).val() != ""){
		  
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>cobrancaFaturamento/pegaQtdArquivosDiarios',
              data: {
                dataPainel: $(this).val(),
                banco: $("#banco_arquivo").val()
              },
              dataType: "json",
              error: function(res) {
 	            $("#resQtdArquivosDia").html('<span>Erro de execução</span>');
              },
              success: function(res) {
                
                var conteudoHtml = '';
                if(res.length > 0){
                
                    conteudoHtml += '<table class="table zebra">';
                    conteudoHtml += '<thead>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<th>Arquivo banco</th>';
                    conteudoHtml += '<th>Quantidade</th>';
                    conteudoHtml += '</tr>';
                    conteudoHtml += '</thead>';
                    conteudoHtml += '<tbody>';
                    
                    var total = 0;
                    $.each(res, function() {
                      
                      conteudoHtml += '<tr>';
                      conteudoHtml += '<td>'+this.nome_banco+'</td>';
                      conteudoHtml += '<td>'+this.qtd_arquivos+'</td>';
                      conteudoHtml += '</tr>';
                      
                      total += Number(this.qtd_arquivos);
                      
                    });
    
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<td><strong>TOTAL:</strong></td>';
                    conteudoHtml += '<td><strong>'+total+'</strong></td>';
                    conteudoHtml += '</tr>';
                      
                    conteudoHtml += '</tbody>';
                
                }else{
                    conteudoHtml += '<div class="alert alert-warning" role="alert"><strong>Nenhum arquivo foi validado nesse dia.</strong></div>';
                }
                
                $("#resQtdArquivosDia").html(conteudoHtml);
                
              }
            });  
                    
		}else{
		  
            $("#resQtdArquivosDia").html('');
          
		}      
   });   
      
});

/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data_lote, #dataPainel").datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Pr&oacute;ximo',
	prevText: 'Anterior',
    
    // Traz o calendário input datepicker para frente da modal
    beforeShow :  function ()  { 
        setTimeout ( function (){ 
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
        },  0 ); 
    } 
});
</script>