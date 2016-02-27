<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Supplier Purchase Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc supplier purchase model
 * 
 */
class Supplier_purchase_model extends CI_Model
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
     * get supplier
     * @return type
     */
    function getAllSupplier() {
        $data = $this->db
                ->where('is_delete',0)
                ->order_by('supplier')
                ->get('supplier')
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
     * insert supplier credit
     * @param array $param
     * @return int last inserted id
     */
    function InsertSupplierCredit($param) {
        $this->db->insert('supplier_credit',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * get supplier credit
     * @param int $id_supplier
     * @param boolean $is_paid
     * @return boolean
     */
    function getSupplierCredit($id_supplier,$is_paid=0) {
        $data = $this->db
                ->select('sum(credit_price) as total')
                ->where('credit_status',$is_paid)
                ->where('id_supplier',$id_supplier)
                ->limit(1)
                ->get('supplier_credit')
                ->row_array();
        if ($data) {
            return $data['total'];
        } else {
            return FALSE;
        }
    }
    
    /**
     * get product
     */
    function getProducts() {
        $data = $this->db
                ->join('category','category.id_category=product.id_category','left')
                ->where('product.is_delete',0)
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
                ->order_by('product.product_name')
                ->limit(1)
                ->get('product')
                ->row_array();
        
        return $data;
    }
    
    /**
     * get supplier transaction
     * @param int $id_purchase
     * @return array $data
     */
    function getSupplierPurchase($id_purchase) {
        $data = $this->db
                ->join('supplier','supplier.id_supplier=supplier_purchase.id_supplier','left')
                ->where('supplier_purchase.id_supplier_purchase',$id_purchase)
                ->where('supplier_purchase.is_delete',0)
                ->limit(1)
                ->get('supplier_purchase')
                ->row_array();
        if ($data) {
            $product = $this->db
                    ->join('product','product.id_product=supplier_purchase_product.id_product','left')
                    ->join('category','category.id_category=product.id_category','left')
                    ->where('supplier_purchase_product.id_supplier_purchase',$data['id_supplier_purchase'])
                    ->get('supplier_purchase_product')
                    ->result_array();
            $data['product'] = $product;
        }
        
        return $data;
    }
    
    /**
     * search supplier
     * @param string $q
     * @return array $data
     */
    function SearchSupplier($q,$limit=10) {
        $data = $this->db
                ->select('id_supplier as id, supplier as text, supplier_address as address')
                ->like('LCASE(supplier)',strtolower($q))
                ->where('is_delete',0)
                ->order_by('supplier','asc')
                ->limit($limit)
                ->get('supplier')
                ->result_array();
        
        return $data;
    }
    
    /**
     * insert new record supplier purchase
     * @param array $post
     * @return boolean last id or false
     */
    function InsertRecord($post) {
        $this->db->insert('supplier_purchase',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update supplier record
     * @param int $id
     * @param array $post
     */
    function UpdateRecord($id,$post) {
        $this->db->where('id_supplier_purchase',$id);
        $this->db->update('supplier_purchase',$post);
    }
    
    /**
     * insert product stock
     * @param type $post
     * @return type
     */
    function InsertStock($post) {
        $this->db->insert('supplier_purchase_product',$post);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update product stock
     * @param int $id_product
     * @param int $stock
     * @param decimal $price
     * @param array $param
     */
    function UpdateProductStock($id_product,$stock,$price) {
        $this->db->set('product_stock', '`product_stock`+'.$stock.'', FALSE);
        $this->db->set('buy_price', ''.$price.'', FALSE);
        $this->db->set('sell_price', ''.markupPrice($price).'', FALSE);
        $this->db->where('id_product',$id_product);
        $this->db->update('product');
    }
    
    /**
     * get supplier detail
     * @param int $id
     * @return array $data
     */
    function getSupplierByID($id) {
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
        $this->db->where('id_supplier_purchase',$id);
        $this->db->update('supplier_purchase',array('is_delete'=>1));
    }
    
    /**
     * 
     * @param int $id_purchase
     * @return array $data
     */
    function getPaymentByTransactionID($id_purchase) {
        $data = $this->db
                ->where('id_supplier_purchase',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('supplier_purchase_payment')
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
                ->where('id_supplier_purchase',$id_purchase)
                ->order_by('payment_date','desc')
                ->get('supplier_purchase_payment')
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
        $this->db->insert('supplier_purchase_payment',$param);
        $last_id = $this->db->insert_id();
        
        return $last_id;
    }
    
    /**
     * update payment data
     * @param int $id
     * @param array $param
     */
    function UpdatePayment($id,$param) {
        $this->db->where('id_supplier_purchase_payment',$id);
        $this->db->update('supplier_purchase_payment',$param);
    }
    
    /**
     * update supploer credit
     * @param int $id_supplier
     * @param array $param
     */
    function UpdateSupplierCredit($id_supplier,$param) {
        $this->db->where('id_supplier',$id_supplier);
        $this->db->update('supplier_credit',$param);
    }
    
    /**
     * get purchase product detail
     * @param int $id_supplier_purchase
     * @return array $data
     */
    function getSupplierPurchaseProduct($id_supplier_purchase) {
        $data = $this->db
                ->select("
                    supplier_purchase_product.id_product,supplier_purchase_product.id_supplier_purchase_product,
                    sum({$this->db->dbprefix('supplier_purchase_product')}.purchase_qty) as total_qty,
                    product.product_name,product.product_code,product.product_note
                ")
                ->join('product','product.id_product=supplier_purchase_product.id_product','left')
                ->where('id_supplier_purchase',$id_supplier_purchase)
                ->order_by('product_name')
                ->group_by('supplier_purchase_product.id_product')
                ->get('supplier_purchase_product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get product retur
     * @param int $id_supplier_purchase
     * @return array $data
     */
    function getPurchaseRetur($id_supplier_purchase) {
        $data = $this->db
                ->select("
                    supplier_purchase_retur.id_product,supplier_purchase_retur.id_supplier_purchase_retur,
                    sum({$this->db->dbprefix('supplier_purchase_retur')}.retur_qty) as total_qty,
                    product.product_name,product.product_code,product.product_note
                ")
                ->join('product','product.id_product=supplier_purchase_retur.id_product','left')
                ->where('id_supplier_purchase',$id_supplier_purchase)
                ->order_by('product_name')
                ->group_by('supplier_purchase_retur.id_product')
                ->get('supplier_purchase_retur')
                ->result_array();
        
        return $data;
    }
    
    /**
     * cleaning up the retur
     * @param int $id_supplier_purchase
     */
    function DeletePurchaseRetur($id_supplier_purchase) {
        $this->db->where('id_supplier_purchase',$id_supplier_purchase);
        $this->db->delete('supplier_purchase_retur');
    }
    
    /**
     * insert retur
     * @param array $param
     * @return int last inserted id
     */
    function InsertPurchaseRetur($param) {
        $this->db->insert('supplier_purchase_retur',$param);
        
        return $this->db->insert_id();
    }
    
    /**
     * update stock product after retur
     * @param int $id_product
     * @param int $qty
     */
    function UpdateProductStockRetur($id_product,$qty) {
        $this->db->set('product_stock', '`product_stock`-'.$qty.'', FALSE);
        $this->db->set('product_retur', '`product_retur`+'.$qty.'', FALSE);
        $this->db->where('id_product',$id_product);
        $this->db->update('product');
    }
    
    /**
     * get quantity purchase product
     * @param int $id_supplier_purchase
     * @param int $id_product
     * @return string quantity
     */
    function getSupplierPurchaseProductReturQty($id_supplier_purchase,$id_product) {
        $data = $this->db
                ->select("
                    coalesce(sum({$this->db->dbprefix('supplier_purchase_product')}.purchase_qty),0) as t_purchase_qty,coalesce({$this->db->dbprefix('supplier_purchase_retur')}.t_retur_qty,0) as t_retur_qty
                ",FALSE)
                ->join(
                    "( 
                        select 
                            id_supplier_purchase,id_product,COALESCE(sum(retur_qty),0) as t_retur_qty
                        from {$this->db->dbprefix('supplier_purchase_retur')} 
                        where id_supplier_purchase='{$id_supplier_purchase}' and id_product='{$id_product}'
                        group by id_product
                    ) {$this->db->dbprefix('supplier_purchase_retur')}",
                    'supplier_purchase_retur.id_product=supplier_purchase_product.id_product',
                    'left'
                )
                ->where('supplier_purchase_product.id_supplier_purchase',$id_supplier_purchase)
                ->where('supplier_purchase_product.id_product',$id_product)
                ->group_by('supplier_purchase_product.id_product')
                ->limit(1)
                ->get('supplier_purchase_product')
                ->row_array();
                    
        if (isset($data['t_purchase_qty']) && isset($data['t_retur_qty'])) {
            return ($data['t_purchase_qty']-$data['t_retur_qty']);
        } else {
            return '0';
        }
    }
    
    /**
     * get maximum sales price product
     * @param int $id_supplier_purchase
     * @param int $id_product
     * @return string max price
     */
    function MaxPricePurchaseProduct($id_supplier_purchase,$id_product) {
        $data = $this->db
                ->select("max(purchase_price) as max_price")
                ->where('id_supplier_purchase',$id_supplier_purchase)
                ->where('id_product',$id_product)
                ->group_by('id_product')
                ->get('supplier_purchase_product')
                ->row_array();
        if (isset($data['max_price'])) {
            return $data['max_price'];
        } else {
            return '0';
        }
    }

}

/* End of file supplier_purchase_model.php */
/* Location: ./application/model/supplier_purchase_model.php */

