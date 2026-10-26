-- ==========================================================
-- AIVORA - Intelligent Everyday Assistant Database
-- University Assignment: Advanced Web Development in PHP (Code 2010043347)
-- Compatible with MySQL 5.7+ / 8.0+ and SmarterASP.NET MySQL
-- ==========================================================

-- Create Database if not exists (Works directly for 1-click import in local XAMPP phpMyAdmin)
-- If importing on remote hosting with a fixed DB name (like SmarterASP.NET), select that DB in phpMyAdmin or adjust DB name below.
CREATE DATABASE IF NOT EXISTS `aivora_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aivora_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------
-- Table: users
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: admins
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: contacts
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: subscribers
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: features
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `icon` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: plans
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `plans`;
CREATE TABLE `plans` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `price_monthly` DECIMAL(6,2) NOT NULL,
  `price_yearly` DECIMAL(6,2) NOT NULL,
  `features_list` TEXT NOT NULL,
  `is_popular` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: testimonials
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(100) NOT NULL,
  `message` TEXT NOT NULL,
  `rating` TINYINT DEFAULT 5,
  `avatar` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: team
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(100) NOT NULL,
  `bio` TEXT NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: blog_posts
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `author` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: faq_responses (For Rule-Based Keyword Matching in Chat)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `faq_responses`;
CREATE TABLE `faq_responses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `keyword` VARCHAR(100) NOT NULL,
  `response` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Table: chat_logs
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `chat_logs`;
CREATE TABLE `chat_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `user_message` TEXT NOT NULL,
  `bot_response` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_chat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================================
-- SEED DATA
-- ==========================================================

-- Admin User: Username: admin | Password: Admin@123
INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$1D05PSQW.20mm9ee5f9cyuyEeNjHcCLmIEUcMpgqQSNlWB4BnoE7u', NOW());

-- Demo User: Email: demo@aivora.com | Password: User@123
INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Alex Mercer', 'demo@aivora.com', '$2y$10$HSFuXNtUdqtSDVH0Mml59ejOXPYAldsafaYZezQ6CS3vpW7niJNFq', NOW());

-- Features (6 items)
INSERT INTO `features` (`id`, `title`, `description`, `icon`, `created_at`) VALUES
(1, 'Conversational AI Chat', 'Engage in natural, context-aware conversations that understand nuanced questions and provide actionable insights in seconds.', 'bi-chat-dots', NOW()),
(2, 'Smart Writing Assistant', 'Draft polished emails, academic essays, blog posts, and marketing copy with tone control and instant grammar refinement.', 'bi-pen', NOW()),
(3, 'Code Copilot & Debugger', 'Write clean code, generate unit tests, explain complex algorithms, and debug syntax across 30+ programming languages.', 'bi-code-slash', NOW()),
(4, 'Deep Document Summarizer', 'Condense lengthy PDFs, research papers, meeting transcripts, and articles into executive summaries and key bullet points.', 'bi-file-text', NOW()),
(5, 'Multilingual Translator', 'Break communication barriers with real-time, culturally nuanced translations supporting over 85 languages worldwide.', 'bi-translate', NOW()),
(6, 'Voice & Speech Intelligence', 'Dictate thoughts seamlessly and listen to realistic neural speech playback for an effortless hands-free experience.', 'bi-mic', NOW());

-- Plans (3 items)
INSERT INTO `plans` (`id`, `name`, `price_monthly`, `price_yearly`, `features_list`, `is_popular`) VALUES
(1, 'Free Starter', 0.00, 0.00, '30 AI queries per day\r\nStandard response speed\r\nBasic writing & chat tools\r\nCommunity support\r\nWeb access preview', 0),
(2, 'Pro Creator', 19.00, 190.00, 'Unlimited AI conversations\r\nFast-track neural inference\r\nFull Code & Debug Copilot\r\nDocument & PDF summarization\r\nPriority email support\r\nExport chat transcripts', 1),
(3, 'Enterprise Scale', 49.00, 490.00, 'Everything in Pro plan\r\nCustom AI workflow fine-tuning\r\nTeam collaboration workspace\r\nDedicated API access\r\n99.9% uptime SLA\r\n24/7 dedicated account manager', 0);

-- Testimonials (4 items)
INSERT INTO `testimonials` (`id`, `name`, `role`, `message`, `rating`, `avatar`) VALUES
(1, 'Dr. Sarah Jenkins', 'Associate Professor, Computer Science', 'Aivora has transformed how my students approach literature reviews and coding labs. It clarifies complex concepts without just giving away answers.', 5, 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80'),
(2, 'Marcus Thorne', 'Senior Full Stack Engineer', 'The code assistant is remarkably precise with PHP PDO, Python scripts, and SQL schemas. It saves me at least 2 hours of boilerplate writing each day.', 5, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'),
(3, 'Elena Rostova', 'Content Marketing Strategist', 'Generating structured blog outlines and high-converting marketing hooks used to take hours. With Aivora, creative brainstorming is instantaneous.', 5, 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80'),
(4, 'David Chen', 'B.Tech IT Student', 'For university projects and exam prep, Aivora is like having a private tutor in my pocket. The document summarizer turns 40-page notes into digestible nuggets.', 5, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80');

-- Team (4 items)
INSERT INTO `team` (`id`, `name`, `role`, `bio`, `photo`) VALUES
(1, 'Dr. Aris Vance', 'Chief AI Scientist', 'PhD in Machine Learning from MIT. 12+ years pioneering transformer models and natural language processing architectures.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80'),
(2, 'Sophia Lin', 'Head of Product & UX', 'Passionate about human-computer interaction, cognitive accessibility, and crafting interfaces that feel like magic.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80'),
(3, 'Tariq Al-Mansoor', 'Lead Cloud Architect', 'Specializes in low-latency API distribution, enterprise data security, and scalable distributed database architectures.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80'),
(4, 'Maya Patel', 'AI Ethics & Research Lead', 'Dedicated to building trustworthy, unbiased AI systems that empower students and researchers around the globe.', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=400&q=80');

-- Blog Posts (5 items)
INSERT INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `image`, `author`, `created_at`) VALUES
(1, 'Mastering Prompt Engineering: A Beginner Guide for Students', 'mastering-prompt-engineering-students', 'Unlock the full potential of large language models with structured prompt patterns, role priming, and few-shot examples.', '<p>Prompt engineering is rapidly becoming one of the most essential digital literacy skills for students and engineers alike. When communicating with an AI model like Aivora, the specificity and structure of your prompt dictate the quality of the answer you receive.</p><h3>1. Role Priming</h3><p>Begin by defining the AI perspective. Instead of asking <em>"Explain recursion,"</em> try: <em>"Act as a university computer science professor explaining recursion to a first-year student using real-world analogies."</em> This primes the latent space for educational clarity.</p><h3>2. Delimiters and Formatting</h3><p>Use markdown tags, backticks, or triple quotes to separate your input code or essay from instructions. This eliminates confusion and reduces hallucination.</p><h3>3. Few-Shot Prompting</h3><p>Provide one or two examples of desired inputs and outputs. You will witness a dramatic jump in accuracy and formatting consistency.</p>', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80', 'Sophia Lin', NOW() - INTERVAL 10 DAY),

(2, 'How Generative AI is Reshaping Modern Software Engineering', 'generative-ai-reshaping-software-engineering', 'From boilerplate generation to automated unit testing, see how modern developers leverage AI copilot tools responsibly.', '<p>Software engineering is undergoing its biggest paradigm shift since the invention of high-level programming languages. AI copilots do not replace developers; they elevate them to architects.</p><h3>Accelerating Boilerplate Code</h3><p>Setting up CRUD endpoints, writing SQL migration scripts, or declaring schema interfaces can consume 30% of sprint time. AI assistants automate this grunt work within seconds.</p><h3>Explaining Legacy Codebases</h3><p>Junior engineers onboarding into complex repositories can paste unfamiliar functions and receive comprehensive, step-by-step breakdowns.</p><h3>Security and Verification</h3><p>Never run generated code blindly. Always review database queries for SQL injection safety, verify edge cases, and run automated unit tests.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=800&q=80', 'Marcus Thorne', NOW() - INTERVAL 8 DAY),

(3, 'The Ethics of Artificial Intelligence in Higher Education', 'ethics-of-artificial-intelligence-in-education', 'Navigating the balance between academic integrity, authentic learning, and embracing intelligent educational assistants.', '<p>As AI assistants become ubiquitous, academic institutions face a pivotal moment. Banning AI tools is as futile as banning handheld calculators was decades ago. Instead, educators must foster responsible adoption.</p><h3>Collaborative Learning vs. Cognitive Offloading</h3><p>When used as a private tutor—asking probing questions, offering analogies, and checking logic—AI accelerates comprehension. The danger arises when students surrender the cognitive effort required to synthesize ideas.</p><h3>Transparency and Attribution</h3><p>Students should be taught to disclose AI utilization openly and critically evaluate AI citations. Truthful academic work relies on human accountability.</p>', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80', 'Maya Patel', NOW() - INTERVAL 5 DAY),

(4, 'Deep Document Summarization: How Transformer Models Extract Knowledge', 'deep-document-summarization-transformers', 'Explore the mechanics behind abstractive vs. extractive summarization and how Aivora synthesizes dense research papers.', '<p>Academic research papers often span tens of pages filled with dense statistical jargon. Summarization algorithms allow researchers to scan literature in a fraction of the time.</p><h3>Extractive vs. Abstractive</h3><p>Early algorithms pulled key sentences verbatim (extractive). Modern transformer architectures like Aivora rewrite the essence of the document in fresh, coherent prose (abstractive), identifying the core hypothesis, methodology, and empirical findings.</p><h3>Handling Large Context Windows</h3><p>With chunking algorithms and hierarchical attention, modern summarizers digest entire textbooks without losing context or omitting crucial nuances.</p>', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=800&q=80', 'Dr. Aris Vance', NOW() - INTERVAL 3 DAY),

(5, 'Boost Your Everyday Productivity: 7 Practical AI Workflows', 'boost-everyday-productivity-practical-ai-workflows', 'Actionable routines to automate inbox zero, draft meeting briefs, organize weekly tasks, and enhance focus.', '<p>High-performing teams don’t work harder; they build intelligent leverage. Here are 7 actionable ways to integrate Aivora into your daily workflow:</p><ul><li><strong>Inbox Triage:</strong> Paste lengthy email chains to get a 3-bullet action item list.</li><li><strong>Meeting Debrief:</strong> Convert raw rough notes into formatted client deliverables.</li><li><strong>Regex & SQL Assistant:</strong> Describe your data query in plain English and let Aivora write the regex or SQL join.</li><li><strong>Grammar & Tone Polish:</strong> Elevate informal drafts into boardroom-ready memos.</li><li><strong>Study Flashcards:</strong> Turn lecture notes into instant question-and-answer flashcard decks.</li></ul>', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80', 'Sophia Lin', NOW() - INTERVAL 1 DAY);

-- FAQ Responses (15+ intelligent keyword matches for Rule-Based Chat Assistant)
INSERT INTO `faq_responses` (`id`, `keyword`, `response`) VALUES
(1, 'hello', 'Hello! I am Aivora, your intelligent everyday AI assistant. How can I help you today? You can ask me to write code, summarize text, draft an email, or explain a concept!'),
(2, 'hi', 'Hi there! Welcome to Aivora. What exciting task or question are we tackling today?'),
(3, 'who are you', 'I am Aivora, a state-of-the-art AI assistant crafted to empower students, developers, and professionals with lightning-fast reasoning, writing, coding, and productivity tools.'),
(4, 'what is aivora', 'Aivora is your personal AI companion. We offer conversational intelligence, code generation and debugging, document summarization, multilingual translation, and content drafting—all in one intuitive platform.'),
(5, 'pricing', 'Aivora offers three flexible plans: Free Starter ($0/mo with 30 queries/day), Pro Creator ($19/mo with unlimited AI & code assistant), and Enterprise Scale ($49/mo for teams with SLA & custom models). Check our Pricing page for more details!'),
(6, 'cost', 'You can start using Aivora 100% free with 30 queries daily! For power users, our Pro plan is $19/month (or $190/year with 2 months free).'),
(7, 'features', 'Aivora includes 6 core capabilities: 1) Conversational AI Chat, 2) Smart Writing Assistant, 3) Code Copilot & Debugger, 4) Deep Document Summarizer, 5) Multilingual Translator, and 6) Voice Intelligence.'),
(8, 'coding', 'I can generate, explain, and debug code across PHP, JavaScript, Python, C++, Java, SQL, and HTML/CSS. Paste your snippet or describe the function you want to create!'),
(9, 'php', 'PHP is an amazing server-side language! Aivora excels at writing secure PDO prepared statements, session management, REST APIs, and modern object-oriented or procedural PHP 8 scripts.'),
(10, 'write', 'Need an essay outline, marketing copy, executive email, or creative story? Tell me the topic, target audience, and desired tone, and I will compose it for you immediately!'),
(11, 'summarize', 'I can summarize articles, research papers, lecture notes, or transcripts. Simply paste your text or key points and specify whether you want a brief summary or key bullet points.'),
(12, 'translate', 'I support fluent, context-aware translations in over 85 languages including Spanish, French, German, Hindi, Mandarin, Arabic, and Japanese.'),
(13, 'contact', 'You can reach our human support team anytime via our Contact page or by emailing support@aivora.com. We respond within 24 business hours!'),
(14, 'security', 'We take security seriously! All data is encrypted in transit and at rest. We adhere to prepared statements to prevent SQL injection, CSRF tokens on all forms, and robust password hashing via bcrypt.'),
(15, 'university', 'Aivora was engineered for university students, educators, and researchers. It helps with study guides, code debugging, and academic synthesis while promoting ethical AI practices.'),
(16, 'help', 'I can assist you with multiple tasks! Try asking: "Explain recursion in PHP", "Give me pricing details", "Write a thank you email", or "Summarize modern AI trends".');

-- Demo Chat Logs
INSERT INTO `chat_logs` (`id`, `user_id`, `user_message`, `bot_response`, `created_at`) VALUES
(1, 1, 'Hello Aivora! What can you help me with?', 'Hello Alex! I am Aivora, your intelligent everyday AI assistant. How can I help you today? You can ask me to write code, summarize text, draft an email, or explain a concept!', NOW() - INTERVAL 2 HOUR),
(2, 1, 'Tell me about your coding assistance for PHP.', 'I can generate, explain, and debug code across PHP, JavaScript, Python, C++, Java, SQL, and HTML/CSS. Paste your snippet or describe the function you want to create!', NOW() - INTERVAL 1 HOUR);
