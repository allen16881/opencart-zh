<?php
class ControllerAccountAvatar extends Controller {
	public function upload() {
		$this->load->language('account/avatar');

		$json = array();

		if ($this->customer->isLogged() && !empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			$allowed = array('image/gif', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png');

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_file_type');
			}

			if ($this->request->files['file']['size'] > 2097152) {
				$json['error'] = $this->language->get('error_file_size');
			}

			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_file_type');
			}

			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$file = $this->customer->getId() . token(8) . '.png';

			$dir_id = intval($this->customer->getId() / 1000);

			$dir = DIR_IMAGE . 'avatar/' . $dir_id . '/';

			if (!file_exists($dir)) {
				@mkdir($dir, 0777, true);
			}

			move_uploaded_file($this->request->files['file']['tmp_name'], $dir . $file);

			$this->load->model('tool/image');

			$json['image'] = $this->model_tool_image->resize('avatar/' . $dir_id . '/' . $file, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_avatar_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_avatar_height'));

			$this->load->model('account/customer');

			$this->model_account_customer->editAvatar('avatar/' . $dir_id . '/' . $file);

			$json['success'] = $this->language->get('text_upload_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}