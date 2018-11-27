<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Sim TV - Intera&ccedil;&atilde;o de viabilidade</title>
  
<?php
# Bootstrap core CSS
echo link_tag(array('href' => 'assets/css/bootstrap.css','rel' => 'stylesheet','type' => 'text/css'));

# JavaScript
echo "<script type='text/javascript' src='".base_url('assets/js/jquery-1.10.2.js')."'></script>";
echo "<script type='text/javascript' src='".base_url('assets/js/bootstrap.js')."'></script>";

echo link_tag(array('href' => 'assets/js/jquery-ui/jquery-ui.css','rel' => 'stylesheet','type' => 'text/css'));
echo "<script type='text/javascript' src='".base_url("assets/js/jquery-ui/jquery-ui.js")."'></script>";

# Css para personalização
echo link_tag(array('href' => 'assets/css/personalizado.css','rel' => 'stylesheet','type' => 'text/css')); 

?>

</head><!--/head-->
<body>

    <!-- INÍCIO Modal Apaga registro -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar intera&ccedil;&atilde;o?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open($modulo.'/'.$controller.'/deleta',$data);
                        
                            echo form_label('Autor', 'apg_nome');
                    		$data = array('id'=>'apg_nome', 'name'=>'apg_nome', 'readonly'=>'readonly', 'class'=>'form-control');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_id" name="apg_id" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga registro de telecom -->

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12"> 
                <a id="hist-viab-fecha" href="#"><strong>FECHAR</strong></a>
                <?php  
                if(!$statusFinal or in_array($perForcaAlt, $this->session->userdata('permissoes')) ){               
                    if(in_array($perCadEdit, $this->session->userdata('permissoes'))){ ?>
                <a id="hist-viab-novo" href="<?php echo base_url($modulo.'/'.$controller.'/frm/'.$id); ?>"><strong>NOVO</strong></a>
                <?php 
                    } 
                }                
                ?>
            </div>
        </div>
        <?php echo $this->session->flashdata('statusOperacao'); ?>
        <div id="hist-via_cabecalho" class="row">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <th>Controle</th>
                        <th>N&uacute;mero da solicita&ccedil;&atilde;o</th>
                        <th>Permissor</th>
                        <th>Tipo de solicita&ccedil;&atilde;o</th>
                    </tr>
                    <tr>
                        <td><?php echo $dadosViab->controle; ?></td>
                        <td><?php echo $dadosViab->n_solicitacao; ?></td>
                        <td><?php echo $dadosViab->permissor.' - '.htmlentities($dadosViab->unidade); ?></td>
                        <td><?php echo htmlentities($dadosViab->tipo); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-12"> 
            <table class="table">
            <tr>
            <?php 
            foreach($campos as $nome => $display){
                echo '<th>'.htmlentities($display).'</th>'; 
            } 
            ?>
            </tr>
            <?php 
            $cont = 0;
            foreach($dados as $da){ 
                
                $class = ($cont%2 == 0)? 'hist-viab-table-zebra': '';
                
                echo '<tr class="'.$class.'">';
                echo '<td>'.$this->util->formataData($da->data_cadastro,'BR').'</td>';
                echo '<td>'.htmlentities($da->nome).'</td>';
                if($da->anexo){
                    $link = '<a href="'.base_url($dirDownload).'/'.$da->anexo.'" target="_blank">'.$da->anexo_label.'</a>';
                }else{
                    $link = '';
                }
                echo '<td>'.$link.'</td>';
                echo '<td>'.$da->nome_usuario.'</td>';
                echo '</tr>';
                echo '<tr class="'.$class.'">';
                echo '<td colspan="4"><p class="text-left">'.nl2br(htmlentities($da->observacao)).'</p></td>';
                echo '</tr>';
                echo '<tr class="'.$class.'">';
                echo '<td id="hist-via-separador" colspan="4">';
                
                // Se não estiver statusFinaldo (Não é status final) ou se o usuário tiver permissão para forçar alteração
                if(!$statusFinal or in_array($perForcaAlt, $this->session->userdata('permissoes')) ){
                // Se o usuário for o autor e tiver permissão para alterar ou se o usuário tiver permissão para forçar alteração
                    if(
                        (($da->cd_usuario == $this->session->userdata('cd')) 
                        and (in_array($perCadEdit, $this->session->userdata('permissoes')))) 
                        or (in_array($perForcaAlt, $this->session->userdata('permissoes')))){
                        echo '<span class="float-left"><a href="'.base_url($modulo.'/'.$controller.'/frm/'.$id.'/'.$da->id).'">EDITAR</a></span>';
                    }
                    
                    if(
                        (($da->cd_usuario == $this->session->userdata('cd')) 
                        and (in_array($perDeletar, $this->session->userdata('permissoes')))) 
                        or (in_array($perForcaAlt, $this->session->userdata('permissoes')))){
                        echo '<span class="float-right"><a class="del" title="Apagar" href="#" data-toggle="modal" data-target="#apaga" key="'.$da->id.'" nome="'.$da->nome_usuario.' &agrave;s '.$this->util->formataData($da->data_cadastro,'BR').'">APAGAR</a></span>';
                    }
                }
                echo '</td>';
                echo '</tr>';
                $cont++;
            }    
            ?>
            </table>
            </div>
        </div>
    </div>
		
<script>   

$(".del").click(function(){
   $("#apg_id").val($(this).attr('key'));
   $("#apg_nome").val($(this).attr('nome'));
});

$("#hist-viab-fecha").click(function(){
   window.close(); 
}); 
   
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

$(document).ready(function() {
   
});

</script>    
</body>
</html>