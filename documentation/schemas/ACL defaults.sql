/*
-- Query:
-- Date: 2011-06-23 16:08
*/
INSERT INTO `AuthItem` (`name`,`type`,`description`,`bizrule`,`data`) VALUES ('guest',2,'Guest user role','return Yii::app()->user->getIsGuest();',NULL);
INSERT INTO `AuthItem` (`name`,`type`,`description`,`bizrule`,`data`) VALUES ('authenticated',2,'Authenticated user role','return !Yii::app()->user->getIsGuest();',NULL);
INSERT INTO `AuthItem` (`name`,`type`,`description`,`bizrule`,`data`) VALUES ('administrator',2,'Administrator user role',NULL,NULL);
INSERT INTO `AuthItem` (`name`,`type`,`description`,`bizrule`,`data`) VALUES ('*',1,'SuperPowers Task',NULL,NULL);