<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\GroupRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new/{groupId}', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository, $groupId, GroupRepository $groupRepository): Response
    {
        $user = $this->getUser();
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $post->setUser($user);
        $referer = $request->headers->get('referer');
        // Get the Group object from the ID
        $group = $groupRepository->find($groupId);
        if (!$group) {
            throw $this->createNotFoundException('The group does not exist');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setGroupe($group);
            $postRepository->save($post, true);

            return $this->redirect($referer);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'group' => $group,
        ]);
    }


    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post, PostRepository $postRepository, LikeRepository $likeRepository): Response
    {
        $likes = $post->getLikes()->toArray();
        $post->setNbrLikes(count($likes));
        $postRepository->update($post);

        // set alreadyLiked to true if the user has already liked the post and false otherwise to di it search if post.likes where user.id and post.id match the user.id and post.id
        $user = $this->getUser();


        return $this->render('post/show.html.twig', [
            'post' => $post,
            // 'alreadyLiked' => $alreadyLiked,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->save($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
