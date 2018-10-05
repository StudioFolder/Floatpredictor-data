<?php
require 'vendor/autoload.php';
require_once('./FPCredentials.php');
include('./MailChimp.php');
use \DrewM\MailChimp\MailChimp;

class  FPMailchimpSubscriber{
	public static function subscribe($data){
		$MailChimp = new MailChimp(FPCredentials::$mailchimp_apikey);
		$mailchimp_list_id = FPCredentials::$mailchimp_list_id;

		$result = $MailChimp->post("lists/".$mailchimp_list_id."/members",
			['email_address' => $data['email'],
			'status'        => 'subscribed',
			'merge_fields'        => [
					'FNAME' => $data['name'],
					'LNAME' => ''
				]
			]
	 	);
		 return $result['status'];
	 }
}
?>
