<?php
class ModelUpgrade1003 extends Model {
	public function upgrade() {
		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "comment' AND COLUMN_NAME = 'product_id'");

		if ($query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "comment` CHANGE `product_id` `content_id` INT(11) NOT NUL");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "comment' AND COLUMN_NAME = 'content_type'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "comment` ADD `content_type` INT(3) NOT NULL DEFAULT '1' AFTER `content_id`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "review' AND COLUMN_NAME = 'featured'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD `featured` TINYINT(1) NOT NULL DEFAULT '0' AFTER `rating`");
		}

		$query = $this->db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "customer' AND COLUMN_NAME = 'avatar'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `avatar` VARCHAR(255) NOT NULL AFTER `username`");
		}
	}
}