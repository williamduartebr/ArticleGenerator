<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\Service;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\Entity\ForumCategory;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Serviço de domínio para validação de compatibilidade entre elementos
 */
class ElementCompatibilityValidator implements ElementCompatibilityValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function areElementsCompatible(
        HumanPersona $persona,
        BrazilianLocation $location,
        ?ForumDiscussion $discussion = null
    ): bool {
        // Verifica compatibilidade entre persona e localização
        $personaLocationCompatible = $this->isPersonaCompatibleWithLocation($persona, $location);
        
        if (!$personaLocationCompatible) {
            return false;
        }
        
        // Se não houver discussão, retorna o resultado da compatibilidade persona-localização
        if ($discussion === null) {
            return true;
        }
        
        // Verifica compatibilidade da discussão com persona e localização
        $discussionPersonaCompatible = $this->isDiscussionCompatibleWithPersona($discussion, $persona);
        $discussionLocationCompatible = $this->isDiscussionCompatibleWithLocation($discussion, $location);
        
        return $discussionPersonaCompatible && $discussionLocationCompatible;
    }

    /**
     * {@inheritDoc}
     */
    public function isPersonaCompatibleWithLocation(
        HumanPersona $persona,
        BrazilianLocation $location
    ): bool {
        // Verifica se a persona já está associada à localização
        if ($persona->getLocation() === $location->getCity()) {
            return true;
        }
        
        // Verifica se a região da localização faz sentido para a profissão da persona
        // Este é um exemplo simplificado, em uma implementação real teríamos lógicas mais complexas
        
        // Exemplo: certas profissões são mais compatíveis com áreas urbanas
        $urbanProfessions = [
            'desenvolvedor', 'programador', 'analista', 'advogado', 'médico',
            'engenheiro', 'arquiteto', 'designer', 'consultor', 'executivo'
        ];
        
        $profession = strtolower($persona->getProfession());
        $isUrbanProfession = false;
        
        foreach ($urbanProfessions as $urbanProfession) {
            if (str_contains($profession, $urbanProfession)) {
                $isUrbanProfession = true;
                break;
            }
        }
        
        // Capitais e grandes cidades são compatíveis com profissões urbanas
        $majorCities = [
            'São Paulo', 'Rio de Janeiro', 'Brasília', 'Belo Horizonte',
            'Salvador', 'Fortaleza', 'Recife', 'Porto Alegre', 'Curitiba',
            'Manaus', 'Belém', 'Goiânia', 'Guarulhos', 'Campinas'
        ];
        
        $isMajorCity = in_array($location->getCity(), $majorCities);
        
        // Se for uma profissão urbana e a localização não for uma grande cidade,
        // ou se não for uma profissão urbana e a localização for uma grande cidade,
        // há uma incompatibilidade potencial
        if (($isUrbanProfession && !$isMajorCity) || (!$isUrbanProfession && $isMajorCity)) {
            // Mas não consideramos isso como uma incompatibilidade forte
            // Pessoas podem ter profissões urbanas em cidades menores e vice-versa
            // Aqui poderíamos retornar false para casos específicos
        }
        
        // Por padrão, consideramos compatível
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isDiscussionCompatibleWithPersona(
        ForumDiscussion $discussion,
        HumanPersona $persona
    ): bool {
        // Verifica se a discussão tem relação com os veículos preferidos da persona
        $personaVehicles = $persona->getPreferredVehicles();
        
        if (!empty($personaVehicles)) {
            foreach ($personaVehicles as $vehicle) {
                // Verifica se o título ou conteúdo da discussão menciona algum veículo preferido
                if (str_contains(strtolower($discussion->getTitle()), strtolower($vehicle)) ||
                    str_contains(strtolower($discussion->getContent()), strtolower($vehicle))) {
                    return true;
                }
            }
        }
        
        // Verifica se a categoria da discussão é compatível com o perfil da persona
        // Exemplo: discussões técnicas são mais compatíveis com personas de profissões técnicas
        $discussionCategory = $discussion->getCategory();
        $isTechnicalProfession = str_contains(strtolower($persona->getProfession()), 'engenheiro') ||
                              str_contains(strtolower($persona->getProfession()), 'mecânico') ||
                              str_contains(strtolower($persona->getProfession()), 'técnico');
        
        if ($isTechnicalProfession && 
            ($discussionCategory === ForumCategory::MAINTENANCE || 
             $discussionCategory === ForumCategory::TROUBLESHOOTING ||
             $discussionCategory === ForumCategory::MODIFICATION)) {
            return true;
        }
        
        // Por padrão, se não encontramos incompatibilidades específicas, consideramos compatível
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isDiscussionCompatibleWithLocation(
        ForumDiscussion $discussion,
        BrazilianLocation $location
    ): bool {
        // Verifica se a discussão menciona a cidade ou estado da localização
        $city = $location->getCity();
        $state = $location->getStateCode()->value;
        
        if (str_contains(strtolower($discussion->getTitle()), strtolower($city)) ||
            str_contains(strtolower($discussion->getContent()), strtolower($city)) ||
            str_contains(strtolower($discussion->getTitle()), strtolower($state)) ||
            str_contains(strtolower($discussion->getContent()), strtolower($state))) {
            return true;
        }
        
        // Verifica se o padrão de tráfego da localização é mencionado na discussão
        $trafficPattern = strtolower($location->getTrafficPattern()->value);
        $trafficKeywords = [
            'congestionado', 'trânsito', 'tráfego', 'engarrafamento',
            'fluxo', 'lento', 'rápido', 'livre', 'intenso'
        ];
        
        foreach ($trafficKeywords as $keyword) {
            if (str_contains(strtolower($discussion->getContent()), $keyword)) {
                return true;
            }
        }
        
        // Por padrão, se não encontramos incompatibilidades específicas, consideramos compatível
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getContextCompatibilityScore(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions,
        string $context
    ): float {
        // Inicializa com um score base
        $totalScore = 0.7;
        $factorsCount = 1;
        
        // Extrai palavras-chave do contexto
        $contextWords = explode(' ', strtolower($context));
        $relevantWords = array_filter($contextWords, fn($word) => strlen($word) > 3);
        
        // Verifica se a persona tem relação com o contexto
        $personaInfo = strtolower($persona->getProfession() . ' ' . $persona->getName());
        $personaScore = 0;
        
        foreach ($relevantWords as $word) {
            if (str_contains($personaInfo, $word)) {
                $personaScore += 0.1;
            }
        }
        
        if (!empty($persona->getPreferredVehicles())) {
            foreach ($persona->getPreferredVehicles() as $vehicle) {
                if (str_contains(strtolower($context), strtolower($vehicle))) {
                    $personaScore += 0.2;
                }
            }
        }
        
        $totalScore += min(0.3, $personaScore);
        $factorsCount++;
        
        // Verifica se a localização tem relação com o contexto
        $locationInfo = strtolower($location->getCity() . ' ' . $location->getRegion() . ' ' . $location->getStateCode()->value);
        $locationScore = 0;
        
        foreach ($relevantWords as $word) {
            if (str_contains($locationInfo, $word)) {
                $locationScore += 0.1;
            }
        }
        
        $totalScore += min(0.3, $locationScore);
        $factorsCount++;
        
        // Verifica se as discussões têm relação com o contexto
        $discussionScore = 0;
        
        foreach ($discussions as $discussion) {
            $discussionContent = strtolower($discussion->getTitle() . ' ' . $discussion->getContent());
            $discussionRelevance = 0;
            
            foreach ($relevantWords as $word) {
                if (str_contains($discussionContent, $word)) {
                    $discussionRelevance += 0.05;
                }
            }
            
            $discussionScore += min(0.1, $discussionRelevance);
        }
        
        $totalScore += min(0.3, $discussionScore);
        $factorsCount++;
        
        // Normaliza o score para garantir que esteja entre 0.0 e 1.0
        return min(1.0, $totalScore / $factorsCount);
    }

    /**
     * {@inheritDoc}
     */
    public function identifyCompatibilityIssues(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions
    ): array {
        $issues = [];
        
        // Verifica compatibilidade entre persona e localização
        if (!$this->isPersonaCompatibleWithLocation($persona, $location)) {
            $issues[] = "A profissão '{$persona->getProfession()}' não é comum na localização '{$location->getFullLocationName()}'";
        }
        
        // Verifica se a persona tem veículos preferidos
        if (empty($persona->getPreferredVehicles())) {
            $issues[] = "A persona não possui veículos preferidos definidos";
        }
        
        // Verifica compatibilidade com discussões
        foreach ($discussions as $index => $discussion) {
            if (!$this->isDiscussionCompatibleWithPersona($discussion, $persona)) {
                $issues[] = "A discussão '{$discussion->getTitle()}' não é compatível com o perfil da persona";
            }
            
            if (!$this->isDiscussionCompatibleWithLocation($discussion, $location)) {
                $issues[] = "A discussão '{$discussion->getTitle()}' não tem relação com a localização '{$location->getFullLocationName()}'";
            }
        }
        
        // Verifica se há pelo menos uma discussão
        if (empty($discussions)) {
            $issues[] = "Não há discussões associadas ao conjunto de elementos";
        }
        
        return $issues;
    }

    /**
     * {@inheritDoc}
     */
    public function suggestCompatibilityAdjustments(
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions
    ): array {
        $suggestions = [];
        
        // Identifica os problemas existentes
        $issues = $this->identifyCompatibilityIssues($persona, $location, $discussions);
        
        if (empty($issues)) {
            return [
                'status' => 'compatible',
                'message' => 'O conjunto de elementos já é compatível, não são necessários ajustes'
            ];
        }
        
        // Sugere ajustes com base nos problemas identificados
        foreach ($issues as $issue) {
            if (str_contains($issue, "profissão") && str_contains($issue, "localização")) {
                // Sugestões para incompatibilidade profissão-localização
                $suggestions[] = [
                    'type' => 'location_change',
                    'message' => "Considere mudar a localização para uma cidade maior que seja mais compatível com a profissão da persona"
                ];
                
                $suggestions[] = [
                    'type' => 'persona_update',
                    'message' => "Adicione detalhes específicos sobre como a persona se adaptou a viver nesta localização com sua profissão"
                ];
            }
            
            if (str_contains($issue, "veículos preferidos")) {
                // Sugestões para falta de veículos preferidos
                $suggestions[] = [
                    'type' => 'persona_update',
                    'message' => "Adicione pelo menos um veículo preferido para a persona, preferencialmente relacionado ao contexto do artigo"
                ];
            }
            
            if (str_contains($issue, "discussão") && str_contains($issue, "perfil da persona")) {
                // Sugestões para incompatibilidade discussão-persona
                $suggestions[] = [
                    'type' => 'discussion_change',
                    'message' => "Selecione discussões que mencionem veículos ou temas relacionados à profissão ou interesses da persona"
                ];
            }
            
            if (str_contains($issue, "discussão") && str_contains($issue, "localização")) {
                // Sugestões para incompatibilidade discussão-localização
                $suggestions[] = [
                    'type' => 'discussion_change',
                    'message' => "Selecione discussões que mencionem a cidade, estado ou região da localização"
                ];
            }
            
            if (str_contains($issue, "Não há discussões")) {
                // Sugestões para falta de discussões
                $suggestions[] = [
                    'type' => 'discussion_add',
                    'message' => "Adicione pelo menos uma discussão relevante para o contexto do artigo"
                ];
            }
        }
        
        // Elimina sugestões duplicadas
        $uniqueSuggestions = [];
        
        foreach ($suggestions as $suggestion) {
            $key = $suggestion['type'] . '_' . $suggestion['message'];
            $uniqueSuggestions[$key] = $suggestion;
        }
        
        return [
            'status' => 'needs_adjustment',
            'issues' => $issues,
            'suggestions' => array_values($uniqueSuggestions)
        ];
    }
}