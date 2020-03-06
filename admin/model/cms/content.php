<?php
class ModelCmsContent extends Model {
	public function addContent($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content SET sort_order = '" . (int)$data['sort_order'] . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', status = '" . (int)$data['status'] . "', date_added = NOW(), template = '" . $this->db->escape($data['template']) . "'");

		$content_id = $this->db->getLastId();

		foreach ($data['content_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content_description SET content_id = '" . (int)$content_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', content = '" . $this->db->escape($value['content']) . "', image = '" . $this->db->escape($value['image']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['content_store'])) {
			foreach ($data['content_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content_to_store SET content_id = '" . (int)$content_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['content_category'])) {
			foreach ($data['content_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content_to_category SET content_id = '" . (int)$content_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		// SEO URL
		if (isset($data['content_seo_url'])) {
			foreach ($data['content_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (trim($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'cms_content_id=" . (int)$content_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		if (isset($data['content_layout'])) {
			foreach ($data['content_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content_to_layout SET content_id = '" . (int)$content_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('cms_content');

		return $content_id;
	}

	public function editContent($content_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "cms_content SET sort_order = '" . (int)$data['sort_order'] . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), template = '" . $this->db->escape($data['template']) . "' WHERE content_id = '" . (int)$content_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "cms_content_description WHERE content_id = '" . (int)$content_id . "'");

		foreach ($data['content_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content_description SET content_id = '" . (int)$content_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', content = '" . $this->db->escape($value['content']) . "', image = '" . $this->db->escape($value['image']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "cms_content_to_store WHERE content_id = '" . (int)$content_id . "'");

		if (isset($data['content_store'])) {
			foreach ($data['content_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content_to_store SET content_id = '" . (int)$content_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "cms_content_to_category WHERE content_id = '" . (int)$content_id . "'");

		if (isset($data['content_category'])) {
			foreach ($data['content_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cms_content_to_category SET content_id = '" . (int)$content_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'cms_content_id=" . (int)$content_id . "'");

		if (isset($data['content_seo_url'])) {
			foreach ($data['content_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (trim($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'cms_content_id=" . (int)$content_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "cms_content_to_layout` WHERE content_id = '" . (int)$content_id . "'");

		if (isset($data['content_layout'])) {
			foreach ($data['content_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "cms_content_to_layout` SET content_id = '" . (int)$content_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('cms_content');
	}

	public function deleteContent($content_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cms_content` WHERE content_id = '" . (int)$content_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cms_content_description` WHERE content_id = '" . (int)$content_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cms_content_to_store` WHERE content_id = '" . (int)$content_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cms_content_to_category` WHERE content_id = '" . (int)$content_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cms_content_to_layout` WHERE content_id = '" . (int)$content_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'cms_content_id=" . (int)$content_id . "'");

		$this->cache->delete('cms_content');
	}

	public function getContent($content_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "cms_content WHERE content_id = '" . (int)$content_id . "'");

		return $query->row;
	}

	public function getContents($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "cms_content c LEFT JOIN " . DB_PREFIX . "cms_content_description cd ON (c.content_id = cd.content_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'c.content_id',
				'cd.title',
				'c.date_added',
				'c.sort_order',
				'c.status'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY c.sort_order";
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
		} else {
			$content_data = $this->cache->get('cms_content.' . (int)$this->config->get('config_language_id'));

			if (!$content_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content c LEFT JOIN " . DB_PREFIX . "cms_content_description cd ON (c.content_id = cd.content_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cd.title");

				$content_data = $query->rows;

				$this->cache->set('cms_content.' . (int)$this->config->get('config_language_id'), $content_data);
			}

			return $content_data;
		}
	}

	public function getContentByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content c LEFT JOIN " . DB_PREFIX . "cms_content_description cd ON (c.content_id = cd.content_id) LEFT JOIN " . DB_PREFIX . "cms_content_to_category c2c ON (c.content_id = c2c.content_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2c.category_id = '" . (int)$category_id . "' ORDER BY cd.name ASC");

		return $query->rows;
	}

	public function getContentDescriptions($content_id) {
		$content_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content_description WHERE content_id = '" . (int)$content_id . "'");

		foreach ($query->rows as $result) {
			$content_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'content'          => $result['content'],
				'image'            => $result['image'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $content_description_data;
	}

	public function getContentCategories($content_id) {
		$content_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content_to_category WHERE content_id = '" . (int)$content_id . "'");

		foreach ($query->rows as $result) {
			$content_category_data[] = $result['category_id'];
		}

		return $content_category_data;
	}

	public function getContentStores($content_id) {
		$content_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content_to_store WHERE content_id = '" . (int)$content_id . "'");

		foreach ($query->rows as $result) {
			$content_store_data[] = $result['store_id'];
		}

		return $content_store_data;
	}

	public function getContentSeoUrls($content_id) {
		$content_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'cms_content_id=" . (int)$content_id . "'");

		foreach ($query->rows as $result) {
			$content_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $content_seo_url_data;
	}

	public function getContentLayouts($content_id) {
		$content_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cms_content_to_layout WHERE content_id = '" . (int)$content_id . "'");

		foreach ($query->rows as $result) {
			$content_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $content_layout_data;
	}

	public function getTotalContents($data = array()) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cms_content");

		return $query->row['total'];
	}

	public function getTotalContentsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cms_content_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}