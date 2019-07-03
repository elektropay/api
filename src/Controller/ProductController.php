<?php

namespace App\Controller;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ProductController extends ApiController
{

    /**
    * @Route("/products", methods="GET")
    */
    public function index(ProductRepository $productRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }

        $products = $productRepository->transformAll();
        return $this->respond($products);
    }

    /**
    * @Route("/products", methods="POST")
    */
    public function create(Request $request, ProductRepository $productRepository, EntityManagerInterface $em)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }

        $request = $this->transformJsonBody($request);
        if (! $request) {
            return $this->respondValidationError('Please provide a valid request!');
        }


        // validate the name
        if (! $request->get(’name’)) {
            return $this->respondValidationError('Please provide a product name!’);
        }

        // persist the new product
        $product = new product;
        $product->setName($request->get(‘name’));
        $product->setCount(0);
        $em->persist($product);
        $em->flush();

        return $this->respondCreated($productRepository->transform($product));
    }

    /**
    * @Route("/products/{id}/count", methods="POST")
    */
    
    public function increaseCount($id, EntityManagerInterface $em, ProductRepository $productRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }

        $product = $productRepository->find($id);
        if (! $product) {
            return $this->respondNotFound();
        }

        $product->setCount($product->getCount() + 1);
        $em->persist($product);
        $em->flush();

        return $this->respond([
            'count' => $product->getCount()
        ]);
    }

}
