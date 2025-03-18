-- CREAR EL NUEVO LOGIN “SuperAdmin”
USE master;
GO
-- Crea el login SuperAdmin con una contraseña segura
CREATE LOGIN [SuperAdmin] WITH PASSWORD = 'P@ssw0rd';
GO
-- Asigna el rol de sysadmin para que tenga privilegios elevados
ALTER SERVER ROLE [sysadmin] ADD MEMBER [SuperAdmin];
GO

-- CREAR LA BASE DE DATOS AdminDB PARA NO USAR MASTER
CREATE DATABASE AdminDB;
GO


