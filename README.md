[![Build Status](https://travis-ci.org/cartbooking/cartbooking.svg?branch=master)](https://travis-ci.org/cartbooking/cartbooking)
[![Code Climate](https://codeclimate.com/github/cartbooking/cartbooking.svg)](https://codeclimate.com/github/cartbooking/cartbooking)

# CartBooking
Booking platform for cart witnessing

## Requires

* PHP7
* MySQL
* Nginx
* Composer

## Installation

In order to install the app, we need to create a DB, and import the sql file that is found
under the `sql/` folder.

The credentials must be set at the web server layer as environment variables:

- DB_USERNAME
- DB_HOST
- DB_PASSWORD
- DB_NAME

It's also required to install `composer` and run `composer install` 
in order to install the dependencies.

## Folder structures

This is an immature structure. It is an application using Slilex as microframework,
and we may want to move to use Symfony eventually. The core is entirely agnostic of
any underlaying library, so it should very easy to plug/play new libraries or dependencies
should the need arise.

Under `src` we will find most of the important areas.

### `src/Application`

Here is where we can find most of the controllers and a couple of
application services which are used directly by the controllers.

Inside, we have a `Provider` and a `Web` folder. The `Provider` is where we define all
the Service Providers that we are using from Silex. Is under application. `Web` is the
parent directory for the Controllers. We have controllers for administration tasks
and controllers for publishers be able to do the bookings.

### `src/Infrastructure`

Here we have the infrastructure concerns. At the moment is just the types
that Doctrine needs in order to be able to do the mapping between objects and primitive
types when interacting with the persistence layer

### `src/Model`

Here is where the core functionality, where the invariants or business logic should
be protected. There is a collection of subdomains. Most of this have been previously designed
which opens to the door for further improvements.

## Doctrine

We are using XML mapping to interact with the ORM in order to leave clean the 
entities. Work for improvement in this area is more than welcome.
