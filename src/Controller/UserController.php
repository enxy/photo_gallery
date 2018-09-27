<?php
/**
 * Photo controller.
 */
namespace Controller;

use Repository\UserRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Form\user\UserType;
use Form\user\UserLoginType;
use Form\user\UserPasswordType;

    /**
     * Class UserController
     *
     * @package Controller
     */
class UserController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'userPhotosAction'])->bind('user_index');
        $controller->get('/account', [$this, 'userAccountAction'])->method('POST|GET')->bind('user_account');
        $controller->get('/account/edit', [$this, 'editAccountAction'])->bind('edit_account');
        $controller->get('/admin', [$this, 'userPhotosAction'])->bind('admin_index');
        return $controller;
    }
    /**
     * User photos action.
     *
     * @param \Silex\Application $app     Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function userPhotosAction(Application $app)
    {
        $userRepo = new UserRepository($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $login = $token->getUsername();
        }
        $data = $userRepo->getUserByLogin($login);
        $role = $userRepo->getUserRoles($data['id']);
        $role=$userRepo->getUserRoles($login);

        if($app['security.authorization_checker']->isGranted('ROLE_ADMIN')){
            return $app['twig']->render('user/admin.html.twig');
        }else{
            $user = $userRepo->getUserByLogin($login);
	  return $app['twig']->render('user/user.html.twig', ['photos'=>$userRepo->selectUserPhotos($user['id']), 'login'=>$login]);
	} 
    }
    /**
     * Admin action.
     *
     * @param \Silex\Application $app     Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function adminAction(Application $app)
    {
        return $app['twig']->render(
            'user/admin.html.twig'
        );
    }
    /**
     * User account action.
     *
     * @param \Silex\Application $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function userAccountAction(Application $app, Request $request)
    {
        $userRepo = new UserRepository($app['db']);
        $token = $app['security.token_storage']->getToken();
        if ($token !== null) {
            $login = $token->getUsername();
            $user=$token->getUser();
        }
        if(!empty($login)) $userData = $userRepo->getUserData($login);

        $form1 = $app['form.factory']->createBuilder(UserLoginType::class)->getForm();
        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {
            $newLogin = $form1->getData();
            $userRepo->editUser($newLogin, $userData[0]['id']);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.personal_data_edited',
                ]);
            return $app->redirect(
                $app['url_generator']->generate('user_index'),
                301
            );
        }
        $form2 = $app['form.factory']->createBuilder(UserPasswordType::class)->getForm();
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $newPassword = $form2->getData();
            $password = $app['security.encoder.bcrypt']->encodePassword($newPassword['password'], '');
            $userRepo->editPassword($password, $userData[0]['id']);
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.personal_data_edited',
                ]);
            return $app->redirect(
                $app['url_generator']->generate('user_index'),
                301
            );
        }
        return $app['twig']->render('user/userSettings.html.twig',array('user'=>$userData[0]['login'], 'form1' => $form1->createView(), 'form2' => $form2->createView()));
    }}
