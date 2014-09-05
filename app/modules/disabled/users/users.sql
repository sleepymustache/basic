SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `users` ;
CREATE SCHEMA IF NOT EXISTS `users` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `users` ;

-- -----------------------------------------------------
-- Table `users`.`roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`.`roles` ;

CREATE  TABLE IF NOT EXISTS `users`.`roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `id_UNIQUE` ON `users`.`roles` (`id` ASC) ;


-- -----------------------------------------------------
-- Table `users`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`.`users` ;

CREATE  TABLE IF NOT EXISTS `users`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` INT UNSIGNED NOT NULL ,
  `email` VARCHAR(100) NOT NULL ,
  `password` VARCHAR(45) NOT NULL ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `modified` TIMESTAMP NULL ,
  PRIMARY KEY (`id`, `role_id`) ,
  CONSTRAINT `fk_users_roles`
    FOREIGN KEY (`role_id` )
    REFERENCES `users`.`roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `id_UNIQUE` ON `users`.`users` (`id` ASC) ;

CREATE INDEX `fk_users_roles_idx` ON `users`.`users` (`role_id` ASC) ;


-- -----------------------------------------------------
-- Table `users`.`usermeta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`.`usermeta` ;

CREATE  TABLE IF NOT EXISTS `users`.`usermeta` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT UNSIGNED NOT NULL ,
  `key` VARCHAR(100) NOT NULL ,
  `value` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`, `user_id`) ,
  CONSTRAINT `fk_usermeta_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `id_UNIQUE` ON `users`.`usermeta` (`id` ASC) ;

CREATE INDEX `fk_usermeta_users1_idx` ON `users`.`usermeta` (`user_id` ASC) ;


-- -----------------------------------------------------
-- Table `users`.`permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`.`permissions` ;

CREATE  TABLE IF NOT EXISTS `users`.`permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` INT UNSIGNED NOT NULL ,
  `key` VARCHAR(45) NULL ,
  `value` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`, `role_id`) ,
  CONSTRAINT `fk_permissions_roles1`
    FOREIGN KEY (`role_id` )
    REFERENCES `users`.`roles` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `id_UNIQUE` ON `users`.`permissions` (`id` ASC) ;

CREATE INDEX `fk_permissions_roles1_idx` ON `users`.`permissions` (`role_id` ASC) ;

USE `users` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `users`.`roles`
-- -----------------------------------------------------
START TRANSACTION;
USE `users`;
INSERT INTO `users`.`roles` (`id`, `name`) VALUES (1, 'admin');
INSERT INTO `users`.`roles` (`id`, `name`) VALUES (2, 'user');
INSERT INTO `users`.`roles` (`id`, `name`) VALUES (3, 'anonymous');

COMMIT;

-- -----------------------------------------------------
-- Data for table `users`.`permissions`
-- -----------------------------------------------------
START TRANSACTION;
USE `users`;
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (1, 1, 'add-user', '1');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (2, 1, 'edit-user', '1');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (3, 1, 'delete-user', '1');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (4, 1, 'view-admin-section', '1');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (5, 1, 'manage-roles', '1');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (6, 2, 'view-user-section', '1');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (7, 2, 'add-user', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (8, 2, 'edit-user', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (9, 2, 'delete-user', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (10, 2, 'view-admin-section', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (11, 2, 'manage-roles', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (12, 1, 'view-user-section', '1');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (13, 3, 'add-user', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (14, 3, 'edit-user', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (15, 3, 'delete-user', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (16, 3, 'view-admin-section', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (17, 3, 'manage-roles', '0');
INSERT INTO `users`.`permissions` (`id`, `role_id`, `key`, `value`) VALUES (18, 3, 'view-user-section', '0');

COMMIT;
