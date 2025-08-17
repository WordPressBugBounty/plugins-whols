<?php
return array(
    // Heading
    array(
        'type'    => 'heading',
        'content' => esc_html__('Admin Notification for Wholesaler Registrations', 'whols'),
    ),

    // enable_registration_notification_for_admin
    array(
        'id'         => 'enable_registration_notification_for_admin',
        'type'       => 'checkbox',
        'title'      => esc_html__( 'Notify Admin of New Wholesaler', 'whols' ),
        'label'      => esc_html__( 'Enable', 'whols' ),
        'desc'       => esc_html__( 'Send an email notification to admin when a new wholesale application is received.' , 'whols' ),
    ),

    // registration_notification_recipients
    array(
        'id'       => 'registration_notification_recipients',
        'type'     => 'text',
        'title'    => esc_html__( 'Admin Email(s)', 'whols'),
        'desc'     => esc_html__( 'Enter email addresses that should receive new wholesaler notifications. Use commas to separate multiple emails. If left empty, the default admin email will be used.' , 'whols' ),
        'placeholder' => get_option('admin_email'),
        'dependency' => array(
            'enable_registration_notification_for_admin', '==', '1'
        )
    ),

    // email_subject
    array(
        'id'       => 'registration_notification_subject_for_admin',
        'type'     => 'text',
        'title'    => esc_html__( 'Admin Email Subject', 'whols'),
        'default'  => esc_html__('A New Wholesale Application Received', 'whols'),
        'dependency' => array(
            'enable_registration_notification_for_admin', '==', '1'
        )
    ),

    // message
    array(
        'id'       => 'registration_notification_message_for_admin',
        'type'     => 'wp_editor',
        'title'    => esc_html__( 'Successful Registration Message', 'whols'),
        'desc'     => esc_html__( 'Use these  {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'  ),
        'default'  => esc_html__(
            "A new wholesale application has been received.\n\n" . // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
            "Applicant Details:\n" .
            "Name: {name}\n" .
            "Email: {email}\n" .
            "Date: {date}\n" .
            "Time: {time}",
            'whols'
        ),
        'dependency' => array(
            'enable_registration_notification_for_admin', '==', '1'
        )
    ),

    // Heading
    array(
        'type'    => 'heading',
        'content' => esc_html__('Customer Registration Confirmation', 'whols'),
    ),

    // enable_registration_notification_for_user
    array(
        'id'         => 'enable_registration_notification_for_user',
        'type'       => 'checkbox',
        'title'      => esc_html__( 'Send Registration Confirmation to Customer', 'whols' ),
        'label'      => esc_html__( 'Enable', 'whols' ),
        'desc'       => esc_html__('Send an automatic confirmation email to customers when they submit a wholesale application.', 'whols'),
    ),

    // email_subject
    array(
        'id'       => 'registration_notification_subject_for_user',
        'type'     => 'text',
        'title'    => esc_html__( 'Customer Email Subject', 'whols'),
        'default'  => esc_html__('Your Wholesale Application Has Been Received', 'whols'),
        'desc'     => esc_html__('Subject line for the confirmation email sent to customers.', 'whols'),
        'dependency' => array(
            'enable_registration_notification_for_user', '==', '1'
        )
    ),

    // message
    array(
        'id'       => 'registration_notification_message_for_user',
        'type'     => 'wp_editor',
        'title'    => esc_html__( 'Customer Confirmation Message', 'whols'),
        'desc'     => esc_html__( 'Use these  {name}, {email}, {date}, {time} placeholder tags to get dynamic content.', 'whols'  ),
        'default'  => __(
            "Dear {name},\n\n" . // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
            "Thank you for applying for a wholesale account with us. We have received your application and it is currently under review.\n\n" .
            "Application Details:\n" .
            "Date Submitted: {date}\n" .
            "Time: {time}\n\n" .
            "We will review your application and contact you soon with our decision.\n\n" .
            "If you have any questions, please don't hesitate to contact us.\n\n" .
            "Best regards,\n" .
            get_bloginfo('name'),
            'whols'
        ),
        'dependency' => array(
            'enable_registration_notification_for_user', '==', '1'
        )
    ),

    // Heading
    array(
        'type'    => 'heading',
        'content' => esc_html__('Approval Notification', 'whols'),
    ),

    // enable_approved_notification
    array(
        'id'         => 'enable_approved_notification',
        'type'       => 'checkbox',
        'title'      => esc_html__( 'Send Approval Notification', 'whols' ),
        'label'      => esc_html__( 'Enable', 'whols' ),
        'desc'       => esc_html__('Automatically notify customers when their wholesale application is approved.', 'whols'),
        'class'      => 'whols_pro'
    ),

    // Heading
    array(
        'type'    => 'heading',
        'content' => esc_html__('Rejection Notification', 'whols'),
    ),

    // enable_rejection_notification
    array(
        'id'         => 'enable_rejection_notification',
        'type'       => 'checkbox',
        'title'      => esc_html__( 'Send Rejection Notification', 'whols' ),
        'label'      => esc_html__( 'Enable', 'whols' ),
        'desc'       => esc_html__('Automatically notify customers if their wholesale application is not approved.', 'whols'),
        'class'      => 'whols_pro'
    ),

    // Request a quote notification
    array(
        'type'    => 'heading',
        'content' => esc_html__('Quote Request Notification', 'whols'),
    ),

    // enable_raq_email_notification
    array(
        'id'         => 'enable_raq_email_notification',
        'type'       => 'checkbox',
        'title'      => esc_html__('Notify Admin of Quote Requests', 'whols'),
        'label'      => esc_html__('Yes', 'whols'),
        'desc'       => esc_html__('Send email notifications to administrators when customers submit quote requests.', 'whols'),
    ),

    // @todo admin_receipents option

    // request_a_quote_email_subject
    array(
        'id'       => 'raq_new_request_email_subject',
        'type'     => 'text',
        'title'    => esc_html__('Quote Request Email Subject', 'whols'),
        'desc'     => esc_html__('Use these {name}, {email}, {subject} placeholder tags to get dynamic content.', 'whols'),
        'default'  => __('[{site_title}] New Quote Request from {name}', 'whols'),
        'dependency' => array(
            'enable_raq_email_notification', '==', '1'
        )
    ),
    
    // request_a_quote_email_message
    array(
        'id'       => 'raq_new_request_email_body',
        'type'     => 'wp_editor',
        'title'    => esc_html__('Quote Request Notification Message', 'whols'),
        'desc'     => esc_html__('Use these {name}, {email}, {date}, {time}, {products}, {message} placeholder tags to get dynamic content.', 'whols'),
        'default'  => esc_html__(
            "New Quote Request Details:\n\n" . // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
            "Customer Name: {name}\n" .
            "Email: {email}\n" .
            "Subject: {subject}\n" .
            "Date: {date}\n" .
            "Time: {time}\n\n" .
            "Message:\n{message}\n\n" .
            "Requested Products:\n{products}",
            'whols'
        ),
        'dependency' => array(
            'enable_raq_email_notification', '==', '1'
        )
    ),
);