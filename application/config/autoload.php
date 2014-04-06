<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$autoload['libraries']   = array();
$autoload['libraries'][] = 'database';
$autoload['libraries'][] = 'session';
$autoload['libraries'][] = 'form_validation';
$autoload['libraries'][] = 'loader';
$autoload['libraries'][] = 'pagination';
$autoload['libraries'][] = 'image_lib';
$autoload['libraries'][] = 'uri';
$autoload['libraries'][] = 'upload';

$autoload['helper']      = array('url', 'file', 'form', 'security');
$autoload['plugin']      = array();
$autoload['config']      = array('templates');
$autoload['language']    = array();

$autoload['model']       = array();
$autoload['model'][]     = 'image';
$autoload['model'][]     = 'image_item';
$autoload['model'][]     = 'manager_modules';
$autoload['model'][]     = 'page/page_mapper';
$autoload['model'][]     = 'text/text_mapper';
$autoload['model'][]     = 'news/news_mapper';
$autoload['model'][]     = 'gallery/gallery_mapper';
$autoload['model'][]     = 'banner/banner_mapper';
$autoload['model'][]     = 'feedback/feedback_mapper';
$autoload['model'][]     = 'comments/comments_mapper';
$autoload['model'][]     = 'custom/cart/cart_mapper';
$autoload['model'][]     = 'custom/catalog/catalog_mapper';