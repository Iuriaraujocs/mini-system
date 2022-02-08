## Mini System

This project aimed to achieve a small framework in PHP language from scratch.
The ideia is development from scratch everything that a developer needs to make a profissional project without
using any famous framework from market.

My mini-system is a way I found to study how real frameworks really work under the hood, and I've been created along of my
programmer development, mainly in the beggining for learning purposes and it was not thought to be commercialized or anything like that.

Many classes I created to understand how works, how to do and was designed in order to obtain flexibility and modularized systems easily.

An example of an implementation using this mini framework is:

<h1 align="center">
    <a href="https://github.com/Iuriaraujocs/products-categories-manager">🔗 Products/Categories E-commerce Manager</a>
</h1>


### The main feature is:

- Functional and secure Bootstrap for application (load this framework)
- Intuitive Route class (inspired in Django route system)
- Helper functions to be used in all the project scope  (see below)
- Friendly interface to template engine system (using Smarty library)
- Own ORM class from scratch inspired in Eloquent (Laravel ORM)
- Persistence class to real connect with database
- Base Controller to common controllers methods


### How to use
```php
    composer require iuriaraujocs/mini-system
  ```

## Next steps:
- Implement cache system
- Minify css and javascript files
- PHPunit support
- Dependecy Injection support
- Custom Exceptions treatment 
- CLI interface, and commands support
- Refatored code to be according with PSRs (PHP Standards Recommendations)
- Install grumPHP and configured PHPCS, PHPMD, PHPCPD
- Create classes to https helpers (using curl or guzzle)
- Add suport to messaging tools to micro-services communication



## Helpers functions until now:

  - session_initialize (to secure session initialize)
  - app_log   (a monolog interface)
  - app_post  (get post variable by name when it exists)
  - app_get  
  - app_request
  - app_config (get configured data assign by user)
  - app_session (get session variable)
  - app_unsession (unsession variable)
  - clearBrowserCache  (force browser to clear its cache)
  - is_server
  - is_local
  - app_get_env  (get variables from env file)
  - app_set_env
  - app_path (get root path of project. By default is directory before this minisystem, but can be configured)
  - app_https (force to redirect http to https)
  - app_upload_img
  - app_encode_csv
  - app_decode_csv
  - others string helpers
  - other array helpers
  
