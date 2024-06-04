<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('https://res.cloudinary.com/hilnmyskv/image/upload/q_70,f_auto/v1690828912/Algolia_com_Blog_assets/Featured_images/ecommerce/building-an-online-marketplace-get-the-secrets-of-success/iwsxckflvoc0sfl1fld8.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 400px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            background-color: rgba(255, 255, 255, 0.5);
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            color: #007bff;
        }

        .auth-links {
            text-align: center;
        }

        .auth-links a {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .auth-links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Marketplace Web Application</h1>
        <div class="auth-links">
            @if (Route::has('login'))
                <a href="{{ route('login') }}">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endif
        </div>
    </div>
</body>
</html>
