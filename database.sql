SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `anggota` (
  id BIGINT(10) NOT NULL,
  user_name   VARCHAR(100) NOT NULL,
  nama_awal  VARCHAR(255) NOT NULL DEFAULT 'anonim',
  nama_akhir VARCHAR(255) DEFAULT NULL,
  email      VARCHAR(255) NOT NULL,
  password   VARCHAR(60)
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

--
-- tambahkan user - username = admin, password = password
--
INSERT INTO anggota(user_name, nama_awal, email, password)
    VALUES ('admin', 'administrator', 'admin@example.com', sha1('password'));

--
-- TAMBAHKAN DISINI
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
