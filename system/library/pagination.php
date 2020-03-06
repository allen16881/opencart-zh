<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Pagination class
*/
class Pagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 8;
	public $url = '';
	public $text_first = '&laquo;';
	public $text_last = '&raquo;';
	public $text_next = '&rarr;';
	public $text_prev = '&larr;';

	/**
     * 
     *
     * @return	text
     */
	public function render() {
		$total = $this->total;

		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}

		if (!(int)$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}

		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);

		$this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}
		}

		$output = '<ul class="pagination';

		if (isset($start) && $start == 1) {
			$output .= ' has-first';
		}

		if (isset($end) && $end == $num_pages) {
			$output .= ' has-last';
		}

		$output .= '">';

		if ($page > 1) {
			$output .= '<li class="first"><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_first . '</a></li>';
			
			if ($page - 1 === 1) {
				$output .= '<li class="prev"><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $this->text_prev . '</a></li>';
			} else {
				$output .= '<li class="prev"><a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a></li>';
			}
		}

		if ($num_pages > 1) {
			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= '<li class="active"><span>' . $i . '</span></li>';
				} else {
					if ($i === 1) {
						$output .= '<li><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . '">' . $i . '</a></li>';
					} else {
						$output .= '<li><a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
					}
				}
			}
		}

		if ($page < $num_pages) {
			$output .= '<li class="next"><a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a></li>';
			$output .= '<li class="last"><a href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a></li>';
		}

		$output .= '</ul>';

		if ($num_pages > 1) {
			return $output;
		} else {
			return '';
		}
	}

	public function getData() {
		$data = array();

		$total = $this->total;

		$data['total'] = $total;

		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}

		$data['page'] = $page;

		if (!(int)$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}

		$data['limit'] = $limit;

		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);

		$data['num_links'] = $num_links;
		$data['num_pages'] = $num_pages;

		$this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}
		}

		$data['start'] = isset($start) ? $start : 1;
		$data['end'] = isset($end) ? $end : 1;

		if (isset($start) && $start == 1) {
			$data['has_first'] = true;
		}

		if (isset($end) && $end == $num_pages) {
			$data['has_last'] = true;
		}

		$data['first_link'] = str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url);
		
		if ($page - 1 <= 1) {
			$data['prev_link'] = str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url);
		} else {
			$data['prev_link'] = str_replace('{page}', $page - 1, $this->url);
		}

		$links = array();

		if ($num_pages > 1) {
			for ($i = $start; $i <= $end; $i++) {
				if ($i === 1) {
					$link = str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url);
				} else {
					$link = str_replace('{page}', $i, $this->url);
				}

				$links[] = array(
					'page' => $i,
					'link' => $link,
				);
			}
		}

		$data['links'] = $links;

		if ($page < $num_pages) {
			$data['next_link'] = str_replace('{page}', $page + 1, $this->url);
		} else {
			$data['next_link'] = str_replace('{page}', $page, $this->url);
		}
		$data['last_link'] = str_replace('{page}', $num_pages, $this->url);

		return $data;
	}
}
