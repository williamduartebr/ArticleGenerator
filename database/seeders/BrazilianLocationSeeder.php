<?php

declare(strict_types=1);

namespace Database\Seeders;

// Adicionar mais cidades
// https://grok.com/chat/dacbda7e-4eca-497a-b7a1-fd514de9c286

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Src\ArticleGenerator\Domain\Entity\BrazilianStateCode;
use Src\ArticleGenerator\Domain\Entity\TrafficPattern;
use Faker\Factory as FakerFactory;

/**
 * Seeder para popular a tabela brazilian_locations com dados realistas
 * de cidades e regiões brasileiras para uso no sistema de geração de artigos.
 */
class BrazilianLocationSeeder extends Seeder
{
    /**
     * Executa o seeder para popular a tabela de localizações brasileiras.
     *
     * @return void
     */
    public function run(): void
    {
        // Configura o faker para português brasileiro
        $faker = FakerFactory::create('pt_BR');

        // Limpa a tabela antes de inserir novos dados
        DB::table('brazilian_locations')->truncate();

        // Mapeia estados para regiões geográficas
        $stateRegions = [
            'AC' => 'Norte', 'AM' => 'Norte', 'AP' => 'Norte', 'PA' => 'Norte',
            'RO' => 'Norte', 'RR' => 'Norte', 'TO' => 'Norte',
            'AL' => 'Nordeste', 'BA' => 'Nordeste', 'CE' => 'Nordeste', 'MA' => 'Nordeste',
            'PB' => 'Nordeste', 'PE' => 'Nordeste', 'PI' => 'Nordeste', 'RN' => 'Nordeste',
            'SE' => 'Nordeste', 'DF' => 'Centro-Oeste', 'GO' => 'Centro-Oeste',
            'MS' => 'Centro-Oeste', 'MT' => 'Centro-Oeste', 'ES' => 'Sudeste',
            'MG' => 'Sudeste', 'RJ' => 'Sudeste', 'SP' => 'Sudeste', 'PR' => 'Sul',
            'RS' => 'Sul', 'SC' => 'Sul',
        ];

        // Insere as principais cidades
        $locations = [];
        foreach ($this->getMajorCities() as $cityData) {
            if (!in_array($cityData['state_code'], array_keys($stateRegions))) {
                continue; // Valida state_code
            }
            $locations[] = [
                'id' => (string) Str::uuid(),
                'city' => $cityData['city'],
                'region' => $cityData['region'],
                'state_code' => $cityData['state_code'],
                'traffic_pattern' => $cityData['traffic_pattern'],
                'latitude' => $cityData['latitude'],
                'longitude' => $cityData['longitude'],
                'population' => $cityData['population'],
                'postal_code_range' => $cityData['postal_code_range'],
                'usage_count' => rand(0, 15),
                'last_used_at' => rand(0, 5) > 3 ? now()->subDays(rand(1, 60)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('brazilian_locations')->insert($locations);

        // Adiciona cidades de médio porte e pequenas
        $this->seedMediumSizedCities($faker, $stateRegions);
        $this->seedSmallTowns($faker, $stateRegions);
    }

    /**
     * Retorna dados das principais cidades brasileiras.
     *
     * Nota: Esta lista contém 50 cidades como amostra. Para as ~200 cidades principais,
     * use um dataset externo (ex.: SimpleMaps ou IBGE) e armazene em um arquivo JSON.
     * Exemplo de integração: `json_decode(file_get_contents('cities.json'), true)`.
     *
     * @return array Lista de dados das principais cidades
     */
    private function getMajorCities(): array
    {
        return [
            // Capitais
            [
                'city' => 'São Paulo', 'region' => 'Avenida Paulista', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::CONGESTED->value, 'latitude' => -23.5558,
                'longitude' => -46.6396, 'population' => 11500000, 'postal_code_range' => '01000-000 a 05999-999'
            ],
            [
                'city' => 'Rio de Janeiro', 'region' => 'Copacabana', 'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -22.9714,
                'longitude' => -43.1823, 'population' => 6500000, 'postal_code_range' => '20000-000 a 28999-999'
            ],
            [
                'city' => 'Brasília', 'region' => 'Plano Piloto', 'state_code' => 'DF',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -15.7801,
                'longitude' => -47.9292, 'population' => 3100000, 'postal_code_range' => '70000-000 a 73699-999'
            ],
            [
                'city' => 'Salvador', 'region' => 'Barra', 'state_code' => 'BA',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -13.0096,
                'longitude' => -38.5262, 'population' => 2900000, 'postal_code_range' => '40000-000 a 44999-999'
            ],
            [
                'city' => 'Fortaleza', 'region' => 'Praia de Iracema', 'state_code' => 'CE',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -3.7227,
                'longitude' => -38.5159, 'population' => 2700000, 'postal_code_range' => '60000-000 a 63999-999'
            ],
            [
                'city' => 'Belo Horizonte', 'region' => 'Savassi', 'state_code' => 'MG',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -19.9433,
                'longitude' => -43.9346, 'population' => 2500000, 'postal_code_range' => '30000-000 a 34999-999'
            ],
            [
                'city' => 'Manaus', 'region' => 'Centro', 'state_code' => 'AM',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -3.1142,
                'longitude' => -60.0211, 'population' => 2250000, 'postal_code_range' => '69000-000 a 69999-999'
            ],
            [
                'city' => 'Curitiba', 'region' => 'Batel', 'state_code' => 'PR',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -25.4431,
                'longitude' => -49.2879, 'population' => 1950000, 'postal_code_range' => '80000-000 a 87999-999'
            ],
            [
                'city' => 'Recife', 'region' => 'Boa Viagem', 'state_code' => 'PE',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -8.1196,
                'longitude' => -34.9017, 'population' => 1650000, 'postal_code_range' => '50000-000 a 56999-999'
            ],
            [
                'city' => 'Goiânia', 'region' => 'Setor Bueno', 'state_code' => 'GO',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -16.7087,
                'longitude' => -49.2649, 'population' => 1550000, 'postal_code_range' => '74000-000 a 76999-999'
            ],
            // Outras capitais
            [
                'city' => 'Porto Alegre', 'region' => 'Moinhos de Vento', 'state_code' => 'RS',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -30.0331,
                'longitude' => -51.2300, 'population' => 1450000, 'postal_code_range' => '90000-000 a 94999-999'
            ],
            [
                'city' => 'Belém', 'region' => 'Ver-o-Peso', 'state_code' => 'PA',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -1.4542,
                'longitude' => -48.5021, 'population' => 1500000, 'postal_code_range' => '66000-000 a 68999-999'
            ],
            [
                'city' => 'São Luís', 'region' => 'Centro Histórico', 'state_code' => 'MA',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -2.5297,
                'longitude' => -44.3028, 'population' => 1100000, 'postal_code_range' => '65000-000 a 65999-999'
            ],
            [
                'city' => 'Maceió', 'region' => 'Pajuçara', 'state_code' => 'AL',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -9.6658,
                'longitude' => -35.7353, 'population' => 1000000, 'postal_code_range' => '57000-000 a 57999-999'
            ],
            [
                'city' => 'Campo Grande', 'region' => 'Centro', 'state_code' => 'MS',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -20.4428,
                'longitude' => -54.6463, 'population' => 900000, 'postal_code_range' => '79000-000 a 79999-999'
            ],
            [
                'city' => 'Cuiabá', 'region' => 'Centro Político', 'state_code' => 'MT',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -15.5961,
                'longitude' => -56.0967, 'population' => 620000, 'postal_code_range' => '78000-000 a 78999-999'
            ],
            [
                'city' => 'Teresina', 'region' => 'Centro', 'state_code' => 'PI',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -5.0892,
                'longitude' => -42.8019, 'population' => 870000, 'postal_code_range' => '64000-000 a 64999-999'
            ],
            [
                'city' => 'Natal', 'region' => 'Ponta Negra', 'state_code' => 'RN',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -5.7945,
                'longitude' => -35.2110, 'population' => 880000, 'postal_code_range' => '59000-000 a 59999-999'
            ],
            [
                'city' => 'João Pessoa', 'region' => 'Tambaú', 'state_code' => 'PB',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -7.1153,
                'longitude' => -34.8631, 'population' => 820000, 'postal_code_range' => '58000-000 a 58999-999'
            ],
            [
                'city' => 'Aracaju', 'region' => 'Atalaia', 'state_code' => 'SE',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -10.9112,
                'longitude' => -37.0718, 'population' => 670000, 'postal_code_range' => '49000-000 a 49999-999'
            ],
            [
                'city' => 'Palmas', 'region' => 'Plano Diretor Sul', 'state_code' => 'TO',
                'traffic_pattern' => TrafficPattern::LIGHT->value, 'latitude' => -10.1675,
                'longitude' => -48.3277, 'population' => 310000, 'postal_code_range' => '77000-000 a 77999-999'
            ],
            [
                'city' => 'Porto Velho', 'region' => 'Centro', 'state_code' => 'RO',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -8.7618,
                'longitude' => -63.9039, 'population' => 540000, 'postal_code_range' => '76800-000 a 76999-999'
            ],
            [
                'city' => 'Boa Vista', 'region' => 'Centro', 'state_code' => 'RR',
                'traffic_pattern' => TrafficPattern::LIGHT->value, 'latitude' => 2.8197,
                'longitude' => -60.6733, 'population' => 430000, 'postal_code_range' => '69300-000 a 69399-999'
            ],
            [
                'city' => 'Macapá', 'region' => 'Centro', 'state_code' => 'AP',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => 0.0389,
                'longitude' => -51.0664, 'population' => 510000, 'postal_code_range' => '68900-000 a 68999-999'
            ],
            [
                'city' => 'Rio Branco', 'region' => 'Centro', 'state_code' => 'AC',
                'traffic_pattern' => TrafficPattern::LIGHT->value, 'latitude' => -9.9747,
                'longitude' => -67.8100, 'population' => 420000, 'postal_code_range' => '69900-000 a 69999-999'
            ],
            [
                'city' => 'Vitória', 'region' => 'Praia do Canto', 'state_code' => 'ES',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -20.3197,
                'longitude' => -40.3377, 'population' => 370000, 'postal_code_range' => '29000-000 a 29999-999'
            ],
            [
                'city' => 'Florianópolis', 'region' => 'Centro', 'state_code' => 'SC',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -27.5969,
                'longitude' => -48.5495, 'population' => 510000, 'postal_code_range' => '88000-000 a 88999-999'
            ],
            // Outras cidades principais
            [
                'city' => 'Campinas', 'region' => 'Cambuí', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -22.9056,
                'longitude' => -47.0608, 'population' => 1200000, 'postal_code_range' => '13000-000 a 13999-999'
            ],
            [
                'city' => 'Guarulhos', 'region' => 'Centro', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -23.4628,
                'longitude' => -46.5333, 'population' => 1350000, 'postal_code_range' => '07000-000 a 07999-999'
            ],
            [
                'city' => 'São Bernardo do Campo', 'region' => 'Rudge Ramos', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -23.6939,
                'longitude' => -46.5650, 'population' => 820000, 'postal_code_range' => '09000-000 a 09999-999'
            ],
            [
                'city' => 'Santo André', 'region' => 'Jardim', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -23.6667,
                'longitude' => -46.5383, 'population' => 710000, 'postal_code_range' => '09000-000 a 09999-999'
            ],
            [
                'city' => 'Osasco', 'region' => 'Vila Osasco', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -23.5325,
                'longitude' => -46.7917, 'population' => 700000, 'postal_code_range' => '06000-000 a 06999-999'
            ],
            [
                'city' => 'Ribeirão Preto', 'region' => 'Jardim Paulista', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -21.1775,
                'longitude' => -47.8103, 'population' => 720000, 'postal_code_range' => '14000-000 a 14999-999'
            ],
            [
                'city' => 'Sorocaba', 'region' => 'Campolim', 'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -23.5015,
                'longitude' => -47.4526, 'population' => 690000, 'postal_code_range' => '18000-000 a 18999-999'
            ],
            [
                'city' => 'Uberlândia', 'region' => 'Santa Mônica', 'state_code' => 'MG',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -18.9141,
                'longitude' => -48.2749, 'population' => 700000, 'postal_code_range' => '38400-000 a 38499-999'
            ],
            [
                'city' => 'Juiz de Fora', 'region' => 'São Mateus', 'state_code' => 'MG',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -21.7640,
                'longitude' => -43.3503, 'population' => 570000, 'postal_code_range' => '36000-000 a 36999-999'
            ],
            [
                'city' => 'Niterói', 'region' => 'Icaraí', 'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -22.8864,
                'longitude' => -43.1149, 'population' => 520000, 'postal_code_range' => '24000-000 a 24999-999'
            ],
            [
                'city' => 'São Gonçalo', 'region' => 'Alcântara', 'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -22.8269,
                'longitude' => -43.0539, 'population' => 1050000, 'postal_code_range' => '24000-000 a 24999-999'
            ],
            [
                'city' => 'Duque de Caxias', 'region' => 'Centro', 'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::HEAVY->value, 'latitude' => -22.7858,
                'longitude' => -43.3117, 'population' => 920000, 'postal_code_range' => '25000-000 a 25999-999'
            ],
            [
                'city' => 'Londrina', 'region' => 'Gleba Palhano', 'state_code' => 'PR',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -23.3045,
                'longitude' => -51.1696, 'population' => 580000, 'postal_code_range' => '86000-000 a 86999-999'
            ],
            [
                'city' => 'Maringá', 'region' => 'Zona 7', 'state_code' => 'PR',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -23.4253,
                'longitude' => -51.9386, 'population' => 430000, 'postal_code_range' => '87000-000 a 87999-999'
            ],
            [
                'city' => 'Joinville', 'region' => 'América', 'state_code' => 'SC',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -26.3044,
                'longitude' => -48.8458, 'population' => 610000, 'postal_code_range' => '89200-000 a 89999-999'
            ],
            [
                'city' => 'Caxias do Sul', 'region' => 'São Pelegrino', 'state_code' => 'RS',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -29.1662,
                'longitude' => -51.1796, 'population' => 520000, 'postal_code_range' => '95000-000 a 95999-999'
            ],
            [
                'city' => 'Feira de Santana', 'region' => 'Centro', 'state_code' => 'BA',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -12.2551,
                'longitude' => -38.9598, 'population' => 620000, 'postal_code_range' => '44000-000 a 44999-999'
            ],
            [
                'city' => 'Juazeiro do Norte', 'region' => 'Pirajá', 'state_code' => 'CE',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -7.2131,
                'longitude' => -39.3153, 'population' => 280000, 'postal_code_range' => '63000-000 a 63999-999'
            ],
            [
                'city' => 'Caruaru', 'region' => 'Maurício de Nassau', 'state_code' => 'PE',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -8.2833,
                'longitude' => -35.9722, 'population' => 370000, 'postal_code_range' => '55000-000 a 55999-999'
            ],
            [
                'city' => 'Ananindeua', 'region' => 'Cidade Nova', 'state_code' => 'PA',
                'traffic_pattern' => TrafficPattern::MODERATE->value, 'latitude' => -1.3656,
                'longitude' => -48.3746, 'population' => 540000, 'postal_code_range' => '67000-000 a 67999-999'
            ],
            [
                'city' => 'Diamantino', 'region' => 'Centro', 'state_code' => 'MT',
                'traffic_pattern' => TrafficPattern::LIGHT->value, 'latitude' => -14.4086,
                'longitude' => -56.4477, 'population' => 22000, 'postal_code_range' => '78400-000 a 78499-999'
            ],
        ];
    }

    /**
     * Seed para cidades de médio porte no Brasil.
     *
     * @param \Faker\Generator $faker Instância do faker
     * @param array $stateRegions Mapeamento de estados para regiões
     * @return void
     */
    private function seedMediumSizedCities($faker, array $stateRegions): void
    {
        $mediumCities = [
            [
                'city' => 'Ribeirão Preto',
                'state_code' => 'SP',
                'regions' => ['Centro', 'Jardim Paulista', 'Alto da Boa Vista', 'Jardim Botânico'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -21.1775,
                'longitude' => -47.8103,
                'population' => 720116,
                'postal_code_range' => '14000-000 a 14109-999'
            ],
            [
                'city' => 'Uberlândia',
                'state_code' => 'MG',
                'regions' => ['Centro', 'Santa Mônica', 'Tibery', 'Martins'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -18.9141,
                'longitude' => -48.2749,
                'population' => 699097,
                'postal_code_range' => '38400-000 a 38415-999'
            ],
            [
                'city' => 'Sorocaba',
                'state_code' => 'SP',
                'regions' => ['Centro', 'Jardim Simus', 'Vila Hortência', 'Campolim'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -23.5015,
                'longitude' => -47.4526,
                'population' => 687357,
                'postal_code_range' => '18000-000 a 18109-999'
            ],
            [
                'city' => 'Joinville',
                'state_code' => 'SC',
                'regions' => ['Centro', 'América', 'Anita Garibaldi', 'Bucarein'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -26.3044,
                'longitude' => -48.8458,
                'population' => 604708,
                'postal_code_range' => '89200-000 a 89239-999'
            ],
            [
                'city' => 'Londrina',
                'state_code' => 'PR',
                'regions' => ['Centro', 'Gleba Palhano', 'Jardim Quebra Pedras', 'Aeroporto'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -23.3045,
                'longitude' => -51.1696,
                'population' => 576664,
                'postal_code_range' => '86000-000 a 86109-999'
            ],
            [
                'city' => 'Niterói',
                'state_code' => 'RJ',
                'regions' => ['Centro', 'Icaraí', 'São Francisco', 'Charitas'],
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -22.8864,
                'longitude' => -43.1149,
                'population' => 516981,
                'postal_code_range' => '24000-000 a 24399-999'
            ],
            [
                'city' => 'São José do Rio Preto',
                'state_code' => 'SP',
                'regions' => ['Centro', 'Jardim Redentor', 'Boa Vista', 'Vila Imperial'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -20.8113,
                'longitude' => -49.3758,
                'population' => 469173,
                'postal_code_range' => '15000-000 a 15104-999'
            ],
            [
                'city' => 'Caxias do Sul',
                'state_code' => 'RS',
                'regions' => ['Centro', 'São Pelegrino', 'Exposição', 'Panazzolo'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -29.1662,
                'longitude' => -51.1796,
                'population' => 517451,
                'postal_code_range' => '95000-000 a 95124-999'
            ],
            [
                'city' => 'Florianópolis',
                'state_code' => 'SC',
                'regions' => ['Centro', 'Canasvieiras', 'Lagoa da Conceição', 'Campeche'],
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -27.5969,
                'longitude' => -48.5495,
                'population' => 508826,
                'postal_code_range' => '88000-000 a 88099-999'
            ],
            [
                'city' => 'Petrolina',
                'state_code' => 'PE',
                'regions' => ['Centro', 'Jardim Amazonas', 'Vila Mocó', 'Dom Avelar'],
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -9.3896,
                'longitude' => -40.5027,
                'population' => 354317,
                'postal_code_range' => '56300-000 a 56332-999'
            ]
        ];

        $locations = [];
        foreach ($mediumCities as $cityData) {
            foreach ($cityData['regions'] as $region) {
                $locations[] = [
                    'id' => (string) Str::uuid(),
                    'city' => $cityData['city'],
                    'region' => $region,
                    'state_code' => $cityData['state_code'],
                    'traffic_pattern' => $cityData['traffic_pattern'],
                    'latitude' => $cityData['latitude'] + (rand(-10, 10) / 1000),
                    'longitude' => $cityData['longitude'] + (rand(-10, 10) / 1000),
                    'population' => $cityData['population'],
                    'postal_code_range' => $cityData['postal_code_range'],
                    'usage_count' => rand(0, 15),
                    'last_used_at' => rand(0, 5) > 3 ? now()->subDays(rand(1, 60)) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('brazilian_locations')->insert($locations);

        // Cidades médias geradas aleatoriamente
        $regionalCities = [
            'SP' => ['Bauru', 'Franca', 'Presidente Prudente', 'Marília', 'Araçatuba'],
            'MG' => ['Divinópolis', 'Ipatinga', 'Poços de Caldas', 'Teófilo Otoni', 'Varginha'],
            'RS' => ['Pelotas', 'Santa Maria', 'Passo Fundo', 'Rio Grande', 'Uruguaiana'],
            'PR' => ['Cascavel', 'Ponta Grossa', 'Foz do Iguaçu', 'Guarapuava', 'Paranaguá'],
            'SC' => ['Blumenau', 'Chapecó', 'Criciúma', 'Itajaí', 'Lages'],
            'BA' => ['Vitória da Conquista', 'Juazeiro', 'Ilhéus', 'Itabuna', 'Barreiras'],
            'GO' => ['Anápolis', 'Rio Verde', 'Catalão', 'Jataí', 'Luziânia'],
            'PE' => ['Caruaru', 'Garanhuns', 'Vitória de Santo Antão', 'Serra Talhada', 'Arcoverde'],
            'CE' => ['Sobral', 'Juazeiro do Norte', 'Crato', 'Iguatu', 'Itapipoca'],
        ];

        $locations = [];
        foreach ($regionalCities as $stateCode => $cities) {
            foreach ($cities as $city) {
                $regions = [];
                $usedNames = [];
                for ($i = 0; $i < rand(2, 4); $i++) {
                    $region = $this->generateRegionName($faker, $usedNames);
                    $usedNames[] = $region;
                    $regions[] = $region;
                }

                $stateBounds = $this->getStateBounds($stateCode);
                $latitude = $faker->latitude($stateBounds['min_lat'], $stateBounds['max_lat']);
                $longitude = $faker->longitude($stateBounds['min_lon'], $stateBounds['max_lon']);
                $population = rand(100000, 400000);
                $postalCodeRange = $this->getPostalCodeRange($stateCode);

                $trafficPattern = $this->getTrafficPattern($population);

                foreach ($regions as $region) {
                    $locations[] = [
                        'id' => (string) Str::uuid(),
                        'city' => $city,
                        'region' => $region,
                        'state_code' => $stateCode,
                        'traffic_pattern' => $trafficPattern,
                        'latitude' => $latitude + (rand(-10, 10) / 1000),
                        'longitude' => $longitude + (rand(-10, 10) / 1000),
                        'population' => $population,
                        'postal_code_range' => $postalCodeRange,
                        'usage_count' => rand(0, 15),
                        'last_used_at' => rand(0, 5) > 3 ? now()->subDays(rand(1, 60)) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        DB::table('brazilian_locations')->insert($locations);
    }

    /**
     * Seed para cidades pequenas e áreas rurais.
     *
     * @param \Faker\Generator $faker Instância do faker
     * @param array $stateRegions Mapeamento de estados para regiões
     * @return void
     */
    private function seedSmallTowns($faker, array $stateRegions): void
    {
        $smallTowns = [
            ['Parintins', 'AM'], ['Santarém', 'PA'], ['Ariquemes', 'RO'], ['Oiapoque', 'AP'],
            ['Rio Branco', 'AC'], ['Bonfim', 'RR'], ['Gurupi', 'TO'],
            ['Barreirinhas', 'MA'], ['Parnaíba', 'PI'], ['Quixadá', 'CE'], ['Mossoró', 'RN'],
            ['Campina Grande', 'PB'], ['Petrolina', 'PE'], ['Penedo', 'AL'], ['Estância', 'SE'],
            ['Porto Seguro', 'BA'], ['Lençóis', 'BA'], ['Jeremoabo', 'BA'],
            ['Chapada dos Guimarães', 'MT'], ['Bonito', 'MS'], ['Pirenópolis', 'GO'], ['Planaltina', 'DF'],
            ['Diamantina', 'MG'], ['Ouro Preto', 'MG'], ['São João del-Rei', 'MG'], ['Guarapari', 'ES'],
            ['Paraty', 'RJ'], ['Angra dos Reis', 'RJ'], ['Campos do Jordão', 'SP'], ['Brotas', 'SP'],
            ['Antonina', 'PR'], ['Morretes', 'PR'], ['Urubici', 'SC'], ['Penha', 'SC'],
            ['Gramado', 'RS'], ['Canela', 'RS'], ['Bento Gonçalves', 'RS']
        ];

        $locations = [];
        foreach ($smallTowns as [$city, $stateCode]) {
            $regions = [];
            $usedNames = [];
            for ($i = 0; $i < rand(1, 3); $i++) {
                $region = $this->generateRuralRegionName($faker, $usedNames);
                $usedNames[] = $region;
                $regions[] = $region;
            }

            $stateBounds = $this->getStateBounds($stateCode);
            $latitude = $faker->latitude($stateBounds['min_lat'], $stateBounds['max_lat']);
            $longitude = $faker->longitude($stateBounds['min_lon'], $stateBounds['max_lon']);
            $population = rand(5000, 100000);
            $postalCodeRange = $this->getPostalCodeRange($stateCode);
            $trafficPattern = $population > 50000 ? TrafficPattern::MODERATE->value : TrafficPattern::LIGHT->value;

            foreach ($regions as $region) {
                $locations[] = [
                    'id' => (string) Str::uuid(),
                    'city' => $city,
                    'region' => $region,
                    'state_code' => $stateCode,
                    'traffic_pattern' => $trafficPattern,
                    'latitude' => $latitude + (rand(-10, 10) / 1000),
                    'longitude' => $longitude + (rand(-10, 10) / 1000),
                    'population' => $population,
                    'postal_code_range' => $postalCodeRange,
                    'usage_count' => rand(0, 15),
                    'last_used_at' => rand(0, 5) > 3 ? now()->subDays(rand(1, 60)) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Áreas rurais aleatórias
        $ruralStates = ['PA', 'MT', 'GO', 'MG', 'SP', 'PR', 'RS', 'BA', 'CE', 'MA'];
        foreach ($ruralStates as $stateCode) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $city = $faker->city();
                $region = $this->generateRuralRegionName($faker, []);

                $stateBounds = $this->getStateBounds($stateCode);
                $latitude = $faker->latitude($stateBounds['min_lat'], $stateBounds['max_lat']);
                $longitude = $faker->longitude($stateBounds['min_lon'], $stateBounds['max_lon']);
                $population = rand(1000, 30000);
                $postalCodeRange = $this->getPostalCodeRange($stateCode);

                $locations[] = [
                    'id' => (string) Str::uuid(),
                    'city' => $city,
                    'region' => $region,
                    'state_code' => $stateCode,
                    'traffic_pattern' => TrafficPattern::LIGHT->value,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'population' => $population,
                    'postal_code_range' => $postalCodeRange,
                    'usage_count' => rand(0, 15),
                    'last_used_at' => rand(0, 5) > 3 ? now()->subDays(rand(1, 60)) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('brazilian_locations')->insert($locations);
    }

    /**
     * Retorna os limites geográficos de um estado.
     *
     * @param string $stateCode Código do estado (UF)
     * @return array Limites de latitude e longitude
     */
    private function getStateBounds(string $stateCode): array
    {
        $bounds = [
            'AC' => ['min_lat' => -11.0, 'max_lat' => -7.0, 'min_lon' => -74.0, 'max_lon' => -66.5],
            'AL' => ['min_lat' => -10.5, 'max_lat' => -8.5, 'min_lon' => -38.0, 'max_lon' => -35.0],
            'AM' => ['min_lat' => -10.0, 'max_lat' => 2.0, 'min_lon' => -73.0, 'max_lon' => -57.0],
            'AP' => ['min_lat' => -1.0, 'max_lat' => 4.5, 'min_lon' => -55.0, 'max_lon' => -49.5],
            'BA' => ['min_lat' => -18.0, 'max_lat' => -8.5, 'min_lon' => -46.5, 'max_lon' => -37.0],
            'CE' => ['min_lat' => -8.0, 'max_lat' => -2.5, 'min_lon' => -41.5, 'max_lon' => -37.5],
            'DF' => ['min_lat' => -16.5, 'max_lat' => -15.0, 'min_lon' => -48.5, 'max_lon' => -47.0],
            'ES' => ['min_lat' => -21.5, 'max_lat' => -18.0, 'min_lon' => -41.5, 'max_lon' => -39.5],
            'GO' => ['min_lat' => -19.0, 'max_lat' => -12.0, 'min_lon' => -53.0, 'max_lon' => -46.0],
            'MA' => ['min_lat' => -10.0, 'max_lat' => -1.0, 'min_lon' => -48.5, 'max_lon' => -41.5],
            'MG' => ['min_lat' => -23.0, 'max_lat' => -14.0, 'min_lon' => -51.0, 'max_lon' => -39.5],
            'MS' => ['min_lat' => -24.0, 'max_lat' => -17.0, 'min_lon' => -58.0, 'max_lon' => -51.0],
            'MT' => ['min_lat' => -18.0, 'max_lat' => -7.5, 'min_lon' => -61.0, 'max_lon' => -50.0],
            'PA' => ['min_lat' => -9.0, 'max_lat' => 2.0, 'min_lon' => -58.0, 'max_lon' => -46.0],
            'PB' => ['min_lat' => -8.5, 'max_lat' => -6.0, 'min_lon' => -38.5, 'max_lon' => -34.5],
            'PE' => ['min_lat' => -9.5, 'max_lat' => -7.0, 'min_lon' => -41.0, 'max_lon' => -34.5],
            'PI' => ['min_lat' => -10.0, 'max_lat' => -2.5, 'min_lon' => -45.0, 'max_lon' => -40.5],
            'PR' => ['min_lat' => -26.5, 'max_lat' => -22.0, 'min_lon' => -54.5, 'max_lon' => -48.0],
            'RJ' => ['min_lat' => -23.5, 'max_lat' => -20.5, 'min_lon' => -45.0, 'max_lon' => -41.0],
            'RN' => ['min_lat' => -6.5, 'max_lat' => -4.5, 'min_lon' => -38.5, 'max_lon' => -35.0],
            'RO' => ['min_lat' => -13.5, 'max_lat' => -7.5, 'min_lon' => -66.0, 'max_lon' => -60.0],
            'RR' => ['min_lat' => -1.0, 'max_lat' => 5.0, 'min_lon' => -64.0, 'max_lon' => -58.5],
            'RS' => ['min_lat' => -33.5, 'max_lat' => -27.0, 'min_lon' => -57.0, 'max_lon' => -49.5],
            'SC' => ['min_lat' => -29.5, 'max_lat' => -25.5, 'min_lon' => -54.0, 'max_lon' => -48.0],
            'SE' => ['min_lat' => -11.5, 'max_lat' => -9.5, 'min_lon' => -38.0, 'max_lon' => -36.5],
            'SP' => ['min_lat' => -25.0, 'max_lat' => -20.0, 'min_lon' => -53.0, 'max_lon' => -44.0],
            'TO' => ['min_lat' => -13.0, 'max_lat' => -5.0, 'min_lon' => -49.5, 'max_lon' => -45.5],
        ];

        return $bounds[$stateCode] ?? [
            'min_lat' => -33.7, 'max_lat' => -2.8, 'min_lon' => -73.9, 'max_lon' => -34.7
        ];
    }

    /**
     * Retorna a faixa de CEP para um estado.
     *
     * @param string $stateCode Código do estado (UF)
     * @return string Faixa de CEP
     */
    private function getPostalCodeRange(string $stateCode): string
    {
        $cepRanges = [
            'AC' => '69900-000 a 69999-999', 'AL' => '57000-000 a 57999-999',
            'AM' => '69000-000 a 69999-999', 'AP' => '68900-000 a 68999-999',
            'BA' => '40000-000 a 48999-999', 'CE' => '60000-000 a 63999-999',
            'DF' => '70000-000 a 73699-999', 'ES' => '29000-000 a 29999-999',
            'GO' => '74000-000 a 76999-999', 'MA' => '65000-000 a 65999-999',
            'MG' => '30000-000 a 39999-999', 'MS' => '79000-000 a 79999-999',
            'MT' => '78000-000 a 78999-999', 'PA' => '66000-000 a 68899-999',
            'PB' => '58000-000 a 58999-999', 'PE' => '50000-000 a 56999-999',
            'PI' => '64000-000 a 64999-999', 'PR' => '80000-000 a 87999-999',
            'RJ' => '20000-000 a 28999-999', 'RN' => '59000-000 a 59999-999',
            'RO' => '76800-000 a 76999-999', 'RR' => '69300-000 a 69399-999',
            'RS' => '90000-000 a 99999-999', 'SC' => '88000-000 a 89999-999',
            'SE' => '49000-000 a 49999-999', 'SP' => '01000-000 a 19999-999',
            'TO' => '77000-000 a 77999-999',
        ];

        return $cepRanges[$stateCode] ?? '00000-000 a 99999-999';
    }

    /**
     * Determina o padrão de tráfego com base na população.
     *
     * @param int $population População da cidade
     * @return string Padrão de tráfego
     */
    private function getTrafficPattern(int $population): string
    {
        if ($population > 1000000) {
            $patterns = [
                TrafficPattern::CONGESTED->value => 40,
                TrafficPattern::HEAVY->value => 30,
                TrafficPattern::MODERATE->value => 20,
                TrafficPattern::LIGHT->value => 10,
            ];
        } elseif ($population > 500000) {
            $patterns = [
                TrafficPattern::HEAVY->value => 40,
                TrafficPattern::MODERATE->value => 50,
                TrafficPattern::LIGHT->value => 10,
            ];
        } elseif ($population > 100000) {
            $patterns = [
                TrafficPattern::MODERATE->value => 60,
                TrafficPattern::LIGHT->value => 40,
            ];
        } else {
            $patterns = [
                TrafficPattern::LIGHT->value => 80,
                TrafficPattern::MODERATE->value => 20,
            ];
        }

        $total = array_sum($patterns);
        $rand = rand(1, $total);
        $current = 0;
        foreach ($patterns as $pattern => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $pattern;
            }
        }
        return TrafficPattern::LIGHT->value;
    }

    /**
     * Gera um nome de região/bairro urbano realista.
     *
     * @param \Faker\Generator $faker Instância do faker
     * @param array $usedNames Nomes já usados na mesma cidade
     * @return string Nome da região/bairro
     */
    private function generateRegionName($faker, array $usedNames): string
    {
        $prefixes = [
            'Jardim', 'Vila', 'Parque', 'Conjunto', 'Residencial', 'Bairro',
            'Setor', 'Núcleo', 'Alto', 'Baixo', 'Recanto', 'Portal', 'Loteamento',
        ];
        $suffixes = [
            'das Flores', 'dos Pássaros', 'das Palmeiras', 'dos Ipês', 'das Acácias',
            'Verde', 'Azul', 'Imperial', 'Real', 'Central', 'Novo', 'Velho',
            'Alegre', 'Feliz', 'Esperança', 'da Paz', 'Bela Vista', 'Boa Vista',
            'do Sol', 'da Lua', 'das Estrelas',
        ];
        $commonNames = [
            'Centro', 'Aeroporto', 'Alvorada', 'São José', 'São Francisco', 'Santo Antônio',
            'Santa Maria', 'Santa Luzia', 'Monte Alegre', 'Nossa Senhora', 'Industrial',
            'Comercial', 'Universitário', 'Independência', 'Liberdade', 'Eldorado',
            'Parque das Nações', 'Zona Norte', 'Zona Sul', 'Zona Leste', 'Zona Oeste',
        ];
        $streetTypes = ['Avenida', 'Rua', 'Alameda', 'Largo', 'Praça'];
        $streetNames = [
            'Brasil', 'Santos Dumont', 'Rio Branco', 'Getúlio Vargas', 'Juscelino Kubitschek',
            'São Paulo', 'XV de Novembro', 'Sete de Setembro', 'Dom Pedro II', 'Tiradentes',
            'das Flores', 'dos Bandeirantes', 'Anhanguera', 'Castelo Branco',
        ];

        do {
            $nameType = rand(1, 10);
            if ($nameType <= 4) {
                $name = $commonNames[array_rand($commonNames)];
            } elseif ($nameType <= 8) {
                $name = $prefixes[array_rand($prefixes)] . ' ' . $suffixes[array_rand($suffixes)];
            } else {
                $name = $streetTypes[array_rand($streetTypes)] . ' ' . $streetNames[array_rand($streetNames)];
            }
        } while (in_array($name, $usedNames));

        return $name;
    }

    /**
     * Gera um nome de região rural ou pequena cidade realista.
     *
     * @param \Faker\Generator $faker Instância do faker
     * @param array $usedNames Nomes já usados na mesma cidade
     * @return string Nome da região rural
     */
    private function generateRuralRegionName($faker, array $usedNames): string
    {
        $ruralPrefixes = [
            'Fazenda', 'Sítio', 'Chácara', 'Estrada', 'Comunidade', 'Assentamento',
            'Distrito', 'Povoado', 'Campo', 'Vale', 'Serra', 'Colônia', 'Rincão',
        ];
        $ruralSuffixes = [
            'Verde', 'Feliz', 'do Sol', 'da Lua', 'das Águas', 'dos Campos',
            'do Horizonte', 'dos Buritis', 'dos Ipês', 'das Araras', 'dos Pinheiros',
            'da Esperança', 'do Futuro', 'da Paz', 'São José', 'São João', 'Santa Maria',
            'Grande', 'Pequeno', 'Novo', 'Velho', 'Dourado', 'do Céu', 'das Pedras',
        ];
        $commonRuralNames = [
            'Zona Rural', 'Área Rural', 'Região das Águas', 'Vale do Rio', 'Quilômetro 42',
            'Beira Rio', 'Beira Estrada', 'Linha Norte', 'Linha Sul', 'Interior', 'BR-101',
            'Entroncamento', 'Ponte Alta', 'Cabeceira', 'Três Barras', 'Água Clara',
            'Poço Fundo', 'Bom Retiro', 'Boa Esperança', 'Nova Canaã', 'Terra Nova',
        ];

        do {
            $nameType = rand(1, 10);
            if ($nameType <= 4) {
                $name = $commonRuralNames[array_rand($commonRuralNames)];
            } else {
                $name = $ruralPrefixes[array_rand($ruralPrefixes)] . ' ' . $ruralSuffixes[array_rand($ruralSuffixes)];
            }
        } while (in_array($name, $usedNames));

        return $name;
    }
}