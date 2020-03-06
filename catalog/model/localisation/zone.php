<?php
class ModelLocalisationZone extends Model {
	public function getZone($zone_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "' AND status = '1'");

		return $query->row;
	}

	public function getZonesByCountryId($country_id) {
		$zone_data = $this->cache->get('zone.' . (int)$country_id);

		if (!$zone_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND parent_id = '0' AND status = '1' ORDER BY sort_order DESC, zone_id ASC, name");

			$zone_data = $query->rows;

			$this->cache->set('zone.' . (int)$country_id, $zone_data);
		}

		return $zone_data;
	}

	public function getZonesByParentId($parent_id = 0) {
		$zone_data = $this->cache->get('zone.p.' . (int)$parent_id);

		if (!$zone_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE parent_id = '" . (int)$parent_id . "' AND status = '1' ORDER BY sort_order DESC, zone_id ASC, name");

			$zone_data = $query->rows;

			$this->cache->set('zone.p.' . (int)$parent_id, $zone_data);
		}

		return $zone_data;
	}
}