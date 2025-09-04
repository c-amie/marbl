<?php
   /*
   Plugin Name: Marbl
   Plugin URI: http://www.c-amie.co.uk/marbl
   Description: Embeds a Multi-region Affiliate & Referral Buyers Link (Marbl) for Amazon and eBay. Embed a drop-list buyer link into the page at any point the shortcode is added. Page can include, none, 1 or 1000 different shortcodes. Written in pure JavaScript, with no external dependencies, tracking, privacy issues and no geo-location guesttimation - the user selects their preffered store from your Amazon Associates or eBay Partners list.
   Version: 0.9.23
   Author: C:Amie
   Author URI: http://www.c-amie.co.uk/
   License: GPLv2 or later.
   */

// Note: ShortCode Attributes must be lower case

	/*
	 * Usage:
	 *		[marbl
     *          type="amazon"	[amazon,ebay]				{Required, default=Amazon}
	 *			region=""		[ALL|<country code, CSV>]	{Optional, default=ALL}
	 *			search=""		[<freetext>]				{Optional}
	 *			asin=""			[<ASIN>]					{Optional}
	 *			isbn=""			[<ISBN>]					{Optional}
	 *			size=""			[16,24,32,48 (px)]			{Optional, default=16}
	 *			label=""		[<freetext>]				{Optional}
	 *			display=""		[IMAGE|TEXT|BOTH]			{Optional, default=IMAGE}
	 *			nofollow=""		[true|false]				[Optional, default=true]
	 *		]
	 *
	 * Examples:
	 *   [marbl type="amazon" region="UK,US,CA" search="Intel NUC" size="16"]
	 *   [marbl type="amazon" region="UK,US,CA" asin="B01N6SRT4H"]
	 *   [marbl type="amazon" region="UK,CA" isbn="0593077180" size="48"]
	 *   [marbl type="amazon" region="ALL" search="Intel NUC"]								// If there is a registered Amazon Associates ID / Country Code (or is 'null'), create a link for it in the output
	 *   [marbl type="amazon" search="Intel NUC"]											// Same as above, without the explicit all
	 *   [marbl type="amazon" asin="B01N6SRT4H" label="buy now on Amazon" display=TEXT]		// Write buy 'now on Amazon' as the trigger for the drop menu with no icon
	 *   [marbl type="amazon" region="UK,CA,ES" isbn="0593077180"]							// Assuming that ES doesn't have a Associates ID and isn't set to null in the Associates ID, this will force ES to display as a link
	 *   [marbl type="amazon" region="MX,FR,UK,IN" isbn="0593077180"]						// Writes them out in the order MX,FR,UK,IN
	 */
	class Marbl {
		
		protected $pluginPath;
		protected $pluginUrl;
		public static $Version = '0.9.23';
		
		public function __construct() {
			// Set plugin Path
			$this->pluginPath = dirname(__FILE__);

			// Set plugin URL
			$this->pluginUrl = rtrim(plugin_dir_url(__FILE__), '/');//plugins_url() . '/marbl';

			// Register shortcode hook
			add_shortcode('marbl', array($this, 'create_Link') );
			
			// Register Admin Settings
			add_action('admin_init', array($this, 'marbl_register_settings') );
			
			// Register Admin UI Settings Page
			add_action('admin_menu', array($this, 'marbl_link_register_options_page') );
			
			// Register required JavaScript & CSS
			add_action('wp_enqueue_scripts', array($this, 'marblLink_scriptsStyles') );
		}
		
		public function shortcode() {
			
		}

		public function create_Link( $atts ) {
			// Set Attribute Default Values (if not provided)
			$a = shortcode_atts( array(
				'type' => 'amazon',			// Values: amazon,ebay
				'regions' => 'ALL',			// All
				'search' => '',				// All
				'asin' => '',				// Amazon
				'isbn' => '',				// Amazon
				'item' => '',				// eBay: link to specific item
				'store' => '',				// eBay: Link to a specific eBay store front
				'size' => '16',				// All
				'label' => '',				// All
				'display' => 'IMAGE',		// All
				'campaign' => ''			// eBay: Track into a specific campaign ID
			), $atts );
			
			switch (strtolower($a['type'])) {
				case 'amazon':
					if (get_option('marbl_amazon_enabled')) {
						return $this->createLink_amazon($a['regions'], $a['label'], $a['search'], $a['asin'], $a['isbn'], $a['size'], $a['display']);
					} else {
						return;
					}
					break;
				case 'ebay':
					if (get_option('marbl_ebay_enabled')) {
						return $this->createLink_ebay($a['regions'], $a['label'], $a['search'], $a['item'], $a['store'], $a['size'], $a['display'], $a['campaign']);
					} else {
						return;
					}
					break;
				default:
					return;
					break;
			}
		}
	
		private function getCountryCodes($strType) {
			if ($strType === 'amazon') {
				return ['AE', 'AU', 'BE', 'BR', 'CA', 'CN', 'DE', 'ES', 'FR', 'IN', 'IT', 'JP', 'MX', 'NL', 'PL', 'SA', 'SE', 'SG', 'UK', 'US'];
			} elseif ($strType === 'ebay') {
				return ['AT', 'AU', 'BE', 'CA', 'CH', 'DE', 'ES', 'FR', 'GB', 'IE', 'IT', 'NL', 'PL', 'US'];
			}
		}

		private function getCountryName($strCountryCode) {
			switch ($strCountryCode) {
				case 'AE':	return 'United Arab Emirates';
				case 'AT':	return 'Austria';
				case 'AU':	return 'Australia';
				case 'BE':	return 'Belgium';
				case 'BR':	return 'Brazil';
				case 'CA':	return 'Canada';
				case 'CH':	return 'Switzerland';
				case 'CN':	return 'China';
				case 'DE':	return 'Germany';
				case 'ES':	return 'Spain';
				case 'FR':	return 'France';
				case 'GB':	return 'United Kingdom';
				case 'IE':	return 'Ireland';
				case 'IN':	return 'India';
				case 'IT':	return 'Italy';
				case 'JP':	return 'Japan';
				case 'MX':	return 'Mexico';
				case 'NL':	return 'Netherlands';
				case 'PL':	return 'Poland';
				case 'SA':	return 'Saudi Arabia';
				case 'SE':	return 'Sweden';
				case 'SG':	return 'Singapore';
				case 'UK':	return 'United Kingdom';
				case 'US':	return 'United States';
				
			}
		}

		/**
		 * Create Amazon Link
		 */
		private function createLink_amazon($strRegions, $strLabel, $strFreetext, $strAsin, $strIsbn, $strSize, $strDisplay) {
			if (!isset($strFreetext) || !isset($strAsin) || !isset($strIsbn)) {
				//die
				return 'You must provide a either an ASIN, ISBN or a Search String';
				//die();
			} else {
				$strRegions		= trim(str_replace(' ', '', $strRegions));
				$strLabel		= str_replace('&amp;quot;', '"', $strLabel);	// Search for quotation marks &quot; coming in (double escaped) from Wordpress's sanitisation and restore to "
				$strLabel		= trim($strLabel);					// Make safe to paste into JavaScript
				$strFreetext	= str_replace('&amp;quot;', '"', $strFreetext);
				$strFreetext	= trim($strFreetext);
				$strAsin		= trim($strAsin);
				$strIsbn		= trim($strIsbn);
				$iSize			= sprintf("%d", $strSize);						// Numeric or 0
				switch (strtoupper($strDisplay)) {
					case 'IMAGE':
						$strDisplay	= 1;		// Image Only
						break;
					case 'TEXT':
						$strDisplay	= 2;		// Text Only
						break;
					case 'BOTH':
						$strDisplay	= 3;		// Both Text and Image
						break;
					default:
						$strDisplay	= 1;		// Image Only
						break;
				}
				//filter_var($strDisplay, FILTER_VALIDATE_BOOLEAN)
				
				switch ($iSize) {
					case 16:
						$strSize = 16;
						break;
					case 24:
						$strSize = 24;
						break;
					case 32:
						$strSize = 32;
						break;
					case 48:
						$strSize = 48;
						break;
					default:
						$strSize = 16;
						break;
				}

				$arrCountryCodes = $this->getCountryCodes('amazon');
				$strAssociateIds = '';
				for ($i = 0; $i < count($arrCountryCodes); $i++) {
					if (get_option('marbl_amazon_link_associate_id_' . strtolower($arrCountryCodes[$i])) != '') {
						$strAssociateIds .= 'marblAmazon.addAssociateId(\'' . esc_js(strtoupper($arrCountryCodes[$i])) .'\', \'' . esc_js(get_option('marbl_amazon_link_associate_id_' . (strtolower($arrCountryCodes[$i])))) . '\');';
					}
				}

				if (get_option('marbl_general_custom_resources_path') != null) {
					$strAssetsPath = esc_url(get_option('marbl_general_custom_resources_path'));
				} else {
					$strAssetsPath = esc_url(plugin_dir_url( __FILE__ ));
				}

				$strDisclaimerCfg = '';
				if (get_option('marbl_amazon_show_disclaimer')) {
					$strDisclaimerCfg = 'marblAmazon.DisclaimerPosition = marblAmazon.' . ((get_option('marbl_amazon_disclaimer_position') === 'TOP') ? 'TOP' : 'BOTTOM') . ';';
					$strDisclaimerCfg .= 'marblAmazon.DisclaimerText = \'' . esc_js(get_option('marbl_amazon_disclaimer')) . '\';';
				}

				return 
				'<script type="text/javascript">
					var marblAmazon = new MarblAmazonLink(\'' . esc_url($strAssetsPath) . '\');
						' . $strAssociateIds . '
						marblAmazon.DisplayFlags = ' . ((get_option('marbl_general_link_show_flags')) ? 'true' : 'false') . ';
						marblAmazon.OpenInNewWindow = ' . ((get_option('marbl_general_link_open_new_window')) ? 'true' : 'false') . ';
						marblAmazon.LinksNoFollow = ' . ((get_option('marbl_general_link_link_nofollow')) ? 'true' : 'false') . ';
						' . $strDisclaimerCfg . '
						marblAmazon.createLink(\'' . (esc_js($strRegions)) . '\', \'' . (esc_js($strLabel)) .'\', \'' . (esc_js($strFreetext)) . '\', \'' . (esc_js($strAsin)) . '\', \'' . (esc_js($strIsbn)) . '\', ' . esc_js($strSize) . ', ' . esc_js($strDisplay) . ');
				</script>'; // Note: String parameters are not wrapped in " " because json_encode appends this itself
			}
		}


		/**
		 * Create eBay EPN Link
		 */
		private function createLink_eBay($strRegions, $strLabel, $strFreetext, $strItemId, $strStoreId, $strSize, $strDisplay, $strCampaign) {
			if (!isset($strFreetext) || !isset($strItemId) || !isset($strStoreId)) {
				//die
				return 'You must provide a either an Item Number, Store Link ID or a Search String';
				//die();
			} else {
				$strRegions		= trim(str_replace(' ', '', $strRegions));
				$strLabel		= str_replace('&amp;quot;', '"', $strLabel);	// Search for quotation marks &quot; coming in (double escaped) from Wordpress's sanitisation and restore to "
				$strLabel		= trim($strLabel);	// Make safe to paste into JavaScript
				$strFreetext	= str_replace('&amp;quot;', '"', $strFreetext);
				$strFreetext	= trim($strFreetext);
				$strItemId		= trim($strItemId);
				$strStoreId		= trim($strStoreId);
				$iSize			= sprintf("%d", $strSize);		// Numeric or 0
				switch (strtoupper($strDisplay)) {
					case 'IMAGE':
						$strDisplay	= 1;		// Image Only
						break;
					case 'TEXT':
						$strDisplay	= 2;		// Text Only
						break;
					case 'BOTH':
						$strDisplay	= 3;		// Both Text and Image
						break;
					default:
						$strDisplay	= 1;		// Image Only
						break;
				}
				//filter_var($strDisplay, FILTER_VALIDATE_BOOLEAN)
				
				switch ($iSize) {
					case 16:
						$strSize = 16;
						break;
					case 24:
						$strSize = 24;
						break;
					case 32:
						$strSize = 32;
						break;
					case 48:
						$strSize = 48;
						break;
					default:
						$strSize = 16;
						break;
				}

				// If there is a custom Campaign ID, use it, else fall back to the default one.
				if (isset($strCampaign) && ($strCampaign != '')) {
					$strCampaignId = 'marblEbay.CampaignId = \'' . esc_js($strCampaign) . '\';';
				} else {
					$strCampaignId = 'marblEbay.CampaignId = \'' . get_option('marbl_ebay_default_campaign_id') . '\';';
				}

				$arrCountryCodes = $this->getCountryCodes('ebay');
				$strPermittedRegions = '';
				foreach($arrCountryCodes as $strCountryCode) {
					if (get_option('marbl_ebay_region_' . strtolower($strCountryCode) . '_enabled')) {
						$strPermittedRegions .= 'marblEbay.addRegion(\'' . esc_js(strtoupper($strCountryCode)) .'\');';
					}
				}
				
				if (get_option('marbl_general_custom_resources_path') != null) {
					$strAssetsPath = esc_url(get_option('marbl_general_custom_resources_path'));
				} else {
					$strAssetsPath = esc_url(plugin_dir_url( __FILE__ ));
				}

				$strDisclaimerCfg = '';
				if (get_option('marbl_ebay_show_disclaimer')) {
					$strDisclaimerCfg = 'marblEbay.DisclaimerPosition = marblEbay.' . ((get_option('marbl_general_link_show_flags')) ? 'TOP' : 'BOTTOM') . ';';
					$strDisclaimerCfg .= 'marblEbay.DisclaimerText = \'' . esc_js(get_option('marbl_ebay_disclaimer')) . '\';';
				}

				return 
				'<script type="text/javascript">
					var marblEbay = new MarblEbayLink(\'' . esc_url($strAssetsPath) . '\');
						' . $strCampaignId . '
						' . $strPermittedRegions . '
						marblEbay.DisplayFlags = ' . ((get_option('marbl_general_link_show_flags')) ? 'true' : 'false') . ';
						marblEbay.OpenInNewWindow = ' . ((get_option('marbl_general_link_open_new_window')) ? 'true' : 'false') . ';
						marblEbay.LinksNoFollow = ' . ((get_option('marbl_general_link_link_nofollow')) ? 'true' : 'false') . ';
						' . $strDisclaimerCfg . '
						marblEbay.createLink(\'' . (esc_js($strRegions)) . '\', \'' . (esc_js($strLabel)) .'\', \'' . (esc_js($strFreetext)) . '\', \'' . (esc_js($strItemId)) . '\', \'' . (esc_js($strStoreId)) . '\', ' . (esc_js($strSize)) . ', ' . (esc_js($strDisplay)) . ');
				</script>'; // Note: String parameters are not wrapped in " " because json_encode appends this itself
			}
		}


		/**
		 * Include CSS and JS content.
		 */
		function marblLink_scriptsStyles() {
			if (get_option('marbl_general_include_default_css')) {
				wp_register_style(
					'marbl-css',
					plugin_dir_url( __FILE__ ) . 'css/main.css',
					NULL,
					self::$Version
				);
				wp_enqueue_style( 'marbl-css' );
			}

			wp_enqueue_script(
				'marbl-js',
				plugin_dir_url( __FILE__ ) . 'js/Marbl.js',
				NULL,
				self::$Version,
				array(
					'in_footer'  => false,
				)
			);

		}
//		add_action( 'wp_enqueue_scripts', 'marblLink_scriptsStyles' );

		/**
		 * Register Admin Settings Parms
		 */
		function marbl_register_settings() {

			// General
			add_option( 'marbl_general_link_open_new_window', true);
				register_setting( 'marbl_general_options_group', 'marbl_general_link_open_new_window', array($this, 'marbl_validate_boolean') ); // Parm 2 is a string callback function ref where the value is sanitised
			add_option( 'marbl_general_link_show_flags', true);
				register_setting( 'marbl_general_options_group', 'marbl_general_link_show_flags', array($this, 'marbl_validate_boolean') );
			add_option( 'marbl_general_link_link_nofollow', true);
				register_setting( 'marbl_general_options_group', 'marbl_general_link_link_nofollow', array($this, 'marbl_validate_boolean') );
			add_option( 'marbl_general_include_default_css', true);
				register_setting( 'marbl_general_options_group', 'marbl_general_include_default_css', array($this, 'marbl_validate_boolean') );
			add_option( 'marbl_general_custom_resources_path', null);
				register_setting( 'marbl_general_options_group', 'marbl_general_custom_resources_path', array($this, 'marbl_settings_general_resources_path_validate') );

			// Amazon
			add_option( 'marbl_amazon_enabled', false);
				register_setting( 'marbl_amazon_link_options_group', 'marbl_amazon_enabled', array($this, 'marbl_validate_boolean') );
			add_option( 'marbl_amazon_show_disclaimer', false);
				register_setting( 'marbl_amazon_link_options_group', 'marbl_amazon_show_disclaimer', array($this, 'marbl_validate_boolean') );
			add_option( 'marbl_amazon_disclaimer_position', false);
				register_setting( 'marbl_amazon_link_options_group', 'marbl_amazon_disclaimer_position', array($this, 'marbl_validate_string') );
			add_option( 'marbl_amazon_disclaimer', false);
				register_setting( 'marbl_amazon_link_options_group', 'marbl_amazon_disclaimer', array($this, 'marbl_validate_string') );
			$arrCountryCodes = $this->getCountryCodes('amazon');
			for ($i = 0; $i < count($arrCountryCodes); $i++) {
				add_option( 'marbl_amazon_link_associate_id_' . strtolower($arrCountryCodes[$i]), '');
					register_setting( 'marbl_amazon_link_options_group', 'marbl_amazon_link_associate_id_' . strtolower($arrCountryCodes[$i]), array($this, 'marbl_amazon_link_associate_id_validate') );
			}

			// eBay
			add_option( 'marbl_ebay_enabled', false);
				register_setting( 'marbl_ebay_link_options_group', 'marbl_ebay_enabled', array($this, 'marbl_validate_boolean') );
			add_option( 'marbl_ebay_default_campaign_id', '');
				register_setting( 'marbl_ebay_link_options_group', 'marbl_ebay_default_campaign_id', array($this, 'marbl_ebay_campaign_id_validate') );
			add_option( 'marbl_ebay_show_disclaimer', false);
				register_setting( 'marbl_ebay_link_options_group', 'marbl_ebay_show_disclaimer', array($this, 'marbl_validate_boolean') );
			add_option( 'marbl_ebay_disclaimer_position', false);
				register_setting( 'marbl_ebay_link_options_group', 'marbl_ebay_disclaimer_position', array($this, 'marbl_validate_string') );
			add_option( 'marbl_ebay_disclaimer', false);
				register_setting( 'marbl_ebay_link_options_group', 'marbl_ebay_disclaimer', array($this, 'marbl_validate_string') );
			
			$arrCountryCodes = $this->getCountryCodes('ebay');
			foreach($arrCountryCodes as $iVectorId => $strCountryCode) {
				add_option( 'marbl_ebay_region_' . strtolower($strCountryCode) . '_enabled', true);
					register_setting( 'marbl_ebay_link_options_group', 'marbl_ebay_region_' . strtolower($strCountryCode) . '_enabled', array($this, 'marbl_validate_boolean') );
			}
		}
//		add_action( 'admin_init', 'marbl_register_settings' );

		/* Settings Sanitisation and Validation */
		//https://codex.wordpress.org/Validating_Sanitizing_and_Escaping_User_Data
		public function marbl_settings_general_resources_path_validate($strInput) {
			$strInput = trim($strInput);
			if (strlen($strInput) == 0) {
				return null;
			} else {
				if (substr($strInput, -1) != '/') {
					$strInput .= '/';
				}
				return esc_url($strInput);
			}
		}

		public function marbl_amazon_link_associate_id_validate($strInput) {
			return esc_attr($strInput);
		}

		public function marbl_ebay_campaign_id_validate($strInput) {
			$strInput = trim($strInput);
			if (!is_numeric($strInput)) {
				return null;
			}

			if (strlen($strInput) > 10) {
				return substr($strInput, 0, 10);
			} else {
				return $strInput;	
			}
		}
		
		public function marbl_validate_boolean($strInput) {
			$iVal = intval($strInput);
			if ($iVal === 0 || $iVal === null) {
				return false;
			} else {
				return true;
			}
		}
		
		public function marbl_validate_string($strInput) {
			return htmlentities($strInput);
		}

		/**
		 * Register Admin Settings Page
		 */
		function marbl_link_register_options_page() {
			//               Page TTL       Nav Link Name                         Capability       Menu Slug     Callback Function
			add_options_page('Marbl Links', 'Marbl Links for Amazon &amp; eBay', 'manage_options', 'marbl_link', array($this, 'marbl_link_options_page'));
		}
//		add_action('admin_menu', 'marbl_link_register_options_page');

		function marbl_link_options_page() {
		  //content on page goes here
			?>

<div class="wrap">
  <h1>Multi-region Affiliate &amp; Referral Buyers Link (Marbl)</h1>
  <p>Multi-region Affiliate &amp; Referral Buyers Link creates a user-selectable drop list of your Amazon Affiliate store and eBay Partner Network accounts, allowing the user to select their preferred region.</p>
  <p>Marbl does not use IP geo-location, third party services and is strong on protecting your and your users privacy.</p>
  <div id="marbl_settings_container" style="display: flex; flex-direction: row; flex-wrap: no-wrap; justify-content: flex-start; align-tiems: stretch; background-color: #fcfcfc; padding: 0 16px 16px 8px; clear: both;">
    <div style="flex-grow: 0; min-width: 148px; width: 140px; margin-right: 8px; max-width: 140px; order: 0;">
      <ul id="marbl_settings_tabbar" style="margin-top: 8px;">
        <li id="marbl_settings_button_general" class="button button-primary" style="margin-bottom: 16px; margin-right: 8px; width: 96%;" tabindex="0" onclick="document.getElementById('marbl_settings_general').style.display = 'flex';document.getElementById('marbl_settings_amazon').style.display = 'none';document.getElementById('marbl_settings_ebay').style.display = 'none';document.getElementById('marbl_settings_button_general').classList.add('button-primary');document.getElementById('marbl_settings_button_amazon').classList.remove('button-primary');document.getElementById('marbl_settings_button_ebay').classList.remove('button-primary');"> General Settings</li>
        <li id="marbl_settings_button_amazon"  class="button" style="margin-bottom: 16px; margin-right: 8px; width: 96%;" tabindex="1" onclick="document.getElementById('marbl_settings_general').style.display = 'none';document.getElementById('marbl_settings_amazon').style.display = 'flex';document.getElementById('marbl_settings_ebay').style.display = 'none';document.getElementById('marbl_settings_button_general').classList.remove('button-primary');document.getElementById('marbl_settings_button_amazon').classList.add('button-primary');document.getElementById('marbl_settings_button_ebay').classList.remove('button-primary');"> Amazon Settings</li>
        <li id="marbl_settings_button_ebay"    class="button" style="margin-bottom: 16px; margin-right: 8px; width: 96%;" tabindex="2" onclick="document.getElementById('marbl_settings_general').style.display = 'none';document.getElementById('marbl_settings_amazon').style.display = 'none';document.getElementById('marbl_settings_ebay').style.display = 'flex';document.getElementById('marbl_settings_button_general').classList.remove('button-primary');document.getElementById('marbl_settings_button_amazon').classList.remove('button-primary');document.getElementById('marbl_settings_button_ebay').classList.add('button-primary');"> eBay Settings</li>
      </ul>
      <form action="https://www.paypal.com/donate" method="post" target="_top">
        <input type="hidden" name="business" value="ZGA5JPWAG4FYL" />
        <input type="hidden" name="no_recurring" value="0" />
        <input type="hidden" name="item_name" value="Donation for Marbl" />
        <input type="hidden" name="currency_code" value="GBP" />
        <div style="font-size: smaller; margin-top: 64px;">If you found this free plugin useful, please show your support via<br />
              <input type="submit" name="submit" value="Donate with PayPal" class="button button-primary">
              <a href='https://ko-fi.com/A0A6ZDDYZ' target='_blank' class="button button-primary" style="margin-top: 8px;">Donate with ko-fi.com</a>
        </div>
        </form>
    </div>
    <div id="marbl_settings_general" style="flex-grow: 2; order: 1;">
      <form method="post" action="options.php">
        <?php settings_fields( 'marbl_general_options_group' ); ?>
        <h1>General Settings</h1>
        <p>These settings control the functionality of the Marbl plugin.</p>
        <table>
          <tr>
            <td colspan="2"><h2>Behaviour</h2></td>
          </tr>
          <tr>
            <td valign="top" scope="row"><label for="marbl_general_link_open_new_window_true">Links open in a new tab/browser window</label></td>
            <td><label>
                <input type="radio" id="marbl_general_link_open_new_window_true" name="marbl_general_link_open_new_window" value="1"<?php echo (get_option('marbl_general_link_open_new_window')) ? ' checked="checked"' : ''; ?> />
                Yes</label>
              <label>
                <input type="radio" id="marbl_general_link_open_new_window_false" name="marbl_general_link_open_new_window" value="0"<?php echo (!get_option('marbl_general_link_open_new_window')) ? ' checked="checked"' : ''; ?> />
                No</label></td>
          </tr>
          <tr>
            <td valign="top" scope="row"><label for="marbl_general_link_show_flags_true">Add 'nofollow' to all links <div style="font-size: smaller;">(strongly recommended)</div></label></td>
            <td><label>
                <input type="radio" id="marbl_general_link_link_nofollow_true" name="marbl_general_link_link_nofollow" value="1"<?php echo (get_option('marbl_general_link_link_nofollow')) ? ' checked="checked"' : ''; ?> />
                Yes</label>
              <label>
                <input type="radio" id="marbl_general_link_link_nofollow_false" name="marbl_general_link_link_nofollow" value="0"<?php echo (!get_option('marbl_general_link_link_nofollow')) ? ' checked="checked"' : ''; ?> />
                No</label></td>
          </tr>
          <tr>
            <td colspan="2"><h2>Styling</h2></td>
          </tr>
          <tr>
            <td valign="top" scope="row"><label for="marbl_general_link_show_flags_true">Shows flags icons on dropdown</label></td>
            <td><label>
                <input type="radio" id="marbl_general_link_show_flags_true" name="marbl_general_link_show_flags" value="1"<?php echo (get_option('marbl_general_link_show_flags')) ? ' checked="checked"' : ''; ?> />
                Yes</label>
              <label>
                <input type="radio" id="marbl_general_link_show_flags_false" name="marbl_general_link_show_flags" value="0"<?php echo (!get_option('marbl_general_link_show_flags')) ? ' checked="checked"' : ''; ?> />
                No</label></td>
          </tr>
          <tr>
            <td valign="top" scope="row"><label for="marbl_general_include_default_css_true">Include default CSS StyleSheet</label></td>
            <td><label>
                <input type="radio" id="marbl_general_include_default_css_true" name="marbl_general_include_default_css" value="1"<?php echo (get_option('marbl_general_include_default_css')) ? ' checked="checked"' : ''; ?> />
                Yes</label>
              <label>
                <input type="radio" id="marbl_general_include_default_css_false" name="marbl_general_include_default_css" value="0"<?php echo (!get_option('marbl_general_include_default_css')) ? ' checked="checked"' : ''; ?> />
                No</label></td>
          </tr>
          <tr>
            <td valign="top">
              <label for="marbl_general_custom_styles_path">Path to custom style resources</label>
            </td>
            <td valign="top">
              <input type="text" id="marbl_general_custom_resources_path" name="marbl_general_custom_resources_path" value="<?php echo esc_attr(get_option('marbl_general_custom_resources_path')) ?>" style="width: 99%;" />
              <div style="font-size: smaller;"><strong>{Optional}</strong> This should be the root path containing ./css/main.css and ./images/&lt;custom_image_files&gt;</div>
              <div style="font-size: smaller;">It can be a relative path (/static/marbl/) or an absolute path (http://www.mysite.com/static/marbl/)</div>
              <div style="font-size: smaller;">See the <a href="https://www.c-amie.co.uk/marbl/guides/customising-the-look-of-marbl/">Marbl documentation</a> for more information on how to use custom styles and images.</div>
            </td>
          </tr>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <div id="marbl_settings_amazon" style="display: none; flex-grow: 2; order: 2;">
      <form method="post" action="options.php">
        <?php settings_fields( 'marbl_amazon_link_options_group' ); ?>
        <?php $arrCountryCodes = $this->getCountryCodes('amazon'); ?>
            <h1>Amazon Associates Settings</h1>
            <div style="display: flex;">
                <div style="width: 180px;">Enable Marbl for Amazon</div>
                <div style="flex-grow: 2;"><label>
                    <input type="radio" id="marbl_amazon_enabled_true" name="marbl_amazon_enabled" value="1"<?php echo (get_option('marbl_amazon_enabled')) ? ' checked="checked"' : ''; ?> />
                    Yes</label>
                  <label>
                    <input type="radio" id="marbl_amazon_enabled_false" name="marbl_amazon_enabled" value="0"<?php echo (!get_option('marbl_amazon_enabled')) ? ' checked="checked"' : ''; ?> />
                    No</label>
                    <?php if (!get_option('marbl_amazon_enabled')) { submit_button(); } ?>
                </div>
            </div>
        <div id="marbl_amazon_config_container" style="display:  <?php echo (get_option('marbl_amazon_enabled')) ? 'flex' : 'none'; ?>; width: 100%; align-content: stretch; justify-content: left;">
          <div style="width: 50%;">
            <h2>Settings</h2>
            <div style="display: flex;">
                <div style="width: 180px;">Include disclaimer</div>
                <div style="flex-grow: 2;"><label>
                    <input type="radio" id="marbl_amazon_show_disclaimer_true" name="marbl_amazon_show_disclaimer" value="1"<?php echo (get_option('marbl_amazon_show_disclaimer')) ? ' checked="checked"' : ''; ?> />
                    Yes</label>
                  <label>
                    <input type="radio" id="marbl_amazon_show_disclaimer_false" name="marbl_amazon_show_disclaimer" value="0"<?php echo (!get_option('marbl_amazon_show_disclaimer')) ? ' checked="checked"' : ''; ?> />
                    No</label>
                </div>
            </div>
			<?php if (get_option('marbl_amazon_show_disclaimer')) { ?>
            <div style="display: flex;">
                <div style="width: 180px;">Position</div>
                <div style="flex-grow: 2;"><label>
                    <div><select id="marbl_amazon_disclaimer_position" name="marbl_amazon_disclaimer_position">
                            <option value="TOP"<?php echo (get_option('marbl_amazon_disclaimer_position') === 'TOP') ? ' selected="selected"' : ''; ?>>Top</option>
                            <option value="BOTTOM"<?php echo (get_option('marbl_amazon_disclaimer_position') === 'BOTTOM') ? ' selected="selected"' : ''; ?>>Bottom</option>
                          </select></div>
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 180px;">Text</div>
                <div style="flex-grow: 2;">
                    <div><input type="text" id="marbl_amazon_disclaimer" name="marbl_amazon_disclaimer" value="<?php echo esc_attr(get_option('marbl_amazon_disclaimer')) ?>" placeholder="Enter your message here..." style="width: 99%;" /></div>
                </div>
            </div>
			<?php } ?>
            <h2>Your Associates ID's</h2>
            <p>Enter any Amazon Associate ID's that you wish to use on this site. If you do not have an associate ID for a particular country, leave it blank. If it is left blank, it will not appear in the dropdown list.</p>
            <p><strong>Note:</strong> If you do not have an Associate ID for a particular country, but you want the country to appear in the list anyway, enter 'null' (without quotes).</p>
            <table>
              <?php for ($i = 0; $i < count($arrCountryCodes); $i++) { ?>
              <tr valign="top">
                <td scope="row"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/' . strtoupper($arrCountryCodes[$i]) . '.gif'); ?>" alt="<?php echo esc_attr(strtoupper($arrCountryCodes[$i])); ?> Flag" title="<?php echo esc_attr(strtoupper($arrCountryCodes[$i])); ?> Flag" style="width: 16px;" />
                  <label for="marbl_amazon_link_associate_id_<?php echo esc_attr(strtolower($arrCountryCodes[$i])); ?>"><?php echo esc_html($this->getCountryName(strtoupper($arrCountryCodes[$i]))); ?></label></td>
                <td><input type="text" id="marbl_amazon_link_associate_id_<?php echo esc_attr(strtolower($arrCountryCodes[$i])); ?>" name="marbl_amazon_link_associate_id_<?php echo esc_attr(strtolower($arrCountryCodes[$i])); ?>" value="<?php echo esc_attr(get_option('marbl_amazon_link_associate_id_' . strtolower($arrCountryCodes[$i]))); ?>" /></td>
              </tr>
              <?php } ?>
            </table>
            <?php submit_button(); ?>
          </div>
          <div style="width: 50%;">
            <h2>WordPress Usage &amp; Examples</h2>
            <h3>Usage</h3>
            <code>[marbl<br />
            type="amazon"                                   {Required}<br />
            regions=""		[ALL|&lt;country code, CSV&gt;]	{Optional, default=ALL}<br />
            search=""		[&lt;freetext&gt;]				{Optional}<br />
            asin=""			[&lt;ASIN&gt;]					{Optional}<br />
            isbn=""			[&lt;ISBN&gt;]					{Optional}<br />
            size=""			[16,24,32,48 (px)]			{Optional, default=16}<br />
            label=""		[&lt;freetext&gt;]				{Optional}<br />
            display=""	[IMAGE|TEXT|BOTH]				{Optional, default=IMAGE}<br />
            ]</code>
            <h3>Examples</h3>
            <p>Create a link using all of your registered Associate ID's to a product search using the 16x16 icon</p>
            <code>[marbl type="amazon" search="Intel NUC"]</code>
            <p>Add UK, US and Canadian links using the 16x16 Amazon icon to a product search results page</p>
            <code>[marbl type="amazon" regions="UK,US,CA" search="Intel NUC" size="16"]</code>
            <p>As above but using an ASIN instead of a search phrase</p>
            <code>[marbl type="amazon" regions="UK,US,CA" asin="B01N6SRT4H"]</code>
            <p>Add UK and Canadian links using the 48x48 icon linking to an ISBN book product page</p>
            <code>[marbl type="amazon" regions="UK,CA" isbn="0593077180" size="48"]</code>
            <p>Add any registered country's Amazon Associates ID (or is 'null') in the default order</p>
            <code>[marbl type="amazon" regions="ALL" search="Intel NUC"]</code>
            <p>Write 'buy now on Amazon' as the trigger for the drop menu with no icon</p>
            <code>[marbl type="amazon" asin="B01N6SRT4H" label="buy now on Amazon" display=TEXT]</code>
            <p>Assuming that ES doesn't have a Associates ID and isn't set to null in the Associates ID, this will force ES to display as a link but with no referral code while UK and CA will display with their referral codes</p>
            <code>[marbl type="amazon" regions="UK,CA,ES" isbn="0593077180"]</code>
            <p>Writes them out in the order MX,FR,UK,IN</p>
            <code>[marbl type="amazon" regions="MX,FR,UK,IN" isbn="0593077180"]</code>
            <p>To include a quotation mark (&quot;) in the search or label field</p>
            <code>[marbl type="amazon" search="2&amp;quot; lead pipe" label="2&amp;quot; lead pipe"]</code>
          </div>
	      <div> <img src="<?php echo esc_url($this->pluginUrl); ?>/images/amazon-16x16.png" width="16" height="16" alt="Amazon 16x16 icon" /><br /><img src="<?php echo esc_url($this->pluginUrl); ?>/images/amazon-24x24.png" width="24" height="24" alt="Amazon 24x24 icon" /><br /><img src="<?php echo esc_url($this->pluginUrl); ?>/images/amazon-32x32.png" width="32" height="32" alt="Amazon 32x32 icon" /><br /><img src="<?php echo esc_url($this->pluginUrl); ?>/images/amazon-48x48.png" width="48" height="48" alt="Amazon 48x48 icon" /> </div>
        </div>
      </form>
    </div>
    <div id="marbl_settings_ebay" style="display: none; flex-grow: 2; order: 3;">
      <form method="post" action="options.php">
        <?php settings_fields( 'marbl_ebay_link_options_group' ); ?>
        <?php $arrCountryCodes = $this->getCountryCodes('ebay'); ?>
        <h1>eBay Partner Network (EPN) Settings</h1>
            <div style="display: flex;">
                <div style="width: 180px;">Enable Marbl for eBay</div>
                <div style="flex-grow: 2;"><label>
                <input type="radio" id="marbl_ebay_enabled_true" name="marbl_ebay_enabled" value="1"<?php echo (get_option('marbl_ebay_enabled')) ? ' checked="checked"' : ''; ?> />
                Yes</label>
              <label>
                <input type="radio" id="marbl_ebay_enabled_false" name="marbl_ebay_enabled" value="0"<?php echo (!get_option('marbl_ebay_enabled')) ? ' checked="checked"' : ''; ?> />
                No</label>
                <?php if (!get_option('marbl_ebay_enabled')) { submit_button(); } ?>
                </div>
            </div>
        <div id="marbl_ebay_config_container" style="display:  <?php echo (get_option('marbl_ebay_enabled')) ? 'flex' : 'none'; ?>; width: 100%; align-content: stretch; justify-content: left;">
          <div style="width: 50%;">
            <h2>Settings</h2>
            <div style="display: flex;">
                <div style="width: 180px;">Include disclaimer</div>
                <div style="flex-grow: 2;"><label>
                    <input type="radio" id="marbl_ebay_show_disclaimer_true" name="marbl_ebay_show_disclaimer" value="1"<?php echo (get_option('marbl_ebay_show_disclaimer')) ? ' checked="checked"' : ''; ?> />
                    Yes</label>
                  <label>
                    <input type="radio" id="marbl_ebay_show_disclaimer_false" name="marbl_ebay_show_disclaimer" value="0"<?php echo (!get_option('marbl_ebay_show_disclaimer')) ? ' checked="checked"' : ''; ?> />
                    No</label>
                </div>
            </div>
			<?php if (get_option('marbl_ebay_show_disclaimer')) { ?>
            <div style="display: flex;">
                <div style="width: 180px;">Position</div>
                <div style="flex-grow: 2;"><label>
                    <div><select id="marbl_ebay_disclaimer_position" name="marbl_ebay_disclaimer_position">
                            <option value="TOP"<?php echo (get_option('marbl_ebay_disclaimer_position') === 'TOP') ? ' selected="selected"' : ''; ?>>Top</option>
                            <option value="BOTTOM"<?php echo (get_option('marbl_ebay_disclaimer_position') === 'BOTTOM') ? ' selected="selected"' : ''; ?>>Bottom</option>
                          </select></div>
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 180px;">Text</div>
                <div style="flex-grow: 2;">
                    <div><input type="text" id="marbl_ebay_disclaimer" name="marbl_ebay_disclaimer" value="<?php echo esc_attr(get_option('marbl_ebay_disclaimer')) ?>" placeholder="Enter your message here..." style="width: 99%;" /></div>
                </div>
            </div>
			<?php } ?>
            <h3>Your Account</h3>
            <p>Enter your eBay Partner Network settings and preferences.</p>
            <p><strong>Note:</strong> You <em>must</em> provide a Default Campaign ID before using eBay Partner Network Marbl Links.</p>
            <table>
            <tr>
            <td valign="top">
            <label for="marbl_ebay_default_campaign_id">Default Campaign ID:</label>
            </td>
            <td valign="top">
            <input type="text" id="marbl_ebay_default_campaign_id" name="marbl_ebay_default_campaign_id" value="<?php echo esc_attr(get_option('marbl_ebay_default_campaign_id')); ?>" /><br />
            <small>This is a 10-digit numeric value that is accessed and managed via the <a href="https://epn.ebay.com/campaigns">Campaigns section of your EPN Portal</a>. A default compaign will have been created for you at registration.</small>
            </td>
            </tr>
            </table>
            <h3>Default Regions</h3>
            <table>
              <?php foreach($arrCountryCodes as $iVectorId => $strCountryCode) { ?>
              <tr valign="top">
                <td scope="row">
                <img src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/' . strtoupper($strCountryCode) . '.gif'); ?>" alt="<?php echo esc_attr($this->getCountryName(strtoupper($strCountryCode))); ?> Flag" title="<?php echo esc_attr($this->getCountryName(strtoupper($strCountryCode))); ?> Flag" style="width: 16px;" /> <?php echo esc_html($this->getCountryName(strtoupper($strCountryCode))); ?> 
                </td>
                <td><label>
                    <input type="radio" id="marbl_ebay_region_<?php echo esc_attr(strtolower($strCountryCode)); ?>_enabled_true" name="marbl_ebay_region_<?php echo esc_attr(strtolower($strCountryCode)); ?>_enabled" value="1"<?php echo (get_option('marbl_ebay_region_' . strtolower($strCountryCode) . '_enabled')) ? ' checked="checked"' : ''; ?> />
                    Show</label>
                  <label>
                    <input type="radio" id="marbl_ebay_region_<?php echo esc_attr(strtolower($strCountryCode)); ?>_enabled_false" name="marbl_ebay_region_<?php echo esc_attr(strtolower($strCountryCode)); ?>_enabled" value="0"<?php echo (!get_option('marbl_ebay_region_' . strtolower($strCountryCode) . '_enabled')) ? ' checked="checked"' : ''; ?> />
                    Hide</label></td>
              </tr>
              <?php } ?>
            </table>
            <?php submit_button(); ?>
          </div>
          <div style="width: 50%;">
            <h2>WordPress Usage &amp; Examples</h2>
            <h3>Usage</h3>
            <code>[marbl<br />
            type="ebay"                               	    {Required}<br />
            campaign=""		[&lt;EPN Campaign ID&gt;]		{Optional}<br />
            regions=""		[ALL|&lt;country code, CSV&gt;]	{Optional, default=ALL}<br />
            search=""		[&lt;freetext&gt;]				{Optional}<br />
            item=""			[&lt;Item #&gt;]				{Optional}<br />
            store=""		[&lt;Store ID&gt;]				{Optional}<br />
            size=""			[16,24,32,48 (px)]				{Optional, default=16}<br />
            label=""		[&lt;freetext&gt;]				{Optional}<br />
            display=""		[IMAGE|TEXT|BOTH]				{Optional, default=IMAGE}<br />
            ]</code>
            <h3>Examples</h3>
            <p>Create a link to a product search using the 16x16 icon</p>
            <code>[marbl type="ebay" search="Intel NUC"]</code>
            <p>Add UK, US and Canadian links using the 16x16 eBay icon to a product search results page</p>
            <code>[marbl type="ebay" regions="GB,US,CA" search="Intel NUC" size="16"]</code>
            <p>As above but using an item ID instead of a search phrase</p>
            <code>[marbl type="ebay" regions="GB,US,CA" item="1234567890"]</code>
            <p>Add UK and Canadian links using the 48x48 icon linking to an store page</p>
            <code>[marbl type="ebay" regions="GB,CA" store="myAmazingStoreId" size="48"]</code>
            <p>Add all available eBay regional stores in the default order</p>
            <code>[marbl type="ebay" regions="ALL" search="Intel NUC"]</code>
            <p>Write 'buy now on eBay' as the trigger for the drop menu with no icon which links to the stated eBay store ID</p>
            <code>[marbl type="ebay" store="myAmazingStoreId" label="buy now in my eBay store" display=TEXT]</code>
            <p>Writes them out in the order of US,IE,AT,IT</p>
            <code>[marbl type="ebay" regions="US,IE,AT,IT" search="Intel NUC"]</code>
            <p>To include a quotation mark (&quot;) in the search or label field</p>
            <code>[marbl type="ebay" search="2&amp;quot; lead pipe" label="2&amp;quot; lead pipe"]</code>
          </div>
	      <div> <img src="<?php echo esc_url($this->pluginUrl); ?>/images/eBay-16x16.png" width="16" height="16" alt="Amazon 16x16 icon" /><br /><img src="<?php echo esc_url($this->pluginUrl); ?>/images/eBay-24x24.png" width="24" height="24" alt="Amazon 24x24 icon" /><br /><img src="<?php echo esc_url($this->pluginUrl); ?>/images/eBay-32x32.png" width="32" height="32" alt="Amazon 32x32 icon" /><br /><img src="<?php echo esc_url($this->pluginUrl); ?>/images/eBay-48x48.png" width="48" height="48" alt="Amazon 48x48 icon" /> </div>
        </div>
      </form>

    </div>
  </div>
</div>
<?php
		}
	} // end class
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	// Instantiate and Go
	$marbl = new Marbl();
?>
