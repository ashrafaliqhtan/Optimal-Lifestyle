




-- --------------------------------------------------------

--
-- Table structure for table `exercises_default`
--


--
-- Dumping data for table `exercises_default`
--


-- --------------------------------------------------------

--
-- Table structure for table `friends_follow`
--

CREATE TABLE `friends_follow` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `friends_follow`
--

INSERT INTO `friends_follow` (`follow_id`, `follower_id`, `followed_user_id`) VALUES
(3, 3, 2),
(43, 31, 2),
(44, 31, 3),
(45, 31, 7),
(46, 31, 8),
(47, 31, 15),
(48, 31, 25),
(49, 31, 26),
(50, 3, 25),
(52, 3, 7),
(53, 7, 2),
(54, 7, 25),
(56, 32, 2),
(57, 32, 7),
(59, 8, 2),
(60, 8, 3),
(61, 8, 7),
(62, 8, 15),
(63, 8, 25),
(64, 8, 26),
(65, 8, 31),
(66, 31, 2),
(67, 2, 3),
(68, 2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `friends_post`
--

CREATE TABLE `friends_post` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `like_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `friends_post`
--



-- --------------------------------------------------------

--
-- Table structure for table `profile_img`
--

CREATE TABLE `profile_img` (
  `id` int(11) NOT NULL,
  `img_name` varchar(255) NOT NULL,
  `img_data` mediumblob NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `profile_img`
--



-- --------------------------------------------------------

--
-- Table structure for table `usersmanage`
--

CREATE TABLE `usersmanage` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user` varchar(20) NOT NULL,
  `gender` varchar(2) NOT NULL,
  `initial_weight` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `kcal_objective` int(11) NOT NULL DEFAULT 600,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_type` varchar(11) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `usersmanage`
--

INSERT INTO `usersmanage` (`id`, `name`, `email`, `password`, `user`, `gender`, `initial_weight`, `height`, `kcal_objective`, `date`, `user_type`) VALUES
(1, 'Administrator', 'admin@admin.com', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'm', 1, 1, 1, '2025-06-01 00:00:00', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `weight`
--

CREATE TABLE `weight` (
  `weight` int(5) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `weight`
--

INSERT INTO `weight` (`weight`, `date`, `id`) VALUES
(72, '2025-05-19 00:00:00', 2),
(74, '2025-05-21 23:37:22', 2),
(77, '2025-05-23 23:37:24', 2),
(80, '2025-05-25 23:37:27', 2),
(103, '2025-05-27 23:37:30', 2),
(89, '2025-05-29 10:03:44', 2),
(85, '2025-05-31 10:03:48', 2),
(45, '2025-05-31 10:58:42', 8),
(69, '2025-06-10 16:20:17', 2),
(74, '2025-06-14 10:26:35', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`user_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `exercises_default`
--
ALTER TABLE `exercises_default`
  ADD PRIMARY KEY (`exercise_id`);

--
-- Indexes for table `friends_follow`
--
ALTER TABLE `friends_follow`
  ADD PRIMARY KEY (`follow_id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `followed_user_id` (`followed_user_id`);

--
-- Indexes for table `friends_post`
--
ALTER TABLE `friends_post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profile_img`
--
ALTER TABLE `profile_img`
  ADD UNIQUE KEY `img_name` (`img_name`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `usersmanage`
--
ALTER TABLE `usersmanage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user` (`user`),
  ADD KEY `initial_weight` (`initial_weight`);

--
-- Indexes for table `weight`
--
ALTER TABLE `weight`
  ADD UNIQUE KEY `date` (`date`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `exercises_default`
--
ALTER TABLE `exercises_default`
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `friends_follow`
--
ALTER TABLE `friends_follow`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `friends_post`
--
ALTER TABLE `friends_post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `usersmanage`
--
ALTER TABLE `usersmanage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises_default` (`exercise_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friends_post`
--
ALTER TABLE `friends_post`
  ADD CONSTRAINT `friends_post_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friends_post_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profile_img`
--
ALTER TABLE `profile_img`
  ADD CONSTRAINT `profile_img_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `weight`
--
ALTER TABLE `weight`
  ADD CONSTRAINT `weight_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
