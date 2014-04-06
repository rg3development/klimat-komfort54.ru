<?php

class Catalog_Category extends MY_Model_Catalog
{

  public $id;
  public $parent_category_id;
  public $parent_section_id;
  public $title;
  public $num_items;

  public function __construct( $object_id = 0 )
  {
    $this->id                 = 0;
    $this->parent_category_id = 0;
    $this->parent_section_id  = 0;
    $this->title              = '';
    $this->num_items          = 0;
  }

}