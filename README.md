## Installation (alpha version)
1. Upload the `hmapi` folder to `application/module` folder
2. Update the `application/config/config.php`and include in the `$config['csrf_exclude_uris']` variable also the following endpoint `hmapi/pay/stripe_validate_payment`
3. Update the `hmapi/controllers/Pay.php`file and add your `WEBHOOK SECRET`, `STRIPE API KEY` and `STRIPE PUBLISHABLE KEY`
4. On line 38 of `hmapi/controllers/Pay.php` be sure to set the right currency for the payments
5. 
