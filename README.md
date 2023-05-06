# Stringfluent
[![Source](https://img.shields.io/badge/source-S_McDonald-blue.svg)](https://github.com/s-mcdonald/Stringfluent)
[![Source](https://img.shields.io/badge/license-MIT-gold.svg)](https://github.com/s-mcdonald/Stringfluent)

Stringfluent is a String helper that allo you to easily manipulate strings using a fluent and 
chainable syntax with Multibyte support baked in.

```php


        $str = Stringfluent::create("  any form of string, or stringable interface can be used. ");
        $str->toUpperCase()->trim()->truncate(3); // ANY
        
        $str = Stringfluent::create("プログラミングはクールです");
        $str->containsMultibyteCharacters(); // true

```

## Documentation

* [Installation](#installation)
* [Dependencies](#dependencies)


<a name="installation"></a>
## Installation

Via Composer. Run the following command from your project's root.

```
composer require s-mcdonald/stringfluent
```


<a name="dependencies"></a>
## Dependencies

*  Php 8.0



## License

Stringfluent is licensed under the terms of the [MIT License](http://opensource.org/licenses/MIT)
(See LICENSE file for details).
