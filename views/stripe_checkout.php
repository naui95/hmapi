<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Redirecting ...</title>
        <script src="https://js.stripe.com/v3/"></script>
    </head>
    <body>
    <script type="text/javascript">
        // Create an instance of the Stripe object with your publishable API key
        var stripe = Stripe("pk_test_ruttiYWbopHL0a3ttNAf0aUf");

        stripe.redirectToCheckout({ sessionId: '<?php echo $stripe_session_id;?>' })
        .then(function (result) {
            // If `redirectToCheckout` fails due to a browser or network
            // error, display the localized error message to your customer
            // using `result.error.message`.
        });

    </script>
    </body>
</html>