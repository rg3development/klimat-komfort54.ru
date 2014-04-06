<?php

if ( ! defined('BASEPATH') )
{
    exit('No direct script access allowed');
}

class Catalog extends Admin_Controller
{
    protected $_module;
    protected $_templates;
    protected $_url;
    protected $_links;

    protected $template_data;
    protected $page_list;

    // section views
    const VIEW_CATALOG_INDEX  = 'catalog/section_index';
    const VIEW_SECTION_ADD    = 'catalog/section_add';
    const VIEW_SECTION_EDIT   = 'catalog/section_edit';
    // category views
    const VIEW_CATEGORY_INDEX = 'catalog/category_index';
    const VIEW_CATEGORY_ADD   = 'catalog/category_add';
    const VIEW_CATEGORY_EDIT  = 'catalog/category_edit';
    // item views
    const VIEW_ITEM_INDEX     = 'catalog/item_index';
    const VIEW_ITEM_ADD       = 'catalog/item_add';
    const VIEW_ITEM_EDIT      = 'catalog/item_edit';
    // image upload settings
    const IMAGE_MAX_WIDTH        = 1500;
    const IMAGE_MAX_HEIGHT       = 1500;
    const IMAGE_THUMB_MAX_WIDTH  = 224;
    const IMAGE_THUMB_MAX_HEIGHT = 224;
    const IMAGE_ALLOWED_TYPES    = 'gif|jpg|jpeg|png';
    const IMAGE_MAX_SIZE         = '2048';
    const IMAGE_UPLOAD_PATH      = 'catalog';


    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('custom/catalog/catalog_mapper', 'catalog_mapper');
        $this->load->model('page/page_mapper', 'page_mapper');

        $this->_module['title'] = 'Торговый каталог';

        // section templates
        $this->_templates['section_index'] = self::VIEW_CATALOG_INDEX;
        $this->_templates['section_add']   = self::VIEW_SECTION_ADD;
        $this->_templates['section_edit']  = self::VIEW_SECTION_EDIT;
        // category templates
        $this->_templates['category_index'] = self::VIEW_CATEGORY_INDEX;
        $this->_templates['category_add']   = self::VIEW_CATEGORY_ADD;
        $this->_templates['category_edit']  = self::VIEW_CATEGORY_EDIT;
        // items templates
        $this->_templates['item_add']   = self::VIEW_ITEM_ADD;
        $this->_templates['item_edit']   = self::VIEW_ITEM_EDIT;
        $this->_templates['item_index']   = self::VIEW_ITEM_INDEX;

        $this->_templates['page_select_list']   = 'admin/page_select_list';
        $this->_templates['page_select_item']   = 'admin/page_select_item';

        $this->_url['section_index'] = base_url() . 'admin/catalog';

        $this->_links['section_add'] = '/admin/catalog/section/add';
        $this->_links['category_add'] = '/admin/catalog/category/add';

        // TODO: need new TemplateData class; rewrite array to object
        $this->page_list = $this->page_mapper->get_all_pages();
        $currency_list = $this->catalog_mapper->get_currency_list();
    $this->template_data = array (
            'module' => array (
                'title' => $this->_module['title']
            ),
            'links' => array (
                'section_index' => $this->_url['section_index'],
                'section_add'   => $this->_links['section_add'],
                'category_add'   => $this->_links['category_add'],
            ),
            'currency_list' => $currency_list
        );
    }

    public function index ()
    {
        $catalog_section_list = $this->catalog_mapper->get_section_list();
        $this->_template_data('catalog_section_list', $catalog_section_list);
        $this->_view($this->_templates['section_index'], $this->template_data);
    }

    private function _category_index ( $object_id = 0 )
    {
        $category_list = $this->catalog_mapper->get_category_list($object_id);
        $section       = new Catalog_Section($object_id);
        $items = $this->catalog_mapper->get_section_item_list($object_id);

        $this->_template_data('section', $section);
        $this->_template_data('category_list', $category_list);
        $this->_template_data('item_list', $items);
        $this->_view($this->_templates['category_index'], $this->template_data);
    }

    public function section ( $request_type = 'none', $object_id = 0 )
    {
        switch ( $request_type )
        {
            case 'add':
                $this->_section_add();
                break;

            case 'edit':
                $this->_section_edit($object_id);
                break;

            case 'del':
                $this->_section_del($object_id);
                break;

            default:
                redirect($this->_url['section_index']);
                break;
        }
    }

    public function category ( $request_type = 'none', $object_id = 0, $optional = '' )
    {
        switch ( $request_type )
        {
            case 'list':
                $this->_category_index($object_id);
                break;

            case 'add':
                $this->_category_add($object_id);
                break;

            case 'edit':
                $this->_category_edit($object_id);
                break;

            case 'del':
                $this->_category_del($this->catalog_mapper->get_object($object_id, 'category'));
                break;

            case 'items':
                $this->_category_items($object_id);
                break;

            case 'unlink':
                $this->_items_unlink($optional, $object_id);
                break;

            default:
                redirect($this->_url['section_index']);
                break;
        }
    }

    public function imgdel ( $item_id = 0, $image_id = 0 )
    {
        $this->catalog_mapper->image_delete($item_id, $image_id);
        redirect($this->_url['section_index']);
    }

    public function items ( $request_type = 'none', $object_id = 0, $optional = '' )
    {
        switch ( $request_type )
        {
            case 'list':
                $this->_items_index($object_id);
                break;

            case 'add':
                $this->_items_add($object_id);
                break;

            case 'edit':
                $this->_items_edit($object_id);
                break;

            case 'del':
                $this->_items_del($this->catalog_mapper->get_object($object_id, 'item'));
                break;

            case 'unlink':
                $this->_items_unlink($object_id, $optional);
                break;

            default:
                redirect($this->_url['section_index']);
                break;
        }
    }

    private function _items_unlink ( $item_id, $category_id )
    {
        $this->catalog_mapper->unlink($item_id, $category_id);
        redirect($this->_url['section_index']);
    }

    private function _items_index ( $item_id )
    {
        $item = $this->catalog_mapper->get_object($item_id, 'item');
        $categories = $this->catalog_mapper->get_item_category_list($item_id);
        $this->_template_data('item', $item);
        $this->_template_data('is_item', TRUE);
        $this->_template_data('categories', $categories);
        $this->_view($this->_templates['item_index'], $this->template_data);
    }

    private function _category_items ( $category_id )
    {
        $category = $this->catalog_mapper->get_object($category_id, 'category');
        $items = $this->catalog_mapper->get_category_item_list($category_id);
        $this->_template_data('items', $items);
        $this->_template_data('is_item', FALSE);
        $this->_template_data('category', $category);
        $this->_view($this->_templates['item_index'], $this->template_data);
    }

    private function _section_add ( $parent_id = 0 )
    {
        if ( $this->input->post('cmd') )
        {
            $this->form_validation->set_rules('title', 'Название', 'trim|required');
        $this->form_validation->set_rules('per_page', 'Количество на страницу', 'required|integer');
        if ( $this->_set_form_validation_message('section_add') && $this->form_validation->run() )
        {
            $section = new Catalog_Section();
                $section->parent_page_id = $this->input->post('page_id');
                $section->title          = $this->input->post('title');
                $section->count_per_page = $this->input->post('per_page');
                $section->currency_id    = $this->input->post('currency_id');
                $section_id = $this->catalog_mapper->save($section);

                $uf_title = $this->input->post('uf_title');
                $uf_type  = $this->input->post('uf_type');
                if ( !empty($uf_type) && !empty($uf_title) )
                {
                    $this->catalog_mapper->user_field_add($section_id, $uf_title, $uf_type);
                }
            redirect($this->_url['section_index']);
        }
        }
        $page_list = $this->_get_pages_tree($this->page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], $parent_id);
        $this->_template_data('page_list', $page_list);
        $this->_view($this->_templates['section_add'], $this->template_data);
    }

    private function _category_add ( $parent_id )
    {
        if ( $this->input->post('cmd') )
        {
            $this->form_validation->set_rules('title', 'Название', 'trim|required');
        if ( $this->_set_form_validation_message('section_add') && $this->form_validation->run() )
        {
            $section = new Catalog_Category();
                $section->parent_category_id = $this->input->post('parent_category_id');
                $section->parent_section_id  = $this->input->post('parent_section_id');
                $section->title              = $this->input->post('title');
                $this->catalog_mapper->save($section);
            redirect($this->_url['section_index']);
        }
        }
        $section = new Catalog_Section($parent_id);
        $category_list = $this->catalog_mapper->get_category_list($parent_id);
        $this->_template_data('section', $section);
        $this->_template_data('category_list', $category_list);
        $this->_view($this->_templates['category_add'], $this->template_data);
    }

    private function _items_add ( $section_id )
    {
        if ( $this->input->post('cmd') )
        {
            $this->form_validation->set_rules('title', 'Название', 'trim|required');
            $this->form_validation->set_rules('article', 'Артикул', 'trim|required');
            $this->form_validation->set_rules('price', 'Цена', 'trim|required');
            $this->form_validation->set_rules('description', 'Описание', 'trim|required');
        if ( $this->_set_form_validation_message('item_add') && $this->form_validation->run() )
        {
            $item = new Catalog_Item();
                $item->title       = $this->input->post('title');
                $item->description = $this->input->post('description');
                $item->article     = $this->input->post('article');
                $item->price       = str_replace(',', '.', $this->input->post('price'));
                $item->section_id  = $section_id;
                $imgs_id = array();
                if ( isset($_FILES) && !empty($_FILES) )
                {
                    $img_count = $this->input->post('img_count');
                    for ( $i = 1; $i <= $img_count; $i++ )
                    {
                        $img_name = 'image_' . $i;
                        if ( $_FILES[$img_name]['error'] == 0 )
                        {
                            $image = new Image_item();
                            $image->doUpload(self::IMAGE_MAX_WIDTH, self::IMAGE_MAX_HEIGHT, $img_name, self::IMAGE_ALLOWED_TYPES, self::IMAGE_MAX_SIZE, self::IMAGE_UPLOAD_PATH);
                            $image_id = $image->save();
                            $imgs_id[] = $image_id;
                            $image->createThumbnail(self::IMAGE_THUMB_MAX_WIDTH, self::IMAGE_THUMB_MAX_HEIGHT, self::IMAGE_UPLOAD_PATH);
                        }
                    }
                }
                $item_id = $this->catalog_mapper->save($item);
                // add images
                $this->catalog_mapper->images_add($item_id, $imgs_id);
                // add category links
                $parent_category_list = $this->input->post('parent_category_id');
                $this->catalog_mapper->links_add($item_id, $parent_category_list);
                // add user field values
                $uf_values = $this->input->post('uf_values');
                $uf_ids    = $this->input->post('uf_ids');
                $uf_types  = $this->input->post('uf_types');
                if ( !empty($uf_values) && !empty($uf_ids) && !empty($uf_types) )
                {
                    $this->catalog_mapper->set_uf_values($item_id, $uf_values, $uf_ids, $uf_types);
                }
            redirect($this->_url['section_index']);
        }
        }
        $this->scripts[] = base_url().'js/redactor/redactor.js';

        $section = new Catalog_Section($section_id);
        $category_list = $this->catalog_mapper->get_category_list($section_id);
        $user_fields = $this->catalog_mapper->get_uf_list($section_id);
        $this->_template_data('section', $section);
        $this->_template_data('user_fields', $user_fields);
        $this->_template_data('category_list', $category_list);
        $this->_view($this->_templates['item_add'], $this->template_data);
    }

    private function _section_edit ( $object_id )
    {
        if ( $this->input->post('cmd') )
        {
            $this->form_validation->set_rules('title', 'Название', 'trim|required');
        $this->form_validation->set_rules('per_page', 'Количество на страницу', 'required|integer');
        if ( $this->_set_form_validation_message('section_add') && $this->form_validation->run() )
        {
            $section = new Catalog_Section($object_id);
                $section->parent_page_id = $this->input->post('page_id');
                $section->title          = $this->input->post('title');
                $section->count_per_page = $this->input->post('per_page');
                $section->currency_id    = $this->input->post('currency_id');
                $this->catalog_mapper->save($section);
                // new user fields
                $uf_title = $this->input->post('uf_title');
                $uf_type  = $this->input->post('uf_type');
                if ( !empty($uf_type) && !empty($uf_title) )
                {
                    $this->catalog_mapper->user_field_add($section->id, $uf_title, $uf_type);
                }
                // old user fields
                $cur_uf_title = $this->input->post('cur_uf_title');
                $cur_uf_id    = $this->input->post('cur_uf_id');
                if ( !empty($cur_uf_title) && !empty($cur_uf_id) )
                {
                    $this->catalog_mapper->user_field_upd($section->id, $cur_uf_title, $cur_uf_id);
                }
            redirect($this->_url['section_index']);
        }
        }
        $section = new Catalog_Section($object_id);
        $page_list = $this->_get_pages_tree($this->page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], $section->parent_page_id);
        $user_fields = $this->catalog_mapper->get_uf_list($section->id);
        $this->_template_data('section', $section);
        $this->_template_data('user_fields', $user_fields);
        $this->_template_data('page_list', $page_list);
        $this->_view($this->_templates['section_edit'], $this->template_data);
    }

    private function _category_edit ( $object_id )
    {
        $category = $this->catalog_mapper->get_object($object_id, 'category');
        $section = new Catalog_Section($category->parent_section_id);
        $category_list = $this->catalog_mapper->get_category_list($category->parent_section_id);
        $this->_template_data('section', $section);
        $this->_template_data('category', $category);
        $this->_template_data('category_list', $category_list);
        $this->_view($this->_templates['category_edit'], $this->template_data);
    }

    private function _items_edit ( $object_id  )
    {
        if ( $this->input->post('cmd') )
        {
            $this->form_validation->set_rules('title', 'Название', 'trim|required');
            $this->form_validation->set_rules('article', 'Артикул', 'trim|required');
            $this->form_validation->set_rules('price', 'Цена', 'trim|required');
            $this->form_validation->set_rules('description', 'Описание', 'trim|required');
        if ( $this->_set_form_validation_message('item_add') && $this->form_validation->run() )
        {
            // save new item
            $item = $this->catalog_mapper->get_object($object_id, 'item');
                $item->title       = $this->input->post('title');
                $item->description = $this->input->post('description');
                $item->article     = $this->input->post('article');
                $item->price       = str_replace(',', '.', $this->input->post('price'));
                $this->catalog_mapper->save($item);
                // images
                $imgs_id = array();
                if ( isset($_FILES) && !empty($_FILES) )
                {
                    $img_count = $this->input->post('img_count');
                    for ( $i = 1; $i <= $img_count; $i++ )
                    {
                        $img_name = 'image_' . $i;
                        if ( $_FILES[$img_name]['error'] == 0 )
                        {
                            $image = new Image_item();
                            $image->doUpload(self::IMAGE_MAX_WIDTH, self::IMAGE_MAX_HEIGHT, $img_name, self::IMAGE_ALLOWED_TYPES, self::IMAGE_MAX_SIZE, self::IMAGE_UPLOAD_PATH);
                            $image_id = $image->save();
                            $imgs_id[] = $image_id;
                            $image->createThumbnail(self::IMAGE_THUMB_MAX_WIDTH, self::IMAGE_THUMB_MAX_HEIGHT, self::IMAGE_UPLOAD_PATH);
                        }
                    }
                }
                $this->catalog_mapper->images_edit($item->id, $imgs_id);
                // links
                $parent_category_list = $this->input->post('parent_category_id');
                if ( ! $parent_category_list )
                {
                    $parent_category_list = array();
                }
                $this->catalog_mapper->links_edit($item->id, $parent_category_list);
                // add user field values
                $form_type = $this->input->post('form_type');
                $uf_values = $this->input->post('uf_values');
                $uf_ids    = $this->input->post('uf_ids');
                $uf_types  = $this->input->post('uf_types');
                if ( !empty($uf_values) && !empty($uf_ids) && !empty($uf_types) )
                {
                    if ( $form_type )
                    {
                        $this->catalog_mapper->set_uf_values($item->id, $uf_values, $uf_ids, $uf_types);
                    } else {
                        $this->catalog_mapper->upd_uf_values($item->id, $uf_values, $uf_ids, $uf_types);
                    }
                }
            redirect($this->_url['section_index']);
        }
        }
        $this->scripts[] = base_url().'js/redactor/redactor.js';

        $item = $this->catalog_mapper->get_object($object_id, 'item');
        $item_links = $this->catalog_mapper->get_item_links($item->id);
        $item_images = $this->catalog_mapper->get_item_images($item->id);
        $section = new Catalog_Section($item->section_id);
        $category_list = $this->catalog_mapper->get_category_list($section->id);
        $user_values = $this->catalog_mapper->get_uf_values($section->id, $item->id);
        $user_fields = $this->catalog_mapper->get_uf_list($section->id);
    // $this->_debug($user_fields);
        $this->_template_data('user_values', $user_values);
        $this->_template_data('user_fields', $user_fields);
        $this->_template_data('item', $item);
        $this->_template_data('item_links', $item_links);
        $this->_template_data('item_images', $item_images);
        $this->_template_data('section', $section);
        $this->_template_data('category_list', $category_list);
        $this->_view($this->_templates['item_edit'], $this->template_data);
    }

    private function _section_del ( $object_id )
    {
        $this->catalog_mapper->delete($object_id, 'section');
        redirect($this->_url['section_index']);
    }

    private function _category_del ( $object )
    {
        $this->catalog_mapper->delete($object, 'category');
        redirect($this->_url['section_index']);
    }

    private function _items_del ( $object )
    {
        $this->catalog_mapper->delete($object, 'item');
        redirect($this->_url['section_index']);
    }

    private function _set_form_validation_message ( $config )
  {
    $this->form_validation->set_error_delimiters('', '<br />');
      switch ( $config )
      {
        case 'section_add':
          $this->form_validation->set_message('required', 'Поле "%s" обязательно для заполнения.');
          $this->form_validation->set_message('integer', 'Поле "%s" должно содержать только цифры.');
      break;

      case 'item_add':
        $this->form_validation->set_message('required', 'Поле "%s" обязательно для заполнения.');
        break;

      default:
        return FALSE;
    }
    return TRUE;
  }

  private function _template_data ( $data = array(), $data_value = NULL )   // TODO: need new TemplateData class; rewrite array to object
  {
        if ( is_array($data) && ! empty($data) )
        {
            $this->template_data[] = $data;
        } else {
            $this->template_data[$data] = $data_value;
        }
  }



    // ========================================================
  private function _debug( $value, $exit = true )
    {
        print('<pre>');
        print_r($value);
        print('</pre>');
        if ( $exit )
        {
            exit();
        }
    }
    // ========================================================










    public function get_parent_ids ($id) {
        //Вычисление верхнего уровня каталога и уровня родителя.
        $tmp = $this->_catalog_mapper->get_category($id);
        $tmp->depth ? $parent_id = $tmp->parent_id : $parent_id = 0;
        while ( $tmp->depth > 0 ) {
            $tmp = $this->_catalog_mapper->get_category($tmp->parent_id);
        }
        $this->session->set_userdata('root_id', $tmp->id);
        $this->session->set_userdata('parent_id', $parent_id);
        unset ($tmp);
        return 0;
    }

    public function add_item($parent_id = 0) {
        // echo "<pre>"; print_r($_POST);die();
        $this->form_validation->set_error_delimiters('', '<br/>');
        $this->form_validation->set_message('required', 'поле "%s" незаполнено');
        $this->form_validation->set_rules('title', '<b>название</b>','trim|required');
        $this->form_validation->set_rules('price', '<b>цена</b>','required');
        $page_list          = $this->_page_mapper->get_all_pages();
        $page_select        = $this->_get_pages_tree($page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], (int)$parent_id);
        $item               = new Catalog_item();

        $this->get_parent_ids($parent_id);

        if ($this->form_validation->run()) {
            if (!empty($_FILES) && $_FILES['image1']['error'] == 0) {
                $image1 = new Image_item();
                $image1->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image1', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                $image1_id = $image1->Save();
                $image1->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
            }
            if (!empty($_FILES) && $_FILES['image2']['error'] == 0) {
                $image2 = new Image_item();
                $image2->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image2', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                $image2_id = $image2->Save();
                $image2->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
            }
            if (!empty($_FILES) && $_FILES['image3']['error'] == 0) {
                $image3 = new Image_item();
                $image3->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image3', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                $image3_id = $image3->Save();
                $image3->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
            }
            if (!empty($_FILES) && $_FILES['image4']['error'] == 0) {
                $image4 = new Image_item();
                $image4->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image4', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                $image4_id = $image4->Save();
                $image4->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
            }
            $item->depth            = ++$this->_catalog_mapper->get_category($parent_id)->depth;
            $item->parent_id        = (int)$parent_id;
            $item->price            = $this->input->post('price');
            $item->discount         = $this->input->post('discount');
            $item->title            = $this->input->post('title');
            $item->image1_id        = $image1_id;
            $item->image2_id        = $image2_id;
            $item->image3_id        = $image3_id;
            $item->image4_id        = $image4_id;
            $item->description      = trim($this->input->post('description'));
            $this->_catalog_mapper->save($item, 'item');
            $parent_id = (int)$parent_id > 0 ? $parent_id = (int)$parent_id : '';
            redirect(base_url().'admin/catalog/edit/'.$parent_id);
        }

        $data                       = array();
        $data['parent_id']          = (int)$parent_id;
        $data['module_title']       = $this->_module_title;
        $data['page_select']        = $page_select;
        $data['root_id']            = $this->session->userdata('root_id');

        $this->_view($this->_templates['catalog_add'], $data);
    }

    public function edit_item($id = 0, $parent_id = 0) {
        // echo "<pre>"; print_r($_POST);die();
        if ((int)$id == 0) redirect('/admin/catalog/');
        $item                   = $this->_catalog_mapper->get_object($id);
        $image1             = new Image_item($item->image1_id);
        $image2             = new Image_item($item->image2_id);
        $image3             = new Image_item($item->image3_id);
        $image4             = new Image_item($item->image4_id);
        $item_data = array();
        $item_data['id']                = $item->id;
        $item_data['title']         = $item->title;
        $item_data['description']   = $item->description;
        $item_data['price']         = $item->price;
        $item_data['discount']      = $item->discount;
        $item_data['path']          = $this->_path_to_image;
        $item_data['image1']            = $image1->getFilename();
        $item_data['image2']            = $image2->getFilename();
        $item_data['image3']            = $image3->getFilename();
        $item_data['image4']            = $image4->getFilename();
        $page_list              = $this->_page_mapper->get_all_pages();
        $page_select            = $this->_get_pages_tree($page_list, $this->_templates['page_select_list'], $this->_templates['page_select_item'], $item->parent_id);

        $catalog_list_raw       = $this->_catalog_mapper->get_data_tree(0);
        $catalog_list           = '';

        $tmp = $this->_catalog_mapper->get_object($id);
        $this->get_parent_ids($tmp->parent_id);
        $parent_id = $tmp->parent_id;
        unset ($tmp);

        //Парсинг списка каталогов. Вынести нахер отсюда.
        foreach ($catalog_list_raw as $key => $object) {
            if ( get_class($object) != "Catalog_category" ) {
                continue;
            }
            $catalog_list .= '<option value=';
            $catalog_list .= '"'.$object->id.'"';
            if ($object->id == $item->parent_id) {
                $catalog_list .= 'selected="selected"';
            }
            $catalog_list .= '>';
            for ($i = 0; $i < (int)$object->depth; $i++) {
                $catalog_list .= '-';
            }
            $catalog_list .= '&nbsp;'.$object->title;
            $catalog_list .= '</option>';
        }

        if (!empty($_POST)) {
            $this->form_validation->set_error_delimiters('', '<br/>');
            $this->form_validation->set_message('required', 'поле "%s" незаполнено');
            $this->form_validation->set_rules('title', '<b>название</b>','trim|required');
            if ($this->form_validation->run() != FALSE) {
                if (!empty($_FILES) && $_FILES['image1']['error'] == 0) {
                    $image1             = new Image_item();
                    $image1->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image1', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                    $image1_id = $image1->Save();
                    $image1->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
                    $item->image1_id    = $image1_id;
                }
                if (!empty($_FILES) && $_FILES['image2']['error'] == 0) {
                    $image2             = new Image_item();
                    $image2->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image2', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                    $image2_id = $image2->Save();
                    $image2->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
                    $item->image2_id    = $image2_id;
                }
                if (!empty($_FILES) && $_FILES['image3']['error'] == 0) {
                    $image3             = new Image_item();
                    $image3->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image3', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                    $image3_id = $image3->Save();
                    $image3->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
                    $item->image3_id    = $image3_id;
                }
                if (!empty($_FILES) && $_FILES['image4']['error'] == 0) {
                    $image4             = new Image_item();
                    $image4->doUpload($this->_sub_image['w'], $this->_sub_image['h'], 'image4', 'gif|jpg|jpeg|png', $this->_max_size_image, 'catalog');
                    $image4_id = $image4->Save();
                    $image4->createThumbnail($this->_image_thumbnail['w'], $this->_image_thumbnail['h'], 'catalog');
                    $item->image4_id    = $image4_id;
                }
                $item->parent_id        = $this->input->post('parent_id');
                $item->price            = $this->input->post('price');
                $item->discount         = $this->input->post('discount');
                $item->depth            = ++$this->_catalog_mapper->get_category($item->parent_id)->depth;
                $item->title            = $this->input->post('title');
                $item->description      = trim($this->input->post('description'));
                $this->_catalog_mapper->save($item, 'item');
                redirect(base_url().'admin/catalog/edit/'.$parent_id);
            }
        }

        $data       = array();
        $data['catalog_list']   = $catalog_list;
        $data['data']           = $item_data;
        $data['module_title']   = $this->_module_title;
        $data['page_select']    = $page_select;
        $data['root_id']        = $this->session->userdata('root_id');
        $data['parent_id']      = (int)$parent_id;
        $this->_view($this->_templates['catalog_edit'], $data);
    }

    public function delete_item($id) {
        $parent_id = $this->_catalog_mapper->get_object($id)->parent_id;
        $this->_catalog_mapper->delete((int)$id, 'item');
        redirect(base_url().'admin/catalog/edit/'.$parent_id);
    }

    public function delete_image($image_num = 1, $id, $parent_id) {
        $item = $this->_catalog_mapper->get_object((int)$id);
        $image_del      = 'image'.$image_num.'_id';
        $item->{$image_del} = 0;
        $this->_catalog_mapper->save($item, 'item');
        redirect(base_url().'admin/catalog/edit_item/'.(int)$id.'/'.(int)$parent_id);
    }

    public function prioritydown($id, $parent_id) {
        $this->_catalog_mapper->to_down($id, $parent_id);
        redirect(base_url().'admin/catalog/');
    }

    public function priorityup($id, $parent_id) {
        $this->_catalog_mapper->to_up($id, $parent_id);
        redirect(base_url().'admin/catalog/');
    }
}
