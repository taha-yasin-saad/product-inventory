# Laravel Product Inventor Management System

This is a simple Laravel-based product management system that allows users to add products, update their details, and calculate their total values. The data is stored in a JSON file, and the page dynamically updates using AJAX. The application uses Bootstrap for styling and includes a basic modal for editing product details.

## Features

- **Add Product**: A form where users can input product details (name, quantity, price).
- **Display Products**: A table displaying all products with their details: product name, quantity in stock, price per item, date of submission, and total value.
- **Edit Product**: An edit button next to each product in the table, which opens a modal allowing users to edit product details.
- **Total Value Calculation**: The total value for each product is calculated dynamically based on the quantity and price.
- **Sum Total**: A row at the bottom of the table shows the sum of all total values.

## Installation

### Requirements

- PHP 8.0 or higher
- Composer
- Laravel 8 or higher
- MySQL (or any other database of your choice for Laravel's default setup)

### Steps to Install

1. **Clone the repository:**

   ```bash
   git clone https://github.com/taha-yasin-saad/product-inventory.git
   cd product-inventory
