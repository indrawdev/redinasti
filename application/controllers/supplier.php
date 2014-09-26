<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Supplier Class
 * this class is for supplier management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Supplier Controller
 */
class Supplier extends CI_Controller
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
        $this->data['add_url'] = site_url('supplier/add');
        $this->data['export_excel_url'] = site_url('supplier/export_excel');
        $this->data['list_data'] = site_url('supplier/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_supplier'] = "supplier";
        $alias['search_pic'] = "supplier_pic";
        $alias['search_address'] = "supplier_address";
        $query = "
            select 
                id_supplier as id, 
                id_supplier as idx, 
                " . $this->db->dbprefix('supplier') . ".*
            from " . $this->db->dbprefix('supplier') . "
            where is_delete=0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Supplier_model');
        $this->data['form_action'] = site_url('supplier/add');
        $this->data['cancel_url'] = site_url('supplier');
        $this->data['page_title'] = 'Tambah Supplier';
        $this->data['products'] = $this->Supplier_model->listProduct();
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $products = false;
                if (isset($post['products'])) {
                    $products = $post['products'];
                    unset($post['products']);
                }
                $last_id = $this->Supplier_model->InsertNewRecord($post);
                if ($last_id) {
                    // insert product to supplier if set
                    if ($products) {
                        foreach ($products as $product => $val) {
                            $input['id_product'] = $val;
                            $input['id_supplier'] = $last_id;
                            $this->Supplier_model->InsertSupplierProduct($input);
                        }
                    }
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('supplier');
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
        $this->data['content_layout'] = 'form';
    }
    
    /**
     * edit new record
     */
    public function edit($id=0)
    {
        $this->load->model('Supplier_model');
        $this->data['form_action'] = site_url('supplier/edit/'.$id);
        $this->data['cancel_url'] = site_url('supplier');
        $this->data['page_title'] = 'Edit Supplier';
        $this->data['supplier_product'] = $this->Supplier_model->connSupplierProduct($id);
        $this->data['products'] = $this->Supplier_model->listProduct();
        $id = (int)$id;
        if (!$id) {
            redirect('supplier');
        }
        $detail = $this->Supplier_model->getSupplier($id);
        $detail['products'] = $this->data['supplier_product'];
        $this->data['post'] = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $products = false;
                if (isset($post['products'])) {
                    $products = $post['products'];
                    unset($post['products']);
                }
                $this->Supplier_model->UpdateRecord($id,$post);
                $this->Supplier_model->DeleteSupplierProduct($id);
                // insert product to supplier if set
                if ($products) {
                    foreach ($products as $product => $val) {
                        $input['id_product'] = $val;
                        $input['id_supplier'] = $id;
                        $this->Supplier_model->InsertSupplierProduct($input);
                    }
                }
                $this->session->set_flashdata('success_msg','Edit data berhasil.');
                redirect('supplier');
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
        $this->data['content_layout'] = 'form';
    }
    
    /**
     * delete record
     */
    public function delete() {
        $this->load->model('Supplier_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Supplier_model->getSupplier($id);
                    if ($detail) {
                        $this->Supplier_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect('supplier');
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Supplier_model');
        $post = $this->input->post();
        $err = '';
        if ($post['supplier'] == '') {
            $err .= 'Mohon isi Nama Supplier.<br/>';
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}
/* End of file supplier.php */
/* Location: ./application/controllers/supplier.php */