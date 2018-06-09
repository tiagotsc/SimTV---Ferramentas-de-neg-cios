/*
Navicat MySQL Data Transfer

Source Server         : Mysql - Novo
Source Server Version : 50540
Source Host           : 192.168.140.91:3306
Source Database       : telecom

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-10-20 09:55:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tcom_analise_financ`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_analise_financ`;
CREATE TABLE `tcom_analise_financ` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idContrato`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`aprovado`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_aprovacao`  timestamp NULL DEFAULT NULL ,
`cd_usuario_disparador`  int(11) UNSIGNED NULL DEFAULT NULL ,
`data_disparo_email`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario_disparador`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idContrato`) REFERENCES `tcom_contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=4

;

-- ----------------------------
-- Table structure for `tcom_aval`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_aval`;
CREATE TABLE `tcom_aval` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=6

;

-- ----------------------------
-- Table structure for `tcom_circuito`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_circuito`;
CREATE TABLE `tcom_circuito` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`designacao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`idInterface`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idTaxaDigital`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario_cadastro`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT NULL ,
`cd_usuario_atualizacao`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_atualizacao`  timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`obs`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idInterface`) REFERENCES `tcom_interface` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idTaxaDigital`) REFERENCES `tcom_taxa_digital` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=8184

;

-- ----------------------------
-- Table structure for `tcom_cliente`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_cliente`;
CREATE TABLE `tcom_cliente` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`titulo`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`email`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cnpj`  varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`razaoSocial`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`inscEstadual`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`inscMunicipal`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`observacao`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3005

;

-- ----------------------------
-- Table structure for `tcom_cliente_end`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_cliente_end`;
CREATE TABLE `tcom_cliente_end` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idCliente`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cep`  varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`endereco`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`numero`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`bairro`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cidade`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_estado`  int(10) UNSIGNED NULL DEFAULT NULL ,
`complemento`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_estado`) REFERENCES `adminti`.`estado` (`cd_estado`) ON DELETE RESTRICT ON UPDATE RESTRICT,
FOREIGN KEY (`idCliente`) REFERENCES `tcom_cliente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=27648

;

-- ----------------------------
-- Table structure for `tcom_cliente_telefone`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_cliente_telefone`;
CREATE TABLE `tcom_cliente_telefone` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idCliente`  int(10) UNSIGNED NULL DEFAULT NULL ,
`telefone`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idCliente`) REFERENCES `tcom_cliente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=98

;

-- ----------------------------
-- Table structure for `tcom_contrato`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_contrato`;
CREATE TABLE `tcom_contrato` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`numero`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`idOper`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idCliente`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idCircuito`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_inicio`  date NULL DEFAULT NULL ,
`data_fim`  date NULL DEFAULT NULL ,
`duracao_mes`  int(11) NULL DEFAULT NULL ,
`qtd_circuitos`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`anexo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('A','I','C','P') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'P' ,
`data_cadastro`  timestamp NULL DEFAULT '0000-00-00 00:00:00' ,
`cd_usuario_cadastro`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_atualizacao`  timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ,
`cd_usuario_atualizacao`  int(10) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idCircuito`) REFERENCES `tcom_circuito` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idCliente`) REFERENCES `tcom_cliente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idOper`) REFERENCES `tcom_oper` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=8187

;

-- ----------------------------
-- Table structure for `tcom_contrato_anexo`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_contrato_anexo`;
CREATE TABLE `tcom_contrato_anexo` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idContrato`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`anexo_label`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`anexo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idContrato`) REFERENCES `tcom_contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=24

;

-- ----------------------------
-- Table structure for `tcom_contrato_circuito`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_contrato_circuito`;
CREATE TABLE `tcom_contrato_circuito` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idCircuito`  int(11) UNSIGNED NULL DEFAULT NULL ,
`idContrato`  int(11) UNSIGNED NULL DEFAULT NULL ,
`idCliente`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idTaxaDigital`  int(11) UNSIGNED NULL DEFAULT NULL ,
`idInterface`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cep`  varchar(9) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`endereco`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`numero`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`bairro`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cidade`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_estado`  int(11) NULL DEFAULT NULL ,
`complemento`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`telefones`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_usuario`  int(11) NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idCircuito`) REFERENCES `tcom_circuito` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`idCliente`) REFERENCES `tcom_cliente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idContrato`) REFERENCES `tcom_contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`idInterface`) REFERENCES `tcom_interface` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idTaxaDigital`) REFERENCES `tcom_taxa_digital` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=6154

;

-- ----------------------------
-- Table structure for `tcom_contrato_equip`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_contrato_equip`;
CREATE TABLE `tcom_contrato_equip` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idContrato`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idEquipModCod`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario_cadastro`  int(11) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario_cadastro`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idContrato`) REFERENCES `tcom_contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`idEquipModCod`) REFERENCES `tcom_equip_modelo_codigo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=34

;

-- ----------------------------
-- Table structure for `tcom_contrato_valor`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_contrato_valor`;
CREATE TABLE `tcom_contrato_valor` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idContrato`  int(10) UNSIGNED NULL DEFAULT NULL ,
`valor`  decimal(15,2) NULL DEFAULT NULL ,
`mens_contratada_sem_imposto`  decimal(15,2) NULL DEFAULT NULL ,
`mens_atual_sem_imposto`  decimal(15,2) NULL DEFAULT NULL ,
`mens_atual_com_imposto`  decimal(15,2) NULL DEFAULT NULL ,
`taxa_inst_com_imposto`  decimal(15,2) NULL DEFAULT NULL ,
`taxa_inst_sem_imposto`  decimal(15,2) NULL DEFAULT NULL ,
`pro_rata_primeiro_mes`  decimal(15,2) NULL DEFAULT NULL ,
`primeira_mensalidade`  decimal(15,2) NULL DEFAULT NULL ,
`receita_total_com_imposto`  decimal(15,2) NULL DEFAULT NULL ,
`receita_total_sem_imposto`  decimal(15,2) NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`mao_obra_empreiteira`  decimal(15,2) NULL DEFAULT NULL ,
`aquisicao_equipamento`  decimal(15,2) NULL DEFAULT NULL ,
`data_pri_fatura`  date NULL DEFAULT NULL ,
`sub_total`  decimal(15,2) NULL DEFAULT NULL ,
`cd_usuario`  int(11) UNSIGNED NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idContrato`) REFERENCES `tcom_contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=6178

;

-- ----------------------------
-- Table structure for `tcom_edificacao`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_edificacao`;
CREATE TABLE `tcom_edificacao` (
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
FOREIGN KEY (`idNode`) REFERENCES `tcom_node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_estado`) REFERENCES `adminti`.`estado` (`cd_estado`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idAval`) REFERENCES `tcom_aval` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=27863

;

-- ----------------------------
-- Table structure for `tcom_equip_marca`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_equip_marca`;
CREATE TABLE `tcom_equip_marca` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=10

;

-- ----------------------------
-- Table structure for `tcom_equip_modelo`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_equip_modelo`;
CREATE TABLE `tcom_equip_modelo` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idEquipMarca`  int(10) UNSIGNED NULL DEFAULT NULL ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idEquipMarca`) REFERENCES `tcom_equip_marca` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=9

;

-- ----------------------------
-- Table structure for `tcom_equip_modelo_codigo`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_equip_modelo_codigo`;
CREATE TABLE `tcom_equip_modelo_codigo` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idEquipModelo`  int(10) UNSIGNED NULL DEFAULT NULL ,
`identificacao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`codigo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idEquipModelo`) REFERENCES `tcom_equip_modelo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=40

;

-- ----------------------------
-- Table structure for `tcom_interface`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_interface`;
CREATE TABLE `tcom_interface` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`descricao`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`status`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`dataCadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=28

;

-- ----------------------------
-- Table structure for `tcom_log_contrato`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_log_contrato`;
CREATE TABLE `tcom_log_contrato` (
`id`  bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idContrato`  int(11) NULL DEFAULT NULL ,
`numero`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`idOper`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idCliente`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idCircuito`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_inicio`  date NULL DEFAULT NULL ,
`data_fim`  date NULL DEFAULT NULL ,
`duracao_mes`  int(11) NULL DEFAULT NULL ,
`qtd_circuitos`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`anexo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('A','I','C') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`acao`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_acao`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`query_sql`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`query_equip`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`query_circuito`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3576

;

-- ----------------------------
-- Table structure for `tcom_node`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_node`;
CREATE TABLE `tcom_node` (
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
AUTO_INCREMENT=3435

;

-- ----------------------------
-- Table structure for `tcom_nodeCelula`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_nodeCelula`;
CREATE TABLE `tcom_nodeCelula` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idNode`  int(10) UNSIGNED NOT NULL ,
`tipo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idNode`) REFERENCES `tcom_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3437

;

-- ----------------------------
-- Table structure for `tcom_oper`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_oper`;
CREATE TABLE `tcom_oper` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`titulo`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`email`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`razaoSocial`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`inscEstadual`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`inscMunicipal`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`pai`  smallint(6) NULL DEFAULT 0 ,
`cobInst`  enum('SIM','NAO') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'NAO' ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`observacao`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=588

;

-- ----------------------------
-- Table structure for `tcom_oper_cobr`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_oper_cobr`;
CREATE TABLE `tcom_oper_cobr` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idOper`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cnpj`  varchar(22) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cep`  varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`endereco`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`numero`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cd_estado`  int(10) UNSIGNED NULL DEFAULT NULL ,
`bairro`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cidade`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`complemento`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`hub`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`headend`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_estado`) REFERENCES `adminti`.`estado` (`cd_estado`) ON DELETE RESTRICT ON UPDATE RESTRICT,
FOREIGN KEY (`idOper`) REFERENCES `tcom_oper` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3596

;

-- ----------------------------
-- Table structure for `tcom_oper_cobr_telefone`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_oper_cobr_telefone`;
CREATE TABLE `tcom_oper_cobr_telefone` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idOper`  int(11) UNSIGNED NULL DEFAULT NULL ,
`idOperCobr`  int(10) UNSIGNED NULL DEFAULT NULL ,
`telefone`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idOper`) REFERENCES `tcom_oper` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`idOperCobr`) REFERENCES `tcom_oper_cobr` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=75

;

-- ----------------------------
-- Table structure for `tcom_oper_inst`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_oper_inst`;
CREATE TABLE `tcom_oper_inst` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idOper`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cnpj`  varchar(22) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cep`  varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`endereco`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`numero`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`bairro`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cd_estado`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cidade`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`complemento`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_estado`) REFERENCES `adminti`.`estado` (`cd_estado`) ON DELETE RESTRICT ON UPDATE RESTRICT,
FOREIGN KEY (`idOper`) REFERENCES `tcom_oper` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3644

;

-- ----------------------------
-- Table structure for `tcom_status_hist`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_status_hist`;
CREATE TABLE `tcom_status_hist` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`pai`  smallint(6) UNSIGNED NULL DEFAULT 0 ,
`status`  enum('I','A') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`final`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N' ,
`idEmailEnvia`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idEmailEnvia`) REFERENCES `sistema`.`email_envia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=77

;

-- ----------------------------
-- Table structure for `tcom_taxa_digital`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_taxa_digital`;
CREATE TABLE `tcom_taxa_digital` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`velocidade`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`mnemonico`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=129

;

-- ----------------------------
-- Table structure for `tcom_viab`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_viab`;
CREATE TABLE `tcom_viab` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`controle`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`n_solicitacao`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`idViabTipo`  int(11) UNSIGNED NULL DEFAULT NULL ,
`idContrato`  int(10) UNSIGNED NULL DEFAULT NULL ,
`dt_solicitacao`  date NULL DEFAULT NULL ,
`dt_prazo`  date NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idOper`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idCliente`  int(11) UNSIGNED NULL DEFAULT NULL ,
`qtd_circuitos`  int(11) NULL DEFAULT NULL ,
`idInterface`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idTaxaDigital`  int(11) UNSIGNED NULL DEFAULT NULL ,
`redundancia`  enum('NAO','EQUIPAMENTO','FIBRA') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'NAO' ,
`vistoriado`  enum('S','N') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N' ,
`observacao`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`anexo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idTaxaDigital`) REFERENCES `tcom_taxa_digital` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_unidade`) REFERENCES `adminti`.`unidade` (`cd_unidade`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idCliente`) REFERENCES `tcom_cliente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idContrato`) REFERENCES `tcom_contrato` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
FOREIGN KEY (`idInterface`) REFERENCES `tcom_interface` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idOper`) REFERENCES `tcom_oper` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idViabTipo`) REFERENCES `tcom_viab_tipo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3912

;

-- ----------------------------
-- Table structure for `tcom_viab_md_end`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_viab_md_end`;
CREATE TABLE `tcom_viab_md_end` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idViab`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cep`  varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`endereco`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`numero`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`bairro`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`cd_estado`  int(10) UNSIGNED NULL DEFAULT NULL ,
`complemento`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_estado`) REFERENCES `adminti`.`estado` (`cd_estado`) ON DELETE RESTRICT ON UPDATE RESTRICT,
FOREIGN KEY (`idViab`) REFERENCES `tcom_viab` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=10

;

-- ----------------------------
-- Table structure for `tcom_viab_md_end_tel`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_viab_md_end_tel`;
CREATE TABLE `tcom_viab_md_end_tel` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idViabMdEnd`  int(10) UNSIGNED NULL DEFAULT NULL ,
`telefone`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`tipo`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idViabMdEnd`) REFERENCES `tcom_viab_md_end` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=24

;

-- ----------------------------
-- Table structure for `tcom_viab_pend`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_viab_pend`;
CREATE TABLE `tcom_viab_pend` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idViab`  int(10) UNSIGNED NULL DEFAULT NULL ,
`status`  enum('Respondido','Pendente') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'Pendente' ,
`pergunta`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`cd_usuario_pergunta`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro_pergunta`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
`resposta`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`cd_usuario_resposta`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro_resposta`  timestamp NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario_resposta`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_usuario_pergunta`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idViab`) REFERENCES `tcom_viab` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=70

;

-- ----------------------------
-- Table structure for `tcom_viab_resp`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_viab_resp`;
CREATE TABLE `tcom_viab_resp` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idViab`  int(10) UNSIGNED NULL DEFAULT NULL ,
`cd_unidade`  int(10) UNSIGNED NULL DEFAULT NULL ,
`viavel`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`end_enco`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`anexo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cabo`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cordoalha`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`canalizacao`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`node_distancia`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`aprovacao`  enum('N','S','C') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`data_aprovacao`  timestamp NULL DEFAULT NULL ,
`cd_usuario_aprovacao`  int(11) UNSIGNED NULL DEFAULT NULL ,
`prazo_ativacao`  date NULL DEFAULT NULL ,
`observacao`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idContrato`  int(10) UNSIGNED NULL DEFAULT NULL ,
`idContratoAtual`  int(10) UNSIGNED NULL DEFAULT NULL ,
`gerou_contrato`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N' ,
`idStatusHist`  int(10) UNSIGNED NULL DEFAULT NULL ,
`ativou`  enum('N','S') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N' ,
`data_ativacao`  timestamp NULL DEFAULT NULL ,
`cd_usuario_ativacao`  int(11) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`idContratoAtual`) REFERENCES `tcom_contrato` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
FOREIGN KEY (`cd_usuario_aprovacao`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idContrato`) REFERENCES `tcom_contrato` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
FOREIGN KEY (`idStatusHist`) REFERENCES `tcom_status_hist` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idViab`) REFERENCES `tcom_viab` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=4009

;

-- ----------------------------
-- Table structure for `tcom_viab_resp_hist`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_viab_resp_hist`;
CREATE TABLE `tcom_viab_resp_hist` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`idViabResp`  int(11) UNSIGNED NULL DEFAULT NULL ,
`observacao`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`anexo_label`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`anexo`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`idStatusHist`  int(11) UNSIGNED NULL DEFAULT NULL ,
`cd_usuario`  int(10) UNSIGNED NULL DEFAULT NULL ,
`data_cadastro`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cd_usuario`) REFERENCES `adminti`.`usuario` (`cd_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idStatusHist`) REFERENCES `tcom_status_hist` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`idViabResp`) REFERENCES `tcom_viab_resp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=267161

;

-- ----------------------------
-- Table structure for `tcom_viab_tipo`
-- ----------------------------
DROP TABLE IF EXISTS `tcom_viab_tipo`;
CREATE TABLE `tcom_viab_tipo` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`nome`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  enum('A','I') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'A' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=6

;

-- ----------------------------
-- Indexes structure for table tcom_analise_financ
-- ----------------------------
CREATE INDEX `fk_idContrato_tcom_analise_financ` ON `tcom_analise_financ`(`idContrato`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_tcom_analise_financ` ON `tcom_analise_financ`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_disparador_tcom_analise_financ` ON `tcom_analise_financ`(`cd_usuario_disparador`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_analise_financ`
-- ----------------------------
ALTER TABLE `tcom_analise_financ` AUTO_INCREMENT=4;

-- ----------------------------
-- Auto increment value for `tcom_aval`
-- ----------------------------
ALTER TABLE `tcom_aval` AUTO_INCREMENT=6;

-- ----------------------------
-- Indexes structure for table tcom_circuito
-- ----------------------------
CREATE INDEX `fk_idInterface_tcom_circuito` ON `tcom_circuito`(`idInterface`) USING BTREE ;
CREATE INDEX `fk_idTaxaDigital_tcom_circuito` ON `tcom_circuito`(`idTaxaDigital`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_circuito`
-- ----------------------------
ALTER TABLE `tcom_circuito` AUTO_INCREMENT=8184;

-- ----------------------------
-- Auto increment value for `tcom_cliente`
-- ----------------------------
ALTER TABLE `tcom_cliente` AUTO_INCREMENT=3005;

-- ----------------------------
-- Indexes structure for table tcom_cliente_end
-- ----------------------------
CREATE INDEX `fk_cd_estado_tcom_cliente_end` ON `tcom_cliente_end`(`cd_estado`) USING BTREE ;
CREATE INDEX `fk_idCliente_tcom_cliente_end` ON `tcom_cliente_end`(`idCliente`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_cliente_end`
-- ----------------------------
ALTER TABLE `tcom_cliente_end` AUTO_INCREMENT=27648;

-- ----------------------------
-- Indexes structure for table tcom_cliente_telefone
-- ----------------------------
CREATE INDEX `fk_idCliente_tcom_cliente_telefone` ON `tcom_cliente_telefone`(`idCliente`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_cliente_telefone`
-- ----------------------------
ALTER TABLE `tcom_cliente_telefone` AUTO_INCREMENT=98;

-- ----------------------------
-- Indexes structure for table tcom_contrato
-- ----------------------------
CREATE INDEX `fk_idOper_tcom_contrato` ON `tcom_contrato`(`idOper`) USING BTREE ;
CREATE INDEX `fk_idCliente_tcom_contrato` ON `tcom_contrato`(`idCliente`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_tcom_contrato` ON `tcom_contrato`(`cd_unidade`) USING BTREE ;
CREATE INDEX `fk_idCircuito_tcom_contrato` ON `tcom_contrato`(`idCircuito`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_contrato`
-- ----------------------------
ALTER TABLE `tcom_contrato` AUTO_INCREMENT=8187;

-- ----------------------------
-- Indexes structure for table tcom_contrato_anexo
-- ----------------------------
CREATE INDEX `fk_idContrato_tcom_contrato_anexo` ON `tcom_contrato_anexo`(`idContrato`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_tcom_contrato_anexo` ON `tcom_contrato_anexo`(`cd_usuario`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_contrato_anexo`
-- ----------------------------
ALTER TABLE `tcom_contrato_anexo` AUTO_INCREMENT=24;

-- ----------------------------
-- Indexes structure for table tcom_contrato_circuito
-- ----------------------------
CREATE INDEX `fk_idCircuito_tcom_contrato_circuito` ON `tcom_contrato_circuito`(`idCircuito`) USING BTREE ;
CREATE INDEX `fk_idContrato_tcom_contrato_circuito` ON `tcom_contrato_circuito`(`idContrato`) USING BTREE ;
CREATE INDEX `fk_idInterface_tcom_contrato_circuito` ON `tcom_contrato_circuito`(`idInterface`) USING BTREE ;
CREATE INDEX `fk_idTaxaDigital_tcom_contrato_circuito` ON `tcom_contrato_circuito`(`idTaxaDigital`) USING BTREE ;
CREATE INDEX `fk_idCliente_tcom_contrato_circuito` ON `tcom_contrato_circuito`(`idCliente`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_contrato_circuito`
-- ----------------------------
ALTER TABLE `tcom_contrato_circuito` AUTO_INCREMENT=6154;

-- ----------------------------
-- Indexes structure for table tcom_contrato_equip
-- ----------------------------
CREATE INDEX `fk_idContrato_tcom_contrato_equip` ON `tcom_contrato_equip`(`idContrato`) USING BTREE ;
CREATE INDEX `fk_idEquipModCod_tcom_contrato_equip` ON `tcom_contrato_equip`(`idEquipModCod`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_cadastro_tcom_contrato_equip` ON `tcom_contrato_equip`(`cd_usuario_cadastro`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_contrato_equip`
-- ----------------------------
ALTER TABLE `tcom_contrato_equip` AUTO_INCREMENT=34;

-- ----------------------------
-- Indexes structure for table tcom_contrato_valor
-- ----------------------------
CREATE INDEX `fk_idContrato_tcom_contrato_valor` ON `tcom_contrato_valor`(`idContrato`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_tcom_contrato_valor` ON `tcom_contrato_valor`(`cd_usuario`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_contrato_valor`
-- ----------------------------
ALTER TABLE `tcom_contrato_valor` AUTO_INCREMENT=6178;

-- ----------------------------
-- Indexes structure for table tcom_edificacao
-- ----------------------------
CREATE UNIQUE INDEX `idx_controle_tcom_controle` ON `tcom_edificacao`(`controle`) USING BTREE ;
CREATE INDEX `fk_idAval_tcom_edificacao` ON `tcom_edificacao`(`idAval`) USING BTREE ;
CREATE INDEX `fk_cd_estado_tcom_edificacao` ON `tcom_edificacao`(`cd_estado`) USING BTREE ;
CREATE INDEX `idx_contrato_tcom_edificacao` ON `tcom_edificacao`(`contrato`) USING BTREE ;
CREATE INDEX `fk_idNode_tcom_edificacao` ON `tcom_edificacao`(`idNode`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_edificacao`
-- ----------------------------
ALTER TABLE `tcom_edificacao` AUTO_INCREMENT=27863;

-- ----------------------------
-- Auto increment value for `tcom_equip_marca`
-- ----------------------------
ALTER TABLE `tcom_equip_marca` AUTO_INCREMENT=10;

-- ----------------------------
-- Indexes structure for table tcom_equip_modelo
-- ----------------------------
CREATE INDEX `fk_idEquipMarca_tcom_equip_modelo` ON `tcom_equip_modelo`(`idEquipMarca`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_equip_modelo`
-- ----------------------------
ALTER TABLE `tcom_equip_modelo` AUTO_INCREMENT=9;

-- ----------------------------
-- Indexes structure for table tcom_equip_modelo_codigo
-- ----------------------------
CREATE INDEX `fk_idEquipModelo_tcom_equip_mod_codigo` ON `tcom_equip_modelo_codigo`(`idEquipModelo`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_equip_modelo_codigo`
-- ----------------------------
ALTER TABLE `tcom_equip_modelo_codigo` AUTO_INCREMENT=40;

-- ----------------------------
-- Auto increment value for `tcom_interface`
-- ----------------------------
ALTER TABLE `tcom_interface` AUTO_INCREMENT=28;

-- ----------------------------
-- Auto increment value for `tcom_log_contrato`
-- ----------------------------
ALTER TABLE `tcom_log_contrato` AUTO_INCREMENT=3576;

-- ----------------------------
-- Indexes structure for table tcom_node
-- ----------------------------
CREATE UNIQUE INDEX `descricao` ON `tcom_node`(`descricao`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_node` ON `tcom_node`(`cd_unidade`) USING BTREE ;
CREATE INDEX `fk_cd_estado_node` ON `tcom_node`(`cd_estado`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_node`
-- ----------------------------
ALTER TABLE `tcom_node` AUTO_INCREMENT=3435;

-- ----------------------------
-- Indexes structure for table tcom_nodeCelula
-- ----------------------------
CREATE INDEX `fk_idNode_nodeCelula` ON `tcom_nodeCelula`(`idNode`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_nodeCelula`
-- ----------------------------
ALTER TABLE `tcom_nodeCelula` AUTO_INCREMENT=3437;

-- ----------------------------
-- Indexes structure for table tcom_oper
-- ----------------------------
CREATE INDEX `fk_cd_unidade_tcom_per` ON `tcom_oper`(`cd_unidade`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_oper`
-- ----------------------------
ALTER TABLE `tcom_oper` AUTO_INCREMENT=588;

-- ----------------------------
-- Indexes structure for table tcom_oper_cobr
-- ----------------------------
CREATE INDEX `fk_idOper_tcom_oper_cobr` ON `tcom_oper_cobr`(`idOper`) USING BTREE ;
CREATE INDEX `fk_cd_estado_tcom_oper_cobr` ON `tcom_oper_cobr`(`cd_estado`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_oper_cobr`
-- ----------------------------
ALTER TABLE `tcom_oper_cobr` AUTO_INCREMENT=3596;

-- ----------------------------
-- Indexes structure for table tcom_oper_cobr_telefone
-- ----------------------------
CREATE INDEX `fk_idOperCobr_tcom_oper_cobr_telefone` ON `tcom_oper_cobr_telefone`(`idOperCobr`) USING BTREE ;
CREATE INDEX `fk_idOper_tcom_oper_cobr_telefone` ON `tcom_oper_cobr_telefone`(`idOper`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_oper_cobr_telefone`
-- ----------------------------
ALTER TABLE `tcom_oper_cobr_telefone` AUTO_INCREMENT=75;

-- ----------------------------
-- Indexes structure for table tcom_oper_inst
-- ----------------------------
CREATE INDEX `fk_cd_estado_tcom_oper_inst` ON `tcom_oper_inst`(`cd_estado`) USING BTREE ;
CREATE INDEX `fk_idOper_tcom_oper_inst` ON `tcom_oper_inst`(`idOper`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_oper_inst`
-- ----------------------------
ALTER TABLE `tcom_oper_inst` AUTO_INCREMENT=3644;

-- ----------------------------
-- Indexes structure for table tcom_status_hist
-- ----------------------------
CREATE INDEX `fk_idEmailEnvia_tcom_status_hist` ON `tcom_status_hist`(`idEmailEnvia`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_status_hist`
-- ----------------------------
ALTER TABLE `tcom_status_hist` AUTO_INCREMENT=77;

-- ----------------------------
-- Auto increment value for `tcom_taxa_digital`
-- ----------------------------
ALTER TABLE `tcom_taxa_digital` AUTO_INCREMENT=129;

-- ----------------------------
-- Indexes structure for table tcom_viab
-- ----------------------------
CREATE INDEX `fk_idViabTipo_tcom_viab` ON `tcom_viab`(`idViabTipo`) USING BTREE ;
CREATE INDEX `fk_cd_unidade_tcom_viab` ON `tcom_viab`(`cd_unidade`) USING BTREE ;
CREATE INDEX `fk_idOper_tcom_viab` ON `tcom_viab`(`idOper`) USING BTREE ;
CREATE INDEX `fk_idCliente_tcom_viab` ON `tcom_viab`(`idCliente`) USING BTREE ;
CREATE INDEX `fk_idContrato_tcom_viab` ON `tcom_viab`(`idContrato`) USING BTREE ;
CREATE INDEX `fk_idInterface_tcom_viab` ON `tcom_viab`(`idInterface`) USING BTREE ;
CREATE INDEX `fk_idTaxaDigital_tcom_viab` ON `tcom_viab`(`idTaxaDigital`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_viab`
-- ----------------------------
ALTER TABLE `tcom_viab` AUTO_INCREMENT=3912;

-- ----------------------------
-- Indexes structure for table tcom_viab_md_end
-- ----------------------------
CREATE INDEX `fk_cd_estado_tcom_viab_md_end` ON `tcom_viab_md_end`(`cd_estado`) USING BTREE ;
CREATE INDEX `fk_idViab_tcom_viab_md_end` ON `tcom_viab_md_end`(`idViab`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_viab_md_end`
-- ----------------------------
ALTER TABLE `tcom_viab_md_end` AUTO_INCREMENT=10;

-- ----------------------------
-- Indexes structure for table tcom_viab_md_end_tel
-- ----------------------------
CREATE INDEX `fk_idCliente_tcom_viab_md_end_tel` ON `tcom_viab_md_end_tel`(`idViabMdEnd`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_viab_md_end_tel`
-- ----------------------------
ALTER TABLE `tcom_viab_md_end_tel` AUTO_INCREMENT=24;

-- ----------------------------
-- Indexes structure for table tcom_viab_pend
-- ----------------------------
CREATE INDEX `fk_idViab_tcom_viab_pend` ON `tcom_viab_pend`(`idViab`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_pergunta_tcom_viab_pend` ON `tcom_viab_pend`(`cd_usuario_pergunta`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_resposta_tcom_viab_pend` ON `tcom_viab_pend`(`cd_usuario_resposta`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_viab_pend`
-- ----------------------------
ALTER TABLE `tcom_viab_pend` AUTO_INCREMENT=70;

-- ----------------------------
-- Indexes structure for table tcom_viab_resp
-- ----------------------------
CREATE INDEX `fk_idNode_tcom_viab_resp` ON `tcom_viab_resp`(`node_distancia`) USING BTREE ;
CREATE INDEX `fk_idViab_tcom_viab_resp` ON `tcom_viab_resp`(`idViab`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_tcom_viab_resp` ON `tcom_viab_resp`(`cd_usuario`) USING BTREE ;
CREATE INDEX `fk_idContrato_tcom_viab_res` ON `tcom_viab_resp`(`idContrato`) USING BTREE ;
CREATE INDEX `fk_idStatusHist_tcom_viab_resp` ON `tcom_viab_resp`(`idStatusHist`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_aprovacao_tcom_viab_resp` ON `tcom_viab_resp`(`cd_usuario_aprovacao`) USING BTREE ;
CREATE INDEX `fk_idContratoAtual_tcom_viab_resp` ON `tcom_viab_resp`(`idContratoAtual`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_viab_resp`
-- ----------------------------
ALTER TABLE `tcom_viab_resp` AUTO_INCREMENT=4009;

-- ----------------------------
-- Indexes structure for table tcom_viab_resp_hist
-- ----------------------------
CREATE INDEX `fk_idViabResp_tcom_viab_resp_hist` ON `tcom_viab_resp_hist`(`idViabResp`) USING BTREE ;
CREATE INDEX `fk_idStatusHist_tcom_viab_resp_hist` ON `tcom_viab_resp_hist`(`idStatusHist`) USING BTREE ;
CREATE INDEX `fk_cd_usuario_tcom_viab_resp_hist` ON `tcom_viab_resp_hist`(`cd_usuario`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `tcom_viab_resp_hist`
-- ----------------------------
ALTER TABLE `tcom_viab_resp_hist` AUTO_INCREMENT=267161;

-- ----------------------------
-- Auto increment value for `tcom_viab_tipo`
-- ----------------------------
ALTER TABLE `tcom_viab_tipo` AUTO_INCREMENT=6;
