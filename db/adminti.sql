/*
Navicat MySQL Data Transfer

Source Server         : Mysql - Novo
Source Server Version : 50540
Source Host           : 192.168.140.91:3306
Source Database       : adminti

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-10-20 09:53:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `acesso_sistema`
-- ----------------------------
DROP TABLE IF EXISTS `acesso_sistema`;
CREATE TABLE `acesso_sistema` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_sistema`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=12

;

-- ----------------------------
-- Table structure for `cargo`
-- ----------------------------
DROP TABLE IF EXISTS `cargo`;
CREATE TABLE `cargo` (
`cd_cargo`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_cargo`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=225

;

-- ----------------------------
-- Table structure for `centro_custo`
-- ----------------------------
DROP TABLE IF EXISTS `centro_custo`;
CREATE TABLE `centro_custo` (
`cd_centro_custo`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_departamento`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(11) UNSIGNED NULL DEFAULT NULL ,
`codigo`  smallint(6) NULL DEFAULT NULL ,
PRIMARY KEY (`cd_centro_custo`),
FOREIGN KEY (`cd_unidade`) REFERENCES `unidade` (`cd_unidade`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_departamento`) REFERENCES `departamento` (`cd_departamento`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=114

;

-- ----------------------------
-- Table structure for `chat_dinamica`
-- ----------------------------
DROP TABLE IF EXISTS `chat_dinamica`;
CREATE TABLE `chat_dinamica` (
`cd_chat_dinamica`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_origem`  int(11) NULL DEFAULT NULL ,
`tipo_origem`  enum('grupo','dp','user') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_destino`  int(11) NULL DEFAULT NULL ,
`tipo_destino`  enum('grupo','dp','user') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`local`  int(11) NULL DEFAULT NULL ,
`status_escrita`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_abertura`  timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_chat_dinamica`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=40

;

-- ----------------------------
-- Table structure for `chat_favoritos`
-- ----------------------------
DROP TABLE IF EXISTS `chat_favoritos`;
CREATE TABLE `chat_favoritos` (
`cd_chat_favoritos`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_adicionado`  int(11) NULL DEFAULT NULL ,
`cd_usuario`  int(11) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`cd_chat_favoritos`),
FOREIGN KEY (`cd_usuario`) REFERENCES `usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=2265

;

-- ----------------------------
-- Table structure for `chat_lidas`
-- ----------------------------
DROP TABLE IF EXISTS `chat_lidas`;
CREATE TABLE `chat_lidas` (
`cd_chat_lidas`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_chat_msg`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_destino`  int(11) NULL DEFAULT NULL ,
`local`  int(11) NULL DEFAULT NULL ,
`tipo`  enum('grupo','dp') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_usuario`  int(11) UNSIGNED NULL DEFAULT NULL ,
`status_lida`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N' ,
`data_lida`  timestamp NULL DEFAULT NULL ,
PRIMARY KEY (`cd_chat_lidas`),
FOREIGN KEY (`cd_usuario`) REFERENCES `usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_chat_msg`) REFERENCES `chat_msg` (`cd_chat_msg`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=440

;

-- ----------------------------
-- Table structure for `chat_msg`
-- ----------------------------
DROP TABLE IF EXISTS `chat_msg`;
CREATE TABLE `chat_msg` (
`cd_chat_msg`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_origem`  int(11) UNSIGNED NULL DEFAULT NULL ,
`tipo_origem`  enum('user','dp','grupo') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_destino`  int(11) NULL DEFAULT NULL ,
`tipo_destino`  enum('user','dp','grupo') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`local`  int(11) NULL DEFAULT NULL ,
`mensagem`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`mensagem_tipo`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`diretorio`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`extensao`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`lida`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N' ,
`data_lida`  timestamp NULL DEFAULT NULL ,
`data_envio`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_chat_msg`),
FOREIGN KEY (`cd_origem`) REFERENCES `usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=441

;

-- ----------------------------
-- Table structure for `DADOS`
-- ----------------------------
DROP TABLE IF EXISTS `DADOS`;
CREATE TABLE `DADOS` (
`matricula`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`local`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cargo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`admissao`  date NULL DEFAULT NULL ,
`email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `departamento`
-- ----------------------------
DROP TABLE IF EXISTS `departamento`;
CREATE TABLE `departamento` (
`cd_departamento`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_departamento`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status_departamento`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_departamento`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=57

;

-- ----------------------------
-- Table structure for `estado`
-- ----------------------------
DROP TABLE IF EXISTS `estado`;
CREATE TABLE `estado` (
`cd_estado`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_estado`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`sigla_estado`  char(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_estado`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=29

;

-- ----------------------------
-- Table structure for `feriado`
-- ----------------------------
DROP TABLE IF EXISTS `feriado`;
CREATE TABLE `feriado` (
`id_feriado`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_unidade`  int(11) UNSIGNED NOT NULL ,
`data`  date NOT NULL ,
`descricao`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`id_feriado`),
FOREIGN KEY (`cd_unidade`) REFERENCES `unidade` (`cd_unidade`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=235

;

-- ----------------------------
-- Table structure for `ferias`
-- ----------------------------
DROP TABLE IF EXISTS `ferias`;
CREATE TABLE `ferias` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`inicio`  date NULL DEFAULT NULL ,
`fim`  date NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`status`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`tipo`  enum('F','B') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'F' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario`) REFERENCES `usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=118

;

-- ----------------------------
-- Table structure for `log`
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
`cd_log`  bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`aplicacao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`modulo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`funcao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`acao`  enum('DELETE','UPDATE','INSERT','INICIA','PROCESSANDO','FINALIZA') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`idAcao`  int(11) NULL DEFAULT NULL ,
`descricao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_log`),
FOREIGN KEY (`cd_usuario`) REFERENCES `usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=6747

;

-- ----------------------------
-- Table structure for `log_arquivo`
-- ----------------------------
DROP TABLE IF EXISTS `log_arquivo`;
CREATE TABLE `log_arquivo` (
`cd_log_arquivo`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`localizacao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`md5file`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`fonte`  enum('CALLCENTER - ATIVO','SERVIDOR ASTERISK','CALLCENTER - RECEPTIVO','CALLCENTER - RECEPTIVO - 0800','MOVEL','CALLCENTER - RECEPTIVO - 4004') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`permissor`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`cd_log_arquivo`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=834

;

-- ----------------------------
-- Table structure for `log_chat_msg`
-- ----------------------------
DROP TABLE IF EXISTS `log_chat_msg`;
CREATE TABLE `log_chat_msg` (
`cd_log_chat_msg`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_chat_msg`  int(11) NULL DEFAULT NULL ,
`cd_origem`  int(11) NULL DEFAULT NULL ,
`tipo_origem`  enum('dp','user') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_destino`  int(11) NULL DEFAULT NULL ,
`tipo_destino`  enum('user','dp') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`local`  int(11) NULL DEFAULT NULL ,
`mensagem`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`mensagem_tipo`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`diretorio`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`extensao`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`lida`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_lida`  timestamp NULL DEFAULT NULL ,
`data_envio`  timestamp NULL DEFAULT NULL ,
PRIMARY KEY (`cd_log_chat_msg`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=107

;

-- ----------------------------
-- Table structure for `log_compra_vale_transporte`
-- ----------------------------
DROP TABLE IF EXISTS `log_compra_vale_transporte`;
CREATE TABLE `log_compra_vale_transporte` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_usuario_comprador`  int(10) UNSIGNED NOT NULL ,
`data_geracao_arquivo`  datetime NOT NULL ,
`matricula_usuario_solicitante`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`dias_uteis_mes`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`dias_acrescimos`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`dias_descontos`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`valor_passagem`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario_comprador`) REFERENCES `usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1349

;

-- ----------------------------
-- Table structure for `log_tarefa_agendada`
-- ----------------------------
DROP TABLE IF EXISTS `log_tarefa_agendada`;
CREATE TABLE `log_tarefa_agendada` (
`cd_log_tarefa_agendada`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`tarefa`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_log_tarefa_agendada`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1003

;

-- ----------------------------
-- Table structure for `menu`
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
`cd_menu`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_menu`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`pai_menu`  tinyint(11) NULL DEFAULT NULL ,
`link_menu`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`ordem_menu`  tinyint(4) NULL DEFAULT NULL ,
`status_menu`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_menu`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=19

;

-- ----------------------------
-- Table structure for `operadora`
-- ----------------------------
DROP TABLE IF EXISTS `operadora`;
CREATE TABLE `operadora` (
`cd_operadora`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_operadora`  smallint(11) NULL DEFAULT NULL ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_operadora`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=5

;

-- ----------------------------
-- Table structure for `passagem`
-- ----------------------------
DROP TABLE IF EXISTS `passagem`;
CREATE TABLE `passagem` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`passagens`  tinyint(4) NULL DEFAULT NULL ,
`valor`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`data_cadastro`  date NOT NULL ,
`data_desativacao`  date NULL DEFAULT NULL ,
`cd_unidade`  int(11) NOT NULL ,
`bilhete_unico`  enum('S','N') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=26

;

-- ----------------------------
-- Table structure for `solicitacao_sistema`
-- ----------------------------
DROP TABLE IF EXISTS `solicitacao_sistema`;
CREATE TABLE `solicitacao_sistema` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_usuario`  int(10) UNSIGNED NOT NULL ,
`id_sistema`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario`) REFERENCES `usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`id_sistema`) REFERENCES `acesso_sistema` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=184

;

-- ----------------------------
-- Table structure for `telefonia_acessorio`
-- ----------------------------
DROP TABLE IF EXISTS `telefonia_acessorio`;
CREATE TABLE `telefonia_acessorio` (
`cd_telefonia_acessorio`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_telefonia_acessorio`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=10

;

-- ----------------------------
-- Table structure for `telefonia_aparelho`
-- ----------------------------
DROP TABLE IF EXISTS `telefonia_aparelho`;
CREATE TABLE `telefonia_aparelho` (
`cd_telefonia_aparelho`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_telefonia_marca`  int(10) UNSIGNED NULL DEFAULT NULL ,
`tipo`  enum('CEL','INT') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'CEL' ,
`nota_fiscal`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`modelo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_inicio`  date NULL DEFAULT NULL ,
`data_fim`  date NULL DEFAULT NULL ,
`status`  enum('Ativo','Estoque','Avariado','Furtado','Baixa Estoque') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'Estoque' ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_telefonia_aparelho`),
FOREIGN KEY (`cd_telefonia_marca`) REFERENCES `telefonia_marca` (`cd_telefonia_marca`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=653

;
