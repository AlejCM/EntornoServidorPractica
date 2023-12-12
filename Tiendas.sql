CREATE SCHEMA tiendas;
USE tiendas;

create table productos (
	idProducto int primary key auto_increment,
    nombreProducto varchar(40) not null,
    precio numeric(7,2) not null,
    descripcion varchar(255) not null,
    cantidad numeric(5) not null
);

drop table usuarios;

create table usuarios (
	usuario varchar(12) primary key,
    contrasena varchar(255) not null,
    fechaNacimiento date not null
);

create table cestas (
	idCesta int primary key auto_increment,
    usuario varchar(12),
    precioTotal numeric(7,2) not null,
    constraint fk_cestas_usuarios
		foreign key (usuario)
        references usuarios(usuario)
);

create table productosCestas (
	idProducto int,
    idCesta int,
    cantidad numeric(2),
    constraint pk_productosCestas
		primary key (idProducto, idCesta),
	constraint fk_productosCestas_productos
		foreign key (idProducto)
        references productos(idProducto),
	constraint fk_productosCestas_cestas
		foreign key (idCesta)
        references cestas(idCesta)
);

create table pedidos (
	idPedido int primary key auto_increment,
    usuario varchar(12),
    precioTotal numeric(7,2) not null,
    fechaPedido datetime not null default (CURRENT_DATE),
    constraint fk_pedidos_usuarios
		foreign key (usuario)
        references usuarios(usuario)
);

create table lineasPedidos (
	lineaPedido numeric(2) not null,
    idProducto int,
	idPedido int,
    precioUnitario numeric(7,2) not null,
    cantidad numeric(5) not null,
    constraint fk_lineasPedidos_productos
		foreign key (idProducto)
        references productos(idProducto),
	constraint fk_lineasPedidos_pedidos
		foreign key (idPedido)
        references pedidos(idPedido)
);

select * from productos;
select * from usuarios;
select * from cestas;
select * from productosCestas;
select * from pedidos;
select * from lineasPedidos;

drop table productos;
drop table usuarios;
drop table cestas;
drop table productosCestas;
drop table pedidos;
drop table lineasPedidos;

alter table productos 
	add column imagen varchar(100) not null;

alter table productos 
	modify imagen varchar(100) not null;
    
alter table usuarios 
	add rol varchar(10) default 'cliente';

/* Cambiar el rol del usuario */
update usuarios set rol = 'admin' where usuario = 'AleCM';


delete from productos;
