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
     * get division transaction
     * @param int $id_purchase
     * @return array $data
     */
    function getDivisionPurchase($id_purchase) {
        $data = $this->db
                ->join('division','division.id_division=division_purchase.id_division','left')
                ->where('division_purchase.id_division_purchase',$id_purchase)
                ->where('division_purchase.is_delete',0)
                ->limit(1)
                ->get('division_purchase')
                ->row_array();
        if ($data) {
            $data['product'] = $this->db
                    ->join('product','product.id_product=division_purchase_product.id_product','left')
                    ->join('category','category.id_category=product.id_category','left')
                    ->where('division_purchase_product.id_division_purchase',$data['id_division_purchase'])
                    ->get('division_purchase_product')
                    ->result_array();
            
            $data['credit'] = $this->db
                    ->where('id_division_purchase',$data['id_division_purchase'])
                    ->get('division_purchase_credit')
                    ->result_array();
        }
        
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
     * get division product
     * @param type $id_product
     * @param type $id_division
     * @return type
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
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_division_purchase',$id);
        $this->db->update('division_purchase',array('is_delete'=>1));
    }
    
    /**
     * 
     * @param int $id_purchase
     * @return array $data
     */
    function getPaymentByTransactionID($id_purchase) {
        $data = $this->db
                ->where('id_division_purchase',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('division_purchase_payment')
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
     * @param int $id_purchase
     * @return int total
     */
    function getTotalPayment($id_purchase) {
        $data = $this->db
                ->select('sum(payment_total) as total_paid')
                ->where('id_division_purchase',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('division_purchase_payment')
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
        $this->db->insert('division_purchase_payment',$param);
        $last_id = $this->db->insert_id();
        
        return $last_id;
    }
    
    /**
     * update payment data
     * @param int $id
     * @param array $param
     */
    function UpdatePayment($id,$param) {
        $this->db->where('id_division_purchase_payment',$id);
        $this->db->update('division_purchase_payment',$param);
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
    
    /**
     * insert division purchase credit
     * @param array $param
     * @return int last inserted id
     */
    function InsertDivisionCredit($param) {
        $this->db->insert('division_purchase_credit',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * get product buy price
     * @param int $id_product
     * @return string buy price
     */
    function getProductBuyPrice($id_product) {
        $data = $this->db
                ->select('buy_price')
                ->where('id_product',$id_product)
                ->limit(1)
                ->get('product')
                ->row_array();
        if ($data) {
            return $data['buy_price'];
        } else {
            return '0';
        }
    }
    
    
    /**
     * get purchase product detail
     * @param int $id_division_purchase
     * @return array $data
     */
    function getDivisionPurchaseProduct($id_division_purchase) {
        $data = $this->db
                ->select("
                    division_purchase_product.id_product,division_purchase_product.id_division_purchase_product,
                    sum({$this->db->dbprefix('division_purchase_product')}.purchase_qty) as total_qty,
                    product.product_name,product.product_code,product.product_note
                ")
                ->join('product','product.id_product=division_purchase_product.id_product','left')
                ->where('id_division_purchase',$id_division_purchase)
                ->order_by('product_name')
                ->group_by('division_purchase_product.id_product')
                ->get('division_purchase_product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get product retur
     * @param int $id_division_purchase
     * @return array $data
     */
    function getPurchaseRetur($id_division_purchase) {
        $data = $this->db
                ->select("
                    division_purchase_retur.id_product,division_purchase_retur.id_division_purchase_retur,
                    sum({$this->db->dbprefix('division_purchase_retur')}.retur_qty) as total_qty,
                    product.product_name,product.product_code,product.product_note
                ")
                ->join('product','product.id_product=division_purchase_retur.id_product','left')
                ->where('id_division_purchase',$id_division_purchase)
                ->order_by('product_name')
                ->group_by('division_purchase_retur.id_product')
                ->get('division_purchase_retur')
                ->result_array();
        
        return $data;
    }
    
    /**
     * cleaning up the retur
     * @param int $id_division_purchase
     */
    function DeletePurchaseRetur($id_division_purchase) {
        $this->db->where('id_division_purchase',$id_division_purchase);
        $this->db->delete('division_purchase_retur');
    }
    
    /**
     * insert retur
     * @param array $param
     * @return int last inserted id
     */
    function InsertPurchaseRetur($param) {
        $this->db->insert('division_purchase_retur',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * update stock product after retur
     * @param int $id_product
     * @param int $qty
     */
    function UpdateProductStockRetur($id_product,$id_division,$qty) {
        //update to product
        $this->db->set('product_stock', '`product_stock`+'.$qty.'', FALSE);
        //$this->db->set('product_retur', '`product_retur`+'.$qty.'', FALSE);
        $this->db->where('id_product',$id_product);
        $this->db->update('product');
        
        // update stock in division product
        $this->db->set('division_product_stock', '`division_product_stock`-'.$qty.'', FALSE);
        $this->db->set('division_product_retur', '`division_product_retur`+'.$qty.'', FALSE);
        $this->db->where('id_product',$id_product);
        $this->db->where('id_division',$id_division);
        $this->db->update('division_product');
    }
    
    /**
     * get quantity purchase product
     * @param int $id_division_purchase
     * @param int $id_product
     * @return string quantity
     */
    function getDivisionPurchaseProductReturQty($id_division_purchase,$id_product) {
        $data = $this->db
                ->select("
                    coalesce(sum({$this->db->dbprefix('division_purchase_product')}.purchase_qty),0) as t_purchase_qty,coalesce({$this->db->dbprefix('division_purchase_retur')}.t_retur_qty,0) as t_retur_qty
                ",FALSE)
                ->join(
                    "( 
                        select 
                            id_division_purchase,id_product,COALESCE(sum(retur_qty),0) as t_retur_qty
                        from {$this->db->dbprefix('division_purchase_retur')} 
                        where id_division_purchase='{$id_division_purchase}' and id_product='{$id_product}'
                        group by id_product
                    ) {$this->db->dbprefix('division_purchase_retur')}",
                    'division_purchase_retur.id_product=division_purchase_product.id_product',
                    'left'
                )
                ->where('division_purchase_product.id_division_purchase',$id_division_purchase)
                ->where('division_purchase_product.id_product',$id_product)
                ->group_by('division_purchase_product.id_product')
                ->limit(1)
                ->get('division_purchase_product')
                ->row_array();
                    
        if (isset($data['t_purchase_qty']) && isset($data['t_retur_qty'])) {
            return ($data['t_purchase_qty']-$data['t_retur_qty']);
        } else {
            return '0';
        }
    }
    
    /**
     * get maximum sales price product
     * @param int $id_division_purchase
     * @param int $id_product
     * @return string max price
     */
    function MaxPricePurchaseProduct($id_division_purchase,$id_product) {
        $data = $this->db
                ->select("max(purchase_price) as max_price")
                ->where('id_division_purchase',$id_division_purchase)
                ->where('id_product',$id_product)
                ->group_by('id_product')
                ->get('division_purchase_product')
                ->row_array();
        if (isset($data['max_price'])) {
            return $data['max_price'];
        } else {
            return '0';
        }
    }

}

/* End of file cashier_model.php */
/* Location: ./application/model/cashier_model.php */

