<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Arquivos duplicados</li>
                </ol>
                <div id="divMain">
                            <?php echo $this->session->flashdata('statusOperacao'); ?>       
                    <div class="tabela-div">
                        
                        <?php
                    	$this->table->set_heading('Possui duplica&ccedil;&atilde;o', 'Banco', 'Data do lote', 'Qtd. duplicados', 'Qtd. para remo&ccedil;&atilde;o');
                        
                        if($duplicados){
                            
                            echo '<p><strong>A tabela abaixo lista os arquivos que possuem duplica&ccedil;&otilde;es.</strong></p>';
                        
                            $totalDuplicados = 0;
                            $totalRemocao = 0;
                            foreach($duplicados as $resg){
                                
                                $cell1 = array('data' => $resg->nome_arquivo_retorno);
                                $cell2 = array('data' => html_entity_decode($resg->nome_banco));
                                $cell3 = array('data' => $resg->data_lote);
                                $cell4 = array('data' => $resg->qtd_arquvos);
                                $cell5 = array('data' => $resg->precisa_remocao);
                                
                                $totalDuplicados += $resg->qtd_arquvos;
                                $totalRemocao += $resg->precisa_remocao;
                                    
                                $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5);
                            }
                            
                            $this->table->add_row('TOTAL', '#', '#', $totalDuplicados, $totalRemocao);
                            
                        	$template = array('table_open' => '<table class="table table-bordered">');
                        	$this->table->set_template($template);
                        	echo $this->table->generate();
                            
                            echo "<div id='aguarde' style='text-align: center; display: none'><img src='".base_url('assets/img/aguarde.gif')."' /></div>";
                            
                            $data = array('class'=>'pure-form','id'=>'removerDuplicacao');
                        	echo form_open('cobrancaFaturamento/exeRemocaoArquivos',$data);
                                echo '<p><strong>Existem atualmente '.$totalRemocao.' arquivos que precisam ser removidos. Clique no bot&atilde;o abaixo para remov&ecirc;-los.</strong></p>';
                                echo '<div class="actions">';
                                
                                $data = array(
                                    'name'        => 'btn_remove',
                                    'id'          => 'btn_remove',
                                    'value'       => 'Remover duplicados',
                                    'class'     => 'btn btn-primary pull-right'
                                );
                                
                                echo form_submit($data);
                                echo '</div>';
                                                                
                        	echo form_close();  
                        
                        }else{
                            
                            echo '<p><strong>N&atilde;o existe arquivos duplicados.</strong></p>';
                            
                        }
                    	?> 
                    </div>
                </div>
            </div>
    
<script type="text/javascript">

$(document).ready(function() {
   
   $("#btn_remove").click(function(){
    
        $('#aguarde').css({display:"block"});
    
   });
   
   
});

</script>