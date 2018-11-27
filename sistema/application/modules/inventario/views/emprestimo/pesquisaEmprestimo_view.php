<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(array('href' => 'assets/css/inventario/hardware/maquina/pesquisaEquipamento_view.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(array('href' => 'assets/css/inventario/emprestimo/pesquisaEmprestimo_view.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(['href' => 'assets/css/dataTable/jquery.dataTables.css','rel' => 'stylesheet','type' => 'text/css']);
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>  
    
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/dataTable/jquery.dataTables.js") ?>"></script>
    
    <div class="col-md-10">
        <div class="col-md-5">
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                <li><a href="<?php echo base_url('inventario')?>">Inventario</a></li>            
                <li class="active">Pesquisa Emprestimo</li>                
            </ol>
        </div>
        
        <div class="col-md-12">
            <!--<div class="col-md-10">-->
            <legend>Pesquisar<a href="<?php echo base_url('inventario/cadastraEmprestimo');?>" class="float-right">Cadastrar&nbsp;<span class="glyphicon glyphicon-plus" style="font-size: 25px"></span></a></legend>
                <form>
                    <?php
                        
                        echo '<div class="col-md-6">';
                        echo form_label('Equipamento');
                        $equipamentosOpc = [''=>''];
                        foreach($equipamentos as $equipamento){
                            $equipamentosOpc[$equipamento['id_equipamento']] = '('.$equipamento['numero_serie'].') '.$equipamento['tipo_equipamento'].' '.$equipamento['nome_marca'].'('.$equipamento['modelo'].') ['.$equipamento['unidade_nome'].']';
                        }
                        echo form_dropdown('equipamentos',$equipamentosOpc,'','class="form-control" id="equipamentos"');
                    echo '</div>';

                    echo '<div class="col-md-6">';
                        echo form_label('Usuarios');
                        $usuariosOpc = [''=>''];
                        foreach($usuarios as $usuario){
                            $usuariosOpc[$usuario['cd_usuario']] = '('.$usuario['matricula_usuario'].') '.$usuario['nome_usuario'].' ('.$usuario['nome_estado'].')';
                        }

                        echo form_dropdown('usuarios',$usuariosOpc,'','class="form-control" id="usuarios"');
                    echo '</div>';
                        
                        echo '<div class="row"></div>';
                        
                        echo '<div class="col-md-12">';
                            $btnInfo = ['name'=>'pesquisaEmprestimo', 'id'=>'btnPesq', 'class'=>'btn btn-primary form-control float-right', 'content'=>'Pesquisar'];
                            echo form_button($btnInfo);
                        echo '</div>';
                    ?>
                </form>
            <!--</div>-->
        </div>
        
        <div class="row"></div>
        <br>
        
        
        
    </div>
    <script type="text/javascript" src="<?php echo base_url("assets/js/inventario/emprestimo/cadastraEmprestimo.js") ?>"></script>