<?php
class Cache {
	private $expire = 3600;

	public function __construct() {
		if ((defined("CACHE_DRIVER") && CACHE_DRIVER == "memcache") && $this->ismemcache) {
			return;
		} else {
		$files = glob(DIR_CACHE . 'cache.*');

		if ($files) {
			foreach ($files as $file) {
				$time = substr(strrchr($file, '.'), 1);

				if ($time < time()) {
					if (file_exists($file)) {
						unlink($file);
					}
				}
			}
		}
		} // if memcache
	}

	public function get($key) {
		if ((defined("CACHE_DRIVER") && CACHE_DRIVER == "memcache") && $this->ismemcache) {
			return($this->memcache->get(MEMCACHE_NAMESPACE . $key, 0));
		} else {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			$handle = fopen($files[0], 'r');

			$cache = fread($handle, filesize($files[0]));

			fclose($handle);

			return unserialize($cache);
		}
		} // if memcache
	}

	public function set($key, $value) {
		if ((defined("CACHE_DRIVER") && CACHE_DRIVER == "memcache") && $this->ismemcache) {
			$this->memcache->set(MEMCACHE_NAMESPACE . $key, $value, 0, $this->expire);
		} else {
		$this->delete($key);

		$file = DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $this->expire);

		$handle = fopen($file, 'w');

		fwrite($handle, serialize($value));

		fclose($handle);
		} // if memcache
	}

	public function delete($key) {
		if ((defined("CACHE_DRIVER") && CACHE_DRIVER == "memcache") && $this->ismemcache) {
			$this->memcache->delete(MEMCACHE_NAMESPACE . $key);
		} else {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
		} // if memcache
	}
}
?>
