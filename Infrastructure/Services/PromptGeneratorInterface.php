<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Services;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;

/**
 * Interface para serviço de geração de prompts para criação de artigos automotivos
 *
 * Esta interface define métodos para geração de prompts especializados com elementos
 * humanizantes, permitindo a criação de conteúdo personalizado para diferentes tipos
 * de artigos relacionados ao setor automotivo.
 */
interface PromptGeneratorInterface
{
    /**
     * Gera um prompt base para artigo automotivo a partir do contexto fornecido
     *
     * @param string $context Contexto principal do artigo
     * @param array<string> $keywords Palavras-chave relacionadas ao tópico
     * @param string $articleType Tipo de artigo (how-to, informativo, comparativo, etc.)
     * @param array<string, mixed> $options Opções adicionais para personalização do prompt
     * @return string Prompt base gerado
     */
    public function generateBasePrompt(
        string $context,
        array $keywords = [],
        string $articleType = 'informative',
        array $options = []
    ): string;

    /**
     * Personaliza um prompt com elementos humanizantes como persona e localização
     *
     * @param string $basePrompt Prompt base a ser enriquecido
     * @param HumanPersona $persona Persona humana a ser incorporada no artigo
     * @param BrazilianLocation $location Localização brasileira a ser contextualizada
     * @param array<ForumDiscussion> $discussions Discussões relevantes para enriquecer o conteúdo
     * @param array<string, mixed> $options Opções adicionais de personalização
     * @return string Prompt personalizado com elementos humanizantes
     */
    public function personalizePromptWithHumanElements(
        string $basePrompt,
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions = [],
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo de procedimento de manutenção
     *
     * @param string $maintenanceType Tipo de manutenção (troca de óleo, filtro, etc.)
     * @param VehicleReference $vehicle Referência do veículo alvo da manutenção
     * @param int $difficultyLevel Nível de dificuldade de 1 (básico) a 5 (avançado)
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo de manutenção
     */
    public function generateMaintenancePrompt(
        string $maintenanceType,
        VehicleReference $vehicle,
        int $difficultyLevel = 3,
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo de comparação entre veículos
     *
     * @param array<VehicleReference> $vehicles Lista de veículos a serem comparados
     * @param array<string> $comparisonAspects Aspectos a serem comparados (performance, segurança, etc.)
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo comparativo
     */
    public function generateVehicleComparisonPrompt(
        array $vehicles,
        array $comparisonAspects = [],
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo de diagnóstico de problemas
     *
     * @param string $problemDescription Descrição do problema ou sintoma
     * @param VehicleReference $vehicle Referência do veículo com o problema
     * @param array<string> $possibleCauses Possíveis causas do problema
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo de diagnóstico
     */
    public function generateTroubleshootingPrompt(
        string $problemDescription,
        VehicleReference $vehicle,
        array $possibleCauses = [],
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo sobre calibragem de pneus
     *
     * @param VehicleReference $vehicle Referência do veículo alvo
     * @param array<string, mixed> $tirePressureData Dados de pressão dos pneus para diferentes condições
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo sobre calibragem de pneus
     */
    public function generateTirePressurePrompt(
        VehicleReference $vehicle,
        array $tirePressureData = [],
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo sobre troca de óleo
     *
     * @param VehicleReference $vehicle Referência do veículo alvo
     * @param string $oilType Tipo de óleo recomendado
     * @param float $oilQuantity Quantidade de óleo em litros
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo sobre troca de óleo
     */
    public function generateOilChangePrompt(
        VehicleReference $vehicle,
        string $oilType,
        float $oilQuantity,
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo de novidades sobre um modelo
     *
     * @param VehicleReference $vehicle Referência do veículo
     * @param array<string> $newFeatures Novos recursos ou características
     * @param array<string, mixed> $marketData Dados de mercado relevantes
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo de novidades
     */
    public function generateVehicleNewsPrompt(
        VehicleReference $vehicle,
        array $newFeatures = [],
        array $marketData = [],
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo com dicas de direção econômica
     *
     * @param VehicleReference|null $vehicle Referência do veículo (opcional)
     * @param BrazilianLocation|null $location Localização específica (opcional)
     * @param array<string> $ecoTips Dicas específicas de economia
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo de direção econômica
     */
    public function generateEcoDrivingPrompt(
        ?VehicleReference $vehicle = null,
        ?BrazilianLocation $location = null,
        array $ecoTips = [],
        array $options = []
    ): string;

    /**
     * Gera um prompt especializado para artigo de uma experiência pessoal com veículo
     *
     * @param HumanPersona $persona Persona que relata a experiência
     * @param VehicleReference $vehicle Veículo relacionado à experiência
     * @param string $experienceType Tipo de experiência (viagem, teste, compra, etc.)
     * @param array<string, mixed> $experienceDetails Detalhes da experiência
     * @param array<string, mixed> $options Opções adicionais para personalização
     * @return string Prompt especializado para artigo de experiência pessoal
     */
    public function generatePersonalExperiencePrompt(
        HumanPersona $persona,
        VehicleReference $vehicle,
        string $experienceType,
        array $experienceDetails = [],
        array $options = []
    ): string;

    /**
     * Adapta um prompt existente para incluir informações de tráfego locais
     *
     * @param string $prompt Prompt original a ser adaptado
     * @param BrazilianLocation $location Localização com informações de tráfego
     * @param array<string, mixed> $trafficData Dados adicionais de tráfego
     * @return string Prompt adaptado com informações de tráfego
     */
    public function incorporateTrafficInfo(
        string $prompt,
        BrazilianLocation $location,
        array $trafficData = []
    ): string;

    /**
     * Incorpora insights de discussões de fórum em um prompt existente
     *
     * @param string $prompt Prompt original a ser enriquecido
     * @param array<ForumDiscussion> $discussions Discussões contendo insights relevantes
     * @param int $maxInsights Número máximo de insights a incorporar
     * @return string Prompt enriquecido com insights de discussões
     */
    public function incorporateForumInsights(
        string $prompt,
        array $discussions,
        int $maxInsights = 3
    ): string;
}