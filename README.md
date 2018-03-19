Welcome to MyTicTacToe
======================

This is a testing purposes project. The target is create a TicTacToe game from scratch without third parties libraries and implementing Q-Learning algorithm for machine learning.

Setup
=====

Clone or download the project in a folder and then we have two options for to run the project:

Option 1: Using built-in PHP Web Server
--------------------------------------

For to run the project using the built-in PHP Web server:

Go to web folder inside your project folder and then execute the script **start.sh** from your terminal.

Example:

```bash
    $ cd your_project_folder/web
    $ ./start.sh
```

After that you can open the next url in your browser:

For Example:

http://localhost:8080

Option 2: Using Apache
----------------------

For to run the project using Apache you will have to create a VirtualHost pointing to web folder of your project folder.

Example:

```bash
<VirtualHost *:80>
    ServerName www.example.com

    DocumentRoot /var/www/mytictactoe/web
    <Directory /var/www/mytictactoe/web>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>

    ErrorLog /var/log/apache2/mytictactoe_error.log
    CustomLog /var/log/apache2/mytictactoe_access.log combined
</VirtualHost>
```

After that you can open the project from your browser using the ServerName selected.

For example:

http://www.example.com

Training
========

The game use an Machine Learn algorithm called [Q-Learning](https://en.wikipedia.org/wiki/Q-learning)

For to train your MyTicTacToe game you will have to do the next steps:

1. Assign full permissions recursively to **bin** folder located inside your project folder.
2. Enter in bin folder.
3. Execute the script training.php

```bash
    $ cd your_project_folder
    $ chmod 777 -R bin
    $ cd bin
    $ php training.php
```

You can execute the training many times, every time you execute it the app will play better.