<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Store Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc store model
 * 
 */
class Store_model extends CI_Model
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
     * insert new record store
     * @param array $post
     * @return boolean last id or false
     */
    function InsertNewRecord($post) {
        $this->db->insert('store',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update store record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_store',$id);
        $this->db->update('store',$post);
    }
    
    /**
     * get store detail
     * @param int $id
     * @return array $data
     */
    function getStore($id) {
        $data = $this->db
            ->where('id_store',$id)
            ->where('is_delete',0)
            ->limit(1)
            ->get('store')
            ->row_array();
        return $data;
    }
    
    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_store',$id);
        $this->db->update('store',array('is_delete'=>1));
    }

}

/* End of file store_model.php */
/* Location: ./application/model/store_model.php */

