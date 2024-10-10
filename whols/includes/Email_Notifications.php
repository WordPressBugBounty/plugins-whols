<?php
namespace Whols;

/**
 * Email Notifications
 */
class Email_Notifications {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'whols_user_registration_success', array( $this, 'user_registration_email_for_admin' ) );
        add_action( 'whols_user_registration_success', array( $this, 'user_registration_email_for_user' ) );
    }

    /**
     * Notification for admin
     */
    public function user_registration_email_for_admin( $user_id ){
        $enable_email_notification = whols_get_option('enable_registration_notification_for_admin');
        $subject                   = whols_get_option('registration_notification_subject_for_admin');
        $body                      = whols_get_option('registration_notification_message_for_admin');
        $user                      = get_user_by( 'ID', $user_id );
        $custom_emails             = whols_get_option('registration_notification_recipients'); // Comma separated emails

        if( $enable_email_notification && $user ){
            // subject
            $subject = stripslashes( html_entity_decode($subject, ENT_QUOTES, 'UTF-8' ) );
            
            // body
            $body = str_replace('{name}', $user->first_name, $body);
            $body = str_replace('{email}', $user->user_email, $body);
            $body = str_replace('{date}', gmdate( 'Y-m-d', strtotime( $user->user_registered ) ), $body);
            $body = str_replace('{time}', gmdate( 'H:i:s', strtotime( $user->user_registered ) ), $body);
            $body = wpautop($body);

            // send the mail
            $to = get_option('admin_email');
            if( $custom_emails ){
                $to = implode(',', $custom_emails);
            }
            
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

            wp_mail( $to, $subject, $body, $headers );
        }
    }

    /**
     * Notification for user
     */
    public function user_registration_email_for_user( $user_id ){
        $enable_email_notification = whols_get_option('enable_registration_notification_for_user');
        $subject                   = whols_get_option('registration_notification_subject_for_user');
        $body                      = whols_get_option('registration_notification_message_for_user');
        $user                      = get_user_by( 'ID', $user_id );

        if( $enable_email_notification && $user ){
            // body
            $body = str_replace('{name}', $user->first_name, $body);
            $body = str_replace('{email}', $user->user_email, $body);
            $body = str_replace('{date}', gmdate( 'Y-m-d', strtotime( $user->user_registered ) ), $body);
            $body = str_replace('{time}', gmdate( 'H:i:s', strtotime( $user->user_registered ) ), $body);
            $body = wpautop($body);

            // send the mail
            $to = $user->user_email;
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

            wp_mail( $to, $subject, $body, $headers );
        }
    }
}

// New instance
new Email_Notifications();