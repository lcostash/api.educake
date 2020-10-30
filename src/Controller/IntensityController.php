<?php

namespace App\Controller;

use App\Core\Enum\ConstraintEnum;
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
     * Get Carbon Intensity data for current half hour
     * @Route("/intensity", methods={"GET"}, requirements={"_format":"json"})
     * @return FamaResponse
     * @throws Exception
     */
    public function getRows(): FamaResponse
    {
        try {
            $response = $this->httpClient->request('GET', self::URL);
            $data = $response->toArray();

            if ($response->getStatusCode() !== 200 && isset($data['error'])) {
                throw new Exception($data['error']['code'], $response->getStatusCode());
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


    /**
     * Get Carbon Intensity data for current half hour
     * @Route("/intensity/date/{date?}/{period?}", methods={"GET"}, requirements={
     *     "_format":"json",
     *     "date":"\d{4}-\d{2}-\d{2}",
     *     "period":"([1-9]|[1-3][0-9]|4[0-8])"
     * })
     * @param Request $request
     * @param string $date
     * @param int $period
     * @return FamaResponse
     * @throws Exception
     */
    public function getRowsByDate(Request $request, $date = null, $period = null): FamaResponse
    {
        try {
            $url = '/date';
            if (!is_null($date)) {
                $url .= '/' . $date;
            }
            if (!is_null($period)) {
                $url .= '/' . $period;
            }
            $response = $this->httpClient->request('GET', self::URL . $url);
            $data = $response->toArray();

            if ($response->getStatusCode() !== 200 && isset($data['error'])) {
                throw new Exception($data['error']['code'], $response->getStatusCode());
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
