<?php

class MY_Controller extends CI_Controller {

	protected $scripts = array();
	protected $css     = array();
	protected $menu    = array();

	public function __construct() {
		parent::__construct();
		//$this->output->enable_profiler(FALSE);;
	}

	protected function _view ( $view, $param = array() )
	{
		$custom_themes_directory = dirname(dirname(__FILE__)) . '/views/' . CUSTOM_THEMES_DIRECTORY . '/';
		$view_directory          = dirname(dirname(__FILE__)) . '/views/';

		$header = 'header';
		$footer = 'footer';

		// header template
		if ( file_exists($custom_themes_directory) && file_exists($custom_themes_directory . $view . '_header.php') )
		{
			$header = CUSTOM_THEMES_DIRECTORY . '/' . $view . '_header';
		} else if ( file_exists($custom_themes_directory) && file_exists($custom_themes_directory . $header . '.php') ) {
			$header = CUSTOM_THEMES_DIRECTORY . '/' . $header;
		} else if ( file_exists($view_directory . $view . '_header.php') ) {
			$header = $view . '_header';
		}
		// view
		if ( file_exists($custom_themes_directory) && file_exists($custom_themes_directory . $view . '.php') )
		{
			$view = CUSTOM_THEMES_DIRECTORY . '/' . $view;
		}
		// footer
		if ( file_exists($custom_themes_directory) && file_exists($custom_themes_directory . $view . '_footer.php') )
		{
			$footer = CUSTOM_THEMES_DIRECTORY . '/' . $view . '_footer';
		} else if ( file_exists($custom_themes_directory) && file_exists($custom_themes_directory . $footer . '.php') ) {
			$footer = CUSTOM_THEMES_DIRECTORY . '/' . $footer;
		} else if ( file_exists($view_directory . $view . '_footer.php') ) {
			$footer = $view . '_footer';
		}
		$this->load->view($header, $param);
		$this->load->view($view, $param);
		$this->load->view($footer, $param);
	}

	protected function _view_content ( $view, $vars = array(), $return = FALSE )
	{
		$custom_themes_directory = dirname(dirname(__FILE__)) . '/views/' . CUSTOM_THEMES_DIRECTORY . '/';
		if ( file_exists($custom_themes_directory) && file_exists($custom_themes_directory . $view . '.php') )
		{
		  $view = 'custom/' . $view;
		}
		return $this->load->view($view, $vars, $return);
	}

	protected function _get_pages_tree($map_array = array(), $list_tmp = '', $item_tmp = '', $active_id = 0, $params = array()) {
		if (!is_array($map_array)) return false;
		if (sizeof($map_array) == 0) return false;
		if (empty($list_tmp)) $list_tmp = $this->_templates['map_list'];
		if (empty($item_tmp)) $item_tmp = $this->_templates['map_item'];
		$map		= '';
		$max_level  = sizeof($map_array) - 1;
		for ($i = $max_level; $i >= 0 ; $i--) {
			if (!empty($map_array[$i]) && sizeof($map_array[$i]) > 0) {
				foreach ($map_array[$i] as $key => $page) {
					$j = $i+1;
					$tmp_submenu = '';
					if (!empty($html_menu[$j]) && sizeof($html_menu[$j]) > 0) {
						foreach($html_menu[$j] as $key => $subpage) {
							if ($page->id == $subpage['parent_id']) {
								$tmp_submenu .= $subpage['string'];
							}
						}
					}
					if (!empty($tmp_submenu)) $tmp_submenu = $this->_view_content($list_tmp, array('pages_block' => $tmp_submenu, 'params' => $params), true);
					$html_menu[$i][] = array(
											'parent_id' => $page->parent_id,
											'string'	=> $this->_view_content($item_tmp, array('page' => $page, 'submenu' => $tmp_submenu, 'level' => $i, 'active_id' => (int)$active_id,  'params' => $params), true),
											);
				}
			}
		}
		if (!empty($html_menu[0])) {
			foreach ($html_menu[0] as $menu_item) {
				$map .= $menu_item['string'];
			}
		} else {
			$map = '';
		}
		return $map;
	}
}

class Admin_Controller extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('auth_model');
		$this->menu = $this->config->item('admin_menu');

		if (!$this->auth_model->is_auth()) redirect (base_url().'admin/auth/login');
		$this->_is_auth	= $this->auth_model->is_auth();
		// CSS
		$this->css = array();
		$this->css[] = base_url('js/redactor/redactor.css');
		$this->css[] = base_url('js/plugins/formstyler/jquery.formstyler.css');
		$this->css[] = base_url('css/admin/style.css');
		// JS
		$this->scripts = array();
		$this->scripts[] = base_url('js/jquery-1.7.min.js');
		$this->scripts[] = base_url('js/plugins/formstyler/jquery.formstyler.min.js');
	}

	protected function _view ( $view, $param = array() ) {
		$param['css']     = $this->css;
		$param['scripts'] = $this->scripts;
		$param['menu']    = $this->menu;

		$this->load->view('admin/header', $param);
		$this->load->view('admin/'.$view);
		$this->load->view('admin/footer');
	}
}