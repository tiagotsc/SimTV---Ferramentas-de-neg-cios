<style>
    
    .matricula{
        width: 75px;
        text-align: center;
    }
    
    .total{
        width: 70px;
        text-align: center;
    }
    
    .passagem{
        width: 60px;
        text-align: center;
    }
    
    .dia{
        width: 60px;
        text-align: center;
    }
    
    .botao{
        margin-top: -20;
    }
    
    #salvaArquivo{
        color: #00008B;
        font-size: 30px;
    }
    
    
</style>


<?php
    echo link_tag(array('href' => 'assets/js/drag_drop/style.css','rel' => 'stylesheet','type' => 'text/css'));
    #echo "<script type='text/javascript' src='".base_url('assets/js/jquery.blockui/jquery.block.ui.js')."'></script>";
?>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <script type="text/javascript" src="<?php echo base_url("assets/js/drag_drop/fieldChooser.js") ?>"></script>

    <div class="col-lg-offset-1 col-md-10">
        
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
            <li><a href="<?php echo base_url('rh/rh')?>">RH</a></li>            
            <li class="active">Compra beneficio alelo</li>
        </ol>
        
        <div id="divMain">
            <?php
                echo $this->session->flashdata('statusOperacao');
                
                $totRegistro = count($valeTransporte);
            
                $titulo = 'Compra Vale Transporte';
                $nome = (isset($nome))? $nome: false;
                $status = (isset($status))? $status: false;
                
                echo '<div id="formPesq">';
                    $data = array('class'=>'pure-form','id'=>'pesquisar');
                    echo form_open($data);
                        echo form_fieldset($titulo.$botaoCadastrar, $attributes);

                            echo '<div class="row">';
                                echo '<div id="regional" class="col-md-4">';
                                    $options = array('' => '', '15' => 'Icarai');
                                    foreach($unidade as $uni){
                                            $options[$uni->cd_unidade] = htmlentities($uni->nome);
                                    }	
                                    echo form_label('Unidade', 'cd_unidade');
                                    echo form_dropdown('cd_unidade', $options, $cd_unidade, 'id="cd_unidade" class="form-control"');
                                echo '</div>';

                                echo '<div id="mesCompra" class="col-md-2">';
                                    echo form_label('Mes', 'mes_compra');
    //                                echo form_input('mes_compra',$t,'id="feriados" type="number" class="form-control"');
                                    echo '<input id="mesCompraBeneficio" type="number" class="form-control" value=".$t." name="mesCompraBeneficio"';
                                echo '</div>';

    //                            echo '<div class="col-md-1 marginTop25">';
    //                                echo form_submit("btn_gerar","Gerar", 'class="btn btn-primary pull-right"');
    //                            echo '</div>';
                            echo '</div>';
                        echo '</div>';    
                        echo form_fieldset_close();
                    echo form_close();
                echo '</div>';
            ?>
                
                <div id="exibicaoFeriados" class="row well col-md-12 marginTop10">
                    
                </div>
            
                <div id="previewArquivo" class="row col-md-12 marginTop10">
                    <div>                        
                        <div class="row well col-md-12">
                            <p>
                                <div class="col-md-5">
                                    <strong>Beneficios neste arquivo: </strong><strong id="totalBeneficiosCompra"></strong>
                                </div>
                                
                                <div class="col-md-5">
                                    <strong>Valor total da compra: </strong>
                                    <strong id="valorTotalArquivo">
                                        
                                    </strong>
                                </div>
                            </p>
                        </div>
                        
                        <div class="row well col-md-12">
                            <?php
                            $data = array('id'=>'geraArquivo');
                                echo form_open('rh-beneficio/beneficio/geraArquivo',$data);
                                    echo '<input id="regionalValue" type="hidden" name="regionalValue" value=""></input>';
                                    echo '<div>';
                                        echo '<a href="#"><span id="salvaArquivo" class="glyphicon glyphicon-floppy-save pull-right"></span></a>';
                                    echo '</div>';
                                    echo '<div class="table-responsive">';
                                        echo '<table class="table">';
                                            echo '<thead>';
                                                echo '<tr>';
                                                    echo '<th class="matricula">Matricula</th>';
                                                    echo '<th>Nome</th>';
                                                    echo '<th class="dia">Dias</th>';
                                                    echo '<th class="dia"><span class="dataExtra glyphicon glyphicon-plus"></span></th>';
                                                    echo '<th class="dia"><span class="dataExtra glyphicon glyphicon-minus"></span></th>';
                                                    echo '<th class="passagem">Valor</th>';
                                                    echo '<th class="total">Total</th>';
                                                echo '</tr>';
                                            echo '</thead>';
                                            echo '<tbody id="corpoTabela">';
    //                                            
                                            echo '</tbody>';
                                        echo '</table>';
                                    echo '</div>';
                                echo form_close();
                            ?>
                        </div>
                    </div>
                </div>
        
        </div>
    </div>
                        
    
<script type="text/javascript">
        
    $(document).ready(function(){
        $(".data").mask("00/00/0000");
        $("#previewArquivo").hide();
        $("#mesCompra").hide();
        $('#exibicaoFeriados').hide();
    });
    
    
    $(".data").datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Pr&oacute;ximo',
	prevText: 'Anterior',
    
        // Traz o calend�rio input datepicker para frente da modal
        beforeShow :  function (){
            setTimeout ( function (){
                $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 );
            },  0 );
        }
    });
    
    
    $("#cd_unidade").on("change",function(){
        $('#mesCompra').show('slow');
    });
    
    $("#salvaArquivo").click(function (){
        event.preventDefault();
        $('#geraArquivo').submit(); 
    });
    
    $("#mesCompraBeneficio").on("keyup",function(){
        if($(this).val().length > 1){
            
            $.ajax({
              type: "POST",
              url: '<?php echo base_url(); ?>rh-beneficio/ajaxBeneficio/dados',
              data: {
                cd_unidade: $("#cd_unidade").val(),
                mesCompraBeneficio: $("#mesCompraBeneficio").val(),
              },
              dataType: "json",

              success: function(res) {
                    var tr;
                    var feriados = '';
                    var tot = res['compraValeTransporte'].length;
                    var totValor = 0;
                    
                    $('#regionalValue').val(res['unidade']);
                    
                        $.fn.myFunction = function (){

                            totValor = 0;
                            $('.total').each(function(index){
                               if(index < tot){
                                    totValor += parseFloat($('#total_'+index).val());
                               }
                            });

                        };
                    
                    
                    
                    
                    
                        $.each(res['compraValeTransporte'],function(index){
                            var total = Number(this.diasUteis) * (this.valor*2);

                            tr+= '<tr>';
                                tr+= '<td><input id="matricula_'+index+'" name="matricula['+index+']" class="form-control matricula" type="text" value="'+this.matricula_usuario+'" readonly></input></td>';
                                tr+= '<td><input id="nome_'+index+'" name="nome['+index+']" class="form-control" type="text" value="'+this.nome_usuario+'" readonly></input></td>';
                                tr+= '<td><input id="dias_'+index+'" name="dias['+index+']" class="form-control dia" type="text" value="'+this.diasUteis+'" readonly></input></td>';
                                tr+= '<td><input key="'+index+'" id="acrescimos_'+index+'" name="acrescimos['+index+']" class="form-control dia dataExtraSoma" type="number" ></input></td>';
                                tr+= '<td><input key="'+index+'" id="descontos_'+index+'" name="descontos['+index+']" class="form-control dia dataExtraSubtracao" type="number" ></input></td>';
                                tr+= '<td><input id="valorPassagem_'+index+'" name="valorPassagem['+index+']" class="form-control passagem" type="text" value="'+this.valor+'" readonly></input></td>';
                                tr+= '<td><input id="total_'+index+'" name="total['+index+']" class="form-control total" type="text" value="'+total.toFixed(2)+'" readonly></input></td>';
                                tr+= '<td><input id="id_'+index+'" type="hidden" name="cpf['+index+']" value="'+this.cpf+'"></input></td>';
                            tr+= '</tr>';
                            totValor+= Number(total);
                        });
                        
                        if(res['feriado'] == null){
                        
                            feriados+= '<div class="col-md-4">';
                                feriados+= '<strong>nenhum feriado neste mes<strong>';
                            feriados+= '</div>';
                        
                        }else{
                            
                            $.each(res['feriado'],function(){

                                    feriados+= '<div class="col-md-4">';

                                        feriados+= '<ul type="none">';
                                            feriados+= '<li><strong>Data: '+this.data+'</strong></li>';
                                            feriados+= '<li><strong>Descricao: '+this.descricao+'</strong></li>';
                                        feriados+= '</ul>';
                                    feriados+= '</div>';

                            });
                            
                        }
                                               
                        $('#corpoTabela').html(tr);
                        
                        $(".dataExtraSoma").keyup(function(){
                            
                            var valPassagem = $('#valorPassagem_'+ $(this).attr('key')).val() * 2;
                            var valDias = $('#dias_'+ $(this).attr('key')).val();
                            var acrescimo = $('#acrescimos_' + $(this).attr('key')).val();
                            
                            acrescimo = (acrescimo.length > 0)? parseInt(acrescimo): 0;
                                
//                            totValor+= valPassagem * parseInt(acrescimo);
                            
                            var sumDias = parseInt(valDias) + parseInt(acrescimo);
                            var tot = sumDias * valPassagem;
                            $('#total_' + $(this).attr('key')).val(tot.toFixed(2));
                            
                            $(this).myFunction();
                            $('#valorTotalArquivo').html(totValor.toFixed(2));
//                            
                        });
                        
                        $(".dataExtraSubtracao").keyup(function(){
                             
                            var valPassagem = $('#valorPassagem_'+ $(this).attr('key')).val() * 2;
                            var valDias = $('#dias_'+ $(this).attr('key')).val();
                            var descontos = $('#descontos_' + $(this).attr('key')).val();
                            
                            descontos = (descontos.length > 0)? parseInt(descontos): 0;
                                
//                            totValor-= valPassagem * parseInt(descontos);
                            
                            var sumDias = parseInt(valDias) - parseInt(descontos);
                            var tot = sumDias * valPassagem;
                            $('#total_' + $(this).attr('key')).val(tot.toFixed(2));
                            
                            $(this).myFunction();
                            $('#valorTotalArquivo').html(totValor.toFixed(2));
                            
                         });
                        
                        
                        
//                        $("#formPesq").hide('slow');
                        $('#exibicaoFeriados').html(feriados);
                        
                        $('#totalBeneficiosCompra').html(tot);
                        $('#valorTotalArquivo').html(totValor.toFixed(2));
                        
                        

                        $('#previewArquivo').hide();
                        $('#exibicaoFeriados').hide();
                        
                        $('#previewArquivo').fadeIn('slow');
                        $('#exibicaoFeriados').fadeIn('slow');
                         
              }
            });
            
        }
    });

</script>