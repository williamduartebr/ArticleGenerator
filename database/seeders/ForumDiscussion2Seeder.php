<?php

declare(strict_types=1);

namespace Database\Seeders;

use DateTimeImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

/**
 * Seeder para popular a tabela forum_discussions com dados realistas
 * de discussões de fóruns automotivos brasileiros.
 */
class ForumDiscussion2Seeder extends Seeder
{
    /**
     * Execute o seeder para criar 500 discussões de fóruns automotivos.
     * 
     * @return void
     */
    public function run(): void
    {
        // Inicializa o Faker em português brasileiro
        $faker = FakerFactory::create('pt_BR');
        
        // Marcas e modelos populares de veículos no Brasil
        $carMakes = [
            'Volkswagen' => ['Gol', 'Fox', 'Polo', 'T-Cross', 'Virtus', 'Jetta', 'Amarok', 'Saveiro', 'Nivus', 'Taos'],
            'Fiat' => ['Uno', 'Palio', 'Argo', 'Mobi', 'Cronos', 'Strada', 'Toro', 'Pulse', 'Fastback', 'Doblo'],
            'Chevrolet' => ['Onix', 'Onix Plus', 'Prisma', 'Tracker', 'Spin', 'Cruze', 'S10', 'Montana', 'Equinox', 'Joy'],
            'Ford' => ['Ka', 'Ecosport', 'Ranger', 'Bronco Sport', 'Territory', 'Maverick', 'Edge', 'Mustang'],
            'Hyundai' => ['HB20', 'HB20S', 'Creta', 'i30', 'Tucson', 'Santa Fe', 'Azera', 'Kona', 'Elantra'],
            'Toyota' => ['Corolla', 'Corolla Cross', 'Hilux', 'SW4', 'Yaris', 'RAV4', 'Etios', 'Camry', 'Prius'],
            'Renault' => ['Kwid', 'Sandero', 'Logan', 'Duster', 'Oroch', 'Captur', 'Stepway', 'Kardian'],
            'Honda' => ['Civic', 'City', 'Fit', 'HR-V', 'WR-V', 'CR-V', 'Accord', 'ZR-V'],
            'Nissan' => ['Versa', 'Kicks', 'Sentra', 'Frontier', 'March', 'Leaf'],
            'Jeep' => ['Renegade', 'Compass', 'Commander', 'Wrangler', 'Gladiator', 'Cherokee'],
        ];
        
        // Fóruns automotivos brasileiros populares
        $forumSources = [
            'https://www.clubedofiat.com.br/forum' => 'Clube do Fiat',
            'https://www.torcidafiat.net/forum' => 'Torcida Fiat',
            'https://www.forumdocorsa.com.br/discussoes' => 'Fórum do Corsa',
            'https://www.fordclub.com.br/forum' => 'Ford Club Brasil',
            'https://www.golfclube.com.br/forum' => 'Golf Clube',
            'https://forum.carrosnaweb.com.br' => 'Carros na Web',
            'https://www.hondaclube.com.br/forum' => 'Honda Clube',
            'https://www.civic-forum.com.br/discussoes' => 'Fórum do Civic',
            'https://nissanbr.com.br/forum' => 'Nissan Brasil Clube',
            'https://www.fenixclube.com.br/forum' => 'Fênix Clube',
            'https://www.clubedosfiesta.com.br/forum' => 'Clube dos Fiesta',
            'https://forum.webmotors.com.br' => 'Webmotors Fórum',
            'https://www.fiatpaliobr.com.br/forum' => 'Palio Brasil',
            'https://salaomotors.com.br/forum' => 'Salão Motors',
            'https://www.meucarronovo.com.br/forum' => 'Meu Carro Novo',
            'https://www.forumdocelta.com.br/discussoes' => 'Fórum do Celta',
            'https://www.forumcorolla.com.br/topicos' => 'Fórum Corolla',
            'https://www.fordkaclub.com.br/forum' => 'Ford Ka Clube',
            'https://www.clubedomitsubishi.com.br/forum' => 'Clube do Mitsubishi',
            'https://www.jeeptrack.com.br/forum' => 'Jeep Track Brasil',
        ];
        
        // Categorias para as discussões
        $categories = [
            'maintenance', 
            'performance', 
            'modification', 
            'troubleshooting', 
            'purchase', 
            'comparison', 
            'news', 
            'other'
        ];
        
        // Tipos comuns de problemas e tópicos para discussões
        $commonIssues = [
            'Consumo alto de combustível',
            'Barulho na suspensão',
            'Problema no câmbio',
            'Falha na partida',
            'Luz de injeção acesa',
            'Bateria descarregando',
            'Ar condicionado não gela',
            'Vazamento de óleo',
            'Superaquecimento do motor',
            'Barulho no motor',
            'Problemas elétricos',
            'Freios rangendo',
            'Direção pesada',
            'Marcha lenta instável',
            'Pneus desgastando irregularmente',
            'Consumo de óleo',
            'Luz do airbag acesa',
            'Trepidação ao frear',
            'Vibração em alta velocidade',
            'Corrosão na carroceria',
            'Ruído na direção',
            'Embreagem patinando',
            'Fumaça no escapamento',
            'Vidro elétrico com problema',
            'Sistema multimídia travando',
        ];
        
        // Perguntas típicas para discussões
        $commonQuestions = [
            'Vale a pena trocar?',
            'É normal isso acontecer?',
            'Alguém já passou por isso?',
            'Como resolver esse problema?',
            'Qual a melhor oficina em {cidade}?',
            'Quanto custa para consertar?',
            'Devo levar na concessionária ou oficina particular?',
            'Posso fazer esse reparo em casa?',
            'Preciso trocar a peça inteira ou tem conserto?',
            'É defeito de fábrica?',
            'Qual a durabilidade média?',
            'Posso continuar rodando assim?',
            'Qual o melhor fabricante dessa peça?',
            'O que deveria custar esse serviço?',
            'Quais as opções de personalização?',
            'Como faço a manutenção preventiva?',
            'Qual versão é mais econômica?',
            'Compensa o custo-benefício?',
            'É melhor que o concorrente?',
            'Qual a opinião de vocês sobre esse modelo?',
        ];
        
        // Tags típicas para discussões automotivas
        $commonTags = [
            'manutenção', 'reparo', 'diagnóstico', 'óleo', 'filtro', 'suspensão', 
            'freios', 'motor', 'transmissão', 'elétrica', 'ar-condicionado', 'direção',
            'problema', 'dúvida', 'comparativo', 'cuidados', 'personalização', 'tuning',
            'recall', 'economia', 'desempenho', 'ruído', 'consumo', 'peças',
            'garantia', 'seguro', 'acessórios', 'pneus', 'rodas', 'bateria',
            'faróis', 'lanterna', 'combustível', 'gasolina', 'etanol', 'flex',
            'conforto', 'segurança', 'tecnologia', 'multimídia', 'interior', 'estofamento',
            'pintura', 'funilaria', 'quilometragem', 'revisão', 'concessionária', 'oficina'
        ];
        
        // "Gems" - Insights particularmente interessantes para uso em artigos especiais
        $gems = [
            [
                'title' => 'Descobri forma de economizar 25% de combustível no meu HB20 - Compartilhando a experiência',
                'content' => 'Pessoal, depois de muito pesquisar e testar, consegui uma redução SIGNIFICATIVA no consumo do meu HB20 1.0 2019. Estou fazendo média de 18km/l na estrada e 14km/l na cidade, bem acima da média normal. Vou compartilhar o passo a passo:

1. Troca do filtro de ar a cada 10.000km (não espere os 20.000km recomendados)
2. Calibragem dos pneus com 2 libras acima do recomendado pelo manual (testei por 6 meses, sem problemas de desgaste)
3. Troca para velas de irídio (custou R$320, mas valeu DEMAIS)
4. Limpeza do corpo de borboleta e TBI
5. Uso INTERCALADO de aditivo de combustível (um tanque com, um tanque sem)
6. Evito ultrapassar 2.500rpm nas trocas de marcha

A diferença foi tão grande que até criei uma planilha monitorando antes e depois. O investimento total foi de aproximadamente R$650 mas já economizei mais de R$1.200 em um ano. 

Ah, e detalhe importante: nenhuma dessas alterações prejudica o motor ou afeta a garantia. Inclusive, o mecânico da concessionária que me indicou algumas dessas dicas.

Testem e me digam o resultado!',
                'relevance_score' => 95,
                'forum_url' => 'https://www.hbclube.com.br/forum/topico/economia-combustivel-teste-comprovado',
                'category' => 'performance',
                'tags' => ['economia', 'combustível', 'HB20', 'dicas', 'consumo', 'desempenho'],
                'views' => 15320,
                'replies' => 243
            ],
            [
                'title' => 'ALERTA: Defeito recorrente nas caixas de câmbio CVT do Nissan Kicks - Minha análise após 3 trocas',
                'content' => 'Comprei um Nissan Kicks SL 2018 zero km e infelizmente já estou na 3ª caixa de câmbio em menos de 80.000km. Sou engenheiro mecânico e resolvi compartilhar uma análise detalhada do problema.

O CVT da Nissan (principalmente dos modelos 2016-2019) tem um defeito na corrente de transmissão e no sistema de arrefecimento da caixa. Quando o óleo esquenta além do ideal (geralmente em subidas longas ou trânsito intenso), a corrente começa a desgastar prematuramente os cones internos.

Sintomas para ficarem atentos:
- Solavancos leves entre 40-60km/h
- Barulho semelhante a "chocalho" em baixa velocidade
- Aumento de rotação sem ganho de velocidade
- Trepidações em acelerações bruscas

A Nissan estendeu a garantia desse componente para 160.000km nos EUA após ações judiciais, mas no Brasil continua com garantia padrão. Se você tem um Kicks, March, Versa ou Sentra com CVT desse período, recomendo:

1. Trocar o fluido da transmissão a cada 40.000km (não 60.000km como diz o manual)
2. Instalar um medidor de temperatura do óleo do CVT
3. Evitar acelerações bruscas com o carro frio
4. Em subidas longas, use o modo Sport ou manual
5. Em caso de sintomas, filme e documente TUDO para usar na concessionária

Entrei com um processo e consegui a substituição sem custo mesmo fora da garantia. Existem grupos de proprietários organizando ações coletivas que podem ser uma boa alternativa.',
                'relevance_score' => 98,
                'forum_url' => 'https://nissanbr.com.br/forum/topico/problema-cvt-kicks-analise-tecnica',
                'category' => 'troubleshooting',
                'tags' => ['Nissan', 'Kicks', 'CVT', 'câmbio', 'defeito', 'garantia', 'problema', 'transmissão'],
                'views' => 22456,
                'replies' => 376
            ],
            [
                'title' => 'Transformei meu Volkswagen Gol 2010 1.0 em um carro semi-autônomo gastando menos de R$2.000',
                'content' => 'Pessoal, sou entusiasta de tecnologia e resolvi transformar meu Gol 2010 básico em um carro com funções semi-autônomas por uma fração do preço de um carro novo. O resultado ficou incrível e resolvi documentar passo-a-passo.

Instalei:
1. Sistema de assistente de permanência em faixa usando uma Raspberry Pi, câmera e um motor DC acoplado à direção (R$560)
2. Sistema de controle de cruzeiro adaptativo com sensor de proximidade ultrassônico (R$380)
3. Central multimídia Android com GPS, câmera de ré e conexão com os sistemas acima (R$790)
4. Sensores de estacionamento dianteiro e traseiro (R$170)

O projeto todo levou 4 meses para concluir, mas o carro agora mantém distância do veículo à frente, alerta quando saio da faixa, tem assistência de estacionamento e muito mais. Tudo integrado à central multimídia.

O mais impressionante: o consumo de bateria é mínimo e nenhuma das modificações comprometeu os sistemas originais do carro. Tudo é um sistema "paralelo" que pode ser facilmente removido.

Vou compartilhar o código do Raspberry, diagrama de conexões e lista de peças utilizadas. Se alguém tiver interesse em replicar, estou à disposição para dúvidas!',
                'relevance_score' => 92,
                'forum_url' => 'https://www.clubedogol.com.br/forum/topico/projeto-gol-semi-autonomo-diy',
                'category' => 'modification',
                'tags' => ['Volkswagen', 'Gol', 'DIY', 'tecnologia', 'autonomo', 'modificação', 'raspberry', 'arduino', 'eletrônica'],
                'views' => 8942,
                'replies' => 167
            ],
            [
                'title' => 'Guia definitivo: todas as oficinas mecânicas em São Paulo com avaliação baseada em dados reais (Planilha com 300+ oficinas)',
                'content' => 'Depois de sofrer com oficinas de baixa qualidade, decidi criar um método para avaliar objetivamente as melhores oficinas de São Paulo. Passei 6 meses coletando dados e criei uma planilha com mais de 300 oficinas avaliadas.

Cada oficina foi avaliada em:
- Preço médio de serviços comuns (tabela comparativa com 20 serviços básicos)
- Tempo médio de entrega (baseado em relatos de clientes)
- Qualidade das peças utilizadas (originais, paralelas premium, paralelas comuns)
- Garantia oferecida
- Nota de satisfação média (compilada de Google, Reclame Aqui e grupos do Facebook)
- Especialidades (algumas oficinas são excelentes em elétrica mas ruins em suspensão, por exemplo)

Organizei por região da cidade e incluí informações sobre quais marcas cada oficina atende melhor.

Algumas descobertas surpreendentes:
1. As oficinas mais baratas nem sempre são as piores - encontrei 7 oficinas com preços abaixo da média e avaliações excelentes
2. Concessionárias têm desempenho muito variado - algumas são excelentes, outras péssimas
3. A zona sul tem as oficinas mais caras em média, mas também as mais bem avaliadas
4. Oficinas pequenas de bairro frequentemente superam redes conhecidas em satisfação

A planilha está disponível para download (link abaixo). Pretendo atualizar a cada 3 meses e expandir para outras cidades.',
                'relevance_score' => 96,
                'forum_url' => 'https://forum.webmotors.com.br/manutencao/oficinas-sao-paulo-avaliacao-completa',
                'category' => 'maintenance',
                'tags' => ['oficinas', 'São Paulo', 'manutenção', 'avaliação', 'comparativo', 'planilha', 'dados', 'concessionárias'],
                'views' => 31245,
                'replies' => 412
            ],
            [
                'title' => 'Análise profunda após 100.000 km com meu Jeep Compass diesel: o que deu problema e o que superou expectativas',
                'content' => 'Atingi a marca de 100.000 km no meu Jeep Compass Longitude Diesel 2018 e resolvi fazer um relatório detalhado de toda minha experiência. Uso o carro tanto na cidade quanto em viagens frequentes para o interior.

PONTOS FORTES que SUPERARAM minhas expectativas:
- Consumo médio real de 11,2 km/l na cidade e 14,5 km/l na estrada (melhor que o anunciado)
- Sistema de tração 4x4 funcionou impecavelmente em todas as situações (incluindo 2 atolamentos graves)
- Valor de revenda se manteve extremamente alto (consulta na FIPE mostra desvalorização de apenas 28% em 5 anos)
- Conforto em viagens longas é excepcional, sem fadiga mesmo após 8h dirigindo

PROBLEMAS que enfrentei:
- Falha na bomba de combustível aos 32.000 km (substituída em garantia)
- Infiltração no teto solar aos 45.000 km (resolvido após 3 tentativas na concessionária)
- Barulho na suspensão dianteira a partir dos 70.000 km (problema crônico do modelo, trocas recorrentes dos coxins)
- Central multimídia travou completamente aos 88.000 km (substituição completa custou R$4.800)

CUSTOS DE MANUTENÇÃO (valores reais que paguei):
- Revisões programadas até 100.000 km: R$12.460
- Manutenções corretivas fora da garantia: R$7.850
- Conjunto de pneus: troquei 2 vezes, total de R$6.200
- Seguro anual médio: R$3.900

MODIFICAÇÕES que fiz e recomendo:
- Protetor de cárter reforçado para uso off-road leve
- Calibração específica para o diesel brasileiro
- Snorkel para travessias mais profundas (instalação não afeta garantia)

Se estivesse comprando hoje, compraria novamente? SIM, mas optaria pelo modelo 2020 em diante, que resolveu vários dos problemas crônicos.',
                'relevance_score' => 97,
                'forum_url' => 'https://www.jeeptrack.com.br/forum/topico/relatorio-100mil-km-compass-diesel-analise-completa',
                'category' => 'maintenance',
                'tags' => ['Jeep', 'Compass', 'diesel', 'longo prazo', 'análise', 'manutenção', 'custo', 'revisões', '100.000 km'],
                'views' => 18560,
                'replies' => 287
            ]
        ];
        
        // Arrays para armazenar discussões por marca (garantir diversidade)
        $discussionsByMake = [];
        foreach (array_keys($carMakes) as $make) {
            $discussionsByMake[$make] = 0;
        }

        // Contador para garantir discussões balanceadas por marca
        $maxDiscussionsPerMake = 70; // Limitando a ~70 discussões por marca para garantir diversidade
        
        // Array para armazenar todas as discussões a serem inseridas
        $discussions = [];
        
        // Primeiro, adiciona as "gems" (insights especiais)
        foreach ($gems as $gem) {
            $publishedDate = $faker->dateTimeBetween('-2 years', '-3 months');
            
            $discussions[] = [
                'id' => (string) Str::uuid(),
                'title' => $gem['title'],
                'content' => $gem['content'],
                'forum_url' => $gem['forum_url'],
                'category' => $gem['category'],
                'tags' => json_encode($gem['tags']),
                'published_at' => $publishedDate,
                'view_count' => $gem['views'] ?? $faker->numberBetween(500, 20000),
                'reply_count' => $gem['replies'] ?? $faker->numberBetween(5, 300),
                'relevance_score' => $gem['relevance_score'],
                'usage_count' => 0,
                'last_used_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Agora, gera o restante das discussões até atingir 500
        while (count($discussions) < 500) {
            // Seleciona uma marca e modelo aleatoriamente
            $make = $faker->randomElement(array_keys($carMakes));
            
            // Verifica se já atingiu o máximo para essa marca
            if ($discussionsByMake[$make] >= $maxDiscussionsPerMake) {
                continue; // Pula para a próxima iteração para tentar outra marca
            }
            
            $model = $faker->randomElement($carMakes[$make]);
            $year = $faker->numberBetween(2005, 2025);
            
            // Seleciona um fórum aleatoriamente
            $forumUrl = $faker->randomElement(array_keys($forumSources));
            $forumName = $forumSources[$forumUrl];
            
            // Seleciona uma categoria aleatoriamente (com pesos para maior realismo)
            $categoryWeights = [
                'troubleshooting' => 30,
                'maintenance' => 25,
                'purchase' => 15,
                'comparison' => 10,
                'modification' => 10,
                'performance' => 5,
                'news' => 3,
                'other' => 2
            ];
            
            $category = $this->weightedRandom($categoryWeights);
            
            // Gera tags baseadas na categoria e veículo
            $tagsCount = $faker->numberBetween(3, 8);
            $baseTags = [$make, $model, strtolower($category)];
            $remainingTags = $faker->randomElements($commonTags, $tagsCount);
            $tags = array_unique(array_merge($baseTags, $remainingTags));
            
            // Gera título baseado na categoria
            $title = $this->generateDiscussionTitle(
                $category, 
                $make, 
                $model, 
                $year, 
                $commonIssues,
                $commonQuestions,
                $faker
            );
            
            // Gera conteúdo baseado na categoria
            $content = $this->generateDiscussionContent(
                $category, 
                $make, 
                $model, 
                $year, 
                $commonIssues,
                $commonQuestions,
                $faker
            );
            
            // Calcula pontuação de relevância (varia por categoria)
            $baseRelevance = match($category) {
                'troubleshooting' => $faker->numberBetween(60, 90),
                'maintenance' => $faker->numberBetween(50, 85),
                'modification' => $faker->numberBetween(55, 80),
                'performance' => $faker->numberBetween(60, 85),
                'purchase' => $faker->numberBetween(40, 70),
                'comparison' => $faker->numberBetween(45, 75),
                'news' => $faker->numberBetween(30, 60),
                'other' => $faker->numberBetween(20, 50),
            };
            
            // Adiciona bônus para conteúdos mais longos e detalhados
            $contentLengthBonus = min(10, strlen($content) / 1000);
            $relevanceScore = min(90, $baseRelevance + $contentLengthBonus);
            
            // Gera data de publicação (mais recente para maior relevância)
            $publishedDate = match(true) {
                $relevanceScore > 80 => $faker->dateTimeBetween('-6 months', '-1 week'),
                $relevanceScore > 70 => $faker->dateTimeBetween('-1 year', '-1 month'),
                $relevanceScore > 50 => $faker->dateTimeBetween('-2 years', '-3 months'),
                default => $faker->dateTimeBetween('-3 years', '-6 months'),
            };
            
            // Número de visualizações e respostas (correlacionadas com relevância)
            $viewCount = $faker->numberBetween(
                $relevanceScore * 50, 
                $relevanceScore * 150
            );
            
            $replyCount = (int)($viewCount * $faker->randomFloat(2, 0.01, 0.08));
            
            // Adiciona a discussão ao array
            $discussions[] = [
                'id' => (string) Str::uuid(),
                'title' => $title,
                'content' => $content,
                'forum_url' => $forumUrl,
                'category' => $category,
                'tags' => json_encode($tags),
                'published_at' => $publishedDate,
                'view_count' => $viewCount,
                'reply_count' => $replyCount,
                'relevance_score' => (int)$relevanceScore,
                'usage_count' => 0,
                'last_used_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Incrementa o contador para essa marca
            $discussionsByMake[$make]++;
        }
        
        // Insere todas as discussões no banco de dados
        DB::table('forum_discussions')->insert($discussions);
        
        $this->command->info('Foram criadas 500 discussões de fóruns automotivos com sucesso!');
    }
    
    /**
     * Gera um título realista para a discussão baseado na categoria.
     * 
     * @param string $category Categoria da discussão
     * @param string $make Marca do veículo
     * @param string $model Modelo do veículo
     * @param int $year Ano do veículo
     * @param array $commonIssues Lista de problemas comuns
     * @param array $commonQuestions Lista de perguntas comuns
     * @param \Faker\Generator $faker Instância do Faker
     * @return string
     */
    private function generateDiscussionTitle(
        string $category,
        string $make,
        string $model,
        int $year,
        array $commonIssues,
        array $commonQuestions,
        $faker
    ): string {
        $vehicleDesc = "{$make} {$model} {$year}";
        
        return match($category) {
            'troubleshooting' => $faker->randomElement([
                "Problema: {$faker->randomElement($commonIssues)} no meu {$vehicleDesc}",
                "{$vehicleDesc} - {$faker->randomElement($commonIssues)}. {$faker->randomElement($commonQuestions)}",
                "Socorro! {$faker->randomElement($commonIssues)} no {$vehicleDesc}",
                "Alguém já teve {$faker->randomElement($commonIssues)} no {$model}?",
                "URGENTE: {$faker->randomElement($commonIssues)} {$faker->randomElement($commonQuestions)}",
                "Luz de alerta acendeu no {$vehicleDesc} - O que pode ser?",
                "Barulho estranho no {$vehicleDesc} - {$faker->randomElement($commonQuestions)}"
            ]),
            
            'maintenance' => $faker->randomElement([
                "Revisão dos {$year} mil km no {$vehicleDesc} - Dúvidas",
                "Custo de manutenção do {$vehicleDesc} - Vale a pena?",
                "Troca de {$faker->randomElement(['óleo', 'filtro de ar', 'filtro de combustível', 'pastilhas de freio', 'amortecedores', 'correia dentada', 'velas', 'cabos'])} no {$vehicleDesc}",
                "Intervalo ideal para revisão do {$vehicleDesc}",
                "Concessionária vs. Oficina para manutenção do {$vehicleDesc}",
                "Esquema completo de manutenção preventiva para {$vehicleDesc}",
                "Revisão fora da concessionária - Perco a garantia do meu {$vehicleDesc}?"
            ]),
            
            'modification' => $faker->randomElement([
                "Tuning no {$vehicleDesc} - Quais modificações recomendam?",
                "Instalação de {$faker->randomElement(['GNV', 'turbo', 'kit rebaixamento', 'rodas aro '.$faker->numberBetween(17, 22), 'xenon', 'central multimídia', 'pneus off road'])} no {$vehicleDesc}",
                "Alguém já fez upgrade de {$faker->randomElement(['som', 'suspensão', 'freios', 'escapamento', 'motor', 'injeção'])} no {$vehicleDesc}?",
                "Projeto de personalização do meu {$vehicleDesc} - Fase 1",
                "Reprogramação de ECU para {$vehicleDesc} - Resultados reais",
                "Dúvida sobre legalidade de modificações no {$vehicleDesc}",
                "Transformei meu {$vehicleDesc} em versão {$faker->randomElement(['esportiva', 'off road', 'rally', 'track day'])}"
            ]),
            
            'performance' => $faker->randomElement([
                "Como melhorar o consumo do {$vehicleDesc}?",
                "Desempenho do {$vehicleDesc} após {$faker->numberBetween(10, 100)}mil km",
                "Aumento de potência para o {$vehicleDesc} - Vale a pena?",
                "Etanol vs. Gasolina no {$vehicleDesc} - Teste real",
                "Comparativo: {$vehicleDesc} vs {$faker->randomElement(array_diff($carMakes[$make], [$model]))}",
                "Consegui reduzir o consumo do meu {$vehicleDesc} em 20% - Veja como",
                "Problema de desempenho no {$vehicleDesc} após troca de {$faker->randomElement(['combustível', 'filtros', 'velas', 'bobinas'])}"
            ]),
            
            'purchase' => $faker->randomElement([
                "Vale a pena comprar {$vehicleDesc} em 2024?",
                "Comprei um {$vehicleDesc} - Primeiras impressões",
                "Dúvida: {$vehicleDesc} ou {$faker->randomElement(array_diff($carMakes[$make], [$model]))}?",
                "Avaliação de compra de {$vehicleDesc} usado - O que verificar?",
                "Preço justo para {$vehicleDesc} com {$faker->numberBetween(30, 150)}mil km?",
                "Relato: {$faker->numberBetween(1, 10)} anos com meu {$vehicleDesc}",
                "Compra de {$vehicleDesc} - Versão {$faker->randomElement(['básica', 'intermediária', 'top de linha'])}"
            ]),
            
            'comparison' => $faker->randomElement([
                "{$vehicleDesc} vs {$faker->randomElement(array_keys($carMakes))} {$faker->randomElement($carMakes[$faker->randomElement(array_keys($carMakes))])} - Comparativo detalhado",
                "Comparativo real: {$vehicleDesc} contra concorrentes diretos",
                "Mudei de um {$faker->randomElement(array_keys($carMakes))} para {$vehicleDesc} - Impressões",
                "Diferenças entre {$model} {$year} e {$model} {$year-3}",
                "Teste: {$vehicleDesc} vs. {$faker->randomElement(array_keys($carMakes))} {$faker->randomElement($carMakes[$faker->randomElement(array_keys($carMakes))])} - Consumo e manutenção",
                "Versões do {$make} {$model} - Qual o melhor custo-benefício?",
                "Comparei todas as versões do {$model} - Resultado surpreendente"
            ]),
            
            'news' => $faker->randomElement([
                "Novo {$make} {$model} {$year+1} - Informações antecipadas",
                "Flagra do novo {$model} nas ruas - O que mudou?",
                "Recall anunciado para {$vehicleDesc} - Detalhes",
                "{$make} anuncia fim da produção do {$model}?",
                "Nova geração do {$model} chegará em {$year+1} - Veja as novidades",
                "Reestilização do {$model} {$year+1} foi revelada - Fotos e detalhes",
                "Rumores: Próxima geração do {$model} será híbrida"
            ]),
            
            'other' => $faker->randomElement([
                "Grupos de WhatsApp/Telegram para donos de {$vehicleDesc}",
                "Encontro de {$model} em {$faker->city} - Quem vai?",
                "Seguro para {$vehicleDesc} - Valores e experiências",
                "App para monitoramento do {$vehicleDesc} - Recomendações",
                "Viagem de {$faker->numberBetween(1000, 5000)}km com {$vehicleDesc} - Relato",
                "Melhor combustível para {$vehicleDesc} - Experiências",
                "Preciso de indicação de oficina para {$vehicleDesc} em {$faker->city}"
            ])
        };
    }
    
    /**
     * Gera conteúdo realista para a discussão baseado na categoria.
     * 
     * @param string $category Categoria da discussão
     * @param string $make Marca do veículo
     * @param string $model Modelo do veículo
     * @param int $year Ano do veículo
     * @param array $commonIssues Lista de problemas comuns
     * @param array $commonQuestions Lista de perguntas comuns
     * @param \Faker\Generator $faker Instância do Faker
     * @return string
     */
    private function generateDiscussionContent(
        string $category,
        string $make,
        string $model,
        int $year,
        array $commonIssues,
        array $commonQuestions,
        $faker
    ): string {
        $vehicleDesc = "{$make} {$model} {$year}";
        $randomIssue = $faker->randomElement($commonIssues);
        $randomQuestion = $faker->randomElement($commonQuestions);
        $userCity = $faker->city;
        $kmCount = $faker->numberBetween(10, 150) . '.000';
        
        $nextYear = $year + 1;
        // Texto base dependendo da categoria
        $contentBase = match($category) {
            'troubleshooting' => "Olá pessoal do fórum,

Estou com um problema no meu {$vehicleDesc} que está me deixando preocupado. O carro tem atualmente {$kmCount} km rodados.

**Problema**: {$randomIssue}.

**Quando acontece**: " . $faker->randomElement([
                "Geralmente quando o carro está frio, logo após dar a partida.",
                "Principalmente em dias de chuva ou alta umidade.",
                "Sempre que acelero acima de {$faker->numberBetween(60, 120)} km/h.",
                "Quando o carro fica parado por mais de {$faker->numberBetween(2, 7)} dias.",
                "Em subidas íngremes ou quando o carro está muito carregado.",
                "Em qualquer situação, independentemente da temperatura ou condições.",
                "Somente quando o ar-condicionado está ligado."
            ]) . "

**O que já tentei fazer**: " . $faker->randomElement([
                "Levei na concessionária e eles disseram que é 'normal', mas não acho que seja.",
                "Troquei {$faker->randomElement(['o óleo', 'o filtro de ar', 'o filtro de combustível', 'as velas', 'a bateria'])} mas o problema persistiu.",
                "Fiz diagnóstico com scanner e não apareceu nenhum código de erro.",
                "Consultei outro mecânico que sugeriu ser problema de {$faker->randomElement(['sensor', 'atuador', 'módulo', 'chicote elétrico', 'bomba', 'boia'])}.",
                "Ainda não fiz nada, queria consultar vocês primeiro.",
                "Vi vários vídeos no YouTube mas nenhuma solução funcionou."
            ]) . "

Alguém já passou por isso? Quanto custou o reparo? Vale a pena consertar ou é caso de procurar outro mecânico?

Desde já agradeço a ajuda!",
            
            'maintenance' => "Boa tarde a todos,

Sou proprietário de um {$vehicleDesc} com {$kmCount} km rodados. Estou programando as próximas manutenções e gostaria de tirar algumas dúvidas.

**Situação atual**: " . $faker->randomElement([
                "O carro está funcionando perfeitamente, quero apenas manter em dia.",
                "Estou percebendo que o rendimento não está mais o mesmo de antes.",
                "Ouvi um comentário que essa motorização tem problemas recorrentes.",
                "Estou preparando o carro para uma viagem longa que farei no próximo mês.",
                "Comprei o carro recentemente usado e quero fazer uma revisão geral.",
                "Percebi um aumento no consumo de combustível nos últimos meses."
            ]) . "

**Dúvidas**:
1. Qual o intervalo recomendado para troca de {$faker->randomElement(['óleo', 'filtros', 'correia dentada', 'velas', 'fluido de freio', 'amortecedores'])}?
2. {$randomQuestion}
3. Qual a média de preço para revisão completa na {$faker->randomElement(['concessionária', 'oficina especializada', 'oficina de bairro'])}?

Se alguém tiver planilha de custos de manutenção para esse modelo, agradeço compartilhar!

Valeu pela ajuda!",
            
            'modification' => "E aí galera!

Acabei de adquirir um {$vehicleDesc} e já estou planejando algumas modificações. Meu objetivo é " . $faker->randomElement([
                "melhorar o desempenho sem comprometer muito a economia.",
                "deixar o visual mais esportivo e diferenciado.",
                "preparar para uso em estradas de terra nos fins de semana.",
                "melhorar o conforto e praticidade no dia a dia.",
                "atualizar a parte tecnológica sem desvalorizar muito o carro.",
                "resolver algumas limitações de fábrica conhecidas do modelo."
            ]) . "

**Modificações que estou pensando**:
- " . $faker->randomElement(['Instalar central multimídia Android', 'Trocar as rodas para aro ' . $faker->numberBetween(17, 22), 'Instalar kit de suspensão a ar', 'Reprogramação da ECU', 'Upgrade no sistema de freios', 'Instalação de GNV']) . "
- " . $faker->randomElement(['Melhorar o sistema de som', 'Colocar película nos vidros', 'Instalar faróis de LED/Xenon', 'Escapamento esportivo', 'Kit body', 'Protetor de cárter reforçado']) . "
- " . $faker->randomElement(['Bancos em couro', 'Teto solar', 'Câmera de ré com sensores', 'Kit turbo', 'Sistema de telemetria', 'Envelopamento personalizado']) . "

**Orçamento**: Tenho aproximadamente R$ {$faker->numberBetween(3, 15)}.000 para investir.

Alguém já fez alguma dessas modificações nesse modelo? Têm fotos para compartilhar? Alguma oficina recomendada em {$userCity}?

Valeu demais!",
            
            'performance' => "Fala pessoal!

Possuo um {$vehicleDesc} há {$faker->numberBetween(1, 5)} anos e gostaria de compartilhar/receber informações sobre desempenho.

**Meus números atuais**:
- Consumo cidade: {$faker->randomFloat(1, 6, 13)} km/l
- Consumo estrada: {$faker->randomFloat(1, 10, 18)} km/l
- 0-100 km/h: {$faker->randomFloat(1, 9, 16)} segundos (medição aproximada)
- Velocidade máxima testada: {$faker->numberBetween(160, 220)} km/h

**Observações**: " . $faker->randomElement([
                "Uso principalmente etanol e faço manutenção rigorosa.",
                "Já troquei o filtro de ar por um esportivo e senti diferença.",
                "Sempre abasteço em postos de confiança com combustível aditivado.",
                "Calibro os pneus semanalmente com a pressão recomendada.",
                "Tenho um app que monitora o desempenho e consumo em tempo real.",
                "Recentemente fiz uma limpeza de bicos e TBI que melhorou bastante."
            ]) . "

Acham que estes números estão bons para o modelo? Alguém consegue resultados melhores? Existe alguma modificação simples que possa melhorar o desempenho sem prejudicar a confiabilidade?

Grato pelas respostas!",
            
            'purchase' => "Olá a todos!

Estou considerando seriamente a compra de um {$vehicleDesc} e gostaria de ouvir a opinião de vocês que já possuem o modelo.

**Principais dúvidas**:
1. Qual o consumo real na cidade e estrada?
2. Existem problemas crônicos conhecidos nesse modelo/ano?
3. O custo de manutenção é muito alto comparado a concorrentes?
4. {$randomQuestion}
5. Quanto vocês pagaram no seguro?

**Uso que farei**: " . $faker->randomElement([
                "Principalmente cidade, com alguns trajetos de estrada nos fins de semana.",
                "Uso diário para trabalho, aproximadamente 30km por dia.",
                "Viagens frequentes de longa distância pela BR.",
                "Família com crianças, precisa ser espaçoso e seguro.",
                "Uso misto cidade/estrada, com algumas incursões em estradas de terra.",
                "Principalmente para trabalho em aplicativos de transporte."
            ]) . "

Estou em dúvida entre esse modelo e o {$faker->randomElement(array_keys($carMakes))} {$faker->randomElement($carMakes[$faker->randomElement(array_keys($carMakes))])}.

Agradeço antecipadamente a todos que puderem compartilhar suas experiências!",
            
            'comparison' => "Boa noite pessoal,

Estou no processo de decisão entre alguns modelos e gostaria de fazer um comparativo focado em experiências reais (e não só ficha técnica).

**Modelos em consideração**:
- {$vehicleDesc}
- {$faker->randomElement(array_keys($carMakes))} {$faker->randomElement($carMakes[$faker->randomElement(array_keys($carMakes))])} {$faker->numberBetween($year-2, $year+2)}
- {$faker->randomElement(array_keys($carMakes))} {$faker->randomElement($carMakes[$faker->randomElement(array_keys($carMakes))])} {$faker->numberBetween($year-2, $year+2)}

**Critérios importantes para mim**:
1. Custo-benefício em longo prazo (incluindo desvalorização)
2. Confiabilidade e frequência de problemas
3. Consumo real (não o anunciado pelas montadoras)
4. Conforto em viagens longas
5. Custo de manutenção após fim da garantia

Se alguém já teve/testou mais de um desses modelos, poderia compartilhar impressões comparativas? Ou se conhece problemas recorrentes de algum deles?

Obrigado pela ajuda na decisão!",
            
            'news' => "Pessoal, compartilhando algumas informações que obtive sobre o {$make} {$model} {$nextYear}.

**Fonte da informação**: " . $faker->randomElement([
                "Conversei com um gerente de concessionária que confirmou os dados.",
                "Saiu matéria na revista {$faker->randomElement(['Quatro Rodas', 'Auto Esporte', 'Car and Driver', 'Motor Show'])}.",
                "Meu primo trabalha na montadora e passou algumas informações internas.",
                "Vi o modelo de teste rodando camuflado aqui em {$userCity}.",
                "Foi apresentado no salão do automóvel internacional.",
                "Divulgação oficial no site da montadora hoje pela manhã."
            ]) . "

**Principais novidades**:
- " . $faker->randomElement([
                "Novo motor {$faker->randomFloat(1, 1.0, 2.5)} turbo com {$faker->numberBetween(120, 220)} cv",
                "Câmbio automático de {$faker->numberBetween(6, 10)} marchas substitui o antigo",
                "Reestilização completa da dianteira com novo conjunto ótico",
                "Interior totalmente redesenhado com nova central multimídia",
                "Nova plataforma global compartilhada com outros modelos da marca",
                "Versão híbrida/elétrica confirmada para o mercado brasileiro"
            ]) . "
- " . $faker->randomElement([
                "Nova versão aventureira para competir com {$faker->randomElement(array_keys($carMakes))}",
                "Pacote de segurança com {$faker->numberBetween(4, 8)} airbags em todas as versões",
                "Sistema de assistência ao motorista com piloto automático adaptativo",
                "Aumento das dimensões: {$faker->numberBetween(5, 15)}cm mais comprido",
                "Novos equipamentos de série desde a versão de entrada",
                "Suspensão traseira independente em todas as versões"
            ]) . "
- " . $faker->randomElement([
                "Previsão de chegada às concessionárias em {$faker->monthName()} de {$year}",
                "Preços estimados a partir de R$ {$faker->numberBetween(70, 150)} mil",
                "Produção nacional confirmada na fábrica de {$faker->city}",
                "Importação confirmada da unidade do {$faker->randomElement(['México', 'Argentina', 'China', 'Índia', 'Coreia do Sul'])}",
                "Fim da motorização {$faker->randomFloat(1, 1.0, 2.0)} aspirada",
                "Garantia estendida para {$faker->numberBetween(5, 10)} anos"
            ]) . "

Se alguém tiver mais informações ou fotos, favor compartilhar!",
            
            'other' => "Olá, pessoal do fórum!

Sou proprietário de um {$vehicleDesc} há {$faker->numberBetween(1, 10)} anos e gostaria de " . $faker->randomElement([
                "saber se existe algum grupo de WhatsApp/Telegram para trocarmos informações.",
                "indicações de oficinas confiáveis na região de {$userCity}.",
                "dicas para viagem longa que farei nas próximas férias (aproximadamente {$faker->numberBetween(1000, 5000)}km).",
                "saber onde encontrar peças originais por um preço mais acessível.",
                "compartilhar uma experiência inusitada que tive com o carro semana passada.",
                "encontrar outros proprietários para um evento que estou organizando."
            ]) . "

" . $faker->paragraph(3) . "

" . $faker->paragraph(2) . "

Agradeço desde já a atenção e ajuda de todos!

Abraços!"
        };
        
        // Adiciona variação ao conteúdo
        $additionalContent = $faker->boolean(70) ? "\n\n**Atualização**: " . $faker->paragraph(2) : "";
        
        // Para alguns conteúdos, adiciona comentários de resposta para criar uma discussão mais rica
        if ($faker->boolean(30)) {
            $additionalContent .= "\n\n--- \n**Resposta de ".ucfirst($faker->userName)."**: \n" . $faker->paragraph(3);
            
            if ($faker->boolean(50)) {
                $additionalContent .= "\n\n--- \n**Resposta de ".ucfirst($faker->userName)."**: \n" . $faker->paragraph(2);
            }
        }
        
        return $contentBase . $additionalContent;
    }
    
    /**
     * Seleciona um elemento aleatório de um array baseado em pesos.
     * 
     * @param array $weightedValues Array associativo com valores e seus pesos
     * @return string|null Valor selecionado ou null se o array estiver vazio
     */
    private function weightedRandom(array $weightedValues): ?string
    {
        if (empty($weightedValues)) {
            return null;
        }
        
        $totalWeight = array_sum($weightedValues);
        $randomNumber = mt_rand(1, $totalWeight);
        
        foreach ($weightedValues as $value => $weight) {
            $randomNumber -= $weight;
            if ($randomNumber <= 0) {
                return $value;
            }
        }
        
        // Fallback, não deveria chegar aqui
        return array_key_first($weightedValues);
    }
}