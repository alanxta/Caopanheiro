-- Desabilitar verificações de unicidade e chaves estrangeiras temporariamente
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Criar esquema se não existir
CREATE SCHEMA IF NOT EXISTS `caopanheiro` DEFAULT CHARACTER SET utf8;
USE `caopanheiro`;

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS `usuario` (
  `usuarioId` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `sobrenome` VARCHAR(50) NULL,
  `data_nascimento` DATE NULL,
  `email` VARCHAR(100) NOT NULL,
  `estado` VARCHAR(45) NULL,
  `cidade` VARCHAR(45) NULL,
  `endereco` VARCHAR(100) NOT NULL,
  `status` ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
  `perfil` ENUM('adotante', 'doador', 'administrador') NOT NULL,
  `cpf` VARCHAR(14) NOT NULL,
  `senha` VARCHAR(100) NOT NULL COMMENT 'Senha armazenada com bcrypt',
  PRIMARY KEY (`usuarioId`),
  UNIQUE INDEX `cpf_UNIQUE` (`cpf` ASC))
ENGINE = InnoDB;

-- Criar tabela de mensagens
CREATE TABLE IF NOT EXISTS `mensagens` (
  `mensagemId` INT NOT NULL AUTO_INCREMENT,
  `chatId` INT NOT NULL,
  `remetente` INT NOT NULL,
  `conteudo` VARCHAR(255) NOT NULL,
  `dataEnvio` DATETIME NOT NULL,
  PRIMARY KEY (`mensagemId`),
  INDEX (`chatId`),
  INDEX (`remetente`),
  FOREIGN KEY (`chatId`) REFERENCES `chats` (`chatId`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY (`remetente`) REFERENCES `usuario` (`usuarioId`)
    ON DELETE NO ACTION ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Criar tabela de pets
CREATE TABLE IF NOT EXISTS `pets` (
  `petId` INT NOT NULL AUTO_INCREMENT,
  `especie` ENUM('gato', 'cachorro') NOT NULL,
  `raca` VARCHAR(45) NOT NULL,
  `dataNascimento` DATE NULL,
  `sexo` ENUM('F', 'M') NOT NULL,
  `porte` ENUM('grande', 'medio', 'pequeno') NULL,
  `estado` VARCHAR(45) NULL,
  `cidade` VARCHAR(45) NULL,
  `descricao` VARCHAR(255) NULL,
  `fotos` VARCHAR(255) NOT NULL, -- Caminho para a foto
  `status` ENUM('disponivel', 'adotado'),
  PRIMARY KEY (`petId`))
ENGINE = InnoDB;

-- Criar tabela de adoção
CREATE TABLE IF NOT EXISTS `adocao` (
  `idAdocao` INT NOT NULL AUTO_INCREMENT,
  `adotante` INT NOT NULL,
  `petId` INT NOT NULL,
  `dataAdocao` DATE NOT NULL,
  PRIMARY KEY (`idAdocao`),
  INDEX `fk_adocao_pet_idx` (`petId` ASC),
  INDEX `fk_adocao_usuario_idx` (`adotante` ASC),
  CONSTRAINT `fk_adocao_usuario`
    FOREIGN KEY (`adotante`)
    REFERENCES `usuario` (`usuarioId`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_adocao_pets1`
    FOREIGN KEY (`petId`)
    REFERENCES `pets` (`petId`)
    ON DELETE NO ACTION ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Criar tabela de chats
CREATE TABLE IF NOT EXISTS `chats` (
  `chatId` INT NOT NULL AUTO_INCREMENT,
  `adotante` INT NOT NULL,
  `doador` INT NOT NULL,
  PRIMARY KEY (`chatId`),
  INDEX `fk_chats_usuario1_idx` (`adotante` ASC),
  INDEX `fk_chats_usuario2_idx` (`doador` ASC),
  CONSTRAINT `fk_chats_usuario1`
    FOREIGN KEY (`adotante`)
    REFERENCES `usuario` (`usuarioId`)
    ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_chats_usuario2`
    FOREIGN KEY (`doador`)
    REFERENCES `usuario` (`usuarioId`)
    ON DELETE NO ACTION ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- Criar tabela de administradores
CREATE TABLE IF NOT EXISTS `administrador` (
  `adminId` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `cargo` VARCHAR(45) NOT NULL,
  `perfil` VARCHAR(15) NULL DEFAULT 'administrador',
  `status` ENUM('ativo', 'inativo') NULL DEFAULT 'ativo',
  PRIMARY KEY (`adminId`))
ENGINE = InnoDB;

-- Restaurar configurações SQL
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
