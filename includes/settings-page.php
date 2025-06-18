<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add settings page to WooCommerce menu
add_action('admin_menu', 'cod_verifier_admin_menu');

function cod_verifier_admin_menu() {
    add_submenu_page(
        'woocommerce',
        __('COD Verifier Settings', 'cod-verifier'),
        __('COD Verifier', 'cod-verifier'),
        'manage_woocommerce',
        'cod-verifier-settings',
        'cod_verifier_settings_page'
    );
}

function cod_verifier_settings_page() {
    // Handle form submission
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['cod_verifier_nonce'], 'cod_verifier_settings')) {
        update_option('cod_verifier_enable_otp', sanitize_text_field($_POST['enable_otp']));
        update_option('cod_verifier_enable_token', sanitize_text_field($_POST['enable_token']));
        update_option('cod_verifier_test_mode', sanitize_text_field($_POST['test_mode']));

        // Save Twilio credentials
        update_option('cod_verifier_twilio_account_sid', sanitize_text_field($_POST['twilio_account_sid']));
        update_option('cod_verifier_twilio_auth_token', sanitize_text_field($_POST['twilio_auth_token']));
        update_option('cod_verifier_twilio_phone_number', sanitize_text_field($_POST['twilio_phone_number']));

        // Fast2SMS API key is no longer used, but keeping the option update for potential future use or migration
        update_option('cod_verifier_fast2sms_api_key', sanitize_text_field($_POST['fast2sms_api_key']));


        update_option('cod_verifier_razorpay_key_id', sanitize_text_field($_POST['razorpay_key_id']));
        update_option('cod_verifier_razorpay_key_secret', sanitize_text_field($_POST['razorpay_key_secret']));

        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'cod-verifier') . '</p></div>';
    }

    // Get current settings
    $enable_otp = get_option('cod_verifier_enable_otp', '1');
    $enable_token = get_option('cod_verifier_enable_token', '1');
    $test_mode = get_option('cod_verifier_test_mode', '1');

    // Get Twilio credentials
    $twilio_account_sid = get_option('cod_verifier_twilio_account_sid', '');
    $twilio_auth_token = get_option('cod_verifier_twilio_auth_token', '');
    $twilio_phone_number = get_option('cod_verifier_twilio_phone_number', '');

    // Get Fast2SMS API key (keeping for now)
    $fast2sms_api_key = get_option('cod_verifier_fast2sms_api_key', '');


    $razorpay_key_id = get_option('cod_verifier_razorpay_key_id', '');
    $razorpay_key_secret = get_option('cod_verifier_razorpay_key_secret', '');
    ?>

    <div class="wrap">
        <h1><?php _e('COD Verifier Settings', 'cod-verifier'); ?></h1>

        <form method="post" action="">
            <?php wp_nonce_field('cod_verifier_settings', 'cod_verifier_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Mode', 'cod-verifier'); ?></th>
                    <td>
                        <label>
                            <input type="radio" name="test_mode" value="1" <?php checked($test_mode, '1'); ?>>
                            <?php _e('Test Mode (Recommended for initial setup)', 'cod-verifier'); ?>
                        </label><br>
                        <label>
                            <input type="radio" name="test_mode" value="0" <?php checked($test_mode, '0'); ?>>
                            <?php _e('Production Mode (Live SMS & Payment)', 'cod-verifier'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Use Test Mode for initial testing. OTP will be shown in popup, payments simulated.', 'cod-verifier'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Enable OTP Verification', 'cod-verifier'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_otp" value="1" <?php checked($enable_otp, '1'); ?>>
                            <?php _e('Require phone number verification via OTP', 'cod-verifier'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Enable Token Payment', 'cod-verifier'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_token" value="1" <?php checked($enable_token, '1'); ?>>
                            <?php _e('Require ₹1 token payment to confirm COD order', 'cod-verifier'); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <h2><?php _e('SMS Configuration (Twilio)', 'cod-verifier'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Twilio Account SID', 'cod-verifier'); ?></th>
                    <td>
                        <input type="text" name="twilio_account_sid" value="<?php echo esc_attr($twilio_account_sid); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Find your Account SID in your Twilio Console.', 'cod-verifier'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Twilio Auth Token', 'cod-verifier'); ?></th>
                    <td>
                        <input type="password" name="twilio_auth_token" value="<?php echo esc_attr($twilio_auth_token); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Find your Auth Token in your Twilio Console.', 'cod-verifier'); ?>
                        </p>
                    </td>
                </tr>
                 <tr>
                    <th scope="row"><?php _e('Twilio Phone Number', 'cod-verifier'); ?></th>
                    <td>
                        <input type="text" name="twilio_phone_number" value="<?php echo esc_attr($twilio_phone_number); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Your Twilio phone number (e.g., +15017122661).', 'cod-verifier'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <h2><?php _e('Payment Configuration (Razorpay)', 'cod-verifier'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Razorpay Key ID', 'cod-verifier'); ?></th>
                    <td>
                        <input type="text" name="razorpay_key_id" value="<?php echo esc_attr($razorpay_key_id); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Razorpay Key Secret', 'cod-verifier'); ?></th>
                    <td>
                        <input type="password" name="razorpay_key_secret" value="<?php echo esc_attr($razorpay_key_secret); ?>" class="regular-text">
                        <p class="description">
                            <?php _e('Get your API keys from', 'cod-verifier'); ?> <a href="https://razorpay.com" target="_blank">Razorpay Dashboard</a>
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

        <div class="card" style="margin-top: 30px; padding: 20px;">
            <h3><?php _e('🚀 Quick Start Guide', 'cod-verifier'); ?></h3>
            <ol>
                <li><strong><?php _e('Test Mode Setup:', 'cod-verifier'); ?></strong> <?php _e('Enable Test Mode above and save settings', 'cod-verifier'); ?></li>
                <li><strong><?php _e('Test the Plugin:', 'cod-verifier'); ?></strong> <?php _e('Go to checkout, select COD, test OTP and token payment', 'cod-verifier'); ?></li>
                <li><strong><?php _e('Production Setup (Twilio):', 'cod-verifier'); ?></strong> <?php _e('Add Twilio API credentials and configure Fast2SMS/Razorpay if needed, then switch to Production Mode', 'cod-verifier'); ?></li>
                <li><strong><?php _e('Go Live:', 'cod-verifier'); ?></strong> <?php _e('Your plugin is ready for real customers!', 'cod-verifier'); ?></li>
            </ol>

            <h4><?php _e('📋 Testing Steps', 'cod-verifier'); ?></h4>
            <ul>
                <li><?php _e('✅ Go to WooCommerce checkout page', 'cod-verifier'); ?></li>
                <li><?php _e('✅ Select "Cash on Delivery" payment method', 'cod-verifier'); ?></li>
                <li><?php _e('✅ Verification box should appear below', 'cod-verifier'); ?></li>
                <li><?php _e('✅ Test OTP: Enter phone number, click Send OTP (check alert for test OTP in Test Mode, receive SMS in Production Mode)', 'cod-verifier'); ?></li>
                <li><?php _e('✅ Test Token: Click Pay ₹1 Token (payment simulated in test mode)', 'cod-verifier'); ?></li>
                <li><?php _e('✅ Complete order - should work without errors', 'cod-verifier'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

?>