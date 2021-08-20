<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Flash;
use \App\Auth;
use \App\Models\User;


class UsersController extends AdminAuthenticated
{

    // Load admin panel homepage
    public function indexAction()
    {
        View::renderTemplate('Admin/Home/index.html');
    }

    // Show all users in users table
    public function usersAction()
    {
        $users = User::getAllUsers();
        View::renderTemplate('Admin/Users/index.html',[
            'users' => $users
        ]);
    }

    // Delete single user
    public function deleteAction()
    {
        if (isset($_POST['deleteUser'])) {
            $user = User::findByID($this->route_params['id']);
            if ($user) {
                User::deleteUser($user->id);
            }
        }
        $this->redirect('/admin/users');
    }

    // Edit single user
    public function editAction()
    {
        $userFromDB = User::findByID($this->route_params['id']);
        if (isset($_POST['user_updated'])) {
            $userModel = new User($_POST);
            $userModel->id = $userFromDB->id;
            if($userModel->edit())
            {
                    Flash::addMessage('פרטי המשתמש עודכנו בהצלחה', Flash::SUCCESS);
                    $this->redirect('/admin/users');
            }
            else {
                Flash::addMessage('אחד מהפרטים שהזנת שגויים', Flash::WARNING);
                $this->renderEditUser($userModel);
            }
        }else {
            View::renderTemplate('Admin/Users/edit_user.html',[
                'user' => $userFromDB ?? null
            ]);
        }
    }

        // Render Edit specific user page 
        public function renderEditUser($user)
        {
            View::renderTemplate('Admin/Users/edit_user.html',[
                'user' => $user ?? null
            ]);
        }

}
