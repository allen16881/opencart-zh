<?php
class ModelUpgrade1002 extends Model {
	public function upgrade() {
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "cms_category' AND COLUMN_NAME = 'template'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "cms_category` ADD `template` VARCHAR(255) NOT NULL AFTER `status`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "cms_content' AND COLUMN_NAME = 'template'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "cms_content` ADD `template` VARCHAR(255) NOT NULL AFTER `status`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "category' AND COLUMN_NAME = 'template'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `template` VARCHAR(255) NOT NULL AFTER `status`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product' AND COLUMN_NAME = 'template'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `template` VARCHAR(255) NOT NULL AFTER `status`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "information' AND COLUMN_NAME = 'template'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "information` ADD `template` VARCHAR(255) NOT NULL AFTER `status`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "manufacturer' AND COLUMN_NAME = 'status'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "manufacturer` ADD `status` TINYINT(1) NOT NULL DEFAULT '1'");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "manufacturer' AND COLUMN_NAME = 'template'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "manufacturer` ADD `template` VARCHAR(255) NOT NULL AFTER `status`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "banner_image' AND COLUMN_NAME = 'subtitle'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "banner_image` ADD `subtitle` VARCHAR(255) NOT NULL AFTER `title`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "banner_image' AND COLUMN_NAME = 'text'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "banner_image` ADD `text` TEXT NOT NULL AFTER `subtitle`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "product_image' AND COLUMN_NAME = 'code'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_image` ADD `code` VARCHAR(64) NOT NULL AFTER `image`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "attribute' AND COLUMN_NAME = 'code'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "attribute` ADD `code` VARCHAR(64) NOT NULL AFTER `attribute_group_id`");
		}

		$file = DIR_OPENCART . 'admin/config.php';

		$lines = file($file);

		for ($i = 0; $i < count($lines); $i++) { 
			if ((strpos($lines[$i], 'DIR_TEMPLATE\'') !== false) && (strpos($lines[$i + 1], 'DIR_TEMPLATE_CATALOG') === false)) {
				array_splice($lines, $i + 1, 0, array('define(\'DIR_TEMPLATE_CATALOG\', DIR_MAIN . \'themes/\');' . "\n"));
			}
		}

		$output = implode('', $lines);
		
		$handle = fopen($file, 'w');

		fwrite($handle, $output);

		fclose($handle);
	}
}