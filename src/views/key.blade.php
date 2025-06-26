<!DOCTYPE html>
<html lang="en">

<head>
    <title>Key Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link rel="icon" href="{{ asset('elsner_favicon.svg') }}" type="image/x-icon">
    <link href="{{ asset('dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('dist/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-position: center bottom;
            background-repeat: no-repeat;
            background-size: contain;
            background-attachment: fixed;
        }

        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .form-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-header {
            padding: 0.5rem 1rem;
            /* reduce top/bottom padding */
            min-height: auto;
            /* remove minimum height */
        }

        .modal-header .modal-title {
            margin-bottom: 0;
            /* remove bottom margin */
            font-weight: 600;
            /* keep it semi-bold */
            font-size: 1.25rem;
            /* adjust size if you want */
            line-height: 1.2;
            padding-top: 0.2rem;
            /* small padding for alignment */
            padding-bottom: 0.2rem;
        }
    </style>
</head>

<body>

   
    {{-- key and email popup for copy --}}
    @if(session('key'))
        <div class="modal fade" id="sessionInfoModal" tabindex="-1" aria-labelledby="sessionInfoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-top-centered">
                <div class="modal-content border border-primary">
                    <div class="modal-header bg-light-primary">
                        <p class="modal-title text-primary" id="sessionInfoModalLabel">Info</p>
                        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body d-flex align-items-center gap-3">
                        <span>Key : </span>
                        <span id="sessionKeyText" class="text-gray-700 fw-semibold fs-6 flex-grow-1"> 
                            <strong>{{ session('key') }}</strong></span>

                        {{-- Copy Button --}}
                        <button id="copyBtn" type="button" class="btn btn-icon btn-sm btn-light-primary"
                            onclick="copySessionKey()" title="Copy">
                            <span id="copyIcon"><i class="fa-solid fa-copy"></i></span>
                        </button>
                    </div>
                    {{-- email --}}
                    <div class="modal-body d-flex align-items-center gap-3">
                        <span>Email : </span>
                        <span id="sessionEmailText" class="text-gray-700 fw-semibold fs-6 flex-grow-1"> 
                            <strong>{{ session('email') }}</strong></span>

                        {{-- Copy Button --}}
                        <button id="copyBtn" type="button" class="btn btn-icon btn-sm btn-light-primary"
                            onclick="copySessionEmail()" title="Copy">
                            <span id="copyIcon"><i class="fa-solid fa-copy"></i></span>
                        </button>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function copySessionKey() {
                const text = document.getElementById('sessionKeyText').innerText;
                navigator.clipboard.writeText(text).then(() => {
                    document.getElementById('copyIcon').innerHTML = '<i class="fa-solid fa-check"></i>';
                    setTimeout(() => {
                        document.getElementById('copyIcon').innerHTML = '<i class="fa-solid fa-copy"></i>';
                    }, 2000);
                }).catch(err => {
                    console.error('Copy failed:', err);
                });
            }
            function copySessionEmail() {
                const text = document.getElementById('sessionEmailText').innerText;
                navigator.clipboard.writeText(text).then(() => {
                    document.getElementById('copyIcon').innerHTML = '<i class="fa-solid fa-check"></i>';
                    setTimeout(() => {
                        document.getElementById('copyIcon').innerHTML = '<i class="fa-solid fa-copy"></i>';
                    }, 2000);
                }).catch(err => {
                    console.error('Copy failed:', err);
                });
            }

            // Auto-show modal when session message exists
            document.addEventListener('DOMContentLoaded', function () {
                const sessionModal = document.getElementById('sessionInfoModal');
                if (sessionModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalInstance = new bootstrap.Modal(sessionModal);
                    modalInstance.show();
                }
            });
        </script>
    @endif






    {{-- Error Popup --}}
    @if(session('error'))
        <div id="sessionError"
            class="d-flex align-items-center bg-light-danger border border-danger border-dashed rounded px-6 py-4 position-fixed top-0 start-50 translate-middle-x mt-10 shadow-lg zindex-105"
            style="min-width: 350px; max-width: 90%; animation: fadeInDown 0.5s ease; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 12px; box-shadow: 0 4px 20px rgba(220, 38, 38, 0.2);">
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

    <div class="page-wrapper">
        <div class="form-wrapper">
            <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15">
                <form class="form w-100" id="kt_sign_in_form" action="{{ route('system.auth.verify') }}" method="post">
                    @csrf
                    <div class="fv-row mb-10">
                        <label class="form-label fs-6 fw-bolder text-dark">Enter Key</label>
                        <input class="form-control form-control-lg form-control-solid" type="text" name="key"
                            autocomplete="off" id="key"
                            value="{{session('key_value') ? session('key_value') : old('key') }}" />
                        <div id="key_error" style="color: red"></div>
                        {{-- @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif --}}
                        {{-- @if(isset($errors) && $errors->has('key'))
                        <div style="color:red">
                            {{ $errors->first('key') }}
                        </div>
                        @endif --}}

                    </div>
                    <div class="text-center">
                        <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                            <span class="indicator-label">Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <footer class="footer py-4 w-100" id="kt_footer">
            <div class="container-fluid text-center">
                <span class="text-muted fw-bold">© {{ date('Y') }} Elsner Technologies Pvt. Ltd. All rights
                    reserved.</span>
            </div>
        </footer>
    </div>
</body>
<script>var hostUrl = "assets/";</script>
<!--begin::Javascript-->
<!-- Global Metronic JS Bundle -->
<script src="{{ asset('dist/assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('dist/assets/js/scripts.bundle.js') }}"></script>

<!--  DataTables (AFTER jQuery from Metronic) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!--end::Global Javascript Bundle-->
<!--begin::Page Vendors Javascript(used by this page)-->
<script src="{{ asset('dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
<!--end::Page Vendors Javascript-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="{{ asset('dist/assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('dist/assets/js/custom/apps/chat/chat.js') }}"></script>
<script src="{{ asset('dist/assets/js/custom/modals/create-app.js') }}"></script>
<script src="{{ asset('dist/assets/js/custom/modals/upgrade-plan.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            const errBox = document.getElementById("sessionError");
            if (errBox) {
                errBox.style.transition = "opacity 0.5s ease";
                errBox.style.opacity = "0";
                setTimeout(() => errBox.remove(), 600);
            }
        }, 2000);
    });

    $(document).ready(function () {
        $("#key").on("input", ValidateKey);

        $("#kt_sign_in_form").submit(function (e) {
            let key = ValidateKey();
            if (!key) {
                e.preventDefault();
            }
        });
    });

    function ValidateKey() {
        let key = $("#key").val();
        if (key === "") {
            $("#key_error").html("Key cannot be empty");
            return false;
        } else if (key.length !== 14) {
            $("#key_error").html("Key must be of 14 digits");
            return false;
        } else {
            $("#key_error").html("");
            return true;
        }
    }  
</script>

</html>