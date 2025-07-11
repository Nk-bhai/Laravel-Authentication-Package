<!DOCTYPE html>
<html lang="en">

<head>
    <title>Purchase Code Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link rel="icon" href="{{ asset('elsner_favicon.svg') }}" type="image/x-icon">
    <link href="{{ asset('dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
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
    </style>
</head>

<body>
      
    {{-- Error Popup --}}
    @if($errors->has('purchase_code'))
        <div id="sessionError"
            class="d-flex align-items-center bg-light-danger border border-danger border-dashed rounded px-6 py-4 position-fixed top-0 start-50 translate-middle-x mt-10 shadow-lg zindex-105"
            style="min-width: 350px; max-width: 90%; animation: fadeInDown 0.5s ease; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 12px; box-shadow: 0 4px 20px rgba(220, 38, 38, 0.2);">
            <span class="svg-icon svg-icon-2tx svg-icon-danger me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
                    <path opacity="0.3" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2z" fill="#b91c1c" />
                    <path d="M13 7h-2v6h2V7zm0 8h-2v2h2v-2z" fill="#b91c1c" />
                </svg>
            </span>
            <span class="fw-bold text-danger fs-6">{{ $errors->first('purchase_code') }}</span>
            <button type="button" class="btn-close ms-auto" onclick="$('#sessionError').fadeOut(300)"
                aria-label="Close"></button>
        </div>
    @endif

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
                <form class="form w-100" id="kt_sign_in_form" action="{{ route('system.auth.purchase_code_verify') }}" method="post">
                    @csrf
                    <div class="fv-row mb-10">
                        <label class="form-label fs-6 fw-bolder text-dark">Enter Purchase Code</label>
                        <input class="form-control form-control-lg form-control-solid" type="text" name="purchase_code"
                            autocomplete="off" id="purchase_code"
                            />
                        <div id="purchase_code_error" style="color: red">
                        </div>

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
        $("#purchase_code").on("input", ValidatePurchaseCode);

        $("#kt_sign_in_form").submit(function (e) {
            let purchase_code = ValidatePurchaseCode();
            if (!purchase_code) {
                e.preventDefault();
            }
        });
    });

    function ValidatePurchaseCode() {
        let purchase_code = $("#purchase_code").val();
        if (purchase_code === "") {
            $("#purchase_code_error").html("Purchase Code cannot be empty");
            return false;
        } else {
            $("#purchase_code_error").html("");
            return true;
        }
    }  
</script>

</html>