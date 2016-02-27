<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Division Purchase Class
 * this class is for division purchase management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Division Purchase Controller
 */
class Division_purchase extends CI_Controller
{
    //private $error = array();
    private $error = '';
    private $controller = 'division_purchase';
    
    /**
     * Index Division for this controller.
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
        $inc_where = '';
        if (is_superadmin()) {
            $alias['search_division'] = "b.division";
            $alias['search_address'] = "b.division_address";
        }
        $alias['search_invoice'] = "a.purchase_invoice";
        if (!is_superadmin()) {
            $inc_where = " and b.id_division='".getSessionAdmin('admin_id_division')."'";
        }
        $query = "
            select 
                a.id_division_purchase as id, 
                a.id_division_purchase as idx, 
                b.*,
                a.purchase_invoice,
                a.shipping_date,
                a.total_price,
                a.payment_status,
                a.create_date as date_created
            from " . $this->db->dbprefix('division_purchase') . " a
            left join " . $this->db->dbprefix('division') . " b on b.id_division=a.id_division
            where a.is_delete=0".$inc_where;
        $this->data = query_grid($query, $alias);
        echo $this->db->last_query();
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Division_purchase_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['product_info_url'] = site_url($this->controller.'/ajax_product_info');
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['search_division_url'] = site_url($this->controller.'/searchq');
        $this->data['page_title'] = 'Tambah Pembelian Divisi';
        if (is_superadmin()) {
            $this->data['divisions'] = $this->Division_purchase_model->getAllDivision();
        }
        $this->data['products'] = $this->Division_purchase_model->getProducts();
        $this->data['product_count'] = 0;
        $this->data['credit_count'] = 0;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $post_product = false;
                $post_credit = false;
                if (isset($post['post_product']) && count($post['post_product'])>0) {
                    $post_product = $post['post_product'];
                    unset($post['post_product']);
                }
                if (isset($post['post_credit']) && count($post['post_credit'])>0) {
                    $post_credit = $post['post_credit'];
                    unset($post['post_credit']);
                }
                if (is_superadmin()) {
                    $post['id_division'] = $post['id_division'];
                } else {
                    $post['id_division'] = getSessionAdmin('id_division');
                }
                $last_id = $this->Division_purchase_model->InsertRecord($post);
                if ($last_id) {
                    $invoice = 'INV'.$post['id_division'].'/'.$last_id.'/'.date('Ymd');
                    $total_price = 0;
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
                            $buy_price = $this->Division_purchase_model->getProductBuyPrice($prd['id_product']);
                            $product['id_division_purchase'] = $last_id;
                            $product['id_division'] = $post['id_division'];
                            $product['id_product'] = $prd['id_product'];
                            $product['purchase_qty'] = $prd['purchase_qty'];
                            $product['purchase_price'] = $prd['purchase_price'];
                            $product['purchase_buy'] = $buy_price;
                            $total_price += ($prd['purchase_price']*$prd['purchase_qty']);
                            
                            // insert product to division stock
                            $this->Division_purchase_model->InsertDivisionProduct($product);
                            
                            // update product record
                            $this->Division_purchase_model->UpdateProductStock($prd['id_product'],$prd['purchase_qty'],$prd['purchase_price'],$post['id_division']);
                        }
                    }
                    if ($post_credit) {
                        foreach ($post_credit as $crd) {
                            $credit['id_division_purchase'] = $last_id;
                            $credit['id_division'] = $post['id_division'];
                            $credit['purchase_invoice'] = $invoice;
                            $credit['credit_price'] = $crd['price'];
                            
                            $total_price += $crd['price'];

                            // insert product to division stock
                            $this->Division_purchase_model->InsertDivisionCredit($credit);
                        }
                    }
                    // update transaction
                    $update = array(
                        'total_price'=>$total_price,
                        'purchase_invoice'=>$invoice,
                    );
                    $this->Division_purchase_model->UpdateRecord($last_id,$update);
                    
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect($this->controller);
            }
            if (isset($post['post_product']) && count($post['post_product'])>0) {
                $this->data['product_count'] = count($post['post_product']);
            }
            if (isset($post['post_credit']) && count($post['post_credit'])>0) {
                $this->data['post_credit'] = count($post['post_credit']);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * request ajax product info
     */
    public function ajax_product_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();
            $this->load->model('Division_purchase_model');
            $json = array();
            if (!isset($post['product_id'])) {
                $json['error'] = alert_box('Mohon pilih Produk.','error');
            }
            $data = $this->Division_purchase_model->getProductInfo($post['product_id']);
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
     * edit new record
     */
    public function detail($id=0)
    {
        $this->load->model('Division_purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['print_url'] = site_url($this->controller.'/printit/'.$id);
        $this->data['page_title'] = 'Detail Pembelian Divisi';
        $id = (int)$id;
        if (!$id) {
            redirect('division_purchase');
        }
        $detail = $this->Division_purchase_model->getDivisionPurchase($id);
        if (!$detail) {
            redirect('division_purchase');
        }
        if (!is_superadmin()) {
            $id_division = getSessionAdmin('admin_id_division');
            if ($id_division != $detail['id_division']) {
                redirect('division_purchase');
            }
        }
        $this->data['record'] = $detail;
    }
    
    /**
     * print page
     * @param int $id
     */
    public function printit($id=0) {
        $this->layout = 'layout/print';
        $this->load->model('Division_purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['page_title'] = 'Invoice Penjualan Master Produk';
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Division_purchase_model->getDivisionPurchase($id);
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
        $this->load->model('Division_purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['form_action'] = site_url($this->controller.'/retur/'.$id);
        $this->data['page_title'] = 'Form Retur Penjualan Master Produk';
        $this->data['product_count'] = 0;
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Division_purchase_model->getDivisionPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['record'] = $detail;
        $this->data['products'] = $this->Division_purchase_model->getDivisionPurchaseProduct($detail['id_division_purchase']);
        $this->data['retur'] = $this->Division_purchase_model->getPurchaseRetur($detail['id_division_purchase']);
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
                    $price_product = $this->Division_purchase_model->MaxPricePurchaseProduct($detail['id_division_purchase'],$row['id_product']);
                    $product_insert['id_division_purchase'] = $detail['id_division_purchase'];
                    $product_insert['id_division'] = $detail['id_division'];
                    $product_insert['id_product'] = $row['id_product'];
                    $product_insert['retur_qty'] = $row['qty'];
                    $product_insert['retur_price'] = $price_product;
                    
                    // insert to db retur
                    $this->Division_purchase_model->InsertPurchaseRetur($product_insert);
                    
                    // update stock in master product
                    $this->Division_purchase_model->UpdateProductStockRetur($row['id_product'],$detail['id_division'],$row['qty']);
                    
                    $total_price_retur += $price_product*$row['qty'];
                }
                
                // input to payment
                $payment['id_division_purchase'] = $detail['id_division_purchase'];
                $payment['purchase_invoice'] = $detail['purchase_invoice'];
                $payment['payment_type'] = 1;
                $payment['payment_note'] = 'Pengurangan dari retur barang.';
                $payment['payment_date'] = date('Y-m-d');
                $payment['payment_total'] = $total_price_retur;
                $this->Division_purchase_model->InsertPayment($payment);
                
                // set transaction status if payment is paid
                $total_paid = $this->Division_purchase_model->getTotalPayment($detail['id_division_purchase']);
                if ($detail['total_price'] <= $total_paid) {
                    // set status as paid
                    $transaction['payment_status'] = 2;
                } else {
                    $transaction['payment_status'] = 1;
                }
                
                // update transaction
                $transaction['total_price_retur'] = $total_price_retur+$detail['total_price_retur'];
                $this->Division_purchase_model->UpdateRecord($detail['id_division_purchase'],$transaction);
                
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
            redirect($this->controller);
        }
        $this->load->model('Division_purchase_model');
        $this->data['form_action'] = site_url($this->controller.'/payment/'.$id);
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['page_title'] = 'Pembayaran Invoice Penjualan Produk';
        $this->data['payment_count'] = 0;
        $detail = $this->Division_purchase_model->getDivisionPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['total_paid'] = $this->Division_purchase_model->getTotalPayment($detail['id_division_purchase']);
        $this->data['record'] = $detail;
        $this->data['payments'] = $this->Division_purchase_model->getPaymentByTransactionID($detail['id_division_purchase']);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validatePaymentForm($detail['id_division_purchase'])) {
                $post['id_division_purchase'] = $detail['id_division_purchase'];
                $post['purchase_invoice'] = $detail['purchase_invoice'];
                $post_payment = false;
                if (isset($post['post_payment']) && count($post['post_payment'])>0) {
                    $post_payment = $post['post_payment'];
                    unset($post['post_payment']);
                }
                $total_payment = 0;
                $payment = array();
                $post_image = $_FILES;
                $picture_db = '';
                if ($post_image['payment_image']['tmp_name']) {
                    $filename = 'proof_'.md5plus($id).'_'.date('YmdHis');
                    $picture_db = file_copy_to_folder($post_image['payment_image'], IMG_UPLOAD_DIR.'division_purchase/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'division_purchase/'.$picture_db, IMG_UPLOAD_DIR.'division_purchase/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'division_purchase/'.$picture_db, IMG_UPLOAD_DIR.'division_purchase/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                }
                foreach ($post_payment as $pay) {
                    if ($pay['type'] == 2) {
                        $giro['giro_code'] = $pay['giro_code'];
                        $giro['giro_date'] = $pay['giro_date'];
                        $giro['giro_bank'] = $pay['giro_bank'];
                        $giro['giro_price'] = $pay['price'];
                        $giro['giro_from'] = 1;
                        $giro['giro_invoice'] = $detail['sales_invoice'];

                        //insert giro to db
                        $payment['id_giro'] = $this->Division_purchase_model->InsertGiro($giro);
                    }
                    $payment['id_division_purchase'] = $id;
                    $payment['purchase_invoice'] = $detail['purchase_invoice'];
                    $payment['payment_type'] = $pay['type'];
                    $payment['payment_note'] = $post['payment_note'];
                    $payment['payment_date'] = $post['payment_date'];
                    $payment['payment_total'] = $pay['price'];
                    $payment['payment_image'] = $picture_db;
                    $total_payment += $pay['price'];

                    // insert payment
                    $this->Division_purchase_model->InsertPayment($payment);
                }
                $total_paid = ($total_payment+$this->data['total_paid']);
                if ($total_paid >= $detail['total_price']) {
                    $update['payment_status'] = 2;
                } else {
                    $update['payment_status'] = 1;
                }
                $this->Division_purchase_model->UpdateRecord($detail['id_division_purchase'],$update);
                $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                redirect($this->controller);
            }
            if (isset($post['post_payment']) && count($post['post_payment'])>0) {
                $this->data['payment_count'] = count($post['post_payment']);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * search division
     */
    public function ajax_get_product_info() {
        $this->layout='none';
        $json = array();
        if ($this->input->get('q') || ($this->input->get('id') && ctype_digit($this->input->get('id')))) {
            $get = $this->input->get();
            $this->load->model('Division_purchase_model');
            if (utf8_strlen($get['q'])>=3) {
                $data = $this->Division_purchase_model->SearchDivision($get['q'],$get['page']);
                if ($data) {
                    $json['records'] = $data;
                }
            }
            if (isset($get['id'])) {
                $data = $this->Division_purchase_model->getDivisionByID($get['id']);
                if ($data) {
                    $json['id'] = $data['id_division'];
                    $json['text'] = $data['division'].' - '.$data['division_address'];
                }
            }
        }
        echo json_encode($json);
    }
    
    /**
     * delete record
     */
    public function delete() {
        $this->load->model('Division_purchase_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Division_purchase_model->getDivisionPurchase($id);
                    if ($detail) {
                        $this->Division_purchase_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect('division_purchase');
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Division_purchase_model');
        $post = $this->input->post();
        $err = '';
        /*if ($post['purchase_invoice'] == '') {
            $err .= 'Mohon isi No Faktur.<br/>';
        }*/
        if (is_superadmin()) {
            if ($post['id_division'] == '') {
                $err .= 'Mohon pilih Divisi.<br/>';
            }
        }
        
        if (!isset($post['post_product']) || count($post['post_product'])==0) {
            $err .= 'Mohon tambah barang.<br/>';
        } else {
            $product = array();
            $prod = array();
            foreach ($post['post_product'] as $prow) {
                $prod[$prow['id_product']]['id_product'] = $prow['id_product'];
                $prod[$prow['id_product']]['purchase_qty'] = (int)$prow['purchase_qty'];
                $prod[$prow['id_product']]['purchase_price'] = (int)$prow['purchase_price'];
            }
            $products = array_values($prod);
            foreach ($products as $product) {
                if ($product['id_product'] == '') {
                    $err .= 'Mohon isi Produk.<br/>';
                    break;
                } else {
                    if ($product['id_product']!='' && ($product['purchase_qty'] < 1 && $product['purchase_price'] < 1)) {
                        $err .= 'Mohon isi Produk dengan benar/lengkap.<br/>';
                        break;
                    } else {
                        if ($this->Division_purchase_model->checkProductStock($product['id_product']) < $product['purchase_qty']) {
                            $err .= 'Mohon isi qty yang lebih kecil.<br/>';
                            break;
                        }
                    }
                }
            }
        }
        
        if (isset($post['post_credit']) && count($post['post_credit'])>0) {
            foreach ($post['post_credit'] as $credit) {
                if ($credit['price'] == '') {
                    $err .= 'Mohon isi jumlah hutang.';
                    break;
                } else {
                    if (!ctype_digit($credit['price'])) {
                        $err .= 'Mohon isi jumlah hutang dengan angka saja.';
                        break;
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
        $this->load->model('Division_purchase_model');
        $detail = $this->Division_purchase_model->getDivisionPurchase($id);
        $total_price = $detail['total_price'];
        $post = $this->input->post();
        $err = '';
        if (!isset($post['post_payment'])) {
            $err .= 'Mohon isi Pembayaran.<br/>';
        } else {
            if (count($post['post_payment'])==0) {
                $err .= 'Mohon isi Pembayaran.<br/>';
            } else {
                foreach ($post['post_payment'] as $payment) {
                    if ($payment['type'] == '' || $payment['price'] == '') {
                        $err .= 'Mohon isi detail Pembayaran.<br/>';
                        break;
                    } else {
                        if ($payment['type'] == 2 && ($payment['giro_code'] == '' || $payment['giro_bank'] == '' || $payment['giro_date'] == '')) {
                            $err .= 'Mohon isi detail Pembayaran.<br/>';
                            break;
                        }
                    }
                }
            }
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
        $this->load->model('Division_purchase_model');
        $detail = $this->Division_purchase_model->getDivisionPurchase($id);
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
                $product_qty = $this->Division_purchase_model->getDivisionPurchaseProductReturQty($detail['id_division_purchase'],$row['id_product']);
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

/* End of file division_purchase.php */
/* Location: ./application/controllers/division_purchase.php */