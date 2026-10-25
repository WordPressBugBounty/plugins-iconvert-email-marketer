<?php

namespace IconvertEmailMarketer\Settings;

use IconvertEmailMarketer\Core\Crypto;
use IconvertEmailMarketer\Core\DataHelper;
use IconvertEmailMarketer\Core\Singleton;
use IlluminateAgnostic\Arr\Support\Arr;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EmailProviderSettings  extends DataHelper {
	use Singleton;
	protected $settingPath = 'emailProvidersSettings';

	public function getFromName() {
		return $this->getProp( 'fromName', '' );
	}

	public function getFromEmail() {
		return $this->getProp( 'fromEmail', '' );
	}
	public function getSelectedProvider() {
		return $this->getProp( 'selectedProvider', '' );
	}
	public function getSmtpSettings() {
		$smtp = $this->getProp( 'optionsByProvider.smtp', array() );
		if ( isset( $smtp['password'] ) ) {
			$smtp['password'] = Crypto::decrypt( $smtp['password'] );
		}
		return $smtp;
	}

	public function getSendGridApiKey() {
		return  Crypto::decrypt( $this->getProp( 'optionsByProvider.sendGrid.apiKey', '' ) );
	}

	public function getMailJetApiKey() {
		return  Crypto::decrypt( $this->getProp( 'optionsByProvider.mailJet.apiKey', '' ) );
	}
	public function getMailJetSecretKey() {
		return  Crypto::decrypt( $this->getProp( 'optionsByProvider.mailJet.secretKey', '' ) );
	}
	public function getCampaignMonitorApiKey() {
		return  Crypto::decrypt( $this->getProp( 'optionsByProvider.campaignMonitor.apiKey', '' ) );
	}
	public function getCampaignMonitorClientId() {
		return  Crypto::decrypt( $this->getProp( 'optionsByProvider.campaignMonitor.clientId', '' ) );
	}

	public function getSendInBlueKey() {
		return  Crypto::decrypt( $this->getProp( 'optionsByProvider.send-in-blue.apiKey', '' ) );
	}
	public function getGetResponseBlueKey() {
		return  Crypto::decrypt( $this->getProp( 'optionsByProvider.get-response.apiKey', '' ) );
	}


	public static function getKeyPathsToEncrypt() {
		$base_path = 'emailProvidersSettings.props.optionsByProvider';
		return array(
			"$base_path.smtp.password",
			"$base_path.sendGrid.apiKey",
			"$base_path.mooSend.apiKey",
			"$base_path.mailJet.apiKey",
			"$base_path.mailJet.secretKey",
			"$base_path.campaignMonitor.apiKey",
			"$base_path.campaignMonitor.clientId",
			"$base_path.send-in-blue.apiKey",
			"$base_path.get-response.secretKey",
		);
	}
	public static function encryptApiKeysAndAndPasswordsFromPostArray( $data ) {
		$paths = static::getKeyPathsToEncrypt();
		foreach ( $paths as $path ) {
			$value = Arr::get( $data, $path );
			if ( empty( $value ) ) {
				continue;
			}
			$crypted_value = Crypto::encrypt( $value );
			Arr::set( $data, $path, $crypted_value );
		}

		return $data;
	}

	public static  function decryptApiKeysAndAndPasswordsFromPostArray( $data ) {
		$paths = static::getKeyPathsToEncrypt();
		foreach ( $paths as $path ) {
			$value = Arr::get( $data, $path );
			if ( empty( $value ) ) {
				continue;
			}
			$decrypted_value = Crypto::decrypt( $value );
			Arr::set( $data, $path, $decrypted_value );
		}
		return $data;
	}
}
