## Disclaimer
This stripe module for [InvoicePlane](https://www.invoiceplane.com/) is still in an early version (_alpha_) and it's not ready for production. The module is distributed for free. Use it at your own risk, any liability of the developer is excluded. Do not use this module if it is in contrast with your country's regulation. This module is in no way supported by Stripe.

## Installation (alpha version)
1. Download the last version from the [release page](https://github.com/naui95/hmapi/releases)
2. Upload the `hmapi` folder to `application/module` folder
3. Update the `application/config/config.php`and include in the `$config['csrf_exclude_uris']` variable also the following endpoint `hmapi/pay/stripe_validate_payment`
4. Create an endpoint in stripe `https://dashboard.stripe.com/webhooks` that points to `https://yourdomain.tld/hmapi/pay/stripe_validate_payment` with `Events to send` set to `checkout.session.completed`
5. Update the `hmapi/controllers/Pay.php`file and add your `WEBHOOK SECRET`, `STRIPE API KEY` and `STRIPE PUBLISHABLE KEY`
6. On line 38 of `hmapi/controllers/Pay.php` be sure to set the right currency for the payments
7. Copy the `InvoicePlane_Stripe.php` file in `application/views/invoices_templates/public`
8. Go to your InvoicePlane system settings (http://yourdomain.tld/index.php/settings) and in the `Invoices` tab go to the `Invoice Templates` section and set the `Default Public Template` to `InvoicePlane_Stripe`


## Bugs
If you encouter bugs, please open an issue in the Github section.

## Security issues
Security concerns can be sent to support(at)0ll.ch
