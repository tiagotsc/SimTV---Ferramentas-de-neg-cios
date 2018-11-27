<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="modal fade" id="faltasModal" tabindex="-1" role="dialog" aria-labellebdy="faltasModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" arial-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="FaltasModalLabel">Historico de faltas - <?php echo $nome_usuario;?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php   
                        echo '<div id="cadastraFalta" class="col-md-12">';
                            echo form_fieldset("Cadastrar faltas ".'<a href="#"><span class="glyphicon glyphicon-remove" id="fechaModalFaltas" style="color:red" aria-hidden="true"></span></a>');
                                echo '<div class="row">';
                                    echo '<div class="col-md-12">';
                                        $inf = array('class'=>'pure-form','id'=>'feriasAlter');
                                        echo form_open('rh-usuario/faltas/salvaFalta',$inf);

                                            echo form_hidden('cd_usuario',$cd_usuario);

                                            echo '<div class="col-md-4">';
                                                echo form_label('Dia da Falta', 'diaFalta');
                                                $data = array('id'=>'diaFalta', 'name'=>'data_falta', 'class'=>'form-control data', 'value'=>$this->util->formataData($feriasAtivas['inicio'],'br'));
                                                echo form_input($data,'');
                                            echo '</div>';

                                            /*echo '<div class="col-md-4">';
                                                $options = array('F' => 'Ferias', 'B' => 'Banco');		
                                                echo form_label('Tipo', 'tipo');
                                                echo form_dropdown('tipo', $options, $status_config_usuario, 'id="status_usuario" class="form-control"');
                                            echo '</div>';*/

                                            echo '<div class="col-md-offset-9 col-md-3">';
                                                echo form_submit("btn_cadastro","Salvar", 'type="submit" id="edita-ferias" class="btn btn-primary pull-right marginTopBottom"');
                                                echo '</div>';
                                            echo '</div>';

                                        echo form_close();
                                    echo '</div>';
                                echo '</div>';
                            echo form_fieldset_close();
                        echo '</div>';

                        echo '<br>';echo '<br>';

                        echo form_fieldset("Historico de faltas");
                            echo '<div class="row"></div>';
                            
                            echo '<div class="col-md-12">';
                                echo '<table class="table table-striped">';
                                    echo '<thead>';
                                        echo '<tr>';
                                            echo '<th>Data Falta</th>';
                                            echo '<th>Motivo</th>';
                                        echo '</tr>';
                                    echo '</thead>';
                                    
                                    echo '<tbody id="corpoTabela">';

                                    echo '</tbody>';
                                    
                                echo '</table>';
                            echo '</div>';
                        echo form_fieldset_close();
                    ?>
                </div>
                <div class="modal-footer">
                    
                    <?php
                    
                        
                        echo '<button type="button" id="btnCadastraFaltas" class="btn btn-success">Cadastrar</button>';
                    
                    ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var urlFaltas = '<?php echo base_url('rh-usuario/ajaxUsuario/retornaFaltas')?>';
</script>
<script type="text/javascript" src="<?php echo base_url("assets/js/rh-usuario/faltas/faltas_modal.js") ?>"></script>