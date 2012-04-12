<?php
class User {
	private $user_id;
	private $username;
  	private $permission = array();

	// Данный костыль превращает адреса вида ::ffff:123.45.67.89
	// (возникающие при настройке сервера на использование одного IPv6-сокета
	// для обоих типов соединений и когда клиент использует IPv4) в обычные IPv4.
	// Возможно её можно как-то применить с меньшим оверхедом, так что
	// если кто знает — милости просим :)
	// @mva
	$_SERVER['REMOTE_ADDR']=str_replace("::ffff:","",$_SERVER['REMOTE_ADDR']);

  	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

    	if (isset($this->session->data['user_id'])) {
			$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "' AND status = '1'");

			if ($user_query->num_rows) {
				$this->user_id = $user_query->row['user_id'];
				$this->username = $user_query->row['username'];

      			$this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");

      			$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

	  			$permissions = unserialize($user_group_query->row['permission']);

				if (is_array($permissions)) {
	  				foreach ($permissions as $key => $value) {
	    				$this->permission[$key] = $value;
	  				}
				}
			} else {
				$this->logout();
			}
    	}
  	}

  	public function login($username, $password) {
    	$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND password = '" . $this->db->escape(md5($password)) . "' AND status = '1'");

    	if ($user_query->num_rows) {
			$this->session->data['user_id'] = $user_query->row['user_id'];

			$this->user_id = $user_query->row['user_id'];
			$this->username = $user_query->row['username'];

      		$user_group_query = $this->db->query("SELECT permission FROM " . DB_PREFIX . "user_group WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");

	  		$permissions = unserialize($user_group_query->row['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}

      		return true;
    	} else {
      		return false;
    	}
  	}

  	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
		$this->username = '';

		session_destroy();
  	}

  	public function hasPermission($key, $value) {
    	if (isset($this->permission[$key])) {
	  		return in_array($value, $this->permission[$key]);
		} else {
	  		return false;
		}
  	}

  	public function isLogged() {
    	return $this->user_id;
  	}

  	public function getId() {
    	return $this->user_id;
  	}

  	public function getUserName() {
    	return $this->username;
  	}
}
?>