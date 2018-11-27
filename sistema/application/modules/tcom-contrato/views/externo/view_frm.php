<?php
echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
#echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
echo link_tag(array('href' => 'assets/css/tooltip.css','rel' => 'stylesheet','type' => 'text/css'));
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

            <!--<div class="col-md-10 col-sm-9">-->
            <div class="col-lg-12">
                
                <div id="divMain">
                    <?php echo $this->session->flashdata('statusOperacao'); ?>
                    <h4>Telecom - Análise financeira</h4>
                    <table class="table">
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
                    
                    <table class="table table-bordered">
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
                            <td class="text-center">
                            <?php if($this->session->userdata('cd') == $resp->cd_usuario){ ?>
                            <a href="<?php echo base_url('tcom-contrato/contratoExterno/analiseReposta/'.md5($contrato->id).'/S'); ?>" class="glyphicon glyphicon-thumbs-up" title="Aprovado"></a>
                            <a href="<?php echo base_url('tcom-contrato/contratoExterno/analiseReposta/'.md5($contrato->id).'/N'); ?>" class="glyphicon glyphicon-thumbs-down" title="Reprovado"></a>
                            Resposta = 
                            <?php } ?>
                            <?php if($resp->aprovado == 'S'){ ?>
                            <a href="#" class="glyphicon glyphicon-thumbs-up" title="Aprovado"></a>
                            <?php }elseif($resp->aprovado == 'N'){ ?>
                            <a href="#" class="glyphicon glyphicon-thumbs-down" title="Reprovado"></a>
                            <?php } ?>

                            </td>
                            <td class="text-center"><?php echo $resp->data_aprovacao; ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                  
                </div>
            </div>
        
    
<script type="text/javascript">

$(function() {
$( document ).tooltip({
  position: {
    my: "center bottom-20",
    at: "center top",
    using: function( position, feedback ) {
      $( this ).css( position );
      $( "<div>" )
        .addClass( "arrow" )
        .addClass( feedback.vertical )
        .addClass( feedback.horizontal )
        .appendTo( this );
    }
  }
});
});

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}


$(document).ready(function(){
    
    //$(".data").mask("00/00/0000");
    $(".navbar-brand").attr('href','#');
    //$(".navbar-brand").removeAttribute('href');
    
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data,#data2").datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Pr&oacute;ximo',
	prevText: 'Anterior',
    
    // Traz o calendário input datepicker para frente da modal
    beforeShow :  function ()  { 
        setTimeout ( function (){ 
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
        },  0 ); 
    } 
});

</script>