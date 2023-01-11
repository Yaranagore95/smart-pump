<?php


namespace App\Entity\customEntity;


use App\Entity\TypeCarburant;

class VenteGlobalTypeCarburant
{
    private TypeCarburant $typeCarburant;
    private $qteBon;
    private $montantBon;
    private $qte;
    private $montant;
    private $montantNet;

    /**
     * @return TypeCarburant
     */
    public function getTypeCarburant(): TypeCarburant
    {
        return $this->typeCarburant;
    }

    /**
     * @param TypeCarburant $typeCarburant
     */
    public function setTypeCarburant(TypeCarburant $typeCarburant): void
    {
        $this->typeCarburant = $typeCarburant;
    }

    /**
     * @return mixed
     */
    public function getQteBon()
    {
        return $this->qteBon;
    }

    /**
     * @param mixed $qteBon
     */
    public function setQteBon($qteBon): void
    {
        $this->qteBon = $qteBon;
    }

    /**
     * @return mixed
     */
    public function getQte()
    {
        return $this->qte;
    }

    /**
     * @param mixed $qte
     */
    public function setQte($qte): void
    {
        $this->qte = $qte;
    }

    /**
     * @return mixed
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * @param mixed $montant
     */
    public function setMontant($montant): void
    {
        $this->montant = $montant;
    }

    /**
     * @return mixed
     */
    public function getMontantNet()
    {
        return $this->montantNet;
    }

    /**
     * @param mixed $montantNet
     */
    public function setMontantNet($montantNet): void
    {
        $this->montantNet = $montantNet;
    }

    /**
     * @return mixed
     */
    public function getMontantBon()
    {
        return $this->montantBon;
    }

    /**
     * @param mixed $montantBon
     */
    public function setMontantBon($montantBon): void
    {
        $this->montantBon = $montantBon;
    }


}