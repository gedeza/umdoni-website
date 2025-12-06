<?php
/**
 * @author : rakheoana lefela
 * @date : 16th dec 2021
 * 
 * Front Controller/ hadles all the incoming requests to site
 */
namespace App\Controllers\Dashboard;

use \Core\View;
use App\Models\Profile;
use App\Models\User;
use App\Models\Countries;
use App\Models\UserModel;
use Aws\S3\S3Client;
use App\Models\Roles;


class Users extends \Core\Controller
{
    public function indexAction()
    {
        
        if(isset($_POST) && count($_POST) > 0){
            $data = $_POST;
            $id = $data['user_id'];
            $users = Profile::getById($id);    
        }
        else{
        $users = Profile::getAll();
        }
        view::render('dashboard/users/index.php',  $users, 'dashboard');
    }

    public function addAction()
    {
        $data = getPostData();
        if(isset($data['id'])) 
        {
            $id = $data['id'];
            $user = UserModel::getUser($id);
           
        }else
            $user = array();
        view::render('dashboard/users/add.php',  $user, 'dashboard');
    }

    public function updateAction()
    {
        $data = $_POST;
        try 
        {
            $id =  UserModel::UpdateUser($data);
        } catch (\Throwable $th) 
        {
            echo $th->getMessage();
        }
        redirect('dashboard/users/list');
    }
   

    public function saveAction()
    {
        global $context;

        if(isset($_POST))   $data = $_POST;

        try
        {
            // Validation for new user creation
            if (!isset($data['user_id']) || empty($data['user_id'])) {
                // Password validation
                if (empty($data['password'])) {
                    throw new \Exception("Password is required");
                }

                if (empty($data['confirm_password'])) {
                    throw new \Exception("Password confirmation is required");
                }

                if ($data['password'] !== $data['confirm_password']) {
                    throw new \Exception("Passwords do not match");
                }

                if (strlen($data['password']) < 8) {
                    throw new \Exception("Password must be at least 8 characters");
                }

                // Role validation
                if (empty($data['role_id'])) {
                    throw new \Exception("User role is required");
                }

                // Email validation
                if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Valid email address is required");
                }

                // Map first_name/last_name to username/surname for UserModel
                $data['username'] = $data['first_name'];
                $data['surname'] = $data['last_name'];

                // Set required fields with defaults
                $data['createdAt'] = date("Y-m-d H:i:s");
                $data['status'] = 1;  // 1 = active
                $data['locked'] = 0;  // 0 = unlocked (account active)

                // Remove confirm_password (not needed in database)
                unset($data['confirm_password']);
            }

            // Save user
            $id = UserModel::Save($data);

            // Success message is set in UserModel::Save()

        } catch (\Throwable $th)
        {
            $_SESSION['error'] = ['message' => $th->getMessage()];
        }

        redirect('dashboard/users/index');
    }

    public function deleteAction()
    {
        $id = $_GET['id'];
        try 
        {
            UserModel::Delete($id);
        } catch (\Throwable $th) 
        {
            echo $th->getMessage();
        }
        redirect('dashboard/users/list');
    }

public function detailsAction()
{
    
    $id = getPostData();
    if(isset($id)) $id = $id['id'];
    $user = UserModel::getUser($id);
    view::render('dashboard/users/details.php',  $user, 'dashboard');
}





public function manageuserAction()
{
    $data = $_POST;
    $locked = [
        'false' => '1',
        'true' => '0' 
    ];
    $data['locked'] = $locked[$data['locked']];
    try{
       $id = UserModel::ChangeStatus($data);
       redirect('dashboard/users/index');

    }catch(\Throwable $th)
    {
        echo $th->getMessage();
    }
}


    public function manageroleAction()
    {
        $data = $_POST;
        try{
            UserModel::ChangeRole($data);
            redirect('dashboard/users/index');
        }catch(\Throwable $th)
        {
            echo $th->getMessage();
        }
    }


    protected function before()
    {
        enable_authorize();
    }

    protected function after()
    {
        // echo "(after)";
    }

}



?>