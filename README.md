## Installation (alpha version)
1. Download the last version from 
2. Upload the `hmapi` folder to `application/module` folder
3. Update the `application/config/config.php`and include in the `$config['csrf_exclude_uris']` variable also the following endpoint `hmapi/pay/stripe_validate_payment`
4. Update the `hmapi/controllers/Pay.php`file and add your `WEBHOOK SECRET`, `STRIPE API KEY` and `STRIPE PUBLISHABLE KEY`
5. On line 38 of `hmapi/controllers/Pay.php` be sure to set the right currency for the payments
6. Copy the `InvoicePlane_Stripe.php` file in `application/views/invoices_templates/public`
7. Go to your InvoicePlane system settings (http://yourdomain.tld/index.php/settings) and in the `Invoices` tab go to the `Invoice Templates` section and set the `Default Public Template` to `InvoicePlane_Stripe`
