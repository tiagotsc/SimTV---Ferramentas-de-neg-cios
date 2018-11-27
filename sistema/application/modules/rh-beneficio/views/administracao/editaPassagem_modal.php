<div class="modal fade" id="editaPassagem" tabindex="-1" role="dialog">
    
    <div class="modal-dialog modal-md" role="document">
        
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Altera passagem</h4>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-offset-1 col-md-10">
                        <!--<form id="alteraPassagem" action="<?php echo base_url('http://sistemas-teste.simtv.com.br/sistema/rh-beneficio/administracao/alteraVale')?>" method="post">-->
                        <form id="alteraPassagem" action="alteraVale" method="post">
                            <input type="hidden" name="idPassagemAntiga" id="idPassagemAntiga">
                            <table class="table table-striped">
                                <thead>
                                    <th>Quantidade</th>
                                    <th>Valor</th>
                                    <th>Opcao</th>
                                </thead>
                                <tbody id="corpoTableEditaPassagemModal">

                                </tbody>
                            </table>
                        </form>    
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button id="salvaAlteracao" type="button" class="btn btn-primary">Salvar</button>
            </div>
            
        </div><!-- /.modal-content -->
        
    </div><!-- /.modal-dialog -->
    
</div><!-- /.modal -->