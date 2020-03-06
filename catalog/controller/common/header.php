<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'uploads/' . $this->config->get('config_icon'), 'icon');
		}

		// Javascript Language Items
		$this->document->addValue('config_seo_url', $this->config->get('config_seo_url'));
		$this->document->addValue('text_yes', $this->language->get('text_yes'));
		$this->document->addValue('text_no', $this->language->get('text_no'));
		$this->document->addValue('text_select', $this->language->get('text_select'));
		$this->document->addValue('text_none', $this->language->get('text_none'));
		$this->document->addValue('text_no_content', $this->language->get('text_no_content'));
		$this->document->addValue('button_confirm', $this->language->get('button_confirm'));
		$this->document->addValue('button_cancel', $this->language->get('button_cancel'));
		$this->document->addValue('button_view_cart', $this->language->get('button_view_cart'));
		$this->document->addValue('button_checkout', $this->language->get('button_checkout'));

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['values'] = $this->document->getValues();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');
		$data['address'] = $this->config->get('config_address');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['email'] = $this->config->get('config_email');
		$data['open'] = $this->config->get('config_open');
		$data['comment'] = $this->config->get('config_comment');

		if (!empty($this->config->get('config_notification'))) {
			$data['notification'] = html_entity_decode($this->config->get('config_notification'));

			if (isset($this->request->cookie['notification']) && $this->request->cookie['notification'] == 'close') {
				$data['notification'] = false;
			}
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'uploads/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/customer');

			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

			if ($this->request->server['HTTPS']) {
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}

			$data['customer'] = array(
				'fullname'   => $customer_info['fullname'],
				'email'      => $customer_info['email'],
				'telephone'  => $customer_info['telephone'],
				'avatar'     => $server . 'uploads/no_avatar.png',
				'date_added' => date($this->language->get('date_format_short'), strtotime($customer_info['date_added']))
			);

			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFullName(), $this->url->link('account/logout', '', true));

		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');

		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		$data['route'] = isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home';

		$parts = explode('/', $data['route']);
		$data['directory'] = $parts[0];
		$data['file'] = $parts[1];

		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$id_name = ' product-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$id_name = ' path-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$id_name = ' manufacturer-' . $this->request->get['manufacturer_id'];
			} elseif (isset($this->request->get['information_id'])) {
				$id_name = ' information-' . $this->request->get['information_id'];
			} else {
				$id_name = '';
			}

			$data['page_name'] = str_replace('/', '-', $this->request->get['route']) . $id_name;
			/*$route_array = explode('/', $this->request->get['route']);

			if (isset($route_array[1])) {
				$data['class'] = $route_array[1];
			} else {
				$data['class'] = '';
			}*/
		} else {
			$data['page_name'] = 'common-home';
		}

		if (isset($this->request->get['_route_']) && $this->request->get['_route_'] != 'error/not_found') {
			$data['page_name'] .= ' ' . str_replace('/', '-', $this->request->get['_route_']) . '-page';
		}

		return $this->load->view('common/header', $data);
	}
}
