<!DOCTYPE html>
<html lang="en">

<head>
    <title>System Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('dist/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <script src="https:/ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="{{ route('database') }}"
            method="post">
            @csrf
            <div class="fv-row mb-10">
                <label class="form-label fs-6 fw-bolder text-dark">Enter Database Name</label>
                <input class="form-control form-control-lg form-control-solid" type="text" name="database_name" autocomplete="off"
                    value="{{session('database_value') ? session('database_value') : old('database_name') }}"  id="database_name"/>
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div id="database_name_error" style="color:red"></div>

            </div>
            <div class="text-center">
                <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                    <span class="indicator-label">Submit</span>
                </button>
            </div>
        </form>
    </div>
</body>
<script>
    $(document).ready(function () {
            $("#database_name").on("input", ValidateDatabase);

            $("#kt_sign_in_form").submit(function (e) {
                let database_name = ValidateDatabase();
                if (!database_name ) {
                    e.preventDefault();
                }
            });
        });

        function ValidateDatabase() {
            let database_name = $("#database_name").val();
            if (database_name == "") {
                $("#database_name_error").html("Database Name cannot be blank");
                return false;
            } else if (!/^[A-Za-z_]{1,20}$/.test(database_name)) {
                $("#database_name_error").html("Database Name must contain characters and _");
                return false;
            } else {
                $("#database_name_error").html("");
                return true;
            }
        } 
</script>
</html>