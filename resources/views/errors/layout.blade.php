<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exception->getStatusCode() ?? 'Error' }} - HRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .error-box { text-align: center; background: #fff; padding: 3rem 4rem; border-radius: .75rem; box-shadow: 0 10px 25px rgba(0,0,0,.08); max-width: 32rem; margin: 1rem; }
        .error-code { font-size: 4.5rem; font-weight: 700; color: #2563eb; line-height: 1; }
        h1 { font-size: 1.5rem; margin: 1rem 0 .5rem; color: #111827; }
        p { color: #6b7280; margin-bottom: 1.5rem; }
        a { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: .6rem 1.4rem; border-radius: .375rem; font-size: .9rem; }
        a:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code">{{ $exception->getStatusCode() ?? 500 }}</div>
        <h1>@yield('title', 'Something went wrong')</h1>
        <p>@yield('message', 'We could not complete your request. Please try again.')</p>
        <a href="{{ url('/') }}"><i class="fas fa-home mr-2"></i>Back to Home</a>
    </div>
</body>
</html>
