<p align="center"><a href="" ><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
<p align="center">
</p>

## Laravel Stripe Payment
Repoistory for implementing Laravel Stripe Course.


## Features
### Visitor Page
- **Home:** Display different Courses to but
- **Cart:** List all the courses the user is gona buy
- **Plans:** List all the plans the user is gona subscribe to
- **Members Area:** Only allowed when the user subscribe to a plan.








## Setup


- To run this project locally, follow these steps:

1. Clone the Repository:
    git clone https://github.com/lotfy5o/Laravel-Anwar-Stripe

2. Navigate to the Project Directory:
    cd your_project

3. Install Dependencies:
    composer install

4. Setup Environment:
    - Copy .env.example to .env.
    - Configure your database connection details in the .env file.

5. Generate Application Key:
    php artisan key:generate

6. Run Migrations and Seed Database:
    php artisan migrate --seed

7. Serve the Application:
    php artisan serve



## To Do
- Make sure you run webhooks when using Stripe Hosted Pages. 
