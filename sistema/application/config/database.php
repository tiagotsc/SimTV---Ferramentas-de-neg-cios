<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

#MYSQL - NOVO
$db['default']['hostname'] = 'localhost'/*'192.168.140.91'*/;
$db['default']['username'] = 'sistemas';
$db['default']['password'] = 'SimSisMysql531';
$db['default']['database'] = 'sistema';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
#$db['default']['char_set'] = 'latin1';
#$db['default']['dbcollat'] = 'latin1_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

#MYSQL - NOVO
$db['adminti']['hostname'] = 'localhost'/*'192.168.140.91'*/;
$db['adminti']['username'] = 'sistemas';
$db['adminti']['password'] = 'SimSisMysql531';
$db['adminti']['database'] = 'adminti';
$db['adminti']['dbdriver'] = 'mysqli';
$db['adminti']['dbprefix'] = '';
$db['adminti']['pconnect'] = TRUE;
$db['adminti']['db_debug'] = TRUE;
$db['adminti']['cache_on'] = FALSE;
$db['adminti']['cachedir'] = '';
$db['adminti']['char_set'] = 'utf8';
$db['adminti']['dbcollat'] = 'utf8_general_ci';
#$db['default']['char_set'] = 'latin1';
#$db['default']['dbcollat'] = 'latin1_general_ci';
$db['adminti']['swap_pre'] = '';
$db['adminti']['autoinit'] = TRUE;
$db['adminti']['stricton'] = FALSE;

#MYSQL - ANTIGO
$db['mysqlAntigo']['hostname'] = '192.168.140.108';
$db['mysqlAntigo']['username'] = 'root';
$db['mysqlAntigo']['password'] = 'tvcidade';
$db['mysqlAntigo']['database'] = 'sisatp';
$db['mysqlAntigo']['dbdriver'] = 'mysqli';
$db['mysqlAntigo']['dbprefix'] = '';
$db['mysqlAntigo']['pconnect'] = TRUE;
$db['mysqlAntigo']['db_debug'] = TRUE;
$db['mysqlAntigo']['cache_on'] = FALSE;
$db['mysqlAntigo']['cachedir'] = '';
$db['mysqlAntigo']['char_set'] = 'utf8';
$db['mysqlAntigo']['dbcollat'] = 'utf8_general_ci';
#$db['mysqlAntigo']['char_set'] = 'latin1';
#$db['mysqlAntigo']['dbcollat'] = 'latin1_general_ci';
$db['mysqlAntigo']['swap_pre'] = '';
$db['mysqlAntigo']['autoinit'] = TRUE;
$db['mysqlAntigo']['stricton'] = FALSE;

#MYSQL - Telefonia Producao
$db['telefoniaProducao']['hostname'] = '192.168.150.212';
$db['telefoniaProducao']['username'] = 'suporte';
$db['telefoniaProducao']['password'] = 'S1mTV@dmMysql';
$db['telefoniaProducao']['database'] = 'asteriskcdrdb';
$db['telefoniaProducao']['dbdriver'] = 'mysqli';
$db['telefoniaProducao']['dbprefix'] = '';
$db['telefoniaProducao']['pconnect'] = TRUE;
$db['telefoniaProducao']['db_debug'] = TRUE;
$db['telefoniaProducao']['cache_on'] = FALSE;
$db['telefoniaProducao']['cachedir'] = '';
$db['telefoniaProducao']['char_set'] = 'utf8';
$db['telefoniaProducao']['dbcollat'] = 'utf8_general_ci';
#$db['telefonia']['char_set'] = 'latin1';
#$db['telefonia']['dbcollat'] = 'latin1_general_ci';
$db['telefonia']['swap_pre'] = '';
$db['telefonia']['autoinit'] = TRUE;
$db['telefonia']['stricton'] = FALSE;

#MYSQL - Telefonia (Treinamento)
$db['telefonia']['hostname'] = '192.168.150.211';
$db['telefonia']['username'] = 'cdrasterisk';
$db['telefonia']['password'] = 'MySqDbasT01';
$db['telefonia']['database'] = 'asteriskcdrdb';
$db['telefonia']['dbdriver'] = 'mysqli';
$db['telefonia']['dbprefix'] = '';
$db['telefonia']['pconnect'] = TRUE;
$db['telefonia']['db_debug'] = TRUE;
$db['telefonia']['cache_on'] = FALSE;
$db['telefonia']['cachedir'] = '';
$db['telefonia']['char_set'] = 'utf8';
$db['telefonia']['dbcollat'] = 'utf8_general_ci';
#$db['telefonia']['char_set'] = 'latin1';
#$db['telefonia']['dbcollat'] = 'latin1_general_ci';
$db['telefonia']['swap_pre'] = '';
$db['telefonia']['autoinit'] = TRUE;
$db['telefonia']['stricton'] = FALSE;

#MYSQL - URA
$db['ura']['hostname'] = '192.168.19.230';
$db['ura']['username'] = 'tiago';
$db['ura']['password'] = 'SimtvTiago2016';
$db['ura']['database'] = 'telefonia';
$db['ura']['dbdriver'] = 'mysqli';
$db['ura']['dbprefix'] = '';
$db['ura']['pconnect'] = TRUE;
$db['ura']['db_debug'] = TRUE;
$db['ura']['cache_on'] = FALSE;
$db['ura']['cachedir'] = '';
$db['ura']['char_set'] = 'utf8';
$db['ura']['dbcollat'] = 'utf8_general_ci';
#$db['telefonia']['char_set'] = 'latin1';
#$db['telefonia']['dbcollat'] = 'latin1_general_ci';
$db['ura']['swap_pre'] = '';
$db['ura']['autoinit'] = TRUE;
$db['ura']['stricton'] = FALSE;

#ORACLE
#$db['oracle']['dsn']      = '';
$db['oracle']['hostname'] = '192.168.140.41/gxvsim';
$db['oracle']['username'] = 'supsiga';
$db['oracle']['password'] = 'supsiga2012';
$db['oracle']['database'] = '';
$db['oracle']['dbdriver'] = 'oci8';
$db['oracle']['dbprefix'] = '';
$db['oracle']['pconnect'] = TRUE;
$db['oracle']['db_debug'] = TRUE;
$db['oracle']['cache_on'] = FALSE;
$db['oracle']['cachedir'] = '';
$db['oracle']['char_set'] = 'utf8';
$db['oracle']['dbcollat'] = 'utf8_general_ci';
$db['oracle']['swap_pre'] = '';
$db['oracle']['autoinit'] = TRUE;
$db['oracle']['stricton'] = FALSE;
$db['oracle']['failover'] = array();

#SIGA BCV
#$db['siga_bcv']['dsn']      = '';
$db['siga_bcv']['hostname'] = '192.168.140.42/gxvsim';
$db['siga_bcv']['username'] = 'supsiga';
$db['siga_bcv']['password'] = 'supsiga2012';
$db['siga_bcv']['database'] = '';
$db['siga_bcv']['dbdriver'] = 'oci8';
$db['siga_bcv']['dbprefix'] = '';
$db['siga_bcv']['pconnect'] = TRUE;
$db['siga_bcv']['db_debug'] = TRUE;
$db['siga_bcv']['cache_on'] = FALSE;
$db['siga_bcv']['cachedir'] = '';
$db['siga_bcv']['char_set'] = 'utf8';
$db['siga_bcv']['dbcollat'] = 'utf8_general_ci';
$db['siga_bcv']['swap_pre'] = '';
$db['siga_bcv']['autoinit'] = TRUE;
$db['siga_bcv']['stricton'] = FALSE;
$db['siga_bcv']['failover'] = array();

#Banco Teste de Impressao
$db['impTest']['hostname'] = '192.168.19.232';
$db['impTest']['username'] = 'tiago';
$db['impTest']['password'] = 'S1mTVT1@g0';
$db['impTest']['database'] = 'jasmine';
$db['impTest']['dbdriver'] = 'mysqli';
$db['impTest']['dbprefix'] = '';
$db['impTest']['pconnect'] = TRUE;
$db['impTest']['db_debug'] = TRUE;
$db['impTest']['cache_on'] = FALSE;
$db['impTest']['cachedir'] = '';
$db['impTest']['char_set'] = 'utf8';
$db['impTest']['dbcollat'] = 'utf8_general_ci';
#$db['default']['char_set'] = 'latin1';
#$db['default']['dbcollat'] = 'latin1_general_ci';
$db['impTest']['swap_pre'] = '';
$db['impTest']['autoinit'] = TRUE;
$db['impTest']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */