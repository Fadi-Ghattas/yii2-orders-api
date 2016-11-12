<?php

/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 11/12/2016
 * Time: 10:44 AM
 */

namespace common\Emails;

use common\helpers\Helpers;

class Emails
{


	public static function sendUserSingUpEmail()
	{
		$emailTemplate = file_get_contents(dirname(dirname(__FILE__)) . '//emails-templates//user-sing-up-email.html');

		$emailTemplate = str_replace('{home_url}', '#', $emailTemplate);
		$emailTemplate = str_replace('{logo_url}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/jommakan-icon.png' , $emailTemplate);
		$emailTemplate = str_replace('{back_ground}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/email-back-ground.png', $emailTemplate);
		$emailTemplate = str_replace('{heart}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/heart.png', $emailTemplate);

		$emailTemplate = str_replace('{facebook_icon}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/facebook-icon.png', $emailTemplate);
		$emailTemplate = str_replace('{facebook_url}', '#', $emailTemplate);
		$emailTemplate = str_replace('{google_icon}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/google-icon.png', $emailTemplate);
		$emailTemplate = str_replace('{google_url}', '#', $emailTemplate);
		$emailTemplate = str_replace('{google_play_icon}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/google-play-icon.png', $emailTemplate);
		$emailTemplate = str_replace('{google_play_url}', '#', $emailTemplate);
		$emailTemplate = str_replace('{instagram_icon}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/instagram-icon.png', $emailTemplate);
		$emailTemplate = str_replace('{instagram_url}', '#', $emailTemplate);
		$emailTemplate = str_replace('{twitter_icon}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/twitter-icon.png', $emailTemplate);
		$emailTemplate = str_replace('{twitter_url}', '#', $emailTemplate);
		$emailTemplate = str_replace('{whatsapp_icon}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/whatsapp-icon.png', $emailTemplate);
		$emailTemplate = str_replace('{whatsapp_url}', '#', $emailTemplate);
		$emailTemplate = str_replace('{apple_store_icon}', 'https://s3-ap-southeast-1.amazonaws.com/foodhunting.app.assets/apple-store-icon.png', $emailTemplate);
		$emailTemplate = str_replace('{apple_store_url}', '#', $emailTemplate);

		Helpers::sendMailgunEmail($emailTemplate);
	}
}