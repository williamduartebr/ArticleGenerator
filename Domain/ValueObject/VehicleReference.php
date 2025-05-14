<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\ValueObject;

/**
 * Value Object para representar uma referência a um veículo
 * 
 * Implementa uma representação imutável de uma referência a um veículo,
 * com validações e métodos de formatação.
 */
readonly class VehicleReference
{
    /**
     * @param string $make Marca do veículo
     * @param string $model Modelo do veículo
     * @param int $year Ano de fabricação do veículo
     * @param string|null $version Versão do veículo (opcional)
     * 
     * @throws \InvalidArgumentException Se os dados não atenderem aos requisitos
     */
    public function __construct(
        public string $make,
        public string $model,
        public int $year,
        public ?string $version = null
    ) {
        $this->validate();
    }

    /**
     * Valida os componentes da referência ao veículo
     * 
     * @throws \InvalidArgumentException Se os dados não atenderem aos requisitos
     */
    private function validate(): void
    {
        if (empty(trim($this->make))) {
            throw new \InvalidArgumentException('A marca do veículo não pode ser vazia');
        }

        if (empty(trim($this->model))) {
            throw new \InvalidArgumentException('O modelo do veículo não pode ser vazio');
        }

        $currentYear = (int) date('Y');
        
        if ($this->year < 1900 || $this->year > ($currentYear + 1)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'O ano do veículo deve estar entre 1900 e %d',
                    $currentYear + 1
                )
            );
        }
    }

    /**
     * Retorna a descrição completa do veículo (marca, modelo, versão e ano)
     */
    public function fullDescription(): string
    {
        $baseDescription = "{$this->make} {$this->model}";
        
        if ($this->version) {
            $baseDescription .= " {$this->version}";
        }
        
        return "{$baseDescription} {$this->year}";
    }

    /**
     * Retorna a descrição curta do veículo (marca e modelo)
     */
    public function shortDescription(): string
    {
        return "{$this->make} {$this->model}";
    }

    /**
     * Retorna uma representação de string do veículo
     */
    public function __toString(): string
    {
        return $this->fullDescription();
    }

    /**
     * Verifica se outra referência de veículo é igual a esta
     */
    public function equals(self $other): bool
    {
        return $this->make === $other->make && 
               $this->model === $other->model && 
               $this->year === $other->year && 
               $this->version === $other->version;
    }

    /**
     * Verifica se este veículo é da mesma marca que o outro
     */
    public function isSameMake(self $other): bool
    {
        return strtolower($this->make) === strtolower($other->make);
    }

    /**
     * Verifica se este veículo é do mesmo modelo que o outro (ignora versão)
     */
    public function isSameModel(self $other): bool
    {
        return strtolower($this->make) === strtolower($other->make) &&
               strtolower($this->model) === strtolower($other->model);
    }

    /**
     * Verifica se este veículo é mais novo que o outro
     */
    public function isNewerThan(self $other): bool
    {
        return $this->year > $other->year;
    }

    /**
     * Retorna a idade do veículo baseada no ano atual
     */
    public function getAge(): int
    {
        return (int) date('Y') - $this->year;
    }

    /**
     * Verifica se o veículo é considerado novo (até 2 anos)
     */
    public function isNew(): bool
    {
        return $this->getAge() <= 2;
    }

    /**
     * Cria uma instância a partir de uma string formatada
     * 
     * @param string $formattedString String no formato "Marca Modelo Versão Ano"
     * @return self
     * @throws \InvalidArgumentException Se a string não estiver no formato correto
     */
    public static function fromString(string $formattedString): self
    {
        $parts = preg_split('/\s+/', trim($formattedString));
        
        if (count($parts) < 3) {
            throw new \InvalidArgumentException('O formato deve ser pelo menos "Marca Modelo Ano"');
        }
        
        // O último elemento é sempre o ano
        $year = (int) array_pop($parts);
        
        // O primeiro elemento é sempre a marca
        $make = array_shift($parts);
        
        // O segundo elemento é sempre o modelo
        $model = array_shift($parts);
        
        // Qualquer coisa restante é considerada parte da versão
        $version = !empty($parts) ? implode(' ', $parts) : null;
        
        return new self($make, $model, $year, $version);
    }
}