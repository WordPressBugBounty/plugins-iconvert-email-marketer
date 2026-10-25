<?php

namespace  IconvertEmailMarketer\App\Core\Frontend;

use  IconvertEmailMarketer\App\Core\Admin\Actions\EditorActions;
use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;
use  IconvertEmailMarketer\App\Core\Structure\EmailTemplatePreview;
use  IconvertEmailMarketer\App\Core\Admin\Actions\EmailTemplateTags;

use  IconvertEmailMarketer\App\Core\Structure\SendingOptions;

if ( ! defined( 'ABSPATH' ) ) {exit;}


/**
 * Application
 */
class Application {


	/**
	 * Admin initialization
	 *
	 * @return void
	 */
	public static function boot() {
		new EmailTemplateCPT();

		//load the assets

		new EmailTemplateTags();

		$sending_options_class = apply_filters( 'iconvertem_sending_options_class', SendingOptions::class );
		new $sending_options_class();

		new EmailTemplatePreview();
		new EditorActions();

	}


}
