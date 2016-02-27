<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Item Category Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc item category model
 * 
 */
class Item_category_model extends CI_Model {

    /**
     * Constructor 
     * @desc to load extends
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * inser new record item  category
     * @param array $post
     * @return boolean last id or false
     */
    function InsertNewRecord($post) {
        $this->db->insert('item_category',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }

    /**
     * update item category record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id, $post) {
        $this->db->where('id_item_category', $id);
        $this->db->update('item_category',$post);
    }

    /**
     * get item category detail
     * @param int $id
     * @return array $data
     */
    function getItemCategory($id) {
        $data = $this->db
                ->where('id_item_category', $id)
                ->where('is_delete', 0)
                ->limit(1)
                ->get('item_category')
                ->row_array();
        return $data;
    }
    
    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_item_category', $id);
        $this->db->update('item_category', array('is_delete' => 1));
    }

}

/* End of file item_category_model.php */
/* Location: ./application/model/item_category_model.php */

