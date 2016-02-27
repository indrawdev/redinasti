<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Services Class
 * this class is for services management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Services Controller
 */
class Services extends CI_Controller
{
    //private $error = array();
    private $error = '';

    /**
     * Index Services for this controller.
     */
    public function index()
    {
        /**
         * let this function empty just for generating layout
         */
        $this->data['add_url'] = site_url('services/add');
        $this->data['export_excel_url'] = site_url('services/export_excel');
        $this->data['list_data'] = site_url('services/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_create_date'] = "DATE_FORMAT(CURRENT_TIMESTAMP, '%d  %b  %Y %H:%i:%S')";
        $alias['search_title'] = "c.title";
        $alias['search_status_text'] = "b.status_text";
        $query = "
            select 
                a.id_services as id, 
                a.id_services as idx, 
                DATE_FORMAT(CURRENT_TIMESTAMP, '%d  %b  %Y %H:%i:%S') as create_date,
                a.*,
                b.status_text,
                d.locale,
                c.title
            from " . $this->db->dbprefix('services') . " a 
            left join " . $this->db->dbprefix('status') . " b on a.id_status = b.id_status
            left join " . $this->db->dbprefix('services_detail') . " c on c.id_services=a.id_services
            left join ".$this->db->dbprefix('localization')." d on d.id_localization=c.id_localization
            where is_delete = 0 and d.locale_status=1";
        $group_by = "a.id_services";
        $this->data = query_grid($query, $alias,$group_by);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Services_model');
        $this->data['form_action'] = site_url('services/add');
        $post = array(
            //'alias'=>'',
            'uri_path'=>'',
            'publish_date'=>date('Y-m-d'),
            'id_status'=>'',
            'is_featured'=>false,
            'primary_image'=>'',
            'thumbnail_image'=>'',
        );
        $this->data['status_list'] = $this->Services_model->getStatus();
        $this->data['locales'] = $this->Services_model->getLocale();
        foreach($this->data['locales'] as $locale) {
            $post['content_locale'][$locale['id_localization']] = array(
                'title'=>'',
                'teaser'=>'',
                'description'=>'',
            );
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm()) {
                $post['uri_path'] = url_title($post['uri_path'], '-', true);
                if (isset($post['is_featured'])) {
                    $post['is_featured'] = 1;
                } else{
                    $post['is_featured'] = 0;
                }
                $last_id = $this->Services_model->InsertNewRecord($post);
                if ($last_id) {
                    $post_image = $_FILES;
                    if ($post_image['primary_image']['tmp_name']) {
                        $filename = 'pri_'.$post['uri_path'].md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['primary_image'], IMG_UPLOAD_DIR.'services/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                        
                        $this->Services_model->UpdateData($last_id,array('primary_image'=>$picture_db));
                    }
                    if ($post_image['thumbnail_image']['tmp_name']) {
                        $filename = 'thumb_'.$post['uri_path'].'_'.md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['thumbnail_image'], IMG_UPLOAD_DIR.'services/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                        
                        $this->Services_model->UpdateData($last_id,array('thumbnail_image'=>$picture_db));
                    }
                    $this->session->set_flashdata('success_msg','Succeed.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Failed.');
                }
                redirect('services');
            }
        }
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * edit new record
     */
    public function edit($id=0)
    {
        $this->load->model('Services_model');
        $this->data['form_action'] = site_url('services/edit/'.$id);
        $id = (int)$id;
        if (!$id) {
            redirect('services');
        }
        $this->data['status_list'] = $this->Services_model->getStatus();
        $this->data['locales'] = $this->Services_model->getLocale();
        $detail = $this->Services_model->getServices($id);
        $post = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                $post['uri_path'] = url_title($post['uri_path'], '-', true);
                if (isset($post['is_featured'])) {
                    $post['is_featured'] = 1;
                } else{
                    $post['is_featured'] = 0;
                }
                $this->Services_model->UpdateRecord($id,$post);
                $post_image = $_FILES;
                if ($post_image['primary_image']['tmp_name']) {
                    if ($detail['primary_image'] != '' && file_exists(IMG_UPLOAD_DIR.'services/'.$detail['primary_image'])) {
                        unlink(IMG_UPLOAD_DIR.'services/'.$detail['primary_image']);
                        unlink(IMG_UPLOAD_DIR.'services/tmb_'.$detail['primary_image']);
                        unlink(IMG_UPLOAD_DIR.'services/sml_'.$detail['primary_image']);
                    }
                    $filename = 'pri_'.$post['uri_path'].md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['primary_image'], IMG_UPLOAD_DIR.'services/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                    $this->Services_model->UpdateData($id,array('primary_image'=>$picture_db));
                }
                if ($post_image['thumbnail_image']['tmp_name']) {
                    if ($detail['thumbnail_image'] != '' && file_exists(IMG_UPLOAD_DIR.'services/'.$detail['thumbnail_image'])) {
                        unlink(IMG_UPLOAD_DIR.'services/'.$detail['thumbnail_image']);
                        unlink(IMG_UPLOAD_DIR.'services/tmb_'.$detail['thumbnail_image']);
                        unlink(IMG_UPLOAD_DIR.'services/sml_'.$detail['thumbnail_image']);
                    }
                    $filename = 'thumb_'.$post['uri_path'].'_'.md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['thumbnail_image'], IMG_UPLOAD_DIR.'services/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'services/'.$picture_db, IMG_UPLOAD_DIR.'services/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                    
                    $this->Services_model->UpdateData($id,array('thumbnail_image'=>$picture_db));
                }
                $this->session->set_flashdata('success_msg','Succeed.');
                
                redirect('services');
            }
        }
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * delete record
     */
    public function delete() {
        $this->load->model('Services_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Services_model->getServices($id);
                    if ($detail) {
                        $this->Services_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data has been deleted.');
                    } else {
                        $this->session->set_flashdata('message','There is no record in our database.');
                    }
                }
            }
        } else {
            redirect('services');
        }
    }

    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Services_model');
        $locales = $this->Services_model->getLocaleDefault();
        $post = $this->input->post();
        $post['uri_path'] = url_title($post['uri_path'], '-', true);
        $err = '';
        /*if ($post['alias'] =='') {
            $err .= 'Please insert Services Title.<br/>';
        }*/
        foreach($post['content_locale'] as $row => $val) {
            if ($row == $locales['id_localization'] && $val['title'] == '') {
                $err .= 'Please insert Content Title.<br/>';
            }
        }
        if ($post['uri_path'] == '') {
            $err .= 'Please insert SEO Link.<br/>';
        } else {
            if (!$this->Services_model->check_exists_path($post['uri_path'],$id)) {
                $err .= 'SEO Link already used.<br/>';
            }
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
/* End of file services.php */
/* Location: ./application/controllers/services.php */