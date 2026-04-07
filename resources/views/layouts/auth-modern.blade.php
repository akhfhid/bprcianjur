<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Autentikasi') - SIKAP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Source+Sans+3:wght@400;500;600&display=swap">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Source Sans 3', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 14px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .auth-brand {
            font-family: 'Manrope', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0c4a6e;
            text-align: center;
            margin-bottom: 4px;
        }

        .auth-brand-sub {
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
            margin-bottom: 28px;
        }

        .auth-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .auth-desc {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 22px;
            line-height: 1.45;
        }

        .form-label-modern {
            display: block;
            margin-bottom: 5px;
            color: #334155;
            font-weight: 600;
            font-size: 0.88rem;
        }

        .form-control-modern {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 13px;
            font-size: 0.93rem;
            transition: border-color .2s, box-shadow .2s;
            background: #fff;
        }

        .form-control-modern:focus {
            border-color: #0c4a6e;
            box-shadow: 0 0 0 3px rgba(12, 74, 110, 0.1);
            outline: 0;
        }

        .btn-auth {
            display: block;
            width: 100%;
            border: 0;
            border-radius: 10px;
            background: #0c4a6e;
            color: #fff;
            font-weight: 700;
            padding: 11px 16px;
            font-size: 0.93rem;
            transition: background .2s;
            cursor: pointer;
        }

        .btn-auth:hover {
            background: #0a3a57;
            color: #fff;
        }

        .auth-link {
            color: #0c4a6e;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            color: #0a3a57;
            text-decoration: none;
        }
    </style>
    @stack('auth-styles')
</head>

<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-brand">SIKAP</div>
            <div class="auth-brand-sub">Sistem Kepegawaian dan Peraturan</div>

            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('auth-scripts')
</body>

</html>