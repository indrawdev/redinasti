<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Store Class
 * this class is for store management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Store Controller
 */
class Store extends CI_Controller
{
    //private $error = array();
    private $error = '';

    /**
     * Index Store for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('store/add');
        $this->data['export_excel_url'] = site_url('store/export_excel');
        $this->data['list_data'] = site_url('store/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_store'] = "store";
        $alias['search_pic'] = "store_pic";
        $alias['search_address'] = "store_address";
        $query = "
            select 
                id_store as id, 
                id_store as idx, 
                " . $this->db->dbprefix('store') . ".*
            from " . $this->db->dbprefix('store') . "
            where is_delete=0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Store_model');
        $this->data['form_action'] = site_url('store/add');
        $this->data['cancel_url'] = site_url('store');
        $this->data['page_title'] = 'Tambah Toko';
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $products = false;
                if (isset($post['products'])) {
                    $products = $post['products'];
                    unset($post['products']);
                }
                $last_id = $this->Store_model->InsertNewRecord($post);
                if ($last_id) {
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('store');
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
        $this->load->model('Store_model');
        $this->data['form_action'] = site_url('store/edit/'.$id);
        $this->data['cancel_url'] = site_url('store');
        $this->data['page_title'] = 'Edit Toko';
        $id = (int)$id;
        if (!$id) {
            redirect('store');
        }
        $detail = $this->Store_model->getStore($id);
        $this->data['post'] = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $this->Store_model->UpdateRecord($id,$post);
                $this->session->set_flashdata('success_msg','Edit data berhasil.');
                redirect('store');
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
        $this->load->model('Store_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Store_model->getStore($id);
                    if ($detail) {
                        $this->Store_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect('store');
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Store_model');
        $post = $this->input->post();
        $err = '';
        if ($post['store'] == '') {
            $err .= 'Mohon isi Nama Toko.<br/>';
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}
/* End of file store.php */
/* Location: ./application/controllers/store.php */