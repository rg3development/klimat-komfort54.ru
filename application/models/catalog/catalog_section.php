<?php

class Catalog_Section extends MY_Model_Catalog
{

  public $id;
  public $parent_page_id;
  public $title;
  public $count_per_page;
  public $num_categories;
  public $currency_id;

  public function __construct( $object_id = 0 )
  {
    $this->id             = 0;
    $this->parent_page_id = 0;
    $this->title          = '';
    $this->count_per_page = 5;
    $this->num_categories = 0;
    $this->currency_id    = 0;
    if ( $object_id )
    {
      $catalog_mapper = new Catalog_Mapper();
      $section = $catalog_mapper->get_object($object_id, 'section');
      $this->id             = $section->id;
      $this->parent_page_id = $section->parent_page_id;
      $this->title          = $section->title;
      $this->count_per_page = $section->count_per_page;
      $this->num_categories = $section->num_categories;
      $this->currency_id    = $section->currency_id;
    }
  }

}