/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE IF NOT EXISTS `bank` (
  `id` char(36) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `rekening` varchar(50) NOT NULL,
  `atas_nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bank` (`id`, `nama`, `rekening`, `atas_nama`) VALUES
	('c9f807ba-fb4f-43d6-bfd8-8871c24a561c', 'BCA', '60184819383', 'Fulan');

CREATE TABLE IF NOT EXISTS `rekening_admin` (
  `id` char(36) NOT NULL,
  `bank` char(36) NOT NULL,
  `rekening` varchar(50) NOT NULL,
  `atas_nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `rekening_admin` (`id`, `bank`, `rekening`, `atas_nama`) VALUES
	('c9f807ba-fb4f-43d6-bfd8-8871c24a561c', 'BNI', '12700283733', 'PT Bos COD Indonesia');

CREATE TABLE IF NOT EXISTS `transaksi_transfer` (
  `id` char(36) NOT NULL,
  `id_transaksi` varchar(11) NOT NULL,
  `nilai_transfer` int(11) NOT NULL,
  `kode_unik` int(11) NOT NULL,
  `total_transfer` int(11) NOT NULL,
  `berlaku_hingga` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `transaksi_transfer` (`id`, `id_transaksi`, `nilai_transfer`, `kode_unik`, `total_transfer`, `berlaku_hingga`) VALUES
	('4a2aafc1-601d-11ed-871e-bcee7b0f0dcb', 'TF221109999', 50000, 584, 50584, '2022-11-10 04:57:32'),
	('d3cabc58-601d-11ed-871e-bcee7b0f0dcb', 'TF221109999', 50000, 795, 50795, '2022-11-10 05:01:23'),
	('dfaa7870-601d-11ed-871e-bcee7b0f0dcb', 'TF221109999', 50000, 287, 50287, '2022-11-10 05:01:43');

CREATE TABLE IF NOT EXISTS `user` (
  `id` char(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `username`, `password`) VALUES
	('ac7bf1a2-3e6c-40f9-a8f0-ba6781de6e3d', 'user@boscod.com', 'ac43724f16e9241d990427ab7c8f4228');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
