<?php
//    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    echo link_tag(['href' => 'assets/css/rh-usuario/cadastra_faltas.css','rel' => 'stylesheet','type' => 'text/css']);
    echo link_tag(['href' => 'assets/css/dataTable/jquery.dataTables.css','rel' => 'stylesheet','type' => 'text/css']);
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/dataTable/jquery.dataTables.js") ?>"></script>

<div class="col-lg-offset-1 col-md-10">

    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
        <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>
        <li class="active">Cadastro de faltas</li>
    </ol>

    <div id="divMain">
    <?php
        echo $this->session->flashdata('statusOperacao');

        $totRegistro = count($valeTransporte);

        $titulo = 'Cadastra Falta';
        $nome = (isset($nome))? $nome: false;
        $status = (isset($status))? $status: false;

        echo '<div id="formPesq">';
            $data = array('class'=>'pure-form','id'=>'pesquisar');
//            echo form_open($data);
                echo form_fieldset($titulo, $attributes);

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
//            echo form_close();
        echo '</div>';
    ?>

        </div>
        
        <div class="row"></div>
        
        <div id="previewArquivo" class="well col-md-12">
            <form id="faltasForm" action="<?php echo base_url('/rh-usuario/faltas/salvaFalta');?>"  method="POST">
                
                <input id="mesFalta" type="hidden" name="mesFalta" value="">
                <input id="tableName" type="hidden" name="nomeDaTabela" value="rh_faltas">
                <input id="regionalValue" type="hidden" name="regionalValue" value="">
                <input id="pageAnswer" type="hidden" name="paginaRetorno" value="<?php echo base_url('rh-usuario/faltas/cadastraFalta'); ?>">
                
                <div>
                    <!--<a href="#"><span id="baixaArquivo" class="glyphicon glyphicon-download-alt icone pull-right"></span></a>-->
                    <a href="#"><span id="salvaFalta" class="glyphicon glyphicon-floppy-disk icone pull-right"></span></a>
                </div>
                <!--<a href="#"><span id="salvaArquivo" class="glyphicon glyphicon-floppy-save pull-right"></span></a>-->
                <table id="tabela" class="table">
                    <thead>
                        <tr>
                            <th class="matricula">Matricula</th>
                            <th>Nome</th>
                            <th class="dia">Dias</th>
                            <th class="dia"><span class="dataExtra glyphicon glyphicon-plus"></span></th>
                            <th class="dia"><span class="dataExtra glyphicon glyphicon-minus"></span></th>
                            <th class="total">Total</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabela"></tbody>
                </table>
            </form>
            <div id="paginacao"></div>
        </div>
        
    </div>
</div>

<script>
    var urlAjaxFaltas = '<?php echo base_url('rh-usuario/ajaxUsuario/retornaUsuarios')?>';
    var urlCadastrafaltas = '<?php echo base_url('/rh-usuario/faltas/teste');?>';
</script>
<script type="text/javascript" src="<?php echo base_url("assets/js/rh-usuario/faltas/cadastro_faltas_view.js") ?>"></script>