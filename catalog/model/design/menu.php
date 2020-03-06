<?php
class ModelDesignMenu extends Model {
	public function getMenuLinks($menu_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_link ml ON (m.menu_id = ml.menu_id) WHERE m.menu_id = '" . (int)$menu_id . "' AND m.status = '1' AND ml.parent_id = 0 AND ml.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ml.sort_order ASC");
		return $query->rows;
	}

	public function getMenuLinksByParentId($parent_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_link WHERE parent_id = '" . (int)$parent_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}
}
