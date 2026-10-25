<?php
if ( ! defined( 'ABSPATH' ) ) {exit;}
if ( ! function_exists( 'iconvertem_is_plugin_installed' ) ) {
	function iconvertem_is_plugin_installed() {
		return true;
	}
}

if ( ! function_exists( 'iconvertem_wp_terms_list' ) ) {
	function iconvertem_wp_terms_list( $postID, $taxonomy, $separator = ', ' ) {
		$term_obj_list = get_the_terms( $postID, $taxonomy );
		return implode( $separator, wp_list_pluck( $term_obj_list, 'slug' ) );
	}
}



if ( ! function_exists( 'iconvertem_minify_html' ) ) {
	function iconvertem_minify_html( $html ) {

		$html = preg_replace( '/[\s\r\n]+/m', ' ', $html );
		$html = str_replace( 'style=""', '', $html );
		$html = preg_replace( '/\s\s+/m', ' ', $html );

		$replace = array(
			//remove tabs before and after HTML tags
			'/\>[^\S ]+/s'               => '>',
			'/[^\S ]+\</s'               => '<',
			//shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
			'/([\t ])+/s'                => ' ',
			//remove leading and trailing spaces
			'/^([\t ])+/m'               => '',
			'/([\t ])+$/m'               => '',
			// remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
			'~//[a-zA-Z0-9 ]+$~m'        => '',
			//remove empty lines (sequence of line-end and white-space characters)
			'/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",
			//remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
			'/\>[\r\n\t ]+\</s'          => '><',
			//remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
			'/}[\r\n\t ]+/s'             => '}',
			'/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
			//remove new-line after JS's function or condition start; join with next line
			'/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
			'/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
			//remove new-line after JS's line end (only most obvious and safe cases)
			'/\),[\r\n\t ]+/s'           => '),',
			//remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
			// '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
		);
		$html = preg_replace( array_keys( $replace ), array_values( $replace ), $html );

		//remove optional ending tags (see http://www.w3.org/TR/html5/syntax.html#syntax-tag-omission )
		$remove = array(
			'</option>',
			'</li>',
			'</dt>',
			'</dd>',
			'</tr>',
			'</th>',
			'</td>',
		);
		$html   = str_ireplace( $remove, '', $html );

		return $html;
	}
}
