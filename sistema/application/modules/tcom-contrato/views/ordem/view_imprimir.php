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
    <title>Formul�rio de viabilidade</title>
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
     
     .bordaToda{
        border: 1px solid;
     }
     
     .respTecnico{
        width: 200px;
     }
     
     .divEsquerda{
        width: 47%; 
        float: left;
     }
     
     body{
        font-size: 11px;
     }
     
     td{
        padding: 2px;
     }
     
     .tdWidth120{
        width: 120px;
     }
     
     .paddingTopGrande{
        padding-top: 50px;
     }
     
     .paddingTopPequena{
        padding-top: 25px;
     }
     
     hr{
        width: 100%; margin: 0px; padding: 0px; color: black;
     }
     
    </style>
  </head>

  <body>


    <table class="table">
        <tr>
            <td style="width: 212px; height: 58px;" class="text-left bordaToda"><img id="logo" src="<?php echo base_url('assets/img/logo.jpg');?>" /></td>
            <td class="text-center bordaToda">TELEVIS�O CIDADE S/A<br />RELAT�RIO DE ATENDIMENTO T�CNICO</td>
            <td class="bordaToda">Relat�rio: N� <?php echo $ordem->numero; ?></td>
        </tr>
    </table>
    
    <table class="table bordaToda">
        <tr>
            <td>DESIGNA��O:</td>
            <td><?php echo $circuito->designacao; ?></td>
            <td>DATA / HORA ABERTURA:</td>
            <td><?php echo $ordem->data_cadastro; ?></td>
        </tr>
    </table>
    
    <table class="table bordaToda">
        <tr>
            <td>SERVI�OS E ATIVIDADES SOLICITADAS:</td>
            <td><?php echo htmlentities($ordem->viab_tipo); ?></td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td colspan="4" class="bordaToda">PONTA A: <?php echo $operadora['titulo']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda" colspan="3">Endere�o: <?php echo $operadoraInst['endereco']; ?></td>
            <td class="bordaToda">N�mero:</td>
        </tr>
        <tr>
            <td class="bordaToda">Bairro: <?php echo $operadoraInst['numero']; ?></td>
            <td class="bordaToda">CEP: <?php echo $operadoraInst['cep']; ?></td>
            <td class="bordaToda">Estado: <?php echo $operadoraEstado->sigla_estado; ?></td>
            <td class="bordaToda">Cidade: <?php echo $operadoraInst['cidade']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda" colspan="4">Complemento: <?php echo $operadoraInst['complemento']; ?></td>
        </tr>
        <tr>
            <td colspan="4" class="bordaToda">PONTA B: <?php echo $cliente['titulo']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda" colspan="3">Endere�o: <?php echo $clienteEnd['endereco']; ?></td>
            <td class="bordaToda">N�mero: <?php echo $clienteEnd['numero']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda">Bairro: <?php echo $clienteEnd['bairro']; ?></td>
            <td class="bordaToda">CEP: <?php echo $clienteEnd['cep']; ?></td>
            <td class="bordaToda">Estado: <?php echo $clienteEstado->sigla_estado; ?></td>
            <td class="bordaToda">Cidade: <?php echo $clienteEnd['cidade']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda" colspan="4">Telefones: 
            <?php 
            foreach($clienteEndTel as $cliTel){ 
                
                $tel[] = $cliTel->telefone;
            } 
            echo implode(' / ', $tel);
            ?>
            </td>
        </tr>
    </table>
    <!--
    <table class="table">
        <tr>
            <td class="tdWidth120 bordaToda">Cliente:</td>
            <td colspan="3" class="bordaToda"><?php echo $operadora['titulo']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda">Endere�o Ponta A:</td>
            <td class="bordaToda" colspan="3"><?php echo $operadoraInst['endereco']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda">Endere�o Ponta B:</td>
            <td class="bordaToda" colspan="3"></td>
        </tr>
        <tr>
            <td class="bordaToda">Respons�vel Local:</td>
            <td class="bordaToda" colspan="3"></td>
        </tr>
        <tr>
            <td class="bordaToda">Telefone:</td>
            <td class="bordaToda" colspan="2"></td>
            <td class="bordaToda">Velocidade:</td>
        </tr>
    </table>-->
    <!--
    <table class="table">
        <tr>
            <td class="text-center bordaToda" colspan="6">SERVI�OS E ATIVIDADES SOLICITADAS</td>
        </tr>
        <tr>
            <td class="bordaToda">ATIVA��O</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">MUDAN�A DE ENDERE�O</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">ALTERA��O T�CNICA</td>
            <td class="bordaToda">( )</td>
        </tr>
        <tr>
            <td class="bordaToda">DESCONEX�O</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">MANUTEN��O CORRETIVA</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">OUTROS</td>
            <td class="bordaToda">( )</td>
        </tr>
        <tr>
            <td class="bordaToda">VISITA T�NICA</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">MANUTEN��O PREVENTIVA</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda"></td>
            <td class="bordaToda"></td>
        </tr>
    </table>
    -->
    <table class="table">
        <tr>
            <td class="text-center bordaToda" colspan="6">MANUTEN��O</td>
        </tr>
        <tr>
            <td class="bordaToda">Equipamentos TVC no usu�rio</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Escorregamento</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Interrup��o Intermitente</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td class="bordaToda">Sem Sincronismo</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">OLOS</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Instabilidade el�trica</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td class="bordaToda">Bit Error</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">LOS</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Outros(obs)</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td class="text-center bordaToda" colspan="6">CHECK LIST</td>
        </tr>
        <tr>
            <td class="bordaToda">Itens</td>
            <td class="bordaToda text-center">Sim N�o</td>
            <td class="bordaToda">Itens</td>
            <td class="bordaToda text-center">Sim N�o</td>
            <td class="bordaToda">Itens</td>
            <td class="bordaToda text-center">Sim N�o</td>
        </tr>
        <tr>
            <td class="bordaToda">Alimenta��o Especificada</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Local de Inst. Adequado</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Teste de Continuidade</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td class="bordaToda">Aterramento Adequado</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Interfer�ncia El�trica</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Emula��o Frame Relay</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td class="bordaToda">Fia��o Interna Adequado</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Teste de Circuito Normal</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Outros (obs)</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td class="text-center bordaToda" colspan="10">EQUIPAMENTO NA TVC</td>
        </tr>
        <tr>
            <td class="text-center bordaToda">SDH ORIGEM</td>
            <td class="text-center bordaToda">PORTA</td>
            <td class="text-center bordaToda">PATH</td>
            <td class="text-center bordaToda">SDH DESTINO</td>
            <td class="text-center bordaToda">PORTA</td>
            <td class="text-center bordaToda">CANALIZADO</td>
            <td class="text-center bordaToda">CANAL ENTRADA</td>
            <td class="text-center bordaToda">CANAL SA�DA</td>
            <td class="text-center bordaToda">TIMESLOT</td>
            <td class="text-center bordaToda">OBS:</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td class="text-center bordaToda" colspan="7">EQUIPAMENTO NO CLIENTE</td>
        </tr>
        <tr>
            <td class="bordaToda">Tipo</td>
            <td class="bordaToda">N� de S�rie</td>
            <td class="bordaToda">Fabricante</td>
            <td class="bordaToda">Interface</td>
            <td class="bordaToda">Tribut�rio</td>
            <td class="bordaToda">Endere�o doEnlace</td>
            <td class="bordaToda">Canalizado</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
            <td class="bordaToda">&nbsp;</td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td class="bordaToda">OBSERVA��ES:</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
        </tr>
        <tr>
            <td class="bordaToda">&nbsp;</td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td class="bordaToda" colspan="3">DADOS DO CHAMADO</td>
        </tr>
        <tr>
            <td class="bordaToda">T�cnico TVC:</td>
            <td class="bordaToda"></td>
            <td class="bordaToda">Hora de Fechamento:</td>
        </tr>
        <tr>
            <td class="bordaToda">N� Matr�cula:</td>
            <td class="bordaToda"></td>
            <td class="bordaToda">Data de Fechamento:</td>
        </tr>
        <tr>
            <td class="bordaToda">Assinatura:</td>
            <td class="bordaToda"></td>
            <td class="bordaToda">OBS:</td>
        </tr>
        <tr>
            <td class="bordaToda" colspan="3">Senha de Aceite:</td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td class="respTecnico">Respons�vel T�cnico CLIENTE:</td>
            <td rowspan="2" colspan="2" class="text-center paddingTopPequena">
                <hr />
                <!--<div style="border-top: 1px solid; width: 100%;">Nome por extenso</div>-->
                <span>Nome por extenso</span>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="paddingTopGrande"></td>
            <td class="text-center paddingTopGrande">
                <div class="divEsquerda text-center">
                    ___________________________________________<br />
                    Assinatura
                </div>
            </td>
            <td class="text-center paddingTopGrande">
                <div class="divEsquerda">
                    ____/____/______<br/>
                    Data
                </div>
            </td>
        </tr>
    </table>

  </body>
</html>