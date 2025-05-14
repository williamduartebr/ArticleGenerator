<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;

class HumanPersonaSeeder extends Seeder
{
    /**
     * Popula a tabela human_personas com dados fictícios realistas.
     *
     * @return void
     */
    public function run(): void
    {
        // Cria uma instância do Faker em português do Brasil
        $faker = FakerFactory::create('pt_BR');

        // Nomes e sobrenomes brasileiros comuns
        $firstNames = $this->getFirstNames();
        $lastNames = $this->getLastNames();

        // Lista de profissões variadas
        $professions = $this->getProfessions();

        // Cidades brasileiras para as localizações
        $brazilianCities = $this->getBrazilianCities();

        // Marcas e modelos de veículos populares no Brasil
        $vehicles = $this->getPopularVehiclesInBrazil();

        // Níveis educacionais
        $educationLevels = [
            'Ensino Fundamental',
            'Ensino Médio',
            'Ensino Técnico',
            'Graduação',
            'Pós-graduação',
            'Mestrado',
            'Doutorado',
        ];

        // Gerar 300 personas únicas e diversificadas
        for ($i = 0; $i < 300; $i++) {
            // Determina o gênero para o nome
            $gender = $faker->randomElement(['male', 'female', 'other']);
            
            // Escolhe um nome apropriado com base no gênero
            $firstName = $faker->randomElement($gender === 'female' ? $firstNames['female'] : $firstNames['male']);
            $lastName = $faker->randomElement($lastNames);
            
            // Escolhe uma profissão aleatória
            $profession = $faker->randomElement($professions);
            
            // Escolhe uma cidade aleatória
            $location = $faker->randomElement($brazilianCities);
            
            // Define veículos preferidos (entre 1 e 3)
            $preferredVehiclesCount = $faker->numberBetween(1, 3);
            $preferredVehicles = [];
            
            for ($j = 0; $j < $preferredVehiclesCount; $j++) {
                $vehicle = $faker->randomElement($vehicles);
                $preferredVehicles[] = $vehicle['brand'] . ' ' . $vehicle['model'];
            }
            
            // Remove duplicatas
            $preferredVehicles = array_unique($preferredVehicles);
            
            // Ajusta a profissão para ser mais compatível com a localização
            $profession = $this->adjustProfessionForLocation($profession, $location, $faker);
            
            // Cria biografia realista baseada na profissão e localização
            $bio = $this->generateBio($firstName, $profession, $location, $preferredVehicles, $faker);
            
            // Insere na tabela
            DB::table('human_personas')->insert([
                'id' => (string) Str::uuid(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'profession' => $profession,
                'location' => $location,
                'preferred_vehicles' => json_encode($preferredVehicles),
                'usage_count' => $faker->numberBetween(0, 20),
                'last_used_at' => $faker->optional(0.7)->dateTimeBetween('-6 months', 'now'),
                'age' => $faker->numberBetween(22, 65),
                'gender' => $gender,
                'education_level' => $faker->randomElement($educationLevels),
                'bio' => $bio,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Retorna uma lista de nomes brasileiros comuns por gênero.
     *
     * @return array
     */
    private function getFirstNames(): array
    {
        return [
            'male' => [
                'João', 'Pedro', 'Lucas', 'Matheus', 'Gabriel', 'Miguel', 'Guilherme', 'Rafael', 
                'Felipe', 'Bruno', 'Carlos', 'Eduardo', 'Gustavo', 'Paulo', 'André', 'Fábio', 
                'Marcelo', 'Ricardo', 'Luiz', 'Fernando', 'Thiago', 'Rodrigo', 'Henrique', 'Daniel', 
                'Marcos', 'Vinícius', 'José', 'Alexandre', 'Roberto', 'Antônio', 'Caio', 'Diego',
                'Leonardo', 'Hugo', 'Samuel', 'Enzo', 'Raul', 'Otávio', 'Renan', 'Bernardo', 'Davi',
                'Igor', 'Leandro', 'Arthur', 'Murilo', 'Júlio', 'Luciano', 'Rogério', 'Cristiano',
                'Yuri', 'Lorenzo', 'Flávio', 'Kaique', 'Isaac', 'Cauã', 'Erick', 'Augusto', 'Jonas',
                'Danilo', 'Adriano', 'Alex', 'Renato', 'Wagner', 'Marco', 'Victor', 'Alan', 'Joaquim'
            ],
            'female' => [
                'Maria', 'Ana', 'Julia', 'Camila', 'Fernanda', 'Amanda', 'Juliana', 'Letícia', 
                'Beatriz', 'Gabriela', 'Larissa', 'Isabella', 'Mariana', 'Caroline', 'Vitória', 
                'Aline', 'Bianca', 'Jéssica', 'Tatiana', 'Patrícia', 'Natália', 'Bruna', 'Paula', 
                'Daniela', 'Luiza', 'Sofia', 'Luciana', 'Mônica', 'Roberta', 'Clara', 'Marina',
                'Rafaela', 'Adriana', 'Cristina', 'Vanessa', 'Marta', 'Carla', 'Giovanna', 'Melissa',
                'Elisa', 'Eduarda', 'Helena', 'Lara', 'Alessandra', 'Renata', 'Valéria', 'Débora',
                'Laura', 'Lívia', 'Lúcia', 'Cecília', 'Heloísa', 'Manuela', 'Érica', 'Raquel',
                'Simone', 'Viviane', 'Tânia', 'Andreia', 'Sara', 'Sueli', 'Alice', 'Rita', 'Rosa'
            ]
        ];
    }

    /**
     * Retorna uma lista de sobrenomes brasileiros comuns.
     *
     * @return array
     */
    private function getLastNames(): array
    {
        return [
            'Silva', 'Santos', 'Oliveira', 'Souza', 'Pereira', 'Costa', 'Rodrigues', 'Almeida', 
            'Nascimento', 'Lima', 'Araújo', 'Fernandes', 'Carvalho', 'Gomes', 'Martins', 'Rocha', 
            'Ribeiro', 'Alves', 'Monteiro', 'Mendes', 'Barros', 'Freitas', 'Barbosa', 'Pinto', 
            'Moura', 'Cavalcanti', 'Dias', 'Castro', 'Campos', 'Cardoso', 'Correia', 'Ferreira',
            'Azevedo', 'Vieira', 'Moreira', 'Nunes', 'Teixeira', 'Andrade', 'Machado', 'Peixoto',
            'Marques', 'Borges', 'Gonçalves', 'Lopes', 'Duarte', 'Ramos', 'Farias', 'Guimarães',
            'Moraes', 'Schneider', 'Mello', 'Schmidt', 'Garcia', 'Toledo', 'Miranda', 'Cruz',
            'Reis', 'Vasconcelos', 'Wagner', 'Siqueira', 'Fogaça', 'Novaes', 'Melo', 'Brito'
        ];
    }

    /**
     * Retorna uma lista de profissões variadas e realistas.
     *
     * @return array
     */
    private function getProfessions(): array
    {
        return [
            // Técnicas/Engenharia
            'Engenheiro Mecânico', 'Engenheiro Civil', 'Engenheiro Elétrico', 'Engenheiro de Software', 
            'Técnico em Eletrônica', 'Arquiteto', 'Técnico em Manutenção', 'Mecânico Automotivo',
            'Engenheiro de Produção', 'Técnico em Informática', 'Eletricista', 'Programador',
            'Desenvolvedor de Sistemas', 'Analista de Sistemas', 'DevOps', 'Analista de Dados',

            // Saúde
            'Médico', 'Enfermeiro', 'Fisioterapeuta', 'Dentista', 'Psicólogo', 'Farmacêutico',
            'Nutricionista', 'Fonoaudiólogo', 'Técnico em Enfermagem', 'Veterinário',
            'Biomédico', 'Terapeuta Ocupacional',
            
            // Negócios/Administração
            'Administrador', 'Contador', 'Economista', 'Gerente de Vendas', 'Analista Financeiro',
            'Consultor de Negócios', 'Empreendedor', 'Gerente de Marketing', 'Gerente de RH',
            'Corretor de Imóveis', 'Analista de Marketing', 'Empresário', 'Gerente Comercial',
            
            // Educação
            'Professor', 'Pedagogo', 'Diretor Escolar', 'Coordenador Pedagógico', 
            'Professor Universitário', 'Educador Físico', 'Instrutor de Autoescola',
            
            // Serviços
            'Advogado', 'Designer Gráfico', 'Jornalista', 'Fotógrafo', 'Cozinheiro',
            'Policial', 'Bombeiro', 'Motorista', 'Piloto', 'Vendedor', 'Cabeleireiro', 
            'Personal Trainer', 'Atendente Comercial', 'Motorista de Aplicativo',
            'Corretor de Seguros', 'Representante Comercial', 'Chef de Cozinha',
            
            // Comércio/Indústria
            'Comerciante', 'Microempreendedor', 'Gerente de Loja', 'Supervisor de Produção',
            'Operador de Máquinas', 'Técnico em Segurança do Trabalho', 'Logística',
            
            // Outros
            'Artesão', 'Músico', 'Ator', 'Produtor de Eventos', 'Social Media Manager',
            'Influenciador Digital', 'Youtuber', 'Técnico em Telecomunicações',
            'Técnico em Refrigeração', 'Barbeiro', 'Agricultor', 'Pescador'
        ];
    }

    /**
     * Retorna uma lista de cidades brasileiras populares.
     *
     * @return array
     */
    private function getBrazilianCities(): array
    {
        return [
            // São Paulo
            'São Paulo', 'Campinas', 'Santos', 'Ribeirão Preto', 'Guarulhos', 'Osasco', 
            'São José dos Campos', 'Sorocaba', 'Santo André', 'São Bernardo do Campo',
            
            // Rio de Janeiro
            'Rio de Janeiro', 'Niterói', 'Duque de Caxias', 'Nova Iguaçu', 'Petrópolis',
            'Volta Redonda', 'Campos dos Goytacazes', 'Macaé',
            
            // Minas Gerais
            'Belo Horizonte', 'Uberlândia', 'Juiz de Fora', 'Contagem', 'Montes Claros',
            'Poços de Caldas', 'Ipatinga', 'Divinópolis', 'Governador Valadares',
            
            // Paraná
            'Curitiba', 'Londrina', 'Maringá', 'Ponta Grossa', 'Cascavel', 'Foz do Iguaçu',
            
            // Santa Catarina
            'Florianópolis', 'Joinville', 'Blumenau', 'Chapecó', 'Criciúma', 'Itajaí',
            'Balneário Camboriú',
            
            // Rio Grande do Sul
            'Porto Alegre', 'Caxias do Sul', 'Pelotas', 'Canoas', 'Santa Maria', 'Gramado',
            'Passo Fundo', 'Novo Hamburgo',
            
            // Bahia
            'Salvador', 'Feira de Santana', 'Vitória da Conquista', 'Itabuna', 'Juazeiro',
            'Porto Seguro',
            
            // Pernambuco
            'Recife', 'Olinda', 'Caruaru', 'Petrolina', 'Jaboatão dos Guararapes',
            
            // Ceará
            'Fortaleza', 'Juazeiro do Norte', 'Sobral', 'Crato',
            
            // Pará
            'Belém', 'Santarém', 'Marabá', 'Parauapebas',
            
            // Amazonas
            'Manaus', 'Parintins',
            
            // Distrito Federal, Goiás e Centro-Oeste
            'Brasília', 'Goiânia', 'Anápolis', 'Caldas Novas', 'Cuiabá', 'Campo Grande',
            'Corumbá', 'Dourados',
            
            // Outras capitais e cidades importantes
            'Vitória', 'Vila Velha', 'São Luís', 'Teresina', 'Natal', 'João Pessoa',
            'Maceió', 'Aracaju', 'Palmas', 'Porto Velho', 'Rio Branco', 'Boa Vista', 'Macapá',
            'Diamantino', 'Rondonópolis', 'Imperatriz', 'Mossoró', 'Camaçari', 'Ilhéus'
        ];
    }

    /**
     * Retorna uma lista de veículos populares no Brasil.
     *
     * @return array
     */
    private function getPopularVehiclesInBrazil(): array
    {
        return [
            // Chevrolet
            ['brand' => 'Chevrolet', 'model' => 'Onix'],
            ['brand' => 'Chevrolet', 'model' => 'Prisma'],
            ['brand' => 'Chevrolet', 'model' => 'Cruze'],
            ['brand' => 'Chevrolet', 'model' => 'S10'],
            ['brand' => 'Chevrolet', 'model' => 'Tracker'],
            
            // Volkswagen
            ['brand' => 'Volkswagen', 'model' => 'Gol'],
            ['brand' => 'Volkswagen', 'model' => 'Polo'],
            ['brand' => 'Volkswagen', 'model' => 'T-Cross'],
            ['brand' => 'Volkswagen', 'model' => 'Saveiro'],
            ['brand' => 'Volkswagen', 'model' => 'Amarok'],
            ['brand' => 'Volkswagen', 'model' => 'Nivus'],
            
            // Fiat
            ['brand' => 'Fiat', 'model' => 'Strada'],
            ['brand' => 'Fiat', 'model' => 'Argo'],
            ['brand' => 'Fiat', 'model' => 'Mobi'],
            ['brand' => 'Fiat', 'model' => 'Toro'],
            ['brand' => 'Fiat', 'model' => 'Pulse'],
            ['brand' => 'Fiat', 'model' => 'Fastback'],
            
            // Hyundai
            ['brand' => 'Hyundai', 'model' => 'HB20'],
            ['brand' => 'Hyundai', 'model' => 'Creta'],
            ['brand' => 'Hyundai', 'model' => 'Tucson'],
            
            // Toyota
            ['brand' => 'Toyota', 'model' => 'Corolla'],
            ['brand' => 'Toyota', 'model' => 'Hilux'],
            ['brand' => 'Toyota', 'model' => 'Yaris'],
            ['brand' => 'Toyota', 'model' => 'SW4'],
            ['brand' => 'Toyota', 'model' => 'Corolla Cross'],
            
            // Renault
            ['brand' => 'Renault', 'model' => 'Kwid'],
            ['brand' => 'Renault', 'model' => 'Sandero'],
            ['brand' => 'Renault', 'model' => 'Duster'],
            ['brand' => 'Renault', 'model' => 'Logan'],
            
            // Jeep
            ['brand' => 'Jeep', 'model' => 'Renegade'],
            ['brand' => 'Jeep', 'model' => 'Compass'],
            ['brand' => 'Jeep', 'model' => 'Commander'],
            
            // Honda
            ['brand' => 'Honda', 'model' => 'Civic'],
            ['brand' => 'Honda', 'model' => 'HR-V'],
            ['brand' => 'Honda', 'model' => 'City'],
            ['brand' => 'Honda', 'model' => 'Fit'],
            ['brand' => 'Honda', 'model' => 'WR-V'],
            
            // Nissan
            ['brand' => 'Nissan', 'model' => 'Kicks'],
            ['brand' => 'Nissan', 'model' => 'Versa'],
            ['brand' => 'Nissan', 'model' => 'Frontier'],
            
            // Ford
            ['brand' => 'Ford', 'model' => 'Ranger'],
            ['brand' => 'Ford', 'model' => 'Territory'],
            ['brand' => 'Ford', 'model' => 'Bronco Sport'],
            
            // Outros
            ['brand' => 'Mitsubishi', 'model' => 'L200'],
            ['brand' => 'Mitsubishi', 'model' => 'Pajero'],
            ['brand' => 'Citroën', 'model' => 'C4 Cactus'],
            ['brand' => 'Peugeot', 'model' => '208'],
            ['brand' => 'Kia', 'model' => 'Sportage'],
            ['brand' => 'BMW', 'model' => '320i'],
            ['brand' => 'Mercedes-Benz', 'model' => 'GLA'],
            ['brand' => 'Audi', 'model' => 'Q3']
        ];
    }

    /**
     * Ajusta a profissão para ser mais compatível com a localização.
     *
     * @param string $profession A profissão original
     * @param string $location A localização
     * @param \Faker\Generator $faker Instância do Faker
     * @return string A profissão ajustada
     */
    private function adjustProfessionForLocation(string $profession, string $location, $faker): string
    {
        // Lista de grandes cidades
        $bigCities = [
            'São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Brasília', 'Curitiba',
            'Fortaleza', 'Salvador', 'Recife', 'Porto Alegre', 'Manaus', 'Goiânia',
            'Belém', 'Guarulhos', 'Campinas', 'São Luís', 'Maceió', 'Natal', 'Teresina',
            'São Bernardo do Campo', 'João Pessoa'
        ];
        
        // Profissões mais comuns em grandes cidades
        $urbanProfessions = [
            'Advogado', 'Engenheiro de Software', 'Médico', 'Analista de Sistemas',
            'Consultor de Negócios', 'Arquiteto', 'Desenvolvedor', 'Designer Gráfico',
            'Economista', 'Jornalista', 'Professor Universitário', 'Analista Financeiro',
            'Gerente de Marketing', 'Psicólogo', 'Dentista', 'Empresário'
        ];
        
        // Profissões mais comuns em cidades menores
        $ruralProfessions = [
            'Comerciante', 'Professor', 'Agricultor', 'Pescador', 'Técnico em Manutenção',
            'Mecânico Automotivo', 'Vendedor', 'Funcionário Público', 'Eletricista',
            'Cabeleireiro', 'Motorista', 'Artesão', 'Técnico em Enfermagem'
        ];
        
        // Verifique se é uma cidade grande
        $isBigCity = in_array($location, $bigCities);
        
        // Se a profissão não for compatível com a localização, ajuste
        if ($isBigCity && in_array($profession, $ruralProfessions) && $faker->boolean(70)) {
            return $faker->randomElement($urbanProfessions);
        } elseif (!$isBigCity && in_array($profession, $urbanProfessions) && $faker->boolean(70)) {
            return $faker->randomElement($ruralProfessions);
        }
        
        return $profession;
    }

    /**
     * Gera uma biografia realista baseada nos atributos da persona.
     *
     * @param string $firstName Nome da persona
     * @param string $profession Profissão da persona
     * @param string $location Localização da persona
     * @param array $preferredVehicles Veículos preferidos
     * @param \Faker\Generator $faker Instância do Faker
     * @return string A biografia gerada
     */
    private function generateBio(string $firstName, string $profession, string $location, array $preferredVehicles, $faker): string
    {
        $yearsOfExperience = $faker->numberBetween(1, 25);
        
        $bioTemplates = [
            "{$firstName} é {$profession} há {$yearsOfExperience} anos, morando em {$location}. " .
            "No tempo livre, gosta de passar tempo com a família e fazer passeios de " . 
            (count($preferredVehicles) > 0 ? $preferredVehicles[0] : "carro") . ".",
            
            "Atuando como {$profession} em {$location}, {$firstName} tem compartilhado suas experiências " .
            "com diversos veículos, especialmente " . 
            (count($preferredVehicles) > 0 ? $preferredVehicles[0] : "seu carro atual") . ".",
            
            "{$firstName} divide seu tempo entre a carreira como {$profession} e sua paixão por automóveis. " .
            "Residente em {$location}, já teve diversos veículos e atualmente " .
            (count($preferredVehicles) > 0 ? "possui um " . $preferredVehicles[0] : "está em busca de um novo veículo") . ".",
            
            "Depois de {$yearsOfExperience} anos trabalhando como {$profession}, {$firstName} conhece bem " .
            "os desafios diários de se locomover em {$location}. " .
            (count($preferredVehicles) > 0 ? "Seu " . $preferredVehicles[0] . " tem sido seu companheiro fiel." : "Já teve várias experiências com diferentes modelos de carros."),
            
            "Combinando sua experiência como {$profession} com a paixão por automóveis, {$firstName} " .
            "traz uma perspectiva única sobre a rotina em {$location}. " .
            (count($preferredVehicles) > 0 ? "Seu veículo preferido é o " . $preferredVehicles[0] . "." : "Está sempre atento às novidades do mercado automobilístico."),
        ];
        
        return $faker->randomElement($bioTemplates);
    }
}