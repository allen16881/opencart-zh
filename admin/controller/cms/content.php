<?php
class ControllerCmsContent extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('cms/content');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cms/content');

		$this->getList();
	}

	public function add() {
		$this->load->language('cms/content');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cms/content');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_cms_content->addContent($this->request->post);

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

			$this->response->redirect($this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('cms/content');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cms/content');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_cms_content->editContent($this->request->get['content_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('cms/content');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cms/content');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $content_id) {
				$this->model_cms_content->deleteContent($content_id);
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

			$this->response->redirect($this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'c.content_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
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
			'href' => $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('cms/content/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('cms/content/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['contents'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$content_total = $this->model_cms_content->getTotalContents();

		$results = $this->model_cms_content->getContents($filter_data);

		foreach ($results as $result) {
			$data['contents'][] = array(
				'content_id'     => $result['content_id'],
				'title'          => $result['title'],
				'sort_order'     => $result['sort_order'],
				'status'         => $result['status'],
				'edit'           => $this->url->link('cms/content/edit', 'user_token=' . $this->session->data['user_token'] . '&content_id=' . $result['content_id'] . $url, true)
			);
		}

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

		$data['sort_content_id'] = $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . '&sort=c.content_id' . $url, true);
		$data['sort_title'] = $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.title' . $url, true);
		$data['sort_sort_order'] = $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . '&sort=c.sort_order' . $url, true);
		$data['sort_status'] = $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);


		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $content_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($content_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($content_total - $this->config->get('config_limit_admin'))) ? $content_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $content_total, ceil($content_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cms/content_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['content_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['content'])) {
			$data['error_content'] = $this->error['content'];
		} else {
			$data['error_content'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
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
			'href' => $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['content_id'])) {
			$data['action'] = $this->url->link('cms/content/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('cms/content/edit', 'user_token=' . $this->session->data['user_token'] . '&content_id=' . $this->request->get['content_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('cms/content', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['content_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$content_info = $this->model_cms_content->getContent($this->request->get['content_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['content_description'])) {
			$data['content_description'] = $this->request->post['content_description'];
		} elseif (isset($this->request->get['content_id'])) {
			$data['content_description'] = $this->model_cms_content->getContentDescriptions($this->request->get['content_id']);
		} else {
			$data['content_description'] = array();
		}

		$this->load->model('tool/image');

		foreach ($data['content_description'] as $language_id => $value_data) {
			if (isset($value_data['image']) && !empty($value_data['image']) && is_file(DIR_IMAGE . $value_data['image'])) {
				$data['content_description'][$language_id]['thumb'] = $this->model_tool_image->resize($value_data['image'], 100, 100);
			} else {
				$data['content_description'][$language_id]['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			}
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Categories
		$this->load->model('cms/category');

		if (isset($this->request->post['content_category'])) {
			$categories = $this->request->post['content_category'];
		} elseif (isset($this->request->get['content_id'])) {
			$categories = $this->model_cms_content->getContentCategories($this->request->get['content_id']);
		} else {
			$categories = array();
		}

		$data['content_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_cms_category->getCategory($category_id);

			if ($category_info) {
				$data['content_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['content_store'])) {
			$data['content_store'] = $this->request->post['content_store'];
		} elseif (isset($this->request->get['content_id'])) {
			$data['content_store'] = $this->model_cms_content->getContentStores($this->request->get['content_id']);
		} else {
			$data['content_store'] = array(0);
		}

		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($content_info)) {
			$data['bottom'] = $content_info['bottom'];
		} else {
			$data['bottom'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($content_info)) {
			$data['status'] = $content_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($content_info)) {
			$data['sort_order'] = $content_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['content_seo_url'])) {
			$data['content_seo_url'] = $this->request->post['content_seo_url'];
		} elseif (isset($this->request->get['content_id'])) {
			$data['content_seo_url'] = $this->model_cms_content->getContentSeoUrls($this->request->get['content_id']);
		} else {
			$data['content_seo_url'] = array();
		}

		if (isset($this->request->post['template'])) {
			$data['template'] = $this->request->post['template'];
		} elseif (!empty($content_info)) {
			$data['template'] = $content_info['template'];
		} else {
			$data['template'] = '';
		}

		if (isset($this->request->post['content_layout'])) {
			$data['content_layout'] = $this->request->post['content_layout'];
		} elseif (isset($this->request->get['content_id'])) {
			$data['content_layout'] = $this->model_cms_content->getContentLayouts($this->request->get['content_id']);
		} else {
			$data['content_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('cms/content_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'cms/content')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['content_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			/*if (utf8_strlen($value['content']) < 1) {
				$this->error['content'][$language_id] = $this->language->get('error_content');
			}*/

			/*if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}*/
		}

		if ($this->request->post['content_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['content_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (trim($keyword)) {
						/*if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}*/

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['content_id']) || ($seo_url['query'] != 'cms_content_id=' . $this->request->get['content_id']))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'cms/content')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/store');

		foreach ($this->request->post['selected'] as $content_id) {
			if ($this->config->get('config_account_id') == $content_id) {
				$this->error['warning'] = $this->language->get('error_account');
			}

			if ($this->config->get('config_checkout_id') == $content_id) {
				$this->error['warning'] = $this->language->get('error_checkout');
			}

			if ($this->config->get('config_affiliate_id') == $content_id) {
				$this->error['warning'] = $this->language->get('error_affiliate');
			}

			if ($this->config->get('config_return_id') == $content_id) {
				$this->error['warning'] = $this->language->get('error_return');
			}

			$store_total = $this->model_setting_store->getTotalStoresByContentId($content_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
		}

		return !$this->error;
	}
}
