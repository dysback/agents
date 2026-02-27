# Project: Agents
This is a Project for training Claude agent workflows for PHP and JS

## Stack
- PHP + Mustache, 
- JavaScript + Handlebars, 
- MariaDb

## Rules
- Always write tests for new functions

# Code Style
- Javascript
  - Always add JSDoc comments to exported functions
  - Respect Airbnb codiing rules
  - Don't use any frameworks
  - Never use `any` types

- PHP
  - Make code PHP 8.5 compatibile
  - Always add PHPDoc comments to the class public dunctions and class itself
  - Don't use any frameworks
  - Respect PSR 3.0 coding standard
  - Use Enums for small list



# Architecture
- Public files /src/public
  - api.php - router for JSON REST api (Controllers)
  - app.php - router for HTML pages (Pages)
  - js - JavaScript files
  - css - CSS styles
  - assets - images, fonts and other public files
  - tests Unit tests
- Database models in /src/model
- Controllers in /src/controller
- Pages
  - Logic (php) in /pages
  - Templates (mustache) /pages/templates
  - Master page templates /pages/templates