<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZoneTypes.
 *
 * @ORM\Table(name="zone_types")
 * @ORM\Entity
 */
class ZoneType
{
    /**
     * @ORM\Column(name="code", type="string", length=10, nullable=false, options={"comment"="Code d'identification du type"})
     * @ORM\Id
     *
     * @var string|null Code identification
     */
    private ?string $code;

    /**
     * @ORM\Column(name="libelle", type="string", length=250, nullable=false, options={"comment"="Libellé du type"})
     *
     * @var string|null Libellé type
     */
    private $libelle;

    /**
     * @ORM\Column(name="exclusion", type="boolean", nullable=false, options={"comment"="Marque le type comme exclu des statistiques"})
     *
     * @var bool Type à exclure
     */
    private $exclusion = false;

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
     * Récupère le libellé du type.
     *
     * @return string|null Libellé type
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * Indique si le type est à exclure.
     *
     * @return bool True => à exclure / False => à inclure
     */
    public function isExclusion(): bool
    {
        return $this->exclusion;
    }
}
