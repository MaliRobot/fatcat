<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\SecurityService;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

class UserController extends AbstractController
{
    /**
    * @SWG\Post(
    *     @SWG\Schema(
    *         type="array",
    *         @Model(type=App\Entity\User::class)
    *     ),
    *     @SWG\Parameter(
    *         name="request",
    *         description="Register as a user",
    *         required=true,
    *         in="body",
    *         type="object",
    *     @SWG\Property(property="username", type="string"),
    *     @SWG\Property(property="password", type="string"),
    *     ),
    *     @SWG\Response(
    *         response="200",
    *             description="Returned when successful",
    *             @Model(type=App\Entity\User::class)
    *         ),
    *     @SWG\Response(response="400",description="Returned when the data is missing or data is not correct"),
    *     @SWG\Response(response="500",description="Returned when server side error occurred")
    * )
    */
    public function register(Request $request, SecurityService $securityService)
    {
        return new JsonResponse($securityService->register($request));
    }

}