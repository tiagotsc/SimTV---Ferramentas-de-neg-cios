<style>
    .money{
        width: 100px;
    }
</style>

<div class="modal fade" id="cadastra_vale" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Cadastra Valor Vale</h4>
      </div>
      <div class="modal-body">
        <?php
            $attr = ['id'=>'cadastro_vale'];
            echo form_open('rh-beneficio/administracao/salvaVale',$attr);
                echo '<div class="col-md-5 ">';
                    echo form_label('Vale Refeicao - Valor Dia');
                    $dado = ['class'=>'form-control money','name'=>'VR'];
                    echo form_input($dado);
                echo '</div>';
                echo '<div class="col-md-5">';
                    echo form_label('Vale Alimentacao - Valor Mes');
                    $dados = ['class'=>'form-control money','name'=>'VA'];
                    echo form_input($dados);
                echo '</div>';
            echo form_close();
        ?>
          <div class="row"></div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_cadastro" class="btn btn-primary">Cadastra</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->