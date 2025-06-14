



--

-- Constraints for table `chat_sessions`

--

ALTER TABLE `chat_sessions`

ADD CONSTRAINT `chat_sessions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,

ADD CONSTRAINT `chat_sessions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

MySQL said: 

#3780 - Referencing column 'customer_id' and referenced column 'id' in foreign key constraint 'chat_sessions_ibfk_1' are incompatible.



--

-- Table structure for table `chat_sessions`

--



CREATE TABLE `chat_sessions` (

  `session_id` varchar(255) NOT NULL,

  `customer_id` int NOT NULL,

  `user_id` int DEFAULT NULL,

  `status` enum('open','closed','pending') DEFAULT 'pending',

  `started_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  `closed_at` timestamp NULL DEFAULT NULL,

  `last_customer_activity` timestamp NULL DEFAULT NULL,

  `customer_typing` tinyint(1) DEFAULT '0',

  `user_typing` tinyint(1) DEFAULT '0'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



--

-- Dumping data for table `chat_sessions`

--



INSERT INTO `chat_sessions` (`session_id`, `customer_id`, `user_id`, `status`, `started_at`, `closed_at`, `last_customer_activity`, `customer_typing`, `user_typing`) VALUES

('1daf919f-887e-46ab-a008-63f8a39ca609', 2, 1, 'pending', '2025-06-09 07:18:07', NULL, '2025-06-10 20:03:10', 0, 0),

('301c96c3-f85b-4cc8-9457-9fbe196a2864', 2, NULL, 'closed', '2025-06-10 21:00:18', NULL, NULL, 0, 0),

('847a8e6c-e6f1-4238-9361-7970b563f41b', 3, NULL, 'closed', '2025-06-10 21:34:24', NULL, NULL, 0, 0);



-- --------------------------------------------------------



--

-- Table structure for table `messages`

--



CREATE TABLE `messages` (

  `id` int NOT NULL,

  `chat_session_id` varchar(255) NOT NULL,

  `sender_id` int NOT NULL,

  `sender_type` enum('customer','user','bot') NOT NULL,

  `message_text` text NOT NULL,

  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  `is_read_by_user` tinyint(1) DEFAULT '0',

  `is_read_by_customer` tinyint(1) DEFAULT '0'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



--

-- Dumping data for table `messages`

--



--

-- Indexes for table `chat_sessions`

--

ALTER TABLE `chat_sessions`

  ADD PRIMARY KEY (`session_id`),

  ADD KEY `customer_id` (`customer_id`),

  ADD KEY `user_id` (`user_id`);



--

-- Indexes for table `messages`

--

ALTER TABLE `messages`

  ADD PRIMARY KEY (`id`),

  ADD KEY `sender_id` (`sender_id`);



--

-- AUTO_INCREMENT for table `messages`

--

ALTER TABLE `messages`

  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;



--

-- Constraints for table `chat_sessions`

--

ALTER TABLE `chat_sessions`

  ADD CONSTRAINT `chat_sessions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,

  ADD CONSTRAINT `chat_sessions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;



--

-- Constraints for table `messages`

--

ALTER TABLE `messages`

  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

COMMIT;