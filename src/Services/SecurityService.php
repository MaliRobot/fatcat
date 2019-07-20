<?php


namespace App\Services;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityService
{
    /**
     * SecurityService constructor.
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param AuthenticationUtils $authenticationUtils
     */
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $encoder, AuthenticationUtils $authenticationUtils)
    {
        $this->repository = $userRepository;
        $this->encoder = $encoder;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @param $request
     * @return array
     */
    public function register($request)
    {
        try {
            $content = json_decode($request->getContent(), true);
            $username = $content['username'];
            $password = $content['password'];
            $user = new User();
            $user->setUsername($username);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->encoder->encodePassword($user, $password));
            $this->repository->save($user);
            return ['created' => $user->getUsername()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param $username
     * @return User[]
     */
    public function getUserByUsername($username) {
        return $this->repository->findBy(['username' => $username]);
    }

}