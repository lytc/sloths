SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `user_permissions`;
DROP TABLE IF EXISTS `posts`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

CREATE TABLE `permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `user_permissions` (
  `user_id` int(11) unsigned NOT NULL,
  `permission_id` int(11) unsigned NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `users` VALUES ('1', 'name1');
INSERT INTO `users` VALUES ('2', 'name2');
INSERT INTO `users` VALUES ('3', 'name3');
INSERT INTO `users` VALUES ('4', 'name4');

INSERT INTO `products` VALUES ('1', 'name1');
INSERT INTO `products` VALUES ('2', 'name2');
INSERT INTO `products` VALUES ('3', 'name3');
INSERT INTO `products` VALUES ('4', 'name4');

INSERT INTO `orders` VALUES ('1', '1', '1', '0');
INSERT INTO `orders` VALUES ('2', '1', '2', '0');
INSERT INTO `orders` VALUES ('3', '2', '1', '0');
INSERT INTO `orders` VALUES ('4', '2', '2', '0');

INSERT INTO `permissions` VALUES ('1', 'name1');
INSERT INTO `permissions` VALUES ('2', 'name2');
INSERT INTO `permissions` VALUES ('3', 'name3');
INSERT INTO `permissions` VALUES ('4', 'name4');

INSERT INTO `user_permissions` VALUES ('1', '1');
INSERT INTO `user_permissions` VALUES ('1', '2');
INSERT INTO `user_permissions` VALUES ('2', '1');
INSERT INTO `user_permissions` VALUES ('2', '2');

INSERT INTO `posts` VALUES ('1', '1', 'name1', 'content1');
INSERT INTO `posts` VALUES ('2', '1', 'name2', 'content2');
INSERT INTO `posts` VALUES ('3', '2', 'name3', 'content3');
INSERT INTO `posts` VALUES ('4', '2', 'name4', 'content4');

SET FOREIGN_KEY_CHECKS = 1;