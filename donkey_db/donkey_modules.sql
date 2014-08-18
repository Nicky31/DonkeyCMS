--
-- Structure de la table `donkey_modules`
--

CREATE TABLE IF NOT EXISTS `donkey_modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `settings` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `donkey_modules`
--

INSERT INTO `donkey_modules` (`id`, `name`, `enabled`, `settings`) VALUES
(1, 'home', 1, NULL);

