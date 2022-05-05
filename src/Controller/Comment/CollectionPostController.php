<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class CollectionPostController extends AbstractController
{
    public function __invoke(Comment $data, ?Post $post): Comment
    {
        if (!$post) {
            throw new NotFoundHttpException();
        }

        return $data->setPost($post);
    }
}
