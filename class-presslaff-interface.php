<?php
/**
 * Plugin Name.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Your Name or Company Name
 */

/**
 * Plugin class.
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package Plugin_Name
 * @author  Your Name <email@example.com>
 */
class Presslaff_Interface extends Presslaff {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @const   string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'presslaff-interface';
	
	protected $loginSlug  = "";
	
	protected $contestSlugPrivate = "";
	
	protected $contestSlugPublic = "";
	
	protected $stationid = "";
	
	protected $dummyid = "";

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	private $presslaffObj; 

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	public function __construct() 
	{
		if( file_exists( plugin_dir_path( __FILE__) . "assets/presslaff.ini" ) )
		{
			$ini = file_get_contents(  plugin_dir_path( __FILE__) . "assets/presslaff.ini" );
			
			$lines = explode("\n", $ini);
			
			$regUserName 		= "";
			$regPassWord 		= "";
			$contestUserName 	= "";
			$contestPassword 	= "";
			$regUrl 			= "";
			$contestUrl			= "";
			$stationid			= "";
			
			foreach( $lines as $line )
			{
				list($key, $value) = explode(":", $line );
				
				switch( $key )
				{
					case "regUserName":
						$regUserName = urldecode( $value );
					break;
					case "regPassWord":
						$regPassWord = urldecode( $value );
					break;
					case "contestUserName":
						$contestUserName = urldecode( $value );
					break;
					case "contestPassWord":
						$contestPassword = urldecode( $value );
					break;
					case "regUrl":
						$regUrl = urldecode( $value );
					break;
					case "contestUrl":
						$contestUrl = urldecode( $value );
					break;
					case "stationid":
						$this->stationid = $value;
					break;
					case "loginSlug":
						$this->loginSlug = $value;
					break;
					case "contestSlug":
						$this->contestSlugPrivate = $value;
					break;
					case "publicSlug":
						$this->contestSlugPublic = $value;
					break;
					case "dummyid":
						$this->dummyid = $value;
					break;
				}	
				
			}
			//instantiate Presslaff Object
			$this->presslaffObj = new Presslaff($regUserName, $regPassWord, $contestUserName, $contestPassword, $regUrl, $contestUrl, $this->stationid );
		}
			
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		
		add_action( 'admin_init', array($this, 'presslaff_settings_init') );
		// Add the options page and menu item.
		// add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page. TODO: Rename "plugin-name.php" to the name your plugin
		// $plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'plugin-name.php' );
		// add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_shortcode( 'presslaff_login', array( $this, 'presslaff_login' ) );
		
		add_shortcode( 'presslaff_edit_account', array( $this, 'presslaff_edit' ) );
		
		add_shortcode( 'presslaff_signup', array( $this, 'presslaff_signup' ) );
		
		add_shortcode( 'presslaff_contests', array( $this, 'presslaff_contests' ) );

		//AJX Call for retrieving All Contests - Private 
		add_action( 'wp_ajax_nopriv_getContests', array( $this, 'getContests' ) );
		
		add_action( 'wp_ajax_getContests', array( $this, 'getContests' ) );
		
		//AJX Call for retrieving All Contests - Public
		add_action( 'wp_ajax_nopriv_getContestsPublic', array( $this, 'getContestsPublic' ) );
		
		add_action( 'wp_ajax_getContestsPublic', array( $this, 'getContestsPublic' ) );
		
		//AJAX Call for Single Contest
		add_action( 'wp_ajax_nopriv_getContest', array( $this, 'getContest' ) );
		
		add_action( 'wp_ajax_getContest', array( $this, 'getContest' ) );
		
		//AJAX Call that will increment the open count for rich media ad
		add_action( 'wp_ajax_nopriv_presslafflogin', array( $this, 'processLogin') );
		
		add_action( 'wp_ajax_presslafflogin', array( $this, 'processLogin') );
		
		add_action( 'wp_ajax_nopriv_entercontest', array( $this, 'enterContest' ) );
		
		add_action( 'wp_ajax_entercontest', array( $this, 'enterContest' ) );
		
		add_action( 'wp_ajax_nopriv_entercontest_public', array( $this, 'enterContestPublic' ) );
		
		add_action( 'wp_ajax_entercontest_public', array( $this, 'enterContestPublic' ) );
		
		add_action( 'wp_ajax_createaccount', array( $this, 'createAccount' ) );
		
		add_action( 'wp_ajax_nopriv_createaccount', array( $this, 'createAccount' ) );
		
		add_action( 'wp_ajax_accountinfo', array( $this, 'getAccountInfo' ) );
		
		add_action( 'wp_ajax_nopriv_accountinfo', array( $this, 'getAccountInfo' ) );
		
		add_action( 'wp_ajax_updateaccount', array( $this, 'updateAccount' ) );
		
		add_action( 'wp_ajax_nopriv_updateaccount', array( $this, 'updateAccount' ) );
		
		add_action( 'wp_ajax_config', array( $this, 'config' ) );
		
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() 
	{
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( $this->plugin_slug . '-colorbox-style', plugins_url( 'css/colorbox.css', __FILE__), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() 
	{
		wp_enqueue_script( $this->plugin_slug . '-public-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
		
		$pageslug = basename( get_permalink( ) );
		
		if( file_exists( plugin_dir_path(__FILE__) . "assets/presslaff.ini" ) ) 
		{
			echo "<script type='text/javascript'>\n
				var loginslug = '" . $this->loginSlug . "';
				var contestslug = '" . $this->contestSlugPrivate . "';
				var contestslug_public	= '" . $this->contestSlugPublic . "';
				var stationid	= " . $this->stationid . ";
				</script>";

				
			if( $pageslug == $this->loginSlug )
			{
				wp_enqueue_script( $this->plugin_slug . '-login-script', plugins_url( 'js/login.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-public-script' ), $this->version );
			}
			elseif( $pageslug == $this->contestSlugPrivate )
			{
				wp_enqueue_script( $this->plugin_slug . '-contest-script', plugins_url( 'js/contests.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-public-script'), $this->version );
				wp_enqueue_script( $this->plugin_slug . 'jqueryforms', plugins_url( 'js/jquery.form.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( $this->plugin_slug . 'colorbox', plugins_url( 'js/jquery.colorbox-min.js',__FILE__ ), array('jquery' ) );
			}
			elseif( $pageslug == $this->contestSlugPublic )
			{
				
				wp_enqueue_script( $this->plugin_slug . '-public-contest-script', plugins_url( 'js/contests_public.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-public-script' ), $this->version );
				wp_enqueue_script( $this->plugin_slug . 'jqueryforms', plugins_url( 'js/jquery.form.js', __FILE__ ), array( 'jquery' ) );
			}	
		}
	}
	
	public function add_plugin_admin_menu() 
	{


		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Presslaff Interface', $this->plugin_slug ),
			__( 'Presslaff Interface', $this->plugin_slug ),
			'edit_posts',
			$this->plugin_slug,
			array( $this, 'display_admin_page' )
		);
	}

	public function presslaff_settings_init( )
	{
		register_setting('presslaff_options', 'presslaff_option');
		
		add_settings_section('presslaff_settings', NULL, NULL, $this->plugin_slug);
		
		$usernameReg 		= "";
		$passwordReg 		= "";
		$usernameContest 	= "";
		$passwordContest 	= "";
		$urlReg				= "https://www.1.dat-e-baseonline.com/admin/admin_net/corp_api/pircorpwebservice.asmx";
		$urlContest			= "https://www.1.dat-e-baseonline.com/contestingapi/service.asmx";
		$stationID			= "";
		$loginSlug			= "";
		$contestSlug		= "";
		$contestSlugPublic	= "";
		
		if( file_exists( plugin_dir_path( __FILE__ ) . "assets/presslaff.ini" ) )
		{
			$fh = fopen(plugin_dir_path( __FILE__ ) . "assets/presslaff.ini", "r");
			
			$content = fread($fh, filesize(plugin_dir_path( __FILE__ ) . "assets/presslaff.ini" ) );
			
			$lines = explode("\n", $content);
			
			foreach( $lines as $line )
			{
				list( $key, $value ) = explode(":", $line);
				
				$value = urldecode( $value );
				
				switch( $key )
				{
					case "regUserName":
						$usernameReg = $value;
					break;
					case "contestUserName":
						$passwordReg = $value;
					break;
					case "regPassWord":
						$usernameContest = $value;
					break;
					case "contestPassWord":
						$passwordContest = $value;
					break;
					case "regUrl":
						$urlReg = $value;
					break;
					case "contestUrl":
						$urlContest = $value;
					break;
					case "stationid":
						$stationID = $value;
					break;
					case "loginSlug":
						$loginSlug = $value;
					break;
					case "contestSlug":
						$contestSlug = $value;
					break;
					case "publicSlug":
						$contestSlugPublic = $value;
					break;
				
				}
			}
		}
		
		add_settings_field(
			'username-reg', 
			"Registration Username", 
			array( $this, "presslaff_reg_username_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $usernameReg 
			) 
		);
		
		add_settings_field(
			'password-reg', 
			"Registration Password", 
			array( $this, "presslaff_reg_password_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $passwordReg 
			) 
		);
		
		add_settings_field(
			'username-contest', 
			"Contest Username", 
			array( $this, "presslaff_contest_username_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $usernameContest 
			) 
		);
		
		add_settings_field(
			'password-contest', 
			"Contest Password", 
			array( $this, "presslaff_contest_password_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $passwordContest
			) 
		);
		
		add_settings_field(
			'url-reg', 
			"Registration URL", 
			array( $this, "presslaff_reg_url_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $urlReg 
			) 
		);
		
		add_settings_field(
			'url-contest', 
			"Contest URL", 
			array( $this, "presslaff_contest_url_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $urlContest
			) 
		);
		
		add_settings_field(
			'station-id', 
			"Station ID", 
			array( $this, "presslaff_station_id_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $stationID
			) 
		);
		
		add_settings_field(
			'login-slug', 
			"Login Slug", 
			array( $this, "presslaff_login_slug_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $loginSlug 
			) 
		);
		
		add_settings_field(
			'contest-slug', 
			"Contest Slug", 
			array( $this, "presslaff_contest_slug_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $contestSlug 
			) 
		);
		
		add_settings_field(
			'contest-public-slug', 
			"Contest Public Slug", 
			array( $this, "presslaff_contest_public_slug_callback" ), 
			$this->plugin_slug, 
			'presslaff_settings', 
			array( 
				"value" => $contestSlugPublic 
			) 
		);

	}
	
	public function presslaff_reg_username_callback( $args )
	{
		echo "<input type='text' name='regUserName' value='" . $args["value"] . "' />";
	}
	
	public function presslaff_reg_password_callback( $args )
	{
		echo "<input type='text' name='regPassWord' value='" . $args["value"] . "' />";
	}
	
	public function presslaff_contest_username_callback( $args )
	{
		echo "<input type='text' name='contestUserName' value='" . $args["value"] . "' />";
	}
	
	public function presslaff_contest_password_callback( $args )
	{
		echo "<input type='text' name='contestPassWord' value='" . $args["value"] . "' />";
	}
	
	public function presslaff_reg_url_callback( $args )
	{
		echo "<input type='text' name='regUrl' value='" . $args["value"] . "' size='70'/>";
	}
	
	public function presslaff_contest_url_callback( $args )
	{
		echo "<input type='text' name='contestUrl' value='" . $args["value"] . "' size='70' />";
	}
	
	public function presslaff_station_id_callback( $args )
	{
		echo "<input type='text' name='stationID' value='" . $args["value"] . "' size='5'/>";
	}
	
	public function presslaff_login_slug_callback( $args )
	{
		global $wpdb;
		
		$posts = $wpdb->get_results("SELECT post_title, post_name FROM wp_posts WHERE post_type = 'page'");
		
		if( $posts )
		{
			echo "<select name='loginSlug'>";
			echo "	<option value=''>Select Login Slug</option>";
			foreach( $posts as $post )
			{
				if( $args["value"] == $post->post_name )
				{
					echo "<option value='" . $post->post_name . "' selected='selected'>" . $post->post_title . "</option>";	
				}
				else
				{
					echo "<option value='" . $post->post_name . "' >" . $post->post_title . "</option>";	
				}
			}
			echo "</select>";
		}
	}
	
	public function presslaff_contest_slug_callback( $args )
	{
		global $wpdb;
		
		$posts = $wpdb->get_results("SELECT post_title, post_name FROM wp_posts WHERE post_type = 'page'");
		
		if( $posts )
		{
			echo "<select name='contestSlug'>";
			echo "	<option value=''>Select Contest Slug</option>";
			foreach( $posts as $post )
			{
				if( $args["value"] == $post->post_name )
				{
					echo "<option value='" . $post->post_name . "' selected='selected'>" . $post->post_title . "</option>";	
				}
				else
				{
					echo "<option value='" . $post->post_name . "' >" . $post->post_title . "</option>";	
				}
			}
			echo "</select>";
		}
	}
	
	public function presslaff_contest_public_slug_callback( $args )
	{
		global $wpdb;
		
		$posts = $wpdb->get_results("SELECT post_title, post_name FROM wp_posts WHERE post_type = 'page'");
		
		if( $posts )
		{
			echo "<select name='contestPublicSlug'>";
			echo "	<option value=''>Select Public Contest Slug</option>";
			foreach( $posts as $post )
			{
				if( $args["value"] == $post->post_name )
				{
					echo "<option value='" . $post->post_name . "' selected='selected'>" . $post->post_title . "</option>";	
				}
				else
				{
					echo "<option value='" . $post->post_name . "' >" . $post->post_title . "</option>";	
				}
			}
			echo "</select>";
		}
	}	
	
	
	public function display_admin_page( )
	{
		include_once( 'views/admin.php' );
	}

	

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'plugins.php?page=plugin-name' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	public function presslaff_login( )
	{
		if( file_exists( plugin_dir_path(__FILE__ ) . "assets/presslaff.ini" ) )
		{
			//ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE);
			
			$output = "";
			
			include("views/login.php");
			
			//$output = ob_get_clean( );
			
			return $output;
			
		}
		else
		{
			include("views/noconfig.php");
		}
		
	}
	
	public function presslaff_contests( $atts, $content )
	{
	

		
		$a = shortcode_atts(array(
			"type" => "private"
		), $atts);
	
		$pageslug = basename( get_permalink( ) );
		
		if( $a["type"] == "private" )
		{

			if( $pageslug == $this->contestSlugPrivate )
			{

				//ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE);

				include("views/private.php");
				
				//$content = ob_get_clean( );
				
				return $content;
				
			}
			else
			{
				echo "Your configuration is not set up for Private Contests on this page.";
			}
		}
		else if( $a["type"] == "public" )
		{
			if( $pageslug == $this->contestSlugPublic )
			{
				include("views/public.php");
				
				return $content;
			}
			else
			{
				echo "Your configuration is not set up for Private Contests on this page.";
			}
		}
		else if( $a["type"] == "home" )
		{
			
			$contests = $this->presslaffObj->getAllContests( );
			
			$success = array_shift( $contests );
			
			$amount  = 0;
			
			//$list = "";
			
			if( (boolean)$success )
			{
				
				foreach( $contests as $contest )
				{

					if( $contest["Hidden"] == "False")
					{

						$logo = $contest["LogoURL"];
						$name = $contest["Name"];
						$id   = $contest["ContestID"];
						
						$list .= "<li class='presslaff-item'><a href='" . $this->contestSlugPublic . "/?contestid=" . $id . "'><img src='" . $logo . "' /></a><br />" . $name . "</li>";
						$amount++;
						
					} 
				}	
			}
			
			$listlength = 205 * $amount;
			
			
			
			$list = "<ul class='presslaff-list' style='width: " . $listlength . "px;'>" . $list . "</ul>";
			
			if( $amount < 3 )
			{
				$out = "<div class='p-contests p-middle'>";	
			}
			else
			{
				$out = "<div class='p-contests'>";
			}
			
			$out .= $list;
			
			$out .= "</div>";
			
			if( $amount > 3 )
			{
				$out .= "<div class='p-nav'><table><tr><td  id='p-prev-contest'>Prev</td><td  id='p-next-contest'>Next</td></tr></table></div>";
			}
			
			return $out;
		}
		
	}
	
	public function processLogin( )
	{
		
		$sanitized_email = sanitize_email( urldecode( $_POST["username"] ) );
		
		if( is_email( $sanitized_email ) ) 
		{
			$loginResponse = $this->presslaffObj->getAccountIDbyEmail( $sanitized_email );
		
			echo json_encode( $loginResponse );
		
			exit;	
		}
		else
		{
			echo json_encode(array("status"=>300) );
			
			exit;
		}
	}
	
	public function getContests( )
	{
		$pID = $_GET['pID'];
	
		$contests = $this->presslaffObj->getContestsforSubscriber( $pID );
		
		header("Content-type: application/json" );
		echo json_encode( $contests );
		exit;
	}
	
	public function getContest( )
	{
		$pID = $this->dummyid;
		
		$firstname 	= "";
		$lastname  	= "";
		$email 		= "";
		
		if( isset( $_GET['pID'] ) && ! empty( $_GET['pID'] ) )
		{
			$pID = $_GET['pID'];	
			
			$accountinfo = $this->presslaffObj->getAccountbyID( $pID );
		
			$questions = $this->presslaffObj->getRegistrationQuestions( );
			
			foreach( $questions["questions"] as $question )
			{
				$value = "";
					
				if( isset( $accountinfo["Data_" . $question["QuestionNumber"]] ) )
				{
					$value = $accountinfo["Data_" . $question["QuestionNumber"]];
					
					if( preg_match("/^first name/", strtolower( $question["QuestionText"] ) ) )
					{
						$firstname = $value;
					}
					
					if( preg_match("/^last name/", strtolower( $question["QuestionText"] ) ) )
					{
						$lastname = $value;
					}
					
					if( preg_match("/^email/", strtolower( $question["QuestionText"] ) ) )
					{
						$email = $value;
					}
					
				} 
			}
		}
		
		$cID = $_GET['cID'];
		
		$contest = $this->presslaffObj->getContest( $cID, $pID );
		
		$contest["firstname"] 	= $firstname;
		
		$contest["lastname"] 	= $lastname;
		
		$contest["email"]		= $email;
		
		header("Content-type:application/json");
		echo json_encode( $contest );
		exit;
		
	}
	
	public function getContestsPublic( )
	{
		$contests = $this->presslaffObj->getAllContests( );
		
		header("Content-type: application/json" );
		echo json_encode( $contests );
		exit;
	}
	
	public function getContestPublic( )
	{
		
	}
	
	
	public function enterContest( )
	{
	
		$fieldcount 	= $_POST['fieldcount'];
		
		$contestid  	= $_POST['contestid'];
		
		$presslaffid 	= $_POST['presslaffid'];
		
		$xmlString = $this->getContestXML( $fieldcount );		
		
		$entry = $this->presslaffObj->putContestData( $presslaffid, $contestid, $xmlString );
		
		header("Content-Type: application/json");
		
		echo json_encode( $entry );
		die;
	
	}
	
	public function enterContestPublic( )
	{
		$fieldcount 	= $_POST['fieldcount'];
		
		$contestid 		= $_POST['contestid'];
		
		$email 			= $_POST['email'];
		
		$firstname		= $_POST['firstname'];
		
		$lastname 		= $_POST['lastname'];
		
		$presslaffID	= "";
		
		
		$sanitized_email = sanitize_email( urldecode( $email ) );
		
		if( is_email( $sanitized_email ) ) 
		{
			$loginResponse = $this->presslaffObj->getAccountIDbyEmail( $sanitized_email );
			
			if( (int)$loginResponse["status"] == 100 )
			{
				$presslaffID = $loginResponse["accountID"];
			}
			else
			{
				
				$account = $this->presslaffObj->createAccount($firstname, $lastname, $sanitized_email );
				
				if( $account["status"] == 100 )
				{
					$presslaffID = $account["accountid"];					
				}
			}
			
			
			
			$fieldcount 	= $_POST['fieldcount'];
			
			$contestid  	= $_POST['contestid'];
			
			$xmlString = $this->getContestXML( $fieldcount );
			
			$entry = $this->presslaffObj->putContestData( $presslaffID, $contestid, $xmlString );
			
			header("Content-Type: application/json");

			echo json_encode( $entry );
			exit;

		}
		else
		{
			echo json_encode(array("status"=>300) );
			
			exit;
		}
	}
	
	public function createAccount( )
	{
		$nodes = $_POST['nodes'];
		$values;
		
		foreach( $nodes as $node )
		{
			if( isset( $_POST["question_" . $node] ) )
			{
				$values["Data_" . $node] = $_POST["question_" . $node];	
			}
			else
			{
				$values["Data_" . $node] = "";
			}
		}
		
		$resp = $this->presslaffObj->createAccountComplete( $values );
		
		header("Content-Type: application/json");
		
		echo json_encode( $resp );
		exit;
		
	}
	
	public function getAccountInfo( )
	{
		$accountid = $_GET['accountid'];
		
		$accountinfo = $this->presslaffObj->getAccountbyID( $accountid );
		
		$form = "";
		
		if( trim( $accountinfo["status"] ) == "100" )
		{
			$questions = $this->presslaffObj->getRegistrationQuestions( ); 
			
			$form = "<div id='register_msg'></div>
						<span id='modal-close' class='close'></span>
						<form name='updateaccount' action='/wp-admin/admin-ajax.php' method='post'>
						<input type='hidden' name='action' value='createaccount' />
						<input type='hidden' name='accountid' value='" . $accountid . "' />";
						
			
			foreach( $questions["questions"] as $question )
			{
				$value = "";
					
				if( isset( $accountinfo["Data_" . $question["QuestionNumber"]] ) )
				{
					$value = $accountinfo["Data_" . $question["QuestionNumber"]];
				}	
					
				if( trim( $question["QuestionType"] ) != "Hidden Text" )
				{
					
					$form .= "<label for='question_" . $question['QuestionNumber'] . "'>";
					if( (boolean)$question["Required"] )
					{
						$form .= $question["QuestionText"] . "*";	
						$form .= "<input type='hidden' name='question_" . $question["QuestionNumber"] . "_required' value='1' />";
					}
					else
					{
						$form .= $question["QuestionText"];
						$form .= "<input type='hidden' name='question_" . $question["QuestionNumber"] . "_required' value='0' />";
					}
					$form .=  "</label><br />";
				}
				
				switch( $question["QuestionType"] )
				{
					case "Text":
						if( trim( strtolower($question["QuestionType"] ) ) == "password" )
						{
							$form .= "<input type='password' name='question_" . $question['QuestionNumber'] . "' size='40' value='" . $value . "' />";
						}
						else
						{
							$form .= "<input type='text' name='question_" . $question['QuestionNumber'] . "' size='40' value='" . $value . "' />";	
						}
						$form .= "<br />";
					break;
					case "Multiple Choice":
						
						$form .= "<select name='question_" . $question['QuestionNumber'] . "' >";
						$form .= "<option value=''>--Pick One--</option>";
						foreach( $question["choices"] as $choice )
						{
							if( $choice == $value )
							{
								$form .= "<option value='" . $choice . "' selected='selected'>" . $choice . "</option>";		
							}
							else
							{
								$form .= "<option value='" . $choice . "'>" . $choice . "</option>";	
							}
						}
						$form .= "</select>";
						$form .= "<br />";
					break;
					
					
				}
				$form .= "<input type='hidden' name='nodes[]' value='" . $question["QuestionNumber"] . "' />";
			}
			$form .= "<p>* Denotes Required Field</p>";
			$form .= "<input type='submit' name='sub1' value='Edit Information' />";
			$form .= "<br />";
		}
		else
		{
			$form = "<b> Account Information could not be retrieved. </b>";	
		}
		
		echo $form;
		exit;
	}
	
	public function updateAccount( )
	{
		$nodes 	= $_POST['nodes'];
		$id 	= $_POST['accountid'];
		$values;
		
		foreach( $nodes as $node )
		{
			if( isset( $_POST["question_" . $node] ) )
			{
				$values["Data_" . $node] = $_POST["question_" . $node];	
			}
			else
			{
				$values["Data_" . $node] = "";
			}
		}
		
		$resp = $this->presslaffObj->modifyAccount( $values, $id );
		
		header("Content-Type: application/json");
		
		echo json_encode( $resp );
		exit;
	}
	
	public function config( )
	{
		$referer = $_SERVER['HTTP_REFERER'];

		

		$savepath = plugin_dir_path(__FILE__ ) . "assets/presslaff.ini";
		
		if( $_SERVER['REQUEST_METHOD'] == "POST" )
		{
			
			$regUserName 		= $_POST['regUserName'];
			$regPassWord 		= $_POST['regPassWord'];
			$contestUserName 	= $_POST['contestUserName'];
			$contestPassWord	= $_POST['contestPassWord'];
			$regUrl				= $_POST['regUrl'];
			$contestUrl			= $_POST['contestUrl'];
			$stationID			= $_POST['stationID'];
			$loginSlug			= $_POST['loginSlug'];
			$contestSlug		= $_POST['contestSlug'];
			$publicSlug			= $_POST['contestPublicSlug'];
			
			$iniContent = "regUserName:" . urlencode( $regUserName ) . "\n";
			$iniContent .= "regPassWord:" . urlencode( $regPassWord ) . "\n";
			$iniContent .= "contestUserName:" . urlencode( $contestUserName ) . "\n";
			$iniContent .= "contestPassWord:" . urlencode( $contestPassWord ) . "\n";
			$iniContent .= "regUrl:" . urlencode( $regUrl ) . "\n";
			$iniContent .= "contestUrl:" . urlencode( $contestUrl ) . "\n";
			$iniContent .= "stationid:" . $stationID . "\n";
			$iniContent .= "loginSlug:" . $loginSlug . "\n";
			$iniContent .= "contestSlug:" . $contestSlug . "\n";
			$iniContent .= "publicSlug:" . $publicSlug;
		

			
			file_put_contents(plugin_dir_path( __FILE__) . 'assets/presslaff.ini', "");
			
			
			
			header("Location: " . $referer );	
		}
		else
		{
			echo "no dice, man!";
		}

	}
	
	protected function getContestXML( $fieldcount )
	{
		$xmlString = "<contest><data>";
		
		for( $i = 0 ; $i < $fieldcount ; $i++ )
		{
			$fieldid = $_POST['fieldID' . $i];
			
			switch( $_POST[$fieldid . "_type"] )
			{
				case "radio":
					
					if( isset( $_POST['input_' . $fieldid] ) )
					{
						$xmlString .= "<field>";
						$xmlString .= "<questionID>" . $fieldid . "</questionID>";
						$xmlString .= "<responses>";
						$xmlString .= "<response><![CDATA[" . urldecode( $_POST['input_' . $fieldid] ) . "]]></response>";
						$xmlString .= "</responses>";
						$xmlString .= "</field>";
					}
					else
					{
						$xmlString .= "<field>";
						$xmlString .= "<questionID>" . $fieldid . "</questionID>";
						$xmlString .= "<responses>";
						$xmlString .= "<response><![CDATA[]]></response>";
						$xmlString .= "</responses>";
						$xmlString .= "</field>";
					}
					
				break;
				case "checkbox":
					
					if( isset( $_POST['input_' . $fieldid] ) )
					{
						$xmlString .= "<field>";
						$xmlString .= "<questionID>" . $fieldid . "</questionID>";
						$xmlString .= "<responses>";
						
						foreach( $_POST['input_' . $fieldid] as $checkbox )
						{
							$xmlString .= "<response><![CDATA[" . $checkbox . "]]></response>";
						}
						
						$xmlString .= "</responses>";
						$xmlString .= "</field>";
					}
					else
					{
						$xmlString .= "<field>";
						$xmlString .= "<questionID>" . $fieldid . "</questionID>";
						$xmlString .= "<responses>";
						$xmlString .= "<response><![CDATA[]]></response>";
						$xmlString .= "</responses>";
						$xmlString .= "</field>";
						
					}
					
				break;
				case "text":
					
					$xmlString .= "<field>";
					$xmlString .= "<questionID>" . $fieldid . "</questionID>";
					$xmlString .= "<responses>";
					$xmlString .= "<response><![CDATA[" . $_POST['input_' . $fieldid] . "]]></response>";
					$xmlString .= "</responses>";
					$xmlString .= "</field>";
				
				break;
				case "verbatim":
				
					$xmlString .= "<field>";
					$xmlString .= "<questionID>" . $fieldid . "</questionID>";
					$xmlString .= "<responses>";
					$xmlString .= "<response><![CDATA[" . $_POST['input_' . $fieldid] . "]]></response>";
					$xmlString .= "</responses>";
					$xmlString .= "</field>";
				
				break;
				case "select":
				
					$xmlString .= "<field>";
					$xmlString .= "<questionID>" . $fieldid . "</questionID>";
					$xmlString .= "<responses>";
					$xmlString .= "<response><![CDATA[" . $_POST['input_' . $fieldid] . "]]></response>";
					$xmlString .= "</responses>";
					$xmlString .= "</field>";
				
				break;
				case "select_multiple":
				
					$xmlString .= "<field>";
					$xmlString .= "<questionID>" . $fieldid . "</questionID>";
					$xmlString .= "<responses>";
					
					if( isset( $_POST['input_'. $fieldid] ) && ( sizeof( $_POST['input_' . $fieldid] ) > 0 ) )
					{
						foreach( $_POST['input_' . $fieldid] as $item )
						{
							$xmlString .= "<response><![CDATA[" . $item . "]]></response>";
						}	
					}
					else
					{
						$xmlString .= "<response><![CDATA[]]></response>";
					}					
					
					$xmlString .= "</responses>";
					$xmlString .= "</field>";
				
				break;
				case "upload":
					
					$xmlString .= "<field>";
					$xmlString .= "<questionID>" . $fieldid . "</questionID>";
					$xmlString .= "<responses>";
					$xmlString .= "<title><![CDATA[]]></title>";
					$xmlString .= "<description><![CDATA[]]></description>";
					
					$photoname = str_replace("'","", $_FILES['input_' . $fieldid]['name'] );
					$phototype = $_FILES['input_' . $fieldid]['type'];
					$phototmp  = $_FILES['input_' . $fieldid]['tmp_name'];
					$photoerr  = $_FILES['input_' . $fieldid]['error'];
					
					$thumbnailwidth = 95;
					$thumbnailheight = 95;
					
					$scalemag = 600;
					
					
					if( ! function_exists( "wp_handle_upload" ) )
					{
						echo "<script>console.log('wp_handle_upload ! exists');</script>";
						
						require_once(ABSPATH . "wp-admin/includes/file.php");

					}
					
					$imageupload = wp_handle_upload($_FILES['input_' . $fieldid], array('test_form' => false) );
					
					$imageupload_url = $imageupload['url'];
					
					$imageupload_file = $imageupload['file'];
					
					$image_parts = pathinfo( $imageupload_file );
					
					$thumbnail_file = $image_parts["dirname"] . "/" . $image_parts["filename"] . "_thumb." . $image_parts["extension"];
					
					$imageurl_parts = explode(".", $imageupload_url);
					
					$ext = array_pop( $imageurl_parts ); 
					
					$thumbnail_url = implode(".", $imageurl_parts);
					
					$thumbnail_url .= "_thumb." . $ext;
					
					
					if( ! function_exists( "wp_get_image_editor" ) )
					{
						require_once( ABSPATH . "wp-includes/media.php");
					}
					
					$thumb = wp_get_image_editor( $imageupload_file );
					
					if( ! is_wp_error( $thumb ) )
					{
						$thumb->resize( $thumbnailwidth, $thumbnailwidth );
						
						$thumb->save( $thumbnail_file );
					}
					
					list($imagewidth, $imageheight) = getimagesize( $imageupload_file );
					
					$xmlString .= "<photoURL><![CDATA[" . $imageupload_url . "]]></photoURL>";
					$xmlString .= "<thumbnailURL><![CDATA[" . $thumbnail_url . "]]></thumbnailURL>";
					$xmlString .= "<imageHeight>" . $imageheight . "</imageHeight>";
					$xmlString .= "<imageWidth>" . $imagewidth . "</imageWidth>";
					$xmlString .= "</responses>";
					$xmlString .= "</field>";
					
									
				break;
				case "hidden":
					
					$xmlString .= "<field>";
					$xmlString .= "<questionID>" . $fieldid . "</questionID>";
					$xmlString .= "<responses>";
					$xmlString .= "<response><![CDATA[" . $_POST['input_' . $fieldid] . "]]></response>";
					$xmlString .= "</responses>";
					$xmlString .= "</field>";
					
				break;
			}
		}
		
		$xmlString .= "</data></contest>";
		
		return $xmlString;

	}
	
}
