<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;
use Src\ArticleGenerator\Domain\Entity\ForumCategory;

/**
 * Seeder para popular a tabela forum_discussions com dados de discussões
 * de fóruns automotivos brasileiros realistas.
 * 
 * Este seeder cria 200 discussões com tópicos variados relacionados a veículos,
 * manutenção, problemas comuns e dúvidas de usuários, simulando o conteúdo
 * real encontrado em fóruns automotivos brasileiros.
 */
class ForumDiscussionSeeder extends Seeder
{
    /**
     * Executa o seeder para criar 200 discussões de fóruns automotivos.
     *
     * @return void
     */
    public function run(): void
    {
        // Configura o Faker para português brasileiro
        $faker = FakerFactory::create('pt_BR');
        
        // Obtém as categorias disponíveis
        $categories = array_column(ForumCategory::cases(), 'value');
        
        // Fontes de fóruns automotivos brasileiros comuns
        $forumSources = [
            'https://forum.carrosnaweb.com.br/viewforum.php?f=',
            'https://www.cluzbrasil.com/forum/viewforum.php?f=',
            'https://www.forofiatbrasil.com/forum/viewforum.php?f=',
            'https://forumvwbrasil.com.br/viewforum.php?f=',
            'https://www.grupocarrobyvolks.com.br/forum/viewtopic.php?f=',
            'https://www.clubedofiatbrasil.com.br/forum/viewtopic.php?t=',
            'https://www.clubedochevet.com.br/forum/viewtopic.php?f=',
            'https://www.nissanclube.com.br/forum/viewtopic.php?f=',
            'https://www.hondaclubbrasil.com.br/forum/viewtopic.php?f=',
            'https://forum.webmotors.com.br/viewtopic.php?t='
        ];
        
        // Modelos de veículos populares no Brasil para usar nas discussões
        $carModels = [
            'Fiat' => ['Uno', 'Palio', 'Strada', 'Toro', 'Argo', 'Cronos', 'Pulse', 'Mobi', 'Fastback'],
            'Volkswagen' => ['Gol', 'Voyage', 'Fox', 'Polo', 'Virtus', 'T-Cross', 'Nivus', 'Jetta', 'Taos', 'Saveiro'],
            'Chevrolet' => ['Onix', 'Prisma', 'Cruze', 'Tracker', 'S10', 'Spin', 'Equinox', 'Montana', 'Corsa'],
            'Ford' => ['Ka', 'Fiesta', 'Focus', 'EcoSport', 'Ranger', 'Bronco', 'Territory', 'Maverick'],
            'Hyundai' => ['HB20', 'Creta', 'Tucson', 'i30', 'Elantra', 'Santa Fe', 'Azera'],
            'Toyota' => ['Corolla', 'Etios', 'Yaris', 'Hilux', 'SW4', 'RAV4', 'Camry', 'Corolla Cross'],
            'Honda' => ['Civic', 'Fit', 'City', 'HR-V', 'WR-V', 'CR-V', 'Accord'],
            'Nissan' => ['March', 'Versa', 'Sentra', 'Kicks', 'Frontier', 'Leaf'],
            'Renault' => ['Kwid', 'Sandero', 'Logan', 'Stepway', 'Duster', 'Captur', 'Oroch'],
            'Jeep' => ['Renegade', 'Compass', 'Commander', 'Wrangler', 'Grand Cherokee']
        ];
        
        // Tags automotivas comuns para categorizar as discussões
        $commonTags = [
            'manutenção', 'troca de óleo', 'suspensão', 'motor', 'câmbio', 'freios', 'elétrica', 
            'problemas comuns', 'revisão', 'recall', 'consumo', 'preço', 'conforto', 'segurança',
            'comparativo', 'potência', 'tecnologia', 'acessórios', 'modificações', 'desempenho',
            'barulho', 'vibração', 'compra', 'venda', 'seguro', 'concessionária', 'oficina',
            'pecas originais', 'customização', 'som automotivo', 'pneus', 'rodas', 'faróis',
            'ar condicionado', 'direção hidráulica', 'injeção eletrônica', 'airbag', 'abs'
        ];
        
        // Estruturas de títulos de discussões para criar variações realistas
        $titleTemplates = [
            'Problema com [componente] do [modelo] [ano]',
            'Dúvida sobre [componente] do [marca] [modelo]',
            'Barulho estranho no [componente] do [modelo]',
            'Devo trocar meu [modelo] por um [modelo2]?',
            'Consumo de combustível [modelo] [ano]',
            'Melhor [componente] para [marca] [modelo]',
            'Comparativo: [modelo] vs [modelo2]',
            'Ajuda com [componente] [marca] [modelo] [ano]',
            'Vale a pena comprar [marca] [modelo] [ano]?',
            'Opinião sobre [marca] [modelo] [ano]',
            'Troca de [componente]: como fazer no [modelo]?',
            'Kit GNV para [marca] [modelo]: vale a pena?',
            'Experiência com o novo [marca] [modelo]',
            'Falha no [componente] do [modelo] após [número] km',
            'Recall [marca] [modelo] [ano]: alguém mais?',
            'Luz de injeção acesa no [modelo] [ano]',
            'Oficina recomendada para [marca] em [cidade]',
            'Peças para [marca] [modelo]: onde encontrar?',
            'Modificações no meu [marca] [modelo] [ano]',
            'Manual do proprietário [marca] [modelo] [ano]',
            'Filtro de [componente] para [modelo]: qual usar?',
            'Óleo recomendado para [marca] [modelo] [ano]',
            'Meu [marca] [modelo] está [problema]',
            'Como resolver [problema] no [modelo]?',
            'Manutenção preventiva [marca] [modelo]',
            'Seguro para [marca] [modelo]: dicas?',
            'Som automotivo para [marca] [modelo]',
            'Rodas aro [número] para [modelo]',
            'Suspensão rebaixada para [modelo]: prós e contras',
            'Troca de [componente] a cada quantos km no [modelo]?'
        ];
        
        // Componentes de veículos para usar nas discussões
        $carComponents = [
            'motor', 'câmbio', 'suspensão', 'freios', 'direção', 'radiador', 'alternador', 
            'bateria', 'filtro de ar', 'filtro de óleo', 'filtro de combustível', 'velas', 
            'cabos de vela', 'bobina', 'correia dentada', 'correia do alternador', 'bomba d\'água',
            'bomba de combustível', 'bicos injetores', 'catalisador', 'escapamento', 'coxins',
            'amortecedores', 'molas', 'buchas', 'bandejas', 'pivôs', 'terminal de direção',
            'pastilhas de freio', 'discos de freio', 'tambor de freio', 'sapatas de freio',
            'cilindro mestre', 'fluido de freio', 'óleo do motor', 'óleo do câmbio', 'embreagem',
            'platô', 'disco de embreagem', 'rolamento', 'caixa de direção', 'bomba de direção',
            'ar condicionado', 'compressor do ar', 'condensador', 'evaporador', 'filtro de cabine',
            'faróis', 'lanternas', 'painel', 'computador de bordo', 'central multimídia',
            'vidros elétricos', 'travas elétricas', 'alarme', 'sensor de estacionamento',
            'câmera de ré', 'airbag', 'cinto de segurança', 'volante', 'coluna de direção'
        ];
        
        // Problemas comuns em veículos
        $commonProblems = [
            'vazamento de óleo', 'consumo excessivo de combustível', 'superaquecimento',
            'barulho ao frear', 'dificuldade para engatar marchas', 'volante duro',
            'ar condicionado fraco', 'falha na partida', 'marcha lenta irregular',
            'vibração', 'barulho na suspensão', 'luzes no painel acesas', 'perda de potência',
            'falha na aceleração', 'fumaça no escapamento', 'bateria descarregando',
            'rangido ao fazer curvas', 'pedal de freio mole', 'vazamento de água',
            'trepidação', 'corrosão', 'estofado rasgado', 'desembaçador ineficiente',
            'fechadura travada', 'controle de estabilidade com falha', 'sensor de oxigênio',
            'carro morrendo em movimento', 'sistema stop/start com defeito'
        ];
        
        // Cidades brasileiras para usar nos contextos
        $cities = [
            'São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Brasília', 'Salvador',
            'Curitiba', 'Porto Alegre', 'Recife', 'Fortaleza', 'Manaus', 'Goiânia',
            'Belém', 'Campinas', 'São Luís', 'Guarulhos', 'Ribeirão Preto', 'Sorocaba',
            'Florianópolis', 'Joinville', 'Londrina', 'Maringá', 'Uberlândia'
        ];
        
        // Anos comuns para veículos no Brasil
        $carYears = range(2010, 2025);
        
        // Gera 200 discussões
        $discussionsData = [];
        
        for ($i = 0; $i < 200; $i++) {
            // Seleciona elementos aleatórios para construir uma discussão realista
            $marca = array_rand($carModels);
            $modelo = $faker->randomElement($carModels[$marca]);
            $marca2 = array_rand($carModels);
            $modelo2 = $faker->randomElement($carModels[$marca2]);
            $ano = $faker->randomElement($carYears);
            $componente = $faker->randomElement($carComponents);
            $problema = $faker->randomElement($commonProblems);
            $cidade = $faker->randomElement($cities);
            $numero = $faker->numberBetween(2, 22);
            
            // Seleciona e formata um template de título
            $titleTemplate = $faker->randomElement($titleTemplates);
            $title = str_replace(
                ['[marca]', '[modelo]', '[modelo2]', '[componente]', '[problema]', '[ano]', '[cidade]', '[número]'],
                [$marca, $modelo, $modelo2, $componente, $problema, $ano, $cidade, $numero],
                $titleTemplate
            );
            
            // Gera tags relevantes para a discussão
            $tags = $faker->randomElements($commonTags, $faker->numberBetween(3, 6));
            // Adiciona tags específicas baseadas no conteúdo da discussão
            $tags[] = strtolower($marca);
            $tags[] = strtolower($marca) . ' ' . strtolower($modelo);
            if (str_contains($title, $componente)) {
                $tags[] = $componente;
            }
            
            // Gera conteúdo da discussão
            $content = $this->generateDiscussionContent(
                $faker, 
                $title, 
                $marca, 
                $modelo, 
                $ano, 
                $componente, 
                $problema,
                $carModels
            );
            
            // Seleciona uma fonte de fórum aleatória
            $forumUrl = $faker->randomElement($forumSources) . $faker->numberBetween(1, 99);
            
            // Seleciona uma categoria apropriada com base no conteúdo
            $category = $this->determineCategory($title, $content);
            
            // Gera dados da discussão
            $discussionData = [
                'id' => (string) Str::uuid(),
                'title' => $title,
                'content' => $content,
                'forum_url' => $forumUrl,
                'category' => $category,
                'tags' => json_encode(array_unique($tags)),
                'published_at' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s'),
                'view_count' => $faker->numberBetween(10, 5000),
                'reply_count' => $faker->numberBetween(0, 50),
                'relevance_score' => $faker->numberBetween(30, 100),
                'usage_count' => $faker->numberBetween(0, 10),
                'last_used_at' => $faker->optional(0.7)->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            $discussionsData[] = $discussionData;
        }
        
        // Insere as discussões no banco de dados em lote para melhor performance
        DB::table('forum_discussions')->insert($discussionsData);
        
        $this->command->info('200 discussões de fóruns foram criadas com sucesso!');
    }
    
    /**
     * Gera conteúdo realista para uma discussão de fórum automotivo.
     *
     * @param \Faker\Generator $faker Instância do Faker
     * @param string $title Título da discussão
     * @param string $marca Marca do veículo
     * @param string $modelo Modelo do veículo
     * @param int $ano Ano do veículo
     * @param string $componente Componente do veículo mencionado
     * @param string $problema Problema mencionado
     * @return string Conteúdo da discussão
     */
    private function generateDiscussionContent(
        $faker, 
        string $title, 
        string $marca, 
        string $modelo, 
        int $ano, 
        string $componente, 
        string $problema,
        array $carModels
    ): string {
        // Estruturas introdutórias comuns em fóruns
        $intros = [
            "Fala galera do fórum,\n\n",
            "Olá pessoal,\n\n",
            "E aí pessoal, tudo bem?\n\n",
            "Bom dia/tarde/noite a todos,\n\n",
            "Saudações, membros do fórum!\n\n",
            "Galera, preciso da ajuda de vocês...\n\n",
            "Gente, estou com um problema no meu carro e preciso de conselhos.\n\n",
            "Amigos do fórum, estou passando por uma situação com meu carro e gostaria de compartilhar.\n\n"
        ];
        
        // Frases de conclusão comuns
        $conclusions = [
            "\n\nAlguém já passou por isso? Alguma dica?\n\nObrigado desde já!",
            "\n\nSe alguém puder ajudar, agradeço muito!",
            "\n\nAlguém sabe me dizer o que pode ser? Valeu!",
            "\n\nAgradeço qualquer informação que possam compartilhar.",
            "\n\nEstou desesperado! Socorro!",
            "\n\nJá levei na concessionária, mas queria uma segunda opinião antes de fazer o serviço.",
            "\n\nVale a pena consertar ou é melhor vender o carro?",
            "\n\nAlguém indica uma boa oficina que entenda desse problema? Obrigado!",
            "\n\nQuanto vocês acham que vai custar esse reparo?",
            "\n\nPreciso resolver isso o quanto antes. Agradeço a ajuda!"
        ];
        
        // Frases contextuais baseadas no tipo de discussão
        if (str_contains($title, 'Problema') || str_contains($title, 'problema') || str_contains($title, 'falha')) {
            $content = $faker->randomElement($intros);
            $content .= "Tenho um {$marca} {$modelo} {$ano} e recentemente comecei a notar um problema no {$componente}. ";
            $content .= "{$faker->sentence(8, true)} {$problema}. {$faker->sentence(10, true)}\n\n";
            $content .= "Já tentei {$faker->sentence(8, true)}, mas o problema persiste. {$faker->sentence(7, true)}";
            $content .= $faker->randomElement($conclusions);
        } 
        elseif (str_contains($title, 'Dúvida') || str_contains($title, 'dúvida') || str_contains($title, '?')) {
            $content = $faker->randomElement($intros);
            $content .= "Estou pensando em {$faker->sentence(6, true)} no meu {$marca} {$modelo} {$ano}. ";
            $content .= "{$faker->sentence(8, true)} {$componente}. {$faker->sentence(10, true)}\n\n";
            $content .= "Gostaria de saber se {$faker->sentence(10, true)}. {$faker->sentence(7, true)}";
            $content .= $faker->randomElement($conclusions);
        }
        elseif (str_contains($title, 'Comparativo') || str_contains($title, 'vs') || str_contains($title, 'comparação')) {
            $content = $faker->randomElement($intros);
            $content .= "Estou em dúvida entre o {$marca} {$modelo} e {$faker->randomElement($carModels[$marca])}. ";
            $content .= "{$faker->sentence(10, true)}\n\n";
            $content .= "No quesito {$componente}, qual vocês acham que é melhor? {$faker->sentence(8, true)}\n\n";
            $content .= "Minha prioridade é {$faker->sentence(5, true)}. {$faker->sentence(7, true)}";
            $content .= $faker->randomElement($conclusions);
        }
        elseif (str_contains($title, 'Vale a pena') || str_contains($title, 'Opinião')) {
            $content = $faker->randomElement($intros);
            $content .= "Estou pensando em comprar um {$marca} {$modelo} {$ano}. {$faker->sentence(8, true)}\n\n";
            $content .= "Gostaria de saber a opinião de quem já tem esse carro. ";
            $content .= "Como é o {$componente}? {$faker->sentence(6, true)} {$problema}?\n\n";
            $content .= "{$faker->sentence(9, true)}";
            $content .= $faker->randomElement($conclusions);
        }
        elseif (str_contains($title, 'Manutenção') || str_contains($title, 'Troca') || str_contains($title, 'Revisão')) {
            $content = $faker->randomElement($intros);
            $content .= "Chegou a hora de fazer a manutenção do {$componente} do meu {$marca} {$modelo} {$ano}. ";
            $content .= "{$faker->sentence(8, true)}\n\n";
            $content .= "Gostaria de saber {$faker->sentence(10, true)}. ";
            $content .= "O manual recomenda {$faker->sentence(7, true)}, mas já ouvi falar que {$faker->sentence(8, true)}.\n\n";
            $content .= "{$faker->sentence(6, true)}";
            $content .= $faker->randomElement($conclusions);
        }
        else {
            $content = $faker->randomElement($intros);
            $content .= "Tenho um {$marca} {$modelo} {$ano} e queria compartilhar minha experiência sobre o {$componente}. ";
            $content .= "{$faker->sentence(12, true)}\n\n";
            $content .= "{$faker->paragraph(3)}\n\n";
            $content .= "{$faker->sentence(8, true)}";
            $content .= $faker->randomElement($conclusions);
        }
        
        // Adiciona menção de quilometragem para tornar mais realista
        $kmOptions = [
            "Meu carro está com {$faker->numberBetween(10, 300)}mil km rodados.",
            "Já rodei {$faker->numberBetween(10, 300)}mil km com ele.",
            "Tenho {$faker->numberBetween(10, 300)} mil km no hodômetro.",
            "Comprei com {$faker->numberBetween(10, 150)}mil km e hoje está com {$faker->numberBetween(160, 300)}mil km."
        ];
        $content = str_replace("[KM]", $faker->randomElement($kmOptions), $content);
        
        return $content;
    }
    
    /**
     * Determina a categoria mais apropriada para a discussão com base no conteúdo.
     *
     * @param string $title Título da discussão
     * @param string $content Conteúdo da discussão
     * @return string Categoria determinada
     */
    private function determineCategory(string $title, string $content): string
    {
        $lowerTitle = strtolower($title);
        $lowerContent = strtolower($content);
        
        // Mapeia palavras-chave para categorias
        $categoryKeywords = [
            'maintenance' => ['manutenção', 'troca de óleo', 'revisão', 'trocar', 'óleo', 'filtro'],
            'troubleshooting' => ['problema', 'falha', 'erro', 'luz', 'acesa', 'barulho', 'não funciona', 'quebrou'],
            'performance' => ['desempenho', 'potência', 'consumo', 'velocidade', 'aceleração', 'economia'],
            'modification' => ['modificação', 'instalar', 'personalizar', 'tuning', 'acessório', 'upgrade'],
            'purchase' => ['comprar', 'vale a pena', 'compra', 'preço', 'valor', 'promoção', 'financiamento'],
            'comparison' => ['comparação', 'comparativo', 'versus', 'vs', 'melhor', 'diferença', 'qual escolher'],
            'news' => ['lançamento', 'novidade', 'atualização', 'nova versão', 'recall', 'anúncio'],
        ];
        
        // Verifica cada categoria baseada nas palavras-chave no título e conteúdo
        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lowerTitle, $keyword) || str_contains($lowerContent, $keyword)) {
                    return $category;
                }
            }
        }
        
        // Categoria padrão se nenhuma correspondência for encontrada
        return 'other';
    }
}