<?php
class ModelToolString extends Model {
	public function cleanHtml($content) {
		$content = strip_tags($content, '<img>');
		return $content;
	}
		
}