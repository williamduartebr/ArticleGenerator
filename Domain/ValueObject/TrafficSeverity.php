<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\ValueObject;

/**
 * Enum para severidade de condições de tráfego
 */
enum TrafficSeverity: string
{
    case NONE = 'none';
    case MILD = 'mild';
    case MODERATE = 'moderate';
    case SEVERE = 'severe';
    case CRITICAL = 'critical';
}