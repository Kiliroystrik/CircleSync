<?php

namespace App\Controller;

use App\Services\LikeManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/like')]
class LikeController extends AbstractController
{


    #[Route('/addLike/post/{idPost}', name: 'app_like_add_post', methods: ['GET', 'POST'])]
    public function addPostLike(Request $request, $idPost, LikeManagerService $likeManagerService): Response
    {
        if ($likeManagerService->addLike('post', $idPost, $this->getUser())) {

            // redirect to referer
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }



    #[Route('/removeLike/post/{idPost}', name: 'app_like_remove_post', methods: ['GET', 'POST'])]
    public function removePostLike(Request $request, $idPost, LikeManagerService $likeManagerService): Response
    {


        if ($likeManagerService->removeLike('post', $idPost, $this->getUser())) {

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }



    #[Route('/addLike/commentary/{idCommentary}', name: 'app_like_add_commentary', methods: ['GET', 'POST'])]
    public function addCommentary(Request $request, $idCommentary, LikeManagerService $likeManagerService): Response
    {
        if ($likeManagerService->addLike('commentary', $idCommentary, $this->getUser())) {

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }



    #[Route('/removeLike/commentary/{idCommentary}', name: 'app_like_remove_commentary', methods: ['GET', 'POST'])]
    public function removeCommentary(Request $request, $idCommentary, LikeManagerService $likeManagerService): Response
    {
        if ($likeManagerService->removeLike('commentary', $idCommentary, $this->getUser())) {

            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }
}
