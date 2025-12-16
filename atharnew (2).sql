-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 25 أبريل 2025 الساعة 12:31
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atharnew`
--

-- --------------------------------------------------------

--
-- بنية الجدول `admins`
--

CREATE TABLE `admins` (
  `national_id` varchar(20) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `birth_date` varchar(150) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `PASSWORD` varchar(30) NOT NULL,
  `Blocked` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admins`
--

INSERT INTO `admins` (`national_id`, `first_name`, `last_name`, `birth_date`, `Email`, `phoneNumber`, `PASSWORD`, `Blocked`) VALUES
('1111111111', 'ساره', 'المطيري', ' 1999-12-27', 'hgd3@gmail.com', '0547891258', '123', 0),
('1212345678', 'احمد', 'العتيبي', '', '', '4444444444', '123', 0),
('1232355568', 'عمر', 'المطيري', '', '', '05353534342', '123', 0),
('1236544561', 'علي', 'المطيري', '', '', '', '123', 0),
('2345121230', 'ساره', 'المطيري', '', '', '', '123', 0),
('2345678001', 'محمد', 'المطيري', '2005-06-07', 'ggh34@gmail.com', '1029394857', '123', 0),
('2345678910', 'خالد', 'العتيبي', '1997-10-22', 'ggg34@gmail.com', '0980980980', '123', 0),
('5254874565', 'نور', 'العضيله', '', '', '0548487898', '123', 0),
('7896325410', 'dfds', 'ioyh', '1999-02-10', 'sde@gmail.com', '98765432145', '123', 0);

-- --------------------------------------------------------

--
-- بنية الجدول `consultations`
--

CREATE TABLE `consultations` (
  `ID` int(11) NOT NULL,
  `Donor_id` varchar(20) NOT NULL,
  `Subject` varchar(4000) NOT NULL,
  `MessageDonor` varchar(4000) NOT NULL,
  `consultation_date` varchar(4000) NOT NULL,
  `doctor_id` varchar(20) NOT NULL,
  `DrAnswer` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `consultations`
--

INSERT INTO `consultations` (`ID`, `Donor_id`, `Subject`, `MessageDonor`, `consultation_date`, `doctor_id`, `DrAnswer`) VALUES
(1, '2222222222', 'استشارة عنوان', 'الاستشارة 111111111111111111', '', '', ''),
(2, '2222222222', 'استشارة عنوان', 'تتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتتت', '', '', ''),
(3, '2222222222', 'عنوان الاستشارة', 'للللللللللللللللللللللللللللللللللللل', '', '', ''),
(4, '1121177792', 'المشروبات الغازيه', 'هل المشروبات الغزيه تؤثر على الكليه', '', '', ''),
(5, '1161155572', 'استشارة عنوان', 'الاثار الجانبيه ', '', '', '');

-- --------------------------------------------------------

--
-- بنية الجدول `doctors`
--

CREATE TABLE `doctors` (
  `national_id` varchar(20) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `birth_date` varchar(20) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `speciality` varchar(4000) NOT NULL,
  `Hospital` varchar(400) NOT NULL,
  `city` varchar(400) NOT NULL,
  `Blocked` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `doctors`
--

INSERT INTO `doctors` (`national_id`, `first_name`, `last_name`, `birth_date`, `Email`, `phoneNumber`, `Password`, `speciality`, `Hospital`, `city`, `Blocked`) VALUES
('0909090909', 'صالح', 'المطيري', '', '', '', '123', '', '', '', 1),
('1114445566', 'فهد', 'العتيبي', ' 2003-02-26', 'dffg@gmail.com', '0987654321', '123', 'باطنيه', 'hhhhh', 'جده', 0),
('1191177722', 'راكان', 'خالدي', ' 2000-06-06', 'rfvb34@gmail.com', '1029394857', '123', 'جراحه كبد', 'الملك فهد', 'جده', 0),
('1478520036', 'منيره', 'المطيري', '1990-05-14', 'hgfs@gmail.com', '98765432145', '123', 'lsde', 'asd', 'rty', 0),
('2222233333', 'نوره', 'المطيري', '2008-01-30', 'fhgf34@gmail.com', '0987654321', '123', '', '', '', 0),
('2323409876', 'العنود', 'العزيزي', '', '', '', '123', '', '', '', 0),
('5467878789', 'حمد', 'المطيري', '', '', '', '123', '', '', '', 0),
('7777777777', 'نجد', 'المطيري', '2015-05-05', 'dfy@gmal.com', '1233214565', '123', '', '', '', 0),
('8888888888', 'عبدالعزيز', 'المطيري', '', '', '', '123', '', '', '', 0),
('988963254', 'نهى', 'العتيبي', '', '', '', '123', '', '', '', 0);

-- --------------------------------------------------------

--
-- بنية الجدول `donations`
--

CREATE TABLE `donations` (
  `ID` int(11) NOT NULL,
  `PatientID` varchar(20) NOT NULL,
  `DonorID` varchar(20) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `DateTimess` varchar(30) NOT NULL,
  `Descr` varchar(4000) NOT NULL,
  `Organ` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `donations`
--

INSERT INTO `donations` (`ID`, `PatientID`, `DonorID`, `Status`, `DateTimess`, `Descr`, `Organ`) VALUES
(1, '', '', '', '2025-04-30', 'عاجله', 'كبد'),
(2, '', '', '', '2025-04-28', 'ااااا', 'كلية'),
(3, '', '', '', '2025-04-25', 'يبليبليبليبلبليل', 'كلية'),
(4, '', '2222222222', '', '2025-04-03', 'يليبليبل', 'كبد'),
(5, '', '2222222222', '', '2025-04-11', 'يبيب', 'كبد'),
(6, '', '', '', '2025-04-11', 'dfsdfsdf', 'كبد'),
(7, '', '2222222222', '', '2025-04-17', 'sdsdfsdf', 'كبد'),
(8, '', '2222222222', '', '2025-04-10', 'dsfefewr', 'كبد'),
(9, '', '4444555663', '', '2025-04-29', 'عاجله', 'كبد'),
(10, '', '1121177792', '', '2025-05-05', 'عمليه طارئه', 'كلية'),
(11, '', '1161155572', '', '2025-04-30', 'الحضور قبل الموعد', 'كبد');

-- --------------------------------------------------------

--
-- بنية الجدول `donors`
--

CREATE TABLE `donors` (
  `national_id` varchar(200) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `birth_date` varchar(200) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Password` varchar(200) NOT NULL,
  `phoneNumber` varchar(200) NOT NULL,
  `Blocked` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `donors`
--

INSERT INTO `donors` (`national_id`, `first_name`, `last_name`, `birth_date`, `Email`, `Password`, `phoneNumber`, `Blocked`) VALUES
('1121177792', 'نجد', 'المطيري', '2003-05-24', 'najd@gmail.com', '123', '98765432145', 1),
('1161155572', 'سالم', 'المطيري', '2012-06-14', 'dfg3@2gmail.com', '123', '0987654321', 0),
('1231231231', 'سالم', 'lllll', ' 1998-05-05', 'dcf@gmail', '123', '1029394857', 0),
('1234567890', 'شهد', 'المطيري', '1995-06-06', 'gdsa34@ugmail.com', '123', '98765432145', 0),
('1234567899', 'hhh', 'المطيري', '2019-12-31', 'gdseea34@ugmail.com', '123', '98765432145', 0),
('2222222222', 'hhhhh', 'gtttt', '', '', '123', '', 0),
('4444555663', 'لانا', 'العتيبي', '', '', '123', '', 0),
('6573334545', 'رتيل', 'المطيري', '', '', '123', '', 0),
('6587876767', 'رهف', 'العتيبي', '', '', '123', '', 0),
('7496325187', 'تركي', 'المطيري', '1996-06-05', 'iiui32@gmail.com', '123', '98765432145', 0);

-- --------------------------------------------------------

--
-- بنية الجدول `medicalcases`
--

CREATE TABLE `medicalcases` (
  `id` int(11) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `have_disease` varchar(4000) NOT NULL,
  `FilePath` varchar(4000) NOT NULL,
  `bloodType` varchar(10) NOT NULL,
  `organ` varchar(4000) NOT NULL,
  `National_ID_Donor` varchar(20) NOT NULL,
  `diagnosis` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `medicalcases`
--

INSERT INTO `medicalcases` (`id`, `national_id`, `have_disease`, `FilePath`, `bloodType`, `organ`, `National_ID_Donor`, `diagnosis`) VALUES
(1, '', '', 'Uploads/05- Web Forms.pdf', 'AB+ ', 'كبد', '2222222222', 'الموافقه على التبرع'),
(2, '6666699999', 'سكر', 'C:UsersmonerDownloadsAtharAtharAtharUploads\05- Web Forms.pdf', 'B- ', 'كلية', '', 'رفض التبرع'),
(3, '9999999999', 'ربو', '', 'A+ ', 'كلية', '', 'uh[gi'),
(4, '1254789632', '', 'Uploads/05- Web Forms.pdf', 'B- ', 'كبد', '', 'رفض التبرع'),
(5, '7418529630', '', 'Uploads/05- Web Forms.pdf', 'B+ ', 'كبد', '', 'رفض التبرع'),
(6, '4545685241', 'سكر', 'Uploads/Activity Book2.pdf', 'B+ ', 'كلية', '', 'رفض التبرع'),
(7, '4564567895', 'ربو', '', 'O+ ', 'كبد', '', 'ارجو القيام ببعض التحاليل وارفاقها مره اخرى'),
(8, '', '', '', 'O+ ', 'كلية', '1234567890', 'رفض التبرع'),
(9, '', '', 'Uploads/05- Web Forms.pdf', 'A+ ', 'كلية', '1234567899', 'رفض التبرع'),
(10, '', '', 'Uploads/05- Web Forms.pdf', 'O+ ', 'كبد', '4444555663', 'الموافقه على التبرع'),
(11, '', '', 'Uploads/05- Web Forms.pdf', 'O+ ', 'كلية', '7496325187', 'الموافقه على التبرع'),
(12, '1120968258', '', 'Uploads/Activity Book2.pdf', 'O+ ', 'كلية', '', 'يحتاج المريض الى زراعه كلى في اقرب وقت'),
(13, '', '', 'Uploads/04- Table and structured data.pptx', 'O+ ', 'كلية', '1121177792', 'الموافقه على التبرع'),
(14, '', '', 'Uploads/Activity Book.pdf', 'A+ ', 'كبد', '1161155572', 'الموافقه على التبرع'),
(15, '1165589732', '', 'Uploads/Activity Book2.pdf', 'A+ ', 'كبد', '', 'الكبد لا تقوم بوظائفها وتحتاج الى متبرع');

-- --------------------------------------------------------

--
-- بنية الجدول `memberss`
--

CREATE TABLE `memberss` (
  `member_id` int(11) NOT NULL,
  `name` varchar(4000) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `donation_date` varchar(20) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `PatientID` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `patients`
--

CREATE TABLE `patients` (
  `national_id` varchar(20) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `birth_date` varchar(20) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `Blocked` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `patients`
--

INSERT INTO `patients` (`national_id`, `first_name`, `last_name`, `birth_date`, `Email`, `password`, `phoneNumber`, `Blocked`) VALUES
('3333333333', '', '', '', '', '123', '', 0),
('9999999999', 'lllll', 'sdsfff', '2004-06-08', 'sds34@gmal.com', '123', '1029394857', 1),
('6666699999', 'منيره', 'المطيري', '1999-02-03', 'hhggg@gmail.com', '123', '98765432145', 0),
('1254789632', 'فارس', 'العتيبي', ' 1994-03-04', 'xx@ad.com', '123', '0987654321', 0),
('7418529630', 'لانا', 'المطيري', '', '', '123', '', 0),
('4545685241', 'احمد', 'العضيله', '', '', '123', '', 0),
('3456700117', 'نهى', 'المطيري', '', '', '', '', 0),
('', '', '', '', '', '123', '', 0),
('4568210047', 'اسماء', 'المطيري', '', '', '123', '', 0),
('4564567895', 'SDF', 'DFG', '1991-06-05', 'fdu34@gmail.com', '123', '0987654321', 0),
('1120968258', 'منيره', 'المطيري', '2003-05-24', 'mno45@gmail.com', '123', '0980980980', 0),
('1165589732', 'خالد', 'المطيري', '1999-06-10', 'dfg3@2gmail.com', '123', '0980980980', 0),
('1200333333', 'ASDFFSAD', 'hhhh', '1999-09-20', 'dcf@gmail', '123', '0980980980', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`national_id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`national_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`national_id`);

--
-- Indexes for table `medicalcases`
--
ALTER TABLE `medicalcases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberss`
--
ALTER TABLE `memberss`
  ADD PRIMARY KEY (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `medicalcases`
--
ALTER TABLE `medicalcases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `memberss`
--
ALTER TABLE `memberss`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
