<?php
declare(strict_types=1);

use Phalcon\Http\ResponseInterface;

class UserController extends ControllerBase
{
    public function signupAction()
    {

    }

    public function loginAction()
    {

    }

    /**
     * @return ResponseInterface
     */
    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect('/');
    }

    public function registerAction()
    {

        if (!$this->request->isPost()) {
            return $this->response->redirect('user/signup');
        }

        $user = new Users();
        $user->name = $this->request->getPost('name');
        $user->email = $this->request->getPost('email');
        $user->password = $this->security->hash($this->request->getPost('password'));

        if (!$user->save()){
            $messages = $user->getMessages();
            foreach ($messages as $message) {
                $this->flashSession->error($message->getMessage());
                $this->dispatcher->forward([
                    'controller' => $this->router->getControllerName(),
                    'action'     => 'signup',
                ]);
                return ;
            }
        }
        $this->session->set('AUTH_NAME', $user->name);
        $this->session->set('IS_LOGIN', 1);
        $this->flashSession->success('Thanks for registering!');
        return $this->response->redirect('/');
    }

    public function authAction(){
        if (!$this->request->isPost()) {
            return $this->response->redirect('user/login');
        }

        $password = $this->request->getPost('password');
        $email = $this->request->getPost('email');

        $user = Users::findFirst([
            'email = :email:',
            'bind' => [
                'email' => $email,
            ]
        ]);

        if ($user) {
            if ($this->security->checkHash($password, $user->password)){
                $this->session->set('AUTH_NAME', $user->name);
                $this->session->set('IS_LOGIN', 1);
                $this->flashSession->success('You are logged in!');
                return $this->response->redirect('/');
            }
        }

        $this->flashSession->error("Invalid password or email");
        return $this->response->redirect('user/login');
    }
}

