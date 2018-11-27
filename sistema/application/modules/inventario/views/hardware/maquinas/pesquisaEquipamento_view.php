<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(array('href' => 'assets/css/inventario/hardware/maquina/pesquisaEquipamento_view.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(['href' => 'assets/css/dataTable/jquery.dataTables.css','rel' => 'stylesheet','type' => 'text/css']);
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>  
    
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/dataTable/jquery.dataTables.js") ?>"></script>
    
    <?php include_once 'editaEquipamento_modal.php';?>
    
    <div class="col-md-10">
        <div class="col-md-5">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                <li><a href="<?php echo base_url('inventario')?>">Inventario</a></li>            
                <li class="active">Pesquisa Equipamento</li>                
            </ol>
        </div>

        <!--<div class="row"></div>-->
        
        <div class="col-md-12">
            <?php
//                $form = ['id'=>'form_pesq'];
//                $paramBase = ['class'=>'form-control'];
                echo form_fieldset('Pesquisar<a href="'.base_url("inventario/cadastro").'" id="cadastraEquip" class="float-right">Cadastrar&nbsp<span id="spanCadastrar" class="glyphicon glyphicon-plus float-right"></span></a>');
//                    echo form_open('',$form);
//
//                        echo '<div class="col-md-2">';
//                            echo form_label('Numero de serie');
//                            $paramUsu = $paramBase + ['id'=>'frmUsuario','type'=>'text'];
//                            echo form_input($paramUsu);
//                        echo '</div>';
//
//                        echo '<div class="col-md-3">';
//                            echo form_label('Tipo Equipamento');
//                            $tipoEquip = [''=>'', 'pc'=>'PC', 'laptop'=>'Laptop'];
//                            echo form_dropdown('tipoEquip',$tipoEquip,'','class="form-control" id="tipoEquip"');
//                        echo '</div>';
//
//                        echo '<div class="col-md-2">';
//                            echo form_label('Status');
//                            $opcStatus = [''=>'','ativo'=>'Ativo','Estoque'=>'Estoque'];
//                            echo form_dropdown('equipStatus',$opcStatus,'','class="form-control" id="equipStatus"');
//                        echo '</div>';
//
//                        echo '<div class="col-md-3">';
//                            echo form_label('Localidade');
//                            $opcLocalidade = [''=>'','holding'=>'Holding','salvador'=>'Salvador'];
//                            echo form_dropdown('equipStatus',$opcLocalidade,'','class="form-control" id="equipStatus"');
//                        echo '</div>';
//
//                        echo '<div class="col-md-2">';
//                            echo form_label('Modelo');
//                            $opcModelo = [''=>'','sim+'=>'Sim+','compac'=>'Compac'];
//                            echo form_dropdown('equipStatus',$opcModelo,'','class="form-control" id="equipStatus"');
//                        echo '</div>';
//                        
//                        echo '<div class="row"></div>';
//                        
////                        echo '<div class="col-md-12">';
//                            $btnInfo = ['name'=>'pesquisaEquipamento', 'id'=>'btnPesq', 'class'=>'btn btn-primary form-control float-right', 'content'=>'Pesquisar'];
//                            echo form_button($btnInfo);
////                        echo '</div>';
//
//
//                    echo form_close();
                echo form_fieldset_close();
            ?>
        </div>
        
        <div class="row"></div>
        <br>
        
        <div id="exibicaoInventario" class="col-md-12">
            <div class="well">
                <table id="equipInfo" style="text-align: center" class="table">
                    <thead style="text-align: center">
                        <tr>
                            <th>Regional</th>
                            <th>Setor</th>
                            <th>Numero de Serie</th>
                            <th>Tipo Equipamento</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Status</th>
                            <th>Editar</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        <?php
                            $i = 1;
                            foreach($maquinas as $maquina){
                                echo '<tr>';
//                                    echo '<input id="id_equipamento_'.$i.'" type="hidden" value="'.$maquina["id_equipamento"].'">';
                                    echo '<th>'.$maquina['unidade_nome'].'</th>';
                                    echo '<th>'.$maquina['nome_departamento'].'</th>';
                                    echo '<th>'.$maquina['numero_serie'].'</th>';
                                    echo '<th>'.ucfirst($maquina['tipo_equipamento']).'</th>';
                                    echo '<th>'.$maquina['nome'].'</th>';
                                    echo '<th>'.$maquina['modelo'].'</th>';
                                    echo '<th>'.$maquina['status'].'</th>';
                                    echo '<th><a href="#" key="'.$i.'" class="modalOpen" onclick=p('.$maquina["id_equipamento"].');><span class="glyphicon glyphicon-pencil"></span></a></th>';
//                                    echo '<th><a href="#" key="'.$i.'" class="modalOpen" onclick=p('.$maquina["id_equipamento"].');><span class="glyphicon glyphicon-pencil"></span></a></th>';
                                echo '</tr>';
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="<?php echo base_url('assets/js/inventario/hardware/maquina/pesquisaEquipamento_view.js');?>"></script>
    
    <script>
        var retornaEquipamento = "<?php echo base_url('inventario/ajaxInventario/retornaEquipamento');?>";
        
        /*function t(){
            $('#editaModal').modal('show');
        }*/
    </script>