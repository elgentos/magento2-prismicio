---
title: Console Commands
description: Technical documentation for Magento CLI commands in the Prismic module
---

# Console Commands

The module provides several console commands for managing Prismic integration.

## Available Commands

### Scaffold Command
```php
Elgentos\PrismicIO\Console\Command\Scaffold
```

Creates necessary files for a new Prismic custom type.

```bash
bin/magento prismic:scaffold <custom-type>
```

### Slice Machine Commands

#### Initialize
```php
Elgentos\PrismicIO\Console\Command\SliceMachineInit
```

Initializes Slice Machine configuration:

```bash
bin/magento elgentos:prismic:slice-machine:init --store-code=<store>
```

#### Start
```php
Elgentos\PrismicIO\Console\Command\SliceMachineStart
```

Starts the Slice Machine development environment:

```bash
bin/magento elgentos:prismic:slice-machine:start --store-code=<store>
```

## Implementation Details

### Scaffold Command

Creates:
- Layout files
- Templates
- Route configurations
- JSON schema

### Slice Machine Integration

- Creates configuration files
- Sets up development environment
- Handles multi-store setups

## Usage Examples

### Creating a New Content Type

```bash
# Create a new blog post type
bin/magento prismic:scaffold blog_post
```

### Setting Up Slice Machine

```bash
# Initialize for specific store
bin/magento elgentos:prismic:slice-machine:init --store-code=default

# Start development environment
bin/magento elgentos:prismic:slice-machine:start --store-code=default
```

## Best Practices

1. **Scaffolding**
   - Use consistent naming
   - Follow content type patterns
   - Verify generated files

2. **Slice Machine**
   - Use store-specific configurations
   - Maintain version control
   - Document custom slices

3. **Development**
   - Test generated code
   - Follow coding standards
   - Update documentation 