<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité pour la table "régions" listant les régions de Dùralas.
 *
 * @ORM\Table(name="regions", uniqueConstraints={@ORM\UniqueConstraint(name="nom", columns={"nom"})})
 * @ORM\Entity
 */
class Region
{
    /**
     * @ORM\Column(name="code", type="string", length=10, nullable=false, options={"comment"="Code représentant la région"})
     * @ORM\Id
     *
     * @var string|null Code indentification
     */
    private $code;

    /**
     * @ORM\Column(name="nom", type="string", length=250, nullable=false, options={"comment"="Nom de la région"}
     *
     * @var string|null Nom région
     */
    private $nom;

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
     * Récupère le nom de la région.
     *
     * @return string|null Nom région
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }
}
