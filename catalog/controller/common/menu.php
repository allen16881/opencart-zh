<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->model('design/menu');

		$this->load->model('tool/image');

		$this->load->language('common/menu');

		$menu_id = $this->config->get('theme_' . $this->config->get('config_theme') . '_menu_top');

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

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'],
						'count' => $this->config->get('config_product_count') ? $this->model_catalog_product->getTotalProducts($filter_data) : '',
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		$this->load->model('cms/category');

		$data['cms_categories'] = array();

		$cms_categories = $this->model_cms_category->getCategories(0);

		foreach ($cms_categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_cms_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'],
						'href'  => $this->url->link('cms/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['cms_categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('cms/category', 'path=' . $category['category_id'])
				);
			}
		}

		return $this->load->view('common/menu', $data);
	}
}
