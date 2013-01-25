<?php
final class MainSms extends SmsGate {
/**
s-m-o-k@list.ru
http://mainsms.ru/office/user/index_ref
*/
	public function send() {
		$results = array();
		$to = $this->to;
		if ($this->copy) {
			$to .= "," . $this->copy;
		}


		$params = array(
		    'recipients'    => $to,
		    'message'       => $this->message,
		    'sender'        => $this->from,
		);

		$params = $this->joinArrayValues($params);
		$sign = $this->generateSign($params);
		$params = array_merge(array('project' => $this->username), $params);

		$url = 'http://mainsms.ru/api/mainsms/message/send';
		$post = http_build_query(array_merge($params, array('sign' => $sign)), '', '&');

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

    protected function joinArrayValues($params)
    {
        $result = array();
        foreach ($params as $name => $value) {
            $result[$name] = is_array($value) ? join(',', $value) : $value;
        }
        return $result;
    }

    protected function generateSign(array $params) {
	    $params['project'] = $this->username;
	    ksort($params);
	    return md5(sha1(join(';', array_merge($params, Array($this->password)))));
    }


}
?>