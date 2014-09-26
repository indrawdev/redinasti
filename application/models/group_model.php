<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Group Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc Group Model
 */
class Group_model extends CI_Model
{
    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get admin group detail by id
     * @param int $id
     * @return array data
     */
    function getGroup($id) {
        $data = $this->db
                ->where('id_auth_group',$id)
                ->limit(1)
                ->get('auth_group')
                ->row_array();
        
        return $data;
    }
    
    /**
     * insert new record
     * @param array $param
     * @return int last inserted id
     */
    function InsertRecord($param) {
        $this->db->insert('auth_group',$param);
        $last_id = $this->db->insert_id();
        return $last_id;
    }
    
    /**
     * update record admin user group
     * @param int $id
     * @param array $param
     */
    function UpdateRecord($id,$param) {
        $this->db->where('id_auth_group',$id);
        $this->db->update('auth_group',$param);
    }
    
    /**
     * delete record
     * @param int $id
     */
    function DeleteRecord($id) {
        $this->db->where('id_auth_group',$id);
        $this->db->delete(array('auth_menu_group','auth_group'));
    }
    
    /**
     * update authorization menu group
     * @param int $id
     * @param array $param
     */
    function UpdateAuth($id,$param) {
        // delete data before insert new record
        $this->db->where('id_auth_group',$id);
        $this->db->delete('auth_menu_group');
        
        $data = false;
        foreach($param as $row => $val) {
            $data[] = " ('".$id."','".$val."') ";
        }
        if ($data) {
            $insert = implode(",", $data);
            $this->db->query("insert into ".$this->db->dbprefix('auth_menu_group')." (id_auth_group,id_auth_menu) values ".$insert." ");
        }
    }
    
    /**
     * get menu auth admin
     * @param int $id_parent
     * @return array data
     */
    function getMenus($id_parent=0) {
        if (is_superadmin()) {
            $data = $this->db
                    ->where('parent_auth_menu',$id_parent)
                    ->order_by('position','asc')
                    ->order_by('id_auth_menu','asc')
                    ->get('auth_menu')
                    ->result_array();
        } else {
            $data = $this->db
                    ->where('parent_auth_menu',$id_parent)
                    ->where('is_superadmin',0)
                    ->order_by('position','asc')
                    ->order_by('id_auth_menu','asc')
                    ->get('auth_menu')
                    ->result_array();
        }
        return $data;
    }
    
    /**
     * get auth menu group
     * @param int $id_group
     * @param int $id_menu
     * @return boolean
     */
    function getAuthMenuGroup($id_group,$id_menu)
    {
        $data = $this->db
                ->where('id_auth_group',$id_group)
                ->where('id_auth_menu',$id_menu)
                ->limit(1)
                ->get('auth_menu_group')
                ->row_array();
        if ($data)
        {
            return $data['id_auth_menu_group'];
        }
        else
        {
            return false;
        }
    }
    
    /**
     * print list of menu to checkbox
     * @param int $id_group
     * @param int $id_parent
     * @param string $prefix
     * @return string return
     */
    function printAuthMenuGroup($id_group,$id_parent=0, $prefix='')
    {
        $tmp_menu = '';
        $menus = $this->getMenus($id_parent);
        foreach ($menus as $menu) 
        {
            $checked = '';
            $tree = '';
            $divider = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $id_auth_menu_group = $this->getAuthMenuGroup($id_group,$menu["id_auth_menu"]);
            if ($id_auth_menu_group)
            {
                $checked = 'checked="checked"';
            }
            if ($id_parent != 0) {
                $tree = '&nbsp;&nbsp;<img src="'.IMG_URL.'tree-tax.png" class="tree-tax" alt="taxo"/>';
            }
            $tmp_menu .=  '<label class="checkbox" style="margin-top: 8px;">
                                <input type="checkbox" value="'.$menu["id_auth_menu"].'" '.$checked.'" id="menu-group-'.$menu["id_auth_menu"].'" name="auth_menu_group[]" class="checkauth">
                        '.$prefix.' '.$tree.' &nbsp;&nbsp;'.$menu["menu"].'</label>';

           $tmp_menu .=  $this->printAuthMenuGroup($id_group,$menu["id_auth_menu"], $prefix.$divider);
        }
        return $tmp_menu;
    }
    
}

/* End of file group_model.php */
/* Location: ./application/model/webcontrol/group_model.php */

