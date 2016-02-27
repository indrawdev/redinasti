<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Pages Class
 * this class is for menu/page management
 * @author ivan lubis
 * @version 2.1
 * @category Controller
 * @desc Pages Controller
 */
class Pages extends CI_Controller
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
        $this->data['add_url'] = site_url('pages/add');
        $this->data['export_excel_url'] = site_url('pages/export_excel');
        $this->data['list_data'] = site_url('pages/list_data');
    }
    
    /**
     * list of data
     */
    public function list_data()
    {
        $alias['search_create_date'] = "DATE_FORMAT(CURRENT_TIMESTAMP, '%d  %b  %Y %H:%i:%S')";
        $alias['search_name'] = "a.page_name";
        $alias['search_parent_name'] = "c.page_name";
        $alias['search_status_text'] = "b.status_text";
        $query = "
            select 
                a.id_page as id, 
                a.id_page as idx, 
                DATE_FORMAT(CURRENT_TIMESTAMP, '%d  %b  %Y %H:%i:%S') as create_date,
                a.*,
                b.status_text,
                c.page_name as parent_name
            from " . $this->db->dbprefix('pages') . " a 
            left join " . $this->db->dbprefix('status') . " b on b.id_status = a.id_status
            left join " . $this->db->dbprefix('pages') . " c on c.id_page = a.parent_page
            where a.is_delete = 0";
        $this->data = query_grid($query, $alias);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * add new record
     */
    public function add()
    {
        $this->load->model('Pages_model');
        $this->data['form_action'] = site_url('pages/add');
        $post = array(
            'page_name'=>'',
            'parent_page'=>0,
            'page_type'=>1,
            'uri_path'=>'',
            'publish_date'=>date('Y-m-d'),
            'id_status'=>'',
            'is_header'=>true,
            'is_footer'=>false,
            'is_featured'=>false,
            'primary_image'=>'',
            'thumbnail_image'=>'',
            'ext_link'=>'',
            'module'=>'',
            'position'=>($this->Pages_model->getMaxPosition()+1),
        );
        $this->data['status_list'] = $this->Pages_model->getStatus();
        $this->data['locales'] = $this->Pages_model->getLocale();
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
                if ($post['uri_path'] == '') {
                    $post['uri_path'] = url_title($post['page_name'], '-', true);
                } else {
                    $post['uri_path'] = url_title($post['uri_path'], '-', true);
                }
                if (isset($post['is_featured'])) {
                    $post['is_featured'] = 1;
                } else{
                    $post['is_featured'] = 0;
                }
                if (isset($post['is_header'])) {
                    $post['is_header'] = 1;
                } else{
                    $post['is_header'] = 0;
                }
                if (isset($post['is_footer'])) {
                    $post['is_footer'] = 1;
                } else{
                    $post['is_footer'] = 0;
                }
                $last_id = $this->Pages_model->InsertNewRecord($post);
                if ($last_id) {
                    $post_image = $_FILES;
                    if ($post_image['primary_image']['tmp_name']) {
                        $filename = 'pri_'.$post['uri_path'].md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['primary_image'], IMG_UPLOAD_DIR.'pages/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                        
                        $this->Pages_model->UpdateData($last_id,array('primary_image'=>$picture_db));
                    }
                    if ($post_image['thumbnail_image']['tmp_name']) {
                        $filename = 'thumb_'.$post['uri_path'].'_'.md5plus($last_id);
                        $picture_db = file_copy_to_folder($post_image['thumbnail_image'], IMG_UPLOAD_DIR.'pages/', $filename);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                        copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                        
                        $this->Pages_model->UpdateData($last_id,array('thumbnail_image'=>$picture_db));
                    }
                    $this->session->set_flashdata('success_msg','Succeed.');
                } else {
                    $this->session->set_flashdata('tmp_msg','Failed.');
                }
                redirect('pages');
            }
        }
        $this->data['parent_option'] = $this->Pages_model->getParentSelect(0,'',$post['parent_page']);
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * add new record
     */
    public function edit($id=0)
    {
        $this->load->model('Pages_model');
        $this->data['form_action'] = site_url('pages/edit/'.$id);
        $id = (int)$id;
        if (!$id) {
            redirect('pages');
        }
        $this->data['status_list'] = $this->Pages_model->getStatus();
        $this->data['locales'] = $this->Pages_model->getLocale();
        $detail = $this->Pages_model->getPage($id);
        $post = $detail;
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateForm($id)) {
                if ($post['uri_path'] == '') {
                    $post['uri_path'] = url_title($post['page_name'], '-', true);
                } else {
                    $post['uri_path'] = url_title($post['uri_path'], '-', true);
                }
                if (isset($post['is_featured'])) {
                    $post['is_featured'] = 1;
                } else{
                    $post['is_featured'] = 0;
                }
                $this->Pages_model->UpdateRecord($id,$post);
                $post_image = $_FILES;
                if ($post_image['primary_image']['tmp_name']) {
                    if ($detail['primary_image'] != '' && file_exists(IMG_UPLOAD_DIR.'pages/'.$detail['primary_image'])) {
                        unlink(IMG_UPLOAD_DIR.'pages/'.$detail['primary_image']);
                        unlink(IMG_UPLOAD_DIR.'pages/tmb_'.$detail['primary_image']);
                        unlink(IMG_UPLOAD_DIR.'pages/sml_'.$detail['primary_image']);
                    }
                    $filename = 'pri_'.$post['uri_path'].md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['primary_image'], IMG_UPLOAD_DIR.'pages/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);

                    $this->Pages_model->UpdateData($id,array('primary_image'=>$picture_db));
                }
                if ($post_image['thumbnail_image']['tmp_name']) {
                    if ($detail['thumbnail_image'] != '' && file_exists(IMG_UPLOAD_DIR.'pages/'.$detail['thumbnail_image'])) {
                        unlink(IMG_UPLOAD_DIR.'pages/'.$detail['thumbnail_image']);
                        unlink(IMG_UPLOAD_DIR.'pages/tmb_'.$detail['thumbnail_image']);
                        unlink(IMG_UPLOAD_DIR.'pages/sml_'.$detail['thumbnail_image']);
                    }
                    $filename = 'thumb_'.$post['uri_path'].'_'.md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['thumbnail_image'], IMG_UPLOAD_DIR.'pages/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                    
                    $this->Pages_model->UpdateData($id,array('thumbnail_image'=>$picture_db));
                }
                $this->session->set_flashdata('success_msg','Succeed.');
                
                redirect('pages');
            }
        }
        $this->data['parent_option'] = $this->Pages_model->getParentSelect(0,'',$post['parent_page'],$detail['id_page']);
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * gallery page
     * @param int $id
     */
    public function gallery($id=0) {
        $this->load->model('Pages_model');
        $this->data['form_action'] = site_url('pages/gallery/'.$id);
        $id = (int)$id;
        if (!$id) {
            redirect('pages');
        }
        $this->data['cancel_url'] = site_url('pages');
        $this->data['list_data'] = site_url('pages/list_gallery/'.$id);
        $detail = $this->Pages_model->getPage($id);
        $post = $detail;
        $this->data['locales'] = $this->Pages_model->getLocale();
        foreach($this->data['locales'] as $locale) {
            $post['content_locale'][$locale['id_localization']] = array(
                'caption'=>'',
            );
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($this->validateFormGallery($id)) {
                $uri_path = $detail['uri_path'];
                $post_image = $_FILES;
                if ($post_image['image']['tmp_name']) {
                    $total_gallery = $this->Pages_model->countGallery($id);
                    $filename = 'gal_'.($total_gallery+1).'_'.$uri_path.md5plus($id);
                    $picture_db = file_copy_to_folder($post_image['image'], IMG_UPLOAD_DIR.'pages/', $filename);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'tmb_'.$filename, IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT);
                    copy_image_resize_to_folder(IMG_UPLOAD_DIR.'pages/'.$picture_db, IMG_UPLOAD_DIR.'pages/', 'sml_'.$filename, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
                    $post['id_page'] = $id;
                    $post['image'] = $picture_db;
                    $id_insert = $this->Pages_model->InsertDataImage($post);
                    if ($id_insert) {
                        $this->session->set_flashdata('success_msg','Succeed.');
                    } else {
                        $this->session->set_flashdata('tmp_msg','Failed.');
                    }
                }
                redirect('pages/gallery/'.$id);
            }
        }
        $this->data['post'] = $post;
        if ($this->error) {
            $this->data['message'] = alert_box($this->error,'error');
        }
    }
    
    /**
     * list of gallery
     */
    public function list_gallery($id=0)
    {
        $alias = array();
        $query = "
            select 
                a.id_page_image as id, 
                a.id_page_image as idx, 
                DATE_FORMAT(CURRENT_TIMESTAMP, '%d  %b  %Y %H:%i:%S') as create_date,
                a.*,
                b.caption,
                c.locale
            from " . $this->db->dbprefix('pages_image') . " a 
            left join " . $this->db->dbprefix('pages_image_caption') . " b on b.id_page_image=a.id_page_image 
            left join " . $this->db->dbprefix('localization') . " c on c.id_localization=b.id_localization
            where a.id_page='".(int)$id."' and c.locale_status=1
            ";
        $group_by = "a.id_page_image";
        $this->data = query_grid($query, $alias, $group_by);
        $this->data['paging'] = paging($this->data['total']);
    }
    
    /**
     * delete record
     * @param int $id
     */
    public function delete($id=0) {
        $this->load->model('Pages_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Pages_model->getPage($id);
                    if ($detail) {
                        $this->Pages_model->DeleteRecord($id);
                        $this->session->set_flashdata('success_msg','Data has been deleted.');
                    } else {
                        $this->session->set_flashdata('message','There is no record in our database.');
                    }
                }
            }
        } else {
            redirect('pages');
        }
    }
    
    /**
     * delete gallery record
     */
    public function delete_gallery() {
        $this->load->model('Pages_model');
        if (is_ajax_requested()) {
            $this->layout = 'none';
            if ($this->input->post()) {
                $id = (int)$this->input->post('iddel');
                if ($id) {
                    $detail = $this->Pages_model->getPagesGallery($id);
                    if ($detail) {
                        $this->Pages_model->DeleteRecordGallery($id);
                        $this->session->set_flashdata('success_msg','Data has been deleted.');
                    } else {
                        $this->session->set_flashdata('message','There is no record in our database.');
                    }
                }
            }
        } else {
            redirect('pages');
        }
    }


    /**
     * form validation
     * @param int $id
     * @return boolean true/false
     */
    private function validateForm($id=0) {
        $id = (int)$id;
        $this->load->model('Pages_model');
        $locales = $this->Pages_model->getLocaleDefault();
        $post = $this->input->post();
        if ($post['uri_path'] == '') {
            $post['uri_path'] = url_title($post['page_name'], '-', true);
        } else {
            $post['uri_path'] = url_title($post['uri_path'], '-', true);
        }
        $err = '';
        if ($post['page_name'] == '') {
            $err .= 'Please insert Page Name.<br/>';
        }
        if ($post['page_type'] == 2) {
            if ($post['module'] == '') {
                $err .= 'Please input Module.<br/>';
            }
        } elseif ($post['page_type'] == 3) {
            if ($post['ext_link'] == '') {
                $err .= 'Please input External Link.<br/>';
            }
        } else {
            foreach ($post['content_locale'] as $row => $val) {
                if ($row == $locales['id_localization'] && $val['title'] == '') {
                    $err .= 'Please insert Content Title.<br/>';
                }
            }
        
            if ($post['uri_path'] == '') {
                $err .= 'Please insert SEO Link.<br/>';
            } else {
                if (!$this->Pages_model->check_exists_path($post['uri_path'],$id)) {
                    $err .= 'SEO Link already used.<br/>';
                }
            }
        }
        if ($post['publish_date'] == '') {
            $err .= 'Please set Publish Date.<br/>';
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
    
    /**
     * validate gallery form
     * @param int $id
     * @return boolean
     */
    private function validateFormGallery($id=0) {
        $id = (int)$id;
        $post = $this->input->post();
        $post_file = $_FILES;
        $err = '';
        if (!$id) {
            redirect('pages');
        }
        if ($post_file['image']['tmp_name'] =='') {
            $err .= 'Please insert Image.<br/>';
        } else {
            $check_picture = validatePicture('image');
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
/* End of file pages.php */
/* Location: ./application/controllers/pages.php */