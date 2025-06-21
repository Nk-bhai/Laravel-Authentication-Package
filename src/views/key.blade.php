<!DOCTYPE html>
<html lang="en">

<head>
    <title>Key Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link rel="icon" href="{{ asset('elsner_favicon.svg') }}" type="image/x-icon">
    <link href="{{ asset('dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dist/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-image: url('{{ asset('dist/assets/media/illustrations/dozzy-1/14.png') }}');
            background-position: center bottom;
            background-repeat: no-repeat;
            background-size: contain;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="{{ route('system.auth.verify') }}"
            method="post">
            @csrf
            <div class="fv-row mb-10">
                <label class="form-label fs-6 fw-bolder text-dark">Enter Key</label>
                <input class="form-control form-control-lg form-control-solid" type="text" name="key" autocomplete="off"
                    value="{{session('key_value') ? session('key_value') : old('key') }}" />
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if(isset($errors) && $errors->has('key'))
                    <div style="color:red">
                        {{ $errors->first('key') }}
                    </div>
                @endif

            </div>
            <div class="text-center">
                <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                    <span class="indicator-label">Submit</span>
                </button>
            </div>
        </form>
    </div>
</body>

</html>