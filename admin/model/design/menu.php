<?php
class ModelDesignMenu extends Model {
	public function addMenu($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "menu SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "'");

		$menu_id = $this->db->getLastId();

		if (isset($data['menu_link'])) {
			foreach ($data['menu_link'] as $language_id => $value) {
				foreach ($value as $row_id => $menu_link) {
					$parent_id = isset($menu_link['parent_id']) ? (int)$menu_link['parent_id'] : 0;

					if ($parent_id == 0) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "menu_link SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($menu_link['title']) . "', link = '" .  $this->db->escape($menu_link['link']) . "', subtitle = '" .  $this->db->escape($menu_link['subtitle']) . "', extra = '" .  $this->db->escape($menu_link['extra']) . "', image = '" .  $this->db->escape($menu_link['image']) . "', sort_order = '" .  (int)$menu_link['sort_order'] . "', parent_id = '" .  (int)$menu_link['parent_id'] . "'");

						$value[$row_id]['insert_id'] = $this->db->getLastId();
					} else {
						if (isset($value[$parent_id]) && isset($value[$parent_id]['insert_id'])) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "menu_link SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($menu_link['title']) . "', link = '" .  $this->db->escape($menu_link['link']) . "', subtitle = '" .  $this->db->escape($menu_link['subtitle']) . "', extra = '" .  $this->db->escape($menu_link['extra']) . "', image = '" .  $this->db->escape($menu_link['image']) . "', sort_order = '" .  (int)$menu_link['sort_order'] . "', parent_id = '" .  (int)$value[$parent_id]['insert_id'] . "'");

							$value[$row_id]['insert_id'] = $this->db->getLastId();
						}
					}
				}
			}
		}

		return $menu_id;
	}

	public function editMenu($menu_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "menu SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "' WHERE menu_id = '" . (int)$menu_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "menu_link WHERE menu_id = '" . (int)$menu_id . "'");

		if (isset($data['menu_link'])) {
			foreach ($data['menu_link'] as $language_id => $value) {
				foreach ($value as $row_id => $menu_link) {
					$parent_id = isset($menu_link['parent_id']) ? (int)$menu_link['parent_id'] : 0;

					if ($parent_id == 0) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "menu_link SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($menu_link['title']) . "', link = '" .  $this->db->escape($menu_link['link']) . "', subtitle = '" .  $this->db->escape($menu_link['subtitle']) . "', extra = '" .  $this->db->escape($menu_link['extra']) . "', image = '" .  $this->db->escape($menu_link['image']) . "', sort_order = '" .  (int)$menu_link['sort_order'] . "', parent_id = '" .  (int)$menu_link['parent_id'] . "'");

						$value[$row_id]['insert_id'] = $this->db->getLastId();
					} else {
						if (isset($value[$parent_id]) && isset($value[$parent_id]['insert_id'])) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "menu_link SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($menu_link['title']) . "', link = '" .  $this->db->escape($menu_link['link']) . "', subtitle = '" .  $this->db->escape($menu_link['subtitle']) . "', extra = '" .  $this->db->escape($menu_link['extra']) . "', image = '" .  $this->db->escape($menu_link['image']) . "', sort_order = '" .  (int)$menu_link['sort_order'] . "', parent_id = '" .  (int)$value[$parent_id]['insert_id'] . "'");

							$value[$row_id]['insert_id'] = $this->db->getLastId();
						}
					}
				}
			}
		}
	}

	public function deleteMenu($menu_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "menu WHERE menu_id = '" . (int)$menu_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "menu_link WHERE menu_id = '" . (int)$menu_id . "'");
	}

	public function getMenu($menu_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "menu WHERE menu_id = '" . (int)$menu_id . "'");

		return $query->row;
	}

	public function getMenusList($data = array()) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "menu WHERE status = '1'");

		return $query->rows;

	}

	public function getMenus($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "menu";

		$sort_data = array(
			'name',
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getMenuLinks($menu_id) {
		$menu_link_data = array();

		$menu_link_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_link WHERE menu_id = '" . (int)$menu_id . "' AND parent_id = 0 ORDER BY sort_order ASC");

		foreach ($menu_link_query->rows as $menu_link) {
			$menu_link_data[$menu_link['language_id']][] = array(
				'menu_link_id' => $menu_link['menu_link_id'],
				'title'      => $menu_link['title'],
				'link'       => $menu_link['link'],
				'subtitle'   => $menu_link['subtitle'],
				'extra'      => $menu_link['extra'],
				'image'      => $menu_link['image'],
				'parent_id'  => $menu_link['parent_id'],
				'sort_order' => $menu_link['sort_order']
			);
		}

		return $menu_link_data;
	}

	public function getTotalMenus() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "menu");

		return $query->row['total'];
	}

	public function getMenuLinksByParentId($parent_id) {
		$menu_link_data = array();

		$menu_link_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_link WHERE parent_id = '" . (int)$parent_id . "' ORDER BY sort_order ASC");

		foreach ($menu_link_query->rows as $menu_link) {
			$menu_link_data[] = array(
				'menu_link_id' => $menu_link['menu_link_id'],
				'title'      => $menu_link['title'],
				'link'       => $menu_link['link'],
				'subtitle'   => $menu_link['subtitle'],
				'extra'      => $menu_link['extra'],
				'image'      => $menu_link['image'],
				'parent_id'  => $menu_link['parent_id'],
				'sort_order' => $menu_link['sort_order']
			);
		}

		return $menu_link_data;
	}
}
