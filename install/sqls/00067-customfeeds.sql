CREATE TABLE IF NOT EXISTS `customfeeds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner` bigint NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `privacy` tinyint unsigned NOT NULL DEFAULT '0',
  `content_type` tinyint unsigned NOT NULL DEFAULT '0',
  `created` bigint unsigned NOT NULL,
  `edited` bigint unsigned DEFAULT NULL,
  `deleted` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `owner_deleted` (`owner`, `deleted`),
  FULLTEXT KEY `title_description` (`name`, `description`),
  FULLTEXT KEY `title` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS `customfeed_sources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `feed` bigint unsigned NOT NULL,
  `source` bigint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_source` (`feed`, `source`),
  KEY `feed` (`feed`),
  KEY `source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS `customfeed_relations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user` bigint unsigned NOT NULL,
  `feed` bigint unsigned NOT NULL,
  `type` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_feed` (`user`, `feed`),
  KEY `user` (`user`),
  KEY `feed` (`feed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

ALTER TABLE `profiles`
  ADD COLUMN `customfeeds_order` TEXT DEFAULT NULL,
  ADD COLUMN `hidden_customfeeds` TEXT DEFAULT NULL;