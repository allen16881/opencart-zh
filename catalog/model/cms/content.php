<?php
class ModelCmsContent extends Model {
	public function getContent($content_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "cms_content c LEFT JOIN " . DB_PREFIX . "cms_content_description cd ON (c.content_id = cd.content_id) LEFT JOIN " . DB_PREFIX . "cms_content_to_store c2s ON (c.content_id = c2s.content_id) WHERE c.content_id = '" . (int)$content_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row;
	}

	public function getContents($data = array()) {
		$sql = "SELECT c.*, cd.*";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "cms_category_path cp LEFT JOIN " . DB_PREFIX . "cms_content_to_category c2c ON (cp.category_id = c2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "cms_content_to_category c2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "cms_content_filter cf ON (c2c.content_id = cf.content_id) LEFT JOIN " . DB_PREFIX . "cms_content c ON (cf.content_id = c.content_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "cms_content c ON (c2c.content_id = c.content_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "cms_content c";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "cms_content_description cd ON (c.content_id = cd.content_id) LEFT JOIN " . DB_PREFIX . "cms_content_to_store c2s ON (c.content_id = c2s.content_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = '1' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND c2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND cf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_title']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_title'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_title'])));

				foreach ($words as $word) {
					$implode[] = "cd.title LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_content'])) {
					$sql .= " OR cd.content LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
				}
			}

			if (!empty($data['filter_title']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "cd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

		if (!empty($data['filter_bottom'])) {
			$sql .= " AND c.bottom = 1";
		}

		$sql .= " GROUP BY c.content_id";

		$sort_data = array(
			'cd.title',
			'c.content_id',
			'c.date_added',
			'c.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'cd.title') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY c.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(c.content_id) DESC";
		} else {
			$sql .= " ASC, LCASE(c.content_id) ASC";
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

	public function getTotalContents($data = array()) {
		$sql = "SELECT COUNT(DISTINCT c.content_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "cms_category_path cp LEFT JOIN " . DB_PREFIX . "cms_content_to_category c2c ON (cp.category_id = c2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "cms_content_to_category c2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "cms_content_filter cf ON (c2c.content_id = cf.content_id) LEFT JOIN " . DB_PREFIX . "cms_content c ON (cf.content_id = c.content_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "cms_content c ON (c2c.content_id = c.content_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "cms_content c";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "cms_content_description cd ON (c.content_id = cd.content_id) LEFT JOIN " . DB_PREFIX . "cms_content_to_store c2s ON (c.content_id = c2s.content_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = '1' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND c2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND cf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_title']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_title'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_title'])));

				foreach ($words as $word) {
					$implode[] = "cd.title LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_content'])) {
					$sql .= " OR cd.content LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
				}
			}

			if (!empty($data['filter_title']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "cd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getContentLayoutId($content_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content_to_layout WHERE content_id = '" . (int)$content_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($content_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content_to_category WHERE content_id = '" . (int)$content_id . "'");

		return $query->rows;
	}

	public function getContentSeoUrlKeyword($content_id) {		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'cms_content_id=" . (int)$content_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['keyword'];
	}

}
