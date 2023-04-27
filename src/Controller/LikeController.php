<?php

namespace App\Controller;

use App\Entity\Like;
use App\Form\LikeType;
use App\Repository\CommentaryRepository;
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

    #[Route('/addLike/post/{idPost}', name: 'app_like_add_post', methods: ['GET', 'POST'])]
    public function addPostLike(Request $request, LikeRepository $likeRepository, $idPost, PostRepository $postRepository): Response
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

        $post = $postRepository->find($idPost);
        $postRepository->updatePostLikes($post);

        // redirect to referer
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }



    #[Route('/removeLike/post/{idPost}', name: 'app_like_remove_post', methods: ['GET', 'POST'])]
    public function removePostLike(Request $request, LikeRepository $likeRepository, $idPost, PostRepository $postRepository): Response
    {
        // if this like exists, delete it
        $like = $likeRepository->findOneBy(['post' => $idPost, 'user' => $this->getUser()]);
        if ($like) {
            $likeRepository->remove($like, true);

            $post = $postRepository->find($idPost);
            $postRepository->updatePostLikes($post);

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        // else redirect to function newPostLike
        return $this->redirectToRoute('app_like_add_post', ['idPost' => $idPost], Response::HTTP_SEE_OTHER);
    }



    #[Route('/addLike/commentary/{idCommentary}', name: 'app_like_add_commentary', methods: ['GET', 'POST'])]
    public function addCommentary(Request $request, LikeRepository $likeRepository, $idCommentary, CommentaryRepository $commentaryRepository): Response
    {
        // search if this like already exists
        $like = $likeRepository->findOneBy(['commentary' => $idCommentary, 'user' => $this->getUser()]);
        if ($like) {
            return $this->redirectToRoute('app_like_remove_commentary', ['idCommentary' => $idCommentary], Response::HTTP_SEE_OTHER);
        }
        $user = $this->getUser();
        $like = new Like();
        // get post by id
        $post = $commentaryRepository->find($idCommentary);
        $like->setPost($post);
        $like->setUser($user);
        $like->setType("Post");

        $likeRepository->save($like, true);

        $commentary = $commentaryRepository->find($idCommentary);
        $commentaryRepository->updateCommentaryLikes($post);

        // redirect to referer
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }



    #[Route('/removeLike/commentary/{idCommentary}', name: 'app_like_remove_commentary', methods: ['GET', 'POST'])]
    public function removeCommentary(Request $request, LikeRepository $likeRepository, $idCommentary, CommentaryRepository $commentaryRepository): Response
    {
        // if this like exists, delete it
        $like = $likeRepository->findOneBy(['commentary' => $idCommentary, 'user' => $this->getUser()]);
        if ($like) {
            $likeRepository->remove($like, true);

            $post = $commentaryRepository->find($idCommentary);
            $commentaryRepository->updateCommentaryLikes($post);

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        // else redirect to function newPostLike
        return $this->redirectToRoute('app_like_new_commentary', ['idCommentary' => $idCommentary], Response::HTTP_SEE_OTHER);
    }
}
