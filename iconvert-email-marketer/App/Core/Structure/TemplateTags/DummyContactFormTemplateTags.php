<?php

namespace  IconvertEmailMarketer\App\Core\Structure\TemplateTags;

if ( ! defined( 'ABSPATH' ) ) {exit;}

class DummyContactFormTemplateTags {

	public function getContactFormName() {
		return 'John Doe';
	}


	public function getContactFormEmail() {
		return 'example@example.com';
	}

	public function getContactFormSubject() {
		return 'Email subject sample';
	}
	public function getContactFormMessage() {
		return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam gravida, libero id dignissim tincidunt, dolor velit aliquam justo, quis vulputate purus nisl at felis. Nam a purus in lorem commodo cursus et vitae eros.';
	}
}
