<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		$this->load->language('common/header');
		
		$data['title'] = $this->document->getTitle();
		$data['description'] = $this->document->getDescription();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();

		$data['base'] = HTTP_SERVER;

		if (isset($this->request->get['route'])) {
			$data['route'] = $this->request->get['route'];
		} else {
			$data['route'] = 'install/step_1';
		}

		if (!isset($this->request->get['route'])) {
			$data['redirect'] = $this->url->link('install/step_1');
		} else {
			$url_data = $this->request->get;

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
		}

		if (isset($this->session->data['language'])) {
			$data['code'] = $this->session->data['language'];
		} else {
			$data['code'] = $this->config->get('language_default');
		}

		$language_files = glob(DIR_LANGUAGE . '*', GLOB_ONLYDIR);

		foreach ($language_files as $file) {
			$code = basename($file);

			$language['code'] = $code;

			switch ($code) {
				case 'zh-cn':
					$language['name'] = '简体中文';
					$language['sort_order'] = 3;
					break;
				case 'zh-tw':
					$language['name'] = '繁体中文';
					$language['sort_order'] = 2;
					break;
				case 'en-gb':
					$language['name'] = 'English';
					$language['sort_order'] = 1;
					break;
				default:
					$language['name'] = $code;
					$language['sort_order'] = 0;
					break;
			}

			$language['link'] = $this->url->link('common/header/language', 'code=' . $code . '&redirect=' . $data['redirect'], $this->request->server['HTTPS']);

			$language['current'] = $code == $data['code'];

			$data['languages'][] = $language;
		}

		$sort_order = array_column($data['languages'], 'sort_order');

		array_multisort($sort_order, SORT_DESC, $data['languages']);

		return $this->load->view('common/header', $data);
	}

	public function language() {
		if (isset($this->request->get['code']) && is_dir(DIR_LANGUAGE . basename($this->request->get['code']))) {
			$this->session->data['language'] = $this->request->get['code'];
		}

		if (isset($this->request->get['redirect'])) {
			$this->response->redirect($this->request->get['redirect']);
		} else {
			$this->response->redirect($this->url->link('install/step_1'));
		}
	}
}
