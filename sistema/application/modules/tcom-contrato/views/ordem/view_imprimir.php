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
            <td class="text-center bordaToda">TELEVISÃO CIDADE S/A<br />RELATÓRIO DE ATENDIMENTO TÉCNICO</td>
            <td class="bordaToda">Relatório: Nº <?php echo $ordem->numero; ?></td>
        </tr>
    </table>
    
    <table class="table bordaToda">
        <tr>
            <td>DESIGNAÇÃO:</td>
            <td><?php echo $circuito->designacao; ?></td>
            <td>DATA / HORA ABERTURA:</td>
            <td><?php echo $ordem->data_cadastro; ?></td>
        </tr>
    </table>
    
    <table class="table bordaToda">
        <tr>
            <td>SERVIÇOS E ATIVIDADES SOLICITADAS:</td>
            <td><?php echo htmlentities($ordem->viab_tipo); ?></td>
        </tr>
    </table>
    
    <table class="table">
        <tr>
            <td colspan="4" class="bordaToda">PONTA A: <?php echo $operadora['titulo']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda" colspan="3">Endereço: <?php echo $operadoraInst['endereco']; ?></td>
            <td class="bordaToda">Número:</td>
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
            <td class="bordaToda" colspan="3">Endereço: <?php echo $clienteEnd['endereco']; ?></td>
            <td class="bordaToda">Número: <?php echo $clienteEnd['numero']; ?></td>
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
            <td class="bordaToda">Endereço Ponta A:</td>
            <td class="bordaToda" colspan="3"><?php echo $operadoraInst['endereco']; ?></td>
        </tr>
        <tr>
            <td class="bordaToda">Endereço Ponta B:</td>
            <td class="bordaToda" colspan="3"></td>
        </tr>
        <tr>
            <td class="bordaToda">Responsável Local:</td>
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
            <td class="text-center bordaToda" colspan="6">SERVIÇOS E ATIVIDADES SOLICITADAS</td>
        </tr>
        <tr>
            <td class="bordaToda">ATIVAÇÃO</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">MUDANÇA DE ENDEREÇO</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">ALTERAÇÃO TÉCNICA</td>
            <td class="bordaToda">( )</td>
        </tr>
        <tr>
            <td class="bordaToda">DESCONEXÃO</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">MANUTENÇÃO CORRETIVA</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">OUTROS</td>
            <td class="bordaToda">( )</td>
        </tr>
        <tr>
            <td class="bordaToda">VISITA TÉNICA</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda">MANUTENÇÃO PREVENTIVA</td>
            <td class="bordaToda">( )</td>
            <td class="bordaToda"></td>
            <td class="bordaToda"></td>
        </tr>
    </table>
    -->
    <table class="table">
        <tr>
            <td class="text-center bordaToda" colspan="6">MANUTENÇÃO</td>
        </tr>
        <tr>
            <td class="bordaToda">Equipamentos TVC no usuário</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Escorregamento</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Interrupção Intermitente</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td class="bordaToda">Sem Sincronismo</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">OLOS</td>
            <td class="bordaToda">(&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Instabilidade elétrica</td>
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
            <td class="bordaToda text-center">Sim Não</td>
            <td class="bordaToda">Itens</td>
            <td class="bordaToda text-center">Sim Não</td>
            <td class="bordaToda">Itens</td>
            <td class="bordaToda text-center">Sim Não</td>
        </tr>
        <tr>
            <td class="bordaToda">Alimentação Especificada</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Local de Inst. Adequado</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Teste de Continuidade</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td class="bordaToda">Aterramento Adequado</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Interferência Elétrica</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
            <td class="bordaToda">Emulação Frame Relay</td>
            <td class="bordaToda text-center">(&nbsp;&nbsp;&nbsp;) (&nbsp;&nbsp;&nbsp;)</td>
        </tr>
        <tr>
            <td class="bordaToda">Fiação Interna Adequado</td>
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
            <td class="text-center bordaToda">CANAL SAÍDA</td>
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
            <td class="bordaToda">Nº de Série</td>
            <td class="bordaToda">Fabricante</td>
            <td class="bordaToda">Interface</td>
            <td class="bordaToda">Tributário</td>
            <td class="bordaToda">Endereço doEnlace</td>
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
            <td class="bordaToda">OBSERVAÇÕES:</td>
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
            <td class="bordaToda">Técnico TVC:</td>
            <td class="bordaToda"></td>
            <td class="bordaToda">Hora de Fechamento:</td>
        </tr>
        <tr>
            <td class="bordaToda">Nº Matrícula:</td>
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
            <td class="respTecnico">Responsável Técnico CLIENTE:</td>
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