CREATE TABLE `Articles` (
  `article_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `Articles`
--



CREATE TABLE `Article_Views` (
  `view_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `view_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `Article_Views`
--



CREATE TABLE `CalorieCalculator` (
  `calorie_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) DEFAULT NULL,
  `calorie_amount` int(11) NOT NULL,
  `food_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `CalorieCalculator`
--



CREATE TABLE `Calories` (
  `calorie_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `calorie_count` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `Calories`
--



CREATE TABLE `Exercise` (
  `exercise_id` int(11) NOT NULL,
  `exercise_type` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `time` time NOT NULL,
  `fitness_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- إرجاع أو استيراد بيانات الجدول `Exercise`
--



CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `people` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_done` date NOT NULL,
  `total_time` time NOT NULL,
  `total_kcal` int(5) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- بنية الجدول `exercises_backup`
--

CREATE TABLE `exercises_backup` (
  `id` int(11) NOT NULL DEFAULT '0',
  `exercise_id` int(11) NOT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `place` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `people` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_done` date NOT NULL,
  `total_time` time NOT NULL,
  `total_kcal` int(5) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `exercises_default` (
  `exercise_type` varchar(100) NOT NULL,
  `img_data` mediumblob NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `img_name` varchar(255) NOT NULL,
  `kcal_hour` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `exercises_temp` (
  `exercise_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `place` varchar(50) DEFAULT NULL,
  `people` varchar(50) DEFAULT NULL,
  `date_done` date DEFAULT NULL,
  `total_time` time DEFAULT NULL,
  `total_kcal` float DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `usersmanage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(20) NOT NULL,
  `gender` ENUM('M', 'F', 'NB', 'O') NOT NULL,
  `initial_weight` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `kcal_objective` int(11) NOT NULL DEFAULT 2000,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_type` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `usersmanage` (`name`, `email`, `password`, `username`, `gender`, `initial_weight`, `height`, `user_type`) VALUES
('Admin', 'admin@lifestyle.com', '$2y$10$VWj8v.M6n.bddgCXZTHsE.DdXqoPkQQE2Ed/4x7k4ShKjuasNMmD2', 'Admin', 'F', 88, 180, 'admin');




INSERT INTO `usersmanage` (`id`, `name`, `email`, `password`, `user`, `gender`, `initial_weight`, `height`, `kcal_objective`, `date`, `user_type`) VALUES
(3, 'Admin', 'admin@lifestyle.com', '$2y$10$VWj8v.M6n.bddgCXZTHsE.DdXqoPkQQE2Ed/4x7k4ShKjuasNMmD2', 'Admin', 'f', 88, 180, 600, '2025-03-18 01:56:23', 'admin');




CREATE TABLE `FitnessRecords` (
  `fitness_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `exercises` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `Food` (
  `food_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `calorie_amount` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `friends_follow` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `friends_post` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `like_count` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- بنية الجدول `profile_img`
--

CREATE TABLE `profile_img` (
  `id` int(11) NOT NULL,
  `img_name` varchar(255) NOT NULL,
  `img_data` mediumblob NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `UserProfiles` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `address` text,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `calorie_id` int(11) DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `usersmanage` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user` varchar(20) NOT NULL,
  `gender` varchar(2) NOT NULL,
  `initial_weight` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `kcal_objective` int(11) NOT NULL DEFAULT '600',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_type` varchar(11) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `weight` (
  `weight` int(5) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



ALTER TABLE `Articles`
  ADD PRIMARY KEY (`article_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `Article_Views`
--
ALTER TABLE `Article_Views`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `CalorieCalculator`
--
ALTER TABLE `CalorieCalculator`
  ADD PRIMARY KEY (`calorie_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `Calories`
--
ALTER TABLE `Calories`
  ADD PRIMARY KEY (`calorie_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Exercise`
--
ALTER TABLE `Exercise`
  ADD PRIMARY KEY (`exercise_id`),
  ADD KEY `fitness_id` (`fitness_id`);

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
-- Indexes for table `FitnessRecords`
--
ALTER TABLE `FitnessRecords`
  ADD PRIMARY KEY (`fitness_id`);

--
-- Indexes for table `Food`
--
ALTER TABLE `Food`
  ADD PRIMARY KEY (`food_id`);

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
-- Indexes for table `UserProfiles`
--
ALTER TABLE `UserProfiles`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `usersmanage`
--
ALTER TABLE `usersmanage`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `Articles`
--
ALTER TABLE `Articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Article_Views`
--
ALTER TABLE `Article_Views`
  MODIFY `view_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `CalorieCalculator`
--
ALTER TABLE `CalorieCalculator`
  MODIFY `calorie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Calories`
--
ALTER TABLE `Calories`
  MODIFY `calorie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Exercise`
--
ALTER TABLE `Exercise`
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercises_default`
--
ALTER TABLE `exercises_default`
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `FitnessRecords`
--
ALTER TABLE `FitnessRecords`
  MODIFY `fitness_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Food`
--
ALTER TABLE `Food`
  MODIFY `food_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `friends_follow`
--
ALTER TABLE `friends_follow`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `friends_post`
--
ALTER TABLE `friends_post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `UserProfiles`
--
ALTER TABLE `UserProfiles`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usersmanage`
--
ALTER TABLE `usersmanage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;