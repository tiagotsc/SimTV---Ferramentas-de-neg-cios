<?php 
#header('Content-Type: text/html; charset=utf-8');
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    
    <!-- INÍCIO Modal Apagar associação -->
    <div class="modal fade" id="apagaAssociacao" tabindex="-1" role="dialog" aria-labelledby="apagaAssociacao" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar a associa&ccedil;&atilde;o?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaAssoc');
                        echo form_open('anatel/apagaAssociacao',$data);
                        
                            echo form_label('Associa&ccedil;&atilde;o', 'apg_assoc');
                    		$data = array('id'=>'desc_apg_assoc', 'name'=>'desc_apg_assoc', 'class'=>'form-control', 'readonly'=>'readonly');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="cd_anatel_frm" name="cd_anatel_frm" value="<?php echo $cd_anatel_frm; ?>" />
                    <input type="hidden" id="apg_assoc" name="apg_assoc" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apagar associação -->
    
    <!-- INÍCIO Modal associa usuário -->
    <div class="modal fade" id="associarUsuario" tabindex="-1" role="dialog" aria-labelledby="associarUsuario" aria-hidden="true">
        <div class="modal-dialog modalMedia">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Associar usu&aacute;rio(s) ao indicador</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'associaUsuario');
                        echo form_open('anatel/associaResponsavel',$data);
                        
                        echo '<div class="col-md-6">';
                            $options = array('' => '');		
                    		foreach($departamentoUser as $dep){
                    			$options[$dep->cd_departamento] = htmlentities($dep->nome_departamento);
                    		}	
                    		echo form_label('<strong>Departamento<span class="obrigatorio">*</span></strong>', 'cd_departamento_user');
                    		echo form_dropdown('cd_departamento_user', $options, '', 'id="cd_departamento_user" class="form-control"');
                            echo '<div id="usuarios" class="divScrollMedia"></div>';
                        echo '</div>';
                        
                        echo '<div class="col-md-6 divScrollMedia">';
                            echo '<strong>Marque a(s) unidade(s) que ele(s) ser&aacute;(&atilde;o) respons&aacute;vel(is):</strong><br>';
                            foreach($unidades as $uni){
                                $data = array(
                                    'name'        => 'cd_unidade[]',
                                    'id'          => 'cd_unidade[]',
                                    'value'       => $uni->cd_unidade,
                                    'checked'     => false,
                                    /*'style'       => 'margin:10px',*/
                                    );
                                echo form_checkbox($data);
                                echo htmlentities($uni->nome).'<br>';
                            }
                        echo '</div>';
                    ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="cd_anatel_frm" name="cd_anatel_frm" value="<?php echo $cd_anatel_frm; ?>" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button id="assUser" type="submit" class="btn btn-primary">Salvar</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal associa usuário -->
    
    <!-- INÍCIO Modal Apagar pergunta -->
    <div class="modal fade" id="apagaPergunta" tabindex="-1" role="dialog" aria-labelledby="apagaPergunta" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar a regra?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaQuestao');
                        echo form_open('anatel/apagaQuestao',$data);
                        
                            echo form_label('ID quest&atilde;o', 'apg_questao');
                    		$data = array('id'=>'apg_questao', 'name'=>'apg_questao', 'class'=>'form-control', 'readonly'=>'readonly');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="cd_anatel_frm" name="cd_anatel_frm" value="<?php echo $cd_anatel_frm; ?>" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apagar pergunta -->
    
    <!-- INÍCIO Modal Apagar regra meta -->
    <div class="modal fade" id="apagaRegra" tabindex="-1" role="dialog" aria-labelledby="apagaRegra" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar a regra?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaQuestao');
                        echo form_open('anatel/apagaRegraMeta',$data);
                        
                            echo form_label('ID regra meta', 'apg_regra_meta');
                    		$data = array('id'=>'apg_regra_meta', 'name'=>'apg_regra_meta', 'class'=>'form-control', 'readonly'=>'readonly');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="cd_anatel_frm" name="cd_anatel_frm" value="<?php echo $cd_anatel_frm; ?>" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apagar regra meta -->    
    
    <!-- INÍCIO Modal Adicionar regra meta -->
    <div class="modal fade" id="addRegraMeta" tabindex="-1" role="dialog" aria-labelledby="addRegraMeta" aria-hidden="true">
        <div class="modal-dialog modalGrande">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Configura&ccedil;&atilde;o da regra de meta (Justificativa)</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'regraMeta');
                        echo form_open('anatel/salvarRegraMeta',$data);
                        
                        echo '<div class="col-md-12">';
                        $options = array(
                                            ''=>'', 
                                            'P' => 'Porcentagem ( ((Pergunta A / Pergunta B) x 100) = % Meta)', 
                                            'N' => 'Num&eacute;rica (Pergunta A = N&ordf; Meta)', 
                                            'F' => 'F&oacute;rmula (((Pergunta A - Pergunta B) x 100) / Pergunta A) / 100 = % Meta'
                                    );		
                		echo form_label('Tipo de regra', 'tipo_regra');
                		echo form_dropdown('tipo_regra', $options, $postStatus, 'id="tipo_regra" class="form-control"');
                        echo '</div>';
                        echo '<div id="camposPorcentagem">';
                            echo '<div class="col-md-1 operador text-center">';
                            echo '<br>((';
                            echo '</div>';
                            echo '<div class="col-md-2">';
                            if($perguntas){
                                
                                $options = array(''=>'');
                                foreach($perguntas as $perg){
                                    $options[$perg->cd_anatel_quest] = 'ID '.$perg->cd_anatel_quest;
                                }		
                        		echo form_label('Pergunta 1', 'cd_pergunta1_porc');
                        		echo form_dropdown('cd_pergunta1_porc', $options, '', 'id="cd_pergunta1_porc" class="form-control"');
                                
                            }
                            echo '</div>';
                            
                            echo '<div class="col-md-2">';
                            echo form_label('&nbsp', 'operador');
                			$data = array('name'=>'operador', 'value'=>'/','id'=>'operador', 'class'=>'form-control operador', 'readonly'=>'readonly');
                			echo form_input($data);
                            echo '</div>';
                            
                            echo '<div class="col-md-2">';
                            if($perguntas){
                                
                                $options = array(''=>'');
                                foreach($perguntas as $perg){
                                    $options[$perg->cd_anatel_quest] = 'ID '.$perg->cd_anatel_quest;
                                }		
                        		echo form_label('Pergunta 2', 'cd_pergunta2_porc');
                        		echo form_dropdown('cd_pergunta2_porc', $options, '', 'id="cd_pergunta2_porc" class="form-control"');
                                
                            }
                            echo '</div>';
                            echo '<div class="col-md-1 operador text-center">';
                            echo '<br>)x100)';
                            echo '</div>';
                            
                            echo '<div class="col-md-2">';
                            $options = array(''=>'', '=' => '=', '<=' => '<=', '>=' => '>=');		
                    		echo form_label('Comparar', 'comparador');
                    		echo form_dropdown('comparador', $options, '', 'id="comparador" class="form-control"');
                            echo '</div>';
                            
                            echo '<div class="col-md-2">';
                            echo form_label('%', 'porc_meta');
                    		$data = array('id'=>'porc_meta', 'name'=>'porc_meta', 'class'=>'form-control poncentagem');
                    		echo form_input($data,'');
                            echo '</div>';
                        echo '</div>';
                        echo '<div id="CampoNumero">';
                        
                            echo '<div class="col-md-4">';
                            if($perguntas){
                                
                                $options = array(''=>'');
                                foreach($perguntas as $perg){
                                    $options[$perg->cd_anatel_quest] = 'ID '.$perg->cd_anatel_quest;
                                }		
                        		echo form_label('Pergunta', 'cd_pergunta_num');
                        		echo form_dropdown('cd_pergunta_num', $options, '', 'id="cd_pergunta_num" class="form-control"');
                                
                            }
                            echo '</div>';
                            
                            echo '<div class="col-md-4">';
                            echo form_label('&nbsp', 'igual');
                			$data = array('name'=>'igual', 'value'=>'=','id'=>'igual', 'class'=>'form-control operador', 'readonly'=>'readonly');
                			echo form_input($data);
                            echo '</div>';
                            
                            echo '<div class="col-md-4">';
                            echo form_label('N&uacute;mero', 'num_meta');
                    		$data = array('id'=>'num_meta', 'name'=>'num_meta', 'class'=>'form-control poncentagem');
                    		echo form_input($data,'');
                            echo '</div>';
                            
                        echo '</div>';
                        echo '<div id="camposFormula">';
                            echo '<div class="col-md-1 operador text-center">';
                            echo '<br>((';
                            echo '</div>';
                            echo '<div class="col-md-2">';
                            if($perguntas){
                                
                                $options = array(''=>'');
                                foreach($perguntas as $perg){
                                    $options[$perg->cd_anatel_quest] = 'ID '.$perg->cd_anatel_quest;
                                }		
                        		echo form_label('Pergunta 1', 'cd_pergunta1_form');
                        		echo form_dropdown('cd_pergunta1_form', $options, '', 'id="cd_pergunta1_form" class="form-control"');
                                
                            }
                            echo '</div>';
                            
                            echo '<div class="col-md-1">';
                            echo form_label('&nbsp', 'operador');
                			$data = array('name'=>'operador', 'value'=>'-','id'=>'operador', 'class'=>'form-control operador', 'readonly'=>'readonly');
                			echo form_input($data);
                            echo '</div>';
                            
                            echo '<div class="col-md-2">';
                            if($perguntas){
                                
                                $options = array(''=>'');
                                foreach($perguntas as $perg){
                                    $options[$perg->cd_anatel_quest] = 'ID '.$perg->cd_anatel_quest;
                                }		
                        		echo form_label('Pergunta 2', 'cd_pergunta2_form');
                        		echo form_dropdown('cd_pergunta2_form', $options, '', 'id="cd_pergunta2_form" class="form-control"');
                                
                            }
                            echo '</div>';
                            echo '<div class="col-md-3 operador text-center">';
                            echo '<br>)x100) / ID <span id="varFormula"></span>';
                            echo '</div>';
                            
                            echo '<div class="col-md-2">';
                            $options = array(''=>'', '=' => '=', '<=' => '<=', '>=' => '>=');		
                    		echo form_label('Comparar', 'comparador');
                    		echo form_dropdown('comparador', $options, '', 'id="comparador" class="form-control"');
                            echo '</div>';
                            
                            echo '<div class="col-md-1">';
                            echo form_label('%', 'porc_meta');
                    		$data = array('id'=>'porc_meta', 'name'=>'porc_meta', 'class'=>'form-control poncentagem', 'style' => 'width:100%');
                    		echo form_input($data,'');
                            echo '</div>';
                        echo '</div>';
                        
                    ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="cd_anatel_meta" name="cd_anatel_meta" value="" />
                    <input type="hidden" id="cd_anatel_frm" name="cd_anatel_frm" value="<?php echo $cd_anatel_frm; ?>" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar regra</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Adicionar regra meta -->    

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Anatel - Criar/Editar fomul&aacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'salvar_frm');
                    	echo form_open('anatel/salvarForm',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
             
                    		echo form_fieldset("Criar / Editar fomul&aacute;rio<a href='".base_url('anatel/formularios')."' class='linkDireita'><span class='glyphicon glyphicon-arrow-left'></span>&nbspVoltar Pesquisa</a>", $attributes);
                    		
                                echo '<div class="row">';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '');	
                                    foreach($tipos_frm_anatel as $tFa){
                                        $options[$tFa->cd_anatel_tipo_frm] = $tFa->nome;
                                    }	
                            		echo form_label('Qual sistema?<span class="obrigatorio">*</span>', 'cd_anatel_tipo_frm');
                            		echo form_dropdown('cd_anatel_tipo_frm', $options, $cd_anatel_tipo_frm, 'id="cd_anatel_tipo_frm" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div id="grupos" class="col-md-3">';
                                    if($cd_anatel_frm){
                                    $options = array('' => '');	
                                    foreach($grupos as $gru){
                            			$options[$gru->cd_anatel_xml] = $gru->nome;
                            		}	
                            		echo form_label('Grupo<span class="obrigatorio">*</span>', 'cd_anatel_xml');
                            		echo form_dropdown('cd_anatel_xml', $options, $cd_anatel_xml, 'id="cd_anatel_xml" class="form-control"');
                                    }
                                    echo '</div>';
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('' => '');		
                            		foreach($departamento as $dep){
                            			$options[$dep->cd_departamento] = htmlentities($dep->nome_departamento);
                            		}	
                            		echo form_label('Departamento<span class="obrigatorio">*</span>', 'cd_departamento');
                            		echo form_dropdown('cd_departamento', $options, $cd_departamento, 'id="cd_departamento" class="form-control"');
                                    echo '</div>';
                                    
                                    echo '<div id="indicadores" class="col-md-3">';
                                    if($cd_anatel_frm){
                                    $options = array('' => '');	
                                    foreach($indicador as $ind){
                            			$options[$ind->cd_anatel_indicador] = $ind->sigla;
                            		}	
                            		echo form_label('Indicador<span class="obrigatorio">*</span>', 'cd_anatel_indicador');
                            		echo form_dropdown('cd_anatel_indicador', $options, $cd_anatel_indicador, 'id="cd_anatel_indicador" class="form-control"');
                                    }
                                    echo '</div>';
                                    
                                    /*echo '<div class="col-md-2">';
                                    $options = array('' => '');	
                                    foreach($produtos as $pro){
                            			$options[$pro->cd_anatel_produto] = $pro->tipo;
                            		}	
                            		echo form_label('H&aacute; produto?', 'cd_anatel_produto');
                            		echo form_dropdown('cd_anatel_produto', $options, $cd_anatel_produto, 'id="cd_anatel_produto" class="form-control"');
                                    echo '</div>';*/
                                    
                                    echo '<div class="col-md-2">';
                                    $options = array('A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status');
                            		echo form_dropdown('status', $options, $status, 'id="status" class="form-control"');
                                    echo '</div>';
                                
                                echo '</div>';
                                
                                echo '<div class="row marginTopBottom">';
                                    echo '<div class="col-md-12">';
                                            echo '<button id="addQuestoes" type="button" class="btn btn-primary pull-left">Adicionar quest&atilde;o</button>';
                                            /*if($cd_anatel_frm and count($perguntas) > 0){
                                            echo '<button id="addRegraMeta" data-toggle="modal"  data-target="#addRegraMeta" type="button" class="btn btn-primary pull-left marginLeft" onclick="">Adicionar regra meta</button>';
                                            }*/
                                    echo '</div>';
                                echo '</div>';
                                
                                echo '<div id="todasQuestoes" class="row">';
                                    
                                    if($perguntas){
                                        
                                        foreach($perguntas as $perg){
                                            
                                            echo '<div class="col-md-7">';
                                            echo form_label('ID '.$perg->cd_anatel_quest.' - Quest&atilde;o&nbsp<a class="removeElemento" data-toggle="modal"  data-target="#apagaPergunta" onclick="apagaQuestao('.$perg->cd_anatel_quest.')" href="#addQuestoes">X</a>', 'perguntas-up['.$perg->cd_anatel_quest.']');
                                			#$data = array('name'=>'perguntas-up['.$perg->cd_anatel_quest.']', 'value'=>utf8_decode($perg->questao),'id'=>'perguntas-up['.$perg->cd_anatel_quest.']', 'placeholder'=>'Informe a pergunta', 'class'=>'form-control');
                                			#echo form_input($data);
                                            echo '<input type="text" id="perguntas-up['.$perg->cd_anatel_quest.']" name="perguntas-up['.$perg->cd_anatel_quest.']" value="'.utf8_decode($perg->questao).'" placeholder="Informe a pergunta" class="form-control" />';
                                            echo '</div>';
                                            
                                            echo '<div class="col-md-1">';
                                            echo form_label('Sigla');
                                			$data = array('name'=>'sigla-up['.$perg->cd_anatel_quest.']', 'value'=>$perg->sigla,'id'=>'sigla-up['.$perg->cd_anatel_quest.']', 'placeholder'=>'Informe a sigla', 'class'=>'form-control');
                                			echo form_input($data);
                                            echo '</div>';
                                            
                                            echo '<div class="col-md-2">';
                                            $options = array('I' => 'Inteiro', 'M' => 'Moeda', 'P' => 'Porcentagem', 'T' => 'Texto');		
                                    		echo form_label('Tipo resposta', 'tipo-resposta-up['.$perg->cd_anatel_quest.']');
                                    		echo form_dropdown('tipo-resposta-up['.$perg->cd_anatel_quest.']', $options, $perg->tipo_resp, 'id="tipo-resposta-up['.$perg->cd_anatel_quest.']" class="form-control"');
                                            echo '</div>';
                                            
                                            echo '<div class="col-md-2">';
                                            $options = array('S' => 'Sim', 'N' => 'N&atilde;o');		
                                    		echo form_label('Resp. obrigat&oacute;ria', 'resp-obrig-up['.$perg->cd_anatel_quest.']');
                                    		echo form_dropdown('resp-obrig-up['.$perg->cd_anatel_quest.']', $options, $perg->obrigatorio, 'id="resp-obrig-up['.$perg->cd_anatel_quest.']" class="form-control"');
                                            echo '</div>';
                                        }
                                    }
                                
                                echo '</div>';
                      
                                echo '<div class="actions">';
                                echo form_hidden('cd_anatel_frm', $cd_anatel_frm);
                                echo form_submit("btn_cadastro","Salvar", 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 

                    if($cd_anatel_frm){
                    ?>
                    <div id="accordion">
                        <h3>Regra de Meta</h3>
                        <div>
                            <p>
                            <div class="row marginTopBottom">
                                <div class="col-md-12">
                                    <button id="addRegraMeta" data-toggle="modal"  data-target="#addRegraMeta" type="button" class="btn btn-primary pull-left" onclick="">Adicionar regra meta</button>
                                </div>
                            </div>
                            <?php
                            if(count($regrasMeta) > 0){
                                foreach($regrasMeta as $rM){ 
                                    if($rM->regra == 'P'){ 
                                        echo '<div class="col-md-4 btn btn-default">';
                                        echo '<strong>'.$rM->cd_anatel_meta.' - </strong>((ID '.$rM->pergunta1.' / '.'ID '.$rM->pergunta2.') x 100) '.$rM->comparador.' '.$rM->numero.'%';
                                        echo '<a class="removeElemento float_right" data-toggle="modal"  data-target="#apagaRegra" onclick="removeRegra('.$rM->cd_anatel_meta.')" href="#addQuestoes">X</a>';
                                        echo '</div>';
                                    }elseif($rM->regra == 'N'){
                                        echo '<div class="col-md-4 btn btn-default">';
                                        echo '<strong>'.$rM->cd_anatel_meta.' - </strong>ID '.$rM->pergunta1.' = '.$rM->numero;
                                        echo '<a class="removeElemento float_right" data-toggle="modal"  data-target="#apagaRegra" onclick="removeRegra('.$rM->cd_anatel_meta.')" href="#addQuestoes">X</a>';
                                        echo '</div>';
                                    }else{ 
                                        echo '<div class="col-md-4 btn btn-default">';
                                        echo '<strong>'.$rM->cd_anatel_meta.' - </strong>((ID '.$rM->pergunta1.' - '.'ID '.$rM->pergunta2.') x 100)'.' / ID '.$rM->pergunta1.') '.$rM->comparador.' '.$rM->numero.'%';
                                        echo '<a class="removeElemento float_right" data-toggle="modal"  data-target="#apagaRegra" onclick="removeRegra('.$rM->cd_anatel_meta.')" href="#addQuestoes">X</a>';
                                        echo '</div>';
                                    }
                                }
                            } 
                            ?>
                            </p>
                        </div>
                        <h3>Usu&aacute;rio respons&aacute;vel por responder</h3>
                        <div>
                            <p>
                            <div class="row marginTopBottom">
                                <div class="col-md-12">
                                    <button id="associarUsuario" data-toggle="modal"  data-target="#associarUsuario" type="button" class="btn btn-primary pull-left" onclick="">Associar usu&aacute;rio</button>
                                </div>
                                <div class="col-md-12">
                                    <?php
                                        $this->table->set_heading('Respons&aacute;vel', 'Unidade', 'A&ccedil;&atilde;o');
                                        foreach($responsaveis as $resp){
                                            $cell1 = array('data' => htmlentities($resp->nome_usuario));
                                            
                                            $dadosUni = '';
                                            foreach($respUnidade[$resp->cd_usuario] as $resUni){
                                                $dadosUni .= htmlentities($resUni->nome).'&nbsp<span onclick="apgUniUser('.$resUni->cd_unidade.', '.$resp->cd_usuario.')" class="glyphicon glyphicon glyphicon glyphicon-remove"></span><br>';
                                            }
                                            
                                            $cell2 = array('data' => $dadosUni);
                                            #$cell3 = (in_array(131, $this->session->userdata('permissoes')))? '<a title="Apagar" href="#" onclick="apagarRegistro('.$resp->cd_usuario.')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>': '';
                                            $cell3 = '<a title="Apagar" href="#" onclick="apagarRegistro('.$resp->cd_usuario.',\''.$resp->nome_usuario.'\')" data-toggle="modal"  data-target="#apagaAssociacao" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>';
                                            $this->table->add_row($cell1, $cell2, $cell3);
                                        }
                                        
                                        
                                        $template = array('table_open' => '<table class="table zebra">');
                                    	$this->table->set_template($template);
                                    	echo $this->table->generate();
                                    ?>
                                </div>
                            </div>    
                            </p>
                        </div>
                    </div>
                    <?php
                    }
                    ?>  
                </div>
            </div>
    
<script type="text/javascript">

function removeQuestao(campo){
    event.preventDefault();
    $(campo).parent().parent().parent().remove();
}

function apgUniUser(cdUnidade, cdUsuario){
    
    var r = confirm("Deseja apagar a unidade associada ao usuario?");
    if (r == true) {
        $(location).attr('href',"<?php echo base_url(); ?>anatel/apgUniUser/<?php echo $cd_anatel_frm; ?>/"+cdUnidade+"/"+cdUsuario);
    }
    
}

/* Se aba do dashboard for aberta grava log de acesso */
$.fn.verificaClick = function() {
    if(!$(this).hasClass('ui-state-active')) {
        $.post( "<?php echo base_url(); ?>dashboard/registraAcesso/", { cd_grafico: $("#cd_grafico").val()} );
    }
};

function apagarRegistro(cd, nome){
    $("#apg_assoc").val(cd);
    $("#desc_apg_assoc").val(nome);
}

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

function apagaQuestao(cd){
    
    $("#apg_questao").val(cd);
    
}

function removeRegra(cd){
    
    $("#apg_regra_meta").val(cd);
    
}

$.fn.carregando = function(tempo) {
    
    $.blockUI({ 
    message:  '<h1>Carregando combo...</h1>',
    css: { 
    	border: 'none', 
    	padding: '15px', 
    	backgroundColor: '#000', 
    	'-webkit-border-radius': '10px', 
    	'-moz-border-radius': '10px', 
    	opacity: .5, 
    	color: '#fff' 
    	} 
    }); 
    
    setTimeout($.unblockUI, tempo);    
    
}

$(document).ready(function(){
    
<?php if($cd_anatel_frm){ ?>          

    $(function() {
        $( "#accordion" ).accordion({
            collapsible: true, // Habilita a opção de expandir e ocultar ao clicar
            heightStyle: "content",
            active: false
        });
    });    
    
    // Informações da regra selecionada
    $("#tipo_regra").change(function(){
        
        if($(this).val() != ''){
            
            if($(this).val() == 'P'){
                
                $("#camposPorcentagem").css("display", "block");
                $("#CampoNumero").css("display", "none");
                $("#camposFormula").css("display", "none");
                
            }
            
            if($(this).val() == 'N'){
                
                $("#CampoNumero").css("display", "block");
                $("#camposPorcentagem").css("display", "none");
                $("#camposFormula").css("display", "none");
                
            }
            
            if($(this).val() == 'F'){
                
                $("#camposFormula").css("display", "block");
                $("#camposPorcentagem").css("display", "none");
                $("#CampoNumero").css("display", "none");
                
            }
            
        }else{
            
            $("#camposPorcentagem").css("display", "none");
            $("#CampoNumero").css("display", "none");
            $("#camposFormula").css("display", "none");
            
        }
            
    });
    
    $("#camposPorcentagem").css("display", "none");
    $("#CampoNumero").css("display", "none");
    $("#camposFormula").css("display", "none");
    
    $(".data").mask("00/00/0000");
    
    $(".poncentagem").mask("00.00");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
<?php } ?>     
    // Adiciona Questão
    $("#addQuestoes").click(function(){
        
        //var contCnae = $('[name*="cnae_sec"]').length;
        var addQuestacao = '<div>';
            addQuestacao += '<div class="col-md-7">';
            addQuestacao += '<label for="pergunta">Quest&atilde;o&nbsp';
            addQuestacao += '<a class="removeElemento" onclick="removeQuestao(this)" href="#">X</a>';
            addQuestacao += '</label>';
            addQuestacao += '<input type="text" name="perguntas[]" value="" data="perguntas[]" id="perguntas[]" placeholder="Informe a pergunta" class="form-control"  />';                                   
            addQuestacao += '</div>';
            addQuestacao += '<div class="col-md-1">';
            addQuestacao += '<label for="sigla">Sigla';
            addQuestacao += '</label>';
            addQuestacao += '<input type="text" name="sigla[]" value="" data="sigla[]" id="sigla[]" placeholder="Informe a sigla" class="form-control"  />';                                   
            addQuestacao += '</div>';
            addQuestacao += '<div class="col-md-2">';
            addQuestacao += '<label for="tipo_resposta">Tipo resposta</label>';
            addQuestacao += '<select name="tipo_resposta[]" id="tipo_resposta[]" class="form-control">';
            //addQuestacao += '<option value="" selected="selected"></option>';
            addQuestacao += '<option value="I">Inteiro</option>';
            addQuestacao += '<option value="M">Moeda</option>';
            addQuestacao += '<option value="P">Porcentagem</option>';
            addQuestacao += '<option value="T">Texto</option>';
            addQuestacao += '</select>';
            addQuestacao += '</div>';
            addQuestacao += '<div class="col-md-2">';
            addQuestacao += '<label for="obrigatorio">Resp. obrigat&oacute;ria</label>';
            addQuestacao += '<select name="resp-obrig[]" id="resp-obrig[]" class="form-control">';
            //addQuestacao += '<option value="" selected="selected"></option>';
            addQuestacao += '<option value="S">Sim</option>';
            addQuestacao += '<option value="N">N&atilde;o</option>';
            addQuestacao += '</select>';
            addQuestacao += '</div>';
            addQuestacao += '</div>';
  
            $("#todasQuestoes").append(addQuestacao);
            
            /*$(this).estiloCamposDinamicos('cnae');
            
            $('[name*="cnae_sec"]').each(function () {
                //dump(this);
                $(this).rules('add', {
                    required: true,
                    messages: {
                        required: "O campo CNAE Secund&aacute;rio &eacute; indispens&aacute;vel"
                    }
                });
            });*/
            
            
    });    
    
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data,#data2").datepicker({
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

$(document).ready(function(){
    
    $('#assUser').css('display', 'none');
    
    // Informações da regra selecionada
    $("#cd_departamento").change(function(){
        
        if($(this).val() != ''){
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/anatelIndDepartamento',
              data: {
                cd_departamento: $(this).val()
              },
              dataType: "json",
              error: function(res) {
 	            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
                alert('erro');
              },
              success: function(res) {
                
               var select = '<label for="cd_anatel_indicador">Indicador<span class="obrigatorio">*</span></label>';
                   select += '<select name="cd_anatel_indicador" id="cd_anatel_indicador" class="form-control">';
                   select += '<option value="" selected="selected"></option>';
                if(res.length > 0){
                
                    $.each(res, function() {
                      
                      select += '<option title="'+this.nome+'" value="'+this.cd_anatel_indicador+'">'+this.sigla+'</option>';
                      
                    });
                    
                    select += '</select>';
                    
                    $("#indicadores").html(select);
                
                }else{
                    
                    $("#indicadores").html('');
                    
                }
                
              }
              
            });
            
        }else{
            
            $("#indicadores").html('');
            
        }
            
    });
    
    // Informações da regra selecionada
    $("#cd_anatel_tipo_frm").change(function(){
        
        if($(this).val() != ''){
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/anatelGrupoIndicador',
              data: {
                cd_sistema: $(this).val()
              },
              dataType: "json",
              error: function(res) {
 	            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
                alert('erro');
              },
              success: function(res) {
                
               var select = '<label for="cd_anatel_xml">Grupo<span class="obrigatorio">*</span></label>';
                   select += '<select name="cd_anatel_xml" id="cd_anatel_xml" class="form-control">';
                   select += '<option value="" selected="selected"></option>';
                if(res.length > 0){
                
                    $.each(res, function() {
                      
                      select += '<option value="'+this.cd_anatel_xml+'">'+this.nome+'</option>';
                      
                    });
                    
                    select += '</select>';
                    
                    $("#grupos").html(select);
                
                }else{
                    
                    $("#grupos").html('');
                    
                }
                
              }
              
            });
            
        }else{
            
            $("#grupos").html('');
            
        }
            
    });

<?php if($cd_anatel_frm){ ?>   
    // Informações da regra selecionada
    $("#cd_departamento_user").change(function(){
        
        if($(this).val() != ''){
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>ajax/usuariosDepartamento',
              data: {
                cd_departamento: $(this).val(),
                cd_anatel_frm: <?php echo $cd_anatel_frm ?>
              },
              dataType: "json",
              error: function(res) {
 	            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
                alert('erro');
              },
              success: function(res) {
                
                   var checkbox = '';
                    if(res.length > 0){
                    
                        $('#assUser').css('display', 'inline');
                        
                        $.each(res, function() {
                          
                          //checkbox += '<input type="checkbox" id="user[]" name="user[]" value="'+this.cd_usuario+'" />&nbsp'+this.nome_usuario+'<br/>';
                          checkbox += '<input id="user[]" name="user[]" type="checkbox" value="'+this.cd_usuario+'">&nbsp'+this.nome_usuario+'<br/>';
                          
                        });
                        
                        $("#usuarios").html(checkbox);
                        
                        $("#associaUsuario").validate();
                    
                    }else{
                        
                        $('#assUser').css('display', 'none');
                        $("#usuarios").html('');
                        
                    }
                
              }
              
            });
              
        }else{
            
            $('#assUser').css('display', 'none');
            
            $("#usuarios").html('');
            
        }
        
    });
<?php } ?>  
    
    // Completa formula da equação
    $("#cd_pergunta1_form").change(function(){
        
        if($(this).val() != ''){
            $("#varFormula").html($(this).val());
        }
            
    });
    
    // Valida o formulário
	$("#salvar_frm").validate({
		debug: false,
		rules: {
			cd_anatel_tipo_frm: {
                required: true
            },
            cd_anatel_xml: {
                required: true
            },
            cd_departamento: {
                required: true
            },
            cd_anatel_indicador: {
                required: true
            }
		},
		messages: {
			cd_anatel_tipo_frm: {
                required: "Selecione o tipo de formul&aacute;rio."
            },
            cd_anatel_xml: {
                required: "Selecione o grupo do indicador."
            },
            cd_departamento: {
                required: "Selecione o departamento."
            },
            cd_anatel_indicador: {
                required: "Selecione o indicador."
            }
            
	   }
   });
   
   // Valida o formulário
	$("#associaUsuario").validate({
		debug: false,
		rules: {
            'user[]': {
                required: true
            },
			'cd_unidade[]': {
                required: true
            }
		},
		messages: {
            'user[]': {
                required: "Selecione um usu&aacute;rio."
            },
			'cd_unidade[]': {
                required: "Selecione uma unidade."
            }
	   }
   });      
   
});

</script>