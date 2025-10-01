<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Model: UserModel
 * 
 * Automatically generated via CLI.
 */
class UserModel extends Model {
    protected $table = 'users';
    protected $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
    }

   /* public function create($username, $email, $password, $role, $created_at) {
        $data = array(
            'username' => $username,
            'email' => $email,
            '$password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s', time() + 8*3600)
        );
        return $this->db->table('users')->insert($data);
    }   */


        public function create() {
    // Load form validation library
    $this->call->library('form_validation');

    // Check if form was submitted
    if ($this->form_validation->submitted()) {

        // ✅ Safely get POST data
        $username  = trim($this->io->post('username') ?? '');
        $email     = trim($this->io->post('email') ?? '');
        $password  = $this->io->post('password') ?? '';
        $password_confirmation = $this->io->post('password_confirmation') ?? '';
        $role      = $this->io->post('role') ?? '';
        $created_at = date('Y-m-d H:i:s', time() + 8*3600);

        // ✅ Basic server-side validation
        if (empty($username) || empty($email) || empty($password) || empty($password_confirmation) || empty($role)) {
            $this->session->set_flashdata('error', 'All fields are required!');
            redirect('users/add_User');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('error', 'Invalid email address!');
            redirect('users/add_User');
            return;
        }

        if ($password !== $password_confirmation) {
            $this->session->set_flashdata('error', 'Passwords do not match!');
            redirect('users/add_User');
            return;
        }

        if (strlen($password) < 8) {
            $this->session->set_flashdata('error', 'Password must be at least 8 characters!');
            redirect('users/add_User');
            return;
        }

        // ✅ Hash password before storing
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // ✅ Insert user
        $this->UserModel->create($username, $email, $hashed_password, $role, $created_at);

        $this->session->set_flashdata('success', 'User added successfully!');
        redirect('users/view');

    } else {
        // Show form if not submitted
        $this->call->view('add_User');
    }
}


    public function get_one($id){
       return $this->db->table('users')->where('id', $id)->get();
    }

    

   public function delete($id) {
       return $this->db->table('users')->where('id', $id)->delete();
   }

   public function page($q, $records_per_page = null, $page = null) {
            if (is_null($page)) {
                return $this->db->table('users')->get_all();
            } else {
                $query = $this->db->table('users');
                
                // Build LIKE conditions
                $query->like('id', '%'.$q.'%')
                    ->or_like('username', '%'.$q.'%')
                    ->or_like('email', '%'.$q.'%')
                    ->or_like('role', '%'.$q.'%');
                    

                // Clone before pagination
                $countQuery = clone $query;

                $data['total_rows'] = $countQuery->select_count('*', 'count')
                                                ->get()['count'];

                $data['records'] = $query->pagination($records_per_page, $page)
                                        ->get_all();

                return $data;
            }
        }

         public function get_user_by_id($id)
    {
        return $this->db->table($this->table)
                        ->where('id', $id)
                        ->get();
    }

    public function get_all_users()
    {
        return $this->db->table($this->table)->get_all();
    }






















        


        /*
    public function count_all_records()
    {
        $sql = "SELECT COUNT({$this->primary_key}) as total FROM {$this->table} WHERE 1=1";
        $result = $this->db->raw($sql);
        return $result ? $result->fetch(PDO::FETCH_ASSOC)['total'] : 0;
    }

    public function get_records_with_pagination($limit_clause)
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1 ORDER BY {$this->primary_key} DESC {$limit_clause}";
        $result = $this->db->raw($sql);
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function count_filtered_records($q)
{
    $like = "%{$q}%";
    $sql = "SELECT COUNT({$this->primary_key}) as total
            FROM {$this->table}
            WHERE username LIKE ? OR email LIKE ? OR role LIKE ?";
    $result = $this->db->raw($sql, [$like, $like, $like]);
    $row = $result ? $result->fetch(PDO::FETCH_ASSOC) : null;
    return $row ? (int)$row['total'] : 0;
}

public function get_filtered_records($q, $limit, $offset)
{
    $like = "%{$q}%";
    $sql = "SELECT * FROM {$this->table}
            WHERE username LIKE ? OR email LIKE ? OR role LIKE ?
            ORDER BY {$this->primary_key} DESC
            LIMIT {$offset}, {$limit}";
    $result = $this->db->raw($sql, [$like, $like, $like]);
    return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
}

*/
}