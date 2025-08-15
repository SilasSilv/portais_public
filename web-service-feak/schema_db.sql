-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: refsoft
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.13-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE refsoft;

USE refsoft;

--
-- Table structure for table `associado`
--

DROP TABLE IF EXISTS `associado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `associado` (
  `num_associado` varchar(25) NOT NULL,
  `nome_associado` varchar(120) DEFAULT NULL,
  `num_cpf` varchar(16) DEFAULT NULL,
  `nome_mae` varchar(120) NOT NULL,
  `ie_alterado` char(1) DEFAULT NULL,
  `dt_nascimento` date DEFAULT NULL,
  `dt_atualizacao` date DEFAULT NULL,
  `num_fone` varchar(20) DEFAULT NULL,
  `cod_prestador` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`num_associado`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargo` (
  `cd_cargo` int(11) NOT NULL,
  `ds_cargo` varchar(1000) NOT NULL,
  PRIMARY KEY (`cd_cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `confirma_aviso`
--

DROP TABLE IF EXISTS `confirma_aviso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confirma_aviso` (
  `nr_cracha` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dominio`
--

DROP TABLE IF EXISTS `dominio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dominio` (
  `CD_DOMINIO` int(11) NOT NULL AUTO_INCREMENT,
  `DS_DOMINIO` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`CD_DOMINIO`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `informativo`
--

DROP TABLE IF EXISTS `informativo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `informativo` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `ds_titulo` varchar(250) NOT NULL,
  `ds_descricao` varchar(2400) NOT NULL DEFAULT '',
  `ie_situacao` char(1) NOT NULL,
  `nr_cracha` int(11) NOT NULL,
  `dt_inclusao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nr_sequencia`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mensagem`
--

DROP TABLE IF EXISTS `mensagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensagem` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `nr_cracha` decimal(10,0) DEFAULT NULL,
  `dt_mensagem` date DEFAULT NULL,
  `ie_classificacao` char(1) NOT NULL,
  `ds_mensagem` varchar(2000) NOT NULL,
  `ie_anonimo` char(1) DEFAULT NULL,
  `ie_situacao` char(1) DEFAULT NULL,
  `ie_parecer` char(1) DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mensagem_resposta`
--

DROP TABLE IF EXISTS `mensagem_resposta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensagem_resposta` (
  `nr_sequencia` int(11) NOT NULL,
  `nr_seq_mensagem` int(11) DEFAULT NULL,
  `dt_resposta_rh` date DEFAULT NULL,
  `dt_resposta_mkt` date DEFAULT NULL,
  `ds_reposta_rh` varchar(2000) DEFAULT NULL,
  `ds_resposta_mkt` varchar(2000) DEFAULT NULL,
  `ie_lido_colaborador` char(1) DEFAULT NULL,
  `nr_cracha_rh` int(11) DEFAULT NULL,
  `nr_cracha_mkt` int(11) DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `FK_mensagem_resposta` (`nr_seq_mensagem`),
  CONSTRAINT `FK_mensagem_resposta` FOREIGN KEY (`nr_seq_mensagem`) REFERENCES `mensagem` (`nr_sequencia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setores`
--

DROP TABLE IF EXISTS `setores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setores` (
  `CD_SETOR` int(11) NOT NULL AUTO_INCREMENT,
  `DS_SETOR` varchar(100) DEFAULT NULL,
  `DT_INCLUSAO` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CD_SETOR`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_grupo`
--

DROP TABLE IF EXISTS `tb_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_grupo` (
  `cd_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `nm_grupo` varchar(50) NOT NULL,
  PRIMARY KEY (`cd_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


-- Table structure for table `pessoa_fisica`
--

DROP TABLE IF EXISTS `pessoa_fisica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pessoa_fisica` (
  `nr_cracha` int(50) NOT NULL COMMENT 'Nº Do Cracha',
  `nm_pessoa_fisica` varchar(200) NOT NULL COMMENT 'Nome da Pessoa',
  `ds_mail` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT 'E-mail pessoal',
  `ds_senha` varchar(32) DEFAULT NULL,
  `dt_inclusao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cd_setor` int(11) NOT NULL,
  `cd_pessoa_fisica` int(11) DEFAULT NULL,
  `cd_cargo` int(11) DEFAULT NULL,
  `ie_situacao` char(1) DEFAULT NULL,
  `dt_demissao` date DEFAULT NULL,
  `cd_grupo` int(11) DEFAULT NULL,
  `ds_login_alternativo` varchar(70) DEFAULT NULL,
  `url_foto_perfil` varchar(100) DEFAULT 'img/people/default.jpg',
  `ie_alterar_senha` CHAR(1) DEFAULT NULL,
  PRIMARY KEY (`nr_cracha`),
  KEY `FK_pessoa_fisica` (`cd_setor`),
  KEY `FK_pessoa_fisica_cargo` (`cd_cargo`),
  KEY `fk_pf_cd_grupo` (`cd_grupo`),
  CONSTRAINT `FK_pessoa_fisica` FOREIGN KEY (`cd_setor`) REFERENCES `setores` (`CD_SETOR`) ON DELETE NO ACTION,
  CONSTRAINT `FK_pessoa_fisica_cargo` FOREIGN KEY (`cd_cargo`) REFERENCES `cargo` (`cd_cargo`) ON DELETE NO ACTION,
  CONSTRAINT `fk_pf_cd_grupo` FOREIGN KEY (`cd_grupo`) REFERENCES `tb_grupo` (`cd_grupo`),
  CONSTRAINT `uc_ds_login_alternativo` UNIQUE (`ds_login_alternativo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `tb_tipo_categoria`
--

DROP TABLE IF EXISTS `tb_tipo_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_tipo_categoria` (
  `cd_tipo_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `ds_tipo_categoria` varchar(256) NOT NULL,
  PRIMARY KEY (`cd_tipo_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `tb_categoria`
--

DROP TABLE IF EXISTS `tb_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_categoria` (
  `cd_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `cd_tipo_categoria` int(11) DEFAULT NULL,
  `ds_categoria` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cd_categoria`),
  KEY `fk_cd_tipo_categoria` (`cd_tipo_categoria`),
  CONSTRAINT `fk_cd_tipo_categoria` FOREIGN KEY (`cd_tipo_categoria`) REFERENCES `tb_tipo_categoria` (`cd_tipo_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `ocorrencia`
--

DROP TABLE IF EXISTS `ocorrencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ocorrencia` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `nr_cracha` int(11) NOT NULL,
  `cd_tipo_ocorrencia` int(11) DEFAULT NULL,
  `dt_ocorrencia` date NOT NULL,
  `ds_qt_horas_dias` varchar(10) DEFAULT NULL,
  `ds_justificativa` varchar(1000) NOT NULL,
  `cd_tipo_parecer` int(11) DEFAULT NULL,
  `ds_observacao` varchar(1000) DEFAULT NULL,
  `dt_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nr_cracha_inclusao` int(11) DEFAULT NULL,
  `ie_lido_rh` char(1) DEFAULT NULL,
  `ds_parecer_rh` varchar(1000) DEFAULT NULL,
  `dt_parecer_rh` date DEFAULT NULL,
  `nr_cracha_rh` int(11) DEFAULT NULL,
  `dt_criacao` timestamp NULL DEFAULT NULL,
  `cd_tipo_horas_dias` int(1) DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `FK_cracha_col` (`nr_cracha`),
  KEY `fk_cd_tipo_ocorrencia` (`cd_tipo_ocorrencia`),
  KEY `fk_cd_tipo_horas_dias` (`cd_tipo_horas_dias`),
  KEY `fk_cd_tipo_parecer` (`cd_tipo_parecer`),
  CONSTRAINT `FK_cracha_col` FOREIGN KEY (`nr_cracha`) REFERENCES `pessoa_fisica` (`nr_cracha`),
  CONSTRAINT `fk_cd_tipo_horas_dias` FOREIGN KEY (`cd_tipo_horas_dias`) REFERENCES `tb_categoria` (`cd_categoria`),
  CONSTRAINT `fk_cd_tipo_ocorrencia` FOREIGN KEY (`cd_tipo_ocorrencia`) REFERENCES `tb_categoria` (`cd_categoria`),
  CONSTRAINT `fk_cd_tipo_parecer` FOREIGN KEY (`cd_tipo_parecer`) REFERENCES `tb_categoria` (`cd_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=17020 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ocorrencia_acidente`
--

DROP TABLE IF EXISTS `ocorrencia_acidente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ocorrencia_acidente` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `nr_cracha` int(11) NOT NULL,
  `dt_ocorrencia` date NOT NULL,
  `nr_horas` varchar(1000) NOT NULL,
  `ds_acidente` varchar(1000) NOT NULL,
  `cd_parte_corpo` int(11) NOT NULL,
  `ie_situacao_epi` char(1) NOT NULL,
  `ds_epi` varchar(1000) DEFAULT NULL,
  `ds_acidente_evitado` varchar(1000) DEFAULT NULL,
  `ie_situacao_comunicado` char(1) NOT NULL,
  `cd_motivo_acidente` int(11) DEFAULT NULL,
  `ds_outros` varchar(1000) DEFAULT NULL,
  `ie_situacao_testemunha` char(1) NOT NULL,
  `ds_testemunha` varchar(1000) DEFAULT NULL,
  `ie_lido_cipa` char(1) DEFAULT NULL,
  `ds_parecer_cipa` varchar(1000) DEFAULT NULL,
  `dt_parecer_cipa` date DEFAULT NULL,
  `nr_cracha_cipa` int(11) DEFAULT NULL,
  `dt_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nr_cracha_inclusao` int(11) DEFAULT NULL,
  `ds_local` varchar(1000) NOT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `fk_cd_motivo_acidente` (`cd_motivo_acidente`),
  KEY `fk_cd_parte_corpo` (`cd_parte_corpo`)
) ENGINE=MyISAM AUTO_INCREMENT=269 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opine`
--

DROP TABLE IF EXISTS `opine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opine` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `dt_inclusao` datetime DEFAULT NULL,
  `ds_opiniao` varchar(500) NOT NULL,
  `cd_tipo_opiniao` int(11) DEFAULT NULL,
  `nr_cracha` int(11) DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `fk_opine_cd_tipo_opiniao` (`cd_tipo_opiniao`),
  KEY `fk_opine_nr_cracha` (`nr_cracha`),
  CONSTRAINT `fk_opine_cd_tipo_opiniao` FOREIGN KEY (`cd_tipo_opiniao`) REFERENCES `tb_categoria` (`cd_categoria`),
  CONSTRAINT `fk_opine_nr_cracha` FOREIGN KEY (`nr_cracha`) REFERENCES `pessoa_fisica` (`nr_cracha`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parte_corpo`
--

DROP TABLE IF EXISTS `parte_corpo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parte_corpo` (
  `cd_pr_corpo` int(11) NOT NULL,
  `ds_pr_corpo` varchar(1000) NOT NULL,
  PRIMARY KEY (`cd_pr_corpo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qualidade`
--

DROP TABLE IF EXISTS `qualidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qualidade` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `cd_apresentacao` int(11) NOT NULL,
  `cd_temperatura` int(11) NOT NULL,
  `cd_sabor` int(11) NOT NULL,
  `dt_inclusao` datetime DEFAULT NULL,
  `nr_cracha` int(11) NOT NULL,
  `cd_simpatia` int(11) NOT NULL,
  `cd_higiene_loc` int(11) NOT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `fk_qualidade_cd_apresentacao` (`cd_apresentacao`),
  KEY `fk_qualidade_cd_temperatura` (`cd_temperatura`),
  KEY `fk_qualidade_cd_sabor` (`cd_sabor`),
  KEY `fk_qualidade_cd_simpatia` (`cd_simpatia`),
  KEY `fk_qualidade_cd_higiene_loc` (`cd_higiene_loc`),
  CONSTRAINT `fk_qualidade_cd_apresentacao` FOREIGN KEY (`cd_apresentacao`) REFERENCES `tb_categoria` (`cd_categoria`),
  CONSTRAINT `fk_qualidade_cd_higiene_loc` FOREIGN KEY (`cd_higiene_loc`) REFERENCES `tb_categoria` (`cd_categoria`),
  CONSTRAINT `fk_qualidade_cd_sabor` FOREIGN KEY (`cd_sabor`) REFERENCES `tb_categoria` (`cd_categoria`),
  CONSTRAINT `fk_qualidade_cd_simpatia` FOREIGN KEY (`cd_simpatia`) REFERENCES `tb_categoria` (`cd_categoria`),
  CONSTRAINT `fk_qualidade_cd_temperatura` FOREIGN KEY (`cd_temperatura`) REFERENCES `tb_categoria` (`cd_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=2787 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refeicao`
--

DROP TABLE IF EXISTS `refeicao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refeicao` (
  `nr_refeicao` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Chave Primária',
  `ie_tipo_refeicao` char(1) CHARACTER SET utf8 NOT NULL COMMENT 'Tipo da Refeição - Almoço ou Jantar',
  `dt_refeicao` date NOT NULL COMMENT 'Data da Refeição',
  `ds_refeicao` varchar(1000) NOT NULL,
  `dt_inicio` datetime NOT NULL COMMENT 'Data inicial da solicitação',
  `dt_final` datetime NOT NULL COMMENT 'Data final da solicitação',
  `nr_cracha` int(11) DEFAULT NULL,
  `dt_atualizacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ie_situacao` char(1) DEFAULT NULL,
  `ie_feriado` CHAR(1) DEFAULT 'N',
  PRIMARY KEY (`nr_refeicao`)
) ENGINE=InnoDB AUTO_INCREMENT=914 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refeicao_pedidos`
--

DROP TABLE IF EXISTS `refeicao_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refeicao_pedidos` (
  `nr_refeicao` int(11) NOT NULL COMMENT 'Chave extrangeira',
  `nr_cracha` int(11) NOT NULL COMMENT 'Chave extrangeira',
  `dt_atualizacao` datetime DEFAULT NULL,
  `ds_ref_alt` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`nr_refeicao`,`nr_cracha`),
  CONSTRAINT `FK_refeicao_pedidos` FOREIGN KEY (`nr_refeicao`) REFERENCES `refeicao` (`nr_refeicao`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_cartao`
--

DROP TABLE IF EXISTS `tb_cartao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_cartao` (
  `nr_cartao` int(11) NOT NULL,
  `nr_cracha` int(11) DEFAULT NULL,
  `cd_setor` int(11) DEFAULT NULL,
  `ie_passe_livre_catraca` char(1) DEFAULT NULL,
  `ie_situacao` char(1) DEFAULT NULL,
  PRIMARY KEY (`nr_cartao`),
  KEY `fk_tb_cartao_nr_cracha` (`nr_cracha`),
  KEY `fk_tb_cartao_cd_setor` (`cd_setor`),
  CONSTRAINT `fk_tb_cartao_cd_setor` FOREIGN KEY (`cd_setor`) REFERENCES `setores` (`CD_SETOR`),
  CONSTRAINT `fk_tb_cartao_nr_cracha` FOREIGN KEY (`nr_cracha`) REFERENCES `pessoa_fisica` (`nr_cracha`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `refeicao_terceiros`
--

DROP TABLE IF EXISTS `refeicao_terceiros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refeicao_terceiros` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `ie_terceiro_dobra` char(1) DEFAULT NULL,
  `dt_refeicao` date NOT NULL,
  `ie_tipo_refeicao` char(1) NOT NULL,
  `nm_pessoa_cartao` varchar(100) DEFAULT NULL,
  `nr_cracha_resp` int(11) NOT NULL,
  `nr_cartao` int(11) DEFAULT NULL,
  `nr_cracha` int(11) DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `fk_refeicao_terceiros_nr_cracha_resp` (`nr_cracha_resp`),
  KEY `fk_refeicao_terceiros_nr_cracha` (`nr_cracha`),
  KEY `fk_refeicao_terceiros_nr_cartao` (`nr_cartao`),
  CONSTRAINT `fk_refeicao_terceiros_nr_cartao` FOREIGN KEY (`nr_cartao`) REFERENCES `tb_cartao` (`nr_cartao`),
  CONSTRAINT `fk_refeicao_terceiros_nr_cracha` FOREIGN KEY (`nr_cracha`) REFERENCES `pessoa_fisica` (`nr_cracha`),
  CONSTRAINT `fk_refeicao_terceiros_nr_cracha_resp` FOREIGN KEY (`nr_cracha_resp`) REFERENCES `pessoa_fisica` (`nr_cracha`)
) ENGINE=InnoDB AUTO_INCREMENT=993 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `sorteio`
--

DROP TABLE IF EXISTS `sorteio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sorteio` (
  `nr_cracha` int(11) NOT NULL,
  `ie_sorteado` char(1) DEFAULT 'N',
  PRIMARY KEY (`nr_cracha`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_agenda_telefonica`
--

DROP TABLE IF EXISTS `tb_agenda_telefonica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_agenda_telefonica` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `ds_contato` varchar(100) DEFAULT NULL,
  `nr_ddd` decimal(2,0) DEFAULT NULL,
  `nr_telefone` varchar(10) DEFAULT NULL,
  `ds_observacao` varchar(256) DEFAULT NULL,
  `cd_tipo_contato` int(11) DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `fk_tb_agenda_telef_cd_tipo_contato` (`cd_tipo_contato`),
  CONSTRAINT `fk_tb_agenda_telef_cd_tipo_contato` FOREIGN KEY (`cd_tipo_contato`) REFERENCES `tb_categoria` (`cd_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_sistema`
--

DROP TABLE IF EXISTS `tb_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_sistema` (
  `cd_sistema` int(11) NOT NULL AUTO_INCREMENT,
  `nm_sistema` VARCHAR(256) NOT NULL,
  `ds_sistema` varchar(256) NOT NULL,
  `cd_token` char(36) NOT NULL,
  `ie_situacao` char(1) NOT NULL,
  `img_logo` VARCHAR(200),
  PRIMARY KEY (`cd_sistema`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_permissao`
--

DROP TABLE IF EXISTS `tb_permissao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_permissao` (
  `cd_permissao` int(11) NOT NULL AUTO_INCREMENT,
  `cd_sistema` int(11) DEFAULT NULL,
  `cd_tipo_permissao` int(11) NOT NULL,
  `ds_titulo` varchar(100) NOT NULL,
  `ds_descricao` text NOT NULL,
  `vl_padrao` varchar(15) NOT NULL,
  `ie_mostrar_cliente` char(1) NOT NULL,
  `ds_descricao_cliente` varchar(100) DEFAULT NULL,
  `ie_mostrar_parametro` char(1) NOT NULL,
  `ie_situacao` char(1) NOT NULL,
  PRIMARY KEY (`cd_permissao`),
  KEY `fk_tb_permissao_cd_tipo_permissao` (`cd_tipo_permissao`),
  KEY `fk_tb_permissao_cd_sistema` (`cd_sistema`),
  CONSTRAINT `fk_tb_permissao_cd_sistema` FOREIGN KEY (`cd_sistema`) REFERENCES `tb_sistema` (`cd_sistema`),
  CONSTRAINT `fk_tb_permissao_cd_tipo_permissao` FOREIGN KEY (`cd_tipo_permissao`) REFERENCES `tb_categoria` (`cd_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_permissao_pf`
--

DROP TABLE IF EXISTS `tb_permissao_pf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_permissao_pf` (
  `cd_permissao` int(11) NOT NULL,
  `nr_cracha` int(11) NOT NULL,
  `vl_pf` varchar(15) NOT NULL,
  PRIMARY KEY (`cd_permissao`,`nr_cracha`),
  KEY `fk_tb_permissao_pf_nr_cracha` (`nr_cracha`),
  CONSTRAINT `fk_tb_permissao_pf_cd_permissao` FOREIGN KEY (`cd_permissao`) REFERENCES `tb_permissao` (`cd_permissao`),
  CONSTRAINT `fk_tb_permissao_pf_nr_cracha` FOREIGN KEY (`nr_cracha`) REFERENCES `pessoa_fisica` (`nr_cracha`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_permissao_setor`
--

DROP TABLE IF EXISTS `tb_permissao_setor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_permissao_setor` (
  `cd_permissao` int(11) NOT NULL,
  `cd_setor` int(11) NOT NULL,
  `vl_setor` varchar(15) NOT NULL,
  PRIMARY KEY (`cd_permissao`,`cd_setor`),
  KEY `fk_tb_permissao_setor_cd_setor` (`cd_setor`),
  CONSTRAINT `fk_tb_permissao_setor_cd_permissao` FOREIGN KEY (`cd_permissao`) REFERENCES `tb_permissao` (`cd_permissao`),
  CONSTRAINT `fk_tb_permissao_setor_cd_setor` FOREIGN KEY (`cd_setor`) REFERENCES `setores` (`CD_SETOR`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_sessao`
--

DROP TABLE IF EXISTS `tb_sessao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_sessao` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) DEFAULT NULL,
  `refresh_token` varchar(100) DEFAULT NULL,
  `cd_sistema` int(11) DEFAULT NULL,
  `nr_cracha` int(11) DEFAULT NULL,
  `hostname` varchar(70) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `navegador` varchar(150) DEFAULT NULL,
  `expire` datetime DEFAULT NULL,
  `expire_unix` bigint(20) DEFAULT NULL,
  `dt_inicio` datetime DEFAULT NULL,
  `dt_fim` datetime DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`),
  KEY `fk_tb_sessao_cd_sistema` (`cd_sistema`),
  CONSTRAINT `fk_tb_sessao_cd_sistema` FOREIGN KEY (`cd_sistema`) REFERENCES `tb_sistema` (`cd_sistema`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tb_temp`
--

DROP TABLE IF EXISTS `tb_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_temp` (
  `nr_refeicao` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `valor_dominio`
--

DROP TABLE IF EXISTS `valor_dominio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `valor_dominio` (
  `cd_dominio` int(11) DEFAULT NULL,
  `vl_dominio` char(1) DEFAULT NULL,
  `ds_valor` varchar(200) DEFAULT NULL,
  KEY `FK_valor_dominio` (`cd_dominio`),
  CONSTRAINT `FK_valor_dominio` FOREIGN KEY (`cd_dominio`) REFERENCES `dominio` (`CD_DOMINIO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `valor_refeicao`
--

DROP TABLE IF EXISTS `valor_refeicao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `valor_refeicao` (
  `nr_sequencia` int(11) NOT NULL AUTO_INCREMENT,
  `dt_vigencia_inicial` date DEFAULT NULL,
  `dt_vigencia_final` date DEFAULT NULL,
  `vl_refeicao` decimal(10,2) DEFAULT NULL,
  `ie_situacao` char(1) DEFAULT NULL,
  PRIMARY KEY (`nr_sequencia`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-04 10:29:10

CREATE TABLE tb_variavel_global (
	nr_sequencia INTEGER AUTO_INCREMENT,
	nm_variavel VARCHAR(256),
	vl_variavel VARCHAR(256),
	CONSTRAINT pk_tb_variavel_global_nr_sequencia
		PRIMARY KEY(nr_sequencia)
);

CREATE TABLE tb_lido_opine (
	nr_cracha INTEGER,
	nr_seq_opine INTEGER,
	ie_lido CHAR(1),
	CONSTRAINT fk_tb_lido_opine__nr_cracha 
		FOREIGN KEY(nr_cracha) REFERENCES pessoa_fisica(nr_cracha),
	CONSTRAINT fk_tb_lido_opine__nr_seq_opine
		FOREIGN KEY(nr_seq_opine) REFERENCES opine(nr_sequencia)
);

/* Insert na tabelas*/

INSERT INTO tb_tipo_categoria
(cd_tipo_categoria,
 ds_tipo_categoria)
VALUES
(1, 'Tipo de ocorrência de ponto'),
(2, 'Tipo paracer ocorrência de ponto'),
(3, 'Categoria dia ou horas'),
(4, 'Parte de Corpo'),
(5, 'Motivo de Acidente'),
(6, 'Tipo de opinião'),
(7, 'Qualidade do Refeitório'),
(8, 'Tipos de Permissão'),
(9, 'Categoria de telefones');

INSERT INTO tb_categoria
(cd_categoria, 
 cd_tipo_categoria, 
 ds_categoria)
VALUES
(1, 1, 'Atraso/Saída Antecipada'),
(2, 1, 'Falta'),
(3, 1, 'Troca de Horário'),
(4, 1, 'Troca de Folga'),
(5, 1, 'Prorrogação de Horário/Dobra'),
(6, 1, 'Banco de Horas'),
(7, 1, 'Ausência de Marcação'),
(8, 2, 'Hora Extra'),
(9, 2, 'Ausência de Parecer'),
(10, 2, 'Banco de Horas'),
(11, 3, 'Dia'),
(12, 3, 'Hora'),
(13, 4, 'Cabeça'),
(14, 4, 'Testa'),
(15, 4, 'Olho'),
(16, 4, 'Orelha'),
(17, 4, 'Nariz'),
(18, 4, 'Boca'),
(19, 4, 'Língua'),
(20, 4, 'Dente'),
(21, 4, 'Mandíbula'),
(22, 4, 'Bochecha'),
(23, 4, 'Queixo'),
(24, 4, 'Pescoço'),
(25, 4, 'Garganta'),
(26, 4, 'Pomo-de-adão'),
(27, 4, 'Ombros'),
(28, 4, 'Braço'),
(29, 4, 'Cotovelo'),
(30, 4, 'Pulso'),
(31, 4, 'Mão'),
(32, 4, 'Dedos da mão'),
(33, 4, 'Polegar'),
(34, 4, 'Coluna'),
(35, 4, 'Peito'),
(36, 4, 'Mama'),
(37, 4, 'Costela'),
(38, 4, 'Abdome'),
(39, 4, 'Umbigo'),
(40, 4, 'Órgão sexual'),
(41, 4, 'Quadril'),
(42, 4, 'Nádegas'),
(43, 4, 'Coxa'),
(44, 4, 'Joelho'),
(45, 4, 'Perna'),
(46, 4, 'Panturrilha'),
(47, 4, 'Calcanhar'),
(48, 4, 'Tornozelo'),
(49, 4, 'Pé'),
(50, 4, 'Dedos do pé'),
(51, 5, 'Falta de treinamento'),
(52, 5, 'Não conhecimento do processo'),
(53, 5, 'Procedimento inadequado'),
(54, 5, 'Inexperiência'),
(55, 5, 'Condição insegura'),
(56, 5, 'Ato inseguro'),
(57, 5, 'Falta de atenção'),
(58, 5, 'Layout inadequado'),
(59, 5, 'Outros'),
(60, 6, 'Sugestão'),
(61, 6, 'Dúvida'),
(62, 6, 'Reclamação'),
(63, 6, 'Elogio'),
(64, 7, 'Ótimo'),
(65, 7, 'Bom'),
(66, 7, 'Regular'),
(67, 7, 'Ruim'),
(68, 8, 'Acesso'),
(69, 8, 'Sistema'),
(70, 8, 'Catraca'),
(71, 8, 'Consulta - Relatório'),
(72, 9, 'Estabelecimento'),
(73, 9, 'Funcionário'),
(74, 9, 'Ramal'),
(75, 9, 'Serviço'),
(76, 3, 'Dia e Hora');

INSERT INTO tb_sistema
(cd_sistema,
 nm_sistema,
 ds_sistema,
 ie_situacao,
 cd_token,
 img_logo)
VALUES
(1, 'Agenda Telefônica', 'O sistema vai gerenciar os números de telefone do hospital, com foco na utilização da telefonista', 'A','ab29deba-0179-45ad-8b87-86659d0e983e', 'img/system/cf6d083903a8b3a9d6af5a5871fd88d2.png'),
(2, 'Ocorrência de Ponto', 'Gerenciar qualquer divergência no ponto com os funcionários', 'A','92a014b9-b82a-4681-8b7c-b78eacb6f6c6', 'img/system/6b54e371a3c58e2cbb991969510aabfb.png'),
(3, 'Ocorrência de Acidente', 'O sistema irá tratar as informações referente a acidente', 'A','41cd7b9b-ba4e-4734-8def-bce441651971', 'img/system/c428b637dfa925d0eb4f424b5df0b222.png'),
(4, 'Portal de Refeição', 'Controlar as solicitações e gerenciamento das refeições', 'A','4b348ba0-ccb1-4ae1-89a4-6b49effa8249', 'img/system/d75be8f6331f9f3d583dbbab2788fd23.png'),
(5, 'Controle de Pessoas', 'Controlar os acessos aos portais e cadastrar os usuários', 'A','356b9ae5-89cc-4dcc-b018-ef682a2109c1', 'img/system/fa0677ba0f227634f6e3d71ff891747b.png'),
(6, 'Administrador de Sistema', 'Gerenciar todos os sistemas auxiliares e cadastros de categorias', 'A','34b1477e-a6f7-4558-9729-1136306285bb', 'img/system/60330019044bcb96d5ee31098d619f48.png'),
(7, 'Portal CIPA', 'Controlar as pautas das reuniões e facilitar o gerenciamento da CIPA', 'A', 'e411477a-a6f7-4558-5986-1136306285ee', NULL);

INSERT INTO tb_permissao
(cd_permissao, 
 cd_sistema,
 cd_tipo_permissao,
 ds_titulo,
 ds_descricao,
 vl_padrao,
 ie_mostrar_cliente,
 ds_descricao_cliente,
 ie_mostrar_parametro,
 ie_situacao)
VALUES
(1, 1, 68, 'Acessar Agenda telefônica', 
 'Esta permissão permite se logar no sistema
  Agenda telefônica e usar os recursos restritos 
  deste sistema.', 'N', 'N', NULL, 'S', 'A'),
(2, 2, 68, 'Acessar Ocorrência de ponto', 
 'Esta permissão permite se logar no sistema
  Ocorrência de ponto e usar os recursos restritos 
  deste sistema.', 'N', 'N', NULL, 'S', 'A'),
(3, 2, 69, 'Permitir parecer RH',
 'Esta permissão permite o usuário dar o
  parecer da RH em uma ocorrência de ponto.',
  'N', 'S', 'Parecer RH', 'S', 'A'),
(4, 3, 68, 'Acessar Ocorrência de acidente',
 'Esta permissão permite se logar no sistema
  Ocorrência de acidente e usar os recursos restritos 
  deste sistema.', 'N', 'N', NULL, 'S', 'A'),
(5, 3, 69, 'Permitir parecer da SESMT',
 'Esta permissão permite o usuário dar o
  parecer da SESMT em uma ocorrência de acidente.',
  'N', 'S', 'Parecer CIPA', 'S', 'A'),
(6, 4, 68, 'Acessar Portal de refeição',
 'Esta permissão permite se logar no sistema
  Portal de refeição e usar os recursos restritos 
  deste sistema.', 'S', 'N', NULL, 'S', 'A'),
(7, 4, 68, 'Acessar Portal de refeição administrativo', 
 'Esta permissão permite se logar no sistema
  Portal de refeição administrativo e usar os  
  recursos restritos deste sistema.', 'N', 
  'S', 'Refeição Administrativo', 'S', 'A'),
(8, 4, 69, 'Permitir solicitar refeições de dobras e terceiros', 
 'Esta permissão permite o usuário solicitar
  refeições para dobras e terceiros.',
  'N', 'S', 'Solicitar Dobras/Terceiros', 'S', 'A'),
(9, 4, 70, 'Almoçar de Marmita',
 'Esta permissão permite o usuário ter acesso
  ao refeitorio sem solicitar refeição.',
  'N', 'N', NULL, 'S', 'A'),
(10, 4, 70, 'Passe Livre na Catraca',
 'Esta permissão permite o usuário ter acesso
  ao refeitorio sem solicitar refeição.',
  'N', 'N', NULL, 'S', 'A'),
(11, 5, 68, 'Acessar Controle de pessoas',
 'Esta permissão permite se logar no sistema
  Controle de pessoas e usar os recursos restritos 
  deste sistema.', 'N', 'N', NULL, 'S', 'A'),
(12, 7, 68, 'Acessar Portal da CIPA',
 'Esta permissão permite se logar no sistema
 Portal da CIPA e usar os recursos restritos 
  deste sistema.', 'N', 'N', NULL, 'S', 'A'),
(13, 4, 69, 'Gerenciamento do Preço da Refeição',
 'Esta permissão permite o usuário controlar o preço da refeição.',
 'N', 'S', 'Preço Refeição', 'S', 'A'),
(14, 4, 69, 'Solicitar final de semana e feriado',
 'Esta permissão permite o usuário solicitar refeição de final de semana e feriado.',
 'N', 'S', 'FDS/Feriado', 'S', 'A'),
(15, 4, 71, 'Consulta da refeições folha de pagamento',
 'O usuário pode consultar o valor a ser cobrado na folha de pagamento.',
 'N', 'S', 'Consulta - Refeições Folha de Pagamento', 'S', 'A'),
(16, 6, 68, 'Acessar o Kernel',
 'O usuário tem acesso ao sistema kernel',
 'N', 'N', NULL, 'N', 'A'),
(17, 4, 71, 'Consulta acesso catraca',
 'O usuário pode consultar os acessos na catraca.',
 'N', 'S', 'Consulta - Acesso Catraca', 'S', 'A');
     
INSERT INTO tb_variavel_global(nm_variavel, vl_variavel)
VALUES 
('url_raiz', 'http://localhost/');

INSERT INTO setores(cd_setor, ds_setor, dt_inclusao) VALUES (1, 'TI', NOW());

INSERT INTO cargo values (1, 'Administrador');

INSERT INTO pessoa_fisica(nr_cracha, nm_pessoa_fisica, ds_mail, ds_senha, dt_inclusao, cd_setor, cd_cargo, ie_situacao, ds_login_alternativo) 
values (
	1,
  'Admin',
  'admin@portal.com',
  md5('123'),
  NOW(),
  1,
  1,
  'A',
  'Santa Casa Paraíso'
);

INSERT INTO tb_permissao_pf VALUES (1, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (2, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (3, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (4, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (5, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (6, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (7, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (8, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (9, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (10, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (11, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (12, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (13, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (14, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (15, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (16, 1, 'S');
INSERT INTO tb_permissao_pf VALUES (17, 1, 'S');


select *
from pessoa_fisica