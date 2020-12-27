<?php

namespace App\Form;

use App\Entity\Faction;
use App\Entity\Personnage;
use App\Entity\Region;
use App\Model\RecenseFilters;
use App\Repository\PersonnageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\CacheInterface;

class RecenseFilterType extends AbstractType
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        $this->em    = $em;
        $this->cache = $cache;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $region      = $options['region'];
        $faction     = $options['faction'];
        $personnages = $options['personnages'];

        $builder
            ->add('region', EntityType::class, array(
                'class'    => Region::class,
                'data'     => $region ? $this->em->getRepository(Region::class)->find($region) : null,
                'required' => false,
            ))
            ->add('faction', EntityType::class, array(
                'class'    => Faction::class,
                'data'     => $faction ? $this->em->getRepository(Faction::class)->find($faction) : null,
                'required' => false,
            ))
            ->add('personnages', EntityType::class, array(
                'class'    => Personnage::class,
                'data'     => !empty($personnages) ? $this->em->getRepository(Personnage::class)->findByList($personnages) : null,
                'group_by' => static function (Personnage $personnage) {
                    return $personnage->isActif() ? 'Actif' : 'Inactif';
                },
                'multiple'      => true,
                'query_builder' => static function (PersonnageRepository $repo) {
                    return $repo->getQBBase();
                },
                'required' => false,
            ))
        ;

        $appCache = $this->cache;
        // Mise en cache
        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (PostSubmitEvent $event) use ($appCache) {
            /** @var RecenseFilters $filters */
            $filters = $event->getData();

            $cachedRegion = $appCache->getItem('recensement_filters_region');
            $cachedRegion->set($filters->region);
            $appCache->save($cachedRegion);

            $cachedFaction = $appCache->getItem('recensement_filters_faction');
            $cachedFaction->set($filters->faction);
            $appCache->save($cachedFaction);

            $cachedPersonnages = $appCache->getItem('recensement_filters_personnages');
            $cachedPersonnages->set($filters->personnages);
            $appCache->save($cachedPersonnages);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'  => RecenseFilters::class,
            'region'      => $this->cache->getItem('recensement_filters_region')->get(),
            'faction'     => $this->cache->getItem('recensement_filters_faction')->get(),
            'personnages' => $this->cache->getItem('recensement_filters_personnages')->get(),
        ));
        $resolver->setAllowedTypes('region', array(Region::class, 'null'));
        $resolver->setAllowedTypes('faction', array(Faction::class, 'null'));
    }
}
