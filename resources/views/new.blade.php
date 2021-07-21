<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Payment</title>
        {{-- <script>
            var wpwlOptions = {
                style: "card",

            }
        </script> --}}
       <script src="https://test.oppwa.com/v1/paymentWidgets.js"></script>

    </head>
    <body>

        <form  class="paymentWidgets" data-brands="PAYPAL_CONTINUE APPLEPAY GOOGLEPAY"></form>

    </body>
</html>
