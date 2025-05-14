<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\ValueObject;

/**
 * Value Object para representar uma profissão
 * 
 * Implementa uma representação imutável de uma profissão,
 * com validações e métodos para verificar a categoria.
 */
readonly class Profession
{
    /**
     * @param string $title Título da profissão
     * @param ProfessionCategory $category Categoria da profissão
     * 
     * @throws \InvalidArgumentException Se o título não atender aos requisitos
     */
    public function __construct(
        public string $title,
        public ProfessionCategory $category
    ) {
        $this->validate();
    }

    /**
     * Valida os componentes da profissão
     * 
     * @throws \InvalidArgumentException Se o título não atender aos requisitos
     */
    private function validate(): void
    {
        if (empty(trim($this->title))) {
            throw new \InvalidArgumentException('O título da profissão não pode ser vazio');
        }

        if (strlen(trim($this->title)) < 3) {
            throw new \InvalidArgumentException('O título da profissão deve ter pelo menos 3 caracteres');
        }
    }

    /**
     * Verifica se a profissão está na categoria especificada
     */
    public function isInCategory(ProfessionCategory $category): bool
    {
        return $this->category === $category;
    }

    /**
     * Retorna uma representação de string da profissão
     */
    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * Verifica se outra profissão é igual a esta
     */
    public function equals(self $other): bool
    {
        return $this->title === $other->title && 
               $this->category === $other->category;
    }

    /**
     * Cria uma profissão a partir do título, inferindo a categoria
     * 
     * @param string $title Título da profissão
     * @return self
     */
    public static function fromTitle(string $title): self
    {
        $title = trim($title);
        $loweredTitle = strtolower($title);
        
        $category = match(true) {
            str_contains($loweredTitle, 'desenvolvedor') || 
            str_contains($loweredTitle, 'programador') || 
            str_contains($loweredTitle, 'analista de sistema') => ProfessionCategory::TECHNOLOGY,
            
            str_contains($loweredTitle, 'médico') || 
            str_contains($loweredTitle, 'enfermeiro') || 
            str_contains($loweredTitle, 'fisioterapeuta') => ProfessionCategory::HEALTHCARE,
            
            str_contains($loweredTitle, 'professor') || 
            str_contains($loweredTitle, 'educador') || 
            str_contains($loweredTitle, 'pedagogo') => ProfessionCategory::EDUCATION,
            
            str_contains($loweredTitle, 'contador') || 
            str_contains($loweredTitle, 'financeiro') || 
            str_contains($loweredTitle, 'bancário') => ProfessionCategory::FINANCE,
            
            str_contains($loweredTitle, 'advogado') || 
            str_contains($loweredTitle, 'juiz') || 
            str_contains($loweredTitle, 'jurídico') => ProfessionCategory::LEGAL,
            
            str_contains($loweredTitle, 'designer') || 
            str_contains($loweredTitle, 'artista') || 
            str_contains($loweredTitle, 'produtor') => ProfessionCategory::CREATIVE,
            
            str_contains($loweredTitle, 'eletricista') || 
            str_contains($loweredTitle, 'mecânico') || 
            str_contains($loweredTitle, 'carpinteiro') => ProfessionCategory::TRADE,
            
            str_contains($loweredTitle, 'engenheiro') || 
            str_contains($loweredTitle, 'arquiteto') => ProfessionCategory::ENGINEERING,
            
            str_contains($loweredTitle, 'gerente') || 
            str_contains($loweredTitle, 'diretor') || 
            str_contains($loweredTitle, 'coordenador') => ProfessionCategory::MANAGEMENT,
            
            str_contains($loweredTitle, 'vendedor') || 
            str_contains($loweredTitle, 'representante') || 
            str_contains($loweredTitle, 'comercial') => ProfessionCategory::SALES,
            
            str_contains($loweredTitle, 'atendente') || 
            str_contains($loweredTitle, 'garçom') || 
            str_contains($loweredTitle, 'recepcionista') => ProfessionCategory::SERVICE,
            
            default => ProfessionCategory::OTHER
        };
        
        return new self($title, $category);
    }

    /**
     * Verifica se a profissão é do setor tecnológico
     */
    public function isTechnical(): bool
    {
        return in_array($this->category, [
            ProfessionCategory::TECHNOLOGY,
            ProfessionCategory::ENGINEERING,
            ProfessionCategory::TRADE
        ]);
    }

    /**
     * Verifica se a profissão é do setor criativo
     */
    public function isCreative(): bool
    {
        return $this->category === ProfessionCategory::CREATIVE;
    }

    /**
     * Verifica se a profissão é do setor de atendimento
     */
    public function isServiceOriented(): bool
    {
        return in_array($this->category, [
            ProfessionCategory::SERVICE,
            ProfessionCategory::HEALTHCARE,
            ProfessionCategory::EDUCATION
        ]);
    }
}