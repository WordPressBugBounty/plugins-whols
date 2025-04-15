<?php
namespace Whols\Vue_Settings;

class Settings_Schema {
    /**
     * Get the complete settings schema
     */
    public static function get_schema() {
        return array_merge(
            // General
            require __DIR__ . '/schema-parts/general.php',

            // Registration
            require __DIR__ . '/schema-parts/registration.php',

            // Fields Manager
            require __DIR__ . '/schema-parts/fields-manager.php',

            // Product Visibility
            require __DIR__ . '/schema-parts/product-visibility.php',

            // Wholesale only categories
            require __DIR__ . '/schema-parts/wholesale-only-categories.php',

            // Guest Access
            require __DIR__ . '/schema-parts/guest-access.php',

            // Dynamic Rules
            require __DIR__ . '/schema-parts/dynamic-rules.php',

            // Request a Quote
            require __DIR__ . '/schema-parts/request-a-quote.php',

            // Conversation
            require __DIR__ . '/schema-parts/conversation.php',

            // Wallet
            require __DIR__ . '/schema-parts/wallet.php',

            // Save Order List
            require __DIR__ . '/schema-parts/save-order-list.php',

            // Email Notifications
            require __DIR__ . '/schema-parts/email-notifications.php',

            // Custom Thank You Message
            require __DIR__ . '/schema-parts/custom-thank-you-message.php',

            // Others
            require __DIR__ . '/schema-parts/others.php',

            // Design
            require __DIR__ . '/schema-parts/design.php',

            // Bulk Order Form
            require __DIR__ . '/schema-parts/bulk-order-form.php',
        );
    }
}
