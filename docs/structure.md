# General Project structure

- **assets** (_backend source scripts (css, js etc.)_)
- **bin** (_application binaries_)
- **bundles** (_some custom bundles_)
- **config** (_all the config needed to run the application_)
- **docker** (_configuration Docker files_)
- **docs** (_application documentation_)
- **public** (_the entry point to the application, and public frontend artifacts like CSS and JS files_)
- **source** (_frontend files_)
- **migrations** (_migrations files_)
- **src**
    - **Users**
      - **[Action]** (_framework controller actions._)
      - **[Application]** (_frameworks things realisation - such as EventListeners, Middleware etc._)
      - **[Domain]** (_contains the actual business logic and domain models._)
      - **[Infrastructure]** (_binds the business logic(domain) implementation to infrastructure._)
      - **[View]** (_is responsible for presenting a user interface, allowing users to make use of the business logic_)
- **source** (_directory for frontend src files_)
- **storage** (_directory for store files_)
- **templates** (_view templates_)
- **tests**
    - **acceptance** (_acceptance tests_)
    - **functional** (_functional tests_)
    - **unit** (_unit tests_)
- **translations**
- **var** (_volatile artifacts like logs, cache, temporary test databases, generated code, ..._)
- **vendor** (_distributable libraries_)

## Domain
  - **Users**
    - **[Domain]** 
      - **User**
        - **Repository** (_repository interfaces_)
        - **Service** (_useCases like Create, Update, Delete etc._)
        - **ValueObject**
        - **EntityClass.php**

Most importantly, the domain layer contains the domain models, domain repositories (simple interactions with models), 
and domain services (complex interactions with [multiple] entities). Also command classes describing business interactions 
and corresponding domain events belong here. Last but not least, also custom value objects that are important to the domain are stored here.

All classes and interfaces defined in this layer have no dependencies to any third party library.

## Infrastructure
  - **Users**
    - **[Infrastructure]** 
      - **Repository**
      - **Service**

The infrastructure layer binds the elements defined in the domain layer to a specific framework or platform 
in order to have a runnable application. The layer can for example act as an adapter/wrapper for 
specific persistence tasks or provide application services (such as email, caching, message queues, etc.).

In case of Symfony, the infrastructure layer also contains console commands the application may have or 
EventListeners that listen to Symfony-specific or custom events through the frameworks event system.

In general, the layer contains the actual implementations of the business services described in the domain layer. 

## View(Presentation)
  - **Users**
    - **[View]** 
      - **Form**
      - **Twig**

The presentation layer contains all resources concerned with creating a user interface rendered on the server side for the end user. 
Web based controllers(actions), forms with input validation and Twig view scripts are stored in this layer.

Having separated the user interface from the domain and the infrastructure simplifies the development of either layer. 
The code in the presentation layer should not contain any business logic, but only forward calls to the respective 
services of the domain layer. Hence, the presentation layer stays small helping developers to easily evolve the user interface, 
even during big changes.

## Action(View)
  - **Users**
    - **[Action]** 
      - **Api** 
      - **Backend** 
      - **Frontend** 
      - **Command** 
      
Framework controllers & actions & commands

## Application
  - **Users**
    - **[Application]** 
      - **Listener** 
      - **Validator**
      
Framework things like Event subscribers, validators etc.

# Modules & Entity description (src directory)

## Clients
In this directory stores main models of project: Clients, Cards, Transactions, Documents etc.

## Clients/Domain/Users
Users registered in portal

## Import
All about import 1C/PC files and documents.

## Export
All about export files to 1C/PC.

## Api
Api crud files and projects api methods.

## Users
CMS(admin) users. Admins, Managers....