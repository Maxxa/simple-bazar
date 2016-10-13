DROP TABLE advertisement;
CREATE TABLE advertisement (id bigint unsigned NOT NULL AUTO_INCREMENT, type varchar(100) NOT NULL, timestamp timestamp DEFAULT CURRENT_TIMESTAMP, ip_address varchar(100) NOT NULL, enabled tinyint NOT NULL, name text NOT NULL, email text NOT NULL, text text NOT NULL, image longblob, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf16;
