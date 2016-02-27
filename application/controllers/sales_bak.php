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
    
    /**
     * Index Supplier for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('sales/add');
        $this->data['export_excel_url'] = site_url('sales/export_excel');
        $this->data['list_data'] = site_url('sales/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_invoice'] = "a.purchase_invoice";
        $alias['search_store'] = "b.supplier";
        $alias['search_pic'] = "b.supplier_pic";
        $alias['search_address'] = "b.supplier_address";
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
        $this->data['form_action'] = site_url('sales/add');
        $this->data['cancel_url'] = site_url('sales');
        $this->data['search_store_url'] = site_url('sales/searchq');
        $this->data['getproduction_url'] = site_url('sales/ajax_get_production');
        $this->data['productioninfo_url'] = site_url('sales/ajax_production_info');
        $this->data['page_title'] = 'Tambah Penjualan';
        $this->data['stores'] = $this->Sales_model->getStores();
        $this->data['production_count'] = 0;
        if (is_superadmin()) {
            $id_division = 0;
            $this->data['divisions'] = $this->Sales_model->getDivisions();
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
                $post['id_auth_user'] = adm_sess_userid();
                $last_id = $this->Sales_model->InsertRecord($post);
                if ($last_id) {
                    //$invoice = 'INV'.$post['id_supplier'].$last_id.date('Ymd');
                    $total_price = 0;
                    //$total_qty = 0;
                    //$product_price = 0;
                    if ($post_production) {
                        $production = array();
                        foreach ($post_production as $prow) {
                            $production[$prow['id_production']]['id_sales'] = $last_id;
                            $production[$prow['id_production']]['id_store'] = $post['id_store'];
                            $production[$prow['id_production']]['id_production'] = $prow['id_production'];
                            $production[$prow['id_production']]['id_division'] = $post['id_division'];
                            $production[$prow['id_production']]['production_hpp'] = $prow['production_hpp'];
                            $production[$prow['id_production']]['production_price'] = $prow['production_price'];
                        }
                        $productions = array_values($production);
                        foreach ($productions as $prd) {
                            $prdtion['id_sales'] = $last_id;
                            $prdtion['id_store'] = $post['id_store'];
                            $prdtion['id_division'] = $post['id_division'];
                            $prdtion['id_production'] = $prd['id_production'];
                            $prdtion['production_hpp_price'] = $prd['production_hpp'];
                            $prdtion['sales_price'] = $prd['production_price'];
                            $total_price += $prd['production_price'];
                            
                            // insert product to stock
                            $this->Sales_model->InsertProduction($prdtion);
                            
                            // update production status
                            $set_status = 2;
                            $data_production = array(
                                'production_status'=>$set_status,
                                'production_sell_price'=>$prd['production_price']
                            );
                            $this->Sales_model->UpdateProduction($prd['id_production'],$data_production);
                        }
                    }
                    // update transaction
                    $update = array(
                        'total_price'=>$total_price,
                        //'purchase_invoice'=>$invoice,
                    );
                    $this->Sales_model->UpdateRecord($last_id,$update);
                    
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('sales');
                
            }
            if (isset($post['post_production']) && count($post['post_production'])>0) {
                $this->data['production_count'] = count($post['post_production']);
            }
            $this->data['post'] = $post;
        }
        $this->data['productions'] = $this->Sales_model->getProductions($id_division);
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
                $data = $this->Sales_model->getProductionInfo($post['production_id'],$id_division);
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
     * ajax get production list
     */
    public function ajax_get_production() {
        $this->layout = 'none';
        $json = array();
        if (is_ajax_requested() && $this->input->post()) {
            $this->load->model('Sales_model');
            $post = $this->input->post();
            if (is_superadmin()) {
                $id_division = $post['division_id'];
            } else {
                $id_division = getSessionAdmin('admin_id_division');
            }
            if (!$id_division) {
                $json['error'] = alert_box('Mohon pilih Divisi terlebih dahulu.<br/>','error');
            }
            $data = $this->Sales_model->getProductions($id_division);
            if ($data) {
                $return = '';
                foreach ($data as $row) {
                    $return .= '<option value="'.$row['id_production'].'">'.$row['production_name'].' ('.$row['production_code'].')</option>';
                }
                $json['return'] = $return;
            } else {
                $json['error'] = alert_box('Barang tidak ada.<br/>','error');
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
        $this->data['back_url'] = site_url('sales');
        $this->data['print_url'] = site_url('sales/print/'.$id);
        $this->data['page_title'] = 'Detail Penjualan';
        $id = (int)$id;
        if (!$id) {
            redirect('sales');
        }
        $detail = $this->Sales_model->getSales($id);
        if (!$detail) {
            redirect('sales');
        }
        if (!is_superadmin()) {
            $id_division = getSessionAdmin('admin_id_division');
            if ($detail['id_division'] != $id_division) {
                redirect('sales');
            }
        }
        $this->data['record'] = $detail;
    }
    
    /**
     * payment
     */
    public function payment($id=0)
    {
        $id = (int)$id;
        if (!$id) {
            redirect('sales');
        }
        $this->load->model('Sales_model');
        $this->data['form_action'] = site_url('sales/payment/'.$id);
        $this->data['cancel_url'] = site_url('sales');
        $this->data['page_title'] = 'Pembayaran Invoice Penjualan';
        $detail = $this->Sales_model->getSales($id);
        if (!$detail) {
            redirect('sales');
        }
        if (!is_superadmin()) {
            $id_division = getSessionAdmin('admin_id_division');
            if ($detail['id_division'] != $id_division) {
                redirect('sales');
            }
        }
        $this->data['total_paid'] = $this->Sales_model->getTotalPayment($detail['id_sales']);
        $this->data['record'] = $detail;
        $this->data['payments'] = $this->Sales_model->getPaymentByTransactionID($detail['id_sales']);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validatePaymentForm($detail['id_supplier_purchase'])) {
                $post['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                $post['purchase_invoice'] = $detail['purchase_invoice'];
                $post['id_supplier_purchase'] = $detail['id_supplier_purchase'];
                $post_giro = false;
                if ($post['payment_type'] == 2) {
                    $post_giro['giro_code'] = $post['giro_code'];
                    $post_giro['giro_date'] = $post['giro_date'];
                    $post_giro['giro_bank'] = $post['giro_bank'];
                    $post_giro['giro_price'] = $post['payment_total'];
                    $post_giro['giro_from'] = 2;
                    $post_giro['giro_invoice'] = $post['purchase_invoice'];
                }
                unset($post['giro_date']);
                unset($post['giro_bank']);
                $last_id = $this->Sales_model->InsertPayment($post);
                if ($last_id) {
                    $post_image = $_FILES;
                    if ($post_image['payment_image']['tmp_name']) {
                        $filename = 'proof_'.md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['payment_image'], IMG_UPLOAD_DIR.'supplier_purchase/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'supplier_purchase/'.$picture_db, IMG_UPLOAD_DIR.'supplier_purchase/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'supplier_purchase/'.$picture_db, IMG_UPLOAD_DIR.'supplier_purchase/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                        
                        $this->Sales_model->UpdatePayment($last_id,array('payment_image'=>$picture_db));
                    }
                    $total_paid = ($post['payment_total']+$this->data['total_paid']);
                    if ($total_paid >= $detail['total_price']) {
                        $update['payment_status'] = 2;
                    } else {
                        $update['payment_status'] = 1;
                    }
                    if ($post_giro) {
                        $update['id_giro'] = $this->Sales_model->InsertGiro($post_giro);
                    }
                    $this->Sales_model->UpdateRecord($detail['id_supplier_purchase'],$update);
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('supplier_purchase');
                
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * search supplier
     */
    public function searchq() {
        $this->layout='none';
        $json = array();
        if ($this->input->get('q') || ($this->input->get('id') && ctype_digit($this->input->get('id')))) {
            $get = $this->input->get();
            $this->load->model('Sales_model');
            if (utf8_strlen($get['q'])>=3) {
                $data = $this->Sales_model->SearchSupplier($get['q'],$get['page']);
                if ($data) {
                    $json['records'] = $data;
                }
            }
            if (isset($get['id'])) {
                $data = $this->Sales_model->getSupplierByID($get['id']);
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
        if ($post['sales_invoice'] == '') {
            $err .= 'Mohon isi No Faktur.<br/>';
        }
        if ($post['id_store'] == '') {
            $err .= 'Mohon pilih Toko.<br/>';
        }
        
        if (!isset($post['post_production']) || count($post['post_production'])==0) {
            $err .= 'Mohon tambah Barang.<br/>';
        } else {
            $production = array();
            foreach ($post['post_production'] as $prow) {
                $production[$prow['id_production']]['id_production'] = $prow['id_production'];
                $production[$prow['id_production']]['production_hpp'] = $prow['production_hpp'];
                $production[$prow['id_production']]['production_price'] = $prow['production_price'];
            }
            $productions = array_values($production);
            foreach ($productions as $prd) {
                if ($prd['id_production'] == '') {
                    $err .= 'Mohon isi Barang.<br/>';
                    break;
                } else {
                    if ($prd['production_price'] == '' || !ctype_digit($prd['production_price'])) {
                        $err .= 'Mohon isi Harga Jual Barang.<br/>';
                        break;
                    } else {
                        if ( ($prd['id_production']!='') && ($prd['production_price'] < $prd['production_hpp'])) {
                            $err .= 'Harga Jual harus lebih besar dari HPP.<br/>';
                            break;
                        }
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
        $detail = $this->Sales_model->getgetSales($id);
        $total_price = $detail['total_price'];
        $post = $this->input->post();
        $err = '';
        if (!isset($post['payment_type']) && $post['payment_type'] == '0') {
            $err .= 'Mohon pilih Tipe Pembayaran.<br/>';
        }
        if ($post['payment_total'] == '') {
            $err .= 'Mohon isi Jumlah Pembayaran.<br/>';
        } else {
            $post['payment_total'] = (int)$post['payment_total'];
            if ($total_price < $post['payment_total']) {
                $err .= 'Mohon isi Jumlah yang lebih kecil dari Total Harga.<br/>';
            } else {
                $total_paid = $this->Sales_model->getTotalPayment($detail['id_sales']);
                if ( ($total_paid+$post['payment_total']) > $total_price) {
                    $err .= 'Mohon isi Jumlah yang lebih kecil dari Total Harga.<br/>';
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

}

/* End of file sales.php */
/* Location: ./application/controllers/sales.php */