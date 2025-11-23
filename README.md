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
# Motivo 
Familiaridade 

## Models 
User, City, Specialty, Room, Reservation 



php artisan storage:link  
sail php artisan db:seed SpecialtySeeder  
RoleSeeder  
AdminUserSeeder   
ClientUserSeeder  


API:  
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


