<?php

namespace ArcheeNic\Cbr\Object;

class CurrencyItemObject
{
    private ?string $name;
    private ?string $engName;
    private ?int    $nominal;
    private ?string $parentCode;
    private ?int    $ISONumCode;
    private ?string $ISOCharCode;

    /**
     * @param  mixed[]  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->name        = $attributes['name'] ?? null;
        $this->engName     = $attributes['engName'] ?? null;
        $this->nominal     = $attributes['nominal'] ?? null;
        $this->parentCode  = $attributes['parentCode'] ?? null;
        $this->ISONumCode  = $attributes['ISONumCode'] ?? null;
        $this->ISOCharCode = $attributes['ISOCharCode'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): CurrencyItemObject
    {
        $this->name = $name;

        return $this;
    }

    public function getEngName(): ?string
    {
        return $this->engName;
    }

    public function setEngName(?string $engName): CurrencyItemObject
    {
        $this->engName = $engName;

        return $this;
    }

    public function getNominal(): ?int
    {
        return $this->nominal;
    }

    public function setNominal(?int $nominal): CurrencyItemObject
    {
        $this->nominal = $nominal;

        return $this;
    }

    public function getParentCode(): ?string
    {
        return $this->parentCode;
    }

    public function setParentCode(?string $parentCode): CurrencyItemObject
    {
        $this->parentCode = trim($parentCode);

        return $this;
    }

    public function getISONumCode(): ?int
    {
        return $this->ISONumCode;
    }

    public function setISONumCode(?int $ISONumCode): CurrencyItemObject
    {
        $this->ISONumCode = $ISONumCode;

        return $this;
    }

    public function getISOCharCode(): ?string
    {
        return $this->ISOCharCode;
    }

    public function setISOCharCode(?string $ISOCharCode): CurrencyItemObject
    {
        $this->ISOCharCode = trim($ISOCharCode);

        return $this;
    }
}
