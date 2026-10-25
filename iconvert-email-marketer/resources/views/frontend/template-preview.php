<?php

use IconvertEmailMarketer\App\Core\Frontend\MailContainerTableFrontendCss;
use IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateSettings;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$iconvertem_email_settings = EmailTemplateSettings::getInstance();
$iconvertem_email_campaign = $iconvertem_email_settings->get( 'email_campaign' );
$iconvertem_template_id    = $iconvertem_email_settings->get( 'template_id' );
$iconvertem_email_template = $iconvertem_email_settings->get( 'ic_email_template' );
if ( empty( $iconvertem_email_template ) ) {
	global $post;
	$iconvertem_email_template = $post;
}
if ( empty( $iconvertem_email_template ) || ! $iconvertem_email_template instanceof WP_Post || $iconvertem_email_template->post_type !== EmailTemplateCPT::POST_TYPE ) {
	return;
}


$iconvertem_use_real_data             = $iconvertem_email_settings->get( 'use_real_data', false );
$iconvertem_mail_container_table_css  = MailContainerTableFrontendCss::getCss( $iconvertem_email_template );
$iconvertem_has_email_container_block = str_contains( $iconvertem_email_template->post_content, 'extendstudio/mail-container' );

if ( $iconvertem_email_template->post_content == '' ) {
	return;
}

global $post;
$iconvertem_old_post = $post;
$post                = $iconvertem_email_template;
setup_postdata( $iconvertem_email_template );

ob_start();
?>
	<!DOCTYPE html>
	<html lang="en"
		  xmlns="http://www.w3.org/1999/xhtml"
		  xmlns:v="urn:schemas-microsoft-com:vml"
		  xmlns:o="urn:schemas-microsoft-com:office:office">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<style>

			<?php
				//This is the content of plugin css file so it does not need escaping
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo file_get_contents( ICONVERTEM_PATH . 'assets/css/mail-template.css' );

			?>



		</style>
		<style>
			<?php
			  //This is the content of plugin css file so it does not need escaping
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo file_get_contents( ICONVERTEM_PATH . 'assets/fonts/font-faces/css/style.css' );

			?>
		</style>
	</head>

	<body>
	<center>
	<?php

	if ( $iconvertem_has_email_container_block ) {
		the_content();
	}

	if ( ! $iconvertem_has_email_container_block ) :
		?>
		<table class="nl-container" width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation"
			   style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; <?php echo esc_attr( $iconvertem_mail_container_table_css ); ?>>
			<tbody>
			<tr>
				<td>
					<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
						   style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%">
						<tbody>
						<tr>
							<td>
								<?php
									the_content();
								?>
							</td>
						</tr>
						</tbody>
					</table>
				</td>

			</tr>
			</tbody>
		</table>
		<?php
	endif;
	?>
	</center>
	</body>

	</html>


<?php
$post = $iconvertem_old_post;
wp_reset_postdata();


$iconvertem_content = ob_get_clean();
$iconvertem_content = apply_filters( 'iconvertem_parse_content', $iconvertem_content, ! ! $iconvertem_use_real_data );

$iconvertem_content = iconvertem_minify_html( $iconvertem_content );
//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $iconvertem_content;
