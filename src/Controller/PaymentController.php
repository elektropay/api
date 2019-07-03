<?php
namespace App\Controller;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class PaymentController extends ApiController
{
    /**
    * @Route("/payments", methods="GET")
    */
    public function index(PaymentRepository $paymentRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $payments = $paymentRepository->transformAll();
        return $this->respond($payments);
    }
    /**
    * @Route("/payments", methods="POST")
    */
    public function create(Request $request, PaymentRepository $paymentRepository, EntityManagerInterface $em)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $request = $this->transformJsonBody($request);
        if (! $request) {
            return $this->respondValidationError('Please provide a valid request!');
        }
        // validate the transaction id
        if (! $request->get('transaction_id')) {
            return $this->respondValidationError('Please provide a transaction id!');
        }
        // persist the new payment
        $payment = new Payment;
        $payment->setTransaction_id($request->get('transaction_id'));
        $payment->setCount(0);
        $em->persist($payment);
        $em->flush();
        return $this->respondCreated($paymentRepository->transform($payment));
    }
    /**
    * @Route("/payments/{id}/count", methods="POST")
    */
    public function increaseCount($id, EntityManagerInterface $em, PaymentRepository $paymentRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $movie = $paymentRepository->find($id);
        if (! $payment) {
            return $this->respondNotFound();
        }
        $payment->setCount($payment->getCount() + 1);
        $em->persist($payment);
        $em->flush();
        return $this->respond([
            'count' => $payment->getCount()
        ]);
    }
}
