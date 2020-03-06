<?php
class ControllerExtensionApp extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/app');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/extension');

		$this->getList();
	}

	public function install() {
		$this->load->language('extension/app');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->install('app', $this->request->get['extension']);

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/app/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/app/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('extension/app/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getList();
	}

	public function uninstall() {
		$this->load->language('extension/app');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->uninstall('app', $this->request->get['extension']);

			// Call uninstall method if it exsits
			$this->load->controller('extension/app/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = $this->language->get('text_success');
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
			'href' => $this->url->link('extension/app', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['install'] = $this->url->link('extension/app/install', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['uninstall'] = $this->url->link('extension/app/uninstall', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['text_layout'] = sprintf($this->language->get('text_layout'), $this->url->link('design/layout', 'user_token=' . $this->session->data['user_token'], true));

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

		$extensions = $this->model_setting_extension->getInstalled('app');

		foreach ($extensions as $key => $value) {
			if (!is_file(DIR_APPLICATION . 'controller/extension/app/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/app/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('app', $value);

				unset($extensions[$key]);
			}
		}

		$data['extensions'] = array();
		
		// Compatibility code for old extension folders
		$files = glob(DIR_APPLICATION . 'controller/extension/app/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language('extension/app/' . $extension, 'extension');

				$data['extensions'][] = array(
					'name'      => $this->language->get('extension')->get('heading_title'),
					'status'    => $this->config->get('app_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'install'   => $this->url->link('extension/app/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension . $url, true),
					'uninstall' => $this->url->link('extension/app/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension . $url, true),
					'installed' => in_array($extension, $extensions),
					'edit'      => $this->url->link('extension/app/' . $extension, 'user_token=' . $this->session->data['user_token'] . $url, true)
				);
			}
		}

		$sort_order = array();

		foreach ($data['extensions'] as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $data['extensions']);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/app', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/app')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
