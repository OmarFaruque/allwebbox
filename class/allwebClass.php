<?php 
/**
* Allwebbox class
*/
if (!class_exists('Allwebbox')) {
class Allwebbox{

	public $plugin_url;
	public $plugin_path;
	public $wpdb;
	public $cQuestion;
	public $f_q_value; 
	public $form_table;
	public $journey_table;
	public $entry_table;
	public $template_table;
	public $brandsms;
	public $savedflr_tbl;
	public $tbl_campaign;
	public $tbl_subcampaign;
	public $tbl_objective;
	public $tbl_subobjective;

	//	= $prefix . 'awe_formqansvalue';

	/**Plugin init action**/ 
	public function __construct() {
		global $wpdb;
		$this->plugin_url 		= ALWEBURL;
		$this->plugin_dir 		= ALWEBDIR;
		$this->wpdb 			= $wpdb;	
		$this->form_table 		= $this->wpdb->prefix . 'awe_forms'; 
		$this->cQuestion 		= $this->wpdb->prefix . 'awe_customq';
		$this->f_q_value 		= $this->wpdb->prefix . 'awe_formqansvalue';
		$this->journey_table 	= $this->wpdb->prefix . 'awe_journey';
		$this->entry_table 		= $this->wpdb->prefix . 'awe_entry';
		$this->template_table 	= $this->wpdb->prefix . 'template_table';
		$this->brandsms 		= $this->wpdb->prefix . 'brandsms';
		$this->savedflr_tbl 	= $this->wpdb->prefix . 'savedflr_tbl';
		$this->tbl_campaign 	= $this->wpdb->prefix . 'tbl_campaign';
		$this->tbl_subcampaign 	= $this->wpdb->prefix . 'tbl_subcampaign';
		$this->tbl_objective 	= $this->wpdb->prefix . 'tbl_objective';
		$this->tbl_subobjective = $this->wpdb->prefix . 'tbl_subobjective';


		$this->init();
		$this->createdb();
		$this->get_settings();
		$this->msgvaliditydate();
		$this->sendGridDefaultAPISet();
	}

	private function init(){
		
		
		add_filter('gettext', array($this, 'allwebbox_cpt_text_filter'), 20, 3);

		add_action('admin_menu',array($this,'clivern_plugin_top_menu'));
		add_shortcode('form-custom',array($this,'front_end'));
		add_action( 'admin_enqueue_scripts', array($this, 'allwebbox_admin_script') );
		add_action( 'wp_enqueue_scripts', array($this, 'allwebbox_front_script') );

		/*Ajax Callback*/
		// custom question delete action
    	add_action('wp_ajax_nopriv_deleteCustomQuestion', array($this, 'deleteCustomQuestion'));
		add_action( 'wp_ajax_deleteCustomQuestion', array($this, 'deleteCustomQuestion') );

		/*Custom Question Update Action */
		add_action('wp_ajax_nopriv_updateCustomQuestion', array($this, 'updateCustomQuestion'));
		add_action( 'wp_ajax_updateCustomQuestion', array($this, 'updateCustomQuestion') );

		/*Custom Question Update Action */
		add_action('wp_ajax_nopriv_deleteOptions', array($this, 'deleteOptions'));
		add_action( 'wp_ajax_deleteOptions', array($this, 'deleteOptions') );

		/*Delete Journey*/
		add_action('wp_ajax_nopriv_journeyDelete', array($this, 'journeyDelete'));
		add_action( 'wp_ajax_journeyDelete', array($this, 'journeyDelete') );

		/*Delete Form*/
		add_action('wp_ajax_nopriv_formDelete', array($this, 'formDelete'));
		add_action( 'wp_ajax_formDelete', array($this, 'formDelete') );

		/*Delete Entry*/
		add_action('wp_ajax_nopriv_formEntryDelete', array($this, 'formEntryDelete'));
		add_action( 'wp_ajax_formEntryDelete', array($this, 'formEntryDelete') );

		/*Store Campaign*/
		add_action('wp_ajax_nopriv_storeCampaign', array($this, 'storeCampaign'));
		add_action( 'wp_ajax_storeCampaign', array($this, 'storeCampaign') );

		/*Delete Campaign */
		add_action('wp_ajax_nopriv_campaignDelete', array($this, 'campaignDelete'));
		add_action( 'wp_ajax_campaignDelete', array($this, 'campaignDelete') );

		/*Delete Sub-Campaign*/
		add_action('wp_ajax_nopriv_subCampaignDelete', array($this, 'subCampaignDelete'));
		add_action( 'wp_ajax_subCampaignDelete', array($this, 'subCampaignDelete') );

		/*UPdate Campaign Name / Title */
		add_action('wp_ajax_nopriv_updateCampaignTitle', array($this, 'updateCampaignTitle'));
		add_action( 'wp_ajax_updateCampaignTitle', array($this, 'updateCampaignTitle') );

		/*Campaign Email Sent*/		
		add_action('wp_ajax_nopriv_campaignEmailSent', array($this, 'campaignEmailSent'));
		add_action( 'wp_ajax_campaignEmailSent', array($this, 'campaignEmailSent') );

		/*Object Data Storage*/
		add_action('wp_ajax_nopriv_storeObjective', array($this, 'storeObjective'));
		add_action( 'wp_ajax_storeObjective', array($this, 'storeObjective') );

		/*Delete Sub-Campaign*/
		add_action('wp_ajax_nopriv_subObjectiveDelete', array($this, 'subObjectiveDelete'));
		add_action( 'wp_ajax_subObjectiveDelete', array($this, 'subObjectiveDelete') );

		
		/*Delete Objective */
		add_action('wp_ajax_nopriv_objectiveDelete', array($this, 'objectiveDelete'));
		add_action( 'wp_ajax_objectiveDelete', array($this, 'objectiveDelete') );

		/*Select Sub Object for related Campaign */
		add_action('wp_ajax_nopriv_selectRltSubObject', array($this, 'selectRltSubObject'));
		add_action( 'wp_ajax_selectRltSubObject', array($this, 'selectRltSubObject') );

		/*Lode Existing Email Template */
		add_action('wp_ajax_nopriv_loadTemplateFunction', array($this, 'loadTemplateFunction'));
		add_action( 'wp_ajax_loadTemplateFunction', array($this, 'loadTemplateFunction') );


		

		
		
		/*Add Ajax loading html to footer*/
		add_action('admin_footer', array($this, 'awboxAdminFootherSpneer'));

		/*Content Filter*/
		 add_filter( 'the_content', array( $this, 'process_content' ), 9999 );
		
		/*WP header*/
		add_action('admin_head', array($this, 'jquerytohead'));


		/* Crone Job for SMS/Message */
		add_action('wp', array($this, 'cronstarter_activation'));

		// hook that function onto our scheduled event:
		add_action ('allwebcronjob', array($this, 'my_repeat_function')); 
		register_deactivation_hook (__FILE__, array($this, 'cronstarter_deactivate'));

		/*Extra Function for test perpose */
		add_filter('cron_schedules', array($this, 'my_cron_schedules'));

		/*Add Script to wp head */
		add_action('wp_head', array($this, 'allwebPushNotification'));

		/*Change Mimes for upload CSV*/
		add_action('upload_mimes', array($this, 'allwebbox_upload_mimes'));

		add_action( 'admin_init', array( $this, 'admin_page_message_settings' ) );

		/*Load Language File*/
		add_action('plugins_loaded', array($this, 'allwebboxLanguageFile'));
	}



		/**
		 * get all settings
		 */
	    private function get_settings() {
	      $defaultM = 'Perform on social section for unlock content';
	      $this->settings = array();
	      $this->settings['message_count_as'] = (get_option( 'message_count_as'))?get_option( 'message_count_as'):'day';
	      $this->settings['message_count_amount'] = (get_option('message_count_amount'))?get_option('message_count_amount'):'0';
	      $this->settings['allwebThisSection'] = (get_option('allwebThisSection'))?get_option('allwebThisSection'):'0';
	    }


	  /*
	  * Send Grid Default API Key
	  */
	  private function sendGridDefaultAPISet(){
	  	$dfltKey = get_site_option( 'sendgrid_api_key', $default = false, $deprecated = true );
	  	$exKey = get_option( 'sendgrid_api_key', $default = false );
	  	if(!$exKey){
	  		update_option( 'sendgrid_api_key', $dfltKey, $autoload = null );
	  	}
	  }


	  /*
	  * Update Message Section Date
	  */
	  private function msgvaliditydate(){
	  	$sction = $this->settings['message_count_as'];
	  	$day = 0;
	  	if($sction == 'day'){
	  		$day = 1;
	  	}elseif($sction == 'week'){
	  		$day = 7; 
	  	}elseif($sction == 'month'){
	  		$day = 30;
	  	} 


	  	if(get_option( 'msg_uptodate')){

	  		$exDate 	= get_option( 'msg_uptodate');
	  		$newDate 	= date('Y-m-d', strtotime($exDate . ' +'.$day.' day'));
	  		
	  		if(strtotime($exDate) < strtotime(date('Y-m-d', time())) ){
	  			update_option( 'msg_uptodate', $newDate, $autoload = null );
	  			update_option( 'allwebThisSection', '0', $autoload = null );
	  		}
	  	}else{
	  		$nextDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$day.' day'));		
	  		add_option( 'msg_uptodate', $value = $nextDate, $deprecated = '', $autoload = 'yes' );
	  	}

	  } //End msgvaliditydate


	/*
	* Create Table
	*/

	private function createdb(){

		/**
		* Create tbl_objective table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->tbl_objective'") != $this->tbl_objective) {
		    //table not in database. Create new table
		    $charset_collate = $this->wpdb->get_charset_collate();
		    $sqlo = "CREATE TABLE $this->tbl_objective (
		         id int(10) NOT NULL AUTO_INCREMENT,
		         objective_name varchar(500) NOT NULL,
		         ob_desc text NOT NULL, 
		         created_dt timestamp NOT NULL,
		         UNIQUE KEY id (id)
		    ) $charset_collate;";
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sqlo );
		}


		/**
		* Create tbl_subobjective table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->tbl_subobjective'") != $this->tbl_subobjective) {
		    //table not in database. Create new table
		    $charset_collate = $this->wpdb->get_charset_collate();
		    $sqlso = "CREATE TABLE $this->tbl_subobjective (
		         id int(20) NOT NULL AUTO_INCREMENT,
		         oids int(10) NOT NULL,
		         sub_obj varchar(500) NOT NULL,
		         sub_desc text NOT NULL,
		         created_dt timestamp NOT NULL,
		         UNIQUE KEY id (id)
		    ) $charset_collate;";
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sqlso );
		}



		/**
		* Create tbl_campaign table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->tbl_campaign'") != $this->tbl_campaign) {
		    //table not in database. Create new table
		    $charset_collate = $this->wpdb->get_charset_collate();
		    $sqlc = "CREATE TABLE $this->tbl_campaign (
		         id int(10) NOT NULL AUTO_INCREMENT,
		         cmp_name varchar(500) NOT NULL,
		         obj int(20) NOT NULL,
		         sub_obj varchar(300) NOT NULL,
		         created_dt timestamp NOT NULL,
		         UNIQUE KEY id (id)
		    ) $charset_collate;";
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sqlc );
		}



		/**
		* Create tbl_subcampaign table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->tbl_subcampaign'") != $this->tbl_subcampaign) {
		    //table not in database. Create new table
		    $charset_collate = $this->wpdb->get_charset_collate();
		    $sqlsc = "CREATE TABLE $this->tbl_subcampaign (
		         id int(20) NOT NULL AUTO_INCREMENT,
		         cid int(10) NOT NULL,
		         scmp_name varchar(500) NOT NULL,
		         type varchar(100) NOT NULL,
		         landing int(50) NOT NULL,
		         subject text NOT NULL,
		         sc_content text NOT NULL,
		         smspush text NOT NULL,
		         sms text NOT NULL,
		         push text NOT NULL,
		         action int(10) NOT NULL,
		         nd_action text NOT NULL,
		         created_dt timestamp NOT NULL,
		         UNIQUE KEY id (id)
		    ) $charset_collate;";
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sqlsc );
		}


		/**
		* Create Main Form Table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->form_table'") != $this->form_table) {
		    //table not in database. Create new table
		    $charset_collate = $this->wpdb->get_charset_collate();
		    $sql = "CREATE TABLE $this->form_table (
		         row_id mediumint(10) NOT NULL AUTO_INCREMENT,
		         form_name varchar(500) NOT NULL,
		         identi_ques text NOT NULL,
		         contact_ques text NOT NULL,
		         profile_ques text NOT NULL,
		         custom_ques text NOT NULL,
		         created_custom_que text NOT NULL,
		         total_custom_ques tinyint(30) NOT NULL,
		         total_enteries tinyint(30) NOT NULL,
		         style text NOT NULL,
		         journey int(50) NULL,
		         content varchar(500) NULL,
		         terms text NOT NULL,
		         brand_options text NOT NULL,
		         created_dt timestamp NOT NULL,
		         UNIQUE KEY row_id (row_id)
		    ) $charset_collate;";
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sql );
		}

		/*
		* Custom Question table
		*/

		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->cQuestion'") != $this->cQuestion) {
		    //table not in database. Create new table
		    $charset_collate = $this->wpdb->get_charset_collate();
		    $sql = "CREATE TABLE $this->cQuestion (
		         row_id mediumint(10) NOT NULL AUTO_INCREMENT,
		         form_id mediumint(10) NOT NULL,
		         questions text NOT NULL,
		         answer_type varchar(200) NOT NULL,
		         total_single int(5) NOT NULL,
		         total_multi int(5) NOT NULL,
		         question_enable int(5) NOT NULL,
		         required int(5) NOT NULL DEFAULT 0,
		         created_dt timestamp NOT NULL,
		         UNIQUE KEY row_id (row_id)
		    ) $charset_collate;";
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sql );
		}



		/*
		* Custom Question select type option value
		*/

		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->f_q_value'") != $this->f_q_value) {
		    //table not in database. Create new table
		    $charset_collate = $this->wpdb->get_charset_collate();
		    $sql = "CREATE TABLE $this->f_q_value (
		         row_id mediumint(10) NOT NULL AUTO_INCREMENT,
		         entry_id mediumint(20) NOT NULL,
		         form_id mediumint(20) NOT NULL,
		         ques_value text NOT NULL,
		         created_dt timestamp NOT NULL,
		         UNIQUE KEY row_id (row_id)
		    ) $charset_collate;";
		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sql );
		}

		/*
		* Journey table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->journey_table'") != $this->journey_table) {
		     //table not in database. Create new table
		     $charset_collate = $this->wpdb->get_charset_collate();
		     $sql = "CREATE TABLE $this->journey_table (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          j_name varchar(500) NOT NULL,
		          
		          j_description text NOT NULL,
		          j_goal text NOT NULL,
		          j_sender varchar(500) NOT NULL,
		          j_rep_email varchar(300) NOT NULL,

		          j_time int(50) NOT NULL,
		          j_time_unit varchar(500) NOT NULL,
		          j_emails text NOT NULL,
		          j_unsubscribe text,
		          j_type varchar(100) NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
		}


		/*
		* Template Table 
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->template_table'") != $this->template_table) {
		     //table not in database. Create new table
		     $charset_collate = $this->wpdb->get_charset_collate();
		     $sql = "CREATE TABLE $this->template_table (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          name varchar(500) NOT NULL,
		          tmplate text NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
		}





		/*
		* Entry table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->entry_table'") != $this->entry_table) {
		     //table not in database. Create new table
		     $charset_collate = $this->wpdb->get_charset_collate();
		     $sql = "CREATE TABLE $this->entry_table (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          form_id mediumint(20) NOT NULL,
		          firstname varchar(500) NOT NULL,
		          lastname varchar(500) NOT NULL,
		          nickname varchar(500) NOT NULL,
		          salute varchar(100) NOT NULL,
		          idnumber varchar(200) NOT NULL,
		          brand varchar(500) NOT NULL,
		          country varchar(300) NOT NULL,
		          city varchar(300) NOT NULL,
		          address text NOT NULL,
		          email varchar(200) NOT NULL,
		          subscribed varchar(300) NOT NULL,
		          phonenumber varchar(200) NOT NULL,
		          facebook text NOT NULL,
		          twitter text NOT NULL,
		          mobile varchar(300) NOT NULL,
		          linkedin text NOT NULL,
		          instagram text NOT NULL,
		          google text NOT NULL, 
		          pinterest text NOT NULL,
		          youtube text NOT NULL,
		          whatsapp varchar(200) NOT NULL,
		          gender varchar (100) NOT NULL,
		          dateofbirth date NOT NULL,
		          civilstatus varchar(200) NOT NULL,
		          academiclevel varchar (300) NOT NULL,
		          others text NOT NULL,
		          journey_lastdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		          j_fixed_dt text NOT NULL,
		          journey_count int(10) NOT NULL DEFAULT '0',
		          vest_journey mediumint(30) NOT NULL,
		          smslastdate date NOT NULL,
		          brandsms_count int(10) NOT NULL DEFAULT '0',
		          sms_unsbe varchar(100) NOT NULL,
		          ip varchar(500) NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
		} //End Entry Table



		/*
		* Brand Sms's
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->brandsms'") != $this->brandsms) {
		     //table not in database. Create new table
		     $charset_collateb = $this->wpdb->get_charset_collate();
		     $sqlb = "CREATE TABLE $this->brandsms (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          brand_name varchar(500) NOT NULL,
		          msgtype varchar(200) NOT NULL,
		          msgduration varchar(200) NOT NULL,
		          brand_icon varchar(100) NOT NULL,
		          msgamount int(50) NOT NULL,
		          sendmsgcount int(50) NOT NULL,
		          msg text NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collateb;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sqlb );
		} //End Brand Table


		/*
		* Saved Filter Table
		*/
		if($this->wpdb->get_var("SHOW TABLES LIKE '$this->savedflr_tbl'") != $this->savedflr_tbl) {
		     //table not in database. Create new table
		     $charset_collatef = $this->wpdb->get_charset_collate();
		     $sqlf = "CREATE TABLE $this->savedflr_tbl (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          filter_name varchar(500) NOT NULL,
		          f_description text NOT NULL,
		          sv_filter text NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collatef;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sqlf );
		} //End Brand Table
		   

	}

	/**DROP database table**/ 
	function user_rating_deactivate() {

	}
		
	/**Css add plugin admin**/ 

	public function survey_assets(){
	 wp_enqueue_style ( 'customcss', plugin_dir_url(__FILE__).'assets/css/plugin_style.css', '', '4.8.1',false);
	}


	/**Create admin menu**/
	public function clivern_plugin_top_menu(){
	add_menu_page(__('Smart Marketing', 'allwebbox'), __('Smart Marketing', 'allwebbox'), 'manage_options', 'my-menu', '','dashicons-admin-users' );
	   
		/*Email Marketing Campaigns*/
	    add_submenu_page('my-menu', __('Strategy', 'allwebbox'), __('Strategy', 'allwebbox'), 'manage_options', 'my-menu', array(&$this,'email_markeging_campaigns'));

	    /*CRM*/
	   	add_submenu_page('my-menu', __('CRM', 'allwebbox'), __('CRM', 'allwebbox'), 'manage_options', 'crm', array(&$this,'crm'));

	    add_submenu_page('my-menu', __('Create form', 'allwebbox'), __('Create form', 'allwebbox'), 'manage_options', 'create_new', array(&$this,'clivern_render_plugin_page'));
		add_submenu_page('my-menu', __('All Forms', 'allwebbox'), __('All Forms', 'allwebbox'), 'manage_options', 'all_forms', array(&$this,'all_forms'));

	    add_submenu_page('my-menu', __('Journey', 'allwebbox'), __('Journey', 'allwebbox'), 'manage_options', 'email_marketing', array(&$this,'email_marketing'));
	    add_submenu_page(null, __('Form Entries', 'allwebbox'), __('Form Entries', 'allwebbox'), 'manage_options', 'all_form_entries', array(&$this,'all_form_entries'));
	    

	    /*Email Template */
	    add_submenu_page('my-menu', __('Templates', 'allwebbox'), __('Templates', 'allwebbox'), 'manage_options', 'email_templates', array(&$this,'email_templates'));

	    /*SMS / Message Template */
	    //add_submenu_page('my-menu', 'SMS/Message', 'SMS/Message', 'manage_options', 'sms-message', array(&$this,'smsAndMessage'));

	    /* Configuration Page */
	    add_submenu_page('my-menu', __('Configuration', 'allwebbox'), __('Configuration', 'allwebbox'), 'manage_options', 'config', array(&$this,'configurationSmarMKT'));


	   }

	/**Get all the data from the tabe **/
	function clivern_render_plugin_page(){
		/*if(isset($_GET['id'])){ 
			require_once($this->plugin_dir . 'inc/edit.php');
			edit_record();
		}else{*/
			require_once($this->plugin_dir . 'inc/mainpage.php');
			All_web_box();
		//}
	}   

	public function all_forms() {
		require_once($this->plugin_dir . 'inc/all_forms.php');
	}

	public function crm(){
		require_once($this->plugin_dir . 'inc/crm.php');
	}
	function email_marketing(){
		require_once($this->plugin_dir . 'inc/email_marketing.php');
		Email_marketing();
	}
	function all_form_entries(){
		require_once($this->plugin_dir . 'inc/form_entries.php');
		Form_entries();
	}

	function front_end($form_id) {
		 //print_r($form_id);
		 //return "Hello Front End ".$form_id['id'];
		 require_once($this->plugin_dir . 'inc/view_front.php');
		 return view_area($form_id['id']);
	}

	function allwebbox_admin_script(){
		wp_enqueue_style( 'allWebboxAdminCSS', $this->plugin_url . 'assets/css/admincss.css', array(), true, 'all' );
		wp_enqueue_style( 'fontAwesone', $this->plugin_url . 'assets/font-awesome/font-awesome.min.css', array(), true, 'all' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'jquery-ui-sortable');
		
		if(isset($_GET['page'])):
		if($_GET['page'] == 'email_templates' || $_GET['page'] == 'email_marketing' || $_GET['page'] == 'my-menu' || $_GET['page'] == 'crm' || $_GET['page'] == 'email_markeging_campaigns' || $_GET['page'] == 'sms-message'){
			wp_enqueue_style( 'fastselect', $this->plugin_url . 'assets/css/fastselect.min.css', array(), true, 'all' );	
			wp_enqueue_script( 'fastselectJS', $this->plugin_url . 'assets/js/fastselect.standalone.min.js', array(), false, false );
			wp_enqueue_script( 'nicEdit', $this->plugin_url . 'assets/js/nicEdit-latest.js', array(), false, false );
			
		}
		if(isset($_GET['page']) && $_GET['page'] == 'all_forms' || $_GET['page'] == 'crm' || $_GET['page'] == 'email_markeging_campaigns' || $_GET['page'] == 'all_form_entries' || $_GET['page'] == 'sms-message'){
			wp_enqueue_style( 'dataTableCSS', $this->plugin_url . 'assets/css/dataTables.jqueryui.min.css', array(), true, 'all' );	

			wp_enqueue_script( 'dataTablejs', $this->plugin_url . 'assets/js/jquery.dataTables.min.js', array(), false, false );
			wp_enqueue_script( 'dataTablejQuery', $this->plugin_url . 'assets/js/dataTables.jqueryui.min.js', array(), false, false );// no need
		}
		endif; // isset($_GET['page'])
		wp_enqueue_style( 'jQueryUICSS', $this->plugin_url . 'assets/css/jquery-ui.css', array(), true, 'all' );	
		wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
		wp_enqueue_script( 'tinymce', $this->plugin_url . 'assets/js/tinymce.min.js', array(), false, true );
		wp_enqueue_script( 'allWebboxAdminJS', $this->plugin_url . 'assets/js/adminjs.js', array(), false, false );

		wp_localize_script( 'allWebboxAdminJS', 'webbox', admin_url( 'admin-ajax.php' ));

	}


	/*
	* Ajax Call back functon for delete custom question 
	*/
	function deleteCustomQuestion(){
		$qs = $_POST['question'];
		$qs = htmlentities($qs);

		$delete = $this->wpdb->delete( $this->cQuestion, array( 'questions' => $qs ), array( '%s' ) );
		if($delete){
			echo 'Success';
		}else{
			echo 'Failed';
		}
		die();
	}

	/*
	* Update Call function for custom created question 
	*/
	function updateCustomQuestion(){
		$type 		= $_POST['type'];
		$entry_id 	= $_POST['entry_id'];
		$required 	= $_POST['required'];
		$options 	= $_POST['options'];
		$formid 	= $_POST['formid'];
		$update = $this->wpdb->update(
			$this->cQuestion, 
			array(
				'answer_type' => $type,
				'required' 	=> $required
			),
			array(
				'row_id' 	=> $entry_id
			)
		);


		$reload = false;
		if(is_array($options)){
			foreach($options as $sop){
				if(empty($sop[0]) && !empty($sop[1])){
					$this->wpdb->insert( 
						$this->f_q_value, 
						array( 
							'entry_id' => $entry_id, 
							'form_id' => $formid,
							'ques_value' => $sop[1] 
						), 
						array( 
							'%d', 
							'%d',
							'%s' 
						) 
					);
				}else{
					$this->wpdb->update(
						$this->f_q_value, 
						array(
							'ques_value' => $sop[1]
						),
						array(
							'row_id' 	=> $sop[0]
						)
					);	
				} // End is empty row_id
			}// end options foreach
		$reload = true;
		}

		if($update || $reload){
			echo 'success';
		}else{
			echo 'fail';
		}
		die();
	}


	/*
	* Ajax Delete options
	*/
	function deleteOptions(){
		$row_id = $_POST['row_id'];
		$delete = $this->wpdb->delete( $this->f_q_value, array( 'row_id' => $row_id ), array( '%d' ) );
		if($delete){
			echo 'success';
		}else{
			echo 'fail';
		}
		die();
	}

	/*
	* Delete Journey
	*/
	function journeyDelete(){
		$id = $_POST['id'];
		$delete = $this->wpdb->delete( $this->journey_table, array( 'id' => $id ), array( '%d' ) );
		if($delete){
			echo 'success';
		}else{
			echo 'fail';
		}
		die();
	}

	function formDelete(){
		$id = $_POST['id'];
		$delete = $this->wpdb->delete( $this->form_table, array( 'row_id' => $id ), array( '%d' ) );
		if($delete){
			echo 'success';
		}else{
			echo 'fail';
		}
		die();
	}


	function awboxAdminFootherSpneer(){
		echo '<div class="awbox-spinner"><div class="spineer_inner"></div></div>';
	}

	function allwebbox_front_script(){
			wp_enqueue_style( 'chosenCSS', $this->plugin_url . 'assets/css/chosen.min.css', array(), true, 'all' );	
			wp_enqueue_style( 'allwebboxFrontCSS', $this->plugin_url . 'assets/css/allwebboxfront.css', array(), true, 'all' );	
			wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
  			wp_enqueue_style( 'jquery-ui' );   
			wp_enqueue_script( 'chosenJS', $this->plugin_url . 'assets/js/chosen.jquery.js', array(), false, true );

			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );


			wp_enqueue_script( 'pushJS', $this->plugin_url . 'assets/js/push.min.js', array(), true, false );
			wp_enqueue_script( 'allwebboxJS', $this->plugin_url . 'assets/js/allwebboxfrontjs.js', array(), false, true );
			
	}

	/*
	* Ajax Delete Entry
	*/
	function formEntryDelete(){
		$id = $_POST['id'];
		$delete = $this->wpdb->delete( $this->entry_table, array( 'id' => $id ), array( '%d' ) );
		if($delete){
			echo 'success';
		}else{
			echo 'fail';
		}
		die();
	}


	/*
	* Email marketing campaign
	*/
	function email_markeging_campaigns(){
		require_once($this->plugin_dir . 'inc/email_marketing_campaign.php');
	}

	/*
	* Email Templates
	*/
	function email_templates(){
		require_once($this->plugin_dir . 'inc/email_templates.php');
	}

	/*
	* content desable if el parameter is true
	*/
	function process_content($content){
		global $post;
		$el = (isset($_GET['el']))?true:false;
		if($el){
			$html ='<div style="margin-top:150px; margin-bottom:150px;" class="fl-row fl-row-fixed-width fl-row-bg-none"><div class="fl-row-content-wrap">';
			$html .= '<form action="'.get_permalink($post->ID).'" method="post" accept-charset="utf-8">
					<div class="col-md-12 col-sm-12">
					<div class="form-group">
						<label for="usrEmail">Submit your Email for continue</label>
						<input type="email" required name="usrEmail" value="">
					</div>
					<input type="submit" class="btn btn-primary" value="Continue" />
					</div>
			</form>';
			$html .= '</div></div>';
			return $html;
		}

		elseif(isset($_POST['r_quss']) && $_POST['r_quss'] != ''){

			$styles = (isset($_POST['style']))?explode(';', $_POST['style']):array(); 
			$pstyles = array();
			foreach($styles as $sS){
				$eS = explode('=', $sS);
				$pstyles[$eS[0]] = $eS[1];		
			}

			/*echo '<pre>';
			print_r($pstyles);
			echo '</pre>';*/

			$qsall = explode(';', $_POST['r_quss']);

			/*
			* Submit Process
			*/
			$sMeg = '';
			if(isset($_POST['submit_e'])){
				$otherArray = array();
				if(isset($_POST['others'])){
					foreach($_POST['others'] as $k => $ot) $otherArray[$k] = (empty($ot))?0:$ot;	
				}

				$imploadOthers = implode('; ', array_map(
				    function ($v, $k) 
				    { 
				    	if(is_array($v)){
				    		$outp = array();
				    		foreach($v as $sv) {
				    			array_push($outp, sprintf("%s", $sv));
				    		}
				    		$out = $k . '=' . implode(', ', $outp);
				    		return $out;
				    	}else{
				    		return sprintf("%s=%s", $k, $v); 	
				    	}
				    },
				    $otherArray,
				    array_keys($otherArray)
				));
				$_POST['others'] = $imploadOthers;
				$posts = $_POST;
				unset($posts['r_entry_id']);
				unset($posts['r_quss']);
				unset($posts['submit_e']);



				$update = $this->wpdb->update(
					$this->entry_table,
					$posts,
					array(
						'id' => $_POST['r_entry_id']
					)
				);

				if($update){
					$sMeg = '<div class="successMsg alert-success bg-success"><span>'.__('Your Form submit successfully.', 'allwebbox').'</span></div>';
					echo '<script>window.location.replace("'.home_url( $path = '/', $scheme = null ).'");</script>';
				}else{
					$sMeg = '<div class="successMsg alert-danger bg-danger"><span>'.__('Your Form submit Failed, please try again.', 'allwebbox').'</span></div>';
				}

			} // End Submit process if(isset($_POST['submit_e']))
			$title = (isset($pstyles['lnd_heading']))?'<div class="col-md-12 headingextiRecord"><h2 class="title">'.$pstyles['lnd_heading'].'</h2></div>':'';
			$output = '<div class="fl-row-content-wrap">
				<div class="fl-row-content fl-row-fixed-width fl-node-content">
				<div id="entryFormAllWebBox" class="entryForm"><div class="entryFormInner">
					<form class="form allwebboxDynamicForm editExisting" action="" method="post" accept-charset="utf-8">
						<div class="row">'.$title.'';

						if($sMeg != ''){
							$output .= '<div id="messageSow" class="col-md-12 col-sm-12">'.$sMeg.'</div>';
						}
						foreach($qsall as $sQ){
							$sQN = str_replace(' ', '', strtolower($sQ));
							$sQ = htmlentities($sQ);

							$type = $this->wpdb->get_row('SELECT `row_id`, `required`, `answer_type` FROM '.$this->cQuestion.' WHERE questions="'.$sQ.'"');
							$req = '';
							$requi = '';
							if($type){
								if($type->required == 1){
									$req .= '<small><i>(required *)</i></small>';
									$requi .= 'required';
								}
							}else{
								$req .= '<small><i>(required *)</i></small>';
								$requi .= 'required';
							}

							if($type){
								if($type->answer_type == 2){
									$output .= '<div class="form-group col-sm-12 col-xs-12 col-sm-12">';	
								}else{
									$output .= '<div class="form-group col-sm-6 col-xs-12 col-sm-6">';		
								}
							}else{
								$output .= '<div class="form-group col-sm-6 col-xs-12 col-sm-6">';	
							}
							
							$output .= '<label for="'.$sQN.'">'.$sQ. ' '. $req .'</label>';
							
							switch($sQN){
								case 'salute(mr,ms,miss,dr)':
									$salutesArray = array('Mr', 'Ms', 'Miss', 'Dr');
									$output .= '<select '.$requi.' class="form-control" name="salute" id="salute">';
									foreach($salutesArray as $ss){
										$output .= '<option value="'.$ss.'">'.$ss.'</option>';
									}
									$output .= '</select>';
								break;
								case 'phonenumber':
								case 'mobile':
									$output .= '<input '.$requi.' type="tel" name="'.$sQN.'" id="'.$sQN.'" class="form-control"/>';
								break;
								case 'email':
									$output .= '<input '.$requi.' type="email" name="'.$sQN.'" id="'.$sQN.'" class="form-control"/>';
								break;
								case 'dateofbirth':
									$output .= '<input '.$requi.' type="text" name="'.$sQN.'" id="'.$sQN.'" class="form-control datepicker"/>';
								break;

								case 'google+':
									$output .= '<input '.$requi.' type="text" name="'.str_replace('+', '', $sQN).'" id="'.$sQN.'" class="form-control"/>';
								break;
								case 'gender':
									$output .= '<select '.$requi.' class="form-control" name="gender" id="gender">';
										$output .= '<option value="male">Male</option>';
										$output .= '<option value="female">Female</option>';
									$output .= '</select>';
								break;

								/*Default Others*/
								case 'dateofvisit':
									$output .= '<input '.$requi.' type="text" name="others['.$sQN.']" id="'.$sQN.'" class="form-control datepicker"/>';
								break;

								case 'incharge':
								case 'economicactivity':
								case 'presenceinsocialnetworks':
								case 'hasawebsite':
								case 'yourwebsitehas':
									$output .= '<input '.$requi.' type="text" name="others['.$sQN.']" id="'.$sQN.'" class="form-control"/>';
								break;

								default:
									if($type){
										switch($type->answer_type){

											/*
											* Custom Post Types
											*/
											case 3:
											$soptions = $this->wpdb->get_results("SELECT `row_id`, `ques_value` FROM $this->f_q_value WHERE `entry_id`= '$type->row_id'", OBJECT);
											$output .='<select '.$requi.' name="others['.$sQ.']" class="form-control">';
												for($c=0; count($soptions) > $c; $c++){
													$output .='<option value="'.$soptions[$c]->ques_value.'">'.$soptions[$c]->ques_value.'</option>';
												}
											$output .='</select>';
											break;
											case 4:
												$soptions = $this->wpdb->get_results("SELECT `row_id`, `ques_value` FROM $this->f_q_value WHERE `entry_id`= '$type->row_id'", OBJECT);

												//$html .='<select '.$required.' multiple name="others['.$bcs.'][]" class="form-control">';
												$output .= '<br/>';
													for($c=0; count($soptions) > $c; $c++){


														//$html .='<option '.$sltd.' value="'.$soptions[$c]->ques_value.'">'.$soptions[$c]->ques_value.'</option>';
														$output .= '<label  class="questom '.$requi.' checkbox-inline"><input type="checkbox" '.$requi.' name="others['.$sQ.'][]" value="'.$soptions[$c]->ques_value.'" />'.$soptions[$c]->ques_value.'</label>';

													}
												//$html .='</select>';
											break;
											case 2: 
												$output .= '<textarea '.$requi.' id="'. $sQN .'" name="others['.$sQ.']" class="form-control"></textarea>';
											break;
											case 5: 
												$output .= '<input type="number" '.$requi.' class="form-control" name="others['.$sQ.']" value=""/>';
											break;
											case 6: 
												$output .= '<input type="text" class="datepicker '.$requi.' form-control" name="others['.$sQ.']" value=""/>';
											break;

											case 7: 
												$output .= '<input type="email" class="form-control" '.$requi.' name="others['.$sQ.']" value=""/>';
											break;

											default:
											$output .= '<input '.$requi.' type="text" name="others['.$sQ.']" id="'.$sQN.'" class="form-control"/>';	
										}
										

									}else{
										$output .= '<input '.$requi.' type="text" name="'.$sQN.'" id="'.$sQN.'" class="form-control"/>';
									} // End if($type) 
							} // End Switch
							$output .= '</div>';
						}
						$output .= '</div>
					<input type="hidden" name="r_entry_id" value="'.$_POST['r_entry_id'].'" />
					<input type="hidden" name="r_quss" value="'.$_POST['r_quss'].'" />
					<input type="submit" name="submit_e" value="'.__('Submit', 'allwebbox').'" class="btn btn-primary">
					</form>
					</div></div>
				</div>
			</div>';


			 $bgimg 	= (isset($pstyles['lnd_bgImg']))?wp_get_attachment_url( $pstyles['lnd_bgImg'] ):'';
		     $bgrepet 	= (isset($pstyles['bg_repeat']) == 'yes')?'repeat':'no-repeat';	 

		     $output .= '<style>
		     div#entryFormAllWebBox form{
		     	position:relative;
		     }
		     div#entryFormAllWebBox{
		     	padding:20px;
		     	background-image:url('.$bgimg.');
		     	background-repeat:'.$bgrepet.';
		     	background-attachment:'.$pstyles['bg_attachment'].';
		     	background-size:'.$pstyles['bg_size'].';
		     	position:relative;
		     }';
		     if(isset($pstyles['lnd_bgImg'])){
		     	$output .= '
		     		.headingextiRecord h2,
		     		div#entryFormAllWebBox form label{
		     			color:#fff;
		     		}
		     	';
		     }
		    if(isset($pstyles['bg_overColor']) && isset($pstyles['bg_overOpacity'])){
				$output .= '
				div#entryFormAllWebBox .entryFormInner:before{
					background-color: '.$this->hex2rgba($pstyles['bg_overColor'], $pstyles['bg_overOpacity']).';
				}
				';
			}



 	$output .='</style>';

			return $output;
			
		}

		else{
			return $content;
		}

	}

	
	/*
	* jQuery to head
	*/
	function jquerytohead(){
		$columns 		= $this->wpdb->get_col("DESC " . $this->entry_table, 0);
		$allpages 		= get_all_page_ids();
		$exTemplates 	= $this->wpdb->get_results('SELECT * FROM '.$this->template_table.'', OBJECT);
		$sTemp 			= (isset($_GET['ltmpid']))?$_GET['ltmpid']:'';

		$allPagesJs = array();
		$allemTemp = array();
		foreach($allpages as $sp)  $allPagesJs[$sp] = get_the_title( $sp );
		if($exTemplates) foreach($exTemplates as $st)  $allemTemp[$st->id] = $st->name;
		echo "<script>
			var entry_clmn 	= '".json_encode($columns)."';
			var allPages 	= '".json_encode($allPagesJs)."';
			var emalTems 	= '".json_encode($allemTemp)."';
			var sltTemp 	= '".$sTemp."';

		</script>";
	}

	/*function smsAndMessage(){
		require_once($this->plugin_dir . 'inc/smsnmessage.php');
	}*/

	// create a scheduled event (if it does not exist already)
	function cronstarter_activation() {
		if( !wp_next_scheduled( 'allwebcronjob' ) ) {  
		   //wp_schedule_event( time(), 'daily', 'allwebcronjob' );  
		   wp_schedule_event( time(), '3min', 'allwebcronjob' );  
		}
	}


	// unschedule event upon plugin deactivation
	function cronstarter_deactivate() {	
		// find out when the last event was scheduled
		$timestamp = wp_next_scheduled ('allwebcronjob');
		// unschedule previous event if any
		wp_unschedule_event ($timestamp, 'allwebcronjob');
	} 


	/*
	* SMS next Date
	*/
	public function tergateSMSDate($lastD, $duration){
             $prevDate = ($lastD !='0000-00-00')?$lastD:date('Y-m-d', strtotime("-1 day"));
            $nextDate = '';
            if($duration == 'day'){
                $nextDate .= date('Y-m-d', strtotime($prevDate . ' +1 day'));
            }elseif($duration == 'week'){
                $nextDate .= date('Y-m-d', strtotime($prevDate . ' +7 days'));
            }else{
                $nextDate .= date('Y-m-d', strtotime($prevDate . ' +30 days'));
            }
            return $nextDate;
    }

    /*
    * Crone Job for Send SMS to email
    */
	// here's the function we'd like to call with our cron job
	function my_repeat_function() { //Cron Job Function Start

			
		$gTlthisAs 		= $this->settings['message_count_as'];
		$gTlthisAmnt 	= $this->settings['message_count_amount'];
		$gettotTSec 	= $this->settings['allwebThisSection'];

		if((int)$gTlthisAmnt > (int)$gettotTSec ): // Check if have message on Section


		/* Journey Functionality */

			//$jerney = $wpdb->get_row('SELECT * FROM '.$journeyTbl.' WHERE `id`='.$_POST['journey'].'', OBJECT);

			//$allEmails = $wpdb->get_results('SELECT `id`, `email`, `journey_count` FROM '.$entry_table.' WHERE journey_lastdate < DATE_SUB(CURDATE(), INTERVAL '.$jerney->j_time.' DAY) AND email != "" AND form_id = '.$_POST['form_id'].' GROUP BY email', OBJECT);	

			$allEmails = $this->wpdb->get_results('SELECT `id`, `form_id`, `email`, `vest_journey`, `journey_count`, `journey_lastdate` FROM '.$this->entry_table.' WHERE email != ""', OBJECT);	



			foreach($allEmails as $singM): //Main Foreach form_table
			$jerney = $this->wpdb->get_row('SELECT j.`id`, j.`j_time`, j.`j_rep_email`, j.`j_sender`, j.`j_unsubscribe`, j.`j_emails` FROM '.$this->journey_table.' j LEFT JOIN '.$this->form_table.' f ON j.id=f.journey LEFT JOIN '.$this->entry_table.' e ON e.form_id=f.row_id WHERE e.id='.$singM->id.' AND j.j_type="email"', OBJECT); 
			if(!$jerney && $singM->vest_journey != ''){
				$jerney = $this->wpdb->get_row('SELECT j.`id`, j.`j_time`, j.`j_rep_email`, j.`j_sender`, j.`j_unsubscribe`, j.`j_emails` FROM '.$this->journey_table.' j WHERE id='.$singM->vest_journey.' AND j.j_type="email"', OBJECT);
			} 
			if($jerney):

			$allEmailsJ = json_decode($jerney->j_emails); 


			$alltemplas 	= $allEmailsJ->j_emails; 
			$allSubjects 	= $allEmailsJ->j_subject;  
			$unsubcribesEms = ($jerney->j_unsubscribe)?json_decode($jerney->j_unsubscribe):array();
			/*echo '<pre>';
			print_r($unsubcribesEms);
			echo '</pre>';*/
			$specialTemp = '';
			$comDate = '';
			$exceptionalTime = '';
			$speCountTemp = '';
			$defaultTime = '';
			$timeUnit = '';

			$timeUnitFDB = (array)$allEmailsJ->time_unit;
			ksort($timeUnitFDB);
			$timeUnitFDB = implode(',', $timeUnitFDB);
			$timeUnitFDB = explode(',', $timeUnitFDB);
			
			for($jk=0; count($allEmailsJ->j_date) > $jk; $jk++){
				
				$jTime = ($timeUnitFDB[$jk] == 'minutes')?$allEmailsJ->j_time[$jk] * 60:$allEmailsJ->j_time[$jk];
				$jCount = (isset($singM->journey_count) && $singM->journey_count !='')?$singM->journey_count:0;


				//echo 'journey existing count: ' . $jCount . '<br/>';
				if($allEmailsJ->j_date[$jk] !=''){
					//echo 'under if <br/>';
					$now = date('Y-m-d'); // or your date as well
					$ex_date = strtotime($allEmailsJ->j_date[$jk]);

					$diff = abs(strtotime($now)-$ex_date);

					$years = floor($diff / (365*60*60*24));
					$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
					$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
					if($days < 2 ){
						$specialTemp = $jk;	
						$comDate = $allEmailsJ->j_date[$jk];		
					}
					//$datediff = $ex_date - $now;		

				}elseif($jTime > 0 && $jCount <= $jk && $timeUnitFDB[$jk] != '0'){ // get time from json field
					if(!$speCountTemp) $speCountTemp = $jk+1;	
					if(!$exceptionalTime) $exceptionalTime = $allEmailsJ->j_time[$jk];
					if(!$timeUnit) $timeUnit = $timeUnitFDB[$jk];
				}else{
					$defaultTime = $jerney->j_time;
				}
			}


			//foreach($allEmails as $se){
					$to 			= $singM->email;
					//$subject 		= $jerney->j_name;
					$defaultJCount 	= ($speCountTemp)?$speCountTemp-1:$singM->journey_count;
					

					$exSpDate = $this->wpdb->get_row('SELECT `j_fixed_dt` FROM '.$this->entry_table.' WHERE id='.$singM->id.' AND j_fixed_dt !=""', OBJECT);
					$newSpDATE = ($exSpDate)?json_decode($exSpDate->j_fixed_dt):array();

					$dCountTem = (count($alltemplas) == $defaultJCount)?$defaultJCount-1:$defaultJCount;	
					

					$subject 	= ((int)$specialTemp && !in_array($comDate, $newSpDATE))?stripslashes($allSubjects[(int)$specialTemp]):stripslashes($allSubjects[$dCountTem]);

					$subject = ($subject != '')?$subject:$jerney->j_name; // if subject empty get subject ferom journey name

					
					$message  	= ((int)$specialTemp && !in_array($comDate, $newSpDATE))?stripslashes($alltemplas[(int)$specialTemp]):stripslashes($alltemplas[$dCountTem]);


					preg_match_all("/\[([^\]]*)\]/", $message, $matches);
					foreach($matches[0] as $mk => $sm){
						$vFind = $matches[1][$mk];
						$getMatchFDB = $this->wpdb->get_row('SELECT `'.$matches[1][$mk].'` FROM '.$this->entry_table.' WHERE id='.$singM->id.'', OBJECT);
						$message = str_replace($sm, $getMatchFDB->$vFind, $message);
					}

					$permalink 	= get_permalink($_POST['assign_page']) . '?j_id='.$jerney->id.'&email=' . $singM->email;

					$message	 .= '<div style="text-align: center; overflow: hidden;line-height: 40px;"><a style="background: #4c4c4c; background: -moz-linear-gradient(top, #4c4c4c 0%, #595959 12%, #666666 25%, #474747 39%, #2c2c2c 49%, #000000 51%, #111111 60%, #2b2b2b 76%, #1c1c1c 91%, #131313 100%); background: -webkit-linear-gradient(top, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); background: linear-gradient(to bottom, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); color: #fff; text-decoration: none; font-size: 14px;  padding: 5px 10px; border-radius: 5px; line-height: 10px;" href="'.$permalink.'">Unsubscribe</a></div>';


					
						//$headers[0] 	= 'Content-Type: text/html; charset=UTF-8';
					 	$headers = '';
						if($jerney->j_sender !='' && $jerney->j_rep_email !=''){
							$name = $jerney->j_sender;
							$email = $jerney->j_rep_email;
							$headers .= "From: $name <$email>" . "\r\n";
						}

					
					
					
					//echo '$compTemplate: ' . $compTemplate . '<br/>';
					add_filter( 'wp_mail_content_type', array($this, 'wpdocs_set_html_mail_content_type') );
					if(!in_array($to, $unsubcribesEms) && count($alltemplas) > $singM->journey_count ){
						if(count($newSpDATE) > 0 && !in_array($comDate, $newSpDATE)){
							//echo 'if newSpDATE <br/>';

							$send = wp_mail( $to, $subject, $message, $headers);	
						}elseif($speCountTemp) { //Email send for each individual time
							//echo 'speCountTemp <br/>';
							
							if($timeUnit == 'minutes'){
								$minutesLeft  	= $exceptionalTime * 60; 
								$tergDate 	= date('Y-m-d H:i:s', strtotime($singM->journey_lastdate . ' + '.$minutesLeft.' minutes'));
							}else{
								$dayLeft  	= $exceptionalTime / 24; 
								$tergDate 	= date('Y-m-d', strtotime($singM->journey_lastdate . ' + '.$dayLeft.' days'));
							}

							if(strtotime($tergDate) <= strtotime(date('Y-m-d H:i:s'))){
								$send = wp_mail( $to, $subject, $message, $headers);	
							}	
						}else{
							//echo 'under ilseif else : <br/>';
								$dayLeft  	= $defaultTime / 24; 
								if($dayLeft < 1){
									$mntLeft = $dayLeft * 24;
									//$mntLeft = $mntLeft * 60;
									$tergDate 	= date('Y-m-d H:i:s', strtotime($singM->journey_lastdate . ' + '.$mntLeft.' minutes'));	
								}else{
									$tergDate 	= date('Y-m-d H:i:s', strtotime($singM->journey_lastdate . ' + '.$dayLeft.' days'));	
								}

								if(strtotime($tergDate) <= strtotime(date('Y-m-d H:i:s'))){
									
									$send = wp_mail( $to, $subject, $message, $headers);	
								}	
						}
						
						if(isset($send)){
							
							if((int)$specialTemp && !in_array($comDate, $newSpDATE)){
								if(!in_array($comDate, $newSpDATE)){
									array_push($newSpDATE, $comDate);
									$newSpDATE 	= array_unique($newSpDATE);
									$newSpDATEJ = json_encode($newSpDATE);
									$update = $this->wpdb->update(
										$this->entry_table,
										array(
											'j_fixed_dt' => $newSpDATEJ
										),
										array(
											'id' => $singM->id
										)
									);	
								}
							}

							else{
								
								$update = $this->wpdb->update(
									$this->entry_table,
									array(
										'journey_lastdate' => date('Y-m-d H:i:s'),
										'journey_count' => (int)$singM->journey_count + 1
									),
									array(
										'id' => $singM->id
									)
								);
							} // End if special else


														/*
							* Count to Option
							*/

							if(get_option( 'allwebThisSection' )){
								$newV = (int)$gettotTSec + 1;
								update_option( 'allwebThisSection', $newV, $autoload = null );	
							}else{
								add_option( 'allwebThisSection', $value = 1, $deprecated = '', $autoload = 'yes' );
							}


						} // End if $send

				} // end not in array
			remove_filter( 'wp_mail_content_type', array($this, 'wpdocs_set_html_mail_content_type') );
			//} // End for each
			endif; //if($jerney)
 			endforeach; // End Main Foreach Main Foreach form_table
 			endif; // End if have message on Section
		//End Journey Function
		
		// all brund query from database which fillup by form user



 		/*
 		* Brand Email
 		*/
		$ThisQrys = $this->wpdb->get_results('SELECT * FROM '.$this->entry_table.' WHERE email != "" AND brand != ""', OBJECT);
		
		if($ThisQrys):
		foreach($ThisQrys as $sQuery): // Main Loop
		$jBrans = ($sQuery->brand !='')?json_decode($sQuery->brand):array();

		$msGArray = array();
		if(is_array($jBrans)){
			foreach($jBrans as $sB){
				$gBM = $wpdb->get_row('SELECT `msg` FROM '.$this->brandsms.' WHERE brand_name="'.$sB.'"', OBJECT);
				if($gBM){
					$bArray = json_decode($gBM->msg);
					for($v=0; count($bArray) > $v; $v++) array_push($msGArray, $bArray[$v]);
				}
			}
		}
		if(is_array($jBrans)){
		foreach($jBrans as $mBMQ):
		$qry = $wpdb->get_row('SELECT `id`, `sendmsgcount`, `msgtype`, `brand_icon`, `msgduration` FROM '.$this->brandsms.' WHERE msgtype="email" AND `msgamount` > `sendmsgcount` AND brand_name="'.$mBMQ.'"', OBJECT);

		if($qry){

			// components for our email
            $nextDate = $this->tergateSMSDate($sQuery->smslastdate, $qry->msgduration);
            
            $today = date('Y-m-d');
            if($nextDate <= $today){

				$recepients = $sQuery->email;
				$subject = get_bloginfo('name') . ': '. $mBMQ;
				$message = stripslashes($msGArray[$sQuery->brandsms_count]);
				$headers = array('Content-Type: text/html; charset=UTF-8');

				preg_match_all("/\[([^\]]*)\]/", $message, $matches);
				foreach($matches[0] as $mk => $sm){
					$vFind = $matches[1][$mk];
					$getMatchFDB = $wpdb->get_row('SELECT `'.$matches[1][$mk].'` FROM '.$entry_table.' WHERE id='.$sQuery->id.'', OBJECT);
					$message = str_replace($sm, $getMatchFDB->$vFind, $message);
				}


				$permalink 	= get_home_url('/') . '?e_id='.$sQuery->id.'&unsbcr=1';

				$message .= '<div style="text-align: center; overflow: hidden;line-height: 40px;"><a style="background: #4c4c4c; background: -moz-linear-gradient(top, #4c4c4c 0%, #595959 12%, #666666 25%, #474747 39%, #2c2c2c 49%, #000000 51%, #111111 60%, #2b2b2b 76%, #1c1c1c 91%, #131313 100%); background: -webkit-linear-gradient(top, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); background: linear-gradient(to bottom, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); color: #fff; text-decoration: none; font-size: 14px;  padding: 5px 10px; border-radius: 5px; line-height: 10px;" href="'.$permalink.'">Unsubscribe</a></div>';
				
				// let's send it 
				if(isset($msGArray[$sQuery->brandsms_count])){
					
					//echo 'under email';
					$mail = wp_mail($recepients, $subject, $message, $headers);	
					if($mail){
						$nextCount = $sQuery->brandsms_count + 1;
						$updateEntry = $wpdb->update(
							$entry_table,
							array(
								'smslastdate' => date('Y-m-d'),
								'brandsms_count' => $nextCount
							),
							array(
								'id' => $sQuery->id
							)
						);


						          /*
					              * Count store to DB
					              */
					              
					              $newV = (int)$qry->sendmsgcount + 1;
					              $this->wpdb->update(
					                $this->brandsms,
					                array(
					                  'sendmsgcount' => $newV
					                ),
					                array(
					                  'id' => $qry->id
					                ),
					                array(
					                  '%d'
					                ),
					                array(
					                  '%d'
					                )
					              );

					}
				} // if(isset($msg[$sAction->brandsms_count]))

             } //End date compeare
		}
		endforeach;  //End foreach($jBrans as $mBMQ):
	}
	endforeach; //End Main Loop
	endif; // End if($ThisQrys)
	


	} // End Cron Job Function


	/*Function for cron test */
	function my_cron_schedules($schedules){
	    if(!isset($schedules["3min"])){
	        $schedules["3min"] = array(
	            'interval' => 3*60,
	            'display' => __('Once every 3 minutes'));
	    }
	    if(!isset($schedules["30min"])){
	        $schedules["30min"] = array(
	            'interval' => 30*60,
	            'display' => __('Once every 30 minutes'));
	    }
    	return $schedules;
	}


	/*
	* PUSH to Browser 
	* Emal SMS Unscribe
	*/
	function allwebPushNotification()
	{


		/*Email Unscribe*/
		if(isset($_GET['unsbcr']) && $_GET['unsbcr'] == true){
			$upUnsbcr = $this->wpdb->update(
				$this->entry_table,
				array(
					'sms_unsbe' => 'yes'
				),
				array(
					'id' => $_GET['e_id']
				)
			);
		}
		/*End Unscribe*/


		/*Start Journey SMS & PUHS Process*/
		/*
		* GET Settings
		*/
		$gTlthisAs 		= $this->settings['message_count_as'];
		$gTlthisAmnt 	= $this->settings['message_count_amount'];
		$gettotTSec 	= $this->settings['allwebThisSection'];




		if((int)$gTlthisAmnt > (int)$gettotTSec ): // Check if have message on Section
		
		$allEmails = $this->wpdb->get_results('SELECT `id`, `form_id`, `email`, `vest_journey`, `journey_count`, `journey_lastdate` FROM '.$this->entry_table.' WHERE email != "" AND ip != ""', OBJECT);	



			foreach($allEmails as $singM): //Main Foreach form_table
			$jerney = $this->wpdb->get_row('SELECT j.`id`, j.`j_type`, j.`j_name`, e.`mobile`,  j.`j_time`, j.`j_rep_email`, j.`j_sender`, j.`j_unsubscribe`, j.`j_emails` FROM '.$this->journey_table.' j LEFT JOIN '.$this->form_table.' f ON j.id=f.journey LEFT JOIN '.$this->entry_table.' e ON e.form_id=f.row_id WHERE e.id='.$singM->id.' AND j.j_type!="email" AND e.mobile !=""', OBJECT); 
			if(!$jerney && $singM->vest_journey != ''){
				$jerney = $this->wpdb->get_row('SELECT j.`id`, j.`j_type`, j.`j_name`, e.`mobile`, j.`j_time`, j.`j_rep_email`, j.`j_sender`, j.`j_unsubscribe`, j.`j_emails` FROM '.$this->journey_table.' j WHERE id='.$singM->vest_journey.' AND j.j_type!="email" AND e.mobile !=""', OBJECT);
			} 
			if($jerney):

			$allEmailsJ = json_decode($jerney->j_emails); 


			$alltemplas 	= $allEmailsJ->j_emails; 
			$allSubjects 	= $allEmailsJ->j_subject;  
			//$unsubcribesEms = ($jerney->j_unsubscribe)?json_decode($jerney->j_unsubscribe):array();
			/*echo '<pre>';
			print_r($unsubcribesEms);
			echo '</pre>';*/
			$specialTemp = '';
			$comDate = '';
			$exceptionalTime = '';
			$speCountTemp = '';
			$defaultTime = '';
			$timeUnit = '';

			$timeUnitFDB = (array)$allEmailsJ->time_unit;
			ksort($timeUnitFDB);
			$timeUnitFDB = implode(',', $timeUnitFDB);
			$timeUnitFDB = explode(',', $timeUnitFDB);
			
			for($jk=0; count($allEmailsJ->j_date) > $jk; $jk++){
				
				$jTime = ($timeUnitFDB[$jk] == 'minutes')?$allEmailsJ->j_time[$jk] * 60:$allEmailsJ->j_time[$jk];
				$jCount = (isset($singM->journey_count) && $singM->journey_count !='')?$singM->journey_count:0;


				//echo 'journey existing count: ' . $jCount . '<br/>';
				if($allEmailsJ->j_date[$jk] !=''){
					//echo 'under if <br/>';
					$now = date('Y-m-d'); // or your date as well
					$ex_date = strtotime($allEmailsJ->j_date[$jk]);

					$diff = abs(strtotime($now)-$ex_date);

					$years = floor($diff / (365*60*60*24));
					$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
					$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
					if($days < 2 ){
						$specialTemp = $jk;	
						$comDate = $allEmailsJ->j_date[$jk];		
					}
					//$datediff = $ex_date - $now;		

				}elseif($jTime > 0 && $jCount <= $jk && $timeUnitFDB[$jk] != '0'){ // get time from json field
					if(!$speCountTemp) $speCountTemp = $jk+1;	
					if(!$exceptionalTime) $exceptionalTime = $allEmailsJ->j_time[$jk];
					if(!$timeUnit) $timeUnit = $timeUnitFDB[$jk];
				}else{
					$defaultTime = $jerney->j_time;
				}
			}


			//foreach($allEmails as $se){
					$to 			= $singM->email;
					//$subject 		= $jerney->j_name;
					$defaultJCount 	= ($speCountTemp)?$speCountTemp-1:$singM->journey_count;
					

					$exSpDate = $this->wpdb->get_row('SELECT `j_fixed_dt` FROM '.$this->entry_table.' WHERE id='.$singM->id.' AND j_fixed_dt !=""', OBJECT);
					$newSpDATE = ($exSpDate)?json_decode($exSpDate->j_fixed_dt):array();

					$dCountTem = (count($alltemplas) == $defaultJCount)?$defaultJCount-1:$defaultJCount;	
					

					$subject 	= ((int)$specialTemp && !in_array($comDate, $newSpDATE))?stripslashes($allSubjects[(int)$specialTemp]):stripslashes($allSubjects[$dCountTem]);

					$subject = ($subject != '')?$subject:$jerney->j_name; // if subject empty get subject ferom journey name

					
					$message  	= ((int)$specialTemp && !in_array($comDate, $newSpDATE))?stripslashes($alltemplas[(int)$specialTemp]):stripslashes($alltemplas[$dCountTem]);


					preg_match_all("/\[([^\]]*)\]/", $message, $matches);
					foreach($matches[0] as $mk => $sm){
						$vFind = $matches[1][$mk];
						$getMatchFDB = $this->wpdb->get_row('SELECT `'.$matches[1][$mk].'` FROM '.$this->entry_table.' WHERE id='.$singM->id.'', OBJECT);
						$message = str_replace($sm, $getMatchFDB->$vFind, $message);
					}

					
						
					
					//unsubcribesEms
					
					//echo '$compTemplate: ' . $compTemplate . '<br/>';
					if(count($alltemplas) > $singM->journey_count ){
						$smsSend = false;
						if(count($newSpDATE) > 0 && !in_array($comDate, $newSpDATE)){
							//echo 'if newSpDATE <br/>';
							$smsSend = true;
						}elseif($speCountTemp) { //Email send for each individual time
							//echo 'speCountTemp <br/>';
							
							if($timeUnit == 'minutes'){
								$minutesLeft  	= $exceptionalTime * 60; 
								$tergDate 	= date('Y-m-d H:i:s', strtotime($singM->journey_lastdate . ' + '.$minutesLeft.' minutes'));
							}else{
								$dayLeft  	= $exceptionalTime / 24; 
								$tergDate 	= date('Y-m-d', strtotime($singM->journey_lastdate . ' + '.$dayLeft.' days'));
							}

							if(strtotime($tergDate) <= strtotime(date('Y-m-d H:i:s'))){
								//$send = wp_mail( $to, $subject, $message, $headers);	
								$smsSend = true;
							}	
						}else{
							//echo 'under ilseif else : <br/>';
								$dayLeft  	= $defaultTime / 24; 
								if($dayLeft < 1){
									$mntLeft = $dayLeft * 24;
									//$mntLeft = $mntLeft * 60;
									$tergDate 	= date('Y-m-d H:i:s', strtotime($singM->journey_lastdate . ' + '.$mntLeft.' minutes'));	
								}else{
									$tergDate 	= date('Y-m-d H:i:s', strtotime($singM->journey_lastdate . ' + '.$dayLeft.' days'));	
								}

								if(strtotime($tergDate) <= strtotime(date('Y-m-d H:i:s'))){
									//$send = wp_mail( $to, $subject, $message, $headers);
									$smsSend = true;	
								}	
						}
						
						if($smsSend == true){

							if($jerney->j_type == 'pushtobrowser'){	 
								 echo '<script>
									Push.create("'.$subject.'", {
								    body: "'.$message.'",
								    icon: "'.ALWEBURL.'/assets/img/push.png",
								    timeout: 90000,
								    onClick: function () {
								        window.focus();
								        this.close();
								    }
								});
								</script>';

							}elseif($jerney->j_type == 'sms' && $jerney->mobile != ''){					
								echo "<script>
								jQuery.ajax({
									type:'POST', 
						            dataType: 'json',
						            url: 'http://sms.calltopbx.co/api/v1/enviar.json',
						            data:
						            {
						                'envio[cliente]'   	: '18',
						                'envio[apikey]'    	: 'f9f74c3d9a728ea0e23156430a2eb58b',
						                'envio[telefono]' 	: '".$jerney->mobile."',
						                'envio[mensaje]' 	: '".$message."'
						            },success:function(data){
						            		console.log('Success sms');
						            }
								});
								</script>";
							}

							if((int)$specialTemp && !in_array($comDate, $newSpDATE)){
								if(!in_array($comDate, $newSpDATE)){
									array_push($newSpDATE, $comDate);
									$newSpDATE 	= array_unique($newSpDATE);
									$newSpDATEJ = json_encode($newSpDATE);
									$update = $this->wpdb->update(
										$this->entry_table,
										array(
											'j_fixed_dt' => $newSpDATEJ
										),
										array(
											'id' => $singM->id
										)
									);	
								}
							}

							else{
								
								$update = $this->wpdb->update(
									$this->entry_table,
									array(
										'journey_lastdate' => date('Y-m-d H:i:s'),
										'journey_count' => (int)$singM->journey_count + 1
									),
									array(
										'id' => $singM->id
									)
								);
							} // End if special else

							/*
							* Count to Option
							*/

							if(get_option( 'allwebThisSection' )){
								$newV = (int)$gettotTSec + 1;
								update_option( 'allwebThisSection', $newV, $autoload = null );	
							}else{
								add_option( 'allwebThisSection', $value = 1, $deprecated = '', $autoload = 'yes' );
							}

							


						} // End if $send

				} // end not in array
			//} // End for each
			endif; //if($jerney)
 			endforeach; // End Main Foreach

		/*End Journey SMS & PUSH Process*/
		endif; //End Check if have message on Section






		/*
		* Send Brand PUSH & SMS Message
		*/
		$ip=$_SERVER['REMOTE_ADDR'];

		$ThisIPQry = $this->wpdb->get_row('SELECT * FROM '.$this->entry_table.' WHERE ip="'.$ip.'"', OBJECT);
		$jBrans = ($ThisIPQry)?json_decode($ThisIPQry->brand):array();


		$msGArray = array();
		if(count($jBrans) > 0){
			foreach($jBrans as $sB){

				$gBM = $this->wpdb->get_row('SELECT `msg` FROM '.$this->brandsms.' WHERE brand_name="'.$sB.'"', OBJECT);
				
				if($gBM){
					$bArray = json_decode($gBM->msg);
					for($v=0; count($bArray) > $v; $v++) array_push($msGArray, $bArray[$v]);
				}
			}
		

		foreach($jBrans as $mBMQ):
		$qry = $this->wpdb->get_row('SELECT `id`, `sendmsgcount`, `msgtype`, `brand_icon`, `msgduration` FROM '.$this->brandsms.' WHERE (msgtype="pushtobrowser" OR msgtype="sms") AND `msgamount` > `sendmsgcount` AND brand_name="'.$mBMQ.'"', OBJECT);
		if($qry){

			// components for our email
            $nextDate = $this->tergateSMSDate($ThisIPQry->smslastdate, $qry->msgduration);
            //echo 'nextDate: ' . $nextDate . '<br/>';
            $today = date('Y-m-d');
             if($nextDate <= $today){
             	
             	

             	if(isset($msGArray[$ThisIPQry->brandsms_count])){
             	$message = $msGArray[$ThisIPQry->brandsms_count];
             	$message = str_replace('&nbsp;', ' ', $message);

             	preg_match_all("/\[([^\]]*)\]/", $message, $matches);
				foreach($matches[0] as $mk => $sm){
					$vFind = $matches[1][$mk];
					$getMatchFDB = $this->wpdb->get_row('SELECT `'.$matches[1][$mk].'` FROM '.$this->entry_table.' WHERE id='.$ThisIPQry->id.'', OBJECT);
					$message = str_replace("'", "", str_replace($sm, $getMatchFDB->$vFind, $message));
				}

				$icon = ($qry->brand_icon !='')?wp_get_attachment_url($qry->brand_icon):'';
				

				$qrTF = false;
				if($qry->msgtype == 'pushtobrowser' && count($msGArray) > $ThisIPQry->brandsms_count){	 
					//echo 'Under Pus to browser <br/> ';	
					$qrTF = true;
					 echo '<script>
						Push.create("'.$qry->brand.'", {
					    body: "'.$message.'",
					    icon: "'.$icon.'",
					    timeout: 90000,
					    onClick: function () {
					        window.focus();
					        this.close();
					    }
					});
					</script>';

				}elseif($qry->msgtype == 'sms' && $ThisIPQry->mobile != '' && count($msGArray) > $ThisIPQry->brandsms_count){

					$qrTF = true;				
					echo "<script>
					jQuery.ajax({
						type:'POST', 
			            dataType: 'json',
			            url: 'http://sms.calltopbx.co/api/v1/enviar.json',
			            data:
			            {
			                'envio[cliente]'   	: '18',
			                'envio[apikey]'    	: 'f9f74c3d9a728ea0e23156430a2eb58b',
			                'envio[telefono]' 	: '".$qry->mobile."',
			                'envio[mensaje]' 	: '".$message."'
			            },success:function(data){
			            		console.log('Success sms');
			            }
					});
					</script>";
				}
				} // if(isset($msGArray[$ThisIPQry->brandsms_count]))
				
				if(isset($msGArray[$ThisIPQry->brandsms_count]) && $qrTF == true){
							$nextCount = $ThisIPQry->brandsms_count + 1;
							
							$this->wpdb->update(
								$this->entry_table,
								array(
									'smslastdate' => date('Y-m-d'),
									'brandsms_count' => $nextCount
								),
								array(
									'id' => $ThisIPQry->id
								), 
								array(
									'%s',
									'%s'
								),
								array(
									'%d'
								)
							); 

							 /*
				              * Count store to DB
				              */
				              
				              $newV = (int)$qry->sendmsgcount + 1;
				              $this->wpdb->update(
				                $this->brandsms,
				                array(
				                  'sendmsgcount' => $newV
				                ),
				                array(
				                  'id' => $qry->id
				                ),
				                array(
				                  '%d'
				                ),
				                array(
				                  '%d'
				                )
				              );


				} // if(isset($msg[$sAction->brandsms_count]))

             } //End date compeare
		}
		endforeach;
		} // End if(count($jBrans) > 0)





		/*
		* Campaigns PUSH & SMS
		*/
		$allCampaigns = $this->wpdb->get_results('SELECT * FROM '.$this->tbl_subcampaign.' WHERE type!="email" AND action=1', OBJECT);

		foreach($allCampaigns as $sCamp){
			$allEmais 		= ($sCamp->nd_action != '')?json_decode($sCamp->nd_action):array();
			$alreadySends 	= ($sCamp->sms != '')?json_decode($sCamp->sms):array();
			$alreadySendP 	= ($sCamp->push != '')?json_decode($sCamp->push):array();

			if($sCamp->type == 'sms'){
				$sendEArray = array();
				for($e=0; count($allEmais) > $e; $e++){
					$getMob = $this->wpdb->get_row('SELECT `mobile` FROM '.$this->entry_table.' WHERE email="'.$allEmais[$e].'"', OBJECT);			
					if($getMob->mobile && !in_array($getMob->mobile, $alreadySends)){
						array_push($sendEArray, $getMob->mobile);
							echo "<script>
							jQuery.ajax({
								type:'POST', 
					            dataType: 'json',
					            url: 'http://sms.calltopbx.co/api/v1/enviar.json',
					            data:
					            {
					                'envio[cliente]'   	: '18',
					                'envio[apikey]'    	: 'f9f74c3d9a728ea0e23156430a2eb58b',
					                'envio[telefono]' 	: '".$getMob->mobile."',
					                'envio[mensaje]' 	: '".$sCamp->smspush."'
					            },success:function(data){
					            		console.log('Success sms');
					            }
							});
							</script>";		
					}
				}
				$newSendArray = array_merge($sendEArray, $alreadySends);
				$sendEJson = json_encode($newSendArray);
				$updateSms = $this->wpdb->update(
					$this->tbl_subcampaign,
					array(
						'sms' => $sendEJson
					), 
					array(
						'id' => $sCamp->id
					), 
					array('%s'),
					array('%d')
				);

			}else{ //Else if($sCamp->type == 'sms')
				$icon = ALWEBURL . 'assets/img/push.png';
				
				$seleEml = $this->wpdb->get_row('SELECT `email` FROM '.$this->entry_table.' WHERE ip="'.$ip.'"', OBJECT);
				if($seleEml && in_array($seleEml->email, $allEmais)) {
					if(!in_array($ip, $alreadySendP)){
						 echo '<script>
							Push.create("'.$sCamp->subject.'", {
						    body: "'.$sCamp->smspush.'",
						    icon: "'.$icon.'",
						    timeout: 900000,
						    onClick: function () {
						        window.focus();
						        this.close();
						    }
						});
						</script>';
						array_push($alreadySendP, $ip);
						$sendPJson = json_encode($alreadySendP);
						$updateSms = $this->wpdb->update(
								$this->tbl_subcampaign,
								array(
									'push' => $sendPJson
								), 
								array(
									'id' => $sCamp->id
								), 
								array('%s'),
								array('%d')
							);

					}
				}



			} //End if($sCamp->type == 'sms')
			
		} //End foreach($allCampaigns as $sCamp)



		
	} // End allwebPushNotification


	function allwebbox_upload_mimes($mimes = array()) {
		// Add a key and value for the CSV file type
		$mimes['csv'] = "text/csv";
		return $mimes;
	} 


	/**
	   * handle the settings field : no link creation
	*/
	function message_count_per() {
		$val = $this->settings['message_count_as'];
		$options = array('day', 'week', 'month');
	     $output = '<select name="message_count_as" id="message_count_as">';
	     foreach($options as $op){
	     	$selected = ($val == $op)?'selected':'';
	     	$output .= '<option '.$selected.' value="'.$op.'">'.ucfirst($op).'</option>';
	     }
	     $output .='</select>'; 
	     echo $output;
	}

	function message_count_count(){
		echo '<input type="number" min="0" name="message_count_amount" id="message_count_amount" value="'.$this->settings['message_count_amount'].'"/>';
	}

    function admin_section_tags_title() {
	      echo '<p><strong>' . __( 'Message / SMS Settings', 'allwebbox' ) . ':</strong></p><hr />';
    }


	function admin_page_message_settings(){
			add_settings_section( 'smart-marketing-message-settings', '', array( $this, 'admin_section_tags_title' ), 'socialunlock_settings_section_message' );
	      	register_setting( 'socialunlock_settings_message', 'message_count_as' );
	      	add_settings_field( 'swcc_unlock_settings_tags_nolinks', __( 'Select Message Session', 'allwebbox') . '' , array( $this, 'message_count_per' ), 'socialunlock_settings_section_message', 'smart-marketing-message-settings', array( 'label_for' => 'message_count_as' ) );

	      	register_setting( 'socialunlock_settings_message', 'message_count_amount' );
	      	add_settings_field( 'allwebbox_settings_message_count', __( 'Message per Session', 'allwebbox') . '' , array( $this, 'message_count_count' ), 'socialunlock_settings_section_message', 'smart-marketing-message-settings', array( 'label_for' => 'message_count_amount' ) );
	}

	/*
	* Configuration Page
	*/
	function configurationSmarMKT(){

	  $url = admin_url( 'admin.php?page=' . $_GET['page'] . '&tab=' );
      $current_tab = 'message';
      if ( isset( $_GET['tab'] ) ) {
        $current_tab = $_GET['tab'];
      }
      if ( ! in_array( $current_tab, array('message', 'api') ) ) {
        $current_tab = 'message';
      }
      ?>
      <div class="wrap">
        <h1 id="pp-plugin-info-social-share-unlock"><?php echo __('Smart Marketing Configuration', 'allwebbox'); ?><span></span></h1>
        <h2 class="nav-tab-wrapper" id="wp-social_share">
          <a href="<?php echo $url . 'message'; ?>" class="nav-tab<?php if ( 'message' == $current_tab ) { echo ' nav-tab-active'; } ?>"><?php _e( 'Message Settings' ); ?></a>
          <a href="<?php echo $url . 'api'; ?>" class="nav-tab<?php if ( 'api' == $current_tab ) { echo ' nav-tab-active'; } ?>"><?php _e( 'API' ); ?></a>
        </h2>
          <form method="post" action="options.php" id="pp-plugin-settings-hashtagger">
            <div class="postbox">
              <div class="inside">
                  <?php
                  settings_fields( 'socialunlock_settings_' . $current_tab );   
                  do_settings_sections( 'socialunlock_settings_section_' . $current_tab );
                  submit_button(); 
                 ?>
              </div>
            </div>
          </form>
      </div>
      <?php
	}


	/*
	* Email Content Type
	*/
	function wpdocs_set_html_mail_content_type() {
    	return 'text/html';
	}


	/*
	* Color convert
	*/
	function hex2rgba($color, $opacity = false) {
 
	$default = 'rgb(0,0,0)';
 
	//Return default if no color provided
	if(empty($color))
          return $default; 
 
	//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }
 
        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }
 
        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);
 
        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }
 
        //Return rgb(a) color string
        return $output;
	}





	/*
	* Store Campaign storeCampaign
	*/
	function storeCampaign(){
		$alldata = $_POST['formdata'];
		$camName = $_POST['campaign'];
		$exid 	 = (isset($_POST['exid']))?$_POST['exid']:'';
		$campID  = (isset($_POST['campID']))?$_POST['campID']:'';
		$obj 	 = (isset($_POST['obj']))?$_POST['obj']:'';
		$sub_obj = (isset($_POST['sub_obj']))?json_encode($_POST['sub_obj']):'';
		$allrdata = array();

		foreach($alldata as $sd){
			$allrdata[$sd['name']] = $sd['value']; 	
		}
		unset($allrdata['loadTemplate']); 

		//print_r($allrdata);

		$exitCam = $this->wpdb->get_row('SELECT `id` FROM '.$this->tbl_campaign.' WHERE cmp_name="'.$camName.'"', OBJECT);

		$cmpID = '';
		if($campID != ''){
			$update = $this->wpdb->update(
				$this->tbl_campaign, 
				array(
					'cmp_name' 	=> $camName,
					'obj' 		=> $obj,
					'sub_obj' 	=> $sub_obj
				),
				array('id' => (int)$campID),
				array('%s', '%s', '%s'),
				array('%d')
			);
			$cmpID = $campID;
			
		}elseif(!$exitCam){
			$insert = $this->wpdb->insert(
				$this->tbl_campaign, 
				array(
					'cmp_name'	=> $camName,
					'obj' 		=> $obj,
					'sub_obj' 	=> $sub_obj
				),
				array(
					'%s', 
					'%s',
					'%s'
				)
			);
			$cmpID = $this->wpdb->insert_id;
			echo $cmpID;
			
		}else{
			$cmpID = $exitCam->id;	
		}

		if($cmpID != ''){
			$allrdata['cid'] = $cmpID;
			if($exid != ''){
				$updateSub = $this->wpdb->update(
					$this->tbl_subcampaign,
					$allrdata,
					array('id' => (int)$exid),
					array(
						'%s',
						'%s',
						'%s', 
						'%s',
						'%s',
						'%s',
						'%d'
					),
					array('%d')
				);

				if($updateSub){
					echo 'update success';
				}else{
					echo 'update failed';
				}
			}else{
				$insertSub = $this->wpdb->insert(
					$this->tbl_subcampaign,
					$allrdata,
					array(
						'%s',
						'%s',
						'%s', 
						'%s',
						'%s',
						'%s',
						'%d'
					)
				);
			}
		}	


		die();
	} // End StoreCampaign



	function campaignDelete(){
		$id = (isset($_POST['id']))?$_POST['id']:'';
		if($id != ''){
			$delete = $this->wpdb->delete(
				$this->tbl_campaign,
				array('id' => (int)$id),
				array('%d')
			);
			
			if($delete){
				$sDelete = $this->wpdb->delete(
					$this->tbl_subcampaign,
					array('cid' => $id),
					array('%d')
				);
				echo 'success';
			}
		}
		die();
	} // End campaignDelete


	/*
	* subCampaignDelete
	*/
	function subCampaignDelete(){
		$id = (isset($_POST['id']))?$_POST['id']:'';
		if($id != ''){
			$delete = $this->wpdb->delete(
				$this->tbl_subcampaign,
				array('id' => (int)$id),
				array('%d')

			);
			if($delete){
				echo 'success';
			}
		}
		die();

	} // End subCampaignDelete







	/*
	* Update Campaign Title only
	*/
	function updateCampaignTitle(){
		$id 		= $_POST['id'];
		$title 		= $_POST['title'];
		$obj 		= $_POST['obj'];
		$sub_obj 	= (isset($_POST['sub_obj']))?$_POST['sub_obj']:array();
		$sub_objJS 	= json_encode($sub_obj);

		$update = $this->wpdb->update(
			$this->tbl_campaign,
			array(
				'cmp_name' 	=> $title,
				'obj' 		=> $obj,
				'sub_obj' 	=> $sub_objJS
			),
			array('id' => $id),
			array(
				'%s',
				'%d',
				'%s'
			),
			array('%d')
		);

		if($update){
			echo 'success';
		}

		die();
	}

	// End update Campaign title only


	/*
	* Campaign Email Sent
	*/
	function campaignEmailSent(){


		$id = $_POST['id'];
		$emails = $_POST['emails'];
		$js_emails = (count($_POST['emails']) > 0)?json_encode($emails):'';
		$sCampins = $this->wpdb->get_row('SELECT * FROM '.$this->tbl_subcampaign.' WHERE id='.$id.'', OBJECT);

		if($sCampins->type == 'email'){
		$to 		= array_unique($emails);
		$subject 	= $sCampins->subject;
		$message  	= stripslashes($sCampins->sc_content);
		$permalink 	= ($_POST['campainfor'] == 'new')?get_permalink($sCampins->landing):get_permalink($sCampins->landing) . '?el=1';
		
		if($_POST['campainfor'] && $sCampins->landing != '' ){
		$message	 .= '<div style="margin-top:30px;text-align: center; overflow: hidden;line-height: 40px;"><a style="background: #4c4c4c; background: -moz-linear-gradient(top, #4c4c4c 0%, #595959 12%, #666666 25%, #474747 39%, #2c2c2c 49%, #000000 51%, #111111 60%, #2b2b2b 76%, #1c1c1c 91%, #131313 100%); background: -webkit-linear-gradient(top, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); background: linear-gradient(to bottom, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); color: #fff; text-decoration: none; font-size: 18px;  padding: 15px; text-transform: uppercase; border-radius: 10px; line-height: 50px;" href="'.$permalink.'">Click for Start</a></div>';
		}
		$headers = array('Content-Type: text/html; charset=UTF-8');

		//add_filter( 'wp_mail_content_type', array($this, 'wpdocs_set_html_mail_content_type') );
		$send = wp_mail( $to, $subject, $message, $headers);
		//remove_filter( 'wp_mail_content_type', array($this, 'wpdocs_set_html_mail_content_type') );

			if($send){
				echo 'success';
			}
		}else{
			$updateAction = $this->wpdb->update(
				$this->tbl_subcampaign, 
				array(
					'action' 	=> 1,
					'nd_action' => $js_emails
				),
				array('id' => $id), 
				array('%d', '%s'),
				array('%d')
			);
		}
		
		die();
	}


	/*
	* Object Data Storage
	*/
	function storeObjective(){
		$objective_name 	= $_POST['objective_name'];
		$sub_obj 			= $_POST['sub_obj'];
		$exid 	 			= (isset($_POST['exid']))?$_POST['exid']:'';
		$campID  			= (isset($_POST['ObjID']))?$_POST['ObjID']:'';
		$ob_desc 			= (isset($_POST['ob_desc']))?$_POST['ob_desc']:'';
		$sub_desc 			= (isset($_POST['sub_desc']))?$_POST['sub_desc']:'';

		$exitObj = $this->wpdb->get_row('SELECT `id` FROM '.$this->tbl_objective.' WHERE objective_name="'.$objective_name.'"', OBJECT);

		// Insert Objective 
	
		
		$objID = '';
		if($campID != ''){
			$update = $this->wpdb->update(
				$this->tbl_campaign, 
				array('cmp_name' => $camName),
				array('id' => (int)$campID),
				array('%s'),
				array('%d')
			);
			$objID = $campID;
			
		}elseif(!$exitObj){
			$insert = $this->wpdb->insert(
						$this->tbl_objective,
						array(
							'objective_name' 	=> $objective_name,
							'ob_desc' 			=> $ob_desc
						),
						array('%s', '%s')
					);
			$objID = $this->wpdb->insert_id;
			echo $objID;
		}else{
			$update = $this->wpdb->update(
					$this->tbl_objective,
					array(
							'objective_name' 	=> $objective_name,
							'ob_desc' 			=> $ob_desc
					),
					array('id' => $exitObj->id),
					array('%s', '%s'),
					array('%d')

			);
			$objID = $exitObj->id;	
		}

		if($objID != ''){
			if($exid != ''){
				$updateSub = $this->wpdb->update(
					$this->tbl_subobjective,
					array(
						'sub_obj' 	=> $sub_obj,
						'sub_desc' 	=> $sub_desc
					),
					array('id' => (int)$exid),
					array('%s', '%s'),
					array('%d')
				);

				if($updateSub){
					echo 'update success';
				}else{
					echo 'update failed';
				}
			}else{
				$insertSub = $this->wpdb->insert(
					$this->tbl_subobjective,
					array(
						'oids' 		=> $objID,
			         	'sub_obj' 	=> $sub_obj,
			         	'sub_desc' 	=> $sub_desc
					),
					array(
						'%d',
						'%s',
						'%s'
					)	
				);
			}
		}
		die();
	} 




	/*
	* Delete Sub Campaign
	*/
	function subObjectiveDelete(){
		$id = $_POST['id'];
		if($id != ''){
			$delete = $this->wpdb->delete(
				$this->tbl_subobjective,
				array('id' => (int)$id),
				array('%d')
			);
			if($delete){
				echo 'success';
			}
		}
		die();
	}


	function objectiveDelete(){
		$id = $_POST['id'];
		if($id != ''){
			$delete = $this->wpdb->delete(
				$this->tbl_objective,
				array('id' => (int)$id),
				array('%d')
			);
			if($delete){
				$subDelete = $this->wpdb->delete(
					$this->tbl_subobjective,
					array('oids' => $id),
					array('%d')
				);
				echo 'success';
			}
		}
		die();
	}



	/*
	* Select Sub Objects for Campaign page
	*/
	function selectRltSubObject(){
		$id = $_POST['id'];
		$subObjcs = $this->wpdb->get_results('SELECT * FROM '.$this->tbl_subobjective.' WHERE oids='.$id.'');
		echo json_encode($subObjcs);
		die();
	}

	/*
	* Load Language File
	*/
	function allwebboxLanguageFile(){
		$plugin_rel_path = ALWEBURL . '/languages'; /* Relative to WP_PLUGIN_DIR */

		$plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages'; /* Relative to WP_PLUGIN_DIR */
		load_plugin_textdomain( 'allwebbox', false, $plugin_rel_path );
	}




	/*
	 * Change the text in the admin for my custom post type
	 * 
	**/
	function allwebbox_cpt_text_filter( $translated_text, $untranslated_text, $domain ) {
	  global $typenow;
	 // echo 'current language: ' . get_user_locale( get_current_user_id() ) . ' &nbsp;&nbsp;  ';
	  if( is_admin() && get_locale() == 'es_CO' )  {

	    //make the changes to the text
	    switch( $untranslated_text ) {
	         case 'sms & message settings':
	          $translated_text = __( 'sms y configuracion de mensajes','allwebbox' );
	        break;

	        case "All Brand's":
	          $translated_text = __( 'Todas las Marcas','allwebbox' );
	        break;

  			case "Basic Questions":
	          $translated_text = __( 'Preguntas Basicas','allwebbox' );
	        break;
			
			case "Custom Questions":
	          $translated_text = __( 'Preguntas Personalizadas','allwebbox' );
	        break;
			
			case "Saved Filter":
	          $translated_text = __( 'Filtros Guardados','allwebbox' );
	        break;
			
			case "Do you like to use this Filter in future? ":
	          $translated_text = __( '¿Te gustaría utilizar este filtro en el futuro?','allwebbox' );
	        break;
			
			case "Not Answered":
	          $translated_text = __( 'Sin respuesta','allwebbox' );
	        break;
			
			case "Identification Questions":
	          $translated_text = __( 'Preguntas de Identificación','allwebbox' );
	        break;
			
			case "Contact Information":
	          $translated_text = __( 'Información de Contacto','allwebbox' );
	        break;
			
			case "Profiling questions":
	          $translated_text = __( 'Preguntas de Perfil','allwebbox' );
	        break;

	        //add more items
	     }
	   }
	   return $translated_text;
	}



	/*
	* Load Existing Template 
	*/
	function loadTemplateFunction(){
		$val = (isset($_POST['val']))?$_POST['val']:'';
		$tmpData = $this->wpdb->get_row('SELECT `tmplate` FROM '.$this->template_table.' WHERE id='.$val.'');
		echo $tmpData->tmplate;
		die();
	}



} // End Class
} // End check class if existi or not 