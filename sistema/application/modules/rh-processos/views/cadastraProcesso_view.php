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
            <li><a href="<?php echo base_url('rh-processos/rhProcessos/pesquisar')?>">Pesquisa Processo</a></li>
            <li class="active">Cadastrar Processos</li>
        </ol>
        
        <?php echo $this->session->flashdata('statusOperacao');?>
        
        <div class="roll"></div>
        
        <legend>Cadastro de Processos<a  href="<?php echo base_url('rh-processos/rhProcessos/pesquisar');?>" class="pull-right cadastrar">&nbsp<span class="glyphicon glyphicon-arrow-left cadastrar"></span>Voltar</a></legend>
        
        <br>
        
        <form method="POST" action="<?php echo base_url('rh-processos/rhProcessos/cadastrarProcessos');?>" id="cadastraProcesso">
            
            <p class="legenda">Colaborador</p>

            <div class="col-md-11 capsula">
                <div class="col-md-3">
                    <label>N. Matricula</label>
                    <input class="form-control" id="numMatricula" name="matricula_colaborador" type="text" placeholder="N. Matricula">
                </div>

                <div class="col-md-4">
                    <label>Nome</label>
                    <input class="form-control" id="nome" name="nome_colaborador" type="text" placeholder="Nome">
                </div>

                <div class="row"></div>

                <div class="col-md-4">
                    <label>Cargo</label>
                    <!--<input class="form-control" id="cargo" name="cargo" type="text" placeholder="Cargo">-->
                    <select class="form-control" id="cargo" name="cargo_colaborador">
                        <option value=""></option>
                        <?php
                            foreach($cargos as $cargo){
                                echo '<option value="'.$cargo['cd_cargo'].'">'.htmlentities($cargo['nome']).'</option>';
                            }
                        ?>
                    </select>

                </div>

                <div class="col-md-3">
                    <label>Setor</label>
                    <!--<input class="form-control" id="setor" name="setor" type="text" placeholder="Setor">-->
                    <select class="form-control" id="setor" name="setor_colaborador">
                        <option value=""></option>
                        <?php
                            foreach($setores as $setor){
                                echo '<option value="'.$setor['cd_departamento'].'">'.htmlentities($setor['nome_departamento']).'</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Unidade de Negocio</label>
                    <!--<input class="form-control" id="unidade" name="unidade" type="text" placeholder="Unidade de Negocio">-->
                    <select class="form-control" id="unidade" name="unidade_colaborador">
                        <option value=""></option>
                        <?php
                            foreach($unidades as $unidade){
                                echo '<option value="'.$unidade['cd_unidade'].'">'.htmlentities($unidade['nome']).'</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label>Periodo Trabalhado</label>
                    <div class="col-md-3">
                        <div><label>Inicio</label><input class="form-control data" name="inicio_periodo_trabalho" type="text"></div>
                    </div>

                    <div class="col-md-3">
                        <div><label>Fim</label><input class="form-control data" name="fim_periodo_trabalho" type="text"></div>
                    </div>
                </div>


            </div>

            <div class="row"></div><br>

            <p class="legenda">Juridico</p>

            <div class="col-md-11 capsula">

                <div class="col-md-3">
                    <label>N. Processo</label>
                    <input class="form-control" name="numero_processo" type="text" placeholder="N. Processo">
                </div>

                <div class="col-md-3">
                    <label>Motivo da Acao</label>
                    <input class="form-control" name="motivo_processo" type="text" placeholder="Motivo da Acao">
                </div>

                <div class="col-md-3">
                    <label>Vara</label>
                    <input class="form-control" name="vara_processo" type="text" placeholder="Vara">
                </div>

                <div class="col-md-3">
                    <label>1&ordf Reclamada</label>
                    <input class="form-control" name="primeira_reclamada_processo" type="text" placeholder="Reclamada">
                </div>

                <div class="col-md-3">
                    <label>2&ordf Reclamada</label>
                    <input class="form-control" name="segunda_reclamada_processo" type="text" placeholder="Reclamada">
                </div>

                <div class="col-md-3">
                    <label>Andamento</label>
                    <input class="form-control" name="andamento_processo" type="text" placeholder="Andamento">
                </div>

                <div class="col-md-3">
                    <label>Valor da Causa</label>
                    <input class="form-control money" name="valor_causa_processo" type="text" placeholder="Valor da Causa">
                </div>

                <div class="col-md-3">
                    <label>Principal</label>
                    <input class="form-control" name="principal_processo" type="text" placeholder="Principal">
                </div>

                <div class="col-md-3">
                    <label>INSS</label>
                    <input class="form-control" name="INSS_processo" type="text" placeholder="INSS">
                </div>

                <div class="col-md-3">
                    <label>IR</label>
                    <input class="form-control" name="IR_processo" type="text" placeholder="IR">
                </div>

                <div class="col-md-3">
                    <label>Valor da Provisao</label>
                    <input class="form-control money" name="valor_provisao_processo" type="number" placeholder="Valor da Provisao">
                </div>

                <div class="col-md-3">
                    <label>Prognostico</label>
                    <input class="form-control" name="prognostico_processo" type="text" placeholder="Prognostico">
                </div>

                <div class="col-md-3">
                    <label>Valor envolvido</label>
                    <input class="form-control money" name="valor_envolvido_processo" type="number" placeholder="Valor envolvido">
                </div>

                <div class="col-md-3">
                    <label>Valor de Contigencia</label>
                    <input class="form-control money" name="valor_contigencia_processo" type="number" placeholder="Valor de Contigencia">
                </div>

                <div class="col-md-3">
                    <label>Fase Processual</label>
                    <input class="form-control" name="fase_processo" type="text" placeholder="Fase Processual">
                </div>

                <div class="col-md-3">
                    <label>Objeto</label>
                    <input class="form-control" name="objeto_processo" type="text" placeholder="Objeto">
                </div>

                <div class="col-md-3">
                    <label>Deposito</label>
                    <input class="form-control" name="deposito_processo" type="text" placeholder="Deposito">
                </div>

                <div class="col-md-3">
                    <label>Data</label>
                    <input class="form-control data" name="data_processo" type="text">
                </div>

                <div class="col-md-3">
                    <label>Valor Bloqueado</label>
                    <input class="form-control money" name="valor_bloqueado_processo" type="number" placeholder="Valor Bloqueado">
                </div>

                <div class="col-md-3">
                    <label>Acordo</label>
                    <input class="form-control" name="acordo_processo" type="text" placeholder="Acordo">
                </div>

                <div class="col-md-6">
                    <label>Outros</label>
                    <textarea class="form-control" name="outros_processo" maxlength="250" rows="4" cols="50"></textarea>
                </div>

            </div>
            
        </form>
        
        <div class="row"></div><br>
        
        <div class="col-md-2 pull-right">
            <input type="button" id="btnCadastra" value="Cadastrar" class="form-control btn-primary">
        </div>
    </div>
</div>

<script>
    var retornaInformacao = '<?php echo base_url('rh-processos/ajaxProcesso/retornaInformacoesColaborador')?>';
</script>

<script type="text/javascript" src="<?php echo base_url("assets/js/rh-processos/cadastraProcesso.js") ?>"></script>