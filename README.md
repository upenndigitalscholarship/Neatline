![Neatline Logo](http://neatline.org/images/neatline-logo-rgb.png)

# Neatline

**Neatline is a lightweight, extensible framework for creating interactive editions of visual materials - maps, paintings, photographs, and anything else that can be captured as an image.**

Built as a plugin for [Omeka](http://omeka.org/), a collection-management framework developed by the [Roy Rosenzweig Center for History and New Media](http://chnm.gmu.edu/), Neatline adds a digital map-making environment that makes it easy to represent geospatial information as a collection of "records" plotted on a map, which can be bound together into interactive exhibits that tell stories and make arguments.

Designed for scholars, archivists, journalists, and students, Neatline provides a flexible set of tools that can be adapted to fit the needs of a wide range of digital mapping projects. In addition to the core content management system, Neatline exposes a flexible programming API and "sub-plugin" system makes it easy for developers to add custom functionality for specific projects - everything from simple UI widgets up to really elaborate modifications that extend the core data model and add completely new interactions.

  - For general information and demos, head over to **[neatline.org](http://neatline.org/)**.

  - Read the docs at **[docs.neatline.org](http://docs.neatline.org/)**.

  - If you found a bug or thought of a new feature, file a ticket on the **[issue tracker](https://github.com/scholarslab/Neatline/issues)**.

# NeatlinePenn

*[A Penn Libraries Digital Scholarship Project](https://guides.library.upenn.edu/digital-scholarship)*

In addition to the functionality described above, NeatlinePenn offers the ability to export exhibits as CSV and/or Geojson files.  

## Getting Started

### Prerequisites

Installing this plugin requires having a live Omeka Classic site to install it into.  More information on how to deploy an Omeka classic site can be found on our [github](https://github.com/upenndigitalscholarship/dsdocs/blob/master/omeka_install.md) or on the [Omeka installation guide](https://omeka.org/classic/docs/Installation/Installation/).
Both of these installation methods require an Ubuntu server with Apache, mySQL, and PHP installed.

### Installing

Once you have an Omeka Classic site up and running, you must clone the NeatlinePenn plugin into the plugins folder. Note that if you already have a version Neatline plugin installed, you should uninstall it before continuing.

```
cd /var/www/html/plugins
git clone https://github.com/upenndigitalscholarship/Neatline.git
```

Once your have cloned the repository into your plugins folder, simply visit the admin page of your Omeka site and navigate to the plugins page, which should be in the top right navigation bar.  You should see Neatline listed on this page and can install it by pressing the install button to the right of the plugin.  

Test to ensure that Neatline is working correctly by following the instructions below.

## Running the tests

After installing the plugin, it should appear in the at the bottom of the sidebar of your admin page.  Follow the instructions for installing and testing the [CsvImport](https://github.com/upenndigitalscholarship/plugin-CsvImport) plugin, which can be found in its [README](https://github.com/upenndigitalscholarship/plugin-CsvImport/blob/master/README.md). Import the new items into Neatline by navigating to the Neatline plugin, creating a Test exhibit with the default setup, and selecting import. Once the import is complete, export the exhibit and download both the Csv and Geojson file to confirm that they contain the correct information.


## Authors

* **Sasha Renninger** - *Project Lead* - [Sasha](https://github.com/sashafr)
* **Haoran Shao** - *Export Function*
* **Breanna Porter** - *Refactoring for general use* - [Breanna](https://github.com/breannamporter)

See also the list of [contributors](https://github.com/upenndigitalscholarship/NeatlinePenn/graphs/contributors) who participated in this project.
