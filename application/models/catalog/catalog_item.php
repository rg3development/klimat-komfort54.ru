<?php


class Catalog_Item extends MY_Model_Catalog
{

  public $id;
  public $title;
  public $description;
  public $article;
  public $price;
  public $section_id;

  public function __construct( $object_id = 0 )
  {
    $this->id          = 0;
    $this->title       = '';
    $this->description = '';
    $this->article     = '';
    $this->price       = 0;
    $this->section_id  = 0;
  }

  // получить ссылку на 1е (основное) изображение товара
  public function image ()
  {
    $image = NULL;
    if ( $this->id )
    {
      $catalog_mapper = new Catalog_Mapper();
      $image = $catalog_mapper->get_first_image($this->id);
    }
    return $this->img_src($image);
  }

  // получить массив изображений товара
  public function images ()
  {
    $result = array();
    if ( $this->id )
    {
      $catalog_mapper = new Catalog_Mapper();
      $result = $catalog_mapper->get_images($this->id);
    }
    return $result;
  }

  // генератор ссылок на изображение
  public function img_src ( $img_obj )
  {
    $image_src = '/img/admin/noimage.svg';
    if ( $img_obj && $img_obj->image_id )
    {
      $image = new Image_item($img_obj->image_id);
      $image_src = '/upload/images/catalog/' . $image->getFilename();
    }
    return $image_src;
  }

  // получить строковое представление цены
  public function price()
  {
    return $this->cart->format_number($this->price) . ' руб.';  // TODO: add currency
  }

  // получить ссылку на подробное описание товара
  public function details ()
  {
    return '?item=' . $this->id;
  }

  public function add_to_cart ()
  {
    $form = '
      <form id="cart_add" method="post">
        <input type="hidden" name="type" value="add" />
        <input type="hidden" name="qty" value="1" />
        <input type="hidden" name="item_id" value="' . $this->id . '" />
      </form>
    ';
    print($form);
  }

  public function buy ()
  {
    $form = '
      <form name="cart_add" id="cart_add_' . $this->id . '" method="post">
        <input type="hidden" name="type" value="add" />
        <input type="hidden" name="qty" value="1" />
        <input type="hidden" name="item_id" value="' . $this->id . '" />
      </form>
    ';
    print($form);
  }

}