<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Pesquisar conte&uacute;do no retorno</li>
                </ol>
                <div id="divMain">
                    <?php   
                                    
                    echo $this->session->flashdata('statusOperacao');
                    echo '<a class="descritivo_manual" target="_BLANK" href="'.base_url('manual/manual_extracao_retorno.pdf').'">Descri&ccedil;&atilde;o dos campos extra&iacute;dos</a>';
                    echo '<div class="well">';
                    $data = array('class'=>'pure-form','id'=>'psqConteudoRetorno');
                	echo form_open('cobrancaFaturamento/pesquisaConteudoRetorno',$data);
                        $attributes = array('id' => 'address_info', 'class' => 'address_info');
         
                		echo form_fieldset("Pesquisar conte&uacute;do no retorno", $attributes);
                		
                            echo '<div class="alert alert-info"><strong>Observa&ccedil;&atilde;o:</strong> Preencha um dos dois campos, caso contr&aacute;rio a pesquisa n&atilde;o ser&aacute; realizada.</div>';
                        
                            echo form_label('N&uacute;mero do t&iacute;tulo<span class="obrigatorio"></span>', 'numero_titulo');
                			$data = array('name'=>'numero_titulo', 'value'=>$numero_titulo,'id'=>'numero_titulo', 'placeholder'=>'Digite o n&uacute;mero do t&iacute;tulo', 'class'=>'form-control');
                			echo form_input($data);
                            
                            echo form_label('Nosso n&uacute;mero<span class="obrigatorio"></span>', 'nosso_numero');
                			$data = array('name'=>'nosso_numero', 'value'=>$nosso_numero,'id'=>'nosso_numero', 'placeholder'=>'Digite nosso n&uacute;mero', 'class'=>'form-control');
                			echo form_input($data);
                            
                            echo form_label('Nosso n&uacute;mero corresp<span class="obrigatorio"></span>', 'nosso_numero_corresp');
                			$data = array('name'=>'nosso_numero_corresp', 'value'=>$nosso_numero_corresp,'id'=>'nosso_numero_corresp', 'placeholder'=>'Digite nosso n&uacute;mero corresp', 'class'=>'form-control');
                			echo form_input($data);
                   
                            echo form_hidden('pesquisar','sim');
                            
                            echo '<div class="actions">';
                            echo form_submit("btn_cadastro",'Pesquisar', 'class="btn btn-primary pull-right"');
                            echo '</div>';
                                                        
                		echo form_fieldset_close();
                	echo form_close(); 
                    echo '</div>';                                          
                    ?>     
                           <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>        
                    <div class="tabela-div">
                    <?php
                    if($conteudoArquivo <> ''){
                        
                            foreach($conteudoArquivo as $cA){
                                
                    ?>                                
                                
                        <div class="col-lg-4 col-md-4">
                            <strong>Nome do arquivo:</strong> <?php echo $cA->nome_arquivo; ?><br>
                            <strong>Banco:</strong> <?php echo htmlentities($cA->nome_banco); ?><br>
                            <strong>Data Lote:</strong> <?php echo $cA->data_lote; ?><br>
                            <strong>Ag&ecirc;ncia:</strong> <?php echo $cA->agencia; ?><br>
                            <strong>Conta:</strong> <?php echo $cA->conta; ?><br>
                            <strong>Nosso n&uacute;mero:</strong> <?php echo $cA->nosso_numero; ?><br>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <strong>Permissor:</strong> <?php echo $cA->permissor; ?><br>
                            <strong>T&iacute;tulo:</strong> <?php echo $cA->boleto; ?><br>
                            <strong>Data vencimento:</strong> <?php echo $cA->data_vencimento; ?><br>
                            <strong>Valor t&iacute;tulo:</strong> <?php echo $cA->valor_titulo; ?><br>
                            <strong>Valor pago:</strong> <?php echo $cA->valor_pago; ?><br>
                            <strong>N&uacute;mero corresp:</strong> <?php echo $cA->numero_corresp; ?><br>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <strong>Data ocorr&ecirc;ncia:</strong> <?php echo $cA->data_ocorrencia; ?><br>
                            <strong>C&oacute;digo ocorr&ecirc;ncia:</strong> <?php echo $cA->codigo_ocorrencia; ?>&nbsp;(<?php echo htmlentities($cA->nome_ocorrencia)?>)<br>
                            <strong>C&oacute;digo inscri&ccedil;&atilde;o:</strong> <?php echo $cA->codigo_inscricao; ?><br>
                            <strong>N&uacute;mero inscri&ccedil;&atilde;o:</strong> <?php echo $cA->numero_inscricao; ?><br>
                            <strong>C&oacute;digo banco pagamento:</strong> <?php echo $cA->codigo_banco; ?><br>
                            <strong>N&uacute;mero linha:</strong> <?php echo $cA->numero_linha; ?>
                        </div>        
                        <div class="col-lg-12 col-md-12">
                            <strong>Linha:</strong> <br><?php echo $cA->linha_arquivo_retorno; ?>
                            <br /><br /><br /><br />
                        </div>
                                
                    <?php            
                            } // Fecha Foreach

                    } // Fecha IF
                    ?>
                    </div>
                </div>
            </div>

    
<script type="text/javascript">

$(document).ready(function() {
    
    $('#numero_titulo').mask('00000###');
    $('#nosso_numero').mask('00000000000');
    //$('#nosso_numero_corresp').mask('00000000000000000');
   
   // Valida o formulário
	/*$("#psqConteudoRetorno").validate({
       
		debug: false,
		rules: {
			numero_titulo: {
                required: true
		},
		messages: {
			numero_titulo: {
                required: "Digite o número do título."
            }
	   },
        submitHandler: function(form) {
            $('#aguarde').css({display:"block"});
            form.submit();
        }       
   });*/
   
});

</script>