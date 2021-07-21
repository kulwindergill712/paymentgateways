<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Payment</title>
        <script>
            var wpwlOptions = {
                style: "plain",

            }
        </script>
        <script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId={{ $data['checkout_id'] }}"></script>

    </head>
    <body>

    <div class="wpwl-group">
    <div class="wpwl-label">
     <div class="wpwl-brand wpwl-brand-MASTER"></div>
     </div>
    <div class="wpwl-wrapper"><div class="wpwl-brand wpwl-brand-VISA"></div>
</div>
        <div class="wpwl-container">


        <form action="{{ $data['callback_url']}}" class="paymentWidgets" data-brands="PAYPAL_CONTINUE AFTERPAY DAOPAY  CREDIT_CLICK VISA MASTER AMEX AIRPLUS VPAY ARGENCARD ">

        </form>
        </div>

    </body>
</html>
