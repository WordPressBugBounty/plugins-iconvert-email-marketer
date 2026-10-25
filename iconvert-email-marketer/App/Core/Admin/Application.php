<?php

namespace  IconvertEmailMarketer\App\Core\Admin;

use  IconvertEmailMarketer\App\Core\Structure\SendingOptions;

use  IconvertEmailMarketer\App\Core\Admin\Actions\Assets;
use  IconvertEmailMarketer\App\Core\Structure\EmailTemplateCPT;


use  IconvertEmailMarketer\App\Core\Admin\Actions\EditorActions;

use  IconvertEmailMarketer\App\Core\Structure\EmailTemplatePreview;
use  IconvertEmailMarketer\App\Core\Admin\Actions\EmailTemplateTags;

if ( ! defined( 'ABSPATH' ) ) {exit;}


// use  IconvertEmailMarketer\App\Core\Admin\Structure\Menus;


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

		$sending_options_class = apply_filters( 'iconvertem_sending_options_class', SendingOptions::class );
		new $sending_options_class();

		//load the assets
		new Assets();
		new EditorActions();
		new EmailTemplateTags();
		new EmailTemplatePreview();

	}
}
