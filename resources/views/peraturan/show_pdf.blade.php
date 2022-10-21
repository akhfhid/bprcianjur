<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <title>Rules Pdf | {{ Auth::user()->name }}</title>
</head>

<body>
    <style>
        .watemark img{
            width: 100%;
        }
        .watermark {
            position: relative;
        }
        .watermark::after {
            content: 'Printed By {{ Auth::user()->name }} - {{ Auth::user()->email }} -{{ $time = \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}';
            position: absolute;
            bottom: 10;
            top: 10;
            right: 0;
            opacity:0,5;
            font-size: 1,5em;
        }
    </style>
    <div class="container">
        <div>
            <div class="watermark">
                {!! $peraturan->pdf !!}
            </div>
            </div>
        </div>
        <br>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>
</body>

</html>
