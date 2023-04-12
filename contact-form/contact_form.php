<?php
/**
 * Plugin Name: contact_form
 */
register_activation_hook( __FILE__, 'createtable' );

function createtable() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $wp_contact_form = $wpdb->prefix . 'contact_form';
    $sql = "CREATE TABLE `$wp_contact_form` (
        id INT(11) NOT NULL AUTO_INCREMENT,
        sujet VARCHAR(255) NOT NULL,
        nom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        date_envoi TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    )";
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$wp_conatct_form'") != $wp_conatct_form) {
          require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
          dbDelta($sql);
        }
}

register_deactivation_hook(__FILE__, 'deletetable');

function deletetable() {
          global $wpdb;
          $wp_contact_form = $wpdb->prefix . 'contact_form';
          $sql = "DROP TABLE IF EXISTS $wp_contact_form";
          $wpdb->query($sql);
          delete_option("devnote_plugin_db_version");

}


// Affichage du formulaire de contact
add_shortcode( 'contact_form', 'contact_form_shortcode' );
function contact_form_shortcode() {
    ?> 

    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">

        <label for="sujet">Sujet :</label><br>
        <input type="text" name="sujet" required><br>

        <label for="nom">Nom :</label><br>
        <input type="text" name="nom" required><br>

        <label for="prenom">Prénom :</label><br>
        <input type="text" name="prenom" required><br>

        <label for="email">Email :</label><br>
        <input type="email" name="email" required><br>

        <label for="message">Message :</label><br>
        <textarea name="message" rows="5" required></textarea><br><br>

        <input type="submit" name="envoyer" value="envoyer" >
        
    </form>
    <?php
}
?>
<?php

          global $wpdb;
          if(isset($_POST['envoyer'])){
                    $sujet = $_POST['sujet'];
                    $nom = $_POST['nom'];
                    $prenom = $_POST['prenom'];
                    $email = $_POST['email'];
                    $message = $_POST['message'];

                    $wpdb->insert( 'wp_contact_form', array(

                                  'sujet' => $sujet,
                                  'nom' => $nom,
                                  'prenom' => $prenom,
                                  'email' => $email,
                                  'message' => $message,

                              ));         
          }
          if($wpdb->insert_id){
                    echo 'vouz avez ajouter avec succes';
          }else{
                    echo "vouz avez des problèmes à l'insertion";
          }

          add_action('admin_menu', 'custom_menu');

          function custom_menu() { 

            add_menu_page( 
                'contact us', 
                'contact', 
                'edit_posts', 
                'menu_slug', 
                'page_callback_function', 
                'dashicons-media-spreadsheet' 
          
               );
          }

          if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
      }

   

      // Plugin menu callback function
        function page_callback_function()
        {
            // Creating an instance
            $wp_contact_form = new contact_form();

            echo '<div class="wrap"><h2>contact_form</h2>';
            // Prepare table
            $wp_contact_form->prepare_items();
            // Display table
            $wp_contact_form->display();
            echo '</div>';
        }


        // Extending class
class contact_form extends WP_List_Table
{
    // Here we will add our code

    // Define table columns
    public function get_columns()
    {
        $columns = array(
                'id'            => '<input type="checkbox" />',
                'sujet'          => __('sujet', 'supporthost-cookie-consent'),
                'nom'         => __('nom', 'supporthost-cookie-consent'),
                'prenom'   => __('prenom', 'supporthost-cookie-consent'),
                'email'        => __('email', 'supporthost-cookie-consent'),
                'message'        => __('message', 'supporthost-cookie-consent'),
                'date_envoi'        => __('date_envoi', 'supporthost-cookie-consent')
        );
        return $columns;
    }
      // Get table data
   private function get_table_data() {
    global $wpdb;

    $wp_contact_form = $wpdb->prefix . 'wp_contact_form';

    return $wpdb->get_results(
        "SELECT * from `wp_contact_form`",
        ARRAY_A

    );
}

// Bind table with columns, data and all
public function prepare_items()
{
    //data
    $this->table_data = $this->get_table_data();

    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $primary  = 'name';
    $this->_column_headers = array($columns, $hidden, $sortable, $primary);
    
    $this->items = $this->table_data;
}

 // define $table_data property
  public function column_default($item, $column_name)
 {
       switch ($column_name) {
             case 'id':
             case 'sujet':
             case 'nom':
             case 'prenom':
             case 'email':
             case 'message':
             case 'date_envoi':
             default:
                 return $item[$column_name];
       }
 }
}


// $table = new contact_form;
// $table->prepare_items();
// $table->column_default();
// $table->display();

?>
