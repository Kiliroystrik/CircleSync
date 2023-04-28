<?php

namespace App\Services;

use App\Entity\Like;
use App\Entity\User;
use App\Repository\CommentaryRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

class LikeManagerService
{
    private $likeRepository;
    private $postRepository;
    private $commentaryRepository;
    private $entityManager;

    public function __construct(
        LikeRepository $likeRepository,
        PostRepository $postRepository,
        CommentaryRepository $commentaryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->likeRepository = $likeRepository;
        $this->postRepository = $postRepository;
        $this->commentaryRepository = $commentaryRepository;
        $this->entityManager = $entityManager;
    }

    public function addLike(string $type, int $targetId, User $user): bool
    {

        // search if this like already exists by type = $type and targetId = $targetId
        $existingLike = $this->likeRepository->findOneBy([$type => $targetId, 'user' => $user]);

        if ($existingLike) {
            return false;
        }

        $like = new Like();
        $like->setType($type);
        $like->setUser($user);

        if ($type == 'post') {
            $like->setPost($this->postRepository->find($targetId));
        } elseif ($type == 'commentary') {
            $like->setCommentary($this->commentaryRepository->find($targetId));
        }

        $this->entityManager->persist($like);
        $this->entityManager->flush();

        $this->refreshLikes($type, $targetId, $this->postRepository, $this->commentaryRepository);

        return true;
    }

    public function removeLike(string $type, int $targetId, User $user): bool
    {
        $like = $this->likeRepository->findOneBy([$type => $targetId, 'user' => $user]);
        if (!$like) {
            return false;
        }

        $this->entityManager->remove($like);
        $this->entityManager->flush();

        $this->refreshLikes($type, $targetId);

        return true;
    }

    public function refreshLikes(string $type, int $targetId): void
    {
        if ($type == 'post') {
            $post = $this->postRepository->find($targetId);
            $this->postRepository->updatePostLikes($post);
        } elseif ($type == 'commentary') {
            $commentary = $this->commentaryRepository->find($targetId);
            $this->commentaryRepository->updatePostLikes($commentary);
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }
}
