# UPGRADE

## From versions < 0.4

* TabControlSeparatorElement type has slightly changed to tabcontrolSeparator. Use migration script to updated your elements
* TabControlSeparatorElement template name has slightly changed to ce_tabcontrol_separator_default. If have custom tab templates, you need to adept the names to made them show up in the contao backend.
* if you use encore bundle from version 1.3 you can remove active entries for tab control bundle as they added now added automatically

## From versions < 0.3
* `ce_tabcontrol_start_default.html5` template slightly changes due new option. If you have overridden the the template, you need to update your template, at least you need to the css class of the tab link from `nav-link` to `tab-link`. See the [commit](https://github.com/heimrichhannot/contao-tab-control-bundle/commit/9cc7183521b239aff01ddce693ad18b01b4b406e#diff-58debeb61289f0fb116e8dfa7f11a30a) for detailed changes.

