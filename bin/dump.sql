CREATE TABLE tb_categoria_produto (
    id_categoria_planejamento INT PRIMARY KEY AUTO_INCREMENT,
    nome_categoria VARCHAR(150) NOT NULL
);

CREATE TABLE tb_produto (
    id_produto INT PRIMARY KEY AUTO_INCREMENT,
    id_categoria_produto INT NOT NULL,
    data_cadastro DATETIME DEFAULT NOW(),
    nome_produto VARCHAR(150) NOT NULL,
    valor_produto DECIMAL(10, 2) NOT NULL,
  CONSTRAINT `IXFK_tb_produto_tb_categoria_produto` FOREIGN KEY (`id_categoria_produto`) REFERENCES `tb_categoria_produto` (`id_categoria_planejamento`)
);
