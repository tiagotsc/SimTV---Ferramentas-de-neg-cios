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
  </head>

  <body>

    <div class="container">
        <?php if($feriasEntra){ ?>
        <table class="table table-bordered" border="1">
            <thead>
                <tr><th colspan="2" class="cabecalho"><h4><strong>Colaboradores entrando de férias em <?php echo date('d/m/Y'); ?></strong></h4></th></tr>
            </thead>
            <tbody>
            <tr>
                <td><strong>Matrícula</strong></td>
                <td><strong>Nome</strong></td>
            </tr>
            <?php foreach($feriasEntra as $fEntra){ ?>
            <tr>
                <td><?php echo $fEntra->matricula_usuario; ?></td>
                <td><?php echo $fEntra->nome_usuario; ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>  
        <br /><br />       
        <?php } ?>
        <?php if($feriasVolta){ ?>
        <table class="table table-bordered" border="1">
            <thead>
                <tr><th colspan="2" class="cabecalho"><h4><strong>Colaboradores voltando de férias em <?php echo date('d/m/Y', strtotime("+1 days",strtotime( date('Y-m-d') ))); ?></strong></h4></th></tr>
            </thead>
            <tbody>
            <tr>
                <td><strong>Matrícula</strong></td>
                <td><strong>Nome</strong></td>
            </tr>
            <?php foreach($feriasVolta as $fVolta){ ?>
            <tr>
                <td><?php echo $fVolta->matricula_usuario; ?></td>
                <td><?php echo $fVolta->nome_usuario; ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>        
        <?php } ?>
    </div> <!-- /container -->

  </body>
</html>