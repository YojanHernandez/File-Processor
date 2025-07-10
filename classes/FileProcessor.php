<?php

declare(strict_types=1);


/**
 * Class FileProcessor
 * Processes CSV files for product and profit data.
 */
class FileProcessor
{
    /** @var string */
    private string $filepath;
    /** @var array */
    private array $headers = [];
    /** @var array */
    private array $data = [];

    /**
     * FileProcessor constructor.
     * @param string $filepath
     */
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * Process the CSV file and return an array of product data.
     *
     * @return array
     * @throws \RuntimeException If the file cannot be opened or required columns are missing.
     */
    public function process(): array
    {
        $handle = fopen($this->filepath, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Unable to open file: ' . $this->filepath);
        }
        $this->headers = fgetcsv($handle);

        if (!$this->headers) {
            fclose($handle);
            throw new \RuntimeException('CSV file is empty or invalid.');
        }

        $mapped = array_map('strtolower', $this->headers);
        $indexes = [
            'sku' => array_search('sku', $mapped),
            'cost' => array_search('cost', $mapped),
            'price' => array_search('price', $mapped),
            'qty' => array_search('qty', $mapped),
        ];

        foreach ($indexes as $key => $index) {
            if ($index === false) {
                fclose($handle);
                throw new \RuntimeException("Missing required column: $key");
            }
        }

        $row = fgetcsv($handle);

        while ($row !== false) {
            $cost = (float)$row[$indexes['cost']];
            $price = (float)$row[$indexes['price']];
            $qty = (int)$row[$indexes['qty']];
            $profit = $price - $cost;
            $totalProfit = $profit * $qty;
            $margin = $price != 0 ? ($profit / $price) * 100 : 0;
            $this->data[] = [
                'sku' => $row[$indexes['sku']],
                'cost' => $cost,
                'price' => $price,
                'qty' => $qty,
                'margin' => $margin,
                'profit' => $totalProfit
            ];

            // Read the next row
            $row = fgetcsv($handle);
        }

        fclose($handle);

        return $this->data;
    }
}
