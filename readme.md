# CodeIgniter Multi Lang

Allows for the switching of languages within CodeIgniter, with a focus on support for SEO friendly URLs, eg.

- http://yoursite.com/welcome (English)
- http://yoursite.com/bienvenu (French)
- http://yoursite.com/bienvenidos (Spanish)

SEO URLs default to using language codes if no translation is provided, eg.

- http://yoursite.com/fr/contact (French)
- http://yoursite.com/es/contact (Spanish)

## Support

1. CodeIgniter 3+

## Installation and use

1. Copy file application/core/MY_Lang.php to your project if your change subclass_prefix in config.php change this file name to match your prefix
2. Copy Codeigniter Multi Lang config in application/config/config.php to your config.
3. add language you want

## note
function _t in helper is optional