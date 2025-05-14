<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Entity;

use Attribute;
use DateTimeInterface;

/**
 * Atributo para marcar fontes verificadas
 */
#[Attribute]
class Verified
{
    public function __construct(
        public readonly DateTimeInterface $verifiedAt,
        public readonly string $verifiedBy
    ) {
    }
}