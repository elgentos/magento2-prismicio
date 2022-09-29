---
title: Installation of the Prismic module
description: This page describers the basic of the Prismic Magento 2 module
---

## Prismic repository
First, head over to [Prismic.io](https://prismic.io/) and create a account and repository.
After you created a module, install the Magento 2 module by elgentos.


## Module installation
```bash
composer install --prefer-dist --optimize-classloadder elgentos/module-prismicio
bin/magento module:enable Elgentos_PrismicIO
bin/magento setup:upgrade
```
