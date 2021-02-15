# Magento 2 Store Credit Extension

This module allows you to add credit to user accounts in Magento 2 Open Source to be spent at the checkout.

## Features
The features below are summarised from the original [blog post](https://swarmingtech.com/blog/post/magento-store-credit).
* The store credit name, value and currency are customisable as needed.
* Facility to refund as store credit
* Store credit can have an expiry date
* Customer notifications
* Customer dashboard
* Viewing and manual addition/subtraction of credit from admin

## Installation
Use composer to install the extension. Remove the old version of the module first if it's installed - `composer remove swarming/magento2-store-credit`
```bash
composer require getjohn/swarming-store-credit
php bin/magento setup:upgrade
```

## Documentation
[User Guide](https://github.com/getjohn/swarming-store-credit/wiki/Store-Credit-Extension-User-Guide)

## Contributing
Pull requests are welcome, we'll review them when we can. We'll look at bug reports without patches when we can, but we've a limited amount of time to spend on the module, so patches are much appreciated. We're mostly just maintaining this and ensuring it stays working with the latest Magento versions, so it's unlikely we'll implement new feature requests without patches or an internal requirement.

We're happy to offer our commercial development services if you need additional features or modifications for your deployment - [drop us a line](https://getjohn.co.uk/).

## Credits
[Swarming Technology](https://swarmingtech.com/) released this under the MIT license. We relicensed it under the BSD 3 Clause license to keep it friendly to business, while covering ourselves.
Maintained by [Get John](https://www.getjohn.co.uk/).

## Notes
There's still quite a few copyright notices in here, we've not gone through in detail to check for commercial software notices which were in the original release. If anyone wants to clean it up, feel free. We also welcome patches for this README file!

## License
[BSD 3-Clause](https://choosealicense.com/licenses/bsd-3-clause/)
