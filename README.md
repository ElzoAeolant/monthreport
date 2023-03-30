# [AEOLANT API Reportes ]

![version](https://img.shields.io/badge/version-1.0.1-blue.svg) ![license](https://img.shields.io/badge/license-MIT-blue.svg)

## Prerequisites

If you don't already have an Apache local environment with PHP and MySQL, use one of the following links:

 - Windows: https://updivision.com/blog/post/beginner-s-guide-to-setting-up-your-local-development-environment-on-windows
 - Linux: https://howtoubuntu.org/how-to-install-lamp-on-ubuntu
 - Mac: https://wpshout.com/quick-guides/how-to-install-mamp-on-your-mac/

Also, you will need to install Composer: https://getcomposer.org/doc/00-intro.md

## Installation

1. Unzip the downloaded archive
2. Copy and paste **monthreport-master** folder in your **projects** folder. Rename the folder to your project's name
3. In your terminal run `composer install`
4. Copy `.env.example` to `.env` and updated the configurations (mainly the database configuration)
5. In your terminal run `php artisan key:generate`
6. Run `php artisan migrate --seed` to create the database tables and seed the roles and users tables
7. Run `php artisan storage:link` to create the storage symlink (if you are using **Vagrant** with **Homestead** for development, remember to ssh into your virtual machine and run the command from there).

## Usage

To start testing the Pro theme, register as a user or log in using one of the default users: 

- admin type - **admin@lightbp.com** with the password **secret**
- creator type - **creator@lightbp.com** with the password **secret**
- member type - **member@lightbp.com** with the password **secret**

Make sure to run the migrations and seeders for the above credentials to be available.

In addition to the features included in the free preset, the Pro theme also has a role management example with an updated user management, as well as tag management, category management and item management examples. All the necessary files (controllers, requests, views) are installed out of the box and all the needed routes are added to `routes/web.php`. Keep in mind that all the features can be viewed once you log in using the credentials provided above or by registering your own user.

Each role has a different privilege level and can perform a certain number of actions according to this level.  

A **member type** user can log in, update his profile and view a list of added items.  
A **creator type** user can log in, update his profile and perform actions on categories, tags and items.  
A **admin type** user can log in, update his profile and perform actions on categories, tags, items, roles and users.  

## Documentation
working 

## File Structure
```
|   artisan
|   composer.json
|   composer.lock
|   package.json
|   phpunit.xml
|   README.md
|   server.php
|   webpack.mix.js
|   yarn.lock
|   
+---app
│   ├── Category.php
│   ├── Console
│   │   └── Kernel.php
│   ├── Exceptions
│   │   └── Handler.php
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── Auth
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── ResetPasswordController.php
│   │   │   │   └── VerificationController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── Controller.php
│   │   │   ├── HomeController.php
│   │   │   ├── ItemController.php
│   │   │   ├── PageController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── RoleController.php
│   │   │   ├── TagController.php
│   │   │   └── UserController.php
│   │   ├── Kernel.php
│   │   ├── Middleware
│   │   │   ├── Authenticate.php
│   │   │   ├── CheckForMaintenanceMode.php
│   │   │   ├── EncryptCookies.php
│   │   │   ├── RedirectIfAuthenticated.php
│   │   │   ├── TrimStrings.php
│   │   │   ├── TrustProxies.php
│   │   │   └── VerifyCsrfToken.php
│   │   └── Requests
│   │       ├── CategoryRequest.php
│   │       ├── ItemRequest.php
│   │       ├── PasswordRequest.php
│   │       ├── ProfileRequest.php
│   │       ├── RoleRequest.php
│   │       ├── TagRequest.php
│   │       └── UserRequest.php
│   ├── Item.php
│   ├── Observers
│   │   ├── ItemObserver.php
│   │   └── UserObserver.php
│   ├── Policies
│   │   ├── CategoryPolicy.php
│   │   ├── ItemPolicy.php
│   │   ├── RolePolicy.php
│   │   ├── TagPolicy.php
│   │   └── UserPolicy.php
│   ├── Providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   ├── Role.php
│   ├── Rules
│   │   └── CurrentPasswordCheckRule.php
│   ├── Tag.php
│   └── User.php
├── artisan
├── bootstrap
│   ├── app.php
│   └── cache
│       ├── config.php
│       ├── packages.php
│       └── services.php
├── composer.json
├── composer.lock
├── config
│   ├── app.php
│   ├── auth.php
│   ├── broadcasting.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── hashing.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   ├── session.php
│   └── view.php
├── database
│   ├── factories
│   │   └── UserFactory.php
│   ├── migrations
│   │   ├── 2014_10_12_100000_create_password_resets_table.php
│   │   ├── 2019_01_15_100000_create_roles_table.php
│   │   ├── 2019_01_15_110000_create_users_table.php
│   │   ├── 2019_01_17_121504_create_categories_table.php
│   │   ├── 2019_01_21_130422_create_tags_table.php
│   │   ├── 2019_01_21_163402_create_items_table.php
│   │   ├── 2019_01_21_163414_create_item_tag_table.php
│   │   ├── 2019_03_06_132557_add_photo_column_to_users_table.php
│   │   ├── 2019_03_06_143255_add_fields_to_items_table.php
│   │   └── 2019_03_20_090438_add_color_tags_table.php
│   └── seeds
│       ├── CategoriesTableSeeder.php
│       ├── DatabaseSeeder.php
│       ├── ItemsTableSeeder.php
│       ├── RolesTableSeeder.php
│       ├── TagsTableSeeder.php
│       └── UsersTableSeeder.php
├── package.json
├── phpunit.xml
├── public
│   ├── css
│   │   ├── bootstrap.min.css
│   │   ├── demo.css
│   │   ├── documentation.css
│   │   └── light-bootstrap-dashboard.css
│   ├── favicon.ico
│   ├── fonts
│   │   ├── nucleo-icons.eot
│   │   ├── nucleo-icons.svg
│   │   ├── nucleo-icons.ttf
│   │   ├── nucleo-icons.woff
│   │   └── nucleo-icons.woff2
│   ├── img
│   │   ├── apple-icon.png
│   │   ├── bg11.jpg
│   │   ├── bg4.jpg
│   │   ├── bg5.jpg
│   │   ├── bg6.jpg
│   │   ├── bg7.jpg
│   │   ├── bg8.jpg
│   │   ├── blog-1.jpg
│   │   ├── blog-2.jpg
│   │   ├── blog-3.jpg
│   │   ├── blog-4.jpg
│   │   ├── blog-5.jpg
│   │   ├── carousel_1.jpg
│   │   ├── carousel_2.jpg
│   │   ├── carousel_3.jpg
│   │   ├── default-avatar.png
│   │   ├── faces
│   │   │   ├── face-0.jpg
│   │   │   ├── face-1.jpg
│   │   │   ├── face-2.jpg
│   │   │   ├── face-3.jpg
│   │   │   ├── face-4.jpg
│   │   │   ├── face-5.jpg
│   │   │   ├── face-6.jpg
│   │   │   ├── face-7.jpg
│   │   │   ├── margot.jpg
│   │   │   └── tim_vector.jpe
│   │   ├── favicon.ico
│   │   ├── flags
│   │   │   ├── AD.png
│   │   │   ├── AE.png
│   │   │   ├── AG.png
│   │   │   ├── AM.png
│   │   │   ├── AR.png
│   │   │   ├── AT.png
│   │   │   ├── AU.png
│   │   │   ├── BE.png
│   │   │   ├── BF.png
│   │   │   ├── BG.png
│   │   │   ├── BO.png
│   │   │   ├── BR.png
│   │   │   ├── CA.png
│   │   │   ├── CD.png
│   │   │   ├── CG.png
│   │   │   ├── CH.png
│   │   │   ├── CL.png
│   │   │   ├── CM.png
│   │   │   ├── CN.png
│   │   │   ├── CO.png
│   │   │   ├── CZ.png
│   │   │   ├── DE.png
│   │   │   ├── DJ.png
│   │   │   ├── DK.png
│   │   │   ├── DZ.png
│   │   │   ├── EE.png
│   │   │   ├── EG.png
│   │   │   ├── ES.png
│   │   │   ├── FI.png
│   │   │   ├── FR.png
│   │   │   ├── GA.png
│   │   │   ├── GB.png
│   │   │   ├── GM.png
│   │   │   ├── GT.png
│   │   │   ├── HN.png
│   │   │   ├── HT.png
│   │   │   ├── HU.png
│   │   │   ├── ID.png
│   │   │   ├── IE.png
│   │   │   ├── IL.png
│   │   │   ├── IN.png
│   │   │   ├── IQ.png
│   │   │   ├── IR.png
│   │   │   ├── IT.png
│   │   │   ├── JM.png
│   │   │   ├── JO.png
│   │   │   ├── JP.png
│   │   │   ├── KG.png
│   │   │   ├── KN.png
│   │   │   ├── KP.png
│   │   │   ├── KR.png
│   │   │   ├── KW.png
│   │   │   ├── KZ.png
│   │   │   ├── LA.png
│   │   │   ├── LB.png
│   │   │   ├── LC.png
│   │   │   ├── LS.png
│   │   │   ├── LU.png
│   │   │   ├── LV.png
│   │   │   ├── MG.png
│   │   │   ├── MK.png
│   │   │   ├── ML.png
│   │   │   ├── MM.png
│   │   │   ├── MT.png
│   │   │   ├── MX.png
│   │   │   ├── NA.png
│   │   │   ├── NE.png
│   │   │   ├── NG.png
│   │   │   ├── NI.png
│   │   │   ├── NL.png
│   │   │   ├── NO.png
│   │   │   ├── OM.png
│   │   │   ├── PA.png
│   │   │   ├── PE.png
│   │   │   ├── PG.png
│   │   │   ├── PK.png
│   │   │   ├── PL.png
│   │   │   ├── PT.png
│   │   │   ├── PY.png
│   │   │   ├── QA.png
│   │   │   ├── RO.png
│   │   │   ├── RU.png
│   │   │   ├── RW.png
│   │   │   ├── SA.png
│   │   │   ├── SE.png
│   │   │   ├── SG.png
│   │   │   ├── SL.png
│   │   │   ├── SN.png
│   │   │   ├── SO.png
│   │   │   ├── SV.png
│   │   │   ├── TD.png
│   │   │   ├── TJ.png
│   │   │   ├── TL.png
│   │   │   ├── TR.png
│   │   │   ├── TZ.png
│   │   │   ├── UA.png
│   │   │   ├── US.png
│   │   │   ├── VE.png
│   │   │   ├── VN.png
│   │   │   └── YE.png
│   │   ├── full-screen-image-1.jpg
│   │   ├── full-screen-image-2.jpg
│   │   ├── full-screen-image-3.jpg
│   │   ├── full-screen-image-4.jpg
│   │   ├── laravel.svg
│   │   ├── loading-bubbles.svg
│   │   ├── margot.jpg
│   │   ├── mask.png
│   │   ├── new_logo.png
│   │   ├── sidebar-1.jpg
│   │   ├── sidebar-2.jpg
│   │   ├── sidebar-3.jpg
│   │   ├── sidebar-4.jpg
│   │   ├── sidebar-5.jpg
│   │   └── tim_80x80.png
│   ├── index.php
│   ├── js
│   │   ├── core
│   │   │   ├── bootstrap.min.js
│   │   │   ├── jquery.3.2.1.min.js
│   │   │   └── popper.min.js
│   │   ├── demo.js
│   │   ├── light-bootstrap-dashboard.js
│   │   └── plugins
│   │       ├── bootstrap-datetimepicker.js
│   │       ├── bootstrap-notify.js
│   │       ├── bootstrap-selectpicker.js
│   │       ├── bootstrap-switch.js
│   │       ├── bootstrap-table.js
│   │       ├── bootstrap-tagsinput.js
│   │       ├── chartist.min.js
│   │       ├── fullcalendar.min.js
│   │       ├── jasny-bootstrap.min.js
│   │       ├── jquery.bootstrap-wizard.js
│   │       ├── jquery.dataTables.min.js
│   │       ├── jquery-jvectormap.js
│   │       ├── jquery.validate.min.js
│   │       ├── moment.min.js
│   │       ├── nouislider.js
│   │       ├── perfect-scrollbar.jquery.min.js
│   │       └── sweetalert2.min.js
│   ├── robots.txt
│   └── storage -> /home/updivision/Code/light-bootstrap-dashboard/storage/app/public
├── README.md
├── resources
│   ├── lang
│   │   └── en
│   │       ├── auth.php
│   │       ├── pagination.php
│   │       ├── passwords.php
│   │       └── validation.php
│   └── views
│       ├── alerts
│       │   ├── errors.blade.php
│       │   ├── error_self_update.blade.php
│       │   ├── feedback.blade.php
│       │   ├── migrations_check.blade.php
│       │   └── success.blade.php
│       ├── auth
│       │   ├── login.blade.php
│       │   ├── passwords
│       │   │   ├── email.blade.php
│       │   │   └── reset.blade.php
│       │   └── register.blade.php
│       ├── categories
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── index.blade.php
│       ├── items
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── index.blade.php
│       ├── layouts
│       │   ├── app.blade.php
│       │   ├── footer
│       │   │   └── nav.blade.php
│       │   └── navbars
│       │       ├── navbar.blade.php
│       │       ├── navs
│       │       │   ├── auth.blade.php
│       │       │   └── guest.blade.php
│       │       └── sidebar.blade.php
│       ├── pages
│       │   ├── calendar.blade.php
│       │   ├── charts.blade.php
│       │   ├── components
│       │   │   ├── buttons.blade.php
│       │   │   ├── grid.blade.php
│       │   │   ├── icons.blade.php
│       │   │   ├── notifications.blade.php
│       │   │   ├── panels.blade.php
│       │   │   ├── sweet-alert.blade.php
│       │   │   └── typography.blade.php
│       │   ├── dashboard.blade.php
│       │   ├── forms
│       │   │   ├── extended-forms.blade.php
│       │   │   ├── regular-forms.blade.php
│       │   │   ├── validation-forms.blade.php
│       │   │   └── wizard.blade.php
│       │   ├── lock.blade.php
│       │   ├── maps
│       │   │   ├── fullscreen.blade.php
│       │   │   ├── google.blade.php
│       │   │   └── vector.blade.php
│       │   ├── sidebarstyle.blade.php
│       │   └── tables
│       │       ├── bootstrap.blade.php
│       │       ├── datatables.blade.php
│       │       ├── extended.blade.php
│       │       └── regular.blade.php
│       ├── profile
│       │   └── edit.blade.php
│       ├── roles
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── index.blade.php
│       ├── tags
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── index.blade.php
│       └── users
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── index.blade.php
├── routes
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   └── web.php
├── server.php
├── storage
│   ├── app
│   │   └── public
│   │       ├── pictures
│   │       │   
│   │       └── profile
```
