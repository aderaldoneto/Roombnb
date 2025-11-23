### Roombnb 

## Contexto 
Reservas de Consultórios (inspirado nas regras do Airbnb). 
Profissionais de saúde podem reservar salas equipadas por diária/temporada para atender aos pacientes. 
O sistema gerencia disponibilidade, reservas, aprovação pelo dono da sala e histórico. 

## Stack 
# Backend 
Laravel  
# Frontend 
Vue.js  
# Banco de Dados 
PostgreSQL  
## Models 
User, City, Specialty, Room, Reservation  

## Rodar o projeto 
git clone https://github.com/aderaldoneto/Roombnb  
cd Roombnb  
cp .env.example .env  

# Com Sail (eu costumo usar o Sail)
(PS: eu uso apenas `sail` porque criei um alias para não precisar digita `./vendor/bin/sail`)  
composer install  
npm install  
npm run build  
php artisan sail:install  
sail up -d  
sail artisan migrate  
sail artisan db:seed  
sail npm run dev -- --host  
php artisan storage:link  

## Acessar o projeto
Web: http://localhost  
API: http://localhost/api/v1  

## Seeders 
sail php artisan db:seed SpecialtySeeder  
sail php artisan db:seed RoleSeeder  
sail php artisan db:seed AdminUserSeeder   
sail php artisan db:seed CitySeeder  
sail php artisan db:seed ClientUserSeeder  
sail php artisan db:seed DatabaseSeeder  


## API:  
GET  
localhost/api/v1/rooms  

GET  
localhost/api/v1/rooms/{room_id}  

GET  
localhost/api/v1/reservations?user_id={id}  


POST  
localhost/api/v1/rooms/{room_id}/reservations  
{  
  "user_id": 4,  
  "check_in": "2025-02-11",  
  "check_out": "2025-02-12",  
  "payment_method": "pix"  
}  


## Documentação (Swagger)  
sail php artisan l5-swagger:generate    

# ULR  
http://localhost/api/documentation  