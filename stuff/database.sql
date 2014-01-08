
CREATE TABLE IF NOT EXISTS `sensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `type` varchar(32) NOT NULL,
  `description` varchar(300) DEFAULT '' NOT NULL,
  `pin` varchar(32) DEFAULT '' NOT NULL,
  `interval` int(9) DEFAULT 1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sensor_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `value` double NOT NULL,
  `sensor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `point` (`sensor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `sensor_values`
  ADD CONSTRAINT `sensor_id` FOREIGN KEY (`sensor_id`) REFERENCES `sensors` (`id`) ON DELETE CASCADE;
