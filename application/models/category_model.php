<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Category Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc category model
 * 
 */
class Category_model extends CI_Model {

    /**
     * Constructor 
     * @desc to load extends
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * inser new record category
     * @param array $post
     * @return boolean last id or false
     */
    function InsertNewRecord($post) {
        $this->db->insert('category', array(
            'category' => $post['category'],
            )
        );
        $last_id = $this->db->insert_id();
        return $last_id;
    }

    /**
     * update category record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id, $post) {
        $this->db->where('id_category', $id);
        $this->db->update('category', array(
            'category' => $post['category'],
            )
        );
    }

    /**
     * get category detail
     * @param int $id
     * @return array $data
     */
    function getCategory($id) {
        $data = $this->db
                ->where('id_category', $id)
                ->where('is_delete', 0)
                ->limit(1)
                ->get('category')
                ->row_array();
        return $data;
    }
    
    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_category', $id);
        $this->db->update('category', array('is_delete' => 1));
    }

}

/* End of file category_model.php */
/* Location: ./application/model/category_model.php */

