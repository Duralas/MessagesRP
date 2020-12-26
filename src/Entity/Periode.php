<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité pour la table "periodes" listant les périodes couvertes par la gazette du Phénix Enchaîné.
 *
 * @ORM\Table(name="periodes")
 * @ORM\Entity(repositoryClass="App\Repository\PeriodeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Periode
{
    /**
     * @ORM\Column(name="code", type="string", length=10, nullable=false, options={"comment"="Code d'identification de la période"})
     * @ORM\Id
     *
     * @Assert\NotBlank(message="La période doit pouvoir être identifiée à partir d'un code.")
     *
     * @var string|null Code identification
     */
    private ?string $code;

    /**
     * @ORM\Column(name="nom", type="string", length=50, nullable=false, options={"comment"="Nom de la période"})
     *
     * @Assert\NotBlank(message="La période doit être nommée.")
     *
     * @var string|null Nom de la période
     */
    private ?string $nom;

    /**
     * @ORM\Column(name="date_debut", type="datetime", nullable=false, options={"comment"="Début de la période"})
     *
     * @Assert\NotBlank(message="Une date de début doit être spécifiée.")
     * @Assert\LessThan(message="La date de départ « {{ value }} » ne doit pas excéder la date de fin « {{ compared_value }} ».", propertyPath="endDate")
     *
     * @var DateTime|null Date de début de période
     */
    private ?DateTime $startDate;

    /**
     * @ORM\Column(name="date_fin", type="datetime", nullable=false, options={"comment"="Fin de la période"})
     * @Assert\NotBlank(message="Une date de fin doit être spécifiée.")
     *
     * @var DateTime|null Date de fin de période
     */
    private ?DateTime $endDate;

    /**
     * @var bool [Champ fonctionnel] Indique si la période est active ou non
     */
    private bool $active = false;

    /**
     * Spécifie l'état "actif" ou "non actif" de la période selon les dates renseignées et la date du jour.
     *
     * Si la date du jour est entre la date de début et la date de fin, alors la période est active.
     */
    public function setActiveOnPostLoad(): void
    {
        if (!$this->startDate || !$this->endDate) {
            return;
        }

        $today        = date_create();
        $this->active = $today < $this->endDate && $today >= $this->startDate;
    }

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
     * Spécifie le code d'identification de l'entité.
     *
     * @param string $code Code identification
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * Récupère le nom de la période.
     *
     * @return string|null Nom de la période
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Spécifie le nom de la période.
     *
     * @param string $nom Nom de la période
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * Récupère la date de début de la période.
     *
     * @return DateTime|null Date de début de période
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * Spécifie la date de début de la période.
     *
     * @param DateTime $startDate Date de début de période
     */
    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * Récupère la date de fin de la période.
     *
     * @return DateTime|null Date de fin de période
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * Spécifie la date de fin de la période.
     *
     * @param DateTime $endDate Date de fin de période
     */
    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * Indique si la période est "active" ou non.
     *
     * @return bool True => Active / False => Non active
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    public function __toString()
    {
        return $this->nom ?: 'Inconnu';
    }
}
