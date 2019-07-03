<?php
namespace App\Controller;
use App\Entity\Recipient;
use App\Repository\RecipientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class RecipientController extends ApiController
{
    /**
    * @Route("/recipients", methods="GET")
    */
    public function index(RecipientRepository $recipientRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $movies = $recipientRepository->transformAll();
        return $this->respond($recipients);
    }
    /**
    * @Route("/recipients", methods="POST")
    */
    public function create(Request $request, RecipientRepository $recipientRepository, EntityManagerInterface $em)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $request = $this->transformJsonBody($request);
        if (! $request) {
            return $this->respondValidationError('Please provide a valid request!');
        }
        // validate the Name
        if (! $request->get('Name')) {
            return $this->respondValidationError('Please provide a Name!');
        }
        // persist the new recipient
        $recipient = new Recipient;
        $recipient->setName($request->get('name'));
        $recipient->setCount(0);
        $em->persist($recipient);
        $em->flush();
        return $this->respondCreated($recipientRepository->transform($recipient));
    }
    /**
    * @Route("/recipients/{id}/count", methods="POST")
    */
    public function increaseCount($id, EntityManagerInterface $em, RecipientRepository $recipientRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $recipient = $recipientRepository->find($id);
        if (! $recipient) {
            return $this->respondNotFound();
        }
        $movie->setCount($recipient->getCount() + 1);
        $em->persist($recipient);
        $em->flush();
        return $this->respond([
            'count' => $recipient->getCount()
        ]);
    }
}
