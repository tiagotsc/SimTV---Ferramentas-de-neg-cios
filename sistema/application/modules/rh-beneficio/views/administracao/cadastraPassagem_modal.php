<style>
    #bilheteUnico{
        width: 17px;
        height: 17px;
        margin-left: 40px;
    }
    
    #addBtn{
        font-size: 25px;
    }
    
</style>

<div class="modal fade" id="cadastraPassagem" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastro de Passagem</h4>
        </div>
        
        <div class="modal-body">
            
            <legend>Passagem Info<a href="#"><span id="addBtn" class="glyphicon glyphicon-plus pull-right"></span></a></legend>
            
            <form id="form" action="/sistema/rh-beneficio/administracao/cadastraPassagem" method="POST">
                
                <div id="passForm">
                    
                    <input type="hidden" name="cd_unidade" id="cdUnidadeModal" value="">
                    
                    <div class="col-md-10">

                        <div class="col-md-4">
                            <label>Bilhete Unico</label>
                            <input key="0" name='passagem_0[bilheteUnico]' id="bilheteUnico" type="checkbox" value="true" class="bilhete">
                        </div>

                        <div class="col-md-4">
                            <label>Qdt. Passagens</label>
                            <input name="passagem_0[qdtPassagem]" id="passagem_0" type="text" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Valor Passagem</label>
                            <input name="passagem_0[valorPassagem]" type="text" class="valorPassagem form-control">
                        </div>

                    </div>
                    
                    <div class="row"></div><br>
                    
                </div>
                
            </form>
 
        </div>
        
        <div class="modal-footer">
            <button id="btnCadastraPassagem" type="button" class="btn btn-primary">Salvar</button>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    
    $('input:checkbox').css('width', '17px');
    
    $('.valorPassagem').mask('0.000.00',{reverse: true});
    
    $('#btnCadastraPassagem').click(function(){
        $('#form').submit();
    });
    
    $('.bilhete').click(function(){
        if($(this).prop('checked')== true){
            $('#passagem_'+$(this).attr('key')).prop('disabled', true);
        }else{
            $('#passagem_'+$(this).attr('key')).prop('disabled', false);
        }
    });
    
    
    
    $('#form').validate({
        debug: false,
        rules:{
            'valor[2]': {
                required: true
            }
        },
        messages:{
            'valor[2]': {
                required: "campo obrigatorio."
            }
        }
    });
    var index = 1;
    
    $('#addBtn').click(function(){
        var passagemDiv = '<div class="col-md-10"><div class="col-md-4"><label>Bilhete Unico</label>'+
                        '<input key="'+index+'" name="passagem_'+index+'[bilheteUnico]" id="bilheteUnico" class="bilhete" type="checkbox" value="true">'+
                        '</div><div class="col-md-4"><label>Qdt. Passagens</label><input name="passagem_'+index+'[qdtPassagem]" id="passagem_'+index+'" type="text" class="form-control">'+
                        '</div><div class="col-md-4"><label>Valor Passagem</label><input name="passagem_'+index+'[valorPassagem]" type="text" class="valorPassagem form-control"></div></div>'+
                        '<div class="row"></div><br>';
        
        $('#passForm').append(passagemDiv);
        
        $('.bilhete').click(function(){
            if($(this).prop('checked')== true){
                $('#passagem_'+$(this).attr('key')).prop('disabled', true);
            }else{
                $('#passagem_'+$(this).attr('key')).prop('disabled', false);
            }
        });
        
        $('.valorPassagem').mask('0.000.00',{reverse: true});
        
        index++;
    });
    
    
    
    
</script>