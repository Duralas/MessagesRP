<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité pour la table "zones" listant les zones de Dùralas (appartenant à des régions).
 *
 * @ORM\Table(name="zones",
 * uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_ZONES_NOM", columns={"nom"})},
 * indexes={
 *     @ORM\Index(name="FK_TYPE_ZONE", columns={"type"}),
 *     @ORM\Index(name="FK_REGION_ZONE", columns={"region"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ZoneRepository")
 */
class Zone
{
    /**
     * @ORM\Column(name="code", type="string", length=10, nullable=false, options={"comment"="Code d'identification de la zone"})
     * @ORM\Id
     *
     * @var string|null Code identification
     */
    private ?string $code;

    /**
     * @ORM\Column(name="nom", type="string", length=250, nullable=false, options={"comment"="Nom de la zone"})
     *
     * @var string|null Nom zone
     */
    private ?string $nom;

    /**
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="region", referencedColumnName="code")
     * })
     *
     * @var Region|null Région de Dùralas où se trouve la région
     */
    private ?Region $region;

    /**
     * @ORM\ManyToOne(targetEntity="ZoneType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type", referencedColumnName="code")
     * })
     *
     * @var ZoneType|null Type de zone
     */
    private ?ZoneType $type;

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
     * Récupère le nom de la zone.
     *
     * @return string|null Nom zone
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Récupère la région dans laquelle se trouve la zone.
     *
     * @return Region|null Région de Dùralas
     */
    public function getRegion(): ?Region
    {
        return $this->region;
    }

    /**
     * Récupère le type de la zone.
     *
     * @return ZoneType|null Type de zone
     */
    public function getType(): ?ZoneType
    {
        return $this->type;
    }

    public function __toString()
    {
        return $this->nom ?: 'Inconnu';
    }
}
