<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class BanController extends AbstractController
{
    public function __invoke(Comment $comment): Comment
    {
        return $comment->setIsBanned(true);
    }
}
