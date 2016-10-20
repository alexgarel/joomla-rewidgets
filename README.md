joomla-rewidgets
#################

A joomla plugin to add elements via shortcodes based on regexp.

**Warning:** This is a very rough extension.

Usage
#######

Adding widgets
===============

To add a new code, go to the plugin and edit it.

In the text area you can define widget using this kind of definition:

```
====
{box text="Sample" color="#000000"}
----
<span style="border: solid 1px {color};padding: 3px;">{text}</span>
====
{pullright title="title" text="On the right"}
----
<div style="float:right; padding: 3px;"><h2>{title}</h2><p>{text}</p></div>
====
```

The `====` delimit widget definition (exactly four `=` characters),
while the `----` delimit the widget declaration and its template.

The widget declaration (first part) mimic the widget as it will be used,
with a name and properties.
Properties values defines default values.

The template part, defines the text by which the widget will be replaced.
You can use properties, enclosing them in curly-brackets.

Using widgets
=============

Widgets can be used in any article.

User simply use the widget declaration, eventually providing values for properties.

With definitions above, a user could add:
`{box text="Inside a box"}`
to get a Inside a box surounded by a black frame in his/her article.

Note that depending on your template and where they are inserted, this can lead to non valid html.
