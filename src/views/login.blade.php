@extends('master_old')
@section('contents')

    <div class="d-flex flex-column flex-root ">

        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed"
            style="background-image: url(dist/assets/media/illustrations/dozzy-1/14.png">
            <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                <form class="form w-100" action="{{ route('system.auth.login.post') }}" method="post" id="loginform">
                    @csrf
                    <div class="text-center mb-10">
                        <h1 class="text-dark mb-3">Login Page</h1>
                    </div>
                    <div class="fv-row mb-10">
                        <label class="form-label fs-6 fw-bolder text-dark">Email</label>
                        <input class="form-control form-control-lg form-control-solid" type="email" name="email"
                            id="email" value="{{session('loginemail')}}" />
                        <div id="email_error" style="color:red"></div>
                    </div>  
                    <div class="fv-row mb-10">
                        <div class="d-flex flex-stack mb-2">
                            <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
                        </div>
                        <div class="password-wrapper">
                            <input class="form-control form-control-lg form-control-solid" type="password" name="password"
                                autocomplete="off" id="password" value="{{session('loginpassword')}}" />
                            <span class="password-toggle-icon" onclick="Password_Show_hide()">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                            <div id="password_error" style="color:red"></div>
                    </div>
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="text-center">
                        <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                            <span class="indicator-label">Log In</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // password show hide
    function Password_Show_hide() {
        var x = document.getElementById("password");
        let icon = document.querySelector(".password-toggle-icon i");
        if (x.type === "password") {
            x.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            x.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
        $(document).ready(function () {
            $("#email").on("input", ValidateEmail);
            $("#password").on("input", ValidatePassword);

            $("#loginform").submit(function (e) {
                let email = ValidateEmail();
                let password = ValidatePassword();
                if (!email || !password) {
                    e.preventDefault();
                }
            });
        });

        function ValidateEmail() {
            let email = $("#email").val();
            if (email == "") {
                $("#email_error").html("Email cannot be blank");
                return false;
            } else if (!/^[A-Za-z0-9.]+@[A-Za-z]{2,7}\.[A-Za-z]{2,3}$/.test(email)) {
                $("#email_error").html("Email must be valid");
                return false;
            } else {
                $("#email_error").html("");
                return true;
            }
        }

        function ValidatePassword() {
            let password = $("#password").val();
            if (password == "") {
                $("#password_error").html("Password cannot be blank");
                return false;
            } else if (!/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8}$/.test(password)) {
                $("#password_error").html("Password must contain at least 1 uppercase, 1 lowercase, 1 digit, 1 special character, and be exactly 8 characters");
                return false;
            } else {
                $("#password_error").html("");
                return true;
            }
        }
    </script>
@endsection