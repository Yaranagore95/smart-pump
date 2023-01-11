<?php


namespace App\Entity\customEntity;


class CarburantTotal
{
    private string $type;

    private int $somme;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getSomme(): int
    {
        return $this->somme;
    }

    /**
     * @param int $somme
     */
    public function setSomme(int $somme): void
    {
        $this->somme = $somme;
    }
}