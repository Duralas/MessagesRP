<?php

namespace App\Form;

use App\Entity\Personnage;
use App\Entity\Region;
use App\Model\RecenseFilters;
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
        $personnages = $options['personnages'];

        $builder
            ->add('region', EntityType::class, array(
                'class'    => Region::class,
                'data'     => $region ? $this->em->getRepository(Region::class)->find($region) : null,
                'required' => false,
            ))
            ->add('personnages', EntityType::class, array(
                'class'    => Personnage::class,
                'data'     => !empty($personnages) ? $this->em->getRepository(Personnage::class)->findByList($personnages) : null,
                'group_by' => static function (Personnage $personnage) {
                    return $personnage->isActif() ? 'Actif' : 'Inactif';
                },
                'multiple' => true,
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
            'personnages' => $this->cache->getItem('recensement_filters_personnages')->get(),
        ));
        $resolver->setAllowedTypes('region', array(Region::class, 'null'));
    }
}
