# concrete5-attribute-forms

Create concrete5 forms using attributes.

_Work in progress - not ready for production!_

## Todos / Ideas

* Attach actions to form types, e.g. "send mail", "write file", "create user"
* Use SPAM control
* Use doctrine instead of hacky models
* Mail templates using attribute handles as placeholders

## Attributes

This package uses the concrete5 attribute system and is thus very flexible if need to add new types of form fields.
Since version 5.7 of concrete5 hasn't been on the market for very long the list of custom attribute types is limited. We'll update this list in the future:

* https://github.com/Remo/concrete5-attribute-plain-text Allows you to add static text to give your users hints or additional information about a form field
* http://www.concrete5.org/marketplace/addons/colorpicker-attribute Adds a color picker to your forms
* http://www.concrete5.org/marketplace/addons/fileset-attribute1 Adds a fileset list to your form
* https://github.com/Remo/concrete5-attribute-masked-input An attribute where you can restrict the input using a format mask
