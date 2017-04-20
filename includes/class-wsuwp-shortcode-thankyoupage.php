<?php

/**
 * plugin class file for the idonate_thankyoupage shortcode
 *
 * Defines the functions necessary to register our custom thank you page enclosing shortcode
 *
 * @link       https://github.com/washingtonstateuniversity/WSUWP-Plugin-iDonate
 * @since      0.0.20
 *
 * @package    WSUWP_Plugin_iDonate_ShortCode_ThankYouPage
 * @author     Blair Lierman
 */
class WSUWP_Plugin_iDonate_ShortCode_ThankYouPage {

	/**
	 * Initializes the shortcode by registering the hooks necessary
	 * for creating our shortcode within WordPress.
	 *
	 * @since 0.0.20
	 */
	public function init() {

		add_shortcode( 'idonate_thankyoupage', array( $this, 'thankyoupage_create_shortcode' ) );
	}
 
	 /**
	 * Defines the Thank You Page shortcode and applies the templating to the content
	 * Usage: [idonate_thankyoupage]Thank You Page Content[/idonate_thankyoupage]
	 *
	 * @since 0.0.20
	 */
	public function thankyoupage_create_shortcode( $atts, $content = null ) {
		$args = shortcode_atts( array(
			// 'server' => 'production',
		), $atts );

		$query_params = $this->parse_querystring();

		$content = '
		<img class="alignnone size-medium wp-image-565" src="https://hub.wsu.edu/foundation-sandbox/wp-content/uploads/sites/1540/2017/03/WSUFLogo-396x278.png" alt="" width="396" height="278" />
		<h3><strong>Donation Summary:</strong></h3>
		Donor number: {{donor.id}}
		Date: {{created}}
		Approval Code: {{transaction_id}}

		<strong>Billing Information</strong>
		<p style="white-space: pre-line;">
		{{donor.contact.firstname}} {{donor.contact.lastname}}
		{{donor.contact.address.street}}
		{{if donor.contact.address.street2}}{{donor.contact.address.street2}}{{end}}
		{{donor.contact.address.city}}, {{donor.contact.address.state}} {{donor.contact.address.zip}}
		{{donor.contact.address.country_name}}
		{{donor.contact.phone}}
		{{donor.contact.email}}
		</p>

		{{if comments}}Gift comments: {{comments}}{{end}}

		<p style="white-space: pre-line;">
			<strong><em>Management Information Systems Development Fund</em></strong>
			Amount: $10.00
			Date: {{created}}
			Frequency: Onetime
		</p>
		
		<p style="white-space: pre-line;">
		Total Amount Charged: $<span />{{value}}
		to your {{card_type}} with the last four {{last_four}}
		</p>

		<p style="white-space: pre-line;">
			<h5>WSU Foundation</h5>
			<em>255 E. Main Street, Suite 201</em>
			<em> PO Box 641927</em>
			<em> Pullman, WA 99164-1927</em>
			<em> Phone: 509-335-1686 or 800-GIV-2-WSU (448-2978)</em>
			<em> Fax: 509-335-5903</em>
			<em> E-mail: foundation@wsu.edu</em>
		</p>
		';

		$content = $this->replace_conditionals( $content, $query_params );

		foreach ( $query_params as $key => $value ) {
			$content = str_replace( '{{' . $key . '}}', "$value", $content );
		}

		return $content;
	}

	/**
	* Parses out the query string passed by iDonate and creates a list of params
	*
	* @since 0.0.20
	*
	* @return array $return_array
	**/
	private function parse_querystring() {
		$return_array = array();
		if ( ! empty( $_GET ) ) {

			$query_params = $this->array_flatten( $_GET );

			//loop over each category
			foreach ( $query_params as $key => $qp ) {
				// print_r( $key . ":" . $qp . "<br />" );
				$return_array[ $key ] = $qp;
			}
		}

		return $return_array;
	}

	/**
	* Flattens an array, but keep the naming structure by using . in the key name
	* Based on the example found at http://stackoverflow.com/a/14972714
	*
	* @param array $array The array to flatten
	* @param string $parent The name of the parent node(s), passed recursively
	*
	* @since 0.0.20
	*
	* @return array $return
	**/
	private function array_flatten( $array, $parent = '' ) {

		$parent = $parent . (empty( $parent ) ? '' : '.');

		$return = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$return = array_merge( $return, $this->array_flatten( $value, $parent . $key ) );
			} else { $return[ $parent . $key ] = $value; }
		}

		return $return;
	}

	/**
	* Checks for any conditionals
	* Template style based on https://developers.sparkpost.com/api/substitutions-reference.html#header-if-then-else-syntax
	* Matching based on https://github.com/slimndap/wp-theatre/blob/master/functions/template/wpt_template.php
	*
	* @param string $content The content from shortcode
	* @param array $query_params The flattened list of query parameters passed to the shortcode page
	*
	* @since 0.0.20
	*
	* @return string $content
	**/
	private function replace_conditionals( $content, $query_params ) {
		$tags = array();
		preg_match_all( '/{{if\s+(.*?)}}(.*?){{end}}/', $content, $tags, PREG_SET_ORDER );

		foreach ( $tags as $tag ) {
			$replace_content = ! empty( $query_params[ $tag[1] ] ) ? $tag[2]: '';
			$content = str_replace( $tag[0], $replace_content, $content );
		}

		return $content;
	}
}
