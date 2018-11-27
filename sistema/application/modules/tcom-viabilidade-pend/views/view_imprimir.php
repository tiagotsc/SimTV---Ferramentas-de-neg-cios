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
                <strong>PENDENCIA <?php echo $tipo; ?></strong>
            </div>
            <div class="col-md-12">

                <table class="table table-bordered">
                    <tr>
                        <td><strong>CONTROLE: <?php echo $viab['viabilidade']->controle; ?></strong></td>
                        <td><strong>Tipo solicitação:</strong> <?php echo htmlentities($viab['viabilidade']->tipo); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Autor pergunta:</strong> <?php echo $dadosPendencia->usuario_pergunta; ?></td>
                        <td><strong>Data / Hora:</strong> <?php echo $dadosPendencia->data_cadastro_pergunta; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong> <?php echo $dadosPendencia->status; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Pergunta:</strong> <?php echo htmlentities($dadosPendencia->pergunta); ?></td>
                    </tr>
                    <?php if($dadosPendencia->data_cadastro_resposta != ''){ ?>
                    <tr>
                        <td><strong>Autor resposta:</strong> <?php echo $dadosPendencia->usuario_resposta; ?></td>
                        <td><strong>Data / Hora:</strong> <?php echo $dadosPendencia->data_cadastro_resposta; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Resposta:</strong> <?php echo htmlentities($dadosPendencia->resposta); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div> <!-- /container -->

  </body>
</html>