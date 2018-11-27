<?php
    echo link_tag(['href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css']);
    echo link_tag(['href' => 'assets/css/pagionation/pagination.css','rel' => 'stylesheet','type' => 'text/css']);
    echo link_tag(['href' => 'assets/css/rh-beneficio/compra_beneficio.css','rel' => 'stylesheet','type' => 'text/css']);
    echo link_tag(['href' => 'assets/css/dataTable/jquery.dataTables.css','rel' => 'stylesheet','type' => 'text/css']);
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui/jquery-ui.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/dataTable/jquery.dataTables.js") ?>"></script>

    <div class="col-lg-offset-1 col-md-10">
        
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>            
            <li class="active">Compra beneficio alelo</li>
        </ol>
        
        <div id="divMain">
            <?php
                echo $this->session->flashdata('statusOperacao');
                
                $totRegistro = count($valeTransporte);
            
                $titulo = 'Comprar beneficio alelo';
                $nome = (isset($nome))? $nome: false;
                $status = (isset($status))? $status: false;
                
                echo '<div id="formPesq">';
                    $data = array('class'=>'pure-form','id'=>'pesquisar');
                        echo form_fieldset($titulo.$botaoCadastrar, $attributes);

                            echo '<div class="row"></div>';
                            
                            
                            echo '<div class="col-offset-1 col-md-10">';
                                
                                foreach($valorBeneficio as $vb){
                                    echo '<input type="hidden" id="valor'.$vb['tipo'].'" value="'.$vb['valor'].'"></input>';
                                }
                            
                                echo '<div class="col-md-3">';
                                    echo form_label('Beneficio');
                                    $beneficioOpc = [''=>'', '2'=>'Alimentacao', '1'=>'Refeicao'];
                                    echo form_dropdown('opcVale',$beneficioOpc,'','id="tipoBeneficio" class="form-control"');
                                echo '</div>';
                                
                                echo '<div id="regional" class="col-md-3">';
                                    $options = ['' => ''];
                                    foreach($unidades as $unidade){
                                        $options[$unidade['cd_razao_social']] = $unidade['nome'];
                                    }
                                    echo form_label('Regional', 'regional');
                                    echo form_dropdown('regional', $options, $cd_unidade, 'id="razao_social" class="form-control"');
                                echo '</div>';
                                
                                echo '<div id="unidades" class="col-md-3">';
                                    echo form_label('Unidade','unidade');
                                    echo '<select id="unidadeOpc" class="form-control"></select>';
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
            
                <div  id="previewArquivo" class="row col-md-12 marginTop10">
                    <div>                        
                        <div class="row well col-md-12">
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
                        
                        <div class="row"></div>
                        
                        <div class="well col-md-12">
                            <form id="geraArquivo" action="<?php echo base_url('rh-beneficio/beneficio/geraArquivoAlelo');?>"  method="POST">
                                <input id="mesFalta" type="hidden" name="mesCompraBeneficio" value="">
                                <input id="regionalValue" type="hidden" name="regionalValue" value="">
                                <input id="tableName" type="hidden" name="nomeDaTabela" value="rh_faltas_alelo">
                                <input id="pageAnswer" type="hidden" name="paginaRetorno" value="<?php echo base_url("rh-beneficio/beneficio/compraValeAlelo")?>">
                                <input id="opcBeneficio" type="hidden" name="opcBeneficio" value="">
                                <input id="razaoSocial" type="hidden" name="razaoSocial" value="">
                                <input id="nomeDaTabela" type="hidden" name="nomeDaTabela" value="rh_faltas_alelo">
                                
<!--                                <div id="tes" class="pull-right">
                                    <label>Divisao Arquivo</label>
                                    <input id="divisaoCompra" class="form-control" type="text" name="divisaoCompra">
                                </div>-->
                                
                                <div>
                                    <a href="#"><span id="salvaFalta" class="glyphicon glyphicon-floppy-disk icone pull-left"></span></a>
                                    <a href="#"><span id="montaArquivo" class="glyphicon glyphicon-download-alt icone pull-left"></span></a>
                                </div>
                                
                                <div class="row"></div>
                                <br>
                                
                                <div class="table-responsive">
                                    <table id="paginaca" data-order='[[ 1, "asc" ]]' data-page-length='10'>
                                        <thead>
                                            <tr>
                                                <th class="matricula">Matricula</th>
                                                <th class="nome">Nome</th>
                                                <th class="dia">Dias</th>
                                                <th class="dia"><span class="dataExtra glyphicon glyphicon-plus"></span></th>
                                                <th class="dia"><span class="dataExtra glyphicon glyphicon-minus"></span></th>
                                                <th id="opcBeneficio" title="">Beneficio<span id="info" class="glyphicon glyphicon-exclamation-sign pull-up"></span></th>
                                                <th class="total">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="corpoTabela">
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
    </div>
                        
<script>
    var salvaFalta = '<?php echo base_url('rh-usuario/faltas/salvaFalta'); ?>';
    var geraArquivo = '<?php echo base_url('rh-beneficio/beneficio/geraArquivoAlelo'); ?>';
    var urlAjaxAlelo = '<?php echo base_url('rh-beneficio/ajaxBeneficio/dadosAlelo'); ?>';
    var urlUnidades = '<?php echo base_url('rh-beneficio/ajaxBeneficio/dadosUnidade'); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url("assets/js/rh-beneficio/geraArquivoAlelo.js") ?>"></script>