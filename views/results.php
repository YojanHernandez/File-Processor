<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../classes/FileProcessor.php';
require_once '../classes/CurrencyConverter.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$error = null;
$rows = [];
$cadRate = 1.0;
$API_KEY = $_ENV['API_KEY'] ?? '';

$base_currency = 'EUR'; // The Free plan only support EUR as the base currency
$target_currency = 'CAD';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        $processor = new FileProcessor($_FILES['csv_file']['tmp_name']);
        $rows = $processor->process();
        $converter = new CurrencyConverter();
        $cadRate = $converter->getExchangeRate($API_KEY, $base_currency, $target_currency);
    }
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>CSV Results</title>
    <link rel="icon" href="../public/images/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/css/style.css">
</head>

<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center">
    <div class="container py-4">

        <div class="card shadow-sm border-0 p-3">
            <div class="card-body">
                <?php if ($rows): ?>
                    <h1 class="card-title text-center mb-4">Results</h1>

                    <div class="table-responsive rounded">
                        <table class="table table-bordered table-striped text-center align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Cost</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Profit Margin (%)</th>
                                    <th>Total Profit (<?= $base_currency; ?>)</th>
                                    <th>Total Profit (<?= $target_currency; ?>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sumQty = $sumCost = $sumPrice = $sumMargin = $sumUSD = 0;
                                $rowCount = count($rows);
                                foreach ($rows as $row):
                                    $cadProfit = $row['profit'] * $cadRate;
                                    $sumQty += $row['qty'];
                                    $sumCost += $row['cost'];
                                    $sumPrice += $row['price'];
                                    $sumMargin += $row['margin'];
                                    $sumUSD += $row['profit'];
                                    $colorClass = fn($v) => $v < 0 ? 'text-danger' : 'text-success';
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['sku']) ?></td>
                                        <td>$<?= number_format($row['cost'], 2) ?></td>
                                        <td>$<?= number_format($row['price'], 2) ?></td>
                                        <td class="<?= $colorClass($row['qty']) ?>"><?= number_format($row['qty']) ?></td>
                                        <td class="<?= $colorClass($row['margin']) ?>"><?= number_format($row['margin'], 2) ?>%</td>
                                        <td class="<?= $colorClass($row['profit']) ?>">$<?= number_format($row['profit'], 2) ?></td>
                                        <td class="<?= $colorClass($cadProfit) ?>">$<?= number_format($cadProfit, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td><strong>Totals</strong></td>
                                    <td>Average: $<?= $rowCount > 0 ? number_format($sumCost / $rowCount, 2) : '0.00' ?></td>
                                    <td>Average: $<?= $rowCount > 0 ? number_format($sumPrice / $rowCount, 2) : '0.00' ?></td>
                                    <td><?= $sumQty ?></td>
                                    <td><?= $rowCount > 0 ? number_format($sumMargin / $rowCount, 2) : '0.00' ?>%</td>
                                    <td>$<?= number_format($sumUSD, 2) ?></td>
                                    <td>$<?= number_format($sumUSD * $cadRate, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="mt-4 text-center">
                    <?php if (!$rows): ?>
                        <h3 class="card-title text-center mb-4">No data available. Please upload a valid CSV file.</h3>
                    <?php endif; ?>

                    <a href="../index.php" class="btn btn-dark">Back to Upload</a>
                </div>
            </div>

        </div>

        <?php if ($error) : ?>
            <div class="alert alert-danger mt-5"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>


</body>

</html>