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

        wp_localize_script('pmpro_mu_editor-users-custom', 'users_custom', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'is_super_admin' => is_super_admin()
        ));
        wp_enqueue_script('pmpro_mu_editor-users-custom');


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
