# Como subir a aplicação

Projeto feito com o Docker, basta executar:

```bash
$ docker-compose up -d --build
```

Agora a aplicação já está rodando no http://localhost:8080 porém ainda é preciso subir o banco de dados. Execute:

```bash
$ docker exec -it zf3-crud_zf_1 composer create-database
```

> Ou caso não dê certo, basta entrar no mysql local ("mysql:host=localhost;dbname=zf3-crud" user 'root' e senha 'root') e executar manualmente o arquivo dump `bin/dump.sql`.