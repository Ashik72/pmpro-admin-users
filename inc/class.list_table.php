<?php

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * list_table
 */
class list_table
{

    private static $instance;
    public static $titan_data;


    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    function __construct()  {

        add_action('admin_menu', [$this, 'add_user_sub_menu']);
        add_action('admin_enqueue_scripts', [$this, 'load_custom_wp_frontend_style']);

        add_action('user_new_form', [$this, 'passfield']);
        add_action('after_signup_user', [$this, 'setpass'], 1000, 4);
        add_action('in_admin_header', function () {
           if ( isset( $_GET['update'] ) )
               $_GET['update'] = 'addnoconfirmation';
        });

    }

    public function load_custom_wp_frontend_style() {


        $current_screen = get_current_screen();

        if ($current_screen->base == "users_page_users-custom") {
            wp_enqueue_style('pmpro_mu_editor-bs-style-css', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');

            wp_register_script('pmpro_mu_editor-popper-script', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array('jquery'), '', true);

            wp_enqueue_script('pmpro_mu_editor-popper-script');

            wp_register_script('pmpro_mu_editor-bootstrapcdn-script', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array('jquery'), '', true);

            wp_enqueue_script('pmpro_mu_editor-bootstrapcdn-script');


            wp_enqueue_style('pmpro_mu_editor-style-css', pmpro_mu_upage_PLUGIN_URL . 'css/users.css');


        }

        wp_register_script('pmpro_mu_editor-users-custom', pmpro_mu_upage_PLUGIN_URL . 'js/users.js', array('jquery'), '', true);

        $adminurl = admin_url();

        $get_quantity = get_user_meta(get_current_user_id(), 'set_editor_quantity', true);


        wp_localize_script('pmpro_mu_editor-users-custom', 'users_custom', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'is_super_admin' => is_super_admin(),
            'admin_dir' => $adminurl,
            'quantity_av' => $get_quantity
        ));
        wp_enqueue_script('pmpro_mu_editor-users-custom');
        wp_enqueue_script('user-profile');


    }


    public function passfield() {
        ?>

        <table class="form-table setpass">



            <tr class="form-field form-required user-pass1-wrap">
                <th scope="row">
                    <label for="pass1">
                        <?php _e( 'Password' ); ?>
                        <span class="description hide-if-js"><?php _e( '(required)' ); ?></span>
                    </label>
                </th>
                <td>
                    <input class="hidden" value=" " /><!-- #24364 workaround -->
                    <button type="button" class="button wp-generate-pw hide-if-no-js"><?php _e( 'Show password' ); ?></button>
                    <div class="wp-pwd hide-if-js">
                        <?php $initial_password = wp_generate_password( 24 ); ?>
                        <span class="password-input-wrapper">
					<input type="password" name="pass1" id="pass1" class="regular-text" autocomplete="off" data-reveal="1" data-pw="<?php echo esc_attr( $initial_password ); ?>" aria-describedby="pass-strength-result" />
				</span>
                        <button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
                            <span class="dashicons dashicons-hidden"></span>
                            <span class="text"><?php _e( 'Hide' ); ?></span>
                        </button>
                        <button type="button" class="button wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
                            <span class="text"><?php _e( 'Cancel' ); ?></span>
                        </button>
                        <div style="display:none" id="pass-strength-result" aria-live="polite"></div>
                    </div>
                </td>
            </tr>
            <tr class="form-field form-required user-pass2-wrap hide-if-js">
                <th scope="row"><label for="pass2"><?php _e( 'Repeat Password' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
                <td>
                    <input name="pass2" type="password" id="pass2" autocomplete="off" />
                </td>
            </tr>
            <tr class="pw-weak">
                <th><?php _e( 'Confirm Password' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="pw_weak" class="pw-checkbox" />
                        <?php _e( 'Confirm use of weak password' ); ?>
                    </label>
                </td>
            </tr>

            <input type="hidden" name="noconfirmation" value="1">

        </table>


        <?php
    }

    public function setpass($user, $user_email, $key, $meta) {


        //file_put_contents(pmpro_mu_editor_PLUGIN_DIR."datan-".time().".txt", maybe_serialize([$_POST['pass1'], $user->ID]), FILE_APPEND);

        $user = get_user_by('login', $user);


        wp_set_password( $_POST['pass1'], $user->ID );


    }

    public function add_user_sub_menu() {

        $hook = add_submenu_page(
            'users.php',
            'Users',
            'Users',
            'manage_options',
            'users-custom',
            [$this, 'users_custom']
    );

        add_action("load-".$hook, function () {
            add_screen_option( 'per_page', [
                'option' => 'users_per_page'
            ] );
        });
    }

    public function users_custom() {
        ob_start();
        include_once pmpro_mu_upage_PLUGIN_DIR.DS."template".DS."users.php";
        $output = ob_get_clean();
        echo $output;

    }

}


add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );
function my_show_extra_profile_fields( $user ) { ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="phone">Phone Number</label></th>
            <td>
                <input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Please enter your phone number.</span>
            </td>
        </tr>
    </table>
<?php }

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    update_usermeta( $user_id, 'phone', $_POST['phone'] );
}


?>
