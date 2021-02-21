<?php

namespace App\Controller;

use App\Entity\Parcel;
use App\Entity\Rate;
use App\Repository\ParcelRepository;
use App\Repository\RateRepository;
use App\Service\PriceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Psr\Log\LoggerInterface;

/**
 * Class ParcelController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class ParcelController extends AbstractController
{
    /**
     * @var ParcelRepository
     */
    private $parcelRepository;

    /**
     * @var RateRepository
     */
    private $rateRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(RateRepository $rateRepository, ParcelRepository $parcelRepository, LoggerInterface $logger)
    {
        $this->parcelRepository = $parcelRepository;
        $this->rateRepository = $rateRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/parcels/", name="create_parcel", methods={"POST"})
     */
    public function create(PriceCalculator $priceCalculator, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $map = $this->mapValues($data);

        if (!$map['weight'] && !$map['volume'] && !$map['declared_value']) {
            return new JsonResponse([
                'error' => 'Measurements of the parcel must be provided either weight, volume or parcel declared value.'
            ], 400);
        }

        $result = $priceCalculator->getMaxRate($this->rateRepository, $map['weight'], $map['volume'], $map['declared_value']);

        if (!$result['rateEntity'] instanceof Rate) {
            $this->logger->error('Error: Max price did not get calculated with values' . print_r($map) . __FILE__ . ' line:' . __LINE__);
            return new JsonResponse([
                'error' => 'Ooops. Something went wrong, please try again later, we are already working on the issue.'
            ], 500);
        }

        $parcelId = null;
        try {
            $parcelId = $this->parcelRepository->saveParcel(array_merge($map, $result));
        } catch (\Exception $e) {
            $this->logger->error('Error: ' . $e->getMessage() . ' with values' . print_r(array_merge($map, $result)) . __FILE__ . ' line:' . __LINE__);
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'Parcel saved!', 'id' => $parcelId], 200);
    }

    /**
     * @Route("/parcels/{id}", name="get_parcel", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getOne(int $id): JsonResponse
    {
        $parcel = $this->parcelRepository->findOneBy(['id' => $id]);
        return new JsonResponse($parcel ? $parcel->toArray() : [], 200);
    }

    /**
     * @Route("/parcels/{id}", name="update_parcel", methods={"PUT"})
     * @param $id
     * @return JsonResponse
     */
    public function update($id, PriceCalculator $priceCalculator, Request $request): JsonResponse
    {
        $parcel = $this->parcelRepository->findOneBy(['id' => $id]);
        if (!$parcel instanceof Parcel) {
            return new JsonResponse([
                'error' => 'Parcel for update is not found.'
            ], 400);
        }

        $data = json_decode($request->getContent(), true);
        $shouldUpdate = false;

        $map = $this->mapValues($data, [
            'name' => $parcel->getName(),
            'weight' => $parcel->getWeight(),
            'declared_value' => $parcel->getDeclaredValue(),
            'volume' => $parcel->getVolume()
        ]);

        $parcelAsArray = $parcel->toArray();
        /**
         * Checking whether the existing parcel needs updating by comparing the current values
         */
        foreach ($map as $field => $value) {
            $value = $field !== 'name' ? (double)$value : $value;
            if ($parcelAsArray[$field] && $parcelAsArray[$field] != $value) {
                $shouldUpdate = true;
            }
            if ($shouldUpdate) {
                break;
            }
        }

        if (!$shouldUpdate) {
            return new JsonResponse(['status' => 'Apparently nothing to update'], 200);
        }

        if (!$map['weight'] && !$map['volume'] && !$map['declared_value']) {
            return new JsonResponse([
                'error' => 'Measurements of the parcel must be provided either weight, volume or parcel declared value.'
            ], 400);
        }

        $result = $priceCalculator->getMaxRate($this->rateRepository, $map['weight'], $map['volume'], $map['declared_value']);

        if (!$result['rateEntity'] instanceof Rate) {
            $this->logger->error('Error: Max price did not get calculated with values' . print_r($map) . __FILE__ . ' line:' . __LINE__);
            return new JsonResponse([
                'error' => 'Ooops. Something went wrong, please try again later, we are already working on the issue' . $result['maxPrice']
            ], 500);
        }

        $parcelId = null;

        try {
            $parcelId = $this->parcelRepository->updateParcel($parcel, array_merge($map, $result));
        } catch (\Exception $e) {
            $this->logger->error('Error: ' . $e->getMessage() . ' with values' . print_r(array_merge($map, $result)) . __FILE__ . ' line:' . __LINE__);
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'Parcel updated!', 'id' => $parcelId], 200);
    }

    /**
     * @Route("/parcels/{id}", name="delete_parcel", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        try {
            $this->parcelRepository->deleteParcel($id);
        } catch (\Exception $e) {
            $this->logger->error('Error: ' . $e->getMessage() . ' with values' . print_r([$id]) . __FILE__ . ' line:' . __LINE__);
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse(['status' => 'Parcel deleted!'], 200);
    }

    private function mapValues($data = [], $map = [])
    {
        if (count($map) === 0) {
            $map = [
                'name' => null,
                'weight' => null,
                'declared_value' => null,
                'volume' => null
            ];
        }
        foreach ($map as $field => $val) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $map[$field] = $field !== 'name' ? (double)$data[$field] : (string)$data[$field];
            }
        }

        return $map;
    }
}
