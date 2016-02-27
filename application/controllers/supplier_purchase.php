<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Supplier Purchase Class
 * this class is for supplier purchase management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Supplier Purchase Controller
 */
class Supplier_purchase extends CI_Controller
{
    //private $error = array();
    private $error = '';
    private $controller = 'supplier_purchase';
    
    /**
     * Index Supplier for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url($this->controller.'/add');
        $this->data['export_excel_url'] = site_url($this->controller.'/export_excel');
        $this->data['list_data'] = site_url($this->controller.'/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_invoice'] = "a.purchase_invoice";
        $alias['search_supplier'] = "b.supplier";
        $alias['search_pic'] = "b.supplier_pic";
        $alias['search_address'] = "b.supplier_address";
        $query = "
            select 
                a.id_supplier_purchase as id, 
                a.id_supplier_purchase as idx, 
                b.*,
                a.purchase_invoice,
                a.shipping_date,
                a.total_price,
                a.payment_status,
                a.create_date as date_created
            from " . $this->db->dbprefix('supplier_purchase') . " a
            left join " . $this->db->dbprefix('supplier') . " b on b.id_supplier=a.id_supplier
            where a.is_delete=0";
        $this->data = query_grid($query, $alias);
        //echo $this->db->last_query();
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Supplier_purchase_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['search_supplier_url'] = site_url($this->controller.'/searchq');
        $this->data['page_title'] = 'Tambah Pembelian Supplier';
        $this->data['product_info_url'] = site_url($this->controller.'/ajax_product_info');
        $this->data['suppliers'] = $this->Supplier_purchase_model->getAllSupplier();
        $this->data['products'] = $this->Supplier_purchase_model->getProducts();
        $this->data['product_count'] = 0;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $post_product = false;
                if (isset($post['post_product']) && count($post['post_product'])>0) {
                    $post_product = $post['post_product'];
                    unset($post['post_product']);
                }
                $post['id_auth_user'] = adm_sess_userid();
                $last_id = $this->Supplier_purchase_model->InsertRecord($post);
                if ($last_id) {
                    //$invoice = 'INV'.$post['id_supplier'].$last_id.date('Ymd');
                    $total_price = 0;
                    //$total_qty = 0;
                    //$product_price = 0;
                    if ($post_product) {
                        $product = array();
                        $prod = array();
                        foreach ($post_product as $prow) {
                            $prod[$prow['id_product']]['id_product'] = $prow['id_product'];
                            $prod[$prow['id_product']]['purchase_qty'] = $prow['purchase_qty'];
                            $prod[$prow['id_product']]['purchase_price'] = $prow['purchase_price'];
                        }
                        $products = array_values($prod);
                        foreach ($products as $prd) {
                            $product['id_supplier_purchase'] = $last_id;
                            $product['id_supplier'] = $post['id_supplier'];
                            $product['id_product'] = $prd['id_product'];
                            $product['purchase_qty'] = $prd['purchase_qty'];
                            $product['purchase_price'] = $prd['purchase_price'];
                            $total_price += ($prd['purchase_price']*$prd['purchase_qty']);
                            
                            // insert product to stock
                            $this->Supplier_purchase_model->InsertStock($product);
                            
                            // update product record
                            $this->Supplier_purchase_model->UpdateProductStock($prd['id_product'],$prd['purchase_qty'],$prd['purchase_price']);
                        }
                    }
                    // update transaction
                    $update = array(
                        'total_price'=>$total_price,
                        //'purchase_invoice'=>$invoice,
                    );
                    $this->Supplier_purchase_model->UpdateRecord($last_id,$update);
                    
                    // cek if supplier have credit
                    $credit = $this->Supplier_purchase_model->getSupplierCredit($post['id_supplier']);
                    if ($credit) {
                        // insert to payment
                        $payment['id_supplier_purchase'] = $last_id;
                        $payment['purchase_invoice'] = $post['purchase_invoice'];
                        $payment['payment_type'] = 1;
                        $payment['payment_note'] = 'Diambil dari piutang supplier.';
                        $payment['payment_date'] = date('Y-m-d');
                        $payment['payment_total'] = ($total_price <= $credit) ? $total_price : $credit;
                        $this->Supplier_purchase_model->InsertPayment($payment);
                        
                        // update all credit to paid and set new if there's credit is more than total price
                        $c_status['credit_status'] = 1;
                        $this->Supplier_purchase_model->UpdateSupplierCredit($post['id_supplier'],$c_status);
                        // set as paid or add more credit if credit is more than total price
                        if ($total_price <= $credit) {
                            $updt_payment['payment_status'] = 2;
                        } else {
                            $updt_payment['payment_status'] = 1;
                        }
                        $this->Supplier_purchase_model->UpdateRecord($last_id,$updt_payment);
                        // set new credit if credit is more than total price
                        if ($total_price < $credit) {
                            $credits['id_supplier_purchase'] = $last_id;
                            $credits['id_supplier'] = $post['id_supplier'];
                            $credits['purchase_invoice'] = $post['purchase_invoice'];
                            $credits['credit_price'] = $credit - $total_price;
                            $this->Supplier_purchase_model->InsertSupplierCredit($credits);
                        }
                    }
                    
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect($this->controller);
                
            }
            if (isset($post['post_product']) && count($post['post_product'])>0) {
                $this->data['product_count'] = count($post['post_product']);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * edit new record
     */
    public function detail($id=0)
    {
        $this->load->model('Supplier_purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['print_url'] = site_url($this->controller.'/printit/'.$id);
        $this->data['page_title'] = 'Detail Pembelian Supplier';
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Supplier_purchase_model->getSupplierPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['record'] = $detail;
    }
    
    /**
     * retur page
     * @param int $id
     */
    public function retur($id=0) {
        $this->load->model('Supplier_purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['form_action'] = site_url($this->controller.'/retur/'.$id);
        $this->data['page_title'] = 'Form Retur Pembelian Master Produk';
        $this->data['product_count'] = 0;
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Supplier_purchase_model->getSupplierPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['record'] = $detail;
        $this->data['products'] = $this->Supplier_purchase_model->getSupplierPurchaseProduct($detail['id_supplier_purchase']);
        $this->data['retur'] = $this->Supplier_purchase_model->getPurchaseRetur($detail['id_supplier_purchase']);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateReturForm($id)) {
                $prd = array();
                $total_price_retur=0;
                foreach ($post['post_product'] as $product) {
                    $prd[$product['id_product']]['id_product'] = $product['id_product'];
                    if (isset($prd[$product['id_product']]['qty'])) {
                        $prd[$product['id_product']]['qty'] += $product['qty'];
                    } else {
                        $prd[$product['id_product']]['qty'] = $product['qty'];
                    }
                }
                $products = array_values($prd);
                foreach ($products as $row) {
                    $price_product = $this->Supplier_purchase_model->MaxPricePurchaseProduct($detail['id_supplier_purchase'],$row['id_product']);
                    $product_insert['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                    $product_insert['id_supplier'] = $detail['id_supplier'];
                    $product_insert['id_product'] = $row['id_product'];
                    $product_insert['retur_qty'] = $row['qty'];
                    $product_insert['retur_price'] = $price_product;
                    
                    // insert to db retur
                    $this->Supplier_purchase_model->InsertPurchaseRetur($product_insert);
                    
                    // update stock in master product
                    $this->Supplier_purchase_model->UpdateProductStockRetur($row['id_product'],$row['qty']);
                    
                    $total_price_retur += $price_product*$row['qty'];
                }
                    
                // input to credit
                $credit['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                $credit['id_supplier'] = $detail['id_supplier'];
                $credit['purchase_invoice'] = $detail['purchase_invoice'];
                $credit['credit_price'] = $total_price_retur;
                $credit['credit_note'] = 'Pengurangan dari retur barang.';
                $this->Supplier_purchase_model->InsertSupplierCredit($credit);
                
                // update transaction
                $transaction['total_price_retur'] = $total_price_retur+$detail['total_price_retur'];
                $this->Supplier_purchase_model->UpdateRecord($detail['id_supplier_purchase'],$transaction);
                
                $this->session->set_flashdata('success_msg','Data berhasil diretur.');
                redirect($this->controller);
            }
            if (isset($post['post_product']) && count($post['post_product'])>0) {
                $this->data['product_count'] = count($post['post_product']);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * payment
     */
    public function payment($id=0)
    {
        $id = (int)$id;
        if (!$id) {
            redirect('supplier_purchase');
        }
        $this->data['payment_count'] = 0;
        $this->load->model('Supplier_purchase_model');
        $this->data['form_action'] = site_url($this->controller.'/payment/'.$id);
        $this->data['giro_info_url'] = site_url($this->controller.'/ajax_giro_info');
        $this->data['cancel_url'] = site_url('supplier_purchase');
        $this->data['page_title'] = 'Pembayaran Invoice Pembelian Barang';
        $detail = $this->Supplier_purchase_model->getSupplierPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['giros'] = $this->Supplier_purchase_model->getUnUsedGiro();
        $this->data['supplier_credit'] = $this->Supplier_purchase_model->getSupplierCredit($detail['id_supplier']);
        $this->data['total_paid'] = $this->Supplier_purchase_model->getTotalPayment($detail['id_supplier_purchase']);
        $this->data['record'] = $detail;
        $this->data['payments'] = $this->Supplier_purchase_model->getPaymentByTransactionID($detail['id_supplier_purchase']);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validatePaymentForm($detail['id_supplier_purchase'])) {
                $post['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                $post['purchase_invoice'] = $detail['purchase_invoice'];
                $post['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                $post_payment = $post['post_payment'];
                $payment = array();
                $pay = array();
                foreach ($post_payment as $prow) {
                    $pay[$prow['type']]['type'] = $prow['type'];
                    $pay[$prow['type']]['price'] = $prow['price'];
                }
                $total_price=0;
                $payments = array_values($pay);
                foreach ($payments as $pmt) {
                    if ($pmt['type'] == 0) {
                        $payment['payment_type'] = 1;
                    } else {
                        $payment['payment_type'] = 2;
                    }
                    $payment['id_giro'] = $pmt['type'];
                    $payment['payment_total'] = $pmt['price'];
                    $payment['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                    $payment['purchase_invoice'] = $detail['purchase_invoice'];
                    $payment['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                    $payment['payment_date'] = $post['payment_date'];
                    $total_price += ($pmt['price']);

                    // insert product to stock
                    $this->Supplier_purchase_model->InsertPayment($payment);
                    
                    // set giro as used if payment using giro
                    if ($pmt['type'] > 0) {
                        $giro['giro_status'] = 1;
                        $this->Supplier_purchase_model->UpdateGiro($payment['id_giro'],$giro);
                    }
                }
                $total_paid = ($total_price+$this->data['total_paid']);
                if ($total_paid >= $detail['total_price']) {
                    $update['payment_status'] = 2;
                } else {
                    $update['payment_status'] = 1;
                }
                $this->Supplier_purchase_model->UpdateRecord($detail['id_supplier_purchase'],$update);
                if ($total_paid > $detail['total_price']) {
                    $credit['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                    $credit['id_supplier'] = $detail['id_supplier'];
                    $credit['purchase_invoice'] = $detail['purchase_invoice'];
                    $credit['credit_price'] = ($total_paid - $detail['total_price']);
                    $this->Supplier_purchase_model->InsertSupplierCredit($credit);
                }
                $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                redirect($this->controller);
            }
            if (isset($post['post_payment']) && count($post['post_payment'])>0) {
                $this->data['post_payment'] = count($post['post_payment']);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * print page
     * @param int $id
     */
    public function printit($id=0) {
        $this->layout = 'layout/print';
        $this->load->model('Supplier_purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['page_title'] = 'Invoice Pembelian Supplier';
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Supplier_purchase_model->getSupplierPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['record'] = $detail;
    }
    
    /**
     * search supplier
     */
    public function searchq() {
        $this->layout='none';
        $json = array();
        if ($this->input->get('q') || ($this->input->get('id') && ctype_digit($this->input->get('id')))) {
            $get = $this->input->get();
            $this->load->model('Supplier_purchase_model');
            if (utf8_strlen($get['q'])>=3) {
                $data = $this->Supplier_purchase_model->SearchSupplier($get['q'],$get['page']);
                if ($data) {
                    $json['records'] = $data;
                }
            }
            if (isset($get['id'])) {
                $data = $this->Supplier_purchase_model->getSupplierByID($get['id']);
                if ($data) {
                    $json['id'] = $data['id_supplier'];
                    $json['text'] = $data['supplier'].' - '.$data['supplier_address'];
                }
            }
        }
        echo json_encode($json);
    }
    
    /**
     * delete record
     */
    public function delete() {
        $this->load->model('Supplier_purchase_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Supplier_purchase_model->getSupplierPurchase($id);
                    if ($detail) {
                        $this->Supplier_purchase_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect($this->controller);
        }
    }
    
    /**
     * request ajax product info
     */
    public function ajax_product_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();
            $this->load->model('Supplier_purchase_model');
            $json = array();
            if (!isset($post['product_id'])) {
                $json['error'] = alert_box('Mohon pilih Produk.','error');
            }
            $data = $this->Supplier_purchase_model->getProductInfo($post['product_id']);
            if (!$data) {
                $json['error'] = alert_box('Produk tidak ada. Mohon pilih Produk yang lain<br/>.');
            }
            if (!$json) {
                $json['value'] = $data;
            }
            echo json_encode($json);
        }
    }
    
    /**
     * request ajax giro info
     */
    public function ajax_giro_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();$this->load->model('Supplier_purchase_model');
            $json = array();
            if (!isset($post['giro_id'])) {
                $json['error'] = alert_box('Mohon pilih Giro.','error');
            }
            $data = $this->Supplier_purchase_model->getGiroValueByID($post['giro_id']);
            if (!$data || $data['giro_status'] == 1) {
                $json['error'] = alert_box('Giro tidak ada atau sudah terpakai. Mohon pilih Giro yang lain<br/>.');
            }
            if (!$json) {
                $json['value'] = $data;
            }
            echo json_encode($json);
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Supplier_purchase_model');
        $post = $this->input->post();
        $err = '';
        if ($post['purchase_invoice'] == '') {
            $err .= 'Mohon isi No Faktur.<br/>';
        }
        if ($post['id_supplier'] == '') {
            $err .= 'Mohon pilih Supplier.<br/>';
        }
        
        if (!isset($post['post_product']) || count($post['post_product'])==0) {
            $err .= 'Mohon tambah barang.<br/>';
        } else {
            foreach ($post['post_product'] as $product) {
                if ($product['id_product'] == '') {
                    $err .= 'Mohon isi Produk.';
                } else {
                    if ($product['id_product']!='' && ($product['purchase_qty'] < 1 && $product['purchase_price'] < 1)) {
                        $err .= 'Mohon isi Produk dengan lengkap.';
                    }
                }
            }
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * validate payment form
     * @param int $id
     * @return boolean
     */
    private function validatePaymentForm($id=0) {
        $id = (int)$id;
        $this->load->model('Supplier_purchase_model');
        $detail = $this->Supplier_purchase_model->getSupplierPurchase($id);
        $total_price = $detail['total_price'];
        $post = $this->input->post();
        $err = '';
        /*
        if (!isset($post['payment_type']) && $post['payment_type'] == '0') {
            $err .= 'Mohon pilih Tipe Pembayaran.<br/>';
        } else {
            if ($post['payment_type'] == 2) {
                if ($post['id_giro'] == '') {
                    $err .= 'Mohon pilih Giro untuk pembayaran.<br/>';
                }
            } else {
                if ($post['payment_total'] == '') {
                    $err .= 'Mohon isi Jumlah Pembayaran.<br/>';
                } else {
                    $post['payment_total'] = (int)$post['payment_total'];
                    if ($total_price < $post['payment_total']) {
                        $err .= 'Mohon isi Jumlah yang lebih kecil dari Total Harga.<br/>';
                    } else {
                        $total_paid = $this->Supplier_purchase_model->getTotalPayment($detail['id_supplier_purchase']);
                        if ( ($total_paid+$post['payment_total']) > $total_price) {
                            $err .= 'Mohon isi Jumlah yang lebih kecil dari Total Harga.<br/>';
                        }
                    }
                }
            }
        }
         * 
         */
        $total_payment = 0;
        if (!isset($post['post_payment'])) {
            $err .= 'Mohon isi Pembayaran.<br/>';
        } else {
            foreach ($post['post_payment'] as $payment) {
                if ($payment['type'] == '') {
                    $err .= 'Mohon isi Pembayaran.';
                } else {
                    if ($payment['type'] !='' && $payment['price'] == '') {
                        $err .= 'Mohon isi Pembayaran dengan lengkap.';
                    }
                }
            }
        }
        if ($post['payment_date'] == '') {
            $err .= 'Mohon isi Tanggal Pembayaran.<br/>';
        }
        
        $post_image = $_FILES;
        if (!empty($post_image['payment_image']['tmp_name'])) {
            $check_picture = validatePicture('payment_image');
            if (!empty($check_picture)) {
                $err .= $check_picture.'<br/>';
            }
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * validate retur form
     * @param int $id
     * @return boolean
     */
    private function validateReturForm($id=0) {
        $id = (int)$id;
        $this->load->model('Supplier_purchase_model');
        $detail = $this->Supplier_purchase_model->getSupplierPurchase($id);
        $post = $this->input->post();
        $err = '';
        if (!isset($post['post_product'])) {
            $err .= 'Mohon isi Barang yang akan di retur.<br/>';
        } else {
            $prd = array();
            foreach ($post['post_product'] as $product) {
                $prd[$product['id_product']]['id_product'] = $product['id_product'];
                if (isset($prd[$product['id_product']]['qty'])) {
                    $prd[$product['id_product']]['qty'] += $product['qty'];
                } else {
                    $prd[$product['id_product']]['qty'] = $product['qty'];
                }
            }
            $products = array_values($prd);
            foreach ($products as $row) {
                $product_qty = $this->Supplier_purchase_model->getSupplierPurchaseProductReturQty($detail['id_supplier_purchase'],$row['id_product']);
                if ($row['qty']>$product_qty) {
                    $err .= 'Mohon isi QTY dengan angka yang lebih kecil.<br/>';
                    break;
                }
            }
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}

/* End of file supplier_purchase.php */
/* Location: ./application/controllers/supplier_purchase.php */