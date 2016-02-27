<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Localization Class
 * this class is for localization management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Localization Controller
 */
class Localization extends CI_Controller
{
    //private $error = array();
    private $error = '';

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('localization/add');
        $this->data['export_excel_url'] = site_url('localization/export_excel');
        $this->data['list_data'] = site_url('localization/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias = array();
        $query = "
            select 
                a.id_localization as id, 
                a.id_localization as idx, 
                a.*
            from " . $this->db->dbprefix('localization') . " a 
            where 1";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Localization_model');
        $this->data['form_action'] = site_url('localization/add');
        $post = array(
            'locale'=>'',
            'iso_1'=>'',
            'iso_2'=>'',
            'locale_path'=>'',
            'locale_status'=>false,
        );
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                if ($post['locale_path'] == '') {
                    $post['locale_path'] = url_title($post['locale'], '-', true);
                } else {
                    $post['locale_path'] = url_title($post['locale_path'], '-', true);
                }
                if (isset($post['locale_status'])) {
                    $post['locale_status'] = 1;
                } else{
                    $post['locale_status'] = 0;
                }
                $last_id = $this->Localization_model->InsertNewRecord($post);
                if ($last_id) {
                    $this->session->set_flashdata('success_msg','Succeed.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Failed.');
                }
                redirect('localization');
            }
        }
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * validation form
     * @param int $id
     * @return boolean
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Localization_model');
        $post = $this->input->post();
        if ($post['locale_path'] == '') {
            $post['locale_path'] = url_title($post['locale'], '-', true);
        } else {
            $post['locale_path'] = url_title($post['locale_path'], '-', true);
        }
        $err = '';
        if ($post['locale'] =='') {
            $err .= 'Please insert Locale.<br/>';
        }
        if ($post['iso_1'] =='') {
            $err .= 'Please insert ISO 2.<br/>';
        }
        if ($post['iso_2'] =='') {
            $err .= 'Please insert ISO 3.<br/>';
        }
        if (!$this->Localization_model->check_exists_path($post['locale_path'],$id)) {
            $err .= 'Path already used.<br/>';
        }
        if (!$this->Localization_model->check_default($id)) {
            $err .= 'There\'s other record is set to default. Please unset default that record first.<br/>';
        }
        
        if ($err) {
            $this->error = $err;
            return false;
        } else {
            return true;
        }
    }

}
/* End of file localization.php */
/* Location: ./application/controllers/localization.php */