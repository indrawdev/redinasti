<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Purchase Class
 * this class is for purchase management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Purchase Controller
 */
class Purchase extends CI_Controller
{
    //private $error = array();
    private $error = '';
    private $controller = 'purchase';
    
    /**
     * Index Purchase for this controller.
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
        $alias['search_division'] = "b.division";
        $alias['search_pic'] = "b.division_pic";
        $alias['search_address'] = "b.division_address";
        $query = "
            select 
                a.id_purchase as id, 
                a.id_purchase as idx, 
                b.*,
                a.purchase_invoice,
                a.shipping_date,
                a.total_price,
                a.payment_status,
                a.create_date as date_created
            from " . $this->db->dbprefix('purchase') . " a
            left join " . $this->db->dbprefix('division') . " b on b.id_division=a.id_division
            where a.is_delete=0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Purchase_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['page_title'] = 'Tambah Pembelian Barang Produksi';
        $this->data['divisions'] = $this->Purchase_model->getAllDivision();
        $this->data['getitem_url'] = site_url($this->controller.'/ajax_get_item');
        $this->data['iteminfo_url'] = site_url($this->controller.'/ajax_item_info');
        $this->data['production_count'] = 0;
        if (is_superadmin()) {
            $id_division = 0;
            $this->data['divisions'] = $this->Purchase_model->getAllDivision();
        } else {
            $id_division = getSessionAdmin('admin_id_division');
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if (!is_superadmin()) {
                $post['id_division'] = getSessionAdmin('admin_id_division');
            } else {
                $id_division = $post['id_division'];
            }
            if ($this->validateForm()) {
                $post_production = false;
                if (isset($post['post_production']) && count($post['post_production'])>0) {
                    $post_production = $post['post_production'];
                    unset($post['post_production']);
                }
                $post['id_auth_user'] = id_auth_user();
                $last_id = $this->Purchase_model->InsertRecord($post);
                if ($last_id) {
                    //$invoice = 'INV'.$post['id_division'].$last_id.date('Ymd');
                    $total_price = 0;
                    $total_hpp = 0;
                    $total_discount = 0;
                    $product = array();
                    if ($post_production) {
                        foreach ($post_production as $prow) {
                            $product[$prow['code']]['code'] = $prow['code'];
                            $product[$prow['code']]['id'] = $prow['id'];
                            $product[$prow['code']]['qty'] = $prow['qty'];
                            $product[$prow['code']]['price'] = $prow['price'];
                            $product[$prow['code']]['hpp'] = $prow['hpp'];
                            $product[$prow['code']]['discount'] = $prow['discount'];
                            $product[$prow['code']]['id_purchase'] = $last_id;
                            $product[$prow['code']]['id_division'] = $post['id_division'];
                            $product[$prow['code']]['purchase_invoice'] = $post['purchase_invoice'];
                        }
                        $productions = array_values($product);
                        foreach ($productions as $production) {
                            $prd_insert['id_purchase'] = $production['id_purchase'];
                            $prd_insert['id_division'] = $production['id_division'];
                            $prd_insert['id_item'] = $production['id'];
                            $prd_insert['purchase_invoice'] = $production['purchase_invoice'];
                            $prd_insert['production_code'] = $production['code'];
                            $prd_insert['purchase_qty'] = $production['qty'];
                            $prd_insert['purchase_sales_price'] = $production['price'];
                            $prd_insert['purchase_hpp_price'] = $production['hpp'];
                            $prd_insert['purchase_discount_price'] = $production['discount'];
                            $total_price += $production['price'];
                            $total_hpp += $production['hpp'];
                            $total_discount += $production['discount'];
                            
                            // insert production
                            $this->Purchase_model->InsertPurchaseProduction($prd_insert);
                            
                            // insert production stock
                            $this->Purchase_model->UpdateProductionStock($prd_insert);
                            
                            // update item stock in division
                            $this->Purchase_model->UpdateItemStock($prd_insert);
                        }
                    }
                    // update transaction
                    $update = array(
                        'total_price'=>$total_price,
                        'total_hpp'=>$total_hpp,
                        'total_discount'=>$total_discount,
                        //'purchase_invoice'=>$invoice,
                    );
                    $this->Purchase_model->UpdateRecord($last_id,$update);
                    
                    // cek if division have credit
                    $credit = $this->Purchase_model->getDivisionCredit($post['id_division']);
                    if ($credit) {
                        // insert to payment
                        $payment['id_purchase'] = $last_id;
                        $payment['id_division'] = $post['id_division'];
                        $payment['purchase_invoice'] = $post['purchase_invoice'];
                        $payment['payment_type'] = 1;
                        $payment['payment_note'] = 'Diambil dari piutang divisi.';
                        $payment['payment_date'] = date('Y-m-d');
                        $payment['payment_total'] = ($total_price <= $credit) ? $total_price : $credit;
                        $this->Purchase_model->InsertPayment($payment);
                        
                        // update all credit to paid and set new if there's credit is more than total price
                        $c_status['credit_status'] = 1;
                        $this->Purchase_model->UpdateDivisionCredit($post['id_division'],$c_status);
                        // set as paid or add more credit if credit is more than total price
                        if ($total_price <= $credit) {
                            $updt_payment['payment_status'] = 2;
                        } else {
                            $updt_payment['payment_status'] = 1;
                        }
                        $this->Purchase_model->UpdateRecord($last_id,$updt_payment);
                        // set new credit if credit is more than total price
                        if ($total_price < $credit) {
                            $credits['id_purchase'] = $last_id;
                            $credits['id_division'] = $post['id_division'];
                            $credits['purchase_invoice'] = $post['purchase_invoice'];
                            $credits['credit_price'] = $credit - $total_price;
                            $this->Purchase_model->InsertDivisionCredit($credits);
                        }
                    }
                    
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect($this->controller);
                
            }
            if (isset($post['post_production']) && count($post['post_production'])>0) {
                $this->data['production_count'] = count($post['post_production']);
            }
            $this->data['post'] = $post;
        }
        $this->data['items'] = $this->Purchase_model->getItemByDivision($id_division);
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * edit new record
     */
    public function detail($id=0)
    {
        $this->load->model('Purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['print_url'] = site_url($this->controller.'/printit/'.$id);
        $this->data['page_title'] = 'Detail Pembelian Barang Produksi';
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Purchase_model->getPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['record'] = $detail;
    }
    
    /**
     * print page
     * @param int $id
     */
    public function printit($id=0) {
        $this->layout = 'layout/print';
        $this->load->model('Purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['page_title'] = 'Invoice Pembelian Barang Produksi';
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Purchase_model->getPurchase($id);
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
        $this->load->model('Purchase_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['form_action'] = site_url($this->controller.'/retur/'.$id);
        $this->data['page_title'] = 'Form Retur Pembelian Barang Produksi';
        $this->data['production_count'] = 0;
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Purchase_model->getPurchase($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['record'] = $detail;
        $this->data['productions'] = $this->Purchase_model->getPurchaseProduction($detail['id_purchase']);
        $this->data['retur'] = $this->Purchase_model->getPurchaseRetur($detail['id_purchase']);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateReturForm($id)) {
                $prd = array();
                $total_price_retur=0;
                foreach ($post['post_production'] as $production) {
                    $prd[$production['id']]['id'] = $production['id'];
                    $prd[$production['id']]['qty'] = $production['qty'];
                }
                $products = array_values($prd);
                foreach ($products as $row) {
                    $production_info = $this->Purchase_model->getPurchaseProductionInfo($detail['id_purchase'],$row['id']);
                    $production_insert['id_purchase'] = $detail['id_purchase'];
                    $production_insert['id_division'] = $detail['id_division'];
                    $production_insert['id_purchase_production'] = $row['id'];
                    $production_insert['production_code'] = $production_info['production_code'];
                    $production_insert['id_item'] = $production_info['id_item'];
                    $production_insert['retur_qty'] = $row['qty'];
                    $production_insert['retur_price'] = ($production_info['purchase_sales_price']-$production_info['purchase_discount_price']);
                    
                    // insert to db retur
                    $this->Purchase_model->InsertPurchaseRetur($production_insert);
                    
                    // update stock in master product
                    $this->Purchase_model->UpdateProductionStockRetur($row['id'],$row['qty']);
                    
                    $total_price_retur += ($production_info['purchase_sales_price']-$production_info['purchase_discount_price'])*$row['qty'];
                }
                
                // input to payment
                $payment['id_purchase'] = $detail['id_purchase'];
                $payment['id_division'] = $detail['id_division'];
                $payment['purchase_invoice'] = $detail['purchase_invoice'];
                $payment['payment_type'] = 1;
                $payment['payment_note'] = 'Pengurangan dari retur barang.';
                $payment['payment_date'] = date('Y-m-d');
                $payment['payment_total'] = $total_price_retur;
                $this->Purchase_model->InsertPayment($payment);
                
                // set transaction status if payment is paid
                $total_paid = $this->Purchase_model->getTotalPayment($detail['id_purchase']);
                if ($detail['total_price'] <= $total_paid) {
                    // set status as paid
                    $transaction['payment_status'] = 2;
                } else {
                    $transaction['payment_status'] = 1;
                }
                // update transaction
                $transaction['total_price_retur'] = $total_price_retur+$detail['total_price_retur'];
                $this->Purchase_model->UpdateRecord($detail['id_purchase'],$transaction);
                
                $this->session->set_flashdata('success_msg','Data berhasil diretur.');
                redirect($this->controller);
            }
            if (isset($post['post_production']) && count($post['post_production'])>0) {
                $this->data['production_count'] = count($post['post_production']);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * ajax get item list
     */
    public function ajax_get_item() {
        $this->layout = 'none';
        $json = array();
        if (is_ajax_requested() && $this->input->post()) {
            $this->load->model('Purchase_model');
            $post = $this->input->post();
            if (is_superadmin()) {
                $id_division = $post['division_id'];
            } else {
                $id_division = getSessionAdmin('admin_id_division');
            }
            if (!$id_division) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.<br/>','error');
            }
            $data = $this->Purchase_model->getItemByDivision($id_division);
            if ($data) {
                $return = '';
                foreach ($data as $row) {
                    $return .= '<option value="'.$row['id_item'].'">'.$row['item_category'].' ('.$row['item_name'].')</option>';
                }
                $json['return'] = $return;
            } else {
                $json['error'] = alert_box('Kode Barang tidak ada.<br/>','error');
            }
            echo json_encode($json);
        }
    }
    
    /**
     * request ajax item info
     */
    public function ajax_item_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();
            $this->load->model('Purchase_model');
            $json = array();
            if (!isset($post['item_id'])) {
                $json['error'] = alert_box('Mohon pilih Kode Barang.','error');
            }
            if (!isset($post['division_id'])) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.','error');
            }
            if (is_superadmin()) {
                $id_division = $post['division_id'];
            } else {
                $id_division = getSessionAdmin('admin_id_division');
            }
            if (!$id_division) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.<br/>','error');
            }
            if (!$json) {
                $data = $this->Purchase_model->getItemInfo($post['item_id'],$id_division);
                if (!$data) {
                    $json['error'] = alert_box('Kode Barang tidak ada. Mohon pilih Kode Produksi yang lain<br/>.');
                }
                if (!$json) {
                    $json['value'] = $data;
                }
            }
            echo json_encode($json);
        }
    }
    
    /**
     * payment
     */
    public function payment($id=0)
    {
        $id = (int)$id;
        if (!$id) {
            redirect('purchase');
        }
        $this->data['payment_count'] = 0;
        $this->load->model('Purchase_model');
        $this->data['form_action'] = site_url('purchase/payment/'.$id);
        $this->data['giro_info_url'] = site_url('purchase/ajax_giro_info');
        $this->data['cancel_url'] = site_url('purchase');
        $this->data['page_title'] = 'Pembayaran Invoice Pembelian Barang';
        $detail = $this->Purchase_model->getPurchase($id);
        if (!$detail) {
            redirect('purchase');
        }
        $this->data['giros'] = $this->Purchase_model->getUnUsedGiro();
        $this->data['division_credit'] = $this->Purchase_model->getDivisionCredit($detail['id_division']);
        $this->data['total_paid'] = $this->Purchase_model->getTotalPayment($detail['id_purchase']);
        $this->data['record'] = $detail;
        $this->data['payments'] = $this->Purchase_model->getPaymentByTransactionID($detail['id_purchase']);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validatePaymentForm($detail['id_purchase'])) {
                $post['id_purchase'] = $detail['id_purchase'];
                $post['purchase_invoice'] = $detail['purchase_invoice'];
                $post['id_purchase'] = $detail['id_purchase'];
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
                    $payment['id_purchase'] = $detail['id_purchase'];
                    $payment['purchase_invoice'] = $detail['purchase_invoice'];
                    $payment['id_purchase'] = $detail['id_purchase'];
                    $payment['payment_date'] = $post['payment_date'];
                    $total_price += ($pmt['price']);

                    // insert product to stock
                    $this->Purchase_model->InsertPayment($payment);
                    
                    // set giro as used if payment using giro
                    if ($pmt['type'] > 0) {
                        $giro['giro_status'] = 1;
                        $this->Purchase_model->UpdateGiro($payment['id_giro'],$giro);
                    }
                }
                $total_paid = ($total_price+$this->data['total_paid']);
                if ($total_paid >= $detail['total_price']) {
                    $update['payment_status'] = 2;
                } else {
                    $update['payment_status'] = 1;
                }
                $this->Purchase_model->UpdateRecord($detail['id_purchase'],$update);
                if ($total_paid > $detail['total_price']) {
                    $credit['id_purchase'] = $detail['id_purchase'];
                    $credit['id_division'] = $detail['id_division'];
                    $credit['purchase_invoice'] = $detail['purchase_invoice'];
                    $credit['credit_price'] = ($total_paid - $detail['total_price']);
                    $this->Purchase_model->InsertDivisionCredit($credit);
                }
                $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                redirect('purchase');
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
     * delete record
     */
    public function delete() {
        $this->load->model('Purchase_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Purchase_model->getPurchase($id);
                    if ($detail) {
                        $this->Purchase_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect('purchase');
        }
    }
    
    /**
     * request ajax giro info
     */
    public function ajax_giro_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();$this->load->model('Purchase_model');
            $json = array();
            if (!isset($post['giro_id'])) {
                $json['error'] = alert_box('Mohon pilih Giro.','error');
            }
            $data = $this->Purchase_model->getGiroValueByID($post['giro_id']);
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
        $this->load->model('Purchase_model');
        $post = $this->input->post();
        $err = '';
        if ($post['purchase_invoice'] == '') {
            $err .= 'Mohon isi No Faktur.<br/>';
        }
        if ($post['id_division'] == '') {
            $err .= 'Mohon pilih Division.<br/>';
        }
        
        if (!isset($post['post_production']) || count($post['post_production'])==0) {
            $err .= 'Mohon tambah barang produksi.<br/>';
        } else {
            foreach ($post['post_production'] as $product) {
                if ($product['code'] == '' || $product['id'] == '' || ($product['qty'] < 1 || $product['hpp'] < 1 || $product['price'] < 1)) {
                    $err .= 'Mohon isi barang produksi dengan lengkap.<br/>';
                    break;
                } else {
                    if (!$this->Purchase_model->CheckExistsProductionCode($product['code'])) {
                        $err .= 'Kode Produksi telah digunakan/sudah ada.<br/>';
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
        $this->load->model('Purchase_model');
        $detail = $this->Purchase_model->getPurchase($id);
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
                        $total_paid = $this->Purchase_model->getTotalPayment($detail['id_purchase']);
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
        $this->load->model('Purchase_model');
        $detail = $this->Purchase_model->getPurchase($id);
        $post = $this->input->post();
        $err = '';
        if (!isset($post['post_production'])) {
            $err .= 'Mohon isi Barang yang akan di retur.<br/>';
        } else {
            $prd = array();
            foreach ($post['post_production'] as $production) {
                $prd[$production['id']]['id'] = $production['id'];
                $prd[$production['id']]['qty'] = $production['qty'];
            }
            $productions = array_values($prd);
            foreach ($productions as $row) {
                // check if production is already retur 
                if (!$this->Purchase_model->CheckPurchaseRetur($detail['id_purchase'],$row['id'])) {
                    $err .= 'Barang Produksi ini sudah di retur.<br/>';
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

/* End of file purchase.php */
/* Location: ./application/controllers/purchase.php */