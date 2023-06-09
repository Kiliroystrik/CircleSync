<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\Group;
use App\Entity\Post;
use App\Form\CommentaryType;
use App\Form\GroupType;
use App\Form\PostType;
use App\Repository\GroupRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Services\GroupManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/group')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'app_group_index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GroupRepository $groupRepository): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRepository->save($group, true);

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_show', methods: ['GET', 'POST'])]
public function show(Request $request, Group $group, PostRepository $postRepository, LikeRepository $likeRepository): Response
{

    $user = $this->getUser();
    $post = new Post();
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);
    $post->setUser($user);

    $referer = $request->headers->get('referer');

    if (!$group) {
        throw $this->createNotFoundException('The group does not exist');
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $post->setGroupe($group);
        $postRepository->save($post, true);

        return $this->redirect($referer);
    }

    $commentary = new Commentary();
    $formCommentary = $this->createForm(CommentaryType::class, $commentary);
    $formCommentary->handleRequest($request);
    $commentary->setUser($user);
    // $commentary->setPost($post); // Commentez cette ligne

    $posts = $group->getPosts();
    foreach ($posts as $post) {
        $postRepository->updatePostLikes($post);
    }

    return $this->render('group/show.html.twig', [
        'group' => $group,
        'post' => $post,
        'form' => $form->createView(),
        'formCommentary' => $formCommentary->createView(),
        'referer' => $referer,
    ]);
}


    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group, GroupRepository $groupRepository): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRepository->save($group, true);

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Group $group, GroupRepository $groupRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $group->getId(), $request->request->get('_token'))) {
            $groupRepository->remove($group, true);
        }

        return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/join_group/{groupId}', name: 'app_join_group')]
    public function joinGroup(int $groupId, GroupManagerService $groupManagerService, UserRepository $userRepository): Response
    {
        // Récupérez l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();
        $user = $userRepository->find($user);

        // Utilisez le service GroupManagerService pour ajouter l'utilisateur au groupe
        $groupManagerService->joinGroup($groupId, $user);

        //redirect to app_group_show page
        return $this->redirectToRoute('app_group_show', ['id' => $groupId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/leave_group/{groupId}', name: 'app_leave_group')]
    public function leaveGroup(int $groupId, GroupManagerService $groupManagerService, UserRepository $userRepository): Response
    {
        // Récupérez l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();
        $user = $userRepository->find($user);

        // Utilisez le service GroupManagerService pour ajouter l'utilisateur au groupe
        $groupManagerService->leaveGroup($groupId, $user);

        //redirect to app_group_show page
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
