---
title: Layout Handles in Prismic
description: Understanding and using layout handles with Prismic content in Magento 2
---

# Layout Handles

Layout handles allow you to customize the presentation of your Prismic content within Magento 2. This page explains the available layout handles and how to use them effectively.

## Available Layout Handles

The module provides several layout handles that you can use to customize how Prismic content is displayed:

- `prismicio_by_type_[content-type]` - Applied when viewing a Prismic document of a specific content type
- `prismicio_content` - The main container where Prismic content is rendered

## Usage Examples

Here's an example of how to customize the layout for a homepage content type:

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="prismicio_content">
            <!-- Remove default title if needed -->
            <referenceBlock name="page.main.title" remove="true"/>
            
            <!-- Custom title block -->
            <block name="homepage.title" class="Elgentos\PrismicIO\Block\Layout\PageTitle">
                <block class="Elgentos\PrismicIO\Block\Dom\Plain" template="data.title"/>
            </block>
            
            <!-- Main content template -->
            <block class="Elgentos\PrismicIO\Block\Template" template="homepage.phtml">
                <arguments>
                    <argument name="reference" xsi:type="string">data</argument>
                </arguments>
                
                <!-- Slice content -->
                <block name="page.body" class="Elgentos\PrismicIO\Block\Slices" template="body">
                    <block name="page.body.block" class="Elgentos\PrismicIO\Block\Container">
                        <arguments>
                            <argument name="reference" xsi:type="string">primary</argument>
                        </arguments>
                        <block class="Elgentos\PrismicIO\Block\Template" template="element/homepage-block.phtml">
                            <block class="Elgentos\PrismicIO\Block\Dom\Plain" name="block.width" template="width" />
                            <block class="Elgentos\PrismicIO\Block\Dom\Plain" name="block.image" template="image" />
                            <block class="Elgentos\PrismicIO\Block\Dom\Link" name="block.link" template="link" />
                        </block>
                    </block>
                </block>
            </block>
        </referenceContainer>
    </body>
</page>
```

## Best Practices

1. Always use meaningful block names that reflect their purpose
2. Group related blocks using container blocks
3. Remove default blocks that aren't needed (like page.main.title) when they conflict with your Prismic content
4. Keep your layout XML organized and well-commented for maintainability

Remember that layout handles should be placed in your theme's layout directory following Magento's standard layout structure:

```
app/design/frontend/<Vendor>/<theme>/Elgentos_PrismicIO/layout/
```

## Dynamic Layout Handles

[Need info about any dynamic handles generated based on Prismic content] 