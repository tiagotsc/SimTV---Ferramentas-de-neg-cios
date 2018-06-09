<style>
    .valoresPassagem{
        text-align: center;
    }
    .passagensInfo{
        width: 60px;
        text-align: center;
    }
    .th{
        width: 60px;
        text-align: center;
    }
</style>


<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>

    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js"); ?>"></script>
        
    <div class="col-lg-offset-1 col-md-10">
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh') ?>">RH</a></li>            
            <li class="active">Passagem</li>
        </ol>
    </div>
    <?php include_once 'editaPassagem_modal.php';?>
    <?php echo $this->session->flashdata('statusOperacao');?>
    
    <div class="col-md-offset-1 col-md-10">
        <?php
            echo form_open('#');
                echo form_fieldset('Unidades');
                    echo '<div class="col-md-4">';
                        $opc = array('' => '', '15' => 'Icarai');
                        foreach($unidade as $uni){
                            $opc[$uni->cd_unidade] = htmlentities($uni->nome);
                        }	
                        echo form_label('Unidades','cd_unidade');
                        echo form_dropdown('cd_unidade',$opc,'', 'id="cd_unidade" class="form-control"');
                    echo '</div>';
                echo form_fieldset_close();
            echo form_close();
        ?>
    </div>

    <div class="row">&nbsp</div>
    <div id="passagensInfo" class="col-md-offset-1 col-md-10 marginTop25">
        <legend>Passagens&nbsp<a class="pull-right" href="#" id="cadastraPassagem"><span style="font-size: 30px" class="glyphicon glyphicon-plus"></span></a></legend>    
        <div class="well">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="valoresPassagem">Quantidade</th>
                        <th class="valoresPassagem">Valor</th>
                        <th class="valoresPassagem">Data cadastro</th>
                        <th class="valoresPassagem">Data desativacao</th>
                    </tr>
                </thead>
                <tbody id="corpoTabela">

                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        
        $(document).ready(function(){
            $('#passagensInfo').hide();
        });
        
        $( "#cadastraPassagem").on("click", function(event){
            var unidade = $("#cd_unidade").val();
            
            $.post('#',{unidade: unidade},function(retorno){
                $('#editarPassagem').modal('toggle');
                $('#cdUnidadeModal').val(unidade);
            });
                
        });
        
        $("#cd_unidade").on('change',function(){
        
            $.ajax({
                type:"POST",
                url: '<?php echo base_url(); ?>rh-beneficio/ajaxAdministracao/dados',
//                url: 'http://sistemas-teste.simtv.com.br/sistema/rh-beneficio/ajaxAdministracao/dados',
                data:{
                    cd_unidade: $("#cd_unidade").val()
                },
                dataType: "json",
                success:function(data){
                    var td = '';
                    var hid = '';
                    
                    $('#passagensInfo').hide();
                    
                    $.each(data['passagens'],function(index){
                    
                        td += '<tr>';
                            td += '<td><p id="numeroPassagem_'+index+'" class="valoresPassagem">'+((this.passagens === null)?'Bilhete Unico':this.passagens)+'</p></td>';
                            td += '<td><p id="valor_'+index+'" class="valoresPassagem">'+this.valor+'</p></td>';
                            td += '<td><p id="dataCadastro_'+index+'" class="valoresPassagem">'+this.data_cadastro+'</p></td>';
                            td += '<td><p id="dataDesativacao_'+index+'" class="valoresPassagem">'+((this.data_desativacao===null)?'--/--/----':this.data_desativacao)+'</p></td>';
                        td += '</tr>';
                    });
                    
//                    hid += '<input type="hidden" name="cd_unidade" value="">';
                    
                    
                    $('#corpoTabela').html(td);
//                    $('#passagensInfo').html(hid);
                    $('#passagensInfo').fadeIn('slow');
                }
            });
        });
        
    </script>