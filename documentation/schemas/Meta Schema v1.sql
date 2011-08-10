SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `country`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `country` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `country` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `localization`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `localization` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `localization` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `country_id` INT(10) UNSIGNED NOT NULL ,
  `locale` VARCHAR(5) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `public` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `fallback_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_localization_fallback`
    FOREIGN KEY (`fallback_id` )
    REFERENCES `localization` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_localization_country`
    FOREIGN KEY (`country_id` )
    REFERENCES `country` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_localization_locale` ON `localization` (`locale` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_localization_fallback` ON `localization` (`fallback_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ix_localization_active` ON `localization` (`active` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ix_localization_public` ON `localization` (`active` ASC, `public` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_localization_country` ON `localization` (`country_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `locale_id` INT(10) UNSIGNED NOT NULL ,
  `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `blocked` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `blocked_msg` TEXT NULL DEFAULT NULL ,
  `manager` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `merchant` TINYINT(1) NOT NULL DEFAULT '0' ,
  `merchant_id` INT(10) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `password` VARCHAR(16) NULL DEFAULT NULL ,
  `facebook_uid` VARCHAR(255) NULL DEFAULT NULL ,
  `google_uid` VARCHAR(255) NULL DEFAULT NULL ,
  `twitter_uid` VARCHAR(255) NULL DEFAULT NULL ,
  `microsoft_uid` VARCHAR(255) NULL DEFAULT NULL ,
  `linkedin_uid` VARCHAR(255) NULL DEFAULT NULL ,
  `nickname` VARCHAR(64) NULL DEFAULT NULL ,
  `first_name` VARCHAR(255) NULL DEFAULT NULL ,
  `middle_name` VARCHAR(255) NULL DEFAULT NULL ,
  `family_name` VARCHAR(255) NULL DEFAULT NULL ,
  `birth_date` DATE NULL DEFAULT NULL ,
  `birth_place` VARCHAR(255) NULL DEFAULT NULL ,
  `birth_country` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `nationality` VARCHAR(255) NULL DEFAULT NULL ,
  `gender` VARCHAR(1) NULL DEFAULT NULL ,
  `marital_status` TINYINT(4) NULL DEFAULT NULL ,
  `home_address` VARCHAR(255) NULL DEFAULT NULL ,
  `home_address_ext` VARCHAR(255) NULL DEFAULT NULL ,
  `home_city` VARCHAR(255) NULL DEFAULT NULL ,
  `home_postcode` VARCHAR(255) NULL DEFAULT NULL ,
  `home_country` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `home_phone` VARCHAR(255) NULL DEFAULT NULL ,
  `home_mobile` VARCHAR(255) NULL DEFAULT NULL ,
  `home_latitude` FLOAT(10,6) NULL DEFAULT NULL ,
  `home_longitude` FLOAT(10,6) NULL DEFAULT NULL ,
  `work_type` VARCHAR(255) NULL DEFAULT NULL ,
  `work_industry` VARCHAR(255) NULL DEFAULT NULL ,
  `work_company` VARCHAR(255) NULL DEFAULT NULL ,
  `work_department` VARCHAR(255) NULL DEFAULT NULL ,
  `work_address` VARCHAR(255) NULL DEFAULT NULL ,
  `work_address_ext` VARCHAR(255) NULL DEFAULT NULL ,
  `work_city` VARCHAR(255) NULL DEFAULT NULL ,
  `work_postcode` VARCHAR(255) NULL DEFAULT NULL ,
  `work_country` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `work_phone` VARCHAR(255) NULL DEFAULT NULL ,
  `work_mobile` VARCHAR(255) NULL DEFAULT NULL ,
  `work_latitude` FLOAT(10,6) NULL DEFAULT NULL ,
  `work_longitude` FLOAT(10,6) NULL DEFAULT NULL ,
  `children_profile` TEXT NULL DEFAULT NULL ,
  `profile` TEXT NULL DEFAULT NULL ,
  `extra` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_user_locale`
    FOREIGN KEY (`locale_id` )
    REFERENCES `localization` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_home_country`
    FOREIGN KEY (`home_country` )
    REFERENCES `country` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_work_country`
    FOREIGN KEY (`work_country` )
    REFERENCES `country` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_birth_country`
    FOREIGN KEY (`birth_country` )
    REFERENCES `country` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_user_email` ON `user` (`email` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_user_facebook_uid` ON `user` (`facebook_uid` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_user_google_uid` ON `user` (`google_uid` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_user_twitter_uid` ON `user` (`twitter_uid` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_user_microsoft_uid` ON `user` (`microsoft_uid` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_user_linkedin_uid` ON `user` (`linkedin_uid` ASC) ;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_user_nickname` ON `user` (`nickname` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_user_locale` ON `user` (`locale_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ix_user_login` ON `user` (`email` ASC, `password` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_user_home_country` ON `user` (`home_country` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_user_work_country` ON `user` (`work_country` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_user_birth_country` ON `user` (`birth_country` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `frontend`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `frontend` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `frontend` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type` ENUM('WEB','MOBILE','DESKTOP') NOT NULL ,
  `name` VARCHAR(64) NOT NULL ,
  `version` VARCHAR(16) NULL DEFAULT NULL ,
  `extra` TEXT NULL DEFAULT NULL ,
  `obsolete` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE UNIQUE INDEX `ux_name` ON `frontend` (`name` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `action`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `action` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `action` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type` INT(10) UNSIGNED NOT NULL ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `latitude` FLOAT(10,6) NULL DEFAULT NULL ,
  `longitude` FLOAT(10,6) NULL DEFAULT NULL ,
  `frontend_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `user_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `points` INT(10) NOT NULL DEFAULT '0' ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_action_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_action_from`
    FOREIGN KEY (`frontend_id` )
    REFERENCES `frontend` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE INDEX `fk_action_user` ON `action` (`user_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_action_from` ON `action` (`frontend_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ix_action_geolocation` ON `action` (`latitude` ASC, `longitude` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ix_action_type` ON `action` (`type` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ix_action_user_type` ON `action` (`type` ASC, `user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `user_frontend`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_frontend` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `user_frontend` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(10) UNSIGNED NOT NULL ,
  `frontend_id` INT(10) UNSIGNED NOT NULL ,
  `install_uuid` VARCHAR(255) NOT NULL ,
  `install_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_user_frontend`
    FOREIGN KEY (`frontend_id` )
    REFERENCES `frontend` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_frontend_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE INDEX `fk_user_frontend` ON `user_frontend` (`frontend_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_frontend_user` ON `user_frontend` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `acl_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `acl_item` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `acl_item` (
  `name` VARCHAR(255) NOT NULL ,
  `type` TINYINT(4) NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE INDEX `ix_acl_item_type` ON `acl_item` (`type` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `acl_item_relation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `acl_item_relation` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `acl_item_relation` (
  `parent` VARCHAR(255) NOT NULL ,
  `child` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`parent`, `child`) ,
  CONSTRAINT `fk_acl_item_relation_parent`
    FOREIGN KEY (`parent` )
    REFERENCES `acl_item` (`name` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_acl_item_relation_child`
    FOREIGN KEY (`child` )
    REFERENCES `acl_item` (`name` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE INDEX `ix_acl_item_relation_child` ON `acl_item_relation` (`child` ASC) ;

SHOW WARNINGS;
CREATE INDEX `ix_acl_item_relation_parent` ON `acl_item_relation` (`parent` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `acl_assignment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `acl_assignment` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `acl_assignment` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `userid` INT(10) UNSIGNED NOT NULL ,
  `itemname` VARCHAR(255) NOT NULL ,
  `bizrule` TEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_acl_assignment_acl_item`
    FOREIGN KEY (`itemname` )
    REFERENCES `acl_item` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_acl_assignment_user`
    FOREIGN KEY (`userid` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE INDEX `fk_acl_assignment_acl_item` ON `acl_assignment` (`itemname` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_acl_assignment_user` ON `acl_assignment` (`userid` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ar_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ar_log` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `ar_log` (
  `action` TINYINT(4) NULL DEFAULT NULL COMMENT '0 - INSERT\n1 - UPDATE\n2 - DELETE' ,
  `user_id` INT(10) UNSIGNED NOT NULL ,
  `table` VARCHAR(255) NULL DEFAULT NULL ,
  `model` VARCHAR(255) NULL DEFAULT NULL ,
  `model_pk` VARCHAR(255) NULL DEFAULT NULL ,
  `before` TEXT NULL DEFAULT NULL ,
  `after` TEXT NULL DEFAULT NULL ,
  `browser` VARCHAR(255) NULL DEFAULT NULL ,
  `host_info` VARCHAR(255) NULL DEFAULT NULL ,
  `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  CONSTRAINT `fk_ar_log_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE INDEX `ix_ar_log_model_history` ON `ar_log` (`model` ASC, `model_pk` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_ar_log_user` ON `ar_log` (`user_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `preference`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `preference` ;

SHOW WARNINGS;
CREATE  TABLE IF NOT EXISTS `preference` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(10) UNSIGNED NOT NULL ,
  `frontend_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `value_int` INT(10) NULL DEFAULT NULL ,
  `value_bool` TINYINT(1) NULL DEFAULT NULL ,
  `value_string` VARCHAR(255) NULL DEFAULT NULL ,
  `value_text` TEXT NULL DEFAULT NULL ,
  `value_datetime` DATETIME NULL DEFAULT NULL ,
  `value_serialized` TEXT NULL DEFAULT NULL ,
  `value_image` VARCHAR(255) NULL DEFAULT NULL ,
  `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_preference_frontend`
    FOREIGN KEY (`frontend_id` )
    REFERENCES `frontend` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_preference_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SHOW WARNINGS;
CREATE INDEX `fk_preference_user` ON `preference` (`user_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_preference_frontend` ON `preference` (`frontend_id` ASC) ;

SHOW WARNINGS;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
