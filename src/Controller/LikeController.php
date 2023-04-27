<?php

namespace App\Controller;

use App\Entity\Like;
use App\Form\LikeType;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/like')]
class LikeController extends AbstractController
{
    // #[Route('/', name: 'app_like_index', methods: ['GET'])]
    // public function index(LikeRepository $likeRepository): Response
    // {
    //     return $this->render('like/index.html.twig', [
    //         'likes' => $likeRepository->findAll(),
    //     ]);
    // }

    #[Route('/addPostLike/{idPost}', name: 'app_like_add_post', methods: ['GET', 'POST'])]
    public function new(Request $request, LikeRepository $likeRepository, $idPost, PostRepository $postRepository): Response
    {
        // search if this like already exists
        $like = $likeRepository->findOneBy(['post' => $idPost, 'user' => $this->getUser()]);
        if ($like) {
            return $this->redirectToRoute('app_like_remove_post', ['idPost' => $idPost], Response::HTTP_SEE_OTHER);
        }
        $user = $this->getUser();
        $like = new Like();
        // get post by id
        $post = $postRepository->find($idPost);
        $like->setPost($post);
        $like->setUser($user);
        $like->setType("Post");

        $likeRepository->save($like, true);

        // redirect to referer
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    // #[Route('/{id}', name: 'app_like_show', methods: ['GET'])]
    // public function show(Like $like): Response
    // {
    //     return $this->render('like/show.html.twig', [
    //         'like' => $like,
    //     ]);
    // }


    #[Route('/removePostLike/{idPost}', name: 'app_like_remove_post', methods: ['POST'])]
    public function delete(Request $request, LikeRepository $likeRepository, $idPost): Response
    {
        // if this like exists, delete it
        $like = $likeRepository->findOneBy(['post' => $idPost, 'user' => $this->getUser()]);
        if ($like) {
            $likeRepository->remove($like, true);
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        // else redirect to function newPostLike
        return $this->redirectToRoute('app_like_new_post', ['idPost' => $idPost], Response::HTTP_SEE_OTHER);
    }
}
