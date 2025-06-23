@extends('master_old')
@section('title', 'Login Page')

@section('contents')
    {{-- Success Popup --}}
    @if(session('message'))
        <div id="sessionMessage"
            class="d-flex align-items-center bg-light-success border border-success border-dashed rounded px-6 py-4 position-fixed start-50 translate-middle-x mt-20 shadow-lg"
            style="min-width: 350px; max-width: 90%; z-index: 9999; animation: fadeInDown 0.5s ease;">
            <span class="svg-icon svg-icon-2tx svg-icon-success me-3">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3"
                        d="M6 21H18C19.1 21 20 20.1 20 19V5C20 3.9 19.1 3 18 3H6C4.9 3 4 3.9 4 5V19C4 20.1 4.9 21 6 21Z"
                        fill="#047857" />
                    <path
                        d="M9.29 16.29L5.7 12.7C5.31 12.31 5.31 11.68 5.7 11.29C6.09 10.9 6.72 10.9 7.11 11.29L10 14.17L16.88 7.29C17.27 6.9 17.9 6.9 18.29 7.29C18.68 7.68 18.68 8.31 18.29 8.7L10.7 16.29C10.31 16.68 9.68 16.68 9.29 16.29Z"
                        fill="#047857" />
                </svg>
            </span>
            <span class="fw-bold text-success fs-6">{{ session('message') }}</span>
            <button type="button" class="btn-close ms-auto" onclick="$('#sessionMessage').fadeOut(300)"
                aria-label="Close"></button>
        </div>
    @endif

    {{-- Error Popup --}}
    @if(session('error'))
        <div id="sessionError"
            class="d-flex align-items-center bg-light-danger border border-danger border-dashed rounded px-6 py-4 position-fixed start-50 translate-middle-x mt-20 shadow-lg"
            style="min-width: 350px; max-width: 90%; z-index: 9999; animation: fadeInDown 0.5s ease;">
            <span class="svg-icon svg-icon-2tx svg-icon-danger me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
                    <path opacity="0.3" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2z" fill="#b91c1c" />
                    <path d="M13 7h-2v6h2V7zm0 8h-2v2h2v-2z" fill="#b91c1c" />
                </svg>
            </span>
            <span class="fw-bold text-danger fs-6">{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" onclick="$('#sessionError').fadeOut(300)"
                aria-label="Close"></button>
        </div>
    @endif

    <!--begin::Authentication - Sign-in -->
    <div class="d-flex flex-center flex-column flex-grow-1 py-10 px-5">
        <div class="d-flex flex-center w-100 p-10">
            <div class="card w-md-450px shadow-sm bg-body rounded p-7 p-lg-15">
                <form class="form w-100" action="{{ route('system.auth.login.post') }}" method="POST" id="loginform">
                    @csrf
                    <div class="text-center mb-10">
                        <h1 class="text-dark mb-3">Login Page</h1>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="form-label fs-6 fw-bolder text-dark">Email</label>
                        <input class="form-control form-control-lg form-control-solid" type="email" name="email" id="email"
                            value="{{ session('loginemail') }}" />
                        <div id="email_error" class="text-danger fs-7 mt-1"></div>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="form-label fs-6 fw-bolder text-dark">Password</label>
                        <div class="password-wrapper position-relative">
                            <input class="form-control form-control-lg form-control-solid" type="password" name="password"
                                id="password" autocomplete="off" value="{{ session('loginpassword') }}" />
                            <span class="password-toggle-icon position-absolute top-50 end-0 translate-middle-y me-3"
                                onclick="Password_Show_hide()" style="cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div id="password_error" class="text-danger fs-7 mt-1"></div>
                    </div>

                    <div class="text-center">
                        <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                            <span class="indicator-label">Log In</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Authentication-->

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(() => {
                const msgBox = document.getElementById("sessionMessage");
                const errBox = document.getElementById("sessionError");

                if (msgBox) {
                    msgBox.style.transition = "opacity 0.5s ease";
                    msgBox.style.opacity = "0";
                    setTimeout(() => msgBox.remove(), 600);
                }

                if (errBox) {
                    errBox.style.transition = "opacity 0.5s ease";
                    errBox.style.opacity = "0";
                    setTimeout(() => errBox.remove(), 600);
                }
            }, 2000);
        });

        function Password_Show_hide() {
            let x = document.getElementById("password");
            let icon = document.querySelector(".password-toggle-icon i");
            if (x.type === "password") {
                x.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                x.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
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
            if (email === "") {
                $("#email_error").html("Email cannot be empty");
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
            if (password === "") {
                $("#password_error").html("Password cannot be empty");
                return false;
            } else if (!/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8}$/.test(password)) {
                $("#password_error").html("Password must be 8 chars, include upper, lower, digit, and special char");
                return false;
            } else {
                $("#password_error").html("");
                return true;
            }
        }
    </script>
@endsection