<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar <?php echo $controller; ?>?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open($this->session->userdata('indexPHP').$pasta.'/'.$controller.'/deleta'.ucfirst($controller),$data);
                        
                            echo form_label('Nome', 'apg_nome');
                    		$data = array('id'=>'apg_nome', 'name'=>'apg_nome', 'readonly'=>'readonly', 'class'=>'form-control');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_id" name="apg_id" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga registro de telecom -->
    
            <div id="corpo" class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url($this->session->userdata('indexPHP').'home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url($this->session->userdata('indexPHP').'telecom'); ?>"><?php echo ucfirst($pasta); ?></a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $node = (isset($node))? $node: false;
                        $cd_unidade = (isset($cd_unidade))? $cd_unidade: false;
                        $bairro = (isset($bairro))? $bairro: false;
                        $pop = (isset($pop))? $pop: false;
                        $cm = (isset($cm))? $cm: false;
                        $tv = (isset($tv))? $tv: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open($this->session->userdata('indexPHP').$pasta.'/'.$controller.'/'.$metodo,$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? "<a href='".base_url($pasta.'/'.$controller.'/ficha'.ucfirst($controller))."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset($titulo.$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-4">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Node', 'node');
                        			$data = array('name'=>'node', 'value'=>$node,'id'=>'node', 'placeholder'=>'Digite node', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '');		
                            		foreach($permissor as $per){
                      		            
                                        if($per->permissor != ''){                                        
                          			       $options[$per->cd_unidade] = $per->permissor.' - '.htmlentities($per->nome);
                                        }                                      
                            		}	
                            		echo form_label('Permissor', 'cd_unidade');
                            		echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '');		
                            		foreach($bairros as $bar){
                      		            
                                        if($bar->bairro != ''){                                        
                          			       $options[$bar->bairro] = htmlentities($bar->bairro);
                                        }                                      
                            		}	
                            		echo form_label('Bairro', 'bairro');
                            		echo form_dropdown('bairro', $options, $bairro, 'id="bairro" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'hub' => 'Hub', 'headend' => 'Headend');		
                            		echo form_label('Pop', 'pop');
                            		echo form_dropdown('pop', $options, $pop, 'id="pop" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'sim' => 'Sim', 'nao' => 'N&atilde;o', 'parcial' => 'Parcial');		
                            		echo form_label('Cm', 'cm');
                            		echo form_dropdown('cm', $options, $cm, 'id="cm" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'sim' => 'Sim', 'nao' => 'N&atilde;o');		
                            		echo form_label('Tv', 'tv');
                            		echo form_dropdown('tv', $options, $tv, 'id="tv" class="form-control"');
                                    echo '</div>';
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn",utf8_encode($titulo), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>    
                </div>
                
                <div class="row">&nbsp</div>
                <div class="well">
                <p>
                    <strong>Mostrando <?php echo ($qtdDadosCorrente)? $qtdDadosCorrente: 0; ?> de <?php echo ($qtdRegistos)? $qtdRegistos: 0; ?> registros localizados.</strong>
                </p>
                <?php 
    
                $colunas = array();
                $contCell = 0; 
                
                foreach($campos as $nome){     
    
                    if($nome == $id){
                        $pk = $nome;
                    }
                    
                    if($nome != $id){
                    
                        if($sort_by == $nome){
                            $class = "sort_$sort_order";
                            
                            if($sort_order == 'asc'){
                                // Crescente
                                $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span>';
                            }else{
                                // Descrecente
                                $icoAscDesc = '&nbsp<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>';
                            }
                            
                        }else{
                            $class = "";
                            $icoAscDesc = '';
                        }
                        
                        $colunas[] = anchor($this->session->userdata('indexPHP').$pasta.'/'.$controller.'/'.$metodo.'/'.$post.'/'.$nome.'/'.(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$nome.$icoAscDesc, array('class' => $class));
                        
                    }
                    
                }
                
                $colunas[] = 'A&ccedil;&atilde;o';
                
                $this->table->set_heading($colunas);
                
                foreach($dados as $da){
                    
                    $campo1 = strtolower($campos[1]);
                    $campo2 = strtolower($campos[2]);

                    $nome = $da->$campo1.' - '.$da->$campo2;
                    
                    foreach($campos as $campo => $valor){
                        $valor = strtolower($valor);
                        if($campo != $id){
                            $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)) );
                        }
                    
                    }
                    
                    $botaoEditar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url($this->session->userdata('indexPHP').$pasta.'/'.$controller.'/ficha'.ucfirst($controller).'/'.$da->$pk).'" class="glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a>': '';
                    $cell[$contCell++] = array('data' => $botaoEditar.$botaoExcluir);
                        
                    $this->table->add_row($cell);
                    $contCell = 0; 
                    
                }
    
                $template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>";
                ?>
                </div>
                
            </div>
            
<script type="text/javascript">

$(".del").click(function(){
   $("#apg_id").val($(this).attr('key'));
   $("#apg_nome").val($(this).attr('nome'));
});

function apagarRegistro(id, nome){
    $("#apg_id").val(id);
    $("#apg_nome").val(nome);
}

</script>