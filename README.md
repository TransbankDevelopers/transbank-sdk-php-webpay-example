# Proyecto de ejemplo para uso de Webpay con el SDK de Transbank para PHP

El siguiente proyecto es un ejemplo simple de Webpay a través del
SDK de Transbank para PHP.

## Requerimientos
Para ejecutar el proyecto es necesario tener: 
 ```docker``` y ```docker-compose``` ([como instalar Docker](https://docs.docker.com/install/))

## Ejecutar ejemplo
Con el código fuente del proyecto en tu computador, puedes ejecutar en la raíz del proyecto los siguientes pasos:

### 1. Construir imágenes e instalar dependencias

Para instalar las dependencia puedes ejecutar el siguiente comando en tu consola:
```bash
make build
make update
```

### 2. Ejecutar ejemplo

```bash
make start
```

## Ejecutar Tests

Asegurate de tener las imagenes construidas con `make build` y luego ejecuta

```bash
make test
```

## Otras funcionalidades

### Detener las imagenes

```bash
make stop
```

o también

```bash
make kill
```

### Conectarte a shell del contenedor

```bash
./shell
```
Puedes también pasar un usuario en especifico

```bash
./shell <usuario>
```

### Reconstruir las imagenes

```bash
make clean build
```

En ambos casos el proyecto se ejecutará en http://localhost:9000 (y fallará en caso de que el puerto 9000 no esté disponible)

Este proyecto está hecho en PHP 7.2