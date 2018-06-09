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
    <title>Formulário de viabilidade</title>
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
     
     @media print {
        
        #anexoConteudo{
            display: none;
        }
        
        body{
            padding: 0px;
            margin: 0px;
        }
        .row{
            margin: 0px;
        }
        
     }
    </style>
  </head>

  <body>


            <table class="table">
                <tr>
                    <td class="text-left"><img id="logo" src="<?php echo base_url('assets/img/logo.jpg');?>" /></td>
                </tr>
            </table>




            <div class="espacoInferior col-md-12">
                <strong>RESPOSTA DE VIABILIDADE TÉCNICA<?php /*echo $tipo;*/ ?></strong>
            </div>
            <!--
            <div class="bg col-md-12">
                <strong>CONTROLE: <?php echo $viab['viabilidade']->controle; ?></strong>
            </div>
            -->
            <table class="table">
                <tr class="bg">
                    <td><strong>CONTROLE:</strong> <?php echo $viab['viabilidade']->controle; ?></td>
                    <td><strong>CONTRATO:</strong> <?php echo $circuito->numero; ?></td>
                </tr>
            </table>
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
                        <td><strong>Tipo solicitação:</strong> <?php echo htmlentities($viab['viabilidade']->tipo); ?></td>
                        <?php
                        if(trim($viab['viabilidade']->designacao) != ''){
                            $designacao = '<strong>Designação:</strong> '.'<a target="_blank" href="'.base_url('tcom-contrato/contrato/imprimir/'.$viab['viabilidade']->idContrato).'">'.$viab['viabilidade']->designacao.'</a>';
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
                        <td><strong>Endereço:</strong> <?php echo htmlentities($viab['operadoraInst']->endereco.', '.$viab['operadoraInst']->numero); ?></td>
                        <td><strong>Endereço:</strong> <?php echo htmlentities($viab[$clienteEnd]->endereco.', '.$viab[$clienteEnd]->numero); ?></td>
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


        
        <div class="row">
            <div class="bg col-md-12 cabecalhoDivisor text-center">
                <strong id="titulo_endereco">DADOS INFORMADO PELO TÉCNICO: <?php echo $viabResp->nome_usuario; ?></strong>
            </div>
        </div>

        <table class="table table-bordered">
            <tr>
                <td><strong>Viável:</strong> <?php echo ($viabResp->viavel =='S')? 'Sim': 'Não'; ?></td>
                <td><strong>Endereço encontrado?</strong> <?php echo ($viabResp->end_enco =='S')? 'Sim': 'Não'; ?></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3"><h5 class="text-center"><strong>CLIENTE HFC</strong></h5></td>
            </tr>
            <tr>
                <td><strong>Cabo (Mts):</strong> <?php echo $viabResp->cabo; ?></td>
                <td><strong>Cordoalha (Mts):</strong> <?php echo $viabResp->cordoalha; ?></td>
                <td><strong>Canalização (Mts):</strong> <?php echo $viabResp->canalizacao; ?></td>
            </tr>
            <tr>
                <td colspan="3"><h5 class="text-center"><strong>PERCURSO FIBRA</strong></h5></td>
            </tr>
            <tr>
                <td><strong>Node distância:</strong> <?php echo $viabResp->node_distancia; ?></td>
                
                <?php
                $link = '';
                if($viabResp->anexo){
                    
                    $anexo = explode(',', $viabResp->anexo);
                    
                    foreach($anexo as $ane){
                        $link[] = '<a target="_blank" href="'.base_url($dirDownload.'/'.$ane).'">'.$ane.'</a>';
                    }
                }
                ?>
                
                <td colspan="2"><span id="anexoConteudo"><strong>Anexo:</strong> <?php echo implode(', ',$link); ?></span></td>
            </tr>
            <tr>
                <td colspan="3"><p class="text-left"><strong>Observação:</strong><br /><?php echo nl2br(htmlentities($viabResp->observacao)); ?></p></td>
            </tr>
            <tr>
                <td><strong>Designação:</strong> <?php echo $circuito->designacao; ?></td>
                <td><strong>Interface:</strong> <?php echo $circuito->interface; ?> </td>
                <td><strong>Velocidade:</strong> <?php echo $circuito->velocidade; ?> </td>
            </tr>
        </table>

  </body>
</html>