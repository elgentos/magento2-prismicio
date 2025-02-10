---
title: Prismic Templates
description: Creating and working with Prismic templates in Magento 2
---

# Templates

Templates define how your Prismic content is rendered in Magento 2. Here's how to work with templates effectively.

## Template Structure

A typical Prismic template consists of multiple parts:

1. The main template file
2. Element templates for specific content blocks
3. Layout configuration that ties it all together

## Example Implementation

Here's a complete example showing how to structure templates for a homepage:

1. Create your main template (`homepage.phtml`):

```php
<?php declare(strict_types=1);
/** @var $block \Elgentos\PrismicIO\Block\Template */
?>
<section class="content-wrap">
    <div class="grid lg:grid-cols-6">
        <?php echo $block->getChildHtml('page.body'); ?>
    </div>
</section>
```

2. Create your element template (`element/homepage-block.phtml`) for individual content blocks:

```php
<?php declare(strict_types=1);
/** @var $block \Elgentos\PrismicIO\Block\Template */
$homepageBlock = $block->getContext();
?>
<div class="block <?= $block->getChildHtml('block.width') ?>">
    <a href="<?= $block->getChildHtml('block.link') ?>">
        <img src="<?= $block->getChildHtml('block.image') ?>" alt="">
    </a>
</div>
```

## Best Practices

1. Keep templates focused on presentation logic
2. Use meaningful template names that reflect their purpose
3. Organize templates in appropriate directories based on their function
4. Use PHP DocBlocks to properly type-hint block classes
5. Leverage block methods like `getChildHtml()` for structured content
6. Keep presentation logic in templates and structural logic in blocks 