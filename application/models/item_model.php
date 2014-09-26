<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Item Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc item model
 * 
 */
class Item_model extends CI_Model
{
    /**
     * Constructor 
     * @desc to load extends
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get item category data list
     * @return array $data
     */
    function getItemCategory() {
        $data = $this->db
                ->order_by('item_category')
                ->get('item_category')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert new category item
     * @param array $param
     * @return int last inserted id
     */
    function InsertItemCategory($param) {
        $this->db->insert('item_category',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * get division
     * @return array $data
     */
    function getAllDivision() {
        $data = $this->db
                ->where('is_delete',0)
                ->order_by('division')
                ->get('division')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get item detail
     * @param int $id
     * @return array $data
     */
    function getItem($id) {
        $data = $this->db
                ->join('division','division.id_division=item.id_division','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->where('id_item',$id)
                ->where('item.is_delete',0)
                ->limit(1)
                ->get('item')
                ->row_array();
        return $data;
    }
    
    /**
     * insert new record division purchase
     * @param array $post
     * @return boolean last id or false
     */
    function InsertRecord($post) {
        $this->db->insert('item',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update division record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_item',$id);
        $this->db->update('item',$post);
    }
    
    /**
     * get division detail
     * @param int $id
     * @return array $data
     */
    function getDivisionByID($id) {
        $data = $this->db
            ->where('id_division',$id)
            ->where('is_delete',0)
            ->limit(1)
            ->get('division')
            ->row_array();
        return $data;
    }
    
    /**
     * get category by id
     * @param int $id
     * @return array $data
     */
    function getCategoryByID($id) {
        $data = $this->db
            ->where('id_item_category',$id)
            ->where('is_delete',0)
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
        $this->db->where('id_item',$id);
        $this->db->update('item',array('is_delete'=>1));
    }
    

}

/* End of file item_model.php */
/* Location: ./application/model/item_model.php */

