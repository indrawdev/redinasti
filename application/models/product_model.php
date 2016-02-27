<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Product Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc product model
 * 
 */
class Product_model extends CI_Model {

    /**
     * Constructor 
     * @desc to load extends
     */
    function __construct() {
        parent::__construct();
    }
    
    /**
     * get supplier product
     * @param int $id_product
     * @return array $data
     */
    function getSupplierProduct($id_product) {
        $data = $this->db
                ->join('supplier','supplier.id_supplier=supplier_product.id_supplier','left')
                ->where('supplier.is_delete',0)
                ->where('supplier_product.id_product',$id_product)
                ->order_by('supplier.supplier')
                ->get('supplier_product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get all categories
     * @return array $data
     */
    function getCategories() {
        $data = $this->db
                ->order_by('category','asc')
                ->get('category')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get product detail
     * @param int $id
     * @return array $data
     */
    function getProduct($id) {
        $data = $this->db
                ->where('id_product', $id)
                ->where('is_delete', 0)
                ->limit(1)
                ->get('product')
                ->row_array();
        return $data;
    }

    /**
     * check exists product code or pref
     * @param string $path
     * @param int $id
     * @return boolean
     */
    function check_exists_code_pref($path, $type='code', $id = 0) {
        $this->db->where('LCASE(product_'.$type.')', strtolower($path));
        if ($id) {
            $this->db->where('id_product !=', $id);
        }
        $this->db->from('product');
        $total = $this->db->count_all_results();
        if ($total > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * update data
     * @param int $id
     * @param array $param
     */
    function UpdateData($id, $param) {
        $this->db->where('id_product', $id);
        $this->db->update('product', $param);
    }

    /**
     * insert new data
     * @param array $param
     * @return int $last_id last inserted id
     */
    function InsertData($param) {
        $this->db->insert('product', $param);
        $last_id = $this->db->insert_id();
        return $last_id;
    }

    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_product', $id);
        $this->db->update('product', array('is_delete' => 1));
    }

}

/* End of file product_model.php */
/* Location: ./application/model/product_model.php */

