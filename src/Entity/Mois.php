<?php

namespace App\Entity;

use App\Repository\MoisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité pour la table "mois" listant les mois de l'année.
 *
 * @ORM\Table(name="mois", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_MOIS_NOM", columns={"nom"})})
 * @ORM\Entity(repositoryClass=MoisRepository::class)
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
    private ?int $ordre = null;

    /**
     * @ORM\Column(name="nom", type="string", length=15, nullable=false, options={"comment"="Le nom du mois"})
     *
     * @var string|null Nom du mois
     */
    private ?string $nom = null;

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

    public function __toString()
    {
        return $this->nom ?: 'Inconnu';
    }
}
