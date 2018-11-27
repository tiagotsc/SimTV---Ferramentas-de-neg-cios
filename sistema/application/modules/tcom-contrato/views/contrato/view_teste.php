<!DOCTYPE html>
<html lang="en">
  <head>
    <!--<meta charset="utf-8">-->
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Ferramentas de neg&oacute;cio</title>
    <?php
    echo link_tag(array('href' => 'assets/css/bootstrap.css','rel' => 'stylesheet','type' => 'text/css'));  
    echo link_tag(array('href' => 'assets/font-awesome/css/font-awesome.min.css','rel' => 'stylesheet','type' => 'text/css'));
    echo "<script type='text/javascript' src='".base_url('assets/js/bootstrap.js')."'></script>";  
    ?>
    <style>
    <?php if($pdf == 'sim'){ ?>
        .container{
            padding: 0px;
        }
        
        .table thead{
            /*background-color: red;*/
        }
        
        tr td{
            padding: 5px 5px;
        }
        
        .row div.cabecalho, tr th.cabecalho, tr td.cabecalho{
            padding: 10px 5px;
        }
        
        #thTitulo{
            padding-left: 140px; 
            padding-bottom: 20px; 
            text-align: center;
        }
        
        #thLogo{
            text-align: right;
            vertical-align: top;
        }
        
        .semBorda{
            border-top: 0px;
            border-left: 0px;
            border-right: 0px;
        }
    <?php } ?>

    .bordaInferior{
        border-bottom: 1px solid;
    }
    .assinatura{
        height: 60px;
        background-color: #D3D3D3;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    
    #thTitulo{
        padding-left: 240px; 
        padding-bottom: 20px; 
        text-align: center;
    }
    
    #thLogo{
        text-align: right;
        vertical-align: top;
    }
    
    @media print {
        h2{
            font-size: 18px;
        }
        h5{
            margin: 3px;
            /*padding: 0px;*/
            font-size: 12px;
        }
        tr td{
            padding: 3px 3px;
            font-size: 11px;
        }
        .row div.cabecalho, tr th.cabecalho, tr td.cabecalho{
            padding: 8px 5px;
        }
        
        
        #thTitulo{
            padding-left: 180px; 
            padding-bottom: 20px; 
            text-align: center;
        }
        
        #thLogo{
            text-align: right;
            vertical-align: top;
        }
        
        #logo{
            width: 80px; display: block; float: right;
        }
    }
    
    </style>
  </head>

  <body>

    <div class="container">
        
        <table class="table">
            <thead>
                <tr>
                    <th id="thTitulo" colspan="3" class="bordaInferior">
                        <h2>TELECOM</h2>
                        <h4><strong>Informações Gerais</strong></h4>
                    </th>
                    <th id="thLogo" class="bordaInferior">
                        <img id="logo" src="<?php echo base_url('assets/img/logo.jpg');?>" />
                    </th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="2"><strong>NÚMERO DO CONTRATO</strong></td>
                <td colspan="2"><strong>CIDADE</strong></td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $contrato['numero']; ?></td>
                <td colspan="2"><?php echo utf8_decode($unidade['nome']); ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>CLIENTE FINAL</strong></td>
                <td colspan="2"><strong>NOME DA OPERADORA</strong></td>
            </tr>
            <tr>
                <td colspan="2"><?php echo utf8_decode($cliente['titulo']); ?></td>
                <td colspan="2"><?php echo utf8_decode($operadora['titulo']); ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>PRAZO DO CONTRATO (MESES)</strong></td>
                <td colspan="2"><strong>DESIGNAÇÃO (NÚMERO DO CIRCUITO)</strong></td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $contrato['duracao_mes']; ?></td>
                <td colspan="2"><?php echo $circuito['designacao']; ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>VELOCIDADE</strong></td>
                <td colspan="2"><strong>INTERFACE</strong></td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $taxa_digital['velocidade'].' '.$taxa_digital['tipo'];?></td>
                <td colspan="2"><?php echo $interface['nome']; ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>DATA DA INSTALAÇÃO PREVISTA</strong></td>
                <td colspan="2"><strong>DATA DA 1ª FATURA</strong></td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $this->util->formataData($viabResp->prazo_ativacao); ?></td>
                <td colspan="2"><?php echo $this->util->formataData($contrato['data_pri_fatura'],'BR'); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-center bordaInferior"><h5>ANÁLISE FINANCEIRA DO PROJETO</h5></td>
            </tr>
            <tr>
                <td colspan="2"><strong>CAPEX DO PROJETO</strong></td>
                <td colspan="2"><strong>SEM IMPOSTOS</strong></td>
            </tr>
            <tr>
                <td colspan="2">Mão de obra empreiteira</td>
                <td colspan="2">R$ <?php echo number_format($valores['mao_obra_empreiteira'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="2">Aquisição de equipamentos</td>
                <td colspan="2">R$ <?php echo number_format($valores['aquisicao_equipamento'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="2">SUB Total</td>
                <td colspan="2">R$ <?php echo number_format($valores['sub_total'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>RECEITA DO PROJETO</strong></td>
                <td colspan="1"><strong>SEM IMPOSTOS</strong></td>
                <td colspan="1"><strong>COM IMPOSTOS</strong></td>
            </tr>
            <tr>
                <td colspan="2">Taxa de instalação</td>
                <td colspan="1">R$ <?php echo number_format($valores['taxa_inst_sem_imposto'], 2, ',', '.'); ?></td>
                <td colspan="1">R$ <?php echo number_format($valores['taxa_inst_com_imposto'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="2">Valor da mensalidade</td>
                <td colspan="1">R$ <?php echo number_format($valores['mens_atual_sem_imposto'], 2, ',', '.'); ?></td>
                <td colspan="1">R$ <?php echo number_format($valores['mens_atual_com_imposto'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="2">Receita Total</td>
                <td colspan="1">R$ <?php echo number_format($valores['receita_total_sem_imposto'], 2, ',', '.'); ?></td>
                <td colspan="1">R$ <?php echo number_format($valores['receita_total_com_imposto'], 2, ',', '.'); ?></td>
            </tr>
            </tbody>
        </table>   
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-center bordaInferior">APROVAÇÃO</h5>
            </div>  
        </div> 
        <div class="row">
            <div class="col-xs-5 col-sm-4">
                <h6 class="text-center bordaInferior"><strong>Aprovação - Gerência de Telecom</strong></h6>
                <div class="assinatura"></div>
            </div>
            <div style="float: right;" class="col-xs-5 col-sm-4">
                <h6 class="text-center bordaInferior"><strong>Aprovação - Gerência Financeira</strong></h6>
                <div class="assinatura"></div>
            </div>
        </div> 
        <div class="row">
            <div class="col-xs-5 col-sm-4">
                <h6 class="text-center bordaInferior"><strong>Aprovação - Diretoria Comercial</strong></h6>
                <div class="assinatura"></div>
            </div>
            <div style="float: right;" class="col-xs-5 col-sm-4">
                <h6 class="text-center bordaInferior"><strong>Aprovação - Diretoria Financeira</strong></h6>
                <div class="assinatura"></div>
            </div>
        </div> 

    </div> <!-- /container -->
  </body>
</html>