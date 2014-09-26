<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cashier Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc cashier model
 * 
 */
class Cashier_model extends CI_Model
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
     * get division
     * @return type
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
     * get product
     */
    function getProducts() {
        $data = $this->db
                ->join('category','category.id_category=product.id_category','left')
                ->where('product.is_delete',0)
                ->where('product.product_stock >',0)
                ->order_by('product.product_name')
                ->get('product')
                ->result_array();
        
        return $data;         
    }
    
    /**
     * get product info
     * @param int $id_product
     * @return array $data
     */
    function getProductInfo($id_product) {
        $data = $this->db
                ->join('category','category.id_category=product.id_category','left')
                ->where('product.is_delete',0)
                ->where('product.id_product',$id_product)
                ->where('product.product_stock >',0)
                ->order_by('product.product_name')
                ->limit(1)
                ->get('product')
                ->row_array();
        
        return $data;
    }
    
    /**
     * search division
     * @param string $q
     * @return array $data
     */
    function SearchDivision($q,$limit=10) {
        $data = $this->db
                ->select('id_division as id, division as text, division_address as address')
                ->like('LCASE(division)',strtolower($q))
                ->where('is_delete',0)
                ->order_by('division','asc')
                ->limit($limit)
                ->get('division')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert new record division purchase
     * @param array $post
     * @return boolean last id or false
     */
    function InsertRecord($post) {
        $this->db->insert('division_purchase',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update division record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_division_purchase',$id);
        $this->db->update('division_purchase',$post);
    }
    
    /**
     * insert product stock
     * @param type $post
     * @return type
     */
    function InsertDivisionProduct($post) {
        $this->db->insert('division_purchase_product',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update product stock
     * @param int $id_product
     * @param int $stock
     * @param decimal $price
     * @param int $id_division
     * @param array $param
     */
    function UpdateProductStock($id_product,$stock,$price,$id_division) {
        // update master product
        $this->db->set('product_stock', '`product_stock`-'.$stock.'', FALSE);
        $this->db->set('sell_price', ''.$price.'', FALSE);
        $this->db->where('id_product',$id_product);
        $this->db->update('product');
        
        // update detail division product
        // check if exists
        $data = $this->getDivisionProduct($id_product, $id_division);
        if ($data) {
            // if exists then update
            $this->db->set('division_product_stock', '`division_product_stock`+'.$stock.'', FALSE);
            $this->db->set('division_product_price', ''.$price.'', FALSE);
            $this->db->set('modify_date', ''.date('Y-m-d H:i:s').'', FALSE);
            $this->db->where('id_division_product',$data['id_division_product']);
            $this->db->update('division_product');
        } else {
            $param = array(
                'id_division'=>$id_division,
                'id_product'=>$id_product,
                'division_product_stock'=>$stock,
                'division_product_price'=>$price,
            );
            $this->db->insert('division_product',$param);
        }
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
     * get product stock in database
     * @param int $id_product
     * @return string total stock
     */
    function checkProductStock($id_product) {
        $data = $this->db
                ->select('product_stock')
                ->where('id_product',$id_product)
                ->where('is_delete',0)
                ->limit(1)
                ->get('product')
                ->row_array();
        
        if ($data) {
            return $data['product_stock'];
        } else {
            return '0';
        }
    }

}

/* End of file cashier_model.php */
/* Location: ./application/model/cashier_model.php */

