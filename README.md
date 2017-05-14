# SINCE - Simple Income and Expense App

[![BSD-2-Clause License](https://img.shields.io/badge/License-BSD--2-blue.svg)](https://github.com/fbeuster/since/blob/master/LICENSE.md)

**SINCE** is a simple web application, that allows you to keep track of your incomes and expenses, this can be for your business or private use.

## Fetures

* Overview of all incoming and outgoing transactions
* Summary of all transactions for different time periods
* Nice pie charts
* Configurable colors for customers (yay)

## Table of Contents

* [Installation](#installation)
    * [Requirements](#requirements)
    * [Instructions](#instructions)
* [How to use SINCE](#how-to-use-since)
    * [Transaction History](#transaction-history)
    * [Summaries](#summaries)
* [Future Plans](#future-plans)
* [Disclaimer](#disclaimer)
* [Remarks](#remarks)

## Installation

### Requirements

* PHP 7.0.15
* MySQL 5.0.12-dev

** Note:** This is what my development runs on. I will check for lower versions soon.

### Instructions

A setup process will be implemented soon. Until then, follow these instructions.

1. Clone the repository to your environment.
2. In the main directory create a file `local.php` like the one below. (Of course you should fill in your own data.)

```php
<?php

  # setting up database
  define('DB_HOST', 'your_database_host');
  define('DB_NAME', 'your_database_name');
  define('DB_PASS', 'your_database_password');
  define('DB_USER', 'your_database_username');
  date_default_timezone_get('Europe/Berlin');

?>
```
3. Create the tables in your database by importing the `setup.sql` file in your database.
4. All done, you now can use **SINCE**.

## How to use SINCE

### Transaction History

On this page, you get a list of all transactions recorded in your database. It shows information about date, sender/receiver, as well as the amount and tax information for a single transaction.

#### Add new transaction

New transactions can be added with the form at the bottom of the transaction history page. Required values are marked with a red \*.

As of now, **Netto** and **Brutto** are needed to enter a transactions, tax values can be left at 0. The app does **not** check, whether the values for taxes add up correctly with Netto and Brutto. This is to allow entering the values as they are written on an invoice or receipt.

For easier use, the fields for customer and description offer suggestions based on your inputs and previous transactions. Of course new values can be entered here as well.

The assignment of a color for a customer will be used in charts on the [Summaries](#summaries) page. If left untouched, the system assigns a color automatically.

#### Delete a transaction

With the action buttons on the end of each row of the table, the transaction in that row can be deleted from the system.

### Summaries

On the summaries page, you get an overview of your transactions, grouped by categories. Summaries are shown for the whole year, as well on a per-quarter basis.

#### Distribution Charts

These pie charts illustrate the distribution of your income and expenses, based on the sender/receiver of a transaction. At a glance, you get an overview, where the majority of your money comes from or goes to.

## Future Plans

* Category management to create, edit and delete categories
* Customer management to create, edit and delete customers
* Inline editing for transactions in the transactions history table
* Configurable language and currency settings
* Configurable tax columns
* Configurable summary time periods
* Support for multiple years
* Paging for long transaction history lists
* More charts
* Make API [more robust](#disclaimer)

## Disclaimer

The tool **SINCE** doesn't provide any authentification (as of now) since it was developed for the use in a local environment and single user usage. Though it is possible to use the tool with multiple users, you have to keep in mind, that every user has access to all of the data.

Unless you implement the necessary securtiy functionality yourself, using SINCE in any publicly accessible environment is **strongly not recommended**.

The API is relatively open right now, and can easily be used to remove all data from the database. This again speaks for keeping SINCE in local environment, only accessible to authorized people.

Also, this is still a prototype and features can change drastically.

## Remarks

This project was developed from the ground up, though some external pieces where be used.

* [Material Design Icons](https://github.com/google/material-design-icons/tree/master/iconfont) - Simple und understandable icons
* [Lixter](https://github.com/fbeuster/beuster-se) - I reused some of my existing classes from here.
