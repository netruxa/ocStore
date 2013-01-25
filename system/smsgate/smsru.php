<?php
final class SmsRu extends SmsGate {
/**
s-m-o-k@list.ru
http://10768.sms.ru/
*/
	public function send() {

		$results = array();
		$to = $this->to;
		if ($this->copy) {
			$to .= "," . $this->copy;
		}

		$params['api_id'] = $this->username;
		$params['to'] = $to;
		$params['text'] = $this->message;
		if (strlen($this->from) > 0) {
			$params['from'] = $this->from;
		}
		$params['partner_id'] = 10768;

		$url = 'http://sms.ru/sms/send';
		$post = http_build_query($params, '', '&');

		if (function_exists('curl_init')) {
		    $ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		    $response = curl_exec($ch);
		    curl_close($ch);
		} else {
		    $context = stream_context_create(array(
			'http' => array(
			    'method' => 'POST',
			    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			    'content' => $post,
			    'timeout' => 10,
			),
		    ));
		    $response = file_get_contents($url, false, $context);
		}

		return json_decode($response, true);
	}

}
?>