<?php

declare(strict_types=1);

namespace Src\ArticleGenerator\Domain\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Value Object para representar condições de tráfego
 * 
 * Implementa uma representação imutável de condições de tráfego,
 * com validações e métodos para classificar sua severidade.
 */
readonly class TrafficCondition
{
    /**
     * @param string $location Localização da condição de tráfego
     * @param int $congestionLevel Nível de congestionamento (0-100)
     * @param int $averageSpeed Velocidade média em km/h
     * @param int $delayMinutes Minutos de atraso estimados
     * @param string|null $cause Causa da condição de tráfego (opcional)
     * @param DateTimeInterface|null $reportedAt Data/hora do relatório (opcional)
     * 
     * @throws \InvalidArgumentException Se os dados não atenderem aos requisitos
     */
    public function __construct(
        public string $location,
        public int $congestionLevel,
        public int $averageSpeed,
        public int $delayMinutes,
        public ?string $cause = null,
        public ?DateTimeInterface $reportedAt = null
    ) {
        $this->validate();
    }

    /**
     * Valida os componentes da condição de tráfego
     * 
     * @throws \InvalidArgumentException Se os dados não atenderem aos requisitos
     */
    private function validate(): void
    {
        if (empty(trim($this->location))) {
            throw new \InvalidArgumentException('A localização não pode ser vazia');
        }

        if ($this->congestionLevel < 0 || $this->congestionLevel > 100) {
            throw new \InvalidArgumentException('O nível de congestionamento deve estar entre 0 e 100');
        }

        if ($this->averageSpeed < 0) {
            throw new \InvalidArgumentException('A velocidade média não pode ser negativa');
        }

        if ($this->delayMinutes < 0) {
            throw new \InvalidArgumentException('Os minutos de atraso não podem ser negativos');
        }
    }

    /**
     * Retorna a severidade da condição de tráfego com base nos parâmetros
     */
    public function getSeverity(): TrafficSeverity
    {
        // Lógica para determinar a severidade baseada em múltiplos fatores
        $severityScore = 0;
        
        // Fatores de congestionamento
        if ($this->congestionLevel >= 80) {
            $severityScore += 4;
        } elseif ($this->congestionLevel >= 60) {
            $severityScore += 3;
        } elseif ($this->congestionLevel >= 40) {
            $severityScore += 2;
        } elseif ($this->congestionLevel >= 20) {
            $severityScore += 1;
        }
        
        // Fatores de velocidade
        if ($this->averageSpeed <= 5) {
            $severityScore += 4;
        } elseif ($this->averageSpeed <= 15) {
            $severityScore += 3;
        } elseif ($this->averageSpeed <= 30) {
            $severityScore += 2;
        } elseif ($this->averageSpeed <= 50) {
            $severityScore += 1;
        }
        
        // Fatores de atraso
        if ($this->delayMinutes >= 45) {
            $severityScore += 4;
        } elseif ($this->delayMinutes >= 30) {
            $severityScore += 3;
        } elseif ($this->delayMinutes >= 15) {
            $severityScore += 2;
        } elseif ($this->delayMinutes >= 5) {
            $severityScore += 1;
        }
        
        // Mapeia o score para a severidade
        return match(true) {
            $severityScore >= 10 => TrafficSeverity::CRITICAL,
            $severityScore >= 7 => TrafficSeverity::SEVERE,
            $severityScore >= 4 => TrafficSeverity::MODERATE,
            $severityScore >= 1 => TrafficSeverity::MILD,
            default => TrafficSeverity::NONE
        };
    }

    /**
     * Retorna uma descrição textual da condição de tráfego
     */
    public function getDescription(): string
    {
        $severity = $this->getSeverity();
        $description = "Tráfego na {$this->location}: ";
        
        $description .= match($severity) {
            TrafficSeverity::CRITICAL => "extremamente congestionado",
            TrafficSeverity::SEVERE => "severamente congestionado",
            TrafficSeverity::MODERATE => "moderadamente congestionado",
            TrafficSeverity::MILD => "com lentidão",
            TrafficSeverity::NONE => "fluindo normalmente"
        };
        
        if ($this->averageSpeed > 0) {
            $description .= ", velocidade média de {$this->averageSpeed} km/h";
        }
        
        if ($this->delayMinutes > 0) {
            $description .= ", atraso estimado de {$this->delayMinutes} minutos";
        }
        
        if ($this->cause) {
            $description .= ". Causa: {$this->cause}";
        }
        
        return $description;
    }

    /**
     * Retorna uma representação de string da condição de tráfego
     */
    public function __toString(): string
    {
        return $this->getDescription();
    }

    /**
     * Verifica se outra condição de tráfego é igual a esta
     */
    public function equals(self $other): bool
    {
        return $this->location === $other->location && 
               $this->congestionLevel === $other->congestionLevel && 
               $this->averageSpeed === $other->averageSpeed && 
               $this->delayMinutes === $other->delayMinutes && 
               $this->cause === $other->cause;
    }

    /**
     * Verifica se a condição de tráfego é crítica
     */
    public function isCritical(): bool
    {
        return $this->getSeverity() === TrafficSeverity::CRITICAL;
    }

    /**
     * Verifica se a condição de tráfego é severa ou pior
     */
    public function isSevereOrWorse(): bool
    {
        $severity = $this->getSeverity();
        return $severity === TrafficSeverity::SEVERE || $severity === TrafficSeverity::CRITICAL;
    }

    /**
     * Calcula o tempo de viagem estimado em minutos para uma distância fornecida
     * 
     * @param float $distanceKm Distância em quilômetros
     * @return int Tempo estimado em minutos
     */
    public function estimateTravelTime(float $distanceKm): int
    {
        if ($this->averageSpeed <= 0) {
            return 0; // Evita divisão por zero
        }
        
        // Tempo base em horas: distância / velocidade
        $baseTimeHours = $distanceKm / $this->averageSpeed;
        
        // Converte para minutos e adiciona o atraso
        $totalMinutes = (int) ceil($baseTimeHours * 60) + $this->delayMinutes;
        
        return $totalMinutes;
    }

    /**
     * Cria uma condição de tráfego ideal (sem congestionamento)
     * 
     * @param string $location Localização
     * @param int $averageSpeed Velocidade média ideal em km/h
     * @return self
     */
    public static function createIdeal(string $location, int $averageSpeed = 60): self
    {
        return new self(
            location: $location,
            congestionLevel: 0,
            averageSpeed: $averageSpeed,
            delayMinutes: 0,
            reportedAt: new DateTimeImmutable()
        );
    }

    /**
     * Cria uma condição de tráfego crítica
     * 
     * @param string $location Localização
     * @param string $cause Causa do congestionamento
     * @return self
     */
    public static function createCritical(string $location, string $cause): self
    {
        return new self(
            location: $location,
            congestionLevel: 95,
            averageSpeed: 5,
            delayMinutes: 60,
            cause: $cause,
            reportedAt: new DateTimeImmutable()
        );
    }

    /**
     * Verifica se a condição de tráfego é recente
     * 
     * @param int $minutesThreshold Número de minutos para considerar como recente
     * @return bool
     */
    public function isRecent(int $minutesThreshold = 30): bool
    {
        if ($this->reportedAt === null) {
            return false;
        }
        
        $now = new DateTimeImmutable();
        $diff = $now->getTimestamp() - $this->reportedAt->getTimestamp();
        
        return ($diff / 60) < $minutesThreshold;
    }
}