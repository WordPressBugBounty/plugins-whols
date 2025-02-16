<?php
return [
    // Admin - Email Notifications
    'enable_registration_notification_for_admin' => [
        'id' => 'enable_registration_notification_for_admin',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('If Enabled, The site admin will get an email about the new wholesaler registration request.', 'whols'),
        'default' => '0'
    ],
    'registration_notification_recipients_for_admin' => [
        'id' => 'registration_notification_recipients_for_admin',
        'type' => 'text',
        'title' => __('Email Recipients', 'whols'),
        'help' => __('Specify email addresses that should receive new wholesaler notifications. <br>• Multiple emails can be separated by commas. <br>• Admin email will be used if left empty.', 'whols'),
        'default' => '',
    ],
    'registration_notification_subject_for_admin' => [
        'id' => 'registration_notification_subject_for_admin',
        'type' => 'text',
        'title' => __('Email Subject', 'whols'),
        'help' => __('Specify the email subject.', 'whols'),
        'desc' => __('Use these {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'),
        'default' => __('[{site_title}] New Wholesaler Registration Request from {name} ', 'whols')
    ],
    'registration_notification_message_for_admin' => [
        'id' => 'registration_notification_message_for_admin',
        'type' => 'wp_editor',
        'title' => __('Message', 'whols'),
        'label_position' => 'top',
        'help' => __('Specify the email message.', 'whols'),
        'desc' => __('Use these {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'),
        'default' => __('Hello Admin,

A new wholesale registration request has been submitted on your website.

Registration Details:
------------------------
Full Name: {name}
Email Address: {email}
Submission Date: {date}
Submission Time: {time}

This request requires your review. Please log in to your WordPress dashboard to approve or reject this registration.

Best regards,
WholS Pro System', 'whols')
    ],

    // Request a Quote
    'enable_raq_email_notification' => [
        'id' => 'enable_raq_email_notification',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('If enabled, the site admin will receive an email when a customer submits a request for a quote.', 'whols'),
        'default' => '0'
    ],
    'request_a_quote_email_subject' => [
        'id' => 'request_a_quote_email_subject',
        'type' => 'text',
        'title' => __('Email Subject', 'whols'),
        'desc' => __('Use these {name}, {email}, {subject} placeholder tags to get dynamic content.', 'whols'),
        'default' => '[{site_title}] New Quote Request from {name}'
    ],


    // Conversation
    'request_a_quote_email_message' => [
        'id' => 'request_a_quote_email_message',
        'type' => 'wp_editor',
        'title' => __('Message', 'whols'),
        'label_position' => 'top',
        'desc' => __('Use these {name}, {email}, {date}, {time}, {products}, {message} placeholder tags to get dynamic content.', 'whols'),
        'default' => 'Name: {name}
Email: {email}
Subject: {subject}
Message: {message}
Products: {products}'
    ],
    'enable_conversation_email_notification' => [
        'id' => 'enable_conversation_email_notification',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('If enabled, the site admin will receive an email when a new conversation is started.', 'whols'),
        'default' => '0'
    ],
    'conversation_start_email_subject' => [
        'id' => 'conversation_start_email_subject',
        'type' => 'text',
        'title' => __('Email Subject', 'whols'),
        'desc' => __('Use these {name}, {email}, {subject} placeholder tags to get dynamic content.', 'whols'),
        'default' => '[{site_title}] New Conversation from {name}'
    ],
    'conversation_start_email_body' => [
        'id' => 'conversation_start_email_body',
        'type' => 'wp_editor',
        'title' => __('Message', 'whols'),
        'label_position' => 'top',
        'desc' => __('Use these {name}, {email}, {date}, {time}, {message} placeholder tags to get dynamic content.', 'whols'),
        'default' => 'Name: {name}
Email: {email}
Subject: {subject}
Message: {message}
Products: {products}'
    ],

    // Customer - Email Notifications
    // Registration Confirmation
    'enable_registration_notification_for_user' => [
        'id' => 'enable_registration_notification_for_user',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('If Enabled, The registered wholesale customer will get an email about the registration.', 'whols'),
        'default' => '0'
    ],
    'registration_notification_subject_for_user' => [
        'id' => 'registration_notification_subject_for_user',
        'type' => 'text',
        'title' => __('Email Subject', 'whols'),
        'desc' => __('Use these {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'),
        'default' => __('[{site_title}] Welcome - Your Wholesale Account Registration', 'whols')
    ],
    'registration_notification_message_for_user' => [
        'id' => 'registration_notification_message_for_user',
        'type' => 'wp_editor',
        'title' => __(' Message', 'whols'),
        'label_position' => 'top',
        'desc' => __('Use these {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'),
        'default' => __('Hi {name},

Thanks for registering with {site_title}. Your wholesale account request is under review.

Details:
Email: {email}
Date: {date}

We\'ll notify you once your account is approved.

Regards,
{site_title}', 'whols')
    ],

    // Account Approval
    'enable_approved_notification' => [
        'id' => 'enable_approved_notification',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('If Enabled, The registered wholesale customer will get an email if the wholesaler request is approved.', 'whols'),
        'default' => '0',
        'class' => 'whols-pro-field-opacity'
    ],
    'approved_email_subject' => [
        'id' => 'approved_email_subject',
        'type' => 'text',
        'title' => __('Email Subject', 'whols'),
        'default' => __('[{site_title}] Your Wholesale Account Request is Approved', 'whols'),
        'class' => 'whols-pro-field-opacity'
    ],
    'approved_email_message' => [
        'id' => 'approved_email_message',
        'type' => 'wp_editor',
        'title' => __('Message', 'whols'),
        'label_position' => 'top',
        'desc' => __('Use these {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'),
        'default' => __('Hi {name},

Good news! Your wholesale account request for {site_title} has been approved.

You can now log in to access wholesale pricing and place orders:
Email: {email}
Password: The one you set during registration

Visit our store: {shop_url}

Welcome aboard!
{site_title}', 'whols'),
        'class' => 'whols-pro-field-opacity'
    ],

    // Account Rejection
    'enable_rejection_notification' => [
        'id' => 'enable_rejection_notification',
        'type' => 'switch',
        'title' => __('Enable', 'whols'),
        'label' => __('Yes', 'whols'),
        'help' => __('If Enabled, The registered wholesale customer will get an email if the wholesaler request is rejected.', 'whols'),
        'default' => '0',
        'class' => 'whols-pro-field-opacity'
    ],
    'rejection_email_subject' => [
        'id' => 'rejection_email_subject',
        'type' => 'text',
        'title' => __('Email Subject', 'whols'),
        'default' => __('[{site_title}] Your Wholesale Account Request is Rejected', 'whols'),
        'class' => 'whols-pro-field-opacity'
    ],
    'rejection_email_message' => [
        'id' => 'rejection_email_message',
        'type' => 'wp_editor',
        'title' => __('Message', 'whols'),
        'label_position' => 'top',
        'desc' => __('Use these {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'),
        'default' => __('Hi {name},

We\'re sorry to inform you that your wholesale account request for {site_title} has been rejected.

Please contact us for more information.

Regards,
{site_title}', 'whols'),
        'class' => 'whols-pro-field-opacity'
    ]
];