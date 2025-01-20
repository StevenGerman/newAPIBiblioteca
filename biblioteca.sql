create database biblioteca;
use biblioteca;


create table Roles(
    idRol int auto_increment primary key,
    rolNombre varchar(30)
)

create table Personas(
    idPersona int auto_increment primary key,
    perNombre varchar(50),
    perApellido varchar(50),
    perDni varchar(10),
    perContrasena varchar(255),
    rolID int
)


create table Materias(
    idMateria int auto_increment primary key,
    matNombre varchar(50)
)



create table Autores(
    idAutor int auto_increment primary key, 
    autNombre varchar(100),
    autApellido varchar(100),
    autFechaNac varchar(30),
    autFechaDes varchar(30),
    autBiografia varchar(255)
)

create table Editoriales(
    idEditorial int auto_increment primary key,
    ediNombre varchar(100)
    ediDireccion varchar(100)
    ediTelefono varchar(50)
    ediEmail varchar(255)
)

create table Libros(
    idLibro int auto_increment primary key,
    libTitulo varchar(100),
    libAnio varchar(6),
    libNotaDeContenido varchar(255),
    editorialID int,
    materiaID int,
    autorID int
)

create table Prestamos(
    idPrestamo int auto_increment primary key,
    presFechaDev varchar(20),
    presFechaSal varchar(20),
    presObservacion varchar(100),
    prestamoID int,
    libroID int
);