# CloudBedsTestTask

Deployed to http://t95610id.beget.tech/

## main focus:
- CRUD works.
- web interface works.
- most success stories of api's using are covered by autotests.
- sql dump existed
- docker-compose up web and api projects
- implemented and used DI
- Collection of Intervals which have relations named PriceList (but is not renamed all now)
- ResponseCreators are separated for more easy support.
- Date manipulations are separated to Helper. Class Date and request validation will replace them.
- "Entity" could (but it is not best idea) to encapsulate most logic with them without sharing their structure.
- "ActionInterface" - example of architecture solution for api controllers.
- Select ... for update with transaction
- divide most operations to separate methods/classes with their responsibilities.
- "IntervalsJoiner" - example of naming, simplify logic, single abstract level, clear functions.
- directory "api" is one way of security.
- directory "Model" is integrating datastore and domain.
- "Entity" collect entity and other domain logic now.
- 

##Technical moments:

"price" - Storing as int is better way. 
Mysql have problems with comparison of float numbers. 
Of course, if you do not use bitcoins.

I don't understand why you deny to use Symfony framework and other packages. 
So I was need to create and use own classes as Application, Controller, Database, ... 
 
 
# help
app/Action - 1 file = 1 Action
app/Application - contains list of routes, credentials for db
