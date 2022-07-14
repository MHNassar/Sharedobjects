<?php

namespace Es\Sharedobjects\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BaseController extends AbstractController
{
    protected function getRequestObj(Request $request, string $requestClass): RequestInterface {

        $routParams = $request->attributes->get('_route_params',[]);
        $requestPayload = (json_decode($request->getContent(), true)) ?? [];
        $queryParams =$request->query->all();

        $parameters = array_merge($routParams,$requestPayload,$queryParams);

        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $request = $normalizer->denormalize($parameters, $requestClass);
        $request->validate();
        return $request;
    }

    public function response(ResponseInterface $response) :JsonResponse {
        $encoders = [new JsonEncoder()];
        $responseNormalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $normalizers = [$responseNormalizer];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($response, 'json');
        return $this->json(json_decode($jsonContent, true));
    }
}
