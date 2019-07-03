<?php
namespace App\Controller;
use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class InvoiceController extends ApiController
{
    /**
    * @Route("/invoices", methods="GET")
    */
    public function index(InvoiceRepository $invoiceRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $invoices = $invoiceRepository->transformAll();
        return $this->respond($invoices);
    }
    /**
    * @Route("/invoices", methods="POST")
    */
    public function create(Request $request, InvoiceRepository $invoiceRepository, EntityManagerInterface $em)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $request = $this->transformJsonBody($request);
        if (! $request) {
            return $this->respondValidationError('Please provide a valid request!');
        }
        // validate the title
        if (! $request->get('title')) {
            return $this->respondValidationError('Please provide a title!');
        }
        // persist the new invoice
        $movie = new Invoice;
        $movie->setTitle($request->get('title'));
        $movie->setCount(0);
        $em->persist($invoice);
        $em->flush();
        return $this->respondCreated($invoiceRepository->transform($invoice));
    }
    /**
    * @Route("/invoices/{id}/count", methods="POST")
    */
    public function increaseCount($id, EntityManagerInterface $em, InvoiceRepository $invoiceRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $movie = $invoiceRepository->find($id);
        if (! $invoice) {
            return $this->respondNotFound();
        }
        $movie->setCount($invoice->getCount() + 1);
        $em->persist($invoice);
        $em->flush();
        return $this->respond([
            'count' => $invoice->getCount()
        ]);
    }
}
