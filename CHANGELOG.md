# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2021-05-17

- added php cs fixer config
- fixed some typos
- removed bootstrap as dependency for non-encore implementation in order to avoid duplicate imports

## [0.6.0] - 2021-05-14

- moved to bootstrap 5, contao 4.9 and php 7.4
- refactoring

## [0.5.0] - 2020-08-03

- added migration for bootstrapper tabs to migration command
- added direct migration option to migration command
- updated encore config
- increased minimum encore bundle support to 1.5
- fixed migration command registration
- fixed a bug to select first tab as active tab

## [0.4.2] - 2019-12-12

- fixed removed trailing slash if external url is given as TabLink

## [0.4.1] - 2019-10-24

- updated UPGRADE.md

## [0.4.0] - 2019-10-23

- added migration command
- added support automatic entries points inclusion of encore bundle 1.3
- BREAKING: fixed typo in separator element type (see UPGRADE.md)

## [0.3.0] - 2019-09-16

- added optional link for tab title

## [0.2.2] - 2019-05-11

- removed yarn.lock

## [0.2.1] - 2019-05-15

- made encore bundle an optional dependency
- added missing utils bundle dependency

## [0.2.0] - 2019-05-14

- structure tab helper now works with recursive tabs
- fixed first tab was not selected if no cached tab
- fixed legacy config for encore bundle
