<?php
namespace App\Controller;
use App\Entity\Transfer;
use App\Repository\TransferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class TransferController extends ApiController
{
    /**
    * @Route("/transfers", methods="GET")
    */
    public function index(TransferRepository $transferRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $movies = $transferRepository->transformAll();
        return $this->respond($transfers);
    }
    /**
    * @Route("/transfers", methods="POST")
    */
    public function create(Request $request, TransferRepository $transferRepository, EntityManagerInterface $em)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $request = $this->transformJsonBody($request);
        if (! $request) {
            return $this->respondValidationError('Please provide a valid request!');
        }
        // validate the transfer id
        if (! $request->get('transfer_id')) {
            return $this->respondValidationError('Please provide a transfer Id!');
        }
        // persist the new transfer
        $transfer = new Transfer;
        $transfer->setTransfer_id($request->get('transfer_id'));
        $transfer->setCount(0);
        $em->persist($transfer);
        $em->flush();
        return $this->respondCreated($transferRepository->transform($transfer));
    }
    /**
    * @Route("/transfers/{id}/count", methods="POST")
    */
    public function increaseCount($id, EntityManagerInterface $em, TransferRepository $transferRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $movie = $transferRepository->find($id);
        if (! $transfer) {
            return $this->respondNotFound();
        }
        $movie->setCount($transfer->getCount() + 1);
        $em->persist($transfer);
        $em->flush();
        return $this->respond([
            'count' => $transfer->getCount()
        ]);
    }
}
