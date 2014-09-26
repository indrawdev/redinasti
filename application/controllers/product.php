<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Product Class
 * this class is for product management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Product Controller
 */
class Product extends CI_Controller {

    //private $error = array();
    private $error = '';

    /**
     * Index Product for this controller.
     */
    public function index() {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('product/add');
        $this->data['export_excel_url'] = site_url('product/export_excel');
        $this->data['list_data'] = site_url('product/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_product_name'] = "a.product_name";
        $alias['search_product_code'] = "a.product_code";
        $alias['search_category'] = "b.category";
        $query = "
            select 
                id_product as id, 
                id_product as idx, 
                a.*,
                b.category
            from " . $this->db->dbprefix('product') . " a
            left join " . $this->db->dbprefix('category') . " b on b.id_category=a.id_category
            where a.is_delete=0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Product_model');
        $this->data['form_action'] = site_url('product/add');
        $this->data['cancel_url'] = site_url('product');
        $this->data['page_title'] = 'Tambah Produk';
        $this->data['categories'] = $this->Product_model->getCategories();
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $last_id = $this->Product_model->InsertData($post);
                if ($last_id) {
                    $post_image = $_FILES;
                    if ($post_image['primary_image']['tmp_name']) {
                        $filename = 'prod_'.url_title($post['product_name'].'-'.$post['product_code'],'_',true).md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['primary_image'], IMG_UPLOAD_DIR.'product/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                        $this->Product_model->UpdateData($last_id,array('primary_image'=>$picture_db));
                    }
                    if ($post_image['thumbnail_image']['tmp_name']) {
                        $filename = 'thumb_'.url_title($post['product_name'].'-'.$post['product_code'],'_',true).md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['thumbnail_image'], IMG_UPLOAD_DIR.'product/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                        $this->Product_model->UpdateData($last_id,array('thumbnail_image'=>$picture_db));
                    }
                    $this->session->set_flashdata('success_msg','Data berhasil ditambah.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Gagal. Mohon coba lagi.');
                }
                redirect('product');
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
        $this->load->model('Product_model');
        $this->data['form_action'] = site_url('product/edit/'.$id);
        $this->data['cancel_url'] = site_url('product');
        $this->data['page_title'] = 'Edit Produk';
        $id = (int)$id;
        if (!$id) {
            redirect('product');
        }
        $detail = $this->Product_model->getProduct($id);
        $this->data['categories'] = $this->Product_model->getCategories();
        $this->data['suppliers'] = $this->Product_model->getSupplierProduct($detail['id_product']);
        $this->data['post'] = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $this->Product_model->UpdateData($id,$post);
                $post_image = $_FILES;
                if ($post_image['primary_image']['tmp_name']) {
                    if ($detail['primary_image'] != '' && file_exists(IMG_UPLOAD_DIR.'product/'.$detail['primary_image'])) {
                        unlink(IMG_UPLOAD_DIR.'product/'.$detail['primary_image']);
                        unlink(IMG_UPLOAD_DIR.'product/tmb_'.$detail['primary_image']);
                        unlink(IMG_UPLOAD_DIR.'product/sml_'.$detail['primary_image']);
                    }
                    $filename = 'prod_'.url_title($post['product_name'].'-'.$post['product_code'],'_',true).md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['primary_image'], IMG_UPLOAD_DIR.'product/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                    $this->Product_model->UpdateData($id,array('primary_image'=>$picture_db));
                }
                if ($post_image['thumbnail_image']['tmp_name']) {
                    if ($detail['thumbnail_image'] != '' && file_exists(IMG_UPLOAD_DIR.'product/'.$detail['thumbnail_image'])) {
                        unlink(IMG_UPLOAD_DIR.'product/'.$detail['thumbnail_image']);
                        unlink(IMG_UPLOAD_DIR.'product/tmb_'.$detail['thumbnail_image']);
                        unlink(IMG_UPLOAD_DIR.'product/sml_'.$detail['thumbnail_image']);
                    }
                    $filename = 'thumb_'.url_title($post['product_name'].'-'.$post['product_code'],'_',true).md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['thumbnail_image'], IMG_UPLOAD_DIR.'product/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'product/'.$picture_db, IMG_UPLOAD_DIR.'product/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                    $this->Product_model->UpdateData($id,array('thumbnail_image'=>$picture_db));
                }
                $this->session->set_flashdata('success_msg','Edit data berhasil.');
                redirect('product');
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
        $this->load->model('Product_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Product_model->getProduct($id);
                    if ($detail) {
                        $this->Product_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data berhasil dihapus.');
                    } else {
                        $this->session->set_flashdata('message','Data tidak ditemukan.');
                    }
                }
            }
        } else {
            redirect('product');
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Product_model');
        $post = $this->input->post();
        $err = '';
        if ($post['product_name'] == '') {
            $err .= 'Mohon isi Nama Produk.<br/>';
        }
        if ($post['product_code'] == '') {
            $err .= 'Mohon isi Kode Produk.<br/>';
        } else {
            if (!$this->Product_model->check_exists_code_pref($post['product_code'],'code',$id)) {
                $err .= 'Kode Produk telah digunakan. Mohon isi yang lain.<br/>';
            }
        }
        if ($post['id_category'] == '') {
            $err .= 'Mohon isi Kategori.<br/>';
        }
        if ($post['product_unit'] == '') {
            $err .= 'Mohon isi Satuan Produk.<br/>';
        }
        
        $post_image = $_FILES;
        if (!empty($post_image['primary_image']['tmp_name'])) {
            $check_picture = validatePicture('primary_image');
            if (!empty($check_picture)) {
                $err .= $check_picture.'<br/>';
            }
        }
        if (!empty($post_image['thumbnail_image']['tmp_name'])) {
            $check_picture = validatePicture('thumbnail_image');
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

/* End of file product.php */
/* Location: ./application/controllers/product.php */