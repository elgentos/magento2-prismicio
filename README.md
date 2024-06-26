# Magento 2 [Prismic.io](https://prismic.io) integration - by elgentos
For the love of Magento and Prismic we created a module to do a seamless integration between the two.
It is a full replacement for the default CMS in Magento.

## Requirements
* a Magento 2 installation
* PHP 8.1 (we still have PHP 7.4 support on the `1.7.*` releases)
* a Prismic repository, head over to https://prismic.io and start today

## Installation
Head over to your Magento installation and require the extension.

```bash
composer require elgentos/module-prismicio
php bin/magento setup:upgrade
```

## Documentation
You can find our documentation here https://elgentos.github.io/magento2-prismicio/

It will cover:
- Configuration
- Creating Content models
- Writing View XML 
- Best practices

Docs are build with [mkdocs](https://www.mkdocs.org/), if you want to contribute start editing files in `docs/*`.

## Configuration / Quick start
To make a connection from Magento to Prismic  you have to create repository in Prismic 
or select and existing one, head to *Settings*, *API & Security*, copy the entry point.

Open the Magento admin, head to *Stores*, *Configuration*, *Elgentos / PrismicIO*.

1. First
   - Enable Prismic
   - Paste the entry point
   - Save
2. Second
   - Select language
   - Save

### Configuration
![Configuration](https://user-images.githubusercontent.com/431360/100359099-60a84480-2ff7-11eb-87e2-4a01ec82fdbc.png)

## Contributors
Without your [contributions](https://github.com/elgentos/magento2-prismicio/graphs/contributors) this module wouldn't exist.

**And also; everyone who created issues, asked questions, collaborating in any other way, thanks a lot!**

Want your name on this list, start contributing today.

Do you like what we do, take a look at [elgentos.nl](https://elgentos.nl/) to learn more about our company.
