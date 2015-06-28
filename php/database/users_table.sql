
CREATE TABLE IF NOT EXISTS `?` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(99) NOT NULL,
      `pwhash` varchar(99) NOT NULL,
      `role` varchar(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
