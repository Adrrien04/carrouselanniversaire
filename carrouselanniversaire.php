<?php
/*
Plugin Name: Carrousel d'anniversaire
Plugin URI: https://github.com/Adrrien04/carrouselanniversaire
Description: Vous trouverez ici le meilleur plugin de l'histoire de wordpress, le carrousel d'anniversaire !
Author: CHANDRAKUMAR Adrrien
Version: 3.2
Author URI: https://adrrienchandrakumar.vercel.app/
*/

function bc_add_custom_user_profile_fields($user) {
    ?>
    <h3><?php _e("Date d'anniversaire", "blank"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="birthday"><?php _e("Date d'anniversaire"); ?></label></th>
            <td>
                <input type="date" name="birthday" id="birthday" value="<?php echo esc_attr(get_the_author_meta('birthday', $user->ID)); ?>" class="regular-text" /><br />
                <span class="description"><?php _e("Veuillez entrer la date d'anniversaire."); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'bc_add_custom_user_profile_fields');
add_action('edit_user_profile', 'bc_add_custom_user_profile_fields');

function bc_save_custom_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    update_user_meta($user_id, 'birthday', $_POST['birthday']);
}

add_action('personal_options_update', 'bc_save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'bc_save_custom_user_profile_fields');

function bc_enqueue_custom_styles() {
    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', array('jquery'), null, true);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');

    wp_add_inline_style('bootstrap-css', '
        .carousel-item {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f7a000;
            margin: 10px;
            margin-right: 10px;
            border-radius: 10px;
            height: 280px
        }
        .carousel-item img {
            max-width: 125px;
            border-radius: 50%;
            margin-bottom: 5px;
        }
        .carousel-caption {
            position: static;
            padding: 5px;
        }
        .carousel-caption h4 {
            margin: 7px 0;
            color: black;
        }
        .carousel-caption p {
            color: black;
        }
        .carousel-caption i {
            color: black;
            margin-right: 5px;
        }
        .carousel-content {
            margin-top: 50px;
            display: flex;
            align-items: center;
        }
        .carousel-image {
            flex: 1;
            text-align: center;
        }
        .carousel-text {
            flex: 2;
        }
   .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-image: none;
    }
    .carousel-control-prev-icon::before,
    .carousel-control-next-icon::before {
        content: "\\f104";
        font-family: "Font Awesome 5 Free";
        font-weight: 1000;
        color: black;
    }
    .carousel-control-next-icon::before {
        content: "\\f105";
    }
    ');
}
add_action('wp_enqueue_scripts', 'bc_enqueue_custom_styles');

function bc_birthday_carousel() {
    $today = date('m / d');
    $args = array(
        'meta_key' => 'birthday',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_type' => 'DATE',
    );

    $users = get_users($args);
    $active_class = 'active';
    ob_start();
    ?>
    <div id="birthdayCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php
            foreach ($users as $user) {
                $birthday = date('d / m', strtotime(get_user_meta($user->ID, 'birthday', true)));
                $display_name = $user->display_name;
                $profile_picture = get_avatar_url($user->ID);
                $today_md = date('m-d', strtotime(get_user_meta($user->ID, 'birthday', true)));
                $today_date = date('m-d');
                if($today_md >= $today_date){
                ?>
                <div class="carousel-item <?php echo $active_class; ?>">
                    <div class="carousel-caption d-none d-md-block">
                        <div class="carousel-content">
                            <div class="carousel-image">
                                <img src="<?php echo $profile_picture; ?>" alt="<?php echo $display_name; ?>">
                            </div>
                            <div class="carousel-text">
                                <h4><i class="fa-solid fa-party-horn"></i><?php echo $display_name; ?></h4>
                                <p><?php echo $birthday; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $active_class = '';
                }
            }
            ?>
        </div>
        <a class="carousel-control-prev" href="#birthdayCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#birthdayCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('carrousel_anniversaire', 'bc_birthday_carousel');
?>