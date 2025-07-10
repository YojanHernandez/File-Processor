<?php

declare(strict_types=1);

/**
 * Class CurrencyConverter
 * Handles currency conversion using exchangerate.host API.
 */
class CurrencyConverter
{
    /**
     * Get the exchange rate from one currency to another.
     *
     * @param string $API_KEY Your API key for exchangeratesapi.io
     * @param string $from Source currency code (e.g. 'USD')
     * @param string $to   Target currency code (e.g. 'CAD')
     * @return float
     * @throws \RuntimeException If the API call fails or returns invalid data.
     */
    public function getExchangeRate(string $API_KEY, string $from, string $to): float
    {
        $url = "https://api.exchangeratesapi.io/v1/latest?access_key={$API_KEY}&base={$from}&symbols={$to}";
        $response = @file_get_contents($url);

        if ($response === false) {
            throw new \RuntimeException('Failed to fetch exchange rate.');
        }

        // Decode the JSON response
        $data = json_decode($response, true);

        if (!isset($data['rates'][$to])) {
            throw new \RuntimeException('Invalid response from exchange rate API.');
        }

        return (float) $data['rates'][$to];
    }
}
