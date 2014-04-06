<?php

/*
 * Поисковый движок
 *
 * @author rav <arudyuk@gmail.com>
 * @version 1.0
 */

class Search_mapper extends MY_Model {

	protected $_table_search_index;
	protected $_table_content_text;
	protected $_table_page;
	protected $_table_text;
	protected $_table_news_item;
	protected $_table_news;
	protected $_table_gallery_item;
	protected $_table_gallery;
	protected $_select_symb;
	protected $_select_content_symb;

	public function __construct() {
		parent::__construct();
		$this->_table_search_index	= 'search_index';
		$this->_table_page			= 'pages';
		$this->_table_page_modules	= 'module_pages';
		$this->_table_news			= 'news_category';
		$this->_table_news_item		= 'news_item';
		$this->_table_gallery		= 'gallery_category';
		$this->_table_gallery_item	= 'gallery_item';
		$this->_table_text			= 'text_item';

		$this->_template = 'search/index';

		$this->_select_title_symb = 0;
		$this->_select_content_symb = 200;
	}

	public function get_page_content() {
		$sentence = isset($_GET['s']) ? $_GET['s'] : '';
		$result = array();
		$result += $this->_get_text_content($sentence);
		$result += $this->_get_news_content($sentence);
		$result += $this->_get_gallery_content($sentence);
		foreach ($result as $key => $page) {
			$first_pos = mb_strpos($result[$key]['content'], $sentence);
			$result[$key]['content'] = mb_substr($result[$key]['content'], $first_pos, $this->_select_content_symb);
			$result[$key]['content'] = str_replace($sentence, '<b>' . $sentence . '</b>', $result[$key]['content']);
		};
		return $this->load->view($this->_template, array('content' => $result), true);
	}

	protected function _get_text_content($sentence = '') {
		if (empty($sentence)) return array();
		$sql = "select t.parent_id, t.description, p.url
				from {$this->_table_text} t inner join {$this->_table_page} p on p.id=t.parent_id
				where description like '%$sentence%' and p.show = 1";
		$pages		= $this->db->query($sql)->result_array();
		$content_url_array = array();
		foreach ($pages as $page) {
			if ($page['url'] != 'dev' && $page['url'] != 'error404') {
				if ($page['url'] == '') $page['url'] = 'main';
				$content = $this->_parse_string($page['description']);
				$content_url_array[] = array('content' => mb_strtolower($content), 'url' => $page['url']);
			}
		}
		return $content_url_array;
	}

	protected function _get_news_content($sentence = '') {
		if (empty($sentence)) return array();
		$sql = "select li.id id, l.parent_id parent_id, li.description description, p.url url
				from {$this->_table_news_item} li inner join {$this->_table_news} l on li.parent_id = l.id inner join {$this->_table_page} p on p.id=l.parent_id
				where description like '%$sentence%' and p.show = 1";
		$pages			= $this->db->query($sql)->result_array();
		$content_url_array = array();
		$content		= '';
		foreach ($pages as $page) {
			$content .= str_replace('<br/>', '', $page['description']);
			$content_url_array[] = array('content' => mb_strtolower($content), 'url' => $page['url'].'?news_id='.$page['id']);
		}
		return $content_url_array;
	}

	protected function _get_gallery_content($sentence = '') {
		if (empty($sentence)) return array();
		$sql = "select li.id id, li.title title, l.parent_id parent_id, li.description description, p.url url
				from {$this->_table_gallery_item} li inner join {$this->_table_gallery} l on li.parent_id = l.id inner join {$this->_table_page} p on p.id=l.parent_id
				where description like '%$sentence%' and p.show = 1";
		$pages				= $this->db->query($sql)->result_array();
		$content_url_array	= array();
		$content			= '';
		foreach ($pages as $page) {
			$content .= str_replace('<br/>', '', $page['description']);
			$content_url_array[] = array('content' => mb_strtolower($content), 'url' => $page['url'].'?news_id='.$page['id']);
		}
		return $content_url_array;
	}

	protected function _parse_string($str) {
		$str = trim($str);
		$str = preg_replace("/[^\x20-\xFF]/", "", @strval($str));
		$str = preg_replace("/&(.+?);/", "", @strval($str));
		$str = strip_tags($str);
		$str = htmlspecialchars($str, ENT_QUOTES);
		return $str;
	}

}
