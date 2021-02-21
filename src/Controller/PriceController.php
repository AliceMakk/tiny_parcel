<?php

namespace App\Controller;

use App\Repository\ParcelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PriceController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class PriceController extends AbstractController
{
    const MAX_PARCELS_TO_GET= 12;

    private $parcelRepository;

    public function __construct(ParcelRepository $parcelRepository)
    {
        $this->parcelRepository = $parcelRepository;
    }

    /**
     * @Route("/prices/", name="get_prices", methods={"GET"})
     */
    public function getAll(Request $request): JsonResponse
    {
        $parcelIds = $request->query->get('parcelIds');
        if (!$parcelIds) {
            return new JsonResponse([
                'error' => 'Please provide parcel ids for getting their rates.'
            ], 400);
        }

        $ids = explode(',', $parcelIds);
        if (count($ids) > self::MAX_PARCELS_TO_GET) {
            return new JsonResponse([
                'error' => 'Unfortunately we cannot look for more than ' . self::MAX_PARCELS_TO_GET . ' parcels for you.'
            ], 400);
        }

        $parcels = $this->parcelRepository->findBy(['id' => $ids]);

        if(count($parcels) === 0) {
            return new JsonResponse([
                'error' => 'Unfortunately no parcels with provided ids were found.'
            ], 400);
        }
        $prices = [];

        foreach ($parcels as $parcel) {
            $prices[] = [
                'parcelId' => $parcel->getId(),
                'quote' => '$' . $parcel->getQuote(),
            ];
        }

        return new JsonResponse($prices, 200);
    }
}
