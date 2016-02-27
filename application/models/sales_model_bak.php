<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sales Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc sales model
 * 
 */
class Sales_model extends CI_Model
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
     * get store
     * @return array $data
     */
    function getStores() {
        $data = $this->db
                ->where('is_delete',0)
                ->order_by('store')
                ->get('store')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert giro
     * @param type $param
     * @return type
     */
    function InsertGiro($param) {
        $this->db->insert('giro',$param);
        $last_id = $this->db->insert_id();
        
        return $last_id;
    }
    
    /**
     * get division
     * @return array $data
     */
    function getDivisions() {
        $data = $this->db
                ->where('is_delete',0)
                ->order_by('division')
                ->get('division')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get productions
     */
    function getProductions($id_division=0) {
        if ($id_division) {
            $this->db->where('id_division',$id_division);
        }
        $data = $this->db
                ->where('is_delete',0)
                ->where('production_type',1)
                ->where('production_status',0)
                ->where('production_hpp_price !=','')
                ->order_by('production_name')
                ->get('production')
                ->result_array();
        
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
                ->select('*,production_hpp_price as price_hpp')
                ->where('is_delete',0)
                ->where('id_division',$id_division)
                ->where('id_production',$id_production)
                ->order_by('production_name')
                ->get('production')
                ->row_array();
        
        return $data;
    }
    
    /**
     * get sales detail
     * @param int $id_sales
     * @return array $data
     */
    function getSales($id_sales) {
        $data = $this->db
                ->where('sales.is_delete',0)
                ->where('sales.id_sales',$id_sales)
                ->join('store','store.id_store=sales.id_store','left')
                ->join('division','division.id_division=sales.id_division','left')
                ->limit(1)
                ->get('sales')
                ->row_array();
        if ($data) {
            $data['productions'] = $this->db
                    ->where('id_sales',$data['id_sales'])
                    ->join('production','production.id_production=sales_production.id_production','left')
                    ->join('production_category','production_category.id_production_category=production.id_production_category','left')
                    ->order_by('production.production_code')
                    ->get('sales_production')
                    ->result_array();
        }
        
        return $data;
    }
    
    /**
     * insert new record sales
     * @param array $post
     * @return boolean last id or false
     */
    function InsertRecord($post) {
        $this->db->insert('sales',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update supplier record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_sales',$id);
        $this->db->update('sales',$post);
    }
    
    /**
     * insert sales production
     * @param array $param
     */
    function InsertProduction($param) {
        $this->db->insert('sales_production',$param);
    }
    
    /**
     * change production status
     * @param int $id_production
     * @param array $param
     */
    function UpdateProduction($id_production,$param) {
        $this->db->where('id_production',$id_production);
        $this->db->update('production',$param);
    }
    
    /**
     * get store detail
     * @param int $id
     * @return array $data
     */
    function getStoreByID($id) {
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
        $this->db->where('id_sales',$id);
        $this->db->update('sales',array('is_delete'=>1));
    }
    
    /**
     * 
     * @param int $id_purchase
     * @return array $data
     */
    function getPaymentByTransactionID($id_purchase) {
        $data = $this->db
                ->where('id_sales',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('sales_payment')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get total transaction that has been paid
     * @param int $id_purchase
     * @return int total
     */
    function getTotalPayment($id_purchase) {
        $data = $this->db
                ->select('sum(payment_total) as total_paid')
                ->where('id_sales',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('sales_payment')
                ->row_array();
        if ($data) {
            return $data['total_paid'];
        } else {
            return 0;
        }
    }
    
    /**
     * insert payment
     * @param array $param
     * @return int $last_id
     */
    function InsertPayment($param) {
        $this->db->insert('sales_payment',$param);
        $last_id = $this->db->insert_id();
        
        return $last_id;
    }
    
    /**
     * update payment data
     * @param int $id
     * @param array $param
     */
    function UpdatePayment($id,$param) {
        $this->db->where('id_sales_payment',$id);
        $this->db->update('sales_payment',$param);
    }

}

/* End of file sales_model.php */
/* Location: ./application/model/sales_model.php */

