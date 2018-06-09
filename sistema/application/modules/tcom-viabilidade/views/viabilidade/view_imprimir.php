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
    
    $email = (isset($email))? $email: '';
    ?>
    <style>
    <?php if($pdf == 'sim'){ ?>
        .container{
            padding: 0px;
        }
        
        .table thead{
            background-color: red;
        }
        
        tr td{
            padding: 10px 5px;
        }
        
        .row div.cabecalho, tr th.cabecalho, tr td.cabecalho{
            padding: 20px 5px;
        }
    <?php } ?>

    </style>
  </head>

  <body>

    <div class="container">
        
        <?php if(isset($tipo)){ ?>
        <div class="row">
            <div class="col-md-12 text-center cabecalho">
                <h4><strong>Ordem <?php echo $tipo;?></strong></h4>
            </div>
        </div>
        <?php } ?>
        <?php $borda = ($email == 'sim')? 'border="1"': ''; ?>
        <table class="table table-bordered" <?php echo $borda; ?> >
            <thead>
                <tr>
                    <th colspan="2" class="cabecalho">
                        <h4><strong>An&aacute;lise de Viabilidade</strong></h4>
                    </th>
                    <th class="cabecalho">
                        <h4><strong>Data da solicita&ccedil;&atilde;o</strong></h4>
                    </th>
                    <th class="cabecalho">
                        <h4><strong><?php echo $this->util->formataData($viabilidade->dt_solicitacao,'BR'); ?></strong></h4>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php if($viabilidade->designacao != ''){ ?>
            <tr>
                <td colspan="2"><strong>Designa&ccedil;&atilde;o</strong></td>
                <td colspan="2"><?php echo $viabilidade->designacao;?></td>
            </tr>
            <?php } ?>
            <tr>
                <td><strong>N&uacute;mero da solicita&ccedil;&atilde;o</strong></td>
                <td><?php echo $viabilidade->n_solicitacao;?></td>
                <td><strong>Status</strong></td>
                <td><?php echo utf8_decode($viabilidade->tipo);?></td>
            </tr>
            <tr>
                <td><strong>Controle:</strong></td>
                <td><?php echo $viabilidade->controle;?></td>
                <td><strong>Qtd. circuitos:</strong></td>
                <td><?php echo $viabilidade->qtd_circuitos;?></td>
            </tr>
            <tr>
                <td><strong>Velocidade:</strong></td>
                <td><?php echo $viabilidade->velocidade;?></td>
                <td><strong>Interface:</strong></td>
                <td><?php echo $viabilidade->interface; ?></td>
            </tr>
            <tr>
                <td><strong>Observa&ccedil;&otilde;es:</strong></td>
                <td colspan="3"><?php echo utf8_decode($viabilidade->observacao); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="cabecalho"><h4><strong>PONTO A</strong></h4></td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong></td>
                <td><?php echo utf8_decode($operadora->titulo); ?></td>
                <td><strong>E-mail:</strong></td>
                <td><?php echo utf8_decode($operadora->email); ?></td>
            </tr>
            <tr>
                <td><strong>CNPJ:</strong></td>
                <td colspan="3"><?php echo $operadoraCob->cnpj; ?></td>
            </tr>
            <tr>
                <td><strong>Telefones:</strong></td>
                <td colspan="3">
                <?php 
                    foreach($operadoraCobTel as $opeTel){ 
                        $tel[] = $opeTel->telefone;
                    } 
                    echo implode(' / ', $tel);
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="cabecalho"><h4><strong>PONTO B</strong></h4></td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong></td>
                <td><?php echo utf8_decode($cliente->titulo); ?></td>
                <td><strong>E-mail:</strong></td>
                <td><?php echo utf8_decode($cliente->email); ?></td>
            </tr>
            <tr>
                <td><strong>CNPJ:</strong></td>
                <td colspan="3"><?php echo $cliente->cnpj; ?></td>
            </tr>
            <tr>
                <td><strong>Telefones:</strong></td>
                <td colspan="3">
                <?php 
                    foreach($clienteEndTel as $cliTel){ 
                        $tel[] = $cliTel->telefone;
                    } 
                    echo implode(' / ', $tel);
                ?>
                </td>
            </tr>
            <tr>
                <td><strong>Endere&ccedil;o:</strong></td>
                <td colspan="3"><?php echo utf8_decode($clienteEnd->endereco); ?></td>
            </tr>
            <tr>
                <td><strong>N&uacute;mero:</strong></td>
                <td><?php echo utf8_decode($clienteEnd->numero); ?></td>
                <td><strong>Complemento:</strong></td>
                <td><?php echo utf8_decode($clienteEnd->complemento); ?></td>
            </tr>
            <tr>
                <td><strong>CEP:</strong></td>
                <td><?php echo $clienteEnd->cep; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Bairro:</strong></td>
                <td><?php echo utf8_decode($clienteEnd->bairro); ?></td>
                <td><strong>Cidade:</strong></td>
                <td><?php echo utf8_decode($clienteEnd->nome_estado); ?></td>
            </tr>
            </tbody>
        </table>        

    </div> <!-- /container -->

  </body>
</html>