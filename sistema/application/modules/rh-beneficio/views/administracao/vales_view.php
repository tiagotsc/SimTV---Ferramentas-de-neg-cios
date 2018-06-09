<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>

    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js"); ?>"></script>
        
    <div class="col-lg-offset-1 col-md-10">
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh') ?>">RH</a></li>            
            <li class="active">Vales</li>
        </ol>
    </div>
    <?php include_once 'cadastraVale_modal.php';?>
    <?php echo $this->session->flashdata('statusOperacao');?>

<div class="row"></div>

<div class="col-md-offset-1 col-md-10 marginTop25">
    <legend>Gestao de Vales<a href="#" id="cadastraVale" class="float-right"><span style="font-size: 30px" class="glyphicon glyphicon-plus"></span></a></legend>
    <div class="well">
        <form>
            <table class="table table-striped">
                <thead>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Data Cadastro</th>
                    <th>Data Desativacao</th>
                </thead>

                <tbody>
                    <?php
                        foreach($vales as $vale){

                            echo '<tr>';
                                echo '<td>'.$vale["tipo"].'</td>';
                                echo '<td class="money">R$ '.$vale["valor"].'</td>';
                                echo '<td>'.date("d/m/Y", strtotime($vale["data_cadastro"])).'</td>';
                                echo '<td>'.(($vale['data_desativacao'] != NULL)?$vale['data_desativacao']:"--/--/----").'</td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

<script type="text/javascript">
    
    $('.money').mask('R$ 00,00');
    
    $('#cadastraVale').click(function(){
        $('#cadastra_vale').modal('toggle');
    });
    
    $('#btn_cadastro').click(function(e){
        e.preventDefault();
        $('.money').unmask();
        $('#cadastro_vale').submit();
    });
    
</script>