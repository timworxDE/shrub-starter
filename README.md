# 🌿 Shrub: Static pages, made easy

Shrub is a lightweight PHP framework that specializes in simplifying the creation and management of static websites.
As an extension of the **Leaf PHP framework**, Shrub combines the simplicity of Leaf with powerful, modern web tools to take the burden off developers who need fast, low-maintenance static websites.
At the heart of the simplification is the **Blade** template engine, which allows you to create dynamic, reusable components for your static pages, making writing HTML intuitive and efficient.
Another feature is the use of **Vite** for optimized bundling of CSS and JavaScript files.

## Installation

Currently, just download the repo into your project destination.
Set the webroot to the _public_ directory.

Run ``composer install`` and ``npm install`` to install the required packages.
After installing the packages you can run ``npm run build`` to build the styles and javascript files.

## Basic Usage

Shrub uses the _pages_ directory as the structure of the website.
Each view corresponds to a page for the website, so _about.blade.php_ is called with _/about_.
Index files retain the folder name, such as an _index.html_ file.

By using Blade, you can also use its functions, such as ``@section``, ``@include``, or ``@extends``.
For more details, you can read the [Blade documentation](https://laravel.com/docs/12.x/blade#blade-directives).

### Creating a page

After installing Shrub, edit the _index.blade.php_ file inside the _pages_ folder.

```
@extends('layouts.base')
@section('title', 'Page Title')
@section('content')
    <h1>Homepage</h1>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
    </p>
@endsection
```

### Edit the layout

You can change the layout of your website by editing the file _templates/layouts/base.blade.php_.
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

## Roadmap

I will be adding a few more features in the future.
If you have any ideas, please let me know.

Planned features:

- Twig template engine implementation
  - I would like to provide the Twig template engine not only for Shrub, but also for Leaf itself. 
  - In addition, Twig should be the preferred engine.
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