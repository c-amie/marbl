<?php
/**
* Uninstall script
*
* Uninstall script for Marbl
*
* @since 0.9.3
*
* @package WordPress
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!defined( 'WP_UNINSTALL_PLUGIN' )) {
    exit ();
}
		function getCountryCodes($strType) {
			if ($strType === 'amazon') {
				return ['AU', 'BR', 'CA', 'CN', 'DE', 'ES', 'FR', 'IN', 'IT', 'JP', 'MX', 'UK', 'US'];
			} elseif ($strType === 'ebay') {
				return ['AT', 'AU', 'BE', 'CA', 'CH', 'DE', 'ES', 'FR', 'GB', 'IE', 'IT', 'NL', 'PL', 'US'];
			}
		}

	// Clean-up Amazon country code records
	$arrCountryCodes = getCountryCodes('amazon');
	for ($i = 0; $i < count($arrCountryCodes); $i++) {
		delete_option( 'marbl_amazon_link_associate_id_' . strtolower($arrCountryCodes[$i]) );
		delete_site_option( 'marbl_amazon_link_associate_id_' . strtolower($arrCountryCodes[$i]) );
	}

	// Clean-up eBay country code records
	$arrCountryCodes = getCountryCodes('ebay');
	for ($i = 0; $i < count($arrCountryCodes); $i++) {
		$strCountryCode = strtolower($arrCountryCodes[$i]);
		delete_option( 'marbl_ebay_region_' . $strCountryCode );
		delete_site_option( 'marbl_ebay_region_' . $strCountryCode );
		delete_option( 'marbl_ebay_region_' . $strCountryCode );
		delete_site_option( 'marbl_ebay_region_' . $strCountryCode );
		delete_option( 'marbl_ebay_region_' . $strCountryCode . '_enabled');
		delete_site_option( 'marbl_ebay_region_' . $strCountryCode . '_enabled' );
		delete_option( 'marbl_ebay_link_partner_id_' . $strCountryCode );
		delete_site_option( 'marbl_ebay_link_partner_id_' . $strCountryCode );
	}

	$arrOptions = [
		   'marbl_general_link_open_new_window',
		   'marbl_general_link_show_flags',
		   'marbl_general_link_link_nofollow',
		   'marbl_general_include_default_css',
		   'marbl_general_custom_resources_path',
		   'marbl_general_version',
		   'marbl_amazon_enabled',
		   'marbl_amazon_link_open_new_window',
		   'marbl_amazon_link_show_flags',
		   'marbl_amazon_include_default_css',
		   'marbl_amazon_link_link_nofollow',
		   'marbl_amazon_show_disclaimer',
		   'marbl_amazon_disclaimer_position',
		   'marbl_amazon_disclaimer',
		   'marbl_ebay_enabled',
		   'marbl_ebay_partner_id',
		   'marbl_ebay_default_campaign_id',
		   'marbl_ebay_show_disclaimer',
		   'marbl_ebay_disclaimer_position',
		   'marbl_ebay_disclaimer'
		];
	for ($i = 0; $i < count($arrOptions); $i++) {
		delete_option( $arrOptions[$i] );
		delete_site_option( $arrOptions[$i] );
	}
?>