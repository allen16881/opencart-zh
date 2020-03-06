<?php
class ControllerCommonSearch extends Controller {
	public function index() {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['keyword'])) {
			$data['keyword'] = $this->request->get['keyword'];
		} else {
			$data['keyword'] = '';
		}

		$data['search'] = $this->url->link('product/search', '', true);

		return $this->load->view('common/search', $data);
	}
}