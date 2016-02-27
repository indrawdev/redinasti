<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Item Category Class
 * this class is for item item_category management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Item Category Controller
 */
class Item_category extends CI_Controller {

    //private $error = array();
    private $error = '';
    private $controller = 'item_category';

    /**
     * Index Item_category for this controller.
     */
    public function index() {
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
    public function list_data() {
        $alias['search_category'] = "item_category";
        $query = "
            select 
                id_item_category as id, 
                id_item_category as idx, 
                " . $this->db->dbprefix('item_category') . ".*
            from " . $this->db->dbprefix('item_category') . "
            where is_delete = 0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }

    /**
     * add new record
     */
    public function add() {
        $this->load->model('Item_category_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['page_title'] = 'Tambah Nama Barang';
        $this->data['cancel_url'] = site_url($this->controller);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $last_id = $this->Item_category_model->InsertNewRecord($post);
                if ($last_id) {
                    $this->session->set_flashdata('success_msg', 'Berhasil tambah data.');
                } else {
                    $this->session->set_flashdata('tmp_msg', 'Gagal.');
                }
                redirect($this->controller);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['err_message'] = alert_box($this->error, 'error');
        }
        $this->data['content_layout'] = 'form';
    }

    /**
     * edit new record
     */
    public function edit($id = 0) {
        $this->load->model('Item_category_model');
        $this->data['form_action'] = site_url($this->controller.'/edit/' . $id);
        $this->data['page_title'] = 'Edit Nama Barang';
        $this->data['cancel_url'] = site_url($this->controller);
        $id = (int) $id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Item_category_model->getItemCategory($id);
        $this->data['post'] = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $this->Item_category_model->UpdateRecord($id, $post);
                $this->session->set_flashdata('success_msg', 'Berhasil edit data.');

                redirect($this->controller);
            }
            $this->data['post'] = $post;
        }
        if ($this->error) {
            $this->data['message'] = alert_box($this->error, 'error');
        }
        $this->data['content_layout'] = 'form';
    }

    /**
     * delete record
     */
    public function delete() {
        $this->load->model('Item_category_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int) $this->input->post('iddel');
                if ($id) {
                    $detail = $this->Item_category_model->getItemCategory($id);
                    if ($detail) {
                        $this->Item_category_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg', 'Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message', 'Tidak ada data.');
                    }
                }
            }
        } else {
            redirect($this->controller);
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id = 0) {
        $id = (int) $id;
        $err = '';
        $post = $this->input->post();
        if ($post['item_category'] == '') {
            $err .= 'Mohon isi Nama Barang.<br/>';
        }

        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}

/* End of file item_category.php */
/* Location: ./application/controllers/item_category.php */