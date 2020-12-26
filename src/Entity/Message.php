<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité pour la table "messages" indiquant les statistiques de messages RP.
 *
 * @ORM\Table(name="messages", indexes={
 *     @ORM\Index(name="FK_PERSONNAGE_MESSAGE", columns={"personnage"}),
 *     @ORM\Index(name="FK_MOIS_MESSAGE", columns={"mois"}),
 *     @ORM\Index(name="FK_ZONE_MESSAGE", columns={"zone"}),
 *     @ORM\Index(name="IDX_DB021E96B06A3786", columns={"période"})
 * })
 * @ORM\Entity
 */
class Message
{
    /**
     * @ORM\Column(name="nombre", type="smallint", nullable=false, options={"default"="1","comment"="Nombre de messages"})
     *
     * @Assert\Positive(message="Le nombre de messages postés doit être supérieur à 0.")
     *
     * @var int Nombre de messages
     */
    private int $nombre = 1;

    /**
     * @ORM\Column(name="modification", type="datetime", nullable=false, options={"default"="current_timestamp()","comment"="Dernière date de modification"})
     * @Gedmo\Timestampable(on="update")
     *
     * @var DateTime Dernière date de modification
     */
    private DateTime $updatedAt;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Periode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="période", referencedColumnName="code")
     * })
     *
     * @Assert\NotNull(message="La période du Phénix Enchaîné est essentielle pour le recensement.")
     *
     * @var Periode|null Période du Phénix Enchaîné pour laquelle est(sont) défini(s) le(s) message(s)
     */
    private ?Periode $periode;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Zone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="zone", referencedColumnName="code")
     * })
     *
     * @Assert\NotNull(message="Le(s) message(s) a(ont) forcément été posté(s) dans une zone.")
     *
     * @var Zone|null Zone dans laquelle a(ont) été posté(s) le(s) message(s)
     */
    private ?Zone $zone;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Mois")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mois", referencedColumnName="ordre")
     * })
     *
     * @Assert\NotNull(message="Le(s) message(s) a(ont) forcément été posté(s) à un mois réel de l'année.")
     *
     * @var Mois|null Mois réel du(des) message(s)
     */
    private ?Mois $mois;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Personnage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personnage", referencedColumnName="nom")
     * })
     *
     * @Assert\NotNull(message="Le(s) message(s) a(ont) forcément été posté(s) par un personnage.")
     *
     * @var Personnage|null Auteur du(des) message(s)
     */
    private ?Personnage $auteur;

    public function __construct()
    {
        $this->updatedAt = date_create();
    }

    /**
     * Récupère le nombre de messages postés correspondant au couple (Période / Zone / Mois / Auteur).
     *
     * @return int Nombre de messages postés
     */
    public function getNombre(): int
    {
        return $this->nombre;
    }

    /**
     * Spécifie le nombre de messages postés correspondant au couple (Période / Zone / Mois / Auteur).
     *
     * @param int $nombre Nombre de messages postés
     *
     * @return self Entité
     */
    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Récupère La date de dernière mise à jour de l'enregistrement.
     *
     * @return DateTime|null Date dernière mise à jour
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Récupère la période du Phénix Enchaîné pour laquelle est(sont) défini(s) le(s) message(s).
     *
     * @return Periode|null Période du Phénix Enchaîné active
     */
    public function getPeriode(): ?Periode
    {
        return $this->periode;
    }

    /**
     * Spécifie la période du Phénix Enchaîné pour laquelle est(sont) défini(s) le(s) message(s).
     *
     * @param Periode|null $periode Période du Phénix Enchaîné active
     *
     * @return self Entité
     */
    public function setPeriode(Periode $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Récupère la zone dans laquelle a été posté le(s) message(s).
     *
     * @return Zone|null Zone à Dùralas
     */
    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    /**
     * Spécifie la zone dans laquelle a été posté le(s) message(s).
     *
     * @param Zone|null $zone Zone à Dùralas
     *
     * @return self Entité
     */
    public function setZone(Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Récupère le mois réel de la date d'écriture du(des) message(s).
     *
     * @return Mois|null Mois réel
     */
    public function getMois(): ?Mois
    {
        return $this->mois;
    }

    /**
     * Spécifie le mois réel de la date d'écriture du(des) message(s).
     *
     * @param Mois|null $mois Mois réel
     *
     * @return self Entité
     */
    public function setMois(Mois $mois): self
    {
        $this->mois = $mois;

        return $this;
    }

    /**
     * Récupère l'auteur du(des) message(s).
     *
     * @return Personnage|null Personnage de Dùralas
     */
    public function getAuteur(): ?Personnage
    {
        return $this->auteur;
    }

    /**
     * Spécifie l'auteur du(des) message(s).
     *
     * @param Personnage|null $auteur Personnage de Dùralas
     *
     * @return self Entité
     */
    public function setAuteur(Personnage $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
