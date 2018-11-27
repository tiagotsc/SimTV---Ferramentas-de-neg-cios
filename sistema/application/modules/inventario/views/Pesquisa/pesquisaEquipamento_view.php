<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

    <div class="col-lg-offset-1 col-md-10">
        
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh')?>">Inventario</a></li>            
            <li class="active">Pesquisa Equipamento</li>
        </ol>
        <div id="divMain">
            
        </div>