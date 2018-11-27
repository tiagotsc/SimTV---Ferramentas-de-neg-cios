<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<div class="row">&nbsp</div>
<?php echo $this->session->flashdata('statusOperacao'); ?>
<div class="row">&nbsp</div>
<div class="row">
    <div class="col-md-4 col-md-offset-4">

            <form id="form_login" style="padding: 10px;" method="post" action="home/autentica">
    		
    			<label>
    				Login:
    				<input type="text" id="login" name="login" class="form-control" />
    			</label>
    			
    			<label>
    				Senha:
    				<input type="password" id="senha" name="senha" class="form-control" />
    			</label>
    			
    			<!--<a id="link_lembra_senha" data-toggle="modal" href="#alterarSenha">Alterar senha</a>-->
    			<input id="btn_logar" class="btn btn-primary pull-right" type="submit" value="Logar" />
    			
    		</form>

    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    
    // Valida o formulário
	$("#form_login").validate({
		debug: false,
		rules: {
			login: {
                required: true
            },
            senha: {
                required: true
            }
		},
		messages: {
			login: {
                required: "Digite o login."
            },
            senha: {
                required: "Digite a senha."
            }
	   }
   });   
   
});
</script>