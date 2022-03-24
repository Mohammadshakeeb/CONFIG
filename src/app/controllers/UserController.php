<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
//use Phalcon\Http\Response\Cookies;

class UserController extends Controller
{
    public function indexAction()
    {
        //return '<h1>Hello!!!</h1>';

    }

    public function signupAction()
    {

        if ($this->request->isPost('name') || $this->request->isPost('email')) {

            $user = new Users();

            //assign value from the form to $user
            $user->assign(
                $this->request->getPost(),
                [
                    'name',
                    'email',
                    'password'
                ]
            );

            // Store and check for errors
            $success = $user->save();

            // passing the result to the view
            $this->view->success = $success;

            if ($success) {
                $message = "Thanks for registering!";
            } else {
                $message = "Sorry, the following problems were generated:<br>"
                    . implode('<br>', $user->getMessages());
            }

            // passing a message to the view
            $this->view->message = $message;
        }
    }

    public function loginAction()
    {

        // $data=$this->request->getpost();
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();
        if ($this->cookies->has("cookies")) {
            header('location: http://localhost:8080/user/dashboard');
        } else {



            $data = $_POST ?? array();
            $email = $this->request->getpost('email');
            $password = $this->request->getpost('password');
            //$email = $_POST["email"];
            //$password = $_POST["password"];
            $data = Users::query()
                ->where("email = '$email'")
                ->andwhere("password = '$password'")
                ->execute();

            // echo "<pre>";
            // echo($data[0]->email);
            // echo "</pre>";

            if (count($data) > 0) {

                $userdata = array(
                    'name' => $data[0]->name,
                    'id' => $data[0]->id,
                    'email' => $data[0]->email,
                    'password' => $data[0]->password,

                );
                $this->session->login = $userdata;
                //  print_r ($this->session->get('login[name]'));
                //  print_r($this->di->get(''));
                global $container;
                //  $cookies = $container->get('cookies');
                if (isset($_POST['remember-me'])) {
                    $this->cookies->set(
                        "cookies",
                        json_encode([
                            "email" => $email,
                            "password" => $password
                        ]),
                        time() + 3600
                    );
                }

                header('location: http://localhost:8080/user/dashboard');
            } else {


                $response = new Response();
                $response->setStatusCode(404, 'Not Found');
                $response->setContent("Sorry, Wrong credentials");
                //  $response->redirect('user/error');
                // $p = $response->getContent();
                // $c = $response->getStatusCode();
                // $a = $response->getReasonPhrase();
                $response->send();

                // echo "<h1>".$c."</h1>";
                // echo "<h1>".$a."</h1>";


                echo $this->tag->linkTo("user/login", "Click here to Login");
            }
        }
    }


    public function dashboardAction()
    {

        global $container;
        echo "<h1>" . "DASHBOARD" . "</h1>";
        print_r($this->session->get('login'));
        // echo $this->session->get('login');
        echo '<form method="post" action="logout"><input type="submit" value="logout" name="logout"></form>';
        // $response=new Response();
        //print_r($response->getCookies());
        // header('location:http://localhost:8080/user/logout');
      //  $this->$container->get('datetime');
        $datetime = $container->get('datetime');
        foreach ((array)$datetime as $key => $value) {
            echo "<br>".$key." : ".$value;
        }
        
    }

    public function logoutAction()
    {
        $this->session->destroy();
        $this->cookies->get('cookies')->delete();

        header('location:http://localhost:8080/user/login');
    }
}
