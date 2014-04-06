<?php

define('PATH_TO_IMAGE', IMAGESRC . 'catalog');
define('COUNT_PER_PAGE', 6);

class Catalog_mapper extends MY_Model implements Mapper
{
  // table constants
  const TABLE_CATALOG_SECTION     = 'catalog_section';
  const TABLE_CATALOG_CATEGORY    = 'catalog_category';
  const TABLE_CATALOG_CURRENCY    = 'catalog_currency';
  const TABLE_CATALOG_ITEM        = 'catalog_item';
  const TABLE_CATALOG_ITEM_IMAGES = 'catalog_item_images';
  const TABLE_CATALOG_ITEM_LINKS  = 'catalog_item_links';
  const TABLE_CATALOG_USER_FIELDS = 'catalog_user_fields';
  const TABLE_CATALOG_USER_VALUES = 'catalog_user_values';
  const TABLE_IMAGES              = 'images';
  const TABLE_PAGES               = 'pages';
  // class for result object
  const CLASS_SECTION      = 'Catalog_Section';
  const CLASS_CATEGORY     = 'Catalog_Category';
  const CLASS_ITEM         = 'Catalog_Item';

  public function  __construct ()
  {
    parent::__construct();
    $this->_path_to_image      = PATH_TO_IMAGE;
    $this->_template['show']   = 'catalog/index';
    $this->_template['detail'] = 'catalog/detail';
    $this->load->model('catalog/catalog_category');
    $this->load->model('catalog/catalog_item');
    $this->load->model('catalog/catalog_section');
  }

  // получить список разделов каталога
  public function get_section_list ( $order_title = 'id', $order_type = 'DESC' )
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_SECTION);
    $this->db->where('is_deleted !=', 1);
    $this->db->order_by($order_title, $order_type);
    return $this->db->get()->result(self::CLASS_SECTION);
  }

  // получить список категорий раздела
  public function get_category_list ( $section_id = 0, $order_title = 'id', $order_type = 'DESC' )
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_CATEGORY);
    $this->db->where('parent_section_id', $section_id);
    $this->db->where('is_deleted !=', 1);
    $this->db->order_by($order_title, $order_type);
    return $this->db->get()->result(self::CLASS_CATEGORY);
  }

  // получить список товаров в разделе
  public function get_section_item_list ( $section_id = 0, $order_title = 'id', $order_type = 'DESC' )
  {
    $this->db->distinct();
    $this->db->select('
      catalog_item.id,
      catalog_item.title,
      catalog_item.description,
      catalog_item.article,
      catalog_item.price,
      catalog_item.section_id
    ');
    $this->db->from('catalog_item');
    $this->db->join('catalog_section', 'catalog_section.id = catalog_item.section_id');
    $this->db->where('catalog_section.id', $section_id);
    $this->db->where('catalog_section.is_deleted', 0);
    $this->db->where('catalog_item.is_deleted', 0);
    return $this->db->get()->result(self::CLASS_ITEM);
  }

  // получение списка доступных товаров
  public function get_item_list ( $order_title = 'id', $order_type = 'DESC' )
  {
    $this->db->select('
      catalog_item.id,
      catalog_item.title,
      catalog_item.description,
      catalog_item.article,
      catalog_item.price,
      catalog_item.section_id
    ');
    $this->db->from('catalog_item');
    $this->db->where('is_deleted', 0);
    return $this->db->get()->result(self::CLASS_ITEM);
  }

  // получить список товаров в категории
  public function get_category_item_list ( $category_id = 0, $order_title = 'id', $order_type = 'DESC' )
  {
    $this->db->distinct();
    $this->db->select('
      catalog_item.id,
      catalog_item.title,
      catalog_item.description,
      catalog_item.article,
      catalog_item.price
    ');
    $this->db->from('catalog_item');
    $this->db->join('catalog_item_links', 'catalog_item_links.item_id = catalog_item.id');
    $this->db->join('catalog_category', 'catalog_category.id = catalog_item_links.category_id');
    $this->db->where('catalog_category.id', $category_id);
    $this->db->where('catalog_category.is_deleted', 0);
    $this->db->where('catalog_item.is_deleted', 0);
    return $this->db->get()->result(self::CLASS_ITEM);
  }

  // получить список категорий, которые привязаны к товару
  public function get_item_category_list ( $item_id = 0 )
  {
    $this->db->distinct();
    $this->db->select('
      catalog_category.id,
      catalog_category.parent_category_id,
      catalog_category.parent_section_id,
      catalog_category.title
    ');
    $this->db->from('catalog_category');
    $this->db->join('catalog_item_links', 'catalog_item_links.category_id = catalog_category.id');
    $this->db->join('catalog_item', 'catalog_item.id = catalog_item_links.item_id');
    $this->db->where('catalog_item.id', $item_id);
    $this->db->where('catalog_category.is_deleted', 0);
    $this->db->where('catalog_item.is_deleted', 0);
    return $this->db->get()->result(self::CLASS_CATEGORY);
  }

  // получить 1е изображение товара
  public function get_first_image ( $item_id = 0 )
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_ITEM_IMAGES);
    $this->db->where('item_id', $item_id);
    $this->db->order_by('id', 'ASC');
    $this->db->limit(1);
    return $this->db->get()->row();
  }

  // получить все изображения товара
  public function get_images ( $item_id = 0 )
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_ITEM_IMAGES);
    $this->db->where('item_id', $item_id);
    $this->db->order_by('id', 'ASC');
    return $this->db->get()->result();
  }

  // отвязать товар от категории
  public function unlink ( $item_id, $category_id )
  {
    $data = array (
      'item_id'     => $item_id,
      'category_id' => $category_id
    );
    $this->db->delete(self::TABLE_CATALOG_ITEM_LINKS, $data);
  }

  // public function get_a ( $section_id = 0, $order_title = 'id', $order_type = 'DESC' )
  // {
  //   $this->db->select('*');
  //   $this->db->from(self::TABLE_CATALOG_CATEGORY);
  //   $this->db->where('parent_section_id', $section_id);
  //   $this->db->where('parent_category_id', 0);
  //   $this->db->where('is_deleted !=', 1);
  //   $first_level_list = $this->db->get()->result(self::CLASS_CATEGORY);
  // }

  // public function get_list_down ( $category_list )
  // {
  //   foreach ( $category_list as $category )
  //   {
  //     $this->db->select('*');
  //     $this->db->from(self::TABLE_CATALOG_CATEGORY);
  //     $this->db->where('parent_section_id', $category->parent_section_id);
  //     $this->db->where('parent_category_id', $category->id);
  //     $this->db->where('is_deleted !=', 1);
  //     $sub_list = $this->db->get()->result(self::CLASS_CATEGORY);
  //   }
  // }

  // получить объект указанного типа (section, category, item)
  public function get_object ( $object_id = 0, $object_type = '' )
  {
    switch ( $object_type )
    {
      case 'section':
        $table = self::TABLE_CATALOG_SECTION;
        $class = self::CLASS_SECTION;
        break;

      case 'category':
        $table = self::TABLE_CATALOG_CATEGORY;
        $class = self::CLASS_CATEGORY;
        break;

      case 'item':
        $table = self::TABLE_CATALOG_ITEM;
        $class = self::CLASS_ITEM;
        break;

      default:
        return FALSE;
        break;
    }
    $this->db->select('*');
    $this->db->from($table);
    $this->db->where('id', $object_id);
    $result = $this->db->get()->result($class);
    if ( ! empty($result) )
    {
      return $result[0];
    }
    return FALSE;
  }

  // получить список валют
  public function get_currency_list ()
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_CURRENCY);
    return $this->db->get()->result();
  }

  // получить список всех привязок товаров и категорий
  public function get_category_items ( $object_id )
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_ITEM_LINKS);
    return $this->db->get()->result();
  }

  // сохранить/обновить данные объекта
  public function save ( $object )
  {
    $object_class = get_class($object);
    switch ( $object_class )
    {
      case self::CLASS_SECTION:
        if ( $object->id )
        {
          $this->db->update(self::TABLE_CATALOG_SECTION, $object, array('id' => $object->id));
        } else {
          $this->db->insert(self::TABLE_CATALOG_SECTION, $object);
          return $this->db->insert_id();
        }
        break;

      case self::CLASS_CATEGORY:
        if ( $object->id )
        {
          $this->db->update(self::TABLE_CATALOG_CATEGORY, $object, array('id' => $object->id));
        } else {
          $this->_inc_num_categories($object->parent_section_id);
          $this->db->insert(self::TABLE_CATALOG_CATEGORY, $object);
        }
        break;

      case self::CLASS_ITEM:
        if ( $object->id )
        {
          $this->db->update(self::TABLE_CATALOG_ITEM, $object, array('id' => $object->id));
        } else {
          $this->db->insert(self::TABLE_CATALOG_ITEM, $object);
          return $this->db->insert_id();
        }
        break;

      default:
        return FALSE;
        break;
    }
    return TRUE;
  }

  // добавление пользовательского поля
  public function user_field_add ( $section_id, $uf_title, $uf_type )
  {
    if ( count($uf_title) == count($uf_type) )
    {
      for ( $i = 0; $i < count($uf_title); $i++ )
      {
        if ( $uf_title[$i] )
        {
          $data = array (
             'title'      => $uf_title[$i],
             'type'       => $uf_type[$i],
             'section_id' => $section_id
          );
          $this->db->insert(self::TABLE_CATALOG_USER_FIELDS, $data);
        }
      }
    }
  }

  // обновление пользовательского поля
  public function user_field_upd ( $section_id, $cur_uf_title, $cur_uf_id )
  {
    if ( count($cur_uf_title) == count($cur_uf_id) )
    {
      for ( $i = 0; $i < count($cur_uf_title); $i++ )
      {
        if ( $cur_uf_title[$i] )
        {
          $data = array (
             'title' => $cur_uf_title[$i]
          );
        } else {
          $data = array (
             'is_deleted' => 1
          );
        }
        $this->db->where('section_id', $section_id);
        $this->db->where('id', $cur_uf_id[$i]);
        $this->db->update(self::TABLE_CATALOG_USER_FIELDS, $data);
      }
    }
  }

  // получить список пользовательских полей
  public function get_uf_list ( $section_id )
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_USER_FIELDS);
    $this->db->where('section_id', $section_id);
    $this->db->where('is_deleted', 0);
    return $this->db->get()->result_array();
  }

  // получить список значений пользовательских полей
  public function get_uf_values ( $section_id, $item_id )
  {
    $this->db->select('*');
    $this->db->from(self::TABLE_CATALOG_USER_VALUES);
    $this->db->join('catalog_user_fields', 'catalog_user_values.user_field_id = catalog_user_fields.id');
    $this->db->where('catalog_user_fields.is_deleted', 0);
    $this->db->where('catalog_user_fields.section_id', $section_id);
    $this->db->where('catalog_user_values.item_id', $item_id);
    return $this->db->get()->result_array();
  }

  // добавление значений пользовательских полей
  public function set_uf_values ( $item_id, $uf_values, $uf_ids, $uf_types )
  {
    for ( $i = 0; $i < count($uf_values); $i++ )
    {
      if ( $uf_values[$i] )
      {
        switch ( $uf_types[$i] )
        {
          case 1:
            $data['value_int'] = (int) $uf_values[$i];
            break;
          case 2:
            $data['value_float'] = (float) $uf_values[$i];
            break;
          case 3:
            $data['value_string'] = (string) $uf_values[$i];
            break;
          case 4:
            $data['value_text'] = trim($uf_values[$i]);
            break;
          case 5:
            $data['value_date'] = date('Y-m-d H:i:s', strtotime($uf_values[$i]));
            break;
          default:
            continue;
            break;
        }
      } else {
        $data['value_int']    = NULL;
        $data['value_float']  = NULL;
        $data['value_string'] = NULL;
        $data['value_text']   = NULL;
        $data['value_date']   = NULL;
      }
      $data = array (
         'item_id'       => $item_id,
         'user_field_id' => $uf_ids[$i]
      );
      $this->db->insert(self::TABLE_CATALOG_USER_VALUES, $data);
    }
  }

  // обновление значений пользовательских полей
  public function upd_uf_values ( $item_id, $uf_values, $uf_ids, $uf_types )
  {
    if ( count($uf_values) == count($uf_ids) )
    {
      for ( $i = 0; $i < count($uf_values); $i++ )
      {
        if ( $uf_values[$i] )
        {
          switch ( $uf_types[$i] )
          {
            case 1:
              $data['value_int'] = (int) $uf_values[$i];
              break;
            case 2:
              $data['value_float'] = (float) $uf_values[$i];
              break;
            case 3:
              $data['value_string'] = (string) $uf_values[$i];
              break;
            case 4:
              $data['value_text'] = trim($uf_values[$i]);
              break;
            case 5:
              $data['value_date'] = date('Y-m-d H:i:s', strtotime($uf_values[$i]));
              break;
            default:
              continue;
              break;
          }
        } else {
          $data['value_int']    = NULL;
          $data['value_float']  = NULL;
          $data['value_string'] = NULL;
          $data['value_text']   = NULL;
          $data['value_date']   = NULL;
        }
        $this->db->where('item_id', $item_id);
        $this->db->where('user_field_id', $uf_ids[$i]);
        $this->db->update(self::TABLE_CATALOG_USER_VALUES, $data);
      }
    }
  }

  // добавление изображения
  public function images_add ( $item_id, $imgs_id )
  {
    if ( ! empty($imgs_id) )
    {
      $data = array();
      foreach ( $imgs_id as $image_id )
      {
        $data[] = array (
          'item_id'  => $item_id,
          'image_id' => $image_id
        );
      }
      $this->db->insert_batch(self::TABLE_CATALOG_ITEM_IMAGES, $data);
    }
  }

  // редактирование изображения
  public function images_edit ( $item_id, $imgs_id )
  {
    if ( ! empty($imgs_id) )
    {
      // $this->db->delete(self::TABLE_CATALOG_ITEM_IMAGES, array('item_id' => $item_id));
      $data = array();
      foreach ( $imgs_id as $image_id )
      {
        $data[] = array (
          'item_id'  => $item_id,
          'image_id' => $image_id
        );
      }
      $this->db->insert_batch(self::TABLE_CATALOG_ITEM_IMAGES, $data);
    }
  }

  public function image_delete ( $item_id, $image_id )
  {
    if ( $item_id )
    {
      $data['item_id'] = $item_id;
      $data['image_id'] = $image_id;
      $this->db->delete(self::TABLE_CATALOG_ITEM_IMAGES, $data);
    }
  }

  // получение списка изображений товара
  public function get_item_images ( $item_id )
  {
    $this->db->select('image_id');
    $this->db->from(self::TABLE_CATALOG_ITEM_IMAGES);
    $this->db->where('item_id', $item_id);
    $result = $this->db->get()->result_array();
    $res = array();
    foreach ($result as $key => $value)
    {
      $res[] = new Image_item($value['image_id']);
    }
    return $res;
  }

  // получение списка категорий, привязаннх к товару
  public function get_item_links ( $item_id )
  {
    $this->db->select('category_id');
    $this->db->from(self::TABLE_CATALOG_ITEM_LINKS);
    $this->db->where('item_id', $item_id);
    $result = $this->db->get()->result_array();
    $res = array();
    foreach ($result as $key => $value)
    {
      $res[] = $value['category_id'];
    }
    return $res;
  }

  // привязка категории к товару
  public function links_add ( $item_id, $category_id_list )
  {
    if ( !empty($category_id_list) && !in_array(0, $category_id_list) )
    {
      $data = array();
      foreach ( $category_id_list as $category_id )
      {
        $data[] = array (
          'item_id'     => $item_id,
          'category_id' => $category_id
        );
      }
      $this->db->insert_batch(self::TABLE_CATALOG_ITEM_LINKS, $data);
    }
  }

  // перевязка ссылок категорий на товар
  public function links_edit ( $item_id, $category_id_list )
  {
    if ( !empty($category_id_list) && !in_array(0, $category_id_list) )
    {
      $this->db->delete(self::TABLE_CATALOG_ITEM_LINKS, array('item_id' => $item_id));
      $data = array();
      foreach ( $category_id_list as $category_id )
      {
        $data[] = array (
          'item_id'     => $item_id,
          'category_id' => $category_id
        );
      }
      $this->db->insert_batch(self::TABLE_CATALOG_ITEM_LINKS, $data);
    } elseif ( !empty($category_id_list) && in_array(0, $category_id_list) ) {
      $this->db->delete(self::TABLE_CATALOG_ITEM_LINKS, array('item_id' => $item_id));
    }
  }

  private function _inc_num_categories ( $section_id )
  {
    $this->db->set('num_categories', 'num_categories + 1', FALSE);
    $this->db->where('id', $section_id);
    return $this->db->update(self::TABLE_CATALOG_SECTION);
  }

  private function _dec_num_categories ( $section_id )
  {
    $this->db->set('num_categories', 'num_categories - 1', FALSE);
    $this->db->where('id', $section_id);
    return $this->db->update(self::TABLE_CATALOG_SECTION);
  }

  // удаление данных объекта
  public function delete ( $object_id, $object_type )
  {
    switch ( $object_type )
    {
      case 'section':
        $this->_set_is_deleted(self::TABLE_CATALOG_SECTION, $object_id, 1);
        break;

      case 'category':
        $this->_dec_num_categories($object_id->parent_section_id);
        $this->_set_is_deleted(self::TABLE_CATALOG_CATEGORY, $object_id->id, 1);
        break;

      case 'item':
        $this->_set_is_deleted(self::TABLE_CATALOG_ITEM, $object_id->id, 1);
        break;

      default:
        return FALSE;
        break;
    }
    return TRUE;
  }

  private function _set_is_deleted ( $table, $id, $value )
  {
    $this->db->set('is_deleted', $value);
    $this->db->where('id', $id);
    return $this->db->update($table);
  }

  public function get_page_content ( $page_id = 0 )
  {
    // update cart
    if ( isset($_POST['type']) && ($_POST['type'] == 'upd') && isset($_POST['my_ids']) && isset($_POST['my_qty']) )
    {
      foreach ( $_POST['my_ids'] as $key => $rowid )
      {
        $data[] = array (
          'rowid' => $rowid,
          'qty'   => $_POST['my_qty'][$key]
        );
      }
      $this->cart->update($data);
      redirect($_SERVER['HTTP_REFERER']);
    }

    // add to cart
    if ( isset($_POST['type']) && ($_POST['type'] == 'add') && isset($_POST['item_id']) && isset($_POST['qty']) )
    {
      $item_id  = $_POST['item_id'];
      $quantity = $_POST['qty'];
      $find_item = FALSE;
      foreach ( $this->cart->contents() as $item )
      {
        if ( $item['id'] == $item_id )
        {
          $find_item = TRUE;
          $data = array(
            'rowid' => $item['rowid'],
            'qty'   => $item['qty'] + $quantity
          );
          $this->cart->update($data);
          break;
        }
      }
      if ( ! $find_item )
      {
        $item = $this->get_object($item_id, 'item') ;
        $data = array (
          'id'    => $item->id,
          'qty'   => $quantity,
          'price' => $item->price,
          'name'  => $item->title,
        );
        $this->cart->insert($data);
      }
      redirect($_SERVER['HTTP_REFERER']);
    }

    // delete from cart
    if ( isset($_POST['type']) && ($_POST['type'] == 'del') && isset($_POST['rowid']) )
    {
      $data = array(
        'rowid' => $_POST['rowid'],
        'qty'   => 0
      );
      $this->cart->update($data);
      redirect($_SERVER['HTTP_REFERER']);
    }

    // single item
    if ( $item_id = $this->input->get('item') )
    {
      $item = $this->get_object($item_id, 'item');
      $data = array (
        'item' => $item
      );
      return $this->_view_content($this->_template['detail'], $data, true);
    }

    // item list
    if ( $cat_id = $this->input->get('cat') )
    {
      $catalog_products = $this->get_category_item_list($cat_id);
    } else {
      $catalog_products = $this->get_item_list();
    }
    $count_per_page = COUNT_PER_PAGE;  // TODO rewrite per page from page_id-section link
    $total_rows = count($catalog_products);
    $paginator = $this->_paginator($total_rows, $count_per_page);  // TODO rewrite per page from page_id-section link

    $catalog_products = array_splice($catalog_products, (int) $this->input->get('per_page'), $count_per_page);

    $data = array(
      'catalog_products' => $catalog_products,
      'paginator'        => $paginator,
    );
    return $this->_view_content($this->_template['show'], $data, true);
  }

  private function _paginator ( $total_rows, $per_page )
  {
    $full_url = site_url() . $_SERVER['REQUEST_URI'];
    $full_url = explode('?', $full_url);
    $full_url = $full_url[0];

    $config                     = array();
    $config['base_url']         = $full_url . '?';
    $config['total_rows']       = $total_rows;
    $config['per_page']         = $per_page;
    // Adding Enclosing Markup
    $config['full_tag_open']    = '<div class="pagenavi">';
    $config['full_tag_close']   = '</div>';
    // Customizing the First Link
    $config['first_link']       = 'в начало';
    $config['first_tag_open']   = '';
    $config['first_tag_close']  = '';
    // Customizing the Last Link
    $config['last_link']        = 'в конец';
    $config['last_tag_open']    = '';
    $config['last_tag_close']   = '';
    // Customizing the "Next" Link
    $config['next_link']        = '>';
    $config['next_tag_open']    = '';
    $config['next_tag_close']   = '';
    // Customizing the "Previous" Link
    $config['prev_link']        = '<';
    $config['prev_tag_open']    = '';
    $config['prev_tag_close']   = '';
    // Customizing the "Current Page" Link
    $config['cur_tag_open']     = '<span class="current">';
    $config['cur_tag_close']    = '</span>';
    // Customizing the "Digit" Link
    $config['num_tag_open']     = '';
    $config['num_tag_close']    = '';

    $this->pagination->initialize($config);
    return $this->pagination->create_links();
  }






/*================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================
================================================================================*/

  public function get_path_to_image ()
  {
    return $this->_path_to_image;
  }

  public function get_categories($parent_id = 0, $sort = 'desc', $sort_type = 'id') {
    $where    = $parent_id > 0 ? " where parent_id = {$parent_id}" : " where depth = 0";
    $sort   = $sort == 'asc' ? 'asc' : 'desc';
    $sort_type  = $sort_type == 'id' || $sort_type == 'parent_id' || $sort_type == 'title' ? $sort_type : 'id';
    $sql    = "SELECT id, parent_id, title, show_title, depth, count_per_page FROM {$this->_table} {$where} ORDER BY {$sort_type} {$sort}";
    $data   = $this->db->query($sql)->result_array();
    if (sizeof($data) == 0) return array();
    return $this->_create_collection($data, 'category');
  }

  public function get_data_tree($id, $type = false, &$ret = array())
  {
    $cats_list = $this->get_categories($id);
    if (count($cats_list) > 0)
    {
      foreach ($cats_list as $item)
      {
        if ($type != 'ITEMS_ONLY')
        {
          $ret[] = $item;
        }
        $this->get_data_tree($item->id, $type, $ret);
      }
    }
    else
    {
      if ($type != 'CATS_ONLY')
      {
        $tmp = $this->get_all_objects($id);
        foreach ($tmp as $item)
        {
          $ret[] = $item;
        }
      }
    }
    return $ret;
  }

  public function get_data_tree_v2 ($id, $type = false)
  {
    $cats_list = $this->get_categories($id);
    if (count($cats_list) > 0)
    {
      foreach ($cats_list as $item)
      {
        if ($type != 'ITEMS_ONLY')
        {
          $ret[] = $item;
        }
        $this->get_data_tree($item->id, $type, $ret);
      }
    }
    else
    {
      if ($type != 'CATS_ONLY')
      {
        $tmp = $this->get_all_objects($id);
        foreach ($tmp as $item)
        {
          $ret[] = $item;
        }
      }
    }
    return $ret;
  }


  public function get_category($id) {
    $sql = "select id, parent_id, title, show_title, depth, count_per_page from {$this->_table} where id = {$id}";
    $res = $this->db->query($sql)->row_array();
    if (sizeof($res) == 0) return false;
    return $this->_get_object($res, 'category');
  }

  public function get_all_objects($parent_id = 0, $sort = 'asc', $sort_type = 'priority') {
    $where    = $parent_id > 0 ? " where parent_id = {$parent_id}" : "";
    $sort   = $sort == 'asc' ? 'asc' : 'desc';
    $sql = "select id, parent_id, title, image1_id, image2_id, image3_id, image4_id, description, depth, price, discount, unix_timestamp(date_created) created from {$this->_table_item} {$where} order by {$sort_type} {$sort}";
    $data = $this->db->query($sql)->result_array();
    if ($data === false) return array();
    return $this->_create_collection($data, 'item');
  }

  public function get_object2($id) {
    $sql = "select id, parent_id, title, image1_id, image2_id, image3_id, image4_id, description, depth, price, discount, unix_timestamp(date_created) created from {$this->_table_item} where id = {$id}";
    $res = $this->db->query($sql)->row_array();
    if (sizeof($res) == 0) return false;
    return $this->_get_object($res, 'item');
  }



  public function delete2 ($id = 0, $type = '') {
    if ($type == 'category') {
      $sql_cat    = "delete from {$this->_table} where id = {$id}";
      $sql_item   = "delete from {$this->_table_item} where parent_id = {$id}";
      $this->db->query($sql_cat);
      $this->db->query($sql_item);
      return true;
    } elseif ($type == 'item') {
      $sql_item   = "delete from {$this->_table_item} where id = {$id}";
      $this->db->query($sql_item);
      return true;
    }
    return false;
  }

  public function get_page_content2 ( $page_id = 0 )
  {
    $offset = (int) $this->input->get('per_page');

    if ( $this->input->get('cat') )
    {
      $selection = $this->get_category( (int) $this->input->get('cat') );
    }
    if ( $this->input->get('item') )
    {
      $selection = $this->get_object( (int) $this->input->get('item') );
    }

    // == breadcrumbs ==========================================================
    $category_list = array();
    $item_info = array();
    if ( ( ! empty($selection) ) && ( get_class($selection) == 'Catalog_item') )
    {
      $item_info = array (
        'id' => $selection->id,
        'title' => $selection->title
      );
      $cur_cat = $this->get_category($selection->parent_id);
      $category_list = $this->_get_parents_list($cur_cat->id, $cur_cat->title);
    }
    if ( ( ! empty($selection) ) && ( get_class($selection) == 'Catalog_category') )
    {
      $category_list = $this->_get_parents_list($selection->id, $selection->title);
    }
    // == breadcrumbs ==========================================================

  ////Обработка _GET

    $catalog_category   = $this->get_categories();
    if (empty($catalog_category))
    {
      return '';
    }

  //Добавление товара в корзину
    if (!empty($_POST))
    {
      foreach ($this->cart->contents() as $item)
      {
        if ($item['id'] == $_POST['id'])
        {
          $_POST['qty'] += $item['qty'];
          break;
        }
      }

      $item = $this->get_object($_POST['id']);
      if ( $item->image1_id )
      {
        $image = new Image_item($item->image1_id);
        $img_path = IMAGESRC.'catalog/'.$image->getFilenameThumb();
      } else {
        $img_path = '/img/admin/noimage.svg';
      }

      $price = $item->discount ? $item->price*(100 - $item->discount)/100 : $item->price;
      $data = array(
        'id'    => $item->id,
        'qty'   => $_POST['qty'],
        'price'   => $price,
        'name'    => $item->title,
        'options' => array( 'imagepath' => $img_path , 'description' => $item->description, 'discount' => $item->discount )
      );

      $this->cart->insert($data);
      $this->session->set_userdata('q', $_POST['q']);
      redirect($_SERVER['HTTP_REFERER']);
    }
  ////Добавление товара в корзину



  //Выборка товара

    // $inventory = array();
    // if (!($id = $this->input->get('cat')))
    // {
    //  $id = 0;
    // }
    // $inventory = $this->get_data_tree($id, 'ITEMS_ONLY');

    // print_r($inventory);
    // exit('fail');

    // $count_all       = count($inventory);

    // $inventory = array_splice($inventory, (int)$this->input->get('per_page'), $per_page = 12);
    // $active_cat = false;
    // if ($getcat = $this->input->get('cat'))
    // {
    //  $active_cat = $this->get_category($getcat);
    // }

    $where = '';
    if ($this->input->get('cat'))
    {
      $where = " WHERE `parent_id` = ".(int)$this->input->get('cat');
    }
    $sql = "SELECT id FROM `catalog_item`".$where;
    $inventory = $this->db->query($sql)->result_array();
    foreach ($inventory as &$item)
    {
      $item = $item['id'];
    }
    unset ($item);
    // if (!empty($filters))
    // {
    //   $inventory = array_intersect($inventory, $endFilter);
    // }
    foreach ($inventory as &$item)
    {
      $item = $this->get_object($item);
    }
    unset ($item);
    $count_all        = count($inventory);
    $inventory = array_splice($inventory, (int)$this->input->get('per_page'), $per_page = 12);

    $active_cat = false;
    if ($getcat = $this->input->get('cat'))
    {
      $active_cat = $this->get_category($getcat);
    }
  ////Выборка товара

  //paginator
    $full_url       = site_url().$_SERVER['REQUEST_URI'];
    $full_url       = explode("?", $full_url);
    $full_url       = $full_url[0];

    $config                     = array();
    $config['base_url']         = $full_url . '?';
    $config['total_rows']       = $count_all;
    $config['per_page']         = $per_page;
    // Adding Enclosing Markup
    $config['full_tag_open']    = '<ul>';
    $config['full_tag_close']   = '</ul>';
    // Customizing the First Link
    $config['first_link']       = 'в начало';
    $config['first_tag_open']   = '<a href="#" class="prev">';
    $config['first_tag_close']  = '</a>';
    // Customizing the Last Link
    $config['last_link']        = 'в конец';
    $config['last_tag_open']    = '<a href="#" class="next">';
    $config['last_tag_close']   = '</a>';
    // Customizing the "Next" Link
    $config['next_link']        = '>';
    $config['next_tag_open']    = '<li class="link">';
    $config['next_tag_close']   = '</li>';
    // Customizing the "Previous" Link
    $config['prev_link']        = '<';
    $config['prev_tag_open']    = '<li class="link">';
    $config['prev_tag_close']   = '</li>';
    // Customizing the "Current Page" Link
    $config['cur_tag_open']     = '<li><a href="#" class="link selected">';
    $config['cur_tag_close']    = '</a></li>';
    // Customizing the "Digit" Link
    $config['num_tag_open']     = '<li class="link">';
    $config['num_tag_close']    = '</li>';

    $this->pagination->initialize($config);
    $paginator = $this->pagination->create_links((int)$this->input->get('per_page'));
  ////paginator


/*    $image = array();
    foreach ($inventory as $key => $item)
    {
      $tmp1 = new Image_item($item->image1_id);
      $tmp2 = new Image_item($item->image2_id);
      $tmp3 = new Image_item($item->image3_id);
      $tmp4 = new Image_item($item->image4_id);
      $tmp1_id = $tmp1->getId();
      $tmp2_id = $tmp2->getId();
      $tmp3_id = $tmp3->getId();
      $tmp4_id = $tmp4->getId();
      $image[$tmp1_id] = $tmp1_id ? $tmp1 : false;
      $image[$tmp2_id] = $tmp2_id ? $tmp2 : false;
      $image[$tmp4_id] = $tmp4_id ? $tmp3 : false;
      $image[$tmp4_id] = $tmp4_id ? $tmp4 : false;
    }
*/

    if ($this->input->get('item'))
    {
      $image = array();
      $item = $this->get_object($this->input->get('item'));
      $image[1] = new Image_item($item->image1_id);
      $image[2] = new Image_item($item->image2_id);
      $image[3] = new Image_item($item->image3_id);
      $image[4] = new Image_item($item->image4_id);

      $data = array(
                    'item'  => $item,
                    'image' => $image,
                    'cats' => $category_list,
                    'cur_item' => $item_info
                    );
      return $this->_view_content($this->_template['detail'], $data, true);
    }
    $data = array(
                  'cat_menu'  => $this->get_recur_data(0),
                  'active_cat'  => $active_cat,
                  'inventory' => $inventory,
                  'image'   => $image,
                  'offset'    => $offset,
                  'path'    => $this->_path_to_image,
                  'paginator' => $paginator,
                  'cats' => $category_list,
                  'cur_item' => $item_info
                  // 'banner'   => $banner
                  );
    return $this->_view_content($this->_template['show'], $data, true);
  }

  public function _get_object($data = array(), $type = 'item') {
    if ($type == 'category') {
      $tmp_object         = new Catalog_category();
      $tmp_object->id       = $data['id'];
      $tmp_object->parent_id    = $data['parent_id'];
      $tmp_object->depth      = $data['depth'];
      $tmp_object->title      = $data['title'];
      $tmp_object->show_title   = $data['show_title'];
      $tmp_object->count_per_page = $data['count_per_page'];
      return $tmp_object;
    } elseif ($type == 'item') {
      $tmp_object         = new Catalog_item();
      $tmp_object->id       = $data['id'];
      $tmp_object->parent_id    = $data['parent_id'];
      $tmp_object->depth      = $data['depth'];
      $tmp_object->title      = $data['title'];
      $tmp_object->price      = $data['price'];
      $tmp_object->discount   = $data['discount'];
      $tmp_object->description  = $data['description'];
      $tmp_object->image1_id    = $data['image1_id'];
      $tmp_object->image2_id    = $data['image2_id'];
      $tmp_object->image3_id    = $data['image3_id'];
      $tmp_object->image4_id    = $data['image4_id'];
      $tmp_object->created    = $data['created'];
      return $tmp_object;
    }
    return false;
  }

  public function to_down($id, $parent_id) {
    $id       = (int)$id;
    $parent_id    = (int)$parent_id;
    $sql      = "select priority from {$this->_table_item} where id = {$id}";
    $current_page = $this->db->query($sql)->row_array();
    if (sizeof($current_page) == 0) return false;
    $sql    = "select id, priority from {$this->_table_item} where priority > {$current_page['priority']} and parent_id = {$parent_id} order by priority asc limit 0,1";
    $next_page  = $this->db->query($sql)->row_array();
    if (sizeof($next_page) > 0) {
      $this->db->query("start transaction");
      $sql_next   = "update {$this->_table_item} set priority = {$current_page['priority']} where id = {$next_page['id']} and parent_id = {$parent_id}";
      $sql_current  = "update {$this->_table_item} set priority = {$next_page['priority']} where id = {$id} and parent_id = {$parent_id}";
      if ($this->db->query($sql_next) && $this->db->query($sql_current)) {
        $this->db->query("commit");
        return true;
      } else {
        $this->db->query("rollback");
      }
    }
    return false;
  }

  public function to_up($id, $parent_id) {
    $id       = (int)$id;
    $parent_id    = (int)$parent_id;
    $sql      = "select priority from {$this->_table_item} where id = {$id}";
    $current_page = $this->db->query($sql)->row_array();
    if (sizeof($current_page) == 0) return false;
    $sql    = "select id, priority from {$this->_table_item} where priority < {$current_page['priority']} and parent_id = {$parent_id} and priority > 0 order by priority desc limit 0,1";
    $prev_page  = $this->db->query($sql)->row_array();
    if (sizeof($prev_page) > 0) {
      $this->db->query("start transaction");
      $sql_prev   = "update {$this->_table_item} set priority = {$current_page['priority']} where id = {$prev_page['id']} and parent_id = {$parent_id}";
      $sql_current  = "update {$this->_table_item} set priority = {$prev_page['priority']} where id = {$id} and parent_id = {$parent_id}";
      if ($this->db->query($sql_prev) && $this->db->query($sql_current)) {
        $this->db->query("commit");
        return true;
      } else {
        $this->db->query("rollback");
      }
    }
    return false;
  }

  public function get_recur_data($id = 0, &$arr = array())
  {
    $cat_list   = $this->get_categories($id);
    if (empty($cat_list))
    {
      return $this->get_all_objects($id);
    }

    foreach ($cat_list as $category)
    {

      $arr[$category->id] = array( 'cat' => $category, 'children' => $this->get_recur_data($category->id, $arr[$category->id]['children']));

    }
    return $arr;
  }

  public function get_widget()
  {
    $sql = "SELECT
          id,
          title,
          image1_id,
          price,
          discount
        FROM
          {$this->_table_item}
        ORDER BY id DESC
        LIMIT 30
        ";
    $result = $this->db->query($sql)->result_array();
    foreach ($result as &$item)
    {
      $img = new Image_item($item['image1_id']);
      if ($filename = $img->getFilenameThumb())
      {
        $item['image_path'] = IMAGESRC.'catalog/'.$filename;
      }
      else
      {
        $item['image_path'] = false;
      }

      if ($item['discount'])
      {
        $item['price'] *= ( (100 - (int)$item['discount'])/100 );
      }
      $item['price'] = number_format($item['price'], 2);
    }
    return $result;
  }

  /*===============*/
  public function get_category_list2 ()
  {
    $query = "SELECT `id`, `title` FROM `catalog_category` WHERE `parent_id` = 0";
    $categories = $this->db->query($query)->result_array();
    $result = array();
    foreach ($categories as $key => $value)
    {
      $query = "SELECT `id`, `title` FROM `catalog_category` WHERE `parent_id` = {$value['id']}";
      $value['subcats'] = $this->db->query($query)->result_array();
      $result[] = $value;
    }
    return $result;
  }

  public function get_last_objects ( $number )
  {
    $number = intval($number);
    $query = "SELECT `id` FROM {$this->_table_item} ORDER BY `id` DESC LIMIT {$number}";
    $obj_list = $this->db->query($query)->result_array();
    $result = array();
    foreach ($obj_list as $key => $value)
    {
      $result[] = $this->get_object($value['id']);
    }
    return $result;
  }

  /*-- page breadcrumbs ------------------------------------------------------*/

  protected function _get_parents_list ( $category_id, $category_title )
  {
    $result = array();
    // add all parents to result array
    $category_tree = $this->_get_pages_tree_up($category_id);
    foreach ( $category_tree as $key => $cat_item )
    {
      $item = $this->get_category($cat_item);
      $result[] = array (
        'id' => $item->id,
        'title' => $item->title
      );
    }
    // add current page to result array
    $result[] = array (
      'id' => $category_id,
      'title' => $category_title
    );
    return $result;
  }

  // get all parents for current page
  protected function _get_pages_tree_up ( $page_id, $pages_array = array() )
  {
    $page = $this->db->select('parent_id, title')->from($this->_table)->where('id', $page_id)->get()->row_array();
    if ( ! empty($page['parent_id']) )
    {
      $pages_array[] = $page['parent_id'];
      return $this->_get_pages_tree_up($page['parent_id'], $pages_array);
    }
    return array_reverse($pages_array);
  }

  /*-- page breadcrumbs end --------------------------------------------------*/

}
