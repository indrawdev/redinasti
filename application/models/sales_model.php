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
    function getProductions() {
        $data = $this->db
                ->join('item','item.id_item=production.id_item','left')
                ->where('production.is_delete',0)
                //->where('production_type',1)
                ->where('production_status',0)
                ->where('production_hpp_price !=','')
                ->order_by('production_code')
                ->get('production')
                ->result_array();
        
        return $data;         
    }
    
    /**
     * get production info
     * @param int $id_production
     * @return array $data
     */
    function getProductionInfo($id_production) {
        $data = $this->db
                ->select('
                    production.*,production_hpp_price as price_hpp,
                    item.id_division,item.item_name,item.item_code,
                    item_category.item_category,
                    division.division
                ')
                ->join('item','item.id_item=production.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->join('division','division.id_division=item.id_division','left')
                ->where('production.is_delete',0)
                ->where('production.id_production',$id_production)
                ->order_by('production.production_code')
                ->limit(1)
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
                ->limit(1)
                ->get('sales')
                ->row_array();
        if ($data) {
            $data['productions'] = $this->db
                    ->where('id_sales',$data['id_sales'])
                    ->join('production','production.id_production=sales_production.id_production','left')
                    ->join('item','item.id_item=production.id_item','left')
                    ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                    ->join('division','division.id_division=item.id_division','left')
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
     * @param int $id_sales
     * @return array $data
     */
    function getPaymentByTransactionID($id_sales) {
        $data = $this->db
                ->where('id_sales',$id_sales)
                ->order_by('payment_date','desc')
                ->get('sales_payment')
                ->result_array();
        $i=0;
        foreach ($data as $row) {
            if (isset($row['id_giro']) && ($row['id_giro'] != '' || $row['id_giro'] != 0)) {
                $data[$i]['giro_info'] = $this->db
                        ->where('id_giro',$row['id_giro'])
                        ->limit(1)
                        ->get('giro')
                        ->row_array();
            }
            $i++;
        }
        
        return $data;
    }
    
    /**
     * get total transaction that has been paid
     * @param int $id_sales
     * @return int total
     */
    function getTotalPayment($id_sales) {
        $data = $this->db
                ->select('sum(payment_total) as total_paid')
                ->where('id_sales',$id_sales)
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
    
    
    /**
     * get sales production detail
     * @param int $id_sales
     * @return array $data
     */
    function getSalesProduction($id_sales) {
        $data = $this->db
                ->select("
                    sales_production.id_item,sales_production.id_sales_production,sales_production.id_production,sales_production.production_code,
                    sum({$this->db->dbprefix('sales_production')}.sales_qty) as total_qty,
                    item.item_name,item.item_code,item_category.item_category
                ")
                ->join('item','item.id_item=sales_production.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->where('id_sales',$id_sales)
                ->order_by('item_category')
                ->group_by('sales_production.id_sales_production')
                ->get('sales_production')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get product retur
     * @param int $id_sales
     * @return array $data
     */
    function getSalesRetur($id_sales) {
        $data = $this->db
                ->select("
                    sales_retur.id_item,sales_retur.id_sales_retur,sales_retur.id_production,sales_retur.production_code,
                    sum({$this->db->dbprefix('sales_retur')}.retur_qty) as total_qty,
                    item.item_name,item.item_code,item_category.item_category,
                    division.division
                ")
                ->join('item','item.id_item=sales_retur.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->join('division','division.id_division=item.id_division','left')
                ->where('id_sales',$id_sales)
                ->order_by('item_category')
                ->group_by('sales_retur.id_production')
                ->get('sales_retur')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert retur
     * @param array $param
     * @return int last inserted id
     */
    function InsertSalesRetur($param) {
        $this->db->insert('sales_retur',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * update stock product after retur
     * @param int $id_sales
     * @param int $id_sales_production
     * @param int $qty
     */
    function UpdateProductionStockRetur($id_sales,$id_sales_production,$qty) {
        $data = $this->getSalesProductionInfo($id_sales,$id_sales_production);
        //update to production
        $this->db->set('production_stock_retur', '`production_stock_retur`+'.$qty.'', FALSE);
        $this->db->set('production_status', '3', FALSE);
        $this->db->where('LCASE(production_code)',strtolower($data['production_code']));
        $this->db->update('production');
        
        // update stock item
        $this->db->set('item_stock_retur', '`item_stock_retur`-'.$qty.'', FALSE);
        $this->db->set('item_stock', '`item_stock`+'.$qty.'', FALSE);
        $this->db->where('id_item',$data['id_item']);
        $this->db->update('item');
    }
    
    /**
     * check if production is already retured
     * @param int $id_sales
     * @param int $id_sales_production
     * @return boolean
     */
    function CheckSalesRetur($id_sales,$id_sales_production) {
        $data = $this->db
                ->select('count(id_sales_retur) as total')
                ->where('id_sales',$id_sales)
                ->where('id_sales_production',$id_sales_production)
                ->limit(1)
                ->get('sales_retur')
                ->row_array();
                    
        if ($data['total'] > 0) {
            // return false if already retured
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     * get sales production info
     * @param int $id_sales
     * @param int $id_sales_production
     * @return array $data
     */
    function getSalesProductionInfo($id_sales,$id_sales_production) {
        $data = $this->db
                ->select('
                    sales_production.*,
                    item.item_name,item.item_code,
                    item_category.item_category,
                    division.division
                ')
                ->join('division','division.id_division=sales_production.id_division','left')
                ->join('item','item.id_item=sales_production.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->where('sales_production.id_sales',$id_sales)
                ->where('sales_production.id_sales_production',$id_sales_production)
                ->order_by('sales_production.production_code')
                ->limit(1)
                ->get('sales_production')
                ->row_array();
        
        return $data;
    }
    
    /**
     * get production info by store
     * @param int $id_production
     * @param int $id_store
     * @return array $data
     */
    function getProductionInfoByStore($id_production,$id_store=0) {
        $data = $this->getProductionInfo($id_production);
        if ($id_store) {
            $data['sales_production'] = $this->db
                    ->select('buy_price,sales_price,discount_percentage')
                    ->where('id_item',$data['id_item'])
                    ->where('id_store',$id_store)
                    ->limit(1)
                    ->order_by('id_sales_production','desc')
                    ->get('sales_production')
                    ->row_array();
        }
        
        return $data;
    }

}

/* End of file sales_model.php */
/* Location: ./application/model/sales_model.php */

