<div id="editaModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edita Computador</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <?php
                        $form = ['id'=>'form_pesq'];
                        $paramBase = ['class'=>'form-control'];
                        echo form_open('',$form);
                            echo '<div class="col-md-10">';

                                echo '<h3>Localidade</h3>';

                                echo '<div class="col-md-3">';
                                    echo form_label('Regional');
                                    $opcLocalidade = [''=>''];
                                    foreach($unidades as $unidade){
                                        $opcLocalidade[$unidade->cd_unidade] = $unidade->nome;
                                    }
                                    echo form_dropdown('FK_cd_localidade',$opcLocalidade,'','class="form-control" id="regional"');
                                echo '</div>';

                                echo '<div class="col-md-3">';
                                    echo form_label('Departamento');
                                    $opcDepto = [''=>''];
                                    foreach($departamentos as $depto){
                                        $opcDepto[$depto->cd_departamento] = htmlentities($depto->nome_departamento);
                                    }
                                    echo form_dropdown('FK_cd_setor',$opcDepto,'','class="form-control" id="departamento"');
                                echo '</div>';

                            echo '</div>';

                            echo '<div class="row"></div>';
                            echo '<br>';

                            echo '<div class="col-md-11">';
                                echo '<h3>Dados Tecnicos</h3>';
                                echo '<div id="nSerie" class="col-md-3 form-group">';
                                    echo '<label class="control-label">N&deg Serie</label>';
                                    $paramUsu = $paramBase + ['id'=>'numSerie', 'class'=>'has-warning','name'=>'numero_serie','type'=>'text','valido'=>'false'];
                                    echo form_input($paramUsu);
                                echo '</div>';

                                echo '<div class="col-md-3">';
                                    echo form_label('Tipo Equipamento');
                                    $tipoEquip = [''=>'', '1'=>'Computador', '2'=>'Laptop'];
                                    echo form_dropdown('tipo_equipamento',$tipoEquip,'','class="form-control" id="tipoEquip"');
                                echo '</div>';

                                echo '<div class="col-md-2">';
                                    echo form_label('Fabricante');
                                    $opcFabricante = [''=>'','1'=>'DELL','2'=>'HP'];
                                    echo form_dropdown('fabricante',$opcFabricante,'','class="form-control" id="fabricante"');
                                echo '</div>';

                                echo '<div class="col-md-2">';
                                    echo form_label('Modelo');
                                    $opcModelo = [''=>'','1'=>'Sim+','2'=>'Compac'];
                                    echo form_dropdown('modelo',$opcModelo,'','class="form-control" id="modeloEquipamento"');
                                echo '</div>';

                                echo '<div class="col-md-2">';
                                    echo form_label('Status');
                                    $opcStatus = [''=>'','ativo'=>'Ativo','Estoque'=>'Estoque'];
                                    echo form_dropdown('status',$opcStatus,'','class="form-control" id="equipStatus"');
                                echo '</div>';
                            echo '</div>';
                        echo form_close();          
                    ?>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary ">Salvar Alteracoes</button>
            </div>
            <input type="hidden" id="validaNumeroSerie" value="<?php echo base_url('/inventario/ajaxInventario/validaNumeroSeire');?>">
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript" src="<?php echo base_url('assets/js/inventario/hardware/maquina/editaEquipamento_modal.js');?>"></script>