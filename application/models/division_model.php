<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Division Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc division model
 * 
 */
class Division_model extends CI_Model
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
     * get division product
     * @param int $id_division
     * @return array $data
     */
    function getDivisionProduct($id_division) {
        $data = $this->db
                ->select('product.*,division_product_stock as total_qty')
                ->join('product','product.id_product=division_product.id_product','left')
                ->where('division_product.id_division',$id_division)
                ->order_by('product.product_name')
                ->get('division_product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert new record division
     * @param array $post
     * @return boolean last id or false
     */
    function InsertNewRecord($post) {
        $this->db->insert('division',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update division record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_division',$id);
        $this->db->update('division',$post);
    }
    
    /**
     * get division detail
     * @param int $id
     * @return array $data
     */
    function getDivision($id) {
        $data = $this->db
            ->where('id_division',$id)
            ->where('is_delete',0)
            ->limit(1)
            ->get('division')
            ->row_array();
        return $data;
    }
    
    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_division',$id);
        $this->db->update('division',array('is_delete'=>1));
    }
    
    /**
     * chack exist code/prefix
     * @param string $param
     * @param string $type
     * @return boolean
     */
    function check_exists_code_pref($param,$type='code') {
        $total = $this->db
                    ->where('is_delete',0)
                    ->where('LCASE(division_'.$type.')',strtolower($param))
                    ->from('division')
                    ->count_all_results();
        if ($total > 0) {
            return true;
        } else {
            return false;
        }
    }

}

/* End of file division_model.php */
/* Location: ./application/model/division_model.php */

