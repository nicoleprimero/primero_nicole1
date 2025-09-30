<?php
class Auth
{
    protected $_lava;
    protected $db;
    protected $session;


    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->database();   // Initializes the database
        $this->_lava->call->library('session');  // Loads the session library

        // Assign the session and db objects to the class properties
        $this->db = $this->_lava->db;
        $this->session = $this->_lava->session;  // Assign session to $this->session
    }

    /**
     * Register a new user
     */
    public function register($username, $email, $password, $role = 'user')
    
    {
        
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->db->table('magicusers')->insert([
            'username' => $username,
            'email' => $email,
            'password' => $hash,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    /**
     * Login user
     */
    public function login($username, $password)
    {
    $username = $username;
    $password = $password;

    // Allow login via username
    $user = $this->db->table('magicusers')
                       ->where('username', $username)
                       ->get();

    if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
        $this->session->set_userdata([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'role'      => ($user['role'] ?? 'fairy'),
            'logged_in' => true
        ]);
        return true;
    }
    return false;
    }


    /**
     * Check if user is logged in
     */
   /* public function is_logged_in()
    {
        return (bool) $this->session->userdata('logged_in');
    }*/

    public function is_logged_in()
	{
		$data = array(
			'user_id' => $this->_lava->session->userdata('user_id'),
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'session_data' => $this->_lava->session->userdata('session_data')
		);
		$count = $this->_lava->db->table('sessions')
						->select_count('session_id', 'count')
						->where($data)
						->get()['count'];
		if($this->_lava->session->userdata('logged_in') == 1 && $count > 0) {
			return true;
		} else {
			if($this->_lava->session->has_userdata('user_id')) {
				$this->set_logged_out();
			}
		}
	}

    /**
     * Check user role
     */
    public function has_role($role)
    {
        return $this->session->userdata('role') === $role;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->session->unset_userdata(['user_id','username','role','logged_in']);
    }


    public function reset_password($email) {
		$row = $this->_lava->db
						->table('magicusers')
						->where('email', $email)
						->get();
		if($this->_lava->db->row_count() > 0) {
			$this->_lava->call->helper('string');
			$data = array(
				'email' => $email,
				'reset_token' => random_string('alnum', 10)
			);
			$this->_lava->db
				->table('password_reset')
				->insert($data)
				;
			return $data['reset_token'];
		} else {
			return FALSE;
		}
	}
    
    public function set_logged_in($user_id) {
		$session_data = hash('sha256', md5(time().$this->get_user_id()));
		$data = array(
			'user_id' => $user_id,
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'ip' => $_SERVER['REMOTE_ADDR'],
			'session_data' => $session_data
		);
		$res = $this->_lava->db->table('sessions')
				->insert($data);
		if($res) $this->_lava->session->set_userdata(array('session_data' => $session_data, 'user_id' => $user_id, 'logged_in' => 1));
	}


}


?>