-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 24, 2025 at 12:22 PM
-- Server version: 5.7.44
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_nutrify_db`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_coupons`
-- (See below for the actual view)
--
CREATE TABLE `active_coupons` (
`coupon_code` varchar(50)
,`coupon_name` varchar(100)
,`current_usage_count` int
,`description` text
,`discount_type` enum('fixed','percentage')
,`discount_value` decimal(10,2)
,`id` int
,`is_reward_coupon` tinyint(1)
,`max_discount_amount` decimal(10,2)
,`minimum_order_amount` decimal(10,2)
,`points_required` int
,`usage_limit_per_customer` int
,`usage_limit_total` int
,`valid_from` datetime
,`valid_until` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_product_offers`
-- (See below for the actual view)
--
CREATE TABLE `active_product_offers` (
`CategoryId` int
,`created_date` timestamp
,`discount_percentage` double
,`MetaKeywords` text
,`MetaTags` text
,`min_mrp` text
,`min_offer_price` text
,`offer_description` text
,`offer_id` int
,`offer_title` varchar(255)
,`PhotoPath` text
,`product_id` int
,`ProductCode` text
,`ProductName` text
,`savings_amount` double
,`ShortDescription` text
,`Specification` text
,`SubCategoryId` int
,`updated_date` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_applications`
--

CREATE TABLE `affiliate_applications` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `traffic_range` enum('1k-5k','5k-10k','10k-50k','50k-100k','100k+') COLLATE utf8mb4_unicode_ci NOT NULL,
  `marketing_experience` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_message` text COLLATE utf8mb4_unicode_ci,
  `application_status` enum('pending','under_review','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reviewed_by` int DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `approval_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Affiliate program applications';

-- --------------------------------------------------------

--
-- Table structure for table `authenticate_customers`
--

CREATE TABLE `authenticate_customers` (
  `CustId` int NOT NULL,
  `Name` text COLLATE utf8mb4_general_ci,
  `Email` text COLLATE utf8mb4_general_ci,
  `Mobile` text COLLATE utf8mb4_general_ci,
  `Code` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authenticate_customers`
--

INSERT INTO `authenticate_customers` (`CustId`, `Name`, `Email`, `Mobile`, `Code`) VALUES
(1, 'Muddassar', 'admin@gmail.com', '8329566751', '#pw483851856'),
(2, 'Muddassar', 'admin@gmail.com', '8329566751', '123456'),
(3, 'Muddassar', 'admin@gmail.com', '8329566751', '123456'),
(4, 'Muddassar', 'admin@gmail.com', '8329566751', '#pw242129434'),
(5, 'Muddassar', 'admin@gmail.com', '8329566751', '123456'),
(6, 'Muddassar', 'admin@gmail.com', '8329566751', '#pw242129434'),
(7, 'Muddassar', '', '8329566751', '123456'),
(8, 'Affan', 'affan@purenutritionco.com', '8329566751', '12345678'),
(9, 'Affan', 'affan@purenutritionco.com', '8329566751', '#pw695761212'),
(10, 'Vaibhav ', 'vakawadevaibhav@gmai.com', '8975817213', '1691'),
(11, 'Vaibhav ', 'vakawadevaibhav@gmai.com', '8975817213', '16911691'),
(12, 'Vaibhav ', 'vakawadevaibhav@gmai.com', '8975817213', '1416121'),
(13, 'Vaibhav ', 'vakawadevaibhav@gmai.com', '8975817213', '169116911691'),
(14, 'Sahil', 'aftabmujawar2308@gmail.com', '8830135758', '416'),
(15, 'Sahil', 'aftabmujawar2308@gmail.com', '8830135758', ' MN-KN100'),
(16, 'Sahil', 'aftabmujawar2308@gmail.com', '8830135758', 'MN-KN100'),
(17, 'Sahil', 'aftabmujawar2308@gmail.com', '8830135758', 'MN-KN100'),
(18, 'Sahil', 'aftabmujawar2308@gmail.com', '8830135758', 'MN100843'),
(19, 'Sahil', 'aftabmujawar2308@gmail.com', '8830135758', 'MN100843'),
(20, 'Sahil', 'aftabmujawar2308@gmail.com', '8830135758', 'MN100843');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `BannerId` int NOT NULL,
  `PhotoPath` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `Title` text NOT NULL,
  `ShortDescription` text NOT NULL,
  `Position` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`BannerId`, `PhotoPath`, `Title`, `ShortDescription`, `Position`) VALUES
(12, '63663.jpg', 'Experience the Heading Power Of  ', 'Shilajit', NULL),
(14, '88724.webp', 'Helps Reduce Stress and Supports Heart Function', 'Herbal & Ayurvedic BP Care Juice', NULL),
(17, '83515.jpg', 'Fuel Your Wellness with the Power of Amla', 'Refresh & Rejuvenate with Pure Amla Juice\'s  Nature\'s Boost in Every Sip!', NULL),
(18, '76619.jpg', 'She Care Plus Juice', 'A Natural Hormonal Balance solution for her', NULL),
(19, '64014.jpg', 'Thyroid Health, the Natural Boost You Need  ', 'Reclaim Balance, Energy, and vitality with Thyro Balance Care! ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blogs_master`
--

CREATE TABLE `blogs_master` (
  `BlogId` int NOT NULL,
  `SubCategoryId` int DEFAULT NULL,
  `BlogTitle` text NOT NULL,
  `BlogDate` date NOT NULL,
  `PhotoPath` text NOT NULL,
  `Description` text NOT NULL,
  `IsActive` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blogs_master`
--

INSERT INTO `blogs_master` (`BlogId`, `SubCategoryId`, `BlogTitle`, `BlogDate`, `PhotoPath`, `Description`, `IsActive`) VALUES
(7, 1, 'Shilajit in Summer', '2025-06-28', '2402.png', '<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Shilajit in summer is a topic that raises curiosity for many health enthusiasts. Known for its incredible health benefits, shilajit has been a prized Ayurvedic remedy for centuries. But can this potent substance be consumed during the sweltering heat of summer? Let&rsquo;s explore how you can effectively use shilajit in the summer months while enjoying its remarkable advantages.</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-family:Georgia,serif;\"><strong><span style=\"font-size:36px\"><span style=\"color:#000000\">Can I Take Shilajit in Summer?</span></span></strong></span></h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<hr />\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Yes, you can take shilajit in summer! This natural substance is beneficial year-round. While it&rsquo;s commonly associated with boosting energy and immunity during colder months, shilajit can be equally helpful in summer when taken correctly.</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">It helps maintain energy levels despite the draining heat.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Supports hydration by enhancing the absorption of nutrients.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Aids in detoxification, which is crucial in hot weather.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Balances body heat when consumed in the right dosage.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"color:#000000\"><strong>Can Shilajit Be Taken in Summer?</strong></span></span></span></h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<hr />\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Absolutely, shilajit can be taken in summer, but some precautions are essential:</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Dosage Adjustment</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Reduce the dosage to suit your body&rsquo;s summer metabolism.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Timing</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Consume it during cooler parts of the day, like early morning or late evening.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Hydration</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Drink plenty of water to balance its warming properties.</span></span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color:transparent\">By following these simple steps, you can enjoy the benefits of shilajit without discomfort.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Georgia,serif;\"><strong>How to Take Shilajit in Summer?</strong></span></span><br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">To maximize the benefits of shilajit in summer, here&rsquo;s how you can incorporate it into your routine:</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Diluted in Water</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Mix a pea-sized amount of shilajit in lukewarm water or herbal tea. Avoid hot liquids during summer.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>With Cooling Agents</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Pair shilajit with cooling foods like buttermilk or aloe vera juice to counterbalance its heat.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Time It Right</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Take it in the early morning to boost energy or at night to support recovery.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Georgia,serif;\"><strong>Shilajit Benefits in Summer</strong></span></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Even during summer, shilajit offers numerous health benefits:</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Energy Booster</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Helps combat fatigue caused by excessive heat.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Detoxification</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Flushes out toxins that accumulate due to summer heat.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Improved Digestion</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Enhances gut health, which can be sensitive during the hot season.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Skin Health</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Its antioxidant properties help fight skin damage caused by sun exposure.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><strong>Immune Support</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">: Strengthens immunity against seasonal infections.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Georgia,serif;\"><strong>Is It Safe to Take Shilajit in Summer?</strong></span></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-family:Georgia,serif;\"><span style=\"font-size:20px\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Yes, it is safe to take shilajit in summer when consumed responsibly. Here are some safety tips:</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-family:Georgia,serif;\"><span style=\"font-size:20px\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Stick to a recommended dosage (consult a healthcare professional if unsure).</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-family:Georgia,serif;\"><span style=\"font-size:20px\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Pair it with cooling beverages to neutralize its heating effect.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-family:Georgia,serif;\"><span style=\"font-size:20px\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Monitor your body&rsquo;s response, especially if you&rsquo;re new to shilajit.</span></span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Georgia,serif;\"><strong>Conclusion</strong></span></span></h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Georgia,serif;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">Shilajit in summer is not only safe but also beneficial when consumed wisely. By adjusting your dosage and pairing it with cooling foods, you can harness its power to stay energized and healthy even in the hottest months. For the best quality, trust </span><strong>My Nutrify Shilajit</strong><span style=\"background-color: transparent; color: rgb(0, 0, 0);\">, crafted to deliver optimal benefits and unmatched purity. Experience the difference this summer!</span></span></span></p>\r\n', 'Y'),
(8, 1, 'Shilajit for Testosterone: Boost Your Vitality Naturally', '2025-01-09', '8810.png', '<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Shilajit for testosterone is a topic gaining increasing attention for its promising benefits in boosting male vitality. Shilajit, a potent mineral-rich substance, has been revered in Ayurveda for centuries due to its diverse health benefits. In recent years, it has been recognized for its role in enhancing testosterone levels, contributing to better energy, muscle growth, and overall male health. This article delves into how Shilajit supports testosterone levels, its benefits, and how to use it effectively.</span></span></p>\r\n\r\n<p><span style=\"font-size:18px;\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span></span></p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>How Does Shilajit Help Increase Testosterone?</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"display: none;\">&nbsp;</span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Shilajit is known to contain fulvic acid and over 85 minerals, which play an essential role in boosting testosterone production. It helps stimulate the production of luteinizing hormone (LH), which is responsible for signaling the testes to produce more testosterone. Additionally, Shilajit enhances the body&#39;s ability to absorb minerals, vitamins, and nutrients that are crucial for hormone production.</span></span></p>\r\n\r\n<p><span style=\"font-size:18px;\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span></span><span style=\"display: none;\">&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>Shilajit Increases Testosterone</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Research has shown that Shilajit has a profound impact on increasing testosterone levels. It works by improving mitochondrial function, enhancing energy production in the body, and supporting hormonal balance. The increase in testosterone is linked to Shilajit&#39;s adaptogenic properties, which help the body cope with stress, a major inhibitor of testosterone.</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Shilajit supports optimal testicular health</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">It helps balance hormones naturally</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">It reduces oxidative stress, which negatively affects testosterone levels</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:36px;\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span><strong>Shilajit Benefits for Testosterone</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">The benefits of Shilajit for testosterone are wide-ranging, supporting various aspects of male health, including:</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type: disc;\"><span style=\"font-size:20px;\"><strong>Enhanced Muscle Mass</strong><span style=\"background-color:transparent; font-family:Arial,sans-serif\">:</span></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\"> Testosterone plays a key role in muscle growth, and Shilajit can help boost this effect by naturally increasing testosterone.</span></span><br />\r\n	&nbsp;</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li style=\"list-style-type: disc;\"><span style=\"font-size:18px;\"><strong>I</strong></span><span style=\"font-size:20px;\"><strong>mproved Energy and Stamina</strong></span><span style=\"background-color:transparent; font-family:Arial,sans-serif\"><span style=\"font-size:20px;\">:</span><span style=\"font-size:18px;\"> Higher testosterone levels lead to improved physical performance and stamina.</span></span><br />\r\n	&nbsp;</li>\r\n</ul>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Better Libido</strong></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">: Testosterone is directly linked to sexual health and libido, and Shilajit can enhance these aspects.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Improved Mood and Mental Clarity</strong><span style=\"background-color:transparent; font-family:Arial,sans-serif\">:</span></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\"> By supporting hormonal balance, Shilajit can reduce stress and improve mental clarity.</span></span></p>\r\n	</li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:18px;\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span></span></p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>Shilajit Can Increase Testosterone</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Scientific studies have found that daily consumption of Shilajit can increase testosterone levels in men, particularly those with low testosterone. By optimizing the body&rsquo;s natural hormone production, Shilajit helps men experience better health, vitality, and a more youthful outlook.</span></span></p>\r\n\r\n<p><span style=\"font-size:20px\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span></span></p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>Shilajit Effect on Testosterone</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">The effect of Shilajit on testosterone levels has been observed to be long-term. Regular use can provide:</span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Sustained Energy</strong><span style=\"background-color:transparent; font-family:Arial,sans-serif\">:</span></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\"> More testosterone means better energy levels throughout the day.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Muscle Recovery</strong><span style=\"background-color:transparent; font-family:Arial,sans-serif\">:</span></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\"> Faster recovery post-workout due to improved hormone levels.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Reduced Fatigue</strong><span style=\"background-color:transparent; font-family:Arial,sans-serif\">:</span></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\"> Shilajit helps reduce fatigue caused by low testosterone levels, ensuring consistent energy throughout the day.</span></span></p>\r\n	</li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:20px\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span></span></p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>Shilajit: How Much Testosterone Does Shilajit Increase?</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">The amount of testosterone that Shilajit increases can vary depending on individual health conditions. Studies show that Shilajit may help increase testosterone levels by up to 23%, with some users reporting even more significant results. However, it&rsquo;s important to note that results depend on factors like diet, lifestyle, and the presence of other health conditions.</span></span></p>\r\n\r\n<p><span style=\"font-size:20px\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span></span></p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>Shilajit Dosage for Testosterone</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">To experience the benefits of Shilajit for testosterone, the recommended dosage typically ranges from </span><strong>300 to 500 mg</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> of Shilajit resin daily. It&rsquo;s best to take it in small doses, preferably with warm water or milk, to ensure proper absorption.</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Start with a smaller dose and gradually increase based on your body&rsquo;s response.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Consistent use is key to seeing noticeable effects over time.</span></span></p>\r\n	</li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:18px;\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span></span></p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>Key Benefits of Shilajit for Testosterone</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Boosts natural testosterone production</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Enhances muscle mass and strength</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Improves sexual health and libido</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Promotes better mood and mental clarity</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; font-family:Arial,sans-serif\">Reduces fatigue and improves stamina</span></span></p>\r\n	</li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:36px;\"><span new=\"\" roman=\"\" style=\"background-color:transparent; color:#000000; font-family:\" times=\"\">&nbsp;</span><strong>Conclusion</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">For those seeking a natural way to boost testosterone levels, </span><strong>My Nutrify Shilajit</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> stands out as the best choice. With its pure, high-quality Himalayan Shilajit resin, it offers all the benefits of increased testosterone, enhanced energy, muscle growth, and improved sexual health. Choose My Nutrify Shilajit to support your journey toward optimal health and vitality.</span></span></p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">&nbsp;</span></span></p>\r\n\r\n<div>&nbsp;</div>\r\n', 'Y'),
(9, 1, 'Shilajit Ayurvedic Medicine: The Ancient Elixir for Modern Health', '2025-01-09', '8691.png', '<p>&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Shilajit Ayurvedic Medicine has been revered in traditional healing systems for centuries, offering a multitude of health benefits. This potent natural substance, found in the high-altitude rocks of the Himalayas, is known for its rich mineral content and numerous therapeutic properties. Let&rsquo;s explore the uses and benefits of Shilajit and discover why it is considered a staple in Ayurvedic medicine.</span></span><br />\r\n&nbsp;</h3>\r\n\r\n<h4><span style=\"font-size:36px;\"><strong>Uses of Shilajit</strong></span><br />\r\n&nbsp;</h4>\r\n\r\n<h3><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Shilajit is utilized in various forms within Ayurvedic practices, including:</span></span></h3>\r\n\r\n<h3>&nbsp;</h3>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><strong>Supplement for Energy:</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> Known as a natural energy booster, Shilajit helps combat fatigue and improves stamina.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><strong>Cognitive Enhancer:</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> It supports cognitive functions, improving memory and concentration.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><strong>Adaptogen:</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> Shilajit helps the body adapt to stress, promoting overall mental well-being.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><strong>Nutrient Absorption:</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> It enhances the absorption of essential nutrients, promoting better health.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><strong>Support for Sexual Health:</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> Traditionally used to enhance libido and sexual performance.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h4><span style=\"font-size:36px;\"><strong>Benefits of Shilajit</strong></span><br />\r\n&nbsp;</h4>\r\n\r\n<h3><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">The benefits of Shilajit extend beyond its uses, making it a powerful ally for health:</span></span></h3>\r\n\r\n<h3>&nbsp;</h3>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Rich in Minerals:</strong></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> Contains over 80 essential minerals, including fulvic acid, which aids in nutrient absorption.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Anti-Aging Properties:</strong></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> Its antioxidants help combat free radicals, promoting youthful skin and overall vitality.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Supports Joint Health:</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> </span></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Reduces inflammation and supports joint flexibility, beneficial for conditions like arthritis.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Boosts Immunity:</strong></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> Strengthens the immune system, helping the body fight off infections.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:20px;\"><strong>Enhances Physical Performance:</strong></span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> Increases energy levels and improves athletic performance, making it popular among bodybuilders and athletes.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Conclusion</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:20px\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">In conclusion, Shilajit Ayurvedic Medicine is a powerful natural supplement with a wide array of health benefits. Its rich mineral content and therapeutic properties make it a vital component of Ayurvedic healing. For those seeking quality Shilajit, </span><strong>My Nutrify Shilajit</strong><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\"> stands out as the best choice, providing a pure and effective product to enhance your well-being.</span></span></p>\r\n\r\n<h3>&nbsp;</h3>\r\n', 'Y'),
(11, 1, 'Shilajit for Hair Growth: Unlock the Secrets to Healthier Hair', '2025-01-09', '9885.png', '<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Shilajit for hair growth has been a time-tested natural remedy used in traditional medicine to improve the health and appearance of hair. If you&rsquo;re struggling with hair loss, thinning, or lackluster locks, Shilajit may provide the solution. In this blog, we&#39;ll explore how Shilajit supports hair health, prevents hair loss, and stimulates hair growth, making it a potent ingredient for anyone looking to boost their hair naturally.</span></span><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Shilajit and Hair Loss</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Hair loss can occur due to a variety of factors, including genetics, stress, hormonal imbalances, and nutrient deficiencies. Here&#39;s how Shilajit can help combat these issues:</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Rich in minerals: Shilajit contains over 80 minerals, including fulvic acid, which helps to nourish hair follicles and strengthen hair strands.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Reduces oxidative stress: The antioxidant properties of Shilajit help protect hair from free radical damage, which can lead to hair thinning and loss.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Hormonal balance: Shilajit supports balanced hormone levels, particularly in men, helping to reduce hair loss caused by hormonal imbalances.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Shilajit Benefits for Hair</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Nourishes the scalp: The minerals and nutrients in Shilajit improve scalp health, promoting a healthy environment for hair growth.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Strengthens hair: Regular use of Shilajit can improve hair strength, reducing breakage and making your hair more resilient.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Boosts hair growth: Shilajit stimulates hair follicles, promoting thicker and faster hair growth.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Prevents hair thinning: By improving blood circulation to the scalp, Shilajit can help prevent thinning hair.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Reverses hair damage: Shilajit&#39;s antioxidant properties help reverse damage caused by environmental pollutants and chemical treatments.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Shilajit and Hair Growth</strong></span></h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Shilajit plays a vital role in enhancing hair growth by stimulating dormant hair follicles and boosting nutrient delivery to the scalp. Here&rsquo;s how:</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Increases oxygen supply to hair follicles, encouraging them to enter the growth phase.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Enhances nutrient absorption in the scalp, ensuring hair gets the necessary vitamins and minerals.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Promotes healthy cell regeneration, which is essential for stronger, thicker hair growth.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Rejuvenates hair follicles, especially in cases of hair thinning or early-stage hair loss.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Conclusion</strong></span></h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Shilajit for hair growth is an all-natural, powerful solution that supports healthy hair from the roots to the tips. For those seeking a reliable, pure source of Shilajit, My Nutrify Shilajit is the best choice, offering high-quality, Himalayan Shilajit resin that ensures optimal hair growth results.</span></span></p>\r\n', 'Y'),
(12, 1, 'Shilajit for Fertility: Boost Your Reproductive Health Naturally', '2025-01-09', '2698.png', '<p><br />\r\n<span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Shilajit for fertility has gained attention in the wellness world for its potential to support reproductive health in both men and women. Known for its rich mineral content and adaptogenic properties, Shilajit is traditionally used in Ayurvedic medicine to enhance vitality, stamina, and hormonal balance. In this blog, we will explore the benefits of Shilajit for fertility, its effects on male and female reproductive health, and how to incorporate it into your wellness routine for optimal results.</span></span><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Understanding Fertility &amp; Reproductive Health</strong></span><br />\r\n<br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Fertility is influenced by various factors, including lifestyle, diet, and overall hormonal balance. Reproductive health relies on a well-functioning endocrine system, where hormones like estrogen and testosterone play a crucial role. Maintaining balanced hormone levels is essential for both male and female fertility. Natural supplements like Shilajit have been found to support reproductive health by enhancing energy levels, hormone balance, and overall vitality.</span></span><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Shilajit for Female Fertility</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">For women, fertility can be influenced by stress, hormonal imbalances, and nutritional deficiencies. Shilajit, with its nutrient-dense composition, may help address these challenges by providing essential minerals and fulvic acid, which improve nutrient absorption and cellular function. The adaptogenic properties of Shilajit also help women manage stress, which is a common factor that can impact fertility.</span></span><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Shilajit Benefits for Female Fertility</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Shilajit offers numerous benefits for enhancing female fertility, including:</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Hormonal Balance: Shilajit helps regulate estrogen and progesterone levels, essential for a healthy menstrual cycle.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Improved Energy and Vitality: By boosting energy at a cellular level, Shilajit helps women feel more energetic and resilient.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Enhanced Nutrient Absorption: Fulvic acid in Shilajit enhances the body&rsquo;s ability to absorb essential nutrients, supporting reproductive health.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Stress Reduction: Shilajit&rsquo;s adaptogenic properties reduce stress, which can positively impact fertility and reproductive health.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Shilajit for Male Fertility</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Male fertility can also benefit from Shilajit&rsquo;s natural properties. Shilajit has been shown to boost testosterone levels, increase sperm count, and improve overall reproductive health. Its mineral-rich composition and antioxidant properties make it ideal for enhancing male fertility and combating oxidative stress, which can affect sperm quality.</span></span><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Shilajit Benefits for Male Fertility</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Here are some key ways Shilajit supports male fertility:</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Boosted Testosterone Levels: Shilajit has been shown to increase testosterone levels, essential for sperm production and sexual health.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Increased Sperm Count and Quality: Shilajit&rsquo;s nutrients and antioxidants support healthier sperm, contributing to a better chance of conception.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Improved Sexual Health: Shilajit enhances libido and overall vitality, making it beneficial for male reproductive health.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Reduced Oxidative Stress: The antioxidants in Shilajit protect sperm cells from oxidative damage, which can improve fertility outcomes.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>How To Take Shilajit for Fertility</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Incorporating Shilajit into your daily routine can support reproductive health. Here are some guidelines for taking Shilajit:</span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Dosage: 300-500 mg of Shilajit resin daily is recommended, but consult a healthcare provider to find the right dosage for you.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Timing: Take Shilajit in the morning or early afternoon for the best energy-boosting effects.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Consumption Method: Dissolve Shilajit resin in warm water or milk to make it easier to absorb and enjoy.</span></span></p>\r\n	</li>\r\n	<li style=\"list-style-type:disc\">\r\n	<p><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Arial,sans-serif\">Consistency: Regular use over several weeks may yield the best results for fertility.</span></span><br />\r\n	&nbsp;</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:36px;\"><strong>Conclusion</strong></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"display: none;\">&nbsp;</span><span style=\"font-size:18px;\"><span style=\"background-color:transparent; color:#000000; font-family:Poppins,sans-serif\">Shilajit for fertility is a natural way to enhance reproductive health in both men and women. With its potent minerals, fulvic acid, and antioxidant properties, Shilajit offers a holistic approach to improving fertility. Incorporating<strong> My Nutrify Shilajit</strong> into your routine can be a game-changer for your reproductive health, ensuring you receive a high-quality, pure product designed to support your wellness journey.</span></span><span style=\"display: none;\">&nbsp;</span></p>\r\n', 'Y');
INSERT INTO `blogs_master` (`BlogId`, `SubCategoryId`, `BlogTitle`, `BlogDate`, `PhotoPath`, `Description`, `IsActive`) VALUES
(13, 1, 'Shilajit for Pre-Workout: Boost Your Performance Naturally', '2025-01-09', '7283.png', '<p class=\"MsoNormal\" style=\"margin: 12pt 0in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">Shilajit for pre-workout is gaining traction among fitness enthusiasts looking to elevate their performance naturally. With its adaptogenic properties, Shilajit offers multiple benefits as a pre-workout supplement, enhancing energy levels, stamina, and recovery. Shilajit for pre-workout is a smart choice for those seeking a holistic approach to peak athletic performance. Here&acirc;&euro;&trade;s a comprehensive guide on how to incorporate Shilajit effectively into your fitness routine.</span></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:36px;\"><span segoe=\"\" ui=\"\">&nbsp;</span><b><span segoe=\"\" ui=\"\">Uses of Shilajit for Pre-Workout</span></b></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<ul style=\"margin-top:0in\" type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Energy Boost:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">&nbsp;Shilajit provides a natural source of energy, reducing fatigue and allowing for longer, more intense workouts.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;\r\n     font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Enhanced Stamina:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">&nbsp;Its rich mineral content aids in sustaining energy, making Shilajit a powerful addition to pre-workout routines.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;\r\n     font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Focus and Alertness</span></b><span segoe=\"\" ui=\"\">: </span></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">By improving mental clarity and focus, Shilajit ensures you stay sharp and motivated during workouts.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;font-family:\r\n     \" ui=\"\"><o:p></o:p></span></li>\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Recovery Support</span></b></span><span segoe=\"\" style=\"font-size:16.0pt;\r\n     font-family:\" ui=\"\"><span style=\"font-size:20px;\">:</span><span style=\"font-size:18px;\"> Its anti-inflammatory properties help in faster muscle recovery, reducing soreness post-workout.</span></span><br />\r\n	<br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;font-family:\r\n     \" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 14pt 0in 4pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:36px;\"><b><span segoe=\"\" ui=\"\">Benefits of Shilajit for Pre-Workout</span></b></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<ul style=\"margin-top:0in\" type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Boosts Oxygen Levels:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\"> Fulvic acid in Shilajit increases oxygenation in muscles, reducing the build-up of lactic acid.</span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">&nbsp;</span></span></p>\r\n\r\n<ul type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Natural Adaptogen:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\"> Helps the body adapt to stress, optimizing both physical and mental performance.</span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-left: 0.5in; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">&nbsp;</span></span></p>\r\n\r\n<ul type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Rich Nutrient Profile:</span></b><span segoe=\"\" ui=\"\"> </span></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">Provides vital minerals and nutrients that aid in overall muscle health and energy production.</span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">&nbsp;</span></span></p>\r\n\r\n<ul type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Supports Endurance:</span></b></span><span segoe=\"\" style=\"font-size:17.0pt;font-family:\" ui=\"\"><span style=\"font-size:20px;\"> </span><span style=\"font-size:18px;\">Regular use can lead to enhanced endurance, making Shilajit a preferred supplement for athletes.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 14pt 0in 4pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:36px;\"><b><span segoe=\"\" ui=\"\">Unveiling the Power of Shilajit</span></b></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span segoe=\"\" style=\"font-size: 16pt; font-family: \" ui=\"\"><span style=\"font-size:18px;\">Shilajit&#39;s potency lies in its unique composition of minerals, fulvic acid, and humic acid, which work in synergy to support the body&acirc;&euro;&trade;s energy production and endurance. It acts as a natural fuel, delivering a steady source of energy and supporting muscle function.</span></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 14pt 0in 4pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:36px;\"><b><span segoe=\"\" ui=\"\">Shilajit&#39;s Impact on Pre-Workout Performance</span></b></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<ul style=\"margin-top:0in\" type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Improved Muscle Efficiency:</span></b><span segoe=\"\" ui=\"\"> </span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in 0.0001pt 0.5in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">Shilajit aids in muscle efficiency by enhancing mitochondrial function, allowing for extended and more effective workouts.</span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<ul style=\"margin-top:0in\" type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Reduced Oxidative Stress:</span></b><span segoe=\"\" ui=\"\"> </span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in 0.0001pt 0.5in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">Helps in minimizing the impact of free radicals generated during intense physical activity.</span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<ul style=\"margin-top:0in\" type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Adaptogenic Qualities</span></b><span segoe=\"\" ui=\"\">:</span></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\"> </span></span><span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in 0.0001pt 0.5in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span segoe=\"\" style=\"font-size:17.0pt;font-family:\" ui=\"\"><span style=\"font-size:18px;\">Shilajit&#39;s adaptogenic properties assist in managing stress, ensuring a balanced approach to challenging workouts.</span></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 14pt 0in 4pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:36px;\"><b><span segoe=\"\" ui=\"\">Incorporating Shilajit into Your Pre-Workout Routine</span></b></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<ul style=\"margin-top:0in\" type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Dosage:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\"> Recommended to take 300-500 mg of Shilajit around 20-30 minutes before workout.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;\r\n     font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">How to Use:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\"> Mix Shilajit resin in warm water, milk, or a smoothie for a natural energy boost.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Consistency:</span></b></span><span segoe=\"\" style=\"font-size:16.0pt;font-family:\" ui=\"\"><span style=\"font-size:18px;\"> For optimal results, incorporate Shilajit consistently in your routine to build endurance and strength over time.</span></span><br />\r\n	<br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;\r\n     font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 14pt 0in 4pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:36px;\"><b><span segoe=\"\" ui=\"\">Precautions and Considerations</span></b></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<ul style=\"margin-top:0in\" type=\"disc\">\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Check with a Professional:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\"> Consult a healthcare provider, especially if you have medical conditions.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Quality Matters:</span></b></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\"> Choose high-quality, pure Shilajit, such as My Nutrify Shilajit, to avoid contaminants.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n	<li class=\"MsoNormal\" style=\"color: black; margin-top: 12pt; margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; vertical-align: baseline;\"><span style=\"font-size:20px;\"><b><span segoe=\"\" ui=\"\">Start with Small Doses:</span></b><span segoe=\"\" ui=\"\"> </span></span><span style=\"font-size:18px;\"><span segoe=\"\" ui=\"\">Gradually increase dosage to assess tolerance, as it can be potent for some users.</span></span><br />\r\n	<br />\r\n	<span segoe=\"\" style=\"font-size:12.0pt;font-family:\" ui=\"\"><o:p></o:p></span></li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\" style=\"margin-bottom: 0.0001pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\">&nbsp;</p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 14pt 0in 4pt; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span style=\"font-size:36px;\"><b><span segoe=\"\" ui=\"\">Conclusion</span></b></span><br />\r\n<br />\r\n<span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span segoe=\"\" style=\"font-size: 17pt; font-family: \" ui=\"\">Incorporating My Nutrify Shilajit as part of your pre-workout routine offers a natural and effective way to boost performance, endurance, and recovery. Embrace the power of Shilajit for a balanced, long-lasting approach to peak fitness performance.</span><span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\"><o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\"><span segoe=\"\" style=\"font-size: 12pt; font-family: \" ui=\"\">&nbsp;<o:p></o:p></span></p>\r\n\r\n<p class=\"MsoNormal\" style=\"margin: 12pt 0in; line-height: normal; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\">&nbsp;</p>\r\n\r\n<p class=\"MsoNormal\"><o:p>&nbsp;</o:p></p>\r\n\r\n<p style=\"margin-top:12.0pt;margin-right:0in;margin-bottom:12.0pt;margin-left:\r\n0in\">&nbsp;</p>\r\n\r\n<p class=\"MsoNormal\"><o:p>&nbsp;</o:p></p>\r\n', 'Y'),
(14, 1, '10 Ayurvedic Herbs to Naturally Reduce Cholesterol Levels', '2025-03-19', '4071.png', '<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:18px;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0); font-family: Poppins, sans-serif; white-space-collapse: preserve;\">In today&#39;s fast-moving world, maintaining a healthy lifestyle has become more important than ever. One of the most common health concerns linked to a sedentary lifestyle and poor dietary habits is high cholesterol. </span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:18px;\"><span style=\"background-color: transparent; color: rgb(0, 0, 0); font-family: Poppins, sans-serif; white-space-collapse: preserve;\">Increased cholesterol levels can raise the risk of heart disease and other cardiovascular issues.</span></span></h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b id=\"docs-internal-guid-c1ee90a9-7fff-43cf-9492-402d486c8533\" style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Many individuals turn to natural remedies to manage cholesterol, either as an alternative or alongside conventional medications. Among these, Ayurvedic herbs have gained significant popularity due to their ability to naturally regulate cholesterol levels and promote heart health.</span></b></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b id=\"docs-internal-guid-c1ee90a9-7fff-43cf-9492-402d486c8533\" style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Ayurveda, the ancient Indian medical system, is rich in herbal solutions for various health concerns, including high cholesterol. For centuries, these herbs have been used to maintain overall well-being and support cardiovascular<br />\r\nhealth. Below are ten powerful Ayurvedic herbs known for their cholesterol-lowering properties.</span></b></span></p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Guggul</span></span><br />\r\n&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">One of the most well-known Ayurvedic remedies for managing cholesterol is Guggul. It contains guggulsterones, compounds that help reduce LDL (bad cholesterol) while increasing HDL (good cholesterol), making it a great choice for heart health.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Turmeric (Curcuma longa)</span></span><br />\r\n&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Turmeric has long been valued in Ayurveda for its anti-inflammatory and cholesterol-lowering benefits. The active component, curcumin, helps prevent cholesterol oxidation, reducing the risk of plaque buildup in the arteries and supporting cardiovascular health.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Arjuna (Terminalia arjuna)</span></span><br />\r\n&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">A well-known Ayurvedic herb for heart support, Arjuna is believed to strengthen heart muscles and reduce LDL cholesterol and triglycerides. Research suggests that it plays a role in promoting overall cardiovascular health</span><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Tulsi (Holy Basil)</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Also known as Ocimum sanctum, Tulsi is a sacred herb in Ayurveda. It is recognized for its cholesterol-lowering effects and its ability to improve lipid metabolism, making it beneficial for heart health.</span></b></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\">&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Fenugreek (Trigonella foenum-graecum)</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Fenugreek seeds contain soluble fiber, which helps prevent cholesterol absorption in the digestive system. Additionally, fenugreek has been found to support blood sugar regulation and enhance insulin sensitivity.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Cinnamon (Dalchini)</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">More than just a flavorful spice, Cinnamon is known for its ability to increase HDL cholesterol while lowering LDL cholesterol and triglycerides. It also contains powerful antioxidants that support overall heart health.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Licorice (Glycyrrhiza glabra)</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Widely used in Ayurveda for cardiovascular benefits, Licorice helps reduce LDL cholesterol and triglycerides while raising HDL cholesterol. It also has anti-inflammatory properties that further promote heart wellness.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Ashwagandha (Withania somnifera)</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Ashwagandha is an adaptogenic herb known for its ability to help the body cope with stress&acirc;&euro;&rdquo;a major contributor to high cholesterol levels. By reducing stress, Ashwagandha indirectly aids in cholesterol management and supports overall heart health.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Garlic (Allium sativum)</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Garlic helps reduce total cholesterol, lower blood pressure, and prevent plaque buildup in the arteries. It supports better blood circulation and overall heart health.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Shatavari (Asparagus racemosus)</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><b style=\"font-weight:normal;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Shatavari is a rejuvenating herb that aids in balancing cholesterol levels and supporting heart function. It also helps reduce oxidative stress, promoting cardiovascular well-being.</span></b></span><br />\r\n&nbsp;</p>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\"><span style=\"font-size:36px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Conclusion</span></span></h3>\r\n\r\n<h3 dir=\"ltr\" style=\"line-height:1.38;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Adding these Ayurvedic herbs to your routine can naturally help lower cholesterol and support heart health. For optimal benefits, combine them with a balanced diet rich in fruits, vegetables, whole grains, and lean proteins, along with regular exercise.</span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\"><span style=\"font-size:18px;\"><span style=\"font-family: Poppins, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Ayurveda provides a holistic and natural way to manage cholesterol while promoting overall well-being. By embracing the healing power of nature, you can take proactive steps toward a healthier heart and a better quality of life.</span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n', 'Y');
INSERT INTO `blogs_master` (`BlogId`, `SubCategoryId`, `BlogTitle`, `BlogDate`, `PhotoPath`, `Description`, `IsActive`) VALUES
(18, 5, '5 Amazing Benefits of AMLA Juice for Glowing Skin and Strong Hair', '2025-04-20', '1983.jpg', '<h3 dir=\"ltr\" style=\"line-height:1.38;text-align: justify;margin-top:14pt;margin-bottom:4pt;\">&nbsp;</h3>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">AMLA, also called Indian gooseberry, is a small green fruit that is very good for your health. It has a lot of vitamin C even 20 times more than an orange! That&#39;s why it helps to boost your immunity, keeps your skin glowing, and makes your hair strong. AMLA grows in many parts of India and is used in Ayurveda (traditional Indian medicine) for hundreds of years. Not just the fruit, but even its leaves and seeds are used in herbal remedies. It tastes sour and a little bitter, but it&#39;s full of powerful nutrients. AMLA is used in juices, powders, oils, and tonics to improve health in a natural way. The Importance of AMLA in Ayurveda AMLA juice is a powerful Ayurvedic drink that supports your skin, hair, and overall health. Rich in vitamin C and antioxidants, it helps fight free radicals, boosts immunity, improves digestion, and naturally detoxifies the body. Regular use of AMLA juice can reduce hair fall, promote hair growth, clear the skin, and give a healthy glow. It also helps balance all three dos has-Vata, Pitta, and Kafka-making it a great choice for daily wellness. Whether you&#39;re looking to improve your beauty or boost your energy, AMLA juice is a simple and natural solution.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Is AMLA Juice Good for Hair and Skin? Yes, absolutely! AMLA juice, often enjoyed as a daily health tonic, is well-known for its role in improving digestion, boosting immunity, and managing issues like constipation and high blood sugar. But beyond internal health, AMLA juice also works wonders for your skin and hair. Thanks to its rich vitamin C content and powerful antioxidants, AMLA juice helps nourish your skin from within, reduce signs of aging, and promote clear, glowing skin. For hair, it strengthens the roots, reduces hair fall, and supports healthy hair growth. That&#39;s why AMLA has become a star ingredient in many beauty and haircare products over the years. Here&#39;s everything you need to know about the incredible benefits of AMLA juice for your overall health. Let&#39;s explore the Benefits of wild AMLA Juice for your skin and hair health. Benefits of AMLA Juice for Skin Promotes a Natural Glow AMLA juice is rich in vitamin C, which brightens the skin and helps restore its natural radiance. It combats dullness and gives your skin a fresh, healthy glow. Fights Acne and Pimples its anti-inflammatory and antibacterial properties, AMLA juice helps reduce acne, pimples, and skin irritation. It also balances oil production, preventing future breakouts. Reduces Dark Spots and Pigmentation Regular consumption of AMLA juice can lighten dark spots, sun damage, and uneven skin tone. Its antioxidants help fight free radical damage, giving you clearer skin over time. Slows Down Aging Signs AMLA juice helps prevent wrinkles and fine lines by protecting the skin from free radicals. It promotes youthful skin by reducing sagging and maintaining elasticity.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Hydrates and Nourishes the Skin AMLA juice deeply hydrates the skin, keeping it soft, smooth, and well-moisturized. It helps maintain a healthy complexion, preventing dryness and giving your skin a fresh, nourished feel.How to Consume Amla Juice for Glowing Skin:</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"><b>Simple Amla Juice Drink</b></span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">1. To enjoy the benefits of amla juice, mix 30ml of amla juice with 30-50ml of water. You can add a sweetener like honey, sugar, or a pinch of salt to suit your taste. This refreshing drink can be consumed daily for radiant and healthy skin.</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">2. Amla &amp; Lauki Skin Shot</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">For an extra skin-boosting shot, blend together 1/4 of a medium bottle gourd (peeled and grated), 4 fresh amlas, 2 tbsp honey, a squeeze of lime, and 1 tbsp rock salt. This powerful drink nourishes your skin with natural nutrients, helping you achieve a glowing complexion.</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Benefits of Amla Juice for Hair</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Stimulates Hair Growth</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;Amla juice is a powerhouse of vitamin C and antioxidants that help nourish the scalp and hair follicles. These nutrients stimulate the follicles, promoting healthy hair growth. Consuming amla juice regularly can improve blood circulation to the scalp, which enhances the overall health of hair and encourages new hair growth. This makes it a natural solution for thinning hair and balding.</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Reduces Hair Fall</span></span></span><br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;The strengthening properties of amla juice help fortify the hair roots. Rich in tannins and phytochemicals, amla juice prevents hair from weakening and falling out. By improving scalp health and balancing oil production, it reduces scalp infections and other causes of hair fall. It also ensures that your hair grows from stronger roots, making it less prone to breakage.</span></span></span><br />\r\n<br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Prevents Premature Greying</span></span></span><br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;Amla juice contains a high amount of antioxidants, which fight off free radical damage that contributes to premature greying. It helps in retaining your hair&#39;s natural color and pigmentation. By boosting the production of melanin, amla juice can slow down the greying process and maintain darker, healthier hair for longer. Regular consumption or topical use can keep your hair looking vibrant and youthful.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Improves Scalp Health</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;Amla juice is known for its anti-inflammatory and antibacterial properties, which help to maintain a healthy scalp. It soothes irritation, reduces itching, and helps control excess oil production. The juice also balances the scalp&acirc;&euro;&trade;s natural oils, preventing conditions like dandruff, scalp infections, and dryness. A well-balanced scalp is essential for healthy hair growth and a clean, comfortable scalp.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Adds Shine and Softness</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;One of the key benefits of amla juice is its ability to hydrate and nourish the hair, making it soft, shiny, and manageable. Amla helps retain moisture in the hair strands, preventing dryness and frizz. The high levels of vitamin C and flavonoids in amla juice also help in enhancing the natural shine of your hair, giving it a glossy, smooth appearance. Regular intake and topical application will leave your hair feeling silky and well-nourished.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">How to Apply Amla Juice on Hair</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">There are several easy ways to harness the powerful hair benefits of amla juice:</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Amla Juice &amp; Lemon Scalp Massage</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;Mix 1 tablespoon of amla juice with 1 tablespoon of fresh lemon juice. Gently massage this mixture onto your scalp for about 5 minutes. Allow it to sit for 10 minutes, then wash it off with warm water and a mild shampoo. It improves blood flow in the scalp and makes hair roots stronger.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Consume Amla Juice Daily</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;For internal hair health, consume amla juice every morning on an empty stomach. Mix 30ml of Wild Amla Juice with 30&acirc;&euro;&ldquo;50ml of water for optimal daily consumption. You can add honey, sugar, or a pinch of salt to suit your taste. This will nourish your hair from within, providing essential vitamins and antioxidants for stronger, healthier hair.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Common Mistakes While Consuming Amla</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Undiluted Amla Juice: Drinking concentrated amla juice can cause acidity or heartburn. Always dilute it as per the instructions or your doctor&acirc;&euro;&trade;s advice.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:20px;\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Mixing with Medications</span><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">:</span></span><span style=\"font-size:18px;\"><span><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"> Amla may interact with certain medications. Consult a doctor before adding it to your routine if you&#39;re on other treatments.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:36px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Conclusion</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Amla truly lives up to its reputation as a powerful Ayurvedic superfruit. Rich in vitamin C, antioxidants, and essential nutrients, amla juice supports your overall wellness&acirc;&euro;&rdquo;from strengthening immunity to enhancing skin glow and promoting strong, healthy hair.</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Whether you consume it raw or as a juice, amla is a must-have in your daily wellness routine. For added convenience, opt for a high-quality product like My Nutrify Herbal &amp; Ayurveda&#39;s Amla Juice, which brings you all the natural goodness of fresh amla in a ready-to-drink form</span></span></span></p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\">&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">&Acirc;&nbsp;how My Nutrify Wild Amla Juice can transform your skin and hair&acirc;&euro;&rdquo;watch this video to learn the right way to use it for maximum benefits.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\"><b>https://www.youtube.com/watch?v=W2HhrqBKpKw</b></span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Q: Can drinking amla juice help with hair growth?</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">A: Yes. Amla juice is rich in Vitamin C, which boosts collagen production&acirc;&euro;&rdquo;a key protein for hair growth. It strengthens hair from root to tip, improving thickness, volume, and overall hair health.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Q: Does amla juice make the skin brighter or fairer?</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">A: Yes. The antioxidants and Vitamin C in amla juice help reduce dark spots, pigmentation, and tanning. Regular intake or external use can lead to brighter, clearer skin over time.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Q: Is it safe to drink amla juice every day for hair health?</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">A: Absolutely. Daily consumption of amla juice helps reduce dandruff, control hair fall, and delay premature greying. It strengthens hair follicles and keeps the scalp healthy.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Q: Is amla really beneficial for both hair and skin?</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">A: Yes. Amla is a natural remedy for skin and hair problems. It fights acne, dullness, pigmentation, and improves scalp conditions like dandruff, itching, and hair thinning.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">Q: Which amla juice is best for skin and hair?</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\"><span style=\"font-size:18px;\"><span id=\"docs-internal-guid-16052d91-7fff-c717-5c86-80e3084f41bb\"><span style=\"font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\">A: My Nutrify Wild Amla Juice is a great choice. It&acirc;&euro;&trade;s made from pure wild amla, packed with nutrients, and free from additives&acirc;&euro;&rdquo;perfect for glowing skin and strong, healthy hair.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n', 'Y'),
(19, 2, 'Wheatgrass Benefits: 10 Reasons to Drink It Every Day', '2025-05-04', '4803.jpg', '<p><span style=\"font-size:18px;\">Growing up, you probably heard your parents say, &quot;Eat your veggies!&quot; to help you stay healthy. As adults, though, eating enough vegetables every day can still feel like a challenge&mdash;how many greens can you really eat, right?&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">What if there was an easier way to get all those nutrients in one simple form? That&rsquo;s where wheatgrass comes in! Made from the young shoots of the wheat plant, this superfood is packed with fiber, vitamins, and nutrients, just like vegetables. It&rsquo;s not a magic fix, but it&rsquo;s a great way to boost your daily nutrition.&nbsp;</span></p>\r\n\r\n<p><br />\r\n<span style=\"font-size:36px;\"><u><strong><em>Here are the top 10 reasons why drinking wheatgrass every day could be a game-changer for you!&nbsp;</em></strong></u></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>What is Wheatgrass?&nbsp;</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass comes from the young green leaves of the wheat plant . These leaves, resembling regular grass, are harvested 7-10 days after planting and used to make juice or powder for consumption.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass gets its bright green color from chlorophyll, the green pigment in plants responsible for photosynthesis. Chlorophyll&rsquo;s structure is similar to hemoglobin, the protein in blood that carries oxygen, earning it the nickname &ldquo;green blood.&rdquo; Chlorophyll is also abundant in leafy greens like spinach.<br />\r\n&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>Why do people love wheatgrass?&nbsp;</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\">It&#39;s rich in chlorophyll (70%) and packed with amino acids, vitamins, minerals, enzymes, flavonoids, alkaloids, and tannins.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">It offers immense health benefits, from detoxification to energy boosting.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">While you can grow and juice this superfood at home for freshness, it&rsquo;s not without risks. Homegrown ingredients may sometimes be contaminated with bacteria or mold. To make things easier, My Nutrify offers Wheatgrass Juice, made from fresh, 9th-day harvested leaves. Our products contain no added sugar or artificial flavors, preserving all active nutrients for your health.&nbsp;</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>10 amazing benefits of wheatgrass&nbsp;</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass is a popular remedy in Ayurveda, commonly used for numerous health benefits. It is rich in vitamins, minerals and antioxidants.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">In folk medicine, people often use wheatgrass as a natural health tonic to treat various ailments, including colds, coughs, throat infections, fevers, joint pain, chronic skin conditions, and constipation, among others.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Its versatile healing properties have made it a popular remedy in traditional practices for promoting overall wellness.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass is rich in antioxidants, which help reduce inflammation and neutralize free radicals in the body.&nbsp;</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:36px;\"><strong>Here are some amazing benefits of Wheatgrass&nbsp;</strong></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>1) Reducing Chronic Disease Risk&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">s can lower the risk of chronic diseases such as heart disease, diabetes, and cancer by protecting cells and promoting overall health. Regular consumption supports cellular health and boosts the immune system, further contributing to disease prevention.&nbsp;</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>2) Provide Energy&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass boosts energy levels by supplying essential nutrients, including chlorophyll, vitamins, minerals, and amino acids. These nutrients support overall body functions, increase oxygen levels, and enhance metabolism, which helps reduce fatigue and promotes stamina. Wheatgrass is also known for its detoxifying properties, which can contribute to improved vitality.&nbsp;</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>3) Aids Digestion&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass aids digestion by improving the balance of gut flora, promoting healthy bowel movements, and reducing bloating. Its high fiber content supports regularity and the enzymes present in wheatgrass help break down food, easing the digestive process. This makes it a helpful addition to your diet for maintaining overall digestive health.&nbsp;</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>4) Lowers Bad Cholesterol&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass can help lower bad cholesterol (LDL) by promoting the elimination of toxins and improving liver function, which plays a role in fat metabolism. Additionally, the antioxidants and fiber in wheatgrass contribute to heart health by reducing inflammation and improving blood circulation.&nbsp;</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>5) Prevent Cancer&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass contains antioxidants like chlorophyll, flavonoids, and phenolic acids, which may help neutralize free radicals and reduce oxidative stress. This could potentially lower the risk of cancer by protecting cells from damage. Regular consumption of wheatgrass may contribute to overall cancer prevention by supporting detoxification and improving immune function.&nbsp;</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>6) Supports Heart Health</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass is rich in antioxidants, which help reduce oxidative stress and inflammation&mdash;two key factors in heart disease. Studies suggest that its high chlorophyll and flavonoid content may improve blood circulation and support healthy blood pressure levels. Additionally, wheatgrass may enhance oxygen flow and reduce arterial stiffness, promoting overall cardiovascular well-being.</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>7) Boosts Immune System</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass is packed with phytochemicals and antioxidants that help protect cells from damage. Its rich oligosaccharide content also supports immune function, making your body more resilient against infections.</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>8) Supports Weight Management</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass is low in calories and may aid in weight management by boosting metabolism. Its proteins and antioxidants help burn extra calories when combined with a healthy diet and active lifestyle.</span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>9) It Helps Keep Your Weight in Check</strong></span></p>\r\n\r\n<h3><span style=\"font-size:18px;\">Wheatgrass is very low in calories, making it a great choice if you&rsquo;re aiming to lose weight or maintain a healthy weight. But that&rsquo;s not all&mdash;wheatgrass may also support your weight management efforts by boosting metabolism, thanks to its proteins and antioxidants. In other words, it could help you burn a few extra calories while eating healthfully and staying active.</span><br />\r\n&nbsp;</h3>\r\n\r\n<h3><span style=\"font-size:24px;\"><strong>10) It Helps Eliminate Toxins</strong></span></h3>\r\n\r\n<h3><span style=\"font-size:18px;\">Studies suggest that the enzymes, amino acids, and chlorophyll in wheatgrass may help neutralize stored toxins and heavy metals in your body. But, remember, to support this effect, it&rsquo;s also important to follow a well-balanced, nutrient-dense diet that includes lean proteins, fruits, vegetables, and fiber.</span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>11) Maximizing the Benefits of Wheatgrass&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass can be consumed as juice or powder, but it&#39;s strong, earthy flavor may not be to everyone&#39;s liking. To make it more enjoyable, mix it with fruit juice, coconut water, or incorporate it into smoothies.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:18px;\">For those seeking a concentrated dose, wheatgrass shots are a convenient option. You can also add wheatgrass to your salads, dressings, green tea, or detox drinks to boost their nutritional value.&nbsp;</span></p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>12) Things to Consider Before Adding Wheatgrass to Your Diet&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">Wheatgrass is typically safe for most people, but it may cause mild side effects like nausea, loss of appetite, or constipation. To avoid potential discomfort, start with a small amount to observe how your body reacts. Once you understand how it affects you, you can gradually increase the dosage. Always consult with a healthcare professional before introducing any new supplement into your routine.</span></p>\r\n\r\n<p><span style=\"font-size:24px;\"><strong>Conclusion&nbsp;</strong></span></p>\r\n\r\n<p><span style=\"font-size:18px;\">The benefits of this superfood make it a must-have addition to a healthy lifestyle. From detoxification to boosting immunity and improving skin health, it offers a myriad of advantages. To experience its full potential, consider incorporating <strong>My Nutrify Herbal &amp; Ayurveda&#39; Wheatgrass Juice</strong> into your routine. Carefully crafted to retain all the goodness of fresh ingredients, it&rsquo;s the perfect choice for your wellness journey.</span></p>\r\n', 'Y');
INSERT INTO `blogs_master` (`BlogId`, `SubCategoryId`, `BlogTitle`, `BlogDate`, `PhotoPath`, `Description`, `IsActive`) VALUES
(20, 1, 'How to Balance Your Hormones Naturally', '2025-05-16', '9072.jpg', '<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Hormones are essential for maintaining homeostasis, ensuring that internal processes function efficiently.</span></span></span></p>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>What are hormones?</strong></span></span></span></h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span sans-serif=\"\"><img alt=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXclzk6oRDMTVHi7hiDcWK68d_IQvDx6MhX0-IShq3yEDkGHSxfi9pqBuejwDfb7Dj7eRKR8AXtkydhpkfTtcBQcI_wyxIHDVHVVqYbO00-mLm08YT7udBDvcIJC6yzUamh2EAszsw?key=J8xbdCC0fS8jRfNXZUIv7Un2\" src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXclzk6oRDMTVHi7hiDcWK68d_IQvDx6MhX0-IShq3yEDkGHSxfi9pqBuejwDfb7Dj7eRKR8AXtkydhpkfTtcBQcI_wyxIHDVHVVqYbO00-mLm08YT7udBDvcIJC6yzUamh2EAszsw?key=J8xbdCC0fS8jRfNXZUIv7Un2\" style=\"float: left; height: 329px; width: 602px;\" /></span></span></p>\r\n\r\n<p><br />\r\n&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span sans-serif=\"\"><span arial=\"\"><span style=\"color:black\">Hormones are chemical messengers that control important functions in your body, like metabolism, mood, energy, and growth. They are made by glands in the endocrine system and travel through the blood to tell different organs what to do. Key hormones like insulin, cortisol, thyroid hormones, estrogen, and testosterone help keep your body running smoothly. When they are balanced, you feel good, but if they get out of sync, you may feel tired, gain weight, or have mood swings. Eating healthy, staying active, managing stress, and getting enough sleep can help keep your hormones in balance.</span></span></span></span><br />\r\n<span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>What is a hormonal imbalance?</strong></span></span></span></p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><a c:=\"\" href=\"\"><br />\r\n<br />\r\n<br />\r\n<br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXcYrhd_jOwTl-J5I6wOj5VSe6KhsT0qWuY7uX3qdOD644lLjHFaeHUXOPdqDnRsOc7Y94yg60551WmGZAVHKMu-AIjCScOvhmvLpntVmB_yLuZmIQI4_YGd8k5WMrPlFYqxnkIg8g?key=J8xbdCC0fS8jRfNXZUIv7Un2\" style=\"width: 602px; height: 329px;\" /></a></span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">A hormonal imbalance occurs when your body produces either too many or too few of certain hormones. Think of hormones as tiny messengers that keep everything running smoothly. Even a small change in their levels can have a big impact on how you feel&mdash;affecting your mood, energy, and overall health. Sometimes these imbalances are just temporary, while other times they can be long-term. Some may need treatment to help you stay physically healthy, and others might not harm your body but can make daily life feel off. Keeping your hormones balanced is key to feeling your best every day.</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>What conditions are caused by hormonal imbalances?</strong></span></span></span><br />\r\n&nbsp;</h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Hormonal imbalances occur when your body produces too much or too little of certain hormones, leading to a range of symptoms and conditions</span></span></span></p>\r\n\r\n<ol>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Weight Fluctuations:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><span style=\"font-size:20px;\"> </span>An imbalance can affect metabolism and appetite, leading to unexpected weight gain or loss.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Diabetes:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> Disruption in insulin production or sensitivity can lead to impaired blood sugar regulation, increasing the risk of type 2 diabetes.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Irregular Periods:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> Hormonal imbalances can cause cycles to become unpredictable or even stop.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Mood Swings:</strong> </span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Changes in hormone levels may lead to sudden shifts in mood or feelings of anxiety and depression.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Skin Problems:</strong> </span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Acne and oily skin can result from too much or too little of certain hormones.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Thyroid Issues:</strong> </span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">An imbalance in thyroid hormones might cause fatigue, sensitivity to temperature, or other health concerns.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>PCOS:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> High levels of certain hormones can lead to polycystic ovary syndrome, affecting menstrual cycles and fertility.</span></span></span></li>\r\n</ol>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>What are the signs of hormone imbalance?</strong></span></span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:24px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Symptoms and Causes</strong></span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<h4><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Hormone imbalance symptoms that affect your metabolism:</strong></span></span></span><br />\r\n&nbsp;</h4>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Your body is like a symphony, with over 50 hormones playing unique roles to keep you in perfect tune. When one or more of these delicate notes go off-key, you might notice a mix of symptom be it unexpected fatigue, mood shifts, weight fluctuations, or sleep troubles. Remember, these signals might stem from various factors, not just hormone imbalances. If your daily rhythm starts to falter, it&rsquo;s always a good idea to check in with your healthcare provider to restore harmony.</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Hormonal imbalances can disrupt the delicate balance of your metabolism. Here&rsquo;s a professional overview of how these imbalances may affect your metabolic function:</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Weight Variations</span></span></span><br />\r\n	<br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Persistent Fatigue</span></span></span><br />\r\n	<br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Appetite Changes</span></span></span><br />\r\n	<br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Temperature Fluctuations</span></span></span><br />\r\n	<br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Digestive Irregularities</span></span></span><br />\r\n	<br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Mood Disturbances</span></span></span></li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>&nbsp;hormone imbalance symptoms for females:</strong></span></span></span><br />\r\n&nbsp;</h4>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Females can experience shifts in the delicate balance of sex hormones&mdash;primarily estrogen and progesterone from the ovaries, along with potential excesses of testosterone and other androgens. These fluctuations can unsettle the body&#39;s natural equilibrium, leading to a range of noticeable symptoms. Spotting these symptoms early can point to hidden problems and lead to the right treatment.Consider the following indicators of a sex hormone imbalance:</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Irregular Menstrual Cycles</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Mood Swings and Emotional Instability</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Acne and Other Skin Changes</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Unexplained Weight Fluctuations</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Hair Loss or Thinning</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Sleep Disturbances</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Vaginal Dryness and Discomfort</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>hormone imbalance symptoms for males:</strong></span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Males can experience an imbalance of testosterone and other sex hormones, leading to a range of symptoms that may affect physical, emotional, and reproductive health. Here are some key indic</span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">ators:</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Reduced Muscle Mass and Strength</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Increased Body Fat</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Decreased Libido</strong></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Erectile Dysfunction</strong></span></span></span></li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>What to drink to balance hormones?</strong></span></span></span></span><br />\r\n<span style=\"font-size:18px;\"><img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXfGIYwDsB1z1fpFdr5-SDQYncK2FR8riYxofc_V1zpDh8U4cvLEMsT9qUdP9EYGdQrs78El_Dhr07uiw49xwX4RFzhepETGyWPosQoB_km9GDIxXqJh23vnY8u86cWisFUOp7F2?key=J8xbdCC0fS8jRfNXZUIv7Un2\" style=\"height: 339px; width: 602px;\" /></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">In Ayurvedic and herbal traditions, certain drinks are believed to help balance hormones by soothing the body, reducing stress, and supporting natural detoxification. Here are some beverages that are often recommended:</span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Ashwagandha Tea:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> Known for its adaptogenic properties, ashwagandha helps lower cortisol levels, which may support balanced thyroid and reproductive hormones.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Shatavari Infusion:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> Traditionally used to support female reproductive health, shatavari is thought to help balance estrogen levels and promote overall well-being.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Turmeric-Ginger Tea:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> With powerful anti-inflammatory properties, this tea may help reduce systemic inflammation, contributing to improved hormone function.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Tulsi (Holy Basil) Tea:</strong> </span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Revered in Ayurveda, tulsi is believed to help manage stress and support the body&rsquo;s natural resilience, indirectly aiding hormone regulation.</span></span></span></li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">For all-around herbal support, try My Nutrify Herbal &amp; Ayurveda&rsquo;s She Care Plus Juice, a 100% natural blend featuring Daru Haldi, Rasut, Adusa, Nagar Motha, Chirayta, and Bel ka Fal.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">For all-around herbal support, try </span><span style=\"color:#1155cc\"><u>My Nutrify Herbal &amp; Ayurveda&rsquo;s She Care Plus Juice</u></span><span style=\"color:#000000\">, a 100% natural blend featuring Daru Haldi, Rasut, Adusa, Nagar Motha, Chirayta, Bel ka Fal, Bhilawa, Ashokchaal, Dhava, Kala Jeera, Sonth, Neel Kamal, Amla, Harad, Baheda, Aam ki Gutli, and Safed Chandana. These potent herbs work synergistically to support hormonal balance, detoxification, and overall female wellness&mdash;naturally and effectively.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>How can I check my hormone levels myself?</strong></span></span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">While you can&#39;t fully diagnose a hormonal imbalance on your own, there are professional methods you can use to monitor your hormone levels:</span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>At-Home Testing Kits:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> Many reputable companies now offer FDA-approved at-home test kits for hormones such as thyroid hormones, testosterone, estrogen, and cortisol. These kits typically involve collecting a blood or saliva sample, which is then sent to a certified laboratory for analysis.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Tracking Physical Symptoms:</strong></span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> Keeping a detailed record of symptoms&mdash;like changes in energy, mood, sleep, and weight&mdash;can provide valuable insights. However, this should complement, not replace, professional testing.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:20px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Consultation with a Healthcare Provider:</strong> </span></span></span><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Ultimately, the most accurate and comprehensive assessment of your hormone levels comes from working with a healthcare professional. They can interpret test results in the context of your overall health and recommend further tests or treatments if necessary.</span></span></span></li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:36px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Conclusion</strong></span></span></span></h3>\r\n\r\n<p><span style=\"font-size:18px;\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Balanced hormones are key to feeling energetic, emotionally stable, and healthy. A wholesome lifestyle with proper diet, sleep, and stress management plays a big role. For added support, </span><span style=\"color:#1155cc\"><u>My Nutrify Herbal &amp; Ayurveda&#39;s She Care Plus&nbsp; Juice</u></span><span style=\"color:#000000\"> offers a natural way to help balance female hormones with Ayurvedic herbs like Shatavari and Ashoka. Stay in tune with your body naturally.</span></span></span></p>\r\n\r\n<p><br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n&nbsp;</p>\r\n', 'Y'),
(21, 1, 'PCOD vs PCOS: Causes, Symptoms, Key Differences & Natural Treatment', '2025-05-16', '1977.jpg', '<h2><span style=\"font-size:22px\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#0f0f0f\"><strong>Table of contents</strong></span></span></span></h2>\r\n\r\n<ol>\r\n	<li style=\"list-style-type:lower-alpha\"><span style=\"font-size:16px\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Difference Between PCOD &amp; PCOS</span></span></span></li>\r\n	<li style=\"list-style-type:lower-alpha\"><span style=\"font-size:16px\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">PCOD &amp; PCOS Problem Symptoms</span></span></span></li>\r\n	<li style=\"list-style-type:lower-alpha\"><span style=\"font-size:16px\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">PCOD &amp; PCOS Causes</span></span></span></li>\r\n	<li style=\"list-style-type:lower-alpha\"><span style=\"font-size:16px\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Treatment Options</span></span></span></li>\r\n</ol>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">You&rsquo;ve been having irregular periods, weight gain, and unwanted facial hair for the past few months. Thinking it might be PCOD, you visited the doctor. But after some tests, you found out it&rsquo;s PCOS. Confused? Many people think PCOD and PCOS are the same, but they are not.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>PCOD</strong> is a condition where the ovaries make too many immature eggs. These eggs turn into small cysts, causing period problems and hormonal changes. It usually happens because of poor lifestyle habits and can be managed with better diet and exercise.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>PCOS</strong>, on the other hand, is a more serious hormone problem. In this, the ovaries make too many male hormones, which can stop periods, cause acne, weight gain, and even affect fertility. PCOS needs more care and long-term treatment.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">In Ayurveda, both conditions are seen as problems caused by an imbalance in the body. Ayurveda helps by using herbs, natural detox, and lifestyle changes to bring balance back. Herbs like Shatavari, Ashoka, Lodhra, and Guduchi help to manage hormones and improve women&rsquo;s health.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">Support hormonal balance naturally with </span></span><span style=\"color:#1c4587\"><span style=\"background-color:#ffffff\"><strong><u>My Nutrify Herbal &amp; Ayurveda&rsquo;s She Care Plus&nbsp; Juice</u></strong><u>.</u></span></span><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">a trusted Ayurvedic solution for PCOD and PCOS symptoms.without any side effects. Along with a healthy diet, yoga, and stress-free life, this can really help you feel better naturally.</span></span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:20px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><strong>Difference Between PCOD &amp; PCOS</strong></span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXcmuFNo0lhAZw4klnv7RfFMItP-1KTA3IjLG5d9ukHcwxwlhtCC7QrNi-e4MkIJgnB5egZ8dxpskRN99_RMuCmTG2Vgx5aQb3maJZKSvETo_WadVsq6KC2nTUOXAsy6toi0AWO3?key=J8xbdCC0fS8jRfNXZUIv7Un2\" style=\"height:391px; width:602px\" /></strong></span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>PCOD (Polycystic Ovarian Disease)</strong> and <strong>PCOS (Polycystic Ovary Syndrome)</strong> are two hormonal conditions that often get confused, but they are different. In PCOD, the ovaries release immature or partially developed eggs which can turn into small cysts. This happens mainly due to poor lifestyle, unhealthy eating habits, and stress. Women with PCOD may experience irregular periods, weight gain, acne, or unwanted facial hair. However, ovulation usually still happens, and fertility is not severely affected. PCOD is quite common and can often be managed with simple lifestyle changes, diet improvements, and natural remedies.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>PCOS</strong>, on the other hand, is a more serious hormonal disorder where the ovaries produce higher levels of male hormones (androgens), which can completely stop ovulation. This leads to missed periods, difficulty in getting pregnant, insulin resistance, and sometimes long-term health risks like type 2 diabetes and heart issues. PCOS affects not just the reproductive system but also metabolism and overall hormonal balance. It requires a more focused and long-term treatment approach, including medical care, diet control, and support through Ayurvedic or holistic healing.</span></span></span></span></p>\r\n\r\n<p><br />\r\n<br />\r\n<br />\r\n<span style=\"font-size:16px\"><img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXeiKErQSLYkakfu5ntOIXW3J_3dMnGq8OgUwUiqe1pMC2fraqGSMStdpbnxs2W8cRM1KFaoYsuVkWM0U3iXJxNAVxl-9H4y1kXLvaRmsuamPU-nGhJli0zzb7cT5XqSZ-7byZ-6OQ?key=J8xbdCC0fS8jRfNXZUIv7Un2\" style=\"height:206px; width:262px\" /></span></p>\r\n\r\n<p><span style=\"font-size:20px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><strong>PCOD &amp; PCOS Problem Symptoms</strong></span></span></span><br />\r\n<span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Irregular or Missed Periods:</strong> Hormonal imbalance often leads to unpredictable menstrual cycles, including missed or delayed periods.</span></span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Heavy Bleeding:</strong> A thicker uterine lining can cause periods to be heavier than usual.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Enlarged Ovaries:</strong> Fluid-filled cysts may develop, leading to ovarian enlargement and disrupted reproductive function.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Unwanted Hair Growth (Hirsutism):</strong> Increased male hormone levels can result in excess facial and body hair.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Weight Gain:</strong> Hormonal and metabolic imbalances commonly cause unexpected weight gain or obesity.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Acne and Oily Skin:</strong> Elevated androgens boost sebum production, leading to clogged pores and acne breakouts.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Hair Thinning or Baldness:</strong> A reduction in hair density, especially around the crown, is also a common symptom.</span></span></span></span></li>\r\n</ul>\r\n\r\n<p><br />\r\n<br />\r\n<span style=\"font-size:16px\"><img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXfJJ3QoX-WOsucOT-JE_u4VvzqSaMzJr8Plj2dxpybM4DZqWpm25r7N4X3kchYEVGHlBcFjKVJ9FNChj2fIIS1lCTE06jmLfXu7zO4G_AJP28nbKjGAFGvva-pbJz7ubrXbmbASGA?key=J8xbdCC0fS8jRfNXZUIv7Un2\" style=\"height:235px; width:277px\" /></span></p>\r\n\r\n<p><span style=\"font-size:20px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><strong>PCOD &amp; PCOS Causes</strong></span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Unhealthy Diet:</strong> Eating too much junk food or low-nutrient meals can disturb your body&rsquo;s balance and increase the risk of PCOS/PCOD.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Inactive Lifestyle:</strong> Sitting for long hours with little to no physical movement can make hormonal issues worse.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Insulin Resistance:</strong> When your body makes too much insulin, it can lead to higher male hormone levels and stop the normal release of eggs.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Excess Weight Gain:</strong> Putting on too many extra kilos can affect hormone levels and may trigger PCOS symptoms.</span></span></span></span></li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Hormonal Imbalance:</strong> Irregular levels of hormones like testosterone, androgens, or prolactin can prevent your ovaries from working properly.</span></span></span></span></li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:20px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><strong>Treatment Options</strong></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><strong><img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXeU99qrjBZQt0z1ksXynOmrPTm6RNWPt1S0O8kUkOYNbEwD5qKtZM05ipsloBKo_Tdi0WrC4AdLVSr7wZVYfgYu7Y5PjG8QDNZDelxI3pJ1Tv-sjEznEzx4LcZq0clste76BreL?key=J8xbdCC0fS8jRfNXZUIv7Un2\" style=\"height:400px; width:602px\" /></strong></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">Ayurvedic treatment for hormonal imbalances like PCOD and PCOS focuses on restoring the natural balance of the body through powerful herbs and mindful lifestyle practices. While approaches may vary between practitioners, the core principle remains the same: to support the body&#39;s healing systems using time-tested natural remedies. Among the most trusted herbs is Ashok Chhal, known in Ayurveda for promoting reproductive wellness. It helps regulate the menstrual cycle and supports healthy hormone levels. Another beneficial herb is Safed Chandan, prized for its cooling and calming effects. It not only helps soothe the nervous system but also addresses skin concerns such as acne, which are common in PCOS.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">These herbs, along with other Ayurvedic ingredients like Baheda, Soonth (dry ginger), Amla, and more, come together in </span></span><span style=\"color:#1c4587\"><span style=\"background-color:#ffffff\"><strong><u>My Nutrify Herbal &amp; Ayurveda&rsquo;s She Care Plus Juice</u></strong><u> </u>&mdash;</span></span><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">a holistic formulation crafted to naturally support women&rsquo;s health. It works gently yet effectively to ease symptoms of hormonal imbalance, helping women feel more balanced, energized, and at ease.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:20px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Conclusion&nbsp;</strong></span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:16px\"><span style=\"font-family:Poppins,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\">Although PCOD and PCOS often present alike, they arise from different hormonal imbalances and carry distinct health implications. With PCOD, simple diet and lifestyle tweaks can bring your cycle back on track, whereas PCOS usually calls for a more comprehensive, long-term approach. Embracing Ayurvedic therapies, consistent self-care habits, and supportive tonics like She Care Plus Juice can help restore harmony and vitality, so begin your journey today and commit to a balanced, natural path to wellness.</span></span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n', 'Y');
INSERT INTO `blogs_master` (`BlogId`, `SubCategoryId`, `BlogTitle`, `BlogDate`, `PhotoPath`, `Description`, `IsActive`) VALUES
(22, 0, 'Top 10 Powerful Ayurvedic Herbs to Enhance Your Health and Wellbeing', '1970-01-01', '8547.jpg', '<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:22pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXcdDdRtcPNUAlyDMZmYTBmgo_qx_psEpuoFg19hHn2sLQdgoE1w6UWKxf0kYvxcxgagpVTiN-2jdcxiPDORQdZUysjzPbIKqxh9fKlKbnVci3pKO1zgnmK9_la14GNflrE4ShsxSQ?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:401px; width:602px\" /></strong></span></span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">In today&#39;s fast-moving world, maintaining good health often becomes a challenge. Work pressure, poor eating habits, and lack of rest can lead to stress and imbalance in the body. This is where Ayurveda, the ancient Indian system of natural healing, offers simple and effective solutions. By adding </span></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#1155cc\"><span style=\"background-color:#ffffff\"><strong><u>Ayurvedic herbs</u></strong></span></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"> to your daily routine, you can naturally support your body, boost energy, and strengthen immunity &mdash; all without side effects.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Ayurvedic herbs like Ashwagandha, Tulsi, Giloy, and Triphala are known for their powerful healing properties. Rich in antioxidants and anti-inflammatory compounds, these herbs help reduce stress, improve digestion, support the immune system, and promote overall well-being. The best part? These herbs are easily available, affordable, and suitable for people of all ages.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Integrating Ayurveda into daily life doesn&rsquo;t require a big change. Just a few small habits &mdash; like drinking herbal teas, adding powdered herbs to your meals, or taking herbal supplements and Ayurvedic Juices&nbsp; &mdash; can bring long-term health benefits. Ayurveda believes in balance, and these daily herbs help your body stay in harmony, naturally.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>&nbsp;The Role of Herbs in Ayurveda</strong></span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXezt4K3AApZwHK_xG7KDiuuIVFmeYVWx_sYhE6yU-rXrwNm_HucTG7h4QIG876ZjbJonaPNMcFZ_TcBHNlaW5sUUfWiu8KLlYUDO8SOl6NsZoz5cZWaQIz0oSN3V8XivlVhKCBfeQ?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:337px; width:602px\" /></strong></span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Ayurveda is an ancient healing system from India that focuses on keeping the body and mind in balance. A key part of this practice is the use of natural herbs. Well-known herbs like Ashwagandha, Tulsi, and Turmeric are widely used in Ayurveda to support the immune system, reduce stress, and fight inflammation. These herbs are gentle, effective, and have been trusted for centuries.</span></span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">What makes Ayurvedic herbs special is that they work with the body&rsquo;s natural processes. They can be taken in many forms&mdash;like teas, powders, oils, or supplements&mdash;and are easy to include in your daily routine. Whether you&#39;re looking to boost energy, improve digestion, or just stay healthy, using Ayurvedic herbs regularly can help you feel better naturally and maintain overall wellness.</span></span></span></span></p>\r\n\r\n<h2><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>&nbsp;Ayurvedic Doshas and Their Importance in Mind-Body Balance</strong></span></span></span></span></h2>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">In Ayurveda, the ancient Indian system of natural healing, it is believed that the entire universe&mdash;including the human body&mdash;is made up of five basic elements: earth, water, fire, air, and space. These elements combine within the body to form three main energy types, known as doshas: </span></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>Vata, Pitta, and Kapha</strong></span></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">. Each person has a unique combination of these doshas, which shapes their physical, mental, and emotional characteristics.</span></span></span></span></p>\r\n\r\n<h2><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>Ayurvedic Doshas and Their Importance in Mind-Body Balance</strong></span></span></span></span></h2>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">In Ayurveda, the ancient Indian system of natural healing, it is believed that the entire universe&mdash;including the human body&mdash;is made up of five basic elements: earth, water, fire, air, and space. These elements combine within the body to form three main energy types, known as doshas: Vata, Pitta, and Kapha. Each person has a unique combination of these doshas, which shapes their physical, mental, and emotional characteristics.</span></span></span></span></p>\r\n\r\n<h3><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>ðŸ”¹ Vata Dosha :</strong></span></span></span></span></h3>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Vata governs all movement in the body, including breathing, circulation, and nerve impulses. When balanced, Vata brings creativity, energy, and flexibility. If Vata becomes imbalanced, it may lead to anxiety, dry skin, constipation, or restlessness. To balance Vata, it&rsquo;s helpful to:</span></span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Eat warm, soft, and nourishing foods</span></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Follow a daily routine</span></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Practice calming activities like meditation or gentle yoga</span></span></span></span><br />\r\n	&nbsp;</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>ðŸ”¸ Pitta Dosha :</strong></span></span></span></span></h3>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Pitta is responsible for digestion, metabolism, and internal heat. When in balance, it gives sharp focus, confidence, and motivation. But an excess of Pitta can cause anger, acidity, skin rashes, or inflammation. To keep Pitta balanced:</span></span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Avoid overly spicy or fried foods</span></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Stay cool and hydrated</span></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Choose calming exercises like swimming or slow yoga</span></span></span></span><br />\r\n	&nbsp;</li>\r\n</ul>\r\n\r\n<h3><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>ðŸ”¹ Kapha Dosha :&nbsp;</strong></span></span></span></span></h3>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Kapha provides structure, stability, and lubrication to the body. It helps maintain healthy tissues, strong immunity, and emotional steadiness. Balanced Kapha shows up as compassion, patience, and strength, but an imbalance may lead to tiredness, extra weight, or blocked sinuses. To balance Kapha:</span></span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Eat light, warm, and flavorful foods</span></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Stay active and avoid sleeping during the day</span></span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Practice energizing breathing techniques and regular exercise</span></span></span></span><br />\r\n	&nbsp;</li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\">Everyone has all three doshas, but usually one or two are more dominant. The goal of Ayurveda is to maintain a healthy balance between these energies for overall well-being. Ayurvedic herbs such as Ashwagandha, Triphala, Brahmi, and Tulsi are commonly used to support this balance. By understanding your personal dosha type and recognizing imbalances, you can adjust your diet, lifestyle, and use of herbs to restore harmony in both body and mind.</span></span></span></span></p>\r\n\r\n<h2><span style=\"font-size:20pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>10 of the Most Powerful Ayurvedic Herbs</strong></span></span></span></span></h2>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>1. Ashwagandha &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Stress Buster</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXczdrULV79QzWdMYBgLcAQg1QjIKBywo-9OOuDjUXYxBRoACidMNI2o4O9BE0SnmbnM5tPJlMhIxr0GWHZiX4dcevjQnw8lUHdqVtFnwsq_OfNiNbfzmmHVQlBHUX6oZoeP1-8GyA?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:221px; width:313px\" /></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Ashwagandha, also known as Indian Ginseng, is a well-known Ayurvedic herb. It helps </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>reduce stress</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>improve sleep</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>boost immunity</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, and </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>support fertility</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">. It is rich in </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>antioxidants</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> and helps calm the body by lowering stress hormones.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">It also improves </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>mental health</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, reduces </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>anxiety</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, and boosts </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>energy and strength</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">. You can take it in </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>powder</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>capsule</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, or mix it into </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>juices or milk</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Ashwagandha Milk (Bedtime Drink)</strong></span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Mix </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>1/2 tsp Ashwagandha powder</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> in </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>1 cup warm milk</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Add </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>honey</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> or </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>cinnamon</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> (optional).</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Drink </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>at night</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> to relax and sleep better.</span></span></span></li>\r\n</ul>\r\n\r\n<p><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>2. Tulsi (Holy Basil) &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Immunity Booster</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXeOyqBEKvCq1hNmRcfJbCyc102CT-GpsIN6wCibv7RgBLVaRGikaYwW_40sI_PICCCMoLvWaORkMGXwB7PLz11RZuzP5E9W4ETdrm3r0DRHQ6HYg3mAefPYiJBMuCG827qkG180Rg?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:261px; width:262px\" /></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Tulsi, also called Holy Basil, is a sacred herb in Ayurveda known for its power to support the immune system and balance Vata and Kapha doshas. It has antibacterial, anti-diabetic, and anti-inflammatory effects. Tulsi is great for easing sore throat, cold, and respiratory issues. It&rsquo;s easy to grow at home, so you can always have fresh leaves ready.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Tulsi is also helpful for reducing stress and anxiety, promoting calmness, and improving mental clarity. It&rsquo;s been used for thousands of years in Ayurveda for both body and mind balance.</span></span></span></p>\r\n\r\n<h3><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Tulsi Tea (Quick Steps)</strong></span></span></span></h3>\r\n\r\n<ol>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Boil 1 cup water.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Add 5&ndash;6 Tulsi leaves.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Steep for 5 minutes.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Strain and drink warm.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Add honey or lemon (optional).</span></span></span></li>\r\n</ol>\r\n\r\n<p><br />\r\n<br />\r\n<br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>3. Turmeric (Haldi) &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Natural Healer</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXfZDHycS_VZj5aYT8XHulX7miJz5n6BxPhiDh7L_soYodOAKJ9_kWd54ur5BhkwYVE7gAM-FIH3yukFbPA_179swx0tdaJm2VOrGs8sBdADYBzLB7ZEe55BKdKmmYgDgmQzZZA3Mw?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:206px; width:318px\" /></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Turmeric is a well-known Ayurvedic spice with many health benefits. It has a natural compound that helps reduce swelling (inflammation), improves digestion, strengthens the immune system, and keeps your skin healthy. People often use turmeric to fight infections and help the body recover naturally.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Turmeric is also great for your skin. When made into a paste and used as a face mask, it can help reduce pimples, brighten your face, and give your skin a healthy glow. This is why turmeric is used in many beauty and skincare routines.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Turmeric Milk (Golden Milk)&nbsp;</strong></span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Warm 1 cup of milk in a pan.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Add turmeric and black pepper.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Mix well and heat for 3&ndash;5 minutes (don&rsquo;t boil).</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Turn off the heat.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Add honey if you like.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Drink warm, best at night.</span></span></span></li>\r\n</ul>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>4. Amla &ndash; A Natural Body Booster</strong></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXenB-81WZiSyH6YRl6lo52mNdeKVi3jfsTvXrB1B4cDAOYXhrLS6cOJfyGHTVdixiuCYsJhs1ZENoDRNBoU0UqnKRql_NUORcWt6rLIaQz4kjljKpo2zMwELFovX-0tZQm50DlzAQ?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:280px; width:281px\" /><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Amla, or Indian Gooseberry, is packed with Vitamins C, A, and E, along with other powerful nutrients. It helps refresh your body, supports heart and lung health, and keeps your skin and hair glowing. In Ayurveda, it&rsquo;s a popular ingredient in Chyavanprash, known for boosting immunity.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Amla supports digestion, helps remove toxins, and improves how your body absorbs nutrients. It strengthens the immune system and protects against common infections. For hair, </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>amla oil</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> helps reduce hair fall and adds shine and strength. Drinking </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#1155cc\"><strong><u>amla juice</u></strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> daily is also great for managing blood sugar and supporting weight loss.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Prefer a pure and natural option? Try [My Nutrify Amla Juice]</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> &ndash; made from fresh, high-quality amlas to give your body daily nourishment in the easiest way!</span></span></span></p>\r\n\r\n<h3><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>How to Use</strong></span></span></span></h3>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Mix </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>20&ndash;30 ml</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> of </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#1155cc\"><u>My Nutrify herbal &amp; Ayurveda&rsquo;s&nbsp; Wild Amla Juice</u></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> with a glass of </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>lukewarm water</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">.</span></span></span><br />\r\n<span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Drink </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>once daily before breakfast</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> in the morning.</span></span></span></p>\r\n\r\n<p><br />\r\n<br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>5. </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><span style=\"background-color:#ffffff\"><strong>Triphala </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>&ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Digestive Cleanser</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXeWc4jcyI2YtrIh30IZ-3IELupMu5ip5r5ULgYV31jSkSv2eL7tqN0wEXwim1WV0biyn3WCYSI_xnmx5GNI6zSx5GIZvhR2njg5nTgWOHewF-nHXGcZD8zi1HhrjLZBNd1o3Rmo7A?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:242px; width:312px\" /><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Triphala is a traditional Ayurvedic blend of three fruits&mdash;Amla, Haritaki, and Bibhitaki. It helps improve digestion, supports detox, and boosts immunity. Triphala is often used for relieving constipation, cleansing the gut, and improving overall health. Taking it regularly in small amounts can be very helpful, but too much may cause stomach upset. Always follow the proper dose or ask a healthcare expert.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Use As a Tea&nbsp;</strong></span></span></span></p>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Boil 1 cup of water.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Add 1 teaspoon Triphala powder.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Let it sit for 5&ndash;10 minutes.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Strain if needed.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Drink warm.</span></span></span></li>\r\n</ul>\r\n\r\n<p><br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>6. Brahmi &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Brain Tonic</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXflX2IF3RzcWSyzpKUWbe96MQdK2Dh23PeEtAj97RFQR7anfpJfN5-qr6su5Kw22TU6j3z_9pTOXpZAgQZNmG2MWmcRhEbAeawpmmeBokax4xKQsP7wLGpywVcgO1E1bUD3EnMK6Q?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:280px; width:280px\" /></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Brahmi is an Ayurvedic herb that helps your brain work better. It improves memory, focus, and keeps your mind calm. It&rsquo;s great for people who feel stressed or forget things easily. Brahmi also helps you sleep better and keeps your brain healthy as you get older.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Brahmi Tea Recipe:</strong></span></span></span></p>\r\n\r\n<ol>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Boil 1 cup of water.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Add 1 teaspoon of Brahmi leaves or powder.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Let it sit for 5&ndash;7 minutes.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Strain and drink warm.</span></span></span><br />\r\n	&nbsp;</li>\r\n</ol>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">You can drink this tea once a day, morning or evening is best.</span></span></span><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>7. Giloy &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Immunity Enhancer</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXeHYFsG8pvjIgEhD_lLlYCQ4JhKyXEs_cloLrLkbvrBZHKkXU5VYEXN0w7zw86nJXdV_kh__5V9Y_ZmyyFxmAXNaoZI2pcQh9cyJr21HS_vjvOUID7oQkRwQ1Oez_aT8le3M8outw?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:245px; width:327px\" /><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Giloy is a powerful herb in Ayurveda, known for making the immune system strong. It helps the body fight off common colds, fevers, and infections. Giloy also keeps the body cool and supports healthy digestion. It&rsquo;s often called &ldquo;Amrita&rdquo; in Ayurveda, which means &ldquo;root of immortality,&rdquo; because of its many health benefits. People who feel tired or get sick often can really benefit from using Giloy.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Giloy Juice Recipe:</strong></span></span></span></p>\r\n\r\n<ol>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Take 2&ndash;3 tablespoons of Giloy juice.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Mix it with half a glass of water.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Drink it </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>before breakfast</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> in the morning.</span></span></span></li>\r\n</ol>\r\n\r\n<p><br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>8. Shatavari &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>Women&rsquo;s Wellness Herb</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXdL0JD_K4q1_L0f3M1yf1F-q9D7wyBpq435n4lxlHM0MXmKoGhQ9wMYsPUhojOF7Yag9AhPXmow0tyZ6AgJudnaYBVMb7nZ6cBwasJGXu2LsvjuCrvgrwhITfZicVWMf59jY1EE?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:228px; width:298px\" /></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Shatavari is an Ayurvedic herb that is very good for women&rsquo;s health. It helps balance hormones, supports periods, and improves fertility. It is also helpful during menopause. Shatavari gives energy, reduces stress, and keeps the body strong. Women can use it daily to stay healthy and feel better.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Shatavari Milk:</strong></span></span></span></p>\r\n\r\n<ol>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Take 1 teaspoon of Shatavari powder.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Mix it in 1 glass of warm milk.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Drink it </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>at night before sleep</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">.</span></span></span></li>\r\n</ol>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Try My Nutrify Herbal and Ayurveda&rsquo;s She Care Plus&nbsp; Juice</strong></span></span></span><br />\r\n<span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#1155cc\"><u>My Nutrify Herbal&amp; Ayurveda&#39;s&nbsp; She CarePlus&nbsp; Juice</u></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> is good for women&rsquo;s health. It helps balance hormones, gives energy, and supports periods and menopause.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>How to Use:</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> Drink </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>20&ndash;30 ml</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> with water, </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>before eating</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">, once or twice a day.</span></span></span></p>\r\n\r\n<p><br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>9. Neem &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Skin Purifier</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXdmZgG6LxU-TzsZWJ3LbjAXMobdlbCfL1GQBiG6yrgMvfhDrz00SOiDopVzB7LKHaq0WoKDFkoqG2dWHBiSGmhVLnUnGZylgDum1fBYyvzHnTkJaVLIHqUwU_w450S-9-74jIG03w?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:263px; width:263px\" /><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Neem is a powerful Ayurvedic herb known for its cleansing and purifying effects, especially for the skin. It helps fight acne, pimples, and other skin problems by removing toxins from the blood. Neem also supports a healthy immune system and keeps the digestive system clean. Because of its anti-bacterial and anti-inflammatory properties, neem is often used in skin care and health remedies.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Neem Tea Steps:</strong></span></span></span></p>\r\n\r\n<ol>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Boil 4&ndash;5 neem leaves in 1 cup water (5 mins)</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Strain the water</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:decimal\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Drink warm once a day</span></span></span></li>\r\n</ol>\r\n\r\n<h3><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>Try My Nutrify Herbal &amp; Ayurveda&rsquo;s&nbsp; Karela Neem Jamun Juice</strong></span></span></span></h3>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">For daily wellness,</span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#1155cc\"><u> </u></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#1155cc\"><strong><u>My Nutrify Karela Neem Jamun Juice</u></strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> is a great choice. It supports skin health, helps control blood sugar, and keeps your body clean from the inside. Drink 20&ndash;30 ml daily with water, preferably on an </span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>empty stomach in the morning</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"> for best results.</span></span></span></p>\r\n\r\n<p><br />\r\n<br />\r\n<br />\r\n<br />\r\n<br />\r\n&nbsp;</p>\r\n\r\n<h3><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong>10. Guggul &ndash; </strong></span></span></span></span><span style=\"font-size:17pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#191919\"><span style=\"background-color:#ffffff\"><strong><em>The Cholesterol Balancer</em></strong></span></span></span></span></h3>\r\n\r\n<p><br />\r\n<img src=\"https://lh7-rt.googleusercontent.com/docsz/AD_4nXcGdXZN7TMY-jngtNxqFFL-ZifGdSCir3NrnprN-QWAXbNTXwV955BgQxdAU1hzVEtIIMs9E3DkB3C-eUwvKJ5KeCT9eOxpL0KikI1p2_ihZs80kvKumlXt6_7jN3ufSW1URFgJBA?key=z5qxnDgZAcoT9x3UQ_cnWfx7\" style=\"height:286px; width:286px\" /><br />\r\n&nbsp;</p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Guggul is a well-known herb in Ayurveda used to keep your heart healthy and manage cholesterol levels. It comes from the gum of a small tree and helps lower bad cholesterol (LDL) and increase good cholesterol (HDL). It also supports weight loss and keeps your metabolism active.</span></span></span></p>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Guggul is also good for cleaning the blood and keeping the body balanced, especially for people with slow digestion or extra fat (Kapha dosha).</span></span></span></p>\r\n\r\n<h3><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>How to Use Guggul:</strong></span></span></span></h3>\r\n\r\n<ul>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Take Guggul after food.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Mix the powder with warm water or honey.</span></span></span><br />\r\n	&nbsp;</li>\r\n	<li style=\"list-style-type:disc\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\">Use 1&ndash;2 times a day.</span></span></span></li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong>For a simple option, try </strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#1155cc\"><strong><u>My Nutrify Herbal &amp; Ayurveda&rsquo;s Cholesterol Care Juice</u></strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#000000\"><strong> &ndash; it has Guggul and other helpful herbs to support heart health.</strong></span></span></span></p>\r\n\r\n<p style=\"margin-left:40px; margin-right:40px\"><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#0000ff\"><strong>Tip:</strong></span></span></span><span style=\"font-size:15pt\"><span style=\"font-family:Arial,sans-serif\"><span style=\"color:#0000ff\"> For better results, eat healthy and stay active along with taking the juice.</span></span></span></p>\r\n\r\n<p>&nbsp;</p>\r\n', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `CustomerId` int NOT NULL COMMENT 'Reference to customer_master.CustomerId',
  `ProductId` int NOT NULL COMMENT 'Reference to product_master.ProductId',
  `Quantity` int NOT NULL DEFAULT '1',
  `Price` decimal(10,2) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_master`
--

CREATE TABLE `category_master` (
  `CategoryId` int NOT NULL,
  `CategoryName` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_master`
--

INSERT INTO `category_master` (`CategoryId`, `CategoryName`) VALUES
(1, 'Juices'),
(7, 'Resin');

-- --------------------------------------------------------

--
-- Table structure for table `cod_payments`
--

CREATE TABLE `cod_payments` (
  `PaymentId` int NOT NULL,
  `OrderId` int DEFAULT NULL,
  `Date` int DEFAULT NULL,
  `Amount` text,
  `Status` text NOT NULL,
  `ReceivedBy` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_bookings`
--

CREATE TABLE `consultation_bookings` (
  `BookingId` int NOT NULL,
  `BookingNumber` varchar(20) NOT NULL,
  `DoctorId` int NOT NULL,
  `PatientName` varchar(100) NOT NULL,
  `PatientEmail` varchar(100) NOT NULL,
  `PatientPhone` varchar(15) NOT NULL,
  `PatientAge` int NOT NULL,
  `PatientGender` enum('Male','Female','Other') NOT NULL,
  `HealthConcerns` text NOT NULL,
  `CurrentMedications` text,
  `PreferredDate` date NOT NULL,
  `PreferredTime` time NOT NULL,
  `ConsultationDate` date NOT NULL,
  `ConsultationTime` time NOT NULL,
  `ConsultationFee` decimal(10,2) NOT NULL,
  `PaymentStatus` enum('Pending','Paid','Failed','Refunded') DEFAULT 'Pending',
  `PaymentId` varchar(100) DEFAULT NULL,
  `BookingStatus` enum('Pending','Confirmed','Completed','Cancelled','Rescheduled') DEFAULT 'Pending',
  `ConsultationMode` enum('Phone','Video','In-Person') DEFAULT 'Phone',
  `Notes` text,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_history`
--

CREATE TABLE `consultation_history` (
  `HistoryId` int NOT NULL,
  `BookingId` int NOT NULL,
  `DoctorId` int NOT NULL,
  `PatientName` varchar(100) NOT NULL,
  `PatientPhone` varchar(15) NOT NULL,
  `ConsultationDate` date NOT NULL,
  `ConsultationTime` time NOT NULL,
  `Duration` int DEFAULT NULL COMMENT 'Duration in minutes',
  `Diagnosis` text,
  `Prescription` text,
  `RecommendedProducts` text,
  `FollowUpRequired` tinyint(1) DEFAULT '0',
  `FollowUpDate` date DEFAULT NULL,
  `DoctorNotes` text,
  `PatientFeedback` text,
  `Rating` int DEFAULT NULL COMMENT 'Rating from 1-5',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_settings`
--

CREATE TABLE `consultation_settings` (
  `SettingId` int NOT NULL,
  `SettingKey` varchar(50) NOT NULL,
  `SettingValue` text NOT NULL,
  `Description` text,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultation_settings`
--

INSERT INTO `consultation_settings` (`SettingId`, `SettingKey`, `SettingValue`, `Description`, `UpdatedAt`) VALUES
(1, 'default_consultation_fee', '200.00', 'Default consultation fee in INR', '2025-07-23 09:11:56'),
(2, 'default_slot_duration', '30', 'Default consultation slot duration in minutes', '2025-07-23 09:11:56'),
(3, 'advance_booking_days', '30', 'How many days in advance can patients book', '2025-07-23 09:11:56'),
(4, 'consultation_start_time', '09:00:00', 'Default consultation start time', '2025-07-23 09:11:56'),
(5, 'consultation_end_time', '18:00:00', 'Default consultation end time', '2025-07-23 09:11:56'),
(6, 'phone_consultation_enabled', '1', 'Enable phone consultations', '2025-07-23 09:11:56'),
(7, 'video_consultation_enabled', '0', 'Enable video consultations', '2025-07-23 09:11:56'),
(8, 'booking_confirmation_sms', '1', 'Send SMS confirmation for bookings', '2025-07-23 09:11:56'),
(9, 'booking_confirmation_email', '1', 'Send email confirmation for bookings', '2025-07-23 09:11:56'),
(10, 'reminder_sms_hours', '24', 'Send reminder SMS X hours before consultation', '2025-07-23 09:11:56'),
(11, 'cancellation_hours', '24', 'Minimum hours before consultation to allow cancellation', '2025-07-23 09:11:56');

-- --------------------------------------------------------

--
-- Table structure for table `consultation_slots`
--

CREATE TABLE `consultation_slots` (
  `SlotId` int NOT NULL,
  `DoctorId` int NOT NULL,
  `SlotDate` date NOT NULL,
  `SlotTime` time NOT NULL,
  `Duration` int DEFAULT '30' COMMENT 'Duration in minutes',
  `IsBooked` tinyint(1) DEFAULT '0',
  `BookingId` int DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT '1',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_master`
--

CREATE TABLE `contact_master` (
  `ContactId` int NOT NULL,
  `MobileNo` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Primary mobile number',
  `LandLineNo` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Landline number',
  `EmailSales` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Sales email address',
  `EmailSupport` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Support email address',
  `MapURL` text COLLATE utf8mb4_general_ci COMMENT 'Google Maps URL',
  `GST` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'GST number',
  `CIN` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Company Identification Number',
  `PhotoPath` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Logo image path',
  `WhatsAppNo` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'WhatsApp number',
  `CompanyName` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Company name',
  `Address` text COLLATE utf8mb4_general_ci COMMENT 'Company address',
  `City` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'City',
  `State` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'State',
  `Pincode` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Postal code',
  `Country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'India' COMMENT 'Country',
  `Website` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Website URL',
  `IsActive` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT 'Y' COMMENT 'Active status',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_master`
--

INSERT INTO `contact_master` (`ContactId`, `MobileNo`, `LandLineNo`, `EmailSales`, `EmailSupport`, `MapURL`, `GST`, `CIN`, `PhotoPath`, `WhatsAppNo`, `CompanyName`, `Address`, `City`, `State`, `Pincode`, `Country`, `Website`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, '+91 9834243754', NULL, 'sales@mynutrify.com', 'support@mynutrify.com', NULL, NULL, NULL, 'logo.png', '919876543210', 'My Nutrify Herbal And Ayurveda', 'S.NO.31/32, 1st Floor, Old Mumbai Pune Road, Dapoli (Maharashtra)', 'Pune', 'Maharashtra', '411012', 'India', 'https://mynutrify.com', 'Y', '2025-07-23 07:40:13', '2025-07-23 07:40:13');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','read','replied') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contact form submissions';

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `phone`, `email`, `subject`, `message`, `ip_address`, `user_agent`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Srijeet Shikalgar', '8208593432', 'srijeetshikalgar00@mail.com', 'asasd', 'Aad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'replied', '2025-07-19 11:41:18', '2025-07-21 10:55:16'),
(2, 'Srijeet Shikalgar', '8208593432', 'srijeetshikalgar00@mail.com', 'asd', 'asd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'replied', '2025-07-19 11:54:14', '2025-07-21 10:55:14'),
(3, 'Srijeet Shikalgar', '8208593432', 'srijeetshikalgar00@mail.com', '', 'sadasd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'replied', '2025-07-19 12:15:04', '2025-07-23 06:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `customer_id` int NOT NULL,
  `discount_type` enum('fixed','percentage','free_shipping') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `is_used` tinyint(1) DEFAULT '0',
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `coupon_code`, `customer_id`, `discount_type`, `discount_value`, `min_order_amount`, `is_active`, `is_used`, `used_at`, `created_at`, `expires_at`) VALUES
(1, 'DISC1000317530006354419', 10003, 'fixed', 50.00, 0.00, 1, 0, NULL, '2025-07-20 08:37:15', '2025-08-19 08:37:15');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int NOT NULL,
  `coupon_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_applied` decimal(10,2) NOT NULL,
  `order_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track coupon usage by customers';

-- --------------------------------------------------------

--
-- Table structure for table `customer_address`
--

CREATE TABLE `customer_address` (
  `AdrId` int NOT NULL,
  `CustomerId` int DEFAULT NULL,
  `Address` text COLLATE utf8mb4_general_ci,
  `Landmark` text COLLATE utf8mb4_general_ci,
  `State` text COLLATE utf8mb4_general_ci,
  `City` text COLLATE utf8mb4_general_ci,
  `PinCode` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_address`
--

INSERT INTO `customer_address` (`AdrId`, `CustomerId`, `Address`, `Landmark`, `State`, `City`, `PinCode`) VALUES
(1, 1, 'Guruvar Peth New address', 'Near Priyadarshini Hotel', 'Maharashtra', 'Sangli', '416410'),
(2, 2, 'Wasagde road , Nandre', 'Nandre', 'Maharastra', 'Sangli', '416416'),
(3, 6, 'Near Nadaf Galli ', 'Priyadarshini Hotel', 'Maharashtra', 'Miraj', '416416'),
(4, 12, 'Nandre Near Q Mart', NULL, 'Maharashtra', 'Nandre', '416416');

-- --------------------------------------------------------

--
-- Stand-in structure for view `customer_available_coupons`
-- (See below for the actual view)
--
CREATE TABLE `customer_available_coupons` (
`coupon_code` varchar(50)
,`coupon_name` varchar(100)
,`customer_id` int
,`description` text
,`discount_type` enum('fixed','percentage')
,`discount_value` decimal(10,2)
,`expires_at` timestamp
,`id` int
,`is_reward_coupon` tinyint(1)
,`max_discount_amount` decimal(10,2)
,`minimum_order_amount` decimal(10,2)
,`points_required` int
,`wallet_status` enum('active','used','expired','cancelled')
);

-- --------------------------------------------------------

--
-- Table structure for table `customer_coupons`
--

CREATE TABLE `customer_coupons` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `coupon_id` int NOT NULL,
  `redeemed_from_points` tinyint(1) DEFAULT '0',
  `points_used` int DEFAULT NULL,
  `redemption_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','used','expired','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `used_at` timestamp NULL DEFAULT NULL,
  `order_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Order where coupon was used',
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer coupon wallet for redeemed coupons';

-- --------------------------------------------------------

--
-- Table structure for table `customer_master`
--

CREATE TABLE `customer_master` (
  `CustomerId` int NOT NULL,
  `Name` text COLLATE utf8mb4_unicode_ci,
  `MobileNo` text COLLATE utf8mb4_unicode_ci,
  `Email` text COLLATE utf8mb4_unicode_ci,
  `Pass` text COLLATE utf8mb4_unicode_ci,
  `OTP` text COLLATE utf8mb4_unicode_ci,
  `CreationDate` text COLLATE utf8mb4_unicode_ci,
  `UpdateDate` text COLLATE utf8mb4_unicode_ci,
  `IsActive` text COLLATE utf8mb4_unicode_ci,
  `whatsapp_opt_in` tinyint(1) DEFAULT '1' COMMENT '1=opted in, 0=opted out',
  `whatsapp_opt_out` tinyint(1) DEFAULT '0' COMMENT '1=opted out, 0=opted in',
  `last_whatsapp_sent` timestamp NULL DEFAULT NULL COMMENT 'Last WhatsApp message sent timestamp',
  `DateOfBirth` date DEFAULT NULL COMMENT 'Customer birth date for birthday wishes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_master`
--

INSERT INTO `customer_master` (`CustomerId`, `Name`, `MobileNo`, `Email`, `Pass`, `OTP`, `CreationDate`, `UpdateDate`, `IsActive`, `whatsapp_opt_in`, `whatsapp_opt_out`, `last_whatsapp_sent`, `DateOfBirth`) VALUES
(2, 'Shadab Mulani', '9876543210', 'shadabmulani@gmail.com', '$2y$12$OIQKETkl74F3hHFp9Z6HzeawsKppvOIP4/5EkGePRi4QhKNm.61Ci', '481226', '20 Feb 2025 13:05', '20 Feb 2025 13:14', 'Y', 1, 0, NULL, NULL),
(3, 'Shadab Mulani', '7456123078', 'shadabmulani@gmail.com', '$2y$12$OkVRB9mUkB8B.56OGNE3u.QbnZI.7JxP2aJM9hFbkyC2Xz8xS/vtq', '286882', '20 Feb 2025 13:13', '20 Feb 2025 13:14', 'Y', 1, 0, NULL, NULL),
(4, 'Affan', '8459945385', 'affan@purenutritionco.com', '$2y$12$SbLsWqguQYJtsSjIhS27rulhYPE8VKzpNKMNKQ4SrFULLg9Od8iSa', '621896', '25 Feb 2025 18:04', NULL, 'N', 1, 0, NULL, NULL),
(9, 'Muddassar Kazi', '8329566751', 'iammaddyk@gmail.com', '$2y$12$Tpvy2hfueAfvwTl4lNmzqOoewoNtpQjs42h6M4ybQO5VOM382izHK', '565040', '5 Mar 2025 20:43', '5 Mar 2025 20:43', 'Y', 1, 0, NULL, NULL),
(12, 'Sahil Mujawar', '8830135758', 'aftabmujawar2308@gmail.com', '$2y$12$3Ym5.UE27wkbQDZvdkBWw.k7AzGDFIvSN/Y.yh8CaOfz8tOm/u8BG', '589982', '10 Mar 2025 16:10', '4 Apr 2025 16:52', 'Y', 1, 0, NULL, NULL),
(13, 'Akash kamble', '9503046790', 'akashkamble547@gmail.com', '$2y$12$k/fGjaRvHRtVTYTl9WXhbecrEV6rFU0.lv3EzQSjR8.A2GCR9134W', '957154', '4 Apr 2025 16:20', NULL, 'N', 1, 0, NULL, NULL),
(14, 'Akash kamble', '8830135758', 'akashkamble547@gmail.com', '$2y$12$PbVbs4.JG5w8uBsgUVprieQHA4Y9w9bKU17JpeyaXflOC9On6Tryi', '601047', '4 Apr 2025 16:22', NULL, 'N', 1, 0, NULL, NULL),
(15, 'Akash kamble', '9175719198', 'aftabmujawar2308@gmail.com', '$2y$12$jw9UhjH2w.6FrcOY3GPuw.gNPC18nw2iD6D6lAtT0m3mKuFSPhkVm', '116196', '4 Apr 2025 16:52', '4 Apr 2025 16:52', 'Y', 1, 0, NULL, NULL),
(16, 'Srijeet', '8208593432', 'srijeetshikalgar00@mail.com', '$2y$12$erSsC83mAHUXwL70Bwky9O4yiWaUFBulcY/HPQI7qRieGeVu/b.SS', '403328', '29 Jun 2025 16:21', '29 Jun 2025 16:21', 'Y', 1, 0, NULL, NULL),
(18, 'Khalid', '8007996538', 'khalidkazi2003@gmail.com', '$2y$12$zb3/HNdrpM4k9hWKCCBK1.148.VQwt.x2pbvhycT3ppKyx7h3MRN2', '724576', '29 Jun 2025 16:27', NULL, 'N', 1, 0, NULL, NULL),
(19, 'Khalid', '8007996583', 'khalidkazi2003@gmail.com', '$2y$12$2HakRp8.XOr8au4lKLsgfOjIN5ZvYFv9JTYPUDZIq6cWlQTiSOJ72', '238766', '29 Jun 2025 16:27', NULL, 'N', 1, 0, NULL, NULL),
(20, 'Srijeet', '9503046790', 'test@example.com', '$2y$12$MNWNA7QwqCOHWhbelLC.wupfGXrrukdgJwZ0o0NbATah/PGDsSpR.', '777527', '29 Jun 2025 18:27', '29 Jun 2025 18:28', 'Y', 1, 0, NULL, NULL),
(21, 'Khalid Kazi', '8007996583', 'khaldkazi2003@gmail.com', '$2y$12$bANzRNk5wfmhwB9dDXepBO7vK75ksIm99YnkygZpCT1MyGmHkYyyi', '946148', '5 Jul 2025 11:02', '5 Jul 2025 11:02', 'Y', 1, 0, NULL, NULL),
(22, 'Akash Dada', '9503046790', 'akashkamble5407@gmail.com', '$2y$12$96zJ8RSv997p7qBQqNbSWOjO7uFJnA/Iuf4NTbtfXX7AVDDOP3TKy', '106032', '5 Jul 2025 11:18', '5 Jul 2025 11:18', 'Y', 1, 0, NULL, NULL),
(23, 'Test User 986', '8208593432', 'test916@example.com', '$2y$12$QSdIQuPetMWkUkQ6dlVRo.TAtA9sFq.uEb/DY7NaDqT/a1x2yevkK', '584244', '5 Jul 2025 11:37', '5 Jul 2025 06:10', 'Y', 1, 0, NULL, NULL),
(24, 'kalid', '8208593432', 'test@email.com', '$2y$12$FdUO9sH6U6hVUzubli2KtetK16Nt1wX2Eyx4ETsOnekhUM.O3wM/S', '446558', '5 Jul 2025 11:45', '5 Jul 2025 06:15', 'Y', 1, 0, NULL, NULL),
(9999, 'Simulation Customer', '8208593432', 'simulation@test.com', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL),
(10000, 'Jatin', '7057058333', 'srijeet@gmail.com', '$2y$12$R4aLC6eUTs09fcg1AopTWOVOBlBq.CPC7CfZe6YGslhqCDKpzUv4O', '727391', '9 Jul 2025 19:01', '9 Jul 2025 13:32', 'Y', 1, 0, NULL, NULL),
(10001, 'shubham', '9172582289', 'skasdssh@gmail.com', '$2y$12$4ojn9Uby9T2zo9WkPBtkIecbpEsn9OppwSbRwC5DVKSGr/x/uKKti', '422577', '10 Jul 2025 16:09', '10 Jul 2025 10:40', 'Y', 1, 0, NULL, NULL),
(10002, 'Srijeet Shikalgar', '1234567891', 'abcd@1234.com', '$2y$12$jJU.mT9Cabxq/MmrQnVd1uNzar6N6Q/UiUB7wovNfgDyCvOdaPgYO', '968590', '18 Jul 2025 14:09', NULL, 'N', 1, 0, NULL, NULL),
(10003, 'Trial Login', '9702201491', 'trial@gmail.com', '$2y$12$nFOrHN.ndSyMMVHWy1.veO4P5C.AEQ3xI5cbYTnMCwEfVRU/EQdDC', '119159', '20 Jul 2025 11:15', '20 Jul 2025 05:46', 'Y', 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_points`
--

CREATE TABLE `customer_points` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `total_points` int DEFAULT '0',
  `lifetime_points` int DEFAULT '0',
  `tier_level` enum('Bronze','Silver','Gold','Platinum') DEFAULT 'Bronze',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_points`
--

INSERT INTO `customer_points` (`id`, `customer_id`, `total_points`, `lifetime_points`, `tier_level`, `created_at`, `updated_at`) VALUES
(1, 1, 43, 43, 'Bronze', '2025-07-20 07:52:05', '2025-07-20 07:52:16'),
(4, 10003, 51, 201, 'Bronze', '2025-07-20 07:53:38', '2025-07-20 09:14:31'),
(11, 16, 49, 49, 'Bronze', '2025-07-21 10:54:02', '2025-07-23 07:05:56');

-- --------------------------------------------------------

--
-- Table structure for table `daily_analytics`
--

CREATE TABLE `daily_analytics` (
  `id` int NOT NULL,
  `analytics_date` date NOT NULL,
  `total_visitors` int DEFAULT '0',
  `new_visitors` int DEFAULT '0',
  `returning_visitors` int DEFAULT '0',
  `total_page_views` int DEFAULT '0',
  `unique_page_views` int DEFAULT '0',
  `average_pages_per_session` decimal(5,2) DEFAULT '0.00',
  `total_sessions` int DEFAULT '0',
  `average_session_duration` int DEFAULT '0' COMMENT 'Average duration in seconds',
  `bounce_rate` decimal(5,2) DEFAULT '0.00',
  `total_registrations` int DEFAULT '0',
  `total_orders` int DEFAULT '0',
  `total_revenue` decimal(12,2) DEFAULT '0.00',
  `conversion_rate` decimal(5,2) DEFAULT '0.00',
  `most_viewed_product_id` int DEFAULT NULL,
  `most_purchased_product_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily aggregated analytics data for reporting';

-- --------------------------------------------------------

--
-- Table structure for table `delhivery_api`
--

CREATE TABLE `delhivery_api` (
  `Id` int NOT NULL,
  `OrderId` text,
  `WayBill` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_config`
--

CREATE TABLE `delivery_config` (
  `id` int NOT NULL,
  `config_key` varchar(100) DEFAULT NULL,
  `config_value` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_config`
--

INSERT INTO `delivery_config` (`id`, `config_key`, `config_value`, `created_at`, `updated_at`) VALUES
(1, 'auto_accept_orders', '1', '2025-07-06 13:11:51', '2025-07-06 13:11:51'),
(2, 'auto_ship_orders', '1', '2025-07-06 13:11:51', '2025-07-06 13:11:51'),
(5, 'auto_send_whatsapp', '1', '2025-07-06 13:16:05', '2025-07-06 13:16:05'),
(6, 'automation_enabled', '1', '2025-07-06 13:16:05', '2025-07-06 13:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_logs`
--

CREATE TABLE `delivery_logs` (
  `id` int NOT NULL,
  `order_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `provider` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('success','failed','pending') COLLATE utf8mb4_general_ci NOT NULL,
  `request_data` text COLLATE utf8mb4_general_ci,
  `response` text COLLATE utf8mb4_general_ci,
  `error_message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_logs`
--

INSERT INTO `delivery_logs` (`id`, `order_id`, `provider`, `action`, `status`, `request_data`, `response`, `error_message`, `created_at`) VALUES
(1, 'MN000015', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751778985196\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:25'),
(2, 'MN000001', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007837\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(3, 'MN000002', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007946\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(4, 'MN000003', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007804\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(5, 'MN000004', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007880\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(6, 'MN000005', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007160\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(7, 'MN000006', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007580\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(8, 'MN000007', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007133\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(9, 'MN000008', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007375\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(10, 'MN000009', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007266\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(11, 'MN000010', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007473\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(12, 'MN000011', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007972\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(13, 'MN000012', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007673\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(14, 'MN000013', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007668\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(15, 'MN000014', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007930\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(16, 'MN000015', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751779007413\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 05:16:47'),
(17, 'MN000019', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751782059323\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 06:07:39'),
(18, 'MN000019', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751782075285\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 06:07:55'),
(19, 'MN000019', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184566\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(20, 'SIM785452', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184471\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(21, 'MN000001', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184565\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(22, 'MN000002', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184134\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(23, 'MN000003', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184717\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(24, 'MN000004', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184971\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(25, 'MN000005', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184114\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(26, 'MN000006', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184733\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(27, 'MN000007', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184194\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(28, 'MN000008', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184515\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(29, 'MN000009', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184531\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(30, 'MN000010', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184389\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(31, 'MN000011', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184290\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(32, 'MN000012', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184395\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(33, 'MN000013', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184260\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(34, 'MN000014', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184472\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(35, 'MN000015', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751790184964\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 08:23:04'),
(36, 'MN000019', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"MOCK1751805312557\",\"status\":\"Created\",\"message\":\"Order created successfully (Mock Mode)\",\"mock_mode\":true}', NULL, '2025-07-06 12:35:12'),
(37, 'MN000002', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000002\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683639987\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683639987\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(38, 'MN000003', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000003\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683639870\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683639870\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(39, 'MN000004', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000004\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683636486\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683636486\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(40, 'MN000005', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000005\",\"customer_name\":\"Khalid Kazi\",\"customer_phone\":\"8007996583\",\"shipping_address\":\"Khalid Kazi, khaldkazi2003@gmail.com, 8007996583, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"549.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683638588\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683638588\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(41, 'MN000006', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000006\",\"customer_name\":\"Khalid Kazi\",\"customer_phone\":\"8007996583\",\"shipping_address\":\"Khalid Kazi, khaldkazi2003@gmail.com, 8007996583, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"549.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683638286\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683638286\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(42, 'MN000007', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000007\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683634977\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683634977\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(43, 'MN000008', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000008\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683638235\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683638235\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(44, 'MN000009', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000009\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683636934\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683636934\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(45, 'MN000010', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000010\",\"customer_name\":\"Khalid Kazi\",\"customer_phone\":\"8007996583\",\"shipping_address\":\"Khalid Kazi, khaldkazi2003@gmail.com, 8007996583, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"549.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683635579\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683635579\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(46, 'MN000011', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000011\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683635625\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683635625\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(47, 'MN000012', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000012\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"249.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683633323\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683633323\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(48, 'MN000013', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000013\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtanagar, Samtangar, 416410, Miraj, Maharashtra\",\"amount\":\"798.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683638912\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683638912\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(49, 'MN000014', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000014\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtanagar, Samtangar, 416410, Miraj, Maharashtra\",\"amount\":\"798.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683637858\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683637858\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(50, 'MN000015', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000015\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"kalid, test@email.com, 8208593432, Samtanagar, Samtangar, 416410, Miraj, Maharashtra\",\"amount\":\"798.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683636569\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683636569\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:03'),
(51, 'MN000019', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000019\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"549.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683635052\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683635052\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(52, 'SIM785452', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"SIM785452\",\"customer_name\":\"Simulation Customer\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Test Address, Test City, 123456\",\"amount\":\"299.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683642351\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683642351\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(53, 'MN000026', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000026\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"747.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683646044\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683646044\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(54, 'MN000027', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000027\",\"customer_name\":\"kalid\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Test Customer, test@example.com, 8208593432, Test Address, Test Landmark, 123456, Test City, Test State\",\"amount\":\"299.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683647693\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683647693\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(55, 'MN000028', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000028\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"747.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683642064\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683642064\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(56, 'MN000029', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000029\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, , 416410, Miraj, Maharashtra\",\"amount\":\"747.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683641747\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683641747\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(57, 'MN000030', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000030\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, , 416410, Miraj, Maharashtra\",\"amount\":\"747.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683646726\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683646726\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(58, 'MN000031', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000031\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra\",\"amount\":\"1494.00\",\"original_status\":\"Placed\"}', '{\"waybill\":\"AUTO17518683641849\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518683641849\",\"status\":\"shipped\"}', NULL, '2025-07-07 06:06:04'),
(59, 'MN000033', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":\"AUTO17518697397819\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518697397819\",\"processed_at\":\"2025-07-07 11:58:59\"}', NULL, '2025-07-07 06:28:59'),
(60, 'MN000034', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":\"AUTO17518715847045\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518715847045\",\"processed_at\":\"2025-07-07 12:29:44\"}', NULL, '2025-07-07 06:59:44'),
(61, 'MN000035', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":\"AUTO17518853769109\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518853769109\",\"processed_at\":\"2025-07-07 16:19:36\"}', NULL, '2025-07-07 10:49:36'),
(62, 'MN000036', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":\"AUTO17518962855195\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518962855195\",\"processed_at\":\"2025-07-07 19:21:25\"}', NULL, '2025-07-07 13:51:25'),
(63, 'MN000037', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":\"AUTO17518978639590\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518978639590\",\"processed_at\":\"2025-07-07 19:47:43\"}', NULL, '2025-07-07 14:17:43'),
(64, 'MN000038', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":\"AUTO17518978675707\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518978675707\",\"processed_at\":\"2025-07-07 19:47:47\"}', NULL, '2025-07-07 14:17:47'),
(65, 'MN000039', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":\"AUTO17518978913049\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/AUTO17518978913049\",\"processed_at\":\"2025-07-07 19:48:11\"}', NULL, '2025-07-07 14:18:11'),
(66, 'MN000040', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-07 20:05:41\"}', NULL, '2025-07-07 14:35:41'),
(67, 'MN000040', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000040\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"customer_email\":\"srijeetshikalgar00@mail.com\",\"shipping_address\":\"Samtanagar, near telgu chruch, Miraj, Maharashtra - 416410\",\"amount\":\"1098.00\"}', '{\"waybill\":\"DHL17518989833666\",\"status\":\"created\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/DHL17518989833666\"}', NULL, '2025-07-07 14:36:23'),
(68, 'MN000041', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-07 20:07:12\"}', NULL, '2025-07-07 14:37:12'),
(69, 'DEBUG_TEST_1751900455', 'delhivery', 'create_order', 'success', NULL, '{\"cash_pickups_count\":0,\"package_count\":0,\"upload_wbn\":null,\"replacement_count\":0,\"rmk\":\"format key missing in POST\",\"pickups_count\":0,\"packages\":[],\"cash_pickups\":0,\"cod_count\":0,\"success\":false,\"prepaid_count\":0,\"error\":true,\"cod_amount\":0}', NULL, '2025-07-07 15:00:55'),
(70, 'PROD_TEST_1751900561', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery API error: HTTP 401 - {\"detail\": \"Authentication credentials were not provided.\"}', NULL, '2025-07-07 15:02:42'),
(71, 'DEBUG_TEST_1751900566', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery API error: HTTP 401 - {\"detail\": \"Authentication credentials were not provided.\"}', NULL, '2025-07-07 15:02:47'),
(72, 'DEBUG_TEST_1751900932', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery API error: HTTP 401 - {\"detail\": \"Authentication credentials were not provided.\"}', NULL, '2025-07-07 15:08:53'),
(73, 'API_TEST_1751900996', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery API error: HTTP 401 - {\"detail\": \"Authentication credentials were not provided.\"}', NULL, '2025-07-07 15:09:57'),
(74, 'VERIFY_1751901049', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery API error: HTTP 401 - {\"detail\": \"Authentication credentials were not provided.\"}', NULL, '2025-07-07 15:10:50'),
(75, 'FIX_TEST_1752045475', 'delhivery', 'create_order', 'success', NULL, '{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL17767479746892418080\",\"replacement_count\":0,\"rmk\":\"An internal Error has occurred, Please get in touch with client.support@delhivery.com\",\"pickups_count\":0,\"packages\":[{\"status\":\"Fail\",\"err_code\":\"ER0005\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"MUM\\/KCK\",\"remarks\":[\"Crashing while saving package due to exception suspicious order\\/consignee. Package might have been partially saved.\"],\"waybill\":\"\",\"cod_amount\":999,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"FIX_TEST_1752045475\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":false,\"prepaid_count\":0,\"cod_amount\":0}', NULL, '2025-07-09 07:17:56'),
(76, 'FINAL_TEST_1752045546', 'delhivery', 'create_order', 'success', NULL, '{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL6894969873478995407\",\"replacement_count\":0,\"rmk\":\"An internal Error has occurred, Please get in touch with client.support@delhivery.com\",\"pickups_count\":0,\"packages\":[{\"status\":\"Fail\",\"err_code\":\"ER0005\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"MUM\\/KCK\",\"remarks\":[\"Crashing while saving package due to exception suspicious order\\/consignee. Package might have been partially saved.\"],\"waybill\":\"\",\"cod_amount\":999,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"FINAL_TEST_1752045546\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":false,\"prepaid_count\":0,\"cod_amount\":0}', NULL, '2025-07-09 07:19:07'),
(77, 'MN000042', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-09 12:55:59\"}', NULL, '2025-07-09 07:25:59'),
(78, 'MN000043', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-09 13:00:40\"}', NULL, '2025-07-09 07:30:40'),
(79, 'MN000044', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-09 13:01:44\"}', NULL, '2025-07-09 07:31:44'),
(80, 'MN000045', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-09 14:42:47\"}', NULL, '2025-07-09 09:12:47'),
(81, 'MN000046', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-09 15:22:53\"}', NULL, '2025-07-09 09:52:53'),
(82, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: No phone number provided.', NULL, '2025-07-09 11:54:56'),
(83, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: No phone number provided.', NULL, '2025-07-09 11:55:57'),
(84, 'MN000041', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:36'),
(85, 'MN000041', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000041\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:36'),
(86, 'MN000042', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:37'),
(87, 'MN000042', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000042\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:37'),
(88, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:38'),
(89, 'MN000043', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:38'),
(90, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:39'),
(91, 'MN000044', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:39'),
(92, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:40'),
(93, 'MN000045', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:40'),
(94, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:41'),
(95, 'MN000046', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:41'),
(96, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:42'),
(97, 'ON1752056715681', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Pending\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:42'),
(98, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:43'),
(99, 'ON1752056877360', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Pending\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:43'),
(100, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:44'),
(101, 'ON1752057640788', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Pending\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:44'),
(102, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:04:45'),
(103, 'ON1752061926930', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Placed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:04:45'),
(104, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount\"}', NULL, '2025-07-09 12:05:08'),
(105, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount\"}', NULL, '2025-07-09 12:05:15'),
(106, 'MN000041', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000041\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"customer_email\":\"srijeetshikalgar00@mail.com\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\"}', '{\"waybill\":\"DHL17520627512337\",\"status\":\"created\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/DHL17520627512337\"}', NULL, '2025-07-09 12:05:51'),
(107, 'MN000042', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000042\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"customer_email\":\"srijeetshikalgar00@mail.com\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\"}', '{\"waybill\":\"DHL17520627546109\",\"status\":\"created\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/DHL17520627546109\"}', NULL, '2025-07-09 12:05:54'),
(108, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount\"}', NULL, '2025-07-09 12:06:08'),
(109, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:28'),
(110, 'MN000043', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:28'),
(111, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:29'),
(112, 'MN000044', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:29'),
(113, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:30'),
(114, 'MN000045', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:30'),
(115, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:31'),
(116, 'MN000046', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:31'),
(117, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:31'),
(118, 'ON1752056715681', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:31'),
(119, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:32'),
(120, 'ON1752056877360', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:32'),
(121, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:33'),
(122, 'ON1752057640788', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:34'),
(123, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:08:34'),
(124, 'ON1752061926930', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:08:35'),
(125, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:14'),
(126, 'MN000043', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:14'),
(127, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:15'),
(128, 'MN000044', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:15'),
(129, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:16'),
(130, 'MN000045', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:16'),
(131, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:17'),
(132, 'MN000046', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:17'),
(133, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:18'),
(134, 'ON1752056715681', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:18'),
(135, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:19'),
(136, 'ON1752056877360', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:19'),
(137, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:20'),
(138, 'ON1752057640788', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:20'),
(139, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:09:21'),
(140, 'ON1752061926930', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"shipped\"}', NULL, '2025-07-09 12:09:21'),
(141, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:00'),
(142, 'MN000043', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:00'),
(143, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:00'),
(144, 'MN000044', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:01'),
(145, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:01'),
(146, 'MN000045', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:01'),
(147, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:03'),
(148, 'MN000046', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:03'),
(149, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:04');
INSERT INTO `delivery_logs` (`id`, `order_id`, `provider`, `action`, `status`, `request_data`, `response`, `error_message`, `created_at`) VALUES
(150, 'ON1752056715681', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:04'),
(151, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:05'),
(152, 'ON1752056877360', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:05'),
(153, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:06'),
(154, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:06'),
(155, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:07'),
(156, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:07'),
(157, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:52'),
(158, 'MN000043', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:52'),
(159, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:53'),
(160, 'MN000044', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:53'),
(161, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:54'),
(162, 'MN000045', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:54'),
(163, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:55'),
(164, 'MN000046', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:55'),
(165, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:56'),
(166, 'ON1752056715681', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:56'),
(167, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:57'),
(168, 'ON1752056877360', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:57'),
(169, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:58'),
(170, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:58'),
(171, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:14:59'),
(172, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":null,\"tracking_url\":null,\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:14:59'),
(173, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:16:52'),
(174, 'MN000043', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:16:53'),
(175, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:16:54'),
(176, 'MN000044', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:16:54'),
(177, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:16:55'),
(178, 'MN000045', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:16:55'),
(179, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:16:56'),
(180, 'MN000046', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:16:56'),
(181, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:16:57'),
(182, 'ON1752056715681', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:16:57'),
(183, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:16:58'),
(184, 'ON1752056877360', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:16:58'),
(185, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:16:59'),
(186, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:16:59'),
(187, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:17:00'),
(188, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:17:00'),
(189, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:34'),
(190, 'MN000043', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:34'),
(191, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:36'),
(192, 'MN000044', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:36'),
(193, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:37'),
(194, 'MN000045', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:37'),
(195, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:38'),
(196, 'MN000046', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:38'),
(197, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:39'),
(198, 'ON1752056715681', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:39'),
(199, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:40'),
(200, 'ON1752056877360', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:40'),
(201, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:41'),
(202, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:41'),
(203, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:18:42'),
(204, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:18:42'),
(205, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:04'),
(206, 'MN000043', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:04'),
(207, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:05'),
(208, 'MN000044', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:05'),
(209, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:06'),
(210, 'MN000045', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:06'),
(211, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:07'),
(212, 'MN000046', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:07'),
(213, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:08'),
(214, 'ON1752056715681', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:08'),
(215, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:09'),
(216, 'ON1752056877360', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:09'),
(217, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:10'),
(218, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:10'),
(219, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:19:11'),
(220, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:19:11'),
(221, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:02'),
(222, 'MN000043', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:02'),
(223, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:03'),
(224, 'MN000044', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:03'),
(225, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:04'),
(226, 'MN000045', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:04'),
(227, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:05'),
(228, 'MN000046', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:05'),
(229, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:06'),
(230, 'ON1752056715681', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:06'),
(231, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:07'),
(232, 'ON1752056877360', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:07'),
(233, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:08'),
(234, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:08'),
(235, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:20:09'),
(236, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:20:09'),
(237, 'MN000043', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:41'),
(238, 'MN000043', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:41'),
(239, 'MN000044', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:42'),
(240, 'MN000044', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:42'),
(241, 'MN000045', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:43'),
(242, 'MN000045', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:43'),
(243, 'MN000046', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:44'),
(244, 'MN000046', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:44'),
(245, 'ON1752056715681', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:45'),
(246, 'ON1752056715681', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:45'),
(247, 'ON1752056877360', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:46'),
(248, 'ON1752056877360', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:46'),
(249, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:47'),
(250, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:47'),
(251, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:23:47'),
(252, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:23:48'),
(253, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount\"}', NULL, '2025-07-09 12:38:44'),
(254, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount\"}', NULL, '2025-07-09 12:38:52'),
(255, 'MN000043', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001396\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001396\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL15510045680000976964\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001396\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000043\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}', NULL, '2025-07-09 12:40:07'),
(256, 'MN000043', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000043\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"30983010001396\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001396\",\"status\":\"shipped\",\"shipment_result\":{\"success\":true,\"waybill\":\"30983010001396\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001396\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL15510045680000976964\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001396\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000043\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}}', NULL, '2025-07-09 12:40:07'),
(257, 'MN000044', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001400\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001400\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL17136335395576040997\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001400\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000044\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}', NULL, '2025-07-09 12:40:09'),
(258, 'MN000044', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000044\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"30983010001400\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001400\",\"status\":\"shipped\",\"shipment_result\":{\"success\":true,\"waybill\":\"30983010001400\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001400\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL17136335395576040997\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001400\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000044\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}}', NULL, '2025-07-09 12:40:09'),
(259, 'MN000045', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001411\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001411\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL382159303788479607\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001411\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000045\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}', NULL, '2025-07-09 12:40:10'),
(260, 'MN000045', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000045\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"30983010001411\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001411\",\"status\":\"shipped\",\"shipment_result\":{\"success\":true,\"waybill\":\"30983010001411\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001411\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL382159303788479607\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001411\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000045\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}}', NULL, '2025-07-09 12:40:10'),
(261, 'MN000046', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001422\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001422\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL11750687338910600541\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001422\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000046\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}', NULL, '2025-07-09 12:40:11'),
(262, 'MN000046', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"MN000046\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410\",\"amount\":\"549.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"30983010001422\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001422\",\"status\":\"shipped\",\"shipment_result\":{\"success\":true,\"waybill\":\"30983010001422\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001422\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL11750687338910600541\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001422\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000046\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}}', NULL, '2025-07-09 12:40:11'),
(263, 'ON1752056715681', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001433\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001433\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL9060586690283848361\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001433\",\"cod_amount\":0,\"payment\":\"Pre-paid\",\"serviceable\":true,\"refnum\":\"ON1752056715681\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":true,\"prepaid_count\":1,\"cod_amount\":0}}', NULL, '2025-07-09 12:40:12'),
(264, 'ON1752056715681', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056715681\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"30983010001433\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001433\",\"status\":\"shipped\",\"shipment_result\":{\"success\":true,\"waybill\":\"30983010001433\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001433\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL9060586690283848361\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001433\",\"cod_amount\":0,\"payment\":\"Pre-paid\",\"serviceable\":true,\"refnum\":\"ON1752056715681\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":true,\"prepaid_count\":1,\"cod_amount\":0}}}', NULL, '2025-07-09 12:40:12'),
(265, 'ON1752056877360', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001444\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001444\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL12896388296309361741\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001444\",\"cod_amount\":0,\"payment\":\"Pre-paid\",\"serviceable\":true,\"refnum\":\"ON1752056877360\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":true,\"prepaid_count\":1,\"cod_amount\":0}}', NULL, '2025-07-09 12:40:13'),
(266, 'ON1752056877360', 'delhivery', 'create_shipment', 'success', '{\"order_id\":\"ON1752056877360\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"30983010001444\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001444\",\"status\":\"shipped\",\"shipment_result\":{\"success\":true,\"waybill\":\"30983010001444\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001444\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL12896388296309361741\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001444\",\"cod_amount\":0,\"payment\":\"Pre-paid\",\"serviceable\":true,\"refnum\":\"ON1752056877360\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":true,\"prepaid_count\":1,\"cod_amount\":0}}}', NULL, '2025-07-09 12:40:13'),
(267, 'ON1752057640788', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:40:14'),
(268, 'ON1752057640788', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752057640788\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"499.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:40:14'),
(269, 'ON1752061926930', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Crashing while saving package due to exception \'Prepaid client manifest charge API failed due to insufficient balance\'. Package might have been partially saved.', NULL, '2025-07-09 12:40:15');
INSERT INTO `delivery_logs` (`id`, `order_id`, `provider`, `action`, `status`, `request_data`, `response`, `error_message`, `created_at`) VALUES
(270, 'ON1752061926930', 'delhivery', 'create_shipment', 'failed', '{\"order_id\":\"ON1752061926930\",\"customer_name\":\"Srijeet\",\"customer_phone\":\"8208593432\",\"shipping_address\":\"Samtangar, Sangli, Maharashtra - 416410\",\"amount\":\"2.00\",\"original_status\":\"Confirmed\"}', '{\"waybill\":\"\",\"tracking_url\":\"\",\"status\":\"failed\",\"shipment_result\":null}', NULL, '2025-07-09 12:40:15'),
(271, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752061926930\', customer_name: \'Srijeet\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752061926930\', customer_name: \'Srijeet\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\"}', NULL, '2025-07-09 12:52:56'),
(272, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752061926930\', customer_name: \'Srijeet\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752061926930\', customer_name: \'Srijeet\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\"}', NULL, '2025-07-09 13:02:06'),
(273, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752061926930\', customer_name: \'Srijeet\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752061926930\', customer_name: \'Srijeet\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\"}', NULL, '2025-07-09 13:03:08'),
(274, 'ON1752061926930', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001470\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001470\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL15217301858380039776\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001470\",\"cod_amount\":0,\"payment\":\"Pre-paid\",\"serviceable\":true,\"refnum\":\"ON1752061926930\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":true,\"prepaid_count\":1,\"cod_amount\":0}}', NULL, '2025-07-09 13:08:57'),
(275, 'ON1752127967565', 'delhivery', 'create_order', 'success', NULL, '{\"success\":false,\"error\":\"Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752127967565\', customer_name: \'Jatin\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\",\"message\":\"Delhivery order creation failed: Missing required fields: customer_phone, shipping_address, total_amount. Received: order_id: \'ON1752127967565\', customer_name: \'Jatin\', customer_phone: \'NOT_SET\', shipping_address: \'NOT_SET\', total_amount: \'NOT_SET\'\"}', NULL, '2025-07-10 06:15:51'),
(276, 'ON1752127967565', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001481\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001481\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL8579819400794951273\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001481\",\"cod_amount\":0,\"payment\":\"Pre-paid\",\"serviceable\":true,\"refnum\":\"ON1752127967565\"}],\"cash_pickups\":0,\"cod_count\":0,\"success\":true,\"prepaid_count\":1,\"cod_amount\":0}}', NULL, '2025-07-10 06:17:39'),
(277, 'MN000047', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-10 12:45:26\"}', NULL, '2025-07-10 07:15:26'),
(278, 'MN000048', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-10 13:26:58\"}', NULL, '2025-07-10 07:56:58'),
(279, 'MN000048', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001492\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001492\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL18338074372109059788\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001492\",\"cod_amount\":249,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000048\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":249}}', NULL, '2025-07-10 10:07:24'),
(280, 'MN000049', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-10 15:47:54\"}', NULL, '2025-07-10 10:17:54'),
(281, 'MN000049', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001503\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001503\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL7580774184252689652\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001503\",\"cod_amount\":2,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000049\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":2}}', NULL, '2025-07-10 10:18:05'),
(282, 'MN000050', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-10 16:10:58\"}', NULL, '2025-07-10 10:40:58'),
(283, 'MN000050', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001514\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001514\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL4514050895448195806\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001514\",\"cod_amount\":899,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000050\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":899}}', NULL, '2025-07-10 10:55:58'),
(284, 'MN000051', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-10 18:44:51\"}', NULL, '2025-07-10 13:14:51'),
(285, 'MN000052', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 14:56:37\"}', NULL, '2025-07-11 09:26:37'),
(286, 'MN000052', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001525\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001525\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL511515897590793934\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001525\",\"cod_amount\":499,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000052\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":499}}', NULL, '2025-07-11 09:27:23'),
(287, 'MN000051', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001536\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001536\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL15937245933155074376\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001536\",\"cod_amount\":549,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000051\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":549}}', NULL, '2025-07-11 09:31:44'),
(288, 'MN000053', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 15:04:16\"}', NULL, '2025-07-11 09:34:16'),
(289, 'MN000053', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001540\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001540\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL3057315789075598678\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001540\",\"cod_amount\":249,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000053\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":249}}', NULL, '2025-07-11 09:34:22'),
(290, 'MN000054', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 15:07:39\"}', NULL, '2025-07-11 09:37:39'),
(291, 'MN000054', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001551\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001551\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL1372844829172518599\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001551\",\"cod_amount\":699,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000054\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":699}}', NULL, '2025-07-11 09:37:47'),
(292, 'MN000055', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 15:10:36\"}', NULL, '2025-07-11 09:40:36'),
(293, 'MN000055', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001562\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001562\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL14595521908558155008\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001562\",\"cod_amount\":1248,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000055\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":1248}}', NULL, '2025-07-11 09:40:53'),
(294, 'MN000055', 'delhivery', 'create_order', 'failed', NULL, 'Delhivery order creation failed: Duplicate order id', NULL, '2025-07-11 09:41:08'),
(295, 'MN000056', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 15:15:07\"}', NULL, '2025-07-11 09:45:07'),
(296, 'MN000056', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001573\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001573\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"prepaid_count\":0,\"pickups_count\":0,\"replacement_count\":0,\"cash_pickups\":0,\"cod_amount\":249,\"cod_count\":1,\"upload_wbn\":\"UPL5276820220893348007\",\"packages\":[{\"waybill\":\"30983010001573\",\"refnum\":\"MN000056\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"payment\":\"COD\",\"cod_amount\":249,\"status\":\"Success\",\"sort_code\":\"KOL\\/HUP\",\"serviceable\":true,\"remarks\":[\"\"]}],\"success\":true}}', NULL, '2025-07-11 09:45:18'),
(297, 'MN000057', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 15:17:25\"}', NULL, '2025-07-11 09:47:25'),
(298, 'MN000057', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001584\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001584\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL2650789794514813110\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001584\",\"cod_amount\":599,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000057\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":599}}', NULL, '2025-07-11 09:47:33'),
(299, 'MN000058', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 15:33:43\"}', NULL, '2025-07-11 10:03:43'),
(300, 'MN000058', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001595\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001595\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL15658846302255663488\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001595\",\"cod_amount\":249,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000058\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":249}}', NULL, '2025-07-11 10:03:49'),
(301, 'MN000059', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-11 15:35:51\"}', NULL, '2025-07-11 10:05:51'),
(302, 'MN000059', 'delhivery', 'create_order', 'success', NULL, '{\"success\":true,\"waybill\":\"30983010001606\",\"tracking_url\":\"https:\\/\\/www.delhivery.com\\/track\\/package\\/30983010001606\",\"status\":\"Created\",\"message\":\"Order created successfully\",\"raw_response\":{\"cash_pickups_count\":0,\"package_count\":1,\"upload_wbn\":\"UPL6964766067049776200\",\"replacement_count\":0,\"pickups_count\":0,\"packages\":[{\"status\":\"Success\",\"client\":\"5088d9-PURENUTRITIONCO-do\",\"sort_code\":\"KOL\\/HUP\",\"remarks\":[\"\"],\"waybill\":\"30983010001606\",\"cod_amount\":249,\"payment\":\"COD\",\"serviceable\":true,\"refnum\":\"MN000059\"}],\"cash_pickups\":0,\"cod_count\":1,\"success\":true,\"prepaid_count\":0,\"cod_amount\":249}}', NULL, '2025-07-11 10:07:46'),
(303, 'MN000060', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-17 18:53:59\"}', NULL, '2025-07-17 13:23:59'),
(304, 'MN000061', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-19 14:36:12\"}', NULL, '2025-07-19 09:06:12'),
(305, 'MN000062', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-19 15:13:17\"}', NULL, '2025-07-19 09:43:17'),
(306, 'MN000063', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-19 15:16:39\"}', NULL, '2025-07-19 09:46:39'),
(307, 'MN000064', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-19 15:16:48\"}', NULL, '2025-07-19 09:46:48'),
(308, 'MN000065', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-19 15:17:10\"}', NULL, '2025-07-19 09:47:10'),
(309, 'MN000066', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-19 15:20:30\"}', NULL, '2025-07-19 09:50:30'),
(310, 'MN000067', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-19 16:45:55\"}', NULL, '2025-07-19 11:15:55'),
(311, 'MN000068', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 11:26:37\"}', NULL, '2025-07-20 05:56:37'),
(312, 'MN000069', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 12:06:46\"}', NULL, '2025-07-20 06:36:46'),
(313, 'MN000070', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 12:13:23\"}', NULL, '2025-07-20 06:43:23'),
(314, 'MN000071', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 12:44:09\"}', NULL, '2025-07-20 07:14:09'),
(315, 'MN000072', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 12:58:07\"}', NULL, '2025-07-20 07:28:07'),
(316, 'MN000073', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 12:58:10\"}', NULL, '2025-07-20 07:28:10'),
(317, 'MN000074', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 12:59:28\"}', NULL, '2025-07-20 07:29:28'),
(318, 'MN000075', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:01:35\"}', NULL, '2025-07-20 07:31:35'),
(319, 'MN000076', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:04:02\"}', NULL, '2025-07-20 07:34:02'),
(320, 'MN000077', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:04:35\"}', NULL, '2025-07-20 07:34:35'),
(321, 'MN000078', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:04:50\"}', NULL, '2025-07-20 07:34:50'),
(322, 'MN000079', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:07:50\"}', NULL, '2025-07-20 07:37:50'),
(323, 'MN000080', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:09:40\"}', NULL, '2025-07-20 07:39:40'),
(324, 'MN000081', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:11:02\"}', NULL, '2025-07-20 07:41:02'),
(325, 'MN000082', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:15:43\"}', NULL, '2025-07-20 07:45:43'),
(326, 'MN000083', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:17:46\"}', NULL, '2025-07-20 07:47:46'),
(327, 'MN000084', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:22:16\"}', NULL, '2025-07-20 07:52:16'),
(328, 'MN000085', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:23:38\"}', NULL, '2025-07-20 07:53:38'),
(329, 'MN000086', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:51:36\"}', NULL, '2025-07-20 08:21:36'),
(330, 'MN000087', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:54:43\"}', NULL, '2025-07-20 08:24:43'),
(331, 'MN000088', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:55:22\"}', NULL, '2025-07-20 08:25:22'),
(332, 'MN000089', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 13:55:48\"}', NULL, '2025-07-20 08:25:48'),
(333, 'MN000090', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-20 14:44:31\"}', NULL, '2025-07-20 09:14:31'),
(334, 'MN000091', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-21 16:24:27\"}', NULL, '2025-07-21 10:54:27'),
(335, 'MN000092', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-23 11:43:01\"}', NULL, '2025-07-23 06:13:01'),
(336, 'MN000093', 'delhivery', 'auto_process', 'success', NULL, '{\"waybill\":null,\"tracking_url\":null,\"processed_at\":\"2025-07-23 12:35:56\"}', NULL, '2025-07-23 07:05:56');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_performance`
--

CREATE TABLE `delivery_performance` (
  `id` int NOT NULL,
  `provider` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `total_orders` int DEFAULT '0',
  `successful_deliveries` int DEFAULT '0',
  `failed_deliveries` int DEFAULT '0',
  `average_delivery_time` decimal(5,2) DEFAULT '0.00',
  `on_time_deliveries` int DEFAULT '0',
  `customer_rating` decimal(3,2) DEFAULT '0.00',
  `cost_per_delivery` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_rates`
--

CREATE TABLE `delivery_rates` (
  `id` int NOT NULL,
  `provider` varchar(50) NOT NULL,
  `from_pincode` varchar(10) NOT NULL,
  `to_pincode` varchar(10) NOT NULL,
  `weight_from` decimal(5,2) NOT NULL,
  `weight_to` decimal(5,2) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `service_type` varchar(50) NOT NULL DEFAULT 'standard',
  `estimated_days` int NOT NULL DEFAULT '7',
  `is_cod_available` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_tracking`
--

CREATE TABLE `delivery_tracking` (
  `id` int NOT NULL,
  `order_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `provider` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tracking_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `waybill_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `courier_company` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `current_status` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `current_location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_date` timestamp NOT NULL,
  `status_description` text COLLATE utf8mb4_general_ci,
  `is_delivered` tinyint(1) DEFAULT '0',
  `delivery_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zones`
--

CREATE TABLE `delivery_zones` (
  `id` int NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `pincode_start` varchar(10) NOT NULL,
  `pincode_end` varchar(10) NOT NULL,
  `preferred_provider` varchar(50) NOT NULL,
  `backup_provider` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `priority` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_zones`
--

INSERT INTO `delivery_zones` (`id`, `zone_name`, `pincode_start`, `pincode_end`, `preferred_provider`, `backup_provider`, `is_active`, `priority`, `created_at`) VALUES
(1, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 13:57:33'),
(2, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 13:57:33'),
(3, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 13:57:33'),
(4, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 13:57:33'),
(5, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 13:57:33'),
(6, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 13:57:33'),
(7, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 13:57:33'),
(8, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 13:57:33'),
(9, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:11:27'),
(10, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:11:27'),
(11, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:11:27'),
(12, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:11:27'),
(13, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:11:27'),
(14, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:11:27'),
(15, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:11:27'),
(16, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:11:27'),
(17, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:12:28'),
(18, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:12:28'),
(19, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:12:28'),
(20, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:12:28'),
(21, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:12:28'),
(22, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:12:28'),
(23, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:12:28'),
(24, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:12:28'),
(25, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:04'),
(26, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:04'),
(27, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:04'),
(28, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:04'),
(29, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:04'),
(30, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:04'),
(31, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:04'),
(32, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:04'),
(33, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:10'),
(34, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:10'),
(35, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:10'),
(36, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:10'),
(37, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:10'),
(38, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:10'),
(39, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:14:10'),
(40, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:14:10'),
(41, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:15:52'),
(42, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:15:52'),
(43, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:15:52'),
(44, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:15:52'),
(45, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:15:52'),
(46, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:15:52'),
(47, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:15:52'),
(48, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:15:52'),
(49, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:21:34'),
(50, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:21:34'),
(51, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:21:34'),
(52, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:21:34'),
(53, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:21:34'),
(54, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:21:34'),
(55, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:21:34'),
(56, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:21:34'),
(57, 'Mumbai Metro', '400001', '400099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:25:10'),
(58, 'Delhi NCR', '110001', '110099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:25:10'),
(59, 'Bangalore', '560001', '560099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:25:10'),
(60, 'Chennai', '600001', '600099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:25:10'),
(61, 'Pune', '411001', '411099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:25:10'),
(62, 'Hyderabad', '500001', '500099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:25:10'),
(63, 'Kolkata', '700001', '700099', 'shiprocket', 'delhivery', 1, 1, '2025-06-28 14:25:10'),
(64, 'Ahmedabad', '380001', '380099', 'delhivery', 'shiprocket', 1, 1, '2025-06-28 14:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `direct_customers`
--

CREATE TABLE `direct_customers` (
  `CustomerId` int NOT NULL,
  `CustomerName` text,
  `MobileNo` text,
  `Email` text,
  `Address` text,
  `City` text,
  `Pincode` text,
  `State` text,
  `OTP` int DEFAULT NULL,
  `IsVerify` text,
  `CreatedAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `direct_customers`
--

INSERT INTO `direct_customers` (`CustomerId`, `CustomerName`, `MobileNo`, `Email`, `Address`, `City`, `Pincode`, `State`, `OTP`, `IsVerify`, `CreatedAt`) VALUES
(40, 'Amisha Padia', '09403283502', 'amishapadia@gmail.com', 'Venkatesh nagar near nexa showroom vraj niwas sangli, ', 'Sangli', '416416', 'Maharashtra', 496308, 'Y', NULL),
(46, 'Sabir Jamadar', '8108873902', 'jamadarsabir29@gmail.com', 'Samta Nagar, Old Haripur Road', 'Sangli', '416410', 'Maharashtra', 750098, 'Y', NULL),
(51, 'Yelvish Yadav', '7894561230', 'yelvishyadav123@gmail.com', 'Ashoka Chouck, Aahir mohla, Kishanganj', 'Kishanganj', '855107', 'Bihar', 987079, 'Y', NULL),
(52, 'sds ds', '9876543210', 'trd@gmail.com', 'sdss efsawe, fsss', 'Sangli', '416416', 'Maharashtra', 348446, 'Y', NULL),
(53, 'Misba Abdulmajjid Bepari', '8856085750', 'misba0101b@gmail,com', 'shaniwar peth, Sphurti chowk , miraj', 'Sangli', '416410', 'Maharashtra', 278062, 'N', NULL),
(75, 'Noor Kazi', '8310470031', 'noorkhazi82@gmail.com', 'Near Nadaf Masjid, Hotel', 'Sangli', '416410', 'Maharashtra', 164884, 'Y', NULL),
(76, 'Abdulaziz kagwade', '8421776088', 'abdulazizkagwade@gmail.com', 'RAILWAY KOLHAPUR CHAWL,NEAR MADINA MASJID,MIRAJ, Madina Masjid', 'Sangli', '416410', 'Maharashtra', 956146, 'Y', NULL),
(84, 'Amar Shirgure', '9175719198', 'amarshirgure@gmail.com', 'Kolhapur road, Sangli', 'Sangli', '416416', 'Maharashtra', 618938, 'N', NULL),
(85, 'Amar Shirgure', '9175719198', 'amarshirgure@gmail.com', 'Kolhapur road, Sangli', 'Sangli', '416416', 'Maharashtra', 310243, 'N', NULL),
(86, 'Amar Shirgure', '9175719198', 'amarshirgure@gmail.com', 'Kolhapur road, Sangli', 'Sangli', '416416', 'Maharashtra', 287563, 'N', NULL),
(87, 'Sahil Dilawar Mujawar', '8830135758', 'sahilmujawar225@gmail.com', 'nandre, Sangli', 'Sangli', '416416', 'Maharashtra', 227168, 'Y', NULL),
(88, 'Sahil Mujawar', '8830135758', 'aftabmujawar2308@gmail.com', 'vasagde Road , Nandre, Nandre', 'Sangli', '416416', 'Maharashtra', 353712, 'Y', NULL),
(89, 'Sahil Mujawar', '8830135758', 'aftabmujawar2308@gmail.com', 'vasagde Road , Nandre, Nandre', 'Sangli', '416416', 'Maharashtra', 786357, 'N', NULL),
(97, 'Muddassar kazi', '8329566751', 'iammadyyk@gmail.com', 'Near nadaf masjid, Priyadarhsini Hotel', 'Sangli', '416410', 'Maharashtra', 221394, 'Y', NULL),
(98, 'Shivam Kamboj', '8307635152', 'Shivamkamboj046@gmail.com', 'Dholi, Nera hanuman mandir Dholi  P.o chamrori', 'Yamuna Nagar', '135133', 'Haryana', 729777, 'Y', NULL),
(99, 'Shivam Kamboj', '8307635152', 'Shivamkamboj046@gmail.com', 'Dholi, Nera hanuman mandir Dholi  P.o chamrori', 'Yamuna Nagar', '135133', 'Haryana', 418221, 'N', NULL),
(100, 'Akash Kamble', '9503046790', 'akashkamble547@gmail.com', 'Maharvawa, Wasagde, Wasagde', 'Sangli', '416416', 'Maharashtra', 355564, 'Y', NULL),
(101, 'Shubham Kenche', '9172582289', 'kencheshubham6@gmail.com', 'Kenche Colony, haripur, Haripur ,Sangli', 'Sangli', '416416', 'Maharashtra', 536889, 'Y', NULL),
(106, 'PSI Arun Gaikwad', '09096402650', 'arun12gaikwad@gmail.com', 'BMC main road, Killa court Azad maidan police station', 'Mumbai', '400001', 'Maharashtra', 430693, 'Y', NULL),
(107, 'PSI Arun Gaikwad', '09096402650', 'arun12gaikwad@gmail.com', 'BMC main road, Killa court Azad maidan police station', 'Mumbai', '400001', 'Maharashtra', 596067, 'N', NULL),
(108, 'Mamta Thakur', '7876583824', 'mamtathakurs231@gmail.com', 'Baijnath, Bandian Tehsil Baijnath District kangra HP 176128', 'Kangra', '176125', 'Himachal Pradesh', 993225, 'Y', NULL),
(109, 'Ravindra ghanghav', '9975759827', 'ravindraghanghav1997@gmail.com', 'Mahanagarpalika marg,mumbai, Azad maidan police station', 'Mumbai', '400001', 'Maharashtra', 363952, 'Y', NULL),
(110, 'Ravindra ghanghav', '9975759827', 'ravindraghanghav1997@gmail.com', 'Mahanagarpalika marg,mumbai, Azad maidan police station', 'Mumbai', '400001', 'Maharashtra', 682316, 'N', NULL),
(111, 'Ravindra ghanghav', '9975759827', 'ravindraghanghav1997@gmail.com', 'Mahanagarpalika marg,mumbai, Azad maidan police station', 'Mumbai', '400001', 'Maharashtra', 560280, 'N', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `DoctorId` int NOT NULL,
  `DoctorName` varchar(100) NOT NULL,
  `Qualification` varchar(200) NOT NULL,
  `Specialization` varchar(200) NOT NULL,
  `Experience` int NOT NULL COMMENT 'Years of experience',
  `PhotoPath` varchar(255) DEFAULT NULL,
  `Description` text,
  `ConsultationFee` decimal(10,2) DEFAULT '200.00',
  `PhoneNumber` varchar(15) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT '1',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`DoctorId`, `DoctorName`, `Qualification`, `Specialization`, `Experience`, `PhotoPath`, `Description`, `ConsultationFee`, `PhoneNumber`, `Email`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Dr. Rajesh Kumar', 'BAMS, MD (Ayurveda)', 'General Ayurvedic Medicine, Digestive Disorders', 15, NULL, 'Experienced Ayurvedic practitioner specializing in digestive wellness and general health consultation. Expert in traditional Ayurvedic treatments and herbal medicine.', 200.00, '9876543210', 'dr.rajesh@mynutrify.com', 1, '2025-07-23 09:11:56', '2025-07-23 09:11:56'),
(2, 'Dr. Priya Sharma', 'BAMS, MS (Ayurveda)', 'Women\'s Health, Skin & Hair Care', 12, NULL, 'Specialist in women\'s health issues, skin care, and hair problems. Provides comprehensive Ayurvedic solutions for hormonal imbalances and beauty concerns.', 200.00, '9876543211', 'dr.priya@mynutrify.com', 1, '2025-07-23 09:11:56', '2025-07-23 09:11:56'),
(3, 'Dr. Amit Patel', 'BAMS, PhD (Ayurveda)', 'Diabetes, Cardiac Wellness, Weight Management', 18, NULL, 'Senior consultant specializing in lifestyle disorders like diabetes, heart problems, and weight management through Ayurvedic principles and dietary guidance.', 200.00, '9876543212', 'dr.amit@mynutrify.com', 1, '2025-07-23 09:11:56', '2025-07-23 09:11:56');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `AvailabilityId` int NOT NULL,
  `DoctorId` int NOT NULL,
  `DayOfWeek` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `SlotDuration` int DEFAULT '30' COMMENT 'Duration in minutes',
  `IsActive` tinyint(1) DEFAULT '1',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctor_availability`
--

INSERT INTO `doctor_availability` (`AvailabilityId`, `DoctorId`, `DayOfWeek`, `StartTime`, `EndTime`, `SlotDuration`, `IsActive`, `CreatedAt`) VALUES
(1, 1, 'Monday', '09:00:00', '18:00:00', 30, 1, '2025-07-23 09:11:56'),
(2, 1, 'Tuesday', '09:00:00', '18:00:00', 30, 1, '2025-07-23 09:11:56'),
(3, 1, 'Wednesday', '09:00:00', '18:00:00', 30, 1, '2025-07-23 09:11:56'),
(4, 1, 'Thursday', '09:00:00', '18:00:00', 30, 1, '2025-07-23 09:11:56'),
(5, 1, 'Friday', '09:00:00', '18:00:00', 30, 1, '2025-07-23 09:11:56'),
(6, 1, 'Saturday', '09:00:00', '15:00:00', 30, 1, '2025-07-23 09:11:56'),
(7, 2, 'Monday', '10:00:00', '17:00:00', 30, 1, '2025-07-23 09:11:56'),
(8, 2, 'Tuesday', '10:00:00', '17:00:00', 30, 1, '2025-07-23 09:11:56'),
(9, 2, 'Wednesday', '10:00:00', '17:00:00', 30, 1, '2025-07-23 09:11:56'),
(10, 2, 'Thursday', '10:00:00', '17:00:00', 30, 1, '2025-07-23 09:11:56'),
(11, 2, 'Friday', '10:00:00', '17:00:00', 30, 1, '2025-07-23 09:11:56'),
(12, 2, 'Saturday', '10:00:00', '14:00:00', 30, 1, '2025-07-23 09:11:56'),
(13, 3, 'Monday', '11:00:00', '19:00:00', 30, 1, '2025-07-23 09:11:56'),
(14, 3, 'Tuesday', '11:00:00', '19:00:00', 30, 1, '2025-07-23 09:11:56'),
(15, 3, 'Wednesday', '11:00:00', '19:00:00', 30, 1, '2025-07-23 09:11:56'),
(16, 3, 'Thursday', '11:00:00', '19:00:00', 30, 1, '2025-07-23 09:11:56'),
(17, 3, 'Friday', '11:00:00', '19:00:00', 30, 1, '2025-07-23 09:11:56'),
(18, 3, 'Saturday', '11:00:00', '16:00:00', 30, 1, '2025-07-23 09:11:56');

-- --------------------------------------------------------

--
-- Table structure for table `enhanced_coupons`
--

CREATE TABLE `enhanced_coupons` (
  `id` int NOT NULL,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coupon_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('fixed','percentage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(10,2) NOT NULL,
  `max_discount_amount` decimal(10,2) DEFAULT NULL COMMENT 'Max discount for percentage type',
  `minimum_order_amount` decimal(10,2) DEFAULT '0.00',
  `applicable_categories` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of category IDs, NULL for all',
  `excluded_products` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of product IDs to exclude',
  `usage_limit_total` int DEFAULT NULL COMMENT 'Total usage limit across all customers',
  `usage_limit_per_customer` int DEFAULT '1',
  `current_usage_count` int DEFAULT '0',
  `customer_type` enum('all','new','existing','specific') COLLATE utf8mb4_unicode_ci DEFAULT 'all',
  `specific_customers` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of customer IDs if customer_type is specific',
  `tier_restrictions` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of tier levels (Bronze,Silver,Gold,Platinum)',
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `points_required` int DEFAULT NULL COMMENT 'Points needed to redeem this coupon, NULL if not from rewards',
  `is_reward_coupon` tinyint(1) DEFAULT '0',
  `reward_catalog_id` int DEFAULT NULL COMMENT 'Link to rewards_catalog if generated from rewards',
  `is_active` tinyint(1) DEFAULT '1',
  `is_stackable` tinyint(1) DEFAULT '0' COMMENT 'Can be combined with other coupons',
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'system',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Enhanced coupon system with rewards integration';

--
-- Dumping data for table `enhanced_coupons`
--

INSERT INTO `enhanced_coupons` (`id`, `coupon_code`, `coupon_name`, `description`, `discount_type`, `discount_value`, `max_discount_amount`, `minimum_order_amount`, `applicable_categories`, `excluded_products`, `usage_limit_total`, `usage_limit_per_customer`, `current_usage_count`, `customer_type`, `specific_customers`, `tier_restrictions`, `valid_from`, `valid_until`, `points_required`, `is_reward_coupon`, `reward_catalog_id`, `is_active`, `is_stackable`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME50', 'Welcome Discount', 'Get ₹50 off on your first order', 'fixed', 50.00, NULL, 500.00, NULL, NULL, NULL, 1, 0, 'all', NULL, NULL, '2025-07-19 14:37:40', '2026-07-19 14:37:40', NULL, 0, NULL, 1, 0, 'system', '2025-07-19 09:07:40', '2025-07-19 09:07:40'),
(2, 'SAVE100', 'Save ₹100', 'Get ₹100 off on orders above ₹1000', 'fixed', 100.00, NULL, 1000.00, NULL, NULL, NULL, 3, 0, 'all', NULL, NULL, '2025-07-19 14:37:40', '2026-01-19 14:37:40', NULL, 0, NULL, 1, 0, 'system', '2025-07-19 09:07:40', '2025-07-19 09:07:40'),
(3, 'PERCENT10', '10% Off', 'Get 10% discount up to ₹200', 'percentage', 10.00, NULL, 800.00, NULL, NULL, NULL, 2, 0, 'all', NULL, NULL, '2025-07-19 14:37:40', '2025-10-19 14:37:40', NULL, 0, NULL, 1, 0, 'system', '2025-07-19 09:07:40', '2025-07-19 09:07:40'),
(4, 'FREESHIP', 'Free Shipping', 'Free shipping on all orders', 'fixed', 50.00, NULL, 0.00, NULL, NULL, NULL, 5, 0, 'all', NULL, NULL, '2025-07-19 14:37:40', '2026-07-19 14:37:40', NULL, 0, NULL, 1, 0, 'system', '2025-07-19 09:07:40', '2025-07-19 09:07:40'),
(5, 'WELCOME10', 'Welcome 10% Off', 'Get 10% off on your first order', 'percentage', 10.00, 100.00, 500.00, NULL, NULL, NULL, 1, 0, 'new', NULL, NULL, '2025-07-19 16:33:55', '2026-07-19 16:33:55', NULL, 0, NULL, 1, 0, 'system', '2025-07-19 11:03:55', '2025-07-19 11:23:29'),
(6, 'SAVE50', 'Save ₹50', 'Flat ₹50 off on orders above ₹1000', 'fixed', 50.00, NULL, 1000.00, NULL, NULL, 100, 1, 0, 'all', NULL, NULL, '2025-07-19 16:33:55', '2026-07-19 16:33:55', NULL, 0, NULL, 1, 0, 'system', '2025-07-19 11:03:55', '2025-07-19 11:03:55'),
(7, 'FLAT100', 'Flat ₹100 Off', 'Get ₹100 off on orders above ₹2000', 'fixed', 100.00, NULL, 2000.00, NULL, NULL, 50, 1, 0, 'all', NULL, NULL, '2025-07-19 16:33:55', '2026-07-19 16:33:55', NULL, 0, NULL, 1, 0, 'system', '2025-07-19 11:03:55', '2025-07-19 11:03:55'),
(8, 'WELCOME25', 'Save 20%', '', 'fixed', 25.00, 0.00, 499.00, NULL, NULL, 1, 1, 0, 'new', NULL, NULL, '2025-07-21 10:14:00', '2025-08-20 10:14:00', NULL, 0, NULL, 1, 0, 'admin', '2025-07-21 10:16:28', '2025-07-21 10:16:28'),
(9, 'KHALIDHARAMI', 'oisudbhoiu', '', 'percentage', 99.00, 0.00, 499.00, NULL, NULL, 0, 1, 0, 'all', NULL, NULL, '2025-07-21 10:16:00', '2025-08-20 10:16:00', NULL, 0, NULL, 0, 0, 'admin', '2025-07-21 10:17:28', '2025-07-21 10:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `FAQId` int NOT NULL,
  `ProductId` int NOT NULL,
  `Question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`FAQId`, `ProductId`, `Question`, `Answer`) VALUES
(1, 6, 'What is Wild Amla Juice used for?', 'It is used to boost immunity, improve digestion, and promote hair and skin health. Rich in natural Vitamin C.'),
(2, 6, ' What makes Wild Amla different from regular amla?', 'Wild Amla is more potent and nutrient-rich. It has higher antioxidant and Vitamin C content'),
(3, 6, 'Can I take Wild Amla Juice daily?', 'Yes, it is safe for daily use. Regular intake supports overall wellness and vitality.'),
(4, 6, 'What are the key benefits of this juice?', 'It strengthens immunity, enhances skin glow, supports hair growth, and aids digestion.'),
(5, 6, 'How much Wild Amla Juice should I take?', 'Take 30 ml twice daily on an empty stomach. Dilute with water before consumption.\r\n'),
(6, 9, 'Q.1 What is Cholesterol Care Juice used for?', 'It helps manage cholesterol levels naturally and supports heart health. It promotes better lipid balance and cardiovascular wellness.\r\n'),
(7, 9, 'Q.2 Which ingredients are included in Cholesterol Care Juice?', 'It contains  Garlic, Apple Cider Vinagar, Lemon, Ginger & Honey. These herbs are known for heart and cholesterol support.\r\n'),
(8, 9, 'Q.3 How does this juice help manage cholesterol levels', 'It lowers bad cholesterol (LDL), increases good cholesterol (HDL), and improves blood flow. The antioxidants also reduce plaque buildup.'),
(9, 9, 'Q.4 Can Cholesterol Care Juice be taken daily?', 'Yes, it is safe and beneficial for daily use. Regular intake supports consistent cholesterol control.'),
(10, 9, 'Q.5  Is it safe for people with high blood pressure?', 'Yes, it’s generally safe and may support blood pressure balance. Still, consult your doctor if on medication.\r\n'),
(11, 9, 'Q.6 How much Cholesterol Care Juice should I take in a day?', 'Take 30 ml twice daily on an empty stomach. Mix it with water for best results.\r\n'),
(12, 9, 'Q.7 Are there any side effects of Cholesterol Care Juice?', 'It’s made with natural ingredients and usually well-tolerated. Mild digestive changes may occur initially.'),
(13, 9, 'Q.8 Can I take this juice along with my regular cholesterol medication?', 'Yes, it can complement your current medication. Always consult your doctor for personalized advice'),
(14, 9, 'Q.9 Is Cholesterol Care Juice suitable for all age groups?', 'It is recommended for adults only. Children and pregnant women should consult a physician before use.'),
(15, 9, 'Q.10. How long does it take to see results from using this juice?', 'You may notice changes within 4 to 6 weeks of regular use. Results vary based on lifestyle and diet.'),
(16, 23, '  How to use My Nutrify Herbal & Ayurveda’s She Care Plus Juice ?', 'Shake the bottle before use\r\nDilute 30ml juice in a glass of water\r\nMorning Dose- Before Breakfast\r\nEvening Dose- Before Dinner at night.'),
(17, 23, ' Is it helpful in conceiving ?', 'Yes, it helps maintain the regular menstrual cycles and hormonal changes. So it might be helpful if you are planning to conceive.'),
(18, 23, 'Is it helpful for PCOS and PCOD? ', 'Yes, She Care Plus Juice may help in managing PCOS and PCOD by supporting hormonal balance and improving menstrual health. It also helps reduce stress and boosts overall female wellness naturally.'),
(19, 23, 'How many months to use to get better results ?', 'You will be able to see noticeable results in 60-90 days. For best results it is advised to continue drinking this juice daily for a prolonged period.'),
(20, 23, 'Are there any precautions while taking She Care Plus Juice?', ' There are no major precautions, but avoid consuming curd and spicy food while using the juice'),
(21, 23, 'What is the age criteria and dosage for She Care Plus Juice?', ' Individuals aged 13 years and above can take it – 15 ml twice daily for teens, and 30 ml twice daily for adults.'),
(22, 23, 'Can thyroid patients use this juice ?', 'Yes, thyroid patients can use this juice. However, please read the ingredients list for any known allergies'),
(23, 23, 'Can She Care Plus Juice help with irregular periods', 'Yes, She Care Plus Juice supports hormonal balance, which may help regulate menstrual cycles naturally'),
(24, 23, 'Does She Care Plus Juice help with mood swings and fatigue?', 'Yes, She Care Plus Juice contains Ayurvedic herbs that help reduce stress, support emotional balance, and combat fatigue commonly experienced during hormonal imbalances.\r\n'),
(25, 23, 'Can She Care Plus Juice be taken along with other medications?', 'Yes, it can generally be taken with other medications, but it\'s best to consult your doctor if you are on specific treatments or long-term medication'),
(26, 22, 'What is BP Care Juice used for?', 'It helps manage blood pressure naturally and supports heart health. Also promotes relaxation and better circulation.'),
(27, 22, 'Which ingredients are used in BP Care Juice?', 'It contains herbs like Amla, Arjuna, Ashwagandha, Brahmi, and Sarpagandha. These are traditionally known for balancing blood pressure.'),
(28, 22, 'How does this juice help control blood pressure?', 'It calms the nervous system, improves heart function, and helps regulate both high and low BP levels.\r\n'),
(29, 22, 'Can I take BP Care Juice daily?', 'Yes, it’s safe for daily use. Consistent intake helps maintain stable blood pressure levels.'),
(30, 22, 'Is it safe for people on BP medication?', 'Yes, but consult your doctor before use. It may complement your existing treatment.'),
(31, 22, 'How much BP Care Juice should I take per day?', 'Take 30 ml twice daily on an empty stomach. Dilute with water for better absorption.\r\n'),
(32, 22, 'Does it help with anxiety or stress?', 'Yes, herbs like Ashwagandha and Brahmi help reduce stress. This indirectly supports healthy blood pressure.'),
(33, 22, 'Are there any side effects?', 'It is natural and generally safe. Some may experience mild drowsiness or digestive changes initially.'),
(34, 22, 'Is BP Care Juice suitable for long-term use?', 'Yes, it can be used long-term as part of a heart-healthy lifestyle. Safe when taken as directed.'),
(35, 22, 'When can I expect to see results?', 'Most people notice improvement in 2–3 Months. Results may vary based on lifestyle and diet.'),
(36, 25, 'How do I use My Nutrify Herbal & Ayurveda\'s Thyro Balance Care Juice for best results?', 'Shake well and mix 30ml juice in a glass of water. Take it twice daily—morning before breakfast and evening before dinner.\r\n\r\n'),
(37, 25, 'Can this juice help in managing weight associated with thyroid imbalance?', 'Yes, it may support weight management by balancing metabolism and improving thyroid function naturally.'),
(38, 25, 'Is it possible to completely cure thyroid disorders with this juice', 'It helps support thyroid health but should not be considered a permanent cure. Consistent use may show good results over time.'),
(39, 25, 'Does it support both hypothyroidism and hyperthyroidism conditions?', 'Yes, it is formulated with herbs that help balance thyroid hormones in both conditions.'),
(40, 25, 'Can I take Thyro Balance Juice along with my prescribed allopathic thyroid medicines?', '\r\nYes, but consult your doctor before combining with allopathic medication to avoid interactions.'),
(41, 25, 'Is this juice safe for pregnant or breastfeeding women?', 'Pregnant or breastfeeding women should take this juice only after consulting their healthcare provider.'),
(42, 25, 'How long should I use the product to notice visible improvements?', '\r\nResults may vary, but noticeable changes can be seen in 4–8 weeks of regular use.'),
(43, 25, 'Are there any specific precautions or dietary restrictions I should follow?', 'Avoid junk food, processed sugar, and soy-based products. Maintain a balanced diet and routine.\r\n'),
(44, 25, 'Can this juice help in balancing hormonal fluctuations caused by thyroid issues?', 'Yes, it helps regulate hormones naturally and supports better energy and mood.'),
(45, 25, 'Is Thyro Balance Care Juice suitable for long-term daily use?', 'Yes, it is made with natural Ayurvedic ingredients and can be taken regularly for long-term support.'),
(46, 14, 'What is Wheatgrass Juice used for?', 'It is used for detoxifying the body, boosting energy, and improving immunity. It also supports digestion and skin health.'),
(47, 14, 'What nutrients are found in Wheatgrass Juice?', 'Wheatgrass is rich in chlorophyll, vitamins A, C, E, iron, calcium, magnesium, and amino acids.'),
(48, 14, 'Can I take Wheatgrass Juice daily?', 'Yes, it is safe for daily use. It promotes overall health and natural cleansing of the body.'),
(49, 14, 'How does Wheatgrass support detoxification?', 'It helps remove toxins, cleanses the liver, and supports blood purification with its high chlorophyll content.'),
(50, 14, ' Is this juice helpful for boosting immunity?', 'Yes, its antioxidants and nutrients strengthen the immune system. It helps fight fatigue and infections.'),
(51, 14, 'When will I start seeing results from this juice?', 'Results may appear in 2–4 weeks with consistent use. Energy levels and digestion usually improve first.'),
(52, 14, 'Does it help with digestion and acidity?', 'Yes, Wheatgrass supports gut health, relieves acidity, and improves bowel movements.'),
(53, 14, 'Can this juice help with anemia or low hemoglobin?', 'Yes, it is high in iron and chlorophyll. It may help improve hemoglobin levels naturally.'),
(54, 14, ' Are there any side effects of Wheatgrass Juice?', '\r\nIt is generally well-tolerated. Some may experience mild nausea or headache initially as part of detox.'),
(55, 14, 'How is the juice prepared?', 'The juice is extracted from freshly harvested wheatgrass using methods that minimize oxidation and preserve its active enzymes and nutrients. This traditional process ensures you receive a potent, high-quality herbal supplement.'),
(56, 6, 'Is this juice good for hair fall?', 'Yes, Amla is known to nourish hair follicles. It may reduce hair fall and promote healthy hair.'),
(57, 6, 'Does it help with acidity or digestion issues?', 'Yes, it balances stomach acids and improves digestion. It also soothes gut inflammation.'),
(58, 6, 'Is Wild Amla Juice safe for children?', 'It is generally safe in small amounts. Consult a pediatrician before giving it to children.'),
(59, 6, ' Are there any side effects?', 'It’s natural and usually well-tolerated. Some may experience mild acidity if taken in excess.'),
(60, 6, 'When can I expect to see results?', 'Results vary, but noticeable improvements in energy and digestion often appear within 2–3 weeks.'),
(61, 10, 'What is Diabetic Care Juice used for?', 'It helps manage blood sugar levels naturally. Supports insulin function and overall diabetic wellness.'),
(62, 10, 'Which ingredients are included in Diabetic Care Juice?', '\r\nIt contains Karela, Jamun, Amla, Methi, Gudmar, Neem, and Vijaysar. These herbs are known for their anti-diabetic properties.'),
(63, 10, 'How does this juice help manage diabetes?', 'It improves insulin sensitivity, regulates glucose metabolism, and reduces sugar spikes. Also helps control cravings and fatigue.\r\n'),
(64, 10, 'Can Diabetic Care Juice be taken daily?', 'Yes, it’s safe for daily use. Regular intake helps maintain healthy blood sugar levels.'),
(65, 10, 'Is it safe for people on diabetes medication?', 'Yes, but consult your doctor to adjust dosage if needed. It can complement your diabetes management plan.'),
(66, 10, 'How much Diabetic Care Juice should I take in a day?', 'Take 30 ml twice a day on an empty stomach. Mix with water for best absorption.\r\n'),
(67, 10, 'Are there any side effects of Diabetic Care Juice?', 'It’s natural and generally well-tolerated. Minor digestive changes may occur in the beginning.'),
(68, 10, 'Can this juice prevent diabetes in pre-diabetic individuals?', 'Yes, it may help delay or prevent progression. It supports healthy metabolism and pancreatic function.'),
(69, 10, ' Is Diabetic Care Juice suitable for all age groups', 'It’s mainly for adults with diabetes or high sugar levels. Consult a doctor for use in children or the elderly.'),
(70, 10, 'How long does it take to see results from using this juice?', 'Most users notice better sugar control in 3–6 weeks. Consistency and diet play a key role in results.\r\n'),
(71, 15, 'What is Apple Cider Vinegar (ACV) used for?', 'ACV supports weight management, digestion, detox, and healthy skin. It’s also known to help balance blood sugar levels.'),
(72, 15, 'What makes this ACV different from others?', 'It is raw, unfiltered, and contains the “mother” – rich in enzymes and beneficial bacteria for better health benefits.'),
(73, 15, 'Can I take Apple Cider Vinegar daily?', 'Yes, daily use is safe in the right amount. It supports metabolism, energy, and gut health.'),
(74, 15, 'How much ACV should I take per day?', 'Take 10–15 ml diluted in a glass of water, once or twice daily. Always consume it diluted.\r\n'),
(75, 15, 'Is this ACV helpful for weight loss?', 'Yes, it helps curb appetite, boosts metabolism, and supports fat burning when combined with a healthy lifestyle.'),
(76, 15, 'Does it help with digestion?', 'Absolutely, ACV improves digestion by increasing stomach acid levels. It also helps relieve bloating and acidity.'),
(77, 15, 'Can ACV improve skin and hair health?', 'Yes, ACV supports clear skin and shiny hair. It can also be used topically for dandruff and acne.'),
(78, 15, ' Is it safe for people with diabetes?', 'Yes, it may help manage blood sugar levels. But diabetic individuals should consult their doctor before use.'),
(79, 15, 'Are there any side effects of ACV?', 'If taken undiluted, it may cause throat or stomach irritation. Always dilute and avoid excess use.'),
(80, 15, ' How soon can I expect results from using ACV?', 'You may start noticing benefits in 2–3 Months with regular use. Results vary depending on diet and lifestyle.'),
(81, 18, 'How do I use My Nutrify Shilajit?', 'Use the provided spoon to scoop a pea-sized amount of the resin. Dissolve it in 100–200 ml of lukewarm water or milk, stirring until completely dissolved. For best results, consume it twice daily—preferably in the morning or as advised by a healthcare professional.'),
(82, 18, 'What are the benefits of My Nutrify Shilajit?', 'My Nutrify Shilajit is designed to boost energy levels, enhance muscle strength, and improve endurance. With 80+ trace minerals and a rich fulvic acid content, it aids nutrient absorption, supports detoxification, and contributes to overall vitality and well-being.'),
(83, 18, 'What role does fulvic acid play in Shilajit?', 'Fulvic acid is a key ingredient in our purified Himalayan Shilajit. It acts as a powerful antioxidant, helping to reduce inflammation, enhance nutrient absorption at the cellular level, and support gut health, making it an essential component for natural energy and wellness.'),
(84, 18, 'How can I identify pure Shilajit?', 'Pure Shilajit is typically sticky, tar-like, and ranges from dark brown to black in color with a distinctive earthy aroma. When a small amount is dissolved in warm water, it should completely dissolve without leaving any residue, indicating its purity.'),
(85, 18, 'What should I do if the Shilajit resin solidifies in the bottle?', 'If you notice that the resin has solidified, gently warm the bottle in a container of warm water. This will soften the resin, allowing you to carefully scrape or pour out the desired amount without compromising its quality.'),
(86, 18, 'Is My Nutrify Shilajit safe to consume?', 'Yes, My Nutrify Shilajit is a high-quality, purified Ayurvedic supplement manufactured under strict quality control standards. It is safe for consumption when taken as directed. However, if you have any underlying health conditions or concerns, consult your healthcare provider before use.'),
(87, 18, 'Where is My Nutrify Shilajit sourced from?', 'Our Shilajit is harvested from the pristine Himalayan mountain ranges, ensuring a naturally potent, high-altitude resin enriched with 80+ trace minerals and fulvic acid. This authentic Ayurvedic source guarantees optimal purity and efficacy.'),
(88, 18, 'What is the recommended dosage for My Nutrify Shilajit?', 'For optimal benefits, take a pea-sized portion dissolved in 100–200 ml of lukewarm water or milk, twice daily. Always follow the product label instructions or consult your healthcare provider for personalized guidance.'),
(89, 18, 'Can both men and women use My Nutrify Shilajit?', 'Yes, this purified Ayurvedic supplement is designed for both men and women. It supports energy levels, stamina, and overall well-being across genders, making it a versatile addition to your daily routine.'),
(90, 18, 'Is My Nutrify Shilajit vegan-friendly?', 'Absolutely. Our Shilajit is 100% vegetarian and vegan-friendly, containing no animal-derived ingredients. It’s an ideal natural supplement for those following a plant-based lifestyle.'),
(91, 19, 'How do I use My Nutrify Shilajit?', 'Use the provided spoon to scoop a pea-sized amount of the resin. Dissolve it in 100–200 ml of lukewarm water or milk, stirring until completely dissolved. For best results, consume it twice daily—preferably in the morning or as advised by a healthcare professional.'),
(92, 19, 'What are the benefits of My Nutrify Shilajit?', 'My Nutrify Shilajit is designed to boost energy levels, enhance muscle strength, and improve endurance. With 80+ trace minerals and a rich fulvic acid content, it aids nutrient absorption, supports detoxification, and contributes to overall vitality and well-being.'),
(93, 19, 'What role does fulvic acid play in Shilajit?', 'Fulvic acid is a key ingredient in our purified Himalayan Shilajit. It acts as a powerful antioxidant, helping to reduce inflammation, enhance nutrient absorption at the cellular level, and support gut health, making it an essential component for natural energy and wellness.'),
(94, 19, 'How can I identify pure Shilajit?', 'Pure Shilajit is typically sticky, tar-like, and ranges from dark brown to black in color with a distinctive earthy aroma. When a small amount is dissolved in warm water, it should completely dissolve without leaving any residue, indicating its purity.'),
(95, 19, 'What should I do if the Shilajit resin solidifies in the bottle?', 'If you notice that the resin has solidified, gently warm the bottle in a container of warm water. This will soften the resin, allowing you to carefully scrape or pour out the desired amount without compromising its quality.'),
(96, 19, 'Is My Nutrify Shilajit safe to consume?', 'Yes, My Nutrify Shilajit is a high-quality, purified Ayurvedic supplement manufactured under strict quality control standards. It is safe for consumption when taken as directed. However, if you have any underlying health conditions or concerns, consult your healthcare provider before use.'),
(97, 19, 'Where is My Nutrify Shilajit sourced from?', 'Our Shilajit is harvested from the pristine Himalayan mountain ranges, ensuring a naturally potent, high-altitude resin enriched with 80+ trace minerals and fulvic acid. This authentic Ayurvedic source guarantees optimal purity and efficacy.'),
(98, 19, 'What is the recommended dosage for My Nutrify Shilajit?', 'For optimal benefits, take a pea-sized portion dissolved in 100–200 ml of lukewarm water or milk, twice daily. Always follow the product label instructions or consult your healthcare provider for personalized guidance.'),
(99, 19, 'Can both men and women use My Nutrify Shilajit?', 'Yes, this purified Ayurvedic supplement is designed for both men and women. It supports energy levels, stamina, and overall well-being across genders, making it a versatile addition to your daily routine.'),
(100, 19, 'Is My Nutrify Shilajit vegan-friendly?', 'Absolutely. Our Shilajit is 100% vegetarian and vegan-friendly, containing no animal-derived ingredients. It’s an ideal natural supplement for those following a plant-based lifestyle.'),
(101, 21, 'How do I use My Nutrify Shilajit?', 'Use the provided spoon to scoop a pea-sized amount of the resin. Dissolve it in 100–200 ml of lukewarm water or milk, stirring until completely dissolved. For best results, consume it twice daily—preferably in the morning or as advised by a healthcare professional.'),
(102, 21, 'What are the benefits of My Nutrify Shilajit?', 'My Nutrify Shilajit is designed to boost energy levels, enhance muscle strength, and improve endurance. With 80+ trace minerals and a rich fulvic acid content, it aids nutrient absorption, supports detoxification, and contributes to overall vitality and well-being.'),
(103, 21, 'What role does fulvic acid play in Shilajit?', 'Fulvic acid is a key ingredient in our purified Himalayan Shilajit. It acts as a powerful antioxidant, helping to reduce inflammation, enhance nutrient absorption at the cellular level, and support gut health, making it an essential component for natural energy and wellness.'),
(104, 21, 'How can I identify pure Shilajit?', 'Pure Shilajit is typically sticky, tar-like, and ranges from dark brown to black in color with a distinctive earthy aroma. When a small amount is dissolved in warm water, it should completely dissolve without leaving any residue, indicating its purity.'),
(105, 21, 'What should I do if the Shilajit resin solidifies in the bottle?', 'If you notice that the resin has solidified, gently warm the bottle in a container of warm water. This will soften the resin, allowing you to carefully scrape or pour out the desired amount without compromising its quality.'),
(106, 21, 'Is My Nutrify Shilajit safe to consume?', 'Yes, My Nutrify Shilajit is a high-quality, purified Ayurvedic supplement manufactured under strict quality control standards. It is safe for consumption when taken as directed. However, if you have any underlying health conditions or concerns, consult your healthcare provider before use.'),
(107, 21, 'Where is My Nutrify Shilajit sourced from?', 'Our Shilajit is harvested from the pristine Himalayan mountain ranges, ensuring a naturally potent, high-altitude resin enriched with 80+ trace minerals and fulvic acid. This authentic Ayurvedic source guarantees optimal purity and efficacy.'),
(108, 21, 'What is the recommended dosage for My Nutrify Shilajit?', 'For optimal benefits, take a pea-sized portion dissolved in 100–200 ml of lukewarm water or milk, twice daily. Always follow the product label instructions or consult your healthcare provider for personalized guidance.'),
(109, 21, 'Can both men and women use My Nutrify Shilajit?', 'Yes, this purified Ayurvedic supplement is designed for both men and women. It supports energy levels, stamina, and overall well-being across genders, making it a versatile addition to your daily routine.'),
(110, 21, 'Is My Nutrify Shilajit vegan-friendly?', 'Absolutely. Our Shilajit is 100% vegetarian and vegan-friendly, containing no animal-derived ingredients. It’s an ideal natural supplement for those following a plant-based lifestyle.'),
(111, 11, 'What is Karela Neem & Jamun Juice used for?', 'It helps regulate blood sugar levels and supports digestion. It also purifies blood and promotes skin health.'),
(112, 11, 'Which ingredients are used in this juice?', 'The juice contains Karela (Bitter Gourd), Neem, and Jamun. All are traditional herbs used in diabetes and detox care.'),
(113, 11, ' How does this juice help with diabetes?', 'It enhances insulin sensitivity and helps reduce sugar absorption. Karela and Jamun are especially effective in glucose control.'),
(114, 11, 'Can I take this juice daily?', 'Yes, daily use is recommended. It helps maintain balanced sugar levels and improves overall wellness.'),
(115, 11, ' Is this juice good for skin problems?', 'Yes, Neem and Karela help detoxify the blood. Regular use may reduce acne and improve skin clarity.'),
(116, 11, ' How much should I take per day?', 'Take 30 ml twice daily on an empty stomach. Mix it with water for best results.'),
(117, 11, 'Is it suitable for people on diabetes medication?', 'Yes, but consult your doctor for personalized advice. It can support your existing treatment plan.\r\n'),
(118, 11, 'Are there any side effects?', 'It’s natural and generally safe. Some may experience mild digestive changes at first.'),
(119, 11, 'Can this juice help with weight management?', 'Yes, it supports metabolism and may reduce sugar cravings. Helpful in weight and appetite control.'),
(120, 11, 'When will I start seeing results?', 'Most users see benefits in 3–4 weeks. Consistency and a healthy diet improve results.'),
(121, 34, '1. What is included in the My Nutrify Diabetic & Cholesterol Care Juice combo?', ' This combo includes two Ayurvedic juices â€“ one for supporting healthy blood sugar levels (Diabetic Care Juice) and one for promoting heart health and managing cholesterol (Cholesterol Care Juice).\r\n\r\n'),
(122, 34, '2. Are both juices safe to consume together?', 'Yes, both the juices are made up of natural ingredients and can be taken together as a component of a healthy wellness regime. However, take advice from a doctor if you are on medication or suffer from medical conditions.\r\n'),
(123, 34, '3. Are there any side effects of this combo?', 'These juices are derived from herbal herbs and typically don\'t have any side effects. But for those with allergies or sensitivities to some herbs, it\'s recommended to look at the ingredients list prior to consumption.\r\n'),
(124, 34, '4. How does the Diabetic Care Juice control blood sugar levels?', ' This juice is formulated with Ayurvedic herbs such as Karela (Bitter Gourd), Jamun, which are believed to balance blood sugar levels naturally.\r\n'),
(125, 34, '5. Can I discontinue my diabetes medication after taking this juice?', 'No. This juice is a natural supplement and must be taken in addition to your prescribed treatment. Always check with your doctor before you alter your medication.\r\n'),
(126, 34, '6. How long do I have to take the Diabetic Care Juice to experience results?', 'Regular consumption for a period of at least 1â€“3 months, along with a balanced diet and lifestyle, may assist in yielding improved results.\r\n'),
(127, 34, '7. How does the Cholesterol Care Juice function?', 'It consists of herbs such as Ginger, Garlic, and Honey, used conventionally to help to maintain heart health and manage cholesterol.\r\n'),
(128, 34, '8. Does this juice help lower LDL (bad) cholesterol?', 'Although individual outcomes may differ, the herbs incorporated in this juice are reputed to aid in lowering bad cholesterol levels in the long run.\r\n'),
(129, 34, '9.How do I take these juices?', '\r\nTake 30ml of each juice individually, ideally on an empty stomach in the morning or as advised by your healthcare practitioner. Mix with water if preferred.\r\n'),
(130, 34, ' How do I store the juices?', '\r\n Store the bottles in a dry, cool place out of direct sunlight. Refrigerate once opened and use within 3 months.\r\n\r\n'),
(131, 35, '1. Can I consume both Diabetic Care Juice and BP Care Juice simultaneously?', '\r\nYes, both juices are prepared with natural Ayurvedic ingredients and can be taken together, and are safe. They are synergistic in their actions and will aid overall well-being. But please consult your doctor if you are under medication.\r\n'),
(132, 35, '2. Does this combo have any side effects when taken daily?', '\r\nThese juices are prepared with standard herbs that do not have known side effects upon usage as prescribed. Always label-read and discuss with a physician if you experience allergies or ailments.\r\n'),
(133, 35, '3. How does the Diabetic Care Juice aid blood sugar management?', '\r\n It has elements such as Karela, Jamun, and Amla that in Ayurveda, are said to control blood sugar levels naturally.\r\n\r\n'),
(134, 35, '4. How do I take the Diabetic Care Juice?', '\r\n Drink 30ml of the juice on an empty stomach in the morning. You may mix it with lukewarm water if you wish.\r\n'),
(135, 35, '5. Is Diabetic Care Juice beneficial for people across all age groups?', 'It can be taken normally by adults. In case of children, pregnant women, and elderly persons suffering from conditions, take it on the advice of a physician. \r\n'),
(136, 35, '6.What is the use of herbs in the BP Care Juice? It contains Arjuna, Ashwagandha, Garlic, and other heart-friendly herbs recognized to provide healthy blood pressure.', '\r\nIt contains Arjuna, Ashwagandha, Garlic, and other heart-friendly herbs recognized to provide healthy blood pressure.\r\n'),
(137, 35, '7.Will this juice reduce high blood pressure without medicine?', 'Yes, this herb is typically employed in Ayurveda for managing blood pressure. It should work best along with a regular healthy lifestyle.\r\n'),
(138, 35, '8.How many times a day should I use the BP Care Juice?', ' \r\nConsume 30ml once or twice a day, preferably as a pre-meal or as directed by your physician.\r\n'),
(139, 35, '9.Can BP Care Juice be consumed in the long run?', '\r\nYes, it is from natural herbs and is safe on daily, long-term consumption provided one takes the suggested dosage as and when advisable. \r\n'),
(140, 35, '10.Does BP Care Juice serve in stress-related BP or anxiety-related BP disorders?', '\r\nYes, ingredients like Ashwagandha help reduce stress, which can indirectly support healthy blood pressure levels.\r\n'),
(141, 39, '1. Can I consume both Diabetic Care Juice and Karela Neem & Jamun Juice together?', '\r\nYes, both juices are prepared using complementary Ayurvedic ingredients and can be taken together for increased support in the natural regulation of blood sugar levels. Please consult your doctor if you are on medication.\r\n'),
(142, 39, '2. Will this combination contribute to improved control of diabetes compared to the use of individual juice?', '\r\nYes, this combination provides a more balanced approach since the combined ingredients synergize to help regulate insulin action, detox, and sugar metabolism.\r\n'),
(143, 39, '3. Is My Nutrify Diabetic Care Juice constituted with 100% herbal ingredients?', ' Yes, it is constituted with 100% Ayurvedic and herbal ingredients without the addition of artificial additives or preservatives.\r\n'),
(144, 39, '4. Can Diabetic Care Juice improve energy levels in diabetics?', ' Yes, by regulating balanced blood sugar levels, it may help mitigate fatigue and contribute to sustained energy levels during the day.\r\n'),
(145, 39, '5. Does it control sugar craving?', 'Yes, certain herbs in the juice such as Jamun and Karela are known to naturally suppress sugar cravings by controlling insulin response.\r\n\r\n'),
(146, 39, '6. Can non-diabetic people consume this juice as a preventive health measure?', 'Absolutely. It is safe for risk individuals or those who wish to keep their blood sugar levels healthy through natural means.\r\n'),
(147, 39, '7. Is it safe to use this juice in combination with insulin therapy?', 'Generally yes, but itâ€™s important to consult your doctor before combining with insulin or other prescribed medications to avoid sugar level drops.\r\n'),
(148, 39, '8. What are the health benefits of Karela Neem & Jamun Juice?', ' It supports blood sugar control, promotes liver health, improves digestion, and helps detoxify the body naturally.\r\n'),
(149, 39, '9. How is this juice different from the Diabetic Care Juice?', 'Though both of them provide sugar balance, Karela Neem & Jamun Juice is more about detoxifying, bitter tonic effect, and purifying the blood, while Diabetic Care Juice has more herbs in its combination for blood sugar control.\r\n'),
(150, 39, '10. May I take Karela Neem & Jamun Juice on an empty stomach?', ' Yes, take an empty stomach preferably in the morning diluted with water for easier assimilation.\r\n'),
(151, 39, '11. Is it ok for regular consumption?', ' Yes, the juice is safe for regular use and can be a part of your health regimen.\r\n'),
(152, 39, '12. Are there cautions in the usage of this juice?', ' Avoid excessive use. Low blood sugar (hypoglycemia) patients, pregnant women, or diabetics taking medication should consult a physician before use.\r\n'),
(153, 37, '1. Are She Care Plus and Wild Amla Juice compatible to consume together?', 'Yes, both are natural herbal juices and can be taken together. They complement each other in supporting women\'s health and immune system boosting.\r\n\r\n'),
(154, 37, '2. What are the advantages of taking this combo every day?', 'The combination aids hormonal balance, skin and hair maintenance, menstrual well-being, and immunity buildup owing to the nutritive abundance of amla and woman-directed herbs.\r\n'),
(155, 37, '3. For what is She Care Plus Juice intended?', '\r\nIt is specifically designed for women\'s well-being, from hormonal equilibrium, menstrual ease, to reproductive wellness.\r\n'),
(156, 37, '4. Is the juice beneficial in terms of having irregular periods?', 'Yes, She Care Plus has herbs such as Ashoka, Lodhra, and Shatavari which are used in Ayurveda to balance menstrual cycles naturally.\r\n'),
(157, 37, '5. Is it helpful for PCOS or PCOD symptoms?', 'It can give supportive benefits, but there is no cure. Daily use with life-style modifications can help manage PCOS/PCOD symptoms.\r\n\r\n'),
(158, 37, '6. Is She Care Plus safe for teenage girls or menopausal women?', 'Yes, it is safe for the majority of women across all age groups. Nevertheless, one should consult a physician for teenagers or menopause when on medication.\r\n'),
(159, 37, '7. Is it free from added sugars and preservatives?', 'No, free from added sugars, preservatives, and artificial chemicals â€” purely made of Ayurvedic herbs.\r\n'),
(160, 37, '8. What are the major advantages of Wild Amla Juice?', 'It is loaded with Vitamin C and antioxidants that nourish immunity, skin radiance, digestion, and hair.\r\n\r\n'),
(161, 37, '9. Can I take Wild Amla Juice daily?', ' Yes, it is safe and healthy to consume daily. Drink 20â€“30ml diluted in water on an empty stomach in the morning.\r\n'),
(162, 37, '10. Is Wild Amla Juice beneficial for hair fall and dandruff?', 'Yes. Amla is a natural solution for making hair roots stronger, decreasing dandruff, and stimulating hair growth.\r\n'),
(163, 37, '11. Does the juice aid acidity or digestion issues?', ' Yes, Amla is cooling in nature and has beneficial effects on the gut, reducing acidity and enhancing digestion with regular use.\r\n'),
(164, 37, '12. Is Wild Amla Juice suitable for diabetic people?', 'Yes, Amla has a low glycemic index and might even stabilize blood sugar levels. But consult your doctor if you are on diabetic medication.\r\n\r\n'),
(165, 36, '1. Can I consume Cholesterol Care Juice and BP Care Juice together?', 'Yes, they are formulated with natural ingredients that work in synergy and can be consumed together to nourish general heart health, blood flow, and healthy cholesterol levels.\r\n\r\n'),
(166, 36, '2. Will this combination promote long-term cardiovascular well-being?', 'Yes. Continued use, in conjunction with a balanced lifestyle, may help control cholesterol and blood pressure naturally, lowering cardiovascular risk over the long term.\r\n'),
(167, 36, '3. What is the main application of Cholesterol Care Juice?', 'It controls cholesterol levels by lowering LDL (bad cholesterol) and enhancing HDL (good cholesterol) with the assistance of heart-friendly ingredients such as Ginger, Garlic, Apple Cider Vinegar, Lemon, and Honey.\r\n'),
(168, 36, '4. How do I take Cholesterol Care Juice?', 'Take 30ml twice daily, preferably on an empty stomach. Dilute with water for easier absorption and palatability.\r\n\r\n'),
(169, 36, '5. Does it help control triglyceride levels as well?', 'Yes, ingredients such as Apple Cider Vinegar and Garlic are said to assist in reducing levels of triglycerides as well as bad cholesterol.\r\n\r\n'),
(170, 36, '6. Can this juice be consumed long-term?', 'Yes, Cholesterol Care Juice can be taken long-term as part of a healthy lifestyle for the heart, as long as you are not allergic to any of its natural constituents.\r\n'),
(171, 36, '7. Will it interact with cholesterol medicine?', 'It\'s fine to take normally, but if you\'re on statins or other cholesterol-lowering drugs, talk to your doctor before you include it in your routine.\r\n'),
(172, 36, '8. What does BP Care Juice do to keep blood pressure normal?', 'It has herbs such as Arjuna, Sarpagandha, and Brahmi, which soothe the nervous system, improve arterial elasticity, and maintain normal BP.\r\n'),
(173, 36, '9. Can BP Care Juice reduce stress and anxiety?', 'Yes, herbs such as Brahmi and Jatamansi have a relaxing effect, which can possibly reduce BP surges caused by stress.\r\n'),
(174, 36, '10.Can I take BP Care Juice if I have fluctuating blood pressure?', 'Yes, it may help support stability, but always consult a doctor if your BP varies significantly.\r\n'),
(175, 36, '11. Can I take this juice with BP tablets?', ' Yes, but do not stop or alter your medication without consulting your physician.\r\n'),
(176, 40, '1. I take Karela Neem Jamun Juice and BP Care Juice together?', 'I take Karela Neem Jamun Juice and BP Care Juice together?\r\nYes, both juices are prepared from natural, Ayurvedic ingredients that are complementary to each other. Drinking them together might benefit healthy blood sugar levels, blood pressure, and general metabolic health.\r\n\r\n'),
(177, 40, '2. Are there any side effects of taking BP Care Juice with Karela Neem Jamun Juice?', 'These juices are safe to use as directed. Nevertheless, because both can affect blood pressure and blood sugar, consult your physician if you\'re taking medication or have a medical condition.\r\nCan\r\n'),
(178, 40, '3. What is normal blood pressure and when should I be concerned?', 'Normal BP is usually 120/80 mmHg. Regular readings above 140/90 mmHg are high and require medical care.\r\n'),
(179, 40, '4. Which lifestyle modifications can reduce high blood pressure?', 'Salt intake reduction, regular exercise, stress management, alcohol and tobacco avoidance, and a balanced diet can do so.\r\n'),
(180, 40, '5. Is it possible to control high blood pressure without medication?', 'In early or borderline instances, changes in lifestyle can work. Always consult your doctor, though, for an appropriate treatment plan.\r\n'),
(181, 40, '6. What are the signs of high blood pressure?', ' Often referred to as the \"silent killer,\" it can be asymptomatic. In extreme cases, you can get headaches, dizziness, or chest pain.\r\n'),
(182, 40, '7. How frequently should I monitor my blood pressure?', 'If you are at risk or diagnosed, check it once a week or as recommended by your doctor.\r\n\r\n'),
(183, 40, '8. What are the benefits of Karela Neem Jamun Juice?', 'It maintains blood sugar levels, detoxifies the body, enhances digestion, and improves skin health.\r\n'),
(184, 40, '9. Is Karela Neem Jamun Juice good for diabetes?', 'Yes, it includes compounds such as polypeptide-p, charantin, and bitter components that aid in controlling blood sugar levels naturally.\r\n'),
(185, 40, '10. When should one consume Karela Neem Jamun Juice?', '\r\n The best time is in the morning on an empty stomach to ensure maximum absorption and efficacy.\r\n'),
(186, 40, '11.  Are there side effects of this juice?', '\r\n It is safe to consume in moderation. Overindulgence may cause low blood sugar or gastric discomfort.\r\n\r\n'),
(187, 40, '12. Can I take Karela Neem Jamun Juice daily?', '\r\n Yes, daily use is safe for most people. However, consult your healthcare provider if you have a medical condition or are on medication.\r\n\r\n'),
(188, 38, '1. Can I take Apple Cider Vinegar and Wheatgrass Juice together?', '\r\n Yes, both can be taken together safely. They complement each other and support detoxification, digestion, metabolism, and overall wellness.\r\n'),
(189, 38, '2. When is the ideal time to drink Apple Cider Vinegar and Wheatgrass Juice during the day?', '\r\nTake Wheatgrass Juice in the morning on an empty stomach and Apple Cider Vinegar with meals (in diluted form) for heightened digestion and metabolism.\r\n'),
(190, 38, '3. What are the primary health benefits of Apple Cider Vinegar?', 'It helps digestion, aids in weight control, maintains blood sugar balance, and aids in healthy skin.\r\n\r\n'),
(191, 38, '4. How do I take Apple Cider Vinegar every day?', '\r\n Mix 1â€“2 teaspoons in a glass of warm water and take it 15â€“20 minutes before meals.\r\n'),
(192, 38, '5. Can Apple Cider Vinegar aid in weight loss?', ' Yes, it could help in increasing satiety and metabolism and assist in weight control when accompanied by a healthy diet.\r\n'),
(193, 38, '6.Do any side effects occur from Apple Cider Vinegar?', '\r\nIf undiluted or taken in excessive amounts, it can lead to tooth enamel wearing away or gastric irritation. Use it always in a diluted state.\r\n\r\n'),
(194, 38, '7. Is Apple Cider Vinegar safe for daily consumption?', 'Yes, provided it is in moderate quantities and diluted.\r\n\r\n'),
(195, 38, '8. What are Wheatgrass Juice advantages?', 'It\'s packed with chlorophyll, antioxidants, vitamins, and minerals that detoxify the body, enhance immunity, and enhance energy levels.\r\n'),
(196, 38, '9. Can Wheatgrass Juice aid digestion?', 'Yes, it has enzymes that facilitate digestion and enhance gut health.\r\n'),
(197, 38, '10. How much Wheatgrass Juice do I need to drink daily?', 'Generally, 30 ml once or twice daily on an empty stomach is advisable for optimal benefits.\r\n'),
(198, 38, '11. Is Wheatgrass Juice beneficial for skin and hair?', 'Yes, its antioxidant and cleansing properties can support clearer skin and healthier hair.\r\n\r\n'),
(199, 38, '12. Are there any side effects of Wheatgrass Juice?', ' Itâ€™s generally safe but may cause mild nausea or headaches in some people during the detox phase. Start with a small dose.\r\n\r\n'),
(200, 41, '1. Can I take Apple Cider Vinegar and Wild Amla Juice together? ', 'Yes, they are both natural and go well together. When used together, they enhance digestion, immunity, metabolism, detoxification, and skin health.\r\n'),
(201, 41, '2. How should Apple Cider Vinegar and Wild Amla Juice be taken?', 'Mix 15â€“30 ml of Wild Amla Juice and 1â€“2 teaspoons of Apple Cider Vinegar in water. Take it on an empty stomach in the morning or before meals for optimal results.\r\n'),
(202, 41, '3.  What are the key advantages of Apple Cider Vinegar?', ' It helps with weight control, digestion, blood sugar control, and clearer skin.\r\n'),
(203, 41, '4.How do I take Apple Cider Vinegar safely?', ' Always dilute 1â€“2 teaspoons in water and take before meals. Never take it neat to avoid damaging your teeth and stomach lining.\r\n'),
(204, 41, '5. Can Apple Cider Vinegar ease bloating and indigestion?', 'Yes, it boosts stomach acid production, which may help digest food and prevent bloating.\r\n'),
(205, 41, '6.Is Apple Cider Vinegar safe to take daily?', 'Yes, diluted and in moderation, it is generally safe for everyday use by healthy adults.\r\n'),
(206, 41, '7. Can I use Apple Cider Vinegar on my skin and hair?', 'Yes, externally as a toner or hair rinse after proper dilution, it may help with acne, dandruff, and scalp health.\r\n'),
(207, 41, '8. What are the key benefits of Wild Amla Juice?', ' It is rich in Vitamin C and antioxidants that boost immunity, improve digestion, enhance skin glow, and support hair health.\r\n'),
(208, 41, '9.  How much Wild Amla Juice should I take daily?', '\r\n The recommended dose is 15â€“30 ml once or twice daily, preferably on an empty stomach.\r\n\r\n'),
(209, 41, '10. Is Wild Amla Juice good for hair and skin?', '\r\nYes, the rich Vitamin C content stimulates collagen production, combats free radicals, and fortifies hair follicles.\r\n'),
(210, 41, '11. Can Wild Amla Juice alleviate acidity or stomach problems?', '\r\n Yes, it possesses cooling and alkaline qualities that can potentially neutralize excess stomach acid and enhance gut health.\r\n\r\n'),
(211, 41, '12. Is Wild Amla Juice safe for long-term consumption?', ' Absolutely, it\'s a natural supplement and safe for daily, long-term consumption when consumed in the suggested dosage.\r\n'),
(212, 44, '1. Can I combine Thyro Balance and Wild Amla Juice?', ' Yes, both being natural preparations, can be consumed together. They function synergistically to nourish the thyroid gland, immunity, metabolism, and energy levels.\r\n'),
(213, 44, '2. What is the ideal time to take Thyro Balance and Wild Amla Juice?', 'Take Wild Amla Juice in the morning on an empty stomach. Thyro Balance capsules or syrup can be used as directed by dosage, after meals or as advised by your healthcare practitioner.\r\n'),
(214, 44, '3.What is Thyro Balance Care used for?', 'Thyro Balance is an all-natural supplement that targets healthy thyroid functioning, hormonal balance, and metabolism.\r\n'),
(215, 44, '4.Is Thyro Balance ideal for hypothyroidism?', 'Yes, it contains herbs like Ashwagandha, Guggul, and Brahmi that may help regulate thyroid hormones and reduce hypothyroid symptoms naturally.\r\n\r\n'),
(216, 44, '5. How long does it take for Thyro Balance to show results?', ' Results may vary, but consistent use over 4â€“6 weeks often brings noticeable improvements in energy, mood, and metabolism.\r\n'),
(217, 44, '6. Can I take Thyro Balance if Iâ€™m already on thyroid medication?', 'Consult your physician prior to using it with prescribed thyroid medication since it can affect hormone levels and dosage needs.\r\n\r\n'),
(218, 44, '7. Does Thyro Balance have side effects?', ' It is safe to use when taken as directed. However, if you notice any unusual symptoms, stop using it and consult your healthcare professional.\r\n'),
(219, 44, '8. What are the advantages of Wild Amla Juice?', 'Wild Amla Juice is packed with Vitamin C and antioxidants that enhance immunity, digestive health, skin health, and hair growth.\r\n'),
(220, 44, '9. Is Wild Amla Juice beneficial for hormonal balance?', '\r\n Yes, its antioxidant and anti-inflammatory actions can be beneficial in mitigating oxidative stress, which has a role in keeping hormonal balance. \r\n'),
(221, 44, '10. How do I take Wild Amla Juice on a daily basis?', 'Have 15â€“30 ml of diluted juice in water, preferably an empty stomach in the morning.\r\n'),
(222, 44, '11. Does Wild Amla Juice help with thyroid health?', 'Yes, by lowering inflammation and oxidative stress, indirectly, it can promote thyroid function and glandular equilibrium. \r\n'),
(223, 44, '12. Does Wild Amla Juice have side effects?', 'It is normally well tolerated, but excess use can cause mild acidity or loose stool in some users.\r\n'),
(224, 47, '1. Can I consume She Care Plus and Apple Cider Vinegar together?', ' Yes, both products are natural and can be consumed together. They complement one another by maintaining hormonal balance, digestive health, weight management, and skin wellness.\r\n'),
(225, 47, '2. What is the ideal way to take She Care Plus and Apple Cider Vinegar?', 'Take Apple Cider Vinegar (1â€“2 tsp diluted in water) with meals and She Care Plus capsules/syrup after meals or as directed. Use the recommended dosage for optimal results.\r\n\r\n'),
(226, 47, '3. What is She Care Plus used for?', ' She Care Plus is designed to maintain women\'s hormonal balance, menstrual health, energy levels, and overall well-being.\r\n'),
(227, 47, '4. Is She Care Plus beneficial for irregular periods?', 'Yes, it does have Ayurvedic herbs such as Ashoka, Shatavari, and Lodhra that are well documented to aid menstrual cycles and fertility.\r\n'),
(228, 47, '5. Is She Care Plus okay to consume during menstruation?', ' Yes, taking it during menstruation is not only safe, but it also helps ease the symptoms such as cramps and fatigue.\r\n'),
(229, 47, '6. Does She Care Plus have side effects?', 'It is normally well-tolerated. If, however, you feel any inconvenience or allergy, stop use and see a doctor.\r\n\r\n'),
(230, 47, '7. What are the health benefits of Apple Cider Vinegar?', 'It helps digestion, maintains weight, stabilizes blood sugar, and encourages clearer skin.\r\n'),
(231, 47, '8. How do I take Apple Cider Vinegar every day?', ' Mix 1â€“2 teaspoons in a glass of warm water and have with meals. Don\'t take it neat.\r\n'),
(232, 47, '9. Can Apple Cider Vinegar assist with bloating or digestion?', ' Yes, it enhances stomach acid levels and digestion, alleviating bloating and indigestion.\r\n'),
(233, 47, '10. Is it okay to take Apple Cider Vinegar every day?', 'Yes, when diluted and consumed in moderation, it is safe for daily use.\r\n'),
(234, 47, '11. Can I use Apple Cider Vinegar for skin and hair?', ' Yes, it can be used topically (diluted) as a toner or hair rinse for acne, dandruff, and scalp issues.\r\n\r\n'),
(235, 49, '1. Can I take BP Care Juice and Apple Cider Vinegar together?', 'Yes, both are natural and can be consumed together. They can help maintain healthy blood pressure, enhanced metabolism, digestion, and overall cardiovascular health.\r\n\r\n'),
(236, 49, '2. How should BP Care Juice and Apple Cider Vinegar be consumed?', 'Take 15â€“30 ml of BP Care Juice in the morning on an empty stomach. Apple Cider Vinegar may be taken by mixing 1â€“2 teaspoons in a glass of water before meals.\r\n\r\n'),
(237, 49, '3. What is BP Care Juice used for?', ' BP Care Juice is formulated to maintain healthy blood pressure levels naturally with herbs such as Arjuna, Brahmi, and Ashwagandha.\r\n'),
(238, 49, '4. How frequently should I consume BP Care Juice?', 'Take 15â€“30 ml twice a day, ideally on an empty stomach or as directed by a healthcare professional.\r\n'),
(239, 49, '5. Can I substitute BP Care Juice for my blood pressure medication?', 'No, it is a complementary natural supplement. Always consult your physician before altering or discontinuing any prescription drug.\r\n'),
(240, 49, '6. Does BP Care Juice have any side effects?', 'It is usually safe if used as instructed. In case you feel uncomfortable, stop using it and consult your doctor.\r\n'),
(241, 49, '7. Is BP Care Juice safe for extended use?', 'Yes, it contains natural ingredients and is safe to use regularly on a long-term basis with proper advice.\r\n\r\n');
INSERT INTO `faqs` (`FAQId`, `ProductId`, `Question`, `Answer`) VALUES
(242, 49, '8. What are the advantages of Apple Cider Vinegar?', 'Apple Cider Vinegar helps with weight control, digestion, blood sugar level, and cleansing.\r\n\r\n'),
(243, 49, '9. How can I take Apple Cider Vinegar on a daily basis?', 'Mix 1â€“2 teaspoons in water with a glass and take before eating. Do not take it undiluted.\r\n'),
(244, 49, '10. Is Apple Cider Vinegar able to treat high blood pressure?', 'It can aid indirectly by stimulating heart health as well as fat loss, but it must never be used by itself as treatment for hypertension.\r\n\r\n'),
(245, 49, '11. Is it OK to take Apple Cider Vinegar daily?', 'Yes, when diluted and consumed in moderation, it is safe for most people for daily use.\r\n'),
(246, 49, '12. Can Apple Cider Vinegar be used externally as well?', ' Yes, it can be used (diluted) on the skin for acne or as a hair rinse to reduce dandruff and improve scalp health.\r\n\r\n'),
(247, 42, '1. Can I take Wild Amla Juice and Wheatgrass Juice together?', '\r\nYes, they can be consumed together since both are nutrient and antioxidant-rich. This combination aids in immunity boost, digestion, detoxification of the body, and skin and hair care.\r\n\r\n'),
(248, 42, '2. What is the best time to take Wild Amla and Wheatgrass Juice?', 'For optimal results, take both on an empty stomach in the morningâ€”take 15â€“30 ml each juice separately or with water.\r\n'),
(249, 42, '3. What is the health value of Wild Amla Juice?', ' It enhances immunity, gives a glow to the skin, improves vision, fortifies hair, and aids digestion because it is rich in vitamin C.\r\n\r\n'),
(250, 42, '4.Is Wild Amla Juice suitable for acidity?', ' Yes, it\'s cooling and anti-inflammatory in nature and helps calm the stomach while decreasing acidity.\r\n\r\n'),
(251, 42, '5. Can one take Wild Amla Juice every day?', 'Yes. It is safe and healthy for daily consumption when taken according to recommended dosage.\r\n\r\n'),
(252, 42, '6. Is Wild Amla useful in detoxification?', 'Yes, it is a natural detoxifier, cleaning the blood and liver as well as helping with metabolism.\r\n\r\n'),
(253, 42, '7. Is Wild Amla Juice good for hair growth?', 'Yes, its antioxidant and vitamin C richness helps provide nutrition to the scalp and stabilize hair roots.\r\n'),
(254, 42, '8. What are the advantages of Wheatgrass Juice?', 'Wheatgrass contains high amounts of chlorophyll, vitamins, and minerals that detoxify the body, help in digestion, boost energy, and improve blood health.\r\n'),
(255, 42, '9. How do I take Wheatgrass Juice daily?', 'Take 15â€“30 ml combined in a glass of water on an empty stomach, preferably in the morning.\r\n'),
(256, 42, '10. Does Wheatgrass Juice aid in weight loss?', '\r\nYes, it helps detoxify the digestive system, enhance metabolism, and might help in healthy weight management.\r\n'),
(257, 42, '11. Is Wheatgrass safe to consume every day?', 'Yes, it is safe to consume when taken within suggested dosages. Begin with a little dose and build up gradually.\r\n\r\n'),
(258, 42, '12. Can Wheatgrass Juice increase hemoglobin levels?', 'Yes, it is a good source of chlorophyll and iron, which aid red blood cell formation and enhance hemoglobin levels.\r\n\r\n'),
(259, 43, '1.I can take Thyro Balance and Apple Cider Vinegar at the same time?', '\r\nYes, both are acceptable to take at the same time. Thyro Balance enhances thyroid function naturally, and Apple Cider Vinegar supports metabolism and digestionâ€”together they could perhaps support enhanced hormonal balance and weight control.\r\n'),
(260, 43, '2. How do I use Thyro Balance and ACV together correctly?', 'Take 15â€“30 ml of Thyro Balance juice in the morning on an empty stomach. Apple Cider Vinegar may be consumed by mixing 1â€“2 tsp with water before eating.\r\n'),
(261, 43, '3. What is Thyro Balance Juice used for?', 'Thyro Balance aids in healthy thyroid functioning and may assist in symptom management such as fatigue, weight changes, and mood swings.\r\n'),
(262, 43, '4. Which of the following ingredients are there in Thyro Balance Juice?', 'It has herbs such as Kanchanar, Ashwagandha, Brahmi, and Guggulâ€”all used in Ayurveda to normalize thyroid hormones.\r\n'),
(263, 43, '5. Can Thyro Balance treat hypothyroidism and hyperthyroidism?', '\r\nIt is generally supportive of hypothyroidism, but always consult a physician before using it with ongoing medication for any thyroid disorder.\r\n'),
(264, 43, '6. Is Thyro Balance safe for long-term consumption?', '\r\nYes, it is safe for long-term consumption when taken according to the recommended dosage.\r\n'),
(265, 43, '7. Can I take Thyro Balance with thyroid medications?', '\r\n Yes, but keep a 30-minute gap and consult your doctor before taking it with any prescription drug.\r\n'),
(266, 43, '8. What are the health benefits of Apple Cider Vinegar?', ' ACV aids digestion, increases metabolism, regulates blood sugar levels, and aids in weight management.\r\n'),
(267, 43, '9. How do I consume Apple Cider Vinegar every day?', '\r\n Mix 1â€“2 teaspoons in a glass of lukewarm water and have before meals, ideally in the morning or before lunch.\r\n'),
(268, 43, '10. Is Apple Cider Vinegar useful for weight loss?', '\r\n Yes, if used with a healthy lifestyle, it can aid fat metabolism and appetite suppression.\r\n'),
(269, 43, '11. Can I use ACV with thyroid disorders?', '\r\nYes, ACV is generally safe, but itâ€™s best to consult your healthcare provider if you are on thyroid medication.\r\n\r\n'),
(270, 43, '12. Is ACV safe for everyday use?', '\r\n Yes, when diluted and taken as directed, it is safe for most people.\r\n\r\n'),
(271, 45, '1. Can I take Thyro Balance Juice and Karela Neem Jamun Juice together?', '\r\nYes, both juices are made from natural Ayurvedic ingredients and can be taken together to support thyroid balance, blood sugar control, metabolism, and overall wellness.\r\n'),
(272, 45, '2. What is the right time to take Thyro Balance and Karela Neem Jamun Juice?', '\r\n Itâ€™s best to take both juices on an empty stomach in the morningâ€”15â€“30 ml of each mixed with water or taken separately.\r\n\r\n'),
(273, 45, '3. What does Thyro Balance Juice do?', '\r\nIt maintains healthy thyroid function, particularly for hypothyroidism, and can alleviate fatigue, mood swings, and weight problems.\r\n'),
(274, 45, '4. Which herbs does Thyro Balance Juice comprise?', '\r\nIt includes Kanchanar, Ashwagandha, Brahmi, Guggul, and other thyroid-supportive Ayurvedic herbs.\r\n\r\n'),
(275, 45, '5. Is Thyro Balance safe for long-term use?', '\r\nYes, when consumed in the recommended dose, it is safe and useful for long-term management of the thyroid.\r\n\r\n'),
(276, 45, '6. Can I consume Thyro Balance with thyroid medication?', '\r\nYes, maintain a 30-minute interval and consult your physician prior to using prescription medications.\r\n\r\n'),
(277, 45, '7. Is Thyro Balance Juice beneficial for weight management in thyroid conditions?', '\r\nYes, by promoting hormonal balance and metabolism, it can help manage thyroid-induced weight gain.\r\n'),
(278, 45, '8. What are the key advantages of Karela Neem & Jamun Juice?', '\r\nIt promotes healthy blood sugar levels, cleanses the blood, enhances digestion, and enhances skin health.\r\n\r\n'),
(279, 45, '9. Is it safe for diabetic patients?', '\r\nYes, it\'s been used traditionally to help control blood sugar and might promote diabetic well-being.\r\n'),
(280, 45, '10. How do I take Karela Neem Jamun Juice?', '\r\nTake 15â€“30 ml with lukewarm water on an empty stomach in the morning, or as directed by a medical practitioner.\r\n'),
(281, 45, '11.Can I use this juice every day?', '\r\nYes, daily use is safe and may help in long-term sugar level management and detoxification.\r\n\r\n'),
(282, 45, '12. Does this juice have any side effects?', '\r\n It is generally well-tolerated. Those with low blood sugar or specific medical conditions should consult a doctor before use.\r\n\r\n'),
(283, 46, '1. Can I take Cholesterol Care and Karela Neem Jamun Juice together?', '\r\nYes, both the juices are prepared from natural ingredients and can be consumed together safely. They help maintain heart health, regulate cholesterol and blood sugar levels, and aid detoxification.\r\n'),
(284, 46, '2. When and how should I take Cholesterol Care and Karela Neem Jamun Juice?', '\r\nIt is recommended to take both the juices on an empty stomach in the morningâ€”15â€“30 ml each, either blended in a glass of water or separately.\r\n\r\n'),
(285, 46, '3. What is Cholesterol Care Juice for?', '\r\nIt is formulated to address bad cholesterol (LDL), maintain heart health, and promote healthy blood circulation.\r\n\r\n'),
(286, 46, '4. Which herbs does Cholesterol Care Juice contain?', '\r\nIngredients usually consist of Arjuna, Garlic, Ginger, Lemon, and other herbs that are good for the heart.\r\n'),
(287, 46, '5. Is Cholesterol Care Juice safe to take for a long period of time?', '\r\nYes, it is prepared from Ayurvedic herbs and is safe for daily consumption if taken according to the recommended dosage.\r\n'),
(288, 46, '6. Can I consume this juice with cholesterol medicines?', '\r\n Yes, but keep a gap of 30 minutes and take advice from your doctor to prevent interactions.\r\n'),
(289, 46, '7. Does it reduce triglycerides?', '\r\n Yes, its herbal ingredients can reduce triglycerides and enhance lipid profile in the long run.\r\n'),
(290, 46, '8. What are the major advantages of Karela Neem Jamun Juice?', '\r\nIt controls blood sugar levels, cleanses the blood, makes the skin healthy, and supports digestion.\r\n'),
(291, 46, '9. Is this juice good for diabetics?', '\r\nYes, it is extremely beneficial for diabetics or those who want to control high blood sugar naturally.\r\n\r\n'),
(292, 46, '10. How do I use Karela Neem Jamun Juice daily?', '\r\nTake 15â€“30 ml in water on an empty stomach in the morning.\r\n'),
(293, 46, '11. Does Karela Neem Jamun contribute to weight loss?', '\r\nYes, it can help with metabolism and fat burning, helping maintain a healthy weight.\r\n'),
(294, 46, '12. Can it be taken daily?', '\r\nYes, it can be used daily when taken in the recommended quantity.\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `interested_product` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `message` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location_master`
--

CREATE TABLE `location_master` (
  `LocationId` int NOT NULL,
  `LocationHeading` varchar(200) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Location title/heading',
  `LocationAddress` text COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Full address',
  `City` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'City',
  `State` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'State',
  `Pincode` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Postal code',
  `Country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'India' COMMENT 'Country',
  `Phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Location phone number',
  `Email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Location email',
  `IsActive` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT 'Y' COMMENT 'Active status',
  `DisplayOrder` int DEFAULT '1' COMMENT 'Display order',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location_master`
--

INSERT INTO `location_master` (`LocationId`, `LocationHeading`, `LocationAddress`, `City`, `State`, `Pincode`, `Country`, `Phone`, `Email`, `IsActive`, `DisplayOrder`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Head Office', 'S.NO.31/32, 1st Floor, Old Mumbai Pune Road, Dapoli (Maharashtra) Pune - 411012', 'Pune', 'Maharashtra', '411012', 'India', '+91 9834243754', 'support@mynutrify.com', 'Y', 1, '2025-07-23 07:40:13', '2025-07-23 07:40:13');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `time` varchar(30) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `user_id`, `time`, `ip_address`) VALUES
(1, 1, '1751109973', NULL),
(2, 1, '1751110008', NULL),
(3, 1, '1751110177', NULL),
(4, 1, '1751110192', NULL),
(5, 1, '1751110194', NULL),
(6, 1, '1751110217', NULL),
(7, 1, '1751110240', NULL),
(8, 1, '1751112997', NULL),
(9, 1, '1751454127', NULL),
(10, 1, '1751691864', NULL),
(11, 1, '1752226013', NULL),
(12, 1, '1752492369', NULL),
(13, 1, '1752816978', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `username`, `email`, `password`, `salt`, `created_at`) VALUES
(1, 'admin', 'admin@mynutrify.com', '2f252bf9059a5fc4fd63bac755dc865582ef29e5109f7ff245ece1a702cd6aa777c90ac5bf784b17600dd4da10d9cc37500748a1f64ee8bd2385937591df81ba', '15e70bdec834f8e05b19f81ec01996df71e4927881d3b5ff7b9cc96c53636d143dfcc3983f5311d945f1497fb31537f42f96f21c297959b3b33cdfbd2588cf51', '2025-06-28 11:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `model_images`
--

CREATE TABLE `model_images` (
  `ImageId` int NOT NULL,
  `ProductId` int NOT NULL,
  `PhotoPath` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `model_images`
--

INSERT INTO `model_images` (`ImageId`, `ProductId`, `PhotoPath`) VALUES
(34, 5, 'Img_1486.webp'),
(2, 1, 'Img_9740.webp'),
(3, 1, 'Img_6403.webp'),
(33, 5, 'Img_7168.webp'),
(32, 5, 'Img_3319.webp'),
(31, 5, 'Img_6579.webp'),
(361, 25, 'Img_4833.jpg'),
(55, 7, 'Img_4231.webp'),
(54, 7, 'Img_7567.webp'),
(362, 25, 'Img_7734.jpg'),
(363, 25, 'Img_2949.jpg'),
(313, 9, 'Img_1646.jpg'),
(110, 21, 'Img_4565.jpg'),
(86, 12, 'Img_5264.jpg'),
(310, 23, 'Img_3259.jpg'),
(108, 21, 'Img_2563.jpg'),
(22, 16, 'Img_5921.jpg'),
(23, 17, 'Img_2217.jpg'),
(300, 18, 'Img_8286.jpg'),
(77, 19, 'Img_3045.jpg'),
(26, 20, 'Img_8822.jpg'),
(364, 25, 'Img_4451.jpg'),
(306, 23, 'Img_6382.jpg'),
(355, 23, 'Img_9344.jpg'),
(116, 13, 'Img_2599.jpg'),
(218, 9, 'Img_2435.jpg'),
(109, 21, 'Img_8504.jpg'),
(282, 25, 'Img_2055.jpg'),
(318, 22, 'Img_8293.jpg'),
(319, 22, 'Img_7677.jpg'),
(222, 14, 'Img_8799.jpg'),
(251, 6, 'Img_4620.jpg'),
(348, 6, 'Img_8524.jpg'),
(111, 21, 'Img_8469.jpg'),
(320, 22, 'Img_7187.jpg'),
(92, 12, 'Img_3101.jpg'),
(371, 9, 'Img_2667.jpg'),
(117, 13, 'Img_3644.jpg'),
(367, 11, 'Img_6013.jpg'),
(342, 14, 'Img_9015.jpg'),
(87, 12, 'Img_3281.jpg'),
(78, 19, 'Img_8135.jpg'),
(79, 19, 'Img_1867.jpg'),
(90, 12, 'Img_1935.jpg'),
(321, 22, 'Img_5845.jpg'),
(130, 34, 'Img_7126.jpg'),
(131, 34, 'Img_9036.jpg'),
(132, 35, 'Img_6514.jpg'),
(133, 35, 'Img_8635.jpg'),
(134, 36, 'Img_6984.jpg'),
(135, 38, 'Img_4013.jpg'),
(136, 38, 'Img_5363.jpg'),
(137, 39, 'Img_3381.jpg'),
(138, 39, 'Img_8877.jpg'),
(139, 40, 'Img_7573.jpg'),
(140, 40, 'Img_8565.jpg'),
(141, 41, 'Img_9670.jpg'),
(142, 41, 'Img_1145.jpg'),
(143, 42, 'Img_4658.jpg'),
(144, 42, 'Img_2927.jpg'),
(145, 43, 'Img_2544.jpg'),
(146, 43, 'Img_1232.jpg'),
(147, 44, 'Img_1852.jpg'),
(148, 44, 'Img_3813.jpg'),
(149, 45, 'Img_5364.jpg'),
(150, 45, 'Img_9563.jpg'),
(151, 46, 'Img_9559.jpg'),
(152, 46, 'Img_6410.jpg'),
(153, 47, 'Img_5286.jpg'),
(154, 49, 'Img_8207.jpg'),
(155, 49, 'Img_6415.jpg'),
(156, 47, 'Img_1264.jpg'),
(210, 15, 'Img_7102.jpg'),
(209, 15, 'Img_6192.jpg'),
(208, 15, 'Img_6653.jpg'),
(341, 15, 'Img_4535.jpg'),
(204, 15, 'Img_5383.jpg'),
(340, 15, 'Img_9408.jpg'),
(339, 15, 'Img_6427.jpg'),
(338, 15, 'Img_4823.jpg'),
(337, 15, 'Img_7003.jpg'),
(312, 9, 'Img_1153.jpg'),
(372, 9, 'Img_3159.jpg'),
(373, 9, 'Img_4290.jpg'),
(374, 9, 'Img_6050.jpg'),
(336, 15, 'Img_7459.jpg'),
(375, 9, 'Img_3752.jpg'),
(223, 14, 'Img_1654.jpg'),
(224, 14, 'Img_7756.jpg'),
(343, 14, 'Img_7782.jpg'),
(344, 14, 'Img_4467.jpg'),
(345, 14, 'Img_6673.jpg'),
(346, 14, 'Img_2163.jpg'),
(347, 14, 'Img_6273.jpg'),
(230, 14, 'Img_5757.jpg'),
(281, 25, 'Img_4592.jpg'),
(365, 25, 'Img_7780.jpg'),
(280, 25, 'Img_1133.jpg'),
(293, 22, 'Img_6100.jpg'),
(292, 22, 'Img_3107.jpg'),
(317, 22, 'Img_9596.jpg'),
(291, 22, 'Img_5609.jpg'),
(252, 6, 'Img_9458.jpg'),
(253, 6, 'Img_9463.jpg'),
(349, 6, 'Img_1251.jpg'),
(350, 6, 'Img_9746.jpg'),
(351, 6, 'Img_1548.jpg'),
(352, 6, 'Img_4766.jpg'),
(353, 6, 'Img_9878.jpg'),
(354, 6, 'Img_7320.jpg'),
(261, 11, 'Img_4359.jpg'),
(262, 11, 'Img_3413.jpg'),
(263, 11, 'Img_6251.jpg'),
(368, 11, 'Img_4909.jpg'),
(369, 11, 'Img_5107.jpg'),
(376, 11, 'Img_1788.jpg'),
(370, 11, 'Img_3631.jpg'),
(268, 11, 'Img_6456.jpg'),
(308, 23, 'Img_9315.jpg'),
(356, 23, 'Img_8607.jpg'),
(357, 23, 'Img_2155.jpg'),
(358, 23, 'Img_8773.jpg'),
(359, 23, 'Img_2212.jpg'),
(360, 23, 'Img_3707.jpg'),
(307, 23, 'Img_7463.jpg'),
(366, 25, 'Img_7587.jpg'),
(288, 25, 'Img_6955.jpg'),
(289, 25, 'Img_8338.jpg'),
(322, 22, 'Img_9436.jpg'),
(299, 22, 'Img_8338.jpg'),
(301, 18, 'Img_4189.jpg'),
(302, 18, 'Img_2584.jpg'),
(303, 18, 'Img_7647.jpg'),
(304, 18, 'Img_2270.jpg'),
(305, 18, 'Img_9374.jpg'),
(309, 23, 'Img_1943.jpg'),
(311, 23, 'Img_8487.jpg'),
(314, 9, 'Img_3178.jpg'),
(315, 9, 'Img_9454.jpg'),
(323, 10, 'Img_9664.jpg'),
(324, 10, 'Img_7195.jpg'),
(325, 10, 'Img_7424.jpg'),
(326, 10, 'Img_5411.jpg'),
(327, 10, 'Img_8297.jpg'),
(328, 10, 'Img_5856.jpg'),
(329, 10, 'Img_3476.jpg'),
(330, 10, 'Img_9938.jpg'),
(331, 10, 'Img_5337.jpg'),
(332, 10, 'Img_4522.jpg'),
(334, 10, 'Img_6228.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `Id` int NOT NULL,
  `OrderId` text COLLATE utf8mb4_general_ci NOT NULL,
  `ProductId` int NOT NULL,
  `ProductCode` text COLLATE utf8mb4_general_ci,
  `Quantity` int NOT NULL,
  `Size` text COLLATE utf8mb4_general_ci,
  `Price` int NOT NULL,
  `SubTotal` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`Id`, `OrderId`, `ProductId`, `ProductCode`, `Quantity`, `Size`, `Price`, `SubTotal`) VALUES
(1, 'MN000001', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(2, 'MN000002', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(3, 'MN000003', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(4, 'MN000004', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(5, 'MN000005', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(6, 'MN000006', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(7, 'MN000007', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(8, 'MN000007', 15, 'MN-AC100', 1, '1000 ml | Pack of 1', 749, 749),
(9, 'MN000008', 10, 'MN-DC100', 1, '1000 ml | Pack of 1', 549, 549),
(10, 'MN000009', 8, 'MN-BP100', 2, '1000 ml | Pack of 1 â‚¹1,049.00', 999, 1998),
(11, 'MN000012', 1, 'TP123', 1, 'M', 10, 10),
(12, 'MN000010', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(13, 'MN000011', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(14, 'MN000012', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(15, 'MN000013', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(16, 'MN000014', 13, 'MN-TB100', 1, '1000 ml | Pack of 1', 499, 499),
(17, 'MN000016', 13, 'MN-TB100', 1, '1000 ml | Pack of 1', 499, 499),
(20, 'MN000019', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(25, 'MN000027', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(26, 'MN000028', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(27, 'MN000029', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(28, 'MN000030', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(29, 'MN000031', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(30, 'MN000032', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(31, 'MN000033', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(32, 'MN000034', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(33, 'MN000035', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(34, 'MN000036', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(35, 'MN000037', 16, 'MN-SJ100', 1, '20 gm | Pack of 1', 1, 1),
(43, 'MN000043', 12, 'MN-SC100', 1, '1000 ml | Pack of 1', 499, 499),
(75, 'MN000069', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(100, 'MN000096', 10, 'MN-DC100', 1, '1000 ml | Pack of 1', 549, 549),
(101, 'MN000097', 15, 'MN-AC100', 1, '1000 ml | Pack of 1', 749, 749),
(102, 'MN000098', 12, 'MN-SC100', 1, '1000 ml | Pack of 1', 499, 499),
(103, 'MN000099', 12, 'MN-SC100', 1, '1000 ml | Pack of 1', 499, 499),
(104, 'MN000100', 19, 'MN-SR020', 1, '20 gm | Pack of 1 â‚¹1,199.00', 499, 499),
(105, 'MN000101', 10, 'MN-DC100', 1, '1000 ml | Pack of 1', 549, 549),
(106, 'MN000102', 10, 'MN-DC100', 1, '1000 ml | Pack of 1', 549, 549),
(107, 'MN000103', 14, 'MN-WG100', 1, '1000 ml | Pack of 1', 449, 449),
(108, 'MN000104', 12, 'MN-SC100', 1, '1000 ml | Pack of 1', 499, 499),
(109, 'MN000105', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(113, 'MN000109', 9, 'MN-CC100', 1, '1000 ml | Pack of 1', 599, 599),
(114, 'MN000110', 21, 'MN-SG020', 1, '20 gm | Pack of 1 â‚¹1,849.00', 899, 899),
(115, 'MN000111', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(116, 'MN000112', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(117, 'MN000113', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(120, 'MN000115', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(123, 'MN000118', 22, 'MN-BC100', 1, '1000 ml | Pack of 1 â‚¹1,049.00', 999, 999),
(124, 'MN000119', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(125, 'MN000120', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(126, 'MN000121', 6, 'MN-AM100', 1, '1000 ml | Pack of 1', 249, 249),
(127, 'MN000123', 19, 'MN-SR020', 1, '20 gm | Pack of 1 â‚¹1,199.00', 499, 499),
(128, 'MN000126', 10, 'MN-DC100', 1, '1000 ml | Pack of 1', 549, 549),
(129, 'MN000127', 23, 'MN-SC020', 1, '1000 ml | Pack of 1', 499, 499),
(130, 'MN000129', 19, 'MN-SR020', 1, '20 gm | Pack of 1 â‚¹1,199.00', 499, 499),
(131, 'MN000130', 15, 'MN-AC100', 1, '1000 ml | Pack of 1', 749, 749),
(132, 'MN000131', 25, 'MN-C2100', 1, '1000 ml | Pack of 1', 499, 499),
(133, 'MN000132', 25, 'MN-C2100', 1, '1000 ml | Pack of 1', 499, 499),
(134, 'MN000133', 15, 'MN-AC100', 1, '1000 ml | Pack of 1', 749, 749),
(138, 'MN000134', 19, 'MN-SR020', 3, '20 gm | Pack of 1 â‚¹1,199.00', 499, 1497),
(139, 'MN000135', 25, 'MN-C2100', 1, '1000 ml | Pack of 1', 499, 499),
(140, 'MN000136', 25, 'MN-C2100', 1, '1000 ml | Pack of 1', 499, 499),
(141, 'MN000137', 19, 'MN-SR020', 1, '20 gm | Pack of 1 â‚¹1,199.00', 499, 499),
(142, 'MN000138', 19, 'MN-SR020', 1, '20 gm | Pack of 1 â‚¹1,199.00', 499, 499),
(143, 'MN000003', 6, 'MN-AM100', 1, '1000', 249, 249),
(145, 'MN000015', 6, 'MN-AM100', 1, '1000', 249, 249),
(146, 'MN000015', 10, 'MN-DC100', 1, '1000', 549, 549),
(147, 'MN000016', 10, 'MN-DC100', 1, '1000', 549, 549),
(148, 'MN000019', 10, 'MN-DC100', 1, '1000', 549, 549),
(149, 'SIM785452', 1, 'SIM-PRODUCT', 1, 'Test Size', 299, 299),
(150, 'SIM785462', 1, 'SIM-PRODUCT', 1, 'Test Size', 299, 299),
(151, 'SIM786218', 1, 'SIM-PRODUCT', 1, 'Test Size', 299, 299),
(152, 'SIM786473', 1, 'SIM-PRODUCT', 1, 'Test Size', 299, 299),
(153, 'SIM788132', 1, 'SIM-PRODUCT', 1, 'Test Size', 299, 299),
(154, 'SIM788324', 1, 'SIM-PRODUCT', 1, 'Test Size', 299, 299),
(156, 'MN000026', 6, 'MN-AM100', 3, '1000', 249, 747),
(157, 'MN000028', 6, 'MN-AM100', 3, '1000', 249, 747),
(158, 'MN000029', 6, 'MN-AM100', 3, '1000', 249, 747),
(159, 'MN000030', 6, 'MN-AM100', 3, '1000', 249, 747),
(160, 'MN000031', 6, 'MN-AM100', 6, '1000', 249, 1494),
(161, 'MN000032', 6, 'MN-AM100', 6, '1000', 249, 1494),
(162, 'MN000033', 6, 'MN-AM100', 6, '1000', 249, 1494),
(163, 'MN000034', 10, 'MN-DC100', 1, '1000', 549, 549),
(164, 'MN000035', 10, 'MN-DC100', 1, '1000', 549, 549),
(165, 'MN000036', 10, 'MN-DC100', 1, '1000', 549, 549),
(166, 'MN000037', 10, 'MN-DC100', 1, '1000', 549, 549),
(167, 'MN000038', 10, 'MN-DC100', 1, '1000', 549, 549),
(168, 'MN000039', 10, 'MN-DC100', 1, '1000', 549, 549),
(169, 'MN000040', 10, 'MN-DC100', 2, '1000', 549, 1098),
(170, 'MN000041', 10, 'MN-DC100', 1, '1000', 549, 549),
(171, 'MN000042', 10, 'MN-DC100', 1, '1000', 549, 549),
(172, 'MN000043', 10, 'MN-DC100', 1, '1000', 549, 549),
(173, 'MN000044', 10, 'MN-DC100', 1, '1000', 549, 549),
(174, 'MN000045', 10, 'MN-DC100', 1, '1000', 549, 549),
(175, 'MN000046', 10, 'MN-DC100', 1, '1000', 549, 549),
(178, 'ON1752061926930', 50, '12121', 1, '500 ml | Pack of 1', 2, 2),
(179, 'ON1752127967565', 50, '12121', 1, '.00 500 ml | Pack of 1', 2, 2),
(180, 'MN000047', 6, 'MN-AM100', 2, '1000', 249, 498),
(181, 'MN000047', 19, 'MN-SR020', 1, '20', 499, 499),
(182, 'MN000048', 6, 'MN-AM100', 1, '0', 249, 249),
(183, 'MN000049', 50, '12121', 1, '0', 2, 2),
(184, 'MN000050', 21, 'MN-SG020', 1, '0', 899, 899),
(185, 'MN000051', 10, 'MN-DC100', 1, '0', 549, 549),
(186, 'MN000052', 19, 'MN-SR020', 1, '0', 499, 499),
(187, 'MN000053', 6, 'MN-AM100', 1, '0', 249, 249),
(188, 'MN000054', 18, 'MN-SG020', 1, '0', 699, 699),
(189, 'MN000055', 47, 'MN-C14200', 1, '0', 1248, 1248),
(190, 'MN000056', 6, 'MN-AM100', 1, '0', 249, 249),
(191, 'MN000057', 9, 'MN-CC100', 1, '0', 599, 599),
(192, 'MN000058', 6, 'MN-AM100', 1, '0', 249, 249),
(193, 'MN000059', 6, 'MN-AM100', 1, '0', 249, 249),
(194, 'MN000060', 22, '', 1, '0', 999, 999),
(195, 'MN000061', 6, '', 1, '0', 249, 249),
(196, 'MN000062', 6, '', 1, '0', 249, 249),
(197, 'MN000063', 6, '', 1, '0', 249, 249),
(198, 'MN000064', 6, '', 1, '0', 249, 249),
(199, 'MN000065', 6, '', 1, '0', 249, 249),
(200, 'MN000066', 6, '', 1, '0', 249, 249),
(201, 'MN000067', 21, '', 3, '20', 899, 2697),
(202, 'MN000068', 11, 'MN-KN100', 1, '0', 349, 349),
(203, 'MN000069', 18, '', 1, '0', 699, 699),
(204, 'MN000070', 6, '', 1, '0', 249, 249),
(205, 'MN000071', 10, '', 1, '0', 549, 549),
(206, 'MN000072', 6, '', 1, '1000', 249, 249),
(207, 'MN000073', 6, '', 1, '1000', 249, 249),
(208, 'MN000074', 1, 'TP001', 1, '0', 100, 100),
(209, 'MN000075', 1, 'TP001', 1, '0', 100, 100),
(210, 'MN000076', 1, 'TP001', 1, '0', 299, 299),
(211, 'MN000077', 6, '', 1, '0', 249, 249),
(212, 'MN000078', 1, 'TP001', 1, '0', 299, 299),
(213, 'MN000079', 1, 'TP001', 1, '0', 299, 299),
(214, 'MN000080', 1, 'TP001', 1, '0', 299, 299),
(215, 'MN000081', 1, 'PTP001', 1, '0', 599, 599),
(216, 'MN000082', 1, 'ITP001', 1, '0', 450, 450),
(217, 'MN000083', 1, 'PTP001', 1, '0', 599, 599),
(218, 'MN000084', 1, 'TP001', 1, '0', 299, 299),
(219, 'MN000085', 11, '', 1, '0', 349, 349),
(220, 'MN000086', 21, '', 1, '0', 899, 899),
(221, 'MN000087', 22, '', 1, '0', 999, 999),
(222, 'MN000088', 47, '', 2, '0', 1248, 2496),
(223, 'MN000089', 38, '', 1, '0', 1148, 1148),
(224, 'MN000090', 10, '', 1, '0', 549, 549),
(225, 'MN000091', 6, '', 1, '0', 249, 249),
(226, 'MN000092', 9, '', 1, '0', 599, 599),
(227, 'MN000093', 6, '', 1, '1000', 249, 249),
(228, 'MN000093', 9, '', 1, '1000', 599, 599);

-- --------------------------------------------------------

--
-- Table structure for table `order_master`
--

CREATE TABLE `order_master` (
  `Id` int NOT NULL,
  `OrderId` varchar(50) NOT NULL,
  `CustomerId` int NOT NULL,
  `CustomerType` varchar(50) DEFAULT 'Registered',
  `OrderDate` date NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentStatus` varchar(50) DEFAULT 'Due',
  `OrderStatus` varchar(50) DEFAULT 'Placed',
  `ShipAddress` text,
  `PaymentType` varchar(50) DEFAULT 'COD',
  `CouponCode` varchar(50) DEFAULT NULL,
  `CouponDiscount` decimal(10,2) DEFAULT '0.00',
  `PointsUsed` int DEFAULT '0',
  `PointsDiscount` decimal(10,2) DEFAULT '0.00',
  `TransactionId` varchar(100) DEFAULT 'NA',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Waybill` varchar(50) DEFAULT NULL COMMENT 'Primary Waybill/AWB number from delivery provider',
  `WaybillNumber` varchar(50) DEFAULT NULL COMMENT 'Alternative Waybill field for compatibility',
  `delivery_status` varchar(50) DEFAULT 'pending' COMMENT 'Current delivery status',
  `delivery_provider` varchar(50) DEFAULT 'delhivery' COMMENT 'Delivery provider',
  `tracking_url` text COMMENT 'Tracking URL from delivery provider',
  `estimated_delivery_date` date DEFAULT NULL COMMENT 'Estimated delivery date',
  `actual_delivery_date` timestamp NULL DEFAULT NULL COMMENT 'Actual delivery date and time',
  `delivery_attempts` int DEFAULT '0' COMMENT 'Number of delivery attempts made',
  `delivery_notes` text COMMENT 'Delivery notes and remarks',
  `last_tracking_update` timestamp NULL DEFAULT NULL COMMENT 'Last tracking update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_master`
--

INSERT INTO `order_master` (`Id`, `OrderId`, `CustomerId`, `CustomerType`, `OrderDate`, `Amount`, `PaymentStatus`, `OrderStatus`, `ShipAddress`, `PaymentType`, `CouponCode`, `CouponDiscount`, `PointsUsed`, `PointsDiscount`, `TransactionId`, `CreatedAt`, `Waybill`, `WaybillNumber`, `delivery_status`, `delivery_provider`, `tracking_url`, `estimated_delivery_date`, `actual_delivery_date`, `delivery_attempts`, `delivery_notes`, `last_tracking_update`) VALUES
(1, 'MN000001', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Created', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 07:30:53', 'MOCK1751808018791', NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(2, 'MN000002', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 07:39:16', 'AUTO17518683639987', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683639987', NULL, NULL, 0, NULL, NULL),
(3, 'MN000003', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 07:43:12', 'AUTO17518683639870', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683639870', NULL, NULL, 0, NULL, NULL),
(4, 'MN000004', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 07:47:10', 'AUTO17518683636486', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683636486', NULL, NULL, 0, NULL, NULL),
(5, 'MN000005', 21, 'Registered', '2025-07-05', 549.00, 'Due', 'Shipped', 'Khalid Kazi, khaldkazi2003@gmail.com, 8007996583, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 12:59:50', 'AUTO17518683638588', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683638588', NULL, NULL, 0, NULL, NULL),
(6, 'MN000006', 21, 'Registered', '2025-07-05', 549.00, 'Due', 'Shipped', 'Khalid Kazi, khaldkazi2003@gmail.com, 8007996583, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:01:02', 'AUTO17518683638286', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683638286', NULL, NULL, 0, NULL, NULL),
(7, 'MN000007', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:01:25', 'AUTO17518683634977', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683634977', NULL, NULL, 0, NULL, NULL),
(8, 'MN000008', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:02:38', 'AUTO17518683638235', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683638235', NULL, NULL, 0, NULL, NULL),
(9, 'MN000009', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:03:44', 'AUTO17518683636934', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683636934', NULL, NULL, 0, NULL, NULL),
(10, 'MN000010', 21, 'Registered', '2025-07-05', 549.00, 'Due', 'Shipped', 'Khalid Kazi, khaldkazi2003@gmail.com, 8007996583, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:05:02', 'AUTO17518683635579', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683635579', NULL, NULL, 0, NULL, NULL),
(11, 'MN000011', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:05:18', 'AUTO17518683635625', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683635625', NULL, NULL, 0, NULL, NULL),
(12, 'MN000012', 24, 'Registered', '2025-07-05', 249.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:07:54', 'AUTO17518683633323', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683633323', NULL, NULL, 0, NULL, NULL),
(13, 'MN000013', 24, 'Registered', '2025-07-05', 798.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtanagar, Samtangar, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:11:54', 'AUTO17518683638912', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683638912', NULL, NULL, 0, NULL, NULL),
(14, 'MN000014', 24, 'Registered', '2025-07-05', 798.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtanagar, Samtangar, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:14:09', 'AUTO17518683637858', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683637858', NULL, NULL, 0, NULL, NULL),
(15, 'MN000015', 24, 'Registered', '2025-07-05', 798.00, 'Due', 'Shipped', 'kalid, test@email.com, 8208593432, Samtanagar, Samtangar, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-05 13:15:35', 'AUTO17518683636569', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683636569', NULL, NULL, 0, NULL, NULL),
(16, 'MN000016', 16, 'Registered', '2025-07-06', 549.00, 'Due', 'Created', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-06 05:47:20', 'MOCK1751805217940', NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(19, 'MN000019', 16, 'Registered', '2025-07-06', 549.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-06 06:07:08', 'AUTO17518683635052', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683635052', NULL, NULL, 0, NULL, NULL),
(20, 'SIM785452', 9999, 'Registered', '2025-07-06', 299.00, 'Due', 'Shipped', 'Test Address, Test City, 123456', 'COD', NULL, 0.00, 0, 0.00, 'SIM_1751785452', '2025-07-06 07:04:12', 'AUTO17518683642351', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683642351', NULL, NULL, 0, NULL, NULL),
(21, 'SIM785462', 9999, 'Registered', '2025-07-06', 299.00, 'Due', 'Delivered', 'Test Address, Test City, 123456', 'COD', NULL, 0.00, 0, 0.00, 'SIM_1751785462', '2025-07-06 07:04:22', 'SIM1751785522548', NULL, 'delivered', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(22, 'SIM786218', 9999, 'Registered', '2025-07-06', 299.00, 'Due', 'Delivered', 'Test Address, Test City, 123456', 'COD', NULL, 0.00, 0, 0.00, 'SIM_1751786218', '2025-07-06 07:16:58', 'SIM1751786278798', NULL, 'delivered', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(23, 'SIM786473', 9999, 'Registered', '2025-07-06', 299.00, 'Due', 'Delivered', 'Test Address, Test City, 123456', 'COD', NULL, 0.00, 0, 0.00, 'SIM_1751786473', '2025-07-06 07:21:13', 'SIM1751786533376', NULL, 'delivered', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(24, 'SIM788132', 9999, 'Registered', '2025-07-06', 299.00, 'Due', 'Delivered', 'Test Address, Test City, 123456', 'COD', NULL, 0.00, 0, 0.00, 'SIM_1751788132', '2025-07-06 07:48:52', 'SIM1751788194704', NULL, 'delivered', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(25, 'SIM788324', 9999, 'Registered', '2025-07-06', 299.00, 'Due', 'In Transit', 'Test Address, Test City, 123456', 'COD', NULL, 0.00, 0, 0.00, 'SIM_1751788324', '2025-07-06 07:52:04', 'SIM1751788369945', NULL, 'in_transit', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(54, 'MN000026', 16, 'Registered', '2025-07-06', 747.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-06 12:30:37', 'AUTO17518683646044', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683646044', NULL, NULL, 0, NULL, NULL),
(55, 'MN000027', 24, 'Registered', '2025-07-06', 299.00, 'Due', 'Shipped', 'Test Customer, test@example.com, 8208593432, Test Address, Test Landmark, 123456, Test City, Test State', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-06 12:39:41', 'AUTO17518683647693', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683647693', NULL, NULL, 0, NULL, NULL),
(60, 'MN000028', 16, 'Registered', '2025-07-06', 747.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-06 12:52:16', 'AUTO17518683642064', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683642064', NULL, NULL, 0, NULL, NULL),
(79, 'MN000029', 16, 'Registered', '2025-07-06', 747.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, , 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-06 13:19:14', 'AUTO17518683641747', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683641747', NULL, NULL, 0, NULL, NULL),
(80, 'MN000030', 16, 'Registered', '2025-07-06', 747.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, , 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-06 13:29:32', 'AUTO17518683646726', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683646726', NULL, NULL, 0, NULL, NULL),
(81, 'MN000031', 16, 'Registered', '2025-07-07', 1494.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, Masjid Road, 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 06:00:21', 'AUTO17518683641849', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518683641849', NULL, NULL, 0, NULL, NULL),
(82, 'MN000032', 16, 'Registered', '2025-07-07', 1494.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, , 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 06:11:21', 'AUTO17518689744854', NULL, 'shipped', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(83, 'MN000033', 16, 'Registered', '2025-07-07', 1494.00, 'Due', 'Shipped', 'Srijeet, srijeetshikalgar00@mail.com, 8208593432, Samtangar, , 416410, Miraj, Maharashtra', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 06:28:59', 'AUTO17518697397819', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518697397819', NULL, NULL, 0, NULL, NULL),
(84, 'MN000034', 16, 'Registered', '2025-07-07', 549.00, 'Due', 'Shipped', 'Samtangar, , Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 06:59:44', 'AUTO17518715847045', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518715847045', NULL, NULL, 0, NULL, NULL),
(85, 'MN000035', 16, 'Registered', '2025-07-07', 549.00, 'Due', 'Shipped', 'Samtangar, , Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 10:49:36', 'AUTO17518853769109', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518853769109', NULL, NULL, 0, NULL, NULL),
(86, 'MN000036', 16, 'Registered', '2025-07-07', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 13:51:24', 'AUTO17518962855195', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518962855195', NULL, NULL, 0, NULL, NULL),
(87, 'MN000037', 16, 'Registered', '2025-07-07', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 14:17:42', 'AUTO17518978639590', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518978639590', NULL, NULL, 0, NULL, NULL),
(88, 'MN000038', 16, 'Registered', '2025-07-07', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 14:17:47', 'AUTO17518978675707', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518978675707', NULL, NULL, 0, NULL, NULL),
(89, 'MN000039', 16, 'Registered', '2025-07-07', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 14:18:11', 'AUTO17518978913049', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/AUTO17518978913049', NULL, NULL, 0, NULL, NULL),
(90, 'MN000040', 16, 'Registered', '2025-07-07', 1098.00, 'Due', 'Shipped', 'Samtanagar, near telgu chruch, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 14:35:41', 'DHL17518989833666', NULL, 'shipped', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(91, 'MN000041', 16, 'Registered', '2025-07-07', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-07 14:37:12', 'DHL17520627512337', NULL, 'shipped', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(92, 'MN000042', 16, 'Registered', '2025-07-09', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-09 07:25:59', 'DHL17520627546109', NULL, 'shipped', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(93, 'MN000043', 16, 'Registered', '2025-07-09', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-09 07:30:40', '30983010001396', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001396', NULL, NULL, 0, NULL, NULL),
(94, 'MN000044', 16, 'Registered', '2025-07-09', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Miraj, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-09 07:31:44', '30983010001400', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001400', NULL, NULL, 0, NULL, NULL),
(95, 'MN000045', 16, 'Registered', '2025-07-09', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-09 09:12:46', '30983010001411', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001411', NULL, NULL, 0, NULL, NULL),
(96, 'MN000046', 16, 'Registered', '2025-07-09', 549.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-09 09:52:53', '30983010001422', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001422', NULL, NULL, 0, NULL, NULL),
(114, 'ON1752061926930', 16, 'Registered', '2025-07-09', 2.00, 'Paid', 'Shipped', 'Samtangar, Sangli, Maharashtra - 416410', 'Online', NULL, 0.00, 0, 0.00, 'pay_QqxXj2XXA211P2', '2025-07-09 11:52:08', '30983010001470', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001470', NULL, NULL, 0, NULL, NULL),
(115, 'ON1752127967565', 10000, 'Registered', '2025-07-10', 2.00, 'Paid', 'Shipped', 'Samtangar, Sangli, Maharashtra - 416410', 'Online', NULL, 0.00, 0, 0.00, 'pay_QrGGmEXrsMKBUG', '2025-07-10 06:12:49', '30983010001481', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001481', NULL, NULL, 0, NULL, NULL),
(116, 'MN000047', 10000, 'Registered', '2025-07-10', 997.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-10 07:15:26', 'SR17521425518958', NULL, 'shipped', 'shiprocket', 'https://shiprocket.co/tracking/SR17521425518958', NULL, NULL, 0, NULL, NULL),
(117, 'MN000048', 10000, 'Registered', '2025-07-10', 249.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-10 07:56:58', '30983010001492', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001492', NULL, NULL, 0, NULL, NULL),
(118, 'MN000049', 16, 'Registered', '2025-07-10', 2.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-10 10:17:54', 'RS17521426966522', NULL, 'shipped', 'rapidshyp', 'https://rapidshyp.com/track/RS17521426966522', NULL, NULL, 0, NULL, NULL),
(119, 'MN000050', 10001, 'Registered', '2025-07-10', 899.00, 'Due', 'Shipped', 'Samtangar, Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-10 10:40:58', '30983010001514', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001514', NULL, NULL, 0, NULL, NULL),
(120, 'MN000051', 16, 'Registered', '2025-07-10', 549.00, 'Due', 'Shipped', 'Samtangar, Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-10 13:14:51', '30983010001536', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001536', NULL, NULL, 0, NULL, NULL),
(121, 'MN000052', 16, 'Registered', '2025-07-11', 499.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 09:26:37', '30983010001525', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001525', NULL, NULL, 0, NULL, NULL),
(122, 'MN000053', 16, 'Registered', '2025-07-11', 249.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 09:34:16', '30983010001540', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001540', NULL, NULL, 0, NULL, NULL),
(123, 'MN000054', 16, 'Registered', '2025-07-11', 699.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 09:37:39', '30983010001551', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001551', NULL, NULL, 0, NULL, NULL),
(124, 'MN000055', 16, 'Registered', '2025-07-11', 1248.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 09:40:36', '30983010001562', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001562', NULL, NULL, 0, NULL, NULL),
(125, 'MN000056', 16, 'Registered', '2025-07-11', 249.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 09:45:07', '30983010001573', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001573', NULL, NULL, 0, NULL, NULL),
(126, 'MN000057', 16, 'Registered', '2025-07-11', 599.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 09:47:25', '30983010001584', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001584', NULL, NULL, 0, NULL, NULL),
(127, 'MN000058', 16, 'Registered', '2025-07-11', 249.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 10:03:43', '30983010001595', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001595', NULL, NULL, 0, NULL, NULL),
(128, 'MN000059', 16, 'Registered', '2025-07-11', 249.00, 'Due', 'Shipped', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-11 10:05:50', '30983010001606', NULL, 'shipped', 'delhivery', 'https://www.delhivery.com/track/package/30983010001606', NULL, NULL, 0, NULL, NULL),
(129, 'MN000060', 16, 'Registered', '2025-07-17', 999.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-17 13:23:58', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(130, 'MN000061', 16, 'Registered', '2025-07-19', 249.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-19 09:06:12', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(131, 'MN000062', 16, 'Registered', '2025-07-19', 249.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-19 09:43:17', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(132, 'MN000063', 16, 'Registered', '2025-07-19', 249.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-19 09:46:39', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(133, 'MN000064', 16, 'Registered', '2025-07-19', 249.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-19 09:46:48', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(134, 'MN000065', 16, 'Registered', '2025-07-19', 249.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-19 09:47:10', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(135, 'MN000066', 16, 'Registered', '2025-07-19', 249.00, 'Due', 'Confirmed', 'Samtangar, Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-19 09:50:29', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(136, 'MN000067', 16, 'Registered', '2025-07-19', 2647.00, 'Due', 'Confirmed', 'Samtanagar, near telgu chruch, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-19 11:15:55', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(137, 'MN000068', 10003, 'Registered', '2025-07-20', 349.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 05:56:37', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(138, 'MN000069', 10003, 'Registered', '2025-07-20', 699.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 06:36:46', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(139, 'MN000070', 10003, 'Registered', '2025-07-20', 249.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 06:43:23', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(140, 'MN000071', 10003, 'Registered', '2025-07-20', 549.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:14:09', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(141, 'MN000072', 10003, 'Registered', '2025-07-20', 249.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:28:07', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(142, 'MN000073', 10003, 'Registered', '2025-07-20', 249.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:28:10', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(143, 'MN000074', 1, 'Registered', '2025-07-20', 100.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:29:28', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(144, 'MN000075', 1, 'Registered', '2025-07-20', 100.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:31:35', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(145, 'MN000076', 1, 'Registered', '2025-07-20', 299.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:34:02', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(146, 'MN000077', 10003, 'Registered', '2025-07-20', 249.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:34:35', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(147, 'MN000078', 1, 'Registered', '2025-07-20', 299.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:34:50', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(148, 'MN000079', 1, 'Registered', '2025-07-20', 299.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:37:50', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(149, 'MN000080', 1, 'Registered', '2025-07-20', 299.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:39:40', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(150, 'MN000081', 1, 'Registered', '2025-07-20', 599.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:41:02', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(151, 'MN000082', 1, 'Registered', '2025-07-20', 450.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:45:43', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(152, 'MN000083', 1, 'Registered', '2025-07-20', 599.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:47:45', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(153, 'MN000084', 1, 'Registered', '2025-07-20', 299.00, 'Due', 'Confirmed', '123 Test Street, Near Mall, Mumbai, Maharashtra - 400001', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:52:16', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(154, 'MN000085', 10003, 'Registered', '2025-07-20', 349.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 07:53:38', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(155, 'MN000086', 10003, 'Registered', '2025-07-20', 899.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 08:21:36', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(156, 'MN000087', 10003, 'Registered', '2025-07-20', 999.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 08:24:42', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(157, 'MN000088', 10003, 'Registered', '2025-07-20', 2496.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 08:25:22', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(158, 'MN000089', 10003, 'Registered', '2025-07-20', 1148.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 08:25:48', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(159, 'MN000090', 10003, 'Registered', '2025-07-20', 499.00, 'Due', 'Confirmed', 'Samtangar, , Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-20 09:14:31', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(160, 'MN000091', 16, 'Registered', '2025-07-21', 249.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-21 10:54:26', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(161, 'MN000092', 16, 'Registered', '2025-07-23', 599.00, 'Due', 'Confirmed', 'Aasdasd, asdasdasd, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-23 06:13:00', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL),
(162, 'MN000093', 16, 'Registered', '2025-07-23', 848.00, 'Due', 'Confirmed', 'Samtangar, Aksa Masjid Road, Sangli, Maharashtra - 416410', 'COD', NULL, 0.00, 0, 0.00, 'NA', '2025-07-23 07:05:56', NULL, NULL, 'pending', 'delhivery', NULL, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `our_services`
--

CREATE TABLE `our_services` (
  `Id` int NOT NULL,
  `Heading` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Service heading',
  `Description` text COLLATE utf8mb4_general_ci COMMENT 'Service description',
  `PhotoPath` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Service image path',
  `IsActive` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT 'Y' COMMENT 'Active status',
  `DisplayOrder` int DEFAULT '1' COMMENT 'Display order',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `our_services`
--

INSERT INTO `our_services` (`Id`, `Heading`, `Description`, `PhotoPath`, `IsActive`, `DisplayOrder`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Quality Products', 'Premium Ayurvedic and herbal products for your health', 'service1.jpg', 'Y', 1, '2025-07-23 07:47:27', '2025-07-23 07:47:27'),
(2, 'Expert Consultation', 'Get advice from certified Ayurvedic experts', 'service2.jpg', 'Y', 2, '2025-07-23 07:47:27', '2025-07-23 07:47:27'),
(3, 'Fast Delivery', 'Quick and secure delivery to your doorstep', 'service3.jpg', 'Y', 3, '2025-07-23 07:47:27', '2025-07-23 07:47:27'),
(4, 'Customer Support', '24/7 customer support for all your queries', 'service4.jpg', 'Y', 4, '2025-07-23 07:47:27', '2025-07-23 07:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `page_views`
--

CREATE TABLE `page_views` (
  `id` int NOT NULL,
  `visitor_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int DEFAULT NULL,
  `page_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_type` enum('home','product','category','cart','checkout','other') COLLATE utf8mb4_unicode_ci DEFAULT 'other',
  `product_id` int DEFAULT NULL COMMENT 'If viewing a product page',
  `category_id` int DEFAULT NULL COMMENT 'If viewing a category page',
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_on_page` int DEFAULT NULL COMMENT 'Time spent on page in seconds',
  `scroll_depth` decimal(5,2) DEFAULT NULL COMMENT 'Percentage of page scrolled',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `referrer_url` text COLLATE utf8mb4_unicode_ci,
  `viewed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track individual page views and user interactions';

--
-- Dumping data for table `page_views`
--

INSERT INTO `page_views` (`id`, `visitor_id`, `customer_id`, `page_url`, `page_title`, `page_type`, `product_id`, `category_id`, `session_id`, `time_on_page`, `scroll_depth`, `ip_address`, `user_agent`, `referrer_url`, `viewed_at`) VALUES
(1, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/checkout.php', '2025-07-21 10:41:54'),
(2, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/checkout.php', '2025-07-21 10:41:56'),
(3, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/checkout.php', '2025-07-21 10:41:57'),
(4, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/product_details.php?ProductId=6', 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'product', 6, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 10:42:51'),
(5, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/product_details.php?ProductId=6', 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'product', 6, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 10:47:15'),
(6, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/product_details.php?ProductId=6', 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'product', 6, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 10:47:29'),
(7, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/product_details.php?ProductId=37', 'My Nutrify Herbal & Ayurveda\'s she care plus Juice | amla Juice combo', 'product', 37, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/product_details.php?ProductId=6', '2025-07-21 10:48:11'),
(8, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/rewards.php', '2025-07-21 10:52:21'),
(9, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/product_details.php?ProductId=6', 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'product', 6, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 10:54:15'),
(10, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 11:01:33'),
(11, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 11:03:20'),
(12, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/product_details.php?ProductId=6', 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'product', 6, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 11:14:49'),
(13, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 11:40:07'),
(14, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 11:50:22'),
(15, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php?SubCategoryId=1', '2025-07-21 13:23:36'),
(16, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'e9c8598kb62dn7k3cjkhbi255j', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php?logout=success', '2025-07-21 13:25:53'),
(17, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'e9c8598kb62dn7k3cjkhbi255j', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php?logout=success', '2025-07-21 13:25:54'),
(18, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'e9c8598kb62dn7k3cjkhbi255j', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php?logout=success', '2025-07-21 13:26:00'),
(19, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'e9c8598kb62dn7k3cjkhbi255j', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-21 13:26:08'),
(20, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'a5tfkjtpnilnl3bkv1tlq78n6m', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-21 13:26:29'),
(21, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'a5tfkjtpnilnl3bkv1tlq78n6m', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-21 13:28:26'),
(22, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'a5tfkjtpnilnl3bkv1tlq78n6m', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 13:28:55'),
(23, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'a5tfkjtpnilnl3bkv1tlq78n6m', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/cart.php', '2025-07-21 13:30:07'),
(24, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'a5tfkjtpnilnl3bkv1tlq78n6m', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/blogs.php', '2025-07-21 13:30:11'),
(25, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'a5tfkjtpnilnl3bkv1tlq78n6m', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/customer-care.php', '2025-07-21 13:31:50'),
(26, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 13:32:55'),
(27, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-21 13:33:07'),
(28, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php', '2025-07-21 13:33:28'),
(29, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/', '', 'other', NULL, NULL, '50heer1v7caaet9g2hbiikbtl7', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-21 13:42:56'),
(30, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '29ss4nr44rkq0or1nkt65p84cb', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 05:45:18'),
(31, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '29ss4nr44rkq0or1nkt65p84cb', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 05:46:43'),
(32, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '29ss4nr44rkq0or1nkt65p84cb', 178, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 05:48:50'),
(33, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/', '', 'other', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 213, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 05:51:55'),
(34, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '4h628bdrhsn82o1v4m9i3qb8np', 343, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 05:55:16'),
(35, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '4h628bdrhsn82o1v4m9i3qb8np', 32, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/order-placed.php?order_id=ON1752061926930', '2025-07-23 06:06:23'),
(36, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '4h628bdrhsn82o1v4m9i3qb8np', 71, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 06:07:07'),
(37, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/', '', 'other', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 1172, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 06:07:53'),
(38, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '4h628bdrhsn82o1v4m9i3qb8np', 23, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/order-placed.php?order_id=ON1752061926930', '2025-07-23 06:08:28'),
(39, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '4h628bdrhsn82o1v4m9i3qb8np', 20, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/order-placed.php?order_id=ON1752061926930', '2025-07-23 06:09:44'),
(40, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '4h628bdrhsn82o1v4m9i3qb8np', 0, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 06:12:07'),
(41, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '4h628bdrhsn82o1v4m9i3qb8np', 3, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php', '2025-07-23 06:12:34'),
(42, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php', '2025-07-23 06:30:45'),
(43, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 258, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php', '2025-07-23 06:35:04'),
(44, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 4, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php', '2025-07-23 06:37:43'),
(45, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '22tplbp8o1ijvechcv8ppu9den', 2, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php', '2025-07-23 06:38:08'),
(46, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'b6un0e0bd3a84j2b6qhftqbltr', 58, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 06:40:02'),
(47, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'bo91oepq9nlt87jn22edfcf5aq', 3, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php?logout=success', '2025-07-23 06:41:14'),
(48, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'bo91oepq9nlt87jn22edfcf5aq', 1902, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php?logout=success', '2025-07-23 06:43:58'),
(49, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, '/nutrify/', '', 'other', NULL, NULL, 'bo91oepq9nlt87jn22edfcf5aq', 5, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 07:22:02'),
(50, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '2p5c0r82dj0l7hvrpefqr5j43v', 0, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php?logout=success', '2025-07-23 07:22:15'),
(51, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '2p5c0r82dj0l7hvrpefqr5j43v', 1, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php?logout=success', '2025-07-23 07:22:16'),
(52, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', NULL, '/nutrify/', '', 'other', NULL, NULL, '2p5c0r82dj0l7hvrpefqr5j43v', 13, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 07:22:26'),
(53, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '2p5c0r82dj0l7hvrpefqr5j43v', 1, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/login.php', '2025-07-23 07:22:49'),
(54, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '2p5c0r82dj0l7hvrpefqr5j43v', 1000, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/order-details.php?id=MN000092', '2025-07-23 07:47:27'),
(55, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/account.php', '2025-07-23 07:52:07'),
(56, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 50, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/account.php', '2025-07-23 07:53:51'),
(57, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 3, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/account.php', '2025-07-23 07:55:22'),
(58, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/track_order.php', '2025-07-23 07:55:27'),
(59, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 5, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/track_order.php', '2025-07-23 07:58:01'),
(60, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'ggg0579jifrqi3f98bf7c4b2ls', 56, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/add_full_width_css.php', '2025-07-23 07:59:07'),
(61, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 11, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/combos.php', '2025-07-23 08:00:36'),
(62, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php?open_chat=1', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/customer-care.php', '2025-07-23 08:01:21'),
(63, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/index.php?open_chat=1', '2025-07-23 08:01:36'),
(64, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/index.php', '2025-07-23 08:01:40'),
(65, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 1346, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/index.php', '2025-07-23 08:04:15'),
(66, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/product_details.php?ProductId=23', '2025-07-23 09:35:33'),
(67, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 4173, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/product_details.php?ProductId=23', '2025-07-23 09:36:58'),
(68, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 311, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/product_details.php?ProductId=9', '2025-07-23 11:09:24'),
(69, 'a793929bdf7d55478ef979a20af459f47656ec2e59188101c49ba0d389499fad', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '8floo8gn33cnq4reo7nr4hdb7v', 773, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/combos.php', '2025-07-23 11:20:43'),
(70, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 200, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 11:26:37'),
(71, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 1, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 11:29:58'),
(72, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, 'gtqmo9bdh6dg7htjtm1q2kt6jo', 1, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 11:41:48'),
(73, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 12:05:44'),
(74, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5ba5hiha1q3ul297jm7216pb38', 149, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 12:08:14'),
(75, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5ba5hiha1q3ul297jm7216pb38', 10, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 12:08:24'),
(76, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 113, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 12:22:32'),
(77, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 12:24:28'),
(78, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5ba5hiha1q3ul297jm7216pb38', 1014, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 12:25:22'),
(79, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 54, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 12:25:28'),
(80, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 50, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 12:26:23'),
(81, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 305, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 12:31:31'),
(82, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 115, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 12:33:28'),
(83, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 550, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 12:54:47'),
(84, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 13:03:58'),
(85, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 13:04:43'),
(86, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 304, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-23 13:09:03'),
(87, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 329, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 13:10:14'),
(88, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 28, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 13:10:44'),
(89, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 217, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 13:14:24'),
(90, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5krnvapl5mh8rg5oj15quoud84', 993, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 13:23:03'),
(91, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, '/nutrify/index.php', 'Home Page', 'home', NULL, NULL, '5ba5hiha1q3ul297jm7216pb38', 2, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'http://localhost/nutrify/products.php', '2025-07-23 13:43:49'),
(92, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, '/nutrify/', '', 'other', NULL, NULL, 'seihve75fm1almac0ncdfps535', 41, 0.00, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-24 06:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `points_config`
--

CREATE TABLE `points_config` (
  `id` int NOT NULL,
  `config_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `points_config`
--

INSERT INTO `points_config` (`id`, `config_key`, `config_value`, `description`, `updated_at`) VALUES
(1, 'points_per_rupee', '3', 'Points earned per ₹100 spent', '2025-07-20 05:48:05'),
(2, 'signup_bonus_points', '25', 'Points awarded on customer signup', '2025-07-20 05:48:05'),
(3, 'review_points', '25', 'Points awarded for writing a product review', '2025-07-20 05:48:05'),
(4, 'referral_points_referrer', '100', 'Points awarded to referrer when referred customer makes first purchase', '2025-07-20 05:48:05'),
(5, 'referral_points_referred', '50', 'Points awarded to referred customer on signup', '2025-07-20 05:48:05'),
(6, 'points_expiry_months', '12', 'Number of months after which points expire', '2025-07-20 05:48:05'),
(7, 'silver_tier_threshold', '500', 'Points required for Silver tier', '2025-07-20 05:48:05'),
(8, 'gold_tier_threshold', '1500', 'Points required for Gold tier', '2025-07-20 05:48:05'),
(9, 'platinum_tier_threshold', '5000', 'Points required for Platinum tier', '2025-07-20 05:48:05'),
(10, 'signup_bonus', '25', 'Points awarded on signup', '2025-07-20 07:03:15'),
(11, 'review_bonus', '25', 'Points awarded for product review', '2025-07-20 07:03:15'),
(12, 'referral_bonus_referrer', '100', 'Points for successful referral (referrer)', '2025-07-20 07:03:15'),
(13, 'referral_bonus_referred', '50', 'Points for being referred', '2025-07-20 07:03:15'),
(14, 'bronze_threshold', '0', 'Points needed for Bronze tier', '2025-07-20 07:03:15'),
(15, 'silver_threshold', '500', 'Points needed for Silver tier', '2025-07-20 07:03:15'),
(16, 'gold_threshold', '1500', 'Points needed for Gold tier', '2025-07-20 07:03:15'),
(17, 'platinum_threshold', '3000', 'Points needed for Platinum tier', '2025-07-20 07:03:15');

-- --------------------------------------------------------

--
-- Table structure for table `points_transactions`
--

CREATE TABLE `points_transactions` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `transaction_type` enum('earned','redeemed','expired','bonus') NOT NULL,
  `points` int NOT NULL,
  `description` text,
  `order_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `points_transactions`
--

INSERT INTO `points_transactions` (`id`, `customer_id`, `transaction_type`, `points`, `description`, `order_id`, `created_at`) VALUES
(1, 1, 'earned', 16, 'Points for ₹549 order', 'MN000071', '2025-07-20 07:52:05'),
(2, 1, 'earned', 10, 'Manual SQL test', 'MANUAL_SQL_TEST_1752997932', '2025-07-20 07:52:12'),
(3, 1, 'earned', 9, 'Points earned for order #STEP_TEST_1752997932 (₹300)', 'STEP_TEST_1752997932', '2025-07-20 07:52:12'),
(4, 1, 'earned', 8, 'Points earned for order #MN000084 (₹299)', 'MN000084', '2025-07-20 07:52:16'),
(5, 10003, 'earned', 10, 'Points earned for order #MN000085 (₹349)', 'MN000085', '2025-07-20 07:53:38'),
(6, 10003, 'earned', 26, 'Points earned for order #MN000086 (₹899)', 'MN000086', '2025-07-20 08:21:36'),
(7, 10003, 'earned', 29, 'Points earned for order #MN000087 (₹999)', 'MN000087', '2025-07-20 08:24:43'),
(8, 10003, 'earned', 74, 'Points earned for order #MN000088 (₹2496)', 'MN000088', '2025-07-20 08:25:22'),
(9, 10003, 'earned', 34, 'Points earned for order #MN000089 (₹1148)', 'MN000089', '2025-07-20 08:25:48'),
(15, 10003, 'redeemed', -150, 'Redeemed: Free Shipping', 'DISC1000317530006354419', '2025-07-20 08:37:15'),
(16, 10003, 'earned', 14, 'Points earned for order #ON1753002861310 (₹499)', 'ON1753002861310', '2025-07-20 09:14:22'),
(17, 10003, 'earned', 14, 'Points earned for order #MN000090 (₹499)', 'MN000090', '2025-07-20 09:14:31'),
(18, 16, 'earned', 7, 'Points earned for order #MN000091 (₹249)', 'MN000091', '2025-07-21 10:54:27'),
(19, 16, 'earned', 17, 'Points earned for order #MN000092 (₹599)', 'MN000092', '2025-07-23 06:13:01'),
(20, 16, 'earned', 25, 'Points earned for order #MN000093 (₹848)', 'MN000093', '2025-07-23 07:05:56');

-- --------------------------------------------------------

--
-- Stand-in structure for view `popular_products`
-- (See below for the actual view)
--
CREATE TABLE `popular_products` (
`overall_conversion_rate` decimal(5,2)
,`PhotoPath` text
,`product_id` int
,`ProductName` text
,`purchase_rank` bigint unsigned
,`total_cart_additions` int
,`total_purchases` int
,`total_revenue` decimal(12,2)
,`total_views` int
,`unique_views` int
,`view_rank` bigint unsigned
);

-- --------------------------------------------------------

--
-- Table structure for table `product_analytics`
--

CREATE TABLE `product_analytics` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `total_views` int DEFAULT '0',
  `unique_views` int DEFAULT '0',
  `total_cart_additions` int DEFAULT '0',
  `unique_cart_additions` int DEFAULT '0',
  `total_purchases` int DEFAULT '0',
  `total_purchase_quantity` int DEFAULT '0',
  `total_revenue` decimal(12,2) DEFAULT '0.00',
  `view_to_cart_rate` decimal(5,2) DEFAULT '0.00',
  `cart_to_purchase_rate` decimal(5,2) DEFAULT '0.00',
  `overall_conversion_rate` decimal(5,2) DEFAULT '0.00',
  `average_time_on_product_page` int DEFAULT '0' COMMENT 'Average time in seconds',
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aggregate analytics data for each product';

--
-- Dumping data for table `product_analytics`
--

INSERT INTO `product_analytics` (`id`, `product_id`, `total_views`, `unique_views`, `total_cart_additions`, `unique_cart_additions`, `total_purchases`, `total_purchase_quantity`, `total_revenue`, `view_to_cart_rate`, `cart_to_purchase_rate`, `overall_conversion_rate`, `average_time_on_product_page`, `last_updated`) VALUES
(1, 6, 5, 2, 7, 5, 0, 0, 0.00, 140.00, 0.00, 0.00, 0, '2025-07-23 06:46:14'),
(2, 9, 0, 0, 1, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-23 06:46:21'),
(3, 10, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(4, 11, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(5, 14, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(6, 15, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(7, 18, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(8, 19, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(9, 21, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(10, 22, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(11, 23, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(12, 25, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(13, 34, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(14, 35, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(15, 36, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(16, 37, 1, 1, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:48:11'),
(17, 38, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(18, 39, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(19, 40, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(20, 41, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(21, 42, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(22, 43, 0, 0, 2, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-23 06:30:18'),
(23, 44, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(24, 45, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(25, 46, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(26, 47, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11'),
(27, 49, 0, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0, '2025-07-21 10:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `product_benefits`
--

CREATE TABLE `product_benefits` (
  `Product_BenefitId` int NOT NULL,
  `ProductId` int NOT NULL,
  `Title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ShortDescription` text COLLATE utf8mb4_general_ci NOT NULL,
  `PhotoPath` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_benefits`
--

INSERT INTO `product_benefits` (`Product_BenefitId`, `ProductId`, `Title`, `ShortDescription`, `PhotoPath`) VALUES
(1, 12, 'Maintains Bone Strength & Stamina', 'Supports bone health and enhances overall stamina with natural herbal ingredients.', 'Hormone.jpg'),
(2, 12, 'Menstrual Health Support', 'Aids in regulating cycles, easing cramps, and reducing menstrual discomfort.', 'Metabolism.jpg'),
(3, 12, 'Natural Stress Reliever', 'Helps the body adapt to stress, reducing fatigue and improving resilience.', 'skin_hair.jpg'),
(4, 12, 'Boosts Immunity', 'Strengthens the immune system while promoting overall well-being and vitality.', 'Stress.jpg'),
(5, 25, 'Supports Thyroid Function', '', '2958_1742965008.jpg'),
(6, 25, 'Boosts Metabolism & Energy Levels', '', '9592_1742965050.jpg'),
(7, 25, 'Enhances Cognitive Function', '', '8669_1742965083.jpg'),
(8, 25, 'Balances Mood & Reduces Stress', '', '7468_1742965106.jpg'),
(9, 25, 'Promotes Hormonal Balance', '', '5644_1742965129.jpg'),
(10, 9, 'Boosts Heart Health', '', '8937_1742965932.jpeg'),
(11, 9, 'Aids in Fat Metabolism', '', '9401_1742965957.webp'),
(12, 9, 'Rich in Antioxidants', '', '1118_1742965975.jpg'),
(13, 9, 'Helps detoxifies the body', '', '4389_1742965996.jpg'),
(14, 10, 'Manages Blood Sugar Levels', '', '5218_1742967499.jpg'),
(15, 10, 'Boosts Metabolism & Improves Digestion', '', '6163_1742967542.jpg'),
(16, 10, 'Stimulates Insulin Secretion', '', '3113_1742967587.webp'),
(17, 10, 'Helps Manage Weight', '', '3395_1742967664.jpg'),
(18, 11, 'Improves Digestion', '', '9214_1742968640.jpg'),
(19, 11, 'Regulates Blood Sugar', '', '4584_1742968717.jpg'),
(20, 11, 'Regulates Blood Pressure', '', '6334_1742968737.jpg'),
(21, 11, 'Rich in Antioxidants', '', '7278_1742968765.jpg'),
(22, 6, 'Prevents Hair Fall & Boosts Hair Growth', '', '7162_1742969292.jpg'),
(23, 6, 'Improves Skin Health', '', '3416_1742969320.jpg'),
(24, 6, 'Aids Digestion & Better Gut Health', '', '4466_1742969340.jpg'),
(25, 6, 'Enhances Immunity', '', '8855_1742969358.jpg'),
(26, 14, 'Rich in Chlorophyll', '', '6313_1742970820.jpg'),
(27, 14, 'Regulates Cholesterol Levels', '', '6222_1742970835.jpg'),
(29, 14, 'Packed with Nutrients & Antioxidants', '', '9700_1742970895.jpg'),
(30, 14, 'Increases Hemoglobin Naturally', '', '4949_1742971189.jpg'),
(31, 22, 'Helps Regulate Blood Pressure Naturally', '', '7920_1742971298.jpg'),
(32, 22, 'Supports Healthy Blood Circulation', '', '6042_1742971331.jpg'),
(33, 22, 'Reduces Stress & Anxiety for Better Heart Health', '', '5722_1742971359.jpeg'),
(34, 22, 'Promotes Relaxation & Mental Well-being', '', '7096_1742971404.jpg'),
(35, 23, 'Supports Hormonal Balance', '', '4168_1742972351.jpg'),
(36, 23, 'Enhances Reproductive Health', '', '3708_1742972407.jpg'),
(37, 23, 'Boosts Energy and Vitality', '', '8117_1742972502.jpg'),
(38, 23, 'Supports Skin and Hair Health', '', '9586_1742972536.jpg'),
(39, 15, 'Rich in antioxidants', '', '9042_1742982535.jpg'),
(40, 15, 'Supports weight management', '', '1612_1742982561.jpg'),
(41, 15, 'Boosts metabolism & aids digestion', '', '7318_1742982597.jpg'),
(42, 15, 'Enhances hemoglobin levels', '', '5572_1742982648.jpg'),
(47, 18, 'Boosts stamina and vitality', '', '4024_1743233219.jpg'),
(48, 18, 'Reduces fatigue naturally', '', '2476_1743233237.jpg'),
(49, 18, 'Strengthens bones and muscles', '', '8491_1743233261.jpg'),
(50, 18, 'Pure and herbal energy enhancer', '', '1447_1743233283.jpg'),
(51, 19, 'Boosts stamina and vitality', '', '3689_1743234340.jpg'),
(52, 19, 'Reduces fatigue naturally', '', '1515_1743234357.jpg'),
(53, 19, 'Strengthens bones and muscles', '', '7907_1743234383.jpg'),
(54, 19, 'Pure and herbal energy enhancer', '', '8447_1743234403.jpg'),
(55, 21, 'Boosts stamina and vitality', '', '9479_1743234443.jpg'),
(56, 21, 'Reduces fatigue naturally', '', '3896_1743234459.jpg'),
(57, 21, 'Strengthens bones and muscles', '', '3909_1743234481.jpg'),
(58, 21, 'Pure and herbal energy enhancer', '', '6104_1743234512.jpg'),
(59, 34, 'Manages Blood Sugar Levels', '', '5218_1742967499.jpg'),
(60, 34, 'Boosts Heart Health', '', '8937_1742965932.jpeg'),
(61, 34, 'Helps Manage Weight', '', '3395_1742967664.jpg'),
(62, 34, 'Aids in Fat Metabolism', '', '9401_1742965957.webp'),
(63, 35, 'Manages Blood Sugar Levels', '', '5218_1742967499.jpg'),
(64, 35, 'Boosts Metabolism & Improves Digestion', '', '6163_1742967542.jpg'),
(65, 35, 'Helps Manage Weight', '', '3395_1742967664.jpg'),
(66, 35, 'Helps Regulate Blood Pressure Naturally', '', '7920_1742971298.jpg'),
(67, 36, 'Boosts Heart Health', '', '8937_1742965932.jpeg'),
(68, 36, 'Rich in Antioxidants', '', '1118_1742965975.jpg'),
(69, 36, 'Helps detoxifies the body', '', '4389_1742965996.jpg'),
(70, 36, 'Supports Healthy Blood Circulation', '', '6042_1742961331.jpg'),
(71, 37, 'Prevents Hair Fall & Boosts Hair Growth', '', '7162_1742969292.jpg'),
(72, 37, 'Improves Skin Health', '', '3416_1742969320.jpg'),
(73, 37, 'Enhances Immunity', '', '8855_1742969358.jpg'),
(74, 37, 'Aids Digestion & Better Gut Health', '', '4466_1742969340.jpg'),
(75, 38, 'Rich in Chlorophyll', '', '6313_1742970820.jpg'),
(76, 38, 'Increases Hemoglobin Naturally', '', '4949_1742971189.jpg'),
(77, 38, 'Packed with Nutrients & Antioxidants', '', '9700_1742970895.jpg'),
(78, 38, 'Boosts metabolism & aids digestion', '', '7318_1742982597.jpg'),
(79, 39, 'Manages Blood Sugar Levels', '', '5218_1742967499.jpg'),
(80, 39, 'Boosts Metabolism & Improves Digestion', '', '6163_1742967542.jpg'),
(81, 39, 'Stimulates Insulin Secretion', '', '3113_1742967587.webp'),
(82, 39, 'Helps Manage Weight', '', '3395_1742967664.jpg'),
(83, 40, 'Helps Regulate Blood Pressure Naturally', '', '7920_1742971298.jpg'),
(84, 40, 'Supports Healthy Blood Circulation', '', '6042_1742971331.jpg'),
(85, 40, 'Reduces Stress & Anxiety for Better Heart Health', '', '5722_1742971359.jpeg'),
(86, 40, 'Rich in Antioxidants', '', '7278_1742968765.jpg'),
(91, 41, 'Prevents Hair Fall & Boosts Hair Growth', '', '7162_1742969292.jpg'),
(92, 41, 'Enhances Immunity', '', '8855_1742969358.jpg'),
(93, 41, 'Boosts Metabolism & Aids Digestion', '', '7318_1742982597.jpg'),
(94, 41, 'Supports Weight Management', '', '1612_1742982561.jpg'),
(95, 42, 'Rich in Chlorophyll', '', '6313_1742970820.jpg'),
(96, 42, 'Enhances Immunity', '', '8855_1742969358.jpg'),
(97, 42, 'Regulates Cholesterol Levels', '', '6222_1742970835.jpg'),
(98, 42, 'Packed with Nutrients & Antioxidants', '', '9700_1742970895.jpg'),
(99, 43, 'Supports Thyroid Function', '', '2958_1742965008.jpg'),
(100, 43, 'Boosts Metabolism & Energy Levels', '', '9592_1742965050.jpg'),
(101, 43, 'Balances Mood & Reduces Stress', '', '7468_1742965106.jpg'),
(102, 43, 'Enhances Cognitive Function', '', '8669_1742965083.jpg'),
(103, 44, 'Supports Thyroid Function', '', '2958_1742965008.jpg'),
(104, 44, 'Boosts Metabolism & Energy Levels', '', '9592_1742965050.jpg'),
(105, 44, 'Enhances Cognitive Function', '', '8669_1742965083.jpg'),
(106, 44, 'Balances Mood & Reduces Stress', '', '7468_1742965106.jpg'),
(107, 45, 'Supports Thyroid Function', '', '2958_1742965008.jpg'),
(108, 45, 'Boosts Metabolism & Energy Levels', '', '9592_1742965050.jpg'),
(109, 45, 'Enhances Cognitive Function', '', '8669_1742965083.jpg'),
(110, 45, 'Balances Mood & Reduces Stress', '', '7468_1742965106.jpg'),
(111, 46, 'Boosts Heart Health', '', '8937_1742965932.jpeg'),
(112, 46, 'Aids in Fat Metabolism', '', '9401_1742965957.webp'),
(113, 46, 'Improves Digestion', '', '9214_1742968640.jpg'),
(114, 46, 'Regulates Blood Sugar', '', '4584_1742968717.jpg'),
(115, 47, 'Supports Hormonal Balance', '', '4168_1742972351.jpg'),
(116, 47, 'Enhances Reproductive Health', '', '3708_1742972407.jpg'),
(117, 47, 'Boosts Energy and Vitality', '', '8117_1742972502.jpg'),
(118, 47, 'Supports Skin and Hair Health', '', '9586_1742972536.jpg'),
(119, 49, 'Rich in antioxidants', '', '9042_1742982535.jpg'),
(120, 49, 'Supports weight management', '', '1612_1742982561.jpg'),
(121, 49, 'Boosts metabolism & aids digestion', '', '7318_1742982597.jpg'),
(122, 49, 'Enhances hemoglobin levels', '', '5572_1742982648.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE `product_details` (
  `Product_DetailsId` int NOT NULL,
  `ProductId` int NOT NULL,
  `PhotoPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Title` varchar(255) NOT NULL,
  `ImagePath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_details`
--

INSERT INTO `product_details` (`Product_DetailsId`, `ProductId`, `PhotoPath`, `Description`, `Title`, `ImagePath`) VALUES
(1, 6, '36578.jpg', 'Made from fresh wild Indian gooseberries, this juice boosts immunity, improves digestion, and promotes healthy skin and hair.\r\n\r\n', '', '79333.jpg'),
(2, 9, '89546.jpg', 'A powerful blend of garlic, ginger, lemon, apple cider vinegar, and honey to support heart health, manage cholesterol, and enhance circulation.\r\n', '', '84127.jpg'),
(3, 10, '89545.jpg', 'A natural blend of 13 Ayurvedic herbs to support healthy blood sugar and overall wellness.\r\n\r\n', '', '33948.jpg'),
(4, 11, '71764.jpg', 'A 100% pure blend of Karela, Neem, and Jamun that helps regulate blood sugar, purify the blood, enhance digestion, and support pancreas and liver health.\r\n', '', '41854.jpg'),
(6, 14, '39743.jpg', '100% pure wheatgrass juice that detoxifies, boosts metabolism, improves digestion, and supports healthy weight management.\r\n\r\n', '', '22122.jpg'),
(7, 15, '83019.jpg', 'Unfiltered vinegar made from premium apples to boost digestion, metabolism, and support healthy weight management.\r\n', '', '28149.jpg'),
(8, 22, '61728.jpg', 'A powerful Ayurvedic formula with 10  herbs to help regulate blood pressure and boost immunity.\r\n', '', '20422.jpg'),
(9, 23, '50166.jpg', 'A blend of Ayurvedic herbs to balance hormones, boost energy, improve digestion, and enhance women’s overall health.\r\n', '', '61590.jpg'),
(11, 25, '22069.jpg', 'An Ayurvedic blend rich in vitamins and fiber that calms stress, reduces hypersensitivity, and supports natural detox and balance\r\n', '', '68912.jpg'),
(13, 10, '', 'My Nutrify Herbal & Ayurveda\'s Diabetic Care Juice is an\r\ninnovative, all-natural, and high-quality herbal product\r\ncarefully formulated to assist in the best blood sugar\r\nequilibrium and overall metabolic health, blending the\r\n\r\nancient knowledge of Ayurveda with contemporary\r\nnutritional science to offer a higher quality dietary\r\nsupplement perfectly suited for diabetic as well as\r\npre-diabetic management while also acting as an\r\nexcellent preventive agent. My Nutrify Herbal &\r\nAyurveda\'s Diabetic Care Juice is a Natural Blend of 13\r\nPowerful herbs.\r\nThis special combination taps the powerful potential of\r\nage-old herbs like bitter gourd (Karela), Indian\r\nblackberry (Jamun), Gudmar, Amla (Indian\r\nGooseberry), Neem, and fenugreek (Methi) that are\r\nrenowned for their natural capacity to balance blood\r\nsugar levels, increase insulin sensitivity, and improve\r\npancreatic functioning and thus create a revolutionary\r\nsystem that naturally stimulates glycemic control and\r\nreduces the typical complications of diabetes while at\r\nthe same time providing other health benefits such as\r\nantioxidant support, improved cardiovascular\r\nwell-being, and better digestion—all contributing to a\r\ncomplete diabetes management and wellness scheme.\r\nThis Diabetic Care Juice is made under rigorous quality\r\ncontrol measures from only the best, well-sourced\r\ningredients to provide each sip with a concentrated\r\ndosage of nature\'s most potent healing plant extracts\r\n\r\nthat act synergistically to fight against oxidative stress,\r\ndampen inflammation, and support a harmonious\r\nmetabolism without any dependency on synthetic\r\nadditives or chemicals, making it a perfect product for\r\nhealth-conscious consumers who want a safe, effective,\r\nand high-quality herbal supplement aligned with\r\nAyurvedic principles and contemporary scientific\r\nevidence.\r\nFormulated to address the needs of today\'s busy\r\nprofessionals, gym-goers, and entrepreneurs who care\r\nabout their health and well-being, our product is\r\nconveniently packaged for daily consumption and can\r\nbe easily integrated into a healthy diet and lifestyle\r\nroutine. Whether in the morning to rev up your\r\nmetabolism or as part of a general daily regimen to\r\nprovide stable energy and avoid the unpredictable\r\nspikes and dives in blood sugar that can contribute to\r\nlong-term health problems; in addition, its natural\r\ndetoxifying abilities help purify the body of built-up\r\ntoxins while supporting liver health, immune system\r\nfunction, and cardiovascular health, making it a vital\r\naddition to any health and wellness regimen.\r\nThe technology behind our product is based on the\r\nancient Ayurvedic principle of balancing the doshas,\r\n\r\nspecifically by using bitter and astringent tastes that\r\nassist in controlling the digestive fire (Agni) of the body\r\nand ensuring effective assimilation of nutrients, and the\r\naddition of strong herbs such as Jamun and Gudmar,\r\nwhich have been clinically reported to possess\r\nanti-diabetic activity, to make the product not only\r\neffective in controlling hyperglycemia but also help\r\nprevent the development of diabetic complications by\r\nmaintaining endothelial function and inhibiting the\r\nglycation of proteins.\r\nApart from its main role as a diabetic care supplement,\r\nthis juice has been carefully crafted to be a holistic\r\nhealth tonic that caters to various aspects of well-being,\r\nranging from aiding weight management and\r\nincreasing energy levels to facilitating mental clarity\r\nand stress resistance, thus making it an ideal\r\ncompanion for those who have busy lifestyles and\r\nrequire a natural pick-me-up to keep their performance\r\ngoing in both personal and professional endeavors.', '', ''),
(14, 10, '', 'Database Error: Field \'Title\' doesn\'t have a default value | File: C:\\laragon\\www\\nutrify\\cms\\database\\dbconnection.php | Line: 701', '', ''),
(15, 6, '', 'My Nutrify Herbal & Ayurveda\'s Diabetic Care Juice is an\r\ninnovative, all-natural, and high-quality herbal product\r\ncarefully formulated to assist in the best blood sugar\r\nequilibrium and overall metabolic health, blending the\r\n\r\nancient knowledge of Ayurveda with contemporary\r\nnutritional science to offer a higher quality dietary\r\nsupplement perfectly suited for diabetic as well as\r\npre-diabetic management while also acting as an\r\nexcellent preventive agent. My Nutrify Herbal &\r\nAyurveda\'s Diabetic Care Juice is a Natural Blend of 13\r\nPowerful herbs.\r\nThis special combination taps the powerful potential of\r\nage-old herbs like bitter gourd (Karela), Indian\r\nblackberry (Jamun), Gudmar, Amla (Indian\r\nGooseberry), Neem, and fenugreek (Methi) that are\r\nrenowned for their natural capacity to balance blood\r\nsugar levels, increase insulin sensitivity, and improve\r\npancreatic functioning and thus create a revolutionary\r\nsystem that naturally stimulates glycemic control and\r\nreduces the typical complications of diabetes while at\r\nthe same time providing other health benefits such as\r\nantioxidant support, improved cardiovascular\r\nwell-being, and better digestion—all contributing to a\r\ncomplete diabetes management and wellness scheme.\r\nThis Diabetic Care Juice is made under rigorous quality\r\ncontrol measures from only the best, well-sourced\r\ningredients to provide each sip with a concentrated\r\ndosage of nature\'s most potent healing plant extracts\r\n\r\nthat act synergistically to fight against oxidative stress,\r\ndampen inflammation, and support a harmonious\r\nmetabolism without any dependency on synthetic\r\nadditives or chemicals, making it a perfect product for\r\nhealth-conscious consumers who want a safe, effective,\r\nand high-quality herbal supplement aligned with\r\nAyurvedic principles and contemporary scientific\r\nevidence.\r\nFormulated to address the needs of today\'s busy\r\nprofessionals, gym-goers, and entrepreneurs who care\r\nabout their health and well-being, our product is\r\nconveniently packaged for daily consumption and can\r\nbe easily integrated into a healthy diet and lifestyle\r\nroutine. Whether in the morning to rev up your\r\nmetabolism or as part of a general daily regimen to\r\nprovide stable energy and avoid the unpredictable\r\nspikes and dives in blood sugar that can contribute to\r\nlong-term health problems; in addition, its natural\r\ndetoxifying abilities help purify the body of built-up\r\ntoxins while supporting liver health, immune system\r\nfunction, and cardiovascular health, making it a vital\r\naddition to any health and wellness regimen.\r\nThe technology behind our product is based on the\r\nancient Ayurvedic principle of balancing the doshas,\r\n\r\nspecifically by using bitter and astringent tastes that\r\nassist in controlling the digestive fire (Agni) of the body\r\nand ensuring effective assimilation of nutrients, and the\r\naddition of strong herbs such as Jamun and Gudmar,\r\nwhich have been clinically reported to possess\r\nanti-diabetic activity, to make the product not only\r\neffective in controlling hyperglycemia but also help\r\nprevent the development of diabetic complications by\r\nmaintaining endothelial function and inhibiting the\r\nglycation of proteins.\r\nApart from its main role as a diabetic care supplement,\r\nthis juice has been carefully crafted to be a holistic\r\nhealth tonic that caters to various aspects of well-being,\r\nranging from aiding weight management and\r\nincreasing energy levels to facilitating mental clarity\r\nand stress resistance, thus making it an ideal\r\ncompanion for those who have busy lifestyles and\r\nrequire a natural pick-me-up to keep their performance\r\ngoing in both personal and professional endeavors.', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `product_ingredients`
--

CREATE TABLE `product_ingredients` (
  `IngredientId` int NOT NULL,
  `ProductId` int NOT NULL,
  `IngredientName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `PhotoPath` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ingredients`
--

INSERT INTO `product_ingredients` (`IngredientId`, `ProductId`, `IngredientName`, `PhotoPath`) VALUES
(2, 14, 'Wheat Grass', '67672.png'),
(3, 6, 'Amla', '33065.png'),
(4, 10, 'Amla', '89007.png'),
(5, 10, 'Bel Patra', '18092.png'),
(6, 10, 'Giloy', '65137.png'),
(7, 10, 'Gudmaar', '74463.png'),
(8, 10, 'Jamun', '69944.png'),
(9, 10, 'Karela', '78741.png'),
(10, 10, 'Kutki', '33481.png'),
(11, 10, 'Methi', '61907.png'),
(12, 10, 'Neem', '93709.png'),
(13, 10, 'Tulsi', '82719.png'),
(14, 10, 'Vijaysar', '71593.png'),
(16, 11, 'Neem', '50798.png'),
(17, 11, 'Jamun', '97456.png'),
(22, 15, 'Raw Apple Juice', '62376.png'),
(23, 22, 'Bach', '37499.png'),
(24, 22, 'Jatamansi', '55413.png'),
(25, 22, 'Rudraksh', '86807.png'),
(26, 22, 'Sarpagandha', '74237.png'),
(27, 22, 'Shankhapushpi', '79779.png'),
(28, 23, 'Adusa', '51862.png'),
(29, 23, 'Amla', '27960.png'),
(30, 23, 'Ashok Chaal', '54862.png'),
(31, 23, 'Baheda', '93779.png'),
(32, 23, 'Bel Ka Fal', '66672.png'),
(33, 23, 'Chirayta', '41399.png'),
(34, 23, 'Daru Haldi', '59732.png'),
(35, 23, 'Harad', '18953.png'),
(36, 23, 'Harad', '11153.png'),
(37, 23, 'Kala Jeera', '24374.png'),
(38, 23, 'Mango Seed', '56022.png'),
(39, 23, 'Nagarmotha', '87824.png'),
(40, 23, 'Rasut', '28199.png'),
(41, 23, 'Soonth', '32456.png'),
(42, 25, 'Sarsaparilla', '98840.png'),
(43, 25, 'Shatavari', '76540.png'),
(44, 25, 'Shigru Patra', '89593.png'),
(45, 25, 'Shilajit ', '28114.png'),
(46, 25, 'Soonth', '11677.png'),
(47, 25, 'Suddh', '78512.png'),
(48, 25, 'Anardana ', '51897.png'),
(49, 25, 'Brahmi', '17074.png'),
(50, 25, 'Chitramoola', '76267.png'),
(51, 25, 'Daru Haldi ', '91521.png'),
(52, 25, 'Durva', '33701.png'),
(53, 25, 'GIloy', '51323.png'),
(54, 25, 'Gokshur', '32470.png'),
(55, 25, 'Guggulu', '83863.png'),
(56, 25, 'Jatamansi ', '52799.png'),
(57, 25, 'Jyotishmati', '72176.png'),
(58, 25, 'Kachur', '26616.png'),
(59, 25, 'Kanchanar', '56783.png'),
(60, 25, 'Lajlalu', '26065.png'),
(61, 25, 'Piper', '62048.png'),
(62, 25, 'Piplamool', '49851.png'),
(63, 25, 'Punarnava ', '12308.png'),
(64, 25, 'Rasna', '60457.png'),
(65, 25, 'Sariva', '63811.png'),
(66, 9, 'Lemon', '41571.png'),
(67, 9, 'Honey', '46602.png'),
(68, 9, 'Apple CV', '11593.png'),
(69, 9, 'Lahsun', '98513.png'),
(70, 9, 'Adrak', '21366.png'),
(71, 21, 'Kesar', '19746.jpg'),
(72, 21, 'Swarna Bhasma', '25920.jpg'),
(73, 21, 'Swarna Vang', '50310.jpg'),
(74, 21, 'Shilajit ', '39808.jpg'),
(75, 34, 'Raw Apple Juice', '33358.png'),
(76, 34, 'Honey', '86280.png'),
(77, 34, 'Lemon Juice', '57234.png'),
(78, 34, 'Ginger Juice', '44960.png'),
(79, 34, 'Garlic Juice', '60065.png'),
(80, 34, 'Amla', '65231.png'),
(81, 34, 'Bel Patra', '50059.png'),
(82, 34, 'Giloy', '51504.png'),
(83, 34, 'Gudmaar', '60451.png'),
(84, 34, 'Jamun', '11565.png'),
(85, 34, 'Karela', '36018.png'),
(86, 34, 'Kutki', '42424.png'),
(87, 34, 'Kutki', '79656.png'),
(88, 34, 'Methi', '20772.png'),
(89, 34, 'Neem', '87539.png'),
(90, 34, 'Tulsi', '60511.png'),
(91, 34, 'Vijaysar', '31400.png'),
(92, 35, 'Amla', '65231.png'),
(93, 35, 'Bel Patra', '50059.png'),
(94, 35, 'Giloy', '51504.png'),
(95, 35, 'Gudmaar', '60451.png'),
(96, 35, 'Jamun', '11565.png'),
(97, 35, 'Karela', '36018.png'),
(98, 35, 'Kutki', '42424.png'),
(100, 35, 'Methi', '20772.png'),
(101, 35, 'Neem', '87539.png'),
(102, 35, 'Tulsi', '60511.png'),
(103, 35, 'Vijaysar', '31400.png'),
(104, 35, 'Bach', '37499.png'),
(105, 35, 'Jatamansi', '55413.png'),
(106, 35, 'Rudraksh', '86807.png'),
(107, 35, 'Sarpagandha', '74237.png'),
(108, 35, 'Shankhapushpi', '79779.png'),
(109, 36, 'Lemon', '41571.png'),
(110, 36, 'Honey', '46602.png'),
(111, 36, 'Apple CV', '11593.png'),
(112, 36, 'Lahsun', '98513.png'),
(113, 36, 'Adrak', '21366.png'),
(114, 36, 'Bach', '37499.png'),
(115, 36, 'Jatamansi', '55413.png'),
(116, 36, 'Rudraksh', '86807.png'),
(117, 36, 'Sarpagandha', '74237.png'),
(118, 36, 'Shankhapushpi', '79779.png'),
(119, 37, 'Adusa', '51862.png'),
(120, 37, 'Amla', '27960.png'),
(121, 37, 'Ashok Chaal', '54862.png'),
(122, 37, 'Baheda', '93779.png'),
(123, 37, 'Bel Ka Fal', '66672.png'),
(124, 37, 'Chirayta', '41399.png'),
(125, 37, 'Daru Haldi', '59732.png'),
(126, 37, 'Harad', '18953.png'),
(127, 37, 'Harad', '11153.png'),
(128, 37, 'Kala Jeera', '24374.png'),
(129, 37, 'Mango Seed', '56022.png'),
(130, 37, 'Nagarmotha', '87824.png'),
(131, 37, 'Rasut', '28199.png'),
(132, 37, 'Soonth', '32456.png'),
(133, 38, 'Lemon', '41571.png'),
(134, 38, 'Honey', '46602.png'),
(135, 38, 'Apple CV', '11593.png'),
(136, 38, 'Lahsun', '98513.png'),
(137, 38, 'Adrak', '21366.png'),
(138, 38, 'Wheat Grass', '67672.png'),
(139, 39, 'Lemon', '41571.png'),
(140, 39, 'Amla', '89007.png'),
(141, 39, 'Bel Patra', '18092.png'),
(142, 39, 'Giloy', '65137.png'),
(143, 39, 'Gudmaar', '74463.png'),
(144, 39, 'Jamun', '69944.png'),
(145, 39, 'Karela', '78741.png'),
(146, 39, 'Kutki', '33481.png'),
(147, 39, 'Methi', '61907.png'),
(148, 39, 'Neem', '93709.png'),
(149, 39, 'Tulsi', '82719.png'),
(150, 39, 'Vijaysar', '71593.png'),
(151, 40, 'Bach', '37499.png'),
(152, 40, 'Jatamansi', '55413.png'),
(153, 40, 'Rudraksh', '86807.png'),
(154, 40, 'Sarpagandha', '74237.png'),
(155, 40, 'Shankhapushpi', '79779.png'),
(156, 40, 'Neem', '93709.png'),
(157, 40, 'Karela', '78741.png'),
(158, 41, 'Honey', '46602.png'),
(159, 41, 'Apple CV', '11593.png'),
(160, 41, 'Lahsun', '98513.png'),
(161, 41, 'Adrak', '21366.png'),
(162, 41, 'Amla', '89007.png'),
(163, 42, 'Wheat Grass', '67672.png'),
(164, 42, 'Amla', '89007.png'),
(165, 43, 'Sarsaparilla', '98840.png'),
(166, 43, 'Shatavari', '76540.png'),
(167, 43, 'Shigru Patra', '89593.png'),
(168, 43, 'Shilajit', '28114.png'),
(169, 43, 'Soonth', '11677.png'),
(170, 43, 'Suddh', '78512.png'),
(171, 43, 'Anardana', '51897.png'),
(172, 43, 'Brahmi', '17074.png'),
(173, 43, 'Chitramoola', '76267.png'),
(174, 43, 'Daru Haldi', '91521.png'),
(175, 43, 'Durva', '33701.png'),
(176, 43, 'GIloy', '51323.png'),
(177, 43, 'Gokshur', '32470.png'),
(178, 43, 'Guggulu', '83863.png'),
(179, 43, 'Jatamansi', '52799.png'),
(180, 43, 'Jyotishmati', '72176.png'),
(181, 43, 'Kachur', '26616.png'),
(182, 43, 'Kanchanar', '56783.png'),
(183, 43, 'Lajlalu', '26065.png'),
(184, 43, 'Piper', '62048.png'),
(185, 43, 'Piplamool', '49851.png'),
(186, 43, 'Punarnava', '12308.png'),
(187, 43, 'Rasna', '60457.png'),
(188, 43, 'Sariva', '63811.png'),
(189, 44, 'Sarsaparilla', '98840.png'),
(190, 44, 'Shatavari', '76540.png'),
(191, 44, 'Shigru Patra', '89593.png'),
(192, 44, 'Shilajit', '28114.png'),
(193, 44, 'Soonth', '11677.png'),
(194, 44, 'Suddh', '78512.png'),
(195, 44, 'Anardana', '51897.png'),
(196, 44, 'Brahmi', '17074.png'),
(197, 44, 'Chitramoola', '76267.png'),
(198, 44, 'Daru Haldi', '91521.png'),
(199, 44, 'Durva', '33701.png'),
(200, 44, 'GIloy', '51323.png'),
(201, 44, 'Gokshur', '32470.png'),
(202, 44, 'Guggulu', '83863.png'),
(203, 44, 'Jatamansi', '52799.png'),
(204, 44, 'Jyotishmati', '72176.png'),
(205, 44, 'Kachur', '26616.png'),
(206, 44, 'Kanchanar', '56783.png'),
(207, 44, 'Lajlalu', '26065.png'),
(208, 44, 'Piper', '62048.png'),
(209, 44, 'Piplamool', '49851.png'),
(210, 44, 'Punarnava', '12308.png'),
(211, 44, 'Rasna', '60457.png'),
(212, 44, 'Sariva', '63811.png'),
(213, 45, 'Sarsaparilla', '98840.png'),
(214, 45, 'Shatavari', '76540.png'),
(215, 45, 'Shigru Patra', '89593.png'),
(216, 45, 'Shilajit', '28114.png'),
(217, 45, 'Soonth', '11677.png'),
(218, 45, 'Suddh', '78512.png'),
(219, 45, 'Anardana', '51897.png'),
(220, 45, 'Brahmi', '17074.png'),
(221, 45, 'Chitramoola', '76267.png'),
(222, 45, 'Daru Haldi', '91521.png'),
(223, 45, 'Durva', '33701.png'),
(224, 45, 'GIloy', '51323.png'),
(225, 45, 'Gokshur', '32470.png'),
(226, 45, 'Guggulu', '83863.png'),
(227, 45, 'Jatamansi', '52799.png'),
(228, 45, 'Jyotishmati', '72176.png'),
(229, 45, 'Kachur', '26616.png'),
(230, 45, 'Kanchanar', '56783.png'),
(231, 45, 'Lajlalu', '26065.png'),
(232, 45, 'Piper', '62048.png'),
(233, 45, 'Piplamool', '49851.png'),
(234, 45, 'Punarnava', '12308.png'),
(235, 45, 'Rasna', '60457.png'),
(236, 45, 'Sariva', '63811.png'),
(237, 46, 'Honey', '46602.png'),
(238, 46, 'Apple CV', '11593.png'),
(239, 46, 'Lahsun', '98513.png'),
(240, 46, 'Adrak', '21366.png'),
(241, 46, 'Neem', '93709.png'),
(242, 46, 'Karela', '78741.png'),
(243, 47, 'Honey', '46602.png'),
(244, 47, 'Apple CV', '11593.png'),
(245, 47, 'Lahsun', '98513.png'),
(246, 47, 'Adrak', '21366.png'),
(247, 47, 'Adusa', '51862.png'),
(248, 47, 'Amla', '27960.png'),
(249, 47, 'Ashok Chaal', '54862.png'),
(250, 47, 'Baheda', '93779.png'),
(251, 47, 'Bel Ka Fal', '66672.png'),
(252, 47, 'Chirayta', '41399.png'),
(253, 47, 'Daru Haldi', '59732.png'),
(254, 47, 'Harad', '18953.png'),
(255, 47, 'Harad', '11153.png'),
(256, 47, 'Kala Jeera', '24374.png'),
(257, 47, 'Mango Seed', '56022.png'),
(258, 47, 'Nagarmotha', '87824.png'),
(259, 47, 'Rasut', '28199.png'),
(260, 49, 'Honey', '46602.png'),
(261, 49, 'Apple CV', '11593.png'),
(262, 49, 'Lahsun', '98513.png'),
(263, 49, 'Adrak', '21366.png'),
(264, 49, 'Bach', '37499.png'),
(265, 49, 'Jatamansi', '55413.png'),
(266, 49, 'Rudraksh', '86807.png'),
(267, 49, 'Sarpagandha', '74237.png'),
(268, 49, 'Shankhapushpi', '79779.png');

-- --------------------------------------------------------

--
-- Table structure for table `product_master`
--

CREATE TABLE `product_master` (
  `ProductId` int NOT NULL,
  `Description` text COLLATE utf8mb4_general_ci,
  `CategoryId` int DEFAULT NULL,
  `SubCategoryId` int DEFAULT NULL,
  `ProductName` text COLLATE utf8mb4_general_ci,
  `MetaTags` text COLLATE utf8mb4_general_ci,
  `MetaKeywords` text COLLATE utf8mb4_general_ci,
  `ShortDescription` text COLLATE utf8mb4_general_ci,
  `PhotoPath` text COLLATE utf8mb4_general_ci,
  `Specification` text COLLATE utf8mb4_general_ci,
  `VideoURL` text COLLATE utf8mb4_general_ci,
  `ProductCode` text COLLATE utf8mb4_general_ci,
  `IsCombo` text COLLATE utf8mb4_general_ci,
  `Title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_master`
--

INSERT INTO `product_master` (`ProductId`, `Description`, `CategoryId`, `SubCategoryId`, `ProductName`, `MetaTags`, `MetaKeywords`, `ShortDescription`, `PhotoPath`, `Specification`, `VideoURL`, `ProductCode`, `IsCombo`, `Title`) VALUES
(6, '', 1, 1, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', 'My Nutrify Herbal & Ayurveda\'s Wild Amla Juice is a potent natural source of Vitamin C, made from handpicked wild Indian gooseberries. This revitalizing juice supports immunity, aids digestion, promotes glowing skin, and strengthens hair. Free from added sugar and chemicals, itâ€™s a pure and powerful daily health tonic for overall wellness.', '6526.png', '<span id=\"docs-internal-guid-c30a5736-7fff-1ebf-8311-42d1341fee9a\"><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Rich in Vitamin C &amp; Antioxidants:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Enhances immunity and fights infections, Helps in collagen production for healthy skin &amp; hair</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports Digestion &amp; Gut Health:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Aids in better digestion and relieves acidity, Improves gut health by promoting good bacteria</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Boosts Hair Growth &amp; Reduces Hair Fall</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">: Strengthens hair follicles and prevents premature greying, Nourishes scalp, reducing dandruff</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances Skin Glow &amp; Fights Aging:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Detoxifies skin, making it clear and radiant, Reduces wrinkles and pigmentation</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Helps in Detoxification &amp; Liver Health</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">: Flushes out toxins from the body, Supports liver function and reduces oxidative stress</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports Weight Management: </span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Boosts metabolism and aids in fat reduction, Controls appetite naturally</span></p></li></ul></span>', '', 'MN-AM100', NULL, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster'),
(9, '', 1, 1, 'My Nutrify Herbal & Ayurveda\'s  Cholesterol Care Juice Boosts Heart Health, Supports Cholesterol Balance with  Ginger, Garlic, Lemon, Honey & Apple Cider Vinegarâ€“ Antioxidant & Detox |  1 Ltr ', 'My Nutrify Herbal & Ayurveda\'s  Cholesterol Care Juice Boosts Heart Health, Supports Cholesterol Balance with Amla, Giloy, Turmeric, Neem & Ashwagandha â€“ Antioxidant & Detox | 500 ml / 1000 ml', 'My Nutrify Herbal & Ayurveda\'s  Cholesterol Care Juice Boosts Heart Health, Supports Cholesterol Balance with Amla, Giloy, Turmeric, Neem & Ashwagandha â€“ Antioxidant & Detox | 500 ml / 1000 ml', 'My Nutrify Herbal & Ayurveda\'s Cholesterol Care Juice  is a natural Ayurvedic blend crafted to support healthy cholesterol levels, heart function, and overall cardiovascular wellness. Enriched with plant sterols, antioxidants, and heart-protective herbs, it helps lower LDL, boost HDL, balance triglycerides, and reduce inflammation. This 100% natural, sugar-free formula also supports liver health, weight management, improved circulation, and blood purification. Ideal for those with high cholesterol, a family history of heart disease, or sedentary lifestyles, itâ€™s a safe, daily tonic for holistic heart careâ€”rooted in Ayurveda and backed by modern science.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n', '9444.jpg', '<ul style=\"margin-bottom: 0px; color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 17.3333px; white-space: pre; padding-inline-start: 48px;\"><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 12pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports healthy cholesterol levels</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes heart health and circulation</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Helps lower LDL and raise HDL cholesterol</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Rich in antioxidants for cardiovascular wellness</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Aids in detoxification and weight management</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 12pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">May help maintain healthy blood pressure</span></p></li></ul>', '', 'MN-CC100', NULL, 'My Nutrify Herbal & Ayurveda\'s  Cholesterol Care Juice Boosts Heart Health, Supports Cholesterol Balance with  Ginger, Garlic, Lemon, Honey & Apple Cider Vinegarâ€“ Antioxidant & Detox |  1 Ltr '),
(10, '', 1, 1, 'My Nutrify Herbal and Ayurveda\'s  Diabetic Care Juice - 1 Ltr | Natural Blood Glucose Control | Enhances Insulin Sensitivity & Metabolic Health | Boosts Energy, Digestion & Immunity | 100% Herbal & No Added Sugar', 'My Nutrify Herbal and Ayurveda\'s  Diabetic Care Juice - 1 Ltr | Natural Blood Glucose Control | Enhances Insulin Sensitivity & Metabolic Health | Boosts Energy, Digestion & Immunity | 100% Herbal & No Added Sugar', 'My Nutrify Herbal and Ayurveda\'s  Diabetic Care Juice - 1 Ltr | Natural Blood Glucose Control | Enhances Insulin Sensitivity & Metabolic Health | Boosts Energy, Digestion & Immunity | 100% Herbal & No Added Sugar', 'My Nutrify Herbal & Ayurveda\'s Diabetic Care Juice is an\r\ninnovative, all-natural, and high-quality herbal product\r\ncarefully formulated to assist in the best blood sugar\r\nequilibrium and overall metabolic health, blending the\r\n\r\nancient knowledge of Ayurveda with contemporary\r\nnutritional science to offer a higher quality dietary\r\nsupplement perfectly suited for diabetic as well as\r\npre-diabetic management while also acting as an\r\nexcellent preventive agent. My Nutrify Herbal &\r\nAyurveda\'s Diabetic Care Juice is a Natural Blend of 13\r\nPowerful herbs.\r\nThis special combination taps the powerful potential of\r\nage-old herbs like bitter gourd (Karela), Indian\r\nblackberry (Jamun), Gudmar, Amla (Indian\r\nGooseberry), Neem, and fenugreek (Methi) that are\r\nrenowned for their natural capacity to balance blood\r\nsugar levels, increase insulin sensitivity, and improve\r\npancreatic functioning and thus create a revolutionary\r\nsystem that naturally stimulates glycemic control and\r\nreduces the typical complications of diabetes while at\r\nthe same time providing other health benefits such as\r\nantioxidant support, improved cardiovascular\r\nwell-being, and better digestion—all contributing to a\r\ncomplete diabetes management and wellness scheme.\r\nThis Diabetic Care Juice is made under rigorous quality\r\ncontrol measures from only the best, well-sourced\r\ningredients to provide each sip with a concentrated\r\ndosage of nature\'s most potent healing plant extracts\r\n\r\nthat act synergistically to fight against oxidative stress,\r\ndampen inflammation, and support a harmonious\r\nmetabolism without any dependency on synthetic\r\nadditives or chemicals, making it a perfect product for\r\nhealth-conscious consumers who want a safe, effective,\r\nand high-quality herbal supplement aligned with\r\nAyurvedic principles and contemporary scientific\r\nevidence.\r\nFormulated to address the needs of today\'s busy\r\nprofessionals, gym-goers, and entrepreneurs who care\r\nabout their health and well-being, our product is\r\nconveniently packaged for daily consumption and can\r\nbe easily integrated into a healthy diet and lifestyle\r\nroutine. Whether in the morning to rev up your\r\nmetabolism or as part of a general daily regimen to\r\nprovide stable energy and avoid the unpredictable\r\nspikes and dives in blood sugar that can contribute to\r\nlong-term health problems; in addition, its natural\r\ndetoxifying abilities help purify the body of built-up\r\ntoxins while supporting liver health, immune system\r\nfunction, and cardiovascular health, making it a vital\r\naddition to any health and wellness regimen.\r\nThe technology behind our product is based on the\r\nancient Ayurvedic principle of balancing the doshas,\r\n\r\nspecifically by using bitter and astringent tastes that\r\nassist in controlling the digestive fire (Agni) of the body\r\nand ensuring effective assimilation of nutrients, and the\r\naddition of strong herbs such as Jamun and Gudmar,\r\nwhich have been clinically reported to possess\r\nanti-diabetic activity, to make the product not only\r\neffective in controlling hyperglycemia but also help\r\nprevent the development of diabetic complications by\r\nmaintaining endothelial function and inhibiting the\r\nglycation of proteins.\r\nApart from its main role as a diabetic care supplement,\r\nthis juice has been carefully crafted to be a holistic\r\nhealth tonic that caters to various aspects of well-being,\r\nranging from aiding weight management and\r\nincreasing energy levels to facilitating mental clarity\r\nand stress resistance, thus making it an ideal\r\ncompanion for those who have busy lifestyles and\r\nrequire a natural pick-me-up to keep their performance\r\ngoing in both personal and professional endeavors.', '4772.jpg', '<span id=\"docs-internal-guid-66e5e6ce-7fff-33b8-9954-be231d5ec891\"><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Regulates Blood Sugar:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Maintains optimal glucose levels naturally.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances Insulin Sensitivity: </span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Ingredients like Karela and Jamun support efficient insulin function.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports Kidney Health:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Guduchi aids in detoxifying and rejuvenating kidney function.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Boosts Metabolism: </span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes better digestion and nutrient absorption.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Aids in Weight Management: </span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances metabolism to help regulate body weight.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Rich in Antioxidants: </span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Protects against oxidative stress and free radical damage.</span></p></li></ul></span>', '', 'MN-DC100', NULL, 'My Nutrify Herbal and Ayurveda\'s  Diabetic Care Juice - 1 Ltr | Natural Blood Glucose Control | Enhances Insulin Sensitivity & Metabolic Health | Boosts Energy, Digestion & Immunity | 100% Herbal & No Added Sugar'),
(11, '', 1, 1, 'My Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun Juice -  1 Ltr | Supports Blood Sugar, Digestion & Detox | Pure Herbal Health Tonic. ', 'My Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun Juice - 1 Ltr | Supports Blood Sugar, Digestion & Detox | Pure Herbal Health Tonic. ', 'My Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun Juice - 1 Ltr | Supports Blood Sugar, Digestion & Detox | Pure Herbal Health Tonic. ', 'Nutrify Herbal & Ayurveda  Karela Neem & Jamun Juice is an effective combination of natural ingredients famous for their medicinal properties. Formulated from Neem, Karela (bitter gourd), and Jamun (Indian blackberry), this juice assists in maintaining healthy blood sugar levels, enhances digestion, and supports immunity. Being rich in antioxidants and essential nutrients, it supports detoxification, encourages glowing skin, and contributes to overall wellness. An ideal daily tonic for a healthier lifestyle.', '7133.jpg', '<div id=\"importantInformation_feature_div\" class=\"celwidget\" data-feature-name=\"importantInformation\" data-csa-c-type=\"widget\" data-csa-c-content-id=\"importantInformation\" data-csa-c-slot-id=\"importantInformation_feature_div\" data-csa-c-asin=\"\" data-csa-c-is-in-initial-active-row=\"false\" data-csa-c-id=\"lls474-z1671h-6k40jb-kg9e4h\" data-cel-widget=\"importantInformation_feature_div\" style=\"color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><div id=\"important-information\" class=\"a-section a-spacing-extra-large bucket\" style=\"margin-bottom: 0px;\"><div class=\"a-section content\" style=\"margin-bottom: 22px; margin-top: 6.5px; margin-left: 25px;\"><h4 style=\"padding: 0px 0px 4px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; text-rendering: optimizelegibility; font-weight: 700; font-size: 18px; line-height: 24px;\"><span style=\"font-weight:normal;\" id=\"docs-internal-guid-ad0cc465-7fff-f9ab-1b79-2212e7e00036\"><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 11pt; font-family: Georgia, serif; color: rgb(33, 37, 41); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre; margin-left: 36pt;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 11pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports Blood Sugar Management</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 11pt; font-family: Georgia, serif; color: rgb(33, 37, 41); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre; margin-left: 36pt;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 11pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Detoxifies the Body</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 11pt; font-family: Georgia, serif; color: rgb(33, 37, 41); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre; margin-left: 36pt;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 11pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes Healthy Skin</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 11pt; font-family: Georgia, serif; color: rgb(33, 37, 41); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre; margin-left: 36pt;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 11pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Improves Metabolism</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 11pt; font-family: Georgia, serif; color: rgb(33, 37, 41); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre; margin-left: 36pt;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 11pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Maintains Healthy Cholesterol Levels</span></p></li></ul></span></h4></div></div></div>', '', 'MN-KN100', NULL, 'My Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun Juice -  1 Ltr | Supports Blood Sugar, Digestion & Detox | Pure Herbal Health Tonic. '),
(14, '', 1, 1, 'My Nutrify Herbal & Ayurveda\'s  Wheatgrass Juice - 1 Ltr | 100% Natural Detox & Immunity Booster | Rich in Chlorophyll, Vitamins & Antioxidants, Supports Digestion & Metabolism ', 'My Nutrify Herbal & Ayurveda\'s  Wheatgrass Juice - 1 Ltr  | 100% Natural Detox & Immunity Booster | Rich in Chlorophyll, Vitamins & Antioxidants | Supports Digestion & Metabolism | 500ml', 'My Nutrify Herbal & Ayurveda\'s  Wheatgrass Juice - 1 Ltr  | 100% Natural Detox & Immunity Booster | Rich in Chlorophyll, Vitamins & Antioxidants | Supports Digestion & Metabolism | 500ml', 'My Nutrify Herbal & Ayurveda Wheat Grass Juice is a pure, organic detox drink made from eco-friendly, ethically sourced wheatgrass, rich in chlorophyll, antioxidants, and essential nutrients. It helps cleanse the liver, boost immunity, improve digestion, and enhance energy levels. Packed with vitamins A, C, and E, along with iron and magnesium, it supports skin health, muscle function, and overall vitality. Free from chemicals and artificial additives, this daily health tonic is perfect for anyone seeking a natural, Ayurvedic way to energize, detoxify, and maintain an active lifestyleâ€”now available in eco-friendly packaging.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n', '9976.jpg', '<span id=\"docs-internal-guid-6c1e8d95-7fff-95d4-ca51-8484cab2df98\"><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Powerful Ayurvedic Detoxifier:</span><span style=\"font-size: 10pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Supports liver detoxification, acts as a liver tonic, and promotes overall body cleansing.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes Digestive Health:</span><span style=\"font-size: 10pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Rich in fiber and enzymes that cleanse the digestive system and boost energy levels.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances Skin Health:</span><span style=\"font-size: 10pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Naturally purifies the blood, contributing to clear, glowing skin.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Superior Nutritional Sourcing: </span><span style=\"font-size: 10pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Harvested at the optimal stage to ensure maximum chlorophyll content and nutritional potency.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10pt; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">100% Pure &amp; Ayurvedic: </span><span style=\"font-size: 10pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Made exclusively from fresh wheatgrass with no added sugars, artificial colors, or preservatives.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 10pt; font-family: Georgia, serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size: 10pt; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Convenient Liquid Form: </span><span style=\"font-size: 10pt; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Offers faster absorption compared to wheatgrass powder for effective detoxification and nourishment.</span></p></li></ul></span>', '', 'MN-WG100', NULL, 'My Nutrify Herbal & Ayurveda\'s  Wheatgrass Juice - 1 Ltr | 100% Natural Detox & Immunity Booster | Rich in Chlorophyll, Vitamins & Antioxidants, Supports Digestion & Metabolism '),
(15, '', 1, 1, 'My Nutrify Herbal & Ayurveda\'s  Apple Cider Vinegar- 1 Ltr | Raw, Unfiltered & With Mother | Aids Digestion, Detox, Supports Weight Management, Boosts Immunity, Improves Skin & Hair Health ', 'My Nutrify Herbal & Ayurveda\'s  Apple Cider Vinegar- 1 Ltr  | Raw, Unfiltered & With Mother | Aids Digestion, Detox, Supports Weight Management, Boosts Immunity, Improves Skin & Hair Health | 500ml / 1000ml', 'My Nutrify Herbal & Ayurveda\'s  Apple Cider Vinegar- 1 Ltr  | Raw, Unfiltered & With Mother | Aids Digestion, Detox, Supports Weight Management, Boosts Immunity, Improves Skin & Hair Health | 500ml / 1000ml', 'My Nutrify Herbal & Ayurveda\'s Apple Cider Vinegar is a potent, natural health tonic crafted with Raw Apples . Rich in antioxidants and acetic acid, it supports digestion, boosts immunity, and promotes heart healthâ€”all wrapped up in a refreshing flavor perfect for your daily wellness routine.\r\n', '7579.jpg', '<ul style=\"margin-bottom: 0px; color: rgb(0, 0, 0); font-family: Arial, sans-serif; font-size: 13.3333px; white-space: pre; padding-inline-start: 48px;\"><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 12pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports Weight Management:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Boosts metabolism and aids in fat reduction when paired with a balanced diet.</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances Digestive Health:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Contains natural enzymes that improve digestion and promote a healthy gut.</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Boosts Immunity:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Rich in antioxidants and antimicrobial properties to strengthen your immune system.</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Regulates Blood Sugar:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Helps maintain balanced blood sugar levels and improves insulin sensitivity.</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes Heart Health:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Supports healthy blood pressure and cholesterol levels.</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 12pt; line-height: 1.38;\"><span style=\"font-size: 10pt; background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Improves Skin &amp; Hair:</span><span style=\"font-size: 10pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Detoxifies the skin and enhances hair health by reducing impurities and promoting natural shine.</span></p></li></ul>', '', 'MN-AC100', NULL, 'My Nutrify Herbal & Ayurveda\'s  Apple Cider Vinegar- 1 Ltr | Raw, Unfiltered & With Mother | Aids Digestion, Detox, Supports Weight Management, Boosts Immunity, Improves Skin & Hair Health ');
INSERT INTO `product_master` (`ProductId`, `Description`, `CategoryId`, `SubCategoryId`, `ProductName`, `MetaTags`, `MetaKeywords`, `ShortDescription`, `PhotoPath`, `Specification`, `VideoURL`, `ProductCode`, `IsCombo`, `Title`) VALUES
(18, 'Shilajit Himalayan Origin, is  a powerhouse of essential nutrients, is known for its detoxifying and refreshing properties. \n\nRich in fulvic acid, B vitamins, magnesium, and potassium, My Nutrify Shilajit Himalayan Origin naturally boosts energy and overall health. This resin contains bioactive ingredients from pure, natural Shilajit sourced from Himalayan rocks.\n', 7, 1, 'My Nutrify Herbal & Ayurveda\'s Pure Shilajit Resin â€“ Himalayan Shilajit Power with Fulvic Acid, Minerals & Antioxidants | Increases Energy, Stamina & Strength | Enhances Immunity, Testosterone & Overall Well-Being | 100% Natural & Ayurvedic', 'My Nutrify Herbal & Ayurveda\'s Pure Shilajit Resin â€“ Himalayan Shilajit Power with Fulvic Acid, Minerals & Antioxidants | Increases Energy, Stamina & Strength | Enhances Immunity, Testosterone & Overall Well-Being | 100% Natural & Ayurvedic', 'My Nutrify Herbal & Ayurveda\'s Pure Shilajit Resin â€“ Himalayan Shilajit Power with Fulvic Acid, Minerals & Antioxidants | Increases Energy, Stamina & Strength | Enhances Immunity, Testosterone & Overall Well-Being | 100% Natural & Ayurvedic', 'My Nutrify Herbal & Ayurveda\'s Shilajit Resin Origin is a centuries-old natural substance produced in the Himalayas and Altai Mountains. Packed with fulvic acid and valuable minerals, it has long been used in Ayurveda to promote wellness and energy. Derived from high-altitude rocks and carefully refined, it supports energy, endurance, and overall well-being.\r\n', '2974.png', '<div><b>Key Specification</b></div><ul><ul style=\"padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Increases Energy &amp; Stamina: </span><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances physical performance and reduces fatigue.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Maintains Testosterone Levels: </span><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports healthy male virility and reproductive function.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Improves Brain Function:</span><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Boosts cognitive performance, memory, and concentration.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">High in Fulvic Acid: </span><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes improved nutrient uptake and detoxification.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Boosts Immunity: </span><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Aids the body in fighting infections and supports overall well-being.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances Digestion:</span><span style=\"font-size: 10.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Supports gut health and improves metabolism.\r\n</span><span style=\"background-color: transparent; font-size: 10.5pt; color: rgb(34, 34, 34); font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\">Anti-Aging Qualities: </span><span style=\"background-color: transparent; font-size: 10.5pt; color: rgb(34, 34, 34); font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\">Helps reduce oxidative stress and promotes youthful skin.</span></p></li></ul></ul>', '', 'MN-SG020', NULL, 'Pure Shilajit Resin'),
(19, 'My Nutrify Pure Himalayan Shilajit Resin is a premium, natural supplement packed with essential nutrients to boost energy, stamina, and overall vitality. Sourced from the pristine Himalayan rocks, this pure resin is minimally processed to retain its full nutritional potency.\n', 7, 1, ' My Nutrify Herbal & Ayurvedaâ€™s Shilajit Resin Pro | 100% Pure Himalayan Shilajit | Energy, Strength & Stamina Booster | Rich in Fulvic Acid & Minerals | Supports Endurance, Vitality & Wellness', 'Shilajit Resin, Pure Shilajit, My Nutrify Shilajit, Shilajit for Energy, Natural Shilajit Resin, Buy Shilajit Online, Shilajit Benefits, Stamina Booster, Immunity Booster, Organic Shilajit', 'Shilajit Resin, Pure Shilajit, My Nutrify Shilajit, Shilajit for Energy, Natural Shilajit Resin, Buy Shilajit Online, Shilajit Benefits, Stamina Booster, Immunity Booster, Organic Shilajit', 'My Nutrify Herbal & Ayurveda Shilajit Resin Pro is a premium, 100% natural supplement sourced from the high-altitude Himalayas, rich in fulvic acid, essential minerals, and powerful antioxidants. This Ayurvedic adaptogen boosts energy, stamina, cognitive function, hormonal balance, and immune strength. It supports detoxification, enhances nutrient absorption, and promotes anti-aging, cardiovascular, and digestive health. Ideal for athletes, professionals, and wellness seekers, this pure, preservative-free resin elevates physical and mental vitality while helping combat stress and fatigueâ€”making it a holistic, daily wellness essential.', '3310.jpg', '<span id=\"docs-internal-guid-852abd86-7fff-d690-a8ea-22d850732042\"><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 17pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Increases Energy &amp; Stamina: </span><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Enhances physical performance and reduces fatigue.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 17pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Maintains Testosterone Levels: </span><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports healthy male virility and reproductive function.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 17pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Improves Brain Function:</span><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\"> Boosts cognitive performance, memory, and concentration.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 17pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">High in Fulvic Acid: </span><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes improved nutrient uptake and detoxification.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 17pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Boosts Immunity: </span><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Aids the body in fighting infections and supports overall well-being.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 17pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space-collapse: preserve;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 12pt;\" role=\"presentation\"><span style=\"text-wrap-mode: wrap; font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-weight: 700; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\">Enhances Digestion:</span><span style=\"font-size: 12.5pt; color: rgb(34, 34, 34); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline;\"> Supports gut health and improves metabolism.\r\n</span><span id=\"docs-internal-guid-b3b7a390-7fff-bf00-0725-a0304cd9e2e5\"></span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:17pt;font-family:Arial,sans-serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:12.499999999999998pt;font-family:Arial,sans-serif;color:#222222;background-color:transparent;font-weight:700;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Anti-Aging Qualities: </span><span style=\"font-size:12.499999999999998pt;font-family:Arial,sans-serif;color:#222222;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Helps reduce oxidative stress and promotes youthful skin.</span></p></li></ul></li></ul></span>', '', 'MN-SR020', NULL, 'Pure Shilajit Pro'),
(21, 'Shilajit Gold Pro is a premium herbal formulation designed to boost energy, stamina, strength, and overall vitality. Enriched with Shilajit and other essential natural ingredients, it promotes overall well-being, supports male health, and enhances physical and mental performance.\n', 7, 1, 'My Nutrify Herbal & Ayurveda\'s Himalayan Shilajit Gold Pro â€“ Premium Himalayan Shilajit & Ayurvedic Super Herbs for Strength, Testosterone & Immunity Support', 'My Nutrify Herbal & Ayurveda\'s Himalayan Shilajit Gold Pro â€“ Premium Himalayan Shilajit & Ayurvedic Super Herbs for Strength, Testosterone & Immunity Support', 'My Nutrify Herbal & Ayurveda\'s Himalayan Shilajit Gold Pro â€“ Premium Himalayan Shilajit & Ayurvedic Super Herbs for Strength, Testosterone & Immunity Support', 'My Nutrify Herbal & Ayurveda\'s Shilajit Gold Pro is a premium Ayurvedic formulation designed to boost vitality, strength, and overall well-being. This powerful blend combines pure Himalayan Shilajit resin, Kesar (Saffron) for energy, Swarna Bhasma for rejuvenation, and Swarna Vang to enhance endurance and stamina. Rich in fulvic acid and essential minerals, it promotes immune strength, mental clarity, and sustained energy. Experience the wisdom of Ayurveda in every drop for a healthier, more active lifestyle.\r\n', '5992.jpg', '<span id=\"docs-internal-guid-15afd9ed-7fff-c0b1-f846-db8ea22e74ce\"><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 15pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Strength &amp; Revitalization: Helps improve muscle strength and fight fatigue.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 15pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Fulvic Acid Enriched: Contains potent antioxidants with anti-inflammatory properties.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 15pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Natural Energy Booster: Includes 80+ trace minerals for sustained energy.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 15pt; font-family: Arial, sans-serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size: 15pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Authentic Himalayan Sourcing: Extracted from golden-brown Shilajit rocks in the high-altitude Himalayas.</span></p></li></ul><br></span>', '', 'MN-SG020', NULL, 'Pure Shilajit Gold Pro'),
(22, '', 1, 1, 'My Nutrify Herbal & Ayurveda\'s BP Care Juice 1 Ltr â€“ Natural Blood Pressure Management, Helps Reduce Stress and Supports Heart Function. Enriched with Bach, Sarpagandha, Shankhpushpi, and many other herbs.', 'My Nutrify Herbal & Ayurveda\'s BP Care Juice 1 Ltr  â€“ Natural Blood Pressure Management, Helps Reduce Stress and Supports Heart Function. Enriched with Bach, Sarpagandha, Shankhpushpi, and many other herbs.', 'My Nutrify Herbal & Ayurveda\'s BP Care Juice 1 Ltr  â€“ Natural Blood Pressure Management, Helps Reduce Stress and Supports Heart Function. Enriched with Bach, Sarpagandha, Shankhpushpi, and many other herbs.', 'My Nutrify Herbal & Ayurveda\'s BP Care Juice is a natural blend of Ayurvedic herbs, adaptogens, and antioxidants designed to support healthy blood pressure and heart health. Crafted from time-tested Ayurvedic wisdom and modern nutrition, it promotes circulation, reduces oxidative stress, and helps calm the body. Free from artificial additives, this daily tonic supports vascular integrity, lowers cholesterol, and enhances overall cardiovascular wellnessâ€”making it a perfect addition to a heart-healthy lifestyle.', '6726.jpg', '<span id=\"docs-internal-guid-45cc887c-7fff-fdde-2c14-2d562313b89a\"><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 12pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports healthy blood pressure levels.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 12pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Promotes heart health.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 12pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Rich in antioxidants.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 12pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size: 12pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Improves blood circulation.</span></p></li><li dir=\"ltr\" style=\"list-style-type: disc; font-size: 12pt; font-family: Georgia, serif; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; white-space: pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size: 12pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-variant-position: normal; font-variant-emoji: normal; vertical-align: baseline; text-wrap-mode: wrap;\">Supports detoxification.</span></p></li></ul></span>', '', 'MN-BC100', NULL, 'My Nutrify Herbal & Ayurveda\'s BP Care Juice 1 Ltr â€“ Natural Blood Pressure Management, Helps Reduce Stress and Supports Heart Function. Enriched with Bach, Sarpagandha, Shankhpushpi, and many other herbs.'),
(23, '', 1, 1, 'My Nutrify Herbal & Ayurveda\'s She Care plus Juice â€“ A Natural Women\'s Health Drink for Hormonal Balance, Period Relief, and Skin & Hair Support | Detox, Digestion & Immunity | PCOD/PCOS | 1 Ltr ', 'My Nutrify Herbal & Ayurveda\'s She Care plus Juice â€“ A Natural Women\'s Health Drink for Hormonal Balance, Period Relief, and Skin & Hair Support | Detox, Digestion & Immunity | PCOD/PCOS | 1 Ltr ', 'My Nutrify Herbal & Ayurveda\'s She Care plus Juice â€“ A Natural Women\'s Health Drink for Hormonal Balance, Period Relief, and Skin & Hair Support | Detox, Digestion & Immunity | PCOD/PCOS | 1 Ltr ', 'My Nutrify Herbal & Ayurveda\'s She Care Plus Juice is a powerful Ayurvedic blend crafted to support womenâ€™s reproductive health and hormonal balance. Enriched with herbs like Bel ka Fal, Adusa, Nagarmotha, Rasaut, Daru Haldi, and Amla, it helps manage menstrual irregularities, PCOD/PCOS symptoms, mood swings, fatigue, and acne. This natural tonic works from within to detoxify the reproductive system, regulate cycles, and restore hormonal harmonyâ€”supporting a pain-free, balanced, and vibrant life. 100% natural, free from chemicals and added hormones, itâ€™s safe for daily use and ideal for every stage of womanhood.', '2081.jpg', '<p><span id=\"docs-internal-guid-419467a4-7fff-aa0a-7f33-c445dc454650\"></span></p><ul style=\"margin-top:0;margin-bottom:0;padding-inline-start:48px;\"><li dir=\"ltr\" style=\"list-style-type:disc;font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:12pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Improves digestion and metabolism</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Detoxifies the body naturally</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Promotes healthy skin and hair</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:0pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Helps reduce stress and anxiety</span></p></li><li dir=\"ltr\" style=\"list-style-type:disc;font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;\" aria-level=\"1\"><p dir=\"ltr\" style=\"line-height:1.38;margin-top:0pt;margin-bottom:12pt;\" role=\"presentation\"><span style=\"font-size:12pt;font-family:Georgia,serif;color:#000000;background-color:transparent;font-weight:400;font-style:normal;font-variant:normal;text-decoration:none;vertical-align:baseline;white-space:pre;white-space:pre-wrap;\">Supports hormonal balance in women</span></p></li></ul>', '', 'MN-SC020', NULL, 'My Nutrify Herbal & Ayurveda\'s She Care plus Juice â€“ A Natural Women\'s Health Drink for Hormonal Balance, Period Relief, and Skin & Hair Support | Detox, Digestion & Immunity | PCOD/PCOS | 1 Ltr '),
(25, 'My Nutrify Herbal & Ayurveda\'s Thyro Balance Care Juice is a natural blend of 20+ Ayurvedic herbs like Kanchnar, Shuddh Shilajit, Guggulu, and Brahmi. It supports healthy thyroid function, boosts metabolism, and balances hormones. Helps manage fatigue, weight issues, and mood swings. 100% natural with No added sugar or chemicals.', 1, 1, 'My Nutrify Herbal & Ayurveda\'s Thyro Balance Care Juice â€“ 1 Ltr |  Natural Thyroid Support, Energy Booster & Metabolism Enhancer.', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance Juice â€“ 1 Ltr |  Natural Thyroid Support, Energy Booster & Metabolism Enhancer, 1000ml', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance Juice â€“ 1 Ltr |  Natural Thyroid Support, Energy Booster & Metabolism Enhancer, 1000ml', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance Care Juice is a 100% natural Ayurvedic blend designed to support optimal thyroid function, hormonal balance, and metabolic health. Infused with potent herbs like Jatamansi, Guggulu, Gokshur, and Jyotishmati, it helps regulate thyroid hormone levels, boost energy, support mental clarity, reduce inflammation, and promote healthy skin, hair, and digestion. Ideal for those with thyroid imbalances or seeking overall hormonal wellness, this fast-absorbing liquid supplement is sustainably packaged and easy to integrate into daily routinesâ€”your natural path to balanced energy and endocrine health.\r\n', '1368.jpg', '', NULL, 'MN-C2100', '', 'Thyro Balance Care Juice'),
(34, 'My Nutrify Cholesterol Care juice:\nMy Nutrify Herbal & Ayurveda\'s Cholesterol Care Juice is a powerful blend of fresh-pressed raw garlic, zesty ginger, and refreshing lemon juices, expertly blended with organic apple cider vinegar and pure honey. Each component is specifically chosen for its individual heart-healthy properties, working in harmony to assist with cholesterol balancing and overall cardiovascular function. Take a daily sip to feed your heart and boost your well-being. \n\nMy Nutrify Diabetic Care Juice:\nMy Nutrify Herbal & Ayurveda\'s Diabetic Care Juice helps keep your blood sugar levels in check! Made with a powerful blend of 13 herbs and superfoods, this natural juice supports healthy blood sugar management. It\'s easy to drink and boosts overall health. Each batch is crafted using raw herbs sourced from trusted farms, following traditional Ayurvedic practices.', 1, 4, 'My Nutrify Herbal & Ayurveda\'s Cholesterol care Juice & Diabetic Care Juice combo', 'My Nutrify Herbal & Ayurveda\'s Cholesterol care Juice & Diabetic Care Juice combo', 'My Nutrify Herbal & Ayurveda\'s Cholesterol care Juice & Diabetic Care Juice combo', 'Cholesterol Care Juice: A natural blend of garlic, ginger, lemon, apple cider vinegar, and honey to help support a healthy heart and cholesterol levels.\r\nDiabetic Care Juice: A herbal juice with 13 powerful ingredients to help manage blood sugar and boost overall health.', '4348.jpg', '', NULL, 'MN-C1200', 'Y', 'Cholesterol care Juice & Diabetic Care Juice combo'),
(35, 'My Nutrify Herbal & Ayurveda\'s Diabetic Care Juice:\nMy Nutrify Diabetic Care Juice is a natural way to maintain healthy blood sugar levels. Made with 13 powerful herbs and superfoods, it supports overall wellness and balances blood sugar naturally. This pure Ayurvedic juice is crafted using traditional methods with raw herbs sourced from trusted Indian farms. It\'s easy to consume and offers additional health benefits for a healthier life!\n\nMy Nutrify Herbal & Ayurveda\'s BP Care Juice:\nMy Nutrify BP Care Juice is an Ayurvedic blend of 5 powerful herbs. It contains Sarpagandha and Rudraksh to regulate blood pressure, Bach for better metabolism, Jatamansi to manage high BP, and Sankhpushpi to boost immunity and energy.', 1, 4, 'My Nutrify Herbal & Ayurveda\'s Diabetic Care Juice | BP Care Juice combo', 'My Nutrify Herbal & Ayurveda\'s Diabetic Care Juice | BP Care Juice combo', 'My Nutrify Herbal & Ayurveda\'s Diabetic Care Juice | BP Care Juice combo', 'Diabetic Care Juice: A natural Ayurvedic blend of 13 herbs and superfoods that help maintain healthy blood sugar levels and overall wellness. Made using traditional methods with raw herbs from trusted Indian farms.\r\nBP Care Juice: A powerful Ayurvedic mix of 5 herbs, including Sarpagandha and Jatamansi, to help regulate blood pressure, support metabolism, and boost immunity.', '1204.jpg', '', NULL, ' MN-C2200', 'Y', 'Diabetic Care Juice | BP Care Juice combo'),
(36, 'BP Care Juice\nMy Nutrify Herbal & Ayurveda\'s BP Care Juice is a powerful Ayurvedic formulation crafted from a blend of 5 natural herbs. This blood pressure care juice contains the goodness of Sarpagandha and Rudraksh, which help regulate and manage blood pressure effectively. Bach works to enhance metabolism, while Jatamansi helps lower blood pressure and keep it within a healthy range. Additionally, Shankhpushpi supports immunity and rejuvenates the entire body for improved overall wellness.\nCholesterol Care Juice\n\nMy Nutrify Herbal & Ayurveda\'s Cholesterol Care Juice is apowerful herbal blend designed to support heart health and manage cholesterol naturally. Made with fresh garlic, ginger, lemon, apple cider vinegar, and honey, this juice helps reduce bad cholesterol (LDL), strengthen heart muscles, and improve blood circulation. It also aids in reducing stress, anxiety, and promoting overall cardiovascular wellness. Regular consumption can help maintain a healthy heart, boost vitality, and enhance overall well-being.', 1, 6, 'My Nutrify Herbal & Ayurveda\'s BP Care Juice | Cholesterol Care Juice combo', 'My Nutrify Herbal & Ayurveda\'s BP Care Juice | Cholesterol Care Juice combo', 'My Nutrify Herbal & Ayurveda\'s BP Care Juice | Cholesterol Care Juice combo', 'BP Care Juice: A natural Ayurvedic blend of 5 powerful herbs, including Sarpagandha and Jatamansi, to help regulate blood pressure, improve metabolism, and boost immunity for overall wellness.\r\n\r\nCholesterol Care Juice: A heart-friendly mix of garlic, ginger, lemon, apple cider vinegar, and honey to help manage cholesterol, support heart health, and improve blood circulation.', '6289.png', '', NULL, 'MN-C3200', 'Y', 'BP Care Juice | Cholesterol Care Juice combo'),
(37, 'Wild Amla Juice: Made from fresh wild Indian gooseberries, this vitamin C-rich juice boosts immunity, improves digestion, and promotes healthy skin and hair.\n\nShe Care Plus Juice: A herbal blend designed for women\'s wellness, supporting hormone balance, digestion, metabolism, and overall energy.', 1, 5, 'My Nutrify Herbal & Ayurveda\'s she care plus Juice | amla Juice combo', 'My Nutrify Herbal & Ayurveda\'s she care plus Juice | amla Juice combo', 'My Nutrify Herbal & Ayurveda\'s she care plus Juice | amla Juice combo', 'Wild Amla Juice: Made from fresh wild Indian gooseberries, this vitamin C-rich juice boosts immunity, improves digestion, and promotes healthy skin and hair.\r\n\r\nShe Care Plus Juice: A herbal blend designed for womenâ€™s wellness, supporting hormone balance, digestion, metabolism, and overall energy.', '3760.png', '', NULL, 'MN-C4200', 'Y', 'My Nutrify Herbal & Ayurveda\'s she care plus Juice | amla Juice combo'),
(38, '', 1, 2, 'My Nutrify Herbal & Ayurveda\'s Apple Cider Vinegar | Wheatgrass Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Apple Cider Vinegar | Wheatgrass Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Apple Cider Vinegar | Wheatgrass Juice Combo', 'Apple Cider Vinegar: Pure and unfiltered, this vinegar supports metabolism, weight management, and overall health with its rich vitamins and antioxidants.\r\n\r\nWheatgrass Juice: A natural detoxifier that improves digestion, boosts metabolism, and supports weight management while promoting gut health.', '1473.jpg', '', '', 'MN-C5200', 'Y', 'My Nutrify Herbal & Ayurveda\'s Apple Cider Vinegar | Wheatgrass Juice Combo'),
(39, 'My Nutrify Karela,Neem&Jamun :\nMy Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun Juice is made with a balanced blend of Karela, Neem, and Jamun, each offering unique health benefits. This 100% pure and preservative-free juice is especially beneficial for diabetic patients, as it helps regulate blood sugar levels. It also purifies the blood due to its antioxidant properties, enhances digestion, boosts immunity, and supports the healthy functioning of the pancreas and liver.\nMy Nutrify Diabetic Care Juice:\nMy Nutrify Herbal & Ayurveda\'s Diabetic Care Juice helps keep your blood sugar levels in check! Made with a powerful blendof 13 herbs and superfoods, this natural juice supports healthy blood sugar management. It\'s easy to drink and boosts overall health. Each batch is crafted using raw herbs sourced from trusted farms, following traditional Ayurvedic practices.', 1, 4, 'My Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun And Diabetic care Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun And Diabetic care Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun And Diabetic care Juice Combo', 'Karela Neem & Jamun Juice: A natural blend that helps regulate blood sugar, purify the blood, improve digestion, and support liver and pancreas health.\r\n\r\nDiabetic Care Juice: A powerful mix of 13 herbs and superfoods to support healthy blood sugar levels and overall well-being.', '8490.jpg', '', NULL, 'MN-C6200', 'Y', 'Karela, Neem & Jamun And Diabetic care Juice Combo'),
(40, 'My Nutrify Karela Neem & Jamun Juice:\nMy Nutrify Herbal & Ayurveda\'s Karela Neem & Jamun Juice is a natural blend of Karela, Neem, and Jamun. This 100% pure, preservative-free juice helps manage blood sugar, purify the blood, improve digestion, boost immunity, and support the pancreas and liver making it especially beneficial for diabetics.\n\nMy Nitrify bp care juice:\nMy Nutrify Herbal & Ayurveda\'s BP Care Juice is a natural Ayurvedic formulation crafted from a blend of 10 powerful herbs to support healthy blood pressure. It contains Sarpagandha and Rudraksh, known for their ability to regulate and manage blood pressure effectively. Bach helps enhance metabolism, while Jatamansi works to lower and stabilize blood pressure, keeping it within a healthy range. Additionally, Shankhpushpi strengthens immunity and revitalizes the body, promoting overall wellness. This herbal juice offers a holistic, natural solution for maintaining heart health and balanced well-being.', 1, 4, 'My Nutrify Herbal & Ayurveda\'s Karela neem & jamun juice and bp care juice Combo', 'My Nutrify Herbal & Ayurveda\'s Karela neem & jamun juice and bp care juice Combo', 'My Nutrify Herbal & Ayurveda\'s Karela neem & jamun juice and bp care juice Combo', 'Karela Neem & Jamun Juice: A pure, preservative-free blend that helps manage blood sugar, purify the blood, support digestion, and boost immunityâ€”ideal for diabetic care and overall wellness.\r\n\r\nBP Care Juice: A herbal mix of 10 Ayurvedic ingredients like Sarpagandha and Jatamansi to naturally manage blood pressure, boost metabolism, and strengthen immunity for a healthy heart and balanced body.', '4367.jpg', '', NULL, 'MN-C7200', 'Y', 'Karela neem & jamun juice and bp care juice Combo'),
(41, 'My Nutrify Wild Amla Juice:\nMy Nutrify Herbal & Ayurveda\'s Wild Amla Juice is made from fresh, ripe amla (gooseberries) to preserve its natural goodness. Rich in vitamin C, iron, and essential nutrients, it supports overall health by boosting immunity, strengthening bones, aiding weight management, and improving body function.\n\nMy Nutrify Apple Cider Vinegar:\nMy Nutrify Herbal & Ayurveda\'s Apple Cider Vinegar is a luxurious, unfiltered juice derived from the best apples. With high vitamin, mineral, and antioxidant content, it occurs naturally to aid digestion, stimulate metabolism, and induce fullness to help manage weight. Adopt this pure tonic as a gateway to greater vigor and overall health.\n', 1, 1, 'My Nutrify Herbal & Ayurveda\'s Wild Amla Juice  & Apple Cider Vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Wild Amla Juice  & Apple Cider Vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Wild Amla Juice  & Apple Cider Vinegar Juice Combo', 'Wild Amla Juice: Made from fresh amla, this vitamin C-rich juice boosts immunity, strengthens bones, aids in weight management, and supports overall health.\r\n\r\nApple Cider Vinegar: A pure, unfiltered tonic made from premium applesâ€”supports digestion, boosts metabolism, and helps with weight management naturally.', '5097.jpg', '', NULL, 'MN-C8200', 'Y', 'Wild Amla Juice  & Apple Cider Vinegar Juice Combo'),
(42, '\nMy Nutrify Wild Amla Juice:\nMy Nutrify Herbal & Ayurveda\'s Wild Amla Juice is made from freshly picked, fully ripe amla berries to retain their natural, pure goodness. Rich in vitamin C, iron, and essential nutrients, this powerful extract boosts immunity, promotes bone health, and helps with weight management giving nature\'s best gift for everyday wellness.\n\nMy Nutrify Wheatgrass Juice:\nMy Nutrify Herbal & Ayurvedas Wheatgrass Juice (1ltr) combine nature\'s nutrition with modern wellness. Rich in required vitamins, minerals, and antioxidants, this blend supports healthy hemoglobin levels, boosts energy, and enhances immunity. Ideal for daily use, it revitalizes your system for a naturally healthier, more energetic life.', 1, 2, 'My Nutrify Herbal & Ayurveda\'s Wild Amla Juice  & Wheatgrass Combo', 'My Nutrify Herbal & Ayurveda\'s Wild Amla Juice  & Wheatgrass Combo', 'My Nutrify Herbal & Ayurveda\'s Wild Amla Juice  & Wheatgrass Combo', 'Wild Amla Juice: Made from ripe, fresh amla berries, this juice is packed with vitamin C and nutrients to boost immunity, strengthen bones, and support weight management for daily wellness.\r\n\r\nWheatgrass Juice: A nutrient-rich blend that supports healthy hemoglobin, boosts energy, and strengthens immunityâ€”perfect for daily detox and overall vitality.', '6528.png', '', NULL, 'MN-C9200', 'Y', 'Wild Amla Juice  & Wheatgrass Combo'),
(43, 'My Nutrify Thyro Balance care Juice:\nMy Nutrify Herbal & Ayurveda Thyro Balance Juice is an Ayurvedic,natural tonic that is filled with vital vitamins and fiber. It soothingly calms stress and anxiety and lowers hypersensitivity, making it a great option for detox and cleansing. Take a sip to reset your body, calm your mind, and rebalance naturally.\n\nMy Nutrify Apple Cider Vinegar:\nMy Nutrify Herbal & Ayurveda Apple Cider Vinegar finest unfiltered juice made from high-quality apples. Filled with natural vitamins, minerals, and antioxidants, it nourishes digestion, increases metabolism, and maintains weight, adding to your well-being with each glass.', 1, 1, 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Apple Cider vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Apple Cider vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Apple Cider vinegar Juice Combo', 'Thyro Balance Care Juice: A calming Ayurvedic tonic rich in vitamins and fiber that helps reduce stress, support detox, and naturally rebalance the body.\r\n\r\nApple Cider Vinegar: Made from high-quality apples, this unfiltered vinegar supports digestion, boosts metabolism, and helps manage weight for better overall wellness.', '2698.jpg', '', NULL, 'MN-C10200', 'Y', 'Thyro Balance care juice & Apple Cider vinegar Juice Combo');
INSERT INTO `product_master` (`ProductId`, `Description`, `CategoryId`, `SubCategoryId`, `ProductName`, `MetaTags`, `MetaKeywords`, `ShortDescription`, `PhotoPath`, `Specification`, `VideoURL`, `ProductCode`, `IsCombo`, `Title`) VALUES
(44, 'My Nutrify Thyro Balance care Juice:\nMy Nutrify Herbal & Ayurveda’s Thyro Balance Juice is an Ayurvedic herbal tonic enriched with essential vitamins and fiber. It gently eases stress and anxiety while reducing sensitivity, making it an ideal choice for a natural detox and cleanse. Enjoy a sip to rejuvenate your body, soothe your mind, and restore harmony naturally.\n\nMy Nutrify Wild Amla Juice:\nMy Nutrify Herbal & Ayurveda\'s Wild Amla Juice is made from just-picked, completely ripe amla berries to maintain their pure natural integrity. Packed with vitamin C, iron, and other critical nutrients, this concentrated extract boosts a healthy immune system, fortifies bone structure, and facilitates weight control bringing nature\'s best gift for daily energy.', 1, 1, 'My Nutrify Herbal & Ayurveda\'s Thyro Balance  care juice & Wild Amla Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance  care juice & Wild Amla Juice Combo', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance  care juice & Wild Amla Juice Combo', 'Thyro Balance Care Juice: A soothing Ayurvedic blend with essential vitamins and fiber that helps reduce stress, support natural detox, and restore balance for overall well-being.\r\n\r\nWild Amla Juice: Made from freshly picked ripe amla, this nutrient-rich juice boosts immunity, strengthens bones, and aids in weight management perfect for daily vitality.', '6359.jpg', '', NULL, 'MN-C11200', 'Y', 'Thyro Balance  care juice & Wild Amla Juice Combo'),
(45, 'My Nutrify Thyro Balance care juice\nMy Nutrify Herbal & Ayurveda\'s Thyro Balance Juice, an Ayurvedic elixir that is supplemented with essential vitamins and fiber. This soothing tonic calms stress and anxiety, decreasing sensitivity as well, so it is ideal to have on hand for a natural cleanse and detox. Drink to revitalize your body, soothe your mind, and restore inner harmony naturally.\n\nMy Nutrify Karela neem & jamun juice\nMy Nutrify Herbal & Ayurveda\'s Karela Jamun Neem Juice is a wonderful combination of bitter Karela, juicy Jamun, and Neem. Made with 100% pure, preservative-free herbs, this natural juice helps regulate blood sugar levels, cleanse the blood, and improve digestion. Its immunity-boosting properties, combined with pancreatic and liver support, make it a beneficial tonic for diabetic management and general well-being.', 1, 4, 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Karela neem & jamun juice Combo', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Karela neem & jamun juice Combo', 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Karela neem & jamun juice Combo', 'Thyro Balance Care Juice: A natural Ayurvedic drink that helps reduce stress, supports detox, and brings balance to your body and mind.\r\n\r\nKarela Neem & Jamun Juice: A pure herbal blend that helps manage blood sugar, cleanses the blood, improves digestion, and supports overall health especially for diabetics.', '1518.jpg', '', NULL, 'MN-C12200', 'Y', 'Thyro Balance care juice & Karela neem & jamun juice Combo'),
(46, 'My Nutrify Karela neem & jamun juice:\nMy Nutrify Herbal & Ayurveda\'s Karela Jamun Neem Juice combines the unique properties of bitter Karela, juicy Jamun, and pungent Neem into a strong herbal tonic. Made from 100% pure, preservative-free herbs, it helps to regulate balanced blood sugar levels, cleanse the blood, and boost digestive health. With its strong immunity boost and positive impact on the pancreas and liver, this juice is a great tonic for diabetic management and overall health.\n\nMy Nutrify Cholesterol Care juice:\nMy Nutrify Herbal & Ayurveda\'s Cholesterol Care Juice is a powerful blend of fresh-pressed raw garlic, zesty ginger, and refreshing lemon juices, expertly blended with organic apple cider vinegar and pure honey. Each component is specifically chosen for its individual heart-healthy properties, working in harmony to assist with cholesterol balancing and overall cardiovascular function. Take a daily sip to feed your heart and boost your well-being.', 1, 4, 'My Nutrify Herbal & Ayurveda\'s Cholesterol Care juice & Karela neem & jamun juice Combo', 'My Nutrify Herbal & Ayurveda\'s Cholesterol Care juice & Karela neem & jamun juice Combo', 'My Nutrify Herbal & Ayurveda\'s Cholesterol Care juice & Karela neem & jamun juice Combo', 'Karela Neem & Jamun Juice: A natural blend of Karela, Jamun, and Neem that helps manage blood sugar, purify the blood, support digestion, and boost immunity ideal for diabetic care and overall wellness.\r\n\r\nCholesterol Care Juice: A heart-friendly mix of garlic, ginger, lemon, apple cider vinegar, and honey that helps balance cholesterol, support heart health, and improve overall cardiovascular wellness.\r\n', '3402.jpg', '', NULL, 'MN-C13200', 'Y', 'Cholesterol Care juice & Karela neem & jamun juice Combo'),
(47, 'My Nutrify She Care Plus Juice:\nMy Nutrify Herbal & Ayurveda\'s She Care Plus Juice is a simple Ayurvedic herbal drink that helps balance hormones for women. It supports regular menstrual cycles and a healthy reproductive system while reducing skin issues like pimples, acne, and blisters caused by hormonal changes.\n\nMy Nutrify Apple Cider Vinegar Juice :\nMyNutrify Herbal & Ayurveda’s Apple Cider Vinegar is a delectable, unfiltered juice made from the best apples, retaining nature\'s entire wealth. Every glass contains vital vitamins, minerals, and antioxidants that support easy digestion, increase metabolism, and assist in weight management—rendering it a tasty everyday pick-me-up for your well-being.', 1, 5, 'My Nutrify Herbal & Ayurveda\'s She Care Plus Juice & Apple Cider vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s She Care Plus Juice & Apple Cider vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s She Care Plus Juice & Apple Cider vinegar Juice Combo', 'She Care Plus Juice: An Ayurvedic juice for women that helps balance hormones, supports menstrual health, and reduces skin problems like acne and pimples caused by hormonal changes.\r\n\r\nApple Cider Vinegar Juice: A pure, unfiltered vinegar made from the best applesâ€”helps with digestion, boosts metabolism, and supports weight management for daily wellness.', '1494.jpg', '', NULL, 'MN-C14200', 'Y', 'She Care Plus Juice & Apple Cider vinegar Juice Combo'),
(49, 'My Nutrify BP Care Juice:\nMy Nutrify Herbal & Ayurveda\'s BP Care Juice is an Ayurvedic herbal synergistic blend designed for heart health. Bach increases metabolism, and Jatamansi and Sarpagandha combine to soothe the body and stabilize blood pressure. Sankhpushpi increases immunity, Arjuna fortifies the heart, and Brahmi reduces stress. These are supported by Rudraksh to support healthy pressure, Mint for digestion, Suddh Guggal for anti-inflammatory benefits, and Lahsun (garlic) for healthy cholesterol levels.\n\nMy Nutrify Apple Cider Vinegar Juice :\nMy Nutrify Herbal & Ayurveda\'s Apple Cider Vinegar is a delectable, unfiltered juice made from the best apples, retaining nature\'s entire wealth. Every glass contains vital vitamins, minerals, and antioxidants that support easy digestion, increase metabolism, and assist in weight management rendering it a tasty everyday pick-me-up for your well-being.', 1, 6, 'My Nutrify Herbal & Ayurveda\'s BP Care Juice &  Apple Cider vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s BP Care Juice &  Apple Cider vinegar Juice Combo', 'My Nutrify Herbal & Ayurveda\'s BP Care Juice &  Apple Cider vinegar Juice Combo', 'BP Care Juice: A powerful Ayurvedic blend of 10 herbs that helps manage blood pressure, boost heart health, improve metabolism, reduce stress, and support overall wellness.\r\n\r\nApple Cider Vinegar Juice: A pure, unfiltered vinegar made from premium apples supports digestion, boosts metabolism, and helps with weight management for daily vitality.', '7336.jpg', '', NULL, 'MN-C15200', 'Y', 'BP Care Juice &  Apple Cider vinegar Juice Combo');

-- --------------------------------------------------------

--
-- Table structure for table `product_offers`
--

CREATE TABLE `product_offers` (
  `offer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `offer_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional custom title for the offer',
  `offer_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Optional description for the offer',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1 = active offer, 0 = inactive',
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to manage which products are featured as special offers';

--
-- Dumping data for table `product_offers`
--

INSERT INTO `product_offers` (`offer_id`, `product_id`, `offer_title`, `offer_description`, `is_active`, `created_date`, `updated_date`) VALUES
(1, 19, NULL, NULL, 1, '2025-07-23 13:25:56', '2025-07-23 13:25:56');

-- --------------------------------------------------------

--
-- Table structure for table `product_price`
--

CREATE TABLE `product_price` (
  `PriceId` int NOT NULL,
  `ProductId` int DEFAULT NULL,
  `ProductCode` text COLLATE utf8mb4_general_ci,
  `Size` text COLLATE utf8mb4_general_ci,
  `OfferPrice` text COLLATE utf8mb4_general_ci,
  `MRP` text COLLATE utf8mb4_general_ci,
  `Coins` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_price`
--

INSERT INTO `product_price` (`PriceId`, `ProductId`, `ProductCode`, `Size`, `OfferPrice`, `MRP`, `Coins`) VALUES
(5, 2, NULL, '500 ml | Pack of 1', '200', '250', '10'),
(6, 2, NULL, '1000 ml | Pack of 1', '400', '500', '15'),
(7, 2, NULL, '1000 ml | Pack of 2', '750', '1000', '20'),
(8, 2, NULL, '500 ml | Pack of 2', '400', '500', '15'),
(9, 3, NULL, '500 ml | Pack of 1', '0', '0', '0'),
(10, 3, NULL, '1000 ml | Pack of 1', '225', '230', '6'),
(11, 3, NULL, '1000 ml | Pack of 2', '445', '460', '13'),
(12, 3, NULL, '500 ml | Pack of 2', '0', '0', '0'),
(13, 4, NULL, '30 caps | Pack of 1', '98', '110', '2'),
(14, 4, NULL, '60 caps | Pack of 1', '196', '220', '6'),
(15, 4, NULL, '60 caps | Pack of 2', '0', '0', '0'),
(16, 4, NULL, '90 caps | Pack of 1', '0', '0', '0'),
(17, 4, NULL, '90 caps | Pack of 2', '0', '0', '0'),
(18, 5, NULL, '20 g | Pack of 1', '699', '1499', '25'),
(19, 5, NULL, '1000 ml | Pack of 1', '500', '510', '15'),
(20, 5, NULL, '1000 ml | Pack of 2', '980', '1020', '29'),
(21, 5, NULL, '500 ml | Pack of 2', '0', '0', '0'),
(26, 7, 'MN-AM050', '500 ml | Pack of 1', '0', '0', '0'),
(27, 7, NULL, '1000 ml | Pack of 1', '640', '660', '15'),
(28, 7, NULL, '1000 ml | Pack of 2', '1240', '1320', '20'),
(29, 7, NULL, '500 ml | Pack of 2', '0', '0', '0'),
(30, 8, NULL, '500 ml | Pack of 1', '0', '0', '0'),
(31, 8, 'MN-BP100', '1000 ml | Pack of 1', '999', '1049', '20'),
(33, 8, NULL, '500 ml | Pack of 2', '0', '0', '0'),
(46, 12, NULL, '500 ml | Pack of 1', '0', '0', '0'),
(47, 12, 'MN-SC100', '1000 ml | Pack of 1', '499', '549', '20'),
(49, 12, NULL, '500 ml | Pack of 2', '0', '0', '0'),
(50, 13, NULL, '500 ml | Pack of 1', '0', '0', '0'),
(51, 13, 'MN-TB100', '1000 ml | Pack of 1', '499', '549', '20'),
(53, 13, NULL, '500 ml | Pack of 2', '0', '0', '0'),
(62, 16, NULL, '20 gm | Pack of 1', '1', '100', '20'),
(63, 16, NULL, '20 gm | Pack of 2', '0', '0', '0'),
(64, 16, NULL, '20 gm | Pack of 3', '0', '0', '0'),
(65, 17, NULL, '20 gm | Pack of 1', '899', '1849', '25'),
(66, 17, NULL, '20 gm | Pack of 2', '0', '0', '0'),
(67, 17, NULL, '20 gm | Pack of 3', '0', '0', '0'),
(68, 18, NULL, '20 gm | Pack of 1', '699', '1499', '25'),
(69, 18, NULL, '20 gm | Pack of 2', '0', '0', '0'),
(70, 18, NULL, '20 gm | Pack of 3', '0', '0', '0'),
(71, 19, NULL, '20 gm | Pack of 1', '499', '1199', '25'),
(72, 19, NULL, '20 gm | Pack of 2', '0', '0', '0'),
(73, 19, NULL, '20 gm | Pack of 3', '0', '0', '0'),
(79, 21, NULL, '20 gm | Pack of 1', '899', '1849', ''),
(80, 21, NULL, '20 gm | Pack of 2', '', '', ''),
(81, 21, NULL, '20 gm | Pack of 3', '', '', ''),
(90, 24, NULL, '500 ml | Pack of 1', '0', '0', '0'),
(91, 24, NULL, '1000 ml | Pack of 1', '0', '0', '0'),
(92, 24, NULL, '1000 ml | Pack of 2', '1148', '1248', '50'),
(93, 24, NULL, '500 ml | Pack of 2', '0', '0', '0'),
(94, 25, NULL, '500 ml | Pack of 1', '', '', ''),
(95, 25, NULL, '1000 ml | Pack of 1', '499.00', '549.00', '50'),
(96, 25, NULL, '1000 ml | Pack of 2', '', '', ''),
(97, 25, NULL, '500 ml | Pack of 2', '', '', ''),
(98, 26, NULL, '500 ml | Pack of 1', '', '', ''),
(99, 26, NULL, '1000 ml | Pack of 1', '', '', ''),
(100, 26, NULL, '1000 ml | Pack of 2', '1098', '1148', '50'),
(101, 26, NULL, '500 ml | Pack of 2', '', '', ''),
(102, 27, NULL, '500 ml | Pack of 1', '', '', ''),
(103, 27, NULL, '1000 ml | Pack of 1', '', '', ''),
(104, 27, NULL, '1000 ml | Pack of 2', '', '', '50'),
(105, 27, NULL, '500 ml | Pack of 2', '', '', ''),
(106, 28, NULL, '500 ml | Pack of 1', '', '', ''),
(107, 28, NULL, '1000 ml | Pack of 1', '', '', ''),
(108, 28, NULL, '1000 ml | Pack of 2', '', '', ''),
(109, 28, NULL, '500 ml | Pack of 2', '', '', ''),
(110, 29, NULL, '500 ml | Pack of 1', '', '', ''),
(111, 29, NULL, '1000 ml | Pack of 1', '', '', ''),
(112, 29, NULL, '1000 ml | Pack of 2', '698', '748', '50'),
(113, 29, NULL, '500 ml | Pack of 2', '', '', ''),
(114, 30, NULL, '500 ml | Pack of 1', '', '', ''),
(115, 30, NULL, '1000 ml | Pack of 1', '', '', ''),
(116, 30, NULL, '1000 ml | Pack of 2', '1148', '1198', '50'),
(117, 30, NULL, '500 ml | Pack of 2', '', '', ''),
(118, 31, NULL, '500 ml | Pack of 1', '', '', ''),
(119, 31, NULL, '1000 ml | Pack of 1', '', '', ''),
(120, 31, NULL, '1000 ml | Pack of 2', '1447', '1497', '50'),
(121, 31, NULL, '500 ml | Pack of 2', '', '', ''),
(122, 32, NULL, '500 ml | Pack of 1', '', '', ''),
(123, 32, NULL, '1000 ml | Pack of 1', '', '', ''),
(124, 32, NULL, '1000 ml | Pack of 2', '1298', '1348', ''),
(125, 32, NULL, '500 ml | Pack of 2', '', '', ''),
(126, 33, NULL, '500 ml | Pack of 1', '', '', ''),
(127, 33, NULL, '1000 ml | Pack of 1', '', '', ''),
(128, 33, NULL, '1000 ml | Pack of 2', '648', '698', '50'),
(129, 33, NULL, '500 ml | Pack of 2', '', '', ''),
(130, 34, NULL, '500 ml | Pack of 1', '', '', ''),
(131, 34, NULL, '1000 ml | Pack of 1', '', '', ''),
(132, 34, NULL, '1000 ml | Pack of 2', '1098', '1248', '50'),
(133, 34, NULL, '500 ml | Pack of 2', '', '', ''),
(134, 35, NULL, '500 ml | Pack of 1', '', '', ''),
(135, 35, NULL, '1000 ml | Pack of 1', '', '', ''),
(136, 35, NULL, '1000 ml | Pack of 2', '1498', '1648', '50'),
(137, 35, NULL, '500 ml | Pack of 2', '', '', ''),
(138, 36, NULL, '500 ml | Pack of 1', '', '', ''),
(139, 36, NULL, '1000 ml | Pack of 1', '', '', ''),
(140, 36, NULL, '1000 ml | Pack of 2', '1598', '1748', '50'),
(141, 36, NULL, '500 ml | Pack of 2', '', '', ''),
(142, 37, NULL, '500 ml | Pack of 1', '', '', ''),
(143, 37, NULL, '1000 ml | Pack of 1', '', '', ''),
(144, 37, NULL, '1000 ml | Pack of 2', '798', '948', '50'),
(145, 37, NULL, '500 ml | Pack of 2', '', '', ''),
(150, 39, NULL, '500 ml | Pack of 1', '', '', ''),
(151, 39, NULL, '1000 ml | Pack of 1', '', '', ''),
(152, 39, NULL, '1000 ml | Pack of 2', '848', '998', '50'),
(153, 39, NULL, '500 ml | Pack of 2', '', '', ''),
(154, 40, NULL, '500 ml | Pack of 1', '', '', ''),
(155, 40, NULL, '1000 ml | Pack of 1', '', '', ''),
(156, 40, NULL, '1000 ml | Pack of 2', '1298', '1448', '50'),
(157, 40, NULL, '500 ml | Pack of 2', '', '', ''),
(158, 41, NULL, '500 ml | Pack of 1', '', '', ''),
(159, 41, NULL, '1000 ml | Pack of 1', '', '', ''),
(160, 41, NULL, '1000 ml | Pack of 2', '948', '1098', '50'),
(161, 41, NULL, '500 ml | Pack of 2', '', '', ''),
(162, 42, NULL, '500 ml | Pack of 1', '', '', ''),
(163, 42, NULL, '1000 ml | Pack of 1', '', '', ''),
(164, 42, NULL, '1000 ml | Pack of 2', '648', '798', '50'),
(165, 42, NULL, '500 ml | Pack of 2', '', '', ''),
(166, 43, NULL, '500 ml | Pack of 1', '', '', ''),
(167, 43, NULL, '1000 ml | Pack of 1', '', '', ''),
(168, 43, NULL, '1000 ml | Pack of 2', '1198', '1348', '50'),
(169, 43, NULL, '500 ml | Pack of 2', '', '', ''),
(170, 44, NULL, '500 ml | Pack of 1', '', '', ''),
(171, 44, NULL, '1000 ml | Pack of 1', '', '', ''),
(172, 44, NULL, '1000 ml | Pack of 2', '698', '848', '50'),
(173, 44, NULL, '500 ml | Pack of 2', '', '', ''),
(174, 45, NULL, '500 ml | Pack of 1', '', '', ''),
(175, 45, NULL, '1000 ml | Pack of 1', '', '', ''),
(176, 45, NULL, '1000 ml | Pack of 2', '798', '948', '50'),
(177, 45, NULL, '500 ml | Pack of 2', '', '', ''),
(178, 46, NULL, '500 ml | Pack of 1', '', '', ''),
(179, 46, NULL, '1000 ml | Pack of 1', '', '', ''),
(180, 46, NULL, '1000 ml | Pack of 2', '898', '1048', '50'),
(181, 46, NULL, '500 ml | Pack of 2', '', '', ''),
(182, 47, NULL, '500 ml | Pack of 1', '', '', ''),
(183, 47, NULL, '1000 ml | Pack of 1', '', '', ''),
(184, 47, NULL, '1000 ml | Pack of 2', '1248', '1398', '50'),
(185, 47, NULL, '500 ml | Pack of 2', '', '', ''),
(186, 48, NULL, '500 ml | Pack of 1', '', '', ''),
(187, 48, NULL, '1000 ml | Pack of 1', '', '', ''),
(188, 48, NULL, '1000 ml | Pack of 2', '1198', '1348', '50'),
(189, 48, NULL, '500 ml | Pack of 2', '', '', ''),
(190, 49, NULL, '500 ml | Pack of 1', '', '', ''),
(191, 49, NULL, '1000 ml | Pack of 1', '', '', ''),
(192, 49, NULL, '1000 ml | Pack of 2', '1698', '1848', '50'),
(193, 49, NULL, '500 ml | Pack of 2', '', '', ''),
(194, 52, NULL, '500 ml | Pack of 1', '2', '', ''),
(195, 52, NULL, '1000 ml | Pack of 1', '', '', ''),
(196, 52, NULL, '1000 ml | Pack of 2', '', '', ''),
(197, 52, NULL, '500 ml | Pack of 2', '', '', ''),
(198, 50, NULL, '500 ml | Pack of 1', '2.00', '100', ''),
(199, 50, NULL, '1000 ml | Pack of 1', '', '', ''),
(200, 50, NULL, '1000 ml | Pack of 2', '', '', ''),
(201, 50, NULL, '500 ml | Pack of 2', '', '', ''),
(214, 9, NULL, '500 ml | Pack of 1', '', '', ''),
(215, 9, NULL, '1000 ml | Pack of 1', '599', '649', '20'),
(216, 9, NULL, '1000 ml | Pack of 2', '', '', ''),
(217, 9, NULL, '500 ml | Pack of 2', '', '', ''),
(230, 11, NULL, '500 ml | Pack of 1', '', '', ''),
(231, 11, NULL, '1000 ml | Pack of 1', '349', '399', '20'),
(232, 11, NULL, '1000 ml | Pack of 2', '', '', ''),
(233, 11, NULL, '500 ml | Pack of 2', '', '', ''),
(234, 22, NULL, '500 ml | Pack of 1', '', '', ''),
(235, 22, NULL, '1000 ml | Pack of 1', '999', '1049', ''),
(236, 22, NULL, '1000 ml | Pack of 2', '', '', ''),
(237, 22, NULL, '500 ml | Pack of 2', '', '', ''),
(238, 14, NULL, '500 ml | Pack of 1', '', '', ''),
(239, 14, NULL, '1000 ml | Pack of 1', '449', '499', '20'),
(240, 14, NULL, '1000 ml | Pack of 2', '', '', ''),
(241, 14, NULL, '500 ml | Pack of 2', '', '', ''),
(254, 6, NULL, '500 ml | Pack of 1', '', '', ''),
(255, 6, NULL, '1000 ml | Pack of 1', '249', '299', '10'),
(256, 6, NULL, '1000 ml | Pack of 2', '', '', ''),
(257, 6, NULL, '500 ml | Pack of 2', '', '', ''),
(258, 15, NULL, '500 ml | Pack of 1', '', '', ''),
(259, 15, NULL, '1000 ml | Pack of 1', '599', '649', '20'),
(260, 15, NULL, '1000 ml | Pack of 2', '', '', ''),
(261, 15, NULL, '500 ml | Pack of 2', '', '', ''),
(266, 23, NULL, '500 ml | Pack of 1', '', '', ''),
(267, 23, NULL, '1000 ml | Pack of 1', '549', '550', '25'),
(268, 23, NULL, '1000 ml | Pack of 2', '', '', ''),
(269, 23, NULL, '500 ml | Pack of 2', '', '', ''),
(270, 10, NULL, '500 ml | Pack of 1', '', '', ''),
(271, 10, NULL, '1000 ml | Pack of 1', '549', '599', '20'),
(272, 10, NULL, '1000 ml | Pack of 2', '', '', ''),
(273, 10, NULL, '500 ml | Pack of 2', '', '', ''),
(274, 38, NULL, '500 ml | Pack of 1', '', '', ''),
(275, 38, NULL, '1000 ml | Pack of 1', '', '', ''),
(276, 38, NULL, '1000 ml | Pack of 2', '648', '798', '50'),
(277, 38, NULL, '500 ml | Pack of 2', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `product_review`
--

CREATE TABLE `product_review` (
  `Product_ReviewId` int NOT NULL,
  `ProductId` int NOT NULL,
  `PhotoPath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_review`
--

INSERT INTO `product_review` (`Product_ReviewId`, `ProductId`, `PhotoPath`, `Name`, `Review`, `Date`) VALUES
(7, 23, '90482.jpg', 'Anita Sharma', 'Mere periods irregular the, par ye product use karne ke 3  weeks ke andar hi cycle regular ho gaya. Ye hormonal balance Ye hormonal balancemaintain karne me bhi madad karta hai.   Bahut accha product  hai, highly recommended!', '2024-01-05'),
(8, 23, '12716.jpeg', 'Priya Verma', 'Ye ek amazing product hai! Mera health improve ho gaya aur energy bhi badh gayi. Ab monthly cycle bhi sahi se ho raha hai. Bilkul worth it!\r\n\r\n', '2024-02-09'),
(9, 23, '27815.jpeg', 'Rashmi Patel', 'Product acha hai, par regular 3-4 mahine tak use karna padta hai proper result ke liye. Packaging bhi achi hai aur taste theek hai.\r\n\r\n', '2024-01-07'),
(10, 23, '', 'Swati Mehra', 'Mujhe PCOS hai aur ye juice use karne ke baad bahut improvement mehsoos kiya. Mere acne aur mood swings bhi control me aa gaye hain. Must try!\r\n\r\n', '2024-02-16'),
(11, 23, '', 'Neha Agarwal', 'Effect dikhne me thoda time lagta hai, par agar regular use kare to fayda hota hai. Natural aur safe product hai.\r\n', '2024-01-10'),
(12, 23, '46174.jpg', 'pooja Choudhary', 'Isko maine 1 mahine tak use kiya aur mera hormonal imbalance solve ho gaya. Bahut hi accha juice hai, koi side effect nahi hai.\r\n\r\n', '2024-02-15'),
(13, 6, '', 'Nitin Kumar', 'This juice very nice. After drinking feel more fresh and active.\r\n', '2024-04-25'),
(14, 6, '', 'Priyadharshini Rao', 'Good quality. Taste little strong but very natural. My digestion better now.\r\n\r\n', '2024-05-30'),
(15, 6, '', 'Prashant Jaat', 'Excellent amla juice. Every morning I drink, feel good.\r\n', '2024-06-05'),
(16, 6, '', 'shaan kapur', 'Before my energy low, now after using this I feel strong.', '2024-07-10'),
(17, 6, '', 'Pankaj Verma', 'Nice product. My skin glow and hair also strong now.', '2024-08-15'),
(18, 6, '', 'Sunita Reddy ', 'Very good for health. Stomach problem gone after drink\r\ndaily.\r\n', '2024-09-20'),
(19, 9, '', 'N. Ramanathan', 'This product is great for maintaining cholesterol levels. I feel healthier after using it.\r\n\r\n', '2024-10-25'),
(20, 9, '', 'Kamal Kishore Negi', 'I have been using this for a few months, and my cholesterol levels have improved.\r\n\r\n', '2024-06-30'),
(21, 9, '', ' Ramanpreet Kaur', 'Very effective for heart health. I feel more active and energetic.\r\n', '2024-07-11'),
(22, 9, '', 'Ambernath Sharma', '\r\nGood product, helps in maintaining a healthy lifestyle.\r\n', '2024-08-22'),
(23, 9, '', ' Pankaj Verma', 'Amazing supplement! My cholesterol levels are under control now.\r\n', '2024-09-03'),
(24, 9, '', 'Sunita Reddy', '\r\nNatural and effective. I feel much better after regular\r\nuse.\r\n', '2024-10-14'),
(25, 10, '', 'N. Ramanathan', 'Effective product, noticed improvement in my sugar levels after a month. Will continue using for better results.\r\n', '2024-11-25'),
(26, 10, '', 'Kamal Kishore Negi', ' Good for diabetic patients, my glucose levels are under control. Happy with the natural formula and long-term benefits. \r\n\r\n\r\n\r\n\r\n', '2025-01-17'),
(27, 10, '', 'Deepak', '\r\nPure ayurvedic product, feels good for overall health. Highly recommended for those looking for natural remedies.\r\n\r\n', '2025-02-28'),
(28, 10, '', 'Amit Verma', '\r\nQuality product, no side effects, and helps in maintaining energy levels throughout the day. Really satisfied with my purchase.\r\n\r\n\r\n', '2025-03-11'),
(29, 10, '', 'Suresh Mehta', 'Took time but effective, my sugar levels are stable now. Good quality and trusted herbal formula for long-term use. \r\n\r\n', '2025-04-23'),
(30, 10, '', 'Pooja Sharma', '\r\nRefreshing and natural, feels light and detoxifying. My digestion and energy levels have improved after regular use.\r\n \r\n', '2025-05-04'),
(31, 11, '', 'Gogi Sharma', 'I bought this for my mother, and she has been using it regularly. It\'s excellent for managing high blood pressure and sugar levels. A great herbal remedy!\r\n \r\n\r\n\r\n', '2025-06-15'),
(32, 11, '', ' Aman Bhaskar', 'Been using this Karela Neem Jamun juice for a long time. It\'s effective and value for money. Helps with overall health and sugar control.\r\n\r\n', '2025-07-26'),
(33, 11, '', ' Ananya Choudhary', 'A very nice product! My mother drinks this daily for diabetes management and has noticed improvements. Looking forward to long-term benefits.\r\n\r\n\r\n', '2025-08-07'),
(34, 11, '', 'Sumit Gupta', 'A helpful herbal medicine for keeping sugar levels in check. Worth trying for those looking for natural alternatives.\r\n\r\n\r\n', '2025-09-18'),
(35, 11, '', 'Swami Dhyan Raj', 'This juice is pure and beneficial. It’s a great addition to my daily routine for better health.\r\n\r\n\r\n', '2025-10-30'),
(36, 11, '', 'Rahul Verma', 'A very effective product at a reasonable price. I have been using it for months and can feel the difference in my sugar levels.\r\n\r\n\r\n\r\n', '2025-11-19'),
(37, 14, '', 'Rohit Shashtri', '\r\nVery good juice. After drink daily, feel more fresh and\r\nactive.\r\n', '2025-12-31'),
(38, 14, '', 'Sneha Verma', '\r\nNice product. Taste little strong but good for health. My digestion better now.\r\n', '2024-01-09'),
(39, 14, '', ' Amit Yadav', '\r\nExcellent wheatgrass juice. After use, my energy level improved.\r\n', '2024-02-21'),
(40, 14, '', 'Kavita Rani', '\r\nBefore always feel tired, now after drinking this, feel\r\nmore strong.\r\n', '2024-03-13'),
(41, 14, '', 'Suresh Nair', '\r\nVery healthy. My skin glow and stomach feel light.', '2024-04-25'),
(42, 14, '', 'Pooja Mishra', '\r\nGood for digestion. After daily drink, my stomach\r\nproblem gone.\r\n', '2024-05-06'),
(43, 15, '', ' Sanjay Patil', '\r\nVery good apple cider vinegar. After drinking daily,\r\nfeel fresh and active.\r\n', '2024-06-17'),
(44, 15, '', 'Meena Kapoor', '\r\nNice product. Taste little strong but very healthy. My digestion better now.\r\n\r\n', '2024-09-01'),
(45, 15, '', ' Rakesh Sharma', '\r\nExcellent apple cider vinegar. My metabolism improved after use.\r\n\r\n', '2024-10-12'),
(46, 15, '', ' Poonam Yadav', '\r\nBefore always feel bloated, now after drinking this, feel light and energetic.\r\n\r\n', '2024-11-24'),
(47, 15, '', 'Arvind Kumar', '\r\nVery healthy. My skin glow and stomach feel better.\r\n', '2024-12-05'),
(48, 15, '', 'Sushma Nair', 'Good for digestion. After daily drink, my acidity problem gone.\r\n\r\n', '2025-01-16'),
(49, 25, '', 'Priyadharshini Rao ', 'My TSH dropped from 9.25 to 6.75 in 35 days. Very effective!\r\n', '2025-02-27'),
(50, 25, '', 'Sunita Reddy ', 'I feel healthier and more energetic. Great product!\r\n', '2025-03-10'),
(51, 25, '', 'Priya Malhotra ', 'Best natural remedy for thyroid care. My energy improved.\r\n\r\n', '2025-04-21'),
(52, 25, '', 'Neha Bansal ', 'Good quality. Helped my metabolism and overall health.\r\n', '2025-05-02'),
(53, 25, '', 'Sneha Iyer ', 'Works naturally with no side effects. Love it!\r\n', '2025-06-13'),
(54, 25, '', 'Anjali Deshmukh', ' My thyroid levels are stable after two months. Very happy!\r\n\r\n', '2025-07-25'),
(55, 22, '', 'Nitin Kumar', 'Good product. After drink daily, my BP level more stable.\r\n', '2025-09-17'),
(56, 22, '', ' Priyadharshini Rao', 'Nice juice. Taste little strong, but working good for BP control.\r\n', '2025-10-29'),
(57, 22, '', 'Prashant Jaat', 'Excellent juice! My BP was high before, now much better.\r\n', '2025-11-10'),
(58, 22, '', 'Shaan Kapoor', 'Before my BP always up-down, now after using this, feel more normal.\r\n\r\n', '2025-12-22'),
(59, 22, '', 'Pankaj Verma', 'Very good for BP and heart health. Feel relaxed after drink.\r\n\r\n', '2025-06-10'),
(60, 22, '', 'Sunita Reddy', 'Daily drinking this juice, BP under control. Feel very light and fresh.\r\n\r\n', '2024-08-15');

-- --------------------------------------------------------

--
-- Table structure for table `product_subcategories`
--

CREATE TABLE `product_subcategories` (
  `id` int NOT NULL,
  `ProductId` int NOT NULL,
  `SubCategoryId` int NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0' COMMENT 'Indicates the primary/main subcategory',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_subcategories`
--

INSERT INTO `product_subcategories` (`id`, `ProductId`, `SubCategoryId`, `is_primary`, `created_at`, `updated_at`) VALUES
(7, 18, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(8, 19, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(9, 21, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(12, 25, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(13, 34, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(14, 35, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(15, 36, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(16, 37, 5, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(18, 39, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(19, 40, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(20, 41, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(21, 42, 2, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(22, 43, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(23, 44, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(24, 45, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(25, 46, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(26, 47, 5, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(27, 49, 1, 1, '2025-07-11 06:07:45', '2025-07-11 06:07:45'),
(35, 9, 1, 1, '2025-07-11 06:27:53', '2025-07-11 06:27:53'),
(36, 9, 4, 0, '2025-07-11 06:27:53', '2025-07-11 06:27:53'),
(43, 11, 1, 1, '2025-07-11 06:49:20', '2025-07-11 06:49:20'),
(44, 11, 4, 0, '2025-07-11 06:49:20', '2025-07-11 06:49:20'),
(45, 22, 1, 1, '2025-07-11 06:52:43', '2025-07-11 06:52:43'),
(46, 22, 6, 0, '2025-07-11 06:52:43', '2025-07-11 06:52:43'),
(47, 14, 1, 1, '2025-07-11 06:54:52', '2025-07-11 06:54:52'),
(48, 14, 2, 0, '2025-07-11 06:54:52', '2025-07-11 06:54:52'),
(82, 6, 1, 1, '2025-07-11 09:14:50', '2025-07-11 09:14:50'),
(83, 6, 2, 0, '2025-07-11 09:14:50', '2025-07-11 09:14:50'),
(84, 6, 5, 0, '2025-07-11 09:14:50', '2025-07-11 09:14:50'),
(85, 15, 1, 1, '2025-07-11 09:15:31', '2025-07-11 09:15:31'),
(86, 15, 2, 0, '2025-07-11 09:15:31', '2025-07-11 09:15:31'),
(87, 15, 3, 0, '2025-07-11 09:15:31', '2025-07-11 09:15:31'),
(88, 15, 5, 0, '2025-07-11 09:15:31', '2025-07-11 09:15:31'),
(91, 23, 1, 1, '2025-07-11 09:22:14', '2025-07-11 09:22:14'),
(92, 23, 5, 0, '2025-07-11 09:22:14', '2025-07-11 09:22:14'),
(93, 10, 1, 1, '2025-07-11 10:23:11', '2025-07-11 10:23:11'),
(94, 10, 4, 0, '2025-07-11 10:23:11', '2025-07-11 10:23:11'),
(95, 35, 4, 1, '2025-07-23 08:35:12', '2025-07-23 08:35:12'),
(96, 36, 6, 1, '2025-07-23 08:35:12', '2025-07-23 08:35:12'),
(97, 39, 4, 1, '2025-07-23 08:35:12', '2025-07-23 08:35:12'),
(98, 40, 4, 1, '2025-07-23 08:35:12', '2025-07-23 08:35:12'),
(99, 45, 4, 1, '2025-07-23 08:35:12', '2025-07-23 08:35:12'),
(100, 46, 4, 1, '2025-07-23 08:35:12', '2025-07-23 08:35:12'),
(101, 49, 6, 1, '2025-07-23 08:35:12', '2025-07-23 08:35:12'),
(102, 38, 2, 1, '2025-07-23 08:46:58', '2025-07-23 08:46:58'),
(103, 38, 3, 0, '2025-07-23 08:46:58', '2025-07-23 08:46:58');

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_with_subcategories`
-- (See below for the actual view)
--
CREATE TABLE `product_with_subcategories` (
`CategoryId` int
,`Description` text
,`IsCombo` text
,`MetaKeywords` text
,`MetaTags` text
,`PhotoPath` text
,`PrimarySubCategoryId` bigint
,`ProductCode` text
,`ProductId` int
,`ProductName` text
,`ShortDescription` text
,`Specification` text
,`SubCategoryIds` text
,`SubCategoryNames` text
,`Title` varchar(255)
,`VideoURL` text
);

-- --------------------------------------------------------

--
-- Table structure for table `promo_leads`
--

CREATE TABLE `promo_leads` (
  `id` int NOT NULL,
  `mobile_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_generated_at` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0' COMMENT '0=not verified, 1=verified',
  `verified_at` timestamp NULL DEFAULT NULL,
  `promo_code_used` tinyint(1) DEFAULT '0' COMMENT '0=not used, 1=used',
  `promo_code_used_at` timestamp NULL DEFAULT NULL,
  `source` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'promotional_popup' COMMENT 'Source of the lead',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Store promotional popup leads and OTP verification data';

--
-- Dumping data for table `promo_leads`
--

INSERT INTO `promo_leads` (`id`, `mobile_number`, `otp`, `otp_generated_at`, `is_verified`, `verified_at`, `promo_code_used`, `promo_code_used_at`, `source`, `created_at`, `updated_at`) VALUES
(2, '8208593432', '838897', '2025-07-23 05:52:04', 1, '2025-07-23 05:52:23', 0, NULL, 'promotional_popup', '2025-07-18 12:17:52', '2025-07-23 05:52:23'),
(3, '9503046790', '764585', '2025-07-23 12:06:18', 1, '2025-07-23 12:07:34', 0, NULL, 'promotional_popup', '2025-07-23 12:06:18', '2025-07-23 12:07:34');

-- --------------------------------------------------------

--
-- Table structure for table `rewards_catalog`
--

CREATE TABLE `rewards_catalog` (
  `id` int NOT NULL,
  `reward_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reward_description` text COLLATE utf8mb4_unicode_ci,
  `points_required` int NOT NULL,
  `reward_type` enum('coupon','discount','freebie') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'coupon',
  `reward_value` decimal(10,2) NOT NULL,
  `minimum_order_amount` decimal(10,2) DEFAULT '0.00',
  `max_redemptions_per_customer` int DEFAULT '1',
  `total_redemptions_limit` int DEFAULT NULL,
  `current_redemptions` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `terms_conditions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rewards_catalog`
--

INSERT INTO `rewards_catalog` (`id`, `reward_name`, `reward_description`, `points_required`, `reward_type`, `reward_value`, `minimum_order_amount`, `max_redemptions_per_customer`, `total_redemptions_limit`, `current_redemptions`, `is_active`, `valid_from`, `valid_until`, `terms_conditions`, `created_at`, `updated_at`) VALUES
(1, '₹25 Discount Coupon', 'Get ₹25 off on your next order', 250, 'coupon', 25.00, 500.00, 1, NULL, 0, 1, NULL, NULL, NULL, '2025-07-19 10:26:27', '2025-07-19 10:26:27'),
(2, '₹50 Discount Coupon', 'Get ₹50 off on your next order', 500, 'coupon', 50.00, 1000.00, 1, NULL, 0, 1, NULL, NULL, NULL, '2025-07-19 10:26:27', '2025-07-19 10:26:27'),
(3, '₹100 Discount Coupon', 'Get ₹100 off on your next order', 1000, 'coupon', 100.00, 2000.00, 1, NULL, 0, 1, NULL, NULL, NULL, '2025-07-19 10:26:27', '2025-07-19 11:22:58'),
(4, 'Free Shipping', 'Free shipping on your next order', 150, 'discount', 50.00, 0.00, 2, NULL, 0, 1, NULL, NULL, NULL, '2025-07-19 10:26:27', '2025-07-19 10:26:27'),
(5, '₹50 Off Coupon', 'Get ₹50 discount on your next order', 500, 'discount', 50.00, 500.00, 1, NULL, 0, 1, NULL, NULL, 'Valid for 30 days. Minimum order value ₹500.', '2025-07-20 05:48:05', '2025-07-20 05:48:05'),
(6, '₹100 Off Coupon', 'Get ₹100 discount on your next order', 1000, 'discount', 100.00, 1000.00, 1, NULL, 0, 1, NULL, NULL, 'Valid for 30 days. Minimum order value ₹1000.', '2025-07-20 05:48:05', '2025-07-20 05:48:05'),
(7, '₹200 Off Coupon', 'Get ₹200 discount on your next order', 2000, 'discount', 200.00, 1500.00, 1, NULL, 0, 1, NULL, NULL, 'Valid for 30 days. Minimum order value ₹1500.', '2025-07-20 05:48:05', '2025-07-20 05:48:05'),
(8, 'Free Shipping', 'Free shipping on your next order', 300, 'discount', 50.00, 0.00, 1, NULL, 0, 1, NULL, NULL, 'Valid for 30 days. Applicable on all orders.', '2025-07-20 05:48:05', '2025-07-20 05:48:05'),
(9, '₹200 Discount Coupon', NULL, 2000, 'discount', 200.00, 0.00, 1, NULL, 0, 1, NULL, NULL, NULL, '2025-07-20 07:04:28', '2025-07-20 07:04:28');

-- --------------------------------------------------------

--
-- Table structure for table `reward_redemptions`
--

CREATE TABLE `reward_redemptions` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `reward_id` int NOT NULL,
  `points_used` int NOT NULL,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'completed',
  `redeemed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `used_at` timestamp NULL DEFAULT NULL,
  `order_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `search_analytics`
--

CREATE TABLE `search_analytics` (
  `id` int NOT NULL,
  `visitor_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int DEFAULT NULL,
  `search_query` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `search_type` enum('product','category','general') COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `results_count` int DEFAULT '0',
  `clicked_result_position` int DEFAULT NULL COMMENT 'Position of clicked result (1-based)',
  `clicked_product_id` int DEFAULT NULL,
  `page_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `searched_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track search queries and interactions';

-- --------------------------------------------------------

--
-- Table structure for table `shipping_config`
--

CREATE TABLE `shipping_config` (
  `id` int NOT NULL,
  `config_key` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `config_value` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `provider` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_config`
--

INSERT INTO `shipping_config` (`id`, `config_key`, `config_value`, `is_active`, `provider`, `created_at`, `updated_at`) VALUES
(13, 'pickup_location', 'Primary', 1, 'shiprocket', '2025-06-28 13:48:42', '2025-06-28 13:48:42'),
(14, 'default_weight', '0.5', 1, 'shiprocket', '2025-06-28 13:48:42', '2025-06-28 13:48:42'),
(15, 'default_dimensions', '{\"length\":\"10\",\"width\":\"10\",\"height\":\"10\"}', 1, 'delhivery', '2025-06-28 13:48:42', '2025-07-09 07:31:18'),
(16, 'default_delivery_provider', 'shiprocket', 1, 'general', '2025-06-28 13:48:42', '2025-06-28 13:48:42'),
(17, 'enable_fallback', 'yes', 1, 'general', '2025-06-28 13:48:42', '2025-06-28 13:48:42'),
(18, 'auto_assign_delivery', 'yes', 1, 'general', '2025-06-28 13:48:42', '2025-06-28 13:48:42'),
(19, 'delivery_notification_email', '', 1, 'general', '2025-06-28 13:48:42', '2025-06-28 13:48:42'),
(154, 'auto_create_shipments', '1', 1, 'delhivery', '2025-06-29 10:08:29', '2025-07-07 15:01:34'),
(155, 'default_package_weight', '0.5', 1, 'delhivery', '2025-06-29 10:08:29', '2025-07-07 15:01:34'),
(175, 'default_shipping_mode', 'Surface', 1, 'general', '2025-07-02 11:42:46', '2025-07-02 11:42:46'),
(204, 'delhivery_api_key', '56a30b2a709d35b625006ac3b12b77e582249ea7', 1, 'delhivery', '2025-07-02 11:52:35', '2025-07-09 07:53:39'),
(205, 'delhivery_client_name', 'Pure Nutrition Co', 1, 'delhivery', '2025-07-02 11:52:35', '2025-07-07 15:01:34'),
(206, 'delhivery_return_address', 'Pure Nutrition Co, Business Address', 1, 'delhivery', '2025-07-02 11:52:35', '2025-07-07 15:01:34'),
(207, 'delhivery_return_city', 'Mumbai', 1, 'delhivery', '2025-07-02 11:52:35', '2025-07-02 11:52:35'),
(208, 'delhivery_return_state', 'Maharashtra', 1, 'delhivery', '2025-07-02 11:52:35', '2025-07-02 11:52:35'),
(209, 'delhivery_return_pincode', '400001', 1, 'delhivery', '2025-07-02 11:52:35', '2025-07-02 11:52:35'),
(216, 'delhivery_return_phone', '9876543210', 1, 'delhivery', '2025-07-04 12:45:36', '2025-07-07 15:01:34'),
(217, 'delhivery_seller_name', 'Pure Nutrition Co', 1, 'delhivery', '2025-07-04 12:45:36', '2025-07-07 15:01:34'),
(218, 'delhivery_seller_address', 'Pure Nutrition Co, Business Address, Mumbai', 1, 'delhivery', '2025-07-04 12:45:36', '2025-07-07 15:01:34'),
(219, 'delhivery_seller_gst', 'GST123456789', 1, 'delhivery', '2025-07-04 12:45:36', '2025-07-07 15:01:34'),
(250, 'delhivery_test_mode', '0', 1, 'general', '2025-07-07 13:48:39', '2025-07-07 13:48:39'),
(251, 'delhivery_mock_mode', '0', 1, 'general', '2025-07-07 13:48:39', '2025-07-07 13:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `SocialId` int NOT NULL,
  `Facebook` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Facebook page URL',
  `Instagram` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Instagram profile URL',
  `Twitter` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Twitter profile URL',
  `LinkedIn` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'LinkedIn profile URL',
  `YouTube` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'YouTube channel URL',
  `WhatsApp` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'WhatsApp link',
  `Telegram` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Telegram link',
  `IsActive` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT 'Y' COMMENT 'Active status',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`SocialId`, `Facebook`, `Instagram`, `Twitter`, `LinkedIn`, `YouTube`, `WhatsApp`, `Telegram`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'https://www.facebook.com/p/My-nutrify-100086009867166/', 'https://www.instagram.com/mynutrify.official/', NULL, NULL, 'https://www.youtube.com/@mynutrify_official', 'https://wa.me/919876543210', NULL, 'Y', '2025-07-23 07:40:13', '2025-07-23 07:40:13');

-- --------------------------------------------------------

--
-- Table structure for table `sub_category`
--

CREATE TABLE `sub_category` (
  `SubCategoryId` int NOT NULL,
  `SubCategoryName` text COLLATE utf8mb4_unicode_ci,
  `PhotoPath` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_category`
--

INSERT INTO `sub_category` (`SubCategoryId`, `SubCategoryName`, `PhotoPath`) VALUES
(1, 'Immunity Wellness', '64303.jpg'),
(2, 'Digestive Wellness', '21405.jpg'),
(3, 'Pain Reliever', '33842.jpg'),
(4, 'Diabetic Wellness', '58397.jpg'),
(5, 'Skin Wellness', '87603.jpg'),
(6, 'Blood Purifier', '37141.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user_actions`
--

CREATE TABLE `user_actions` (
  `id` int NOT NULL,
  `visitor_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int DEFAULT NULL,
  `action_type` enum('page_view','product_view','add_to_cart','remove_from_cart','purchase','search','filter','click','scroll') COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_details` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Additional action-specific data (JSON format)',
  `target_type` enum('product','category','page','button','link','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` int DEFAULT NULL COMMENT 'ID of the target (product_id, category_id, etc.)',
  `target_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_value` decimal(10,2) DEFAULT NULL COMMENT 'Monetary value if applicable',
  `quantity` int DEFAULT NULL COMMENT 'Quantity if applicable',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track all user actions and interactions';

--
-- Dumping data for table `user_actions`
--

INSERT INTO `user_actions` (`id`, `visitor_id`, `customer_id`, `action_type`, `action_details`, `target_type`, `target_id`, `target_name`, `page_url`, `session_id`, `action_value`, `quantity`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'product_view', '{\"time_on_page\": null}', 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/product_details.php?ProductId=6', 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 10:47:15'),
(2, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, 'product_view', '{\"time_on_page\": null}', 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/product_details.php?ProductId=6', 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 10:47:29'),
(3, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'product_view', '{\"time_on_page\": null}', 'product', 37, 'My Nutrify Herbal & Ayurveda\'s she care plus Juice | amla Juice combo', '/nutrify/product_details.php?ProductId=37', 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 10:48:11'),
(4, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, 'add_to_cart', NULL, 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/exe_files/add_to_cart_session.php', 'qtm3a17djisipjin6r6hlcnu16', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 10:54:14'),
(5, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', 16, 'product_view', '{\"time_on_page\": null}', 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/product_details.php?ProductId=6', 'qtm3a17djisipjin6r6hlcnu16', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 10:54:15'),
(6, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'product_view', '{\"time_on_page\": null}', 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/product_details.php?ProductId=6', 'fvu5e3hqtrfokfn68rcv7p7bln', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 11:14:49'),
(7, '061947878d543eb456944a8b33509b3cbe13382b31a6b08e2ae02500c89355e1', NULL, 'add_to_cart', NULL, 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/exe_files/add_to_cart_session.php', '0dlgeqc36sejujjh4i67odfi6b', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 12:39:28'),
(8, '061947878d543eb456944a8b33509b3cbe13382b31a6b08e2ae02500c89355e1', NULL, 'add_to_cart', NULL, 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/exe_files/add_to_cart_session.php', '0dlgeqc36sejujjh4i67odfi6b', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 12:46:27'),
(9, '061947878d543eb456944a8b33509b3cbe13382b31a6b08e2ae02500c89355e1', NULL, 'add_to_cart', NULL, 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/exe_files/add_to_cart_session.php', '0dlgeqc36sejujjh4i67odfi6b', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-21 12:46:32'),
(10, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, 'add_to_cart', NULL, 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/exe_files/add_to_cart_session.php', 'a5tfkjtpnilnl3bkv1tlq78n6m', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:29:25'),
(11, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"promo-copy-btn\"}', 'button', NULL, 'Copied!', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 05:52:32'),
(12, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 05:52:34'),
(13, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:07:58'),
(14, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:26:01'),
(15, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:26:01'),
(16, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'add_to_cart', NULL, 'product', 43, 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Apple Cider vinegar Juice Combo', '/nutrify/exe_files/add_to_cart_session.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:29:49'),
(17, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'add_to_cart', NULL, 'product', 43, 'My Nutrify Herbal & Ayurveda\'s Thyro Balance care juice & Apple Cider vinegar Juice Combo', '/nutrify/exe_files/add_to_cart_session.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:30:18'),
(18, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', 'b6un0e0bd3a84j2b6qhftqbltr', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:41:00'),
(19, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'add_to_cart', NULL, 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/exe_files/add_to_cart_session.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:46:11'),
(20, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'add_to_cart', NULL, 'product', 6, 'My Nutrify Herbal and Ayurveda\'s Wild Amla Juice - 1 Ltr | Fresh cold pressed Amla Juice | Helps Boosts Skin and Hair Health | Helps Detox | Rich in Vitamin C | Natural Immunity Booster', '/nutrify/exe_files/add_to_cart_session.php', 'ndrspntep9d9q6748l0fkdg52o', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:46:14'),
(21, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'add_to_cart', NULL, 'product', 9, 'My Nutrify Herbal & Ayurveda\'s  Cholesterol Care Juice Boosts Heart Health, Supports Cholesterol Balance with  Ginger, Garlic, Lemon, Honey & Apple Cider Vinegarâ€“ Antioxidant & Detox |  1 Ltr ', '/nutrify/exe_files/add_to_cart_session.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', 0.00, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 06:46:21'),
(22, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', 'bo91oepq9nlt87jn22edfcf5aq', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 07:22:06'),
(23, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', NULL, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', '2p5c0r82dj0l7hvrpefqr5j43v', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 07:22:38'),
(24, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 07:52:11'),
(25, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', 'ggg0579jifrqi3f98bf7c4b2ls', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 07:59:30'),
(26, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:22'),
(27, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:25'),
(28, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:28'),
(29, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:28'),
(30, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:28'),
(31, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:28'),
(32, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:28'),
(33, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:29'),
(34, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:04:35'),
(35, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 09:39:56'),
(36, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 09:40:06'),
(37, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 11:09:28'),
(38, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 11:09:31'),
(39, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 11:09:34'),
(40, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 11:10:16'),
(41, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', 16, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', 'gtqmo9bdh6dg7htjtm1q2kt6jo', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 11:10:17'),
(42, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"promo-copy-btn\"}', 'button', NULL, 'Copied!', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:07:43'),
(43, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:07:45'),
(44, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:03'),
(45, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:03'),
(46, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:05'),
(47, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:06'),
(48, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:07'),
(49, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:09'),
(50, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:10'),
(51, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:11'),
(52, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:11'),
(53, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:11'),
(54, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:11'),
(55, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:17'),
(56, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:18'),
(57, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:19'),
(58, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:20'),
(59, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:22'),
(60, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:22'),
(61, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:22'),
(62, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:22'),
(63, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:22'),
(64, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:08:23'),
(65, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:47'),
(66, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:49'),
(67, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:50'),
(68, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:50'),
(69, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:51'),
(70, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:51'),
(71, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:52'),
(72, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:22:59'),
(73, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:23:39'),
(74, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5ba5hiha1q3ul297jm7216pb38', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:29'),
(75, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:36'),
(76, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:36'),
(77, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:37'),
(78, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:43'),
(79, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:43'),
(80, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:44'),
(81, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:46'),
(82, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:47'),
(83, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:47'),
(84, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:47'),
(85, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:48'),
(86, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:48'),
(87, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:25:49'),
(88, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:26:43'),
(89, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:26:43'),
(90, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:26:44'),
(91, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:26:56'),
(92, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:26:56'),
(93, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:26:58'),
(94, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:02'),
(95, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:02'),
(96, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:02'),
(97, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:03'),
(98, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:04'),
(99, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:50'),
(100, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:51'),
(101, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:53'),
(102, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:54'),
(103, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:55'),
(104, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:55'),
(105, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:56'),
(106, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:27:56'),
(107, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:38:50'),
(108, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:38:50'),
(109, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:56:15'),
(110, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:56:19'),
(111, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:56:20'),
(112, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 13:04:49');
INSERT INTO `user_actions` (`id`, `visitor_id`, `customer_id`, `action_type`, `action_details`, `target_type`, `target_id`, `target_name`, `page_url`, `session_id`, `action_value`, `quantity`, `ip_address`, `user_agent`, `created_at`) VALUES
(113, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-prev disabled\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 13:05:46'),
(114, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 13:11:15'),
(115, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 13:11:15'),
(116, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, 'click', '{\"page_url\": \"/nutrify/index.php\", \"element_id\": null, \"element_class\": \"owl-next\"}', 'button', NULL, '', '/nutrify/analytics_endpoint.php', '5krnvapl5mh8rg5oj15quoud84', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 13:11:16'),
(117, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'click', '{\"page_url\": \"/nutrify/\", \"element_id\": null, \"element_class\": \"promo-close-btn\"}', 'button', NULL, '×', '/nutrify/analytics_endpoint.php', 'seihve75fm1almac0ncdfps535', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-24 06:00:59');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_analytics`
--

CREATE TABLE `visitor_analytics` (
  `id` int NOT NULL,
  `visitor_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique cookie-based visitor identifier',
  `customer_id` int DEFAULT NULL COMMENT 'Reference to customer_master.CustomerId if logged in',
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','unknown') COLLATE utf8mb4_unicode_ci DEFAULT 'unknown',
  `browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operating_system` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_visit` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visit` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_visits` int DEFAULT '1',
  `total_page_views` int DEFAULT '0',
  `total_session_duration` int DEFAULT '0' COMMENT 'Total time spent in seconds',
  `has_registered` tinyint(1) DEFAULT '0',
  `has_purchased` tinyint(1) DEFAULT '0',
  `total_orders` int DEFAULT '0',
  `total_order_value` decimal(10,2) DEFAULT '0.00',
  `referrer_url` text COLLATE utf8mb4_unicode_ci,
  `utm_source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `utm_medium` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `utm_campaign` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track unique visitors using cookie-based identification';

--
-- Dumping data for table `visitor_analytics`
--

INSERT INTO `visitor_analytics` (`id`, `visitor_id`, `customer_id`, `session_id`, `ip_address`, `user_agent`, `country`, `city`, `device_type`, `browser`, `operating_system`, `first_visit`, `last_visit`, `total_visits`, `total_page_views`, `total_session_duration`, `has_registered`, `has_purchased`, `total_orders`, `total_order_value`, `referrer_url`, `utm_source`, `utm_medium`, `utm_campaign`, `created_at`, `updated_at`) VALUES
(1, '7eee61f9bdc052ec1f8a7958a052b95ae1c140000c31cdac34bf73962e0296af', NULL, 'e9c8598kb62dn7k3cjkhbi255j', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-21 10:41:54', '2025-07-21 13:26:08', 17, 12, 0, 0, 0, 0, 0.00, 'http://localhost/nutrify/checkout.php', NULL, NULL, NULL, '2025-07-21 10:41:54', '2025-07-21 13:26:08'),
(5, '66fbda213e4ccbb7c6577473f0228042e55d2602021a7cab18d440a46eda3040', NULL, 'seihve75fm1almac0ncdfps535', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-21 10:47:15', '2025-07-24 06:01:36', 145, 33, 0, 0, 0, 0, 0.00, 'http://localhost/nutrify/products.php', NULL, NULL, NULL, '2025-07-21 10:47:15', '2025-07-24 06:01:36'),
(25, '061947878d543eb456944a8b33509b3cbe13382b31a6b08e2ae02500c89355e1', NULL, '0dlgeqc36sejujjh4i67odfi6b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-21 12:39:28', '2025-07-21 12:46:32', 3, 0, 0, 0, 0, 0, 0.00, 'http://localhost/nutrify/products.php?SubCategoryId=1', NULL, NULL, NULL, '2025-07-21 12:39:28', '2025-07-21 12:46:32'),
(33, '8e0a58de88a9c0c8c87eceb6a250d79000c2716bb2500b131b8bb6fbe861dd8a', NULL, 'a5tfkjtpnilnl3bkv1tlq78n6m', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-21 13:26:29', '2025-07-21 13:31:50', 7, 6, 0, 0, 0, 0, 0.00, NULL, NULL, NULL, NULL, '2025-07-21 13:26:29', '2025-07-21 13:31:50'),
(44, '8494f20dcdff6cedc545c02300c4ab7c9ddd532443b7006d529902db24a88a48', NULL, '5krnvapl5mh8rg5oj15quoud84', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-23 05:45:18', '2025-07-23 13:25:38', 132, 26, 0, 0, 0, 0, 0.00, NULL, NULL, NULL, NULL, '2025-07-23 05:45:18', '2025-07-23 13:25:38'),
(103, '6031b2039d82be3daa8dd2d6c0accbd579db3cc6da414bf41ac8bb0490051ba6', 16, '2p5c0r82dj0l7hvrpefqr5j43v', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-23 06:38:08', '2025-07-23 08:04:08', 32, 9, 0, 0, 0, 0, 0.00, 'http://localhost/nutrify/login.php', NULL, NULL, NULL, '2025-07-23 06:38:08', '2025-07-23 08:04:08'),
(223, 'a793929bdf7d55478ef979a20af459f47656ec2e59188101c49ba0d389499fad', NULL, '8floo8gn33cnq4reo7nr4hdb7v', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-23 11:20:43', '2025-07-23 11:33:37', 4, 1, 0, 0, 0, 0, 0.00, 'http://localhost/nutrify/combos.php', NULL, NULL, NULL, '2025-07-23 11:20:43', '2025-07-23 11:33:37'),
(235, '7daea833428d86b03a918e5ff402097fd6fa7c0a015cda088f9f765288dd9f22', NULL, '5ba5hiha1q3ul297jm7216pb38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, NULL, 'desktop', 'Chrome', 'Windows', '2025-07-23 12:05:44', '2025-07-23 13:43:52', 42, 5, 0, 0, 0, 0, 0.00, 'http://localhost/nutrify/products.php', NULL, NULL, NULL, '2025-07-23 12:05:44', '2025-07-23 13:43:52');

-- --------------------------------------------------------

--
-- Stand-in structure for view `visitor_summary`
-- (See below for the actual view)
--
CREATE TABLE `visitor_summary` (
`avg_pages_per_visitor` decimal(14,4)
,`avg_session_duration` decimal(14,4)
,`converted_visitors` bigint
,`new_visitors` bigint
,`returning_visitors` bigint
,`total_revenue` decimal(32,2)
,`total_visitors` bigint
,`visit_date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_activity_log`
--

CREATE TABLE `whatsapp_activity_log` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL COMMENT 'Reference to customer_master.CustomerId',
  `order_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reference to order_master.OrderId',
  `message_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Activity type',
  `success` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=success, 0=failed',
  `response` text COLLATE utf8mb4_unicode_ci COMMENT 'API response or error details',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_message_log`
--

CREATE TABLE `whatsapp_message_log` (
  `id` int NOT NULL,
  `message_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Interakt API message ID',
  `customer_id` int DEFAULT NULL COMMENT 'Reference to customer_master.CustomerId',
  `order_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reference to order_master.OrderId',
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer phone number',
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'WhatsApp template name used',
  `message_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type: order_update, payment_reminder, birthday, etc.',
  `status` enum('pending','sent','delivered','read','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `api_response` text COLLATE utf8mb4_unicode_ci COMMENT 'Full API response from Interakt',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Error message if failed',
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `retry_count` int DEFAULT '0' COMMENT 'Number of retry attempts'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_templates`
--

CREATE TABLE `whatsapp_templates` (
  `id` int NOT NULL,
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_category` enum('MARKETING','UTILITY','AUTHENTICATION') COLLATE utf8mb4_unicode_ci NOT NULL,
  `language_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `header_type` enum('TEXT','IMAGE','VIDEO','DOCUMENT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_text` text COLLATE utf8mb4_unicode_ci,
  `body_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_text` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_type` enum('URL','PHONE','QUICK_REPLY') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variable_count` int DEFAULT '0' COMMENT 'Number of variables in template',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_templates`
--

INSERT INTO `whatsapp_templates` (`id`, `template_name`, `template_category`, `language_code`, `status`, `header_type`, `header_text`, `body_text`, `footer_text`, `button_type`, `button_text`, `variable_count`, `created_at`, `updated_at`) VALUES
(1, 'order_shipped', 'MARKETING', 'en', 'approved', NULL, NULL, 'Hi {{1}}, great news! Your order #{{2}} has been shipped and is on its way to you. Track your order: {{3}}', 'My Nutrify - Your Health Partner', NULL, NULL, 3, '2025-07-03 07:49:35', '2025-07-03 07:49:35'),
(2, 'out_for_delivery', 'MARKETING', 'en', 'approved', NULL, NULL, 'Hi {{1}}, your order #{{2}} is out for delivery and will reach you today ({{3}}). Please keep your phone handy!', 'My Nutrify - Your Health Partner', NULL, NULL, 3, '2025-07-03 07:49:35', '2025-07-03 07:49:35'),
(3, 'order_delivered', 'MARKETING', 'en', 'approved', NULL, NULL, 'Hi {{1}}, your order #{{2}} has been delivered successfully! We hope you love your products from {{3}}.', 'My Nutrify - Your Health Partner', NULL, NULL, 3, '2025-07-03 07:49:35', '2025-07-03 07:49:35'),
(4, 'payment_reminder', 'MARKETING', 'en', 'approved', NULL, NULL, 'Hi {{1}}, your order #{{2}} for {{3}} is awaiting payment. Complete your payment within {{4}} to confirm your order.', 'My Nutrify - Your Health Partner', NULL, NULL, 4, '2025-07-03 07:49:35', '2025-07-03 07:49:35'),
(5, 'birthday_wishes', 'MARKETING', 'en', 'approved', NULL, NULL, '🎉 Happy Birthday {{1}}! Use code {{2}} and get {{3}} OFF on your next order! Valid for 7 days only.', 'My Nutrify - Your Health Partner', NULL, NULL, 3, '2025-07-03 07:49:35', '2025-07-03 07:49:35'),
(6, 'feedback_request', 'MARKETING', 'en', 'approved', NULL, NULL, 'Hi {{1}}, we hope you are loving your {{2}} from {{3}}! Could you spare 2 minutes to share your experience?', 'My Nutrify - Your Health Partner', NULL, NULL, 3, '2025-07-03 07:49:35', '2025-07-03 07:49:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliate_applications`
--
ALTER TABLE `affiliate_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email_pending` (`email`,`application_status`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_status` (`application_status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `authenticate_customers`
--
ALTER TABLE `authenticate_customers`
  ADD PRIMARY KEY (`CustId`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`BannerId`);

--
-- Indexes for table `blogs_master`
--
ALTER TABLE `blogs_master`
  ADD PRIMARY KEY (`BlogId`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_customer_product` (`CustomerId`,`ProductId`),
  ADD KEY `idx_customer_id` (`CustomerId`),
  ADD KEY `idx_product_id` (`ProductId`),
  ADD KEY `idx_creation_date` (`CreationDate`);

--
-- Indexes for table `category_master`
--
ALTER TABLE `category_master`
  ADD PRIMARY KEY (`CategoryId`);

--
-- Indexes for table `cod_payments`
--
ALTER TABLE `cod_payments`
  ADD PRIMARY KEY (`PaymentId`);

--
-- Indexes for table `consultation_bookings`
--
ALTER TABLE `consultation_bookings`
  ADD PRIMARY KEY (`BookingId`),
  ADD UNIQUE KEY `BookingNumber` (`BookingNumber`),
  ADD KEY `DoctorId` (`DoctorId`);

--
-- Indexes for table `consultation_history`
--
ALTER TABLE `consultation_history`
  ADD PRIMARY KEY (`HistoryId`),
  ADD KEY `BookingId` (`BookingId`),
  ADD KEY `DoctorId` (`DoctorId`);

--
-- Indexes for table `consultation_settings`
--
ALTER TABLE `consultation_settings`
  ADD PRIMARY KEY (`SettingId`),
  ADD UNIQUE KEY `SettingKey` (`SettingKey`);

--
-- Indexes for table `consultation_slots`
--
ALTER TABLE `consultation_slots`
  ADD PRIMARY KEY (`SlotId`),
  ADD UNIQUE KEY `unique_slot` (`DoctorId`,`SlotDate`,`SlotTime`),
  ADD KEY `BookingId` (`BookingId`);

--
-- Indexes for table `contact_master`
--
ALTER TABLE `contact_master`
  ADD PRIMARY KEY (`ContactId`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_code` (`coupon_code`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_coupon_customer_order` (`coupon_id`,`customer_id`,`order_id`),
  ADD KEY `idx_coupon_id` (`coupon_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_used_at` (`used_at`);

--
-- Indexes for table `customer_address`
--
ALTER TABLE `customer_address`
  ADD PRIMARY KEY (`AdrId`);

--
-- Indexes for table `customer_coupons`
--
ALTER TABLE `customer_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_customer_coupon` (`customer_id`,`coupon_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_coupon_id` (`coupon_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `customer_master`
--
ALTER TABLE `customer_master`
  ADD PRIMARY KEY (`CustomerId`),
  ADD KEY `idx_customer_whatsapp_opt_in` (`whatsapp_opt_in`),
  ADD KEY `idx_customer_birthday` (`DateOfBirth`),
  ADD KEY `idx_customer_active` (`IsActive`(10));

--
-- Indexes for table `customer_points`
--
ALTER TABLE `customer_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`);

--
-- Indexes for table `daily_analytics`
--
ALTER TABLE `daily_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_date` (`analytics_date`),
  ADD KEY `idx_analytics_date` (`analytics_date`),
  ADD KEY `idx_total_visitors` (`total_visitors`),
  ADD KEY `idx_total_revenue` (`total_revenue`);

--
-- Indexes for table `delhivery_api`
--
ALTER TABLE `delhivery_api`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `delivery_config`
--
ALTER TABLE `delivery_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `delivery_logs`
--
ALTER TABLE `delivery_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_provider` (`provider`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `delivery_performance`
--
ALTER TABLE `delivery_performance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_provider_date` (`provider`,`date`),
  ADD KEY `idx_provider` (`provider`),
  ADD KEY `idx_date` (`date`);

--
-- Indexes for table `delivery_rates`
--
ALTER TABLE `delivery_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_provider` (`provider`),
  ADD KEY `idx_pincodes` (`from_pincode`,`to_pincode`),
  ADD KEY `idx_weight` (`weight_from`,`weight_to`),
  ADD KEY `idx_service_type` (`service_type`);

--
-- Indexes for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_provider` (`provider`),
  ADD KEY `idx_tracking_id` (`tracking_id`),
  ADD KEY `idx_waybill` (`waybill_number`),
  ADD KEY `idx_status` (`current_status`),
  ADD KEY `idx_delivered` (`is_delivered`);

--
-- Indexes for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_zone_name` (`zone_name`),
  ADD KEY `idx_pincodes` (`pincode_start`,`pincode_end`),
  ADD KEY `idx_provider` (`preferred_provider`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `direct_customers`
--
ALTER TABLE `direct_customers`
  ADD PRIMARY KEY (`CustomerId`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`DoctorId`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`AvailabilityId`),
  ADD KEY `DoctorId` (`DoctorId`);

--
-- Indexes for table `enhanced_coupons`
--
ALTER TABLE `enhanced_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon_code` (`coupon_code`),
  ADD KEY `idx_coupon_code` (`coupon_code`),
  ADD KEY `idx_valid_dates` (`valid_from`,`valid_until`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_reward_coupon` (`is_reward_coupon`),
  ADD KEY `idx_points_required` (`points_required`),
  ADD KEY `reward_catalog_id` (`reward_catalog_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`FAQId`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location_master`
--
ALTER TABLE `location_master`
  ADD PRIMARY KEY (`LocationId`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `model_images`
--
ALTER TABLE `model_images`
  ADD PRIMARY KEY (`ImageId`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `order_master`
--
ALTER TABLE `order_master`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `OrderId` (`OrderId`),
  ADD KEY `idx_waybill` (`Waybill`),
  ADD KEY `idx_delivery_status` (`delivery_status`),
  ADD KEY `idx_delivery_provider` (`delivery_provider`),
  ADD KEY `idx_order_coupon` (`CouponCode`),
  ADD KEY `idx_order_points` (`PointsUsed`);

--
-- Indexes for table `our_services`
--
ALTER TABLE `our_services`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_id` (`visitor_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_page_type` (`page_type`),
  ADD KEY `idx_viewed_at` (`viewed_at`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `idx_visitor_customer_date` (`visitor_id`,`customer_id`,`viewed_at`);

--
-- Indexes for table `points_config`
--
ALTER TABLE `points_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `points_transactions`
--
ALTER TABLE `points_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `product_analytics`
--
ALTER TABLE `product_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product` (`product_id`),
  ADD KEY `idx_total_views` (`total_views`),
  ADD KEY `idx_total_purchases` (`total_purchases`),
  ADD KEY `idx_total_revenue` (`total_revenue`),
  ADD KEY `idx_conversion_rate` (`overall_conversion_rate`);

--
-- Indexes for table `product_benefits`
--
ALTER TABLE `product_benefits`
  ADD PRIMARY KEY (`Product_BenefitId`);

--
-- Indexes for table `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`Product_DetailsId`);

--
-- Indexes for table `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD PRIMARY KEY (`IngredientId`);

--
-- Indexes for table `product_master`
--
ALTER TABLE `product_master`
  ADD PRIMARY KEY (`ProductId`);

--
-- Indexes for table `product_offers`
--
ALTER TABLE `product_offers`
  ADD PRIMARY KEY (`offer_id`),
  ADD UNIQUE KEY `unique_product_offer` (`product_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_created_date` (`created_date`);

--
-- Indexes for table `product_price`
--
ALTER TABLE `product_price`
  ADD PRIMARY KEY (`PriceId`);

--
-- Indexes for table `product_review`
--
ALTER TABLE `product_review`
  ADD PRIMARY KEY (`Product_ReviewId`);

--
-- Indexes for table `product_subcategories`
--
ALTER TABLE `product_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_subcategory` (`ProductId`,`SubCategoryId`),
  ADD KEY `idx_product_id` (`ProductId`),
  ADD KEY `idx_subcategory_id` (`SubCategoryId`),
  ADD KEY `idx_primary` (`is_primary`);

--
-- Indexes for table `promo_leads`
--
ALTER TABLE `promo_leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mobile` (`mobile_number`),
  ADD KEY `idx_verified` (`is_verified`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_otp_generated` (`otp_generated_at`);

--
-- Indexes for table `rewards_catalog`
--
ALTER TABLE `rewards_catalog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_points` (`points_required`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_type` (`reward_type`);

--
-- Indexes for table `reward_redemptions`
--
ALTER TABLE `reward_redemptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_reward` (`reward_id`),
  ADD KEY `idx_coupon` (`coupon_code`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `search_analytics`
--
ALTER TABLE `search_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_id` (`visitor_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_search_query` (`search_query`),
  ADD KEY `idx_clicked_product_id` (`clicked_product_id`),
  ADD KEY `idx_searched_at` (`searched_at`);

--
-- Indexes for table `shipping_config`
--
ALTER TABLE `shipping_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_config_key` (`config_key`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`SocialId`);

--
-- Indexes for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD PRIMARY KEY (`SubCategoryId`);

--
-- Indexes for table `user_actions`
--
ALTER TABLE `user_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visitor_id` (`visitor_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_target_type` (`target_type`),
  ADD KEY `idx_target_id` (`target_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `idx_product_action_date` (`target_id`,`action_type`,`created_at`),
  ADD KEY `idx_visitor_session` (`visitor_id`,`session_id`);

--
-- Indexes for table `visitor_analytics`
--
ALTER TABLE `visitor_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_visitor` (`visitor_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_first_visit` (`first_visit`),
  ADD KEY `idx_last_visit` (`last_visit`),
  ADD KEY `idx_device_type` (`device_type`),
  ADD KEY `idx_has_purchased` (`has_purchased`);

--
-- Indexes for table `whatsapp_activity_log`
--
ALTER TABLE `whatsapp_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_message_type` (`message_type`),
  ADD KEY `idx_success` (`success`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `whatsapp_message_log`
--
ALTER TABLE `whatsapp_message_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_phone` (`phone_number`),
  ADD KEY `idx_template` (`template_name`),
  ADD KEY `idx_message_type` (`message_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `idx_retry_count` (`retry_count`);

--
-- Indexes for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_name` (`template_name`),
  ADD KEY `idx_template_name` (`template_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`template_category`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliate_applications`
--
ALTER TABLE `affiliate_applications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `consultation_bookings`
--
ALTER TABLE `consultation_bookings`
  MODIFY `BookingId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultation_history`
--
ALTER TABLE `consultation_history`
  MODIFY `HistoryId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultation_settings`
--
ALTER TABLE `consultation_settings`
  MODIFY `SettingId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `consultation_slots`
--
ALTER TABLE `consultation_slots`
  MODIFY `SlotId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_master`
--
ALTER TABLE `contact_master`
  MODIFY `ContactId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_coupons`
--
ALTER TABLE `customer_coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_master`
--
ALTER TABLE `customer_master`
  MODIFY `CustomerId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10004;

--
-- AUTO_INCREMENT for table `customer_points`
--
ALTER TABLE `customer_points`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `daily_analytics`
--
ALTER TABLE `daily_analytics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_config`
--
ALTER TABLE `delivery_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `delivery_logs`
--
ALTER TABLE `delivery_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT for table `delivery_performance`
--
ALTER TABLE `delivery_performance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_rates`
--
ALTER TABLE `delivery_rates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_tracking`
--
ALTER TABLE `delivery_tracking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `DoctorId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `AvailabilityId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `enhanced_coupons`
--
ALTER TABLE `enhanced_coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `location_master`
--
ALTER TABLE `location_master`
  MODIFY `LocationId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `model_images`
--
ALTER TABLE `model_images`
  MODIFY `ImageId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=377;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `order_master`
--
ALTER TABLE `order_master`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `our_services`
--
ALTER TABLE `our_services`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `points_config`
--
ALTER TABLE `points_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `points_transactions`
--
ALTER TABLE `points_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product_analytics`
--
ALTER TABLE `product_analytics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `Product_DetailsId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_offers`
--
ALTER TABLE `product_offers`
  MODIFY `offer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_subcategories`
--
ALTER TABLE `product_subcategories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `promo_leads`
--
ALTER TABLE `promo_leads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rewards_catalog`
--
ALTER TABLE `rewards_catalog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reward_redemptions`
--
ALTER TABLE `reward_redemptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `search_analytics`
--
ALTER TABLE `search_analytics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipping_config`
--
ALTER TABLE `shipping_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `SocialId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_actions`
--
ALTER TABLE `user_actions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `visitor_analytics`
--
ALTER TABLE `visitor_analytics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=383;

--
-- AUTO_INCREMENT for table `whatsapp_activity_log`
--
ALTER TABLE `whatsapp_activity_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whatsapp_message_log`
--
ALTER TABLE `whatsapp_message_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- --------------------------------------------------------

--
-- Structure for view `active_coupons`
--
DROP TABLE IF EXISTS `active_coupons`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_coupons`  AS SELECT `enhanced_coupons`.`id` AS `id`, `enhanced_coupons`.`coupon_code` AS `coupon_code`, `enhanced_coupons`.`coupon_name` AS `coupon_name`, `enhanced_coupons`.`description` AS `description`, `enhanced_coupons`.`discount_type` AS `discount_type`, `enhanced_coupons`.`discount_value` AS `discount_value`, `enhanced_coupons`.`max_discount_amount` AS `max_discount_amount`, `enhanced_coupons`.`minimum_order_amount` AS `minimum_order_amount`, `enhanced_coupons`.`usage_limit_total` AS `usage_limit_total`, `enhanced_coupons`.`usage_limit_per_customer` AS `usage_limit_per_customer`, `enhanced_coupons`.`current_usage_count` AS `current_usage_count`, `enhanced_coupons`.`valid_from` AS `valid_from`, `enhanced_coupons`.`valid_until` AS `valid_until`, `enhanced_coupons`.`points_required` AS `points_required`, `enhanced_coupons`.`is_reward_coupon` AS `is_reward_coupon` FROM `enhanced_coupons` WHERE ((`enhanced_coupons`.`is_active` = true) AND (`enhanced_coupons`.`valid_from` <= now()) AND (`enhanced_coupons`.`valid_until` >= now())) ;

-- --------------------------------------------------------

--
-- Structure for view `active_product_offers`
--
DROP TABLE IF EXISTS `active_product_offers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_product_offers`  AS SELECT `po`.`offer_id` AS `offer_id`, `po`.`product_id` AS `product_id`, `po`.`offer_title` AS `offer_title`, `po`.`offer_description` AS `offer_description`, `po`.`created_date` AS `created_date`, `po`.`updated_date` AS `updated_date`, `pm`.`ProductName` AS `ProductName`, `pm`.`PhotoPath` AS `PhotoPath`, `pm`.`ShortDescription` AS `ShortDescription`, `pm`.`CategoryId` AS `CategoryId`, `pm`.`SubCategoryId` AS `SubCategoryId`, `pm`.`ProductCode` AS `ProductCode`, `pm`.`Specification` AS `Specification`, `pm`.`MetaTags` AS `MetaTags`, `pm`.`MetaKeywords` AS `MetaKeywords`, min(`pp`.`OfferPrice`) AS `min_offer_price`, min(`pp`.`MRP`) AS `min_mrp`, (min(`pp`.`MRP`) - min(`pp`.`OfferPrice`)) AS `savings_amount`, round((((min(`pp`.`MRP`) - min(`pp`.`OfferPrice`)) / min(`pp`.`MRP`)) * 100),0) AS `discount_percentage` FROM ((`product_offers` `po` join `product_master` `pm` on((`po`.`product_id` = `pm`.`ProductId`))) join `product_price` `pp` on((`pm`.`ProductId` = `pp`.`ProductId`))) WHERE (`po`.`is_active` = 1) GROUP BY `po`.`offer_id`, `po`.`product_id`, `po`.`offer_title`, `po`.`offer_description`, `po`.`created_date`, `po`.`updated_date`, `pm`.`ProductName`, `pm`.`PhotoPath`, `pm`.`ShortDescription`, `pm`.`CategoryId`, `pm`.`SubCategoryId`, `pm`.`ProductCode`, `pm`.`Specification`, `pm`.`MetaTags`, `pm`.`MetaKeywords` ;

-- --------------------------------------------------------

--
-- Structure for view `customer_available_coupons`
--
DROP TABLE IF EXISTS `customer_available_coupons`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customer_available_coupons`  AS SELECT `c`.`id` AS `id`, `c`.`coupon_code` AS `coupon_code`, `c`.`coupon_name` AS `coupon_name`, `c`.`description` AS `description`, `c`.`discount_type` AS `discount_type`, `c`.`discount_value` AS `discount_value`, `c`.`max_discount_amount` AS `max_discount_amount`, `c`.`minimum_order_amount` AS `minimum_order_amount`, `c`.`points_required` AS `points_required`, `c`.`is_reward_coupon` AS `is_reward_coupon`, `cc`.`customer_id` AS `customer_id`, `cc`.`status` AS `wallet_status`, `cc`.`expires_at` AS `expires_at` FROM (`enhanced_coupons` `c` left join `customer_coupons` `cc` on((`c`.`id` = `cc`.`coupon_id`))) WHERE ((`c`.`is_active` = true) AND (`c`.`valid_from` <= now()) AND (`c`.`valid_until` >= now())) ;

-- --------------------------------------------------------

--
-- Structure for view `popular_products`
--
DROP TABLE IF EXISTS `popular_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `popular_products`  AS SELECT `pa`.`product_id` AS `product_id`, `pm`.`ProductName` AS `ProductName`, `pm`.`PhotoPath` AS `PhotoPath`, `pa`.`total_views` AS `total_views`, `pa`.`unique_views` AS `unique_views`, `pa`.`total_cart_additions` AS `total_cart_additions`, `pa`.`total_purchases` AS `total_purchases`, `pa`.`total_revenue` AS `total_revenue`, `pa`.`overall_conversion_rate` AS `overall_conversion_rate`, rank() OVER (ORDER BY `pa`.`total_views` desc ) AS `view_rank`, rank() OVER (ORDER BY `pa`.`total_purchases` desc ) AS `purchase_rank` FROM (`product_analytics` `pa` join `product_master` `pm` on((`pa`.`product_id` = `pm`.`ProductId`))) WHERE (`pa`.`total_views` > 0) ORDER BY `pa`.`total_views` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `product_with_subcategories`
--
DROP TABLE IF EXISTS `product_with_subcategories`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_with_subcategories`  AS SELECT `pm`.`ProductId` AS `ProductId`, `pm`.`ProductName` AS `ProductName`, `pm`.`PhotoPath` AS `PhotoPath`, `pm`.`IsCombo` AS `IsCombo`, `pm`.`CategoryId` AS `CategoryId`, `pm`.`ProductCode` AS `ProductCode`, `pm`.`ShortDescription` AS `ShortDescription`, `pm`.`Specification` AS `Specification`, `pm`.`MetaTags` AS `MetaTags`, `pm`.`MetaKeywords` AS `MetaKeywords`, `pm`.`Title` AS `Title`, `pm`.`Description` AS `Description`, `pm`.`VideoURL` AS `VideoURL`, group_concat(`ps`.`SubCategoryId` separator ',') AS `SubCategoryIds`, group_concat(`sc`.`SubCategoryName` separator ',') AS `SubCategoryNames`, (select `product_subcategories`.`SubCategoryId` from `product_subcategories` where ((`product_subcategories`.`ProductId` = `pm`.`ProductId`) and (`product_subcategories`.`is_primary` = true)) limit 1) AS `PrimarySubCategoryId` FROM ((`product_master` `pm` left join `product_subcategories` `ps` on((`pm`.`ProductId` = `ps`.`ProductId`))) left join `sub_category` `sc` on((`ps`.`SubCategoryId` = `sc`.`SubCategoryId`))) GROUP BY `pm`.`ProductId` ;

-- --------------------------------------------------------

--
-- Structure for view `visitor_summary`
--
DROP TABLE IF EXISTS `visitor_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `visitor_summary`  AS SELECT cast(`visitor_analytics`.`first_visit` as date) AS `visit_date`, count(0) AS `total_visitors`, count((case when (`visitor_analytics`.`total_visits` = 1) then 1 end)) AS `new_visitors`, count((case when (`visitor_analytics`.`total_visits` > 1) then 1 end)) AS `returning_visitors`, avg(`visitor_analytics`.`total_page_views`) AS `avg_pages_per_visitor`, avg(`visitor_analytics`.`total_session_duration`) AS `avg_session_duration`, count((case when (`visitor_analytics`.`has_purchased` = true) then 1 end)) AS `converted_visitors`, sum(`visitor_analytics`.`total_order_value`) AS `total_revenue` FROM `visitor_analytics` GROUP BY cast(`visitor_analytics`.`first_visit` as date) ORDER BY `visit_date` DESC ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consultation_bookings`
--
ALTER TABLE `consultation_bookings`
  ADD CONSTRAINT `consultation_bookings_ibfk_1` FOREIGN KEY (`DoctorId`) REFERENCES `doctors` (`DoctorId`) ON DELETE RESTRICT;

--
-- Constraints for table `consultation_history`
--
ALTER TABLE `consultation_history`
  ADD CONSTRAINT `consultation_history_ibfk_1` FOREIGN KEY (`BookingId`) REFERENCES `consultation_bookings` (`BookingId`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultation_history_ibfk_2` FOREIGN KEY (`DoctorId`) REFERENCES `doctors` (`DoctorId`) ON DELETE RESTRICT;

--
-- Constraints for table `consultation_slots`
--
ALTER TABLE `consultation_slots`
  ADD CONSTRAINT `consultation_slots_ibfk_1` FOREIGN KEY (`DoctorId`) REFERENCES `doctors` (`DoctorId`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultation_slots_ibfk_2` FOREIGN KEY (`BookingId`) REFERENCES `consultation_bookings` (`BookingId`) ON DELETE SET NULL;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `enhanced_coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer_master` (`CustomerId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_coupons`
--
ALTER TABLE `customer_coupons`
  ADD CONSTRAINT `customer_coupons_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_master` (`CustomerId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_coupons_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `enhanced_coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD CONSTRAINT `doctor_availability_ibfk_1` FOREIGN KEY (`DoctorId`) REFERENCES `doctors` (`DoctorId`) ON DELETE CASCADE;

--
-- Constraints for table `enhanced_coupons`
--
ALTER TABLE `enhanced_coupons`
  ADD CONSTRAINT `enhanced_coupons_ibfk_1` FOREIGN KEY (`reward_catalog_id`) REFERENCES `rewards_catalog` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_analytics`
--
ALTER TABLE `product_analytics`
  ADD CONSTRAINT `product_analytics_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_master` (`ProductId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_offers`
--
ALTER TABLE `product_offers`
  ADD CONSTRAINT `product_offers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_master` (`ProductId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_subcategories`
--
ALTER TABLE `product_subcategories`
  ADD CONSTRAINT `product_subcategories_ibfk_1` FOREIGN KEY (`ProductId`) REFERENCES `product_master` (`ProductId`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_subcategories_ibfk_2` FOREIGN KEY (`SubCategoryId`) REFERENCES `sub_category` (`SubCategoryId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
