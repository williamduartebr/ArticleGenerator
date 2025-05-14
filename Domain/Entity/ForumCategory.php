<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

/**
 * Enum para categorias de discussão em fóruns
 */
enum ForumCategory: string
{
    case MAINTENANCE = 'maintenance';
    case PERFORMANCE = 'performance';
    case MODIFICATION = 'modification';
    case TROUBLESHOOTING = 'troubleshooting';
    case PURCHASE = 'purchase';
    case COMPARISON = 'comparison';
    case NEWS = 'news';
    case OTHER = 'other';
}