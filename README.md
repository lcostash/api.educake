# Back-End for Educake

This project was create using Symfony 5 special for Educake.   

## How to up the project

1) get the project from git on local folder with name `educake`
2) go to inside of `educake`, you must to see composer.json file
3) run cmd terminal and run next command `composer install` to install required packages and press enter
4) after that you must create virtual host on your Apache server on local machine with name `educake.one`
5) use front-end for this project from [here](https://github.com/lcostash/frontend.educake.git) or you can use command line like that
```bash
$ curl -H "Content-Type: application/json" -X GET http://educake.one/intensity
```

## End points
1) `http://educake.one/intensity` Get Carbon Intensity data for current half hour
2) `http://educake.one/intensity/{from}` Get Carbon Intensity data for specific half hour period
3) `http://educake.one/intensity/{from}/{to}` Get Carbon Intensity data between from and to datetime 
4) `http://educake.one/intensity/date` Get Carbon Intensity data for today 
5) `http://educake.one/intensity/date/{date}` Get Carbon Intensity data for specific date
6) `http://educake.one/intensity/date/{date}/{period}` Get Carbon Intensity data for specific date and period
7) `http://educake.one/intensity/factors` Get Carbon Intensity factors for each fuel type 

## End point params
1) `from` and `to` - Start and End datetime in ISO8601 format YYYY-MM-DDThh:mmZ e.g. 2017-08-25T12:35Z
2) `date` - Date in YYYY-MM-DD format e.g. 2017-08-25 

## Example responses
```json
{
    "status": 200,
    "rows": [
        {
            "from": "2020-10-31T08:30Z",
            "to": "2020-10-31T09:00Z",
            "intensity": {
                "forecast": 88,
                "actual": 92,
                "index": "low"
            }
        }
    ]
}
```
```json
{
    "status": 200,
    "rows": [
        {
            "name": "Biomass",
            "value": 120
        },
        {
            "name": "Coal",
            "value": 937
        },
        {
            "name": "Dutch Imports",
            "value": 474
        },
        {
            "name": "French Imports",
            "value": 53
        },
        {
            "name": "Gas (Combined Cycle)",
            "value": 394
        },
        {
            "name": "Gas (Open Cycle)",
            "value": 651
        },
        {
            "name": "Hydro",
            "value": 0
        },
        {
            "name": "Irish Imports",
            "value": 458
        },
        {
            "name": "Nuclear",
            "value": 0
        },
        {
            "name": "Oil",
            "value": 935
        },
        {
            "name": "Other",
            "value": 300
        },
        {
            "name": "Pumped Storage",
            "value": 0
        },
        {
            "name": "Solar",
            "value": 0
        },
        {
            "name": "Wind",
            "value": 0
        }
    ]
}
```
```json
{
    "status": 404,
    "message": "Not Found"
}
```
```json
{
    "status": 500,
    "message": "Internal Server Error"
}
```
## Stay in touch

Author - [Lilian Costas](https://www.linkedin.com/in/lcostash/)