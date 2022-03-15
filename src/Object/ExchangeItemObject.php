<?php

namespace ArcheeNic\Cbr\Object;

use DateTime;

class ExchangeItemObject
{
    private ?string   $code;
    private ?int      $nominal;
    private ?float    $value;
    private ?DateTime $date;
    private ?DateTime $previousSaleDay;

    /**
     * @param  array<mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->code            = $attributes['code'] ?? null;
        $this->nominal         = $attributes['nominal'] ?? null;
        $this->value           = $attributes['value'] ?? null;
        $this->date            = $attributes['date'] ?? null;
        $this->previousSaleDay = $attributes['previousSaleDay'] ?? null;
    }

    public function getPreviousSaleDay(): ?DateTime
    {
        return $this->previousSaleDay;
    }

    public function setPreviousSaleDay(?DateTime $previousSaleDay): ExchangeItemObject
    {
        $this->previousSaleDay = $previousSaleDay;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): ExchangeItemObject
    {
        $this->code = $code;

        return $this;
    }

    public function getNominal(): ?int
    {
        return $this->nominal;
    }

    public function setNominal(?int $nominal): ExchangeItemObject
    {
        $this->nominal = $nominal;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): ExchangeItemObject
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): ExchangeItemObject
    {
        $this->date = $date ? (clone $date) : null;

        return $this;
    }
}
