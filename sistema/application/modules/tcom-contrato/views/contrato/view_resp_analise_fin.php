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

  </head>

  <body>

    <!--<div class="col-md-10 col-sm-9">-->
    <div class="col-lg-12">
        
        <div id="divMain">
            <h3>Alguém respondeu a aprovação! Dê uma olhada abaixo:</h3>
            <table class="table" border="1">
                <tr>
                    <th>Contrato</th>
                    <th>Permissor</th>
                    <th>Início</th>
                    <th>Fim</th>
                </tr>
                <tr>
                    <td><?php echo $contrato->numero; ?></td>
                    <td><?php echo utf8_decode($contrato->unidade); ?></td>
                    <td><?php echo $contrato->data_inicio; ?></td>
                    <td><?php echo $contrato->data_fim; ?></td>
                </tr>
            </table>
            <br /><br />
            <table class="table table-bordered" border="1">
                <tr>
                    <th>Responsável</th>
                    <th>Cargo</th>
                    <th>Aprovado</th>
                    <th>Data aprovação</th>
                </tr>
                <?php foreach($responsaveis as $resp){ ?>
                <tr>
                    <td><?php echo $resp->nome_usuario; ?></td>
                    <td><?php echo $resp->cargo; ?></td>
                    <td>
                    <?php if($resp->aprovado == 'S'){ ?>
                    <span>Sim</span>
                    <?php }elseif($resp->aprovado == 'N'){ ?>
                    <span>N&atilde;o</span>
                    <?php } ?>

                    </td>
                    <td><?php echo $resp->data_aprovacao; ?></td>
                </tr>
                <?php } ?>
            </table>
             
        </div>
    </div>
  </body>
</html>