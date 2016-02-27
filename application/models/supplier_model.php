<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Supplier Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc supplier model
 * 
 */
class Supplier_model extends CI_Model
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
     * get list product
     * @return array $data
     */
    function listProduct() {
        $data = $this->db
                ->where('is_delete',0)
                ->order_by('product_name')
                ->get('product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get supplier product
     * @param int $id_supplier
     * @return array $data
     */
    function getSupplierProduct($id_supplier) {
        $data = $this->db
                ->join('product','product.id_product=supplier_product.id_product','left')
                ->where('product.is_delete',0)
                ->where('supplier_product.id_supplier',$id_supplier)
                ->order_by('supplier_product.id_supplier_product')
                ->get('supplier_product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * supplier product connection
     * @param int $id_supplier
     * @return array $data
     */
    function connSupplierProduct($id_supplier) {
        $return = array();
        $data = $this->getSupplierProduct($id_supplier);
        $i=0;
        foreach ($data as $row => $val) {
            $return[$i] = $val['id_product'];
            $i++;
        }
        
        return $return;
    }

    /**
     * insert supplier product
     * @param array $param
     */
    function InsertSupplierProduct($param) {
        $this->db->insert('supplier_product',$param);
    }
    
    /**
     * delete supplier product
     * @param int $id_supplier
     */
    function DeleteSupplierProduct($id_supplier) {
        $this->db->where('id_supplier',$id_supplier);
        $this->db->delete('supplier_product');
    }

    /**
     * insert new record supplier
     * @param array $post
     * @return boolean last id or false
     */
    function InsertNewRecord($post) {
        $this->db->insert('supplier',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update supplier record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_supplier',$id);
        $this->db->update('supplier',$post);
    }
    
    /**
     * get supplier detail
     * @param int $id
     * @return array $data
     */
    function getSupplier($id) {
        $data = $this->db
            ->where('id_supplier',$id)
            ->where('is_delete',0)
            ->limit(1)
            ->get('supplier')
            ->row_array();
        return $data;
    }
    
    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_supplier',$id);
        $this->db->update('supplier',array('is_delete'=>1));
    }

}

/* End of file supplier_model.php */
/* Location: ./application/model/supplier_model.php */

