<?php
class ControllerDesignMenu extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('design/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/menu');

		$this->getList();
	}

	public function add() {
		$this->load->language('design/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/javascript/jquery/nestable/jquery.nestable.min.css');
		$this->document->addScript('view/javascript/jquery/nestable/jquery.nestable.min.js');

		$this->load->model('design/menu');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_menu->addMenu($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
	

	public function edit() {
		$this->load->language('design/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/javascript/jquery/nestable/jquery.nestable.min.css');
		$this->document->addScript('view/javascript/jquery/nestable/jquery.nestable.min.js');

		$this->load->model('design/menu');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_menu->editMenu($this->request->get['menu_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('design/menu');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/menu');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $menu_id) {
				$this->model_design_menu->deleteMenu($menu_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('design/menu/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('design/menu/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['menus'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$menu_total = $this->model_design_menu->getTotalMenus();

		$results = $this->model_design_menu->getMenus($filter_data);

		foreach ($results as $result) {
			$data['menus'][] = array(
				'menu_id' => $result['menu_id'],
				'name'      => $result['name'],
				'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'edit'      => $this->url->link('design/menu/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $result['menu_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_status'] = $this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $menu_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($menu_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($menu_total - $this->config->get('config_limit_admin'))) ? $menu_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $menu_total, ceil($menu_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/menu_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['menu_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['menu_link'])) {
			$data['error_menu_link'] = $this->error['menu_link'];
		} else {
			$data['error_menu_link'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['menu_id'])) {
			$data['action'] = $this->url->link('design/menu/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('design/menu/edit', 'user_token=' . $this->session->data['user_token'] . '&menu_id=' . $this->request->get['menu_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('design/menu', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['menu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$menu_info = $this->model_design_menu->getMenu($this->request->get['menu_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['menu_id'] = isset($this->request->get['menu_id']) ? $this->request->get['menu_id'] : 0;

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($menu_info)) {
			$data['name'] = $menu_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($menu_info)) {
			$data['status'] = $menu_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		if (isset($this->request->post['menu_link'])) {
			$menu_links = $this->request->post['menu_link'];
		} elseif (isset($this->request->get['menu_id'])) {
			$menu_links = $this->model_design_menu->getMenuLinks($this->request->get['menu_id']);
		} else {
			$menu_links = array();
		}

		$data['menu_links'] = array();

		foreach ($menu_links as $key => $value) {
			foreach ($value as $menu_link) {
				$level_2_data = array();

				$links_2 = $this->model_design_menu->getMenuLinksByParentId($menu_link['menu_link_id']);

				foreach ($links_2 as $link_2) {
					$level_3_data = array();

					$links_3 = $this->model_design_menu->getMenuLinksByParentId($link_2['menu_link_id']);

					foreach ($links_3 as $link_3) {
						if (is_file(DIR_IMAGE . $link_3['image'])) {
							$image = $link_3['image'];
							$thumb = $link_3['image'];
						} else {
							$image = '';
							$thumb = 'no_image.png';
						}

						$level_3_data[] = array(
							'menu_link_id' => $link_3['menu_link_id'],
							'title'      => $link_3['title'],
							'link'       => $link_3['link'],
							'subtitle'   => $link_3['subtitle'],
							'extra'      => $link_3['extra'],
							'image'      => $image,
							'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
							'parent_id'  => $link_3['parent_id'],
							'sort_order' => $link_3['sort_order']
						);
					}

					if (is_file(DIR_IMAGE . $link_2['image'])) {
						$image = $link_2['image'];
						$thumb = $link_2['image'];
					} else {
						$image = '';
						$thumb = 'no_image.png';
					}

					$level_2_data[] = array(
						'menu_link_id' => $link_2['menu_link_id'],
						'title'      => $link_2['title'],
						'link'       => $link_2['link'],
						'subtitle'   => $link_2['subtitle'],
						'extra'      => $link_2['extra'],
						'image'      => $image,
						'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
						'parent_id'  => $link_2['parent_id'],
						'children'   => $level_3_data,
						'sort_order' => $link_2['sort_order']
					);
				}

				if (is_file(DIR_IMAGE . $menu_link['image'])) {
					$image = $menu_link['image'];
					$thumb = $menu_link['image'];
				} else {
					$image = '';
					$thumb = 'no_image.png';
				}
				
				$data['menu_links'][$key][] = array(
					'title'      => $menu_link['title'],
					'link'       => $menu_link['link'],
					'subtitle'   => $menu_link['subtitle'],
					'extra'      => $menu_link['extra'],
					'image'      => $image,
					'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
					'parent_id'  => $menu_link['parent_id'],
					'children'   => $level_2_data,
					'sort_order' => $menu_link['sort_order']
				);
			}
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/menu_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'design/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (isset($this->request->post['menu_link'])) {
			foreach ($this->request->post['menu_link'] as $language_id => $value) {
				foreach ($value as $menu_link_id => $menu_link) {
					if ((utf8_strlen($menu_link['title']) < 1) || (utf8_strlen($menu_link['title']) > 64)) {
						$this->error['warning'] = $this->language->get('error_form');

						$this->error['menu_link'][$language_id][$menu_link_id] = $this->language->get('error_title');
					}
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'design/menu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}