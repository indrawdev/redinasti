<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Giro Class
 * this class is for giro management
 * @author ivan lubis
 * @version 2.1
 * @giro Controller
 * @desc Giro Controller
 */
class Giro extends CI_Controller {

    //private $error = array();
    private $error = '';
    private $controller = 'giro';

    /**
     * Index Giro for this controller.
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
        $alias['search_giro'] = "giro_code";
        $alias['search_bank'] = "giro_bank";
        $alias['search_date'] = "giro_date";
        $alias['search_opt_giro_status'] = "giro_status";
        $query = "
            select 
                id_giro as id, 
                id_giro as idx, 
                " . $this->db->dbprefix('giro') . ".*
            from " . $this->db->dbprefix('giro') . "
            where 1";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }

    /**
     * add new record
     */
    public function add() {
        $this->load->model('Giro_model');
        $this->data['form_action'] = site_url($this->controller.'/add');
        $this->data['page_title'] = 'Tambah Giro';
        $this->data['cancel_url'] = site_url($this->controller);
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $last_id = $this->Giro_model->InsertNewRecord($post);
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
        $this->load->model('Giro_model');
        $this->data['form_action'] = site_url($this->controller.'/edit/' . $id);
        $this->data['page_title'] = 'Edit Giro';
        $this->data['cancel_url'] = site_url($this->controller);
        $id = (int) $id;
        if (!$id) {
            redirect($this->controller);
        }
        $detail = $this->Giro_model->getGiro($id);
        $this->data['cashed_url'] = site_url($this->controller.'/cashed_giro');
        $this->data['histories'] = $this->Giro_model->GiroHistory($detail['id_giro']);
        $this->data['post'] = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $this->Giro_model->UpdateRecord($id, $post);
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
     * cash giro
     */
    public function cashed_giro() {
        $this->layout = 'none';
        $json = array();
        if ($this->input->post() && is_ajax_requested()) {
            $this->load->model('Giro_model');
            $post = $this->input->post();
            if (!$post['giro_id'] || !ctype_digit($post['giro_id'])) {
                $json['error'] = alert_box('Gagal. Mohon refresh browser Anda.<br/>','error');
            }
            if (!$json) {
                $detail = $this->Giro_model->getGiro($post['giro_id']);
                if ($detail) {
                    $cashed['id_giro'] = $detail['id_giro'];
                    $cashed['giro_code'] = $detail['giro_code'];
                    $cashed['giro_price'] = $detail['giro_price'];
                    $this->Giro_model->CashedInGiro($cashed);
                    $update['giro_status'] = 2;
                    $this->Giro_model->UpdateRecord($detail['id_giro'],$update);
                    $json['success'] = alert_box('Berhasil.','success');
                }
            }
        }
        echo json_encode($json);
    }

    /**
     * delete record
     */
    public function delete() {
        $this->load->model('Giro_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int) $this->input->post('iddel');
                if ($id) {
                    $detail = $this->Giro_model->getGiro($id);
                    if ($detail) {
                        $this->Giro_model->DeleteRecord($id);
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
        if ($post['giro_code'] == '') {
            $err .= 'Mohon isi Bilyet Giro.<br/>';
        }
        if ($post['giro_bank'] == '') {
            $err .= 'Mohon isi Nama Bank.<br/>';
        }
        if ($post['giro_date'] == '') {
            $err .= 'Mohon isi Tanggal Giro.<br/>';
        }
        if ($post['giro_price'] == '') {
            $err .= 'Mohon isi Nominal Giro.<br/>';
        }

        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}

/* End of file giro.php */
/* Location: ./application/controllers/giro.php */