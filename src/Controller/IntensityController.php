<?php

namespace App\Controller;

use App\Core\FamaResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Exception;


class IntensityController extends MainController
{
    const URL = 'https://api.carbonintensity.org.uk/intensity';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * FindController constructor.
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        parent::__construct();
    }


    /**
     * @Route("/intensity", methods={"GET"}, requirements={"_format":"json"})
     * @return FamaResponse
     * @throws Exception
     */
    public function getIntensity(): FamaResponse
    {
        try {
            $response = $this->httpClient->request('GET', IntensityController::URL);
            $data = $response->toArray();

            if (isset($data['error'])) {
                throw new Exception($data['error']['code'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $data = [
                'status' => Response::HTTP_OK,
                'rows' => isset($data['data']) ? $data['data'] : []
            ];

            return new FamaResponse($data);

        } catch (Exception $exception) {
            return new FamaResponse($exception);
        } catch (TransportExceptionInterface $exception) {
            return new FamaResponse($exception);
        } catch (ClientExceptionInterface $exception) {
            return new FamaResponse($exception);
        } catch (DecodingExceptionInterface $exception) {
            return new FamaResponse($exception);
        } catch (RedirectionExceptionInterface $exception) {
            return new FamaResponse($exception);
        } catch (ServerExceptionInterface $exception) {
            return new FamaResponse($exception);
        }
    }
}
