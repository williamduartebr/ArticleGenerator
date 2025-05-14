<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;
use Faker\Provider\pt_BR\Internet as BrazilianInternet;
use Src\ArticleGenerator\Domain\Entity\ContentSourceType;
use Carbon\Carbon;

/**
 * Seeder para popular a tabela content_sources com fontes de conteúdo
 * diversificadas para o sistema de geração de artigos automatizados.
 */
class ContentSourceSeeder extends Seeder
{
    /**
     * Lista de sites de fóruns automotivos brasileiros reais
     * 
     * @var array<string>
     */
    private array $forumSites = [
        'https://www.clubedofiat.com.br',
        'https://www.carrosnaweb.com.br/forum',
        'https://www.webmotors.com.br/forum',
        'https://www.forumdopalio.com.br',
        'https://www.forumdefiateiros.com.br',
        'https://www.fiatclube.com.br',
        'https://www.forumfocus.com.br',
        'https://www.clubedoford.com.br',
        'https://www.corsa-clube.com.br',
        'https://www.chevroletforum.com.br',
        'https://www.clubedovectra.com.br',
        'https://www.golclube.com.br',
        'https://www.forumdogol.com.br',
        'https://www.clubedovw.com.br',
        'https://www.br-honda.com',
        'https://www.forumcivic.com.br',
        'https://www.toyoteiros.com.br',
        'https://www.hiluxclube.com.br',
        'https://www.forumrenault.com.br',
        'https://www.forumpeugeot.com.br',
    ];

    /**
     * Lista de blogs automotivos brasileiros reais
     * 
     * @var array<string>
     */
    private array $blogSites = [
        'https://www.autoesporte.globo.com',
        'https://www.quatrorodas.abril.com.br',
        'https://www.noticiasautomotivas.com.br',
        'https://www.autopapo.uol.com.br',
        'https://www.carpress.com.br',
        'https://www.blogdocarro.com.br',
        'https://www.bestcars.com.br',
        'https://www.flatout.com.br',
        'https://www.autoentusiastas.com.br',
        'https://www.carrobonito.com',
        'https://www.caranddrive.com.br',
        'https://www.autoblog.com.br',
        'https://www.motormundo.com.br',
        'https://www.automovel.com.br',
        'https://www.autofoco.blog.br',
    ];

    /**
     * Lista de sites de notícias e portais automotivos brasileiros reais
     * 
     * @var array<string>
     */
    private array $newsSites = [
        'https://www.uol.com.br/carros',
        'https://www.g1.globo.com/carros',
        'https://www.estadao.com.br/mobilidade/carros',
        'https://www.r7.com/veiculos',
        'https://www.terra.com.br/veiculos',
        'https://www.ig.com.br/carros',
        'https://www.olx.com.br/autos-e-pecas',
        'https://www.mercadolivre.com.br/carros',
        'https://www.webmotors.com.br/webnews',
        'https://www.icarros.com.br/noticias',
        'https://www.car.blog.br',
        'https://www.automotivenews.com.br',
        'https://www.folha.uol.com.br/autos',
    ];

    /**
     * Lista de sites de redes sociais e comunidades
     * 
     * @var array<string>
     */
    private array $socialMediaSites = [
        'https://www.facebook.com/groups/carrosbrasil',
        'https://www.facebook.com/groups/fiateros',
        'https://www.facebook.com/groups/clubedobmw',
        'https://www.facebook.com/groups/chevroletbrasil',
        'https://www.facebook.com/groups/volkswagenclube',
        'https://www.reddit.com/r/carrosbrasil',
        'https://www.instagram.com/carros_brasil',
        'https://www.instagram.com/webmotorsbr',
        'https://www.instagram.com/quatrorodasbr',
        'https://www.youtube.com/c/autoesporte',
        'https://www.youtube.com/c/webmotorsbr',
        'https://www.youtube.com/c/quatrorodas',
        'https://www.youtube.com/c/carrodesafio',
        'https://www.tiktok.com/@carrosdobrasil',
        'https://www.tiktok.com/@dicascarros',
    ];

    /**
     * Lista de sites de revisão de veículos
     * 
     * @var array<string>
     */
    private array $reviewSites = [
        'https://www.car.blog.br/reviews',
        'https://www.autopapo.uol.com.br/teste',
        'https://www.quatrorodas.abril.com.br/testes',
        'https://www.revista.testesdecarros.com.br',
        'https://www.webmotors.com.br/revista/avaliacao',
        'https://www.carrocarro.com.br/avaliacao',
        'https://www.testdrive.com.br',
        'https://www.comparador.com.br/carros',
        'https://www.testedrive.com.br',
        'https://www.autoforum.com.br/avaliacao',
    ];

    /**
     * Lista de sites oficiais de montadoras
     * 
     * @var array<string>
     */
    private array $officialSites = [
        'https://www.chevrolet.com.br',
        'https://www.ford.com.br',
        'https://www.volkswagen.com.br',
        'https://www.fiat.com.br',
        'https://www.toyota.com.br',
        'https://www.honda.com.br',
        'https://www.nissan.com.br',
        'https://www.hyundai.com.br',
        'https://www.renault.com.br',
        'https://www.peugeot.com.br',
        'https://www.citroen.com.br',
        'https://www.mitsubishi.com.br',
        'https://www.bmw.com.br',
        'https://www.mercedes-benz.com.br',
        'https://www.audi.com.br',
    ];

    /**
     * Lista de tópicos relacionados ao segmento automotivo
     * 
     * @var array<string>
     */
    private array $topics = [
        'manutenção', 'revisão', 'mecânica', 'elétrica', 'suspensão',
        'pneus', 'freios', 'motor', 'câmbio', 'transmissão',
        'combustível', 'consumo', 'desempenho', 'aceleração', 'velocidade',
        'segurança', 'conforto', 'interior', 'exterior', 'acessórios',
        'modificações', 'personalização', 'tuning', 'som automotivo', 'multimídia',
        'GPS', 'navegação', 'seguro', 'IPVA', 'licenciamento',
        'compra', 'venda', 'financiamento', 'leasing', 'consórcio',
        'lançamentos', 'novidades', 'comparativos', 'recalls', 'problemas comuns',
        'carros usados', 'seminovos', 'zero km', 'test-drive', 'opinião do dono',
        'SUVs', 'sedãs', 'hatches', 'picapes', 'esportivos',
        'elétricos', 'híbridos', 'flex', 'diesel', 'etanol',
        'viagem', 'estrada', 'cidade', 'off-road', 'aventura',
    ];

    /**
     * Execute o seeder e popule o banco de dados com dados de fontes de conteúdo.
     */
    public function run(): void
    {
        // Configura o Faker para utilizar o locale pt_BR
        $faker = FakerFactory::create('pt_BR');
        $faker->addProvider(new BrazilianInternet($faker));

        // Limpa dados existentes
        DB::table('content_sources')->truncate();

        $contentSources = [];
        $types = ContentSourceType::cases();

        // Adiciona fontes de FÓRUNS (8-10 fontes)
        $forumCount = $faker->numberBetween(8, 10);
        for ($i = 0; $i < $forumCount; $i++) {
            $url = $this->forumSites[$i % count($this->forumSites)];
            $forumName = explode('www.', explode('.com', $url)[0])[1];
            $forumName = str_replace(['https://', 'http://', '.br', 'forum'], '', $forumName);
            $name = ucfirst($forumName) . " - Fórum";
            
            // Seleciona tópicos relacionados a discussões de fórum
            $topicCount = $faker->numberBetween(3, 6);
            $topicIndexes = array_rand($this->topics, $topicCount);
            $selectedTopics = [];
            foreach ((array)$topicIndexes as $index) {
                $selectedTopics[] = $this->topics[$index];
            }

            $contentSources[] = $this->createContentSource(
                $faker,
                $name,
                $url,
                ContentSourceType::FORUM,
                $faker->numberBetween(60, 95), // Confiabilidade média-alta para fóruns
                $selectedTopics,
                $faker->boolean(70) // 70% de chance de estar ativo
            );
        }

        // Adiciona fontes de BLOGS (10-12 fontes)
        $blogCount = $faker->numberBetween(10, 12);
        for ($i = 0; $i < $blogCount; $i++) {
            $url = $this->blogSites[$i % count($this->blogSites)];
            $blogName = explode('www.', explode('.com', $url)[0])[1];
            $blogName = str_replace(['https://', 'http://', '.br', '.abril', '.uol', '.globo'], '', $blogName);
            $name = ucfirst($blogName) . " - Blog";
            
            // Seleciona tópicos relacionados a blogs automotivos
            $topicCount = $faker->numberBetween(4, 8);
            $topicIndexes = array_rand($this->topics, $topicCount);
            $selectedTopics = [];
            foreach ((array)$topicIndexes as $index) {
                $selectedTopics[] = $this->topics[$index];
            }

            $contentSources[] = $this->createContentSource(
                $faker,
                $name,
                $url,
                ContentSourceType::BLOG,
                $faker->numberBetween(65, 85), // Confiabilidade média para blogs
                $selectedTopics,
                $faker->boolean(80) // 80% de chance de estar ativo
            );
        }

        // Adiciona fontes de NOTÍCIAS (10-12 fontes)
        $newsCount = $faker->numberBetween(10, 12);
        for ($i = 0; $i < $newsCount; $i++) {
            $url = $this->newsSites[$i % count($this->newsSites)];
            $newsName = explode('www.', explode('.com', $url)[0])[1];
            $newsName = str_replace(['https://', 'http://', '.br', '.com', '/carros', '/veiculos', '/autos', '.uol', '.globo'], '', $newsName);
            $name = ucfirst($newsName) . " - Notícias Auto";
            
            // Seleciona tópicos relacionados a notícias automotivas
            $topicCount = $faker->numberBetween(5, 10);
            $topicIndexes = array_rand($this->topics, $topicCount);
            $selectedTopics = [];
            foreach ((array)$topicIndexes as $index) {
                $selectedTopics[] = $this->topics[$index];
            }

            $contentSources[] = $this->createContentSource(
                $faker,
                $name,
                $url,
                ContentSourceType::NEWS,
                $faker->numberBetween(75, 95), // Confiabilidade alta para portais de notícias
                $selectedTopics,
                $faker->boolean(90) // 90% de chance de estar ativo
            );
        }

        // Adiciona fontes de REDES SOCIAIS (6-8 fontes)
        $socialCount = $faker->numberBetween(6, 8);
        for ($i = 0; $i < $socialCount; $i++) {
            $url = $this->socialMediaSites[$i % count($this->socialMediaSites)];
            
            // Extrai o nome da rede social a partir da URL
            $platform = "";
            if (str_contains($url, 'facebook.com')) {
                $platform = "Facebook";
                $groupName = explode('groups/', $url)[1];
            } elseif (str_contains($url, 'instagram.com')) {
                $platform = "Instagram";
                $groupName = explode('instagram.com/', $url)[1];
            } elseif (str_contains($url, 'reddit.com')) {
                $platform = "Reddit";
                $groupName = explode('r/', $url)[1];
            } elseif (str_contains($url, 'youtube.com')) {
                $platform = "YouTube";
                $groupName = explode('c/', $url)[1];
            } elseif (str_contains($url, 'tiktok.com')) {
                $platform = "TikTok";
                $groupName = explode('@', $url)[1];
            } else {
                $platform = "Comunidade";
                $groupName = "automotiva" . $i;
            }
            
            $name = ucfirst($groupName) . " - " . $platform;
            
            // Seleciona tópicos relacionados a redes sociais
            $topicCount = $faker->numberBetween(3, 6);
            $topicIndexes = array_rand($this->topics, $topicCount);
            $selectedTopics = [];
            foreach ((array)$topicIndexes as $index) {
                $selectedTopics[] = $this->topics[$index];
            }

            $contentSources[] = $this->createContentSource(
                $faker,
                $name,
                $url,
                ContentSourceType::SOCIAL_MEDIA,
                $faker->numberBetween(50, 75), // Confiabilidade média-baixa para redes sociais
                $selectedTopics,
                $faker->boolean(75) // 75% de chance de estar ativo
            );
        }

        // Adiciona fontes de REVISÃO (5-8 fontes)
        $reviewCount = $faker->numberBetween(5, 8);
        for ($i = 0; $i < $reviewCount; $i++) {
            $url = $this->reviewSites[$i % count($this->reviewSites)];
            $reviewName = explode('www.', explode('.com', $url)[0])[1];
            $reviewName = str_replace(['https://', 'http://', '.br', '.com', '/reviews', '/avaliacao', '/teste', '/testes'], '', $reviewName);
            $name = ucfirst($reviewName) . " - Avaliações";
            
            // Seleciona tópicos relacionados a revisões
            $topicCount = $faker->numberBetween(4, 7);
            $topicIndexes = array_rand($this->topics, $topicCount);
            $selectedTopics = [];
            foreach ((array)$topicIndexes as $index) {
                $selectedTopics[] = $this->topics[$index];
            }

            $contentSources[] = $this->createContentSource(
                $faker,
                $name,
                $url,
                ContentSourceType::REVIEW,
                $faker->numberBetween(70, 90), // Confiabilidade alta para sites de revisão
                $selectedTopics,
                $faker->boolean(85) // 85% de chance de estar ativo
            );
        }

        // Adiciona fontes OFICIAIS (8-10 fontes)
        $officialCount = $faker->numberBetween(8, 10);
        for ($i = 0; $i < $officialCount; $i++) {
            $url = $this->officialSites[$i % count($this->officialSites)];
            $montadoraName = explode('www.', explode('.com', $url)[0])[1];
            $montadoraName = str_replace(['https://', 'http://', '.br'], '', $montadoraName);
            $name = ucfirst($montadoraName) . " - Site Oficial";
            
            // Seleciona tópicos relacionados a sites oficiais
            $topicCount = $faker->numberBetween(5, 8);
            $topicIndexes = array_rand($this->topics, $topicCount);
            $selectedTopics = [];
            foreach ((array)$topicIndexes as $index) {
                $selectedTopics[] = $this->topics[$index];
            }

            $contentSources[] = $this->createContentSource(
                $faker,
                $name,
                $url,
                ContentSourceType::OFFICIAL,
                $faker->numberBetween(85, 100), // Confiabilidade muito alta para sites oficiais
                $selectedTopics,
                $faker->boolean(95) // 95% de chance de estar ativo
            );
        }

        // Adiciona algumas fontes DIVERSAS (2-4 fontes)
        $otherCount = $faker->numberBetween(2, 4);
        for ($i = 0; $i < $otherCount; $i++) {
            $domainName = $faker->domainName;
            $name = "Auto " . ucfirst($faker->word) . " Brasil";
            
            // Seleciona tópicos aleatórios
            $topicCount = $faker->numberBetween(2, 5);
            $topicIndexes = array_rand($this->topics, $topicCount);
            $selectedTopics = [];
            foreach ((array)$topicIndexes as $index) {
                $selectedTopics[] = $this->topics[$index];
            }

            $contentSources[] = $this->createContentSource(
                $faker,
                $name,
                "https://www." . $domainName,
                ContentSourceType::OTHER,
                $faker->numberBetween(40, 70), // Confiabilidade variável para outras fontes
                $selectedTopics,
                $faker->boolean(60) // 60% de chance de estar ativo
            );
        }

        // Insere todas as fontes na tabela
        DB::table('content_sources')->insert($contentSources);
        
        $this->command->info('Tabela content_sources populada com ' . count($contentSources) . ' fontes de conteúdo!');
    }

    /**
     * Cria um registro para a tabela content_sources
     * 
     * @param \Faker\Generator $faker Instância do Faker
     * @param string $name Nome da fonte
     * @param string $url URL da fonte
     * @param ContentSourceType $type Tipo da fonte
     * @param float $trustScore Pontuação de confiabilidade
     * @param array<string> $topics Tópicos abordados pela fonte
     * @param bool $isActive Indica se a fonte está ativa
     * @return array<string, mixed> Registro formatado para inserção no banco
     */
    private function createContentSource(
        \Faker\Generator $faker,
        string $name,
        string $url,
        ContentSourceType $type,
        float $trustScore,
        array $topics,
        bool $isActive
    ): array {
        // Gera timestamps aleatórios para último uso e crawling
        $lastCrawledAt = $faker->boolean(80) 
            ? Carbon::now()->subDays($faker->numberBetween(1, 30))->toDateTimeString() 
            : null;
            
        $lastUsedAt = $faker->boolean(70) 
            ? Carbon::now()->subDays($faker->numberBetween(1, 60))->toDateTimeString() 
            : null;
            
        $usageCount = $lastUsedAt ? $faker->numberBetween(0, 50) : 0;

        // Verificação (30% de chance de estar verificada)
        $verifiedAt = $faker->boolean(30) 
            ? Carbon::now()->subDays($faker->numberBetween(10, 120))->toDateTimeString() 
            : null;
            
        $verifiedBy = $verifiedAt 
            ? $faker->firstName . ' ' . $faker->lastName 
            : null;

        // Configurações para crawler
        $crawlerConfig = [
            'max_depth' => $faker->numberBetween(1, 3),
            'max_pages' => $faker->numberBetween(10, 100),
            'delay_between_requests' => $faker->numberBetween(1, 5),
            'respect_robots_txt' => $faker->boolean(90),
            'user_agent' => 'Mozilla/5.0 AutoContentCrawler/1.0',
        ];

        // Regras de extração de conteúdo
        $contentExtractionRules = [
            'title_selector' => '.post-title, .entry-title, .title, h1',
            'content_selector' => '.post-content, .entry-content, .content, article',
            'author_selector' => '.author-name, .entry-author, .byline',
            'date_selector' => '.post-date, .entry-date, .published, time',
            'ignore_selectors' => '.comments, .sidebar, .related, .ads, nav, header, footer',
        ];

        // Frequência de crawling baseada no tipo da fonte
        $crawlFrequencyHours = match($type) {
            ContentSourceType::NEWS => $faker->numberBetween(6, 24),
            ContentSourceType::BLOG => $faker->numberBetween(24, 72),
            ContentSourceType::FORUM => $faker->numberBetween(12, 48),
            ContentSourceType::SOCIAL_MEDIA => $faker->numberBetween(2, 12),
            ContentSourceType::REVIEW => $faker->numberBetween(72, 168),
            ContentSourceType::OFFICIAL => $faker->numberBetween(168, 336),
            default => $faker->numberBetween(24, 168)
        };

        // Monta o registro final
        return [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'url' => $url,
            'type' => $type->value,
            'trust_score' => $trustScore,
            'topics' => json_encode($topics),
            'is_active' => $isActive,
            'last_crawled_at' => $lastCrawledAt,
            'usage_count' => $usageCount,
            'last_used_at' => $lastUsedAt,
            'verified_at' => $verifiedAt,
            'verified_by' => $verifiedBy,
            'crawler_config' => json_encode($crawlerConfig),
            'content_extraction_rules' => json_encode($contentExtractionRules),
            'crawl_frequency_hours' => $crawlFrequencyHours,
            'created_at' => Carbon::now()->subDays($faker->numberBetween(30, 365))->toDateTimeString(),
            'updated_at' => Carbon::now()->subDays($faker->numberBetween(0, 30))->toDateTimeString(),
        ];
    }
}