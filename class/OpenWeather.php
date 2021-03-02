<?php
require_once 'CurlException.php';
require_once 'HTTPException.php';
require_once 'UnauthorizedHTTPException.php';

/**
 * Manage OpenWeather API
 * @author ldrt
 */
class OpenWeather {
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Get the current weather forecast
     *
     * @param  string $location City,Country (Country is optional)
     * @return array
     */
    public function getTodayForecast(string $location) : ?array
    {
        $data = $this->callAPI("weather?q={$location}");

        return [
            'temp' => $data['main']['temp'],
            'description' => $data['weather'][0]['description'],
            'date' => new DateTime()
        ];
    }
        
    /**
     *  Get the weather forecast for the 5 next days
     *
     * @param  string $location City,Country (Country is optional)
     * @return array
     */
    public function getForecast(string $location) : ?array
    {
        $data = $this->callAPI("forecast?q={$location}");

        foreach ($data['list'] as $day) {
            $d = new DateTime('@' . $day['dt']);
            $d = strval($d->format('d/m/Y'));
            $result[$d][] = [
                'temp' => $day['main']['temp'],
                'description' => $day['weather'][0]['description'],
                'date' => new DateTime('@' . $day['dt'])
            ];
        }
        return $result;
    }
    
    /**
     * Call the Open Weather Map API
     *
     * @param  string $endpoint action to call (weather, forecast etc.)
     * @return array
     * @throws CurlException  
     * @throws UnauthorizedHTTPException 
     * @throws HTTPException 
     */
    private function callAPI(string $endpoint) : ?array
    {
        // init
        $curl = curl_init("https://api.openweathermap.org/data/2.5/{$endpoint}&units=metric&lang=fr&appid={$this->apiKey}");
        // options
        curl_setopt_array($curl, [
            // CURLOPT_SSL_VERIFYPEER => false,  // deactivate ssl verification: do not use
            CURLOPT_CAINFO => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cert.cer', // fix ssl issue (clean solution)
            CURLOPT_RETURNTRANSFER => true, // save data in var instead of displaying it in browser
            CURLOPT_TIMEOUT => 1 // timeout in sec
            // CURLOPT_TIMEOUT_MS => 10 // to trigger an error
        ]);
        // execute
        $data = curl_exec($curl);
        // cannot contact server
        if ($data === false) {
            throw new CurlException($curl);
        }
        // no data
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode !== 200) {
            curl_close($curl);
            if ($statusCode === 401) {
                $data = json_decode($data, true); 
                throw new UnauthorizedHTTPException($data['message'], $statusCode);
            }
            throw new HTTPException($data, $statusCode);
        } 
        // data retrieved
        curl_close($curl);
        return json_decode($data, true); 
    }
}
?>