##Collaborate

**ToDoList is a great app but there's still a lot to do and to improve !**

**You're welcome !**

###Before beginning

This app use Git, the versionning tool the most used in the world.
If you don't use it yet, please install it : [Github](https://github.com/)

    1. Fork the repository on your computer
       - within zip format on `https://github.com/caroleduval/ToDoList`
       - via the console :`git clone https://github.com/caroleduval/ToDoList.git`.
          
    2. Install the projet with the console
       - browse to the directory that contains the project.
       - Run `composer update` and define your own values when asked.
       
    3. If you nedd it, you can fill the database with tests datas
       - Run : `bin/console app:initialize-TDL`
       
    4. Create you own branch to work on.
    
        
    5. And Let's Go ...


###Requirements

Please keep it in mind when working :
**Your code should fulfill the best development practices**
* psr recommendations
* SOLID principles
* Symfony Best Practices
    
**Tests are your best friends**
* run regularly PHPUnit in order to check if everything's OK
* Implement your own tests, code coverage mustn't decrease because of your code
    
**Quality is in details**
* Check your code with Quality tools (codacy, codeclimate, scrutinizer)
* Check the performance impact of your code with Blackfire.io

###When finished

You have produced a good job, we are confident !

    1. Push your branch in the original repository
        
    2. Open a pull request : be synthetic but exhaustive in your explaination 
     => Travis will automatically launch a bunch of tests
     Your code must succeed at Travis tests unless we cannot go forward
        
    3. When everythings OK, I can proceed to the merge
    maybe we'll have to discuss before ... just for pleasure