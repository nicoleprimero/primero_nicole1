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
  /*  public function register($username, $email, $password, $role = 'user')
    
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
 /*   public function login($username, $password)
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
  /*  public function is_logged_in()
    {
        return (bool) $this->session->userdata('logged_in');
    }

    /**
     * Check user role
     */
 /*   public function has_role($role)
    {
        return $this->session->userdata('role') === $role;
    }

    /**
     * Logout user
     */
 /*   public function logout()
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
    
*/

/**
	 * Password Default Hash
	 * @param  string $password User Password
	 * @return string  Hashed Password
	 */
	public function passwordhash($password)
	{
		$options = array(
		'cost' => 4,
		);
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}

	/**
	 * [register description]
	 * @param  string $username  Username
	 * @param  string $password  Password
	 * @param  string $email     Email
	 * @param  string $usertype   Usertype
	 * @return $this
	 */
	public function register($username, $email, $password, $email_token, $role='fairy')
	{
		$this->_lava->db->transaction();
		$data = array(
			'username' => $username,
			'password' => $this->passwordhash($password),
			'email' => $email,
			'role'=> $role,
			'email_token' => $email_token,
            'created_at' => date("Y-m-d h:i:s", time()),
            'updated_at' => date("Y-m-d h:i:s", time())
		);

		$res = $this->_lava->db->table('magicusers')->insert($data);
		if($res) {
			$this->_lava->db->commit();
			return $this->_lava->db->last_id();
		} else {
			$this->_lava->db->roll_back();
			return false;
		}
	}

	 public function login($username, $password)
    {
    $username = $username;
    $password = $password;

    // Allow login via username
    $user = $this->db->table('magicusers')
                       ->where('email', $email)
                       ->get();

    if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
        $this->session->set_userdata([
            'user_id'   => $user['id'],
            'email'  => $user['email'],
            'role'      => ($user['role'] ?? 'fairy'),
            'logged_in' => true
        ]);
        return true;
    }
    return false;
    }

	


	/**
	 * Change Password
	 *
	 * @param string $password
	 * @return void
	 */
	public function change_password($password) {
		$data = array(
					'password' => $this->passwordhash($password)
				);
		return  $this->_lava->db
					->table('magicusers')
					->where('id', $this->get_user_id())
					->update($data);
	}

	/**
	 * Set up session for login
	 * @param $this
	 */
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

	/**
	 * Check if user is Logged in
	 * @return bool TRUE is logged in
	 */
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
	 * Get User ID
	 * @return string User ID from Session
	 */
	public function get_user_id()
	{
		$user_id = $this->_lava->session->userdata('user_id');
		return !empty($user_id) ? (int) $user_id : 0;
	}

	/**
	 * Get Username
	 * @return string Username from Session
	 */
	public function get_username($user_id)
	{
		$row = $this->_lava->db
						->table('magicusers')
						->select('username')					
    					->where('id', $user_id)
    					->limit(1)
    					->get();
    	if($row) {
    		return html_escape($row['username']);
    	}
	}

	public function set_logged_out() {
		$data = array(
			'user_id' => $this->get_user_id(),
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'session_data' => $this->_lava->session->userdata('session_data')
		);
		$res = $this->_lava->db->table('sessions')
						->where($data)
						->delete();
		if($res) {
			$this->_lava->session->unset_userdata(array('user_id'));
			$this->_lava->session->sess_destroy();
			return true;
		} else {
			return false;
		}
		
	}

	public function verify($token) {
		return $this->_lava->db
						->table('magicusers')
						->select('id')
						->where('email_token', $token)
						->where_null('email_verified_at')
						->get();	
	}

	public function verify_now($token) {
		return $this->_lava->db
						->table('magicusers')
						->where('email_token' ,$token)
						->update(array('email_verified_at' => date("Y-m-d h:i:s", time())));	

	}
	
	public function send_verification_email($email) {
		return $this->_lava->db
						->table('users')
						->select('username, email_token')
						->where('email', $email)
						->where_null('email_verified_at')
						->get();	
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

	public function is_user_verified($email) {
		$this->_lava->db
				->table('magicusers')
				->where('email', $email)
				->where_not_null('email_verified_at')
				->get();
	return $this->_lava->db->row_count();
	}

	public function get_reset_password_token($token)
	{
		return $this->_lava->db
				->table('password_reset')	
				->select('email')			
				->where('reset_token', $token)
				->get();
	}

	public function reset_password_now($token, $password)
	{
		$email = $this->get_reset_password_token($token)['email'];
		$data = array(
			'password' => $this->passwordhash($password)
		);
		return $this->_lava->db
				->table('magicusers')
				->where('email', $email)
				->update($data);
	}


}


?>