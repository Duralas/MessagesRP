<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité pour la table "personnages" indiquant l'ensemble des personnages de Dùralas définis dans l'application.
 *
 * @ORM\Table(name="personnages", indexes={@ORM\Index(name="FK_FACTION_PERSONNAGE", columns={"faction"})})
 * @ORM\Entity
 */
class Personnage
{
    /**
     * @ORM\Column(name="nom", type="string", length=150, nullable=false, options={"comment"="Le nom du personnage"})
     * @ORM\Id
     *
     * @Assert\NotBlank(message="Un personnage a forcément un nom permettant de l'identifier.")
     * @Assert\Unique(message="Le nom du personnage est unique pour pouvoir l'identifier.")
     *
     * @var string|null Nom du personnage (sert d'identifiant)
     */
    private ?string $nom;

    /**
     * @ORM\Column(name="actif", type="boolean", nullable=false, options={"comment"="Si le personnage est actif ou non"})
     *
     * @var bool Si le personnage est actif ou non
     */
    private bool $actif = true;

    /**
     * @ORM\ManyToOne(targetEntity="Faction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="faction", referencedColumnName="code")
     * })
     *
     * @var Faction|null Faction à laquelle est rattaché le personnage
     */
    private ?Faction $faction;

    /**
     * Récupère le nom du personnage.
     *
     * @return string|null Nom personnage
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Spécifie le nom du personnage.
     *
     * @param string $nom Nom personnage
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * Indique si le personnage est actif.
     *
     * @return bool True => actif / False => non actif
     */
    public function isActif(): bool
    {
        return $this->actif;
    }

    /**
     * Spécifie si le personnage est actif ou non.
     *
     * @param bool $actif True => actif / False => non actif
     */
    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }

    /**
     * Récupère la faction à laquelle appartient le personnage.
     *
     * Si NULL, le personnage n'appartient à aucune faction.
     *
     * @return Faction|null Faction de Dùralas / Null => Aucune faction
     */
    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    /**
     * Spécifie la faction à laquelle appartient le personnage.
     *
     * Si NULL, le personnage n'appartient à aucune faction.
     *
     * @param Faction|null $faction Faction de Dùralas / Null => Aucune faction
     */
    public function setFaction(?Faction $faction): void
    {
        $this->faction = $faction;
    }
}
