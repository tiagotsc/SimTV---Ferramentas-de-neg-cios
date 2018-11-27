<?php
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
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
                        echo form_open($pasta.'/'.$controller.'/deleta'.ucfirst($controller),$data);
                        
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
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telecom'); ?>"><?php echo ucfirst($pasta); ?></a></li>
                    <li class="active"><?php echo $titulo; ?></li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        $controle = (isset($controle))? $controle: false;
                        $idNode = (isset($idNode))? $idNode: false;
                        $contrato = (isset($contrato))? $contrato: false;
                        $endereco = (isset($endereco))? $endereco: false;
                        $cd_unidade = (isset($cd_unidade))? $cd_unidade: false;
                        $concluido = (isset($concluido))? $concluido: false;
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisar');
                    	echo form_open($pasta.'/'.$controller.'/'.$metodo,$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? "<a href='".base_url($pasta.'/'.$controller.'/ficha'.ucfirst($controller))."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>": '';
                            
                    		echo form_fieldset($titulo.$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Controle', 'controle');
                        			$data = array('name'=>'controle', 'value'=>$controle,'id'=>'controle', 'placeholder'=>'Digite o controle', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    $options = array('' => '');		
                            		foreach($nodes as $nod){                                
                    			       $options[$nod->id] = $nod->permissor.' ('.htmlentities($nod->nome).') - '.htmlentities($nod->descricao);                                    
                            		}	
                            		echo form_label('Node', 'idNode');
                            		echo form_dropdown('idNode', $options, $idNode, 'id="idNode" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-4">';
                                    echo form_label('Contrato', 'contrato');
                        			$data = array('name'=>'contrato', 'value'=>$contrato,'id'=>'contrato', 'placeholder'=>'Digite o contrato', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-5">';
                                    echo form_label('Endere&ccedil;o', 'endereco');
                        			$data = array('name'=>'endereco', 'value'=>$endereco,'id'=>'endereco', 'placeholder'=>'Digite o endere&ccedil;o', 'class'=>'form-control');
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
                                    
                                    echo '<div class="col-md-3">';
                                    $options = array(''=>'', 'sim' => 'Sim', 'nao' => 'N&atilde;o');		
                            		echo form_label('Conclu&iacute;do', 'concluido');
                            		echo form_dropdown('concluido', $options, $concluido, 'id="concluido" class="form-control"');
                                    echo '</div>';
                                       
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn",'Listar', 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>    
                </div>
                
                <div class="row">&nbsp</div>
                <div class="well">
                    <div class="row">
                        <div class="col-md-2"> 
                        <span class="ok glyphicon glyphicon-asterisk" aria-hidden="true"></span> Conclu&iacute;do<br />
                        </div>
                        <div class="col-md-2"> 
                        <span class="pendente glyphicon glyphicon-asterisk" aria-hidden="true"></span> Pendente<br />
                        </div>
                        <div class="col-md-2"> 
                        <span class="atrasado glyphicon glyphicon-asterisk" aria-hidden="true"></span> Atrasado<br />
                        </div>
                    </div>
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
                        
                        $colunas[] = anchor($pasta.'/'.$controller.'/'.$metodo.'/'.$post.'/'.$nome.'/'.(($sort_order == 'asc' && $sort_by == $nome) ? 'desc' : 'asc') ,$nome.$icoAscDesc, array('class' => $class));
                        
                    }
                    
                }
                
                $colunas[] = 'A&ccedil;&atilde;o';
                
                $this->table->set_heading($colunas);
                
                foreach($dados as $da){
                    
                    $campo1 = strtolower($campos[1]);
                    $campo2 = strtolower($campos[2]);

                    $nome = $da->$campo1.' - '.$da->$campo2;
                    
                    foreach($campos as $campo => $valor){
                        
                        if($da->situacao_atual == 'OK'){
                            $classSituacao = 'ok';
                        }elseif($da->situacao_atual == 'PENDENTE'){
                            $classSituacao = 'pendente';
                        }else{
                            $classSituacao = 'atrasado';
                        }
                        
                        $valor = strtolower($valor);
                        if($campo != $id and $campo != 'situacao_atual'){
                            if(in_array($valor, array('inicio', 'previsao', 'conclusao'))){
                                $cell[$contCell++] = array('data' => $this->util->formataData($da->$valor, 'BR'), 'class' => $classSituacao );
                            }else{
                                $cell[$contCell++] = array('data' => ucfirst(htmlentities($da->$valor)), 'class' => $classSituacao );
                            }
                        }
                    
                    }
                    
                    $botaoEnviarEmail = (in_array($perReenvioVist, $this->session->userdata('permissoes')) and $da->concluido != 'Sim')? '<a class="reenvio glyphicon glyphicon-envelope" ordem="'.$this->util->base64url_encode($da->$pk).'" aria-hidden="true" title="Reenviar ordem por e-mail"></a>': '';
                    $botaoImprimir = (in_array($perImprimir, $this->session->userdata('permissoes')))? '<a title="Imprimir" target="_blank" href="'.base_url($pasta.'/'.$controller.'/imprimirOrdem/'.$this->util->base64url_encode($da->$pk)).'" class="glyphicon glyphicon-print"></a>': '';
                    $botaoEditar = (in_array($perEditarCadastrar, $this->session->userdata('permissoes')))? '<a title="Editar" href="'.base_url($pasta.'/'.$controller.'/ficha'.ucfirst($controller).'/'.$da->$pk).'" class="glyphicon glyphicon-pencil"></a>': '';
                    $botaoExcluir = (in_array($perExcluir, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->$pk.'" nome="'.$nome.'" class="del glyphicon glyphicon-remove"></a>': '';
                    $cell[$contCell++] = array('data' => $botaoEnviarEmail.$botaoImprimir.$botaoEditar.$botaoExcluir);
                        
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

$.fn.carregando = function() {
    $(document).ajaxStart(
        $.blockUI({ 
        message:  '<h1>Reenviando vistoria por e-mail...</h1>',
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

$(".reenvio").click(function(){
    var r = confirm("Deseja reenviar essa vistoria por e-mail?");
    if (r == true) {
        $(this).carregando();
        url = "<?php echo base_url($pasta.'/'.$controller.'/reEnviarOrdemEmail');?>/"+$(this).attr('ordem');
        $(location).attr("href", url);
    }
});

$(".del").click(function(){
   $("#apg_id").val($(this).attr('key'));
   $("#apg_nome").val($(this).attr('nome'));
});

function apagarRegistro(id, nome){
    $("#apg_id").val(id);
    $("#apg_nome").val(nome);
}

</script>