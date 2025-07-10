<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV</title>

    <link rel="icon" href="./public/images/favicon.png" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="./public/css/style.css" rel="stylesheet">
</head>

<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-3">
                    <div class="card-body">
                        <img src="../public/images/favicon.png" alt="Logo" class="d-block mx-auto mb-4" style="max-width: 150px;">
                        <h1 class="card-title text-center mb-4">Upload</h1>
                        <p class="card-text text-center mb-4">Please upload a CSV file containing your product data.</p>
                        <form method="post" enctype="multipart/form-data" action="views/results.php">
                            <div class="mb-4">
                                <input class="form-control" type="file" name="csv_file" accept=".csv" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark w-25 m-auto">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>