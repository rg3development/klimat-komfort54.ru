<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Text extends Admin_Controller {

    protected $_module_title;
    protected $_templates;

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->_module_title                    = 'текстовый блок';
        $this->_templates['index']                = 'text/index';
        $this->_templates['add']                = 'text/add';
        $this->_templates['edit']                = 'text/edit';
        $this->_templates['page_select_list']    = 'admin/page_select_list';
        $this->_templates['page_select_item']    = 'admin/page_select_item';
    }

    public function index() {
        $parent_id      = !empty($_REQUEST['parent_id']) ? (int)$_REQUEST['parent_id'] : 0;
        $page_list      = $this->page_mapper->get_all_pages();
        $page_select    = $this->_get_pages_tree($page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], $parent_id);
        $list_text      = $this->text_mapper->get_all_objects($parent_id, 'title asc');
        $this->_view(
                    $this->_templates['index'],
                    array(
                        'page_select'   => $page_select,
                        'module_title'  => $this->_module_title,
                        'list_text'     => $list_text,
                        'parent_id'     => $parent_id
                         )
                    );
    }

    public function add($parent_id = 0) {
        $this->scripts[]    = base_url().'js/plugins/ckeditor/ckeditor.js';
        $this->scripts[]    = base_url().'js/plugins/ckeditor/ckfinder.js';
        $this->css[]        = base_url().'js/plugins/ckeditor/sample.css';
        $this->form_validation->set_error_delimiters('', '<br/>');
        $this->form_validation->set_message('required', 'поле "%s" незаполнено');
        $this->form_validation->set_rules('title', '<b>название</b>','trim|required');
        $page_list      = $this->page_mapper->get_all_pages();
        $page_select    = $this->_get_pages_tree($page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], (int)$parent_id);
        $new_text       = new Text_item();
        if ($this->form_validation->run() != false) {
            $new_text->parent_id        = $this->input->post('parent_id');
            $new_text->title            = $this->input->post('title');
            $new_text->show_title       = $this->input->post('show_title') == 'on' ? 1 : 0;
            $new_text->description      = $this->input->post('description');
            $new_text->description      = str_replace('<div>', '<p>', $new_text->description);
            $new_text->description      = str_replace('</div>', '</p>', $new_text->description);
            $new_id = $this->text_mapper->save($new_text);
            $parent_id = (int)$parent_id > 0 ? $parent_id = '?parent_id='.(int)$parent_id : '';
            redirect(base_url().'admin/text/'.$parent_id);
        }
        $this->_view($this->_templates['add'], array('parent_id' => (int)$parent_id, 'page_select' => $page_select));
    }

    public function edit($id = 0, $parent_id = 0) {
        if ((int)$id == 0) redirect(base_url().'admin/textblock/');
        $this->scripts[]    = base_url().'js/plugins/ckeditor/ckeditor.js';
        $this->scripts[]    = base_url().'js/plugins/ckeditor/ckfinder.js';
        $this->css[]        = base_url().'js/plugins/ckeditor/sample.css';
        $edit_text          = $this->text_mapper->get_object($id);
        $data = array();
        $data['id']           = $edit_text->id;
        $data['parent_id']    = $edit_text->parent_id;
        $data['title']        = $edit_text->title;
        $data['show_title']   = $edit_text->show_title;
        $data['description']  = $edit_text->description;
        $page_list            = $this->page_mapper->get_all_pages();
        $page_select          = $this->_get_pages_tree($page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], $edit_text->parent_id);
        if (!empty($_POST)) {
            $this->form_validation->set_error_delimiters('', '<br/>');
            $this->form_validation->set_message('required', 'поле "%s" незаполнено');
            $this->form_validation->set_rules('title', '<b>название</b>','trim|required');
            if ($this->form_validation->run() != false) {
                $edit_text->parent_id   = $this->input->post('parent_id');
                $edit_text->title       = $this->input->post('title');
                $edit_text->show_title  = $this->input->post('show_title') == 'on' ? 1 : 0;
                $edit_text->description = $this->input->post('description');
                $edit_text->description = str_replace('<div>', '<p>', $edit_text->description);
                $edit_text->description = str_replace('</div>', '</p>', $edit_text->description);
                $this->text_mapper->save($edit_text);
                redirect(base_url().'admin/text/?page_id='.$parent_id.'#textblock'.(int)$id);
            }
            $this->_view($this->_templates['edit'], array('parent_id' => (int)$parent_id, 'data' => $data, 'textblock' => $edit_text, 'page_select' => $page_select));
        } else {
            $this->_view($this->_templates['edit'], array('parent_id' => (int)$parent_id, 'data' => $data, 'textblock' => $edit_text, 'page_select' => $page_select));
        }
    }

    public function delete($id, $parent_id) {
        $this->text_mapper->delete($id);
        redirect(base_url().'admin/text/?parent_id='.(int)$parent_id.'#text'.$id);
    }

    public function copy($id, $parent_id) {
        if ((int)$id == 0) redirect(base_url().'admin/textblock/');
        $edit_text              = $this->text_mapper->get_object($id);
        $data = array();
        $data['id']             = $edit_text->id;
        $data['parent_id']      = $edit_text->parent_id;
        $data['title']          = $edit_text->title; 
        $data['show_title']     = $edit_text->show_title;
        $data['description']    = $edit_text->description;
        $new_text                   = new Text_item();
        $new_text->parent_id        = 0;
        $new_text->title            = $data['title'].'-КОПИЯ';
        $new_text->show_title       = $data['show_title'];
        $new_text->description      = $data['description'];
        $new_id = $this->text_mapper->save($new_text);
        $parent_id = (int)$parent_id > 0 ? $parent_id = '?parent_id='.(int)$parent_id : '';
        redirect(base_url().'admin/text/'.$parent_id);
        redirect(base_url().'admin/text/?parent_id='.(int)$parent_id.'#text'.$id);
    }
}