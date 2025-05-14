<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\ValueObject;

/**
 * Enum para categorias de profissões
 */
enum ProfessionCategory: string
{
    case TECHNOLOGY = 'technology';
    case HEALTHCARE = 'healthcare';
    case EDUCATION = 'education';
    case FINANCE = 'finance';
    case LEGAL = 'legal';
    case CREATIVE = 'creative';
    case TRADE = 'trade';
    case ENGINEERING = 'engineering';
    case MANAGEMENT = 'management';
    case SALES = 'sales';
    case SERVICE = 'service';
    case OTHER = 'other';
}