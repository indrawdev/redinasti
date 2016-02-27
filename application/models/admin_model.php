<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Admin Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc Admin Model
 */
class Admin_model extends CI_Model
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get admin user detail by id
     * @param int $id
     * @return array data
     */
    function getAdmin($id) {
        $data = $this->db
                ->where('id_auth_user',$id)
                ->limit(1)
                ->get('auth_user')
                ->row_array();
        
        return $data;
    }
    
    /**
     * get all division
     * @return array $data
     */
    function getDivisions() {
        $data = $this->db
                ->where('is_delete',0)
                ->order_by('division')
                ->get('division')
                ->result_array();
        
        return $data;
    }
    
    /**
     * get all admin group
     * @return array data
     */
    function getAdminGroup() {
        if (is_superadmin()) {
            $data = $this->db
                    ->order_by('auth_group','asc')
                    ->get('auth_group')
                    ->result_array();
        } else {
            $data = $this->db
                    ->where('is_superadmin',0)
                    ->order_by('auth_group','asc')
                    ->get('auth_group')
                    ->result_array();
        }
        
        return $data;
    }
    
    /**
     * insert new record
     * @param array $param
     * @return int last inserted id
     */
    function InsertRecord($param) {
        $this->db->insert('auth_user',$param);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update record admin user
     * @param int $id
     * @param array $param
     */
    function UpdateRecord($id,$param) {
        $this->db->where('id_auth_user',$id);
        $this->db->update('auth_user',$param);
    }
    
    /**
     * check exist email
     * @param string $email
     * @param int $id
     * @return boolean true/false 
     */
    function checkExistsEmail($email,$id=0) {
        if ($id != '' && $id != 0) {
            $this->db->where('id_auth_user !=',$id);
        }
        $this->db->where('LCASE(email)',strtolower($email));
        $query = $this->db->get('auth_user');
        if ($query->num_rows()>0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     * check exist username
     * @param string $username
     * @param int $id
     * @return boolean true/false 
     */
    function checkExistsUsername($username,$id=0) {
        if ($id != '' && $id != 0) {
            $this->db->where('id_auth_user !=',$id);
        }
        $this->db->where('username',$username);
        $query = $this->db->get('auth_user');
        if ($query->num_rows()>0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * count all user
     * @param type $search1
     * @param type $search2
     * @param type $search3
     * @return type integer number of user
     */
    function getTotalUser($search1=null,$search2=null,$search3=null)
    {
        $this->db->select('count(*) as total');
        if($search1 != null || $search1 != '') {
            $this->db->like('CAST(BASE64_DECODE(name) AS CHAR(10000) CHARACTER SET utf8)',$search1);
        }
        if($search2 != null || $search2 != '') {
            $this->db->like('CAST(BASE64_DECODE(email) AS CHAR(10000) CHARACTER SET utf8)',$search2);
        }
        if($search3 != null) $this->db->where('activation_status', $search3);
        $this->db->where('is_delete',0);
        $query = $this->db->get('users');
        if ($query->num_rows()>0)
        {
            $row = $query->row_array();
            return $row['total'];
        }
        else
        {
            return '0';
        }
    }
    
    /**
     * get all user
     * @param type $search1
     * @param type $search2
     * @param type $search3
     * @param type $limit
     * @param type $perpage
     * @return type string $query
     */
    function GetAllUser($search1=null,$search2=null,$search3=null,$limit=0,$perpage=0)
    {
        if($search1 != null || $search1 != '') {
            $this->db->like('CAST(BASE64_DECODE(name) AS CHAR(10000) CHARACTER SET utf8)',$search1);
        }
        if($search2 != null || $search2 != '') {
            $this->db->like('CAST(BASE64_DECODE(email) AS CHAR(10000) CHARACTER SET utf8)',$search2);
        }
        if($search3 != null) $this->db->where('activation_status', $search3);
        if ($perpage > 0)  $this->db->limit($perpage,$limit);
        $this->db->where('is_delete',0);
        $this->db->order_by('id_user', 'desc');
        $query = $this->db->get('users');

        return $query;
    }

    /**
     * get user by user id
     * @param type $Id
     * @return type string $query
     */
    function GetUserByID($Id=0)
    {
        $this->db->where('id_user', $Id);
        $this->db->where('is_delete',0);
        $this->db->limit(1);
        $this->db->order_by('id_user', 'desc');
        $query = $this->db->get('users');

        return $query;
    }

    /**
     *update user by user id
     * @param type $Id
     * @param type $data 
     */
    function UpdateUser($Id,$data)
    {
        $this->db->where('id_user',$Id);
        $this->db->update('users',$data);
    }

    /**
     * insert user and returning last inserted id
     * @param type $data
     * @return type integer $id_user last inserted id
     */
    function InsertUser($data)
    {
        $this->db->insert('users',$data);
        $id_user = $this->db->insert_id();
        return $id_user;
    }

    /**
     * delete user by user id 
     * //(set status to deleted, but not delete data just incase)
     * @param type $Id 
     */
    function DeleteUser($Id)
    {
        $query = $this->GetUserByID($Id);
        if ($query->num_rows()>0)
        {
            $row = $query->row_array();
            
            $data = array('is_delete'=>1);
            $this->db->where('id_user',$row['id_user']);
            $this->db->update('users',$data);
            
            /**
            // delete store
            // if user have store
            $this->db->where('id_user',$row['id_user']);
            $stores = $this->db->get('stores');
            // if user have store
            if ($stores->num_rows()>0) {
                foreach($stores->result_array() as $store) {
                    $this->db->where('id_store',$store['id_store']);
                    $items = $this->db->get('items');
                    // if user have items
                    if ($items->num_rows()>0) {
                        foreach($items->result_array() as $item) {
                            $this->db->where('id_item',$item['id_item']);
                            
                            // delete item picture
                            if ($item['primary_image'] != '' && file_exists('./uploads/store/item/'.$item['primary_image'])) {
                                @unlink('./uploads/store/item/'.$item['primary_image']);
                            }
                            if ($item['primary_thumb'] != '' && file_exists('./uploads/store/item/'.$item['primary_thumb'])) {
                                @unlink('./uploads/store/item/'.$item['primary_thumb']);
                            }
                            
                            // delete item comment db
                            //$this->db->where('id_item',$item['id_item']);
                            //$this->db->delete('items_comments');
                            
                            // delete categories
                            $this->db->where('id_item',$item['id_item']);
                            $this->db->delete('items_categories');
                        }
                        // delete item db user
                        $this->db->where('id_store',$store['id_store']);
                        $this->db->delete('items');
                        
                        // delete item comment by id user
                        //$this->db->where('id_user',$row['id_user']);
                        //$this->db->delete('items_comments');
                        
                    }
                    
                    // delete store picture
                    if ($store['logo'] != '' && file_exists('./uploads/store/'.$store['logo'])) {
                        @unlink('./uploads/store/'.$store['logo']);
                    }
                    if ($store['logo_thumbnail'] != '' && file_exists('./uploads/store/'.$store['logo_thumbnail'])) {
                        @unlink('./uploads/store/'.$store['logo_thumbnail']);
                    }
                    
                    // delete store feedback
                    //$this->db->where('id_store',$store['id_store']);
                    //$this->db->delete('stores_feedbacks');
                
                    // delete store promote
                    //$this->db->where('id_store',$store['id_store']);
                    //$this->db->delete('stores_promotes');
                }
                
                // delete feedback by user id
                //$this->db->where('id_user',$row['id_user']);
                //$this->db->delete('stores_feedbacks');
                
                // delete store db user
                $this->db->where('id_user',$row['id_user']);
                $this->db->delete('stores');
            }          
            
            // delete user pic
            $username = strtolower($row['username']);
            $this->DeletePictureByID($row['id_user']);
            $path = './uploads/user/';
            if (is_dir($path.$username)) {
                remove_module_directory($path.$username);
            }
            
            $this->db->where('id_user',$row['id_user']);
            $this->db->delete('users');
             * 
             */
        }
    }

    /**
     * delete picture by user id
     * @param type $id
     * @param type $type 
     */
    function DeletePictureByID($id)
    {
        $data = array();
        $query = $this->GetUserByID($id);

        $path = './uploads/user/';

        if ($query->num_rows() > 0)
        {
            $row = $query->row_array();
            $username = strtolower($row['username']);
            if ($row['image'] != '' && file_exists($path.$username.'/'.$row['image']))
            {
                @unlink($path.$username.'/'.$row['image']);
                @unlink($path.$username.'/'.$row['image'].'-thumb2');
            }
            if ($row['thumbnail'] != '' && file_exists($path.$username.'/'.$row['thumbnail']))
            {
                @unlink($path.$username.'/'.$row['thumbnail']);
            }
                
            $data = array('image'=>'','thumbnail'=>'');
            
            
            
            $this->UpdateUser($row['id_user'],$data);
        }
    }

    /**
     * change blog publish status
     * @param type $Id
     * @return type string publish status
     */
    function ChangeStatus($Id)
    {
        $this->db->where('id_user',$Id);
        $this->db->where('is_delete',0);
        $query = $this->db->get('users');
        if ($query->num_rows()>0)
        {
            $row = $query->row_array();
            if ($row['activation_status'] == 1) $val = 0;
            else $val = 1;

            $this->db->where('id_user',$row['id_user']);
            $this->db->update('users', array('activation_status'=>$val));

            if ($val == 1) return 'Active';
            else return 'Not Active';
        }
    }
    
    /**
     * get user point
     * @param int $id_user
     * @return int $return user point
     */
    function getUserPointById($id_user) {
        $return = '0';
        $this->db->select('sum(user_point) as total_point');
        $this->db->group_by('id_user');
        $this->db->where('id_user',$id_user);
        $this->db->limit(1);
        $query = $this->db->get('users_points');
        if ($query->num_rows()>0) {
            $row = $query->row_array();
            $return = $row['total_point'];
        }
        return $return;
    }
    
    /**
     * get option list of province
     * @param string $selected
     * @return string
     */
    function getProvinceOption($selected='') {
        $return = '';
        $this->db->distinct();
        $this->db->select('provinsi');
        $this->db->order_by('provinsi');
        $query = $this->db->get('kodepos');
        if ($query->num_rows()>0) {
            $selected = myUrlDecode($selected);
            foreach($query->result_array() as $row) {
                if ($selected == $row['provinsi'] && $selected != '') {
                    $return .= '<option value="'.myUrlEncode($row['provinsi']).'" selected="selected">'.$row['provinsi'].'</option>';
                } else {
                    $return .= '<option value="'.myUrlEncode($row['provinsi']).'">'.$row['provinsi'].'</option>';
                }
            }
        }
        return $return;
    }
    
    /**
     * 
     * @param string $province
     * @return string $query
     */
    function getKabupatenList($province='') {
        $this->db->distinct();
        $this->db->select('kabupaten');
        $this->db->order_by('kabupaten');
        if ($province) {
            $this->db->where('LCASE(provinsi)',strtolower($province));
        }
        $query = $this->db->get('kodepos');
        return $query;
    }
    
    /**
     * 
     * @param string $kabupaten
     * @return string $query
     */
    function getKecamatanList($province='',$kabupaten='') {
        $this->db->distinct();
        $this->db->select('kecamatan');
        $this->db->order_by('kecamatan');
        if ($province) {
            $this->db->where('LCASE(provinsi)',strtolower($province));
        }
        if ($kabupaten) {
            $this->db->where('LCASE(kabupaten)',strtolower($kabupaten));
        }
        $query = $this->db->get('kodepos');
        return $query;
    }
    
    /**
     * 
     * @param string $kecamatan
     * @return string $query
     */
    function getKelurahanList($province='',$kabupaten='',$kecamatan='') {
        $this->db->distinct();
        $this->db->select('kelurahan');
        $this->db->order_by('kelurahan');
        if ($province) {
            $this->db->where('LCASE(provinsi)',strtolower($province));
        }
        if ($kabupaten) {
            $this->db->where('LCASE(kabupaten)',strtolower($kabupaten));
        }
        if ($kecamatan) {
            $this->db->where('LCASE(kecamatan)',strtolower($kecamatan));
        }
        $query = $this->db->get('kodepos');
        return $query;
    }
    
    /**
     * 
     * @param string $kelurahan
     * @return string $query
     */
    function getKelurahanKodepos($province='',$kabupaten='',$kecamatan='',$kelurahan='') {
        $this->db->distinct();
        $this->db->select('kodepos');
        $this->db->order_by('id_kodepos','desc');
        $this->db->where('LCASE(kelurahan)',strtolower($kelurahan));
        if ($province) {
            $this->db->where('LCASE(provinsi)',strtolower($province));
        }
        if ($kabupaten) {
            $this->db->where('LCASE(kabupaten)',strtolower($kabupaten));
        }
        if ($kecamatan) {
            $this->db->where('LCASE(kecamatan)',strtolower($kecamatan));
        }
        $this->db->limit(1);
        $query = $this->db->get('kodepos');
        
        return $query;
    }
    
    /**
     * this function is use for installation module
     * @return type boolean return true
     */
    function install()
    {
        /*
        $create_tbl_user = "
            CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('users')." (
                `id_user` int(11) NOT NULL AUTO_INCREMENT,
                `id_ref_publish` int(11) NOT NULL,
                `id_template` int(11) NOT NULL,
                `publish_date` date NOT NULL,
                `title` varchar(255) COLLATE utf8_bin NOT NULL,
                `intro` text COLLATE utf8_bin NOT NULL,
                `content` text COLLATE utf8_bin NOT NULL,
                `title_in` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                `intro_in` text COLLATE utf8_bin,
                `content_in` text COLLATE utf8_bin,
                `picture_thumbnail` varchar(255) COLLATE utf8_bin NOT NULL,
                `picture_content` varchar(255) COLLATE utf8_bin NOT NULL,
                `picture_landing` varchar(255) COLLATE utf8_bin NOT NULL,
                `urut` int(11) NOT NULL,
                `menu_path` varchar(255) COLLATE utf8_bin NOT NULL,
                `is_delete` tinyint(4) NOT NULL DEFAULT '0',
                `is_landing` tinyint(4) NOT NULL DEFAULT '0',
                `is_must_read` tinyint(4) NOT NULL DEFAULT '0',
                `created_by` int(11) NOT NULL DEFAULT '0',
                `edited_by` int(11) NOT NULL DEFAULT '0',
                `tags` varchar(255) COLLATE utf8_bin NOT NULL,
                `viewed` int(11) NOT NULL,
                `modify_date` datetime NOT NULL,
                `create_date` datetime NOT NULL,
                PRIMARY KEY (`id_user`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
            ";
        $this->db->query($create_tbl_user);
        
        $create_tbl_user_comments = "
            CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('users_comments')." (
                `id_user_comment` int(11) NOT NULL AUTO_INCREMENT,
                `id_user` int(11) NOT NULL,
                `id_user` int(11) NOT NULL,
                `id_ref_publish` int(11) NOT NULL,
                `ip_address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                `comment` text COLLATE utf8_bin NOT NULL,
                `is_delete` tinyint(4) NOT NULL DEFAULT '0',
                `create_date` datetime NOT NULL,
                PRIMARY KEY (`id_user_comment`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
            ";
        $this->db->query($create_tbl_user_comments);
        
        $create_tbl_user_galleries = "
            CREATE TABLE IF NOT EXISTS ".$this->db->dbprefix('users_galleries')." (
                `id_user_gallery` int(11) NOT NULL AUTO_INCREMENT,
                `id_user` int(11) NOT NULL,
                `file_gallery` varchar(255) COLLATE utf8_bin NOT NULL,
                `caption_gallery` text COLLATE utf8_bin NOT NULL,
                `create_date_gallery` datetime NOT NULL,
                PRIMARY KEY (`id_user_gallery`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
            ";
        $this->db->query($create_tbl_user_galleries);
        
        $create_tbl_user_sites = "
            CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('users_sites')."` (
              `id_user` int(11) NOT NULL,
              `id_site` int(11) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id_user`,`id_site`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
            ";
        $this->db->query($create_tbl_user_sites);
        
        $create_tbl_user_widget = "
            CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('users_widget')."` (
                `id_user_widget` int(11) NOT NULL AUTO_INCREMENT,
                `id_user` int(11) NOT NULL,
                `id_widget` int(11) NOT NULL,
                PRIMARY KEY (`id_user_widget`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;
            ";
        $this->db->query($create_tbl_user_widget);
        */
        // get module
        $this->db->where('module','user');
        $this->db->where('module_link','user');
        $this->db->limit(1);
        $this->db->order_by('id_module','desc');
        $query = $this->db->get('modules');
        if ($query->num_rows()>0) {
            $row = $query->row_array();
            $module = $row['module'];
            $module_name = $row['module_title'];
        } else {
            $module = 'user';
            $module_name = 'User Management';
        }
        
        // get last urut
        $this->db->select('max(urut) as urut');
        $query2 = $this->db->get('menu_admin');
        $row2 = $query2->row_array();
        $last_urut = $row2['urut'] + 1;
        
        // add menu admin
        $this->db->insert('menu_admin',array('id_parents_menu_admin'=>0,'urut'=>$last_urut,'menu'=>$module_name,'file'=>$module));
        $id_menu_admin = $this->db->insert_id();
        
        // add auth
        $this->db->insert('auth_pages',array('id_auth_user_grup'=>adm_sess_usergroupid(),'id_menu_admin'=>$id_menu_admin));
        
        //mkdir("./uploads/user", 0755);
        
        return true;
    }
    
    /**
     * this function is use for un-installation or delete module user
     * @return type boolean true
     */
    function uninstall()
    {
        /*
        // delete auth
        $this->db->where('file','user');
        $this->db->limit(1);
        $query = $this->db->get('menu_admin');
        if ($query->num_rows()>0) {
            $row = $query->row_array();
            
            // delete auth
            $this->db->where('id_menu_admin',$row['id_menu_admin']);
            $this->db->delete('auth_pages');
            
            // delete menu admin
            $this->db->where('id_menu_admin',$row['id_menu_admin']);
            $this->db->delete('menu_admin');
        }
        
        $this->db->query("DROP TABLE 
            `".$this->db->dbprefix('users_widget')."`,
            `".$this->db->dbprefix('users_sites')."`,
            `".$this->db->dbprefix('users_galleries')."`,
            `".$this->db->dbprefix('users_comments')."`,
            `".$this->db->dbprefix('users')."`
        ");
        */
        
        //rmdir("./uploads/user");
        
        return true;
    }
}

/* End of file admin_model.php */
/* Location: ./application/model/webcontrol/admin_model.php */

