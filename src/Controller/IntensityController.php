<?php

namespace App\Controller;

use App\Core\FamaResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * Get Carbon Intensity data for current half hour or between from and to datetime
     * @see https://carbon-intensity.github.io/api-definitions/#get-intensity
     * @see https://carbon-intensity.github.io/api-definitions/#get-intensity-from-to
     *
     * @Route("/intensity/{from?}/{to?}", methods={"GET"}, requirements={
     *     "_format":"json",
     *     "from":"\d{4}-\d{2}-\d{2}T\d{2}:\d{2}Z",
     *     "to":"\d{4}-\d{2}-\d{2}T\d{2}:\d{2}Z"
     * })
     * @param Request $request
     * @param string $from - optional param. Start datetime in in ISO8601 format YYYY-MM-DDThh:mmZ e.g. 2017-08-25T12:35Z
     * @param string $to - optional param. End datetime in in ISO8601 format YYYY-MM-DDThh:mmZ e.g. 2017-08-25T12:35Z
     * @return FamaResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getRows(Request $request, $from, $to): FamaResponse
    {
        try {
            $url = self::URL;
            if (!is_null($from)) {
                $url .= '/' . $from;
            }
            if (!is_null($to)) {
                $url .= '/' . $to;
            }
            $response = $this->fetchData($url);
            if ($response['status'] !== 200 && isset($response['data'])) {
                throw new Exception($response['data'], $response['status']);
            }

            $data = [
                'status' => $response['status'],
                'rows' => $response['data']
            ];

            return new FamaResponse($data);

        } catch (Exception $exception) {
            return new FamaResponse($exception);
        }
    }


    /**
     * Get Carbon Intensity data for today or specific date or specific date and period
     * @see https://carbon-intensity.github.io/api-definitions/#get-intensity-date
     * @see https://carbon-intensity.github.io/api-definitions/#get-intensity-date-date
     * @see https://carbon-intensity.github.io/api-definitions/#get-intensity-date-date-period
     *
     * @Route("/intensity/date/{date?}/{period?}", methods={"GET"}, requirements={
     *     "_format":"json",
     *     "date":"\d{4}-\d{2}-\d{2}",
     *     "period":"([1-9]|[1-3][0-9]|4[0-8])"
     * })
     * @param Request $request
     * @param string $date - optional param. Date in YYYY-MM-DD format e.g. 2017-08-25
     * @param int $period - optional param. Half hour settlement period between 1-48 e.g. 42
     * @return FamaResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getRowsByDate(Request $request, $date = null, $period = null): FamaResponse
    {
        try {
            $url = self::URL . '/date';
            if (!is_null($date)) {
                $url .= '/' . $date;
            }
            if (!is_null($period)) {
                $url .= '/' . $period;
            }
            $response = $this->fetchData($url);
            if ($response['status'] !== 200 && isset($response['data'])) {
                throw new Exception($response['data'], $response['status']);
            }

            $data = [
                'status' => $response['status'],
                'rows' => $response['data']
            ];

            return new FamaResponse($data);

        } catch (Exception $exception) {
            return new FamaResponse($exception);
        }
    }


    /**
     * Get Carbon Intensity factors for each fuel type
     * @see https://carbon-intensity.github.io/api-definitions/#get-intensity-factors
     *
     * @Route("/intensity/factors", methods={"GET"}, requirements={"_format":"json"})
     * @return FamaResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFactors(): FamaResponse
    {
        try {
            $response = $this->fetchData(self::URL . '/factors');
            if ($response['status'] !== 200 && isset($response['data'])) {
                throw new Exception($response['data'], $response['status']);
            }

            $rows = [];
            foreach ($response['data'] as $data) {
                foreach ($data as $key => $value) {
                    $rows[] = [
                        'name' => $key,
                        'value' => $value
                    ];
                }
            }

            $data = [
                'status' => $response['status'],
                'rows' => $rows
            ];

            return new FamaResponse($data);

        } catch
        (Exception $exception) {
            return new FamaResponse($exception);
        }
    }


    /**
     * @param string $url
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function fetchData($url): array
    {
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $data = $response->toArray();

        return [
            'status' => $response->getStatusCode(),
            'data' => $response->getStatusCode() === 200 ? $data['data'] : $data['error']['code']
        ];
    }
}
