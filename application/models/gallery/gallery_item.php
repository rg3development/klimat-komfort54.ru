<?php
/*
 * Model of gallery item
 *
 * @author rav <arudyuk@gmail.com>
 * @version 1.0
 */

class Gallery_item extends MY_Model_Item {

	public function __construct($id = 0) {
		parent::__construct();
		$this->_info['id']				= 0;
		$this->_info['parent_id']		= 0;
		$this->_info['title']			= '';
		$this->_info['description']		= '';
		$this->_info['link']			= '';
		$this->_info['image_id']		= 0;
		$this->_info['created']			= time();
	}

	public function __set($name, $value) {
		if (isset($this->_info[$name])) {
			if ($name == 'title' || $name == 'description' || $name == 'link') $this->_info[$name] = trim($value);
			else $this->_info[$name] = (int)$value;
		} else {
			return parent::__set($name, $value);
		}
	}
}