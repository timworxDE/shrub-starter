# 🌿 Shrub: Static pages, made easy

Shrub is a lightweight PHP framework that specializes in simplifying the creation and management of static websites.
As an extension of the **Leaf PHP framework**, Shrub combines the simplicity of Leaf with powerful, modern web tools to take the burden off developers who need fast, low-maintenance static websites.
At the heart of the simplification is the **Blade** template engine, which allows you to create dynamic, reusable components for your static pages, making writing HTML intuitive and efficient.
Another feature is the use of **Vite** for optimized bundling of CSS and JavaScript files.

## Documentation

Since this project is based on Leaf and contains various packages, you can find more information about it here:

- [Leaf](https://leafphp.dev/docs/)
- [Blade](https://laravel.com/docs/12.x/blade)
- [Twig](https://twig.symfony.com/doc/3.x/)
- [Vite](https://vite.dev/guide/)

## Installation

Currently, just download the repo into your project destination.
Set the webroot to the _public_ directory.

Run ``composer install`` and ``npm install`` to install the required packages.
After installing the packages you can run ``npm run build`` to build the styles and javascript files.

## Basic Usage

Shrub uses the _pages_ directory as the structure of the website.
Each view corresponds to a page for the website, so _about.html.twig_ is called with _/about_.
Index files retain the folder name, such as an _index.html_ file.

By using Twig, you can also use its functions, such as ``{% extends %}``, ``{% inclue %}``, or ``{% block abc %}{% endblock %}``.
For more details, you can read the [Twig documentation](https://twig.symfony.com/doc/3.x/).

### Creating a page

After installing Shrub, edit the _index.html.twig_ file inside the _pages_ folder.

```
{% extends 'layouts/base.html.twig' %}

{% block title %}Page Titel{% endblock %}

{% block content %}
    <h1>Homepage</h1>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
    </p>
{% endblock %}
```

### Edit the layout

You can change the layout of your website by editing the file _templates/layouts/base.html.twig_.
You can also create new layouts in the same folder, but these would need to be integrated into the pages.

### Using the Formmailer

Shrub has its own function for sending forms by email.
To do this, the form must be sent via POST to _/\_formmail_.
The email sent is a plain text message where every input is listed with its value. (name: value)
Meta data and ignored fields are not included.

```
<form action="/_formmail" method="POST">
    <input type="hidden" name="from" value="from@example.org">
    <input type="hidden" name="to" value="to@example.org">
    <input type="hidden" name="subject" value="this is the subject">
    <input type="hidden" name="reply-to" value="email">

    <input type="text" name="name" placeholder="Your Name">
    <input type="email" name="email" placeholder="Your Email">
    <textarea name="message" placeholder="Your Message"></textarea>
    <button type="submit">Send</button>
</form>
```

The form has several meta fields:

- **from**
  - The sending email
- fromName
  - The sending name
- **to**
  - The recipient's email
- toName
  - The recipient's name
- **subject**
  - Subject of the mail
- reply-to
  - field name of the reply email
- success
  - Link to the success page
  - default will be the current page
- error
  - Link to the error page
  - default will be the current page
- ignore
  - Ignored fields
  - names connected by ``,``
- required
  - Required fields
  - names connected by ``,``

**Bold** fields are required.

## Configuration

Shrub uses a _config.yaml_ file located in the project root.
This file is read and loaded into ``Leaf\Config``.
This allows you to create your own configurations.

A _.env_ file can also be read for the Env variable. To do this, the ``load_env()`` function must be called.

## Versions

### v0.7 "Twig integration"

- Added Twig template engine
  - I've created [my own package](https://packagist.org/packages/timworx/leaf-twig) for easier usage inside Leaf
- Setup Vite for Twig
- Changed default pages to Twig syntax
- Changed default engine to twig
- Added default templates and pages for Blade to _examples_ directory

### v0.6 "First release"

- Moved project to _shrub_ directory
- Added mailer support
- Created Formmailer
  - Added route
  - Added validation
- **First release**

### v0.5

- Added custom config file
- Loading config file in Shrub initialisation
- Created config loading function ``_set_config()``
- Added default config
- Removed base ``.env`` file
  - Env vars are located in config file

### v0.4

- Added Blade template engine
- Setup Vite for Blade
- Made template engine configurable
- Created default page and layout
- Created 404 page and return it, if the URL is not found

### v0.3

- Added Vite to project
- Setup Vite and BareUI
- Initialisation of Shrub
  - Set default configurations

### v0.2

- Created the Shrub class as a wrapper for Leaf
- Created global function ``shrub()`` to get Shrub instance
- Created the "catch all" route (.*) to send pages
  - Route was added in ``run()`` function
- Created env loading function ``load_env()``

### v0.1

- Created directory structure (public, pages, assets, templates, src)
- Created basic ``index.php`` in _public_ folder for Leaf installation
  - Added ``.htaccess`` with matching front controller
- Added Leaf **BareUI**
- Created testing views
- Added ``.env`` support

## Roadmap

I will be adding a few more features in the future.
If you have any ideas, please let me know.

Planned features:

- Custom template engines
  - Easier handling to use your own template engines
- Custom error pages (partially implemented)
  - Using views to display custom error pages to the user
  - Currently, you can edit the 404 page in _/templates/error/404.blade.php_
- Image cache
  - I'd like to implement a feature to reduce the size of images so they're suitable for different devices. This should reduce traffic. The resulting images should be cached to keep loading times as short as possible.
- Bundeling / Packaging
  - Seperate the Shrub folder as its own package, to be installed inside existing projects
  - I'd also want to create a project base to use composers create project
- Commands
  - Adding commands for installation and updating

## Good to know

