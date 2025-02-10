---
title: Exceptions
description: Technical documentation for Prismic module exceptions in Magento 2
---

# Exceptions

The module provides several custom exceptions for handling various error conditions.

## Core Exceptions

### ApiNotEnabledException
Thrown when attempting to use the API while it's disabled.

### ContextNotFoundException
Thrown when a requested document context cannot be found.

### DocumentNotFoundException
Thrown when a requested Prismic document doesn't exist.

### RouteNotFoundException
Thrown when a requested route cannot be found.

### StaticBlockNotFoundException
Thrown when a static block cannot be found.

## Implementation

### Exception Handling

```php
try {
    $document = $this->getDocument();
} catch (DocumentNotFoundException $e) {
    // Handle missing document
} catch (ContextNotFoundException $e) {
    // Handle missing context
}
```

### Configuration-Based Handling

The module can be configured to handle exceptions differently based on environment:

```xml
<prismicio>
    <content>
        <throw_exceptions>0</throw_exceptions>
    </content>
</prismicio>
```

## Best Practices

1. **Exception Types**
   - Use specific exceptions
   - Maintain hierarchy
   - Document error conditions

2. **Error Handling**
   - Graceful degradation
   - Proper logging
   - User-friendly messages

3. **Development**
   - Debug mode considerations
   - Proper error reporting
   - Exception documentation 