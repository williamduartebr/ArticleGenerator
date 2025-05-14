<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Exception;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;

/**
 * Exceção lançada quando elementos humanizantes são incompatíveis entre si
 */
class ElementIncompatibilityException extends DomainException
{
    /**
     * @var array<string, mixed> $elements Elementos incompatíveis
     */
    private array $elements = [];
    
    /**
     * @var string $incompatibilityReason Razão da incompatibilidade
     */
    private string $incompatibilityReason;
    
    /**
     * Construtor
     * 
     * @param string $message Mensagem de erro
     * @param array<string, mixed> $elements Elementos incompatíveis
     * @param string $incompatibilityReason Razão da incompatibilidade
     * @param array<string, mixed> $context Contexto adicional
     */
    public function __construct(
        string $message,
        array $elements,
        string $incompatibilityReason,
        array $context = []
    ) {
        parent::__construct(
            $message,
            'ELEMENT_INCOMPATIBILITY',
            array_merge($context, ['elements' => $elements])
        );
        
        $this->elements = $elements;
        $this->incompatibilityReason = $incompatibilityReason;
    }
    
    /**
     * Cria uma exceção para incompatibilidade entre persona e localização
     * 
     * @param HumanPersona $persona Persona incompatível
     * @param BrazilianLocation $location Localização incompatível
     * @param string $reason Razão específica da incompatibilidade
     * @return self
     */
    public static function fromPersonaAndLocation(
        HumanPersona $persona,
        BrazilianLocation $location,
        string $reason
    ): self {
        $message = sprintf(
            'A persona "%s" (%s) é incompatível com a localização "%s". Razão: %s',
            (string)$persona->getName(),
            $persona->getProfession(),
            $location->getFullLocationName(),
            $reason
        );
        
        $elements = [
            'persona' => [
                'id' => $persona->id,
                'name' => (string)$persona->getName(),
                'profession' => $persona->getProfession()
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->getFullLocationName(),
                'state' => $location->getStateCode()->value
            ]
        ];
        
        return new self($message, $elements, $reason);
    }
    
    /**
     * Cria uma exceção para incompatibilidade entre discussão e outros elementos
     * 
     * @param ForumDiscussion $discussion Discussão incompatível
     * @param HumanPersona|null $persona Persona relacionada (opcional)
     * @param BrazilianLocation|null $location Localização relacionada (opcional)
     * @param string $reason Razão específica da incompatibilidade
     * @return self
     */
    public static function fromDiscussionIncompatibility(
        ForumDiscussion $discussion,
        ?HumanPersona $persona = null,
        ?BrazilianLocation $location = null,
        string $reason = 'Conteúdo incompatível'
    ): self {
        $context = '';
        $elements = [
            'discussion' => [
                'id' => $discussion->id,
                'title' => $discussion->getTitle(),
                'category' => $discussion->getCategory()->value
            ]
        ];
        
        if ($persona !== null) {
            $context .= ' persona "' . $persona->getName() . '"';
            $elements['persona'] = [
                'id' => $persona->id,
                'name' => (string)$persona->getName(),
                'profession' => $persona->getProfession()
            ];
        }
        
        if ($location !== null) {
            $context .= ($persona !== null ? ' e' : '') . ' localização "' . $location->getFullLocationName() . '"';
            $elements['location'] = [
                'id' => $location->id,
                'name' => $location->getFullLocationName(),
                'state' => $location->getStateCode()->value
            ];
        }
        
        $message = sprintf(
            'A discussão "%s" é incompatível com %s. Razão: %s',
            $discussion->getTitle(),
            $context ?: 'outros elementos',
            $reason
        );
        
        return new self($message, $elements, $reason);
    }
    
    /**
     * Cria uma exceção para incompatibilidade genérica entre múltiplos elementos
     * 
     * @param array<string, mixed> $elements Elementos incompatíveis
     * @param string $reason Razão da incompatibilidade
     * @return self
     */
    public static function fromIncompatibleElements(
        array $elements,
        string $reason
    ): self {
        $elementDescriptions = [];
        
        foreach ($elements as $type => $element) {
            if (is_object($element)) {
                $elementDescriptions[] = $type . ' "' . (string)$element . '"';
            } else {
                $elementDescriptions[] = $type;
            }
        }
        
        $message = sprintf(
            'Os elementos %s são incompatíveis. Razão: %s',
            implode(', ', $elementDescriptions),
            $reason
        );
        
        return new self($message, $elements, $reason);
    }
    
    /**
     * Retorna os elementos incompatíveis
     * 
     * @return array<string, mixed>
     */
    public function getElements(): array
    {
        return $this->elements;
    }
    
    /**
     * Retorna a razão da incompatibilidade
     * 
     * @return string
     */
    public function getIncompatibilityReason(): string
    {
        return $this->incompatibilityReason;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'elements' => $this->elements,
            'incompatibilityReason' => $this->incompatibilityReason
        ]);
    }
}