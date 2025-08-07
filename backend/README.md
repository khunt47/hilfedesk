# DaySupport Backend

## Info
This repository is the backend module of DaySupport.

## Tech Stack
It is built with Laravel 10. The frontend module is not part of this repo and is integrated with the backend via rest api.

## Localhost setup
The backend source code is containerized for localhost development, so you do not need any environment to be setup on computer. Just checkout the code and run the following commands and you should be up and running.

### Running first time

`docker-compose up -d --build`

### After the first time

`docker-compose up -d`

The application will open on `http://localhost:8082`.
