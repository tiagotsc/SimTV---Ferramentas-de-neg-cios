<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

    
    <div class="col-lg-offset-1 col-md-10" style="margin-top: 50px">
        
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>
            <li class="active">Cadastra vale transporte</li>
        </ol>
        
        
        <?php
            echo $this->session->flashdata('statusOperacao');

            $data = array('id'=>'formControl');
            $d = array('class'=>'form-control');

            echo form_fieldset('Cadastro em lote', $data);

                echo '<div class="col-md-offset-1 col-md-10 ">';


                echo form_open_multipart($url_model,$d);

                    $data = array('name'=>'userfile','id'=>'userfile'/*, 'class'=>'form-control'*/);
                    echo form_upload($data);
                    echo '<br>';
                    echo form_submit('botao','Enviar');
                echo form_close();

                echo '</div>';

            echo form_fieldset_close();
        
        ?>
            
    </div>


























        <?php
//            
//        
//            echo form_open();
//        
//                echo form_open_multipart();
//
//                echo form_upload();
//
//                echo form_submit('botao','Enviar');
//            echo form_close();
//        
//        
        ?>