<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    @yield('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header__inner">
                <h1 class="header__logo">
                    <img class="header__logo--link" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
                </h1>
                @yield('link')
            </div>
        </header>

        <main>
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
