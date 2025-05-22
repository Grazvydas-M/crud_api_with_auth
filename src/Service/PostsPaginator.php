<?php

namespace App\Service;

use App\Entity\Post;
use App\Repository\PostRepository;

readonly class PostsPaginator
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    /**
     * @return Post[]
     */
    public function paginate(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->postRepository->getPosts($offset, $limit);
    }
}
