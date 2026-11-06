<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : 'Aplikasi Pengaduan LaporDah' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .brand-title {
            color:rgb(0, 255, 51);
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        .form-control {
            border-radius: 5px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
        }
        .btn-submit {
            background:rgb(53, 220, 73);
            border: none;
            border-radius: 5px;
            padding: 0.75rem;
            color: white;
            font-weight: 500;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-submit:hover {
            background:rgb(88, 199, 14);
        }
        .captcha-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .captcha-text {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #333;
        }

        .forgot-password {
            color:rgb(46, 223, 26);
            text-decoration: none;
            font-size: 14px;
            float: right;
        }
        .auth-links {
            text-align: center;
            margin-top: 1rem;
        }
        .auth-links a {
            color: #666;
            text-decoration: none;
        }

        .captcha-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .refresh-button {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
        }

        .refresh-button:hover {
            color: rgb(53, 220, 73);
        }
    </style>
</head>
<body>
    <main class="container">
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>