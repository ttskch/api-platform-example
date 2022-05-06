<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ItemPutLatestController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function __invoke(Post $data): ?Post
    {
        /** @var Post|null $post */
        $post = $this->postRepository->findOneBy([], ['id' => 'DESC']);

        return $post ? $post->setTitle($data->getTitle())->setBody($data->getBody()) : null;
    }
}
