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
            background-color: red;
        }
        
        tr td{
            padding: 10px 5px;
        }
        
        .row div.cabecalho, tr th.cabecalho, tr td.cabecalho{
            padding: 20px 5px;
        }
    <?php } ?>
    @media print {
        #anexo{
            display: none;
        }
    }
    </style>
  </head>

  <body>

    <div class="container">
        
        <?php $borda = ($email == 'sim')? 'border="1"': ''; ?>
        <table class="table table-bordered" <?php echo $borda; ?> >
            <thead>
                <tr>
                    <th colspan="2" class="cabecalho">
                        <h4><strong>Contrato</strong></h4>
                    </th>
                    <th colspan="2" class="cabecalho">
                        <h4><strong><?php echo $contrato['numero']; ?></strong></h4>
                    </th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td><strong>Data in&iacute;cio</strong></td>
                <td><?php echo $this->util->formataData($contrato['data_inicio'],'BR'); ?></td>
                <td><strong>Data Fim</strong></td>
                <td><?php echo $this->util->formataData($contrato['data_fim'],'BR'); ?></td>
            </tr>
            <tr>
                <td><strong>Velocidade:</strong></td>
                <td><?php echo $taxa_digital['velocidade'].' '.$taxa_digital['tipo'];?></td>
                <td><strong>Interface:</strong></td>
                <td><?php echo $interface['nome']; ?></td>
            </tr>
            <tr>
                <td><strong>Qtd. circuitos:</strong></td>
                <td><?php echo $contrato['qtd_circuitos'];?></td>
                <td><strong>Status:</strong></td>
                <?php $status = array('A'=>'Ativo', 'I'=>'Inativo','P'=>'Pendente','C'=>'Cancelado'); ?>
                <td><?php echo $status[$contrato['status']];?></td>
            </tr>
            <?php if($anexos){ ?>
            <tr id="anexo">
                <td><strong>Anexo:</strong></td>
                <td colspan="3">
                <?php foreach($anexos as $an){ ?>
                    <a target="_blank" href="<?php echo base_url($dirDownload.'/'.$an->anexo); ?>"><?php echo $an->anexo_label; ?></a><br />
                <?php } ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="4" class="cabecalho"><h4><strong>PONTO A</strong></h4></td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong></td>
                <td><?php echo utf8_decode($operadora['titulo']); ?></td>
                <td><strong>E-mail:</strong></td>
                <td><?php echo utf8_decode($operadora['email']); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="cabecalho"><h4>Dados instala&ccedil;&atilde;o</h4></td>
            </tr>
            <tr>
                <td><strong>CNPJ:</strong></td>
                <td><?php echo $operadoraInst['cnpj']; ?></td>
                <td><strong>Endere&ccedil;o:</strong></td>
                <td><?php echo htmlentities($operadoraInst['endereco']); ?></td>
            </tr>
            <tr>
                <td><strong>N&uacute;mero:</strong></td>
                <td><?php echo htmlentities($operadoraInst['numero']); ?></td>
                <td><strong>Bairro:</strong></td>
                <td><?php echo htmlentities($operadoraInst['bairro']); ?></td>
            </tr>
            <tr>
                <td><strong>CEP:</strong></td>
                <td><?php echo $operadoraInst['cep']; ?></td>
                <td><strong>Estado:</strong></td>
                <td><?php echo $this->contrato->estado($operadoraInst['cd_estado'])->nome_estado; ?></td>
            </tr>
            <tr>
                <td><strong>Complemento</strong></td>
                <td colspan="3"><?php echo htmlentities($operadoraInst['complemento']); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="cabecalho"><h4>Dados cobran&ccedil;a</h4></td>
            </tr>
            <tr>
                <td><strong>CNPJ:</strong></td>
                <td><?php echo $operadoraCob['cnpj']; ?></td>
                <td><strong>Endereço:</strong></td>
                <td><?php echo htmlentities($operadoraCob['endereco']); ?></td>
            </tr>
            <tr>
                <td><strong>N&uacute;mero:</strong></td>
                <td><?php echo htmlentities($operadoraCob['numero']); ?></td>
                <td><strong>Bairro:</strong></td>
                <td><?php echo htmlentities($operadoraCob['bairro']); ?></td>
            </tr>
            <tr>
                <td><strong>CEP:</strong></td>
                <td><?php echo $operadoraCob['cep']; ?></td>
                <td><strong>Estado:</strong></td>
                <td><?php echo $this->contrato->estado($operadoraCob['cd_estado'])->nome_estado; ?></td>
            </tr>
            <tr>
                <td><strong>Complemento</strong></td>
                <td colspan="3"><?php echo htmlentities($operadoraCob['complemento']); ?></td>
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
                <td><?php echo utf8_decode($cliente['titulo']); ?></td>
                <td><strong>E-mail:</strong></td>
                <td><?php echo htmlentities($cliente['email']); ?></td>
            </tr>
            <tr>
                <td><strong>CNPJ:</strong></td>
                <td colspan="3"><?php echo $cliente['cnpj']; ?></td>
            </tr>
            <tr>
                <td><strong>Telefones:</strong></td>
                <td colspan="3">
                <?php 
                echo $contratoDados['telefones'];
                ?>
                </td>
            </tr>
            <tr>
                <td><strong>Endere&ccedil;o:</strong></td>
                <td colspan="3"><?php echo htmlentities($contratoDados['endereco']); ?></td>
            </tr>
            <tr>
                <td><strong>N&uacute;mero:</strong></td>
                <td><?php echo htmlentities($contratoDados['numero']); ?></td>
                <td><strong>Complemento:</strong></td>
                <td><?php echo htmlentities($contratoDados['complemento']); ?></td>
            </tr>
            <tr>
                <td><strong>Bairro:</strong></td>
                <td><?php echo htmlentities($contratoDados['bairro']); ?></td>
                <td><strong>Estado:</strong></td>
                <td><?php echo $this->contrato->estado($contratoDados['cd_estado'])->nome_estado; ?></td>
            </tr>
            <?php if($equipamentos){ ?>
            <tr>
                <td colspan="4" class="cabecalho"><h4><strong>EQUIPAMENTO</strong></h4></td>
            </tr>
            <tr>
                <td colspan="1"><strong>Marca</strong></td>
                <td colspan="2"><strong>Modelo</strong></td>
                <td><strong>Identificação</strong></td>
            </tr>
                <?php foreach($equipamentos as $equi){ ?>
            <tr>
                <td colspan="1"><?php echo $equi->marca; ?></td>
                <td colspan="2"><?php echo $equi->modelo; ?></td>
                <td><?php echo $equi->identificacao; ?></td>
            </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>        

    </div> <!-- /container -->
  </body>
</html>