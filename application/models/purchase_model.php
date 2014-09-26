<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Purchase Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc purchase model
 * 
 */
class Purchase_model extends CI_Model
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
     * get unused giro
     * @return array $data
     */
    function getUnUsedGiro() {
        $data = $this->db
                ->where('giro_status',0)
                ->order_by('giro_code','asc')
                ->get('giro')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get giro price
     * @param int $id_giro
     * @return string giro value
     */
    function getGiroPrice($id_giro) {
        $data = $this->getGiroValueByID($id_giro);
        if (isset($data['giro_price'])) {
            return $data['giro_price'];
        } else {
            return '0';
        }
    }
    
    /**
     * get giro info
     * @param int $id_giro
     * @return array $data
     */
    function getGiroValueByID($id_giro) {
        $data = $this->db
                ->where('id_giro',$id_giro)
                ->limit(1)
                ->get('giro')
                ->row_array();
        
        return $data;
    }
    
    /**
     * update giro
     * @param int $id_giro
     * @param array $param
     */
    function UpdateGiro($id_giro,$param) {
        $this->db->where('id_giro',$id_giro);
        $this->db->update('giro',$param);
    }
    
    /**
     * insert division credit
     * @param array $param
     * @return int last inserted id
     */
    function InsertDivisionCredit($param) {
        $this->db->insert('division_credit',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * get division credit
     * @param int $id_division
     * @param boolean $is_paid
     * @return boolean
     */
    function getDivisionCredit($id_division,$is_paid=0) {
        $data = $this->db
                ->select('sum(credit_price) as total')
                ->where('credit_status',$is_paid)
                ->where('id_division',$id_division)
                ->limit(1)
                ->get('division_credit')
                ->row_array();
        if ($data) {
            return $data['total'];
        } else {
            return FALSE;
        }
    }
    
    /**
     * get purchase by id
     * @param int $id_purchase
     * @return array $data
     */
    function getPurchase($id_purchase) {
        $data = $this->db
                ->join('division','division.id_division=purchase.id_division','left')
                ->where('purchase.id_purchase',$id_purchase)
                ->where('purchase.is_delete',0)
                ->limit(1)
                ->get('purchase')
                ->row_array();
        if ($data) {
            $product = $this->db
                    ->join('item','item.id_item=purchase_production.id_item','left')
                    ->where('purchase_production.id_purchase',$data['id_purchase'])
                    ->get('purchase_production')
                    ->result_array();
            $data['production'] = $product;
        }
        
        return $data;
    }
    
    /**
     * insert new record purchase
     * @param array $post
     * @return boolean last id or false
     */
    function InsertRecord($post) {
        $this->db->insert('purchase',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_purchase',$id);
        $this->db->update('purchase',$post);
    }
    
    /**
     * get production info by code
     * @param string $production_code
     * @return array $data
     */
    function getProduction($production_code) {
        $data = $this->db
                ->where('LCASE(production_code)',strtolower($production_code))
                ->limit(1)
                ->get('production')
                ->row_array();
        
        return $data;
    }
    
    /**
     * insert purchase production
     * @param array $post
     * @return int last inserted id
     */
    function InsertPurchaseProduction($post) {
        $this->db->insert('purchase_production',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update production stock
     * @param array $param
     */
    function UpdateProductionStock($param) {
        // update detail production
        // check if exists
        $data = $this->getProduction($param['production_code']);
        if ($data) {
            // if exists then update
            $this->db->set('production_stock', '`production_stock`+'.$param['purchase_qty'].'', FALSE);
            $this->db->set('production_hpp_price', ''.$param['purchase_hpp_price'].'', FALSE);
            $this->db->set('production_sell_price', ''.$param['purchase_sales_price'].'', FALSE);
            $this->db->set('production_discount_price', ''.$param['purchase_discount_price'].'', FALSE);
            $this->db->where('id_production',$data['id_production']);
            $this->db->update('production');
        } else {
            // if not exists then insert
            $product['production_code'] = $param['production_code'];
            $product['id_item'] = $param['id_item'];
            $product['production_stock'] = $param['purchase_qty'];
            $product['production_hpp_price'] = $param['purchase_hpp_price'];
            $product['production_sell_price'] = $param['purchase_sales_price'];
            $product['production_discount_price'] = $param['purchase_discount_price'];
            //$product['production_type'] = 1;
            $this->db->insert('production',$product);
        }
    }
    
    /**
     * update item info
     * @param array $param
     */
    function UpdateItemStock($param) {
        $this->db->set('item_stock', '`item_stock`-'.$param['purchase_qty'].'', FALSE);
        $this->db->set('item_hpp_price', ''.$param['purchase_hpp_price'].'', FALSE);
        $this->db->set('item_sell_price', ''.$param['purchase_sales_price'].'', FALSE);
        $this->db->where('id_item',$param['id_item']);
        $this->db->update('item');
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
        $this->db->where('id_purchase',$id);
        $this->db->update('purchase',array('is_delete'=>1));
    }
    
    /**
     * 
     * @param int $id_purchase
     * @return array $data
     */
    function getPaymentByTransactionID($id_purchase) {
        $data = $this->db
                ->where('id_purchase',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('purchase_payment')
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
                ->where('id_purchase',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('purchase_payment')
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
        $this->db->insert('purchase_payment',$param);
        $last_id = $this->db->insert_id();
        
        return $last_id;
    }
    
    /**
     * update payment data
     * @param int $id
     * @param array $param
     */
    function UpdatePayment($id,$param) {
        $this->db->where('id_purchase_payment',$id);
        $this->db->update('purchase_payment',$param);
    }
    
    /**
     * update division credit
     * @param int $id_division
     * @param array $param
     */
    function UpdateDivisionCredit($id_division,$param) {
        $this->db->where('id_division',$id_division);
        $this->db->update('division_credit',$param);
    }
    
    /**
     * get all division items
     * @param int $id_division
     * @return array $data
     */
    function getItemByDivision($id_division) {
        $data = $this->db
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->where('id_division',$id_division)
                ->where('item_type',1)
                ->where('item.is_delete',0)
                ->get('item')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get item info
     * @param int $id_item
     * @param int $id_division
     * @return array $data
     */
    function getItemInfo($id_item,$id_division) {
        $data = $this->db
                ->select('*,item_hpp_price as hpp,item_sell_price as price')
                ->where('is_delete',0)
                ->where('id_division',$id_division)
                ->where('id_item',$id_item)
                ->get('item')
                ->row_array();
        
        return $data;
    }
    
    
    /**
     * get purchase production detail
     * @param int $id_purchase
     * @return array $data
     */
    function getPurchaseProduction($id_purchase) {
        $data = $this->db
                ->select("
                    purchase_production.id_item,purchase_production.id_purchase_production,purchase_production.production_code,
                    sum({$this->db->dbprefix('purchase_production')}.purchase_qty) as total_qty,
                    item.item_name,item.item_code,item_category.item_category
                ")
                ->join('item','item.id_item=purchase_production.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->where('id_purchase',$id_purchase)
                ->order_by('item_category')
                ->group_by('purchase_production.id_purchase_production')
                ->get('purchase_production')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get product retur
     * @param int $id_purchase
     * @return array $data
     */
    function getPurchaseRetur($id_purchase) {
        $data = $this->db
                ->select("
                    purchase_retur.id_item,purchase_retur.id_purchase_retur,purchase_retur.id_purchase_production,purchase_retur.production_code,
                    sum({$this->db->dbprefix('purchase_retur')}.retur_qty) as total_qty,
                    item.item_name,item.item_code,item_category.item_category
                ")
                ->join('item','item.id_item=purchase_retur.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->where('id_purchase',$id_purchase)
                ->order_by('item_category')
                ->group_by('purchase_retur.id_purchase_production')
                ->get('purchase_retur')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert retur
     * @param array $param
     * @return int last inserted id
     */
    function InsertPurchaseRetur($param) {
        $this->db->insert('purchase_retur',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * update stock product after retur
     * @param int $id_purchase_production
     * @param int $qty
     */
    function UpdateProductionStockRetur($id_purchase_production,$qty) {
        $data = $this->getPurchaseProductionInfo($id_purchase_production);
        //update to production
        $this->db->set('production_stock_retur', '`production_stock_retur`+'.$qty.'', FALSE);
        $this->db->set('production_status', '3', FALSE);
        $this->db->where('LCASE(production_code)',strtolower($data['production_code']));
        $this->db->update('production');
        
        // update stock item
        $this->db->set('item_stock_retur', '`item_stock_retur`+'.$qty.'', FALSE);
        $this->db->set('item_stock', '`item_stock`+'.$qty.'', FALSE);
        $this->db->where('id_item',$data['id_item']);
        $this->db->update('item');
    }
    
    /**
     * check if production is already retured
     * @param int $id_purchase
     * @param int $id_purchase_production
     * @return boolean
     */
    function CheckPurchaseRetur($id_purchase,$id_purchase_production) {
        $data = $this->db
                ->select('count(id_purchase_retur) as total')
                ->where('id_purchase',$id_purchase)
                ->where('id_purchase_production',$id_purchase_production)
                ->limit(1)
                ->get('purchase_retur')
                ->row_array();
                    
        if ($data['total'] > 0) {
            // return false if already retured
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     * get purchase production info
     * @param int $id_purchase
     * @param int $id_purchase_production
     * @return array $data
     */
    function getPurchaseProductionInfo($id_purchase,$id_purchase_production) {
        $data = $this->db
                ->where('id_purchase',$id_purchase)
                ->where('id_purchase_production',$id_purchase_production)
                ->limit(1)
                ->get('purchase_production')
                ->row_array();
        
        return $data;
    }
    
    /**
     * check exists production code
     * @param string $production_code
     * @return boolean
     */
    function CheckExistsProductionCode($production_code) {
        $total = $this->db
                ->where('LCASE(production_code)', $production_code)
                ->from('production')
                ->count_all_results();
        
        if ($total>0) {
            // if exists return false
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

/* End of file purchase_model.php */
/* Location: ./application/model/purchase_model.php */

