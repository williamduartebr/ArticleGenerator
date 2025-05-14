<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

/**
 * Enum para tipos de fontes de conteúdo
 */
enum ContentSourceType: string
{
    case FORUM = 'forum';
    case SOCIAL_MEDIA = 'social_media';
    case BLOG = 'blog';
    case NEWS = 'news';
    case REVIEW = 'review';
    case OFFICIAL = 'official';
    case OTHER = 'other';
}