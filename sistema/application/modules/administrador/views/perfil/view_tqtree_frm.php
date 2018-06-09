<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Criar/Editar perfil</li>
                </ol>
                <div><a href="#" id="todos">Todos</a></div>
                <div class="row" id="divMain">
                    <div id="tree1"></div>
                </div>
            </div>
    
<script type="text/javascript">

$("#todos").click(function(){
    
    if($("li.jqtree_common").hasClass( "jqtree-closed" )){
        $("li.jqtree_common").removeClass('jqtree-closed');
    }else{
        $("li.jqtree_common").addClass('jqtree-closed');
    }
    
    
    
    /*$("li.jqtree_common").toggle(0,function(){
		if($(this).css('display')=='none'){
			$("li.jqtree_common").removeClass('jqtree-closed');
		}else{
			
            $("li.jqtree_common").addClass('jqtree-closed');
		}
	});*/
    
    
});



$('#tree1').tree({
   dataUrl: "<?= base_url() ?>administrador/arvore/getTqTree/<?php echo $cd_perfil; ?>",
   //autoOpen: true,
   onCreateLi: function(node, $li) {
		// Append a link to the jqtree-element div.
		// The link has an url '#node-[id]' and a data property 'node-id'.
        if(node.permitido == 'S'){
            var checked = 'checked';
        }else{
            var checked = '';
        }
        
		$li.find('.jqtree-element').prepend(
			/*'<a href="#node-'+ node.id +'" class="edit" data-node-id="'+
			node.id +'">edit</a>'*/
			'<input type="checkbox" '+checked+' class="edit" name="permissao['+node.id+']"  value="'+node.id+'" />'
		)
        
        //$('ul:checkbox').shiftcheckbox();
	}
});

$('#tree1:checkbox').shiftcheckbox();

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
CONFIGURA O CALEND�RIO DATEPICKER NO INPUT INFORMADO
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
    
    // Traz o calend�rio input datepicker para frente da modal
    beforeShow :  function ()  { 
        setTimeout ( function (){ 
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
        },  0 ); 
    } 
});

$(document).ready(function(){
    
    // Valida o formul�rio
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
   
});

</script>