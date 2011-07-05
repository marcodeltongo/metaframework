/*
-- Query:
-- Date: 2011-07-05 15:51
*/

INSERT INTO `AuthItem` (`name`,`type`,`description`,`bizrule`,`data`) VALUES
('guest',2,'Guest user role','return Yii::app()->user->getIsGuest();',NULL),
('authenticated',2,'Authenticated user role','return !Yii::app()->user->getIsGuest();',NULL),
('administrator',2,'Administrator user role',NULL,NULL),
('*.site',1,'SiteController all actions',NULL,NULL),
('index.site',0,'SiteController::actionIndex',NULL,NULL),
('*.user',1,'UserController all actions',NULL,NULL),
('*',1,'SuperPowers Task',NULL,NULL);

INSERT INTO `AuthItemChild` VALUES
('administrator', '*');