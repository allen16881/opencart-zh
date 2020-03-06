<?php
class ModelLocalisationZone extends Model {
	public function addZone($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "zone SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "', parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$this->cache->delete('zone');
		
		return $this->db->getLastId();
	}

	public function editZone($zone_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "zone SET status = '" . (int)$data['status'] . "', name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', country_id = '" . (int)$data['country_id'] . "', parent_id = '" . (int)$data['parent_id'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE zone_id = '" . (int)$zone_id . "'");

		$this->cache->delete('zone');
	}

	public function deleteZone($zone_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "zone WHERE parent_id = '" . (int)$zone_id . "'");

		$this->cache->delete('zone');
	}

	public function getZone($zone_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row;
	}

	public function getZones($data = array()) {
		$sql = "SELECT z.*, z.name, c.name AS country FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "country c ON (z.country_id = c.country_id) WHERE z.status != 9";

		if (isset($data['country_id']) && (int)$data['country_id'] > 0) {
			$sql .= " AND z.country_id = '" . (int)$this->db->escape($data['country_id']) . "'";
		}

		if (isset($data['parent_id'])) {
			$sql .= " AND z.parent_id = '" . (int)$this->db->escape($data['parent_id']) . "'";
		}

		$sort_data = array(
			'c.name',
			'z.name',
			'z.code',
			'z.sort_order'
		);

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order = " DESC";
		} else {
			$order = " ASC";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'] . $order . ", zone_id ASC";
		} else {
			$sql .= " ORDER BY c.name" . $order;
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

	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . (int)$country_id);

		if (!$zone_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND parent_id = '0' AND status = '1' ORDER BY sort_order DESC, name");

			$zone_data = $query->rows;

			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}

		return $zone_data;
	}

	public function getTotalZones() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone WHERE parent_id = '0'");

		return $query->row['total'];
	}

	public function getTotalZonesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND parent_id = '0'");

		return $query->row['total'];
	}

	public function getZonesByParentId($parent_id) {
		$zone_data = $this->cache->get('zone.p.' . (int)$parent_id);

		if (!$zone_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE parent_id = '" . (int)$parent_id . "' AND status = '1' ORDER BY sort_order DESC, name");

			$zone_data = $query->rows;

			$this->cache->set('zone.p.' . (int)$parent_id, $zone_data);
		}

		return $zone_data;
	}

	public function getTotalZonesByParentId($parent_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone WHERE parent_id = '" . (int)$parent_id . "'");

		return $query->row['total'];
	}
}