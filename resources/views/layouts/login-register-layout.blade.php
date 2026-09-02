<!DOCTYPE html>
<html>

<head>
    <title>@yield('title') - AI1 Business ERP</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

    <div class="container d-flex vh-100 justify-content-center align-items-center">

        <div class="row border shadow rounded-4 overflow-hidden" style="width: 900px; height: 500px;">
            <div class="col-md-6 image-container">
                <img class="login-banner" src="{{ Vite::asset('resources/images/login-banner.webp') }}">
            </div>
            <div class="col-md-6 ">
                @yield('content')
            </div>
        </div>
    </div>

    @if (session('success'))
        <div id="success-message" style="
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                                    background-color: #d4edda;
                                    color: #155724;
                                    padding: 15px 20px;
                                    border-left: 5px solid #28a745;
                                    border-radius: 5px;
                                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                    z-index: 9999;
                                    width:300px;
                                ">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div id="error-message" style="
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                                    width:300px;
                                    background-color: #f8d7da;
                                    color: #721c24;
                                    padding: 15px 20px;
                                    border-left: 5px solid #dc3545;
                                    border-radius: 5px;
                                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                    z-index: 9999;
                                ">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div id="error-message" style="
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                                    width:300px;
                                    background-color: #f8d7da;
                                    color: #721c24;
                                    padding: 15px 20px;
                                    border-left: 5px solid #dc3545;
                                    border-radius: 5px;
                                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                    z-index: 9999;
                            ">
            {{ $errors->first() }}
        </div>
    @endif

    <script>
        // Hide success message after 3 seconds
        setTimeout(function () {
            var successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 3000);

        // Hide error message after 3 seconds
        setTimeout(function () {
            var errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 3000);
    </script>
</body>

</html>