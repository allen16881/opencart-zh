<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$this->load->model('design/menu');

		$this->load->model('tool/image');

		$menu_id = $this->config->get('theme_' . $this->config->get('config_theme') . '_menu_bottom');

		$data['menus'] = array();

		$menu_links = $this->model_design_menu->getMenuLinks($menu_id);

		foreach ($menu_links as $key => $menu_link) {
			$level_2_data = array();

			$links_2 = $this->model_design_menu->getMenuLinksByParentId($menu_link['menu_link_id']);

			foreach ($links_2 as $link_2) {
				$level_3_data = array();

				$links_3 = $this->model_design_menu->getMenuLinksByParentId($link_2['menu_link_id']);

				foreach ($links_3 as $link_3) {
					if ($link_3['image'] && is_file(DIR_IMAGE . $link_3['image'])) {
						$image = $link_3['image'];
					} else {
						$image = '';
					}

					$level_3_data[] = array(
						'menu_link_id' => $link_3['menu_link_id'],
						'title'      => $link_3['title'],
						'link'       => $link_3['link'],
						'subtitle'   => $link_3['subtitle'],
						'extra'      => $link_3['extra'],
						'image'      => $image,
						'parent_id'  => $link_3['parent_id'],
						'sort_order' => $link_3['sort_order']
					);
				}

				if ($link_2['image'] && is_file(DIR_IMAGE . $link_2['image'])) {
					$image = $link_2['image'];
				} else {
					$image = '';
				}

				$level_2_data[] = array(
					'menu_link_id' => $link_2['menu_link_id'],
					'title'      => $link_2['title'],
					'link'       => $link_2['link'],
					'subtitle'   => $link_2['subtitle'],
					'extra'      => $link_2['extra'],
					'image'      => $image,
					'parent_id'  => $link_2['parent_id'],
					'children'   => $level_3_data,
					'sort_order' => $link_2['sort_order']
				);
			}

			if ($menu_link['image'] && is_file(DIR_IMAGE . $menu_link['image'])) {
				$image = $menu_link['image'];
			} else {
				$image = '';
			}
			
			$data['menus'][$key] = array(
				'title'      => $menu_link['title'],
				'link'       => $menu_link['link'],
				'subtitle'   => $menu_link['subtitle'],
				'extra'      => $menu_link['extra'],
				'image'      => $image,
				'parent_id'  => $menu_link['parent_id'],
				'children'   => $level_2_data,
				'sort_order' => $menu_link['sort_order']
			);
		}

		$this->load->model('cms/content');

		$data['contents'] = array();

		foreach ($this->model_cms_content->getContents(array('filter_bottom'=>1)) as $result) {
			$data['contents'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('cms/content', 'content_id=' . $result['content_id'])
			);
		}

		$data['route'] = isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home';

		$data['cart_quantity'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);

		$data['customer_id'] = $this->customer->getId();

		$data['home'] = $this->url->link('common/home');
		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['tracking'] = $this->url->link('information/tracking');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/login', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['faqs'] = $this->url->link('information/information', 'information_id=' . $this->config->get('config_shopping_faq_id'));

		$data['name'] = $this->config->get('config_name');
		$data['address'] = $this->config->get('config_address');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['cs_email'] = $this->config->get('config_cs_email');
		$data['open'] = $this->config->get('config_open');
		$data['comment'] = $this->config->get('config_comment');
		$data['notification'] = html_entity_decode($this->config->get('config_notification'));
		$data['links'] = html_entity_decode($this->config->get('config_links'));
		$data['copyright'] = sprintf($this->language->get('text_copyright'), date('Y', time()), $data['name']);

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		$data['scripts'] = $this->document->getScripts('footer');

		return $this->load->view('common/footer', $data);
	}
}
