<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;

/**
 * Interface para o serviço de validação de compatibilidade entre elementos
 */
interface ElementCompatibilityValidatorInterface
{
    /**
     * Verifica se os elementos são compatíveis entre si
     * 
     * @param HumanPersona $persona A persona a verificar
     * @param BrazilianLocation $location A localização a verificar
     * @param ForumDiscussion|null $discussion A discussão a verificar (opcional)
     * @return bool Verdadeiro se os elementos são compatíveis
     */
    public function areElementsCompatible(
        HumanPersona $persona,
        BrazilianLocation $location,
        ?ForumDiscussion $discussion = null
    ): bool;
    
    /**
     * Verifica se a persona é compatível com a localização
     * 
     * @param HumanPersona $persona A persona a verificar
     * @param BrazilianLocation $location A localização a verificar
     * @return bool Verdadeiro se os elementos são compatíveis
     */
    public function isPersonaCompatibleWithLocation(
        HumanPersona $persona,
        BrazilianLocation $location
    ): bool;
    
    /**
     * Verifica se a discussão é compatível com a persona
     * 
     * @param ForumDiscussion $discussion A discussão a verificar
     * @param HumanPersona $persona A persona a verificar
     * @return bool Verdadeiro se os elementos são compatíveis
     */
    public function isDiscussionCompatibleWithPersona(
        ForumDiscussion $discussion,
        HumanPersona $persona
    ): bool;
    
    /**
     * Verifica se a discussão é compatível com a localização
     * 
     * @param ForumDiscussion $discussion A discussão a verificar
     * @param BrazilianLocation $location A localização a verificar
     * @return bool Verdadeiro se os elementos são compatíveis
     */
    public function isDiscussionCompatibleWithLocation(
        ForumDiscussion $discussion,
        BrazilianLocation $location
    ): bool;
    
    /**
     * Verifica se o conjunto de elementos é compatível com um contexto
     * 
     * @param HumanPersona $persona A persona
     * @param BrazilianLocation $location A localização
     * @param array<ForumDiscussion> $discussions As discussões
     * @param string $context O contexto do artigo
     * @return float Score de compatibilidade (0.0 a 1.0)
     */
    public function getContextCompatibilityScore(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions,
        string $context
    ): float;
    
    /**
     * Identifica problemas de compatibilidade entre elementos
     * 
     * @param HumanPersona $persona A persona
     * @param BrazilianLocation $location A localização
     * @param array<ForumDiscussion> $discussions As discussões
     * @return array<string> Lista de problemas identificados
     */
    public function identifyCompatibilityIssues(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions
    ): array;
    
    /**
     * Sugere ajustes para tornar elementos mais compatíveis
     * 
     * @param HumanPersona $persona A persona
     * @param BrazilianLocation $location A localização
     * @param array<ForumDiscussion> $discussions As discussões
     * @return array<string, mixed> Sugestões de ajustes
     */
    public function suggestCompatibilityAdjustments(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions
    ): array;
}