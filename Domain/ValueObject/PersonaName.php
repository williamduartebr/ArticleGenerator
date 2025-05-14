<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\ValueObject;

/**
 * Value Object para representar o nome de uma pessoa
 * 
 * Implementa uma representação imutável de um nome de pessoa,
 * com validações e métodos de formatação.
 */
readonly class PersonaName
{
    /**
     * @param string $firstName Primeiro nome
     * @param string $lastName Sobrenome
     * 
     * @throws \InvalidArgumentException Se o nome não atender aos requisitos
     */
    public function __construct(
        public string $firstName,
        public string $lastName
    ) {
        $this->validate();
    }

    /**
     * Valida os componentes do nome
     * 
     * @throws \InvalidArgumentException Se o nome não atender aos requisitos
     */
    private function validate(): void
    {
        if (empty(trim($this->firstName))) {
            throw new \InvalidArgumentException('O primeiro nome não pode ser vazio');
        }

        if (strlen(trim($this->firstName)) < 2) {
            throw new \InvalidArgumentException('O primeiro nome deve ter pelo menos 2 caracteres');
        }

        if (empty(trim($this->lastName))) {
            throw new \InvalidArgumentException('O sobrenome não pode ser vazio');
        }

        if (strlen(trim($this->lastName)) < 2) {
            throw new \InvalidArgumentException('O sobrenome deve ter pelo menos 2 caracteres');
        }

        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/u', $this->firstName)) {
            throw new \InvalidArgumentException('O primeiro nome contém caracteres inválidos');
        }

        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/u', $this->lastName)) {
            throw new \InvalidArgumentException('O sobrenome contém caracteres inválidos');
        }
    }

    /**
     * Retorna o nome completo (primeiro nome + sobrenome)
     */
    public function fullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    /**
     * Retorna as iniciais do nome
     */
    public function initials(): string
    {
        return mb_substr($this->firstName, 0, 1) . mb_substr($this->lastName, 0, 1);
    }

    /**
     * Retorna uma representação de string do nome
     */
    public function __toString(): string
    {
        return $this->fullName();
    }

    /**
     * Cria uma nova instância a partir de um nome completo
     * 
     * @param string $fullName Nome completo no formato "Primeiro Sobrenome"
     * @return self
     * @throws \InvalidArgumentException Se o nome completo não for válido
     */
    public static function fromFullName(string $fullName): self
    {
        $parts = explode(' ', trim($fullName), 2);
        
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('O nome completo deve conter primeiro nome e sobrenome');
        }
        
        return new self($parts[0], $parts[1]);
    }

    /**
     * Verifica se outro nome é igual a este
     */
    public function equals(self $other): bool
    {
        return $this->firstName === $other->firstName && 
               $this->lastName === $other->lastName;
    }
}