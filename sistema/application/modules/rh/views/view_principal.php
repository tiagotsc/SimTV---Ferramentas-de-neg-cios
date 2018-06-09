            <div class="col-md-9 col-sm-8">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li class="active">Recursos Humanos</li>
                </ol>
                <div id="divMain">
                    <div class="row">
                    <?php
                    echo '<div class="col-md-12">';
                    echo $this->session->flashdata('statusOperacao');
                    echo '</div>';
                    
                    ?>
                       <!-- <p>Gerenciamento de telef&ocirc;nia</p>
                        <p>Selecione a op&ccedil;&atilde;o ao lado para o que deseja</p>    -->
                    </div>  
                </div>
                
            </div>
    
