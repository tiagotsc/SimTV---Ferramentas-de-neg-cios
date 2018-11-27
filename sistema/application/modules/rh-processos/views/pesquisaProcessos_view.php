<?php
    echo link_tag(['href' => 'assets/css/pagionation/pagination.css','rel' => 'stylesheet','type' => 'text/css']);
    echo link_tag(['href' => 'assets/css/rh-processos/pesquisaProcessos_view.css','rel' => 'stylesheet','type' => 'text/css']);
    echo link_tag(['href' => 'assets/css/dataTable/jquery.dataTables.css','rel' => 'stylesheet','type' => 'text/css']);
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui/jquery-ui.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/dataTable/jquery.dataTables.js") ?>"></script>

<div class="col-md-12">
    <div id="pesquisa" class="col-md-offset-1 col-md-10">
        
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>            
            <li class="active">Pesquisar Processos</li>
        </ol>
        
        <legend>Pesquisa Processo<a href="<?php echo base_url('rh-processos/rhProcessos/cadastrar');?>" class="pull-right cadastrar">Cadastrar&nbsp<span class="glyphicon glyphicon-plus pull-right cadastrar"></span></a></legend>
        
        <br>
        
        <?php include_once 'editaProcesso_modal.php'?>
        
        <form>
            
            <div class="col-md-4">
                <label>Reclamante</label>
                <input class="form-control" type="text" placeholder="Reclamante">
            </div>
            
            <div class="col-md-4">
                <label>N. Processo</label>
                <input class="form-control" type="text" placeholder="N. Processo">
            </div>
            
            <div class="col-md-4">
                <label>Fase Processual</label>
                <input class="form-control" type="text" placeholder="Fase Processual">
            </div>
            
<!--            <div class="col-md-4">
                <input class="form-control" type="text" placeholder="">
            </div>-->
            
            <div class="roll"></div>
            <br><br><br>
            
            <div class="col-md-2 pull-right">
                <input type="button" value="Pesquisar" class="form-control btn-primary">
            </div>
        </form>
    </div>
    
    <div class="row"></div>
    
    <br><br>
    
    <div id="resultadoPesquisa" class="col-md-offset-1 col-md-10 well">
        <table id="tableResultado" class="table">
            <thead>
                <tr>
                    <th>N. Processo</th>
                    <th>Nome</th>
                    <th>Ano</th>
                    <th>Empresa</th>
                    <th>Motivo da Acao</th>
                    <th>Fase Processual</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($processos as $processo){
                        echo '<tr>';
                            echo '<td>'.$processo['numero_processo'].'</td>';
                            echo '<td>'.$processo['nome_colaborador'].'</td>';
                            echo '<td>'.$processo['data_processo'].'</td>';
                            echo '<td>'.$processo['nome'].'</td>';
                            echo '<td>'.$processo['motivo_processo'].'</td>';
                            echo '<td>'.$processo['fase_processo'].'</td>';
//                            echo '<td><a href="#" class="modalOpen" onclick=editaProcesso{"'.$processo['id_processo'].'"}><span class="glyphicon glyphicon-pencil"></span></a></td>';
                            echo '<td><a href="#" class="modalOpen" onclick=teste()><span class="glyphicon glyphicon-pencil"></span></a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url("assets/js/rh-processo/pesquisaProcesso_view.js") ?>"></script>

<!--<script>
//    var editaProcesso = '<?php echo base_url('rh-processos/ajaxProcesso/editaProcesso')?>';
    var t = 'teste';
</script>-->