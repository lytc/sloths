Lazy - A small php 5.4+ framework
====
[![Build Status](https://travis-ci.org/lytc/lazy.png?branch=master)](https://travis-ci.org/lytc/lazy)

Lazy is a PHP 5.4+ full stack framework that helps you to build quickly web application and APIs.

===
Features
- Modular applications
- Powerful router (replicated from [Sinatra](http://www.sinatrarb.com/))
- Template rendering
  + Nested layout support
  + Buildin view helpers (escape, assets tags, capture, pagination,...)
  + Easy to write your own custom view helper
- Object relation mapper (idea from Active Record pattern)
  + Eager loading associations
  + Dirty tracking
  + Lazy load support
  + Solving N+1 problem

===
Install
- Composer
```json
{
  "require": {
        "lazy/lazy": "2.*"
    }
}
```

===
System Requirements
- PHP 5.4+
- PHP PDO extension required if you use Db part
  
