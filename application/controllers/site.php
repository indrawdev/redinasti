<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Site Class
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Site Controller
 * 
 */
class Site extends CI_Controller
{
    //private $error = array();
    private $error = '';

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Index Page for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['list_data'] = site_url('site/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_sitename'] = "site_name";
        $query = "
            select 
                id_site as id, 
                id_site as idx, 
                " . $this->db->dbprefix('sites') . ".*
            from " . $this->db->dbprefix('sites') . "
            where is_delete='0'
        ";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    /**
     * edit page
     */
    public function edit($id=0)
    {
        $id = (int)$id;
        if (!$id) {
            redirect('site');
        }
        $this->load->model('Site_model');
        $this->data['page_title'] = 'Edit Site';
        $this->data['cancel_url'] = site_url('site');
        $this->data['form_action'] = site_url('site/edit/'.$id);
        $detail = $this->Site_model->getSite($id);
        if (!$detail) {
            redirect('site');
        }
        $post = $detail;
        $this->data['settings'] = $this->Site_model->getSetting($detail['id_site']);
        if ($this->input->post()) {
            $post = purify($this->input->post());
            if ($this->validateForm($id)) {
                $post['modify_date'] = date('Y-m-d H:i:s');
                $post['is_default'] = (isset($post['is_default'])) ? 1 : 0;
                
                // update data
                $this->Site_model->UpdateData($id,$post);
                $post_image = $_FILES;
                if ($post_image['site_logo']['tmp_name']) {
                    if ($detail['site_logo'] != '' && file_exists(IMG_UPLOAD_DIR.'site/'.$detail['site_logo'])) {
                        unlink(IMG_UPLOAD_DIR.'site/'.$detail['site_logo']);
                        unlink(IMG_UPLOAD_DIR.'site/tmb_'.$detail['site_logo']);
                        unlink(IMG_UPLOAD_DIR.'site/sml_'.$detail['site_logo']);
                    }
                    $filename = 'site_'.url_title($post['site_name'],'_',true).md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['site_logo'], IMG_UPLOAD_DIR.'site/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'site/'.$picture_db, IMG_UPLOAD_DIR.'site/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'site/'.$picture_db, IMG_UPLOAD_DIR.'site/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                    $this->Site_model->UpdateRecord($id,array('site_logo'=>$picture_db));
                }
                $this->session->set_flashdata('success_msg', 'Success.<br/>');

                redirect('site');
            }
        }
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * validate form
     * @return boolean
     */
    private function validateForm()
    {
        $post = purify($this->input->post());
        $err = '';
        if ($post['site_name'] == '') {
            $err .= 'Please insert Site Name.<br/>';
        }

        if ($post['site_url'] == '') {
            $err .= 'Please insert Site URL.<br/>';
        }
        
        $post_image = $_FILES;
        if (!empty($post_image['site_logo']['tmp_name'])) {
            $check_picture = validatePicture('site_logo');
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

/* End of file site.php */
/* Location: ./application/controllers/site.php */


