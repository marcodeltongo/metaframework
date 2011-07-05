SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `AuthItem`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `AuthItem` (
  `name` VARCHAR(255) NOT NULL ,
  `type` TINYINT(4) NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`name`) ,
  INDEX `ix_authitem_type` (`type` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `AuthItemChild`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `AuthItemChild` (
  `parent` VARCHAR(255) NOT NULL ,
  `child` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`parent`, `child`) ,
  INDEX `ix_authitemchild` (`child` ASC) ,
  CONSTRAINT `fk_authitemparent`
    FOREIGN KEY (`parent` )
    REFERENCES `AuthItem` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_authitemchild`
    FOREIGN KEY (`child` )
    REFERENCES `AuthItem` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `AuthAssignment`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `AuthAssignment` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `itemname` VARCHAR(255) NOT NULL ,
  `userid` VARCHAR(255) NOT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_authassignment2authitem` (`itemname` ASC) ,
  CONSTRAINT `fk_authassignment2authitem`
    FOREIGN KEY (`itemname` )
    REFERENCES `AuthItem` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `ActiveRecordLog`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ActiveRecordLog` (
  `action` TINYINT(4) NULL DEFAULT NULL COMMENT '0 - INSERT\n1 - UPDATE\n2 - DELETE' ,
  `table` VARCHAR(255) NULL DEFAULT NULL ,
  `model` VARCHAR(255) NULL DEFAULT NULL ,
  `model_pk` VARCHAR(255) NULL DEFAULT NULL ,
  `before` TEXT NULL DEFAULT NULL ,
  `after` TEXT NULL DEFAULT NULL ,
  `user_pk` VARCHAR(255) NULL DEFAULT NULL ,
  `browser` VARCHAR(255) NULL DEFAULT NULL ,
  `host_info` VARCHAR(255) NULL DEFAULT NULL ,
  `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  INDEX `ix_arlog_model_history` (`model` ASC, `model_pk` ASC) ,
  INDEX `ix_arlog_user_actions` (`user_pk` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
