<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
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
        #croqui{
            height: 300px; 
            vertical-align: text-top;
        }
        
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
    <?php }else{ ?>
        #croqui{
            height: 230px; 
            vertical-align: text-top;
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
                <tr><th colspan="4" class="cabecalho"><h4><strong>Requisição para Vistoria a Ser Executada <?php echo $this->util->formataData($inicio,'BR'); ?></strong></h4></th></tr>
            </thead>
            <!--<tfoot>
                <tr><td colspan="4">Visite nossa loja</td></tr>
            </tfoot>-->
            <tbody>
            <!--<tr>
                <td rowspan="2">Seminovos</td>
                <td>Trompete</td>
                <td>Trombone</td>
                <td>Trompa</td>
            </tr>-->
            <tr>
                <td><strong>De:</strong></td>
                <td>Geodados</td>
                <td><strong>Para:</strong></td>
                <td><?php echo $sigla_estado.'-'.utf8_decode($unidade); ?></td>
            </tr>
            <tr>
                <td><strong>Controle:</strong></td>
                <td><?php echo $controle; ?></td>
                <td><strong>Node:</strong></td>
                <td><?php echo $descricao; ?></td>
            </tr>
            <tr>
                <td><strong>Contrato:</strong></td>
                <td><?php echo $contrato; ?></td>
                <td><strong>Origem:</strong></td>
                <td><?php echo $origem; ?></td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong></td>
                <td><?php echo utf8_decode($nome); ?></td>
                <td><strong>Telefones:</strong></td>
                <td><?php echo $telefone.' / '.$celular; ?></td>
            </tr>
            <tr>
                <td><strong>Endereço:</strong></td>
                <td colspan="3"><?php echo utf8_decode($endereco); ?></td>
            </tr>
            <tr>
                <td><strong>Número:</strong></td>
                <td><?php echo $numero; ?></td>
                <td><strong>Complemento:</strong></td>
                <td><?php echo utf8_decode($complemento); ?></td>
            </tr>
            <tr>
                <td><strong>Bairro:</strong></td>
                <td><?php echo utf8_decode($bairro); ?></td>
                <td><strong>Cidade:</strong></td>
                <td><?php echo utf8_decode($cidade); ?></td>
            </tr>
            <tr>
                <td><strong>Referência:</strong></td>
                <td colspan="3"><?php echo utf8_decode($referencia); ?></td>
            </tr>
            <tr>
                <td><strong>Observações:</strong></td>
                <td colspan="3"><?php echo utf8_decode($observacao); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="cabecalho"><h4><strong>Retorno do técnico</strong></h4></td>
            </tr>
            <tr>
                <td><strong>Téc. Responsável:</strong></td>
                <td></td>
                <td><strong>Data Execução:</strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Aval:</strong></td>
                <td></td>
                <td><strong>TAP:</strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>N°s Próximos:</strong></td>
                <td></td>
                <td><strong>Distância:</strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Carac. Edificação:</strong></td>
                <td></td>
                <td><strong>Classe Social:</strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Observações:</strong></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="4" id="croqui"><strong>Croqui:</strong></td>
            </tr>
            </tbody>
        </table>        

    </div> <!-- /container -->

  </body>
</html>