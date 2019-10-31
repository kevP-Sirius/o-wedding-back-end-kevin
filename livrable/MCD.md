# MOCODO

ROLE : id , name , created_at, updated_at, is_active
BELONG TO, 0N ROLE , 11 USER
THEME : id, name, created_at, updated_at, is_active
Contains, 1N PROVIDER, 0N THEME

TYPE: id , name , created_at, updated_at, is_active
USER : id, username, password,email,session_duration, token, created_at, updated_at, is_active,
Having, 0N PROVIDER , 0N PROJECT
PROVIDER : id, name, phone_number, email, average_price, description , created_at, updated_at, is_active

IS , 0N TYPE, 11 GUEST
Have, 11 PROJECT, 01 USER
PROJECT : id, name, deadline, forecast_budget,token, current_budget, created_at, updated_at, is_active
Work_at, 1N PROVIDER, 0N DEPARTMENT

GUEST : id, lastname, firstname, email, phone_number,is_active,is_coming_with,vegetarian_meal_number,meat_meal_number,token ,type , created_at, updated_at, is_active
Can_Have, 0N PROJECT, 1N GUEST
Take_place_to, 11 PROJECT, 0N DEPARTMENT
DEPARTMENT : id, number, name, created_at, updated_at, is_active

CHAT: id ,created_at,updated_at,is_active

MESSAGE:id,body,from,to,created_at,updated_at,is_active
IS INSIDE,0N CHAT , 11 MESSAGE