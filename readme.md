# Project Starter Template 

## Getting Started 

- Update `/app/settings.ini` to get started.

## Database Schemas

### logs.db

```
CREATE TABLE 'requests' (
  'request_id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
  'date_time' DATETIME DEFAULT CURRENT_TIMESTAMP,
  'ip' TEXT, 
  'url' TEXT, 
  'user_agent' TEXT,
  'tracker' TEXT, 
);
```
