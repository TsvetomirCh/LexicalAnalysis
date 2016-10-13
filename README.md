# Dummy Lexical Analyzer

##### This is code for an exam.  

#### Examples:
    php start.php 
   
   write some expression
   
Green: 

[A + B = C]
[A<B+36!=A335BC]
[A=B??(A)]

Fail: 

[A=B+C12D*314??(A33)]

[A=B+C12D@314]

#### Rules:
- Always begin with opening tag [ and finish with closing tag ].
- Use only the proper grammar:
- Grammar:
    - Opening tag: [
    - Closing tag: ]
    - Math: + - * /
    - Logical < > != ==
    - Loop ?? ( ) (*) you can use ( and ) after the loop operator, with identifier after the opening bracket.
    - Exit ^
    
    
    TODO: 
    - Abstract most of the analyze in classes.
    - Add rules before and after logical and math operators.
    
#### Screenshots:

[![start.png](https://s14.postimg.org/ir7zlpqm9/start.png)](https://postimg.org/image/phogv5drx/)
[![success.png](https://s16.postimg.org/6thzt2m2d/success.png)](https://postimg.org/image/6gqlmw3sh/)
[![fail.png](https://s18.postimg.org/7regrczs9/fail.png)](https://postimg.org/image/ryrwjnx9h/)
