/*
Navicat MySQL Data Transfer

Source Server         : Mysql - Novo
Source Server Version : 50540
Source Host           : 192.168.140.91:3306
Source Database       : sistema

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-12-19 15:38:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `anatel_config`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_config`;
CREATE TABLE `anatel_config` (
`cd_anatel_config`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`valor`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`sistema`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_anatel_config`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3

;

-- ----------------------------
-- Table structure for `anatel_frm`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_frm`;
CREATE TABLE `anatel_frm` (
`cd_anatel_frm`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_tipo_frm`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_departamento`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_anatel_indicador`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_anatel_produto`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_anatel_xml`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_anatel_frm`),
FOREIGN KEY (`cd_anatel_indicador`) REFERENCES `anatel_indicador` (`cd_anatel_indicador`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_anatel_tipo_frm`) REFERENCES `anatel_tipo_frm` (`cd_anatel_tipo_frm`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_anatel_xml`) REFERENCES `anatel_xml` (`cd_anatel_xml`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_departamento`) REFERENCES `adminti`.`departamento` (`cd_departamento`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=27

;

-- ----------------------------
-- Table structure for `anatel_indicador`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_indicador`;
CREATE TABLE `anatel_indicador` (
`cd_anatel_indicador`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`sigla`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_departamento`  int(10) UNSIGNED NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_anatel_indicador`),
FOREIGN KEY (`cd_departamento`) REFERENCES `adminti`.`departamento` (`cd_departamento`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=19

;

-- ----------------------------
-- Table structure for `anatel_meta`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_meta`;
CREATE TABLE `anatel_meta` (
`cd_anatel_meta`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_frm`  int(11) UNSIGNED NULL DEFAULT NULL ,
`regra`  enum('P','N','F') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`operador`  varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`comparador`  varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`numero`  float(6,2) NULL DEFAULT NULL ,
PRIMARY KEY (`cd_anatel_meta`),
FOREIGN KEY (`cd_anatel_frm`) REFERENCES `anatel_frm` (`cd_anatel_frm`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=33

;

-- ----------------------------
-- Table structure for `anatel_meta_campo`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_meta_campo`;
CREATE TABLE `anatel_meta_campo` (
`cd_anatel_meta_campo`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_meta`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_anatel_quest`  int(10) UNSIGNED NULL DEFAULT NULL ,
`ordem_questao`  smallint(6) NULL DEFAULT NULL ,
PRIMARY KEY (`cd_anatel_meta_campo`),
FOREIGN KEY (`cd_anatel_meta`) REFERENCES `anatel_meta` (`cd_anatel_meta`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_anatel_quest`) REFERENCES `anatel_quest` (`cd_anatel_quest`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=55

;

-- ----------------------------
-- Table structure for `anatel_meta_res`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_meta_res`;
CREATE TABLE `anatel_meta_res` (
`cd_anatel_meta_res`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_meta`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_anatel_frm`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_anatel_motivo_just`  int(10) UNSIGNED NULL DEFAULT NULL ,
`diagnostico`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`acao_corretiva`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`ilustracao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`resultado`  float(6,2) NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_anatel_meta_res`),
FOREIGN KEY (`cd_anatel_frm`) REFERENCES `anatel_frm` (`cd_anatel_frm`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_anatel_meta`) REFERENCES `anatel_meta` (`cd_anatel_meta`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_anatel_motivo_just`) REFERENCES `anatel_motivo_just` (`cd_anatel_motivo_just`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=2774

;

-- ----------------------------
-- Table structure for `anatel_motivo_just`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_motivo_just`;
CREATE TABLE `anatel_motivo_just` (
`cd_anatel_motivo_just`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_anatel_motivo_just`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=13

;

-- ----------------------------
-- Table structure for `anatel_quest`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_quest`;
CREATE TABLE `anatel_quest` (
`cd_anatel_quest`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_frm`  int(10) UNSIGNED NULL DEFAULT NULL ,
`sigla`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`questao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo_resp`  varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`obrigatorio`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'S' ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_anatel_quest`),
FOREIGN KEY (`cd_anatel_frm`) REFERENCES `anatel_frm` (`cd_anatel_frm`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=62

;

-- ----------------------------
-- Table structure for `anatel_res`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_res`;
CREATE TABLE `anatel_res` (
`cd_anatel_res`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_frm`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_anatel_quest`  int(10) UNSIGNED NULL DEFAULT NULL ,
`resposta`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`grupo`  tinyint(11) NULL DEFAULT 1 ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_anatel_res`),
FOREIGN KEY (`cd_anatel_frm`) REFERENCES `anatel_frm` (`cd_anatel_frm`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_anatel_quest`) REFERENCES `anatel_quest` (`cd_anatel_quest`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=44386

;

-- ----------------------------
-- Table structure for `anatel_resp_indicador`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_resp_indicador`;
CREATE TABLE `anatel_resp_indicador` (
`cd_anatel_resp_indicador`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_frm`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`cd_anatel_resp_indicador`),
FOREIGN KEY (`cd_anatel_frm`) REFERENCES `anatel_frm` (`cd_anatel_frm`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=853

;

-- ----------------------------
-- Table structure for `anatel_tipo_frm`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_tipo_frm`;
CREATE TABLE `anatel_tipo_frm` (
`cd_anatel_tipo_frm`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_anatel_tipo_frm`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3

;

-- ----------------------------
-- Table structure for `anatel_tipo_indicador`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_tipo_indicador`;
CREATE TABLE `anatel_tipo_indicador` (
`cd_anatel_tipo_indicador`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_anatel_tipo_indicador`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=6

;

-- ----------------------------
-- Table structure for `anatel_xml`
-- ----------------------------
DROP TABLE IF EXISTS `anatel_xml`;
CREATE TABLE `anatel_xml` (
`cd_anatel_xml`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_anatel_tipo_frm`  int(10) UNSIGNED NULL DEFAULT NULL ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_anatel_xml`),
FOREIGN KEY (`cd_anatel_tipo_frm`) REFERENCES `anatel_tipo_frm` (`cd_anatel_tipo_frm`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=7

;

-- ----------------------------
-- Table structure for `arquivo_retorno`
-- ----------------------------
DROP TABLE IF EXISTS `arquivo_retorno`;
CREATE TABLE `arquivo_retorno` (
`cd_arquivo_retorno`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_arquivo_retorno`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`cd_banco`  int(11) UNSIGNED NOT NULL ,
`data_arquivo_retorno`  date NULL DEFAULT NULL ,
`tipo_arquivo_retorno`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`nome_empresa_arquivo_retorno`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`data_insercao_arquivo_retorno`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_arquivo_retorno`),
FOREIGN KEY (`cd_banco`) REFERENCES `banco` (`cd_banco`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=16574

;

-- ----------------------------
-- Table structure for `auehara_tmp`
-- ----------------------------
DROP TABLE IF EXISTS `auehara_tmp`;
CREATE TABLE `auehara_tmp` (
`nome_arquivo_retorno`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`data_arquivo_retorno`  date NULL DEFAULT NULL ,
`tipo_arquivo_retorno`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`cd_banco`  int(11) UNSIGNED NOT NULL ,
`nome_banco`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`cd_conteudo_arquivo_retorno`  bigint(15) UNSIGNED NOT NULL DEFAULT 0 ,
`cd_arquivo_retorno`  int(10) UNSIGNED NULL DEFAULT NULL ,
`linha_arquivo_retorno`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`permissor_arquivo_retorno`  int(2) NULL DEFAULT NULL ,
`titulo_arquivo_retorno`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`codigo_ocorrencia_arquivo_retorno`  int(11) NULL DEFAULT NULL ,
`agencia_arquivo_retorno`  varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`conta_arquivo_retorno`  varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`valor_titulo_arquivo_retorno`  decimal(13,2) NULL DEFAULT NULL ,
`valor_pago_arquivo_retorno`  decimal(13,2) NULL DEFAULT NULL ,
`data_vencimento_arquivo_retorno`  date NULL DEFAULT NULL ,
`codigo_inscricao_arquivo_retorno`  int(2) NULL DEFAULT NULL ,
`numero_inscricao_arquivo_retorno`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nosso_numero_arquivo_retorno`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nosso_num_corresp_arquivo_retorno`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_ocorrencia_arquivo_retorno`  date NULL DEFAULT NULL ,
`cd_tipo_linha_arquivo_retorno`  int(10) UNSIGNED NULL DEFAULT NULL ,
`codigo_banco_arquivo_retorno`  int(11) NULL DEFAULT NULL ,
`numero_linha_arquivo_retorno`  int(11) NULL DEFAULT NULL ,
`cd_ocorrencia_arquivo_retorno`  int(11) NULL DEFAULT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `banco`
-- ----------------------------
DROP TABLE IF EXISTS `banco`;
CREATE TABLE `banco` (
`cd_banco`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`codigo_banco`  int(3) NULL DEFAULT NULL ,
`nome_banco`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`nome_diretorio_banco`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status_banco`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`cd_banco`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=9

;

-- ----------------------------
-- Table structure for `ci_sessions`
-- ----------------------------
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions` (
`session_id`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,
`ip_address`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,
`user_agent`  varchar(254) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`last_activity`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`user_data`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`session_id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `cidade`
-- ----------------------------
DROP TABLE IF EXISTS `cidade`;
CREATE TABLE `cidade` (
`cd_cidade`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_cidade`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`codigo_area_cidade`  int(11) NULL DEFAULT NULL ,
`status_cidade`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_cidade`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=2

;

-- ----------------------------
-- Table structure for `config_usuario`
-- ----------------------------
DROP TABLE IF EXISTS `config_usuario`;
CREATE TABLE `config_usuario` (
`cd_config_usuario`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_usuario`  int(11) UNSIGNED NOT NULL ,
`cd_perfil`  int(11) UNSIGNED NULL DEFAULT NULL ,
`status_config_usuario`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_config_usuario`),
FOREIGN KEY (`cd_perfil`) REFERENCES `perfil` (`cd_perfil`) ON DELETE SET NULL ON UPDATE SET NULL,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=2422

;

-- ----------------------------
-- Table structure for `conteudo_arquivo_retorno`
-- ----------------------------
DROP TABLE IF EXISTS `conteudo_arquivo_retorno`;
CREATE TABLE `conteudo_arquivo_retorno` (
`cd_conteudo_arquivo_retorno`  bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_arquivo_retorno`  int(10) UNSIGNED NULL DEFAULT NULL ,
`linha_arquivo_retorno`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`permissor_arquivo_retorno`  int(2) NULL DEFAULT NULL ,
`titulo_arquivo_retorno`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`codigo_ocorrencia_arquivo_retorno`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`agencia_arquivo_retorno`  varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`conta_arquivo_retorno`  varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`valor_titulo_arquivo_retorno`  decimal(13,2) NULL DEFAULT NULL ,
`valor_pago_arquivo_retorno`  decimal(13,2) NULL DEFAULT NULL ,
`data_vencimento_arquivo_retorno`  date NULL DEFAULT NULL ,
`codigo_inscricao_arquivo_retorno`  int(2) NULL DEFAULT NULL ,
`numero_inscricao_arquivo_retorno`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nosso_numero_arquivo_retorno`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nosso_num_corresp_arquivo_retorno`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_ocorrencia_arquivo_retorno`  date NULL DEFAULT NULL ,
`cd_tipo_linha_arquivo_retorno`  int(10) UNSIGNED NULL DEFAULT NULL ,
`codigo_banco_arquivo_retorno`  int(11) NULL DEFAULT NULL ,
`numero_linha_arquivo_retorno`  int(11) NULL DEFAULT NULL ,
`cd_ocorrencia_arquivo_retorno`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`cd_conteudo_arquivo_retorno`),
FOREIGN KEY (`cd_arquivo_retorno`) REFERENCES `arquivo_retorno` (`cd_arquivo_retorno`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_tipo_linha_arquivo_retorno`) REFERENCES `tipo_linha_arquivo_retorno` (`cd_tipo_linha_arquivo_retorno`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=5010770

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
AUTO_INCREMENT=36

;

-- ----------------------------
-- Table structure for `email_envia`
-- ----------------------------
DROP TABLE IF EXISTS `email_envia`;
CREATE TABLE `email_envia` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_permissao`  int(10) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_permissao`) REFERENCES `permissao` (`cd_permissao`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=40

;

-- ----------------------------
-- Table structure for `email_grupo`
-- ----------------------------
DROP TABLE IF EXISTS `email_grupo`;
CREATE TABLE `email_grupo` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=113

;

-- ----------------------------
-- Table structure for `email_grupo_recebe`
-- ----------------------------
DROP TABLE IF EXISTS `email_grupo_recebe`;
CREATE TABLE `email_grupo_recebe` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idEmailGrupo`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idEmailEnvia`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idEmailGrupo`) REFERENCES `email_grupo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1421

;

-- ----------------------------
-- Table structure for `email_recebe`
-- ----------------------------
DROP TABLE IF EXISTS `email_recebe`;
CREATE TABLE `email_recebe` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idEmailEnvia`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`idEmailEnvia`) REFERENCES `email_envia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1563

;

-- ----------------------------
-- Table structure for `envio`
-- ----------------------------
DROP TABLE IF EXISTS `envio`;
CREATE TABLE `envio` (
`matricula`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `excluido_arquivo_retorno`
-- ----------------------------
DROP TABLE IF EXISTS `excluido_arquivo_retorno`;
CREATE TABLE `excluido_arquivo_retorno` (
`cd_excluido_arquivo_retorno`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`linha_excluido_arquivo_retorno`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`cd_arquivo_retorno`  int(10) UNSIGNED NOT NULL ,
`tipo_excluido_arquivo_retorno`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`numero_linha_excluido_arquivo_retorno`  int(11) NOT NULL ,
PRIMARY KEY (`cd_excluido_arquivo_retorno`),
FOREIGN KEY (`cd_arquivo_retorno`) REFERENCES `arquivo_retorno` (`cd_arquivo_retorno`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=24025

;

-- ----------------------------
-- Table structure for `grafico`
-- ----------------------------
DROP TABLE IF EXISTS `grafico`;
CREATE TABLE `grafico` (
`cd_grafico`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_grafico`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_departamento`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_permissao`  int(11) UNSIGNED NULL DEFAULT NULL ,
`status_grafico`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_grafico`),
FOREIGN KEY (`cd_departamento`) REFERENCES `departamento` (`cd_departamento`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=14

;

-- ----------------------------
-- Table structure for `grafico_acesso`
-- ----------------------------
DROP TABLE IF EXISTS `grafico_acesso`;
CREATE TABLE `grafico_acesso` (
`cd_grafico_acesso`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_grafico`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(11) UNSIGNED NULL DEFAULT NULL ,
`data_grafico_acesso`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_grafico_acesso`),
FOREIGN KEY (`cd_grafico`) REFERENCES `grafico` (`cd_grafico`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=4680

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
`cd_permissao`  int(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`cd_menu`),
FOREIGN KEY (`cd_permissao`) REFERENCES `permissao` (`cd_permissao`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=48

;

-- ----------------------------
-- Table structure for `menu_lateral`
-- ----------------------------
DROP TABLE IF EXISTS `menu_lateral`;
CREATE TABLE `menu_lateral` (
`cd_menu_lateral`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`link`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`ordem`  int(11) NULL DEFAULT NULL ,
`modulo`  enum('RH','TELEFONIA','TELECOM') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`pai`  int(11) NULL DEFAULT NULL ,
`cd_permissao`  int(11) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`cd_menu_lateral`),
FOREIGN KEY (`cd_permissao`) REFERENCES `permissao` (`cd_permissao`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=59

;

-- ----------------------------
-- Table structure for `meta_dados`
-- ----------------------------
DROP TABLE IF EXISTS `meta_dados`;
CREATE TABLE `meta_dados` (
`cd_meta_dados`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_meta_tipo`  int(10) UNSIGNED NOT NULL ,
`tipo`  enum('A','M') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`numero`  float(10,2) NULL DEFAULT NULL ,
`data`  date NULL DEFAULT NULL ,
PRIMARY KEY (`cd_meta_dados`),
FOREIGN KEY (`cd_meta_tipo`) REFERENCES `meta_tipo` (`cd_meta_tipo`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=28

;

-- ----------------------------
-- Table structure for `meta_tipo`
-- ----------------------------
DROP TABLE IF EXISTS `meta_tipo`;
CREATE TABLE `meta_tipo` (
`cd_meta_tipo`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_meta_tipo`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=5

;

-- ----------------------------
-- Table structure for `ocorrencia_arquivo_retorno`
-- ----------------------------
DROP TABLE IF EXISTS `ocorrencia_arquivo_retorno`;
CREATE TABLE `ocorrencia_arquivo_retorno` (
`cd_ocorrencia_arquivo_retorno`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`codigo_ocorrencia_arquivo_retorno`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nome_ocorrencia_arquivo_retorno`  varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo_ocorrencia_arquivo_retorno`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_banco`  int(11) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`cd_ocorrencia_arquivo_retorno`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=978

;

-- ----------------------------
-- Table structure for `parametro`
-- ----------------------------
DROP TABLE IF EXISTS `parametro`;
CREATE TABLE `parametro` (
`cd_parametro`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_parametro`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`legenda_parametro`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`mascara_parametro`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`campo_parametro`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`variavel_parametro`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo_parametro`  enum('NUMERO','LETRA','MOEDA','DATA','PORCETAGEM') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status_parametro`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_parametro`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=15

;

-- ----------------------------
-- Table structure for `perfil`
-- ----------------------------
DROP TABLE IF EXISTS `perfil`;
CREATE TABLE `perfil` (
`cd_perfil`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_perfil`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_criacao_perfil`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`data_atualizacao_perfil`  timestamp NULL DEFAULT NULL ,
`criador_perfil`  int(11) NULL DEFAULT NULL ,
`atualizador_perfil`  int(11) NULL DEFAULT NULL ,
`status_perfil`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_perfil`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=56

;

-- ----------------------------
-- Table structure for `permissao`
-- ----------------------------
DROP TABLE IF EXISTS `permissao`;
CREATE TABLE `permissao` (
`cd_permissao`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_permissao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao_permissao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`pai_permissao`  int(11) NULL DEFAULT NULL ,
`ordem_permissao`  int(11) NULL DEFAULT NULL ,
`status_permissao`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`data_criacao_permissao`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_permissao`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=405

;

-- ----------------------------
-- Table structure for `permissao_perfil`
-- ----------------------------
DROP TABLE IF EXISTS `permissao_perfil`;
CREATE TABLE `permissao_perfil` (
`cd_permissao_perfil`  bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_perfil`  int(11) UNSIGNED NOT NULL ,
`cd_permissao`  int(11) UNSIGNED NOT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_permissao_perfil`),
FOREIGN KEY (`cd_perfil`) REFERENCES `perfil` (`cd_perfil`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_permissao`) REFERENCES `permissao` (`cd_permissao`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=40543

;

-- ----------------------------
-- Table structure for `registro_telecom`
-- ----------------------------
DROP TABLE IF EXISTS `registro_telecom`;
CREATE TABLE `registro_telecom` (
`cd_registro_telecom`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cod_registro_telecom`  smallint(11) NOT NULL ,
`status_registro_telecom`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`cd_registro_telecom`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=17

;

-- ----------------------------
-- Table structure for `relatorio`
-- ----------------------------
DROP TABLE IF EXISTS `relatorio`;
CREATE TABLE `relatorio` (
`cd_relatorio`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_relatorio`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao_relatorio`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao_parametro_relatorio`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_departamento`  int(11) UNSIGNED NULL DEFAULT NULL ,
`banco_relatorio`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`query_relatorio`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`criador_relatorio`  int(11) NULL DEFAULT NULL ,
`data_criacao_relatorio`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`atualizador_relatorio`  int(11) NULL DEFAULT NULL ,
`data_atualizacao_relatorio`  timestamp NULL DEFAULT NULL ,
`cd_permissao`  int(11) UNSIGNED NULL DEFAULT NULL ,
`status_relatorio`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_relatorio`),
FOREIGN KEY (`cd_departamento`) REFERENCES `departamento` (`cd_departamento`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_permissao`) REFERENCES `permissao` (`cd_permissao`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=215

;

-- ----------------------------
-- Table structure for `relatorio_acesso`
-- ----------------------------
DROP TABLE IF EXISTS `relatorio_acesso`;
CREATE TABLE `relatorio_acesso` (
`cd_relatorio_acesso`  bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_relatorio`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_relatorio_acesso`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`cd_relatorio_acesso`),
FOREIGN KEY (`cd_relatorio`) REFERENCES `relatorio` (`cd_relatorio`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=9220

;

-- ----------------------------
-- Table structure for `relatorio_parametro`
-- ----------------------------
DROP TABLE IF EXISTS `relatorio_parametro`;
CREATE TABLE `relatorio_parametro` (
`cd_relatorio_parametro`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_relatorio`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_parametro`  int(10) UNSIGNED NULL DEFAULT NULL ,
`nome_relatorio_parametro`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`cd_relatorio_parametro`),
FOREIGN KEY (`cd_parametro`) REFERENCES `parametro` (`cd_parametro`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_relatorio`) REFERENCES `relatorio` (`cd_relatorio`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=736

;

-- ----------------------------
-- Table structure for `status`
-- ----------------------------
DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
`sigla_status`  char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
`nome_status`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`sigla_status`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `tabelaexemplo`
-- ----------------------------
DROP TABLE IF EXISTS `tabelaexemplo`;
CREATE TABLE `tabelaexemplo` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`data`  timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ,
`descricao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=93

;

-- ----------------------------
-- Table structure for `tcom_avalAPAGAR`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_avalAPAGAR`;
CREATE TABLE `tcom_avalAPAGAR` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=6

;

-- ----------------------------
-- Table structure for `tcom_edificacaoAPAGAR`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_edificacaoAPAGAR`;
CREATE TABLE `tcom_edificacaoAPAGAR` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`controle`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_unidade`  int(11) NOT NULL ,
`inicio`  date NULL DEFAULT NULL ,
`previsao`  date NULL DEFAULT NULL ,
`contrato`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`origem`  enum('NOVO','ENDERECO') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`telefone`  varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`celular`  varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`idNode`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cep`  char(9) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`endereco`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`cidade`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`cd_estado`  int(11) UNSIGNED NOT NULL ,
`bairro`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`numero`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`complemento`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`referencia`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`concluido`  enum('SIM','NAO') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'NAO' ,
`conclusao`  date NULL DEFAULT NULL ,
`idAval`  int(10) UNSIGNED NULL DEFAULT NULL ,
`anexo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`observacao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idNode`) REFERENCES `tcom_nodeAPAGAR` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_estado`) REFERENCES `adminti`.`estado` (`cd_estado`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idAval`) REFERENCES `tcom_avalAPAGAR` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=27538

;

-- ----------------------------
-- Table structure for `tcom_nodeAPAGAR`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_nodeAPAGAR`;
CREATE TABLE `tcom_nodeAPAGAR` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`node`  varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`distancia`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NOT NULL ,
`cep`  char(9) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`endereco`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`numero`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cidade`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_estado`  int(10) UNSIGNED NULL DEFAULT NULL ,
`bairro`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`complemento`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`coordx`  int(11) NULL DEFAULT NULL ,
`coordy`  int(11) NULL DEFAULT NULL ,
`pop`  enum('hub','headend') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cm`  enum('sim','nao','parcial') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tv`  enum('sim','nao') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_estado`) REFERENCES `adminti`.`estado` (`cd_estado`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3433

;

-- ----------------------------
-- Table structure for `tcom_nodeCelulaAPAGAR`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_nodeCelulaAPAGAR`;
CREATE TABLE `tcom_nodeCelulaAPAGAR` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idNode`  int(10) UNSIGNED NOT NULL ,
`tipo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idNode`) REFERENCES `tcom_nodeAPAGAR` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3437

;

-- ----------------------------
-- Table structure for `tipo_linha_arquivo_retorno`
-- ----------------------------
DROP TABLE IF EXISTS `tipo_linha_arquivo_retorno`;
CREATE TABLE `tipo_linha_arquivo_retorno` (
`cd_tipo_linha_arquivo_retorno`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`descricao_tipo_linha_arquivo_retorno`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`cd_tipo_linha_arquivo_retorno`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=5

;

-- ----------------------------
-- Table structure for `usuario`
-- ----------------------------
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
`cd_usuario`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome_usuario`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`email_usuario`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`login_usuario`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`senha_usuario`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_cidade`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_departamento`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_perfil`  int(11) UNSIGNED NULL DEFAULT NULL ,
`criador_usuario`  int(11) NULL DEFAULT NULL ,
`data_criacao_usuario`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`atualizador_usuario`  int(11) NULL DEFAULT NULL ,
`data_atualizacao_usuario`  timestamp NULL DEFAULT NULL ,
`ramal_usuario`  char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status_usuario`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`cd_usuario`),
FOREIGN KEY (`cd_cidade`) REFERENCES `cidade` (`cd_cidade`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_departamento`) REFERENCES `departamento` (`cd_departamento`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_perfil`) REFERENCES `perfil` (`cd_perfil`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=4422

;

-- ----------------------------
-- Table structure for `usuario_unidade`
-- ----------------------------
DROP TABLE IF EXISTS `usuario_unidade`;
CREATE TABLE `usuario_unidade` (
`cd_usuario_unidade`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`cd_unidade`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`cd_usuario_unidade`),
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=18

;

-- ----------------------------
-- Auto increment value for `anatel_config`
-- ----------------------------
ALTER TABLE `anatel_config` AUTO_INCREMENT=3;

-- ----------------------------
-- Indexes structure for table anatel_frm
-- ----------------------------
CREATE INDEX `fk_cd_anatel_tipo_frm_anatel_frm` ON `anatel_frm`(`cd_anatel_tipo_frm`) USING BTREE ;
CREATE INDEX `fk_cd_departamento_anatel_frm` ON `anatel_frm`(`cd_departamento`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_anatel_frm` ON `anatel_frm`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_anatel_indicador` ON `anatel_frm`(`cd_anatel_indicador`) USING BTREE ;
CREATE INDEX `fk_cd_anatel_xml_anatel_frm` ON `anatel_frm`(`cd_anatel_xml`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_frm`
-- ----------------------------
ALTER TABLE `anatel_frm` AUTO_INCREMENT=27;

-- ----------------------------
-- Indexes structure for table anatel_indicador
-- ----------------------------
CREATE INDEX `fk_cd_departamento_anatel_indicador` ON `anatel_indicador`(`cd_departamento`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_indicador`
-- ----------------------------
ALTER TABLE `anatel_indicador` AUTO_INCREMENT=19;

-- ----------------------------
-- Indexes structure for table anatel_meta
-- ----------------------------
CREATE INDEX `fk_cd_anatel_frm_anatel_meta` ON `anatel_meta`(`cd_anatel_frm`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_meta`
-- ----------------------------
ALTER TABLE `anatel_meta` AUTO_INCREMENT=33;

-- ----------------------------
-- Indexes structure for table anatel_meta_campo
-- ----------------------------
CREATE INDEX `fk_cd_anatel_meta_anatel_meta_campo` ON `anatel_meta_campo`(`cd_anatel_meta`) USING BTREE ;
CREATE INDEX `fk_cd_anatel_quest_anatel_meta_campo` ON `anatel_meta_campo`(`cd_anatel_quest`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_meta_campo`
-- ----------------------------
ALTER TABLE `anatel_meta_campo` AUTO_INCREMENT=55;

-- ----------------------------
-- Indexes structure for table anatel_meta_res
-- ----------------------------
CREATE INDEX `fk_cd_anatel_meta_anatel_meta_res` ON `anatel_meta_res`(`cd_anatel_meta`) USING BTREE ;
CREATE INDEX `fk_cd_anatel_frm_anatel_meta_res` ON `anatel_meta_res`(`cd_anatel_frm`) USING BTREE ;
CREATE INDEX `fk_cd_anatel_motivo_just_anatel_meta_res` ON `anatel_meta_res`(`cd_anatel_motivo_just`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_anatel_meta_res` ON `anatel_meta_res`(`cd_unidade`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_anatel_meta_res` ON `anatel_meta_res`(`cd_usuario`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_meta_res`
-- ----------------------------
ALTER TABLE `anatel_meta_res` AUTO_INCREMENT=2774;

-- ----------------------------
-- Auto increment value for `anatel_motivo_just`
-- ----------------------------
ALTER TABLE `anatel_motivo_just` AUTO_INCREMENT=13;

-- ----------------------------
-- Indexes structure for table anatel_quest
-- ----------------------------
CREATE INDEX `fk_cd_anatel_frm_anatel_quest` ON `anatel_quest`(`cd_anatel_frm`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_quest`
-- ----------------------------
ALTER TABLE `anatel_quest` AUTO_INCREMENT=62;

-- ----------------------------
-- Indexes structure for table anatel_res
-- ----------------------------
CREATE INDEX `fk_cd_anatel_quest_anatel_res` ON `anatel_res`(`cd_anatel_quest`) USING BTREE ;
CREATE INDEX `fk_cd_anatel_frm_anatel_res` ON `anatel_res`(`cd_anatel_frm`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_anatel_res` ON `anatel_res`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_unidade` ON `anatel_res`(`cd_unidade`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_res`
-- ----------------------------
ALTER TABLE `anatel_res` AUTO_INCREMENT=44386;

-- ----------------------------
-- Indexes structure for table anatel_resp_indicador
-- ----------------------------
CREATE INDEX `fk_cd_anatel_frm_anatel_resp_indicador` ON `anatel_resp_indicador`(`cd_anatel_frm`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_anatel_resp_indicador` ON `anatel_resp_indicador`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_anatel_resp_indicador` ON `anatel_resp_indicador`(`cd_unidade`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_resp_indicador`
-- ----------------------------
ALTER TABLE `anatel_resp_indicador` AUTO_INCREMENT=853;

-- ----------------------------
-- Auto increment value for `anatel_tipo_frm`
-- ----------------------------
ALTER TABLE `anatel_tipo_frm` AUTO_INCREMENT=3;

-- ----------------------------
-- Auto increment value for `anatel_tipo_indicador`
-- ----------------------------
ALTER TABLE `anatel_tipo_indicador` AUTO_INCREMENT=6;

-- ----------------------------
-- Indexes structure for table anatel_xml
-- ----------------------------
CREATE INDEX `fk_cd_anatel_tipo_frm_anatel_xml` ON `anatel_xml`(`cd_anatel_tipo_frm`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `anatel_xml`
-- ----------------------------
ALTER TABLE `anatel_xml` AUTO_INCREMENT=7;

-- ----------------------------
-- Indexes structure for table arquivo_retorno
-- ----------------------------
CREATE INDEX `idx_cd_banco` ON `arquivo_retorno`(`cd_banco`) USING BTREE ;
CREATE INDEX `idx_data_arquivo_retorno` ON `arquivo_retorno`(`data_arquivo_retorno`) USING BTREE ;
CREATE INDEX `idx_nome_empresa_arquivo_retorno` ON `arquivo_retorno`(`nome_empresa_arquivo_retorno`) USING BTREE ;
CREATE INDEX `idx_data_insercao_arquivo_retorno` ON `arquivo_retorno`(`data_insercao_arquivo_retorno`) USING BTREE ;
CREATE INDEX `idx_cd_arquivo_retorno` ON `arquivo_retorno`(`cd_arquivo_retorno`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `arquivo_retorno`
-- ----------------------------
ALTER TABLE `arquivo_retorno` AUTO_INCREMENT=16574;

-- ----------------------------
-- Indexes structure for table banco
-- ----------------------------
CREATE INDEX `idx_cd_banco` ON `banco`(`cd_banco`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `banco`
-- ----------------------------
ALTER TABLE `banco` AUTO_INCREMENT=9;

-- ----------------------------
-- Indexes structure for table ci_sessions
-- ----------------------------
CREATE INDEX `last_activity_idx` ON `ci_sessions`(`last_activity`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `cidade`
-- ----------------------------
ALTER TABLE `cidade` AUTO_INCREMENT=2;

-- ----------------------------
-- Indexes structure for table config_usuario
-- ----------------------------
CREATE INDEX `fk_cd_usuario_config_usuario` ON `config_usuario`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_perfil_config_usuario` ON `config_usuario`(`cd_perfil`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `config_usuario`
-- ----------------------------
ALTER TABLE `config_usuario` AUTO_INCREMENT=2422;

-- ----------------------------
-- Indexes structure for table conteudo_arquivo_retorno
-- ----------------------------
CREATE INDEX `idx_cd_arquivo_retorno` ON `conteudo_arquivo_retorno`(`cd_arquivo_retorno`) USING BTREE ;
CREATE INDEX `fk_cd_tipo_linha_arquivo_retorno` ON `conteudo_arquivo_retorno`(`cd_tipo_linha_arquivo_retorno`) USING BTREE ;
CREATE INDEX `idx_numero_incricao_arquivo_retorno` ON `conteudo_arquivo_retorno`(`numero_inscricao_arquivo_retorno`) USING BTREE ;
CREATE INDEX `fk_cd_ocorrencia_arquivo_retorno` ON `conteudo_arquivo_retorno`(`cd_ocorrencia_arquivo_retorno`) USING BTREE ;
CREATE INDEX `idx_titulo_arquivo_retorno` ON `conteudo_arquivo_retorno`(`titulo_arquivo_retorno`) USING BTREE ;
CREATE INDEX `idx_permissor_arquivo_retorno` ON `conteudo_arquivo_retorno`(`permissor_arquivo_retorno`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `conteudo_arquivo_retorno`
-- ----------------------------
ALTER TABLE `conteudo_arquivo_retorno` AUTO_INCREMENT=5010770;

-- ----------------------------
-- Auto increment value for `departamento`
-- ----------------------------
ALTER TABLE `departamento` AUTO_INCREMENT=36;

-- ----------------------------
-- Indexes structure for table email_envia
-- ----------------------------
CREATE INDEX `fk_cd_permissao_email_envia` ON `email_envia`(`cd_permissao`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `email_envia`
-- ----------------------------
ALTER TABLE `email_envia` AUTO_INCREMENT=40;

-- ----------------------------
-- Indexes structure for table email_grupo
-- ----------------------------
CREATE UNIQUE INDEX `email` ON `email_grupo`(`email`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `email_grupo`
-- ----------------------------
ALTER TABLE `email_grupo` AUTO_INCREMENT=113;

-- ----------------------------
-- Indexes structure for table email_grupo_recebe
-- ----------------------------
CREATE INDEX `fk_idEmailGrupo_email_grupo_recebe` ON `email_grupo_recebe`(`idEmailGrupo`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_email_grupo_recebe` ON `email_grupo_recebe`(`cd_unidade`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `email_grupo_recebe`
-- ----------------------------
ALTER TABLE `email_grupo_recebe` AUTO_INCREMENT=1421;

-- ----------------------------
-- Indexes structure for table email_recebe
-- ----------------------------
CREATE INDEX `fk_idEmailEnvia_email_recebe` ON `email_recebe`(`idEmailEnvia`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_email_recebe` ON `email_recebe`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_email_recebe` ON `email_recebe`(`cd_unidade`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `email_recebe`
-- ----------------------------
ALTER TABLE `email_recebe` AUTO_INCREMENT=1563;

-- ----------------------------
-- Indexes structure for table excluido_arquivo_retorno
-- ----------------------------
CREATE INDEX `idx_cd_arquivo_retorno` ON `excluido_arquivo_retorno`(`cd_arquivo_retorno`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `excluido_arquivo_retorno`
-- ----------------------------
ALTER TABLE `excluido_arquivo_retorno` AUTO_INCREMENT=24025;

-- ----------------------------
-- Indexes structure for table grafico
-- ----------------------------
CREATE UNIQUE INDEX `idx_cd_grafico` ON `grafico`(`cd_grafico`) USING BTREE ;
CREATE INDEX `idx_cd_departamento` ON `grafico`(`cd_departamento`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `grafico`
-- ----------------------------
ALTER TABLE `grafico` AUTO_INCREMENT=14;

-- ----------------------------
-- Indexes structure for table grafico_acesso
-- ----------------------------
CREATE INDEX `fk_cd_grafico_grafico_acesso` ON `grafico_acesso`(`cd_grafico`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_grafico_acesso` ON `grafico_acesso`(`cd_usuario`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `grafico_acesso`
-- ----------------------------
ALTER TABLE `grafico_acesso` AUTO_INCREMENT=4680;

-- ----------------------------
-- Indexes structure for table menu
-- ----------------------------
CREATE INDEX `fk_cd_permissao` ON `menu`(`cd_permissao`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `menu`
-- ----------------------------
ALTER TABLE `menu` AUTO_INCREMENT=48;

-- ----------------------------
-- Indexes structure for table menu_lateral
-- ----------------------------
CREATE INDEX `fk_cd_permissao_menu_lateral` ON `menu_lateral`(`cd_permissao`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `menu_lateral`
-- ----------------------------
ALTER TABLE `menu_lateral` AUTO_INCREMENT=59;

-- ----------------------------
-- Indexes structure for table meta_dados
-- ----------------------------
CREATE INDEX `fk_cd_meta_tipo_meta_dados` ON `meta_dados`(`cd_meta_tipo`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `meta_dados`
-- ----------------------------
ALTER TABLE `meta_dados` AUTO_INCREMENT=28;

-- ----------------------------
-- Auto increment value for `meta_tipo`
-- ----------------------------
ALTER TABLE `meta_tipo` AUTO_INCREMENT=5;

-- ----------------------------
-- Auto increment value for `ocorrencia_arquivo_retorno`
-- ----------------------------
ALTER TABLE `ocorrencia_arquivo_retorno` AUTO_INCREMENT=978;

-- ----------------------------
-- Auto increment value for `parametro`
-- ----------------------------
ALTER TABLE `parametro` AUTO_INCREMENT=15;

-- ----------------------------
-- Indexes structure for table perfil
-- ----------------------------
CREATE INDEX `idx_cd_perfil` ON `perfil`(`cd_perfil`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `perfil`
-- ----------------------------
ALTER TABLE `perfil` AUTO_INCREMENT=56;

-- ----------------------------
-- Indexes structure for table permissao
-- ----------------------------
CREATE INDEX `idx_cd_permissao` ON `permissao`(`cd_permissao`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `permissao`
-- ----------------------------
ALTER TABLE `permissao` AUTO_INCREMENT=405;

-- ----------------------------
-- Indexes structure for table permissao_perfil
-- ----------------------------
CREATE INDEX `fk_cd_perfil` ON `permissao_perfil`(`cd_perfil`) USING BTREE ;
CREATE INDEX `fk_cd_permissao_cd_permissao_perfil` ON `permissao_perfil`(`cd_permissao`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `permissao_perfil`
-- ----------------------------
ALTER TABLE `permissao_perfil` AUTO_INCREMENT=40543;

-- ----------------------------
-- Indexes structure for table registro_telecom
-- ----------------------------
CREATE INDEX `idx_cod_registro_telecom` ON `registro_telecom`(`cod_registro_telecom`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `registro_telecom`
-- ----------------------------
ALTER TABLE `registro_telecom` AUTO_INCREMENT=17;

-- ----------------------------
-- Indexes structure for table relatorio
-- ----------------------------
CREATE INDEX `fk_cd_departamento_relatorio` ON `relatorio`(`cd_departamento`) USING BTREE ;
CREATE INDEX `fk_cd_permissao_relatorio` ON `relatorio`(`cd_permissao`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `relatorio`
-- ----------------------------
ALTER TABLE `relatorio` AUTO_INCREMENT=215;

-- ----------------------------
-- Indexes structure for table relatorio_acesso
-- ----------------------------
CREATE INDEX `fk_cd_relatorio_relatorio_acesso` ON `relatorio_acesso`(`cd_relatorio`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_relatorio_acesso` ON `relatorio_acesso`(`cd_usuario`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `relatorio_acesso`
-- ----------------------------
ALTER TABLE `relatorio_acesso` AUTO_INCREMENT=9220;

-- ----------------------------
-- Indexes structure for table relatorio_parametro
-- ----------------------------
CREATE INDEX `fk_cd_relatorio_relatorio_parametro` ON `relatorio_parametro`(`cd_relatorio`) USING BTREE ;
CREATE INDEX `fk_cd_parametro_relatorio_parametro` ON `relatorio_parametro`(`cd_parametro`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `relatorio_parametro`
-- ----------------------------
ALTER TABLE `relatorio_parametro` AUTO_INCREMENT=736;

-- ----------------------------
-- Auto increment value for `tabelaexemplo`
-- ----------------------------
ALTER TABLE `tabelaexemplo` AUTO_INCREMENT=93;

-- ----------------------------
-- Auto increment value for `tcom_avalAPAGAR`
-- ----------------------------
ALTER TABLE `tcom_avalAPAGAR` AUTO_INCREMENT=6;

-- ----------------------------
-- Indexes structure for table tcom_edificacaoAPAGAR
-- ----------------------------
CREATE UNIQUE INDEX `idx_controle_tcom_controle` ON `tcom_edificacaoAPAGAR`(`controle`) USING BTREE ;
CREATE INDEX `fk_idAval_tcom_edificacao` ON `tcom_edificacaoAPAGAR`(`idAval`) USING BTREE ;
CREATE INDEX `fk_cd_estado_tcom_edificacao` ON `tcom_edificacaoAPAGAR`(`cd_estado`) USING BTREE ;
CREATE INDEX `idx_contrato_tcom_edificacao` ON `tcom_edificacaoAPAGAR`(`contrato`) USING BTREE ;
CREATE INDEX `fk_idNode_tcom_edificacao` ON `tcom_edificacaoAPAGAR`(`idNode`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_edificacaoAPAGAR`
-- ----------------------------
ALTER TABLE `tcom_edificacaoAPAGAR` AUTO_INCREMENT=27538;

-- ----------------------------
-- Indexes structure for table tcom_nodeAPAGAR
-- ----------------------------
CREATE UNIQUE INDEX `descricao` ON `tcom_nodeAPAGAR`(`descricao`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_node` ON `tcom_nodeAPAGAR`(`cd_unidade`) USING BTREE ;
CREATE INDEX `fk_cd_estado_node` ON `tcom_nodeAPAGAR`(`cd_estado`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_nodeAPAGAR`
-- ----------------------------
ALTER TABLE `tcom_nodeAPAGAR` AUTO_INCREMENT=3433;

-- ----------------------------
-- Indexes structure for table tcom_nodeCelulaAPAGAR
-- ----------------------------
CREATE INDEX `fk_idNode_nodeCelula` ON `tcom_nodeCelulaAPAGAR`(`idNode`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_nodeCelulaAPAGAR`
-- ----------------------------
ALTER TABLE `tcom_nodeCelulaAPAGAR` AUTO_INCREMENT=3437;

-- ----------------------------
-- Indexes structure for table tipo_linha_arquivo_retorno
-- ----------------------------
CREATE INDEX `idx_cd_tipo_linha_arquivo_retorno` ON `tipo_linha_arquivo_retorno`(`cd_tipo_linha_arquivo_retorno`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tipo_linha_arquivo_retorno`
-- ----------------------------
ALTER TABLE `tipo_linha_arquivo_retorno` AUTO_INCREMENT=5;

-- ----------------------------
-- Indexes structure for table usuario
-- ----------------------------
CREATE UNIQUE INDEX `idx_login_usuario` ON `usuario`(`login_usuario`) USING BTREE ;
CREATE UNIQUE INDEX `idx_email_usuario` ON `usuario`(`email_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_departamento` ON `usuario`(`cd_departamento`) USING BTREE ;
CREATE INDEX `fk_cd_perfil_usuario` ON `usuario`(`cd_perfil`) USING BTREE ;
CREATE INDEX `fk_cd_cidade_usuario` ON `usuario`(`cd_cidade`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `usuario`
-- ----------------------------
ALTER TABLE `usuario` AUTO_INCREMENT=4422;

-- ----------------------------
-- Indexes structure for table usuario_unidade
-- ----------------------------
CREATE INDEX `fk_cd_usuario_usuario_unidade` ON `usuario_unidade`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_usuario_unidade` ON `usuario_unidade`(`cd_unidade`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `usuario_unidade`
-- ----------------------------
ALTER TABLE `usuario_unidade` AUTO_INCREMENT=18;
