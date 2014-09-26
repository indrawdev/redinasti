<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Cashier Class
 * this class is for division purchase management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Cashier Controller
 */
class Cashier extends CI_Controller
{
    //private $error = array();
    private $error = '';
    
    /**
     * index form
     */
    public function index()
    {
        $this->load->model('Cashier_model');
        $this->data['form_action'] = site_url('cashier');
        $this->data['product_info_url'] = site_url('cashier/ajax_product_info');
        $this->data['cancel_url'] = site_url('cashier');
        $this->data['search_division_url'] = site_url('cashier/searchq');
        $this->data['page_title'] = 'Tambah Penjualan Divisi';
        if (is_superadmin()) {
            $this->data['divisions'] = $this->Cashier_model->getAllDivision();
        }
        $this->data['products'] = $this->Cashier_model->getProducts();
        $this->data['product_count'] = 0;
        $this->data['credit_count'] = 0;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $post_product = false;
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
                $last_id = $this->Cashier_model->InsertRecord($post);
                if ($last_id) {
                    $invoice = 'INV'.$post['id_division'].$last_id.date('Ymd');
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
                            $product['id_division_purchase'] = $last_id;
                            $product['id_division'] = $post['id_division'];
                            $product['id_product'] = $prd['id_product'];
                            $product['purchase_qty'] = $prd['purchase_qty'];
                            $product['purchase_price'] = $prd['purchase_price'];
                            $total_price += ($prd['purchase_price']*$prd['purchase_qty']);
                            
                            // insert product to division stock
                            $this->Cashier_model->InsertDivisionProduct($product);
                            
                            // update product record
                            $this->Cashier_model->UpdateProductStock($prd['id_product'],$prd['purchase_qty'],$prd['purchase_price'],$post['id_division']);
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
                    $this->Cashier_model->UpdateRecord($last_id,$update);
                    
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('cashier');
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
            $this->load->model('Cashier_model');
            $json = array();
            if (!isset($post['product_id'])) {
                $json['error'] = alert_box('Mohon pilih Produk.','error');
            }
            $data = $this->Cashier_model->getProductInfo($post['product_id']);
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
     * search division
     */
    public function ajax_get_product_info() {
        $this->layout='none';
        $json = array();
        if ($this->input->get('q') || ($this->input->get('id') && ctype_digit($this->input->get('id')))) {
            $get = $this->input->get();
            $this->load->model('Cashier_model');
            if (utf8_strlen($get['q'])>=3) {
                $data = $this->Cashier_model->SearchDivision($get['q'],$get['page']);
                if ($data) {
                    $json['records'] = $data;
                }
            }
            if (isset($get['id'])) {
                $data = $this->Cashier_model->getDivisionByID($get['id']);
                if ($data) {
                    $json['id'] = $data['id_division'];
                    $json['text'] = $data['division'].' - '.$data['division_address'];
                }
            }
        }
        echo json_encode($json);
    }
    
    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Cashier_model');
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
                        if ($this->Cashier_model->checkProductStock($product['id_product']) < $product['purchase_qty']) {
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
    

}

/* End of file cashier.php */
/* Location: ./application/controllers/cashier.php */