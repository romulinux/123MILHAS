# Teste da 123Milhas feito em Lumen PHP Framework

A escolha do Lumem foi feita por ser um framework mais simples, facilmente aplicável a APIs.

## Requisitos

PHP v7.3 ou superior

COMPOSER v2.0 ou superior

## Aplicação

Para executar a aplicação basta executar o comando `composer install` na raiz do projeto e instanciar o servidor localmente com o comando `php -S localhost:8000 -t public`.

## Rotas

Foram criadas 2 rotas: flights/list e flights/groups. A url base é `http://localhost:8000`.

### ***_GET_ /flights/list*** - Retorna um json com uma lista de flights obtidos na [api de testes](http://prova.123milhas.net/api/flights) da 123Milhas.
### ***_GET_ /flights/groups*** - Retorna um json com uma lista de flights agrupadas.
