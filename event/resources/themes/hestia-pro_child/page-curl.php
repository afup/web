<?php
/**
 * Template Name: Curl Template
 *
 * @package Hestia Pro Child
 */

get_header();

/**
 * Don't display page header if header layout is set as classic blog.
 */
do_action( 'hestia_before_single_page_wrapper' ); ?>

<div class="<?php echo hestia_layout(); ?>">
    <?php
    $class_to_add = '';
    if ( class_exists( 'WooCommerce', false ) && ! is_cart() ) {
        $class_to_add = 'blog-post-wrapper';
    }
    ?>
    <div class="blog-post <?php echo esc_attr( $class_to_add ); ?>">
        <div class="container">
            <?php get_template_part( '_post', 'title' ); ?>
            <?php the_content(); ?>
            <?php
                $curl_url = get_post_meta(get_the_ID(), 'curl_url', true);
                $html_block = get_post_meta(get_the_ID(), 'html_block', true);
                if ($html_block !== '') {
                    echo $html_block;
                }
                if ($curl_url !== '') {
                    $curl_url = json_decode($curl_url, true);
                    $lang = 'fr';
                    if (isset($_GET['lang']) && isset($curl_url[$_GET['lang']])) {
                        $lang = $_GET['lang'];
                    }
                    remove_filter('the_content', 'wpautop');
                    if ($_SERVER['HTTP_HOST'] === '127.0.0.1:9225') {
                        $curl_url = array_map(function($item){ return str_replace('https://afup.org', $_ENV['AFUP_WEBSITE_URL'], $item);}, $curl_url);
                        //var_dump($curl_url);die;
                    }
                    $ch = curl_init();
                    curl_setopt(
                        $ch,
                        CURLOPT_URL,
                        $curl_url[$lang]
                    );

                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt(
                        $ch,
                        CURLOPT_REFERER,
                        'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
                    );
/*print_r(file_get_contents('php://input'));*/
                    if (isset($_POST) && get_post_meta(get_the_ID(), 'curl_handle_post', true) === 'true') {
                                                curl_setopt($ch, CURLOPT_POST, 1);
                        $post = $_POST;
                                                if (isset($_FILES)) {
                                                    foreach ($_FILES as $name => $file) {
                                if ($file['size'] !== 0) {
                                    $post[$name] = new \CURLFile($file['tmp_name'], $file['type'], $file['name']);
                                }
                        }
                                                }

                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                    }

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    if (defined('WP_DEBUG') && WP_DEBUG == true) {
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    }

                    $return = curl_exec($ch);

                    if (curl_errno($ch) > 0) {
                        echo 'Une erreur est survenue';
                    } else {
                        echo $return;
                    }

                }
            ?>
            <?php get_template_part( '_pagination', 'single' ); ?>
    </div>
</div>

<?php get_footer(); ?>
