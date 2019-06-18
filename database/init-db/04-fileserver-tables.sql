USE `fileserver`;

--
-- Datenbank: `identity`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` CHAR(36)  NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `preview_pic` VARCHAR(200) NOT NULL,
  `short_description` VARCHAR(250) NOT NULL,
  `redirect` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Tabellenstruktur für Tabelle `clients_tags`
--

DROP TABLE If EXISTS  `clients_tags`;
CREATE TABLE IF NOT EXISTS `clients_tags` (
  `id` CHAR(36) NOT NULL,
  `client_id` CHAR(36) NOT NULL,
  `tag_id` CHAR(36) NOT NULL,
  PRIMARY KEY (`id`) -- ,
  --  FOREIGN KEY (`client_id`) REFERENCES clients (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  --  FOREIGN KEY (`tag_id`) REFERENCES tags (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;




-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` CHAR(36)  NOT NULL,
  `name` VARCHAR(50) UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Tabellenstruktur für Tabelle `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` CHAR(36)  NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `extension` VARCHAR(5) NOT NULL,
  `version` INT(11) NOT NULL,
  `date` DATE NOT NULL,
  `userId` CHAR(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tags_files`
--

ALTER TABLE files ADD INDEX file_id_idx(id);
ALTER TABLE tags ADD INDEX tag_id_idx(id);

DROP TABLE If EXISTS  `tags_files`;
CREATE TABLE IF NOT EXISTS `tags_files` (
  `id` CHAR(36) NOT NULL,
  `tag_id` CHAR(36) NOT NULL,
  `file_id` CHAR(36) NOT NULL,
  PRIMARY KEY (`id`),
--  CONSTRAINT FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE
  --  FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  --  CONSTRAINT FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;









