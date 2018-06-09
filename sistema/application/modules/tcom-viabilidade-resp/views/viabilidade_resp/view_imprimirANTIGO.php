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
    
    $tipo = ($tipo != '')? ' - '.strtoupper($tipo): '';
    
    ?>
    <style>
     .bg{
        background-color: #ccc;
     }
     #emailEspaco{
        margin: 0px 100px;
     }
     .espacoInferior{
        margin-bottom: 15px;
     }
    </style>
  </head>

  <body>

    <div id="emailEspaco" class="container">
        <div class="row">
            <div class="espacoInferior col-md-12">
                <strong>RESPOSTA DE VIABILIDADE T�CNICA<?php echo $tipo; ?></strong>
            </div>
            <div class="bg col-md-12">
                <strong>CONTROLE: <?php echo $viab['viabilidade']->controle; ?></strong>
            </div>
            <div class="col-md-12">
                <?php
                if($viab['viabilidade']->id_tipo == 5){
                    $clienteEnd = 'mdEnd';
                }else{
                    $clienteEnd = 'clienteEnd';
                }
                ?>
            
                <table class="table table-bordered">
                    <tr>
                        <th>PONTO A</th>
                        <th>PONTO B</th>
                    </tr>
                    <tr>
                        <td><strong>Tipo solicita��o:</strong> <?php echo htmlentities($viab['viabilidade']->tipo); ?></td>
                        <?php
                        if(trim($viab['viabilidade']->designacao) != ''){
                            $designacao = '<strong>Designa��o:</strong> '.'<a target="_blank" href="'.base_url('tcom-contrato/contrato/imprimir/'.$viab['viabilidade']->idContrato).'">'.$viab['viabilidade']->designacao.'</a>';
                        }else{
                            $designacao = '';
                        }
                        ?>
                        <td class="text-left"><?php echo $designacao; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nome:</strong> <?php echo htmlentities($viab['operadora']->titulo); ?></td>
                        <td><strong>Nome:</strong> <?php echo htmlentities($viab['cliente']->titulo); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Endere�o:</strong> <?php echo htmlentities($viab['operadoraInst']->endereco.', '.$viab['operadoraInst']->numero); ?></td>
                        <td><strong>Endere�o:</strong> <?php echo htmlentities($viab[$clienteEnd]->endereco.', '.$viab[$clienteEnd]->numero); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cidade:</strong> <?php echo htmlentities($viab['operadoraInst']->nome_estado); ?></td>
                        <td><strong>Cidade:</strong> <?php echo htmlentities($viab[$clienteEnd]->nome_estado); ?></td>
                    </tr>
                    <tr>
                        <td><strong>UF:</strong> <?php echo $viab['operadoraInst']->sigla_estado; ?></td>
                        <td><strong>UF:</strong> <?php echo $viab[$clienteEnd]->sigla_estado; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row">
            <div class="bg col-md-12 cabecalhoDivisor text-center">
                <strong id="titulo_endereco">DADOS INFORMADO PELO T�CNICO: <?php echo $viabResp->nome_usuario; ?></strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Vi�vel:</strong> <?php echo ($viabResp->viavel =='S')? 'Sim': 'N�o'; ?></td>
                        <td><strong>Endere�o encontrado?</strong> <?php echo ($viabResp->end_enco =='S')? 'Sim': 'N�o'; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"><h5 class="text-center"><strong>CLIENTE HFC</strong></h5></td>
                    </tr>
                    <tr>
                        <td><strong>Cabo (Mts):</strong> <?php echo $viabResp->cabo; ?></td>
                        <td><strong>Cordoalha (Mts):</strong> <?php echo $viabResp->cordoalha; ?></td>
                        <td><strong>Canaliza��o (Mts):</strong> <?php echo $viabResp->canalizacao; ?></td>
                    </tr>
                    <tr>
                        <td colspan="3"><h5 class="text-center"><strong>PERCURSO FIBRA</strong></h5></td>
                    </tr>
                    <tr>
                        <td><strong>Node dist�ncia:</strong> <?php echo $viabResp->node_distancia; ?></td>
                        
                        <?php
                        $link = '';
                        if($viabResp->anexo){
                            
                            $anexo = explode(',', $viabResp->anexo);
                            
                            foreach($anexo as $ane){
                                $link[] = '<a target="_blank" href="'.base_url($dirDownload.'/'.$ane).'">'.$ane.'</a>';
                            }
                        }
                        
                        ?>
                        
                        <td colspan="2"><strong>Anexo:</strong> <?php echo implode(', ',$link); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3"><p class="text-left"><strong>Observa��o:</strong><br /><?php echo nl2br(htmlentities($viabResp->observacao)); ?></p></td>
                    </tr>
                </table>
            </div>
        </div>
    </div> <!-- /container -->

  </body>
</html>