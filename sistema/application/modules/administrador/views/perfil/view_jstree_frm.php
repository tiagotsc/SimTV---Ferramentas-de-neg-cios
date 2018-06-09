<link rel="stylesheet" href="<?php echo base_url("assets/js/jstree/themes/default/style.min.css") ?>" />

<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.shiftcheckbox.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jstree/jstree.min.js") ?>"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Criar/Editar perfil</li>
                </ol>
                <div class="row" id="divMain">
                    <div id="tree">
                        <?php echo $permissoes; ?>
                    </div>
                    <?php
                        $data = array('class'=>'pure-form','id'=>'frm-valores');
                        echo form_open('administrador/arvore/salvar',$data); 
                    ?>
                    <div>
                        <input class="search-input form-control"></input>
                    </div>
                    <div class="container">
                        <input type="hidden" name="node" id="node" value="" />
                        <div class="form-group">
                            <div id="tree-container"></div>
                        </div>
                    </div>
                    <div>
                        <input type="submit" value="Salvar" /> 
                    </div>
                    <?php
                    echo form_close();
                    ?>
                </div>
            </div>
    
<script type="text/javascript">

$(function () {
    $("#tree").jstree({
        "checkbox": {
            "keep_selected_style": false
        },
            "plugins": ["checkbox"]
    });
    $("#tree").bind("changed.jstree",
    function (e, data) {
        alert("Checked: " + data.node.id);
        alert("Parent: " + data.node.parent); 
        //alert(JSON.stringify(data));
    });
});


    $(document).ready(function(){

        $(".search-input").keyup(function() {

            var searchString = $(this).val();
            console.log(searchString);
            $('#tree-container').jstree('search', searchString);
        });

        //setting to hidden field
        //fill data to tree  with AJAX call
        $('#tree-container')
        .on('changed.jstree', function (e, data) { // Marca / desmarca
            var i, j, r = [];
            var t, y, z = [];
            // var state = false;
            // Marcados
            if(data.changed.selected.length > 0){
                
                //alert(data.instance.get_parent(this));
                /*if(data.instance.is_parent() === true){
                    alert('e pai');
                }else{
                    alert('e filho');
                }*/
                
                //dump(data.node.id);
                alert(data.changed.selected);
                
                //alert(data.instance.get_selected (true).length);
                /*for(i = 0, j = data.changed.selected.length; i < j; i++) {
                    r.push(data.instance.get_node(data.changed.selected[i]).id);
                }
                alert(r.join(','));*/
            }
            
            // Desmarcados
            if(data.changed.deselected.length > 0){
                alert(data.changed.deselected);
                /*for(t = 0, y = data.changed.deselected.length; t < y; t++) {
                    z.push(data.instance.get_node(data.changed.deselected[t]).id);
                }
                alert(z.join(','));*/
            }

        })
        .on('move_node.jstree', function (e, data) { // Arrastar e saltar
            alert(data.old_parent+'>'+data.parent+'='+data.node.id);
        }).jstree({
                //'plugins': ["wholerow","checkbox"],
                'core' : {
                    "multiple" : true,
                    "check_callback" : true,
                    'data' : {
                        "url" : "<?= base_url() ?>administrador/arvore/getChildren",
                        "dataType" : "json" // needed only if you do not supply JSON headers
                    }               
                },
                'checkbox': {
                    'three_state': true
                },
                'plugins': ["checkbox","dnd", "search", "changed"]
            }
        )
    });


function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

function marcaTodos(){
    if($('#todos').prop('checked') == true){
        $('input:checkbox').prop('checked', true);
    }else{
        $('input:checkbox').prop('checked', false);
    }
    
}

$("#mostraOcultaTodos").click(function(){
    event.preventDefault();
   $(".todasDivs").toggle(0,function(){
		if($(this).css('display')=='none'){
			$("#mostraOcultaTodos").html('Mostrar todos');//change the button label to be 'Show'
		}else{
			$("#mostraOcultaTodos").html('Ocultar todos');//change the button label to be 'Hide'
		}
	});
});

function mostrarOcultar(link, div){
    event.preventDefault();
    $(div).toggle(0,function(){
		if($(this).css('display')=='none'){
			$(link).html('Mostrar');//change the button label to be 'Show'
		}else{
			$(link).html('Ocultar');//change the button label to be 'Hide'
		}
	});
}

function marcaGrupo(classe, campo){
    if(campo.checked == true){
        $(classe).prop('checked', true);
    }else{
        $(classe).prop('checked', false);
    }

}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    //$(".todasDivs").css('display', 'none');
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
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

$(document).ready(function(){
    
    // Valida o formulário
	$("#salvar_perfil").validate({
		debug: false,
		rules: {
			nome_perfil: {
                required: true
            }
		},
		messages: {
			nome_perfil: {
                required: "Digite um nome para o perfil."
            }
	   }
   });
   
   $('#idPermissoes :checkbox').shiftcheckbox();    
   
});

</script>