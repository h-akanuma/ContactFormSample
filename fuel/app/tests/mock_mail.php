<?php

// PHP の mail() 関数をオーバーライド
namespace Email;

function mail($to, $subject, $message, $additional_headers, $additional_parameters) {
	$data = array(
		'to' => $to,
		'subject' => $subject,
		'message' => $message,
		'additional_headers' => $additional_headers,
		'additional_parameters' => $additional_parameters,
	);
	
	\Config::set('_test.mail.data', $data);
	
	return true;
}