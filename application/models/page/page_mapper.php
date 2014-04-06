<?php
/*
 * Model of page mapper
 *
 * @author rav <arudyuk@gmail.com>
 * @version 1.0
 */

class Page_mapper extends MY_Model {
    protected $_table = 'pages';

    public function  __construct() {
        parent::__construct();
        require_once dirname(__FILE__).'/page_item'.EXT;
        $this->_path_to_image = IMAGESRC.'page';
    }

    public function get_path_to_image() {
        return $this->_path_to_image;
    }

    public function get_all_pages() {
        $sql = "select max(level) level from {$this->_table}";
        $max_level = $this->db->query($sql)->row()->level;
        $sitemap = array();
        for ($i = 0; $i <= $max_level; $i++) {
            $res = $this->_get_pages_array("level = {$i}");
            if (sizeof($res) > 0) $sitemap[$i] = $this->_create_collection($res);
        }
        return $sitemap;
    }

    public function get_page($id) {
        $sql                = "select * from {$this->_table} where id = {$id}";
        $res                = $this->db->query($sql)->row_array();
        if (sizeof($res) == 0) return false;
        return $this->_get_page_object($res);
    }

    public function get_menu($max_level = 1, $parent_id = 0) {
        $sitemap = array();
        if ($parent_id == 0) {
            for ($i = 0; $i <= $max_level; $i++) {
                $res = $this->_get_pages_array("`show` = 1 and `level` = {$i}");
                if (sizeof($res) > 0) $sitemap[$i] = $this->_create_collection($res);
            }
            return $sitemap;
        }
        for ($i = 0; $i <= $max_level; $i++) {
            $res = $this->_get_pages_array("`show` = 1 AND `level` = {$i} AND `parent_id` = {$parent_id}");
            if (sizeof($res) > 0) $sitemap = $this->_create_collection($res);
        }
        return $sitemap;
    }

    public function delete_page($id) {
        $id = (int)$id;
        $pages_id       = $this->_get_pages_tree_down($id);
        $pages_id[]     = $id;
        $pages_id       = join(",", $pages_id);
        $sql            = "select level, priority, parent_id from {$this->_table} where id in ({$pages_id})";
        $page           = $this->db->query($sql)->row();
        $sql            = "delete from {$this->_table} where id in ({$pages_id})";
        $this->db->query($sql);
        // правим приоритеты страниц находящихся после удаленной
        $sql    = "select id, priority from {$this->_table} where parent_id = {$page->parent_id} and priority > {$page->priority}";
        $pages  = $this->db->query($sql)->result();
        if (count > 0) {
            foreach ($pages as $page) {
                $new_priority = $page->priority - 1;
                $sql = "update {$this->_table} set priority = {$new_priority} where  id = {$page->id}";
                $this->db->query($sql);
            }
        }
        return;
    }

    public function check_url_exist($id, $url) {
        $sql    = "select id from {$this->_table} where url = {$this->db->escape($url)}";
        $res    = $this->db->query($sql)->result_array();
        if (sizeof($res) == 1) if ($res[0]['id'] == $id) return true;
        if (sizeof($res) == 0) return true;
        return false;
    }

    /* Page to down */
    public function page_to_down($id) {
        $id             = (int)$id;
        $sql            = "select level, priority from {$this->_table} where id = {$id}";
        $current_page   = $this->db->query($sql)->row_array();
        if (sizeof($current_page) == 0) return false;
        $sql = "select id, priority from {$this->_table} where level = {$current_page['level']} and priority >{$current_page['priority']} order by priority asc limit 0,1";
        $next_page = $this->db->query($sql)->row_array();
        if (sizeof($next_page) > 0) {
            $this->db->query("start transaction");
            $sql_next       = "update {$this->_table} set priority = {$current_page['priority']} where id = {$next_page['id']}";
            $sql_current    = "update {$this->_table} set priority = {$next_page['priority']} where id = {$id}";
            if ($this->db->query($sql_next) && $this->db->query($sql_current)) {
                $this->db->query("commit");
                return true;
            } else {
                $this->db->query("rollback");
            }
        }
        return false;
    }

    /* Page to up */
    public function page_to_up($id) {
        $id             = (int)$id;
        $sql            = "select level, priority from {$this->_table} where id = {$id}";
        $current_page   = $this->db->query($sql)->row_array();
    // print_r($current_page);
        if (sizeof($current_page) == 0) return false;
        // $sql            = "select id, priority from {$this->_table} where level = {$current_page['level']} and priority < {$current_page['priority']} and priority > 0 order by priority desc limit 0,1";
        $sql            = "select id, priority from {$this->_table} where level = {$current_page['level']} and priority < {$current_page['priority']} order by priority desc limit 0,1";
        $prev_page      = $this->db->query($sql)->row_array();
    // print_r($prev_page);
    // exit;
        if (sizeof($prev_page) > 0) {
            $this->db->query("start transaction");
            $sql_prev       = "update {$this->_table} set priority = {$current_page['priority']} where id = {$prev_page['id']}";
            $sql_current    = "update {$this->_table} set priority = {$prev_page['priority']} where id = {$id}";
            if ($this->db->query($sql_prev) && $this->db->query($sql_current)) {
                $this->db->query("commit");
                return true;
            } else {
                $this->db->query("rollback");
            }
        }
        return false;
    }


    protected function _get_pages_array($where = 'level = 0',
                                        $limit = '',
                                        $offset = 0,
                                        $order = 'priority asc,id asc'
                                     ) {
        $sql = "select * from {$this->_table}";
        if (!empty($where)) $sql .= " where {$where}";
        if (!empty($limit)) $sql .= " limit {$offset}, {$limit}";
        if (!empty($order)) $sql .= " order by {$order}";
        return $this->db->query($sql)->result_array();
    }

    /* Get all subpage for page */
    protected function _get_pages_tree_down($id, $pages_array = array()) {
        $page = $this->db->select('id')->from($this->_table)->where('parent_id', $id)->get()->row_array();
        if (!empty($page['id'])){
            $pages_array[] = $page['id'];
            return $this->_get_pages_tree_down($page['id'], $pages_array);
        }
        $id_array = array_reverse($pages_array);
        return $id_array;
    }

    public function save_page($object) {
        if (get_class($object) != 'Page_item') return false;
        $this->db->query("start transaction");
        // change parent page and change level page and subpages
        $sql            = "select parent_id, level from {$this->_table} where id = {$object->id}";
        $res            = $this->db->query($sql)->row_array();
        $old_level        = !empty($res['level']) ? (int)$res['level'] : 0;
        $old_parent_id    = !empty($res['parent_id']) ? (int)$res['parent_id'] : 0;
        if ($old_parent_id !== $object->parent_id) {
            $sql_parent            = "select * from {$this->_table} where id = {$object->parent_id}";
            $sql_priority        = "select max(priority) priority from {$this->_table} where parent_id = {$object->parent_id}";
            $parent_page        = $this->db->query($sql_parent)->row_array();
            $max_priority        = $this->db->query($sql_priority)->row()->priority;
            if (sizeof($parent_page) > 0) {
                $object->level        = $parent_page['level'] + 1;
                $object->priority    = $max_priority + 1;
            } else {
                $object->level        = 0;
                $object->priority    = $max_priority + 1;
            }
            // set new level for subpages

            $pages_id        = $this->_get_pages_tree_down($object->id);
            $delta_level    = $object->level - $old_level;
            foreach ($pages_id as $page_id) {
                $sql    = "select level from {$this->_table} where id = {$page_id}";
                $level    = $this->db->query($sql)->row()->level;
                $level    = $level + $delta_level;
                $sql    = "update {$this->_table} set level = $level where id = {$page_id}";
                // print_r($sql); die;
                $this->db->query($sql);
            }
         } else {
            $sql_priorty        = "select max(priority) priority from {$this->_table} where level = 0";
            $max_priority        = $this->db->query($sql_priorty)->row()->priority;
        }
        if ($object->id > 0) {
            $sql = "update {$this->_table}
                        set `parent_id`        = {$object->parent_id},
                            `url`            = {$this->db->escape($object->url)},
                            `priority`        = {$object->priority},
                            `meta`            = {$this->db->escape($object->meta)},
                            `description`    = {$this->db->escape($object->description)},
                            `keywords`        = {$this->db->escape($object->keywords)},
                            `level`            = {$object->level},
                            `title`            = {$this->db->escape($object->title)},
                            `show_title`    = {$object->show_title},
                            `show`            = {$object->show},
                            `alias`            = {$this->db->escape($object->alias)},
                            `show_alias`    = {$object->show_alias},
                            `template`        = {$this->db->escape($object->template)},
                            `image_bottom`    = {$object->image_bottom}
                    where `id` = {$object->id}";
            if ($this->db->query($sql)) {
                $this->db->query("commit");
                return $object->id;
            } else return false;
        }
        $sql = "insert into {$this->_table}
                        set `parent_id`        = {$object->parent_id},
                            `url`            = {$this->db->escape($object->url)},
                            `priority`        = {$object->priority},
                            `meta`            = {$this->db->escape($object->meta)},
                            `description`    = {$this->db->escape($object->description)},
                            `keywords`        = {$this->db->escape($object->keywords)},
                            `level`            = {$object->level},
                            `title`            = {$this->db->escape($object->title)},
                            `show_title`    = {$object->show_title},
                            `show`            = {$object->show},
                            `alias`            = {$this->db->escape($object->alias)},
                            `show_alias`    = {$object->show_alias},
                            `template`        = {$this->db->escape($object->template)},
                            `image_bottom`    = {$object->image_bottom}
                            ";
        if ($this->db->query($sql)) {
            $this->db->query("commit");
            $sql = "select id from {$this->_table} where url = {$this->db->escape($object->url)}";
            return $this->db->query($sql)->row()->id;
        } else {
            $this->db->query("roolback");
            return false;
        }
    }

    protected function _get_page_object($data = array()) {
        $tmp_object = new Page_item();
        $tmp_object->id           = $data['id'];
        $tmp_object->title        = $data['title'];
        $tmp_object->show_title   = $data['show_title'];
        $tmp_object->alias        = $data['alias'];
        $tmp_object->show_alias   = $data['show_alias'];
        $tmp_object->meta         = $data['meta'];
        $tmp_object->keywords     = $data['keywords'];
        $tmp_object->description  = $data['description'];
        $tmp_object->url          = $data['url'];
        $tmp_object->level        = $data['level'];
        $tmp_object->parent_id    = $data['parent_id'];
        $tmp_object->priority     = $data['priority'];
        $tmp_object->show         = $data['show'];
        $tmp_object->template     = $data['template'];
        $tmp_object->image_bottom = $data['image_bottom'];
        return $tmp_object;
    }

    protected function _create_collection($data = array()) {
        if (sizeof($data) == 0) return false;
        $object_collection = array();
        foreach ($data as $data_element) {
            $object_collection[] = $this->_get_page_object($data_element);
        }
        return $object_collection;
    }
}
