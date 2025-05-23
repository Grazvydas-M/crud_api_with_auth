<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PostRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $content;
}
