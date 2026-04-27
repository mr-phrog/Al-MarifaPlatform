-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2024 at 09:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `course_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `user_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`user_id`, `playlist_id`) VALUES
(8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `book_image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(100) NOT NULL,
  `num_pages` int(11) NOT NULL,
  `book_file` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `tutor_id`, `title`, `book_image`, `description`, `author`, `num_pages`, `book_file`, `upload_date`, `category_id`) VALUES
(52, 9, 'HTML5', 'mpu6KQNMlxwiQphbRY4j.png', 'HTML لغة ترميز النص الفائق ‏، هي لغة ترميز تستخدم في إنشاء وتصميم صفحات ومواقع الويب، وتعتبر هذه اللّغة من أقدم اللّغات وأوسعها استخداما في تصميم صفحات الويب.', 'اسامة محمد', 39, 'LnjRgbEVvJXAhK3TkKPb.pdf', '2024-09-15 05:21:06', 7),
(53, 9, 'العملات الرقمية - دراسة اقتصادية شرعية-', 'fHtzBx0AsIIriMVwZhvO.png', 'دراسة اقتصادية قانونية شرعية مركّزة حول العملات الرقمية، مع وصف تقني شامل لها، وبيانٍ لأهم الأحكام والضوابط الشرعية الحاكمة لهذا المجال.', 'د. أبونصر شخار', 154, 'F1QstQq42wslv5kbFAmr.pdf', '2024-09-15 05:32:11', 5);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name_ar`, `name_en`) VALUES
(1, 'التطوير', 'Development'),
(2, 'تنمية بشرية', 'Human Development'),
(3, 'التصميم', 'Design'),
(4, 'الاعمال', 'Business'),
(5, 'التسويق', 'Marketing'),
(6, 'لغات', 'Languages'),
(7, 'البرمجيات', 'Software'),
(8, 'العلوم', 'Science');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `content_id`, `user_id`, `tutor_id`, `comment`, `date`) VALUES
(67, 86, 8, 9, 'شرح ممتاز!', '2024-09-15'),
(81, 86, 8, 9, 'افضل دورة تعليمية لتطوير الالعاب 3>', '2024-09-15');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `video` varchar(100) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `tutor_id`, `playlist_id`, `title`, `description`, `video`, `thumb`, `date`, `status`) VALUES
(86, 9, 7, '#1 what is Godot', 'مقدمة بسيطة عن محرك (Godot)', 'jYx4vE15CemVNqai8Ujm.mp4', 'LSqLV0FvdJJ8xtSn4pmL.png', '2024-09-15', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `user_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`user_id`, `tutor_id`, `content_id`) VALUES
(8, 9, 86);

-- --------------------------------------------------------

--
-- Table structure for table `mcq`
--

CREATE TABLE `mcq` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mcq`
--

INSERT INTO `mcq` (`id`, `content_id`, `question`) VALUES
(57, 86, ' ما هو محرك Godot؟'),
(58, 86, 'ما هي لغة البرمجة الرئيسية المستخدمة في محرك Godot؟'),
(59, 86, ' أي من هذه الخيارات هو النظام الذي يستخدمه Godot للتعامل مع المشاهد والأجسام؟'),
(60, 86, 'ما هي التقنية التي يستخدمها Godot لمعالجة الرسومات ثلاثية الأبعاد؟');

-- --------------------------------------------------------

--
-- Table structure for table `mcq_options`
--

CREATE TABLE `mcq_options` (
  `id` int(11) NOT NULL,
  `mcq_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mcq_options`
--

INSERT INTO `mcq_options` (`id`, `mcq_id`, `option_text`, `is_correct`) VALUES
(129, 57, 'محرك برمجة للذكاء الاصطناعي', 0),
(130, 57, 'محرك ألعاب مفتوح المصدر', 1),
(131, 57, 'برنامج لتحرير الفيديو', 0),
(132, 57, 'منصة تطوير تطبيقات الهاتف', 0),
(133, 58, '++C', 0),
(134, 58, 'Python', 0),
(135, 58, 'GDScript', 1),
(136, 58, 'Java', 0),
(137, 59, 'نظام العقد (Nodes)', 1),
(138, 59, 'نظام الشرائح (Layers)', 0),
(139, 59, 'نظام المصفوفات (Matrices)', 0),
(140, 59, ' نظام المجموعات (Groups)', 0),
(141, 60, 'DirectX', 0),
(142, 60, 'OpenGL', 0),
(143, 60, 'Vulkan', 1),
(144, 60, 'Metal', 0);

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive',
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `tutor_id`, `title`, `description`, `thumb`, `date`, `status`, `category_id`) VALUES
(4, 9, 'HTML', 'تعلم لغة (HTML) لهيكلة المواقع الاكترونية ', 'VsqV4Kry0dYCOfzIiten.png', '2024-09-15', 'active', 7),
(7, 9, 'Godot 2D', 'تعلم تطوير الالعاب في محرك جودوت مفتوح المصدر و المجاني من الصفر الى الاحتراف', 'FXREyOMNNeAdgM4dveWq.png', '2024-09-15', 'active', 1),
(8, 9, 'فن الثقة بالنفس', 'محتويات الدورة: مفهوم الثقة بالنفس، تعرّف على نفسك \r\nوحلّل أفكارك، علاج ضعف الثقة بالنفس، ممارسة الثقة بالنفس على أرض الواقع.', 'mBhvFHTXVl8nj8hnAe76.jpg', '2024-09-15', 'deactive', 2),
(9, 9, 'تعلم تصميم تجربة المستخدم/واجهة المستخدم', 'تعلّم أساسيات البحث والتصميم المتعلقين بتجربة المستخدم/واجهة المستخدم (UI/UX). الانغماس في عملية تجربة المستخدم للتعرف على المشكلات وتكرار واختبار التصاميم لإيجاد الحلول المناسبة.', 'Ey1C8i77iSxkbA2bUCOp.jpg', '2024-09-15', 'active', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `profession` varchar(50) NOT NULL,
  `profession_ar` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`id`, `name`, `profession`, `profession_ar`, `email`, `password`, `image`) VALUES
(9, 'ASA', 'engineer', 'مهندس', 'mrphorgmmm@gmail.com', '601f1889667efaebb33b8c12572835da3f027f78', 'bGRgQr5ytMpWlGXfS37T.jpg'),
(10, 'Esmail', 'developer', '', 'esmail@gmail.com', '601f1889667efaebb33b8c12572835da3f027f78', '9eGyd2dvo0xgWYz7y4h3.jpg'),
(11, 'Farooq', 'teacher', '', 'farooq@gmail.com', '601f1889667efaebb33b8c12572835da3f027f78', 'VFiSjJIlQQ4CpuHfV8F4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `image`) VALUES
(1, 'Ahmed Ali', 'alazokari@hotmail.co', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'o79p5UOsW7mmt018Ka3V.png'),
(2, 'Khaled', 'test1@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'G1QAS8y2NEMGFhxj3NKy.png'),
(6, 'أحمد', 'user@gmail.com', '7ab395624ef7fa7cdecb7a6a4bebfae20c81a2e4', 'vgueOgJTDNiEMActzVdN.jpg'),
(7, 'Alaa', 'alaa8@gmail.com', '7ab395624ef7fa7cdecb7a6a4bebfae20c81a2e4', 'pTaANkPpQZ8tNrDfzqUp.webp'),
(8, 'Mohammed', 'mrphorgmmm@gmail.com', '601f1889667efaebb33b8c12572835da3f027f78', 'UQ57ZoGIKqulfBNqoxSA.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user_chats`
--

CREATE TABLE `user_chats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `message_type` enum('incoming','outgoing') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_chats`
--

INSERT INTO `user_chats` (`id`, `user_id`, `message`, `message_type`, `timestamp`) VALUES
(493, 6, 'مرحبًا', 'outgoing', '2024-09-02 19:34:13'),
(539, 8, 'اشرح النظرية النسبية كانني طفل لديه 5 سنوات', 'outgoing', '2024-09-15 06:32:05'),
(540, 8, 'تخيل أنك تلعب بالسيارة الصغيرة في غرفتك. عندما تدفعها بسرعة، تبدو لك أسرع من عندما تدفعها ببطء، صحيح؟&lt;br&gt;&lt;br&gt;النظرية النسبية تقول أن الوقت والمسافة ليسا ثابتين مثلما يبدو لنا. تمامًا مثل سرعتك في اللعب بالسيارة، كل شيء نسبي!&lt;br&gt;&lt;br&gt;فإذا كنت تركض بسرعة كبيرة جدًا، سيبدو الوقت أبطأ بالنسبة لك من شخص واقف! 😲&lt;br&gt;&lt;br&gt;وإذا سافرت بسرعة الضوء، سيصبح الوقت متوقفًا تمامًا! 🚀&lt;br&gt;&lt;br&gt;لكن لا تقلق، نحن لا نسافر بسرعة الضوء بعد! 😅&lt;br&gt;&lt;br&gt;النظرية النسبية تقول أيضًا أن الضوء هو أسرع شيء في الكون. 💫&lt;br&gt;&lt;br&gt;إنها نظرية غريبة بعض الشيء، لكنها تساعد العلماء على فهم الكون بشكل أفضل. 😊&lt;br&gt;', 'incoming', '2024-09-15 06:32:17'),
(541, 8, 'كيف يمكن ان تفيدني واجهة المستخدم في تحسين جودة نطبيق الجوال الخاص بي اجب بثلاثه اسطر كحد اقصى', 'outgoing', '2024-09-15 06:37:37'),
(542, 8, 'واجهات المستخدم الجيدة تجعل تطبيقك سهل الاستخدام وواضحًا، مما يحسّن تجربة المستخدم ويجعله أكثر انجذابًا. التصميم المريح يقلّل من خطأ المستخدم ويجعل التطبيق أكثر فاعلية. واجهات المستخدم الفعالة تزيد من تقييمات التطبيق وتجعله أكثر انتشارًا.&lt;br&gt;', 'incoming', '2024-09-15 06:37:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD KEY `fk_userbmid` (`user_id`),
  ADD KEY `fk_playbmid` (`playlist_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_id` (`category_id`),
  ADD KEY `fk_tuterbid` (`tutor_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_councid` (`content_id`),
  ADD KEY `fk_usercid` (`user_id`),
  ADD KEY `fk_totercid` (`tutor_id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_playid` (`playlist_id`),
  ADD KEY `fk_tuterid` (`tutor_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD KEY `fk_likeuid` (`user_id`),
  ADD KEY `fk_liketid` (`tutor_id`),
  ADD KEY `fk_likecid` (`content_id`);

--
-- Indexes for table `mcq`
--
ALTER TABLE `mcq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `mcq_options`
--
ALTER TABLE `mcq_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mcq_id` (`mcq_id`);

--
-- Indexes for table `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category` (`category_id`),
  ADD KEY `fk_tutorsid` (`tutor_id`);

--
-- Indexes for table `tutors`
--
ALTER TABLE `tutors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_chats`
--
ALTER TABLE `user_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_chats_ibf` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `mcq`
--
ALTER TABLE `mcq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `mcq_options`
--
ALTER TABLE `mcq_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `playlist`
--
ALTER TABLE `playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tutors`
--
ALTER TABLE `tutors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_chats`
--
ALTER TABLE `user_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=543;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `fk_playbmid` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userbmid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tuterbid` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_councid` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_totercid` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usercid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `fk_playid` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tuterid` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `fk_likecid` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_liketid` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_likeuid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mcq`
--
ALTER TABLE `mcq`
  ADD CONSTRAINT `mcq_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mcq_options`
--
ALTER TABLE `mcq_options`
  ADD CONSTRAINT `mcq_options_ibfk_1` FOREIGN KEY (`mcq_id`) REFERENCES `mcq` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist`
--
ALTER TABLE `playlist`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tutorsid` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_chats`
--
ALTER TABLE `user_chats`
  ADD CONSTRAINT `user_chats_ibf` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
