<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Production Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc production model
 * 
 */
class Production_model extends CI_Model
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
     * get production category data list
     * @return array $data
     */
    function getProductionCategory() {
        $data = $this->db
                ->order_by('production_category')
                ->get('production_category')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert new category production
     * @param array $param
     * @return int last inserted id
     */
    function InsertProductCategory($param) {
        $this->db->insert('production_category',$param);
        
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
     * get division product
     * @param int $id_division
     * @return array $data
     */
    function getDivisionProducts($id_division) {
        $data = $this->db
                ->join('product','product.id_product=division_product.id_product','left')
                ->join('category','category.id_category=product.id_category','left')
                ->where('division_product.division_product_stock >',0)
                ->where('division_product.id_division',$id_division)
                ->order_by('product.product_name')
                ->get('division_product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get product info
     * @param int $id_product
     * @param int $id_division
     * @return array $data
     */
    function getProductInfo($id_product,$id_division) {
        $data = $this->db
                ->join('product','product.id_product=division_purchase_product.id_product','left')
                ->join('category','category.id_category=product.id_category','left')
                ->where('product.is_delete',0)
                ->where('division_purchase_product.id_division',$id_division)
                ->where('division_purchase_product.id_product',$id_product)
                ->order_by('product.product_name')
                ->get('division_purchase_product')
                ->row_array();
        
        return $data;
    }
    
    /**
     * get production info
     * @param int $id_production
     * @param int $id_division
     * @return array $data
     */
    function getProductionInfo($id_production,$id_division) {
        $data = $this->db
                ->select('*,production_hpp_price as price')
                ->where('is_delete',0)
                ->where('id_division',$id_division)
                ->where('id_production',$id_production)
                ->order_by('production_name')
                ->get('production')
                ->row_array();
        
        return $data;
    }
    
    /**
     * get division transaction
     * @param int $id_purchase
     * @return array $data
     */
    function getDivisionPurchase($id_purchase) {
        $data = $this->db
                ->join('division','division.id_division=production.id_division','left')
                ->where('production.id_production',$id_purchase)
                ->where('production.is_delete',0)
                ->limit(1)
                ->get('production')
                ->row_array();
        if ($data) {
            $product = $this->db
                    ->join('product','product.id_product=production_product.id_product','left')
                    ->join('category','category.id_category=product.id_category','left')
                    ->where('production_product.id_production',$data['id_production'])
                    ->get('production_product')
                    ->result_array();
            $data['product'] = $product;
        }
        
        return $data;
    }
    
    /**
     * get production detail
     * @param int $id
     * @return array $data
     */
    function getProduction($id) {
        $return = false;
        $data = $this->db
                ->join('division','division.id_division=production.id_division','left')
                ->join('production_category','production_category.id_production_category=production.id_production_category','left')
                ->where('id_production',$id)
                ->where('production.is_delete',0)
                ->limit(1)
                ->get('production')
                ->row_array();
        if ($data) {
            $return = $data;
            $return['products'] = $this->db
                    ->join('product','product.id_product=production_product.id_product','left')
                    ->join('category','category.id_category=product.id_category','left')
                    ->where('production_product.id_production',$data['id_production'])
                    ->get('production_product')
                    ->result_array();
            $return['productions'] = $this->db
                    ->where('id_production',$data['id_production'])
                    ->get('production_production')
                    ->result_array();
            $return['costs'] = $this->db
                    ->where('id_production',$data['id_production'])
                    ->get('production_cost')
                    ->result_array();
        }
        return $return;
    }
    
    /**
     * insert new record division purchase
     * @param array $post
     * @return boolean last id or false
     */
    function InsertRecord($post) {
        $this->db->insert('production',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update division record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_production',$id);
        $this->db->update('production',$post);
    }
    
    /**
     * insert production product detail
     * @param array $param
     */
    function InsertProductionProduct($param) {
        $this->db->insert('production_product',$param);
    }
    
    /**
     * insert production cost detail
     * @param array $param
     */
    function InsertProductionCost($param) {
        $this->db->insert('production_cost',$param);
    }
    
    /**
     * update product stock
     * @param int $id_product
     * @param int $stock
     * @param decimal $price
     * @param int $id_division
     */
    function UpdateProductStock($id_product,$stock,$price,$id_division) {
        // update master product
        $this->db->set('division_product_stock', '`division_product_stock`-'.$stock.'', FALSE);
        $this->db->set('division_product_price', ''.$price.'', FALSE);
        $this->db->where('id_product',$id_product);
        $this->db->where('id_division',$id_division);
        $this->db->update('division_product');
    }
    
    /**
     * get product by division
     * @param int $id_product
     * @param int $id_division
     * @return array $data
     */
    function getDivisionProduct($id_product,$id_division=0) {
        if ($id_division) {
            $this->db->where('id_division',$id_division);
        }
        $data = $this->db
                ->where('id_product',$id_product)
                ->limit(1)
                ->get('division_product')
                ->row_array();
        
        return $data;
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
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_production',$id);
        $this->db->update('production',array('is_delete'=>1));
    }
    
    /**
     * check quantity division product 
     * @param int $id_division
     * @param int $id_product
     * @param int $qty
     * @return boolean
     */
    function checkQty($id_division,$id_product,$qty) {
        $data = $this->db
                ->select('sum(division_product_stock) as total')
                ->where('id_division',$id_division)
                ->where('id_product',$id_product)
                ->get('division_product')
                ->row_array();
        if ($data) {
            if (isset($data['division_product_stock']) && $data['division_product_stock']>=$qty) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * get division production
     * @param int $id_division
     * @return array $data
     */
    function getDivisionProductions($id_division) {
        $data = $this->db
                ->where('id_division',$id_division)
                ->where('is_delete',0)
                ->where('production_type !=',1)
                ->where('production_status',0)
                ->where('production_hpp_price !=','')
                ->get('production')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert production to production detail
     * @param array $param
     */
    function InsertProductionProduction($param) {
        $data = $this->db
                ->select('count(*) as total')
                ->where('id_division',$param['id_division'])
                ->where('id_production',$param['id_production'])
                ->where('production_id',$param['production_id'])
                ->get('production_production')
                ->row_array();
        
        if ($data['total']==0) {
            // insert to detail
            $this->db->insert('production_production',$param);
            
            // update status production
            $this->db->where('id_production',$param['id_production']);
            $this->db->update('production',array('production_status'=>1));
        }
    }
    

}

/* End of file production_model.php */
/* Location: ./application/model/production_model.php */

