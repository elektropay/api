<?php
namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class UserController extends ApiController
{
    /**
    * @Route("/users", methods="GET")
    */
    public function index(UserRepository $userRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $users = $userRepository->transformAll();
        return $this->respond($users);
    }
    /**
    * @Route("/users", methods="POST")
    */
    public function create(Request $request, UserRepository $userRepository, EntityManagerInterface $em)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $request = $this->transformJsonBody($request);
        if (! $request) {
            return $this->respondValidationError('Please provide a valid request!');
        }
        // validate the id
        if (! $request->get('id')) {
            return $this->respondValidationError('Please provide a username!');
        }
        // persist the new user
        $user = new User;
        $user->setId($request->get('username'));
        $user->setCount(0);
        $em->persist($user);
        $em->flush();
        return $this->respondCreated($userRepository->transform($user));
    }
    /**
    * @Route("/users/{id}/count", methods="POST")
    */
    public function increaseCount($id, EntityManagerInterface $em, UserRepository $userRepository)
    {
        if (! $this->isAuthorized()) {
            return $this->respondUnauthorized();
        }
        $movie = $userRepository->find($username);
        if (! $user) {
            return $this->respondNotFound();
        }
        $movie->setCount($user->getCount() + 1);
        $em->persist($user);
        $em->flush();
        return $this->respond([
            'count' => $user->getCount()
        ]);
    }
}
