<?php
class ControllerCmsContent extends Controller {
	public function index() {
		$this->load->language('cms/content');

		$this->load->model('cms/content');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		$this->load->model('cms/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_cms_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_cms_category->getCategory($category_id);

			if ($category_info) {
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

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('cms/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		if (isset($this->request->get['content_id'])) {
			$content_id = (int)$this->request->get['content_id'];
		} else {
			$content_id = 0;
		}

		$content_info = $this->model_cms_content->getContent($content_id);

		if (isset($this->request->get['content'])) {
			if ($content_info) {
				$output = html_entity_decode($content_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
			} else {
				$output = $this->language->get('text_error');
			}

			$this->response->setOutput($output);
		} else {
			if ($content_info) {
				$this->document->setTitle(empty($content_info['meta_title']) ? $content_info['title'] : $content_info['meta_title']);
				$this->document->setDescription($content_info['meta_description']);
				$this->document->setKeywords($content_info['meta_keyword']);

				$url = '';

				if (isset($this->request->get['path'])) {
					$url .= '&path=' . $this->request->get['path'];
				}

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['search'])) {
					$url .= '&search=' . $this->request->get['search'];
				}

				if (isset($this->request->get['tag'])) {
					$url .= '&tag=' . $this->request->get['tag'];
				}

				if (isset($this->request->get['content'])) {
					$url .= '&content=' . $this->request->get['content'];
				}

				if (isset($this->request->get['category_id'])) {
					$url .= '&category_id=' . $this->request->get['category_id'];
				}

				if (isset($this->request->get['sub_category'])) {
					$url .= '&sub_category=' . $this->request->get['sub_category'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $content_info['title'],
					'href' => $this->url->link('cms/content', $url . '&content_id=' .  $content_id)
				);

				$data['heading_title'] = $content_info['title'];

				$data['content'] = html_entity_decode($content_info['content'], ENT_QUOTES, 'UTF-8');

				$data['continue'] = $this->url->link('common/home');

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');

				$template = is_file(DIR_TEMPLATE . $this->config->get('config_theme') . '/template/' . $content_info['template'] . '.twig') ? $content_info['template'] : 'cms/content';

				$this->response->setOutput($this->load->view($template, $data));
			} else {
				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_error'),
					'href' => $this->url->link('cms/content', 'content_id=' . $content_id)
				);

				$this->document->setTitle($this->language->get('text_error'));

				$data['heading_title'] = $this->language->get('text_error');

				$data['text_error'] = $this->language->get('text_error');

				$data['continue'] = $this->url->link('common/home');

				$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');

				$this->response->setOutput($this->load->view('error/not_found', $data));
			}
		}
	}

	public function agree() {
		$this->load->model('cms/content');

		if (isset($this->request->get['content_id'])) {
			$content_id = (int)$this->request->get['content_id'];
		} else {
			$content_id = 0;
		}

		$output = '';

		$content_info = $this->model_cms_content->getContent($content_id);

		if ($content_info) {
			$output .= html_entity_decode($content_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}
