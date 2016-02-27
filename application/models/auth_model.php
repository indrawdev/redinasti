<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Auth Model Class
 * @author ivan lubis
 * @version 2.1
 * @category Model
 * @desc authentication model
 * 
 */
class Auth_model extends CI_Model
{
    // error login admin message
    private $err_login_adm;
    // error login member message
    private $err_login_mem;

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->err_login_adm = 'Username or Password is incorrect';
    }

    /**
     * check login admin
     * @param type $username
     * @param type $password 
     */
    function check_login($username, $password)
    {
        if ($username != '' && $password != ''):
            $username = strtolower($username);
            $query = $this->db->query("SELECT * FROM " . $this->db->dbprefix('auth_user') . " WHERE LCASE(username) = ?", array($username));
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $password = md5plus($password);
                if (($password == $row['userpass']) && ($password != "")) {
                    $user_sess = array(
                        'admin_name' => $row['name'],
                        'admin_id_auth_group' => $row['id_auth_group'],
                        'admin_id_division' => $row['id_division'],
                        'admin_id_auth_user' => md5plus($row['id_auth_user']),
                        'admin_email' => $row['email'],
                        'admin_type' => ($row['is_superadmin']) ? 'superadmin' : 'admin',
                        'admin_url' => base_url(),
                        'admin_ip' => $_SERVER['REMOTE_ADDR'],
                        'admin_last_login' => $row['last_login'],
                    );
                    $this->session->set_userdata('ADM_SESS', $user_sess);
                    
                    # insert to log
                    $data = array(
                        'id_user' => $row['id_auth_user'],
                        'id_group' => $row['id_auth_group'],
                        'action' => 'Login',
                        'desc' => 'Login:succeed; IP:' . $_SERVER['SERVER_ADDR'] . '; username:' . $username . ';',
                    );
                    insert_to_log($data);
                    if ($this->session->userdata('tmp_login_redirect') != '') {
                        redirect($this->session->userdata('tmp_login_redirect'));
                    } else {
                        redirect( 'home');
                    }
                } else {
                    # insert to log
                    $data = array(
                        'action' => 'Login',
                        'desc' => 'Login:failed; IP:' . $_SERVER['SERVER_ADDR'] . '; username:' . $username . ';',
                    );
                    insert_to_log($data);
                    $this->session->set_flashdata('error_msg', $this->err_login_adm);
                    redirect('login');
                }
            }
            /* else if((strtolower($this->input->post("username")) == "administrator") && (strtolower($this->input->post("password")) == "admin"))
              {
              $this->session->set_userdata('admin','Ivan');
              $this->session->set_userdata('id_auth_user_group','1');
              $this->session->set_userdata('id_auth_user','99999');
              redirect('webcontrol/home');
              } */ else {
                #insert to log
                $data = array(
                    'action' => 'Login',
                    'desc' => 'Login:failed; IP:' . $_SERVER['SERVER_ADDR'] . '; username:' . $username . ';',
                );
                insert_to_log($data);
                
                $this->session->set_flashdata('error_msg', $this->err_login_adm);
                redirect('login');
            }
        else:
            $this->session->set_flashdata('error_msg', $this->err_login_adm);
            redirect('login');
        endif;
    }

    /**
     * check username/email
     * @param type $username
     */
    function check_username($username)
    {
        $query = $this->db->query("SELECT * FROM " . $this->db->dbprefix('auth_user') . " WHERE id_site='1' AND LCASE(username) = ?", array($username));
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * check forgot password 
     * @param type $username
     */
    function forgot_password($username)
    {
        if ($username != "") {
            $username = strtolower($username);
            $query = $this->db->query("SELECT * FROM " . $this->db->dbprefix('auth_user') . " WHERE id_site='1' AND LCASE(username) = ?", array($username));
            if ($query->num_rows() > 0) {
                $row = $query->row_array();

                // random char
                $acceptedChars = 'abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                $max = strlen($acceptedChars) - 1;
                $tmp_password = null;
                for ($i = 0; $i < 5; $i++) {
                    $tmp_password .= $acceptedChars{mt_rand(0, $max)};
                }
                $encrypted = $this->encrypt->encode($tmp_password);
                $hash = $this->encrypt->encode('ddi_ultahku' . $tmp_password);

                // random char
                $acceptedChars2 = 'abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                $max2 = strlen($acceptedChars2) - 1;
                $tmp_code = null;
                for ($a = 0; $a < 10; $a++) {
                    $tmp_code .= $acceptedChars2{mt_rand(0, $max2)};
                }
                $enc_code = $tmp_code;

                // update data
                $this->db->where('id_auth_user', $row['id_auth_user']);
                $this->db->update('auth_user', array(
                    'userpass_tmp' => $encrypted,
                    'hash' => $hash,
                    'code_hash' => $enc_code,
                    'last_date_send_forgotpass' => date('Y-m-d H:i:s')
                    )
                );
                if ($this->db->affected_rows() > 0) {
                    #insert to log
                    $data = array(
                        'id_user' => $row['id_auth_user'],
                        'id_group' => 0,
                        'action' => 'Forget Password ID : ' . $row['id_auth_user'] . ' ',
                        'desc' => 'Forget Password Request; IP:' . $_SERVER['SERVER_ADDR'] . '; ID:' . $row['id_auth_user'] . ';',
                        'create_date' => date('Y-m-d H:i:s'),
                    );
                    insert_to_log($data);

                    $this->send_email_forgot_password($row['id_auth_user']);

                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
    }

    /**
     * send email for forgot password, which contain url for confirmation and code
     * @param type $id_auth_user
     */
    function send_email_forgot_password($id_auth_user)
    {
        $this->db->where('id_auth_user', $id_auth_user);
        $query = $this->db->get('auth_user');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            
            $config = array(
                'protocol' => get_setting('mail_protocol'),
                'smtp_host' => get_setting('mail_host'),
                'smtp_port' => get_setting('mail_port'),
                //'smtp_crypto' => 'ssl',
                'smtp_user' => get_setting('mail_user'),
                'smtp_pass' => get_setting('mail_pass'),
                'mailtype'  => 'html', 
                'charset'   => 'iso-8859-1',
                'wordwrap'   => TRUE
            );
            $this->load->library('email', $config);
            $this->email->set_mailtype('html');

            $id = $this->encrypt->encode($row['id_auth_user']);
            $hash = $row['hash'];

            $body = 'Please click this link to change your password.<br/>';
            $body .= '<a href="' . base_url() . getAdminFolder() . '/forgot_password/change_forgot_password?r=' . myUrlEncode($hash) . '&u=' . myUrlEncode($id) . '">' . base_url() . getAdminFolder() . '/forgot_password/change_forgot_password?r=' . urlencode($hash) . '&u=' . urlencode($id) . '</a>';
            $body .= '<br/>';
            $body .= 'Confirmation code : ' . $row['code_hash'] . '<br/><br/>';
            $body .= 'Please ignore this message if you dont want to change your password.<br/><br/>';
            $body .= 'Regards,<br/>Ultahku.com';

            $this->email->from('no-reply@ultahku.com', 'No-Reply Ultahku.com');
            $this->email->to($row['email'], $row['name']);
            $this->email->bcc('ivan@deptechdigital.com');

            $this->email->subject('Ultahku.com [Admin Forgot Password]');
            $this->email->message($body);

            $this->email->send();
        }
    }

    /**
     * check forgot password code
     * @param type $id_auth_user
     * @param type $hash
     */
    function check_code_forgot_pass($id_auth_user, $hash)
    {
        if ($hash != '') {
            $this->db->where('id_auth_user', $id_auth_user);
            $query = $this->db->get('auth_user');
            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $row_hash = $this->encrypt->decode($row['hash']);
                if ($hash == $row_hash) {
                    return true;
                } else {
                    redirect(getAdminFolder() . '/login');
                }
            } else {
                redirect(getAdminFolder() . '/login');
            }
        } else {
            redirect(getAdminFolder() . '/login');
        }
    }

    /**
     * auto login process member after input code of forgot password
     * @param type $id_auth_user
     * @param type $hash
     * @param type $code
     */
    function forget_pass_login($id_auth_user, $hash, $code)
    {
        if ($code != '') {
            $this->db->where('id_auth_user', $id_auth_user);
            $query = $this->db->get('auth_user');
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $row_hash = $this->encrypt->decode($row->hash);

                if (($code == $row->code_hash) && ($hash == $row_hash)) {
                    $user_sess = array(
                        'admin_name' => $row->name,
                        'admin_id_auth_user_group' => $row->id_auth_user_group,
                        'admin_id_auth_user' => $row->id_auth_user,
                        'admin_id_site' => $row->id_site,
                        'admin_email' => $row->email,
                        'admin_type' => 'admin',
                        'admin_url' => base_url(),
                        'admin_ip' => $_SERVER['REMOTE_ADDR'],
                        'admin_last_login' => $row->last_login,
                    );
                    $this->session->set_userdata('ADM_SESS', $user_sess);

                    $this->db->where('id_auth_user', $row->id_auth_user);
                    $this->db->update('auth_user', array('last_login' => date('Y-m-d H:i:s')));

                    # insert to log
                    $data = array(
                        'id_user' => $row->id_auth_user,
                        'id_group' => $row->id_auth_user_group,
                        'action' => 'Login With Forget Password ID: ' . $row->id_auth_user . '',
                        'desc' => 'Login:succeed; IP:' . $_SERVER['SERVER_ADDR'] . '; ID:' . $row->id_auth_user . ';',
                        'create_date' => date('Y-m-d H:i:s'),
                    );
                    insert_to_log($data);

                    $this->send_email_forgot_password_success($row->id_auth_user);
                    
                    $this->session->set_flashdata('success_msg','Login with Forget Password completed.');
                    
                    redirect(getAdminFolder() . '/profile');
                } else {
                    redirect(getAdminFolder() . '/login');
                }
            } else {
                redirect(getAdminFolder() . '/login');
            }
        } else {
            redirect(getAdminFolder() . '/login');
        }
    }

    /**
     * send email after succes login with forgot password (front end)
     * @param type $id_auth_user
     */
    function send_email_forgot_password_success($id_auth_user)
    {
        $this->db->where('id_auth_user', $id_auth_user);
        $query = $this->db->get('auth_user');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            
            $config = array(
                'protocol' => get_setting('mail_protocol'),
                'smtp_host' => get_setting('mail_host'),
                'smtp_port' => get_setting('mail_port'),
                //'smtp_crypto' => 'ssl',
                'smtp_user' => get_setting('mail_user'),
                'smtp_pass' => get_setting('mail_pass'),
                'mailtype'  => 'html', 
                'charset'   => 'iso-8859-1',
                'wordwrap'   => TRUE
            );
            $this->load->library('email', $config);
            $this->email->set_mailtype('html');

            $body = 'Kamu telah berhasil login dengan menggunakan "Forget Password".<br/>';
            $body .= 'Mohon update data dan password kamu agar kamu dapat login dengan nyaman.<br/><br/>';
            $body .= 'Regards,<br/>Ultahku.com';
            
            $this->email->from('no-reply@ultahku.com', 'No-Reply Ultahku.com');
            $this->email->to($row['email'], $row['name']);
            $this->email->bcc('ivan@deptechdigital.com');

            $this->email->subject('Ultahku.com [Forget Password => Success Login]');
            $this->email->message($body);

            $this->email->send();
        }
    }

}
/* End of file auth_model.php */
/* Location: ./application/model/webcontrol/auth_model.php */