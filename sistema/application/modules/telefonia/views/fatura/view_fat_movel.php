<?php
# Dropzone drag and drop de upload de imagens
#echo link_tag(array('href' => 'assets/js/dropzone/basic.css','rel' => 'stylesheet','type' => 'text/css')); 
echo link_tag(array('href' => 'assets/js/dropzone/dropzone.css','rel' => 'stylesheet','type' => 'text/css')); 
echo "<script type='text/javascript' src='".base_url('assets/js/dropzone/dropzone.js')."'></script>";
#echo "<script type='text/javascript' src='".base_url('assets/js/dropzone/dropzone-amd-module.js')."'></script>";
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
            <div class="col-md-10 col-sm-9">
            <!--<div class="col-lg-12">-->
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a></li>
                    <li><a href="<?php echo base_url('telefonia'); ?>">Telefonia</a></li>
                    <li class="active">Faturas</li>
                </ol>
                <div id="divMain">
                    <h4>Quadro de upload de arquivos</h4>
                    <form action="<?php echo base_url('telefonia/faturaproc/uploadArquivos'); ?>" id="my-dropzone" class="dropzone">
                        <div class="fallback">
                        <input name="file" type="file" multiple />
                        </div>  
                    </form>   
                    <?php
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'formulario');
                    	echo form_open('#',$data);
                            $attributes = array('id' => 'frm_fat_proc', 'class' => 'address_info');
                            
                    		echo form_fieldset("Processar fatura", $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-4">';
                                    foreach($operadoras as $ope){
                                        $options[$ope->cd_telefonia_operadora] = $ope->nome;
                                    }		
                            		echo form_label('Selecione a operadora / Tipo', 'tipo');
                            		echo form_dropdown('tipo', $options, '', 'id="tipo" class="form-control"');
                                    echo '</div>';
 
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                $data = array(
                                    'name' => 'button',
                                    'id' => 'button',
                                    'value' => 'true',
                                    'type' => 'button',
                                    'class'=> "btn btn-primary pull-right",
                                    'content' => 'Enviar'
                                );
                                
                                echo form_button($data);
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>     
                </div>
                <br />
                <div id="tempoReal"></div>
                
                <div class="row">&nbsp</div>
                <?php
                $this->table->set_heading('&Uacute;ltimos Arquivos processados', 'Fonte');
                
                foreach($ultimosArquivos as $uA){
                    
                    $cell1 = array('data' => $this->util->formataData($uA->data, 'BR'));
                    $cell2 = array('data' => $uA->fonte);
                    
                    $this->table->add_row($cell1, $cell2);
                    
                }
                
                $template = array('table_open' => '<table class="table zebra">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                ?>
                
            </div>
    
<script type="text/javascript">

$("#button").click(function(){

    $.post( '<?php echo base_url(); ?>telefonia/faturaproc/processaFatura', { tipo: $("#tipo").val(), processar: 'sim'} );
    
    var intervalo = window.setInterval(
        function(){ 
            $.post( '<?php echo base_url(); ?>ajax/feedbackTempoReal', function( data ) {
              //console.log( data.time ); // 2pm
              $("#tempoReal").html(data.feedback);
              
              if(data.feedbackStatus == 'finalizado'){
                $.post( '<?php echo base_url(); ?>ajax/feedbackTempoRealLimpa' );
                window.clearInterval(intervalo);
                $(".dz-preview").remove();
                //$(location).attr('href', '<?php echo base_url(); ?>telefonia/faturasArquivos');
              }
              
            }, "json");
        }    
    ,1000);


});

$(document).ready(function(){

$("#frm_fat_proc").hide();
       
// Valida o formulário
$("#formulario").validate({
	debug: false,
	rules: {
		tipo: {
            required: true
        },
        userfile: {
            required: true
        }
	},
	messages: {
		tipo: {
            required: "Selecione o tipo."
        },
        userfile: {
            required: "Selecione o arquivo"
        }
   }
});   
    
    //$("#btn_processar").css('display', 'none');
    
    $(".data").mask("00/00/0000");
    
    /*$(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });*/
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
var baseUrl = '<?php echo base_url(); ?>';
/* ANEXA ARQUIVOS NO CHAT ATRAVÉS DE DRAG AND DROP */
Dropzone.options.myDropzone = {
    init: function() {
        
        this.on("addedfile", function(file) {
        //alert(file.name);
        // Create the remove button
        var removeButton = Dropzone.createElement('<a href="'+baseUrl+'telefonia/faturaproc/removeArquivo">Remover</a>');
    
    
        // Capture the Dropzone instance as closure.
        var _this = this;
    
        // Listen to the click event
        removeButton.addEventListener("click", function(e) {
          // Make sure the button click doesn't submit the form:
          e.preventDefault();
          //e.stopPropagation();
          // Remove the file preview.
          _this.removeFile(file);
          // If you want to the delete the file on the server as well,
          // you can do the AJAX request here.
          
          $.post( baseUrl+'telefonia/faturaproc/removeArquivo', { arquivo: file.name }, function( data ) {
              if( data.status == 'ok'){
                alert('Apagado com sucesso!');
              }else{
                alert('Erro ao apagar.');
              }
          }, "json");
          
        });
    
        // Add the button to the file preview element.
        file.previewElement.appendChild(removeButton);
      });
        
        this.on('drop', function( ){

        });
        this.on('sending', function( file, resp ){
              
              
        });
        this.on('success', function( file, resp ){
            //alert('concluido');
            /*var obj = $.parseJSON(resp);
            if(obj.status == 'ok'){
                alert('Subiu com sucesso!');
            }else{
                alert('Erro ao subir.');
            }*/
        });
        
        // Subiu todos os arquivos
        this.on('totaluploadprogress', function( progress ){
            
            if(progress == 100){
                //alert('terminado');
                $("#frm_fat_proc").show();
            }
            //alert(progress);
            //var obj = $.parseJSON(resp);
            //alert(obj.status);
            
        });
        
        /*this.on("addedfile", function (file) {
            if(!confirm("Deseja fazer upload do arquivo?")){
                this.removeFile(file);
                return false;
            }
        });*/
        
        this.on("complete", function (data) {
            //var msgConteudo = 'teste</div>';
        		//msgConteudo += '</li>';
            //$("#chat-msg-historico").append(msgConteudo);
    
        });
    
    },
    dictFileTooBig: "Tamanho excedido!",  
    maxFilesize: 100, // MB
    addRemoveLinks: false,
    dictDefaultMessage: "Arraste o arquivo pra cá.",
    dictCancelUploadConfirmation: "Gostaria de cancelar?",
    dictCancelUpload: "Cancelar",
    dictRemoveFile: "" /*"Remover arquivo"*/, 
    fallback: function(file) {
        //alert(1);
    }
};

</script>