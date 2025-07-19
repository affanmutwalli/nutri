<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('includes/urls.php');
include('database/dbconnection.php');
$obj = new main();
$obj->connection();
$FieldNames=array("BannerId","Title","ShortDescription","PhotoPath","Position");
$ParamArray=array();
$Fields=implode(",",$FieldNames);
$banner_data=$obj->MysqliSelect1("Select ".$Fields." from banners ",$FieldNames,"s",$ParamArray);


?>

<!-- End Meta Pixel Code -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- title -->
    <title>My Nutrify Herbal And Ayurveda - Herbal, Natural, and Ayurvedic Products</title>
    <meta name="description"
        content="My Nutrify Herbal And Ayurveda provides a wide range of herbal, natural, and ayurvedic products designed to support your health, wellness, and balanced lifestyle." />
    <meta name="keywords"
        content="herbal products, ayurvedic remedies, natural supplements, healthy living, wellness, traditional medicine, eco-friendly products, nutrition" />
    <meta name="author" content="My Nutrify Herbal And Ayurveda">


    <!-- favicon -->
    <link rel="shortcut icon" type="image/favicon" href="image/fevicon.png">
    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <!-- simple-line icon -->
    <link rel="stylesheet" type="text/css" href="css/simple-line-icons.css">
    <!-- font-awesome icon -->
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <!-- themify icon -->
    <link rel="stylesheet" type="text/css" href="css/themify-icons.css">
    <!-- ion icon -->
    <link rel="stylesheet" type="text/css" href="css/ionicons.min.css">
    <!-- owl slider -->
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/owl.theme.default.min.css">
    <!-- swiper -->
    <link rel="stylesheet" type="text/css" href="css/swiper.min.css">
    <!-- animation -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <!-- style -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts for Krishna Ayurved Style -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
    /* Krishna Ayurveda Inspired Design System */
    :root {
        --primary-green: #305724;
        --primary-orange: #EA652D;
        --secondary-orange: #ec6504;
        --light-green: #E6F7D8;
        --light-orange: #FCE2CF;
        --text-dark: #2c3e50;
        --text-light: #7f8c8d;
        --white: #ffffff;
        --border-light: #e0e0e0;
        --shadow-light: 0 4px 12px rgba(0,0,0,0.1);
        --shadow-medium: 0 8px 25px rgba(0,0,0,0.15);
        --border-radius: 12px;
        --transition: all 0.3s ease;
    }

    /* Enhanced Section Title Styling - Krishna Style with Original Animations */
    .section-title {
        text-align: center !important;
        margin: 60px 0 40px 0 !important;
        position: relative;
    }

    .section-title h2 {
        font-family: "Inter", "Harmonia Sans", sans-serif !important;
        font-size: 28px !important;
        font-weight: 700 !important;
        color: var(--text-dark) !important;
        text-align: center !important;
        line-height: 1.3 !important;
        letter-spacing: 0.5px !important;
        text-transform: none !important;
        margin: 0 !important;
        padding: 18px 35px !important;
        position: relative !important;
        display: inline-block !important;
        background: rgb(253, 244, 244) !important;
        border-radius: 40px !important;
        overflow: hidden !important;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .section-title h2 .highlight {
        color: var(--primary-orange) !important;
        font-weight: 700 !important;
        position: relative;
    }

    /* Restore Krishna Ayurved Moving Highlight Animation */
    .section-title h2::after {
        content: "";
        display: block;
        position: absolute;
        width: 120px;
        height: 50px;
        background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0));
        top: 0;
        bottom: 0;
        transform: matrix(1, 0, -0.57735, 1, 0, 0);
        animation: switchHighlight 2s ease 2s infinite;
        pointer-events: none;
    }

    /* Keyframe for the moving highlight effect */
    @keyframes switchHighlight {
        0% {
            left: -120px;
        }
        50% {
            left: 50%;
            transform: matrix(1, 0, -0.57735, 1, 0, 0) translateX(-50%);
        }
        100% {
            left: calc(100% + 120px);
        }
    }

    /* Animation for section titles */
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section-title h2.animate-in {
        animation-play-state: running;
    }

    /* Enhanced hover effect */
    .section-title h2 .highlight {
        display: inline-block;
        transition: var(--transition);
    }

    .section-title h2 .highlight:hover {
        transform: scale(1.05);
        color: var(--secondary-orange) !important;
    }

    /* Enhanced Rewards Element - My Nutrify Style */
    .rewards-element {
        position: fixed;
        bottom: 90px;
        left: 20px;
        z-index: 9999;
        background: linear-gradient(135deg, #2d5016, #1a3009);
        color: white;
        padding: 0;
        border-radius: 32px;
        cursor: pointer;
        font-family: "Inter", sans-serif;
        font-weight: 400;
        font-size: 14px;
        text-decoration: none;
        display: flex;
        align-items: center;
        height: 50px;
        width: auto;
        transition: all 0.3s ease;
        animation: horizontal-shaking 4s ease 5s infinite normal none running;
        box-shadow: 0 4px 12px rgba(45, 80, 22, 0.4);
        border: 2px solid rgba(255, 165, 0, 0.3);
    }

    .rewards-element:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 20px rgba(45, 80, 22, 0.6);
        text-decoration: none;
        color: white;
        animation-play-state: paused;
        border-color: rgba(255, 165, 0, 0.6);
    }

    .rewards-element .rewards-content {
        display: flex;
        align-items: center;
        padding: 0 15px;
        gap: 10px;
        width: auto;
        height: 100%;
    }

    .rewards-element .icon {
        color: white;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .rewards-element .text {
        font-size: 14px;
        flex-grow: 1;
        color: white;
        text-align: center;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Krishna Ayurveda Style Horizontal Shaking Animation */
    @keyframes horizontal-shaking {
        0% { transform: translateX(0); }
        25% { transform: translateX(5px); }
        50% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
        100% { transform: translateX(0); }
    }

    /* Dynamic State Classes */
    .rewards-element.guest-user {
        background: linear-gradient(135deg, #ff8c00, #ffa500);
        animation: gentle-pulse 3s ease-in-out infinite;
        border-color: rgba(45, 80, 22, 0.3);
    }

    .rewards-element.logged-in-user {
        background: linear-gradient(135deg, #2d5016, #1a3009);
        animation: horizontal-shaking 4s ease 5s infinite normal none running;
        border-color: rgba(255, 165, 0, 0.3);
    }

    .rewards-element.cart-activity {
        background: linear-gradient(135deg, #ff8c00, #ff6b35);
        animation: intense-shake 2s ease-in-out infinite;
        border-color: rgba(45, 80, 22, 0.5);
    }

    .rewards-element.special-offer {
        background: linear-gradient(135deg, #2d5016, #ff8c00);
        animation: rainbow-pulse 2s ease-in-out infinite;
        border-color: rgba(255, 165, 0, 0.6);
    }

    /* Additional Dynamic Animations */
    @keyframes gentle-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.03); }
    }

    @keyframes intense-shake {
        0%, 100% { transform: translateX(0) rotate(0deg); }
        25% { transform: translateX(-8px) rotate(-2deg); }
        50% { transform: translateX(8px) rotate(2deg); }
        75% { transform: translateX(-8px) rotate(-2deg); }
    }

    @keyframes rainbow-pulse {
        0% {
            background: linear-gradient(135deg, #2d5016, #1a3009);
            transform: scale(1);
        }
        25% {
            background: linear-gradient(135deg, #ff8c00, #ffa500);
            transform: scale(1.05);
        }
        50% {
            background: linear-gradient(135deg, #ff6b35, #ff8c00);
            transform: scale(1.08);
        }
        75% {
            background: linear-gradient(135deg, #2d5016, #ff8c00);
            transform: scale(1.05);
        }
        100% {
            background: linear-gradient(135deg, #2d5016, #1a3009);
            transform: scale(1);
        }
    }

    /* Enhanced Product Cards - Krishna Style */
    .tred-pro, .h-cate {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: hidden;
        border: 1px solid var(--border-light);
    }

    .tred-pro:hover, .h-cate:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-medium);
        border-color: var(--primary-orange);
    }

    .h-cate:hover .cat-title {
        color: var(--primary-orange) !important;
    }

    .h-cate:hover .c-img div {
        transform: scale(1.05) !important;
    }

    .h-cate:hover .c-img img {
        transform: scale(1.1) !important;
    }

    .tr-pro-img, .c-img {
        position: relative;
        overflow: hidden;
    }

    .tr-pro-img img, .c-img img {
        transition: var(--transition);
        width: 100%;
        height: auto;
    }

    .tr-pro-img:hover img, .c-img:hover img {
        transform: scale(1.05);
    }

    .caption {
        padding: 20px;
    }

    .caption h3 {
        font-family: "Inter", sans-serif;
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .caption h3 a {
        color: inherit;
        text-decoration: none;
        transition: var(--transition);
    }

    .caption h3 a:hover {
        color: var(--primary-orange);
    }

    .pro-price {
        margin: 15px 0;
    }

    .new-price {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-orange);
    }

    .old-price {
        font-size: 16px;
        color: var(--text-light);
        text-decoration: line-through;
        margin-left: 8px;
    }

    /* Enhanced Buttons */
    .btn-style1 {
        background: linear-gradient(135deg, var(--primary-green), #4a7c59);
        color: var(--white);
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(48, 87, 36, 0.3);
    }

    .btn-style1:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(48, 87, 36, 0.4);
        color: var(--white);
        text-decoration: none;
        background: linear-gradient(135deg, #4a7c59, var(--primary-green));
    }

    /* Responsive adjustments for Dynamic Rewards Element */
    @media (max-width: 768px) {
        .rewards-element {
            bottom: 80px;
            left: 15px;
            height: 45px;
        }

        .rewards-element .rewards-content {
            padding: 0 12px;
            gap: 8px;
        }

        .rewards-element .text {
            font-size: 13px;
        }

        .rewards-element .icon {
            font-size: 16px;
        }

        .section-title h2 {
            font-size: 28px !important;
        }

        .caption {
            padding: 16px;
        }
    }

    @media (max-width: 480px) {
        .rewards-element {
            bottom: 70px;
            left: 10px;
            height: 40px;
        }

        .rewards-element .rewards-content {
            padding: 0 10px;
            gap: 6px;
        }

        .rewards-element .text {
            font-size: 12px;
        }

        .rewards-element .icon {
            font-size: 14px;
        }

        .section-title h2 {
            font-size: 24px !important;
        }

        .caption h3 {
            font-size: 16px;
        }
    }

    /* Enhanced hover effects for desktop */
    @media (min-width: 769px) {
        .rewards-element:hover .icon {
            transform: rotate(15deg) scale(1.1);
            transition: transform 0.3s ease;
        }

        .rewards-element:hover .text {
            letter-spacing: 0.5px;
            transition: letter-spacing 0.3s ease;
        }
    }

    /* Rewards Modal - Krishna Ayurveda Style */
    .rewards-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .rewards-modal.show {
        opacity: 1;
    }

    .rewards-modal-container {
        position: relative;
        width: 375px;
        max-height: 90vh;
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        transform: scale(0.9) translateY(20px);
        transition: transform 0.3s ease;
    }

    .rewards-modal.show .rewards-modal-container {
        transform: scale(1) translateY(0);
    }

    .rewards-modal-content {
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Modal Header */
    .rewards-header {
        background: linear-gradient(135deg, #2d5016, #1a3009);
        color: white;
        padding: 20px;
        position: relative;
        text-align: center;
        border-bottom: 3px solid #ff8c00;
    }

    .rewards-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .close-modal {
        position: absolute;
        top: 15px;
        right: 20px;
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.3s ease;
    }

    .close-modal:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Modal Body */
    .rewards-body {
        padding: 20px;
    }

    /* Welcome Section */
    .rewards-welcome {
        text-align: center;
        margin-bottom: 25px;
    }

    .rewards-intro h3 {
        color: #333;
        font-size: 20px;
        margin: 0 0 8px 0;
        font-weight: 600;
    }

    .rewards-intro p {
        color: #666;
        font-size: 14px;
        margin: 0 0 20px 0;
        line-height: 1.4;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ff8c00, #ffa500);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #2d5016, #1a3009);
        border-color: #ff8c00;
    }

    .login-link {
        font-size: 13px;
        color: #666;
        margin: 0;
    }

    .login-link a {
        color: #ff8c00;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        color: #2d5016;
    }

    /* Features Section */
    .rewards-features {
        margin-bottom: 25px;
    }

    .feature-section {
        margin-bottom: 12px;
    }

    .feature-header {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .feature-header:hover {
        background: #e9ecef;
    }

    .feature-header img {
        width: 24px;
        height: 24px;
        margin-right: 12px;
    }

    .feature-header h4 {
        flex: 1;
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .expand-icon {
        color: #ff8c00;
        font-weight: bold;
        font-size: 18px;
    }

    /* Referral Program */
    .referral-program {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .referral-header h4 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .referral-header p {
        margin: 0 0 15px 0;
        font-size: 13px;
        color: #666;
        line-height: 1.4;
    }

    .referral-benefits {
        display: flex;
        gap: 15px;
    }

    .benefit {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 12px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .benefit img {
        width: 20px;
        height: 20px;
    }

    .benefit-label {
        display: block;
        font-size: 11px;
        color: #666;
        margin-bottom: 2px;
    }

    .benefit-value {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    /* User Points Dashboard */
    .user-points {
        text-align: center;
        margin-bottom: 25px;
    }

    .points-display {
        background: linear-gradient(135deg, #2d5016, #1a3009);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 3px solid #ff8c00;
        box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
    }

    .points-display h3 {
        margin: 0 0 10px 0;
        font-size: 16px;
        font-weight: 500;
        opacity: 0.9;
    }

    .points-value {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .points-number {
        font-size: 36px;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 5px;
    }

    .points-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .tier-info {
        margin-top: 10px;
    }

    .tier-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid rgba(255, 140, 0, 0.5);
    }

    /* Tabs */
    .rewards-tabs {
        display: flex;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 4px;
        margin-bottom: 20px;
    }

    .tab {
        flex: 1;
        text-align: center;
        padding: 10px 8px;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .tab.active {
        background: linear-gradient(135deg, #ff8c00, #ffa500);
        color: white;
        box-shadow: 0 2px 4px rgba(255, 140, 0, 0.3);
    }

    /* Tab Content */
    .tab-content {
        min-height: 150px;
    }

    .tab-content.hidden {
        display: none;
    }

    /* Earn Methods */
    .earn-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .earn-icon {
        font-size: 24px;
        margin-right: 15px;
    }

    .earn-details {
        flex: 1;
    }

    .earn-details h4 {
        margin: 0 0 4px 0;
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .earn-details p {
        margin: 0;
        font-size: 12px;
        color: #666;
    }

    .earn-points {
        font-size: 14px;
        font-weight: bold;
        color: #2d5016;
        background: rgba(255, 140, 0, 0.2);
        padding: 4px 8px;
        border-radius: 12px;
    }

    /* Redeem Options */
    .redeem-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        text-align: center;
    }

    .redeem-item h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .redeem-item p {
        margin: 0 0 15px 0;
        font-size: 12px;
        color: #666;
    }

    .redeem-btn {
        background: #e9ecef;
        color: #666;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: not-allowed;
    }

    .redeem-btn:not(:disabled) {
        background: linear-gradient(135deg, #2d5016, #1a3009);
        color: white;
        cursor: pointer;
        border: 2px solid #ff8c00;
    }

    .redeem-btn:not(:disabled):hover {
        background: linear-gradient(135deg, #ff8c00, #ffa500);
        border-color: #2d5016;
    }

    /* History */
    .history-empty {
        text-align: center;
        padding: 40px 20px;
        color: #666;
    }

    /* Modal Footer */
    .rewards-footer {
        background: #f8f9fa;
        padding: 15px 20px;
        text-align: center;
        border-top: 1px solid #e9ecef;
    }

    .rewards-footer p {
        margin: 0;
        font-size: 12px;
        color: #666;
    }

    /* Responsive Modal */
    @media (max-width: 480px) {
        .rewards-modal-container {
            width: 95%;
            max-width: 350px;
            margin: 0 auto;
        }

        .referral-benefits {
            flex-direction: column;
            gap: 10px;
        }

        .benefit {
            justify-content: center;
        }

        .rewards-tabs {
            flex-direction: column;
            gap: 2px;
        }

        .tab {
            border-radius: 4px;
        }
    }

    /* Container for the marquee */
    #chat-widget {
      position: fixed;
      bottom: 20px; right: 20px;
      width: 350px; height: 600px;
      display: none;
      flex-direction: column;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      background: #fff;
      z-index: 9999;
    }
    #chat-header {
      display: flex; align-items: center;
      background: #ec6504; color: #fff; padding: 10px;
      border-radius: 8px 8px 0 0;
    }
    #chat-header img { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
    #close-chat { background: none; border: none; color: #fff; font-size: 30px; cursor: pointer; margin-left: auto; }
    #chat-body {
      flex: 1; overflow-y: auto; padding: 10px; background: #f9f9f9;
      display: flex; flex-direction: column; gap: 12px;
    }
    .bot-message, .user-message {
      margin: 4px 0; padding: 10px; border-radius: 15px;
      max-width: 85%; word-wrap: break-word;
    }
    .bot-message { background: #fff; align-self: flex-start; border: 1px solid #e0e0e0; }
    .user-message { background: #ec6504; color: #fff; align-self: flex-end; }
    #chat-input-container { display: flex; border-top: 1px solid #ccc; padding: 8px; }
    #chat-input { flex: 1; border: none; padding: 10px; font-size: 14px; border-radius: 20px; margin-right: 8px; }
    #chat-input:focus { outline: none; box-shadow: 0 0 0 2px #ec650455; }
    #send-btn {
      border: none; background: #ec6504; color: #fff;
      padding: 0 16px; cursor: pointer; border-radius: 20px;
      transition: background 0.3s ease;
    }
    #send-btn:hover { background: #d35400; }
    #send-btn:disabled { background: #ec650455; cursor: not-allowed; }

    /* Typing Animation */
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }
    .typing-indicator {
      display: flex; align-items: center;
      padding: 12px; gap: 8px;
    }
    .typing-dot {
      width: 8px; height: 8px;
      background: #888; border-radius: 50%;
      animation: bounce 1.4s infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    /* Optimized Product Carousel */
    .product-carousel {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      gap: 15px;
      padding: 12px 0;
      margin: 10px -10px;
      min-height: 240px;
      -webkit-overflow-scrolling: touch;
    }
    .product-carousel::-webkit-scrollbar {
      height: 5px;
    }
    .product-carousel::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }
    .product-carousel::-webkit-scrollbar-thumb {
      background: #ec6504;
      border-radius: 3px;
    }
    .product-card {
      flex: 0 0 220px;
      scroll-snap-align: start;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 15px;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .product-card img {
      width: 100%;
      height: 140px;
      object-fit: contain;
      margin-bottom: 12px;
      border-radius: 4px;
    }
    .product-name {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #333;
      line-height: 1.3;
    }
    .product-price {
      font-size: 15px;
      color: #ec6504;
      font-weight: 700;
      margin-bottom: 12px;
    }
    .add-btn {
      background: #27ae60;
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 10px 15px;
      font-size: 13px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .add-btn:hover { background: #219a52; }

    @media (max-width: 480px) {
      .product-card {
        flex: 0 0 200px;
      }
    }
    
    .single-product-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 20px;
      margin: 15px 0;
      text-align: center;
      max-width: 280px;
      margin-left: auto;
      margin-right: auto;
    }

    .single-product-image {
      width: 100%;
      height: 180px;
      object-fit: contain;
      margin-bottom: 15px;
      border-radius: 8px;
    }

    .single-product-name {
      font-size: 16px;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 8px;
      line-height: 1.3;
    }

    .single-product-price {
      font-size: 18px;
      color: #ec6504;
      font-weight: 800;
      margin-bottom: 15px;
    }

    .single-product-add-btn {
      background: #27ae60;
      color: #fff;
      padding: 12px 25px;
      border-radius: 25px;
      font-size: 14px;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s ease;
    }

    .single-product-add-btn:hover {
      background: #219a52;
      transform: translateY(-2px);
    }

    /* Updated Product Carousel */
    .product-carousel {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      gap: 15px;
      padding: 12px 0;
      margin: 10px -10px;
      min-height: 240px;
      -webkit-overflow-scrolling: touch;
    }
    .marquee-container {
        width: 100%;
        background-color: #305724;
        /* Set your desired background color */
        padding: 10px 0;
        color: white;
        /* Text color */
        font-size: 18px;
        /* Default font size */
        text-align: center;
        /* Center the text */
        overflow: hidden;
        /* Hide content that overflows */
    }

    /* The content that scrolls */
    .marquee-content {
        display: inline-flex;
        animation: marquee 30s linear infinite;
        /* Infinite scroll */
    }

    .marquee-item {
        display: inline-flex;
        align-items: center;
        margin-right: 30px;
        /* Space between items */
    }

    .marquee-item p {
        margin: 0 10px;
        font-weight: bold;
    }

    .marquee-item img {
        width: 20px;
        /* Default icon size */
        height: auto;
    }

    /* Keyframe animation for continuous scrolling */
    @keyframes marquee {
        0% {
            transform: translateX(100%);
            /* Start from right */
        }

        100% {
            transform: translateX(-100%);
            /* End at left */
        }
    }

    /* For seamless looping, we need to duplicate the content */
    .marquee-container .marquee-content {
        animation-timing-function: linear;
        display: flex;
    }

    /* Duplicate the marquee content to make the loop seamless */
    .marquee-container .marquee-content::after {
        content: attr(data-content);
        visibility: hidden;
    }

    /* Media query for tablets */
    @media (max-width: 768px) {
        .marquee-container {
            font-size: 14px;
            /* Smaller font size for tablets */
            padding: 5px 0;
        }

        .marquee-item {
            margin-right: 15px;
            /* Reduce space between items */
        }

        .marquee-item img {
            width: 15px;
            /* Smaller icon size for tablets */
        }
    }

    /* Media query for mobile phones */
    @media (max-width: 480px) {
        .marquee-container {
            display: none;
            /* Hide marquee on mobile */
        }
    }
    .card-container {
        position: relative;
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .scrollable-cards {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
    }

    .scrollable-cards::-webkit-scrollbar {
        display: none; /* Hide scrollbar */
    }

    .product-card {
        flex: 0 0 auto;
        width: 250px;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 10px;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    .product-card video {
        width: 100%;
        height: 450px;
        object-fit: cover;
    }

    .views-and-icons {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        font-size: 12px;
    }

    .views-and-icons i {
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .product-info {
        padding: 10px;
        text-align: center;
    }

    .product-info h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    .price {
        margin: 5px 0;
        font-size: 14px;
        color: #555;
    }

    .discount {
        color: #305724;
    }

    .buy-now-btn {
        display: inline-block;
        background: #305724;
        color: #fff;
        padding: 10px 15px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        margin-top: 10px;
        transition: background 0.3s ease;
    }

    .buy-now-btn:hover {
        background: #218838;
    }

    .nav-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        font-size: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1;
    }

    .nav-button:hover {
        background: rgba(0, 0, 0, 0.7);
    }

    .nav-button.left {
        left: 10px;
    }

    .nav-button.right {
        right: 10px;
    }
    /* Modal Styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
    background: #fff;
    border-radius: 10px;
    text-align: center;
    max-width: 30%;  /* Adjust width as needed */
    width: 380px;
    max-height: 800px; /* Set a max height for the modal */
    height: 88%; /* Set a relative height */
    overflow: hidden; /* Ensures no scrollbars if the content exceeds the height */
    position: relative; /* Ensure relative positioning for overlay content */
}

    .modal-content video {
        width: 100%;
        height: auto; /* Maintain video aspect ratio */
        border-radius: 10px;
    }


    .modal-content h3 {
        margin: 10px 0;
        font-size: 20px;
    }

    .modal-content p {
        margin: 5px 0;
        font-size: 16px;
        color: #555;
    }
    .modal-overlay {
    position: absolute;
    top: 83%;  /* Adjusted to make the overlay appear closer to the top */
    left: 10%;
    right: 10%;
    z-index: 10;
    background-color: rgba(0, 0, 0, 0.6);  /* Slightly more opaque transparent background */
    color: #fff;
    padding: 10px;  /* Reduced padding for smaller height */
    text-align: center;
    border-radius: 10px;
    max-height: 100px;  /* Limit the height of the overlay */
    overflow: hidden;   /* Ensure content doesn't overflow */
}

#modal-video {
    pointer-events: none;  /* Prevent interaction with video controls */
}

.modal-overlay h3, .modal-overlay p, .modal-overlay button {
    margin: 10px 0;
    text-color: #fff;
}

    .buy-now-btn {
        display: inline-block;
        background: #305724;
        color: #fff;
        padding: 10px 15px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        margin-top: 10px;
        transition: background 0.3s ease;
        cursor: pointer;
    }

    .buy-now-btn:hover {
        background: #305724;
    }

    .modal-content {
        position: relative;
    }
    .video-overlay {
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    width: 100%;
    padding: 0 10px;
}

/* Icon Styling */
.video-overlay i {
    font-size: 30px;
    color: #000;
    cursor: pointer;
    transition: color 0.3s ease;
}

/* Hover effect for icons */
.video-overlay i:hover {
    color: #28a745;  /* Change icon color on hover */
}

@media (max-width: 768px) {
    .modal {
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        max-width: 80%; /* Slightly smaller on medium screens */
        width: auto;
        height: auto;
        max-height: 85%; /* Reduce the height for better visibility */
        padding: 10px;
    }

    .modal-content h3 {
        font-size: 18px;
    }

    .modal-content p {
        font-size: 14px;
    }

    .modal-overlay {
        bottom: 5%; /* Adjust position for smaller screens */
        padding: 8px;
    }

    .buy-now-btn {
        padding: 8px 12px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .modal {
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        max-width: 90%; /* Centered and slightly smaller */
        width: auto;
        height: auto;
        max-height: 80%; /* Reduce the height further for small screens */
        padding: 8px; /* Adjust padding for smaller screens */
    }

    .modal-content h3 {
        font-size: 16px;
    }

    .modal-content p {
        font-size: 13px;
    }

    .modal-overlay {
        font-size: 12px;
        padding: 6px;
    }

    .buy-now-btn {
        font-size: 10px;
        padding: 6px 10px;
    }

    .video-overlay i {
        font-size: 25px;
    }
}

/* Popup Overlay */
.cart-popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1000;
}

/* Popup Content */
.cart-popup-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    width: 300px;
}

/* Close Button */
.close-popup {
    position: absolute;
    top: 10px;
    right: 10px;
    border: none;
    background: none;
    font-size: 20px;
    cursor: pointer;
}

/* Popup Heading */
.cart-popup-body h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 15px;
}

/* Buttons */
.cart-popup-actions a {
    display: inline-block;
    margin: 10px 5px;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.cart-popup-actions .btn-view-cart {
    background: #305724;
}

.cart-popup-actions .btn-view-cart:hover {
    background: #000000;
}

.cart-popup-actions .btn-checkout {
    background: #ec6504;
}

.cart-popup-actions .btn-checkout:hover {
    background: #ffffff;
}




/* Animated Elements */
@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes fadeInUp {
  from {
    transform: translateY(40px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}


/* Enhanced Banner Styles - Krishna Inspired Full Width */
.img-back {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  height: 70vh; /* Reduced height for better proportion */
  background-size: cover;
  background-position: center;
  position: relative;
  overflow: hidden;
  padding: 3rem;
  width: 100vw !important; /* Full viewport width */
  margin-left: calc(-50vw + 50%) !important; /* Center full-width element */
  border-radius: 0; /* Remove border radius for full width */
}

.img-back::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.4) 100%);
  z-index: 1;
}

.home-s-content {
  position: relative;
  z-index: 2;
  max-width: 600px;
  color: var(--white);
  transform: translateY(0);
  opacity: 1;
  animation: slideInRight 1s cubic-bezier(0.23, 1, 0.32, 1) forwards;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 40px;
  border-radius: var(--border-radius);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.home-s-content span {
  display: block;
  font-size: 1.1rem;
  font-weight: 500;
  letter-spacing: 2px;
  margin-bottom: 1rem;
  opacity: 0;
  animation: fadeInUp 0.8s 0.2s cubic-bezier(0.23, 1, 0.32, 1) forwards;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
  color: var(--white);
  text-transform: uppercase;
}

.home-s-content h1 {
  font-size: 2.8rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 2rem;
  opacity: 0;
  animation: fadeInUp 0.8s 0.4s cubic-bezier(0.23, 1, 0.32, 1) forwards;
  text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
  color: var(--white);
}

/* Responsive Design */
@media (max-width: 1200px) {
  .home-s-content h1 {
    font-size: 2.0rem;
  }
}

@media (max-width: 992px) {
  .home-s-content {
    text-align: left;
  }
}

@media (max-width: 768px) {
 
  .home-s-content span {
    font-size: 0.7rem;
    /* letter-spacing: 2px; */
  }
  
  .home-s-content h1 {
    font-size: 1rem;    
    margin-bottom: 0.6rem;
  }
}

@media (max-width: 480px) {
  .home-s-content {
    max-width: 70%;
    padding-left:50px
  }
  
  .home-s-content h1 {
    font-size: 0.8rem;
  }
}


/* Tablet View */
@media (max-width: 1024px) {
    .slider .items .img-back {
        height:  300px; /* Adjust height for tablets */
    }
}

/* Mobile View */
@media (max-width: 768px) {
    .slider .items .img-back {
        height: 200px; /* Adjust height for smaller screens */
        background-position: center center;
    }
  
}
#chat-icon {
    position: fixed;
    bottom: 50px;
    right: 20px;
    cursor: pointer;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    z-index: 9999; /* Ensures it stays above other elements */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#chat-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 12px;
    text-align: center;
    line-height: 20px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

#chat-online {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background-color: #32CD32;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

/* Banner Section Styles */
.banner-section {
    text-align: center;
    margin: 40px 0;
    padding: 20px;
    
    border-radius: 15px;
}

.banner-section img {
    max-width: 95%;
    height: 250px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: block;
    margin: 0 auto;
    border: 1px orange solid;
    transition: transform 0.3s ease;
}

.banner-section img:hover {
    transform: scale(1.02);
}

@media (max-width: 768px) {
    .banner-section {
        margin: 20px 0;
        padding: 15px;
    }
}

    </style>

    <!-- Enhanced Krishna Ayurveda Styling -->
    <style>
    /* Enhanced Tab Navigation - Krishna Style */
    .nav-tabs .nav-link {
        border: none !important;
        background: var(--white) !important;
        color: var(--text-dark) !important;
        border: 2px solid var(--border-light) !important;
        border-radius: 25px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        transition: var(--transition) !important;
        margin: 0 5px !important;
    }

    .nav-tabs .nav-link:hover {
        background: var(--light-orange) !important;
        color: var(--primary-orange) !important;
        border-color: var(--primary-orange) !important;
        transform: translateY(-2px) !important;
    }

    .nav-tabs .nav-link.active {
        background: var(--primary-orange) !important;
        color: var(--white) !important;
        border-color: var(--primary-orange) !important;
        box-shadow: 0 4px 15px rgba(234, 101, 45, 0.3) !important;
    }

    /* Enhanced Banner Section */
    .o-t-banner {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: hidden;
        border: 1px solid var(--border-light);
    }

    .o-t-banner:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-medium);
    }

    .o-t-content {
        padding: 30px;
    }

    .o-t-content h6 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 15px;
        line-height: 1.4;
    }

    /* Enhanced Section Spacing */
    .section-t-padding {
        padding-top: 80px;
    }

    .section-b-padding {
        padding-bottom: 80px;
    }

    .section-tb-padding {
        padding: 80px 0;
    }

    /* Full-Width Layout - Krishna Ayurveda Style */
    .container {
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
    }

    .container-fluid {
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
    }

    /* Full-Width Sections */
    body, html {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden;
    }

    /* Header Full Width */
    .header-area,
    .header-main-area,
    .header-bottom-area {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Main Content Full Width */
    .main-content,
    .content-wrapper,
    section {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Banner Full Width */
    .slider,
    .home-slider,
    .img-back {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Footer Full Width */
    .footer-area,
    .footer-main-area {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Responsive Full Width - All Screen Sizes */
    @media (min-width: 1200px) {
        .container {
            max-width: 100% !important;
            padding-left: 40px !important;
            padding-right: 40px !important;
        }
    }

    @media (min-width: 1400px) {
        .container {
            max-width: 100% !important;
            padding-left: 60px !important;
            padding-right: 60px !important;
        }
    }

    @media (min-width: 1600px) {
        .container {
            max-width: 100% !important;
            padding-left: 80px !important;
            padding-right: 80px !important;
        }
    }

    @media (max-width: 768px) {
        .container,
        .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
    }

    /* Enhanced Rating Stars */
    .rating {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .c-star, .fa-star {
        color: #ffc107 !important;
    }

    /* Enhanced Carousel Navigation */
    .owl-nav button {
        background: var(--white) !important;
        color: var(--primary-orange) !important;
        border: 2px solid var(--primary-orange) !important;
        border-radius: 50% !important;
        width: 50px !important;
        height: 50px !important;
        font-size: 18px !important;
        transition: var(--transition) !important;
    }

    .owl-nav button:hover {
        background: var(--primary-orange) !important;
        color: var(--white) !important;
        transform: scale(1.1) !important;
    }

    .owl-dots .owl-dot span {
        background: var(--border-light) !important;
        transition: var(--transition) !important;
    }

    .owl-dots .owl-dot.active span,
    .owl-dots .owl-dot:hover span {
        background: var(--primary-orange) !important;
    }

    /* Enhanced Home Offer Banner - Keep Original Side-by-Side Layout */
    .home-offer-banner {
        display: flex;
        gap: 30px;
        margin-top: 40px;
    }

    .home-offer-banner .o-t-banner {
        flex: 1;
    }

    @media (max-width: 768px) {
        .home-offer-banner {
            flex-direction: column;
            gap: 20px;
        }
    }
    </style>

    <style>
    .watch-shop-section {
        padding: 2rem 1rem;
        color: white;
    }

    .shop-section-title h2 {
        text-align: center;
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        color: #fff;
    }

    .shop-video-carousel-container {
        position: relative;
        max-width: 1500px;
        margin: 0 auto;
        width: 100%;
        overflow: hidden;
    }

    .shop-video-carousel {
        display: flex !important;
        flex-direction: row !important;
        gap: 1.2rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding: 1rem;
        -ms-overflow-style: none;
        scrollbar-width: none;
        align-items: flex-start;
    }

    .shop-video-carousel::-webkit-scrollbar {
        display: none;
    }

    .shop-product-card {
        flex: 0 0 300px !important;
        background: #212121;
        border-radius: 16px;
        overflow: hidden;
        scroll-snap-align: start;
        transition: transform 0.3s ease;
        position: relative;
        height: auto;
        min-width: 300px;
        display: block;
    }

    .shop-product-card:hover {
        transform: translateY(-5px);
    }

    .shop-video-container {
        position: relative;
        background: #000;
        height: 400px; /* Reduced video height */
    }

    .shop-video-container iframe {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

    .shop-product-info {
        padding: 0.8rem 1rem;
    }

    .shop-product-info h3 {
        font-size: 1.1rem;
        margin-bottom: 0.4rem;
        color: #fff;
    }

    .shop-price-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .shop-current-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #EA652D;
    }

    .shop-original-price {
        font-size: 0.9rem;
        color: #888;
        text-decoration: line-through;
    }

    .shop-discount {
        background: #4CAF50;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-size: 0.75rem;
        color: #fff;
    }

    .shop-buy-button {
        width: 100%;
        padding: 0.6rem;
        background: #EA652D;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .shop-buy-button:hover {
        background: #cc541f;
    }

    .shop-engagement-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 1rem 1rem;
        font-size: 0.85rem;
        color: #aaa;
    }

    .shop-actions {
        display: flex;
        gap: 0.5rem;
    }

    .shop-icon-button {
        background: none;
        border: none;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0.4rem;
    }

    /* Product Modal */
    .shop-product-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .shop-modal-content {
        position: relative;
        width: 100%;
        max-width: 400px;
        height: 85vh;
        background: #000;
        border-radius: 16px;
        overflow: hidden;
    }

    .shop-modal-controls {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        gap: 1rem;
        z-index: 10;
    }

    .shop-close-button {
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
    }

    .shop-mute-button {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(4px);
    }

    .shop-product-details {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2rem;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.85));
        color: white;
    }

    .shop-cta-button {
        width: 100%;
        padding: 1rem;
        background: #ff4444;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .shop-product-card {
            flex: 0 0 260px;
        }

        .shop-modal-content {
            height: 80vh;
            border-radius: 0;
        }

        .shop-video-container {
            height: 180px;
        }
    }
</style>

    <style>
   .combo-carousel-container {
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        position: relative;
    }

    .combo-carousel-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
        gap: 15px;
    }

    .combo-carousel-item {
        flex: 0 0 150px;
        /* show multiple items */
        padding: 10px;
        text-align: center;
    }

    .combo-carousel-item img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .combo-carousel-item img:hover {
        transform: scale(1.05);
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    }

    .combo-carousel-item img.selected {
        border: 3px solid rgb(218, 118, 75);
        box-shadow: 0 0 12px rgb(221, 129, 8);
    }

    .combo-carousel-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        z-index: 10;
    }

    .combo-carousel-button.prev {
        left: 0;
    }

    .combo-carousel-button.next {
        right: 0;
    }

    .combo-card-images {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        position: relative;
    }

    .plus-sign {
        font-size: 32px;
        font-weight: bold;
        color: #333;
        margin: 0 20px;
    }

    .combo-card-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        margin-bottom: 20px;
    }



    /* Main section for product cards */
    .offer-main {
        display: flex;
        flex-wrap: wrap;
        /* Allow wrapping of cards */
        justify-content: center;
        /* Center the cards */
        gap: 20px;
        /* Space between cards */
        padding: 20px;
        /* Padding around the section */
    }

    /* Card container */
    .offer-product-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #FCE2CF;
        border-radius: 15px;
        padding: 40px;
        max-width: 700px;
        /* Max width for larger screens */
        width: 100%;
        /* Allow cards to take full width */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        position: relative;
        /* Added for badge positioning */
        transition: transform 0.3s;
        /* Smooth transition for hover effect */
    }

    .offer-product-card:hover {
        transform: translateY(-5px);
        /* Lift effect on hover */
    }

    /* Left side - Product info */
    .offer-product-info {
        max-width: 50%;
        padding-right: 30px;
        /* Spacing between text and image */
    }

    .offer-category {
        font-size: 14px;
        color: #888;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .offer-product-title {
        font-size: 32px;
        /* Slightly larger title */
        color: #2d4827;
        margin-bottom: 15px;
        line-height: 1.2;
    }

    .offer-description {
        font-size: 16px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .offer-shop-btn {
        display: inline-block;
        background-color: #2d4827;
        color: #fff;
        text-decoration: none;
        padding: 12px 30px;
        /* Wider button */
        border-radius: 25px;
        font-size: 16px;
        transition: all 0.3s ease;
        font-weight: 500;
        border: 2px solid transparent;
    }

    .offer-shop-btn:hover {
        background-color: #EA652D;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(234, 101, 45, 0.3);
    }

    /* Right side - Product image */
    .offer-product-image {
        position: relative;
        width: 400px;
        /* Fixed width for better image control */
        height: 400px;
        /* Square aspect ratio */
    }

    .offer-product-image img {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        object-fit: cover;
        /* Ensure image fills container */
    }

    /* New badge */
    .offer-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: #f97316;
        color: #fff;
        font-size: 14px;
        padding: 6px 15px;
        border-radius: 5px;
        font-weight: 600;
        z-index: 2;
    }

    /* Price tag */
    .offer-price {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: #fff;
        color: #2d4827;
        font-size: 20px;
        /* Slightly larger price */
        padding: 8px 20px;
        border-radius: 5px;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .offer-product-card {
            flex-direction: column;
            /* Stack elements vertically */
            text-align: center;
            /* Center text */
            padding: 25px;
            /* Adjust padding */
            margin: 20px auto;
            /* Center card */
        }

        .offer-product-info {
            max-width: 100%;
            /* Full width on smaller screens */
            padding-right: 0;
            /* Remove right padding */
            margin-bottom: 25px;
            /* Space below info */
        }

        .offer-product-image {
            width: 100%;
            /* Full width for image */
            height: 300px;
            /* Reduced height for mobile */
        }

        .offer-product-title {
            font-size: 28px;
            /* Adjust title size */
        }

        .offer-shop-btn {
            width: auto;
            /* Auto width for button */
            display: inline-flex;
            /* Flex display for button */
        }
    }

    @media (max-width: 480px) {
        .offer-product-image {
            height: 250px;
            /* Further reduced height for mobile */
        }

        .offer-price {
            font-size: 18px;
            /* Adjust price font size */
            padding: 6px 15px;
            /* Adjust padding */
        }

        .offer-badge {
            font-size: 12px;
            /* Smaller badge font size */
        }
    }

    .testimonia-carousel {
        width: 90%;
        max-width: 900px;
        margin: 50px auto;
        position: relative;
    }

    .testimonia-carousel-item {
        text-align: center;
        padding: 40px 30px;
        border-radius: 12px;
        min-height: 400px;
        transition: transform 0.6s ease-in-out;
    }

    .testimonia-testimonial {
        font-size: 18px;
        color: #555;
        line-height: 1.8;
        margin: 20px 0;
    }

    .testimonia-overview b {
        color: #305724;
        font-size: 20px;
        display: block;
        margin-top: 15px;
    }

    .testimonia-star-rating i {
        color: #EA652D;
        font-size: 22px;
        margin: 5px 2px;
    }

    .testimonia-carousel-control-prev,
    .testimonia-carousel-control-next {
        width: 50px;
        height: 50px;
        background: #305724;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 1;
    }

    .testimonia-carousel-control-prev:hover,
    .testimonia-carousel-control-next:hover {
        background: #EA652D;
    }

    .testimonia-carousel-indicators {
        bottom: -40px;
    }

    .testimonia-carousel-indicators li {
        background-color: #ccc;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin: 0 5px;
    }

    .testimonia-carousel-indicators .active {
        background-color: #305724;
    }

    .testimonia-head {
        background-color: #E6F7D8;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .testimonia-carousel-item {
            min-height: 450px;
            padding: 30px;
        }

        .testimonia-testimonial {
            font-size: 16px;
        }

        .testimonia-carousel-control-prev,
        .testimonia-carousel-control-next {
            width: 40px;
            height: 40px;
        }
    }
 /* ✅ Desktop View */
.comparison-section {
    padding: 30px;
    background: #fff;
    display: flex;
    justify-content: center;
}

.comparison-box {
    position: relative;
    display: flex;
    max-width: 1200px;
    width: 100%;
    border: 2px solid #EA652D;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    background: #fff;
}

/* Columns */
.comparison-column {
    flex: 1;
    padding: 40px;
    min-width: 300px;
    position: relative;
}

/* Left Column */
.comparison-column.left {
    background: linear-gradient(to bottom right, #E6F7D8, #D3F4B3);
}

/* Right Column */
.comparison-column.right {
    background: linear-gradient(to bottom right, #FCE2CF, rgb(255, 215, 198));
    border-left: 2px solid #EA652D;
}

/* Brand Header */
.brand-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 25px;
}

.brand-logo {
    height: 50px;
}

.others-heading {
    font-size: 24px;
    font-weight: 600;
    color: #5A2E0C;
    margin-bottom: 50px;
    text-align: center;
}

/* List Styling */
.comparison-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.comparison-list li {
    display: flex;
    align-items: center;
    font-size: 20px;
    padding: 12px 0;
    color: #333;
    gap: 12px;
}

.comparison-list li img {
    height: 30px;
    width: 30px;
    object-fit: contain;
}

/* ✅ VS Badge */
.vs-badge {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border: 2px solid #EA652D;
    border-radius: 50px;
    padding: 8px 20px;
    font-weight: 700;
    color: #EA652D;
    z-index: 2;
    box-shadow: 0 4px 15px rgba(234, 101, 45, 0.2);
    font-size: 18px;
}
@media (max-width: 768px) {
    .comparison-section {
        padding: 20px 0;
        justify-content: flex-start;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .comparison-box {
        flex-direction: row;
        flex-wrap: nowrap;
        min-width: 768px; /* Minimum width for both columns */
        border: none;
        box-shadow: none;
        gap: 20px;
        padding: 0 20px;
    }

    .comparison-column {
        flex: 0 0 calc(100vw - 40px);
        min-width: calc(100vw - 40px);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        scroll-snap-align: center;
    }

    .comparison-column.right {
        border-left: 2px solid #EA652D;
        border-top: none;
    }

    .vs-badge {
        display: block;
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border: 2px solid #EA652D;
        z-index: 3;
    }

    /* Scroll Snap */
    .comparison-section {
        scroll-snap-type: x mandatory;
    }

    /* Scrollbar Styling */
    .comparison-section::-webkit-scrollbar {
        height: 6px;
    }

    .comparison-section::-webkit-scrollbar-thumb {
        background: #EA652D;
        border-radius: 4px;
    }

    /* Mobile Optimizations */
    .comparison-list li {
        font-size: 16px;
        padding: 8px 0;
    }

    .comparison-list li img {
        height: 25px;
        width: 25px;
    }

    .others-heading {
        font-size: 20px;
        margin-bottom: 30px;
    }

    .brand-logo {
        height: 40px;
    }
}

@media (max-width: 480px) {
    .comparison-column {
        flex: 0 0 calc(100vw - 30px);
        min-width: calc(100vw - 30px);
        padding: 20px;
    }

    .comparison-list li {
        font-size: 14px;
    }

    .vs-badge {
        display:none;
    }
}
 

  /* ✅ Desktop Grid Layout */
.use-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Card Styling */
.use-cart {
    background: white;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.use-cart:hover {
    transform: translateY(-5px);
}

.use-cart img {
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin-bottom: 16px;
}

.use-cart p {
    font-size: 16px;
    color: #305724;
    font-weight: 500;
}
/* Optional: Add padding to first/last items */
.use-container > *:first-child {
    margin-left: 16px;
}

.use-container > *:last-child {
    margin-right: 16px;
}

/* Optional: Hide scrollbar on non-interaction */
.use-container {
    scrollbar-color: transparent transparent;
}

.use-container:hover {
    scrollbar-color: #EA652D transparent;
}

/* ✅ Mobile Carousel */
@media (max-width: 768px) {
    .use-container {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
        padding: 20px 16px;
        gap: 16px;
        grid-template-columns: unset;
    }

    .use-container::-webkit-scrollbar {
        height: 4px;
    }

    .use-container::-webkit-scrollbar-thumb {
        background: #EA652D;
        border-radius: 4px;
    }

    .use-cart {
        flex: 0 0 calc(80% - 8px);
        min-width: calc(80% - 8px);
        scroll-snap-align: start;
    }
}

@media (max-width: 480px) {
    .use-cart {
        flex: 0 0 calc(90% - 8px);
        min-width: calc(90% - 8px);
    }
    
    .use-cart img {
        width: 80px;
        height: 80px;
    }
    
    .use-cart p {
        font-size: 14px;
    }
}

/* ✅ Desktop Styling */
.cart-para {
    line-height: 1.8;
    color: #444;
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
    background: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    text-align: justify;
    transition: all 0.3s ease;
}

/* ✅ Hover Effect */
.cart-para:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* ✅ Mobile & Tablet View */
@media (max-width: 1024px) {
    .cart-para {
        font-size: 16px;
        line-height: 1.7;
        padding: 20px;
    }
}

@media (max-width: 768px) {
    .cart-para {
        font-size: 15px;
        line-height: 1.6;
        padding: 16px;
    }
}

/* ✅ Small Screens (Phones) */
@media (max-width: 480px) {
    .cart-para {
        font-size: 14px;
        line-height: 1.5;
        padding: 12px;
        text-align: left; /* Align text to the left for better readability */
    }
}


/* Mobile text styling */
@media (max-width: 480px) {
    section.pro-releted p {
        font-size: 14px;
        line-height: 1.6;
    }
}

    
    #suggestions {
        margin-top: 100px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 10px;
    }
    .suggestion {
        background-color: #FFECC8; /* Orange theme adjustment */
        color: #ec6504;
        border: 1px solid #ec6504;
        border-radius: 20px;
        padding: 8px 12px;
        cursor: pointer;
        text-align: left;
        width: fit-content;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }
    .suggestion:hover {
        background-color: #ec6504;
        color: #fff;
    }
    </style>
    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1209485663860371');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1209485663860371&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

<!-- Promotional Popup Styles -->
<style>
.promo-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    z-index: 999999;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.promo-popup-overlay.show {
    opacity: 1;
    visibility: visible;
}

.promo-popup-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    transform: scale(0.7) translateY(50px);
    transition: all 0.3s ease;
    border: 3px solid #ec6504;
}

.promo-popup-overlay.show .promo-popup-container {
    transform: scale(1) translateY(0);
}

.promo-close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    background: #ff4757;
    color: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1000000;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.promo-close-btn:hover {
    background: #ff3742;
    transform: scale(1.1);
}

.promo-content {
    padding: 30px 25px 25px;
}

.promo-header {
    text-align: center;
    margin-bottom: 25px;
}

.promo-badge {
    background: linear-gradient(135deg, #ec6504, #ff8533);
    color: white;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 1px;
    display: inline-block;
    margin-bottom: 15px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.promo-title {
    color: #2c3e50;
    font-size: 24px;
    font-weight: bold;
    margin: 10px 0;
    line-height: 1.3;
}

.promo-subtitle {
    color: #7f8c8d;
    font-size: 14px;
    margin: 0;
}

.promo-body {
    margin-bottom: 25px;
}

.promo-offer-box {
    background: linear-gradient(135deg, #305724, #4a7c59);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
    position: relative;
    overflow: hidden;
}

.promo-offer-box::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.promo-discount, .promo-bonus {
    text-align: center;
    flex: 1;
}

.promo-percent, .promo-free {
    display: block;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
}

.promo-text, .promo-condition {
    font-size: 12px;
    opacity: 0.9;
}

.promo-plus {
    font-size: 24px;
    font-weight: bold;
    margin: 0 15px;
    opacity: 0.8;
}

.promo-features {
    margin-bottom: 20px;
}

.promo-feature {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-size: 14px;
    color: #2c3e50;
}

.promo-icon {
    margin-right: 10px;
    font-size: 16px;
}

/* Mobile Number Input Styles */
.promo-mobile-section {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    border: 2px solid #ec6504;
    text-align: center;
}

.promo-mobile-text {
    font-size: 16px;
    color: #2c3e50;
    margin-bottom: 15px;
    font-weight: 600;
}

.promo-mobile-input-group {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    gap: 5px;
}

.promo-country-code {
    background: #305724;
    color: white;
    padding: 12px 15px;
    border-radius: 8px 0 0 8px;
    font-weight: bold;
    font-size: 16px;
    border: 2px solid #305724;
}

.promo-mobile-input {
    flex: 1;
    max-width: 250px;
    padding: 12px 15px;
    border: 2px solid #305724;
    border-left: none;
    border-radius: 0 8px 8px 0;
    font-size: 16px;
    outline: none;
    transition: all 0.3s ease;
}

.promo-mobile-input:focus {
    border-color: #ec6504;
    box-shadow: 0 0 0 3px rgba(236, 101, 4, 0.1);
}

.promo-mobile-input::placeholder {
    color: #7f8c8d;
}

/* OTP Input Styles */
.promo-otp-section {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    border: 2px solid #ec6504;
    text-align: center;
}

.promo-otp-text {
    font-size: 16px;
    color: #2c3e50;
    margin-bottom: 20px;
    font-weight: 600;
}

.promo-otp-input-group {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
}

.promo-otp-input {
    width: 45px;
    height: 45px;
    border: 2px solid #305724;
    border-radius: 8px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    outline: none;
    transition: all 0.3s ease;
}

.promo-otp-input:focus {
    border-color: #ec6504;
    box-shadow: 0 0 0 3px rgba(236, 101, 4, 0.1);
    transform: scale(1.05);
}

.promo-otp-input:valid {
    border-color: #27ae60;
    background: #d5f4e6;
}

/* Verify Button Styles */
.promo-verify-btn {
    background: linear-gradient(135deg, #305724, #4a7c59);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(48, 87, 36, 0.3);
    position: relative;
    overflow: hidden;
}

.promo-verify-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(48, 87, 36, 0.4);
}

.promo-verify-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.promo-loader {
    display: inline-block;
}

/* Error Message Styles */
.promo-error-message {
    color: #e74c3c;
    font-size: 14px;
    margin-bottom: 10px;
    font-weight: 600;
    min-height: 20px;
}

/* Resend Section Styles */
.promo-resend-section {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}

.promo-resend-text {
    font-size: 14px;
    color: #7f8c8d;
    margin-bottom: 10px;
}

.promo-resend-btn {
    background: none;
    border: none;
    color: #ec6504;
    font-weight: bold;
    cursor: pointer;
    text-decoration: underline;
    font-size: 14px;
}

.promo-resend-btn:disabled {
    color: #bdc3c7;
    cursor: not-allowed;
    text-decoration: none;
}

.promo-change-number-btn {
    background: #f39c12;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.promo-change-number-btn:hover {
    background: #e67e22;
    transform: scale(1.05);
}

/* Step Animation */
.promo-step {
    opacity: 1;
    transform: translateX(0);
    transition: all 0.3s ease;
}

.promo-step.slide-out-left {
    opacity: 0;
    transform: translateX(-100%);
}

.promo-step.slide-in-right {
    opacity: 0;
    transform: translateX(100%);
}

.promo-step.slide-in-right.active {
    opacity: 1;
    transform: translateX(0);
}

.promo-code-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    border: 2px dashed #ec6504;
}

.promo-code-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.promo-code-label {
    font-size: 14px;
    color: #7f8c8d;
    margin-right: 10px;
}

.promo-code {
    background: #ec6504;
    color: white;
    padding: 8px 15px;
    border-radius: 8px;
    font-weight: bold;
    font-family: monospace;
    letter-spacing: 1px;
    flex: 1;
    text-align: center;
    margin: 0 10px;
}

.promo-copy-btn {
    background: #305724;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
}

.promo-copy-btn:hover {
    background: #4a7c59;
    transform: scale(1.05);
}

.promo-footer {
    text-align: center;
}

.promo-shop-btn {
    background: linear-gradient(135deg, #ec6504, #ff8533);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(236, 101, 4, 0.3);
}

.promo-shop-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(236, 101, 4, 0.4);
}

.promo-timer {
    font-size: 12px;
    color: #e74c3c;
    margin: 0;
    font-weight: bold;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .promo-popup-container {
        margin: 20px;
        max-width: none;
        width: calc(100% - 40px);
    }

    .promo-content {
        padding: 25px 20px 20px;
    }

    .promo-title {
        font-size: 20px;
    }

    .promo-offer-box {
        flex-direction: column;
        text-align: center;
    }

    .promo-plus {
        margin: 10px 0;
        transform: rotate(90deg);
    }

    .promo-code-box {
        flex-direction: column;
        gap: 10px;
    }

    .promo-code {
        margin: 0;
    }

    .promo-mobile-input-group {
        flex-direction: column;
        gap: 0;
    }

    .promo-country-code {
        border-radius: 8px 8px 0 0;
        border-bottom: none;
    }

    .promo-mobile-input {
        border-radius: 0 0 8px 8px;
        border-top: none;
        border-left: 2px solid #305724;
        max-width: none;
    }

    .promo-otp-input-group {
        gap: 5px;
    }

    .promo-otp-input {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }

    .promo-mobile-text, .promo-otp-text {
        font-size: 14px;
    }
}
</style>

</head>

<body class="home-1">

    <!-- Promotional Popup Modal -->
    <div id="promoPopup" class="promo-popup-overlay">
        <div class="promo-popup-container">
            <button class="promo-close-btn" onclick="closePromoPopup()">&times;</button>

            <!-- Step 1: Mobile Number Collection -->
            <div id="promoStep1" class="promo-content promo-step">
                <div class="promo-header">
                    <div class="promo-badge">EXCLUSIVE OFFER</div>
                    <h2 class="promo-title">🌿 Welcome to My Nutrify! 🌿</h2>
                    <p class="promo-subtitle">Get 25% OFF + FREE Delivery on Your First Order!</p>
                </div>

                <div class="promo-body">
                    <div class="promo-offer-box">
                        <div class="promo-discount">
                            <span class="promo-percent">25% OFF</span>
                            <span class="promo-text">on your first order</span>
                        </div>
                        <div class="promo-plus">+</div>
                        <div class="promo-bonus">
                            <span class="promo-free">FREE DELIVERY</span>
                            <span class="promo-condition">on orders above ₹399</span>
                        </div>
                    </div>

                    <div class="promo-mobile-section">
                        <p class="promo-mobile-text">📱 Enter your mobile number to unlock this exclusive offer:</p>
                        <div class="promo-mobile-input-group">
                            <div class="promo-country-code">+91</div>
                            <input type="tel" id="promoMobileNumber" class="promo-mobile-input" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[0-9]{10}">
                        </div>
                        <div id="promoMobileError" class="promo-error-message"></div>
                        <button class="promo-verify-btn" onclick="sendOTP()">
                            <span id="verifyBtnText">📲 Send OTP</span>
                            <span id="verifyBtnLoader" class="promo-loader" style="display: none;">⏳ Sending...</span>
                        </button>
                    </div>

                    <div class="promo-features">
                        <div class="promo-feature">
                            <span class="promo-icon">🔒</span>
                            <span>Your number is safe & secure</span>
                        </div>
                        <div class="promo-feature">
                            <span class="promo-icon">🎁</span>
                            <span>Instant discount code delivery</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="promoStep2" class="promo-content promo-step" style="display: none;">
                <div class="promo-header">
                    <div class="promo-badge">VERIFY OTP</div>
                    <h2 class="promo-title">🔐 Almost There!</h2>
                    <p class="promo-subtitle">We've sent a 6-digit OTP to <span id="displayMobileNumber"></span></p>
                </div>

                <div class="promo-body">
                    <div class="promo-otp-section">
                        <p class="promo-otp-text">Enter the OTP to unlock your discount:</p>
                        <div class="promo-otp-input-group">
                            <input type="text" class="promo-otp-input" maxlength="1" pattern="[0-9]">
                            <input type="text" class="promo-otp-input" maxlength="1" pattern="[0-9]">
                            <input type="text" class="promo-otp-input" maxlength="1" pattern="[0-9]">
                            <input type="text" class="promo-otp-input" maxlength="1" pattern="[0-9]">
                            <input type="text" class="promo-otp-input" maxlength="1" pattern="[0-9]">
                            <input type="text" class="promo-otp-input" maxlength="1" pattern="[0-9]">
                        </div>
                        <div id="promoOtpError" class="promo-error-message"></div>
                        <button class="promo-verify-btn" onclick="verifyOTP()">
                            <span id="otpVerifyBtnText">✅ Verify OTP</span>
                            <span id="otpVerifyBtnLoader" class="promo-loader" style="display: none;">⏳ Verifying...</span>
                        </button>

                        <div class="promo-resend-section">
                            <p class="promo-resend-text">Didn't receive OTP?
                                <button class="promo-resend-btn" onclick="resendOTP()" id="resendOtpBtn" disabled>
                                    Resend in <span id="resendTimer">30</span>s
                                </button>
                            </p>
                            <button class="promo-change-number-btn" onclick="changeNumber()">📝 Change Number</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Success & Promo Code -->
            <div id="promoStep3" class="promo-content promo-step" style="display: none;">
                <div class="promo-header">
                    <div class="promo-badge">🎉 SUCCESS!</div>
                    <h2 class="promo-title">🌟 Congratulations! 🌟</h2>
                    <p class="promo-subtitle">Your exclusive discount is ready to use!</p>
                </div>

                <div class="promo-body">
                    <div class="promo-offer-box">
                        <div class="promo-discount">
                            <span class="promo-percent">25% OFF</span>
                            <span class="promo-text">on your first order</span>
                        </div>
                        <div class="promo-plus">+</div>
                        <div class="promo-bonus">
                            <span class="promo-free">FREE DELIVERY</span>
                            <span class="promo-condition">on orders above ₹399</span>
                        </div>
                    </div>

                    <div class="promo-features">
                        <div class="promo-feature">
                            <span class="promo-icon">🌱</span>
                            <span>100% Natural & Ayurvedic</span>
                        </div>
                        <div class="promo-feature">
                            <span class="promo-icon">🏆</span>
                            <span>Trusted by 50,000+ Customers</span>
                        </div>
                        <div class="promo-feature">
                            <span class="promo-icon">⚡</span>
                            <span>Fast & Secure Delivery</span>
                        </div>
                    </div>

                    <div class="promo-code-section">
                        <div class="promo-code-box">
                            <span class="promo-code-label">Your Exclusive Code:</span>
                            <span class="promo-code" id="promoCode">WELCOME25</span>
                            <button class="promo-copy-btn" onclick="copyPromoCode()">Copy</button>
                        </div>
                    </div>
                </div>

                <div class="promo-footer">
                    <button class="promo-shop-btn" onclick="startShopping()">
                        🛒 Start Shopping Now
                    </button>
                    <p class="promo-timer">⏰ Limited Time Offer - Expires in <span id="promoTimer">24:00:00</span></p>
                </div>
            </div>
        </div>
    </div>

    <?php include("components/header.php"); ?>
    <div id="chat-icon">
        <img src="image/doc.png" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%;">
        <span id="chat-notification">1</span>
        <span id="chat-online"></span>
    </div>



<div id="chat-widget">
    <div id="chat-header">
        <img src="image/doc.png" alt="Expert" class="avatar">
        <span>My Nutrify Herbal & Ayurveda</span>
        <button id="close-chat">&times;</button>
    </div>
    
    <div id="chat-body">
        <div class="bot-message">Hello!👋 I'm your Ayurvedic Expert.<b> How can I take care of your health?</b></div>
        <div id="suggestions">
            <button class="suggestion">Immunity kaise badhaye?</button>
            <button class="suggestion">Diabetes ko kaise manage karein?</button>
            <button class="suggestion">PCOS Ko Kaise Control Kare.</button>
            <button class="suggestion">Khoon ki kami kaise thik karein?</button>
            <button class="suggestion">Cholestrol Kaise control karein?</button>
        </div>
    </div>

    <div id="chat-input-container">
        <input type="text" id="chat-input" placeholder="Type your message...">
        <button id="send-btn">Send</button>
    </div>
</div>

    <div id="cart-popup" class="cart-popup-overlay">
    <div class="cart-popup-content">
        <button class="close-popup" onclick="$('#cart-popup').fadeOut();">
            &times;
        </button>
        <h3>Product added to your cart!</h3>
        <div class="cart-popup-actions">
            <a href="cart.php" class="btn-view-cart">View Cart</a>
            <a href="checkout.php" class="btn-checkout">Checkout</a>
        </div>
    </div>
</div>


    <!--home page slider start-->
    <section class="slider">
        <div class="home-slider owl-carousel owl-theme">
            <?php foreach($banner_data as $banners){ ?>
            <div class="items">
                <div class="img-back"
                    style="background-image:url('cms/images/banners/<?php echo htmlspecialchars($banners["PhotoPath"]); ?>');">
                    <div class="home-s-content slide-c-r">
                        <span><?php echo htmlspecialchars($banners["Title"]); ?></span>
                        <h1><?php echo htmlspecialchars($banners["ShortDescription"]); ?></h1>
                        <a href="products.php" class="btn btn-style1">Shop now</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

    </section>
    <!--home page slider start-->
    <!--Enhanced banner section - Krishna Style-->
    <section class="t-banner1 section-tb-padding" style="background: linear-gradient(135deg, #f8f9fa 0%, var(--white) 100%);">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="home-offer-banner">
                        <div class="o-t-banner">
                            <a href="product_details.php?ProductId=10" class="image-b">
                                <img class="img-fluid" src="cms/images/products/banner_1.webp" alt="banner image">
                            </a>
                            <div class="o-t-content">
                               <h6>Struggling with diabetes management?</h6>
                                <h6>Start Diabetic Care Juice Today for a healthier you.</h6>
                                <a href="product_details.php?ProductId=11" class="btn btn-style1">Explore More</a>
                            </div>
                        </div>
                        <div class="o-t-banner">
                            <a href="product_details.php?ProductId=12" class="image-b">
                                <img class="img-fluid" src="cms/images/products/banner_2.webp" alt="banner image">
                            </a>
                            <div class="o-t-content banner-color">
                                <h6>Struggling with PCOD/PCOS?</h6>
                                <h6>Start She Care Plus Today!</h6>
                                <a href="product_details.php?ProductId=12" class="btn btn-style1">Explore More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner end -->
    <!-- Enhanced Category Section - Krishna Style -->
    <section class="category-img1 section-t-padding section-b-padding" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="section-title">
                        <h2>Shop by <span class="highlight">Category</span></h2>
                        <p style="color: var(--text-light); font-size: 18px; margin-top: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">Discover our comprehensive range of natural and ayurvedic products, carefully categorized for your wellness journey.</p>
                    </div>
                    <div class="home-category owl-carousel owl-theme" style="margin-top: 50px;">
                        <?php
                            $FieldNames = array("SubCategoryId", "SubCategoryName", "PhotoPath");
                            $ParamArray = array();
                            $Fields = implode(",", $FieldNames);
                            $sub_category = $obj->MysqliSelect1("Select ".$Fields." from sub_category", $FieldNames, "", $ParamArray);
                            foreach ($sub_category as $category) {?>
                        <div class="items">
                            <div class="h-cate" style="text-align: center; padding: 10px;">
                                <a href="products.php?SubCategoryId=<?php echo urlencode($category["SubCategoryId"]); ?>" style="text-decoration: none;">
                                    <div style="width: 80px; height: 80px; margin: 0 auto 10px; border-radius: 50%; overflow: hidden;">
                                        <img src="cms/images/products/<?php echo htmlspecialchars($category["PhotoPath"]); ?>"
                                             alt="<?php echo htmlspecialchars($category["SubCategoryName"]); ?>"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <span style="color: #333; font-size: 14px; font-weight: 500;"><?php echo htmlspecialchars($category["SubCategoryName"]); ?></span>
                                </a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Category image slide -->
    <!-- Enhanced Trending Products Section - Krishna Style -->
    <section class="h-t-products1 section-t-padding section-b-padding" style="background: var(--white);">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="section-title">
                        <h2>Trending <span class="highlight">Products</span></h2>
                        <p style="color: var(--text-light); font-size: 18px; margin-top: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">Explore our most popular ayurvedic solutions, trusted by thousands of satisfied customers.</p>
                    </div>
                    <div class="trending-products owl-carousel owl-theme">
                        <?php 
                        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                        $ParamArray = array();
                        $Fields = implode(",", $FieldNames);

                        // Fetching product data
                        $product_data = $obj->MysqliSelect1(
                            "SELECT " . $Fields . " FROM product_master ORDER BY RAND() LIMIT 4", 
                            $FieldNames, 
                            "", 
                            $ParamArray
                        );

                        if (!empty($product_data)) {
                            foreach ($product_data as $products) {
                                $FieldNamesPrice = array("OfferPrice", "MRP");
                                $ParamArrayPrice = array($products["ProductId"]);
                                $FieldsPrice = implode(",", $FieldNamesPrice);

                                // Fetching product prices
                                $product_prices = $obj->MysqliSelect1(
                                    "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                    $FieldNamesPrice, 
                                    "i", 
                                    $ParamArrayPrice
                                );

                                $lowest_price = "N/A";
                                $mrp = "N/A";
                                $savings = 0;

                                if (!empty($product_prices)) {
                                    foreach ($product_prices as $product_price) {
                                        $current_offer_price = floatval($product_price["OfferPrice"]);
                                        $current_mrp = floatval($product_price["MRP"]);

                                        if ($current_offer_price > 0 && ($lowest_price === "N/A" || $current_offer_price < $lowest_price)) {
                                            $lowest_price = $current_offer_price;
                                        }
                                        if ($current_mrp > 0 && ($mrp === "N/A" || $current_mrp < $mrp)) {
                                            $mrp = $current_mrp;
                                        }
                                    }

                                    if ($mrp !== "N/A" && $lowest_price !== "N/A" && $mrp > $lowest_price) {
                                        $savings = $mrp - $lowest_price;
                                    }
                                }
                        ?>
                        <div class="items" style="background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow-light); transition: var(--transition); overflow: hidden; border: 1px solid var(--border-light); margin-top: 20px;">
                            <div class="tred-pro">
                                <div class="tr-pro-img" style="position: relative; overflow: hidden;">
                                    <a href="product_details.php?ProductId=<?php echo $products["ProductId"]; ?>" style="display: block;">
                                        <img class="img-fluid" src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>" alt="<?php echo htmlspecialchars($products["ProductName"]); ?>" style="width: 100%; height: 250px; object-fit: cover; transition: var(--transition);">
                                        <div style="position: absolute; top: 15px; right: 15px; background: var(--primary-orange); color: var(--white); padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600;">New</div>
                                    </a>
                                </div>
                            </div>
                            <div class="caption" style="padding: 20px;">
                                <h3 style="margin-bottom: 12px;"><a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>" style="color: var(--text-dark); text-decoration: none; font-size: 18px; font-weight: 600; line-height: 1.3; transition: var(--transition);"><?php echo htmlspecialchars($products["ProductName"]); ?></a></h3>
                                <div class="rating" style="margin-bottom: 15px;">
                                    <i class="fa fa-star" style="color: #ffc107; font-size: 14px;"></i>
                                    <i class="fa fa-star" style="color: #ffc107; font-size: 14px;"></i>
                                    <i class="fa fa-star" style="color: #ffc107; font-size: 14px;"></i>
                                    <i class="fa fa-star" style="color: #ffc107; font-size: 14px;"></i>
                                    <i class="fa fa-star-o" style="color: #ddd; font-size: 14px;"></i>
                                    <span style="color: var(--text-light); font-size: 14px; margin-left: 8px;">(4.0)</span>
                                </div>
                                <div class="pro-price" style="margin-bottom: 20px;">
                                    <span class="new-price" style="font-size: 20px; font-weight: 700; color: var(--primary-orange);">Starting from ₹<?php echo htmlspecialchars($lowest_price); ?></span>
                                    <?php if ($mrp !== "N/A"): ?>
                                        <span class="old-price" style="text-decoration: line-through; color: var(--text-light); font-size: 16px; margin-left: 8px;">₹<?php echo htmlspecialchars($mrp); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pro-btn text-center">
                                        <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="<?php echo $products["ProductId"]; ?>" style="width: 100%; justify-content: center;">
                                            <i class="fa fa-shopping-bag"></i>Add to Cart
                                        </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            }
                        } else {
                            echo "<p>No trending products available.</p>";
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Trending Products end -->
    <!-- watch and shop section -->
     
    <!-- Enhanced Our Products Tab Section - Krishna Style -->
    <section class="our-products-tab section-tb-padding" id="bestsellers-section" style="background: linear-gradient(135deg, var(--light-green) 0%, #f8f9fa 100%);">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="section-title">
                        <h2>Our <span class="highlight">Products</span></h2>
                        <p style="color: var(--text-light); font-size: 18px; margin-top: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">Choose from our carefully curated selection of premium ayurvedic products.</p>
                        <ul class="nav nav-tabs" style="border: none; justify-content: center; margin-top: 40px;">
                            <li class="nav-item" style="margin: 0 10px;">
                                <a class="nav-link active" data-bs-toggle="tab" href="#home" style="background: var(--primary-orange); color: var(--white); border: none; border-radius: 25px; padding: 12px 24px; font-weight: 600; transition: var(--transition);">SPECIAL OFFERS</a>
                            </li>
                            <li class="nav-item" style="margin: 0 10px;">
                                <a class="nav-link" data-bs-toggle="tab" href="#profile" style="background: var(--white); color: var(--text-dark); border: 2px solid var(--border-light); border-radius: 25px; padding: 12px 24px; font-weight: 600; transition: var(--transition);">NEW PRODUCTS</a>
                            </li>
                            <li class="nav-item" style="margin: 0 10px;">
                                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" style="background: var(--white); color: var(--text-dark); border: 2px solid var(--border-light); border-radius: 25px; padding: 12px 24px; font-weight: 600; transition: var(--transition);">BESTSELLER</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content pro-tab-slider">
                        <div class="tab-pane show active" id="home">
                            <div class="home-pro-tab swiper-container">
                                <div class="swiper-wrapper">
                                <div class="trending-products owl-carousel owl-theme">
                                    <?php 
                                        $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                                        $ParamArray = array();
                                        $Fields = implode(",", $FieldNames);
                                        $product_data = $obj->MysqliSelect1(
                                            "SELECT " . $Fields . " FROM product_master ORDER BY RAND() LIMIT 4", 
                                            $FieldNames, 
                                            "", 
                                            $ParamArray
                                        );

                                        foreach ($product_data as $products) {
                                            $FieldNamesPrice = array("OfferPrice", "MRP");
                                            $ParamArrayPrice = array($products["ProductId"]);
                                            $FieldsPrice = implode(",", $FieldNamesPrice);
                                            $product_prices = $obj->MysqliSelect1(
                                                "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                                $FieldNamesPrice, 
                                                "i", 
                                                $ParamArrayPrice
                                            );

                                            $lowest_price = PHP_INT_MAX; // Initialize to a high value
                                            $mrp = PHP_INT_MAX;          // Initialize to a high value
                                            $savings = 0;

                                            if (!empty($product_prices)) {
                                                foreach ($product_prices as $product_price) {
                                                    $current_offer_price = floatval($product_price["OfferPrice"]);
                                                    $current_mrp = floatval($product_price["MRP"]);

                                                    if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                                                        $lowest_price = $current_offer_price;
                                                    }
                                                    if ($current_mrp > 0 && $current_mrp < $mrp) {
                                                        $mrp = $current_mrp;
                                                    }
                                                }

                                                if ($lowest_price == PHP_INT_MAX) {
                                                    $lowest_price = "N/A";
                                                }
                                                if ($mrp == PHP_INT_MAX) {
                                                    $mrp = "N/A";
                                                }

                                                if ($mrp != "N/A" && $lowest_price != "N/A" && $mrp > $lowest_price) {
                                                    $savings = $mrp - $lowest_price;
                                                }
                                            }
                                    ?>
                                    <div class="items" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-top: 15px;">
                                        <div class="tred-pro">
                                            <div class="tr-pro-img">
                                                <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                                    <img class="img-fluid" src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>" alt="<?php echo htmlspecialchars($products["ProductName"]); ?>">
                                                    <img class="img-fluid additional-image" src="cms/images/products/<?php echo htmlspecialchars($products["PhotoPath"]); ?>" alt="<?php echo htmlspecialchars($products["ProductName"]); ?>">
                                                </a>
                                            </div>
                                           
                                        </div>
                                        <div class="caption">
                                            <h3><a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>"><?php echo htmlspecialchars($products["ProductName"]); ?></a></h3>
                                            <div class="rating">
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                            </div>
                                            <div class="pro-price">
                                                <span class="new-price">Starting from ₹<?php echo htmlspecialchars($lowest_price); ?></span>
                                                <?php if ($mrp != "N/A"): ?>
                                                <span class="old-price" style="text-decoration: line-through; color: #999;">₹<?php echo htmlspecialchars($mrp); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="pro-btn text-center" style="margin: 5px;">
                                                        <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="<?php echo htmlspecialchars($products['ProductId']); ?>">
                                                            <i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Add to Cart
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="profile">
                        <div class="home-pro-tab swiper-container">
                                <div class="swiper-wrapper">
                                <div class="trending-products owl-carousel owl-theme">
                                    <?php 
                                            $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                                            $ParamArray = array();
                                            $Fields = implode(",", $FieldNames);
                                            $product_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_master ORDER BY RAND() LIMIT 4", $FieldNames, "", $ParamArray);

                                            foreach($product_data as $products){
                                                $FieldNamesPrice = array("OfferPrice", "MRP");
                                                $ParamArrayPrice = array($products["ProductId"]);
                                                $FieldsPrice = implode(",", $FieldNamesPrice);
                                                $product_prices = $obj->MysqliSelect1(
                                                    "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                                    $FieldNamesPrice, 
                                                    "i", 
                                                    $ParamArrayPrice
                                                );

                                        ?>
                                    <div class="items" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-top:15px;">
                                        <div class="tred-pro">
                                            <div class="tr-pro-img">
                                                <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                                    <img class="img-fluid" src="cms/images/products/<?php echo $products["PhotoPath"]; ?>" alt="<?php echo $products["ProductName"]; ?>">
                                                    <img class="img-fluid additional-image" src="cms/images/products/<?php echo $products["PhotoPath"]; ?>" alt="<?php echo $products["ProductName"]; ?>"
                                                        alt="additional image">
                                                </a>
                                            </div>
                                            <?php 
                                            $lowest_price = PHP_INT_MAX; // Initialize to a high value
                                            $mrp = PHP_INT_MAX;          // Initialize to a high value
                                            $savings = 0;   
                                            if (!empty($product_prices)) {
                                                // Loop through all rows and find the lowest MRP and OfferPrice greater than 0
                                                foreach ($product_prices as $product_price) {
                                                    $current_offer_price = floatval($product_price["OfferPrice"]);
                                                    $current_mrp = floatval($product_price["MRP"]);
                                    
                                                    if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                                                        $lowest_price = $current_offer_price;
                                                    }
                                                    if ($current_mrp > 0 && $current_mrp < $mrp) {
                                                        $mrp = $current_mrp;
                                                    }
                                                }

                                                if ($lowest_price == PHP_INT_MAX) {
                                                    $lowest_price = "N/A";
                                                }
                                                if ($mrp == PHP_INT_MAX) {
                                                    $mrp = "N/A";
                                                }

                                                if ($mrp != "N/A" && $lowest_price != "N/A" && $mrp > $lowest_price) {
                                                    $savings = $mrp - $lowest_price;
                                                }
                                            }
                                          
                                            ?>
                                        </div>
                                        <div class="caption">
                                            <h3><a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>"><?php echo htmlspecialchars($products["ProductName"]); ?></a></h3>
                                            <div class="rating">
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                            </div>
                                            <?php
                                            echo '        <div class="pro-price">';
                                            echo '            <span class="new-price">Starting from ₹' . htmlspecialchars($lowest_price) . '</span>';
                                            if ($mrp != "N/A") {
                                                echo '            <span class="old-price" style="text-decoration: line-through; color: #999;">â‚¹' . htmlspecialchars($mrp) . '</span>';
                                            }
                                            echo '        </div>';
                                            echo '<div class="row">
                                                <div class="col-sm-12">
                                                    <div class="pro-btn text-center" style="margin: 5px;">
                                                        <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="'.htmlspecialchars($products['ProductId']).'">
                                                            <i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Add to Cart
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>';
                                            ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="contact">
                        <div class="home-pro-tab swiper-container">
                                <div class="swiper-wrapper">
                                <div class="trending-products owl-carousel owl-theme">
                                    <?php 
                                            $FieldNames = array("ProductId", "ProductName", "PhotoPath");
                                            $ParamArray = array();
                                            $Fields = implode(",", $FieldNames);
                                            $product_data = $obj->MysqliSelect1("SELECT " . $Fields . " FROM product_master ORDER BY RAND() LIMIT 4", $FieldNames, "", $ParamArray);

                                            foreach($product_data as $products){
                                                $FieldNamesPrice = array("OfferPrice", "MRP");
                                                $ParamArrayPrice = array($products["ProductId"]);
                                                $FieldsPrice = implode(",", $FieldNamesPrice);
                                                $product_prices = $obj->MysqliSelect1(
                                                    "SELECT " . $FieldsPrice . " FROM product_price WHERE ProductId = ?", 
                                                    $FieldNamesPrice, 
                                                    "i", 
                                                    $ParamArrayPrice
                                                );

                                        ?>
                                    <div class="items" style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-top:15px;">
                                        <div class="tred-pro">
                                            <div class="tr-pro-img">
                                                <a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>">
                                                    <img class="img-fluid" src="cms/images/products/<?php echo $products["PhotoPath"]; ?>" alt="<?php echo $products["ProductName"]; ?>">
                                                    <img class="img-fluid additional-image" src="cms/images/products/<?php echo $products["PhotoPath"]; ?>" alt="<?php echo $products["ProductName"]; ?>"
                                                        alt="additional image">
                                                </a>
                                            </div>
                                            <?php 
                                            $lowest_price = PHP_INT_MAX; // Initialize to a high value
                                            $mrp = PHP_INT_MAX;          // Initialize to a high value
                                            $savings = 0;   
                                            if (!empty($product_prices)) {
                                                // Loop through all rows and find the lowest MRP and OfferPrice greater than 0
                                                foreach ($product_prices as $product_price) {
                                                    $current_offer_price = floatval($product_price["OfferPrice"]);
                                                    $current_mrp = floatval($product_price["MRP"]);
                                    
                                                    if ($current_offer_price > 0 && $current_offer_price < $lowest_price) {
                                                        $lowest_price = $current_offer_price;
                                                    }
                                                    if ($current_mrp > 0 && $current_mrp < $mrp) {
                                                        $mrp = $current_mrp;
                                                    }
                                                }

                                                if ($lowest_price == PHP_INT_MAX) {
                                                    $lowest_price = "N/A";
                                                }
                                                if ($mrp == PHP_INT_MAX) {
                                                    $mrp = "N/A";
                                                }

                                                if ($mrp != "N/A" && $lowest_price != "N/A" && $mrp > $lowest_price) {
                                                    $savings = $mrp - $lowest_price;
                                                }
                                            }
                                          
                                            ?>
                                        </div>
                                        <div class="caption">
                                            <h3><a href="product_details.php?ProductId=<?php echo htmlspecialchars($products["ProductId"]); ?>"><?php echo htmlspecialchars($products["ProductName"]); ?></a></h3>
                                            <div class="rating">
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star c-star"></i>
                                                <i class="fa fa-star-o"></i>
                                                <i class="fa fa-star-o"></i>
                                            </div>
                                            <?php
                                            echo '        <div class="pro-price">';
                                            echo '            <span class="new-price">Starting from ₹' . htmlspecialchars($lowest_price) . '</span>';
                                            if ($mrp != "N/A") {
                                                echo '            <span class="old-price" style="text-decoration: line-through; color: #999;">₹' . htmlspecialchars($mrp) . '</span>';
                                            }
                                            echo '        </div>';
                                            echo '<div class="row">
                                                <div class="col-sm-12">
                                                    <div class="pro-btn text-center" style="margin: 5px;">
                                                        <a href="javascript:void(0);" class="btn btn-style1 add-to-cart-session" data-product-id="'.htmlspecialchars($products['ProductId']).'">
                                                            <i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Add to Cart
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>';
                                            ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="comparison-section">
        <div class="comparison-box">
            <div class="vs-badge">VS</div>

            <div class="comparison-column left">
                <div class="brand-header">
                    <img src="image/main_logo.png" alt="Brand Logo" class="brand-logo">
                </div>
                <ul class="comparison-list">
                    <li><img src="cms/images/products/my_1.png">
                        Pure & Natural Ingredients</li>
                    <li><img src="cms/images/products/my_2.png"> No Artificial Additives</li>
                    <li><img src="cms/images/products/my_3.png"> Lab-Tested & Certified</li>
                    <li><img src="cms/images/products/my_4.png"> Eco-Friendly & BPA-Free Packaging</li>
                    <li><img src="cms/images/products/my_5.png"> Manufactured in GMP & ISO Certified Facilities</li>
                    <li><img src="cms/images/products/my_6.png"> Trusted & Transparent Brand</li>

                </ul>
            </div>
            <div class="comparison-column right">
                <h2 class="others-heading">Others</h2>
                <ul class="comparison-list">
                    <li><img src="cms/images/products/other_1.svg"> Contains Synthetic Ingredients</li>
                    <li><img src="cms/images/products/other_2.svg"> Low-Quality & Diluted Formulations</li>
                    <li><img src="cms/images/products/other_3.png"> Not Lab-Tested</li>
                    <li><img src="cms/images/products/other_4.png"> Substandard Packaging </li>
                    <li><img src="cms/images/products/other_5.png">Unverified Manufacturing Practices </li>
                    <li><img src="cms/images/products/other_6.png"> Lack of Brand Transparency</li>

                </ul>
            </div>
        </div>
    </section>


    <section class="section-b-padding pro-releted">

        <div class="section-title">
            <h2 style="margin-top: 75px;">Your Certified <span class="highlight">Trusted Product</span></h2>
        </div>
        <p class="cart-para">
            Established in 2013, we are a trusted manufacturer and
            supplier of Ayurvedic and herbal products in Sangli,
            Maharashtra. Our mission is to improve lives with
            high-quality, natural healthcare solutions while ensuring
            100% customer satisfaction. Our GMP-certified
            manufacturing unit, approved by the Ayush Department,
            Govt. of India, follows strict quality standards. With a
            dedicated R&D lab and quality control team, we ensure
            purity and effectiveness in every product.
        </p>
        <div class="use-container">
            <div class="use-cart">
                <div>
                    <img src="cms/images/products/Make In India.png" alt="">
                    <p>Make in India </p>
                </div>
            </div>
            <div class="use-cart">
                <div>
                    <img src="cms/images/products/Bpa free.png" alt="">
                    <p>BPA Free </p>
                </div>
            </div>
            <div class="use-cart">
                <div>
                    <img src="cms/images/products/Halal.png" alt="">
                    <p>Halal Certified</p>
                </div>
            </div>
            <div class="use-cart">
                <div>
                    <img src="cms/images/products/Gmp.png" alt="">
                    <p>GMP Certified</p>
                </div>
            </div>
            <div class="use-cart">
                <div>
                    <img src="cms/images/products/Fssai.png" alt="">
                    <p>Fssai Approved </p>
                </div>
            </div>
            <div class="use-cart">
                <div>
                    <img src="cms/images/products/How To Use E Commerce.png" alt="">
                    <p>Ayush Approved </p>
                </div>
            </div>
        </div>
    </section>
    <section class="section-b-padding pro-releted">
        <div class="section-title">
            <h2>Committed to Your <span class="highlight">Wellness and Well-Being</span></h2>
        </div>
        <p class="cart-para">At My Nutrify Herbal and Ayurveda, we stay true to tradition,
            creating products using the Classical Ayurvedic process.
            Made with handpicked ingredients from select farms, every
            product is chemical-free and carefully tested for quality.
            With the expertise of our Ayurveda practitioners, experts,
            and researchers, we ensure our products support your
            health, vitality, and well-being.</p>
        <p class="cart-para">Our guiding mantra, <span></span>reflects our belief that health is the greatest wealth.
            Experience Ayurveda in its purest form because your
            well-being comes first!</p>
    </section>
    </section>

    <!-- Banner Image Section -->
    <section class="banner-section">
        <img src="cms/images/banners/banner.jpg" alt="5% OFF - Use Code SAVE5 on orders above ₹499">
    </section>

    <section class="offer-main">
        <div class="offer-product-card">
            <div class="offer-product-info">
                <p class="offer-category">DIABETIC WELLNESS</p>
                <h2 class="offer-product-title">Diabetic Care Juice</h2>
                <p class="offer-description">More than 13 lakh happy customers have solved their diabetic issues with
                    blend of
                    11 powerful herbs.</p>
                <a href="product_details.php?ProductId=10" class="offer-shop-btn">Shop now</a>
            </div>

            <div class="offer-product-image">
                <div class="offer-badge">New</div>
                <div class="offer-price">₹549</div>
                <img src="cms/images/products/Diabteic-Care.jpg" alt="Diabic Care Juice">
            </div>
        </div>
        <div class="offer-product-card">
            <div class="offer-product-info">
                <p class="offer-category">Cardiac wellness
                </p>
                <h2 class="offer-product-title">Cholesterol Care Juice
                </h2>
                <p class="offer-description">Powerful blend of 5 natural ingredients for healthy cholesterol levels.
                    Healthy juice for Healthy heart.</p>
                <a href="product_details.php?ProductId=9" class="offer-shop-btn">Shop now</a>
            </div>

            <div class="offer-product-image">
                <div class="offer-badge">New</div>
                <div class="offer-price">₹599</div>
                <img src="cms/images/products/Img_4634.jpg" alt="Diabetic Care Juice">
            </div>
        </div>
    </section>
     <section class="combo-container">


        <div class="combo-card">
            <div class="combo-card-content">
                <p class="combo-section-title">BUY TOGETHER</p>
                <h2 class="combo-main-title">Combo</h2>
                <p class="combo-view-all">VIEW ALL</p>
            </div>
            <div class="combo-card-images">
                <div class="combo-carousel-container">
                    <button class="combo-carousel-button prev">❮</button>
                    <div class="combo-carousel-track">
                        <div class="combo-carousel-item">
                        <a href="product_details.php?ProductId=22"> <img src="cms/images/products/6726.jpg" alt="Product 1"> </a>
                        </div>
                        <div class="combo-carousel-item">
                           <a href="product_details.php?ProductId=15"> <img src="cms/images/products/7579.jpg" alt="Product 2"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=14"><img src="cms/images/products/9976.jpg" alt="Product 3"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=6"><img src="cms/images/products/6526.png" alt="Product 4"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=23"><img src="cms/images/products/2081.jpg" alt="Product 5"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=10"><img src="cms/images/products/4772.jpg" alt="Product 6"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=25"></a><img src="cms/images/products/1368.jpg" alt="Product 7">
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=9"><img src="cms/images/products/9444.jpg" alt="Product 8"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=11"><img src="cms/images/products/7133.jpg" alt="Product 9"></a>
                        </div>
                    </div>
                    <button class="combo-carousel-button next">❯</button>
                </div>

                <div class="plus-sign">+</div>

                <div class="combo-carousel-container">
                    <button class="combo-carousel-button prev">❮</button>
                    <div class="combo-carousel-track">
                     <div class="combo-carousel-item">
                           <a href="product_details.php?ProductId=15"> <img src="cms/images/products/7579.jpg" alt="Product 2"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=14"><img src="cms/images/products/9976.jpg" alt="Product 3"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=6"><img src="cms/images/products/6526.png" alt="Product 4"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=23"><img src="cms/images/products/2081.jpg" alt="Product 5"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=10"><img src="cms/images/products/4772.jpg" alt="Product 6"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=25"></a><img src="cms/images/products/1368.jpg" alt="Product 7">
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=9"><img src="cms/images/products/9444.jpg" alt="Product 8"></a>
                        </div>
                        <div class="combo-carousel-item">
                            <a href="product_details.php?ProductId=11"><img src="cms/images/products/7133.jpg" alt="Product 9"></a>
                        </div>
                    </div>
                    <button class="combo-carousel-button next">❯</button>
                </div>
            </div>
        </div>
        <div class="marquee-container">
        <div class="marquee-content">
            <div class="marquee-item">
                <p>ISO Certified</p>
                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">
            </div>

            <div class="marquee-item">
                <p>No Added Sugar</p>
                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">
            </div>

            <div class="marquee-item">
                <p>GMP Certified</p>
                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">
            </div>

            <div class="marquee-item">
                <p>No Extracts Used</p>
                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">
            </div>

            <div class="marquee-item">
                <p>Gluten Free</p>
                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">
            </div>

            <div class="marquee-item">
                <p>BPA Free</p>
                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">
            </div>

            <div class="marquee-item">
                <p>Best in Quality</p>
                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">
            </div>

        </div>
    </div>
   
 <section class="testimonia-head">
        <div class="section-title">
            <h2 style="padding-top: 150px;">From Our <span class="highlight">Customers</span></h2>
        </div>
        <div id="testimonialCarousel" class="carousel slide testimonia-carousel" data-bs-ride="carousel"
            data-bs-interval="4000">

            <!-- Indicators -->
            <ol class="carousel-indicators testimonia-carousel-indicators">
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="1"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="2"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="3"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="4"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="5"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="6"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="7"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="8"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="9"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="10"></li>
                <li data-bs-target="#testimonialCarousel" data-bs-slide-to="11"></li>
            </ol>

            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active testimonia-carousel-item">
                    <p class="testimonia-testimonial">"Very good product! I have been using it for two months now, and I
                        can feel a great boost in my energy levels. Thanks to My Nutrify for this amazing supplement!"
                    </p>
                    <p class="testimonia-overview"><b>Aarav Sharma</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"I have completed three bottles, and the results are fantastic! My
                        digestion has improved, and I feel more active. My Nutrify truly delivers quality!"</p>
                    <p class="testimonia-overview"><b>Neha Patel</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"Been using My Nutrify's product for four days now, and I already
                        feel a difference, more energy, no bloating, and improved digestion. I love it!"</p>
                    <p class="testimonia-overview"><b>Rahul Desai</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"Not just a good product, but an excellent one! This is my fifth
                        bottle, and my health has improved significantly. My Nutrify is now my go-to brand!"</p>
                    <p class="testimonia-overview"><b>Priya Mehta</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 5 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"I was struggling with fatigue, but after using My Nutrify's
                        product for a month, my energy levels are back to normal! Highly recommended."</p>
                    <p class="testimonia-overview"><b>Rohan Verma</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 6 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"My sugar levels are now stable, and I have reduced my allopathic
                        medicine dosage significantly. Thanks to My Nutrify for this life-changing product!"</p>
                    <p class="testimonia-overview"><b>Arjun Singh</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 7 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"I feel lighter, more energetic, and my digestion has improved
                        significantly. My Nutrify's product has made a huge difference in my daily life."</p>
                    <p class="testimonia-overview"><b>Megha Kapoor</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 8 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"After just two weeks of using My Nutrify, I noticed a significant
                        boost in my energy and focus. This product is a game-changer!"</p>
                    <p class="testimonia-overview"><b>Simran Kaur</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 9 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"This product is excellent! I have been using it for the last
                        three months and have completed three bottles. My blood cholesterol was 324, and now it's 146.
                        Grateful to My Nutrify for such a wonderful product."</p>
                    <p class="testimonia-overview"><b>Sahil Mujawar</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 10 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"I received this product four days ago, and I must say it's
                        fantastic! I feel a surge of energy, no bloating, and improved digestion. Thank you, My Nutrify,
                        for this amazing product."</p>
                    <p class="testimonia-overview"><b>Gouri Patil</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 11 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"Not just a good product, but a highly recommended one! I'm now on
                        my fifth bottle. My sugar levels yesterday were Fasting - 104 and PP - 130, and I have stopped
                        taking allopathic medicine. Thanks to My Nutrify!"</p>
                    <p class="testimonia-overview"><b>Akash Kamble</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

                <!-- Slide 12 -->
                <div class="carousel-item testimonia-carousel-item">
                    <p class="testimonia-testimonial">"I absolutely love this product! Been using it for a while now,
                        and I feel more energetic and active throughout the day. My Nutrify has truly made a difference
                        in my health!"</p>
                    <p class="testimonia-overview"><b>Manpreet Singh</b></p>
                    <div class="testimonia-star-rating">
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                            class="fa fa-star"></i><i class="fa fa-star"></i>
                    </div>
                </div>

            </div>

            <!-- Controls -->
            <a class="carousel-control-prev testimonia-carousel-control-prev" href="#testimonialCarousel" role="button"
                data-bs-slide="prev">
                <i class="fa fa-angle-left"></i>
            </a>
            <a class="carousel-control-next testimonia-carousel-control-next" href="#testimonialCarousel" role="button"
                data-bs-slide="next">
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </section>



    </section>
    <!-- Our Products Tab end -->
    <!-- Testimonial Start -->
    <section class="watch-shop-section">
        <div class="section-title">
            <h2>Watch and <span class="highlight">Shop</span></h2>
        </div>

        <div class="shop-video-carousel-container">
            <div class="shop-video-carousel">
                <!-- Product Card 1 -->
                <div class="shop-product-card" onclick="showProductModal('S9AQrirSq6U', 'She Care Plus', 'â‚¹499', 'â‚¹549')">
                    <div class="shop-video-container">
                        <iframe
                            src="https://www.youtube.com/embed/S9AQrirSq6U?autoplay=1&mute=1&loop=1&controls=0&modestbranding=1"
                            frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope"></iframe>
                    </div>
                    <div class="shop-product-info">
                        <h3>She Care Plus</h3>
                        <div class="shop-price-container">
                            <span class="shop-current-price">₹499</span>
                            <span class="shop-original-price">₹549</span>
                            <span class="shop-discount">₹50 off</span>
                        </div>
                        <button class="shop-buy-button">Buy Now</button>
                    </div>
                    <div class="shop-engagement-bar">
                        <span class="shop-views">3.2M views</span>
                        <div class="shop-actions">
                            <button class="shop-icon-button"><i class="fas fa-heart"></i></button>
                            <button class="shop-icon-button"><i class="fas fa-share"></i></button>
                        </div>
                    </div>
                </div>
                <div class="shop-product-card" onclick="showProductModal('S9AQrirSq6U', 'She Care Plus', 'â‚¹499', 'â‚¹549')">
                    <div class="shop-video-container">
                        <iframe
                            src="https://www.youtube.com/embed/uSgQis1q6M0?autoplay=1&mute=1&loop=1&playlist=uSgQis1q6M0&controls=0&showinfo=0&modestbranding=1"
                            frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope"></iframe>
                    </div>
                    <div class="shop-product-info">
                        <h3>Diabetic Care Juice</h3>
                        <div class="shop-price-container">
                            <span class="shop-current-price">₹549</span>
                            <span class="shop-original-price">₹599</span>
                            <span class="shop-discount">₹50 off</span>
                        </div>
                        <button class="shop-buy-button">Buy Now</button>
                    </div>
                    <div class="shop-engagement-bar">
                        <span class="shop-views">1.9M views</span>
                        <div class="shop-actions">
                            <button class="shop-icon-button"><i class="fas fa-heart"></i></button>
                            <button class="shop-icon-button"><i class="fas fa-share"></i></button>
                        </div>
                    </div>
                </div>
                <div class="shop-product-card" onclick="showProductModal('S9AQrirSq6U', 'She Care Plus', '₹499', '₹549')">
                    <div class="shop-video-container">
                        <iframe
                        src="https://www.youtube.com/embed/vB6kY9gnHoc?autoplay=1&mute=1&loop=1&playlist=vB6kY9gnHoc&controls=0&showinfo=0&modestbranding=1"
                        frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope"></iframe>
                    </div>
                    <div class="shop-product-info">
                        <h3>Pure Shilajit Resin</h3>
                        <div class="shop-price-container">
                            <span class="shop-current-price">₹499</span>
                            <span class="shop-original-price">₹549</span>
                            <span class="shop-discount">₹50 off</span>
                        </div>
                        <button class="shop-buy-button">Buy Now</button>
                    </div>
                    <div class="shop-engagement-bar">
                        <span class="shop-views">1.2M views</span>
                        <div class="shop-actions">
                            <button class="shop-icon-button"><i class="fas fa-heart"></i></button>
                            <button class="shop-icon-button"><i class="fas fa-share"></i></button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

       
    </section>
    <!--<section>-->
    <!--    <div class="marquee-container">-->
    <!--        <div class="marquee-content">-->
    <!--            <div class="marquee-item">-->
    <!--                <p>ISO Certified</p>-->
    <!--                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">-->
    <!--            </div>-->

    <!--            <div class="marquee-item">-->
    <!--                <p>No Added Sugar</p>-->
    <!--                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">-->
    <!--            </div>-->

    <!--            <div class="marquee-item">-->
    <!--                <p>GMP Certified</p>-->
    <!--                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">-->
    <!--            </div>-->

    <!--            <div class="marquee-item">-->
    <!--                <p>No Extracts Used</p>-->
    <!--                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">-->
    <!--            </div>-->

    <!--            <div class="marquee-item">-->
    <!--                <p>Gluten Free</p>-->
    <!--                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">-->
    <!--            </div>-->

    <!--            <div class="marquee-item">-->
    <!--                <p>BPA Free</p>-->
    <!--                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">-->
    <!--            </div>-->

    <!--            <div class="marquee-item">-->
    <!--                <p>Best in Quality</p>-->
    <!--                <img src="//krishnaayurved.com/cdn/shop/files/starIcon_10x11.png?v=1721645924" alt="Star Icon">-->
    <!--            </div>-->

    <!--        </div>-->
    <!--    </div>-->

    <!--</section>-->
    <!-- Our Products Tab end -->
    <!-- Testimonial Start -->
    <!-- <section class="section-tb-padding testimonial-bg1">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="section-title">
                        <h2>Our customer say</h2>
                    </div>
                    <div class="testi-m owl-carousel owl-theme">
                        <div class="items">
                            <div class="testimonial-area">
                                <span class="tsti-title">Frendly support</span>
                                <p>I love your store! there is the largest selection of products of the exceptional
                                    quality and the lowest prices like in no other store.</p>
                                <div class="testi-name">
                                    <h6>Williamson</h6>
                                    <span>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="items">
                            <div class="testimonial-area">
                                <span class="tsti-title">Frendly support</span>
                                <p>I love your store! there is the largest selection of products of the exceptional
                                    quality and the lowest prices like in no other store.</p>
                                <div class="testi-name">
                                    <h6>Jessica joy</h6>
                                    <span>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="items">
                            <div class="testimonial-area">
                                <span class="tsti-title">Frendly support</span>
                                <p>I love your store! there is the largest selection of products of the exceptional
                                    quality and the lowest prices like in no other store.</p>
                                <div class="testi-name">
                                    <h6>Jane deo</h6>
                                    <span>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                        <i class="fa fa-star e-star"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- Testimonial end -->
    <!-- Blog start -->
   <section class="section-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="section-title">
                        <h2>Recent <span class="highlight">Blogs</span></h2>
                    </div>
                    <div class="home-blog owl-carousel owl-theme">
                    <?php
                       $FieldNames = array("BlogId", "BlogTitle", "BlogDate", "Description", "PhotoPath", "IsActive");
                       $ParamArray = array();
                       $Fields = implode(",", $FieldNames);
           
                       $blog_data = $obj->MysqliSelect1(
                           "SELECT " . $Fields . " FROM blogs_master WHERE IsActive = 'Y' ORDER BY BlogDate DESC LIMIT 3",
                           $FieldNames,
                           "",
                           $ParamArray
                       );
           
                       if (!empty($blog_data)) {
                           foreach ($blog_data as $blogs) {
                               $description = $blogs["Description"];
                               $short_description = substr($description, 0, 200);
                               if (strlen($description) > 200) {
                                   $short_description .= "...";
                               }
                    ?>
                        <div class="items">
                            <div class="blog-start">
                                            <div class="blog-post">
                                                <div class="blog-image">
                                                    <a href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>">
                                                        <img src="cms/images/blogs/<?php echo htmlspecialchars($blogs["PhotoPath"] ?? 'default.jpg'); ?>"
                                                             alt="<?php echo htmlspecialchars($blogs["BlogTitle"] ?? 'No Title'); ?>"
                                                             class="img-fluid">
                                                    </a>
                                                </div>
                                                <div class="blog-content">
                                                    <div class="blog-title">
                                                        <h6>
                                                            <a href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>">
                                                                <?php echo htmlspecialchars($blogs["BlogTitle"] ?? 'Untitled Blog'); ?>
                                                            </a>
                                                        </h6>
                                                        <span class="blog-admin">By <span class="blog-editor">My Nutrify Herbal & Ayurveda.</span></span>
                                                    </div>

                                                    <a href="blog_details.php?BlogId=<?php echo htmlspecialchars($blogs["BlogId"] ?? ''); ?>" class="read-link">
                                                        <span>Read more</span>
                                                        <i class="ti-arrow-right"></i>
                                                    </a>
                                                    <div class="blog-date-comment">
                                                        <span class="blog-date">
                                                            <?php
                                                            $blogDate = isset($blogs["BlogDate"]) ? new DateTime($blogs["BlogDate"]) : null;
                                                            echo $blogDate ? $blogDate->format('j M Y') : 'Unknown Date';
                                                            ?>
                                                        </span>
                                                        <a href="javascript:void(0)"><?php echo htmlspecialchars($blogs["CommentCount"] ?? '0'); ?> Comments</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        </div>
                    <?php
                           }
                       } else {
                    ?>
                        <div class="no-blogs">
                            <p>No blogs available at the moment. Please check back later!</p>
                        </div>
                    <?php
                       }
                    ?>
                    </div>
                    <?php if (!empty($blog_data)) { ?>
                    <div class="all-blog">
                        <a href="blogs.php" class="btn btn-style1">View all</a>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </section>
    <!-- Blog end -->
    <!-- News Letter start -->
    <?php
    // Check if CustomerId is not set or if the user has already seen the newsletter offer
    if (empty($_SESSION["CustomerId"]) && empty($_SESSION["newsletter_seen"])) {
        // Show the newsletter offer
        ?>
        
        <?php
        // Mark that the user has seen the newsletter section
        $_SESSION["newsletter_seen"] = true;
    }
    ?>

    <!-- News Letter end -->
    <!-- brand logo start -->
    <section class="section-tb-padding home-brand1">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="brand-carousel owl-carousel owl theme">
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/1.png" alt="amazon" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/2.png" alt="flipkart" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/3.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/4.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/5.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/6.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/1.png" alt="amazon" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/2.png" alt="flipkart" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/3.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/4.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/5.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="items">
                            <div class="brand-img">
                                <a href="javascript:void(0)">
                                    <img src="cms/images/platforms/6.png" alt="home brand" class="img-fluid">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    <!-- brand logo end -->

    <!-- We Are Also Available On Section - Krishna Style -->
    <section style="background: var(--white); padding: 60px 0; border-top: 1px solid var(--border-light);">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="text-center">
                        <h3 style="font-size: 28px; font-weight: 700; color: var(--text-dark); margin-bottom: 40px;">We Are Also Available On:</h3>
                        <div style="display: flex; justify-content: center; align-items: center; gap: 40px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px 25px; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow-light); transition: var(--transition); border: 1px solid var(--border-light);">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Amazon_logo.svg/200px-Amazon_logo.svg.png" alt="Amazon" style="height: 30px; object-fit: contain;">
                                <span style="font-weight: 600; color: var(--text-dark);">Amazon</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px 25px; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow-light); transition: var(--transition); border: 1px solid var(--border-light);">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Jio_Mart_Logo.svg/200px-Jio_Mart_Logo.svg.png" alt="Jio Mart" style="height: 30px; object-fit: contain;">
                                <span style="font-weight: 600; color: var(--text-dark);">Jio Mart</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px 25px; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow-light); transition: var(--transition); border: 1px solid var(--border-light);">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a1/Tata_1mg_logo.svg/200px-Tata_1mg_logo.svg.png" alt="Tata 1mg" style="height: 30px; object-fit: contain;">
                                <span style="font-weight: 600; color: var(--text-dark);">Tata 1mg</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px 25px; background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow-light); transition: var(--transition); border: 1px solid var(--border-light);">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/Snapdeal_logo.svg/200px-Snapdeal_logo.svg.png" alt="Snapdeal" style="height: 30px; object-fit: contain;">
                                <span style="font-weight: 600; color: var(--text-dark);">Snapdeal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- footer start -->
    <?php include("components/footer.php"); ?>
    <!-- footer end -->

    <!-- newsletter pop-up start - DISABLED to prevent conflict with promo popup -->
    <?php
    // Newsletter popup temporarily disabled to prevent conflict with promotional popup
    // Original code commented out to allow promo popup to work properly
    /*
    if (empty($_SESSION["CustomerId"]) && empty($_SESSION["newsletter_seen"])) {
        // Newsletter popup HTML was here
        $_SESSION["newsletter_seen"] = true;
    }
    */
    ?>
    <!-- newsletter pop-up end -->
    <!-- back to top end -->
    <div class="mm-fullscreen-bg"></div>
    <!-- jquery -->
    <script src="js/modernizr-2.8.3.min.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- bootstrap -->
    <script src="js/bootstrap.min.js"></script>
    <!-- popper -->
    <script src="js/popper.min.js"></script>
    <!-- fontawesome -->
    <script src="js/fontawesome.min.js"></script>
    <!-- owl carousal -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- swiper -->
    <script src="js/swiper.min.js"></script>
    <!-- custom -->
    <script src="js/custom.js"></script>
    <script>
       $(document).ready(function () {
            // Handle navigation to bestSellers section from Offers link
            if (window.location.hash === '#contact') {
                // Scroll to the section
                $('html, body').animate({
                    scrollTop: $('#bestsellers-section').offset().top - 100
                }, 1000);

                // Activate the BESTSELLER tab
                $('#contact-tab').tab('show');
            }

            // Handle clicks on Offers links
            $('a[href*="#contact"]').on('click', function(e) {
                e.preventDefault();

                // Scroll to the section
                $('html, body').animate({
                    scrollTop: $('#bestsellers-section').offset().top - 100
                }, 1000);

                // Activate the BESTSELLER tab
                $('#contact-tab').tab('show');

                // Update URL hash
                window.location.hash = 'contact';
            });
            // Add product to cart for logged-in users
            $('.add-to-cart').on('click', function () {
                var productId = $(this).data('product-id'); // Get product ID from data attribute

                $.ajax({
                    url: 'add_to_cart.php', // PHP file to handle the cart addition
                    type: 'POST',
                    data: {
                        action: 'add_to_cart',
                        productId: productId
                    },
                    success: function (response) {
                        try {
                            var data = JSON.parse(response);
                            if (data.status === 'success') {
                                // Show the added to cart popup
                                $('#cart-popup').fadeIn();

                                // Automatically hide popup after a few seconds
                                setTimeout(function () {
                                    $('#cart-popup').fadeOut(function () {
                                        location.reload(); // Reload the page after popup is hidden
                                    });
                                }, 3000);
                            } else {
                                alert(data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error('AJAX error:', status, error);
                    }
                });
            });
            */

            // OVERRIDE: Redirect to product page when Add to Cart is clicked on index page
            // Remove any existing event handlers first
            $(document).off('click', '.add-to-cart-session');

            // Add new event handler with higher priority
            $(document).on('click.redirect', '.add-to-cart-session', function (e) {
                e.preventDefault(); // Prevent default link behavior
                e.stopPropagation(); // Stop event bubbling
                e.stopImmediatePropagation(); // Stop other handlers

                var productId = $(this).data('product-id'); // Get product ID
                console.log('🔄 REDIRECTING to product page for Product ID:', productId);

                // Redirect to product details page
                if (productId) {
                    window.location.href = 'product_details.php?ProductId=' + productId;
                } else {
                    console.error('❌ Product ID not found');
                    alert('Product not found. Please try again.');
                }

                return false; // Ensure no other handlers run
            });

            // Keep the original AJAX cart functionality for other pages (commented out for index page)
            /*
            $(document).on('click', '.add-to-cart-session-ajax', function () { // Use different class for AJAX functionality
                var productId = $(this).data('product-id'); // Get product ID
                console.log('Product ID:', productId); // Debugging: Log the product ID

                $.ajax({
                    url: 'exe_files/add_to_cart_session.php', // PHP file to handle the cart addition in session
                    type: 'POST',
                    data: {
                        action: 'add_to_cart',
                        productId: productId
                    },
                    success: function (response) {
                        try {
                            var data = JSON.parse(response); // Parse the response
                            if (data.status === 'success') {
                                // Show the added to cart popup
                                $('#cart-popup').fadeIn();

                                // Automatically hide popup after a few seconds
                                setTimeout(function () {
                                    $('#cart-popup').fadeOut(function () {
                                        location.reload(); // Reload the page after popup is hidden
                                    });
                                }, 3000);
                            } else {
                                alert(data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e); // Log error if JSON parsing fails
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });


        const modal = document.getElementById('modal');
        const modalVideo = document.getElementById('modal-video');
        const modalTitle = document.getElementById('modal-title');
        const modalPrice = document.getElementById('modal-price');

        function showModal(title, price, oldPrice, videoSrc) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            modalVideo.src = videoSrc;
            modalTitle.textContent = title;
            modalPrice.innerHTML = `${price} <del>${oldPrice}</del>`;
        }
        function toggleMute() {
            const video = document.getElementById('modal-video');
            const muteIcon = document.getElementById('mute-unmute');
            
            if (video.muted) {
                video.muted = false;
                muteIcon.classList.remove('fa-volume-mute');
                muteIcon.classList.add('fa-volume-up');
            } else {
                video.muted = true;
                muteIcon.classList.remove('fa-volume-up');
                muteIcon.classList.add('fa-volume-mute');
            }
        }
        function closeModal(event) {
    if (event.target === document.getElementById('modal') || event.target.id === 'close-modal') {
        document.getElementById('modal').style.display = 'none';
    }
}

        function closeModal(event) {
            // Close modal only if clicked outside the content
            if (event.target === modal) {
                modal.classList.remove('active');
                modalVideo.pause();
                modalVideo.src = ''; // Stop the video
            }
        }

        const scrollableCards = document.querySelector('.scrollable-cards');

        function scrollLeft() {
            scrollableCards.scrollBy({ left: -320, behavior: 'smooth' });
        }

        function scrollRight() {
            scrollableCards.scrollBy({ left: 320, behavior: 'smooth' });
        }
</script>
 
    <script>
    // Chat Toggle
    document.getElementById("chat-icon").addEventListener("click", () => {
      document.getElementById("chat-widget").style.display = "flex";
      document.getElementById("chat-icon").style.display = "none";
    });

    document.getElementById("close-chat").addEventListener("click", () => {
      document.getElementById("chat-widget").style.display = "none";
      document.getElementById("chat-icon").style.display = "block";
    });

    // Message Handling
    document.getElementById("send-btn").addEventListener("click", sendMessage);
    document.getElementById("chat-input").addEventListener("keypress", (e) => {
      if (e.key === "Enter") sendMessage();
    });

    async function sendMessage() {
      const chatInput = document.getElementById("chat-input");
      const sendBtn = document.getElementById("send-btn");
      const userMsg = chatInput.value.trim();
      
      if (!userMsg) return;
    
      displayMessage(userMsg, "user");
      chatInput.value = "";
      
      chatInput.disabled = true;
      sendBtn.disabled = true;
    
      const typingElement = showTypingIndicator();
    
      try {
        // First try to use actual API
        const response = await fetch("chatbot_api.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ message: userMsg })
        });
        
        const data = await response.json();
        typingElement.remove();
    
        // Process API response
        if (data.response) {
          displayMessage(data.response, "bot");
        }
        if (data.products?.length > 0) {
          addProductCarousel(data.products);
        }
      } catch (error) {
        // Fallback to static mock data if API fails
        typingElement.remove();
        
        const mockResponse = {
          response: "For weakness, I recommend My Nutrify Herbal & Ayurveda's Pure Shilajit Resin. It boosts immunity and energy levels.",
          products: [
            {
              name: "Pure Shilajit Resin",
              price: 1499,
              image_url: "images/shilajit.png"
            },
            {
              name: "Ashwagandha Capsules",
              price: 899,
              image_url: "images/ashwagandha.png"
            },
            {
              name: "Triphala Powder",
              price: 599,
              image_url: "images/triphala.png"
            }
          ]
        };
    
        if (mockResponse.response) {
          displayMessage(mockResponse.response, "bot");
        }
        if (mockResponse.products?.length > 0) {
          addProductCarousel(mockResponse.products);
        }
      } finally {
        chatInput.disabled = false;
        sendBtn.disabled = false;
        chatInput.focus();
      }
    }
    

    function displayMessage(text, sender) {
      const chatBody = document.getElementById("chat-body");
      const messageDiv = document.createElement("div");
      
      messageDiv.className = `${sender}-message`;
      messageDiv.innerHTML = `<p>${text}</p>`;
      chatBody.appendChild(messageDiv);
      
      chatBody.scrollTo({
        top: chatBody.scrollHeight,
        behavior: 'smooth'
      });
    }

    function showTypingIndicator() {
      const chatBody = document.getElementById("chat-body");
      const typingDiv = document.createElement("div");
      
      typingDiv.className = "bot-message typing-indicator";
      typingDiv.innerHTML = `
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      `;
      
      chatBody.appendChild(typingDiv);
      chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: 'smooth' });
      
      return typingDiv;
    }

 function addProductCarousel(products) {
      const chatBody = document.getElementById("chat-body");
      
      if(products.length === 1) {
        // Single product layout
        const container = document.createElement('div');
        const product = products[0];
        
        container.innerHTML = `
          <div class="single-product-card">
            <img src="${product.image_url}" alt="${product.name}" class="single-product-image">
            <div class="single-product-name">${product.name}</div>
            <div class="single-product-price">${product.price.toLocaleString()}</div>
            <a href="${product.url}" class="single-product-add-btn" role="button">Buy Now</a>
          </div>
        `;
        chatBody.appendChild(container);
      } else {
        // Multiple products carousel
        const carousel = document.createElement('div');
        carousel.className = 'product-carousel';
        
        products.forEach(product => {
          const card = document.createElement("div");
          card.className = "product-card";
          card.innerHTML = `
            <img src="${product.image_url}" alt="${product.name}">
            <div class="product-name">${product.name}</div>
            <div class="product-price">â‚¹${product.price.toLocaleString()}</div>
            <a href="${product.url}" class="single-product-add-btn" role="button">Buy Now</a>
          `;
          carousel.appendChild(card);
        });
        chatBody.appendChild(carousel);
      }
      
      chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: 'smooth' });
    }
  </script>
  
  
  <script>
  $(document).ready(function() {
        console.log('Initializing testimonial carousel...');

        // Check if Bootstrap is loaded
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap 5 detected');
            // Bootstrap 5 syntax
            var testimonialCarousel = new bootstrap.Carousel(document.getElementById('testimonialCarousel'), {
                interval: 3000,
                pause: 'hover',
                ride: 'carousel'
            });
        } else if (typeof $.fn.carousel !== 'undefined') {
            console.log('Bootstrap 4/jQuery detected');
            // Bootstrap 4 syntax
            $('#testimonialCarousel').carousel({
                interval: 3000,
                pause: "hover",
                ride: "carousel"
            });

            // Ensure carousel starts automatically
            setTimeout(function() {
                $('#testimonialCarousel').carousel('cycle');
            }, 1000);
        } else {
            console.error('Bootstrap carousel not found!');
        }
    });

    // Convert Shorts URL to Embed URL
    function convertShortsToEmbed(shortsUrl) {
        const videoId = shortsUrl.split("/shorts/")[1]?.split("?")[0];
        return `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}&controls=0&showinfo=0&modestbranding=1`;
    }

    function showModal(title, price1, price2, shortsUrl) {
        const embedUrl = convertShortsToEmbed(shortsUrl);
        document.getElementById("modal-title").innerText = title;
        document.getElementById("modal-price").innerText = `${price1} ${price2}`;
        document.getElementById("modal-video").src = embedUrl;
        document.getElementById("modal").style.display = "flex";
    }

    function closeModal(event) {
        if (event.target.id === "modal") {
            document.getElementById("modal").style.display = "none";
            document.getElementById("modal-video").src = ""; // Stop video
        }
    }

    function toggleMute() {
        const iframe = document.getElementById("modal-video");
        const icon = document.getElementById("mute-unmute");
        // Unfortunately, YouTube iframe mute can't be toggled easily after load.
        // So instead, reload with mute=0 or mute=1 accordingly (basic workaround)
        let src = iframe.src;
        if (src.includes("mute=1")) {
            iframe.src = src.replace("mute=1", "mute=0");
            icon.classList.remove("fa-volume-mute");
            icon.classList.add("fa-volume-up");
        } else {
            iframe.src = src.replace("mute=0", "mute=1");
            icon.classList.remove("fa-volume-up");
            icon.classList.add("fa-volume-mute");
        }
    }

    // Auto carousel scroll
    let scrollInterval;

    function startAutoSlide() {
        scrollInterval = setInterval(() => scrollRight(), 3000);
    }

    function stopAutoSlide() {
        clearInterval(scrollInterval);
    }

    const scrollable = document.querySelector('.index-scrollable-cards');
    if (scrollable) {
        scrollable.addEventListener('mouseenter', stopAutoSlide);
        scrollable.addEventListener('mouseleave', startAutoSlide);
        startAutoSlide();
    }

    // Drag scroll
    let isDown = false,
        startX, initialScrollLeft;
    if (scrollable) {
        scrollable.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - scrollable.offsetLeft;
            initialScrollLeft = scrollable.scrollLeft;
        });
        scrollable.addEventListener('mouseleave', () => isDown = false);
        scrollable.addEventListener('mouseup', () => isDown = false);
        scrollable.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollable.offsetLeft;
            const walk = (x - startX) * 2;
            scrollable.scrollLeft = initialScrollLeft - walk;
        });
    }

    // Double Tap Like
    document.querySelectorAll('.index-product-card').forEach(card => {
        let lastTap = 0;
        card.addEventListener('touchend', (e) => {
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTap;
            if (tapLength < 500 && tapLength > 0) {
                const heart = card.querySelector('.fa-heart');
                heart.classList.add('liked');
                setTimeout(() => heart.classList.remove('liked'), 500);
            }
            lastTap = currentTime;
        });
    });
    </script>
    <script>
    document.querySelectorAll('.suggestion').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('chat-input').value = button.innerText;
        });
    });
</script>
<script>
    window.addEventListener("DOMContentLoaded", () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get("open_chat") === "1") {
            const chatIcon = document.getElementById("chat-icon");
            if (chatIcon) {
                chatIcon.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => {
                    chatIcon.click();
                }, 300);
            }
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const carousels = document.querySelectorAll('.combo-carousel-container');

    carousels.forEach((carousel) => {
        const track = carousel.querySelector('.combo-carousel-track');
        const items = carousel.querySelectorAll('.combo-carousel-item');
        const prevButton = carousel.querySelector('.combo-carousel-button.prev');
        const nextButton = carousel.querySelector('.combo-carousel-button.next');

        let currentIndex = 0;
        const itemWidth = items[0].offsetWidth + 15; // include margin/gap

        const maxScrollIndex = items.length - Math.floor(carousel.offsetWidth / itemWidth);

        const updateCarousel = () => {
            track.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
        };

        prevButton.addEventListener('click', () => {
            currentIndex = Math.max(0, currentIndex - 1);
            updateCarousel();
        });

        nextButton.addEventListener('click', () => {
            currentIndex = Math.min(maxScrollIndex, currentIndex + 1);
            updateCarousel();
        });

        // Image selection logic for this carousel
        items.forEach(item => {
            const img = item.querySelector('img');
            img.addEventListener('click', function () {
                // Remove selected class from all images in this carousel
                carousel.querySelectorAll('.combo-carousel-item img').forEach(i => i.classList.remove('selected'));
                // Add selected class to the clicked image
                this.classList.add('selected');
                console.log('Selected:', this.alt);
            });
        });
    });
});

// Krishna Ayurved Style Section Title Animations
document.addEventListener('DOMContentLoaded', function() {
    // Create intersection observer for section titles
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = '0.2s';
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all section titles
    const sectionTitles = document.querySelectorAll('.section-title h2');
    sectionTitles.forEach(function(title, index) {
        // Add staggered animation delay
        title.style.animationDelay = (index * 0.1) + 's';
        observer.observe(title);
    });

    // Add hover effects for highlighted text
    const highlightElements = document.querySelectorAll('.section-title h2 .highlight');
    highlightElements.forEach(function(element) {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
        });

        element.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-SJ487NCVTQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-SJ487NCVTQ');
  
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');


dataLayer.push({
  'event': 'gtm.load',
  'pageCategory': 'home', 
  'pageTitle': 'Home Page'
  
  
});
dataLayer.push({event: "gtm.load", ...})
{
  event: "gtm.load",
  gtm: {uniqueEventId: 13, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.dom", ..,,,ghgfhfghfgh.})

{
  event: "gtm.dom",
  gtm: {uniqueEventId: 12, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle: "Home Page"
}
dataLayer.push({event: "gtm.load", ...})
{
  event: "gtm.load",
  gtm: {uniqueEventId: 11, start: 1748177630404, priorityId: undefined},
  pageCategory: "home",
  pageTitle:"Home Page"
});

// Google Tag Manager events
dataLayer.push({
  event: "gtm.js",
  gtm: {uniqueEventId: 10, start: 1748177630404, priorityId: undefined}
});
dataLayer.push({
  event: "gtm.js",
  gtm: {uniqueEventId: 3, start: 1748177630404, priorityId: undefined}
});
dataLayer.push({event: "gtm.init", gtm: {uniqueEventId: 2}});
dataLayer.push({event: "gtm.init_consent", gtm: {uniqueEventId: 1}});

</script>
<!-- End Google Tag Manager -->

    <!-- YouTube IFrame Player API -->
<script src="https://www.youtube.com/iframe_api"></script>

<!-- Dynamic Rewards Element - Krishna Ayurveda Style -->
<div class="rewards-element" id="dynamic-rewards-element" onclick="openRewardsPage()">
    <div class="rewards-content">
        <div class="icon">
            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                <polyline points="20 12 20 22 4 22 4 12"></polyline>
                <rect x="2" y="7" width="20" height="5"></rect>
                <line x1="12" y1="22" x2="12" y2="7"></line>
                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
            </svg>
        </div>
        <div class="text" id="rewards-text">Rewards</div>
    </div>
</div>

<!-- Rewards Modal - Krishna Ayurveda Style -->
<div class="rewards-modal" id="rewards-modal" onclick="closeRewardsModal()">
    <div class="rewards-modal-container" onclick="event.stopPropagation()">
        <div class="rewards-modal-content">
            <!-- Content will be dynamically loaded -->
        </div>
    </div>
</div>

<script>
// Dynamic Rewards Element - Krishna Ayurveda Style Implementation
class DynamicRewardsElement {
    constructor() {
        this.element = document.getElementById('dynamic-rewards-element');
        this.textElement = document.getElementById('rewards-text');
        this.isLoggedIn = false;
        this.userPoints = 0;
        this.cartValue = 0;
        this.lastActivity = Date.now();

        this.init();
    }

    init() {
        // Check user login status
        this.checkLoginStatus();

        // Set up dynamic behavior
        this.setupDynamicBehavior();

        // Start monitoring user activity
        this.monitorUserActivity();

        // Initial state setup
        this.updateRewardsState();
    }

    checkLoginStatus() {
        // Check if user is logged in using existing session check
        fetch("check_session.php")
            .then(response => response.json())
            .then(data => {
                this.isLoggedIn = data.loggedIn;
                this.customerData = data.customer || null;
                this.updateRewardsState();
            })
            .catch(error => {
                console.log("Session check failed, assuming guest user");
                this.isLoggedIn = false;
                this.customerData = null;
                this.updateRewardsState();
            });
    }

    setupDynamicBehavior() {
        // Monitor cart changes
        this.monitorCartActivity();

        // Monitor page interactions
        this.monitorPageActivity();

        // Set up periodic updates
        setInterval(() => {
            this.updateRewardsState();
        }, 10000); // Update every 10 seconds
    }

    monitorCartActivity() {
        // Monitor add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-to-cart') ||
                e.target.closest('[onclick*="addToCart"]') ||
                e.target.textContent.includes('Add to cart')) {
                this.onCartActivity();
            }
        });
    }

    monitorPageActivity() {
        // Monitor product page visits
        if (window.location.pathname.includes('product')) {
            this.onProductPageVisit();
        }

        // Monitor scroll activity
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.onUserActivity();
            }, 1000);
        });
    }

    monitorUserActivity() {
        // Track user activity
        ['click', 'scroll', 'mousemove', 'keypress'].forEach(event => {
            document.addEventListener(event, () => {
                this.lastActivity = Date.now();
            });
        });
    }

    updateRewardsState() {
        const timeSinceActivity = Date.now() - this.lastActivity;

        if (!this.isLoggedIn) {
            this.setGuestUserState();
        } else {
            this.setLoggedInUserState();
        }

        // Special states based on activity
        if (timeSinceActivity < 5000) { // Active in last 5 seconds
            this.setActiveUserState();
        }
    }

    setGuestUserState() {
        this.element.className = 'rewards-element guest-user';
        this.textElement.textContent = 'Earn Rewards';

        // Rotate between different messages
        const messages = [
            'Earn Rewards',
            'Join & Save',
            'Get Points',
            'Sign Up Now'
        ];

        setInterval(() => {
            const randomMessage = messages[Math.floor(Math.random() * messages.length)];
            this.textElement.textContent = randomMessage;
        }, 8000);
    }

    setLoggedInUserState() {
        this.element.className = 'rewards-element logged-in-user';

        // Show different messages for logged-in users
        const messages = [
            'Rewards',
            'Your Points',
            'Earn More',
            'Redeem Now'
        ];

        let messageIndex = 0;
        setInterval(() => {
            this.textElement.textContent = messages[messageIndex];
            messageIndex = (messageIndex + 1) % messages.length;
        }, 6000);
    }

    setActiveUserState() {
        // Temporarily enhance animation for active users
        this.element.style.animation = 'horizontal-shaking 2s ease infinite';

        setTimeout(() => {
            this.element.style.animation = '';
        }, 10000);
    }

    onCartActivity() {
        // Special animation when user adds to cart
        this.element.className = 'rewards-element cart-activity';
        this.textElement.textContent = 'Points Added!';

        setTimeout(() => {
            this.updateRewardsState();
        }, 3000);
    }

    onProductPageVisit() {
        // Special behavior on product pages
        setTimeout(() => {
            if (!this.isLoggedIn) {
                this.textElement.textContent = 'Login & Earn';
            } else {
                this.textElement.textContent = 'Review & Earn';
            }
        }, 5000);
    }

    onUserActivity() {
        // Handle user activity
        this.lastActivity = Date.now();
    }

    triggerSpecialOffer() {
        // Special offer animation
        this.element.className = 'rewards-element special-offer';
        this.textElement.textContent = 'Special Offer!';

        setTimeout(() => {
            this.updateRewardsState();
        }, 5000);
    }
}

function openRewardsPage() {
    // Check if promo popup is open and close it first
    const promoPopup = document.getElementById('promoPopup');
    if (promoPopup && promoPopup.classList.contains('show')) {
        closePromoPopup();
        // Wait a bit for promo popup to close before opening rewards modal
        setTimeout(() => {
            openRewardsModalAfterDelay();
        }, 300);
        return;
    }

    openRewardsModalAfterDelay();
}

function openRewardsModalAfterDelay() {
    // Krishna Ayurveda style - Open rewards modal instead of redirect
    const rewardsModal = document.getElementById('rewards-modal');
    const rewardsElement = window.dynamicRewards;

    // Update modal content based on login status
    updateRewardsModalContent();

    // Show the modal with animation
    rewardsModal.style.display = 'flex';
    setTimeout(() => {
        rewardsModal.classList.add('show');
    }, 10);

    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';

    // Track modal open event
    console.log('Rewards modal opened');
}

function closeRewardsModal() {
    const rewardsModal = document.getElementById('rewards-modal');

    // Hide modal with animation
    rewardsModal.classList.remove('show');
    setTimeout(() => {
        rewardsModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

function updateRewardsModalContent() {
    const rewardsElement = window.dynamicRewards;
    const modalContent = document.querySelector('.rewards-modal-content');

    if (!rewardsElement.isLoggedIn) {
        // Guest user content
        modalContent.innerHTML = `
            <div class="rewards-header">
                <h2>🎁 Welcome to My Nutrify Rewards!</h2>
                <button class="close-modal" onclick="closeRewardsModal()">×</button>
            </div>
            <div class="rewards-body">
                <div class="rewards-welcome">
                    <div class="rewards-intro">
                        <h3>Join And Earn Rewards</h3>
                        <p>Win Nutrify Points for every spend and redeem them for exclusive rewards</p>
                    </div>
                    <div class="rewards-actions">
                        <button class="btn-primary" onclick="window.location.href='login.php'">
                            Sign Up and Get Free Nutrify Points
                        </button>
                        <p class="login-link">
                            Already have an account?
                            <a href="login.php">Sign in</a>
                        </p>
                    </div>
                </div>
                <div class="rewards-features">
                    <div class="feature-section">
                        <div class="feature-header">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSIjRkY2QjM1Ii8+Cjwvc3ZnPgo=" alt="Earn">
                            <h4>Ways To Earn</h4>
                            <span class="expand-icon">→</span>
                        </div>
                    </div>
                    <div class="feature-section">
                        <div class="feature-header">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSIjMjdBRTYwIi8+Cjwvc3ZnPgo=" alt="Redeem">
                            <h4>Ways To Redeem</h4>
                            <span class="expand-icon">→</span>
                        </div>
                    </div>
                </div>
                <div class="referral-program">
                    <div class="referral-header">
                        <h4>🎯 Referral Program</h4>
                        <p>Give your friends a reward and claim your own when they make a purchase</p>
                    </div>
                    <div class="referral-benefits">
                        <div class="benefit">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSIjRkY2QjM1Ii8+Cjwvc3ZnPgo=" alt="Friend">
                            <div>
                                <span class="benefit-label">They get</span>
                                <span class="benefit-value">₹50 Off coupon</span>
                            </div>
                        </div>
                        <div class="benefit">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSIjMjdBRTYwIi8+Cjwvc3ZnPgo=" alt="You">
                            <div>
                                <span class="benefit-label">You get</span>
                                <span class="benefit-value">100 Nutrify Points</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rewards-footer">
                <p>Powered by <strong>My Nutrify</strong></p>
            </div>
        `;
    } else {
        // Logged-in user content
        modalContent.innerHTML = `
            <div class="rewards-header">
                <h2>🎁 Your Nutrify Rewards</h2>
                <button class="close-modal" onclick="closeRewardsModal()">×</button>
            </div>
            <div class="rewards-body">
                <div class="user-points">
                    <div class="points-display">
                        <h3>Your Points Balance</h3>
                        <div class="points-value">
                            <span class="points-number" id="user-points-display">${this.customerData?.points || 0}</span>
                            <span class="points-label">Nutrify Points</span>
                        </div>
                        <div class="tier-info">
                            <span class="tier-badge">${this.customerData?.tier_level || 'Bronze'} Member</span>
                        </div>
                    </div>
                </div>
                <div class="rewards-tabs">
                    <div class="tab active" onclick="switchRewardsTab('earn')">Ways to Earn</div>
                    <div class="tab" onclick="switchRewardsTab('redeem')">Redeem Points</div>
                    <div class="tab" onclick="switchRewardsTab('history')">History</div>
                </div>
                <div class="tab-content" id="earn-content">
                    <div class="earn-methods">
                        <div class="earn-item">
                            <span class="earn-icon">🛒</span>
                            <div class="earn-details">
                                <h4>Make a Purchase</h4>
                                <p>Earn 3 points for every ₹100 spent</p>
                            </div>
                            <span class="earn-points">+3 pts</span>
                        </div>
                        <div class="earn-item">
                            <span class="earn-icon">⭐</span>
                            <div class="earn-details">
                                <h4>Write a Review</h4>
                                <p>Share your experience with products</p>
                            </div>
                            <span class="earn-points">+25 pts</span>
                        </div>
                        <div class="earn-item">
                            <span class="earn-icon">👥</span>
                            <div class="earn-details">
                                <h4>Refer a Friend</h4>
                                <p>When they make their first purchase</p>
                            </div>
                            <span class="earn-points">+100 pts</span>
                        </div>
                    </div>
                </div>
                <div class="tab-content hidden" id="redeem-content">
                    <div class="redeem-options">
                        <div class="redeem-item">
                            <h4>₹50 Off Coupon</h4>
                            <p>Minimum order ₹500</p>
                            <button class="redeem-btn" disabled>500 Points Required</button>
                        </div>
                        <div class="redeem-item">
                            <h4>₹100 Off Coupon</h4>
                            <p>Minimum order ₹1000</p>
                            <button class="redeem-btn" disabled>1000 Points Required</button>
                        </div>
                    </div>
                </div>
                <div class="tab-content hidden" id="history-content">
                    <div class="history-empty">
                        <p>No rewards history yet. Start earning points by making purchases!</p>
                    </div>
                </div>
            </div>
            <div class="rewards-footer">
                <p>Powered by <strong>My Nutrify</strong></p>
            </div>
        `;
    }
}

function switchRewardsTab(tabName) {
    // Remove active class from all tabs
    document.querySelectorAll('.rewards-tabs .tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Show selected tab
    document.querySelector(`[onclick="switchRewardsTab('${tabName}')"]`).classList.add('active');
    document.getElementById(`${tabName}-content`).classList.remove('hidden');
}

// Initialize the dynamic rewards system when page loads
document.addEventListener('DOMContentLoaded', function() {
    window.dynamicRewards = new DynamicRewardsElement();

    // Trigger special offer after 30 seconds for demo
    setTimeout(() => {
        if (Math.random() > 0.7) { // 30% chance
            window.dynamicRewards.triggerSpecialOffer();
        }
    }, 30000);
});
</script>

<!-- Promotional Popup JavaScript -->
<script>
// Promotional Popup Functionality
let promoTimer;
let timeLeft = 24 * 60 * 60; // 24 hours in seconds
let resendTimer;
let resendTimeLeft = 30;
let currentStep = 1;
let userMobileNumber = '';
let generatedOTP = '';

// Check if popup should be shown (show once per session)
function shouldShowPopup() {
    const sessionShown = sessionStorage.getItem('promoPopupShownThisSession');

    console.log('Popup check:', {
        sessionShown: sessionShown,
        shouldShow: !sessionShown
    });

    // Show if not shown in this session
    if (!sessionShown) {
        return true;
    }
    return false;
}

// Show promotional popup
function showPromoPopup() {
    console.log('showPromoPopup called');

    if (shouldShowPopup()) {
        console.log('Popup should show - displaying now');

        // Close rewards modal if it's open
        const rewardsModal = document.getElementById('rewards-modal');
        if (rewardsModal && rewardsModal.classList.contains('show')) {
            closeRewardsModal();
        }

        const popup = document.getElementById('promoPopup');
        if (popup) {
            popup.classList.add('show');
            console.log('Popup shown successfully');

            // Mark as shown for this session
            sessionStorage.setItem('promoPopupShownThisSession', 'true');

            // Initialize popup
            showStep(1);

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Popup element not found!');
        }
    } else {
        console.log('Popup should NOT show - already shown recently');
    }
}

// Show specific step
function showStep(step) {
    // Hide all steps
    document.getElementById('promoStep1').style.display = 'none';
    document.getElementById('promoStep2').style.display = 'none';
    document.getElementById('promoStep3').style.display = 'none';

    // Show current step
    document.getElementById('promoStep' + step).style.display = 'block';
    currentStep = step;

    // Start timer only on final step
    if (step === 3) {
        startPromoTimer();
    }
}

// Mobile Number Validation and OTP Functions
function validateMobileNumber(mobile) {
    const mobileRegex = /^[6-9]\d{9}$/;
    return mobileRegex.test(mobile);
}

function generateOTP() {
    return Math.floor(100000 + Math.random() * 900000).toString();
}

function sendOTP() {
    const mobileInput = document.getElementById('promoMobileNumber');
    const errorDiv = document.getElementById('promoMobileError');
    const verifyBtn = document.querySelector('#promoStep1 .promo-verify-btn');
    const btnText = document.getElementById('verifyBtnText');
    const btnLoader = document.getElementById('verifyBtnLoader');

    const mobile = mobileInput.value.trim();

    // Clear previous errors
    errorDiv.textContent = '';

    // Validate mobile number
    if (!mobile) {
        errorDiv.textContent = '❌ Please enter your mobile number';
        mobileInput.focus();
        return;
    }

    if (!validateMobileNumber(mobile)) {
        errorDiv.textContent = '❌ Please enter a valid 10-digit mobile number';
        mobileInput.focus();
        return;
    }

    // Show loading state
    verifyBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';

    // Store mobile number
    userMobileNumber = mobile;

    // Make actual API call to send OTP via Interakt
    fetch('exe_files/promo_popup_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=send_otp&mobile=${encodeURIComponent(mobile)}`
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading state
        verifyBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';

        if (data.response === 'S') {
            // OTP sent successfully

            // For testing purposes, log the OTP (remove in production)
            if (data.otp_for_testing) {
                console.log('OTP for testing:', data.otp_for_testing);
                generatedOTP = data.otp_for_testing; // Store for testing
            }

            // Show success message
            errorDiv.style.color = '#27ae60';
            errorDiv.textContent = '✅ OTP sent successfully to your WhatsApp!';

            // Update display mobile number
            document.getElementById('displayMobileNumber').textContent = '+91 ' + mobile;

            // Move to OTP verification step
            setTimeout(() => {
                showStep(2);
                startResendTimer();

                // Focus first OTP input
                document.querySelector('.promo-otp-input').focus();
            }, 1000);
        } else {
            // Show error message
            errorDiv.style.color = '#dc3545';
            errorDiv.textContent = data.msg || 'Failed to send OTP. Please try again.';
        }
    })
    .catch(error => {
        console.error('Error sending OTP:', error);

        // Hide loading state
        verifyBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';

        // Show error message
        errorDiv.style.color = '#dc3545';
        errorDiv.textContent = 'Network error. Please check your connection and try again.';
    });
}

function verifyOTP() {
    const otpInputs = document.querySelectorAll('.promo-otp-input');
    const errorDiv = document.getElementById('promoOtpError');
    const verifyBtn = document.querySelector('#promoStep2 .promo-verify-btn');
    const btnText = document.getElementById('otpVerifyBtnText');
    const btnLoader = document.getElementById('otpVerifyBtnLoader');

    // Get entered OTP
    let enteredOTP = '';
    otpInputs.forEach(input => {
        enteredOTP += input.value;
    });

    // Clear previous errors
    errorDiv.textContent = '';

    // Validate OTP
    if (enteredOTP.length !== 6) {
        errorDiv.textContent = '❌ Please enter complete 6-digit OTP';
        return;
    }

    // Show loading state
    verifyBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';

    // Make actual API call to verify OTP
    fetch('exe_files/promo_popup_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=verify_otp&mobile=${encodeURIComponent(userMobileNumber)}&otp=${encodeURIComponent(enteredOTP)}`
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading state
        verifyBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';

        if (data.response === 'S') {
            // Success - show promo code
            errorDiv.style.color = '#27ae60';
            errorDiv.textContent = '✅ OTP verified successfully!';

            // Save verified mobile number (for future use)
            localStorage.setItem('verifiedMobileNumber', userMobileNumber);

            setTimeout(() => {
                showStep(3);
            }, 1000);

        } else {
            // Failed verification
            errorDiv.style.color = '#dc3545';
            errorDiv.textContent = data.msg || '❌ Invalid OTP. Please try again.';

            // Clear OTP inputs
            otpInputs.forEach(input => {
                input.value = '';
            });
            otpInputs[0].focus();
        }
    })
    .catch(error => {
        console.error('Error verifying OTP:', error);

        // Hide loading state
        verifyBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';

        // Show error message
        errorDiv.style.color = '#dc3545';
        errorDiv.textContent = 'Network error. Please check your connection and try again.';

        // Clear OTP inputs
        otpInputs.forEach(input => {
            input.value = '';
        });
        otpInputs[0].focus();
    });
}

function resendOTP() {
    const errorDiv = document.getElementById('promoOtpError');
    const resendBtn = document.getElementById('resendOtpBtn');

    // Show loading state
    resendBtn.disabled = true;
    resendBtn.innerHTML = '⏳ Sending...';

    // Make actual API call to resend OTP
    fetch('exe_files/promo_popup_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=send_otp&mobile=${encodeURIComponent(userMobileNumber)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.response === 'S') {
            // For testing purposes, log the OTP (remove in production)
            if (data.otp_for_testing) {
                console.log('New OTP for testing:', data.otp_for_testing);
                generatedOTP = data.otp_for_testing; // Store for testing
            }

            // Show success message
            errorDiv.style.color = '#27ae60';
            errorDiv.textContent = '✅ New OTP sent successfully to your WhatsApp!';

            // Restart resend timer
            startResendTimer();

            // Clear OTP inputs
            const otpInputs = document.querySelectorAll('.promo-otp-input');
            otpInputs.forEach(input => {
                input.value = '';
            });
            otpInputs[0].focus();

            setTimeout(() => {
                errorDiv.textContent = '';
            }, 3000);
        } else {
            // Show error message
            errorDiv.style.color = '#dc3545';
            errorDiv.textContent = data.msg || 'Failed to resend OTP. Please try again.';

            // Reset button
            resendBtn.disabled = false;
            resendBtn.innerHTML = 'Resend OTP';
        }
    })
    .catch(error => {
        console.error('Error resending OTP:', error);

        // Show error message
        errorDiv.style.color = '#dc3545';
        errorDiv.textContent = 'Network error. Please try again.';

        // Reset button
        resendBtn.disabled = false;
        resendBtn.innerHTML = 'Resend OTP';
    });
}

function changeNumber() {
    // Clear mobile input
    document.getElementById('promoMobileNumber').value = '';
    document.getElementById('promoMobileError').textContent = '';

    // Go back to step 1
    showStep(1);

    // Focus mobile input
    document.getElementById('promoMobileNumber').focus();
}

function startResendTimer() {
    resendTimeLeft = 30;
    const resendBtn = document.getElementById('resendOtpBtn');
    const resendTimerSpan = document.getElementById('resendTimer');

    resendBtn.disabled = true;

    resendTimer = setInterval(() => {
        resendTimeLeft--;
        resendTimerSpan.textContent = resendTimeLeft;

        if (resendTimeLeft <= 0) {
            clearInterval(resendTimer);
            resendBtn.disabled = false;
            resendBtn.innerHTML = 'Resend OTP';
        }
    }, 1000);
}

// Close promotional popup
function closePromoPopup() {
    const popup = document.getElementById('promoPopup');
    popup.classList.remove('show');

    // Clear timers
    if (promoTimer) {
        clearInterval(promoTimer);
    }
    if (resendTimer) {
        clearInterval(resendTimer);
    }

    // Reset to step 1
    showStep(1);

    // Clear form data
    document.getElementById('promoMobileNumber').value = '';
    document.getElementById('promoMobileError').textContent = '';
    document.getElementById('promoOtpError').textContent = '';

    const otpInputs = document.querySelectorAll('.promo-otp-input');
    otpInputs.forEach(input => {
        input.value = '';
    });

    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Copy promo code to clipboard
function copyPromoCode() {
    const promoCode = document.getElementById('promoCode').textContent;

    // Create temporary textarea to copy text
    const tempTextarea = document.createElement('textarea');
    tempTextarea.value = promoCode;
    document.body.appendChild(tempTextarea);
    tempTextarea.select();
    document.execCommand('copy');
    document.body.removeChild(tempTextarea);

    // Show feedback
    const copyBtn = document.querySelector('.promo-copy-btn');
    const originalText = copyBtn.textContent;
    copyBtn.textContent = 'Copied!';
    copyBtn.style.background = '#27ae60';

    setTimeout(() => {
        copyBtn.textContent = originalText;
        copyBtn.style.background = '#305724';
    }, 2000);
}

// Start shopping - redirect to products page
function startShopping() {
    closePromoPopup();
    window.location.href = 'products.php';
}

// Start countdown timer
function startPromoTimer() {
    const timerElement = document.getElementById('promoTimer');

    promoTimer = setInterval(() => {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;

        const formattedTime =
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');

        timerElement.textContent = formattedTime;

        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(promoTimer);
            timerElement.textContent = 'Offer Expired';
        }
    }, 1000);
}

// Close popup when clicking outside
document.addEventListener('click', function(event) {
    const popup = document.getElementById('promoPopup');
    const container = document.querySelector('.promo-popup-container');

    if (popup && popup.classList.contains('show') &&
        !container.contains(event.target)) {
        closePromoPopup();
    }
});

// Close popup with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePromoPopup();
    }
});

// OTP Input Navigation
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.promo-otp-input');

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');

            // Move to next input if current is filled
            if (this.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            // Auto-verify if all inputs are filled
            if (index === otpInputs.length - 1 && this.value.length === 1) {
                let allFilled = true;
                otpInputs.forEach(inp => {
                    if (inp.value.length === 0) allFilled = false;
                });
                if (allFilled) {
                    setTimeout(verifyOTP, 500);
                }
            }
        });

        input.addEventListener('keydown', function(e) {
            // Move to previous input on backspace if current is empty
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                otpInputs[index - 1].focus();
            }

            // Move to next input on arrow right
            if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }

            // Move to previous input on arrow left
            if (e.key === 'ArrowLeft' && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');

            if (pastedData.length === 6) {
                otpInputs.forEach((inp, idx) => {
                    inp.value = pastedData[idx] || '';
                });
                setTimeout(verifyOTP, 500);
            }
        });
    });

    // Mobile number input validation
    const mobileInput = document.getElementById('promoMobileNumber');
    if (mobileInput) {
        mobileInput.addEventListener('input', function() {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');

            // Clear error message when user starts typing
            document.getElementById('promoMobileError').textContent = '';
        });

        mobileInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendOTP();
            }
        });
    }
});



// Show popup when DOM is ready - Single trigger only
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - attempting to show popup');
    setTimeout(showPromoPopup, 2000); // Show after 2 seconds

    // Temporary test button (remove after testing)
    setTimeout(() => {
        const testBtn = document.createElement('div');
        testBtn.innerHTML = 'Test Popup';
        testBtn.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#ff8c00;color:white;padding:8px 12px;border-radius:20px;cursor:pointer;font-size:12px;z-index:9999;';
        testBtn.onclick = function() {
            sessionStorage.removeItem('promoPopupShownThisSession');
            showPromoPopup();
        };
        document.body.appendChild(testBtn);
    }, 3000);
});

// FORCE OVERRIDE: Add to Cart redirect functionality
// This runs after all other scripts to ensure it takes priority
window.addEventListener('load', function() {
    console.log('🚀 Overriding Add to Cart functionality...');

    // Remove all existing click handlers from add-to-cart-session buttons
    document.querySelectorAll('.add-to-cart-session').forEach(function(button) {
        // Clone the button to remove all event listeners
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);

        // Add our redirect functionality
        newButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = this.getAttribute('data-product-id');
            console.log('🔄 FORCE REDIRECT to product page for Product ID:', productId);

            if (productId) {
                window.location.href = 'product_details.php?ProductId=' + productId;
            } else {
                console.error('❌ Product ID not found');
                alert('Product not found. Please try again.');
            }
        });
    });

    console.log('✅ Add to Cart override complete!');
});
</script>

</body>

</html>