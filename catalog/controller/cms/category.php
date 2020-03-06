<?php
class ControllerCmsCategory extends Controller {
	public function index() {
		$this->load->language('cms/category');

		$this->load->model('cms/category');

		$this->load->model('cms/content');

		$this->load->model('tool/image');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

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

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_cms_content_limit');
		}

		$limit = $limit > 0 ? $limit : 1;

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_cms_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('cms/category', 'path=' . $path . $url)
					);
				}
			}
		} elseif (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];

			$this->request->get['path'] = $category_id;

			$path = $category_id;
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_cms_category->getCategory($category_id);

		if ($category_info) {
			$this->document->setTitle(empty($category_info['meta_title']) ? $category_info['name'] : $category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['category_id'] = $category_id;
			$data['heading_title'] = $category_info['name'];

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('cms/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');

			$url = '';

			if (isset($this->request->get['filter']) && !empty($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort']) && !empty($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order']) && !empty($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit']) && !empty($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

			$results = $this->model_cms_category->getCategories($category_id);

			$all_category_id = $results ? $category_id : $category_info['parent_id'];

			if (empty($results)) {
				$results = $this->model_cms_category->getCategories($category_info['parent_id']);
			}

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

				if ($result['category_id'] == $category_id) {
					$path = $this->request->get['path'];
				} elseif ($result['parent_id'] == 0) {
					$path = $result['category_id'];
				} elseif ($category_info['parent_id'] == $all_category_id) {
					$path = preg_replace('/(.*)_{1}([^_]*)/i', '$1', $this->request->get['path']) . '_' . $result['category_id'];
				} else {
					$path = $this->request->get['path'] . '_' . $result['category_id'];
				}

				$data['categories'][] = array(
					'category_id' => $result['category_id'],
					'name' => $result['name'],
					'count' => $this->config->get('config_cms_count') ? $this->model_cms_content->getTotalContents($filter_data) : false,
					'href' => $this->url->link('cms/category', 'path=' . $path . $url)
				);
			}
			
			if ($data['categories'] && $all_category_id) {
				if ($all_category_id == $category_id) {
					$path = $this->request->get['path'];
				} else {
					$path = preg_replace('/(.*)_{1}([^_]*)/i', '$1', $this->request->get['path']);
				}

				$all_catagory = array(
					'category_id' => $all_category_id,
					'name' => $this->language->get('text_all'),
					'href' => $this->url->link('cms/category', 'path=' . $path . $url)
				);
				
				array_unshift($data['categories'], $all_catagory);
			}

			$data['contents'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_sub_category' => true,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$content_total = $this->model_cms_content->getTotalContents($filter_data);

			$results = $this->model_cms_content->getContents($filter_data);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 0, 0);
				} else {
					$image = false;//$this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cms_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cms_height'));
				}

				if ($result['meta_description']) {
					$description = $result['meta_description'];
				} else if (utf8_strlen(trim(strip_tags(html_entity_decode($result['content'], ENT_QUOTES, 'UTF-8')))) > $this->config->get('theme_' . $this->config->get('config_theme') . '_cms_description_length')) {
					$description = utf8_substr(trim(strip_tags(html_entity_decode($result['content'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_cms_description_length')) . '...';
				} else {
					$description = false;
				}

				$data['contents'][] = array(
					'conten_id'   => $result['content_id'],
					'image'       => $image,
					'title'       => $result['title'],
					'description' => $description,
					'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'href'        => $this->url->link('cms/content', 'path=' . $this->request->get['path'] . '&content_id=' . $result['content_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['filter']) && !empty($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit']) && !empty($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts']['default'] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('cms/category', 'path=' . $this->request->get['path'] . $url)// . '&sort=p.sort_order&order=ASC'
			);

			$data['sorts']['name_asc'] = array(
				'text'  => $this->language->get('text_title_asc'),
				'value' => 'cd.title-ASC',
				'href'  => $this->url->link('cms/category', 'path=' . $this->request->get['path'] . '&sort=cd.title&order=ASC' . $url)
			);

			$data['sorts']['name_desc'] = array(
				'text'  => $this->language->get('text_title_desc'),
				'value' => 'cd.title-DESC',
				'href'  => $this->url->link('cms/category', 'path=' . $this->request->get['path'] . '&sort=cd.title&order=DESC' . $url)
			);

			$data['sorts']['added_asc'] = array(
				'text'  => $this->language->get('text_added_asc'),
				'value' => 'c.date_added-ASC',
				'href'  => $this->url->link('cms/category', 'path=' . $this->request->get['path'] . '&sort=c.date_added&order=ASC' . $url)
			);

			$data['sorts']['added_desc'] = array(
				'text'  => $this->language->get('text_added_desc'),
				'value' => 'c.date_added-DESC',
				'href'  => $this->url->link('cms/category', 'path=' . $this->request->get['path'] . '&sort=c.date_added&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['filter']) && !empty($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort']) && !empty($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order']) && !empty($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array(10, 25, 50, 75, 100));//$this->config->get('theme_' . $this->config->get('config_theme') . '_cms_limit')

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('cms/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter']) && !empty($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort']) && !empty($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order']) && !empty($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit']) && !empty($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $content_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('cms/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();
			$data['pagination_data'] = $pagination->getData();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($content_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($content_total - $limit)) ? $content_total : ((($page - 1) * $limit) + $limit), $content_total, ceil($content_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('cms/category', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('cms/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}

			if ($page > 1) {
			    $this->document->addLink($this->url->link('cms/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($content_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('cms/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$template = is_file(DIR_TEMPLATE . $this->config->get('config_theme') . '/template/' . $category_info['template'] . '.twig') ? $category_info['template'] : 'cms/category';

			$this->response->setOutput($this->load->view($template, $data));
		} else {
			$url = '';

			if (isset($this->request->get['path']) && !empty($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter']) && !empty($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort']) && !empty($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order']) && !empty($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page']) && !empty($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit']) && !empty($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('cms/category', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

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
