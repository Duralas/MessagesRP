<?php

declare(strict_types=1);

namespace App\Controller\Census\Mass\Seizure;

use App\{
    Entity\Message,
    Entity\Mois,
    Model\CensusMonthSeizureModel,
    Model\MassSeizureCensusModel,
    Model\SeizureCensusModel,
    Repository\MoisRepository,
    Repository\PeriodeRepository,
    Repository\PersonnageRepository,
    Repository\ZoneRepository
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recensement/masse/saisie', name: 'app_census_mass_seizure_post', methods: ['POST'])]
final class PostController extends AbstractController
{
    /**
     * @param PeriodeRepository $periodeRepository
     * @param ZoneRepository $zoneRepository
     * @param PersonnageRepository $personnageRepository
     * @param MoisRepository $moisRepository
     * @param EntityManagerInterface $entityManager
     * @param array<string, Mois> $monthList
     * @param array<string> $personnageAllowedValues
     */
    public function __construct(
        private PeriodeRepository $periodeRepository,
        private ZoneRepository $zoneRepository,
        private PersonnageRepository $personnageRepository,
        private MoisRepository $moisRepository,
        private EntityManagerInterface $entityManager,
        private array $monthList = [],
        private array $personnageAllowedValues = [],
    ) {}

    public function __invoke(Request $request): Response
    {
        $massSeizureCensus = $this->getAssertedSeizureData($request->request->all('mass_seizure'));

        $willInsert = false;
        foreach ($massSeizureCensus->getSeizureCensusList() as $seizureCensus) {
            foreach ($seizureCensus->getCensusMonthSeizures() as $censusMonthSeizure) {
                if ($censusMonthSeizure->getCount() <= 0) {
                    continue;
                }

                $this->entityManager->persist(
                    (new Message())
                        ->setPeriode($massSeizureCensus->getPeriode())
                        ->setZone($massSeizureCensus->getZone())
                        ->setAuteur($seizureCensus->getPersonnage())
                        ->setMois($censusMonthSeizure->getMonth())
                        ->setNombre($censusMonthSeizure->getCount())
                );
                $willInsert = true;
            }
        }
        if ($willInsert) {
            $this->entityManager->flush();
        }

        return $this->render('index.html.twig');
    }

    /**
     * @param array<mixed> $seizureData
     */
    private function getAssertedSeizureData(array $seizureData): MassSeizureCensusModel
    {
        $massSeizureData = $this->resolveMassSeizure($seizureData);

        $seizureCensusArray = [];
        foreach ($massSeizureData['seizureCensusList'] as $seizureCensusItem) {
            $seizureCensusArray[] = $this->getAssertedSeizureCensus((array) $seizureCensusItem);
        }

        return (new MassSeizureCensusModel())
            ->setPeriode($this->periodeRepository->find($massSeizureData['periode']))
            ->setZone($this->zoneRepository->find($massSeizureData['zone']))
            ->setSeizureCensusList($seizureCensusArray);
    }

    /**
     * @param array<mixed> $seizureCensusItemData
     *
     * @return SeizureCensusModel
     */
    private function getAssertedSeizureCensus(array $seizureCensusItemData): SeizureCensusModel
    {
        $seizureCensusData = $this->resolveSeizureCensus($seizureCensusItemData);

        $censusMonthArray = [];
        foreach ($seizureCensusData['censusMonthSeizures'] as $censusMonthSeizureItem) {
            $censusMonthArray[] = $this->getAssertedCensusMonthSeizure((array) $censusMonthSeizureItem);
        }

        return (new SeizureCensusModel())
            ->setPersonnage($this->personnageRepository->find($seizureCensusData['personnage']))
            ->setCensusMonthSeizures($censusMonthArray);
    }

    private function getAssertedCensusMonthSeizure(array $censusMonthItemData): CensusMonthSeizureModel
    {
        $censusMonthData = $this->resolveCensusMonth($censusMonthItemData);

        return (new CensusMonthSeizureModel())
            ->setMonth($this->getMonth($censusMonthData['month']))
            ->setCount((int) $censusMonthData['count']);
    }

    /**
     * @param array<mixed> $seizureData
     *
     * @return array{periode: string, zone: string, seizureCensusList: array<mixed>}
     */
    private function resolveMassSeizure(array $seizureData): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->define('periode')
            ->required()
            ->allowedValues(...$this->periodeRepository->getAllActiveIds());
        $resolver
            ->define('zone')
            ->required()
            ->allowedValues(...$this->zoneRepository->getAllIds());
        $resolver
            ->define('seizureCensusList')
            ->required()
            ->allowedTypes('array');
        $resolver->define('_token');

        return $resolver->resolve($seizureData);
    }

    /**
     * @param array<mixed> $seizureCensusData
     *
     * @return array{personnage: string, censusMonthSeizures: array<mixed>}
     */
    private function resolveSeizureCensus(array $seizureCensusData): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->define('personnage')
            ->required()
            ->allowedValues(...$this->getPersonnageAllowedValues());
        $resolver
            ->define('censusMonthSeizures')
            ->required()
            ->allowedTypes('array');

        return $resolver->resolve($seizureCensusData);
    }

    /**
     * @param array<mixed> $censusMonthData
     *
     * @return array{month: string, count: string}
     */
    private function resolveCensusMonth(array $censusMonthData): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->define('month')
            ->required()
            ->allowedValues(
                ...array_map(
                    static fn (int $monthId): string => (string) $monthId,
                    $this->moisRepository->getAllIds()
                )
            );
        $resolver
            ->define('count')
            ->required()
            ->allowedTypes('numeric');

        return $resolver->resolve($censusMonthData);
    }

    private function getMonth(string $monthOrder): Mois
    {
        if (array_key_exists($monthOrder, $this->monthList) === false) {
            $this->monthList[$monthOrder] = $this->moisRepository->findByOrder((int) $monthOrder);
        }

        return $this->monthList[$monthOrder];
    }

    /**
     * @return array<string>
     */
    private function getPersonnageAllowedValues(): array
    {
        if (count($this->personnageAllowedValues) === 0) {
            $this->personnageAllowedValues = $this->personnageRepository->getAllActiveIds();
        }

        return $this->personnageAllowedValues;
    }
}
