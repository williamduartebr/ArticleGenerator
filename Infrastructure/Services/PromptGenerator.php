<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Infrastructure\Services;

use Src\ArticleGenerator\Domain\Entity\HumanPersona;
use Src\ArticleGenerator\Domain\Entity\BrazilianLocation;
use Src\ArticleGenerator\Domain\Entity\ForumDiscussion;
use Src\ArticleGenerator\Domain\Repository\HumanPersonaRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\BrazilianLocationRepositoryInterface;
use Src\ArticleGenerator\Domain\Repository\ForumDiscussionRepositoryInterface;
use Src\ArticleGenerator\Domain\ValueObject\VehicleReference;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Serviço para geração de prompts personalizados para criação de artigos automotivos
 * 
 * Este serviço implementa métodos para geração de prompts especializados utilizando
 * elementos humanizantes como personas, localizações e discussões de fóruns para
 * criar conteúdo personalizado e contextualizado.
 */
class PromptGenerator implements PromptGeneratorInterface
{
    /**
     * Variações de introduções para prompts para garantir diversidade
     * 
     * @var array<string> 
     */
    private array $promptIntroductions = [
        "Crie um artigo detalhado sobre",
        "Escreva um conteúdo aprofundado sobre",
        "Elabore um texto informativo sobre",
        "Produza um artigo completo sobre",
        "Desenvolva um conteúdo explicativo sobre",
        "Redija um texto especializado sobre",
        "Componha um artigo técnico sobre",
        "Formule um texto abrangente sobre",
        "Prepare um material detalhado sobre",
        "Construa um artigo estruturado sobre"
    ];

    /**
     * Variações de fechamentos para prompts para garantir diversidade
     * 
     * @var array<string>
     */
    private array $promptClosings = [
        "O artigo deve ser escrito em português brasileiro, com linguagem acessível e técnica quando necessário.",
        "Use português brasileiro, com termos técnicos explicados de forma acessível quando necessário.",
        "Utilize português do Brasil, com tom informativo e didático, incluindo terminologia técnica quando apropriado.",
        "Escreva em português brasileiro, em um estilo que equilibre precisão técnica e clareza para o leitor.",
        "Redija em português do Brasil, com clareza e precisão técnica, sem jargões desnecessários."
    ];

    /**
     * Construtor com injeção de dependências para os repositórios
     *
     * @param HumanPersonaRepositoryInterface $personaRepository Repositório de personas
     * @param BrazilianLocationRepositoryInterface $locationRepository Repositório de localizações
     * @param ForumDiscussionRepositoryInterface $discussionRepository Repositório de discussões
     */
    public function __construct(
        private readonly HumanPersonaRepositoryInterface $personaRepository,
        private readonly BrazilianLocationRepositoryInterface $locationRepository,
        private readonly ForumDiscussionRepositoryInterface $discussionRepository
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function generateBasePrompt(
        string $context,
        array $keywords = [],
        string $articleType = 'informative',
        array $options = []
    ): string {
        // Seleciona aleatoriamente uma introdução e fechamento para variabilidade
        $introduction = $this->promptIntroductions[array_rand($this->promptIntroductions)];
        $closing = $this->promptClosings[array_rand($this->promptClosings)];
        
        // Define a estrutura base do artigo conforme o tipo
        $structureInstructions = $this->getArticleStructureInstructions($articleType);
        
        // Formata as palavras-chave para inclusão no prompt
        $keywordsText = !empty($keywords) 
            ? "Inclua as seguintes palavras-chave: " . implode(', ', $keywords) . "." 
            : "";
        
        // Processa opções adicionais
        $targetWordCount = $options['wordCount'] ?? 800;
        $targetAudience = $options['targetAudience'] ?? 'Proprietários de veículos com interesse em manutenção automotiva';
        $tone = $options['tone'] ?? 'informativo e útil';
        
        // Constrói o prompt base
        $prompt = <<<PROMPT
{$introduction} {$context}.

{$keywordsText}

{$structureInstructions}

O artigo deve ter aproximadamente {$targetWordCount} palavras e ser direcionado para: {$targetAudience}.
O tom deve ser {$tone}.

{$closing}
PROMPT;

        // Log para debug (opcional)
        if ($options['debug'] ?? false) {
            Log::debug('Prompt base gerado:', ['prompt' => $prompt]);
        }
        
        return $prompt;
    }

    /**
     * {@inheritDoc}
     */
    public function personalizePromptWithHumanElements(
        string $basePrompt,
        HumanPersona $persona,
        BrazilianLocation $location,
        array $discussions = [],
        array $options = []
    ): string {
        // Extrai informações da persona
        $personaFullName = is_string($persona->getName()) ? $persona->getName() : (string)$persona->getName();
        $personaProfession = $persona->getProfession();
        $personaVehicles = implode(', ', $persona->getPreferredVehicles());
        
        // Extrai informações da localização
        $locationCity = $location->getCity();
        $locationState = $location->getStateCode()->value;
        $trafficPattern = $location->getTrafficPattern()->value;
        
        // Define o estilo de escrita baseado no perfil da persona
        $writingStyle = $this->determineWritingStyle($persona);
        
        // Extrai insights das discussões
        $insights = $this->extractInsightsFromDiscussions($discussions);
        
        // Constrói a instrução de personalização
        $personalizationInstruction = <<<PERSONALIZATION

<persona>
Escreva este artigo do ponto de vista de {$personaFullName}, um(a) {$personaProfession} de {$locationCity}-{$locationState}. 
{$personaFullName} tem preferência por veículos como: {$personaVehicles}.
</persona>

<local>
O artigo deve contextualizar informações para a região de {$locationCity}, que tem um padrão de tráfego "{$trafficPattern}".
Considere elementos locais e regionais relevantes para motoristas desta cidade.
</local>

<estilo>
{$writingStyle}
</estilo>

PERSONALIZATION;

        // Adiciona insights de discussões se disponíveis
        if (!empty($insights)) {
            $personalizationInstruction .= <<<INSIGHTS

<insights>
Incorpore os seguintes insights de discussões reais:
{$insights}
</insights>

INSIGHTS;
        }

        // Adiciona instrução para assinatura do autor
        $personalizationInstruction .= <<<SIGNATURE

Ao final do artigo, inclua uma breve assinatura: "Por {$personaFullName}, {$personaProfession} em {$locationCity}-{$locationState}."

SIGNATURE;

        // Combina o prompt base com as instruções de personalização
        return $basePrompt . "\n" . $personalizationInstruction;
    }

    /**
     * {@inheritDoc}
     */
    public function generateMaintenancePrompt(
        string $maintenanceType,
        VehicleReference $vehicle,
        int $difficultyLevel = 3,
        array $options = []
    ): string {
        // Informações do veículo
        $vehicleDesc = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
        
        // Determinação do nível de dificuldade em texto
        $difficultyText = match($difficultyLevel) {
            1 => "muito básico (iniciante total)",
            2 => "básico (iniciante)",
            3 => "intermediário",
            4 => "avançado",
            5 => "muito avançado (nível profissional)",
            default => "intermediário"
        };
        
        // Ferramentas necessárias (opcionais)
        $toolsText = "";
        if (isset($options['tools']) && !empty($options['tools'])) {
            $toolsText = "As ferramentas necessárias incluem: " . implode(", ", $options['tools']) . ".";
        }
        
        // Tempo estimado (opcional)
        $timeText = "";
        if (isset($options['estimatedTime'])) {
            $timeText = "O procedimento leva aproximadamente {$options['estimatedTime']} para ser concluído.";
        }
        
        // Constrói o prompt especializado
        $context = "como realizar a manutenção de {$maintenanceType} em um {$vehicleDesc}";
        $keywords = [
            $maintenanceType,
            $vehicle->make,
            $vehicle->model,
            "manutenção",
            "passo a passo"
        ];
        
        // Opções específicas para manutenção
        $maintenanceOptions = [
            'wordCount' => $options['wordCount'] ?? 1200,
            'tone' => 'técnico e didático',
            'targetAudience' => "Proprietários de {$vehicleDesc} com nível {$difficultyText} de conhecimento mecânico"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'how-to', array_merge($options, $maintenanceOptions));
        
        // Adiciona instruções específicas de manutenção
        $maintenanceInstructions = <<<MAINTENANCE

<instruções_específicas>
O artigo deve:
1. Incluir uma introdução explicando a importância desta manutenção para um {$vehicleDesc}
2. Listar os materiais e ferramentas necessários antes de começar
3. Fornecer instruções passo a passo com detalhes técnicos precisos
4. Incluir dicas de segurança relevantes
5. Mencionar problemas comuns que podem surgir durante o procedimento
6. Sugerir a frequência recomendada para realizar esta manutenção

O nível de dificuldade técnica é {$difficultyText}.
{$toolsText}
{$timeText}
</instruções_específicas>

MAINTENANCE;

        return $basePrompt . "\n" . $maintenanceInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function generateVehicleComparisonPrompt(
        array $vehicles,
        array $comparisonAspects = [],
        array $options = []
    ): string {
        // Verifica se há veículos suficientes para comparação
        if (count($vehicles) < 2) {
            throw new \InvalidArgumentException("A comparação requer pelo menos 2 veículos");
        }
        
        // Formata os veículos para o prompt
        $vehicleDescriptions = [];
        foreach ($vehicles as $vehicle) {
            $vehicleDescriptions[] = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
        }
        
        $vehiclesList = implode(" vs ", $vehicleDescriptions);
        
        // Aspectos padrão de comparação se não forem fornecidos
        if (empty($comparisonAspects)) {
            $comparisonAspects = [
                "design e estética",
                "desempenho",
                "consumo de combustível",
                "conforto interno",
                "tecnologia embarcada",
                "segurança",
                "custo-benefício",
                "manutenção"
            ];
        }
        
        // Formata os aspectos para o prompt
        $aspectsList = implode(", ", $comparisonAspects);
        
        // Constrói o contexto e keywords
        $context = "comparação detalhada entre {$vehiclesList}";
        $keywords = array_merge(
            array_map(fn($v) => $v->make . " " . $v->model, $vehicles),
            ["comparação", "diferenças", "vantagens"]
        );
        
        // Opções específicas para comparação
        $comparisonOptions = [
            'wordCount' => $options['wordCount'] ?? 1500,
            'tone' => 'analítico e imparcial',
            'targetAudience' => "Consumidores em processo de decisão de compra entre estes modelos"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'comparison', array_merge($options, $comparisonOptions));
        
        // Adiciona instruções específicas de comparação
        $comparisonInstructions = <<<COMPARISON

<instruções_específicas>
Compare os seguintes veículos: {$vehiclesList}.

Faça uma análise comparativa nos seguintes aspectos:
- {$aspectsList}

Para cada aspecto:
1. Apresente as características de cada veículo
2. Compare diretamente os pontos fortes e fracos
3. Indique qual veículo se sobressai neste aspecto específico

No final, inclua uma tabela comparativa resumindo os pontos principais e uma conclusão indicando o perfil de consumidor mais adequado para cada veículo.

A comparação deve ser equilibrada e baseada em fatos, evitando favoritismos.
</instruções_específicas>

COMPARISON;

        return $basePrompt . "\n" . $comparisonInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function generateTroubleshootingPrompt(
        string $problemDescription,
        VehicleReference $vehicle,
        array $possibleCauses = [],
        array $options = []
    ): string {
        // Informações do veículo
        $vehicleDesc = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
        
        // Causas possíveis (se fornecidas)
        $causesText = "";
        if (!empty($possibleCauses)) {
            $causesText = "As possíveis causas incluem: " . implode(", ", $possibleCauses) . ".";
        }
        
        // Constrói o contexto e keywords
        $context = "como diagnosticar e resolver o problema de '{$problemDescription}' em um {$vehicleDesc}";
        $keywords = [
            "problema",
            "diagnóstico",
            $problemDescription,
            $vehicle->make,
            $vehicle->model,
            "solução"
        ];
        
        // Opções específicas para troubleshooting
        $troubleshootingOptions = [
            'wordCount' => $options['wordCount'] ?? 1200,
            'tone' => 'objetivo e solucionador',
            'targetAudience' => "Proprietários de {$vehicleDesc} enfrentando este problema específico"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'troubleshooting', array_merge($options, $troubleshootingOptions));
        
        // Adiciona instruções específicas de diagnóstico
        $troubleshootingInstructions = <<<TROUBLESHOOTING

<instruções_específicas>
O artigo deve abordar o problema de "{$problemDescription}" específico para {$vehicleDesc}.

Estruture o conteúdo da seguinte forma:
1. Descrição detalhada do problema e seus sintomas
2. Possíveis causas do problema, da mais comum à mais rara
   {$causesText}
3. Procedimento de diagnóstico passo a passo
4. Soluções para cada causa possível, incluindo:
   - Soluções que o proprietário pode implementar sozinho
   - Situações em que um profissional deve ser consultado
5. Dicas para evitar que o problema volte a ocorrer
6. Custo estimado para as diferentes soluções

O artigo deve ser prático e orientado à solução, fornecendo informações acionáveis e específicas para este modelo de veículo.
</instruções_específicas>

TROUBLESHOOTING;

        return $basePrompt . "\n" . $troubleshootingInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function generateTirePressurePrompt(
        VehicleReference $vehicle,
        array $tirePressureData = [],
        array $options = []
    ): string {
        // Informações do veículo
        $vehicleDesc = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
        
        // Dados de pressão de pneus
        $pressureText = "";
        if (!empty($tirePressureData)) {
            $pressureText = "Dados de pressão específicos para este veículo:\n";
            
            foreach ($tirePressureData as $condition => $pressureSettings) {
                $pressureText .= "- {$condition}: ";
                if (is_array($pressureSettings)) {
                    $pressureText .= "Dianteiros: {$pressureSettings['front']} psi, Traseiros: {$pressureSettings['rear']} psi\n";
                } else {
                    $pressureText .= "{$pressureSettings} psi\n";
                }
            }
        }
        
        // Constrói o contexto e keywords
        $context = "como realizar a calibragem correta dos pneus de um {$vehicleDesc}";
        $keywords = [
            "calibragem",
            "pneus",
            "pressão",
            "segurança",
            $vehicle->make,
            $vehicle->model
        ];
        
        // Opções específicas para calibragem
        $tirePressureOptions = [
            'wordCount' => $options['wordCount'] ?? 1000,
            'tone' => 'didático e detalhado',
            'targetAudience' => "Proprietários de {$vehicleDesc} que desejam manter seus pneus corretamente calibrados"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'how-to', array_merge($options, $tirePressureOptions));
        
        // Adiciona instruções específicas para calibragem
        $tirePressureInstructions = <<<TIRE_PRESSURE

<instruções_específicas>
O artigo deve ser um guia completo sobre calibragem de pneus para {$vehicleDesc}, incluindo:

1. Importância da calibragem correta para segurança, economia e durabilidade
2. Valores recomendados de pressão:
   {$pressureText}
3. Como verificar a pressão dos pneus corretamente
4. Passo a passo para calibrar os pneus
5. Frequência recomendada para verificação
6. Como ajustar a pressão em diferentes condições (carga pesada, viagem longa, etc.)
7. Sinais de pneus com pressão incorreta (subinflados ou superintflados)
8. Impacto da temperatura na pressão dos pneus
9. Ferramentas recomendadas para verificação de pressão

Inclua informações sobre como encontrar a tabela de pressão no próprio veículo (geralmente na coluna da porta do motorista ou no manual do proprietário).
</instruções_específicas>

TIRE_PRESSURE;

        return $basePrompt . "\n" . $tirePressureInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function generateOilChangePrompt(
        VehicleReference $vehicle,
        string $oilType,
        float $oilQuantity,
        array $options = []
    ): string {
        // Informações do veículo
        $vehicleDesc = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
        
        // Informações específicas de óleo
        $oilTypeText = !empty($oilType) ? "O tipo de óleo recomendado é {$oilType}." : "";
        $oilQuantityText = $oilQuantity > 0 ? "A quantidade necessária é de {$oilQuantity} litros." : "";
        
        // Verifica se há ferramentas específicas
        $toolsText = "";
        if (isset($options['tools']) && !empty($options['tools'])) {
            $toolsText = "Ferramentas necessárias: " . implode(", ", $options['tools']) . ".";
        }
        
        // Constrói o contexto e keywords
        $context = "como realizar a troca de óleo em um {$vehicleDesc}";
        $keywords = [
            "troca de óleo",
            "manutenção",
            "lubrificação",
            $vehicle->make,
            $vehicle->model,
            $oilType
        ];
        
        // Opções específicas para troca de óleo
        $oilChangeOptions = [
            'wordCount' => $options['wordCount'] ?? 1200,
            'tone' => 'técnico e instrutivo',
            'targetAudience' => "Proprietários de {$vehicleDesc} que desejam aprender a fazer sua própria troca de óleo"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'how-to', array_merge($options, $oilChangeOptions));
        
        // Adiciona instruções específicas para troca de óleo
        $oilChangeInstructions = <<<OIL_CHANGE

<instruções_específicas>
O artigo deve ser um guia detalhado sobre como realizar a troca de óleo em um {$vehicleDesc}.

Especificações técnicas importantes:
- {$oilTypeText}
- {$oilQuantityText}
- {$toolsText}

Estruture o conteúdo da seguinte forma:
1. Introdução sobre a importância da troca regular de óleo
2. Materiais e ferramentas necessários
3. Preparação do veículo
4. Procedimento passo a passo com detalhes específicos para este modelo
5. Verificações pós-troca
6. Descarte adequado do óleo usado (enfatizando a importância ambiental)
7. Intervalo recomendado para próximas trocas

Inclua dicas especiais para as particularidades deste modelo específico, como a localização exata do filtro de óleo, tipo de chave necessária para o bujão, etc.
</instruções_específicas>

OIL_CHANGE;

        return $basePrompt . "\n" . $oilChangeInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function generateVehicleNewsPrompt(
        VehicleReference $vehicle,
        array $newFeatures = [],
        array $marketData = [],
        array $options = []
    ): string {
        // Informações do veículo
        $vehicleDesc = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
        
        // Novas características
        $featuresText = "";
        if (!empty($newFeatures)) {
            $featuresText = "Principais novidades:\n- " . implode("\n- ", $newFeatures);
        }
        
        // Dados de mercado
        $marketDataText = "";
        if (!empty($marketData)) {
            $marketDataText = "Dados de mercado relevantes:\n";
            foreach ($marketData as $key => $value) {
                $marketDataText .= "- {$key}: {$value}\n";
            }
        }
        
        // Constrói o contexto e keywords
        $context = "as novidades e atualizações do {$vehicleDesc}";
        $keywords = [
            "lançamento",
            "novidades",
            $vehicle->make,
            $vehicle->model,
            $vehicle->year,
            "mercado automotivo"
        ];
        
        // Opções específicas para notícias
        $newsOptions = [
            'wordCount' => $options['wordCount'] ?? 1000,
            'tone' => 'informativo e atual',
            'targetAudience' => "Entusiastas e potenciais compradores interessados nas novidades do {$vehicleDesc}"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'news', array_merge($options, $newsOptions));
        
        // Adiciona instruções específicas para notícias
        $newsInstructions = <<<NEWS

<instruções_específicas>
O artigo deve apresentar uma visão completa e atualizada sobre o {$vehicleDesc}, incluindo:

1. Introdução ao modelo e seu posicionamento no mercado
2. {$featuresText}
3. {$marketDataText}
4. Comparação com a versão anterior ou com concorrentes diretos
5. Previsão de disponibilidade e preços no mercado brasileiro
6. Impressões iniciais sobre as mudanças (design, tecnologia, desempenho, etc.)
7. Recepção do mercado e dos especialistas

O artigo deve analisar o impacto destas mudanças no segmento e contextualizar para o mercado brasileiro.
</instruções_específicas>

NEWS;

        return $basePrompt . "\n" . $newsInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function generateEcoDrivingPrompt(
        ?VehicleReference $vehicle = null,
        ?BrazilianLocation $location = null,
        array $ecoTips = [],
        array $options = []
    ): string {
        // Verifica se há um veículo específico
        $vehicleText = "";
        if ($vehicle !== null) {
            $vehicleDesc = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
            $vehicleText = "específico para {$vehicleDesc}";
        }
        
        // Verifica se há uma localização específica
        $locationText = "";
        if ($location !== null) {
            $locationCity = $location->getCity();
            $locationState = $location->getStateCode()->value;
            $locationText = "adaptado para a região de {$locationCity}-{$locationState}";
        }
        
        // Dicas de economia
        $tipsText = "";
        if (!empty($ecoTips)) {
            $tipsText = "Inclua as seguintes dicas específicas:\n- " . implode("\n- ", $ecoTips);
        }
        
        // Constrói o contexto e keywords
        $context = "dicas de direção econômica e sustentável {$vehicleText} {$locationText}";
        $keywords = [
            "economia",
            "combustível",
            "direção eficiente",
            "sustentabilidade",
            "consumo"
        ];
        
        // Adiciona keywords específicos se houver veículo
        if ($vehicle !== null) {
            $keywords[] = $vehicle->make;
            $keywords[] = $vehicle->model;
        }
        
        // Opções específicas para eco-driving
        $ecoOptions = [
            'wordCount' => $options['wordCount'] ?? 1000,
            'tone' => 'informativo e prático',
            'targetAudience' => "Motoristas que buscam reduzir o consumo de combustível e o impacto ambiental"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'guide', array_merge($options, $ecoOptions));
        
        // Processa o texto de veículo e localização para o template
        // Evitando o uso de operadores ternários dentro de strings Heredoc
        $vehicleContextText = "";
        if ($vehicleText) {
            $vehicleContextText = " para " . $vehicleText;
        }
        
        $locationContextText = "";
        if ($locationText) {
            $locationContextText = ", " . $locationText;
        }
        
        $locationConsiderationText = "";
        if ($locationText && $location !== null) {
            $locationConsiderationText = "Inclua considerações específicas sobre o tráfego, topografia e clima de " . $location->getCity() . ".";
        }
        
        $vehicleTipsText = "";
        if ($vehicleText && isset($vehicleDesc)) {
            $vehicleTipsText = "Forneça dicas específicas para maximizar a eficiência do " . $vehicleDesc . ".";
        }
        
        // Adiciona instruções específicas para eco-driving utilizando as variáveis processadas
        $ecoInstructions = <<<ECO_DRIVING

<instruções_específicas>
O artigo deve ser um guia abrangente sobre técnicas de direção econômica e sustentável{$vehicleContextText}{$locationContextText}.

Estruture o conteúdo da seguinte forma:
1. Introdução sobre a importância da direção econômica (economia financeira e redução do impacto ambiental)
2. Técnicas de condução eficiente:
   - Aceleração e frenagem progressivas
   - Uso correto das marchas
   - Velocidade constante e uso do controle de cruzeiro quando disponível
   - Antecipação do tráfego
3. Manutenção preventiva para economia
4. Planejamento de rotas eficientes
5. Uso correto do ar-condicionado e outros equipamentos
6. {$tipsText}
7. Estimativa de economia potencial em termos percentuais e financeiros

{$locationConsiderationText}
{$vehicleTipsText}
</instruções_específicas>

ECO_DRIVING;

        return $basePrompt . "\n" . $ecoInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function generatePersonalExperiencePrompt(
        HumanPersona $persona,
        VehicleReference $vehicle,
        string $experienceType,
        array $experienceDetails = [],
        array $options = []
    ): string {
        // Extrai informações da persona
        $personaFullName = is_string($persona->getName()) ? $persona->getName() : (string)$persona->getName();
        $personaProfession = $persona->getProfession();
        $personaLocation = $persona->getLocation();
        
        // Informações do veículo
        $vehicleDesc = "{$vehicle->make} {$vehicle->model} {$vehicle->year}";
        
        // Detalhes da experiência
        $detailsText = "";
        if (!empty($experienceDetails)) {
            $detailsText = "Detalhes específicos da experiência:\n";
            foreach ($experienceDetails as $key => $value) {
                $detailsText .= "- {$key}: {$value}\n";
            }
        }
        
        // Constrói o contexto e keywords
        $context = "relato pessoal de {$personaFullName} sobre sua experiência de {$experienceType} com o {$vehicleDesc}";
        $keywords = [
            $experienceType,
            "experiência pessoal",
            $vehicle->make,
            $vehicle->model,
            "relato",
            "avaliação"
        ];
        
        // Opções específicas para experiência pessoal
        $experienceOptions = [
            'wordCount' => $options['wordCount'] ?? 1500,
            'tone' => 'pessoal e conversacional',
            'targetAudience' => "Leitores interessados em relatos autênticos sobre este modelo de veículo"
        ];
        
        // Gera o prompt base
        $basePrompt = $this->generateBasePrompt($context, $keywords, 'personal', array_merge($options, $experienceOptions));
        
        // Adiciona instruções específicas para experiência pessoal
        $experienceInstructions = <<<EXPERIENCE

<instruções_específicas>
O artigo deve relatar a experiência pessoal de {$personaFullName}, um(a) {$personaProfession} de {$personaLocation}, com seu {$vehicleDesc} durante uma situação de {$experienceType}.

{$detailsText}

O relato deve:
1. Iniciar com uma breve apresentação pessoal e contexto da experiência
2. Detalhar as expectativas antes da experiência
3. Narrar cronologicamente os principais momentos
4. Incluir impressões subjetivas e detalhes sensoriais (sons, sensações, etc.)
5. Destacar pontos positivos e negativos observados
6. Concluir com uma avaliação geral e recomendações para outros motoristas

O artigo deve ser escrito em primeira pessoa, com tom pessoal e autêntico, como se o próprio {$personaFullName} estivesse compartilhando sua experiência diretamente com o leitor.
</instruções_específicas>

EXPERIENCE;

        return $basePrompt . "\n" . $experienceInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function incorporateTrafficInfo(
        string $prompt,
        BrazilianLocation $location,
        array $trafficData = []
    ): string {
        // Extrai informações da localização
        $locationCity = $location->getCity();
        $locationState = $location->getStateCode()->value;
        $trafficPattern = $location->getTrafficPattern()->value;
        
        // Traduz o padrão de tráfego para descrição em português
        $trafficPatternDesc = match($trafficPattern) {
            'light' => 'leve',
            'moderate' => 'moderado',
            'heavy' => 'intenso',
            'congested' => 'congestionado',
            default => $trafficPattern
        };
        
        // Constrói texto de informações de tráfego
        $trafficInfoText = "Considerando o tráfego {$trafficPatternDesc} típico de {$locationCity}-{$locationState}";
        
        // Adiciona dados específicos de tráfego se fornecidos
        if (!empty($trafficData)) {
            $trafficInfoText .= ", onde:\n";
            foreach ($trafficData as $key => $value) {
                $trafficInfoText .= "- {$key}: {$value}\n";
            }
        } else {
            $trafficInfoText .= ".";
        }
        
        // Constrói a instrução de tráfego
        $trafficInstructions = <<<TRAFFIC

<info_tráfego>
{$trafficInfoText}

Adapte as recomendações do artigo considerando estas condições de tráfego, incluindo dicas específicas para lidar com as situações de trânsito comuns nesta região.
</info_tráfego>

TRAFFIC;

        // Adiciona as instruções ao prompt original
        return $prompt . "\n" . $trafficInstructions;
    }

    /**
     * {@inheritDoc}
     */
    public function incorporateForumInsights(
        string $prompt,
        array $discussions,
        int $maxInsights = 3
    ): string {
        // Extrai insights das discussões
        $insights = $this->extractInsightsFromDiscussions($discussions, $maxInsights);
        
        // Se não houver insights, retorna o prompt original
        if (empty($insights)) {
            return $prompt;
        }
        
        // Constrói a instrução de insights
        $insightsInstructions = <<<INSIGHTS

<insights_fórum>
Incorpore os seguintes insights extraídos de discussões reais em fóruns automotivos:

{$insights}

Estes insights devem ser integrados naturalmente ao longo do artigo, ajudando a enriquecer o conteúdo com experiências reais.
</insights_fórum>

INSIGHTS;

        // Adiciona as instruções ao prompt original
        return $prompt . "\n" . $insightsInstructions;
    }

    /**
     * Determina o estilo de escrita baseado no perfil da persona
     *
     * @param HumanPersona $persona A persona para análise
     * @return string Instruções de estilo de escrita
     */
    private function determineWritingStyle(HumanPersona $persona): string
    {
        // Analisa a profissão para definir o estilo base
        $profession = strtolower($persona->getProfession());
        
        // Profissões técnicas
        if (str_contains($profession, 'engenh') || 
            str_contains($profession, 'mecânic') || 
            str_contains($profession, 'técnic') ||
            str_contains($profession, 'programad') ||
            str_contains($profession, 'desenvolvedor')) {
            
            return "Use um tom técnico e preciso, com boa quantidade de detalhes técnicos. " .
                   "Prefira termos exatos e objetivos, evitando ambiguidades. " .
                   "Inclua números e especificações quando relevante.";
        }
        
        // Profissões educacionais
        elseif (str_contains($profession, 'professor') || 
                str_contains($profession, 'educador') || 
                str_contains($profession, 'instrutor')) {
            
            return "Use um tom didático e explicativo, desenvolvendo conceitos de forma clara e progressiva. " .
                   "Estabeleça conexões entre ideias e use analogias quando útil para facilitar a compreensão. " .
                   "Evite jargões sem explicação.";
        }
        
        // Profissões criativas
        elseif (str_contains($profession, 'design') || 
                str_contains($profession, 'artista') || 
                str_contains($profession, 'escritor') ||
                str_contains($profession, 'fotógraf')) {
            
            return "Use um estilo mais expressivo e criativo, com descrições vívidas e linguagem envolvente. " .
                   "Dê atenção a elementos estéticos e sensoriais. " .
                   "Permita-se algumas metáforas e comparações interessantes.";
        }
        
        // Profissões administrativas/empresariais
        elseif (str_contains($profession, 'administrad') || 
                str_contains($profession, 'empresári') || 
                str_contains($profession, 'executiv') ||
                str_contains($profession, 'gerente')) {
            
            return "Use um tom pragmático e orientado a resultados, com ênfase em eficiência e custo-benefício. " .
                   "Estruture o texto de forma direta e organizada. " .
                   "Inclua considerações sobre investimento, economia e durabilidade.";
        }
        
        // Profissões de saúde
        elseif (str_contains($profession, 'médic') || 
                str_contains($profession, 'enferm') || 
                str_contains($profession, 'fisioterapeuta') ||
                str_contains($profession, 'nutricion')) {
            
            return "Use um tom equilibrado entre técnico e acessível, com ênfase em segurança e bem-estar. " .
                   "Inclua considerações sobre ergonomia, conforto e impactos à saúde. " .
                   "Explique termos técnicos quando necessários.";
        }
        
        // Estilo padrão para outras profissões
        else {
            return "Use um tom conversacional e acessível, equilibrando informações técnicas com linguagem do dia a dia. " .
                   "Mantenha o texto claro e direto, evitando excesso de jargões. " .
                   "Inclua exemplos práticos quando possível.";
        }
    }

    /**
     * Extrai insights relevantes das discussões fornecidas
     *
     * @param array<ForumDiscussion> $discussions Lista de discussões para extração
     * @param int $maxInsights Número máximo de insights a extrair
     * @return string Texto formatado com insights
     */
    private function extractInsightsFromDiscussions(array $discussions, int $maxInsights = 5): string
    {
        if (empty($discussions)) {
            return "";
        }
        
        // Limita o número de discussões
        $discussions = array_slice($discussions, 0, $maxInsights);
        
        $insights = "";
        foreach ($discussions as $index => $discussion) {
            // Limita o tamanho do conteúdo para evitar tokens excessivos
            $content = Str::limit($discussion->getContent(), 150);
            
            // Formata o insight
            $insights .= "- Insight #" . ($index + 1) . " (sobre " . $discussion->getTitle() . "): \"" . $content . "\"\n";
        }
        
        return $insights;
    }

    /**
     * Retorna instruções de estrutura de artigo com base no tipo
     *
     * @param string $articleType Tipo de artigo a ser gerado
     * @return string Instruções de estrutura
     */
    private function getArticleStructureInstructions(string $articleType): string
    {
        return match($articleType) {
            'how-to' => "Estruture o artigo em formato de tutorial passo a passo, com seções claramente demarcadas, incluindo lista de materiais necessários, procedimento detalhado, dicas adicionais e resolução de problemas comuns.",
            
            'comparison' => "Estruture o artigo em formato comparativo, com introdução, critérios de comparação bem definidos, análise comparativa de cada aspecto relevante, tabela comparativa e conclusão com recomendações para diferentes perfis de usuário.",
            
            'troubleshooting' => "Estruture o artigo com foco em diagnóstico e solução, começando pelos sintomas, apresentando possíveis causas, métodos de verificação, soluções específicas para cada causa e prevenção de recorrência.",
            
            'news' => "Estruture o artigo em formato jornalístico, com um lead informativo, desenvolvimento das novidades principais, contexto de mercado, análise de impacto e conclusão com perspectivas futuras.",
            
            'guide' => "Estruture o artigo como um guia completo, com introdução ao tema, princípios fundamentais, recomendações práticas, exemplos relevantes, recursos adicionais e conclusão com os pontos-chave.",
            
            'personal' => "Estruture o artigo como um relato pessoal, com introdução contextual, narrativa cronológica da experiência, impressões pessoais, pontos de destaque, desafios encontrados e conclusão com reflexões finais.",
            
            'informative' => "Estruture o artigo de forma informativa e educacional, com introdução ao tema, explicação dos conceitos principais, aplicações práticas, exemplos ilustrativos e conclusão resumindo os pontos essenciais.",
            
            // Formato padrão para outros tipos
            default => "Estruture o artigo com introdução clara ao tema, desenvolvimento lógico das ideias principais, uso de subtítulos para organizar o conteúdo, exemplos concretos quando apropriado e conclusão que sintetize os pontos principais."
        };
    }
}