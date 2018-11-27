    <?php
        echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
        echo link_tag(array('href' => 'assets/css/multi-select.css','rel' => 'stylesheet','type' => 'text/css'));
        echo link_tag(array('href' => 'assets/css/bootstrap.min.css','rel' => 'stylesheet','type' => 'text/css'));
        #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
    ?>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.multi-select.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
    
    <div class="col-md-offset-1 col-md-10">
        
        <div class="col-md-offset-1 col-md-6">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                <li><a href="<?php echo base_url('inventario')?>">Inventario</a></li>
                <li><a href="<?php echo base_url('inventario/pesquisa')?>">Pesquisa</a></li>
                <li class="active">Cadastrar Equipamento</li>
            </ol>
        </div>
        
        <div class="row"></div>
        
        <?php echo $this->session->flashdata('statusOperacao');?>
        
        <div class="col-md-offset-1 col-md-10">
            <?php
                $form = ['id'=>'form_pesq'];
                $paramBase = ['class'=>'form-control'];
                echo form_fieldset('Cadastro de Computadores&nbsp<a href="#"><span style="font-size:30px;" id="btn_salvar" class="glyphicon glyphicon-floppy-disk float-right"></span></a>');
                    echo form_open('inventario/salvarMaquina',$form);
                        
                        echo '<div class="col-md-10">';
                            
                            echo '<h3>Localidade</h3>';

                            echo '<div class="col-md-3">';
                                echo form_label('Regional');
                                $opcLocalidade = [''=>''];
                                foreach($unidades as $unidade){
                                    $opcLocalidade[$unidade->cd_unidade] = $unidade->nome;
                                }
                                echo form_dropdown('FK_cd_localidade',$opcLocalidade,'','class="form-control" id="t"');
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
                            echo '<div id="nSerie" class="col-md-2 form-group">';
                                echo '<label class="control-label">N&deg Serie</label>';
                                $paramUsu = $paramBase + ['id'=>'numSerie', 'class'=>'has-warning','name'=>'numero_serie','type'=>'text','valido'=>'false'];
                                echo form_input($paramUsu);
                            echo '</div>';

                            echo '<div class="col-md-3">';
                                echo form_label('Tipo Equipamento');
                                $tipoEquip = [''=>''];
                                foreach($tipoEquipamento as $tp){
                                    $tipoEquip[$tp->id_tipo_equipamento] = $tp->tipo_equipamento;
                                }
                                
                                echo form_dropdown('FK_tipo_equipamento',$tipoEquip,'','class="form-control" id="tipoEquip"');
                            echo '</div>';

                            echo '<div class="col-md-2">';
                                echo form_label('Fabricante');
                                
                                $opcFabricante = ['' => ''];
                                foreach ($marca as $mar){
                                    $opcFabricante[$mar->id_marca] = $mar->nome;
                                }
                                
                                echo form_dropdown('FK_fabricante',$opcFabricante,'','class="form-control" id="fabricante"');
                            echo '</div>';
                            
                            echo '<div class="col-md-2">';
                                echo form_label('Modelo');
                                
                                $opcModelo = ['' => ''];
                                foreach ($modelo as $mod){
                                    $opcModelo[$mod->id_modelo] = $mod->modelo;
                                }
                                
                                echo form_dropdown('FK_modelo',$opcModelo,'','class="form-control" id="modeloEquipamento"');
                            echo '</div>';

                            echo '<div class="col-md-2">';
                                echo form_label('Status');
                                $opcStatus = [''=>'','ativo'=>'Ativo','Estoque'=>'Estoque'];
                                echo form_dropdown('status',$opcStatus,'','class="form-control" id="equipStatus"');
                            echo '</div>';
                        echo '</div>';
                        
                        
//                        echo '<div class="row"></div>';
//                        echo '<br>';


//                        echo '<h3>Checklists</h3>';
//                        echo '<div class="col-md-10">';
//                            
//                            echo '<div class="col-md-2">';
//                                $opc2 = ['name'=>'checklist[basico]', 'style'=>'margin-left:30px','id'=>'vale_alimentacao_checkbox', 'value'=>'accept'];
//                                echo form_label('Basico');
//                                echo form_checkbox($opc2);
//                            echo '</div>';
//                            
//                        echo '</div>';
//                        
//                        echo '<div class="row"></div>';
//                        echo '<br>';
                        
//                        echo '<div class="col-md-10">';
//                            echo '<h3>Software</h3>';
//                            $opt = ['op1'=>'op1','op2'=>'op2','op3'=>'op3'];
//                            echo form_dropdown('softwareList',$opt,'','id="my" multiple="multiple"');
//                        echo '</div>';
//                        
//                        echo '<div class="row"></div>';
//                        echo '<br>';
//                        echo '<br>';
//                        echo '<br>';
//                        echo '<br>';
                        
                    echo form_close();
                echo form_fieldset_close();
            ?>
        </div>
    </div>    
    
    <script type="text/javascript" src="<?php echo base_url('assets/js/inventario/hardware/maquina/cadastroEquipamento_view.js');?>"></script>
    <script>
        var validaNumeroSeire = '<?php echo base_url('/inventario/ajaxInventario/validaNumeroSeire')?>';
    </script>