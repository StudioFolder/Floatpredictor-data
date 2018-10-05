<?php
/*
This script needs:
https://github.com/mailgun/mailgun-php
https://github.com/drewm/mailchimp-api
*/

require 'vendor/autoload.php';
include('./FPMailgunEmailSender.php');
include('./FPMailchimpSubscriber.php');
include_once '../database.php';

$svgFolder = '../svg/';
$jpgFolder = './jpg/';
$data = array();

$debug = false;

if($debug)
	$data = json_decode('{"email":"angelo@angelosemeraro.info", "departure_date":"02.10.2018","id":112,"text":"Departed from New York, US to Paris, France. Arrived within 898.7 km of Paris in 2.9 days. Travelled total 5950 km from fussel free borders.","name":"Angelo Semeraro"}',true);
else
	$data = json_decode(file_get_contents("php://input"), true);

$response = array();
$response['status'] = 'OK';
function svgToPNG($svgPath, $jpgPath) {
	global $response;
	$im = new Imagick();
	$svg = file_get_contents($svgPath);
	$svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'.$svg;
	$im->setBackgroundColor(new ImagickPixel('#1e1e1e'));
	$im->setResolution(800,800);
	$im->readImageBlob($svg);
	$im->scaleImage(400,400);
	$im->setImageFormat("jpeg");
	$im->writeImage($jpgPath);
	$im->clear();
	$im->destroy();
	return true;
}

function checkPostData($data){
	global $response;
	$valid = (isset($data['name']) && preg_match("/^[a-zA-Z ]*$/", $data['name']) &&
	   isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL) &&
	   isset($data['text']) && strlen($data['text'])>0 &&
	   isset($data['id']) && is_numeric($data['id']));
	if(!$valid) $response['status'] = "ERROR: Invalid POST parameters";
	return $valid;
}

function isIdValid($data){
	global $debug;
	if($debug) return true;
	global $response;

	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];// $_SERVER['REMOTE_ADDR'];
	// if($ip == null)
	//	$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; //$_SERVER['HTTP_CLIENT_IP'];
	// echo($ip);
	$trajectoryId = $data['id'];
	try{
	    $database = new Database();
	    $db = $database->getConnection();
			//  AND ip = ".$ip."
			// AND ip = ".strval($ip)."
	    $query = "UPDATE EXPLORERTRAJECTORIES SET email='sent' WHERE created > TIMESTAMP(DATE_SUB(NOW(),INTERVAL 10 MINUTE)) AND ip = '".strval($ip)."' AND email IS NULL AND id = ".$trajectoryId; //"SELECT
			// echo($query);
			$stmt = $db->prepare($query);
	    $result = $stmt->execute();
	    $affected = $stmt->rowCount();
	    // echo("Affected: ".$affected."\n");
			if(!$affected){
				$response['status'] = "ERROR: impossible to match the database path for ip ".strval($ip);
				return false;
			}
	  }catch(Exception $exception){
	   	$response['status'] = "ERROR: db exception. ".$e->getMessage();;
			return false;
	  }

	return true;
}

function mailChimpSubscribe($data){
	global $response;
	$response['mailchimp_status'] = FPMailchimpSubscriber::subscribe($data);
	if($response['mailchimp_status'] == 400) $response['mailchimp_status'] = "400. Email address already registered on MailChimp";
	return $response['mailchimp_status'] == 'subscribed';
}


function sendEmail($data, $jpgPath, $id){
	global $response;
	$response['mailgun_status'] = FPMailgunEmailSender::send($data, $jpgPath, $id);
}



try{
	if(checkPostData($data, $response) && isIdValid($data)){
		$svgPath = $svgFolder.strval($data['id']).'.svg';
		$jpgPath = $jpgFolder.strval($data['id']).'.jpg';
		// $jpgPath = $jpgFolder.'1.jpg';
		if(svgToPNG($svgPath, $jpgPath)){
			mailChimpSubscribe($data);
			sendEmail($data, $jpgPath, $data['id']);
		}
		unlink($jpgPath);
	}
} catch (Exception $e) {
    $response['status'] = "ERROR: ".$e->getMessage();
}
echo(json_encode($response));
?>
