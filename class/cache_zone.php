<?php

class CacheZone {

	public static function test() {
		$args = func_get_args();
		if (count($args) == 3) {
			$settings = array($args);
		} elseif (count($args) == 1 && is_array($args[0])) {
			$settings = $args[0];
		} else {
			return false;
		}

		$now = getdate();
		$wday = $now['wday'];
		$time = $now['hours'] * 3600 + $now['minutes'] * 60 + $now['seconds'];
		foreach ($settings as $setting) {
			if (substr($setting[0], $wday, 1) != 'y') {
				continue;
			}
			if ($time >= $setting[1] && $time <= $setting[2]) {
				return true;
			}
		}
		return false;
	}

	public static function run($enabled, $script, $cache_file, $expire = 86400) {
		if (false == $enabled) {
			include_once($script);
		} else {
			$t0 = microtime(true);

			$filename = '/cache_dir/' . $cache_file;
			$existed = file_exists($filename);
			if (true == $existed && time() - filemtime($filename) > $expire) {
				$existed = false;
			}

			if (true == $existed) {
				echo \file_get_contents($filename);

				$t1 = microtime(true);
				echo '<div style="background-color:#f0f0f0;line-height:24px;font-size:12px;color:#444;text-align:center">缓冲命中，耗时', number_format(($t1 - $t0) * 1000, 3), '毫秒</div>';
			} else {
				ob_start();
				include_once($script);
				$content = ob_get_contents();
				ob_end_clean();
				\file_put_contents($filename, $content, LOCK_EX);
				echo $content;

				$t1 = microtime(true);
				echo '<div style="background-color:#f0f0f0;line-height:24px;font-size:12px;color:#444;text-align:center">数据库直读，耗时', number_format(($t1 - $t0) * 1000, 3), '毫秒</div>';
			}
		}

		exit;
	}

}
