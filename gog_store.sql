-- --------------------------------------------------------
-- Host:                         192.168.56.103
-- Wersja serwera:               10.4.13-MariaDB-1:10.4.13+maria~stretch - mariadb.org binary distribution
-- Serwer OS:                    debian-linux-gnu
-- HeidiSQL Wersja:              11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Zrzut struktury bazy danych gog_store
CREATE DATABASE IF NOT EXISTS `gog_store` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `gog_store`;

-- Zrzut struktury tabela gog_store.carts
CREATE TABLE IF NOT EXISTS `carts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli gog_store.carts: ~0 rows (około)
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;

-- Zrzut struktury tabela gog_store.cart_products
CREATE TABLE IF NOT EXISTS `cart_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `amount` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli gog_store.cart_products: ~0 rows (około)
/*!40000 ALTER TABLE `cart_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_products` ENABLE KEYS */;

-- Zrzut struktury tabela gog_store.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL COMMENT 'Game title',
  `price` decimal(10,2) unsigned NOT NULL COMMENT 'Game price in USD',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Zrzucanie danych dla tabeli gog_store.products: ~0 rows (około)
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `title`, `price`) VALUES
	(1, 'Fallout', 1.99),
	(2, 'Don’t Starve', 2.99),
	(3, 'Baldur’s Gate', 3.99),
	(4, 'Icewind Dale', 4.99),
	(5, 'Bloodborne', 5.99);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
