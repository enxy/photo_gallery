<?php
/**
 * Photo controller.
 */
namespace Controller;

use Form\photo\PhotoType;
use Form\photo\EditTagType;
use Form\photo\CommentType;
use Form\photo\RatingType;
use Form\photo\EditPhotoType;
use Repository\PhotoRepository;
use Repository\TagRepository;
use Repository\CommentRepository;
use Repository\UserRepository;
use Service\FileUploader;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

    /**
    * Class PhotoController.
    *
    * @package Controller
    */
class PhotoController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match('/add', [$this, 'addAction'])->method('POST|GET')->bind('add_photo');
        $controller->post('/add', [$this, 'addAction'])->bind('added_photo');
        $controller->get('/show', [$this,'showPhotosAction'])->bind('show_photos');
        $controller->get('/{id}', [$this,'selectPhotoAction'])->method('POST|GET')->bind('select_photo');
        $controller->get('/{id}/delete', [$this,'deletePhotoAction'])->bind('delete_photo');
        $controller->get('{id}/edit', [$this,'editPhotoAction'])->method('POST|GET')->bind('edit_photo');
        $controller->get('{id}/edit/remove/{tag}', [$this,'removeLinkedTagAction'])->method('POST|GET')->bind('remove_tag');
        $controller->get('{id}/edit/add', [$this,'editPhotoAction'])->method('POST|GET')->bind('add_tag');
        $controller->get('{id}/edit/comment/{comment}', [$this,'deleteCommentAction'])->bind('delete_comment');
        return $controller;
    }
    /**
     * Add action.
     *
     * @param \Silex\Application  $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAction(Application $app, Request $request)
    {
        $photo = [];
        $userRepository = new UserRepository($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $login = $token->getUsername();
        }
        $user = $userRepository->loadUserByLogin($login);
        $userId = $user['id'];
        $form = $app['form.factory']->createBuilder(
            PhotoType::class,
            $photo,
            ['tag_repository' => new TagRepository($app['db'])]
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo  = $form->getData();
            $fileUploader = new FileUploader($app['config.photos_directory']);
            $fileName = $fileUploader->upload($photo['path']);
            $photo['path'] = $fileName;
            $photoRepository = new PhotoRepository($app['db']);
            $photoRepository->save($photo, $userId);

            $app['session']->getFlashBag()->add(
            'messages',
            [
            'type'    => 'success',
            'message' => 'message.element_successfully_added',
            ]);

            return $app->redirect(
            $app['url_generator']->generate('added_photo'),
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
     * Show photos action.
     *
     * @param \Silex\Application  $app     Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function showPhotosAction(Application $app){

        $galleryRepo = new PhotoRepository($app['db']);
        $photos = $galleryRepo->findUserPhotos();

        return $app['twig']->render('photo/userPhotos.html.twig', ['photos'=>$photos]);
    }
    /**
     * Delete photo action.
     *
     * @param \Silex\Application  $app     Silex application
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function deletePhotoAction(Application $app, $id){
        $photoRepository = new PhotoRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $owner = $photoRepository->checkEditOption($id);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $login = $token->getUsername();
        }
        $userId = $userRepository->getUserByLogin($login);
        if($owner == $userId['id'] || $app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $photoRepository->deleteUserPhoto($id);


            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]);
            $photos = $photoRepository->findUserPhotos();
            return $app['twig']->render('photo/userPhotos.html.twig',['id'=>$id, 'photos'=>$photos]);
        }
        $app['session']->getFlashBag()->add(
            'messages',
            [
                'type'    => 'danger',
                'message' => 'message.authorization_denied',
            ]);

        $photos = $photoRepository->findUserPhotos();
        return $app->redirect(
            $app['url_generator']->generate('select_photo', ['id'=>$id]),
            301
        );
    }
    /**
     * Remowe linked Tag action.
     *
     * @param \Silex\Application  $app     Silex application
     * @param $id
     * @param $tag
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function removeLinkedTagAction(Application $app, $id, $tag){
        $tagRepo = new TagRepository($app['db']);
        $photoRepo = new PhotoRepository($app['db']);
        $photoRepo->removeLinkedTag($tag);
        $tagRepo->deleteTag($tag);
        $app['session']->getFlashBag()->add(
            'messages',
            [
                'type'    => 'success',
                'message' => 'message.tag_successfully_deleted',
            ]);

        return $app->redirect(
            $app['url_generator']->generate('edit_photo',['id'=>$id]),
            301
        );
    }
    /**
     * Add action.
     *
     * @param \Silex\Application  $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function selectPhotoAction(Application $app, Request $request, $id){
        $photoRepo = new PhotoRepository($app['db']);
        $userRepo = new UserRepository($app['db']);
        $photo = $photoRepo->selectUserPhoto($id);
        $commentRepo =  new CommentRepository($app['db']);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $user = $token->getUsername();
        }
        $userData = $userRepo->getUserByLogin($user);
        $idUser = $userData['id'];
        $form2 = $app['form.factory']->createBuilder(
            RatingType::class
        )->getForm();
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $rating = $form2->getData();
            $commentRepo->saveRating($rating['number'], $id);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.element_successfully_added',
                ]);

            return $app->redirect(
                $app['url_generator']->generate('select_photo', ['id'=>$id]),
                301
            );
        }
        $form = $app['form.factory']->createBuilder(
            CommentType::class
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $commentRepo->save($comment, $id, $user, $idUser);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type'    => 'success',
                    'message' => 'message.element_successfully_added',
                ]);

            return $app->redirect(
                $app['url_generator']->generate('select_photo', ['id'=>$id]),
                301
            );
        }
        $commentData = $commentRepo->findCommentAuth($id);

        $grades = $commentRepo->selectRating($id);
        $numOfGrades = count($grades);
        $totalGrade=0;
        if($numOfGrades != 0){
            for($i=0; $i<$numOfGrades; $i++){
                $grade=$grades[$i]['number'];
                $totalGrade += $grade;
            }
            $totalGrade = $totalGrade/$numOfGrades;
        }

        return $app['twig']->render(
            'photo/userPhoto.html.twig',
            ['id'=>$id, 'commentData'=>$commentData, 'photo'=>$photo, 'form'=>$form->createView(), 'form2'=>$form2->createView(), 'numOfGrades'=>$numOfGrades, 'totalGrade'=>$totalGrade]
        );
    }
    /**
     * Edit photo action.
     *
     * @param \Silex\Application  $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function editPhotoAction(Application $app, Request $request, $id){
        $photoRepository = new PhotoRepository($app['db']);
        $userRepository = new UserRepository($app['db']);
        $owner = $photoRepository->checkEditOption($id);
        $token = $app['security.token_storage']->getToken();
        if (null !== $token) {
            $login = $token->getUsername();
        }
        $userId = $userRepository->getUserByLogin($login);

        if($owner[0]['userId'] == $userId['id'] || $app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $tagRepository = new TagRepository($app['db']);
            $commentRepo = new CommentRepository($app['db']);
            $photoData = $photoRepository->selectUserPhoto($id);

            $form1 = $app['form.factory']->createBuilder(
                EditPhotoType::class,
                $data = []
            )->getForm();

            $form1->handleRequest($request);
            if ($form1->isSubmitted() && $form1->isValid()) {
                $data = $form1->getData();
                $photoRepository->saveChanges($data, $photoData['idPhoto']);
                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.element_successfully_added',
                    ]);

                return $app->redirect(
                    $app['url_generator']->generate('show_photos'),
                    301
                );
            }
            $form2 = $app['form.factory']->createBuilder(
                EditTagType::class,
                $tag = []
            )->getForm();
            $form2->handleRequest($request);

            if ($form2->isSubmitted() && $form2->isValid()) {
                $tag = $form2->getData();
                $tagRepository->saveChanges($tag, $photoData['idPhoto']);

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.tag_successfully_added',
                    ]);

                return $app->redirect(
            $app['url_generator']->generate('select_photo', ['id'=>$id]),
            301
        );
            }
            $tagIds = $photoRepository->findLinkedTagsIds($id);
            $tagNames = $tagRepository->findById($tagIds);
            $photoComments = $commentRepo->findCommentAuth($id);

            return $app['twig']->render(
                'photo/editPhoto.html.twig',
                ['id' => $id, 'tagNames' => $tagNames, 'photo' => $photoData, 'commentData' => $photoComments, 'form1' => $form1->createView(), 'form2' => $form2->createView()]
            );
        }
        $app['session']->getFlashBag()->add(
            'messages',
            [
                'type'    => 'danger',
                'message' => 'message.authorization_denied',
            ]);

        return $app->redirect(
            $app['url_generator']->generate('select_photo', ['id'=>$id]),
            301
        );
    }
    /**
     * Delete comment action.
     *
     * @param \Silex\Application  $app     Silex application
     * @param $id
     * @param $comment
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function deleteCommentAction(Application $app, $id, $comment){
        $commentRepo = new CommentRepository($app['db']);
        $commentRepo->deleteComment($comment);
	$app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

        return $app->redirect(
            $app['url_generator']->generate('select_photo', array('id'=>$id)),
            301
        );
    }
}