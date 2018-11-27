<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(['href' => 'assets/css/rh-beneficio/compra_beneficio.css','rel' => 'stylesheet','type' => 'text/css']);
    echo link_tag(['href' => 'assets/css/dataTable/jquery.dataTables.css','rel' => 'stylesheet','type' => 'text/css']);
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/dataTable/jquery.dataTables.js") ?>"></script>

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
//                    echo form_open($data);
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
    //                                echo form_input('mes_compra',$t,'id="feriados" type="number" class="form-control"');
                                    echo '<input id="mesCompraBeneficio" type="number" class="form-control" value=".$t." name="mesCompraBeneficio"';
                                echo '</div>';

    //                            echo '<div class="col-md-1 marginTop25">';
    //                                echo form_submit("btn_gerar","Gerar", 'class="btn btn-primary pull-right"');
    //                            echo '</div>';
                            echo '</div>';
                        echo '</div>';    
                        echo form_fieldset_close();
//                    echo form_close();
                echo '</div>';
            ?>
            
            
                <div id="previewArquivo" class="col-md-12">
                    <div id="exibicaoFeriados" class="well col-md-12 marginTop10">

                    </div>
                    <div class="well col-md-12">
                        <p>
                            <div class="col-md-5">
                                <strong>Beneficios neste arquivo: </strong><strong id="totalBeneficiosCompra"></strong>
                            </div>

                            <div class="col-md-5">
                                <strong>Valor total da compra: </strong>
                                <strong id="valorTotalArquivo"></strong>
                            </div>
                        </p>
                    </div>

                    <div class="well col-md-12" id="dadosColaboradores">
                        <form id="geraArquivo" action="<?php echo base_url('rh-beneficio/beneficio/geraArquivoVt');?>" method="POST">
                            <input id="mesFalta" type="hidden" name="mesFalta" value="">
                            <input id="tableName" type="hidden" name="nomeDaTabela" value="rh_faltas_VT">
                            <input id="regionalValue" type="hidden" name="regionalValue" value=""></input>
                            <input id="pageAnswer" type="hidden" name="paginaRetorno" value="<?php echo base_url("rh-beneficio/beneficio/compraValeTransporte");?>">
                            <!--<input type="button" id="t" value="t">-->
                            <div>
                                <a href="#"><span id="baixaArquivo" class="glyphicon glyphicon-download-alt icone pull-left"></span></a>
                                <a href="#"><span id="salvaFalta" class="glyphicon glyphicon-floppy-disk icone pull-left"></span></a>
                            </div>
                            <div class="table-responsive">
                                <table id="dados">
                                    <thead>
                                        <tr>
                                            <th class="matricula">Matricula</th>
                                            <th>Nome</th>
                                            <th class="dia">Dias</th>
                                            <th class="dia"><span class="dataExtra glyphicon glyphicon-plus"></span></th>
                                            <th class="dia"><span class="dataExtra glyphicon glyphicon-minus"></span></th>
                                            <th class="passagem">Valor</th>
                                            <th class="total">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="corpoTabela"></tbody>
                                </table>
                            </div>
                        </form>
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