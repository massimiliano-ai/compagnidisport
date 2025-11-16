<?php
/**
 * Single Attività Template
 */

get_header();

while (have_posts()) : the_post();
    $activity_id = get_the_ID();
    $author_id = get_the_author_meta('ID');
    $is_organizer = is_user_logged_in() && get_current_user_id() == $author_id;
    $is_participant = is_user_logged_in() && CDV_Participants::is_participant($activity_id, get_current_user_id(), 'accepted');
    $has_requested = is_user_logged_in() && CDV_Participants::is_participant($activity_id, get_current_user_id(), 'pending');
    $participants = CDV_Participants::get_participants($activity_id, 'accepted');
    $pending_requests = CDV_Participants::get_participants($activity_id, 'pending');
    ?>

    <style>
        /* Import Montserrat font */
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        /* Travel hero with overlay effect */
        .travel-hero {
            position: relative;
            width: 100%;
            height: 450px;
            overflow: hidden;
            margin-bottom: calc(var(--spacing-unit) * 4);
            border-radius: 0;
        }
        .travel-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%);
            pointer-events: none;
        }
        .travel-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .travel-hero:hover img {
            transform: scale(1.05);
        }

        .travel-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: calc(var(--spacing-unit) * 4);
            margin-bottom: calc(var(--spacing-unit) * 6);
        }

        .travel-header {
            margin-bottom: calc(var(--spacing-unit) * 4);
        }

        .travel-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #315362;
            margin-bottom: calc(var(--spacing-unit) * 2);
            line-height: 1.3;
        }

        .travel-badges {
            display: flex;
            gap: calc(var(--spacing-unit) * 1);
            margin-bottom: calc(var(--spacing-unit) * 2);
            flex-wrap: wrap;
        }

        .travel-badges .badge {
            font-family: 'Montserrat', sans-serif;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .travel-badges .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .badge-primary {
            background: linear-gradient(135deg, #315362 0%, #426b7d 100%);
            color: white;
        }

        .badge-success {
            background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
            color: white;
        }

        .badge-warning {
            background: linear-gradient(135deg, #ef7b3c 0%, #ff8f50 100%);
            color: white;
        }

        .badge-error {
            background: linear-gradient(135deg, #dc3545 0%, #e85563 100%);
            color: white;
        }

        .travel-description {
            margin-bottom: calc(var(--spacing-unit) * 4);
            line-height: 1.8;
            font-size: 1.05rem;
            color: #4a5568;
        }

        .sidebar-card {
            background: white;
            padding: calc(var(--spacing-unit) * 3);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: calc(var(--spacing-unit) * 3);
            transition: all 0.3s ease;
        }

        .sidebar-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .sidebar-card h3 {
            font-family: 'Montserrat', sans-serif;
            margin-bottom: calc(var(--spacing-unit) * 2);
            padding-bottom: calc(var(--spacing-unit) * 2);
            border-bottom: 3px solid #315362;
            color: #315362;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .organizer-profile {
            text-align: center;
            display: block;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .organizer-profile:hover {
            transform: scale(1.02);
        }

        .organizer-profile:hover .organizer-name {
            color: #ef7b3c;
        }

        .organizer-profile img {
            margin: 0 auto calc(var(--spacing-unit) * 2);
            border-radius: 50%;
            border: 3px solid #315362;
            transition: all 0.3s ease;
        }

        .organizer-profile:hover img {
            border-color: #ef7b3c;
            box-shadow: 0 4px 15px rgba(239, 123, 60, 0.3);
        }

        .organizer-bio {
            margin-top: calc(var(--spacing-unit) * 2);
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        /* Wishlist Card */
        .wishlist-card {
            text-align: center;
        }
        .wishlist-toggle-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 500;
            border: 2px solid #e2e8f0;
            background: white;
            color: #4a5568;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .wishlist-toggle-btn:hover {
            border-color: #f56565;
            background: #fff5f5;
            color: #f56565;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 101, 101, 0.2);
        }
        .wishlist-toggle-btn.wishlist-active {
            border-color: #f56565;
            background: #f56565;
            color: white;
        }
        .wishlist-toggle-btn.wishlist-active:hover {
            background: #e53e3e;
            border-color: #e53e3e;
        }
        .wishlist-toggle-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        .wishlist-icon {
            font-size: 1.2rem;
            line-height: 1;
        }
        .wishlist-active .wishlist-icon {
            animation: heartBeat 0.5s ease;
        }
        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.3); }
            50% { transform: scale(1.1); }
        }
        .wishlist-help-text {
            margin-top: 12px;
            font-size: 0.85rem;
            color: #718096;
        }
        /* Notification Toast */
        .cdv-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            display: none;
            font-size: 0.95rem;
            font-weight: 500;
            max-width: 300px;
        }
        .cdv-notification.success {
            border-left: 4px solid #48bb78;
            color: #22543d;
        }
        .cdv-notification.error {
            border-left: 4px solid #f56565;
            color: #742a2a;
        }
        .travel-details-box-top {
            background: linear-gradient(135deg, #f0f4f8 0%, #ffffff 100%);
            border: 3px solid #315362;
            border-radius: 16px;
            padding: calc(var(--spacing-unit) * 5);
            margin: calc(var(--spacing-unit) * 4) 0;
            box-shadow: 0 8px 30px rgba(49, 83, 98, 0.15);
            transition: all 0.3s ease;
        }

        .travel-details-box-top:hover {
            box-shadow: 0 12px 40px rgba(49, 83, 98, 0.2);
            transform: translateY(-2px);
        }

        .travel-details-box-top h3 {
            font-family: 'Montserrat', sans-serif;
            margin: 0 0 calc(var(--spacing-unit) * 4) 0;
            font-size: 1.7rem;
            font-weight: 700;
            color: #315362;
            padding-bottom: calc(var(--spacing-unit) * 2);
            border-bottom: 3px solid #ef7b3c;
            position: relative;
        }

        .travel-details-box-top h3::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #315362;
        }

        .travel-details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: calc(var(--spacing-unit) * 2.5);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: calc(var(--spacing-unit) * 0.8);
            padding: calc(var(--spacing-unit) * 2);
            background: white;
            border-radius: 10px;
            border-left: 4px solid #ef7b3c;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }

        .detail-item:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateX(3px);
            border-left-color: #315362;
        }

        .detail-item strong {
            font-family: 'Montserrat', sans-serif;
            color: #315362;
            font-size: 0.9rem;
            font-weight: 600;
            display: block;
            letter-spacing: 0.3px;
        }

        .detail-item span {
            color: #4a5568;
            font-size: 1rem;
            font-weight: 500;
        }

        .detail-item-full {
            grid-column: 1 / -1;
        }

        .detail-item-requirements {
            background: linear-gradient(135deg, #fff9e6 0%, #fffbf0 100%);
            border-left-color: #ffc107;
        }

        .detail-item-requirements:hover {
            background: linear-gradient(135deg, #fff6d9 0%, #fffae8 100%);
        }

        .detail-item-requirements span {
            white-space: pre-wrap;
            line-height: 1.7;
            color: #6b5100;
        }
        @media (max-width: 768px) {
            .travel-details-grid {
                grid-template-columns: 1fr;
            }
            .travel-details-box-top {
                padding: calc(var(--spacing-unit) * 3);
                margin: calc(var(--spacing-unit) * 3) 0;
            }
            .travel-details-box-top h3 {
                font-size: 1.3rem;
            }
        }
        .participants-section,
        .pending-requests-section {
            background: white;
            padding: calc(var(--spacing-unit) * 4);
            border-radius: 16px;
            margin-bottom: calc(var(--spacing-unit) * 4);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .participants-section h3,
        .pending-requests-section h3 {
            font-family: 'Montserrat', sans-serif;
            color: #315362;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: calc(var(--spacing-unit) * 3);
            padding-bottom: calc(var(--spacing-unit) * 2);
            border-bottom: 3px solid #ef7b3c;
        }

        .participants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: calc(var(--spacing-unit) * 3);
        }

        .participant-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: calc(var(--spacing-unit) * 2.5);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            background: white;
        }

        .participant-card:hover {
            border-color: #ef7b3c;
            box-shadow: 0 6px 20px rgba(239, 123, 60, 0.15);
            transform: translateY(-3px);
        }

        .participant-card:hover .participant-name {
            color: #ef7b3c;
        }

        .participant-card img {
            margin-bottom: calc(var(--spacing-unit) * 1.5);
            border-radius: 50%;
            border: 3px solid #315362;
            transition: all 0.3s ease;
        }

        .participant-card:hover img {
            border-color: #ef7b3c;
            box-shadow: 0 4px 15px rgba(239, 123, 60, 0.3);
        }

        .organizer-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ef7b3c 0%, #ff8f50 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(239, 123, 60, 0.3);
        }
        .request-card {
            display: flex;
            align-items: center;
            gap: calc(var(--spacing-unit) * 2);
            padding: calc(var(--spacing-unit) * 2);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            margin-bottom: calc(var(--spacing-unit) * 2);
        }
        .request-info {
            flex: 1;
        }
        .request-name {
            font-weight: 600;
            margin-bottom: calc(var(--spacing-unit) * 0.5);
        }
        .request-message {
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        .request-actions {
            display: flex;
            gap: calc(var(--spacing-unit) * 1);
        }
        .btn-success {
            background-color: var(--success-color);
            color: white;
            padding: calc(var(--spacing-unit) * 1) calc(var(--spacing-unit) * 2);
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
        }
        .btn-danger {
            background-color: var(--error-color);
            color: white;
            padding: calc(var(--spacing-unit) * 1) calc(var(--spacing-unit) * 2);
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
        }
        .success-card {
            background-color: #f0fdf4;
            border: 2px solid var(--success-color);
        }
        .warning-card {
            background-color: #fffbeb;
            border: 2px solid var(--warning-color);
        }
        /* Group Chat Styles */
        .group-chat-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-top: calc(var(--spacing-unit) * 4);
        }
        .group-chat-header {
            background: var(--primary-color);
            color: white;
            padding: calc(var(--spacing-unit) * 2) calc(var(--spacing-unit) * 3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .group-chat-header h3 {
            margin: 0;
            font-size: 1.25rem;
        }
        .participants-count {
            font-size: 0.875rem;
            opacity: 0.9;
        }
        .group-chat-container {
            padding: calc(var(--spacing-unit) * 3);
        }
        .group-chat-messages {
            background: #f8f9fa;
            border-radius: var(--border-radius);
            padding: calc(var(--spacing-unit) * 2);
            height: 400px;
            overflow-y: auto;
            margin-bottom: calc(var(--spacing-unit) * 2);
            display: flex;
            flex-direction: column;
            gap: calc(var(--spacing-unit) * 2);
        }
        .loading-indicator {
            text-align: center;
            color: #999;
            padding: calc(var(--spacing-unit) * 4);
        }
        .group-message {
            display: flex;
            gap: calc(var(--spacing-unit) * 1.5);
            animation: fadeInMessage 0.3s ease-in;
        }
        @keyframes fadeInMessage {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .group-message.own-message {
            flex-direction: row-reverse;
        }
        .group-message .avatar {
            flex-shrink: 0;
        }
        .group-message .avatar img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }
        .message-bubble {
            background: white;
            padding: calc(var(--spacing-unit) * 1.5);
            border-radius: var(--border-radius);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            max-width: 70%;
        }
        .group-message.own-message .message-bubble {
            background: var(--primary-color);
            color: white;
        }
        .message-user {
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: calc(var(--spacing-unit) * 0.5);
        }
        .group-message.own-message .message-user {
            text-align: right;
        }
        .message-text {
            margin-bottom: calc(var(--spacing-unit) * 0.5);
            line-height: 1.5;
        }
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
        }
        .group-chat-input {
            display: flex;
            gap: calc(var(--spacing-unit) * 2);
        }
        .group-chat-input textarea {
            flex: 1;
            padding: calc(var(--spacing-unit) * 1.5);
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            resize: vertical;
            font-family: inherit;
            font-size: 1rem;
        }
        .group-chat-input textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .group-chat-input button {
            padding: calc(var(--spacing-unit) * 1.5) calc(var(--spacing-unit) * 3);
            white-space: nowrap;
        }
        .button-loading {
            display: inline-block;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 768px) {
            .travel-layout {
                grid-template-columns: 1fr;
            }
            .group-chat-messages {
                height: 300px;
            }
            .message-bubble {
                max-width: 85%;
            }
        }

        /* Photo Gallery Styles */
        .travel-gallery-section {
            margin: 40px 0;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .travel-gallery-section h3 {
            margin-bottom: 25px;
            color: #2d3748;
            font-size: 1.5rem;
        }

        .travel-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .gallery-item:hover .gallery-item-overlay {
            opacity: 1;
        }

        .gallery-view-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .gallery-view-btn:hover {
            background: #667eea;
            color: white;
        }

        .gallery-empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .gallery-manage-link {
            text-align: center;
            margin-top: 20px;
        }

        /* Gallery Lightbox Modal */
        .gallery-lightbox {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.95);
        }

        .gallery-lightbox-content {
            position: relative;
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
            text-align: center;
        }

        .gallery-lightbox-image {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 8px;
        }

        .gallery-lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }

        .gallery-lightbox-close:hover {
            color: #ccc;
        }

        /* Travel Map Section */
        .travel-map-section {
            margin: 40px 0;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .travel-map-section h3 {
            margin-bottom: 20px;
            color: #2d3748;
            font-size: 1.5rem;
        }

        .map-location-text {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            color: #4a5568;
        }

        .map-placeholder {
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        /* User Uploaded Image Section - 50% width */
        .travel-user-image {
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .travel-user-image h3 {
            margin: 0 0 20px 0;
            color: #2d3748;
            font-size: 1.5rem;
        }

        .user-image-wrapper {
            width: 50%;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .user-image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Responsive: full width on mobile */
        @media (max-width: 768px) {
            .user-image-wrapper {
                width: 100%;
            }
        }

        /* Enhanced Button Styles */
        .btn-primary,
        button[type="submit"],
        input[type="submit"] {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #315362 0%, #426b7d 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(49, 83, 98, 0.25);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover,
        button[type="submit"]:hover,
        input[type="submit"]:hover {
            background: linear-gradient(135deg, #ef7b3c 0%, #ff8f50 100%);
            box-shadow: 0 6px 20px rgba(239, 123, 60, 0.35);
            transform: translateY(-2px);
        }

        .btn-secondary {
            font-family: 'Montserrat', sans-serif;
            background: white;
            color: #315362;
            border: 2px solid #315362;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: #315362;
            color: white;
            border-color: #315362;
            box-shadow: 0 4px 15px rgba(49, 83, 98, 0.25);
            transform: translateY(-2px);
        }

        .btn-danger,
        .btn-reject {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #dc3545 0%, #e85563 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(220, 53, 69, 0.25);
        }

        .btn-danger:hover,
        .btn-reject:hover {
            background: linear-gradient(135deg, #c82333 0%, #d73b49 100%);
            box-shadow: 0 5px 18px rgba(220, 53, 69, 0.35);
            transform: translateY(-2px);
        }

        .btn-accept {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(40, 167, 69, 0.25);
        }

        .btn-accept:hover {
            background: linear-gradient(135deg, #218838 0%, #2bb84b 100%);
            box-shadow: 0 5px 18px rgba(40, 167, 69, 0.35);
            transform: translateY(-2px);
        }

        /* Disabled button state */
        .btn-primary:disabled,
        .btn-secondary:disabled,
        .btn-danger:disabled,
        .btn-accept:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }
    </style>
    <main class="site-main single-travel">
        <!-- Hero Image - Priorità alle immagini della tassonomia tipo_sport -->
        <?php
        $taxonomy_hero_url = false;
        if (class_exists('CDV_Taxonomy_Images')) {
            $activity_types = wp_get_post_terms($activity_id, 'tipo_sport', array('fields' => 'ids'));
            if (!empty($activity_types)) {
                $taxonomy_hero_url = CDV_Taxonomy_Images::get_random_term_image($activity_types, 'travel-hero');
            }
        }
        ?>

        <?php if ($taxonomy_hero_url) : ?>
            <div class="travel-hero">
                <img src="<?php echo esc_url($taxonomy_hero_url); ?>" alt="<?php the_title_attribute(); ?>" />
            </div>
        <?php elseif (has_post_thumbnail()) : ?>
            <div class="travel-hero">
                <?php the_post_thumbnail('travel-hero'); ?>
            </div>
        <?php endif; ?>

        <div class="container">
            <div class="travel-layout">
                <!-- Main Content -->
                <article class="travel-content">
                    <header class="travel-header">
                        <div class="travel-badges">
                            <?php cdv_travel_type_badges(); ?>
                            <?php echo cdv_get_travel_status_label(); ?>
                        </div>

                        <h1><?php the_title(); ?></h1>

                        <?php cdv_travel_meta(); ?>
                    </header>

                    <!-- Travel Details Box - Prominent placement -->
                    <div class="travel-details-box-top">
                        <h3>📋 Dettagli Annuncio</h3>
                        <div class="travel-details-grid">
                            <?php
                            // Core fields
                            $start_date = get_post_meta($activity_id, 'cdv_start_date', true);
                            $end_date = get_post_meta($activity_id, 'cdv_end_date', true);
                            $date_type = get_post_meta($activity_id, 'cdv_date_type', true);
                            $activity_month = get_post_meta($activity_id, 'cdv_activity_month', true);
                            $destination = get_post_meta($activity_id, 'cdv_destination', true);
                            $country = get_post_meta($activity_id, 'cdv_country', true);
                            $budget = get_post_meta($activity_id, 'cdv_budget', true);
                            $max_participants = get_post_meta($activity_id, 'cdv_max_participants', true);

                            // Sport-specific fields
                            $activity_time = get_post_meta($activity_id, 'cdv_activity_time', true);
                            $activity_duration = get_post_meta($activity_id, 'cdv_activity_duration', true);
                            $activity_level = get_post_meta($activity_id, 'cdv_activity_level', true);
                            $equipment = get_post_meta($activity_id, 'cdv_equipment', true);
                            $facilities = get_post_meta($activity_id, 'cdv_facilities', true);
                            $requirements = get_post_meta($activity_id, 'cdv_activity_requirements', true);

                            // Activity level labels
                            $level_labels = array(
                                'principiante' => 'Principiante',
                                'intermedio' => 'Intermedio',
                                'avanzato' => 'Avanzato',
                                'esperto' => 'Esperto'
                            );

                            // Equipment labels
                            $equipment_labels = array(
                                'scarpe' => '👟 Scarpe sportive',
                                'racchetta' => '🎾 Racchetta',
                                'bici' => '🚴 Bicicletta',
                                'abbigliamento' => '👕 Abbigliamento tecnico',
                                'borraccia' => '💧 Borraccia',
                                'pallone' => '⚽ Pallone',
                                'casco' => '🪖 Casco',
                                'altro' => 'Altro'
                            );

                            // Facilities labels
                            $facilities_labels = array(
                                'spogliatoi' => '🚿 Spogliatoi',
                                'docce' => '🚿 Docce',
                                'parcheggio' => '🅿️ Parcheggio',
                                'bar' => '☕ Bar/Ristoro',
                                'wifi' => '📶 WiFi',
                                'campo_coperto' => '🏠 Campo coperto',
                                'illuminazione' => '💡 Illuminazione notturna'
                            );
                            ?>

                            <?php if ($date_type === 'month' && $activity_month) : ?>
                                <div class="detail-item">
                                    <strong>📅 Periodo:</strong>
                                    <span><?php echo date_i18n('F Y', strtotime($activity_month . '-01')); ?> (flessibile)</span>
                                </div>
                            <?php else : ?>
                                <?php if ($start_date) : ?>
                                    <div class="detail-item">
                                        <strong>📅 Inizio:</strong>
                                        <span><?php echo date_i18n('d M Y', strtotime($start_date)); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($end_date) : ?>
                                    <div class="detail-item">
                                        <strong>📅 Fine:</strong>
                                        <span><?php echo date_i18n('d M Y', strtotime($end_date)); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($destination) : ?>
                                <div class="detail-item">
                                    <strong>📍 Luogo:</strong>
                                    <span><?php echo esc_html($destination); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($country) : ?>
                                <div class="detail-item">
                                    <strong>🌍 Paese:</strong>
                                    <span><?php echo esc_html($country); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($budget) : ?>
                                <div class="detail-item">
                                    <strong>💰 Budget indicativo:</strong>
                                    <span>€<?php echo number_format($budget, 0, ',', '.'); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($max_participants) : ?>
                                <div class="detail-item">
                                    <strong>👥 Partecipanti:</strong>
                                    <span><?php echo count($participants); ?>/<?php echo $max_participants; ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($activity_time) : ?>
                                <div class="detail-item">
                                    <strong>🕐 Orario:</strong>
                                    <span><?php echo esc_html($activity_time); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($activity_duration) : ?>
                                <div class="detail-item">
                                    <strong>⏱️ Durata:</strong>
                                    <span><?php echo esc_html($activity_duration); ?> minuti</span>
                                </div>
                            <?php endif; ?>

                            <?php if ($activity_level) : ?>
                                <div class="detail-item">
                                    <strong>📊 Livello:</strong>
                                    <span><?php echo isset($level_labels[$activity_level]) ? esc_html($level_labels[$activity_level]) : esc_html($activity_level); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($equipment) && is_array($equipment)) : ?>
                                <div class="detail-item detail-item-full">
                                    <strong>🎒 Attrezzatura necessaria:</strong>
                                    <span><?php
                                        $equipment_texts = array();
                                        foreach ($equipment as $eq) {
                                            if (isset($equipment_labels[$eq])) {
                                                $equipment_texts[] = $equipment_labels[$eq];
                                            } else {
                                                $equipment_texts[] = esc_html($eq);
                                            }
                                        }
                                        echo implode(', ', $equipment_texts);
                                    ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($facilities) && is_array($facilities)) : ?>
                                <div class="detail-item detail-item-full">
                                    <strong>🏢 Strutture disponibili:</strong>
                                    <span><?php
                                        $facilities_texts = array();
                                        foreach ($facilities as $fac) {
                                            if (isset($facilities_labels[$fac])) {
                                                $facilities_texts[] = $facilities_labels[$fac];
                                            } else {
                                                $facilities_texts[] = esc_html($fac);
                                            }
                                        }
                                        echo implode(', ', $facilities_texts);
                                    ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($requirements) : ?>
                                <div class="detail-item detail-item-full detail-item-requirements">
                                    <strong>📝 Requisiti e Note:</strong>
                                    <span><?php echo nl2br(esc_html($requirements)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="travel-description">
                        <?php the_content(); ?>
                    </div>

                    <!-- Featured Image caricata dall'utente - Mostrata dopo la descrizione -->
                    <?php if (has_post_thumbnail() && $taxonomy_hero_url) : ?>
                        <div class="travel-user-image">
                            <h3>📸 Immagine dell'Annuncio</h3>
                            <div class="user-image-wrapper">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Social Sharing -->
                    <div class="travel-share-section">
                        <h3>💬 Condividi questo annuncio</h3>
                        <?php
                        if (class_exists('CDV_Social_Sharing')) {
                            echo CDV_Social_Sharing::render_share_buttons($activity_id);
                        }
                        ?>
                    </div>

                    <!-- Photo Gallery -->
                    <?php
                    $gallery_images = CDV_Travel_Gallery::get_gallery_images($activity_id);
                    if (!empty($gallery_images)) :
                    ?>
                        <div class="travel-gallery-section">
                            <h3>📸 Galleria Fotografica (<?php echo count($gallery_images); ?> foto)</h3>
                            <div class="travel-gallery-grid">
                                <?php foreach ($gallery_images as $image) : ?>
                                    <div class="gallery-item" data-image-id="<?php echo $image['id']; ?>">
                                        <img src="<?php echo esc_url($image['medium']); ?>"
                                             alt="<?php echo esc_attr($image['alt'] ?: 'Foto di attività'); ?>"
                                             data-full="<?php echo esc_url($image['full']); ?>">
                                        <div class="gallery-item-overlay">
                                            <button class="gallery-view-btn" data-full-url="<?php echo esc_url($image['full']); ?>">
                                                <span>🔍</span> Visualizza
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($is_organizer) : ?>
                                <div class="gallery-manage-link">
                                    <a href="#" id="manage-gallery-btn" class="btn btn-secondary">
                                        <span>📷</span> Gestisci Galleria
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($is_organizer) : ?>
                        <div class="travel-gallery-section empty">
                            <div class="gallery-empty-state">
                                <p>📷 Nessuna foto ancora. Aggiungi foto per far vedere la bellezza di questo annuncio!</p>
                                <a href="#" id="add-first-photo-btn" class="btn btn-primary">
                                    Aggiungi Prime Foto
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Travel Map -->
                    <?php
                    // Only show map section if coordinates exist
                    $map_coords = CDV_Travel_Maps::get_travel_coordinates($activity_id);
                    if ($map_coords && isset($map_coords['lat']) && isset($map_coords['lon'])) :
                    ?>
                        <div class="travel-map-section">
                            <h3>📍 Posizione</h3>
                            <?php echo CDV_Travel_Maps::get_map_html($activity_id, '450px'); ?>
                            <?php
                            $map_destination = get_post_meta($activity_id, 'cdv_destination', true);
                            $map_country = get_post_meta($activity_id, 'cdv_country', true);
                            if ($map_destination || $map_country) :
                            ?>
                                <p class="map-location-text">
                                    <strong>Luogo:</strong> <?php echo esc_html($map_destination); ?><?php echo $map_country ? ', ' . esc_html($map_country) : ''; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Participants Section -->
                    <?php if (!empty($participants)) : ?>
                        <div class="participants-section">
                            <h3>Partecipanti (<?php echo count($participants); ?>)</h3>
                            <div class="participants-grid">
                                <!-- Organizer First -->
                                <div class="participant-card-wrapper">
                                    <a href="<?php echo esc_url(CDV_User_Profiles::get_profile_url($author_id)); ?>" class="participant-card organizer">
                                        <?php echo get_avatar($author_id, 80); ?>
                                        <div class="participant-info">
                                            <div class="participant-name">
                                                <?php echo esc_html(get_the_author_meta('user_login', $author_id)); ?>
                                                <span class="organizer-badge">Organizzatore</span>
                                            </div>
                                            <?php
                                            $reputation = get_user_meta($author_id, 'cdv_reputation_score', true);
                                            if ($reputation) {
                                                cdv_display_stars($reputation);
                                            }
                                            ?>
                                        </div>
                                    </a>
                                    <?php if (is_user_logged_in() && get_current_user_id() != $author_id && ($is_participant || $is_organizer)) : ?>
                                        <a href="<?php echo home_url('/dashboard?tab=messages&user_id=' . $author_id . '&activity_id=' . $activity_id); ?>" class="btn btn-sm btn-primary participant-message-btn">
                                            Invia Messaggio
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Other Participants -->
                                <?php foreach ($participants as $participant) :
                                    $user = get_user_by('id', $participant->user_id);
                                    $reputation = get_user_meta($user->ID, 'cdv_reputation_score', true);
                                    ?>
                                    <div class="participant-card-wrapper">
                                        <a href="<?php echo esc_url(CDV_User_Profiles::get_profile_url($user->ID)); ?>" class="participant-card">
                                            <?php echo get_avatar($user->ID, 80); ?>
                                            <div class="participant-info">
                                                <div class="participant-name"><?php echo esc_html($user->user_login); ?></div>
                                                <?php if ($reputation) {
                                                    cdv_display_stars($reputation);
                                                } ?>
                                            </div>
                                        </a>
                                        <div class="participant-actions">
                                            <?php if (is_user_logged_in() && get_current_user_id() != $user->ID && ($is_participant || $is_organizer)) : ?>
                                                <a href="<?php echo home_url('/dashboard?tab=messages&user_id=' . $user->ID . '&activity_id=' . $activity_id); ?>" class="btn btn-sm btn-primary participant-message-btn">
                                                    Invia Messaggio
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($is_organizer) : ?>
                                                <button class="btn btn-sm btn-danger btn-remove-participant"
                                                        data-travel-id="<?php echo $activity_id; ?>"
                                                        data-user-id="<?php echo $user->ID; ?>"
                                                        data-user-name="<?php echo esc_attr($user->user_login); ?>">
                                                    Rimuovi
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Group Chat (only for participants and organizer) -->
                    <?php if (is_user_logged_in() && ($is_participant || $is_organizer)) : ?>
                        <div class="group-chat-section">
                            <div class="group-chat-header">
                                <h3>Chat di Gruppo</h3>
                                <span class="participants-count" id="chat-participants-count">
                                    <?php echo count($participants) + 1; ?> partecipanti
                                </span>
                            </div>

                            <div class="group-chat-container">
                                <div class="group-chat-messages" id="group-chat-messages">
                                    <div class="loading-indicator">Caricamento messaggi...</div>
                                </div>

                                <div class="group-chat-input">
                                    <textarea
                                        id="group-message-input"
                                        placeholder="Scrivi un messaggio al gruppo..."
                                        rows="2"
                                    ></textarea>
                                    <button id="send-group-message" class="btn btn-primary">
                                        <span class="button-text">Invia</span>
                                        <span class="button-loading" style="display: none;">...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Pending Requests (only for organizer) -->
                    <?php if ($is_organizer && !empty($pending_requests)) : ?>
                        <div class="pending-requests-section">
                            <h3>Richieste in Attesa (<?php echo count($pending_requests); ?>)</h3>
                            <div class="requests-list">
                                <?php foreach ($pending_requests as $request) :
                                    $user = get_user_by('id', $request->user_id);
                                    ?>
                                    <div class="request-card" data-user-id="<?php echo $user->ID; ?>">
                                        <?php echo get_avatar($user->ID, 60); ?>
                                        <div class="request-info">
                                            <div class="request-name"><?php echo esc_html($user->user_login); ?></div>
                                            <?php if ($request->message) : ?>
                                                <div class="request-message"><?php echo esc_html($request->message); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="request-actions">
                                            <button class="btn-success btn-accept" data-travel-id="<?php echo $activity_id; ?>" data-user-id="<?php echo $user->ID; ?>">
                                                Accetta
                                            </button>
                                            <button class="btn-danger btn-reject" data-travel-id="<?php echo $activity_id; ?>" data-user-id="<?php echo $user->ID; ?>">
                                                Rifiuta
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </article>

                <!-- Sidebar -->
                <aside class="travel-sidebar">
                    <!-- Organizer Card -->
                    <div class="sidebar-card organizer-card">
                        <h3>Organizzatore</h3>
                        <?php
                        $verified = get_user_meta($author_id, 'cdv_verified', true);
                        $reputation = get_user_meta($author_id, 'cdv_reputation_score', true);
                        $bio = get_user_meta($author_id, 'cdv_bio', true);
                        ?>
                        <a href="<?php echo esc_url(CDV_User_Profiles::get_profile_url($author_id)); ?>" class="organizer-profile">
                            <?php echo get_avatar($author_id, 100); ?>
                            <div class="organizer-name">
                                <?php echo esc_html(get_the_author_meta('user_login', $author_id)); ?>
                                <?php if ($verified === '1') : ?>
                                    <span class="verified-badge" title="Verificato">✓</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($reputation) {
                                cdv_display_stars($reputation);
                            } ?>
                            <?php if ($bio) : ?>
                                <p class="organizer-bio"><?php echo esc_html($bio); ?></p>
                            <?php endif; ?>
                        </a>
                    </div>

                    <!-- Wishlist Card -->
                    <div class="sidebar-card wishlist-card">
                        <?php echo CDV_Wishlist::get_wishlist_button_html($activity_id, 'btn btn-secondary wishlist-toggle-btn'); ?>
                        <p class="wishlist-help-text">Salva questo annuncio per dopo</p>
                    </div>

                    <!-- Join Card -->
                    <?php if (is_user_logged_in()) : ?>
                        <?php if ($is_organizer) : ?>
                            <div class="sidebar-card">
                                <p><strong>Questo è il tuo annuncio!</strong></p>
                                <a href="<?php echo home_url('/modifica-attivita/?activity_id=' . $activity_id); ?>" class="btn-primary" style="width: 100%; text-align: center;">
                                    Modifica Annuncio
                                </a>
                            </div>
                        <?php elseif ($is_participant) : ?>
                            <div class="sidebar-card success-card">
                                <p><strong>✓ Sei un partecipante</strong></p>
                                <p>Hai accesso alla chat di gruppo</p>
                                <button id="leave-travel-btn" class="btn-danger" style="width: 100%; margin-top: 1rem;"
                                        data-travel-id="<?php echo $activity_id; ?>">
                                    Lascia l'Annuncio
                                </button>
                            </div>
                        <?php elseif ($has_requested) : ?>
                            <div class="sidebar-card warning-card">
                                <p><strong>⏳ Richiesta in attesa</strong></p>
                                <p>La tua richiesta è in attesa di approvazione</p>
                            </div>
                        <?php else : ?>
                            <div class="sidebar-card join-card">
                                <h3>Partecipa all'Annuncio</h3>
                                <form id="join-travel-form">
                                    <div class="form-group">
                                        <label for="join-message">Messaggio per l'organizzatore</label>
                                        <textarea id="join-message" rows="4" placeholder="Presentati e spiega perché vuoi unirti..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-primary" style="width: 100%;">
                                        Richiedi di Partecipare
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- Contact Organizer Form - Available to all logged users -->
                        <?php if (is_user_logged_in() && !$is_organizer) : ?>
                            <div class="sidebar-card contact-card">
                                <h3>💬 Chiedi Informazioni</h3>
                                <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">Hai domande? Contatta l'organizzatore</p>
                                <form id="contact-organizer-form">
                                    <div class="form-group">
                                        <label for="contact-message">Il tuo messaggio</label>
                                        <textarea id="contact-message" rows="4" placeholder="Scrivi la tua domanda o richiesta di informazioni..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn-secondary" style="width: 100%;">
                                        Invia Messaggio
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="sidebar-card">
                            <h3>Vuoi partecipare?</h3>
                            <p>Accedi o registrati per unirti a questo annuncio</p>
                            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn-primary" style="width: 100%; text-align: center; margin-bottom: 10px;">
                                Accedi
                            </a>
                            <a href="<?php echo wp_registration_url(); ?>" class="btn-secondary" style="width: 100%; text-align: center;">
                                Registrati
                            </a>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>

        <!-- Gallery Lightbox -->
        <div id="gallery-lightbox" class="gallery-lightbox">
            <div class="gallery-lightbox-content">
                <button class="gallery-lightbox-close">&times;</button>
                <img id="gallery-lightbox-image" class="gallery-lightbox-image" src="" alt="">
            </div>
        </div>
    </main>


    <script>
    jQuery(document).ready(function($) {
        // Join travel form
        $('#join-travel-form').on('submit', function(e) {
            e.preventDefault();

            var message = $('#join-message').val();

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_join_travel',
                    nonce: cdvAjax.nonce,
                    activity_id: <?php echo $activity_id; ?>,
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        alert('Richiesta inviata con successo!');
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore durante l\'invio della richiesta');
                    }
                },
                error: function() {
                    alert('Errore di connessione');
                }
            });
        });

        // Contact organizer form
        $('#contact-organizer-form').on('submit', function(e) {
            e.preventDefault();

            // Check if cdvAjax is defined
            if (typeof cdvAjax === 'undefined') {
                console.error('cdvAjax is not defined');
                showNotification('Errore di configurazione. Ricarica la pagina e riprova.', 'error');
                return;
            }

            var $btn = $(this).find('button[type="submit"]');
            var originalText = $btn.text();
            var message = $('#contact-message').val();

            if (!message.trim()) {
                showNotification('Inserisci un messaggio', 'error');
                return;
            }

            console.log('Sending contact message...', {
                url: cdvAjax.ajaxurl,
                activity_id: <?php echo $activity_id; ?>,
                organizer_id: <?php echo $author_id; ?>
            });

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_contact_organizer',
                    nonce: cdvAjax.nonce,
                    activity_id: <?php echo $activity_id; ?>,
                    organizer_id: <?php echo $author_id; ?>,
                    message: message
                },
                timeout: 30000, // 30 second timeout
                beforeSend: function() {
                    $btn.prop('disabled', true).text('Invio in corso...');
                },
                success: function(response) {
                    console.log('Response received:', response);
                    if (response.success) {
                        showNotification('Messaggio inviato con successo! L\'organizzatore ti risponderà presto.', 'success');
                        $('#contact-message').val('');
                    } else {
                        showNotification(response.data.message || 'Errore durante l\'invio del messaggio', 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', {
                        status: jqXHR.status,
                        statusText: jqXHR.statusText,
                        responseText: jqXHR.responseText,
                        textStatus: textStatus,
                        errorThrown: errorThrown
                    });
                    var errorMsg = 'Errore di connessione';
                    if (jqXHR.status === 0) {
                        errorMsg = 'Nessuna connessione. Verifica la connessione internet.';
                    } else if (jqXHR.status === 404) {
                        errorMsg = 'Pagina non trovata [404]';
                    } else if (jqXHR.status === 500) {
                        errorMsg = 'Errore interno del server [500]. Riprova tra qualche istante.';
                    } else if (textStatus === 'timeout') {
                        errorMsg = 'La richiesta ha impiegato troppo tempo. Il messaggio potrebbe essere stato inviato.';
                    }
                    showNotification(errorMsg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Accept participant
        $('.btn-accept').on('click', function() {
            var btn = $(this);
            var travelId = btn.data('travel-id');
            var userId = btn.data('user-id');

            if (!confirm('Accettare questo partecipante?')) {
                return;
            }

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_accept_participant',
                    nonce: cdvAjax.nonce,
                    activity_id: travelId,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore');
                    }
                }
            });
        });

        // Reject participant
        $('.btn-reject').on('click', function() {
            var btn = $(this);
            var travelId = btn.data('travel-id');
            var userId = btn.data('user-id');

            if (!confirm('Rifiutare questo partecipante?')) {
                return;
            }

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_reject_participant',
                    nonce: cdvAjax.nonce,
                    activity_id: travelId,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore');
                    }
                }
            });
        });

        // Remove participant (organizer action)
        $('.btn-remove-participant').on('click', function() {
            var btn = $(this);
            var travelId = btn.data('travel-id');
            var userId = btn.data('user-id');
            var userName = btn.data('user-name');

            if (!confirm('Sei sicuro di voler rimuovere ' + userName + ' dall\'annuncio?')) {
                return;
            }

            btn.prop('disabled', true).text('Rimozione...');

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_remove_participant',
                    nonce: cdvAjax.nonce,
                    activity_id: travelId,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Partecipante rimosso con successo');
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore durante la rimozione');
                        btn.prop('disabled', false).text('Rimuovi');
                    }
                },
                error: function() {
                    alert('Errore di connessione');
                    btn.prop('disabled', false).text('Rimuovi');
                }
            });
        });

        // Leave travel (participant action)
        $('#leave-travel-btn').on('click', function() {
            var btn = $(this);
            var travelId = btn.data('travel-id');

            if (!confirm('Sei sicuro di voler lasciare questo annuncio? Questa azione non può essere annullata.')) {
                return;
            }

            btn.prop('disabled', true).text('Uscita in corso...');

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_leave_travel',
                    nonce: cdvAjax.nonce,
                    activity_id: travelId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Hai lasciato l\'annuncio con successo');
                        location.reload();
                    } else {
                        alert(response.data.message || 'Errore durante l\'uscita dall\'annuncio');
                        btn.prop('disabled', false).text('Lascia l\'Annuncio');
                    }
                },
                error: function() {
                    alert('Errore di connessione');
                    btn.prop('disabled', false).text('Lascia l\'Annuncio');
                }
            });
        });

        // === GROUP CHAT FUNCTIONALITY ===
        const $groupChatMessages = $('#group-chat-messages');
        const $groupMessageInput = $('#group-message-input');
        const $sendGroupMessageBtn = $('#send-group-message');
        const travelId = <?php echo $activity_id; ?>;
        let chatRefreshInterval = null;

        // Load group chat messages on page load
        if ($groupChatMessages.length > 0) {
            loadGroupMessages();

            // Refresh messages every 5 seconds
            chatRefreshInterval = setInterval(function() {
                loadGroupMessages(true); // true = silent refresh (no loading indicator)
            }, 5000);
        }

        // Send message
        $sendGroupMessageBtn.on('click', function() {
            sendGroupMessage();
        });

        // Send on Enter (without Shift)
        $groupMessageInput.on('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendGroupMessage();
            }
        });

        function loadGroupMessages(silent = false) {
            if (!silent) {
                $groupChatMessages.html('<div class="loading-indicator">Caricamento messaggi...</div>');
            }

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_get_group_messages',
                    nonce: cdvAjax.nonce,
                    activity_id: travelId
                },
                success: function(response) {
                    if (response.success) {
                        displayGroupMessages(response.data.messages);

                        // Update participants count
                        if (response.data.participants_count) {
                            $('#chat-participants-count').text(response.data.participants_count + ' partecipanti');
                        }
                    } else {
                        if (!silent) {
                            $groupChatMessages.html('<div class="loading-indicator" style="color: #dc3545;">Errore: ' + (response.data.message || 'Impossibile caricare i messaggi') + '</div>');
                        }
                    }
                },
                error: function() {
                    if (!silent) {
                        $groupChatMessages.html('<div class="loading-indicator" style="color: #dc3545;">Errore di connessione</div>');
                    }
                }
            });
        }

        function displayGroupMessages(messages) {
            if (!messages || messages.length === 0) {
                $groupChatMessages.html('<div class="loading-indicator">Nessun messaggio ancora. Inizia la conversazione!</div>');
                return;
            }

            // Save scroll position
            const wasAtBottom = $groupChatMessages[0].scrollHeight - $groupChatMessages.scrollTop() <= $groupChatMessages.outerHeight() + 50;

            let html = '';
            messages.forEach(function(msg) {
                const ownClass = msg.is_own ? 'own-message' : '';
                html += `
                    <div class="group-message ${ownClass}" data-message-id="${msg.id}">
                        <div class="avatar">${msg.avatar}</div>
                        <div class="message-bubble">
                            <div class="message-user">${msg.user_name}</div>
                            <div class="message-text">${msg.message}</div>
                            <div class="message-time">${msg.time_ago}</div>
                        </div>
                    </div>
                `;
            });

            $groupChatMessages.html(html);

            // Scroll to bottom if was already at bottom or if it's the first load
            if (wasAtBottom || $groupChatMessages.find('.group-message').length === messages.length) {
                scrollToBottom();
            }
        }

        function sendGroupMessage() {
            const message = $groupMessageInput.val().trim();

            if (!message) {
                alert('Scrivi un messaggio prima di inviare');
                return;
            }

            // Show loading state
            $sendGroupMessageBtn.find('.button-text').hide();
            $sendGroupMessageBtn.find('.button-loading').show();
            $sendGroupMessageBtn.prop('disabled', true);

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_send_group_message',
                    nonce: cdvAjax.nonce,
                    activity_id: travelId,
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        $groupMessageInput.val('');
                        loadGroupMessages();
                    } else {
                        alert('Errore: ' + (response.data.message || 'Impossibile inviare il messaggio'));
                    }
                },
                error: function() {
                    alert('Errore di connessione');
                },
                complete: function() {
                    // Hide loading state
                    $sendGroupMessageBtn.find('.button-text').show();
                    $sendGroupMessageBtn.find('.button-loading').hide();
                    $sendGroupMessageBtn.prop('disabled', false);
                    $groupMessageInput.focus();
                }
            });
        }

        function scrollToBottom() {
            $groupChatMessages.animate({
                scrollTop: $groupChatMessages[0].scrollHeight
            }, 300);
        }

        // Clean up interval on page unload
        $(window).on('beforeunload', function() {
            if (chatRefreshInterval) {
                clearInterval(chatRefreshInterval);
            }
        });

        // Gallery Lightbox
        const lightbox = $('#gallery-lightbox');
        const lightboxImage = $('#gallery-lightbox-image');

        $('.gallery-view-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const fullUrl = $(this).data('full-url');
            lightboxImage.attr('src', fullUrl);
            lightbox.fadeIn(300);
        });

        $('.gallery-lightbox-close').on('click', function() {
            lightbox.fadeOut(300);
        });

        lightbox.on('click', function(e) {
            if (e.target === this) {
                lightbox.fadeOut(300);
            }
        });

        // Close lightbox with ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && lightbox.is(':visible')) {
                lightbox.fadeOut(300);
            }
        });

        // Wishlist toggle functionality
        $('.wishlist-btn').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const travelId = btn.data('travel-id');

            // Disable button during request
            btn.prop('disabled', true);

            $.ajax({
                url: cdvAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cdv_toggle_wishlist',
                    nonce: cdvAjax.nonce,
                    activity_id: travelId
                },
                success: function(response) {
                    if (response.success) {
                        const action = response.data.action;
                        const inWishlist = response.data.in_wishlist;

                        // Update button appearance
                        if (inWishlist) {
                            btn.addClass('wishlist-active');
                            btn.find('.wishlist-icon').text('♥');
                            btn.find('.wishlist-text').text('Salvato');
                        } else {
                            btn.removeClass('wishlist-active');
                            btn.find('.wishlist-icon').text('♡');
                            btn.find('.wishlist-text').text('Salva');
                        }

                        // Show notification
                        showNotification(response.data.message, 'success');
                    } else {
                        showNotification(response.data.message || 'Errore durante l\'operazione', 'error');
                    }
                },
                error: function() {
                    showNotification('Errore di connessione', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });

        // Simple notification function
        function showNotification(message, type) {
            const notification = $('<div class="cdv-notification ' + type + '">' + message + '</div>');
            $('body').append(notification);

            // Fade in
            notification.fadeIn(300);

            // Auto remove after 3 seconds
            setTimeout(function() {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });
    </script>

    <?php
endwhile;

get_footer();
