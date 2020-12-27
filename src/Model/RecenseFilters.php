<?php


namespace App\Model;

use App\Entity\Personnage;
use App\Entity\Region;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class RecenseFilters
 *
 * @package App\Model
 */
class RecenseFilters
{
    public ?Region $region = null;

    /**
     * @var Personnage[]|Collection
     */
    public $personnages;

    /**
     * RecenseFilters constructor.
     */
    public function __construct()
    {
        $this->personnages = new ArrayCollection();
    }

}
