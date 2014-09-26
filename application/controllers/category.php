<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Category Class
 * this class is for category management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Category Controller
 */
class Category extends CI_Controller {

    //private $error = array();
    private $error = '';

    /**
     * Index Category for this controller.
     */
    public function index() {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('category/add');
        $this->data['export_excel_url'] = site_url('category/export_excel');
        $this->data['list_data'] = site_url('category/list_data');
    }

    /**
     * list of data
     */
    public function list_data() {
        $alias['search_category'] = "category";
        $query = "
            select 
                id_category as id, 
                id_category as idx, 
                " . $this->db->dbprefix('category') . ".*
            from " . $this->db->dbprefix('category') . "
            where is_delete = 0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }

    /**
     * add new record
     */
    public function add() {
        $this->load->model('Category_model');
        $this->data['form_action'] = site_url('category/add');
        $this->data['page_title'] = 'Tambah Kategori';
        $this->data['cancel_url'] = site_url('category');
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $last_id = $this->Category_model->InsertNewRecord($post);
                if ($last_id) {
                    $this->session->set_flashdata('success_msg', 'Berhasil tambah data.');
                } else {
                    $this->session->set_flashdata('tmp_msg', 'Gagal.');
                }
                redirect('category');
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
        $this->load->model('Category_model');
        $this->data['form_action'] = site_url('category/edit/' . $id);
        $this->data['page_title'] = 'Edit Kategori';
        $this->data['cancel_url'] = site_url('category');
        $id = (int) $id;
        if (!$id) {
            redirect('category');
        }
        $detail = $this->Category_model->getCategory($id);
        $this->data['post'] = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $this->Category_model->UpdateRecord($id, $post);
                $this->session->set_flashdata('success_msg', 'Berhasil edit data.');

                redirect('category');
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
        $this->load->model('Category_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int) $this->input->post('iddel');
                if ($id) {
                    $detail = $this->Category_model->getCategory($id);
                    if ($detail) {
                        $this->Category_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg', 'Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message', 'Tidak ada data.');
                    }
                }
            }
        } else {
            redirect('category');
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
        if ($post['category'] == '') {
            $err .= 'Mohon isi Nama Kategori.<br/>';
        }

        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}

/* End of file category.php */
/* Location: ./application/controllers/category.php */