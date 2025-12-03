<?php
// Protect this page - Only authenticated admins can access
require_once 'auth-check.php';

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'config.php';

function getCurrentConfig() {
    global $ALL_SECTION_WEBSITES, $CATEGORIES_WEBSITES, $LATEST_WEBSITES, $SEARCH_WEBSITES, $HERO_CAROUSEL_WEBSITES, $HERO_CAROUSEL_MANUAL_MOVIES, $SITE_SETTINGS;
    return [
        'ALL_SECTION_WEBSITES' => $ALL_SECTION_WEBSITES,
        'CATEGORIES_WEBSITES' => $CATEGORIES_WEBSITES,
        'LATEST_WEBSITES' => $LATEST_WEBSITES,
        'SEARCH_WEBSITES' => $SEARCH_WEBSITES,
        'HERO_CAROUSEL_WEBSITES' => $HERO_CAROUSEL_WEBSITES,
        'HERO_CAROUSEL_MANUAL_MOVIES' => $HERO_CAROUSEL_MANUAL_MOVIES,
        'SITE_SETTINGS' => $SITE_SETTINGS
    ];
}

$config = getCurrentConfig();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/webp" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>">
    <link rel="apple-touch-icon" href="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>">
    <title>Config Manager - <?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 0;
            padding-top: 70px;
        }

        /* Header Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding: 10px 15px;
            z-index: 1000;
            background-color: #0f0f0f;
            margin: 0;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1920px;
            margin: 0 auto;
            gap: 20px;
            width: 100%;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 2.5rem;
            font-weight: 900;
            letter-spacing: 2px;
            margin-left: 10px;
        }

        .logo-image {
            height: 44px;
            width: auto;
            display: block;
        }

        .logo-text {
            font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-right: 10px;
        }

        .user-info {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .logout-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .logout-link:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.5);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px 20px;
        }

        .tabs-wrapper {
            position: relative;
            margin-bottom: 32px;
        }

        .tabs-carousel {
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .tabs {
            display: flex;
            gap: 8px;
            overflow-x: scroll;
            overflow-y: hidden;
            scroll-behavior: smooth;
            padding: 8px 0 20px 0;
            margin-bottom: -20px;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
            -webkit-overflow-scrolling: touch;
        }

        .tabs::-webkit-scrollbar {
            display: none !important;
            width: 0px !important;
            height: 0px !important;
            background: transparent !important;
        }

        .tabs::-webkit-scrollbar-track {
            display: none !important;
            background: transparent !important;
        }

        .tabs::-webkit-scrollbar-thumb {
            display: none !important;
            background: transparent !important;
        }

        .tab {
            padding: 12px 24px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            color: #64748b;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .tab.active {
            background: linear-gradient(90deg, #df0033 0%, #bd284b 100%);
            color: white;
            border-color: #df0033;
        }

        .tab:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        
        .tab.active:hover {
            background: linear-gradient(90deg, #f31447 0%, #d13557 100%);
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 52px;
            background: rgba(15, 15, 15, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            width: 45px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.2s ease;
            opacity: 0;
        }

        .tabs-carousel:hover .carousel-btn {
            opacity: 1;
        }

        .carousel-btn:hover {
            background: rgba(15, 15, 15, 0.85);
        }

        .carousel-btn.left {
            left: 0;
            border-radius: 0 10px 10px 0;
            border-left: none;
        }

        .carousel-btn.right {
            right: 0;
            border-radius: 10px 0 0 10px;
            border-right: none;
        }

        .carousel-btn svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        /* Hero Search Responsive */
        @media (min-width: 640px) {
            .hero-search-container {
                flex-direction: row !important;
                align-items: stretch;
            }

            .hero-search-container input {
                flex: 1;
            }

            .hero-search-container button {
                width: auto !important;
                min-width: 120px;
            }
        }

        @media (max-width: 1024px) {
            .container {
                padding: 20px 16px;
            }

            .tabs {
                overflow-x: auto;
                gap: 8px;
            }

            .tab {
                font-size: 14px;
                padding: 12px 18px;
                white-space: nowrap;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 74px;
            }

            .navbar {
                padding: 15px 12px;
                left: 0;
                right: 0;
                margin: 0;
            }

            .nav-container {
                gap: 10px;
                max-width: 100%;
                width: 100%;
                overflow: hidden;
                padding: 0;
                margin: 0;
            }

            .logo {
                flex: 1;
                min-width: 0;
                margin-left: 25px;
            }

            .logo-image {
                height: 34px;
                flex-shrink: 0;
            }
            
            .nav-right {
                margin-right: 25px;
            }

            .logo-text {
                font-size: 1.4rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .user-info {
                display: none;
            }

            .logout-link span {
                display: none;
            }

            .logout-link {
                padding: 10px;
                flex-shrink: 0;
            }

            .carousel-btn {
                display: none;
            }

            .container {
                padding: 16px 12px;
            }

            .tabs-wrapper {
                margin-top: 0;
            }

            .tabs {
                gap: 6px;
                padding: 0 8px;
            }

            .tab {
                font-size: 13px;
                padding: 10px 16px;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .section-header h2 {
                font-size: 1.25rem;
            }

            .website-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .website-actions {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .config-card {
                padding: 16px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 68px;
            }

            .navbar {
                padding: 12px 10px;
                left: 0;
                right: 0;
                margin: 0;
            }

            .nav-container {
                gap: 8px;
                width: 100%;
                padding: 0;
                margin: 0;
            }

            .logo {
                gap: 4px;
                flex: 1;
                min-width: 0;
                margin-left: 20px;
            }

            .logo-image {
                height: 30px;
                flex-shrink: 0;
            }
            
            .nav-right {
                margin-right: 20px;
            }

            .logo-text {
                font-size: 1.2rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .logout-link {
                padding: 8px;
                flex-shrink: 0;
            }

            .container {
                padding: 12px 10px;
            }

            .tabs-wrapper {
                margin-top: 0;
            }

            .tabs {
                gap: 4px;
                padding: 0 6px;
            }

            .tab {
                font-size: 12px;
                padding: 8px 12px;
            }

            .section-header h2 {
                font-size: 1.1rem;
            }

            .add-btn {
                padding: 8px 14px;
                font-size: 13px;
            }

            .website-item {
                padding: 12px;
            }

            .website-name {
                font-size: 14px;
            }

            .detail-label,
            .detail-value {
                font-size: 12px;
            }

            .btn {
                padding: 8px 12px;
                font-size: 12px;
            }

            .form-group label {
                font-size: 13px;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 14px;
                padding: 10px 12px;
            }

            .config-card {
                padding: 12px;
            }

            .config-card h3 {
                font-size: 1rem;
            }
        }

        @media (max-width: 360px) {
            body {
                padding-top: 64px;
            }

            .navbar {
                padding: 10px 8px;
                left: 0;
                right: 0;
                margin: 0;
            }

            .nav-container {
                gap: 6px;
                width: 100%;
                padding: 0;
                margin: 0;
            }

            .logo {
                gap: 3px;
                margin-left: 15px;
            }

            .logo-image {
                height: 28px;
            }
            
            .nav-right {
                margin-right: 15px;
            }

            .logo-text {
                font-size: 1.1rem;
                max-width: 150px;
            }

            .logout-link {
                padding: 6px;
            }

            .container {
                padding: 10px 8px;
            }

            .tabs-wrapper {
                margin-top: 0;
            }

            .tab {
                font-size: 11px;
                padding: 7px 10px;
            }

            .section-header h2 {
                font-size: 1rem;
            }

            .add-btn {
                padding: 7px 12px;
                font-size: 12px;
            }

            .website-item {
                padding: 10px;
            }

            .btn {
                padding: 6px 10px;
                font-size: 11px;
            }
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .section-header h2 {
            color: #1a202c;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .add-btn {
            background: #6366f1;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
        }

        .add-btn:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .config-grid {
            margin-top: 20px;
        }

        .config-card {
            margin-bottom: 32px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .config-card h3 {
            color: #1a202c;
            margin-bottom: 16px;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .website-item {
            background: white;
            padding: 16px;
            margin: 12px 0;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            transition: all 0.2s ease;
        }

        .website-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(15,15,15,0.05);
            transform: translateY(-1px);
        }

        .website-item.hidden {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .website-item.hidden .website-info {
            opacity: 0.35;
        }

        .website-item.hidden:hover .website-info {
            opacity: 0.5;
        }

        .website-item.hidden .btn-group > *:not(.hide-btn) {
            opacity: 0.35;
        }

        .website-item.hidden:hover .btn-group > *:not(.hide-btn) {
            opacity: 0.5;
        }

        .manual-movie-item.hidden img,
        .manual-movie-item.hidden > div:last-child > div:not(:last-child) {
            opacity: 0.35;
        }

        .manual-movie-item.hidden:hover img,
        .manual-movie-item.hidden:hover > div:last-child > div:not(:last-child) {
            opacity: 0.5;
        }

        .manual-movie-item.hidden button {
            opacity: 0.35;
        }

        .manual-movie-item.hidden:hover button {
            opacity: 0.5;
        }

        .website-info {
            flex: 1;
            min-width: 0;
        }

        .website-name {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .website-details {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .detail-row {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .detail-label {
            font-weight: 500;
            color: #64748b;
            font-size: 13px;
        }

        .detail-value {
            color: #475569;
            font-size: 13px;
            word-break: break-word;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-group {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .edit-btn, .delete-btn, .hide-btn {
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .edit-btn {
            background: #3b82f6;
            color: white;
        }

        .edit-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .hide-btn {
            background: #f59e0b;
            color: white;
        }

        .hide-btn:hover {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15,15,15,0.4);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 32px;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(15,15,15,0.15);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-title {
            font-size: 1.5rem;
            color: #1a202c;
            font-weight: 600;
        }

        .close-btn {
            background: #f1f5f9;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #64748b;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .close-btn:hover {
            background: #e2e8f0;
            color: #1a202c;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #1a202c;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .save-btn {
            background: #6366f1;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
        }

        .save-btn:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            font-size: 14px;
            font-weight: 500;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 70px 12px 12px 12px;
            }

            .container {
                padding: 20px;
                border-radius: 16px;
            }

            h1 {
                font-size: 1.75rem;
                margin-bottom: 6px;
            }
            
            h1 + p {
                font-size: 14px !important;
            }

            .tabs {
                gap: 6px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                padding-bottom: 2px;
                margin-bottom: 24px;
            }

            .tab {
                padding: 10px 16px;
                font-size: 14px;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .section-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 20px;
            }

            .section-header h2 {
                font-size: 1.25rem;
            }

            .add-btn {
                width: 100%;
                padding: 12px 18px;
                font-size: 15px;
            }

            .section-header .hide-btn,
            #toggle-manual-movies-btn {
                width: 100%;
                padding: 10px 18px;
                font-size: 13px;
                height: auto;
            }

            .config-card {
                padding: 16px;
                margin-bottom: 24px;
            }

            .config-card h3 {
                font-size: 1.05rem;
            }

            .website-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 14px;
                gap: 14px;
            }

            .website-info {
                width: 100%;
            }

            .website-name {
                font-size: 14px;
                word-break: break-word;
                line-height: 1.4;
            }

            .website-details {
                flex-direction: column;
                gap: 10px;
            }

            .detail-row {
                display: grid;
                grid-template-columns: auto 1fr;
                gap: 8px;
                align-items: start;
            }

            .detail-value {
                max-width: 100%;
                white-space: normal;
                word-break: break-word;
            }

            .detail-label {
                font-size: 12px;
            }

            .detail-value {
                font-size: 12px;
            }

            .btn-group {
                width: 100%;
                display: grid;
                grid-template-columns: auto auto 1fr 1fr 1fr;
                gap: 6px;
            }

            .btn-group .move-btn {
                width: 42px;
                height: 40px;
                padding: 0;
            }

            .btn-group .hide-btn,
            .btn-group .edit-btn,
            .btn-group .delete-btn {
                padding: 10px 8px;
                font-size: 13px;
            }

            .modal-content {
                width: 95%;
                padding: 24px;
                max-height: 90vh;
                border-radius: 16px;
            }

            .modal-title {
                font-size: 1.35rem;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-label {
                font-size: 14px;
            }

            .form-input {
                padding: 12px;
                font-size: 16px;
            }

            .save-btn {
                padding: 14px 20px;
                font-size: 15px;
            }

            .section-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .section-card-title {
                flex-wrap: wrap;
                font-size: 1.05rem;
                gap: 8px;
            }

            .section-display-name {
                font-size: 0.9rem;
                margin-left: 0 !important;
            }

            .move-buttons {
                align-self: flex-start;
            }

            #site-settings .website-item {
                padding: 16px;
            }

            #site-settings .form-group {
                margin-bottom: 20px;
            }

            #background-preview {
                width: 100% !important;
                max-width: 300px;
                height: auto !important;
                aspect-ratio: 16 / 9;
                object-fit: cover;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 2rem;
            }

            .tab {
                padding: 10px 20px;
                font-size: 15px;
            }

            .detail-value {
                max-width: 250px;
            }

            .edit-btn, .delete-btn, .hide-btn {
                padding: 10px 12px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 68px 10px 10px 10px;
            }

            .container {
                padding: 16px;
            }

            h1 {
                font-size: 1.5rem;
            }
            
            h1 + p {
                font-size: 13px !important;
            }

            .section-header h2 {
                font-size: 1.15rem;
            }

            .tab {
                padding: 9px 14px;
                font-size: 13px;
            }

            .add-btn {
                padding: 12px 16px;
                font-size: 14px;
            }

            .modal-content {
                width: 98%;
                padding: 20px;
            }

            .close-btn {
                font-size: 22px;
                width: 30px;
                height: 30px;
            }

            .section-card-title {
                font-size: 1rem;
            }

            .section-display-name {
                font-size: 0.85rem;
            }

            .config-card {
                padding: 14px;
            }

            .config-card h3 {
                font-size: 1rem;
            }

            .edit-section-btn {
                font-size: 11px;
                padding: 4px 8px;
            }

            .website-item {
                padding: 12px;
                gap: 12px;
            }

            .btn-group {
                grid-template-columns: auto auto 1fr 1fr 1fr;
                gap: 5px;
            }

            .btn-group .move-btn {
                width: 38px;
                height: 38px;
            }

            .btn-group .hide-btn,
            .btn-group .edit-btn,
            .btn-group .delete-btn {
                font-size: 13px;
                padding: 10px 6px;
            }

            .website-name {
                font-size: 13px;
            }

            .detail-label,
            .detail-value {
                font-size: 12px;
            }

            #background-preview {
                max-width: 250px;
            }
        }

        .tabs::-webkit-scrollbar {
            height: 4px;
        }

        .tabs::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 2px;
        }

        .tabs::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }

        .tabs::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .image-preview-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .stat-badge {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
        }

        .built-in-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
            white-space: nowrap;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }

        @media (max-width: 768px) {
            .image-preview-container {
                flex-direction: column;
                gap: 15px;
            }

            .image-preview-container > div:last-child {
                width: 100%;
            }

            .image-preview-container button {
                width: 100%;
            }

            .stat-badge {
                margin-left: 0 !important;
                margin-top: 5px;
                display: inline-block;
            }

            .built-in-badge {
                margin-left: 0 !important;
                margin-top: 5px;
                display: block;
                width: fit-content;
            }

            .delete-btn[style*="width: auto"] {
                width: 100% !important;
            }

            .website-item[style*="max-width"] {
                max-width: 100% !important;
            }
        }

        .section-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .section-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1a202c;
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .section-display-name {
            color: #64748b;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .edit-section-btn {
            background: #6366f1;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .edit-section-btn:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3);
        }

        .move-buttons {
            display: flex;
            gap: 6px;
        }

        .move-btn {
            background: #6366f1;
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .move-btn:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .move-btn:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .move-btn:disabled:hover {
            transform: translateY(0);
            box-shadow: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="<?php echo htmlspecialchars($SITE_SETTINGS['logo_image']); ?>" alt="" class="logo-image" width="150" height="150">
                <span class="logo-text"><?php echo htmlspecialchars($SITE_SETTINGS['website_name']); ?></span>
            </div>
            <div class="nav-right">
                <span class="user-info">
                    Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </span>
                <a href="logout.php" class="logout-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div id="alert" class="alert"></div>

        <div class="tabs-wrapper">
            <div class="tabs-carousel">
                <button class="carousel-btn left" onclick="scrollTabs('left')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                <div class="tabs" id="tabs-container">
                    <button class="tab active" onclick="showTab('all-sections')">All Sections</button>
                    <button class="tab" onclick="showTab('categories')">Categories</button>
                    <button class="tab" onclick="showTab('latest')">Latest Websites</button>
                    <button class="tab" onclick="showTab('search')">Search Websites</button>
                    <button class="tab" onclick="showTab('hero')">Hero Carousel</button>
                    <button class="tab" onclick="showTab('site-settings')">Site Settings</button>
                </div>
                <button class="carousel-btn right" onclick="scrollTabs('right')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="all-sections" class="tab-content active">
            <div class="section-header">
                <h2>All Section Websites</h2>
                <button class="add-btn" onclick="openAddModal('ALL_SECTION_WEBSITES')">+ Add New</button>
            </div>
            <div class="config-grid" id="all-sections-grid">
                <?php 
                $allSectionKeys = array_keys($config['ALL_SECTION_WEBSITES']);
                $allSectionCount = count($allSectionKeys);
                $allSectionIndex = 0;
                foreach($config['ALL_SECTION_WEBSITES'] as $section => $websites): 
                    $displayName = isset($websites['display_name']) ? $websites['display_name'] : $section;
                    foreach($websites as $name => $details): 
                        if ($name === 'display_name') continue;
                        $isBuiltIn = ($section === 'WEEKLY_TOP_10' || $section === 'SEARCH_LINKS');
                        $isSearchLinks = ($section === 'SEARCH_LINKS');
                        $detailsWithDisplayName = array_merge($details, ['display_name' => $displayName]);
                        $isHidden = isset($details['hidden']) && $details['hidden'];
                ?>
                    <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                        <div class="website-info">
                            <div class="website-name">
                                <?php echo htmlspecialchars($section); ?> - <?php echo htmlspecialchars($name); ?>
                                <span style="color: #999; font-size: 11px; margin-left: 8px;">(<?php echo htmlspecialchars($displayName); ?>)</span>
                                <?php if ($isBuiltIn): ?>
                                    <span style="color: #4CAF50; font-size: 11px; margin-left: 8px;">Built-in</span>
                                <?php endif; ?>
                            </div>
                            <div class="website-details">
                                <div class="detail-row">
                                    <span class="detail-label">URL:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Parser:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                </div>
                            </div>
                            <?php if ($section === 'WEEKLY_TOP_10'): ?>
                            <div style="margin-top: 12px; padding: 12px; background: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 6px;">
                                <p style="color: #92400e; font-size: 13px; margin-bottom: 10px; line-height: 1.5;">
                                    <strong>Reset Weekly Views Data:</strong> You can reset all Weekly Top 10 view data using this button.
                                </p>
                                <button class="delete-btn" onclick="resetWeeklyViews()" style="padding: 8px 16px; font-size: 13px;">
                                    Reset Data
                                </button>
                            </div>
                            <?php endif; ?>
                            <?php if ($isSearchLinks): ?>
                            <div style="margin-top: 12px; padding: 12px; background: #dbeafe; border-left: 3px solid #3b82f6; border-radius: 6px;">
                                <p style="color: #1e40af; font-size: 13px; line-height: 1.5;">
                                    <strong>Dynamic Section:</strong> This section automatically displays all search websites from the "Search Websites" tab. Add or remove websites there to update this section.
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="btn-group">
                            <button class="move-btn" onclick='moveSection("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "up")' <?php echo $allSectionIndex === 0 ? 'disabled' : ''; ?>>↑</button>
                            <button class="move-btn" onclick='moveSection("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "down")' <?php echo $allSectionIndex === $allSectionCount - 1 ? 'disabled' : ''; ?>>↓</button>
                            <button class="hide-btn" onclick='toggleHideItem("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                            <?php if ($isBuiltIn): ?>
                                <button class="edit-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Built-in features cannot be edited">Edit</button>
                                <button class="delete-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Built-in features cannot be deleted">Protected</button>
                            <?php else: ?>
                                <button class="edit-btn" onclick='editItem("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "<?php echo $name; ?>", <?php echo json_encode($detailsWithDisplayName); ?>)'>Edit</button>
                                <button class="delete-btn" onclick='deleteItem("ALL_SECTION_WEBSITES", "<?php echo $section; ?>", "<?php echo $name; ?>")'>Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    endforeach;
                    $allSectionIndex++;
                endforeach; 
                ?>
            </div>
        </div>

        <div id="categories" class="tab-content">
            <div class="section-header">
                <h2>Categories Websites</h2>
                <button class="add-btn" onclick="openAddModal('CATEGORIES_WEBSITES')">+ Add New</button>
            </div>
            <div class="config-grid" id="categories-grid">
                <?php 
                $categoriesKeys = array_keys($config['CATEGORIES_WEBSITES']);
                $categoriesCount = count($categoriesKeys);
                $categoriesIndex = 0;
                foreach($config['CATEGORIES_WEBSITES'] as $category => $websites): 
                    $displayName = isset($websites['display_name']) ? $websites['display_name'] : $category;
                    foreach($websites as $name => $details): 
                        if ($name === 'display_name') continue;
                        $detailsWithDisplayName = array_merge($details, ['display_name' => $displayName]);
                        $isHidden = isset($details['hidden']) && $details['hidden'];
                ?>
                    <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                        <div class="website-info">
                            <div class="website-name">
                                <?php echo htmlspecialchars($category); ?> - <?php echo htmlspecialchars($name); ?>
                                <span style="color: #999; font-size: 11px; margin-left: 8px;">(<?php echo htmlspecialchars($displayName); ?>)</span>
                            </div>
                            <div class="website-details">
                                <div class="detail-row">
                                    <span class="detail-label">URL:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Parser:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="move-btn" onclick='moveSection("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "up")' <?php echo $categoriesIndex === 0 ? 'disabled' : ''; ?>>↑</button>
                            <button class="move-btn" onclick='moveSection("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "down")' <?php echo $categoriesIndex === $categoriesCount - 1 ? 'disabled' : ''; ?>>↓</button>
                            <button class="hide-btn" onclick='toggleHideItem("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                            <button class="edit-btn" onclick='editItem("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>", <?php echo json_encode($detailsWithDisplayName); ?>)'>Edit</button>
                            <button class="delete-btn" onclick='deleteItem("CATEGORIES_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>")'>Delete</button>
                        </div>
                    </div>
                <?php 
                    endforeach;
                    $categoriesIndex++;
                endforeach; 
                ?>
            </div>
        </div>

        <div id="latest" class="tab-content">
            <div class="section-header">
                <h2>Latest Websites</h2>
                <button class="add-btn" onclick="openAddModal('LATEST_WEBSITES')">+ Add New</button>
            </div>
            <div class="config-grid" id="latest-grid">
                <?php 
                $latestKeys = array_keys($config['LATEST_WEBSITES']);
                $latestCount = count($latestKeys);
                $latestIndex = 0;
                foreach($config['LATEST_WEBSITES'] as $category => $websites): 
                    $displayName = isset($websites['display_name']) ? $websites['display_name'] : $category;
                    $isBuiltInCategory = ($category === 'LATEST');
                    foreach($websites as $name => $details): 
                        if ($name === 'display_name') continue;
                        $detailsWithDisplayName = array_merge($details, ['display_name' => $displayName]);
                        $isHidden = isset($details['hidden']) && $details['hidden'];
                ?>
                    <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                        <div class="website-info">
                            <div class="website-name">
                                <?php echo htmlspecialchars($category); ?> - <?php echo htmlspecialchars($name); ?>
                                <span style="color: #999; font-size: 11px; margin-left: 8px;">(<?php echo htmlspecialchars($displayName); ?>)</span>
                                <?php if ($isBuiltInCategory): ?>
                                    <span style="color: #4CAF50; font-size: 11px; margin-left: 8px;">Built-in</span>
                                <?php endif; ?>
                            </div>
                            <div class="website-details">
                                <div class="detail-row">
                                    <span class="detail-label">URL:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Parser:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="move-btn" onclick='moveSection("LATEST_WEBSITES", "<?php echo $category; ?>", "up")' <?php echo $latestIndex === 0 ? 'disabled' : ''; ?>>↑</button>
                            <button class="move-btn" onclick='moveSection("LATEST_WEBSITES", "<?php echo $category; ?>", "down")' <?php echo $latestIndex === $latestCount - 1 ? 'disabled' : ''; ?>>↓</button>
                            <button class="hide-btn" onclick='toggleHideItem("LATEST_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                            <button class="edit-btn" onclick='editItem("LATEST_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>", <?php echo json_encode($detailsWithDisplayName); ?>)'>Edit</button>
                            <?php if ($isBuiltInCategory): ?>
                                <button class="delete-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Built-in features cannot be deleted">Protected</button>
                            <?php else: ?>
                                <button class="delete-btn" onclick='deleteItem("LATEST_WEBSITES", "<?php echo $category; ?>", "<?php echo $name; ?>")'>Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    endforeach;
                    $latestIndex++;
                endforeach; 
                ?>
            </div>
        </div>

        <div id="search" class="tab-content">
            <div class="section-header">
                <h2>Search Websites</h2>
                <button class="add-btn" onclick="openAddSearchModal()">+ Add New</button>
            </div>
            <div class="config-grid" id="search-grid">
                <?php 
                $searchKeys = array_keys($config['SEARCH_WEBSITES']);
                $searchCount = count($searchKeys);
                $searchIndex = 0;
                foreach($config['SEARCH_WEBSITES'] as $name => $details): 
                ?>
                    <?php $isHidden = isset($details['hidden']) && $details['hidden']; ?>
                    <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                        <div class="website-info">
                            <div class="website-name"><?php echo htmlspecialchars($name); ?></div>
                            <div class="website-details">
                                <div class="detail-row">
                                    <span class="detail-label">URL:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Param:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['search_param']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Type:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['type']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Parser:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                </div>
                                <?php if(isset($details['api_url'])): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">API:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($details['api_url']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if(isset($details['movie_base_url'])): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Base:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($details['movie_base_url']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="move-btn" onclick='moveSearchWebsite("<?php echo $name; ?>", "up")' <?php echo $searchIndex === 0 ? 'disabled' : ''; ?>>↑</button>
                            <button class="move-btn" onclick='moveSearchWebsite("<?php echo $name; ?>", "down")' <?php echo $searchIndex === $searchCount - 1 ? 'disabled' : ''; ?>>↓</button>
                            <button class="hide-btn" onclick='toggleHideSearchItem("<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                            <button class="edit-btn" onclick='editSearchItem("<?php echo $name; ?>", <?php echo json_encode($details); ?>)'>Edit</button>
                            <button class="delete-btn" onclick='deleteSearchItem("<?php echo $name; ?>")'>Delete</button>
                        </div>
                    </div>
                <?php $searchIndex++; endforeach; ?>
            </div>
        </div>

        <div id="hero" class="tab-content">
            <div class="section-header">
                <h2>Hero Carousel Websites 
                    <span style="background: #4CAF50; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; margin-left: 10px;">Built-in Feature</span>
                </h2>
            </div>
            <div class="config-grid" id="hero-grid">
                <?php foreach($config['HERO_CAROUSEL_WEBSITES'] as $name => $details): ?>
                    <?php $isHidden = isset($details['hidden']) && $details['hidden']; ?>
                    <div class="website-item <?php echo $isHidden ? 'hidden' : ''; ?>" data-hidden="<?php echo $isHidden ? 'true' : 'false'; ?>">
                        <div class="website-info">
                            <div class="website-name">
                                <?php echo htmlspecialchars($name); ?>
                                <span style="color: #4CAF50; font-size: 11px; margin-left: 8px;">(System Feature)</span>
                            </div>
                            <div class="website-details">
                                <div class="detail-row">
                                    <span class="detail-label">URL:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['url']); ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Parser:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($details['parser_type']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="hide-btn" onclick='toggleHideHeroItem("<?php echo $name; ?>")'><?php echo $isHidden ? 'Unhide' : 'Hide'; ?></button>
                            <button class="edit-btn" onclick='editHeroItem("<?php echo $name; ?>", <?php echo json_encode($details); ?>)'>Edit</button>
                            <button class="delete-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Built-in features cannot be deleted">Protected</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="config-card" style="margin-top: 32px;">
                <h3>Manual Hero Carousel Movies</h3>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Search for movies and add them to the hero carousel manually. These movies will appear alongside automatically fetched movies.</p>
                
                <div style="margin-bottom: 24px;">
                    <div class="hero-search-container" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                        <input 
                            type="text" 
                            id="hero-movie-search" 
                            placeholder="Search movies to add to hero carousel..." 
                            style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; box-sizing: border-box;"
                            onkeypress="if(event.key === 'Enter') searchMoviesForHero()"
                        >
                        <button class="add-btn" onclick="searchMoviesForHero()" style="width: 100%; justify-content: center;">
                            <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                            Search
                        </button>
                    </div>
                    
                    <div id="hero-search-results" style="display: none;">
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; max-height: 500px; overflow-y: auto;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <h4 style="color: #1a202c; font-size: 14px; font-weight: 600;">Search Results</h4>
                                <button onclick="closeHeroSearchResults()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: #64748b;">&times;</button>
                            </div>
                            <div id="hero-search-results-content"></div>
                        </div>
                    </div>
                </div>

                <?php
                    $allHidden = true;
                    if (!empty($config['HERO_CAROUSEL_MANUAL_MOVIES'])) {
                        foreach ($config['HERO_CAROUSEL_MANUAL_MOVIES'] as $movie) {
                            if (!isset($movie['hidden']) || $movie['hidden'] !== true) {
                                $allHidden = false;
                                break;
                            }
                        }
                    }
                ?>
                <div>
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h4 style="color: #1a202c; font-size: 14px; font-weight: 600;">Current Manual Movies (<?php echo count($config['HERO_CAROUSEL_MANUAL_MOVIES']); ?>)</h4>
                            <button 
                                id="toggle-manual-movies-btn"
                                onclick="toggleManualMoviesVisibility()"
                                class="hide-btn"
                            >
                                <?php echo $allHidden ? 'Unhide' : 'Hide'; ?>
                            </button>
                        </div>
                        <div id="hero-manual-movies-list" style="display: flex; gap: 12px; overflow-x: auto; scroll-behavior: smooth; padding: 10px 0;">
                            <?php if(empty($config['HERO_CAROUSEL_MANUAL_MOVIES'])): ?>
                                <div style="text-align: center; padding: 20px; color: #94a3b8; width: 100%;">No manual movies added yet. Search and add movies above.</div>
                            <?php else: ?>
                                <?php foreach($config['HERO_CAROUSEL_MANUAL_MOVIES'] as $index => $movie): ?>
                                    <?php $isMovieHidden = isset($movie['hidden']) && $movie['hidden']; ?>
                                    <div class="manual-movie-item <?php echo $isMovieHidden ? 'hidden' : ''; ?>" style="flex: 0 0 auto; width: 110px; display: flex; flex-direction: column; gap: 6px;">
                                        <div style="position: relative; width: 110px; background: #f8f9fa; border-radius: 6px; overflow: hidden;">
                                            <img 
                                                src="<?php echo htmlspecialchars($movie['image']); ?>" 
                                                alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                                style="width: 110px; height: 165px; object-fit: cover; display: block;"
                                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22300%22%3E%3Crect fill=%22%23e2e8f0%22 width=%22200%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%2394a3b8%22 font-family=%22sans-serif%22 font-size=%2216%22%3ENo Image%3C/text%3E%3C/svg%3E'"
                                            >
                                        </div>
                                        <div style="padding: 0 2px;">
                                            <div style="font-size: 11px; color: #1a202c; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500; margin-bottom: 4px;"><?php echo htmlspecialchars($movie['title']); ?></div>
                                            <div style="font-size: 9px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 5px;">
                                                <?php if(!empty($movie['language'])): ?><?php echo htmlspecialchars($movie['language']); ?><?php endif; ?>
                                                <?php if(!empty($movie['website'])): ?> • <?php echo htmlspecialchars($movie['website']); ?><?php endif; ?>
                                            </div>
                                            <button 
                                                onclick='deleteManualHeroMovie(<?php echo $index; ?>)'
                                                style="width: 100%; padding: 5px 10px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 10px; font-weight: 500;"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="site-settings" class="tab-content">
            <div class="section-header">
                <h2>Site Settings</h2>
            </div>
            
            <div class="website-item" style="max-width: 800px; margin: 0 auto;">
                <div style="width: 100%;">
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label" style="font-size: 16px; margin-bottom: 10px; display: block;">Website Name:</label>
                        <input type="text" id="website-name" class="form-input" value="<?php echo htmlspecialchars($config['SITE_SETTINGS']['website_name'] ?? 'FilmHaat'); ?>" style="width: 100%; padding: 12px; font-size: 15px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label" style="font-size: 16px; margin-bottom: 10px; display: block;">
                            Logo Image (1:1 ratio, WebP only):
                        </label>
                        <div class="image-preview-container">
                            <img id="logo-preview" src="<?php echo htmlspecialchars($config['SITE_SETTINGS']['logo_image'] ?? 'attached_image/logo-image.webp'); ?>" alt="Logo" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #667eea; border-radius: 8px;">
                            <div>
                                <input type="file" id="logo-upload" accept=".webp" style="display: none;">
                                <button type="button" class="add-btn" onclick="document.getElementById('logo-upload').click()">Upload Logo</button>
                                <div id="logo-status" style="margin-top: 10px; font-size: 13px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label" style="font-size: 16px; margin-bottom: 10px; display: block;">
                            Background Image (16:9 ratio, WebP only):
                        </label>
                        <div class="image-preview-container">
                            <img id="background-preview" src="<?php echo htmlspecialchars($config['SITE_SETTINGS']['background_image'] ?? 'attached_image/background-image.webp'); ?>" alt="Background" style="width: 320px; height: 180px; object-fit: cover; border: 2px solid #667eea; border-radius: 8px;">
                            <div>
                                <input type="file" id="background-upload" accept=".webp" style="display: none;">
                                <button type="button" class="add-btn" onclick="document.getElementById('background-upload').click()">Upload Background</button>
                                <div id="background-status" style="margin-top: 10px; font-size: 13px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label" style="font-size: 16px; margin-bottom: 10px; display: block;">
                            PWA Logo (1:1 ratio, WebP only):
                        </label>
                        <div class="image-preview-container">
                            <img id="pwa-logo-preview" src="attached_image/pwa-logo.webp" alt="PWA Logo" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #667eea; border-radius: 8px;">
                            <div>
                                <input type="file" id="pwa-logo-upload" accept=".webp" style="display: none;">
                                <button type="button" class="add-btn" onclick="document.getElementById('pwa-logo-upload').click()">Upload PWA Logo</button>
                                <div id="pwa-logo-status" style="margin-top: 10px; font-size: 13px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="add-btn" onclick="saveSiteSettings()" style="width: 100%; padding: 15px; font-size: 16px;">
                        Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modal-title">Add/Edit Config</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="config-form" onsubmit="saveConfig(event)">
                <div id="form-fields"></div>
                <button type="submit" class="save-btn">Save</button>
            </form>
        </div>
    </div>

    <script>
        window.heroManualMovies = <?php echo json_encode($config['HERO_CAROUSEL_MANUAL_MOVIES']); ?>;
        
        let currentAction = 'add';
        let currentConfigType = '';
        let currentSection = '';
        let currentName = '';

        function scrollTabs(direction) {
            const container = document.getElementById('tabs-container');
            const scrollAmount = 300;
            
            if (direction === 'left') {
                container.scrollLeft -= scrollAmount;
            } else {
                container.scrollLeft += scrollAmount;
            }
        }

        function showTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert ${type} active`;
            setTimeout(() => alert.classList.remove('active'), 5000);
        }

        function openAddModal(configType) {
            currentAction = 'add';
            currentConfigType = configType;
            document.getElementById('modal-title').textContent = 'Add New Config';
            
            let formFields = '';
            
            if (configType === 'ALL_SECTION_WEBSITES') {
                formFields = `
                    <div class="form-group">
                        <label class="form-label">Section Key (Capital Letters):</label>
                        <input type="text" class="form-input" id="section-name" placeholder="ACTION_MOVIES" required style="text-transform: uppercase;">
                        <small style="color: #666; font-size: 12px;">Example: ACTION_MOVIES, HINDI_SERIES</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Display Name:</label>
                        <input type="text" class="form-input" id="display-name" placeholder="Action Movies" required>
                        <small style="color: #666; font-size: 12px;">This name will be shown on the website</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website Name:</label>
                        <input type="text" class="form-input" id="website-name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL:</label>
                        <input type="url" class="form-input" id="url" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parser Type:</label>
                        <input type="text" class="form-input" id="parser-type" value="li_thumb" required>
                    </div>
                `;
            } else {
                formFields = `
                    <div class="form-group">
                        <label class="form-label">Section/Category Key (Capital Letters):</label>
                        <input type="text" class="form-input" id="section-name" placeholder="ACTION_MOVIES" required style="text-transform: uppercase;">
                        <small style="color: #666; font-size: 12px;">Example: ACTION_MOVIES, HINDI_SERIES</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Display Name:</label>
                        <input type="text" class="form-input" id="display-name" placeholder="Action Movies" required>
                        <small style="color: #666; font-size: 12px;">This name will be shown on the website</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website Name:</label>
                        <input type="text" class="form-input" id="website-name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL:</label>
                        <input type="url" class="form-input" id="url" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parser Type:</label>
                        <input type="text" class="form-input" id="parser-type" value="li_thumb" required>
                    </div>
                `;
            }
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editItem(configType, section, name, details) {
            currentAction = 'edit';
            currentConfigType = configType;
            currentSection = section;
            currentName = name;
            
            document.getElementById('modal-title').textContent = 'Edit Config';
            
            let formFields = '';
            
            if (configType === 'ALL_SECTION_WEBSITES') {
                const displayName = details.display_name || section;
                formFields = `
                    <div class="form-group">
                        <label class="form-label">Section Key (Not Editable):</label>
                        <input type="text" class="form-input" id="section-name" value="${section}" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                        <small style="color: #666; font-size: 12px;">Section key cannot be changed after creation</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Display Name:</label>
                        <input type="text" class="form-input" id="display-name" value="${displayName}" required>
                        <small style="color: #666; font-size: 12px;">This name will be shown on the website</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website Name:</label>
                        <input type="text" class="form-input" id="website-name" value="${name}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL:</label>
                        <input type="url" class="form-input" id="url" value="${details.url}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parser Type:</label>
                        <input type="text" class="form-input" id="parser-type" value="${details.parser_type}" required>
                    </div>
                `;
            } else {
                const displayName = details.display_name || section;
                formFields = `
                    <div class="form-group">
                        <label class="form-label">Section/Category Key (Not Editable):</label>
                        <input type="text" class="form-input" id="section-name" value="${section}" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                        <small style="color: #666; font-size: 12px;">Section/Category key cannot be changed after creation</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Display Name:</label>
                        <input type="text" class="form-input" id="display-name" value="${displayName}" required>
                        <small style="color: #666; font-size: 12px;">This name will be shown on the website</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website Name:</label>
                        <input type="text" class="form-input" id="website-name" value="${name}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL:</label>
                        <input type="url" class="form-input" id="url" value="${details.url}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parser Type:</label>
                        <input type="text" class="form-input" id="parser-type" value="${details.parser_type}" required>
                    </div>
                `;
            }
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function openAddSearchModal() {
            currentAction = 'add';
            currentConfigType = 'SEARCH_WEBSITES';
            document.getElementById('modal-title').textContent = 'Add Search Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Search Parameter:</label>
                    <input type="text" class="form-input" id="search-param" value="s" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type:</label>
                    <select class="form-input" id="type" required onchange="toggleApiFields()">
                        <option value="html">HTML</option>
                        <option value="api">API</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" required>
                </div>
                <div class="form-group" id="api-url-group" style="display:none;">
                    <label class="form-label">API URL:</label>
                    <input type="url" class="form-input" id="api-url">
                </div>
                <div class="form-group" id="movie-base-url-group" style="display:none;">
                    <label class="form-label">Movie Base URL:</label>
                    <input type="url" class="form-input" id="movie-base-url">
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editSearchItem(name, details) {
            currentAction = 'edit';
            currentConfigType = 'SEARCH_WEBSITES';
            currentName = name;
            
            document.getElementById('modal-title').textContent = 'Edit Search Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" value="${name}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" value="${details.url}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Search Parameter:</label>
                    <input type="text" class="form-input" id="search-param" value="${details.search_param}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type:</label>
                    <select class="form-input" id="type" required onchange="toggleApiFields()">
                        <option value="html" ${details.type === 'html' ? 'selected' : ''}>HTML</option>
                        <option value="api" ${details.type === 'api' ? 'selected' : ''}>API</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="${details.parser_type}" required>
                </div>
                <div class="form-group" id="api-url-group" style="display:${details.type === 'api' ? 'block' : 'none'};">
                    <label class="form-label">API URL:</label>
                    <input type="url" class="form-input" id="api-url" value="${details.api_url || ''}">
                </div>
                <div class="form-group" id="movie-base-url-group" style="display:${details.type === 'api' ? 'block' : 'none'};">
                    <label class="form-label">Movie Base URL:</label>
                    <input type="url" class="form-input" id="movie-base-url" value="${details.movie_base_url || ''}">
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function openAddHeroModal() {
            currentAction = 'add';
            currentConfigType = 'HERO_CAROUSEL_WEBSITES';
            document.getElementById('modal-title').textContent = 'Add Hero Carousel Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="li_thumb" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editHeroItem(name, details) {
            currentAction = 'edit';
            currentConfigType = 'HERO_CAROUSEL_WEBSITES';
            currentName = name;
            
            document.getElementById('modal-title').textContent = 'Edit Hero Carousel Website';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Website Name:</label>
                    <input type="text" class="form-input" id="website-name" value="${name}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">URL:</label>
                    <input type="url" class="form-input" id="url" value="${details.url}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Parser Type:</label>
                    <input type="text" class="form-input" id="parser-type" value="${details.parser_type}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function toggleApiFields() {
            const type = document.getElementById('type').value;
            const apiUrlGroup = document.getElementById('api-url-group');
            const movieBaseUrlGroup = document.getElementById('movie-base-url-group');
            
            if (type === 'api') {
                apiUrlGroup.style.display = 'block';
                movieBaseUrlGroup.style.display = 'block';
            } else {
                apiUrlGroup.style.display = 'none';
                movieBaseUrlGroup.style.display = 'none';
            }
        }

        function editSectionDisplayName(section, currentDisplayName) {
            currentAction = 'edit_display_name';
            currentConfigType = 'ALL_SECTION_WEBSITES';
            currentSection = section;
            
            document.getElementById('modal-title').textContent = 'Edit Section Display Name';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Section Key:</label>
                    <input type="text" class="form-input" value="${section}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Display Name:</label>
                    <input type="text" class="form-input" id="display-name" value="${currentDisplayName}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editCategoryDisplayName(category, currentDisplayName) {
            currentAction = 'edit_display_name';
            currentConfigType = 'CATEGORIES_WEBSITES';
            currentSection = category;
            
            document.getElementById('modal-title').textContent = 'Edit Category Display Name';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Category Key:</label>
                    <input type="text" class="form-input" value="${category}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Display Name:</label>
                    <input type="text" class="form-input" id="display-name" value="${currentDisplayName}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function editLatestDisplayName(category, currentDisplayName) {
            currentAction = 'edit_display_name';
            currentConfigType = 'LATEST_WEBSITES';
            currentSection = category;
            
            document.getElementById('modal-title').textContent = 'Edit Latest Display Name';
            
            const formFields = `
                <div class="form-group">
                    <label class="form-label">Category Key:</label>
                    <input type="text" class="form-input" value="${category}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Display Name:</label>
                    <input type="text" class="form-input" id="display-name" value="${currentDisplayName}" required>
                </div>
            `;
            
            document.getElementById('form-fields').innerHTML = formFields;
            document.getElementById('modal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('active');
        }

        async function saveConfig(event) {
            event.preventDefault();
            
            const modal = document.getElementById('modal');
            
            const data = {
                action: currentAction,
                config_type: currentConfigType,
                old_section: currentSection,
                old_name: currentName
            };

            if (currentAction === 'edit_display_name') {
                data.section = currentSection;
                data.display_name = modal.querySelector('#display-name').value;
            } else if (currentConfigType === 'SEARCH_WEBSITES') {
                data.name = modal.querySelector('#website-name').value;
                data.url = modal.querySelector('#url').value;
                data.search_param = modal.querySelector('#search-param').value;
                data.type = modal.querySelector('#type').value;
                data.parser_type = modal.querySelector('#parser-type').value;
                
                if (data.type === 'api') {
                    const apiUrl = modal.querySelector('#api-url').value;
                    const movieBaseUrl = modal.querySelector('#movie-base-url').value;
                    if (apiUrl) data.api_url = apiUrl;
                    if (movieBaseUrl) data.movie_base_url = movieBaseUrl;
                }
            } else if (currentConfigType === 'HERO_CAROUSEL_WEBSITES') {
                data.name = modal.querySelector('#website-name').value;
                data.url = modal.querySelector('#url').value;
                data.parser_type = modal.querySelector('#parser-type').value;
            } else if (currentConfigType === 'ALL_SECTION_WEBSITES') {
                data.section = modal.querySelector('#section-name').value.toUpperCase();
                data.name = modal.querySelector('#website-name').value;
                data.url = modal.querySelector('#url').value;
                data.parser_type = modal.querySelector('#parser-type').value;
                
                const displayNameInput = modal.querySelector('#display-name');
                if (displayNameInput) {
                    data.display_name = displayNameInput.value;
                }
            } else {
                const sectionInput = modal.querySelector('#section-name');
                data.section = sectionInput.value.toUpperCase();
                data.name = modal.querySelector('#website-name').value;
                data.url = modal.querySelector('#url').value;
                data.parser_type = modal.querySelector('#parser-type').value;
                
                const displayNameInput = modal.querySelector('#display-name');
                if (displayNameInput) {
                    data.display_name = displayNameInput.value;
                }
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error saving config: ' + error.message, 'error');
            }
        }

        async function deleteItem(configType, section, name) {
            if (!confirm(`Are you sure you want to delete ${name} from ${section}?`)) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        config_type: configType,
                        section: section,
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error deleting config: ' + error.message, 'error');
            }
        }

        async function deleteSearchItem(name) {
            if (!confirm(`Are you sure you want to delete ${name}?`)) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        config_type: 'SEARCH_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error deleting config: ' + error.message, 'error');
            }
        }

        async function deleteHeroItem(name) {
            if (!confirm(`Are you sure you want to delete ${name}?`)) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        config_type: 'HERO_CAROUSEL_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error deleting config: ' + error.message, 'error');
            }
        }

        async function toggleHideItem(configType, section, name) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'toggle_hide',
                        config_type: configType,
                        section: section,
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error toggling visibility: ' + error.message, 'error');
            }
        }

        async function toggleHideSearchItem(name) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'toggle_hide',
                        config_type: 'SEARCH_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error toggling visibility: ' + error.message, 'error');
            }
        }

        async function toggleHideHeroItem(name) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'toggle_hide',
                        config_type: 'HERO_CAROUSEL_WEBSITES',
                        name: name
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error toggling visibility: ' + error.message, 'error');
            }
        }

        async function moveSection(configType, sectionName, direction) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'move_section',
                        config_type: configType,
                        section: sectionName,
                        direction: direction
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error moving section: ' + error.message, 'error');
            }
        }

        async function moveSearchWebsite(websiteName, direction) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'move_section',
                        config_type: 'SEARCH_WEBSITES',
                        section: websiteName,
                        direction: direction
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error moving search website: ' + error.message, 'error');
            }
        }

        document.getElementById('logo-upload').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const statusDiv = document.getElementById('logo-status');
            statusDiv.textContent = 'Uploading...';
            statusDiv.style.color = '#667eea';
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('image_type', 'logo');
            
            try {
                const response = await fetch('upload-image-api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('logo-preview').src = result.path + '?t=' + Date.now();
                    statusDiv.textContent = 'Logo uploaded successfully!';
                    statusDiv.style.color = 'green';
                    showAlert('Logo uploaded successfully!', 'success');
                } else {
                    statusDiv.textContent = result.message;
                    statusDiv.style.color = 'red';
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                statusDiv.textContent = 'Upload failed: ' + error.message;
                statusDiv.style.color = 'red';
                showAlert('Upload failed', 'error');
            }
            
            e.target.value = '';
        });
        
        document.getElementById('background-upload').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const statusDiv = document.getElementById('background-status');
            statusDiv.textContent = 'Uploading...';
            statusDiv.style.color = '#667eea';
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('image_type', 'background');
            
            try {
                const response = await fetch('upload-image-api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('background-preview').src = result.path + '?t=' + Date.now();
                    statusDiv.textContent = 'Background uploaded successfully!';
                    statusDiv.style.color = 'green';
                    showAlert('Background uploaded successfully!', 'success');
                } else {
                    statusDiv.textContent = result.message;
                    statusDiv.style.color = 'red';
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                statusDiv.textContent = 'Upload failed: ' + error.message;
                statusDiv.style.color = 'red';
                showAlert('Upload failed', 'error');
            }
            
            e.target.value = '';
        });
        
        document.getElementById('pwa-logo-upload').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const statusDiv = document.getElementById('pwa-logo-status');
            statusDiv.textContent = 'Uploading...';
            statusDiv.style.color = '#667eea';
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('image_type', 'pwa_logo');
            
            try {
                const response = await fetch('upload-image-api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('pwa-logo-preview').src = result.path + '?t=' + Date.now();
                    statusDiv.textContent = 'PWA Logo uploaded successfully!';
                    statusDiv.style.color = 'green';
                    showAlert('PWA Logo uploaded successfully!', 'success');
                } else {
                    statusDiv.textContent = result.message;
                    statusDiv.style.color = 'red';
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                statusDiv.textContent = 'Upload failed: ' + error.message;
                statusDiv.style.color = 'red';
                showAlert('Upload failed', 'error');
            }
            
            e.target.value = '';
        });
        
        async function saveSiteSettings() {
            const websiteName = document.querySelector('#site-settings #website-name').value.trim();
            
            if (!websiteName) {
                showAlert('Website name is required', 'error');
                return;
            }
            
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update_site_settings',
                        website_name: websiteName
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Site settings saved successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error saving settings: ' + error.message, 'error');
            }
        }

        async function resetWeeklyViews() {
            if (!confirm('Are you sure you want to reset all Weekly Top 10 view data? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'reset_weekly_views'
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error resetting weekly views: ' + error.message, 'error');
            }
        }

        async function searchMoviesForHero() {
            const searchInput = document.getElementById('hero-movie-search');
            const query = searchInput.value.trim();
            
            if (!query) {
                showAlert('Please enter a search query', 'error');
                return;
            }
            
            const resultsDiv = document.getElementById('hero-search-results');
            const contentDiv = document.getElementById('hero-search-results-content');
            
            contentDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #64748b;">Searching...</div>';
            resultsDiv.style.display = 'block';
            
            try {
                const response = await fetch(`search.php?query=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (!data.success || !data.results || data.results.length === 0) {
                    contentDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #94a3b8;">No movies found. Try a different search term.</div>';
                    return;
                }
                
                let hasResults = false;
                let resultsHTML = '';
                
                data.results.forEach(websiteResult => {
                    if (websiteResult.success && websiteResult.results && websiteResult.results.length > 0) {
                        hasResults = true;
                        
                        resultsHTML += `
                            <div style="margin-bottom: 24px;">
                                <h5 style="color: #1a202c; font-size: 13px; font-weight: 600; margin-bottom: 12px; padding-left: 4px;">${websiteResult.website}</h5>
                                <div style="display: flex; gap: 12px; overflow-x: auto; scroll-behavior: smooth; padding: 4px 0;">
                        `;
                        
                        websiteResult.results.forEach((movie, index) => {
                            if (index < 10) {
                                const isAdded = window.heroManualMovies && window.heroManualMovies.some(m => m.link === movie.link);
                                
                                resultsHTML += `
                                    <div style="flex: 0 0 auto; width: 110px; display: flex; flex-direction: column; gap: 6px;">
                                        <div style="position: relative; width: 110px; background: #f8f9fa; border-radius: 6px; overflow: hidden; cursor: pointer; transition: all 0.3s ease;">
                                            <img 
                                                src="${movie.image || ''}" 
                                                alt="${movie.title}"
                                                style="width: 110px; height: 165px; object-fit: cover; display: block;"
                                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22300%22%3E%3Crect fill=%22%23e2e8f0%22 width=%22200%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%2394a3b8%22 font-family=%22sans-serif%22 font-size=%2216%22%3ENo Image%3C/text%3E%3C/svg%3E'"
                                            >
                                        </div>
                                        <div style="padding: 0 2px;">
                                            <div style="font-size: 11px; color: #1a202c; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500; margin-bottom: 4px;">${movie.title}</div>
                                            <div style="font-size: 9px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 5px;">
                                                ${movie.language ? `${movie.language}` : ''}
                                            </div>
                                            ${isAdded ? `
                                                <button 
                                                    disabled
                                                    style="width: 100%; padding: 5px 10px; background: #94a3b8; color: white; border: none; border-radius: 4px; cursor: not-allowed; font-size: 10px; font-weight: 500; opacity: 0.6;"
                                                >
                                                    Added
                                                </button>
                                            ` : `
                                                <button 
                                                    onclick='addMovieToHeroCarousel(${JSON.stringify(movie).replace(/'/g, "&apos;")})'
                                                    style="width: 100%; padding: 5px 10px; background: #6366f1; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 10px; font-weight: 500;"
                                                >
                                                    Add
                                                </button>
                                            `}
                                        </div>
                                    </div>
                                `;
                            }
                        });
                        
                        resultsHTML += `
                                </div>
                            </div>
                        `;
                    }
                });
                
                if (!hasResults) {
                    contentDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #94a3b8;">No movies found. Try a different search term.</div>';
                    return;
                }
                
                contentDiv.innerHTML = resultsHTML;
                
            } catch (error) {
                contentDiv.innerHTML = `<div style="text-align: center; padding: 20px; color: #ef4444;">Error: ${error.message}</div>`;
            }
        }

        function closeHeroSearchResults() {
            document.getElementById('hero-search-results').style.display = 'none';
            document.getElementById('hero-movie-search').value = '';
        }

        async function addMovieToHeroCarousel(movie) {
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add_hero_manual_movie',
                        movie: movie
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert('Movie added to hero carousel successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message || 'Error adding movie', 'error');
                }
            } catch (error) {
                showAlert('Error adding movie: ' + error.message, 'error');
            }
        }

        async function deleteManualHeroMovie(index) {
            if (!confirm('Are you sure you want to remove this movie from the hero carousel?')) {
                return;
            }

            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete_hero_manual_movie',
                        index: index
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert('Movie removed successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message || 'Error removing movie', 'error');
                }
            } catch (error) {
                showAlert('Error removing movie: ' + error.message, 'error');
            }
        }

        async function toggleManualMoviesVisibility() {
            const toggleBtn = document.getElementById('toggle-manual-movies-btn');
            const originalContent = toggleBtn.innerHTML;
            
            toggleBtn.disabled = true;
            toggleBtn.innerHTML = '<span>Processing...</span>';
            
            try {
                const response = await fetch('config-api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'toggle_hero_manual_movies_visibility'
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message || 'Error toggling visibility', 'error');
                    toggleBtn.disabled = false;
                    toggleBtn.innerHTML = originalContent;
                }
            } catch (error) {
                showAlert('Error toggling visibility: ' + error.message, 'error');
                toggleBtn.disabled = false;
                toggleBtn.innerHTML = originalContent;
            }
        }

    </script>
</body>
</html>
