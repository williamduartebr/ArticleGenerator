<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

/**
 * Enum para padrões de tráfego
 */
enum TrafficPattern: string
{
    case LIGHT = 'light';
    case MODERATE = 'moderate';
    case HEAVY = 'heavy';
    case CONGESTED = 'congested';
}