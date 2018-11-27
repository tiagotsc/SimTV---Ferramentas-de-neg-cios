<?php 

if($this->session->userdata('telefonia') == 'movel'){
    $activeMovel = 'active';
    $activeFixo = '';
}elseif($this->session->userdata('telefonia') == 'fixo'){
    $activeMovel = '';
    $activeFixo = 'active';
}else{
    $activeMovel = '';
    $activeFixo = '';
}
?>
    <!-- Page Content -->
     <!-- INÍCIO Modal Termo -->
    <div class="modal fade" id="view_termo" tabindex="-1" role="dialog" aria-labelledby="view_termo" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                    <h4 class="modal-title" id="myModalLabel">Termos</h4>
                </div>
                <div class="modal-body row">
                    <div id="tabs">
                      <ul>
                        <li><a href="#tabs-1">Declara&ccedil;&atilde;o</a></li>
                        <li><a href="#tabs-2">Regulamento</a></li>
                      </ul>
                      <div class="view-declaracao" id="tabs-1">
                      </div>
                      <div class="view-regulamento" id="tabs-2">
                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="view-aceite">
                    <!--<strong>Status: ACEITO</strong> - <strong>DATA: </strong> 12/12/2015-->
                    </div>
                    <div id="pre-confirm">
                        <button type="button" id class="btn btn-default" data-dismiss="modal">Fechar</button>
                        <button type="button" id="nao-concordo" class="btn btn-primary">N&atilde;o Concordo</button>
                        <button type="button" id="concordo" class="btn btn-primary">Concordo</button>
                    </div>
                    <div id="confirm">
                        <?php
                        $data = array('class'=>'pure-form','id'=>'termo-concordo');
                    	echo form_open('telefonia/termoSalvaResposta',$data);
                        ?>
                        <p id="questao-confirm"></p>
                        <input type="hidden" id="res_concordo" name="res_concordo" value="" />
                        <input type="hidden" id="cd_emprestimo_res_termo" name="cd_emprestimo_res_termo" value="" />
                        <button type="button" id="nao-confirma" class="btn btn-primary">N&atilde;o</button>
                        <button type="submit" class="btn btn-primary">Sim</button>
                        <?php
                        echo form_close(); 
                        ?>
                    </div>
                </div>
        </div>
      </div>
    </div>
    <!-- FIM Modal Termo -->   

    <div class="container">
        <div class="row">
            <div class="col-md-2 col-sm-3 sidebar">
                <ul class="nav nav-stacked nav-pills">
                <?php 
                foreach($menuLateral as $mL){
                    $sublinhado = ($_SERVER['REDIRECT_QUERY_STRING'] == $mL->link or $this->session->userdata('menuLateral') == $mL->link)? 'style="text-decoration: underline; font-weight: bold"': '';
                ?>
                    <?php if($mL->cd_menu_lateral == 1){ ?>
                        <li><a href="<?php echo base_url($mL->link); ?>"><?php echo htmlentities($mL->nome); ?></a></li>
                    <?php }elseif($mL->cd_menu_lateral == 2 and $this->session->userdata('telefonia') != 'fixo'){ ?>
                        <li class="<?php echo $activeMovel; ?>"><a href="<?php echo base_url($mL->link); ?>"><strong><?php echo htmlentities($mL->nome); ?></strong></a></li>
                    <?php }elseif($mL->cd_menu_lateral == 3 and $this->session->userdata('telefonia') != 'movel'){ ?>
                        <li class="<?php echo $activeFixo; ?>"><a href="<?php echo base_url($mL->link); ?>"><strong><?php echo htmlentities($mL->nome); ?></strong></a></li>
                    <?php }elseif(($mL->tipo == $this->session->userdata('telefonia') or $mL->tipo == 'N') and in_array($this->session->userdata('telefonia'), array('movel', 'fixo')) ){ ?>
                        <li><a <?php echo $sublinhado; ?> href="<?php echo base_url($mL->link); ?>"><?php echo htmlentities($mL->nome); ?></a></li>
                    <?php } ?>
                <?php 
                }
                ?>
                </ul>
                <!--
                <ul class="nav nav-stacked nav-pills"> 
                    <li><a href="<?php echo base_url('telefonia'); ?>">In&iacute;cio</a></li> 
                    <?php if($this->session->userdata('telefonia') != 'fixo'){ ?> 
                    <li class="<?php echo $activeMovel; ?>"><a href="<?php echo base_url('telefonia/movel'); ?>"><strong>M&Oacute;VEL</strong></a>
                    <?php } ?>
                    <?php if($this->session->userdata('telefonia') != 'movel'){ ?>
                    <li class="<?php echo $activeFixo; ?>"><a href="<?php echo base_url('telefonia/fixo'); ?>"><strong>FIXO</strong></a> 
                    <?php } ?> 
                    
                    <?php if($this->session->userdata('telefonia') == 'movel'){ ?>           
                    <li><a href="<?php echo base_url('telefonia/operadoras'); ?>">Operadora</a></li>                    
                    <li><a href="<?php echo base_url('telefonia/planos'); ?>">Plano</a></li>
                    <li><a href="<?php echo base_url('telefonia/servicos'); ?>">Servi&ccedil;o</a></li> 
                    <?php } ?> 
                    <?php if($this->session->userdata('telefonia') == 'movel' or $this->session->userdata('telefonia') == 'fixo'){ ?>                  
                    <li><a href="<?php echo base_url('telefonia/linhas'); ?>">Linha</a></li>                    
                    <?php } ?> 
                    <?php if($this->session->userdata('telefonia') == 'movel'){ ?>  
                    <li><a href="<?php echo base_url('telefonia/aparelhos'); ?>">Aparelho</a></li>                                       
                    <li><a href="<?php echo base_url('telefonia/emprestimos'); ?>">Empr&eacute;stimo</a></li>
                    <?php } ?>
                    <?php if($this->session->userdata('telefonia') == 'movel' or $this->session->userdata('telefonia') == 'fixo'){ ?>
                    <li><a href="<?php echo base_url('telefonia/faturas'); ?>">Fatura</a></li>   
                    <?php } ?>                
                </ul>
                -->
        </div>
<script type="text/javascript">

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$(function() {
    $( "#tabs" ).tabs();
});

$("#nao-concordo").click(function(){
    $("#pre-confirm").css('display', 'none');
    $("#confirm").css('display', 'block');
    $("#questao-confirm").html('<strong>Tem certeza que n&atilde;o aceita os termos acima?</strong>');
    $("#res_concordo").val('N');
});

$("#concordo").click(function(){
    $("#pre-confirm").css('display', 'none');
    $("#confirm").css('display', 'block');
    $("#questao-confirm").html('<strong>Eu li e concordo com os termos e condi&ccedil;&otilde;es descritas acima?</strong>');
    $("#res_concordo").val('S');
});

$("#nao-confirma").click(function(){
    $("#pre-confirm").css('display', 'block');
    $("#confirm").css('display', 'none');
    $("#questao-confirm").html('');
    $("#res_concordo").val('');
});

function viewTermo(cd_termo, cd_operadora, cd_usuario){

    $("#cd_emprestimo_res_termo").val(cd_termo);
    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>ajax/termoUsuario',
      data: {
        cd_emprestimo: cd_termo,
        cd_usuario: cd_usuario,
        cd_operadora: cd_operadora
      },
      dataType: "json",
      error: function(res) {
        alert('erro');
      },
      success: function(res) { 
        if(res['declaracao'] != '' && res['regulamento'] != ''){
            $(".view-declaracao").html(res['declaracao']);
            $(".view-regulamento").html(res['regulamento']);
            
            if(res['respondido'] == true){
                $("#view-aceite").css('display', 'block');
                $("#view-aceite").html(res['dataResposta']);
                $("#nao-concordo").css('display', 'none');
                $("#concordo").css('display', 'none');                
            }else{
                $("#view-aceite").css('display', 'none');
                $("#view-aceite").html('');
                $("#nao-concordo").css('display', 'inline');
                $("#concordo").css('display', 'inline');                                
            }
            
        }
      }
    });
 
}

$(document).ready(function(){
    
    $("#confirm").css('display', 'none');
    $("#view-aceite").css('display', 'none');
});
</script>