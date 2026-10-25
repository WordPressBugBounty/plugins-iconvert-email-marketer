<?php
namespace  IconvertEmailMarketer\App\Core\Structure;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class EmailTemplatePreview {
	public function __construct() {
		add_filter( 'template_include', array( $this, 'iconvertem_mail_template' ) );
	}

	public function iconvertem_mail_template( $template ) {
		global $post;
		$templateCPT = EmailTemplateCPT::getInstance();

		if ( is_object( $post ) && $post->post_type === $templateCPT->getSlug() ) {
			$template = $templateCPT->templatePath();
		}

		return $template;
	}
}
