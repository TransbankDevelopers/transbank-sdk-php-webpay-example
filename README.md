# Proyecto de ejemplo para uso de Webpay con el SDK de Transbank para PHP

El siguiente proyecto es un ejemplo simple de Webpay a través del
SDK de Transbank para PHP.

## Requerimientos
Para ejecutar el proyecto es necesario tener: 
 ```docker``` y ```docker-compose``` ([como instalar Docker](https://docs.docker.com/install/))

## Ejecutar ejemplo
Con el código fuente del proyecto en tu computador, puedes ejecutar en la raíz del proyecto los siguientes pasos:

### 1. Instalar dependencias

Para instalar las dependencia puedes ejecutar el siguiente comando en tu consola:
```bash
docker-compose run --rm -w /var/www web composer install
```

### 2. Ejecutar ejemplo

```bash
docker-compose up -d && \
```
También puedes iniciar el proyecto simplemente ejecutando el archivo `start` en la raíz del proyecto

En ambos casos el proyecto se ejecutará en http://localhost:9000 (y fallará en caso de que el puerto 9000 no esté disponible)

Este proyecto está hecho en PHP 7.2