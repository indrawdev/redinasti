<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Sales Class
 * this class is for sales management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Sales Controller
 */
class Sales extends CI_Controller
{
    //private $error = array();
    private $error = '';
    private $controller = 'sales';
    
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
        $alias['search_invoice'] = "a.sales_invoice";
        $alias['search_store'] = "b.store";
        $alias['search_pic'] = "b.store_pic";
        $alias['search_address'] = "b.store_address";
        $alias['search_opt_payment_status'] = "a.payment_status";
        $query = "
            select 
                a.id_sales as id, 
                a.id_sales as idx, 
                b.*,
                a.sales_invoice,
                a.shipping_date,
                a.total_price,
                a.payment_status,
                a.create_date as date_created
            from " . $this->db->dbprefix('sales') . " a
            left join " . $this->db->dbprefix('store') . " b on b.id_store=a.id_store
            where a.is_delete=0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Sales_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['productioninfo_url'] = site_url($this->controller.'/ajax_production_info');
        $this->data['page_title'] = 'Tambah Penjualan Barang';
        $this->data['stores'] = $this->Sales_model->getStores();
        $this->data['productions'] = $this->Sales_model->getProductions();
        $this->data['production_count'] = 0;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $post_production = false;
                if (isset($post['post_production']) && count($post['post_production'])>0) {
                    $post_production = $post['post_production'];
                    unset($post['post_production']);
                }
                $post['id_auth_user'] = id_auth_user();
                $last_id = $this->Sales_model->InsertRecord($post);
                if ($last_id) {
                    $invoice = 'INVSLS/'.$post['id_store'].'/'.$last_id.date('Ymd');
                    $total_price = 0;
                    if ($post_production) {
                        $production = array();
                        foreach ($post_production as $prow) {
                            $production[$prow['id_production']]['id_sales'] = $last_id;
                            $production[$prow['id_production']]['id_store'] = $post['id_store'];
                            $production[$prow['id_production']]['id_production'] = $prow['id_production'];
                            $production[$prow['id_production']]['sales_qty'] = 1;
                            $production[$prow['id_production']]['sales_price'] = $prow['price'];
                            $production[$prow['id_production']]['discount_percentage'] = (int)$prow['discount'];
                        }
                        $productions = array_values($production);
                        foreach ($productions as $prd) {
                            $production_info = $this->Sales_model->getProductionInfo($prd['id_production']);
                            $prdtion['id_sales'] = $last_id;
                            $prdtion['id_store'] = $post['id_store'];
                            $prdtion['id_production'] = $prd['id_production'];
                            $prdtion['production_code'] = $production_info['production_code'];
                            $prdtion['id_item'] = $production_info['id_item'];
                            $prdtion['id_division'] = $production_info['id_division'];
                            $prdtion['sales_qty'] = 1;
                            $prdtion['sales_price'] = $prd['sales_price'];
                            $prdtion['discount_percentage'] = $prd['discount_percentage'];
                            $prdtion['buy_price'] = ($production_info['production_sell_price']-$production_info['production_discount_price']);
                            
                            // calculate total price
                            $percentage = 0;
                            if ($prd['discount_percentage']) {
                                $percent = ($prd['discount_percentage'] / 100) * $prd['sales_price'];
                                $percentage = round($percent);
                            }
                            
                            $total_price += ($prd['sales_price']-$percentage);
                            
                            // insert product to stock
                            $this->Sales_model->InsertProduction($prdtion);
                            
                            // update production status
                            $set_status = 2;
                            $data_production = array(
                                'production_status'=>$set_status,
                                'production_sell_price'=>$prd['sales_price']
                            );
                            $this->Sales_model->UpdateProduction($prd['id_production'],$data_production);
                        }
                    }
                    
                    // update transaction
                    $update = array(
                        'total_price'=>$total_price,
                        'sales_invoice'=>$invoice,
                    );
                    $this->Sales_model->UpdateRecord($last_id,$update);
                    
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
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * request ajax production info
     */
    public function ajax_production_info() {
        $this->layout = 'none';
        if (is_ajax_requested() && $this->input->post()) {
            $post = $this->input->post();
            $this->load->model('Sales_model');
            $json = array();
            if (!isset($post['production_id'])) {
                $json['error'] = alert_box('Mohon pilih Barang.','error');
            }
            if (!$json) {
                if (isset($post['store_id'])) {
                    $id_store = $post['store_id'];
                } else {
                    $id_store = 0;
                }
                //$data = $this->Sales_model->getProductionInfo($post['production_id']);
                $data = $this->Sales_model->getProductionInfoByStore($post['production_id'],$id_store);
                //print_r($data);
                if (!$data) {
                    $json['error'] = alert_box('Barang tidak ada. Mohon pilih Barang yang lain<br/>.');
                }
                if (!$json) {
                    $json['value'] = $data;
                }
            }
            echo json_encode($json);
        }
    }
    
    /**
     * edit new record
     */
    public function detail($id=0)
    {
        $this->load->model('Sales_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['print_url'] = site_url($this->controller.'/printit/'.$id);
        $this->data['page_title'] = 'Detail Penjualan Barang';
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Sales_model->getSales($id);
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
        $this->load->model('Sales_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['page_title'] = 'Invoice Penjualan Barang Produksi';
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Sales_model->getSales($id);
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
        $this->load->model('Sales_model');
        $this->data['back_url'] = site_url($this->controller);
        $this->data['form_action'] = site_url($this->controller.'/retur/'.$id);
        $this->data['page_title'] = 'Form Retur Penjualan Barang Produksi';
        $this->data['production_count'] = 0;
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Sales_model->getSales($id);
        if (!$detail) {
            redirect($this->controller);
        }
        $this->data['record'] = $detail;
        $this->data['productions'] = $this->Sales_model->getSalesProduction($detail['id_sales']);
        $this->data['retur'] = $this->Sales_model->getSalesRetur($detail['id_sales']);
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
                    $production_info = $this->Sales_model->getSalesProductionInfo($detail['id_sales'],$row['id']);
                    $production_insert['id_sales'] = $detail['id_sales'];
                    $production_insert['id_store'] = $detail['id_store'];
                    $production_insert['id_production'] = $row['id'];
                    $production_insert['id_division'] = $production_info['id_division'];
                    $production_insert['id_item'] = $production_info['id_item'];
                    $production_insert['retur_qty'] = $row['qty'];
                    $production_insert['production_code'] = $production_info['production_code'];
                    $production_insert['retur_price'] = $production_info['sales_price'];
                    
                    // insert to db retur
                    $this->Sales_model->InsertSalesRetur($production_insert);
                    
                    // update stock in master product
                    //$this->Sales_model->UpdateProductionStockRetur($row['id'],$row['qty']);
                    
                    $total_price_retur += $production_info['sales_price']*$row['qty'];
                }
                
                // input to payment
                $payment['id_sales'] = $detail['id_sales'];
                $payment['id_store'] = $detail['id_store'];
                $payment['sales_invoice'] = $detail['sales_invoice'];
                $payment['payment_type'] = 1;
                $payment['payment_note'] = 'Pengurangan dari retur barang.';
                $payment['payment_date'] = date('Y-m-d');
                $payment['payment_total'] = $total_price_retur;
                $this->Sales_model->InsertPayment($payment);
                
                // set transaction status if payment is paid
                $total_paid = $this->Sales_model->getTotalPayment($detail['id_sales']);
                if ($detail['total_price'] <= $total_paid) {
                    // set status as paid
                    $transaction['payment_status'] = 2;
                } else {
                    $transaction['payment_status'] = 1;
                }
                // update transaction
                $transaction['total_price_retur'] = $total_price_retur+$detail['total_price_retur'];
                $this->Sales_model->UpdateRecord($detail['id_sales'],$transaction);
                
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
     * payment
     */
    public function payment($id=0)
    {
        $id = (int)$id;
        if (!$id) {
            redirect($this->controller);
        }
        $this->load->model('Sales_model');
        $this->data['form_action'] = site_url($this->controller.'/payment/'.$id);
        $this->data['cancel_url'] = site_url($this->controller);
        $this->data['page_title'] = 'Pembayaran Invoice Penjualan';
        $this->data['payment_count'] = 0;
        $detail = $this->Sales_model->getSales($id);
        if (!$detail) {
            redirect($this->controller);
        }
        if (!is_superadmin()) {
            $id_division = getSessionAdmin('admin_id_division');
            if ($detail['id_division'] != $id_division) {
                redirect($this->controller);
            }
        }
        $this->data['total_paid'] = $this->Sales_model->getTotalPayment($detail['id_sales']);
        $this->data['record'] = $detail;
        $this->data['payments'] = $this->Sales_model->getPaymentByTransactionID($detail['id_sales']);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validatePaymentForm($detail['id_sales'])) {
                $post['id_sales'] = $detail['id_sales'];
                $post['sales_invoice'] = $detail['sales_invoice'];
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
                    $picture_db = file_copy_to_folder($post_image['payment_image'], IMG_UPLOAD_DIR.'sales/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'sales/'.$picture_db, IMG_UPLOAD_DIR.'sales/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'sales/'.$picture_db, IMG_UPLOAD_DIR.'sales/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
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
                        $payment['id_giro'] = $this->Sales_model->InsertGiro($giro);
                    }
                    $payment['id_sales'] = $id;
                    $payment['id_store'] = $detail['id_store'];
                    $payment['sales_invoice'] = $detail['sales_invoice'];
                    $payment['payment_type'] = $pay['type'];
                    $payment['payment_note'] = $post['payment_note'];
                    $payment['payment_date'] = $post['payment_date'];
                    $payment['payment_total'] = $pay['price'];
                    $payment['payment_image'] = $picture_db;
                    $total_payment += $pay['price'];

                    // insert payment
                    $this->Sales_model->InsertPayment($payment);
                }
                $total_paid = ($total_payment+$this->data['total_paid']);
                if ($total_paid >= $detail['total_price']) {
                    $update['payment_status'] = 2;
                } else {
                    $update['payment_status'] = 1;
                }
                $this->Sales_model->UpdateRecord($detail['id_sales'],$update);
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
     * delete record
     */
    public function delete() {
        $this->load->model('Sales_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Sales_model->getSupplierPurchase($id);
                    if ($detail) {
                        $this->Supplier_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect('supplier_purchase');
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Sales_model');
        $post = $this->input->post();
        $err = '';
        /*
        if ($post['sales_invoice'] == '') {
            $err .= 'Mohon isi No Faktur.<br/>';
        }
         * 
         */
        if ($post['id_store'] == '') {
            $err .= 'Mohon pilih Toko.<br/>';
        }
        
        if (!isset($post['post_production']) || count($post['post_production'])==0) {
            $err .= 'Mohon tambahkan Barang untuk transaksi.<br/>';
        } else {
            $production = array();
            foreach ($post['post_production'] as $prow) {
                $production[$prow['id_production']]['id_production'] = $prow['id_production'];
                $production[$prow['id_production']]['price'] = $prow['price'];
            }
            $productions = array_values($production);
            foreach ($productions as $prd) {
                if ($prd['id_production'] == '') {
                    $err .= 'Mohon isi Barang.<br/>';
                    break;
                } else {
                    if ($prd['price'] == '' || !ctype_digit($prd['price'])) {
                        $err .= 'Mohon isi Harga Jual Barang.<br/>';
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
        $this->load->model('Sales_model');
        $detail = $this->Sales_model->getSales($id);
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
        $this->load->model('Sales_model');
        $detail = $this->Sales_model->getSales($id);
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
                if (!$this->Sales_model->CheckSalesRetur($detail['id_sales'],$row['id'])) {
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

/* End of file sales.php */
/* Location: ./application/controllers/sales.php */