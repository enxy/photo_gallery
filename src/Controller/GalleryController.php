<?php
/**
 * Created by PhpStorm.
 * User: Jolanta
 * Date: 10.05.2017
 * Time: 16:22
 */
namespace Controller;

use Form\photo\PhotoType;
use Form\user\UserPasswordType;
use Form\user\UserType;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Repository\GalleryRepository;
use Repository\UserRepository;
use Form\user\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Repository\PhotoRepository;

    /**
     * Class GalleryController
     *
     * @package Controller
     */
class GalleryController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'galleryAction'])->bind('gallery_index');
        $controller->match('/register', [$this, 'registerAction'])->method('POST|GET')->bind('register_index');
        $controller->get('/{page}', [$this, 'indexAction'])->assert('id', '[1-9]\d*')->bind('page_index');
        $controller->get('/admin', [$this, 'adminAction'])->bind('admin_index');
        $controller->get('/admin/users', [$this, 'showUsersAction'])->bind('show_users');
        $controller->get('admin/users/{id}', [$this, 'selectUserAction'])->bind('select_user');
        $controller->get('admin/users/{id}/edit', [$this, 'editUserAction'])->method('POST|GET')->bind('edit_user');
        $controller->match('admin/users/{id}/delete', [$this, 'deleteUserAction'])->bind('delete_user');

        return $controller;
    }

    /**
     * Gallery action.
     * @param Application $app
     * @return mixed
     */
     public function galleryAction(Application $app){
        return $app['twig']->render('photo/gallery.html.twig');
    }

    /**
     * Index action.
     * @param Application $app
     * @param $page
     * @param Request $request
     * @return mixed
     */
    public function indexAction(Application $app, $page, Request $request)
    {
        $galleryRepo = new GalleryRepository($app['db']);
        $photoRepo = new PhotoRepository($app['db']);
        //$photos = $photoRepo->findUserPhotos();
        $paginator = $galleryRepo->findAllPaginated($page);
        return $app['twig']->render('photo/index.html.twig', ['paginator'=>$paginator, 'page'=>$page]);
    }
    /**
     * Register action.
     *
     * @param \Silex\Application $app   Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function registerAction(Application $app, Request $request){
        $form = $app['form.factory']->createBuilder(RegisterType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $galleryRepository = new UserRepository($app['db']);
                $data  = $form->getData();
                $plainPassword = $data['password'];
                $data['password'] = $app['security.encoder.bcrypt']->encodePassword($data['password'], '');
                $data['role_id'] = 2;
                $galleryRepository->saveUser($data);
            $token = $app['security.token_storage']->getToken();
            if (null !== $token) {
                $user = $token->getUser();
            }
            return $app->redirect($app['url_generator']->generate('auth_login'), 301);
        }

        return $app['twig']->render(
            'user/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * Photo action.
     *
     * @param \Silex\Application $app   Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function PhotoAction(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder(PhotoType::class)->getForm();
        $form->handleRequest($request);
        $user = $app['security.token_storage']->getToken();
        var_dump($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $galleryRepository = new GalleryRepository($app['db']);
            $galleryRepository->saveUser($form->getData());
        }
        return $app['twig']->render(
            'photo/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit action.
     * @param Application $app
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Application $app, $id, Request $request)
    {
        $galleryRepository = new GalleryRepository($app['db']);
        $photo = $galleryRepository->findOneById($id);

        if (!$photo) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'danger',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('photo_index'));
        }

        $form = $app['form.factory']->createBuilder(TagType::class, $photo)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $galleryRepository->savePhoto($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );
            return $app->redirect($app['url_generator']->generate('tag_index'), 301);
        }

        return $app['twig']->render(
            'photo/edit.html.twig',
            [
                'url' => $photo['url'],
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * Admin action.
     *
     * @param \Silex\Application $app   Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function adminAction(Application $app)
    {
        return $app['twig']->render('user/admin.html.twig');
    }

    /**
     * Show users action.
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function showUsersAction(Application $app, Request $request)
    {
        $galleryRepo = new GalleryRepository($app['db']);
        $paginator = $galleryRepo->findAllPaginated();

        return $app['twig']->render(
            'user/showUsers.html.twig', ['users'=>$galleryRepo->findUsers()]
        );
    }
    /**
     * Select User action.
     *
     * @param \Silex\Application $app   Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function selectUserAction(Application $app, Request $request, $id)
    {
        $userData = [];
        $form = $app['form.factory']->createBuilder(UserType::class, $userData)->getForm();
        $form->handleRequest($request);

        $galleryRepo = new GalleryRepository($app['db']);

        return $app['twig']->render(
          'user/showUser.html.twig', [ 'id'=>$id, 'user'=>$galleryRepo->findUser($id),
                'userData' => $userData,
                'form' => $form->createView()]
        );
    }
    /**
     * Delete user action.
     *
     * @param \Silex\Application $app   Silex application
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function deleteUserAction(Application $app, $id)
    {
        $galleryRepo = new UserRepository($app['db']);
        $galleryRepo->deleteUser($id);

        return $app->redirect($app['url_generator']->generate('show_users', array('id'=>$id), 301));

    }
    /**
     * Upload action.
     *
     * @param \Silex\Application $app   Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function uploadAction(Application $app)
    {
        return $app['twig']->render('photo/add.html.twig');
    }
    /**
     * Add action.
     *
     * @param \Silex\Application $app   Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAction(Application $app, Request $request)
    {
        $photo = [];

        $form = $app['form.factory']->createBuilder(PhotoType::class, $photo)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo  = $form->getData();
            $fileUploader = new FileUploader($app['config.photos_directory']);
            $fileName = $fileUploader->upload($photo['photo']);
            $photo['photo'] = $fileName;
            $photoRepository = new PhotoRepository($app['db']);
            $photoRepository->save($photo);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('photo_index'),
                301
            );
        }

        return $app['twig']->render(
            'photo/add.html.twig',
            [
                'photo'  => $photo,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit User action.
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function editUserAction(Application $app, Request $request, $id){
        $galleryRepository = new GalleryRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $user = $galleryRepository->findUser($id);
        $form = $app['form.factory']->createBuilder(UserType::class, $user)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $galleryRepository->editUser($user);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.personal_data_edited',
                ]
            );
        }
        $form2 = $app['form.factory']->createBuilder(UserPasswordType::class, $user)->getForm();
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $data = $form->getData();
            $password = $app['security.encoder.bcrypt']->encodePassword($data['password'], '');
            $userRepository->editPassword($password, $id);
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.password_edited',
                ]
            );
        }
        return $app['twig']->render(
            'user/showUser.html.twig',
            [
                'user' => $user,
                'id' => $id,
                'form' => $form->createView(),
                'form2' => $form2->createView()
            ]
        );
    }
}