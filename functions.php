<?php
/**
 * Base Install functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Base_Install
 */

if ( ! function_exists( 'baseinstall_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function baseinstall_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Base Install, use a find and replace
	 * to change 'baseinstall' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'baseinstall', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'menu-1' => esc_html__( 'Primary', 'baseinstall' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'baseinstall_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif;
add_action( 'after_setup_theme', 'baseinstall_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function baseinstall_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'baseinstall_content_width', 640 );
}
add_action( 'after_setup_theme', 'baseinstall_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function baseinstall_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'baseinstall' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'baseinstall' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'baseinstall_widgets_init' );

/**
 * Enqueue scripts and styles.
 * The first stylesheet is the minified version (maybe rename to main or styles later)
 * The second stylesheet is the one WordPress needs to show theme name, version, description, and author info
 */
function baseinstall_scripts() {

	// minified stylesheet
	// wp_enqueue_style( 'baseinstall-min-style', get_template_directory_uri() . '/style.min.css', array(), time() ); 

	// theme styles
	wp_enqueue_style( 'baseinstall-style', get_stylesheet_uri() );

	// theme scripts
	wp_enqueue_script( 'baseinstall-navigation', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), '20151215', true ); 

	// responsive media queries for IE
	wp_enqueue_script( 'baseinstall-respond', get_template_directory_uri().'/assets/vendor/js/respond.min.js' );
	wp_script_add_data( 'baseinstall-respond', 'conditional', 'lt IE 9' );

	// html5shiv for IE
	wp_enqueue_script( 'baseinstall-html5shiv',get_template_directory_uri().'/assets/vendor/js/html5shiv.min.js');
	wp_script_add_data( 'baseinstall-html5shiv', 'conditional', 'lt IE 9' );

	// comment script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'baseinstall_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';





/**
 * CUSTOM FUNCTIONS START BELOW
 * below are some optional additions to theme functionality
 * feel free to edit or delete to suit your needs
 */





/**
 * RESPONSIVE VIDEO EMBED
 * Filter for adding wrappers around embedded objects
 */
function baseinstall_responsive_embeds( $content ) {
	$content = preg_replace( "/<object/Si", '<div class="video-container"><object', $content );
	$content = preg_replace( "/<\/object>/Si", '</object></div>', $content );
	
	/**
	 * Added iframe filtering for embedded YouTube/Vimeo videos.
	 */
	$content = preg_replace( "/<iframe.+?src=\"(.+?)\"/Si", '<div class="video-container"><iframe src="\1" frameborder="0" allowfullscreen>', $content );
	$content = preg_replace( "/<\/iframe>/Si", '</iframe></div>', $content );
	return $content;
}
add_filter( 'the_content', 'baseinstall_responsive_embeds' );



/**
 * LINK SCROLL
 * remove link scroll from "read more" excerpt links
 */
function baseinstall_remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'baseinstall_remove_more_link_scroll' );



/**
 * EXCERPT LENGTH
 * limit blog post excerpt to 30 words
 */
// function baseinstall_custom_excerpt_length( $length ) {
// 	return 30;
// }
// add_filter( 'excerpt_length', 'baseinstall_custom_excerpt_length', 999 );





/**
 * ADD FEATURED IMAGE TO HERO
 * Enable featured image to be background of hero block
 * Theme has default hero image in hero.scss
 * If featured image has been set, this function gets ID/URL of image and overrides default CSS 
 */
function baseinstall_custom_header_image(){
	if (has_post_thumbnail()) { //if a thumbnail has been set
		$imgID = get_post_thumbnail_id($post->ID); //get id of featured image
		$featuredImage = wp_get_attachment_image_src($imgID, 'full' ); //get url of featured image (returns array)
		$imgURL = $featuredImage[0]; //get url of image from array
	    ?>
		<style type="text/css">
			.hero {
				background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url(<?php echo $imgURL ?>);
			}
		</style>
	<?php
	}
}
add_action( 'wp_head', 'baseinstall_custom_header_image' );



/**
 * CUSTOM LOGIN SCREEN
 * overrides default WP logo, background image/color, and form styles
 */
function baseinstall_login_logo() { ?>
    <style type="text/css">
		.login h1 a {
		    background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo.png);
		    padding-bottom: 0;
			margin: 0 auto 30px;
			width: 260px;
			height: 244px;
			-webkit-background-size: 260px 244px;
			background-size: 260px 244px;
		}
		.login  {
			background-color: #e1e1e1;
			background: -webkit-linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/bg-city.jpg);
			background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/bg-city.jpg);
			background-repeat: no-repeat !important;
			background-position: center !important;
			background-size: cover !important;
		}
		.login form {
			padding: 10px 20px 15px;
			background: #fff;
			box-shadow: 0 0 12px 0px rgba(0, 0, 0, 0.2);
			border-radius: 4px;
		}
		.mobile #login {
			padding: 20px 0 0;
			width: 280px;
		}
		#login { 
			width: 280px; 
		}
		.wp-core-ui .button-primary {
			background: #0066ae;
			border-color: #0066ae;
			text-shadow: none;
		}
		.wp-core-ui .button-primary:focus, 
		.wp-core-ui .button-primary:hover {
			background: #198f8b;
			border-color: #198f8b;
		}
		.login #backtoblog a, 
		.login #nav a {
			text-decoration: none;
			color: #0066ae;
		}
		.login #backtoblog a:hover, 
		.login #nav a:hover {
			color: #198f8b;
		}

		@media screen and (min-width: 550px) {
			.login form {
				padding: 20px 24px 30px;
			}
			#login {
				width: 320px;
			}
			.login {
			    background-position: center bottom;
				background-size: contain;
			}
		}
    </style>
<?php }
add_action( 'login_head', 'baseinstall_login_logo' );

/**
 * CUSTOM LOGIN SCREEN LOGO LINK
 * Redirect custom login logo link to homepage
 */
function baseinstall_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'baseinstall_login_logo_url' );

/**
 * CUSTOM LOGIN SCREEN LOGO TITLE
 * Update custom login logo page title
 */
function baseinstall_login_logo_url_title( $title ) {
	return esc_attr( get_bloginfo( 'title' ) );
}
add_filter( 'login_headertitle', 'baseinstall_login_logo_url_title' );



/**
 * FAVICONS
 * Add custom favicons to admin dashboard and front end of site
 */
function baseinstall_admin_favicon() {
  	$admin_favicon_url = get_stylesheet_directory_uri() . '/assets/img/icons/admin-favicon.ico';
	echo '<link rel="shortcut icon" href="' . $admin_favicon_url . '" />';
}
add_action('login_head', 'baseinstall_admin_favicon');
add_action('admin_head', 'baseinstall_admin_favicon');

function baseinstall_main_favicon() {
  	$main_favicon_url = get_stylesheet_directory_uri() . '/assets/img/icons/favicon.ico';
	echo '<link rel="shortcut icon" href="' . $main_favicon_url . '" />';
}
add_action('wp_head', 'baseinstall_main_favicon');





/**
 * HIDE ADMIN BAR
 * Removes admin bar from front end - optional, but nice for development
 */
// function hide_admin_front_end(){
// 	if (is_blog_admin()) {
// 		return true;
// 	}
// 	return false;
// }
// add_filter( 'show_admin_bar', 'hide_admin_front_end' );












// Modify WooCommerce orderby dropdown
// This overrides the default WooCommerce sorting dropdown text
// Options: renaming popularity, rating, newness, and price
// 
// function mj_translation_sort_change( $translated_text, $text, $domain ) {
// 	if ( is_woocommerce() ) {
// 		switch ( $translated_text ) {
			
// 			case 'Sort by popularity' : 
// 				$translated_text = __( 'Ordenar por popularidad', 'mj_text_domain' ); 
// 				break;
			
// 			case 'Sort by average rating' : 
// 				$translated_text = __( 'Ordenar por calificación', 'mj_text_domain' ); 
// 				break;
			
// 			case 'Sort by newness' : 
// 				$translated_text = __( 'Ordenar por más reciente', 'mj_text_domain' ); 
// 				break;
			
// 			case 'Sort by price: low to high' : 
// 				$translated_text = __( 'Ordenar por precio: menor a mayor', 'mj_text_domain' ); 
// 				break;
			
// 			case 'Sort by price: high to low' : 
// 				$translated_text = __( 'Ordenar por precio: mayor a menor', 'mj_text_domain' ); 
// 				break;
// 		}
// 	}
// 	return $translated_text;
// }
// add_filter( 'gettext', 'mj_translation_sort_change', 20, 3 );












/**
 * CUSTOM FONTS
 * Enable custom Google font and Font Awesome
 */
// function prefix_fonts() {
// 	// Montserrat
// 	wp_enqueue_style( 'prefix_google_fonts', '//fonts.googleapis.com/css?family=Montserrat:100,200,300,400', array(), null, 'screen' );

// 	// Font Awesome
// 	wp_enqueue_style( 'prefix_font_awesome', '//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.0.3' );
// }
// add_action( 'wp_enqueue_scripts', 'prefix_fonts' );



/**
 * THEME WRAPPER
 * Don't Repeat Yourself - header, footer, and sidebar are called in base.php for staying DRY
 * You can have multiple wrappers (base-single.php, base-page.php, etc.) and they can be overwritten like any other template
 * Based on Scribu and Sage
 */
function baseinstall_template_path() {
	return baseinstall_wrapper::$main_template;
}
function baseinstall_template_base() {
	return baseinstall_wrapper::$base;
}
class baseinstall_wrapper {
	/**
	 * Stores the full path to the main template file
	 */
	static $main_template;
	/**
	 * Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
	 */
	static $base;
	static function wrap( $template ) {
		self::$main_template = $template;
		self::$base = substr( basename( self::$main_template ), 0, -4 );
		if ( 'index' == self::$base )
			self::$base = false;
		$templates = array( 'base.php' );
		if ( self::$base )
			array_unshift( $templates, sprintf( 'base-%s.php', self::$base ) );
		return locate_template( $templates );
	}
}
add_filter( 'template_include', array( 'baseinstall_wrapper', 'wrap' ), 99 );


