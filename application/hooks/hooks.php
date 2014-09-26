<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ddi_hook {
    
    /**
     * check admin session authorization, return true or false 
     * @author ivan lubis
     * @return redirect to cms login page if not valid
     */
    function custom_auth() {
        $CI=& get_instance();

        // make exception auth for login
        $segment_1 = $CI->uri->segment(1);
        $segment_2 = $CI->uri->segment(2);
        if ($segment_1 == 'login') {
            if ($CI->session->userdata('ADM_SESS')!='') {
                redirect('home');
            } else {
                return;
            }
        } else {
            if ($segment_1 == 'logout') {
                return;
            }
            if($CI->session->userdata('ADM_SESS')=='') {
                $CI->session->set_userdata('tmp_login_redirect',current_url());
                if (is_ajax_requested()) {
                    echo '<script>window.location="'.site_url('login').'";</script>';
                } else {
                    redirect('login');
                }
            } else {
                $sess = $CI->session->userdata('ADM_SESS');
                if (base_url() != $sess['admin_url']) {
                    $CI->session->unset_userdata('ADM_SESS');
                    $CI->session->set_userdata('tmp_login_redirect',current_url());
                    if (is_ajax_requested()) {
                        echo '<script>window.location="'.site_url('login').'";</script>';
                    } else {
                        redirect('login');
                    }
                } else {
                    if ($_SERVER['REMOTE_ADDR'] != $sess['admin_ip']) {
                        $CI->session->unset_userdata('ADM_SESS');
                        $CI->session->set_userdata('tmp_login_redirect',current_url());
                        if (is_ajax_requested()) {
                            echo '<script>window.location="'.site_url('login').'";</script>';
                        } else {
                            redirect('login');
                        }
                    }
                }
                
                // check auth
                $id_group = $sess['admin_id_auth_group'];
                if (!$this->checkAuth($segment_1, $id_group)) {
                    //$CI->session->sess_destroy();
                    //redirect('login');
                }
                
            }
        }
    }
    
    /**
     * check authorization for user
     * @param string $menu
     * @param int $id_group
     * @return boolean
     */
    private function checkAuth($menu,$id_group) {
        $CI =& get_instance();
        $CI->load->database();
        
        if ($menu == 'home' || $menu == 'dashboard' || $menu == '' && $menu == 'profile') {
            return true;
        }
        
        if (is_superadmin()) {
            $data = $CI->db
                    ->from('auth_menu')
                    ->where('LCASE(file)',strtolower($menu))
                    ->where('id_auth_group',$id_group)
                    ->join('auth_menu_group','auth_menu_group.id_auth_menu=auth_menu.id_auth_menu','left')
                    ->count_all_results();
        } else {
            $data = $CI->db
                    ->from('auth_menu')
                    ->where('LCASE(file)',strtolower($menu))
                    ->where('id_auth_group',$id_group)
                    ->where('is_superadmin',0)
                    ->join('auth_menu_group','auth_menu_group.id_auth_menu=auth_menu.id_auth_menu','left')
                    ->count_all_results();
        }
        if ($data > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * print layout based on controller class and function
     * @return string view layout
     */
    function view()
    {
        $CI = & get_instance();

        if (isset($CI->layout) && $CI->layout == 'none') {
            return;
        }
        
        // set data
        $dir = $CI->router->directory;
        $class = $CI->router->fetch_class();
        $method = $CI->router->fetch_method();
        $method = ($method == 'index') ? $class : $method;
        $data = (isset($CI->data)) ? $CI->data : array();
        $data['current_controller'] = base_url() . $dir . $class . '/';
        $data['base_url'] = base_url();
        $data['current_url'] = current_url();
        $data['sess'] = $CI->session->userdata;
        $message = $CI->session->flashdata('message');
        $tmp_msg = $CI->session->flashdata('tmp_msg');
        $success_message = $CI->session->flashdata('success_msg');
        $error_message = $CI->session->flashdata('error_msg');
        if ($message) {
            $data['message'] = alert_box($message,'info');
        }
        if ($tmp_msg) {
            $data['tmp_msg'] = alert_box($tmp_msg,'warning');
        }
        if ($error_message) {
            $data['error_msg'] = alert_box($error_message,'error');
        }
        if ($success_message) {
            $data['success_msg'] = alert_box($success_message,'success');
        }
        $data['auth_sess'] = $CI->session->userdata('ADM_SESS');
        $data['site_setting'] = get_sitesetting();
        $data['site_info'] = get_site_info();
        
        $data['main_nav'] = $this->printMenu();
        
        /**
        $bread = $this->printBreadcrumbs($class);
        if (isset($data['breadcumbs'])) {
            $data['breadcrumbs'] = array_merge($data['breadcrumbs'],$bread);
        } else {
            $data['breadcrumbs'] = $bread;
        }
         * 
         */
        
        if (isset($data['content_layout'])) {
            $data['content'] = $CI->load->view($class . '/' . $data['content_layout'], $data, true);
        } else {
            $data['content'] = $CI->load->view($class . '/' . $method, $data, true);
        }
        $defaultLayout = ($dir) ? str_replace('/', '', $dir) : 'layout/default';
        $layout = (isset($CI->layout)) ? $CI->layout : $defaultLayout;
        $CI->load->view($layout, $data);
    }
    
    /**
     * print auth menu navigation
     * @param type $id_parent
     * @return string print menu
     */
    private function printMenu($id_parent=0) {
        $CI=&get_instance();
        $CI->load->database();
        $id_group = adm_sess_usergroupid();
        $segment_1 = $CI->uri->segment(1);
        $segment_2 = $CI->uri->segment(2);
        $return = '';
        $menus = $CI->db
                ->where('auth_menu_group.id_auth_group',$id_group)
                ->where('auth_menu.parent_auth_menu',$id_parent)
                ->order_by('auth_menu.position','asc')
                ->order_by('auth_menu.id_auth_menu','asc')
                ->join('auth_menu','auth_menu.id_auth_menu=auth_menu_group.id_auth_menu','left')
                ->get('auth_menu_group')
                ->result_array();
        
        $a=0;
        
        $active_menu = $CI->db
                ->where('LCASE(file)',strtolower($segment_1))
                ->where('file !=','#')
                ->limit(1)
                ->get('auth_menu')
                ->row_array();
        $id_active = false;
        if ($active_menu) {
            $id_active = $this->getActiveMenu($active_menu['parent_auth_menu'], $active_menu['id_auth_menu']);
        }
        
        foreach ($menus as $menu) {
            $set_active = true;
            if ($a==0) {
                $return .= '<li class="divider-vertical"></li>';
                if ($segment_1 == 'home' || $segment_1 == 'dashboard' || $segment_1 == '' || $segment_1 == 'profile') {
                    $set_active = false;
                    $return .= '<li class="active"><a href="'.site_url('home').'">Dashboard</a></li>';
                } else {
                    $return .= '<li><a href="'.site_url('home').'">Dashboard</a></li>';
                }
                $return .= '<li class="divider-vertical"></li>';
            }
            
            $href = ($menu['file'] == '#' || $menu['file'] == '') ? '#' : site_url($menu['file']);
            
            if ($segment_1 == $menu['file'] && $menu['file'] != '#') {
                $class_active = 'active';
            } else {
                if ($id_active == $menu['id_auth_menu'] && $set_active) {
                    $class_active = 'active';
                } else {
                    $class_active = '';
                }
            }
            
            $children = $this->getMenuChildren($menu['id_auth_menu']);
            if ($children) {
                $li_open = '<li class="dropdown '.$class_active.'"><a data-toggle="dropdown" class="dropdown-toggle" href="'.$href.'">'.$menu['menu'].' <b class="caret"></b></a>';
                $print_children = $this->printMenuChildren($menu['id_auth_menu']);
            } else {
                $li_open = '<li class="'.$class_active.'"><a href="'.$href.'">'.$menu['menu'].'</a>';
                $print_children = '';
            }
            
            $return .= $li_open.$print_children.'</li><li class="divider-vertical"></li>';
            
            $a++;
        }
        
        return $return;
    }
    
    /**
     * get active auth menu
     * @param int $id_parent
     * @param int $id_menu
     * @return int id parent of active menu
     */
    private function getActiveMenu($id_parent,$id_menu) {
        $CI=&get_instance();
        $CI->load->database();
        $return = 0;
        if ($id_parent == 0) {
            return $id_menu;
        }
        $data = $CI->db
                ->where('id_auth_menu',$id_parent)
                ->get('auth_menu')
                ->row_array();
        if ($data) {
            $return = $this->getActiveMenu($data['parent_auth_menu'], $data['id_auth_menu']);
        }
        return $return;
    }
    
    /**
     * get auth menu children
     * @param int $id_parent
     * @return array data
     */
    private function getMenuChildren($id_parent=0) {
        $CI=&get_instance();
        $CI->load->database();
        $id_group = adm_sess_usergroupid();
        $menus = $CI->db
                ->where('auth_menu_group.id_auth_group',$id_group)
                ->where('auth_menu.parent_auth_menu',$id_parent)
                ->order_by('auth_menu.position','asc')
                ->order_by('auth_menu.id_auth_menu','asc')
                ->join('auth_menu','auth_menu.id_auth_menu=auth_menu_group.id_auth_menu','left')
                ->get('auth_menu_group')
                ->result_array();
        
        return $menus;
    }
    
    /**
     * print auth menu children
     * @param int $id_parent
     * @return string return menu
     */
    private function printMenuChildren($id_parent) {
        $menus = $this->getMenuChildren($id_parent);
        $return = '';
        if ($menus) {
            $return .= '<ul class="dropdown-menu">';
            foreach ($menus as $menu) {
                $href = ($menu['file'] == '#' || $menu['file'] == '') ? '#' : site_url($menu['file']);
                $children = $this->getMenuChildren($menu['id_auth_menu']);
                if ($children) {
                    $return .= '<li class="dropdown-submenu">';
                    $return .= '<a tabindex="-1" href="'.$href.'">'.$menu['menu'].'</a>';
                    $return .= $this->printMenuChildren($menu['id_auth_menu']);
                } else {
                    $return .= '<li><a href="'.$href.'">'.$menu['menu'].'</a></li>';
                }
            }
            $return .= '</ul>';
        }
        return $return;
    }
    
    private function printBreadcrumbs($path) {
        $return[] = array(
            'text'  => 'Dashboard',
            'href'  => site_url('/'),
            'class' => ''
        );
        $id_parent = $this->getMenuIDByFile($path);
        $breadcrumbs = $this->breadcrumbsMenu($id_parent);
        $return = array_merge($return,$breadcrumbs);
        return $return;
    }
    
    private function breadcrumbsMenu($id_parent) {
        $CI=& get_instance();
        $CI->load->database();
        $return = array();
        $data = $CI->db
                ->where('id_auth_menu',$id_parent)
                ->limit(1)
                ->get('auth_menu')
                ->row_array();
        if ($data) {
            $href = ($data['file'] == '' || $data['file'] == '#') ? '#' : site_url($data['file']);
            $return[] = array(
                'text'  => $data['menu'],
                'href'  => $href,
                'class' => ''
            );
            $menu = $this->breadcrumbsMenu($data['parent_auth_menu']);
            $return = array_merge($menu,$return);
        }
        return $return;
    }
    
    private function getMenuIDByFile($path) {
        $CI=& get_instance();
        $CI->load->database();
        $data = $CI->db
                ->where('LCASE(file)',strtolower($path))
                ->where('file !=','#')
                ->limit(1)
                ->get('auth_menu')
                ->row_array();
        if ($data) {
            return $data['parent_auth_menu'];
        } else {
            return 0;
        }
            
    }


    /**
     * custom config
     */
    function custom_cfg() {
        $CI = & get_instance();
        //$base_url = str_replace('http://' . $_SERVER['HTTP_HOST'], '', base_url());
        //$CI->base_url = str_replace('https://' . $_SERVER['HTTP_HOST'], '', $base_url); //jika https
        $base_url =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http");
        $base_url .=  "://".$_SERVER['HTTP_HOST'];
        $base_url .=  str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
        $CI->base_url = $base_url;
    }
    
}

/* End of file ddi_hook.php */
/* Location: ./application/hooks/ddi_hook.php */
