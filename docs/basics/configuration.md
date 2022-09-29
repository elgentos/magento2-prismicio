---
title: Configuration of the Magento 2 module
description: Configure Magento to connect to Prismic
---

## Get the repository url

Head over to [Prismic.io](https://prismic.io/), click Dashboard or Login

* click on your repository
* click on settings
* click on Configuration / Api & Security
* copy the API endpoint

Head over to the admin of your Magento installation.

* click on Stores / Configuration
* click on Elgentos / Prismic.IO
* under **General**
    * Enable Prismic
    * Add the API endpoint copied over from Prismic
    * optionally add a API secret [see below](#optionally-protect-with-a-key)
    * save
* Content section
    * Select default language (or set per store)
    * Set [Default content type](#content-types)
    * Optionally set [Fetchlinks](#fetch-links)
    * Optionally enable [Debugging](#debugging)
    * Optionally enable [Preview](#preview-mode)
* Integration Fields section
  * Product Attributes
    *Attributes to make available in Prismic Integration Fields. The following attributes will always be made available: name, image, status, short_description, updated_at.*
* Sitemap section

### Optionally, protect with a key

### Content types

### Fetch Links

### Debugging

### Preview mode
