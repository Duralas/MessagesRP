<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité pour la table "factions" listant les factions disponibles à Dùralas.
 *
 * @ORM\Table(name="factions")
 * @ORM\Entity
 */
class Faction
{
    /**
     * @ORM\Column(name="code", type="string", length=10, nullable=false, options={"comment"="Le code d'identification"})
     * @ORM\Id
     *
     * @var string|null Code identification
     */
    private ?string $code;

    /**
     * @ORM\Column(name="nom", type="string", length=150, nullable=false, options={"comment"="Le nom de la faction"})
     *
     * @var string|null Nom faction
     */
    private ?string $nom;

    /**
     * Récupère le code d'identification de l'entité.
     *
     * @return string|null Code identification
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Récupère le nom de la faction.
     *
     * @return string|null Nom faction
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function __toString(): string
    {
        return $this->getNom() ?: 'Sans nom';
    }
}
