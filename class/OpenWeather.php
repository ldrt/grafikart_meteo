<?php
class OpenWeather {
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getTodayForecast(string $location) : ?array
    {
        $data = $this->callAPI("weather?q={$location}");
        return [
            'temp' => $data['main']['temp'],
            'description' => $data['weather'][0]['description'],
            'date' => new DateTime()
        ];
    }
    
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
        ]);
        // execute
        $data = curl_exec($curl);
        // cannot contact server or no data retrieved
        if ($data === false || curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
            return null;
        } else {
            $result = [];
            // data retrieved
            return $data = json_decode($data, true);
        }
    }
}
?>