<?php
class ModelInstallInstall extends Model {
	public function database($data) {
		$db = new DB($data['db_driver'], htmlspecialchars_decode($data['db_hostname']), htmlspecialchars_decode($data['db_username']), htmlspecialchars_decode($data['db_password']), htmlspecialchars_decode($data['db_database']), $data['db_port']);

		$file = DIR_APPLICATION . 'cnoc.sql';
		$demo_file = DIR_APPLICATION . 'demo.sql';

		if (!file_exists($file)) {
			exit('Could not load sql file: ' . $file);
		}

		$lines = file($file);

		if (isset($data['insert_demo']) && $data['insert_demo'] && file_exists($demo_file)) {
			$demo_lines = file($demo_file);

			$lines = array_merge($lines, $demo_lines);
		}

		if ($lines) {
			$sql = '';

			foreach($lines as $line) {
				if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
					$sql .= $line;

					if (preg_match('/;\s*$/', $line)) {
						$sql = str_replace("DROP TABLE IF EXISTS `cnoc_", "DROP TABLE IF EXISTS `" . $data['db_prefix'], $sql);
						$sql = str_replace("CREATE TABLE `cnoc_", "CREATE TABLE `" . $data['db_prefix'], $sql);
						$sql = str_replace("INSERT INTO `cnoc_", "INSERT INTO `" . $data['db_prefix'], $sql);

						$db->query($sql);

						$sql = '';
					}
				}
			}

			$db->query("SET CHARACTER SET utf8");

			$db->query("SET @@session.sql_mode = 'MYSQL40'");

			$db->query("DELETE FROM `" . $data['db_prefix'] . "user` WHERE user_id = '1'");

			$db->query("INSERT INTO `" . $data['db_prefix'] . "user` SET user_id = '1', user_group_id = '1', username = '" . $db->escape($data['username']) . "', salt = '" . $db->escape($salt = token(9)) . "', password = '" . $db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = '', lastname = '', fullname = '" . $db->escape($data['username']) . "', email = '" . $db->escape($data['email']) . "', status = '1', date_added = NOW()");

			$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_email'");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_email', value = '" . $db->escape($data['email']) . "'");

			$db->query("UPDATE `" . $data['db_prefix'] . "setting` SET value = '" . $db->escape($data['email']) . "' WHERE `key` = 'config_cs_email'");

			$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_encryption'");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_encryption', value = '" . $db->escape(token(1024)) . "'");

			$db->query("UPDATE `" . $data['db_prefix'] . "product` SET `viewed` = '0'");

			$db->query("INSERT INTO `" . $data['db_prefix'] . "api` SET username = '默认', `key` = '" . $db->escape(token(256)) . "', status = 1, date_added = NOW(), date_modified = NOW()");

			$api_id = $db->getLastId();

			$db->query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_api_id'");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `code` = 'config', `key` = 'config_api_id', value = '" . (int)$api_id . "'");
			
			// set the current years prefix
			$db->query("UPDATE `" . $data['db_prefix'] . "setting` SET `value` = 'INV-" . date('Y') . "-00' WHERE `key` = 'config_invoice_prefix'");
		}
	}
}
