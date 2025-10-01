<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Controller: UserController
 * 
 * Automatically generated via CLI.
 */
class UserController extends Controller {
    public function __construct()
    {
        parent::__construct();
        $this->call->library('pagination');
        $this->call->library('session'); 
        
    }

    /*public function get_all(){
       
        $data['s'] = $this->UserModel->all();
        $this->call->view('get_all', $data);
    }*/
        public function landing_page() {
        $this->call->view('create');
    }

    public function user_view() {
        $this->call->view('User_view');
    }

   
     public function create() {

        if($this->form_validation->submitted()) {
            $username = $this->io->post('username');
            $email = $this->io->post('email');
			$email_token = bin2hex(random_bytes(50));
            $this->form_validation
                ->name('username')
                    ->required()
                    ->is_unique('users', 'username', $username, 'Username was already taken.')
                    ->min_length(5, 'Username name must not be less than 5 characters.')
                    ->max_length(20, 'Username name must not be more than 20 characters.')
                    ->alpha_numeric_dash('Special characters are not allowed in username.')
                ->name('password')
                    ->required()
                    //->min_length(8, 'Password must not be less than 8 characters.')
                ->name('password_confirmation')
                    ->required()
                    //->min_length(8, 'Password confirmation name must not be less than 8 characters.')
                    ->matches('password', 'Passwords did not match.')
                ->name('email')
                    ->required()
                    ->is_unique('users', 'email', $email, 'Email was already taken.');
                if($this->form_validation->run()) { 
                    $created_at = date('Y-m-d H:i:s');
                    $role = isset($_POST['role']) ? $_POST['role'] : 'fairy';
                    
                    if($this->lauth->register($username, $email, $email_token, $this->io->post('password'), $role, $created_at)) {
                        $data = $this->lauth->login($email, $this->io->post('password'));
                        $this->lauth->set_logged_in($data);
                        redirect('users/view');
                    } else {
                        set_flash_alert('danger', config_item('SQLError'));
                    }
                }  else {
                    set_flash_alert('danger', $this->form_validation->errors()); 
                    redirect('auth/register');
                }
        } else {
            $this->call->view('auth/register');
        }
        
    }


 /*   public function create() {
        
        $this->call->library('form_validation');
        if($this->form_validation->submitted()){

    
            $username = $this->io->post('username');
            $email    = $this->io->post('email');
            $password = $this->io->post('password');
            $role     = $this->io->post('role');
            $created_at = date('Y-m-d H:i:s', time() + 8*3600);

            $this->UserModel->create($username, $email, $password, $role, $created_at);

           
            $this->session->set_flashdata('success','User added successfully!');
            redirect('users/view');
            }
            else
            {
                $this->call->view('add_User');
            }
        

           
    }
*/
    public function update($id) {
         
        $data['user'] = $this->UserModel->find($id);
        if($this->io->method() === 'post'){
            $data = [
                'username' => $this->io->post('username'),
                'email' => $this->io->post('email'),
                'role' => $this->io->post('role'),
                'updated_at' => date('Y-m-d H:i:s', time() + 8*3600)
            ];
            $this->UserModel->update($id, $data);
            redirect('users/view');
        }
        else
        {
            $this->call->view('update_User', $data);
        }
    }

    public function delete($id) {
        $this->UserModel->delete($id);
        redirect('users/view');
    }

    public function get_all()
{
   $page = 1;
    if (isset($_GET['page']) && ! empty($_GET['page'])) {
        $page = $this->io->get('page');
    }

    // Search term
    $q = '';
    if (isset($_GET['q']) && ! empty($_GET['q'])) {
        $q = trim($this->io->get('q'));
    }

    $records_per_page = 10;

    $all = $this->UserModel->page($q, $records_per_page, $page);
        $data['all'] = $all['records'];
        $total_rows = $all['total_rows'];
        $this->pagination->set_options([
            'first_link'     => '⏮ First',
            'last_link'      => 'Last ⏭',
            'next_link'      => 'Next →',
            'prev_link'      => '← Prev',
            'page_delimiter' => '&page='
        ]);
        $this->pagination->set_theme('bootstrap'); // or 'tailwind', or 'custom'
        $this->pagination->initialize($total_rows, $records_per_page, $page, 'users/view?q='.$q, 1);
        $data['page'] = $this->pagination->paginate();
        $this->call->view('view_page', $data);
}



}