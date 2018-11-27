$(function(){
    $('#cli_per').change(function(){
        $('#cli_node>optgroup').prop('disabled',true);
        $("#cli_node>optgroup[per="+$(this).val()+"]").prop('disabled');
        $('#cli_node').val(
            $("#cli_node>optgroup[per="+$(this).val()+"]")
            .children().first().attr('value')
        );
    }).trigger('change');
    $('#form_save').click(function(){
        var vals = {};
        var check = true;
        $(':radio').each(function(){
            var nm = $(this).attr('name');
            if ( typeof $('input:radio[name='+nm+']:checked').val() === 'undefined'){
                check = false;
                $(this).parent().addClass('highlightme');
                $(this).one('change',function(){
                    $("[name='"+$(this).attr('name')+"']").parent().removeClass('highlightme');
                });
            }
        });
        $('.form_container').find('input,textarea,select').each(function(){
            var mykey = '';
            if($(this).parent().is('.colval')){
                mykey = 'check_'+$(this).parent().parent().children('.colcod').html() + "_" + $(this).val();
                vals[mykey] = (($(this).prop('checked'))?1:0);
            }else{
                if($(this).attr('type') === 'checkbox'){
                    mykey = $(this).attr('id');
                    vals[mykey] = (($(this).prop('checked'))?1:0);
                }else if($(this).attr('type') !== 'hidden'){
                    if($(this).attr('type') === 'radio'){
                        var nm = $(this).attr('name');
                        if(typeof vals[nm] === 'undefined'){
                            vals[nm] = $('input:radio[name='+nm+']:checked').val();
                        }
                    }else{
                        if($(this).is('input') && !$(this).is('.notrequired') && !$(this).val()){
                            $(this).addClass('highlightme');
                            $(this).one('keydown',function(){
                                $(this).removeClass('highlightme').parent().removeClass('highlightme');
                            });
                            check = false;
                        }
                        vals[$(this).attr('id')] = $(this).val();
                    }
                }
            }
        });
        if(check){
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: 'vistoria/sv',
                data: {form:vals},
                beforeSend: function(){
                    cas.hidethis('body');
                },
                error:function(data){
                    cas.showthis('body');
                    cas.makeNotif('error','Não foi possível salvar o formulário, tente novamente.');
                },
                complete:function(data){
                    cas.showthis('body');
                },
                success: function(data){
                    if(data.status == 'success'){
                        cas.makeNotif('success','Formulário salvo com sucesso.');
                    }else if(data.status == 'permission_error'){
                        window.location.replace("login");
                    }else{
                        cas.makeNotif('error',data.msg);
                    }
                }
            });
        }else{
            cas.makeNotif('error','Por Favor, preencha corretamente os campos em destaque.');
        }
    });
    $('#dt_inst').datepicker({defaultDate: 0,dateFormat:'dd-mm-yy'});
    $('#dt_vist').datepicker({defaultDate: 0,dateFormat:'dd-mm-yy'});
    $('#instal_cm,#instal_decoder').mouseover(function(){
        $(this).parent().children('.field_descr').show();
    });

    $('#instal_cm,#instal_decoder').mouseout(function(){
        $(this).parent().children('.field_descr').hide();
    });
});
