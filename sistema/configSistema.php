<?php

/**
 * @author Boomer
 * @copyright 2014
 * 
 * Defines do sistema personalizados
 * 
 */

# PASTA UTILIZADA PELO SISTEMA
# define('PASTA_SISTEMA', '../faturamento/Retornos ICNET - IBBRADESCO/TI/');
# define('PASTA_SISTEMA', '../faturamento/TI/');

define('PASTA_SISTEMA', '../pasta_rede/Retornos ICNET - IBBRADESCO/TI/'); # PASTA AONDE SÃO PROCESSADOS OS ARQUIVOS DE RETORNO
#define('PASTA_REDE_SISTEMA', '../pasta_comp/'); # PASTA DA REDE USUDA PELO SISTEMA
define('PASTA_REDE_SISTEMA', '../pasta_sistemas/'); # PASTA DA REDE USUDA PELO SISTEMA
#\\192.168.150.231\sistemas
# CONFIGURAÇÃO AD
define('HOST_AD', '192.168.40.3');
define('DASE_DN_AD', 'DC=tvcidade,DC=com,DC=br');
define('ACCOUNT_SUFFIX', '@tvcidade');

# SENHA MASTER
define('SENHA_MASTER', 'simTvMaster');

# BANCOS
if($this->session->userdata('cd') == 6){
    define('BANCO_TELECOM', 'telecom');
}else{
    define('BANCO_TELECOM', 'telecom');
}

# CONFIGURAÇÕES PERÍODOS FORMULÁRIOS DA ANATEL
#define('ANATEL_FRM_INICIO', date('01/m/Y'));
#define('ANATEL_FRM_FIM', date('30/m/Y'));

# NOME SISTEMA
define('SISTEMA', utf8_encode('Ferramentas de Negócios'));
?>