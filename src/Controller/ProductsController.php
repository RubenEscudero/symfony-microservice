<?php

namespace App\Controller;

use App\DTO\LowestPriceEnquiry;
use App\Entity\Promotion;
use App\Filter\PromotionsFilterInterface;
use App\Repository\ProductRepository;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductsController extends AbstractController
{

    public function __construct(
        private ProductRepository $repository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/products/{id}/lowest-price', name: 'lowest-price', methods: 'POST')]
    public function lowestPrice(
        Request $request,
        int $id,
        DTOSerializer $serializer,
        PromotionsFilterInterface $promotionsFilter): Response
    {
        if ($request->headers->has('force-fail')) {
            return new JsonResponse(
                ['error' => 'Promotions Engine failure message'],
                $request->headers->get('force-fail')
            );
        }

        // 1. Deserialize json data into a EnquiryDTO
        /** @var LowestPriceEnquiry $lowestPriceEnquiry */
        $lowestPriceEnquiry = $serializer->deserialize(
            $request->getContent(), LowestPriceEnquiry::class, 'json'
        );

        // 2. Pass the Enquiry into a promotion filter
            // the appropriate promotion will be applied

        $product = $this->repository->find($id); // Add error handling for not found product

        $lowestPriceEnquiry->setProduct($product);

        $promotions = $this->entityManager->getRepository(Promotion::class)->findValidForProduct(
            $product,
            date_create_immutable($lowestPriceEnquiry->getRequestDate())
        );

        $modifiedEnquiry = $promotionsFilter->apply($lowestPriceEnquiry, $promotions);

        // 3. Return the modified Enquiry
        $responseContent = $serializer->serialize($modifiedEnquiry, 'json');

        return new Response($responseContent, 200);
    }

    #[Route('/products/{id}/promotions', name: 'promotion', methods: 'GET')]
    public function promotions()
    {

    }
}