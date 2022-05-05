<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class CollectionPostController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function __invoke(Comment $data, int $id): Comment
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return $data->setPost($post);
    }
}
