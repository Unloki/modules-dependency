# Super Term Reference Autocomplete Widget

The Straw (Super Term Reference Autocomplete Widget) module provides
a new interface for associating taxonomy terms with content using a
term reference field. It looks just like a normal placeholder select
widget, but it shows the whole tag hierarchy when displaying existing
values, and it shows and searches the whole hierarchy when finding
matches for the autocomplete dropdown. If term creation is enabled,
it also allows a new term to be created along with all of its parents
that don't exist already by simply using the >> delimiter.

For a full description of the module, visit the
[project page](https://www.drupal.org/project/straw).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/straw).


## Table of contents

- Requirements
- Installation
- Configuration
- Basic usage


## Requirements

This module requires no modules outside of Drupal core.


## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).


## Configuration

Configuring a field widget to use Straw requires two steps. First, in the
settings for the field, choose the "Straw selection" reference method. Then, for
the field widget settings on the Manage Form Display tab, choose "`Autocomplete(Straw style)`".


## Basic usage

Once Straw is configured for a field, that field will show all the parents of
the selected terms in addition to the term itself. Each level of the hierarchy
is separated from the others by two right-facing arrows (>>), and if term
creation is enabled for the field, these same separators can also be used
between levels of the hierarchy when typing in a new term, and all terms in the
hierarchy that don't already exist will get created. For instance, entering
"`Travel >> Tourist Destinations`" will create two terms, "`Travel`"
and "`Tourist Destinations`", with the latter as a child of the former.
