SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `anggota` (
  id BIGINT(10) NOT NULL,
  user_name   VARCHAR(100) NOT NULL COMMENT 'User name unik',
  nama_depan  VARCHAR(255) NOT NULL DEFAULT 'anonim' COMMENT 'Nama awalan default anonim',
  nama_belakang VARCHAR(255) DEFAULT NULL COMMENT 'Nama akhiran anggota',
  email      VARCHAR(255) NOT NULL COMMENT 'Email anggota',
  is_admin   BOOL NOT NULL DEFAULT FALSE COMMENT 'Boolean apabila admin set ke true',
  password   VARCHAR(60) COMMENT 'Password sha1(string password)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- ALTERING for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`user_name`);

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` BIGINT(11) NOT NULL AUTO_INCREMENT;



-- delete admin
DELETE FROM anggota WHERE user_name='admin';
DELETE FROM anggota WHERE user_name='contoh';

--
-- tambahkan user - username = admin, password = password
--
INSERT INTO anggota(user_name, nama_depan, email, password, is_admin)
    VALUES ('admin', 'Administrator', 'admin@example.com', sha1('password'), TRUE);
INSERT INTO anggota(user_name, nama_depan, email, password, is_admin)
    VALUES ('contoh', 'User', 'user@example.com', sha1('password'), FALSE);


CREATE TABLE `buku` (
  id BIGINT(10) NOT NULL,
  judul     VARCHAR(255) NOT NULL COMMENT 'Nama Buku',
  tahun      INT(4) ZEROFILL NOT NULL COMMENT 'Tahun terbit',
  pengarang  VARCHAR(255) NOT NULL COMMENT 'Nama Pengarang maksimum 255 karakter',
  penerbit   VARCHAR(255) NOT NULL COMMENT 'Nama Penerbit',
  keterangan TEXT DEFAULT NULL COMMENT 'Keterangan atau sinopsis',
  path_gambar VARCHAR(255) DEFAULT NULL COMMENT 'path ke gambar',
  anggota_id_pinjam BIGINT(10) DEFAULT NULL COMMENT 'anggota id di pinjam, 0 atau null apabila free',
  tanggal_pinjam TIMESTAMP DEFAULT now()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- TAMBAHKAN DISINI
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
