<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends Admin_Controller {
	protected $_table;
	protected $_module_title;
	protected $_templates;

	public function __construct() {
		parent::__construct();
		$this->load->library('form_validation');
		$this->_table					= 'settings';
		$this->_module_title			= 'настройка сайта';
		$this->_templates['index']		= 'settings/index';
	}

	public function index() {
		$settings		= $this->db->query("select * from {$this->_table}")->result_array();
		$small_image	= new Image_item((int)$settings[5]['value']);
		if ($small_image != false) {
			$settings[5]['logo']	=  IMAGESRC.'/settings/'.$small_image->getFilename();
		}
		if (!empty($_POST)) {
			$this->form_validation->set_error_delimiters('', '<br/>');
			$this->form_validation->set_message('required', 'поле "%s" незаполнено');
			$this->form_validation->set_rules('SITE_TITLE', '<b>название</b>','trim|required');
			if ($this->form_validation->run() != FALSE) {
				if (!empty($_FILES) && $_FILES['SITE_LOGO']['error'] == 0) {
					$image					= new Image_item();
					$image->doUpload(800, 800, 'SITE_LOGO', 'gif|jpg|png', 2048, 'settings');
					$image_id				= $image->Save();
					$settings[5]['value']	= $image_id;
				}
				$settings[0]['value']	= $this->input->post('SITE_TITLE');
				$settings[1]['value']	= $this->input->post('SITE_DESCRIPTION');
				$settings[2]['value']	= $this->input->post('SITE_KEYWORDS');
				$settings[3]['value']	= $this->input->post('EMAIL');
				$settings[4]['value']	= $this->input->post('MY_EMAIL');
				foreach ($settings as $setting) {
					$sql = "update {$this->_table} set value = {$this->db->escape($setting['value'])} where name = {$this->db->escape($setting['name'])}";
					$this->db->query($sql);
				}
			}
			$this->_view($this->_templates['index'], array('settings' => $settings,'module_title'  => $this->_module_title));
		} else {
			$this->_view($this->_templates['index'], array('settings' => $settings,'module_title'  => $this->_module_title));
		}

	}

}