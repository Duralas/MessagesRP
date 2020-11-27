<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité pour la table "mois" listant les mois de l'année.
 *
 * @ORM\Table(name="mois", uniqueConstraints={@ORM\UniqueConstraint(name="nom", columns={"nom"})})
 * @ORM\Entity
 */
class Mois
{
    /**
     * @ORM\Column(name="ordre", type="smallint", nullable=false, options={"comment"="L'ordre du mois dans l'année"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int|null Ordre du mois
     */
    private ?int $ordre;

    /**
     * @ORM\Column(name="nom", type="string", length=15, nullable=false, options={"comment"="Le nom du mois"})
     *
     * @var string|null Nom du mois
     */
    private ?string $nom;

    /**
     * Récupère l'ordre du mois.
     *
     * @return int|null Ordre du mois
     */
    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    /**
     * Récupère le nom du mois.
     *
     * @return string|null Nom du mois
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }
}
