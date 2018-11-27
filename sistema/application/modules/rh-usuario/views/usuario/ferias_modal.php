<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>


<div class="modal fade" id="feriasModal" tabindex="-1" role="dialog" aria-labelledby="feriasModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="FeriasModalLabel">Historico de afastamento - <?php echo $nome_usuario;?></h4>
            </div>
            
            <div class="modal-body">
                <!--<div class="row">-->
                    <?php
                        $operacao = ($feriasAtivas <> NULL)?'Editar':'Cadastrar';
                        
                        echo '<div id="alterarFerias" class="col-md-12">';
                            echo form_fieldset($operacao." Ferias ".'<a href="#"><span class="glyphicon glyphicon-remove" id="fechaModalFerias" style="color:red" aria-hidden="true"></span></a>');
                                echo '<div class="row">';
                                    echo '<div class="col-md-12">';
                                        
                                        $inf = array('class'=>'pure-form','id'=>'feriasAlter');
                                        echo form_open('rh-usuario/usuario/salvarFerias',$inf);
                                            
                                            echo '<input id="id_ferias" type="hidden" name="id_ferias" value="'.$feriasAtivas['id'].'" >';
                                            echo form_hidden('cd_usuario',$cd_usuario);
                                            
                                            echo '<div class="col-md-4">';
                                                echo form_label('Inicio', 'inicio');
                                                $data = array('id'=>'inicio', 'name'=>'inicio', 'class'=>'form-control data', 'value'=>$this->util->formataData($feriasAtivas['inicio'],'br'));
                                                echo form_input($data,'');
                                            echo '</div>';
                                            
                                            echo '<div class="col-md-4">';
                                                echo form_label('Fim', 'fim');
                                                $data = array('id'=>'fim', 'name'=>'fim', 'class'=>'form-control data', 'value'=>$this->util->formataData($feriasAtivas['fim'],'br'));
                                                echo form_input($data,'');
                                            echo '</div>';

                                            echo '<div class="col-md-4">';
                                                $options = array('F' => 'Ferias', 'B' => 'Banco');		
                                                echo form_label('Tipo', 'tipo');
                                                echo form_dropdown('tipo', $options, $status_config_usuario, 'id="status_usuario" class="form-control"');
                                            echo '</div>';
                                            
                                            echo '<div class="col-md-2 pull-right">';
                                                echo form_submit("btn_cadastro","Salvar", 'type="button" id="edita-ferias" class="btn btn-primary pull-right marginTopBottom"');
                                            echo '</div>';
                                            
                                            if($feriasAtivas['id'] != NULL){
                                                echo '<div class="col-md-2" style="margin-left:350px">';
                                                    echo form_submit("btn_cadastro","Deletar", 'type="submit" id="deleta-ferias" class="btn btn-primary marginTopBottom"');
                                                echo '</div>';
                                            }
                                        echo form_close();
                                    echo '</div>';
                                echo '</div>'; //row
                            echo form_fieldset_close();
                        echo '</div>';
                        
                        echo '<div id="feriasAtuais" class="col-md-12">';
                            echo form_fieldset("Ferias Atuais");
                                echo '<div class="row">';
                                    echo '<div class="col-md-12">';
                                        if($feriasAtivas <> NULL){

                                            $data = array('class'=>'pure-form','id'=>'frm_ferias');
                                            echo '<div class="row">';

                                                echo form_open('',$data);

                                                    echo '<div class="col-md-4">';
                                                        echo form_label('Inicio', 'inicio');
                                                        $data = array('id'=>'inicio', 'name'=>'inicio', 'class'=>'form-control', 'readonly'=>'readonly','value'=>$this->util->formataData($feriasAtivas['inicio'],'br'));
                                                        echo form_input($data,'');
                                                    echo '</div>';

                                                    echo '<div class="col-md-4">';
                                                        echo form_label('Fim', 'fim');
                                                        $data = array('id'=>'fim', 'name'=>'fim', 'class'=>'form-control', 'readonly'=>'readonly', 'value'=>$this->util->formataData($feriasAtivas['fim'],'br'));
                                                        echo form_input($data,'');
                                                    echo '</div>';

                                                    echo '<div class="col-md-4">';
                                                        echo form_label('Motivo', 'motivo');
                                                        $info = array('id'=>'motivo', 'name'=>'motivo', 'class'=>'form-control', 'readonly'=>'readonly', 'value'=>($feriasAtivas['tipo']=="F")?"Ferias":"Banco");
                                                        echo form_input($info,'');
                                                    echo '</div>';

                                                    echo '<div class="col-md-offset-8 col-md-4">';
                                                            echo form_submit("btn_cadastro","Editar", 'id="btnEditaFerias" class="btn btn-primary pull-right marginTopBottom"');
                                                    echo '</div>';

                                                echo form_close();

                                            echo '</div>';
                                        }else{
                                            echo '<div>';
                                                echo '<blockquote>';
                                                    echo '<p>O colaborador nao possui ferias agendadas</p>';
                                                echo '</blockquote>';
                                            echo '</div>';
                                        }
                                    echo '</div>';
                                echo '</div>';//row
                            echo form_fieldset_close();
                        echo '</div>';

                        echo '<br>';echo '<br>';
                        
                        echo '<div class="col-md-12">';
                            echo form_fieldset("Ferias Passadas");
                                echo '<div class="row">';
                                    if($feriasInativas <> NULL){
                                        echo '<br>';
                                        echo '<div id="feriasPassadas" class="col-md-12 table-responsive">';
                                            echo '<table class="table table-striped">';
                                                echo '<tr>';
                                                    echo '<th>Inicio</th>';
                                                    echo '<th>Fim</th>';
                                                    echo '<th>Motivo</th>';
                                                echo '</tr>';
                                                foreach ($feriasInativas as $d){
                                                    echo '<tr>';
                                                        echo '<td>'.$this->util->formataData($d['inicio'],'br').'</td>';
                                                        echo '<td>'.$this->util->formataData($d['fim'],'br').'</td>';
                                                        echo '<td>'.(($d['tipo']=='F')?'Ferias':'Banco').'</td>';
                                                    echo '</tr>';
                                                }
                                            echo '</table>';
                                        echo '</div>';
                                    }else{
                                        echo'<div id="nao-ferias" class="col-md-12">';
                                            echo '<blockquote>';
                                                echo '<p>O colaborador nao teve nenhum periodo de ausencia</p>';
                                            echo '</blockquote>';
                                        echo'</div>';
                                    }
                                echo '</div>';
                            echo form_fieldset_close();
                        echo '</div>';
                    ?>
                <!--</div>-->
            </div>
            
            <div class="modal-footer">
                <?php
                    echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
                    if($feriasAtivas == NULL){
                        echo '<button type="button" id="btnCadastraFerias" class="btn btn-success">Cadastrar</button>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>