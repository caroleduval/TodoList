ToDoList
========

##Purpose : Improving a web site helping for shared Todo lists.

Base du projet #8 : Améliorez un projet existant
https://openclassrooms.com/projects/ameliorer-un-projet-existant-1


As user, you can :
- look at the tasks list
- create a task, automatically linked to you
- edit a task (title, content or status = done / to do)
- delete your own tasks

As admin, you can also :
- look at the users list
- create a user
- edit a user (username, password, email or role = user / admin)

## Configuration
Symfony 3.1.10
php     7.2.1
MySQL   5.6.38


## Download the project from github on your computer
- within zip format on `https://github.com/caroleduval/TodoList`
- via the console :
    `git clone https://github.com/caroleduval/TodoList.git`

## Install the projet with the console
- browse to the directory that contains the project.
- Run `composer update` and define your own values when asked.

## Fill the database with tasks and users datas
- Run : `php bin/console app:initialize-TDL`
- open it on your localhost

##It's now OK !


[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d5eef321edbe41d5ac4dcb343fed1ead)](https://www.codacy.com/app/caroleduval/TodoList?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=caroleduval/TodoList&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/9bd9a3df6350327d8871/maintainability)](https://codeclimate.com/github/caroleduval/TodoList/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/caroleduval/TodoList/badges/quality-score.png?b=Final)](https://scrutinizer-ci.com/g/caroleduval/TodoList/?branch=Final)