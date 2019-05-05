DROP TABLE advertisement;
CREATE TABLE advertisement (id bigint unsigned NOT NULL AUTO_INCREMENT, type varchar(100) NOT NULL, timestamp timestamp DEFAULT CURRENT_TIMESTAMP, ip_address varchar(100) NOT NULL, enabled tinyint NOT NULL, name text NOT NULL, email text NOT NULL, text text NOT NULL, image longblob, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf16;

CREATE TABLE `ban_ip` (
  `id` int(11) NOT NULL,
  `ip` varchar(100) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

ALTER TABLE `ban_ip`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ban_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;