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

        // Send email notification for either quote request or conversation
        add_action( 'whols_after_raq_form_submit', array( $this, 'send_raq_email_notification' ) );
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
            $posted_data = [
                'name' => $user->first_name,
                'email' => $user->user_email,
                'date' => gmdate( 'Y-m-d', strtotime( $user->user_registered ) ),
                'time' => gmdate( 'H:i:s', strtotime( $user->user_registered ) )
            ];

            // subject
            $subject = stripslashes( html_entity_decode($subject, ENT_QUOTES, 'UTF-8' ) );
            $subject = $this->replace_placeholders($subject, $posted_data);
            
            // body
            $body = $this->replace_placeholders($body, $posted_data);
            $body = wpautop($body);

            // send the mail
            $to = get_option('admin_email');
            if( $custom_emails ){
                $to = explode(',', $custom_emails);
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
            $posted_data = [
                'name' => $user->first_name,
                'email' => $user->user_email,
                'date' => gmdate( 'Y-m-d', strtotime( $user->user_registered ) ),
                'time' => gmdate( 'H:i:s', strtotime( $user->user_registered ) )
            ];
            
            // subject
            $subject = $this->replace_placeholders($subject, $posted_data);
            
            // body
            $body = $this->replace_placeholders($body, $posted_data);
            $body = wpautop($body);

            // send the mail
            $to = $user->user_email;
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

            wp_mail( $to, $subject, $body, $headers );
        }
    }

    public function send_raq_email_notification($posted_data) {
        $defaults = array('location' => '');
        $posted_data = wp_parse_args($posted_data, $defaults);

        $email_type = $posted_data['location'] === 'cart' ? 'raq' : 'conversation';

        if ($this->should_send_raq_email( $posted_data )) {
            $email_data = $this->prepare_email_data($email_type, $posted_data);
            $this->send_email($email_data);
        }
    }

    public function should_send_raq_email( $posted_data ) {
        $return_value = false;

        $enable_raq_email_notification = whols_get_option('enable_raq_email_notification');
        $enable_conversation_email_notification = whols_get_option('enable_conversation_email_notification');

		// Location is cart, raq email notification is enabled
        if( $posted_data['location'] == 'cart' && $enable_raq_email_notification ){
            $return_value = true;
        }

		// Location is conversation, conversation email notification is enabled
        if( $posted_data['location'] == 'conversation' && $enable_conversation_email_notification ){
            $return_value = true;
        }

        return $return_value;
    }

    public function prepare_email_data($email_type, $posted_data) {
        $defaults = array(
            'raq_new_request_email_subject' => __('[{site_title}] New Quote Request from {name}', 'whols'),
            'raq_new_request_email_body' => __('
            Name: {name}
            Email: {email}
            Subject: {subject}
            Message: {message}
            Products: {products}', 'whols'),

            'conversation_start_email_subject' => __('[{site_title}] New Conversation from {name}', 'whols'),
            'conversation_start_email_body' => __('Name: {name}
            Email: {email}
            Subject: {subject}
            Message: {message}
            ', 'whols'),
        );

        if(whols_get_option('raq_new_request_email_subject')){
            $defaults['raq_new_request_email_subject'] = whols_get_option('raq_new_request_email_subject');
        }

        if(whols_get_option('raq_new_request_email_body')){
            $defaults['raq_new_request_email_body'] = whols_get_option('raq_new_request_email_body');
        }

        $subject_key = $email_type === 'raq' ? 'raq_new_request_email_subject' : 'conversation_start_email_subject';
        $body_key = $email_type === 'raq' ? 'raq_new_request_email_body' : 'conversation_start_email_body';

        $subject = $this->replace_placeholders($defaults[$subject_key], $posted_data);
        $body = $this->replace_placeholders($defaults[$body_key], $posted_data);



        if ($email_type === 'raq') {
            $body = $this->add_products_data($body, $posted_data);
            $body = $this->get_raq_prepared_body($body); // html, head, body
        } else {
            $body = wpautop($body);
        }

        return [
            'to' => get_option('admin_email'),
            'subject' => $subject,
            'body' => $body,
            'headers' => ['Content-Type: text/html; charset=UTF-8'],
        ];
    }

    public function get_raq_prepared_body( $body ) {
        ob_start();
        ?>
        <html>
            <head>
                <style>
                    table {
                        border-collapse: collapse;
                        margin: 20px 0;
                        font-size: 16px;
                        font-family: Arial, sans-serif;
                        text-align: left;
                    }
                    table, th, td {
                        border: 1px solid #dddddd;
                        padding: 8px;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>
                <?php echo wpautop($body); // @phpcs:ignore ?>
            </body>
        </html>
        <?php
        return ob_get_clean();
    }

    public function replace_placeholders($content, $posted_data) {
        $placeholders = [
            '{name}' => $posted_data['name'] ?? '',
            '{email}' => $posted_data['email'] ?? '',
            '{message}' => $posted_data['message'] ?? '', // For Raq
            '{date}' => date_i18n( get_option( 'date_format' ) ),
            '{time}' => date_i18n( get_option( 'time_format' ) ),
            '{subject}' => !empty($posted_data['subject']) ? $posted_data['subject'] : '', // For conversation
            '{site_title}' => get_bloginfo('name'),
            '{shop_url}' => get_permalink( wc_get_page_id( 'shop' ) )
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }

    public function add_products_data($raq_body_html, $posted_data) {
        $products_data = json_decode(wp_unslash($posted_data['products_data']), true);
        $products_data_html = '';

        if ($products_data) {
            ob_start();
            include WHOLS_PATH . '/includes/request-a-quote/html-product-data.php';
            $products_data_html = ob_get_clean();
        }

        return str_replace('{products}', $products_data_html, $raq_body_html);
    }

    public function send_email($email_data) {
		$to      = $email_data['to'];
		$subject = $email_data['subject'];
		$body    = $email_data['body'];

        wp_mail($to, $subject, $body, $email_data['headers']);
    }
}

// New instance
new Email_Notifications();