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
            {{-- @php
                $errors = $errors ?? new \Illuminate\Support\MessageBag;
            @endphp --}}
            <div class="fv-row mb-10">
                <label class="form-label fs-6 fw-bolder text-dark">Database Name</label>
                <input class="form-control form-control-lg form-control-solid" type="text" name="database_name"
                    autocomplete="off"
                    value="{{session('database_value') ? session('database_value') : old('database_name') }}"
                    id="database_name" />

                <div id="database_name_error" style="color:red">
                    @error('database_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="fv-row mb-10">
                <label class="form-label fs-6 fw-bolder text-dark">Host Name</label>
                <input class="form-control form-control-lg form-control-solid" type="text" name="host_name"
                    autocomplete="off"
                    value="{{session('host_name_value') ? session('host_name_value') : old('host_name') }}"
                    id="host_name" />

                <div id="host_name_error" style="color:red">
                    @error('host_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="fv-row mb-10">
                <label class="form-label fs-6 fw-bolder text-dark">User Name</label>
                <input class="form-control form-control-lg form-control-solid" type="text" name="user_name"
                    autocomplete="off"
                    value="{{session('user_name_value') ? session('user_name_value') : old('user_name') }}"
                    id="user_name" />

                <div id="user_name_error" style="color:red">
                    @error('user_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="fv-row mb-10">
                <label class="form-label fs-6 fw-bolder text-dark">Database Password</label>
                <input class="form-control form-control-lg form-control-solid" type="password" name="db_password"
                    autocomplete="off"
                    value="{{session('db_password_value') ? session('db_password_value') : old('db_password') }}"
                    id="db_password" />

                <div id="db_password_error" style="color:red">
                    @error('db_password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif --}}
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
        $("#host_name").on("input", Validate_host_name);
        $("#user_name").on("input", Validate_user_name);
        $("#db_password").on("input", Validate_db_password);

        $("#kt_sign_in_form").submit(function (e) {
            let database_name = ValidateDatabase();
            let host_name = Validate_host_name();
            let user_name = Validate_user_name();
            let db_password = Validate_db_password();
            if (!database_name || !host_name || !user_name || !db_password) {
                e.preventDefault();
            }
        });
    });

    // Database Name
    function ValidateDatabase() {
        let database_name = $("#database_name").val();
        if (database_name == "") {
            $("#database_name_error").html("Database Name cannot be empty");
            return false;
        } else if (!/^[A-Za-z_]{1,20}$/.test(database_name)) {
            $("#database_name_error").html("MySQL Database Name must contain characters and _");
            return false;
        } else {
            $("#database_name_error").html("");
            return true;
        }
    }

    // hostname
    function Validate_host_name() {
        let host_name = $("#host_name").val();
        if (host_name == "") {
            $("#host_name_error").html("Host Name cannot be empty");
            return false;
        } else if (!/^([a-zA-Z0-9\-\.]+|\d{1,3}(\.\d{1,3}){3})$/.test(host_name)) {
            $("#host_name_error").html("MySQL host must be a valid IP address or domain name.");
            return false;
        } else {
            $("#host_name_error").html("");
            return true;
        }
    }

    //database user name
    function Validate_user_name() {
        let user_name = $("#user_name").val();
        if (user_name == "") {
            $("#user_name_error").html("User Name cannot be empty");
            return false;
        } else if (!/^[a-zA-Z0-9._-]{1,32}$/.test(user_name)) {
            $("#user_name_error").html("MySQL username may only contain letters, numbers, dots (.), underscores (_) and hyphens (-).");
            return false;
        } else {
            $("#user_name_error").html("");
            return true;
        }
    }

    // Database password
    function Validate_db_password() {
        let db_password = $("#db_password").val();
        if (db_password.length > 0 && !/^[\w!@#$%^&*()\-+=.\[\]{};:'",<>/?\\|`~]{2,32}$/.test(db_password)) {
            $("#db_password_error").html("password must be 2-32 characters with valid characters.");
            return false;
        } else {
            $("#db_password_error").html("");
            return true;
        }
    } 
</script>

</html>