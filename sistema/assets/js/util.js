var wsBuscaCEP = 'http://api.postmon.com.br/v1/cep/';

/*
    Debuga objeto javascript
*/
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}

/*
    Implode personalizada em JS
*/
function implode_js(separator,array){
       var temp = '';
       for(var i=0;i<array.length;i++){
           temp +=  array[i] 
           if(i!=array.length-1){
                temp += separator  ; 
           }
       }//end of the for loop

       return temp;
}//end of the function

/* Formata valor no formato de dinheiro no formato BR */
function mascaraValor(valor) {
    valor = valor.toString().replace(/\D/g,"");
    valor = valor.toString().replace(/(\d)(\d{8})$/,"$1.$2");
    valor = valor.toString().replace(/(\d)(\d{5})$/,"$1.$2");
    valor = valor.toString().replace(/(\d)(\d{2})$/,"$1,$2");
    return valor                    
}

/*
    Formata número para real (Dinheiro)
*/
function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
function calendar(){
    
    $(".data").datepicker({
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
    
}