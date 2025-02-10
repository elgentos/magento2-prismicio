---
title: Configuration of the Magento 2 module
description: Configure Magento to connect to Prismic
---

# Configuration

The Prismic module can be configured in the Magento admin panel under Stores > Configuration > Elgentos > Prismic.IO.

## General Settings

### Basic Configuration
- **Enabled** - Enable/disable the Prismic integration
- **API Endpoint (URL)** - Your Prismic repository API endpoint
- **Access Token** - Your Prismic API access token

### Multi-repository Support
- **Enabled** - Enable support for querying multiple Prismic repositories
- **Field** - Specify which field to use for matching documents across repositories (default: uid)

## Content Settings

### Language Configuration
- **Language** - Set the primary content language for Prismic documents
- **Language Fallback** - Configure a fallback language if content isn't available in primary language
- **Default Content Type** - Set the default content type for Prismic documents

### Content Features
- **Fetchlinks** - Specify which related fields to fetch from Prismic in one call (format: `customtype.field,customtype.field`)
- **Debugging** - Enable debug output in developer mode
- **Throw Exceptions** - Enable exception throwing for layout errors in developer mode
- **Preview** - Enable Prismic preview features

## Integration Fields

### Product Integration
- **Product Attributes** - Select which product attributes to make available in Prismic
- **Synchronize Disabled Products** - Choose whether to sync disabled products
- **Visibilities to Synchronize** - Select which product visibility types to sync
- **Access Token** - Set access token for integration fields API

### Default Available Attributes
The following attributes are always available:
- name
- image
- status
- short_description
- updated_at

## Webhook Configuration

- **Secret** - Set the webhook secret for authentication
- **Content Types for URL Rewrites** - Select content types for automatic URL rewrite generation
- **Cache Flush Content Types** - Select content types that trigger cache flush on update

## Sitemap Settings

### Prismic Pages Options
- **Include Content Types** - Select which content types to include in sitemap
- **Frequency** - Set update frequency for Prismic pages
- **Priority** - Set priority for Prismic pages (0.0 to 1.0)

## URL Rewrites

- **Content Types** - Select which content types should generate automatic URL rewrites
- Requires webhook setup for automatic URL rewrite generation

## Cache Management

- **Cache Flush Content Types** - Select which content types trigger cache flush when updated
- Requires webhook setup for automatic cache management

## Technical Details

### Configuration Paths

All configuration values are stored under the `prismicio` section in Magento's configuration:

```xml
<prismicio>
    <general>
        <enabled>0</enabled>
        <endpoint/>
        <token/>
    </general>
    <multirepo>
        <enabled>0</enabled>
        <field>uid</field>
    </multirepo>
    <content>
        <language/>
        <language_fallback/>
        <content_type/>
        <fetchlinks/>
        <allow_debug>1</allow_debug>
        <throw_exceptions>0</throw_exceptions>
        <allow_preview>0</allow_preview>
    </content>
    <integration_fields>
        <attributes>sku</attributes>
        <sync_disabled_products>0</sync_disabled_products>
        <visibility>4</visibility>
    </integration_fields>
</prismicio>
```

### Security Considerations

- Access tokens should be kept secure
- Preview mode should only be enabled when needed
- Debug mode should not be enabled in production
- Webhook secrets should be properly configured for security

### CSP Configuration

The module automatically configures Content Security Policy (CSP) rules for:
- script-src: *.prism.app-us1.com, *.prismic.io
- connect-src: *.prism.app-us1.com, *.prismic.io
- img-src: *.prism.app-us1.com, *.prismic.io

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
    * [Multi-repo](#multi-repo)
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
