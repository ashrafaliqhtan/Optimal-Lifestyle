
CREATE TABLE `usersmanage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(20) NOT NULL,
  `gender` enum('M','F','NB','O') NOT NULL,
  `initial_weight` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `kcal_objective` int(11) NOT NULL DEFAULT '2000',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_type` enum('user','admin') NOT NULL DEFAULT 'user',
  `profile_photo` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` enum('active','suspended','deleted') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for Articles
CREATE TABLE `Articles` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`),
  KEY `author_id` (`author_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for Article_Views
CREATE TABLE `Article_Views` (
  `view_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `view_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`view_id`),
  KEY `article_id` (`article_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for CalorieCalculator
CREATE TABLE `CalorieCalculator` (
  `calorie_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) DEFAULT NULL,
  `calorie_amount` int(11) NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `consumed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `meal_type` enum('breakfast','lunch','dinner','snack') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`calorie_id`),
  KEY `user_id` (`user_id`),
  KEY `food_id` (`food_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for Calories
CREATE TABLE `Calories` (
  `calorie_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `calorie_count` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `goal_calories` int(11) DEFAULT NULL,
  PRIMARY KEY (`calorie_id`),
  KEY `user_id` (`user_id`),
  UNIQUE KEY `user_date` (`user_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for Exercise
CREATE TABLE `Exercise` (
  `exercise_id` int(11) NOT NULL AUTO_INCREMENT,
  `exercise_type` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `time` time NOT NULL,
  `fitness_id` int(11) NOT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`exercise_id`),
  KEY `fitness_id` (`fitness_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for exercises
CREATE TABLE `exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exercise_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `people` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_done` date NOT NULL,
  `total_time` time NOT NULL,
  `total_kcal` int(5) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `exercise_id` (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for exercises_default (modified to make img_data nullable)
CREATE TABLE `exercises_default` (
  `exercise_type` varchar(100) NOT NULL,
  `img_data` mediumblob NULL,
  `exercise_id` int(11) NOT NULL AUTO_INCREMENT,
  `img_name` varchar(255) NOT NULL,
  `kcal_hour` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`exercise_id`),
  UNIQUE KEY `exercise_type` (`exercise_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for FitnessRecords
CREATE TABLE `FitnessRecords` (
  `fitness_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `exercises` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `workout_name` varchar(255) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  PRIMARY KEY (`fitness_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for Food
CREATE TABLE `Food` (
  `food_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `calorie_amount` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `food_group` varchar(100) DEFAULT NULL,
  `serving_size` varchar(50) DEFAULT NULL,
  `protein` int(11) DEFAULT NULL,
  `carbs` int(11) DEFAULT NULL,
  `fat` int(11) DEFAULT NULL,
  PRIMARY KEY (`food_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for friends_follow
CREATE TABLE `friends_follow` (
  `follow_id` int(11) NOT NULL AUTO_INCREMENT,
  `follower_id` int(11) NOT NULL,
  `followed_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`follow_id`),
  UNIQUE KEY `follower_followed` (`follower_id`,`followed_user_id`),
  KEY `followed_user_id` (`followed_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for friends_post
CREATE TABLE `friends_post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `like_count` int(11) NOT NULL DEFAULT 0,
  `comment_count` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for meal_plans
CREATE TABLE `meal_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for meal_plan_days
CREATE TABLE `meal_plan_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meal_plan_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `day_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meal_plan_id` (`meal_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for meal_plan_meals
CREATE TABLE `meal_plan_meals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day_id` int(11) NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `meal_time` enum('breakfast','lunch','dinner','snack') NOT NULL,
  `calories` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `day_id` (`day_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for user_meal_plans
CREATE TABLE `user_meal_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meal_plan_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `meal_plan_id` (`meal_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for activity_logs
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `additional_data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_type` (`activity_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for generated_reports
CREATE TABLE `generated_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `report_format` varchar(10) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `parameters` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for scheduled_reports
CREATE TABLE `scheduled_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `report_type` varchar(100) NOT NULL,
  `frequency` enum('daily','weekly','monthly','quarterly') NOT NULL,
  `day_of_week` varchar(10) DEFAULT NULL,
  `day_of_month` int(2) DEFAULT NULL,
  `report_format` varchar(10) NOT NULL,
  `recipients` text NOT NULL,
  `start_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_run` timestamp NULL DEFAULT NULL,
  `next_run` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `next_run` (`next_run`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for weight
CREATE TABLE `weight` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `weight` decimal(5,1) NOT NULL,
  `date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_date` (`user_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for categories
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for media
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for notifications
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `related_id` int(11) DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for post_comments
CREATE TABLE `post_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for post_likes
CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_user` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for user_goals
CREATE TABLE `user_goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `goal_type` enum('weight_loss','muscle_gain','endurance','maintenance') NOT NULL,
  `target_value` decimal(5,1) DEFAULT NULL,
  `current_value` decimal(5,1) DEFAULT NULL,
  `start_date` date NOT NULL,
  `target_date` date NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for user_sessions
CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `user_id` (`user_id`),
  KEY `last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for water_intake
CREATE TABLE `water_intake` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL COMMENT 'Amount in ml',
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_date` (`user_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- 1. إنشاء جدول التدريبات أولاً (إذا لم يكن موجوداً)
CREATE TABLE IF NOT EXISTS `workouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. إنشاء جدول الخطط التدريبية
CREATE TABLE IF NOT EXISTS `training_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `duration_weeks` int(11) NOT NULL DEFAULT 4,
  `goal` enum('weight_loss','muscle_gain','endurance','general_fitness') NOT NULL DEFAULT 'general_fitness',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. الآن إنشاء جدول العلاقة بين الخطط والتدريبات
CREATE TABLE IF NOT EXISTS `plan_workouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `day_of_week` tinyint(1) NOT NULL COMMENT '1-7 for Monday-Sunday',
  `week_number` int(11) NOT NULL COMMENT 'Week in the plan',
  `order_in_day` int(11) NOT NULL DEFAULT 0 COMMENT 'Order of workout in day',
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_workout` (`plan_id`,`workout_id`,`day_of_week`,`week_number`),
  KEY `workout_id` (`workout_id`),
  CONSTRAINT `plan_workouts_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `training_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plan_workouts_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الفئات
CREATE TABLE `recipe_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الوصفات
CREATE TABLE `recipes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `prep_time` int(11) DEFAULT NULL COMMENT 'in minutes',
  `cook_time` int(11) DEFAULT NULL COMMENT 'in minutes',
  `servings` int(11) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL COMMENT 'per serving',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `recipe_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إضافة بعض الفئات الأساسية
INSERT INTO `recipe_categories` (`name`, `description`) VALUES
('Main Dishes', 'Primary meal dishes'),
('Desserts', 'Sweet treats and desserts'),
('Appetizers', 'Starters and snacks'),
('Salads', 'Various salad recipes'),
('Drinks', 'Beverages and drinks');

-- إضافة بعض الوصفات كمثال
INSERT INTO `recipes` (`name`, `category_id`, `description`, `ingredients`, `instructions`, `prep_time`, `cook_time`, `servings`, `calories`) VALUES
('Spaghetti Carbonara', 1, 'Classic Italian pasta dish', 'Spaghetti, Eggs, Pancetta, Parmesan, Black Pepper', 'Cook pasta, fry pancetta, mix with eggs and cheese', 10, 15, 4, 600),
('Chocolate Cake', 2, 'Rich chocolate dessert', 'Flour, Sugar, Cocoa, Eggs, Butter, Milk', 'Mix ingredients, bake at 180C for 30 mins', 20, 30, 8, 350);


-- Foreign key constraints
ALTER TABLE `Articles`
  ADD CONSTRAINT `Articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `usersmanage` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `Articles_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

ALTER TABLE `Article_Views`
  ADD CONSTRAINT `Article_Views_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `Articles` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Article_Views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE SET NULL;

ALTER TABLE `CalorieCalculator`
  ADD CONSTRAINT `CalorieCalculator_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `CalorieCalculator_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `Food` (`food_id`) ON DELETE SET NULL;

ALTER TABLE `Calories`
  ADD CONSTRAINT `Calories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `Exercise`
  ADD CONSTRAINT `Exercise_ibfk_1` FOREIGN KEY (`fitness_id`) REFERENCES `FitnessRecords` (`fitness_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Exercise_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `FitnessRecords`
  ADD CONSTRAINT `FitnessRecords_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `Food`
  ADD CONSTRAINT `Food_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `friends_follow`
  ADD CONSTRAINT `friends_follow_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_follow_ibfk_2` FOREIGN KEY (`followed_user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `friends_post`
  ADD CONSTRAINT `friends_post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_post_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `meal_plans`
  ADD CONSTRAINT `meal_plans_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `meal_plan_days`
  ADD CONSTRAINT `meal_plan_days_ibfk_1` FOREIGN KEY (`meal_plan_id`) REFERENCES `meal_plans` (`id`) ON DELETE CASCADE;

ALTER TABLE `meal_plan_meals`
  ADD CONSTRAINT `meal_plan_meals_ibfk_1` FOREIGN KEY (`day_id`) REFERENCES `meal_plan_days` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_meal_plans`
  ADD CONSTRAINT `user_meal_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_meal_plans_ibfk_2` FOREIGN KEY (`meal_plan_id`) REFERENCES `meal_plans` (`id`) ON DELETE CASCADE;

ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE SET NULL;

ALTER TABLE `generated_reports`
  ADD CONSTRAINT `generated_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `scheduled_reports`
  ADD CONSTRAINT `scheduled_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `weight`
  ADD CONSTRAINT `weight_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `friends_post` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `friends_post` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_goals`
  ADD CONSTRAINT `user_goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

ALTER TABLE `water_intake`
  ADD CONSTRAINT `water_intake_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usersmanage` (`id`) ON DELETE CASCADE;

-- Initial data
INSERT INTO `usersmanage` (`name`, `email`, `password`, `username`, `gender`, `initial_weight`, `height`, `kcal_objective`, `user_type`) VALUES
('Admin', 'admin@lifestyle.com', '$2y$10$VWj8v.M6n.bddgCXZTHsE.DdXqoPkQQE2Ed/4x7k4ShKjuasNMmD2', 'admin', 'M', 80, 180, 2500, 'admin');

INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Nutrition', 'nutrition', 'Articles about healthy eating and diet'),
('Fitness', 'fitness', 'Workout tips and exercise guides'),
('Wellness', 'wellness', 'Mental health and general wellbeing'),
('Recipes', 'recipes', 'Healthy recipes and meal ideas');

-- Corrected exercises_default data with NULL img_data

-- Users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User roles table
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 for system roles that cannot be deleted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissions table
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_name` (`permission_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Role permissions junction table
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permission` (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity logs table


-- Insert default system roles
INSERT INTO `user_roles` (`role_name`, `description`, `is_system`) VALUES
('Super Admin', 'Has full access to all system features', 1),
('Admin', 'Can manage most system features', 1),
('Editor', 'Can create and edit content', 1),
('User', 'Regular user with basic access', 1);

-- Insert common permissions
INSERT INTO `permissions` (`permission_name`, `description`) VALUES
('view_dashboard', 'Access to the admin dashboard'),
('manage_users', 'Create, edit and delete users'),
('manage_roles', 'Create, edit and delete roles'),
('manage_content', 'Create, edit and delete content'),
('manage_settings', 'Change system settings'),
('view_reports', 'View system reports and analytics'),
('export_data', 'Export system data');

-- Assign all permissions to Super Admin role
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id 
FROM user_roles r, permissions p 
WHERE r.role_name = 'Super Admin';

-- Assign basic permissions to Admin role
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id 
FROM user_roles r, permissions p 
WHERE r.role_name = 'Admin' 
AND p.permission_name IN ('view_dashboard', 'manage_users', 'manage_content', 'view_reports');

-- Assign content permissions to Editor role
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id 
FROM user_roles r, permissions p 
WHERE r.role_name = 'Editor' 
AND p.permission_name IN ('view_dashboard', 'manage_content');

-- Assign basic permission to User role
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id 
FROM user_roles r, permissions p 
WHERE r.role_name = 'User' 
AND p.permission_name = 'view_dashboard';

