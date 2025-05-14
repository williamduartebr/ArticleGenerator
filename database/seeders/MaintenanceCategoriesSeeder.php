<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;
use Src\AutoInfoCenter\Domain\Eloquent\MaintenanceCategory;

class MaintenanceCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [

            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Calibragem de Pneus',
                'slug' => 'calibragem-pneus',
                'description' => 'Informações específicas sobre pressão correta dos pneus, procedimentos de calibragem e impacto no desempenho.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'icon_bg_color' => 'bg-blue-100',
                'icon_text_color' => 'text-blue-600',
                'display_order' => 1,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Calibragem de Pneus - Guia Completo | Mercado Veículos',
                    'description' => 'Guia completo sobre calibragem de pneus para todos os modelos de veículos. Informações precisas para manutenção e segurança.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Por que a Calibragem Correta é Importante?',
                    'sections' => [
                        [
                            'title' => 'Segurança',
                            'content' => 'Pneus com pressão inadequada podem comprometer a estabilidade do veículo, aumentar a distância de frenagem e reduzir a aderência em piso molhado, aumentando significativamente o risco de acidentes.'
                        ],
                        [
                            'title' => 'Economia',
                            'content' => 'A calibragem correta pode reduzir o consumo de combustível em até 3%. Pneus com pressão baixa aumentam a resistência ao rolamento, fazendo o motor trabalhar mais e consumir mais combustível.'
                        ],
                        [
                            'title' => 'Durabilidade',
                            'content' => 'Pneus corretamente calibrados desgastam-se de forma uniforme e podem durar até 20% mais. A pressão incorreta causa desgaste irregular, encurtando a vida útil do pneu, que é um componente de custo significativo.'
                        ],
                        [
                            'title' => 'Valores de Referência',
                            'content' => 'A pressão correta varia de acordo com o modelo do veículo, tamanho do pneu e carga transportada. Consulte a tabela no manual do proprietário ou na coluna da porta do motorista. Para viagens com carga completa, geralmente recomenda-se um acréscimo de 2-4 PSI na pressão.'
                        ]
                    ],
                    'alert' => 'Verifique a pressão dos pneus pelo menos uma vez por mês e sempre antes de viagens longas. A calibragem deve ser feita com os pneus frios, preferencialmente pela manhã. A pressão aumenta naturalmente com o aquecimento durante o uso, portanto não esvazie os pneus quando estiverem quentes.'
                ])
            ],
            
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Óleo Recomendado',
                'slug' => 'oleo-recomendado',
                'description' => 'Especificações e recomendações de óleos para diferentes motores e modelos.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />',
                'icon_bg_color' => 'bg-yellow-100',
                'icon_text_color' => 'text-yellow-600',
                'display_order' => 2,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Óleo Recomendado - Escolha o Melhor para seu Veículo | Mercado Veículos',
                    'description' => 'Guia completo de óleos recomendados para todos os modelos de veículos. Informações técnicas para manutenção adequada do motor.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Por que o Óleo Correto é Essencial?',
                    'sections' => [
                        [
                            'title' => 'Lubrificação',
                            'content' => 'O óleo adequado garante a correta lubrificação das partes móveis do motor, reduzindo o atrito e o desgaste prematuro dos componentes.'
                        ],
                        [
                            'title' => 'Resfriamento',
                            'content' => 'O óleo ajuda a dissipar o calor do motor, mantendo a temperatura de operação ideal e evitando superaquecimento das peças internas.'
                        ],
                        [
                            'title' => 'Limpeza',
                            'content' => 'Os óleos modernos possuem aditivos detergentes que mantêm o motor limpo, prevenindo o acúmulo de borra e contaminantes que podem causar entupimentos.'
                        ],
                        [
                            'title' => 'Eficiência',
                            'content' => 'O óleo correto contribui para a economia de combustível e redução de emissões, além de proporcionar melhor desempenho e vida útil prolongada ao motor.'
                        ]
                    ],
                    'alert' => 'Sempre verifique no manual do proprietário a especificação exata do óleo recomendado para seu veículo. O intervalo de troca varia de acordo com o tipo de óleo e condições de uso.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Freios',
                'slug' => 'freios',
                'description' => 'Manutenção e especificações de sistemas de freios, pastilhas, discos e fluidos de freio.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                'icon_bg_color' => 'bg-red-100',
                'icon_text_color' => 'text-red-600',
                'display_order' => 3,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Manutenção de Freios - Guia Completo | Mercado Veículos',
                    'description' => 'Tudo sobre manutenção de freios, pastilhas, discos e fluido. Informações essenciais para a segurança do seu veículo.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Por que a Manutenção dos Freios é Vital?',
                    'sections' => [
                        [
                            'title' => 'Segurança',
                            'content' => 'Os freios são o principal sistema de segurança do veículo. Pastilhas, discos e fluidos em boas condições garantem frenagens eficientes e podem evitar acidentes.'
                        ],
                        [
                            'title' => 'Vida Útil',
                            'content' => 'A manutenção preventiva dos componentes de freio evita danos aos discos e outras peças mais caras, reduzindo custos de manutenção a longo prazo.'
                        ],
                        [
                            'title' => 'Desempenho',
                            'content' => 'Freios em bom estado garantem distâncias de frenagem menores e melhor resposta do pedal, especialmente em situações de emergência ou piso molhado.'
                        ],
                        [
                            'title' => 'Conforto',
                            'content' => 'Um sistema de freios bem mantido elimina ruídos, vibrações e trepidações no pedal, proporcionando uma experiência de condução mais agradável.'
                        ]
                    ],
                    'alert' => 'Verifique regularmente o nível e a cor do fluido de freio. Caso perceba ruídos, vibrações ou aumento na distância de frenagem, procure um profissional imediatamente.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Filtros',
                'slug' => 'filtros',
                'description' => 'Informações sobre os diferentes filtros do veículo e suas funções na manutenção.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />',
                'icon_bg_color' => 'bg-green-100',
                'icon_text_color' => 'text-green-600',
                'display_order' => 4,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Filtros Automotivos - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre todos os tipos de filtros automotivos: ar, óleo, combustível e cabine. Aprenda sobre sua importância e manutenção.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'A Importância da Troca Regular dos Filtros',
                    'sections' => [
                        [
                            'title' => 'Filtro de Ar',
                            'content' => 'O filtro de ar limpo garante melhor desempenho do motor e economia de combustível. Um filtro obstruído pode reduzir a potência e aumentar o consumo em até 10%.'
                        ],
                        [
                            'title' => 'Filtro de Óleo',
                            'content' => 'O filtro de óleo retém partículas e contaminantes que podem danificar o motor. Sua troca regular é essencial para manter a lubrificação eficiente e prolongar a vida útil do motor.'
                        ],
                        [
                            'title' => 'Filtro de Combustível',
                            'content' => 'Este filtro protege o sistema de injeção contra impurezas presentes no combustível. Um filtro obstruído pode causar falhas na aceleração e problemas de partida.'
                        ],
                        [
                            'title' => 'Filtro de Cabine',
                            'content' => 'Responsável pela qualidade do ar dentro do veículo, o filtro de cabine retém poeira, pólen e poluentes. Sua troca regular é importante para a saúde dos ocupantes e bom funcionamento do ar-condicionado.'
                        ]
                    ],
                    'alert' => 'Consulte o manual do proprietário para verificar os intervalos recomendados de troca dos filtros. Em condições severas de uso (poeira, poluição, tráfego intenso), os intervalos devem ser reduzidos.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Suspensão e Amortecedores',
                'slug' => 'suspensao-amortecedores',
                'description' => 'Informações sobre sistemas de suspensão, amortecedores, molas e componentes para diferentes veículos.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />',
                'icon_bg_color' => 'bg-purple-100',
                'icon_text_color' => 'text-purple-600',
                'display_order' => 5,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Suspensão e Amortecedores - Guia Completo | Mercado Veículos',
                    'description' => 'Tudo sobre suspensão automotiva, amortecedores, molas e componentes essenciais para conforto e segurança do seu veículo.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Sistema de Suspensão e Amortecedores',
                    'sections' => [
                        [
                            'title' => 'Função Principal',
                            'content' => 'Os amortecedores controlam as oscilações da carroceria, mantendo os pneus em contato constante com o solo. A suspensão absorve impactos, proporciona conforto e garante estabilidade em curvas e frenagens.'
                        ],
                        [
                            'title' => 'Sinais de Problemas',
                            'content' => 'Quando a suspensão está desgastada, o veículo tende a "pular" em pequenas irregularidades, balancar excessivamente em curvas e apresentar instabilidade. Ruídos, batidas secas e tendência a derrapar são sinais de alerta.'
                        ],
                        [
                            'title' => 'Tipos de Suspensão',
                            'content' => 'Existem diversos tipos como MacPherson, duplo A, multilink e eixo rígido. Cada sistema possui características específicas que influenciam o comportamento dinâmico do veículo.'
                        ],
                        [
                            'title' => 'Manutenção Preventiva',
                            'content' => 'A verificação regular das buchas, bandejas, pivôs e terminais da suspensão é essencial para identificar desgastes antes que comprometam a segurança. Os amortecedores devem ser substituídos em pares no mesmo eixo.'
                        ]
                    ],
                    'alert' => 'Amortecedores desgastados podem aumentar a distância de frenagem em até 20%, especialmente em piso molhado. Recomenda-se verificar a suspensão a cada 20.000 km. A vida útil média dos amortecedores é de 40.000 a 80.000 km, dependendo das condições de uso.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sistema de Arrefecimento',
                'slug' => 'sistema-arrefecimento',
                'description' => 'Guia sobre sistemas de arrefecimento, radiador, líquido de arrefecimento e prevenção de superaquecimento.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                'icon_bg_color' => 'bg-orange-100',
                'icon_text_color' => 'text-orange-600',
                'display_order' => 6,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Sistema de Arrefecimento - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre sistema de arrefecimento, radiador e líquido de arrefecimento. Evite superaquecimento e problemas graves no motor.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Componentes do Sistema de Arrefecimento',
                    'sections' => [
                        [
                            'title' => 'Radiador',
                            'content' => 'Responsável pela troca de calor entre o líquido de arrefecimento e o ar ambiente. Deve ser mantido limpo externamente para garantir a eficiência térmica e inspecionado regularmente quanto a vazamentos.'
                        ],
                        [
                            'title' => 'Bomba d\'Água',
                            'content' => 'Circula o líquido de arrefecimento pelo motor e radiador. Sua falha pode causar superaquecimento rápido e danos graves. Recomenda-se a substituição preventiva junto com a troca da correia dentada.'
                        ],
                        [
                            'title' => 'Líquido de Arrefecimento',
                            'content' => 'O líquido deve ser trocado conforme especificação do fabricante, geralmente entre 24 e 60 meses. Um fluido escurecido ou com partículas indica contaminação e necessidade de troca.'
                        ],
                        [
                            'title' => 'Ventoinha',
                            'content' => 'Força a passagem de ar pelo radiador quando o fluxo natural não é suficiente, como em baixas velocidades ou trânsito parado. Sua falha é uma das principais causas de superaquecimento em tráfego urbano.'
                        ]
                    ],
                    'alert' => 'NUNCA abra a tampa do radiador com o motor quente! A pressão interna pode causar graves queimaduras. O superaquecimento é uma das causas mais comuns de danos graves ao motor. Ao notar aumento anormal da temperatura, pare imediatamente em local seguro e desligue o motor.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Bateria',
                'slug' => 'bateria',
                'description' => 'Informações sobre cuidados, manutenção e troca de baterias automotivas.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                'icon_bg_color' => 'bg-indigo-100',
                'icon_text_color' => 'text-indigo-600',
                'display_order' => 7,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Bateria Automotiva - Guia Completo | Mercado Veículos',
                    'description' => 'Tudo sobre baterias automotivas: tipos, manutenção, cuidados e quando fazer a troca. Evite problemas elétricos no seu veículo.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Cuidados Essenciais com a Bateria',
                    'sections' => [
                        [
                            'title' => 'Vida Útil',
                            'content' => 'A vida útil média de uma bateria automotiva é de 2 a 5 anos, dependendo das condições de uso, clima e qualidade da bateria. Em regiões de clima muito quente, a durabilidade pode ser reduzida.'
                        ],
                        [
                            'title' => 'Sinais de Desgaste',
                            'content' => 'Dificuldade na partida, luzes que enfraquecem ao dar partida, necessidade frequente de auxílio para partida são sinais de que a bateria está chegando ao fim de sua vida útil.'
                        ],
                        [
                            'title' => 'Cuidados Preventivos',
                            'content' => 'Mantenha os terminais limpos e bem conectados. Verifique regularmente se há sinais de corrosão e limpe com uma solução de bicarbonato de sódio se necessário.'
                        ],
                        [
                            'title' => 'Escolha da Bateria',
                            'content' => 'Ao substituir, escolha uma bateria com especificações (amperagem e grupo) idênticas às recomendadas pelo fabricante do veículo. Uma bateria inadequada pode danificar o sistema elétrico.'
                        ]
                    ],
                    'alert' => 'Evite deixar equipamentos ligados com o motor desligado por longos períodos. Em caso de não utilização do veículo por mais de 15 dias, considere um carregador de manutenção ou desconecte o terminal negativo.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Correias e Correntes',
                'slug' => 'correias-correntes',
                'description' => 'Manutenção de correias dentadas, correntes de distribuição e suas funções no motor.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />',
                'icon_bg_color' => 'bg-pink-100',
                'icon_text_color' => 'text-pink-600',
                'display_order' => 8,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Correias e Correntes - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre correias dentadas, correntes de distribuição e seus componentes. Manutenção preventiva para evitar problemas graves.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'A Importância da Manutenção Preventiva',
                    'sections' => [
                        [
                            'title' => 'Função Crítica',
                            'content' => 'A correia/corrente de distribuição sincroniza o movimento entre o virabrequim e o comando de válvulas. Um rompimento pode causar a colisão entre pistões e válvulas, resultando em danos catastróficos ao motor.'
                        ],
                        [
                            'title' => 'Intervalo de Troca',
                            'content' => 'Para correias dentadas, o intervalo varia entre 40.000 e 120.000 km, dependendo do modelo. Correntes geralmente têm vida útil maior, mas também requerem verificação periódica de tensão e desgaste.'
                        ],
                        [
                            'title' => 'Componentes Associados',
                            'content' => 'Durante a substituição da correia/corrente, é recomendável trocar também tensores, polias e bomba d\'água, pois estes componentes têm vida útil similar e sua falha pode comprometer toda a operação.'
                        ],
                        [
                            'title' => 'Sinais de Problemas',
                            'content' => 'Ruídos metálicos na frente do motor, vibração anormal, dificuldade de partida ou perda de potência podem indicar problemas com a correia ou corrente de distribuição.'
                        ]
                    ],
                    'alert' => 'Nunca negligencie a troca preventiva da correia/corrente de distribuição! O custo da substituição preventiva é muito menor que o reparo de um motor danificado por rompimento, que pode chegar a 70% do valor do veículo.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sistema Elétrico',
                'slug' => 'sistema-eletrico',
                'description' => 'Informações sobre o sistema elétrico do veículo, fusíveis, alternador e componentes eletrônicos.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                'icon_bg_color' => 'bg-yellow-100',
                'icon_text_color' => 'text-yellow-600',
                'display_order' => 9,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Sistema Elétrico Automotivo - Guia Completo | Mercado Veículos',
                    'description' => 'Tudo sobre sistema elétrico automotivo, componentes, manutenção e diagnóstico de problemas. Informações essenciais para manter seu veículo funcionando.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Componentes Principais do Sistema Elétrico',
                    'sections' => [
                        [
                            'title' => 'Alternador',
                            'content' => 'Responsável por gerar energia durante o funcionamento do motor e recarregar a bateria. Problemas no alternador geralmente se manifestam como luzes que piscam, bateria que descarrega rapidamente ou luz da bateria acesa no painel.'
                        ],
                        [
                            'title' => 'Motor de Partida',
                            'content' => 'Inicia o processo de combustão do motor. Sinais de problemas incluem ruídos metálicos durante a partida, dificuldade para dar partida mesmo com bateria carregada ou falta de resposta ao girar a chave.'
                        ],
                        [
                            'title' => 'Fusíveis e Relés',
                            'content' => 'Protegem os circuitos elétricos contra sobrecargas. Se algum equipamento elétrico parar de funcionar, verifique primeiro os fusíveis correspondentes antes de qualquer diagnóstico mais complexo.'
                        ],
                        [
                            'title' => 'Sensores e Módulos',
                            'content' => 'Os veículos modernos possuem dezenas de sensores que monitoram diversas funções. Falhas nestes componentes geralmente acionam a luz de "check engine" e requerem diagnóstico eletrônico especializado.'
                        ]
                    ],
                    'alert' => 'Sempre desconecte o terminal negativo da bateria antes de realizar qualquer intervenção no sistema elétrico. Nunca substitua fusíveis por outros de amperagem maior que a especificada, pois isso pode causar incêndios.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Transmissão',
                'slug' => 'transmissao',
                'description' => 'Manutenção de sistemas de transmissão, câmbios manuais e automáticos, e fluidos de transmissão.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />',
                'icon_bg_color' => 'bg-blue-100',
                'icon_text_color' => 'text-blue-600',
                'display_order' => 10,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Transmissão Automotiva - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre transmissões manuais e automáticas, manutenção preventiva e fluidos. Guia essencial para cuidar do câmbio do seu veículo.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Manutenção da Transmissão',
                    'sections' => [
                        [
                            'title' => 'Transmissão Manual',
                            'content' => 'Requer troca periódica do óleo lubrificante, geralmente a cada 30.000 a 60.000 km. Sintomas de problemas incluem dificuldade para engrenar marchas, rangidos durante as trocas ou vazamentos.'
                        ],
                        [
                            'title' => 'Transmissão Automática',
                            'content' => 'O fluido ATF deve ser trocado conforme especificação do fabricante, geralmente entre 40.000 e 100.000 km. A cor do fluido é um indicador importante: deve ser vermelho claro e translúcido, não escuro ou com cheiro queimado.'
                        ],
                        [
                            'title' => 'Transmissão CVT',
                            'content' => 'As transmissões continuamente variáveis exigem fluidos específicos. O uso de fluido incorreto pode causar danos irreparáveis. A manutenção preventiva é essencial para prolongar sua vida útil.'
                        ],
                        [
                            'title' => 'Embreagem',
                            'content' => 'Para transmissões manuais, o conjunto de embreagem (platô, disco e rolamento) tem vida útil média de 60.000 a 150.000 km, dependendo do estilo de condução. Patinagem, vibração ou ruídos ao acionar o pedal são sinais de desgaste.'
                        ]
                    ],
                    'alert' => 'A transmissão é um dos componentes mais caros para reparo. Manutenção preventiva regular, troca de fluidos no intervalo correto e estilo de condução adequado são essenciais para evitar falhas prematuras.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Ar-Condicionado',
                'slug' => 'ar-condicionado',
                'description' => 'Informações sobre manutenção do sistema de ar-condicionado, carga de gás e limpeza do sistema.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
                'icon_bg_color' => 'bg-cyan-100',
                'icon_text_color' => 'text-cyan-600',
                'display_order' => 11,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Ar-Condicionado Automotivo - Guia Completo | Mercado Veículos',
                    'description' => 'Tudo sobre manutenção do ar-condicionado automotivo, recarga de gás, higienização e diagnóstico de problemas comuns.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Cuidados com o Sistema de Ar-Condicionado',
                    'sections' => [
                        [
                            'title' => 'Higienização',
                            'content' => 'A limpeza do sistema de ar-condicionado deve ser feita a cada 6 meses para evitar o acúmulo de fungos e bactérias que causam mau cheiro e podem afetar a saúde dos ocupantes.'
                        ],
                        [
                            'title' => 'Filtro de Cabine',
                            'content' => 'O filtro de cabine (ou filtro de pólen) deve ser trocado conforme recomendação do fabricante, geralmente a cada 15.000 km ou anualmente. Um filtro obstruído reduz a eficiência do sistema e a qualidade do ar.'
                        ],
                        [
                            'title' => 'Carga de Gás',
                            'content' => 'A recarga de gás refrigerante geralmente é necessária a cada 2 anos. Uma redução na eficiência de resfriamento é o principal sintoma de baixa carga de gás no sistema.'
                        ],
                        [
                            'title' => 'Condensador',
                            'content' => 'Localizado na frente do radiador, o condensador deve ser mantido limpo e desobstruído para garantir a troca de calor eficiente. Insetos e sujeira acumulados reduzem significativamente a eficiência do sistema.'
                        ]
                    ],
                    'alert' => 'Utilize o ar-condicionado pelo menos uma vez por semana, mesmo no inverno, por cerca de 10 minutos. Isso garante a lubrificação adequada do compressor e ajuda a prevenir vazamentos nos anéis de vedação.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Direção',
                'slug' => 'direcao',
                'description' => 'Informações sobre sistemas de direção, fluido de direção hidráulica e manutenção preventiva.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />',
                'icon_bg_color' => 'bg-teal-100',
                'icon_text_color' => 'text-teal-600',
                'display_order' => 12,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Sistema de Direção Automotiva - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre tipos de direção automotiva, manutenção do sistema hidráulico e elétrico, e solução de problemas comuns.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Tipos de Sistemas de Direção',
                    'sections' => [
                        [
                            'title' => 'Direção Hidráulica',
                            'content' => 'Utiliza pressão hidráulica para reduzir o esforço ao volante. Requer verificação periódica do nível e condição do fluido hidráulico, além da inspeção de vazamentos nas mangueiras e bomba.'
                        ],
                        [
                            'title' => 'Direção Eletro-hidráulica',
                            'content' => 'Combina assistência hidráulica com controle eletrônico, reduzindo o consumo de combustível. A manutenção é similar à direção hidráulica, mas com atenção adicional aos componentes eletrônicos.'
                        ],
                        [
                            'title' => 'Direção Elétrica',
                            'content' => 'Utiliza um motor elétrico para assistir a direção, eliminando a necessidade de fluido hidráulico. Mais eficiente em termos de consumo, requer menos manutenção, mas os reparos tendem a ser mais caros.'
                        ],
                        [
                            'title' => 'Sinais de Problemas',
                            'content' => 'Ruídos ao girar o volante, volante pesado ou com folga excessiva, vibração ou retorno lento após as curvas são sinais de que o sistema de direção requer atenção imediata.'
                        ]
                    ],
                    'alert' => 'Problemas no sistema de direção comprometem diretamente a segurança. Ao perceber qualquer anormalidade no comportamento da direção, procure um especialista imediatamente. Nunca dirija se o volante estiver excessivamente pesado ou com folga anormal.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sistema de Ignição',
                'slug' => 'sistema-ignicao',
                'description' => 'Informações sobre componentes do sistema de ignição, velas, cabos, bobinas e diagnóstico de falhas.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                'icon_bg_color' => 'bg-red-100',
                'icon_text_color' => 'text-red-600',
                'display_order' => 13,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Sistema de Ignição Automotiva - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre velas, cabos, bobinas e componentes do sistema de ignição para diferentes tipos de motores.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Componentes do Sistema de Ignição',
                    'sections' => [
                        [
                            'title' => 'Velas de Ignição',
                            'content' => 'Responsáveis por gerar a faísca que inicia a combustão da mistura ar-combustível. A troca regular (20.000 a 60.000 km) garante partida fácil, economia de combustível e redução de emissões.'
                        ],
                        [
                            'title' => 'Cabos de Vela',
                            'content' => 'Conduzem a alta tensão da bobina até as velas. Desgastes em sua isolação podem causar falhas na ignição, perda de potência e aumento no consumo de combustível.'
                        ],
                        [
                            'title' => 'Bobinas de Ignição',
                            'content' => 'Transformam a baixa tensão da bateria em alta tensão necessária para gerar a faísca nas velas. Nos sistemas modernos, cada vela possui sua própria bobina individual.'
                        ],
                        [
                            'title' => 'Diagnóstico de Falhas',
                            'content' => 'Dificuldade na partida, funcionamento irregular do motor (marcha lenta instável), perda de potência, aumento no consumo e falhas em aceleração são indicativos de problemas no sistema de ignição.'
                        ]
                    ],
                    'alert' => 'Ao substituir as velas, sempre utilize o modelo e a calibragem recomendados pelo fabricante. Velas com grau térmico inadequado podem causar sérios danos ao motor e aumentar significativamente o consumo de combustível.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Injeção Eletrônica',
                'slug' => 'injecao-eletronica',
                'description' => 'Informações sobre sistemas de injeção eletrônica, sensores, atuadores e diagnóstico de falhas.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />',
                'icon_bg_color' => 'bg-purple-100',
                'icon_text_color' => 'text-purple-600',
                'display_order' => 14,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Injeção Eletrônica - Guia Completo | Mercado Veículos',
                    'description' => 'Informações detalhadas sobre sistemas de injeção eletrônica, sensores, atuadores e diagnóstico de falhas para diferentes veículos.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Componentes do Sistema de Injeção Eletrônica',
                    'sections' => [
                        [
                            'title' => 'Sensores',
                            'content' => 'Coletam informações como temperatura do motor, posição do acelerador, vazão de ar e oxigênio nos gases de escape. Esses dados permitem que a central ajuste a mistura ar-combustível em tempo real.'
                        ],
                        [
                            'title' => 'Atuadores',
                            'content' => 'Executam os comandos da central eletrônica, como os injetores que pulverizam combustível e a válvula de marcha lenta que controla a rotação do motor em repouso.'
                        ],
                        [
                            'title' => 'Central (ECU)',
                            'content' => 'O "cérebro" do sistema, processa os dados dos sensores e determina o tempo e quantidade ideal de combustível a ser injetado, além de controlar o ponto de ignição.'
                        ],
                        [
                            'title' => 'Bicos Injetores',
                            'content' => 'Pulverizam combustível na câmara de combustão em forma de névoa. Sua limpeza a cada 20.000-30.000 km é fundamental para manter o bom funcionamento do motor e economia de combustível.'
                        ]
                    ],
                    'alert' => 'Quando a luz de "check engine" acende no painel, é necessário realizar um diagnóstico eletrônico para identificar o problema. Ignorar este alerta pode resultar em danos ao catalisador, aumento no consumo e perda de potência.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Alinhamento e Balanceamento',
                'slug' => 'alinhamento-balanceamento',
                'description' => 'Informações sobre alinhamento, balanceamento, geometria e estabilidade do veículo.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />',
                'icon_bg_color' => 'bg-green-100',
                'icon_text_color' => 'text-green-600',
                'display_order' => 15,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Alinhamento e Balanceamento - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre alinhamento, balanceamento e geometria do veículo. Aprenda a identificar problemas e manter a estabilidade do seu carro.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Importância do Alinhamento e Balanceamento',
                    'sections' => [
                        [
                            'title' => 'Alinhamento',
                            'content' => 'Consiste no ajuste dos ângulos das rodas conforme especificações do fabricante. Um veículo desalinhado pode apresentar puxada para um dos lados, desgaste irregular dos pneus e aumento no consumo de combustível.'
                        ],
                        [
                            'title' => 'Balanceamento',
                            'content' => 'Corrige a distribuição de peso nas rodas, eliminando vibrações em determinadas velocidades. Um desbalanceamento causa desgaste prematuro de componentes da suspensão e desconforto aos ocupantes.'
                        ],
                        [
                            'title' => 'Cambagem e Caster',
                            'content' => 'São ângulos específicos da geometria que afetam a estabilidade em curvas, o retorno do volante após as curvas e o desgaste dos pneus. Seu ajuste correto é essencial para segurança e economia.'
                        ],
                        [
                            'title' => 'Intervalos Recomendados',
                            'content' => 'O alinhamento e balanceamento devem ser verificados a cada 10.000 km ou sempre que o veículo passar por impactos como buracos, meio-fio ou substituição de componentes da suspensão e direção.'
                        ]
                    ],
                    'alert' => 'Nunca ignore sinais como volante descentrado, vibração no volante ou no assoalho, ou desgaste irregular dos pneus. Estes são indicativos claros de problemas no alinhamento ou balanceamento que, se ignorados, aumentam o custo com reposição prematura de pneus e componentes.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Sistema de Escapamento',
                'slug' => 'sistema-escapamento',
                'description' => 'Informações sobre componentes do sistema de escapamento, catalisadores e redução de emissões.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
                'icon_bg_color' => 'bg-gray-100',
                'icon_text_color' => 'text-gray-600',
                'display_order' => 16,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Sistema de Escapamento - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre componentes do sistema de escapamento, catalisadores, silenciadores e redução de emissões de poluentes.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Componentes do Sistema de Escapamento',
                    'sections' => [
                        [
                            'title' => 'Coletor de Escapamento',
                            'content' => 'Conecta-se diretamente ao motor e recebe os gases da combustão. Trabalha em temperaturas extremamente altas e pode desenvolver trincas com o tempo, causando vazamentos e ruídos.'
                        ],
                        [
                            'title' => 'Catalisador',
                            'content' => 'Responsável por transformar gases tóxicos em substâncias menos nocivas. Um catalisador obstruído ou danificado causa perda de potência, aumento no consumo e falha na inspeção ambiental.'
                        ],
                        [
                            'title' => 'Silenciador',
                            'content' => 'Reduz o ruído do motor a níveis aceitáveis. Danos neste componente resultam em ruído excessivo e podem gerar multas por poluição sonora além de desconforto aos ocupantes.'
                        ],
                        [
                            'title' => 'Sonda Lambda',
                            'content' => 'Monitora a eficiência da combustão através da análise dos gases do escapamento. Sua falha altera a mistura ar-combustível, aumentando o consumo e as emissões de poluentes.'
                        ]
                    ],
                    'alert' => 'Ruídos anormais no escapamento, cheiro de gases dentro do veículo ou luzes de injeção/motor acesas no painel podem indicar problemas no sistema. Além da questão ambiental, um sistema de escapamento com vazamentos pode permitir a entrada de monóxido de carbono na cabine, apresentando risco à saúde dos ocupantes.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Combustível Adequado',
                'slug' => 'combustivel-adequado',
                'description' => 'Informações sobre tipos de combustíveis, octanagem e recomendações para diferentes motores.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />',
                'icon_bg_color' => 'bg-cyan-100',
                'icon_text_color' => 'text-cyan-600',
                'display_order' => 17,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Combustível Adequado - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre tipos de combustíveis, octanagem, etanol vs. gasolina e recomendações para diferentes motores e veículos.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Escolhendo o Combustível Correto',
                    'sections' => [
                        [
                            'title' => 'Octanagem',
                            'content' => 'A octanagem indica a resistência do combustível à detonação. Motores de alta compressão ou turboalimentados geralmente requerem combustíveis de maior octanagem para evitar a detonação e possíveis danos ao motor.'
                        ],
                        [
                            'title' => 'Etanol vs. Gasolina',
                            'content' => 'Em veículos flex, o etanol proporciona melhor desempenho, mas maior consumo. Para ser economicamente vantajoso, o preço do etanol deve ser no máximo 70% do preço da gasolina, considerando a diferença de rendimento.'
                        ],
                        [
                            'title' => 'Combustíveis Aditivados',
                            'content' => 'Contêm detergentes e dispersantes que ajudam a manter o sistema de injeção limpo. Seu uso periódico (a cada 3-4 tanques) pode ser benéfico para a manutenção do sistema de alimentação.'
                        ],
                        [
                            'title' => 'Qualidade',
                            'content' => 'Combustíveis de baixa qualidade ou adulterados podem causar sérios danos ao motor e sistema de injeção. Abasteça sempre em postos de confiança e fique atento a preços muito abaixo da média do mercado.'
                        ]
                    ],
                    'alert' => 'Sempre verifique no manual do proprietário a recomendação específica para seu motor. O uso de combustível com octanagem inferior à recomendada pode causar detonação e danos graves, enquanto octanagem superior à necessária representa apenas gasto adicional sem benefícios reais.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Limpeza e Conservação',
                'slug' => 'limpeza-conservacao',
                'description' => 'Dicas e informações sobre limpeza, conservação e proteção da pintura e interior do veículo.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />',
                'icon_bg_color' => 'bg-pink-100',
                'icon_text_color' => 'text-pink-600',
                'display_order' => 18,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Limpeza e Conservação de Veículos - Guia Completo | Mercado Veículos',
                    'description' => 'Dicas e informações sobre limpeza, conservação e proteção da pintura e interior do veículo. Mantenha seu carro sempre novo.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Cuidados com Limpeza e Conservação',
                    'sections' => [
                        [
                            'title' => 'Lavagem Correta',
                            'content' => 'Utilize produtos específicos para automóveis, nunca detergente doméstico ou sabão em pó, que podem danificar a pintura. Comece sempre pelo teto e desça pelas laterais, utilizando uma esponja ou luva diferente para as partes inferiores, que acumulam mais sujeira abrasiva.'
                        ],
                        [
                            'title' => 'Proteção da Pintura',
                            'content' => 'A aplicação periódica de cera ou selante protege a pintura contra raios UV, chuva ácida e contaminantes. Os polimentos devem ser feitos apenas quando necessário, pois removem uma fina camada de verniz a cada aplicação.'
                        ],
                        [
                            'title' => 'Interior',
                            'content' => 'Utilize aspirador regularmente e produtos específicos para cada tipo de material (couro, tecido, plástico). Protetores solares para painel e bancos previnem ressecamento e trincas causados pela exposição prolongada ao sol.'
                        ],
                        [
                            'title' => 'Vidros e Retrovisores',
                            'content' => 'Limpe com produtos específicos para vidros automotivos e microfibra limpa para evitar riscos e manchas. Produtos repelentes de água melhoram a visibilidade em dias chuvosos e facilitam a limpeza futura.'
                        ]
                    ],
                    'alert' => 'Nunca lave o veículo sob sol forte ou com a carroceria quente, pois isso pode causar manchas e danos à pintura. Prefira locais sombreados e horários com temperatura amena. Lembre-se que a conservação adequada, além de manter a aparência, valoriza o veículo na hora da revenda.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Diagnóstico Eletrônico',
                'slug' => 'diagnostico-eletronico',
                'description' => 'Informações sobre sistemas de diagnóstico, códigos de falha e interpretação de alertas do painel.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />',
                'icon_bg_color' => 'bg-indigo-100',
                'icon_text_color' => 'text-indigo-600',
                'display_order' => 19,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Diagnóstico Eletrônico Automotivo - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre sistemas de diagnóstico eletrônico, códigos de falha OBD e interpretação de alertas do painel de instrumentos.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Entendendo o Diagnóstico Eletrônico',
                    'sections' => [
                        [
                            'title' => 'Sistema OBD',
                            'content' => 'O sistema OBD (On-Board Diagnostics) monitora o funcionamento de diversos componentes do veículo. Quando detecta uma falha, armazena um código específico e pode acender a luz de "check engine" no painel.'
                        ],
                        [
                            'title' => 'Códigos de Falha',
                            'content' => 'Os códigos seguem um padrão que indica a área afetada (motor, transmissão, freios, etc.) e o problema específico. A leitura é feita com um scanner conectado à porta de diagnóstico, geralmente localizada sob o painel.'
                        ],
                        [
                            'title' => 'Luzes de Alerta',
                            'content' => 'As luzes do painel são divididas por cores: vermelhas indicam problemas graves que requerem parada imediata; amarelas alertam sobre problemas que necessitam verificação próxima; verdes e azuis são apenas informativas.'
                        ],
                        [
                            'title' => 'Diagnóstico Avançado',
                            'content' => 'Além dos códigos básicos, o diagnóstico avançado permite verificar parâmetros em tempo real, realizar testes de atuadores e configurar módulos após substituições. Essencial para identificar problemas intermitentes.'
                        ]
                    ],
                    'alert' => 'Nunca ignore a luz de "check engine" acesa no painel. Mesmo que o veículo pareça funcionar normalmente, a falha pode estar afetando o sistema antipoluição, aumentando o consumo ou causando danos progressivos que resultarão em reparos mais caros no futuro.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Revisões Programadas',
                'slug' => 'revisoes-programadas',
                'description' => 'Guia sobre revisões periódicas, itens verificados e benefícios da manutenção preventiva.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'icon_bg_color' => 'bg-green-100',
                'icon_text_color' => 'text-green-600',
                'display_order' => 20,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Revisões Programadas - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre revisões periódicas, manutenção preventiva e cuidados essenciais para manter seu veículo sempre em perfeitas condições.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'A Importância das Revisões Programadas',
                    'sections' => [
                        [
                            'title' => 'Intervalos de Revisão',
                            'content' => 'Os fabricantes estabelecem intervalos específicos para revisões, geralmente a cada 10.000 km ou anualmente (o que ocorrer primeiro). Estes intervalos são definidos com base em testes extensivos e garantem a durabilidade do veículo.'
                        ],
                        [
                            'title' => 'Itens Verificados',
                            'content' => 'Uma revisão completa inclui verificação de diversos sistemas: lubrificação, freios, suspensão, direção, arrefecimento, elétrico, transmissão e segurança. Também inclui substituição de itens com vida útil programada, como filtros e fluidos.'
                        ],
                        [
                            'title' => 'Garantia',
                            'content' => 'Realizar as revisões na periodicidade recomendada é fundamental para manter a garantia do fabricante. A não realização pode invalidar a cobertura, mesmo durante o período vigente, se o problema estiver relacionado à falta de manutenção.'
                        ],
                        [
                            'title' => 'Economia',
                            'content' => 'Embora represente um custo imediato, a manutenção preventiva é sempre mais econômica que a corretiva. Problemas detectados precocemente custam significativamente menos para reparar e previnem danos colaterais a outros componentes.'
                        ]
                    ],
                    'alert' => 'Mantenha um registro detalhado de todas as revisões e serviços realizados no veículo. Este histórico não apenas ajuda no diagnóstico de problemas futuros, como também valoriza o veículo no momento da revenda, demonstrando aos compradores potenciais que o carro recebeu os cuidados adequados.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Veículos Híbridos e Elétricos',
                'slug' => 'veiculos-hibridos-eletricos',
                'description' => 'Informações sobre manutenção e cuidados específicos para veículos com motorização híbrida ou totalmente elétrica.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                'icon_bg_color' => 'bg-blue-100',
                'icon_text_color' => 'text-blue-600',
                'display_order' => 21,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Veículos Híbridos e Elétricos - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre manutenção, carregamento e cuidados específicos com veículos híbridos e elétricos. Guia essencial para proprietários de carros eletrificados.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Cuidados Especiais com Veículos Eletrificados',
                    'sections' => [
                        [
                            'title' => 'Baterias de Alta Tensão',
                            'content' => 'São o componente mais crítico e valioso dos veículos elétricos. A durabilidade varia de 8 a 15 anos, dependendo do uso e condições de carregamento. Evite descarregar completamente ou manter a carga em 100% por longos períodos para maximizar a vida útil.'
                        ],
                        [
                            'title' => 'Carregamento',
                            'content' => 'Priorize carregadores de corrente alternada (AC) para o uso diário e reserve os carregadores rápidos de corrente contínua (DC) para viagens longas. O carregamento lento é mais benéfico para a saúde da bateria a longo prazo.'
                        ],
                        [
                            'title' => 'Sistema de Regeneração',
                            'content' => 'Os freios regenerativos capturam energia durante a frenagem, recarregando parcialmente a bateria. Este sistema reduz o desgaste dos freios convencionais, mas ainda requer verificações periódicas dos componentes mecânicos.'
                        ],
                        [
                            'title' => 'Sistema Térmico',
                            'content' => 'O gerenciamento térmico da bateria é fundamental para sua durabilidade e desempenho. Veículos elétricos possuem sistemas específicos para manter a temperatura ideal das células, que requerem manutenção especializada.'
                        ]
                    ],
                    'alert' => 'A manutenção de veículos elétricos e híbridos deve ser realizada exclusivamente por técnicos certificados e qualificados. Os sistemas de alta tensão apresentam riscos graves se manuseados incorretamente. Nunca tente realizar reparos por conta própria em componentes do sistema de propulsão elétrica.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Recall e Campanhas do Fabricante',
                'slug' => 'recall-campanhas',
                'description' => 'Informações sobre campanhas de recall, verificação de pendências e importância de manter seu veículo atualizado.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                'icon_bg_color' => 'bg-yellow-100',
                'icon_text_color' => 'text-yellow-600',
                'display_order' => 22,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Recall e Campanhas do Fabricante - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre campanhas de recall, como verificar pendências e a importância de manter seu veículo atualizado para garantir segurança.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Entendendo os Recalls Automotivos',
                    'sections' => [
                        [
                            'title' => 'O que é um Recall',
                            'content' => 'Recall é uma convocação dos proprietários para substituição ou reparo gratuito de peças, componentes ou acessórios que apresentem defeitos com potencial risco à segurança ou ao meio ambiente. É uma obrigação legal do fabricante e um direito do consumidor.'
                        ],
                        [
                            'title' => 'Como Verificar',
                            'content' => 'Verifique se seu veículo possui recalls pendentes através do site do fabricante, aplicativos oficiais ou consultando o Portal de Recalls do Ministério da Justiça pelo número do chassi. Também é possível verificar em concessionárias autorizadas.'
                        ],
                        [
                            'title' => 'Consequências de Ignorar',
                            'content' => 'Além dos riscos à segurança, ignorar recalls pode comprometer a cobertura do seguro em caso de sinistro relacionado ao problema, dificultar a venda futura do veículo e até mesmo impedir a renovação do licenciamento em alguns casos.'
                        ],
                        [
                            'title' => 'Campanhas de Serviço',
                            'content' => 'Diferentes dos recalls, são ações voluntárias do fabricante para melhorar a durabilidade, confiabilidade ou desempenho de determinados componentes. Embora não sejam obrigatórias, também são gratuitas e beneficiam o proprietário.'
                        ]
                    ],
                    'alert' => 'Os recalls não têm prazo de validade e podem ser realizados gratuitamente mesmo em veículos fora do período de garantia. Se você adquiriu um veículo usado, é especialmente importante verificar se existem recalls pendentes, pois a comunicação pode não ter chegado até você.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Documentação e Regularização',
                'slug' => 'documentacao-regularizacao',
                'description' => 'Informações sobre documentos obrigatórios, licenciamento, transferência e regularização de veículos.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                'icon_bg_color' => 'bg-red-100',
                'icon_text_color' => 'text-red-600',
                'display_order' => 23,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Documentação e Regularização de Veículos - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre documentos obrigatórios, licenciamento, transferência, vistoria e regularização de veículos junto aos órgãos competentes.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Documentação Veicular',
                    'sections' => [
                        [
                            'title' => 'Documentos Obrigatórios',
                            'content' => 'O CRV (Certificado de Registro do Veículo) e CRLV (Certificado de Registro e Licenciamento de Veículo) são os principais documentos. Atualmente, o CRLV pode ser digital (CRLV-e). Portá-lo é obrigatório para circulação, seja na versão impressa ou digital.'
                        ],
                        [
                            'title' => 'Licenciamento Anual',
                            'content' => 'Deve ser renovado anualmente mediante pagamento da taxa e, em alguns estados, após aprovação em vistoria. O não licenciamento configura infração gravíssima, com multa, pontos na CNH e remoção do veículo ao pátio.'
                        ],
                        [
                            'title' => 'Transferência de Propriedade',
                            'content' => 'Ao comprar ou vender um veículo, a transferência deve ser realizada em até 30 dias. O processo envolve vistoria, reconhecimento de firma, quitação de débitos e pagamento da taxa de transferência no DETRAN.'
                        ],
                        [
                            'title' => 'Modificações e Regularização',
                            'content' => 'Alterações nas características originais do veículo (motor, cor, combustível) devem ser autorizadas e registradas no documento. Veículos modificados sem a devida regularização podem ser autuados e removidos.'
                        ]
                    ],
                    'alert' => 'Antes de adquirir um veículo, verifique a situação junto ao DETRAN, Polícia Civil (registro de roubo/furto), e a existência de débitos pendentes como IPVA, multas e licenciamento. Exija sempre o recibo de compra e venda (CRV) com firma reconhecida do vendedor.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Customização e Acessórios',
                'slug' => 'customizacao-acessorios',
                'description' => 'Informações sobre modificações permitidas, instalação de acessórios e impacto na garantia e desempenho do veículo.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
                'icon_bg_color' => 'bg-purple-100',
                'icon_text_color' => 'text-purple-600',
                'display_order' => 24,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Customização e Acessórios - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre modificações permitidas, instalação de acessórios e impacto na garantia do veículo. Personalize seu carro com segurança e legalidade.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Customização Segura e Legal',
                    'sections' => [
                        [
                            'title' => 'Modificações Permitidas',
                            'content' => 'A legislação brasileira permite algumas alterações como troca de rodas e pneus (respeitando limites de tamanho), instalação de sistemas de som, película nos vidros (com transparência mínima regulamentada) e alguns acessórios estéticos que não comprometam a segurança.'
                        ],
                        [
                            'title' => 'Impacto na Garantia',
                            'content' => 'Modificações não homologadas pelo fabricante podem comprometer a garantia, parcial ou totalmente. Sempre consulte a concessionária antes de realizar alterações significativas e, quando possível, utilize acessórios originais ou homologados.'
                        ],
                        [
                            'title' => 'Homologação e Regularização',
                            'content' => 'Alterações mais substanciais como mudança de motor, suspensão, freios ou estrutura da carroceria exigem homologação pelo INMETRO ou entidades credenciadas, além de registro na documentação do veículo junto ao DETRAN.'
                        ],
                        [
                            'title' => 'Instalação Adequada',
                            'content' => 'Mesmo acessórios simples como alarmes, centrais multimídia ou faróis auxiliares devem ser instalados corretamente para evitar problemas elétricos, interferências ou comprometimento de outros sistemas do veículo.'
                        ]
                    ],
                    'alert' => 'Modificações não regularizadas podem resultar em multas, apreensão do veículo e até mesmo negativa de cobertura pelo seguro em caso de sinistro. Sempre pesquise a legislação atual e consulte profissionais especializados antes de modificar seu veículo.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Preparação para Viagens',
                'slug' => 'preparacao-viagens',
                'description' => 'Checklist para preparação do veículo antes de viagens longas, itens essenciais e cuidados especiais.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />',
                'icon_bg_color' => 'bg-orange-100',
                'icon_text_color' => 'text-orange-600',
                'display_order' => 25,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Preparação para Viagens - Guia Completo | Mercado Veículos',
                    'description' => 'Checklist completo para preparar seu veículo antes de viagens longas, itens essenciais e cuidados especiais para garantir uma viagem segura.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Checklist para Viagens Seguras',
                    'sections' => [
                        [
                            'title' => 'Verificações Mecânicas',
                            'content' => 'Antes de viajar, verifique: níveis de óleo, fluido de freio e radiador; estado e pressão dos pneus (incluindo estepe); funcionamento dos freios; suspensão; luzes e sinalização; e estado das correias. Resolva qualquer problema, mesmo que pareça pequeno, antes de pegar a estrada.'
                        ],
                        [
                            'title' => 'Itens Obrigatórios',
                            'content' => 'Certifique-se de ter todos os itens obrigatórios em boas condições: estepe, macaco, chave de roda, triângulo de sinalização e extintor de incêndio. A falta destes itens pode resultar em multas e, mais importante, deixá-lo desamparado em emergências.'
                        ],
                        [
                            'title' => 'Kit de Emergência',
                            'content' => 'Monte um kit com: lanterna e pilhas extras, cabos para partida auxiliar, ferramentas básicas, fita isolante, kit de primeiros socorros, água potável, lona plástica (útil em caso de chuva), e carregador veicular para celular. Em viagens para áreas remotas, inclua alimentos não perecíveis.'
                        ],
                        [
                            'title' => 'Documentação',
                            'content' => 'Tenha em mãos a documentação do veículo, CNH válida, comprovante do seguro e contatos de assistência 24h. Para viagens internacionais, verifique os documentos adicionais necessários e o seguro carta verde para países do Mercosul.'
                        ]
                    ],
                    'alert' => 'Planeje sua rota com antecedência, identificando pontos de parada, postos de combustível e oficinas ao longo do trajeto. Em viagens longas, faça pausas a cada duas horas para descanso. A fadiga ao volante é uma das principais causas de acidentes em rodovias.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Veículos Parados por Longos Períodos',
                'slug' => 'veiculos-parados',
                'description' => 'Cuidados com veículos que ficam parados por semanas ou meses, evitando danos e deterioração.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'icon_bg_color' => 'bg-teal-100',
                'icon_text_color' => 'text-teal-600',
                'display_order' => 26,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Cuidados com Veículos Parados - Guia Completo | Mercado Veículos',
                    'description' => 'Informações sobre cuidados essenciais com veículos que ficam parados por longos períodos. Evite danos e garanta o bom funcionamento ao retomar o uso.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Preservando seu Veículo Durante Longos Períodos sem Uso',
                    'sections' => [
                        [
                            'title' => 'Cuidados com a Bateria',
                            'content' => 'A descarga da bateria é um dos primeiros problemas em veículos parados. Para períodos superiores a 15 dias, considere desconectar o terminal negativo ou utilizar um carregador flutuante (mantenedor de carga) que mantém a bateria carregada sem danificá-la.'
                        ],
                        [
                            'title' => 'Pneus e Suspensão',
                            'content' => 'Pneus que permanecem na mesma posição por muito tempo podem desenvolver "pontos planos" e deformações. Se possível, mova o veículo alguns centímetros a cada duas semanas. Para períodos muito longos, considere apoiar o veículo em cavaletes para aliviar o peso sobre os pneus e suspensão.'
                        ],
                        [
                            'title' => 'Fluidos e Combustível',
                            'content' => 'Mantenha o tanque cheio para evitar condensação e oxidação interna. Para períodos superiores a 3 meses, considere adicionar estabilizador de combustível. Verifique todos os fluidos antes de deixar o veículo parado e novamente antes de voltar a utilizá-lo.'
                        ],
                        [
                            'title' => 'Proteção Externa e Interna',
                            'content' => 'Use capa apropriada se o veículo ficar exposto ao tempo. Para o interior, deixe uma pequena abertura nos vidros para evitar mofo e odores. Desodorizantes ou sachês dessecantes ajudam a manter o interior seco, especialmente em climas úmidos.'
                        ]
                    ],
                    'alert' => 'Antes de voltar a utilizar um veículo que ficou parado por mais de 30 dias, faça uma verificação completa: níveis de fluidos, pressão dos pneus, freios e bateria. Ligue o motor e deixe-o funcionar por alguns minutos antes de movimentar o veículo. Nos primeiros quilômetros, dirija com cautela e atenção aos ruídos e comportamento anormal.'
                ])
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Manutenção Sazonal',
                'slug' => 'manutencao-sazonal',
                'description' => 'Cuidados específicos com seu veículo conforme as estações do ano e condições climáticas.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />',
                'icon_bg_color' => 'bg-cyan-100',
                'icon_text_color' => 'text-cyan-600',
                'display_order' => 27,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Manutenção Sazonal de Veículos - Guia Completo | Mercado Veículos',
                    'description' => 'Guia completo sobre cuidados específicos com seu veículo conforme as estações do ano e condições climáticas para garantir segurança e durabilidade.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Adaptando a Manutenção às Estações do Ano',
                    'sections' => [
                        [
                            'title' => 'Verão',
                            'content' => 'Com o calor intenso, o sistema de arrefecimento é mais exigido. Verifique nível e condição do líquido do radiador, funcionamento da ventoinha e mangueiras. O ar-condicionado também requer atenção, com verificação da carga de gás e limpeza do filtro de cabine. A bateria tende a degradar mais rapidamente em altas temperaturas.'
                        ],
                        [
                            'title' => 'Inverno',
                            'content' => 'Em regiões de clima frio, a bateria é muito exigida no arranque e os sistemas elétricos trabalham mais. Verifique a concentração do aditivo anticongelante no radiador se a região atingir temperaturas próximas a 0°C. Inspecione o sistema de desembaçamento dos vidros e as palhetas do limpador, essenciais em dias chuvosos.'
                        ],
                        [
                            'title' => 'Períodos Chuvosos',
                            'content' => 'Verifique a condição dos pneus e profundidade dos sulcos (mínimo 1,6mm, ideal acima de 3mm para áreas com chuvas intensas). Substitua as palhetas do limpador se estiverem ressecadas ou danificadas. Teste o sistema de iluminação e desembaçador, e certifique-se que os freios estão em perfeitas condições.'
                        ],
                        [
                            'title' => 'Regiões com Poeira ou Areia',
                            'content' => 'Em áreas com muita poeira, substitua o filtro de ar com maior frequência e considere a instalação de pré-filtros em casos extremos. Mantenha os sistemas de vedação das portas limpos e lubrifique-os para evitar a entrada de poeira. Limpe frequentemente o radiador para evitar obstrução por poeira.'
                        ]
                    ],
                    'alert' => 'Adaptação à região e clima local é essencial: veículos no litoral requerem maior proteção contra corrosão; em áreas montanhosas, os sistemas de freio e transmissão são mais exigidos; em regiões de calor extremo, a refrigeração do motor e o ar-condicionado precisam de checagens mais frequentes. Sempre consulte o manual do proprietário para recomendações específicas do fabricante para diferentes condições de uso.'
                ])
            ],

            // Adicione essa nova categoria à lista no arquivo:

            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Troca de Óleo',
                'slug' => 'troca-oleo',
                'description' => 'Guia completo sobre procedimentos, intervalos e melhores práticas para a troca de óleo do motor.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />',
                'icon_bg_color' => 'bg-yellow-100',
                'icon_text_color' => 'text-yellow-600',
                'display_order' => 28,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Troca de Óleo - Guia Completo | Mercado Veículos',
                    'description' => 'Informações detalhadas sobre procedimentos, intervalos e melhores práticas para a troca de óleo do motor. Evite danos e prolongue a vida útil do seu veículo.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Procedimentos para Troca de Óleo',
                    'sections' => [
                        [
                            'title' => 'Intervalos Corretos',
                            'content' => 'O intervalo de troca varia de acordo com o tipo de óleo, modelo do veículo e condições de uso. Óleos minerais geralmente requerem troca a cada 5.000-8.000 km, semissintéticos a cada 7.500-10.000 km, e sintéticos podem chegar a 15.000 km. Em condições severas (trânsito intenso, temperaturas extremas), estes intervalos devem ser reduzidos.'
                        ],
                        [
                            'title' => 'Procedimento Adequado',
                            'content' => 'A troca correta inclui: drenagem completa do óleo usado com o motor morno (não quente), substituição do filtro de óleo, limpeza do bujão magnético (se existente), verificação de vazamentos e utilização da quantidade exata especificada no manual do proprietário.'
                        ],
                        [
                            'title' => 'Erros Comuns',
                            'content' => 'Misturar diferentes tipos de óleo, ultrapassar significativamente o intervalo recomendado, não trocar o filtro junto com o óleo ou utilizar filtros de baixa qualidade são erros frequentes que podem comprometer a lubrificação e causar danos graves ao motor.'
                        ],
                        [
                            'title' => 'Descarte Adequado',
                            'content' => 'O óleo usado é altamente contaminante e deve ser descartado de forma correta. Armazene em recipientes fechados e entregue em postos de coleta, concessionárias ou oficinas que façam a destinação adequada. Um litro de óleo pode contaminar até 1 milhão de litros de água.'
                        ]
                    ],
                    'alert' => 'Mantenha um registro detalhado das trocas de óleo, incluindo data, quilometragem, tipo de óleo utilizado e oficina responsável. Isto não apenas ajuda a controlar os intervalos corretos como também valoriza o veículo na revenda, demonstrando manutenção adequada.'
                ])
            ],

            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Pneus e Rodas',
                'slug' => 'pneus-rodas',
                'description' => 'Informações sobre tipos, medidas, calibragem e manutenção de pneus e rodas para diferentes veículos.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'icon_bg_color' => 'bg-blue-100',
                'icon_text_color' => 'text-blue-600',
                'display_order' => 29,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Pneus e Rodas - Guia Completo | Mercado Veículos',
                    'description' => 'Guia completo sobre pneus, rodas e calibragem para todos os modelos de veículos. Informações precisas para manutenção e segurança.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Cuidados Essenciais com Pneus e Rodas',
                    'sections' => [
                        [
                            'title' => 'Calibragem Correta',
                            'content' => 'Pneus com pressão inadequada podem comprometer a estabilidade do veículo, aumentar a distância de frenagem e reduzir a aderência em piso molhado, aumentando significativamente o risco de acidentes. A calibragem correta pode reduzir o consumo de combustível em até 3% e aumentar a vida útil dos pneus em até 20%.'
                        ],
                        [
                            'title' => 'Medidas e Especificações',
                            'content' => 'As inscrições laterais dos pneus contêm informações como largura, altura, diâmetro, índice de carga e velocidade. Utilizar pneus com especificações diferentes das recomendadas pode afetar a segurança e desempenho do veículo.'
                        ],
                        [
                            'title' => 'Tipos de Pneus',
                            'content' => 'Existem pneus específicos para cada condição de uso: convencionais para uso urbano, esportivos para melhor aderência, all-terrain para uso misto, e modelos específicos para chuva ou neve em regiões com condições climáticas extremas.'
                        ],
                        [
                            'title' => 'Rodízio e Balanceamento',
                            'content' => 'O rodízio de pneus a cada 10.000 km equilibra o desgaste e aumenta a vida útil do conjunto. Já o balanceamento correto evita vibrações e desgaste prematuro dos componentes da suspensão.'
                        ]
                    ],
                    'alert' => 'Verifique a pressão dos pneus pelo menos uma vez por mês e sempre antes de viagens longas. A calibragem deve ser feita com os pneus frios, preferencialmente pela manhã. Nunca negligencie o desgaste dos pneus - são seu único ponto de contato com o solo.'
                ])
            ],

            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Tabela de Manutenção',
                'slug' => 'tabela-manutencao',
                'description' => 'Tabelas e cronogramas de manutenção para diferentes modelos e tipos de veículos.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                'icon_bg_color' => 'bg-green-100',
                'icon_text_color' => 'text-green-600',
                'display_order' => 30,
                'is_active' => true,
                'seo_info' => json_encode([
                    'title' => 'Tabelas de Manutenção - Guia Completo | Mercado Veículos',
                    'description' => 'Tabelas e cronogramas detalhados de manutenção para diferentes modelos de veículos. Organize e planeje a manutenção preventiva do seu carro.'
                ]),
                'info_sections' => json_encode([
                    'title' => 'Como Utilizar as Tabelas de Manutenção',
                    'sections' => [
                        [
                            'title' => 'Manutenção por Quilometragem',
                            'content' => 'As tabelas indicam serviços necessários em marcos específicos (10.000 km, n20.000 km, etc.). Inclui substituição programada de componentes como filtros, correias, velas, fluidos, além de inspeções em sistemas fundamentais como freios, suspensão e direção.'
                        ],
                        [
                            'title' => 'Manutenção por Tempo',
                            'content' => 'Alguns componentes exigem substituição com base no tempo, independentemente da quilometragem. Fluidos hidráulicos, líquido de arrefecimento e óleo de motor (mesmo com baixa quilometragem) degradam-se com o tempo e devem seguir os prazos recomendados.'
                        ],
                        [
                            'title' => 'Condições Severas',
                            'content' => 'Veículos utilizados em condições consideradas severas (tráfego intenso, curtas distâncias, poeira, temperaturas extremas, reboque frequente) requerem intervalos de manutenção reduzidos, geralmente 50-70% do intervalo regular.'
                        ],
                        [
                            'title' => 'Personalização',
                            'content' => 'Adapte as tabelas genéricas às especificações do seu veículo (consultando o manual do proprietário) e crie um cronograma personalizado, levando em conta seu padrão de uso, condições climáticas locais e recomendações específicas do fabricante.'
                        ]
                    ],
                    'alert' => 'Mantenha registros detalhados de todas as manutenções realizadas, incluindo notas fiscais e ordem de serviço. Isto facilita o acompanhamento do cronograma, proporciona histórico em caso de problemas recorrentes e valoriza significativamente o veículo no momento da revenda.'
                ])
            ]
        ];

        foreach ($categories as $category) {
            MaintenanceCategory::create($category);
        }
    }
}
