## Installation
1. Upload the `hmapi` folder to the module folder
2. update the `application/config/config.php`. Include in the `$config['csrf_exclude_uris']` variable also the following endpoint `hmapi/pay/stripe_validate_payment`