<?php

declare(strict_types=1);

namespace Database\Seeders;

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
class BrazilianLocation2Seeder extends Seeder
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
            // Norte
            'AM' => 'Norte',
            'PA' => 'Norte',
            'RO' => 'Norte',
            'AP' => 'Norte',
            'AC' => 'Norte',
            'RR' => 'Norte',
            'TO' => 'Norte',
            // Nordeste
            'MA' => 'Nordeste',
            'PI' => 'Nordeste',
            'CE' => 'Nordeste',
            'RN' => 'Nordeste',
            'PB' => 'Nordeste',
            'PE' => 'Nordeste',
            'AL' => 'Nordeste',
            'SE' => 'Nordeste',
            'BA' => 'Nordeste',
            // Centro-Oeste
            'MT' => 'Centro-Oeste',
            'MS' => 'Centro-Oeste',
            'GO' => 'Centro-Oeste',
            'DF' => 'Centro-Oeste',
            // Sudeste
            'MG' => 'Sudeste',
            'ES' => 'Sudeste',
            'RJ' => 'Sudeste',
            'SP' => 'Sudeste',
            // Sul
            'PR' => 'Sul',
            'SC' => 'Sul',
            'RS' => 'Sul',
        ];

        // Dados específicos para capitais e principais cidades
        $majorCities = $this->getMajorCities();
        
        // Adiciona as principais cidades ao banco
        foreach ($majorCities as $cityData) {
            $this->insertLocation(
                $cityData['city'],
                $cityData['region'],
                $cityData['state_code'],
                $cityData['traffic_pattern'],
                $cityData['latitude'],
                $cityData['longitude'],
                $cityData['population'],
                $cityData['postal_code_range']
            );
        }

        // Adiciona cidades de médio porte com dados realistas
        $this->seedMediumSizedCities($faker, $stateRegions);
        
        // Adiciona cidades pequenas e áreas rurais
        $this->seedSmallTowns($faker, $stateRegions);
    }

    /**
     * Insere uma localização no banco de dados.
     * 
     * @param string $city Nome da cidade
     * @param string $region Região ou bairro
     * @param string $stateCode Código do estado (UF)
     * @param string $trafficPattern Padrão de tráfego
     * @param float $latitude Latitude da localização
     * @param float $longitude Longitude da localização
     * @param int $population População estimada
     * @param string $postalCodeRange Faixa de CEP
     * @return void
     */
    private function insertLocation(
        string $city,
        string $region,
        string $stateCode,
        string $trafficPattern,
        float $latitude,
        float $longitude,
        int $population,
        string $postalCodeRange
    ): void {
        DB::table('brazilian_locations')->insert([
            'id' => (string) Str::uuid(),
            'city' => $city,
            'region' => $region,
            'state_code' => $stateCode,
            'traffic_pattern' => $trafficPattern,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'population' => $population,
            'postal_code_range' => $postalCodeRange,
            'usage_count' => rand(0, 15),
            'last_used_at' => rand(0, 5) > 3 ? now()->subDays(rand(1, 60)) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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

        foreach ($mediumCities as $cityData) {
            foreach ($cityData['regions'] as $region) {
                $this->insertLocation(
                    $cityData['city'],
                    $region,
                    $cityData['state_code'],
                    $cityData['traffic_pattern'],
                    $cityData['latitude'] + (rand(-10, 10) / 1000),
                    $cityData['longitude'] + (rand(-10, 10) / 1000),
                    $cityData['population'],
                    $cityData['postal_code_range']
                );
            }
        }

        // Adiciona algumas cidades médias geradas aleatoriamente
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

        foreach ($regionalCities as $stateCode => $cities) {
            foreach ($cities as $city) {
                $regions = [];
                for ($i = 0; $i < rand(2, 4); $i++) {
                    $regions[] = $this->generateRegionName($faker);
                }

                $latitude = $faker->latitude(-33.7683, -5.2719);
                $longitude = $faker->longitude(-73.9872, -34.7936);
                $population = rand(100000, 400000);
                $postalCodePrefix = substr('00000' . rand(10000, 99999), -5);
                $postalCodeRange = $postalCodePrefix . '-000 a ' . $postalCodePrefix . '-999';

                $trafficPatternValues = [
                    TrafficPattern::LIGHT->value,
                    TrafficPattern::MODERATE->value,
                    TrafficPattern::MODERATE->value,  // Duplicado para aumentar probabilidade
                    TrafficPattern::HEAVY->value
                ];
                $trafficPattern = $trafficPatternValues[array_rand($trafficPatternValues)];

                foreach ($regions as $region) {
                    $this->insertLocation(
                        $city,
                        $region,
                        $stateCode,
                        $trafficPattern,
                        $latitude + (rand(-10, 10) / 1000),
                        $longitude + (rand(-10, 10) / 1000),
                        $population,
                        $postalCodeRange
                    );
                }
            }
        }
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
            // Norte
            ['Parintins', 'AM'], ['Santarém', 'PA'], ['Ariquemes', 'RO'], ['Oiapoque', 'AP'],
            ['Rio Branco', 'AC'], ['Bonfim', 'RR'], ['Gurupi', 'TO'],
            
            // Nordeste
            ['Barreirinhas', 'MA'], ['Parnaíba', 'PI'], ['Quixadá', 'CE'], ['Mossoró', 'RN'],
            ['Campina Grande', 'PB'], ['Petrolina', 'PE'], ['Penedo', 'AL'], ['Estância', 'SE'],
            ['Porto Seguro', 'BA'], ['Lençóis', 'BA'], ['Jeremoabo', 'BA'],
            
            // Centro-Oeste
            ['Chapada dos Guimarães', 'MT'], ['Bonito', 'MS'], ['Pirenópolis', 'GO'], ['Planaltina', 'DF'],
            
            // Sudeste
            ['Diamantina', 'MG'], ['Ouro Preto', 'MG'], ['São João del-Rei', 'MG'], ['Guarapari', 'ES'],
            ['Paraty', 'RJ'], ['Angra dos Reis', 'RJ'], ['Campos do Jordão', 'SP'], ['Brotas', 'SP'],
            
            // Sul
            ['Antonina', 'PR'], ['Morretes', 'PR'], ['Urubici', 'SC'], ['Penha', 'SC'],
            ['Gramado', 'RS'], ['Canela', 'RS'], ['Bento Gonçalves', 'RS']
        ];
        
        foreach ($smallTowns as [$city, $stateCode]) {
            $regions = [];
            for ($i = 0; $i < rand(1, 3); $i++) {
                $regions[] = $this->generateRuralRegionName($faker);
            }
            
            $latitude = $faker->latitude(-33.7683, -5.2719);
            $longitude = $faker->longitude(-73.9872, -34.7936);
            $population = rand(5000, 100000);
            $postalCodePrefix = substr('00000' . rand(10000, 99999), -5);
            $postalCodeRange = $postalCodePrefix . '-000 a ' . $postalCodePrefix . '-999';
            
            $trafficPatternValues = [
                TrafficPattern::LIGHT->value,
                TrafficPattern::LIGHT->value,  // Duplicado para aumentar probabilidade
                TrafficPattern::MODERATE->value
            ];
            $trafficPattern = $trafficPatternValues[array_rand($trafficPatternValues)];
            
            foreach ($regions as $region) {
                $this->insertLocation(
                    $city,
                    $region,
                    $stateCode,
                    $trafficPattern,
                    $latitude + (rand(-10, 10) / 1000),
                    $longitude + (rand(-10, 10) / 1000),
                    $population,
                    $postalCodeRange
                );
            }
        }
        
        // Adiciona algumas áreas rurais e pequenas cidades geradas aleatoriamente
        $ruralStates = ['PA', 'MT', 'GO', 'MG', 'SP', 'PR', 'RS', 'BA', 'CE', 'MA'];
        
        foreach ($ruralStates as $stateCode) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $city = $faker->city();
                $region = $this->generateRuralRegionName($faker);
                
                $latitude = $faker->latitude(-33.7683, -5.2719);
                $longitude = $faker->longitude(-73.9872, -34.7936);
                $population = rand(1000, 30000);
                $postalCodePrefix = substr('00000' . rand(10000, 99999), -5);
                $postalCodeRange = $postalCodePrefix . '-000 a ' . $postalCodePrefix . '-999';
                
                $this->insertLocation(
                    $city,
                    $region,
                    $stateCode,
                    TrafficPattern::LIGHT->value,
                    $latitude,
                    $longitude,
                    $population,
                    $postalCodeRange
                );
            }
        }
    }

    /**
     * Retorna dados específicos para as principais cidades brasileiras.
     * 
     * @return array Lista de dados específicos das principais cidades
     */
    private function getMajorCities(): array
    {
        return [
            // São Paulo - diversos bairros com diferentes padrões de tráfego
            [
                'city' => 'São Paulo',
                'region' => 'Avenida Paulista',
                'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::CONGESTED->value,
                'latitude' => -23.5558,
                'longitude' => -46.6396,
                'population' => 12396372,
                'postal_code_range' => '01000-000 a 05999-999'
            ],
            [
                'city' => 'São Paulo', 
                'region' => 'Vila Madalena',
                'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -23.5469,
                'longitude' => -46.6889,
                'population' => 12396372,
                'postal_code_range' => '05400-000 a 05499-999'
            ],
            [
                'city' => 'São Paulo',
                'region' => 'Liberdade',
                'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -23.5583,
                'longitude' => -46.6378,
                'population' => 12396372,
                'postal_code_range' => '01500-000 a 01599-999'
            ],
            [
                'city' => 'São Paulo',
                'region' => 'Moema',
                'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -23.5975,
                'longitude' => -46.6682,
                'population' => 12396372,
                'postal_code_range' => '04000-000 a 04099-999'
            ],
            [
                'city' => 'São Paulo', 
                'region' => 'Marginal Tietê',
                'state_code' => 'SP',
                'traffic_pattern' => TrafficPattern::CONGESTED->value,
                'latitude' => -23.5185,
                'longitude' => -46.6498,
                'population' => 12396372,
                'postal_code_range' => '02000-000 a 02999-999'
            ],
            
            // Rio de Janeiro - diversos bairros com diferentes padrões de tráfego
            [
                'city' => 'Rio de Janeiro',
                'region' => 'Copacabana',
                'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -22.9714,
                'longitude' => -43.1823,
                'population' => 6775561,
                'postal_code_range' => '22000-000 a 22100-999'
            ],
            [
                'city' => 'Rio de Janeiro',
                'region' => 'Ipanema',
                'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -22.9848,
                'longitude' => -43.2008,
                'population' => 6775561,
                'postal_code_range' => '22400-000 a 22499-999'
            ],
            [
                'city' => 'Rio de Janeiro',
                'region' => 'Linha Vermelha',
                'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::CONGESTED->value,
                'latitude' => -22.8661,
                'longitude' => -43.2391,
                'population' => 6775561,
                'postal_code_range' => '21000-000 a 21999-999'
            ],
            [
                'city' => 'Rio de Janeiro',
                'region' => 'Barra da Tijuca',
                'state_code' => 'RJ',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -23.0003,
                'longitude' => -43.3657,
                'population' => 6775561,
                'postal_code_range' => '22600-000 a 22799-999'
            ],
            
            // Outras capitais e grandes cidades
            [
                'city' => 'Brasília',
                'region' => 'Plano Piloto',
                'state_code' => 'DF',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -15.7801,
                'longitude' => -47.9292,
                'population' => 3094325,
                'postal_code_range' => '70000-000 a 70999-999'
            ],
            [
                'city' => 'Brasília',
                'region' => 'Eixo Monumental',
                'state_code' => 'DF',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -15.7938,
                'longitude' => -47.8828,
                'population' => 3094325,
                'postal_code_range' => '70000-000 a 70999-999'
            ],
            [
                'city' => 'Salvador',
                'region' => 'Barra',
                'state_code' => 'BA',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -13.0096,
                'longitude' => -38.5262,
                'population' => 2953986,
                'postal_code_range' => '40000-000 a 42499-999'
            ],
            [
                'city' => 'Salvador',
                'region' => 'Pelourinho',
                'state_code' => 'BA',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -12.9733,
                'longitude' => -38.5081,
                'population' => 2953986,
                'postal_code_range' => '40000-000 a 42499-999'
            ],
            [
                'city' => 'Fortaleza',
                'region' => 'Praia de Iracema',
                'state_code' => 'CE',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -3.7227,
                'longitude' => -38.5159,
                'population' => 2703391,
                'postal_code_range' => '60000-000 a 61600-999'
            ],
            [
                'city' => 'Fortaleza',
                'region' => 'Avenida Beira Mar',
                'state_code' => 'CE',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -3.7319,
                'longitude' => -38.5089,
                'population' => 2703391,
                'postal_code_range' => '60000-000 a 61600-999'
            ],
            [
                'city' => 'Belo Horizonte',
                'region' => 'Savassi',
                'state_code' => 'MG',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -19.9433,
                'longitude' => -43.9346,
                'population' => 2523794,
                'postal_code_range' => '30000-000 a 31999-999'
            ],
            [
                'city' => 'Belo Horizonte',
                'region' => 'Avenida do Contorno',
                'state_code' => 'MG',
                'traffic_pattern' => TrafficPattern::CONGESTED->value,
                'latitude' => -19.9208,
                'longitude' => -43.9314,
                'population' => 2523794,
                'postal_code_range' => '30000-000 a 31999-999'
            ],
            [
                'city' => 'Belo Horizonte',
                'region' => 'Pampulha',
                'state_code' => 'MG',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -19.8553,
                'longitude' => -43.9775,
                'population' => 2523794,
                'postal_code_range' => '31000-000 a 31999-999'
            ],
            [
                'city' => 'Manaus',
                'region' => 'Centro',
                'state_code' => 'AM',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -3.1142,
                'longitude' => -60.0211,
                'population' => 2255903,
                'postal_code_range' => '69000-000 a 69099-999'
            ],
            [
                'city' => 'Manaus',
                'region' => 'Ponta Negra',
                'state_code' => 'AM',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -3.0856,
                'longitude' => -60.0944,
                'population' => 2255903,
                'postal_code_range' => '69000-000 a 69099-999'
            ],
            [
                'city' => 'Curitiba',
                'region' => 'Batel',
                'state_code' => 'PR',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -25.4431,
                'longitude' => -49.2879,
                'population' => 1963726,
                'postal_code_range' => '80000-000 a 82999-999'
            ],
            [
                'city' => 'Curitiba',
                'region' => 'Linha Verde',
                'state_code' => 'PR',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -25.4463,
                'longitude' => -49.2629,
                'population' => 1963726,
                'postal_code_range' => '80000-000 a 82999-999'
            ],
            [
                'city' => 'Recife',
                'region' => 'Boa Viagem',
                'state_code' => 'PE',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -8.1196,
                'longitude' => -34.9017,
                'population' => 1653461,
                'postal_code_range' => '50000-000 a 52999-999'
            ],
            [
                'city' => 'Recife',
                'region' => 'Marco Zero',
                'state_code' => 'PE',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -8.0631,
                'longitude' => -34.8711,
                'population' => 1653461,
                'postal_code_range' => '50000-000 a 52999-999'
            ],
            [
                'city' => 'Porto Alegre',
                'region' => 'Avenida Ipiranga',
                'state_code' => 'RS',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -30.0437,
                'longitude' => -51.1978,
                'population' => 1483771,
                'postal_code_range' => '90000-000 a 91999-999'
            ],
            [
                'city' => 'Belém',
                'region' => 'Ver-o-Peso',
                'state_code' => 'PA',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -1.4542,
                'longitude' => -48.5021,
                'population' => 1499641,
                'postal_code_range' => '66000-000 a 67999-999'
            ],
            [
                'city' => 'Goiânia',
                'region' => 'Setor Bueno',
                'state_code' => 'GO',
                'traffic_pattern' => TrafficPattern::MODERATE->value,
                'latitude' => -16.7087,
                'longitude' => -49.2649,
                'population' => 1536097,
                'postal_code_range' => '74000-000 a 74899-999'
            ],
            [
                'city' => 'Goiânia',
                'region' => 'Setor Marista',
                'state_code' => 'GO',
                'traffic_pattern' => TrafficPattern::HEAVY->value,
                'latitude' => -16.7199,
                'longitude' => -49.2741,
                'population' => 1536097,
                'postal_code_range' => '74000-000 a 74899-999'
            ],
            
            // Diamantino - MT (cidade do usuário)
            [
                'city' => 'Diamantino',
                'region' => 'Centro',
                'state_code' => 'MT',
                'traffic_pattern' => TrafficPattern::LIGHT->value,
                'latitude' => -14.4086,
                'longitude' => -56.4477,
                'population' => 22041,
                'postal_code_range' => '78400-000 a 78409-999'
            ],
            [
                'city' => 'Diamantino',
                'region' => 'Novo Diamantino',
                'state_code' => 'MT',
                'traffic_pattern' => TrafficPattern::LIGHT->value,
                'latitude' => -14.4097,
                'longitude' => -56.4366,
                'population' => 22041,
                'postal_code_range' => '78400-000 a 78409-999'
            ]
        ];
    }

    /**
     * Gera um nome de região/bairro urbano realista.
     * 
     * @param \Faker\Generator $faker Instância do faker
     * @return string Nome da região/bairro
     */
    private function generateRegionName($faker): string
    {
        $prefixes = [
            'Jardim', 'Vila', 'Parque', 'Conjunto', 'Residencial', 'Bairro', 
            'Setor', 'Núcleo', 'Alto', 'Baixo', 'Recanto', 'Portal'
        ];
        
        $suffixes = [
            'das Flores', 'dos Pássaros', 'das Palmeiras', 'dos Ipês', 'das Acácias',
            'Verde', 'Azul', 'Imperial', 'Real', 'Central', 'Novo', 'Nova', 'Velho', 'Velha',
            'Alegre', 'Feliz', 'Esperança', 'da Paz', 'Bela Vista', 'Boa Vista'
        ];
        
        $commonNames = [
            'Centro', 'Aeroporto', 'Alvorada', 'São José', 'São Francisco', 'Santo Antônio',
            'Santa Maria', 'Santa Luzia', 'Monte Alegre', 'Nossa Senhora', 'Industrial', 'Comercial',
            'Universitário', 'Independência', 'Liberdade', 'Eldorado', 'Parque das Nações',
            'Avenida Principal', 'Rua do Comércio', 'Zona Norte', 'Zona Sul', 'Zona Leste', 'Zona Oeste'
        ];
        
        $nameType = rand(1, 10);
        
        if ($nameType <= 4) {
            // Nome comum
            return $commonNames[array_rand($commonNames)];
        } elseif ($nameType <= 8) {
            // Prefixo + sufixo
            return $prefixes[array_rand($prefixes)] . ' ' . $suffixes[array_rand($suffixes)];
        } else {
            // Avenida ou Rua específica
            $streetTypes = ['Avenida', 'Rua', 'Alameda', 'Largo', 'Praça'];
            $streetNames = [
                'Brasil', 'Santos Dumont', 'Rio Branco', 'Getúlio Vargas', 
                'Juscelino Kubitschek', 'São Paulo', 'XV de Novembro', 'Sete de Setembro',
                'Dom Pedro II', 'Tiradentes', 'das Flores', 'dos Bandeirantes'
            ];
            
            return $streetTypes[array_rand($streetTypes)] . ' ' . $streetNames[array_rand($streetNames)];
        }
    }
    
    /**
     * Gera um nome de região rural ou pequena cidade realista.
     * 
     * @param \Faker\Generator $faker Instância do faker
     * @return string Nome da região rural
     */
    private function generateRuralRegionName($faker): string
    {
        $ruralPrefixes = [
            'Fazenda', 'Sítio', 'Chácara', 'Estrada', 'Comunidade', 'Assentamento', 
            'Distrito', 'Povoado', 'Campo', 'Vale', 'Serra', 'Colônia'
        ];
        
        $ruralSuffixes = [
            'Verde', 'Feliz', 'do Sol', 'da Lua', 'das Águas', 'dos Campos',
            'do Horizonte', 'dos Buritis', 'dos Ipês', 'das Araras', 'dos Pinheiros',
            'da Esperança', 'do Futuro', 'da Paz', 'São José', 'São João', 'Santa Maria',
            'Grande', 'Pequeno', 'Novo', 'Velho', 'Dourado', 'do Céu'
        ];
        
        $commonRuralNames = [
            'Zona Rural', 'Área Rural', 'Região das Águas', 'Vale do Rio', 'Quilômetro 42',
            'Beira Rio', 'Beira Estrada', 'Linha Norte', 'Linha Sul', 'Interior', 'BR-101',
            'Entroncamento', 'Ponte Alta', 'Cabeceira', 'Três Barras', 'Água Clara',
            'Poço Fundo', 'Bom Retiro', 'Boa Esperança', 'Nova Canaã', 'Terra Nova'
        ];
        
        $nameType = rand(1, 10);
        
        if ($nameType <= 4) {
            // Nome comum
            return $commonRuralNames[array_rand($commonRuralNames)];
        } else {
            // Prefixo + sufixo
            return $ruralPrefixes[array_rand($ruralPrefixes)] . ' ' . $ruralSuffixes[array_rand($ruralSuffixes)];
        }
    }
}