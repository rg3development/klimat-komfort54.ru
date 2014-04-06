<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Map extends Admin_Controller {

	protected $_module_title;
	protected $_templates;
	protected $_page_mapper;
	protected $_panager_modules;
	protected $_page_templates;

	public function __construct() {
		parent::__construct();

		$this->_module_title = 'карта сайта';
		$this->_templates['index']            = 'map/index';
		$this->_templates['add']              = 'map/add';
		$this->_templates['edit']             = 'map/edit';
		$this->_templates['map_item']         = 'admin/map/map_item';
		$this->_templates['map_list']         = 'admin/map/map_list';
		$this->_templates['page_select_list'] = 'admin/page_select_list';
		$this->_templates['page_select_item'] = 'admin/page_select_item';

		$this->_page_mapper     = new Page_mapper();
		$this->_manager_modules = new Manager_modules();
		$this->_page_templates  = $this->config->item('page_templates');
	}

	public function index() {
		$map_array   = $this->_page_mapper->get_all_pages();
		$map		 = $this->_get_pages_tree($map_array);
		$this->_view($this->_templates['index'], array('map' => $map, 'module_title' => $this->_module_title));
	}

	public function add() {
		$this->scripts[]	= '/js/admin/ui/jquery-ui-1.8.16.custom.min.js';
		$this->form_validation->set_error_delimiters('', '<br/>');
		$this->form_validation->set_message('required', 'Поле "%s" незаполнено');
		$this->form_validation->set_message('_check_url_exists', 'Такой %s уже существует');
		$this->form_validation->set_rules('title', 'название','trim|required');
		$this->form_validation->set_rules('url', 'url','trim|required|callback__check_url_exists');
		$new_page		= new Page_item();
		$list_modules	= $this->_manager_modules->get_list_module();
		if ($this->form_validation->run() != FALSE) {
			$show		= !empty($_POST['show']) && $_POST['show'] == 'on' ? 1 : 0;
			$parent_id  = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
			$modules	= isset($_POST['active_form']) && sizeof($_POST['active_form']) > 0 ? $_POST['active_form'] : '';
			if (!empty($_FILES) && !empty($_FILES['image_bottom']) && $_FILES['image_bottom']['error'] == 0) {
				$new_image = new Image_item();
				$new_image->doUpload(800, 800, 'image_bottom', 'gif|jpg|png', 2048, 'page');
				$new_id = $new_image->Save();
				$new_page->image_bottom = $new_id;
				unset($new_image);
			}
			$new_page->parent_id   = $parent_id;
			$new_page->url         = $this->input->post('url');
			$new_page->meta        = $this->input->post('meta');
			$new_page->description = $this->input->post('description');
			$new_page->keywords    = $this->input->post('keywords');
			$new_page->title       = $this->input->post('title');
			$new_page->show_title  = $this->input->post('show_title') == 'on' ? 1 : 0;
			$new_page->alias       = $this->input->post('alias');
			$new_page->show_alias  = $this->input->post('show_alias') == 'on' ? 1 : 0;
			$new_page->template    = $this->input->post('template');
			$new_page->show        = $show;
			$new_page_id           = $this->_page_mapper->save_page($new_page);
			$this->_manager_modules->set_page_module($new_page_id, $modules);
			redirect(base_url().'admin/map/');
		}
		$page_list		= $this->_page_mapper->get_all_pages();
		$page_select	= $this->_get_pages_tree($page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item']);
		$this->_view(
			$this->_templates['add'],
			array (
				'module_title'  => $this->_module_title,
				'page_select'   => $page_select,
				'list_modules'  => $list_modules,
				'active_modules'=> array(),
				'page_templates'=> $this->_page_templates
			)
		);
	}

	public function edit($id = 0) {
		$this->scripts[]	= '/js/admin/ui/jquery-ui-1.8.16.custom.min.js';
		$id = (int)$id;
		if ($id == 0) redirect(base_url().'admin/map/');
		$edit_page				= $this->_page_mapper->get_page($id);
		$edit_image_bottom		= new Image_item($edit_page->image_bottom);
		$data = array();
		$data['id']            = $id;
		$data['title']         = $edit_page->title;
		$data['show_title']    = $edit_page->show_title;
		$data['alias']         = $edit_page->alias;
		$data['show_alias']    = $edit_page->show_alias;
		$data['url']           = $edit_page->url;
		$data['parent_id']     = $edit_page->parent_id;
		$data['meta']          = $edit_page->meta;
		$data['keywords']      = $edit_page->keywords;
		$data['description']   = $edit_page->description;
		$data['show']          = $edit_page->show;
		$data['template']      = $edit_page->template;
		$data['path_to_image'] = $this->_page_mapper->get_path_to_image();
		$data['image_bottom']  = $edit_image_bottom->getFilename();
		$page_list       = $this->_page_mapper->get_all_pages();
		$page_select     = $this->_get_pages_tree($page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], $edit_page->parent_id);
		$list_modules    = $this->_manager_modules->get_list_module();
		$active_modules  = $this->_manager_modules->get_page_module($edit_page->id);
		$noactive_module = $this->_check_list_modules($list_modules, $active_modules);
		if (!empty($_POST)) {
			$this->form_validation->set_error_delimiters('', '<br/>');
			$this->form_validation->set_message('required', 'Поле "%s" незаполнено');
			$this->form_validation->set_message('_check_url_exists', 'Такой %s уже существует');
			$this->form_validation->set_rules('title', 'название','trim|required');
			$this->form_validation->set_rules('url', 'url','trim|required|callback__check_url_exists');
			if ($this->form_validation->run() != FALSE) {
				$show	   = !empty($_POST['show']) && $_POST['show'] == 'on' ? 1 : 0;
				$parent_id  = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
				$modules	= isset($_POST['active_form']) && sizeof($_POST['active_form']) > 0 ? $_POST['active_form'] : '';
				if (!empty($_FILES) && !empty($_FILES['image_bottom']) && $_FILES['image_bottom']['error'] == 0) {
					$edit_image = new Image_item();
					$edit_image->doUpload(800, 800, 'image_bottom', 'gif|jpg|png', 2048, 'page');
					$edit_id = $edit_image->Save();
					$edit_page->image_bottom = $edit_id;
					unset($edit_image);
				}
				$edit_page->parent_id   = $parent_id;
				$edit_page->title       = $this->input->post('title');
				$edit_page->show_title  = $this->input->post('show_title') == 'on' ? 1 : 0;
				$edit_page->alias       = $this->input->post('alias');
				$edit_page->show_alias  = $this->input->post('show_alias') == 'on' ? 1 : 0;
				$edit_page->url         = $this->input->post('url');
				$edit_page->meta        = $this->input->post('meta');
				$edit_page->keywords    = $this->input->post('keywords');
				$edit_page->description = $this->input->post('description');
				$edit_page->template    = $this->input->post('template');
				$edit_page->show        = $show;
				$this->_page_mapper->save_page($edit_page);
				$this->_manager_modules->remove_page_module($edit_page->id);
				$this->_manager_modules->set_page_module($edit_page->id, $modules);
				redirect(base_url().'admin/map/');
			}
			$this->_view(
					$this->_templates['edit'],
					array(
						'module_title'  => $this->_module_title,
						'data'		  => $data,
						'page_select'   => $page_select,
						'list_modules'  => $noactive_module,
						'active_modules'=> $active_modules,
						'page_templates'=> $this->_page_templates
						)
					);
		} else {
			$this->_view(
					$this->_templates['edit'],
					array(
						'module_title' => $this->_module_title,
						'data' => $data,
						'page_select' => $page_select,
						'list_modules'  => $noactive_module,
						'active_modules'=> $active_modules,
						'page_templates'=> $this->_page_templates
						)
					);
		}
	}

	public function delete($id) {
		$id = (int)$id;
		$this->_page_mapper->delete_page($id);
		redirect(base_url().'admin/map/');
	}

	public function prioritydown($id) {
		$id = (int)$id;
		$this->_page_mapper->page_to_down($id);
		redirect(base_url().'admin/map/#page'.$id);
	}

	public function priorityup($id) {
		$id = (int)$id;
		$this->_page_mapper->page_to_up($id);
		redirect(base_url().'admin/map/#page'.$id);
	}

	public function editmodules($id) {
		echo 'Управление модулями на странице';
	}

	public function _check_list_modules($list_modules = array() , $active_modules = array()) {
		$res = array();
		foreach ($list_modules as $list_module) {
			$flag = false;
			foreach ($active_modules as $active_module) {
				if ($active_module['id'] == $list_module['id']) {
					$flag = true;
				}
			}
			if (!$flag) $res[] = $list_module;
		}
		return $res;
	}

	public function _check_url_exists($url) {
		$page_id = (int)$this->uri->segment(4);
		return $this->_page_mapper->check_url_exist($page_id, $url);
	}

	public function delete_image_bottom($id) {
		$edit_page = $this->_page_mapper->get_page((int)$id);
		$edit_page->image_bottom = 0;
		$this->_page_mapper->save_page($edit_page);
		redirect(base_url().'admin/map/edit/'.(int)$id);
	}

	// upload image for imperavi editor
	public function imeravi_upload_image() {
		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		if ($_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/jpg' || $_FILES['file']['type'] == 'image/gif' || $_FILES['file']['type'] == 'image/jpeg'|| $_FILES['file']['type'] == 'image/pjpeg') {
			$file = md5(date('YmdHis')).'.jpg';
			copy($_FILES['file']['tmp_name'], EDITORPATH.$file);
			$array = array('filelink' => EDITORSRC.$file);
			echo stripslashes(json_encode($array));
		}
	}

	// upload file for imperavi editor
	public function imeravi_upload_file() {
		copy($_FILES['file']['tmp_name'], EDITORPATH.$_FILES['file']['name']);
		$array = array(
			'filelink' => EDITORSRC.$_FILES['file']['name'],
			'filename' => $_FILES['file']['name']
		);
		echo stripslashes(json_encode($array));
	}
}