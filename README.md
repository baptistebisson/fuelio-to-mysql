![](img/banner.png)

## About The Project

Fuelio is a nice app to keep track of fuel consumption and vehicle costs. You can easily retrieve the CSV files.

I wanted to store my data on a MySQL database in order to keep a local history and also to do some custom stats even if the app is pretty complete.

### Supported data

Check `src/Entity` files.

### Built With

- PHP 8.3
- MySQL

### Query

## Getting Started

### Prerequisites

You need to retrieve your data: https://fuel.io/faq_backup_help.html

Create a folder to store all csv files.

You need a MySQL database

### Installation

- Clone

```bash
git clone https://github.com/baptistebisson/fuelio-to-mysql.git
```

- Setup env

```bash
cd fuelio-to-mysqm && cp .env.example .env
```

- Setup composer

```bash
composer install
```

- Run it

```bash
php console.php app:sync --folder=/path/to/fuelio
```

#### Output example

```
Found 1 files
Found a total of 15 data
Found a total of 15 categories
Found a total of 7 costs
Done!
```
