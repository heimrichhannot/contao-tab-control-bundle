# Contao Tab Control Bundle

This Bundle brings Bootstrap 4 Tabs to Contao. 

## Features
* add 3 new Content Element: Start, Seperator, Stop
* option to store last open tab in session (sessionStorage)
* [Encore Bundle](https://github.com/heimrichhannot/contao-encore-bundle) support


<p align="center">
    <img src="docs/img/frontend.png" width="800">
</p>
<p align="center">
    <img src="docs/img/backend.png" width="800">
</p>


## Install

A note before install: this bundle does not come with bootstrap styles.

1. Install with composer:

    ```
    composer require heimrichhannot/contao-tab-control-bundle
    ```

1. Update Database

1. If you use Encore Bundle, run symfony command `encore:prepare` and `yarn encore [dev|prod]`

1. If you use Encore Bundle, active the `contao-tab-control-bundle` entry in your layout or the pages you need them.

1. If you don't use Encore bundle and have bootstrap already included, you may want to unset `$GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle_bootstrap-tabs']` to don't have bootstrap `tab.js` and `util.js` included twice.

## Usage

This bundle brings three new content elements. To setup a tab section, choose the tab start element. For each new tab add an tab seperator element within the tab start and tab end element. The tab names are set in the tab start and the tab seperator elements.