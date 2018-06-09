<?php
echo link_tag(array('href' => 'assets/js/c3js/c3.css','rel' => 'stylesheet','type' => 'text/css'));
echo link_tag(array('href' => 'assets/css/css_dashboard.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url('assets/js/c3js/d3.v3.min.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/c3js/c3.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/jQuery.print.js')."'></script>";
?>
<style type="text/css">
/*.c3-xgrid-line line {
    stroke: blue;
}
.c3-xgrid-line.grid4 line {
    stroke: pink;
}
.c3-xgrid-line.grid4 text {
    fill: pink;
}*/

/*.c3-ygrid-line.grid800 line {
    stroke: green;
}
.c3-ygrid-line.grid800 text {
    fill: green;
}*/

/*################### RENTABILIZAÇÃO - INÍCIO #######################*/
/* Texto */
.c3-ygrid-line.grid4 text{
    stroke: #696969;
}

/* Linha */
.c3-ygrid-line.grid4 line{
    stroke: green;
}
/*################### RENTABILIZAÇÃO - FIM #######################*/

.cabecalhoDashboard{
    text-align: center;
}
</style>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Dashboard / Marketing</li>
                </ol>
                <div id="divMain">
                    <h3>Marketing</h3>
                    <div id="dashboards">
                    <?php if(in_array(1, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(1), $(this).verificaClick();">Rentabiliza&ccedil;&atilde;o</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#rentabilizacao').print()">Imprimir</a>
                            </div>
                            <div class="row dashCamPreechimento">
                                <div class="col-md-6">
                                <?php
                                echo form_label('Informe a meta', 'meta_rentabilizacao');
                       			$data = array('name'=>'meta_rentabilizacao', 'value'=>'','id'=>'meta_rentabilizacao', 'placeholder'=>'Digite a meta', 'class'=>'form-control');
                       			echo form_input($data);
                                ?>
                                </div>
                                <div class="col-md-6">
                                <?php
                                $options = array('' => '');		
                        		foreach($comoRentabilitacaoTela1 as $cRt){
                        			$options[$cRt->VALOR] = $cRt->EXIBICAO;
                        		}
                        		
                        		echo form_label('Selecione o m&ecirc;s/ano:', 'mes_ano_rentabilizacao');
                        		echo form_dropdown('mes_ano_rentabilizacao', $options, '', 'id="mes_ano_rentabilizacao" class="form-control"');
                                ?>
                                </div>
                            </div>
                            <div id="rentabilizacao">
                                <div class="row">
                                    <div class="divDashboard" id="chart1"></div>
                                </div>
                                <div class="row">
                                    <div id="rentabilizacaoTela2" class="col-md-6">             
                                    </div>
                                    <div id="rentabilizacaoTela3" class="col-md-6">                                
                                    </div>
                                </div>
                            </div>
                          </div>
                    <?php } ?>
                    <?php if(in_array(3, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(3), $(this).verificaClick();">Base de assinantes</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#base_assinantes').print()">Imprimir</a>
                            </div>
                            <div id="base_assinantes">
                                <!-- ###################### Base assinantes consolidado - INÍCIO ###################### -->
                                <div class="row">
                                    <h3 class="cabecalhoDashboard"><strong>Resumo da base com sinal e sem sinal</strong></h3> 
                                    <div class="col-md-8">                      
                                        <table class="table">
                                            <thead>
                                                <tr><th class="tdVerde" colspan="7"><strong>BASE COM SINAL</strong></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="tdVerdeClaro">
                                                    <td class="tdLinhaDireita"></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-2 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% DESVIO</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-1 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% DESVIO</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y'); ?></strong></td>
                                                    <td><strong>% DESVIO</strong></td>
                                                </tr>
                                                <?php 
                                                foreach($baseAssinantesConsolidadoComSinal as $bAcCs){
                                                    $classTr = ($bAcCs->PRODUTO == 'TOTAL')? 'tdVerdeClaro': 'tdBegeClaro'; 
                                                    $classUltPenul = ($bAcCs->PRODUTO != 'TOTAL')? 'tdVerdeClaro': '';
                                                    $espacamento = ($bAcCs->PRODUTO == 'TOTAL')? '<tr><td colspan="7"></td></tr>': '';
                                                    
                                                    if($bAcCs->PRODUTO == 'TOTAL'){
                                                        if($bAcCs->DIAS_ATRAS_1 >= $metaBase){
                                                            $meta = '<span title="Meta prevista '.(int)$metaBase.'" class="glyphicon glyphicon-thumbs-up meta_ok" aria-hidden="true"></span>';
                                                        }else{
                                                            $meta = '<span title="Meta prevista '.(int)$metaBase.'" class="glyphicon glyphicon-thumbs-down meta_no" aria-hidden="true"></span>';
                                                        }
                                                    }else{
                                                        $meta = '';
                                                    }
                                                    
                                                    echo $espacamento;
                                                ?>
                                                <tr class="<?php echo $classTr;?>">
                                                    <td class="tdLinhaDireita"><strong><?php echo $bAcCs->PRODUTO; ?></strong></td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcCs->DIAS_ATRAS_3, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcCs->DIF_PORC_3; ?>%</td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcCs->DIAS_ATRAS_2, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcCs->DIF_PORC_2; ?>%</td>
                                                    <td class="tdLinhaDireita <?php echo $classUltPenul; ?>"><?php echo number_format($bAcCs->DIAS_ATRAS_1, 0, ',', '.'); ?>&nbsp;<?php echo $meta;?></td>
                                                    <td class="<?php echo $classUltPenul;?>"><?php echo (float)$bAcCs->DIF_PORC_1; ?>%</td>
                                                </tr>
                                                <?php 
                                                } 
                                                ?>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr><th class="tdMarrom" colspan="6">BASE SEM SINAL<br /> <span style="font-size: 13px;"><?php echo date("d-M"); ?></span></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="tdMarromClaro">
                                                    <td class="tdLinhaDireita"></td>
                                                    <td class="tdLinhaDireita"><strong>At&eacute; 5 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>D&ecirc; 6 &agrave; 15 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>D&ecirc; 16 &agrave; 59 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>Acima 60 dias</strong></td>
                                                    <td><strong>Total</strong></td>
                                                </tr>
                                                <?php
                                                foreach($baseAssinantesConsolidadoSemSinal as $bAcSs){
                                                    $total = $bAcSs->ATE5DIAS + $bAcSs->DE6A15DIAS + $bAcSs->DE16A59DIAS + $bAcSs->ACIMA60DIAS;
                                                    $classTr = ($bAcSs->PRODUTO == 'TOTAL')? 'tdMarromClaro': 'tdMarromBemClaro';
                                                    $espacamento = ($bAcSs->PRODUTO == 'TOTAL')? '<tr><td colspan="6"></td></tr>': '';
                                                    echo $espacamento;
                                                ?>
                                                <tr class="<?php echo $classTr;?>">
                                                    <td class="tdLinhaDireita"><strong><?php echo $bAcSs->PRODUTO; ?></strong></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAcSs->ATE5DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAcSs->DE6A15DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAcSs->DE16A59DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAcSs->ACIMA60DIAS; ?></td>
                                                    <td><?php echo number_format($total, 0, ',', '.'); ?></td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="cinzaEscuro"><th colspan="2"><strong>MOVIMENTA&Ccedil;&Atilde;O DA BASE</strong></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="cinzaMediano"><td colspan="2" style="text-align: left;"><strong>Sa&iacute;da</strong></td></tr>
                                                <?php 
                                                $totalSaida = 0;
                                                foreach($baseAssinantesConsolidadoMovimentacaoSaida as $bAcMs){ 
                                                ?>
                                                <tr class="cinzaClaro">
                                                    <td><?php echo $bAcMs->MOTIVO; ?></td>
                                                    <td><?php echo $bAcMs->QTD; ?></td>
                                                </tr>
                                                <?php 
                                                    $totalSaida += $bAcMs->QTD;
                                                } 
                                                ?>
                                                <tr class="cinzaMediano"><td colspan="2" style="text-align: left;"><strong>Entrada</strong></td></tr>
                                                <?php 
                                                $totalEntrada = 0;
                                                foreach($baseAssinantesConsolidadoMovimentacaoEntrada as $bAcMe){ 
                                                ?>
                                                <tr class="cinzaClaro">
                                                    <td><?php echo $bAcMe->MOTIVO; ?></td>
                                                    <td><?php echo $bAcMe->QTD; ?></td>
                                                </tr>
                                                <?php 
                                                    $totalEntrada += $bAcMe->QTD;
                                                } 
                                                ?>
                                                <tr class="cinzaMediano">
                                                    <td style="text-align: left;"><strong>Total</strong></td>
                                                    <td><strong><?php echo ($totalSaida > $totalEntrada)? $totalSaida-$totalEntrada: $totalEntrada-$totalSaida; ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row"></div>
                                </div>
                                <!-- ###################### Base assinantes consolidado - FIM ###################### -->
                                
                                <!-- ###################### Base assinantes - Individual - INÍCIO ###################### -->
                                <div class="row">
                                    <h3 class="cabecalhoDashboard"><strong>Resumo da base com sinal e sem sinal - Individual</strong></h3> 
                                    <div class="col-md-8">                      
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="tdVerde"><th colspan="7"><strong>BASE COM SINAL</strong></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="tdVerdeClaro">
                                                    <td class="tdLinhaDireita"></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-2 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% Desvio</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-1 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% Desvio</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y'); ?></strong></td>
                                                    <td><strong>% Desvio</strong></td>
                                                </tr>
                                                <?php 
                                                foreach($baseAssinantesComSinalIndividual as $bAcSi){  
                                                    $classTr = ($bAcSi->PRODUTO == 'TOTAL')? 'tdVerdeClaro': 'tdBegeClaro'; 
                                                    $classUltPenul = ($bAcSi->PRODUTO != 'TOTAL')? 'tdVerdeClaro': '';
                                                    $espacamento = ($bAcSi->PRODUTO == 'TOTAL')? '<tr><td colspan="7"></td></tr>': '';
                                                    echo $espacamento;
                                                ?>
                                                <tr class="<?php echo $classTr;?>">
                                                    <td class="tdLinhaDireita"><strong><?php echo $bAcSi->PRODUTO; ?></strong></td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcSi->DIAS_ATRAS_3, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcSi->DIF_PORC_3; ?>%</td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcSi->DIAS_ATRAS_2, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcSi->DIF_PORC_2; ?>%</td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcSi->DIAS_ATRAS_1, 0, ',', '.'); ?></td>
                                                    <td class="<?php echo $classUltPenul;?>"><?php echo (float)$bAcSi->DIF_PORC_1; ?>%</td>
                                                </tr>
                                                <?php 
                                                } 
                                                ?>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="tdMarrom"><th colspan="6">BASE SEM SINAL<br /> <span style="font-size: 13px;"><?php echo date("d-M"); ?></span></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="tdMarromClaro">
                                                    <td class="tdLinhaDireita"></td>
                                                    <td class="tdLinhaDireita"><strong>At&eacute; 5 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>D&ecirc; 6 &agrave; 15 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>D&ecirc; 16 &agrave; 59 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>Acima 60 dias</strong></td>
                                                    <td><strong>Total</strong></td>
                                                </tr>
                                                <?php
                                                    $total = 0;
                                                foreach($baseAssinantesSemSinalIndividual as $bAsSi){
                                                    $total = $bAsSi->ATE5DIAS + $bAsSi->DE6A15DIAS + $bAsSi->DE16A59DIAS + $bAsSi->ACIMA60DIAS;
                                                    $classTr = ($bAsSi->PRODUTO == 'TOTAL')? 'tdMarromClaro': 'tdMarromBemClaro';
                                                    $espacamento = ($bAsSi->PRODUTO == 'TOTAL')? '<tr><td colspan="6"></td></tr>': '';
                                                    echo $espacamento;
                                                ?>
                                                <tr class="<?php echo $classTr;?>">
                                                    <td class="tdLinhaDireita"><strong><?php echo $bAsSi->PRODUTO; ?></strong></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSi->ATE5DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSi->DE6A15DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSi->DE16A59DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSi->ACIMA60DIAS; ?></td>
                                                    <td><?php echo number_format($total, 0, ',', '.'); ?></td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="cinzaEscuro"><th colspan="2"><strong>MOVIMENTA&Ccedil;&Atilde;O DA BASE</strong></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="cinzaMediano"><td colspan="2" style="text-align: left;"><strong>Sa&iacute;da</strong></td></tr>
                                                <?php 
                                                $totalSaida = 0;
                                                foreach($baseAssinantesMovimentacaoSaidaIndividual as $bAmSi){ 
                                                ?>
                                                <tr class="cinzaClaro">
                                                    <td><?php echo $bAmSi->MOTIVO; ?></td>
                                                    <td><?php echo $bAmSi->QTD; ?></td>
                                                </tr>
                                                <?php 
                                                    $totalSaida += $bAmSi->QTD;
                                                } 
                                                ?>
                                                <tr class="cinzaMediano"><td colspan="2" style="text-align: left;"><strong>Entrada</strong></td></tr>
                                                <?php 
                                                $totalEntrada = 0;
                                                foreach($baseAssinantesMovimentacaoEntradaIndividual as $bAmEi){ 
                                                ?>
                                                <tr class="cinzaClaro">
                                                    <td><?php echo $bAmEi->MOTIVO; ?></td>
                                                    <td><?php echo $bAmEi->QTD; ?></td>
                                                </tr>
                                                <?php 
                                                    $totalEntrada += $bAmEi->QTD;
                                                } 
                                                ?>
                                                <tr class="cinzaMediano">
                                                    <td style="text-align: left;"><strong>Total</strong></td>
                                                    <td><strong><?php echo ($totalSaida > $totalEntrada)? $totalSaida-$totalEntrada: $totalEntrada-$totalSaida; ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row"></div>
                                </div>
                                <!-- ###################### Base assinantes - Individual - FIM ###################### -->
                                
                                <!-- ###################### Base assinantes - Filiado - INÍCIO ###################### -->
                                <div class="row">
                                    <h3 class="cabecalhoDashboard"><strong>Resumo da base com sinal e sem sinal - Filiado</strong></h3> 
                                    <div class="col-md-8">                      
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="tdVerde"><th colspan="7"><strong>BASE COM SINAL</strong></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="tdVerdeClaro">
                                                    <td class="tdLinhaDireita"></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-2 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% Desvio</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-1 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% Desvio</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y'); ?></strong></td>
                                                    <td><strong>% Desvio</strong></td>
                                                </tr>
                                                <?php 
                                                foreach($baseAssinantesComSinalFiliado as $bAcSf){ 
                                                    $classTr = ($bAcSf->PRODUTO == 'TOTAL')? 'tdVerdeClaro': 'tdBegeClaro'; 
                                                    $classUltPenul = ($bAcSf->PRODUTO != 'TOTAL')? 'tdVerdeClaro': '';
                                                    $espacamento = ($bAcSf->PRODUTO == 'TOTAL')? '<tr><td colspan="7"></td></tr>': '';
                                                    echo $espacamento;
                                                ?>
                                                <tr class="<?php echo $classTr;?>">
                                                    <td class="tdLinhaDireita"><strong><?php echo $bAcSf->PRODUTO; ?></strong></td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcSf->DIAS_ATRAS_3, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcSf->DIF_PORC_3; ?>%</td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcSf->DIAS_ATRAS_2, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcSf->DIF_PORC_2; ?>%</td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcSf->DIAS_ATRAS_1, 0, ',', '.'); ?></td>
                                                    <td class="<?php echo $classUltPenul;?>"><?php echo (float)$bAcSf->DIF_PORC_1; ?>%</td>
                                                </tr>
                                                <?php 
                                                } 
                                                ?>
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr><th class="tdMarrom" colspan="6">BASE SEM SINAL<br /> <span style="font-size: 13px;"><?php echo date("d-M"); ?></span></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="tdMarromClaro">
                                                    <td class="tdLinhaDireita"></td>
                                                    <td class="tdLinhaDireita"><strong>At&eacute; 5 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>D&ecirc; 6 &agrave; 15 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>D&ecirc; 16 &agrave; 59 dias</strong></td>
                                                    <td class="tdLinhaDireita"><strong>Acima 60 dias</strong></td>
                                                    <td><strong>Total</strong></td>
                                                </tr>
                                                <?php
                                                    $total = 0;
                                                foreach($baseAssinantesSemSinalFiliado as $bAsSf){
                                                    $total = $bAsSf->ATE5DIAS + $bAsSf->DE6A15DIAS + $bAsSf->DE16A59DIAS + $bAsSf->ACIMA60DIAS;
                                                    $classTr = ($bAsSf->PRODUTO == 'TOTAL')? 'tdMarromClaro': 'tdMarromBemClaro';
                                                    $espacamento = ($bAsSf->PRODUTO == 'TOTAL')? '<tr><td colspan="6"></td></tr>': '';
                                                    echo $espacamento;
                                                ?>
                                                <tr class="<?php echo $classTr;?>">
                                                    <td class="tdLinhaDireita"><strong><?php echo $bAsSf->PRODUTO; ?></strong></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSf->ATE5DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSf->DE6A15DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSf->DE16A59DIAS; ?></td>
                                                    <td class="tdLinhaDireita"><?php echo $bAsSf->ACIMA60DIAS; ?></td>
                                                    <td><?php echo number_format($total, 0, ',', '.'); ?></td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="cinzaEscuro"><th colspan="2"><strong>MOVIMENTA&Ccedil;&Atilde;O DA BASE</strong></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="cinzaMediano"><td colspan="2" style="text-align: left;"><strong>Sa&iacute;da</strong></td></tr>
                                                <?php 
                                                $totalSaida = 0;
                                                foreach($baseAssinantesMovimentacaoSaidaFiliado as $bAmS){ 
                                                ?>
                                                <tr class="cinzaClaro">
                                                    <td><?php echo $bAmS->MOTIVO; ?></td>
                                                    <td><?php echo $bAmS->QTD; ?></td>
                                                </tr>
                                                <?php 
                                                    $totalSaida += $bAmS->QTD;
                                                } 
                                                ?>
                                                <tr class="cinzaMediano"><td colspan="2" style="text-align: left;"><strong>Entrada</strong></td></tr>
                                                <?php 
                                                $totalEntrada = 0;
                                                foreach($baseAssinantesMovimentacaoEntradaFiliado as $bAmEf){ 
                                                ?>
                                                <tr class="cinzaClaro">
                                                    <td><?php echo $bAmEf->MOTIVO; ?></td>
                                                    <td><?php echo $bAmEf->QTD; ?></td>
                                                </tr>
                                                <?php 
                                                    $totalEntrada += $bAmEf->QTD;
                                                } 
                                                ?>
                                                <tr class="cinzaMediano">
                                                    <td style="text-align: left;"><strong>Total</strong></td>
                                                    <td><strong><?php echo ($totalSaida > $totalEntrada)? $totalSaida-$totalEntrada: $totalEntrada-$totalSaida; ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row"></div>
                                </div>
                                
                                <!-- ###################### Base assinantes - Consolidada por permissor - INÍCIO ###################### -->
                                <div class="row">
                                    <div class="col-md-8"><h3 class="cabecalhoDashboard"><strong>Resumo da base por permissor</strong></h3></div>
                                </div>
<?php 
$contLoopPer = 1;
$ultimoOperadora = '';
foreach($baseAssinantesConsolidadoPermissor as $bAcP){  
    if($bAcP->COD_OPERADORA != $ultimoOperadora){
?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table">
                                            <thead>
                                                <tr><th class="tdVerde" colspan="7"><strong><?php echo $bAcP->OPERADORA; ?> - BASE COM SINAL</strong></th></tr>
                                            </thead>
                                            <tbody>
                                                <tr class="tdVerdeClaro">
                                                    <td class="tdLinhaDireita"></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-2 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% DESVIO</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y', strtotime("-1 days")); ?></strong></td>
                                                    <td class="tdLinhaDireita"><strong>% DESVIO</strong></td>
                                                    <td class="tdLinhaDireita"><strong><?php echo date('d/m/Y'); ?></strong></td>
                                                    <td><strong>% DESVIO</strong></td>
                                                </tr>
                                                <?php 
    }
                                                    $classTr = ($bAcP->PRODUTO == 'TOTAL')? 'tdVerdeClaro': 'tdBegeClaro'; 
                                                    $classUltPenul = ($bAcP->PRODUTO != 'TOTAL')? 'tdVerdeClaro': '';
                                                    $espacamento = ($bAcP->PRODUTO == 'TOTAL')? '<tr><td colspan="7"></td></tr>': '';
                                                    
                                                    echo $espacamento;
                                                ?>
                                                <tr class="<?php echo $classTr;?>">
                                                    <td class="tdLinhaDireita"><strong><?php echo $bAcP->PRODUTO; ?></strong></td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcP->DIAS_ATRAS_3, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcP->DIF_PORC_3; ?>%</td>
                                                    <td class="tdLinhaDireita"><?php echo number_format($bAcP->DIAS_ATRAS_2, 0, ',', '.'); ?></td>
                                                    <td class="tdLinhaDireita"><?php echo (float)$bAcP->DIF_PORC_2; ?>%</td>
                                                    <td class="tdLinhaDireita <?php echo $classUltPenul; ?>"><?php echo number_format($bAcP->DIAS_ATRAS_1, 0, ',', '.'); ?></td>
                                                    <td class="<?php echo $classUltPenul;?>"><?php echo (float)$bAcP->DIF_PORC_1; ?>%</td>
                                                </tr>
                                                <?php                                            
    if($bAcP->PRODUTO == 'TOTAL'){
                                                ?>                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
<?php
    } 
    $ultimoOperadora = $bAcP->COD_OPERADORA;
    $contLoopPer++;
} 
?>                                
                                <!-- ###################### Base assinantes - Consolidada por permissor - FIM ###################### -->                               
                                
                                <!-- ###################### Base assinantes - Filiado - FIM ###################### -->         
                            </div>
                          </div>
                    <?php } ?>
                    <?php if(in_array(5, $dashboardPermitidos)){ ?>
                          <h3 onclick="$('#cd_grafico').val(5), $(this).verificaClick(); $(this).reajusteAssinantes();">Reajuste de assinantes</h3>
                          <div>
                            <div class="printDashboard">
                                <a href="#" onclick="jQuery('#div_reaj_assinantes').print()">Imprimir</a>
                            </div>
                            <div id="div_reaj_assinantes">
                                <div class="row">
                                    <div class="divDashboard" id="chart2"></div>
                                </div>
                            </div>
                          </div>
                    <?php } ?>                                        
                    </div>
                           <div id="aguarde" style="text-align: center; display: none"><img src="<?php echo base_url('assets/img/aguarde.gif');?>" /></div>        
                </div>
            </div>
            
    <input type="hidden" id="cd_grafico" value="" />
    
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

/* Se aba do dashboard for aberta grava log de acesso */
$.fn.verificaClick = function() {
    if(!$(this).hasClass('ui-state-active')) {
        $.post( "<?php echo base_url(); ?>dashboard/registraAcesso/", { cd_grafico: $("#cd_grafico").val()} );
    }
};

$(document).ready(function(){
    
    $(function() {
        $( "#dashboards" ).accordion({
            //activate: function( event, ui ) {},
            //beforeActivate: function( event, ui ) {alert(2)},
            collapsible: true, // Habilita a opção de expandir e ocultar ao clicar
            heightStyle: "content",
            active: false
        });
    });
    
    //$(".data").mask("00/00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });    
    
/*################### RENTABILIZAÇÃO - INÍCIO #######################*/ 
    
    // Gráfico tela 1   
    $.fn.graficoTela1Rentabilidade = function() {
        c3.generate({
            bindto: '#chart1',
            data: {
                //url: '<?php echo base_url('assets/c3_test.json')?>',
                url: '<?php echo base_url(); ?>ajax/rentabilizacaoTela1/'+$("#mes_ano_rentabilizacao").val(),
                mimeType: 'json',
                //onclick: function (d, element) { alert(d.value); },
                keys: {
                    x: 'DIA', // it's possible to specify 'x' when category axis
                    value: ['RENTABILIZACAO']
                }
            },
        	axis: {
        		x: {
        			type: 'category'
        		},
                 y : {
                tick: {
                        format: d3.format(',')
                    }
                }            
        	},
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = id === 'RENTABILIZACAO' ? d3.format(',') : d3.format(',');
                        return format(value).replace(',', '.').replace(',', '.');
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            },
            grid: {
                y: {
                    lines: [{value: $("#meta_rentabilizacao").val(), class: 'grid4', text: '<?php echo utf8_encode('META DIÁRIA');?>'}]
                }
            }
        });
    
    }; 
    
    // Gráfico tela 2
    $.fn.graficoTela2Rentabilidade = function(valor) {
        
            $.ajax({
              url: '<?php echo base_url(); ?>ajax/rentabilizacaoTela2/'+valor,
              dataType: "json",
              error: function(res) {
 	            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
                alert('Erro');
              },
              success: function(res) {
                
                var conteudoHtml = '';
                if(res.length > 0){
                    
                    conteudoHtml += '<table class="table table-bordered">';
                    conteudoHtml += '<thead>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<th colspan="3">';
                    conteudoHtml += '<strong>PRODUTOS MAIS RENTABILIZADOS</strong>';
                    conteudoHtml += '</th>';
                    conteudoHtml += '</tr>';
                    conteudoHtml += '</thead>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<th><strong>PRODUTO</strong></th>';
                    conteudoHtml += '<th><strong>QUANTIDADE</strong></th>';
                    conteudoHtml += '<th><strong>RECEITA</strong></th>';
                    conteudoHtml += '</tr>';
                    
                    $.each(res, function() {
                    
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<td>'+this.PRODUTO+'</td>';
                    conteudoHtml += '<td>'+this.QTDE+'</td>';
                    conteudoHtml += '<td>R$ '+this.VLR_TOT+'</td>';
                    conteudoHtml += '</tr>';
                    
                    });
                    
                    conteudoHtml += '</table>';
                    
                }else{
                    conteudoHtml += '';
                }
                
                $("#rentabilizacaoTela2").html(conteudoHtml);
                
              }
            });
        
    };
    
    // Gráfico tela 3
    $.fn.graficoTela3Rentabilidade = function(valor) {
        
            $.ajax({
              url: '<?php echo base_url(); ?>ajax/rentabilizacaoTela3/'+valor,
              dataType: "json",
              error: function(res) {
 	            //$("#resQtdArquivosDia").html('<span>Erro de execução</span>');
                alert('Erro');
              },
              success: function(res) {
                
                var conteudoHtml = '';
                if(res.length > 0){
                    
                    conteudoHtml += '<table class="table table-bordered">';
                    conteudoHtml += '<thead>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<th colspan="3">';
                    conteudoHtml += '<strong>RENTABILIZA&Ccedil;&Atilde;O '+res[0]["MES"]+'</strong>';
                    conteudoHtml += '</th>';
                    conteudoHtml += '</tr>';
                    conteudoHtml += '</thead>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<th>';
                    conteudoHtml += '<strong>PER&Iacute;ODO</strong>';
                    conteudoHtml += '</th>';
                    conteudoHtml += '<th>';
                    conteudoHtml += '<strong>'+res[0]["PERIODO_INICIAL"]+'</strong>';
                    conteudoHtml += '</th>';
                    conteudoHtml += '<th>';
                    conteudoHtml += '<strong>'+res[0]["PERIODO_FINAL"]+'</strong>';
                    conteudoHtml += '</th>';
                    conteudoHtml += '</tr>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<td colspan="2">TOTAL RENTABILIZA&Ccedil;&Atilde;O</td>';
                    conteudoHtml += '<td>R$ '+res[0]["TOT_RENTAB"]+'</td>';
                    conteudoHtml += '</tr>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<td colspan="3">&nbsp;</td>';
                    conteudoHtml += '</tr>';
                    conteudoHtml += '<tr>';
                    conteudoHtml += '<td colspan="2">PROJE&Ccedil;&Atilde;O</td>';
                    conteudoHtml += '<td>R$ '+res[0]["PROJECAO"]+'</td>';
                    conteudoHtml += '</tr>';
                    conteudoHtml += '</table>';
                    
                }else{
                    conteudoHtml += '';
                }
                
                $("#rentabilizacaoTela3").html(conteudoHtml);
                
              }
            });
        
    };           
    
    // Rentabilitação aciona gráficos através da combo
    $("#mes_ano_rentabilizacao").change(function() {
        
        if($(this).val() != '' && $("#meta_rentabilizacao").val() != ''){
            
            $(this).graficoTela1Rentabilidade();
            $(this).graficoTela2Rentabilidade($(this).val());
            $(this).graficoTela3Rentabilidade($(this).val());
            
        }else{
            
            $("#chart1").html('');
            $("#rentabilizacaoTela2").html('');
            $("#rentabilizacaoTela3").html('');
            
        }
        
    });
    
    // Rentabilitação gráfico tela 1
    $("#meta_rentabilizacao").keyup(function() {
        
        if($(this).val() != '' && $("#mes_ano_rentabilizacao").val() != ''){
            
            $(this).graficoTela1Rentabilidade();
            $(this).graficoTela2Rentabilidade($("#mes_ano_rentabilizacao").val());
            $(this).graficoTela3Rentabilidade($("#mes_ano_rentabilizacao").val());
            
        }else{
            
            $("#chart1").html('');
            $("#rentabilizacaoTela2").html('');
            $("#rentabilizacaoTela3").html('');
            
        }
        
    });
    
/*################### RENTABILIZAÇÃO - FIM #######################*/

/*################### REAJUSTA DE ASSINANTES - INÍCIO #######################*/

    $.fn.reajusteAssinantes = function() {
        
        // GRÁFICO BARRA COLADA            
        chart2 = c3.generate({
            bindto: '#chart2',
            data: {
                //url: '<?php echo base_url('assets/c3_test.json')?>',
                url: '<?php echo base_url(); ?>ajax/reajustadosAssinantes/',
                mimeType: 'json',
                type: 'bar',
                x : 'meses',
                columns: ['meses','Qtde']
            },
            axis: {
                x: {
                    type: 'category' // this needed to load string x value
                }/*,
                 y : {
                tick: {
                        format: d3.format(",")
                    }
                }*/
            }/*,
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = id === 'Qtde' ? d3.format(',') : d3.format(',');
                        return format(value).replace(',', '.').replace(',', '.');
                    }
        //            value: d3.format(',') // apply this format to both y and y2
                }
            }*/
        });    
        
    };

/*################### REAJUSTA DE ASSINANTES - FIM #######################*/
   
   
    
});


</script>