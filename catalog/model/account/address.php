<?php
class ModelAccountAddress extends Model {
	public function addAddress($customer_id, $data) {
		$data['firstname'] = isset($data['firstname']) ? $data['firstname'] : '';
		$data['lastname'] = isset($data['lastname']) ? $data['lastname'] : '';
		$data['fullname'] = isset($data['fullname']) ? $data['fullname'] : '';
		$data['telephone'] = isset($data['telephone']) ? $data['telephone'] : '';
		$data['city'] = isset($data['city']) ? $data['city'] : '';
		$data['company'] = isset($data['company']) ? $data['company'] : '';
		$data['address_2'] = isset($data['address_2']) ? $data['address_2'] : '';
		$data['city_id'] = isset($data['city_id']) ? $data['city_id'] : '';
		$data['district_id'] = isset($data['district_id']) ? $data['district_id'] : '';

		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', fullname = '" . $this->db->escape($data['fullname']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', city_id = '" . (int)$data['city_id'] . "', district_id = '" . (int)$data['district_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['address']) ? json_encode($data['custom_field']['address']) : '') . "'");

		$address_id = $this->db->getLastId();

		if (!empty($data['default'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}

		return $address_id;
	}

	public function editAddress($address_id, $data) {
		$data['firstname'] = isset($data['firstname']) ? $data['firstname'] : '';
		$data['lastname'] = isset($data['lastname']) ? $data['lastname'] : '';
		$data['fullname'] = isset($data['fullname']) ? $data['fullname'] : '';
		$data['telephone'] = isset($data['telephone']) ? $data['telephone'] : '';
		$data['city'] = isset($data['city']) ? $data['city'] : '';
		$data['company'] = isset($data['company']) ? $data['company'] : '';
		$data['address_2'] = isset($data['address_2']) ? $data['address_2'] : '';
		$data['city_id'] = isset($data['city_id']) ? $data['city_id'] : '';
		$data['district_id'] = isset($data['district_id']) ? $data['district_id'] : '';

		$this->db->query("UPDATE " . DB_PREFIX . "address SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', fullname = '" . $this->db->escape($data['fullname']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', city_id = '" . (int)$data['city_id'] . "', district_id = '" . (int)$data['district_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['address']) ? json_encode($data['custom_field']['address']) : '') . "' WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

		if (!empty($data['default'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		}
	}

	public function deleteAddress($address_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function getAddress($address_id) {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$city_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['city_id'] . "'");

			if ($city_query->num_rows) {
				$city = $city_query->row['name'];
				$city_code = $city_query->row['code'];
			} else {
				$city = '';
				$city_code = '';
			}

			$district_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['district_id'] . "'");

			if ($district_query->num_rows) {
				$district = $district_query->row['name'];
				$district_code = $district_query->row['code'];
			} else {
				$district = '';
				$district_code = '';
			}

			if ($address_format) {
				$format = $address_format;
			} else {
				$format = '{country}{zone}{city}{district}{address_1}';

				if ($this->config->get('config_sales_country_id') && ($this->config->get('config_sales_country_id') == $address_query->row['country_id'] || empty($address_query->row['country_id']))) {
					$format = '{zone}{city}{district}{address_1}';
				}

				if ($this->config->get('config_sales_zone_id') && ($this->config->get('config_sales_zone_id') == $address_query->row['zone_id'] || empty($address_query->row['zone_id']))) {
					$format = '{city}{district}{address_1}';
				}

				if ($this->config->get('config_sales_city_id') && ($this->config->get('config_sales_city_id') == $address_query->row['city_id'] || empty($address_query->row['city_id']))) {
					$format = '{district}{address_1}';
				}
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{fullname}',
				'{telephone}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}',
				'{district}'
			);

			$replace = array(
				'firstname' => $address_query->row['firstname'],
				'lastname'  => $address_query->row['lastname'],
				'fullname'  => $address_query->row['fullname'],
				'telephone' => $address_query->row['telephone'],
				'company'   => $address_query->row['company'],
				'address_1' => $address_query->row['address_1'],
				'address_2' => $address_query->row['address_2'],
				'city'      => $city ? $city : $address_query->row['city'],
				'postcode'  => $address_query->row['postcode'],
				'zone'      => $zone,
				'zone_code' => $zone_code,
				'country'   => $country,
				'district'  => $district
			);

			$address_data = array(
				'address_id'     => $address_query->row['address_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'fullname'       => $address_query->row['fullname'],
				'telephone'      => $address_query->row['telephone'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $city ? $city : $address_query->row['city'],
				'city_id'        => $address_query->row['city_id'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'district'       => $district,
				'district_id'    => $address_query->row['district_id'],
				'address_format' => $address_format,
				'address'        => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
				'custom_field'   => json_decode($address_query->row['custom_field'], true)
			);

			return $address_data;
		} else {
			return false;
		}
	}

	public function getAddresses($data = array()) {
		$address_data = array();

		$sql = "SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'";

		if (isset($data['sort'])) {
			$data['sort'] = $data['sort'];
		} else {
			$data['sort'] = 'address_id';
		}

		if (isset($data['order'])) {
			$data['order'] = $data['order'];
		} else {
			$data['order'] = 'DESC';
		}

		$sql .= " ORDER BY " . $data['sort'] . " " . $data['order'];

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$city_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['city_id'] . "'");

			if ($city_query->num_rows) {
				$city = $city_query->row['name'];
				$city_code = $city_query->row['code'];
			} else {
				$city = '';
				$city_code = '';
			}

			$district_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['district_id'] . "'");

			if ($district_query->num_rows) {
				$district = $district_query->row['name'];
				$district_code = $district_query->row['code'];
			} else {
				$district = '';
				$district_code = '';
			}

			if ($address_format) {
				$format = $address_format;
			} else {
				$format = '{country}{zone}{city}{district}{address_1}';

				if ($this->config->get('config_sales_country_id') && ($this->config->get('config_sales_country_id') == $result['country_id'] || empty($result['country_id']))) {
					$format = '{zone}{city}{district}{address_1}';
				}

				if ($this->config->get('config_sales_zone_id') && ($this->config->get('config_sales_zone_id') == $result['zone_id'] || empty($result['zone_id']))) {
					$format = '{city}{district}{address_1}';
				}

				if ($this->config->get('config_sales_city_id') && ($this->config->get('config_sales_city_id') == $result['city_id'] || empty($result['city_id']))) {
					$format = '{district}{address_1}';
				}
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{fullname}',
				'{telephone}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}',
				'{district}'
			);

			$replace = array(
				'firstname' => $result['firstname'],
				'lastname'  => $result['lastname'],
				'fullname'  => $result['fullname'],
				'telephone' => $result['telephone'],
				'company'   => $result['company'],
				'address_1' => $result['address_1'],
				'address_2' => $result['address_2'],
				'city'      => $city ? $city : $result['city'],
				'postcode'  => $result['postcode'],
				'zone'      => $zone,
				'zone_code' => $zone_code,
				'country'   => $country,
				'district'  => $district
			);

			$address_data[$result['address_id']] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'fullname'       => $result['fullname'],
				'telephone'      => $result['telephone'],
				'company'        => $result['company'],
				'address_1'      => $result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city_id'        => $result['city_id'],
				'city'           => $city ? $city : $result['city'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $result['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'district_id'    => $result['district_id'],
				'district'       => $district,
				'address_format' => $address_format,
				'address'        => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
				'custom_field'   => json_decode($result['custom_field'], true)
			);
		}

		return $address_data;
	}

	public function getTotalAddresses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}
