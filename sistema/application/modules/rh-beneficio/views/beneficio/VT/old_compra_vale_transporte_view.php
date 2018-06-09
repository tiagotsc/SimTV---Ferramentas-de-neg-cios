<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(['href' => 'assets/css/rh-beneficio/compra_beneficio.css','rel' => 'stylesheet','type' => 'text/css']);
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

    <div class="col-lg-offset-1 col-md-10">
        
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>            
            <li class="active">Compra vale transporte</li>
        </ol>
        
        <div id="divMain">
            <?php
                echo $this->session->flashdata('statusOperacao');
                
                $totRegistro = count($valeTransporte);
            
                $titulo = 'Compra Vale Transporte';
                $nome = (isset($nome))? $nome: false;
                $status = (isset($status))? $status: false;
                
                echo '<div id="formPesq">';
                    $data = array('class'=>'pure-form','id'=>'pesquisar');
                        echo form_fieldset($titulo.$botaoCadastrar, $attributes);

                            echo '<div class="row">';
                                echo '<div id="regional" class="col-md-4">';
                                    $options = array('' => '', '15' => 'Icarai');
                                    foreach($unidade as $uni){
                                            $options[$uni->cd_unidade] = htmlentities($uni->nome);
                                    }	
                                    echo form_label('Unidade', 'cd_unidade');
                                    echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                echo '</div>';

                                echo '<div id="mesCompra" class="col-md-2">';
                                    echo form_label('Mes', 'mes_compra');
                                    echo '<input id="mesCompraBeneficio" type="number" class="form-control" value=".$t." name="mesCompraBeneficio"';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';    
                        echo form_fieldset_close();
                echo '</div>';
            ?>
                
                <div id="exibicaoFeriados" class="well col-md-12 marginTop10">
                    
                </div>
            
                <div id="previewArquivo" class="col-md-12">
                    <div>                        
                        <div class="well col-md-12">
                            <p>
                                <div class="col-md-5">
                                    <strong>Beneficios neste arquivo: </strong><strong id="totalBeneficiosCompra"></strong>
                                </div>
                                
                                <div class="col-md-5">
                                    <strong>Valor total da compra: </strong>
                                    <strong id="valorTotalArquivo">
                                        
                                    </strong>
                                </div>
                            </p>
                        </div>
                        
                        <div class="well col-md-12">
                            <?php
                            $data = array('id'=>'geraArquivo');
                                echo form_open('rh-beneficio/beneficio/geraArquivoVt',$data);
                                
                                    echo '<input id="mesFalta" type="hidden" name="mesFalta" value="">';
                                    echo '<input id="tableName" type="hidden" name="nomeDaTabela" value="rh_faltas_VT">';
                                    echo '<input id="regionalValue" type="hidden" name="regionalValue" value=""></input>';
                                    echo '<input id="pageAnswer" type="hidden" name="paginaRetorno" value="'.base_url("rh-beneficio/beneficio/compraValeTransporte").'">';
                                    
                                    echo '<div>';
                                        echo '<a href="#"><span id="baixaArquivo" class="glyphicon glyphicon-download-alt icone pull-right"></span></a>';
                                        echo '<a href="#"><span id="salvaFalta" class="glyphicon glyphicon-floppy-disk icone pull-right"></span></a>';
                                    echo '</div>';
                                    echo '<div class="table-responsive">';
                                        echo '<table class="table">';
                                            echo '<thead>';
                                                echo '<tr>';
                                                    echo '<th class="matricula">Matricula</th>';
                                                    echo '<th>Nome</th>';
                                                    echo '<th class="dia">Dias</th>';
                                                    echo '<th class="dia"><span class="dataExtra glyphicon glyphicon-plus"></span></th>';
                                                    echo '<th class="dia"><span class="dataExtra glyphicon glyphicon-minus"></span></th>';
                                                    echo '<th class="passagem">Valor</th>';
                                                    echo '<th class="total">Total</th>';
                                                echo '</tr>';
                                            echo '</thead>';
                                            echo '<tbody id="corpoTabela">';
    //                                            
                                            echo '</tbody>';
                                        echo '</table>';
                                    echo '</div>';
                                echo form_close();
                            ?>
                        </div>
                    </div>
                </div>
        
        </div>
    </div>
    
    <script>
        var salvaFalta = '<?php echo base_url('rh-usuario/faltas/salvaFalta'); ?>';
        var geraArquivo = '<?php echo base_url('rh-beneficio/beneficio/geraArquivoVt');?>';
        var urlAjaxPassagem = '<?php echo base_url(); ?>'+'rh-beneficio/ajaxBeneficio/dadosPassagem';
    </script>
    
    <script type="text/javascript" src="<?php echo base_url("assets/js/rh-beneficio/geraArquivoVt.js") ?>"></script>