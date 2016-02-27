<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Report Model Class
 * @author ivan lubis
 * @version 2.1
 * @report Model
 * @desc report model
 * 
 */
class Report_model extends CI_Model {

    /**
     * Constructor 
     * @desc to load extends
     */
    function __construct() {
        parent::__construct();
    }
    
    /**
     * get all sales product
     * @param string $from
     * @param string $to
     * @return array $data
     */
    function getSalesProduct($from='',$to='') {
        if ($from && $to) {
            $this->db->where("division_purchase_product.create_date between '{$from}' and '{$to}'");
        }
        $data = $this->db
                ->select("
                    product.id_product,product.product_code,product.product_name,product.product_unit,product.product_note,product.buy_price,product.sell_price,product.product_stock,
                    sum({$this->db->dbprefix('division_purchase_product')}.purchase_qty) as total_sales_qty,
                    sum({$this->db->dbprefix('division_purchase_product')}.purchase_price * {$this->db->dbprefix('division_purchase_product')}.purchase_qty) as total_sales_price,
                    coalesce(sum({$this->db->dbprefix('division_purchase_retur')}.retur_qty),0) as total_retur_qty
                ",FALSE)
                ->join('product','product.id_product=division_purchase_product.id_product','left')
                ->join('division_purchase_retur','division_purchase_retur.id_product=division_purchase_product.id_product','left')
                ->group_by('division_purchase_product.id_product')
                ->get('division_purchase_product')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get sales product detail
     * @param int $id_product
     * @return array $data
     */
    function getSalesProductDetail($id_product) {
        $data = $this->db
                ->select('id_product,product_code,product_name,product_unit,product_note,buy_price,sell_price,product_stock')
                ->where('id_product',$id_product)
                ->limit(1)
                ->get('product')
                ->row_array();
        if ($data) {
            $sales = $this->db
                    ->select("
                        division_purchase.purchase_invoice,
                        division_purchase_product.purchase_qty,division_purchase_product.purchase_price,division_purchase_product.purchase_buy,
                        sum({$this->db->dbprefix('division_purchase_product')}.purchase_qty) as total_sales_qty,
                        sum({$this->db->dbprefix('division_purchase_retur')}.total_retur) as total_retur_qty,
                        sum({$this->db->dbprefix('division_purchase_product')}.purchase_price * {$this->db->dbprefix('division_purchase_product')}.purchase_qty) as total_sales_price,
                        division.division
                    ")
                    ->join('division_purchase','division_purchase.id_division_purchase=division_purchase_product.id_division_purchase','left')
                    ->join(
                        "( 
                            select 
                                id_division_purchase,sum(retur_qty) as total_retur
                            from {$this->db->dbprefix('division_purchase_retur')} 
                            where id_product='$id_product'
                            group by id_division_purchase
                        ) {$this->db->dbprefix('division_purchase_retur')}",
                        'division_purchase_retur.id_division_purchase=division_purchase.id_division_purchase',
                        'left'
                    )
                    ->join('division','division.id_division=division_purchase.id_division','left')
                    ->where('division_purchase_product.id_product',$data['id_product'])
                    ->group_by('division_purchase_product.id_division_purchase')
                    ->order_by('division_purchase_product.id_division_purchase')
                    ->get('division_purchase_product')
                    ->result_array();
            $data['sales'] = $sales;
        }
        return $data;
    }
    
    /**
     * get supplier purchase
     * @param string $from
     * @param string $to
     * @return array $data
     */
    function getSupplierPurchase($from='',$to='') {
        if ($from && $to) {
            $this->db->where("supplier_purchase.create_date between '{$from}' and '{$to}'");
        }
        $data = $this->db
                ->select("
                    supplier_purchase.id_supplier_purchase,supplier_purchase.purchase_invoice,supplier_purchase.shipping_date,supplier_purchase.total_price,
                    supplier.supplier,
                    supplier_purchase_product.total_qty,
                    supplier_purchase_product.total_product_type,
                    coalesce({$this->db->dbprefix('supplier_purchase_retur')}.total_retur,0) as total_retur,
                    supplier_purchase_payment.total_paid
                ",FALSE)
                ->join(
                    "( 
                        select 
                            id_supplier_purchase,sum(purchase_qty) as total_qty,count(id_product) as total_product_type
                        from {$this->db->dbprefix('supplier_purchase_product')} 
                        group by id_supplier_purchase 
                    ) {$this->db->dbprefix('supplier_purchase_product')}",
                    'supplier_purchase_product.id_supplier_purchase=supplier_purchase.id_supplier_purchase',
                    'left'
                )
                ->join(
                    "( 
                        select 
                            id_supplier_purchase,sum(payment_total) as total_paid
                        from {$this->db->dbprefix('supplier_purchase_payment')} 
                        group by id_supplier_purchase 
                    ) {$this->db->dbprefix('supplier_purchase_payment')}",
                    'supplier_purchase_payment.id_supplier_purchase=supplier_purchase.id_supplier_purchase',
                    'left'
                )
                ->join(
                    "( 
                        select 
                            id_supplier_purchase,sum(retur_qty) as total_retur
                        from {$this->db->dbprefix('supplier_purchase_retur')} 
                        group by id_supplier_purchase 
                    ) {$this->db->dbprefix('supplier_purchase_retur')}",
                    'supplier_purchase_retur.id_supplier_purchase=supplier_purchase.id_supplier_purchase',
                    'left'
                )
                ->join('supplier','supplier.id_supplier=supplier_purchase.id_supplier','left')
                ->where('supplier_purchase.is_delete',0)
                ->group_by('supplier_purchase.id_supplier_purchase')
                ->order_by('supplier_purchase.id_supplier_purchase','desc')
                ->get('supplier_purchase')
                ->result_array();
        return $data;
    }
    
    /**
     * report store sales
     * @param string $from
     * @param String $to
     * @return array $data
     */
    function getStoreSales($from='',$to='') {
        if ($from && $to) {
            $this->db->where("sales.create_date between '{$from}' and '{$to}'");
        }
        $data = $this->db
                ->select("
                    sales.id_store,
                    sum({$this->db->dbprefix('sales')}.total_price) as total_price,
                    sum({$this->db->dbprefix('sales')}.total_price_retur) as total_retur,
                    sales_payment.total_payment,
                    store.store,store.store_address
                ")
                ->join('store','store.id_store=sales.id_store','left')
                ->join(
                    "( 
                        select 
                            id_store,sum(payment_total) as total_payment
                        from {$this->db->dbprefix('sales_payment')} 
                        group by id_store
                    ) {$this->db->dbprefix('sales_payment')}",
                    'sales_payment.id_store=sales.id_store',
                    'left'
                )
                ->where('sales.is_delete',0)
                ->order_by('store.store')
                ->group_by('sales.id_store')
                ->get('sales')
                ->result_array();
                    
        return $data;
    }
    
    /**
     * get all info of store sales detail
     * @param int $id_store
     * @return array $data
     */
    function getStoreSalesDetail($id_store) {
        $data = $this->db
                ->where('id_store',$id_store)
                ->where('is_delete',0)
                ->limit(1)
                ->get('store')
                ->row_array();
        if ($data) {
            // store transaction
            $data['transactions'] = $this->db
                    ->select("
                        sales.id_sales,sales.sales_invoice,sales.shipping_date,sales.driver,sales.sales_note,sales.payment_status,sales.total_price,sales.total_price_retur,
                        coalesce(sum({$this->db->dbprefix('sales_payment')}.payment_total),0) as total_payment
                    ",FALSE)
                    ->join('sales_payment','sales_payment.id_sales=sales.id_sales','left')
                    ->where('sales.is_delete',0)
                    ->where('sales.id_store',$data['id_store'])
                    ->order_by('sales.id_sales','desc')
                    ->group_by('sales.id_sales')
                    ->get('sales')
                    ->result_array();
            if ($data['transactions']) {
                $i=0;
                foreach ($data['transactions'] as $transaction) {
                    // production
                    $data['transactions'][$i]['productions'] = $this->db
                            ->select("
                                sales_production.*,
                                division.division,
                                item.item_code,item.item_name,
                                item_category.item_category
                            ")
                            ->join('division','division.id_division=sales_production.id_division','left')
                            ->join('item','item.id_item=sales_production.id_item','left')
                            ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                            ->where('sales_production.id_sales',$transaction['id_sales'])
                            ->get('sales_production')
                            ->result_array();
                    
                    // retur
                    $data['transactions'][$i]['retur'] = $this->db
                            ->select("
                                sales_retur.*,
                                division.division,
                                item.item_code,item.item_name,
                                item_category.item_category
                            ")
                            ->join('division','division.id_division=sales_retur.id_division','left')
                            ->join('item','item.id_item=sales_retur.id_item','left')
                            ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                            ->where('sales_retur.id_sales',$transaction['id_sales'])
                            ->get('sales_retur')
                            ->result_array();
                    
                    $i++;
                }
            }
        }
        
        return $data;
    }
    
    /**
     * get all sales item
     * @param string $from
     * @param string $to
     * @return array $data
     */
    function getSalesItem($from='',$to='') {
        if ($from && $to) {
            $this->db->where("sales.create_date between '{$from}' and '{$to}'");
        }
        $data = $this->db
                ->select("
                    item.id_item,item.item_code,item.item_name,item.item_stock,item.id_division,
                    item_category.item_category,division.division,
                    sum({$this->db->dbprefix('sales_production')}.sales_qty) as total_sales_qty,
                    sum({$this->db->dbprefix('sales_production')}.sales_price) as total_sales_price,
                    coalesce(sum({$this->db->dbprefix('sales_retur')}.total_retur_qty),0) as total_retur_qty,
                    coalesce(sum({$this->db->dbprefix('sales_retur')}.total_retur_price),0) as total_retur_price
                ",FALSE)
                ->join('item','item.id_item=sales_production.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->join('division','division.id_division=sales_production.id_division','left')
                ->join(
                    "(
                        select 
                            id_item,sum(retur_price) as total_retur_price,
                            sum(retur_qty) as total_retur_qty
                        from {$this->db->dbprefix('sales_retur')}
                        group by id_item
                    ) {$this->db->dbprefix('sales_retur')}",
                    'sales_retur.id_item=sales_production.id_item',
                    'left'
                )
                ->group_by('sales_production.id_item')
                ->get('sales_production')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get sales product detail
     * @param int $id_product
     * @return array $data
     */
    function getSalesItemDetail($id_product) {
        $data = $this->db
                ->select('id_product,product_code,product_name,product_unit,product_note,buy_price,sell_price,product_stock')
                ->where('id_product',$id_product)
                ->limit(1)
                ->get('product')
                ->row_array();
        if ($data) {
            $sales = $this->db
                    ->select("
                        division_purchase.purchase_invoice,
                        division_purchase_product.purchase_qty,division_purchase_product.purchase_price,division_purchase_product.purchase_buy,
                        sum({$this->db->dbprefix('division_purchase_product')}.purchase_qty) as total_sales_qty,
                        sum({$this->db->dbprefix('division_purchase_retur')}.total_retur) as total_retur_qty,
                        sum({$this->db->dbprefix('division_purchase_product')}.purchase_price * {$this->db->dbprefix('division_purchase_product')}.purchase_qty) as total_sales_price,
                        division.division
                    ")
                    ->join('division_purchase','division_purchase.id_division_purchase=division_purchase_product.id_division_purchase','left')
                    ->join(
                        "( 
                            select 
                                id_division_purchase,sum(retur_qty) as total_retur
                            from {$this->db->dbprefix('division_purchase_retur')} 
                            where id_product='$id_product'
                            group by id_division_purchase
                        ) {$this->db->dbprefix('division_purchase_retur')}",
                        'division_purchase_retur.id_division_purchase=division_purchase.id_division_purchase',
                        'left'
                    )
                    ->join('division','division.id_division=division_purchase.id_division','left')
                    ->where('division_purchase_product.id_product',$data['id_product'])
                    ->group_by('division_purchase_product.id_division_purchase')
                    ->order_by('division_purchase_product.id_division_purchase')
                    ->get('division_purchase_product')
                    ->result_array();
            $data['sales'] = $sales;
        }
        return $data;
    }
    
    function getAllGiro($from='',$to='') {
        if ($from && $to) {
            $this->db->where("sales.create_date between '{$from}' and '{$to}'");
        }
        $data = $this->db
                ->select("
                    item.id_item,item.item_code,item.item_name,item.item_stock,item.id_division,
                    item_category.item_category,division.division,
                    sum({$this->db->dbprefix('sales_production')}.sales_qty) as total_sales_qty,
                    sum({$this->db->dbprefix('sales_production')}.sales_price) as total_sales_price,
                    coalesce(sum({$this->db->dbprefix('sales_retur')}.total_retur_qty),0) as total_retur_qty,
                    coalesce(sum({$this->db->dbprefix('sales_retur')}.total_retur_price),0) as total_retur_price
                ",FALSE)
                ->join('item','item.id_item=sales_production.id_item','left')
                ->join('item_category','item_category.id_item_category=item.id_item_category','left')
                ->join('division','division.id_division=sales_production.id_division','left')
                ->join(
                    "(
                        select 
                            id_item,sum(retur_price) as total_retur_price,
                            sum(retur_qty) as total_retur_qty
                        from {$this->db->dbprefix('sales_retur')}
                        group by id_item
                    ) {$this->db->dbprefix('sales_retur')}",
                    'sales_retur.id_item=sales_production.id_item',
                    'left'
                )
                ->group_by('sales_production.id_item')
                ->get('sales_production')
                ->result_array();
        
        return $data;
    }
}

/* End of file report_model.php */
/* Location: ./application/model/report_model.php */

